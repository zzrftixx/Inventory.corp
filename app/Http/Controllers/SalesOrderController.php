<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'user'])->latest()->paginate(15);
        return view('sales_orders.index', compact('salesOrders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('nama')->get();
        // Item fetching array is removed because we now use AJAX Server-Side Rendering via Select2
        $items = [];
        return view('sales_orders.create', compact('customers', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tanggal_transaksi' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.diskon' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $datePrefix = Carbon::parse($request->tanggal_transaksi)->format('Ymd');
            $lastOrder = SalesOrder::whereDate('tanggal_transaksi', $request->tanggal_transaksi)->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (int) substr($lastOrder->no_faktur, -4) + 1 : 1;
            $noFaktur = 'INV-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $salesOrder = SalesOrder::create([
                'no_faktur' => $noFaktur,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                'total_invoice' => 0,
                'status' => 'Draft' // Now starts as DRAFT
            ]);

            $totalInvoice = 0;

            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['id']);

                $qty = $itemData['qty'];
                $diskon = $itemData['diskon'] ?? 0;
                $hargaSatuan = $item->harga_jual_default;

                $metadata = null;
                $harga_modal_real = $item->harga_beli_rata_rata;

                if ($item->is_aluminium) {
                    // Harga modal per batang = berat_profil_kg * panjang_meter * harga_dasar_aluminium_kg
                    $harga_modal_real = $item->berat_profil_kg * $item->panjang_meter * $item->harga_dasar_aluminium_kg;

                    // Bekukan semua parameter saat detik transaksi terjadi
                    $metadata = json_encode([
                        'is_aluminium' => true,
                        'berat_kg' => $item->berat_profil_kg,
                        'panjang_m' => $item->panjang_meter,
                        'harga_dasar_kg' => $item->harga_dasar_aluminium_kg,
                        'modal_beku' => $harga_modal_real,
                        'rumus' => "{$item->berat_profil_kg} kg x {$item->panjang_meter} m x Rp " . number_format($item->harga_dasar_aluminium_kg, 0, ',', '.')
                    ]);
                }

                $subtotalNetto = ($hargaSatuan * $qty) - $diskon;
                $totalInvoice += $subtotalNetto;

                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'harga_modal_saat_transaksi' => $harga_modal_real,
                    'harga_satuan_saat_transaksi' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal_netto' => $subtotalNetto,
                    'metadata_kalkulasi' => $metadata
                ]);
            }

            $salesOrder->update(['total_invoice' => $totalInvoice]);

            DB::commit();

            return redirect()->route('sales-orders.show', $salesOrder->id)->with('success', 'Draft Sales Order berhasil disimpan. Silahkan periksa dan Kunci Transaksi jika sudah benar.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat Draft: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'user', 'details.item']);
        return view('sales_orders.show', compact('salesOrder'));
    }

    public function suratJalan(SalesOrder $sales_order)
    {
        $sales_order->load(['customer', 'details.item']);
        // For printing purposes, render a clean layout
        return view('sales_orders.surat_jalan', compact('sales_order'));
    }

    public function faktur(SalesOrder $sales_order)
    {
        $sales_order->load(['customer', 'user', 'details.item']);
        // For printing purposes
        return view('sales_orders.faktur', compact('sales_order'));
    }

    public function confirm(SalesOrder $sales_order)
    {
        if ($sales_order->status !== 'Draft') {
            return back()->with('error', 'Hanya order berstatus Draft yang bisa dikunci.');
        }

        try {
            DB::beginTransaction();

            $has_deficit = false;
            $backorder_details = [];
            $suppliers_to_po = []; // mapping supplier_id -> array of items for auto PO.

            foreach ($sales_order->details as $detail) {
                $item = Item::lockForUpdate()->with('suppliers')->find($detail->item_id);

                if (!$item) {
                    throw new \Exception("Barang dengan ID {$detail->item_id} tidak ditemukan.");
                }

                $available = $item->stok_saat_ini;

                if ($available < $detail->qty) {
                    $has_deficit = true;
                    $deficit_qty = $detail->qty - $available;

                    // Store deficit data for the split Backorder SO
                    $backorder_details[] = [
                        'item_id' => $item->id,
                        'qty' => $deficit_qty,
                        'harga_modal_saat_transaksi' => $detail->harga_modal_saat_transaksi,
                        'harga_satuan_saat_transaksi' => $detail->harga_satuan_saat_transaksi,
                        'diskon' => $detail->diskon,
                        'subtotal_netto' => ($detail->harga_satuan_saat_transaksi * $deficit_qty) - $detail->diskon,
                        'metadata_kalkulasi' => $detail->metadata_kalkulasi
                    ];

                    // Prepare Auto-PO logic by mapping to the first supplier (if mapped)
                    $supplier = $item->suppliers->first();
                    if ($supplier) {
                        $suppliers_to_po[$supplier->id][] = [
                            'item_id' => $item->id,
                            'qty_butuh' => $deficit_qty,
                            'harga_beli_satuan' => $supplier->pivot->harga_beli_terakhir ?? $item->harga_beli_rata_rata,
                            'metadata_kalkulasi' => $detail->metadata_kalkulasi
                        ];
                    }

                    // Update the CURRENT detail to only take the available stock (if available > 0)
                    if ($available > 0) {
                        $detail->qty = $available;
                        $detail->subtotal_netto = ($detail->harga_satuan_saat_transaksi * $available) - $detail->diskon;
                        $detail->save();
                    } else {
                        // If no stock at all, delete this detail from current SO!
                        $detail->delete();
                    }
                }

                // If available > 0, deduct the stock and write StockMovement for the Current SO
                // Notice we use the possibly mutated $detail->qty which ensures we don't drop below 0
                if ($available > 0) {
                    $item->stok_saat_ini -= $detail->qty;
                    $item->save();

                    StockMovement::create([
                        'item_id' => $item->id,
                        'tipe_pergerakan' => 'OUT',
                        'qty' => $detail->qty,
                        'sisa_stok' => $item->stok_saat_ini,
                        'referensi' => 'SO: ' . $sales_order->no_faktur,
                        'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                        'timestamp' => now()
                    ]);
                }
            }

            // Recalculate Current SO total
            $sales_order->total_invoice = $sales_order->details()->sum('subtotal_netto');
            $sales_order->status = 'Selesai';
            $sales_order->save();

            // Create Backorder SO and Draft POs if there's a deficit
            if ($has_deficit && count($backorder_details) > 0) {
                // Generate a new SO number
                $datePrefix = \Carbon\Carbon::parse($sales_order->tanggal_transaksi)->format('Ymd');
                $lastOrder = SalesOrder::whereDate('tanggal_transaksi', $sales_order->tanggal_transaksi)->orderBy('id', 'desc')->first();
                $sequence = $lastOrder ? (int) substr($lastOrder->no_faktur, -4) + 1 : 1;
                $newNoFaktur = 'INV-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT) . '-BO';

                $boOrder = SalesOrder::create([
                    'no_faktur' => $newNoFaktur,
                    'tanggal_transaksi' => $sales_order->tanggal_transaksi,
                    'customer_id' => $sales_order->customer_id,
                    'user_id' => $sales_order->user_id,
                    'total_invoice' => 0,
                    'status' => 'Backorder' // Locked Backorder status
                ]);

                $boTotal = 0;
                foreach ($backorder_details as $b_detail) {
                    $boTotal += $b_detail['subtotal_netto'];
                    $b_detail['sales_order_id'] = $boOrder->id;
                    \App\Models\SalesOrderDetail::create($b_detail);
                }
                $boOrder->update(['total_invoice' => $boTotal]);

                // Create Auto-POs per supplier
                foreach ($suppliers_to_po as $supplier_id => $po_items) {
                    $datePrefixPO = \Carbon\Carbon::parse($sales_order->tanggal_transaksi)->format('Ymd');
                    $lastPO = \App\Models\PurchaseOrder::whereDate('tanggal_po', $sales_order->tanggal_transaksi)->orderBy('id', 'desc')->first();
                    $sequencePO = $lastPO ? (int) substr($lastPO->no_po, -4) + 1 : 1;
                    $noPO = 'PO-' . $datePrefixPO . '-' . str_pad($sequencePO, 4, '0', STR_PAD_LEFT);

                    $totalPO = 0;
                    foreach ($po_items as $item) {
                        $totalPO += ($item['qty_butuh'] * $item['harga_beli_satuan']);
                    }

                    $draftPO = \App\Models\PurchaseOrder::create([
                        'no_po' => $noPO,
                        'tanggal_po' => $sales_order->tanggal_transaksi,
                        'supplier_id' => $supplier_id,
                        'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                        'status' => 'Draft',
                        'total_amount_po' => $totalPO
                    ]);

                    foreach ($po_items as $item) {
                        \App\Models\PurchaseOrderDetail::create([
                            'purchase_order_id' => $draftPO->id,
                            'item_id' => $item['item_id'],
                            'qty_butuh' => $item['qty_butuh'],
                            'harga_beli_satuan' => $item['harga_beli_satuan'],
                            'metadata_kalkulasi' => $item['metadata_kalkulasi']
                        ]);
                    }

                    // Increase sequence slightly for uniqueness in loops just in case multiple suppliers
                    $sequencePO++;
                    sleep(1); // prevent identical exact timestamps for DB uniqueness if strict
                }

                DB::commit();
                return redirect()->route('sales-orders.show', $sales_order->id)->with('success', 'Transaksi dikunci sebagian. Stok tidak cukup memicu Split Order ke dokumen: ' . $newNoFaktur . ' (Backorder) dan pembuatan Draft PO otomatis ke supplier.');
            }

            DB::commit();
            return redirect()->route('sales-orders.show', $sales_order->id)->with('success', 'Transaksi berhasil dikunci (LOCKED) sepenuhnya tanpa deficit stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses kunci & split order: ' . $e->getMessage());
        }
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'Draft') {
            return redirect()->route('sales-orders.show', $salesOrder->id)->with('error', 'Nota yang sudah terkunci (Selesai) tidak dapat diedit. Hubungi Admin/Bos untuk proses Retur/Void.');
        }

        $customers = Customer::orderBy('nama')->get();
        // Item fetching array is removed because we now use AJAX Server-Side Rendering via Select2
        $items = [];
        $salesOrder->load('details.item');

        return view('sales_orders.edit', compact('salesOrder', 'customers', 'items'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'Draft') {
            return back()->with('error', 'Akses ditolak. Nota sudah dilock.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tanggal_transaksi' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.diskon' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $salesOrder->update([
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'customer_id' => $request->customer_id,
            ]);

            $salesOrder->details()->delete();

            $totalInvoice = 0;
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['id']);

                $qty = $itemData['qty'];
                $diskon = $itemData['diskon'] ?? 0;
                $hargaSatuan = $item->harga_jual_default;

                $metadata = null;
                $harga_modal_real = $item->harga_beli_rata_rata;

                if ($item->is_aluminium) {
                    $harga_modal_real = $item->berat_profil_kg * $item->panjang_meter * $item->harga_dasar_aluminium_kg;

                    $metadata = json_encode([
                        'is_aluminium' => true,
                        'berat_kg' => $item->berat_profil_kg,
                        'panjang_m' => $item->panjang_meter,
                        'harga_dasar_kg' => $item->harga_dasar_aluminium_kg,
                        'modal_beku' => $harga_modal_real,
                        'rumus' => "{$item->berat_profil_kg} kg x {$item->panjang_meter} m x Rp " . number_format($item->harga_dasar_aluminium_kg, 0, ',', '.')
                    ]);
                }

                $subtotalNetto = ($hargaSatuan * $qty) - $diskon;
                $totalInvoice += $subtotalNetto;

                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'harga_modal_saat_transaksi' => $harga_modal_real,
                    'harga_satuan_saat_transaksi' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal_netto' => $subtotalNetto,
                    'metadata_kalkulasi' => $metadata
                ]);
            }

            $salesOrder->update(['total_invoice' => $totalInvoice]);

            DB::commit();

            return redirect()->route('sales-orders.show', $salesOrder->id)->with('success', 'Draft Sales Order berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update draft: ' . $e->getMessage());
        }
    }

    public function destroy(SalesOrder $salesOrder)
    {
        try {
            DB::beginTransaction();

            if ($salesOrder->status == 'Batal') {
                throw new \Exception('Transaksi sudah dibatalkan sebelumnya.');
            }

            // If still draft, just delete cleanly
            if ($salesOrder->status == 'Draft') {
                $salesOrder->details()->delete();
                $salesOrder->delete();
                DB::commit();
                return redirect()->route('sales-orders.index')->with('success', 'Draft SO berhasil dihapus bersih.');
            }

            // If LOCKED (Selesai), we VOID it and return stock
            foreach ($salesOrder->details as $detail) {
                $item = Item::lockForUpdate()->find($detail->item_id);
                if ($item) {
                    $item->stok_saat_ini += $detail->qty;
                    $item->save();

                    StockMovement::create([
                        'item_id' => $item->id,
                        'tipe_pergerakan' => 'IN',
                        'qty' => $detail->qty,
                        'sisa_stok' => $item->stok_saat_ini,
                        'referensi' => 'Void SO: ' . $salesOrder->no_faktur,
                        'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                        'timestamp' => now()
                    ]);
                }
            }

            $salesOrder->update(['status' => 'Batal']);

            DB::commit();

            return redirect()->route('sales-orders.index')->with('success', 'Transaksi berhasil di-VOID (Retur) dan stok gudang sudah dikembalikan otomatis.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-orders.index')->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}

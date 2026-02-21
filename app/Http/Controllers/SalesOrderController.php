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
        // Load items that have stock > 0
        $items = Item::where('stok_saat_ini', '>', 0)->orderBy('nama_barang')->get();
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
            $sequence = $lastOrder ? (int)substr($lastOrder->no_faktur, -4) + 1 : 1;
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
                
                // We still check if they request impossible amount
                // but we DO NOT deduct stock here.
                // It is a draft. However, if stock is not enough for draft, it might be annoying later.
                // We'll just warn or let it pass for now. Assuming draft ignores stock limits until submitted.

                $hargaSatuan = $item->harga_jual_default;
                $subtotalNetto = ($hargaSatuan * $qty) - $diskon;
                $totalInvoice += $subtotalNetto;

                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'harga_modal_saat_transaksi' => $item->harga_beli_rata_rata,
                    'harga_satuan_saat_transaksi' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal_netto' => $subtotalNetto
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

            foreach ($sales_order->details as $detail) {
                $item = Item::lockForUpdate()->find($detail->item_id);
                
                if (!$item || $item->stok_saat_ini < $detail->qty) {
                    $available = $item ? $item->stok_saat_ini : 0;
                    throw new \Exception("Stok tidak memadai untuk {$item->nama_barang}. Butuh {$detail->qty}, Tersedia {$available}");
                }

                // Update stock
                $item->stok_saat_ini -= $detail->qty;
                $item->save();

                // Record movement
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

            $sales_order->update(['status' => 'Selesai']);

            DB::commit();

            return redirect()->route('sales-orders.show', $sales_order->id)->with('success', 'Transaksi berhasil dikunci (LOCKED) dan stok telah dipotong.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengunci transaksi: ' . $e->getMessage());
        }
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'Draft') {
            return redirect()->route('sales-orders.show', $salesOrder->id)->with('error', 'Nota yang sudah terkunci (Selesai) tidak dapat diedit. Hubungi Admin/Bos untuk proses Retur/Void.');
        }

        $customers = Customer::orderBy('nama')->get();
        $items = Item::where('stok_saat_ini', '>', 0)->orderBy('nama_barang')->get();
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
                $subtotalNetto = ($hargaSatuan * $qty) - $diskon;
                $totalInvoice += $subtotalNetto;

                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'harga_modal_saat_transaksi' => $item->harga_beli_rata_rata,
                    'harga_satuan_saat_transaksi' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal_netto' => $subtotalNetto
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])->latest()->paginate(15);
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        // Item fetching array is removed because we now use AJAX Server-Side Rendering via Select2
        $items = [];

        // Auto-generate PO logic based on minimum stock
        $autoItems = [];
        if ($request->has('auto')) {
            $autoItems = Item::whereRaw('stok_saat_ini <= batas_stok_minimum')->get();
        }

        return view('purchase_orders.create', compact('suppliers', 'items', 'autoItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_po' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.qty_butuh' => 'required|integer|min:1',
            'items.*.harga_beli_satuan' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate No PO
            $datePrefix = Carbon::parse($request->tanggal_po)->format('Ymd');
            $lastOrder = PurchaseOrder::whereDate('tanggal_po', $request->tanggal_po)->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (int) substr($lastOrder->no_po, -4) + 1 : 1;
            $noPo = 'PO-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // 2. Create Header
            $purchaseOrder = PurchaseOrder::create([
                'no_po' => $noPo,
                'tanggal_po' => $request->tanggal_po,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1), // Default user for now
                'status' => 'Ordered',
                'total_amount_po' => 0
            ]);

            $total_po = 0;
            // 3. Process Details
            foreach ($request->items as $itemData) {
                $subtotal = $itemData['qty_butuh'] * $itemData['harga_beli_satuan'];
                $total_po += $subtotal;

                $item = Item::findOrFail($itemData['id']);
                $metadata = null;
                $harga_modal_real = $itemData['harga_beli_satuan'];

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

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $itemData['id'],
                    'qty_butuh' => $itemData['qty_butuh'],
                    'harga_beli_satuan' => $itemData['harga_beli_satuan'],
                    'metadata_kalkulasi' => $metadata
                ]);
            }

            $purchaseOrder->update(['total_amount_po' => $total_po]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Purchase Order berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'details.item']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['Received', 'Batal'])) {
            return back()->with('error', 'Status PO tidak valid untuk penerimaan barang.');
        }

        $purchaseOrder->load(['details.item', 'details.receipts']);
        return view('purchase_orders.receive_form', compact('purchaseOrder'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['Received', 'Batal'])) {
            return back()->with('error', 'Status PO tidak valid untuk penerimaan barang.');
        }

        $request->validate([
            'tanggal_terima' => 'required|date',
            'items' => 'required|array',
            'items.*.detail_id' => 'required|exists:purchase_order_details,id',
            'items.*.qty' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Filter out items where qty == 0 to avoid blank receipts
            $items_to_receive = array_filter($request->items, function ($i) {
                return isset($i['qty']) && $i['qty'] > 0; });

            if (empty($items_to_receive)) {
                return back()->with('error', 'Silakan input setidaknya satu barang dengan qty > 0.');
            }

            $receipt = \App\Models\GoodsReceipt::create([
                'purchase_order_id' => $purchaseOrder->id,
                'no_surat_jalan_supplier' => $request->no_surat_jalan_supplier,
                'tanggal_terima' => $request->tanggal_terima,
                'penerima_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                'catatan' => $request->catatan
            ]);

            foreach ($items_to_receive as $input) {
                $detail = \App\Models\PurchaseOrderDetail::findOrFail($input['detail_id']);
                $qty_received = $input['qty'];

                // Track detail
                \App\Models\GoodsReceiptDetail::create([
                    'goods_receipt_id' => $receipt->id,
                    'purchase_order_detail_id' => $detail->id,
                    'item_id' => $detail->item_id,
                    'qty_diterima' => $qty_received,
                    'kondisi' => 'Baik' // For now, assumed good. Can be expanded based on UI in future.
                ]);

                // Update physical stock
                $item = \App\Models\Item::lockForUpdate()->find($detail->item_id);
                if ($item) {
                    $old_stok = $item->stok_saat_ini;
                    $old_avg = $item->harga_beli_rata_rata;
                    $new_price = $detail->harga_beli_satuan;

                    $total_stok = $old_stok + $qty_received;
                    if ($total_stok > 0) {
                        $new_avg = (($old_stok * $old_avg) + ($qty_received * $new_price)) / $total_stok;
                    } else {
                        $new_avg = $new_price;
                    }

                    $item->harga_beli_rata_rata = $new_avg;
                    $item->stok_saat_ini = $total_stok;
                    $item->save();

                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'tipe_pergerakan' => 'IN',
                        'qty' => $qty_received,
                        'sisa_stok' => $item->stok_saat_ini,
                        'referensi' => 'Terima PO: ' . $purchaseOrder->no_po . ' (SJ: ' . ($request->no_surat_jalan_supplier ?? '-') . ')',
                        'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                        'timestamp' => now()
                    ]);
                }
            }

            // Verify if fully received
            $is_complete = true;
            $purchaseOrder->load('details.receipts');
            foreach ($purchaseOrder->details as $d) {
                $total_in = $d->receipts->sum('qty_diterima');
                if ($total_in < $d->qty_butuh) {
                    $is_complete = false;
                    break;
                }
            }

            $purchaseOrder->update(['status' => $is_complete ? 'Received' : 'Partial']);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Penerimaan barang fisik (' . count($items_to_receive) . ' item) berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penerimaan barang: ' . $e->getMessage());
        }
    public function forceClose(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Partial') {
            return back()->with('error', 'Hanya PO berstatus Partial yang dapat ditutup paksa.');
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->update(['status' => 'Closed']);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Purchase Order berhasil ditutup paksa. Sisa barang batal tidak akan ditambahkan ke stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menutup paksa PO: ' . $e->getMessage());
        }
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'details.item']);
        return view('purchase_orders.print', compact('purchaseOrder'));
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status == 'Received') {
            return back()->with('error', 'PO yang sudah diterima tidak dapat dihapus.');
        }

        try {
            DB::beginTransaction();
            $purchaseOrder->details()->delete();
            $purchaseOrder->delete();
            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dibatalkan/dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan PO: ' . $e->getMessage());
        }
    }
}

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
        $items = Item::orderBy('nama_barang')->get();
        
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
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate No PO
            $datePrefix = Carbon::parse($request->tanggal_po)->format('Ymd');
            $lastOrder = PurchaseOrder::whereDate('tanggal_po', $request->tanggal_po)->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (int)substr($lastOrder->no_po, -4) + 1 : 1;
            $noPo = 'PO-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // 2. Create Header
            $purchaseOrder = PurchaseOrder::create([
                'no_po' => $noPo,
                'tanggal_po' => $request->tanggal_po,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1), // Default user for now
                'status' => 'Ordered' 
            ]);

            // 3. Process Details
            foreach ($request->items as $itemData) {
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $itemData['id'],
                    'qty_butuh' => $itemData['qty_butuh']
                ]);
            }

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

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status == 'Received') {
            return back()->with('error', 'Barang pada PO ini sudah diterima sebelumnya.');
        }

        try {
            DB::beginTransaction();

            foreach ($purchaseOrder->details as $detail) {
                $item = Item::lockForUpdate()->find($detail->item_id);
                if ($item) {
                    $item->stok_saat_ini += $detail->qty_butuh;
                    $item->save();

                    StockMovement::create([
                        'item_id' => $item->id,
                        'tipe_pergerakan' => 'IN',
                        'qty' => $detail->qty_butuh,
                        'sisa_stok' => $item->stok_saat_ini,
                        'referensi' => 'Terima PO: ' . $purchaseOrder->no_po,
                        'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                        'timestamp' => now()
                    ]);
                }
            }

            $purchaseOrder->update(['status' => 'Received']);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Barang telah berhasil diterima dan stok ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penerimaan barang: ' . $e->getMessage());
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

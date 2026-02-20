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

            // 1. Generate No Faktur
            $datePrefix = Carbon::parse($request->tanggal_transaksi)->format('Ymd');
            $lastOrder = SalesOrder::whereDate('tanggal_transaksi', $request->tanggal_transaksi)->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (int)substr($lastOrder->no_faktur, -4) + 1 : 1;
            $noFaktur = 'INV-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // 2. Create Header
            $salesOrder = SalesOrder::create([
                'no_faktur' => $noFaktur,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'customer_id' => $request->customer_id,
                // Dynamically fetch first user if not authenticated
                'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1), 
                'total_invoice' => 0, // Will be calculated
                'status' => 'Selesai' // Automatically assumed 'Selesai/Dikirim' for simplicity unless explicitly drafting
            ]);

            $totalInvoice = 0;

            // 3. Process Details
            foreach ($request->items as $itemData) {
                $item = Item::lockForUpdate()->findOrFail($itemData['id']);
                
                $qty = $itemData['qty'];
                $diskon = $itemData['diskon'] ?? 0;
                
                if ($item->stok_saat_ini < $qty) {
                    throw new \Exception("Stok tidak mencukupi untuk item: {$item->nama_barang}. Stok tersedia: {$item->stok_saat_ini}");
                }

                $hargaSatuan = $item->harga_jual_default;
                $subtotalNetto = ($hargaSatuan * $qty) - $diskon;
                $totalInvoice += $subtotalNetto;

                // Create Detail
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'harga_satuan_saat_transaksi' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal_netto' => $subtotalNetto
                ]);

                // Update Item Stock
                $item->stok_saat_ini -= $qty;
                $item->save();

                // Create Stock Movement (Audit)
                StockMovement::create([
                    'item_id' => $item->id,
                    'tipe_pergerakan' => 'OUT',
                    'qty' => $qty,
                    'sisa_stok' => $item->stok_saat_ini,
                    'referensi' => 'SO: ' . $noFaktur,
                    'user_id' => auth()->id() ?? (\App\Models\User::first()->id ?? 1),
                    'timestamp' => now()
                ]);
            }

            // Update Total Invoice
            $salesOrder->update(['total_invoice' => $totalInvoice]);

            DB::commit();

            return redirect()->route('sales-orders.show', $salesOrder->id)->with('success', 'Transaksi Penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
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

    // Edit and Update are intentionally omitted to maintain data integrity for accounting
    // Usually sales orders are voided or restocked via return mechanism in a strict system.
    
    public function destroy(SalesOrder $salesOrder)
    {
        // Cancel/Void mechanism
        try {
            DB::beginTransaction();
            
            if ($salesOrder->status == 'Batal') {
                throw new \Exception('Transaksi sudah dibatalkan sebelumnya.');
            }

            // Return items to stock
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
            
            return redirect()->route('sales-orders.index')->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-orders.index')->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}

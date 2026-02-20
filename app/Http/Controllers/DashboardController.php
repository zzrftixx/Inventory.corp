<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // General Metrics
        $totalItems = Item::count();
        $totalCustomers = Customer::count();
        $todaySO = SalesOrder::whereDate('tanggal_transaksi', Carbon::today())->count();
        $todayRevenue = SalesOrder::whereDate('tanggal_transaksi', Carbon::today())
                                    ->where('status', '!=', 'Batal')
                                    ->sum('total_invoice');
        
        $pendingPO = PurchaseOrder::where('status', 'Ordered')->count();

        // Stock Alerts (Red Zone)
        $lowStockItems = Item::whereRaw('stok_saat_ini <= batas_stok_minimum')
                               ->with('category')
                               ->orderBy('stok_saat_ini', 'asc')
                               ->get();

        // Recent Outbound Activity
        $recentSO = SalesOrder::with('customer')
                                ->latest('created_at')
                                ->take(5)
                                ->get();

        // Recent Inbound Activity
        $recentPO = PurchaseOrder::with('supplier')
                                ->latest('created_at')
                                ->take(5)
                                ->get();

        return view('dashboard.index', compact(
            'totalItems', 'totalCustomers', 'todaySO', 'todayRevenue', 
            'pendingPO', 'lowStockItems', 'recentSO', 'recentPO'
        ));
    }
}

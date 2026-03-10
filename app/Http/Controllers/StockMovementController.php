<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['item', 'user'])
            ->orderBy('timestamp', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('timestamp', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('timestamp', '<=', $request->end_date);
        }
        if ($request->filled('type') && $request->type != 'ALL') {
            $query->where('tipe_pergerakan', $request->type);
        }

        $movements = $query->paginate(20)->withQueryString();

        return view('stock_movements.index', compact('movements'));
    }
}

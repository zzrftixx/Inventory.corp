<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')->latest()->paginate(10);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'kode_barang' => 'required|string|unique:items,kode_barang|max:255',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_jual_default' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
            'is_aluminium' => 'nullable|boolean',
            'berat_profil_kg' => 'nullable|numeric|min:0.001|required_if:is_aluminium,1',
            'panjang_meter' => 'nullable|numeric|min:0.01|required_if:is_aluminium,1',
            'harga_dasar_aluminium_kg' => 'nullable|numeric|min:0|required_if:is_aluminium,1',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['is_aluminium'] = $request->has('is_aluminium') ? 1 : 0;

        Item::create($validatedData);
        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $rules = [
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $item->id,
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_jual_default' => 'required|numeric|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
            'is_aluminium' => 'nullable|boolean',
            'berat_profil_kg' => 'nullable|numeric|min:0.001|required_if:is_aluminium,1',
            'panjang_meter' => 'nullable|numeric|min:0.01|required_if:is_aluminium,1',
            'harga_dasar_aluminium_kg' => 'nullable|numeric|min:0|required_if:is_aluminium,1',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['is_aluminium'] = $request->has('is_aluminium') ? 1 : 0;

        // Omit stok_saat_ini from direct update to maintain data integrity later,
        // unless explicitly needed. For now we will allow it if present.
        $item->update($validatedData);

        return redirect()->route('items.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->stockMovements()->count() > 0) {
            return redirect()->route('items.index')->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat pergerakan stok.');
        }

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    // Backend API Logic for Select2 AJAX Autocomplete
    public function searchAjax(Request $request)
    {
        $search = $request->input('q');

        // Jika user belum ngetik apa-apa, kembalikan array kosong agar server tidak kerja keras
        if (empty($search)) {
            return response()->json([]);
        }

        // Eksekusi pencarian dengan limit 20
        $items = Item::where('kode_barang', 'LIKE', "%{$search}%")
            ->orWhere('nama_barang', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get(['id', 'kode_barang', 'nama_barang', 'harga_jual_default', 'stok_saat_ini', 'satuan']);

        // Format data agar bisa dibaca langsung oleh library Select2
        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->kode_barang . ' - ' . $item->nama_barang,
                'price' => $item->harga_jual_default,
                'stok_saat_ini' => $item->stok_saat_ini,
                'satuan' => $item->satuan
            ];
        });

        return response()->json($formattedItems);
    }
}

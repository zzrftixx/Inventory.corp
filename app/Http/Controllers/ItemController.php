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
        $request->validate([
            'kode_barang' => 'required|string|unique:items,kode_barang|max:255',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_jual_default' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
        ]);

        Item::create($request->all());
        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $item->id,
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_jual_default' => 'required|numeric|min:0',
            'batas_stok_minimum' => 'required|integer|min:0',
        ]);

        // Omit stok_saat_ini from direct update to maintain data integrity later,
        // unless explicitly needed. For now we will allow it if present.
        $item->update($request->except(['stok_saat_ini']));
        
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
}

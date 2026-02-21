@extends('layouts.app')

@section('title', 'Edit Barang')
@section('header')
<div class="flex items-center">
    <a href="{{ route('items.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Edit Barang Master
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800">Detail Barang</h2>
        <p class="text-sm text-slate-500">Perbarui spesifikasi barang. <b>Stok saat ini</b> tidak dapat diedit langsung; gunakan modul penyesuaian stok.</p>
    </div>
    
    <form action="{{ route('items.update', $item->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="kode_barang" class="block text-sm font-medium text-slate-700 mb-1">Kode Barang <span class="text-red-500">*</span></label>
                <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang', $item->kode_barang) }}" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('kode_barang')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-1 md:col-span-2">
                <label for="nama_barang" class="block text-sm font-medium text-slate-700 mb-1">Nama Barang Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('nama_barang')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 col-span-1 md:col-span-2">
                <div>
                    <label for="satuan" class="block text-sm font-medium text-slate-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $item->satuan) }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('satuan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_jual_default" class="block text-sm font-medium text-slate-700 mb-1">Harga Jual Default (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_jual_default" id="harga_jual_default" value="{{ old('harga_jual_default', (int)$item->harga_jual_default) }}" required min="0" step="1"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('harga_jual_default')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 col-span-1 md:col-span-2 bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Stok Saat Ini</label>
                    <input type="number" readonly value="{{ $item->stok_saat_ini }}"
                        class="w-full px-4 py-2 border border-slate-200 bg-slate-100 rounded-lg outline-none text-slate-500 cursor-not-allowed font-medium">
                    <p class="text-xs text-slate-400 mt-1">Stok terkunci. Edit via Modul Inbound/Outbound.</p>
                </div>
                <div>
                    <label for="batas_stok_minimum" class="block text-sm font-medium text-slate-700 mb-1">Batas Stok Minimum <span class="text-red-500">*</span></label>
                    <input type="number" name="batas_stok_minimum" id="batas_stok_minimum" value="{{ old('batas_stok_minimum', $item->batas_stok_minimum) }}" required min="0"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                    @error('batas_stok_minimum')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 space-x-3">
            <a href="{{ route('items.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

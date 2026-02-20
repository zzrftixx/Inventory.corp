@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('header')
<div class="flex items-center">
    <a href="{{ route('suppliers.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Tambah Supplier
</div>
@endsection

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800">Informasi Supplier Baru</h2>
        <p class="text-sm text-slate-500">Isi kelengkapan data pabrik atau distributor baru.</p>
    </div>
    
    <form action="{{ route('suppliers.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        
        <div>
            <label for="nama_supplier" class="block text-sm font-medium text-slate-700 mb-1">Nama Supplier/Pabrik <span class="text-red-500">*</span></label>
            <input type="text" name="nama_supplier" id="nama_supplier" value="{{ old('nama_supplier') }}" required
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                placeholder="Contoh: PT. Bintang Aluminium">
            @error('nama_supplier')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kontak" class="block text-sm font-medium text-slate-700 mb-1">Kontak / Telepon</label>
            <input type="text" name="kontak" id="kontak" value="{{ old('kontak') }}"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                placeholder="Contoh: 0812-3456-7890">
            @error('kontak')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 space-x-3">
            <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">Simpan Supplier</button>
        </div>
    </form>
</div>
@endsection

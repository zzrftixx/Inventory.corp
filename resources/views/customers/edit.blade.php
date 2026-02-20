@extends('layouts.app')

@section('title', 'Edit Customer')
@section('header')
<div class="flex items-center">
    <a href="{{ route('customers.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Edit Customer
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800">Informasi Customer</h2>
        <p class="text-sm text-slate-500">Perbarui data pelanggan atau kontraktor ini.</p>
    </div>
    
    <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1 md:col-span-2">
                <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Customer / Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $customer->nama) }}" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('nama')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-1 md:col-span-2">
                <label for="no_telp" class="block text-sm font-medium text-slate-700 mb-1">No Telepon / WhatsApp</label>
                <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp', $customer->no_telp) }}"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('no_telp')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-1 md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">{{ old('alamat', $customer->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 space-x-3">
            <a href="{{ route('customers.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Data Barang & Stok')
@section('header', 'Data Barang & Stok')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Daftar Barang Master</h2>
        <p class="text-sm text-slate-500">Kelola inventaris, harga jual, dan profil limit stok</p>
    </div>
    <a href="{{ route('items.create') }}" class="bg-primary hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
        <i class="ph ph-plus font-bold mr-2"></i> Tambah Barang
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-sm">Kode</th>
                    <th class="py-3 px-6 font-medium text-sm">Nama Barang</th>
                    <th class="py-3 px-6 font-medium text-sm">Kategori</th>
                    <th class="py-3 px-6 font-medium text-sm">Harga Jual</th>
                    <th class="py-3 px-6 font-medium text-sm text-center">Stok</th>
                    <th class="py-3 px-6 font-medium text-sm text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-sm font-semibold text-slate-700">{{ $item->kode_barang }}</td>
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">{{ $item->nama_barang }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">Rp {{ number_format($item->harga_jual_default, 0, ',', '.') }}<span class="text-xs text-slate-400">/{{ $item->satuan }}</span></td>
                    <td class="py-3 px-6 text-sm text-center">
                        @if($item->stok_saat_ini <= $item->batas_stok_minimum)
                            <span class="bg-red-100 text-red-700 py-1 px-2 rounded-md text-xs font-bold inline-flex items-center">
                                <i class="ph-fill ph-warning-circle mr-1"></i> {{ $item->stok_saat_ini }}
                            </span>
                        @else
                            <span class="bg-emerald-100 text-emerald-700 py-1 px-2 rounded-md text-xs font-bold">
                                {{ $item->stok_saat_ini }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('items.edit', $item->id) }}" class="text-slate-400 hover:text-primary transition-colors p-1" title="Edit">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus master data barang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Hapus">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data barang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection

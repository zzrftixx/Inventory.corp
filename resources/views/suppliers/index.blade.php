@extends('layouts.app')

@section('title', 'Data Supplier')
@section('header', 'Data Supplier')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Daftar Supplier</h2>
        <p class="text-sm text-slate-500">Kelola data pabrik atau distributor penyuplai barang</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="bg-primary hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
        <i class="ph ph-plus font-bold mr-2"></i> Tambah Supplier
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-sm">#</th>
                    <th class="py-3 px-6 font-medium text-sm">Nama Supplier</th>
                    <th class="py-3 px-6 font-medium text-sm">Kontak/Telepon</th>
                    <th class="py-3 px-6 font-medium text-sm">Histori PO</th>
                    <th class="py-3 px-6 font-medium text-sm text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($suppliers as $index => $sup)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $suppliers->firstItem() + $index }}</td>
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">{{ $sup->nama_supplier }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $sup->kontak ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">
                        <span class="bg-slate-100 text-slate-600 py-1 px-2 rounded-md text-xs font-medium">
                            {{ $sup->purchaseOrders()->count() }} PO
                        </span>
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('suppliers.edit', $sup->id) }}" class="text-slate-400 hover:text-primary transition-colors p-1" title="Edit">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data supplier ini?');">
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
                    <td colspan="5" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data supplier distributor.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection

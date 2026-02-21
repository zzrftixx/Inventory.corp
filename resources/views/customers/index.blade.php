@extends('layouts.app')

@section('title', 'Data Customer')
@section('header', 'Data Customer')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Daftar Customer / Klien</h2>
        <p class="text-sm text-slate-500">Kelola data pelanggan dan kontraktor langganan</p>
    </div>
    <a href="{{ route('customers.create') }}" class="bg-primary hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
        <i class="ph ph-plus font-bold mr-2"></i> Tambah Customer
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-sm">#</th>
                    <th class="py-3 px-6 font-medium text-sm">Nama Customer</th>
                    <th class="py-3 px-6 font-medium text-sm">No Telepon</th>
                    <th class="py-3 px-6 font-medium text-sm">Alamat</th>
                    <th class="py-3 px-6 font-medium text-sm">Pemesanan</th>
                    <th class="py-3 px-6 font-medium text-sm text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $index => $cust)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $customers->firstItem() + $index }}</td>
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">{{ $cust->nama }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $cust->no_telp ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600 max-w-xs truncate" title="{{ $cust->alamat }}">{{ $cust->alamat ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">
                        <span class="bg-slate-100 text-slate-600 py-1 px-2 rounded-md text-xs font-medium">
                            {{ $cust->salesOrders()->count() }} SO
                        </span>
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('customers.edit', $cust->id) }}" class="text-slate-400 hover:text-primary transition-colors p-1" title="Edit">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $cust->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data customer ini?');">
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
                        Belum ada data customer atau kontraktor.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection

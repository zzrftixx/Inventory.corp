@extends('layouts.app')

@section('title', 'Data Penjualan (Outbound)')
@section('header', 'Data Penjualan (SO)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Riwayat Sales Order</h2>
        <p class="text-sm text-slate-500">Daftar transaksi penjualan dan pengeluaran barang keluar.</p>
    </div>
    <a href="{{ route('sales-orders.create') }}" class="bg-primary hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
        <i class="ph ph-shopping-cart font-bold mr-2"></i> Buat Order Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-sm">No. Faktur</th>
                    <th class="py-3 px-6 font-medium text-sm">Tanggal</th>
                    <th class="py-3 px-6 font-medium text-sm">Customer</th>
                    <th class="py-3 px-6 font-medium text-sm">Total Invoice</th>
                    <th class="py-3 px-6 font-medium text-sm text-center">Status</th>
                    <th class="py-3 px-6 font-medium text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($salesOrders as $so)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-sm font-semibold text-primary">{{ $so->no_faktur }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ \Carbon\Carbon::parse($so->tanggal_transaksi)->format('d M Y') }}</td>
                    <td class="py-3 px-6 text-sm text-slate-800 font-medium">{{ $so->customer->nama ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm font-semibold text-slate-700">Rp {{ number_format($so->total_invoice, 0, ',', '.') }}</td>
                    <td class="py-3 px-6 text-sm text-center">
                        @if($so->status == 'Selesai')
                            <span class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded-full text-xs font-semibold">Selesai (Locked)</span>
                        @elseif($so->status == 'Draft')
                            <span class="bg-amber-100 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold hover:animate-pulse">Draft</span>
                        @else
                            <span class="bg-slate-100 text-slate-800 py-1 px-3 rounded-full text-xs font-semibold">{{ $so->status }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex justify-end space-x-2 items-center">
                            @if($so->status == 'Draft')
                                <a href="{{ route('sales-orders.edit', $so->id) }}" class="text-white bg-amber-500 hover:bg-amber-600 px-3 py-1.5 rounded text-xs font-medium transition-colors" title="Edit Draft">
                                    <i class="ph ph-pencil-simple font-bold"></i>
                                </a>
                            @endif
                            <a href="{{ route('sales-orders.show', $so->id) }}" class="text-white bg-slate-600 hover:bg-slate-700 px-3 py-1.5 rounded text-xs font-medium transition-colors" title="Lihat Detail">
                                Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada riwayat transaksi penjualan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($salesOrders->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $salesOrders->links() }}
    </div>
    @endif
</div>
@endsection

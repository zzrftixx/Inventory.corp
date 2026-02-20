@extends('layouts.app')

@section('title', 'Data Purchase Order (Inbound)')
@section('header', 'Daftar Purchase Order (PO)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Riwayat Pembelian Barang</h2>
        <p class="text-sm text-slate-500">Kelola pemesanan dan penerimaan barang dari Supplier.</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('purchase-orders.create', ['auto' => 1]) }}" class="bg-accent hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
            <i class="ph ph-magic-wand font-bold mr-2"></i> Auto-Generate PO
        </a>
        <a href="{{ route('purchase-orders.create') }}" class="bg-primary hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
            <i class="ph ph-plus font-bold mr-2"></i> Buat PO Manual
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-sm">No. PO</th>
                    <th class="py-3 px-6 font-medium text-sm">Tanggal</th>
                    <th class="py-3 px-6 font-medium text-sm">Supplier</th>
                    <th class="py-3 px-6 font-medium text-sm text-center">Status</th>
                    <th class="py-3 px-6 font-medium text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($purchaseOrders as $po)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-sm font-semibold text-primary">{{ $po->no_po }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}</td>
                    <td class="py-3 px-6 text-sm text-slate-800 font-medium">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-center">
                        @if($po->status == 'Received')
                            <span class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded-full text-xs font-semibold">Diterima Lengkap</span>
                        @elseif($po->status == 'Ordered')
                            <span class="bg-amber-100 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold">Menunggu Kedatangan</span>
                        @else
                            <span class="bg-slate-100 text-slate-800 py-1 px-3 rounded-full text-xs font-semibold">{{ $po->status }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-white bg-slate-600 hover:bg-slate-700 px-3 py-1.5 rounded text-xs font-medium transition-colors" title="Lihat Detail">
                                Detail & Proses
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada riwayat Purchase Order.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchaseOrders->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $purchaseOrders->links() }}
    </div>
    @endif
</div>
@endsection

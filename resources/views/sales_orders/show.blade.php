@extends('layouts.app')

@section('title', 'Detail Sales Order')
@section('header')
<div class="flex items-center">
    <a href="{{ route('sales-orders.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Detail Transaksi ({{ $salesOrder->no_faktur }})
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 col-span-1 lg:col-span-2">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Transaksi</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400 mb-1">No. Faktur</p>
                <p class="text-sm font-semibold text-primary">{{ $salesOrder->no_faktur }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Status</p>
                @if($salesOrder->status == 'Selesai')
                    <span class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded text-xs font-semibold">Selesai (Locked)</span>
                @elseif($salesOrder->status == 'Draft')
                    <span class="bg-amber-100 text-amber-800 py-1 px-3 rounded text-xs font-semibold hover:animate-pulse">Draft</span>
                @elseif($salesOrder->status == 'Batal')
                    <span class="bg-red-100 text-red-800 py-1 px-3 rounded text-xs font-semibold">Dibatalkan</span>
                @else
                    <span class="bg-slate-100 text-slate-800 py-1 px-3 rounded text-xs font-semibold">{{ $salesOrder->status }}</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Tanggal Transaksi</p>
                <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($salesOrder->tanggal_transaksi)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Admin Pencatat</p>
                <p class="text-sm font-medium text-slate-800">{{ $salesOrder->user->name ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 col-span-1">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Klien / Customer</h3>
        
        <p class="text-base font-bold text-slate-800 mb-1">{{ $salesOrder->customer->nama ?? 'Tidak Diketahui' }}</p>
        <p class="text-sm text-slate-600 mb-2 whitespace-pre-line">{{ $salesOrder->customer->alamat ?? '-' }}</p>
        <div class="flex items-center text-sm text-slate-500">
            <i class="ph-fill ph-phone mr-2"></i> {{ $salesOrder->customer->no_telp ?? '-' }}
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <h3 class="text-sm font-semibold text-slate-800">Rincian Barang Keluar</h3>
        <div class="flex space-x-2">
            @if($salesOrder->status == 'Selesai')
                <a href="{{ route('sales-orders.surat-jalan', $salesOrder->id) }}" target="_blank" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded text-sm font-medium transition-colors flex items-center shadow-sm">
                    <i class="ph ph-printer mr-2 text-slate-400"></i> Cetak Surat Jalan
                </a>
                <a href="{{ route('sales-orders.faktur', $salesOrder->id) }}" target="_blank" class="bg-primary hover:bg-sky-600 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors flex items-center shadow-sm">
                    <i class="ph ph-printer mr-2 text-sky-200"></i> Cetak Faktur (Invoice)
                </a>
            @elseif($salesOrder->status == 'Draft')
                <form action="{{ route('sales-orders.confirm', $salesOrder->id) }}" method="POST" onsubmit="return confirm('Kunci transaksi ini? QTY barang akan memotong stok gudang permanen.');">
                    @csrf
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-1.5 rounded text-sm font-bold transition-colors shadow-sm flex items-center animate-pulse">
                        <i class="ph ph-lock-key mr-2"></i> Submit & Kunci Transaksi
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider">Deskripsi Barang</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center">Qty</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-right">Harga Satuan</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-right">Diskon</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-right">Total Netto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($salesOrder->details as $index => $detail)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $index + 1 }}</td>
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">
                        {{ $detail->item->nama_barang ?? 'Unknown' }}
                        <div class="text-xs text-slate-400 mt-0.5">Kode: {{ $detail->item->kode_barang ?? '-' }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-center font-semibold text-slate-700">
                        {{ $detail->qty }} <span class="text-xs text-slate-500 font-normal ml-0.5">{{ $detail->item->satuan ?? '' }}</span>
                    </td>
                    <td class="py-3 px-6 text-sm text-right text-slate-600">Rp {{ number_format($detail->harga_satuan_saat_transaksi, 0, ',', '.') }}</td>
                    <td class="py-3 px-6 text-sm text-right text-slate-600">Rp {{ number_format($detail->diskon, 0, ',', '.') }}</td>
                    <td class="py-3 px-6 text-sm text-right font-semibold text-slate-800">Rp {{ number_format($detail->subtotal_netto, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200">
                <tr>
                    <td colspan="5" class="py-4 px-6 text-right font-bold text-slate-700 uppercase tracking-widest text-xs">Total Pembayaran</td>
                    <td class="py-4 px-6 text-right font-bold text-primary text-xl">Rp {{ number_format($salesOrder->total_invoice, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($salesOrder->status != 'Batal')
<div class="flex justify-end mt-8 border-t border-slate-200 pt-6">
    <form action="{{ route('sales-orders.destroy', $salesOrder->id) }}" method="POST" onsubmit="return confirm('{{ $salesOrder->status == 'Draft' ? 'Hapus DRAFT transaksi ini secara permanen?' : 'PERINGATAN! Membatalkan pesanan ini akan menarik semua barang ke gudang (stok bertambah) kembali. Lanjutkan?' }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 border border-red-200 rounded-lg text-sm font-medium transition-colors flex items-center">
            @if($salesOrder->status == 'Draft')
                <i class="ph ph-trash mr-2 text-lg"></i> Hapus Draft
            @else
                <i class="ph ph-warning-circle mr-2 text-lg"></i> Void / Batalkan Transaksi Ini
            @endif
        </button>
    </form>
</div>
@endif
@endsection

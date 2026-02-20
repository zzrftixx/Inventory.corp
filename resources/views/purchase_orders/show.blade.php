@extends('layouts.app')

@section('title', 'Detail Purchase Order')
@section('header')
<div class="flex items-center">
    <a href="{{ route('purchase-orders.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Detail PO ({{ $purchaseOrder->no_po }})
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 col-span-1 lg:col-span-2">
        <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-2">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Informasi Purchase Order</h3>
            @if($purchaseOrder->status == 'Ordered')
                <span class="bg-amber-100 text-amber-800 py-1 px-3 rounded text-xs font-semibold animate-pulse">Menunggu Barang Datang</span>
            @elseif($purchaseOrder->status == 'Received')
                <span class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded text-xs font-semibold"><i class="ph-fill ph-check-circle mr-1"></i> Selesai Diterima</span>
            @endif
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400 mb-1">No. Purchase Order</p>
                <p class="text-sm font-bold text-primary">{{ $purchaseOrder->no_po }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Tanggal Pesan</p>
                <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Pembuat PO</p>
                <p class="text-sm font-medium text-slate-800">{{ $purchaseOrder->user->name ?? 'Administrator' }}</p>
            </div>
        </div>

        @if($purchaseOrder->status == 'Ordered')
        <div class="mt-6 p-4 bg-sky-50 border border-sky-200 rounded-lg flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-sky-800">Barang Sudah Tiba di Gudang?</h4>
                <p class="text-xs text-sky-700 mt-1">Klik tombol di samping jika fisik barang sudah diterima dan dihitung. Sistem akan otomatis menambah stok.</p>
            </div>
            <form action="{{ route('purchase-orders.receive', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin barang sudah diterima dan jumlahnya sesuai? Stok sistem akan bertambah otomatis.');">
                @csrf
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center nowrap whitespace-nowrap">
                    <i class="ph-fill ph-check-square-offset text-lg mr-2"></i> Konfirmasi Penerimaan
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 col-span-1 flex flex-col justify-between">
        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Ditujukan Ke Supplier</h3>
            <p class="text-base font-bold text-slate-800 mb-1">{{ $purchaseOrder->supplier->nama_supplier ?? 'Tidak Diketahui' }}</p>
            <div class="flex items-center text-sm text-slate-500 mt-2">
                <i class="ph-fill ph-phone mr-2"></i> {{ $purchaseOrder->supplier->kontak ?? '-' }}
            </div>
        </div>
        <div class="mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('purchase-orders.print', $purchaseOrder->id) }}" target="_blank" class="w-full justify-center bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
                <i class="ph ph-printer mr-2 text-slate-400 text-lg"></i> Cetak / PDF Purchase Order
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-100 bg-slate-50">
        <h3 class="text-sm font-semibold text-slate-800">Rincian Barang Dipesan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200 text-slate-600">
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider w-16 text-center">No</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider">Kode Barang</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider">Nama Barang</th>
                    <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center">Qty Dipesan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($purchaseOrder->details as $index => $detail)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-6 text-sm text-center text-slate-600">{{ $index + 1 }}</td>
                    <td class="py-3 px-6 text-sm font-mono text-slate-500">{{ $detail->item->kode_barang ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">{{ $detail->item->nama_barang ?? 'Unknown' }}</td>
                    <td class="py-3 px-6 text-sm text-center font-bold text-slate-800">
                        {{ $detail->qty_butuh }} <span class="text-xs text-slate-500 font-normal ml-1">{{ $detail->item->satuan ?? '' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($purchaseOrder->status == 'Ordered')
<div class="flex justify-end mt-4">
    <form action="{{ route('purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin DIBATALKAN/HAPUS pesanan ini? Aksi ini tidak dapat dikembalikan.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-slate-400 hover:text-red-600 px-4 py-2 text-sm font-medium transition-colors flex items-center">
            <i class="ph ph-trash mr-2 text-lg"></i> Hapus / Batalkan PO
        </button>
    </form>
</div>
@endif
@endsection

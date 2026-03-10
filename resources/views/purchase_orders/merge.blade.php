@extends('layouts.app')

@section('title', 'Gabung Draft Purchase Order')
@section('header')
    <div class="flex items-center">
        <a href="{{ route('purchase-orders.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
            <i class="ph ph-arrow-left text-xl"></i>
        </a>
        Konsolidasi Draft PO (Merge PO)
    </div>
@endsection

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-800">Menemukan {{ $draftPOs->count() }} Supplier dengan Draft PO Ganda
        </h2>
        <p class="text-sm text-slate-500">Fitur ini menggabungkan beberapa Draft PO ke supplier yang sama menjadi satu
            dokumen tunggal untuk memenuhi spesifikasi pengiriman dan MOQ pabrik.</p>
    </div>

    @if($draftPOs->isEmpty())
        <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-xl p-6 text-center shadow-sm">
            <i class="ph ph-check-circle text-4xl mb-3 text-indigo-500"></i>
            <h3 class="font-bold text-lg">Gudang Bersih!</h3>
            <p class="text-sm mt-1">Tidak ada Supplier yang memiliki lebih dari satu Draft PO saat ini. Semua antrean order
                sudah teroptimalisasi.</p>
            <div class="mt-4">
                <a href="{{ route('purchase-orders.index') }}"
                    class="inline-block bg-white text-indigo-700 px-4 py-2 font-medium text-sm rounded border border-indigo-300 hover:bg-indigo-100 transition-colors">Kembali
                    ke Daftar PO</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($draftPOs as $supplierId => $pos)
                @php $supplier = $pos->first()->supplier; @endphp
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-800">{{ $supplier->nama_supplier ?? 'Supplier Tidak Diketahui' }}</h3>
                            <p class="text-xs text-slate-500 mt-1"><i class="ph-fill ph-files mr-1"></i> {{ $pos->count() }} Dokumen
                                Terpisah</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Total Nilai Tagihan</p>
                            <p class="font-bold text-indigo-600">Rp {{ number_format($pos->sum('total_amount_po'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="p-4">
                        <ul class="text-sm text-slate-600 mb-4 divide-y divide-slate-100 border border-slate-100 rounded">
                            @foreach($pos as $po)
                                <li class="py-2 px-3 flex justify-between items-center hover:bg-slate-50">
                                    <span class="font-medium font-mono text-slate-700">{{ $po->no_po }}</span>
                                    <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500">{{ $po->details->count() }}
                                        item</span>
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('purchase-orders.merge.process', $supplierId) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menggabungkan {{ $pos->count() }} dokumen ini? Dokumen lama akan dihapus dan diganti dengan 1 PO baru.');">
                            @csrf
                            <button type="submit"
                                class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-colors flex justify-center items-center">
                                <i class="ph-fill ph-intersect text-lg mr-2"></i> Gabungkan {{ $pos->count() }} Dokumen
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
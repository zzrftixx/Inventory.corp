@extends('layouts.app')

@section('title', 'Dashboard - CV Ma Karya')
@section('header', 'Overview Dashboard')

@section('content')

<!-- Welcome Banner & Alert -->
<div class="mb-6">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 blur-xl">
            <i class="ph-fill ph-buildings text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Selamat Datang di Command Center!</h2>
                <p class="text-slate-300 mt-1">Sistem ERP Inventory CV Ma Karya Artha Graha. Status: <span class="text-emerald-400 font-semibold text-sm bg-emerald-400/10 px-2 py-0.5 rounded ml-1">Online</span></p>
            </div>
            <div class="mt-4 md:mt-0 text-right">
                <p class="text-sm font-medium text-slate-400">Tanggal Sistem</p>
                <p class="text-xl font-bold font-mono text-primary">{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4">
            <i class="ph-fill ph-shopping-cart text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Penjualan Hari Ini</p>
            <p class="text-2xl font-bold text-slate-800">{{ $todaySO }} <span class="text-xs font-normal text-slate-400">Trx</span></p>
        </div>
    </div>
    
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center">
        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
            <i class="ph-fill ph-currency-circle-dollar text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Nilai Transaksi (Harian)</p>
            <p class="text-xl font-bold text-slate-800 tracking-tight">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center">
        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 mr-4">
            <i class="ph-fill ph-clock-countdown text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">PO Pending (Inbound)</p>
            <p class="text-2xl font-bold text-slate-800">{{ $pendingPO }} <span class="text-xs font-normal text-slate-400">Menunggu</span></p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center">
        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-4">
            <i class="ph-fill ph-box-arrow-down text-2xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Total Macam Barang</p>
            <p class="text-2xl font-bold text-slate-800">{{ $totalItems }} <span class="text-xs font-normal text-slate-400">Master</span></p>
        </div>
    </div>
</div>

<!-- Main Dashboard Body -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Alerts -->
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden h-full flex flex-col">
            <div class="p-4 border-b border-red-100 bg-red-50 flex justify-between items-center">
                <div class="flex items-center">
                    <i class="ph-fill ph-warning-circle text-red-500 text-xl mr-2 animate-pulse"></i>
                    <h3 class="font-bold text-red-800">Critical Stock Alerts</h3>
                </div>
                <!-- Badge Count -->
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($lowStockItems) }}</span>
            </div>
            
            <div class="p-0 flex-1 overflow-y-auto max-h-[400px]">
                @if(count($lowStockItems) > 0)
                    <ul class="divide-y divide-slate-100">
                        @foreach($lowStockItems as $item)
                        <li class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-sm text-slate-800 line-clamp-1" title="{{ $item->nama_barang }}">{{ $item->nama_barang }}</p>
                                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $item->kode_barang }}</p>
                                </div>
                                <div class="text-right ml-2 shrink-0">
                                    <p class="text-lg font-bold text-red-600 leading-none">{{ $item->stok_saat_ini }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold text-center mt-1">Sisa Stok</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-slate-500">Batas Min: {{ $item->batas_stok_minimum }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        <a href="{{ route('purchase-orders.create', ['auto' => 1]) }}" class="w-full justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center shadow-sm">
                            <i class="ph-fill ph-magic-wand mr-2"></i> Auto-Generate PO Sekarang
                        </a>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4">
                            <i class="ph-fill ph-check-shield text-3xl"></i>
                        </div>
                        <p class="font-medium text-slate-600 text-sm">Gudang Aman!</p>
                        <p class="text-xs mt-1">Tidak ada limit stok kritis.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Activities -->
    <div class="col-span-1 lg:col-span-2 space-y-6">
        
        <!-- Outbound -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center"><i class="ph-fill ph-arrow-circle-up text-primary mr-2 text-lg"></i> Transaksi Penjualan Terakhir</h3>
                <a href="{{ route('sales-orders.index') }}" class="text-xs font-semibold text-primary hover:text-sky-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSO as $so)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 font-semibold text-primary pl-4"><a href="{{ route('sales-orders.show', $so->id) }}">{{ $so->no_faktur }}</a></td>
                            <td class="p-3 text-slate-600">{{ $so->customer->nama ?? '-' }}</td>
                            <td class="p-3 font-semibold text-slate-800 text-right">Rp {{ number_format($so->total_invoice, 0, ',', '.') }}</td>
                            <td class="p-3 pr-4 text-right">
                                @if($so->status == 'Selesai')
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Selesai</span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">{{ $so->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-6 text-center text-slate-400 text-xs">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inbound -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center"><i class="ph-fill ph-arrow-circle-down text-accent mr-2 text-lg"></i> Purchase Order Terakhir</h3>
                <a href="{{ route('purchase-orders.index') }}" class="text-xs font-semibold text-accent hover:text-amber-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentPO as $po)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 font-semibold text-slate-800 pl-4"><a href="{{ route('purchase-orders.show', $po->id) }}" class="hover:underline">{{ $po->no_po }}</a></td>
                            <td class="p-3 text-slate-600">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                            <td class="p-3 text-slate-500">{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M y') }}</td>
                            <td class="p-3 pr-4 text-right">
                                @if($po->status == 'Ordered')
                                    <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider animate-pulse">Ordered</span>
                                @elseif($po->status == 'Received')
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Received</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-6 text-center text-slate-400 text-xs">Belum ada aktivitas PO.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

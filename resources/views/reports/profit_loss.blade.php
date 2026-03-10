@extends('layouts.app')

@section('title', 'Laporan Laba Rugi Eksekutif')
@section('header', 'Laporan Laba Rugi (Executive Report)')

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-800">Analisis Margin & Profit</h2>
        <p class="text-sm text-slate-500">Laporan khusus Super Admin untuk memantau performa penjualan dan HPP (Harga Pokok
            Penjualan) riil.</p>
    </div>

    <!-- Filter & Export Area -->
    <div
        class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6 flex flex-col md:flex-row justify-between items-end gap-4">
        <form action="{{ route('reports.profit-loss') }}" method="GET"
            class="flex flex-col md:flex-row items-end gap-4 w-full md:w-auto">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors flex items-center shadow-sm w-full md:w-auto justify-center">
                    <i class="ph ph-funnel mr-2"></i> Filter Date
                </button>
                <a href="{{ route('reports.profit-loss') }}"
                    class="bg-white hover:bg-slate-50 text-slate-600 px-4 py-2 rounded-md text-sm font-semibold transition-colors border border-slate-200 flex items-center shadow-sm w-full md:w-auto justify-center">
                    Reset
                </a>
            </div>
        </form>

        <a href="{{ route('reports.profit-loss', array_merge(request()->query(), ['export' => 'excel'])) }}"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-md text-sm font-bold transition-colors shadow-sm flex items-center whitespace-nowrap mt-4 md:mt-0">
            <i class="ph ph-file-xls mr-2 text-lg"></i> Export Excel / CSV
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-blue-500">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Pendapatan (Omzet)</h3>
            <p class="text-2xl font-black text-slate-800">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-rose-500">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Modal Keluar (HPP)</h3>
            <p class="text-2xl font-black text-slate-800">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</p>
        </div>

        <div
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-emerald-500 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-5">
                <i class="ph ph-money text-8xl"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Laba Kotor</h3>
            <p class="text-2xl font-black {{ $summary['laba_kotor'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                Rp {{ number_format($summary['laba_kotor'], 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1 {{ $summary['laba_kotor'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                Margin:
                {{ $summary['total_pendapatan'] > 0 ? number_format(($summary['laba_kotor'] / $summary['total_pendapatan']) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-700">Rincian Transaksi Laba Rugi</h3>
            <div class="text-xs text-slate-500"><i class="ph ph-lock-key mr-1 text-rose-500"></i> Protected Executive View
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-6 font-medium">No. Faktur</th>
                        <th class="py-3 px-6 font-medium">Tanggal</th>
                        <th class="py-3 px-6 font-medium">Detail Rincian Item (Modal & Jual)</th>
                        <th class="py-3 px-6 font-medium text-right">Laba Trx</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($salesOrders as $order)
                        @php
                            $orderModal = 0;
                            foreach ($order->details as $d) {
                                $orderModal += ($d->qty * $d->harga_modal_saat_transaksi);
                            }
                            $orderLaba = $order->total_invoice - $orderModal;
                        @endphp
                        <tr class="hover:bg-slate-50 group">
                            <td class="py-4 px-6 font-medium text-blue-600 align-top">
                                <a href="{{ route('sales-orders.show', $order->id) }}"
                                    class="hover:underline">{{ $order->no_faktur }}</a>
                                <div class="text-xs text-slate-500 font-normal mt-1">{{ $order->customer->nama ?? 'Umum' }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 align-top whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($order->tanggal_transaksi)->format('d M Y') }}</td>

                            <td class="py-4 px-6 align-top">
                                <div class="space-y-3">
                                    @foreach($order->details as $detail)
                                        @php
                                            $itemModalTotal = $detail->qty * $detail->harga_modal_saat_transaksi;
                                            $itemLaba = $detail->subtotal_netto - $itemModalTotal;
                                        @endphp
                                        <div class="bg-white p-3 rounded border border-slate-100 shadow-sm text-xs relative">
                                            <div class="font-bold text-slate-800 mb-1">{{ $detail->item->nama_barang ?? 'Unknown' }}
                                                <span class="text-slate-500 font-normal">({{ $detail->qty }}
                                                    {{ $detail->item->satuan ?? 'PCS' }})</span></div>

                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                <div>
                                                    <span class="text-slate-400 block mb-0.5">Pendapatan Netto</span>
                                                    <span class="font-semibold text-slate-700">Rp
                                                        {{ number_format($detail->subtotal_netto, 0, ',', '.') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-slate-400 block mb-0.5">Modal Berjalan (HPP)</span>
                                                    <span class="font-semibold text-rose-600">Rp
                                                        {{ number_format($itemModalTotal, 0, ',', '.') }}</span>
                                                    @if($detail->metadata_kalkulasi)
                                                        @php $meta = json_decode($detail->metadata_kalkulasi); @endphp
                                                        <i class="ph-fill ph-warning-circle text-rose-400 ml-1 cursor-help"
                                                            title="Aluminium Rule: {{ $meta->rumus }}"></i>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="absolute top-3 right-3 text-right">
                                                <div class="font-bold {{ $itemLaba >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $itemLaba >= 0 ? '+' : '' }} Rp {{ number_format($itemLaba, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <td class="py-4 px-6 text-right align-top">
                                <div class="text-lg font-black {{ $orderLaba >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $orderLaba >= 0 ? '+' : '' }} Rp {{ number_format($orderLaba, 0, ',', '.') }}
                                </div>
                                <div
                                    class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 border-t border-slate-200 pt-2">
                                    Total Invoice</div>
                                <div class="text-xs font-bold text-slate-700">Rp
                                    {{ number_format($order->total_invoice, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 text-sm">
                                <i class="ph ph-receipt-x text-4xl block mb-2 opacity-50"></i>
                                Tidak ada transaksi penjualan dalam rentang waktu ini.
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
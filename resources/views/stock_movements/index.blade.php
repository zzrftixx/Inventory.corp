@extends('layouts.app')

@section('title', 'Audit Trails & Pergerakan Stok')
@section('header', 'Audit Trails - Pergerakan Stok')

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-800">Catatan Mutasi Stok (Log Histori)</h2>
        <p class="text-sm text-slate-500">Tampilan read-only untuk memantau semua riwayat barang masuk (IN) dan keluar
            (OUT).</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <form action="{{ route('stock-movements.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
            <div class="w-full md:w-auto">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            <div class="w-full md:w-auto flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Mutasi</label>
                <select name="type"
                    class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="ALL">Semua Tipe (Masuk & Keluar)</option>
                    <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>IN (Barang Masuk / Tambah Stok)
                    </option>
                    <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>OUT (Barang Keluar / Kurang Stok)
                    </option>
                    <option value="RETUR" {{ request('type') == 'RETUR' ? 'selected' : '' }}>RETUR (Pengembalian Barang)
                    </option>
                </select>
            </div>
            <div class="w-full md:w-auto flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors flex items-center shadow-sm w-full md:w-auto justify-center">
                    <i class="ph ph-funnel mr-2"></i> Filter Data
                </button>
                <a href="{{ route('stock-movements.index') }}"
                    class="bg-white hover:bg-slate-50 text-slate-600 px-4 py-2 rounded-md text-sm font-semibold transition-colors border border-slate-200 flex items-center shadow-sm w-full md:w-auto justify-center">
                    <i class="ph ph-arrow-counter-clockwise mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="py-3 px-6 font-medium text-sm">Waktu Transaksi</th>
                        <th class="py-3 px-6 font-medium text-sm">Nama/Kode Barang</th>
                        <th class="py-3 px-6 font-medium text-sm text-center">Tipe</th>
                        <th class="py-3 px-6 font-medium text-sm text-center">Qty Mutasi</th>
                        <th class="py-3 px-6 font-medium text-sm text-center">Sisa Stok (Sistem)</th>
                        <th class="py-3 px-6 font-medium text-sm">Referensi / Alasan</th>
                        <th class="py-3 px-6 font-medium text-sm">User Eksekutor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($movements as $mov)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-6 text-slate-600 font-mono text-xs">
                                {{ \Carbon\Carbon::parse($mov->timestamp)->format('d-m-Y H:i:s') }}</td>
                            <td class="py-3 px-6 font-medium text-slate-800">
                                {{ $mov->item->nama_barang ?? 'Deleted Item' }}
                                <span
                                    class="block text-xs font-normal text-slate-400 font-mono">{{ $mov->item->kode_barang ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($mov->tipe_pergerakan == 'IN')
                                    <span
                                        class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded-full text-[10px] font-bold tracking-wider">IN</span>
                                @elseif($mov->tipe_pergerakan == 'OUT')
                                    <span
                                        class="bg-rose-100 text-rose-800 py-1 px-3 rounded-full text-[10px] font-bold tracking-wider">OUT</span>
                                @else
                                    <span
                                        class="bg-slate-100 text-slate-800 py-1 px-3 rounded-full text-[10px] font-bold tracking-wider">{{ $mov->tipe_pergerakan }}</span>
                                @endif
                            </td>
                            <td
                                class="py-3 px-6 text-center font-bold {{ $mov->tipe_pergerakan == 'IN' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mov->tipe_pergerakan == 'IN' ? '+' : '-' }}{{ $mov->qty }}
                            </td>
                            <td class="py-3 px-6 text-center font-bold text-slate-800">{{ $mov->sisa_stok }}</td>
                            <td class="py-3 px-6 text-slate-600 text-xs">{{ $mov->referensi }}</td>
                            <td class="py-3 px-6 text-slate-500 font-medium">{{ $mov->user->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada aktivitas mutasi barang di gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection
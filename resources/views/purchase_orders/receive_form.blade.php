@extends('layouts.app')

@section('title', 'Penerimaan Parsial Barang (Partial Receiving)')
@section('header')
    <div class="flex items-center">
        <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}"
            class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
            <i class="ph ph-arrow-left text-xl"></i>
        </a>
        Form Penerimaan PO ({{ $purchaseOrder->no_po }})
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Form Input Goods Receipt</h3>
                <p class="text-xs text-slate-500 mt-1">Hanya input stok sesuai dengan fisik barang yang tiba.</p>
            </div>
            <div>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded">Status:
                    {{ $purchaseOrder->status }}</span>
            </div>
        </div>

        <form action="{{ route('purchase-orders.receive', $purchaseOrder->id) }}" method="POST">
            @csrf

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-100">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No Surat Jalan Supplier (Opsional)</label>
                    <input type="text" name="no_surat_jalan_supplier" value="{{ old('no_surat_jalan_supplier') }}"
                        class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        placeholder="Contoh: SJ-092/SUPP/2026">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Terima FIsik <span
                            class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', date('Y-m-d')) }}"
                        class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Penerimaan (Opsional)</label>
                    <textarea name="catatan" rows="2"
                        class="w-full text-sm border-slate-200 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        placeholder="Kondisi barang, kekurangan, dll">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider">Barang</th>
                            <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center">Total Dipesan
                            </th>
                            <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center">Telah Diterima
                            </th>
                            <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center">Sisa Kekurangan
                            </th>
                            <th class="py-3 px-6 font-medium text-xs uppercase tracking-wider text-center w-48">Diterima
                                Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($purchaseOrder->details as $index => $detail)
                            @php
                                $already_received = $detail->receipts->sum('qty_diterima');
                                $remaining = $detail->qty_butuh - $already_received;
                            @endphp
                            <tr class="hover:bg-slate-50 {{ $remaining <= 0 ? 'bg-emerald-50 opacity-60' : '' }}">
                                <td class="py-4 px-6">
                                    <p class="text-sm font-medium text-slate-800">{{ $detail->item->nama_barang ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $detail->item->kode_barang ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-6 text-center text-sm font-medium text-slate-700">{{ $detail->qty_butuh }}
                                </td>
                                <td class="py-4 px-6 text-center text-sm font-medium text-emerald-600">{{ $already_received }}
                                </td>
                                <td class="py-4 px-6 text-center text-sm font-bold text-rose-500">
                                    {{ $remaining > 0 ? $remaining : 0 }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($remaining > 0)
                                        <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                        <input type="number" name="items[{{ $index }}][qty]" max="{{ $remaining }}" min="0"
                                            value="0"
                                            class="w-full text-center text-sm border-slate-300 rounded focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    @else
                                        <span class="text-xs font-bold text-emerald-600"><i
                                                class="ph-fill ph-check-circle mr-1"></i>LENGKAP</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors">Batal</a>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center">
                    <i class="ph-fill ph-check-square-offset mr-2 text-lg"></i> Simpan Penerimaan Barang
                </button>
            </div>
        </form>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Sales Order')
@section('header')
<div class="flex items-center">
    <a href="{{ route('sales-orders.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    Edit Draft Sales Order ({{ $salesOrder->no_faktur }})
</div>
@endsection

@section('content')
<div class="w-full bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Ubah Transaksi Tersimpan (Draft)</h2>
            <p class="text-sm text-slate-500">Anda masih bisa merevisi pesanan karena statusnya belum dikunci (Locked).</p>
        </div>
        <span class="bg-amber-100 text-amber-800 py-1.5 px-4 rounded-full text-xs font-bold uppercase tracking-wider">Status: Draft</span>
    </div>
    
    <form action="{{ route('sales-orders.update', $salesOrder->id) }}" method="POST" id="soForm">
        @csrf
        @method('PUT')
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50">
            <div>
                <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Customer / Klien <span class="text-red-500">*</span></label>
                <select name="customer_id" id="customer_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ $salesOrder->customer_id == $cust->id ? 'selected' : '' }}>{{ $cust->nama }}</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_transaksi" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" value="{{ \Carbon\Carbon::parse($salesOrder->tanggal_transaksi)->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('tanggal_transaksi')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="p-6 border-t border-slate-100">
            <h3 class="text-md font-semibold text-slate-800 mb-4">Revisi Item Pesanan</h3>
            
            <div class="flex space-x-3 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Barang Baru</label>
                    <select id="item_selector" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none bg-white">
                        <option value="">-- Ketik/Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}" data-harga="{{ $item->harga_jual_default }}" data-satuan="{{ $item->satuan }}" data-stok="{{ $item->stok_saat_ini }}">
                                {{ $item->kode_barang }} - {{ $item->nama_barang }} (Stok: {{ $item->stok_saat_ini }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btn-add-item" class="bg-secondary hover:bg-slate-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center h-[42px]">
                    <i class="ph ph-plus font-bold mr-2"></i> Tambah
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-left border-collapse" id="trxTable">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600">
                            <th class="py-3 px-4 font-medium text-sm">Kode</th>
                            <th class="py-3 px-4 font-medium text-sm">Nama Barang</th>
                            <th class="py-3 px-4 font-medium text-sm text-right">Harga (Rp)</th>
                            <th class="py-3 px-4 font-medium text-sm w-32">Qty</th>
                            <th class="py-3 px-4 font-medium text-sm w-32">Diskon (Rp)</th>
                            <th class="py-3 px-4 font-medium text-sm text-right">Subtotal</th>
                            <th class="py-3 px-4 font-medium text-sm text-center w-16">#</th>
                        </tr>
                    </thead>
                    <tbody id="items-container" class="divide-y divide-slate-100">
                        @foreach($salesOrder->details as $detail)
                        <tr id="row-{{ $detail->item_id }}">
                            <td class="py-3 px-4 text-sm font-mono text-slate-600">
                                <input type="hidden" name="items[{{ $detail->item_id }}][id]" value="{{ $detail->item_id }}">
                                {{ $detail->item->kode_barang ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-slate-800">{{ $detail->item->nama_barang ?? 'Unknown' }}</td>
                            <td class="py-3 px-4 text-sm text-right">
                                {{ number_format($detail->harga_satuan_saat_transaksi, 0, ',', '.') }}
                                <input type="hidden" id="harga-{{ $detail->item_id }}" value="{{ $detail->harga_satuan_saat_transaksi }}">
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <input type="number" name="items[{{ $detail->item_id }}][qty]" id="qty-{{ $detail->item_id }}" value="{{ $detail->qty }}" min="1" max="{{ $detail->item->stok_saat_ini }}" class="w-16 px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-center" onchange="calculateRow({{ $detail->item_id }})" onkeyup="calculateRow({{ $detail->item_id }})">
                                    <span class="ml-2 text-xs text-slate-500">/{{ $detail->item->satuan ?? '-' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">Max: {{ $detail->item->stok_saat_ini }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <input type="number" name="items[{{ $detail->item_id }}][diskon]" id="diskon-{{ $detail->item_id }}" value="{{ $detail->diskon }}" min="0" class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-right" onchange="calculateRow({{ $detail->item_id }})" onkeyup="calculateRow({{ $detail->item_id }})">
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-slate-700 text-right" id="subtotal-{{ $detail->item_id }}">
                                {{ number_format($detail->subtotal_netto, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button" onclick="removeRow({{ $detail->item_id }})" class="text-slate-400 hover:text-red-500 p-1">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr>
                            <td colspan="5" class="py-4 px-4 text-right font-bold text-slate-700">Total Invoice</td>
                            <td class="py-4 px-4 text-right font-bold text-primary text-lg" id="grand_total">Rp {{ number_format($salesOrder->total_invoice, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @error('items')<p class="mt-2 text-sm text-red-500">Minimal harus ada 1 barang dalam pesanan.</p>@enderror
        </div>

        <div class="flex justify-between p-6 border-t border-slate-100 bg-slate-50">
            <div>
                <a href="{{ route('sales-orders.show', $salesOrder->id) }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors inline-block">Batal Ubah</a>
            </div>
            <button type="button" onclick="validateAndSubmit()" class="px-6 py-2.5 text-sm font-bold text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">
                Simpan Perubahan Draft
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 0; // We keep it, though not strictly needed here for IDs if using DB ID
    
    document.getElementById('btn-add-item').addEventListener('click', function() {
        const selector = document.getElementById('item_selector');
        const selectedOption = selector.options[selector.selectedIndex];
        
        if (!selectedOption.value) {
            alert('Silakan pilih barang terlebih dahulu!');
            return;
        }

        const id = selectedOption.value;
        const kode = selectedOption.getAttribute('data-kode');
        const nama = selectedOption.getAttribute('data-nama');
        const harga = parseFloat(selectedOption.getAttribute('data-harga'));
        const satuan = selectedOption.getAttribute('data-satuan');
        const stok = parseInt(selectedOption.getAttribute('data-stok'));

        // Cek duplicate
        const exist = document.querySelector(`input[name="items[${id}][id]"]`);
        if (exist) {
            alert('Barang ini sudah ada dalam list, silahkan sesuaikan Qty-nya.');
            return;
        }

        // Hapus empty state
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.id = `row-${id}`;
        tr.innerHTML = `
            <td class="py-3 px-4 text-sm font-mono text-slate-600">
                <input type="hidden" name="items[${id}][id]" value="${id}">
                ${kode}
            </td>
            <td class="py-3 px-4 text-sm font-medium text-slate-800">${nama}</td>
            <td class="py-3 px-4 text-sm text-right">
                ${new Intl.NumberFormat('id-ID').format(harga)}
                <input type="hidden" id="harga-${id}" value="${harga}">
            </td>
            <td class="py-3 px-4">
                <div class="flex items-center">
                    <input type="number" name="items[${id}][qty]" id="qty-${id}" value="1" min="1" max="${stok}" class="w-16 px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-center" onchange="calculateRow(${id})" onkeyup="calculateRow(${id})">
                    <span class="ml-2 text-xs text-slate-500">/${satuan}</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-1">Max: ${stok}</div>
            </td>
            <td class="py-3 px-4">
                <input type="number" name="items[${id}][diskon]" id="diskon-${id}" value="0" min="0" class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-right" onchange="calculateRow(${id})" onkeyup="calculateRow(${id})">
            </td>
            <td class="py-3 px-4 text-sm font-semibold text-slate-700 text-right" id="subtotal-${id}">
                ${new Intl.NumberFormat('id-ID').format(harga)}
            </td>
            <td class="py-3 px-4 text-center">
                <button type="button" onclick="removeRow(${id})" class="text-slate-400 hover:text-red-500 p-1">
                    <i class="ph ph-trash text-lg"></i>
                </button>
            </td>
        `;
        
        document.getElementById('items-container').appendChild(tr);
        itemIndex++;
        calculateTotal();
        
        // Reset selector
        selector.selectedIndex = 0;
    });

    function calculateRow(id) {
        const harga = parseFloat(document.getElementById(`harga-${id}`).value) || 0;
        let qtyInput = document.getElementById(`qty-${id}`);
        let qty = parseFloat(qtyInput.value) || 0;
        const maxStok = parseFloat(qtyInput.getAttribute('max'));
        
        if (qty > maxStok) {
            alert('Qty melebihi stok yang tersedia!');
            qtyInput.value = maxStok;
            qty = maxStok;
        }

        let diskon = parseFloat(document.getElementById(`diskon-${id}`).value) || 0;

        const subtotal = (harga * qty) - diskon;
        document.getElementById(`subtotal-${id}`).innerText = new Intl.NumberFormat('id-ID').format(subtotal > 0 ? subtotal : 0);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        rows.forEach(row => {
            const id = row.id.split('-')[1];
            const harga = parseFloat(document.getElementById(`harga-${id}`).value) || 0;
            const qty = parseFloat(document.getElementById(`qty-${id}`).value) || 0;
            const diskon = parseFloat(document.getElementById(`diskon-${id}`).value) || 0;
            const subtotal = (harga * qty) - diskon;
            if(subtotal > 0) total += subtotal;
        });

        document.getElementById('grand_total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function removeRow(id) {
        document.getElementById(`row-${id}`).remove();
        calculateTotal();
        
        // Cek kalau kosong
        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        if (rows.length === 0) {
            document.getElementById('items-container').innerHTML = `
                <tr id="empty-row">
                    <td colspan="7" class="py-6 text-center text-slate-400 text-sm">Belum ada barang yang ditambahkan.</td>
                </tr>
            `;
        }
    }

    function validateAndSubmit() {
        // Prevent default / normal submit
        const customerField = document.getElementById('customer_id').value;
        if (!customerField) {
            alert('Silakan pilih Customer/Klien.');
            return;
        }

        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        if (rows.length === 0) {
            alert('Silakan tambahkan minimal 1 barang ke dalam pesanan.');
            return;
        }
        
        if (confirm('Simpan perubahan pada draft kasir?')) {
            document.getElementById('soForm').submit();
        }
    }
</script>
@endsection

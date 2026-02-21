@extends('layouts.app')

@section('title', 'Buat Purchase Order')
@section('header')
<div class="flex items-center">
    <a href="{{ route('purchase-orders.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
        <i class="ph ph-arrow-left text-xl"></i>
    </a>
    {{ request()->has('auto') ? 'Auto-Generate Purchase Order' : 'Buat Purchase Order Manual' }}
</div>
@endsection

@section('content')
@if(request()->has('auto') && count($autoItems) == 0)
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg flex items-center shadow-sm">
    <i class="ph-fill ph-check-circle text-2xl mr-3 text-emerald-500"></i>
    <div>
        <h3 class="font-semibold text-sm">Gudang Aman!</h3>
        <p class="text-sm">Saat ini tidak ada barang yang stoknya berada di bawah batas minimum.</p>
    </div>
</div>
@endif

<div class="w-full bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Form Pemesanan (Inbound)</h2>
            <p class="text-sm text-slate-500">Pesan barang ke supplier untuk mengisi stok gudang.</p>
        </div>
        @if(request()->has('auto'))
        <span class="bg-accent text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm">
            <i class="ph-fill ph-magic-wand mr-1"></i> Mode Restock Otomatis
        </span>
        @endif
    </div>
    
    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
        @csrf
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50">
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-slate-700 mb-1">Pabrik / Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" id="supplier_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_po" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pemesanan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_po" id="tanggal_po" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                @error('tanggal_po')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="p-6 border-t border-slate-100">
            <h3 class="text-md font-semibold text-slate-800 mb-4">Daftar Kebutuhan Barang</h3>
            
            <div class="flex space-x-3 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tambah Barang Tambahan</label>
                    <select id="item_selector" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none bg-white">
                        <option value="">-- Ketik/Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}" data-satuan="{{ $item->satuan }}" data-stok="{{ $item->stok_saat_ini }}" data-min="{{ $item->batas_stok_minimum }}" data-hargabeli="{{ $item->harga_beli_rata_rata }}">
                                {{ $item->kode_barang }} - {{ $item->nama_barang }} (Stok: {{ $item->stok_saat_ini }} | Min: {{ $item->batas_stok_minimum }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btn-add-item" class="bg-secondary hover:bg-slate-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center h-[42px]">
                    <i class="ph ph-plus font-bold mr-2"></i> Tambah ke List
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-left border-collapse" id="poTable">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600">
                            <th class="py-3 px-4 font-medium text-sm">Kode</th>
                            <th class="py-3 px-4 font-medium text-sm">Nama Barang</th>
                            <th class="py-3 px-4 font-medium text-sm text-center">Stok Anda</th>
                            <th class="py-3 px-4 font-medium text-sm w-32 text-center">Qty <span class="text-red-500">*</span></th>
                            <th class="py-3 px-4 font-medium text-sm w-40 text-right">Harga Beli Sat. <span class="text-red-500">*</span></th>
                            <th class="py-3 px-4 font-medium text-sm w-32 text-right">Subtotal</th>
                            <th class="py-3 px-4 font-medium text-sm text-center w-16">#</th>
                        </tr>
                    </thead>
                    <tbody id="items-container" class="divide-y divide-slate-100">
                        @if(request()->has('auto') && count($autoItems) > 0)
                            @foreach($autoItems as $auto)
                            <tr id="row-{{ $auto->id }}" class="bg-red-50">
                                <td class="py-3 px-4 text-sm font-mono text-slate-600">
                                    <input type="hidden" name="items[{{ $auto->id }}][id]" value="{{ $auto->id }}">
                                    {{ $auto->kode_barang }}
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-slate-800">{{ $auto->nama_barang }}</td>
                                <td class="py-3 px-4 text-sm text-center font-bold text-red-600">
                                    {{ $auto->stok_saat_ini }}
                                    <span class="block text-[10px] text-slate-500 font-normal">Min: {{ $auto->batas_stok_minimum }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center">
                                        @php $recOrder = ($auto->batas_stok_minimum * 2) - $auto->stok_saat_ini; $recOrder = $recOrder < 1 ? 10 : $recOrder; @endphp
                                        <input type="number" name="items[{{ $auto->id }}][qty_butuh]" id="qty-{{ $auto->id }}" value="{{ $recOrder }}" min="1" class="w-16 px-2 py-1 text-sm border border-red-300 rounded focus:outline-none focus:border-red-500 text-center" onchange="calcRow({{ $auto->id }})" onkeyup="calcRow({{ $auto->id }})">
                                        <span class="ml-2 text-xs text-slate-500">/{{ $auto->satuan }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <input type="number" name="items[{{ $auto->id }}][harga_beli_satuan]" id="harga-{{ $auto->id }}" value="{{ $auto->harga_beli_rata_rata }}" min="0" step="1" class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-right" onchange="calcRow({{ $auto->id }})" onkeyup="calcRow({{ $auto->id }})">
                                </td>
                                <td class="py-3 px-4 text-right text-sm font-semibold text-slate-800" id="subtotal-{{ $auto->id }}">
                                    {{ number_format($recOrder * $auto->harga_beli_rata_rata, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" onclick="removeRow({{ $auto->id }})" class="text-slate-400 hover:text-red-500 p-1"><i class="ph ph-trash text-lg"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr id="empty-row">
                                <td colspan="7" class="py-6 text-center text-slate-400 text-sm">List pesanan masih kosong. Silakan tambah barang di atas.</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr>
                            <td colspan="5" class="py-4 px-4 text-right font-bold text-slate-700">Estimasi Total PO:</td>
                            <td class="py-4 px-4 text-right font-bold text-primary text-lg" id="grand_total">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @error('items')<p class="mt-2 text-sm text-red-500">Minimal harus ada 1 barang untuk di-order.</p>@enderror
        </div>

        <div class="flex justify-end p-6 border-t border-slate-100 bg-slate-50 space-x-3">
            <a href="{{ route('purchase-orders.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors">Batal</a>
            <button type="button" onclick="validateAndSubmit()" class="px-6 py-2.5 text-sm font-bold text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">
                Terbitkan Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
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
        const satuan = selectedOption.getAttribute('data-satuan');
        const stok = parseInt(selectedOption.getAttribute('data-stok'));
        const min = parseInt(selectedOption.getAttribute('data-min'));
        const hargaBeli = parseFloat(selectedOption.getAttribute('data-hargabeli')) || 0;

        const exist = document.querySelector(`input[name="items[${id}][id]"]`);
        if (exist) {
            alert('Barang ini sudah ada dalam list pesanan.');
            return;
        }

        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const rowColorClass = stok <= min ? 'bg-red-50' : '';
        const stokColor = stok <= min ? 'text-red-600' : 'text-slate-800';

        const tr = document.createElement('tr');
        tr.id = `row-${id}`;
        tr.className = rowColorClass;
        tr.innerHTML = `
            <td class="py-3 px-4 text-sm font-mono text-slate-600">
                <input type="hidden" name="items[${id}][id]" value="${id}">
                ${kode}
            </td>
            <td class="py-3 px-4 text-sm font-medium text-slate-800">${nama}</td>
            <td class="py-3 px-4 text-sm text-center font-bold ${stokColor}">
                ${stok}
                <span class="block text-[10px] text-slate-500 font-normal">Min: ${min}</span>
            </td>
            <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center">
                    <input type="number" name="items[${id}][qty_butuh]" id="qty-${id}" value="10" min="1" class="w-16 px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-center" onchange="calcRow(${id})" onkeyup="calcRow(${id})">
                    <span class="ml-2 text-xs text-slate-500">/${satuan}</span>
                </div>
            </td>
            <td class="py-3 px-4 text-right">
                <input type="number" name="items[${id}][harga_beli_satuan]" id="harga-${id}" value="${hargaBeli}" min="0" step="1" class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:border-primary text-right" onchange="calcRow(${id})" onkeyup="calcRow(${id})">
            </td>
            <td class="py-3 px-4 text-right text-sm font-semibold text-slate-800" id="subtotal-${id}">
                ${new Intl.NumberFormat('id-ID').format(10 * hargaBeli)}
            </td>
            <td class="py-3 px-4 text-center">
                <button type="button" onclick="removeRow(${id})" class="text-slate-400 hover:text-red-500 p-1">
                    <i class="ph ph-trash text-lg"></i>
                </button>
            </td>
        `;
        
        document.getElementById('items-container').appendChild(tr);
        selector.selectedIndex = 0;
        calcTotalPO();
    });

    function calcRow(id) {
        let harga = parseFloat(document.getElementById(`harga-${id}`).value) || 0;
        let qty = parseFloat(document.getElementById(`qty-${id}`).value) || 0;
        let subtotal = harga * qty;
        document.getElementById(`subtotal-${id}`).innerText = new Intl.NumberFormat('id-ID').format(subtotal);
        calcTotalPO();
    }

    function calcTotalPO() {
        let total = 0;
        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        rows.forEach(row => {
            const id = row.id.split('-')[1];
            let harga = parseFloat(document.getElementById(`harga-${id}`).value) || 0;
            let qty = parseFloat(document.getElementById(`qty-${id}`).value) || 0;
            total += (harga * qty);
        });
        const gt = document.getElementById('grand_total');
        if(gt) gt.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    // Call on load to setup auto mode totals
    calcTotalPO();

    function removeRow(id) {
        document.getElementById(`row-${id}`).remove();
        calcTotalPO();
        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        if (rows.length === 0) {
            document.getElementById('items-container').innerHTML = `
                <tr id="empty-row">
                    <td colspan="7" class="py-6 text-center text-slate-400 text-sm">List pesanan masih kosong. Silakan tambah barang di atas.</td>
                </tr>
            `;
        }
    }

    function validateAndSubmit() {
        if (!document.getElementById('supplier_id').value) {
            alert('Pilih Supplier terlebih dahulu.');
            return;
        }

        const rows = document.getElementById('items-container').querySelectorAll('tr[id^="row-"]');
        if (rows.length === 0) {
            alert('Data barang yang mau diorder masih kosong.');
            return;
        }

        document.getElementById('poForm').submit();
    }
</script>
@endsection

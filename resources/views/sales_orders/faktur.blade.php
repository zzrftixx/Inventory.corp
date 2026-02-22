<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $sales_order->no_faktur }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #fff; color: #000; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto">

    <!-- Print Action -->
    <div class="mb-6 flex justify-end no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Faktur (Invoice)
        </button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-[#0ea5e9]">CV MA KARYA Artha Graha</h1>
            <p class="text-sm mt-1 text-gray-500 font-medium tracking-wide">ALUMINIUM & GLASS FABRICATION</p>
            <div class="mt-3 text-sm text-gray-700">
                <p>Jl. Karang Tengah Sitimulyo, Karang Anom, Sitimulyo, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55792</p>
                <p>Telp: 0851 0158 8887 | Email: cv.makarya.ag@gmail.com</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-4xl font-black text-gray-800 tracking-tight">INVOICE</h2>
            <div class="mt-4 bg-gray-50 p-3 rounded text-left inline-block border border-gray-200">
                <table class="text-sm">
                    <tr>
                        <td class="text-gray-500 pr-4 pb-1">No Faktur:</td>
                        <td class="font-bold text-gray-800">{{ $sales_order->no_faktur }}</td>
                    </tr>
                    <tr>
                        <td class="text-gray-500 pr-4">Tanggal:</td>
                        <td class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($sales_order->tanggal_transaksi)->format('d F Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Bill To -->
    <div class="mb-8">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">PENAqIHAN KEPADA (BILL TO):</p>
        <p class="text-lg font-bold text-gray-800">{{ $sales_order->customer->nama ?? 'Pelanggan Umum' }}</p>
        <p class="text-sm text-gray-600 mt-1 max-w-md">{{ $sales_order->customer->alamat ?? '-' }}</p>
        <p class="text-sm text-gray-600 mt-1 font-medium">Telp/WA: {{ $sales_order->customer->no_telp ?? '-' }}</p>
    </div>

    <!-- Table Rincian (Dengan Harga) -->
    <table class="w-full mb-8">
        <thead>
            <tr class="bg-gray-800 text-white rounded-t">
                <th class="py-3 px-4 text-left font-semibold text-sm rounded-tl">Deskripsi Barang</th>
                <th class="py-3 px-4 text-center font-semibold text-sm">Qty (Satuan)</th>
                <th class="py-3 px-4 text-right font-semibold text-sm">Harga Satuan</th>
                <th class="py-3 px-4 text-right font-semibold text-sm">Diskon</th>
                <th class="py-3 px-4 text-right font-semibold text-sm rounded-tr">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales_order->details as $index => $detail)
            <tr class="border-b border-gray-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                <td class="py-3 px-4 text-left">
                    <p class="font-bold text-gray-800">{{ $detail->item->nama_barang ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $detail->item->kode_barang ?? '-' }}</p>
                </td>
                <td class="py-3 px-4 text-center text-gray-800">
                    <span class="font-bold text-lg">{{ $detail->qty }}</span> {{ $detail->item->satuan ?? '' }}
                </td>
                <td class="py-3 px-4 text-right text-gray-600 font-mono">
                    {{ number_format($detail->harga_satuan_saat_transaksi, 0, ',', '.') }}
                </td>
                <td class="py-3 px-4 text-right text-gray-600 font-mono">
                    {{ $detail->diskon > 0 ? number_format($detail->diskon, 0, ',', '.') : '-' }}
                </td>
                <td class="py-3 px-4 text-right font-bold text-gray-800 font-mono">
                    {{ number_format($detail->subtotal_netto, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-12">
        <div class="w-1/2 md:w-1/3">
            <table class="w-full text-sm">
                <tr class="border-t-2 border-gray-800 text-lg">
                    <td class="text-right py-2 font-bold text-gray-700 uppercase pr-6">Grand Total:</td>
                    <td class="text-right py-2 font-black text-[#0ea5e9] tracking-wider font-mono">Rp {{ number_format($sales_order->total_invoice, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="flex justify-between items-end border-t border-gray-200 mt-16 pt-6">
        <div class="text-sm text-gray-500">
            <p class="font-bold text-gray-700 mb-1">Metode Pembayaran:</p>
            <p>Transfer Bank BCA: <b>123-456-7890</b></p>
            <p>A.N: CV Ma Karya Artha Graha</p>
        </div>
        
        <div class="text-center">
            <p class="text-sm font-bold text-gray-700 mb-16">Hormat Kami,</p>
            <p class="font-bold underline text-gray-800">Finance Manager</p>
        </div>
    </div>

</body>
</html>

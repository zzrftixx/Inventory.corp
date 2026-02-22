<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $sales_order->no_faktur }}</title>
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
            Cetak Surat Jalan
        </button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-widest text-[#1e3a8a]">CV MA KARYA ARTHA GRAHA</h1>
            <p class="text-sm mt-1">Specialist Aluminium & Glass Contractor</p>
            <p class="text-sm">Telp: 0851 0158 8887</p>
            <p class="text-sm">Email: cv.makarya.ag@gmail.com</p>
            <p class="text-sm w-80 mt-1">Jl. Karang Tengah Sitimulyo, Karang Anom, Sitimulyo, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55792</p>
        </div>
        <div class="text-right">
            <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-widest">Surat Jalan</h2>
            <p class="text-sm mt-2 text-gray-500 font-mono">No. Dokumen:</p>
            <p class="text-lg font-bold font-mono">{{ $sales_order->no_faktur }}</p>
        </div>
    </div>

    <!-- Info Customer -->
    <div class="flex justify-between mb-8">
        <div class="w-1/2 pr-4 border-r border-gray-300">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Kepada Yth:</p>
            <p class="text-base font-bold">{{ $sales_order->customer->nama ?? 'Umum' }}</p>
            <p class="text-sm">{{ $sales_order->customer->alamat ?? '-' }}</p>
            <p class="text-sm mt-1">Telp: {{ $sales_order->customer->no_telp ?? '-' }}</p>
        </div>
        <div class="w-1/2 pl-4">
            <table class="w-full text-sm">
                <tr>
                    <td class="text-gray-500 py-1 w-32">Tanggal Kirim:</td>
                    <td class="font-bold border-b border-dotted border-gray-400">{{ \Carbon\Carbon::parse($sales_order->tanggal_transaksi)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="text-gray-500 py-1">Supir/Ekspedisi:</td>
                    <td class="font-bold border-b border-dotted border-gray-400 pb-1"></td>
                </tr>
                <tr>
                    <td class="text-gray-500 py-1">No Polisi:</td>
                    <td class="font-bold border-b border-dotted border-gray-400 pb-1"></td>
                </tr>
            </table>
        </div>
    </div>

    <p class="text-sm mb-2 font-medium">Harap diterima dengan baik barang-barang berikut ini:</p>

    <!-- Table Barang (Tanpa Harga) -->
    <table class="w-full mb-8 border border-black">
        <thead>
            <tr class="bg-gray-100 border-b border-black">
                <th class="py-2 px-3 text-center border-r border-black w-12">No</th>
                <th class="py-2 px-3 text-left border-r border-black">Nama / Dekripsi Barang</th>
                <th class="py-2 px-3 text-center border-r border-black w-32">Qty</th>
                <th class="py-2 px-3 text-center w-32">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales_order->details as $index => $detail)
            <tr class="border-b border-gray-300">
                <td class="py-2 px-3 text-center border-r border-black">{{ $index + 1 }}</td>
                <td class="py-2 px-3 text-left border-r border-black">
                    {{ $detail->item->nama_barang ?? 'Unknown' }}
                    <div class="text-xs text-gray-500">{{ $detail->item->kode_barang ?? '-' }}</div>
                </td>
                <td class="py-2 px-3 text-center border-r border-black font-bold text-lg">
                    {{ $detail->qty }} <span class="text-xs font-normal text-gray-600">{{ $detail->item->satuan ?? '' }}</span>
                </td>
                <td class="py-2 px-3 text-center"></td>
            </tr>
            @endforeach
            <!-- Padding rows if needed -->
            @for($i = count($sales_order->details); $i < 4; $i++)
            <tr class="border-b border-gray-300">
                <td class="py-4 px-3 border-r border-black text-center text-transparent">-</td>
                <td class="py-4 px-3 border-r border-black text-transparent">-</td>
                <td class="py-4 px-3 border-r border-black text-center text-transparent">-</td>
                <td class="py-4 px-3 text-center text-transparent">-</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <p class="text-xs text-gray-500 italic mb-8">* Periksa kembali barang sebelum supir meninggalkan lokasi. Retur tidak diterima jika kerusakan diakibatkan kelalaian proyek.</p>

    <!-- Signatures -->
    <div class="flex justify-between text-sm text-center">
        <div class="w-1/3">
            <p class="mb-16">Penerima / Klien,</p>
            <p class="font-bold underline">{{ $sales_order->customer->nama ?? '(........................)' }}</p>
        </div>
        <div class="w-1/3">
            <p class="mb-16">Pengirim / Supir,</p>
            <p class="font-bold underline">(........................)</p>
        </div>
        <div class="w-1/3">
            <p class="mb-16">Hormat Kami,</p>
            <p class="font-bold underline">CV MA KARYA</p>
            <p class="text-xs text-gray-500 mt-1">Bagian Gudang</p>
        </div>
    </div>

</body>
</html>

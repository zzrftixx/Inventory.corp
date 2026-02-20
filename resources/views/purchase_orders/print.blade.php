<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->no_po }}</title>
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
<body class="p-8 max-w-4xl mx-auto border border-gray-200 min-h-screen my-4 shadow-sm print:border-none print:shadow-none print:my-0">

    <!-- Print Action -->
    <div class="mb-6 flex justify-end no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Purchase Order
        </button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-8 mt-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900">CV MA KARYA Artha Graha</h1>
            <p class="text-sm font-bold text-gray-500 tracking-widest mt-1">PURCHASE ORDER</p>
            <div class="mt-4 text-sm text-gray-600">
                <p>Jl. Contoh Alamat Perusahaan No. 123</p>
                <p>Telp: 0812-XXXX-XXXX</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-4xl font-black text-gray-200 uppercase tracking-tighter">P.O</h2>
            <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-left">
                <div class="text-gray-500 font-bold uppercase text-right">No. PO:</div>
                <div class="font-bold text-gray-900">{{ $purchaseOrder->no_po }}</div>
                
                <div class="text-gray-500 font-bold uppercase text-right">Tanggal:</div>
                <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Vendor Information -->
    <div class="mb-10 p-4 border border-gray-300 rounded bg-gray-50 w-1/2">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 border-b border-gray-300 pb-2">Kepada (Vendor/Supplier):</p>
        <p class="text-lg font-black text-gray-800">{{ $purchaseOrder->supplier->nama_supplier ?? 'Unknown Supplier' }}</p>
        <p class="text-sm mt-1 text-gray-600 whitespace-pre-line">Kontak / Tlp: {{ $purchaseOrder->supplier->kontak ?? '-' }}</p>
    </div>
    
    <p class="mb-4 text-sm font-medium">Mohon dikirimkan barang-barang di bawah ini sesuai dengan pesanan yang tertera:</p>

    <!-- Table Barang -->
    <table class="w-full mb-12 border border-black">
        <thead>
            <tr class="bg-gray-100 border-b border-black text-sm uppercase">
                <th class="py-3 px-3 text-center border-r border-black w-12 text-gray-600">No</th>
                <th class="py-3 px-3 text-left border-r border-black text-gray-600">Deskripsi / Nama Barang</th>
                <th class="py-3 px-3 text-center border-r border-black w-40 text-gray-600">Kode Barang</th>
                <th class="py-3 px-3 text-center w-32 text-gray-600">Qty Pemesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->details as $index => $detail)
            <tr class="border-b border-gray-300">
                <td class="py-3 px-3 text-center border-r border-black">{{ $index + 1 }}</td>
                <td class="py-3 px-3 text-left border-r border-black font-bold">
                    {{ $detail->item->nama_barang ?? 'Unknown' }}
                </td>
                <td class="py-3 px-3 text-center border-r border-black font-mono text-sm text-gray-600">
                    {{ $detail->item->kode_barang ?? '-' }}
                </td>
                <td class="py-3 px-3 text-center font-black text-lg">
                    {{ $detail->qty_butuh }} <span class="text-xs font-normal text-gray-600">{{ $detail->item->satuan ?? '' }}</span>
                </td>
            </tr>
            @endforeach
            <!-- Padding rows -->
            @for($i = count($purchaseOrder->details); $i < 4; $i++)
            <tr class="border-b border-gray-300">
                <td class="py-5 px-3 border-r border-black text-center text-transparent">-</td>
                <td class="py-5 px-3 border-r border-black text-transparent">-</td>
                <td class="py-5 px-3 border-r border-black text-transparent">-</td>
                <td class="py-5 px-3 text-center text-transparent">-</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- Info Tambahan -->
    <div class="mb-12">
        <h4 class="text-sm font-bold uppercase underline mb-2">Instruksi Pengiriman / Catatan Khusus:</h4>
        <ul class="text-sm list-disc pl-5 text-gray-700 space-y-1">
            <li>Mohon lampirkan Salinan Purchase Order ini bersama dengan Faktur/Surat Jalan saat pengiriman barang.</li>
            <li>Pastikan barang yang dikirim dalam kondisi baik tanpa cacat fisik.</li>
            <li>Segala retur akibat kerusakan pabrik akan diproses sesuai kesepakatan return warranty.</li>
        </ul>
    </div>

    <!-- Signatures -->
    <div class="flex justify-between items-end mt-16 text-center">
        <div class="w-1/3">
            <p class="text-sm font-bold text-gray-500 mb-16">Pihak Supplier / Vendor</p>
            <p class="font-bold underline text-gray-800">(..................................)</p>
            <p class="text-xs text-gray-500 mt-1">Nama Jelas, Tanda Tangan & Cap</p>
        </div>
        <div class="w-1/3">
            <p class="text-sm font-bold text-gray-500 mb-16">Hormat Kami, (Purchasing)</p>
            <p class="font-bold underline text-gray-800">CV MA KARYA Artha Graha</p>
            <p class="text-xs text-gray-500 mt-1">Authorized Signature</p>
        </div>
    </div>

</body>
</html>

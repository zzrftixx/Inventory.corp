<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Item;

class RealisticMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Categories
        $catAluminium = Category::create(['nama_kategori' => 'Ekstrusi Aluminium (Alexindo)']);
        $catKaca = Category::create(['nama_kategori' => 'Kaca Lembaran (Asahimas)']);
        $catHardware = Category::create(['nama_kategori' => 'Hardware & Aksesoris Pintu/Jendela']);
        $catConsumable = Category::create(['nama_kategori' => 'Consumables (Sealant, Karet, Skrup)']);
        $catAlat = Category::create(['nama_kategori' => 'Alat Pertukangan & Mesin']);

        // 2. Suppliers
        $supAlexindo = Supplier::create([
            'nama_supplier' => 'PT. Alexindo Utama (Pabrik Ekstrusi)',
            'kontak' => 'Bpk. Hendra - 021-88997766, alexindo@sales.co.id'
        ]);
        $supAsahimas = Supplier::create([
            'nama_supplier' => 'PT. Asahimas Flat Glass Tbk',
            'kontak' => 'Ibu Siska - 021-55443322, sales@asahimas.com'
        ]);
        $supDekkson = Supplier::create([
            'nama_supplier' => 'Dekkson Hardware Official',
            'kontak' => 'Sales Dept - 0811-999-888, b2b@dekkson.com'
        ]);
        $supWacker = Supplier::create([
            'nama_supplier' => 'Distributor Silicone Wacker',
            'kontak' => 'Koh Ahong - 0812-3344-5566'
        ]);
        $supYKK = Supplier::create([
            'nama_supplier' => 'PT. YKK AP Indonesia',
            'kontak' => 'Bpk. Yanto - 021-29384756'
        ]);

        // 3. Customers
        Customer::create(['nama' => 'PT. Wijaya Karya (WIKA) - Proyek Apartemen', 'alamat' => 'Jl. DI Panjaitan Kav. 9, Jakarta', 'no_telp' => '021-8192808']);
        Customer::create(['nama' => 'PT. PP (Persero) - Proyek Gedung Pemda', 'alamat' => 'Plaza PP, Pasar Rebo, Jakarta', 'no_telp' => '021-8403883']);
        Customer::create(['nama' => 'PT. Adhi Karya - Proyek Stasiun LRT', 'alamat' => 'Jl. Raya Pasar Minggu KM 18', 'no_telp' => '021-7986122']);
        Customer::create(['nama' => 'Bapak Budi Santoso - Rumah Pribadi PIK', 'alamat' => 'Pantai Indah Kapuk, Bukit Golf Mediterania', 'no_telp' => '0812-9988-7766']);
        Customer::create(['nama' => 'Ibu Linda (Renovasi Ruko)', 'alamat' => 'Ruko Gading Serpong Boulevard No. 8', 'no_telp' => '0855-4433-2211']);
        Customer::create(['nama' => 'Pelanggan Umum / Walk-in (Eceran)', 'alamat' => '-', 'no_telp' => '-']);

        // 4. Items (Massive Alexindo Catalog & Complementary)
        $items = [
            // ================= ALUMINIUM - Kusen 4 Inch & 3 Inch =================
            ['kode_barang' => 'ALX-K-4-SLV', 'nama_barang' => 'Kusen Open M 4 Inch Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 145000, 'harga_beli_rata_rata' => 125000, 'stok_saat_ini' => 120, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'ALX-K-4-BRW', 'nama_barang' => 'Kusen Open M 4 Inch Alexindo (Brown/PC)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 165000, 'harga_beli_rata_rata' => 140000, 'stok_saat_ini' => 85, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'ALX-K-4-WHT', 'nama_barang' => 'Kusen Open M 4 Inch Alexindo (White/PC)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 170000, 'harga_beli_rata_rata' => 145000, 'stok_saat_ini' => 40, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'ALX-K-4-BLK', 'nama_barang' => 'Kusen Open M 4 Inch Alexindo (Black/PC)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 170000, 'harga_beli_rata_rata' => 145000, 'stok_saat_ini' => 60, 'batas_stok_minimum' => 30],
            
            ['kode_barang' => 'ALX-K-3-SLV', 'nama_barang' => 'Kusen Open M 3 Inch Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 110000, 'harga_beli_rata_rata' => 85000, 'stok_saat_ini' => 150, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'ALX-K-3-BRW', 'nama_barang' => 'Kusen Open M 3 Inch Alexindo (Brown/PC)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 125000, 'harga_beli_rata_rata' => 95000, 'stok_saat_ini' => 70, 'batas_stok_minimum' => 40],

            // ================= ALUMINIUM - Daun Pintu & Jendela =================
            ['kode_barang' => 'ALX-TG-PINTU', 'nama_barang' => 'Tiang Pintu Polos Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 210000, 'harga_beli_rata_rata' => 180000, 'stok_saat_ini' => 60, 'batas_stok_minimum' => 30],
            ['kode_barang' => 'ALX-TG-MHR', 'nama_barang' => 'Tiang Pintu Mohair Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 225000, 'harga_beli_rata_rata' => 190000, 'stok_saat_ini' => 45, 'batas_stok_minimum' => 20],
            ['kode_barang' => 'ALX-AMB-BWH', 'nama_barang' => 'Ambang Bawah Pintu Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 185000, 'harga_beli_rata_rata' => 155000, 'stok_saat_ini' => 8, 'batas_stok_minimum' => 20], // Kritis
            ['kode_barang' => 'ALX-AMB-ATS', 'nama_barang' => 'Ambang Atas Pintu Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 165000, 'harga_beli_rata_rata' => 140000, 'stok_saat_ini' => 22, 'batas_stok_minimum' => 20],
            
            ['kode_barang' => 'ALX-CSM-SLV', 'nama_barang' => 'Rangka Jendela Casement Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 95000, 'harga_beli_rata_rata' => 75000, 'stok_saat_ini' => 80, 'batas_stok_minimum' => 40],
            ['kode_barang' => 'ALX-SLD-SLV', 'nama_barang' => 'Rangka Jendela Sliding Alexindo (Silver/CA)', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 85000, 'harga_beli_rata_rata' => 65000, 'stok_saat_ini' => 90, 'batas_stok_minimum' => 40],
            
            ['kode_barang' => 'ALX-SPIGOT', 'nama_barang' => 'Spigot / Liku Aluminium 1"', 'category_id' => $catAluminium->id, 'satuan' => 'Btg', 'harga_jual_default' => 55000, 'harga_beli_rata_rata' => 45000, 'stok_saat_ini' => 15, 'batas_stok_minimum' => 30], // Kritis

            // ================= KACA LEMBARAN (ASAHIMAS) =================
            ['kode_barang' => 'ASH-CLR-5MM', 'nama_barang' => 'Kaca Bening / Clear 5mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 220000, 'harga_beli_rata_rata' => 190000, 'stok_saat_ini' => 200, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'ASH-CLR-8MM', 'nama_barang' => 'Kaca Bening / Clear 8mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 380000, 'harga_beli_rata_rata' => 320000, 'stok_saat_ini' => 100, 'batas_stok_minimum' => 30],
            ['kode_barang' => 'ASH-CLR-10MM', 'nama_barang' => 'Kaca Bening / Clear 10mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 550000, 'harga_beli_rata_rata' => 460000, 'stok_saat_ini' => 40, 'batas_stok_minimum' => 20],
            
            ['kode_barang' => 'ASH-RYB-5MM', 'nama_barang' => 'Kaca Rayban / Riben Hitam 5mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 260000, 'harga_beli_rata_rata' => 225000, 'stok_saat_ini' => 150, 'batas_stok_minimum' => 40],
            ['kode_barang' => 'ASH-RYB-8MM', 'nama_barang' => 'Kaca Rayban / Riben Hitam 8mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 450000, 'harga_beli_rata_rata' => 380000, 'stok_saat_ini' => 12, 'batas_stok_minimum' => 25], // Kritis

            ['kode_barang' => 'ASH-PNS-5MM', 'nama_barang' => 'Kaca Panasap Dark Grey 5mm Asahimas', 'category_id' => $catKaca->id, 'satuan' => 'Lbr', 'harga_jual_default' => 320000, 'harga_beli_rata_rata' => 265000, 'stok_saat_ini' => 60, 'batas_stok_minimum' => 20],

            ['kode_barang' => 'ASH-TMP-8MM', 'nama_barang' => 'Kaca Tempered Clear 8mm', 'category_id' => $catKaca->id, 'satuan' => 'M2', 'harga_jual_default' => 650000, 'harga_beli_rata_rata' => 520000, 'stok_saat_ini' => 50, 'batas_stok_minimum' => 20],
            ['kode_barang' => 'ASH-TMP-10MM', 'nama_barang' => 'Kaca Tempered Clear 10mm', 'category_id' => $catKaca->id, 'satuan' => 'M2', 'harga_jual_default' => 850000, 'harga_beli_rata_rata' => 700000, 'stok_saat_ini' => 35, 'batas_stok_minimum' => 15],
            ['kode_barang' => 'ASH-TMP-12MM', 'nama_barang' => 'Kaca Tempered Clear 12mm (Partisi)', 'category_id' => $catKaca->id, 'satuan' => 'M2', 'harga_jual_default' => 1100000, 'harga_beli_rata_rata' => 900000, 'stok_saat_ini' => 15, 'batas_stok_minimum' => 10],

            // ================= HARDWARE (DEKKSONdll) =================
            ['kode_barang' => 'DKS-HDL-BMB', 'nama_barang' => 'Handle Tarikan Pintu Bambu Dekkson 30cm', 'category_id' => $catHardware->id, 'satuan' => 'Psg', 'harga_jual_default' => 350000, 'harga_beli_rata_rata' => 280000, 'stok_saat_ini' => 25, 'batas_stok_minimum' => 10],
            ['kode_barang' => 'DKS-HDL-KCT', 'nama_barang' => 'Handle Pintu Kaca Tempered Dekkson 60cm', 'category_id' => $catHardware->id, 'satuan' => 'Psg', 'harga_jual_default' => 850000, 'harga_beli_rata_rata' => 680000, 'stok_saat_ini' => 2, 'batas_stok_minimum' => 5], // Kritis
            
            ['kode_barang' => 'DKS-ENG-CSM-12', 'nama_barang' => 'Engsel Casement Jendela 12" Dekkson', 'category_id' => $catHardware->id, 'satuan' => 'Psg', 'harga_jual_default' => 85000, 'harga_beli_rata_rata' => 65000, 'stok_saat_ini' => 200, 'batas_stok_minimum' => 100],
            ['kode_barang' => 'DKS-ENG-CSM-16', 'nama_barang' => 'Engsel Casement Jendela 16" Dekkson', 'category_id' => $catHardware->id, 'satuan' => 'Psg', 'harga_jual_default' => 115000, 'harga_beli_rata_rata' => 85000, 'stok_saat_ini' => 110, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'DKS-ENG-PVT', 'nama_barang' => 'Engsel Pivot Pintu Kaca Dekkson', 'category_id' => $catHardware->id, 'satuan' => 'Set', 'harga_jual_default' => 140000, 'harga_beli_rata_rata' => 110000, 'stok_saat_ini' => 30, 'batas_stok_minimum' => 15],
            ['kode_barang' => 'DKS-FLR-84', 'nama_barang' => 'Floor Hinge Dekkson FH 84 (Tanam Beban Berat)', 'category_id' => $catHardware->id, 'satuan' => 'Unit', 'harga_jual_default' => 1250000, 'harga_beli_rata_rata' => 950000, 'stok_saat_ini' => 12, 'batas_stok_minimum' => 10],

            ['kode_barang' => 'DKS-LCK-8403', 'nama_barang' => 'Kunci Pintu Swing Dekkson 8403', 'category_id' => $catHardware->id, 'satuan' => 'Set', 'harga_jual_default' => 125000, 'harga_beli_rata_rata' => 95000, 'stok_saat_ini' => 5, 'batas_stok_minimum' => 15], // Kritis
            ['kode_barang' => 'DKS-LCK-KPG', 'nama_barang' => 'Kunci Kuping Jendela Dekkson (R/L)', 'category_id' => $catHardware->id, 'satuan' => 'Pcs', 'harga_jual_default' => 45000, 'harga_beli_rata_rata' => 30000, 'stok_saat_ini' => 180, 'batas_stok_minimum' => 50],

            // ================= CONSUMABLES (SEALANT, KARET, BAUT) =================
            ['kode_barang' => 'CON-SLN-WCK-CLR', 'nama_barang' => 'Sealant Netral Wacker Clear / Bening', 'category_id' => $catConsumable->id, 'satuan' => 'Tube', 'harga_jual_default' => 35000, 'harga_beli_rata_rata' => 28000, 'stok_saat_ini' => 300, 'batas_stok_minimum' => 100],
            ['kode_barang' => 'CON-SLN-WCK-BLK', 'nama_barang' => 'Sealant Netral Wacker Black / Hitam', 'category_id' => $catConsumable->id, 'satuan' => 'Tube', 'harga_jual_default' => 35000, 'harga_beli_rata_rata' => 28000, 'stok_saat_ini' => 250, 'batas_stok_minimum' => 100],
            ['kode_barang' => 'CON-SLN-WCK-WHT', 'nama_barang' => 'Sealant Netral Wacker White / Putih', 'category_id' => $catConsumable->id, 'satuan' => 'Tube', 'harga_jual_default' => 35000, 'harga_beli_rata_rata' => 28000, 'stok_saat_ini' => 120, 'batas_stok_minimum' => 50],
            
            ['kode_barang' => 'CON-KRT-078', 'nama_barang' => 'Karet Kaca / Gasket 078 (Hitam)', 'category_id' => $catConsumable->id, 'satuan' => 'Roll', 'harga_jual_default' => 65000, 'harga_beli_rata_rata' => 45000, 'stok_saat_ini' => 8, 'batas_stok_minimum' => 20], // Kritis
            ['kode_barang' => 'CON-KRT-MHR', 'nama_barang' => 'Karet Bulu / Mohair Jendela 6x6', 'category_id' => $catConsumable->id, 'satuan' => 'Roll', 'harga_jual_default' => 85000, 'harga_beli_rata_rata' => 55000, 'stok_saat_ini' => 15, 'batas_stok_minimum' => 10],

            ['kode_barang' => 'CON-MR-RIVET', 'nama_barang' => 'Paku Rivet Aluminium 4x11 (1000 Pcs)', 'category_id' => $catConsumable->id, 'satuan' => 'Box', 'harga_jual_default' => 45000, 'harga_beli_rata_rata' => 32000, 'stok_saat_ini' => 150, 'batas_stok_minimum' => 50],
            ['kode_barang' => 'CON-SCR-TJP', 'nama_barang' => 'Sekrup Tapping Kepala Rata 6x3/8', 'category_id' => $catConsumable->id, 'satuan' => 'Box', 'harga_jual_default' => 55000, 'harga_beli_rata_rata' => 40000, 'stok_saat_ini' => 80, 'batas_stok_minimum' => 30],
            ['kode_barang' => 'CON-DN-BOLT', 'nama_barang' => 'Dynabolt / Baut Tanam 10x77', 'category_id' => $catConsumable->id, 'satuan' => 'Box', 'harga_jual_default' => 75000, 'harga_beli_rata_rata' => 50000, 'stok_saat_ini' => 45, 'batas_stok_minimum' => 20],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

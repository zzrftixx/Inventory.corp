<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $catHardware = Category::create(['nama_kategori' => 'Hardware & Aksesoris']);
        $catConsumable = Category::create(['nama_kategori' => 'Consumables (Sealant, Karet)']);

        // 2. Suppliers
        $supAlexindo = Supplier::create([
            'nama_supplier' => 'PT. Alexindo Utama (Pabrik Ekstrusi)',
            'kontak' => 'Bpk. Hendra - 021-88997766'
        ]);
        $supAsahimas = Supplier::create([
            'nama_supplier' => 'PT. Asahimas Flat Glass Tbk',
            'kontak' => 'Ibu Siska - 021-55443322'
        ]);
        $supDekkson = Supplier::create([
            'nama_supplier' => 'Dekkson Hardware Official',
            'kontak' => 'Sales Dept - 0811-999-888'
        ]);
        $supWacker = Supplier::create([
            'nama_supplier' => 'Distributor Silicone Wacker',
            'kontak' => 'Koh Ahong - 0812-3344-5566'
        ]);

        // 3. Customers
        Customer::create([
            'nama' => 'PT. Wijaya Karya (WIKA) - Proyek Apartemen',
            'alamat' => 'Jl. DI Panjaitan Kav. 9, Jakarta Timur',
            'no_telp' => '021-8192808'
        ]);
        Customer::create([
            'nama' => 'PT. PP (Persero) - Proyek Gedung Pemda',
            'alamat' => 'Plaza PP, Pasar Rebo, Jakarta',
            'no_telp' => '021-8403883'
        ]);
        Customer::create([
            'nama' => 'Bapak Budi Santoso - Rumah Pribadi PIK',
            'alamat' => 'Pantai Indah Kapuk, Bukit Golf Mediterania',
            'no_telp' => '0812-9988-7766'
        ]);
        Customer::create([
            'nama' => 'Pelanggan Umum / Walk-in',
            'alamat' => '-',
            'no_telp' => '-'
        ]);

        // 4. Items (Alexindo Catalog & Complementary)
        $items = [
            // Aluminium
            [
                'kode_barang' => 'ALX-OPEN-M-4-SLV',
                'nama_barang' => 'Kusen Open M 4 Inch Alexindo (Silver/CA)',
                'category_id' => $catAluminium->id,
                'satuan' => 'Btg (6m)',
                'harga_jual_default' => 145000,
                'harga_beli_rata_rata' => 125000,
                'stok_saat_ini' => 120,
                'batas_stok_minimum' => 50,
            ],
            [
                'kode_barang' => 'ALX-OPEN-M-4-BRW',
                'nama_barang' => 'Kusen Open M 4 Inch Alexindo (Brown/PC)',
                'category_id' => $catAluminium->id,
                'satuan' => 'Btg (6m)',
                'harga_jual_default' => 165000,
                'harga_beli_rata_rata' => 140000,
                'stok_saat_ini' => 85,
                'batas_stok_minimum' => 50,
            ],
            [
                'kode_barang' => 'ALX-TG-PINTU',
                'nama_barang' => 'Tiang Pintu Polos Alexindo (Silver/CA)',
                'category_id' => $catAluminium->id,
                'satuan' => 'Btg (6m)',
                'harga_jual_default' => 210000,
                'harga_beli_rata_rata' => 180000,
                'stok_saat_ini' => 60,
                'batas_stok_minimum' => 30,
            ],
            [
                'kode_barang' => 'ALX-AMB-BWH',
                'nama_barang' => 'Ambang Bawah Pintu Alexindo (Silver/CA)',
                'category_id' => $catAluminium->id,
                'satuan' => 'Btg (6m)',
                'harga_jual_default' => 185000,
                'harga_beli_rata_rata' => 155000,
                'stok_saat_ini' => 45,
                'batas_stok_minimum' => 20,
            ],
            [
                'kode_barang' => 'ALX-SPIGOT',
                'nama_barang' => 'Spigot / Liku Aluminium',
                'category_id' => $catAluminium->id,
                'satuan' => 'Btg (6m)',
                'harga_jual_default' => 55000,
                'harga_beli_rata_rata' => 45000,
                'stok_saat_ini' => 15, // Kritis
                'batas_stok_minimum' => 30,
            ],
            
            // Kaca
            [
                'kode_barang' => 'ASH-CLR-5MM',
                'nama_barang' => 'Kaca Bening / Clear 5mm Asahimas',
                'category_id' => $catKaca->id,
                'satuan' => 'Lbr', // 122 x 244
                'harga_jual_default' => 220000,
                'harga_beli_rata_rata' => 190000,
                'stok_saat_ini' => 200,
                'batas_stok_minimum' => 50,
            ],
            [
                'kode_barang' => 'ASH-RYB-5MM',
                'nama_barang' => 'Kaca Rayban / Riben 5mm Asahimas',
                'category_id' => $catKaca->id,
                'satuan' => 'Lbr',
                'harga_jual_default' => 260000,
                'harga_beli_rata_rata' => 225000,
                'stok_saat_ini' => 150,
                'batas_stok_minimum' => 40,
            ],
            [
                'kode_barang' => 'ASH-TMP-8MM',
                'nama_barang' => 'Kaca Tempered Clear 8mm (Custom)',
                'category_id' => $catKaca->id,
                'satuan' => 'M2',
                'harga_jual_default' => 650000,
                'harga_beli_rata_rata' => 520000,
                'stok_saat_ini' => 50,
                'batas_stok_minimum' => 20,
            ],

            // Hardware
            [
                'kode_barang' => 'DKS-HDL-BMB',
                'nama_barang' => 'Handle Tarikan Pintu Bambu Dekkson',
                'category_id' => $catHardware->id,
                'satuan' => 'Psg',
                'harga_jual_default' => 350000,
                'harga_beli_rata_rata' => 280000,
                'stok_saat_ini' => 25,
                'batas_stok_minimum' => 10,
            ],
            [
                'kode_barang' => 'DKS-ENG-CSM-12',
                'nama_barang' => 'Engsel Casement Jendela 12" Dekkson',
                'category_id' => $catHardware->id,
                'satuan' => 'Psg',
                'harga_jual_default' => 85000,
                'harga_beli_rata_rata' => 65000,
                'stok_saat_ini' => 200,
                'batas_stok_minimum' => 100,
            ],
            [
                'kode_barang' => 'DKS-LCK-8403',
                'nama_barang' => 'Kunci Pintu Swing Dekkson 8403',
                'category_id' => $catHardware->id,
                'satuan' => 'Set',
                'harga_jual_default' => 125000,
                'harga_beli_rata_rata' => 95000,
                'stok_saat_ini' => 5, // Kritis
                'batas_stok_minimum' => 15,
            ],

            // Consumables
            [
                'kode_barang' => 'CON-SLN-WCK-CLR',
                'nama_barang' => 'Sealant Netral Wacker Clear / Bening',
                'category_id' => $catConsumable->id,
                'satuan' => 'Tube',
                'harga_jual_default' => 35000,
                'harga_beli_rata_rata' => 28000,
                'stok_saat_ini' => 300,
                'batas_stok_minimum' => 100,
            ],
            [
                'kode_barang' => 'CON-SLN-WCK-BLK',
                'nama_barang' => 'Sealant Netral Wacker Black / Hitam',
                'category_id' => $catConsumable->id,
                'satuan' => 'Tube',
                'harga_jual_default' => 35000,
                'harga_beli_rata_rata' => 28000,
                'stok_saat_ini' => 250,
                'batas_stok_minimum' => 100,
            ],
            [
                'kode_barang' => 'CON-KRT-078',
                'nama_barang' => 'Karet Kaca / Gasket 078 (Hitam)',
                'category_id' => $catConsumable->id,
                'satuan' => 'Roll (50m)',
                'harga_jual_default' => 65000,
                'harga_beli_rata_rata' => 45000,
                'stok_saat_ini' => 8, // Kritis
                'batas_stok_minimum' => 20,
            ],
            [
                'kode_barang' => 'CON-MR-RIVET',
                'nama_barang' => 'Paku Rivet Aluminium 4x11',
                'category_id' => $catConsumable->id,
                'satuan' => 'Box',
                'harga_jual_default' => 45000,
                'harga_beli_rata_rata' => 32000,
                'stok_saat_ini' => 150,
                'batas_stok_minimum' => 50,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

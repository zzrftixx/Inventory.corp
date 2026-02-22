# Inventory.corp (Edisi Sample Real Database)

> **Branch `sample-real-database`** - Branch Mandiri dengan Dummy Data Terisi Penuh Khas Kontraktor Aluminium & Kaca.

---

## 🏗️ Fungsi & Tujuan Branch Ini

Branch ini adalah *copy* langsung dari versi V3 (Final Production), namun **sengaja disiapkan dengan kumpulan data contoh (Dummy Data) yang 100% Realistis** layaknya perusahaan CV Ma Karya sungguhan. 

Fungsi utama dari branch `sample-real-database` ini antara lain:
1. **Pemaparan Presentasi Klien/Vendor:** Memudahkan *showcase* kelayakan aplikasi saat mendemokan alur transaksi secara profesional tanpa harus input item fiktif yang memalukan.
2. **Onboarding Pegawai Baru:** Lingkungan yang aman (*Sandbox*) untuk melatih dan membiasakan calon Kasir / Admin Gudang dalam membuat PO dan Sales Order tanpa takut merusak *Data Production* asli CV Ma Karya.
3. **Uji Coba Limit Testing:** Menguji skenario laporan finansial dan profit margin dengan data *Cost of Goods Sold* (COGS) komponen material aluminium yang terhitung realistis layaknya aslinya.

Data contoh yang ditanamkan mengambil studi kasus **Pabrik Ekstrusi Aluminium Alexindo** dan **Kaca Asahimas**.

---

## 📦 Apa Saja Isi Data Otomatisnya?

Ketika Anda me-*refresh* database di cabang ini, ribuan data operasional akan langsung terisi:

### 1. Kategori & Master Barang (Katalog Alexindo Asli)
Barang-barang sudah dikelaskan berdasarkan *Category*:
*   **Ekstrusi Aluminium**: *Kusen Open M 4", Tiang Pintu Polos, Ambang Bawah, Spigot Liku.*
*   **Kaca Lembaran**: *Kaca Bening 5mm Asahimas, Kaca Rayban, Kaca Tempered 8mm.*
*   **Hardware / Aksesoris**: *Handle Pintu Bambu Dekkson, Kunci Swing, Engsel Casement.*
*   **Consumables**: *Sealant Wacker Bening/Hitam, Paku Rivet, Karet Kaca/Gasket.*
*(Catatan: Harga Modal HPP dan Harga Jual sudah diset realistis, lengkap dengan batas stok kritis masing-masing item)*.

### 2. Supplier (Pabrik & Distributor Besar)
*   PT. Alexindo Utama (Pabrik Ekstrusi)
*   PT. Asahimas Flat Glass Tbk
*   Dekkson Hardware Official
*   Distributor Silicone Wacker

### 3. Customer (Proyek & Walk-in)
*   BUMN: *PT. Wijaya Karya (Wika) - Apartemen*, *PT. PP (Persero) - Gedung Pemda*
*   Klien Pribadi: *Bapak Budi Santoso - PIK*
*   Pelanggan Umum / Eceran

### 4. User System (Role-Based Access Control)
*   **Super Admin**: `superadmin@inventory.com` (pass: `password`)
*   **Admin**: `admin@inventory.com` (pass: `password`)
*   **Kasir**: `kasir@inventory.com` (pass: `password`)
*   **Gudang**: `gudang@inventory.com` (pass: `password`)

---

## 🚀 Cara Menekan Tombol "Sihir" (Install Data Ini)

Penting: Branch ini tidak akan merusak branch utama Anda! Untuk mencicipi data di sistem Anda yang terkoneksi dengan cabang git ini, jalankan perintah sikat bersih berikut (⚠️ *Akan menghapus semua data transaksi lama*):

```bash
php artisan migrate:fresh --seed
```

Sistem akan menginstal ulang tabel dengan bersih dan menginjeksi *RealisticMasterDataSeeder* dan *RolesAndPermissionsSeeder* dalam hitungan detik. 

Setelah selesai, login menggunakan akun di atas dan nikmati sistem yang sudah *"hidup"* secara ajaib! 🔥

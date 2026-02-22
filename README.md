# Inventory.corp (Edisi Sample Real Database)

> **Branch `sample-real-database`** - Branch Mandiri dengan Dummy Data Terisi Penuh Khas Kontraktor Aluminium & Kaca.

---

## 🏗️ Tujuan Branch Ini

Branch ini adalah *copy* langsung dari versi V3 (Final Production), namun **sengaja disiapkan dengan kumpulan data contoh (Dummy Data) yang 100% Realistis** layaknya perusahaan CV Ma Karya sungguhan. 

Ini sangat cocok digunakan jika Anda ingin mendemokan aplikasi ke orang lain, mempresentasikan cara kerja aplikasi, atau sekadar melakukan eksperimen *testing* fungsi kasir tanpa perlu capek meng-input data master dari awal.

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

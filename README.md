# Inventory.corp (V3: UI & Document Polishing)

> **Branch `productions/v3-ui-docs`** - Refined User Interface and Authentic Print Documents.

---

## ✨ Apa yang Baru di Versi 3 (v3-ui-docs)?

Cabang (`branch`) ini merupakan penyempurnaan estetika (UI) dan keakuratan dokumen cetak (Surat Jalan & Faktur) dari versi sebelumnya (`v2-auth`).

Fokus utama V3 ini adalah memberikan sentuhan identitas perusahaan yang kuat dan memastikan dokumen siap tayang ke pelanggan maupun supplier tanpa perlu revisi manual lagi.

### 1. 🖼️ Dynamic Favicon & Browser Identity
Menggantikan *favicon* standar bawaan Laravel/Server menjadi logo asli **CV Ma Karya Artha Graha** (`logomakarya.png`). 
Perubahan ini terlihat di:
- Tab Browser (saat membuka aplikasi)
- Header Sidebar (pojok kiri atas di dalam Dashboard)
- Halaman Login / Registrasi

### 2. 🧾 Authentic Print Documents (Komersial Ready)
Pada versi sebelumnya, dokumen Cetak/Print (Surat Jalan, Faktur, PO) masih menggunakan alamat dan nomor telepon "Contoh Alamat Perusahaan No.123".

Di V3 ini, dokumen pengiriman dan penagihan telah diisi dengan informasi *real* yang siap diberikan ke Pelanggan maupun diserahkan ke Vendor:
- **Nama Usaha**: CV MA KARYA ARTHA GRAHA
- **Alamat Asli**: Jl. Karang Tengah Sitimulyo, Karang Anom, Sitimulyo, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55792
- **Telp/Hotline**: 0851 0158 8887
- **Email**: cv.makarya.ag@gmail.com

Dokumen yang terdampak:
1. `Surat Jalan` (Sales Order - Outbound)
2. `Faktur Penjualan` (Sales Order - Outbound)
3. `Purchase Order` (Kulakan - Inbound)

### 3. 🔔 Real-Time Notification Bell
Menyempurnakan kapabilitas Sistem Limit Stok (dari V1).
- Terdapat Ikon Lonceng di pojok kanan atas Navbar yang akan **berkedip merah** (*Ping Alert*) otomatis jika ada item gudang yang menyentuh/berada di bawah *batas minimum*.
- Diklik memunculkan Dropdown list jumlah barang kritis.
- **Terproteksi RBAC**: Notifikasi peringatan stok ini *hanya muncul* untuk hak akses Admin Gudang dan (Super) Admin. Kasir tidak akan melihatnya.

---

## 🔑 Default Credentials untuk Testing (Local)
Agar mempermudah simulasi role saat review cabang ini, jalankan seeding (`php artisan db:seed --class=RolesAndPermissionsSeeder`). Credentials default yang ditanam:

*   **Super Admin**: `superadmin@inventory.com` (pass: `password`)
*   **Admin**: `admin@inventory.com` (pass: `password`)
*   **Kasir**: `kasir@inventory.com` (pass: `password`)
*   **Gudang**: `gudang@inventory.com` (pass: `password`)

---

## Instalasi (Clone khusus branch V3)

1. Clone repository khusus branch `productions/v3-ui-docs`:
```bash
git clone -b productions/v3-ui-docs https://github.com/zzrftixx/Inventory.corp.git
```
2. Jalankan dependensi:
```bash
composer install
npm install && npm run build
```
3. Copy environment & Generate key:
```bash
cp .env.example .env
php artisan key:generate
```
4. Setup Database MySQL di `.env`, lalu migrate beserta seed untuk Auth user bawaan:
```bash
php artisan migrate --seed --class=RolesAndPermissionsSeeder
```

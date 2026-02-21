# Inventory.corp (V2: Auth & Financial Engine)

> **Branch `productions/v2-auth`** - Enterprise Grade Inventory System with Role-Based Access Control and Moving Average COGS.

---

## 🚀 Apa yang Baru di Versi 2 (v2-auth)?

Cabang (`branch`) ini merupakan evolusi besar dari versi `main` (V1). Fokus utama pada `v2-auth` adalah **Keamanan Data**, **Akurasi Laba (HPP)**, dan **Alur Kerja Terproteksi (Approval/Drafts)**. 

Beda utama dari V1 (yang dirancang lebih bebas tanpa login), versi V2 ini sangat *strict* dan level enterprise.

### 1. 🛡️ Authentication & Role-Based Access Control (RBAC)
Sistem sekarang diisolasi menggunakan **Laravel Breeze** untuk otentikasi dan **Spatie Laravel Permission** untuk hak akses sekat antar user.
Setiap karyawan wajib memiliki akun untuk mengakses sistem, dan Menu Navigasi akan beradaptasi secara dinamis sesuai jabatan mereka.

**Tersedia 4 Role Default (Hak Akses):**
*   **Super Admin:** Akses penuh ke seluruh sistem tanpa batas (Master, Transaksi In/Out, Logs, User Management).
*   **Admin:** Memiliki kewenangan mengelola semua Master Data, Sales (Out), maupun Restock (In) beserta Logs.
*   **Kasir:** Hanya difokuskan pada *Front-liner*. Bisa melihat Master Data Customer dan Barang, tapi **hanya bisa membuat Sales Order**. Tidak ada akses ke Purchase Order (Kulakan).
*   **Gudang:** Hanya difokuskan pada manajemen stok fisik. **Hanya bisa mengelola Purchase Order (In)** dan tidak ada akses ke menu Penjualan (Out).

### 2. 📝 Administrative Prison (Sales Order - Drafts)
Di V1, setiap kali nota Sales Order dibuat, stok langsung terpotong. Ini berbahaya untuk *human-error*.
Di V2, alur diperbaiki dengan skema **DRAFT**:
*   Admin/Kasir dapat membuat nota dan menyimpannya sebagai **Draft**.
*   Form Draft dapat direvisi, diedit, atau dihapus berulang kali *tanpa mempengaruhi stok gudang sama sekali*.
*   Stok baru akan dipotong secara *rigid* dan permanen ketika nota di-Lock (tombol **Submit & Kunci Transaksi** ditekan), yang mengubah status SO menjadi `Selesai`.

### 3. 💵 Core Financial Engine (Moving Average COGS)
Di V1, perhitungan modal/HPP (Harga Pokok Penjualan) rawan "halu" atau tidak sinkron akibat fluktuasi harga kulakan dari supplier.
V2 menghapus "Financial Illusion" tersebut dengan logika berikut:
*   **Purchase Orders (Inbound):** Kasir/Admin wajib memasukkan **Harga Beli Satuan** per item. Begitu fisik barang datang (Status PO menjadi *Received*), sistem secara otomatis menjalankan rumus **Moving Average** matematis untuk memperbaharui Modal/HPP rata-rata barang tersebut di database induk.
*   **Sales Orders (Outbound):** Saat nota penjualan dikunci, sistem otomatis me-**Snapshot** `harga_beli_rata_rata` (Modal berjalan saat itu) dan menyimpannya ke dalam record nota. Meskipun 2 hari lagi harga modal kulakan naik, hitungan profit nota hari ini tidak akan ikut bergeser terdistorsi.

---

## 💻 Tech Stack Tambahan (V2)
Selain core framework Laravel 11, Tailwind CSS, dan Phosphor icon di V1, V2 menggunakan:
*   `laravel/breeze` (Auth scaffolding)
*   `spatie/laravel-permission` (RBAC)

---

## 🔑 Default Credentials untuk Testing (Local)
Agar mempermudah simulasi role saat review cabang ini, jalankan seeding (`php artisan db:seed --class=RolesAndPermissionsSeeder`). Credentials default yang ditanam:

*   **Super Admin**: `superadmin@inventory.com` (pass: `password`)
*   **Admin**: `admin@inventory.com` (pass: `password`)
*   **Kasir**: `kasir@inventory.com` (pass: `password`)
*   **Gudang**: `gudang@inventory.com` (pass: `password`)

---

## Instalasi (Clone khusus branch ini)

1. Clone repository khusus branch `productions/v2-auth`:
```bash
git clone -b productions/v2-auth https://github.com/zzrftixx/Inventory.corp.git
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

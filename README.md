# Laravel Inventory & Sales System - CV Ma Karya Artha Graha

Sistem informasi berbasis web yang dibangun dengan Laravel untuk memenuhi kebutuhan administrasi, pelacakan stok, dan transaksi (Inbound & Outbound) pada perusahaan kontraktor **CV Ma Karya Artha Graha**. Sistem ini juga mengakomodasi pelanggan/klien bisnis seperti **Alupbesk**.

## 🌟 Fitur Utama

1. **Master Data Management**: Modul CRUD lengkap untuk mengelola Barang & Limit Stok, Kategori, Pelanggan/Klien, dan Supplier.
2. **Dashboard Peringatan Dini**: Notifikasi (Red-Zone) untuk barang yang stoknya berada di bawah batas minimum, memicu pembuatan *Purchase Order (PO)* secara otomatis.
3. **Smart Transaction Panel (Penjualan)**:
   - Pencetakan **Surat Jalan** (kertas informasi barang & kuantitas untuk supir/pengiriman).
   - Pencetakan **Faktur Penjualan / Nota Manufaktur** (kertas rincian harga, diskon, dan subtotal pembayaran).
4. **Stock Ledger & Tracing (Audit Trail)**: Riwayat pergerakan stok rinci (IN/OUT) untuk melacak setiap rotasi barang secara akurat (PO, SO, Retur, Penyesuaian Barang Rusak/Hilang).
5. **Autopilot Restock (Inbound)**: Modul untuk mengonfirmasi penerimaan barang dari *Supplier* berdasarkan PO yang dicetak otomatis oleh sistem.
6. **Retur Barang**: Fasilitas untuk memproses retur (pengembalian barang) yang terintegrasi dengan penambahan/pengurangan stok otomatis.

---

## 📐 Arsitektur & Alur Bisnis (Flowcharts)

Sistem dibagi menjadi 2 proses utama: **Outbound (Penjualan)** dan **Inbound (Pembelian/Restock)**.

### A. Modul Outbound (Penjualan / Surat Jalan & Faktur)
Proses penanganan pesanan, pengecekan ketersediaan stok, pengeluaran dari gudang, dan penagihan.

```mermaid
graph TD
    A[Terima Pesanan dari Customer <br/> cth: Alupbesk] --> B[Admin Input Sales Order / SO]
    B --> C{Sistem Cek Stok Gudang}
    C -- Stok Cukup --> D[Cetak Surat Jalan <br/> Hanya menampilkan Item & Qty, Tanpa Harga]
    C -- Stok Tidak Cukup --> E[Tahan Pesanan & Trigger Proses Inbound]
    D --> F[Berikan Surat Jalan ke Supir/Gudang <br/> Pengiriman Barang ke Customer]
    F --> G[Cetak Faktur Penjualan <br/> Tampil Harga, Diskon, Subtotal]
    G --> H[Stok di Sistem Berkurang Otomatis]
    H --> DB[(Database Stok Utama)]
```

### B. Modul Inbound (Pembelian / Autopilot Restock)
Proses pengawasan *Minimum Stock Level* agar gudang tidak kehabisan material.

```mermaid
graph TD
    DB[(Database Stok Utama)] --> I[Sistem Cek Stok Terus Menerus]
    I --> J{Apakah Stok Gudang < Limit Minimum?}
    J -- Ya --> K[Sistem Menerbitkan Draf PO otomatis <br/> Daftar Kebutuhan Barang & Qty]
    J -- Tidak --> L[Aman / Standby]
    K --> M[Admin Konfirmasi Draf & Kirim Order ke Pabrik/Supplier]
    M --> N[Barang Fisik Tiba & Admin Input Penerimaan Barang]
    N --> O[Stok di Sistem Bertambah Otomatis]
    O --> DB
```

---

## 🗄️ Struktur Database (ERD)

Database menggunakan rancangan enterprise (Bulletproof Design) yang mencegah redundansi, memudahkan pelacakan kerugian/keuntungan, dan menyediakan jejak audit (audit trails) via `STOCK_MOVEMENTS`.

```mermaid
erDiagram
    USERS {
        int id PK
        string nama
        string role "Admin / Gudang / Kasir"
    }

    CATEGORIES {
        int id PK
        string nama_kategori "Cth: Aksesoris, Kaca, Aluminium"
    }

    SUPPLIERS {
        int id PK
        string nama_supplier "Pabrik / Distributor"
        string kontak
    }

    CUSTOMERS {
        int id PK
        string nama "Cth: Alupbesk, Umum"
        string alamat
        string no_telp
    }
    
    ITEMS {
        int id PK
        string kode_barang "Kode Pabrik"
        string nama_barang "Cth: HANDLE STAINLESS"
        int category_id FK
        string satuan "Cth: PSG, SET, PCS"
        decimal harga_jual_default
        int stok_saat_ini "Auto Update via Trigger"
        int batas_stok_minimum
    }

    SALES_ORDERS {
        int id PK
        string no_faktur "INV-2026-001"
        date tanggal_transaksi
        int customer_id FK
        int user_id FK "Pencatat SO"
        decimal total_invoice
        string status "Draft / Dikirim / Selesai / Retur"
    }

    SALES_ORDER_DETAILS {
        int id PK
        int sales_order_id FK
        int item_id FK
        int qty
        decimal harga_satuan_saat_transaksi
        decimal diskon
        decimal subtotal_netto
    }
    
    PURCHASE_ORDERS {
        int id PK
        string no_po
        date tanggal_po
        int supplier_id FK
        int user_id FK "Pembuat PO"
        string status "Draft / Ordered / Received"
    }
    
    PURCHASE_ORDER_DETAILS {
        int id PK
        int purchase_order_id FK
        int item_id FK
        int qty_butuh
    }

    STOCK_MOVEMENTS {
        int id PK
        int item_id FK
        string tipe_pergerakan "IN (PO/Retur) / OUT (SO/Rusak)"
        int qty
        int sisa_stok "Snapshot stok saat itu"
        string referensi "No PO / No SO / Catatan"
        int user_id FK "Yang Approve"
        datetime timestamp
    }

    USERS ||--o{ SALES_ORDERS : "mencatat"
    USERS ||--o{ PURCHASE_ORDERS : "membuat"
    USERS ||--o{ STOCK_MOVEMENTS : "menyetujui"
    
    CATEGORIES ||--o{ ITEMS : "mengkelompokkan"
    SUPPLIERS ||--o{ PURCHASE_ORDERS : "menerima"
    CUSTOMERS ||--o{ SALES_ORDERS : "melakukan"
    
    SALES_ORDERS ||--|{ SALES_ORDER_DETAILS : "memiliki"
    ITEMS ||--o{ SALES_ORDER_DETAILS : "tercatat di SO"
    
    PURCHASE_ORDERS ||--|{ PURCHASE_ORDER_DETAILS : "memiliki PO"
    ITEMS ||--o{ PURCHASE_ORDER_DETAILS : "kebutuhan"
    
    ITEMS ||--o{ STOCK_MOVEMENTS : "memiliki history"
```

---

## 🚀 Panduan Instalasi & Penggunaan

### Kebutuhan Sistem
* PHP >= 8.2
* Composer
* MySQL Server (Misal: XAMPP, Laragon, MAMP)

### Langkah Instalasi

1. **Clone Repository (atau download ZIP):**
   ```bash
   git clone https://github.com/zzrftixx/Inventory.corp.git
   cd Inventory.corp
   ```

2. **Install Dependensi Laravel:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```
   Atur kredensial Database Anda di file `.env` yang baru saja dibuat:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=masterinventorys
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Buat Database SQL:**
   Buka *phpMyAdmin* (atau aplikasi GUI database yang Anda gunakan) dan buat Schema/Database baru bernama `masterinventorys`.

6. **Jalankan Migrations Database:**
   Perintah ini akan secara otomatis membuat struktur tabel ke database `masterinventorys`.
   ```bash
   php artisan migrate:fresh
   ```

7. **Jalankan Development Server:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di Browser: `http://127.0.0.1:8000`

### Penggunaan (Cara Mulai Mencoba)
Setelah instalasi selesai, ikuti urutan ini untuk menggunakan sistem:
1. Akses menu **Kategori Barang** (`/categories`). Daftarkan kategori, misal: *Kaca*, *Aksesoris*, *Aluminium*.
2. Akses menu **Supplier** (`/suppliers`). Daftarkan pabrik tempat perusahan memesan material.
3. Akses menu **Customer / Klien** (`/customers`). Daftarkan *Alupbesk* atau pelanggan lainnya.
4. Akses menu **Barang & Stok** (`/items`). Daftarkan master data profil barang, harganya, dan tentukan limit minimum *restock*-nya.
5. Selanjutnya, gunakan modul Transaksi (Inbound/Outbound) yang saling teringrasi di panel.

---

> Dibuat khusus untuk **CV Ma Karya Artha Graha** dengan desain UI/UX canggih (_Tailwind CSS_) dan perancangan database (_bulletproof architecture_) by Engineer Support.

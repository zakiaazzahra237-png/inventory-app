# Inventory Management System

Aplikasi web berbasis PHP Native dan MySQL untuk mengelola stok barang, transaksi masuk/keluar, dan pemantauan statistik inventaris secara *real-time*.

---

## 🚀 Fitur Utama

- **Autentikasi & Otorisasi:** Login multi-role (Admin & Staff) berbasis Session.
- **Dashboard Agregat:** Ringkasan total produk, total nilai inventaris, indikator stok rendah, dan statistik barang masuk/keluar.
- **Manajemen Produk:** Daftar produk lengkap dengan pencarian nama/SKU, filter kategori, pengurutan data (*sorting*), serta *pagination*.
- **Transaksi Stok (Movement):** Pencatatan barang masuk (IN) dan keluar (OUT) dilengkapi *Database Transaction* (Commit/Rollback) dan validasi ketersediaan stok.
- **Pencegahan XSS:** Penggunaan fungsi pembungkus `htmlspecialchars()` untuk sanitasi output.

---

## 🛠️ Teknologi yang Digunakan

- **Language:** PHP 8.x
- **Database:** MySQL / MariaDB (via XAMPP)
- **Database Access:** PHP Data Objects (PDO)
- **Frontend Framework:** Bootstrap 5 (CDN)
- **Development Tool:** Visual Studio Code

---

## 📁 Struktur Direktori Proyek

```text
inventory-app/
├── schema.sql              # Skema basis data & data awal
├── config/
│   └── database.php        # Konfigurasi koneksi PDO MySQL
├── helpers/
│   ├── auth.php            # Helper sesi & verifikasi hak akses
│   ├── escape.php          # Helper sanitasi XSS
│   └── validation.php      # Helper validasi input angka
├── views/
│   ├── header.php          # Template header & navigasi
│   └── footer.php          # Template footer
└── public/
    ├── dashboard.php       # Halaman utama ringkasan data
    ├── login.php           # Halaman otentikasi pengguna
    ├── logout.php          # Halaman keluar sesi
    ├── products/
    │   └── index.php       # Halaman daftar & filter produk
    └── stock/
        └── movement.php    # Halaman transaksi stok barang
# Car Care Wash

Aplikasi **Car Care Wash** dari BuildWithAngga menggunakan **Laravel** dan **Spatie** untuk fitur CRUD yang mudah.

---

## 📷 Screenshot

### Home Overview
![Home](/public/assets/images/photos/overview.png)

--- 

## 🚀 Fitur Utama

1. **Admin**:
    - Dashboard Admin untuk mengelola sistem.
    - Manajemen Booking Transactions.
    - Manajemen Car Services dan Car Stores.
    - Pengaturan daftar **Cities**.

2. **Customer**:
    - Memilih layanan cuci mobil, pengecatan, dll.
    - Melakukan pemesanan dan pembayaran layanan.
    - Melihat riwayat transaksi.

---

## 🔄 Workflow Aplikasi

1. **Admin**:
    - Membuat dan mengelola **Car Service** serta **Car Stores**.
    - Mengelola **transaksi** dari pelanggan.

2. **Customer**:
    - Memilih **layanan** yang tersedia.
    - Melakukan pembayaran dan mengunggah **bukti transfer**.

---

## 🛠 Teknologi yang Digunakan

- **Laravel**: Framework utama untuk membangun aplikasi.
- **Filament**: untuk membuat fitur CRUD menjadi mudah.
- **MySQL**: Basis data untuk menyimpan data keuangan.
- **TailwindCSS**: (opsional) Untuk antarmuka pengguna (UI).

---

## Cara Instalasi

1. **Clone repository**:
   ```bash
   git clone https://github.com/rizkycahyono97/car-care-wash.git
   cd car-care-wash
2. **Run Manualy**:
   ```bash
   cd car-care-wash
   composer install
   cp .env.example .env
   php artisan key:generate
   npm install
   php artisan migrate
   php artisan serve
3. **Optimize Laravel**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

## Akun Default 
1. **admin**
    - **username** = *car@care.com*
    - **password** = *test1234*
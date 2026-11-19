# PANDUAN LENGKAP INSTALASI & MENJALANKAN APLIKASI
**Sistem Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21**

---

## 1. Prasyarat Sistem (*System Requirements*)

Pastikan komputer/laptop Anda telah terpasang:
* **PHP:** Versi 8.1 atau lebih tinggi (di komputer Anda sudah tersedia di Laragon: `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`).
* **Database Server:** MySQL 5.7+ atau MariaDB 10.4+ (Tersedia via Laragon/XAMPP).
* **Composer:** Dependency Manager PHP (Tersedia di `C:\laragon\bin\composer`).
* **Web Browser:** Google Chrome / Microsoft Edge.

> Proyek yang tersedia saat ini memakai Laravel 10 dan berjalan pada PHP 8.1. Laravel 13 memerlukan PHP 8.3 atau lebih baru. Upgrade framework perlu dilakukan setelah runtime Laragon diperbarui; menaikkan dependency saja akan membuat aplikasi saat ini tidak dapat dijalankan.

---

## 2. Langkah Demi Langkah Setup & Instalasi

### Langkah 1: Aktifkan Server Database (MySQL)
1. Buka aplikasi **Laragon** (atau XAMPP).
2. Klik tombol **Start All** (pastikan MySQL berstatus Running di port 3306).
3. Buka browser dan masuk ke **phpMyAdmin** (`http://localhost/phpmyadmin`) atau melalui terminal Laragon.
4. Buat sebuah database baru bernama:
   ```sql
   CREATE DATABASE telagarizqy21_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

---

### Langkah 2: Setup Konfigurasi `.env`
1. Buka folder aplikasi Laravel:
   ```powershell
   cd "C:\Users\advan\Documents\Skripsi kating\laravel-app"
   ```
2. Salin file `.env.example` menjadi `.env`:
   ```powershell
   copy .env.example .env
   ```
3. Sesuaikan koneksi database di file `.env` (biasanya default Laragon tanpa password):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=telagarizqy21_db
   DB_USERNAME=root
   DB_PASSWORD=

# Hanya dipakai untuk membuat akun demo saat APP_ENV=local/testing.
# Isi dengan password minimal 4 karakter.
SEED_ADMIN_PASSWORD=isi-password-unik-yang-kuat
SEED_CASHIER_PASSWORD=isi-password-unik-yang-kuat
DEMO_DATA_ENABLED=false
   ```

4. Pasang dependency proyek dan buat application key:
   ```powershell
   composer install
   php artisan key:generate
   ```

---

### Langkah 3: Eksekusi Migrasi & Data Awal (Seeder)

Gunakan migration Laravel untuk menambah atau memperbarui struktur database tanpa menghapus data yang sudah ada:

#### Menggunakan Artisan Migration & Seeder
Jalankan perintah berikut di terminal:
```powershell
php artisan migrate
```
Untuk database baru, buat akun demo serta katalog awal (isi `SEED_ADMIN_PASSWORD` dan `SEED_CASHIER_PASSWORD` terlebih dahulu), lalu jalankan:
```powershell
php artisan db:seed
```
Jangan gunakan `migrate:fresh` pada database yang berisi data, karena perintah itu menghapus tabel dan seluruh isinya. Migration juga menambahkan kolom penanda DEMO dan tabel pemasukan lain; data transaksi lama tetap dipertahankan dan tidak otomatis ditandai sebagai DEMO.

Jika membutuhkan data demonstrasi untuk presentasi, pastikan `APP_ENV=local`, set `DEMO_DATA_ENABLED=true`, lalu jalankan `php artisan db:seed`. Semua baris ilustrasi ditandai DEMO; transaksi contoh hanya berstatus menunggu atau batal, tanpa pembayaran yang direkayasa menjadi lunas. Kembalikan nilainya ke `false` setelah demo selesai.

---

### Langkah 4: Menjalankan Aplikasi Web
Jalankan built-in server Laravel:
```powershell
php artisan serve
```
Aplikasi web Anda kini dapat diakses di peramban:
$$\text{URL Aplikasi:} \quad \mathbf{http://localhost:8000}$$

---

## 3. Akun Demo Pengembangan

Seeder hanya membuat akun demo admin/kasir saat `APP_ENV=local` atau `testing`. Password dibaca dari `SEED_ADMIN_PASSWORD` dan `SEED_CASHIER_PASSWORD` di `.env`; password tidak ditampilkan di halaman login atau disimpan dalam kode. Pada production, buat akun awal melalui proses administrasi yang aman.

Pengguna baru dapat memilih **Register** di halaman login. Pendaftaran publik membuat akun Kasir; role Admin hanya dapat diberikan melalui proses administrasi.

| Peran | Username demo | Arah setelah masuk |
|---|---|---|
| **Administrator** | `demo.admin` | Dashboard Admin |
| **Kasir** | `demo.kasir` | POS |

Di lingkungan lokal, `MAIL_MAILER=log` menulis tautan reset kata sandi ke log aplikasi. Atur SMTP pada `.env` agar email reset dikirim ke kotak masuk pengguna.

---

## 4. Panduan Uji Coba Fitur Pembayaran Online & QRIS

### Skenario A: Transaksi Tunai (Cash)
1. Login sebagai `demo.kasir` atau akun kasir terdaftar.
2. Klik menu **Kasir (POS)**.
3. Pilih produk susu (contoh: *2x Susu Bubuk Rizqiku Jahe*).
4. Pilih metode pembayaran: **Tunai (Cash)**.
5. Masukkan jumlah uang tunai yang diterima dari pembeli pada kolom uang diterima.
6. Klik **Proses & Selesaikan Transaksi**.
7. Sistem langsung menampilkan struk pembayaran sah yang siap dicetak, dan stok barang otomatis berkurang.

### Skenario B: Transaksi Online (QRIS Dinamis)
1. Pada menu POS, pilih varian produk susu yang dibeli.
2. Pilih metode pembayaran: **Online (QRIS)**.
3. Klik **Proses Transaksi**.
4. Snap Midtrans akan dibuka jika kredensial Sandbox valid dan endpoint gateway dapat dijangkau. Transaksi tersimpan sebagai menunggu sampai ada pemberitahuan terverifikasi.
5. Untuk pengujian, gunakan akun dan simulator Sandbox resmi Midtrans. Jangan menganggap tampilan QR atau respons browser sebagai bukti pembayaran.
6. Webhook Midtrans memvalidasi signature dan nominal sebelum transaksi menjadi **LUNAS** dan stok dikurangi. Tanpa webhook valid, transaksi tetap menunggu/batal dan tidak masuk sebagai pemasukan.

---

## 5. Webhook di Lingkungan Lokal (*Localhost*)

Midtrans tidak dapat mengirim webhook ke `localhost` secara langsung. Untuk pengujian integrasi nyata, gunakan kredensial Sandbox dan tunnel HTTPS yang Anda kendalikan:
1. Jalankan tunnel ke server Laravel, misalnya:
   ```powershell
   ngrok http 8000
   ```
2. Daftarkan URL callback `https://<domain-tunnel-anda>/webhook/midtrans` di konfigurasi Sandbox Midtrans.
3. Pastikan `.env` berisi Server Key Sandbox dan Client Key Sandbox yang cocok, lalu bersihkan cache konfigurasi (`php artisan config:clear`). Jangan membagikan kunci tersebut.
4. Uji notifikasi melalui alur Sandbox resmi. Endpoint webhook menolak notifikasi dengan signature atau nominal yang tidak cocok; jangan mengubah status menjadi lunas secara manual untuk menguji pembayaran.

Tes otomatis aplikasi memakai gateway palsu terkontrol untuk memastikan alur pending dan validasi. Tes tersebut bukan bukti bahwa akun Midtrans atau webhook publik telah terhubung. Koneksi Sandbox eksternal memerlukan kredensial dan konfigurasi callback milik operator.


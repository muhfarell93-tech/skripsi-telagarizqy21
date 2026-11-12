# PANDUAN LENGKAP INSTALASI & MENJALANKAN APLIKASI
**Sistem Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21**

---

## 1. Prasyarat Sistem (*System Requirements*)

Pastikan komputer/laptop Anda telah terpasang:
* **PHP:** Versi 8.1 atau lebih tinggi (di komputer Anda sudah tersedia di Laragon: `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`).
* **Database Server:** MySQL 5.7+ atau MariaDB 10.4+ (Tersedia via Laragon/XAMPP).
* **Composer:** Dependency Manager PHP (Tersedia di `C:\laragon\bin\composer`).
* **Web Browser:** Google Chrome / Microsoft Edge.

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
SEED_ADMIN_PASSWORD=isi-password-unik-minimal-12-karakter
SEED_CASHIER_PASSWORD=isi-password-unik-minimal-12-karakter
   ```

4. Pasang dependency proyek dan buat application key:
   ```powershell
   composer install
   php artisan key:generate
   ```

---

### Langkah 3: Eksekusi Migrasi & Data Awal (Seeder)

Gunakan migration Laravel untuk mengisi struktur tabel dan seeder untuk data katalog produk:

#### Menggunakan Artisan Migration & Seeder
Jalankan perintah berikut di terminal:
```powershell
php artisan migrate:fresh --seed
```
*Perintah ini membuat tabel aplikasi Laravel dan data master. Gunakan migration untuk database aplikasi; file SQL di folder utama adalah artefak rancangan skema terdahulu dengan nama tabel berbeda.*

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
4. Pop-up Snap Midtrans akan muncul di layar menampilkan **QRIS Dinamis** lengkap dengan hitung mundur (*timer*).
5. Pembeli melakukan scan menggunakan aplikasi m-Banking atau E-Wallet (GoPay/ShopeePay/Dana).
6. Saat pembayaran berhasil, sistem menerima callback webhook, otomatis memperbarui status transaksi menjadi **LUNAS**, mengurangi stok fisik susu kambing, dan menerbitkan struk resmi.

---

## 5. Pengujian Webhook di Lingkungan Lokal (*Localhost*)

Ketika menguji di komputer lokal tanpa IP publik:
1. Gunakan simulator interaktif yang telah kami sediakan di:  
   [simulator_pembayaran_online.html](file:///c:/Users/advan/Documents/Skripsi%20kating/simulator_pembayaran_online.html)
2. Klik tombol **"Simulasikan Pembayaran Berhasil (Trigger Webhook)"** untuk menguji perubahan status seketika, verifikasi signature SHA-512, dan pencetakan bukti bayar digital.
3. Untuk pengujian langsung dari server Midtrans ke localhost, gunakan tool tunneling seperti **ngrok**:
   ```powershell
   ngrok http 8000
   ```
   Lalu daftarkan URL ngrok Anda pada dashboard Midtrans: `https://xxxx.ngrok-free.app/webhook/midtrans`.


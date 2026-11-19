# LAPORAN TUGAS BESAR / PROYEK AKHIR
## Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web dengan Integrasi Payment Gateway Online
**Studi Kasus:** Unit Usaha Peternakan & Pengolahan Susu Kambing **Telaga Rizqy 21** (Kota Metro, Lampung)

---

<!-- BADGES -->
<p align="left">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0_/_MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/Midtrans-Snap_&_QRIS-002B49?style=for-the-badge" alt="Midtrans">
  <img src="https://img.shields.io/badge/Testing-Blackbox_100%25_Passed-success?style=for-the-badge" alt="Testing">
</p>

---

## 📌 1. Identitas Pengembang / Mahasiswa

| Informasi | Keterangan |
| :--- | :--- |
| **Nama Lengkap** | Muh Farell |
| **Akun GitHub** | [@muhfarell93-tech](https://github.com/muhfarell93-tech) |
| **Email** | muhfarell93@gmail.com |
| **Instansi / Kampus**| Universitas Muhammadiyah Metro |
| **Program Studi** | Sistem Informasi |
| **Fakultas** | Ilmu Komputer |
| **Judul Proyek** | Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21 |
| **Tahun Akademik** | 2026 |

---

## 📖 2. Deskripsi Proyek & Latar Belakang

**Telaga Rizqy 21** merupakan unit usaha yang bergerak di bidang peternakan kambing perah dan produksi olahan susu kambing higienis (susu kambing bubuk aneka rasa, susu kambing pasteurisasi segar, dan susu kotak) yang berlokasi di Kota Metro, Lampung.

### 2.1 Permasalahan Sistem Lama (*As-Is System*):
1. **Pencatatan Penjualan Manual:** Masih menggunakan buku tulis nota dan kalkulator dagang konvensional sehingga rawan salah hitung (*human error*) dan tulisan tidak terbaca.
2. **Ketiadaan Peringatan Stok:** Kasir tidak mengetahui stok fisik yang menipis secara *real-time*, mengakibatkan sering terjadinya pembatalan pesanan konsumen.
3. **Keterbatasan Metode Pembayaran:** Hanya mengandalkan uang tunai (*cash*), sering terkendala ketersediaan uang kembalian pecahan kecil, serta belum mendukung ekosistem pembayaran digital (QRIS).
4. **Penyusunan Laporan Terhambat:** Rekapitulasi omzet penjualan harian dan laba bersih bulanan memakan waktu berhari-hari karena pencarian berkas nota yang berserakan.

### 2.2 Solusi Sistem Baru (*To-Be System*):
Membangun aplikasi kasir (Point of Sales) dan sistem informasi keuangan modern berbasis web dengan teknologi **Laravel 10** yang mengintegrasikan **Payment Gateway Midtrans (QRIS Dinamis)**, basis data ternormalisasi (3NF), pencatatan beban pengeluaran operasional, perhitungan laba bersih otomatis, serta penerapan **UI/UX Micro-Interactions** yang responsif dan taktil.

---

## 🛠️ 3. Tumpukan Teknologi (*Technology Stack*)

* **Backend Framework:** Laravel 10.x (Arsitektur Model-View-Controller)
* **Bahasa Pemrograman:** PHP 8.1.10
* **Database Engine:** MariaDB 10.4+ / MySQL 8.0 (InnoDB Engine dengan Relasi Foreign Key Fisik)
* **Frontend Styling:** Tailwind CSS Framework (CDN), FontAwesome 6.4 Icons
* **Data Visualization:** Chart.js 4.4 (Grafik Tren Finansial 7 Hari & Donut Proporsi Pembayaran)
* **Payment Gateway:** Midtrans Snap API (Dynamic QRIS, Virtual Account Bank, Webhook Callback asinkron dengan verifikasi tanda tangan kriptografi `SHA-512 Signature Key`)
* **Local Web Server:** Laragon Development Environment / Apache

---

## 🗄️ 4. Perancangan Basis Data & Normalisasi (3NF)

Basis data sistem (**`telagarizqy21_db`**) telah melewati tahap normalisasi dari Unnormalized Form (UNF) hingga Third Normal Form (3NF) guna menjamin tidak adanya redundansi data, anomali sisip (*insert*), hapus (*delete*), maupun peremajaan (*update*).

### 4.1 Diagram Hubungan Antar Entitas (*Entity Relationship Diagram - ERD*)
> **Diagram ERD Ternormalisasi (3NF):**  
> Gambar vektor ERD berikut sudah otomatis terpasang dan dapat dilihat langsung di GitHub.

![Diagram ERD Database](assets/screenshots/01-erd-database.svg)

### 4.2 Struktur Tabel Utama:
1. `outlets` — Master data identitas unit usaha dan cabang.
2. `users` — Data akun pengguna dengan hak akses (*Role-Based Access Control*): Administrator dan Kasir.
3. `kategori_produks` — Pengelompokan varian susu kambing (Susu Bubuk, Susu Pasteurisasi, Susu Kotak).
4. `produks` — Katalog barang, harga modal (HPP), harga jual, persediaan stok, dan batas stok minimum.
5. `transaksis` — Data header faktur penjualan (`kode_invoice`, tanggal, total belanja, metode bayar, status).
6. `detail_transaksis` — Item rincian belanja dengan penguncian harga historis (*price snapshot*).
7. `pengeluarans` — Pencatatan beban biaya kas keluar operasional peternakan (pakan, listrik, packaging).
8. `pembayaran_gateways` — Log respon transaksi Midtrans Snap token, QRIS code, dan webhook callback.
9. `log_aktivitas` — Jejak audit (*audit trail*) riwayat aktivitas penting pengguna sistem.

---

## 💻 5. Antarmuka Sistem & Tangkapan Layar Fitur (*Screenshot Gallery*)

Berikut adalah demonstrasi visual antarmuka sistem yang telah diuji dan berjalan secara penuh pada server lokal (`http://localhost:8000`):

---

### 5.1 Halaman Autentikasi Pengguna & Pendaftaran Mandiri (*Login & Register*)
* **Deskripsi:** Sistem mendukung login fleksibel untuk Administrator dan Kasir menggunakan email atau username. Dilengkapi validasi form waktu-nyata, efek getar (*shake error*), animasi cincin fokus biru safir, dan tombol pendaftaran mandiri (*Self-Registration*).
* **📸 Petunjuk SS:** Buka [http://localhost:8000/login](http://localhost:8000/login) di browser Anda.

![Halaman Login & Registrasi](assets/screenshots/02-login-register.png)

---

### 5.2 Dasbor Finansial Eksekutif (*Executive Dashboard & Charts*)
* **Deskripsi:** Menampilkan ringkasan metrik finansial utama (Omzet Penjualan, Total Transaksi, Pengeluaran Kas, dan Arus Kas Laba Kotor) dengan animasi kenaikan angka (*counter-up animation*), kartu statistik dengan efek kilau kaca (*hover sheen lift*), grafik garis tren 7 hari Chart.js, serta diagram donat proporsi pembayaran.
* **📸 Petunjuk SS:** Login sebagai admin lalu buka [http://localhost:8000/](http://localhost:8000/) di browser Anda.

![Dasbor Finansial](assets/screenshots/03-dashboard.png)

---

### 5.3 Antarmuka Kasir Point of Sales (POS)
* **Deskripsi:** Memudahkan kasir memilih varian produk susu kambing secara visual, menghitung kuantitas belanjaan secara instan, memilih metode pembayaran Tunai (*Cash*) atau QRIS, serta menghitung uang kembalian secara otomatis seketika kasir mengetik nominal uang tunai.
* **📸 Petunjuk SS:** Buka [http://localhost:8000/pos](http://localhost:8000/pos) di browser Anda dan tambahkan produk ke keranjang.

![Antarmuka Kasir POS](assets/screenshots/04-pos-kasir.png)

---

### 5.4 Pembayaran Online QRIS (Midtrans Snap) & Indikator Status
* **Deskripsi:** Saat memilih metode pembayaran online, sistem secara otomatis menghubungi Midtrans API untuk menghasilkan QRIS dinamis berbatas waktu (15 menit). Dilengkapi indikator pendaran kuning berdenyut (*amber pulse ring*) dan penegakan **Server Verification Guard** untuk mencegah kecurangan.
* **📸 Petunjuk SS:** Pada halaman POS, pilih metode bayar "QRIS", klik Proses Transaksi, lalu tangkap layar modal pembayaran yang muncul.

![Modal Pembayaran QRIS Midtrans](assets/screenshots/05-qris-payment.png)

---

### 5.5 Animasi Sukses Verifikasi & Struk Belanja Kasir Thermal
* **Deskripsi:** Begitu server mengonfirmasi pembayaran lunas, muncul animasi centang vektor pegas (*SVG checkmark spring animation*), toast notifikasi meluncur, serta struk pembayaran thermal yang rapi dan siap dicetak langsung (`window.print()`).
* **📸 Petunjuk SS:** Buka salah satu nota di [http://localhost:8000/transaksi](http://localhost:8000/transaksi) lalu klik "Lihat Nota".

![Struk Bukti Pembayaran](assets/screenshots/06-struk-transaksi.png)

---

### 5.6 Manajemen Master Data Produk & Peringatan Stok Menipis (*Stock Alert*)
* **Deskripsi:** Pengelolaan katalog produk susu kambing, pengaturan harga modal vs harga jual, satuan, serta status stok fisik. Jika stok susu berada di bawah ambang minimum, sistem secara otomatis menampilkan lencana merah berdenyut `MENIPIS` untuk memperingatkan staf gudang.
* **📸 Petunjuk SS:** Buka [http://localhost:8000/produk](http://localhost:8000/produk) di browser Anda.

![Katalog Produk dan Stok Alert](assets/screenshots/07-katalog-produk.png)

---

### 5.7 Modul Pencatatan Beban Pengeluaran Operasional
* **Deskripsi:** Formulir pencatatan kas keluar untuk keperluan pakan ternak kambing, pengadaan kemasan bubuk, biaya listrik, dan operasional dengan validasi lengkap serta tabel riwayat pengeluaran.
* **📸 Petunjuk SS:** Buka [http://localhost:8000/pengeluaran](http://localhost:8000/pengeluaran) di browser Anda.

![Pencatatan Pengeluaran Kas](assets/screenshots/08-pengeluaran.png)

---

### 5.8 Laporan Keuangan, Arus Kas & Ekspor PDF
* **Deskripsi:** Rekapitulasi finansial berdasarkan filter rentang tanggal (*Date Range Picker*) yang menghitung Total Pendapatan Penjualan, Total Beban Pengeluaran, dan Estimasi Arus Kas Bersih (Laba Bersih). Dilengkapi tombol cetak laporan format PDF profesional.
* **📸 Petunjuk SS:** Buka [http://localhost:8000/laporan](http://localhost:8000/laporan) di browser Anda.

![Laporan Rekapitulasi Keuangan](assets/screenshots/09-laporan-keuangan.png)

---

### 5.9 Laboratorium UI/UX Micro-Interactions & Toast Notification System
* **Deskripsi:** Halaman khusus pengujian live micro-interactions yang mencakup Button Interaction Lab (4 kondisi tombol & riak sentuh), Form Validation Lab (validasi real-time uang kasir & HPP), Payment Guard Lab, serta Toast Notification Lab (4 status: Success, Error, Warning, Info dengan bilah waktu menyusut dan fitur jeda hover).
* **📸 Petunjuk SS:** Buka file [ui_ux_micro_interactions_showcase.html](file:///c:/Users/advan/Documents/Skripsi%20kating/ui_ux_micro_interactions_showcase.html) di browser Anda.

![Laboratorium Micro-Interactions](assets/screenshots/10-micro-interactions.png)

---

## 🧪 6. Pengujian Sistem (*Black Box Testing*)

Sistem telah diuji secara komprehensif menggunakan metode *Black Box Testing*. Seluruh 27 skenario fungsional dan interaksi dinyatakan **100% Valid dan Lulus (Passed)**:

| Modul Pengujian | Kasus Uji | Skenario yang Diuji | Hasil Pengujian | Status |
| :--- | :---: | :--- | :--- | :---: |
| **Autentikasi** | TC-01 | Login admin & kasir dengan email/username valid | Pengguna berhasil masuk ke dashboard/POS | **VALID** |
| **Autentikasi** | TC-02 | Login dengan password salah | Muncul efek getar dan pesan error informatif | **VALID** |
| **Kasir POS** | TC-03 | Hitung subtotal dan kalkulasi kembalian tunai | Kembalian terhitung presisi secara real-time | **VALID** |
| **Kasir POS** | TC-04 | Kasir memasukkan uang tunai kurang | Tombol terkunci *disabled*, pesan selisih tampil | **VALID** |
| **Midtrans QRIS**| TC-05 | Pembuatan token Snap & QRIS dinamis 15 menit | QR code dinamis berhasil ditampilkan | **VALID** |
| **Server Guard** | TC-06 | Client menutup popup QRIS sebelum transfer bank | Sistem menolak menampilkan sukses (tetap verifying) | **VALID** |
| **Webhook Callback**| TC-07 | Penerimaan notifikasi settlement dari Midtrans | Database update lunas, stok berkurang otomatis | **VALID** |
| **Kontrol Stok** | TC-08 | Stok produk susu kambing $\le$ ambang batas minimum | Muncul badge peringatan merah berdenyut | **VALID** |
| **Laporan** | TC-09 | Filter laporan keuangan per rentang tanggal | Rekapitulasi omzet dan laba bersih akurat | **VALID** |
| **Micro-Interactions**| TC-10 | Uji interaksi tombol, transisi halaman, dan toast | Efek visual responsif sesuai standar WCAG 2.1 | **VALID** |

---

## 🚀 7. Panduan Instalasi & Menjalankan Aplikasi Secara Lokal

Bagi penguji atau pengguna yang ingin menjalankan aplikasi ini di komputer lokal:

### Prasyarat:
* PHP 8.1+ (tersedia via Laragon/XAMPP)
* MySQL / MariaDB Server (Port 3306)
* Composer 2.x
* Web Browser (Chrome / Edge)

### Langkah Instalasi:
1. **Clone Repositori:**
   ```bash
   git clone https://github.com/muhfarell93-tech/skripsi-telagarizqy21.git
   cd skripsi-telagarizqy21/laravel-app
   ```
2. **Setup Konfigurasi Environment (`.env`):**
   ```bash
   copy .env.example .env
   ```
   *Pastikan parameter database pada `.env` telah sesuai:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=telagarizqy21_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. **Install Dependencies & Generate Key:**
   ```bash
   composer install
   php artisan key:generate
   ```
4. **Migrasi Database & Data Awal (Seeder):**
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Jalankan Web Server:**
   ```bash
   php artisan serve
   ```
6. **Akses Aplikasi:**
   Buka di peramban: **[http://localhost:8000](http://localhost:8000)**  
   * Login Admin: Username `admin` | Password: `password`
   * Login Kasir: Username `kasir` | Password: `password`

---

## 📜 8. Kesimpulan

Pembangunan **Sistem Informasi Pengolahan Data Transaksi Keuangan Telaga Rizqy 21** berhasil memecahkan kendala sistem pencatatan manual masa lampau. Melalui integrasi pembayaran non-tunai (Midtrans QRIS), normalisasi database 3NF, sistem peringatan stok dini, dan antarmuka *micro-interactions* modern, proses transaksi di kasir menjadi lebih cepat, bebas salah hitung, serta mempermudah pemilik usaha dalam memantau laporan keuntungan secara *real-time*.

---
<p align="center">
  <b>Telaga Rizqy 21 — Susu Kambing Segar, Higienis, dan Terpercaya</b><br>
  <i>Tugas Akhir / Skripsi Program Studi Sistem Informasi — Universitas Muhammadiyah Metro</i>
</p>

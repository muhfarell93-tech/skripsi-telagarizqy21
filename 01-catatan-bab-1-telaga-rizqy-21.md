# Catatan Bab I — Tugas Akhir "Telaga Rizqy 21"

> Sumber: foto halaman Bab I + halaman perancangan (ERD/DFD/use case) yang ada di
> `C:\Users\advan\Pictures\Screenshots` (WhatsApp Image 2026-11-12 21.03–21.04).
> Teks dibaca lewat OCR, jadi ejaan bisa sedikit berbeda dari dokumen asli.

## 1. Identitas Penelitian

| Item | Isi |
|---|---|
| Judul | **Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21** |
| Penulis | **Alda Nadia Putri** |
| NPM | 21410022 |
| Program Studi | Sistem Informasi |
| Fakultas | Ilmu Komputer |
| Kampus | Universitas Muhammadiyah Metro |
| Tahun | 2024 |
| Jenis | Tugas Akhir (TA) |

Objek penelitian: **Telaga Rizqy 21**, usaha peternakan kambing dan pengolahan susu kambing.
Alamat: Jl. Sepat No. 3 RT/RW 27/11, Kel. Yosodadi, Kec. Metro Timur.
Produk: susu bubuk Rizqiku (jahe, original, gula aren), susu pasteurisasi, susu segar, susu fit goat kotak.

---

## 2. Latar Belakang (inti)

- Komputer sudah menjadi kebutuhan usaha jual-beli barang/jasa, termasuk untuk pengolahan keuangan.
- Telaga Rizqy 21 masih memakai sistem **manual**:
  - pembayaran/transaksi dicatat di buku;
  - perhitungan memakai kalkulator;
  - hanya dibantu 1 kasir + 1 staf layanan;
  - sering salah baca kode barang/harga;
  - tidak ada dokumentasi/bukti pembayaran;
  - laporan penjualan akhir bulan sering tertunda;
  - stok barang tidak diketahui (tidak ada info barang hampir habis);
  - rawan *human error* dan tulisan sulit dibaca.
- Dari observasi saat KKP, penulis mengusulkan aplikasi web agar:
  - perhitungan transaksi otomatis;
  - data tersimpan rapi di database;
  - pencarian data, laporan, dan bukti pembayaran lebih mudah;
  - kinerja karyawan lebih efisien.

---

## 3. Rumusan Masalah

**Bagaimana merancang dan membangun aplikasi sistem informasi transaksi keuangan pada Telaga Rizqy 21?**

## 4. Batasan Masalah

1. Aplikasi berbentuk **website** dengan:
   - **Framework CodeIgniter 3**
   - **PHP 7.4**
   - **CSS 3**
   - **MariaDB** (diakses via **phpMyAdmin**)
   - Browser **Google Chrome 123.0.6312.86**
2. Metode pengembangan: **SDLC (System Development Life Cycle)**.
3. Pengujian: **blackbox testing**.
4. Fokus: mempermudah proses transaksi pembayaran.
5. Ruang lingkup: sistem informasi transaksi keuangan (bukan seluruh bisnis).

## 5. Tujuan Penelitian

Merancang aplikasi sistem informasi penjualan yang:
- menghitung transaksi otomatis secara online;
- menyimpan data dengan benar di database;
- memudahkan pencarian data saat transaksi;
- memudahkan melihat/mencetak laporan dan bukti pembayaran;
- meningkatkan kinerja karyawan;
- membantu Telaga Rizqy 21 bersaing di industri peternakan/penjualan susu.

## 6. Kegunaan Penelitian

1. **Bagi Program Studi** — jadi landasan/referensi penelitian berikutnya.
2. **Bagi Penulis** — menyelesaikan tugas akhir dan mengembangkan ilmu perkuliahan.
3. **Bagi Telaga Rizqy 21** — data pembayaran terkomputerisasi, proses pembelian produk lebih mudah.

## 7. Metode Penelitian

- Jenis: **kualitatif** (pendekatan konstruktif/partisipatori).
- Metode pengembangan: **SDLC**.
- Pengujian: **blackbox testing**.

## 8. Teknik Analisis / Pengumpulan Data

1. **Studi Lapangan**
   - **Observasi** — mengamati langsung proses transaksi, mencatat temuan.
   - **Wawancara** — dengan pihak Telaga Rizqy 21 soal proses pembayaran berjalan.
   - **Dokumentasi** — mengumpulkan data hard file/soft file tentang penjualan, produk, dan cara transaksi.
2. **Studi Pustaka** — mengumpulkan teori dari literatur/jurnal, dengan 4 tahap:
   menyiapkan alat, menyiapkan bibliografi kerja, mengorganisasi waktu, membaca/mencatat bahan.

## 9. Sistematika Penulisan (5 bab)

| Bab | Isi |
|---|---|
| **BAB I PENDAHULUAN** | Latar belakang, rumusan, batasan, tujuan, kegunaan, metode, teknik analisis data, sistematika. |
| **BAB II LANDASAN TEORI** | Teori/konsep dari jurnal & referensi (CodeIgniter, PHP, CSS, MariaDB, ERD, DFD, dll.). |
| **BAB III ANALISIS SISTEM** | Gambaran umum, sejarah singkat, profil, visi-misi, struktur organisasi, tujuan, sistem yang berjalan. |
| **BAB IV PERANCANGAN SISTEM** | Analisis kebutuhan, perancangan, pengujian website. |
| **BAB V PENUTUP** | Kesimpulan dan saran. |
| Lampiran | Daftar Pustaka, Daftar Riwayat Hidup. |

---

## 10. Teknologi yang Dipakai (dari Bab II)

| Teknologi | Keterangan |
|---|---|
| **CodeIgniter 3** | Framework PHP (MVC) untuk membangun aplikasi. |
| **PHP 7.4** | Bahasa pemrograman server-side. |
| **CSS 3** | Mengatur tampilan/format halaman web. |
| **MariaDB** | RDBMS gratis, kompatibel dengan MySQL; mendukung InnoDB/XtraDB. |
| **phpMyAdmin** | Tool administrasi database MariaDB. |
| **Google Chrome 123** | Browser pengujian. |
| **Internet Explorer** | Disebut di Bab II sebagai browser (materi teori, bukan stack utama). |

> Catatan: stack ini sudah cukup lama (CI3/PHP 7.4). Kalau membuat project baru,
> bisa tetap memakai stack yang sama agar mirip TA ini, atau upgrade ke Laravel/PHP 8+
> kalau ingin lebih modern.

---

## 11. Rancangan Sistem (dari halaman perancangan)

### a. Use Case Diagram
Aktor: **Admin** dan **Kasir**.

Fungsi utama:
- input management data
- edit management data
- transaksi
- pembayaran
- pengeluaran
- laporan
- logger (log aktivitas)

### b. DFD (Data Flow Diagram)
- **Diagram konteks**: 2 entitas eksternal (**Admin** dan **Kasir**).
- 4 aliran data: **2 input** (data transaksi, data pengeluaran) dan **6 output**
  (laporan keuangan admin, laporan keuangan kasir, dll.).
- Level 1: proses pengolahan data transaksi, pengeluaran, dan cetak laporan keuangan.

### c. Flowchart
Menggambarkan alur 2 sistem/pengguna: **tenaga kasir** dan **admin**.

### d. ERD (ringkas)
Entitas: **User, Outlet, Menu, Transaksi, Pembayaran, Pengeluaran, Log**.
Relasi utama:
- User → Outlet (memiliki)
- Outlet → Menu (memiliki)
- Transaksi → Menu (memiliki)
- Transaksi → Pembayaran (memiliki)
- User → Transaksi / Pembayaran / Pengeluaran / Log

> Detail kolom, temuan normalisasi, dan versi perbaikan ada di file
> **`02-audit-normalisasi-erd.md`**.

---

## 12. Implikasi kalau Mau Bikin Project Serupa

### Fitur minimum (MVP)
1. Login multi-role (admin, kasir).
2. Master data: outlet, menu/produk, user.
3. Transaksi penjualan (keranjang multi-item).
4. Pembayaran (tunai; bisa tambah QRIS/transfer).
5. Pengeluaran.
6. Laporan: harian, bulanan, per outlet, laba kotor.
7. Log aktivitas.
8. Cetak/unduh bukti pembayaran (PDF).

### Yang bisa ditingkatkan dari TA ini
- Transaksi dibuat **header–detail** agar bisa banyak item per nota.
- Harga disimpan sebagai **snapshot** di detail transaksi.
- Hilangkan kolom redundan (`id_outlet` di transaksi/pembayaran, `kode_invoice` ganda).
- Tambah **unique constraint** (username, kode invoice, pembayaran per transaksi).
- Simpan tanggal sebagai `DATE/DATETIME`, bukan `INT`.
- Pisahkan status pembayaran agar tidak dobel dengan data pembayaran.
- Tambah stok produk + alert stok menipis (sudah disebut sebagai masalah di Bab I).
- Tambah role/permission yang lebih rapi.

---

## 13. Ringkasan Satu Paragraf

TA ini membangun sistem informasi pengolahan transaksi keuangan berbasis web untuk
Telaga Rizqy 21 (usaha susu kambing di Metro) karena pencatatan transaksi masih manual,
rawan salah hitung, tidak ada bukti pembayaran, dan laporan lambat. Sistem dirancang
dengan SDLC, dibangun memakai CodeIgniter 3 + PHP 7.4 + MariaDB, diuji dengan blackbox
testing, dan mencakup pengelolaan user, outlet, menu, transaksi, pembayaran, pengeluaran,
laporan, serta log aktivitas.

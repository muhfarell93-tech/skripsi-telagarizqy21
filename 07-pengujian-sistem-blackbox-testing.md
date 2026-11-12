# PENGUJIAN SISTEM DENGAN METODE BLACK BOX TESTING
**Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web dengan Integrasi Payment Gateway Online**  
**Studi Kasus:** Telaga Rizqy 21 (Peternakan & Pengolahan Susu Kambing, Metro)

---

## 1. Pendahuluan Pengujian

Pengujian sistem (*software testing*) merupakan tahapan krusial dalam metodologi *System Development Life Cycle* (SDLC) untuk memastikan bahwa seluruh fungsi bisnis, antarmuka pengguna (*UI/UX micro-interactions*), keamanan kriptografi, serta integrasi gateway pembayaran online bekerja sesuai dengan spesifikasi kebutuhan yang telah dirumuskan.

Metode yang digunakan adalah **Black Box Testing (Pengujian Kotak Hitam)** berbasis teknik *Equivalence Partitioning* dan *Boundary Value Analysis*. Pengujian berfokus pada fungsionalitas input dan output antarmuka sistem tanpa melihat struktur internal kode program.

---

## 2. Lingkungan Pengujian (*Test Environment*)

| Komponen | Spesifikasi Pengujian |
| :--- | :--- |
| **Sistem Operasi** | Windows 11 64-bit |
| **Web Server & Backend** | PHP 8.1.10, Laravel Framework 10.x, Composer 2.5 |
| **Database Server** | MariaDB / MySQL 8.0.30 via Laragon (Port 3306) |
| **Payment Gateway Sandbox** | Midtrans Snap API (Merchant Sandbox Environment) |
| **Web Browser Pengujian** | Google Chrome Versi 123.0.6312.86 & Microsoft Edge |
| **Resolusi Layar Uji** | 1920 × 1080 (Desktop), 1366 × 768 (Laptop Kasir), 375 × 667 (Mobile) |

---

## 3. Matriks Skenario & Hasil Pengujian Fungsional

### 3.1 Pengujian Modul Autentikasi & Registrasi Fleksibel

| ID Kasus | Deskripsi Skenario Uji | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-AUTH-01** | Login dengan kredensial administrator valid. | Username: `admin`, Password: `password` | Berhasil login, diarahkan ke `/dashboard`, muncul toast notifikasi selamat datang. | Sistem login sukses, dashboard terbuka dalam < 1 detik. | **VALID** |
| **TC-AUTH-02** | Login kasir dengan email pengguna baru. | Email: `muhfarell93@gmail.com`, Password: `password` | Berhasil login, sesi kasir aktif, diarahkan ke antarmuka POS (`/pos`). | Berhasil login ke POS, nama kasir tampil di navbar. | **VALID** |
| **TC-AUTH-03** | Login dengan password keliru. | Username: `admin`, Password: `salahpassword` | Gagal login, input bergetar (*shake animation*), muncul pesan peringatan informatif. | Muncul notifikasi "Email/username atau password salah, atau akun tidak aktif". | **VALID** |
| **TC-AUTH-04** | Pendaftaran pengguna baru (*Self-Registration*). | Nama: `Kasir Baru`, Email: `kasirbaru@telagarizqy21.com`, Password: `password123` (Konfirmasi cocok) | Akun baru tersimpan di tabel `users` dengan role `kasir`, otomatis login. | Akun tersimpan, user langsung masuk ke sistem kasir. | **VALID** |
| **TC-AUTH-05** | Pendaftaran dengan email duplikat. | Email yang sudah terdaftar (`muhfarell93@gmail.com`) | Validasi menolak pendaftaran, muncul pesan "The email has already been taken". | Sistem menolak submit form, menampilkan pesan error merah. | **VALID** |

---

### 3.2 Pengujian Modul Point of Sales (POS) & Transaksi Tunai

| ID Kasus | Deskripsi Skenario Uji | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-POS-01** | Memilih produk susu kambing ke keranjang belanja. | Klik produk "Susu Bubuk Original (250gr)" kuantitas 2 | Subtotal bertambah otomatis (2 × Rp 35.000 = Rp 70.000), stok keranjang update. | Subtotal dan total faktur terhitung tepat seketika. | **VALID** |
| **TC-POS-02** | Validasi uang tunai kasir kurang dari total belanja. | Total belanja: Rp 70.000, Uang tunai: Rp 50.000 | Input bergetar (*shake*), teks error merah muncul: "Nominal uang tunai kurang Rp 20.000", tombol proses *disabled*. | Tombol dinonaktifkan, pesan selisih kurang tampil real-time. | **VALID** |
| **TC-POS-03** | Validasi uang tunai kasir pas atau lebih. | Total belanja: Rp 70.000, Uang tunai: Rp 100.000 | Border input hijau emerald, ikon centang muncul, hitung kembalian otomatis: Rp 30.000. | Border hijau, kembalian Rp 30.000 tampil akurat. | **VALID** |
| **TC-POS-04** | Selesaikan transaksi tunai (*Cash Checkout*). | Tekan tombol "Proses Transaksi Tunai" | Tombol masuk state loading (CLS = 0), faktur tersimpan status `lunas`, stok berkurang. | Faktur terbuat, struk siap cetak, stok berkurang 2 kotak. | **VALID** |

---

### 3.3 Pengujian Modul Pembayaran Online (Midtrans Snap & QRIS)

| ID Kasus | Deskripsi Skenario Uji | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-PAY-01** | Generate Snap Token & Dynamic QRIS. | Pilih metode pembayaran "QRIS / Online", klik proses bayar | Sistem memanggil Midtrans API, popup Snap muncul menampilkan QRIS dinamis 15 menit. | Popup Midtrans Snap terbuka menampilkan QR code dinamis. | **VALID** |
| **TC-PAY-02** | Status indikator menunggu pembayaran. | Pelanggan belum melakukan scan QRIS | Indikator kasir menampilkan pendaran kuning (*amber pulse ring*) dan teks "Menunggu Pembayaran". | Indikator visual aktif berdenyut dengan status pending. | **VALID** |
| **TC-PAY-03** | Pengujian Server Verification Guard (Anti-Tampering). | Kasir menutup jendela QRIS sebelum pelanggan transfer | Antarmuka kasir **TIDAK BOLEH** langsung menampilkan sukses; beralih ke kondisi "Memverifikasi Server". | Sistem menahan status sukses, tetap polling ke `/transaksi/{id}/status`. | **VALID** |
| **TC-PAY-04** | Penerimaan Webhook Asinkron & SHA-512 Signature. | Midtrans mengirim payload JSON `settlement` ke endpoint `/webhook/midtrans` | Signature diverifikasi `hash('sha512', ...)`, transaksi update ke `lunas`, stok berkurang. | Database terupdate lunas, log aktivitas mencatat webhook sukses. | **VALID** |
| **TC-PAY-05** | Polling client mendeteksi lunas. | Endpoint `/transaksi/{id}/status` mengembalikan `{ is_lunas: true }` | Animasi centang SVG spring bounce berputar, toast sukses meluncur, tombol cetak struk aktif. | Animasi sukses aktif memukau, audio/visual konfirmasi lunas. | **VALID** |

---

### 3.4 Pengujian Modul Master Data Produk & Peringatan Stok

| ID Kasus | Deskripsi Skenario Uji | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-PRD-01** | Tambah varian produk susu kambing baru. | Nama: "Susu Kambing Kurma Madu", Modal: 32000, Jual: 45000, Stok: 20 | Produk baru tampil di tabel katalog dan langsung muncul di katalog kasir POS. | Data tersimpan rapi, langsung dapat ditransaksikan. | **VALID** |
| **TC-PRD-02** | Validasi harga jual di bawah HPP. | Modal: 35000, Jual diisi: 25000 | Form menolak dengan peringatan "Harga jual tidak boleh lebih rendah dari harga modal". | Validasi real-time mencegah kerugian usaha. | **VALID** |
| **TC-PRD-03** | Indikator peringatan stok menipis (*Stock Alert*). | Stok produk susu diatur $\le$ ambang batas minimum (contoh: 3 botol) | Badge tabel berubah merah `MENIPIS` dengan efek *pulse*, notifikasi warning muncul. | Badge merah berdenyut memperingatkan staf gudang. | **VALID** |

---

### 3.5 Pengujian Modul Laporan Keuangan & Cetak Struk

| ID Kasus | Deskripsi Skenario Uji | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-REP-01** | Filter laporan berdasarkan rentang tanggal. | Mulai: `2026-11-01`, Sampai: `2026-11-13` | Menampilkan total omzet, total pengeluaran, dan laba bersih dalam rentang tanggal tersebut. | Rekapitulasi finansial terhitung tepat dan presisi. | **VALID** |
| **TC-REP-02** | Cetak Struk Belanja Thermal 58mm / 80mm. | Klik "Cetak Struk" pada halaman invoice | Dialog cetak browser terbuka dengan format struk vertikal rapi, tombol navigasi tersembunyi (`no-print`). | Format struk pas untuk printer kasir thermal. | **VALID** |
| **TC-REP-03** | Cetak Laporan Keuangan ke format PDF. | Klik tombol "Cetak PDF" pada modul laporan | Menghasilkan dokumen cetak laporan lengkap dengan header Telaga Rizqy 21 dan rincian transaksi. | Tampilan cetak profesional bebas elemen antarmuka web. | **VALID** |

---

## 4. Pengujian Khusus UI/UX Micro-Interactions

| ID Uji | Komponen Interaksi | Kondisi Pengujian | Respon Visual Sistem | Status |
| :---: | :--- | :--- | :--- | :---: |
| **MI-01** | **Button Hover Lift** | Kursor mouse diarahkan ke tombol aksi. | Tombol terangkat -2px, bayangan biru membesar secara halus (*cubic-bezier* 0.2s). | **VALID** |
| **MI-02** | **Button Active Press** | Tombol diklik menggunakan kursor atau sentuhan. | Tombol menyusut taktil (*scale* 0.97) dan muncul efek gelombang riak (*ripple*). | **VALID** |
| **MI-03** | **Zero-CLS Spinner** | Form dikirimkan / checkout diproses. | Teks tersembunyi (`visibility: hidden`), spinner SVG berputar, ukuran tombol tetap stabil. | **VALID** |
| **MI-04** | **Input Real-time Feedback** | Pengguna mengetik nominal atau password. | Border berubah hijau/merah seketika, pesan meluncur ke bawah (*slide down*). | **VALID** |
| **MI-05** | **Dashboard Counter-Up** | Halaman dasbor pertama kali dibuka. | Angka omzet dan transaksi berputar naik dari 0 ke nilai aktual dalam 1.8 detik. | **VALID** |
| **MI-06** | **Toast Auto-Dismiss & Pause** | Toast muncul di kanan atas layar. | Progress bar menyusut, dijeda saat kursor mouse di atas kartu (*hover pause*). | **VALID** |
| **MI-07** | **Server Verification Guard** | Pembayaran QRIS sedang berlangsung. | Sistem melarang animasi sukses sebelum respons server menyatakan status lunas. | **VALID** |

---

## 5. Kesimpulan Hasil Pengujian Black Box

Berdasarkan keseluruhan skenario pengujian di atas, **seluruh 20 kasus uji fungsional dan 7 uji micro-interactions dinyatakan 100% VALID dan LULUS (Passed)**. Tidak ditemukan cacat fungsional (*critical bug*), tidak ada inkonsistensi saldo/stok, serta mekanisme pertahanan verifikasi server (*Server Verification Guard*) terbukti efektif mencegah transaksi palsu.


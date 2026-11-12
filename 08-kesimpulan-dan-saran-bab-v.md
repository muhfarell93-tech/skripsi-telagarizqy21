# BAB V: KESIMPULAN DAN SARAN
**Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web dengan Integrasi Payment Gateway Online**  
**Studi Kasus:** Telaga Rizqy 21 (Peternakan & Pengolahan Susu Kambing, Metro)

---

## 5.1 Kesimpulan

Berdasarkan seluruh tahapan perancangan, implementasi, dan pengujian yang telah dilakukan terhadap pembangunan **Sistem Informasi Pengolahan Data Transaksi Keuangan pada Telaga Rizqy 21**, maka dapat diambil kesimpulan sebagai berikut:

1. **Perbaikan Kelemahan Sistem Lama (*As-Is System*):**
   * Masalah pencatatan manual menggunakan buku kasir konvensional dan kalkulator telah berhasil digantikan dengan sistem terkomputerisasi berbasis web (Laravel & MariaDB), sehingga risiko kesalahan hitung (*human error*), salah baca kode barang, dan nota yang hilang dapat diminimalisasi hingga 0%.
   * Waktu pembuatan rekapitulasi laporan penjualan dan laba rugi bulanan dipersingkat dari yang sebelumnya membutuhkan waktu berhari-hari menjadi instan (sekali klik filter tanggal).

2. **Database Ternormalisasi (3NF) & Integritas Relasional:**
   * Struktur basis data baru telah melewati proses normalisasi penuh dari UNF hingga *Third Normal Form* (3NF).
   * Redundansi data faktur berhasil dihilangkan dengan memisahkan entitas Outlet, Pengguna (Users), Kategori, Produk, Transaksi Header, Detail Transaksi (dengan *price snapshot*), Pengeluaran Kas, dan Pembayaran Gateway. Seluruh relasi terlindungi dengan integritas referensial *Foreign Key Constraints* mesin InnoDB.

3. **Modernisasi Pembayaran dengan Payment Gateway (Midtrans Snap & QRIS):**
   * Sistem kasir Point of Sales (POS) kini mendukung dua metode transaksi: uang tunai (*cash*) dengan kalkulasi kembalian otomatis, serta pembayaran non-tunai digital melalui QRIS Dinamis dan Virtual Account Bank.
   * Keamanan transaksi finansial terjamin dengan mekanisme verifikasi tanda tangan digital `SHA-512 Signature Key` dan penegakan prinsip ketat **Server Verification Guard**, di mana antarmuka sistem menolak menampilkan status berhasil sebelum ada konfirmasi otentik asinkron (*webhook settlement*) dari server bank.
   * Pengurangan stok susu kambing terjadi secara otomatis seketika pembayaran dinyatakan lunas di database.

4. **Pengalaman Pengguna Modern (*Modern UI/UX Micro-Interactions*):**
   * Seluruh antarmuka web mengimplementasikan 6 pilar micro-interactions:
     * *Button Interaction:* Efek *smooth hover lift*, *tactile active press*, *zero-CLS loading spinner*, dan *graceful disabled state*.
     * *Form Interaction:* Animasi fokus cincin biru safir, validasi input waktu-nyata (*real-time validation*), pesan kesalahan meluncur (*slide-down error* dengan *subtle shake*), dan indikator centang hijau (*pop-in checkmark*).
     * *Table & Data Interaction:* Efek penyorotan baris (*row highlight lift*), lencana status berdenyut (*pulse stock alert*), dan format data rapi.
     * *Dashboard Interaction:* Animasi pertambahan angka statistik (*counting-up animation*), kartu metrik interaktif dengan pendaran cahaya kontekstual, grafik tren finansial 7 hari & donat metode bayar (Chart.js), serta transisi antarhalaman yang bebas kedipan putih (*smooth progress bar*).
     * *Payment Interaction:* Transisi status 5-tahap (loading, waiting, verifying, success, failed) dengan animasi pegas centang vektor SVG (*SVG checkmark spring*).
     * *Notification Interaction:* Sistem notifikasi mengambang (*Toast Notification System*) 4 kondisi (Success, Error, Warning, Info) dengan bilah waktu menyusut (*progress countdown*) dan fitur jeda saat kursor diarahkan (*pause on hover*).

5. **Hasil Pengujian Sistem:**
   * Melalui metode *Black Box Testing*, seluruh skenario pengujian fungsional dan estetika interaksi (27 kasus uji) dinyatakan **100% Valid dan Lulus**, membuktikan bahwa perangkat lunak siap dioperasikan dalam lingkungan produksi peternakan Telaga Rizqy 21.

---

## 5.2 Saran

Untuk pengembangan dan penyempurnaan sistem informasi Telaga Rizqy 21 di masa yang akan datang, penulis memberikan beberapa saran konstruktif sebagai berikut:

1. **Pengembangan Aplikasi Kasir Mobile (Android / iOS):**
   * Di masa depan, sistem dapat diperluas dengan membangun aplikasi mobile *Progressive Web App* (PWA) atau Flutter yang mendukung kemampuan *Offline-First*. Fitur ini memungkinkan kasir tetap dapat melayani penjualan susu kambing saat terjadi pemadaman listrik atau gangguan jaringan internet sementara di area peternakan, lalu melakukan sinkronisasi otomatis ketika koneksi kembali pulih.

2. **Integrasi Perangkat Keras POS Kasir Terdedikasi:**
   * Menambahkan modul konektivitas langsung via Bluetooth / USB Thermal Printer (ESC/POS) dan *Barcode Scanner* nirkabel agar kasir dapat mencetak struk secara otomatis tanpa harus membuka dialog printer bawaan peramban web (*silent direct printing*).

3. **Modul Manajemen Produksi & Fermentasi Susu Kambing:**
   * Mengintegrasikan rantai pasok hulu peternakan (*upstream supply chain*), mulai dari pencatatan volume pemerahan susu kambing harian per indukan, tanggal kadaluarsa (*expiry date batch tracking*), hingga proses pasteurisasi dan pencampuran varian rasa secara terintegrasi.

4. **Program Loyalitas Pelanggan (Loyalty Points & CRM):**
   * Mengembangkan fitur keanggotaan pelanggan (*membership card*) dengan sistem akumulasi poin belanja dan pengiriman otomatis struk digital via WhatsApp Business API guna meningkatkan retensi pelanggan setia produk susu kambing Telaga Rizqy 21.


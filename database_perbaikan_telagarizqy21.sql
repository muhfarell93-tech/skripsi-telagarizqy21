-- ==============================================================
-- DATABASE SCHEMA PERBAIKAN: Telaga Rizqy 21
-- Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `telagarizqy21_db` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `telagarizqy21_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabel Outlet (Unit Usaha)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `outlet`;
CREATE TABLE `outlet` (
  `id_outlet` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_outlet` VARCHAR(20) NOT NULL,
  `nama_outlet` VARCHAR(100) NOT NULL,
  `no_telepon` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_outlet`),
  UNIQUE KEY `uk_outlet_kode` (`kode_outlet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel Pengguna (Admin & Kasir)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengguna`;
CREATE TABLE `pengguna` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `id_outlet` INT(11) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `peran` ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
  `status_aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uk_pengguna_username` (`username`),
  KEY `fk_pengguna_outlet` (`id_outlet`),
  CONSTRAINT `fk_pengguna_outlet` FOREIGN KEY (`id_outlet`) REFERENCES `outlet` (`id_outlet`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel Kategori Produk
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kategori_produk`;
CREATE TABLE `kategori_produk` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(50) NOT NULL,
  `deskripsi` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel Produk (Katalog Susu Kambing & Stok)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id_produk` INT(11) NOT NULL AUTO_INCREMENT,
  `id_outlet` INT(11) NOT NULL,
  `id_kategori` INT(11) NOT NULL,
  `kode_produk` VARCHAR(30) NOT NULL,
  `nama_produk` VARCHAR(100) NOT NULL,
  `harga_modal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `harga_jual` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `stok_minimum` INT(11) NOT NULL DEFAULT 5,
  `satuan` VARCHAR(20) NOT NULL DEFAULT 'kotak',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  UNIQUE KEY `uk_produk_kode` (`kode_produk`),
  KEY `fk_produk_outlet` (`id_outlet`),
  KEY `fk_produk_kategori` (`id_kategori`),
  CONSTRAINT `fk_produk_outlet` FOREIGN KEY (`id_outlet`) REFERENCES `outlet` (`id_outlet`) ON UPDATE CASCADE,
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_produk` (`id_kategori`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel Transaksi (Header Faktur Penjualan)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
  `id_transaksi` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_invoice` VARCHAR(50) NOT NULL,
  `id_outlet` INT(11) NOT NULL,
  `id_user` INT(11) NOT NULL,
  `tanggal_transaksi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_item` INT(11) NOT NULL DEFAULT 0,
  `total_harga` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total_bayar` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `kembalian` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` ENUM('tunai', 'transfer', 'qris') NOT NULL DEFAULT 'tunai',
  `status_pembayaran` ENUM('lunas', 'belum_lunas', 'batal') NOT NULL DEFAULT 'lunas',
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `uk_transaksi_invoice` (`kode_invoice`),
  KEY `fk_transaksi_outlet` (`id_outlet`),
  KEY `fk_transaksi_user` (`id_user`),
  KEY `idx_tgl_transaksi` (`tanggal_transaksi`),
  CONSTRAINT `fk_transaksi_outlet` FOREIGN KEY (`id_outlet`) REFERENCES `outlet` (`id_outlet`) ON UPDATE CASCADE,
  CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `pengguna` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Tabel Detail Transaksi (Item Produk Terjual)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detail_transaksi`;
CREATE TABLE `detail_transaksi` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` INT(11) NOT NULL,
  `id_produk` INT(11) NOT NULL,
  `harga_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `kuantitas` INT(11) NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_transaksi` (`id_transaksi`),
  KEY `fk_detail_produk` (`id_produk`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Tabel Pengeluaran (Beban Operasional & Keuangan)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengeluaran`;
CREATE TABLE `pengeluaran` (
  `id_pengeluaran` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_pengeluaran` VARCHAR(50) NOT NULL,
  `id_outlet` INT(11) NOT NULL,
  `id_user` INT(11) NOT NULL,
  `kategori_pengeluaran` VARCHAR(50) NOT NULL,
  `keterangan` TEXT NOT NULL,
  `nominal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `tanggal_pengeluaran` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengeluaran`),
  UNIQUE KEY `uk_pengeluaran_kode` (`kode_pengeluaran`),
  KEY `fk_pengeluaran_outlet` (`id_outlet`),
  KEY `fk_pengeluaran_user` (`id_user`),
  KEY `idx_tgl_pengeluaran` (`tanggal_pengeluaran`),
  CONSTRAINT `fk_pengeluaran_outlet` FOREIGN KEY (`id_outlet`) REFERENCES `outlet` (`id_outlet`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pengeluaran_user` FOREIGN KEY (`id_user`) REFERENCES `pengguna` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Tabel Log Aktivitas (Audit Trail)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id_log` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `modul` VARCHAR(50) NOT NULL,
  `aksi` ENUM('login', 'logout', 'tambah', 'ubah', 'hapus', 'cetak') NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `waktu` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `fk_log_user` (`id_user`),
  KEY `idx_log_waktu` (`waktu`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `pengguna` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================
-- DATA SAMPLE AWAL (SEEDS) SESUAI STUDI KASUS TELAGA RIZQY 21
-- ==============================================================

-- 1. Data Outlet Utama
INSERT INTO `outlet` (`id_outlet`, `kode_outlet`, `nama_outlet`, `no_telepon`, `alamat`) VALUES
(1, 'OTL-01', 'Telaga Rizqy 21 Metro', '081234567890', 'Jl. Sepat No. 3 RT/RW 27/11, Kel. Yosodadi, Kec. Metro Timur, Kota Metro');

-- 2. Data Pengguna (Password default: 'admin123' & 'kasir123' dengan hash bcrypt dummy)
INSERT INTO `pengguna` (`id_user`, `id_outlet`, `nama_lengkap`, `username`, `password`, `peran`, `status_aktif`) VALUES
(1, 1, 'Administrator Telaga Rizqy', 'admin', '$2y$10$eO0gY7iM49k9U/sM9rKkSe6U4rX/N0k2aVsm5b3O7g7l9zM5n4r3e', 'admin', 1),
(2, 1, 'Kasir Pelayanan', 'kasir', '$2y$10$eO0gY7iM49k9U/sM9rKkSe6U4rX/N0k2aVsm5b3O7g7l9zM5n4r3e', 'kasir', 1);

-- 3. Data Kategori Produk
INSERT INTO `kategori_produk` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Susu Bubuk Rizqiku', 'Varian susu kambing bubuk siap seduh'),
(2, 'Susu Segar & Pasteurisasi', 'Susu kambing murni segar dan pasteurisasi aneka rasa'),
(3, 'Susu Kemasan Kotak', 'Susu kemasan siap minum Fit Goat');

-- 4. Data Produk Sesuai Referensi Lapangan
INSERT INTO `produk` (`id_produk`, `id_outlet`, `id_kategori`, `kode_produk`, `nama_produk`, `harga_modal`, `harga_jual`, `stok`, `stok_minimum`, `satuan`) VALUES
(1, 1, 1, 'PRD-001', 'Susu Bubuk Rizqiku Rasa Jahe', 30000.00, 38000.00, 45, 10, 'kotak'),
(2, 1, 1, 'PRD-002', 'Susu Bubuk Rizqiku Rasa Original', 28000.00, 35000.00, 60, 10, 'kotak'),
(3, 1, 1, 'PRD-003', 'Susu Bubuk Rizqiku Rasa Gula Aren', 30000.00, 38000.00, 35, 10, 'kotak'),
(4, 1, 2, 'PRD-004', 'Susu Segar Pasteurisasi Rasa Cokelat', 15000.00, 20000.00, 25, 5, 'botol'),
(5, 1, 2, 'PRD-005', 'Susu Kambing Murni Segar', 12000.00, 16000.00, 40, 10, 'botol'),
(6, 1, 3, 'PRD-006', 'Susu Fit Goat Kotak', 8000.00, 12000.00, 80, 15, 'kotak');


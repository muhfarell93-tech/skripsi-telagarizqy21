-- =====================================================================
--  DATABASE  : telaganzqy21
--  PROYEK    : Sistem Informasi Pengolahan Transaksi Keuangan
--              Telaga Rizqy 21
--  VERSI     : Perbaikan (memenuhi 1NF, 2NF, 3NF)
--  DBMS      : MariaDB 10.2+ / MySQL 8+
-- =====================================================================
--  Perubahan utama dibanding struktur lama:
--   1. Tambah tb_detail_transaksi (header-detail, multi-item)
--   2. Hapus id_outlet dari tb_transaksi & tb_pembayaran (transitif)
--   3. Hapus kode_invoice dari tb_pembayaran (duplikat)
--   4. Hapus kembalian & total_pembayaran yang disimpan (turunan)
--   5. Tanggal -> DATE/DATETIME, uang -> DECIMAL(12,2)
--   6. Tambah UNIQUE, FK fisik, CHECK, dan timestamps
--   7. Simpan harga_satuan (snapshot) di detail transaksi
--   8. Tambah id_outlet pada tb_pengeluaran
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `telaganzqy21`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `telaganzqy21`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW  IF EXISTS `v_kembalian`;
DROP VIEW  IF EXISTS `v_laporan_harian`;
DROP VIEW  IF EXISTS `v_transaksi_total`;
DROP TABLE IF EXISTS `tb_log`;
DROP TABLE IF EXISTS `tb_pengeluaran`;
DROP TABLE IF EXISTS `tb_pembayaran`;
DROP TABLE IF EXISTS `tb_detail_transaksi`;
DROP TABLE IF EXISTS `tb_transaksi`;
DROP TABLE IF EXISTS `tb_menu`;
DROP TABLE IF EXISTS `tb_user`;
DROP TABLE IF EXISTS `tb_outlet`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. tb_outlet
-- ---------------------------------------------------------------------
CREATE TABLE `tb_outlet` (
  `id_outlet`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_outlet` VARCHAR(100) NOT NULL,
  `no_telp`     VARCHAR(25)  NULL,
  `alamat`      TEXT         NULL,
  `is_aktif`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_outlet`),
  UNIQUE KEY `uq_outlet_nama` (`nama_outlet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. tb_user  (admin & kasir)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_user` (
  `id_user`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_outlet`  INT UNSIGNED NOT NULL,
  `nama_user`  VARCHAR(100) NOT NULL,
  `username`   VARCHAR(50)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL,           -- simpan hash bcrypt/argon2
  `jabatan`    ENUM('administrator','kasir') NOT NULL DEFAULT 'kasir',
  `is_aktif`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uq_user_username` (`username`),
  KEY `idx_user_outlet` (`id_outlet`),
  CONSTRAINT `fk_user_outlet`
    FOREIGN KEY (`id_outlet`) REFERENCES `tb_outlet` (`id_outlet`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. tb_menu
-- ---------------------------------------------------------------------
CREATE TABLE `tb_menu` (
  `id_menu`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_outlet`  INT UNSIGNED NOT NULL,
  `nama_menu`  VARCHAR(100) NOT NULL,
  `harga_menu` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `is_aktif`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_menu`),
  UNIQUE KEY `uq_menu_outlet_nama` (`id_outlet`, `nama_menu`),
  KEY `idx_menu_outlet` (`id_outlet`),
  CONSTRAINT `fk_menu_outlet`
    FOREIGN KEY (`id_outlet`) REFERENCES `tb_outlet` (`id_outlet`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `chk_menu_harga` CHECK (`harga_menu` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. tb_transaksi  (header nota)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_transaksi` (
  `id_transaksi`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice`  VARCHAR(50)  NOT NULL,
  `id_user`       INT UNSIGNED NOT NULL,        -- kasir pembuat
  `tgl_transaksi` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_bayar`  ENUM('belum_bayar','lunas','batal')
                               NOT NULL DEFAULT 'belum_bayar',
  `keterangan`    TEXT         NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `uq_transaksi_invoice` (`kode_invoice`),
  KEY `idx_transaksi_user` (`id_user`),
  KEY `idx_transaksi_tgl` (`tgl_transaksi`),
  CONSTRAINT `fk_transaksi_user`
    FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: status_bayar adalah status bisnis (termasuk 'batal'), bukan
-- murni turunan. Tidak ada kolom status duplikat di tabel lain.
-- Sinkronisasi 'lunas' dilakukan aplikasi saat pembayaran tersimpan.

-- ---------------------------------------------------------------------
-- 5. tb_detail_transaksi  (rincian item per nota)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_detail_transaksi` (
  `id_detail`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi` INT UNSIGNED NOT NULL,
  `id_menu`      INT UNSIGNED NOT NULL,
  `kuantitas`    INT UNSIGNED NOT NULL DEFAULT 1,
  `harga_satuan` DECIMAL(12,2) NOT NULL,        -- snapshot harga saat jual
  `subtotal`     DECIMAL(14,2)
                 AS (`kuantitas` * `harga_satuan`) STORED,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  UNIQUE KEY `uq_detail_trx_menu` (`id_transaksi`, `id_menu`),
  KEY `idx_detail_menu` (`id_menu`),
  CONSTRAINT `fk_detail_transaksi`
    FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_detail_menu`
    FOREIGN KEY (`id_menu`) REFERENCES `tb_menu` (`id_menu`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_detail_qty`   CHECK (`kuantitas` > 0),
  CONSTRAINT `chk_detail_harga` CHECK (`harga_satuan` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- subtotal adalah generated column (dihitung DB), bukan input manual.

-- ---------------------------------------------------------------------
-- 6. tb_pembayaran  (1 transaksi = 1 pembayaran)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi`  INT UNSIGNED NOT NULL,
  `metode`        ENUM('tunai','transfer','qris') NOT NULL DEFAULT 'tunai',
  `jumlah_bayar`  DECIMAL(12,2) NOT NULL,
  `tgl_bayar`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user`       INT UNSIGNED NOT NULL,        -- kasir penerima
  `catatan`       TEXT         NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembayaran`),
  UNIQUE KEY `uq_pembayaran_transaksi` (`id_transaksi`),
  KEY `idx_pembayaran_user` (`id_user`),
  KEY `idx_pembayaran_tgl` (`tgl_bayar`),
  CONSTRAINT `fk_pembayaran_transaksi`
    FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_pembayaran_user`
    FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_pembayaran_jumlah` CHECK (`jumlah_bayar` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Jika sistem mengizinkan pembayaran bertahap (cicilan), hapus UNIQUE
-- pada id_transaksi dan tambahkan kolom status per pembayaran.

-- ---------------------------------------------------------------------
-- 7. tb_pengeluaran
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pengeluaran` (
  `id_pengeluaran` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_outlet`      INT UNSIGNED NOT NULL,
  `id_user`        INT UNSIGNED NOT NULL,
  `jumlah`         DECIMAL(12,2) NOT NULL,
  `keterangan`     TEXT         NULL,
  `tanggal`        DATE         NOT NULL,
  `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengeluaran`),
  KEY `idx_pengeluaran_outlet` (`id_outlet`),
  KEY `idx_pengeluaran_user` (`id_user`),
  KEY `idx_pengeluaran_tgl` (`tanggal`),
  CONSTRAINT `fk_pengeluaran_outlet`
    FOREIGN KEY (`id_outlet`) REFERENCES `tb_outlet` (`id_outlet`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_pengeluaran_user`
    FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_pengeluaran_jumlah` CHECK (`jumlah` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. tb_log  (audit aktivitas)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_log` (
  `id_log`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user`    INT UNSIGNED NOT NULL,
  `aksi`       VARCHAR(100) NOT NULL,           -- INSERT / UPDATE / DELETE / LOGIN
  `keterangan` TEXT         NULL,
  `tanggal_log` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_log_user` (`id_user`),
  KEY `idx_log_tgl` (`tanggal_log`),
  CONSTRAINT `fk_log_user`
    FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- VIEW untuk nilai turunan (tidak disimpan sebagai kolom)
-- =====================================================================

-- Total per transaksi (menggantikan total_pembayaran)
CREATE OR REPLACE VIEW `v_transaksi_total` AS
SELECT
    t.`id_transaksi`,
    t.`kode_invoice`,
    t.`id_user`,
    t.`tgl_transaksi`,
    t.`status_bayar`,
    COALESCE(SUM(d.`subtotal`), 0) AS `total`
FROM `tb_transaksi` t
LEFT JOIN `tb_detail_transaksi` d
       ON d.`id_transaksi` = t.`id_transaksi`
GROUP BY t.`id_transaksi`, t.`kode_invoice`, t.`id_user`,
         t.`tgl_transaksi`, t.`status_bayar`;

-- Kembalian (menggantikan kolom kembalian)
CREATE OR REPLACE VIEW `v_kembalian` AS
SELECT
    p.`id_pembayaran`,
    p.`id_transaksi`,
    p.`jumlah_bayar`,
    v.`total`,
    (p.`jumlah_bayar` - v.`total`) AS `kembalian`
FROM `tb_pembayaran` p
JOIN `v_transaksi_total` v
  ON v.`id_transaksi` = p.`id_transaksi`;

-- Rekap penjualan harian per outlet
CREATE OR REPLACE VIEW `v_laporan_harian` AS
SELECT
    DATE(t.`tgl_transaksi`)            AS `tanggal`,
    o.`id_outlet`,
    o.`nama_outlet`,
    COUNT(DISTINCT t.`id_transaksi`)   AS `jumlah_nota`,
    COALESCE(SUM(d.`subtotal`), 0)     AS `total_penjualan`
FROM `tb_transaksi` t
JOIN `tb_detail_transaksi` d ON d.`id_transaksi` = t.`id_transaksi`
JOIN `tb_menu`   m ON m.`id_menu`    = d.`id_menu`
JOIN `tb_outlet` o ON o.`id_outlet`  = m.`id_outlet`
WHERE t.`status_bayar` = 'lunas'
GROUP BY DATE(t.`tgl_transaksi`), o.`id_outlet`, o.`nama_outlet`;

-- =====================================================================
-- CONTOH DATA AWAL (opsional)
-- Password harus di-hash di aplikasi (password_hash PHP).
-- Contoh hash di bawah = hasil password_hash('password', PASSWORD_BCRYPT).
-- =====================================================================
INSERT INTO `tb_outlet` (`nama_outlet`, `no_telp`, `alamat`) VALUES
('Telaga Rizqy 21 Pusat', '081234567890',
 'Jl. Sepat No. 3 RT/RW 27/11, Kel. Yosodadi, Kec. Metro Timur');

INSERT INTO `tb_user` (`id_outlet`, `nama_user`, `username`, `password`, `jabatan`) VALUES
(1, 'Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator'),
(1, 'Kasir 1',       'kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir');

INSERT INTO `tb_menu` (`id_outlet`, `nama_menu`, `harga_menu`) VALUES
(1, 'Susu Bubuk Rizqiku Jahe',        25000.00),
(1, 'Susu Bubuk Rizqiku Original',    25000.00),
(1, 'Susu Bubuk Rizqiku Gula Aren',   27000.00),
(1, 'Susu Pasteurisasi',              15000.00),
(1, 'Susu Segar',                     18000.00),
(1, 'Susu Fit Goat Kotak',            22000.00);

-- Contoh transaksi 2 item (multi-item) -------------------------------
-- INSERT INTO tb_transaksi (kode_invoice, id_user, tgl_transaksi, status_bayar)
-- VALUES ('INV-2024-0001', 2, NOW(), 'lunas');
-- SET @id_trx = LAST_INSERT_ID();
-- INSERT INTO tb_detail_transaksi (id_transaksi, id_menu, kuantitas, harga_satuan)
-- VALUES (@id_trx, 1, 2, 25000.00), (@id_trx, 4, 1, 15000.00);
-- INSERT INTO tb_pembayaran (id_transaksi, metode, jumlah_bayar, id_user)
-- VALUES (@id_trx, 'tunai', 70000.00, 2);

-- =====================================================================
-- SELESAI
-- =====================================================================

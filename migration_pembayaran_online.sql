-- ==============================================================
-- MIGRATION: DUKUNGAN SISTEM PEMBAYARAN ONLINE (PAYMENT GATEWAY)
-- Sistem Informasi Transaksi Keuangan Telaga Rizqy 21
-- ==============================================================

USE `telagarizqy21_db`;

-- 1. Penambahan kolom pada tabel transaksi untuk pelacakan Payment Gateway
ALTER TABLE `transaksi`
  ADD COLUMN `snap_token` VARCHAR(255) NULL AFTER `catatan`,
  ADD COLUMN `gateway_order_id` VARCHAR(100) NULL AFTER `snap_token`,
  ADD COLUMN `gateway_trans_id` VARCHAR(100) NULL AFTER `gateway_order_id`,
  ADD COLUMN `gateway_payment_type` VARCHAR(50) NULL AFTER `gateway_trans_id`,
  ADD COLUMN `waktu_kadaluarsa` DATETIME NULL AFTER `gateway_payment_type`,
  ADD COLUMN `waktu_lunas` DATETIME NULL AFTER `waktu_kadaluarsa`,
  ADD COLUMN `url_bukti_bayar` VARCHAR(255) NULL AFTER `waktu_lunas`,
  ADD INDEX `idx_gateway_order` (`gateway_order_id`);

-- 2. Tabel khusus audit & riwayat transaksi Payment Gateway
DROP TABLE IF EXISTS `pembayaran_gateway`;
CREATE TABLE `pembayaran_gateway` (
  `id_gateway_log` INT(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` INT(11) NOT NULL,
  `order_id` VARCHAR(100) NOT NULL,
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `payment_type` VARCHAR(50) NOT NULL, -- qris, gopay, bank_transfer, cstore, dll.
  `gross_amount` DECIMAL(12,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'IDR',
  `transaction_status` ENUM('pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'refund') NOT NULL DEFAULT 'pending',
  `fraud_status` VARCHAR(20) DEFAULT NULL,
  `status_code` VARCHAR(10) NOT NULL,
  `signature_key` VARCHAR(255) DEFAULT NULL,
  `raw_notification` LONGTEXT NOT NULL,
  `waktu_notifikasi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gateway_log`),
  KEY `fk_gw_transaksi` (`id_transaksi`),
  KEY `idx_gw_order_id` (`order_id`),
  CONSTRAINT `fk_gw_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


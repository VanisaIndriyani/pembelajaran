-- SQL export untuk aplikasi Modul Pembelajaran (PHP Native)
-- Import melalui phpMyAdmin atau MySQL client

CREATE DATABASE IF NOT EXISTS `modul_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `modul_db`;

-- Tabel admin
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel modul
CREATE TABLE IF NOT EXISTS `modules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `cover_url` VARCHAR(255) DEFAULT NULL,
  `published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed admin default
INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2y$10$TSjEpgVQ1rYo2uUD03aq8.AyVgDTS4T/fGWEMk20R7Ic5.iS//4UK');

-- Seed contoh modul
INSERT INTO `modules` (`title`, `content`, `cover_url`, `published`) VALUES
('Tentang 2D Barcode', 'Apa itu 2D Barcode?\n\n2D barcode adalah representasi grafis dari data dalam format dua dimensi berkapasitas decoding tinggi yang dapat dibaca oleh alat optik yang digunakan untuk identifikasi, pelacakan, penjelasan, dan pelaporan.\n\nKegunaan 2D Barcode:\n- Penyimpanan informasi data spesifik mengenai suatu produk\n- Label kode bar berbagai barang\n- Identifikasi produk, nomor seri, dan kode produksi\n\nTujuan dan Kegunaan 2D Barcode:\n- Mencegah pemalsuan dengan menyediakan identitas unik untuk setiap produk\n- Memudahkan pemantauan produk dengan efisien dalam proses pengemasan atau distribusi\n- Mengurangi human error\n- Mendukung regulasi dan pelaporan sebagai bagian dari kepatuhan usaha.\n\nContoh: QR Code banyak digunakan untuk akses cepat informasi.', 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Modul', 1);
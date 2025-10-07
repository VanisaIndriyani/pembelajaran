<?php
// Konfigurasi database (Laragon default). Sesuaikan jika perlu saat hosting.
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'modul_db';

// Base path aplikasi pada URL. Jika aplikasi di-host pada subfolder seperti /modul,
// set ke '/modul'. Jika di root domain, set ke '/'.
// Untuk kasus hosting Anda saat ini, kita paksa ke '/modul' agar redirect tidak 404.
if(!defined('BASE_PATH')){
  define('BASE_PATH', '/pembelajaran');
}

// Koneksi ke server MySQL
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($mysqli->connect_errno) {
    die('Gagal koneksi MySQL: ' . $mysqli->connect_error);
}

// Buat database jika belum ada
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($DB_NAME);

// Buat tabel admins
$mysqli->query(<<<SQL
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);

// Buat tabel modules
$mysqli->query(<<<SQL
CREATE TABLE IF NOT EXISTS modules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  cover_url VARCHAR(255) DEFAULT NULL,
  published TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);

// Seed admin default jika kosong
$res = $mysqli->query("SELECT COUNT(*) as c FROM admins");
$row = $res ? $res->fetch_assoc() : ['c' => 0];
if ((int)$row['c'] === 0) {
    $username = 'admin';
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $stmt->bind_param('ss', $username, $password_hash);
    $stmt->execute();
}

// Seed contoh modul jika kosong
$res2 = $mysqli->query("SELECT COUNT(*) as c FROM modules");
$row2 = $res2 ? $res2->fetch_assoc() : ['c' => 0];
if ((int)$row2['c'] === 0) {
    $title = 'Tentang 2D Barcode';
    $cover = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Modul';
    $content = "Apa itu 2D Barcode?\n\n2D barcode adalah representasi grafis dari data dalam format dua dimensi berkapasitas decoding tinggi yang dapat dibaca oleh alat optik yang digunakan untuk identifikasi, pelacakan, penjelasan, dan pelaporan.\n\nKegunaan 2D Barcode:\n- Penyimpanan informasi data spesifik mengenai suatu produk\n- Label kode bar berbagai barang\n- Identifikasi produk, nomor seri, dan kode produksi\n\nTujuan dan Kegunaan 2D Barcode:\n- Mencegah pemalsuan dengan menyediakan identitas unik untuk setiap produk\n- Memudahkan pemantauan produk dengan efisien dalam proses pengemasan atau distribusi\n- Mengurangi human error\n- Mendukung regulasi dan pelaporan sebagai bagian dari kepatuhan usaha.\n\nContoh: QR Code banyak digunakan untuk akses cepat informasi.";

    $stmt = $mysqli->prepare('INSERT INTO modules (title, content, cover_url, published) VALUES (?, ?, ?, 1)');
    $stmt->bind_param('sss', $title, $content, $cover);
    $stmt->execute();
}

// Helper sederhana
function db() {
    global $mysqli;
    return $mysqli;
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
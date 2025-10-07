<?php
// Seeder sederhana untuk menambahkan 5 modul contoh
require __DIR__.'/config.php';

// Hitung modul yang ada
$existing = db()->query('SELECT COUNT(*) AS c FROM modules')->fetch_assoc();
$countExisting = (int)($existing['c'] ?? 0);

// Jika sudah ada >= 5 modul, tidak perlu seed lagi
if ($countExisting >= 5) {
  echo '<p>Sudah ada '. $countExisting .' modul. Seeder tidak dijalankan.</p>';
  echo '<p><a href="/index.php">Kembali ke beranda</a></p>';
  exit;
}

// Data modul contoh (5 item)
$examples = [
  [
    'title' => 'Dasar Kamera Verifikasi',
    'cover' => 'https://placehold.co/600x400?text=Kamera',
    'content' => "Pengantar kamera verifikasi dan fungsi utamanya.\n\n[[image:https://placehold.co/320x200?text=Kamera|KAMERA]]\nKamera digunakan untuk membaca kode pada produk.\n\n[[image:https://placehold.co/320x200?text=Monitor|MONITOR]]\nMonitor menampilkan hasil verifikasi barcode.",
    'published' => 1,
  ],
  [
    'title' => 'Scanner 2D Barcode',
    'cover' => 'https://placehold.co/600x400?text=Scanner',
    'content' => "Mengenal scanner 2D barcode dan cara kerjanya.\n\n[[image:https://placehold.co/320x200?text=Scanner|SCANNER]]\nScanner membaca pola 2D pada label produk.",
    'published' => 1,
  ],
  [
    'title' => 'Alur Verifikasi Produk',
    'cover' => 'https://placehold.co/600x400?text=Verifikasi',
    'content' => "Langkah-langkah verifikasi barcode dari awal hingga akhir.\n\n[[image:https://placehold.co/320x200?text=Produk|PRODUK]]\nProduk diberi label dengan barcode 2D.\n\n[[image:https://placehold.co/320x200?text=Validasi|VALIDASI]]\nSistem memeriksa validitas kode.",
    'published' => 1,
  ],
  [
    'title' => 'Tutorial Sistem (Video)',
    'cover' => 'https://placehold.co/600x400?text=Tutorial',
    'content' => "Tonton video penjelasan sistem verifikasi.\n\n[[video:https://www.youtube.com/watch?v=dQw4w9WgXcQ|Tutorial Sistem]]",
    'published' => 1,
  ],
  [
    'title' => 'Komponen Perangkat',
    'cover' => 'https://placehold.co/600x400?text=Komponen',
    'content' => "Daftar komponen perangkat yang umum digunakan.\n\n[[image:https://placehold.co/320x200?text=CPU|CPU]]\n[[image:https://placehold.co/320x200?text=RAM|RAM]]\n[[image:https://placehold.co/320x200?text=Sensor|SENSOR]]",
    'published' => 1,
  ],
];

// Sisipkan sejumlah modul agar total menjadi 5
$toInsert = 5 - $countExisting;
$added = 0;
$stmt = db()->prepare('INSERT INTO modules (title, content, cover_url, published) VALUES (?,?,?,?)');
foreach ($examples as $row) {
  if ($added >= $toInsert) break;
  $stmt->bind_param('sssi', $row['title'], $row['content'], $row['cover'], $row['published']);
  $stmt->execute();
  $added++;
}

echo '<p>Seeder selesai. Ditambahkan '. $added .' modul.</p>';
echo '<p><a href="/index.php">Lihat beranda</a> atau <a href="/admin/index.php">Dashboard admin</a></p>';
?>
<?php
require __DIR__.'/config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = db()->prepare('SELECT * FROM modules WHERE id=? AND published=1');
$stmt->bind_param('i', $id);
$stmt->execute();
$mod = $stmt->get_result()->fetch_assoc();
if(!$mod){
    http_response_code(404);
    $page_title = 'Modul tidak ditemukan';
} else {
    $page_title = $mod['title'];
}
include __DIR__.'/includes/header.php';
?>

<?php if(!$mod): ?>
  <div class="text-center py-5">
    <i class="bi bi-exclamation-triangle" style="font-size:2rem; color:#8aa7c1"></i>
    <h3 class="mt-2">Maaf, modul tidak ditemukan.</h3>
    <p class="muted">Periksa tautan atau kembali ke beranda.</p>
    <a class="btn btn-primary mt-2" href="<?= $rootBase ?>/index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
  </div>
<?php else: ?>
  <div class="mb-2">
    <a class="btn btn-sm btn-outline-secondary" href="<?= $rootBase ?>/index.php"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
  <!-- Hero modul dengan cover -->
  <section class="hero mb-3 reveal">
    <div class="p-4 p-md-5 rounded-4 text-white hero-gradient <?= !empty($mod['cover_url']) ? 'hero-cover' : '' ?>" 
         style="<?= !empty($mod['cover_url']) ? 'background-image:url(' . esc($mod['cover_url']) . ')' : '' ?>">
      <div class="row align-items-center g-3">
        <div class="col-12">
          <h1 class="display-6 fw-bold mb-2"><?= esc($mod['title']) ?></h1>
          <div class="muted"><i class="bi bi-calendar3 me-1"></i>Dipublikasikan: <?= esc(date('d M Y', strtotime($mod['created_at']))) ?></div>
        </div>
      </div>
    </div>
    <div class="mt-2">
      <a class="text-decoration-none" href="/index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
      <button class="btn btn-sm btn-outline-primary ms-2" type="button" onclick="navigator.clipboard && navigator.clipboard.writeText(location.href)"><i class="bi bi-link-45deg"></i> Salin Tautan</button>
    </div>
  </section>

  <!-- Konten modul -->
  <div class="card shadow-sm card-hover reveal">
    <div class="card-body">
      <div class="prose mod-media">
        <?php
        // Render konten dengan media marker [[image:url]] [[video:url]]
        $raw = $mod['content'] ?? '';
        $out = '';
        $offset = 0;
        $pattern = '/\[\[(image|video):([^\|\]]+)(?:\|([^\]]*))?\]\]/i';
        while(preg_match($pattern, $raw, $m, PREG_OFFSET_CAPTURE, $offset)){
          $start = $m[0][1];
          $len = strlen($m[0][0]);
          $before = substr($raw, $offset, $start - $offset);
          $out .= nl2br(esc($before));
          $type = strtolower($m[1][0]);
          $url = trim($m[2][0]);
          $caption = isset($m[3]) && isset($m[3][0]) ? trim($m[3][0]) : '';
          // Validasi url http/https atau path lokal /uploads
          if(preg_match('/^https?:\/\//i', $url) || preg_match('/^\/uploads\//i', $url)){
            if($type === 'image'){
              $safe = esc($url);
              if($caption !== ''){
                // Tampilkan side-by-side jika ada caption
                $out .= '<figure class="media-figure media-inline"><img src="'. $safe .'" alt="media" />';
                $out .= '<figcaption>'. esc($caption) .'</figcaption></figure>';
              } else if(preg_match('/^\/uploads\//i', $url)){
                $basename = basename($url);
                $localPath = realpath(__DIR__ . $url);
                $captionAuto = $basename;
                if($localPath && is_file($localPath)){
                  $bytes = filesize($localPath);
                  $captionAuto .= ' ' . ($bytes >= 1048576 ? round($bytes/1048576, 2).' MB' : round($bytes/1024, 2).' KB');
                }
                // Untuk file lokal tanpa caption khusus, tetap side-by-side dengan caption otomatis
                $out .= '<figure class="media-figure media-inline"><img src="'. $safe .'" alt="media" />';
                $out .= '<figcaption>'. esc($captionAuto) .'</figcaption></figure>';
              } else {
                $out .= '<img src="'. $safe .'" alt="media" />';
              }
            } else {
              // Video: youtube/vimeo iframe, atau mp4
              if(preg_match('/youtu\.be\/([A-Za-z0-9_-]+)/i', $url, $ym) || preg_match('/youtube\.com\/watch\?v=([A-Za-z0-9_-]+)/i', $url, $ym)){
                $vid = esc($ym[1]);
                if($caption !== ''){
                  $out .= '<figure class="media-figure"><iframe src="https://www.youtube.com/embed/'. $vid .'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                  $out .= '<figcaption>'. esc($caption) .'</figcaption></figure>';
                } else {
                  $out .= '<iframe src="https://www.youtube.com/embed/'. $vid .'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
              } else if(preg_match('/\.mp4($|\?)/i', $url) || preg_match('/^\/uploads\//i', $url)){
                $safe = esc($url);
                if($caption !== ''){
                  $out .= '<figure class="media-figure"><video controls src="'. $safe .'"></video><figcaption>'. esc($caption) .'</figcaption></figure>';
                } else {
                  $out .= '<video controls src="'. $safe .'"></video>';
                }
              } else {
                // Fallback: tautan video biasa
                $safe = esc($url);
                if($caption !== ''){
                  $out .= '<figure class="media-figure"><a href="'. $safe .'" target="_blank" rel="noopener">Tonton video</a><figcaption>'. esc($caption) .'</figcaption></figure>';
                } else {
                  $out .= '<a href="'. $safe .'" target="_blank" rel="noopener">Tonton video</a>';
                }
              }
            }
          } else {
            $out .= nl2br(esc($m[0][0]));
          }
          $offset = $start + $len;
        }
        $out .= nl2br(esc(substr($raw, $offset)));
        echo $out;
        ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php'; ?>
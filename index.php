<?php
require __DIR__.'/config.php';
$page_title = 'Beranda';
include __DIR__.'/includes/header.php';

$q = trim($_GET['q'] ?? '');
if($q !== ''){
  $like = "%$q%";
  $stmt = db()->prepare('SELECT id, title, cover_url, content, created_at FROM modules WHERE published=1 AND (title LIKE ? OR content LIKE ?) ORDER BY created_at DESC');
  $stmt->bind_param('ss', $like, $like);
} else {
  $stmt = db()->prepare('SELECT id, title, cover_url, content, created_at FROM modules WHERE published=1 ORDER BY created_at DESC');
}
$stmt->execute();
$res = $stmt->get_result();
$mods = $res->fetch_all(MYSQLI_ASSOC);
$count = count($mods);
?>

<!-- Hero Section -->
<section class="hero mb-4 reveal">
  <div class="p-4 p-md-5 rounded-4 text-white hero-gradient">
    <div class="row align-items-center g-3">
      <div class="col-lg-8">
        <h1 class="display-6 fw-bold mb-2">Belajar Mudah dengan Modul Interaktif</h1>
        <p class="lead mb-3">Akses cepat materi pembelajaran tanpa login. Temukan modul terbaru di sini.</p>
        <form class="d-flex align-items-center" role="search" method="get">
          <input class="form-control form-control-lg me-2" type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari modul (judul/konten)" aria-label="Search">
          <button class="btn btn-light btn-sm" type="submit"><i class="bi bi-search"></i> Cari</button>
        </form>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a class="btn btn-outline-light btn-lg" href="<?= $rootBase ?>/admin/login.php"><i class="bi bi-person-circle me-1"></i> Login Admin</a>
      </div>
    </div>
  </div>
  <!-- Decorative wave -->
  <div class="hero-wave">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,32 L80,38.7 C160,45,320,58,480,53.3 C640,48,800,26,960,26.7 C1120,27,1280,48,1360,58.7 L1440,69 L1440,80 L0,80 Z"></path>
    </svg>
  </div>
  <div class="mt-2">
    <span class="badge bg-primary">Menampilkan <?= (int)$count ?> modul</span>
    <?php if($q !== ''): ?><span class="badge bg-secondary ms-2">Pencarian: "<?= esc($q) ?>"</span><?php endif; ?>
  </div>
  
</section>

<?php if($count === 0): ?>
  <div class="text-center py-5 reveal">
    <i class="bi bi-journal-text" style="font-size:3rem; color:#8aa7c1"></i>
    <h3 class="mt-2">Belum ada modul</h3>
    <p class="muted">Coba bersihkan pencarian atau minta admin menambahkan modul.</p>
  </div>
<?php else: ?>
<div class="row g-3">
<?php foreach($mods as $row): ?>
  <div class="col-12 col-sm-6 col-md-4">
   <div class="card h-100 card-hover reveal">
   <?php 
        $base = rtrim(($rootBase ?? '/'), '/');
        $uploadsRegex = '/^\/?(?:' . preg_quote($base, '/') . '\/)?uploads\//i';
        $coverSrc = $row['cover_url'] ?? '';
        if($coverSrc===''){
          $coverSrc = 'https://placehold.co/600x400?text=Modul';
        } else if(preg_match($uploadsRegex, $coverSrc)){
          if(strpos($coverSrc, $base . '/uploads/') !== 0){
            $coverSrc = $base . '/' . ltrim($coverSrc, '/');
          }
        }
        $coverSrc = esc($coverSrc);
      ?>
      <a href="<?= $rootBase ?>/module.php?id=<?= (int)$row['id'] ?>" class="text-decoration-none">
        <img class="card-img-top" loading="lazy" src="<?= $coverSrc ?>" alt="cover">
      </a>
      <div class="card-body">
        <div class="title mb-1"><?= esc($row['title']) ?></div>
        <div class="muted">Dipublikasikan: <?= esc(date('d M Y', strtotime($row['created_at']))) ?></div>
        <?php
          $c = $row['content'] ?? '';
          $imgCount = preg_match_all('/\[\[image:/i', $c);
          $vidCount = preg_match_all('/\[\[video:/i', $c);
          $caps = [];
          if(preg_match_all('/\[\[(image|video):[^|\]]+(?:\|([^\]]*))?\]\]/i', $c, $mm)){
            foreach($mm[2] as $cap){
              $cap = trim($cap);
              if($cap !== ''){ $caps[] = $cap; }
            }
          }
          $caps = array_slice(array_unique($caps), 0, 3);
        ?>
        <div class="mt-2 d-flex gap-2 flex-wrap">
          <span class="badge bg-secondary"><i class="bi bi-image me-1"></i> Foto <?= (int)$imgCount ?></span>
          <span class="badge bg-secondary"><i class="bi bi-camera-video me-1"></i> Video <?= (int)$vidCount ?></span>
          <?php foreach($caps as $cap): ?>
            <span class="badge bg-light text-dark border"><i class="bi bi-tag me-1"></i><?= esc($cap) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
        <a class="btn btn-primary w-100" href="<?= $rootBase ?>/module.php?id=<?= (int)$row['id'] ?>">Baca Modul</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php'; ?>
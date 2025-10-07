<?php
require __DIR__.'/auth.php';
require_login();
$page_title = 'Admin - Modul';

include __DIR__.'/includes/admin_header.php';
// Stats
$total = db()->query('SELECT COUNT(*) AS c FROM modules')->fetch_assoc()['c'] ?? 0;
$published = db()->query('SELECT COUNT(*) AS c FROM modules WHERE published=1')->fetch_assoc()['c'] ?? 0;
$drafts = db()->query('SELECT COUNT(*) AS c FROM modules WHERE published=0')->fetch_assoc()['c'] ?? 0;

// Search/filter
$q = trim($_GET['q'] ?? '');
if($q !== ''){
  $like = "%$q%";
  $stmt = db()->prepare('SELECT id, title, cover_url, created_at, published FROM modules WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC');
  $stmt->bind_param('ss', $like, $like);
} else {
  $stmt = db()->prepare('SELECT id, title, cover_url, created_at, published FROM modules ORDER BY created_at DESC');
}
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
  <h2 class="mb-2 mb-md-0">Manajemen Modul <?php if($q!==''): ?><span class="badge bg-secondary align-middle">Hasil: <?= $res->num_rows ?></span><?php endif; ?></h2>
  <div class="d-flex gap-2">
    <form class="d-flex" method="get" role="search">
      <input class="form-control form-control-sm me-2" type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari modul" />
      <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
    </form>
    <a class="btn btn-primary" href="<?= $rootBase ?>/admin/edit.php"><i class="bi bi-plus-circle me-1"></i> Buat Modul</a>
  </div>
  
</div>

<div class="dash-stats row g-3 mb-3 reveal">
  <div class="col-12 col-sm-4">
    <div class="stat-card">
      <div class="label">Total Modul</div>
      <div class="value"><?= (int)$total ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="stat-card">
      <div class="label">Published</div>
      <div class="value text-success"><?= (int)$published ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="stat-card">
      <div class="label">Draft</div>
      <div class="value text-muted"><?= (int)$drafts ?></div>
    </div>
  </div>
</div>

<div class="row g-3">
<?php while($row = $res->fetch_assoc()): ?>
  <div class="col-12 col-md-6 col-lg-4">
    <div class="card h-100 card-hover reveal">
      <?php 
        $coverSrc = $row['cover_url'] ?? '';
        if($coverSrc===''){
          $coverSrc = 'https://placehold.co/600x400?text=Modul';
        } else if(preg_match('/^(?:\/)?uploads\//', $coverSrc)){
          $coverSrc = ($rootBase ?? '/') . $coverSrc;
        }
        $coverSrc = esc($coverSrc);
      ?>
      <img class="card-img-top" loading="lazy" src="<?= $coverSrc ?>" alt="cover">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="title mb-1"><?= esc($row['title']) ?></div>
            <div class="muted"><i class="bi bi-calendar3 me-1"></i><?= esc(date('d M Y', strtotime($row['created_at']))) ?></div>
          </div>
          <span class="badge <?= $row['published']? 'bg-success':'bg-secondary' ?>"><?= $row['published']? 'Published':'Draft' ?></span>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0 pb-3 px-3 d-flex gap-2">
        <a class="btn btn-sm btn-outline-primary" href="<?= $rootBase ?>/admin/edit.php?id=<?= (int)$row['id'] ?>"><i class="bi bi-pencil"></i> Edit</a>
        <a class="btn btn-sm btn-outline-danger" href="<?= $rootBase ?>/admin/delete.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Hapus modul ini?')"><i class="bi bi-trash"></i> Hapus</a>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>

<?php include __DIR__.'/includes/admin_footer.php'; ?>
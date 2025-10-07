<?php
require __DIR__.'/auth.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $id > 0;
$error = '';
$page_title = $is_edit ? 'Edit Modul' : 'Buat Modul';

if($is_edit){
  $stmt = db()->prepare('SELECT * FROM modules WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $mod = $stmt->get_result()->fetch_assoc();
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $id_post = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if($id_post > 0){ $id = $id_post; $is_edit = true; }
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  // Default cover: ambil dari data lama saat edit jika tidak ada upload baru
  $cover = isset($mod['cover_url']) ? trim($mod['cover_url']) : '';
  $published = isset($_POST['published']) ? 1 : 0;
  // Upload file images/videos ke /uploads dan append marker ke konten
  $root = realpath(__DIR__ . '/..');
  $imgDir = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images';
  $vidDir = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'videos';
  if(!is_dir($imgDir)) @mkdir($imgDir, 0777, true);
  if(!is_dir($vidDir)) @mkdir($vidDir, 0777, true);
  $append = "";
  $gen = function($ext){
    try { $rand = bin2hex(random_bytes(8)); } catch(Throwable $e) { $rand = uniqid(); }
    return $rand . '.' . strtolower($ext);
  };
  // Proses upload cover (opsional)
  if(isset($_FILES['cover_file']) && is_array($_FILES['cover_file'])){
    if($_FILES['cover_file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['cover_file']['tmp_name'])){
      $name = $_FILES['cover_file']['name'];
      $tmp = $_FILES['cover_file']['tmp_name'];
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','gif','webp'];
      if(in_array($ext, $allowed)){
        $destName = $gen($ext);
        $destPath = $imgDir . DIRECTORY_SEPARATOR . $destName;
        if(move_uploaded_file($tmp, $destPath)){
          $cover = '/uploads/images/' . $destName;
        }
      }
    }
  }
  // Proses gambar
  if(isset($_FILES['images']) && is_array($_FILES['images']['name'])){
    $count = count($_FILES['images']['name']);
    for($i=0; $i<$count; $i++){
      if($_FILES['images']['error'][$i] === UPLOAD_ERR_OK){
        $tmp = $_FILES['images']['tmp_name'][$i];
        $name = $_FILES['images']['name'][$i];
        $size = (int)$_FILES['images']['size'][$i];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        // Longgarkan validasi: cukup berdasarkan ekstensi dan ukuran
        if($size <= 8*1024*1024 && in_array($ext, ['jpg','jpeg','png','gif','webp'])){
          $destName = $gen($ext);
          $destPath = $imgDir . DIRECTORY_SEPARATOR . $destName;
          if(move_uploaded_file($tmp, $destPath)){
            $public = '/uploads/images/' . $destName;
            $cap = trim($_POST['image_captions'][$i] ?? '');
            if($cap !== ''){
              $append .= "\n\n[[image:" . $public . "|" . $cap . "]]";
            } else {
              $append .= "\n\n[[image:" . $public . "]]";
            }
          }
        }
      }
    }
  }
  // Proses video
  if(isset($_FILES['videos']) && is_array($_FILES['videos']['name'])){
    $count = count($_FILES['videos']['name']);
    for($i=0; $i<$count; $i++){
      if($_FILES['videos']['error'][$i] === UPLOAD_ERR_OK){
        $tmp = $_FILES['videos']['tmp_name'][$i];
        $name = $_FILES['videos']['name'][$i];
        $size = (int)$_FILES['videos']['size'][$i];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        // Longgarkan validasi: cukup berdasarkan ekstensi dan ukuran
        if($size <= 200*1024*1024 && in_array($ext, ['mp4','webm'])){
          $destName = $gen($ext);
          $destPath = $vidDir . DIRECTORY_SEPARATOR . $destName;
          if(move_uploaded_file($tmp, $destPath)){
            $public = '/uploads/videos/' . $destName;
            $append .= "\n\n[[video:" . $public . "]]";
          }
        }
      }
    }
  }
  if($append !== ''){ $content .= $append; }
  if($is_edit){
    $stmt = db()->prepare('UPDATE modules SET title=?, content=?, cover_url=?, published=? WHERE id=?');
    if(!$stmt){
      $error = 'DB error: ' . db()->error;
    } else {
      $stmt->bind_param('sssii', $title, $content, $cover, $published, $id);
      if(!$stmt->execute()){
        $error = 'Gagal menyimpan: ' . $stmt->error;
      }
    }
  } else {
    $stmt = db()->prepare('INSERT INTO modules (title, content, cover_url, published) VALUES (?,?,?,?)');
    if(!$stmt){
      $error = 'DB error: ' . db()->error;
    } else {
      $stmt->bind_param('sssi', $title, $content, $cover, $published);
      if(!$stmt->execute()){
        $error = 'Gagal menyimpan: ' . $stmt->error;
      } else {
        $id = db()->insert_id;
      }
    }
  }
  if($error===''){
    $rootBase = defined('BASE_PATH') ? BASE_PATH : '/';
    header('Location: ' . $rootBase . '/admin/index.php');
    exit;
  }
}

include __DIR__.'/includes/admin_header.php';
?>

<div class="card" style="max-width:900px; margin:0 auto">
  <div class="card-body">
    <h2 class="title mb-3"><?= esc($page_title) ?></h2>
    <form method="post" enctype="multipart/form-data">
      <?php if(!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
      <?php endif; ?>
      <input type="hidden" name="id" value="<?= (int)($mod['id'] ?? $id) ?>" />
      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input class="form-control" type="text" name="title" value="<?= esc($mod['title'] ?? '') ?>" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Cover (unggah gambar)</label>
        <?php if(!empty($mod['cover_url'])): ?>
          <div class="mb-2">
            <img src="<?= esc($mod['cover_url']) ?>" alt="cover" style="max-width:180px; border-radius:8px; background:#e9eef7; object-fit:cover"/>
            <div class="muted">Cover saat ini. Unggah file baru untuk mengganti.</div>
          </div>
        <?php endif; ?>
        <input class="form-control" type="file" name="cover_file" accept="image/*" />
      </div>

      <div class="mb-3">
        <label class="form-label">Konten</label>
        <textarea class="form-control" name="content" rows="10" required><?= esc($mod['content'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Unggah Foto (boleh lebih dari satu)</label>
        <div id="imageUploadList" class="d-grid gap-2">
          <div class="input-group">
            <input class="form-control" type="file" name="images[]" accept="image/*" />
            <input class="form-control" type="text" name="image_captions[]" placeholder="Keterangan foto (opsional)" />
            <button class="btn btn-outline-secondary" type="button" title="Hapus" onclick="this.parentNode.remove()"><i class="bi bi-x"></i></button>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2">
          <button class="btn btn-sm btn-outline-primary" type="button" id="addImageUpload"><i class="bi bi-plus-circle"></i> Tambah Foto</button>
          <small class="text-muted">Tambah baris untuk setiap foto agar keterangannya sesuai.</small>
        </div>
        <div class="form-text">Maks 8MB per file. Format: JPG, PNG, GIF, WebP.</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Unggah Video (boleh lebih dari satu)</label>
        <div id="videoUploadList" class="d-grid gap-2">
          <div class="input-group">
            <input class="form-control" type="file" name="videos[]" accept="video/mp4,video/webm" />
            <input class="form-control" type="text" name="video_captions[]" placeholder="Keterangan video (opsional)" />
            <button class="btn btn-outline-secondary" type="button" title="Hapus" onclick="this.parentNode.remove()"><i class="bi bi-x"></i></button>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2">
          <button class="btn btn-sm btn-outline-primary" type="button" id="addVideoUpload"><i class="bi bi-plus-circle"></i> Tambah Video</button>
          <small class="text-muted">Tambah baris untuk setiap video agar keterangannya sesuai.</small>
        </div>
        <div class="form-text">Maks 200MB per file. Format: MP4 atau WebM.</div>
      </div>

<script>
  (function(){
    function addGroup(containerId, fileName, accept, captionName){
      var c = document.getElementById(containerId);
      if(!c) return;
      var div = document.createElement('div');
      div.className = 'input-group';
      div.innerHTML = '<input class="form-control" type="file" name="'+fileName+'" accept="'+accept+'" />'+
        '<input class="form-control" type="text" name="'+captionName+'" placeholder="Keterangan (opsional)" />'+
        '<button class="btn btn-outline-secondary" type="button" title="Hapus"><i class="bi bi-x"></i></button>';
      div.querySelector('button').addEventListener('click', function(){ div.remove(); });
      c.appendChild(div);
    }
    document.getElementById('addImageUpload')?.addEventListener('click', function(){
      addGroup('imageUploadList', 'images[]', 'image/*', 'image_captions[]');
    });
    document.getElementById('addVideoUpload')?.addEventListener('click', function(){
      addGroup('videoUploadList', 'videos[]', 'video/mp4,video/webm', 'video_captions[]');
    });
  })();
</script>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="published" name="published" <?= ($mod['published'] ?? 1)? 'checked':'' ?>>
        <label class="form-check-label" for="published">Published</label>
      </div>

      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-secondary" href="/admin/index.php">Kembali</a>
    </form>
  </div>
  <div class="card-footer text-muted">Tip: gunakan Markdown ringan (baris baru) untuk konten.</div>
</div>



<?php include __DIR__.'/includes/admin_footer.php'; ?>
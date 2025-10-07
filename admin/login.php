<?php
session_start();
require __DIR__.'/../config.php';
$page_title = 'Login Admin';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');
  $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username=?');
  $stmt->bind_param('s', $u);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
  if($user && password_verify($p, $user['password_hash'])){
    $_SESSION['admin_id'] = $user['id'];
    $rootBase = defined('BASE_PATH') ? BASE_PATH : '/';
    header('Location: ' . $rootBase . '/admin/index.php');
    exit;
  } else {
    $error = 'Username atau password salah';
  }
}

include __DIR__.'/../includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-circle fs-2 text-primary me-2"></i>
            <h2 class="h4 mb-0">Login Admin</h2>
          </div>

          <?php if(!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
              <?= esc($error) ?>
            </div>
          <?php endif; ?>

          <form method="post" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input class="form-control" type="text" name="username" required autofocus />
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input class="form-control" type="password" name="password" id="pwd" required />
                <button class="btn btn-outline-secondary" type="button" id="togglePwd" title="Tampilkan/Sembunyikan"><i class="bi bi-eye"></i></button>
              </div>
            </div>

            <button class="btn btn-primary w-100" type="submit">Masuk</button>
          </form>

          <div class="text-center mt-3">
            <a class="text-decoration-none" href="<?= $rootBase ?>/index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
          </div>
          <p class="muted small mt-2">Default: admin / admin123</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    const btn = document.getElementById('togglePwd');
    const input = document.getElementById('pwd');
    if(btn && input){
      btn.addEventListener('click', function(){
        input.type = input.type === 'password' ? 'text' : 'password';
        const icon = this.querySelector('i');
        if(icon){ icon.classList.toggle('bi-eye'); icon.classList.toggle('bi-eye-slash'); }
      });
    }
  })();
</script>

<?php include __DIR__.'/../includes/footer.php'; ?>
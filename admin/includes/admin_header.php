<?php if (!isset($page_title)) $page_title = 'Admin Panel'; ?>
<?php require_once __DIR__.'/../../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($page_title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <?php
    if(defined('BASE_PATH')){
      $rootBase = BASE_PATH;
    } else {
      $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '');
      $base = $script ? dirname($script) : '';
      $rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
      if($rootBase === '') $rootBase = '/';
    }
  ?>
  <link rel="stylesheet" href="<?= $rootBase ?>/assets/css/style.css">
</head>
<body class="bg-app-blue">
  <div class="container-fluid">
    <div class="row">
      <aside class="col-12 col-md-3 col-lg-2 admin-sidebar py-3">
        <div class="px-3 d-flex align-items-center mb-3">
          <i class="bi bi-gear-fill text-white me-2"></i>
          <span class="text-white fw-semibold">Admin Panel</span>
        </div>
        <?php $current = basename($_SERVER['PHP_SELF']); ?>
        <ul class="nav nav-pills flex-column px-2 gap-1">
          <li class="nav-item"><a class="nav-link<?= $current==='index.php' ? ' active' : '' ?>" href="<?= $rootBase ?>/admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link<?= $current==='edit.php' ? ' active' : '' ?>" href="<?= $rootBase ?>/admin/edit.php"><i class="bi bi-plus-circle me-2"></i>Buat Modul</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $rootBase ?>/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </aside>
      <main class="col-12 col-md-9 col-lg-10 p-3">
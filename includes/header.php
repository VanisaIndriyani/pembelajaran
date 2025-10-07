<?php if (!isset($page_title)) $page_title = 'Modul Pembelajaran'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($page_title) ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS (support running under subfolder like /modul) -->
  <?php
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '');
    $base = $script ? dirname($script) : '';
    $rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
    if($rootBase === '') $rootBase = '/';
  ?>
  <link rel="stylesheet" href="<?= $rootBase ?>/assets/css/style.css">
</head>
<body class="has-fixed-nav bg-app-blue">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-navy fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?= $rootBase ?>/index.php">Modul Pembelajaran</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
     
       
      </div>
    </div>
  </nav>

  <main class="container py-3">
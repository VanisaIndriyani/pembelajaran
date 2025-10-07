<?php
require __DIR__.'/auth.php';
require_login();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id>0){
  $stmt = db()->prepare('DELETE FROM modules WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}
$base = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
$rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
if($rootBase === '') $rootBase = '/';
header('Location: ' . $rootBase . '/admin/index.php');
exit;
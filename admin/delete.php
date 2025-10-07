<?php
require __DIR__.'/auth.php';
require_login();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id>0){
  $stmt = db()->prepare('DELETE FROM modules WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}
$rootBase = defined('BASE_PATH') ? BASE_PATH : '/';
header('Location: ' . $rootBase . '/admin/index.php');
exit;
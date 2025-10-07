<?php
session_start();
session_destroy();
$rootBase = defined('BASE_PATH') ? BASE_PATH : '/';
header('Location: ' . $rootBase . '/index.php');
exit;
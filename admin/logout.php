<?php
session_start();
session_destroy();
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '');
$base = $script ? dirname($script) : '';
$rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
if($rootBase === '') $rootBase = '/';
header('Location: ' . $rootBase . '/admin/login.php');
exit;
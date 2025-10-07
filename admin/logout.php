<?php
session_start();
session_destroy();
$base = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
$rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
if($rootBase === '') $rootBase = '/';
header('Location: ' . $rootBase . '/admin/login.php');
exit;
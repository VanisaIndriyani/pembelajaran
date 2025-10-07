<?php
session_start();
session_destroy();

// Tentukan base URL otomatis
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
          . "://{$_SERVER['HTTP_HOST']}/modul/";

header("Location: {$baseUrl}index.php");
exit;
?>

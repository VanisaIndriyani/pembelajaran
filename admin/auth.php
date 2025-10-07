<?php
session_start();
require __DIR__.'/../config.php';

function require_login(){
  if(empty($_SESSION['admin_id'])){
    $rootBase = defined('BASE_PATH') ? BASE_PATH : '/';
    header('Location: ' . $rootBase . '/admin/login.php');
    exit;
  }
}
<?php
session_start();
require __DIR__.'/../config.php';

function require_login(){
  if(empty($_SESSION['admin_id'])){
    $base = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
    $rootBase = rtrim(preg_replace('#/admin$#','',$base), '/');
    if($rootBase === '') $rootBase = '/';
    header('Location: ' . $rootBase . '/admin/login.php');
    exit;
  }
}
<?php
session_start();
require __DIR__.'/../config.php';

function require_login(){
  if(empty($_SESSION['admin_id'])){
    header('Location: /admin/login.php');
    exit;
  }
}
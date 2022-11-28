<?php
session_start();

if($_SESSION['user_type'] == 'superadmin'){
    $page = 'superadmin.php';
} else {
    $page = 'login.php';
}

session_unset();
session_destroy();
$res = setcookie('user', '', time() - 3600);
header('LOCATION:'.$page);die();
?>
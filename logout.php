<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    updateUser($_SESSION['user_id'], array('remember_token' => null));
}

if (isset($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = array();
session_destroy();

header('Location: login.php?logout=1');
exit;

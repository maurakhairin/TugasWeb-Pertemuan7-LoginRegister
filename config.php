<?php
define('USERS_FILE', __DIR__ . '/data/users.json');
require_once __DIR__ . '/functions.php';
session_start();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_token'])) {

    $users = getUsers();
    $user  = findUserByEmail($_COOKIE['remember_email'], $users);

    if ($user != null && !empty($user['remember_token'])) {
        $tokenDariCookie = hash('sha256', $_COOKIE['remember_token']);

        if (hash_equals($user['remember_token'], $tokenDariCookie)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
        }
    }
}

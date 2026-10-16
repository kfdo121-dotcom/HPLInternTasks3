<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

$now = time();
$timeout = 1800; // 30 minutes of inactivity

if (isset($_SESSION['last_activity']) &&
    $now - (int) $_SESSION['last_activity'] > $timeout) {
    $_SESSION = [];
    session_regenerate_id(true);
    session_destroy();
    header('Location: /secure_auth/login.php?expired=1');
    exit;
}

$_SESSION['last_activity'] = $now;

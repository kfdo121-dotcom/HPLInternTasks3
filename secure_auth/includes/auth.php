<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    if (!is_string($submitted) || !is_string($stored) ||
        $stored === '' || !hash_equals($stored, $submitted)) {
        http_response_code(403);
        exit('Invalid request token. Please refresh the page and try again.');
    }
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /secure_auth/login.php');
        exit;
    }
}

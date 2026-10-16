<?php
declare(strict_types=1);

$host = '127.0.0.1';
$dbname = 'secure_auth_db';
$username = 'root';
$password = ''; // Default XAMPP local development setting. Change if configured.

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check your XAMPP MySQL settings.');
}

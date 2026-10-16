<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

require_login();

$stmt = $pdo->prepare('SELECT name, email, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | SecureAuth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">SecureAuth</a>
        <form method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="btn btn-outline-light btn-sm">Log out</button>
        </form>
    </div>
</nav>
<main class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3">Welcome, <?= e($user['name']) ?>!</h1>
            <p class="text-secondary">You have successfully logged in to your protected dashboard.</p>
            <hr>
            <h2 class="h5">Account details</h2>
            <p><strong>Name:</strong> <?= e($user['name']) ?></p>
            <p><strong>Email:</strong> <?= e($user['email']) ?></p>
            <p><strong>Registered:</strong> <?= e($user['created_at']) ?></p>
            <div class="alert alert-success mb-0"><strong>Authentication successful.</strong> Your account information is available only to an authenticated session.</div>
        </div>
    </div>
</main>
</body>
</html>

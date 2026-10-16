<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SecureAuth | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5 text-center">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px">
        <div class="card-body p-5">
            <h1 class="display-5 fw-bold">SecureAuth</h1>
            <p class="lead text-secondary">A secure PHP and MySQL user authentication system.</p>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <p>You are logged in as <strong><?= e($_SESSION['user_name']) ?></strong>.</p>
                <a class="btn btn-primary" href="dashboard.php">Open dashboard</a>
            <?php else: ?>
                <a class="btn btn-primary me-2" href="register.php">Create account</a>
                <a class="btn btn-outline-primary" href="login.php">Log in</a>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>

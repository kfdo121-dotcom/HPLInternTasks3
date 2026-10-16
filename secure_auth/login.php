<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!is_string($email) || !is_string($password) ||
        strlen($email) > 190 || strlen($password) > 72) {
        $error = 'Invalid email or password.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION = [
                'user_id' => (int) $user['id'],
                'user_name' => $user['name'],
                'last_activity' => time(),
                'csrf_token' => bin2hex(random_bytes(32)),
            ];

            if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                $update->execute([$newHash, $user['id']]);
            }

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SecureAuth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 450px">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-2">Welcome back</h1>
            <p class="text-secondary">Log in to your account.</p>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Registration successful. You can now log in.</div>
            <?php endif; ?>
            <?php if (isset($_GET['expired'])): ?>
                <div class="alert alert-warning">Your session expired. Please log in again.</div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" maxlength="190" required autocomplete="username" value="<?= e($email) ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" maxlength="72" required autocomplete="current-password">
                </div>
                <button class="btn btn-primary w-100" type="submit">Log in securely</button>
            </form>
            <p class="mt-4 mb-0 text-center">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</div>
</body>
</html>

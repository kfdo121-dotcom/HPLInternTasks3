<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!is_string($password) || !is_string($confirm)) {
        $error = 'Invalid password input.';
    } elseif ($name === '' || strlen($name) > 100) {
        $error = 'Enter a name of up to 100 characters.';
    } elseif (preg_match('/[\x00-\x1F\x7F]/', $name)) {
        $error = 'The name contains invalid characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 12 || strlen($password) > 72) {
        $error = 'Password must contain 12–72 bytes.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Unable to register with that email address.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $insert = $pdo->prepare(
                    'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
                );
                $insert->execute([$name, $email, $hash]);
                header('Location: login.php?registered=1');
                exit;
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') {
                    $error = 'Unable to register with that email address.';
                } else {
                    error_log($ex->getMessage());
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | SecureAuth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 480px">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-2">Create your account</h1>
            <p class="text-secondary">Register for SecureAuth.</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="mb-3">
                    <label class="form-label" for="name">Full name</label>
                    <input class="form-control" id="name" name="name" maxlength="100" required autocomplete="name" value="<?= e($name) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" type="email" id="email" name="email" maxlength="190" required autocomplete="email" value="<?= e($email) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" type="password" id="password" name="password" minlength="12" maxlength="72" required autocomplete="new-password">
                    <div class="form-text">Use at least 12 characters.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="confirm_password">Confirm password</label>
                    <input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="12" maxlength="72" required autocomplete="new-password">
                </div>
                <button class="btn btn-primary w-100" type="submit">Create account</button>
            </form>
            <p class="mt-4 mb-0 text-center">Already registered? <a href="login.php">Log in</a></p>
        </div>
    </div>
</div>
</body>
</html>

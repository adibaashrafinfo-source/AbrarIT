<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_admin()) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_login($email, $password)) {
        redirect('index.php');
    }
    $error = 'Incorrect email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login — Abrar IT</title>
<link rel="icon" href="../assets/img/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <img src="../assets/img/logo.png" alt="Abrar IT" class="auth-logo">
    <h1>Admin Login</h1>

    <?php if (!empty($_GET['created'])): ?>
      <div class="alert alert-success">Account created. Sign in below.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <?= csrf_field() ?>
      <label>Email
        <input type="email" name="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>">
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <button type="submit" class="btn-primary">Sign In</button>
    </form>
  </div>
</body>
</html>

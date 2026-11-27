<?php
/**
 * One-time setup: creates the first admin account.
 * Visit this page once after importing schema.sql, then DELETE this file
 * from your server — leaving it up would let anyone try to use it.
 */
require_once __DIR__ . '/includes/bootstrap.php';

if (any_admin_exists()) {
    http_response_code(403);
    die('An admin account already exists. For security this setup page now refuses to run. Please delete cms/setup.php from your server, and use login.php instead.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? '');

    if ($name === '') $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO admin_users (name, email, password_hash) VALUES (:name, :email, :hash)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        redirect('login.php?created=1');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Admin Account — Abrar IT CMS</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>Create Your Admin Account</h1>
    <p class="auth-sub">This runs once. Afterwards, delete <code>cms/setup.php</code> from your server.</p>

    <?php if ($errors): ?>
      <div class="alert alert-error">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <?= csrf_field() ?>
      <label>Your Name
        <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
      </label>
      <label>Email
        <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
      </label>
      <label>Password
        <input type="password" name="password" required minlength="8">
      </label>
      <label>Confirm Password
        <input type="password" name="confirm" required minlength="8">
      </label>
      <button type="submit" class="btn-primary">Create Account</button>
    </form>
  </div>
</body>
</html>

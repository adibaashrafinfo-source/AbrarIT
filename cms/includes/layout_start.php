<?php
/**
 * Shared admin layout header. Include after require_login() and after
 * setting $pageTitle and $activeNav (one of: dashboard, blog, portfolio, services, messages).
 */
$admin = current_admin();
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Admin') ?> — Abrar IT CMS</title>
<link rel="icon" href="../assets/img/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-page">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="index.php" class="admin-brand">
      <img src="../assets/img/logo.png" alt="Abrar IT">
    </a>
    <nav class="admin-nav">
      <a href="index.php" class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
      <a href="blog.php" class="<?= ($activeNav ?? '') === 'blog' ? 'active' : '' ?>">Blog Posts</a>
      <a href="portfolio.php" class="<?= ($activeNav ?? '') === 'portfolio' ? 'active' : '' ?>">Portfolio</a>
      <a href="services.php" class="<?= ($activeNav ?? '') === 'services' ? 'active' : '' ?>">Services</a>
      <a href="messages.php" class="<?= ($activeNav ?? '') === 'messages' ? 'active' : '' ?>">Messages</a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="../index.html" target="_blank">View Live Site ↗</a>
      <a href="logout.php">Log Out</a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-menu-toggle" aria-label="Toggle menu">☰</button>
      <h1><?= e($pageTitle ?? '') ?></h1>
      <div class="admin-user">Hi, <?= e($admin['name'] ?? '') ?></div>
    </header>

    <div class="admin-content">
      <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endif; ?>

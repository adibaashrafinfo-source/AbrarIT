<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

$counts = [
    'blog' => (int) db()->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn(),
    'portfolio' => (int) db()->query('SELECT COUNT(*) FROM portfolio_items')->fetchColumn(),
    'services' => (int) db()->query('SELECT COUNT(*) FROM services')->fetchColumn(),
    'messages' => (int) db()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn(),
    'unread' => (int) db()->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn(),
];

$recentMessages = db()->query('SELECT id, name, email, message, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();

require __DIR__ . '/includes/layout_start.php';
?>

<div class="stat-grid">
  <div class="stat-card"><b><?= $counts['blog'] ?></b><span>Blog Posts</span></div>
  <div class="stat-card"><b><?= $counts['portfolio'] ?></b><span>Portfolio Items</span></div>
  <div class="stat-card"><b><?= $counts['services'] ?></b><span>Services</span></div>
  <div class="stat-card"><b><?= $counts['unread'] ?> / <?= $counts['messages'] ?></b><span>Unread Messages</span></div>
</div>

<h2>Recent Messages</h2>
<div class="card">
  <?php if (!$recentMessages): ?>
    <div class="empty-state">No contact messages yet.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>From</th><th>Message</th><th>Received</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentMessages as $m): ?>
          <tr>
            <td><strong><?= e($m['name']) ?></strong><br><span style="color:var(--c-ink-faint)"><?= e($m['email']) ?></span></td>
            <td><?= e(mb_strimwidth($m['message'], 0, 70, '…')) ?></td>
            <td><?= e(date('M j, Y', strtotime($m['created_at']))) ?></td>
            <td><span class="badge <?= $m['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= $m['is_read'] ? 'Read' : 'New' ?></span></td>
            <td><a class="btn-outline btn-sm" href="messages.php?view=<?= (int) $m['id'] ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

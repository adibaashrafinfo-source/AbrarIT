<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM contact_messages WHERE id = :id');
    $stmt->execute(['id' => $id]);
    flash_set('Message deleted.');
    redirect('messages.php');
}

$viewId = isset($_GET['view']) ? (int) $_GET['view'] : null;
$viewed = null;
if ($viewId) {
    $stmt = db()->prepare('SELECT * FROM contact_messages WHERE id = :id');
    $stmt->execute(['id' => $viewId]);
    $viewed = $stmt->fetch();
    if ($viewed && !$viewed['is_read']) {
        db()->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = :id')->execute(['id' => $viewId]);
        $viewed['is_read'] = 1;
    }
}

$pageTitle = 'Messages';
$activeNav = 'messages';
$messages = db()->query('SELECT id, name, email, is_read, created_at FROM contact_messages ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/includes/layout_start.php';
?>

<?php if ($viewed): ?>
  <div class="message-view">
    <h2><?= e($viewed['name']) ?></h2>
    <div class="meta">
      <?= e($viewed['email']) ?><?php if ($viewed['phone']): ?> · <?= e($viewed['phone']) ?><?php endif; ?>
      <?php if ($viewed['service_interest']): ?> · Interested in: <?= e($viewed['service_interest']) ?><?php endif; ?>
      · <?= e(date('M j, Y g:ia', strtotime($viewed['created_at']))) ?>
    </div>
    <div class="body"><?= nl2br(e($viewed['message'])) ?></div>
    <div class="form-actions">
      <a href="mailto:<?= e($viewed['email']) ?>" class="btn-outline">Reply by Email</a>
      <form method="post" data-confirm="Delete this message?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int) $viewed['id'] ?>">
        <button type="submit" class="btn-danger">Delete</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="card">
  <?php if (!$messages): ?>
    <div class="empty-state">No contact messages yet. New submissions from the website's Contact page will appear here.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>From</th><th>Received</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($messages as $m): ?>
          <tr>
            <td><strong><?= e($m['name']) ?></strong><br><span style="color:var(--c-ink-faint)"><?= e($m['email']) ?></span></td>
            <td><?= e(date('M j, Y g:ia', strtotime($m['created_at']))) ?></td>
            <td><span class="badge <?= $m['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= $m['is_read'] ? 'Read' : 'New' ?></span></td>
            <td><a class="btn-outline btn-sm" href="messages.php?view=<?= (int) $m['id'] ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

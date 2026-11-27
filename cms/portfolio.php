<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM portfolio_items WHERE id = :id');
    $stmt->execute(['id' => $id]);
    flash_set('Portfolio item deleted.');
    redirect('portfolio.php');
}

$pageTitle = 'Portfolio';
$activeNav = 'portfolio';
$items = db()->query('SELECT id, title, category, project_url, status, sort_order, updated_at FROM portfolio_items ORDER BY sort_order ASC, updated_at DESC')->fetchAll();

require __DIR__ . '/includes/layout_start.php';
?>

<div class="toolbar">
  <div></div>
  <a href="portfolio-form.php" class="btn-primary">+ New Portfolio Item</a>
</div>

<div class="card">
  <?php if (!$items): ?>
    <div class="empty-state">No portfolio items yet. Add your first project.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Order</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($items as $p): ?>
          <tr>
            <td><strong><?= e($p['title']) ?></strong><?php if ($p['project_url']): ?><br><a href="<?= e($p['project_url']) ?>" target="_blank" rel="noopener" style="font-size:.78rem;color:var(--c-ink-faint);">↗ live site</a><?php endif; ?></td>
            <td><?= e($p['category']) ?></td>
            <td><span class="badge badge-<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span></td>
            <td><?= (int) $p['sort_order'] ?></td>
            <td class="actions">
              <a class="btn-outline btn-sm" href="portfolio-form.php?id=<?= (int) $p['id'] ?>">Edit</a>
              <form method="post" data-confirm="Delete this portfolio item?">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button type="submit" class="btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

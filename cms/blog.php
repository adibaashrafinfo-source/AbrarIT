<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM blog_posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    flash_set('Blog post deleted.');
    redirect('blog.php');
}

$pageTitle = 'Blog Posts';
$activeNav = 'blog';
$posts = db()->query('SELECT id, title, category, status, published_at, updated_at FROM blog_posts ORDER BY updated_at DESC')->fetchAll();

require __DIR__ . '/includes/layout_start.php';
?>

<div class="toolbar">
  <div></div>
  <a href="blog-form.php" class="btn-primary">+ New Blog Post</a>
</div>

<div class="card">
  <?php if (!$posts): ?>
    <div class="empty-state">No blog posts yet. Create your first one.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Updated</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td><strong><?= e($p['title']) ?></strong></td>
            <td><?= e($p['category']) ?></td>
            <td><span class="badge badge-<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span></td>
            <td><?= e(date('M j, Y', strtotime($p['updated_at']))) ?></td>
            <td class="actions">
              <a class="btn-outline btn-sm" href="blog-form.php?id=<?= (int) $p['id'] ?>">Edit</a>
              <form method="post" data-confirm="Delete this blog post? This can't be undone.">
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

<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$item = [
    'title' => '', 'category' => 'Branding', 'description' => '', 'thumb_label' => '',
    'cover_gradient_start' => '#F15922', 'cover_gradient_end' => '#1A1715', 'sort_order' => 0, 'status' => 'published',
];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM portfolio_items WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash_set('Portfolio item not found.', 'error');
        redirect('portfolio.php');
    }
    $item = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $item = array_merge($item, [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')) ?: 'Branding',
        'description' => trim((string) ($_POST['description'] ?? '')),
        'thumb_label' => trim((string) ($_POST['thumb_label'] ?? '')),
        'cover_gradient_start' => (string) ($_POST['cover_gradient_start'] ?? '#F15922'),
        'cover_gradient_end' => (string) ($_POST['cover_gradient_end'] ?? '#1A1715'),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft',
    ]);

    if ($item['title'] === '') {
        $errors[] = 'Title is required.';
    }
    if ($item['thumb_label'] === '') {
        $item['thumb_label'] = mb_substr($item['title'], 0, 12);
    }

    if (!$errors) {
        if ($id) {
            $stmt = db()->prepare('UPDATE portfolio_items SET title=:title, category=:category, description=:description,
                thumb_label=:thumb_label, cover_gradient_start=:cgs, cover_gradient_end=:cge, sort_order=:sort_order, status=:status
                WHERE id=:id');
            $stmt->execute([
                'title' => $item['title'], 'category' => $item['category'], 'description' => $item['description'],
                'thumb_label' => $item['thumb_label'], 'cgs' => $item['cover_gradient_start'], 'cge' => $item['cover_gradient_end'],
                'sort_order' => $item['sort_order'], 'status' => $item['status'], 'id' => $id,
            ]);
            flash_set('Portfolio item updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO portfolio_items (title, category, description, thumb_label, cover_gradient_start, cover_gradient_end, sort_order, status)
                VALUES (:title, :category, :description, :thumb_label, :cgs, :cge, :sort_order, :status)');
            $stmt->execute([
                'title' => $item['title'], 'category' => $item['category'], 'description' => $item['description'],
                'thumb_label' => $item['thumb_label'], 'cgs' => $item['cover_gradient_start'], 'cge' => $item['cover_gradient_end'],
                'sort_order' => $item['sort_order'], 'status' => $item['status'],
            ]);
            flash_set('Portfolio item created.');
        }
        redirect('portfolio.php');
    }
}

$pageTitle = $id ? 'Edit Portfolio Item' : 'New Portfolio Item';
$activeNav = 'portfolio';
require __DIR__ . '/includes/layout_start.php';
?>

<?php if ($errors): ?>
  <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="form-card">
  <form method="post">
    <?= csrf_field() ?>
    <div class="field">
      <label>Project Title</label>
      <input type="text" name="title" required value="<?= e($item['title']) ?>">
    </div>
    <div class="field-row">
      <div class="field">
        <label>Category</label>
        <input type="text" name="category" value="<?= e($item['category']) ?>" placeholder="Branding, Web Design, Software, Marketing, Motion…">
      </div>
      <div class="field">
        <label>Thumbnail Label (short word shown on the tile)</label>
        <input type="text" name="thumb_label" maxlength="20" value="<?= e($item['thumb_label']) ?>">
      </div>
    </div>
    <div class="field">
      <label>Description</label>
      <textarea name="description" style="min-height:90px;"><?= e($item['description']) ?></textarea>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Cover Gradient</label>
        <div class="color-row">
          <input type="color" name="cover_gradient_start" value="<?= e($item['cover_gradient_start']) ?>">
          <input type="color" name="cover_gradient_end" value="<?= e($item['cover_gradient_end']) ?>">
        </div>
      </div>
      <div class="field">
        <label>Sort Order (lower shows first)</label>
        <input type="number" name="sort_order" value="<?= (int) $item['sort_order'] ?>">
      </div>
    </div>
    <div class="field">
      <label>Status</label>
      <select name="status">
        <option value="draft" <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $item['status'] === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary"><?= $id ? 'Save Changes' : 'Create Item' ?></button>
      <a href="portfolio.php" class="btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

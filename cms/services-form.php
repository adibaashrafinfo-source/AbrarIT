<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$service = ['name' => '', 'summary' => '', 'starting_price' => '', 'sort_order' => 0, 'status' => 'published'];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM services WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash_set('Service not found.', 'error');
        redirect('services.php');
    }
    $service = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $service = array_merge($service, [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'summary' => trim((string) ($_POST['summary'] ?? '')),
        'starting_price' => trim((string) ($_POST['starting_price'] ?? '')),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft',
    ]);

    if ($service['name'] === '') {
        $errors[] = 'Service name is required.';
    }

    if (!$errors) {
        $baseSlug = slugify($service['name']);
        $slug = unique_slug('services', $baseSlug, $id);

        if ($id) {
            $stmt = db()->prepare('UPDATE services SET name=:name, slug=:slug, summary=:summary, starting_price=:price,
                sort_order=:sort_order, status=:status WHERE id=:id');
            $stmt->execute([
                'name' => $service['name'], 'slug' => $slug, 'summary' => $service['summary'],
                'price' => $service['starting_price'], 'sort_order' => $service['sort_order'],
                'status' => $service['status'], 'id' => $id,
            ]);
            flash_set('Service updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO services (name, slug, summary, starting_price, sort_order, status)
                VALUES (:name, :slug, :summary, :price, :sort_order, :status)');
            $stmt->execute([
                'name' => $service['name'], 'slug' => $slug, 'summary' => $service['summary'],
                'price' => $service['starting_price'], 'sort_order' => $service['sort_order'], 'status' => $service['status'],
            ]);
            flash_set('Service created.');
        }
        redirect('services.php');
    }
}

$pageTitle = $id ? 'Edit Service' : 'New Service';
$activeNav = 'services';
require __DIR__ . '/includes/layout_start.php';
?>

<?php if ($errors): ?>
  <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="form-card">
  <form method="post">
    <?= csrf_field() ?>
    <div class="field">
      <label>Service Name</label>
      <input type="text" name="name" required value="<?= e($service['name']) ?>">
    </div>
    <div class="field">
      <label>Summary</label>
      <textarea name="summary" style="min-height:90px;"><?= e($service['summary']) ?></textarea>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Starting Price Text</label>
        <input type="text" name="starting_price" value="<?= e($service['starting_price']) ?>" placeholder="Starting from ৳500">
      </div>
      <div class="field">
        <label>Sort Order (lower shows first)</label>
        <input type="number" name="sort_order" value="<?= (int) $service['sort_order'] ?>">
      </div>
    </div>
    <div class="field">
      <label>Status</label>
      <select name="status">
        <option value="draft" <?= $service['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $service['status'] === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary"><?= $id ? 'Save Changes' : 'Create Service' ?></button>
      <a href="services.php" class="btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

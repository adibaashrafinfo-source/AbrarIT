<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM services WHERE id = :id');
    $stmt->execute(['id' => $id]);
    flash_set('Service deleted.');
    redirect('services.php');
}

$pageTitle = 'Services';
$activeNav = 'services';
$services = db()->query('SELECT id, name, starting_price, status, sort_order FROM services ORDER BY sort_order ASC')->fetchAll();

require __DIR__ . '/includes/layout_start.php';
?>

<div class="toolbar">
  <div></div>
  <a href="services-form.php" class="btn-primary">+ New Service</a>
</div>

<div class="card">
  <?php if (!$services): ?>
    <div class="empty-state">No services yet.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Name</th><th>Starting Price</th><th>Status</th><th>Order</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><strong><?= e($s['name']) ?></strong></td>
            <td><?= e($s['starting_price']) ?></td>
            <td><span class="badge badge-<?= e($s['status']) ?>"><?= e(ucfirst($s['status'])) ?></span></td>
            <td><?= (int) $s['sort_order'] ?></td>
            <td class="actions">
              <a class="btn-outline btn-sm" href="services-form.php?id=<?= (int) $s['id'] ?>">Edit</a>
              <form method="post" data-confirm="Delete this service?">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
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

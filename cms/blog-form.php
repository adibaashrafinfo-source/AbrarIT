<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$post = [
    'title' => '', 'category' => 'General', 'excerpt' => '', 'content' => '',
    'cover_gradient_start' => '#F15922', 'cover_gradient_end' => '#1A1715', 'status' => 'draft',
];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM blog_posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash_set('Blog post not found.', 'error');
        redirect('blog.php');
    }
    $post = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $post = array_merge($post, [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')) ?: 'General',
        'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
        'content' => (string) ($_POST['content'] ?? ''),
        'cover_gradient_start' => (string) ($_POST['cover_gradient_start'] ?? '#F15922'),
        'cover_gradient_end' => (string) ($_POST['cover_gradient_end'] ?? '#1A1715'),
        'status' => in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft',
    ]);

    if ($post['title'] === '') {
        $errors[] = 'Title is required.';
    }

    if (!$errors) {
        $baseSlug = slugify($post['title']);
        $slug = unique_slug('blog_posts', $baseSlug, $id);
        $publishedAt = ($post['status'] === 'published') ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : null;

        if ($id) {
            $stmt = db()->prepare('UPDATE blog_posts SET title=:title, slug=:slug, category=:category, excerpt=:excerpt,
                content=:content, cover_gradient_start=:cgs, cover_gradient_end=:cge, status=:status, published_at=:published_at
                WHERE id=:id');
            $stmt->execute([
                'title' => $post['title'], 'slug' => $slug, 'category' => $post['category'], 'excerpt' => $post['excerpt'],
                'content' => $post['content'], 'cgs' => $post['cover_gradient_start'], 'cge' => $post['cover_gradient_end'],
                'status' => $post['status'], 'published_at' => $publishedAt, 'id' => $id,
            ]);
            flash_set('Blog post updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO blog_posts (title, slug, category, excerpt, content, cover_gradient_start, cover_gradient_end, status, published_at)
                VALUES (:title, :slug, :category, :excerpt, :content, :cgs, :cge, :status, :published_at)');
            $stmt->execute([
                'title' => $post['title'], 'slug' => $slug, 'category' => $post['category'], 'excerpt' => $post['excerpt'],
                'content' => $post['content'], 'cgs' => $post['cover_gradient_start'], 'cge' => $post['cover_gradient_end'],
                'status' => $post['status'], 'published_at' => $publishedAt,
            ]);
            flash_set('Blog post created.');
        }
        redirect('blog.php');
    }
}

$pageTitle = $id ? 'Edit Blog Post' : 'New Blog Post';
$activeNav = 'blog';
require __DIR__ . '/includes/layout_start.php';
?>

<?php if ($errors): ?>
  <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="form-card">
  <form method="post">
    <?= csrf_field() ?>
    <div class="field">
      <label>Title</label>
      <input type="text" name="title" required value="<?= e($post['title']) ?>">
    </div>
    <div class="field-row">
      <div class="field">
        <label>Category</label>
        <input type="text" name="category" value="<?= e($post['category']) ?>">
      </div>
      <div class="field">
        <label>Status</label>
        <select name="status">
          <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
          <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
      </div>
    </div>
    <div class="field">
      <label>Excerpt (short summary shown on the Blog page)</label>
      <textarea name="excerpt" style="min-height:80px;"><?= e($post['excerpt']) ?></textarea>
    </div>
    <div class="field">
      <label>Content</label>
      <textarea name="content"><?= e($post['content']) ?></textarea>
      <div class="field-hint">Plain text or basic HTML (e.g. &lt;p&gt;, &lt;strong&gt;). It is shown as-is on the article page.</div>
    </div>
    <div class="field">
      <label>Cover Gradient</label>
      <div class="color-row">
        <input type="color" name="cover_gradient_start" value="<?= e($post['cover_gradient_start']) ?>">
        <input type="color" name="cover_gradient_end" value="<?= e($post['cover_gradient_end']) ?>">
        <span class="field-hint">Used for the article's thumbnail on the Blog page.</span>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary"><?= $id ? 'Save Changes' : 'Create Post' ?></button>
      <a href="blog.php" class="btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>

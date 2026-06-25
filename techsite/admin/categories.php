<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Categories';
$active_admin_nav = 'categories';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = slugify($_POST['slug'] ?? $name);
    $description = trim($_POST['description'] ?? '');
    if ($name && $slug) {
        try {
            $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?,?,?)")->execute([$name, $slug, $description]);
        } catch (PDOException $e) {
            $error = 'A category with that slug already exists.';
        }
    }
}

if (isset($_GET['delete'])) {
    $cid = (int)$_GET['delete'];
    $pdo->prepare("UPDATE posts SET category_id = NULL WHERE category_id = ?")->execute([$cid]);
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$cid]);
    redirect('/admin/categories.php');
}

$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS post_count FROM categories c
    LEFT JOIN posts p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name
")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<div class="admin-card">
  <strong>Add a Category</strong>
  <form method="post" action="" style="display:grid; grid-template-columns:1fr 1fr 2fr auto; gap:12px; align-items:end; margin-top:14px;">
    <div class="form-group" style="margin:0;"><label>Name</label><input type="text" name="name" required></div>
    <div class="form-group" style="margin:0;"><label>Slug (optional)</label><input type="text" name="slug" placeholder="auto from name"></div>
    <div class="form-group" style="margin:0;"><label>Description</label><input type="text" name="description"></div>
    <button type="submit" class="btn btn-primary">Add</button>
  </form>
</div>

<div class="admin-card">
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Slug</th><th>Description</th><th>Posts</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($categories as $c): ?>
      <tr>
        <td><strong><?= e($c['name']) ?></strong></td>
        <td><code><?= e($c['slug']) ?></code></td>
        <td><?= e($c['description']) ?></td>
        <td><?= (int)$c['post_count'] ?></td>
        <td class="row-actions"><a href="?delete=<?= (int)$c['id'] ?>" class="delete" data-confirm="Delete this category? Posts in it will become uncategorized.">Delete</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

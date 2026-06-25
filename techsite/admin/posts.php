<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$is_admin = current_user_role() === 'admin';
$user_id = $_SESSION['user_id'];
$page_title = $is_admin ? 'All Posts' : 'My Posts';
$active_admin_nav = 'posts';

// handle delete
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    if ($is_admin) {
        $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$pid]);
    } else {
        $pdo->prepare("DELETE FROM posts WHERE id = ? AND author_id = ?")->execute([$pid, $user_id]);
    }
    redirect('/admin/posts.php');
}

$status_filter = $_GET['status'] ?? '';
$where = [];
$params = [];
if (!$is_admin) { $where[] = "p.author_id = ?"; $params[] = $user_id; }
if ($status_filter) { $where[] = "p.status = ?"; $params[] = $status_filter; }
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT p.*, u.name AS author_name, c.name AS cat_name
    FROM posts p LEFT JOIN users u ON p.author_id = u.id LEFT JOIN categories c ON p.category_id = c.id
    $where_sql ORDER BY p.created_at DESC
");
$stmt->execute($params);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
    <div style="display:flex; gap:8px;">
      <a href="?status=" class="btn btn-sm <?= $status_filter==='' ? 'btn-primary':'btn-outline' ?>">All</a>
      <a href="?status=published" class="btn btn-sm <?= $status_filter==='published' ? 'btn-primary':'btn-outline' ?>">Published</a>
      <a href="?status=draft" class="btn btn-sm <?= $status_filter==='draft' ? 'btn-primary':'btn-outline' ?>">Drafts</a>
      <a href="?status=pending" class="btn btn-sm <?= $status_filter==='pending' ? 'btn-primary':'btn-outline' ?>">Pending</a>
    </div>
    <a href="<?= SITE_URL ?>/admin/post-edit.php" class="btn btn-sm btn-primary">+ New Post</a>
  </div>

  <?php if ($posts): ?>
  <table class="admin-table">
    <thead><tr><th>Title</th><th>Category</th><?php if ($is_admin): ?><th>Author</th><?php endif; ?><th>Status</th><th>Views</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><strong><?= e($p['title']) ?></strong><?php if($p['is_sponsored']):?> <span class="tag-sponsored">Guest</span><?php endif; ?></td>
        <td><?= e($p['cat_name'] ?? '—') ?></td>
        <?php if ($is_admin): ?><td><?= e($p['author_name']) ?></td><?php endif; ?>
        <td><span class="status-pill status-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
        <td><?= (int)$p['views'] ?></td>
        <td><?= time_ago($p['created_at']) ?></td>
        <td class="row-actions">
          <a href="<?= SITE_URL ?>/admin/post-edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
          <?php if ($p['status']==='published'): ?><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>" target="_blank">View</a><?php endif; ?>
          <a href="?delete=<?= (int)$p['id'] ?>&status=<?= e($status_filter) ?>" class="delete" data-confirm="Delete this post permanently?">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="empty-state"><div class="icon">📝</div><p>No posts found. <a href="<?= SITE_URL ?>/admin/post-edit.php">Create one &rarr;</a></p></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

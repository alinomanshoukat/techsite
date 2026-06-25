<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$is_admin = current_user_role() === 'admin';
$user_id = $_SESSION['user_id'];
$page_title = 'Dashboard';
$active_admin_nav = 'dashboard';

if ($is_admin) {
    $total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $published = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
    $drafts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetchColumn();
    $total_views = $pdo->query("SELECT COALESCE(SUM(views),0) FROM posts")->fetchColumn();
    $new_leads = $pdo->query("SELECT
        (SELECT COUNT(*) FROM contact_messages WHERE is_read=0) +
        (SELECT COUNT(*) FROM guest_post_submissions WHERE status='new') +
        (SELECT COUNT(*) FROM link_insertion_requests WHERE status='new')
    ")->fetchColumn();
    $recent_posts = $pdo->query("
        SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC LIMIT 6
    ")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ?");
    $stmt->execute([$user_id]);
    $total_posts = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ? AND status='published'");
    $stmt->execute([$user_id]);
    $published = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ? AND status='draft'");
    $stmt->execute([$user_id]);
    $drafts = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(views),0) FROM posts WHERE author_id = ?");
    $stmt->execute([$user_id]);
    $total_views = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.author_id = ? ORDER BY p.created_at DESC LIMIT 6");
    $stmt->execute([$user_id]);
    $recent_posts = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="stat-cards">
  <div class="stat-card"><div class="num"><?= (int)$total_posts ?></div><div class="label"><?= $is_admin ? 'Total Posts' : 'My Posts' ?></div></div>
  <div class="stat-card"><div class="num"><?= (int)$published ?></div><div class="label">Published</div></div>
  <div class="stat-card"><div class="num"><?= (int)$drafts ?></div><div class="label">Drafts</div></div>
  <?php if ($is_admin): ?>
  <div class="stat-card"><div class="num"><?= (int)$new_leads ?></div><div class="label">New Leads</div></div>
  <?php else: ?>
  <div class="stat-card"><div class="num"><?= (int)$total_views ?></div><div class="label">Total Views</div></div>
  <?php endif; ?>
</div>

<?php if ($is_admin): ?>
<div class="admin-card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
    <strong>New guest post pitches, link requests, and messages need review.</strong>
    <a href="<?= SITE_URL ?>/admin/leads.php" class="btn btn-sm btn-outline">Review Leads &rarr;</a>
  </div>
</div>
<?php endif; ?>

<div class="admin-card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <strong>Recent Posts</strong>
    <a href="<?= SITE_URL ?>/admin/post-edit.php" class="btn btn-sm btn-primary">+ New Post</a>
  </div>

  <?php if ($recent_posts): ?>
  <table class="admin-table">
    <thead><tr><th>Title</th><?php if ($is_admin): ?><th>Author</th><?php endif; ?><th>Status</th><th>Views</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recent_posts as $p): ?>
      <tr>
        <td><strong><?= e($p['title']) ?></strong></td>
        <?php if ($is_admin): ?><td><?= e($p['author_name']) ?></td><?php endif; ?>
        <td><span class="status-pill status-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
        <td><?= (int)$p['views'] ?></td>
        <td><?= time_ago($p['created_at']) ?></td>
        <td class="row-actions"><a href="<?= SITE_URL ?>/admin/post-edit.php?id=<?= (int)$p['id'] ?>">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="empty-state"><p>No posts yet. <a href="<?= SITE_URL ?>/admin/post-edit.php">Write your first one &rarr;</a></p></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

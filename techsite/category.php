<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$cat_stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$cat_stmt->execute([$slug]);
$category = $cat_stmt->fetch();

if (!$category) {
    redirect('/blog.php');
}

$per_page = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE status='published' AND category_id = ?");
$count_stmt->execute([$category['id']]);
$total = $count_stmt->fetchColumn();
$total_pages = (int)ceil($total / $per_page);

$stmt = $pdo->prepare("
    SELECT p.*, u.name AS author_name
    FROM posts p LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published' AND p.category_id = :cat_id
    ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':cat_id', $category['id'], PDO::PARAM_INT);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$page_title = $category['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a> / Categories</div>
    <h1><?= e($category['name']) ?></h1>
    <p><?= e($category['description']) ?></p>
  </div>
</div>

<div class="container">
  <?php if ($posts): ?>
  <div class="post-river" style="margin-top:36px;">
    <?php foreach ($posts as $p): ?>
    <article class="post-card">
      <div class="media"><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><img src="<?= SITE_URL ?>/<?= e($p['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($p['title']) ?>"></a></div>
      <h3><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
      <p><?= e($p['excerpt'] ?: excerpt_from_content($p['content'], 100)) ?></p>
      <div class="meta-line"><span><?= e($p['author_name']) ?></span><span class="dot"></span><span><?= time_ago($p['published_at']) ?></span></div>
    </article>
    <?php endforeach; ?>
  </div>
  <?= paginate_links($page, $total_pages, SITE_URL . '/category.php?slug=' . e($category['slug'])) ?>
  <?php else: ?>
    <div class="empty-state"><div class="icon">📂</div><p>No articles in this category yet.</p></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

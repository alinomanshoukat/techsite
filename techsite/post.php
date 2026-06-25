<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, u.name AS author_name, u.bio AS author_bio, u.avatar AS author_avatar
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.slug = ? AND p.status = 'published'
    LIMIT 1
");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $page_title = 'Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container"><div class="empty-state"><div class="icon">🔍</div><h2>Article not found</h2><p>This post may have been moved or unpublished.</p><a href="' . SITE_URL . '/blog.php" class="btn btn-primary">Browse all articles</a></div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// increment views (simple counter)
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

// related posts, same category
$related = $pdo->prepare("
    SELECT title, slug, featured_image FROM posts
    WHERE category_id = ? AND status='published' AND id != ?
    ORDER BY published_at DESC LIMIT 3
");
$related->execute([$post['category_id'], $post['id']]);
$related_posts = $related->fetchAll();

$page_title = $post['title'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div class="breadcrumb" style="margin-top:24px;">
    <a href="<?= SITE_URL ?>/index.php">Home</a> /
    <a href="<?= SITE_URL ?>/category.php?slug=<?= e($post['cat_slug']) ?>"><?= e($post['cat_name']) ?></a>
  </div>
</div>

<div class="post-header">
  <div class="meta-line">
    <span class="eyebrow"><?= e($post['cat_name'] ?? 'General') ?></span>
    <?php if ($post['is_sponsored']): ?><span class="tag-sponsored">Guest Post</span><?php endif; ?>
  </div>
  <h1><?= e($post['title']) ?></h1>
  <div class="meta-line" style="margin-bottom:8px;">
    <span>By <strong><?= e($post['author_name']) ?></strong></span>
    <span class="dot"></span>
    <span><?= format_date($post['published_at']) ?></span>
    <span class="dot"></span>
    <span><?= (int)$post['views'] ?> views</span>
  </div>
</div>

<?php if ($post['featured_image']): ?>
<div class="post-hero-image">
  <img src="<?= SITE_URL ?>/<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>">
</div>
<?php endif; ?>

<div class="post-body">
  <?= $post['content'] ?>

  <div class="author-box">
    <img src="<?= SITE_URL ?>/<?= e($post['author_avatar'] ?: 'assets/images/avatar-default.png') ?>" alt="<?= e($post['author_name']) ?>">
    <div>
      <div class="name"><?= e($post['author_name']) ?></div>
      <div class="bio"><?= e($post['author_bio']) ?></div>
    </div>
  </div>

  <?php if ($related_posts): ?>
  <div class="section-head" style="margin-top:10px;"><h2>More in <?= e($post['cat_name']) ?></h2></div>
  <div class="post-river" style="grid-template-columns:repeat(3,1fr);">
    <?php foreach ($related_posts as $r): ?>
    <article class="post-card">
      <div class="media"><a href="<?= SITE_URL ?>/post.php?slug=<?= e($r['slug']) ?>"><img src="<?= SITE_URL ?>/<?= e($r['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($r['title']) ?>"></a></div>
      <h3 style="font-size:16px;"><a href="<?= SITE_URL ?>/post.php?slug=<?= e($r['slug']) ?>"><?= e($r['title']) ?></a></h3>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

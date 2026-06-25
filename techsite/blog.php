<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Blog';
$active_nav = 'blog';

$per_page = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$total = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$total_pages = (int)ceil($total / $per_page);

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, u.name AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published'
    ORDER BY p.published_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>All Articles</h1>
    <p>Every story we've published, newest first. Browse by category or use search to find something specific.</p>
  </div>
</div>

<div class="container">
  <div class="layout-with-sidebar">
    <div>
      <?php if ($posts): ?>
      <div class="post-river" style="margin-top:36px;">
        <?php foreach ($posts as $p): ?>
        <article class="post-card">
          <div class="media">
            <a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>">
              <img src="<?= SITE_URL ?>/<?= e($p['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($p['title']) ?>">
            </a>
          </div>
          <div class="meta-line"><span class="eyebrow"><?= e($p['cat_name'] ?? 'General') ?></span><?php if ($p['is_sponsored']): ?><span class="tag-sponsored">Guest Post</span><?php endif; ?></div>
          <h3><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
          <p><?= e($p['excerpt'] ?: excerpt_from_content($p['content'], 100)) ?></p>
          <div class="meta-line"><span><?= e($p['author_name']) ?></span><span class="dot"></span><span><?= time_ago($p['published_at']) ?></span></div>
        </article>
        <?php endforeach; ?>
      </div>
      <?= paginate_links($page, $total_pages, SITE_URL . '/blog.php?x=1') ?>
      <?php else: ?>
        <div class="empty-state"><div class="icon">📰</div><p>No articles published yet.</p></div>
      <?php endif; ?>
    </div>

    <aside>
      <div class="sidebar-box">
        <h4>Categories</h4>
        <ul class="cat-list">
          <?php foreach ($categories as $c): ?>
          <li><a href="<?= SITE_URL ?>/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="sidebar-box cta">
        <h4>Get Featured</h4>
        <p>Submit a guest post or get a backlink placed in one of our existing articles.</p>
        <a href="<?= SITE_URL ?>/guest-post.php" class="btn btn-primary btn-block">Write For Us</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Home';
$active_nav = 'home';

// Featured post = latest published
$featured = $pdo->query("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, u.name AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published'
    ORDER BY p.published_at DESC
    LIMIT 1
")->fetch();

// Latest posts (excluding featured), newest first
$latest = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, u.name AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published' AND p.id != ?
    ORDER BY p.published_at DESC
    LIMIT 6
");
$latest->execute([$featured['id'] ?? 0]);
$latest_posts = $latest->fetchAll();

$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS post_count
    FROM categories c
    LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
    GROUP BY c.id ORDER BY c.name
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

  <?php if ($featured): ?>
  <section class="featured-post">
    <div>
      <div class="meta-line">
        <span class="eyebrow"><?= e($featured['cat_name'] ?? 'General') ?></span>
        <?php if ($featured['is_sponsored']): ?><span class="tag-sponsored">Guest Post</span><?php endif; ?>
      </div>
      <h1><a href="<?= SITE_URL ?>/post.php?slug=<?= e($featured['slug']) ?>" style="color:inherit;"><?= e($featured['title']) ?></a></h1>
      <p class="excerpt"><?= e($featured['excerpt'] ?: excerpt_from_content($featured['content'])) ?></p>
      <div class="meta-line" style="margin-bottom:18px;">
        <span>By <?= e($featured['author_name']) ?></span>
        <span class="dot"></span>
        <span><?= format_date($featured['published_at']) ?></span>
        <span class="dot"></span>
        <span><?= (int)$featured['views'] ?> views</span>
      </div>
      <a href="<?= SITE_URL ?>/post.php?slug=<?= e($featured['slug']) ?>" class="btn btn-primary">Read Article</a>
    </div>
    <div class="media">
      <img src="<?= SITE_URL ?>/<?= e($featured['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($featured['title']) ?>">
    </div>
  </section>
  <?php else: ?>
  <div class="empty-state"><div class="icon">📰</div><p>No published articles yet. Log in to the admin panel to publish your first post.</p></div>
  <?php endif; ?>

  <div class="layout-with-sidebar">
    <div>
      <div class="section-head">
        <h2>Latest Articles</h2>
        <a href="<?= SITE_URL ?>/blog.php">View all &rarr;</a>
      </div>

      <?php if ($latest_posts): ?>
      <div class="post-river">
        <?php foreach ($latest_posts as $p): ?>
        <article class="post-card">
          <div class="media">
            <a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>">
              <img src="<?= SITE_URL ?>/<?= e($p['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($p['title']) ?>">
            </a>
          </div>
          <span class="eyebrow"><?= e($p['cat_name'] ?? 'General') ?></span>
          <h3><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
          <p><?= e($p['excerpt'] ?: excerpt_from_content($p['content'], 100)) ?></p>
          <div class="meta-line"><span><?= e($p['author_name']) ?></span><span class="dot"></span><span><?= time_ago($p['published_at']) ?></span></div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <div class="empty-state"><p>More articles will show up here once they're published.</p></div>
      <?php endif; ?>
    </div>

    <aside>
      <div class="sidebar-box">
        <h4>Categories</h4>
        <ul class="cat-list">
          <?php foreach ($categories as $c): ?>
          <li><a href="<?= SITE_URL ?>/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a><span class="cat-count"><?= (int)$c['post_count'] ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="sidebar-box cta">
        <h4>Get Featured</h4>
        <p>Have a tech brand, tool, or story to share? We accept guest posts and link insertions in existing articles.</p>
        <a href="<?= SITE_URL ?>/guest-post.php" class="btn btn-primary btn-block" style="margin-bottom:8px;">Submit a Guest Post</a>
        <a href="<?= SITE_URL ?>/link-insertion.php" class="btn btn-outline btn-block" style="border-color:var(--grey-light); color:var(--paper-warm);">Get a Link Inserted</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

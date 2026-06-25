<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS cat_name, u.name AS author_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.status = 'published'
        AND (MATCH(p.title, p.excerpt, p.content) AGAINST (? IN NATURAL LANGUAGE MODE)
             OR p.title LIKE ?)
        ORDER BY p.published_at DESC
        LIMIT 30
    ");
    $stmt->execute([$q, '%' . $q . '%']);
    $results = $stmt->fetchAll();
}

$page_title = 'Search results for "' . $q . '"';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Search Results</h1>
    <p><?= count($results) ?> result<?= count($results) === 1 ? '' : 's' ?> for "<strong><?= e($q) ?></strong>"</p>
  </div>
</div>

<div class="container">
  <?php if ($results): ?>
  <div class="post-river" style="margin-top:36px;">
    <?php foreach ($results as $p): ?>
    <article class="post-card">
      <div class="media"><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><img src="<?= SITE_URL ?>/<?= e($p['featured_image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= e($p['title']) ?>"></a></div>
      <span class="eyebrow"><?= e($p['cat_name'] ?? 'General') ?></span>
      <h3><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
      <p><?= e($p['excerpt'] ?: excerpt_from_content($p['content'], 100)) ?></p>
    </article>
    <?php endforeach; ?>
  </div>
  <?php elseif ($q !== ''): ?>
    <div class="empty-state"><div class="icon">🔍</div><p>No articles matched "<?= e($q) ?>". Try a different keyword.</p></div>
  <?php else: ?>
    <div class="empty-state"><p>Type something into the search bar above to find articles.</p></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * header.php — shared header for all public-facing pages
 * Expects $pdo and optionally $page_title, $active_nav to be set before include.
 */
$site_name = get_setting($pdo, 'site_name', 'TechWire');
$tagline = get_setting($pdo, 'site_tagline', 'Signal from the noise in tech');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? e($page_title) . ' — ' : '' ?><?= e($site_name) ?></title>
<meta name="description" content="<?= e($tagline) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="masthead-top">
  <div class="container">
    <span>We publish daily on AI, web dev, security &amp; startups.</span>
    <span><a href="<?= SITE_URL ?>/guest-post.php">Write for us</a> &nbsp;|&nbsp; <a href="<?= SITE_URL ?>/link-insertion.php">Link insertion</a></span>
  </div>
</div>

<header class="masthead">
  <div class="container">
    <div>
      <a href="<?= SITE_URL ?>/index.php" style="text-decoration:none;">
        <div class="logo"><?= e(substr($site_name,0,4)) ?><span><?= e(substr($site_name,4)) ?></span></div>
      </a>
      <div class="tagline"><?= e($tagline) ?></div>
    </div>
  </div>
</header>

<nav class="main-nav">
  <div class="container">
    <button class="nav-toggle" aria-label="Toggle menu">&#9776;</button>
    <ul class="nav-links">
      <li><a href="<?= SITE_URL ?>/index.php" class="<?= ($active_nav ?? '') === 'home' ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= SITE_URL ?>/blog.php" class="<?= ($active_nav ?? '') === 'blog' ? 'active' : '' ?>">Blog</a></li>
      <li><a href="<?= SITE_URL ?>/guest-post.php" class="<?= ($active_nav ?? '') === 'guest' ? 'active' : '' ?>">Guest Post</a></li>
      <li><a href="<?= SITE_URL ?>/link-insertion.php" class="<?= ($active_nav ?? '') === 'link' ? 'active' : '' ?>">Link Insertion</a></li>
      <li><a href="<?= SITE_URL ?>/about.php" class="<?= ($active_nav ?? '') === 'about' ? 'active' : '' ?>">About</a></li>
      <li><a href="<?= SITE_URL ?>/contact.php" class="<?= ($active_nav ?? '') === 'contact' ? 'active' : '' ?>">Contact</a></li>
    </ul>
    <form class="nav-search-form" action="<?= SITE_URL ?>/search.php" method="get">
      <input type="search" name="q" placeholder="Search articles..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
      <button type="submit" aria-label="Search">&#128269;</button>
    </form>
  </div>
</nav>

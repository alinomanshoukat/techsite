<?php
/**
 * admin-header.php
 * Expects $pdo, $page_title, $active_admin_nav to be set.
 * Call require_login() before including this.
 */
$site_name = get_setting($pdo, 'site_name', 'TechWire');
$is_admin = current_user_role() === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Admin') ?> — <?= e($site_name) ?> Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">

  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="brand">
      <?= e($site_name) ?>
      <span class="role-badge"><?= $is_admin ? 'Administrator' : 'Author' ?></span>
    </div>
    <nav class="admin-nav">
      <a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= ($active_admin_nav ?? '') === 'dashboard' ? 'active' : '' ?>">&#128202; Dashboard</a>
      <a href="<?= SITE_URL ?>/admin/posts.php" class="<?= ($active_admin_nav ?? '') === 'posts' ? 'active' : '' ?>">&#128221; <?= $is_admin ? 'All Posts' : 'My Posts' ?></a>
      <a href="<?= SITE_URL ?>/admin/post-edit.php" class="<?= ($active_admin_nav ?? '') === 'new-post' ? 'active' : '' ?>">&#10133; New Post</a>
      <?php if ($is_admin): ?>
      <a href="<?= SITE_URL ?>/admin/categories.php" class="<?= ($active_admin_nav ?? '') === 'categories' ? 'active' : '' ?>">&#128193; Categories</a>
      <a href="<?= SITE_URL ?>/admin/leads.php" class="<?= ($active_admin_nav ?? '') === 'leads' ? 'active' : '' ?>">&#128231; Leads &amp; Messages</a>
      <a href="<?= SITE_URL ?>/admin/users.php" class="<?= ($active_admin_nav ?? '') === 'users' ? 'active' : '' ?>">&#128101; Authors &amp; Access</a>
      <a href="<?= SITE_URL ?>/admin/settings.php" class="<?= ($active_admin_nav ?? '') === 'settings' ? 'active' : '' ?>">&#9881; Site Settings</a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/admin/profile.php" class="<?= ($active_admin_nav ?? '') === 'profile' ? 'active' : '' ?>">&#128100; My Profile</a>
      <a href="<?= SITE_URL ?>/index.php" target="_blank">&#8599; View Site</a>
      <a href="<?= SITE_URL ?>/admin/logout.php">&#8594; Log Out</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <div style="display:flex; align-items:center; gap:14px;">
        <button id="sidebar-toggle" class="nav-toggle" style="display:none;" aria-label="Toggle menu">&#9776;</button>
        <h1><?= e($page_title ?? 'Dashboard') ?></h1>
      </div>
      <div style="font-size:13px; color:var(--grey);">Logged in as <strong><?= e($_SESSION['user_name']) ?></strong></div>
    </div>

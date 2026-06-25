<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'About Us';
$active_nav = 'about';

$team = $pdo->query("SELECT name, bio, avatar, role FROM users WHERE status='active' ORDER BY role DESC")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>About <?= e($site_name) ?></h1>
    <p>We cover the tools, companies, and ideas shaping technology — and we're upfront about how the site sustains itself.</p>
  </div>
</div>

<div class="container" style="padding:40px 0 60px;">
  <div style="max-width:720px;">
    <h2>What we do</h2>
    <p style="color:var(--grey); font-size:16px; line-height:1.75;">
      <?= e($site_name) ?> publishes daily articles on artificial intelligence, web development, cybersecurity,
      gadgets, and the startup world. Our editorial team writes original analysis and reporting, and we keep a
      clear line between that work and anything sponsored.
    </p>
    <h2 style="margin-top:36px;">How we fund independent writing</h2>
    <p style="color:var(--grey); font-size:16px; line-height:1.75;">
      Alongside our own reporting, we offer two paid services to brands and writers: <a href="<?= SITE_URL ?>/guest-post.php">guest posting</a>
      and <a href="<?= SITE_URL ?>/link-insertion.php">link insertion</a> in relevant existing articles. Every sponsored
      piece is clearly labeled "Guest Post" so readers always know what they're reading.
    </p>
  </div>

  <div class="section-head" style="margin-top:56px;"><h2>Our Team</h2></div>
  <div class="post-river" style="grid-template-columns:repeat(3,1fr);">
    <?php foreach ($team as $t): ?>
    <div class="post-card" style="text-align:center;">
      <img src="<?= SITE_URL ?>/<?= e($t['avatar'] ?: 'assets/images/avatar-default.png') ?>" alt="<?= e($t['name']) ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin:0 auto 14px;background:var(--paper-warm);">
      <h3 style="font-size:17px;"><?= e($t['name']) ?></h3>
      <span class="eyebrow" style="display:block;margin-bottom:8px;"><?= e(ucfirst($t['role'])) ?></span>
      <p><?= e($t['bio']) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

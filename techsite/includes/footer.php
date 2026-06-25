<?php
$cats_for_footer = $pdo->query("SELECT name, slug FROM categories ORDER BY name LIMIT 5")->fetchAll();
?>
<footer>
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><?= e($site_name) ?></div>
        <p style="font-size:14px; color:var(--grey-light); max-width:320px;"><?= e($tagline) ?>. Independent tech coverage plus paid placement options for brands and writers.</p>
      </div>
      <div>
        <h5>Explore</h5>
        <ul>
          <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
          <li><a href="<?= SITE_URL ?>/blog.php">All Articles</a></li>
          <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
          <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
        </ul>
      </div>
      <div>
        <h5>Categories</h5>
        <ul>
          <?php foreach ($cats_for_footer as $c): ?>
          <li><a href="<?= SITE_URL ?>/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h5>Work With Us</h5>
        <ul>
          <li><a href="<?= SITE_URL ?>/guest-post.php">Guest Post Guidelines</a></li>
          <li><a href="<?= SITE_URL ?>/link-insertion.php">Link Insertion Rates</a></li>
          <li><a href="<?= SITE_URL ?>/admin/login.php">Admin Login</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= e($site_name) ?>. All rights reserved.</span>
      <span>Template by you — built with care.</span>
    </div>
  </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>

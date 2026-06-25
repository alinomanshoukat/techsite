<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Link Insertion';
$active_nav = 'link';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $target = trim($_POST['target_post_url'] ?? '');
    $your_url = trim($_POST['your_url'] ?? '');
    $anchor = trim($_POST['anchor_text'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$your_url || !$anchor) {
        $error = 'Please fill in your name, a valid email, your URL, and the anchor text.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO link_insertion_requests (name, email, target_post_url, your_url, anchor_text, message) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $email, $target, $your_url, $anchor, $message]);
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Link Insertion Service</h1>
    <p>Get a relevant, contextual backlink placed inside one of our existing, already-indexed articles — no new content required.</p>
  </div>
</div>

<div class="container" style="padding:40px 0 60px;">
  <div class="layout-with-sidebar">
    <div>
      <div class="rate-card" style="margin-bottom:36px;">
        <div class="rate-card-row">
          <div><span class="label">Service</span><span class="value">Link Insertion (existing post)</span></div>
          <div class="price"><?= e(get_setting($pdo, 'link_insertion_price', '$45')) ?></div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Turnaround</span><span class="value">Time to live placement</span></div>
          <div class="price"><?= e(get_setting($pdo, 'turnaround_time', '3–5 days')) ?></div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Link Type</span><span class="value">Dofollow, contextual</span></div>
          <div class="price">Included</div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Placement</span><span class="value">Permanent — no removals</span></div>
          <div class="price">Guaranteed</div>
        </div>
      </div>

      <h2 style="font-size:20px;">How it works</h2>
      <ul style="color:var(--grey); font-size:15px; line-height:1.8; padding-left:20px;">
        <li>Tell us which existing article fits your link, or describe your niche and we'll suggest one</li>
        <li>We review your site for relevance before accepting any request</li>
        <li>Your link is added naturally within the article body, in context</li>
        <li>We send you the live URL once the placement is published</li>
      </ul>

      <div class="form-card" style="margin-top:32px; max-width:100%;">
        <?php if ($success): ?>
          <div class="alert alert-success">Thanks, <?= e($name) ?>! Your link insertion request has been submitted. We'll review and follow up by email.</div>
        <?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <form method="post" action="">
          <div class="form-group">
            <label for="name">Your name</label>
            <input type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="target_post_url">Preferred article on our site (optional)</label>
            <input type="url" id="target_post_url" name="target_post_url" placeholder="https://" value="<?= e($_POST['target_post_url'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="your_url">Your URL (the link destination)</label>
            <input type="url" id="your_url" name="your_url" required placeholder="https://" value="<?= e($_POST['your_url'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="anchor_text">Desired anchor text</label>
            <input type="text" id="anchor_text" name="anchor_text" required value="<?= e($_POST['anchor_text'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="message">Additional notes</label>
            <textarea id="message" name="message"><?= e($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
        </form>
      </div>
    </div>

    <aside>
      <div class="sidebar-box">
        <h4>Why Link Insertion</h4>
        <p style="font-size:14px; color:var(--grey);">Placing a link in an already-published, indexed article can pass authority faster than waiting on a brand-new post to rank.</p>
      </div>
      <div class="sidebar-box cta">
        <h4>Prefer A Full Article?</h4>
        <p>Submit a guest post instead and get a full byline plus a contextual link.</p>
        <a href="<?= SITE_URL ?>/guest-post.php" class="btn btn-primary btn-block">Guest Post Guidelines</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

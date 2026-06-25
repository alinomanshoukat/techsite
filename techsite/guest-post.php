<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Guest Post';
$active_nav = 'guest';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $title = trim($_POST['proposed_title'] ?? '');
    $pitch = trim($_POST['pitch'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$title || !$pitch) {
        $error = 'Please fill in your name, a valid email, a proposed title, and your pitch.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO guest_post_submissions (name, email, website, proposed_title, pitch) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $email, $website, $title, $pitch]);
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Write For <?= e($site_name) ?></h1>
    <p>Pitch an original article to our editorial team. Accepted guest posts are published with a byline, an author bio, and one contextual link back to your site.</p>
  </div>
</div>

<div class="container" style="padding:40px 0 60px;">
  <div class="layout-with-sidebar">
    <div>
      <div class="rate-card" style="margin-bottom:36px;">
        <div class="rate-card-row">
          <div><span class="label">Service</span><span class="value">Guest Post Publication</span></div>
          <div class="price"><?= e(get_setting($pdo, 'guest_post_price', '$60')) ?></div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Turnaround</span><span class="value">Review &amp; publish time</span></div>
          <div class="price"><?= e(get_setting($pdo, 'turnaround_time', '3–5 days')) ?></div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Includes</span><span class="value">1 dofollow contextual link</span></div>
          <div class="price">Included</div>
        </div>
        <div class="rate-card-row">
          <div><span class="label">Placement</span><span class="value">Permanent, never delisted</span></div>
          <div class="price">Guaranteed</div>
        </div>
      </div>

      <h2 style="font-size:20px;">Guidelines</h2>
      <ul style="color:var(--grey); font-size:15px; line-height:1.8; padding-left:20px;">
        <li>800+ words, original and unpublished elsewhere</li>
        <li>Relevant to AI, web development, cybersecurity, gadgets, or startups</li>
        <li>No excessive self-promotion — value to the reader comes first</li>
        <li>One contextual backlink to a relevant, non-spammy page</li>
        <li>We reserve the right to edit for clarity, length, and house style</li>
      </ul>

      <div class="form-card" style="margin-top:32px; max-width:100%;">
        <?php if ($success): ?>
          <div class="alert alert-success">Thanks, <?= e($name) ?>! Your pitch has been submitted. We'll review it and email you within a few days.</div>
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
            <label for="website">Your website</label>
            <input type="url" id="website" name="website" placeholder="https://" value="<?= e($_POST['website'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="proposed_title">Proposed article title</label>
            <input type="text" id="proposed_title" name="proposed_title" required value="<?= e($_POST['proposed_title'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="pitch">Your pitch (200 words max)</label>
            <textarea id="pitch" name="pitch" required><?= e($_POST['pitch'] ?? '') ?></textarea>
            <div class="hint">Tell us what the article covers and why it fits our readers.</div>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Submit Pitch</button>
        </form>
      </div>
    </div>

    <aside>
      <div class="sidebar-box">
        <h4>Why Write For Us</h4>
        <p style="font-size:14px; color:var(--grey);">Reach a focused audience of developers, founders, and tech decision-makers. Every accepted post gets a permanent author bio with a link.</p>
      </div>
      <div class="sidebar-box cta">
        <h4>Need A Link Instead?</h4>
        <p>If you'd rather place a link inside one of our existing articles, check our link insertion service.</p>
        <a href="<?= SITE_URL ?>/link-insertion.php" class="btn btn-primary btn-block">Link Insertion Rates</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

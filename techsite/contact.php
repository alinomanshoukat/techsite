<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Contact';
$active_nav = 'contact';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
        $error = 'Please fill in your name, a valid email, and a message.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Contact Us</h1>
    <p>Questions, feedback, or partnership ideas — send us a message and we'll reply within 1–2 business days.</p>
  </div>
</div>

<div class="container" style="padding:40px 0 60px;">
  <div class="layout-with-sidebar">
    <div class="form-card">
      <?php if ($success): ?>
        <div class="alert alert-success">Thanks, <?= e($name) ?> — your message has been sent. We'll get back to you soon.</div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
      <?php endif; ?>

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
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" value="<?= e($_POST['subject'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" required><?= e($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
      </form>
    </div>

    <aside>
      <div class="sidebar-box">
        <h4>Direct Contact</h4>
        <p style="font-size:14px; color:var(--grey);">Email: <strong><?= e(get_setting($pdo,'contact_email','hello@example.com')) ?></strong></p>
      </div>
      <div class="sidebar-box cta">
        <h4>Looking To Advertise?</h4>
        <p>Check our guest post and link insertion pages for pricing and guidelines.</p>
        <a href="<?= SITE_URL ?>/guest-post.php" class="btn btn-primary btn-block">Guest Post Info</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

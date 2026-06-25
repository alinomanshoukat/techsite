<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Site Settings';
$active_admin_nav = 'settings';
$success = false;

$fields = ['site_name','site_tagline','contact_email','guest_post_price','link_insertion_price','turnaround_time'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) {
        $val = trim($_POST[$f] ?? '');
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$f, $val, $val]);
    }
    $success = true;
}

$current = [];
foreach ($pdo->query("SELECT * FROM site_settings")->fetchAll() as $row) {
    $current[$row['setting_key']] = $row['setting_value'];
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($success): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>

<div class="admin-card" style="max-width:600px;">
  <form method="post" action="">
    <div class="form-group"><label>Site name</label><input type="text" name="site_name" value="<?= e($current['site_name'] ?? '') ?>"></div>
    <div class="form-group"><label>Tagline</label><input type="text" name="site_tagline" value="<?= e($current['site_tagline'] ?? '') ?>"></div>
    <div class="form-group"><label>Contact email</label><input type="email" name="contact_email" value="<?= e($current['contact_email'] ?? '') ?>"></div>
    <hr style="border-color:var(--border); margin:22px 0;">
    <div class="form-group"><label>Guest post price</label><input type="text" name="guest_post_price" value="<?= e($current['guest_post_price'] ?? '') ?>"></div>
    <div class="form-group"><label>Link insertion price</label><input type="text" name="link_insertion_price" value="<?= e($current['link_insertion_price'] ?? '') ?>"></div>
    <div class="form-group"><label>Turnaround time</label><input type="text" name="turnaround_time" value="<?= e($current['turnaround_time'] ?? '') ?>"></div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

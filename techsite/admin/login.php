<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect('/admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'];
        redirect('/admin/dashboard.php');
    } else {
        $error = 'Invalid email or password, or your account has been disabled.';
    }
}

$site_name = get_setting($pdo, 'site_name', 'TechWire');
$tagline = get_setting($pdo, 'site_tagline', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?= e($site_name) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="logo"><?= e(substr($site_name,0,4)) ?><span><?= e(substr($site_name,4)) ?></span></div>
    <div class="tagline" style="text-align:center;"><?= e($tagline) ?> — Admin Panel</div>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" action="">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>

    <div class="demo-creds">
      Demo admin: admin@techwire.test / Admin@123<br>
      Demo author: author@techwire.test / Author@123<br>
      <strong>Change these after installing!</strong>
    </div>
    <p style="text-align:center; margin-top:18px;"><a href="<?= SITE_URL ?>/index.php" style="font-size:13px;">&larr; Back to site</a></p>
  </div>
</div>
</body>
</html>

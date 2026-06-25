<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$page_title = 'My Profile';
$active_admin_nav = 'profile';
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fname = 'uploads/' . uniqid('avatar_') . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../' . $fname)) {
                $avatar = $fname;
            }
        }
    }

    if ($name) {
        if ($new_password && strlen($new_password) >= 6) {
            $hash = password_hash($new_password, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET name=?, bio=?, avatar=?, password=? WHERE id=?")->execute([$name, $bio, $avatar, $hash, $user_id]);
        } else {
            $pdo->prepare("UPDATE users SET name=?, bio=?, avatar=? WHERE id=?")->execute([$name, $bio, $avatar, $user_id]);
        }
        $_SESSION['user_name'] = $name;
        $success = 'Profile updated.';
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } else {
        $error = 'Name is required.';
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<div class="admin-card" style="max-width:520px;">
  <form method="post" action="" enctype="multipart/form-data">
    <div class="form-group" style="text-align:center;">
      <img src="<?= SITE_URL ?>/<?= e($user['avatar'] ?: 'assets/images/avatar-default.png') ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:var(--paper-warm);" alt="Avatar">
    </div>
    <div class="form-group"><label>Avatar image</label><input type="file" name="avatar" accept="image/*"></div>
    <div class="form-group"><label>Full name</label><input type="text" name="name" value="<?= e($user['name']) ?>" required></div>
    <div class="form-group"><label>Email</label><input type="text" value="<?= e($user['email']) ?>" disabled style="background:var(--paper-warm);"></div>
    <div class="form-group"><label>Bio (shown on your articles)</label><textarea name="bio"><?= e($user['bio']) ?></textarea></div>
    <div class="form-group"><label>New password (leave blank to keep current)</label><input type="password" name="new_password" minlength="6"></div>
    <button type="submit" class="btn btn-primary">Save Profile</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

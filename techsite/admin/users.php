<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Authors & Access';
$active_admin_nav = 'users';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] === 'admin' ? 'admin' : 'author';
    $bio = trim($_POST['bio'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Please provide a name, valid email, and a password of at least 6 characters.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO users (name, email, password, role, bio) VALUES (?,?,?,?,?)")
                ->execute([$name, $email, $hash, $role, $bio]);
            $success = "Account created for $name. Share these login details with them securely.";
        } catch (PDOException $e) {
            $error = 'That email is already in use.';
        }
    }
}

if (isset($_GET['toggle'])) {
    $uid = (int)$_GET['toggle'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $pdo->prepare("UPDATE users SET status = IF(status='active','disabled','active') WHERE id = ?")->execute([$uid]);
    }
    redirect('/admin/users.php');
}
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    }
    redirect('/admin/users.php');
}

$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM posts WHERE author_id=u.id) AS post_count FROM users u ORDER BY role DESC, name")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<div class="admin-card">
  <strong>Give Someone Author Access</strong>
  <p style="color:var(--grey); font-size:13px; margin-top:4px;">Create a login for a writer. They'll be able to write and submit posts, but only an admin can publish them.</p>
  <form method="post" action="" style="margin-top:14px;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
      <div class="form-group"><label>Full name</label><input type="text" name="name" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-group"><label>Temporary password</label><input type="text" name="password" required minlength="6"></div>
      <div class="form-group">
        <label>Role</label>
        <select name="role">
          <option value="author">Author (can write, can't publish)</option>
          <option value="admin">Admin (full access)</option>
        </select>
      </div>
    </div>
    <div class="form-group"><label>Short bio (shown on their author box)</label><input type="text" name="bio"></div>
    <button type="submit" name="create_user" value="1" class="btn btn-primary">Create Account</button>
  </form>
</div>

<div class="admin-card">
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Posts</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><strong><?= e($u['name']) ?></strong></td>
        <td><?= e($u['email']) ?></td>
        <td><span class="eyebrow"><?= e(ucfirst($u['role'])) ?></span></td>
        <td><?= (int)$u['post_count'] ?></td>
        <td><span class="status-pill <?= $u['status']==='active'?'status-published':'status-draft' ?>"><?= e($u['status']) ?></span></td>
        <td class="row-actions">
          <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
            <a href="?toggle=<?= (int)$u['id'] ?>"><?= $u['status']==='active'?'Disable':'Enable' ?></a>
            <a href="?delete=<?= (int)$u['id'] ?>" class="delete" data-confirm="Delete this user? Their posts will remain but show no author.">Delete</a>
          <?php else: ?>
            <span style="color:var(--grey); font-size:13px;">(you)</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

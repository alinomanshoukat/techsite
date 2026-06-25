<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Leads & Messages';
$active_admin_nav = 'leads';
$tab = $_GET['tab'] ?? 'guest';

// status updates
if (isset($_POST['update_status'])) {
    $table = $_POST['table'];
    $id = (int)$_POST['id'];
    $status = $_POST['new_status'];
    $allowed_tables = ['guest_post_submissions', 'link_insertion_requests'];
    if (in_array($table, $allowed_tables)) {
        $pdo->prepare("UPDATE $table SET status = ? WHERE id = ?")->execute([$status, $id]);
    }
    redirect('/admin/leads.php?tab=' . ($table === 'guest_post_submissions' ? 'guest' : 'link'));
}
if (isset($_GET['mark_read'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([(int)$_GET['mark_read']]);
    redirect('/admin/leads.php?tab=messages');
}

$guest_posts = $pdo->query("SELECT * FROM guest_post_submissions ORDER BY created_at DESC")->fetchAll();
$link_requests = $pdo->query("SELECT * FROM link_insertion_requests ORDER BY created_at DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex; gap:8px; margin-bottom:18px;">
  <a href="?tab=guest" class="btn btn-sm <?= $tab==='guest'?'btn-primary':'btn-outline' ?>">Guest Post Pitches (<?= count($guest_posts) ?>)</a>
  <a href="?tab=link" class="btn btn-sm <?= $tab==='link'?'btn-primary':'btn-outline' ?>">Link Requests (<?= count($link_requests) ?>)</a>
  <a href="?tab=messages" class="btn btn-sm <?= $tab==='messages'?'btn-primary':'btn-outline' ?>">Contact Messages (<?= count($messages) ?>)</a>
</div>

<?php if ($tab === 'guest'): ?>
<div class="admin-card">
  <?php if ($guest_posts): ?>
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Proposed Title</th><th>Website</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($guest_posts as $g): ?>
      <tr>
        <td><strong><?= e($g['name']) ?></strong><br><span style="color:var(--grey); font-size:12px;"><?= e($g['email']) ?></span></td>
        <td><?= e($g['proposed_title']) ?><br><span style="color:var(--grey); font-size:12px;"><?= e(substr($g['pitch'],0,90)) ?>...</span></td>
        <td><?= $g['website'] ? '<a href="'.e($g['website']).'" target="_blank">'.e($g['website']).'</a>' : '—' ?></td>
        <td><span class="status-pill status-<?= $g['status']==='new'?'pending':($g['status']==='accepted'?'published':'draft') ?>"><?= e($g['status']) ?></span></td>
        <td><?= time_ago($g['created_at']) ?></td>
        <td>
          <form method="post" style="display:flex; gap:4px;">
            <input type="hidden" name="table" value="guest_post_submissions">
            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
            <select name="new_status" style="width:auto; padding:5px;">
              <option value="new" <?= $g['status']==='new'?'selected':'' ?>>New</option>
              <option value="reviewing" <?= $g['status']==='reviewing'?'selected':'' ?>>Reviewing</option>
              <option value="accepted" <?= $g['status']==='accepted'?'selected':'' ?>>Accepted</option>
              <option value="rejected" <?= $g['status']==='rejected'?'selected':'' ?>>Rejected</option>
            </select>
            <button type="submit" name="update_status" value="1" class="btn btn-sm btn-outline">Save</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?><div class="empty-state"><p>No guest post pitches yet.</p></div><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($tab === 'link'): ?>
<div class="admin-card">
  <?php if ($link_requests): ?>
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Their URL</th><th>Anchor Text</th><th>Target Article</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($link_requests as $l): ?>
      <tr>
        <td><strong><?= e($l['name']) ?></strong><br><span style="color:var(--grey); font-size:12px;"><?= e($l['email']) ?></span></td>
        <td><a href="<?= e($l['your_url']) ?>" target="_blank"><?= e(substr($l['your_url'],0,40)) ?></a></td>
        <td><?= e($l['anchor_text']) ?></td>
        <td><?= $l['target_post_url'] ? e(substr($l['target_post_url'],0,40)) : '—' ?></td>
        <td><span class="status-pill status-<?= $l['status']==='new'?'pending':($l['status']==='accepted'?'published':'draft') ?>"><?= e($l['status']) ?></span></td>
        <td><?= time_ago($l['created_at']) ?></td>
        <td>
          <form method="post" style="display:flex; gap:4px;">
            <input type="hidden" name="table" value="link_insertion_requests">
            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
            <select name="new_status" style="width:auto; padding:5px;">
              <option value="new" <?= $l['status']==='new'?'selected':'' ?>>New</option>
              <option value="reviewing" <?= $l['status']==='reviewing'?'selected':'' ?>>Reviewing</option>
              <option value="accepted" <?= $l['status']==='accepted'?'selected':'' ?>>Accepted</option>
              <option value="rejected" <?= $l['status']==='rejected'?'selected':'' ?>>Rejected</option>
            </select>
            <button type="submit" name="update_status" value="1" class="btn btn-sm btn-outline">Save</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?><div class="empty-state"><p>No link insertion requests yet.</p></div><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($tab === 'messages'): ?>
<div class="admin-card">
  <?php if ($messages): ?>
  <table class="admin-table">
    <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($messages as $m): ?>
      <tr style="<?= $m['is_read'] ? '' : 'background:#FCEFD9;' ?>">
        <td><strong><?= e($m['name']) ?></strong><br><span style="color:var(--grey); font-size:12px;"><?= e($m['email']) ?></span></td>
        <td><?= e($m['subject'] ?: '—') ?></td>
        <td><?= e(substr($m['message'],0,100)) ?></td>
        <td><?= time_ago($m['created_at']) ?></td>
        <td>
          <?php if (!$m['is_read']): ?><a href="?tab=messages&mark_read=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline">Mark Read</a><?php else: ?>Read<?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?><div class="empty-state"><p>No contact messages yet.</p></div><?php endif; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

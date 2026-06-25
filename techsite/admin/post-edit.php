<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$is_admin = current_user_role() === 'admin';
$user_id = $_SESSION['user_id'];
$post_id = (int)($_GET['id'] ?? 0);
$post = null;
$error = '';

if ($post_id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    if (!$post) { redirect('/admin/posts.php'); }
    // authors can only edit their own posts
    if (!$is_admin && (int)$post['author_id'] !== $user_id) {
        http_response_code(403);
        die('You can only edit your own posts.');
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// ---- handle save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = slugify(trim($_POST['slug'] ?? '') ?: $title);
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $is_sponsored = isset($_POST['is_sponsored']) ? 1 : 0;
    $post_type = $is_sponsored ? 'guest_post' : 'standard';
    $requested_status = $_POST['status'] ?? 'draft';

    // Authors can save as draft or submit as pending, but only admin can mark published
    if (!$is_admin && $requested_status === 'published') {
        $requested_status = 'pending';
    }

    $featured_image = $post['featured_image'] ?? null;
    if (!empty($_FILES['featured_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $fname = 'uploads/' . uniqid('post_') . '.' . $ext;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], __DIR__ . '/../' . $fname)) {
                $featured_image = $fname;
            }
        } else {
            $error = 'Featured image must be jpg, png, webp, or gif.';
        }
    }

    if (!$title || !$content) {
        $error = 'Title and content are required.';
    } elseif (!$error) {
        $published_at = $post['published_at'] ?? null;
        if ($requested_status === 'published' && !$published_at) {
            $published_at = date('Y-m-d H:i:s');
        }

        if ($post) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE posts SET title=?, slug=?, excerpt=?, content=?, featured_image=?, category_id=?,
                    status=?, is_sponsored=?, post_type=?, published_at=? WHERE id=?
                ");
                $stmt->execute([$title, $slug, $excerpt, $content, $featured_image, $category_id, $requested_status, $is_sponsored, $post_type, $published_at, $post['id']]);
                $post_id = $post['id'];
                redirect('/admin/posts.php?saved=1');
            } catch (PDOException $e) {
                $error = (strpos($e->getMessage(), 'slug') !== false)
                    ? 'That URL slug is already used by another post. Please choose a different slug.'
                    : 'Something went wrong saving this post. Please try again.';
            }
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO posts (title, slug, excerpt, content, featured_image, category_id, author_id, status, is_sponsored, post_type, published_at)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)
                ");
                $stmt->execute([$title, $slug, $excerpt, $content, $featured_image, $category_id, $user_id, $requested_status, $is_sponsored, $post_type, $published_at]);
                $post_id = $pdo->lastInsertId();
                redirect('/admin/posts.php?saved=1');
            } catch (PDOException $e) {
                $error = (strpos($e->getMessage(), 'slug') !== false)
                    ? 'That URL slug is already used by another post. Please choose a different slug.'
                    : 'Something went wrong saving this post. Please try again.';
            }
        }
    }
}

$page_title = $post ? 'Edit Post' : 'New Post';
$active_admin_nav = $post ? 'posts' : 'new-post';
require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if (!$is_admin): ?>
<div class="alert" style="background:#FCEFD9;border:1px solid #8A5A00;color:#8A5A00;">
  As an author, your posts are submitted for review. An admin must approve and publish them.
</div>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data">
  <div class="editor-layout">
    <div>
      <div class="admin-card">
        <div class="form-group">
          <label for="title">Post title</label>
          <input type="text" id="title" name="title" required value="<?= e($post['title'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="slug">URL slug</label>
          <input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="auto-generated-from-title">
        </div>
        <div class="form-group">
          <label for="excerpt">Excerpt (shown on homepage &amp; cards)</label>
          <textarea id="excerpt" name="excerpt" style="min-height:70px;"><?= e($post['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Content</label>
          <div class="editor-toolbar">
            <button data-cmd="bold"><b>B</b></button>
            <button data-cmd="italic"><i>I</i></button>
            <button data-cmd="formatBlock" data-value="H2">H2</button>
            <button data-cmd="formatBlock" data-value="H3">H3</button>
            <button data-cmd="insertUnorderedList">&bull; List</button>
            <button data-cmd="insertOrderedList">1. List</button>
            <button data-cmd="createLink" data-value="" onclick="this.setAttribute('data-value', prompt('Link URL:','https://') || '')">Link</button>
            <button data-cmd="formatBlock" data-value="BLOCKQUOTE">Quote</button>
            <button data-cmd="removeFormat">Clear</button>
          </div>
          <div id="content-editor" contenteditable="true"><?= $post['content'] ?? '<p>Start writing your article here...</p>' ?></div>
          <textarea id="content-hidden" name="content" style="display:none;"><?= e($post['content'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <div>
      <div class="admin-card">
        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="draft" <?= ($post['status'] ?? 'draft')==='draft'?'selected':'' ?>>Draft</option>
            <?php if (!$is_admin): ?>
              <option value="pending" <?= ($post['status'] ?? '')==='pending'?'selected':'' ?>>Submit for Review</option>
            <?php else: ?>
              <option value="pending" <?= ($post['status'] ?? '')==='pending'?'selected':'' ?>>Pending</option>
              <option value="published" <?= ($post['status'] ?? '')==='published'?'selected':'' ?>>Published</option>
            <?php endif; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="category_id">Category</label>
          <select id="category_id" name="category_id">
            <option value="">— None —</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= ($post['category_id'] ?? null)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="featured_image">Featured image</label>
          <?php if (!empty($post['featured_image'])): ?>
            <img src="<?= SITE_URL ?>/<?= e($post['featured_image']) ?>" style="width:100%; border-radius:4px; margin-bottom:8px;" alt="Current featured image">
          <?php endif; ?>
          <input type="file" id="featured_image" name="featured_image" accept="image/*">
          <div class="hint">JPG, PNG, WEBP, or GIF.</div>
        </div>
        <div class="form-group" style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" id="is_sponsored" name="is_sponsored" style="width:auto;" <?= !empty($post['is_sponsored'])?'checked':'' ?>>
          <label for="is_sponsored" style="margin:0;">This is a sponsored / guest post</label>
        </div>
        <button type="submit" class="btn btn-primary btn-block"><?= $post ? 'Save Changes' : 'Create Post' ?></button>
      </div>
    </div>
  </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

// admin.js — admin panel interactions

document.addEventListener('DOMContentLoaded', function () {

  // Simple rich-text editor toolbar (uses contenteditable + execCommand)
  const editor = document.getElementById('content-editor');
  if (editor) {
    document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const cmd = btn.getAttribute('data-cmd');
        const value = btn.getAttribute('data-value') || null;
        editor.focus();
        document.execCommand(cmd, false, value);
        syncHiddenContent();
      });
    });

    const hiddenInput = document.getElementById('content-hidden');
    function syncHiddenContent() {
      if (hiddenInput) hiddenInput.value = editor.innerHTML;
    }
    editor.addEventListener('input', syncHiddenContent);
    // sync once on load in case of pre-filled content
    syncHiddenContent();

    // Make sure content syncs right before submit too
    const form = editor.closest('form');
    if (form) form.addEventListener('submit', syncHiddenContent);
  }

  // Auto-slug generator on the post title field
  const titleInput = document.getElementById('title');
  const slugInput = document.getElementById('slug');
  if (titleInput && slugInput) {
    let slugTouched = slugInput.value.length > 0;
    slugInput.addEventListener('input', function () { slugTouched = true; });
    titleInput.addEventListener('input', function () {
      if (slugTouched) return;
      slugInput.value = titleInput.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    });
  }

  // Mobile admin sidebar toggle
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('admin-sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

});

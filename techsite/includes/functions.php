<?php
/**
 * functions.php — shared helper functions
 */

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function get_setting($pdo, $key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        foreach ($stmt->fetchAll() as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function excerpt_from_content($html, $length = 160) {
    $text = trim(strip_tags($html));
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function time_ago($datetime) {
    if (!$datetime) return '';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', strtotime($datetime));
}

function format_date($datetime) {
    if (!$datetime) return 'Not published';
    return date('F j, Y', strtotime($datetime));
}

function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/admin/login.php');
    }
}

function require_admin() {
    require_login();
    if (current_user_role() !== 'admin') {
        http_response_code(403);
        die('Access denied. This area is restricted to administrators only.');
    }
}

function paginate_links($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';
    $html = '<div class="pagination">';
    if ($current_page > 1) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="page-link">&larr; Prev</a>';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i === $current_page ? ' active' : '';
        $html .= '<a href="' . $base_url . '&page=' . $i . '" class="page-link' . $active . '">' . $i . '</a>';
    }
    if ($current_page < $total_pages) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="page-link">Next &rarr;</a>';
    }
    $html .= '</div>';
    return $html;
}

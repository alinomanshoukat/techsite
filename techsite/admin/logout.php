<?php
require_once __DIR__ . '/../includes/config.php';
$_SESSION = [];
session_destroy();
header('Location: ' . SITE_URL . '/admin/login.php');
exit;

<?php
/**
 * config.php
 * -------------------------------------------------
 * Database connection settings.
 * EDIT THESE 4 LINES to match your hosting/database.
 * -------------------------------------------------
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'techsite');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site-wide path config — leave as is unless you install in a subfolder
define('SITE_URL', '/techsite'); // e.g. '/techwire' if installed in a subfolder, else leave blank

session_start();

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Please check your config.php settings. (' . $e->getMessage() . ')');
}

<?php
/**
 * config.php
 * Database connection + shared session bootstrap.
 * Update DB_USER / DB_PASS to match your XAMPP / WAMP / phpMyAdmin setup.
 */

session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // default XAMPP password is empty
define('DB_NAME', 'appointment_system');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// ---- small helpers used across pages ----
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
function require_admin() {
    if (!is_admin()) {
        header('Location: ../login.php');
        exit;
    }
}
function clean($conn, $str) {
    // Note: SQL injection is already prevented via prepared statements (bind_param)
    // throughout this project, so we only need to trim + escape for safe HTML output.
    return htmlspecialchars(trim($str));
}
?>

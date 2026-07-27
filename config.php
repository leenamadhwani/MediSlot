<?php
/**
 * config.php
 * Database connection + shared session bootstrap.
 */

session_start();

// Fetch database credentials securely from environment variables
$servername = getenv('DB_HOST');
$username   = getenv('DB_USER');
$password   = getenv('DB_PASS');
$dbname     = getenv('DB_NAME');
$port       = getenv('DB_PORT');

// Initialize MySQLi
$conn = mysqli_init();

// Tell MySQLi to use an encrypted SSL connection (Required for Aiven)
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

// Connect to Aiven using the MYSQLI_CLIENT_SSL flag
if (!@$conn->real_connect($servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set character set
$conn->set_charset('utf8mb4');


// ---- small helpers used across pages ----
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: index.php");
        exit();
    }
}
?>
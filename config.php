<?php
/**
 * config.php
 * Database connection + shared session bootstrap.
 */

session_start();

// Database Configuration for Aiven Cloud MySQL
$servername = "mysql-27297956-leenamadhwani196-ccae.h.aivencloud.com";
$username   = "avnadmin";
$password   = "YOUR_AIVEN_PASSWORD"; // <-- Replace this with your actual Aiven password
$dbname     = "defaultdb";
$port       = 19394;

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
        header("Location: index.php"); // Or wherever you want unauthorized users to go
        exit();
    }
}
?>
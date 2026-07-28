<?php
require_once 'config.php';

$new_password = 'admin123';
$hashed = password_hash($new_password, PASSWORD_DEFAULT);
$email = 'admin@medislot.com';

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param('ss', $hashed, $email);
    $stmt->execute();
    echo "Admin password updated.<br>";
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'admin')");
    $name = 'System Admin';
    $phone = '9999999999';
    $stmt->bind_param('ssss', $name, $email, $hashed, $phone);
    $stmt->execute();
    echo "Admin account created.<br>";
}

// verify immediately
$verify = $conn->prepare("SELECT password FROM users WHERE email = ?");
$verify->bind_param('s', $email);
$verify->execute();
$row = $verify->get_result()->fetch_assoc();

if (password_verify($new_password, $row['password'])) {
    echo "<strong style='color:green;'>SUCCESS: password_verify() confirms 'admin123' will work.</strong>";
} else {
    echo "<strong style='color:red;'>FAILED: something is still wrong.</strong>";
}
?>
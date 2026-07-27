<?php
require_once 'config.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// only allow the owner of the appointment to cancel it
$stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute();

header('Location: my_appointments.php?cancelled=1');
exit;
?>

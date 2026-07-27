<?php if (!isset($conn)) { require_once __DIR__ . '/../config.php'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . ' · MediSlot' : 'MediSlot — Doctor Appointment Booking'; ?></title>
<link rel="stylesheet" href="<?php echo isset($base) ? $base : ''; ?>css/style.css">
<link rel="icon" href="data:,">
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="<?php echo isset($base) ? $base : ''; ?>index.php" class="brand"><span class="dot"></span> MediSlot</a>

    <ul class="nav-links">
      <li><a href="<?php echo isset($base) ? $base : ''; ?>index.php">Home</a></li>
      <li><a href="<?php echo isset($base) ? $base : ''; ?>doctors.php">Find Doctors</a></li>
      <?php if (is_logged_in() && !is_admin()): ?>
        <li><a href="<?php echo isset($base) ? $base : ''; ?>my_appointments.php">My Appointments</a></li>
      <?php endif; ?>
    </ul>

    <div class="nav-cta">
      <?php if (is_logged_in()): ?>
        <?php if (is_admin()): ?>
          <a href="<?php echo isset($base) ? $base : ''; ?>admin/dashboard.php" class="btn btn-outline btn-sm">Admin Panel</a>
        <?php endif; ?>
        <span style="font-size:.85rem;color:var(--muted);">Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a href="<?php echo isset($base) ? $base : ''; ?>logout.php" class="btn btn-primary btn-sm">Logout</a>
      <?php else: ?>
        <a href="<?php echo isset($base) ? $base : ''; ?>login.php" class="btn btn-outline btn-sm">Login</a>
        <a href="<?php echo isset($base) ? $base : ''; ?>register.php" class="btn btn-primary btn-sm">Sign Up</a>
      <?php endif; ?>
    </div>

    <button class="nav-toggle">&#9776;</button>
  </div>
</nav>

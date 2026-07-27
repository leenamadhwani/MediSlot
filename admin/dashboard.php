<?php
require_once '../config.php';
require_admin();
$pageTitle = 'Admin Dashboard';
$base = '../';

$totalDoctors = $conn->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'];
$totalPatients = $conn->query("SELECT COUNT(*) c FROM users WHERE role='patient'")->fetch_assoc()['c'];
$totalAppointments = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$pendingAppointments = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='pending'")->fetch_assoc()['c'];

$recent = $conn->query("
  SELECT a.*, d.name AS doctor_name, u.name AS patient_name
  FROM appointments a
  JOIN doctors d ON d.id = a.doctor_id
  JOIN users u ON u.id = a.patient_id
  ORDER BY a.created_at DESC LIMIT 6
");

include '../includes/header.php';
?>

<section class="section" style="padding-top:3rem;">
  <div class="container">
    <div class="section-head" style="text-align:left;margin-bottom:2rem;max-width:none;">
      <span class="eyebrow">Admin panel</span>
      <h2>Dashboard overview</h2>
    </div>

    <div class="stats-row">
      <div class="stat-card"><strong><?php echo $totalDoctors; ?></strong><span>Total doctors</span></div>
      <div class="stat-card"><strong><?php echo $totalPatients; ?></strong><span>Registered patients</span></div>
      <div class="stat-card"><strong><?php echo $totalAppointments; ?></strong><span>Total appointments</span></div>
      <div class="stat-card"><strong><?php echo $pendingAppointments; ?></strong><span>Pending approval</span></div>
    </div>

    <div class="hero-actions" style="margin-bottom:2rem;">
      <a href="manage_doctors.php" class="btn btn-primary">Manage Doctors</a>
      <a href="manage_appointments.php" class="btn btn-outline">Manage Appointments</a>
    </div>

    <h3>Recent appointments</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php if ($recent->num_rows === 0): ?>
            <tr><td colspan="5" style="text-align:center;color:var(--muted);">No appointments yet.</td></tr>
          <?php endif; ?>
          <?php while ($r = $recent->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($r['doctor_name']); ?></td>
            <td class="mono"><?php echo date('d M Y', strtotime($r['appointment_date'])); ?></td>
            <td class="mono"><?php echo date('g:i A', strtotime($r['appointment_time'])); ?></td>
            <td><span class="status status-<?php echo $r['status']; ?>"><?php echo $r['status']; ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>

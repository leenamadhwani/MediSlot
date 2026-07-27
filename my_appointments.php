<?php
require_once 'config.php';
require_login();
if (is_admin()) { header('Location: admin/dashboard.php'); exit; }

$pageTitle = 'My Appointments';
$base = '';
$booked = isset($_GET['booked']);
$cancelled = isset($_GET['cancelled']);

$stmt = $conn->prepare("
  SELECT a.*, d.name AS doctor_name, d.specialization, d.fee
  FROM appointments a
  JOIN doctors d ON d.id = a.doctor_id
  WHERE a.patient_id = ?
  ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$appointments = $stmt->get_result();

include 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Your bookings</span>
      <h2>My Appointments</h2>
    </div>

    <?php if ($booked): ?><div class="alert alert-success" style="max-width:600px;margin:0 auto 2rem;">Appointment requested! We'll confirm it shortly.</div><?php endif; ?>
    <?php if ($cancelled): ?><div class="alert alert-success" style="max-width:600px;margin:0 auto 2rem;">Appointment cancelled.</div><?php endif; ?>

    <?php if ($appointments->num_rows === 0): ?>
      <div class="empty-state">
        <div class="icon">📅</div>
        <h3>No appointments yet</h3>
        <p>Book your first appointment with one of our specialists.</p>
        <a href="doctors.php" class="btn btn-primary mt-2">Find a Doctor</a>
      </div>
    <?php else: ?>
      <div class="grid grid-2">
        <?php while ($a = $appointments->fetch_assoc()): ?>
        <div class="stub-card">
          <div class="stub-main">
            <div class="stub-avatar"><?php echo strtoupper(substr($a['doctor_name'], 4, 1)); ?></div>
            <div>
              <span class="badge"><?php echo htmlspecialchars($a['specialization']); ?></span>
              <h3><?php echo htmlspecialchars($a['doctor_name']); ?></h3>
              <p class="stub-meta mono"><?php echo date('d M Y', strtotime($a['appointment_date'])); ?> · <?php echo date('g:i A', strtotime($a['appointment_time'])); ?></p>
              <?php if ($a['reason']): ?><p class="stub-meta">"<?php echo htmlspecialchars($a['reason']); ?>"</p><?php endif; ?>
            </div>
          </div>
          <div class="stub-perf"></div>
          <div class="stub-foot">
            <span class="status status-<?php echo $a['status']; ?>"><?php echo $a['status']; ?></span>
            <?php if ($a['status'] === 'pending' || $a['status'] === 'confirmed'): ?>
              <a href="cancel_appointment.php?id=<?php echo $a['id']; ?>" class="btn btn-danger btn-sm confirm-cancel">Cancel</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

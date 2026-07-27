<?php
require_once '../config.php';
require_admin();
$pageTitle = 'Manage Appointments';
$base = '../';

// ---- update status ----
if (isset($_GET['status'], $_GET['id'])) {
    $allowed = ['pending','confirmed','completed','cancelled'];
    $status = $_GET['status'];
    $id = (int) $_GET['id'];
    if (in_array($status, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
    }
    header('Location: manage_appointments.php?updated=1');
    exit;
}

// ---- filter ----
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "
  SELECT a.*, d.name AS doctor_name, d.specialization, u.name AS patient_name, u.phone
  FROM appointments a
  JOIN doctors d ON d.id = a.doctor_id
  JOIN users u ON u.id = a.patient_id
";
if (in_array($filter, ['pending','confirmed','completed','cancelled'], true)) {
    $sql .= " WHERE a.status = '" . $conn->real_escape_string($filter) . "'";
}
$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = $conn->query($sql);

include '../includes/header.php';
?>

<section class="section" style="padding-top:3rem;">
  <div class="container">
    <div class="section-head" style="text-align:left;margin-bottom:1.5rem;max-width:none;">
      <span class="eyebrow">Admin panel</span>
      <h2>Manage Appointments</h2>
    </div>

    <?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Appointment status updated.</div><?php endif; ?>

    <div class="hero-actions" style="margin-bottom:1.5rem;">
      <a href="manage_appointments.php?filter=all" class="btn <?php echo $filter==='all'?'btn-primary':'btn-outline'; ?> btn-sm">All</a>
      <a href="manage_appointments.php?filter=pending" class="btn <?php echo $filter==='pending'?'btn-primary':'btn-outline'; ?> btn-sm">Pending</a>
      <a href="manage_appointments.php?filter=confirmed" class="btn <?php echo $filter==='confirmed'?'btn-primary':'btn-outline'; ?> btn-sm">Confirmed</a>
      <a href="manage_appointments.php?filter=completed" class="btn <?php echo $filter==='completed'?'btn-primary':'btn-outline'; ?> btn-sm">Completed</a>
      <a href="manage_appointments.php?filter=cancelled" class="btn <?php echo $filter==='cancelled'?'btn-primary':'btn-outline'; ?> btn-sm">Cancelled</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if ($appointments->num_rows === 0): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--muted);">No appointments found.</td></tr>
          <?php endif; ?>
          <?php while ($a = $appointments->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($a['patient_name']); ?><br><small class="mono" style="color:var(--muted);"><?php echo htmlspecialchars($a['phone']); ?></small></td>
            <td><?php echo htmlspecialchars($a['doctor_name']); ?><br><small style="color:var(--muted);"><?php echo htmlspecialchars($a['specialization']); ?></small></td>
            <td class="mono"><?php echo date('d M Y', strtotime($a['appointment_date'])); ?></td>
            <td class="mono"><?php echo date('g:i A', strtotime($a['appointment_time'])); ?></td>
            <td><span class="status status-<?php echo $a['status']; ?>"><?php echo $a['status']; ?></span></td>
            <td>
              <?php if ($a['status'] === 'pending'): ?>
                <a href="manage_appointments.php?id=<?php echo $a['id']; ?>&status=confirmed" class="btn btn-primary btn-sm">Confirm</a>
              <?php endif; ?>
              <?php if ($a['status'] === 'confirmed'): ?>
                <a href="manage_appointments.php?id=<?php echo $a['id']; ?>&status=completed" class="btn btn-outline btn-sm">Mark done</a>
              <?php endif; ?>
              <?php if ($a['status'] !== 'cancelled' && $a['status'] !== 'completed'): ?>
                <a href="manage_appointments.php?id=<?php echo $a['id']; ?>&status=cancelled" class="btn btn-danger btn-sm confirm-delete">Cancel</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>

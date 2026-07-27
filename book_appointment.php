<?php
require_once 'config.php';
require_login();
if (is_admin()) { header('Location: admin/dashboard.php'); exit; }

$pageTitle = 'Book Appointment';
$base = '';
$error = '';
$success = '';

$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : (isset($_POST['doctor_id']) ? (int) $_POST['doctor_id'] : 0);

$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ? AND status='active'");
$stmt->bind_param('i', $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();

if (!$doctor) {
    header('Location: doctors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date   = clean($conn, $_POST['appointment_date']);
    $time   = clean($conn, $_POST['appointment_time']);
    $reason = clean($conn, $_POST['reason']);
    $patient_id = $_SESSION['user_id'];

    $today = date('Y-m-d');
    if ($date < $today) {
        $error = 'Please choose a valid future date.';
    } else {
        // prevent double-booking same doctor / date / time
        $check = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $check->bind_param('iss', $doctor_id, $date, $time);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'That slot is already booked. Please choose a different time.';
        } else {
            $ins = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param('iisss', $patient_id, $doctor_id, $date, $time, $reason);
            if ($ins->execute()) {
                header('Location: my_appointments.php?booked=1');
                exit;
            } else {
                $error = 'Could not book the appointment. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
$initials = strtoupper(substr($doctor['name'], 4, 1));
?>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:.9fr 1.1fr; gap:2.5rem; align-items:flex-start; max-width:960px; margin:0 auto;">

      <div class="stub-card">
        <div class="stub-main">
          <div class="stub-avatar" style="width:64px;height:64px;font-size:1.5rem;"><?php echo $initials; ?></div>
          <div>
            <span class="badge"><?php echo htmlspecialchars($doctor['specialization']); ?></span>
            <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
            <p class="stub-meta"><?php echo htmlspecialchars($doctor['qualification']); ?></p>
          </div>
        </div>
        <div class="stub-perf"></div>
        <div style="padding:1rem 1.5rem 1.5rem;">
          <p class="stub-meta"><?php echo htmlspecialchars($doctor['bio']); ?></p>
          <p class="stub-meta"><strong>Available:</strong> <?php echo htmlspecialchars($doctor['available_days']); ?>, <?php echo date('g:i A', strtotime($doctor['slot_start'])); ?>&ndash;<?php echo date('g:i A', strtotime($doctor['slot_end'])); ?></p>
          <div class="stub-fee mt-2">₹<?php echo number_format($doctor['fee'],0); ?><span>Consultation fee</span></div>
        </div>
      </div>

      <div class="form-card" style="margin:0;">
        <h2 style="text-align:left;">Book your slot</h2>
        <p class="form-sub" style="text-align:left;">Choose a date and time that works for you.</p>

        <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
          <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
          <div class="field-row">
            <div class="field">
              <label for="appointment_date">Date</label>
              <input type="date" id="appointment_date" name="appointment_date" required>
            </div>
            <div class="field">
              <label for="appointment_time">Time</label>
              <input type="time" id="appointment_time" name="appointment_time" required
                     min="<?php echo $doctor['slot_start']; ?>" max="<?php echo $doctor['slot_end']; ?>">
            </div>
          </div>
          <div class="field">
            <label for="reason">Reason for visit (optional)</label>
            <textarea id="reason" name="reason" rows="3" placeholder="Briefly describe your symptoms or reason for the visit"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Confirm booking</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

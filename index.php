<?php
require_once 'config.php';
$pageTitle = 'Home';
$base = '';

// pull 3 doctors to feature
$featured = $conn->query("SELECT * FROM doctors WHERE status='active' ORDER BY experience_years DESC LIMIT 3");
include 'includes/header.php';
?>

<section class="hero">
  <div class="container">
    <div class="hero-copy">
      <span class="eyebrow">● Slots open today</span>
      <h1>Book a doctor's appointment in under a minute.</h1>
      <p class="lead">MediSlot connects you with verified doctors across specializations. Pick a time that works, and skip the waiting-room phone calls.</p>
      <div class="hero-actions">
        <a href="doctors.php" class="btn btn-primary">Find a Doctor</a>
        <a href="<?php echo is_logged_in() ? 'my_appointments.php' : 'register.php'; ?>" class="btn btn-outline">
          <?php echo is_logged_in() ? 'My Appointments' : 'Create Account'; ?>
        </a>
      </div>
      <div class="hero-stats">
        <div><strong>6+</strong><span>Specializations</span></div>
        <div><strong>24/7</strong><span>Online booking</span></div>
        <div><strong>0₹</strong><span>Booking fee</span></div>
      </div>
    </div>

    <div class="ticket-visual">
      <div class="stub-row">
        <span class="stub-label">Patient</span>
        <span class="stub-value">You</span>
      </div>
      <div class="stub-row">
        <span class="stub-label">Doctor</span>
        <span class="stub-value">Dr. Ananya Sharma</span>
      </div>
      <div class="stub-row">
        <span class="stub-label">Date</span>
        <span class="stub-value">26 Jul, 2026</span>
      </div>
      <div class="stub-row">
        <span class="stub-label">Time slot</span>
        <span class="stub-value">10:30 AM</span>
      </div>
      <div class="stub-row">
        <span class="stub-label">Status</span>
        <span class="stub-status">Confirmed</span>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">How it works</span>
      <h2>Three steps, no phone calls</h2>
      <p>Everything happens online — search, pick a slot, and show up.</p>
    </div>
    <div class="grid grid-3">
      <div class="stub-card">
        <div class="stub-main">
          <div class="stub-avatar">1</div>
          <div>
            <h3>Search a specialist</h3>
            <p class="stub-meta">Filter doctors by specialization, experience or consultation fee.</p>
          </div>
        </div>
      </div>
      <div class="stub-card">
        <div class="stub-main">
          <div class="stub-avatar">2</div>
          <div>
            <h3>Pick a time slot</h3>
            <p class="stub-meta">Choose a date and time that fits your schedule in a couple of taps.</p>
          </div>
        </div>
      </div>
      <div class="stub-card">
        <div class="stub-main">
          <div class="stub-avatar">3</div>
          <div>
            <h3>Get confirmed</h3>
            <p class="stub-meta">Track the status of your booking any time from "My Appointments".</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background:var(--white);">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Popular doctors</span>
      <h2>Meet a few of our specialists</h2>
    </div>
    <div class="grid grid-3">
      <?php while ($doc = $featured->fetch_assoc()):
          $initials = strtoupper(substr($doc['name'], 4, 1)); // skip "Dr. "
      ?>
      <div class="stub-card">
        <div class="stub-main">
          <div class="stub-avatar"><?php echo $initials; ?></div>
          <div>
            <span class="badge"><?php echo htmlspecialchars($doc['specialization']); ?></span>
            <h3><?php echo htmlspecialchars($doc['name']); ?></h3>
            <p class="stub-meta"><?php echo htmlspecialchars($doc['experience_years']); ?> yrs experience · <?php echo htmlspecialchars($doc['qualification']); ?></p>
          </div>
        </div>
        <div class="stub-perf"></div>
        <div class="stub-foot">
          <div class="stub-fee">₹<?php echo number_format($doc['fee'],0); ?><span>Consultation</span></div>
          <a href="book_appointment.php?doctor_id=<?php echo $doc['id']; ?>" class="btn btn-amber btn-sm">Book</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div class="center mt-2">
      <a href="doctors.php" class="btn btn-outline">View all doctors</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

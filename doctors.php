<?php
require_once 'config.php';
$pageTitle = 'Find Doctors';
$base = '';

$doctors = $conn->query("SELECT * FROM doctors WHERE status='active' ORDER BY name ASC");
$specs   = $conn->query("SELECT DISTINCT specialization FROM doctors WHERE status='active' ORDER BY specialization ASC");
$specList = [];
while ($s = $specs->fetch_assoc()) { $specList[] = $s['specialization']; }

include 'includes/header.php';
?>

<section class="section" style="padding-top:3rem;">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Directory</span>
      <h2>Find the right specialist</h2>
      <p>Search by name or filter by specialization to find an available slot.</p>
    </div>

    <div class="grid grid-2" style="max-width:700px;margin:0 auto 2.5rem;">
      <div class="field" style="margin:0;">
        <label for="doctorSearch">Search by name</label>
        <input type="text" id="doctorSearch" placeholder="e.g. Ananya Sharma">
      </div>
      <div class="field" style="margin:0;">
        <label for="specFilter">Specialization</label>
        <select id="specFilter">
          <option value="">All specializations</option>
          <?php foreach ($specList as $sp): ?>
            <option value="<?php echo htmlspecialchars($sp); ?>"><?php echo htmlspecialchars($sp); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="grid grid-3">
      <?php if ($doctors->num_rows === 0): ?>
        <div class="empty-state" style="grid-column:1/-1;">
          <div class="icon">🩺</div>
          <h3>No doctors available</h3>
          <p>Please check back soon.</p>
        </div>
      <?php endif; ?>

      <?php while ($doc = $doctors->fetch_assoc()):
          $initials = strtoupper(substr($doc['name'], 4, 1));
      ?>
      <div class="stub-card doctor-card-item" data-name="<?php echo htmlspecialchars($doc['name']); ?>" data-spec="<?php echo htmlspecialchars($doc['specialization']); ?>">
        <div class="stub-main">
          <div class="stub-avatar"><?php echo $initials; ?></div>
          <div>
            <span class="badge"><?php echo htmlspecialchars($doc['specialization']); ?></span>
            <h3><?php echo htmlspecialchars($doc['name']); ?></h3>
            <p class="stub-meta"><?php echo htmlspecialchars($doc['qualification']); ?></p>
            <p class="stub-meta"><?php echo htmlspecialchars($doc['experience_years']); ?> yrs exp · Available <?php echo htmlspecialchars($doc['available_days']); ?></p>
          </div>
        </div>
        <div class="stub-perf"></div>
        <div class="stub-foot">
          <div class="stub-fee">₹<?php echo number_format($doc['fee'],0); ?><span>Consultation</span></div>
          <a href="book_appointment.php?doctor_id=<?php echo $doc['id']; ?>" class="btn btn-amber btn-sm">Book slot</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <div id="noResults" class="empty-state" style="display:none;">
      <div class="icon">🔍</div>
      <h3>No matches found</h3>
      <p>Try a different name or specialization.</p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

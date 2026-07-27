<?php
require_once '../config.php';
require_admin();
$pageTitle = 'Manage Doctors';
$base = '../';
$error = '';

// ---- delete ----
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: manage_doctors.php?deleted=1');
    exit;
}

// ---- create / update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = clean($conn, $_POST['name']);
    $spec   = clean($conn, $_POST['specialization']);
    $qual   = clean($conn, $_POST['qualification']);
    $exp    = (int) $_POST['experience_years'];
    $fee    = (float) $_POST['fee'];
    $days   = clean($conn, $_POST['available_days']);
    $start  = clean($conn, $_POST['slot_start']);
    $end    = clean($conn, $_POST['slot_end']);
    $bio    = clean($conn, $_POST['bio']);
    $status = ($_POST['status'] === 'inactive') ? 'inactive' : 'active';

    if ($name === '' || $spec === '') {
        $error = 'Name and specialization are required.';
    } elseif (!empty($_POST['doctor_id'])) {
        // ---- update existing doctor ----
        $doctor_id = (int) $_POST['doctor_id'];
        $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, qualification=?, experience_years=?, fee=?, available_days=?, slot_start=?, slot_end=?, bio=?, status=? WHERE id=?");
        $stmt->bind_param('sssidsssssi', $name, $spec, $qual, $exp, $fee, $days, $start, $end, $bio, $status, $doctor_id);
        $stmt->execute();
        header('Location: manage_doctors.php?updated=1');
        exit;
    } else {
        // ---- insert new doctor ----
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, qualification, experience_years, fee, available_days, slot_start, slot_end, bio, status) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssidsssss', $name, $spec, $qual, $exp, $fee, $days, $start, $end, $bio, $status);
        $stmt->execute();
        header('Location: manage_doctors.php?added=1');
        exit;
    }
}

// ---- load doctor for editing ----
$editing = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
}

$doctors = $conn->query("SELECT * FROM doctors ORDER BY created_at DESC");

include '../includes/header.php';
?>

<section class="section" style="padding-top:3rem;">
  <div class="container">
    <div class="section-head" style="text-align:left;margin-bottom:2rem;max-width:none;">
      <span class="eyebrow">Admin panel</span>
      <h2><?php echo $editing ? 'Edit doctor' : 'Manage Doctors'; ?></h2>
    </div>

    <?php if (isset($_GET['added'])): ?><div class="alert alert-success">Doctor added successfully.</div><?php endif; ?>
    <?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Doctor updated successfully.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Doctor removed.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <div class="form-card" style="max-width:none;margin-bottom:3rem;">
      <h2 style="text-align:left;"><?php echo $editing ? 'Update doctor details' : 'Add a new doctor'; ?></h2>
      <form method="POST" class="needs-validation" novalidate>
        <?php if ($editing): ?><input type="hidden" name="doctor_id" value="<?php echo $editing['id']; ?>"><?php endif; ?>
        <div class="field-row">
          <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" required placeholder="Dr. Full Name" value="<?php echo $editing ? htmlspecialchars($editing['name']) : ''; ?>">
          </div>
          <div class="field">
            <label for="specialization">Specialization</label>
            <input type="text" id="specialization" name="specialization" required placeholder="e.g. Cardiologist" value="<?php echo $editing ? htmlspecialchars($editing['specialization']) : ''; ?>">
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="qualification">Qualification</label>
            <input type="text" id="qualification" name="qualification" placeholder="MBBS, MD" value="<?php echo $editing ? htmlspecialchars($editing['qualification']) : ''; ?>">
          </div>
          <div class="field">
            <label for="experience_years">Experience (years)</label>
            <input type="number" id="experience_years" name="experience_years" min="0" value="<?php echo $editing ? (int)$editing['experience_years'] : 0; ?>">
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="fee">Consultation fee (₹)</label>
            <input type="number" id="fee" name="fee" min="0" step="0.01" value="<?php echo $editing ? $editing['fee'] : 0; ?>">
          </div>
          <div class="field">
            <label for="available_days">Available days</label>
            <input type="text" id="available_days" name="available_days" placeholder="Mon-Sat" value="<?php echo $editing ? htmlspecialchars($editing['available_days']) : 'Mon-Sat'; ?>">
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="slot_start">Slot start time</label>
            <input type="time" id="slot_start" name="slot_start" value="<?php echo $editing ? substr($editing['slot_start'],0,5) : '09:00'; ?>">
          </div>
          <div class="field">
            <label for="slot_end">Slot end time</label>
            <input type="time" id="slot_end" name="slot_end" value="<?php echo $editing ? substr($editing['slot_end'],0,5) : '17:00'; ?>">
          </div>
        </div>
        <div class="field">
          <label for="bio">Short bio</label>
          <textarea id="bio" name="bio" rows="3"><?php echo $editing ? htmlspecialchars($editing['bio']) : ''; ?></textarea>
        </div>
        <div class="field">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="active" <?php echo (!$editing || $editing['status']==='active') ? 'selected':''; ?>>Active</option>
            <option value="inactive" <?php echo ($editing && $editing['status']==='inactive') ? 'selected':''; ?>>Inactive</option>
          </select>
        </div>
        <div class="hero-actions">
          <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Save changes' : 'Add doctor'; ?></button>
          <?php if ($editing): ?><a href="manage_doctors.php" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <h3>All doctors</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Name</th><th>Specialization</th><th>Fee</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if ($doctors->num_rows === 0): ?>
            <tr><td colspan="5" style="text-align:center;color:var(--muted);">No doctors added yet.</td></tr>
          <?php endif; ?>
          <?php while ($d = $doctors->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($d['name']); ?></td>
            <td><?php echo htmlspecialchars($d['specialization']); ?></td>
            <td class="mono">₹<?php echo number_format($d['fee'],0); ?></td>
            <td><span class="status status-<?php echo $d['status']==='active'?'confirmed':'cancelled'; ?>"><?php echo $d['status']; ?></span></td>
            <td>
              <a href="manage_doctors.php?edit=<?php echo $d['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="manage_doctors.php?delete=<?php echo $d['id']; ?>" class="btn btn-danger btn-sm confirm-delete">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>

<?php
require_once 'config.php';
$pageTitle = 'Login';
$base = '';
$error = '';
$registered = isset($_GET['registered']);

if (is_logged_in()) {
    header('Location: ' . (is_admin() ? 'admin/dashboard.php' : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php'));
            exit;
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $error = 'No account found with that email.';
    }
}
include 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <span class="eyebrow" style="display:block;width:fit-content;margin:0 auto 1rem;">Welcome back</span>
      <h2>Log in to MediSlot</h2>
      <p class="form-sub">Manage your bookings in one place.</p>

      <?php if ($registered): ?><div class="alert alert-success">Account created! Please log in.</div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

      <form method="POST" class="needs-validation" novalidate>
        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log in</button>
        <p class="form-foot">New here? <a href="register.php">Create an account</a></p>
        <p class="form-foot" style="color:var(--muted);font-size:.78rem;">Admin demo: admin@medislot.com / admin123</p>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

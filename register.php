<?php
require_once 'config.php';
$pageTitle = 'Sign Up';
$base = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = clean($conn, $_POST['name']);
    $email = clean($conn, $_POST['email']);
    $phone = clean($conn, $_POST['phone']);
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($name === '' || $email === '' || $phone === '' || $pass === '') {
        $error = 'Please fill in all fields.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'patient')");
            $stmt->bind_param('ssss', $name, $email, $hashed, $phone);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
include 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <span class="eyebrow" style="display:block;width:fit-content;margin:0 auto 1rem;">Get started</span>
      <h2>Create your account</h2>
      <p class="form-sub">Book appointments and track them anytime.</p>

      <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

      <form method="POST" class="needs-validation" novalidate>
        <div class="field">
          <label for="name">Full name</label>
          <input type="text" id="name" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
        </div>
        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>
        <div class="field">
          <label for="phone">Phone number</label>
          <input type="text" id="phone" name="phone" maxlength="10" required value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
        </div>
        <div class="field-row">
          <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="field">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create account</button>
        <p class="form-foot">Already have an account? <a href="login.php">Log in</a></p>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

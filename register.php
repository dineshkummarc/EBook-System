<?php
session_start();
include 'db_config.php';

$error   = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role_id  = 2;

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check duplicate
        $check = mysqli_query($conn, "SELECT id FROM accounts WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = "An account with this email already exists.";
        } else {
            $insert = "INSERT INTO accounts (name, email, password, role_id) VALUES ('$name', '$email', '$password', $role_id)";
            if (mysqli_query($conn, $insert)) {
                $_SESSION['message'] = "Welcome, $name! Your account has been created. Please log in.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
  <!-- Left Panel -->
  <div class="auth-left">
    <div class="auth-left-content">
      <div class="auth-illustration">🌟</div>
      <h2>Join Our Community</h2>
      <p>Create a free account to unlock thousands of eBooks, participate in competitions, and connect with readers worldwide.</p>

      <div class="mt-4" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:280px;margin:24px auto;">
        <?php
        $perks = [
          ['icon'=>'fas fa-book-open','text'=>'5K+ Books'],
          ['icon'=>'fas fa-download','text'=>'Instant Access'],
          ['icon'=>'fas fa-trophy','text'=>'Competitions'],
          ['icon'=>'fas fa-headset','text'=>'24/7 Support'],
        ];
        foreach ($perks as $p):
        ?>
        <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:14px;text-align:center;">
          <i class="<?= $p['icon'] ?>" style="font-size:1.3rem;color:var(--accent);margin-bottom:6px;display:block;"></i>
          <span style="font-size:.75rem;color:rgba(255,255,255,.7);"><?= $p['text'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <a href="index.php" class="btn-white-custom mt-3" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Back to Home
      </a>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="auth-right">
    <div class="auth-form-container" style="max-width:460px;">
      <a href="index.php" class="navbar-logo mb-4 d-inline-flex">
        <div class="logo-icon me-2" style="background:linear-gradient(135deg,#70a0af,#5a8a9f);border-radius:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-book-open text-white"></i>
        </div>
        <span style="font-family:Poppins,sans-serif;font-weight:800;font-size:1.4rem;color:var(--text-primary);">e<span style="color:var(--primary);">BOOK</span></span>
      </a>

      <h2>Create Account</h2>
      <p>Fill in your details to get started — it's free!</p>

      <?php if (!empty($error)): ?>
      <div class="alert-premium error mb-4 auto-dismiss">
        <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" data-validate>
        <!-- Full Name -->
        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Full Name</label>
          <i class="fas fa-user input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="text" name="name" class="form-input with-icon" placeholder="Your full name"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autocomplete="name">
        </div>

        <!-- Email -->
        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Email Address</label>
          <i class="fas fa-envelope input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="email" name="email" class="form-input with-icon" placeholder="you@example.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
        </div>

        <!-- Password -->
        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Password</label>
          <i class="fas fa-lock input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="password" name="password" id="password-strength-input" class="form-input with-icon"
            placeholder="Min 6 characters" required autocomplete="new-password" style="padding-right:44px;">
          <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
          <div class="strength-meter mt-2">
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Confirm Password</label>
          <i class="fas fa-lock input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="password" name="confirm_password" id="confirmPassword" class="form-input with-icon"
            placeholder="Repeat your password" required autocomplete="new-password" style="padding-right:44px;">
          <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
        </div>

        <div class="mb-4" style="font-size:.8rem;color:var(--text-secondary);">
          <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer;">
            <input type="checkbox" name="terms" required style="accent-color:var(--primary);margin-top:3px;flex-shrink:0;">
            <span>I agree to the <a href="#" style="color:var(--secondary);">Terms of Service</a> and <a href="#" style="color:var(--secondary);">Privacy Policy</a></span>
          </label>
        </div>

        <button type="submit" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
          <i class="fas fa-user-plus me-2"></i>Create Account
        </button>
      </form>

      <p style="text-align:center;font-size:.875rem;color:var(--text-secondary);margin-top:24px;">
        Already have an account?
        <a href="login.php" style="color:var(--primary);font-weight:600;">Sign in here</a>
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
  // Confirm password real-time validation
  const pwd = document.querySelector('input[name="password"]');
  const confirm = document.getElementById('confirmPassword');
  confirm.addEventListener('input', function() {
    if (this.value && this.value !== pwd.value) {
      this.style.borderColor = 'var(--danger)';
    } else {
      this.style.borderColor = '';
    }
  });
</script>
</body>
</html>

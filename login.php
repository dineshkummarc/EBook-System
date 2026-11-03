<?php
session_start();
include 'db_config.php';

$error = '';

if (isset($_GET['redirect'])) {
    $_SESSION['redirect_to'] = $_GET['redirect'];
}
$redirect = $_SESSION['redirect_to'] ?? 'user_dashboard.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM accounts WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['password']) {
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_name']  = $row['name'] ?? 'User';
            $_SESSION['role_id']    = $row['role_id'];
            $_SESSION['user_id']    = $row['id'];
            unset($_SESSION['redirect_to']);

            if ($row['role_id'] == 1) {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($row['role_id'] == 2) {
                if (strpos($redirect, 'login.php') !== false || empty($redirect)) {
                    $redirect = 'user_dashboard.php';
                }
                header("Location: " . $redirect);
                exit();
            } else {
                $error = "Invalid role ID!";
            }
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — eBook Hub</title>
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
      <div class="auth-illustration">📚</div>
      <h2>Welcome Back, Reader!</h2>
      <p>Log in to access your digital library, track your orders, and join exciting writing competitions.</p>

      <div class="mt-4" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
        <div style="text-align:center;color:rgba(255,255,255,.6);">
          <div style="font-size:1.8rem;font-weight:800;color:var(--accent);">5K+</div>
          <div style="font-size:.8rem;">Books</div>
        </div>
        <div style="text-align:center;color:rgba(255,255,255,.6);">
          <div style="font-size:1.8rem;font-weight:800;color:var(--accent);">12K+</div>
          <div style="font-size:.8rem;">Readers</div>
        </div>
        <div style="text-align:center;color:rgba(255,255,255,.6);">
          <div style="font-size:1.8rem;font-weight:800;color:var(--accent);">50+</div>
          <div style="font-size:.8rem;">Competitions</div>
        </div>
      </div>

      <a href="index.php" class="btn-white-custom mt-4" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Back to Home
      </a>
    </div>
  </div>

  <!-- Right Panel — Form -->
  <div class="auth-right">
    <div class="auth-form-container">
      <a href="index.php" class="navbar-logo mb-4 d-inline-flex">
        <div class="logo-icon me-2" style="background:linear-gradient(135deg,#70a0af,#5a8a9f);border-radius:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-book-open text-white"></i>
        </div>
        <span style="font-family:Poppins,sans-serif;font-weight:800;font-size:1.4rem;color:var(--text-primary);">e<span style="color:var(--primary);">BOOK</span></span>
      </a>

      <h2>Sign In</h2>
      <p>Enter your credentials to access your account</p>

      <?php if (!empty($error)): ?>
      <div class="alert-premium error mb-4 auto-dismiss">
        <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" data-validate>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? 'user_dashboard.php') ?>">

        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Email Address</label>
          <i class="fas fa-envelope input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="email" name="email" class="form-input with-icon" placeholder="you@example.com" required autocomplete="email">
        </div>

        <div class="form-group" style="position:relative;">
          <label class="form-label-float">Password</label>
          <i class="fas fa-lock input-icon" style="position:absolute;left:14px;bottom:13px;color:var(--text-muted);font-size:.9rem;"></i>
          <input type="password" name="password" id="loginPassword" class="form-input with-icon" placeholder="Your password" required autocomplete="current-password" style="padding-right:44px;">
          <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
          <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;cursor:pointer;">
            <input type="checkbox" name="remember" style="accent-color:var(--primary);"> Remember me
          </label>
          <a href="#" style="font-size:.85rem;color:var(--secondary);">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
          <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </button>
      </form>

      <div class="auth-divider"><span>or continue with</span></div>

      <div class="d-flex gap-3 mb-4">
        <button class="social-auth-btn">
          <i class="fab fa-google" style="color:#ea4335;font-size:1.1rem;"></i>Google
        </button>
        <button class="social-auth-btn">
          <i class="fab fa-facebook-f" style="color:#1877f2;font-size:1.1rem;"></i>Facebook
        </button>
      </div>

      <p style="text-align:center;font-size:.875rem;color:var(--text-secondary);">
        Don't have an account?
        <a href="register.php" style="color:var(--primary);font-weight:600;">Create one free</a>
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

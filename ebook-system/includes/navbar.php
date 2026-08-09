<?php
// Shared Navbar Include
// Expects: $conn, session_start() already called
$cartCount = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
  }
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? 'User';
$userInitials = strtoupper(substr($userName, 0, 1));
?>

<!-- ========== NAVBAR ========== -->
<nav class="site-navbar" id="siteNavbar">
  <div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between w-100">

      <!-- Logo -->
      <a href="index.php" class="navbar-logo">
        <div class="logo-icon"><i class="fas fa-book-open"></i></div>
        <span class="logo-text">e<span class="logo-highlight">BOOK</span></span>
      </a>

      <!-- Desktop Nav Links -->
      <ul class="navbar-nav-links d-none d-lg-flex">
        <li><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
        <li><a href="books.php"><i class="fas fa-book me-1"></i>Books</a></li>
        <?php if ($isLoggedIn): ?>
        <li><a href="competitions.php"><i class="fas fa-trophy me-1"></i>Competitions</a></li>
        <?php endif; ?>
        <li><a href="about.php"><i class="fas fa-info-circle me-1"></i>About</a></li>
        <li><a href="contact.php"><i class="fas fa-envelope me-1"></i>Contact</a></li>
      </ul>

      <!-- Actions -->
      <div class="navbar-actions">
        <!-- Search (Desktop) -->
        <form action="books.php" method="GET" class="navbar-search d-none d-lg-block">
          <i class="fas fa-search search-icon"></i>
          <input type="text" name="category" placeholder="Search books...">
        </form>

        <!-- Cart -->
        <a href="cart.php" class="cart-icon-btn" aria-label="Shopping cart">
          <i class="fas fa-shopping-cart"></i>
          <?php if ($cartCount > 0): ?>
          <span class="cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>

        <!-- Wishlist icon (UI only) -->
        <a href="#" class="cart-icon-btn d-none d-lg-flex" aria-label="Wishlist" title="Wishlist">
          <i class="far fa-heart"></i>
        </a>

        <!-- Auth -->
        <?php if ($isLoggedIn): ?>
        <div class="user-dropdown d-none d-lg-block">
          <div class="user-dropdown-btn">
            <div class="user-avatar"><?= htmlspecialchars($userInitials) ?></div>
            <?= htmlspecialchars($userName) ?>
            <i class="fas fa-chevron-down ms-1" style="font-size:.7rem;"></i>
          </div>
          <div class="user-dropdown-menu">
            <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="view_orders.php"><i class="fas fa-box"></i>My Orders</a>
            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <a href="admin/dashboard.php"><i class="fas fa-cog"></i>Admin Panel</a>
            <?php endif; ?>
            <div class="divider"></div>
            <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i>Logout</a>
          </div>
        </div>
        <?php else: ?>
        <a href="login.php" class="btn-nav-login d-none d-lg-block">Login</a>
        <a href="register.php" class="btn-nav-register d-none d-lg-block">Sign Up</a>
        <?php endif; ?>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler-btn" aria-label="Toggle menu" id="navbarToggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>

    </div>
  </div>
</nav>

<!-- ========== MOBILE NAV ========== -->
<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-panel">
    <button class="mobile-nav-close" id="mobileNavClose">
      <i class="fas fa-times"></i>
    </button>

    <!-- Mobile Search -->
    <form action="books.php" method="GET" class="mb-4">
      <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4);font-size:.85rem;"></i>
        <input type="text" name="category" placeholder="Search books..."
          style="width:100%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:8px;padding:10px 14px 10px 36px;color:white;font-size:.85rem;outline:none;">
      </div>
    </form>

    <ul class="mobile-nav-links">
      <li><a href="index.php"><i class="fas fa-home"></i>Home</a></li>
      <li><a href="books.php"><i class="fas fa-book"></i>Books</a></li>
      <?php if ($isLoggedIn): ?>
      <li><a href="competitions.php"><i class="fas fa-trophy"></i>Competitions</a></li>
      <?php endif; ?>
      <li><a href="about.php"><i class="fas fa-info-circle"></i>About</a></li>
      <li><a href="contact.php"><i class="fas fa-envelope"></i>Contact</a></li>
      <li><a href="cart.php"><i class="fas fa-shopping-cart"></i>Cart<?= $cartCount > 0 ? " ($cartCount)" : '' ?></a></li>
      <?php if ($isLoggedIn): ?>
      <li><a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
      <li><a href="view_orders.php"><i class="fas fa-box"></i>My Orders</a></li>
      <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
      <li><a href="admin/dashboard.php"><i class="fas fa-cog"></i>Admin Panel</a></li>
      <?php endif; ?>
      <li><a href="logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
      <?php else: ?>
      <li><a href="login.php"><i class="fas fa-sign-in-alt"></i>Login</a></li>
      <li><a href="register.php"><i class="fas fa-user-plus"></i>Sign Up</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>

<script>
  // Mobile nav toggle
  document.getElementById('navbarToggle').addEventListener('click', () => {
    document.getElementById('mobileNav').classList.add('open');
  });
  document.getElementById('mobileNavClose').addEventListener('click', () => {
    document.getElementById('mobileNav').classList.remove('open');
  });
  document.getElementById('mobileNav').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
</script>

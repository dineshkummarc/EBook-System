<?php
// Admin Header — shared include for all admin pages
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
$adminName = $_SESSION['user_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Admin Panel' ?> — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
    :root { --sidebar-w: 240px; }
    .admin-layout { display: flex; min-height: 100vh; }
    .admin-sidebar {
      width: var(--sidebar-w);
      background: var(--dark-bg);
      position: fixed; top: 0; left: 0;
      height: 100vh; overflow-y: auto;
      padding: 0;
      z-index: 1000;
      transition: var(--transition);
      border-right: 1px solid rgba(255,255,255,.06);
    }
    .sidebar-brand {
      display: flex; align-items: center; gap: 10px;
      padding: 20px 24px;
      font-family: 'Poppins', sans-serif;
      font-size: 1.2rem; font-weight: 800;
      color: white;
      border-bottom: 1px solid rgba(255,255,255,.06);
      text-decoration: none;
    }
    .sidebar-brand:hover { color: var(--accent); }
    .sidebar-brand .logo-icon { width:34px;height:34px;background:var(--gradient-btn);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0; }
    .sidebar-brand span.highlight { color: var(--accent); }
    .sidebar-section { padding: 24px 16px 8px; }
    .sidebar-section-label {
      font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;
      color:rgba(255,255,255,.25);padding:0 8px;margin-bottom:6px;
    }
    .admin-nav-link {
      display:flex;align-items:center;gap:12px;
      padding:10px 8px;border-radius:8px;
      color:rgba(255,255,255,.55);text-decoration:none;
      font-size:.85rem;font-weight:500;transition:var(--transition);
      margin-bottom:2px;
    }
    .admin-nav-link i { width:18px;text-align:center;font-size:.875rem;flex-shrink:0; }
    .admin-nav-link:hover { background:rgba(255,255,255,.07);color:white; }
    .admin-nav-link.active { background:var(--primary);color:white; }
    .admin-nav-link.active i { color:var(--accent); }
    .admin-nav-link .nav-badge {
      margin-left:auto;background:var(--danger);color:white;
      font-size:.65rem;font-weight:700;padding:2px 6px;border-radius:50px;
    }

    /* Main area */
    .admin-main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: var(--bg);
    }
    .admin-topbar {
      position: sticky; top: 0; z-index: 999;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 14px 32px;
      display: flex; align-items: center; justify-content: space-between;
      backdrop-filter: blur(10px);
    }
    .topbar-title { font-family:'Poppins',sans-serif;font-size:1.1rem;font-weight:700; }
    .topbar-breadcrumb { font-size:.8rem;color:var(--text-muted);margin-top:2px; }
    .topbar-actions { display:flex;align-items:center;gap:12px; }
    .topbar-btn {
      width:38px;height:38px;border-radius:50%;background:var(--bg);border:1px solid var(--border);
      display:flex;align-items:center;justify-content:center;cursor:pointer;
      color:var(--text-secondary);font-size:.875rem;transition:var(--transition);text-decoration:none;
    }
    .topbar-btn:hover { border-color:var(--primary);color:var(--primary);background:rgba(112,160,175,.05); }
    .admin-user-chip {
      display:flex;align-items:center;gap:8px;
      background:var(--bg);border:1px solid var(--border);border-radius:50px;
      padding:5px 14px 5px 5px;cursor:pointer;transition:var(--transition);
      text-decoration:none;color:var(--text-primary);font-size:.85rem;font-weight:600;
    }
    .admin-user-chip:hover { border-color:var(--primary); }
    .admin-user-avatar {
      width:28px;height:28px;border-radius:50%;background:var(--gradient-btn);
      display:flex;align-items:center;justify-content:center;color:white;font-size:.7rem;font-weight:700;
    }
    .admin-content { padding: 32px; flex: 1; }
    @media (max-width:991px) { .admin-sidebar{display:none;} .admin-main{margin-left:0;} }
  </style>
</head>
<body>
<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <a href="dashboard.php" class="sidebar-brand">
      <div class="logo-icon"><i class="fas fa-book-open text-white"></i></div>
      e<span class="highlight">BOOK</span> Admin
    </a>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>
      <a href="dashboard.php" class="admin-nav-link <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i>Dashboard
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Catalog</div>
      <a href="upload_book.php"  class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'upload_book')  !== false) ? 'active' : '' ?>">
        <i class="fas fa-plus-circle"></i>Upload Book
      </a>
      <a href="view_books.php"   class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'view_books')   !== false) ? 'active' : '' ?>">
        <i class="fas fa-book"></i>All Books
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Commerce</div>
      <a href="view_orders.php"  class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'view_orders')  !== false) ? 'active' : '' ?>">
        <i class="fas fa-box"></i>Orders
      </a>
      <a href="manage_dealers.php" class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'manage_dealers') !== false) ? 'active' : '' ?>">
        <i class="fas fa-store"></i>Dealers
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Community</div>
      <a href="manage_users.php" class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'manage_users') !== false) ? 'active' : '' ?>">
        <i class="fas fa-users"></i>Users
      </a>
      <a href="manage_competitions.php" class="admin-nav-link <?= (strpos($_SERVER['PHP_SELF'], 'manage_competitions') !== false) ? 'active' : '' ?>">
        <i class="fas fa-trophy"></i>Competitions
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Account</div>
      <a href="../index.php"     class="admin-nav-link"><i class="fas fa-globe"></i>View Site</a>
      <a href="../logout.php"    class="admin-nav-link" style="color:rgba(239,68,68,.7);">
        <i class="fas fa-sign-out-alt"></i>Logout
      </a>
    </div>
  </aside>

  <!-- Main Area -->
  <div class="admin-main">
    <!-- Top Bar -->
    <div class="admin-topbar">
      <div>
        <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
        <div class="topbar-breadcrumb">
          Admin Panel &rsaquo; <?= $pageTitle ?? 'Dashboard' ?>
        </div>
      </div>
      <div class="topbar-actions">
        <a href="../index.php" class="topbar-btn" title="View Site"><i class="fas fa-globe"></i></a>
        <a href="../books.php" class="topbar-btn" title="Books"><i class="fas fa-book"></i></a>
        <a href="../logout.php" class="admin-user-chip">
          <div class="admin-user-avatar"><?= strtoupper($adminName[0]) ?></div>
          <?= htmlspecialchars($adminName) ?>
          <i class="fas fa-sign-out-alt ms-1" style="font-size:.75rem;color:var(--danger);"></i>
        </a>
      </div>
    </div>

    <!-- Page Content -->
    <div class="admin-content">

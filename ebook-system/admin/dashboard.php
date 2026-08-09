<?php
session_start();
include '../db_config.php';

$pageTitle = 'Dashboard';

// Auth check inside admin_header.php
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Stats
$totalBooks   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM books"))['cnt']   ?? 0;
$totalOrders  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders"))['cnt']  ?? 0;
$totalUsers   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM accounts WHERE role_id=2"))['cnt'] ?? 0;
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS rev FROM orders WHERE paid=1"))['rev'] ?? 0;
$pendingOrds  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE paid=0"))['cnt'] ?? 0;

// Recent orders
$recentOrders = [];
$rRes = mysqli_query($conn, "SELECT o.*, a.name AS user_name FROM orders o LEFT JOIN accounts a ON o.user_id=a.id ORDER BY o.id DESC LIMIT 10");
if ($rRes) while ($row = mysqli_fetch_assoc($rRes)) $recentOrders[] = $row;

// Recent books
$recentBooks = [];
$bRes = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 5");
if ($bRes) while ($row = mysqli_fetch_assoc($bRes)) $recentBooks[] = $row;

include 'admin_header.php';
?>

<!-- Welcome -->
<div class="row mb-4" data-aos="fade-up">
  <div class="col-12">
    <div style="background:var(--gradient-hero);border-radius:var(--radius-lg);padding:28px 36px;position:relative;overflow:hidden;">
      <div style="position:absolute;width:250px;height:250px;border-radius:50%;background:rgba(255,255,255,.04);right:-40px;top:-40px;"></div>
      <div class="row align-items-center">
        <div class="col-md-8">
          <p style="color:rgba(255,255,255,.65);margin:0;font-size:.85rem;">Good day,</p>
          <h3 style="color:white;font-weight:800;margin:4px 0 8px;">Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?> 👋</h3>
          <p style="color:rgba(255,255,255,.6);margin:0;font-size:.875rem;">Here's what's happening with your eBook store today.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
          <a href="../books.php"      class="btn-accent-custom me-2 btn-ripple" style="font-size:.85rem;padding:10px 18px;"><i class="fas fa-book me-1"></i>View Store</a>
          <a href="upload_book.php"   class="btn-white-custom btn-ripple" style="font-size:.85rem;padding:10px 18px;"><i class="fas fa-plus me-1"></i>Add Book</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-5">
  <?php
  $stats = [
    ['icon'=>'fas fa-book',         'wrap'=>'blue',  'val'=>$totalBooks,               'label'=>'Total Books',       'link'=>'view_books.php'],
    ['icon'=>'fas fa-box',          'wrap'=>'gold',  'val'=>$totalOrders,              'label'=>'Total Orders',      'link'=>'view_orders.php'],
    ['icon'=>'fas fa-users',        'wrap'=>'green', 'val'=>$totalUsers,               'label'=>'Registered Users',  'link'=>'manage_users.php'],
    ['icon'=>'fas fa-rupee-sign',   'wrap'=>'red',   'val'=>'Rs.'.number_format($totalRevenue),'label'=>'Revenue (Paid)', 'link'=>'view_orders.php'],
    ['icon'=>'fas fa-clock',        'wrap'=>'gold',  'val'=>$pendingOrds,              'label'=>'Pending Orders',    'link'=>'view_orders.php'],
  ];
  foreach ($stats as $idx => $s):
  ?>
  <div class="col-sm-6 col-lg-<?= $idx < 4 ? '3' : '12' ?>" data-aos="fade-up" data-aos-delay="<?= $idx*60 ?>">
    <a href="<?= $s['link'] ?>" style="text-decoration:none;">
      <div class="dashboard-stat-card" style="cursor:pointer;">
        <div class="stat-icon-wrap <?= $s['wrap'] ?>"><i class="<?= $s['icon'] ?>"></i></div>
        <div>
          <div class="stat-value"><?= is_numeric($s['val']) ? number_format($s['val']) : $s['val'] ?></div>
          <div class="stat-label"><?= $s['label'] ?></div>
        </div>
        <i class="fas fa-arrow-right ms-auto" style="color:var(--text-muted);font-size:.75rem;"></i>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-5" data-aos="fade-up">
  <div class="col-12">
    <h6 style="font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:1px;font-size:.75rem;margin-bottom:14px;">Quick Actions</h6>
  </div>
  <?php
  $actions = [
    ['icon'=>'fas fa-plus-circle',   'title'=>'Upload Book',   'href'=>'upload_book.php',          'color'=>'var(--primary)'],
    ['icon'=>'fas fa-book',          'title'=>'Manage Books',  'href'=>'view_books.php',           'color'=>'var(--secondary)'],
    ['icon'=>'fas fa-box',           'title'=>'All Orders',    'href'=>'view_orders.php',          'color'=>'var(--success)'],
    ['icon'=>'fas fa-users',         'title'=>'Users',         'href'=>'manage_users.php',         'color'=>'var(--warning)'],
    ['icon'=>'fas fa-store',         'title'=>'Dealers',       'href'=>'manage_dealers.php',       'color'=>'var(--accent)'],
    ['icon'=>'fas fa-trophy',        'title'=>'Competitions',  'href'=>'manage_competitions.php',  'color'=>'var(--danger)'],
  ];
  foreach ($actions as $a):
  ?>
  <div class="col-6 col-md-4 col-lg-2">
    <a href="<?= $a['href'] ?>" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px 12px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);text-decoration:none;color:var(--text-primary);transition:var(--transition);"
      onmouseover="this.style.borderColor=this.dataset.color;this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)'"
      onmouseout="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow=''"
      data-color="<?= $a['color'] ?>">
      <div style="width:44px;height:44px;border-radius:10px;background:<?= $a['color'] ?>18;display:flex;align-items:center;justify-content:center;color:<?= $a['color'] ?>;font-size:1.1rem;">
        <i class="<?= $a['icon'] ?>"></i>
      </div>
      <span style="font-size:.8rem;font-weight:600;text-align:center;"><?= $a['title'] ?></span>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<!-- Recent Orders + Recent Books -->
<div class="row g-4">
  <!-- Recent Orders Table -->
  <div class="col-lg-8" data-aos="fade-right">
    <div class="dashboard-card">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="dashboard-card-title mb-0"><i class="fas fa-box"></i>Recent Orders</div>
        <a href="view_orders.php" style="font-size:.8rem;color:var(--secondary);">View all <i class="fas fa-arrow-right ms-1"></i></a>
      </div>
      <?php if (empty($recentOrders)): ?>
      <div style="text-align:center;padding:32px;color:var(--text-muted);">
        <i class="fas fa-inbox fa-2x mb-3 d-block" style="color:var(--border);"></i>
        <p>No orders yet.</p>
      </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#ID</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentOrders as $order):
              $paid    = $order['paid']   ?? 0;
              $status  = $order['status'] ?? 'Pending';
            ?>
            <tr>
              <td><span style="font-weight:700;color:var(--primary);">#<?= $order['id'] ?></span></td>
              <td style="font-weight:500;"><?= htmlspecialchars($order['user_name'] ?? 'N/A') ?></td>
              <td>
                <div style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.8rem;color:var(--text-secondary);">
                  <?= htmlspecialchars($order['items'] ?? '') ?>
                </div>
              </td>
              <td style="font-weight:700;">Rs. <?= number_format($order['total']) ?></td>
              <td style="font-size:.8rem;"><?= htmlspecialchars($order['payment_method'] ?? '') ?></td>
              <td>
                <span class="status-pill <?= $paid ? 'delivered' : ($status === 'Cancelled' ? 'cancelled' : 'pending') ?>">
                  <?= $paid ? 'Paid' : htmlspecialchars($status) ?>
                </span>
              </td>
              <td>
                <div style="display:flex;gap:6px;">
                  <?php if (!$paid): ?>
                  <a href="mark_paid.php?id=<?= $order['id'] ?>" class="topbar-btn" title="Mark as Paid"
                     onclick="return confirm('Mark order #<?= $order['id'] ?> as paid?')"
                     style="background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.2);color:var(--success);">
                    <i class="fas fa-check"></i>
                  </a>
                  <?php endif; ?>
                  <a href="delete_order.php?id=<?= $order['id'] ?>" class="topbar-btn" title="Delete"
                     onclick="return confirm('Delete this order?')"
                     style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.15);color:var(--danger);">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Books -->
  <div class="col-lg-4" data-aos="fade-left">
    <div class="dashboard-card">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="dashboard-card-title mb-0"><i class="fas fa-book"></i>Recent Books</div>
        <a href="upload_book.php" style="font-size:.8rem;color:var(--secondary);">Add new <i class="fas fa-plus ms-1"></i></a>
      </div>
      <?php if (empty($recentBooks)): ?>
      <div style="text-align:center;padding:32px;color:var(--text-muted);">
        <i class="fas fa-book fa-2x mb-3 d-block" style="color:var(--border);"></i>
        <p>No books uploaded yet.</p>
        <a href="upload_book.php" class="btn-primary-custom btn-ripple" style="font-size:.8rem;padding:8px 18px;">Upload First Book</a>
      </div>
      <?php else: ?>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($recentBooks as $book): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
          <div style="width:40px;height:50px;background:var(--gradient-section);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">📖</div>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($book['title']) ?></div>
            <div style="font-size:.72rem;color:var(--text-muted);"><?= htmlspecialchars($book['category'] ?? '') ?></div>
            <div style="font-weight:700;font-size:.8rem;color:var(--primary);">Rs. <?= number_format($book['price']) ?></div>
          </div>
          <a href="edit_book.php?id=<?= $book['id'] ?>" class="topbar-btn" style="flex-shrink:0;" title="Edit">
            <i class="fas fa-edit"></i>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

<?php
session_start();
include '../db_config.php';
$pageTitle = 'Manage Orders';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

$message = $_SESSION['admin_message'] ?? ''; unset($_SESSION['admin_message']);

$orders = [];
$result = mysqli_query($conn, "SELECT o.*, a.name AS user_name, a.email AS user_email FROM orders o LEFT JOIN accounts a ON o.user_id=a.id ORDER BY o.id DESC");
if ($result) while ($row = mysqli_fetch_assoc($result)) $orders[] = $row;

include 'admin_header.php';
?>

<?php if ($message): ?>
<div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-4 mb-4">
  <?php
  $total   = count($orders);
  $paid    = count(array_filter($orders, fn($o) => $o['paid']));
  $pending = $total - $paid;
  $revenue = array_sum(array_map(fn($o) => $o['paid'] ? $o['total'] : 0, $orders));
  $items   = [
    ['icon'=>'fas fa-box',        'wrap'=>'blue',  'val'=>$total,   'label'=>'Total Orders'],
    ['icon'=>'fas fa-check',      'wrap'=>'green', 'val'=>$paid,    'label'=>'Paid'],
    ['icon'=>'fas fa-clock',      'wrap'=>'gold',  'val'=>$pending, 'label'=>'Pending'],
    ['icon'=>'fas fa-rupee-sign', 'wrap'=>'red',   'val'=>'Rs.'.number_format($revenue),'label'=>'Revenue'],
  ];
  foreach ($items as $s):
  ?>
  <div class="col-sm-6 col-lg-3" data-aos="fade-up">
    <div class="dashboard-stat-card">
      <div class="stat-icon-wrap <?= $s['wrap'] ?>"><i class="<?= $s['icon'] ?>"></i></div>
      <div><div class="stat-value"><?= is_numeric($s['val']) ? number_format($s['val']) : $s['val'] ?></div><div class="stat-label"><?= $s['label'] ?></div></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="dashboard-card">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="dashboard-card-title mb-0"><i class="fas fa-box"></i>All Orders</div>
    <div class="d-flex gap-2">
      <input type="text" id="orderSearch" class="form-input" placeholder="Search orders..." style="width:200px;padding:8px 14px;font-size:.85rem;" oninput="filterTable(this.value)">
    </div>
  </div>

  <?php if (empty($orders)): ?>
  <div style="text-align:center;padding:40px;color:var(--text-muted);">
    <i class="fas fa-inbox fa-2x mb-3 d-block" style="color:var(--border);"></i>
    <p>No orders yet.</p>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table" id="ordersTable">
      <thead>
        <tr>
          <th>#ID</th><th>Customer</th><th>Email</th><th>Items</th>
          <th>Total</th><th>Payment</th><th>Date</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order):
          $paid   = $order['paid']   ?? 0;
          $status = $order['status'] ?? 'Pending';
        ?>
        <tr>
          <td><span style="font-weight:700;color:var(--primary);">#<?= $order['id'] ?></span></td>
          <td style="font-weight:500;"><?= htmlspecialchars($order['user_name'] ?? 'N/A') ?></td>
          <td style="font-size:.8rem;color:var(--text-muted);"><?= htmlspecialchars($order['user_email'] ?? '') ?></td>
          <td style="max-width:180px;">
            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.8rem;">
              <?= htmlspecialchars($order['items'] ?? '') ?>
            </div>
          </td>
          <td style="font-weight:700;">Rs. <?= number_format($order['total']) ?></td>
          <td style="font-size:.8rem;"><?= htmlspecialchars($order['payment_method'] ?? '') ?></td>
          <td style="font-size:.8rem;color:var(--text-muted);"><?= htmlspecialchars($order['order_date'] ?? '') ?></td>
          <td>
            <span class="status-pill <?= $paid ? 'delivered' : ($status === 'Cancelled' ? 'cancelled' : 'pending') ?>">
              <?= $paid ? 'Paid' : htmlspecialchars($status) ?>
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <?php if (!$paid): ?>
              <a href="mark_paid.php?id=<?= $order['id'] ?>" class="topbar-btn" title="Mark Paid"
                 onclick="return confirm('Mark order #<?= $order['id'] ?> as paid?')"
                 style="background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.2);color:var(--success);">
                <i class="fas fa-check"></i>
              </a>
              <?php endif; ?>
              <a href="delete_order.php?id=<?= $order['id'] ?>" class="topbar-btn" title="Delete"
                 onclick="return confirm('Delete order #<?= $order['id'] ?>?')"
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

<?php include 'admin_footer.php'; ?>
<script>
function filterTable(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>

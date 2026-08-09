<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];

$message = $_SESSION['message'] ?? ''; unset($_SESSION['message']);

$orders = [];
$result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC");
if ($result) while ($row = mysqli_fetch_assoc($result)) $orders[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="orders-section">
  <div class="container">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3" data-aos="fade-up">
      <div>
        <div class="breadcrumb-nav mb-2" style="justify-content:flex-start;color:var(--text-muted);">
          <a href="index.php" style="color:var(--secondary);">Home</a><span>/</span>
          <a href="user_dashboard.php" style="color:var(--secondary);">Dashboard</a><span>/</span>
          <span>My Orders</span>
        </div>
        <h2 class="section-title mb-0">
          <i class="fas fa-box me-2" style="color:var(--primary);font-size:1.6rem;"></i>My Orders
        </h2>
      </div>
      <a href="books.php" class="btn-primary-custom btn-ripple">
        <i class="fas fa-plus me-1"></i>Add Books
      </a>
    </div>

    <?php if ($message): ?>
    <div class="alert-premium success mb-4 auto-dismiss" data-aos="fade-up">
      <i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
    <!-- Empty -->
    <div class="text-center py-5" data-aos="fade-up">
      <div style="font-size:5rem;margin-bottom:20px;">📦</div>
      <h3>No orders yet</h3>
      <p class="text-muted mb-4">You haven't placed any orders. Start exploring our amazing library!</p>
      <a href="books.php" class="btn-primary-custom btn-ripple">
        <i class="fas fa-book-open me-1"></i>Browse Books
      </a>
    </div>
    <?php else: ?>

    <!-- Filters (UI) -->
    <div class="d-flex flex-wrap gap-2 mb-4" data-aos="fade-up">
      <button class="filter-tab active" onclick="filterOrders('all', this)" style="cursor:pointer;border:none;">
        All Orders (<?= count($orders) ?>)
      </button>
      <button class="filter-tab" onclick="filterOrders('paid', this)" style="cursor:pointer;border:none;">
        <i class="fas fa-check me-1"></i>Paid
      </button>
      <button class="filter-tab" onclick="filterOrders('pending', this)" style="cursor:pointer;border:none;">
        <i class="fas fa-clock me-1"></i>Pending
      </button>
    </div>

    <!-- Orders List -->
    <div id="ordersList">
      <?php foreach ($orders as $idx => $order):
        $status = $order['status'] ?? 'Pending';
        $paid   = $order['paid']   ?? 0;
        $isPaid = $paid ? 'paid' : 'pending';
      ?>
      <div class="order-card" data-status="<?= $isPaid ?>" data-aos="fade-up" data-aos-delay="<?= ($idx % 3) * 60 ?>">
        <div class="order-card-header">
          <div>
            <div class="order-id"><i class="fas fa-hashtag me-1" style="color:var(--primary);"></i>Order <?= $order['id'] ?></div>
            <div class="order-date">
              <i class="fas fa-calendar-alt me-1"></i>
              <?= htmlspecialchars($order['order_date'] ?? 'Date not available') ?>
            </div>
          </div>
          <span class="status-pill <?= $paid ? 'delivered' : ($status === 'Cancelled' ? 'cancelled' : 'pending') ?>">
            <i class="fas fa-<?= $paid ? 'check' : ($status === 'Cancelled' ? 'times' : 'clock') ?> me-1"></i>
            <?= $paid ? 'Paid & Confirmed' : htmlspecialchars($status) ?>
          </span>
        </div>

        <div class="order-card-body">
          <div class="row g-3 align-items-center">
            <div class="col-md-6">
              <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:6px;">Items Ordered</div>
              <p style="font-size:.875rem;margin:0;color:var(--text-secondary);">
                <?= htmlspecialchars($order['items']) ?>
              </p>
            </div>
            <div class="col-md-3">
              <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:6px;">Payment</div>
              <div style="font-size:.875rem;font-weight:600;">
                <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?>
              </div>
              <div class="d-flex align-items-center gap-2 mt-1">
                <span class="status-pill <?= $paid ? 'delivered' : 'pending' ?>" style="font-size:.65rem;padding:3px 8px;">
                  <?= $paid ? 'Paid' : 'Awaiting Payment' ?>
                </span>
              </div>
            </div>
            <div class="col-md-3 text-md-end">
              <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:6px;">Order Total</div>
              <div style="font-family:Poppins,sans-serif;font-size:1.3rem;font-weight:800;color:var(--primary);">
                Rs. <?= number_format($order['total']) ?>
              </div>
              <?php if ($paid): ?>
              <a href="download_book.php?order_id=<?= $order['id'] ?>" class="btn-primary-custom btn-ripple mt-2"
                 style="padding:8px 16px;font-size:.8rem;justify-content:center;text-align:center;">
                <i class="fas fa-download me-1"></i>Download PDF
              </a>
              <?php else: ?>
              <div style="font-size:.78rem;color:var(--text-muted);margin-top:6px;">
                <i class="fas fa-info-circle me-1"></i>Processing...
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<style>
  .filter-tab { background:var(--surface);border:1.5px solid var(--border);border-radius:50px;padding:8px 18px;font-size:.85rem;font-weight:600;cursor:pointer;transition:var(--transition);color:var(--text-secondary);text-decoration:none; }
  .filter-tab:hover,.filter-tab.active{background:var(--primary);border-color:var(--primary);color:white;}
</style>
<script>
function filterOrders(type, btn) {
  document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.order-card').forEach(card => {
    if (type === 'all' || card.dataset.status === type) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>
</body>
</html>

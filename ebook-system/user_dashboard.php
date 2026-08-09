<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];

$message = $_SESSION['message'] ?? ''; unset($_SESSION['message']);

// Orders count
$ordersResult = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE user_id=$user_id");
$ordersCount  = mysqli_fetch_assoc($ordersResult)['cnt'] ?? 0;

// Paid orders
$paidResult = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE user_id=$user_id AND paid=1");
$paidCount  = mysqli_fetch_assoc($paidResult)['cnt'] ?? 0;

// Recent orders
$recentOrders = [];
$rResult = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC LIMIT 5");
if ($rResult) while ($row = mysqli_fetch_assoc($rResult)) $recentOrders[] = $row;

$initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($user_name)))));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div style="padding-top:80px;min-height:100vh;background:var(--bg);">
  <div class="container py-4">

    <?php if ($message): ?>
    <div class="alert-premium success mb-4 auto-dismiss">
      <i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <!-- Welcome Banner -->
    <div class="row mb-4" data-aos="fade-up">
      <div class="col-12">
        <div style="background:var(--gradient-hero);border-radius:var(--radius-lg);padding:36px 40px;position:relative;overflow:hidden;">
          <div style="position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.05);right:-60px;top:-60px;"></div>
          <div style="position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(244,232,193,.1);left:-40px;bottom:-40px;"></div>
          <div class="row align-items-center" style="position:relative;z-index:1;">
            <div class="col-md-8">
              <div style="display:flex;align-items:center;gap:20px;">
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.15);border:3px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;color:white;font-family:Poppins,sans-serif;">
                  <?= htmlspecialchars($initials) ?>
                </div>
                <div>
                  <p style="color:rgba(255,255,255,.7);margin:0;font-size:.85rem;">Welcome back,</p>
                  <h2 style="color:white;margin:4px 0;font-size:1.8rem;font-weight:800;"><?= htmlspecialchars($user_name) ?> 👋</h2>
                  <p style="color:rgba(255,255,255,.6);margin:0;font-size:.85rem;">
                    <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <a href="books.php" class="btn-accent-custom btn-ripple">
                <i class="fas fa-book-open me-1"></i>Browse Books
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-lg-3" data-aos="fade-up">
        <div class="dashboard-stat-card">
          <div class="stat-icon-wrap blue"><i class="fas fa-box"></i></div>
          <div>
            <div class="stat-value"><?= $ordersCount ?></div>
            <div class="stat-label">Total Orders</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
        <div class="dashboard-stat-card">
          <div class="stat-icon-wrap green"><i class="fas fa-check-circle"></i></div>
          <div>
            <div class="stat-value"><?= $paidCount ?></div>
            <div class="stat-label">Paid Orders</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
        <div class="dashboard-stat-card">
          <div class="stat-icon-wrap gold"><i class="fas fa-shopping-cart"></i></div>
          <div>
            <div class="stat-value"><?= $cartCount ?></div>
            <div class="stat-label">Cart Items</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
        <div class="dashboard-stat-card">
          <div class="stat-icon-wrap red"><i class="fas fa-trophy"></i></div>
          <div>
            <div class="stat-value">0</div>
            <div class="stat-label">Competitions Won</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions + Recent Orders -->
    <div class="row g-4">
      <!-- Quick Actions -->
      <div class="col-lg-4" data-aos="fade-right">
        <div class="dashboard-card h-100">
          <div class="dashboard-card-title"><i class="fas fa-bolt"></i>Quick Actions</div>
          <div class="d-flex flex-column gap-3">
            <a href="books.php" style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);text-decoration:none;color:var(--text-primary);transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div style="width:40px;height:40px;background:rgba(112,160,175,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary);">
                <i class="fas fa-book-open"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.875rem;">Browse Books</div>
                <div style="font-size:.75rem;color:var(--text-muted);">Explore our library</div>
              </div>
              <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:.75rem;"></i>
            </a>
            <a href="view_orders.php" style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);text-decoration:none;color:var(--text-primary);transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div style="width:40px;height:40px;background:rgba(160,193,185,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--secondary);">
                <i class="fas fa-box"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.875rem;">My Orders</div>
                <div style="font-size:.75rem;color:var(--text-muted);">View order history</div>
              </div>
              <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:.75rem;"></i>
            </a>
            <a href="cart.php" style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);text-decoration:none;color:var(--text-primary);transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div style="width:40px;height:40px;background:rgba(244,232,193,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--accent);">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.875rem;">Shopping Cart</div>
                <div style="font-size:.75rem;color:var(--text-muted);"><?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?> in cart</div>
              </div>
              <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:.75rem;"></i>
            </a>
            <a href="competitions.php" style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);text-decoration:none;color:var(--text-primary);transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div style="width:40px;height:40px;background:rgba(239,68,68,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--danger);">
                <i class="fas fa-trophy"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.875rem;">Competitions</div>
                <div style="font-size:.75rem;color:var(--text-muted);">Enter & win prizes</div>
              </div>
              <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:.75rem;"></i>
            </a>
            <a href="logout.php" style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(239,68,68,.05);border-radius:var(--radius-sm);border:1px solid rgba(239,68,68,.15);text-decoration:none;color:var(--danger);">
              <div style="width:40px;height:40px;background:rgba(239,68,68,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-sign-out-alt"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.875rem;">Logout</div>
                <div style="font-size:.75rem;color:rgba(239,68,68,.6);">Sign out of your account</div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="col-lg-8" data-aos="fade-left">
        <div class="dashboard-card">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="dashboard-card-title mb-0"><i class="fas fa-history"></i>Recent Orders</div>
            <a href="view_orders.php" style="font-size:.8rem;color:var(--secondary);">View all <i class="fas fa-arrow-right ms-1"></i></a>
          </div>

          <?php if (empty($recentOrders)): ?>
          <div style="text-align:center;padding:40px;color:var(--text-muted);">
            <i class="fas fa-box-open fa-2x mb-3 d-block" style="color:var(--border);"></i>
            <p class="mb-3">No orders placed yet.</p>
            <a href="books.php" class="btn-primary-custom btn-ripple" style="font-size:.85rem;padding:10px 20px;">
              <i class="fas fa-book-open me-1"></i>Start Shopping
            </a>
          </div>
          <?php else: ?>
          <div class="table-responsive">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
              <thead>
                <tr style="background:var(--bg);">
                  <th style="padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-secondary);border-bottom:1px solid var(--border);">Order ID</th>
                  <th style="padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-secondary);border-bottom:1px solid var(--border);">Items</th>
                  <th style="padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-secondary);border-bottom:1px solid var(--border);">Total</th>
                  <th style="padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-secondary);border-bottom:1px solid var(--border);">Status</th>
                  <th style="padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-secondary);border-bottom:1px solid var(--border);">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr style="border-bottom:1px solid var(--border);">
                  <td style="padding:12px 14px;font-weight:700;color:var(--primary);">#<?= $order['id'] ?></td>
                  <td style="padding:12px 14px;max-width:200px;">
                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.82rem;">
                      <?= htmlspecialchars(substr($order['items'], 0, 40)) ?>...
                    </div>
                  </td>
                  <td style="padding:12px 14px;font-weight:600;">Rs. <?= number_format($order['total']) ?></td>
                  <td style="padding:12px 14px;">
                    <?php
                    $status = $order['status'] ?? 'Pending';
                    $paid   = $order['paid']   ?? 0;
                    $cls    = $paid ? 'delivered' : ($status === 'Cancelled' ? 'cancelled' : 'pending');
                    ?>
                    <span class="status-pill <?= $cls ?>"><?= $paid ? 'Paid' : htmlspecialchars($status) ?></span>
                  </td>
                  <td style="padding:12px 14px;">
                    <?php if ($order['paid']): ?>
                    <a href="download_book.php?order_id=<?= $order['id'] ?>" class="btn-primary-custom btn-ripple" style="padding:6px 14px;font-size:.75rem;">
                      <i class="fas fa-download"></i>
                    </a>
                    <?php else: ?>
                    <span style="font-size:.75rem;color:var(--text-muted);">Pending</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
session_start();
include 'db_config.php';

// Handle quantity update
if (isset($_POST['update_qty'])) {
    $title = $_POST['title'];
    $qty   = max(1, (int)$_POST['qty']);
    if (isset($_SESSION['cart'][$title])) {
        $_SESSION['cart'][$title]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// Handle remove item
if (isset($_GET['remove'])) {
    $key = urldecode($_GET['remove']);
    unset($_SESSION['cart'][$key]);
    header("Location: cart.php");
    exit();
}

// Handle clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

$cart  = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $item) $cartCount += $item['quantity'];

$subtotal      = 0;
$totalShipping = 0;
$totalWeight   = 0;

foreach ($cart as $item) {
    $qty           = $item['quantity'] ?? 1;
    $subtotal     += ($item['price'] ?? 0) * $qty;
    $totalShipping += ($item['shipping'] ?? 0) * $qty;
    $totalWeight  += ($item['weight']   ?? 0) * $qty;
}

$grandTotal = $subtotal + $totalShipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="cart-section">
  <div class="container">
    <div class="section-header text-start mb-4" data-aos="fade-up">
      <div class="breadcrumb-nav mb-2" style="justify-content:flex-start;color:var(--text-muted);">
        <a href="index.php" style="color:var(--secondary);">Home</a><span>/</span>
        <span>Shopping Cart</span>
      </div>
      <h2 class="section-title mb-0">
        <i class="fas fa-shopping-cart me-2" style="color:var(--primary);font-size:1.6rem;"></i>
        Your Cart
        <?php if ($cartCount > 0): ?>
        <span class="ms-2" style="background:var(--primary);color:white;font-size:1rem;padding:4px 14px;border-radius:50px;"><?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?></span>
        <?php endif; ?>
      </h2>
    </div>

    <?php if (empty($cart)): ?>
    <!-- Empty Cart -->
    <div class="text-center py-5" data-aos="fade-up">
      <div style="font-size:6rem;margin-bottom:20px;">🛒</div>
      <h3>Your cart is empty</h3>
      <p class="text-muted mb-4">Looks like you haven't added any books yet. Start exploring our library!</p>
      <a href="books.php" class="btn-primary-custom btn-ripple">
        <i class="fas fa-book-open me-1"></i>Browse Books
      </a>
    </div>

    <?php else: ?>
    <div class="row g-4">
      <!-- Cart Items -->
      <div class="col-lg-8">

        <!-- Cart Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <p class="text-muted mb-0" style="font-size:.875rem;"><?= count($cart) ?> item<?= count($cart) !== 1 ? 's' : '' ?> in your cart</p>
          <a href="cart.php?clear=1" class="btn btn-sm btn-outline-danger"
             onclick="return confirm('Remove all items from cart?')">
            <i class="fas fa-trash me-1"></i>Clear Cart
          </a>
        </div>

        <!-- Items -->
        <?php foreach ($cart as $title => $item):
          $qty       = $item['quantity'] ?? 1;
          $itemTotal = ($item['price'] ?? 0) * $qty;
        ?>
        <div class="cart-item-card" data-aos="fade-up">
          <!-- Book Icon / Cover -->
          <div class="cart-item-cover">📚</div>

          <!-- Details -->
          <div class="flex-grow-1">
            <div class="cart-item-title"><?= htmlspecialchars($title) ?></div>
            <div class="cart-item-format">
              <?= htmlspecialchars($item['format'] ?? 'PDF') ?> &bull;
              Weight: <?= $item['weight'] ?? 'N/A' ?> kg
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <div class="cart-item-price">Rs. <?= number_format($item['price'] ?? 0) ?></div>
              <span style="font-size:.8rem;color:var(--text-muted);">+ Rs. <?= number_format($item['shipping'] ?? 0) ?> shipping</span>
            </div>
          </div>

          <!-- Qty + Remove -->
          <div class="d-flex flex-column align-items-end gap-3">
            <form action="cart.php" method="POST" style="display:flex;align-items:center;gap:8px;">
              <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
              <div class="qty-control">
                <button type="button" class="qty-btn" onclick="this.form.qty.value=Math.max(1,parseInt(this.form.qty.value)-1);this.form.submit()">
                  <i class="fas fa-minus" style="font-size:.7rem;"></i>
                </button>
                <input type="number" name="qty" class="qty-value" value="<?= $qty ?>" min="1" max="99"
                  style="border:none;outline:none;width:50px;font-weight:700;font-size:.9rem;">
                <button type="button" class="qty-btn" onclick="this.form.qty.value=Math.min(99,parseInt(this.form.qty.value)+1);this.form.submit()">
                  <i class="fas fa-plus" style="font-size:.7rem;"></i>
                </button>
              </div>
              <input type="hidden" name="update_qty" value="1">
            </form>
            <div style="font-family:Poppins,sans-serif;font-weight:700;font-size:1.05rem;color:var(--primary);">
              Rs. <?= number_format($itemTotal) ?>
            </div>
            <a href="cart.php?remove=<?= urlencode($title) ?>" class="text-danger" style="font-size:.8rem;" title="Remove item">
              <i class="fas fa-trash-alt me-1"></i>Remove
            </a>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Continue Shopping -->
        <div class="mt-3">
          <a href="books.php" class="btn-secondary-custom" style="font-size:.85rem;padding:10px 20px;">
            <i class="fas fa-arrow-left me-1"></i>Continue Shopping
          </a>
        </div>
      </div>

      <!-- Cart Summary -->
      <div class="col-lg-4" data-aos="fade-left">
        <div class="cart-summary">
          <div class="cart-summary-title">Order Summary</div>

          <div class="cart-summary-row">
            <span>Subtotal (<?= $cartCount ?> items)</span>
            <span>Rs. <?= number_format($subtotal) ?></span>
          </div>
          <div class="cart-summary-row">
            <span>Shipping Charges</span>
            <span>Rs. <?= number_format($totalShipping) ?></span>
          </div>
          <div class="cart-summary-row">
            <span>Total Weight</span>
            <span><?= number_format($totalWeight, 2) ?> kg</span>
          </div>
          <div class="cart-summary-row total">
            <span>Grand Total</span>
            <span>Rs. <?= number_format($grandTotal) ?></span>
          </div>

          <!-- Coupon Code (UI Only) -->
          <div class="coupon-input-group">
            <input type="text" placeholder="Coupon code...">
            <button type="button" onclick="showToast('Coupon feature coming soon!','info')">Apply</button>
          </div>

          <div class="mt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="checkout.php" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
              <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
            </a>
            <?php else: ?>
            <a href="login.php?redirect=checkout.php" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
              <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
            </a>
            <?php endif; ?>
          </div>

          <!-- Trust Badges -->
          <div class="mt-4 pt-3" style="border-top:1px solid var(--border);">
            <div class="d-flex flex-column gap-2">
              <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
                <i class="fas fa-shield-alt" style="color:var(--success);"></i>Secure & encrypted checkout
              </div>
              <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
                <i class="fas fa-truck" style="color:var(--primary);"></i>Home delivery across Pakistan
              </div>
              <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
                <i class="fas fa-undo" style="color:var(--secondary);"></i>7-day return guarantee
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>

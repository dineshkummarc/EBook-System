<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php?redirect=checkout.php"); exit(); }
if (empty($_SESSION['cart']))     { header("Location: cart.php"); exit(); }

$cart = $_SESSION['cart'];
$cartCount = 0;
foreach ($cart as $item) $cartCount += $item['quantity'];

$subtotal      = 0;
$totalShipping = 0;
foreach ($cart as $item) {
    $qty = $item['quantity'] ?? 1;
    $subtotal     += ($item['price']    ?? 0) * $qty;
    $totalShipping += ($item['shipping'] ?? 0) * $qty;
}
$grandTotal = $subtotal + $totalShipping;

$user_id = $_SESSION['user_id'];
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name      = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $phone          = mysqli_real_escape_string($conn, $_POST['phone']);
    $address        = mysqli_real_escape_string($conn, $_POST['address']);
    $city           = mysqli_real_escape_string($conn, $_POST['city']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    // Build items string
    $items_list = '';
    foreach ($cart as $title => $item) {
        $qty = $item['quantity'] ?? 1;
        $items_list .= "$title (x$qty), ";
    }
    $items_list = rtrim($items_list, ', ');

    $sql = "INSERT INTO orders (user_id, items, total, payment_method, paid, status, order_date, full_name, email, phone, address, city)
            VALUES ($user_id, '$items_list', $grandTotal, '$payment_method', 0, 'Pending', NOW(), '$full_name', '$email', '$phone', '$address', '$city')";

    // Note: columns full_name/email/phone/address/city may not exist — fall back gracefully
    $insert_ok = mysqli_query($conn, $sql);
    if (!$insert_ok) {
        // Fallback without extra columns
        $sql2 = "INSERT INTO orders (user_id, items, total, payment_method, paid, status, order_date)
                 VALUES ($user_id, '$items_list', $grandTotal, '$payment_method', 0, 'Pending', NOW())";
        $insert_ok = mysqli_query($conn, $sql2);
    }

    if ($insert_ok) {
        $order_id = mysqli_insert_id($conn);

        // Send email via PHPMailer if available
        $mailerPath = __DIR__ . '/PHPMailer/src/PHPMailer.php';
        if (file_exists($mailerPath)) {
            require $mailerPath;
            require __DIR__ . '/PHPMailer/src/SMTP.php';
            require __DIR__ . '/PHPMailer/src/Exception.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'ebook.systems3@gmail.com';
                $mail->Password   = 'ebooksystems456';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom('ebook.systems3@gmail.com', 'eBook Hub');
                $mail->addAddress($email, $full_name);
                $mail->isHTML(true);
                $mail->Subject = "Order #$order_id Confirmed — eBook Hub";
                $mail->Body    = "<h2>Thank you for your order!</h2>
                    <p>Hi $full_name,</p>
                    <p>Your order <strong>#$order_id</strong> has been received and is being processed.</p>
                    <p><strong>Items:</strong> $items_list</p>
                    <p><strong>Total:</strong> Rs. ".number_format($grandTotal)."</p>
                    <p><strong>Payment:</strong> $payment_method</p>
                    <p>You will be notified once your order is confirmed and shipped.</p>
                    <p>— eBook Hub Team</p>";
                $mail->send();
            } catch (Exception $e) { /* Mail failed silently */ }
        }

        // Clear cart
        unset($_SESSION['cart']);
        $_SESSION['message']  = "Order #$order_id placed successfully! We'll contact you soon.";
        $_SESSION['order_id'] = $order_id;
        header("Location: view_orders.php");
        exit();
    } else {
        $error = "Order could not be placed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="checkout-section">
  <div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav mb-3" style="justify-content:flex-start;color:var(--text-muted);" data-aos="fade-up">
      <a href="index.php" style="color:var(--secondary);">Home</a><span>/</span>
      <a href="cart.php" style="color:var(--secondary);">Cart</a><span>/</span>
      <span>Checkout</span>
    </div>
    <h2 class="section-title mb-4" data-aos="fade-up"><i class="fas fa-credit-card me-2" style="color:var(--primary);"></i>Secure Checkout</h2>

    <!-- Step Indicator -->
    <div class="step-indicator" data-step="2" data-aos="fade-up">
      <div class="step-item">
        <div class="step-circle done"><i class="fas fa-check" style="font-size:.7rem;"></i></div>
        <div class="step-label" style="color:var(--success);">Cart</div>
      </div>
      <div class="step-connector done"></div>
      <div class="step-item">
        <div class="step-circle active">2</div>
        <div class="step-label active">Details</div>
      </div>
      <div class="step-connector"></div>
      <div class="step-item">
        <div class="step-circle">3</div>
        <div class="step-label">Confirm</div>
      </div>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert-premium error mb-4 auto-dismiss">
      <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Checkout Form -->
      <div class="col-lg-7" data-aos="fade-right">
        <form method="POST" action="" data-validate id="checkoutForm">
          <!-- Customer Info -->
          <div class="checkout-form-card mb-4">
            <h5 style="font-weight:700;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--border);">
              <i class="fas fa-user me-2" style="color:var(--primary);"></i>Customer Information
            </h5>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label-float">Full Name *</label>
                <input type="text" name="full_name" class="form-input" placeholder="Your full name" required
                  value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label-float">Email Address *</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required
                  value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label-float">Phone Number *</label>
                <input type="tel" name="phone" class="form-input" placeholder="+92 300 0000000" required>
              </div>
              <div class="col-12">
                <label class="form-label-float">Delivery Address *</label>
                <textarea name="address" class="form-input" rows="2" placeholder="Street, area, landmark..." required style="resize:vertical;"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label-float">City *</label>
                <input type="text" name="city" class="form-input" placeholder="Karachi, Lahore..." required>
              </div>
              <div class="col-md-6">
                <label class="form-label-float">Postal Code</label>
                <input type="text" name="postal_code" class="form-input" placeholder="75000">
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="checkout-form-card mb-4">
            <h5 style="font-weight:700;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--border);">
              <i class="fas fa-wallet me-2" style="color:var(--primary);"></i>Payment Method
            </h5>
            <div>
              <label class="payment-method-option">
                <input type="radio" name="payment_method" value="Credit Card" checked required>
                <i class="fas fa-credit-card" style="font-size:1.2rem;color:var(--secondary);"></i>
                <div>
                  <div style="font-weight:600;font-size:.9rem;">Credit / Debit Card</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">Visa, MasterCard, JazzCash</div>
                </div>
              </label>
              <label class="payment-method-option">
                <input type="radio" name="payment_method" value="Demand Draft" required>
                <i class="fas fa-file-invoice" style="font-size:1.2rem;color:var(--primary);"></i>
                <div>
                  <div style="font-weight:600;font-size:.9rem;">Demand Draft</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">Bank demand draft</div>
                </div>
              </label>
              <label class="payment-method-option">
                <input type="radio" name="payment_method" value="Cheque" required>
                <i class="fas fa-money-check" style="font-size:1.2rem;color:var(--success);"></i>
                <div>
                  <div style="font-weight:600;font-size:.9rem;">Cheque</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">Pay via cheque</div>
                </div>
              </label>
              <label class="payment-method-option">
                <input type="radio" name="payment_method" value="VPP" required>
                <i class="fas fa-truck" style="font-size:1.2rem;color:var(--accent);"></i>
                <div>
                  <div style="font-weight:600;font-size:.9rem;">VPP (Value Payable Post)</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">Pay on delivery</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Notes -->
          <div class="checkout-form-card mb-4">
            <h5 style="font-weight:700;margin-bottom:16px;">
              <i class="fas fa-sticky-note me-2" style="color:var(--accent);"></i>Order Notes
              <span style="font-size:.8rem;font-weight:400;color:var(--text-muted);">(optional)</span>
            </h5>
            <textarea name="notes" class="form-input" rows="3" placeholder="Any special instructions for your order..." style="resize:vertical;"></textarea>
          </div>

          <button type="submit" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1.05rem;padding:16px;">
            <i class="fas fa-lock me-2"></i>Place Order — Rs. <?= number_format($grandTotal) ?>
          </button>
          <p style="font-size:.75rem;color:var(--text-muted);text-align:center;margin-top:10px;">
            <i class="fas fa-shield-alt me-1"></i>Your order information is secure and encrypted.
          </p>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-5" data-aos="fade-left">
        <div class="order-summary-card">
          <h5 style="font-weight:700;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--border);">
            <i class="fas fa-receipt me-2" style="color:var(--primary);"></i>Order Summary
          </h5>

          <?php foreach ($cart as $title => $item):
            $qty = $item['quantity'] ?? 1;
          ?>
          <div class="order-item-row">
            <div>
              <div style="font-weight:600;font-size:.875rem;"><?= htmlspecialchars($title) ?></div>
              <div style="font-size:.75rem;color:var(--text-muted);">
                <?= htmlspecialchars($item['format'] ?? 'PDF') ?> &times; <?= $qty ?>
              </div>
            </div>
            <span style="font-weight:700;color:var(--primary);white-space:nowrap;">
              Rs. <?= number_format(($item['price'] ?? 0) * $qty) ?>
            </span>
          </div>
          <?php endforeach; ?>

          <div class="cart-summary-row">
            <span>Subtotal</span>
            <span>Rs. <?= number_format($subtotal) ?></span>
          </div>
          <div class="cart-summary-row">
            <span>Shipping</span>
            <span>Rs. <?= number_format($totalShipping) ?></span>
          </div>

          <div class="order-total-row">
            <span>Total</span>
            <span>Rs. <?= number_format($grandTotal) ?></span>
          </div>

          <div class="mt-4 p-3" style="background:rgba(34,197,94,.07);border-radius:var(--radius-sm);border:1px solid rgba(34,197,94,.15);">
            <div style="font-size:.8rem;color:#15803d;font-weight:600;margin-bottom:4px;">
              <i class="fas fa-shield-check me-1"></i>Buyer Protection
            </div>
            <p style="font-size:.75rem;color:var(--text-secondary);margin:0;">
              Your purchase is protected by our 7-day satisfaction guarantee. If anything goes wrong, we'll make it right.
            </p>
          </div>

          <div class="mt-3 d-flex flex-column gap-2">
            <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
              <i class="fas fa-lock" style="color:var(--success);"></i>Secure payment processing
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
              <i class="fas fa-envelope" style="color:var(--secondary);"></i>Order confirmation via email
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
session_start();
include 'db_config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: books.php"); exit(); }

$result = mysqli_query($conn, "SELECT * FROM books WHERE id=$id LIMIT 1");
if (!$result || mysqli_num_rows($result) === 0) { header("Location: books.php"); exit(); }
$book = mysqli_fetch_assoc($result);

// Related books
$cat = mysqli_real_escape_string($conn, $book['category'] ?? '');
$related = [];
$relResult = mysqli_query($conn, "SELECT * FROM books WHERE category='$cat' AND id != $id LIMIT 4");
if ($relResult) {
  while ($r = mysqli_fetch_assoc($relResult)) $related[] = $r;
}

// Cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) $cartCount += $item['quantity'];
}

$img = htmlspecialchars($book['image']);
if (strpos($img, 'http') !== 0 && !file_exists($img)) {
  $img = 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&h=650&fit=crop&q=80';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($book['title']) ?> — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="book-detail-section">
  <div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb-nav mb-4" style="justify-content:flex-start;background:transparent;padding:0;color:var(--text-muted);" data-aos="fade-up">
      <a href="index.php" style="color:var(--secondary);">Home</a>
      <span>/</span>
      <a href="books.php" style="color:var(--secondary);">Books</a>
      <span>/</span>
      <span style="color:var(--text-primary);"><?= htmlspecialchars($book['title']) ?></span>
    </div>

    <div class="row g-5">
      <!-- Book Image -->
      <div class="col-lg-4" data-aos="fade-right">
        <div class="book-detail-images">
          <div style="background:var(--gradient-section);border-radius:var(--radius-lg);padding:40px;text-align:center;box-shadow:var(--shadow-lg);">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($book['title']) ?>"
              style="max-height:400px;max-width:100%;object-fit:contain;border-radius:8px;box-shadow:var(--shadow-xl);">
          </div>
          <!-- Social Share -->
          <div class="d-flex align-items-center gap-2 mt-3">
            <span style="font-size:.8rem;color:var(--text-muted);">Share:</span>
            <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="share-btn"><i class="fab fa-twitter"></i></a>
            <a href="#" class="share-btn"><i class="fab fa-whatsapp"></i></a>
            <a href="#" class="share-btn"><i class="fas fa-link"></i></a>
          </div>
        </div>
      </div>

      <!-- Book Info -->
      <div class="col-lg-8" data-aos="fade-left">
        <div class="book-detail-badge">
          <i class="fas fa-tag"></i><?= htmlspecialchars($book['category'] ?? 'General') ?>
        </div>
        <h1 class="book-detail-title"><?= htmlspecialchars($book['title']) ?></h1>
        <p class="book-detail-author">
          <i class="fas fa-pen-nib me-1" style="color:var(--primary);"></i>
          By <strong><?= htmlspecialchars($book['author'] ?? 'Unknown Author') ?></strong>
        </p>

        <!-- Rating -->
        <div class="d-flex align-items-center gap-2 mb-3">
          <div style="color:var(--warning);">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
          </div>
          <span style="font-size:.85rem;color:var(--text-secondary);">4.5 out of 5 (128 reviews)</span>
        </div>

        <div class="book-detail-price">Rs. <?= number_format($book['price']) ?></div>

        <!-- Book Meta -->
        <div class="book-detail-meta">
          <div class="book-meta-item">
            <div class="book-meta-label"><i class="fas fa-file-pdf me-1"></i>Format</div>
            <div class="book-meta-value"><?= htmlspecialchars($book['format'] ?? 'PDF') ?></div>
          </div>
          <div class="book-meta-item">
            <div class="book-meta-label"><i class="fas fa-weight me-1"></i>Weight</div>
            <div class="book-meta-value"><?= htmlspecialchars($book['weight'] ?? 'N/A') ?> kg</div>
          </div>
          <div class="book-meta-item">
            <div class="book-meta-label"><i class="fas fa-shipping-fast me-1"></i>Shipping</div>
            <div class="book-meta-value">Rs. <?= number_format($book['shipping_charge'] ?? 0) ?></div>
          </div>
          <div class="book-meta-item">
            <div class="book-meta-label"><i class="fas fa-star me-1"></i>Rating</div>
            <div class="book-meta-value">4.5 / 5</div>
          </div>
        </div>

        <!-- Description -->
        <div class="mb-4">
          <h6 style="font-weight:700;margin-bottom:10px;">About This Book</h6>
          <p style="font-size:.9rem;color:var(--text-secondary);line-height:1.8;">
            <?= nl2br(htmlspecialchars($book['description'] ?? 'No description available.')) ?>
          </p>
        </div>

        <!-- Add to Cart Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <form action="add_to_cart.php" method="POST" class="mb-4" data-ajax-cart>
          <input type="hidden" name="title"    value="<?= htmlspecialchars($book['title']) ?>">
          <input type="hidden" name="price"    value="<?= $book['price'] ?>">
          <input type="hidden" name="format"   value="<?= htmlspecialchars($book['format']) ?>">
          <input type="hidden" name="shipping" value="<?= $book['shipping_charge'] ?? 0 ?>">
          <input type="hidden" name="weight"   value="<?= $book['weight'] ?? 0.5 ?>">

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label-float">Format</label>
              <select name="format" class="form-input">
                <option value="PDF" <?= ($book['format'] ?? '') === 'PDF' ? 'selected' : '' ?>>PDF (Digital Download)</option>
                <option value="Hard copy" <?= ($book['format'] ?? '') === 'Hard copy' ? 'selected' : '' ?>>Hard Copy (Physical)</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label-float">Quantity</label>
              <div class="qty-control" style="border-radius:var(--radius-sm);">
                <button type="button" class="qty-btn" onclick="changeQty(-1)"><i class="fas fa-minus"></i></button>
                <input type="number" name="quantity" id="bookQty" class="qty-value" value="1" min="1" max="10"
                  style="border:none;outline:none;width:50px;text-align:center;font-weight:700;">
                <button type="button" class="qty-btn" onclick="changeQty(1)"><i class="fas fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="d-flex gap-3 flex-wrap">
            <button type="submit" class="btn-primary-custom btn-ripple">
              <i class="fas fa-cart-plus me-1"></i>Add to Cart
            </button>
            <a href="cart.php" class="btn-accent-custom btn-ripple btn-buy-now">
              <i class="fas fa-bolt me-1"></i>Buy Now
            </a>
            <button type="button" class="wishlist-btn" style="position:static;width:44px;height:44px;border-radius:50%;border:1.5px solid var(--border);">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </form>
        <?php else: ?>
        <div class="alert-premium info mb-4">
          <i class="fas fa-info-circle"></i>
          Please <a href="login.php?redirect=book_detail.php?id=<?= $id ?>" style="color:var(--secondary);font-weight:600;">log in</a>
          to add this book to your cart.
        </div>
        <?php endif; ?>

        <!-- Trust Badges -->
        <div class="d-flex flex-wrap gap-3">
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
            <i class="fas fa-shield-alt" style="color:var(--success);"></i>Secure Checkout
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
            <i class="fas fa-undo" style="color:var(--primary);"></i>7-Day Returns
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
            <i class="fas fa-truck" style="color:var(--secondary);"></i>Fast Delivery
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-secondary);">
            <i class="fas fa-headset" style="color:var(--warning);"></i>24/7 Support
          </div>
        </div>
      </div>
    </div>

    <!-- Related Books -->
    <?php if (!empty($related)): ?>
    <div class="mt-5" data-aos="fade-up">
      <h4 style="font-weight:700;margin-bottom:24px;">
        <i class="fas fa-bookmark me-2" style="color:var(--primary);"></i>You Might Also Like
      </h4>
      <div class="row g-4">
        <?php foreach ($related as $idx => $rel):
          $rImg = htmlspecialchars($rel['image']);
          if (strpos($rImg, 'http') !== 0 && !file_exists($rImg)) {
            $rImg = 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=400&fit=crop&q=80';
          }
        ?>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $idx * 80 ?>">
          <div class="book-card">
            <div class="book-card-image">
              <img src="<?= $rImg ?>" alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy">
              <div class="book-card-overlay">
                <a href="book_detail.php?id=<?= $rel['id'] ?>" class="overlay-btn">View</a>
              </div>
            </div>
            <div class="book-card-body">
              <h5 class="book-card-title">
                <a href="book_detail.php?id=<?= $rel['id'] ?>" style="color:inherit;">
                  <?= htmlspecialchars($rel['title']) ?>
                </a>
              </h5>
              <p class="book-card-author"><?= htmlspecialchars($rel['author'] ?? '') ?></p>
              <span class="book-card-price">Rs. <?= number_format($rel['price']) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
function changeQty(delta) {
  const inp = document.getElementById('bookQty');
  const val = Math.max(1, Math.min(10, parseInt(inp.value) + delta));
  inp.value = val;
}
</script>
</body>
</html>

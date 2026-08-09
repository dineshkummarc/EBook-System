<?php
session_start();
include 'db_config.php';

// Display session message
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
  }
}

// Fetch featured books
$books = [];
$result = mysqli_query($conn, "SELECT * FROM books WHERE is_featured = 1 ORDER BY id DESC LIMIT 9");
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
  }
}

// Fetch ALL books for new arrivals
$allBooks = [];
$allResult = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 6");
if ($allResult && mysqli_num_rows($allResult) > 0) {
  while ($row = mysqli_fetch_assoc($allResult)) {
    $allBooks[] = $row;
  }
}

// Fetch categories
$categories = [];
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM books ORDER BY category ASC LIMIT 8");
if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row['category'];
  }
}

// Fetch winners
$winner_query = "
  SELECT w.*, a.name AS user_name, t.title AS topic_title
  FROM winners w
  JOIN accounts a ON w.user_id = a.id
  JOIN essay_topics t ON w.competition_id = t.id
  ORDER BY w.id DESC LIMIT 5
";
$winner_result = mysqli_query($conn, $winner_query);
if (!$winner_result) {
  $winner_result_rows = [];
} else {
  $winner_result_rows = [];
  while ($row = mysqli_fetch_assoc($winner_result)) {
    $winner_result_rows[] = $row;
  }
}

$categoryIcons = [
  'Novels'    => '📖',
  'Comics'    => '🦸',
  'History'   => '📜',
  'Science'   => '🔬',
  'Education' => '🎓',
  'Fiction'   => '🌌',
  'Biography' => '👤',
  'Business'  => '💼',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eBook Hub — Your Digital Library</title>
  <meta name="description" content="Discover thousands of eBooks across all genres. Read anywhere, join competitions, and be part of a global readers community.">

  <!-- Fonts & Icons -->
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

<div id="preloader">
  <div class="preloader-inner">
    <div class="preloader-logo">
      <div class="preloader-mark">
        <i class="fas fa-book-open"></i>
      </div>
      <span class="preloader-brand">e<span class="preloader-highlight">BOOK</span></span>
    </div>
    <div class="preloader-bar">
      <div class="preloader-bar-fill"></div>
    </div>
    <div class="preloader-text">Loading Experience</div>
  </div>
</div>

<!-- ===== HERO SECTION ===== -->
<section class="hero-section" id="home">
  <div class="hero-orb-1"></div>
  <div class="hero-orb-2"></div>
  <div class="container">
    <div class="row align-items-center g-5" style="min-height:100vh; padding-top:80px;">
      <div class="col-lg-6 hero-content">
        <div class="hero-badge" data-aos="fade-up">
          <i class="fas fa-star"></i> #1 Digital Bookstore Platform
        </div>
        <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">
          Discover Your Next<br><span class="highlight">Favorite Book</span>
        </h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">
          Thousands of eBooks at your fingertips. Explore novels, comics, history, science and more. Read anywhere, anytime — and compete to win amazing prizes.
        </p>
        <form action="books.php" method="GET" class="hero-search-box" data-aos="fade-up" data-aos-delay="300">
          <i class="fas fa-search" style="color:rgba(255,255,255,.5);"></i>
          <input type="text" name="category" placeholder="Search by category, title, author...">
          <button type="submit"><i class="fas fa-arrow-right me-1"></i>Explore</button>
        </form>
        <div class="d-flex flex-wrap gap-3" data-aos="fade-up" data-aos-delay="350">
          <a href="books.php" class="btn-accent-custom btn-ripple">
            <i class="fas fa-book-open"></i> Browse Library
          </a>
          <a href="register.php" class="btn-white-custom btn-ripple">
            <i class="fas fa-user-plus"></i> Join Free
          </a>
        </div>
        <div class="hero-stats" data-aos="fade-up" data-aos-delay="400">
          <div class="hero-stat-item">
            <span class="stat-num" data-counter="5000">0</span>
            <span class="stat-label">Books Available</span>
          </div>
          <div class="hero-stat-item">
            <span class="stat-num" data-counter="12000">0</span>
            <span class="stat-label">Active Readers</span>
          </div>
          <div class="hero-stat-item">
            <span class="stat-num" data-counter="98">0</span>
            <span class="stat-label">% Satisfaction</span>
          </div>
        </div>
      </div>

      <div class="col-lg-6 hero-image-area d-none d-lg-flex" data-aos="fade-left" data-aos-delay="200">
        <div class="hero-book-showcase">
          <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&h=550&fit=crop&auto=format&q=80"
            class="hero-book-main" alt="Featured Book">
          <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=200&h=280&fit=crop&auto=format&q=80"
            class="hero-book-side hero-book-left" alt="Book">
          <img src="https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=200&h=280&fit=crop&auto=format&q=80"
            class="hero-book-side hero-book-right" alt="Book">
        </div>
      </div>
    </div>
  </div>

  <!-- Scroll Indicator -->
  <div style="position:absolute;bottom:32px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.4);font-size:.75rem;text-align:center;">
    <div style="animation:float 2s ease-in-out infinite;"><i class="fas fa-chevron-down fa-lg"></i></div>
  </div>
</section>

<!-- ===== CATEGORIES ===== -->
<?php if (!empty($categories)): ?>
<section class="section-gap" id="categories" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-th me-1"></i>Browse by Genre</span>
      <h2 class="section-title">Explore <span class="accent-text">Categories</span></h2>
      <p class="section-subtitle">Dive into a world of knowledge. Find books across every genre and subject.</p>
    </div>
    <div class="row g-3">
      <?php
      $icons = ['📖','🦸','📜','🔬','🎓','🌌','👤','💼','🎭','🌍','💻','🎨'];
      foreach ($categories as $idx => $cat):
      ?>
      <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($idx % 4) * 80 ?>">
        <a href="books.php?category=<?= urlencode($cat) ?>" class="category-card">
          <span class="category-icon"><?= $icons[$idx % count($icons)] ?></span>
          <h5><?= htmlspecialchars($cat) ?></h5>
          <span>Browse books</span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== FEATURED BOOKS ===== -->
<section class="section-gap" id="featured">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-fire-alt me-1"></i>Hand-Picked Selections</span>
      <h2 class="section-title">Featured <span class="accent-text">Books</span></h2>
      <p class="section-subtitle">Our editors' top picks — the most loved and most-read titles on our platform.</p>
    </div>

    <?php if (!empty($books)): ?>
    <!-- Swiper Slider -->
    <div class="swiper featured-swiper" data-aos="fade-up" data-aos-delay="100">
      <div class="swiper-wrapper">
        <?php foreach ($books as $book): ?>
        <div class="swiper-slide">
          <div class="book-card">
            <div class="book-card-image">
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
              <?php
                $img = htmlspecialchars($book['image']);
                if (strpos($img, 'http') !== 0 && !file_exists($img)) {
                  $img = 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=300&h=400&fit=crop&q=80';
                }
              ?>
              <img src="<?= $img ?>" alt="<?= htmlspecialchars($book['title']) ?>" loading="lazy">
              <div class="book-card-overlay">
                <a href="book_detail.php?id=<?= $book['id'] ?>" class="overlay-btn">
                  <i class="fas fa-eye me-1"></i>Quick View
                </a>
              </div>
            </div>
            <div class="book-card-body">
              <span class="book-card-category"><?= htmlspecialchars($book['category'] ?? 'General') ?></span>
              <div class="book-card-rating">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                <span class="text-muted">(4.5)</span>
              </div>
              <h5 class="book-card-title">
                <a href="book_detail.php?id=<?= $book['id'] ?>" style="color:inherit;">
                  <?= htmlspecialchars($book['title']) ?>
                </a>
              </h5>
              <p class="book-card-author"><i class="fas fa-user-pen me-1"></i><?= htmlspecialchars($book['author'] ?? 'Unknown Author') ?></p>
              <div class="d-flex align-items-center justify-content-between">
                <span class="book-card-price">Rs. <?= number_format($book['price']) ?></span>
                <span class="book-card-category"><?= htmlspecialchars($book['format'] ?? 'PDF') ?></span>
              </div>
              <?php if (isset($_SESSION['user_id'])): ?>
              <form action="add_to_cart.php" method="POST" data-ajax-cart>
                <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
                <input type="hidden" name="price" value="<?= $book['price'] ?>">
                <input type="hidden" name="format" value="<?= $book['format'] ?>">
                <input type="hidden" name="shipping" value="<?= $book['shipping_charge'] ?? 0 ?>">
                <input type="hidden" name="weight" value="<?= $book['weight'] ?? 0.5 ?>">
                <button type="submit" class="btn-add-cart btn-ripple">
                  <i class="fas fa-cart-plus me-1"></i>Add to Cart
                </button>
              </form>
              <?php else: ?>
              <a href="login.php?redirect=books.php" class="btn-add-cart" style="text-align:center;display:block;text-decoration:none;">
                <i class="fas fa-sign-in-alt me-1"></i>Login to Order
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination mt-4"></div>
    </div>
    <div class="text-center mt-5" data-aos="fade-up">
      <a href="books.php" class="btn-primary-custom btn-ripple">
        <i class="fas fa-book-open me-1"></i>View All Books
      </a>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:60px;color:var(--text-secondary);">
      <i class="fas fa-book-open fa-3x mb-3 d-block" style="color:var(--border);"></i>
      <p>No featured books yet. Check back soon!</p>
      <a href="books.php" class="btn-primary-custom btn-ripple mt-3">Browse All Books</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats-section">
  <div class="container">
    <div class="row g-4 text-center" style="position:relative;z-index:1;">
      <div class="col-6 col-md-3" data-aos="fade-up">
        <div class="stat-card">
          <span class="stat-number" data-counter="5000">0</span>
          <div class="stat-label">Books Available</div>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card">
          <span class="stat-number" data-counter="12000">0</span>
          <div class="stat-label">Happy Readers</div>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card">
          <span class="stat-number" data-counter="50">0</span>
          <div class="stat-label">Competitions Held</div>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-card">
          <span class="stat-number" data-counter="15">0</span>
          <div class="stat-label">Book Categories</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== NEW ARRIVALS ===== -->
<?php if (!empty($allBooks)): ?>
<section class="section-gap" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-sparkles me-1"></i>Just Added</span>
      <h2 class="section-title">New <span class="accent-text">Arrivals</span></h2>
      <p class="section-subtitle">The freshest additions to our library — be the first to read them.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($allBooks as $idx => $book):
        $img = htmlspecialchars($book['image']);
        if (strpos($img, 'http') !== 0 && !file_exists($img)) {
          $img = 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=400&fit=crop&q=80';
        }
      ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx % 3) * 80 ?>">
        <div class="book-card">
          <div class="book-card-image">
            <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            <span class="discount-badge">New</span>
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($book['title']) ?>" loading="lazy">
            <div class="book-card-overlay">
              <a href="book_detail.php?id=<?= $book['id'] ?>" class="overlay-btn">
                <i class="fas fa-eye me-1"></i>View Details
              </a>
            </div>
          </div>
          <div class="book-card-body">
            <span class="book-card-category"><?= htmlspecialchars($book['category'] ?? 'General') ?></span>
            <h5 class="book-card-title">
              <a href="book_detail.php?id=<?= $book['id'] ?>" style="color:inherit;"><?= htmlspecialchars($book['title']) ?></a>
            </h5>
            <p class="book-card-author"><?= htmlspecialchars($book['author'] ?? 'Unknown Author') ?></p>
            <div class="d-flex align-items-center justify-content-between">
              <span class="book-card-price">Rs. <?= number_format($book['price']) ?></span>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
            <form action="add_to_cart.php" method="POST" data-ajax-cart>
              <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
              <input type="hidden" name="price" value="<?= $book['price'] ?>">
              <input type="hidden" name="format" value="<?= $book['format'] ?>">
              <input type="hidden" name="shipping" value="<?= $book['shipping_charge'] ?? 0 ?>">
              <input type="hidden" name="weight" value="<?= $book['weight'] ?? 0.5 ?>">
              <button type="submit" class="btn-add-cart btn-ripple">
                <i class="fas fa-cart-plus me-1"></i>Add to Cart
              </button>
            </form>
            <?php else: ?>
            <a href="login.php?redirect=books.php" class="btn-add-cart" style="text-align:center;display:block;text-decoration:none;">
              <i class="fas fa-sign-in-alt me-1"></i>Login to Order
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== COMPETITIONS ===== -->
<section class="section-gap" id="competitions">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-trophy me-1"></i>Win Exciting Prizes</span>
      <h2 class="section-title">Current <span class="accent-text">Competitions</span></h2>
      <p class="section-subtitle">Showcase your creativity and writing talent. Top entries win cash prizes and publication opportunities.</p>
    </div>
    <div class="row g-4">
      <!-- Story Writing -->
      <div class="col-lg-6" data-aos="fade-right">
        <div class="comp-card-premium">
          <div class="comp-card-header">
            <div class="comp-prize"><i class="fas fa-star me-1"></i>$500 + Publication</div>
            <h3 class="mb-0">Story Writing Competition</h3>
            <p class="mb-0 mt-2" style="color:rgba(255,255,255,.7);font-size:.9rem;">
              <i class="fas fa-clock me-1"></i>Duration: 7 days &nbsp;&bull;&nbsp;
              <i class="fas fa-users me-1"></i>Open to all
            </p>
          </div>
          <div class="comp-card-body">
            <ul class="comp-rules">
              <li>Max 5,000 words — original content only</li>
              <li>Submit within 7 days of registration</li>
              <li>English only — one entry per participant</li>
              <li>Best story gets published in our next journal</li>
            </ul>
            <a href="competitions.php" class="btn-primary-custom btn-ripple w-100" style="justify-content:center;">
              <i class="fas fa-pen-nib me-1"></i>Enter Competition
            </a>
          </div>
        </div>
      </div>

      <!-- Essay Writing -->
      <div class="col-lg-6" data-aos="fade-left">
        <div class="comp-card-premium">
          <div class="comp-card-header" style="background:linear-gradient(135deg,#a0c1b9,#7fa8a0);">
            <div class="comp-prize"><i class="fas fa-book me-1"></i>Educational Books</div>
            <h3 class="mb-0">Essay Writing Competition</h3>
            <p class="mb-0 mt-2" style="color:rgba(255,255,255,.7);font-size:.9rem;">
              <i class="fas fa-clock me-1"></i>Duration: 3 hours &nbsp;&bull;&nbsp;
              <i class="fas fa-users me-1"></i>Open to all
            </p>
          </div>
          <div class="comp-card-body">
            <ul class="comp-rules">
              <li>1,000–1,500 words on a given topic</li>
              <li>No external resources allowed</li>
              <li>Timer starts when you begin</li>
              <li>Quick challenge with amazing book prizes</li>
            </ul>
            <a href="competitions.php" class="btn-secondary-custom w-100" style="justify-content:center;">
              <i class="fas fa-stopwatch me-1"></i>Start Essay Timer
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== WHY CHOOSE US ===== -->
<section class="section-gap" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-shield-check me-1"></i>Why eBook Hub</span>
      <h2 class="section-title">Why <span class="accent-text">Choose Us?</span></h2>
      <p class="section-subtitle">We're not just a bookstore. We're a literary ecosystem built for passionate readers.</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['icon'=>'fas fa-infinity','title'=>'Unlimited Access','desc'=>'Browse thousands of eBooks across every genre without any subscription limits.'],
        ['icon'=>'fas fa-download','title'=>'Instant Download','desc'=>'After purchase, download your books instantly in PDF or receive your physical copy.'],
        ['icon'=>'fas fa-trophy','title'=>'Writing Competitions','desc'=>'Win cash prizes and publication opportunities through our regular competitions.'],
        ['icon'=>'fas fa-headset','title'=>'24/7 Support','desc'=>'Our dedicated team is always here to help you with any question or concern.'],
        ['icon'=>'fas fa-lock','title'=>'Secure Payments','desc'=>'Multiple secure payment methods with fully encrypted transactions.'],
        ['icon'=>'fas fa-users','title'=>'Reader Community','desc'=>'Join thousands of readers, share reviews, and discover your next great read.'],
      ];
      foreach ($features as $idx => $f):
      ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx % 3) * 80 ?>">
        <div class="why-us-card">
          <div class="why-us-icon"><i class="<?= $f['icon'] ?>"></i></div>
          <h4><?= $f['title'] ?></h4>
          <p><?= $f['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== WINNERS ===== -->
<?php if (!empty($winner_result_rows)): ?>
<section class="section-gap" id="winners">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-crown me-1"></i>Hall of Fame</span>
      <h2 class="section-title">Recent <span class="accent-text">Winners</span></h2>
      <p class="section-subtitle">Celebrating the creative minds who shone brightest in our competitions.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($winner_result_rows as $idx => $winner): ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx % 3) * 80 ?>">
        <div class="winner-card-premium">
          <div class="winner-trophy">🏆</div>
          <h5><?= htmlspecialchars($winner['user_name']) ?></h5>
          <p class="text-muted mb-1" style="font-size:.85rem;">
            <i class="fas fa-pen me-1"></i><?= htmlspecialchars($winner['topic_title']) ?>
          </p>
          <span class="status-pill delivered">
            <i class="fas fa-star me-1"></i>Winner
          </span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== TESTIMONIALS ===== -->
<section class="section-gap" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-star me-1"></i>What Readers Say</span>
      <h2 class="section-title">Customer <span class="accent-text">Reviews</span></h2>
    </div>
    <div class="swiper testimonial-swiper" data-aos="fade-up" data-aos-delay="100">
      <div class="swiper-wrapper">
        <?php
        $testimonials = [
          ['text'=>'eBook Hub has completely transformed my reading life. The collection is incredible and the interface is beautiful. Highly recommended!','name'=>'Sarah Ahmed','role'=>'Avid Reader, Karachi'],
          ['text'=>'I won the essay competition and got published! The platform is professional, the community is warm, and the prizes are real.','name'=>'Muhammad Ali','role'=>'Competition Winner'],
          ['text'=>'Fast downloads, great prices, and the book selection is outstanding. I have been a subscriber for 2 years and never disappointed.','name'=>'Fatima Malik','role'=>'Regular Customer'],
          ['text'=>'The glassmorphism design is gorgeous and the checkout is smooth. Best eBook experience I have ever had online.','name'=>'Ahmed Hassan','role'=>'Literature Student'],
          ['text'=>'Joined for the competitions, stayed for the books. An absolute gem of a platform for anyone who loves reading.','name'=>'Zara Khan','role'=>'Blogger & Reviewer'],
        ];
        foreach ($testimonials as $t):
        ?>
        <div class="swiper-slide">
          <div class="testimonial-card">
            <div class="testimonial-quote">"</div>
            <p class="testimonial-text"><?= $t['text'] ?></p>
            <div class="testimonial-author">
              <div class="testimonial-avatar"><?= strtoupper($t['name'][0]) ?></div>
              <div>
                <div class="testimonial-name"><?= $t['name'] ?></div>
                <div class="testimonial-role"><?= $t['role'] ?></div>
              </div>
              <div class="ms-auto" style="color:var(--warning);">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                <i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="testimonial-pagination mt-4" style="text-align:center;"></div>
    </div>
  </div>
</section>

<!-- ===== NEWSLETTER ===== -->
<section class="newsletter-section">
  <div class="container" style="position:relative;z-index:1;">
    <div class="row justify-content-center text-center">
      <div class="col-lg-6" data-aos="fade-up">
        <h2 style="color:white;font-size:2rem;font-weight:800;margin-bottom:12px;">Stay in the Loop 📚</h2>
        <p style="color:rgba(255,255,255,.7);margin-bottom:32px;">Get new arrivals, competition announcements, and exclusive deals delivered to your inbox.</p>
        <form class="newsletter-form" onsubmit="return false;">
          <input type="email" placeholder="Enter your email address...">
          <button type="button" class="btn-accent-custom newsletter-submit">
            <i class="fas fa-paper-plane"></i> Subscribe
          </button>
        </form>
        <p style="font-size:.75rem;color:rgba(255,255,255,.4);margin-top:12px;">No spam. Unsubscribe at any time.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== FAQ ===== -->
<section class="section-gap">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge"><i class="fas fa-question-circle me-1"></i>Help Center</span>
      <h2 class="section-title">Frequently Asked <span class="accent-text">Questions</span></h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php
        $faqs = [
          ['q'=>'What types of books are available?','a'=>'We offer a wide range including comics, novels, history, science, education, biography, and general knowledge books — in both PDF and hard-copy formats.'],
          ['q'=>'How do I purchase a book?','a'=>'Browse or search for a book, click "Add to Cart", then proceed to checkout. You can pay via credit card, demand draft, cheque, or VPP.'],
          ['q'=>'Can I download my book as a PDF?','a'=>'Yes! PDF-format purchases are available for download directly from your dashboard after payment is confirmed by our team.'],
          ['q'=>'Do I need an account to buy books?','a'=>'Yes, registration is required to place orders and access downloads. It is free and takes less than a minute.'],
          ['q'=>'How do the competitions work?','a'=>'We host story-writing competitions (7-day submission) and essay-writing competitions (3-hour timed challenge). Register and participate to win prizes.'],
          ['q'=>'Is there home delivery?','a'=>'Yes, we provide home delivery for hard-copy orders across Pakistan. Shipping charges are calculated at checkout based on weight.'],
        ];
        foreach ($faqs as $i => $faq):
        ?>
        <div class="faq-item" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>">
          <div class="faq-question">
            <?= $faq['q'] ?>
            <i class="fas fa-plus toggle-icon"></i>
          </div>
          <div class="faq-answer">
            <div class="faq-answer-inner"><?= $faq['a'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($message): ?>
<script>document.addEventListener('DOMContentLoaded', () => showToast("<?= addslashes($message) ?>", 'success'));</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
</body>
</html>

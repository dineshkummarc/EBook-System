<?php
session_start();
include 'db_config.php';

$search   = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$category = $search;

if ($search) {
    $sql = "SELECT * FROM books WHERE category LIKE '%$search%' OR title LIKE '%$search%' OR author LIKE '%$search%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM books ORDER BY id DESC";
}
$result = mysqli_query($conn, $sql);

$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}

// All categories for filter tabs
$catResult  = mysqli_query($conn, "SELECT DISTINCT category FROM books ORDER BY category ASC");
$categories = ['All'];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row['category'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Books — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .filter-tabs { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:32px; }
    .filter-tab {
      background: var(--surface); border: 1.5px solid var(--border);
      border-radius: 50px; padding: 8px 18px; font-size:.85rem; font-weight:600;
      cursor:pointer; transition:var(--transition); color:var(--text-secondary);
      text-decoration:none;
    }
    .filter-tab:hover, .filter-tab.active {
      background: var(--primary); border-color:var(--primary); color:white;
    }
    .books-grid { transition: opacity .3s; }
    .sort-bar {
      display:flex; align-items:center; justify-content:space-between;
      background:var(--surface); border-radius:var(--radius-sm);
      padding:12px 20px; border:1px solid var(--border); margin-bottom:24px;
    }
    .sort-bar select {
      background:transparent; border:none; outline:none; font-size:.875rem;
      color:var(--text-secondary); cursor:pointer;
    }
    .results-count { font-size:.875rem; color:var(--text-secondary); }
    .results-count strong { color:var(--primary); }
    .view-toggle { display:flex; gap:4px; }
    .view-btn {
      width:34px;height:34px; background:var(--bg); border:1px solid var(--border);
      border-radius:6px; display:flex; align-items:center; justify-content:center;
      cursor:pointer; color:var(--text-muted); font-size:.85rem; transition:var(--transition);
    }
    .view-btn.active { background:var(--primary); border-color:var(--primary); color:white; }
    /* List view */
    .book-list-item {
      background:var(--surface); border-radius:var(--radius-md); border:1px solid var(--border);
      padding:20px; display:flex; gap:20px; align-items:flex-start; transition:var(--transition);
      margin-bottom:16px;
    }
    .book-list-item:hover { box-shadow:var(--shadow-md); border-color:transparent; }
    .book-list-item img { width:80px; height:100px; object-fit:cover; border-radius:6px; flex-shrink:0; }
    #listView { display:none; }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>



<!-- Page Header -->
<div class="page-header">
  <h1 data-aos="fade-up"><i class="fas fa-book-open me-2"></i>Browse Books</h1>
  <p data-aos="fade-up" data-aos-delay="100">
    <?= $search ? "Showing results for: <em>\"".htmlspecialchars($search)."\"</em>" : "Discover our complete digital library" ?>
  </p>
  <div class="breadcrumb-nav" data-aos="fade-up" data-aos-delay="150">
    <a href="index.php">Home</a><span>/</span><span>Books</span>
    <?= $search ? "<span>/</span><span>".htmlspecialchars($search)."</span>" : '' ?>
  </div>
</div>

<section style="padding: 60px 0; min-height:70vh;">
  <div class="container">

    <!-- Search Bar -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-6" data-aos="fade-up">
        <form action="books.php" method="GET">
          <div style="display:flex; gap:10px;">
            <input type="text" name="category" class="form-input" placeholder="Search by title, author, or category..."
              value="<?= htmlspecialchars($search) ?>" style="border-radius:50px;padding-left:20px;">
            <button type="submit" class="btn-primary-custom btn-ripple" style="white-space:nowrap;">
              <i class="fas fa-search"></i>
            </button>
            <?php if ($search): ?>
            <a href="books.php" class="btn-secondary-custom" style="white-space:nowrap;">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="filter-tabs" data-aos="fade-up" data-aos-delay="50">
      <?php foreach ($categories as $cat): ?>
      <a href="<?= $cat === 'All' ? 'books.php' : 'books.php?category='.urlencode($cat) ?>"
         class="filter-tab <?= ($cat === 'All' && !$search) || $cat === $search ? 'active' : '' ?>">
        <?= htmlspecialchars($cat) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Sort & View Bar -->
    <div class="sort-bar" data-aos="fade-up" data-aos-delay="100">
      <span class="results-count">
        <strong><?= count($books) ?></strong> book<?= count($books) !== 1 ? 's' : '' ?> found
        <?= $search ? ' for "'.htmlspecialchars($search).'"' : '' ?>
      </span>
      <div class="d-flex align-items-center gap-3">
        <select class="sort-bar select" onchange="sortBooks(this.value)">
          <option value="default">Sort by: Default</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="title">Title: A–Z</option>
        </select>
        <div class="view-toggle">
          <button class="view-btn active" id="gridBtn" onclick="setView('grid')" title="Grid view">
            <i class="fas fa-th"></i>
          </button>
          <button class="view-btn" id="listBtn" onclick="setView('list')" title="List view">
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>
    </div>

    <?php if (empty($books)): ?>
    <!-- Empty State -->
    <div class="text-center py-5" data-aos="fade-up">
      <div style="font-size:5rem;margin-bottom:20px;">🔍</div>
      <h3>No books found</h3>
      <p class="text-muted mb-4">We couldn't find any books matching "<?= htmlspecialchars($search) ?>"</p>
      <a href="books.php" class="btn-primary-custom btn-ripple">Browse All Books</a>
    </div>

    <?php else: ?>

    <!-- Grid View -->
    <div id="gridView">
      <div class="row g-4" id="booksGrid">
        <?php foreach ($books as $idx => $book):
          $img = htmlspecialchars($book['image']);
          if (strpos($img, 'http') !== 0 && !file_exists($img)) {
            $img = 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=300&h=400&fit=crop&q=80';
          }
        ?>
        <div class="col-sm-6 col-lg-4 col-xl-3" data-aos="fade-up" data-aos-delay="<?= ($idx % 4) * 60 ?>"
             data-price="<?= $book['price'] ?>" data-title="<?= htmlspecialchars($book['title']) ?>">
          <div class="book-card">
            <div class="book-card-image">
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
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
              <p class="book-card-author"><i class="fas fa-user-pen me-1"></i><?= htmlspecialchars($book['author'] ?? 'Unknown') ?></p>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="book-card-price">Rs. <?= number_format($book['price']) ?></span>
                <span style="font-size:.75rem;background:rgba(160,193,185,.08);color:var(--secondary);padding:3px 8px;border-radius:50px;font-weight:600;">
                  <?= htmlspecialchars($book['format'] ?? 'PDF') ?>
                </span>
              </div>
              <?php if (isset($_SESSION['user_id'])): ?>
              <form action="add_to_cart.php" method="POST" data-ajax-cart>
                <input type="hidden" name="title"    value="<?= htmlspecialchars($book['title']) ?>">
                <input type="hidden" name="price"    value="<?= $book['price'] ?>">
                <input type="hidden" name="format"   value="<?= $book['format'] ?>">
                <input type="hidden" name="shipping" value="<?= $book['shipping_charge'] ?? 0 ?>">
                <input type="hidden" name="weight"   value="<?= $book['weight'] ?? 0.5 ?>">
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

    <!-- List View -->
    <div id="listView">
      <?php foreach ($books as $book):
        $img = htmlspecialchars($book['image']);
        if (strpos($img, 'http') !== 0 && !file_exists($img)) {
          $img = 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=300&h=400&fit=crop&q=80';
        }
      ?>
      <div class="book-list-item">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($book['title']) ?>" loading="lazy">
        <div class="flex-grow-1">
          <span class="book-card-category"><?= htmlspecialchars($book['category'] ?? 'General') ?></span>
          <h5 style="font-family:Poppins,sans-serif;font-weight:700;margin:6px 0 4px;">
            <a href="book_detail.php?id=<?= $book['id'] ?>" style="color:var(--text-primary);"><?= htmlspecialchars($book['title']) ?></a>
          </h5>
          <p style="font-size:.85rem;color:var(--text-secondary);margin-bottom:8px;"><?= htmlspecialchars($book['author'] ?? 'Unknown Author') ?></p>
          <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
            <?= htmlspecialchars(substr($book['description'] ?? '', 0, 150)) ?>...
          </p>
          <div class="d-flex align-items-center gap-3">
            <span class="book-card-price">Rs. <?= number_format($book['price']) ?></span>
            <a href="book_detail.php?id=<?= $book['id'] ?>" class="btn-secondary-custom" style="padding:8px 18px;font-size:.8rem;">View Details</a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <form action="add_to_cart.php" method="POST" data-ajax-cart style="margin:0;">
              <input type="hidden" name="title"    value="<?= htmlspecialchars($book['title']) ?>">
              <input type="hidden" name="price"    value="<?= $book['price'] ?>">
              <input type="hidden" name="format"   value="<?= $book['format'] ?>">
              <input type="hidden" name="shipping" value="<?= $book['shipping_charge'] ?? 0 ?>">
              <input type="hidden" name="weight"   value="<?= $book['weight'] ?? 0.5 ?>">
              <button type="submit" class="btn-primary-custom btn-ripple" style="padding:8px 18px;font-size:.8rem;">
                <i class="fas fa-cart-plus me-1"></i>Add to Cart
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
function setView(v) {
  document.getElementById('gridView').style.display = v === 'grid' ? 'block' : 'none';
  document.getElementById('listView').style.display  = v === 'list' ? 'block' : 'none';
  document.getElementById('gridBtn').classList.toggle('active', v === 'grid');
  document.getElementById('listBtn').classList.toggle('active', v === 'list');
}

function sortBooks(val) {
  const grid = document.getElementById('booksGrid');
  const items = [...grid.querySelectorAll('[data-price]')];
  items.sort((a, b) => {
    if (val === 'price-asc')  return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
    if (val === 'price-desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
    if (val === 'title')      return a.dataset.title.localeCompare(b.dataset.title);
    return 0;
  });
  items.forEach(item => grid.appendChild(item));
}
</script>
</body>
</html>

<?php
session_start();
include '../db_config.php';
$pageTitle = 'Upload Book';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title          = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $author         = mysqli_real_escape_string($conn, trim($_POST['author'] ?? ''));
    $category       = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));
    $format         = mysqli_real_escape_string($conn, $_POST['format'] ?? 'PDF');
    $price          = (float)($_POST['price'] ?? 0);
    $shipping_charge= (float)($_POST['shipping_charge'] ?? 0);
    $weight         = (float)($_POST['weight'] ?? 0.5);
    $description    = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
    $is_featured    = isset($_POST['is_featured']) ? 1 : 0;

    // Handle image upload
    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $imgDir = '../uploads/images/';
        if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $imgPath = $imgDir . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
            $image_path = 'uploads/images/' . $imgName;
        }
    }

    // Handle PDF upload
    $pdf_path = '';
    if (!empty($_FILES['pdf_file']['name'])) {
        $pdfDir = '../uploads/pdfs/';
        if (!is_dir($pdfDir)) mkdir($pdfDir, 0755, true);
        $pdfName = time() . '_' . basename($_FILES['pdf_file']['name']);
        $pdfPath = $pdfDir . $pdfName;
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
            $pdf_path = 'uploads/pdfs/' . $pdfName;
        }
    }

    if (empty($title) || empty($author) || $price <= 0) {
        $error = "Title, author, and a valid price are required.";
    } else {
        $sql = "INSERT INTO books (title, author, category, format, price, shipping_charge, weight, description, image, pdf_path, is_featured)
                VALUES ('$title','$author','$category','$format',$price,$shipping_charge,$weight,'$description','$image_path','$pdf_path',$is_featured)";
        if (mysqli_query($conn, $sql)) {
            $success = "Book \"$title\" uploaded successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}

include 'admin_header.php';
?>

<?php if ($success): ?>
<div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= htmlspecialchars($success) ?> <a href="view_books.php">View all books →</a></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert-premium error mb-4"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="dashboard-card">
      <div class="dashboard-card-title mb-4"><i class="fas fa-plus-circle"></i>Upload New Book</div>
      <form method="POST" action="" enctype="multipart/form-data" data-validate>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-float">Book Title *</label>
            <input type="text" name="title" class="form-input" placeholder="Enter book title" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Author *</label>
            <input type="text" name="author" class="form-input" placeholder="Author name" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Category</label>
            <input type="text" name="category" class="form-input" placeholder="e.g. Novels, Comics, Science" list="catList">
            <datalist id="catList">
              <option>Novels</option><option>Comics</option><option>History</option>
              <option>Science</option><option>Education</option><option>Fiction</option>
              <option>Biography</option><option>Business</option>
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Format</label>
            <select name="format" class="form-input">
              <option value="PDF">PDF (Digital)</option>
              <option value="Hard copy">Hard Copy</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Price (Rs.) *</label>
            <input type="number" name="price" class="form-input" placeholder="e.g. 500" min="0" step="0.01" required>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Shipping Charge (Rs.)</label>
            <input type="number" name="shipping_charge" class="form-input" placeholder="e.g. 150" min="0" step="0.01" value="0">
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Weight (kg)</label>
            <input type="number" name="weight" class="form-input" placeholder="e.g. 0.5" min="0" step="0.01" value="0.5">
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Featured Book?</label>
            <label style="display:flex;align-items:center;gap:10px;margin-top:8px;cursor:pointer;font-size:.875rem;">
              <input type="checkbox" name="is_featured" value="1" style="accent-color:var(--primary);width:18px;height:18px;">
              Show on homepage featured section
            </label>
          </div>
          <div class="col-12">
            <label class="form-label-float">Description</label>
            <textarea name="description" class="form-input" rows="4" placeholder="Brief description of the book..." style="resize:vertical;"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Cover Image</label>
            <input type="file" name="image" class="form-input" accept="image/*" style="padding:10px;">
          </div>
          <div class="col-md-6">
            <label class="form-label-float">PDF File (for digital download)</label>
            <input type="file" name="pdf_file" class="form-input" accept=".pdf" style="padding:10px;">
          </div>
          <div class="col-12 mt-2">
            <button type="submit" class="btn-primary-custom btn-ripple" style="font-size:1rem;padding:14px 32px;">
              <i class="fas fa-upload me-2"></i>Upload Book
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

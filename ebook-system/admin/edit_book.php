<?php
session_start();
include '../db_config.php';
$pageTitle = 'Edit Book';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) { header("Location: view_books.php"); exit(); }

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

    // Existing paths (keep unless replaced)
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image, pdf_path FROM books WHERE id=$id"));
    $image_path = $existing['image'] ?? '';
    $pdf_path   = $existing['pdf_path'] ?? '';

    // Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $imgDir = '../uploads/images/';
        if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $imgFull = $imgDir . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imgFull)) {
            if ($image_path && file_exists('../' . $image_path)) @unlink('../' . $image_path);
            $image_path = 'uploads/images/' . $imgName;
        }
    }

    // Handle new PDF upload
    if (!empty($_FILES['pdf_file']['name'])) {
        $pdfDir = '../uploads/pdfs/';
        if (!is_dir($pdfDir)) mkdir($pdfDir, 0755, true);
        $pdfName = time() . '_' . basename($_FILES['pdf_file']['name']);
        $pdfFull = $pdfDir . $pdfName;
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfFull)) {
            if ($pdf_path && file_exists('../' . $pdf_path)) @unlink('../' . $pdf_path);
            $pdf_path = 'uploads/pdfs/' . $pdfName;
        }
    }

    if (empty($title) || empty($author) || $price <= 0) {
        $error = "Title, author, and a valid price are required.";
    } else {
        $image_esc = mysqli_real_escape_string($conn, $image_path);
        $pdf_esc   = mysqli_real_escape_string($conn, $pdf_path);
        $sql = "UPDATE books SET
                    title='$title',
                    author='$author',
                    category='$category',
                    format='$format',
                    price=$price,
                    shipping_charge=$shipping_charge,
                    weight=$weight,
                    description='$description',
                    image='$image_esc',
                    pdf_path='$pdf_esc',
                    is_featured=$is_featured
                WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $success = "Book \"$title\" updated successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}

// Load current book
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id=$id"));
if (!$book) {
    include 'admin_header.php';
    echo '<div class="alert-premium error mb-4"><i class="fas fa-exclamation-circle"></i>Book not found. <a href="view_books.php">Back to books</a></div>';
    include 'admin_footer.php';
    exit();
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
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="dashboard-card-title mb-0"><i class="fas fa-edit"></i>Edit Book</div>
        <a href="view_books.php" class="topbar-btn" title="Back"><i class="fas fa-arrow-left"></i></a>
      </div>
      <form method="POST" action="" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-float">Book Title *</label>
            <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($book['title']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Author *</label>
            <input type="text" name="author" class="form-input" value="<?= htmlspecialchars($book['author'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Category</label>
            <input type="text" name="category" class="form-input" value="<?= htmlspecialchars($book['category'] ?? '') ?>" list="catList">
            <datalist id="catList">
              <option>Novels</option><option>Comics</option><option>History</option>
              <option>Science</option><option>Education</option><option>Fiction</option>
              <option>Biography</option><option>Business</option>
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Format</label>
            <select name="format" class="form-input">
              <?php $fmt = $book['format'] ?? 'PDF'; ?>
              <option value="PDF" <?= $fmt==='PDF'?'selected':'' ?>>PDF (Digital)</option>
              <option value="Hard copy" <?= $fmt==='Hard copy'?'selected':'' ?>>Hard Copy</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Price (Rs.) *</label>
            <input type="number" name="price" class="form-input" min="0" step="0.01" value="<?= htmlspecialchars($book['price']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label-float">Shipping Charge (Rs.)</label>
            <input type="number" name="shipping_charge" class="form-input" min="0" step="0.01" value="<?= htmlspecialchars($book['shipping_charge'] ?? 0) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Weight (kg)</label>
            <input type="number" name="weight" class="form-input" min="0" step="0.01" value="<?= htmlspecialchars($book['weight'] ?? 0.5) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Featured Book?</label>
            <label style="display:flex;align-items:center;gap:10px;margin-top:8px;cursor:pointer;font-size:.875rem;">
              <input type="checkbox" name="is_featured" value="1" <?= !empty($book['is_featured']) ? 'checked' : '' ?> style="accent-color:var(--primary);width:18px;height:18px;">
              Show on homepage featured section
            </label>
          </div>
          <div class="col-12">
            <label class="form-label-float">Description</label>
            <textarea name="description" class="form-input" rows="4" style="resize:vertical;"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">Cover Image</label>
            <?php if (!empty($book['image']) && file_exists('../' . $book['image'])): ?>
              <div style="margin-bottom:8px;">
                <img src="../<?= htmlspecialchars($book['image']) ?>" style="width:70px;height:90px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Current cover</div>
              </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-input" accept="image/*" style="padding:10px;">
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Leave empty to keep current image.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label-float">PDF File (for digital download)</label>
            <?php if (!empty($book['pdf_path']) && file_exists('../' . $book['pdf_path'])): ?>
              <div style="margin-bottom:8px;font-size:.85rem;">
                <i class="fas fa-file-pdf" style="color:var(--danger);"></i>
                <a href="../<?= htmlspecialchars($book['pdf_path']) ?>" target="_blank"><?= htmlspecialchars(basename($book['pdf_path'])) ?></a>
              </div>
            <?php endif; ?>
            <input type="file" name="pdf_file" class="form-input" accept=".pdf" style="padding:10px;">
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Leave empty to keep current PDF.</div>
          </div>
          <div class="col-12 mt-2 d-flex gap-2">
            <button type="submit" class="btn-primary-custom btn-ripple" style="font-size:1rem;padding:14px 32px;">
              <i class="fas fa-save me-2"></i>Save Changes
            </button>
            <a href="view_books.php" class="topbar-btn" style="padding:14px 24px;">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

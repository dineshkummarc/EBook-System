<?php
session_start();
include '../db_config.php';
$pageTitle = 'Manage Books';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $del = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM books WHERE id=$del");
    $_SESSION['admin_message'] = "Book deleted.";
    header("Location: view_books.php"); exit();
}

// Handle feature toggle
if (isset($_GET['toggle_featured'])) {
    $bid = (int)$_GET['toggle_featured'];
    $cur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_featured FROM books WHERE id=$bid"))['is_featured'] ?? 0;
    mysqli_query($conn, "UPDATE books SET is_featured=".($cur ? 0 : 1)." WHERE id=$bid");
    header("Location: view_books.php"); exit();
}

$message = $_SESSION['admin_message'] ?? ''; unset($_SESSION['admin_message']);

$books = [];
$result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
if ($result) while ($row = mysqli_fetch_assoc($result)) $books[] = $row;

include 'admin_header.php';
?>

<?php if ($message): ?>
<div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="dashboard-card">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="dashboard-card-title mb-0"><i class="fas fa-book"></i>All Books (<?= count($books) ?>)</div>
    <div class="d-flex gap-2 align-items-center">
      <input type="text" id="bookSearch" class="form-input" placeholder="Search..." style="width:180px;padding:8px 14px;font-size:.85rem;" oninput="filterTable(this.value)">
      <a href="upload_book.php" class="btn-primary-custom btn-ripple" style="font-size:.85rem;padding:10px 18px;">
        <i class="fas fa-plus me-1"></i>Add Book
      </a>
    </div>
  </div>

  <?php if (empty($books)): ?>
  <div style="text-align:center;padding:40px;color:var(--text-muted);">
    <i class="fas fa-book fa-3x mb-3 d-block" style="color:var(--border);"></i>
    <p>No books uploaded yet.</p>
    <a href="upload_book.php" class="btn-primary-custom btn-ripple mt-3">Upload First Book</a>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table" id="booksTable">
      <thead>
        <tr><th>Cover</th><th>Title</th><th>Author</th><th>Category</th><th>Format</th><th>Price</th><th>Featured</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($books as $book):
          $img = $book['image'] ?? '';
          $hasImg = $img && file_exists('../' . $img);
        ?>
        <tr>
          <td>
            <?php if ($hasImg): ?>
            <img src="../<?= htmlspecialchars($img) ?>" style="width:40px;height:52px;object-fit:cover;border-radius:4px;">
            <?php else: ?>
            <div style="width:40px;height:52px;background:var(--bg);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📖</div>
            <?php endif; ?>
          </td>
          <td style="font-weight:600;max-width:160px;">
            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($book['title']) ?></div>
          </td>
          <td style="font-size:.85rem;color:var(--text-secondary);"><?= htmlspecialchars($book['author'] ?? '') ?></td>
          <td><span class="book-card-category"><?= htmlspecialchars($book['category'] ?? '') ?></span></td>
          <td style="font-size:.8rem;"><?= htmlspecialchars($book['format'] ?? '') ?></td>
          <td style="font-weight:700;color:var(--primary);">Rs. <?= number_format($book['price']) ?></td>
          <td>
            <a href="view_books.php?toggle_featured=<?= $book['id'] ?>" class="status-pill <?= $book['is_featured'] ? 'delivered' : 'pending' ?>" style="cursor:pointer;text-decoration:none;">
              <?= $book['is_featured'] ? '⭐ Yes' : 'No' ?>
            </a>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="edit_book.php?id=<?= $book['id'] ?>" class="topbar-btn" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="view_books.php?delete=<?= $book['id'] ?>" class="topbar-btn" title="Delete"
                 onclick="return confirm('Delete this book?')"
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
  document.querySelectorAll('#booksTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>

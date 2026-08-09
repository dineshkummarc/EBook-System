<?php
session_start();
include '../db_config.php';
$pageTitle = 'Manage Dealers';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $city    = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));

    if ($name) {
        $sql = "INSERT INTO dealers (name, address, phone, city) VALUES ('$name','$address','$phone','$city')";
        if (mysqli_query($conn, $sql)) $success = "Dealer added successfully!";
        else $error = "Failed: " . mysqli_error($conn);
    } else {
        $error = "Dealer name is required.";
    }
}

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM dealers WHERE id=$did");
    header("Location: manage_dealers.php"); exit();
}

$dealers = [];
$result = mysqli_query($conn, "SELECT * FROM dealers ORDER BY id DESC");
if ($result) while ($row = mysqli_fetch_assoc($result)) $dealers[] = $row;

include 'admin_header.php';
?>

<?php if ($success): ?><div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= $success ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert-premium error   mb-4"><i class="fas fa-exclamation-circle"></i><?= $error ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="dashboard-card">
      <div class="dashboard-card-title"><i class="fas fa-plus"></i>Add Dealer</div>
      <form method="POST" action="" data-validate>
        <div class="form-group"><label class="form-label-float">Name *</label><input type="text" name="name" class="form-input" required placeholder="Dealer / shop name"></div>
        <div class="form-group"><label class="form-label-float">City</label><input type="text" name="city" class="form-input" placeholder="Karachi, Lahore..."></div>
        <div class="form-group"><label class="form-label-float">Address</label><input type="text" name="address" class="form-input" placeholder="Street & area"></div>
        <div class="form-group"><label class="form-label-float">Phone</label><input type="tel" name="phone" class="form-input" placeholder="+92 300 0000000"></div>
        <button type="submit" class="btn-primary-custom btn-ripple w-100" style="justify-content:center;"><i class="fas fa-plus me-1"></i>Add Dealer</button>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="dashboard-card">
      <div class="dashboard-card-title"><i class="fas fa-store"></i>All Dealers (<?= count($dealers) ?>)</div>
      <?php if (empty($dealers)): ?>
      <div style="text-align:center;padding:32px;color:var(--text-muted);"><p>No dealers added yet.</p></div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>City</th><th>Phone</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($dealers as $d): ?>
            <tr>
              <td style="font-weight:600;"><?= htmlspecialchars($d['name'] ?? $d['dealer_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($d['city'] ?? '') ?></td>
              <td><?= htmlspecialchars($d['phone'] ?? '') ?></td>
              <td><a href="manage_dealers.php?delete=<?= $d['id'] ?>" class="topbar-btn" onclick="return confirm('Delete dealer?')" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.15);color:var(--danger);"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

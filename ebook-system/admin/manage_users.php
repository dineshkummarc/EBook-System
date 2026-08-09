<?php
session_start();
include '../db_config.php';
$pageTitle = 'Manage Users';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM accounts WHERE id=$uid AND role_id=2");
    }
    header("Location: manage_users.php"); exit();
}

$users = [];
$result = mysqli_query($conn, "SELECT * FROM accounts ORDER BY id DESC");
if ($result) while ($row = mysqli_fetch_assoc($result)) $users[] = $row;

include 'admin_header.php';
?>

<div class="dashboard-card">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="dashboard-card-title mb-0"><i class="fas fa-users"></i>All Users (<?= count($users) ?>)</div>
    <input type="text" class="form-input" placeholder="Search users..." style="width:200px;padding:8px 14px;font-size:.85rem;"
      oninput="document.querySelectorAll('#usersTable tbody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(this.value.toLowerCase()) ? '' : 'none')">
  </div>
  <div class="table-responsive">
    <table class="admin-table" id="usersTable">
      <thead>
        <tr><th>#ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td style="font-weight:600;"><?= htmlspecialchars($u['name'] ?? 'N/A') ?></td>
          <td style="color:var(--text-secondary);"><?= htmlspecialchars($u['email']) ?></td>
          <td>
            <span class="status-pill <?= $u['role_id'] == 1 ? 'delivered' : 'pending' ?>">
              <?= $u['role_id'] == 1 ? 'Admin' : 'User' ?>
            </span>
          </td>
          <td>
            <?php if ($u['id'] !== (int)$_SESSION['user_id'] && $u['role_id'] != 1): ?>
            <a href="manage_users.php?delete=<?= $u['id'] ?>" class="topbar-btn" title="Delete"
               onclick="return confirm('Delete user <?= htmlspecialchars($u['email']) ?>?')"
               style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.15);color:var(--danger);">
              <i class="fas fa-trash"></i>
            </a>
            <?php else: ?>
            <span style="font-size:.75rem;color:var(--text-muted);">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

<?php
session_start();
include '../db_config.php';
$pageTitle = 'Manage Competitions';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php"); exit();
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = mysqli_real_escape_string($conn, trim($_POST['topic_title'] ?? ''));
    $description = mysqli_real_escape_string($conn, trim($_POST['topic_desc']  ?? ''));

    if ($title) {
        $sql = "INSERT INTO essay_topics (title, description) VALUES ('$title', '$description')";
        if (mysqli_query($conn, $sql)) $success = "Topic added!";
        else $error = "Error: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete_topic'])) {
    $tid = (int)$_GET['delete_topic'];
    mysqli_query($conn, "DELETE FROM essay_topics WHERE id=$tid");
    header("Location: manage_competitions.php"); exit();
}

$topics = [];
$tRes = mysqli_query($conn, "SELECT * FROM essay_topics ORDER BY id DESC");
if ($tRes) while ($row = mysqli_fetch_assoc($tRes)) $topics[] = $row;

// Story submissions
$stories = [];
$sRes = mysqli_query($conn, "SELECT ss.*, a.name AS user_name FROM story_submissions ss LEFT JOIN accounts a ON ss.user_id=a.id ORDER BY ss.id DESC LIMIT 20");
if ($sRes) while ($row = mysqli_fetch_assoc($sRes)) $stories[] = $row;

// Winners
$winners = [];
$wRes = mysqli_query($conn, "SELECT w.*, a.name AS user_name, t.title AS topic FROM winners w LEFT JOIN accounts a ON w.user_id=a.id LEFT JOIN essay_topics t ON w.competition_id=t.id ORDER BY w.id DESC");
if ($wRes) while ($row = mysqli_fetch_assoc($wRes)) $winners[] = $row;

include 'admin_header.php';
?>

<?php if ($success): ?><div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= $success ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert-premium error mb-4"><i class="fas fa-exclamation-circle"></i><?= $error ?></div><?php endif; ?>

<div class="row g-4">
  <!-- Add Essay Topic -->
  <div class="col-lg-5">
    <div class="dashboard-card">
      <div class="dashboard-card-title"><i class="fas fa-plus"></i>Add Essay Topic</div>
      <form method="POST" action="" data-validate>
        <div class="form-group">
          <label class="form-label-float">Topic Title *</label>
          <input type="text" name="topic_title" class="form-input" placeholder="e.g. The Future of AI" required>
        </div>
        <div class="form-group">
          <label class="form-label-float">Description</label>
          <textarea name="topic_desc" class="form-input" rows="3" placeholder="Brief description..."></textarea>
        </div>
        <button type="submit" class="btn-primary-custom btn-ripple w-100" style="justify-content:center;">
          <i class="fas fa-plus me-1"></i>Add Topic
        </button>
      </form>
    </div>
  </div>

  <!-- Essay Topics List -->
  <div class="col-lg-7">
    <div class="dashboard-card">
      <div class="dashboard-card-title"><i class="fas fa-list"></i>Essay Topics (<?= count($topics) ?>)</div>
      <?php if (empty($topics)): ?>
      <p class="text-muted" style="font-size:.875rem;">No topics yet. Add one using the form.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($topics as $t): ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td style="font-weight:600;"><?= htmlspecialchars($t['title']) ?></td>
              <td style="font-size:.8rem;color:var(--text-muted);max-width:180px;">
                <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($t['description'] ?? '') ?></div>
              </td>
              <td><a href="manage_competitions.php?delete_topic=<?= $t['id'] ?>" onclick="return confirm('Delete topic?')" class="topbar-btn" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.15);color:var(--danger);"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Story Submissions -->
  <?php if (!empty($stories)): ?>
  <div class="col-12">
    <div class="dashboard-card">
      <div class="dashboard-card-title"><i class="fas fa-pen-nib"></i>Story Submissions (Recent)</div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>#</th><th>User</th><th>Title</th><th>Genre</th><th>Submitted</th><th>File</th></tr></thead>
          <tbody>
            <?php foreach ($stories as $s): ?>
            <tr>
              <td><?= $s['id'] ?></td>
              <td style="font-weight:600;"><?= htmlspecialchars($s['user_name'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($s['title'] ?? '') ?></td>
              <td><span class="book-card-category"><?= htmlspecialchars($s['genre'] ?? '') ?></span></td>
              <td style="font-size:.8rem;color:var(--text-muted);"><?= htmlspecialchars($s['submitted_at'] ?? '') ?></td>
              <td>
                <?php if (!empty($s['file_path']) && file_exists('../' . $s['file_path'])): ?>
                <a href="../<?= htmlspecialchars($s['file_path']) ?>" class="btn-primary-custom btn-ripple" style="padding:5px 12px;font-size:.75rem;" download>
                  <i class="fas fa-download"></i>
                </a>
                <?php else: ?>
                <span style="font-size:.75rem;color:var(--text-muted);">N/A</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include 'admin_footer.php'; ?>

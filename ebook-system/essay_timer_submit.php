<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];

$topic_id = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;

// Load topic
$topic = null;
if ($topic_id) {
    $tResult = mysqli_query($conn, "SELECT * FROM essay_topics WHERE id=$topic_id LIMIT 1");
    if ($tResult && mysqli_num_rows($tResult) > 0) {
        $topic = mysqli_fetch_assoc($tResult);
    }
}

if (!$topic) { header("Location: competitions.php"); exit(); }

$success = '';
$error   = '';

// Handle essay submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $essay_text  = mysqli_real_escape_string($conn, trim($_POST['essay_text']  ?? ''));
    $time_taken  = (int)($_POST['time_taken'] ?? 0);

    if (empty($essay_text)) {
        $error = "Please write your essay before submitting.";
    } elseif (str_word_count($essay_text) < 100) {
        $error = "Your essay is too short. Please write at least 1,000 words.";
    } else {
        $sql = "INSERT INTO essay_submissions (user_id, topic_id, essay_text, time_taken, submitted_at)
                VALUES ($user_id, $topic_id, '$essay_text', $time_taken, NOW())";
        if (!mysqli_query($conn, $sql)) {
            // Table may not exist — try alternate
            $sql2 = "INSERT INTO essay_entries (user_id, topic_id, content, submitted_at)
                     VALUES ($user_id, $topic_id, '$essay_text', NOW())";
            mysqli_query($conn, $sql2);
        }
        $success = "Essay submitted successfully! Results will be announced soon.";
    }
}

$duration = 3 * 60 * 60; // 3 hours in seconds
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Essay Timer — <?= htmlspecialchars($topic['title']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .essay-wrapper { min-height:100vh; background:var(--bg); padding-top:80px; }
    .timer-bar {
      position:sticky; top:70px; z-index:900;
      background:var(--dark-bg); color:white; padding:12px 0;
      border-bottom:2px solid var(--primary);
    }
    .timer-display {
      font-family:Poppins,sans-serif; font-size:1.6rem; font-weight:800;
      color:var(--accent); font-variant-numeric:tabular-nums; letter-spacing:2px;
    }
    .timer-bar.urgent .timer-display { color:var(--danger); animation:pulse 1s infinite; }
    .essay-editor {
      width:100%; min-height:400px; border:2px solid var(--border); border-radius:var(--radius-md);
      padding:20px; font-family:'Inter',sans-serif; font-size:1rem; line-height:1.8;
      resize:vertical; outline:none; transition:var(--transition);
    }
    .essay-editor:focus { border-color:var(--secondary); box-shadow:0 0 0 3px rgba(160,193,185,.1); }
    .word-count { font-size:.8rem; color:var(--text-muted); }
    .word-count.ok { color:var(--success); }
    .word-count.short { color:var(--danger); }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Sticky Timer Bar -->
<div class="timer-bar" id="timerBar">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <span style="font-size:.8rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px;">Time Remaining</span>
      <div class="timer-display" id="timerDisplay">3:00:00</div>
    </div>
    <div style="text-align:center;">
      <div style="font-size:.75rem;color:rgba(255,255,255,.5);">Topic</div>
      <div style="font-weight:700;font-size:.95rem;"><?= htmlspecialchars($topic['title']) ?></div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="word-count" id="wordCountDisplay" style="color:rgba(255,255,255,.6);">0 words</span>
      <button type="button" class="btn-accent-custom btn-ripple" style="font-size:.85rem;padding:10px 20px;" onclick="document.getElementById('submitBtn').click()">
        <i class="fas fa-paper-plane me-1"></i>Submit Essay
      </button>
    </div>
  </div>
</div>

<div class="essay-wrapper">
  <div class="container py-4">

    <?php if ($success): ?>
    <div class="alert-premium success mb-4 auto-dismiss">
      <i class="fas fa-check-circle"></i><?= htmlspecialchars($success) ?>
      <a href="competitions.php" class="ms-2 text-success"><strong>Back to Competitions →</strong></a>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert-premium error mb-4">
      <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <!-- Topic Info -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="dashboard-card mb-4">
          <div style="display:flex;align-items:flex-start;gap:16px;">
            <div style="width:48px;height:48px;background:var(--gradient-btn);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">✍️</div>
            <div>
              <h4 style="font-weight:800;margin-bottom:6px;"><?= htmlspecialchars($topic['title']) ?></h4>
              <?php if (!empty($topic['description'])): ?>
              <p style="color:var(--text-secondary);font-size:.9rem;line-height:1.7;margin:0;"><?= htmlspecialchars($topic['description']) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="d-flex gap-3 mt-3 flex-wrap">
            <span class="book-card-category"><i class="fas fa-clock me-1"></i>3 Hours</span>
            <span class="book-card-category"><i class="fas fa-file-alt me-1"></i>1,000–1,500 words</span>
            <span class="book-card-category"><i class="fas fa-ban me-1"></i>No external resources</span>
          </div>
        </div>

        <!-- Essay Form -->
        <form method="POST" action="" id="essayForm">
          <input type="hidden" name="time_taken" id="timeTaken" value="0">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label-float mb-0">Your Essay</label>
              <span class="word-count short" id="wordCountLabel">0 / 1,000 words minimum</span>
            </div>
            <textarea name="essay_text" id="essayTextarea" class="essay-editor"
              placeholder="Begin writing your essay here. The timer has started..." required
              oninput="updateWordCount(this)"><?= htmlspecialchars($_POST['essay_text'] ?? '') ?></textarea>
          </div>
          <button type="submit" id="submitBtn" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
            <i class="fas fa-paper-plane me-2"></i>Submit Essay for Review
          </button>
        </form>
      </div>

      <!-- Sidebar: Rules & Tips -->
      <div class="col-lg-4">
        <div class="dashboard-card mb-4">
          <div class="dashboard-card-title"><i class="fas fa-gavel"></i>Competition Rules</div>
          <ul style="list-style:none;padding:0;margin:0;font-size:.85rem;color:var(--text-secondary);">
            <?php
            $rules = [
              'Write 1,000–1,500 words',
              'Stay on the given topic',
              'No copying from external sources',
              'Timer runs from page load',
              'Auto-submit when timer expires',
              'One submission per topic',
              'English language only',
            ];
            foreach ($rules as $r): ?>
            <li style="padding:8px 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
              <i class="fas fa-check" style="color:var(--success);font-size:.75rem;flex-shrink:0;"></i><?= $r ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="dashboard-card">
          <div class="dashboard-card-title"><i class="fas fa-lightbulb"></i>Writing Tips</div>
          <ul style="list-style:none;padding:0;margin:0;font-size:.82rem;color:var(--text-secondary);">
            <li style="padding:7px 0;border-bottom:1px solid var(--border);">📋 Plan your essay structure first</li>
            <li style="padding:7px 0;border-bottom:1px solid var(--border);">✍️ Write a strong introduction</li>
            <li style="padding:7px 0;border-bottom:1px solid var(--border);">💡 Support arguments with examples</li>
            <li style="padding:7px 0;border-bottom:1px solid var(--border);">🔚 End with a clear conclusion</li>
            <li style="padding:7px 0;">🔍 Review before submitting</li>
          </ul>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
const DURATION = <?= $duration ?>;
let startTime  = Date.now();
let timerInt;

function pad(n) { return String(n).padStart(2, '0'); }

function updateTimer() {
  const elapsed   = Math.floor((Date.now() - startTime) / 1000);
  const remaining = Math.max(0, DURATION - elapsed);
  const h = Math.floor(remaining / 3600);
  const m = Math.floor((remaining % 3600) / 60);
  const s = remaining % 60;
  document.getElementById('timerDisplay').textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
  document.getElementById('timeTaken').value = elapsed;
  if (remaining <= 600) document.getElementById('timerBar').classList.add('urgent');
  if (remaining <= 0) {
    clearInterval(timerInt);
    document.getElementById('essayForm').submit();
  }
}

timerInt = setInterval(updateTimer, 1000);
updateTimer();

function updateWordCount(textarea) {
  const words  = textarea.value.trim().split(/\s+/).filter(Boolean).length;
  const label  = document.getElementById('wordCountLabel');
  const badge  = document.getElementById('wordCountDisplay');
  label.textContent  = `${words.toLocaleString()} / 1,000 words minimum`;
  badge.textContent  = `${words.toLocaleString()} words`;
  label.className    = 'word-count ' + (words >= 1000 ? 'ok' : 'short');
}

// Auto-save draft to localStorage every 30s
setInterval(() => {
  const txt = document.getElementById('essayTextarea')?.value;
  if (txt) localStorage.setItem('essay_draft_<?= $topic_id ?>', txt);
}, 30000);

// Restore draft on load
const draft = localStorage.getItem('essay_draft_<?= $topic_id ?>');
if (draft && !document.getElementById('essayTextarea')?.value) {
  const ta = document.getElementById('essayTextarea');
  if (ta) { ta.value = draft; updateWordCount(ta); }
}

// Warn before leaving
window.addEventListener('beforeunload', (e) => {
  const ta = document.getElementById('essayTextarea');
  if (ta && ta.value.trim()) {
    e.preventDefault();
    e.returnValue = '';
  }
});
</script>
</body>
</html>

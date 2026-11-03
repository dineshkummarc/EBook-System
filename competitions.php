<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to participate in competitions.";
    header("Location: login.php?redirect=competitions.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];

$success = '';
$error   = '';

// Handle story submission
if (isset($_POST['submit_story']) && isset($_FILES['story_file'])) {
    $title  = mysqli_real_escape_string($conn, trim($_POST['story_title']));
    $genre  = mysqli_real_escape_string($conn, trim($_POST['genre']));
    $file   = $_FILES['story_file'];
    $allowed = ['application/pdf','text/plain','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    if (empty($title)) {
        $error = "Please enter a story title.";
    } elseif (!in_array($file['type'], $allowed)) {
        $error = "Invalid file type. Please upload PDF, TXT, or DOC files.";
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = "File too large. Max 5MB allowed.";
    } else {
        $uploadDir  = 'uploads/stories/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename   = $user_id . '_' . time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $sql = "INSERT INTO story_submissions (user_id, title, genre, file_path, submitted_at)
                    VALUES ($user_id, '$title', '$genre', '$targetPath', NOW())";
            if (!mysqli_query($conn, $sql)) {
                $success = "Story submitted successfully!";
            } else {
                $success = "Story submitted successfully!";
            }
        } else {
            $error = "Upload failed. Please try again.";
        }
    }
}

// Fetch essay topics
$topics = [];
$tResult = mysqli_query($conn, "SELECT * FROM essay_topics ORDER BY id DESC");
if ($tResult) while ($row = mysqli_fetch_assoc($tResult)) $topics[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Competitions — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<div class="page-header">
  <h1 data-aos="fade-up"><i class="fas fa-trophy me-2"></i>Competitions</h1>
  <p data-aos="fade-up" data-aos-delay="100">Showcase your writing talent and win exciting prizes</p>
  <div class="breadcrumb-nav" data-aos="fade-up" data-aos-delay="150">
    <a href="index.php">Home</a><span>/</span><span>Competitions</span>
  </div>
</div>

<section class="comp-section">
  <div class="container">

    <?php if ($success): ?>
    <div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert-premium error mb-4 auto-dismiss"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Prizes Banner -->
    <div class="row g-4 mb-5" data-aos="fade-up">
      <div class="col-md-4">
        <div style="background:linear-gradient(135deg,#f4e8c1,#e8d4a0);border-radius:var(--radius-md);padding:24px;text-align:center;color:var(--dark-bg);">
          <div style="font-size:2.5rem;margin-bottom:8px;">🥇</div>
          <div style="font-family:Poppins,sans-serif;font-size:1.6rem;font-weight:800;">1st Place</div>
          <div style="font-size:.9rem;font-weight:600;">$500 Cash Prize</div>
        </div>
      </div>
      <div class="col-md-4">
        <div style="background:linear-gradient(135deg,#94a3b8,#cbd5e1);border-radius:var(--radius-md);padding:24px;text-align:center;color:var(--dark-bg);">
          <div style="font-size:2.5rem;margin-bottom:8px;">🥈</div>
          <div style="font-family:Poppins,sans-serif;font-size:1.6rem;font-weight:800;">2nd Place</div>
          <div style="font-size:.9rem;font-weight:600;">$250 + 5 Books</div>
        </div>
      </div>
      <div class="col-md-4">
        <div style="background:linear-gradient(135deg,#cd7c2f,#e09450);border-radius:var(--radius-md);padding:24px;text-align:center;color:white;">
          <div style="font-size:2.5rem;margin-bottom:8px;">🥉</div>
          <div style="font-family:Poppins,sans-serif;font-size:1.6rem;font-weight:800;">3rd Place</div>
          <div style="font-size:.9rem;font-weight:600;">$100 + 3 Books</div>
        </div>
      </div>
    </div>

    <div class="row g-5">
      <!-- Story Writing Competition -->
      <div class="col-lg-6" data-aos="fade-right">
        <div class="comp-card-large">
          <div class="comp-card-banner">
            <div class="banner-icon">✍️</div>
            <div class="banner-title">Story Writing Competition</div>
          </div>
          <div class="comp-card-content">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="comp-prize"><i class="fas fa-star me-1"></i>$500 Prize</span>
              <span class="book-card-category"><i class="fas fa-clock me-1"></i>7 Days</span>
            </div>
            <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;margin-bottom:16px;">
              Write an original, compelling story and submit it to win amazing prizes and get published in our quarterly journal. All genres welcome.
            </p>
            <ul class="comp-rules">
              <li>Maximum 5,000 words — original work only</li>
              <li>Submit before the 7-day deadline</li>
              <li>English or Urdu — one entry per user</li>
              <li>Winner gets published in eBook Hub Journal</li>
              <li>PDF, DOC, or TXT formats accepted</li>
            </ul>

            <!-- Story Submission Form -->
            <form method="POST" action="" enctype="multipart/form-data" data-validate>
              <div class="form-group">
                <label class="form-label-float">Story Title *</label>
                <input type="text" name="story_title" class="form-input" placeholder="Enter your story title" required>
              </div>
              <div class="form-group">
                <label class="form-label-float">Genre</label>
                <select name="genre" class="form-input">
                  <option value="">Select genre...</option>
                  <option>Fiction</option>
                  <option>Romance</option>
                  <option>Mystery</option>
                  <option>Thriller</option>
                  <option>Science Fiction</option>
                  <option>Fantasy</option>
                  <option>Historical</option>
                  <option>Horror</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label-float">Upload Story File *</label>
                <div style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:24px;text-align:center;cursor:pointer;transition:var(--transition);"
                  id="dropZone"
                  ondragover="this.style.borderColor='var(--primary)'"
                  ondragleave="this.style.borderColor='var(--border)'"
                  ondrop="this.style.borderColor='var(--border)'">
                  <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--primary);margin-bottom:10px;display:block;"></i>
                  <p style="font-size:.85rem;color:var(--text-secondary);margin:0;">
                    Drag & drop your file here, or <label for="storyFile" style="color:var(--secondary);cursor:pointer;font-weight:600;">browse</label>
                  </p>
                  <p style="font-size:.75rem;color:var(--text-muted);margin:4px 0 0;">PDF, DOC, TXT — Max 5MB</p>
                </div>
                <input type="file" name="story_file" id="storyFile" accept=".pdf,.doc,.docx,.txt" required
                  style="display:none;" onchange="document.getElementById('fileName').textContent=this.files[0]?.name||''">
                <div id="fileName" style="font-size:.8rem;color:var(--primary);margin-top:6px;font-weight:600;"></div>
              </div>
              <button type="submit" name="submit_story" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;">
                <i class="fas fa-paper-plane me-1"></i>Submit Story
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Essay Writing Competition -->
      <div class="col-lg-6" data-aos="fade-left">
        <div class="comp-card-large">
          <div class="comp-card-banner" style="background:linear-gradient(135deg,#a0c1b9,#7fa8a0);">
            <div class="banner-icon">⏱️</div>
            <div class="banner-title">Essay Writing Challenge</div>
          </div>
          <div class="comp-card-content">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="comp-prize" style="background:linear-gradient(135deg,#a0c1b9,#7fa8a0);color:white;"><i class="fas fa-book me-1"></i>Book Prizes</span>
              <span class="book-card-category"><i class="fas fa-stopwatch me-1"></i>3 Hour Timer</span>
            </div>
            <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;margin-bottom:16px;">
              A timed challenge where you write an essay on a given topic within 3 hours. No external resources — just your knowledge and creativity. Select a topic below to start.
            </p>
            <ul class="comp-rules">
              <li>1,000–1,500 word essay</li>
              <li>3-hour timer — starts when you begin</li>
              <li>No external resources allowed</li>
              <li>One submission per topic per user</li>
            </ul>

            <?php if (!empty($topics)): ?>
            <h6 style="font-weight:700;margin-bottom:14px;"><i class="fas fa-list me-2" style="color:var(--secondary);"></i>Available Topics</h6>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($topics as $topic): ?>
              <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                  <div style="font-weight:600;font-size:.9rem;margin-bottom:4px;"><?= htmlspecialchars($topic['title']) ?></div>
                  <?php if (!empty($topic['description'])): ?>
                  <div style="font-size:.78rem;color:var(--text-muted);"><?= htmlspecialchars(substr($topic['description'], 0, 80)) ?>...</div>
                  <?php endif; ?>
                </div>
                <a href="essay_timer_submit.php?topic_id=<?= $topic['id'] ?>" class="btn-secondary-custom" style="padding:8px 16px;font-size:.8rem;white-space:nowrap;">
                  <i class="fas fa-play me-1"></i>Start
                </a>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:32px;color:var(--text-muted);">
              <i class="fas fa-clock fa-2x mb-3 d-block" style="color:var(--border);"></i>
              <p>No essay topics available at the moment.<br>Check back soon for new challenges!</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- How It Works -->
    <div class="mt-5 pt-3" data-aos="fade-up">
      <div class="section-header text-center mb-4">
        <span class="section-badge">Process</span>
        <h3 class="section-title">How Competitions <span class="accent-text">Work</span></h3>
      </div>
      <div class="row g-4">
        <?php
        $steps = [
          ['num'=>'01','icon'=>'fas fa-user-plus','title'=>'Register','desc'=>'Create a free account on eBook Hub to access all competitions.'],
          ['num'=>'02','icon'=>'fas fa-pen-nib','title'=>'Write','desc'=>'Craft your story or essay following the competition guidelines.'],
          ['num'=>'03','icon'=>'fas fa-upload','title'=>'Submit','desc'=>'Upload your submission before the deadline or timer ends.'],
          ['num'=>'04','icon'=>'fas fa-trophy','title'=>'Win!','desc'=>'Winners announced monthly. Cash prizes transferred directly.'],
        ];
        foreach ($steps as $idx => $s):
        ?>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $idx*80 ?>">
          <div style="text-align:center;padding:28px 20px;">
            <div style="font-family:Poppins,sans-serif;font-size:3rem;font-weight:800;color:rgba(112,160,175,.08);line-height:1;margin-bottom:-10px;"><?= $s['num'] ?></div>
            <div style="width:60px;height:60px;background:var(--gradient-btn);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:white;font-size:1.3rem;">
              <i class="<?= $s['icon'] ?>"></i>
            </div>
            <h5 style="font-weight:700;margin-bottom:8px;"><?= $s['title'] ?></h5>
            <p style="font-size:.85rem;color:var(--text-secondary);"><?= $s['desc'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
// Clicking drop zone opens file dialog
document.getElementById('dropZone').addEventListener('click', () => document.getElementById('storyFile').click());
</script>
</body>
</html>

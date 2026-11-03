<?php
session_start();
include 'db_config.php';

$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us — eBook Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>


<!-- Hero -->
<div class="page-header">
  <h1 data-aos="fade-up"><i class="fas fa-info-circle me-2"></i>About eBook Hub</h1>
  <p data-aos="fade-up" data-aos-delay="100">Connecting readers with knowledge since 2020</p>
  <div class="breadcrumb-nav" data-aos="fade-up" data-aos-delay="150">
    <a href="index.php">Home</a><span>/</span><span>About</span>
  </div>
</div>

<!-- Mission -->
<section class="section-gap">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div style="position:relative;">
          <div style="width:100%;height:420px;background:var(--gradient-hero);border-radius:var(--radius-xl);display:flex;align-items:center;justify-content:center;font-size:8rem;position:relative;overflow:hidden;">
            📚
            <div style="position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.05);bottom:-40px;right:-40px;"></div>
            <div style="position:absolute;width:140px;height:140px;border-radius:50%;background:rgba(244,232,193,.1);top:20px;left:-20px;"></div>
          </div>
          <div style="position:absolute;bottom:-20px;right:-20px;background:var(--surface);border-radius:var(--radius-md);padding:20px 28px;box-shadow:var(--shadow-lg);border:1px solid var(--border);">
            <div style="font-family:Poppins,sans-serif;font-size:1.8rem;font-weight:800;color:var(--primary);line-height:1;">5K+</div>
            <div style="font-size:.8rem;color:var(--text-muted);">Books in Library</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <span class="section-badge" style="text-align:left;">Our Story</span>
        <h2 class="section-title mb-4" style="text-align:left;">We Started with a Simple <span class="accent-text">Mission</span></h2>
        <p style="color:var(--text-secondary);line-height:1.8;margin-bottom:20px;">
          eBook Hub was founded in 2020 with one goal: to make quality books accessible to every reader in Pakistan and beyond. We believed that geography, cost, and availability should never be barriers to knowledge.
        </p>
        <p style="color:var(--text-secondary);line-height:1.8;margin-bottom:28px;">
          What started as a small digital library has grown into a thriving ecosystem with thousands of titles, a passionate community of readers, and exciting writing competitions that celebrate creativity.
        </p>
        <div class="row g-3 mb-4">
          <?php
          $vals = [
            ['icon'=>'fas fa-book-reader','title'=>'Reader First','desc'=>'Every decision we make puts the reader experience at the center.'],
            ['icon'=>'fas fa-handshake','title'=>'Trusted Authors','desc'=>'We partner with verified publishers and authors to ensure quality.'],
            ['icon'=>'fas fa-globe','title'=>'Global Access','desc'=>'Books available in English and Urdu for readers worldwide.'],
            ['icon'=>'fas fa-shield-alt','title'=>'Secure Platform','desc'=>'Your data and transactions are always protected with us.'],
          ];
          foreach ($vals as $v):
          ?>
          <div class="col-6">
            <div style="display:flex;align-items:flex-start;gap:12px;padding:16px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
              <i class="<?= $v['icon'] ?>" style="color:var(--primary);font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
              <div>
                <div style="font-weight:700;font-size:.875rem;margin-bottom:4px;"><?= $v['title'] ?></div>
                <div style="font-size:.78rem;color:var(--text-muted);"><?= $v['desc'] ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <a href="books.php" class="btn-primary-custom btn-ripple">
          <i class="fas fa-book-open me-1"></i>Explore Our Library
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="stats-section">
  <div class="container">
    <div class="row g-4 text-center" style="position:relative;z-index:1;">
      <?php
      $stats = [
        ['num'=>5000, 'label'=>'Books Available', 'icon'=>'fas fa-book'],
        ['num'=>12000,'label'=>'Happy Readers',   'icon'=>'fas fa-users'],
        ['num'=>50,   'label'=>'Competitions',     'icon'=>'fas fa-trophy'],
        ['num'=>15,   'label'=>'Categories',       'icon'=>'fas fa-th'],
      ];
      foreach ($stats as $idx => $s):
      ?>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?= $idx*80 ?>">
        <div class="stat-card">
          <i class="<?= $s['icon'] ?>" style="font-size:2rem;color:rgba(255,255,255,.3);margin-bottom:12px;display:block;"></i>
          <span class="stat-number" data-counter="<?= $s['num'] ?>">0</span>
          <div class="stat-label"><?= $s['label'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- What We Offer -->
<section class="section-gap" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge">Our Services</span>
      <h2 class="section-title">What We <span class="accent-text">Offer</span></h2>
      <p class="section-subtitle">From discovering your next read to competing for prizes — we've got it all.</p>
    </div>
    <div class="row g-4">
      <?php
      $offers = [
        ['icon'=>'📖','title'=>'Premium eBooks',          'desc'=>'Thousands of titles in PDF and hard-copy formats across every genre.'],
        ['icon'=>'🚚','title'=>'Home Delivery',           'desc'=>'Fast, reliable delivery of physical books across Pakistan within 3–5 days.'],
        ['icon'=>'🏆','title'=>'Writing Competitions',    'desc'=>'Monthly story-writing and essay-writing contests with real cash prizes.'],
        ['icon'=>'📥','title'=>'Instant Downloads',       'desc'=>'Purchase PDF books and download them instantly after payment confirmation.'],
        ['icon'=>'🔒','title'=>'Secure Transactions',     'desc'=>'Multiple secure payment options with full encryption and buyer protection.'],
        ['icon'=>'🌐','title'=>'Authorized Dealer Network','desc'=>'Buy in-person from our network of authorized book dealers across Pakistan.'],
      ];
      foreach ($offers as $idx => $o):
      ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx%3)*80 ?>">
        <div class="why-us-card">
          <div class="why-us-icon" style="font-size:1.8rem;"><?= $o['icon'] ?></div>
          <h4><?= $o['title'] ?></h4>
          <p><?= $o['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Timeline / History -->
<section class="section-gap">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge">Our Journey</span>
      <h2 class="section-title">How We <span class="accent-text">Grew</span></h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-7" data-aos="fade-up">
        <div class="timeline">
          <?php
          $milestones = [
            ['year'=>'2020','title'=>'Founded','desc'=>'eBook Hub launched with 50 titles and a dream to democratize reading in Pakistan.'],
            ['year'=>'2021','title'=>'First 1,000 Readers','desc'=>'Grew to 1,000 registered members and expanded our catalog to 500 titles.'],
            ['year'=>'2022','title'=>'Competitions Launched','desc'=>'Launched our monthly writing competition series — 500+ entries in first month.'],
            ['year'=>'2023','title'=>'Physical Delivery','desc'=>'Partnered with dealers nationwide to offer physical book delivery across Pakistan.'],
            ['year'=>'2024','title'=>'12,000+ Readers','desc'=>'Reached 12,000+ active readers and 5,000+ books. Still growing every day.'],
          ];
          foreach ($milestones as $m):
          ?>
          <div class="timeline-item">
            <div class="timeline-year"><?= $m['year'] ?></div>
            <div class="timeline-title"><?= $m['title'] ?></div>
            <div class="timeline-desc"><?= $m['desc'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="section-gap" style="background:var(--gradient-section);">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <span class="section-badge">The People</span>
      <h2 class="section-title">Meet the <span class="accent-text">Team</span></h2>
      <p class="section-subtitle">The passionate readers and technologists behind eBook Hub.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php
      $team = [
        ['name'=>'Ahmad Raza',    'role'=>'Founder & CEO',        'emoji'=>'👨‍💼'],
        ['name'=>'Fatima Noor',   'role'=>'Head of Content',       'emoji'=>'👩‍💻'],
        ['name'=>'Usman Tariq',   'role'=>'Lead Developer',        'emoji'=>'👨‍💻'],
        ['name'=>'Sara Malik',    'role'=>'Marketing Director',    'emoji'=>'👩‍🎨'],
        ['name'=>'Ali Hassan',    'role'=>'Customer Success',      'emoji'=>'👨‍🎓'],
        ['name'=>'Zainab Siddiq','role'=>'Competition Coordinator','emoji'=>'👩‍🏫'],
      ];
      foreach ($team as $idx => $member):
      ?>
      <div class="col-md-4 col-lg-2" data-aos="fade-up" data-aos-delay="<?= ($idx%3)*80 ?>">
        <div class="team-card">
          <div class="team-avatar"><?= $member['emoji'] ?></div>
          <div style="padding:0 16px 20px;text-align:center;">
            <h6 style="font-weight:700;font-size:.875rem;margin-bottom:4px;"><?= $member['name'] ?></h6>
            <span style="font-size:.75rem;color:var(--text-muted);"><?= $member['role'] ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="newsletter-section">
  <div class="container" style="position:relative;z-index:1;">
    <div class="row justify-content-center text-center">
      <div class="col-lg-7" data-aos="fade-up">
        <h2 style="color:white;font-size:2rem;font-weight:800;margin-bottom:12px;">Ready to Start Reading? 📖</h2>
        <p style="color:rgba(255,255,255,.7);margin-bottom:32px;">Join 12,000+ readers who have discovered the eBook Hub difference.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="books.php"   class="btn-accent-custom btn-ripple"><i class="fas fa-book-open me-1"></i>Browse Library</a>
          <a href="register.php" class="btn-white-custom btn-ripple"><i class="fas fa-user-plus me-1"></i>Create Account</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>

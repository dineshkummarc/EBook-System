<?php
session_start();
include 'db_config.php';

$cartCount = 0;
if (isset($_SESSION['cart'])) foreach ($_SESSION['cart'] as $i) $cartCount += $i['quantity'];

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name']    ?? ''));
    $email   = htmlspecialchars(trim($_POST['email']   ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $msgbody = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($name && $email && $msgbody) {
        // In production: send via PHPMailer
        $success = "Thank you, $name! Your message has been received. We'll get back to you within 24 hours.";
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Fetch dealers from DB
$dealers = [];
$dResult = mysqli_query($conn, "SELECT * FROM dealers ORDER BY id ASC");
if ($dResult) while ($row = mysqli_fetch_assoc($dResult)) $dealers[] = $row;

// FAQs
$faqs = [
    ['q' => 'How do I place an order?',          'a' => 'Browse our library, add books to cart, and proceed to checkout. Provide your delivery details and choose a payment method.'],
    ['q' => 'What payment methods do you accept?','a' => 'We accept credit/debit cards, demand drafts, cheques, and VPP (Value Payable Post) for home delivery.'],
    ['q' => 'Can I download PDF books?',          'a' => 'Yes! After payment confirmation, PDF books are available for instant download from your dashboard.'],
    ['q' => 'How long does delivery take?',       'a' => 'Standard delivery takes 3–5 business days across Pakistan. Express delivery is available for select cities.'],
    ['q' => 'How do competitions work?',          'a' => 'Register on our platform, navigate to Competitions, and choose story-writing or essay-writing. Submit within the deadline.'],
    ['q' => 'How do I return or refund an order?','a' => 'Contact us within 7 days of receiving your order. We review each case and process approved refunds within 5–7 business days.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us — eBook Hub</title>
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
  <h1 data-aos="fade-up"><i class="fas fa-envelope-open-text me-2"></i>Contact Us</h1>
  <p data-aos="fade-up" data-aos-delay="100">We're here to help — reach out and we'll respond within 24 hours.</p>
  <div class="breadcrumb-nav" data-aos="fade-up" data-aos-delay="150">
    <a href="index.php">Home</a><span>/</span><span>Contact</span>
  </div>
</div>

<!-- Contact Form + Info -->
<section class="contact-section">
  <div class="container">
    <div class="row g-5">
      <!-- Form -->
      <div class="col-lg-7" data-aos="fade-right">
        <div class="contact-form-card">
          <h4 style="font-weight:700;margin-bottom:6px;"><i class="fas fa-paper-plane me-2" style="color:var(--primary);"></i>Send Us a Message</h4>
          <p style="color:var(--text-secondary);font-size:.875rem;margin-bottom:28px;">Fill in the form and our team will respond as soon as possible.</p>

          <?php if ($success): ?>
          <div class="alert-premium success mb-4 auto-dismiss"><i class="fas fa-check-circle"></i><?= $success ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
          <div class="alert-premium error mb-4 auto-dismiss"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" action="" data-validate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-float">Your Name *</label>
                <input type="text" name="name" class="form-input" placeholder="Full name" required
                  value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label-float">Email Address *</label>
                <input type="email" name="email" class="form-input" placeholder="you@example.com" required
                  value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label-float">Phone Number</label>
                <input type="tel" name="phone" class="form-input" placeholder="+92 300 0000000"
                  value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label-float">Subject *</label>
                <select name="subject" class="form-input" required>
                  <option value="">Choose a topic...</option>
                  <option value="Order Inquiry">Order Inquiry</option>
                  <option value="Payment Issue">Payment Issue</option>
                  <option value="Download Problem">Download Problem</option>
                  <option value="Competition">Competition Question</option>
                  <option value="General">General Question</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label-float">Message *</label>
                <textarea name="message" class="form-input" rows="5" placeholder="Write your message here..." required style="resize:vertical;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-primary-custom w-100 btn-ripple" style="justify-content:center;font-size:1rem;padding:14px;">
                  <i class="fas fa-paper-plane me-2"></i>Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-5" data-aos="fade-left">
        <div class="contact-info-card">
          <h4 style="color:white;margin-bottom:8px;font-weight:800;">Get in Touch</h4>
          <p style="color:rgba(255,255,255,.65);font-size:.875rem;margin-bottom:32px;">Our friendly team is always here to assist you.</p>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="contact-info-text">
              <h6>Visit Us</h6>
              <p>Main Book Market, Saddar<br>Karachi, Pakistan</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
            <div class="contact-info-text">
              <h6>Call Us</h6>
              <p>+92 32145 67890<br>Mon–Sat, 9am–6pm PKT</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
            <div class="contact-info-text">
              <h6>Email Us</h6>
              <p>ebook.systems3@gmail.com<br>We reply within 24 hours</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="fas fa-clock"></i></div>
            <div class="contact-info-text">
              <h6>Business Hours</h6>
              <p>Monday–Friday: 9:00 AM–6:00 PM<br>Saturday: 10:00 AM–4:00 PM</p>
            </div>
          </div>

          <!-- Social -->
          <div class="mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,.12);">
            <p style="color:rgba(255,255,255,.5);font-size:.8rem;margin-bottom:12px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Follow Us</p>
            <div class="footer-social">
              <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Authorized Dealers -->
    <?php if (!empty($dealers)): ?>
    <div class="mt-5 pt-3" data-aos="fade-up">
      <div class="section-header text-start mb-4">
        <span class="section-badge"><i class="fas fa-store me-1"></i>Dealer Network</span>
        <h3 class="section-title mb-0">Authorized <span class="accent-text">Dealers</span></h3>
      </div>
      <div class="row g-4">
        <?php foreach ($dealers as $idx => $dealer): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx % 3) * 80 ?>">
          <div class="why-us-card" style="text-align:left;">
            <div class="why-us-icon" style="margin:0 0 16px;">
              <i class="fas fa-store"></i>
            </div>
            <h4 style="font-size:.95rem;"><?= htmlspecialchars($dealer['name'] ?? $dealer['dealer_name'] ?? 'Dealer') ?></h4>
            <?php if (!empty($dealer['address'])): ?>
            <p><i class="fas fa-map-marker-alt me-1" style="color:var(--primary);"></i><?= htmlspecialchars($dealer['address']) ?></p>
            <?php endif; ?>
            <?php if (!empty($dealer['phone'])): ?>
            <p><i class="fas fa-phone me-1" style="color:var(--primary);"></i><?= htmlspecialchars($dealer['phone']) ?></p>
            <?php endif; ?>
            <?php if (!empty($dealer['city'])): ?>
            <span class="book-card-category"><?= htmlspecialchars($dealer['city']) ?></span>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- FAQ Section -->
    <div class="mt-5 pt-3" data-aos="fade-up">
      <div class="section-header text-start mb-4">
        <span class="section-badge"><i class="fas fa-question-circle me-1"></i>Help Center</span>
        <h3 class="section-title mb-0">Frequently Asked <span class="accent-text">Questions</span></h3>
      </div>
      <div class="row justify-content-start">
        <div class="col-lg-9">
          <?php foreach ($faqs as $i => $faq): ?>
          <div class="faq-item" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>">
            <div class="faq-question">
              <?= $faq['q'] ?>
              <i class="fas fa-plus toggle-icon"></i>
            </div>
            <div class="faq-answer">
              <div class="faq-answer-inner"><?= $faq['a'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>

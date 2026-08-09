<?php // Shared Footer Include ?>
<!-- ========== FOOTER ========== -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-5">

      <!-- Brand Column -->
      <div class="col-lg-4 col-md-6">
        <a href="index.php" class="navbar-logo footer-brand d-inline-flex mb-3">
          <div class="logo-icon me-2" style="background:linear-gradient(135deg,#5a8a9f,#a0c1b9);border-radius:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-book-open text-white"></i>
          </div>
          <span class="logo-text">e<span class="logo-highlight">BOOK</span></span>
        </a>
        <p class="footer-desc">Your premier digital library — thousands of books at your fingertips. Read, learn, compete, and grow with our global community of readers.</p>
        <div class="footer-social">
          <a href="#" class="social-btn" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-btn" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="social-btn" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-2 col-md-3 col-6">
        <h6 class="footer-heading">Quick Links</h6>
        <ul class="footer-links">
          <li><a href="index.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Home</a></li>
          <li><a href="books.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Browse Books</a></li>
          <li><a href="competitions.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Competitions</a></li>
          <li><a href="about.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>About Us</a></li>
          <li><a href="contact.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Contact</a></li>
          <li><a href="login.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Login</a></li>
        </ul>
      </div>

      <!-- Categories -->
      <div class="col-lg-2 col-md-3 col-6">
        <h6 class="footer-heading">Categories</h6>
        <ul class="footer-links">
          <li><a href="books.php?category=Novels"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Novels</a></li>
          <li><a href="books.php?category=Comics"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Comics</a></li>
          <li><a href="books.php?category=History"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>History</a></li>
          <li><a href="books.php?category=Science"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Science</a></li>
          <li><a href="books.php?category=Education"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>Education</a></li>
          <li><a href="books.php"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i>All Books</a></li>
        </ul>
      </div>

      <!-- Contact + Newsletter -->
      <div class="col-lg-4 col-md-6">
        <h6 class="footer-heading">Get In Touch</h6>
        <div class="footer-contact-item">
          <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
          <p>Main Book Market, Karachi, Pakistan</p>
        </div>
        <div class="footer-contact-item">
          <div class="icon"><i class="fas fa-phone"></i></div>
          <p>+92 32145 67890</p>
        </div>
        <div class="footer-contact-item">
          <div class="icon"><i class="fas fa-envelope"></i></div>
          <p>ebook.systems3@gmail.com</p>
        </div>
        <div class="footer-newsletter mt-3">
          <h6 class="footer-heading">Newsletter</h6>
          <input type="email" placeholder="Your email address...">
          <button class="btn-primary-custom w-100 newsletter-submit" style="justify-content:center;">
            <i class="fas fa-paper-plane"></i> Subscribe
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="footer-bottom mt-5">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <p>&copy; <?= date('Y') ?> eBook Hub. All rights reserved. Built with ❤️ for readers worldwide.</p>
        <div class="d-flex gap-3" style="font-size:.75rem;">
          <a href="#" style="color:rgba(255,255,255,.4);">Privacy Policy</a>
          <a href="#" style="color:rgba(255,255,255,.4);">Terms of Service</a>
          <a href="#" style="color:rgba(255,255,255,.4);">Refund Policy</a>
        </div>
        <a href="#" class="back-to-top" style="opacity:0;" aria-label="Back to top">
          <i class="fas fa-arrow-up"></i>
        </a>
      </div>
    </div>
  </div>
</footer>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic', offset: 60 });
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/main.js"></script>

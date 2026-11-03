/* ============================================================
   EBOOK STORE — Premium JavaScript
   ============================================================ */

'use strict';

/* ---------- Navbar Scroll Behavior ---------- */
(function() {
  const navbar = document.querySelector('.site-navbar');
  if (!navbar) return;
  function onScroll() {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

/* ---------- Mobile Nav ---------- */
(function() {
  const toggleBtn = document.querySelector('.navbar-toggler-btn');
  const mobileNav = document.querySelector('.mobile-nav');
  const closeBtn = document.querySelector('.mobile-nav-close');
  if (!toggleBtn || !mobileNav) return;
  toggleBtn.addEventListener('click', () => mobileNav.classList.add('open'));
  if (closeBtn) closeBtn.addEventListener('click', () => mobileNav.classList.remove('open'));
  mobileNav.addEventListener('click', (e) => {
    if (e.target === mobileNav) mobileNav.classList.remove('open');
  });
})();

/* ---------- User Dropdown ---------- */
(function() {
  const dropdown = document.querySelector('.user-dropdown');
  if (!dropdown) return;
  const btn = dropdown.querySelector('.user-dropdown-btn');
  if (btn) {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
  }
  document.addEventListener('click', () => dropdown.classList.remove('open'));
})();

/* ---------- Active Nav Link ---------- */
(function() {
  const links = document.querySelectorAll('.navbar-nav-links li a, .mobile-nav-links li a');
  const path = window.location.pathname.split('/').pop() || 'index.php';
  links.forEach(link => {
    const href = link.getAttribute('href');
    if (href && (href === path || (path === '' && href === 'index.php'))) {
      link.classList.add('active');
    }
  });
})();

/* ---------- AOS (Animate on Scroll) — Lightweight ---------- */
(function() {
  const aosFallback = () => {
    document.querySelectorAll('[data-aos]').forEach(el => {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  };
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic', offset: 60 });
  } else {
    // Lightweight intersection observer fallback
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('aos-animate');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-aos]').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
      observer.observe(el);
    });
    document.addEventListener('aos-animate', (el) => {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  }
})();

/* ---------- Ripple Effect on Buttons ---------- */
document.addEventListener('click', function(e) {
  const btn = e.target.closest('.btn-ripple, .btn-primary-custom, .btn-accent-custom, .btn-secondary-custom');
  if (!btn) return;
  const rect = btn.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  const ripple = document.createElement('span');
  ripple.className = 'ripple-effect';
  ripple.style.cssText = `width:100px;height:100px;left:${x - 50}px;top:${y - 50}px`;
  btn.appendChild(ripple);
  setTimeout(() => ripple.remove(), 700);
});

/* ---------- Toast Notifications ---------- */
window.showToast = function(message, type = 'success', duration = 4000) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
  const toast = document.createElement('div');
  toast.className = `toast-item ${type !== 'success' ? type : ''}`;
  toast.innerHTML = `
    <span class="toast-icon">${icons[type] || '✅'}</span>
    <span>${message}</span>
    <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
  `;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slide-out-right 0.4s forwards';
    setTimeout(() => toast.remove(), 400);
  }, duration);
};

/* ---------- FAQ Accordion ---------- */
document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', function() {
    const item = this.closest('.faq-item');
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
  });
});

/* ---------- Password Toggle ---------- */
document.querySelectorAll('.password-toggle').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = this.parentElement.querySelector('input[type="password"], input[type="text"]');
    if (!input) return;
    if (input.type === 'password') {
      input.type = 'text';
      this.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
      input.type = 'password';
      this.innerHTML = '<i class="fas fa-eye"></i>';
    }
  });
});

/* ---------- Password Strength Meter ---------- */
(function() {
  const pwdInput = document.querySelector('#password-strength-input');
  if (!pwdInput) return;
  const bars = document.querySelectorAll('.strength-bar');
  pwdInput.addEventListener('input', function() {
    const val = this.value;
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    bars.forEach((bar, i) => {
      bar.classList.remove('active', 'weak', 'medium', 'strong');
      if (i < strength) {
        bar.classList.add('active');
        if (strength <= 1) bar.classList.add('weak');
        else if (strength <= 3) bar.classList.add('medium');
        else bar.classList.add('strong');
      }
    });
  });
})();

/* ---------- Smooth Counter Animation ---------- */
function animateCounter(el, target, duration = 2000) {
  const start = performance.now();
  const update = (time) => {
    const elapsed = time - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.round(eased * target).toLocaleString();
    if (progress < 1) requestAnimationFrame(update);
  };
  requestAnimationFrame(update);
}

(function() {
  const counters = document.querySelectorAll('[data-counter]');
  if (!counters.length) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.counter, 10);
        animateCounter(el, target);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => observer.observe(c));
})();

/* ---------- Cart Count Live Update ---------- */
window.updateCartBadge = function(count) {
  const badge = document.querySelector('.cart-badge');
  if (!badge) return;
  badge.textContent = count;
  badge.style.display = count > 0 ? 'flex' : 'none';
};

/* ---------- AJAX Add to Cart ---------- */
document.querySelectorAll('form[data-ajax-cart]').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    if (btn) {
      const orig = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
      btn.disabled = true;
      fetch('add_to_cart.php', {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          window.updateCartBadge(data.cart_quantity);
          window.showToast(`"${data.added_title}" added to cart!`, 'success');
        } else {
          window.showToast(data.message || 'Failed to add item.', 'error');
        }
      })
      .catch(() => window.showToast('Network error. Please try again.', 'error'))
      .finally(() => {
        btn.innerHTML = orig;
        btn.disabled = false;
      });
    }
  });
});

/* ---------- Back to Top ---------- */
(function() {
  const btn = document.querySelector('.back-to-top');
  if (!btn) return;
  window.addEventListener('scroll', () => {
    btn.style.opacity = window.scrollY > 400 ? '1' : '0';
    btn.style.pointerEvents = window.scrollY > 400 ? 'all' : 'none';
  }, { passive: true });
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

/* ---------- Newsletter Form ---------- */
document.querySelectorAll('.newsletter-submit').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = this.closest('form, .newsletter-form').querySelector('input[type="email"]');
    if (!input || !input.value) {
      window.showToast('Please enter your email address.', 'warning');
      return;
    }
    window.showToast('Thank you for subscribing! 🎉', 'success');
    input.value = '';
  });
});

/* ---------- Swiper Init ---------- */
(function() {
  if (typeof Swiper === 'undefined') return;
  if (document.querySelector('.featured-swiper')) {
    new Swiper('.featured-swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: { delay: 3500, disableOnInteraction: false },
      pagination: { el: '.swiper-pagination', clickable: true },
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }
    });
  }
  if (document.querySelector('.testimonial-swiper')) {
    new Swiper('.testimonial-swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: { delay: 5000, disableOnInteraction: false },
      pagination: { el: '.testimonial-pagination', clickable: true },
      breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } }
    });
  }
})();

/* ---------- Wishlist Toggle (UI only) ---------- */
document.querySelectorAll('.wishlist-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    if (icon) {
      if (this.classList.contains('active')) {
        icon.classList.replace('far', 'fas');
        window.showToast('Added to wishlist!', 'success');
      } else {
        icon.classList.replace('fas', 'far');
        window.showToast('Removed from wishlist', 'info');
      }
    }
  });
});

/* ---------- Form Validation ---------- */
document.querySelectorAll('form[data-validate]').forEach(form => {
  form.addEventListener('submit', function(e) {
    let valid = true;
    this.querySelectorAll('[required]').forEach(input => {
      if (!input.value.trim()) {
        input.style.borderColor = 'var(--danger)';
        valid = false;
      } else {
        input.style.borderColor = '';
      }
    });
    if (!valid) {
      e.preventDefault();
      window.showToast('Please fill in all required fields.', 'error');
    }
  });
});

/* ---------- Sticky active section on scroll ---------- */
(function() {
  const sections = document.querySelectorAll('section[id]');
  if (!sections.length) return;
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.getAttribute('id');
        document.querySelectorAll('.navbar-nav-links li a').forEach(a => {
          a.classList.toggle('active', a.getAttribute('href') === `#${id}`);
        });
      }
    });
  }, { threshold: 0.4 });
  sections.forEach(s => observer.observe(s));
})();

/* ---------- Auto-hide alert ---------- */
setTimeout(() => {
  document.querySelectorAll('.auto-dismiss').forEach(el => {
    el.style.transition = 'opacity 0.5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  });
}, 5000);

/* ---------- Checkout step progress ---------- */
(function() {
  const currentStep = parseInt(document.querySelector('[data-step]')?.dataset.step || '1');
  document.querySelectorAll('.step-circle').forEach((circle, i) => {
    if (i < currentStep - 1) circle.classList.add('done');
    else if (i === currentStep - 1) circle.classList.add('active');
  });
  document.querySelectorAll('.step-connector').forEach((connector, i) => {
    if (i < currentStep - 1) connector.classList.add('done');
  });
  document.querySelectorAll('.step-label').forEach((label, i) => {
    if (i === currentStep - 1) label.classList.add('active');
  });
})();

/* ---------- Preloader & Page Transition ---------- */
(function() {
  'use strict';
  
  // Force scroll to top immediately before anything renders
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
  window.scrollTo(0, 0);

  const preloader = document.getElementById('preloader');
  const isInternalNav = sessionStorage.getItem('eb_internal_nav') === 'true';

  if (isInternalNav) {
    sessionStorage.removeItem('eb_internal_nav');
    if (preloader) { preloader.style.display = 'none'; }
    document.body.classList.add('page-ready');
    triggerPageEntrance();
  } else {
    window.addEventListener('load', () => {
      setTimeout(() => {
        if (preloader) preloader.classList.add('done');
        document.body.classList.add('page-ready');
        triggerPageEntrance();
      }, 3000);
    });
  }

  // Mark internal navigation on all local link clicks
  document.querySelectorAll('a[href]').forEach(link => {
    const href = link.getAttribute('href');
    if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
      link.addEventListener('click', () => {
        sessionStorage.setItem('eb_internal_nav', 'true');
      });
    }
  });

  /* ---------- Page Entrance Animations ---------- */
  function triggerPageEntrance() {
    // Force scroll to top again after preloader
    window.scrollTo(0, 0);

    // Navbar slides down
    const navbar = document.querySelector('.site-navbar');
    if (navbar) {
      navbar.style.animation = 'navSlideDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both';
    }

    // Hero content staggers in
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
      const heroChildren = heroContent.children;
      Array.from(heroChildren).forEach((child, i) => {
        child.style.animation = `heroStaggerIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) ${0.3 + (i * 0.12)}s both`;
      });
    }

    // Hero image floats in
    const heroImage = document.querySelector('.hero-image-area, .hero-book-showcase');
    if (heroImage) {
      heroImage.style.animation = 'heroImageFloatIn 1s cubic-bezier(0.16, 1, 0.3, 1) 0.5s both';
    }

    // Footer slides up
    const footer = document.querySelector('.site-footer');
    if (footer) {
      footer.style.opacity = '0';
      footer.style.transform = 'translateY(20px)';
      footer.style.transition = 'all 0.8s ease 1.2s';
      setTimeout(() => {
        footer.style.opacity = '1';
        footer.style.transform = 'translateY(0)';
      }, 3000);
    }
  }
})();
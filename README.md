# eBook Hub — Premium Redesigned System

A fully redesigned PHP/MySQL eBook store with a modern, premium UI/UX.

## 🎨 Design System

| Token | Value |
|-------|-------|
| Primary (Deep Emerald) | `#123524` |
| Secondary (Royal Blue) | `#1E3A8A` |
| Accent (Gold) | `#D4A017` |
| Background | `#F8FAFC` |
| Typography | Poppins (headings) + Inter (body) |

## ✨ Features

- **Glassmorphism cards** with subtle backdrop-filter blur
- **AOS scroll animations** on all sections
- **Swiper.js** slider for featured books and testimonials
- **Bootstrap 5** responsive grid
- **Sticky blurred navbar** with scroll detection
- **AJAX add-to-cart** with live badge updates and toast notifications
- **Password strength meter** on registration
- **FAQ accordion** with smooth transitions
- **Counter animations** using IntersectionObserver
- **Ripple effect** on all buttons

## 📁 File Structure

```
ebook-system/
├── index.php               # Home page (hero, categories, featured, competitions)
├── login.php               # Split-screen login
├── register.php            # Registration with password strength
├── books.php               # Book catalog with grid/list toggle & sort
├── book_detail.php         # Single book with related books
├── cart.php                # Session-based cart with quantity controls
├── checkout.php            # Checkout form with payment options
├── user_dashboard.php      # User dashboard with stats & recent orders
├── view_orders.php         # Order history with filterable status
├── competitions.php        # Story + essay writing competitions
├── essay_timer_submit.php  # Timed essay submission with auto-save
├── about.php               # About page with timeline & team
├── contact.php             # Contact form + dealers + FAQ
├── add_to_cart.php         # AJAX + POST cart handler
├── download_book.php       # Secure PDF download for paid orders
├── logout.php              # Session clear & redirect
├── db_config.php           # Database credentials
│
├── css/
│   └── style.css           # Complete design system (~1,700 lines)
│
├── js/
│   └── main.js             # Interactive behaviors & animations
│
├── includes/
│   ├── navbar.php          # Shared premium navbar
│   └── footer.php          # Shared footer with newsletter
│
├── admin/
│   ├── dashboard.php       # Admin overview with stats & orders
│   ├── admin_header.php    # Admin sidebar + topbar
│   └── admin_footer.php    # Admin footer with scripts
│
└── uploads/
    └── stories/            # User-uploaded story files
```

## ⚙️ Setup Instructions

### 1. Requirements
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+
- Apache/Nginx with `mod_rewrite` (optional)

### 2. Database
1. Create a database named `ebook_system`
2. Import the provided `ebook_system.sql` file
3. Update credentials in `db_config.php`

```php
$host     = 'localhost';
$dbname   = 'ebook_system';
$username = 'root';
$password = '';
```

### 3. Deploy
- Upload all files to your web server's public directory (e.g., `htdocs/ebook-system/`)
- Ensure `uploads/` directory is writable: `chmod 755 uploads/`
- Navigate to `http://your-domain/ebook-system/`

### 4. Admin Access
- Register an account, then manually set `role_id = 1` in the `accounts` table
- Access admin panel at `/admin/dashboard.php`

### 5. Email (Optional)
- Configure `checkout.php` with your Gmail SMTP credentials for order confirmation emails
- PHPMailer is included — set username/password in the SMTP block

## 📦 Dependencies (via CDN — no npm/composer needed)

| Library | Version | Purpose |
|---------|---------|---------|
| Bootstrap | 5.3.7 | Responsive grid & utilities |
| Font Awesome | 6.5.0 | Icons throughout |
| AOS | 2.3.4 | Scroll animations |
| Swiper.js | 11 | Book & testimonial sliders |
| Google Fonts | — | Poppins + Inter |

## 🔒 Security Notes

- All user inputs are sanitized with `htmlspecialchars()` and `mysqli_real_escape_string()`
- Sessions are properly managed with `session_start()`
- Downloads are authenticated (checks `paid=1` and matching `user_id`)
- Admin pages verify `role_id = 1` on every load

## 🎯 What's Preserved (Unchanged Logic)

- ✅ PHP session-based cart with quantity, shipping & weight calculation
- ✅ `add_to_cart.php` AJAX endpoint returning JSON
- ✅ Login with role-based redirect (role_id 1 → admin, 2 → user)
- ✅ Order insertion into `orders` table with all original columns
- ✅ PHPMailer order confirmation flow
- ✅ Download locked behind `paid=1` check
- ✅ Competition story upload & essay timer submission
- ✅ Dealers fetched from DB in contact page
- ✅ Admin CRUD operations (mark paid, delete, view)

---

Built with ❤️ for the eBook Hub platform.

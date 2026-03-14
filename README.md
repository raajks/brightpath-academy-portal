# 🎓 BrightPath Academy – Coaching Center Website

A complete, professional coaching center management website built with **PHP + MySQL**.

---

## 📦 Setup Instructions

### Step 1: Import Database

1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click **"New"** to create a new database named: `brightpath_academy`
3. Select the new database, click **"Import"** tab
4. Choose the file: `database.sql`
5. Click **"Go"** to import

### Step 2: Configure (if needed)

Open `config.php` and verify these settings match your environment:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Your MySQL password (empty by default in XAMPP)
define('DB_NAME', 'brightpath_academy');
define('SITE_URL', 'http://localhost/Project/Coaching_Center');
```

### Step 3: Visit the Website

Open your browser and go to:
**http://localhost/Project/Coaching_Center**

---

## 🔐 Default Login Credentials

### Admin Panel
- **URL**: http://localhost/Project/Coaching_Center/admin/login.php
- **Email**: `admin@brightpath.com`
- **Password**: `password`

> ⚠️ **Change the admin password immediately after first login!**

### Student Portal
Students register at: `/register.php`  
Login at: `/login.php`

Default password when admin adds a student manually: `brightpath123`

---

## 📄 Pages / Features

### 🌐 Public Pages
| Page | URL |
|------|-----|
| Homepage | `/index.php` |
| About Us | `/about.php` |
| Courses | `/courses.php` |
| Course Detail | `/course-detail.php?id=1` |
| Admissions | `/admissions.php` |
| Results Lookup | `/results.php` |
| Contact Us | `/contact.php` |

### 🎓 Student Portal
| Page | URL |
|------|-----|
| Student Login | `/login.php` |
| Student Register | `/register.php` |
| Student Dashboard | `/dashboard.php` |
| Logout | `/logout.php` |

### ⚙️ Admin Panel
| Page | URL |
|------|-----|
| Admin Login | `/admin/login.php` |
| Admin Dashboard | `/admin/index.php` |
| Manage Students | `/admin/students.php` |
| Manage Courses | `/admin/courses.php` |
| Manage Enrollments | `/admin/enrollments.php` |
| Manage Results | `/admin/results.php` |
| Announcements | `/admin/announcements.php` |
| View Messages | `/admin/contacts.php` |
| Admin Logout | `/admin/logout.php` |

---

## 🗄️ Database Tables

| Table | Purpose |
|-------|---------|
| `admins` | Admin user accounts |
| `students` | Student accounts & profiles |
| `courses` | Course catalog |
| `enrollments` | Student-course enrollments |
| `results` | Exam marks and grades |
| `toppers` | Hall of fame/topper board |
| `testimonials` | Student testimonials |
| `announcements` | Notices and updates |
| `gallery` | Photo gallery images |
| `contacts` | Contact form submissions |
| `admission_inquiries` | Online admission requests |
| `newsletter` | Newsletter subscribers |

---

## 🎨 Customization

To customize the website for your coaching center:

1. **Site Name**: Change `SITE_NAME` in `config.php`
2. **Contact Info**: Update `SITE_PHONE`, `SITE_EMAIL`, `SITE_ADDRESS`, `SITE_WHATSAPP` in `config.php`
3. **Colors**: Edit CSS variables at the top of `css/style.css`
4. **Courses**: Add/edit courses via Admin Panel → Courses
5. **Announcements**: Post via Admin Panel → Announcements
6. **Toppers/Results**: Add via Admin Panel → Results

---

## 🛡️ Security Features

- All form inputs sanitized & validated
- Passwords hashed with `password_hash()` (bcrypt)
- All database queries use **prepared statements**
- Session-based authentication
- Admin and student sessions are separate

---

## 📁 File Structure

```
Coaching_Center/
├── index.php          ← Homepage
├── about.php          ← About page
├── courses.php        ← Course listing
├── course-detail.php  ← Course detail
├── admissions.php     ← Admission form
├── results.php        ← Results lookup
├── contact.php        ← Contact page
├── login.php          ← Student login
├── register.php       ← Student registration
├── logout.php         ← Student logout
├── dashboard.php      ← Student dashboard
├── config.php         ← DB config & helpers
├── database.sql       ← Database schema
├── process_newsletter.php
├── css/
│   └── style.css      ← Main stylesheet
├── js/
│   └── script.js      ← JavaScript
├── includes/
│   ├── header.php     ← HTML <head>
│   ├── navbar.php     ← Navigation
│   └── footer.php     ← Footer
└── admin/
    ├── login.php
    ├── logout.php
    ├── index.php      ← Dashboard
    ├── students.php   ← Manage students
    ├── courses.php    ← Manage courses
    ├── enrollments.php
    ├── results.php    ← Add results
    ├── announcements.php
    └── contacts.php   ← View messages
```

---

*Built with ❤️ using PHP, MySQL, HTML, CSS, JavaScript*

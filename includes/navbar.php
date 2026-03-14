<?php
// includes/navbar.php
$navBase = defined('SITE_URL') ? SITE_URL : '';
$isAdmin = isset($isAdminPage) && $isAdminPage;
?>
<!-- NAVBAR -->
<nav class="navbar <?= isset($lightNav) && $lightNav ? 'light-nav' : '' ?>" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="<?= $navBase ?>/index.php" class="nav-logo">
            <div class="nav-logo-icon">🎓</div>
            <div>
                <div class="nav-logo-text">Bright<span>Path</span></div>
                <div class="nav-logo-sub">ACADEMY</div>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="nav-links">
            <a href="<?= $navBase ?>/index.php" class="nav-link">Home</a>
            <a href="<?= $navBase ?>/about.php" class="nav-link">About</a>
            <a href="<?= $navBase ?>/courses.php" class="nav-link">Courses</a>
            <a href="<?= $navBase ?>/results.php" class="nav-link">Results</a>
            <a href="<?= $navBase ?>/admissions.php" class="nav-link">Admissions</a>
            <a href="<?= $navBase ?>/contact.php" class="nav-link">Contact</a>
        </div>

        <!-- Desktop Actions -->
        <div class="nav-actions">
            <?php if (isStudentLoggedIn()): ?>
                <a href="<?= $navBase ?>/student/dashboard.php" class="nav-btn-login">
                    👤 My Dashboard
                </a>
            <?php else: ?>
                <a href="<?= $navBase ?>/student/login.php" class="nav-btn-login">Login</a>
            <?php endif; ?>
            <a href="<?= $navBase ?>/admissions.php" class="nav-btn-enroll">✨ Enroll Now</a>
        </div>

        <!-- Hamburger -->
        <div class="hamburger" id="hamburger" aria-label="Toggle Menu" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= $navBase ?>/index.php" class="nav-link">🏠 Home</a>
    <a href="<?= $navBase ?>/about.php" class="nav-link">ℹ About Us</a>
    <a href="<?= $navBase ?>/courses.php" class="nav-link">📚 Courses</a>
    <a href="<?= $navBase ?>/results.php" class="nav-link">🏆 Results</a>
    <a href="<?= $navBase ?>/admissions.php" class="nav-link">📝 Admissions</a>
    <a href="<?= $navBase ?>/contact.php" class="nav-link">📞 Contact</a>
    <div style="border-top:1px solid rgba(255,255,255,0.1);margin-top:12px;padding-top:16px;display:flex;gap:10px;">
        <?php if (isStudentLoggedIn()): ?>
            <a href="<?= $navBase ?>/student/dashboard.php" class="btn btn-outline" style="flex:1;justify-content:center;">My Dashboard</a>
        <?php else: ?>
            <a href="<?= $navBase ?>/student/login.php" class="btn btn-outline" style="flex:1;justify-content:center;">Login</a>
        <?php endif; ?>
        <a href="<?= $navBase ?>/admissions.php" class="btn btn-secondary" style="flex:1;justify-content:center;">Enroll Now</a>
    </div>
</div>

<?php
// includes/footer.php
$footerBase = defined('SITE_URL') ? SITE_URL : '';
$footerSiteName = defined('SITE_NAME') ? SITE_NAME : 'BrightPath Academy';
$whaNum = defined('SITE_WHATSAPP') ? SITE_WHATSAPP : '';
?>
<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer-main">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?= $footerBase ?>/index.php" class="nav-logo" style="margin-bottom:20px;display:inline-flex;">
                    <div class="nav-logo-icon">🎓</div>
                    <div>
                        <div class="nav-logo-text">Bright<span>Path</span></div>
                        <div class="nav-logo-sub">ACADEMY</div>
                    </div>
                </a>
                <p class="footer-about">
                    BrightPath Academy is dedicated to providing world-class coaching for Classes 6-12, JEE & NEET. 
                    We believe every student has the potential to achieve greatness with the right guidance.
                </p>
                <div class="footer-social">
                    <a href="#" title="Facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" title="Instagram" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" title="YouTube" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" title="Twitter" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/<?= $whaNum ?>" title="WhatsApp" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <div class="footer-col-title">Quick Links</div>
                <div class="footer-links">
                    <a href="<?= $footerBase ?>/index.php" class="footer-link">Home</a>
                    <a href="<?= $footerBase ?>/about.php" class="footer-link">About Us</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">Our Courses</a>
                    <a href="<?= $footerBase ?>/results.php" class="footer-link">Results & Toppers</a>
                    <a href="<?= $footerBase ?>/admissions.php" class="footer-link">Admissions</a>
                    <a href="<?= $footerBase ?>/contact.php" class="footer-link">Contact Us</a>
                </div>
            </div>

            <!-- Courses -->
            <div>
                <div class="footer-col-title">Our Courses</div>
                <div class="footer-links">
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">Class 6-8 Foundation</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">Class 9-10 (CBSE/ICSE)</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">Class 11-12 Science</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">JEE Preparation</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">NEET Preparation</a>
                    <a href="<?= $footerBase ?>/courses.php" class="footer-link">Computer Science</a>
                </div>
            </div>

            <!-- Contact & Newsletter -->
            <div>
                <div class="footer-col-title">Get In Touch</div>
                <div class="footer-contact-item">
                    <span class="icon">📍</span>
                    <span><?= defined('SITE_ADDRESS') ? SITE_ADDRESS : '123, Knowledge Park, New Delhi' ?></span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon">📞</span>
                    <span>
                        <a href="tel:<?= defined('SITE_PHONE') ? SITE_PHONE : '' ?>" style="color:rgba(255,255,255,0.65);">
                            <?= defined('SITE_PHONE') ? SITE_PHONE : '' ?>
                        </a>
                    </span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon">✉️</span>
                    <span>
                        <a href="mailto:<?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?>" style="color:rgba(255,255,255,0.65);">
                            <?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?>
                        </a>
                    </span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon">🕐</span>
                    <span>Mon - Sun: 8:00 AM - 9:00 PM</span>
                </div>

                <!-- Newsletter -->
                <div class="footer-newsletter">
                    <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);margin-bottom:4px;">📧 Subscribe for Updates</div>
                    <form action="<?= $footerBase ?>/process_newsletter.php" method="POST" class="footer-newsletter-form">
                        <input type="email" name="email" placeholder="Your email address" class="footer-newsletter-input" required>
                        <button type="submit" class="btn btn-secondary btn-sm">→</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <span>© <?= date('Y') ?> <?= htmlspecialchars($footerSiteName) ?>. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Use</a>
                <a href="<?= $footerBase ?>/admin/login.php">Admin Panel</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">↑</button>

<!-- WhatsApp Float -->
<a href="https://wa.me/<?= $whaNum ?>?text=Hello%2C%20I%20am%20interested%20in%20enrolling%20at%20BrightPath%20Academy." 
   class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
   💬
</a>

<!-- Main JS -->
<script src="<?= $footerBase ?>/js/script.js"></script>
<?= isset($extraJS) ? $extraJS : '' ?>
</body>
</html>

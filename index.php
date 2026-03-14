<?php
require_once 'config.php';
$pageTitle = 'Home';
$pageDesc  = 'BrightPath Academy - Expert coaching for Class 6-12, JEE & NEET. Join 5000+ successful students. Admissions open!';

// Fetch data for homepage
$courses      = getActiveCourses(6);
$testimonials = getFeaturedTestimonials(6);
$toppers      = getToppers(6);
$announcements = getActiveAnnouncements(5);

// Stats
$totalStudents  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students"))['c'] ?? 0;
$totalCourses   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM courses WHERE status='active'"))['c'] ?? 0;
$totalEnrolled  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM enrollments WHERE status='active'"))['c'] ?? 0;

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- ========================
     HERO SECTION
======================== -->
<section class="hero" id="home">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
        <div class="hero-dots"></div>
    </div>

    <div class="hero-content">
        <!-- Left: Text Content -->
        <div class="hero-left">
            <div class="hero-badge">
                <span class="dot"></span>
                <span>🎉 Admissions Open for 2025-26</span>
            </div>

            <h1 class="hero-title">
                Shape Your Future with <span class="highlight">Expert Coaching</span>
            </h1>

            <p class="hero-desc">
                Join BrightPath Academy — where dedicated faculty, proven methods, and 
                a student-first approach have helped 5000+ students achieve their dreams. 
                From Class 6 to competitive exams like JEE & NEET.
            </p>

            <div class="hero-actions">
                <a href="admissions.php" class="btn btn-secondary btn-lg">
                    ✨ Enroll Now — It's Free!
                </a>
                <a href="courses.php" class="btn btn-outline btn-lg">
                    📚 View All Courses
                </a>
            </div>

            <div class="hero-trust">
                <div class="hero-trust-avatars">
                    <div class="hero-trust-avatar">A</div>
                    <div class="hero-trust-avatar">R</div>
                    <div class="hero-trust-avatar">P</div>
                    <div class="hero-trust-avatar">M</div>
                    <div class="hero-trust-avatar">+</div>
                </div>
                <span>⭐ <strong style="color:#fff;">4.9/5</strong> rated by 500+ parents & students</span>
            </div>
        </div>

        <!-- Right: Stats Card -->
        <div class="hero-stats-card animate-on-scroll" style="animation: fadeInRight 0.8s ease 0.3s forwards; opacity:0;">
            <div style="font-size:14px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:20px;">
                📊 Our Track Record
            </div>
            <div class="hero-stats-grid">
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">👨‍🎓</div>
                    <div class="hero-stat-number"><span data-counter="5000">0</span>+</div>
                    <div class="hero-stat-label">Students Enrolled</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">📚</div>
                    <div class="hero-stat-number"><span data-counter="<?= max((int)$totalCourses, 8) ?>">0</span>+</div>
                    <div class="hero-stat-label">Active Courses</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">🏆</div>
                    <div class="hero-stat-number"><span data-counter="95">0</span>%</div>
                    <div class="hero-stat-label">Success Rate</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">⭐</div>
                    <div class="hero-stat-number"><span data-counter="15">0</span>+</div>
                    <div class="hero-stat-label">Years of Excellence</div>
                </div>
            </div>

            <!-- Live announcement in hero card -->
            <?php if (!empty($announcements)): ?>
            <div class="hero-announcement">
                <span class="icon"><?= getAnnouncementTypeIcon($announcements[0]['type']) ?></span>
                <div class="hero-announcement-text">
                    <strong><?= htmlspecialchars($announcements[0]['title']) ?></strong>
                    <span><?= date('d M Y', strtotime($announcements[0]['created_at'])) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ========================
     MARQUEE STRIP
======================== -->
<div class="marquee-strip">
    <div class="marquee-content" id="marqueeContent">
        <div class="marquee-item"><span class="dot">●</span> Class 6-8 Foundation Courses</div>
        <div class="marquee-item"><span class="dot">●</span> Class 9-10 Board Exam Prep</div>
        <div class="marquee-item"><span class="dot">●</span> Class 11-12 Science Coaching</div>
        <div class="marquee-item"><span class="dot">●</span> JEE Main & Advanced</div>
        <div class="marquee-item"><span class="dot">●</span> NEET UG Preparation</div>
        <div class="marquee-item"><span class="dot">●</span> Expert Faculty</div>
        <div class="marquee-item"><span class="dot">●</span> Regular Mock Tests</div>
        <div class="marquee-item"><span class="dot">●</span> Online + Offline Classes</div>
        <div class="marquee-item"><span class="dot">●</span> Admissions Open 2025-26</div>
        <!-- duplicate for seamless loop -->
        <div class="marquee-item"><span class="dot">●</span> Class 6-8 Foundation Courses</div>
        <div class="marquee-item"><span class="dot">●</span> Class 9-10 Board Exam Prep</div>
        <div class="marquee-item"><span class="dot">●</span> Class 11-12 Science Coaching</div>
        <div class="marquee-item"><span class="dot">●</span> JEE Main & Advanced</div>
        <div class="marquee-item"><span class="dot">●</span> NEET UG Preparation</div>
        <div class="marquee-item"><span class="dot">●</span> Expert Faculty</div>
        <div class="marquee-item"><span class="dot">●</span> Regular Mock Tests</div>
        <div class="marquee-item"><span class="dot">●</span> Online + Offline Classes</div>
        <div class="marquee-item"><span class="dot">●</span> Admissions Open 2025-26</div>
    </div>
</div>

<!-- ========================
     WHY CHOOSE US
======================== -->
<section class="section" id="features">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Why BrightPath?</span>
            <h2>Why Students Choose <span class="text-primary">BrightPath Academy</span></h2>
            <p>We combine the best teaching methods with personalized attention to ensure every student reaches their full potential.</p>
            <div class="divider"></div>
        </div>

        <div class="features-grid">
            <div class="feature-card animate-on-scroll delay-1">
                <div class="feature-icon">👨‍🏫</div>
                <h3>Expert Faculty</h3>
                <p>Our teachers include IITians, Ph.D. holders, and experienced educators with 10+ years of teaching excellence.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-2">
                <div class="feature-icon">📊</div>
                <h3>Regular Assessments</h3>
                <p>Weekly tests, monthly mock exams, and detailed performance analysis to track and boost every student's progress.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-3">
                <div class="feature-icon">📖</div>
                <h3>Comprehensive Material</h3>
                <p>Custom-designed study material, chapter notes, formula sheets, and question banks covering every exam pattern.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-4">
                <div class="feature-icon">🎯</div>
                <h3>Doubt Clearing</h3>
                <p>Dedicated doubt-clearing sessions after every class plus WhatsApp/online support — no question goes unanswered.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-1">
                <div class="feature-icon">👨‍👩‍👧</div>
                <h3>Small Batch Sizes</h3>
                <p>Limited seats per batch ensure personalized attention. Every student gets the care and focus they deserve.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-2">
                <div class="feature-icon">🏆</div>
                <h3>Proven Track Record</h3>
                <p>95%+ students score above 80% in boards. Hundreds of JEE and NEET qualifiers every year. Results speak for us!</p>
            </div>
            <div class="feature-card animate-on-scroll delay-3">
                <div class="feature-icon">💻</div>
                <h3>Online Portal</h3>
                <p>Students get access to online tests, study materials, recorded lectures, and performance reports anytime.</p>
            </div>
            <div class="feature-card animate-on-scroll delay-4">
                <div class="feature-icon">📈</div>
                <h3>Parent Updates</h3>
                <p>Regular parent-teacher meetings, SMS/WhatsApp progress updates, and transparent fee structure — full peace of mind.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========================
     STATS SECTION
======================== -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item animate-on-scroll">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-number"><span data-counter="5200">0</span><span>+</span></div>
                <div class="stat-label">Students Enrolled</div>
            </div>
            <div class="stat-item animate-on-scroll delay-1">
                <div class="stat-icon">🏆</div>
                <div class="stat-number"><span data-counter="350">0</span><span>+</span></div>
                <div class="stat-label">JEE / NEET Qualifiers</div>
            </div>
            <div class="stat-item animate-on-scroll delay-2">
                <div class="stat-icon">⭐</div>
                <div class="stat-number"><span data-counter="95">0</span><span>%</span></div>
                <div class="stat-label">Success Rate</div>
            </div>
            <div class="stat-item animate-on-scroll delay-3">
                <div class="stat-icon">📅</div>
                <div class="stat-number"><span data-counter="15">0</span><span>+</span></div>
                <div class="stat-label">Years of Excellence</div>
            </div>
        </div>
    </div>
</section>

<!-- ========================
     COURSES SECTION
======================== -->
<section class="section" id="courses">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Our Programs</span>
            <h2>Explore Our <span class="text-primary">Courses</span></h2>
            <p>Comprehensive coaching programs designed by experts to help every student excel in academics and competitive exams.</p>
            <div class="divider"></div>
        </div>

        <div class="courses-grid">
            <?php
            $catClasses = [
                'Mathematics' => 'math',
                'Physics'     => 'science',
                'Chemistry'   => 'chemistry',
                'Biology'     => 'biology',
                'English'     => 'english',
                'Computer Science' => 'computer',
                'Competitive Exam' => 'competitive',
            ];
            $catIcons = [
                'Mathematics' => '📐',
                'Physics'     => '⚛️',
                'Chemistry'   => '🧪',
                'Biology'     => '🧬',
                'English'     => '📝',
                'Computer Science' => '💻',
                'Competitive Exam' => '🎯',
                'default' => '📚'
            ];
            foreach ($courses as $i => $course):
                $catClass = $catClasses[$course['category']] ?? 'math';
                $icon = $catIcons[$course['category']] ?? $catIcons['default'];
                $features = explode(',', $course['features']);
            ?>
            <div class="course-card animate-on-scroll delay-<?= ($i % 3) + 1 ?>" data-category="<?= htmlspecialchars($course['category']) ?>">
                <div class="course-card-header <?= $catClass ?>">
                    <?php if ($course['is_popular']): ?>
                        <div class="course-popular">🔥 Popular</div>
                    <?php endif; ?>
                    <div class="course-category"><?= $icon ?> <?= htmlspecialchars($course['category']) ?></div>
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <p><?= htmlspecialchars($course['short_desc']) ?></p>
                </div>
                <div class="course-card-body">
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <span class="icon">🎓</span>
                            <span>Class <?= htmlspecialchars($course['class_level']) ?></span>
                        </div>
                        <div class="course-meta-item">
                            <span class="icon">📅</span>
                            <span><?= htmlspecialchars($course['duration']) ?></span>
                        </div>
                        <div class="course-meta-item">
                            <span class="icon">👤</span>
                            <span><?= htmlspecialchars($course['instructor']) ?></span>
                        </div>
                        <div class="course-meta-item">
                            <span class="icon">🪑</span>
                            <span><?= $course['seats'] ?> Seats</span>
                        </div>
                    </div>
                    <div class="course-features">
                        <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                            <div class="course-feature"><?= htmlspecialchars(trim($feature)) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="course-card-footer">
                    <div class="course-fee">
                        <?= formatCurrency($course['fee']) ?>
                        <span>/ year</span>
                    </div>
                    <a href="course-detail.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-sm">View Details →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:40px;" class="animate-on-scroll">
            <a href="courses.php" class="btn btn-outline-primary btn-lg">View All Courses →</a>
        </div>
    </div>
</section>

<!-- ========================
     ABOUT SNIPPET
======================== -->
<section class="section section-about-home" id="about-home">
    <div class="container">
        <div class="about-grid">
            <div class="about-showcase animate-on-scroll">
                <div class="about-showcase-head">
                    <span class="about-showcase-kicker">Campus Snapshot</span>
                    <h3>Where discipline meets smart learning</h3>
                </div>

                <div class="about-showcase-pill-row">
                    <span class="about-showcase-pill">Smart Classrooms</span>
                    <span class="about-showcase-pill">Weekly Doubt Labs</span>
                    <span class="about-showcase-pill">Mentor Tracking</span>
                </div>

                <div class="about-showcase-grid">
                    <article class="about-showcase-item">
                        <div class="about-showcase-icon">🧪</div>
                        <h4>Concept Labs</h4>
                        <p>Practical sessions with visual learning models for deeper understanding.</p>
                    </article>
                    <article class="about-showcase-item">
                        <div class="about-showcase-icon">📝</div>
                        <h4>Mock Test System</h4>
                        <p>Exam-like practice with detailed topic-wise feedback and improvement plans.</p>
                    </article>
                    <article class="about-showcase-item">
                        <div class="about-showcase-icon">📲</div>
                        <h4>Parent Connect</h4>
                        <p>Regular updates and direct mentor communication for transparent progress.</p>
                    </article>
                    <article class="about-showcase-item">
                        <div class="about-showcase-icon">🏅</div>
                        <h4>Rank Focus Batches</h4>
                        <p>Small curated groups for high performers targeting top ranks.</p>
                    </article>
                </div>

                <div class="about-showcase-footer">
                    <span>15+ Years of Excellence</span>
                    <span>5000+ Student Success Stories</span>
                </div>
            </div>

            <div class="about-home-content animate-on-scroll delay-2">
                <span class="badge-label">About Us</span>
                <h2 class="about-home-title">The <span class="text-gradient">BrightPath</span> Story</h2>
                <p class="about-home-lead">
                    Founded in 2010 with a vision to make quality education accessible, BrightPath Academy 
                    has grown from a small classroom to one of the region's most trusted coaching institutions.
                </p>
                <p class="about-home-text">
                    Our team of 30+ expert educators, including IITians and experienced board exam specialists, 
                    has helped over 5000 students achieve top scores, clear JEE, NEET, and build 
                    successful careers in science and technology.
                </p>
                <div class="about-home-points">
                    <div class="about-home-point">🎯 Personalized mentorship for every batch</div>
                    <div class="about-home-point">📊 Weekly analytics and parent progress updates</div>
                    <div class="about-home-point">🧠 Exam-focused strategies for boards and competitive tests</div>
                </div>
                <div class="about-home-stats">
                    <div class="about-home-stat-card">
                        <div class="about-home-stat-value text-primary">30+</div>
                        <div class="about-home-stat-label">Expert Educators</div>
                    </div>
                    <div class="about-home-stat-card">
                        <div class="about-home-stat-value text-secondary">500+</div>
                        <div class="about-home-stat-label">JEE/NEET Qualifiers</div>
                    </div>
                    <div class="about-home-stat-card">
                        <div class="about-home-stat-value" style="color:var(--success);">95%</div>
                        <div class="about-home-stat-label">Success Rate</div>
                    </div>
                    <div class="about-home-stat-card">
                        <div class="about-home-stat-value" style="color:var(--accent);">8+</div>
                        <div class="about-home-stat-label">Active Courses</div>
                    </div>
                </div>
                <a href="about.php" class="btn btn-primary btn-lg">Know More About Us →</a>
            </div>
        </div>
    </div>
</section>

<!-- ========================
     TOPPERS SECTION
======================== -->
<?php if (!empty($toppers)): ?>
<section class="section toppers-section">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Hall of Fame</span>
            <h2>Our <span class="text-primary">Star Achievers</span></h2>
            <p>Celebrating our students who have made us proud with their outstanding achievements.</p>
            <div class="divider"></div>
        </div>

        <div class="toppers-grid">
            <?php foreach ($toppers as $i => $topper): ?>
            <div class="topper-card animate-on-scroll delay-<?= ($i % 4) + 1 ?>">
                <div class="topper-avatar" style="position:relative;">
                    <?= strtoupper(substr($topper['student_name'], 0, 1)) ?>
                    <div class="topper-rank" style="position:absolute;"><?= $i + 1 ?></div>
                </div>
                <div class="topper-name"><?= htmlspecialchars($topper['student_name']) ?></div>
                <div class="topper-achievement"><?= htmlspecialchars($topper['exam_name']) ?></div>
                <div class="topper-score"><?= htmlspecialchars($topper['score']) ?></div>
                <div class="topper-year">Batch <?= htmlspecialchars($topper['year']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:36px;" class="animate-on-scroll">
            <a href="results.php" class="btn btn-outline-primary">See All Toppers & Results →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========================
     TESTIMONIALS
======================== -->
<?php if (!empty($testimonials)): ?>
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label" style="background:rgba(79,70,229,0.15);">Student Stories</span>
            <h2>What Our Students <span style="color:var(--secondary)">Say</span></h2>
            <p style="color:rgba(255,255,255,0.6);">Real stories from real students who transformed their academic journey with BrightPath.</p>
            <div class="divider"></div>
        </div>

        <div class="testimonials-slider">
            <div class="testimonials-track" id="testimonialTrack">
                <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < min(5, (int)$t['rating']); $i++): ?>
                            <span>★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="testimonial-message"><?= htmlspecialchars($t['message']) ?></div>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><?= strtoupper(substr($t['student_name'], 0, 1)) ?></div>
                        <div>
                            <div class="testimonial-author-name"><?= htmlspecialchars($t['student_name']) ?></div>
                            <div class="testimonial-achievement"><?= htmlspecialchars($t['achievement']) ?></div>
                            <div class="testimonial-author-info"><?= htmlspecialchars($t['class_passed']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="slider-controls">
            <button class="slider-btn prev" aria-label="Previous">←</button>
            <div class="slider-dots">
                <?php for ($i = 0; $i < count($testimonials); $i++): ?>
                    <div class="slider-dot <?= $i === 0 ? 'active' : '' ?>"></div>
                <?php endfor; ?>
            </div>
            <button class="slider-btn next" aria-label="Next">→</button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========================
     ANNOUNCEMENTS
======================== -->
<section class="section announcements-section">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Latest Updates</span>
            <h2>News & <span class="text-primary">Announcements</span></h2>
            <p>Stay updated with the latest news, events, and important notices from BrightPath Academy.</p>
            <div class="divider"></div>
        </div>

        <div class="announcements-wrapper">
            <div class="announcements-list">
                <?php foreach ($announcements as $ann): ?>
                <div class="announcement-item <?= $ann['is_important'] ? 'important' : '' ?> animate-on-scroll">
                    <div class="announcement-icon"><?= getAnnouncementTypeIcon($ann['type']) ?></div>
                    <div class="announcement-content">
                        <div class="announcement-meta">
                            <span class="badge <?= getAnnouncementTypeColor($ann['type']) ?>"><?= ucfirst($ann['type']) ?></span>
                            <?php if ($ann['is_important']): ?>
                                <span class="badge badge-important">🔴 Important</span>
                            <?php endif; ?>
                        </div>
                        <div class="announcement-title"><?= htmlspecialchars($ann['title']) ?></div>
                        <div class="announcement-text"><?= htmlspecialchars(substr($ann['content'], 0, 150)) . (strlen($ann['content']) > 150 ? '...' : '') ?></div>
                        <div class="announcement-date">📅 <?= timeAgo($ann['created_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Contact Quick Info -->
            <div>
                <div class="info-contact-card animate-on-scroll delay-2">
                    <h3>📞 Contact Us</h3>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📞</div>
                        <div class="contact-info-text">
                            <strong>Phone</strong>
                            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📱</div>
                        <div class="contact-info-text">
                            <strong>WhatsApp</strong>
                            <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank"><?= SITE_PHONE2 ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">✉️</div>
                        <div class="contact-info-text">
                            <strong>Email</strong>
                            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📍</div>
                        <div class="contact-info-text">
                            <strong>Address</strong>
                            <p><?= SITE_ADDRESS ?></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">🕐</div>
                        <div class="contact-info-text">
                            <strong>Timings</strong>
                            <p>Mon - Sun: 8:00 AM - 9:00 PM</p>
                        </div>
                    </div>
                    <a href="admissions.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                        Apply for Admission →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================
     CTA SECTION
======================== -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>Ready to Start Your Success Journey?</h2>
        <p>Join thousands of students who have transformed their academic performance with BrightPath Academy.</p>
        <div class="cta-actions">
            <a href="admissions.php" class="btn btn-secondary btn-lg">
                🚀 Apply for Admission
            </a>
            <a href="contact.php" class="btn btn-outline btn-lg">
                📞 Talk to a Counselor
            </a>
        </div>
        <p style="margin-top:20px;font-size:13px;color:rgba(255,255,255,0.5);">
            ✓ Free Counseling &nbsp;•&nbsp; ✓ No Hidden Charges &nbsp;•&nbsp; ✓ Limited Seats
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

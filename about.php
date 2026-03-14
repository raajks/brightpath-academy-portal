<?php
require_once 'config.php';
$pageTitle = 'About Us';

$faculty = [
    ['name' => 'Mr. Rajesh Kumar',   'subject' => 'Mathematics',          'qual' => 'M.Sc. Mathematics, IIT Delhi | 12 Yrs Exp.',   'initial' => 'R'],
    ['name' => 'Dr. Amit Verma',     'subject' => 'Physics',              'qual' => 'Ph.D. Physics, BHU | Ex-IIT Faculty | 15 Yrs', 'initial' => 'A'],
    ['name' => 'Mrs. Priya Singh',   'subject' => 'Chemistry',            'qual' => 'M.Sc. Chemistry, Delhi University | 10 Yrs',   'initial' => 'P'],
    ['name' => 'Dr. Sunita Rao',     'subject' => 'Biology (NEET Expert)','qual' => 'MBBS, MD Pursuing | NEET Specialist | 8 Yrs',  'initial' => 'S'],
    ['name' => 'Mr. Arjun Mehta',    'subject' => 'Computer Science',     'qual' => 'B.Tech CSE, IIT Bombay | 8 Yrs Exp.',         'initial' => 'A'],
    ['name' => 'Ms. Kavya Nair',     'subject' => 'English',              'qual' => 'MA English, CTEFL Certified | 7 Yrs Exp.',    'initial' => 'K'],
    ['name' => 'Mr. Deepak Sharma',  'subject' => 'Hindi & Social Sci.',  'qual' => 'MA Hindi, B.Ed. | 10 Yrs Experience',         'initial' => 'D'],
    ['name' => 'Mrs. Anita Tiwari',  'subject' => 'Science (Cl. 6-10)',   'qual' => 'M.Sc. Physics, B.Ed. | 9 Yrs Experience',    'initial' => 'A'],
];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <span class="current">About Us</span>
        </div>
        <h1>About BrightPath Academy</h1>
        <p>15+ years of academic excellence, shaping futures one student at a time.</p>
    </div>
</section>

<!-- Mission & Vision -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="animate-on-scroll">
                <div class="about-image-box" style="min-height:350px;">
                    <div class="about-image-placeholder">🏫</div>
                    <div class="about-badge-float">
                        <div class="about-badge-num">2010</div>
                        <div class="about-badge-text">Est. Year</div>
                    </div>
                </div>
            </div>

            <div class="animate-on-scroll delay-2">
                <span class="badge-label">Our Story</span>
                <h2 style="margin:16px 0 20px;">Building Champions <span class="text-gradient">Since 2010</span></h2>
                <p style="color:var(--text-light);line-height:1.9;margin-bottom:16px;">
                    BrightPath Academy was founded by Mr. Rajesh Kumar with a simple but powerful mission — 
                    to make quality education accessible to every student, regardless of background.
                </p>
                <p style="color:var(--text-light);line-height:1.9;margin-bottom:16px;">
                    Starting with just 20 students in a small room, today BrightPath stands as one of the region's 
                    most trusted coaching institutions with 5000+ students, 30+ expert faculty, and a track record 
                    of producing 350+ JEE and NEET qualifiers.
                </p>
                <p style="color:var(--text-light);line-height:1.9;margin-bottom:28px;">
                    Our approach is simple: strong fundamentals + regular practice + personalized attention = 
                    unstoppable success. We don't just teach; we mentor, motivate, and build champions.
                </p>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <span style="color:var(--primary);font-size:18px;margin-top:2px;">✓</span>
                        <span>CBSE, ICSE, and State Board curriculum expertise</span>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <span style="color:var(--primary);font-size:18px;margin-top:2px;">✓</span>
                        <span>JEE Main, JEE Advanced, and NEET specialized preparation</span>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <span style="color:var(--primary);font-size:18px;margin-top:2px;">✓</span>
                        <span>Digital learning tools and online student portal</span>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <span style="color:var(--primary);font-size:18px;margin-top:2px;">✓</span>
                        <span>Regular parent-teacher communication and transparency</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission / Vision Cards -->
<section class="section-sm" style="background:var(--light);">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">
            <div class="card animate-on-scroll" style="border-left:4px solid var(--primary);">
                <div style="font-size:36px;margin-bottom:12px;">🎯</div>
                <h3 style="margin-bottom:12px;">Our Mission</h3>
                <p style="color:var(--text-light);line-height:1.8;">
                    To provide exceptional coaching that helps every student unlock their true potential, 
                    develop life skills, and achieve academic excellence through innovative teaching methods and 
                    dedicated personal attention.
                </p>
            </div>
            <div class="card animate-on-scroll delay-2" style="border-left:4px solid var(--secondary);">
                <div style="font-size:36px;margin-bottom:12px;">🌟</div>
                <h3 style="margin-bottom:12px;">Our Vision</h3>
                <p style="color:var(--text-light);line-height:1.8;">
                    To be the most trusted and impactful coaching institution in the region, producing not just 
                    academically excellent students but confident, curious, and compassionate individuals 
                    who lead positive change.
                </p>
            </div>
            <div class="card animate-on-scroll delay-3" style="border-left:4px solid var(--success);">
                <div style="font-size:36px;margin-bottom:12px;">💡</div>
                <h3 style="margin-bottom:12px;">Our Values</h3>
                <ul style="color:var(--text-light);line-height:2;padding-left:0;">
                    <li>✓ Excellence in Teaching</li>
                    <li>✓ Student-First Approach</li>
                    <li>✓ Integrity & Transparency</li>
                    <li>✓ Continuous Innovation</li>
                    <li>✓ Inclusive Education</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Faculty Section -->
<section class="section">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Meet Our Team</span>
            <h2>Expert <span class="text-primary">Faculty</span></h2>
            <p>Our team of dedicated educators brings decades of teaching experience and deep subject expertise.</p>
            <div class="divider"></div>
        </div>

        <div class="faculty-grid">
            <?php foreach ($faculty as $i => $f): ?>
            <div class="faculty-card animate-on-scroll delay-<?= ($i % 4) + 1 ?>">
                <div class="faculty-avatar"><?= $f['initial'] ?></div>
                <div class="faculty-name"><?= htmlspecialchars($f['name']) ?></div>
                <div class="faculty-subject"><?= htmlspecialchars($f['subject']) ?></div>
                <div class="faculty-qual"><?= htmlspecialchars($f['qual']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Infrastructure -->
<section class="section" style="background:var(--light);">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Our Facilities</span>
            <h2>World-Class <span class="text-primary">Infrastructure</span></h2>
            <div class="divider"></div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;">
            <?php
            $facilities = [
                ['icon'=>'🏛️','title'=>'Modern Classrooms','desc'=>'Air-conditioned, well-lit classrooms with smart boards'],
                ['icon'=>'📚','title'=>'Library & Reading Room','desc'=>'2000+ books, journals, and competitive exam materials'],
                ['icon'=>'💻','title'=>'Computer Lab','desc'=>'30+ computers for practical sessions and online tests'],
                ['icon'=>'🔬','title'=>'Science Lab','desc'=>'Fully equipped physics, chemistry & biology labs'],
                ['icon'=>'🎯','title'=>'Mock Test Center','desc'=>'Dedicated hall for regular assessments and mock exams'],
                ['icon'=>'🌐','title'=>'Online Portal','desc'=>'24/7 access to recorded lectures, tests, and materials'],
            ];
            foreach ($facilities as $i => $fac): ?>
            <div class="card animate-on-scroll delay-<?= ($i % 3) + 1 ?>" style="text-align:center;">
                <div style="font-size:42px;margin-bottom:14px;"><?= $fac['icon'] ?></div>
                <h4 style="margin-bottom:8px;"><?= $fac['title'] ?></h4>
                <p style="font-size:13.5px;color:var(--text-light);"><?= $fac['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>Join the BrightPath Family</h2>
        <p>Give your child the best start to their academic journey. Seats are limited!</p>
        <div class="cta-actions">
            <a href="admissions.php" class="btn btn-secondary btn-lg">Apply for Admission</a>
            <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

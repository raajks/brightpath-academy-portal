<?php
require_once 'config.php';
$pageTitle = 'Our Courses';

$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

if ($category) {
    $result = mysqli_query($conn, "SELECT * FROM courses WHERE status='active' AND category LIKE '%$category%' ORDER BY is_popular DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM courses WHERE status='active' ORDER BY is_popular DESC, category ASC");
}
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get categories
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM courses WHERE status='active' ORDER BY category");
$categories = mysqli_fetch_all($catResult, MYSQLI_ASSOC);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <span class="current">Courses</span>
        </div>
        <h1>Our Courses & Programs</h1>
        <p>Expert-designed courses for every level — from Class 6 foundation to competitive exam preparation.</p>
    </div>
</section>

<!-- Courses Section -->
<section class="section">
    <div class="container">
        <!-- Filter Buttons -->
        <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:42px;" class="animate-on-scroll">
            <a href="courses.php" class="filter-btn btn btn-<?= !$category ? 'primary' : 'outline-primary' ?> btn-sm" data-filter="all">
                All Courses
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="courses.php?category=<?= urlencode($cat['category']) ?>" 
               class="filter-btn btn btn-<?= $category === $cat['category'] ? 'primary' : 'outline-primary' ?> btn-sm"
               data-filter="<?= htmlspecialchars($cat['category']) ?>">
                <?= htmlspecialchars($cat['category']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($courses)): ?>
            <div style="text-align:center;padding:60px;" class="animate-on-scroll">
                <div style="font-size:64px;margin-bottom:16px;">📚</div>
                <h3>No courses found for this category</h3>
                <a href="courses.php" class="btn btn-primary" style="margin-top:20px;">View All Courses</a>
            </div>
        <?php else: ?>
        <div class="courses-grid">
            <?php
            $catClasses = ['Mathematics'=>'math','Physics'=>'science','Chemistry'=>'chemistry','Biology'=>'biology','English'=>'english','Computer Science'=>'computer','Competitive Exam'=>'competitive'];
            $catIcons   = ['Mathematics'=>'📐','Physics'=>'⚛️','Chemistry'=>'🧪','Biology'=>'🧬','English'=>'📝','Computer Science'=>'💻','Competitive Exam'=>'🎯','default'=>'📚'];

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
                        <div class="course-meta-item"><span>🎓</span><span>Class <?= htmlspecialchars($course['class_level']) ?></span></div>
                        <div class="course-meta-item"><span>📅</span><span><?= htmlspecialchars($course['duration']) ?></span></div>
                        <div class="course-meta-item"><span>🕐</span><span><?= htmlspecialchars($course['batch_timing']) ?></span></div>
                        <div class="course-meta-item"><span>🪑</span><span><?= $course['seats'] ?> Seats</span></div>
                    </div>
                    <div class="course-features">
                        <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                            <div class="course-feature"><?= htmlspecialchars(trim($feature)) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="course-card-footer">
                    <div>
                        <div class="course-fee"><?= formatCurrency($course['fee']) ?><span>/yr</span></div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">👤 <?= htmlspecialchars($course['instructor']) ?></div>
                    </div>
                    <a href="course-detail.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-sm">Details →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Inquiry bar -->
        <div style="background:var(--primary-pale);border:1px solid var(--primary-light);border-radius:var(--radius-md);padding:24px 32px;margin-top:48px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;" class="animate-on-scroll">
            <div>
                <h3 style="margin-bottom:6px;color:var(--primary);">🤔 Not sure which course to choose?</h3>
                <p style="color:var(--text-light);font-size:14.5px;">Talk to our counselors — free consultation, no pressure. We'll help you pick the best course for your goals.</p>
            </div>
            <a href="contact.php" class="btn btn-primary">Get Free Counseling →</a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>Start Your Journey Today</h2>
        <p>Limited seats per batch. Don't miss your spot!</p>
        <div class="cta-actions">
            <a href="admissions.php" class="btn btn-secondary btn-lg">Apply Now — Free</a>
            <a href="tel:<?= SITE_PHONE ?>">  <div class="btn btn-outline btn-lg">📞 Call Us</div></a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = getCourseById($id);

if (!$course) {
    header("Location: courses.php");
    exit();
}

$pageTitle = htmlspecialchars($course['title']);
$features  = explode(',', $course['features']);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <a href="courses.php">Courses</a>
            <span class="sep">›</span>
            <span class="current"><?= htmlspecialchars($course['title']) ?></span>
        </div>
        <div style="display:inline-block;background:rgba(255,255,255,0.1);padding:4px 14px;border-radius:20px;font-size:13px;margin-bottom:14px;color:rgba(255,255,255,0.8);">
            <?= htmlspecialchars($course['category']) ?> • Class <?= htmlspecialchars($course['class_level']) ?>
        </div>
        <h1><?= htmlspecialchars($course['title']) ?></h1>
        <p><?= htmlspecialchars($course['short_desc']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:40px;align-items:start;">

            <!-- Left: Course Details -->
            <div>
                <div class="card animate-on-scroll">
                    <h2 style="margin-bottom:18px;">📋 Course Overview</h2>
                    <p style="color:var(--text-light);line-height:1.9;margin-bottom:0;">
                        <?= nl2br(htmlspecialchars($course['description'])) ?>
                    </p>
                </div>

                <div class="card animate-on-scroll" style="margin-top:24px;">
                    <h2 style="margin-bottom:18px;">✅ What You'll Get</h2>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <?php foreach ($features as $feature): ?>
                        <div style="display:flex;align-items:center;gap:10px;padding:14px;background:var(--primary-pale);border-radius:var(--radius-sm);">
                            <span style="color:var(--success);font-size:18px;font-weight:700;">✓</span>
                            <span style="font-size:14.5px;font-weight:500;"><?= htmlspecialchars(trim($feature)) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($course['syllabus']): ?>
                <div class="card animate-on-scroll" style="margin-top:24px;">
                    <h2 style="margin-bottom:18px;">📚 Syllabus Covered</h2>
                    <div style="color:var(--text-light);line-height:1.9;">
                        <?= nl2br(htmlspecialchars($course['syllabus'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Instructor -->
                <div class="card animate-on-scroll" style="margin-top:24px;">
                    <h2 style="margin-bottom:18px;">👨‍🏫 Your Instructor</h2>
                    <div style="display:flex;align-items:center;gap:20px;">
                        <div style="width:70px;height:70px;background:var(--gradient);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;color:var(--white);font-weight:800;font-family:var(--font-heading);flex-shrink:0;">
                            <?= strtoupper(substr($course['instructor'], 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;margin-bottom:4px;"><?= htmlspecialchars($course['instructor']) ?></div>
                            <div style="color:var(--primary);font-size:14px;font-weight:600;margin-bottom:4px;"><?= htmlspecialchars($course['category']) ?> Expert</div>
                            <div style="color:var(--text-muted);font-size:14px;"><?= htmlspecialchars($course['instructor_qualification']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Enroll Card -->
            <div style="position:sticky;top:100px;">
                <div class="card animate-on-scroll" style="border:2px solid var(--primary-light);overflow:hidden;">
                    <div style="background:var(--gradient);margin:-32px -32px 28px;padding:24px 28px;text-align:center;">
                        <div style="font-size:36px;font-weight:900;color:var(--white);font-family:var(--font-heading);">
                            <?= formatCurrency($course['fee']) ?>
                        </div>
                        <div style="color:rgba(255,255,255,0.8);font-size:14px;">per year (all-inclusive)</div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted);font-size:14px;">📅 Duration</span>
                            <span style="font-weight:600;font-size:14px;"><?= htmlspecialchars($course['duration']) ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted);font-size:14px;">🎓 Level</span>
                            <span style="font-weight:600;font-size:14px;">Class <?= htmlspecialchars($course['class_level']) ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted);font-size:14px;">🕐 Timing</span>
                            <span style="font-weight:600;font-size:13px;text-align:right;max-width:160px;"><?= htmlspecialchars($course['batch_timing']) ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted);font-size:14px;">🪑 Seats</span>
                            <span style="font-weight:600;font-size:14px;"><?= $course['seats'] ?> students/batch</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--text-muted);font-size:14px;">👤 Faculty</span>
                            <span style="font-weight:600;font-size:14px;"><?= htmlspecialchars($course['instructor']) ?></span>
                        </div>
                    </div>

                    <a href="admissions.php?course=<?= $course['id'] ?>" class="btn btn-primary" style="width:100%;justify-content:center;font-size:16px;padding:14px;">
                        🚀 Apply for Admission
                    </a>
                    <div style="text-align:center;margin-top:14px;">
                        <a href="contact.php" style="font-size:13.5px;color:var(--text-muted);">📞 Ask a question first</a>
                    </div>
                </div>

                <!-- Quick Contact -->
                <div style="background:var(--primary-pale);border:1px solid var(--primary-light);border-radius:var(--radius);padding:20px;margin-top:20px;text-align:center;" class="animate-on-scroll">
                    <div style="font-size:20px;margin-bottom:8px;">📞</div>
                    <div style="font-weight:700;color:var(--dark);margin-bottom:4px;">Have questions?</div>
                    <a href="tel:<?= SITE_PHONE ?>" style="font-size:16px;font-weight:700;color:var(--primary);"><?= SITE_PHONE ?></a>
                    <div style="font-size:12.5px;color:var(--text-muted);margin-top:4px;">Mon-Sun: 8AM - 9PM</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

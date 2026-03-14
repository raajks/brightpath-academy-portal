<?php
require_once '../config.php';
requireStudentLogin();

$studentId  = (int)$_SESSION['student_id'];
$student    = getStudentById($studentId);
$pageTitle  = 'Overview';
$activePage = 'dashboard';

// Count enrolled courses
$totalCourses = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as c FROM enrollments WHERE student_id=$studentId"))['c'] ?? 0);

// Count tests appeared
$totalTests = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as c FROM results WHERE student_id=$studentId"))['c'] ?? 0);

// Best score
$bestRow = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT MAX(percentage) as best FROM results WHERE student_id=$studentId AND total_marks>0"));
$bestScore = round($bestRow['best'] ?? 0, 1);

// Average percentage
$avgRow   = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT AVG(marks_obtained/total_marks*100) as avg FROM results WHERE student_id=$studentId AND total_marks>0"));
$avgScore = round($avgRow['avg'] ?? 0, 1);

// Recent results (last 3)
$recentResultsQ = mysqli_query($conn,
    "SELECT * FROM results WHERE student_id=$studentId ORDER BY exam_date DESC LIMIT 3");
$recentResults = mysqli_fetch_all($recentResultsQ, MYSQLI_ASSOC);

// Recent announcements (last 4)
$recentAnnouncements = getActiveAnnouncements(4);

// Recent enrolled courses
$recentCoursesQ = mysqli_query($conn,
    "SELECT e.*, c.title, c.category FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=$studentId ORDER BY e.created_at DESC LIMIT 3");
$recentCourses = mysqli_fetch_all($recentCoursesQ, MYSQLI_ASSOC);

$welcome = isset($_GET['welcome']);

$typeColors = [
    'notice'   => '#4f46e5',
    'event'    => '#10b981',
    'result'   => '#f59e0b',
    'holiday'  => '#ec4899',
    'exam'     => '#ef4444',
    'admission'=> '#14b8a6'
];

$extraStyle = '.dash-grid { grid-template-columns: 1fr 1fr; } @media(max-width:760px){.dash-grid{grid-template-columns:1fr;}}';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
<main class="sp-content">

    <!-- Topbar -->
    <div class="sp-topbar">
        <div class="sp-topbar-left">
            <button class="sp-hamburger" id="spBurger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <div>
                <h1 class="sp-page-title">
                    Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?>,
                    <?= htmlspecialchars(explode(' ', $student['name'])[0]) ?>! 👋
                </h1>
                <p class="sp-page-sub"><?= date('l, d F Y') ?></p>
            </div>
        </div>
        <div class="sp-topbar-right">
            <a href="logout.php" style="font-size:13px;color:#ef4444;font-weight:600;text-decoration:none;">🚪 Logout</a>
            <div class="sp-topbar-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        </div>
    </div>

    <?php if ($welcome): ?>
    <div class="sp-alert success">🎉 Welcome to <?= SITE_NAME ?>! Your account was created successfully. Start exploring courses!</div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="sp-stats">
        <div class="sp-stat">
            <div class="sp-stat-icon blue">📚</div>
            <div><div class="sp-stat-val"><?= $totalCourses ?></div><div class="sp-stat-label">Enrolled Courses</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon green">📋</div>
            <div><div class="sp-stat-val"><?= $totalTests ?></div><div class="sp-stat-label">Tests Appeared</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon amber">📈</div>
            <div><div class="sp-stat-val"><?= $avgScore ?>%</div><div class="sp-stat-label">Avg. Score</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon purple">🏆</div>
            <div><div class="sp-stat-val"><?= getGrade($avgScore) ?></div><div class="sp-stat-label">Overall Grade</div></div>
        </div>
    </div>

    <!-- Two-col grid -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="dash-grid">

        <!-- Recent Results -->
        <div class="sp-card">
            <div class="sp-card-header">
                <span class="sp-card-title">📋 Recent Results</span>
                <a href="results.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View All →</a>
            </div>
            <div class="sp-card-body" style="padding:0;">
                <?php if (empty($recentResults)): ?>
                <div class="sp-empty"><div class="ei">📋</div><p>No results yet.</p></div>
                <?php else: ?>
                <?php foreach ($recentResults as $r):
                    $pct = round($r['percentage'], 1);
                    $gc  = $pct >= 80 ? 'gp-a' : ($pct >= 60 ? 'gp-b' : ($pct >= 40 ? 'gp-c' : ($pct >= 35 ? 'gp-d' : 'gp-f')));
                ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13.5px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($r['exam_name']) ?></div>
                        <div style="font-size:12px;color:#64748b;"><?= htmlspecialchars($r['subject']) ?> &bull; <?= formatDate($r['exam_date']) ?></div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <span class="gp <?= $gc ?>"><?= getGrade($r['percentage']) ?></span>
                        <div style="font-size:12px;color:#64748b;margin-top:3px;"><?= $r['marks_obtained'] ?>/<?= $r['total_marks'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div style="padding:12px 20px;text-align:center;">
                    <a href="results.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View all <?= $totalTests ?> results →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="sp-card">
            <div class="sp-card-header">
                <span class="sp-card-title">📢 Announcements</span>
                <a href="announcements.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View All →</a>
            </div>
            <div class="sp-card-body" style="padding:0;">
                <?php if (empty($recentAnnouncements)): ?>
                <div class="sp-empty"><div class="ei">📢</div><p>No announcements.</p></div>
                <?php else: ?>
                <?php foreach ($recentAnnouncements as $ann):
                    $color = $typeColors[$ann['type']] ?? '#4f46e5';
                ?>
                <div style="display:flex;align-items:flex-start;gap:11px;padding:12px 20px;border-bottom:1px solid #f1f5f9;">
                    <div style="width:34px;height:34px;border-radius:8px;background:<?= $color ?>18;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;"><?= getAnnouncementTypeIcon($ann['type']) ?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            <span style="font-size:13.5px;font-weight:700;color:#0f172a;"><?= htmlspecialchars($ann['title']) ?></span>
                            <?php if ($ann['is_important']): ?><span style="background:#fee2e2;color:#dc2626;font-size:10px;font-weight:700;padding:1px 6px;border-radius:20px;">HOT</span><?php endif; ?>
                        </div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars(substr($ann['content'],0,60)) ?>...</div>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;white-space:nowrap;flex-shrink:0;"><?= timeAgo($ann['created_at']) ?></div>
                </div>
                <?php endforeach; ?>
                <div style="padding:12px 20px;text-align:center;">
                    <a href="announcements.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View all announcements →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="sp-card" style="margin-top:4px;">
        <div class="sp-card-header"><span class="sp-card-title">⚡ Quick Actions</span></div>
        <div class="sp-card-body">
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <a href="courses.php"       class="btn btn-primary btn-sm"  style="text-decoration:none;">📚 My Courses</a>
                <a href="results.php"       class="btn btn-outline-primary btn-sm"  style="text-decoration:none;">📈 View Results</a>
                <a href="announcements.php" class="btn btn-outline-primary btn-sm"  style="text-decoration:none;">📢 Announcements</a>
                <a href="profile.php"       class="btn btn-outline-primary btn-sm"  style="text-decoration:none;">✏️ Edit Profile</a>
                <a href="../courses.php"    class="btn btn-outline-primary btn-sm"  style="text-decoration:none;">→ Browse Courses</a>
            </div>
        </div>
    </div>

    <!-- My Courses (recent) -->
    <?php if (!empty($recentCourses)): ?>
    <div class="sp-card" style="margin-top:4px;">
        <div class="sp-card-header">
            <span class="sp-card-title">📚 My Enrolled Courses</span>
            <a href="courses.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div class="sp-card-body" style="padding:0;">
            <?php foreach ($recentCourses as $ec): ?>
            <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f1f5f9;">
                <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">📚</div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:14px;color:#0f172a;"><?= htmlspecialchars($ec['title']) ?></div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($ec['category']) ?></div>
                </div>
                <span class="sp-pill <?= $ec['status']==='active' ? 'green' : 'gray' ?>"><?= ucfirst($ec['status']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</main>
<?php require_once 'includes/footer.php'; ?>

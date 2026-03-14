<?php
require_once '../config.php';
requireStudentLogin();

$studentId  = (int)$_SESSION['student_id'];
$student    = getStudentById($studentId);
$pageTitle  = 'My Courses';
$activePage = 'courses';

// All enrolled courses with full course data
$enrolledQ = mysqli_query($conn,
    "SELECT e.*, c.title, c.category, c.description, c.short_desc, c.batch_timing,
            c.instructor, c.instructor_qualification, c.fee, c.duration,
            c.class_level, c.image, c.features, c.is_popular
     FROM enrollments e
     JOIN courses c ON e.course_id = c.id
     WHERE e.student_id = $studentId
     ORDER BY e.created_at DESC");
$enrolledCourses = mysqli_fetch_all($enrolledQ, MYSQLI_ASSOC);

// For each course: get result summary (last 5 results)
$courseResults = [];
foreach ($enrolledCourses as $ec) {
    $cid = (int)$ec['course_id'];
    // Results matching the course title (approximate; no direct FK between results and courses)
    $rq = mysqli_query($conn, "SELECT * FROM results WHERE student_id=$studentId ORDER BY exam_date DESC LIMIT 5");
    $courseResults[$cid] = mysqli_fetch_all($rq, MYSQLI_ASSOC);
}

// Stats
$totalEnrolled = count($enrolledCourses);
$activeCount   = 0;
$feePaidCount  = 0;
$totalFeePaid  = 0;
foreach ($enrolledCourses as $ec) {
    if ($ec['status'] === 'active') $activeCount++;
    if ($ec['payment_status'] === 'paid') { $feePaidCount++; $totalFeePaid += $ec['amount_paid']; }
    elseif ($ec['payment_status'] === 'partial') { $totalFeePaid += $ec['amount_paid']; }
}

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
                <h1 class="sp-page-title">My Courses</h1>
                <p class="sp-page-sub">All your enrolled courses in one place</p>
            </div>
        </div>
        <div class="sp-topbar-right">
            <a href="../courses.php" class="btn btn-primary btn-sm" style="text-decoration:none;">➕ Browse More</a>
            <div class="sp-topbar-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        </div>
    </div>

    <!-- Stats row -->
    <div class="sp-stats" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
        <div class="sp-stat">
            <div class="sp-stat-icon blue">📚</div>
            <div><div class="sp-stat-val"><?= $totalEnrolled ?></div><div class="sp-stat-label">Total Enrolled</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon green">✅</div>
            <div><div class="sp-stat-val"><?= $activeCount ?></div><div class="sp-stat-label">Active Courses</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon amber">💰</div>
            <div><div class="sp-stat-val"><?= formatCurrency($totalFeePaid) ?></div><div class="sp-stat-label">Fees Paid</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon purple">🏫</div>
            <div><div class="sp-stat-val"><?= $feePaidCount ?></div><div class="sp-stat-label">Fully Cleared</div></div>
        </div>
    </div>

    <?php if (empty($enrolledCourses)): ?>
    <!-- Empty state -->
    <div class="sp-card">
        <div class="sp-card-body">
            <div class="sp-empty">
                <div class="ei">📚</div>
                <h4>No Courses Yet</h4>
                <p>You haven't enrolled in any course yet. Explore our courses and start learning!</p>
                <a href="../courses.php" class="btn btn-primary" style="text-decoration:none;">Browse Courses</a>
            </div>
        </div>
    </div>

    <?php else: ?>

    <!-- Course cards -->
    <?php foreach ($enrolledCourses as $ec):
        $icons = ['Mathematics'=>'📐','Physics'=>'⚛️','Chemistry'=>'🧪','Biology'=>'🧬',
                  'English'=>'📖','Computer'=>'💻','Hindi'=>'🔤','Science'=>'🔬',
                  'History'=>'📜','Geography'=>'🌍','Economics'=>'📊'];
        $icon = '📚';
        foreach ($icons as $k => $v) {
            if (stripos($ec['title'], $k) !== false) { $icon = $v; break; }
        }
        $payClass = ['paid'=>'green','partial'=>'amber','pending'=>'red'][$ec['payment_status']] ?? 'red';
        $payLabel = ['paid'=>'Fee Paid','partial'=>'Partial','pending'=>'Due'][$ec['payment_status']] ?? 'Due';
        $payIcon  = ['paid'=>'✅','partial'=>'⚠️','pending'=>'⏳'][$ec['payment_status']] ?? '⏳';
        $statusClass = $ec['status'] === 'active' ? 'green' : ($ec['status'] === 'completed' ? 'blue' : 'gray');
    ?>
    <div class="sp-card">
        <div class="sp-card-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:22px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $icon ?></div>
                <div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;"><?= htmlspecialchars($ec['title']) ?></div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:4px;flex-wrap:wrap;">
                        <span class="sp-pill purple"><?= htmlspecialchars($ec['category']) ?></span>
                        <?php if ($ec['is_popular']): ?><span class="sp-pill amber">🔥 Popular</span><?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <span class="sp-pill <?= $payClass ?>"><?= $payIcon ?> <?= $payLabel ?></span>
                <span class="sp-pill <?= $statusClass ?>"><?= ucfirst($ec['status']) ?></span>
            </div>
        </div>

        <div class="sp-card-body">
            <!-- 2-col info grid -->
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:18px;">
                <?php if ($ec['instructor']): ?>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Instructor</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">👨‍🏫 <?= htmlspecialchars($ec['instructor']) ?></div>
                    <?php if ($ec['instructor_qualification']): ?>
                    <div style="font-size:12px;color:#64748b;"><?= htmlspecialchars($ec['instructor_qualification']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($ec['batch_timing']): ?>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Batch Timing</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">⏰ <?= htmlspecialchars($ec['batch_timing']) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($ec['duration']): ?>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Duration</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">📅 <?= htmlspecialchars($ec['duration']) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($ec['class_level']): ?>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Class Level</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">🎓 <?= htmlspecialchars($ec['class_level']) ?></div>
                </div>
                <?php endif; ?>

                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Enrolled On</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">📅 <?= formatDate($ec['enrollment_date']) ?></div>
                </div>

                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Course Fee</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">💰 <?= formatCurrency($ec['fee']) ?></div>
                </div>
            </div>

            <!-- Fee breakdown -->
            <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:24px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Course Fee</div>
                    <div style="font-size:18px;font-weight:800;color:#0f172a;"><?= formatCurrency($ec['fee']) ?></div>
                </div>
                <div>
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Amount Paid</div>
                    <div style="font-size:18px;font-weight:800;color:#059669;"><?= formatCurrency($ec['amount_paid']) ?></div>
                </div>
                <?php if ($ec['fee_due'] > 0): ?>
                <div>
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Fee Due</div>
                    <div style="font-size:18px;font-weight:800;color:#ef4444;"><?= formatCurrency($ec['fee_due']) ?></div>
                </div>
                <?php endif; ?>
                <div>
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Payment</div>
                    <div style="margin-top:2px;">
                        <?php if ($ec['payment_status'] === 'paid'): ?>
                            <span class="sp-pill green">✅ Fully Paid</span>
                        <?php elseif ($ec['payment_status'] === 'partial'): ?>
                            <span class="sp-pill amber">⚠️ Partially Paid</span>
                        <?php else: ?>
                            <span class="sp-pill red">⏳ Payment Pending</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($ec['short_desc'] || $ec['description']): ?>
            <div style="font-size:13.5px;color:#475569;line-height:1.6;margin-bottom:14px;">
                <?= htmlspecialchars($ec['short_desc'] ?: substr($ec['description'],0,200)) ?>...
            </div>
            <?php endif; ?>

            <?php if ($ec['notes']): ?>
            <div style="background:#eff6ff;border-left:3px solid #4f46e5;padding:10px 14px;border-radius:0 8px 8px 0;font-size:13px;color:#1e40af;">
                📝 <strong>Note:</strong> <?= htmlspecialchars($ec['notes']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Browse more CTA -->
    <div class="sp-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;">
        <div class="sp-card-body" style="text-align:center;padding:28px;">
            <div style="font-size:28px;margin-bottom:10px;">🎓</div>
            <h3 style="color:#fff;margin:0 0 8px;font-size:18px;">Explore More Courses</h3>
            <p style="color:rgba(255,255,255,.75);font-size:14px;margin:0 0 16px;">Expand your knowledge. View all available courses and enroll today.</p>
            <a href="../courses.php" class="btn" style="background:#fff;color:#4f46e5;font-weight:700;text-decoration:none;padding:10px 24px;border-radius:8px;display:inline-block;">Browse All Courses →</a>
        </div>
    </div>

    <?php endif; ?>

</main>
<?php require_once 'includes/footer.php'; ?>

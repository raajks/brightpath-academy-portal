<?php
require_once 'config.php';
redirect(SITE_URL . '/student/dashboard.php');


$studentId = $_SESSION['student_id'];
$student   = getStudentById($studentId);
$pageTitle = 'My Dashboard';

// Enrolled courses
$enrolledQuery = mysqli_query($conn, "SELECT e.*, c.title, c.category, c.batch_timing, c.instructor, c.fee, c.image 
    FROM enrollments e JOIN courses c ON e.course_id=c.id 
    WHERE e.student_id=$studentId ORDER BY e.created_at DESC");
$enrolledCourses = [];
while ($row = mysqli_fetch_assoc($enrolledQuery)) $enrolledCourses[] = $row;

// Results
$resultsQuery = mysqli_query($conn, "SELECT * FROM results WHERE student_id=$studentId ORDER BY exam_date DESC LIMIT 20");
$results = [];
while ($row = mysqli_fetch_assoc($resultsQuery)) $results[] = $row;

// Avg percentage
$avgQ = mysqli_query($conn, "SELECT AVG(marks_obtained/total_marks*100) as avg FROM results WHERE student_id=$studentId AND total_marks>0");
$avgRow   = mysqli_fetch_assoc($avgQ);
$avgScore = round($avgRow['avg'] ?? 0, 1);

// Announcements
$announcements = getActiveAnnouncements(5);

$welcome = isset($_GET['welcome']);
?>
<?php include 'includes/header.php'; ?>
<title><?= $pageTitle ?> | <?= SITE_NAME ?></title>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>
<?php include 'includes/navbar.php'; ?>

<div class="dashboard-layout" style="margin-top:80px;">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                <?= strtoupper(substr($student['name'], 0, 2)) ?>
            </div>
            <div class="sidebar-info">
                <h3><?= htmlspecialchars($student['name']) ?></h3>
                <p class="student-id-badge"><?= htmlspecialchars($student['student_id']) ?></p>
                <p><?= htmlspecialchars($student['class_level']) ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="#overview" class="sidebar-link active"><span>📊</span> Overview</a>
            <a href="#my-courses" class="sidebar-link"><span>📚</span> My Courses</a>
            <a href="#my-results" class="sidebar-link"><span>📋</span> My Results</a>
            <a href="#announcements" class="sidebar-link"><span>📢</span> Announcements</a>
            <a href="#profile" class="sidebar-link"><span>👤</span> My Profile</a>
        </nav>

        <div class="sidebar-footer">
            <a href="courses.php" class="btn btn-primary" style="width:100%;justify-content:center;font-size:14px;">
                ➕ Enroll New Course
            </a>
            <a href="logout.php" class="btn btn-ghost" style="width:100%;justify-content:center;color:#ef4444;margin-top:8px;font-size:14px;">
                🚪 Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">

        <?php if ($welcome): ?>
        <div class="alert alert-success" style="margin-bottom:24px;">
            🎉 Welcome to <?= SITE_NAME ?>! Your account has been created successfully. Explore and enroll in courses.
        </div>
        <?php endif; ?>

        <!-- Overview Section -->
        <section id="overview">
            <h2 class="section-heading" style="margin-bottom:24px;">
                Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?>, <?= htmlspecialchars(explode(' ', $student['name'])[0]) ?>! 👋
            </h2>

            <div class="stats-row">
                <div class="stat-card stat-card--primary animate-on-scroll">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?= count($enrolledCourses) ?></div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                <div class="stat-card stat-card--success animate-on-scroll">
                    <div class="stat-icon">📋</div>
                    <div class="stat-value"><?= count($results) ?></div>
                    <div class="stat-label">Tests Appeared</div>
                </div>
                <div class="stat-card stat-card--warning animate-on-scroll">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value"><?= $avgScore ?>%</div>
                    <div class="stat-label">Average Score</div>
                </div>
                <div class="stat-card stat-card--info animate-on-scroll">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-value"><?= getGrade($avgScore) ?></div>
                    <div class="stat-label">Overall Grade</div>
                </div>
            </div>
        </section>

        <!-- My Courses -->
        <section id="my-courses" style="margin-top:40px;">
            <div class="section-header-row">
                <h3 class="section-heading">📚 My Enrolled Courses</h3>
                <a href="courses.php" class="btn btn-outline btn-sm">Browse Courses</a>
            </div>

            <?php if (empty($enrolledCourses)): ?>
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <h4>No Courses Yet</h4>
                <p>You haven't enrolled in any course. Explore our courses and start learning!</p>
                <a href="courses.php" class="btn btn-primary">Browse Courses</a>
            </div>
            <?php else: ?>
            <div class="course-list">
                <?php foreach ($enrolledCourses as $ec): ?>
                <div class="enrolled-card animate-on-scroll">
                    <div class="enrolled-card__header">
                        <div class="enrolled-card__icon">
                            <?php
                            $icons = ['Mathematics'=>'📐','Physics'=>'⚛️','Chemistry'=>'🧪','Biology'=>'🧬','English'=>'📖','Computer'=>'💻','Hindi'=>'🔤'];
                            $icon = '📚';
                            foreach ($icons as $k => $v) { if (stripos($ec['title'], $k) !== false) { $icon = $v; break; } }
                            echo $icon;
                            ?>
                        </div>
                        <div>
                            <h4 class="enrolled-card__title"><?= htmlspecialchars($ec['title']) ?></h4>
                            <span class="badge badge--primary"><?= htmlspecialchars($ec['category']) ?></span>
                        </div>
                        <div class="enrolled-card__status ms-auto">
                            <span class="status-badge status-<?= $ec['status'] ?>">
                                <?= ucfirst($ec['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="enrolled-card__body">
                        <div class="enrolled-meta">
                            <span>⏰ <?= htmlspecialchars($ec['batch_timing']) ?></span>
                            <span>👨‍🏫 <?= htmlspecialchars($ec['instructor']) ?></span>
                            <span>💳 <?= $ec['payment_status'] === 'paid' ? '✅ Paid' : ($ec['payment_status'] === 'partial' ? '⚠️ Partial' : '⏳ Pending') ?></span>
                        </div>
                        <?php
                        // Subject results for this course
                        $subjectQ = mysqli_query($conn, "SELECT * FROM results WHERE student_id=$studentId ORDER BY exam_date DESC LIMIT 5");
                        $subResults = [];
                        while ($sr = mysqli_fetch_assoc($subjectQ)) $subResults[] = $sr;
                        if (!empty($subResults)):
                        ?>
                        <div class="recent-results-mini">
                            <p style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Recent Tests:</p>
                            <?php foreach (array_slice($subResults, 0, 3) as $sr): ?>
                            <div class="mini-result-row">
                                <span><?= htmlspecialchars($sr['exam_name']) ?></span>
                                <div class="mini-progress">
                                    <div class="mini-progress-bar" style="width:<?= $sr['percentage'] ?>%;background:<?= $sr['percentage'] >= 75 ? '#10b981' : ($sr['percentage'] >= 50 ? '#f59e0b' : '#ef4444') ?>"></div>
                                </div>
                                <span class="<?= getGradeClass($sr['percentage']) ?>" style="font-weight:700;min-width:30px;text-align:right;"><?= getGrade($sr['percentage']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- My Results -->
        <section id="my-results" style="margin-top:40px;">
            <div class="section-header-row">
                <h3 class="section-heading">📋 My Exam Results</h3>
                <a href="results.php?sid=<?= urlencode($student['student_id']) ?>" class="btn btn-outline btn-sm" target="_blank">View Full</a>
            </div>

            <?php if (empty($results)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h4>No Results Yet</h4>
                <p>Your exam results will appear here once they are added by your teacher.</p>
            </div>
            <?php else: ?>

            <!-- Average Score Bar -->
            <div class="overall-score-card animate-on-scroll" style="margin-bottom:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-weight:600;">Overall Performance</span>
                    <span style="font-size:24px;font-weight:800;color:var(--primary);"><?= $avgScore ?>%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" data-width="<?= $avgScore ?>" 
                         style="background: <?= $avgScore >= 75 ? 'var(--success)' : ($avgScore >= 50 ? 'var(--secondary)' : '#ef4444') ?>">
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:12px;color:var(--text-muted);">
                    <span>0%</span><span>50%</span><span>75%</span><span>100%</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Exam Name</th>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>%</th>
                            <th>Grade</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $i => $r): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($r['exam_name']) ?></td>
                            <td><?= htmlspecialchars($r['subject']) ?></td>
                            <td><?= $r['marks_obtained'] ?>/<?= $r['total_marks'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="mini-bar-wrap">
                                        <div class="mini-bar" style="width:<?= $r['percentage'] ?>%;background:<?= $r['percentage'] >= 75 ? '#10b981' : ($r['percentage'] >= 50 ? '#f59e0b' : '#ef4444') ?>"></div>
                                    </div>
                                    <?= round($r['percentage'], 1) ?>%
                                </div>
                            </td>
                            <td><span class="grade-badge <?= getGradeClass($r['percentage']) ?>"><?= getGrade($r['percentage']) ?></span></td>
                            <td><?= formatDate($r['exam_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>

        <!-- Announcements -->
        <section id="announcements" style="margin-top:40px;">
            <h3 class="section-heading" style="margin-bottom:20px;">📢 Latest Announcements</h3>
            <?php if (empty($announcements)): ?>
            <div class="empty-state"><div class="empty-icon">📢</div><p>No announcements at the moment.</p></div>
            <?php else: ?>
            <div class="announcements-list">
                <?php foreach ($announcements as $ann): ?>
                <div class="announcement-item animate-on-scroll">
                    <div class="announcement-icon" style="background:<?= getAnnouncementTypeColor($ann['type']) ?>20;color:<?= getAnnouncementTypeColor($ann['type']) ?>">
                        <?= getAnnouncementTypeIcon($ann['type']) ?>
                    </div>
                    <div class="announcement-content">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                            <h4 style="font-weight:700;font-size:15px;"><?= htmlspecialchars($ann['title']) ?></h4>
                            <?php if ($ann['is_important']): ?>
                            <span class="badge badge--danger" style="font-size:11px;">Important</span>
                            <?php endif; ?>
                        </div>
                        <p style="font-size:14px;color:var(--text-muted);margin-bottom:0;"><?= htmlspecialchars($ann['content']) ?></p>
                    </div>
                    <div class="announcement-time"><?= timeAgo($ann['created_at']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Profile -->
        <section id="profile" style="margin-top:40px;margin-bottom:60px;">
            <h3 class="section-heading" style="margin-bottom:20px;">👤 My Profile</h3>
            <div class="profile-card animate-on-scroll">
                <div class="profile-avatar-lg">
                    <?= strtoupper(substr($student['name'], 0, 2)) ?>
                </div>
                <div class="profile-details">
                    <div class="profile-grid">
                        <div class="profile-field">
                            <label>Full Name</label>
                            <p><?= htmlspecialchars($student['name']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Student ID</label>
                            <p style="font-family:monospace;font-weight:700;color:var(--primary);"><?= htmlspecialchars($student['student_id']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Email</label>
                            <p><?= htmlspecialchars($student['email']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Phone</label>
                            <p><?= htmlspecialchars($student['phone']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Class</label>
                            <p><?= htmlspecialchars($student['class_level']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Gender</label>
                            <p><?= $student['gender'] ? ucfirst(htmlspecialchars($student['gender'])) : 'Not specified' ?></p>
                        </div>
                        <?php if ($student['parent_name']): ?>
                        <div class="profile-field">
                            <label>Parent/Guardian</label>
                            <p><?= htmlspecialchars($student['parent_name']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Parent Phone</label>
                            <p><?= htmlspecialchars($student['parent_phone']) ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="profile-field">
                            <label>Member Since</label>
                            <p><?= formatDate($student['created_at']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Account Status</label>
                            <p><span class="status-badge status-active">Active</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>

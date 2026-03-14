<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

// Stats
$totalStudents    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students WHERE status='active'"))[0];
$totalCourses     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses WHERE status='active'"))[0];
$totalEnrollments = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM enrollments"))[0];
$newInquiries     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM admission_inquiries WHERE DATE(created_at)>=DATE_SUB(NOW(),INTERVAL 7 DAY)"))[0];
$unreadContacts   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM contacts WHERE is_read=0"))[0];
$totalResults     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM results"))[0];

// Recent students
$recentStudents = [];
$sq = mysqli_query($conn, "SELECT * FROM students ORDER BY created_at DESC LIMIT 8");
while ($r = mysqli_fetch_assoc($sq)) $recentStudents[] = $r;

// Recent contacts
$recentContacts = [];
$cq = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
while ($r = mysqli_fetch_assoc($cq)) $recentContacts[] = $r;

// Recent admissions inquiries
$recentInquiries = [];
$iq = mysqli_query($conn, "SELECT ai.*, c.title as course_name FROM admission_inquiries ai LEFT JOIN courses c ON ai.course_id=c.id ORDER BY ai.created_at DESC LIMIT 6");
while ($r = mysqli_fetch_assoc($iq)) $recentInquiries[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f1f5f9; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-side { width: 260px; background: #0f172a; position: fixed; height: 100vh; overflow-y: auto; left: 0; top: 0; z-index: 100; }
        .admin-side-logo { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .admin-side-logo .logo-text { color: #fff; font-size: 18px; font-weight: 800; }
        .admin-side-logo .logo-sub { color: rgba(255,255,255,0.4); font-size: 12px; }
        .admin-side-nav { padding: 16px 12px; }
        .admin-nav-group { margin-bottom: 20px; }
        .admin-nav-label { color: rgba(255,255,255,0.3); font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; padding: 0 8px; margin-bottom: 6px; }
        .admin-nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; }
        .admin-nav-link:hover, .admin-nav-link.active { background: rgba(79,70,229,0.3); color: #fff; }
        .admin-nav-link .nav-icon { font-size: 18px; width: 22px; text-align: center; }
        .admin-nav-link .badge-count { margin-left: auto; background: #ef4444; color: #fff; border-radius: 20px; padding: 1px 7px; font-size: 11px; font-weight: 700; }
        .admin-content { margin-left: 260px; padding: 30px; min-height: 100vh; flex: 1; width: calc(100% - 260px); box-sizing: border-box; }
        .admin-topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
        .admin-page-title { font-size: 24px; font-weight: 800; color: #0f172a; }
        .admin-user-info { display: flex; align-items: center; gap: 12px; }
        .admin-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .admin-stat-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); display: flex; align-items: center; gap: 16px; transition: transform 0.2s; }
        .admin-stat-card:hover { transform: translateY(-2px); }
        .admin-stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
        .admin-stat-value { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1; }
        .admin-stat-label { font-size: 13px; color: #64748b; font-weight: 500; margin-top: 3px; }
        .admin-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); overflow: hidden; }
        .admin-card-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
        .admin-card-title { font-weight: 700; font-size: 16px; color: #0f172a; }
        .admin-card-body { padding: 20px; }
        .quick-action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .quick-action { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center; text-decoration: none; color: #334155; transition: all 0.2s; }
        .quick-action:hover { border-color: var(--primary); background: rgba(79,70,229,0.05); color: var(--primary); transform: translateY(-2px); }
        .quick-action .qa-icon { font-size: 28px; margin-bottom: 8px; }
        .quick-action .qa-label { font-size: 13px; font-weight: 600; }
        .table-cell-sm { font-size: 13px; }
        @media (max-width: 900px) {
            .admin-side { transform: translateX(-100%); }
            .admin-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-side">
        <div class="admin-side-logo">
            <div class="logo-text">🎓 <?= SITE_NAME ?></div>
            <div class="logo-sub">Admin Control Panel</div>
        </div>
        <nav class="admin-side-nav">
            <div class="admin-nav-group">
                <div class="admin-nav-label">Main</div>
                <a href="index.php" class="admin-nav-link active">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Academics</div>
                <a href="students.php" class="admin-nav-link">
                    <span class="nav-icon">👨‍🎓</span> Students
                </a>
                <a href="courses.php" class="admin-nav-link">
                    <span class="nav-icon">📚</span> Courses
                </a>
                <a href="enrollments.php" class="admin-nav-link">
                    <span class="nav-icon">📝</span> Enrollments
                </a>
                <a href="results.php" class="admin-nav-link">
                    <span class="nav-icon">🏆</span> Results
                </a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Communications</div>
                <a href="announcements.php" class="admin-nav-link">
                    <span class="nav-icon">📢</span> Announcements
                </a>
                <a href="contacts.php" class="admin-nav-link">
                    <span class="nav-icon">💬</span> Contacts
                    <?php if ($unreadContacts > 0): ?>
                    <span class="badge-count"><?= $unreadContacts ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Account</div>
                <a href="<?= SITE_URL ?>/index.php" class="admin-nav-link" target="_blank">
                    <span class="nav-icon">🌐</span> View Website
                </a>
                <a href="logout.php" class="admin-nav-link" style="color:#ef4444;">
                    <span class="nav-icon">🚪</span> Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-topbar">
            <div>
                <div class="admin-page-title">Dashboard Overview</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;">Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</p>
            </div>
            <div class="admin-user-info">
                <div style="text-align:right;">
                    <div style="font-weight:700;font-size:14px;"><?= htmlspecialchars($_SESSION['admin_name']) ?></div>
                    <div style="font-size:12px;color:#64748b;">Administrator</div>
                </div>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'], 0, 2)) ?></div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#ede9fe;">👨‍🎓</div>
                <div>
                    <div class="admin-stat-value"><?= $totalStudents ?></div>
                    <div class="admin-stat-label">Active Students</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fef3c7;">📚</div>
                <div>
                    <div class="admin-stat-value"><?= $totalCourses ?></div>
                    <div class="admin-stat-label">Active Courses</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#d1fae5;">📝</div>
                <div>
                    <div class="admin-stat-value"><?= $totalEnrollments ?></div>
                    <div class="admin-stat-label">Total Enrollments</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fee2e2;">📋</div>
                <div>
                    <div class="admin-stat-value"><?= $totalResults ?></div>
                    <div class="admin-stat-label">Result Entries</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#cffafe;">📩</div>
                <div>
                    <div class="admin-stat-value" style="color:<?= $newInquiries > 0 ? '#ef4444' : 'inherit' ?>"><?= $newInquiries ?></div>
                    <div class="admin-stat-label">New Inquiries (7d)</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fce7f3;">💬</div>
                <div>
                    <div class="admin-stat-value" style="color:<?= $unreadContacts > 0 ? '#ef4444' : 'inherit' ?>"><?= $unreadContacts ?></div>
                    <div class="admin-stat-label">Unread Messages</div>
                </div>
            </div>
        </div>

        <!-- Grid: Quick actions & Recent -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

            <!-- Quick Actions -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">⚡ Quick Actions</span>
                </div>
                <div class="admin-card-body">
                    <div class="quick-action-grid">
                        <a href="students.php?action=add" class="quick-action">
                            <div class="qa-icon">👨‍🎓</div>
                            <div class="qa-label">Add Student</div>
                        </a>
                        <a href="courses.php?action=add" class="quick-action">
                            <div class="qa-icon">📚</div>
                            <div class="qa-label">Add Course</div>
                        </a>
                        <a href="results.php?action=add" class="quick-action">
                            <div class="qa-icon">🏆</div>
                            <div class="qa-label">Add Result</div>
                        </a>
                        <a href="announcements.php?action=add" class="quick-action">
                            <div class="qa-icon">📢</div>
                            <div class="qa-label">Post Notice</div>
                        </a>
                        <a href="enrollments.php?action=add" class="quick-action">
                            <div class="qa-icon">📝</div>
                            <div class="qa-label">Enroll Student</div>
                        </a>
                        <a href="contacts.php" class="quick-action">
                            <div class="qa-icon">💬</div>
                            <div class="qa-label">View Messages</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Contacts -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">💬 Recent Messages</span>
                    <a href="contacts.php" style="font-size:13px;color:var(--primary);">View All</a>
                </div>
                <div class="admin-card-body" style="padding:0;">
                    <?php if (empty($recentContacts)): ?>
                    <p style="padding:20px;color:#64748b;font-size:14px;">No messages yet.</p>
                    <?php else: ?>
                    <?php foreach ($recentContacts as $c): ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;">
                        <div style="width:36px;height:36px;border-radius:50%;background:<?= $c['is_read'] ? '#e2e8f0' : '#ede9fe' ?>;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:<?= $c['is_read'] ? '#64748b' : 'var(--primary)' ?>;flex-shrink:0;">
                            <?= strtoupper(substr($c['name'], 0, 1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($c['name']) ?></div>
                            <div style="font-size:12px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($c['subject']) ?></div>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;white-space:nowrap;"><?= timeAgo($c['created_at']) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Students & Inquiries -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

            <!-- Recent Students -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">👨‍🎓 Recent Students</span>
                    <a href="students.php" style="font-size:13px;color:var(--primary);">Manage →</a>
                </div>
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Name</th>
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">ID</th>
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Class</th>
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $s): ?>
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:10px 14px;font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
                                <td style="padding:10px 14px;font-family:monospace;color:var(--primary);"><?= htmlspecialchars($s['student_id']) ?></td>
                                <td style="padding:10px 14px;"><?= htmlspecialchars($s['class_level']) ?></td>
                                <td style="padding:10px 14px;color:#64748b;"><?= date('M d', strtotime($s['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Inquiries -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">📩 Admission Inquiries</span>
                    <span style="font-size:13px;color:#64748b;"><?= $newInquiries ?> new this week</span>
                </div>
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Student</th>
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Course</th>
                                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#64748b;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentInquiries)): ?>
                            <tr><td colspan="3" style="padding:16px;text-align:center;color:#64748b;">No inquiries yet.</td></tr>
                            <?php else: ?>
                            <?php foreach ($recentInquiries as $inq): ?>
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:10px 14px;font-weight:600;"><?= htmlspecialchars($inq['student_name']) ?></td>
                                <td style="padding:10px 14px;color:#64748b;font-size:12px;"><?= htmlspecialchars($inq['course_name'] ?? 'N/A') ?></td>
                                <td style="padding:10px 14px;">
                                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                        background:<?= $inq['status'] === 'approved' ? '#d1fae5' : ($inq['status'] === 'rejected' ? '#fee2e2' : '#fef3c7') ?>;
                                        color:<?= $inq['status'] === 'approved' ? '#065f46' : ($inq['status'] === 'rejected' ? '#991b1b' : '#92400e') ?>;">
                                        <?= ucfirst($inq['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../js/script.js"></script>
</body>
</html>

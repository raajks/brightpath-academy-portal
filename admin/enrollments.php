<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = $error = '';
$action = $_GET['action'] ?? 'list';

// ADD ENROLLMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_enrollment'])) {
    $studentId  = (int)$_POST['student_id'];
    $courseId   = (int)$_POST['course_id'];
    $payStatus  = sanitize($_POST['payment_status']);
    $amtPaid    = (float)($_POST['amount_paid'] ?? 0);
    $enrollDate = sanitize($_POST['enrollment_date']);
    $status     = sanitize($_POST['status']);

    if (!$studentId || !$courseId || !$enrollDate) {
        $error = 'Student, course and date are required.';
        $action = 'add';
    } else {
        // Check if already enrolled
        $check = mysqli_query($conn, "SELECT id FROM enrollments WHERE student_id=$studentId AND course_id=$courseId");
        if (mysqli_num_rows($check) > 0) {
            $error = 'This student is already enrolled in the selected course.';
            $action = 'add';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO enrollments (student_id, course_id, enrollment_date, payment_status, amount_paid, status) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'iissds', $studentId, $courseId, $enrollDate, $payStatus, $amtPaid, $status);
            if (mysqli_stmt_execute($stmt)) {
                redirect(SITE_URL . '/admin/enrollments.php?msg=Enrollment+added+successfully');
            } else {
                $error = 'Failed to add enrollment.';
                $action = 'add';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// UPDATE PAYMENT STATUS
if (isset($_GET['mark_paid'])) {
    $eid = (int)$_GET['mark_paid'];
    // Get course fee
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT c.fee FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.id=$eid"));
    $fee = $r['fee'] ?? 0;
    mysqli_query($conn, "UPDATE enrollments SET payment_status='paid', amount_paid=$fee WHERE id=$eid");
    redirect(SITE_URL . '/admin/enrollments.php?msg=Payment+marked+as+paid');
}

// DELETE
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM enrollments WHERE id=$did");
    redirect(SITE_URL . '/admin/enrollments.php?msg=Enrollment+removed');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// Fetch all
$enrollments = [];
$eq = mysqli_query($conn, "SELECT e.*, s.name AS student_name, s.student_id AS sid, s.class_level, c.title AS course_title, c.fee AS course_fee, c.batch_timing FROM enrollments e JOIN students s ON e.student_id=s.id JOIN courses c ON e.course_id=c.id ORDER BY e.created_at DESC");
while ($r = mysqli_fetch_assoc($eq)) $enrollments[] = $r;

// For add form
$allStudents = [];
$sq = mysqli_query($conn, "SELECT id, student_id, name, class_level FROM students WHERE status='active' ORDER BY name");
while ($r = mysqli_fetch_assoc($sq)) $allStudents[] = $r;

$allCourses = [];
$cq = mysqli_query($conn, "SELECT id, title, fee FROM courses WHERE status='active' ORDER BY title");
while ($r = mysqli_fetch_assoc($cq)) $allCourses[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background:#f1f5f9; }
        .admin-wrapper { display:flex; min-height:100vh; }
        .admin-side { width:260px; background:#0f172a; position:fixed; height:100vh; overflow-y:auto; left:0; top:0; z-index:100; }
        .admin-side-logo { padding:24px 20px; border-bottom:1px solid rgba(255,255,255,0.08); }
        .admin-side-logo .logo-text { color:#fff; font-size:18px; font-weight:800; }
        .admin-side-logo .logo-sub { color:rgba(255,255,255,0.4); font-size:12px; }
        .admin-side-nav { padding:16px 12px; }
        .admin-nav-group { margin-bottom:20px; }
        .admin-nav-label { color:rgba(255,255,255,0.3); font-size:11px; text-transform:uppercase; letter-spacing:0.08em; padding:0 8px; margin-bottom:6px; }
        .admin-nav-link { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; color:rgba(255,255,255,0.65); text-decoration:none; font-size:14px; font-weight:500; transition:all 0.2s; margin-bottom:2px; }
        .admin-nav-link:hover, .admin-nav-link.active { background:rgba(79,70,229,0.3); color:#fff; }
        .admin-nav-link .nav-icon { font-size:18px; width:22px; text-align:center; }
        .admin-content { margin-left:260px; padding:30px; min-height:100vh; flex:1; width:calc(100% - 260px); box-sizing:border-box; }
        .admin-topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
        .admin-page-title { font-size:22px; font-weight:800; color:#0f172a; }
        .admin-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07); overflow:hidden; }
        .admin-card-header { padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; }
        .admin-card-title { font-weight:700; font-size:16px; color:#0f172a; }
        .admin-table { width:100%; border-collapse:collapse; font-size:13px; }
        .admin-table th { padding:12px 16px; background:#f8fafc; text-align:left; font-weight:600; color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:0.05em; }
        .admin-table td { padding:12px 16px; border-top:1px solid #f1f5f9; vertical-align:middle; }
        .admin-table tr:hover td { background:#fafafa; }
        .action-btns { display:flex; gap:6px; }
        .btn-icon { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-paid { background:#d1fae5; color:#065f46; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .add-form { padding:24px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    </style>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>
<div class="admin-wrapper">
    <aside class="admin-side">
        <div class="admin-side-logo">
            <div class="logo-text">🎓 <?= SITE_NAME ?></div>
            <div class="logo-sub">Admin Control Panel</div>
        </div>
        <nav class="admin-side-nav">
            <div class="admin-nav-group">
                <div class="admin-nav-label">Main</div>
                <a href="index.php" class="admin-nav-link"><span class="nav-icon">📊</span> Dashboard</a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Academics</div>
                <a href="students.php" class="admin-nav-link"><span class="nav-icon">👨‍🎓</span> Students</a>
                <a href="courses.php" class="admin-nav-link"><span class="nav-icon">📚</span> Courses</a>
                <a href="enrollments.php" class="admin-nav-link active"><span class="nav-icon">📝</span> Enrollments</a>
                <a href="results.php" class="admin-nav-link"><span class="nav-icon">🏆</span> Results</a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Communications</div>
                <a href="announcements.php" class="admin-nav-link"><span class="nav-icon">📢</span> Announcements</a>
                <a href="contacts.php" class="admin-nav-link"><span class="nav-icon">💬</span> Contacts</a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Account</div>
                <a href="<?= SITE_URL ?>/index.php" class="admin-nav-link" target="_blank"><span class="nav-icon">🌐</span> View Website</a>
                <a href="logout.php" class="admin-nav-link" style="color:#ef4444;"><span class="nav-icon">🚪</span> Logout</a>
            </div>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-topbar">
            <div>
                <div class="admin-page-title">📝 Manage Enrollments</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= count($enrollments) ?> total enrollments</p>
            </div>
            <a href="?action=add" class="btn btn-primary">+ Enroll Student</a>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:20px;"><span class="alert-icon">✕</span><?= $error ?></div><?php endif; ?>

        <?php if ($action === 'add'): ?>
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-header">
                <span class="admin-card-title">➕ Enroll a Student</span>
                <a href="enrollments.php" style="font-size:13px;color:#64748b;">← Back</a>
            </div>
            <form method="POST" class="add-form" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Select Student <span style="color:red">*</span></label>
                        <select name="student_id" class="form-control" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach ($allStudents as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= $s['student_id'] ?> | <?= $s['class_level'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Select Course <span style="color:red">*</span></label>
                        <select name="course_id" class="form-control" required id="courseSelect">
                            <option value="">-- Select Course --</option>
                            <?php foreach ($allCourses as $c): ?>
                            <option value="<?= $c['id'] ?>" data-fee="<?= $c['fee'] ?>"><?= htmlspecialchars($c['title']) ?> (₹<?= number_format($c['fee']) ?>/mo)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Enrollment Date <span style="color:red">*</span></label>
                        <input type="date" name="enrollment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid (Full)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount Paid (₹)</label>
                        <input type="number" name="amount_paid" id="amtPaid" class="form-control" min="0" step="100" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="save_enrollment" class="btn btn-primary" style="margin-top:12px;">✅ Add Enrollment</button>
            </form>
            <script>
            document.getElementById('courseSelect').addEventListener('change',function(){
                var fee = this.options[this.selectedIndex]?.dataset.fee || 0;
                var ap = document.getElementById('amtPaid');
                ap.placeholder = 'Course fee: ₹' + Number(fee).toLocaleString('en-IN');
            });
            </script>
        </div>
        <?php endif; ?>

        <!-- Enrollments Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Enrollments</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Enrolled On</th>
                            <th>Payment</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($enrollments)): ?>
                        <tr><td colspan="8" style="text-align:center;padding:30px;color:#64748b;">No enrollments yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($enrollments as $i => $e): ?>
                        <tr>
                            <td style="color:#94a3b8;"><?= $i+1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($e['student_name']) ?></div>
                                <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($e['sid']) ?> | <?= htmlspecialchars($e['class_level']) ?></div>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($e['course_title']) ?></div>
                                <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($e['batch_timing']) ?></div>
                            </td>
                            <td style="color:#64748b;"><?= date('d M Y', strtotime($e['enrollment_date'])) ?></td>
                            <td>
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
                                    background:<?= $e['payment_status']==='paid'?'#d1fae5':($e['payment_status']==='partial'?'#fef3c7':'#fee2e2') ?>;
                                    color:<?= $e['payment_status']==='paid'?'#065f46':($e['payment_status']==='partial'?'#92400e':'#991b1b') ?>;">
                                    <?= ucfirst($e['payment_status']) ?>
                                </span>
                            </td>
                            <td style="font-weight:600;">₹<?= number_format($e['amount_paid']) ?> <span style="font-weight:400;font-size:11px;color:#94a3b8;">/ ₹<?= number_format($e['course_fee']) ?></span></td>
                            <td>
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
                                    background:<?= $e['status']==='active'?'#d1fae5':($e['status']==='completed'?'#dbeafe':'#fee2e2') ?>;
                                    color:<?= $e['status']==='active'?'#065f46':($e['status']==='completed'?'#1e40af':'#991b1b') ?>;">
                                    <?= ucfirst($e['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <?php if ($e['payment_status'] !== 'paid'): ?>
                                    <a href="?mark_paid=<?= $e['id'] ?>" class="btn-icon btn-paid">💳 Paid</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $e['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Remove this enrollment?')">🗑</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>

<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = $error = '';
$action = $_GET['action'] ?? 'list';

// ADD RESULT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_result'])) {
    $studentSearch = sanitize($_POST['student_search']);
    $studentId     = (int)$_POST['student_db_id'];
    $examName      = sanitize($_POST['exam_name']);
    $subject       = sanitize($_POST['subject']);
    $marksObt      = (int)$_POST['marks_obtained'];
    $totalMarks    = (int)$_POST['total_marks'];
    $examDate      = sanitize($_POST['exam_date']);

    if (!$studentId || !$examName || !$subject || !$totalMarks || !$examDate) {
        $error = 'All required fields must be filled.';
        $action = 'add';
    } elseif ($marksObt > $totalMarks) {
        $error = 'Marks obtained cannot exceed total marks.';
        $action = 'add';
    } else {
        $percentage = round(($marksObt / $totalMarks) * 100, 2);
        $grade      = getGrade($percentage);

        $stmt = mysqli_prepare($conn, "INSERT INTO results (student_id, exam_name, subject, marks_obtained, total_marks, percentage, grade, exam_date) VALUES (?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'issiidss', $studentId, $examName, $subject, $marksObt, $totalMarks, $percentage, $grade, $examDate);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Result added successfully!";
        } else {
            $error = 'Failed to add result. ' . mysqli_error($conn);
            $action = 'add';
        }
        mysqli_stmt_close($stmt);
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM results WHERE id=$did");
    redirect(SITE_URL . '/admin/results.php?msg=Result+deleted');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// SEARCH
$search    = sanitize($_GET['q'] ?? '');
$subFilter = sanitize($_GET['subject'] ?? '');

$joinWhere = "WHERE 1=1";
if ($search) $joinWhere .= " AND (s.name LIKE '%$search%' OR s.student_id LIKE '%$search%' OR r.exam_name LIKE '%$search%')";
if ($subFilter) $joinWhere .= " AND r.subject='$subFilter'";

$results = [];
$rq = mysqli_query($conn, "SELECT r.*, s.name AS student_name, s.student_id AS sid, s.class_level FROM results r JOIN students s ON r.student_id=s.id $joinWhere ORDER BY r.exam_date DESC, r.created_at DESC LIMIT 200");
while ($row = mysqli_fetch_assoc($rq)) $results[] = $row;

// Student list for dropdown in add form
$allStudents = [];
$sq = mysqli_query($conn, "SELECT id, student_id, name, class_level FROM students WHERE status='active' ORDER BY name");
while ($row = mysqli_fetch_assoc($sq)) $allStudents[] = $row;

// Subjects for filter
$subjects = ['Mathematics','Physics','Chemistry','Biology','English','Hindi','Computer Science','Science','Social Studies'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results | <?= SITE_NAME ?></title>
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
        .filter-bar { padding:16px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
        .filter-bar input, .filter-bar select { padding:8px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; }
        .admin-table { width:100%; border-collapse:collapse; font-size:13px; }
        .admin-table th { padding:12px 16px; background:#f8fafc; text-align:left; font-weight:600; color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:0.05em; }
        .admin-table td { padding:12px 16px; border-top:1px solid #f1f5f9; vertical-align:middle; }
        .admin-table tr:hover td { background:#fafafa; }
        .action-btns { display:flex; gap:6px; }
        .btn-icon { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .add-form { padding:24px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .mini-bar-w { display:inline-block; width:60px; height:6px; background:#e2e8f0; border-radius:9px; vertical-align:middle; margin-right:6px; }
        .mini-bar-f { display:block; height:6px; border-radius:9px; }
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
                <a href="enrollments.php" class="admin-nav-link"><span class="nav-icon">📝</span> Enrollments</a>
                <a href="results.php" class="admin-nav-link active"><span class="nav-icon">🏆</span> Results</a>
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
                <div class="admin-page-title">🏆 Manage Results</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= count($results) ?> result entries</p>
            </div>
            <a href="?action=add" class="btn btn-primary">+ Add Result</a>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:20px;"><span class="alert-icon">✕</span><?= $error ?></div><?php endif; ?>

        <?php if ($action === 'add'): ?>
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-header">
                <span class="admin-card-title">➕ Add Exam Result</span>
                <a href="results.php" style="font-size:13px;color:#64748b;">← Back to list</a>
            </div>
            <form method="POST" class="add-form" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Select Student <span style="color:red">*</span></label>
                        <select name="student_db_id" class="form-control" required id="studentSelect">
                            <option value="">-- Select Student --</option>
                            <?php foreach ($allStudents as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?> (<?= $st['student_id'] ?> | <?= $st['class_level'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="student_search" id="studentSearch" value="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Exam Name <span style="color:red">*</span></label>
                        <input type="text" name="exam_name" class="form-control" required placeholder="e.g. Monthly Test – December">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject <span style="color:red">*</span></label>
                        <select name="subject" class="form-control" required>
                            <option value="">Select subject</option>
                            <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub ?>"><?= $sub ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Exam Date <span style="color:red">*</span></label>
                        <input type="date" name="exam_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Marks Obtained <span style="color:red">*</span></label>
                        <input type="number" name="marks_obtained" id="marksObt" class="form-control" required min="0" placeholder="e.g. 85">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Marks <span style="color:red">*</span></label>
                        <input type="number" name="total_marks" id="totalMarks" class="form-control" required min="1" placeholder="e.g. 100">
                    </div>
                </div>
                <div id="previewGrade" style="display:none;margin-top:8px;padding:10px 16px;background:#f0f9ff;border-radius:8px;font-size:14px;font-weight:600;"></div>
                <div style="margin-top:16px;display:flex;gap:12px;">
                    <button type="submit" name="save_result" class="btn btn-primary">✅ Save Result</button>
                    <a href="results.php" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
            <script>
            (function(){
                var obt = document.getElementById('marksObt');
                var tot = document.getElementById('totalMarks');
                var prev= document.getElementById('previewGrade');
                function calc(){
                    var o=parseInt(obt.value)||0, t=parseInt(tot.value)||0;
                    if(o>0&&t>0){
                        var pct=Math.round(o/t*100);
                        var grade = pct>=90?'A+':pct>=80?'A':pct>=70?'B+':pct>=60?'B':pct>=50?'C':pct>=33?'D':'F';
                        prev.style.display='block';
                        prev.innerHTML = '📊 Percentage: <strong>'+pct+'%</strong> &nbsp;|&nbsp; Grade: <strong>'+grade+'</strong>';
                    } else { prev.style.display='none'; }
                }
                obt.addEventListener('input',calc);
                tot.addEventListener('input',calc);
            })();
            </script>
        </div>
        <?php endif; ?>

        <!-- Results Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Results</span>
            </div>
            <form method="GET" class="filter-bar">
                <input type="text" name="q" placeholder="🔍 Search by student name, ID or exam..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px;">
                <select name="subject">
                    <option value="">All Subjects</option>
                    <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub ?>" <?= $subFilter === $sub ? 'selected' : '' ?>><?= $sub ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="results.php" class="btn btn-ghost btn-sm">Clear</a>
            </form>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Exam</th>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                        <tr><td colspan="9" style="text-align:center;padding:30px;color:#64748b;">No results yet. Add the first result!</td></tr>
                        <?php else: ?>
                        <?php foreach ($results as $i => $r): ?>
                        <tr>
                            <td style="color:#94a3b8;"><?= $i+1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($r['student_name']) ?></div>
                                <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($r['sid']) ?> | <?= htmlspecialchars($r['class_level']) ?></div>
                            </td>
                            <td style="font-weight:600;font-size:13px;"><?= htmlspecialchars($r['exam_name']) ?></td>
                            <td><span style="padding:3px 9px;border-radius:20px;font-size:11px;background:#f1f5f9;color:#334155;"><?= htmlspecialchars($r['subject']) ?></span></td>
                            <td><?= $r['marks_obtained'] ?>/<?= $r['total_marks'] ?></td>
                            <td>
                                <span class="mini-bar-w"><span class="mini-bar-f" style="width:<?= $r['percentage'] ?>%;background:<?= $r['percentage']>=75?'#10b981':($r['percentage']>=50?'#f59e0b':'#ef4444') ?>"></span></span>
                                <?= round($r['percentage'], 1) ?>%
                            </td>
                            <td>
                                <span style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;
                                    background:<?= $r['percentage']>=75?'#d1fae5':($r['percentage']>=50?'#fef3c7':'#fee2e2') ?>;
                                    color:<?= $r['percentage']>=75?'#065f46':($r['percentage']>=50?'#92400e':'#991b1b') ?>;">
                                    <?= getGrade($r['percentage']) ?>
                                </span>
                            </td>
                            <td style="color:#64748b;"><?= date('d M Y', strtotime($r['exam_date'])) ?></td>
                            <td>
                                <a href="?delete=<?= $r['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Delete this result?')">🗑</a>
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

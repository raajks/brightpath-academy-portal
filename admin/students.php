<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = $error = '';
$action = $_GET['action'] ?? 'list';

// --- ADD STUDENT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name        = sanitize($_POST['name']);
    $email       = sanitize($_POST['email']);
    $phone       = sanitize($_POST['phone']);
    $classLevel  = sanitize($_POST['class_level']);
    $parentName  = sanitize($_POST['parent_name']);
    $parentPhone = sanitize($_POST['parent_phone']);
    $gender      = sanitize($_POST['gender']);
    $password    = password_hash('brightpath123', PASSWORD_DEFAULT); // default pass
    $studentId   = generateStudentId();

    if (!$name || !$email || !$phone || !$classLevel) {
        $error = 'Name, email, phone and class are required.';
        $action = 'add';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already exists.';
            $action = 'add';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO students (student_id, name, email, phone, password, class_level, parent_name, parent_phone, gender) VALUES (?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'sssssssss', $studentId, $name, $email, $phone, $password, $classLevel, $parentName, $parentPhone, $gender);
            if (mysqli_stmt_execute($stmt)) {
                $msg = "Student added! ID: $studentId | Default password: brightpath123";
            } else {
                $error = 'Failed to add student.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// --- TOGGLE STATUS ---
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $cur = mysqli_fetch_row(mysqli_query($conn, "SELECT status FROM students WHERE id=$tid"))[0] ?? '';
    $new = $cur === 'active' ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE students SET status='$new' WHERE id=$tid");
    redirect(SITE_URL . '/admin/students.php?msg=Status+updated');
}

// --- DELETE ---
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM students WHERE id=$did");
    redirect(SITE_URL . '/admin/students.php?msg=Student+deleted');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// --- SEARCH & FILTER ---
$search    = sanitize($_GET['q'] ?? '');
$classFilter = sanitize($_GET['class'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');
$where = "WHERE 1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR student_id LIKE '%$search%' OR phone LIKE '%$search%')";
if ($classFilter) $where .= " AND class_level='$classFilter'";
if ($statusFilter) $where .= " AND status='$statusFilter'";

$students = [];
$sq = mysqli_query($conn, "SELECT * FROM students $where ORDER BY created_at DESC");
while ($r = mysqli_fetch_assoc($sq)) $students[] = $r;
$totalCount = count($students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f1f5f9; }
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
        .filter-bar input:focus, .filter-bar select:focus { border-color:var(--primary); }
        .admin-table { width:100%; border-collapse:collapse; font-size:13px; }
        .admin-table th { padding:12px 16px; background:#f8fafc; text-align:left; font-weight:600; color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:0.05em; }
        .admin-table td { padding:12px 16px; border-top:1px solid #f1f5f9; vertical-align:middle; }
        .admin-table tr:hover td { background:#fafafa; }
        .action-btns { display:flex; gap:6px; }
        .btn-icon { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-edit { background:#ede9fe; color:#4f46e5; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .btn-toggle { background:#f0fdf4; color:#16a34a; }
        .student-avatar { width:36px; height:36px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; }
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
                <a href="students.php" class="admin-nav-link active"><span class="nav-icon">👨‍🎓</span> Students</a>
                <a href="courses.php" class="admin-nav-link"><span class="nav-icon">📚</span> Courses</a>
                <a href="enrollments.php" class="admin-nav-link"><span class="nav-icon">📝</span> Enrollments</a>
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
                <div class="admin-page-title">👨‍🎓 Manage Students</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= $totalCount ?> students found</p>
            </div>
            <a href="?action=add" class="btn btn-primary">+ Add Student</a>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:20px;"><span class="alert-icon">✕</span><?= $error ?></div><?php endif; ?>

        <?php if ($action === 'add'): ?>
        <!-- Add Student Form -->
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-header">
                <span class="admin-card-title">➕ Add New Student</span>
                <a href="students.php" style="font-size:13px;color:#64748b;">← Back to list</a>
            </div>
            <form method="POST" class="add-form" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name <span style="color:red">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Student's full name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Class <span style="color:red">*</span></label>
                        <select name="class_level" class="form-control" required>
                            <option value="">Select class</option>
                            <?php foreach (['6','7','8','9','10','11 (Science)','11 (Commerce)','12 (Science)','12 (Commerce)'] as $cl): ?>
                            <option value="<?= $cl ?>"><?= $cl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span style="color:red">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="student@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone <span style="color:red">*</span></label>
                        <input type="tel" name="phone" class="form-control" required placeholder="10-digit number">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent Name</label>
                        <input type="text" name="parent_name" class="form-control" placeholder="Parent/Guardian name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent Phone</label>
                        <input type="tel" name="parent_phone" class="form-control" placeholder="Parent phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top:8px;padding:10px 14px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;">
                    ℹ️ Default password will be: <strong>brightpath123</strong>. Ask student to change it after first login.
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;">
                    <button type="submit" name="add_student" class="btn btn-primary">✅ Add Student</button>
                    <a href="students.php" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Students Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Students</span>
                <span style="font-size:13px;color:#64748b;"><?= $totalCount ?> total</span>
            </div>
            <form method="GET" class="filter-bar">
                <input type="text" name="q" placeholder="🔍 Search by name, email, ID, phone..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px;">
                <select name="class">
                    <option value="">All Classes</option>
                    <?php foreach (['6','7','8','9','10','11 (Science)','11 (Commerce)','12 (Science)','12 (Commerce)'] as $cl): ?>
                    <option value="<?= $cl ?>" <?= $classFilter === $cl ? 'selected' : '' ?>><?= $cl ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="students.php" class="btn btn-ghost btn-sm">Clear</a>
            </form>

            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Class</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                        <tr><td colspan="8" style="text-align:center;padding:30px;color:#64748b;">No students found.</td></tr>
                        <?php else: ?>
                        <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td style="color:#94a3b8;"><?= $i+1 ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="student-avatar"><?= strtoupper(substr($s['name'],0,2)) ?></div>
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></div>
                                        <div style="font-size:12px;color:#64748b;"><?= htmlspecialchars($s['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-family:monospace;color:var(--primary);font-weight:600;"><?= htmlspecialchars($s['student_id']) ?></td>
                            <td><?= htmlspecialchars($s['class_level']) ?></td>
                            <td><?= htmlspecialchars($s['phone']) ?></td>
                            <td>
                                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:<?= $s['status']==='active' ? '#d1fae5' : '#fee2e2' ?>;color:<?= $s['status']==='active' ? '#065f46' : '#991b1b' ?>;">
                                    <?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td style="color:#64748b;"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?toggle=<?= $s['id'] ?>" class="btn-icon btn-toggle" title="Toggle Status">
                                        <?= $s['status']==='active' ? '🔒' : '🔓' ?>
                                    </a>
                                    <a href="?delete=<?= $s['id'] ?>" class="btn-icon btn-delete" 
                                       onclick="return confirm('Delete student <?= addslashes($s['name']) ?>? This cannot be undone.')" title="Delete">🗑</a>
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

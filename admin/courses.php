<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = $error = '';
$action = $_GET['action'] ?? 'list';

// Admin sidebar include helper - shared styles
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses | <?= SITE_NAME ?></title>
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
        .btn-icon { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-edit { background:#ede9fe; color:#4f46e5; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .add-form { padding:24px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-group-full { grid-column:1/-1; }
    </style>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>
<?php
// Process Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $title       = sanitize($_POST['title']);
    $category    = sanitize($_POST['category']);
    $classLevel  = sanitize($_POST['class_level']);
    $description = sanitize($_POST['description']);
    $fee         = (float)($_POST['fee'] ?? 0);
    $duration    = sanitize($_POST['duration']);
    $timing      = sanitize($_POST['timing']);
    $instructor  = sanitize($_POST['instructor']);
    $seats       = (int)($_POST['seats'] ?? 30);
    $features    = sanitize($_POST['features']);
    $courseId    = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;

    if (!$title || !$category || !$classLevel) {
        $error = 'Title, category and class are required.';
        $action = $courseId ? 'edit' : 'add';
    } else {
        if ($courseId > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE courses SET title=?,category=?,class_level=?,description=?,fee=?,duration=?,batch_timing=?,instructor=?,seats=?,features=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssdsssisi', $title,$category,$classLevel,$description,$fee,$duration,$timing,$instructor,$seats,$features,$courseId);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO courses (title,category,class_level,description,fee,duration,batch_timing,instructor,seats,features) VALUES (?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssssdsssis', $title,$category,$classLevel,$description,$fee,$duration,$timing,$instructor,$seats,$features);
            // Note: 'd' is for double (fee), 'i' is for int (seats)
        }
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            redirect(SITE_URL . '/admin/courses.php?msg=' . urlencode($courseId ? 'Course updated!' : 'Course added successfully!'));
        } else {
            $error = 'Failed to save. ' . mysqli_error($conn);
            $action = 'add';
        }
    }
}

// Process Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "UPDATE courses SET status='inactive' WHERE id=$did");
    redirect(SITE_URL . '/admin/courses.php?msg=Course+deactivated');
}
// Process Activate
if (isset($_GET['activate'])) {
    $aid = (int)$_GET['activate'];
    mysqli_query($conn, "UPDATE courses SET status='active' WHERE id=$aid");
    redirect(SITE_URL . '/admin/courses.php?msg=Course+activated');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// Edit mode
$editCourse = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $eid = (int)$_GET['id'];
    $editCourse = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM courses WHERE id=$eid"));
}

// Fetch courses
$courses = [];
$cq = mysqli_query($conn, "SELECT * FROM courses ORDER BY status DESC, created_at DESC");
while ($r = mysqli_fetch_assoc($cq)) $courses[] = $r;
?>

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
                <a href="courses.php" class="admin-nav-link active"><span class="nav-icon">📚</span> Courses</a>
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
                <div class="admin-page-title">📚 Manage Courses</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= count($courses) ?> total courses</p>
            </div>
            <a href="?action=add" class="btn btn-primary">+ Add Course</a>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:20px;"><span class="alert-icon">✕</span><?= $error ?></div><?php endif; ?>

        <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-header">
                <span class="admin-card-title"><?= $action === 'edit' ? '✏️ Edit Course' : '➕ Add New Course' ?></span>
                <a href="courses.php" style="font-size:13px;color:#64748b;">← Back to list</a>
            </div>
            <form method="POST" class="add-form" novalidate>
                <?php if ($editCourse): ?>
                <input type="hidden" name="course_id" value="<?= $editCourse['id'] ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label class="form-label">Course Title <span style="color:red">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               value="<?= htmlspecialchars($editCourse['title'] ?? '') ?>" placeholder="e.g. JEE Mathematics Foundation">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category <span style="color:red">*</span></label>
                        <select name="category" class="form-control" required>
                            <option value="">Select category</option>
                            <?php foreach (['Mathematics','Physics','Chemistry','Biology','English','Hindi','Computer Science','Foundation','JEE / NEET Prep'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($editCourse['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Class Level <span style="color:red">*</span></label>
                        <select name="class_level" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach (['Class 6-8','Class 9-10','Class 11-12','JEE Prep','NEET Prep','Foundation (6-10)'] as $cl): ?>
                            <option value="<?= $cl ?>" <?= ($editCourse['class_level'] ?? '') === $cl ? 'selected' : '' ?>><?= $cl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fee (₹/month)</label>
                        <input type="number" name="fee" class="form-control" min="0" step="100"
                               value="<?= $editCourse['fee'] ?? '' ?>" placeholder="e.g. 2500">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control"
                               value="<?= htmlspecialchars($editCourse['duration'] ?? '') ?>" placeholder="e.g. 12 Months / 1 Year">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batch Timing</label>
                        <input type="text" name="timing" class="form-control"
                               value="<?= htmlspecialchars($editCourse['batch_timing'] ?? '') ?>" placeholder="e.g. Mon/Wed/Fri 4:00 PM–5:30 PM">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instructor Name</label>
                        <input type="text" name="instructor" class="form-control"
                               value="<?= htmlspecialchars($editCourse['instructor'] ?? '') ?>" placeholder="Teacher's name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Seats Available</label>
                        <input type="number" name="seats" class="form-control" min="1"
                               value="<?= $editCourse['seats'] ?? 30 ?>">
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief course description..."><?= htmlspecialchars($editCourse['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Key Features (comma-separated)</label>
                        <input type="text" name="features" class="form-control"
                               value="<?= htmlspecialchars($editCourse['features'] ?? '') ?>" placeholder="e.g. NCERT Coverage,Practice Tests,Study Material,Doubt Sessions">
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;">
                    <button type="submit" name="save_course" class="btn btn-primary">
                        <?= $action === 'edit' ? '✅ Update Course' : '✅ Add Course' ?>
                    </button>
                    <a href="courses.php" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Courses Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Courses</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>Fee/Month</th>
                            <th>Timing</th>
                            <th>Instructor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                        <tr><td colspan="9" style="text-align:center;padding:30px;color:#64748b;">No courses found. Add your first course!</td></tr>
                        <?php else: ?>
                        <?php foreach ($courses as $i => $c): ?>
                        <tr>
                            <td style="color:#94a3b8;"><?= $i+1 ?></td>
                            <td>
                                <div style="font-weight:700;font-size:14px;"><?= htmlspecialchars($c['title']) ?></div>
                                <div style="font-size:12px;color:#64748b;"><?= htmlspecialchars(substr($c['description'], 0, 60)) ?>...</div>
                            </td>
                            <td><span style="padding:3px 10px;border-radius:20px;font-size:12px;background:#ede9fe;color:#4f46e5;font-weight:600;"><?= htmlspecialchars($c['category']) ?></span></td>
                            <td style="font-size:13px;"><?= htmlspecialchars($c['class_level']) ?></td>
                            <td style="font-weight:600;">₹<?= number_format($c['fee']) ?></td>
                            <td style="font-size:12px;color:#64748b;"><?= htmlspecialchars($c['batch_timing']) ?></td>
                            <td style="font-size:13px;"><?= htmlspecialchars($c['instructor']) ?></td>
                            <td>
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:<?= $c['status']==='active' ? '#d1fae5' : '#fee2e2' ?>;color:<?= $c['status']==='active' ? '#065f46' : '#991b1b' ?>;">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="?action=edit&id=<?= $c['id'] ?>" class="btn-icon btn-edit">✏️</a>
                                    <?php if ($c['status'] === 'active'): ?>
                                    <a href="?delete=<?= $c['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Deactivate this course?')">🔒</a>
                                    <?php else: ?>
                                    <a href="?activate=<?= $c['id'] ?>" class="btn-icon btn-toggle" style="background:#d1fae5;color:#065f46;">🔓</a>
                                    <?php endif; ?>
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

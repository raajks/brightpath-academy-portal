<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = $error = '';
$action = $_GET['action'] ?? 'list';

// SAVE ANNOUNCEMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ann'])) {
    $title       = sanitize($_POST['title']);
    $content     = sanitize($_POST['content']);
    $type        = sanitize($_POST['type']);
    $isImportant = isset($_POST['is_important']) ? 1 : 0;
    $annId       = isset($_POST['ann_id']) ? (int)$_POST['ann_id'] : 0;

    if (!$title || !$content) {
        $error = 'Title and content are required.';
        $action = $annId ? 'edit' : 'add';
    } else {
        if ($annId > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE announcements SET title=?,content=?,type=?,is_important=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssii', $title, $content, $type, $isImportant, $annId);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO announcements (title,content,type,is_important) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'sssi', $title, $content, $type, $isImportant);
        }
        if (mysqli_stmt_execute($stmt)) {
            redirect(SITE_URL . '/admin/announcements.php?msg=' . urlencode($annId ? 'Announcement updated!' : 'Announcement posted!'));
        } else {
            $error = 'Failed to save.';
            $action = 'add';
        }
        mysqli_stmt_close($stmt);
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM announcements WHERE id=$did");
    redirect(SITE_URL . '/admin/announcements.php?msg=Announcement+deleted');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// Edit mode
$editAnn = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $eid = (int)$_GET['id'];
    $editAnn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM announcements WHERE id=$eid"));
}

$announcements = [];
$aq = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
while ($r = mysqli_fetch_assoc($aq)) $announcements[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | <?= SITE_NAME ?></title>
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
        .ann-list { padding:8px 0; }
        .ann-item { display:flex; gap:16px; padding:16px 20px; border-bottom:1px solid #f1f5f9; align-items:flex-start; }
        .ann-item:last-child { border-bottom:none; }
        .ann-icon { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .ann-body { flex:1; }
        .ann-title { font-weight:700; font-size:15px; margin-bottom:4px; }
        .ann-content { font-size:13px; color:#64748b; margin-bottom:8px; }
        .ann-meta { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .ann-actions { display:flex; gap:6px; align-items:flex-start; flex-shrink:0; }
        .btn-icon { padding:7px 11px; border-radius:8px; border:none; cursor:pointer; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-edit { background:#ede9fe; color:#4f46e5; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .add-form { padding:24px; }
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
                <a href="results.php" class="admin-nav-link"><span class="nav-icon">🏆</span> Results</a>
            </div>
            <div class="admin-nav-group">
                <div class="admin-nav-label">Communications</div>
                <a href="announcements.php" class="admin-nav-link active"><span class="nav-icon">📢</span> Announcements</a>
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
                <div class="admin-page-title">📢 Manage Announcements</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= count($announcements) ?> announcements</p>
            </div>
            <a href="?action=add" class="btn btn-primary">+ Post Announcement</a>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:20px;"><span class="alert-icon">✕</span><?= $error ?></div><?php endif; ?>

        <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-header">
                <span class="admin-card-title"><?= $action === 'edit' ? '✏️ Edit Announcement' : '➕ Post New Announcement' ?></span>
                <a href="announcements.php" style="font-size:13px;color:#64748b;">← Back</a>
            </div>
            <form method="POST" class="add-form" novalidate>
                <?php if ($editAnn): ?>
                <input type="hidden" name="ann_id" value="<?= $editAnn['id'] ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label class="form-label">Title <span style="color:red">*</span></label>
                    <input type="text" name="title" class="form-control" required
                           value="<?= htmlspecialchars($editAnn['title'] ?? '') ?>" placeholder="Announcement title...">
                </div>
                <div class="form-group">
                    <label class="form-label">Content <span style="color:red">*</span></label>
                    <textarea name="content" class="form-control" rows="4" required placeholder="Write the announcement details..."><?= htmlspecialchars($editAnn['content'] ?? '') ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <?php foreach (['notice','event','result','holiday','exam'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($editAnn['type'] ?? 'notice') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:30px;">
                        <input type="checkbox" name="is_important" id="isImp" value="1" style="width:18px;height:18px;cursor:pointer;"
                               <?= ($editAnn['is_important'] ?? 0) ? 'checked' : '' ?>>
                        <label for="isImp" style="font-weight:600;cursor:pointer;font-size:14px;">Mark as Important 🔴</label>
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:4px;">
                    <button type="submit" name="save_ann" class="btn btn-primary">
                        <?= $action === 'edit' ? '✅ Update' : '📢 Post Announcement' ?>
                    </button>
                    <a href="announcements.php" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- List -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Announcements</span>
            </div>
            <div class="ann-list">
                <?php if (empty($announcements)): ?>
                <p style="padding:30px;text-align:center;color:#64748b;">No announcements yet. Post your first announcement!</p>
                <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                <div class="ann-item">
                    <div class="ann-icon" style="background:<?= getAnnouncementTypeColor($a['type']) ?>20;color:<?= getAnnouncementTypeColor($a['type']) ?>">
                        <?= getAnnouncementTypeIcon($a['type']) ?>
                    </div>
                    <div class="ann-body">
                        <div class="ann-title">
                            <?= htmlspecialchars($a['title']) ?>
                            <?php if ($a['is_important']): ?>
                            <span style="font-size:11px;background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-weight:700;margin-left:8px;">Important</span>
                            <?php endif; ?>
                        </div>
                        <div class="ann-content"><?= nl2br(htmlspecialchars($a['content'])) ?></div>
                        <div class="ann-meta">
                            <span style="font-size:12px;background:#f1f5f9;padding:2px 10px;border-radius:20px;color:#64748b;"><?= ucfirst($a['type']) ?></span>
                            <span style="font-size:12px;color:#94a3b8;"><?= timeAgo($a['created_at']) ?></span>
                        </div>
                    </div>
                    <div class="ann-actions">
                        <a href="?action=edit&id=<?= $a['id'] ?>" class="btn-icon btn-edit">✏️ Edit</a>
                        <a href="?delete=<?= $a['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Delete this announcement?')">🗑</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>

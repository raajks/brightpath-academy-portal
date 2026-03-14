<?php
$isAdminPage = true;
require_once '../config.php';
requireAdminLogin();

$msg = '';

// Mark as read
if (isset($_GET['read'])) {
    $rid = (int)$_GET['read'];
    mysqli_query($conn, "UPDATE contacts SET is_read=1 WHERE id=$rid");
    redirect(SITE_URL . '/admin/contacts.php?msg=Marked+as+read');
}

// Mark all read
if (isset($_GET['readall'])) {
    mysqli_query($conn, "UPDATE contacts SET is_read=1");
    redirect(SITE_URL . '/admin/contacts.php?msg=All+marked+as+read');
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM contacts WHERE id=$did");
    redirect(SITE_URL . '/admin/contacts.php?msg=Message+deleted');
}

if (isset($_GET['msg'])) $msg = htmlspecialchars($_GET['msg']);

// View single
$viewMsg = null;
if (isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $viewMsg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM contacts WHERE id=$vid"));
    if ($viewMsg && !$viewMsg['is_read']) {
        mysqli_query($conn, "UPDATE contacts SET is_read=1 WHERE id=$vid");
    }
}

$contacts = [];
$cq = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC");
while ($r = mysqli_fetch_assoc($cq)) $contacts[] = $r;

$unread = array_filter($contacts, fn($c) => !$c['is_read']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | <?= SITE_NAME ?></title>
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
        .admin-nav-link .badge-count { margin-left:auto; background:#ef4444; color:#fff; border-radius:20px; padding:1px 7px; font-size:11px; font-weight:700; }
        .admin-content { margin-left:260px; padding:30px; min-height:100vh; flex:1; width:calc(100% - 260px); box-sizing:border-box; }
        .admin-topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
        .admin-page-title { font-size:22px; font-weight:800; color:#0f172a; }
        .admin-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07); overflow:hidden; }
        .admin-card-header { padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; }
        .admin-card-title { font-weight:700; font-size:16px; color:#0f172a; }
        .msg-item { display:flex; gap:14px; padding:16px 20px; border-bottom:1px solid #f1f5f9; align-items:flex-start; }
        .msg-item.unread { background:#fefce8; border-left:3px solid #f59e0b; }
        .msg-item:last-child { border-bottom:none; }
        .msg-avatar { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; flex-shrink:0; }
        .msg-body { flex:1; min-width:0; }
        .msg-header { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:3px; }
        .msg-name { font-weight:700; font-size:14px; }
        .msg-time { font-size:12px; color:#94a3b8; white-space:nowrap; }
        .msg-subject { font-size:13px; font-weight:600; color:#334155; margin-bottom:3px; }
        .msg-preview { font-size:13px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .msg-actions { display:flex; gap:6px; flex-shrink:0; }
        .btn-icon { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600; }
        .btn-view { background:#ede9fe; color:#4f46e5; }
        .btn-read { background:#d1fae5; color:#065f46; }
        .btn-delete { background:#fee2e2; color:#ef4444; }
        .view-panel { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07); padding:24px; margin-bottom:24px; }
        .view-panel h3 { font-size:18px; font-weight:700; margin-bottom:4px; }
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
                <a href="announcements.php" class="admin-nav-link"><span class="nav-icon">📢</span> Announcements</a>
                <a href="contacts.php" class="admin-nav-link active"><span class="nav-icon">💬</span> Contacts
                    <?php if (count($unread) > 0): ?>
                    <span class="badge-count"><?= count($unread) ?></span>
                    <?php endif; ?>
                </a>
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
                <div class="admin-page-title">💬 Contact Messages</div>
                <p style="color:#64748b;font-size:14px;margin-top:2px;"><?= count($contacts) ?> total | <?= count($unread) ?> unread</p>
            </div>
            <?php if (count($unread) > 0): ?>
            <a href="?readall" class="btn btn-outline btn-sm">✓ Mark All Read</a>
            <?php endif; ?>
        </div>

        <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px;"><span class="alert-icon">✓</span><?= $msg ?></div><?php endif; ?>

        <?php if ($viewMsg): ?>
        <div class="view-panel">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:4px;">From</div>
                    <h3><?= htmlspecialchars($viewMsg['name']) ?></h3>
                    <div style="display:flex;gap:16px;margin-top:4px;font-size:13px;color:#64748b;">
                        <span>📧 <?= htmlspecialchars($viewMsg['email']) ?></span>
                        <?php if ($viewMsg['phone']): ?>
                        <span>📱 <?= htmlspecialchars($viewMsg['phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:13px;color:#94a3b8;"><?= date('d M Y, h:i A', strtotime($viewMsg['created_at'])) ?></div>
                    <a href="contacts.php" style="font-size:13px;color:var(--primary);">← Back to list</a>
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:16px 20px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:700;color:#64748b;margin-bottom:6px;">SUBJECT</div>
                <p style="font-weight:600;font-size:15px;margin:0;"><?= htmlspecialchars($viewMsg['subject']) ?></p>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:16px 20px;">
                <div style="font-size:13px;font-weight:700;color:#64748b;margin-bottom:6px;">MESSAGE</div>
                <p style="font-size:15px;line-height:1.7;margin:0;"><?= nl2br(htmlspecialchars($viewMsg['message'])) ?></p>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;">
                <a href="mailto:<?= htmlspecialchars($viewMsg['email']) ?>" class="btn btn-primary btn-sm">
                    📧 Reply via Email
                </a>
                <?php if ($viewMsg['phone']): ?>
                <a href="tel:<?= htmlspecialchars($viewMsg['phone']) ?>" class="btn btn-outline btn-sm">
                    📱 Call
                </a>
                <?php endif; ?>
                <a href="?delete=<?= $viewMsg['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444;" onclick="return confirm('Delete this message?')">🗑 Delete</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Messages List -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Messages</span>
            </div>
            <?php if (empty($contacts)): ?>
            <p style="padding:30px;text-align:center;color:#64748b;">No messages yet.</p>
            <?php else: ?>
            <?php foreach ($contacts as $c): ?>
            <div class="msg-item <?= !$c['is_read'] ? 'unread' : '' ?>">
                <div class="msg-avatar" style="background:<?= !$c['is_read'] ? '#fef3c7' : '#f1f5f9' ?>;color:<?= !$c['is_read'] ? '#92400e' : '#64748b' ?>;">
                    <?= strtoupper(substr($c['name'], 0, 1)) ?>
                </div>
                <div class="msg-body">
                    <div class="msg-header">
                        <span class="msg-name"><?= htmlspecialchars($c['name']) ?><?= !$c['is_read'] ? ' 🔔' : '' ?></span>
                        <span class="msg-time"><?= timeAgo($c['created_at']) ?></span>
                    </div>
                    <div class="msg-subject"><?= htmlspecialchars($c['subject']) ?></div>
                    <div class="msg-preview"><?= htmlspecialchars(substr($c['message'], 0, 100)) ?>...</div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:4px;">📧 <?= htmlspecialchars($c['email']) ?><?= $c['phone'] ? ' | 📱 '.$c['phone'] : '' ?></div>
                </div>
                <div class="msg-actions">
                    <a href="?view=<?= $c['id'] ?>" class="btn-icon btn-view">👁</a>
                    <?php if (!$c['is_read']): ?>
                    <a href="?read=<?= $c['id'] ?>" class="btn-icon btn-read">✓</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $c['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Delete this message?')">🗑</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>

<?php
// student/includes/sidebar.php
// Requires: $student, $activePage, SITE_URL
$ap = $activePage ?? '';

// Count unread/pending for badges
$annCount = 0;
$annQ = mysqli_query($conn, "SELECT COUNT(*) as c FROM announcements WHERE status='active' AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
if ($annQ) { $annCount = (int)(mysqli_fetch_assoc($annQ)['c'] ?? 0); }
?>
<aside class="sp-side" id="spSide">

    <div class="sp-side-logo">
        <div class="sp-logo-main">Bright<span>Path</span></div>
        <div class="sp-logo-sub">Student Portal</div>
    </div>

    <div class="sp-side-profile">
        <div class="sp-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        <div style="overflow:hidden;">
            <div class="sp-profile-name"><?= htmlspecialchars($student['name']) ?></div>
            <div class="sp-profile-id"><?= htmlspecialchars($student['student_id']) ?></div>
        </div>
    </div>

    <nav class="sp-nav">
        <div class="sp-nav-label">Main</div>
        <a href="dashboard.php"      class="sp-nav-link <?= $ap==='dashboard'?'active':'' ?>"><span class="ni">📊</span> Overview</a>
        <a href="courses.php"        class="sp-nav-link <?= $ap==='courses'?'active':'' ?>"><span class="ni">📚</span> My Courses</a>
        <a href="results.php"        class="sp-nav-link <?= $ap==='results'?'active':'' ?>"><span class="ni">📋</span> My Results</a>
        <a href="announcements.php"  class="sp-nav-link <?= $ap==='announcements'?'active':'' ?>">
            <span class="ni">📢</span> Announcements
            <?php if ($annCount > 0): ?>
                <span class="sp-badge"><?= $annCount ?></span>
            <?php endif; ?>
        </a>

        <div class="sp-nav-label">Account</div>
        <a href="profile.php"        class="sp-nav-link <?= $ap==='profile'?'active':'' ?>"><span class="ni">👤</span> My Profile</a>
        <a href="../courses.php"     class="sp-nav-link"><span class="ni">➕</span> Browse Courses</a>
        <a href="../index.php"       class="sp-nav-link"><span class="ni">🌐</span> Back to Website</a>
    </nav>

    <div class="sp-side-footer">
        <a href="logout.php" class="sp-nav-link red-link"><span class="ni">🚪</span> Logout</a>
    </div>
</aside>

<?php
require_once '../config.php';
requireStudentLogin();

$studentId  = (int)$_SESSION['student_id'];
$student    = getStudentById($studentId);
$pageTitle  = 'Announcements';
$activePage = 'announcements';

// Active filter tab
$filterType = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : 'all';
$validTypes = ['all','notice','event','exam','result','holiday','admission'];
if (!in_array($filterType, $validTypes)) $filterType = 'all';

// Query all active announcements
$whereType = ($filterType !== 'all') ? "AND type='$filterType'" : '';
$annsQ = mysqli_query($conn,
    "SELECT * FROM announcements
     WHERE status='active' AND (expiry_date IS NULL OR expiry_date >= CURDATE())
     $whereType
     ORDER BY is_important DESC, created_at DESC");
$announcements = mysqli_fetch_all($annsQ, MYSQLI_ASSOC);

// Count by type for tabs
$typeCountQ = mysqli_query($conn,
    "SELECT type, COUNT(*) as c FROM announcements
     WHERE status='active' AND (expiry_date IS NULL OR expiry_date >= CURDATE())
     GROUP BY type");
$typeCounts = ['all' => 0];
while ($tc = mysqli_fetch_assoc($typeCountQ)) {
    $typeCounts[$tc['type']] = (int)$tc['c'];
    $typeCounts['all'] += (int)$tc['c'];
}

$typeColors = [
    'notice'   => ['bg'=>'#ede9fe','c'=>'#6d28d9','hex'=>'#7c3aed'],
    'event'    => ['bg'=>'#d1fae5','c'=>'#065f46','hex'=>'#10b981'],
    'exam'     => ['bg'=>'#fee2e2','c'=>'#991b1b','hex'=>'#ef4444'],
    'result'   => ['bg'=>'#fef3c7','c'=>'#92400e','hex'=>'#f59e0b'],
    'holiday'  => ['bg'=>'#fce7f3','c'=>'#9d174d','hex'=>'#ec4899'],
    'admission'=> ['bg'=>'#cffafe','c'=>'#155e75','hex'=>'#06b6d4'],
];

$typeLabels = [
    'all'      => 'All',
    'notice'   => 'Notices',
    'event'    => 'Events',
    'exam'     => 'Exams',
    'result'   => 'Results',
    'holiday'  => 'Holidays',
    'admission'=> 'Admissions',
];

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
                <h1 class="sp-page-title">Announcements</h1>
                <p class="sp-page-sub">Stay updated with the latest notices &amp; events</p>
            </div>
        </div>
        <div class="sp-topbar-right">
            <div class="sp-topbar-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        </div>
    </div>

    <!-- Filter tabs -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;overflow-x:auto;padding-bottom:4px;">
        <?php foreach ($typeLabels as $type => $label):
            $isActive = $filterType === $type;
            $cnt      = $typeCounts[$type] ?? 0;
            if ($type !== 'all' && $cnt === 0) continue;
            $tc = $typeColors[$type] ?? null;
            $tabBg  = $isActive ? ($tc ? $tc['hex'] : '#4f46e5') : '#fff';
            $tabCol = $isActive ? '#fff' : '#475569';
            $border = $isActive ? 'none' : '1px solid #e2e8f0';
        ?>
        <a href="?type=<?= $type ?>"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;background:<?= $tabBg ?>;color:<?= $tabCol ?>;border:<?= $border ?>;white-space:nowrap;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <?= getAnnouncementTypeIcon($type === 'all' ? 'notice' : $type) ?>
            <?= $label ?>
            <span style="background:<?= $isActive ? 'rgba(255,255,255,.25)' : '#f1f5f9' ?>;color:<?= $isActive ? '#fff' : '#64748b' ?>;padding:0 7px;border-radius:20px;font-size:11px;font-weight:700;"><?= $cnt ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($announcements)): ?>
    <div class="sp-card">
        <div class="sp-card-body">
            <div class="sp-empty">
                <div class="ei">📢</div>
                <h4>No Announcements</h4>
                <p><?= $filterType !== 'all' ? 'No '.$typeLabels[$filterType].' at the moment.' : 'No announcements right now. Check back later.' ?></p>
                <?php if ($filterType !== 'all'): ?>
                <a href="announcements.php" style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">View all announcements →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php else: ?>

    <!-- Important announcements header -->
    <?php
    $importantAnns = array_filter($announcements, fn($a) => $a['is_important']);
    $normalAnns    = array_filter($announcements, fn($a) => !$a['is_important']);
    ?>

    <?php if (!empty($importantAnns)): ?>
    <div style="margin-bottom:20px;">
        <div style="font-size:12px;font-weight:700;color:#ef4444;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">
            🔴 Important Announcements
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($importantAnns as $ann):
            $tc = $typeColors[$ann['type']] ?? ['bg'=>'#f1f5f9','c'=>'#64748b','hex'=>'#64748b'];
        ?>
        <div style="background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(239,68,68,.12);border:2px solid #fca5a5;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#fef2f2,#fff5f5);padding:4px 16px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #fecaca;">
                <span style="color:#dc2626;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">🔴 Important</span>
                <span style="background:<?= $tc['bg'] ?>;color:<?= $tc['c'] ?>;padding:1px 8px;border-radius:20px;font-size:11px;font-weight:700;"><?= ucfirst($ann['type']) ?></span>
                <?php if ($ann['expiry_date']): ?>
                <span style="margin-left:auto;font-size:11px;color:#ef4444;font-weight:600;">Expires: <?= formatDate($ann['expiry_date']) ?></span>
                <?php endif; ?>
            </div>
            <div style="padding:16px 20px;display:flex;gap:14px;align-items:flex-start;">
                <div style="width:44px;height:44px;border-radius:10px;background:<?= $tc['bg'] ?>;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;"><?= getAnnouncementTypeIcon($ann['type']) ?></div>
                <div style="flex:1;">
                    <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;"><?= htmlspecialchars($ann['title']) ?></h3>
                    <p style="font-size:14px;color:#475569;margin:0;line-height:1.6;"><?= nl2br(htmlspecialchars($ann['content'])) ?></p>
                    <div style="margin-top:10px;font-size:12px;color:#94a3b8;"><?= timeAgo($ann['created_at']) ?> &bull; <?= formatDate($ann['created_at']) ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Normal announcements -->
    <?php if (!empty($normalAnns)): ?>
    <?php if (!empty($importantAnns)): ?>
    <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">All Announcements</div>
    <?php endif; ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
    <?php foreach ($normalAnns as $ann):
        $tc = $typeColors[$ann['type']] ?? ['bg'=>'#f1f5f9','c'=>'#64748b','hex'=>'#64748b'];
    ?>
    <div style="background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;border:1px solid #f1f5f9;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,.06)'">
        <div style="background:<?= $tc['bg'] ?>33;padding:3px 16px;display:flex;align-items:center;gap:8px;border-bottom:1px solid <?= $tc['bg'] ?>;">
            <span style="background:<?= $tc['bg'] ?>;color:<?= $tc['c'] ?>;padding:1px 8px;border-radius:20px;font-size:11px;font-weight:700;"><?= ucfirst($ann['type']) ?></span>
            <?php if ($ann['expiry_date']): ?>
            <span style="margin-left:auto;font-size:11px;color:#94a3b8;font-weight:600;">Expires: <?= formatDate($ann['expiry_date']) ?></span>
            <?php endif; ?>
        </div>
        <div style="padding:14px 18px;display:flex;gap:12px;align-items:flex-start;">
            <div style="width:38px;height:38px;border-radius:9px;background:<?= $tc['bg'] ?>;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"><?= getAnnouncementTypeIcon($ann['type']) ?></div>
            <div style="flex:1;">
                <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 6px;"><?= htmlspecialchars($ann['title']) ?></h3>
                <p style="font-size:13.5px;color:#475569;margin:0;line-height:1.6;"><?= nl2br(htmlspecialchars($ann['content'])) ?></p>
                <div style="margin-top:8px;font-size:12px;color:#94a3b8;"><?= timeAgo($ann['created_at']) ?> &bull; <?= formatDate($ann['created_at']) ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</main>
<?php require_once 'includes/footer.php'; ?>

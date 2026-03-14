<?php
require_once '../config.php';
requireStudentLogin();

$studentId  = (int)$_SESSION['student_id'];
$student    = getStudentById($studentId);
$pageTitle  = 'My Results';
$activePage = 'results';

// All results
$allResultsQ = mysqli_query($conn,
    "SELECT * FROM results WHERE student_id=$studentId ORDER BY exam_date DESC");
$allResults = mysqli_fetch_all($allResultsQ, MYSQLI_ASSOC);

// Overall stats
$totalTests = count($allResults);
$avgScore   = 0;
$bestScore  = 0;
$worstScore = 100;
if ($totalTests > 0) {
    $s = array_column($allResults, 'percentage');
    $avgScore   = round(array_sum($s) / count($s), 1);
    $bestScore  = round(max($s), 1);
    $worstScore = round(min($s), 1);
}
$overallGrade = getGrade($avgScore);

// Subject-wise performance (group by subject)
$subjectQ = mysqli_query($conn,
    "SELECT subject,
            COUNT(*) as total_tests,
            ROUND(AVG(marks_obtained/total_marks*100),1) as avg_pct,
            SUM(marks_obtained) as total_obtained,
            SUM(total_marks) as total_possible,
            MAX(marks_obtained/total_marks*100) as best_pct
     FROM results
     WHERE student_id=$studentId AND total_marks>0
     GROUP BY subject
     ORDER BY avg_pct DESC");
$subjectStats = mysqli_fetch_all($subjectQ, MYSQLI_ASSOC);

// Grade distribution
$gradeMap = ['A+'=>0,'A'=>0,'B+'=>0,'B'=>0,'C'=>0,'D'=>0,'F'=>0];
foreach ($allResults as $r) {
    $g = getGrade($r['percentage']);
    if (isset($gradeMap[$g])) $gradeMap[$g]++;
}

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
                <h1 class="sp-page-title">My Results</h1>
                <p class="sp-page-sub">Detailed exam performance &amp; subject analysis</p>
            </div>
        </div>
        <div class="sp-topbar-right">
            <a href="../results.php?sid=<?= urlencode($student['student_id']) ?>"
               target="_blank" class="btn btn-outline-primary btn-sm" style="text-decoration:none;">🔗 Public View</a>
            <div class="sp-topbar-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        </div>
    </div>

    <!-- Summary stats -->
    <div class="sp-stats" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
        <div class="sp-stat">
            <div class="sp-stat-icon blue">📋</div>
            <div><div class="sp-stat-val"><?= $totalTests ?></div><div class="sp-stat-label">Tests Appeared</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon green">📈</div>
            <div><div class="sp-stat-val"><?= $avgScore ?>%</div><div class="sp-stat-label">Avg. Score</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon amber">🏆</div>
            <div><div class="sp-stat-val"><?= $bestScore ?>%</div><div class="sp-stat-label">Best Score</div></div>
        </div>
        <div class="sp-stat">
            <div class="sp-stat-icon purple">🎯</div>
            <div><div class="sp-stat-val"><?= $overallGrade ?></div><div class="sp-stat-label">Overall Grade</div></div>
        </div>
    </div>

    <?php if ($totalTests === 0): ?>
    <div class="sp-card">
        <div class="sp-card-body">
            <div class="sp-empty">
                <div class="ei">📋</div>
                <h4>No Results Yet</h4>
                <p>Your exam results will appear here once your teacher adds them.</p>
            </div>
        </div>
    </div>

    <?php else: ?>

    <!-- Overall performance bar -->
    <div class="sp-card">
        <div class="sp-card-header"><span class="sp-card-title">📊 Overall Performance</span></div>
        <div class="sp-card-body">
            <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-bottom:6px;">
                        <span>0%</span><span>35% (Pass)</span><span>60%</span><span>80%</span><span>100%</span>
                    </div>
                    <div class="sp-bar-wrap" style="height:14px;">
                        <div class="sp-bar-fill" id="overallBar" data-w="<?= $avgScore ?>"
                             style="width:0%;background:<?= $avgScore>=80?'#10b981':($avgScore>=60?'#4f46e5':($avgScore>=35?'#f59e0b':'#ef4444')) ?>"></div>
                    </div>
                    <div style="margin-top:10px;display:flex;gap:16px;flex-wrap:wrap;">
                        <span style="font-size:13px;color:#64748b;">Best: <strong style="color:#059669;"><?= $bestScore ?>%</strong></span>
                        <span style="font-size:13px;color:#64748b;">Avg: <strong style="color:#4f46e5;"><?= $avgScore ?>%</strong></span>
                        <span style="font-size:13px;color:#64748b;">Lowest: <strong style="color:#ef4444;"><?= $worstScore ?>%</strong></span>
                    </div>
                </div>
                <div style="text-align:center;min-width:80px;">
                    <?php
                        $gc = $avgScore>=80?'gp-a':($avgScore>=70?'gp-b':($avgScore>=60?'gp-b':($avgScore>=50?'gp-c':($avgScore>=35?'gp-d':'gp-f'))));
                    ?>
                    <div style="font-size:52px;font-weight:900;color:<?= $avgScore>=80?'#059669':($avgScore>=60?'#4f46e5':($avgScore>=35?'#d97706':'#dc2626')) ?>;"><?= $overallGrade ?></div>
                    <div style="font-size:12px;color:#94a3b8;">Overall</div>
                </div>
            </div>

            <!-- Grade distribution -->
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Grade Distribution</div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <?php
                    $gradeStyles = [
                        'A+'=>['bg'=>'#d1fae5','c'=>'#059669'],
                        'A' =>['bg'=>'#d1fae5','c'=>'#059669'],
                        'B+'=>['bg'=>'#dbeafe','c'=>'#2563eb'],
                        'B' =>['bg'=>'#dbeafe','c'=>'#2563eb'],
                        'C' =>['bg'=>'#fef3c7','c'=>'#d97706'],
                        'D' =>['bg'=>'#ffedd5','c'=>'#ea580c'],
                        'F' =>['bg'=>'#fee2e2','c'=>'#dc2626'],
                    ];
                    foreach ($gradeMap as $grade => $count):
                        if ($count === 0) continue;
                        $gs = $gradeStyles[$grade] ?? ['bg'=>'#f1f5f9','c'=>'#64748b'];
                    ?>
                    <div style="background:<?= $gs['bg'] ?>;color:<?= $gs['c'] ?>;padding:6px 14px;border-radius:8px;text-align:center;min-width:52px;">
                        <div style="font-size:18px;font-weight:900;"><?= $grade ?></div>
                        <div style="font-size:11px;font-weight:700;"><?= $count ?> exam<?= $count > 1 ? 's' : '' ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise performance -->
    <?php if (!empty($subjectStats)): ?>
    <div class="sp-card">
        <div class="sp-card-header"><span class="sp-card-title">📚 Subject-wise Performance</span></div>
        <div class="sp-card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Tests</th>
                            <th>Total Marks</th>
                            <th>Performance</th>
                            <th>Avg Score</th>
                            <th>Best</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjectStats as $sub):
                            $pct    = (float)$sub['avg_pct'];
                            $barCol = $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#4f46e5' : ($pct >= 35 ? '#f59e0b' : '#ef4444'));
                            $gc     = $pct>=80?'gp-a':($pct>=70?'gp-b':($pct>=60?'gp-b':($pct>=50?'gp-c':($pct>=35?'gp-d':'gp-f'))));
                        ?>
                        <tr>
                            <td style="font-weight:700;color:#0f172a;"><?= htmlspecialchars($sub['subject']) ?></td>
                            <td><?= $sub['total_tests'] ?></td>
                            <td style="font-family:monospace;"><?= $sub['total_obtained'] ?>/<?= $sub['total_possible'] ?></td>
                            <td style="min-width:120px;">
                                <div class="sp-bar-wrap" style="height:6px;width:100px;">
                                    <div class="sp-bar-fill" style="width:<?= $pct ?>%;background:<?= $barCol ?>;"></div>
                                </div>
                            </td>
                            <td style="font-weight:700;color:<?= $barCol ?>;"><?= $pct ?>%</td>
                            <td style="color:#059669;font-weight:600;"><?= round($sub['best_pct'],1) ?>%</td>
                            <td><span class="gp <?= $gc ?>"><?= getGrade($pct) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Full results table -->
    <div class="sp-card">
        <div class="sp-card-header">
            <span class="sp-card-title">📋 All Exam Results (<?= $totalTests ?>)</span>
            <div style="display:flex;gap:8px;align-items:center;">
                <input type="text" id="resultSearch" placeholder="🔍 Search exam / subject..."
                       style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;width:200px;"
                       oninput="filterResults(this.value)">
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="sp-table" id="resultsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Score</th>
                        <th>Grade</th>
                        <th>Class Rank</th>
                        <th>Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="resultsTbody">
                    <?php foreach ($allResults as $i => $r):
                        $pct    = round((float)$r['percentage'], 1);
                        $barCol = $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#4f46e5' : ($pct >= 35 ? '#f59e0b' : '#ef4444'));
                        $gc     = $pct>=80?'gp-a':($pct>=70?'gp-b':($pct>=60?'gp-b':($pct>=50?'gp-c':($pct>=35?'gp-d':'gp-f'))));
                    ?>
                    <tr data-search="<?= strtolower(htmlspecialchars($r['exam_name'].' '.$r['subject'])) ?>">
                        <td style="color:#94a3b8;font-size:12px;"><?= $i + 1 ?></td>
                        <td style="font-weight:700;color:#0f172a;max-width:180px;">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($r['exam_name']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($r['subject']) ?></td>
                        <td style="font-family:monospace;font-weight:600;"><?= $r['marks_obtained'] ?>/<?= $r['total_marks'] ?></td>
                        <td style="min-width:130px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="sp-bar-wrap" style="width:70px;height:5px;">
                                    <div class="sp-bar-fill" style="width:<?= $pct ?>%;background:<?= $barCol ?>;"></div>
                                </div>
                                <span style="font-weight:700;font-size:13px;color:<?= $barCol ?>;"><?= $pct ?>%</span>
                            </div>
                        </td>
                        <td><span class="gp <?= $gc ?>"><?= getGrade($r['percentage']) ?></span></td>
                        <td style="color:#64748b;"><?= $r['rank_in_class'] ? '#'.$r['rank_in_class'] : '—' ?></td>
                        <td style="color:#64748b;white-space:nowrap;"><?= formatDate($r['exam_date']) ?></td>
                        <td style="color:#64748b;font-size:13px;max-width:160px;">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= htmlspecialchars($r['remarks'] ?? '') ?>">
                                <?= htmlspecialchars($r['remarks'] ?: '—') ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="noResultsMsg" style="display:none;" class="sp-empty"><div class="ei">🔍</div><p>No results match your search.</p></div>
    </div>

    <?php endif; ?>

</main>
<?php
$extraScript = <<<JS
// Animate overall performance bar
window.addEventListener('load', function(){
    var bar = document.getElementById('overallBar');
    if (bar) setTimeout(function(){ bar.style.width = bar.dataset.w + '%'; }, 300);
});
// Live search filter
function filterResults(q) {
    q = q.toLowerCase().trim();
    var rows = document.querySelectorAll('#resultsTbody tr');
    var visible = 0;
    rows.forEach(function(row) {
        var match = !q || row.dataset.search.includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    var msg = document.getElementById('noResultsMsg');
    if (msg) msg.style.display = visible === 0 && q ? 'block' : 'none';
}
JS;
require_once 'includes/footer.php';
?>

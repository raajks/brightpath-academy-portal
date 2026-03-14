<?php
require_once 'config.php';
$pageTitle = 'Results & Toppers';

// Get toppers from DB
$toppers = getToppers(12);

// If student lookup submitted
$studentResults = [];
$lookupStudent  = null;
$lookupError    = '';

if (isset($_POST['lookup']) || isset($_GET['sid'])) {
    $searchId = sanitize($_POST['student_id'] ?? $_GET['sid'] ?? '');
    if ($searchId) {
        // Look up by student_id or name
        $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id=? OR name LIKE ? LIMIT 1");
        $like = "%$searchId%";
        mysqli_stmt_bind_param($stmt, 'ss', $searchId, $like);
        mysqli_stmt_execute($stmt);
        $lookupStudent = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($lookupStudent) {
            $sid = $lookupStudent['id'];
            $rRes = mysqli_query($conn, "SELECT * FROM results WHERE student_id=$sid ORDER BY exam_date DESC");
            $studentResults = mysqli_fetch_all($rRes, MYSQLI_ASSOC);
            if (empty($studentResults)) {
                $lookupError = 'No results found for this student yet. Please check with admin.';
            }
        } else {
            $lookupError = 'No student found with this ID or name. Please check with admin.';
        }
    } else {
        $lookupError = 'Please enter a valid Student ID or Name.';
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <span class="current">Results & Toppers</span>
        </div>
        <h1>Results & Hall of Fame</h1>
        <p>Check your results and celebrate the achievements of our star students.</p>
    </div>
</section>

<!-- Result Lookup Box -->
<section class="section-sm" style="background:var(--light);">
    <div class="container">
        <div class="results-search-box animate-on-scroll">
            <div style="text-align:center;margin-bottom:28px;">
                <div style="font-size:48px;margin-bottom:10px;">🔍</div>
                <h2 style="margin-bottom:8px;">Check Your Results</h2>
                <p style="color:var(--text-muted);">Enter your Student ID (given at enrollment) or your name to view your results.</p>
            </div>

            <?php if ($lookupError): ?>
                <div class="alert alert-error"><span class="alert-icon">✕</span><?= htmlspecialchars($lookupError) ?></div>
            <?php endif; ?>

            <form method="POST" id="resultsForm" style="display:flex;gap:12px;flex-wrap:wrap;">
                <input type="text" name="student_id" class="form-control" 
                       placeholder="Enter Student ID (e.g. BPA20240001) or Name..."
                       value="<?= htmlspecialchars($_POST['student_id'] ?? $_GET['sid'] ?? '') ?>"
                       style="flex:1;min-width:200px;" required>
                <button type="submit" name="lookup" class="btn btn-primary" style="padding:12px 28px;">
                    🔍 Search Results
                </button>
            </form>
            <p style="font-size:12.5px;color:var(--text-muted);text-align:center;margin-top:12px;">
                Don't have your Student ID? <a href="contact.php" style="color:var(--primary);">Contact the office</a>
            </p>
        </div>

        <!-- Results Display -->
        <?php if ($lookupStudent && !empty($studentResults)): ?>
        <div class="result-card animate-on-scroll" style="margin-top:30px;max-width:700px;margin-left:auto;margin-right:auto;">
            <div class="result-card-header">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:60px;height:60px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:var(--white);">
                        <?= strtoupper(substr($lookupStudent['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h3 style="color:var(--white);font-size:20px;margin-bottom:2px;"><?= htmlspecialchars($lookupStudent['name']) ?></h3>
                        <div style="color:rgba(255,255,255,0.75);font-size:14px;">
                            ID: <?= htmlspecialchars($lookupStudent['student_id']) ?> • 
                            Class <?= htmlspecialchars($lookupStudent['class_level']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="result-card-body">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalPct = 0;
                            foreach ($studentResults as $r): 
                                $pct = $r['percentage'];
                                $totalPct += $pct;
                                $gradeClass = getGradeClass($pct);
                            ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($r['exam_name']) ?></td>
                                <td><?= htmlspecialchars($r['subject']) ?></td>
                                <td><?= $r['marks_obtained'] ?>/<?= $r['total_marks'] ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <span class="<?= $gradeClass ?>"><?= number_format($pct, 1) ?>%</span>
                                    </div>
                                    <div class="progress-bar" style="width:100px;margin-top:6px;">
                                        <div class="progress-fill <?= $pct >= 80 ? 'high' : ($pct >= 50 ? 'medium' : 'low') ?>" 
                                             data-width="<?= $pct ?>" style="width:0%"></div>
                                    </div>
                                </td>
                                <td><span class="<?= $gradeClass ?>" style="font-size:16px;"><?= getGrade($pct) ?></span></td>
                                <td><?= formatDate($r['exam_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php 
                $avgPct = count($studentResults) > 0 ? round($totalPct / count($studentResults), 1) : 0;
                ?>
                <div style="background:var(--primary-pale);border-radius:var(--radius);padding:18px;margin-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <div style="font-size:13px;color:var(--text-muted);">Overall Average</div>
                        <div style="font-size:26px;font-weight:800;color:var(--primary);font-family:var(--font-heading);"><?= $avgPct ?>%</div>
                    </div>
                    <div>
                        <div style="font-size:13px;color:var(--text-muted);">Overall Grade</div>
                        <div style="font-size:26px;font-weight:800;font-family:var(--font-heading);" class="<?= getGradeClass($avgPct) ?>"><?= getGrade($avgPct) ?></div>
                    </div>
                    <div>
                        <div style="font-size:13px;color:var(--text-muted);">Total Exams</div>
                        <div style="font-size:26px;font-weight:800;color:var(--dark);font-family:var(--font-heading);"><?= count($studentResults) ?></div>
                    </div>
                    <?php if ($lookupStudent && $lookupStudent['id'] && isStudentLoggedIn() && $_SESSION['student_id'] == $lookupStudent['id']): ?>
                    <a href="<?= SITE_URL ?>/student/dashboard.php" class="btn btn-primary btn-sm">View Full Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Toppers Section -->
<?php if (!empty($toppers)): ?>
<section class="section toppers-section">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">Hall of Fame</span>
            <h2>Our <span class="text-primary">Star Achievers</span></h2>
            <p>Students who made us proud with their outstanding results and achievements.</p>
            <div class="divider"></div>
        </div>

        <div class="toppers-grid">
            <?php foreach ($toppers as $i => $topper): ?>
            <div class="topper-card animate-on-scroll delay-<?= ($i % 4) + 1 ?>">
                <div class="topper-avatar" style="position:relative;">
                    <?= strtoupper(substr($topper['student_name'], 0, 1)) ?>
                    <div class="topper-rank" style="position:absolute;"><?= $i + 1 ?></div>
                </div>
                <div class="topper-name"><?= htmlspecialchars($topper['student_name']) ?></div>
                <div class="topper-achievement"><?= htmlspecialchars($topper['exam_name']) ?></div>
                <div class="topper-score"><?= htmlspecialchars($topper['score']) ?></div>
                <?php if ($topper['class_level']): ?>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;"><?= htmlspecialchars($topper['class_level']) ?></div>
                <?php endif; ?>
                <div class="topper-year">Batch <?= htmlspecialchars($topper['year']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>Your Name Could Be Here Next!</h2>
        <p>Join BrightPath Academy and write your success story.</p>
        <div class="cta-actions">
            <a href="admissions.php" class="btn btn-secondary btn-lg">Enroll Now</a>
            <a href="courses.php" class="btn btn-outline btn-lg">View Courses</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

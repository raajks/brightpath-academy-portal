<?php
// ============================================
// BrightPath Academy - Configuration File
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'brightpath_academy');

// Site Configuration
define('SITE_NAME', 'BrightPath Academy');
define('SITE_TAGLINE', 'Shaping Futures, Building Champions');
define('SITE_URL', 'http://localhost/Project/Coaching_Center');
define('SITE_EMAIL', 'info@brightpathacademy.com');
define('SITE_PHONE', '+91 98765 43210');
define('SITE_PHONE2', '+91 87654 32109');
define('SITE_ADDRESS', '123, Knowledge Park, Sector 15, New Delhi - 110001');
define('SITE_WHATSAPP', '919876543210');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Database Connection — auto-creates DB and schema on first run
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$conn) {
    die('<div style="font-family:sans-serif;text-align:center;padding:50px;background:#fee2e2;color:#dc2626;border-radius:8px;margin:20px;">
        <h2>&#9888; Database Connection Error</h2>
        <p>Could not connect to MySQL. Make sure XAMPP is running and MySQL is started.</p>
        <p><small>' . mysqli_connect_error() . '</small></p>
    </div>');
}
mysqli_set_charset($conn, 'utf8mb4');

// Create database if it doesn't exist
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, DB_NAME);

// Auto-import schema if tables don't exist yet
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'admins'");
if (mysqli_num_rows($tableCheck) === 0) {
    $sqlFile = __DIR__ . '/database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        // Remove USE statement (already selected DB) and split into statements
        $sql = preg_replace('/^\s*USE\s+\S+;\s*$/im', '', $sql);
        $sql = preg_replace('/^\s*CREATE DATABASE[^;]+;\s*$/im', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                mysqli_query($conn, $statement);
            }
        }
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isStudentLoggedIn() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        redirect(SITE_URL . '/student/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function formatCurrency($amount) {
    return '₹' . number_format($amount, 0, '.', ',');
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return "Just now";
    elseif ($diff < 3600) return floor($diff / 60) . " min ago";
    elseif ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    elseif ($diff < 2592000) return floor($diff / 86400) . " days ago";
    else return date("d M Y", $timestamp);
}

function getGrade($percentage) {
    if ($percentage >= 90) return 'A+';
    elseif ($percentage >= 80) return 'A';
    elseif ($percentage >= 70) return 'B+';
    elseif ($percentage >= 60) return 'B';
    elseif ($percentage >= 50) return 'C';
    elseif ($percentage >= 35) return 'D';
    else return 'F';
}

function getGradeClass($percentage) {
    if ($percentage >= 80) return 'grade-excellent';
    elseif ($percentage >= 60) return 'grade-good';
    elseif ($percentage >= 40) return 'grade-average';
    else return 'grade-poor';
}

function generateStudentId() {
    global $conn;
    $year = date('Y');
    $prefix = 'BPA' . $year;
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students WHERE student_id LIKE '$prefix%'");
    $row = mysqli_fetch_assoc($result);
    $num = str_pad($row['count'] + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $num;
}

function getActiveAnnouncements($limit = 5) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM announcements WHERE status='active' AND (expiry_date IS NULL OR expiry_date >= CURDATE()) ORDER BY is_important DESC, created_at DESC LIMIT $limit");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getActiveCourses($limit = NULL) {
    global $conn;
    $lim = $limit ? "LIMIT $limit" : "";
    $result = mysqli_query($conn, "SELECT * FROM courses WHERE status='active' ORDER BY is_popular DESC, created_at DESC $lim");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getFeaturedTestimonials($limit = 6) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM testimonials WHERE status='active' AND is_featured=1 ORDER BY created_at DESC LIMIT $limit");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getToppers($limit = 6) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM toppers ORDER BY CAST(SUBSTRING(year, 1, 4) AS UNSIGNED) DESC, id ASC LIMIT $limit");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function showAlert($message, $type = 'success') {
    $icons = ['success' => '✓', 'error' => '✕', 'warning' => '⚠', 'info' => 'ℹ'];
    $icon = $icons[$type] ?? 'ℹ';
    return "<div class='alert alert-{$type}'><span class='alert-icon'>{$icon}</span>{$message}</div>";
}

function getCourseById($id) {
    global $conn;
    $id = (int)$id;
    $result = mysqli_query($conn, "SELECT * FROM courses WHERE id=$id AND status='active'");
    return mysqli_fetch_assoc($result);
}

function getStudentById($id) {
    global $conn;
    $id = (int)$id;
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
    return mysqli_fetch_assoc($result);
}

function getAnnouncementTypeIcon($type) {
    $icons = [
        'notice' => '📌',
        'event' => '🎉',
        'result' => '🏆',
        'holiday' => '🎊',
        'exam' => '📝',
        'admission' => '🎓'
    ];
    return $icons[$type] ?? '📌';
}

function getAnnouncementTypeColor($type) {
    $colors = [
        'notice' => 'badge-notice',
        'event' => 'badge-event',
        'result' => 'badge-result',
        'holiday' => 'badge-holiday',
        'exam' => 'badge-exam',
        'admission' => 'badge-admission'
    ];
    return $colors[$type] ?? 'badge-notice';
}
?>

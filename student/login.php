<?php
require_once '../config.php';
$pageTitle = 'Student Login';

if (isStudentLoggedIn()) redirect(SITE_URL . '/student/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE email=? AND status='active' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id']   = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_sid']  = $student['student_id'];
            $redir = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : SITE_URL . '/student/dashboard.php';
            redirect($redir);
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .student-auth-page { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); }
    </style>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>

<div class="auth-page student-auth-page">
    <a href="../index.php" style="position:fixed;top:24px;left:24px;color:rgba(255,255,255,0.7);display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;z-index:10;text-decoration:none;">
        ← Back to Website
    </a>

    <div class="auth-card animate-on-scroll" style="max-width:440px;">
        <div class="auth-header">
            <div class="logo">🎓</div>
            <h2>Student Login</h2>
            <p>Welcome back! Sign in to your student portal.</p>
        </div>

        <div class="auth-body">
            <?php if ($error): ?>
                <div class="alert alert-error" data-dismiss="5000">
                    <span class="alert-icon">✕</span><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" novalidate>
                <div class="form-group">
                    <label class="form-label">Email Address <span>*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="your.email@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex;justify-content:space-between;">
                        Password <span>*</span>
                        <a href="../contact.php" style="color:var(--primary);font-size:13px;font-weight:600;">Forgot password?</a>
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="passwordField" class="form-control"
                               placeholder="Your password" required autocomplete="current-password"
                               style="padding-right:48px;">
                        <button type="button" class="toggle-password" data-target="#passwordField"
                                style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:18px;">👁</button>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                        <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--primary);">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
                    🔓 Sign In to Dashboard
                </button>
            </form>

            <div class="auth-switch">
                Don't have an account? <a href="register.php">Register here →</a>
            </div>
            <div class="auth-switch" style="margin-top:8px;">
                <a href="../admin/login.php" style="color:var(--text-muted);font-size:13px;">Admin? Login here</a>
            </div>
        </div>
    </div>
</div>

<script src="../js/script.js"></script>
</body>
</html>

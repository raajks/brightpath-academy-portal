<?php
session_start();
define('ADMIN_PAGE', true);
require_once '../config.php';

if (isAdminLoggedIn()) redirect(SITE_URL . '/admin/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE email=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin  = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            redirect(SITE_URL . '/admin/index.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>

<div class="auth-page admin-auth-page">
    <div class="auth-card animate-on-scroll" style="max-width:420px;">
        <div class="auth-header" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);">
            <div class="logo" style="font-size:48px;">⚙️</div>
            <h2 style="color:#fff;">Admin Portal</h2>
            <p style="color:rgba(255,255,255,0.6);">Authorized personnel only.</p>
        </div>
        <div class="auth-body">
            <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">✕</span><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label class="form-label">Admin Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@brightpath.com" required autocomplete="username"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="adminPass" class="form-control"
                               placeholder="Enter password" required autocomplete="current-password"
                               style="padding-right:48px;">
                        <button type="button" class="toggle-password" data-target="#adminPass"
                                style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;">👁</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;background:linear-gradient(135deg,#0f172a,#334155);">
                    🔐 Login to Admin Panel
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-muted);">
                <a href="<?= SITE_URL ?>/index.php" style="color:var(--primary);">← Back to Website</a>
            </div>
        </div>
    </div>
</div>

<script src="../js/script.js"></script>
</body>
</html>

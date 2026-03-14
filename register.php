<?php
require_once 'config.php';
redirect(SITE_URL . '/student/register.php');


$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $email       = sanitize($_POST['email'] ?? '');
    $phone       = sanitize($_POST['phone'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';
    $classLevel  = sanitize($_POST['class_level'] ?? '');
    $parentName  = sanitize($_POST['parent_name'] ?? '');
    $parentPhone = sanitize($_POST['parent_phone'] ?? '');
    $dob         = sanitize($_POST['dob'] ?? '');
    $gender      = sanitize($_POST['gender'] ?? '');

    if (!$name || !$email || !$phone || !$password || !$classLevel) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Phone number must be exactly 10 digits.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPass) {
        $error = 'Passwords do not match.';
    } else {
        // Check email exists
        $check = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'This email is already registered. Please login or use a different email.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $studentId      = generateStudentId();

            $stmt = mysqli_prepare($conn, "INSERT INTO students (student_id, name, email, phone, password, class_level, parent_name, parent_phone, dob, gender) VALUES (?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssssssssss',
                $studentId, $name, $email, $phone, $hashedPassword, 
                $classLevel, $parentName, $parentPhone, $dob, $gender
            );
            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                $_SESSION['student_id']   = $newId;
                $_SESSION['student_name'] = $name;
                $_SESSION['student_sid']  = $studentId;
                redirect(SITE_URL . '/dashboard.php?welcome=1');
            } else {
                $error = 'Registration failed. Please try again.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>

<div class="auth-page" style="align-items:flex-start;padding-top:40px;">
    <a href="index.php" style="position:fixed;top:24px;left:24px;color:rgba(255,255,255,0.7);display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;z-index:10;">
        ← Back to Home
    </a>

    <div class="auth-card animate-on-scroll" style="max-width:600px;">
        <div class="auth-header">
            <div class="logo">🎓</div>
            <h2>Create Account</h2>
            <p>Join <?= SITE_NAME ?> and track your academic journey.</p>
        </div>

        <div class="auth-body">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">✕</span><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" novalidate>
                <!-- Student Info -->
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.06em;margin-bottom:16px;">
                    👨‍🎓 Student Information
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name <span>*</span></label>
                        <input type="text" name="name" class="form-control" 
                               placeholder="Student's full name" required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Class <span>*</span></label>
                        <select name="class_level" class="form-control" required>
                            <option value="">Select class</option>
                            <?php foreach (['6','7','8','9','10','11 (Science)','11 (Commerce)','12 (Science)','12 (Commerce)'] as $cl): ?>
                                <option value="<?= $cl ?>" <?= ($_POST['class_level'] ?? '') === $cl ? 'selected' : '' ?>><?= $cl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select gender</option>
                            <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Parent Info -->
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.06em;margin-bottom:16px;margin-top:8px;">
                    👨‍👩‍👧 Parent / Guardian
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Parent/Guardian Name</label>
                        <input type="text" name="parent_name" class="form-control" 
                               placeholder="Parent's full name"
                               value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent Phone</label>
                        <input type="tel" name="parent_phone" class="form-control" 
                               placeholder="Parent's phone number"
                               value="<?= htmlspecialchars($_POST['parent_phone'] ?? '') ?>">
                    </div>
                </div>

                <!-- Account Info -->
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.06em;margin-bottom:16px;margin-top:8px;">
                    🔐 Account Credentials
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address <span>*</span></label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="email@example.com (for login)" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number <span>*</span></label>
                        <input type="tel" name="phone" class="form-control" 
                               placeholder="10-digit number" required
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password <span>*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="passField" class="form-control" 
                                   placeholder="Min. 6 characters" required style="padding-right:48px;">
                            <button type="button" class="toggle-password" data-target="#passField"
                                    style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;">👁</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span>*</span></label>
                        <input type="password" name="confirm_password" class="form-control" 
                               placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:8px;">
                    🚀 Create Account
                </button>
            </form>

            <div class="auth-switch">
                Already have an account? <a href="login.php">Login here →</a>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>

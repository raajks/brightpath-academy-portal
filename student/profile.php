<?php
require_once '../config.php';
requireStudentLogin();

$studentId = (int)$_SESSION['student_id'];
$student   = getStudentById($studentId);
$pageTitle  = 'My Profile';
$activePage = 'profile';

$successMsg = '';
$errorMsg   = '';

// -- Handle profile update --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_profile') {
        $phone        = trim(mysqli_real_escape_string($conn, strip_tags($_POST['phone'] ?? '')));
        $gender       = in_array($_POST['gender'] ?? '', ['male','female','other','']) ? ($_POST['gender'] ?? '') : '';
        $dob          = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['dob'] ?? '') ? $_POST['dob'] : null;
        $parent_name  = trim(mysqli_real_escape_string($conn, strip_tags($_POST['parent_name']  ?? '')));
        $parent_phone = trim(mysqli_real_escape_string($conn, strip_tags($_POST['parent_phone'] ?? '')));
        $school_name  = trim(mysqli_real_escape_string($conn, strip_tags($_POST['school_name']  ?? '')));
        $address      = trim(mysqli_real_escape_string($conn, strip_tags($_POST['address']      ?? '')));

        if (!preg_match('/^\d{10}$/', $phone)) {
            $errorMsg = 'Phone number must be exactly 10 digits.';
        } else {
            $dobSql = $dob ? "'$dob'" : 'NULL';
            $genSql = $gender ? "'$gender'" : 'NULL';
            $upd = mysqli_query($conn,
                "UPDATE students SET
                    phone='$phone', gender=$genSql, dob=$dobSql,
                    parent_name='$parent_name', parent_phone='$parent_phone',
                    school_name='$school_name', address='$address'
                 WHERE id=$studentId");
            if ($upd) {
                $successMsg = 'Profile updated successfully!';
                $student    = getStudentById($studentId); // refresh
            } else {
                $errorMsg = 'Update failed. Please try again.';
            }
        }
    }

    elseif ($_POST['action'] === 'change_password') {
        $current    = $_POST['current_password']  ?? '';
        $newPass    = $_POST['new_password']       ?? '';
        $confirmNew = $_POST['confirm_password']   ?? '';

        if (!password_verify($current, $student['password'])) {
            $errorMsg = 'Current password is incorrect.';
        } elseif (strlen($newPass) < 6) {
            $errorMsg = 'New password must be at least 6 characters.';
        } elseif ($newPass !== $confirmNew) {
            $errorMsg = 'New passwords do not match.';
        } else {
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            $escaped = mysqli_real_escape_string($conn, $hash);
            $upd = mysqli_query($conn, "UPDATE students SET password='$escaped' WHERE id=$studentId");
            if ($upd) {
                $successMsg = 'Password changed successfully!';
            } else {
                $errorMsg = 'Failed to change password. Please try again.';
            }
        }
    }
}

// Active tab
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['view','edit','password']) ? $_GET['tab'] : 'view';

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
                <h1 class="sp-page-title">My Profile</h1>
                <p class="sp-page-sub">View and manage your account information</p>
            </div>
        </div>
        <div class="sp-topbar-right">
            <div class="sp-topbar-avatar"><?= strtoupper(substr($student['name'], 0, 2)) ?></div>
        </div>
    </div>

    <?php if ($successMsg): ?>
    <div class="sp-alert success">✅ <?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
    <div class="sp-alert error">❌ <?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <!-- Profile header card -->
    <div class="sp-card" style="margin-bottom:20px;">
        <div class="sp-card-body" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding:24px;">
            <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:26px;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <?= strtoupper(substr($student['name'], 0, 2)) ?>
            </div>
            <div style="flex:1;">
                <div style="font-size:20px;font-weight:900;color:#0f172a;"><?= htmlspecialchars($student['name']) ?></div>
                <div style="font-size:13px;color:#4f46e5;font-family:monospace;font-weight:700;margin-top:2px;"><?= htmlspecialchars($student['student_id']) ?></div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px;">
                    <span class="sp-pill <?= $student['status']==='active'?'green':'red' ?>"><?= ucfirst($student['status']) ?></span>
                    <?php if ($student['class_level']): ?>
                    <span class="sp-pill blue"><?= htmlspecialchars($student['class_level']) ?></span>
                    <?php endif; ?>
                    <?php if ($student['gender']): ?>
                    <span class="sp-pill gray"><?= ucfirst($student['gender']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="font-size:12px;color:#94a3b8;text-align:right;">
                Member since<br>
                <strong style="color:#0f172a;font-size:14px;"><?= formatDate($student['created_at']) ?></strong>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div style="display:flex;gap:4px;border-bottom:2px solid #e2e8f0;margin-bottom:20px;overflow-x:auto;">
        <?php
        $tabs = ['view'=>'👁️ View Profile','edit'=>'✏️ Edit Profile','password'=>'🔒 Change Password'];
        foreach ($tabs as $t => $label):
            $active = $tab === $t;
        ?>
        <a href="?tab=<?= $t ?>"
           style="padding:10px 18px;font-size:14px;font-weight:<?= $active?'700':'600' ?>;text-decoration:none;color:<?= $active?'#4f46e5':'#64748b' ?>;border-bottom:2px solid <?= $active?'#4f46e5':'transparent' ?>;margin-bottom:-2px;white-space:nowrap;transition:all .2s;"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- -- VIEW TAB -- -->
    <?php if ($tab === 'view'): ?>
    <div class="sp-card">
        <div class="sp-card-header"><span class="sp-card-title">📋 Profile Details</span></div>
        <div class="sp-card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;">

                <?php
                $fields = [
                    ['Full Name',      $student['name'],         'person'],
                    ['Student ID',     $student['student_id'],   'id'],
                    ['Email Address',  $student['email'],        'email'],
                    ['Phone Number',   $student['phone'],        'phone'],
                    ['Class / Level',  $student['class_level'],  'level'],
                    ['Gender',         $student['gender'] ? ucfirst($student['gender']) : null, 'gender'],
                    ['Date of Birth',  $student['dob'] ? formatDate($student['dob']) : null, 'dob'],
                    ['School / Prev.', $student['school_name'],  'school'],
                    ['Parent / Guardian', $student['parent_name'], 'parent'],
                    ['Parent Phone',   $student['parent_phone'],  'pphone'],
                    ['Address',        $student['address'],        'addr'],
                ];
                foreach ($fields as [$label, $val, $key]):
                    if (!$val) continue;
                ?>
                <div style="padding:12px 0;<?= $key==='addr'?'grid-column:1/-1':'' ?>">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;"><?= $label ?></div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;<?= $key==='id'?'font-family:monospace;color:#4f46e5;':'' ?>"><?= htmlspecialchars($val) ?></div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <!-- Account info -->
    <div class="sp-card">
        <div class="sp-card-header"><span class="sp-card-title">📞 Account Information</span></div>
        <div class="sp-card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Email (Login)</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;"><?= htmlspecialchars($student['email']) ?></div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Account Status</div>
                    <span class="sp-pill <?= $student['status']==='active'?'green':'red' ?>"><?= ucfirst($student['status']) ?></span>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Member Since</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;"><?= formatDate($student['created_at']) ?></div>
                </div>
            </div>
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f1f5f9;display:flex;gap:10px;flex-wrap:wrap;">
                <a href="?tab=edit"     class="btn btn-primary btn-sm" style="text-decoration:none;">✏️ Edit Profile</a>
                <a href="?tab=password" class="btn btn-outline-primary btn-sm" style="text-decoration:none;">🔒 Change Password</a>
            </div>
        </div>
    </div>

    <!-- -- EDIT TAB -- -->
    <?php elseif ($tab === 'edit'): ?>
    <div class="sp-card">
        <div class="sp-card-header"><span class="sp-card-title">✏️ Edit Profile</span></div>
        <div class="sp-card-body">
            <form method="POST" action="?tab=edit" novalidate>
                <input type="hidden" name="action" value="update_profile">

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:20px;">

                    <!-- Read-only fields -->
                    <div class="form-group">
                        <label class="form-label">Full Name <span style="color:#94a3b8;font-size:11px;">(read-only)</span></label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" disabled style="background:#f8fafc;cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Student ID <span style="color:#94a3b8;font-size:11px;">(read-only)</span></label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_id']) ?>" disabled style="background:#f8fafc;cursor:not-allowed;font-family:monospace;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:#94a3b8;font-size:11px;">(read-only)</span></label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" disabled style="background:#f8fafc;cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Class / Level <span style="color:#94a3b8;font-size:11px;">(read-only)</span></label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['class_level'] ?? '') ?>" disabled style="background:#f8fafc;cursor:not-allowed;">
                    </div>

                    <!-- Editable fields -->
                    <div class="form-group">
                        <label class="form-label">Phone Number <span style="color:#ef4444;">*</span></label>
                        <input type="tel" name="phone" class="form-control"
                               value="<?= htmlspecialchars($student['phone']) ?>"
                               placeholder="10-digit mobile number" maxlength="10" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <?php foreach (['male'=>'Male','female'=>'Female','other'=>'Other'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $student['gender']===$v?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control"
                               value="<?= htmlspecialchars($student['dob'] ?? '') ?>"
                               max="<?= date('Y-m-d', strtotime('-5 years')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">School / Previous School</label>
                        <input type="text" name="school_name" class="form-control"
                               value="<?= htmlspecialchars($student['school_name'] ?? '') ?>"
                               placeholder="e.g. ABC Public School" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent / Guardian Name</label>
                        <input type="text" name="parent_name" class="form-control"
                               value="<?= htmlspecialchars($student['parent_name'] ?? '') ?>"
                               placeholder="Parent or guardian name" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent / Guardian Phone</label>
                        <input type="tel" name="parent_phone" class="form-control"
                               value="<?= htmlspecialchars($student['parent_phone'] ?? '') ?>"
                               placeholder="Parent phone number" maxlength="15">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"
                                  placeholder="Your full address"><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;padding-top:4px;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <a href="?tab=view" class="btn btn-outline-primary" style="text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- -- PASSWORD TAB -- -->
    <?php elseif ($tab === 'password'): ?>
    <div class="sp-card" style="max-width:480px;">
        <div class="sp-card-header"><span class="sp-card-title">🔒 Change Password</span></div>
        <div class="sp-card-body">
            <form method="POST" action="?tab=password" novalidate>
                <input type="hidden" name="action" value="change_password">

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Current Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="current_password" class="form-control"
                           placeholder="Enter your current password" required>
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">New Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="new_password" class="form-control"
                           placeholder="At least 6 characters" minlength="6" required>
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Confirm New Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="confirm_password" class="form-control"
                           placeholder="Re-enter new password" required>
                </div>

                <div style="background:#f8fafc;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#475569;">
                    <strong>💡 Password tips:</strong>
                    <ul style="margin:6px 0 0 16px;padding:0;line-height:1.8;">
                        <li>At least 6 characters</li>
                        <li>Use a mix of letters, numbers &amp; symbols</li>
                        <li>Don't share your password with anyone</li>
                    </ul>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">🔒 Update Password</button>
                    <a href="?tab=view" class="btn btn-outline-primary" style="text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</main>
<?php require_once 'includes/footer.php'; ?>


<?php
require_once 'config.php';
$pageTitle = 'Admissions';

$courses = getActiveCourses();

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = sanitize($_POST['student_name'] ?? '');
    $parentName  = sanitize($_POST['parent_name'] ?? '');
    $email       = sanitize($_POST['email'] ?? '');
    $phone       = sanitize($_POST['phone'] ?? '');
    $classLevel  = sanitize($_POST['class_level'] ?? '');
    $courseId    = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $school      = sanitize($_POST['school_name'] ?? '');
    $message     = sanitize($_POST['message'] ?? '');

    if (!$studentName || !$parentName || !$email || !$phone || !$classLevel) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Please enter a valid 10-digit phone number.';
    } else {
        // Check existing
        $check = mysqli_query($conn, "SELECT id FROM admission_inquiries WHERE email='$email' AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)");
        if (mysqli_num_rows($check) > 0) {
            $error = 'An inquiry with this email was recently submitted. We will contact you soon!';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO admission_inquiries (student_name, parent_name, email, phone, class_level, course_id, school_name, message) VALUES (?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'sssssisss',
                $studentName, $parentName, $email, $phone, $classLevel, $courseId, $school, $message);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Thank you! Your admission inquiry has been submitted successfully. Our counselor will call you within 24 hours.";
                $_POST = []; // clear form
            } else {
                $error = 'Something went wrong. Please try again or call us directly.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$selectedCourse = isset($_GET['course']) ? (int)$_GET['course'] : 0;

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <span class="current">Admissions</span>
        </div>
        <h1>Apply for Admission</h1>
        <p>Fill the form below and our counselor will contact you within 24 hours.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Process Steps -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:56px;" class="animate-on-scroll">
            <?php
            $steps = [
                ['num'=>'01','icon'=>'📝','title'=>'Fill the Form','desc'=>'Submit your admission inquiry online'],
                ['num'=>'02','icon'=>'📞','title'=>'Counselor Calls','desc'=>'Our team will call you within 24 hours'],
                ['num'=>'03','icon'=>'🎓','title'=>'Demo Class','desc'=>'Attend a free demo class for your child'],
                ['num'=>'04','icon'=>'✅','title'=>'Enroll & Start','desc'=>'Complete enrollment and begin learning!'],
            ];
            foreach ($steps as $i => $step): ?>
            <div class="card animate-on-scroll delay-<?= $i + 1 ?>" style="text-align:center;">
                <div style="width:44px;height:44px;background:var(--gradient);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:var(--white);margin:0 auto 12px;">
                    <?= $step['num'] ?>
                </div>
                <div style="font-size:28px;margin-bottom:10px;"><?= $step['icon'] ?></div>
                <h4 style="margin-bottom:6px;"><?= $step['title'] ?></h4>
                <p style="font-size:13px;color:var(--text-muted);"><?= $step['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:40px;align-items:start;">

            <!-- Form -->
            <div class="card animate-on-scroll">
                <h2 style="margin-bottom:6px;">📝 Admission Inquiry Form</h2>
                <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px;">All fields marked with <span style="color:var(--danger);">*</span> are required.</p>

                <?php if ($success): ?>
                    <div class="alert alert-success animate-on-scroll" data-dismiss="8000">
                        <span class="alert-icon">🎉</span><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form id="enrollmentForm" method="POST" novalidate>
                    <div style="background:var(--primary-pale);border-radius:var(--radius);padding:18px;margin-bottom:24px;font-size:14px;color:var(--primary);font-weight:600;">
                        👨‍🎓 Student Information
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Student's Full Name <span>*</span></label>
                            <input type="text" name="student_name" class="form-control" 
                                   placeholder="Student's name" required
                                   value="<?= htmlspecialchars($_POST['student_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Class / Grade <span>*</span></label>
                            <select name="class_level" class="form-control" required>
                                <option value="">Select class</option>
                                <?php foreach (['6','7','8','9','10','11 (Science)','11 (Commerce)','12 (Science)','12 (Commerce)','Dropper (JEE)','Dropper (NEET)'] as $cl): ?>
                                    <option value="<?= $cl ?>" <?= ($_POST['class_level'] ?? '') === $cl ? 'selected' : '' ?>><?= $cl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">School Name</label>
                            <input type="text" name="school_name" class="form-control" 
                                   placeholder="Current school name"
                                   value="<?= htmlspecialchars($_POST['school_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Course Interested In</label>
                            <select name="course_id" class="form-control">
                                <option value="">Select a course (optional)</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $selectedCourse === $c['id'] || ($_POST['course_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="background:var(--primary-pale);border-radius:var(--radius);padding:18px;margin-bottom:24px;margin-top:8px;font-size:14px;color:var(--primary);font-weight:600;">
                        👨‍👩‍👧 Parent / Guardian Information
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Parent/Guardian Name <span>*</span></label>
                            <input type="text" name="parent_name" class="form-control" 
                                   placeholder="Parent's name" required
                                   value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number <span>*</span></label>
                            <input type="tel" name="phone" class="form-control" 
                                   placeholder="10-digit mobile number" required
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span>*</span></label>
                        <input type="email" name="email" class="form-control" 
                               placeholder="parent@email.com (for updates)" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Additional Message / Questions</label>
                        <textarea name="message" class="form-control" rows="3" 
                                  placeholder="Any specific requirements or questions..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
                        🚀 Submit Admission Inquiry
                    </button>
                    <p style="font-size:12px;color:var(--text-muted);text-align:center;margin-top:12px;">
                        By submitting this form, you agree to be contacted by our team. No spam, ever.
                    </p>
                </form>
            </div>

            <!-- Sidebar Info -->
            <div>
                <div class="info-contact-card animate-on-scroll delay-2">
                    <h3>📞 Quick Contact</h3>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📞</div>
                        <div class="contact-info-text">
                            <strong>Call / WhatsApp</strong>
                            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">✉️</div>
                        <div class="contact-info-text">
                            <strong>Email</strong>
                            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">🕐</div>
                        <div class="contact-info-text">
                            <strong>Office Hours</strong>
                            <p>Mon–Sun: 8:00 AM – 9:00 PM</p>
                        </div>
                    </div>
                </div>

                <!-- Benefits -->
                <div class="card animate-on-scroll delay-3" style="margin-top:20px;">
                    <h4 style="margin-bottom:16px;">✨ Why Apply Now?</h4>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <?php
                        $benefits = [
                            '🆓 Free Demo Class', '📊 Free Counseling', '💯 Assured Quality', 
                            '📚 Free Study Material', '🏆 Expert Faculty', '⏰ Flexible Timings'
                        ];
                        foreach ($benefits as $b): ?>
                        <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);">
                            <span style="color:var(--success);font-weight:700;">✓</span><?= $b ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="background:var(--warning-light);border-radius:var(--radius-sm);padding:14px;margin-top:16px;font-size:13.5px;color:#92400e;">
                        ⚠️ <strong>Limited Seats Available!</strong><br>
                        Enroll early to secure your spot in the desired batch.
                    </div>
                </div>

                <!-- Fee Structure -->
                <div class="card animate-on-scroll delay-3" style="margin-top:20px;">
                    <h4 style="margin-bottom:16px;">💰 Fee Options</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:14px;">
                        <div style="display:flex;justify-content:space-between;padding:10px;background:var(--light);border-radius:var(--radius-sm);">
                            <span>Monthly</span><span style="font-weight:600;color:var(--primary);">Pay monthly</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:10px;background:var(--light);border-radius:var(--radius-sm);">
                            <span>Quarterly</span><span style="font-weight:600;color:var(--primary);">3 months</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:10px;background:var(--success-light);border-radius:var(--radius-sm);">
                            <span>Annual</span><span style="font-weight:600;color:var(--success);">Best Value ✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

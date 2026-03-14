<?php
require_once 'config.php';
$pageTitle = 'Contact Us';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitize($_POST['email'] ?? '');
    $phone   = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO contacts (name, email, phone, subject, message) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Thank you, $name! Your message has been received. We'll get back to you within 24 hours.";
        } else {
            $error = 'Sorry, something went wrong. Please try again.';
        }
        mysqli_stmt_close($stmt);
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
            <span class="current">Contact Us</span>
        </div>
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Reach out for admissions, inquiries, or any questions.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:48px;align-items:start;">

            <!-- Contact Info -->
            <div class="animate-on-scroll">
                <h2 style="margin-bottom:24px;">We're Here to Help</h2>
                <p style="color:var(--text-light);line-height:1.9;margin-bottom:32px;">
                    Have questions about admissions, courses, timings, or fees? 
                    Our friendly team is available 7 days a week to answer 
                    all your questions.
                </p>

                <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:32px;">
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📞</div>
                        <div class="contact-info-text">
                            <strong>Phone</strong>
                            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">📱</div>
                        <div class="contact-info-text">
                            <strong>WhatsApp</strong>
                            <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" rel="noopener"><?= SITE_PHONE2 ?></a>
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
                        <div class="contact-info-icon">📍</div>
                        <div class="contact-info-text">
                            <strong>Address</strong>
                            <p><?= SITE_ADDRESS ?></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">🕐</div>
                        <div class="contact-info-text">
                            <strong>Working Hours</strong>
                            <p>Monday – Sunday: 8:00 AM – 9:00 PM</p>
                        </div>
                    </div>
                </div>

                <!-- Map placeholder -->
                <div style="background:var(--light);border:1px solid var(--border);border-radius:var(--radius);height:220px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px;">
                    <span style="font-size:48px;">🗺️</span>
                    <p style="color:var(--text-muted);font-size:14px;">Map View — Google Maps integration</p>
                    <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm">Open in Google Maps</a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="card animate-on-scroll delay-2">
                <h2 style="margin-bottom:8px;">Send Us a Message</h2>
                <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px;">We typically respond within 24 hours.</p>

                <?php if ($success): ?>
                    <div class="alert alert-success" data-dismiss="6000">
                        <span class="alert-icon">✓</span><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form id="contactForm" method="POST" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name <span>*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Your full name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address <span>*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit number" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject <span>*</span></label>
                            <select name="subject" class="form-control" required>
                                <option value="">Select a subject</option>
                                <option value="Admission Inquiry">Admission Inquiry</option>
                                <option value="Course Information">Course Information</option>
                                <option value="Fee Structure">Fee Structure</option>
                                <option value="Demo Class Request">Demo Class Request</option>
                                <option value="Result Inquiry">Result Inquiry</option>
                                <option value="Complaint / Feedback">Complaint / Feedback</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Your Message <span>*</span></label>
                        <textarea name="message" class="form-control" rows="5" 
                                  placeholder="Write your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
                        📨 Send Message
                    </button>
                    <p style="font-size:12.5px;color:var(--text-muted);text-align:center;margin-top:14px;">
                        Your information is safe with us. We never share your data.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-sm" style="background:var(--light);">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="badge-label">FAQs</span>
            <h2>Frequently Asked <span class="text-primary">Questions</span></h2>
            <div class="divider"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;max-width:900px;margin:0 auto;">
            <?php
            $faqs = [
                ['q'=>'How can I enroll my child?', 'a'=>'You can enroll by filling our online admission form, calling us, or visiting our center directly. We also offer free demo classes before enrollment.'],
                ['q'=>'Are there demo classes available?', 'a'=>'Yes! We offer free demo classes for all courses. Contact us to book a demo session for your child.'],
                ['q'=>'What are the fee payment options?', 'a'=>'We accept monthly, quarterly, semi-annual, and annual fee payments. We also have easy EMI options available.'],
                ['q'=>'Do you provide study materials?', 'a'=>'Yes, all study materials including notes, question banks, formula sheets, and practice papers are included in the course fee.'],
                ['q'=>'How small are the batch sizes?', 'a'=>'We maintain small batches of 20-35 students to ensure personalized attention for every student.'],
                ['q'=>'Is there an online portal for students?', 'a'=>'Yes! Students get access to their personal dashboard where they can view results, study materials, and class schedules.'],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="card animate-on-scroll delay-<?= ($i % 3) + 1 ?>">
                <h4 style="color:var(--primary);margin-bottom:8px;">❓ <?= htmlspecialchars($faq['q']) ?></h4>
                <p style="color:var(--text-light);font-size:14.5px;line-height:1.7;"><?= htmlspecialchars($faq['a']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

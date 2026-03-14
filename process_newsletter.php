<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit();
}

$emailSafe = mysqli_real_escape_string($conn, $email);

// Check duplicate
$check = mysqli_query($conn, "SELECT id FROM newsletter WHERE email='$emailSafe'");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already subscribed!']);
    exit();
}

$stmt = mysqli_prepare($conn, "INSERT INTO newsletter (email) VALUES (?)");
mysqli_stmt_bind_param($stmt, 's', $email);
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => '🎉 Subscribed successfully! You will receive our latest updates.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Subscription failed. Please try again.']);
}
mysqli_stmt_close($stmt);

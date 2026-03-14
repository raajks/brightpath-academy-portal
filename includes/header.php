<?php
// includes/header.php - Common HTML <head> section
$siteName = defined('SITE_NAME') ? SITE_NAME : 'BrightPath Academy';
$siteUrl  = defined('SITE_URL') ? SITE_URL : '';
$pageTitle = isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName . ' - ' . (defined('SITE_TAGLINE') ? SITE_TAGLINE : 'Shaping Futures');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($pageDesc) ? htmlspecialchars($pageDesc) : 'BrightPath Academy - Expert coaching for Class 6-12, JEE, NEET. Join thousands of successful students.' ?>">
    <meta name="keywords" content="coaching class, tuition, JEE coaching, NEET coaching, class 10 coaching, class 12 coaching">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $siteUrl ?>/css/style.css">
    <?= isset($extraCSS) ? $extraCSS : '' ?>
</head>
<body>

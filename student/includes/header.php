<?php
// student/includes/header.php
// Usage: set $pageTitle and $activePage before including this file
$siteName = defined('SITE_NAME') ? SITE_NAME : 'BrightPath Academy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Student Portal') ?> | <?= $siteName ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ═══════════════════════════════════════
           STUDENT PANEL — SHARED LAYOUT STYLES
        ═══════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; }
        body { background: #f1f5f9; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        .sp-wrapper { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sp-side {
            width: 260px; background: linear-gradient(180deg,#1a1a2e,#16213e);
            position: fixed; height: 100vh; overflow-y: auto;
            left: 0; top: 0; z-index: 200;
            display: flex; flex-direction: column;
            transition: transform 0.3s;
        }
        .sp-side-logo { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }
        .sp-logo-main { color:#fff; font-size:19px; font-weight:800; font-family:'Poppins',sans-serif; line-height:1; }
        .sp-logo-main span { color:#f59e0b; }
        .sp-logo-sub  { color:rgba(255,255,255,0.3); font-size:10px; letter-spacing:.12em; text-transform:uppercase; margin-top:3px; }

        /* Profile strip */
        .sp-side-profile { padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:11px; flex-shrink:0; }
        .sp-avatar { width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; font-size:16px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .sp-profile-name { color:#fff; font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
        .sp-profile-id   { color:rgba(255,255,255,0.35); font-size:10px; font-family:monospace; margin-top:1px; }

        /* Nav */
        .sp-nav { padding:12px 10px; flex:1; overflow-y:auto; }
        .sp-nav-label { color:rgba(255,255,255,0.22); font-size:10px; text-transform:uppercase; letter-spacing:.1em; padding:0 8px; margin:14px 0 4px; }
        .sp-nav-link {
            display:flex; align-items:center; gap:10px;
            padding:9px 12px; border-radius:8px;
            color:rgba(255,255,255,0.58); text-decoration:none;
            font-size:13.5px; font-weight:500; transition:all 0.18s;
            margin-bottom:1px; white-space:nowrap;
        }
        .sp-nav-link:hover { background:rgba(79,70,229,0.28); color:#fff; }
        .sp-nav-link.active { background:rgba(79,70,229,0.4); color:#fff; font-weight:700; }
        .sp-nav-link .ni { font-size:17px; width:21px; text-align:center; flex-shrink:0; }
        .sp-nav-link .sp-badge { margin-left:auto; background:#ef4444; color:#fff; border-radius:20px; padding:1px 7px; font-size:11px; font-weight:700; }
        .sp-nav-link.red-link { color:rgba(239,68,68,0.75); }
        .sp-nav-link.red-link:hover { background:rgba(239,68,68,0.12); color:#ef4444; }

        .sp-side-footer { padding:12px 10px; border-top:1px solid rgba(255,255,255,0.05); flex-shrink:0; }

        /* ── Main content ── */
        .sp-content { margin-left:260px; padding:26px 28px; min-height:100vh; flex:1; width:calc(100% - 260px); }

        /* ── Topbar ── */
        .sp-topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:10px; }
        .sp-topbar-left { display:flex; align-items:center; gap:10px; }
        .sp-page-title { font-size:21px; font-weight:800; color:#0f172a; margin:0; }
        .sp-page-sub   { font-size:13px; color:#64748b; margin-top:2px; }
        .sp-topbar-right { display:flex; align-items:center; gap:10px; }
        .sp-topbar-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center; }

        /* ── Cards ── */
        .sp-card { background:#fff; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,0.06); overflow:hidden; margin-bottom:22px; }
        .sp-card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f5f9; }
        .sp-card-title { font-size:15px; font-weight:700; color:#0f172a; margin:0; }
        .sp-card-body  { padding:20px; }

        /* ── Stat cards ── */
        .sp-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:16px; margin-bottom:22px; }
        .sp-stat { background:#fff; border-radius:14px; padding:18px; box-shadow:0 1px 4px rgba(0,0,0,0.05); display:flex; align-items:center; gap:14px; transition:transform .2s,box-shadow .2s; }
        .sp-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.09); }
        .sp-stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; }
        .sp-stat-icon.blue   { background:rgba(79,70,229,.1); }
        .sp-stat-icon.green  { background:rgba(16,185,129,.1); }
        .sp-stat-icon.amber  { background:rgba(245,158,11,.1); }
        .sp-stat-icon.purple { background:rgba(124,58,237,.1); }
        .sp-stat-icon.rose   { background:rgba(244,63,94,.1); }
        .sp-stat-icon.sky    { background:rgba(14,165,233,.1); }
        .sp-stat-val   { font-size:24px; font-weight:800; color:#0f172a; line-height:1; }
        .sp-stat-label { font-size:12px; color:#64748b; margin-top:3px; }

        /* ── Table ── */
        .sp-table { width:100%; border-collapse:collapse; }
        .sp-table th { background:#f8fafc; padding:10px 14px; text-align:left; font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e2e8f0; }
        .sp-table td { padding:11px 14px; border-bottom:1px solid #f1f5f9; font-size:13.5px; color:#334155; }
        .sp-table tr:last-child td { border-bottom:none; }
        .sp-table tr:hover td { background:#fafbff; }

        /* ── Badges ── */
        .sp-pill { display:inline-flex; align-items:center; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .sp-pill.green  { background:#d1fae5; color:#059669; }
        .sp-pill.amber  { background:#fef3c7; color:#d97706; }
        .sp-pill.red    { background:#fee2e2; color:#dc2626; }
        .sp-pill.blue   { background:#dbeafe; color:#2563eb; }
        .sp-pill.purple { background:#ede9fe; color:#6d28d9; }
        .sp-pill.gray   { background:#f1f5f9; color:#64748b; }

        /* ── Grade pills ── */
        .gp { display:inline-block; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700; }
        .gp-a  { background:#d1fae5; color:#059669; }
        .gp-b  { background:#dbeafe; color:#2563eb; }
        .gp-c  { background:#fef3c7; color:#d97706; }
        .gp-d  { background:#ffedd5; color:#ea580c; }
        .gp-f  { background:#fee2e2; color:#dc2626; }

        /* ── Progress bar ── */
        .sp-bar-wrap { background:#e2e8f0; border-radius:99px; height:7px; overflow:hidden; }
        .sp-bar-fill { height:100%; border-radius:99px; transition:width 1s ease; }

        /* ── Empty state ── */
        .sp-empty { text-align:center; padding:40px 20px; color:#94a3b8; }
        .sp-empty .ei { font-size:44px; margin-bottom:10px; }
        .sp-empty h4   { color:#475569; font-size:15px; margin:0 0 6px; }
        .sp-empty p    { font-size:13px; margin:0 0 16px; }

        /* ── Alert ── */
        .sp-alert { padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:600; margin-bottom:18px; display:flex; align-items:center; gap:10px; }
        .sp-alert.success { background:#d1fae5; color:#065f46; }
        .sp-alert.error   { background:#fee2e2; color:#991b1b; }

        /* ── Mobile ── */
        .sp-hamburger { display:none; background:none; border:none; flex-direction:column; gap:5px; cursor:pointer; padding:4px; margin-right:6px; }
        .sp-hamburger span { width:22px; height:2.5px; background:#0f172a; border-radius:99px; display:block; transition:.3s; }
        .sp-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:199; }
        .sp-overlay.show { display:block; }

        @media (max-width:920px) {
            .sp-side { transform:translateX(-260px); }
            .sp-side.open { transform:translateX(0); }
            .sp-content { margin-left:0; width:100%; padding:18px 14px; }
            .sp-hamburger { display:flex; }
            .sp-stats { grid-template-columns:repeat(2,1fr); }
        }
        @media (max-width:480px) {
            .sp-stats { grid-template-columns:1fr 1fr; gap:10px; }
        }
    </style>
    <?= isset($extraStyle) ? "<style>$extraStyle</style>" : '' ?>
</head>
<body>
<div class="page-loader"><div class="loader-ring"></div></div>
<div class="sp-overlay" id="spOverlay"></div>
<div class="sp-wrapper">

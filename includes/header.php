<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) {
    header("Location: /gym_management/auth/login.php");
    exit();
}
$unread_count = get_unread_count($pdo, $_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gym Management System - Manage memberships, payments, attendance and more">
    <title>GymPro — <?= ucfirst($page_title ?? 'Dashboard') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/gym_management/assets/css/custom.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
<?php
    $role = $_SESSION['role'];
    if ($role === 'admin') include __DIR__ . '/sidebar_admin.php';
    elseif ($role === 'staff') include __DIR__ . '/sidebar_staff.php';
    elseif ($role === 'customer') include __DIR__ . '/sidebar_customer.php';
?>
<div class="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <h1 class="page-title"><?= $page_title ?? 'Dashboard' ?></h1>
        <div class="navbar-right">
            <a href="/gym_management/<?= $_SESSION['role'] ?>/<?= $_SESSION['role'] === 'admin' ? 'notifications.php' : 'dashboard.php' ?>" class="notification-bell">
                <i class="fas fa-bell"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="notification-badge"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <div class="user-info">
                <div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                    <div class="user-role"><?= $_SESSION['role'] ?></div>
                </div>
            </div>
            <a href="/gym_management/auth/logout.php" class="btn btn-outline-danger btn-sm" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
    <!-- Flash Messages -->
    <?php $flash = get_flash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <!-- Loading Spinner -->
    <div class="spinner-overlay">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

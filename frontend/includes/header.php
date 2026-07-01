<?php
require_once __DIR__ . '/../../backend/includes/functions.php';
$pageTitle = isset($pageTitle) ? $pageTitle . ' — Futsal 71' : 'Futsal 71 — Booking Lapangan Futsal';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Booking lapangan futsal online di Futsal 71 Tangerang. Mudah, cepat, dan aman.">
    <title><?= $pageTitle ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Barlow Condensed + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-futsal sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>frontend/pages/index.php">
            <img src="<?= BASE_URL ?>photo/logo.png" alt="Futsal 71" style="height:32px;width:auto">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/booking.php">Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/contact.php">Kontak</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/history.php">Riwayat</a>
                    </li>
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <?php if (!empty($_SESSION['user_avatar'])): ?>
                                <img src="<?= $_SESSION['user_avatar'] ?>" alt="" class="rounded-circle" width="28" height="28">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-lg"></i>
                            <?php endif; ?>
                            <span class="d-none d-lg-inline"><?= $_SESSION['user_name'] ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/index.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>frontend/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/login.php">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-nav-cta" href="<?= BASE_URL ?>frontend/pages/booking.php">Booking</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php
/**
 * Shared site header + top navigation.
 *
 * In this project layout the web root is /public, and shared view
 * partials live in /includes (outside the web root). Pages include this
 * via a filesystem path, e.g. from public/index.php:
 *     require_once __DIR__ . '/../includes/header.php';
 *
 * BASE_URL points at the public web root so links/assets resolve.
 * For a plain XAMPP setup served from htdocs, that is /Gymlens/public.
 * If you point Apache's DocumentRoot at the public/ folder, set it to ''.
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Gymlens/public');
}
$page_title = $page_title ?? 'Gymlens';
$active     = $active ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> · Gymlens</title>
    <meta name="description" content="Gymlens — smart gym management: members, trainers, bookings and live occupancy in one place.">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <a class="brand" href="<?= BASE_URL ?>/index.php">
                <span class="dot">●</span> Gymlens
            </a>

            <ul class="nav-links">
                <li><a href="<?= BASE_URL ?>/index.php#features"<?= $active === 'features' ? ' style="color:var(--color-text)"' : '' ?>>Features</a></li>

                <li class="nav-menu">
                    <button type="button" class="nav-menu-btn"
                            onclick="document.getElementById('pageMenu').classList.toggle('open')">
                        Menu ▾
                    </button>
                    <div class="nav-menu-panel" id="pageMenu">
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Member</span>
                            <a href="<?= BASE_URL ?>/member/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/member/occupancy.php">Live occupancy</a>
                            <a href="<?= BASE_URL ?>/member/book.php">Book a session</a>
                            <a href="<?= BASE_URL ?>/member/bookings.php">My bookings</a>
                            <a href="<?= BASE_URL ?>/member/checkin.php">Check in</a>
                            <a href="<?= BASE_URL ?>/member/checkout.php">Check out</a>
                        </div>
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Trainer</span>
                            <a href="<?= BASE_URL ?>/trainer/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/trainer/sessions.php">Sessions</a>
                            <a href="<?= BASE_URL ?>/trainer/programs.php">Programs</a>
                            <span class="nav-menu-head" style="margin-top:.75rem">General</span>
                            <a href="<?= BASE_URL ?>/notifications.php">Notifications</a>
                        </div>
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Admin</span>
                            <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/admin/occupancy.php">Occupancy</a>
                            <a href="<?= BASE_URL ?>/admin/users.php">Users</a>
                            <a href="<?= BASE_URL ?>/admin/reports.php">Reports</a>
                            <a href="<?= BASE_URL ?>/admin/settings.php">Settings</a>
                        </div>
                    </div>
                </li>

                <li><a href="<?= BASE_URL ?>/login.php"<?= $active === 'login' ? ' style="color:var(--color-text)"' : '' ?>>Log in</a></li>
                <li><a href="<?= BASE_URL ?>/register.php"<?= $active === 'register' ? ' style="color:var(--color-text)"' : '' ?>>Sign up</a></li>
            </ul>

            <div class="nav-cta">
                <a class="btn btn-ghost" href="<?= BASE_URL ?>/login.php">Log in</a>
                <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Get started</a>
            </div>

            <button class="nav-toggle" aria-label="Toggle menu"
                    onclick="document.getElementById('navbar').classList.toggle('open')">☰</button>
        </div>
    </nav>

    <main>

<?php
/**
 * Shared site header + top navigation.
 *
 * Pages include this via a filesystem path, e.g. from public/index.php:
 *     require_once __DIR__ . '/../includes/header.php';
 *
 * It pulls in bootstrap.php, so the session, BASE_URL and the auth
 * helpers (is_logged_in, user_can, current_user) are always available
 * here. The Menu only shows the sections the current role may open.
 */
require_once __DIR__ . '/bootstrap.php';

$page_title = $page_title ?? 'Gymlens';
$active     = $active ?? '';
$me         = current_user();
$flash      = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> · Gymlens</title>
    <meta name="description" content="Gymlens — smart gym management: members, trainers, bookings and live occupancy in one place.">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/favicon.png">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <a class="brand" href="<?= BASE_URL ?>/index.php">
                <span class="dot">●</span> Gymlens
            </a>

            <ul class="nav-links">
                <li><a href="<?= BASE_URL ?>/index.php#features"<?= $active === 'features' ? ' style="color:var(--color-text)"' : '' ?>>Features</a></li>

                <?php if (is_logged_in()): ?>
                <li class="nav-menu">
                    <button type="button" class="nav-menu-btn"
                            onclick="document.getElementById('pageMenu').classList.toggle('open')">
                        Menu ▾
                    </button>
                    <div class="nav-menu-panel" id="pageMenu">
                        <?php if (user_can('member')): ?>
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Member</span>
                            <a href="<?= BASE_URL ?>/member/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/member/occupancy.php">Live occupancy</a>
                            <a href="<?= BASE_URL ?>/member/book.php">Book a session</a>
                            <a href="<?= BASE_URL ?>/member/bookings.php">My bookings</a>
                            <a href="<?= BASE_URL ?>/member/checkin.php">Check in</a>
                            <a href="<?= BASE_URL ?>/member/checkout.php">Check out</a>
                        </div>
                        <?php endif; ?>

                        <?php if (user_can('trainer')): ?>
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Trainer</span>
                            <a href="<?= BASE_URL ?>/trainer/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/trainer/sessions.php">Sessions</a>
                            <a href="<?= BASE_URL ?>/trainer/programs.php">Programs</a>
                        </div>
                        <?php endif; ?>

                        <?php if (user_can('admin')): ?>
                        <div class="nav-menu-col">
                            <span class="nav-menu-head">Admin</span>
                            <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                            <a href="<?= BASE_URL ?>/admin/occupancy.php">Occupancy</a>
                            <a href="<?= BASE_URL ?>/admin/users.php">Users</a>
                            <a href="<?= BASE_URL ?>/admin/reports.php">Reports</a>
                            <a href="<?= BASE_URL ?>/admin/settings.php">Settings</a>
                        </div>
                        <?php endif; ?>

                        <div class="nav-menu-col">
                            <span class="nav-menu-head">General</span>
                            <a href="<?= BASE_URL ?>/notifications.php">Notifications</a>
                        </div>
                    </div>
                </li>
                <?php else: ?>
                <li><a href="<?= BASE_URL ?>/login.php"<?= $active === 'login' ? ' style="color:var(--color-text)"' : '' ?>>Log in</a></li>
                <li><a href="<?= BASE_URL ?>/register.php"<?= $active === 'register' ? ' style="color:var(--color-text)"' : '' ?>>Sign up</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-cta">
                <?php if (is_logged_in()): ?>
                    <span class="nav-user">Hi, <?= e(explode(' ', $me['name'])[0]) ?></span>
                    <a class="btn btn-ghost" href="<?= BASE_URL ?>/logout.php">Log out</a>
                <?php else: ?>
                    <a class="btn btn-ghost" href="<?= BASE_URL ?>/login.php">Log in</a>
                    <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Get started</a>
                <?php endif; ?>
            </div>

            <button class="nav-toggle" aria-label="Toggle menu"
                    onclick="document.getElementById('navbar').classList.toggle('open')">☰</button>
        </div>
    </nav>

    <main>
        <?php if ($flash): ?>
            <div class="container">
                <div class="page-flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            </div>
        <?php endif; ?>

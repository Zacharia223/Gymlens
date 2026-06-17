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

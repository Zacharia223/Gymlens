<?php
/**
 * Gymlens — app bootstrap.
 *
 * Every page should start with:
 *     require_once __DIR__ . '/../includes/bootstrap.php';   // adjust depth
 *
 * It starts the session, defines BASE_URL, and loads the shared
 * helpers + auth layer. It does NOT open a database connection —
 * include config/database.php separately on pages that need data,
 * so public pages (landing) stay DB-free.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    // Plain XAMPP served from htdocs. If DocumentRoot points at public/, set to ''.
    define('BASE_URL', '/Gymlens/public');
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

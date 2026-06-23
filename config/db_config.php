<?php
/**
 * Gymlens — database credentials (single source of truth).
 * Edit these to match your local MySQL / XAMPP setup.
 *
 * Used by config/database.php (app connection) and tools/migrate.php
 * (which can also create the database itself).
 */

$DB = [
    'host'     => '127.0.0.1',
    'port'     => 3306,        // change if your MySQL/MariaDB runs on a different port
    'name'     => 'gym_lens',
    'user'     => 'root',
    'password' => '1234',
];

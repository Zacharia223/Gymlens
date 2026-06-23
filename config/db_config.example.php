<?php
/**
 * Gymlens — database credentials TEMPLATE.
 *
 * Each developer keeps their OWN copy. This .example file is committed to git;
 * your real credentials live in config/db_config.php, which is git-ignored so
 * it never overwrites a teammate's local password.
 *
 * SETUP (one time):
 *   1. Copy this file to config/db_config.php
 *        Windows:  copy config\db_config.example.php config\db_config.php
 *        Mac/Linux: cp config/db_config.example.php config/db_config.php
 *   2. Edit config/db_config.php with YOUR local MySQL / MariaDB password.
 *
 * Default XAMPP installs use an EMPTY root password, so 'password' => '' often
 * works out of the box. Change it only if you set a password for root.
 *
 * Used by config/database.php (app connection) and tools/migrate.php
 * (which can also create the database itself).
 */

$DB = [
    'host'     => '127.0.0.1',
    'port'     => 3306,        // change if your MySQL/MariaDB runs on a different port
    'name'     => 'gym_lens',
    'user'     => 'root',
    'password' => '',          // <-- set YOUR local root password here (empty by default on XAMPP)
];

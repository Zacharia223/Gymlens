<?php
/**
 * Gymlens — database migration.
 * Creates every table from the schema (docs/wireframes/database-schema.jpeg).
 * Safe to run repeatedly: each table uses CREATE TABLE IF NOT EXISTS.
 *
 * Run from the project root:
 *     php tools/migrate.php
 * or open it once in the browser: http://localhost/Gymlens/tools/migrate.php
 */

require_once __DIR__ . '/../config/db_config.php'; // $DB

$cli = (php_sapi_name() === 'cli');
$nl  = $cli ? "\n" : "<br>";

// Step 1: make sure the database itself exists (connect without selecting one).
try {
    $server = new PDO("mysql:host={$DB['host']};port={$DB['port']};charset=utf8", $DB['user'], $DB['password']);
    $server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$DB['name']}`
                   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    die("Could not connect to MySQL or create the database: " . $e->getMessage()
        . " — check that MySQL is running and the credentials in config/db_config.php are correct.{$nl}");
}

// Step 2: connect to the (now guaranteed) database to build the tables.
$conn = new PDO(
    "mysql:host={$DB['host']};port={$DB['port']};dbname={$DB['name']};charset=utf8",
    $DB['user'],
    $DB['password']
);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = [

    'User' => "
        CREATE TABLE IF NOT EXISTS User (
            user_id       INT AUTO_INCREMENT PRIMARY KEY,
            first_name    VARCHAR(50)  NOT NULL,
            last_name     VARCHAR(50)  NOT NULL,
            email         VARCHAR(50)  NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            phone         VARCHAR(20),
            role          ENUM('member','trainer','admin') NOT NULL DEFAULT 'member',
            last_login    TIMESTAMP NULL
        ) ENGINE=InnoDB",

    'members' => "
        CREATE TABLE IF NOT EXISTS members (
            member_id       INT PRIMARY KEY,
            membership_type ENUM('basic','standard','premium') NOT NULL DEFAULT 'basic',
            join_date       DATE,
            expiry_date     DATE,
            status          ENUM('active','inactive','frozen') NOT NULL DEFAULT 'active',
            FOREIGN KEY (member_id) REFERENCES User(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'trainers' => "
        CREATE TABLE IF NOT EXISTS trainers (
            trainer_id     INT PRIMARY KEY,
            specialization VARCHAR(100),
            certification  VARCHAR(100),
            hire_date      DATE,
            FOREIGN KEY (trainer_id) REFERENCES User(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'administrators' => "
        CREATE TABLE IF NOT EXISTS administrators (
            admin_id     INT PRIMARY KEY,
            access_level ENUM('standard','super') NOT NULL DEFAULT 'standard',
            department   VARCHAR(50),
            FOREIGN KEY (admin_id) REFERENCES User(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'zones' => "
        CREATE TABLE IF NOT EXISTS zones (
            zone_id       INT AUTO_INCREMENT PRIMARY KEY,
            name          VARCHAR(50) NOT NULL,
            description   VARCHAR(255),
            capacity      INT NOT NULL DEFAULT 0,
            current_count INT NOT NULL DEFAULT 0,
            location      VARCHAR(100)
        ) ENGINE=InnoDB",

    'sessions' => "
        CREATE TABLE IF NOT EXISTS sessions (
            session_id   INT AUTO_INCREMENT PRIMARY KEY,
            trainer_id   INT,
            zone_id      INT,
            title        VARCHAR(100) NOT NULL,
            session_type VARCHAR(50),
            start_time   DATETIME,
            end_time     DATETIME,
            max_capacity INT NOT NULL DEFAULT 0,
            status       ENUM('scheduled','cancelled','completed') NOT NULL DEFAULT 'scheduled',
            FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE SET NULL,
            FOREIGN KEY (zone_id)    REFERENCES zones(zone_id)       ON DELETE SET NULL
        ) ENGINE=InnoDB",

    'bookings' => "
        CREATE TABLE IF NOT EXISTS bookings (
            booking_id INT AUTO_INCREMENT PRIMARY KEY,
            member_id  INT NOT NULL,
            session_id INT NOT NULL,
            booked_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            status     ENUM('confirmed','cancelled','attended') NOT NULL DEFAULT 'confirmed',
            FOREIGN KEY (member_id)  REFERENCES members(member_id)   ON DELETE CASCADE,
            FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'checkins' => "
        CREATE TABLE IF NOT EXISTS checkins (
            checkin_id     INT AUTO_INCREMENT PRIMARY KEY,
            member_id      INT NOT NULL,
            zone_id        INT NOT NULL,
            checked_in_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            checked_out_at TIMESTAMP NULL,
            FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
            FOREIGN KEY (zone_id)   REFERENCES zones(zone_id)     ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'equipment' => "
        CREATE TABLE IF NOT EXISTS equipment (
            equipment_id    INT AUTO_INCREMENT PRIMARY KEY,
            zone_id         INT,
            name            VARCHAR(100) NOT NULL,
            type            VARCHAR(50),
            status          ENUM('available','in_use','maintenance') NOT NULL DEFAULT 'available',
            last_maintained DATE,
            FOREIGN KEY (zone_id) REFERENCES zones(zone_id) ON DELETE SET NULL
        ) ENGINE=InnoDB",

    'occupancy_log' => "
        CREATE TABLE IF NOT EXISTS occupancy_log (
            log_id      INT AUTO_INCREMENT PRIMARY KEY,
            zone_id     INT NOT NULL,
            recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            count       INT NOT NULL DEFAULT 0,
            FOREIGN KEY (zone_id) REFERENCES zones(zone_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'predictions' => "
        CREATE TABLE IF NOT EXISTS predictions (
            prediction_id   INT AUTO_INCREMENT PRIMARY KEY,
            zone_id         INT NOT NULL,
            predicted_for   INT,
            predicted_count INT,
            confidence      DECIMAL(4,2),
            generated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (zone_id) REFERENCES zones(zone_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

    'notifications' => "
        CREATE TABLE IF NOT EXISTS notifications (
            notification_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id         INT NOT NULL,
            message         TEXT NOT NULL,
            type            ENUM('info','booking','renewal','alert') NOT NULL DEFAULT 'info',
            sent_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_read         BOOLEAN NOT NULL DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
];

echo "Gymlens migration{$nl}-----------------{$nl}";

// Optional clean rebuild: tools/migrate.php?fresh=1  (or `php tools/migrate.php fresh`)
// Drops the existing app tables first so their columns match the app exactly.
// WARNING: this deletes all data in those tables.
$fresh = isset($_GET['fresh']) || in_array('fresh', $argv ?? [], true);
if ($fresh) {
    echo "  ! fresh rebuild — dropping existing tables{$nl}";
    $conn->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (array_keys($tables) as $name) {
        $conn->exec("DROP TABLE IF EXISTS `{$name}`");
    }
    $conn->exec('SET FOREIGN_KEY_CHECKS = 1');
}

foreach ($tables as $name => $sql) {
    try {
        $conn->exec($sql);
        echo "  ✓ {$name}{$nl}";
    } catch (PDOException $e) {
        echo "  ✗ {$name}: " . $e->getMessage() . $nl;
    }
}

echo "{$nl}Done. Run tools/seed.php next to add demo data.{$nl}";

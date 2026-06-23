<?php
/**
 * App database connection. Provides $conn (PDO) to whatever includes it.
 * Credentials live in config/db_config.php so the setup tools can reuse them.
 */

if (!file_exists(__DIR__ . '/db_config.php')) {
    die("Missing config/db_config.php — copy config/db_config.example.php to "
        . "config/db_config.php and set your local MySQL/MariaDB password.");
}

require_once __DIR__ . '/db_config.php'; // $DB

try {
    $conn = new PDO(
        "mysql:host={$DB['host']};port={$DB['port']};dbname={$DB['name']};charset=utf8",
        $DB['user'],
        $DB['password']
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()
        . " — is MySQL running and the database created? Try running tools/migrate.php.");
}

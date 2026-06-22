<?php
/**
 * Gymlens — small, shared helper functions.
 * Kept dependency-free so any page can include it.
 */

/** Escape output for safe HTML rendering. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Redirect to a path under BASE_URL (or an absolute URL) and stop. */
function redirect(string $path): void
{
    if (preg_match('#^https?://#', $path)) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . BASE_URL . $path);
    }
    exit;
}

/** Build a URL under the public web root. */
function url(string $path = ''): string
{
    return BASE_URL . $path;
}

/**
 * Run a SELECT and return all rows. Returns [] if the query fails
 * (e.g. the table doesn't exist yet) so pages degrade gracefully
 * instead of throwing a fatal error during development.
 */
function db_all(PDO $conn, string $sql, array $params = []): array
{
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/** Run a SELECT and return the first row, or null. */
function db_one(PDO $conn, string $sql, array $params = []): ?array
{
    $rows = db_all($conn, $sql, $params);
    return $rows[0] ?? null;
}

/** Store a one-time flash message shown on the next page load. */
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

/** Pull and clear the flash message, or null if none. */
function take_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

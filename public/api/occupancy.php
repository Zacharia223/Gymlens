<?php
/**
 * Gymlens — Live Occupancy API
 * Returns current zone counts as JSON so pages can refresh without reloading.
 * Requires a logged-in user (uses the session cookie sent by the browser).
 *
 * GET /api/occupancy.php  ->  { "zones": [...], "updated": "..." }
 */
require_once __DIR__ . '/../../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php'; // $conn

$zones = db_all($conn, 'SELECT zone_id, name, capacity, current_count FROM zones ORDER BY zone_id');

$out = array_map(static function (array $z): array {
    $cap = (int) $z['capacity'];
    $cur = (int) $z['current_count'];
    return [
        'id'       => (int) $z['zone_id'],
        'name'     => $z['name'],
        'capacity' => $cap,
        'current'  => $cur,
        'pct'      => $cap > 0 ? min(100, (int) round($cur / $cap * 100)) : 0,
    ];
}, $zones);

echo json_encode([
    'zones'   => $out,
    'updated' => date('c'),
]);

<?php
/**
 * Gymlens — demo data seeder.
 * Creates one user per role, plus some zones and sessions so the
 * pages have something to show. Safe to run repeatedly.
 *
 *     php tools/seed.php
 *
 * Demo logins (all use the password:  password123 ):
 *     member   -> zacharia@strathmore.edu
 *     trainer  -> brian@strathmore.edu
 *     admin    -> admin@strathmore.edu
 */

require_once __DIR__ . '/../config/database.php'; // $conn (PDO)

$cli = (php_sapi_name() === 'cli');
$nl  = $cli ? "\n" : "<br>";
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$hash = password_hash('password123', PASSWORD_DEFAULT);

/** Insert a user if the email is new; returns the user_id either way. */
function ensure_user(PDO $conn, array $u): int
{
    $existing = $conn->prepare('SELECT user_id FROM User WHERE email = ?');
    $existing->execute([$u['email']]);
    $id = $existing->fetchColumn();
    if ($id) {
        return (int) $id;
    }
    $ins = $conn->prepare(
        'INSERT INTO User (first_name, last_name, email, password_hash, phone, role)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $ins->execute([$u['first'], $u['last'], $u['email'], $u['hash'], $u['phone'], $u['role']]);
    return (int) $conn->lastInsertId();
}

echo "Gymlens seed{$nl}------------{$nl}";

try {
    // --- Users (one per role) ---
    $memberId = ensure_user($conn, [
        'first' => 'Zacharia', 'last' => 'Ogega', 'email' => 'zacharia@strathmore.edu',
        'hash'  => $hash, 'phone' => '+254700000001', 'role' => 'member',
    ]);
    $trainerId = ensure_user($conn, [
        'first' => 'Brian', 'last' => 'Mwangi', 'email' => 'brian@strathmore.edu',
        'hash'  => $hash, 'phone' => '+254700000002', 'role' => 'trainer',
    ]);
    $adminId = ensure_user($conn, [
        'first' => 'Amina', 'last' => 'Hassan', 'email' => 'admin@strathmore.edu',
        'hash'  => $hash, 'phone' => '+254700000003', 'role' => 'admin',
    ]);
    echo "  ✓ users (member #{$memberId}, trainer #{$trainerId}, admin #{$adminId}){$nl}";

    // --- Role detail rows ---
    $conn->prepare('INSERT IGNORE INTO members (member_id, membership_type, join_date, expiry_date, status)
                    VALUES (?, "standard", CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), "active")')
         ->execute([$memberId]);
    $conn->prepare('INSERT IGNORE INTO trainers (trainer_id, specialization, certification, hire_date)
                    VALUES (?, "Strength & Conditioning", "NASM-CPT", CURDATE())')
         ->execute([$trainerId]);
    $conn->prepare('INSERT IGNORE INTO administrators (admin_id, access_level, department)
                    VALUES (?, "super", "Operations")')
         ->execute([$adminId]);
    echo "  ✓ member / trainer / admin detail rows{$nl}";

    // --- Zones (only if none exist) ---
    $zoneCount = (int) $conn->query('SELECT COUNT(*) FROM zones')->fetchColumn();
    if ($zoneCount === 0) {
        $zones = [
            ['Cardio',  'Treadmills, bikes and rowers',  60, 45, 'Ground floor'],
            ['Weights', 'Free weights and machines',     40, 32, 'Ground floor'],
            ['Studio',  'Group classes and yoga',        30, 12, 'First floor'],
            ['Pool',    'Lap pool and lanes',            25, 8,  'Basement'],
        ];
        $z = $conn->prepare('INSERT INTO zones (name, description, capacity, current_count, location)
                             VALUES (?, ?, ?, ?, ?)');
        foreach ($zones as $row) {
            $z->execute($row);
        }
        echo "  ✓ zones (" . count($zones) . "){$nl}";
    } else {
        echo "  • zones already present ({$zoneCount}){$nl}";
    }

    // --- Sessions (only if none exist) ---
    $sessionCount = (int) $conn->query('SELECT COUNT(*) FROM sessions')->fetchColumn();
    if ($sessionCount === 0) {
        $zoneIds = $conn->query('SELECT zone_id FROM zones ORDER BY zone_id')->fetchAll(PDO::FETCH_COLUMN);
        $sessions = [
            ['Morning Cardio Blast', 'Cardio',  $zoneIds[0] ?? null, '09:00', '10:00', 20],
            ['Strength Foundations', 'Weights', $zoneIds[1] ?? null, '11:00', '12:00', 15],
            ['Afternoon Yoga Flow',  'Studio',  $zoneIds[2] ?? null, '14:00', '15:00', 25],
        ];
        $s = $conn->prepare(
            'INSERT INTO sessions (trainer_id, zone_id, title, session_type, start_time, end_time, max_capacity, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, "scheduled")'
        );
        foreach ($sessions as $row) {
            [$title, $type, $zid, $start, $end, $cap] = $row;
            $s->execute([
                $trainerId, $zid, $title, $type,
                date('Y-m-d') . ' ' . $start . ':00',
                date('Y-m-d') . ' ' . $end . ':00',
                $cap,
            ]);
        }
        echo "  ✓ sessions (" . count($sessions) . "){$nl}";
    } else {
        echo "  • sessions already present ({$sessionCount}){$nl}";
    }

    echo "{$nl}Done. Log in at /login.php with any demo email and password: password123{$nl}";

} catch (PDOException $e) {
    echo "  ✗ seed failed: " . $e->getMessage() . $nl;
    echo "    Did you run tools/migrate.php first?{$nl}";
}

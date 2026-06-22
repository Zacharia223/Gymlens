<?php
/**
 * Gymlens — Member Dashboard
 * Matches wireframe screen 2 (Dashboard & Booking).
 * Access: members (admins may view too — admin is a superuser).
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me = current_user();

// Live occupancy (top zones). Falls back to demo rows before the DB is seeded.
$occupancy = db_all($conn, 'SELECT name, capacity, current_count FROM zones ORDER BY zone_id LIMIT 3');
if (!$occupancy) {
    $occupancy = [
        ['name' => 'Cardio',  'current_count' => 45, 'capacity' => 60],
        ['name' => 'Weights', 'current_count' => 32, 'capacity' => 40],
        ['name' => 'Studio',  'current_count' => 12, 'capacity' => 30],
    ];
}

// Bookable sessions for today.
$slots = db_all(
    $conn,
    "SELECT session_id, title, session_type, start_time
       FROM sessions
      WHERE status = 'scheduled'
   ORDER BY start_time LIMIT 5"
);

$trainers = db_all($conn, "SELECT t.trainer_id, u.first_name, u.last_name
                             FROM trainers t JOIN User u ON u.user_id = t.trainer_id");

$page_title = 'Member Dashboard';
$active     = 'dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">

        <div class="dash-head">
            <div>
                <h1>Hi, <?= e(explode(' ', $me['name'])[0]) ?> 👋</h1>
                <p>Here's what's happening at the gym today.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/member/bookings.php">My bookings</a>
        </div>

        <div class="dash-grid">

            <!-- ===== Left column: occupancy + best times ===== -->
            <div>
                <div class="panel">
                    <h2>Live Occupancy</h2>
                    <?php foreach ($occupancy as $o):
                        $pct = $o['capacity'] > 0 ? round($o['current_count'] / $o['capacity'] * 100) : 0;
                    ?>
                        <div class="occ-block">
                            <div class="occ-label">
                                <span><?= e($o['name']) ?></span>
                                <span class="count"><?= (int) $o['current_count'] ?> / <?= (int) $o['capacity'] ?></span>
                            </div>
                            <div class="bar"><span style="width:<?= $pct ?>%"></span></div>
                        </div>
                    <?php endforeach; ?>
                    <p style="margin-top:1rem"><a class="card-link" href="<?= BASE_URL ?>/member/occupancy.php">See all zones →</a></p>
                </div>

                <div class="panel">
                    <h2>Best Times Today</h2>
                    <div class="chips">
                        <span class="chip quiet">🟢 Quiet 2 — 4 pm</span>
                        <span class="chip busy">🔴 Busy 6 — 8 pm</span>
                    </div>
                </div>
            </div>

            <!-- ===== Right column: book a session ===== -->
            <div>
                <div class="panel">
                    <h2>Book a Session</h2>
                    <form method="post" action="<?= BASE_URL ?>/member/book.php">
                        <input type="hidden" name="date" value="<?= date('Y-m-d') ?>">

                        <div class="form-group">
                            <label class="form-label">Available times</label>
                            <?php if (!$slots): ?>
                                <p style="color:var(--color-muted)">No sessions scheduled yet.
                                   <a class="card-link" href="<?= BASE_URL ?>/member/book.php">Go to booking →</a></p>
                            <?php else: foreach ($slots as $i => $s): ?>
                                <label class="slot">
                                    <input type="radio" name="session_id" value="<?= (int) $s['session_id'] ?>"
                                           <?= $i === 0 ? 'checked' : '' ?>>
                                    <span class="slot-time"><?= e(date('g:i a', strtotime($s['start_time']))) ?></span>
                                    <span class="slot-zone">· <?= e($s['title']) ?></span>
                                </label>
                            <?php endforeach; endif; ?>
                        </div>

                        <?php if ($slots): ?>
                        <div class="book-footer">
                            <div class="form-group">
                                <label class="form-label" for="trainer">Trainer</label>
                                <select class="select" id="trainer" name="trainer_id">
                                    <option value="">Any available</option>
                                    <?php foreach ($trainers as $t): ?>
                                        <option value="<?= (int) $t['trainer_id'] ?>">
                                            <?= e($t['first_name'] . ' ' . $t['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<?php
/**
 * Gymlens — Member Dashboard
 * Matches wireframe screen 2 (Dashboard & Booking).
 * Uses the shared site header/footer so it keeps the same layout
 * as the landing page. Data below is placeholder until the
 * occupancy/bookings backend is wired up.
 */
$page_title = 'Member Dashboard';
$active     = 'dashboard';

// --- Placeholder data (replace with DB queries later) ---
$member_name = 'Zacharia';
$occupancy = [
    ['zone' => 'Cardio',  'current' => 45, 'capacity' => 60],
    ['zone' => 'Weights', 'current' => 32, 'capacity' => 40],
    ['zone' => 'Studio',  'current' => 12, 'capacity' => 30],
];
$slots = [
    ['time' => '9:00 am',  'zone' => 'Cardio'],
    ['time' => '11:00 am', 'zone' => 'Weights'],
    ['time' => '2:00 pm',  'zone' => 'Studio'],
];

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">

        <div class="dash-head">
            <div>
                <h1>Hi, <?= htmlspecialchars($member_name) ?> 👋</h1>
                <p>Here's what's happening at the gym today.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/logout.php">Log out</a>
        </div>

        <div class="dash-grid">

            <!-- ===== Left column: occupancy + best times ===== -->
            <div>
                <div class="panel">
                    <h2>Live Occupancy</h2>
                    <?php foreach ($occupancy as $o):
                        $pct = $o['capacity'] > 0 ? round($o['current'] / $o['capacity'] * 100) : 0;
                    ?>
                        <div class="occ-block">
                            <div class="occ-label">
                                <span><?= htmlspecialchars($o['zone']) ?></span>
                                <span class="count"><?= $o['current'] ?> / <?= $o['capacity'] ?></span>
                            </div>
                            <div class="bar"><span style="width:<?= $pct ?>%"></span></div>
                        </div>
                    <?php endforeach; ?>
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

                        <div class="form-group">
                            <label class="form-label" for="date">Date</label>
                            <input class="form-input" type="date" id="date" name="date"
                                   value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Available times</label>
                            <?php foreach ($slots as $i => $s): ?>
                                <label class="slot">
                                    <input type="radio" name="slot" value="<?= htmlspecialchars($s['time'] . ' ' . $s['zone']) ?>"
                                           <?= $i === 1 ? 'checked' : '' ?>>
                                    <span class="slot-time"><?= htmlspecialchars($s['time']) ?></span>
                                    <span class="slot-zone">· <?= htmlspecialchars($s['zone']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="book-footer">
                            <div class="form-group">
                                <label class="form-label" for="trainer">Trainer</label>
                                <select class="select" id="trainer" name="trainer">
                                    <option value="">Any available</option>
                                    <option value="1">Coach Amina</option>
                                    <option value="2">Coach Brian</option>
                                    <option value="3">Coach Wanjau</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

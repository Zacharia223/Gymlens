<?php
/**
 * Gymlens — Member: Check In
 * Records the member entering a zone and bumps that zone's live count.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me       = current_user();
$memberId = $me['id'];

// Is the member already checked in somewhere?
$open = db_one(
    $conn,
    "SELECT c.checkin_id, c.checked_in_at, z.name AS zone
       FROM checkins c JOIN zones z ON z.zone_id = c.zone_id
      WHERE c.member_id = ? AND c.checked_out_at IS NULL
   ORDER BY c.checked_in_at DESC LIMIT 1",
    [$memberId]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($open) {
        flash('You’re already checked in to ' . $open['zone'] . '. Check out first.', 'error');
        redirect('/member/checkin.php');
    }
    $zoneId = (int) ($_POST['zone_id'] ?? 0);
    try {
        $zone = db_one($conn, 'SELECT zone_id FROM zones WHERE zone_id = ?', [$zoneId]);
        if (!$zone) {
            flash('Please choose a valid zone.', 'error');
            redirect('/member/checkin.php');
        }
        $conn->beginTransaction();
        $conn->prepare('INSERT INTO checkins (member_id, zone_id) VALUES (?, ?)')
             ->execute([$memberId, $zoneId]);
        $conn->prepare('UPDATE zones SET current_count = LEAST(current_count + 1, capacity)
                         WHERE zone_id = ?')->execute([$zoneId]);
        $conn->commit();
        flash('Checked in. Have a great workout!');
        redirect('/member/checkin.php');
    } catch (PDOException $e) {
        if ($conn->inTransaction()) { $conn->rollBack(); }
        flash('Could not check in. Is the database set up?', 'error');
        redirect('/member/checkin.php');
    }
}

$zones = db_all($conn, 'SELECT zone_id, name, capacity, current_count FROM zones ORDER BY zone_id');

$page_title = 'Check In';
$active     = 'checkin';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">
        <div class="dash-head">
            <div>
                <h1>Check In</h1>
                <p>Tap your zone as you enter so occupancy stays accurate.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/member/occupancy.php">View occupancy</a>
        </div>

        <div class="panel">
            <?php if ($open): ?>
                <p>You’re currently checked in to <strong><?= e($open['zone']) ?></strong>
                   since <?= e(date('g:i a', strtotime($open['checked_in_at']))) ?>.</p>
                <p style="margin-top:1rem">
                    <a class="btn btn-primary" href="<?= BASE_URL ?>/member/checkout.php">Check out</a>
                </p>
            <?php elseif (!$zones): ?>
                <p style="color:var(--color-muted)">No zones to check in to yet.</p>
            <?php else: ?>
                <form method="post" action="<?= BASE_URL ?>/member/checkin.php">
                    <div class="form-group">
                        <label class="form-label">Choose a zone</label>
                        <?php foreach ($zones as $i => $z): ?>
                            <label class="slot">
                                <input type="radio" name="zone_id" value="<?= (int) $z['zone_id'] ?>"
                                       <?= $i === 0 ? 'checked' : '' ?>>
                                <span class="slot-time"><?= e($z['name']) ?></span>
                                <span class="slot-zone">· <?= (int) $z['current_count'] ?> / <?= (int) $z['capacity'] ?> in now</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Check in</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

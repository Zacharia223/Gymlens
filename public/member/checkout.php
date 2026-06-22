<?php
/**
 * Gymlens — Member: Check Out
 * Closes the member's open check-in and lowers that zone's live count.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me       = current_user();
$memberId = $me['id'];

// The member's current open check-in, if any.
$open = db_one(
    $conn,
    "SELECT c.checkin_id, c.zone_id, c.checked_in_at, z.name AS zone
       FROM checkins c JOIN zones z ON z.zone_id = c.zone_id
      WHERE c.member_id = ? AND c.checked_out_at IS NULL
   ORDER BY c.checked_in_at DESC LIMIT 1",
    [$memberId]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$open) {
        flash('You’re not checked in anywhere.', 'error');
        redirect('/member/checkout.php');
    }
    try {
        $conn->beginTransaction();
        $conn->prepare('UPDATE checkins SET checked_out_at = NOW() WHERE checkin_id = ?')
             ->execute([$open['checkin_id']]);
        $conn->prepare('UPDATE zones SET current_count = GREATEST(current_count - 1, 0)
                         WHERE zone_id = ?')->execute([$open['zone_id']]);
        $conn->commit();
        flash('Checked out of ' . $open['zone'] . '. Nice work!');
        redirect('/member/dashboard.php');
    } catch (PDOException $e) {
        if ($conn->inTransaction()) { $conn->rollBack(); }
        flash('Could not check out. Please try again.', 'error');
        redirect('/member/checkout.php');
    }
}

$page_title = 'Check Out';
$active     = 'checkout';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">
        <div class="dash-head">
            <div>
                <h1>Check Out</h1>
                <p>Let us know when you’re leaving a zone.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/member/checkin.php">Check in</a>
        </div>

        <div class="panel">
            <?php if (!$open): ?>
                <p style="color:var(--color-muted)">You’re not currently checked in.
                   <a class="card-link" href="<?= BASE_URL ?>/member/checkin.php">Check in to a zone →</a></p>
            <?php else: ?>
                <p>You’re checked in to <strong><?= e($open['zone']) ?></strong>
                   since <?= e(date('g:i a', strtotime($open['checked_in_at']))) ?>.</p>
                <form method="post" action="<?= BASE_URL ?>/member/checkout.php" style="margin-top:1rem">
                    <button type="submit" class="btn btn-primary">Check out now</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

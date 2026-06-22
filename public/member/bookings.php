<?php
/**
 * Gymlens — Member: My Bookings
 * Lists the member's bookings and lets them cancel a confirmed one.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me       = current_user();
$memberId = $me['id'];

// Cancel a booking.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    try {
        $conn->prepare(
            "UPDATE bookings SET status = 'cancelled'
              WHERE booking_id = ? AND member_id = ? AND status = 'confirmed'"
        )->execute([$bookingId, $memberId]);
        flash('Booking cancelled.');
    } catch (PDOException $e) {
        flash('Could not cancel that booking.', 'error');
    }
    redirect('/member/bookings.php');
}

$bookings = db_all(
    $conn,
    "SELECT b.booking_id, b.status, b.booked_at,
            s.title, s.start_time, z.name AS zone
       FROM bookings b
       JOIN sessions s ON s.session_id = b.session_id
       LEFT JOIN zones z ON z.zone_id = s.zone_id
      WHERE b.member_id = ?
   ORDER BY s.start_time DESC",
    [$memberId]
);

$page_title = 'My Bookings';
$active     = 'bookings';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">
        <div class="dash-head">
            <div>
                <h1>My Bookings</h1>
                <p>Your upcoming and past sessions.</p>
            </div>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/member/book.php">Book a session</a>
        </div>

        <div class="panel">
            <?php if (!$bookings): ?>
                <p style="color:var(--color-muted)">You haven’t booked anything yet.
                   <a class="card-link" href="<?= BASE_URL ?>/member/book.php">Book your first session →</a></p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr><th>Session</th><th>Zone</th><th>When</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= e($b['title']) ?></td>
                            <td><?= e($b['zone'] ?? '—') ?></td>
                            <td><?= e(date('D, j M · g:i a', strtotime($b['start_time']))) ?></td>
                            <td><span class="badge badge-<?= e($b['status']) ?>"><?= e(ucfirst($b['status'])) ?></span></td>
                            <td style="text-align:right">
                                <?php if ($b['status'] === 'confirmed'): ?>
                                    <form method="post" action="<?= BASE_URL ?>/member/bookings.php"
                                          onsubmit="return confirm('Cancel this booking?');">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm">Cancel</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

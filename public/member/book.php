<?php
/**
 * Gymlens — Member: Book a Session
 * GET  -> shows the available sessions to book.
 * POST -> records the booking for the logged-in member.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me       = current_user();
$memberId = $me['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionId = (int) ($_POST['session_id'] ?? 0);

    if ($sessionId <= 0) {
        flash('Please pick a session to book.', 'error');
        redirect('/member/book.php');
    }

    try {
        $session = db_one($conn, "SELECT session_id FROM sessions
                                   WHERE session_id = ? AND status = 'scheduled'", [$sessionId]);
        if (!$session) {
            flash('That session is no longer available.', 'error');
            redirect('/member/book.php');
        }

        $already = db_one(
            $conn,
            "SELECT booking_id FROM bookings
              WHERE member_id = ? AND session_id = ? AND status = 'confirmed'",
            [$memberId, $sessionId]
        );
        if ($already) {
            flash('You’ve already booked that session.', 'error');
            redirect('/member/bookings.php');
        }

        $conn->prepare(
            "INSERT INTO bookings (member_id, session_id, status) VALUES (?, ?, 'confirmed')"
        )->execute([$memberId, $sessionId]);

        notify($conn, $memberId, 'Your session booking is confirmed.', 'booking');
        flash('Session booked! See you there.');
        redirect('/member/bookings.php');
    } catch (PDOException $e) {
        flash('Could not book the session: ' . $e->getMessage(), 'error');
        redirect('/member/book.php');
    }
}

// GET — list bookable sessions joined to zone + trainer.
$sessions = db_all(
    $conn,
    "SELECT s.session_id, s.title, s.session_type, s.start_time, s.end_time, s.max_capacity,
            z.name AS zone, u.first_name AS trainer_first, u.last_name AS trainer_last,
            (SELECT COUNT(*) FROM bookings b
              WHERE b.session_id = s.session_id AND b.status = 'confirmed') AS booked
       FROM sessions s
       LEFT JOIN zones z    ON z.zone_id = s.zone_id
       LEFT JOIN trainers t ON t.trainer_id = s.trainer_id
       LEFT JOIN User u     ON u.user_id = t.trainer_id
      WHERE s.status = 'scheduled'
   ORDER BY s.start_time"
);

$page_title = 'Book a Session';
$active     = 'book';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">
        <div class="dash-head">
            <div>
                <h1>Book a Session</h1>
                <p>Pick a class or training slot below.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/member/bookings.php">My bookings</a>
        </div>

        <div class="panel">
            <?php if (!$sessions): ?>
                <p style="color:var(--color-muted)">No sessions are scheduled right now. Check back soon.</p>
            <?php else: ?>
                <form method="post" action="<?= BASE_URL ?>/member/book.php">
                    <div class="form-group">
                        <label class="form-label">Available sessions</label>
                        <?php foreach ($sessions as $i => $s):
                            $full = $s['max_capacity'] > 0 && $s['booked'] >= $s['max_capacity'];
                        ?>
                            <label class="slot" style="<?= $full ? 'opacity:.5' : '' ?>">
                                <input type="radio" name="session_id" value="<?= (int) $s['session_id'] ?>"
                                       <?= $i === 0 && !$full ? 'checked' : '' ?> <?= $full ? 'disabled' : '' ?>>
                                <span class="slot-time"><?= e(date('g:i a', strtotime($s['start_time']))) ?></span>
                                <span class="slot-zone">·
                                    <?= e($s['title']) ?>
                                    <?php if ($s['zone']): ?> · <?= e($s['zone']) ?><?php endif; ?>
                                    <?php if ($s['trainer_first']): ?>
                                        · <?= e($s['trainer_first'] . ' ' . $s['trainer_last']) ?>
                                    <?php endif; ?>
                                    <?= $full ? ' · FULL' : ' · ' . (int) $s['booked'] . '/' . (int) $s['max_capacity'] ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Confirm booking</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

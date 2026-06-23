<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('trainer');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me = current_user();
$trainerId = $me['id'];

$sessions = db_all($conn,
    "SELECT s.session_id, s.title, s.start_time, s.max_capacity, z.name AS zone,
            (SELECT COUNT(*) FROM bookings b
              WHERE b.session_id = s.session_id AND b.status = 'confirmed') AS booked
       FROM sessions s LEFT JOIN zones z ON z.zone_id = s.zone_id
      WHERE s.trainer_id = ? AND s.status = 'scheduled'
   ORDER BY s.start_time", [$trainerId]);

$totalBookings = (int) (db_one($conn,
    "SELECT COUNT(*) AS c FROM bookings b JOIN sessions s ON s.session_id = b.session_id
      WHERE s.trainer_id = ? AND b.status = 'confirmed'", [$trainerId])['c'] ?? 0);

$page_title = 'Trainer Dashboard'; $active = 'dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Hi, <?= e(explode(' ', $me['name'])[0]) ?> 👋</h1>
           <p>Your upcoming sessions and bookings.</p></div>
      <a class="btn btn-ghost" href="<?= BASE_URL ?>/trainer/sessions.php">My sessions</a>
    </div>

    <div class="grid-cards" style="margin-bottom:1.5rem">
      <div class="card"><div class="icon">📅</div><h3><?= count($sessions) ?></h3><p>Upcoming sessions</p></div>
      <div class="card"><div class="icon">👥</div><h3><?= $totalBookings ?></h3><p>Confirmed bookings</p></div>
    </div>

    <div class="panel">
      <h2>Upcoming sessions</h2>
      <?php if (!$sessions): ?>
        <p style="color:var(--color-muted)">No sessions scheduled.</p>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Session</th><th>Zone</th><th>When</th><th>Booked</th></tr></thead>
          <tbody>
          <?php foreach ($sessions as $s): ?>
            <tr>
              <td><?= e($s['title']) ?></td>
              <td><?= e($s['zone'] ?? '—') ?></td>
              <td><?= e(date('D, j M · g:i a', strtotime($s['start_time']))) ?></td>
              <td><?= (int) $s['booked'] ?> / <?= (int) $s['max_capacity'] ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
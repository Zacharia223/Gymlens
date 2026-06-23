<?php
/**
 * Gymlens — Admin: Reports
 * Simple activity snapshot pulled from the database.
 * Access: admins only.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('admin');
require_once __DIR__ . '/../../config/database.php'; // $conn

$count = function (string $sql) use ($conn): int {
    return (int) (db_one($conn, $sql)['c'] ?? 0);
};
$totalBookings = $count("SELECT COUNT(*) c FROM bookings WHERE status = 'confirmed'");
$cancelled     = $count("SELECT COUNT(*) c FROM bookings WHERE status = 'cancelled'");
$checkinsToday = $count("SELECT COUNT(*) c FROM checkins WHERE DATE(checked_in_at) = CURDATE()");

$busiest = db_all($conn,
    "SELECT name, capacity, current_count FROM zones ORDER BY current_count DESC LIMIT 5");

$page_title = 'Reports';
$active     = 'reports';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Reports</h1><p>Activity at a glance.</p></div>
    </div>

    <div class="grid-cards" style="margin-bottom:1.5rem">
      <div class="card"><div class="icon">📅</div><h3><?= $totalBookings ?></h3><p>Confirmed bookings</p></div>
      <div class="card"><div class="icon">🚫</div><h3><?= $cancelled ?></h3><p>Cancelled bookings</p></div>
      <div class="card"><div class="icon">✅</div><h3><?= $checkinsToday ?></h3><p>Check-ins today</p></div>
    </div>

    <div class="panel">
      <h2>Busiest zones</h2>
      <?php if (!$busiest): ?>
        <p style="color:var(--color-muted)">No zone data yet.</p>
      <?php else: foreach ($busiest as $z):
        $cap = (int) $z['capacity']; $cur = (int) $z['current_count'];
        $pct = $cap > 0 ? min(100, round($cur / $cap * 100)) : 0; ?>
        <div class="occ-block">
          <div class="occ-label">
            <span><?= e($z['name']) ?></span>
            <span class="count"><?= $cur ?> / <?= $cap ?> · <?= $pct ?>%</span>
          </div>
          <div class="bar"><span style="width:<?= $pct ?>%"></span></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<?php
/**
 * Gymlens — Admin Dashboard
 * Access: admins only.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('admin');
require_once __DIR__ . '/../../config/database.php'; // $conn

$count = function (string $sql) use ($conn): int {
    return (int) (db_one($conn, $sql)['c'] ?? 0);
};
$members  = $count("SELECT COUNT(*) c FROM members");
$trainers = $count("SELECT COUNT(*) c FROM trainers");
$zones    = $count("SELECT COUNT(*) c FROM zones");
$today    = $count("SELECT COUNT(*) c FROM bookings WHERE DATE(booked_at) = CURDATE()");

$page_title = 'Admin Dashboard';
$active     = 'dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Admin Overview</h1><p>Your gym at a glance.</p></div>
      <a class="btn btn-ghost" href="<?= BASE_URL ?>/admin/users.php">Manage users</a>
    </div>
    <div class="grid-cards">
      <div class="card"><div class="icon">👥</div><h3><?= $members ?></h3><p>Members</p></div>
      <div class="card"><div class="icon">🏋️</div><h3><?= $trainers ?></h3><p>Trainers</p></div>
      <div class="card"><div class="icon">📍</div><h3><?= $zones ?></h3><p>Zones</p></div>
      <div class="card"><div class="icon">📅</div><h3><?= $today ?></h3><p>Bookings today</p></div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

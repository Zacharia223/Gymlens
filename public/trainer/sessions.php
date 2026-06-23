<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('trainer');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me = current_user();
$trainerId = $me['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $zoneId = (int) ($_POST['zone_id'] ?? 0);
    $start = $_POST['start_time'] ?? '';
    $cap = (int) ($_POST['max_capacity'] ?? 0);
    if ($title && $start) {
        try {
            $conn->prepare(
              "INSERT INTO sessions (trainer_id, zone_id, title, session_type, start_time, end_time, max_capacity, status)
               VALUES (?, ?, ?, 'class', ?, DATE_ADD(?, INTERVAL 1 HOUR), ?, 'scheduled')"
            )->execute([$trainerId, $zoneId ?: null, $title, $start, $start, $cap]);
            flash('Session created.');
        } catch (PDOException $e) { flash('Could not create session.', 'error'); }
    } else { flash('Title and start time are required.', 'error'); }
    redirect('/trainer/sessions.php');
}

$zones = db_all($conn, 'SELECT zone_id, name FROM zones ORDER BY name');
$sessions = db_all($conn,
    "SELECT s.title, s.start_time, z.name AS zone, s.max_capacity
       FROM sessions s LEFT JOIN zones z ON z.zone_id = s.zone_id
      WHERE s.trainer_id = ? ORDER BY s.start_time DESC", [$trainerId]);

$page_title = 'My Sessions'; $active = 'sessions';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash"><div class="container">
  <div class="dash-head"><div><h1>My Sessions</h1><p>Schedule and review your classes.</p></div></div>

  <div class="panel">
    <h2>New session</h2>
    <form method="post" action="<?= BASE_URL ?>/trainer/sessions.php">
      <div class="form-group"><label class="form-label">Title</label>
        <input class="form-input" name="title" required></div>
      <div class="form-row-2">
        <div class="form-group"><label class="form-label">Zone</label>
          <select class="select" name="zone_id">
            <option value="">—</option>
            <?php foreach ($zones as $z): ?>
              <option value="<?= (int) $z['zone_id'] ?>"><?= e($z['name']) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="form-group"><label class="form-label">Max capacity</label>
          <input class="form-input" type="number" name="max_capacity" value="15"></div>
      </div>
      <div class="form-group"><label class="form-label">Start time</label>
        <input class="form-input" type="datetime-local" name="start_time" required></div>
      <button class="btn btn-primary">Create session</button>
    </form>
  </div>

  <div class="panel">
    <h2>All sessions</h2>
    <?php if (!$sessions): ?><p style="color:var(--color-muted)">No sessions yet.</p>
    <?php else: ?>
      <table class="data-table"><thead><tr><th>Title</th><th>Zone</th><th>When</th><th>Cap</th></tr></thead><tbody>
      <?php foreach ($sessions as $s): ?>
        <tr><td><?= e($s['title']) ?></td><td><?= e($s['zone'] ?? '—') ?></td>
            <td><?= e(date('j M · g:i a', strtotime($s['start_time']))) ?></td>
            <td><?= (int) $s['max_capacity'] ?></td></tr>
      <?php endforeach; ?></tbody></table>
    <?php endif; ?>
  </div>
</div></section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
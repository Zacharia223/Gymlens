<?php
/**
 * Gymlens — Admin: Occupancy
 * View every zone and correct the live count if needed.
 * Access: admins only.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('admin');
require_once __DIR__ . '/../../config/database.php'; // $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneId = (int) ($_POST['zone_id'] ?? 0);
    $count  = max(0, (int) ($_POST['current_count'] ?? 0));
    try {
        $conn->prepare('UPDATE zones SET current_count = LEAST(?, capacity) WHERE zone_id = ?')
             ->execute([$count, $zoneId]);
        flash('Occupancy updated.');
    } catch (PDOException $e) {
        flash('Could not update occupancy.', 'error');
    }
    redirect('/admin/occupancy.php');
}

$zones = db_all($conn, 'SELECT zone_id, name, capacity, current_count, location FROM zones ORDER BY zone_id');

$page_title = 'Occupancy';
$active     = 'occupancy';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Manage Occupancy</h1><p>Adjust the live count for any zone.</p></div>
    </div>
    <div class="panel">
      <?php if (!$zones): ?>
        <p style="color:var(--color-muted)">No zones yet. Run the seed tool to add some.</p>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Zone</th><th>Location</th><th>Capacity</th><th>Current</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($zones as $z): ?>
            <tr>
              <td><?= e($z['name']) ?></td>
              <td><?= e($z['location'] ?? '—') ?></td>
              <td><?= (int) $z['capacity'] ?></td>
              <td><?= (int) $z['current_count'] ?></td>
              <td style="text-align:right">
                <form method="post" action="<?= BASE_URL ?>/admin/occupancy.php"
                      style="display:flex;gap:.5rem;justify-content:flex-end">
                  <input type="hidden" name="zone_id" value="<?= (int) $z['zone_id'] ?>">
                  <input class="form-input" style="width:90px" type="number" min="0"
                         name="current_count" value="<?= (int) $z['current_count'] ?>">
                  <button type="submit" class="btn btn-ghost btn-sm">Set</button>
                </form>
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

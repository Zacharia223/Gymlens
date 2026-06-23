<?php
/**
 * Gymlens — Admin: Settings
 * Scaffold. There is no `settings` table in the schema yet, so this
 * page is a placeholder for future gym-wide configuration.
 * Access: admins only.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('admin');
require_once __DIR__ . '/../../config/database.php'; // $conn

$page_title = 'Settings';
$active     = 'settings';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Settings</h1><p>Gym configuration.</p></div>
    </div>
    <div class="panel">
      <p style="color:var(--color-muted)">
        There is no <code>settings</code> table in the schema yet. To finish this page,
        add a <code>settings</code> table (for example a single config row, or key/value
        pairs) plus a migration, then load and save the values here using the same form
        pattern as <code>admin/users.php</code>.
      </p>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

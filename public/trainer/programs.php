<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('trainer');
require_once __DIR__ . '/../../config/database.php'; // $conn

$page_title = 'Programs'; $active = 'programs';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash"><div class="container">
  <div class="dash-head"><div><h1>Training Programs</h1>
    <p>Build multi-week programs for your members.</p></div></div>
  <div class="panel">
    <p style="color:var(--color-muted)">
      No <code>programs</code> table exists in the schema yet. To finish this page,
      add a <code>programs</code> table (e.g. id, trainer_id, name, description, weeks)
      and a migration, then list/create programs here following the pattern in
      <code>trainer/sessions.php</code>.
    </p>
  </div>
</div></section>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php
/**
 * Gymlens — Admin: Users
 * List all accounts and change their role (member / trainer / admin).
 * Access: admins only.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('admin');
require_once __DIR__ . '/../../config/database.php'; // $conn

$me = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $role   = $_POST['role'] ?? '';

    if ($userId === (int) $me['id']) {
        flash('You can’t change your own role.', 'error');
    } elseif (in_array($role, ['member', 'trainer', 'admin'], true) && $userId) {
        try {
            $conn->prepare('UPDATE User SET role = ? WHERE user_id = ?')->execute([$role, $userId]);
            // make sure the matching detail row exists for the new role
            if ($role === 'trainer') {
                $conn->prepare('INSERT IGNORE INTO trainers (trainer_id) VALUES (?)')->execute([$userId]);
            } elseif ($role === 'admin') {
                $conn->prepare('INSERT IGNORE INTO administrators (admin_id) VALUES (?)')->execute([$userId]);
            } elseif ($role === 'member') {
                $conn->prepare('INSERT IGNORE INTO members (member_id, join_date, expiry_date)
                                VALUES (?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR))')->execute([$userId]);
            }
            flash('Role updated.');
        } catch (PDOException $e) {
            flash('Could not update role.', 'error');
        }
    } else {
        flash('Please choose a valid role.', 'error');
    }
    redirect('/admin/users.php');
}

$users = db_all($conn, 'SELECT user_id, first_name, last_name, email, role FROM User ORDER BY user_id');

$page_title = 'Users';
$active     = 'users';
require_once __DIR__ . '/../../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div><h1>Users</h1><p>Manage accounts and access levels.</p></div>
    </div>
    <div class="panel">
      <?php if (!$users): ?>
        <p style="color:var(--color-muted)">No users yet.</p>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
              <td><?= e($u['email']) ?></td>
              <td><span class="badge"><?= e(ucfirst($u['role'])) ?></span></td>
              <td style="text-align:right">
                <?php if ((int) $u['user_id'] === (int) $me['id']): ?>
                  <span style="color:var(--color-muted)">You</span>
                <?php else: ?>
                  <form method="post" action="<?= BASE_URL ?>/admin/users.php"
                        style="display:flex;gap:.5rem;justify-content:flex-end">
                    <input type="hidden" name="user_id" value="<?= (int) $u['user_id'] ?>">
                    <select class="select" name="role" style="width:auto">
                      <?php foreach (['member', 'trainer', 'admin'] as $r): ?>
                        <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-ghost btn-sm">Save</button>
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

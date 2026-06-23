<?php
/**
 * Gymlens — Notifications
 * Each user sees their own notifications. Any logged-in role can view.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
require_once __DIR__ . '/../config/database.php'; // $conn

$me     = current_user();
$userId = (int) $me['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'read_all') {
            $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')
                 ->execute([$userId]);
            flash('All notifications marked as read.');
        } elseif ($action === 'read') {
            $id = (int) ($_POST['notification_id'] ?? 0);
            $conn->prepare('UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?')
                 ->execute([$id, $userId]);
        }
    } catch (PDOException $e) {
        flash('Could not update notifications.', 'error');
    }
    redirect('/notifications.php');
}

$notifications = db_all(
    $conn,
    'SELECT notification_id, message, type, sent_at, is_read
       FROM notifications WHERE user_id = ? ORDER BY sent_at DESC',
    [$userId]
);
$unread = 0;
foreach ($notifications as $n) {
    if (!$n['is_read']) { $unread++; }
}

$icons = ['info' => 'ℹ️', 'booking' => '📅', 'renewal' => '🔄', 'alert' => '⚠️'];

$page_title = 'Notifications';
$active     = 'notifications';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="dash">
  <div class="container">
    <div class="dash-head">
      <div>
        <h1>Notifications</h1>
        <p><?= $unread ? $unread . ' unread' : 'You’re all caught up.' ?></p>
      </div>
      <?php if ($unread): ?>
        <form method="post" action="<?= BASE_URL ?>/notifications.php">
          <input type="hidden" name="action" value="read_all">
          <button type="submit" class="btn btn-ghost">Mark all as read</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="panel">
      <?php if (!$notifications): ?>
        <p style="color:var(--color-muted)">No notifications yet.</p>
      <?php else: foreach ($notifications as $n): ?>
        <div class="notif <?= $n['is_read'] ? '' : 'unread' ?>">
          <span class="notif-icon"><?= $icons[$n['type']] ?? 'ℹ️' ?></span>
          <div class="notif-body">
            <p class="notif-msg"><?= e($n['message']) ?></p>
            <span class="notif-time"><?= e(date('D, j M · g:i a', strtotime($n['sent_at']))) ?></span>
          </div>
          <?php if (!$n['is_read']): ?>
            <form method="post" action="<?= BASE_URL ?>/notifications.php">
              <input type="hidden" name="action" value="read">
              <input type="hidden" name="notification_id" value="<?= (int) $n['notification_id'] ?>">
              <button type="submit" class="btn btn-ghost btn-sm">Mark read</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

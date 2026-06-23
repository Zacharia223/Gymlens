<?php
/**
 * Gymlens — Member: Live Occupancy
 * Shows how busy every zone is right now.
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
require_role('member');
require_once __DIR__ . '/../../config/database.php'; // $conn

$zones = db_all($conn, 'SELECT zone_id, name, description, capacity, current_count, location
                          FROM zones ORDER BY zone_id');

$page_title = 'Live Occupancy';
$active     = 'occupancy';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="dash">
    <div class="container">
        <div class="dash-head">
            <div>
                <h1>Live Occupancy</h1>
                <p>Real-time view of how busy each area is. <span class="live-dot">●</span> Live — updates automatically.</p>
            </div>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/member/checkin.php">Check in</a>
        </div>

        <?php if (!$zones): ?>
            <div class="panel"><p style="color:var(--color-muted)">
                No zones found yet. Run <code>tools/migrate.php</code> then <code>tools/seed.php</code> to add them.
            </p></div>
        <?php else: ?>
            <div class="panel">
                <?php foreach ($zones as $z):
                    $cap = (int) $z['capacity'];
                    $cur = (int) $z['current_count'];
                    $pct = $cap > 0 ? min(100, round($cur / $cap * 100)) : 0;
                ?>
                    <div class="occ-block" data-zone="<?= (int) $z['zone_id'] ?>">
                        <div class="occ-label">
                            <span><strong><?= e($z['name']) ?></strong>
                                <?php if ($z['location']): ?>
                                    <span style="color:var(--color-muted)">· <?= e($z['location']) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="count occ-count"><?= $cur ?> / <?= $cap ?> · <?= $pct ?>%</span>
                        </div>
                        <div class="bar"><span style="width:<?= $pct ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Poll the occupancy API and update the bars live, no page reload.
(function () {
    const endpoint = '<?= BASE_URL ?>/api/occupancy.php';

    async function refresh() {
        try {
            const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            (data.zones || []).forEach(function (z) {
                const row = document.querySelector('.occ-block[data-zone="' + z.id + '"]');
                if (!row) return;
                const fill  = row.querySelector('.bar > span');
                const count = row.querySelector('.occ-count');
                if (fill)  fill.style.width = z.pct + '%';
                if (count) count.textContent = z.current + ' / ' + z.capacity + ' · ' + z.pct + '%';
            });
        } catch (e) { /* ignore transient errors */ }
    }

    setInterval(refresh, 5000); // every 5 seconds
})();
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

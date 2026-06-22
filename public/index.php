<?php
/**
 * Gymlens — Landing / Home page (public web root)
 */
$page_title = 'Home';
$active     = 'home';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">Smart Gym Management</span>
            <h1>Run your gym <span class="grad">effortlessly</span>, not in spreadsheets.</h1>
            <p class="lead">
                Gymlens brings members, trainers, bookings and live floor occupancy
                together in one clean dashboard — so you spend less time on admin
                and more time growing your gym.
            </p>

            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Get started free →</a>
                <a class="btn btn-ghost" href="#features">See features</a>
            </div>

            <div class="hero-stats">
                <div>
                    <div class="num">1,200+</div>
                    <div class="label">Active members</div>
                </div>
                <div>
                    <div class="num">98%</div>
                    <div class="label">Check-in accuracy</div>
                </div>
                <div>
                    <div class="num">24/7</div>
                    <div class="label">Live occupancy</div>
                </div>
            </div>
        </div>

        <!-- Live occupancy preview card -->
        <aside class="hero-card">
            <h3>🟢 Live floor occupancy</h3>
            <div class="occupancy-row">
                <span>Weights area</span>
                <div class="bar"><span style="width:78%"></span></div>
                <strong>78%</strong>
            </div>
            <div class="occupancy-row">
                <span>Cardio zone</span>
                <div class="bar"><span style="width:45%"></span></div>
                <strong>45%</strong>
            </div>
            <div class="occupancy-row">
                <span>Studio</span>
                <div class="bar"><span style="width:30%"></span></div>
                <strong>30%</strong>
            </div>
            <div class="occupancy-row">
                <span>Pool</span>
                <div class="bar"><span style="width:60%"></span></div>
                <strong>60%</strong>
            </div>
        </aside>
    </div>
</section>

<!-- ===================== FEATURES ===================== -->
<section class="section" id="features">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Everything in one place</span>
            <h2>One platform for the whole gym</h2>
            <p>From the front desk to the training floor, Gymlens replaces the
               patchwork of tools you're juggling today.</p>
        </div>

        <div class="grid-cards">
            <a class="card" href="<?= BASE_URL ?>/admin/users.php">
                <div class="icon">👥</div>
                <h3>Member management</h3>
                <p>Sign-ups, profiles, memberships and renewals — track every member's journey from day one.</p>
                <span class="card-link">Open Members →</span>
            </a>
            <a class="card" href="<?= BASE_URL ?>/trainer/sessions.php">
                <div class="icon">🏋️</div>
                <h3>Trainers &amp; classes</h3>
                <p>Assign trainers, manage schedules and let members book sessions in a couple of taps.</p>
                <span class="card-link">Open Sessions →</span>
            </a>
            <a class="card" href="<?= BASE_URL ?>/member/book.php">
                <div class="icon">📅</div>
                <h3>Bookings</h3>
                <p>Real-time class and equipment booking that prevents double-booking and no-shows.</p>
                <span class="card-link">Open Bookings →</span>
            </a>
            <a class="card" href="<?= BASE_URL ?>/member/occupancy.php">
                <div class="icon">📊</div>
                <h3>Live occupancy</h3>
                <p>See exactly how busy each zone is right now, and spot your quiet and peak hours.</p>
                <span class="card-link">Open Occupancy →</span>
            </a>
            <a class="card" href="<?= BASE_URL ?>/notifications.php">
                <div class="icon">🔔</div>
                <h3>Notifications</h3>
                <p>Automatic reminders for renewals, bookings and announcements keep members in the loop.</p>
                <span class="card-link">Open Notifications →</span>
            </a>
            <a class="card" href="<?= BASE_URL ?>/admin/reports.php">
                <div class="icon">📈</div>
                <h3>Reports</h3>
                <p>Revenue, attendance and growth insights so you can make decisions with confidence.</p>
                <span class="card-link">Open Reports →</span>
            </a>
        </div>
    </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="section">
    <div class="container">
        <div class="cta-band">
            <h2>Ready to modernise your gym?</h2>
            <p>Set up Gymlens in minutes — no credit card required.</p>
            <a class="btn" href="<?= BASE_URL ?>/register.php">Create your free account</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

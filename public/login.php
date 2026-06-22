<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// already signed in? go straight to your dashboard
if (is_logged_in()) {
    redirect(home_for_role(current_role()));
}

// consume any flash (e.g. "you don't have access to that page") so it
// shows here once and doesn't carry over to the next page.
$flash = take_flash();
$error = $flash && $flash['type'] === 'error' ? $flash['message'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php'; // $conn
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } elseif (login_user($conn, $email, $password)) {
        redirect(home_for_role(current_role()));
    } else {
        $error = 'Incorrect email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in · Gymlens</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="auth-body">

    <header class="auth-topbar">
        <a class="brand" href="<?= BASE_URL ?>/index.php">
            <span class="dot">●</span> Gymlens
        </a>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p class="auth-sub">Sign in to continue</p>

            <?php if ($error): ?>
                <div class="form-alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/login.php" novalidate>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" type="email" id="email" name="email"
                           placeholder="you@strathmore.edu" autocomplete="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password"
                           placeholder="••••••••" autocomplete="current-password" required>
                </div>

                <div class="form-row-end">
                    <a class="form-link" href="#">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <p class="auth-foot">
                Don’t have an account? <a href="<?= BASE_URL ?>/register.php">Sign up</a>
            </p>
        </div>
    </main>

</body>
</html>

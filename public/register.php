<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Gymlens/public');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: validate + insert into users (includes/auth.php), then
    // header('Location: '.BASE_URL.'/login.php');
    $error = 'Sign-up isn’t connected to the database yet — coming next.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up · Gymlens</title>
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
            <h1>Create your account</h1>
            <p class="auth-sub">Join Gymlens </p>

            <?php if ($error): ?>
                <div class="form-alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/register.php" novalidate>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First name</label>
                        <input class="form-input" type="text" id="first_name" name="first_name"
                               placeholder="Zacharia" autocomplete="given-name"
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last name</label>
                        <input class="form-input" type="text" id="last_name" name="last_name"
                               placeholder="Ogega" autocomplete="family-name"
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" type="email" id="email" name="email"
                           placeholder="you@strathmore.edu" autocomplete="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone <span style="opacity:.6">(optional)</span></label>
                    <input class="form-input" type="tel" id="phone" name="phone"
                           placeholder="+254 7xx xxx xxx" autocomplete="tel"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password"
                           placeholder="At least 8 characters" autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm password</label>
                    <input class="form-input" type="password" id="confirm_password" name="confirm_password"
                           placeholder="Re-enter your password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>

            <p class="auth-foot">
                Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a>
            </p>
        </div>
    </main>

</body>
</html>

<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (is_logged_in()) {
    redirect(home_for_role(current_role()));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php'; // $conn

    $first   = trim($_POST['first_name'] ?? '');
    $last    = trim($_POST['last_name'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $phone   = trim($_POST['phone'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // --- Validation ---
    $name_pattern = "/^[A-Za-z][A-Za-z '\\-]{1,49}$/"; // letters, space, apostrophe, hyphen

    if ($first === '' || $last === '' || $email === '' || $pass === '') {
        $error = 'Please fill in all the required fields.';
    } elseif (!preg_match($name_pattern, $first) || !preg_match($name_pattern, $last)) {
        $error = 'Names should be 2–50 letters (letters, spaces, apostrophes or hyphens only).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        $error = 'Please enter a valid email address.';
    } elseif ($phone !== '' && !preg_match('/^\+?[0-9 ]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number, or leave it blank.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $pass)
           || !preg_match('/[a-z]/', $pass)
           || !preg_match('/[0-9]/', $pass)) {
        $error = 'Password must include an uppercase letter, a lowercase letter and a number.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords don’t match.';
    } else {
        try {
            $exists = db_one($conn, 'SELECT user_id FROM User WHERE email = ?', [$email]);
            if ($exists) {
                $error = 'An account with that email already exists.';
            } else {
                // New sign-ups are always members.
                $conn->beginTransaction();
                $stmt = $conn->prepare(
                    'INSERT INTO User (first_name, last_name, email, password_hash, phone, role)
                     VALUES (?, ?, ?, ?, ?, "member")'
                );
                $stmt->execute([$first, $last, $email, password_hash($pass, PASSWORD_DEFAULT), $phone]);
                $userId = (int) $conn->lastInsertId();
                $conn->prepare(
                    'INSERT INTO members (member_id, membership_type, join_date, expiry_date, status)
                     VALUES (?, "basic", CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), "active")'
                )->execute([$userId]);
                $conn->commit();

                login_user($conn, $email, $pass);
                flash('Welcome to Gymlens, ' . $first . '!');
                redirect(home_for_role('member'));
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $error = 'Could not create your account. Is the database set up? (run tools/migrate.php)';
        }
    }
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

            <form method="post" action="<?= BASE_URL ?>/register.php">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First name</label>
                        <input class="form-input" type="text" id="first_name" name="first_name"
                               placeholder="Zacharia" autocomplete="given-name"
                               minlength="2" maxlength="50" pattern="[A-Za-z][A-Za-z '\-]{1,49}"
                               title="2–50 letters; spaces, apostrophes or hyphens allowed"
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last name</label>
                        <input class="form-input" type="text" id="last_name" name="last_name"
                               placeholder="Ogega" autocomplete="family-name"
                               minlength="2" maxlength="50" pattern="[A-Za-z][A-Za-z '\-]{1,49}"
                               title="2–50 letters; spaces, apostrophes or hyphens allowed"
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" type="email" id="email" name="email"
                           placeholder="you@strathmore.edu" autocomplete="email" maxlength="50"
                           title="Enter a valid email address, e.g. you@strathmore.edu"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone <span style="opacity:.6">(optional)</span></label>
                    <input class="form-input" type="tel" id="phone" name="phone"
                           placeholder="+254 7xx xxx xxx" autocomplete="tel"
                           pattern="\+?[0-9 ]{7,20}" title="7–20 digits; an optional leading + is allowed"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password"
                           placeholder="At least 8 characters" autocomplete="new-password"
                           minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}"
                           title="At least 8 characters, with an uppercase letter, a lowercase letter and a number"
                           required>
                    <p class="form-hint">At least 8 characters, including an uppercase letter, a lowercase letter and a number.</p>
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

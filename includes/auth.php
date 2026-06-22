<?php
/**
 * Gymlens — authentication & role-based access control.
 *
 * The logged-in user lives in $_SESSION['user'] as:
 *     ['id' => int, 'name' => string, 'email' => string, 'role' => 'member'|'trainer'|'admin']
 *
 * Pages protect themselves by calling require_login() or require_role(...)
 * at the very top, before any output.
 */

/** All roles known to the app, and the home page each lands on. */
const ROLE_HOMES = [
    'member'  => '/member/dashboard.php',
    'trainer' => '/trainer/dashboard.php',
    'admin'   => '/admin/dashboard.php',
];

/** The currently logged-in user array, or null. */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Is anyone logged in? */
function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

/** The current user's role, or '' if not logged in. */
function current_role(): string
{
    return $_SESSION['user']['role'] ?? '';
}

/**
 * Can the current user access something restricted to $roles?
 * admin is treated as a superuser and may access everything.
 *
 * @param string|array $roles One role or a list of allowed roles.
 */
function user_can($roles): bool
{
    if (!is_logged_in()) {
        return false;
    }
    $role = current_role();
    if ($role === 'admin') {
        return true;
    }
    $roles = (array) $roles;
    return in_array($role, $roles, true);
}

/** Require a logged-in user; otherwise send them to the login page. */
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

/**
 * Require one of $roles. Not logged in -> login page.
 * Logged in but wrong role -> their own dashboard (no peeking).
 *
 * @param string|array $roles
 */
function require_role($roles): void
{
    require_login();
    if (!user_can($roles)) {
        flash('You don’t have access to that page.', 'error');
        redirect(ROLE_HOMES[current_role()] ?? '/index.php');
    }
}

/**
 * Verify credentials against the User table and start a session.
 * Returns true on success, false on bad email/password.
 */
function login_user(PDO $conn, string $email, string $password): bool
{
    $user = db_one(
        $conn,
        'SELECT user_id, first_name, last_name, email, password_hash, role
           FROM User WHERE email = ? LIMIT 1',
        [$email]
    );

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id'    => (int) $user['user_id'],
        'name'  => trim($user['first_name'] . ' ' . $user['last_name']),
        'email' => $user['email'],
        'role'  => $user['role'],
    ];

    // best-effort: stamp the last login time
    try {
        $conn->prepare('UPDATE User SET last_login = NOW() WHERE user_id = ?')
             ->execute([$user['user_id']]);
    } catch (PDOException $e) {
        // ignore if column/table not present
    }

    return true;
}

/** Log out and clear the session. */
function logout_user(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/** Where should this role go after logging in? */
function home_for_role(string $role): string
{
    return ROLE_HOMES[$role] ?? '/index.php';
}

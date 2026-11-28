<?php
/** Session-based admin authentication. */

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_login(): void
{
    if (!current_admin()) {
        redirect('login.php');
    }
}

/** Simple in-session failed-attempt throttle: slows down password guessing
 * beyond what the fixed usleep delay alone provides, without needing a
 * database table or external store. Resets on success or after the window. */
function login_throttle_wait(): void
{
    $window = 300; // 5 minutes
    $now = time();
    $attempts = $_SESSION['login_attempts'] ?? [];
    $attempts = array_filter($attempts, fn ($t) => $t > $now - $window);

    $count = count($attempts);
    if ($count >= 5) {
        // Escalating delay beyond 5 failed attempts in the window, capped at 8s.
        usleep(min(8000000, ($count - 4) * 1000000));
    }

    $attempts[] = $now;
    $_SESSION['login_attempts'] = $attempts;
}

function attempt_login(string $email, string $password): bool
{
    login_throttle_wait();

    $stmt = db()->prepare('SELECT id, name, email, password_hash FROM admin_users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        usleep(300000); // slow down brute-force guessing a little
        return false;
    }

    unset($_SESSION['login_attempts']);
    session_regenerate_id(true);
    $_SESSION['admin'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']];
    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function any_admin_exists(): bool
{
    return (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;
}

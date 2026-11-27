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

function attempt_login(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, name, email, password_hash FROM admin_users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        usleep(300000); // slow down brute-force guessing a little
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']];
    return true;
}

function logout(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function any_admin_exists(): bool
{
    return (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;
}

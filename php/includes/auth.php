<?php
require_once __DIR__ . '/db.php';

function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('hazleton_admin');
        session_set_cookie_params([
            'lifetime' => 60 * 60 * 12,
            'path' => '/',
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function current_admin(): ?array {
    start_session();
    return $_SESSION['admin'] ?? null;
}

function require_login(): void {
    start_session();
    if (empty($_SESSION['admin'])) {
        redirect('/admin/login.php');
    }
}

function attempt_login(string $username, string $password): bool {
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        start_session();
        session_regenerate_id(true);
        $_SESSION['admin'] = ['id' => $user['id'], 'username' => $user['username']];
        return true;
    }
    return false;
}

function update_credentials(string $username, ?string $newPassword): void {
    $admin = current_admin();
    if ($newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = db()->prepare('UPDATE admin_users SET username = ?, password_hash = ? WHERE id = ?');
        $stmt->execute([$username, $hash, $admin['id']]);
    } else {
        $stmt = db()->prepare('UPDATE admin_users SET username = ? WHERE id = ?');
        $stmt->execute([$username, $admin['id']]);
    }
    $_SESSION['admin']['username'] = $username;
}

<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function admin_start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('qvartirnik_sess');
        session_start();
    }
}

function admin_login(string $username, string $password): bool {
    $admin = DB::fetch('SELECT * FROM admins WHERE username = ?', [$username]);
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION[ADMIN_SESSION_KEY] = [
            'id'   => $admin['id'],
            'name' => $admin['name'],
            'user' => $admin['username'],
        ];
        return true;
    }
    return false;
}

function admin_logout(): void {
    unset($_SESSION[ADMIN_SESSION_KEY]);
    session_destroy();
}

function admin_check(): bool {
    admin_start_session();
    return isset($_SESSION[ADMIN_SESSION_KEY]);
}

function admin_require(): void {
    admin_start_session();
    if (!isset($_SESSION[ADMIN_SESSION_KEY])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_info(): ?array {
    return $_SESSION[ADMIN_SESSION_KEY] ?? null;
}

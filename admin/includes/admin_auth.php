<?php
/**
 * Авторизация в админ-панели. Подключайте в начале каждой защищённой страницы админки.
 */
require_once __DIR__ . '/../../includes/functions.php';

function currentAdmin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    static $admin = null;
    if ($admin === null) {
        $stmt = db()->prepare('SELECT id, username, full_name, role FROM admin_users WHERE id = :id AND is_active = 1 LIMIT 1');
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $admin = $stmt->fetch() ?: false;
    }
    return $admin ?: null;
}

function requireAdminLogin(): array
{
    $admin = currentAdmin();
    if (!$admin) {
        redirect('/admin/login.php');
    }
    return $admin;
}

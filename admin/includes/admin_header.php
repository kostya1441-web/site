<?php
/**
 * Общий layout админки. Требует, чтобы страница подключила admin_auth.php
 * и вызвала requireAdminLogin() ДО подключения этого файла.
 * Ожидает переменные: $pageTitle, $activeNav (dashboard|orders|products|categories|settings).
 */
$admin = currentAdmin();
$pageTitle = $pageTitle ?? 'Админка';
$activeNav = $activeNav ?? '';

function navClass(string $key, string $active): string
{
    return $key === $active ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — Админка «<?= e(setting('site_name')) ?>»</title>
<link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="brand">🌾 Ваш фермер — админка</div>
        <nav>
            <a href="/admin/index.php" class="<?= navClass('dashboard', $activeNav) ?>">Дашборд</a>
            <a href="/admin/orders.php" class="<?= navClass('orders', $activeNav) ?>">Заказы</a>
            <a href="/admin/products.php" class="<?= navClass('products', $activeNav) ?>">Товары</a>
            <a href="/admin/categories.php" class="<?= navClass('categories', $activeNav) ?>">Категории</a>
            <a href="/admin/settings.php" class="<?= navClass('settings', $activeNav) ?>">Настройки сайта</a>
            <a href="/index.php" target="_blank">Открыть сайт ↗</a>
        </nav>
        <div class="sidebar-footer">
            <?= e($admin['full_name'] ?? '') ?><br>
            <a href="/admin/logout.php">Выйти</a>
        </div>
    </aside>
    <div class="admin-main">
        <div class="admin-topbar">
            <h2 style="margin:0;font-size:18px;"><?= e($pageTitle) ?></h2>
        </div>
        <div class="admin-content">

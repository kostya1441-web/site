<?php
/**
 * Общий layout админки. Требует, чтобы страница подключила admin_auth.php
 * и вызвала requireAdminLogin() ДО подключения этого файла.
 * Ожидает переменные: $pageTitle, $activeNav (dashboard|orders|archive|products|categories|settings).
 */
$admin = currentAdmin();
$pageTitle = $pageTitle ?? 'Админка';
$activeNav = $activeNav ?? '';
$siteNameAdmin = setting('site_name', 'Ваш фермер');

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
<title><?= e($pageTitle) ?> — Админка «<?= e($siteNameAdmin) ?>»</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/admin/assets/css/admin.css">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/img/favicon-192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32.png">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="brand">
            <span class="logo-chip"><img src="/assets/img/logo-icon.jpg" alt="<?= e($siteNameAdmin) ?>"></span>
            <span><?= e($siteNameAdmin) ?> — админка</span>
            <button class="admin-nav-close" id="adminNavClose" aria-label="Закрыть меню"><span class="ico"><?= icon('close') ?></span></button>
        </div>
        <nav>
            <a href="/admin/index.php" class="<?= navClass('dashboard', $activeNav) ?>"><span class="ico"><?= icon('leaf') ?></span> Дашборд</a>
            <a href="/admin/orders.php" class="<?= navClass('orders', $activeNav) ?>"><span class="ico"><?= icon('cart') ?></span> Заказы</a>
            <a href="/admin/archive.php" class="<?= navClass('archive', $activeNav) ?>"><span class="ico"><?= icon('shield') ?></span> Архив заказов</a>
            <a href="/admin/products.php" class="<?= navClass('products', $activeNav) ?>"><span class="ico"><?= icon('truck') ?></span> Товары</a>
            <a href="/admin/categories.php" class="<?= navClass('categories', $activeNav) ?>"><span class="ico"><?= icon('pin') ?></span> Категории</a>
            <a href="/admin/settings.php" class="<?= navClass('settings', $activeNav) ?>"><span class="ico"><?= icon('mail') ?></span> Настройки сайта</a>
            <a href="/index.php" target="_blank"><span class="ico"><?= icon('arrow') ?></span> Открыть сайт ↗</a>
        </nav>
        <div class="sidebar-footer">
            <?= e($admin['full_name'] ?? '') ?><br>
            <a href="/admin/logout.php">Выйти</a>
        </div>
    </aside>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="admin-nav-toggle" id="adminNavToggle" aria-label="Меню"><span class="ico"><?= icon('menu') ?></span></button>
            <h2><?= e($pageTitle) ?></h2>
        </div>
        <div class="admin-content">

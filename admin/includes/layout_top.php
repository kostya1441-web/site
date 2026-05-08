<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($adminTitle ?? 'Админ-панель') ?> — <?= h(SITE_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
</head>
<body>

<?php
$user = getCurrentUser();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function adminActive(string $path): string {
    global $currentPath;
    return $currentPath === $path ? 'active' : '';
}
?>

<header class="site-header">
    <div class="container">
        <a href="/" class="logo">
            <img src="/assets/img/logo.svg" alt="<?= h(SITE_NAME) ?>" class="logo-img">
            <span class="logo-text"><?= h(SITE_NAME) ?></span>
        </a>
        <span style="font-size:12px;color:var(--text-muted);margin-left:8px;background:var(--accent-glow);border:1px solid #f9731430;padding:3px 10px;border-radius:20px;font-weight:600;color:var(--accent)">ADMIN</span>
        <div style="margin-left:auto;display:flex;align-items:center;gap:12px">
            <a href="/" style="font-size:13px;color:var(--text-muted)">← На сайт</a>
            <img src="<?= h($user['avatar']) ?>" style="width:28px;height:28px;border-radius:50%" alt="">
            <span style="font-size:13px;color:var(--text-muted)"><?= h($user['name']) ?></span>
            <a href="/auth/logout.php" style="font-size:13px;color:var(--red)">Выйти</a>
        </div>
    </div>
</header>

<div class="admin-layout">
<aside class="admin-sidebar">
    <nav class="admin-nav">
        <div class="admin-nav-section">Обзор</div>
        <a href="/admin/" class="admin-nav-link <?= adminActive('/admin/') ?>">📊 Дашборд</a>

        <div class="admin-nav-section">Модерация</div>
        <a href="/admin/bans.php" class="admin-nav-link <?= adminActive('/admin/bans.php') ?>">🔨 Баны</a>
        <a href="/admin/mutes.php" class="admin-nav-link <?= adminActive('/admin/mutes.php') ?>">🔇 Муты</a>
        <a href="/admin/players.php" class="admin-nav-link <?= adminActive('/admin/players.php') ?>">👥 Игроки</a>

        <div class="admin-nav-section">Контент</div>
        <a href="/admin/news.php" class="admin-nav-link <?= adminActive('/admin/news.php') ?>">📰 Новости</a>
        <a href="/admin/rules.php" class="admin-nav-link <?= adminActive('/admin/rules.php') ?>">📜 Правила</a>

        <?php if (isSuperAdmin()): ?>
        <div class="admin-nav-section">Настройки</div>
        <a href="/admin/settings.php" class="admin-nav-link <?= adminActive('/admin/settings.php') ?>">⚙️ Настройки</a>
        <?php endif; ?>
    </nav>
</aside>

<main class="admin-main">

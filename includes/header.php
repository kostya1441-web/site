<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= h(SITE_DESCRIPTION) ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="/" class="logo">
            <img src="/assets/img/logo.svg" alt="<?= h(SITE_NAME) ?>" class="logo-img">
            <span class="logo-text"><?= h(SITE_NAME) ?></span>
        </a>

        <nav class="main-nav">
            <a href="/" class="nav-link <?= ($activePage ?? '') === 'home'   ? 'active' : '' ?>">Главная</a>
            <a href="/rating.php" class="nav-link <?= ($activePage ?? '') === 'rating' ? 'active' : '' ?>">Рейтинг</a>
            <a href="/donate.php" class="nav-link donate-link <?= ($activePage ?? '') === 'donate' ? 'active' : '' ?>">Магазин</a>
            <a href="/rules.php" class="nav-link <?= ($activePage ?? '') === 'rules'  ? 'active' : '' ?>">Правила</a>
            <a href="/news.php" class="nav-link <?= ($activePage ?? '') === 'news'   ? 'active' : '' ?>">Новости</a>
        </nav>

        <div class="header-right">
        <?php if (isLoggedIn()): $u = getCurrentUser(); ?>
            <div class="user-menu" id="userMenu">
                <button class="user-menu-btn" id="userMenuBtn" type="button">
                    <img src="<?= h($u['avatar']) ?>" alt="" class="user-menu-avatar">
                    <span class="user-menu-name"><?= h($u['name']) ?></span>
                    <span class="user-menu-caret">▾</span>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="/profile.php">👤 Мой профиль</a>
                    <?php if (isAdmin()): ?>
                    <a href="/admin/">⚙️ Админ-панель</a>
                    <?php endif; ?>
                    <div class="divider"></div>
                    <a href="/auth/logout.php" class="danger">🚪 Выйти</a>
                </div>
            </div>
        <?php else: ?>
            <a href="/auth/steam.php" class="btn btn-steam">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.979 0C5.678 0 .511 4.86.022 11.037l6.432 2.658c.545-.371 1.203-.59 1.912-.59.063 0 .125.004.188.006l2.861-4.142V8.91c0-2.495 2.028-4.524 4.524-4.524 2.494 0 4.524 2.031 4.524 4.527s-2.03 4.525-4.524 4.525h-.105l-4.076 2.911c0 .052.004.105.004.159 0 1.875-1.515 3.396-3.39 3.396-1.635 0-3.016-1.173-3.331-2.727L.436 15.27C1.862 20.307 6.486 24 11.979 24c6.627 0 11.999-5.373 11.999-12S18.605 0 11.979 0zM7.54 18.21l-1.473-.61c.262.543.714.999 1.314 1.25 1.297.539 2.793-.076 3.332-1.375.263-.63.264-1.319.005-1.949s-.75-1.121-1.377-1.383c-.624-.26-1.29-.249-1.878-.03l1.523.63c.956.4 1.409 1.5 1.009 2.455-.397.957-1.497 1.41-2.454 1.012H7.54zm11.415-9.303c0-1.662-1.353-3.015-3.015-3.015-1.665 0-3.015 1.353-3.015 3.015 0 1.665 1.35 3.015 3.015 3.015 1.663 0 3.015-1.35 3.015-3.015zm-5.273-.005c0-1.252 1.013-2.266 2.265-2.266 1.249 0 2.266 1.014 2.266 2.266 0 1.251-1.017 2.265-2.266 2.265-1.253 0-2.265-1.014-2.265-2.265z"/></svg>
                <span>Войти через Steam</span>
            </a>
        <?php endif; ?>
        </div>

        <button class="burger" id="burger" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <a href="/" class="nav-link">Главная</a>
    <a href="/rating.php" class="nav-link">Рейтинг</a>
    <a href="/donate.php" class="nav-link donate-link">Магазин</a>
    <a href="/rules.php" class="nav-link">Правила</a>
    <a href="/news.php" class="nav-link">Новости</a>
    <?php if (isLoggedIn()): ?>
    <a href="/profile.php" class="nav-link">Мой профиль</a>
    <?php if (isAdmin()): ?><a href="/admin/" class="nav-link">Админ-панель</a><?php endif; ?>
    <a href="/auth/logout.php" class="nav-link" style="color:var(--red)">Выйти</a>
    <?php else: ?>
    <a href="/auth/steam.php" class="nav-link">Войти через Steam</a>
    <?php endif; ?>
</div>

<main class="main-content">

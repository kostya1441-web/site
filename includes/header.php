<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= h(SITE_DESCRIPTION) ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
</div>

<main class="main-content">

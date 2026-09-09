<?php
/**
 * Общая шапка сайта. Ожидает (опционально) переменную $pageTitle.
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/cart.php';

$siteName = setting('site_name', 'Ваш фермер');
$pageTitle = $pageTitle ?? $siteName;
$categoriesForMenu = getActiveCategories();
$cartCount = cartCount();
$phoneHref = e(preg_replace('/[^0-9+]/', '', setting('phone')));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — <?= e($siteName) ?></title>
<meta name="description" content="<?= e(setting('site_tagline')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/img/favicon-192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32.png">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
</head>
<body>
<header class="site-header">
    <div class="topbar">
        <div class="container topbar-inner">
            <a class="topbar-item" href="/contacts.php">
                <span class="ico"><?= icon('pin') ?></span><?= e(setting('address')) ?>
            </a>
            <span class="topbar-item topbar-hide-sm">
                <span class="ico"><?= icon('clock') ?></span><?= e(setting('work_hours')) ?>
            </span>
            <span class="topbar-spacer"></span>
            <a class="topbar-item topbar-phone" href="tel:<?= $phoneHref ?>">
                <span class="ico"><?= icon('phone') ?></span><?= e(setting('phone')) ?>
            </a>
        </div>
    </div>

    <div class="header-main">
        <div class="container header-inner">
            <a href="/index.php" class="logo">
                <img src="/assets/img/logo-icon.jpg" alt="<?= e($siteName) ?>" class="logo-img">
                <span class="logo-text"><?= e($siteName) ?></span>
            </a>

            <nav class="main-nav" id="mainNav">
                <div class="main-nav-head">
                    <span class="logo logo-sm"><img src="/assets/img/logo-icon.jpg" alt="<?= e($siteName) ?>" class="logo-img"><?= e($siteName) ?></span>
                    <button class="nav-close" id="navClose" aria-label="Закрыть меню"><span class="ico"><?= icon('close') ?></span></button>
                </div>
                <ul>
                    <li><a href="/index.php">Главная</a></li>
                    <li><a href="/catalog.php">Каталог</a></li>
                    <li><a href="/about.php">О нас</a></li>
                    <li><a href="/contacts.php">Контакты</a></li>
                </ul>
                <div class="main-nav-foot">
                    <a href="tel:<?= $phoneHref ?>" class="btn btn-accent nav-call-btn"><span class="ico"><?= icon('phone') ?></span> <?= e(setting('phone')) ?></a>
                </div>
            </nav>

            <div class="header-actions">
                <a href="/cart.php" class="cart-link">
                    <span class="ico"><?= icon('cart') ?></span>
                    <span class="cart-label">Корзина</span>
                    <span class="cart-badge" id="cartBadge" <?= $cartCount > 0 ? '' : 'style="display:none;"' ?>><?= (int)$cartCount ?></span>
                </a>
                <button class="nav-toggle" id="navToggle" aria-label="Меню"><span class="ico"><?= icon('menu') ?></span></button>
            </div>
        </div>
    </div>

    <?php if (!empty($categoriesForMenu)): ?>
    <div class="sub-nav">
        <div class="container sub-nav-inner">
            <?php foreach ($categoriesForMenu as $cat): ?>
                <a href="/catalog.php?cat=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</header>
<div class="nav-backdrop" id="navBackdrop"></div>
<main>

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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — <?= e($siteName) ?></title>
<meta name="description" content="<?= e(setting('site_tagline')) ?>">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="/index.php" class="logo">
            <span class="logo-icon">🌾</span>
            <span class="logo-text"><?= e($siteName) ?></span>
        </a>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="/index.php">Главная</a></li>
                <li><a href="/catalog.php">Каталог</a></li>
                <li><a href="/about.php">О нас</a></li>
                <li><a href="/contacts.php">Контакты</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('phone'))) ?>" class="header-phone">
                <?= e(setting('phone')) ?>
            </a>
            <a href="/cart.php" class="cart-link">
                🛒 Корзина
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge" id="cartBadge"><?= (int)$cartCount ?></span>
                <?php endif; ?>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Меню">☰</button>
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
<main>

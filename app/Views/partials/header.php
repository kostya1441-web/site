<?php
$settings  = $settings ?? [];
$cartCount = $cartCount ?? 0;
$navCategories = \App\Models\Category::active();
?>
<div class="topline">
    <div class="container topline__inner">
        <span class="topline__item topline__item--hide-sm">🚚 <?= e($settings['delivery_text'] ?? '') ?></span>
        <span class="topline__item">🕒 <?= e($settings['work_hours'] ?? '') ?></span>
    </div>
</div>

<header class="header" id="header">
    <div class="container header__inner">
        <button class="burger" type="button" aria-label="Меню" aria-expanded="false" data-menu-toggle>
            <span></span><span></span><span></span>
        </button>

        <a class="logo" href="<?= u('/') ?>">
            <span class="logo__mark" aria-hidden="true">🌾</span>
            <span class="logo__text">
                <strong>Ваш фермер</strong>
                <small>Новокузнецк</small>
            </span>
        </a>

        <form class="search" action="<?= u('/catalog') ?>" method="get" role="search">
            <input type="search" name="q" placeholder="Поиск: говядина, облепиха, тушёнка…"
                   value="<?= e((string) ($_GET['q'] ?? '')) ?>" aria-label="Поиск по каталогу">
            <button type="submit" aria-label="Найти">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            </button>
        </form>

        <div class="header__actions">
            <a class="header__phone" href="<?= e(phone_link($settings['phone'] ?? '')) ?>">
                <span><?= e($settings['phone'] ?? '') ?></span>
            </a>
            <a class="cart-button" href="<?= u('/cart') ?>" data-cart-link>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.55L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
                <span class="cart-button__label">Корзина</span>
                <span class="cart-button__count<?= $cartCount ? '' : ' is-empty' ?>" data-cart-count><?= (int) $cartCount ?></span>
            </a>
        </div>
    </div>

    <nav class="nav" id="nav" data-menu>
        <div class="container nav__inner">
            <a class="nav__link<?= is_active('/') ? ' is-active' : '' ?>" href="<?= u('/') ?>">Главная</a>
            <div class="nav__dropdown">
                <a class="nav__link<?= is_active('/catalog') ? ' is-active' : '' ?>" href="<?= u('/catalog') ?>">Каталог</a>
                <?php if ($navCategories): ?>
                    <div class="nav__menu">
                        <?php foreach ($navCategories as $category): ?>
                            <a href="<?= u('/') ?>catalog/<?= e($category['slug']) ?>">
                                <span><?= e($category['icon'] ?: '•') ?></span>
                                <?= e($category['name']) ?>
                                <em><?= (int) $category['products_count'] ?></em>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <a class="nav__link<?= is_active('/delivery') ? ' is-active' : '' ?>" href="<?= u('/delivery') ?>">Доставка и оплата</a>
            <a class="nav__link<?= is_active('/about') ? ' is-active' : '' ?>" href="<?= u('/about') ?>">О нас</a>
            <a class="nav__link<?= is_active('/contacts') ? ' is-active' : '' ?>" href="<?= u('/contacts') ?>">Контакты</a>
            <a class="nav__link nav__link--mobile-phone" href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
        </div>
    </nav>
</header>

<?php \App\Core\View::partial('partials/flash'); ?>

<?php
use App\Core\Auth;

$admin = Auth::user();
$newOrders   = (int) \App\Core\Database::instance()->value("SELECT COUNT(*) FROM orders WHERE status = 'new'");
$newMessages = \App\Models\Message::countNew();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Админка') ?> — Ваш фермер</title>
    <link rel="icon" href="<?= u('/assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
<header class="admin-top">
    <button class="burger burger--admin" type="button" aria-label="Меню" data-admin-menu>
        <span></span><span></span><span></span>
    </button>
    <a class="admin-top__logo" href="<?= u('/admin') ?>">🌾 Ваш фермер <small>админка</small></a>
    <div class="admin-top__right">
        <a class="admin-top__site" href="<?= u('/') ?>" target="_blank" rel="noopener">Открыть сайт ↗</a>
        <span class="admin-top__user"><?= e($admin['name'] ?? 'admin') ?></span>
        <form method="post" action="<?= u('/admin/logout') ?>">
            <?= csrf_field() ?>
            <button class="admin-top__logout" type="submit">Выйти</button>
        </form>
    </div>
</header>

<div class="admin-layout">
    <aside class="admin-side" data-admin-side>
        <nav>
            <a class="admin-side__link<?= is_active('/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? ' is-active' : '' ?>" href="<?= u('/admin') ?>">
                <span aria-hidden="true">📊</span> Дашборд
            </a>
            <a class="admin-side__link<?= is_active('/admin/orders') ? ' is-active' : '' ?>" href="<?= u('/admin/orders') ?>">
                <span aria-hidden="true">🧾</span> Заказы
                <?php if ($newOrders): ?><em class="admin-side__badge"><?= $newOrders ?></em><?php endif; ?>
            </a>
            <a class="admin-side__link<?= is_active('/admin/messages') ? ' is-active' : '' ?>" href="<?= u('/admin/messages') ?>">
                <span aria-hidden="true">✉️</span> Обращения
                <?php if ($newMessages): ?><em class="admin-side__badge"><?= $newMessages ?></em><?php endif; ?>
            </a>
            <a class="admin-side__link<?= is_active('/admin/products') ? ' is-active' : '' ?>" href="<?= u('/admin/products') ?>">
                <span aria-hidden="true">🥩</span> Товары
            </a>
            <a class="admin-side__link<?= is_active('/admin/categories') ? ' is-active' : '' ?>" href="<?= u('/admin/categories') ?>">
                <span aria-hidden="true">🗂️</span> Категории
            </a>
            <a class="admin-side__link<?= is_active('/admin/settings') ? ' is-active' : '' ?>" href="<?= u('/admin/settings') ?>">
                <span aria-hidden="true">⚙️</span> Настройки
            </a>
        </nav>
        <div class="admin-side__foot">
            <a href="<?= u('/admin/orders?status=new') ?>">Новых заказов: <strong><?= $newOrders ?></strong></a>
        </div>
    </aside>

    <main class="admin-main">
        <?php \App\Core\View::partial('partials/flash'); ?>

        <?php $missingTables = \App\Core\Schema::missingTables(); ?>
        <?php if ($missingTables): ?>
            <div class="alert alert--error">
                <strong>База устарела.</strong> Не хватает таблиц: <?= e(implode(', ', $missingTables)) ?>.
                Часть разделов не будет работать, пока вы не обновите базу.
                <form method="post" action="<?= u('/admin/update-database') ?>" style="margin-top:10px">
                    <?= csrf_field() ?>
                    <button class="btn btn--primary btn--sm" type="submit">Обновить базу</button>
                </form>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</div>

<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>

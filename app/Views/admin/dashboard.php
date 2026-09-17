<?php
use App\Models\Order;
/** @var array $stats */
/** @var array $chart */
$maxRevenue = max(1, max(array_column($chart, 'revenue')));
?>
<div class="admin-head">
    <div>
        <h1>Дашборд</h1>
        <p>Сводка по магазину на <?= date_ru(date('Y-m-d H:i:s')) ?></p>
    </div>
    <a class="btn btn--primary" href="<?= u('/admin/products/create') ?>">+ Добавить товар</a>
</div>

<div class="stat-grid">
    <div class="stat">
        <span class="stat__label">Новых заказов</span>
        <strong class="stat__value"><?= (int) $stats['new'] ?></strong>
        <a class="stat__link" href="<?= u('/admin/orders?status=new') ?>">Обработать →</a>
    </div>
    <div class="stat">
        <span class="stat__label">Заказов сегодня</span>
        <strong class="stat__value"><?= (int) $stats['today'] ?></strong>
        <span class="stat__hint">всего <?= (int) $stats['total'] ?></span>
    </div>
    <div class="stat stat--accent">
        <span class="stat__label">Выручка сегодня</span>
        <strong class="stat__value"><?= price($stats['today_revenue']) ?></strong>
        <span class="stat__hint">за месяц <?= price($stats['month_revenue']) ?></span>
    </div>
    <div class="stat">
        <span class="stat__label">Ждут оплаты</span>
        <strong class="stat__value"><?= (int) $stats['unpaid'] ?></strong>
        <a class="stat__link" href="<?= u('/admin/orders?payment_status=pending') ?>">Посмотреть →</a>
    </div>
    <div class="stat">
        <span class="stat__label">Средний чек</span>
        <strong class="stat__value"><?= price(round($stats['avg_check'])) ?></strong>
        <span class="stat__hint">по оплаченным</span>
    </div>
    <div class="stat">
        <span class="stat__label">Новых обращений</span>
        <strong class="stat__value"><?= (int) $newMessages ?></strong>
        <a class="stat__link" href="<?= u('/admin/messages') ?>">Ответить →</a>
    </div>
    <div class="stat">
        <span class="stat__label">Каталог</span>
        <strong class="stat__value"><?= (int) $productCount ?></strong>
        <span class="stat__hint"><?= (int) $categoryCount ?> <?= plural((int) $categoryCount, 'категория', 'категории', 'категорий') ?></span>
    </div>
</div>

<div class="admin-cols">
    <section class="panel">
        <h2 class="panel__title">Заказы и выручка за 14 дней</h2>
        <div class="chart" role="img" aria-label="График выручки за последние 14 дней">
            <?php foreach ($chart as $day): ?>
                <div class="chart__col" title="<?= e($day['label']) ?>: <?= price($day['revenue']) ?>, <?= (int) $day['orders'] ?> зак.">
                    <div class="chart__bar" style="height: <?= max(3, round($day['revenue'] / $maxRevenue * 100)) ?>%">
                        <span class="chart__value"><?= $day['revenue'] > 0 ? price($day['revenue'], false) : '' ?></span>
                    </div>
                    <span class="chart__label"><?= e($day['label']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">Топ товаров</h2>
        <?php if ($topProducts): ?>
            <ul class="rank">
                <?php foreach ($topProducts as $index => $item): ?>
                    <li>
                        <span class="rank__num"><?= $index + 1 ?></span>
                        <span class="rank__name"><?= e($item['name']) ?></span>
                        <span class="rank__value"><?= (int) $item['qty'] ?> шт · <?= price($item['revenue']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">Пока нет продаж.</p>
        <?php endif; ?>
    </section>
</div>

<div class="admin-cols">
    <section class="panel">
        <div class="panel__head">
            <h2 class="panel__title">Последние заказы</h2>
            <a class="panel__link" href="<?= u('/admin/orders') ?>">Все заказы →</a>
        </div>
        <?php if ($recentOrders): ?>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Номер</th><th>Клиент</th><th>Сумма</th><th>Статус</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr onclick="location.href='<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>'">
                            <td><a href="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>"><?= e($order['number']) ?></a><br><small class="muted"><?= date_ru($order['created_at']) ?></small></td>
                            <td><?= e($order['customer_name']) ?><br><small class="muted"><?= e($order['phone']) ?></small></td>
                            <td><strong><?= price($order['total']) ?></strong></td>
                            <td>
                                <span class="chip chip--<?= e($order['status']) ?>"><?= e(Order::STATUSES[$order['status']] ?? $order['status']) ?></span>
                                <span class="chip chip--pay-<?= e($order['payment_status']) ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']] ?? '') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="muted">Заказов пока нет.</p>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2 class="panel__title">Заканчивается на складе</h2>
            <a class="panel__link" href="<?= u('/admin/products') ?>">Все товары →</a>
        </div>
        <?php if ($lowStock): ?>
            <ul class="rank">
                <?php foreach ($lowStock as $product): ?>
                    <li>
                        <span class="rank__num rank__num--warn"><?= (int) $product['stock'] ?></span>
                        <span class="rank__name"><a href="<?= u('/') ?>admin/products/<?= (int) $product['id'] ?>/edit"><?= e($product['name']) ?></a></span>
                        <span class="rank__value"><?= price($product['price']) ?> / <?= e($product['unit']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">Все позиции в достатке.</p>
        <?php endif; ?>
    </section>
</div>

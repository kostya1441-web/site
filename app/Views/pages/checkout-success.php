<?php
/** @var array $order */
/** @var array $items */
/** @var bool $paid */
use App\Models\Order;
?>
<div class="container">
    <div class="result <?= $paid ? 'result--ok' : 'result--wait' ?>">
        <span class="result__icon" aria-hidden="true"><?= $paid ? '✅' : '🕒' ?></span>
        <h1 class="result__title">
            <?= $paid ? 'Заказ оплачен!' : 'Заказ принят!' ?>
        </h1>
        <p class="result__lead">
            Номер заказа — <strong><?= e($order['number']) ?></strong>.
            <?php if ($paid): ?>
                Оплата прошла успешно, мы уже начали собирать вашу корзину.
            <?php elseif ($order['payment_method'] === 'cash'): ?>
                Оплата при получении. Менеджер позвонит для подтверждения.
            <?php else: ?>
                Платёж пока не подтверждён<?= !empty($statusText) ? ': ' . e($statusText) : '' ?>.
            <?php endif; ?>
        </p>

        <?php if (!$paid && $order['payment_method'] === 'sber' && $order['payment_status'] !== 'refunded'): ?>
            <a class="btn btn--primary btn--lg" href="<?= u('/') ?>checkout/retry?order=<?= urlencode($order['number']) ?>">Оплатить картой</a>
        <?php endif; ?>

        <div class="result__card">
            <h2>Детали заказа</h2>
            <ul class="result__items">
                <?php foreach ($items as $item): ?>
                    <li>
                        <span><?= e($item['name']) ?> <em>× <?= (int) $item['quantity'] ?> <?= e($item['unit']) ?></em></span>
                        <span><?= price($item['sum']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <dl class="summary">
                <div><dt>Товары</dt><dd><?= price($order['subtotal']) ?></dd></div>
                <div><dt>Доставка</dt><dd><?= (float) $order['delivery_price'] > 0 ? price($order['delivery_price']) : 'бесплатно' ?></dd></div>
                <div class="summary__total"><dt>Итого</dt><dd><?= price($order['total']) ?></dd></div>
            </dl>

            <ul class="result__meta">
                <li><span>Получение</span><strong><?= $order['delivery_type'] === 'pickup' ? 'Самовывоз, ' . e($settings['address'] ?? '') : e($order['address']) ?></strong></li>
                <li><span>Телефон</span><strong><?= e($order['phone']) ?></strong></li>
                <li><span>Статус</span><strong><?= e(Order::STATUSES[$order['status']] ?? $order['status']) ?></strong></li>
                <li><span>Оплата</span><strong><?= e(Order::PAYMENT_STATUSES[$order['payment_status']] ?? $order['payment_status']) ?></strong></li>
            </ul>
        </div>

        <div class="result__actions">
            <a class="btn btn--ghost" href="<?= u('/catalog') ?>">Продолжить покупки</a>
            <a class="btn btn--ghost" href="<?= e(phone_link($settings['phone'] ?? '')) ?>">Позвонить нам</a>
        </div>
    </div>
</div>

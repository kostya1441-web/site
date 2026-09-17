<?php
use App\Models\Order;
/** @var array $order */
/** @var array $items */
/** @var array $history */
?>
<div class="admin-head">
    <div>
        <a class="back-link" href="<?= u('/admin/orders') ?>">← К списку заказов</a>
        <h1>Заказ <?= e($order['number']) ?></h1>
        <p><?= date_ru($order['created_at']) ?> · <?= e(Order::PAYMENT_METHODS[$order['payment_method']] ?? '') ?></p>
    </div>
    <div class="admin-head__chips">
        <span class="chip chip--<?= e($order['status']) ?>"><?= e(Order::STATUSES[$order['status']] ?? $order['status']) ?></span>
        <span class="chip chip--pay-<?= e($order['payment_status']) ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']] ?? '') ?></span>
    </div>
</div>

<div class="admin-cols admin-cols--2-1">
    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Состав заказа</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['product_id']): ?>
                                    <a href="<?= u('/') ?>admin/products/<?= (int) $item['product_id'] ?>/edit"><?= e($item['name']) ?></a>
                                <?php else: ?>
                                    <?= e($item['name']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= price($item['price']) ?> / <?= e($item['unit']) ?></td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><strong><?= price($item['sum']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr><td colspan="3">Товары</td><td><?= price($order['subtotal']) ?></td></tr>
                    <tr><td colspan="3">Доставка</td><td><?= (float) $order['delivery_price'] > 0 ? price($order['delivery_price']) : 'бесплатно' ?></td></tr>
                    <tr class="total-row"><td colspan="3">Итого</td><td><?= price($order['total']) ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel__title">Клиент и доставка</h2>
            <dl class="deflist">
                <div><dt>Имя</dt><dd><?= e($order['customer_name']) ?></dd></div>
                <div><dt>Телефон</dt><dd><a href="<?= e(phone_link($order['phone'])) ?>"><?= e($order['phone']) ?></a></dd></div>
                <div><dt>E-mail</dt><dd><?= $order['email'] ? '<a href="mailto:' . e($order['email']) . '">' . e($order['email']) . '</a>' : '—' ?></dd></div>
                <div><dt>Получение</dt><dd><?= $order['delivery_type'] === 'pickup' ? 'Самовывоз' : 'Доставка' ?></dd></div>
                <div><dt>Адрес</dt><dd><?= e($order['address'] ?: '—') ?></dd></div>
                <div><dt>Комментарий</dt><dd><?= nl2br(e($order['comment'] ?: '—')) ?></dd></div>
                <div><dt>IP клиента</dt><dd class="muted"><?= e($order['ip']) ?></dd></div>
            </dl>
        </section>

        <section class="panel">
            <h2 class="panel__title">История</h2>
            <ul class="timeline">
                <?php foreach ($history as $event): ?>
                    <li>
                        <span class="timeline__dot"></span>
                        <div>
                            <strong><?= e(Order::STATUSES[$event['status']] ?? $event['status']) ?></strong>
                            <?php if ($event['comment']): ?><p><?= e($event['comment']) ?></p><?php endif; ?>
                            <small class="muted"><?= date_ru($event['created_at']) ?> · <?= e($event['author']) ?></small>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Статус заказа</h2>
            <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/status" class="stack">
                <?= csrf_field() ?>
                <label class="field">
                    <span>Новый статус</span>
                    <select name="status">
                        <?php foreach (Order::STATUSES as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Комментарий (виден только в админке)</span>
                    <input type="text" name="comment" maxlength="500" placeholder="Например: клиент попросил привезти к 18:00">
                </label>
                <button class="btn btn--primary btn--block" type="submit">Сохранить статус</button>
            </form>
        </section>

        <section class="panel">
            <h2 class="panel__title">Оплата</h2>
            <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/payment" class="stack">
                <?= csrf_field() ?>
                <label class="field">
                    <span>Статус оплаты</span>
                    <select name="payment_status">
                        <?php foreach (Order::PAYMENT_STATUSES as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $order['payment_status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="btn btn--ghost btn--block" type="submit">Обновить вручную</button>
            </form>

            <?php if ($order['payment_method'] === 'sber'): ?>
                <div class="divider"></div>
                <p class="muted small">
                    ID платежа: <?= e($order['sber_order_id'] ?: '—') ?>
                    <?php if ($order['paid_at']): ?><br>Оплачен: <?= date_ru($order['paid_at']) ?><?php endif; ?>
                </p>
                <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/check-payment">
                    <?= csrf_field() ?>
                    <button class="btn btn--ghost btn--block" type="submit">Проверить статус в Сбербанке</button>
                </form>
                <?php if ($order['payment_status'] === 'paid'): ?>
                    <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/refund" onsubmit="return confirm('Оформить полный возврат на сумму <?= price($order['total']) ?>?')">
                        <?= csrf_field() ?>
                        <button class="btn btn--danger btn--block" type="submit">Вернуть деньги</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="panel">
            <h2 class="panel__title">Заметка менеджера</h2>
            <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/note" class="stack">
                <?= csrf_field() ?>
                <textarea name="admin_note" rows="4" placeholder="Внутренние пометки по заказу"><?= e($order['admin_note']) ?></textarea>
                <button class="btn btn--ghost btn--block" type="submit">Сохранить заметку</button>
            </form>
        </section>

        <section class="panel panel--danger">
            <h2 class="panel__title">Удаление</h2>
            <p class="muted small">Заказ будет удалён вместе с историей и позициями. Действие необратимо.</p>
            <form method="post" action="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>/delete" onsubmit="return confirm('Удалить заказ <?= e($order['number']) ?>?')">
                <?= csrf_field() ?>
                <button class="btn btn--danger btn--block" type="submit">Удалить заказ</button>
            </form>
        </section>
    </div>
</div>

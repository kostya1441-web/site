<?php
use App\Models\Order;
/** @var array $orders */
/** @var array $filters */
$queryString = http_build_query(array_filter($filters));
?>
<div class="admin-head">
    <div>
        <h1>Заказы</h1>
        <p>Всего <?= (int) $pagination['total'] ?> <?= plural((int) $pagination['total'], 'заказ', 'заказа', 'заказов') ?></p>
    </div>
    <a class="btn btn--ghost" href="<?= u('/') ?>admin/orders/export<?= $queryString ? '?' . e($queryString) : '' ?>">Выгрузить CSV</a>
</div>

<form class="filters" method="get" action="<?= u('/admin/orders') ?>">
    <input type="search" name="q" value="<?= e($filters['search']) ?>" placeholder="Номер, имя или телефон">
    <select name="status">
        <option value="">Все статусы</option>
        <?php foreach (Order::STATUSES as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="payment_status">
        <option value="">Любая оплата</option>
        <?php foreach (Order::PAYMENT_STATUSES as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $filters['payment_status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date_from" value="<?= e($filters['date_from']) ?>" aria-label="Дата с">
    <input type="date" name="date_to" value="<?= e($filters['date_to']) ?>" aria-label="Дата по">
    <button class="btn btn--primary" type="submit">Фильтр</button>
    <a class="btn btn--ghost" href="<?= u('/admin/orders') ?>">Сброс</a>
</form>

<?php if ($orders): ?>
    <div class="table-wrap panel">
        <table class="table table--orders">
            <thead>
            <tr>
                <th>Заказ</th>
                <th>Клиент</th>
                <th>Получение</th>
                <th>Сумма</th>
                <th>Оплата</th>
                <th>Статус</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr class="row-link" data-href="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>">
                    <td>
                        <a class="strong" href="<?= u('/') ?>admin/orders/<?= (int) $order['id'] ?>"><?= e($order['number']) ?></a>
                        <small class="muted"><?= date_ru($order['created_at']) ?></small>
                        <small class="muted"><?= (int) $order['items_count'] ?> <?= plural((int) $order['items_count'], 'позиция', 'позиции', 'позиций') ?></small>
                    </td>
                    <td>
                        <?= e($order['customer_name']) ?>
                        <small class="muted"><a href="<?= e(phone_link($order['phone'])) ?>"><?= e($order['phone']) ?></a></small>
                    </td>
                    <td>
                        <?= $order['delivery_type'] === 'pickup' ? 'Самовывоз' : 'Доставка' ?>
                        <?php if ($order['address']): ?><small class="muted"><?= e(excerpt($order['address'], 40)) ?></small><?php endif; ?>
                    </td>
                    <td><strong><?= price($order['total']) ?></strong></td>
                    <td>
                        <span class="chip chip--pay-<?= e($order['payment_status']) ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']] ?? '') ?></span>
                        <small class="muted"><?= e(Order::PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method']) ?></small>
                    </td>
                    <td><span class="chip chip--<?= e($order['status']) ?>"><?= e(Order::STATUSES[$order['status']] ?? $order['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
<?php else: ?>
    <div class="panel empty-box">
        <p>Заказов по заданным условиям нет.</p>
        <a class="btn btn--ghost" href="<?= u('/admin/orders') ?>">Сбросить фильтры</a>
    </div>
<?php endif; ?>

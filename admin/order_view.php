<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    $pageTitle = 'Заказ не найден';
    $activeNav = 'orders';
    require __DIR__ . '/includes/admin_header.php';
    echo '<div class="admin-card">Заказ не найден.</div>';
    require __DIR__ . '/includes/admin_footer.php';
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfCheck($_POST['csrf_token'] ?? null)) {
    $newStatus = $_POST['status'] ?? $order['status'];
    $newPaymentStatus = $_POST['payment_status'] ?? $order['payment_status'];

    $validStatuses = ['new', 'processing', 'ready', 'shipped', 'completed', 'cancelled'];
    $validPaymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

    if (in_array($newStatus, $validStatuses, true) && in_array($newPaymentStatus, $validPaymentStatuses, true)) {
        db()->prepare('UPDATE orders SET status = :status, payment_status = :payment_status WHERE id = :id')
            ->execute(['status' => $newStatus, 'payment_status' => $newPaymentStatus, 'id' => $id]);
        $order['status'] = $newStatus;
        $order['payment_status'] = $newPaymentStatus;
        $message = 'Статус заказа обновлён.';
    }
}

$itemsStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :id');
$itemsStmt->execute(['id' => $id]);
$orderItems = $itemsStmt->fetchAll();

$pageTitle = 'Заказ №' . $order['order_number'];
$activeNav = 'orders';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
    <h3 style="margin-top:0;">Информация о заказе</h3>
    <div class="form-row">
        <div><strong>Клиент:</strong> <?= e($order['customer_name']) ?></div>
        <div><strong>Телефон:</strong> <?= e($order['phone']) ?></div>
        <div><strong>Email:</strong> <?= e($order['email'] ?: '—') ?></div>
    </div>
    <div class="form-row">
        <div><strong>Получение:</strong> <?= $order['delivery_type'] === 'delivery' ? 'Доставка' : 'Самовывоз' ?></div>
        <div><strong>Адрес:</strong> <?= e($order['address'] ?: '—') ?></div>
        <div><strong>Оплата:</strong> <?= $order['payment_method'] === 'sberbank' ? 'Онлайн (Сбербанк)' : 'При получении' ?></div>
    </div>
    <?php if ($order['comment']): ?><p><strong>Комментарий:</strong> <?= nl2br(e($order['comment'])) ?></p><?php endif; ?>
    <p><strong>Дата:</strong> <?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></p>
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Состав заказа</h3>
    <table class="admin-table">
        <thead><tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr></thead>
        <tbody>
        <?php foreach ($orderItems as $item): ?>
            <tr>
                <td><?= e($item['product_name']) ?></td>
                <td><?= formatPrice($item['price']) ?> / <?= e($item['unit']) ?></td>
                <td><?= e((string)(float)$item['quantity']) ?></td>
                <td><?= formatPrice($item['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div style="margin-top:14px; text-align:right;">
        <div>Товары: <?= formatPrice($order['items_amount']) ?></div>
        <div>Доставка: <?= formatPrice($order['delivery_cost']) ?></div>
        <div style="font-weight:800; font-size:18px;">Итого: <?= formatPrice($order['total_amount']) ?></div>
    </div>
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Изменить статус</h3>
    <form method="post" action="/admin/order_view.php?id=<?= (int)$order['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Статус заказа</label>
                <select name="status" class="form-control">
                    <?php foreach (['new' => 'Новый', 'processing' => 'В обработке', 'ready' => 'Готов к выдаче', 'shipped' => 'Передан в доставку', 'completed' => 'Выполнен', 'cancelled' => 'Отменён'] as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Статус оплаты</label>
                <select name="payment_status" class="form-control">
                    <?php foreach (['pending' => 'Ожидает оплаты', 'paid' => 'Оплачен', 'failed' => 'Не оплачен', 'refunded' => 'Возврат'] as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $order['payment_status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn">Сохранить</button>
        <a href="/admin/orders.php" class="btn btn-outline">Назад к списку</a>
    </form>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

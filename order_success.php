<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/payment/SberbankAcquiring.php';

$orderNumber = trim($_GET['order'] ?? '');
$pageTitle = 'Заказ оформлен';

$stmt = db()->prepare('SELECT * FROM orders WHERE order_number = :n LIMIT 1');
$stmt->execute(['n' => $orderNumber]);
$order = $stmt->fetch();

if ($order && $order['payment_method'] === 'sberbank' && $order['payment_status'] === 'pending' && $order['sber_order_id']) {
    $sber = new SberbankAcquiring();
    $status = $sber->getOrderStatus($order['sber_order_id']);
    if ($status['success'] && $status['isPaid']) {
        db()->prepare('UPDATE orders SET payment_status = "paid", status = IF(status = "new", "processing", status) WHERE id = :id')
            ->execute(['id' => $order['id']]);
        $order['payment_status'] = 'paid';
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <?php if (!$order): ?>
            <div class="empty-state">Заказ не найден.</div>
        <?php elseif ($order['payment_status'] === 'paid'): ?>
            <div class="alert alert-success" style="max-width:600px;margin:0 auto;text-align:center;padding:30px;">
                <h1>Спасибо за заказ!</h1>
                <p>Заказ №<?= e($order['order_number']) ?> оплачен и принят в обработку.</p>
                <p>Мы свяжемся с вами по телефону <?= e($order['phone']) ?> для подтверждения деталей.</p>
                <a class="btn" href="/catalog.php">Вернуться в каталог</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="max-width:600px;margin:0 auto;text-align:center;padding:30px;">
                <h1>Заказ №<?= e($order['order_number']) ?> принят</h1>
                <p>Ожидаем подтверждения оплаты. Если оплата не прошла, вы можете связаться с нами по телефону <?= e(setting('phone')) ?>.</p>
                <a class="btn" href="/catalog.php">Вернуться в каталог</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

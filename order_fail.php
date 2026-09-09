<?php
require_once __DIR__ . '/includes/functions.php';

$orderNumber = trim($_GET['order'] ?? '');
$pageTitle = 'Ошибка оплаты';

if ($orderNumber !== '') {
    db()->prepare('UPDATE orders SET payment_status = "failed" WHERE order_number = :n AND payment_status = "pending"')
        ->execute(['n' => $orderNumber]);
}

require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <div class="alert alert-error" style="max-width:600px;margin:0 auto;text-align:center;padding:30px;">
            <h1>Оплата не завершена</h1>
            <p>
                <?php if ($orderNumber !== ''): ?>
                    Заказ №<?= e($orderNumber) ?> сохранён, но оплата не прошла.
                <?php else: ?>
                    Оплата не прошла.
                <?php endif; ?>
                Вы можете попробовать оплатить ещё раз или связаться с нами по телефону <?= e(setting('phone')) ?>.
            </p>
            <a class="btn" href="/cart.php">Вернуться в корзину</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Callback (server-to-server) уведомление от Сбербанка об изменении статуса платежа.
 * URL этого файла указывается в личном кабинете эквайринга как "URL уведомлений" (notificationUrl).
 *
 * Мы не доверяем параметрам запроса напрямую — по mdOrder/orderNumber всегда
 * перепроверяем статус через getOrderStatusExtended, чтобы избежать подделки уведомления.
 */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/SberbankAcquiring.php';

$mdOrder = $_REQUEST['mdOrder'] ?? $_REQUEST['orderId'] ?? '';
$orderNumber = $_REQUEST['orderNumber'] ?? '';

if ($mdOrder === '' && $orderNumber === '') {
    http_response_code(400);
    echo 'MISSING_PARAMS';
    exit;
}

$stmt = $mdOrder !== ''
    ? db()->prepare('SELECT * FROM orders WHERE sber_order_id = :v LIMIT 1')
    : db()->prepare('SELECT * FROM orders WHERE order_number = :v LIMIT 1');
$stmt->execute(['v' => $mdOrder !== '' ? $mdOrder : $orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo 'ORDER_NOT_FOUND';
    exit;
}

if ($order['payment_status'] === 'pending' && $order['sber_order_id']) {
    $sber = new SberbankAcquiring();
    $status = $sber->getOrderStatus($order['sber_order_id']);
    if ($status['success']) {
        if ($status['isPaid']) {
            db()->prepare('UPDATE orders SET payment_status = "paid", status = IF(status = "new", "processing", status) WHERE id = :id')
                ->execute(['id' => $order['id']]);
        } elseif (in_array($status['orderStatus'], [3, 6], true)) {
            db()->prepare('UPDATE orders SET payment_status = "failed" WHERE id = :id')
                ->execute(['id' => $order['id']]);
        }
    }
}

http_response_code(200);
echo 'OK';

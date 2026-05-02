<?php
// Альфа-Банк callback (уведомление об оплате)
// Банк шлёт GET-запрос: ?mdOrder=ALFA_ORDER_ID&orderNumber=order-ID-TIMESTAMP&operation=deposited&status=1
require_once __DIR__ . '/../includes/functions.php';

$mdOrder     = $_GET['mdOrder'] ?? '';
$orderNumber = $_GET['orderNumber'] ?? '';

if (!$mdOrder && !$orderNumber) {
    http_response_code(400);
    exit('Bad request');
}

// Верифицируем статус через API банка (не доверяем параметрам из запроса)
$paymentStatus = getAlfabankOrderStatus($mdOrder);

if ($paymentStatus === null) {
    http_response_code(500);
    exit('Status check failed');
}

// Извлекаем наш orderId из orderNumber (формат: order-ID-timestamp)
$ourOrderId = 0;
if (preg_match('/^order-(\d+)-/', $orderNumber, $m)) {
    $ourOrderId = (int)$m[1];
} elseif ($mdOrder) {
    $row = DB::fetch('SELECT id FROM orders WHERE payment_id=?', [$mdOrder]);
    $ourOrderId = (int)($row['id'] ?? 0);
}

if ($ourOrderId) {
    if ($paymentStatus === 2) { // оплачен
        DB::update('orders',
            ['payment_status' => 'paid', 'payment_id' => $mdOrder],
            'id=? AND payment_status=?',
            [$ourOrderId, 'pending']
        );
    } elseif (in_array($paymentStatus, [3, 6])) { // отменён
        DB::update('orders',
            ['payment_status' => 'failed'],
            'id=? AND payment_status=?',
            [$ourOrderId, 'pending']
        );
    }
}

http_response_code(200);
echo 'OK';

function getAlfabankOrderStatus(string $alfaOrderId): ?int
{
    if (!$alfaOrderId) return null;

    $baseUrl = ALFABANK_TEST_MODE
        ? 'https://alfa.rbsuat.com/payment/rest/'
        : 'https://pay.alfabank.ru/payment/rest/';

    $url = $baseUrl . 'getOrderStatusExtended.do?' . http_build_query([
        'userName' => ALFABANK_USERNAME,
        'password' => ALFABANK_PASSWORD,
        'orderId'  => $alfaOrderId,
        'language' => 'ru',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => !ALFABANK_TEST_MODE,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return null;
    $data = json_decode($response, true);
    return isset($data['orderStatus']) ? (int)$data['orderStatus'] : null;
}

<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    json_response(['success' => false, 'message' => 'Invalid JSON']);
}

$name    = trim($body['name'] ?? '');
$phone   = trim($body['phone'] ?? '');
$type    = $body['type'] === 'delivery' ? 'delivery' : 'pickup';
$address = trim($body['address'] ?? '');
$comment = trim($body['comment'] ?? '');
$items   = $body['items'] ?? [];

if (!$name || mb_strlen($name) < 2) {
    json_response(['success' => false, 'message' => 'Введите имя']);
}
if (!preg_match('/^\+?[0-9\s\(\)\-]{7,20}$/', $phone)) {
    json_response(['success' => false, 'message' => 'Введите корректный телефон']);
}
if ($type === 'delivery' && mb_strlen($address) < 5) {
    json_response(['success' => false, 'message' => 'Введите адрес доставки']);
}
if (empty($items)) {
    json_response(['success' => false, 'message' => 'Корзина пуста']);
}

// Validate items and recalculate total server-side
$serverTotal = 0;
$validatedItems = [];
foreach ($items as $item) {
    $id  = (int)($item['id'] ?? 0);
    $qty = max(1, (int)($item['qty'] ?? 1));
    $dbItem = DB::fetch('SELECT * FROM menu_items WHERE id=? AND active=1', [$id]);
    if (!$dbItem) continue;
    $serverTotal += $dbItem['price'] * $qty;
    $validatedItems[] = [
        'menu_item_id' => $id,
        'name'         => $dbItem['name'],
        'price'        => $dbItem['price'],
        'qty'          => $qty,
    ];
}
if (empty($validatedItems)) {
    json_response(['success' => false, 'message' => 'Позиции из меню не найдены']);
}

// Create order
$orderId = DB::insert('orders', [
    'name'    => $name,
    'phone'   => $phone,
    'type'    => $type,
    'address' => $address,
    'total'   => $serverTotal,
    'comment' => $comment,
]);

foreach ($validatedItems as $vi) {
    DB::insert('order_items', array_merge(['order_id' => $orderId], $vi));
}

// ── Альфа-Банк payment ───────────────────────────────────
$alfabankEnabled = ALFABANK_USERNAME !== 'YOUR_USERNAME';

if ($alfabankEnabled) {
    $result = createAlfabankPayment($orderId, $serverTotal);
    if ($result) {
        DB::update('orders', ['payment_id' => $result['orderId']], 'id=?', [$orderId]);
        json_response(['success' => true, 'order_id' => $orderId, 'payment_url' => $result['formUrl']]);
    }
}

// Fallback: no payment configured
json_response(['success' => true, 'order_id' => $orderId]);

// ── Альфа-Банк helper ────────────────────────────────────
function createAlfabankPayment(int $orderId, float $amount): ?array
{
    $baseUrl = ALFABANK_TEST_MODE
        ? 'https://alfa.rbsuat.com/payment/rest/'
        : 'https://pay.alfabank.ru/payment/rest/';

    $params = [
        'userName'    => ALFABANK_USERNAME,
        'password'    => ALFABANK_PASSWORD,
        'orderNumber' => 'order-' . $orderId . '-' . time(),
        'amount'      => (int)round($amount * 100), // в копейках
        'returnUrl'   => ALFABANK_RETURN_URL . '?id=' . $orderId,
        'failUrl'     => ALFABANK_RETURN_URL . '?id=' . $orderId . '&fail=1',
        'description' => 'Заказ #' . str_pad($orderId, 5, '0', STR_PAD_LEFT) . ' — Кафе-бар «Квартирник»',
        'language'    => 'ru',
    ];

    $ch = curl_init($baseUrl . 'register.do');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($params),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => !ALFABANK_TEST_MODE,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) return null;
    $data = json_decode($response, true);
    if (!empty($data['errorCode']) || empty($data['formUrl'])) return null;

    return $data; // ['orderId' => '...', 'formUrl' => '...']
}

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
$total   = (float)($body['total'] ?? 0);

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

// ── ЮKassa payment ──────────────────────────────────────
$yukassaEnabled = YUKASSA_SHOP_ID !== 'YOUR_SHOP_ID';

if ($yukassaEnabled) {
    $paymentUrl = createYukassaPayment($orderId, $serverTotal, $name);
    if ($paymentUrl) {
        json_response(['success' => true, 'order_id' => $orderId, 'payment_url' => $paymentUrl]);
    }
}

// Fallback: redirect to confirm without payment
json_response(['success' => true, 'order_id' => $orderId]);

// ── ЮKassa helper ────────────────────────────────────────
function createYukassaPayment(int $orderId, float $amount, string $customerName): ?string {
    $idempotenceKey = 'order-' . $orderId . '-' . time();
    $payload = [
        'amount'       => ['value' => number_format($amount, 2, '.', ''), 'currency' => 'RUB'],
        'confirmation' => ['type' => 'redirect', 'return_url' => YUKASSA_RETURN_URL . '?id=' . $orderId],
        'capture'      => true,
        'description'  => 'Заказ #' . str_pad($orderId, 5, '0', STR_PAD_LEFT) . ' — Кафе-бар «Квартирник»',
        'metadata'     => ['order_id' => $orderId],
    ];

    $ch = curl_init('https://api.yookassa.ru/v3/payments');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_USERPWD        => YUKASSA_SHOP_ID . ':' . YUKASSA_SECRET_KEY,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Idempotence-Key: ' . $idempotenceKey,
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;
    $data = json_decode($response, true);
    return $data['confirmation']['confirmation_url'] ?? null;
}

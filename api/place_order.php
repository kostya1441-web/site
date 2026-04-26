<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();

$allItems = all_menu_items($menu);
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header('Location: /cart.php');
    exit;
}

$total = cart_total($cart, $allItems);
$type = $_POST['delivery_type'] ?? 'pickup';
$status = $type === 'pickup' ? 'Готовится' : 'Готовится';

$order = [
    'id' => uniqid('order_', true),
    'delivery_type' => $type,
    'name' => trim($_POST['name'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'items' => $cart,
    'total' => $total,
    'status' => $status,
    'created_at' => date('c'),
];

$file = __DIR__ . '/../storage_orders.json';
$orders = file_exists($file) ? json_decode((string)file_get_contents($file), true) : [];
$orders[] = $order;
file_put_contents($file, json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// Реальная интеграция: отправка webhook в Telegram-бота о новом заказе.
// Реальная интеграция: создание платежа через API ЮKassa.

$_SESSION['cart'] = [];
header('Location: /checkout.php?ok=1');
exit;

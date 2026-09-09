<?php
/**
 * AJAX-эндпоинт для операций с корзиной: add / update / remove.
 */
require_once __DIR__ . '/includes/cart.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$qty = (float)($_POST['qty'] ?? 1);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Некорректный товар']);
    exit;
}

$product = getProductById($productId);
if (!$product || !$product['is_active']) {
    echo json_encode(['success' => false, 'message' => 'Товар не найден']);
    exit;
}

switch ($action) {
    case 'add':
        if (!$product['in_stock']) {
            echo json_encode(['success' => false, 'message' => 'Товар нет в наличии']);
            exit;
        }
        cartAdd($productId, $qty > 0 ? $qty : 1);
        break;
    case 'update':
        cartSetQty($productId, $qty);
        break;
    case 'remove':
        cartRemove($productId);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
        exit;
}

echo json_encode([
    'success'    => true,
    'cart_count' => cartCount(),
    'cart_total' => cartTotal(),
]);

<?php
/**
 * Работа с корзиной покупателя (хранится в сессии).
 * Формат: $_SESSION['cart'] = [product_id => qty, ...]
 */
require_once __DIR__ . '/functions.php';

function cartInit(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cartAdd(int $productId, float $qty = 1): void
{
    cartInit();
    $qty = max(0.001, $qty);
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cartSetQty(int $productId, float $qty): void
{
    cartInit();
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cartRemove(int $productId): void
{
    cartInit();
    unset($_SESSION['cart'][$productId]);
}

function cartClear(): void
{
    $_SESSION['cart'] = [];
}

/** Полное содержимое корзины с данными товаров из БД (пересчёт по актуальным ценам) */
function cartItems(): array
{
    cartInit();
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    $validIds = [];
    foreach ($products as $product) {
        $qty = (float)$_SESSION['cart'][$product['id']];
        if ($qty <= 0) {
            continue;
        }
        $validIds[] = $product['id'];
        $items[] = [
            'product_id' => (int)$product['id'],
            'name'       => $product['name'],
            'slug'       => $product['slug'],
            'price'      => (float)$product['price'],
            'unit'       => $product['unit'],
            'image'      => $product['image'],
            'in_stock'   => (bool)$product['in_stock'],
            'qty'        => $qty,
            'subtotal'   => round((float)$product['price'] * $qty, 2),
        ];
    }

    // Убираем из сессии товары, которых больше не существует/не активны
    foreach (array_keys($_SESSION['cart']) as $pid) {
        if (!in_array((int)$pid, $validIds, true)) {
            unset($_SESSION['cart'][$pid]);
        }
    }

    return $items;
}

function cartTotal(): float
{
    $total = 0.0;
    foreach (cartItems() as $item) {
        $total += $item['subtotal'];
    }
    return round($total, 2);
}

function cartCount(): int
{
    cartInit();
    return count($_SESSION['cart']);
}

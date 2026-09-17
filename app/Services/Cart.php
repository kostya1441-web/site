<?php

namespace App\Services;

use App\Core\Config;
use App\Core\Session;
use App\Models\Product;

/**
 * Корзина в сессии. Хранит только id и количество — цены и наличие
 * всегда подтягиваются из БД, чтобы их нельзя было подменить на клиенте.
 */
class Cart
{
    private const KEY = '_cart';
    private const MAX_QTY = 99;

    public static function items(): array
    {
        $raw = Session::get(self::KEY, []);
        return is_array($raw) ? $raw : [];
    }

    public static function add(int $productId, int $quantity = 1): void
    {
        $items = self::items();
        $items[$productId] = min(self::MAX_QTY, max(1, ($items[$productId] ?? 0) + $quantity));
        Session::set(self::KEY, $items);
    }

    public static function setQuantity(int $productId, int $quantity): void
    {
        $items = self::items();
        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min(self::MAX_QTY, $quantity);
        }
        Session::set(self::KEY, $items);
    }

    public static function remove(int $productId): void
    {
        $items = self::items();
        unset($items[$productId]);
        Session::set(self::KEY, $items);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    public static function count(): int
    {
        return array_sum(array_map('intval', self::items()));
    }

    public static function isEmpty(): bool
    {
        return self::items() === [];
    }

    /**
     * Полная раскладка корзины: позиции, сумма, доставка, итог.
     * Товары, ставшие неактивными, молча выпадают из корзины.
     */
    public static function detailed(string $deliveryType = 'delivery'): array
    {
        $items    = self::items();
        $products = Product::findMany(array_keys($items));

        $lines    = [];
        $subtotal = 0.0;
        $changed  = false;

        foreach ($items as $productId => $quantity) {
            $productId = (int) $productId;
            if (!isset($products[$productId])) {
                $changed = true;
                continue;
            }
            $product  = $products[$productId];
            $quantity = (int) $quantity;
            $sum      = round((float) $product['price'] * $quantity, 2);
            $subtotal += $sum;

            $lines[] = [
                'product_id' => $productId,
                'name'       => $product['name'],
                'slug'       => $product['slug'],
                'image'      => $product['image'],
                'unit'       => $product['unit'],
                'price'      => (float) $product['price'],
                'old_price'  => $product['old_price'] !== null ? (float) $product['old_price'] : null,
                'quantity'   => $quantity,
                'sum'        => $sum,
                'stock'      => (int) $product['stock'],
            ];
        }

        if ($changed) {
            $clean = [];
            foreach ($lines as $line) {
                $clean[$line['product_id']] = $line['quantity'];
            }
            Session::set(self::KEY, $clean);
        }

        $freeFrom = (float) Config::get('shop.free_delivery_from', 3000);
        $delivery = 0.0;
        if ($deliveryType === 'delivery' && $lines !== [] && $subtotal < $freeFrom) {
            $delivery = (float) Config::get('shop.delivery_price', 300);
        }

        return [
            'lines'           => $lines,
            'count'           => array_sum(array_column($lines, 'quantity')),
            'subtotal'        => round($subtotal, 2),
            'delivery'        => $delivery,
            'total'           => round($subtotal + $delivery, 2),
            'free_from'       => $freeFrom,
            'left_to_free'    => max(0, round($freeFrom - $subtotal, 2)),
            'min_order'       => (float) Config::get('shop.min_order', 0),
            'below_min_order' => $subtotal > 0 && $subtotal < (float) Config::get('shop.min_order', 0),
        ];
    }
}

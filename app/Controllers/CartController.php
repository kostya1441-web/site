<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Category;
use App\Models\Product;
use App\Services\Cart;

class CartController extends Controller
{
    public function index(): void
    {
        $this->view('pages/cart', [
            'title'       => 'Корзина — Ваш фермер',
            'description' => 'Ваш заказ фермерских продуктов с доставкой по Новокузнецку.',
            'cart'        => Cart::detailed(),
            'categories'  => Category::active(),
        ]);
    }

    public function add(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $productId = $this->request->int('product_id');
        $quantity  = max(1, $this->request->int('quantity', 1));
        $product   = Product::find($productId);

        if (!$product || !$product['is_active']) {
            $this->respond(false, 'Товар недоступен');
            return;
        }

        Cart::add($productId, $quantity);
        $this->respond(true, 'Товар «' . $product['name'] . '» в корзине');
    }

    public function update(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Cart::setQuantity($this->request->int('product_id'), $this->request->int('quantity'));
        $this->respond(true, 'Корзина обновлена');
    }

    public function remove(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Cart::remove($this->request->int('product_id'));
        $this->respond(true, 'Товар удалён из корзины');
    }

    public function clear(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Cart::clear();
        $this->respond(true, 'Корзина очищена');
    }

    /** Состояние корзины для шапки — используется JS после AJAX-действий. */
    public function state(): void
    {
        $cart = Cart::detailed();
        $this->json([
            'ok'       => true,
            'count'    => $cart['count'],
            'subtotal' => $cart['subtotal'],
            'total'    => $cart['total'],
            'lines'    => $cart['lines'],
        ]);
    }

    private function respond(bool $ok, string $message): void
    {
        if ($this->request->isAjax()) {
            $cart = Cart::detailed();
            $this->json([
                'ok'            => $ok,
                'message'       => $message,
                'count'         => $cart['count'],
                'subtotal'      => $cart['subtotal'],
                'subtotal_text' => price($cart['subtotal']),
                'total'         => $cart['total'],
                'total_text'    => price($cart['total']),
                'delivery_text' => $cart['delivery'] > 0 ? price($cart['delivery']) : 'бесплатно',
                'left_to_free'  => $cart['left_to_free'],
                'lines'         => $cart['lines'],
            ], $ok ? 200 : 422);
            return;
        }

        Session::flash($ok ? 'success' : 'error', $message);
        $this->back('/cart');
    }
}

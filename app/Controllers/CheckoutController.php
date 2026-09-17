<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Services\Notifier;
use App\Services\SberPayment;

class CheckoutController extends Controller
{
    public function index(): void
    {
        if (Cart::isEmpty()) {
            Session::flash('error', 'Корзина пуста — добавьте товары.');
            $this->redirect('/catalog');
            return;
        }

        $this->view('pages/checkout', [
            'title'       => 'Оформление заказа — Ваш фермер',
            'description' => 'Оформите доставку фермерских продуктов по Новокузнецку.',
            'cart'        => Cart::detailed($this->request->string('delivery_type', 'delivery')),
            'categories'  => Category::active(),
            'sberReady'   => SberPayment::isConfigured(),
        ]);
    }

    public function store(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        if (Cart::isEmpty()) {
            Session::flash('error', 'Корзина пуста.');
            $this->redirect('/catalog');
            return;
        }

        $data = [
            'customer_name'  => $this->request->string('customer_name'),
            'phone'          => $this->request->string('phone'),
            'email'          => $this->request->string('email'),
            'delivery_type'  => $this->request->string('delivery_type', 'delivery'),
            'address'        => $this->request->string('address'),
            'comment'        => $this->request->string('comment'),
            'payment_method' => $this->request->string('payment_method', 'sber'),
            'agree'          => $this->request->bool('agree') ? '1' : '',
        ];

        $validator = (new Validator($data))
            ->required('customer_name', 'Укажите имя')
            ->maxLength('customer_name', 120, 'Слишком длинное имя')
            ->required('phone', 'Укажите телефон')
            ->phone('phone', 'Проверьте номер телефона')
            ->email('email', 'Проверьте адрес электронной почты')
            ->in('delivery_type', ['delivery', 'pickup'], 'Выберите способ получения')
            ->in('payment_method', array_keys(Order::PAYMENT_METHODS), 'Выберите способ оплаты')
            ->required('agree', 'Нужно согласие на обработку персональных данных')
            ->maxLength('comment', 1000, 'Комментарий слишком длинный');

        if ($data['delivery_type'] === 'delivery') {
            $validator->required('address', 'Укажите адрес доставки');
        }
        if ($data['payment_method'] === 'sber' && !SberPayment::isConfigured()) {
            $validator->addError('payment_method', 'Онлайн-оплата временно недоступна, выберите оплату при получении');
        }

        $cart = Cart::detailed($data['delivery_type']);

        if ($cart['lines'] === []) {
            Session::flash('error', 'Товары в корзине закончились.');
            $this->redirect('/cart');
            return;
        }
        if ($cart['below_min_order']) {
            $validator->addError('total', 'Минимальная сумма заказа — ' . price($cart['min_order']));
        }

        if ($validator->fails()) {
            Session::flashInput($data);
            Session::flash('error', (string) $validator->firstError());
            $this->redirect('/checkout');
            return;
        }

        $items = array_map(static fn (array $line) => [
            'product_id' => $line['product_id'],
            'name'       => $line['name'],
            'unit'       => $line['unit'],
            'price'      => $line['price'],
            'quantity'   => $line['quantity'],
            'sum'        => $line['sum'],
        ], $cart['lines']);

        $created = Order::create([
            'status'         => 'new',
            'payment_status' => 'pending',
            'payment_method' => $data['payment_method'],
            'customer_name'  => $data['customer_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'delivery_type'  => $data['delivery_type'],
            'address'        => $data['delivery_type'] === 'pickup' ? '' : $data['address'],
            'comment'        => $data['comment'],
            'subtotal'       => $cart['subtotal'],
            'delivery_price' => $cart['delivery'],
            'total'          => $cart['total'],
            'ip'             => $this->request->ip(),
        ], $items);

        $order = Order::find($created['id']);
        Notifier::newOrder($order, $items);
        Cart::clear();

        // Номер заказа храним в сессии, чтобы показать статус без авторизации
        $tracked   = Session::get('_my_orders', []);
        $tracked[] = $order['number'];
        Session::set('_my_orders', array_slice(array_unique($tracked), -20));

        if ($data['payment_method'] !== 'sber') {
            Session::flash('success', 'Заказ ' . $order['number'] . ' принят! Мы перезвоним для подтверждения.');
            $this->redirect('/checkout/success?order=' . urlencode($order['number']));
            return;
        }

        $payment = SberPayment::register($order, $items);
        if (!$payment['ok']) {
            Order::setPayment((int) $order['id'], 'failed');
            Session::flash('error', 'Заказ сохранён, но платёж не удалось начать: ' . $payment['error']);
            $this->redirect('/checkout/fail?order=' . urlencode($order['number']));
            return;
        }

        Order::setPayment((int) $order['id'], 'pending', [
            'sber_order_id' => $payment['orderId'],
            'sber_form_url' => $payment['formUrl'],
        ]);

        $this->redirect($payment['formUrl']);
    }

    /** Возврат с платёжной страницы: сверяем реальный статус в шлюзе. */
    public function success(): void
    {
        $order = $this->resolveOrder();
        if (!$order) {
            return;
        }

        $paid    = $order['payment_status'] === 'paid';
        $message = null;

        if (!$paid && $order['payment_method'] === 'sber' && SberPayment::isConfigured()) {
            $status = SberPayment::status($order);
            if ($status['ok'] && $status['paid']) {
                $this->markPaid($order);
                $order = Order::find((int) $order['id']);
                $paid  = true;
            } elseif ($status['ok']) {
                $message = $status['message'];
            } else {
                $message = $status['error'] ?? null;
            }
        }

        $this->view('pages/checkout-success', [
            'title'       => 'Заказ ' . $order['number'] . ' оформлен — Ваш фермер',
            'description' => 'Статус вашего заказа в магазине «Ваш фермер».',
            'order'       => $order,
            'items'       => Order::items((int) $order['id']),
            'paid'        => $paid,
            'statusText'  => $message,
            'categories'  => Category::active(),
        ]);
    }

    public function fail(): void
    {
        $order = $this->resolveOrder();
        if (!$order) {
            return;
        }

        if ($order['payment_status'] === 'pending') {
            Order::setPayment((int) $order['id'], 'failed');
            $order = Order::find((int) $order['id']);
        }

        $this->view('pages/checkout-fail', [
            'title'       => 'Оплата не прошла — Ваш фермер',
            'description' => 'Платёж не завершён. Заказ сохранён, попробуйте оплатить ещё раз.',
            'order'       => $order,
            'categories'  => Category::active(),
        ]);
    }

    /** Повторная попытка оплаты по сохранённому заказу. */
    public function retry(): void
    {
        $order = $this->resolveOrder();
        if (!$order) {
            return;
        }

        if ($order['payment_status'] === 'paid') {
            $this->redirect('/checkout/success?order=' . urlencode($order['number']));
            return;
        }

        $items   = Order::items((int) $order['id']);
        $payment = SberPayment::register($order, array_map(static fn ($i) => [
            'product_id' => $i['product_id'],
            'name'       => $i['name'],
            'unit'       => $i['unit'],
            'price'      => $i['price'],
            'quantity'   => $i['quantity'],
            'sum'        => $i['sum'],
        ], $items));

        if (!$payment['ok']) {
            Session::flash('error', $payment['error']);
            $this->redirect('/checkout/fail?order=' . urlencode($order['number']));
            return;
        }

        Order::setPayment((int) $order['id'], 'pending', [
            'sber_order_id' => $payment['orderId'],
            'sber_form_url' => $payment['formUrl'],
        ]);
        $this->redirect($payment['formUrl']);
    }

    /**
     * Заказ доступен только тому, кто его оформил в этой сессии —
     * иначе номера заказов можно было бы перебирать.
     */
    private function resolveOrder(): ?array
    {
        $number = $this->request->string('order');
        $mine   = (array) Session::get('_my_orders', []);

        if ($number === '' || !in_array($number, $mine, true)) {
            $this->abort(404, 'Заказ не найден. Если вы только что оплатили — проверьте ссылку из письма или позвоните нам.');
            return null;
        }

        $order = Order::findByNumber($number);
        if (!$order) {
            $this->abort(404, 'Заказ не найден.');
            return null;
        }
        return $order;
    }

    private function markPaid(array $order): void
    {
        Order::setPayment((int) $order['id'], 'paid');
        if ($order['status'] === 'new') {
            Order::setStatus((int) $order['id'], 'confirmed', 'Оплата подтверждена Сбербанком', 'система');
        }
        foreach (Order::items((int) $order['id']) as $item) {
            if ($item['product_id']) {
                Product::decreaseStock((int) $item['product_id'], (int) $item['quantity']);
            }
        }
        Notifier::orderPaid(Order::find((int) $order['id']));
    }
}

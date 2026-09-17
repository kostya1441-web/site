<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Order;
use App\Models\Product;
use App\Services\Notifier;
use App\Services\SberPayment;

/**
 * Приём callback-уведомлений от эквайринга Сбербанка.
 * Адрес /payment/callback указывается в личном кабинете продавца.
 */
class PaymentController extends Controller
{
    public function callback(): void
    {
        $params = $this->request->all();
        SberPayment::log(null, 'callback_received', $params);

        if (!SberPayment::verifyCallback($params)) {
            SberPayment::log(null, 'callback_bad_signature', $params);
            Response::status(403);
            echo 'invalid checksum';
            return;
        }

        $order = Order::findByNumber((string) ($params['orderNumber'] ?? ''));
        if (!$order) {
            Response::status(404);
            echo 'order not found';
            return;
        }

        $operation = (string) ($params['operation'] ?? '');
        $success   = (string) ($params['status'] ?? '0') === '1';

        match (true) {
            $operation === 'deposited' && $success => $this->markPaid($order, $params),
            $operation === 'approved' && $success  => $this->markPaid($order, $params),
            in_array($operation, ['reversed', 'refunded'], true) => $this->markRefunded($order),
            default => $this->markFailed($order),
        };

        echo 'OK';
    }

    private function markPaid(array $order, array $params): void
    {
        if ($order['payment_status'] === 'paid') {
            return; // callback может прийти повторно
        }

        Order::setPayment((int) $order['id'], 'paid', [
            'sber_order_id' => (string) ($params['mdOrder'] ?? $order['sber_order_id']),
        ]);

        if ($order['status'] === 'new') {
            Order::setStatus((int) $order['id'], 'confirmed', 'Оплата подтверждена (callback Сбербанка)', 'система');
        }

        foreach (Order::items((int) $order['id']) as $item) {
            if ($item['product_id']) {
                Product::decreaseStock((int) $item['product_id'], (int) $item['quantity']);
            }
        }

        Notifier::orderPaid(Order::find((int) $order['id']));
    }

    private function markRefunded(array $order): void
    {
        Order::setPayment((int) $order['id'], 'refunded');
        Order::setStatus((int) $order['id'], 'canceled', 'Возврат средств по данным эквайринга', 'система');
    }

    private function markFailed(array $order): void
    {
        if ($order['payment_status'] !== 'paid') {
            Order::setPayment((int) $order['id'], 'failed');
        }
    }
}

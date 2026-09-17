<?php

namespace App\Services;

use App\Core\Config;
use App\Models\Setting;

/** Уведомления о заказах: письмо магазину и клиенту. */
class Notifier
{
    public static function newOrder(array $order, array $items): void
    {
        $to = Setting::get('notify_email');
        if ($to === '') {
            return;
        }

        $lines = [];
        foreach ($items as $item) {
            $lines[] = sprintf('— %s × %d %s = %s', $item['name'], $item['quantity'], $item['unit'], price($item['sum']));
        }

        $body = implode("\n", [
            'Новый заказ ' . $order['number'],
            '',
            'Покупатель: ' . $order['customer_name'],
            'Телефон: ' . $order['phone'],
            'E-mail: ' . ($order['email'] ?: '—'),
            'Доставка: ' . ($order['delivery_type'] === 'pickup' ? 'самовывоз' : $order['address']),
            'Оплата: ' . ($order['payment_method'] === 'sber' ? 'онлайн (Сбербанк)' : 'при получении'),
            'Комментарий: ' . ($order['comment'] ?: '—'),
            '',
            'Состав:',
            ...$lines,
            '',
            'Товары: ' . price($order['subtotal']),
            'Доставка: ' . price($order['delivery_price']),
            'Итого: ' . price($order['total']),
            '',
            'Заказ в админке: ' . rtrim((string) Config::get('app.url'), '/') . '/admin/orders/' . $order['id'],
        ]);

        self::send($to, 'Новый заказ ' . $order['number'] . ' — Ваш фермер', $body);
    }

    public static function orderPaid(array $order): void
    {
        $to = Setting::get('notify_email');
        if ($to !== '') {
            self::send($to, 'Оплачен заказ ' . $order['number'], 'Заказ ' . $order['number'] . ' оплачен на сумму ' . price($order['total']) . '.');
        }
        if (!empty($order['email'])) {
            self::send(
                $order['email'],
                'Заказ ' . $order['number'] . ' оплачен — Ваш фермер',
                "Спасибо за заказ!\n\nМы получили оплату на сумму " . price($order['total']) . " и уже собираем ваш заказ.\nМенеджер свяжется с вами по телефону " . $order['phone'] . ".\n\nС уважением, «Ваш фермер», Новокузнецк."
            );
        }
    }

    /** Вопрос с формы обратной связи. Возвращает, удалось ли отправить письмо. */
    public static function feedback(array $data): bool
    {
        $to = Setting::get('notify_email');
        if ($to === '') {
            return false;
        }

        $body = implode("\n", [
            'Вопрос с сайта «Ваш фермер»',
            '',
            'Имя: ' . $data['name'],
            'Телефон: ' . $data['phone'],
            'E-mail: ' . ($data['email'] ?: '—'),
            '',
            $data['message'],
        ]);

        return self::send($to, 'Вопрос с сайта «Ваш фермер»', $body);
    }

    private static function send(string $to, string $subject, string $body): bool
    {
        if (!function_exists('mail')) {
            return false;
        }
        $from    = Setting::get('email', 'noreply@localhost');
        $headers = [
            'From: =?UTF-8?B?' . base64_encode('Ваш фермер') . '?= <' . $from . '>',
            'Content-Type: text/plain; charset=UTF-8',
            'MIME-Version: 1.0',
        ];
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        try {
            return @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
        } catch (\Throwable $e) {
            error_log('mail: ' . $e->getMessage());
            return false;
        }
    }
}

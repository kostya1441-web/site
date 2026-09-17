<?php

namespace App\Services;

use App\Core\Config;
use App\Core\Database;

/**
 * Интеграция с интернет-эквайрингом Сбербанка (REST API).
 *
 * Схема работы:
 *   1) register.do — регистрируем заказ, получаем formUrl и уводим клиента на платёжную страницу;
 *   2) клиент возвращается на returnUrl → проверяем статус через getOrderStatusExtended.do;
 *   3) параллельно Сбер шлёт callback на /payment/callback — подпись проверяем HMAC-SHA256.
 *
 * Документация: https://securepayments.sberbank.ru/wiki/doku.php/integration:api:start
 */
class SberPayment
{
    private const TEST_URL = 'https://3dsec.sberbank.ru/payment/rest/';
    private const PROD_URL = 'https://securepayments.sberbank.ru/payment/rest/';

    /** Статусы заказа из getOrderStatusExtended.do */
    public const STATUS_REGISTERED   = 0;
    public const STATUS_HOLD         = 1;
    public const STATUS_PAID         = 2;
    public const STATUS_REVERSED     = 3;
    public const STATUS_REFUNDED     = 4;
    public const STATUS_ACS_INITIATED = 5;
    public const STATUS_DECLINED     = 6;

    public static function isConfigured(): bool
    {
        if (!Config::get('sber.enabled', true)) {
            return false;
        }
        return Config::get('sber.token') !== ''
            || (Config::get('sber.username') !== '' && Config::get('sber.password') !== '');
    }

    private static function endpoint(string $method): string
    {
        $base = Config::get('sber.test_mode', true) ? self::TEST_URL : self::PROD_URL;
        return $base . $method;
    }

    private static function credentials(): array
    {
        $token = (string) Config::get('sber.token', '');
        if ($token !== '') {
            return ['token' => $token];
        }
        return [
            'userName' => (string) Config::get('sber.username', ''),
            'password' => (string) Config::get('sber.password', ''),
        ];
    }

    /**
     * Регистрация заказа. Возвращает ['ok' => bool, 'formUrl' => ?string, 'orderId' => ?string, 'error' => ?string].
     */
    public static function register(array $order, array $items): array
    {
        if (!self::isConfigured()) {
            return ['ok' => false, 'error' => 'Онлайн-оплата не настроена. Укажите доступы к эквайрингу Сбербанка.'];
        }

        $appUrl = rtrim((string) Config::get('app.url'), '/');

        $params = array_merge(self::credentials(), [
            'orderNumber' => $order['number'],
            // сумма передаётся в копейках
            'amount'      => (int) round((float) $order['total'] * 100),
            'currency'    => (int) Config::get('sber.currency', 643),
            'returnUrl'   => $appUrl . Config::get('sber.return_url', '/checkout/success') . '?order=' . urlencode($order['number']),
            'failUrl'     => $appUrl . Config::get('sber.fail_url', '/checkout/fail') . '?order=' . urlencode($order['number']),
            'description' => 'Заказ ' . $order['number'] . ' — Ваш фермер',
            'language'    => 'ru',
            'sessionTimeoutSecs' => 1800,
            'jsonParams'  => json_encode([
                'phone' => $order['phone'],
                'email' => $order['email'],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        if ((string) $order['email'] !== '') {
            $params['email'] = $order['email'];
        }

        if (Config::get('sber.fiscalization', false)) {
            $params['orderBundle'] = json_encode(self::orderBundle($order, $items), JSON_UNESCAPED_UNICODE);
        }

        $response = self::request('register.do', $params);
        self::log((int) $order['id'], 'register', $response);

        if (!empty($response['errorCode']) && (string) $response['errorCode'] !== '0') {
            return ['ok' => false, 'error' => $response['errorMessage'] ?? 'Ошибка платёжного шлюза'];
        }
        if (empty($response['formUrl'])) {
            return ['ok' => false, 'error' => $response['errorMessage'] ?? 'Платёжный шлюз не вернул ссылку на оплату'];
        }

        return [
            'ok'      => true,
            'formUrl' => (string) $response['formUrl'],
            'orderId' => (string) ($response['orderId'] ?? ''),
        ];
    }

    /** Состав чека для фискализации (54-ФЗ). */
    private static function orderBundle(array $order, array $items): array
    {
        $positions = [];
        $position  = 1;

        foreach ($items as $item) {
            $positions[] = [
                'positionId'   => $position++,
                'name'         => mb_substr($item['name'], 0, 100),
                'quantity'     => [
                    'value'   => (int) $item['quantity'],
                    'measure' => $item['unit'],
                ],
                'itemAmount'   => (int) round((float) $item['sum'] * 100),
                'itemCode'     => 'p-' . $item['product_id'],
                'itemPrice'    => (int) round((float) $item['price'] * 100),
                'tax'          => ['taxType' => (int) Config::get('sber.vat_code', 1)],
            ];
        }

        if ((float) $order['delivery_price'] > 0) {
            $positions[] = [
                'positionId' => $position,
                'name'       => 'Доставка',
                'quantity'   => ['value' => 1, 'measure' => 'шт'],
                'itemAmount' => (int) round((float) $order['delivery_price'] * 100),
                'itemCode'   => 'delivery',
                'itemPrice'  => (int) round((float) $order['delivery_price'] * 100),
                'tax'        => ['taxType' => (int) Config::get('sber.vat_code', 1)],
            ];
        }

        return [
            'orderCreationDate' => date('Y-m-d\TH:i:s'),
            'customerDetails'   => [
                'email'    => $order['email'] ?: Config::get('sber.default_email', ''),
                'phone'    => preg_replace('/\D+/', '', (string) $order['phone']),
                'contact'  => $order['customer_name'],
            ],
            'cartItems'         => ['items' => $positions],
        ];
    }

    /** Расширенный статус заказа в шлюзе. */
    public static function status(array $order): array
    {
        if (!self::isConfigured()) {
            return ['ok' => false, 'error' => 'Онлайн-оплата не настроена'];
        }

        $params = self::credentials();
        if (!empty($order['sber_order_id'])) {
            $params['orderId'] = $order['sber_order_id'];
        } else {
            $params['orderNumber'] = $order['number'];
        }

        $response = self::request('getOrderStatusExtended.do', $params);
        self::log((int) $order['id'], 'status', $response);

        if (!isset($response['orderStatus'])) {
            return [
                'ok'    => false,
                'error' => $response['errorMessage'] ?? 'Не удалось получить статус платежа',
                'raw'   => $response,
            ];
        }

        $status = (int) $response['orderStatus'];

        return [
            'ok'        => true,
            'status'    => $status,
            'paid'      => in_array($status, [self::STATUS_HOLD, self::STATUS_PAID], true),
            'declined'  => $status === self::STATUS_DECLINED,
            'refunded'  => in_array($status, [self::STATUS_REVERSED, self::STATUS_REFUNDED], true),
            'amount'    => isset($response['amount']) ? (float) $response['amount'] / 100 : null,
            'message'   => self::statusText($status),
            'raw'       => $response,
        ];
    }

    public static function statusText(int $status): string
    {
        return match ($status) {
            self::STATUS_REGISTERED    => 'Заказ зарегистрирован, но не оплачен',
            self::STATUS_HOLD          => 'Сумма захолдирована',
            self::STATUS_PAID          => 'Оплачен',
            self::STATUS_REVERSED      => 'Авторизация отменена',
            self::STATUS_REFUNDED      => 'Оформлен возврат',
            self::STATUS_ACS_INITIATED => 'Инициирована авторизация через 3-D Secure',
            self::STATUS_DECLINED      => 'Оплата отклонена',
            default                    => 'Неизвестный статус (' . $status . ')',
        };
    }

    /** Возврат средств (полный или частичный). */
    public static function refund(array $order, ?float $amount = null): array
    {
        if (empty($order['sber_order_id'])) {
            return ['ok' => false, 'error' => 'У заказа нет идентификатора платежа'];
        }

        $params = array_merge(self::credentials(), [
            'orderId' => $order['sber_order_id'],
            'amount'  => (int) round(($amount ?? (float) $order['total']) * 100),
        ]);

        $response = self::request('refund.do', $params);
        self::log((int) $order['id'], 'refund', $response);

        $errorCode = (string) ($response['errorCode'] ?? '0');
        if ($errorCode !== '0') {
            return ['ok' => false, 'error' => $response['errorMessage'] ?? 'Ошибка возврата'];
        }
        return ['ok' => true];
    }

    /**
     * Проверка подписи callback-уведомления (симметричная, HMAC-SHA256).
     * Параметры сортируются по имени и склеиваются как key;value;
     */
    public static function verifyCallback(array $params): bool
    {
        $secret = (string) Config::get('sber.callback_token', '');
        $given  = (string) ($params['checksum'] ?? '');

        if ($secret === '' || $given === '') {
            return false;
        }

        unset($params['checksum'], $params['sign_alias']);
        ksort($params, SORT_STRING);

        $data = '';
        foreach ($params as $key => $value) {
            $data .= $key . ';' . $value . ';';
        }

        $expected = strtoupper(hash_hmac('sha256', $data, $secret));
        return hash_equals($expected, strtoupper($given));
    }

    private static function request(string $method, array $params): array
    {
        $url = self::endpoint($method);
        $ch  = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded; charset=utf-8'],
        ]);

        $body  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['errorCode' => 'curl', 'errorMessage' => 'Платёжный шлюз недоступен: ' . $error];
        }

        $decoded = json_decode((string) $body, true);
        if (!is_array($decoded)) {
            return ['errorCode' => 'parse', 'errorMessage' => 'Некорректный ответ шлюза', 'body' => (string) $body];
        }
        return $decoded;
    }

    public static function log(?int $orderId, string $event, array $payload): void
    {
        try {
            Database::instance()->insert('payment_log', [
                'order_id' => $orderId,
                'event'    => $event,
                'payload'  => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        } catch (\Throwable $e) {
            error_log('payment_log: ' . $e->getMessage());
        }
    }
}

<?php
/**
 * Клиент для REST API "Сбербанк Эквайринг" (Sberbank Acquiring / Rapida-совместимый REST API).
 * Документация: https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:start
 *
 * Перед использованием заполните SBER_USERNAME/SBER_PASSWORD (или SBER_TOKEN) в config/config.php,
 * которые выдаёт банк при подключении интернет-эквайринга.
 */
require_once __DIR__ . '/../config/config.php';

class SberbankAcquiring
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = SBER_MODE === 'prod' ? SBER_API_URL_PROD : SBER_API_URL_TEST;
    }

    /**
     * Регистрация заказа на оплату.
     * @param string $orderNumber Уникальный номер заказа в нашей системе
     * @param float  $amount      Сумма в рублях
     * @param string $description Описание заказа
     * @param string $returnUrl   URL успешного возврата
     * @param string $failUrl     URL при ошибке/отмене оплаты
     */
    public function register(string $orderNumber, float $amount, string $description, string $returnUrl, string $failUrl): array
    {
        $params = [
            'orderNumber' => $orderNumber,
            'amount'      => (int)round($amount * 100), // сумма в копейках
            'currency'    => 643,
            'returnUrl'   => $returnUrl,
            'failUrl'     => $failUrl,
            'description' => mb_substr($description, 0, 250),
        ];

        $response = $this->request('register.do', $params);

        if (isset($response['error']) || empty($response['formUrl'])) {
            return [
                'success' => false,
                'message' => $response['errorMessage'] ?? $response['error'] ?? 'Неизвестная ошибка платёжного шлюза',
            ];
        }

        return [
            'success' => true,
            'orderId' => $response['orderId'],
            'formUrl' => $response['formUrl'],
        ];
    }

    /**
     * Проверка статуса заказа по идентификатору Сбербанка (bankOrderId).
     * orderStatus: 0 - не оплачен, 1 - холдирование, 2 - оплачен, 3 - отменён,
     *              4 - возврат, 5 - авторизация ACS, 6 - отказ авторизации.
     */
    public function getOrderStatus(string $sberOrderId): array
    {
        $response = $this->request('getOrderStatusExtended.do', ['orderId' => $sberOrderId]);

        if (!isset($response['orderStatus'])) {
            return ['success' => false, 'message' => $response['errorMessage'] ?? 'Не удалось получить статус заказа'];
        }

        return [
            'success' => true,
            'orderStatus' => (int)$response['orderStatus'],
            'isPaid' => (int)$response['orderStatus'] === 2,
            'raw' => $response,
        ];
    }

    private function request(string $method, array $params): array
    {
        if (SBER_TOKEN !== '') {
            $params['token'] = SBER_TOKEN;
        } else {
            $params['userName'] = SBER_USERNAME;
            $params['password'] = SBER_PASSWORD;
        }

        $ch = curl_init($this->apiUrl . $method);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            return ['error' => 1, 'errorMessage' => 'Ошибка соединения с платёжным шлюзом: ' . $curlError];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return ['error' => 1, 'errorMessage' => 'Некорректный ответ платёжного шлюза'];
        }

        return $data;
    }
}

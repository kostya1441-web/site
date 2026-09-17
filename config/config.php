<?php
/**
 * Базовая конфигурация «Ваш фермер».
 * Локальные значения переопределяются в config/config.local.php (не хранится в git).
 */

$config = [
    'app' => [
        'name'     => 'Ваш фермер',
        'city'     => 'Новокузнецк',
        'url'      => getenv('APP_URL') ?: 'http://localhost:8000',
        'debug'    => (getenv('APP_DEBUG') ?: '1') === '1',
        'timezone' => 'Asia/Novokuznetsk',
        'locale'   => 'ru_RU',
    ],

    // driver: sqlite | mysql
    'db' => [
        'driver'   => getenv('DB_DRIVER') ?: 'sqlite',
        'sqlite'   => ['path' => dirname(__DIR__) . '/storage/shop.sqlite'],
        'mysql'    => [
            'host'     => getenv('DB_HOST') ?: '127.0.0.1',
            'port'     => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_NAME') ?: 'vash_fermer',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'charset'  => 'utf8mb4',
        ],
    ],

    // Эквайринг Сбербанка (REST API)
    'sber' => [
        'enabled'        => true,
        // true — тестовый контур 3dsec.sberbank.ru, false — боевой
        'test_mode'      => (getenv('SBER_TEST') ?: '1') === '1',
        'username'       => getenv('SBER_USER') ?: '',
        'password'       => getenv('SBER_PASS') ?: '',
        // Альтернатива логин/паролю — токен из личного кабинета
        'token'          => getenv('SBER_TOKEN') ?: '',
        // Секрет для проверки подписи колбэка (checksum), HMAC-SHA256
        'callback_token' => getenv('SBER_CALLBACK_TOKEN') ?: '',
        'return_url'     => '/checkout/success',
        'fail_url'       => '/checkout/fail',
        'currency'       => 643, // RUB
        'tax_system'     => 0,   // ОСН, для фискализации
        'vat_code'       => 1,   // Без НДС — уточняется у бухгалтера
    ],

    'shop' => [
        'free_delivery_from' => 3000,
        'delivery_price'     => 300,
        'min_order'          => 500,
        'per_page'           => 12,
    ],

    'upload' => [
        'dir'        => dirname(__DIR__) . '/public/uploads',
        'max_size'   => 6 * 1024 * 1024,
        'mime'       => ['image/jpeg', 'image/png', 'image/webp'],
    ],

    'security' => [
        'session_name' => 'vf_session',
    ],
];

$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        $merge = static function (array $base, array $over) use (&$merge): array {
            foreach ($over as $key => $value) {
                $base[$key] = is_array($value) && isset($base[$key]) && is_array($base[$key])
                    ? $merge($base[$key], $value)
                    : $value;
            }
            return $base;
        };
        $config = $merge($config, $local);
    }
}

return $config;

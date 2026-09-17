<?php
/**
 * Скопируйте в config/config.local.php и заполните боевыми значениями.
 */
return [
    'app' => [
        'url'   => 'https://vash-fermer42.ru',
        'debug' => false,
    ],
    'db' => [
        'driver' => 'mysql',
        'mysql'  => [
            'host'     => 'localhost',
            'database' => 'vash_fermer',
            'username' => 'vash_fermer',
            'password' => 'ЗАМЕНИТЕ',
        ],
    ],
    'sber' => [
        'test_mode'      => false,
        'username'       => 'логин-api',
        'password'       => 'пароль-api',
        'callback_token' => 'секрет-из-лк-сбербанка',
    ],
];

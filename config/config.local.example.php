<?php
/**
 * Скопируйте в config/config.local.php и заполните боевыми значениями.
 * Этот файл создаётся автоматически при установке через /install.php.
 */
return [
    'app' => [
        'url'   => 'https://vash-fermer42.ru',
        'debug' => false,
    ],
    'db' => [
        'host'     => 'localhost',
        'port'     => '3306',
        'database' => 'vash_fermer',
        'username' => 'vash_fermer',
        'password' => 'ЗАМЕНИТЕ',
    ],
    'sber' => [
        'test_mode'      => false,
        'username'       => 'логин-api',
        'password'       => 'пароль-api',
        'callback_token' => 'секрет-из-лк-сбербанка',
    ],
];

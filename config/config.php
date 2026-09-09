<?php
/**
 * Основной файл конфигурации сайта "Ваш фермер".
 * Заполните реальными данными перед публикацией сайта.
 */

// ------------------------------------------------------------------
// База данных
// ------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'vashfermer');
define('DB_USER', 'vashfermer_user');
define('DB_PASS', 'change_me');
define('DB_CHARSET', 'utf8mb4');

// ------------------------------------------------------------------
// Общие параметры сайта
// ------------------------------------------------------------------
define('SITE_URL', 'https://vashfermer-nvkz.ru'); // без слэша на конце
define('SITE_TIMEZONE', 'Asia/Novokuznetsk');

// ------------------------------------------------------------------
// Сбербанк Эквайринг (Sberbank Acquiring REST API)
// Личный кабинет: https://securepayments.sberbank.ru/
// Тестовый стенд:  https://3dsec.sberbank.ru/
// ------------------------------------------------------------------
define('SBER_MODE', 'test'); // 'test' или 'prod'
define('SBER_USERNAME', ''); // логин продавца (userName), выдаётся банком
define('SBER_PASSWORD', ''); // пароль продавца (password), выдаётся банком
define('SBER_TOKEN', '');    // либо токен, если используется token-based авторизация

define('SBER_API_URL_PROD', 'https://securepayments.sberbank.ru/payment/rest/');
define('SBER_API_URL_TEST', 'https://3dsec.sberbank.ru/payment/rest/');

// Куда пользователь вернётся после оплаты
define('SBER_RETURN_URL', SITE_URL . '/order_success.php');
define('SBER_FAIL_URL', SITE_URL . '/order_fail.php');

// ------------------------------------------------------------------
// Служебное
// ------------------------------------------------------------------
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0'); // на продакшене ошибки не выводить в браузер
date_default_timezone_set(SITE_TIMEZONE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

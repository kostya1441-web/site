<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quartirnik');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Квартирник');
define('SITE_URL', 'http://localhost');
define('SITE_PHONE', '+7 (3843) 00-00-00');
define('SITE_ADDRESS', 'г. Новокузнецк, ул. Кирова, 15');
define('SITE_EMAIL', 'hello@kvartirnik-nk.ru');

// Альфа-Банк эквайринг
// Тестовый стенд:  https://alfa.rbsuat.com/payment/rest/
// Боевой стенд:    https://pay.alfabank.ru/payment/rest/
define('ALFABANK_USERNAME', 'YOUR_USERNAME');   // логин из личного кабинета
define('ALFABANK_PASSWORD', 'YOUR_PASSWORD');   // пароль из личного кабинета
define('ALFABANK_TEST_MODE', true);             // false — боевой режим
define('ALFABANK_RETURN_URL', SITE_URL . '/order-confirm.php');

// Session
define('ADMIN_SESSION_KEY', 'quartirnik_admin');

// Upload
define('UPLOAD_DIR', __DIR__ . '/../assets/images/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/images/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

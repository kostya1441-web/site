<?php

use App\Core\Config;
use App\Core\Session;

define('APP_ROOT', dirname(__DIR__));

// PSR-4-подобный автозагрузчик для пространства имён App\
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file     = APP_ROOT . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/Core/helpers.php';

Config::load(require APP_ROOT . '/config/config.php');

date_default_timezone_set(Config::get('app.timezone', 'Asia/Novokuznetsk'));
mb_internal_encoding('UTF-8');

if (Config::get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', APP_ROOT . '/storage/logs/php-error.log');
}

Session::start();

// Данные формы предыдущего запроса доступны в шаблонах через old()
$GLOBALS['_old_input'] = Session::takeOld();

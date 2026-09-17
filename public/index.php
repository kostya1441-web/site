<?php

/**
 * Единая точка входа «Ваш фермер».
 * Все запросы проходят через .htaccess / nginx rewrite сюда.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Models\Setting;
use App\Services\Cart;

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$request = new Request();

// Общие данные для всех шаблонов
View::share('settings', Setting::all());
View::share('cartCount', Cart::count());
View::share('flashes', Session::takeFlash());
View::share('shopConfig', Config::get('shop'));

$router = new Router();
require APP_ROOT . '/app/routes.php';

try {
    $router->dispatch($request);
} catch (Throwable $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

    if (Config::get('app.debug')) {
        Response::status(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo $e->getMessage() . "\n\n" . $e->getTraceAsString();
        exit;
    }

    Response::status(500);
    (new App\Controllers\ErrorController($request))->show(500, 'Мы уже чиним. Попробуйте обновить страницу через минуту.');
}

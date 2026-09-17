<?php

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MaintenanceController;
use App\Controllers\Admin\MessageController as AdminMessageController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\PaymentController;
use App\Core\AdminMiddleware;
use App\Core\Router;

/** @var Router $router */

// ── Витрина ────────────────────────────────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);
$router->get('/catalog', [CatalogController::class, 'index']);
$router->get('/catalog/{slug}', [CatalogController::class, 'category']);
$router->get('/product/{slug}', [CatalogController::class, 'product']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/contacts', [PageController::class, 'contacts']);
$router->get('/delivery', [PageController::class, 'delivery']);
$router->post('/feedback', [PageController::class, 'feedback']);
$router->get('/robots.txt', [PageController::class, 'robots']);
$router->get('/sitemap.xml', [PageController::class, 'sitemap']);

// ── Корзина ────────────────────────────────────────────────────────────────
$router->get('/cart', [CartController::class, 'index']);
$router->get('/cart/state', [CartController::class, 'state']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

// ── Оформление и оплата ────────────────────────────────────────────────────
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'store']);
$router->get('/checkout/success', [CheckoutController::class, 'success']);
$router->get('/checkout/fail', [CheckoutController::class, 'fail']);
$router->get('/checkout/retry', [CheckoutController::class, 'retry']);
$router->any(['GET', 'POST'], '/payment/callback', [PaymentController::class, 'callback']);

// ── Админка ────────────────────────────────────────────────────────────────
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login']);

$router->group('/admin', [AdminMiddleware::class], static function (Router $router): void {
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->get('', [DashboardController::class, 'index']);

    $router->get('/orders', [AdminOrderController::class, 'index']);
    $router->get('/orders/export', [AdminOrderController::class, 'export']);
    $router->get('/orders/{id:\d+}', [AdminOrderController::class, 'show']);
    $router->post('/orders/{id:\d+}/status', [AdminOrderController::class, 'updateStatus']);
    $router->post('/orders/{id:\d+}/payment', [AdminOrderController::class, 'updatePayment']);
    $router->post('/orders/{id:\d+}/note', [AdminOrderController::class, 'saveNote']);
    $router->post('/orders/{id:\d+}/check-payment', [AdminOrderController::class, 'checkPayment']);
    $router->post('/orders/{id:\d+}/refund', [AdminOrderController::class, 'refund']);
    $router->post('/orders/{id:\d+}/delete', [AdminOrderController::class, 'destroy']);

    $router->post('/update-database', [MaintenanceController::class, 'updateDatabase']);

    $router->get('/messages', [AdminMessageController::class, 'index']);
    $router->post('/messages/{id:\d+}/status', [AdminMessageController::class, 'updateStatus']);
    $router->post('/messages/{id:\d+}/note', [AdminMessageController::class, 'saveNote']);
    $router->post('/messages/{id:\d+}/delete', [AdminMessageController::class, 'destroy']);

    $router->get('/products', [AdminProductController::class, 'index']);
    $router->get('/products/create', [AdminProductController::class, 'create']);
    $router->post('/products', [AdminProductController::class, 'store']);
    $router->get('/products/{id:\d+}/edit', [AdminProductController::class, 'edit']);
    $router->post('/products/{id:\d+}', [AdminProductController::class, 'update']);
    $router->post('/products/{id:\d+}/delete', [AdminProductController::class, 'destroy']);
    $router->post('/products/{id:\d+}/toggle/{field:[a-z_]+}', [AdminProductController::class, 'toggle']);

    $router->get('/categories', [AdminCategoryController::class, 'index']);
    $router->get('/categories/create', [AdminCategoryController::class, 'create']);
    $router->post('/categories', [AdminCategoryController::class, 'store']);
    $router->get('/categories/{id:\d+}/edit', [AdminCategoryController::class, 'edit']);
    $router->post('/categories/{id:\d+}', [AdminCategoryController::class, 'update']);
    $router->post('/categories/{id:\d+}/delete', [AdminCategoryController::class, 'destroy']);

    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings', [SettingsController::class, 'save']);
    $router->post('/settings/password', [SettingsController::class, 'changePassword']);
});

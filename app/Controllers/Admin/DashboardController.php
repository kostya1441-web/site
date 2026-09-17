<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title'        => 'Панель управления',
            'stats'        => Order::stats(),
            'chart'        => Order::revenueByDay(14),
            'recentOrders' => Order::recent(8),
            'topProducts'  => Order::topProducts(5),
            'lowStock'     => Product::lowStock(5, 8),
            'newMessages'  => Message::countNew(),
            'productCount' => Product::countAll(),
            'categoryCount' => count(Category::all()),
        ], 'admin');
    }
}

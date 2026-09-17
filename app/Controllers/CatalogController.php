<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index(): void
    {
        $this->render(null);
    }

    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if (!$category) {
            $this->abort(404, 'Категория не найдена');
        }
        $this->render($category);
    }

    private function render(?array $category): void
    {
        $filters = [
            'category'   => $category['id'] ?? null,
            'search'     => $this->request->string('q'),
            'sort'       => $this->request->string('sort', 'popular'),
            'min'        => $this->request->float('min'),
            'max'        => $this->request->float('max'),
            'only_stock' => $this->request->bool('stock'),
        ];

        $result = Product::paginate(
            $filters,
            max(1, $this->request->int('page', 1)),
            (int) Config::get('shop.per_page', 12)
        );

        $title = $category['name'] ?? 'Каталог фермерских продуктов';
        if ($filters['search'] !== '') {
            $title = 'Поиск: ' . $filters['search'];
        }

        $this->view('pages/catalog', [
            'title'       => $title . ' — Ваш фермер, Новокузнецк',
            'heading'     => $category['name'] ?? ($filters['search'] !== '' ? 'Результаты поиска' : 'Каталог'),
            'description' => $category['description'] ?? 'Фермерское мясо, птица, ягода и деликатесы с доставкой по Новокузнецку.',
            'category'    => $category,
            'categories'  => Category::active(),
            'products'    => $result['items'],
            'pagination'  => $result,
            'filters'     => $filters,
        ]);
    }

    public function product(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            $this->abort(404, 'Товар не найден');
        }

        $this->view('pages/product', [
            'title'       => $product['name'] . ' — купить в Новокузнецке | Ваш фермер',
            'description' => excerpt($product['short_description'] ?: $product['description'], 160),
            'product'     => $product,
            'related'     => Product::related($product, 4),
            'categories'  => Category::active(),
        ]);
    }
}

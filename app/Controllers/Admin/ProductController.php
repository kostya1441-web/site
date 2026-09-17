<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploader;

class ProductController extends Controller
{
    public function index(): void
    {
        $filters = [
            'category' => $this->request->int('category'),
            'search'   => $this->request->string('q'),
        ];
        $result = Product::adminList($filters, max(1, $this->request->int('page', 1)), 20);

        $this->view('admin/products/index', [
            'title'      => 'Каталог товаров',
            'products'   => $result['items'],
            'pagination' => $result,
            'filters'    => $filters,
            'categories' => Category::all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/products/form', [
            'title'      => 'Новый товар',
            'product'    => null,
            'categories' => Category::all(),
        ], 'admin');
    }

    public function edit(string $id): void
    {
        $product = Product::find((int) $id);
        if (!$product) {
            $this->abort(404, 'Товар не найден');
            return;
        }

        $this->view('admin/products/form', [
            'title'      => 'Товар: ' . $product['name'],
            'product'    => $product,
            'categories' => Category::all(),
        ], 'admin');
    }

    public function store(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $data = $this->collect();
        if (($error = $this->validate($data)) !== null) {
            Session::flashInput($this->request->all());
            Session::flash('error', $error);
            $this->redirect('/admin/products/create');
            return;
        }

        if ($file = $this->request->file('image')) {
            $upload = ImageUploader::store($file);
            if (!$upload['ok']) {
                Session::flash('error', $upload['error']);
                $this->redirect('/admin/products/create');
                return;
            }
            $data['image'] = $upload['name'];
        }

        $id = Product::create($data);
        Session::flash('success', 'Товар добавлен');
        $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function update(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $product = Product::find((int) $id);
        if (!$product) {
            $this->abort(404, 'Товар не найден');
            return;
        }

        $data = $this->collect();
        if (($error = $this->validate($data)) !== null) {
            Session::flash('error', $error);
            $this->redirect('/admin/products/' . $id . '/edit');
            return;
        }

        if ($file = $this->request->file('image')) {
            $upload = ImageUploader::store($file);
            if (!$upload['ok']) {
                Session::flash('error', $upload['error']);
                $this->redirect('/admin/products/' . $id . '/edit');
                return;
            }
            ImageUploader::delete($product['image']);
            $data['image'] = $upload['name'];
        } elseif ($this->request->bool('remove_image')) {
            ImageUploader::delete($product['image']);
            $data['image'] = null;
        }

        Product::update((int) $id, $data);
        Session::flash('success', 'Изменения сохранены');
        $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function destroy(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        $product = Product::find((int) $id);
        if ($product) {
            ImageUploader::delete($product['image']);
            Product::delete((int) $id);
            Session::flash('success', 'Товар удалён');
        }
        $this->redirect('/admin/products');
    }

    /** Быстрое переключение «активен» / «хит» прямо из списка. */
    public function toggle(string $id, string $field): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        try {
            $value = Product::toggle((int) $id, $field);
        } catch (\InvalidArgumentException $e) {
            $this->json(['ok' => false, 'error' => $e->getMessage()], 422);
            return;
        }

        if ($this->request->isAjax()) {
            $this->json(['ok' => true, 'value' => $value]);
            return;
        }
        $this->back('/admin/products');
    }

    private function collect(): array
    {
        $unit = $this->request->string('unit', 'кг');
        if (!in_array($unit, Product::UNITS, true)) {
            $unit = 'кг';
        }

        $oldPrice = $this->request->float('old_price');
        $weight   = $this->request->float('weight');

        return [
            'category_id'       => $this->request->int('category_id') ?: null,
            'name'              => $this->request->string('name'),
            'slug'              => $this->request->string('slug'),
            'sku'               => $this->request->string('sku'),
            'short_description' => $this->request->string('short_description'),
            'description'       => (string) $this->request->post('description', ''),
            'price'             => round($this->request->float('price'), 2),
            'old_price'         => $oldPrice > 0 ? round($oldPrice, 2) : null,
            'unit'              => $unit,
            'weight'            => $weight > 0 ? $weight : null,
            'stock'             => max(0, $this->request->int('stock')),
            'is_active'         => $this->request->bool('is_active') ? 1 : 0,
            'is_featured'       => $this->request->bool('is_featured') ? 1 : 0,
            'sort_order'        => $this->request->int('sort_order'),
        ];
    }

    private function validate(array $data): ?string
    {
        $validator = (new Validator($data))
            ->required('name', 'Введите название товара')
            ->maxLength('name', 190, 'Название слишком длинное')
            ->maxLength('short_description', 500, 'Краткое описание — не больше 500 символов')
            ->min('price', 0.01, 'Цена должна быть больше нуля');

        if ($data['old_price'] !== null && $data['old_price'] <= $data['price']) {
            $validator->addError('old_price', 'Старая цена должна быть выше текущей');
        }

        return $validator->fails() ? $validator->firstError() : null;
    }
}

<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Services\ImageUploader;

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->view('admin/categories/index', [
            'title'      => 'Категории',
            'categories' => Category::all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/categories/form', [
            'title'    => 'Новая категория',
            'category' => null,
        ], 'admin');
    }

    public function edit(string $id): void
    {
        $category = Category::find((int) $id);
        if (!$category) {
            $this->abort(404, 'Категория не найдена');
            return;
        }
        $this->view('admin/categories/form', [
            'title'    => 'Категория: ' . $category['name'],
            'category' => $category,
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
            $this->redirect('/admin/categories/create');
            return;
        }

        if ($file = $this->request->file('image')) {
            $upload = ImageUploader::store($file, 'c');
            if (!$upload['ok']) {
                Session::flash('error', $upload['error']);
                $this->redirect('/admin/categories/create');
                return;
            }
            $data['image'] = $upload['name'];
        }

        Category::create($data);
        Session::flash('success', 'Категория создана');
        $this->redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $category = Category::find((int) $id);
        if (!$category) {
            $this->abort(404, 'Категория не найдена');
            return;
        }

        $data = $this->collect();
        if (($error = $this->validate($data)) !== null) {
            Session::flash('error', $error);
            $this->redirect('/admin/categories/' . $id . '/edit');
            return;
        }

        if ($file = $this->request->file('image')) {
            $upload = ImageUploader::store($file, 'c');
            if (!$upload['ok']) {
                Session::flash('error', $upload['error']);
                $this->redirect('/admin/categories/' . $id . '/edit');
                return;
            }
            ImageUploader::delete($category['image']);
            $data['image'] = $upload['name'];
        } elseif ($this->request->bool('remove_image')) {
            ImageUploader::delete($category['image']);
            $data['image'] = null;
        }

        Category::update((int) $id, $data);
        Session::flash('success', 'Категория обновлена');
        $this->redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        $category = Category::find((int) $id);
        if ($category) {
            ImageUploader::delete($category['image']);
            Category::delete((int) $id);
            Session::flash('success', 'Категория удалена, товары остались без категории');
        }
        $this->redirect('/admin/categories');
    }

    private function collect(): array
    {
        return [
            'name'        => $this->request->string('name'),
            'slug'        => $this->request->string('slug'),
            'description' => $this->request->string('description'),
            'icon'        => mb_substr($this->request->string('icon'), 0, 8),
            'sort_order'  => $this->request->int('sort_order'),
            'is_active'   => $this->request->bool('is_active') ? 1 : 0,
        ];
    }

    private function validate(array $data): ?string
    {
        $validator = (new Validator($data))
            ->required('name', 'Введите название категории')
            ->maxLength('name', 190, 'Название слишком длинное')
            ->maxLength('description', 1000, 'Описание слишком длинное');

        return $validator->fails() ? $validator->firstError() : null;
    }
}

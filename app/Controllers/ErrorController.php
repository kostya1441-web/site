<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->show(404, 'Такой страницы нет. Возможно, товар уже разобрали.');
    }

    public function show(int $code, string $message = ''): void
    {
        $titles = [
            404 => 'Страница не найдена',
            405 => 'Метод не поддерживается',
            419 => 'Сессия устарела',
            500 => 'Что-то пошло не так',
        ];

        $this->view('pages/error', [
            'title'      => ($titles[$code] ?? 'Ошибка') . ' — Ваш фермер',
            'code'       => $code,
            'heading'    => $titles[$code] ?? 'Ошибка',
            'message'    => $message ?: 'Попробуйте вернуться на главную или написать нам.',
            'categories' => Category::active(),
        ]);
    }
}

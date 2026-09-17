<?php

namespace App\Core;

abstract class Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function view(string $template, array $data = [], string $layout = 'main'): void
    {
        View::render($template, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function back(string $fallback = '/'): void
    {
        Response::back($fallback);
    }

    /** Проверка CSRF для всех POST-запросов. */
    protected function requireCsrf(): bool
    {
        if (Csrf::check($this->request->post('_token'))) {
            return true;
        }

        if ($this->request->isAjax()) {
            $this->json(['ok' => false, 'error' => 'Сессия устарела, обновите страницу'], 419);
        } else {
            Session::flash('error', 'Сессия устарела. Попробуйте ещё раз.');
            $this->back();
        }
        return false;
    }

    protected function abort(int $code, string $message = ''): void
    {
        Response::status($code);
        (new \App\Controllers\ErrorController($this->request))->show($code, $message);
        exit;
    }
}

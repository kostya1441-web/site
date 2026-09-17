<?php

namespace App\Core;

class AdminMiddleware
{
    public function handle(Request $request): bool
    {
        if (Auth::check()) {
            return true;
        }

        if ($request->isAjax()) {
            Response::json(['ok' => false, 'error' => 'Требуется авторизация'], 401);
            return false;
        }

        Session::set('_intended', $request->path());
        Response::redirect('/admin/login');
        return false;
    }
}

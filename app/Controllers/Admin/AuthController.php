<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }
        $this->view('admin/login', ['title' => 'Вход в админку — Ваш фермер'], 'auth');
    }

    public function login(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        if (Auth::throttled()) {
            Session::flash('error', 'Слишком много попыток входа. Повторите через 10 минут.');
            $this->redirect('/admin/login');
            return;
        }

        $login    = $this->request->string('login');
        $password = (string) $this->request->post('password', '');

        if (!Auth::attempt($login, $password)) {
            Auth::registerFailure();
            Session::flashInput(['login' => $login]);
            Session::flash('error', 'Неверный логин или пароль');
            $this->redirect('/admin/login');
            return;
        }

        Auth::clearFailures();
        $intended = Session::get('_intended', '/admin');
        Session::forget('_intended');
        $this->redirect($intended);
    }

    public function logout(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Auth::logout();
        Session::flash('success', 'Вы вышли из админки');
        $this->redirect('/admin/login');
    }
}

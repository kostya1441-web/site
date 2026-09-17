<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Setting;
use App\Models\User;
use App\Services\SberPayment;

class SettingsController extends Controller
{
    /** Поля, которые редактируются через админку. */
    private const FIELDS = [
        'site_title', 'site_description', 'phone', 'phone_extra', 'email', 'address',
        'work_hours', 'map_lat', 'map_lng', 'map_zoom', 'inn', 'legal_name',
        'telegram', 'whatsapp', 'vk', 'delivery_text', 'about_lead', 'about_text', 'notify_email',
    ];

    public function index(): void
    {
        $this->view('admin/settings', [
            'title'      => 'Настройки сайта',
            'settings'   => Setting::all(),
            'sberReady'  => SberPayment::isConfigured(),
            'sberTest'   => (bool) \App\Core\Config::get('sber.test_mode'),
            'user'       => Auth::user(),
        ], 'admin');
    }

    public function save(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        // Сохраняем только те поля, что реально пришли из формы,
        // иначе частичная отправка затёрла бы остальные настройки.
        $values = [];
        foreach (self::FIELDS as $field) {
            $value = $this->request->post($field);
            if ($value !== null) {
                $values[$field] = (string) $value;
            }
        }

        Setting::setMany($values);
        Session::flash('success', 'Настройки сохранены');
        $this->redirect('/admin/settings');
    }

    public function changePassword(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $user    = Auth::user();
        $current = (string) $this->request->post('current_password', '');
        $new     = (string) $this->request->post('new_password', '');
        $repeat  = (string) $this->request->post('repeat_password', '');

        if (!$user || !password_verify($current, $user['password_hash'])) {
            Session::flash('error', 'Текущий пароль указан неверно');
            $this->redirect('/admin/settings');
            return;
        }
        if (mb_strlen($new) < 8) {
            Session::flash('error', 'Новый пароль должен быть не короче 8 символов');
            $this->redirect('/admin/settings');
            return;
        }
        if ($new !== $repeat) {
            Session::flash('error', 'Пароли не совпадают');
            $this->redirect('/admin/settings');
            return;
        }

        User::updatePassword((int) $user['id'], $new);
        Session::flash('success', 'Пароль обновлён');
        $this->redirect('/admin/settings');
    }
}

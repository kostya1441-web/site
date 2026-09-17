<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Message;
use App\Models\Setting;
use App\Services\Notifier;

class PageController extends Controller
{
    public function about(): void
    {
        $this->view('pages/about', [
            'title'       => 'О нас — фермерское хозяйство «Ваш фермер», Новокузнецк',
            'description' => 'Семейное хозяйство из Кузбасса: своё мясо, птица и таёжная ягода с доставкой по Новокузнецку.',
            'categories'  => Category::active(),
        ]);
    }

    public function contacts(): void
    {
        $this->view('pages/contacts', [
            'title'       => 'Контакты — Ваш фермер, Новокузнецк',
            'description' => 'Адрес, телефоны и карта проезда фермерского магазина «Ваш фермер» в Новокузнецке.',
            'categories'  => Category::active(),
        ]);
    }

    public function delivery(): void
    {
        $this->view('pages/delivery', [
            'title'       => 'Доставка и оплата — Ваш фермер',
            'description' => 'Условия доставки по Новокузнецку, самовывоз и способы оплаты, включая онлайн-оплату картой Сбербанка.',
            'categories'  => Category::active(),
        ]);
    }

    /** Форма обратной связи на странице контактов. */
    public function feedback(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $data = [
            'name'    => $this->request->string('name'),
            'phone'   => $this->request->string('phone'),
            'email'   => $this->request->string('email'),
            'message' => $this->request->string('message'),
        ];

        $validator = (new Validator($data))
            ->required('name', 'Представьтесь, пожалуйста')
            ->maxLength('name', 120, 'Слишком длинное имя')
            ->required('phone', 'Укажите телефон для связи')
            ->phone('phone', 'Телефон выглядит некорректно')
            ->email('email', 'Проверьте адрес электронной почты')
            ->required('message', 'Напишите вопрос')
            ->maxLength('message', 2000, 'Слишком длинное сообщение');

        if ($validator->fails()) {
            Session::flashInput($data);
            Session::flash('error', (string) $validator->firstError());
            $this->redirect('/contacts#feedback');
            return;
        }

        // Письмо может не дойти (на хостинге бывает отключён mail), поэтому
        // обращение прежде всего сохраняем в базу — менеджер увидит его в админке.
        $sent = Notifier::feedback($data);

        try {
            Message::create($data + ['ip' => $this->request->ip(), 'mail_sent' => $sent]);
        } catch (\Throwable $e) {
            error_log('Не удалось сохранить обращение: ' . $e->getMessage());
            if (!$sent) {
                Session::flashInput($data);
                Session::flash('error', 'Не получилось отправить сообщение. Позвоните нам, пожалуйста: ' . Setting::get('phone'));
                $this->redirect('/contacts#feedback');
                return;
            }
        }

        Session::flash('success', 'Спасибо! Мы получили ваш вопрос и перезвоним в ближайшее время.');
        $this->redirect('/contacts#feedback');
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $url = rtrim((string) \App\Core\Config::get('app.url'), '/');
        echo "User-agent: *\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nAllow: /\n\nSitemap: {$url}/sitemap.xml\n";
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base  = rtrim((string) \App\Core\Config::get('app.url'), '/');
        $urls  = ['/', '/catalog', '/about', '/contacts', '/delivery'];

        foreach (Category::active() as $category) {
            $urls[] = '/catalog/' . $category['slug'];
        }
        foreach (\App\Core\Database::instance()->all('SELECT slug FROM products WHERE is_active = 1') as $row) {
            $urls[] = '/product/' . $row['slug'];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $path) {
            echo '  <url><loc>' . e($base . $path) . '</loc></url>' . "\n";
        }
        echo '</urlset>';
    }
}

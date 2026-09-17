<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Setting;

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
            'message' => $this->request->string('message'),
        ];

        $validator = (new Validator($data))
            ->required('name', 'Представьтесь, пожалуйста')
            ->required('phone', 'Укажите телефон для связи')
            ->phone('phone', 'Телефон выглядит некорректно')
            ->required('message', 'Напишите вопрос')
            ->maxLength('message', 2000, 'Слишком длинное сообщение');

        if ($validator->fails()) {
            Session::flashInput($data);
            Session::flash('error', (string) $validator->firstError());
            $this->redirect('/contacts#feedback');
            return;
        }

        $to = Setting::get('notify_email');
        if ($to !== '' && function_exists('mail')) {
            $body = "Вопрос с сайта\n\nИмя: {$data['name']}\nТелефон: {$data['phone']}\n\n{$data['message']}";
            @mail($to, '=?UTF-8?B?' . base64_encode('Вопрос с сайта «Ваш фермер»') . '?=', $body, "Content-Type: text/plain; charset=UTF-8\r\n");
        }

        Session::flash('success', 'Спасибо! Мы позвоним вам в ближайшее время.');
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

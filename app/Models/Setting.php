<?php

namespace App\Models;

use App\Core\Database;

/**
 * Контентные настройки сайта (телефоны, адрес, тексты, карта),
 * редактируются в админке без правки кода.
 */
class Setting
{
    private static ?array $cache = null;

    public const DEFAULTS = [
        'site_title'        => 'Ваш фермер — фермерские продукты в Новокузнецке',
        'site_description'  => 'Мясо, птица, ягода и фермерские деликатесы с доставкой по Новокузнецку. Свежесть с фермы — за 24 часа.',
        'phone'             => '+7 (3843) 55-11-22',
        'phone_extra'       => '+7 (923) 555-11-22',
        'email'             => 'zakaz@vash-fermer42.ru',
        'address'           => 'г. Новокузнецк, пр. Металлургов, 24',
        'work_hours'        => 'Ежедневно с 9:00 до 20:00',
        'map_lat'           => '53.757547',
        'map_lng'           => '87.136044',
        'map_zoom'          => '16',
        'inn'               => '422001234567',
        'legal_name'        => 'ИП Фермерское хозяйство «Ваш фермер»',
        'telegram'          => 'https://t.me/vashfermer42',
        'whatsapp'          => 'https://wa.me/79235551122',
        'vk'                => 'https://vk.com/vashfermer42',
        'delivery_text'     => 'Доставляем по Новокузнецку в день заказа при оформлении до 14:00. Бесплатно от 3 000 ₽.',
        'about_lead'        => 'Мы — семейное хозяйство из Кемеровской области. С 2014 года выращиваем скот и собираем таёжную ягоду, а с 2019 года возим продукты напрямую жителям Новокузнецка.',
        'about_text'        => "Наше хозяйство расположено в 60 км от Новокузнецка. Мы держим коров абердин-ангусской породы, свиней и птицу на свободном выгуле, а ягоду собираем в тайге Горной Шории.\n\nМы не работаем с посредниками: от фермы до вашего стола продукт проходит меньше суток. Каждая партия мяса сопровождается ветеринарным свидетельством, а ягода и грибы проходят радиологический контроль.",
        'notify_email'      => 'zakaz@vash-fermer42.ru',
    ];

    public static function all(): array
    {
        if (self::$cache === null) {
            $rows = Database::instance()->all('SELECT `key`, `value` FROM settings');
            $map  = [];
            foreach ($rows as $row) {
                $map[$row['key']] = $row['value'];
            }
            self::$cache = array_merge(self::DEFAULTS, $map);
        }
        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        return (string) ($all[$key] ?? $default);
    }

    public static function set(string $key, string $value): void
    {
        $db = Database::instance();
        $exists = (int) $db->value('SELECT COUNT(*) FROM settings WHERE `key` = ?', [$key]) > 0;
        if ($exists) {
            $db->run('UPDATE settings SET `value` = ? WHERE `key` = ?', [$value, $key]);
        } else {
            $db->run('INSERT INTO settings (`key`, `value`) VALUES (?, ?)', [$key, $value]);
        }
        self::$cache = null;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::set((string) $key, (string) $value);
        }
    }
}

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// =============================================
// Настройки сайта
// =============================================
define('SITE_NAME', 'PrideCS2');
define('SITE_DESCRIPTION', 'Лучший CS2 сервер');

// =============================================
// Настройки базы данных (LvlRanks/Shop)
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'cs2_server');

// =============================================
// Таблицы плагинов
// =============================================
define('LVLRANKS_TABLE', 'lvl_base');       // LvlRanks таблица
define('SHOP_TABLE', 'shop_items');          // Товары магазина
define('PURCHASES_TABLE', 'shop_purchases'); // Покупки

// =============================================
// Серверы CS2 (IP:Port)
// =============================================
define('SERVERS', [
    ['name' => 'Dust2 Only',      'ip' => '194.87.1.1', 'port' => 27015, 'map_img' => 'dust2'],
    ['name' => 'Mirage Only',     'ip' => '194.87.1.2', 'port' => 27015, 'map_img' => 'mirage'],
    ['name' => 'Competitive Mix', 'ip' => '194.87.1.3', 'port' => 27015, 'map_img' => 'inferno'],
]);

// =============================================
// Платёжная система (YooKassa)
// =============================================
define('PAYMENT_SHOP_ID', 'YOUR_SHOP_ID');
define('PAYMENT_SECRET',  'YOUR_SECRET_KEY');
define('PAYMENT_RETURN_URL', 'https://yourdomain.ru/donate.php?success=1');

// =============================================
// Steam Web API (для профилей)
// =============================================
define('STEAM_API_KEY', 'YOUR_STEAM_API_KEY');

// =============================================
// Донат-пакеты
// =============================================
// =============================================
// Суперадмин (SteamID64)
// =============================================
define('SUPERADMIN_STEAMID64', ''); // Укажите SteamID64 владельца

define('DONATE_PACKAGES', [
    [
        'id'       => 'vip',
        'name'     => 'VIP',
        'price'    => 199,
        'color'    => '#f59e0b',
        'duration' => '30 дней',
        'perks'    => [
            'Тег [VIP] в чате',
            'Доступ к скинам оружий',
            'Приоритет в очереди',
            'Иммунитет к авто-бану по пингу',
        ],
    ],
    [
        'id'       => 'premium',
        'name'     => 'Premium',
        'price'    => 349,
        'color'    => '#3b82f6',
        'duration' => '30 дней',
        'perks'    => [
            'Всё из VIP',
            'Тег [PREMIUM] в чате',
            'Зарезервированный слот',
            'Доступ к редким скинам',
            'Кастомный цвет ника',
        ],
    ],
    [
        'id'       => 'elite',
        'name'     => 'Elite',
        'price'    => 599,
        'color'    => '#8b5cf6',
        'duration' => '30 дней',
        'perks'    => [
            'Всё из Premium',
            'Тег [ELITE] в чате',
            'Доступ к AdminMenu',
            'Kick игроков (не admin)',
            'Сменa карты голосованием',
            'Бесплатный респавн x1',
        ],
    ],
]);

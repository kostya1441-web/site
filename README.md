# CS2 Server Website

Сайт для CS2-сервера с интеграцией LvlRanks, магазином привилегий и онлайн-статусом серверов.

## Структура

```
├── index.php          — Главная страница
├── rating.php         — Рейтинг игроков (LvlRanks)
├── donate.php         — Магазин привилегий
├── rules.php          — Правила сервера
├── news.php           — Новости
├── config.php         — ВСЕ настройки (БД, серверы, донат)
├── api/
│   └── server_status.php  — JSON-API онлайна серверов
├── includes/
│   ├── db.php         — PDO-подключение к MySQL
│   ├── functions.php  — Вспомогательные функции
│   ├── header.php     — Шапка сайта
│   └── footer.php     — Подвал сайта
└── assets/
    ├── css/style.css  — Стили (тёмная тема)
    ├── js/main.js     — JS (меню, онлайн серверов)
    └── img/           — Изображения
```

## Настройка

### 1. База данных

Отредактируй `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'cs2_server'); // имя БД, где стоит LvlRanks
```

LvlRanks по умолчанию пишет в таблицу `lvl_base`. Если у тебя другое имя — измени `LVLRANKS_TABLE`.

### 2. Серверы

```php
define('SERVERS', [
    ['name' => 'Dust2 Only', 'ip' => '1.2.3.4', 'port' => 27015, 'map_img' => 'dust2'],
]);
```

### 3. Донат (YooKassa)

Раскомментируй блок с YooKassa в `donate.php` и заполни:
```php
define('PAYMENT_SHOP_ID', 'ваш_shop_id');
define('PAYMENT_SECRET',  'ваш_secret_key');
```

Установи SDK: `composer require yoomoney/yookassa-sdk-php`

### 4. Steam API (аватарки в рейтинге)

```php
define('STEAM_API_KEY', 'ваш_ключ'); // получить на steamcommunity.com/dev/apikey
```

## Требования

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache с mod_rewrite (или Nginx)
- LvlRanks плагин с настроенной MySQL-БД

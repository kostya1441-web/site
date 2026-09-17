<?php

/**
 * Установка магазина: создаёт таблицы, администратора и (по желанию) демо-каталог.
 *
 *   php bin/install.php --admin-password=СЕКРЕТ [--admin-login=admin] [--demo] [--fresh]
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;
use App\Core\Database;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;

$options = getopt('', ['admin-login::', 'admin-password::', 'demo', 'fresh', 'help']);

if (isset($options['help'])) {
    echo "Использование: php bin/install.php --admin-password=СЕКРЕТ [--admin-login=admin] [--demo] [--fresh]\n";
    echo "  --demo   добавить демонстрационный каталог\n";
    echo "  --fresh  удалить существующие таблицы перед установкой\n";
    exit(0);
}

$login    = $options['admin-login'] ?? 'admin';
$password = $options['admin-password'] ?? null;

if ($password === null || strlen($password) < 8) {
    fwrite(STDERR, "Укажите пароль администратора не короче 8 символов: --admin-password=...\n");
    exit(1);
}

$db     = Database::instance();
$driver = $db->driver();

echo "База данных: {$driver}\n";

if (isset($options['fresh'])) {
    echo "Удаляю существующие таблицы…\n";
    if ($driver === 'mysql') {
        $db->run('SET FOREIGN_KEY_CHECKS = 0');
    }
    foreach (['payment_log', 'order_history', 'order_items', 'orders', 'products', 'categories', 'settings', 'users'] as $table) {
        $db->run('DROP TABLE IF EXISTS ' . $table);
    }
    if ($driver === 'mysql') {
        $db->run('SET FOREIGN_KEY_CHECKS = 1');
    }
}

// ── Схема ──────────────────────────────────────────────────────────────────
$schemaFile = APP_ROOT . '/database/schema.' . $driver . '.sql';
$sql        = file_get_contents($schemaFile);

foreach (array_filter(array_map('trim', explode(';', (string) $sql))) as $statement) {
    if (str_starts_with($statement, '--') && !str_contains($statement, 'CREATE')) {
        continue;
    }
    $db->run($statement);
}
echo "Таблицы созданы.\n";

// ── Администратор ──────────────────────────────────────────────────────────
if (User::findByLogin($login)) {
    User::updatePassword((int) User::findByLogin($login)['id'], $password);
    echo "Пароль администратора «{$login}» обновлён.\n";
} else {
    User::create($login, $password, 'Администратор');
    echo "Создан администратор «{$login}».\n";
}

// ── Настройки по умолчанию ────────────────────────────────────────────────
foreach (Setting::DEFAULTS as $key => $value) {
    if ($db->value('SELECT COUNT(*) FROM settings WHERE `key` = ?', [$key]) == 0) {
        Setting::set($key, (string) $value);
    }
}
echo "Настройки сайта заполнены значениями по умолчанию.\n";

// ── Демо-каталог ───────────────────────────────────────────────────────────
if (isset($options['demo'])) {
    if ((int) $db->value('SELECT COUNT(*) FROM products') > 0) {
        echo "Демо-данные пропущены: каталог уже не пуст.\n";
    } else {
        require APP_ROOT . '/database/demo.php';
        seed_demo_data();
        echo "Демонстрационный каталог загружен.\n";
    }
}

echo "\nГотово. Админка: " . rtrim((string) Config::get('app.url'), '/') . "/admin/login\n";

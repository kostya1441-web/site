<?php

/**
 * Веб-установщик «Ваш фермер» — для хостингов без доступа к консоли.
 *
 * Открывается в браузере: https://ваш-домен.ру/install.php
 * Шаги: проверка сервера → подключение к MySQL → создание администратора.
 *
 * ВАЖНО: после установки файл нужно удалить. Повторный запуск при уже
 * созданном администраторе блокируется.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Database;
use App\Models\Setting;
use App\Models\User;

$step       = (int) ($_GET['step'] ?? 1);
$errors     = [];
$manualCode = null;

$localConfigPath = APP_ROOT . '/config/config.local.php';

/** Есть ли в подключённой базе хотя бы один администратор. */
function has_admin(): bool
{
    try {
        $db = Database::instance();
        return $db->tableExists('users') && (int) $db->value('SELECT COUNT(*) FROM users') > 0;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Установка считается завершённой, когда настройки уже записаны И в базе есть
 * администратор. Тогда установщик блокируется — иначе им мог бы воспользоваться
 * посторонний. Одного лишь наличия базы мало: на свежем сервере файла настроек
 * ещё нет, и установку нужно разрешить.
 */
$installed = is_file($localConfigPath) && has_admin();

/** Угадываем адрес сайта, чтобы подставить в конфиг. */
function detect_base_url(): string
{
    $scheme = (($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

/** Проверки окружения для первого шага. */
function requirements(): array
{
    return [
        ['PHP 8.1 или новее', PHP_VERSION_ID >= 80100, 'Текущая версия: ' . PHP_VERSION],
        ['Расширение pdo_mysql', extension_loaded('pdo_mysql'), 'Работа с базой MySQL — обязательно'],
        ['Расширение mbstring', extension_loaded('mbstring'), 'Работа с кириллицей'],
        ['Расширение curl', extension_loaded('curl'), 'Запросы к эквайрингу Сбербанка'],
        ['Расширение gd', extension_loaded('gd'), 'Уменьшение загружаемых фотографий'],
        ['Папка config доступна для записи', is_writable(APP_ROOT . '/config'), APP_ROOT . '/config'],
        ['Папка storage доступна для записи', is_writable(APP_ROOT . '/storage'), APP_ROOT . '/storage'],
        ['Папка public/uploads доступна для записи', is_writable(APP_ROOT . '/public/uploads'), APP_ROOT . '/public/uploads'],
    ];
}

// ── Обработка форм ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
    if (!Csrf::check($_POST['_token'] ?? null)) {
        $errors[] = 'Сессия устарела, обновите страницу.';
    } elseif (($_POST['action'] ?? '') === 'database') {
        $host = trim($_POST['host'] ?? 'localhost');
        $port = trim($_POST['port'] ?? '3306') ?: '3306';
        $name = trim($_POST['database'] ?? '');
        $user = trim($_POST['username'] ?? '');
        $pass = (string) ($_POST['password'] ?? '');

        if ($name === '' || $user === '') {
            $errors[] = 'Укажите имя базы данных и пользователя.';
        } else {
            try {
                $probe = new PDO(
                    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name),
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                // Предупреждаем про кодировку заранее: с latin1 кириллица превратится в «?????»
                $charset = $probe->query('SELECT @@character_set_database')->fetchColumn();
                if (!str_starts_with((string) $charset, 'utf8')) {
                    $errors[] = 'Кодировка базы — ' . $charset . '. Нужна utf8mb4, иначе кириллица сохранится '
                        . 'неправильно. В phpMyAdmin: «Операции» → «Сравнение» → utf8mb4_unicode_ci.';
                }
            } catch (PDOException $e) {
                $errors[] = 'Не удалось подключиться: ' . $e->getMessage();
            }
        }

        if (!$errors) {
            $config = "<?php\n\nreturn [\n"
                . "    'app' => [\n"
                . "        'url'   => " . var_export(detect_base_url(), true) . ",\n"
                . "        'debug' => false,\n"
                . "    ],\n"
                . "    'db' => [\n"
                . "        'host'     => " . var_export($host, true) . ",\n"
                . "        'port'     => " . var_export($port, true) . ",\n"
                . "        'database' => " . var_export($name, true) . ",\n"
                . "        'username' => " . var_export($user, true) . ",\n"
                . "        'password' => " . var_export($pass, true) . ",\n"
                . "    ],\n"
                . "];\n";

            if (@file_put_contents($localConfigPath, $config) === false) {
                $errors[]   = 'Не удалось записать config/config.local.php — нет прав на запись.';
                $manualCode = $config;
            } else {
                @chmod($localConfigPath, 0640);
                header('Location: install.php?step=3');
                exit;
            }
        }
    } elseif (($_POST['action'] ?? '') === 'finish') {
        $login    = trim($_POST['login'] ?? 'admin');
        $password = (string) ($_POST['admin_password'] ?? '');
        $repeat   = (string) ($_POST['repeat_password'] ?? '');
        $demo     = isset($_POST['demo']);

        if ($login === '') {
            $errors[] = 'Укажите логин администратора.';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'Пароль администратора должен быть не короче 8 символов.';
        }
        if ($password !== $repeat) {
            $errors[] = 'Пароли не совпадают.';
        }

        if (!$errors) {
            try {
                $db = Database::instance();

                if (has_admin()) {
                    throw new RuntimeException(
                        'В этой базе уже есть администратор. Если это ваш магазин — войдите в админку '
                        . 'и удалите файл public/install.php. Для установки с нуля очистите базу данных.'
                    );
                }

                $sql = (string) file_get_contents(APP_ROOT . '/database/schema.sql');
                foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
                    if (str_starts_with($statement, '--') && !str_contains($statement, 'CREATE')) {
                        continue;
                    }
                    $db->run($statement);
                }

                User::create($login, $password, 'Администратор');

                foreach (Setting::DEFAULTS as $key => $value) {
                    if ((int) $db->value('SELECT COUNT(*) FROM settings WHERE `key` = ?', [$key]) === 0) {
                        Setting::set($key, (string) $value);
                    }
                }

                if ($demo && (int) $db->value('SELECT COUNT(*) FROM products') === 0) {
                    require APP_ROOT . '/database/demo.php';
                    seed_demo_data();
                }

                $step = 4;
            } catch (Throwable $e) {
                $errors[] = 'Ошибка установки: ' . $e->getMessage();
            }
        }
    }
}

$token = Csrf::token();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Установка «Ваш фермер»</title>
    <link rel="icon" href="<?= u('/assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= u('/assets/css/admin.css') ?>">
    <style>
        .install { max-width: 680px; margin: 0 auto; padding: 28px 16px 60px; }
        .install__head { text-align: center; margin-bottom: 24px; color: #fff; }
        .install__head span { font-size: 40px; }
        .install__head h1 { font-size: 24px; margin: 10px 0 4px; }
        .install__head p { color: rgba(255, 255, 255, .75); margin: 0; }
        .steps-bar { display: flex; gap: 8px; margin-bottom: 18px; }
        .steps-bar div {
            flex: 1; text-align: center; padding: 8px 4px; border-radius: 999px;
            background: rgba(255, 255, 255, .14); color: rgba(255, 255, 255, .75); font-size: 13px; font-weight: 600;
        }
        .steps-bar div.is-active { background: #e29a2d; color: #3a2400; }
        .steps-bar div.is-done { background: rgba(255, 255, 255, .3); color: #fff; }
        .req { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid var(--line); }
        .req:last-child { border-bottom: 0; }
        .req__mark { font-size: 18px; line-height: 1.2; }
        .req__text { flex: 1; }
        .req__text small { display: block; color: var(--ink-400); font-size: 12px; }
        .code { background: #10301f; color: #cfe3d6; padding: 14px; border-radius: 8px; font-size: 12px; overflow-x: auto; white-space: pre; }
        .done-box { text-align: center; }
        .done-box span { font-size: 46px; }
    </style>
</head>
<body class="admin admin--auth">
<div class="install">
    <div class="install__head">
        <span aria-hidden="true">🌾</span>
        <h1>Установка магазина «Ваш фермер»</h1>
        <p>Несколько шагов — и магазин готов к работе</p>
    </div>

    <?php if (!$installed && $step < 4): ?>
        <div class="steps-bar">
            <div class="<?= $step === 1 ? 'is-active' : 'is-done' ?>">1. Сервер</div>
            <div class="<?= $step === 2 ? 'is-active' : ($step > 2 ? 'is-done' : '') ?>">2. База данных</div>
            <div class="<?= $step === 3 ? 'is-active' : '' ?>">3. Администратор</div>
        </div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if ($installed): ?>
        <div class="panel">
            <h2 class="panel__title">Магазин уже установлен</h2>
            <p>В базе данных есть администратор, поэтому установщик заблокирован.</p>
            <p class="alert alert--error">
                <strong>Удалите файл <code>public/install.php</code> с сервера</strong> — иначе им сможет
                воспользоваться посторонний, если вы очистите базу.
            </p>
            <a class="btn btn--primary btn--block btn--lg" href="<?= u('/admin/login') ?>">Перейти в админку</a>
        </div>

    <?php elseif ($step === 1): ?>
        <div class="panel">
            <h2 class="panel__title">Проверка сервера</h2>
            <?php foreach (requirements() as [$label, $ok, $hint]): ?>
                <div class="req">
                    <span class="req__mark"><?= $ok ? '✅' : '⚠️' ?></span>
                    <span class="req__text"><?= e($label) ?><small><?= e($hint) ?></small></span>
                </div>
            <?php endforeach; ?>
            <p class="muted small" style="margin-top:14px">
                Пункты про папки решаются правами 775 на <code>storage</code>, <code>config</code>
                и <code>public/uploads</code> — их можно выставить в файловом менеджере хостинга.
            </p>
            <a class="btn btn--primary btn--block btn--lg" href="install.php?step=2">Продолжить</a>
        </div>

    <?php elseif ($step === 2): ?>
        <div class="panel">
            <h2 class="panel__title">Подключение к базе данных</h2>
            <p class="muted small">
                Базу нужно заранее создать в phpMyAdmin: вкладка «Базы данных» → имя базы →
                сравнение <code>utf8mb4_unicode_ci</code>. Таблицы установщик создаст сам.
            </p>
            <form method="post" action="install.php?step=2">
                <input type="hidden" name="_token" value="<?= e($token) ?>">
                <input type="hidden" name="action" value="database">

                <div class="form-grid">
                    <label class="field"><span>Сервер MySQL</span><input type="text" name="host" value="<?= e((string) ($_POST['host'] ?? 'localhost')) ?>"></label>
                    <label class="field"><span>Порт</span><input type="text" name="port" value="<?= e((string) ($_POST['port'] ?? '3306')) ?>"></label>
                    <label class="field"><span>Имя базы данных</span><input type="text" name="database" value="<?= e((string) ($_POST['database'] ?? '')) ?>" placeholder="u12345_fermer" required></label>
                    <label class="field"><span>Пользователь</span><input type="text" name="username" value="<?= e((string) ($_POST['username'] ?? '')) ?>" placeholder="u12345_fermer" required></label>
                    <label class="field field--full"><span>Пароль пользователя БД</span><input type="password" name="password"></label>
                </div>

                <button class="btn btn--primary btn--block btn--lg" type="submit">Проверить и сохранить</button>
            </form>

            <?php if ($manualCode !== null): ?>
                <div class="divider"></div>
                <p class="small"><strong>Нет прав на запись?</strong> Создайте файл <code>config/config.local.php</code>
                    вручную через файловый менеджер и вставьте в него:</p>
                <div class="code"><?= e($manualCode) ?></div>
                <a class="btn btn--ghost btn--block" href="install.php?step=3">Я создал файл, продолжить</a>
            <?php endif; ?>
        </div>

    <?php elseif ($step === 3): ?>
        <div class="panel">
            <h2 class="panel__title">Администратор магазина</h2>
            <p class="muted small">
                База: <strong><?= e((string) Config::get('db.database')) ?></strong> на
                <?= e((string) Config::get('db.host')) ?>. Сейчас будут созданы таблицы
                и учётная запись для входа в админку.
            </p>
            <form method="post" action="install.php?step=3">
                <input type="hidden" name="_token" value="<?= e($token) ?>">
                <input type="hidden" name="action" value="finish">
                <div class="form-grid">
                    <label class="field field--full"><span>Логин</span><input type="text" name="login" value="admin" required></label>
                    <label class="field"><span>Пароль (от 8 символов)</span><input type="password" name="admin_password" required minlength="8"></label>
                    <label class="field"><span>Повторите пароль</span><input type="password" name="repeat_password" required minlength="8"></label>
                </div>
                <label class="checkbox">
                    <input type="checkbox" name="demo" value="1" checked>
                    <span>Загрузить демонстрационный каталог (23 товара, 5 категорий) — потом можно удалить в админке</span>
                </label>
                <button class="btn btn--primary btn--block btn--lg" type="submit">Установить магазин</button>
            </form>
        </div>

    <?php else: ?>
        <div class="panel done-box">
            <span aria-hidden="true">🎉</span>
            <h2 class="panel__title">Магазин установлен</h2>
            <p>Теперь войдите в админку и заполните контакты в разделе «Настройки».</p>
            <div class="alert alert--error">
                <strong>Обязательно удалите файл <code>public/install.php</code></strong> с сервера —
                это единственный шаг, который нужно сделать вручную.
            </div>
            <a class="btn btn--primary btn--block btn--lg" href="<?= u('/admin/login') ?>">Войти в админку</a>
            <a class="btn btn--ghost btn--block" href="<?= u('/') ?>">Открыть сайт</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

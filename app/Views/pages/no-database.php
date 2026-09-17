<?php
/** Показывается, когда PHP не может подключиться к MySQL. */
$installerExists = is_file(__DIR__ . '/../../../public/install.php');
$configExists    = is_file(APP_ROOT . '/config/config.local.php');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Магазин не настроен — Ваш фермер</title>
    <link rel="stylesheet" href="<?= u('/assets/css/admin.css') ?>">
    <style>
        .nodb { max-width: 620px; margin: 0 auto; padding: 40px 16px; }
        .nodb__head { text-align: center; color: #fff; margin-bottom: 22px; }
        .nodb__head span { font-size: 42px; }
        .nodb__head h1 { font-size: 23px; margin: 10px 0 4px; }
        .nodb__head p { color: rgba(255, 255, 255, .75); margin: 0; }
        .nodb ol { padding-left: 20px; margin: 0 0 16px; }
        .nodb li { margin-bottom: 8px; color: var(--ink-600); }
    </style>
</head>
<body class="admin admin--auth">
<div class="nodb">
    <div class="nodb__head">
        <span aria-hidden="true">🌾</span>
        <h1>Магазин ещё не подключён к базе данных</h1>
        <p>Эту страницу видите только вы — покупателям сайт пока недоступен</p>
    </div>

    <div class="panel">
        <?php if (!$configExists): ?>
            <h2 class="panel__title">Нужно завершить установку</h2>
            <ol>
                <li>Создайте базу данных в phpMyAdmin со сравнением <code>utf8mb4_unicode_ci</code>.</li>
                <li>Заведите пользователя базы и выдайте ему права на неё.</li>
                <li>Откройте установщик и введите эти доступы.</li>
            </ol>
        <?php else: ?>
            <h2 class="panel__title">Не удалось подключиться к MySQL</h2>
            <ol>
                <li>Проверьте доступы в файле <code>config/config.local.php</code>: сервер, имя базы, пользователь, пароль.</li>
                <li>Убедитесь, что база существует и пользователь имеет к ней права.</li>
                <li>Если база на другом сервере — уточните хост у хостинг-провайдера (часто это не <code>localhost</code>).</li>
            </ol>
            <p class="muted small">Подробности ошибки записаны в <code>storage/logs/php-error.log</code>.</p>
        <?php endif; ?>

        <?php if ($installerExists): ?>
            <a class="btn btn--primary btn--block btn--lg" href="<?= u('/install.php') ?>">Открыть установщик</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

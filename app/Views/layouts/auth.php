<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Вход') ?></title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin admin--auth">
<div class="auth-wrap">
    <?php \App\Core\View::partial('partials/flash'); ?>
    <?= $content ?>
</div>
</body>
</html>

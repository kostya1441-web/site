<?php
/** @var string $content */
$settings = $settings ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title ?? $settings['site_title'] ?? 'Ваш фермер') ?></title>
    <meta name="description" content="<?= e($description ?? ($settings['site_description'] ?? '')) ?>">
    <meta name="theme-color" content="#1f5c3d">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($title ?? 'Ваш фермер') ?>">
    <meta property="og:description" content="<?= e($description ?? ($settings['site_description'] ?? '')) ?>">
    <meta property="og:site_name" content="Ваш фермер">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<a class="skip-link" href="#main">Перейти к содержимому</a>

<?php \App\Core\View::partial('partials/header'); ?>

<main id="main"><?= $content ?></main>

<?php \App\Core\View::partial('partials/footer'); ?>
<?php \App\Core\View::partial('partials/toast'); ?>

<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>

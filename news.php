<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = 'Новости — ' . SITE_NAME;
$activePage = 'news';

// В продакшне — из БД. Здесь статичный массив.
$news_items = [
    [
        'date'     => '2025-05-01',
        'tag'      => 'Обновление',
        'tag_color'=> '#3b82f6',
        'title'    => 'Обновление плагинов до новейших версий',
        'preview'  => 'Обновили LvlRanks до последней версии, исправлены баги с очками при дисконнекте.',
        'content'  => 'Мы обновили LvlRanks до версии 2.7.5. Исправлен баг, при котором очки не засчитывались при дисконнекте в конце раунда. Также обновили плагин скинов — теперь работает корректно с CS2 патчем 1.39.',
    ],
    [
        'date'     => '2025-04-20',
        'tag'      => 'Карты',
        'tag_color'=> '#10b981',
        'title'    => 'Новая карта на сервере Dust2 Only',
        'preview'  => 'По просьбам игроков добавлена ротация de_anubis. Голосование за карту теперь доступно через меню.',
        'content'  => 'По просьбам большинства игроков сервера Dust2 Only мы добавили в ротацию карту de_anubis. Голосование за следующую карту доступно в конце каждого раунда через меню !rtv. Enjoy!',
    ],
    [
        'date'     => '2025-04-10',
        'tag'      => 'Акция',
        'tag_color'=> '#f59e0b',
        'title'    => 'Акция на привилегии -30%',
        'preview'  => 'До конца апреля все привилегии со скидкой 30%. Успей купить!',
        'content'  => 'Весенняя акция! До 30 апреля все привилегии (VIP, Premium, Elite) продаются со скидкой 30%. Идеальный момент чтобы поддержать сервер и получить преимущества. Скидка применяется автоматически.',
    ],
    [
        'date'     => '2025-03-15',
        'tag'      => 'Сервер',
        'tag_color'=> '#8b5cf6',
        'title'    => 'Открытие третьего сервера — Competitive Mix',
        'preview'  => 'Запущен новый сервер с ротацией соревновательных карт. 128-тик, честный рейтинг.',
        'content'  => 'Долгожданное открытие! Третий сервер Competitive Mix теперь доступен. 128-тик, ротация классических карт (Mirage, Inferno, Nuke, Ancient, Vertigo), полная интеграция с LvlRanks. Подключайтесь!',
    ],
    [
        'date'     => '2025-03-01',
        'tag'      => 'Система',
        'tag_color'=> '#ef4444',
        'title'    => 'Запуск нового сайта и системы рейтинга',
        'preview'  => 'Мы полностью переработали сайт. Теперь есть таблица рейтинга, магазин и страница правил.',
        'content'  => 'Добро пожаловать на обновлённый сайт! Теперь здесь есть: таблица рейтинга с поиском, магазин привилегий с онлайн-оплатой, страница правил и этот раздел новостей. Следи за обновлениями!',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1 class="page-title">Новости сервера</h1>
        <p class="page-sub">Следи за обновлениями, акциями и событиями на сервере.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="news-full-grid">
            <?php foreach ($news_items as $n): ?>
            <article class="news-full-card">
                <div class="news-full-meta">
                    <span class="news-tag" style="background: <?= $n['tag_color'] ?>20; color: <?= $n['tag_color'] ?>; border-color: <?= $n['tag_color'] ?>40"><?= h($n['tag']) ?></span>
                    <time class="news-date"><?= date('d F Y', strtotime($n['date'])) ?></time>
                </div>
                <h2 class="news-full-title"><?= h($n['title']) ?></h2>
                <p class="news-full-content"><?= h($n['content']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

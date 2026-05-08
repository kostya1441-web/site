<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle  = 'Новости — ' . SITE_NAME;
$activePage = 'news';

require_once __DIR__ . '/includes/db.php';
$db = getDB();
$news_items = [];
if ($db) {
    $news_items = $db->query('SELECT * FROM news WHERE published=1 ORDER BY created_at DESC')->fetchAll();
}
// Fallback static articles when DB is not set up
if (!$news_items) {
    $news_items = [
        ['tag'=>'Событие','tag_color'=>'#22c55e','title'=>'Открытие сервера!','content'=>'Рады сообщить об открытии нашего CS2 сервера.','created_at'=>date('Y-m-d H:i:s')],
        ['tag'=>'Обновление','tag_color'=>'#3b82f6','title'=>'Обновление плагинов','content'=>'Обновлены LvlRanks и другие плагины.','created_at'=>date('Y-m-d H:i:s', strtotime('-7 days'))],
    ];
}

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
                    <span class="news-tag" style="background: <?= h($n['tag_color']) ?>20; color: <?= h($n['tag_color']) ?>; border-color: <?= h($n['tag_color']) ?>40"><?= h($n['tag']) ?></span>
                    <time class="news-date"><?= date('d.m.Y', strtotime($n['created_at'])) ?></time>
                </div>
                <h2 class="news-full-title"><?= h($n['title']) ?></h2>
                <p class="news-full-content"><?= h($n['content']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

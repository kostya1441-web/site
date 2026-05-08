<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = SITE_NAME . ' — CS2 Сервер';
$activePage = 'home';

// Топ-5 игроков
$top_players = [];
$db = getDB();
if ($db) {
    $stmt = $db->query("SELECT steam, name, kills, deaths, skill, rank, playtime
                         FROM " . LVLRANKS_TABLE . "
                         ORDER BY skill DESC LIMIT 5");
    $top_players = $stmt ? $stmt->fetchAll() : [];
}

// Последние 3 новости (из БД или статично)
$news = [
    ['date' => '2025-05-01', 'title' => 'Обновление плагинов',      'text' => 'Обновили LvlRanks до последней версии, исправлены баги с очками.'],
    ['date' => '2025-04-20', 'title' => 'Новая карта на сервере',    'text' => 'На Dust2 Only добавлена ротация de_anubis по просьбам игроков.'],
    ['date' => '2025-04-10', 'title' => 'Акция на привилегии -30%',  'text' => 'До конца апреля все привилегии со скидкой 30%!'],
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <div class="hero-text">
            <h1 class="hero-title">Лучший CS2 сервер<br>— <span class="accent"><?= h(SITE_NAME) ?></span></h1>
            <p class="hero-sub">Играй, прокачивайся, доминируй. Справедливый античит, топовые плагины и дружное комьюнити.</p>
            <div class="hero-btns">
                <a href="steam://connect/<?= h(SERVERS[0]['ip']) ?>:<?= SERVERS[0]['port'] ?>" class="btn btn-primary btn-lg">
                    Играть сейчас
                </a>
                <a href="/rating.php" class="btn btn-outline btn-lg">Рейтинг игроков</a>
            </div>
        </div>
        <div class="hero-stats">
            <?php foreach (SERVERS as $idx => $srv): ?>
            <div class="server-card" id="srv-<?= $idx ?>">
                <div class="server-card-header">
                    <span class="server-status-dot"></span>
                    <span class="server-name"><?= h($srv['name']) ?></span>
                </div>
                <div class="server-info">
                    <span class="server-ip"><?= h($srv['ip']) ?>:<?= $srv['port'] ?></span>
                    <span class="server-players" data-ip="<?= h($srv['ip']) ?>" data-port="<?= $srv['port'] ?>">
                        <span class="players-count">—</span> / <span class="players-max">—</span>
                    </span>
                </div>
                <div class="server-map" data-ip="<?= h($srv['ip']) ?>" data-port="<?= $srv['port'] ?>">Загрузка...</div>
                <a href="steam://connect/<?= h($srv['ip']) ?>:<?= $srv['port'] ?>" class="btn btn-sm btn-primary">Подключиться</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section features-section">
    <div class="container">
        <h2 class="section-title">Почему выбирают нас</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Античит</h3>
                <p>Защита на базе VAC + собственный детектор. Баним читеров в течение нескольких минут.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Система рейтинга</h3>
                <p>LvlRanks — честный рейтинг с 18 уровнями. Прокачивай скилл и поднимайся в топ.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3>Скины оружий</h3>
                <p>Сотни скинов для всех оружий. Для VIP-игроков доступны редкие коллекции.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Низкий пинг</h3>
                <p>Серверы расположены в Москве. Стабильный пинг до 15мс для большинства городов РФ.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3>Турниры</h3>
                <p>Еженедельные турниры с призовым фондом. Участие бесплатное для всех игроков.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Комьюнити</h3>
                <p>Активный Discord и Telegram. Поддержка 24/7, ответим на любой вопрос.</p>
            </div>
        </div>
    </div>
</section>

<!-- TOP PLAYERS -->
<?php if (!empty($top_players)): ?>
<section class="section top-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Топ игроков</h2>
            <a href="/rating.php" class="btn btn-outline">Полный рейтинг →</a>
        </div>
        <div class="top-table-wrap">
            <table class="top-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Игрок</th>
                        <th>Ранг</th>
                        <th>Очки</th>
                        <th>K / D</th>
                        <th>Время</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($top_players as $i => $p): ?>
                    <?php
                    $sid64 = steamid_to_steamid64($p['steam']);
                    $avatar = get_steam_avatar($sid64);
                    $rlabel = rank_label((int)$p['rank']);
                    $rcolor = rank_color((int)$p['rank']);
                    ?>
                    <tr class="<?= $i === 0 ? 'top-1' : ($i === 1 ? 'top-2' : ($i === 2 ? 'top-3' : '')) ?>">
                        <td class="rank-num"><?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)) ?></td>
                        <td class="player-cell">
                            <img src="<?= h($avatar) ?>" alt="" class="player-avatar">
                            <span><?= h($p['name']) ?></span>
                        </td>
                        <td><span class="rank-badge" style="color:<?= $rcolor ?>"><?= h($rlabel) ?></span></td>
                        <td class="skill-val"><?= number_format((int)$p['skill']) ?></td>
                        <td><?= kd_ratio((int)$p['kills'], (int)$p['deaths']) ?></td>
                        <td><?= time_played((int)$p['playtime']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- DONATE PROMO -->
<section class="section donate-promo">
    <div class="container">
        <div class="donate-promo-inner">
            <div class="donate-promo-text">
                <h2>Поддержи сервер и получи привилегии</h2>
                <p>VIP, Premium и Elite статусы с уникальными возможностями. Скины, теги, слоты и многое другое.</p>
                <a href="/donate.php" class="btn btn-primary btn-lg">Смотреть привилегии</a>
            </div>
            <div class="donate-promo-cards">
                <?php foreach (DONATE_PACKAGES as $pkg): ?>
                <div class="promo-pkg" style="border-color: <?= $pkg['color'] ?>">
                    <span class="promo-pkg-name" style="color: <?= $pkg['color'] ?>"><?= h($pkg['name']) ?></span>
                    <span class="promo-pkg-price"><?= $pkg['price'] ?> ₽</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- NEWS -->
<section class="section news-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Последние новости</h2>
            <a href="/news.php" class="btn btn-outline">Все новости →</a>
        </div>
        <div class="news-grid">
            <?php foreach ($news as $n): ?>
            <article class="news-card">
                <time class="news-date"><?= date('d.m.Y', strtotime($n['date'])) ?></time>
                <h3 class="news-title"><?= h($n['title']) ?></h3>
                <p class="news-text"><?= h($n['text']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

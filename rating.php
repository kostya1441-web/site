<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle  = 'Рейтинг игроков — ' . SITE_NAME;
$activePage = 'rating';

$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 25;
$offset   = ($page - 1) * $per_page;
$search   = trim($_GET['q'] ?? '');

$db      = getDB();
$players = [];
$total   = 0;

if ($db) {
    $where = $search ? "WHERE name LIKE :q" : '';
    $params = $search ? [':q' => '%' . $search . '%'] : [];

    $cnt   = $db->prepare("SELECT COUNT(*) FROM " . LVLRANKS_TABLE . " $where");
    $cnt->execute($params);
    $total = (int)$cnt->fetchColumn();

    $stmt = $db->prepare("SELECT steam, name, kills, deaths, headshots, playtime, rank, skill
                           FROM " . LVLRANKS_TABLE . " $where
                           ORDER BY skill DESC LIMIT $per_page OFFSET $offset");
    $stmt->execute($params);
    $players = $stmt->fetchAll();
}

$pages = $total ? (int)ceil($total / $per_page) : 1;

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1 class="page-title">Рейтинг игроков</h1>
        <p class="page-sub">Топ лучших игроков по очкам LvlRanks. Всего в базе: <strong><?= number_format($total) ?></strong> игроков.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Поиск -->
        <form class="search-form" method="get" action="/rating.php">
            <input type="text" name="q" value="<?= h($search) ?>" placeholder="Поиск по нику..." class="search-input">
            <button type="submit" class="btn btn-primary">Найти</button>
            <?php if ($search): ?>
            <a href="/rating.php" class="btn btn-outline">Сбросить</a>
            <?php endif; ?>
        </form>

        <?php if (!$db): ?>
        <div class="alert alert-warning">База данных недоступна. Проверьте настройки в config.php.</div>
        <?php elseif (empty($players)): ?>
        <div class="alert alert-info">Игроки не найдены.</div>
        <?php else: ?>

        <div class="top-table-wrap">
            <table class="top-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Игрок</th>
                        <th>Ранг</th>
                        <th>Очки</th>
                        <th>Убийства</th>
                        <th>Смерти</th>
                        <th>K/D</th>
                        <th>HS%</th>
                        <th>Время</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($players as $i => $p):
                    $pos = $offset + $i + 1;
                    $sid64  = steamid_to_steamid64($p['steam']);
                    $avatar = get_steam_avatar($sid64);
                    $hs_pct = $p['kills'] > 0 ? round($p['headshots'] / $p['kills'] * 100) : 0;
                ?>
                    <tr class="<?= $pos <= 3 ? 'top-' . $pos : '' ?>">
                        <td class="rank-num">
                            <?php if ($pos === 1): ?>🥇<?php elseif ($pos === 2): ?>🥈<?php elseif ($pos === 3): ?>🥉<?php else: ?><?= $pos ?><?php endif; ?>
                        </td>
                        <td class="player-cell">
                            <img src="<?= h($avatar) ?>" alt="" class="player-avatar">
                            <?php if ($sid64): ?>
                            <a href="https://steamcommunity.com/profiles/<?= $sid64 ?>" target="_blank" rel="noopener"><?= h($p['name']) ?></a>
                            <?php else: ?>
                            <span><?= h($p['name']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="rank-badge" style="color:<?= rank_color((int)$p['rank']) ?>">
                                <?= h(rank_label((int)$p['rank'])) ?>
                            </span>
                        </td>
                        <td class="skill-val"><?= number_format((int)$p['skill']) ?></td>
                        <td><?= number_format((int)$p['kills']) ?></td>
                        <td><?= number_format((int)$p['deaths']) ?></td>
                        <td><?= kd_ratio((int)$p['kills'], (int)$p['deaths']) ?></td>
                        <td><?= $hs_pct ?>%</td>
                        <td><?= time_played((int)$p['playtime']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        <?php if ($pages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?= $search ? '&q=' . urlencode($search) : '' ?>" class="page-btn">← Назад</a>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end   = min($pages, $page + 2);
            if ($start > 1) echo '<span class="page-dots">...</span>';
            for ($j = $start; $j <= $end; $j++):
            ?>
            <a href="?page=<?= $j ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
               class="page-btn <?= $j === $page ? 'active' : '' ?>"><?= $j ?></a>
            <?php endfor;
            if ($end < $pages) echo '<span class="page-dots">...</span>';
            ?>

            <?php if ($page < $pages): ?>
            <a href="?page=<?= $page + 1 ?><?= $search ? '&q=' . urlencode($search) : '' ?>" class="page-btn">Вперёд →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<!-- Описание рангов -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Система рангов</h2>
        <div class="ranks-grid">
            <?php
            $all_ranks = [];
            for ($r = 1; $r <= 18; $r++) {
                $all_ranks[] = ['rank' => $r, 'label' => rank_label($r), 'color' => rank_color($r)];
            }
            foreach ($all_ranks as $rk):
            ?>
            <div class="rank-item">
                <span class="rank-dot" style="background:<?= $rk['color'] ?>"></span>
                <span class="rank-num-sm"><?= $rk['rank'] ?></span>
                <span class="rank-name" style="color:<?= $rk['color'] ?>"><?= h($rk['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

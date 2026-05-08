<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();
$steamid64 = $user['steamid64'];
$steamid   = steamid64_to_steamid($steamid64);

$pageTitle  = h($user['name']) . ' — Профиль';
$activePage = '';

$db = getDB();

// LvlRanks stats
$lvl = null;
if ($db) {
    $stmt = $db->prepare('SELECT * FROM ' . LVLRANKS_TABLE . ' WHERE steam = ? LIMIT 1');
    $stmt->execute([$steamid]);
    $lvl = $stmt->fetch();
}

// Ban / mute status
$ban  = getUserBanStatus($steamid);
$mute = getUserMuteStatus($steamid);

// Rank leaderboard position
$position = null;
if ($db && $lvl) {
    $stmt = $db->prepare('SELECT COUNT(*)+1 as pos FROM ' . LVLRANKS_TABLE . ' WHERE skill > ?');
    $stmt->execute([$lvl['skill']]);
    $position = (int)$stmt->fetchColumn();
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="page-sub"><a href="/">Главная</a> / Профиль</p>
        <h1 class="page-title"><?= h($user['name']) ?></h1>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="profile-header">
            <div class="profile-avatar-wrap">
                <img src="<?= h($user['avatar']) ?>" alt="<?= h($user['name']) ?>" class="profile-avatar">
                <?php if ($lvl): ?>
                <span class="profile-avatar-rank" style="color:<?= rank_color((int)$lvl['rank']) ?>">
                    <?= h(rank_label((int)$lvl['rank'])) ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?= h($user['name']) ?></h2>
                <p class="profile-steamid">SteamID: <?= h($steamid) ?> &nbsp;|&nbsp; SteamID64: <?= h($steamid64) ?></p>
                <div class="profile-badges">
                    <?php if ($user['role'] === 'superadmin'): ?>
                        <span class="profile-badge profile-badge--admin">👑 Владелец</span>
                    <?php elseif ($user['role'] === 'admin'): ?>
                        <span class="profile-badge profile-badge--admin">🛡 Администратор</span>
                    <?php elseif ($user['role'] === 'vip'): ?>
                        <span class="profile-badge profile-badge--vip">⭐ VIP</span>
                    <?php endif; ?>
                    <?php if ($ban): ?>
                        <span class="profile-badge profile-badge--banned">🔨 В бане</span>
                    <?php endif; ?>
                    <?php if ($mute): ?>
                        <span class="profile-badge profile-badge--muted">🔇 В муте</span>
                    <?php endif; ?>
                </div>
                <?php if ($position): ?>
                <p style="font-size:14px;color:var(--text-muted)">Место в рейтинге: <strong style="color:var(--accent)">#<?= $position ?></strong></p>
                <?php endif; ?>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-self:flex-start;margin-left:auto">
                <a href="/rating.php" class="btn btn-ghost btn-sm">Рейтинг</a>
                <?php if (isAdmin()): ?>
                <a href="/admin/" class="btn btn-primary btn-sm">Админ-панель</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($ban): ?>
        <div class="alert alert-error" style="margin-bottom:24px">
            <strong>Вы заблокированы.</strong> Причина: <?= h($ban['reason']) ?>.
            Выдан: <?= h($ban['admin_name']) ?>.
            Срок: <?= $ban['duration'] == 0 ? 'Навсегда' : h(formatDuration((int)$ban['duration'])) ?>.
        </div>
        <?php endif; ?>

        <?php if ($mute): ?>
        <div class="alert alert-warning" style="margin-bottom:24px">
            <strong>Вы заглушены.</strong> Причина: <?= h($mute['reason']) ?>.
            Тип: <?= h($mute['type']) ?>.
            Срок: <?= $mute['duration'] == 0 ? 'Навсегда' : h(formatDuration((int)$mute['duration'])) ?>.
        </div>
        <?php endif; ?>

        <?php if ($lvl): ?>
        <h3 class="section-title" style="margin-bottom:24px">Статистика</h3>
        <div class="stats-grid" style="margin-bottom:40px">
            <div class="stat-card">
                <span class="stat-val"><?= number_format((int)$lvl['skill']) ?></span>
                <span class="stat-label">Очки</span>
            </div>
            <div class="stat-card">
                <span class="stat-val"><?= number_format((int)$lvl['kills']) ?></span>
                <span class="stat-label">Убийств</span>
            </div>
            <div class="stat-card">
                <span class="stat-val"><?= number_format((int)$lvl['deaths']) ?></span>
                <span class="stat-label">Смертей</span>
            </div>
            <div class="stat-card">
                <span class="stat-val"><?= kd_ratio((int)$lvl['kills'], (int)$lvl['deaths']) ?></span>
                <span class="stat-label">K/D</span>
            </div>
            <div class="stat-card">
                <?php $hs_pct = $lvl['kills'] > 0 ? round($lvl['headshots'] / $lvl['kills'] * 100) : 0; ?>
                <span class="stat-val"><?= $hs_pct ?>%</span>
                <span class="stat-label">Хедшот %</span>
            </div>
            <div class="stat-card">
                <span class="stat-val"><?= time_played((int)$lvl['playtime']) ?></span>
                <span class="stat-label">Время игры</span>
            </div>
        </div>

        <h3 class="section-title" style="margin-bottom:24px">Ранг</h3>
        <div style="display:flex;align-items:center;gap:20px;padding:24px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius)">
            <div style="font-size:2.5rem">🏆</div>
            <div>
                <div style="font-size:1.4rem;font-weight:800;color:<?= rank_color((int)$lvl['rank']) ?>"><?= h(rank_label((int)$lvl['rank'])) ?></div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:4px">Ранг <?= (int)$lvl['rank'] ?> из 18</div>
            </div>
            <?php if ($lvl['rank'] < 18): ?>
            <div style="margin-left:auto;text-align:right">
                <div style="font-size:12px;color:var(--text-muted)">Следующий ранг</div>
                <div style="font-size:1rem;font-weight:700;color:<?= rank_color((int)$lvl['rank'] + 1) ?>"><?= h(rank_label((int)$lvl['rank'] + 1)) ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🎮</div>
            <p class="empty-state-text">Статистика не найдена. Сыграйте на сервере, чтобы данные появились.</p>
            <?php foreach (SERVERS as $s): ?>
            <a href="steam://connect/<?= h($s['ip']) ?>:<?= $s['port'] ?>" class="btn btn-primary" style="margin-top:16px">
                Подключиться к <?= h($s['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

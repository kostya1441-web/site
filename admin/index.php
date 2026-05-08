<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Дашборд';

$db = getDB();
$stats = ['players' => 0, 'bans' => 0, 'mutes' => 0, 'users' => 0];
$recent_bans = [];
if ($db) {
    $stats['players'] = (int)$db->query('SELECT COUNT(*) FROM ' . LVLRANKS_TABLE)->fetchColumn();
    $stats['bans']    = (int)$db->query('SELECT COUNT(*) FROM bans WHERE active=1')->fetchColumn();
    $stats['mutes']   = (int)$db->query('SELECT COUNT(*) FROM mutes WHERE active=1')->fetchColumn();
    $stats['users']   = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $recent_bans = $db->query('SELECT * FROM bans ORDER BY created_at DESC LIMIT 10')->fetchAll();
}

include __DIR__ . '/includes/layout_top.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Дашборд</h1>
    <p class="admin-sub">Обзор активности сервера</p>
</div>

<div class="admin-stats-grid">
    <div class="admin-stat-card accent-card">
        <span class="val"><?= number_format($stats['players']) ?></span>
        <span class="label">Игроков в базе</span>
    </div>
    <div class="admin-stat-card blue-card">
        <span class="val"><?= number_format($stats['users']) ?></span>
        <span class="label">Пользователей сайта</span>
    </div>
    <div class="admin-stat-card red-card">
        <span class="val"><?= number_format($stats['bans']) ?></span>
        <span class="label">Активных банов</span>
    </div>
    <div class="admin-stat-card">
        <span class="val"><?= number_format($stats['mutes']) ?></span>
        <span class="label">Активных мутов</span>
    </div>
</div>

<h2 style="font-size:1.1rem;font-weight:700;margin-bottom:16px">Последние баны</h2>
<?php if ($recent_bans): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Игрок</th><th>Причина</th><th>Администратор</th><th>Срок</th><th>Дата</th><th>Статус</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recent_bans as $b): ?>
            <tr>
                <td><?= h($b['name'] ?: $b['steamid']) ?></td>
                <td class="ban-reason"><?= h($b['reason']) ?></td>
                <td><?= h($b['admin_name']) ?></td>
                <td><?= $b['duration'] == 0 ? '<span class="duration-perm">Навсегда</span>' : h(formatDuration((int)$b['duration'])) ?></td>
                <td style="color:var(--text-muted);font-size:12px"><?= h(date('d.m.Y H:i', strtotime($b['created_at']))) ?></td>
                <td><?= $b['active'] ? '<span class="badge badge-red">Активен</span>' : '<span class="badge badge-gray">Истёк</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state"><div class="empty-state-icon">✅</div><p class="empty-state-text">Нет банов</p></div>
<?php endif; ?>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

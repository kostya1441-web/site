<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Баны';
$db = getDB();
$msg = '';

// Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $steamid  = trim($_POST['steamid'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $reason   = trim($_POST['reason'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $admin    = getCurrentUser();

        if ($steamid && $reason) {
            $steamid64 = steamid_to_steamid64($steamid) ?: $steamid;
            $expires   = $duration > 0 ? date('Y-m-d H:i:s', time() + $duration * 60) : null;
            $db->prepare('INSERT INTO bans (steamid, steamid64, name, admin_steamid, admin_name, reason, duration, expires_at)
                          VALUES (?,?,?,?,?,?,?,?)')
               ->execute([$steamid, $steamid64, $name, steamid64_to_steamid($admin['steamid64']), $admin['name'], $reason, $duration, $expires]);
            $msg = 'success:Бан добавлен.';
        } else {
            $msg = 'error:Заполните SteamID и причину.';
        }
    } elseif ($action === 'unban') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('UPDATE bans SET active=0 WHERE id=?')->execute([$id]);
        $msg = 'success:Бан снят.';
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM bans WHERE id=?')->execute([$id]);
        $msg = 'success:Запись удалена.';
    }
}

$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 25;
$offset = ($page - 1) * $limit;
$bans   = [];
$total  = 0;

if ($db) {
    $where = $search ? 'WHERE (steamid LIKE ? OR name LIKE ? OR reason LIKE ?)' : '';
    $args  = $search ? ["%$search%", "%$search%", "%$search%"] : [];
    $total = (int)$db->prepare("SELECT COUNT(*) FROM bans $where")->execute($args) ? $db->prepare("SELECT COUNT(*) FROM bans $where")->execute($args) : 0;
    $stmt  = $db->prepare("SELECT * FROM bans $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $stmt->execute($args);
    $bans  = $stmt->fetchAll();
    $cnt   = $db->prepare("SELECT COUNT(*) FROM bans $where");
    $cnt->execute($args);
    $total = (int)$cnt->fetchColumn();
}
$pages = $total > 0 ? ceil($total / $limit) : 1;

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Баны</h1>
    <p class="admin-sub">Управление банами игроков</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<!-- Add ban -->
<details style="margin-bottom:24px">
    <summary style="cursor:pointer;font-weight:700;padding:14px 20px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius-sm);list-style:none;display:flex;justify-content:space-between">
        ➕ Добавить бан <span style="color:var(--accent)">+</span>
    </summary>
    <div style="padding:20px;background:var(--bg2);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius-sm) var(--radius-sm)">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="add">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>SteamID (STEAM_0:X:XXXXX) *</label>
                    <input class="form-control" name="steamid" placeholder="STEAM_0:1:12345678" required>
                </div>
                <div class="form-group">
                    <label>Ник игрока</label>
                    <input class="form-control" name="name" placeholder="Необязательно">
                </div>
            </div>
            <div class="form-group">
                <label>Причина *</label>
                <input class="form-control" name="reason" placeholder="Читы, оскорбления, ..." required>
            </div>
            <div class="form-group">
                <label>Срок (минуты, 0 = навсегда)</label>
                <input class="form-control" name="duration" type="number" min="0" value="0">
                <small style="color:var(--text-muted);font-size:12px">60 = 1ч, 1440 = 1д, 10080 = 1н, 0 = навсегда</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">🔨 Забанить</button>
            </div>
        </form>
    </div>
</details>

<!-- Filter -->
<form method="GET" class="admin-filter-bar">
    <input class="search-input" name="q" value="<?= h($search) ?>" placeholder="Поиск по SteamID, нику, причине...">
    <button class="btn btn-ghost btn-sm" type="submit">Найти</button>
    <?php if ($search): ?><a href="/admin/bans.php" class="btn btn-ghost btn-sm">Сбросить</a><?php endif; ?>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th><th>Игрок</th><th>SteamID</th><th>Причина</th>
                <th>Администратор</th><th>Срок</th><th>Дата</th><th>Статус</th><th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($bans): foreach ($bans as $b): ?>
            <tr>
                <td style="color:var(--text-muted)"><?= $b['id'] ?></td>
                <td><?= h($b['name'] ?: '—') ?></td>
                <td><code style="font-size:12px"><?= h($b['steamid']) ?></code></td>
                <td class="ban-reason" title="<?= h($b['reason']) ?>"><?= h($b['reason']) ?></td>
                <td><?= h($b['admin_name']) ?></td>
                <td><?= $b['duration'] == 0 ? '<span class="duration-perm">Навсегда</span>' : h(formatDuration((int)$b['duration'])) ?></td>
                <td style="color:var(--text-muted);font-size:12px"><?= h(date('d.m.Y H:i', strtotime($b['created_at']))) ?></td>
                <td>
                    <?php
                    $isActive = $b['active'] && ($b['duration'] == 0 || strtotime($b['expires_at']) > time());
                    echo $isActive ? '<span class="badge badge-red">Активен</span>' : '<span class="badge badge-gray">Истёк</span>';
                    ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <?php if ($isActive): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Снять бан?')">
                            <input type="hidden" name="action" value="unban">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <button class="btn btn-success btn-xs" type="submit">Снять</button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Удалить запись?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <button class="btn btn-danger btn-xs" type="submit">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="9"><div class="empty-state"><div class="empty-state-icon">✅</div><p class="empty-state-text">Банов нет</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pages > 1): ?>
<div class="pagination" style="margin-top:20px">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
    <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

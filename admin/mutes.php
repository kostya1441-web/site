<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Муты';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $steamid  = trim($_POST['steamid'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $reason   = trim($_POST['reason'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $type     = $_POST['type'] ?? 'mute';
        $admin    = getCurrentUser();
        if ($steamid && $reason) {
            $steamid64 = steamid_to_steamid64($steamid) ?: $steamid;
            $expires   = $duration > 0 ? date('Y-m-d H:i:s', time() + $duration * 60) : null;
            $db->prepare('INSERT INTO mutes (steamid, steamid64, name, admin_steamid, admin_name, reason, duration, expires_at, type)
                          VALUES (?,?,?,?,?,?,?,?,?)')
               ->execute([$steamid, $steamid64, $name, steamid64_to_steamid($admin['steamid64']), $admin['name'], $reason, $duration, $expires, $type]);
            $msg = 'success:Мут добавлен.';
        } else { $msg = 'error:Заполните SteamID и причину.'; }
    } elseif ($action === 'unmute') {
        $db->prepare('UPDATE mutes SET active=0 WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'success:Мут снят.';
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM mutes WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'success:Запись удалена.';
    }
}

$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 25; $offset = ($page - 1) * $limit;
$mutes  = []; $total = 0;
if ($db) {
    $where = $search ? 'WHERE (steamid LIKE ? OR name LIKE ? OR reason LIKE ?)' : '';
    $args  = $search ? ["%$search%", "%$search%", "%$search%"] : [];
    $cnt   = $db->prepare("SELECT COUNT(*) FROM mutes $where"); $cnt->execute($args);
    $total = (int)$cnt->fetchColumn();
    $stmt  = $db->prepare("SELECT * FROM mutes $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $stmt->execute($args); $mutes = $stmt->fetchAll();
}
$pages = $total > 0 ? ceil($total / $limit) : 1;

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Муты</h1>
    <p class="admin-sub">Управление мутами игроков</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<details style="margin-bottom:24px">
    <summary style="cursor:pointer;font-weight:700;padding:14px 20px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius-sm);list-style:none;display:flex;justify-content:space-between">
        ➕ Добавить мут <span style="color:var(--accent)">+</span>
    </summary>
    <div style="padding:20px;background:var(--bg2);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius-sm) var(--radius-sm)">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="add">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>SteamID *</label>
                    <input class="form-control" name="steamid" placeholder="STEAM_0:1:12345678" required>
                </div>
                <div class="form-group">
                    <label>Ник</label>
                    <input class="form-control" name="name" placeholder="Необязательно">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>Причина *</label>
                    <input class="form-control" name="reason" placeholder="Спам, оскорбления..." required>
                </div>
                <div class="form-group">
                    <label>Тип</label>
                    <select class="form-control" name="type">
                        <option value="mute">Mute (голос)</option>
                        <option value="gag">Gag (чат)</option>
                        <option value="silence">Silence (оба)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Срок (минуты, 0 = навсегда)</label>
                <input class="form-control" name="duration" type="number" min="0" value="0">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">🔇 Замутить</button>
            </div>
        </form>
    </div>
</details>

<form method="GET" class="admin-filter-bar">
    <input class="search-input" name="q" value="<?= h($search) ?>" placeholder="Поиск...">
    <button class="btn btn-ghost btn-sm" type="submit">Найти</button>
    <?php if ($search): ?><a href="/admin/mutes.php" class="btn btn-ghost btn-sm">Сбросить</a><?php endif; ?>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>#</th><th>Игрок</th><th>SteamID</th><th>Тип</th><th>Причина</th><th>Срок</th><th>Дата</th><th>Статус</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php if ($mutes): foreach ($mutes as $m): ?>
            <tr>
                <td style="color:var(--text-muted)"><?= $m['id'] ?></td>
                <td><?= h($m['name'] ?: '—') ?></td>
                <td><code style="font-size:12px"><?= h($m['steamid']) ?></code></td>
                <td><span class="badge badge-purple"><?= h($m['type']) ?></span></td>
                <td class="ban-reason" title="<?= h($m['reason']) ?>"><?= h($m['reason']) ?></td>
                <td><?= $m['duration'] == 0 ? '<span class="duration-perm">Навсегда</span>' : h(formatDuration((int)$m['duration'])) ?></td>
                <td style="color:var(--text-muted);font-size:12px"><?= h(date('d.m.Y H:i', strtotime($m['created_at']))) ?></td>
                <td><?php $isActive = $m['active'] && ($m['duration'] == 0 || strtotime($m['expires_at']) > time()); echo $isActive ? '<span class="badge badge-red">Активен</span>' : '<span class="badge badge-gray">Истёк</span>'; ?></td>
                <td>
                    <div class="admin-actions">
                        <?php if ($isActive): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Снять мут?')">
                            <input type="hidden" name="action" value="unmute">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button class="btn btn-success btn-xs">Снять</button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button class="btn btn-danger btn-xs">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="9"><div class="empty-state"><div class="empty-state-icon">✅</div><p class="empty-state-text">Мутов нет</p></div></td></tr>
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

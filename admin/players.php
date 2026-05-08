<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Игроки';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $action = $_POST['action'] ?? '';
    if ($action === 'edit') {
        $steamid = trim($_POST['steamid'] ?? '');
        $rank    = (int)($_POST['rank'] ?? 0);
        $skill   = (int)($_POST['skill'] ?? 0);
        if ($steamid) {
            $db->prepare('UPDATE ' . LVLRANKS_TABLE . ' SET rank=?, skill=? WHERE steam=?')
               ->execute([$rank, $skill, $steamid]);
            $msg = 'success:Игрок обновлён.';
        }
    } elseif ($action === 'setrole') {
        $steamid64 = trim($_POST['steamid64'] ?? '');
        $role      = $_POST['role'] ?? 'user';
        if (!in_array($role, ['user','vip','admin','superadmin'])) $role = 'user';
        if ($steamid64 && $db) {
            $db->prepare('UPDATE users SET role=? WHERE steamid64=?')->execute([$role, $steamid64]);
            $msg = 'success:Роль обновлена.';
        }
    }
}

$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 25; $offset = ($page - 1) * $limit;
$players = []; $total = 0;

if ($db) {
    $where = $search ? 'WHERE (l.steam LIKE ? OR l.name LIKE ?)' : '';
    $args  = $search ? ["%$search%", "%$search%"] : [];
    $cnt   = $db->prepare("SELECT COUNT(*) FROM " . LVLRANKS_TABLE . " l $where"); $cnt->execute($args);
    $total = (int)$cnt->fetchColumn();
    $stmt  = $db->prepare("SELECT l.*, u.role, u.steamid64 as uid64 FROM " . LVLRANKS_TABLE . " l LEFT JOIN users u ON u.steamid64 = (SELECT steamid64 FROM users WHERE steamid64 IS NOT NULL LIMIT 1) $where ORDER BY l.skill DESC LIMIT $limit OFFSET $offset");
    // Simpler join by steamid64 conversion is done in PHP below
    $stmt2 = $db->prepare("SELECT * FROM " . LVLRANKS_TABLE . " l $where ORDER BY l.skill DESC LIMIT $limit OFFSET $offset");
    $stmt2->execute($args); $players = $stmt2->fetchAll();
}
$pages = $total > 0 ? ceil($total / $limit) : 1;

// Fetch site users for role display
$siteUsers = [];
if ($db) {
    $rows = $db->query('SELECT steamid64, role FROM users')->fetchAll();
    foreach ($rows as $r) $siteUsers[$r['steamid64']] = $r['role'];
}

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Игроки</h1>
    <p class="admin-sub">Список игроков и управление рангами</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<form method="GET" class="admin-filter-bar">
    <input class="search-input" name="q" value="<?= h($search) ?>" placeholder="Поиск по нику, SteamID...">
    <button class="btn btn-ghost btn-sm" type="submit">Найти</button>
    <?php if ($search): ?><a href="/admin/players.php" class="btn btn-ghost btn-sm">Сбросить</a><?php endif; ?>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>#</th><th>Ник</th><th>SteamID</th><th>Ранг</th><th>Очки</th><th>K/D</th><th>Роль</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php if ($players): $i = $offset + 1; foreach ($players as $p): ?>
            <?php
            $steamid64 = steamid_to_steamid64($p['steam']);
            $role = $siteUsers[$steamid64] ?? null;
            ?>
            <tr>
                <td style="color:var(--text-muted)"><?= $i++ ?></td>
                <td><?= h($p['name']) ?></td>
                <td><code style="font-size:12px"><?= h($p['steam']) ?></code></td>
                <td style="color:<?= rank_color((int)$p['rank']) ?>;font-weight:600"><?= h(rank_label((int)$p['rank'])) ?></td>
                <td style="color:var(--accent);font-weight:700"><?= number_format((int)$p['skill']) ?></td>
                <td><?= kd_ratio((int)$p['kills'], (int)$p['deaths']) ?></td>
                <td>
                    <?php if ($role): ?>
                    <span class="badge <?= $role === 'admin' || $role === 'superadmin' ? 'badge-yellow' : ($role === 'vip' ? 'badge-purple' : 'badge-gray') ?>"><?= h($role) ?></span>
                    <?php else: ?><span style="color:var(--text-muted);font-size:12px">—</span><?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <button class="btn btn-ghost btn-xs" onclick="openEditModal('<?= h($p['steam']) ?>','<?= (int)$p['rank'] ?>','<?= (int)$p['skill'] ?>')">Изменить</button>
                        <a href="/admin/bans.php" class="btn btn-danger btn-xs">Бан</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="8"><div class="empty-state"><div class="empty-state-icon">😶</div><p class="empty-state-text">Игроки не найдены</p></div></td></tr>
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

<!-- Edit modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:#00000080;z-index:500;align-items:center;justify-content:center">
    <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:28px;width:360px;max-width:90vw">
        <h3 style="margin-bottom:20px;font-size:1.1rem">Редактировать игрока</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="steamid" id="editSteamid">
            <div class="form-group">
                <label>Ранг (1–18)</label>
                <input class="form-control" name="rank" id="editRank" type="number" min="1" max="18">
            </div>
            <div class="form-group">
                <label>Очки (skill)</label>
                <input class="form-control" name="skill" id="editSkill" type="number" min="0">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>
<script>
function openEditModal(steamid, rank, skill) {
    document.getElementById('editSteamid').value = steamid;
    document.getElementById('editRank').value = rank;
    document.getElementById('editSkill').value = skill;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

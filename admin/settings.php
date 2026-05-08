<?php
require_once __DIR__ . '/includes/admin_head.php';
if (!isSuperAdmin()) { http_response_code(403); die('Только для суперадмина.'); }
$adminTitle = 'Настройки';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $keys = ['vk_url', 'telegram_url', 'discord_url'];
    foreach ($keys as $k) {
        $val = trim($_POST[$k] ?? '');
        $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=?")
           ->execute([$k, $val, $val]);
    }

    // Admin management
    if (!empty($_POST['admin_steamid64']) && !empty($_POST['admin_role'])) {
        $sid = trim($_POST['admin_steamid64']);
        $role = $_POST['admin_role'];
        if (!in_array($role, ['user','vip','admin','superadmin'])) $role = 'user';
        $db->prepare('UPDATE users SET role=? WHERE steamid64=?')->execute([$role, $sid]);
        $msg = 'success:Роль обновлена.';
    } else {
        $msg = 'success:Настройки сохранены.';
    }
}

$settings = [];
if ($db) {
    $rows = $db->query("SELECT `key`, `value` FROM settings")->fetchAll();
    foreach ($rows as $r) $settings[$r['key']] = $r['value'];
}
$admins = $db ? $db->query("SELECT * FROM users WHERE role IN ('admin','superadmin') ORDER BY role DESC, name ASC")->fetchAll() : [];

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Настройки</h1>
    <p class="admin-sub">Настройки сайта и управление администраторами</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <!-- Social links -->
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:24px">
        <h3 style="margin-bottom:20px;font-size:1rem;font-weight:700">Социальные сети</h3>
        <form method="POST" class="admin-form" style="max-width:100%">
            <div class="form-group">
                <label>VK URL</label>
                <input class="form-control" name="vk_url" value="<?= h($settings['vk_url'] ?? '') ?>" placeholder="https://vk.com/...">
            </div>
            <div class="form-group">
                <label>Telegram URL</label>
                <input class="form-control" name="telegram_url" value="<?= h($settings['telegram_url'] ?? '') ?>" placeholder="https://t.me/...">
            </div>
            <div class="form-group">
                <label>Discord URL</label>
                <input class="form-control" name="discord_url" value="<?= h($settings['discord_url'] ?? '') ?>" placeholder="https://discord.gg/...">
            </div>
            <button type="submit" class="btn btn-primary">💾 Сохранить</button>
        </form>
    </div>

    <!-- Admin management -->
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:24px">
        <h3 style="margin-bottom:20px;font-size:1rem;font-weight:700">Управление администраторами</h3>
        <form method="POST" class="admin-form" style="max-width:100%">
            <div class="form-group">
                <label>SteamID64 пользователя</label>
                <input class="form-control" name="admin_steamid64" placeholder="76561198...">
            </div>
            <div class="form-group">
                <label>Роль</label>
                <select class="form-control" name="admin_role">
                    <option value="user">Пользователь</option>
                    <option value="vip">VIP</option>
                    <option value="admin">Администратор</option>
                    <option value="superadmin">Суперадмин</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Обновить роль</button>
        </form>

        <?php if ($admins): ?>
        <div style="margin-top:20px">
            <h4 style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Текущие администраторы</h4>
            <?php foreach ($admins as $a): ?>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                <img src="<?= h($a['avatar']) ?>" style="width:28px;height:28px;border-radius:50%">
                <div>
                    <div style="font-size:13px;font-weight:600"><?= h($a['name']) ?></div>
                    <div style="font-size:11px;color:var(--text-muted)"><?= h($a['steamid64']) ?></div>
                </div>
                <span class="badge <?= $a['role'] === 'superadmin' ? 'badge-yellow' : 'badge-blue' ?>" style="margin-left:auto"><?= h($a['role']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

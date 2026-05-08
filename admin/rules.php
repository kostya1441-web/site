<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Правила';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $content = $_POST['rules_content'] ?? '';
    $db->prepare("INSERT INTO settings (`key`, `value`) VALUES ('rules_content', ?) ON DUPLICATE KEY UPDATE `value`=?")
       ->execute([$content, $content]);
    $msg = 'success:Правила сохранены.';
}

$rules_content = '';
if ($db) {
    $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key`='rules_content'");
    $stmt->execute(); $rules_content = $stmt->fetchColumn() ?: '';
}

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Правила сервера</h1>
    <p class="admin-sub">Редактирование правил (поддерживается HTML)</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div>
        <h3 style="margin-bottom:12px;font-size:1rem;font-weight:700">Редактор</h3>
        <form method="POST">
            <textarea class="form-control" name="rules_content" rows="30" placeholder="<h2>Раздел 1: Общие правила</h2>&#10;<ol>&#10;<li>Правило 1</li>&#10;</ol>"><?= h($rules_content) ?></textarea>
            <div style="margin-top:12px">
                <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                <a href="/rules.php" target="_blank" class="btn btn-ghost" style="margin-left:8px">👁 Просмотр</a>
            </div>
        </form>
    </div>
    <div>
        <h3 style="margin-bottom:12px;font-size:1rem;font-weight:700">Предпросмотр</h3>
        <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:20px;font-size:14px;color:var(--text-muted);line-height:1.7;max-height:600px;overflow-y:auto" id="preview">
            <?= $rules_content ?: '<p style="color:var(--text-muted)">Начните вводить правила...</p>' ?>
        </div>
    </div>
</div>

<script>
document.querySelector('[name="rules_content"]').addEventListener('input', function() {
    document.getElementById('preview').innerHTML = this.value || '<p style="color:var(--text-muted)">Начните вводить правила...</p>';
});
</script>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

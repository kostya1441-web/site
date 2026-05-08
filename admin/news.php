<?php
require_once __DIR__ . '/includes/admin_head.php';
$adminTitle = 'Новости';
$db = getDB();
$msg = '';

// Handle edit fetch
$editing = null;
if (isset($_GET['edit']) && $db) {
    $stmt = $db->prepare('SELECT * FROM news WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id        = (int)($_POST['id'] ?? 0);
        $title     = trim($_POST['title'] ?? '');
        $content   = trim($_POST['content'] ?? '');
        $tag       = trim($_POST['tag'] ?? 'Новость');
        $tag_color = trim($_POST['tag_color'] ?? '#3b82f6');
        $published = isset($_POST['published']) ? 1 : 0;
        $author    = getCurrentUser()['name'];

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $tag_color)) $tag_color = '#3b82f6';

        if ($title && $content) {
            if ($id) {
                $db->prepare('UPDATE news SET title=?,content=?,tag=?,tag_color=?,published=? WHERE id=?')
                   ->execute([$title, $content, $tag, $tag_color, $published, $id]);
                $msg = 'success:Новость обновлена.';
            } else {
                $db->prepare('INSERT INTO news (title, content, tag, tag_color, author, published) VALUES (?,?,?,?,?,?)')
                   ->execute([$title, $content, $tag, $tag_color, $author, $published]);
                $msg = 'success:Новость создана.';
            }
            $editing = null;
        } else { $msg = 'error:Заполните заголовок и текст.'; }
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM news WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'success:Новость удалена.';
    } elseif ($action === 'toggle') {
        $db->prepare('UPDATE news SET published = 1-published WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'success:Статус обновлён.';
    }
}

$articles = $db ? $db->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll() : [];

include __DIR__ . '/includes/layout_top.php';
[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];
?>

<div class="admin-header">
    <h1 class="admin-title">Новости</h1>
    <p class="admin-sub">Создание и редактирование новостей</p>
</div>

<?php if ($msgText): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= h($msgText) ?></div>
<?php endif; ?>

<!-- Form -->
<div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:24px;margin-bottom:28px">
    <h3 style="margin-bottom:20px;font-size:1rem;font-weight:700"><?= $editing ? 'Редактировать новость' : 'Создать новость' ?></h3>
    <form method="POST" class="admin-form" style="max-width:100%">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int)$editing['id'] : 0 ?>">
        <div class="form-group">
            <label>Заголовок *</label>
            <input class="form-control" name="title" value="<?= h($editing['title'] ?? '') ?>" required placeholder="Заголовок новости">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
            <div class="form-group">
                <label>Тег</label>
                <input class="form-control" name="tag" value="<?= h($editing['tag'] ?? 'Новость') ?>" placeholder="Обновление">
            </div>
            <div class="form-group">
                <label>Цвет тега (HEX)</label>
                <input class="form-control" name="tag_color" type="color" value="<?= h($editing['tag_color'] ?? '#3b82f6') ?>">
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:0">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:0">
                    <input type="checkbox" name="published" <?= (!$editing || $editing['published']) ? 'checked' : '' ?>>
                    Опубликовано
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Текст *</label>
            <textarea class="form-control" name="content" rows="6" required placeholder="Текст новости..."><?= h($editing['content'] ?? '') ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $editing ? '💾 Сохранить' : '➕ Создать' ?></button>
            <?php if ($editing): ?><a href="/admin/news.php" class="btn btn-ghost">Отмена</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- List -->
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>#</th><th>Заголовок</th><th>Тег</th><th>Автор</th><th>Дата</th><th>Статус</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php if ($articles): foreach ($articles as $a): ?>
            <tr>
                <td style="color:var(--text-muted)"><?= $a['id'] ?></td>
                <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($a['title']) ?></td>
                <td><span class="news-tag" style="color:<?= h($a['tag_color']) ?>;border-color:<?= h($a['tag_color']) ?>40"><?= h($a['tag']) ?></span></td>
                <td><?= h($a['author']) ?></td>
                <td style="color:var(--text-muted);font-size:12px"><?= h(date('d.m.Y', strtotime($a['created_at']))) ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="badge <?= $a['published'] ? 'badge-green' : 'badge-gray' ?>" style="background:none;border-width:1px;cursor:pointer">
                            <?= $a['published'] ? 'Опубл.' : 'Черновик' ?>
                        </button>
                    </form>
                </td>
                <td>
                    <div class="admin-actions">
                        <a href="/admin/news.php?edit=<?= $a['id'] ?>" class="btn btn-ghost btn-xs">✏️ Изменить</a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Удалить новость?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-xs">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="7"><div class="empty-state"><div class="empty-state-icon">📰</div><p class="empty-state-text">Новостей нет. Создайте первую!</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/layout_bottom.php'; ?>

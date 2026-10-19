<?php
require_once __DIR__ . '/../includes/block_editor.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$page = $stmt->fetch();
if (!$page) {
    http_response_code(404);
    echo 'Страница не найдена';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->beginTransaction();

    db()->prepare('UPDATE pages SET eyebrow_en = ?, eyebrow_ru = ?, title_en = ?, title_ru = ? WHERE id = ?')
        ->execute([
            trim($_POST['eyebrow_en'] ?? ''),
            trim($_POST['eyebrow_ru'] ?? ''),
            trim($_POST['title_en'] ?? $page['title_en']),
            trim($_POST['title_ru'] ?? $page['title_ru']),
            $page['id'],
        ]);

    $rawBlocks = $_POST['blocks'] ?? [];
    ksort($rawBlocks, SORT_NUMERIC);
    db()->prepare('DELETE FROM blocks WHERE page_id = ?')->execute([$page['id']]);
    $insert = db()->prepare('INSERT INTO blocks (page_id, sort_order, type, data) VALUES (?, ?, ?, ?)');
    $order = 0;
    foreach ($rawBlocks as $idx => $raw) {
        if (!empty($raw['__delete'])) continue;
        $type = $raw['type'] ?? '';
        if ($type === '') continue;
        $data = block_from_form($type, (int)$idx);
        $insert->execute([$page['id'], $order++, $type, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }

    if (($_POST['__action'] ?? '') === 'addBlock' && !empty($_POST['__newBlockType'])) {
        $newType = $_POST['__newBlockType'];
        if (isset(BLOCK_LABELS[$newType])) {
            $insert->execute([$page['id'], $order++, $newType, json_encode(default_block_for($newType), JSON_UNESCAPED_UNICODE)]);
        }
    }

    db()->commit();
    redirect('/admin/page-edit.php?slug=' . urlencode($slug));
}

$page = get_page($slug);

$blocksHtml = '';
foreach ($page['blocks'] as $i => $b) {
    $label = BLOCK_LABELS[$b['type']] ?? $b['type'];
    $blocksHtml .= '<div class="a-block">
      <div class="a-block-head">
        <span class="type">' . esc($label) . '</span>
        <label style="font-size:0.8rem"><input type="checkbox" name="blocks[' . $i . '][__delete]" value="1"> Удалить блок</label>
      </div>
      <input type="hidden" name="blocks[' . $i . '][type]" value="' . esc($b['type']) . '">
      ' . block_fields($b, $i) . '
    </div>';
}

$addTypeOptions = '';
foreach (BLOCK_LABELS as $v => $l) {
    $addTypeOptions .= '<option value="' . esc($v) . '">' . esc($l) . '</option>';
}

$body = '
<form method="post" enctype="multipart/form-data">
  <div class="a-card">
    <h2>Заголовок страницы</h2>
    ' . f_bi('eyebrow', 'Надпись над заголовком', $page['eyebrow_en'], $page['eyebrow_ru']) . '
    ' . f_bi('title', 'Заголовок страницы (H1)', $page['title_en'], $page['title_ru']) . '
  </div>
  <div class="a-card">
    <h2>Блоки содержимого</h2>
    ' . ($blocksHtml ?: '<p class="hint">Пока нет блоков.</p>') . '
    <div class="add-type-row">
      <select name="__newBlockType">' . $addTypeOptions . '</select>
      <button class="a-btn ghost small" type="submit" name="__action" value="addBlock">+ Добавить блок</button>
    </div>
  </div>
  <div class="a-row">
    <button class="a-btn accent" type="submit" name="__action" value="save">Сохранить изменения</button>
    <a class="a-btn ghost" href="/' . esc($slug) . '/" target="_blank">Открыть страницу на сайте</a>
  </div>
</form>';

echo admin_shell('Страница: ' . ($page['title_en'] ?: $slug), '/admin/pages.php', $body);

<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$items = get_case_studies();
$rows = '';
foreach ($items as $c) {
    $rows .= '<tr><td><img class="a-thumb" src="' . esc($c['image']) . '"></td><td>' . esc($c['title_en'])
        . '</td><td><code>' . esc($c['slug']) . '</code></td><td class="a-row">'
        . '<a class="a-btn small" href="/admin/case-study-edit.php?id=' . (int)$c['id'] . '">Редактировать</a>'
        . '<form method="post" action="/admin/case-study-delete.php" onsubmit="return confirm(\'Удалить пример проекта?\')">'
        . '<input type="hidden" name="id" value="' . (int)$c['id'] . '"><button class="a-btn small danger" type="submit">Удалить</button></form>'
        . '</td></tr>';
}

$body = '<div class="a-row" style="margin-bottom:16px"><a class="a-btn accent" href="/admin/case-study-edit.php">+ Новый пример проекта</a></div>
<div class="a-card"><table class="a-table"><thead><tr><th>Фото</th><th>Название</th><th>Slug</th><th></th></tr></thead><tbody>' . $rows . '</tbody></table></div>';

echo admin_shell('Примеры проектов', '/admin/case-studies.php', $body);

<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$items = get_articles();
$rows = '';
foreach ($items as $a) {
    $rows .= '<tr><td><img class="a-thumb" src="' . esc($a['image']) . '"></td><td>' . esc($a['title_en'])
        . '</td><td>' . esc($a['date_en']) . '</td><td class="a-row">'
        . '<a class="a-btn small" href="/admin/article-edit.php?id=' . (int)$a['id'] . '">Редактировать</a>'
        . '<form method="post" action="/admin/article-delete.php" onsubmit="return confirm(\'Удалить статью?\')">'
        . '<input type="hidden" name="id" value="' . (int)$a['id'] . '"><button class="a-btn small danger" type="submit">Удалить</button></form>'
        . '</td></tr>';
}

$body = '<div class="a-row" style="margin-bottom:16px"><a class="a-btn accent" href="/admin/article-edit.php">+ Новая статья</a></div>
<div class="a-card"><table class="a-table"><thead><tr><th>Фото</th><th>Заголовок</th><th>Дата</th><th></th></tr></thead><tbody>' . $rows . '</tbody></table></div>';

echo admin_shell('Пресса и статьи', '/admin/articles.php', $body);

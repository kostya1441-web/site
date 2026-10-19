<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

const RESERVED_SLUGS = ['admin', 'assets', 'uploads', 'case-studies', 'press-releases', 'contact'];

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = slugify($_POST['slug'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $titleRu = trim($_POST['title_ru'] ?? '');
    $firstSeg = explode('/', $slug)[0];

    if (!$slug || !$titleEn || !$titleRu) {
        $flash = ['error' => true, 'text' => 'Укажите URL и заголовок на обоих языках.'];
    } elseif (in_array($firstSeg, RESERVED_SLUGS, true)) {
        $flash = ['error' => true, 'text' => "URL, начинающиеся с \"$firstSeg\", зарезервированы системой — выберите другой."];
    } else {
        $stmt = db()->prepare('SELECT id FROM pages WHERE slug = ?');
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $flash = ['error' => true, 'text' => "Страница /$slug/ уже существует."];
        } else {
            $crumbs = json_encode([
                ['en' => 'Home', 'ru' => 'Главная', 'href' => '/'],
                ['en' => $titleEn, 'ru' => $titleRu, 'href' => null],
            ], JSON_UNESCAPED_UNICODE);
            $stmt = db()->prepare('INSERT INTO pages (slug, title_en, title_ru, eyebrow_en, eyebrow_ru, show_cta, crumbs_json) VALUES (?, ?, ?, \'\', \'\', 1, ?)');
            $stmt->execute([$slug, $titleEn, $titleRu, $crumbs]);
            $pageId = (int)db()->lastInsertId();
            $data = json_encode([
                'title_en' => $titleEn, 'title_ru' => $titleRu,
                'body_en' => '<p>New page — edit this text.</p>',
                'body_ru' => '<p>Новая страница — отредактируйте этот текст.</p>',
            ], JSON_UNESCAPED_UNICODE);
            db()->prepare('INSERT INTO blocks (page_id, sort_order, type, data) VALUES (?, 0, \'text\', ?)')->execute([$pageId, $data]);
            redirect('/admin/page-edit.php?slug=' . urlencode($slug));
        }
    }
}

$pages = get_all_pages();
$rows = '';
foreach ($pages as $p) {
    $url = $p['slug'] === '' ? '/' : '/' . esc($p['slug']) . '/';
    $deleteBtn = $p['slug'] === ''
        ? '<span class="a-badge">Главная</span>'
        : '<form method="post" action="/admin/page-delete.php" onsubmit="return confirm(\'Удалить страницу ' . esc($url) . '? Ссылки на неё перестанут работать.\')">'
            . '<input type="hidden" name="id" value="' . (int)$p['id'] . '"><button class="a-btn small danger" type="submit">Удалить</button></form>';
    $rows .= '<tr><td>' . esc($p['title_en'] ?: $p['slug']) . "</td><td><code>$url</code></td><td class=\"a-row\">"
        . '<a class="a-btn small" href="/admin/page-edit.php?slug=' . urlencode($p['slug']) . '">Редактировать</a>'
        . $deleteBtn . '</td></tr>';
}

$body = '
<div class="a-card">
  <h2>Создать новую страницу</h2>
  <form method="post" class="a-grid" style="align-items:end">
    ' . f_text('slug', 'URL страницы (латиницей, например about/team)', '', ['required' => true]) . '
    ' . f_text('title_en', 'Заголовок (EN)', '', ['required' => true]) . '
    ' . f_text('title_ru', 'Заголовок (RU)', '', ['required' => true]) . '
    <button class="a-btn accent" type="submit" style="height:40px">+ Создать страницу</button>
  </form>
  <p class="hint">Страница будет доступна по адресу /<слаг>/. Пункт в меню навигации нужно будет добавить отдельно на странице «Меню навигации», если он там нужен.</p>
</div>
<div class="a-card">
  <table class="a-table"><thead><tr><th>Страница</th><th>URL</th><th></th></tr></thead><tbody>' . $rows . '</tbody></table>
</div>';

echo admin_shell('Страницы сайта', '/admin/pages.php', $body, $flash);

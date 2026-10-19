<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isNew = $id === null;
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = slugify($_POST['slug'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $titleRu = trim($_POST['title_ru'] ?? '');

    if (!$slug || !$titleEn || !$titleRu) {
        $flash = ['error' => true, 'text' => 'Укажите slug и заголовок на обоих языках.'];
    } else {
        $dupStmt = db()->prepare('SELECT id FROM articles WHERE slug = ? AND id != ?');
        $dupStmt->execute([$slug, $id ?? 0]);
        if ($dupStmt->fetch()) {
            $flash = ['error' => true, 'text' => "Такой slug уже существует: $slug"];
        } else {
            $image = handle_image_upload('image', trim($_POST['image_current'] ?? ''));
            $params = [
                $slug, $titleEn, $titleRu,
                trim($_POST['date_en'] ?? ''), trim($_POST['date_ru'] ?? ''),
                $image,
                trim($_POST['teaser_en'] ?? ''), trim($_POST['teaser_ru'] ?? ''),
                trim($_POST['body_en'] ?? ''), trim($_POST['body_ru'] ?? ''),
            ];
            if ($isNew) {
                db()->prepare('INSERT INTO articles (slug, title_en, title_ru, date_en, date_ru, image, teaser_en, teaser_ru, body_en, body_ru, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, (SELECT m FROM (SELECT COALESCE(MAX(sort_order), 0) + 1 m FROM articles) t))')
                    ->execute($params);
            } else {
                $params[] = $id;
                db()->prepare('UPDATE articles SET slug=?, title_en=?, title_ru=?, date_en=?, date_ru=?, image=?, teaser_en=?, teaser_ru=?, body_en=?, body_ru=? WHERE id=?')
                    ->execute($params);
            }
            redirect('/admin/articles.php');
        }
    }
}

$a = $isNew ? ['slug' => '', 'title_en' => '', 'title_ru' => '', 'date_en' => '', 'date_ru' => '', 'image' => '', 'teaser_en' => '', 'teaser_ru' => '', 'body_en' => '', 'body_ru' => ''] : null;
if (!$isNew) {
    $stmt = db()->prepare('SELECT * FROM articles WHERE id = ?');
    $stmt->execute([$id]);
    $a = $stmt->fetch();
    if (!$a) { http_response_code(404); echo 'Не найдено'; exit; }
}

$body = '<form method="post" enctype="multipart/form-data">
  <div class="a-card">
    ' . f_text('slug', 'Slug (часть URL)', $a['slug'], ['required' => true]) . '
    ' . f_bi('title', 'Заголовок', $a['title_en'], $a['title_ru']) . '
    ' . f_bi('date', 'Дата (как текст)', $a['date_en'], $a['date_ru']) . '
    ' . f_image('image', 'Фото', $a['image']) . '
    ' . f_bi('teaser', 'Короткий анонс (для списка)', $a['teaser_en'], $a['teaser_ru'], true) . '
    ' . f_bi('body', 'Текст статьи (HTML разрешён, можно использовать <h3>, <p>)', $a['body_en'], $a['body_ru'], true) . '
  </div>
  <button class="a-btn accent" type="submit">Сохранить</button>
</form>';

echo admin_shell($isNew ? 'Новая статья' : $a['title_en'], '/admin/articles.php', $body, $flash);

<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isNew = $id === null;
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = slugify($_POST['slug'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $titleRu = trim($_POST['title_ru'] ?? '');

    if (!$slug || !$titleEn || !$titleRu) {
        $flash = ['error' => true, 'text' => 'Укажите slug и название на обоих языках.'];
    } else {
        $dupStmt = db()->prepare('SELECT id FROM case_studies WHERE slug = ? AND id != ?');
        $dupStmt->execute([$slug, $id ?? 0]);
        if ($dupStmt->fetch()) {
            $flash = ['error' => true, 'text' => "Такой slug уже существует: $slug"];
        } else {
            $image = handle_image_upload('image', trim($_POST['image_current'] ?? ''));
            $challengeEn = trim($_POST['challenge_en'] ?? '');
            $challengeRu = trim($_POST['challenge_ru'] ?? '');

            if ($isNew) {
                db()->prepare('INSERT INTO case_studies (slug, title_en, title_ru, image, challenge_en, challenge_ru, sort_order) VALUES (?, ?, ?, ?, ?, ?, (SELECT m FROM (SELECT COALESCE(MAX(sort_order), 0) + 1 m FROM case_studies) t))')
                    ->execute([$slug, $titleEn, $titleRu, $image, $challengeEn, $challengeRu]);
                $id = (int)db()->lastInsertId();
            } else {
                db()->prepare('UPDATE case_studies SET slug = ?, title_en = ?, title_ru = ?, image = ?, challenge_en = ?, challenge_ru = ? WHERE id = ?')
                    ->execute([$slug, $titleEn, $titleRu, $image, $challengeEn, $challengeRu, $id]);
            }

            db()->prepare('DELETE FROM case_study_solutions WHERE case_study_id = ?')->execute([$id]);
            $insert = db()->prepare('INSERT INTO case_study_solutions (case_study_id, text_en, text_ru, sort_order) VALUES (?, ?, ?, ?)');
            $solutions = $_POST['solution'] ?? [];
            ksort($solutions, SORT_NUMERIC);
            $order = 0;
            foreach ($solutions as $s) {
                $en = trim($s['en'] ?? '');
                $ru = trim($s['ru'] ?? '');
                if ($en === '' && $ru === '') continue;
                $insert->execute([$id, $en, $ru, $order++]);
            }

            redirect('/admin/case-studies.php');
        }
    }
}

$cs = $isNew ? ['slug' => '', 'title_en' => '', 'title_ru' => '', 'image' => '', 'challenge_en' => '', 'challenge_ru' => '', 'solution' => []]
    : get_case_study_by_id($id);
if (!$isNew && !$cs) { http_response_code(404); echo 'Не найдено'; exit; }

function get_case_study_by_id(int $id): ?array {
    $stmt = db()->prepare('SELECT * FROM case_studies WHERE id = ?');
    $stmt->execute([$id]);
    $cs = $stmt->fetch();
    if (!$cs) return null;
    $stmt = db()->prepare('SELECT * FROM case_study_solutions WHERE case_study_id = ? ORDER BY sort_order');
    $stmt->execute([$id]);
    $cs['solution'] = array_map(fn($s) => ['en' => $s['text_en'], 'ru' => $s['text_ru']], $stmt->fetchAll());
    return $cs;
}

$solutionRows = '';
foreach (($cs['solution'] ?? []) as $i => $it) $solutionRows .= repeater_item_text('solution', $i, $it);
$emptyRow = repeater_item_text('solution', 0, []);
$emptyInner = preg_replace('#^<div class="repeater-row">|</div>$#', '', $emptyRow);

$body = '<form method="post" enctype="multipart/form-data">
  <div class="a-card">
    ' . f_text('slug', 'Slug (часть URL, латиницей, без пробелов)', $cs['slug'], ['required' => true]) . '
    ' . f_bi('title', 'Название', $cs['title_en'], $cs['title_ru']) . '
    ' . f_image('image', 'Фото', $cs['image']) . '
    ' . f_bi('challenge', 'Задача (необязательно)', $cs['challenge_en'], $cs['challenge_ru'], true) . '
    <div class="a-field">
      <label class="a-label">Решение (пункты списка)</label>
      <div id="rep-solution">' . $solutionRows . '</div>
      <template id="rep-solution-template"><div class="repeater-row">' . $emptyInner . '</div></template>
      <button type="button" class="a-btn small ghost" data-add-row="rep-solution" style="margin-top:6px">+ Добавить строку</button>
    </div>
  </div>
  <button class="a-btn accent" type="submit">Сохранить</button>
</form>';

echo admin_shell($isNew ? 'Новый пример проекта' : $cs['title_en'], '/admin/case-studies.php', $body, $flash);

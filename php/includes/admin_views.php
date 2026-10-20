<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

function admin_shell(string $title, string $active, string $body, ?array $flash = null): string {
    $navItems = [
        ['/admin/', 'Дашборд'],
        ['/admin/pages.php', 'Страницы'],
        ['/admin/nav.php', 'Меню навигации'],
        ['/admin/case-studies.php', 'Примеры проектов'],
        ['/admin/articles.php', 'Пресса и статьи'],
        ['/admin/settings.php', 'Настройки и контакты'],
        ['/admin/leads.php', 'Заявки'],
    ];
    $nav = '';
    foreach ($navItems as [$href, $label]) {
        $cls = $active === $href ? 'active' : '';
        $nav .= "<a href=\"$href\" class=\"$cls\">$label</a>";
    }
    $flashHtml = '';
    if ($flash) {
        $cls = !empty($flash['error']) ? 'error' : '';
        $flashHtml = "<div class=\"a-flash $cls\">" . esc($flash['text']) . '</div>';
    }
    return '<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . esc($title) . ' — Админка Адамко</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin">
<div class="a-shell">
  <aside class="a-sidebar">
    <div class="brand"><img src="/assets/img/logo-adamko.jpg" alt=""> Адамко</div>
    <nav>' . $nav . '</nav>
    <div class="group-label">&nbsp;</div>
    <nav>
      <a href="/" target="_blank">↗ Открыть сайт</a>
      <a href="/admin/logout.php">Выйти</a>
    </nav>
  </aside>
  <main class="a-main">
    <div class="a-topbar"><h1>' . esc($title) . '</h1></div>
    <div class="a-content">' . $flashHtml . $body . '</div>
  </main>
</div>
<script src="/assets/js/admin.js"></script>
</body>
</html>';
}

function admin_login_page(?string $error = null): string {
    $errHtml = $error ? '<div class="a-flash error">' . esc($error) . '</div>' : '';
    return '<!doctype html>
<html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вход в админку — Адамко</title>
<link rel="stylesheet" href="/assets/css/admin.css"></head>
<body class="admin">
<div class="login-wrap">
  <form class="login-card" method="post" action="/admin/login.php">
    <img src="/assets/img/logo-adamko.jpg" alt="" style="height:48px;border-radius:6px;margin-bottom:14px">
    <h1>Адамко</h1>
    <p>Вход в панель администратора</p>
    ' . $errHtml . '
    <div class="a-field"><label class="a-label">Логин</label><input type="text" name="username" required autofocus></div>
    <div class="a-field"><label class="a-label">Пароль</label><input type="password" name="password" required></div>
    <button class="a-btn accent" style="width:100%;justify-content:center" type="submit">Войти</button>
  </form>
</div>
</body></html>';
}

// ---------------------------------------------------------- field helpers --

function f_text(string $name, string $label, $value, array $opts = []): string {
    $type = $opts['type'] ?? 'text';
    $req = !empty($opts['required']) ? 'required' : '';
    return '<div class="a-field"><label class="a-label">' . esc($label) . '</label>'
        . "<input type=\"$type\" name=\"" . esc($name) . '" value="' . esc($value) . "\" $req></div>";
}

function f_textarea(string $name, string $label, $value): string {
    return '<div class="a-field"><label class="a-label">' . esc($label) . '</label>'
        . '<textarea name="' . esc($name) . '">' . esc($value) . '</textarea></div>';
}

// $base should end in its own bracket segment for nested fields, e.g.
// "blocks[0][title]" (-> blocks[0][title_en] / blocks[0][title_ru]), or
// be a bare flat name at the top level, e.g. "title".
function f_bi(string $base, string $label, $en, $ru, bool $multiline = false): string {
    $fn = $multiline ? 'f_textarea' : 'f_text';
    return '<div class="a-grid">' . $fn(with_suffix($base, '_en'), "$label (EN)", $en) . $fn(with_suffix($base, '_ru'), "$label (RU)", $ru) . '</div>';
}

// $name is the logical (bracketed or flat) path for this image field,
// e.g. "blocks[0][image]" or "logo". The actual <input type="file">
// is given a separate, flat, deterministically-derived name (see
// file_key_for()) so PHP's nested-$_FILES hoisting never comes into
// play; handle_image_upload() derives the same flat name to find it.
function f_image(string $name, string $label, $value): string {
    $previewId = 'prev-' . preg_replace('/[^a-zA-Z0-9]/', '-', $name);
    $img = $value
        ? '<img id="' . $previewId . '" class="img-preview" src="' . esc($value) . '">'
        : '<img id="' . $previewId . '" class="img-preview" style="display:none">';
    $fileKey = file_key_for($name);
    return '<div class="a-field"><label class="a-label">' . esc($label) . '</label>'
        . $img
        . '<input type="hidden" name="' . esc(with_suffix($name, '_current')) . '" value="' . esc($value) . '">'
        . '<input type="file" name="' . esc($fileKey) . '" data-preview="' . $previewId . '" accept="image/*">'
        . '<p class="hint">Текущий файл: ' . ($value ? esc($value) : 'нет') . '. Загрузите новый, чтобы заменить.</p></div>';
}

function f_checkbox(string $name, string $label, $checked): string {
    $chk = $checked ? 'checked' : '';
    return '<label style="display:flex;gap:8px;align-items:center;font-size:0.85rem;margin-bottom:10px">'
        . '<input type="checkbox" name="' . esc($name) . "\" value=\"1\" $chk> " . esc($label) . '</label>';
}

function f_select(string $name, string $label, array $options, $value): string {
    $opts = '';
    foreach ($options as [$v, $l]) {
        $sel = (string)$v === (string)$value ? 'selected' : '';
        $opts .= "<option value=\"" . esc($v) . "\" $sel>" . esc($l) . '</option>';
    }
    return '<div class="a-field"><label class="a-label">' . esc($label) . "</label><select name=\"" . esc($name) . "\">$opts</select></div>";
}

function repeater_item_text(string $prefix, int $idx, array $item): string {
    return '<div class="repeater-row"><div class="a-grid">'
        . f_text("{$prefix}[$idx][en]", 'Текст (EN)', $item['en'] ?? '')
        . f_text("{$prefix}[$idx][ru]", 'Текст (RU)', $item['ru'] ?? '')
        . '</div><button type="button" class="a-btn small danger remove-row">Удалить</button></div>';
}

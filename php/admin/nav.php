<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->beginTransaction();
    db()->exec('DELETE FROM nav_items');
    $insert = db()->prepare('INSERT INTO nav_items (parent_id, label_en, label_ru, href, sort_order) VALUES (?, ?, ?, ?, ?)');

    $rawItems = $_POST['navItems'] ?? [];
    ksort($rawItems, SORT_NUMERIC);
    $order = 0;
    foreach ($rawItems as $item) {
        $en = trim($item['en'] ?? '');
        $ru = trim($item['ru'] ?? '');
        $href = trim($item['href'] ?? '');
        if ($en === '' && $ru === '' && $href === '') continue;
        $insert->execute([null, $en, $ru, $href, $order++]);
        $parentId = (int)db()->lastInsertId();

        $childOrder = 0;
        $rawChildren = $item['children'] ?? [];
        ksort($rawChildren, SORT_NUMERIC);
        foreach ($rawChildren as $child) {
            $cEn = trim($child['en'] ?? '');
            $cRu = trim($child['ru'] ?? '');
            $cHref = trim($child['href'] ?? '');
            if ($cEn === '' && $cRu === '' && $cHref === '') continue;
            $insert->execute([$parentId, $cEn, $cRu, $cHref, $childOrder++]);
        }
    }
    db()->commit();
    $flash = ['text' => 'Меню сохранено.'];
}

$nav = get_nav();

function nav_child_row(string $prefix, int $idx, array $child): string {
    return '<div class="repeater-row"><div class="a-grid">'
        . f_text("{$prefix}[children][$idx][en]", 'Подпункт — текст (EN)', $child['label_en'] ?? '')
        . f_text("{$prefix}[children][$idx][ru]", 'Подпункт — текст (RU)', $child['label_ru'] ?? '')
        . '</div>' . f_text("{$prefix}[children][$idx][href]", 'Подпункт — ссылка', $child['href'] ?? '')
        . '<button type="button" class="a-btn small danger remove-row">Удалить подпункт</button></div>';
}

function nav_item_row(int $idx, array $item, bool $isTemplate): string {
    $prefix = "navItems[$idx]";
    if ($isTemplate) {
        $childrenHtml = '<p class="hint">Сохраните страницу и откройте её заново, чтобы добавить вложенные пункты этому разделу.</p>';
    } else {
        $childrenContainerId = "nav-children-$idx";
        $rows = '';
        foreach (($item['children'] ?? []) as $j => $c) $rows .= nav_child_row($prefix, $j, $c);
        $emptyChild = nav_child_row($prefix, 0, []);
        $emptyChildInner = preg_replace('#^<div class="repeater-row">|</div>$#', '', $emptyChild);
        $childrenHtml = '<label class="a-label">Выпадающие подпункты</label>'
            . "<div id=\"$childrenContainerId\">$rows</div>"
            . "<template id=\"$childrenContainerId-template\"><div class=\"repeater-row\">$emptyChildInner</div></template>"
            . "<button type=\"button\" class=\"a-btn small ghost\" data-add-row=\"$childrenContainerId\" style=\"margin-top:6px\">+ Добавить подпункт</button>";
    }
    return '<div class="repeater-row a-block"><div class="a-grid">'
        . f_text("{$prefix}[en]", 'Пункт меню — текст (EN)', $item['label_en'] ?? '')
        . f_text("{$prefix}[ru]", 'Пункт меню — текст (RU)', $item['label_ru'] ?? '')
        . '</div>' . f_text("{$prefix}[href]", 'Ссылка (например /history/)', $item['href'] ?? '')
        . $childrenHtml
        . '<button type="button" class="a-btn small danger remove-row" style="position:static;margin-top:10px">Удалить весь пункт меню</button></div>';
}

$rows = '';
foreach ($nav as $i => $item) $rows .= nav_item_row($i, $item, false);
$templateInner = preg_replace('#^<div class="repeater-row a-block">|</div>$#', '', nav_item_row(0, [], true));

$body = '<form method="post">
  <div class="a-card">
    <h2>Пункты меню</h2>
    <p class="hint">Порядок пунктов совпадает с порядком в этом списке. У каждого пункта может быть набор выпадающих подпунктов.</p>
    <div id="nav-items">' . $rows . '</div>
    <template id="nav-items-template"><div class="repeater-row">' . $templateInner . '</div></template>
    <button type="button" class="a-btn ghost small" data-add-row="nav-items" style="margin-top:10px">+ Добавить пункт меню</button>
  </div>
  <button class="a-btn accent" type="submit">Сохранить меню</button>
</form>';

echo admin_shell('Меню навигации', '/admin/nav.php', $body, $flash ?? null);

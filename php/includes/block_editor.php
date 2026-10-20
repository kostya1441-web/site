<?php
require_once __DIR__ . '/admin_views.php';
require_once __DIR__ . '/upload.php';

const BLOCK_LABELS = [
    'hero' => 'Шапка (Hero)',
    'text' => 'Текстовый блок',
    'textImage' => 'Текст + изображение',
    'list' => 'Список',
    'cards' => 'Карточки',
    'panelGrid' => 'Панели (объекты/применение)',
    'advantages' => 'Преимущества конфигурации',
    'image' => 'Изображение',
    'logos' => 'Логотипы партнёров',
    'awards' => 'Награды',
    'caseStudyGrid' => 'Сетка примеров проектов (авто)',
    'articleGrid' => 'Список статей (авто)',
    'pressClippings' => 'Упоминания в прессе',
    'curveGroups' => 'Группы напорных характеристик',
    'contactBlock' => 'Контакты + форма заявки',
    'backLink' => 'Ссылка «Назад»',
];

const ICON_OPTIONS = [
    ['', '— без иконки —'],
    ['target', 'Цель (мишень)'],
    ['gear', 'Работа (шестерёнка)'],
    ['check', 'Применение (галочка)'],
    ['wave', 'Производительность (волна)'],
    ['layers', 'Материалы (слои)'],
    ['pencil', 'Технология (карандаш)'],
    ['atom', 'Материалы (атом)'],
    ['bolt', 'Мощность (молния)'],
    ['shield', 'Надёжность (щит)'],
    ['truck', 'Логистика (грузовик)'],
    ['globe', 'География (глобус)'],
];

function repeater_html(string $id, string $label, array $rows, callable $rowFn): string {
    $rowsHtml = '';
    foreach ($rows as $i => $r) $rowsHtml .= $rowFn($r, $i);
    $emptyHtml = $rowFn([], 0);
    $emptyInner = preg_replace('#^<div class="repeater-row">|</div>$#', '', $emptyHtml);
    return "<div class=\"a-field\"><label class=\"a-label\">" . esc($label) . "</label>"
        . "<div id=\"$id\">$rowsHtml</div>"
        . "<template id=\"$id-template\"><div class=\"repeater-row\">$emptyInner</div></template>"
        . "<button type=\"button\" class=\"a-btn small ghost\" data-add-row=\"$id\" style=\"margin-top:6px\">+ Добавить строку</button></div>";
}

function row_card_item(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\">"
        . f_select("{$prefix}[$idx][icon]", 'Иконка', ICON_OPTIONS, $it['icon'] ?? '')
        . "<div class=\"a-grid\">"
        . f_text("{$prefix}[$idx][title_en]", 'Заголовок (EN)', $it['title_en'] ?? '')
        . f_text("{$prefix}[$idx][title_ru]", 'Заголовок (RU)', $it['title_ru'] ?? '')
        . "</div><div class=\"a-grid\">"
        . f_textarea("{$prefix}[$idx][body_en]", 'Текст (EN)', $it['body_en'] ?? '')
        . f_textarea("{$prefix}[$idx][body_ru]", 'Текст (RU)', $it['body_ru'] ?? '')
        . "</div><div class=\"a-grid\"><div>"
        . f_image("{$prefix}[$idx][image]", 'Изображение', $it['image'] ?? '')
        . "</div><div>"
        . f_text("{$prefix}[$idx][link_href]", 'Ссылка (href)', $it['link_href'] ?? '')
        . f_text("{$prefix}[$idx][link_en]", 'Текст ссылки (EN)', $it['link_en'] ?? '')
        . f_text("{$prefix}[$idx][link_ru]", 'Текст ссылки (RU)', $it['link_ru'] ?? '')
        . "</div></div><button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function row_advantage(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\"><div class=\"a-grid\">"
        . f_text("{$prefix}[$idx][label_en]", 'Название (EN)', $it['label_en'] ?? '')
        . f_text("{$prefix}[$idx][label_ru]", 'Название (RU)', $it['label_ru'] ?? '')
        . "</div><div class=\"a-grid\">"
        . f_textarea("{$prefix}[$idx][body_en]", 'Описание (EN)', $it['body_en'] ?? '')
        . f_textarea("{$prefix}[$idx][body_ru]", 'Описание (RU)', $it['body_ru'] ?? '')
        . "</div><button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function row_panel(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\">"
        . f_select("{$prefix}[$idx][icon]", 'Иконка', ICON_OPTIONS, $it['icon'] ?? '')
        . "<div class=\"a-grid\">"
        . f_text("{$prefix}[$idx][title_en]", 'Заголовок (EN)', $it['title_en'] ?? '')
        . f_text("{$prefix}[$idx][title_ru]", 'Заголовок (RU)', $it['title_ru'] ?? '')
        . "</div><div class=\"a-grid\">"
        . f_textarea("{$prefix}[$idx][body_en]", 'Текст (EN)', $it['body_en'] ?? '')
        . f_textarea("{$prefix}[$idx][body_ru]", 'Текст (RU)', $it['body_ru'] ?? '')
        . '</div>' . f_checkbox("{$prefix}[$idx][wide]", 'На всю ширину', $it['wide'] ?? false)
        . "<button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function row_logo(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\">"
        . f_image("{$prefix}[$idx][image]", 'Изображение', $it['image'] ?? '')
        . f_text("{$prefix}[$idx][alt_en]", 'Подпись (alt)', $it['alt_en'] ?? '')
        . f_text("{$prefix}[$idx][link]", 'Ссылка на сайт партнёра (необязательно)', $it['link'] ?? '')
        . "<button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function row_clip(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\"><div class=\"a-grid\">"
        . f_text("{$prefix}[$idx][title_en]", 'Заголовок (EN)', $it['title_en'] ?? '')
        . f_text("{$prefix}[$idx][title_ru]", 'Заголовок (RU)', $it['title_ru'] ?? '')
        . '</div>' . f_text("{$prefix}[$idx][source]", 'Источник', $it['source'] ?? '')
        . "<button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function row_button(string $prefix, int $idx, array $it): string {
    return "<div class=\"repeater-row\"><div class=\"a-grid\">"
        . f_text("{$prefix}[$idx][label_en]", 'Текст кнопки (EN)', $it['label_en'] ?? '')
        . f_text("{$prefix}[$idx][label_ru]", 'Текст кнопки (RU)', $it['label_ru'] ?? '')
        . '</div>' . f_text("{$prefix}[$idx][href]", 'Ссылка', $it['href'] ?? '')
        . "<button type=\"button\" class=\"a-btn small danger remove-row\">Удалить</button></div>";
}

function block_fields(array $block, int $idx): string {
    $d = $block['data'] ?? [];
    $p = "blocks[$idx]";
    switch ($block['type']) {
        case 'hero':
            return f_bi("{$p}[eyebrow]", 'Надпись над заголовком', $d['eyebrow_en'] ?? '', $d['eyebrow_ru'] ?? '')
                . f_bi("{$p}[title]", 'Заголовок', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . f_bi("{$p}[body]", 'Текст', $d['body_en'] ?? '', $d['body_ru'] ?? '', true)
                . f_image("{$p}[image]", 'Изображение', $d['image'] ?? '')
                . repeater_html("rep-$idx-buttons", 'Кнопки', $d['buttons'] ?? [], fn($it, $i) => row_button("{$p}[buttons]", $i, $it));
        case 'text':
            return f_bi("{$p}[eyebrow]", 'Надпись над заголовком', $d['eyebrow_en'] ?? '', $d['eyebrow_ru'] ?? '')
                . f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . f_bi("{$p}[body]", 'Текст (HTML разрешён)', $d['body_en'] ?? '', $d['body_ru'] ?? '', true)
                . f_checkbox("{$p}[alt]", 'Светлый фон', $d['alt'] ?? false)
                . f_checkbox("{$p}[center]", 'По центру', $d['center'] ?? false);
        case 'textImage':
            return f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . f_bi("{$p}[body]", 'Текст (HTML разрешён)', $d['body_en'] ?? '', $d['body_ru'] ?? '', true)
                . f_image("{$p}[image]", 'Изображение', $d['image'] ?? '')
                . f_select("{$p}[imageSide]", 'Сторона изображения', [['right', 'Справа'], ['left', 'Слева']], $d['image_side'] ?? 'right')
                . f_checkbox("{$p}[alt]", 'Светлый фон', $d['alt'] ?? false);
        case 'list':
            return f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . f_select("{$p}[style]", 'Стиль', [['spec', 'Галочки'], ['tag', 'Плашки']], $d['style'] ?? 'spec')
                . repeater_html("rep-$idx-items", 'Пункты списка', $d['items'] ?? [], fn($it, $i) => repeater_item_text("{$p}[items]", $i, $it));
        case 'cards':
            return f_bi("{$p}[eyebrow]", 'Надпись над заголовком', $d['eyebrow_en'] ?? '', $d['eyebrow_ru'] ?? '')
                . f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . '<div class="a-grid">'
                . f_select("{$p}[variant]", 'Вид карточек', [['plain', 'Простые'], ['feature', 'Преимущества (тёмные)'], ['product', 'Товар (с фото и кнопкой)']], $d['variant'] ?? 'plain')
                . f_select("{$p}[columns]", 'Колонок', [['2', '2'], ['3', '3'], ['4', '4']], (string)($d['columns'] ?? 3))
                . '</div>' . f_checkbox("{$p}[alt]", 'Светлый фон', $d['alt'] ?? false)
                . repeater_html("rep-$idx-items", 'Карточки', $d['items'] ?? [], fn($it, $i) => row_card_item("{$p}[items]", $i, $it));
        case 'panelGrid':
            return repeater_html("rep-$idx-items", 'Панели', $d['items'] ?? [], fn($it, $i) => row_panel("{$p}[items]", $i, $it));
        case 'advantages':
            return f_bi("{$p}[title]", 'Заголовок списка преимуществ', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . repeater_html("rep-$idx-items", 'Преимущества', $d['items'] ?? [], fn($it, $i) => row_advantage("{$p}[items]", $i, $it));
        case 'image':
            return f_image("{$p}[image]", 'Изображение', $d['image'] ?? '')
                . f_bi("{$p}[caption]", 'Подпись (необязательно)', $d['caption_en'] ?? '', $d['caption_ru'] ?? '')
                . f_checkbox("{$p}[alt]", 'Светлый фон', $d['alt'] ?? false);
        case 'logos':
            return f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . repeater_html("rep-$idx-items", 'Логотипы', $d['items'] ?? [], fn($it, $i) => row_logo("{$p}[items]", $i, $it));
        case 'awards':
            return repeater_html("rep-$idx-items", 'Награды', $d['items'] ?? [], fn($it, $i) => row_card_item("{$p}[items]", $i, $it));
        case 'pressClippings':
            return f_bi("{$p}[title]", 'Заголовок', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . repeater_html("rep-$idx-items", 'Упоминания', $d['items'] ?? [], fn($it, $i) => row_clip("{$p}[items]", $i, $it));
        case 'caseStudyGrid':
        case 'articleGrid':
            $kind = $block['type'] === 'caseStudyGrid' ? 'Примеры проектов' : 'Пресса и статьи';
            return f_bi("{$p}[title]", 'Заголовок (необязательно)', $d['title_en'] ?? '', $d['title_ru'] ?? '')
                . f_checkbox("{$p}[alt]", 'Светлый фон', $d['alt'] ?? false)
                . "<p class=\"hint\">Карточки подтягиваются автоматически из раздела «$kind».</p>";
        case 'backLink':
            return f_text("{$p}[href]", 'Ссылка', $d['href'] ?? '')
                . f_text("{$p}[label_en]", 'Текст (EN)', $d['label_en'] ?? '')
                . f_text("{$p}[label_ru]", 'Текст (RU)', $d['label_ru'] ?? '');
        case 'contactBlock':
            return '<p class="hint">Этот блок автоматически использует телефон, email и адрес из раздела «Настройки и контакты», плюс форму заявки.</p>';
        case 'curveGroups':
            return '<p class="hint">Группы и кривые этого раздела редактируются через Raw JSON — самый предсказуемый способ для сложной вложенной структуры.</p>'
                . f_textarea("{$p}[__json]", 'Raw JSON (groups)', json_encode(['groups' => $d['groups'] ?? []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        default:
            return '<p class="hint">Для этого типа блока нет формы — отредактируйте JSON напрямую.</p>'
                . f_textarea("{$p}[__json]", 'Raw JSON', json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}

// Rebuilds one block's `data` array (and its `type`) from $_POST plus
// $_FILES, mirroring block_fields() above field-for-field. $idx is the
// block's own index (into blocks[$idx]); $itemsKey is always "items" for
// every repeater here, kept as a parameter only for readability at the
// call sites below.
function block_from_form(string $type, int $idx): array {
    $raw = $_POST['blocks'][$idx] ?? [];
    $g = fn($k) => $raw[$k] ?? null;
    $items = fn($k) => $raw[$k] ?? [];
    // Resolves one repeater item's image: logical path is
    // blocks[$idx][items][$i][image], matching f_image()'s own
    // with_suffix()/file_key_for() derivation exactly.
    $itemImage = fn($i, $it) => handle_image_upload("blocks[$idx][items][$i][image]", $it['image_current'] ?? '');

    switch ($type) {
        case 'hero':
            $buttons = [];
            foreach (($items('buttons')) as $b) {
                $buttons[] = ['label_en' => $b['label_en'] ?? '', 'label_ru' => $b['label_ru'] ?? '', 'href' => $b['href'] ?? ''];
            }
            return [
                'eyebrow_en' => $g('eyebrow_en'), 'eyebrow_ru' => $g('eyebrow_ru'),
                'title_en' => $g('title_en'), 'title_ru' => $g('title_ru'),
                'body_en' => $g('body_en'), 'body_ru' => $g('body_ru'),
                'image' => handle_image_upload("blocks[$idx][image]", $g('image_current') ?? ''),
                'buttons' => $buttons,
            ];
        case 'text':
            return [
                'eyebrow_en' => $g('eyebrow_en'), 'eyebrow_ru' => $g('eyebrow_ru'),
                'title_en' => $g('title_en'), 'title_ru' => $g('title_ru'),
                'body_en' => $g('body_en'), 'body_ru' => $g('body_ru'),
                'alt' => !empty($g('alt')), 'center' => !empty($g('center')),
            ];
        case 'textImage':
            return [
                'title_en' => $g('title_en'), 'title_ru' => $g('title_ru'),
                'body_en' => $g('body_en'), 'body_ru' => $g('body_ru'),
                'image' => handle_image_upload("blocks[$idx][image]", $g('image_current') ?? ''),
                'image_side' => $g('imageSide') ?: 'right',
                'alt' => !empty($g('alt')),
            ];
        case 'list':
            $out = [];
            foreach ($items('items') as $it) $out[] = ['en' => $it['en'] ?? '', 'ru' => $it['ru'] ?? ''];
            return ['title_en' => $g('title_en'), 'title_ru' => $g('title_ru'), 'style' => $g('style') ?: 'spec', 'items' => $out];
        case 'cards': {
            $out = [];
            foreach ($items('items') as $i => $it) {
                $out[] = [
                    'title_en' => $it['title_en'] ?? '', 'title_ru' => $it['title_ru'] ?? '',
                    'body_en' => $it['body_en'] ?? '', 'body_ru' => $it['body_ru'] ?? '',
                    'image' => $itemImage($i, $it),
                    'link_href' => $it['link_href'] ?? '', 'link_en' => $it['link_en'] ?? '', 'link_ru' => $it['link_ru'] ?? '',
                    'icon' => $it['icon'] ?? '',
                ];
            }
            return [
                'eyebrow_en' => $g('eyebrow_en'), 'eyebrow_ru' => $g('eyebrow_ru'),
                'title_en' => $g('title_en'), 'title_ru' => $g('title_ru'),
                'variant' => $g('variant') ?: 'plain', 'columns' => (int)($g('columns') ?: 3),
                'alt' => !empty($g('alt')), 'items' => $out,
            ];
        }
        case 'panelGrid': {
            $out = [];
            foreach ($items('items') as $it) {
                $out[] = ['title_en' => $it['title_en'] ?? '', 'title_ru' => $it['title_ru'] ?? '', 'body_en' => $it['body_en'] ?? '', 'body_ru' => $it['body_ru'] ?? '', 'wide' => !empty($it['wide']), 'icon' => $it['icon'] ?? ''];
            }
            return ['items' => $out];
        }
        case 'advantages': {
            $out = [];
            foreach ($items('items') as $it) {
                $out[] = ['label_en' => $it['label_en'] ?? '', 'label_ru' => $it['label_ru'] ?? '', 'body_en' => $it['body_en'] ?? '', 'body_ru' => $it['body_ru'] ?? ''];
            }
            return ['title_en' => $g('title_en'), 'title_ru' => $g('title_ru'), 'items' => $out];
        }
        case 'image':
            return ['image' => handle_image_upload("blocks[$idx][image]", $g('image_current') ?? ''), 'caption_en' => $g('caption_en'), 'caption_ru' => $g('caption_ru'), 'alt' => !empty($g('alt'))];
        case 'logos': {
            $out = [];
            foreach ($items('items') as $i => $it) {
                $out[] = ['image' => $itemImage($i, $it), 'alt_en' => $it['alt_en'] ?? '', 'link' => trim($it['link'] ?? '')];
            }
            return ['title_en' => $g('title_en'), 'title_ru' => $g('title_ru'), 'items' => $out];
        }
        case 'awards': {
            $out = [];
            foreach ($items('items') as $i => $it) {
                $out[] = [
                    'title_en' => $it['title_en'] ?? '', 'title_ru' => $it['title_ru'] ?? '',
                    'body_en' => $it['body_en'] ?? '', 'body_ru' => $it['body_ru'] ?? '',
                    'image' => $itemImage($i, $it),
                ];
            }
            return ['items' => $out];
        }
        case 'pressClippings': {
            $out = [];
            foreach ($items('items') as $it) $out[] = ['title_en' => $it['title_en'] ?? '', 'title_ru' => $it['title_ru'] ?? '', 'source' => $it['source'] ?? ''];
            return ['title_en' => $g('title_en'), 'title_ru' => $g('title_ru'), 'items' => $out];
        }
        case 'caseStudyGrid':
        case 'articleGrid':
            return ['title_en' => $g('title_en'), 'title_ru' => $g('title_ru'), 'alt' => !empty($g('alt'))];
        case 'backLink':
            return ['href' => $g('href'), 'label_en' => $g('label_en'), 'label_ru' => $g('label_ru')];
        case 'contactBlock':
            return [];
        case 'curveGroups': {
            $parsed = json_decode($g('__json') ?? '', true);
            return ['groups' => $parsed['groups'] ?? []];
        }
        default:
            $parsed = json_decode($g('__json') ?? '', true);
            return is_array($parsed) ? $parsed : [];
    }
}

function default_block_for(string $type): array {
    $base = [];
    if (in_array($type, ['list', 'panelGrid', 'advantages', 'logos', 'awards', 'pressClippings', 'cards'], true)) {
        $base['items'] = [];
    }
    if ($type === 'hero') $base['buttons'] = [];
    if ($type === 'curveGroups') $base['groups'] = [];
    return $base;
}

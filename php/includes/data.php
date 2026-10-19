<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function get_settings(): array {
    $row = db()->query('SELECT * FROM settings WHERE id = 1')->fetch();
    return $row ?: [];
}

function get_nav(): array {
    $rows = db()->query('SELECT * FROM nav_items ORDER BY parent_id IS NOT NULL, sort_order')->fetchAll();
    $byParent = [];
    foreach ($rows as $r) {
        $byParent[$r['parent_id'] === null ? 0 : $r['parent_id']][] = $r;
    }
    $build = function ($r) use (&$build, $byParent) {
        $item = ['id' => $r['id'], 'label_en' => $r['label_en'], 'label_ru' => $r['label_ru'], 'href' => $r['href']];
        if (!empty($byParent[$r['id']])) {
            $item['children'] = array_map($build, $byParent[$r['id']]);
        }
        return $item;
    };
    return array_map($build, $byParent[0] ?? []);
}

function get_page(string $slug): ?array {
    $stmt = db()->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
    if (!$page) return null;
    $page['crumbs'] = json_in($page['crumbs_json'], []);
    $stmt = db()->prepare('SELECT * FROM blocks WHERE page_id = ? ORDER BY sort_order');
    $stmt->execute([$page['id']]);
    $page['blocks'] = array_map(function ($b) {
        return ['type' => $b['type'], 'data' => json_in($b['data'], [])];
    }, $stmt->fetchAll());
    return $page;
}

function get_all_pages(): array {
    return db()->query('SELECT id, slug, title_en, title_ru FROM pages ORDER BY id')->fetchAll();
}

function get_case_studies(): array {
    $rows = db()->query('SELECT * FROM case_studies ORDER BY sort_order, id')->fetchAll();
    $stmt = db()->prepare('SELECT * FROM case_study_solutions WHERE case_study_id = ? ORDER BY sort_order');
    foreach ($rows as &$r) {
        $stmt->execute([$r['id']]);
        $r['solution'] = array_map(fn($s) => ['en' => $s['text_en'], 'ru' => $s['text_ru']], $stmt->fetchAll());
    }
    return $rows;
}

function get_case_study(string $slug): ?array {
    $stmt = db()->prepare('SELECT * FROM case_studies WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $cs = $stmt->fetch();
    if (!$cs) return null;
    $stmt = db()->prepare('SELECT * FROM case_study_solutions WHERE case_study_id = ? ORDER BY sort_order');
    $stmt->execute([$cs['id']]);
    $cs['solution'] = array_map(fn($s) => ['en' => $s['text_en'], 'ru' => $s['text_ru']], $stmt->fetchAll());
    return $cs;
}

function get_articles(): array {
    return db()->query('SELECT * FROM articles ORDER BY sort_order, id')->fetchAll();
}

function get_article(string $slug): ?array {
    $stmt = db()->prepare('SELECT * FROM articles WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $a = $stmt->fetch();
    return $a ?: null;
}

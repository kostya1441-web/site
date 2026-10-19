<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/render.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$slug = trim($path, '/');

$settings = get_settings();
$nav = get_nav();

function render_full_page(array $page, array $ctx, array $settings, array $nav): string {
    $body = '';
    if (($page['show_head'] ?? 1)) {
        $trail = !empty($page['crumbs'])
            ? array_map(fn($c) => ['en' => $c['en'], 'ru' => $c['ru'], 'href' => $c['href'] ?? null], $page['crumbs'])
            : [['en' => 'Home', 'ru' => 'Главная', 'href' => '/'], ['en' => $page['title_en'], 'ru' => $page['title_ru'], 'href' => null]];
        $body .= page_head($page['eyebrow_en'] ?? '', $page['eyebrow_ru'] ?? '', $page['title_en'], $page['title_ru'], $trail);
    }
    foreach ($page['blocks'] as $block) {
        $body .= render_block($block, $ctx);
    }
    if ($page['show_cta'] ?? 1) {
        $body .= cta_strip($settings);
    }
    return layout($settings, $nav, $page['seo_title_en'] ?? $page['title_en'], $body);
}

// --- Case studies ---
if ($slug === 'case-studies') {
    $caseStudies = get_case_studies();
    $page = get_page('case-studies');
    if (!$page) { http_response_code(404); echo 'Page not found'; exit; }
    echo render_full_page($page, ['caseStudies' => $caseStudies, 'settings' => $settings], $settings, $nav);
    exit;
}
if (preg_match('#^case-studies/([a-z0-9-]+)$#', $slug, $m)) {
    $cs = get_case_study($m[1]);
    if (!$cs) { http_response_code(404); echo 'Not found'; exit; }
    $challengeBlock = '';
    if (!empty($cs['challenge_en'])) {
        $challengeBlock = '<div class="panel"><h3>' . bi('Challenge', 'Задача') . '</h3>'
            . bi_block('<p>' . esc($cs['challenge_en']) . '</p>', '<p>' . esc($cs['challenge_ru']) . '</p>') . '</div>';
    }
    $body = page_head('Case Study', 'Пример проекта', $cs['title_en'], $cs['title_ru'], [
        ['en' => 'Home', 'ru' => 'Главная', 'href' => '/'],
        ['en' => 'Case Studies', 'ru' => 'Примеры проектов', 'href' => '/case-studies/'],
        ['en' => $cs['title_en'], 'ru' => $cs['title_ru'], 'href' => null],
    ]);
    $body .= '<section><div class="container" style="max-width:900px">'
        . '<a class="back-link" href="/case-studies/">' . bi('← Back to case studies', '← Назад к примерам проектов') . '</a>'
        . '<div class="img-frame" style="margin-bottom:26px"><img src="' . esc($cs['image']) . '" alt=""></div>'
        . $challengeBlock
        . '<div class="panel"><h3>' . bi('Solution', 'Решение') . '</h3>' . ul_list($cs['solution']) . '</div>'
        . '<a class="btn" href="/contact/">' . bi('Contact us', 'Связаться с нами') . '</a>'
        . '</div></section>';
    echo layout($settings, $nav, $cs['title_en'], $body);
    exit;
}

// --- Press releases / articles ---
if ($slug === 'press-releases') {
    $articles = get_articles();
    $page = get_page('press-releases');
    if (!$page) { http_response_code(404); echo 'Page not found'; exit; }
    echo render_full_page($page, ['articles' => $articles, 'settings' => $settings], $settings, $nav);
    exit;
}
if (preg_match('#^press-releases/([a-z0-9-]+)$#', $slug, $m)) {
    $a = get_article($m[1]);
    if (!$a) { http_response_code(404); echo 'Not found'; exit; }
    $body = page_head('Press Release', 'Пресс-релиз', $a['title_en'], $a['title_ru'], [
        ['en' => 'Home', 'ru' => 'Главная', 'href' => '/'],
        ['en' => 'Press Releases', 'ru' => 'Пресс-релизы', 'href' => '/press-releases/'],
        ['en' => $a['title_en'], 'ru' => $a['title_ru'], 'href' => null],
    ]);
    $image = !empty($a['image']) ? '<div class="img-frame" style="margin-bottom:28px"><img src="' . esc($a['image']) . '" alt=""></div>' : '';
    $body .= '<section><div class="container" style="max-width:860px">'
        . '<a class="back-link" href="/press-releases/">' . bi('← Back to press releases', '← Назад к пресс-релизам') . '</a>'
        . $image
        . '<p class="meta" style="color:var(--steel);font-weight:700">' . bi($a['date_en'], $a['date_ru']) . '</p>'
        . bi_block($a['body_en'], $a['body_ru'])
        . '</div></section>' . cta_strip($settings);
    echo layout($settings, $nav, $a['title_en'], $body);
    exit;
}

// --- Generic content page (covers home for slug === '') ---
$page = get_page($slug);
if ($page) {
    echo render_full_page($page, ['settings' => $settings], $settings, $nav);
    exit;
}

http_response_code(404);
$body = '<section><div class="container" style="padding:80px 0;text-align:center"><h1>404</h1><p>'
    . bi('Page not found.', 'Страница не найдена.') . '</p><a class="btn" href="/">' . bi('Back home', 'На главную') . '</a></div></section>';
echo layout($settings, $nav, '404', $body);

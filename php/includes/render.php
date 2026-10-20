<?php
require_once __DIR__ . '/helpers.php';

function render_nav(array $nav): string {
    $out = '';
    foreach ($nav as $item) {
        $label = bi($item['label_en'], $item['label_ru']);
        $href = esc($item['href']);
        if (!empty($item['children'])) {
            $kids = '';
            foreach ($item['children'] as $c) {
                $kids .= '<li><a href="' . esc($c['href']) . '">' . bi($c['label_en'], $c['label_ru']) . '</a></li>';
            }
            $out .= "<li><a href=\"$href\" class=\"nav-top\">$label</a><ul class=\"dropdown\">$kids</ul></li>";
        } else {
            $out .= "<li><a href=\"$href\">$label</a></li>";
        }
    }
    return $out;
}

function crumbs_html(array $trail): string {
    $parts = array_map(function ($c) {
        if (!empty($c['href'])) {
            return '<a href="' . esc($c['href']) . '">' . bi($c['en'], $c['ru']) . '</a>';
        }
        return bi($c['en'], $c['ru']);
    }, $trail);
    return '<div class="breadcrumb">' . implode(' / ', $parts) . '</div>';
}

function page_head(string $eyebrowEn, string $eyebrowRu, string $titleEn, string $titleRu, array $crumbTrail): string {
    return '
<section class="page-head">
  <div class="container">
    ' . crumbs_html($crumbTrail) . '
    <p class="eyebrow" style="color:var(--red-light)">' . bi($eyebrowEn, $eyebrowRu) . '</p>
    <h1>' . bi($titleEn, $titleRu) . '</h1>
  </div>
</section>';
}

// Decorative, purely cosmetic underwater-bubble backdrop for the homepage
// hero — a handful of soft circles drifting upward on a loop. Negative
// animation-delay staggers them so they don't all start at the bottom at
// once. aria-hidden since it carries no content.
function hero_fx_layer(): string {
    // [left%, size px, duration s, negative delay s]
    $bubbles = [
        [4, 10, 13, 2], [10, 16, 17, 6], [17, 8, 11, 1], [24, 20, 21, 9],
        [31, 12, 15, 4], [39, 9, 12, 8], [47, 18, 19, 3], [55, 11, 14, 10],
        [62, 15, 16, 5], [70, 9, 13, 0], [78, 19, 20, 7], [86, 12, 17, 2],
        [93, 10, 14, 11],
    ];
    $html = '<div class="hero-fx" aria-hidden="true">';
    foreach ($bubbles as [$left, $size, $dur, $delay]) {
        $html .= '<span class="bubble" style="left:' . $left . '%;width:' . $size . 'px;height:' . $size
            . 'px;animation-duration:' . $dur . 's;animation-delay:-' . $delay . 's;"></span>';
    }
    $html .= '</div>';
    return $html;
}

function cta_strip(array $settings): string {
    return '
<section class="tight">
  <div class="container">
    <div class="cta-strip">
      <div>
        <h3>' . bi($settings['cta_title_en'], $settings['cta_title_ru']) . '</h3>
        ' . bi_block('<p>' . esc($settings['cta_body_en']) . '</p>', '<p>' . esc($settings['cta_body_ru']) . '</p>') . '
      </div>
      <a class="btn" href="/contact/">' . bi('Contact Us', 'Связаться с нами') . '</a>
    </div>
  </div>
</section>';
}

// ---------------------------------------------------------------- blocks --

function render_block(array $block, array $ctx): string {
    $d = $block['data'] ?? [];
    switch ($block['type']) {
        case 'hero':
            $buttons = '';
            foreach (($d['buttons'] ?? []) as $i => $b) {
                $cls = $i === 0 ? '' : 'ghost';
                $buttons .= '<a class="btn ' . $cls . '" href="' . esc($b['href']) . '">' . bi($b['label_en'], $b['label_ru']) . '</a>';
            }
            $eyebrow = !empty($d['eyebrow_en']) ? '<p class="eyebrow" style="color:var(--red-light)">' . bi($d['eyebrow_en'], $d['eyebrow_ru']) . '</p>' : '';
            $image = !empty($d['image']) ? '<div class="hero-art"><img src="' . esc($d['image']) . '" alt=""></div>' : '';
            return '
<section class="hero">
  ' . hero_fx_layer() . '
  <div class="container">
    <div>
      ' . $eyebrow . '
      <h1>' . bi($d['title_en'] ?? '', $d['title_ru'] ?? '') . '</h1>
      ' . bi_block('<p>' . ($d['body_en'] ?? '') . '</p>', '<p>' . ($d['body_ru'] ?? '') . '</p>') . '
      <div style="display:flex;gap:14px;flex-wrap:wrap">' . $buttons . '</div>
    </div>
    ' . $image . '
  </div>
</section>';

        case 'text':
            $alt = !empty($d['alt']) ? 'alt' : '';
            $tight = !empty($d['tight']) ? 'tight' : '';
            $center = !empty($d['center']) ? 'margin:0 auto;text-align:center' : '';
            $eyebrow = !empty($d['eyebrow_en']) ? '<p class="eyebrow">' . bi($d['eyebrow_en'], $d['eyebrow_ru']) . '</p>' : '';
            $title = !empty($d['title_en']) ? '<h2 class="section-title">' . bi($d['title_en'], $d['title_ru']) . '</h2>' : '';
            $maxw = !empty($d['max_width']) ? 'max-width:' . esc($d['max_width']) : '';
            return '
<section class="' . $alt . ' ' . $tight . '">
  <div class="container" style="' . $maxw . '">
    ' . $eyebrow . $title . '
    ' . bi_block("<div class=\"lede\" style=\"$center\">" . ($d['body_en'] ?? '') . '</div>', "<div class=\"lede\" style=\"$center\">" . ($d['body_ru'] ?? '') . '</div>') . '
  </div>
</section>';

        case 'textImage':
            $alt = !empty($d['alt']) ? 'alt' : '';
            $image = '<div class="img-frame"><img src="' . esc($d['image'] ?? '') . '" alt=""></div>';
            if (!empty($d['title_en'])) {
                $textSide = '<div><h2 class="section-title">' . bi($d['title_en'], $d['title_ru']) . '</h2>'
                    . bi_block('<div>' . ($d['body_en'] ?? '') . '</div>', '<div>' . ($d['body_ru'] ?? '') . '</div>') . '</div>';
            } else {
                $textSide = bi_block("<div class='lede'>" . ($d['body_en'] ?? '') . '</div>', "<div class='lede'>" . ($d['body_ru'] ?? '') . '</div>');
            }
            $cols = ($d['image_side'] ?? 'right') === 'left' ? "$image$textSide" : "$textSide$image";
            return '
<section class="' . $alt . '">
  <div class="container"><div class="two-col">' . $cols . '</div></div>
</section>';

        case 'list':
            $title = !empty($d['title_en']) ? '<h2 class="section-title">' . bi($d['title_en'], $d['title_ru']) . '</h2>' : '';
            $cls = ($d['style'] ?? 'spec') === 'tag' ? 'tag-list' : 'spec-list';
            return "
<section>
  <div class=\"container\">
    $title
    " . ul_list($d['items'] ?? [], $cls) . '
  </div>
</section>';

        case 'cards': {
            $cols = $d['columns'] ?? 3;
            $variant = $d['variant'] ?? 'plain';
            $cards = '';
            foreach (($d['items'] ?? []) as $i => $it) {
                if ($variant === 'feature') {
                    $num = !empty($it['num']) ? '<div class="num">' . esc($it['num']) . '</div>' : '';
                    $cards .= '<div class="feature-card">' . $num . '<h3>' . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3>'
                        . bi_block('<p>' . ($it['body_en'] ?? '') . '</p>', '<p>' . ($it['body_ru'] ?? '') . '</p>') . '</div>';
                } elseif ($variant === 'product') {
                    $thumb = !empty($it['image']) ? '<div class="thumb"><img src="' . esc($it['image']) . '" alt=""></div>' : '';
                    $link = !empty($it['link_href']) ? '<a class="btn dark" href="' . esc($it['link_href']) . '">' . bi($it['link_en'] ?? 'Find out more', $it['link_ru'] ?? 'Подробнее') . '</a>' : '';
                    $cards .= '<div class="product-card">' . $thumb . '<div class="body"><h3>' . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3>'
                        . bi_block('<p>' . ($it['body_en'] ?? '') . '</p>', '<p>' . ($it['body_ru'] ?? '') . '</p>') . $link . '</div></div>';
                } else {
                    $cards .= '<div class="card"><h3>' . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3>'
                        . bi_block('<p>' . ($it['body_en'] ?? '') . '</p>', '<p>' . ($it['body_ru'] ?? '') . '</p>') . '</div>';
                }
            }
            $alt = !empty($d['alt']) ? 'alt' : '';
            $eyebrow = !empty($d['eyebrow_en']) ? '<p class="eyebrow">' . bi($d['eyebrow_en'], $d['eyebrow_ru']) . '</p>' : '';
            $title = !empty($d['title_en']) ? '<h2 class="section-title">' . bi($d['title_en'], $d['title_ru']) . '</h2>' : '';
            return "
<section class=\"$alt\">
  <div class=\"container\">
    $eyebrow$title
    <div class=\"grid cols-$cols\" style=\"margin-top:28px\">$cards</div>
  </div>
</section>";
        }

        case 'panelGrid': {
            $panels = '';
            foreach (($d['items'] ?? []) as $it) {
                $wide = !empty($it['wide']) ? 'grid-column:1/-1' : '';
                $panels .= "<div class=\"panel\" style=\"$wide\"><h3>" . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3>'
                    . bi_block('<div>' . ($it['body_en'] ?? '') . '</div>', '<div>' . ($it['body_ru'] ?? '') . '</div>') . '</div>';
            }
            $maxw = esc($d['max_width'] ?? '1020px');
            return "
<section>
  <div class=\"container\" style=\"max-width:$maxw\">
    <div class=\"grid cols-2\" style=\"margin-top:12px\">$panels</div>
  </div>
</section>";
        }

        case 'advantages': {
            $liEn = ''; $liRu = '';
            foreach (($d['items'] ?? []) as $it) {
                $liEn .= '<li><strong>' . esc($it['label_en'] ?? '') . ':</strong> ' . esc($it['body_en'] ?? '') . '</li>';
                $liRu .= '<li><strong>' . esc($it['label_ru'] ?? '') . ':</strong> ' . esc($it['body_ru'] ?? '') . '</li>';
            }
            return '
<section>
  <div class="container" style="max-width:1020px">
    <div class="panel">
      <h3>' . bi('The Hippo Twin-Volute Discharge Design', 'Двухспиральная конструкция нагнетания Hippo') . '</h3>
      <p>' . bi('Balanced hydraulic forces totally eliminate shaft deflection during partial loading.', 'Сбалансированные гидравлические силы полностью исключают прогиб вала при частичной загрузке.') . '</p>
      <h4>' . bi($d['title_en'] ?? '', $d['title_ru'] ?? '') . '</h4>
      ' . bi_block("<ul class=\"spec-list\">$liEn</ul>", "<ul class=\"spec-list\">$liRu</ul>", 'div') . '
    </div>
  </div>
</section>';
        }

        case 'image':
            $alt = !empty($d['alt']) ? 'alt' : '';
            $caption = !empty($d['caption_en']) ? '<figcaption>' . bi($d['caption_en'], $d['caption_ru']) . '</figcaption>' : '';
            return "
<section class=\"$alt tight\">
  <div class=\"container\">
    <figure class=\"img-frame\"><img src=\"" . esc($d['image'] ?? '') . "\" alt=\"\">$caption</figure>
  </div>
</section>";

        case 'logos': {
            $imgs = '';
            foreach (($d['items'] ?? []) as $it) {
                $imgs .= '<img src="' . esc($it['image'] ?? '') . '" alt="' . esc($it['alt_en'] ?? '') . '">';
            }
            $title = !empty($d['title_en']) ? '<p class="eyebrow" style="text-align:center">' . bi($d['title_en'], $d['title_ru']) . '</p>' : '';
            return "
<section class=\"alt tight\">
  <div class=\"container\">
    $title
    <div class=\"assoc-row\">$imgs</div>
  </div>
</section>";
        }

        case 'awards': {
            $cards = '';
            foreach (($d['items'] ?? []) as $it) {
                $cards .= '<div class="award-card"><img src="' . esc($it['image'] ?? '') . '" alt=""><div><h3>' . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3>'
                    . bi_block('<p>' . ($it['body_en'] ?? '') . '</p>', '<p>' . ($it['body_ru'] ?? '') . '</p>') . '</div></div>';
            }
            return "
<section>
  <div class=\"container\"><div class=\"grid cols-2\">$cards</div></div>
</section>";
        }

        case 'caseStudyGrid': {
            $cards = '';
            foreach (($ctx['caseStudies'] ?? []) as $c) {
                $cards .= '<a class="case-card" href="/case-studies/' . esc($c['slug']) . '/"><div class="thumb"><img src="' . esc($c['image']) . '" alt=""></div>'
                    . '<div class="body"><h3>' . bi($c['title_en'], $c['title_ru']) . '</h3><span>' . bi('Learn more →', 'Подробнее →') . '</span></div></a>';
            }
            $alt = !empty($d['alt']) ? 'alt' : '';
            $title = !empty($d['title_en']) ? '<h2 class="section-title">' . bi($d['title_en'], $d['title_ru']) . '</h2>' : '';
            return "
<section class=\"$alt\">
  <div class=\"container\">
    $title
    <div class=\"grid cols-3\" style=\"margin-top:22px\">$cards</div>
  </div>
</section>";
        }

        case 'articleGrid': {
            $cards = '';
            foreach (($ctx['articles'] ?? []) as $a) {
                $cards .= '<a class="article-card" href="/press-releases/' . esc($a['slug']) . '/" style="text-decoration:none;color:inherit">'
                    . '<div class="thumb"><img src="' . esc($a['image']) . '" alt=""></div><div><div class="meta">' . bi($a['date_en'], $a['date_ru']) . '</div>'
                    . '<h3>' . bi($a['title_en'], $a['title_ru']) . '</h3>'
                    . bi_block('<p>' . ($a['teaser_en'] ?? '') . '</p>', '<p>' . ($a['teaser_ru'] ?? '') . '</p>')
                    . '<span class="btn dark" style="margin-top:8px">' . bi('Read more', 'Читать далее') . '</span></div></a>';
            }
            return "<section><div class=\"container\"><div class=\"grid cols-1\" style=\"gap:20px\">$cards</div></div></section>";
        }

        case 'pressClippings': {
            $items = '';
            foreach (($d['items'] ?? []) as $it) {
                $items .= '<li><span>' . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</span><span class="src">' . esc($it['source'] ?? '') . '</span></li>';
            }
            return '
<section>
  <div class="container">
    <h2 class="section-title">' . bi($d['title_en'] ?? '', $d['title_ru'] ?? '') . "</h2>
    <ul class=\"press-clip-list\">$items</ul>
  </div>
</section>";
        }

        case 'curveGroups': {
            $groupsHtml = '';
            foreach (($d['groups'] ?? []) as $g) {
                $cards = '';
                foreach (($g['items'] ?? []) as $it) {
                    $cards .= '<div class="card" style="padding:0;overflow:hidden"><div class="img-frame" style="border:none;border-radius:0"><img src="'
                        . esc($it['image'] ?? '') . '" alt="" loading="lazy"></div><div style="padding:16px 18px"><h3 style="font-size:0.95rem;margin:0">'
                        . bi($it['title_en'] ?? '', $it['title_ru'] ?? '') . '</h3></div></div>';
                }
                $groupsHtml .= '<h2 class="section-title" style="margin-top:40px">' . bi($g['title_en'] ?? '', $g['title_ru'] ?? '') . "</h2><div class=\"grid cols-3\">$cards</div>";
            }
            return "<section><div class=\"container\">$groupsHtml</div></section>";
        }

        case 'contactBlock': {
            $s = $ctx['settings'];
            $phoneHref = preg_replace('/[^\d+]/', '', $s['phone']);
            $notice = $ctx['formNotice'] ?? '';
            return '
<section>
  <div class="container">
    <div class="two-col">
      <div>
        <h2 class="section-title">' . bi('Contact us', 'Свяжитесь с нами') . '</h2>
        <div class="contact-block">
          <div class="item"><span class="ic">&#9679;</span><div><strong>' . esc($s['company_name']) . '</strong><br>' . bi($s['address_en'], $s['address_ru']) . '</div></div>
          <div class="item"><span class="ic">&#9742;</span><div><a href="tel:' . esc($phoneHref) . '">' . esc($s['phone']) . '</a></div></div>
          <div class="item"><span class="ic">&#9993;</span><div><a href="mailto:' . esc($s['email']) . '">' . esc($s['email']) . '</a></div></div>
        </div>
      </div>
      <div class="panel">
        <h3>' . bi('Request a quote', 'Запросить коммерческое предложение') . "</h3>
        $notice
        <form class=\"quote-form\" method=\"post\" action=\"/contact-submit.php\">
          <div><label>" . bi('Name and Surname', 'Имя и фамилия') . '</label><input type="text" name="name" required></div>
          <div><label>' . bi('Email Address', 'Электронная почта') . '</label><input type="email" name="email" required></div>
          <div><label>' . bi('Contact Number', 'Контактный телефон') . '</label><input type="tel" name="phone"></div>
          <div><label>' . bi('Company Name', 'Название компании') . '</label><input type="text" name="company"></div>
          <div><label>' . bi('Message', 'Сообщение') . '</label><textarea name="message" required></textarea></div>
          <button type="submit" class="btn">' . bi('Submit', 'Отправить') . '</button>
        </form>
      </div>
    </div>
  </div>
</section>';
        }

        case 'backLink':
            return '<div class="container" style="padding-top:40px;max-width:' . esc($d['max_width'] ?? '900px') . '"><a class="back-link" href="'
                . esc($d['href'] ?? '') . '">' . bi('← ' . ($d['label_en'] ?? ''), '← ' . ($d['label_ru'] ?? '')) . '</a></div>';

        default:
            return '';
    }
}

function layout(array $settings, array $nav, string $titleEn, string $bodyHtml): string {
    $phoneHref = preg_replace('/[^\d+]/', '', $settings['phone']);
    $logo = esc($settings['logo'] ?: '/assets/img/logo.svg');
    return '<!doctype html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . esc($titleEn) . ' | ' . esc($settings['company_name']) . '</title>
<meta name="description" content="' . esc($settings['meta_description']) . '">
<link rel="icon" href="' . $logo . '">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
<script>
(function(){try{var l=localStorage.getItem(\'hazleton-lang\');if(l===\'ru\'){document.documentElement.setAttribute(\'data-lang\',\'ru\');document.documentElement.setAttribute(\'lang\',\'ru\');}}catch(e){}})();
</script>
</head>
<body>
<div class="topbar">
  <div class="container">
    <div class="contacts">
      <a href="tel:' . esc($phoneHref) . '">T: ' . esc($settings['phone']) . '</a>
      <a href="mailto:' . esc($settings['email']) . '">E: ' . esc($settings['email']) . '</a>
    </div>
    <div class="lang-switch">
      <button type="button" data-lang-btn="en">EN</button>
      <button type="button" data-lang-btn="ru">RU</button>
    </div>
  </div>
</div>
<header class="site-header">
  <div class="container nav-row">
    <a href="/" class="brand">
      <img src="' . $logo . '" alt="' . esc($settings['company_name']) . ' logo">
    </a>
    <nav class="main-nav"><ul>' . render_nav($nav) . '</ul></nav>
    <div class="nav-actions">
      <a class="btn-quote" href="/contact/">' . bi('Request a Quote', 'Запросить КП') . '</a>
      <button class="nav-toggle" aria-label="Menu">&#9776;</button>
    </div>
  </div>
</header>
' . $bodyHtml . '
<footer class="site-footer">
  <div class="container">
    <div class="grid">
      <div>
        <div class="brand" style="color:#fff"><img src="' . $logo . '" alt="" style="height:46px"></div>
        ' . bi_block('<p class=\'tag\'>' . esc($settings['footer_tag_en']) . '</p>', '<p class=\'tag\'>' . esc($settings['footer_tag_ru']) . '</p>') . '
      </div>
      <div>
        <h4>' . bi('Company', 'Компания') . '</h4>
        <ul>
          <li><a href="/history/">' . bi('History', 'История') . '</a></li>
          <li><a href="/awards/">' . bi('Awards', 'Награды') . '</a></li>
          <li><a href="/press-releases/">' . bi('Press & Articles', 'Пресса и статьи') . '</a></li>
        </ul>
      </div>
      <div>
        <h4>' . bi('Products', 'Продукция') . '</h4>
        <ul>
          <li><a href="/hippo-range/">' . bi('Hippo Range', 'Линейка Hippo') . '</a></li>
          <li><a href="/pump-systems/">' . bi('Pump Systems', 'Насосные системы') . '</a></li>
          <li><a href="/pump-curves/">' . bi('Pump Curves', 'Напорные характеристики') . '</a></li>
          <li><a href="/case-studies/">' . bi('Case Studies', 'Примеры проектов') . '</a></li>
        </ul>
      </div>
      <div>
        <h4>' . bi('Contact', 'Контакты') . '</h4>
        <ul>
          <li>' . bi($settings['address_en'], $settings['address_ru']) . '</li>
          <li><a href="tel:' . esc($phoneHref) . '">' . esc($settings['phone']) . '</a></li>
          <li><a href="mailto:' . esc($settings['email']) . '">' . esc($settings['email']) . '</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>' . bi('Copyright © ' . $settings['year'] . ' ' . $settings['company_name'], '© ' . $settings['year'] . ' ' . $settings['company_name'] . '. Все права защищены') . '</span>
      <span>' . bi('Powered by your own editable site', 'Работает на вашем редактируемом сайте') . '</span>
    </div>
  </div>
</footer>
<script src="/assets/js/i18n.js"></script>
<script src="/assets/js/lightbox.js"></script>
</body>
</html>';
}

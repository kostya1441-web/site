<?php
declare(strict_types=1);

require_once __DIR__ . '/content.php';
require_once __DIR__ . '/svg.php';

function catalog_href(?string $categorySlug = null, ?string $productSlug = null): string
{
    $query = [];
    if ($categorySlug) {
        $query['cat'] = $categorySlug;
    }
    if ($productSlug) {
        $query['prod'] = $productSlug;
    }
    return 'catalog.php' . ($query ? '?' . http_build_query($query) : '');
}

function render_head(array $content, string $title, string $description): void
{
    $s = $content['settings'];
    ?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%230b0d0f'/%3E%3Cg fill='none' stroke='%237ed321' stroke-width='2.4'%3E%3Ccircle cx='16' cy='16' r='7'/%3E%3Ccircle cx='16' cy='16' r='2.6'/%3E%3Cpath d='M16 4v3M16 25v3M4 16h3M25 16h3M7.5 7.5l2.1 2.1M22.4 22.4l2.1 2.1M24.5 7.5l-2.1 2.1M9.6 22.4l-2.1 2.1' stroke-linecap='round'/%3E%3C/g%3E%3C/svg%3E">
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<div id="site-root">
  <div id="sr-page">
<?php
}

function render_header(array $content, string $active): void
{
    $s = $content['settings'];
    $logo = trim((string) $s['logo']);
    $showText = $logo === '' || $s['logoShowText'] === '1';
    $height = max(24, min(110, (int) $s['logoHeight']));
    $padded = $logo !== '' && $s['logoPad'] === '1';
    $home = $active === 'home';
    ?>
    <header id="sr-header">
      <div class="bar">
        <div class="brand">
          <a class="mark<?= $padded ? ' padded' : '' ?>" id="sr-mark" href="index.php" aria-label="На главную"
             style="--logo-h:<?= $height ?>px">
            <?php if ($logo !== ''): ?>
              <img class="logo-img" src="<?= e($logo) ?>" alt="<?= e($s['brandName']) ?>">
            <?php else: ?>
              <?= emblem_svg('h') ?>
            <?php endif; ?>
          </a>
          <div id="sr-brandtext"<?= $showText ? '' : ' style="display:none"' ?>>
            <a class="brand-name wordmark" id="sr-brandname" href="index.php"><?= e($s['brandName']) ?></a>
            <div class="brand-tag" id="sr-tagline"><?= e($s['tagline']) ?></div>
          </div>
        </div>
        <div class="contacts">
          <span>☎ <b id="sr-phone"><?= e($s['phone']) ?></b></span>
          <span id="sr-email"><?= e($s['email']) ?></span>
          <span id="sr-city"><?= e($s['city']) ?></span>
        </div>
        <a class="btn" href="<?= $home ? '#sr-contact' : 'index.php#sr-contact' ?>" style="padding:11px 20px; font-size:13px;">Контакты</a>
      </div>
      <nav id="sr-nav"><div class="wrap">
        <a href="index.php"<?= $home ? ' class="active"' : '' ?>>Главная</a>
        <a href="index.php#sr-about">О компании</a>
        <button type="button" id="sr-mega-btn" class="navbtn<?= $active === 'catalog' ? ' active' : '' ?>"
                aria-expanded="false" aria-controls="sr-mega">Каталог<span class="chev" aria-hidden="true">▾</span></button>
        <a href="index.php#sr-features">Услуги</a>
        <a href="<?= $home ? '#sr-contact' : 'index.php#sr-contact' ?>">Контакты</a>
      </div></nav>
      <?php render_mega($content); ?>
    </header>
<?php
}

/** Выпадающее меню каталога: категории и позиции отрисованы сразу, без запросов. */
function render_mega(array $content): void
{
    $categories = $content['categories'];
    $first = $categories[0]['id'] ?? 0;
    ?>
      <div class="mega" id="sr-mega">
        <div class="wrap mega-inner">
          <div class="mega-side">
            <div class="mega-title" id="sr-mega-title"><?= e($content['settings']['megaTitle']) ?></div>
            <a class="mega-all" href="catalog.php">Перейти на страницу <span aria-hidden="true">›</span></a>
          </div>
          <div class="mega-col mega-cats" id="sr-mega-cats">
            <?php if (!$categories): ?>
              <div class="mega-empty">Категории пока не добавлены.</div>
            <?php endif; ?>
            <?php foreach ($categories as $category): ?>
              <a class="mega-link<?= $category['id'] === $first ? ' active' : '' ?>"
                 href="<?= e(catalog_href($category['slug'])) ?>" data-mega-cat="<?= (int) $category['id'] ?>">
                <span><?= e($category['name']) ?></span>
                <span class="cnt3"><?= count(products_of($content, (int) $category['id'])) ?></span>
                <span class="ch" aria-hidden="true">›</span>
              </a>
            <?php endforeach; ?>
          </div>
          <div class="mega-col mega-prods" id="sr-mega-prods">
            <?php foreach ($categories as $category): ?>
              <div class="mega-group<?= $category['id'] === $first ? ' active' : '' ?>" data-mega-group="<?= (int) $category['id'] ?>">
                <div class="mega-head">
                  <span><?= e($category['name']) ?></span>
                  <a href="<?= e(catalog_href($category['slug'])) ?>">Вся категория ›</a>
                </div>
                <?php $items = products_of($content, (int) $category['id']); ?>
                <?php if (!$items): ?>
                  <div class="mega-empty">В этой категории пока нет позиций.</div>
                <?php endif; ?>
                <?php foreach ($items as $product): ?>
                  <a class="mega-link" href="<?= e(catalog_href($category['slug'], $product['slug'])) ?>"
                     data-prod-slug="<?= e($product['slug']) ?>">
                    <span class="mega-prodname">
                      <?php if ($product['code'] !== ''): ?><small><?= e($product['code']) ?></small><?php endif; ?>
                      <span><?= e($product['name']) ?></span>
                    </span>
                    <span class="ch" aria-hidden="true">›</span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
<?php
}

/** Характеристики: строка «параметр: значение» разбивается на две колонки. */
function spec_rows(string $specs): array
{
    $rows = [];
    foreach (lines($specs) as $line) {
        $pos = mb_strpos($line, ':');
        if ($pos !== false && $pos > 0 && $pos < mb_strlen($line) - 1) {
            $rows[] = ['k' => trim(mb_substr($line, 0, $pos)), 'v' => trim(mb_substr($line, $pos + 1))];
        } else {
            $rows[] = ['k' => '', 'v' => $line];
        }
    }
    return $rows;
}

/** Полноэкранная карточка позиции (её же собирает site.js при клике). */
function render_product_view(array $content, array $product): void
{
    $s = $content['settings'];
    $category = null;
    foreach ($content['categories'] as $row) {
        if ((int) $row['id'] === (int) $product['category_id']) {
            $category = $row;
            break;
        }
    }
    $images = $product['images'];
    $rows = spec_rows((string) $product['specs']);
    ?>
    <div class="prod-top">
      <a class="prod-back" href="<?= e(catalog_href($category['slug'] ?? null)) ?>">‹ <?= e($category['name'] ?? 'В каталог') ?></a>
      <button type="button" class="prod-close" id="sr-prod-close" aria-label="Закрыть">×</button>
    </div>
    <div class="wrap prod-wrap">
      <div class="prod-gallery">
        <div class="prod-photo" id="sr-prod-photo">
          <?php if ($images): ?>
            <img src="<?= e($images[0]) ?>" alt="<?= e($product['name']) ?>">
            <?php if (count($images) > 1): ?><span class="prod-counter">1 / <?= count($images) ?></span><?php endif; ?>
          <?php else: ?>
            <div class="ph-empty"><?= gauge_svg(1) ?><span>Фото не добавлено</span></div>
          <?php endif; ?>
        </div>
        <div class="prod-thumbs" id="sr-prod-thumbs">
          <?php if (count($images) > 1): foreach ($images as $i => $src): ?>
            <button type="button" class="<?= $i === 0 ? 'active' : '' ?>" data-thumb="<?= $i ?>"><img src="<?= e($src) ?>" alt=""></button>
          <?php endforeach; endif; ?>
        </div>
      </div>
      <div class="prod-info">
        <div class="top">
          <?php if ($product['code'] !== ''): ?><span class="code"><?= e($product['code']) ?></span><?php endif; ?>
          <?php if ($category): ?><span class="cat-tag"><?= e($category['name']) ?></span><?php endif; ?>
        </div>
        <h2 class="prod-title"><?= e($product['name']) ?></h2>
        <?php if ($rows): ?>
          <div class="prod-block">
            <div class="prod-block-head">Характеристики</div>
            <div class="prod-specs">
              <?php foreach ($rows as $row): ?>
                <div class="spec<?= $row['k'] === '' ? ' solo' : '' ?>">
                  <?php if ($row['k'] !== ''): ?><span class="k"><?= e($row['k']) ?></span><?php endif; ?>
                  <span class="v"><?= e($row['v']) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        <?php if (trim((string) $product['description']) !== ''): ?>
          <div class="prod-block">
            <div class="prod-block-head">Описание</div>
            <div class="prod-text"><?= nl2br(e($product['description'])) ?></div>
          </div>
        <?php endif; ?>
        <div class="prod-cta">
          <div class="prod-cta-text"><?= e($s['catalogCta']) ?></div>
          <div class="prod-cta-row">
            <a class="btn" href="tel:<?= e(tel_href($s['phone'])) ?>"><?= e($s['phone']) ?></a>
            <a class="btn-ghost" href="mailto:<?= e($s['email']) ?>">Написать на почту</a>
          </div>
        </div>
      </div>
    </div>
    <?php
}

function render_contact(array $content): void
{
    $s = $content['settings'];
    ?>
    <section id="sr-contact"><div class="wrap"><div class="contact-panel">
      <div>
        <p class="eyebrow">Контакты</p>
        <h2>Обсудим поставку</h2>
        <p class="lead">Подберём оборудование под задачу, ответим на вопросы по поставке и техническому сопровождению.</p>
        <div class="contact-list">
          <div class="contact-row"><div class="contact-label">Адрес</div><div class="contact-value" id="sr-map-address"><?= e($s['mapAddress']) ?></div></div>
          <div class="contact-row"><div class="contact-label">Телефон</div><div class="contact-value"><a href="tel:<?= e(tel_href($s['phone'])) ?>"><?= e($s['phone']) ?></a></div></div>
          <div class="contact-row"><div class="contact-label">Email</div><div class="contact-value"><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></div></div>
        </div>
      </div>
      <div class="contact-side">
        <div class="mini"><?= e(preg_replace('/^ООО\s*|[«»"]/u', '', $s['brandName'])) ?></div>
        <strong>Техника<br>для сложных задач</strong>
        <p>Прямые поставки горного и строительного оборудования для предприятий Кузбасса и других регионов.</p>
        <a class="btn" href="mailto:<?= e($s['email']) ?>">Написать нам</a>
      </div>
    </div></div></section>
<?php
}

function render_footer(array $content, string $active): void
{
    $s = $content['settings'];
    $logo = trim((string) $s['logo']);
    $padded = $logo !== '' && $s['logoPad'] === '1';
    $contactsHref = $active === 'home' ? '#sr-contact' : 'index.php#sr-contact';
    ?>
    <footer id="sr-footer"><div class="wrap">
      <div class="grid">
        <div>
          <div class="foot-brandrow<?= $padded ? ' padded' : '' ?>" style="--logo-hf:<?= min(46, max(24, (int) $s['logoHeight'])) ?>px">
            <span id="sr-foot-mark"><?= $logo !== '' ? '<img src="' . e($logo) . '" alt="">' : emblem_svg('f') ?></span>
            <span class="brand-name" style="font-size:17px;"><?= e($s['brandName']) ?></span>
          </div>
          <p style="font-size:13px; color:var(--muted); max-width:250px;">Оптовая торговля горным и строительным оборудованием в Новокузнецке.</p>
          <p class="req"><?= e($s['requisites']) ?></p>
        </div>
        <div><h4>Контакты</h4><ul>
          <li><a href="tel:<?= e(tel_href($s['phone'])) ?>"><?= e($s['phone']) ?></a></li>
          <li><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></li>
          <li><?= e($s['city']) ?></li>
        </ul></div>
        <div><h4>Меню</h4><ul>
          <li><a href="index.php#sr-about">О нас</a></li>
          <li><a href="catalog.php">Каталог</a></li>
          <li><a href="index.php#sr-features">Услуги</a></li>
          <li><a href="<?= $contactsHref ?>">Контакты</a></li>
        </ul></div>
        <div><h4>Категории каталога</h4><ul>
          <?php foreach (array_slice($content['categories'], 0, 6) as $category): ?>
            <li><a href="<?= e(catalog_href($category['slug'])) ?>"><?= e($category['name']) ?></a></li>
          <?php endforeach; ?>
        </ul></div>
      </div>
      <div class="bottom">
        <span><?= e($s['copyright']) ?></span>
        <button class="admin-link" id="sr-admin-open">Панель администратора</button>
      </div>
    </div></footer>
  </div>
<?php
}

/** Окна: карточка позиции, вход администратора, панель. */
function render_overlays(array $content, ?array $openProduct = null): void
{
    $s = $content['settings'];
    ?>
  <div id="sr-toast"></div>

  <div class="sr-fullview<?= $openProduct ? ' show' : '' ?>" id="sr-prod-bg" role="dialog" aria-modal="true" aria-label="Карточка позиции">
    <div id="sr-prod-view"><?php if ($openProduct) {
        render_product_view($content, $openProduct);
    } ?></div>
  </div>

  <div class="sr-modal-bg" id="sr-login-bg">
    <div class="sr-modal">
      <button class="sr-close" id="sr-login-close" aria-label="Закрыть">×</button>
      <h3>Вход для администратора</h3>
      <div class="sr-field"><label for="sr-login-pass">Пароль</label><input id="sr-login-pass" type="password" placeholder="Введите пароль"></div>
      <p id="sr-login-err" style="color:#e0554d; font-size:12.5px; display:none; margin:0 0 10px;">Неверный пароль.</p>
      <div class="sr-modal-actions"><button class="btn" id="sr-login-submit" style="flex:1; justify-content:center;">Войти</button></div>
    </div>
  </div>

  <button id="sr-admin-fab" title="Панель администратора" aria-label="Панель администратора"><?= small_gear_svg('slow') ?></button>

  <div id="sr-admin-drawer">
    <div class="ahead">
      <h3><?= small_gear_svg() ?>Панель администратора</h3>
      <button class="sr-close" id="sr-admin-close" style="position:static;" aria-label="Закрыть">×</button>
    </div>
    <div id="sr-admin-body"></div>
    <div id="sr-admin-foot">
      <button class="btn" id="sr-admin-save" style="flex:1; justify-content:center;">Сохранить изменения</button>
      <button class="btn-ghost" id="sr-admin-logout">Выйти</button>
    </div>
  </div>
</div>
<?php
}

function render_scripts(array $content, array $pageData = []): void
{
    $data = array_merge([
        'catalogCta'     => $content['settings']['catalogCta'],
        'catalogMoreCta' => $content['settings']['catalogMoreCta'],
        'phone'          => $content['settings']['phone'],
        'phoneHref'      => tel_href($content['settings']['phone']),
        'email'          => $content['settings']['email'],
        'catalogUrl'     => 'catalog.php',
        'isAdmin'        => is_admin(),
    ], $pageData);
    ?>
  <script id="sr-page-data" type="application/json"><?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
  <script src="assets/site.js"></script>
  <script src="assets/admin.js"></script>
</body>
</html>
<?php
}

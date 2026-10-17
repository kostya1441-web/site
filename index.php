<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

// Сессию открываем до вывода страницы, иначе PHP ругается на «headers already sent».
sr_session_boot();

try {
    $content = content_load();
} catch (Throwable $e) {
    require __DIR__ . '/includes/db_error.php';
    exit;
}

$s = $content['settings'];

render_head($content, $s['brandName'] . ' — ' . $s['catalogTitle'], mb_substr(strip_tags($s['heroText']), 0, 300));
render_header($content, 'home');
?>
    <section id="sr-hero"><div class="wrap"><div class="grid">
      <div>
        <span class="badge"><?= e($s['badge']) ?></span>
        <h1><span><?= e($s['h1a']) ?></span><br><span><?= e($s['h1b']) ?></span></h1>
        <p><?= e($s['heroText']) ?></p>
        <div class="hero-actions">
          <a class="btn" href="#sr-contact"><?= e($s['heroCta']) ?></a>
          <a class="btn-ghost" href="catalog.php">Смотреть каталог</a>
        </div>
      </div>
      <div class="rig"><?= hero_gears_svg() ?></div>
    </div></div></section>

    <section id="sr-yearsbar"><div class="wrap row">
      <div class="num"><?= e($s['yearsNum']) ?></div>
      <p><?= e($s['yearsText']) ?></p>
      <a class="btn-ghost" href="#sr-about" style="flex:none;"><?= e($s['yearsCta']) ?></a>
    </div></section>

    <section id="sr-catalog"><div class="wrap">
      <div class="head">
        <p class="eyebrow"><?= e($s['catalogEyebrow']) ?></p>
        <h2><?= e($s['catalogTitle']) ?></h2>
      </div>
      <div class="grid" id="sr-cats-preview">
        <?php if (!$content['categories']): ?>
          <div class="empty">Категории пока не добавлены. Добавьте их в панели администратора.</div>
        <?php endif; ?>
        <?php foreach ($content['categories'] as $i => $category): ?>
          <?php $cover = category_cover($content, (int) $category['id']); ?>
          <a class="catcard" href="<?= e(catalog_href($category['slug'])) ?>">
            <div class="photo">
              <?php if ($cover !== ''): ?>
                <img src="<?= e($cover) ?>" alt="<?= e($category['name']) ?>" loading="lazy">
              <?php else: ?>
                <div class="ph-empty"><?= gauge_svg($i + 1) ?><span>Фото не добавлено</span></div>
              <?php endif; ?>
            </div>
            <div class="body">
              <div class="top"><?php if ($category['code'] !== ''): ?><span class="code"><?= e($category['code']) ?></span><?php endif; ?></div>
              <h3><?= e($category['name']) ?></h3>
              <?php if ($category['description'] !== ''): ?><p><?= e($category['description']) ?></p><?php endif; ?>
              <div class="more">
                <span>Смотреть позиции</span>
                <span class="cnt2"><?= count(products_of($content, (int) $category['id'])) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="allcta"><a class="btn" href="catalog.php"><?= e($s['catalogAllCta']) ?></a></div>
    </div></section>

    <?php if ($content['features']): ?>
    <section id="sr-features" style="padding-top:0;"><div class="wrap"><div class="grid">
      <?php foreach ($content['features'] as $i => $feature): ?>
        <div class="f">
          <div class="ficon"><?= feature_icon($i) ?></div>
          <div><h3><?= e($feature['title']) ?></h3><p><?= e($feature['text']) ?></p></div>
        </div>
      <?php endforeach; ?>
    </div></div></section>
    <?php endif; ?>

    <section id="sr-about"><div class="wrap grid">
      <div>
        <?php if ($content['stats']): ?>
        <div class="stats">
          <?php foreach ($content['stats'] as $stat): ?>
            <div class="stat"><div class="v"><?= e($stat['value']) ?></div><div class="l"><?= e($stat['label']) ?></div></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <div>
        <p class="eyebrow"><?= e($s['aboutEyebrow']) ?></p>
        <h2><?= e($s['aboutTitle']) ?></h2>
        <div class="body"><p><?= e($s['aboutText']) ?></p></div>
      </div>
    </div></section>
<?php
render_contact($content);
render_footer($content, 'home');
render_overlays($content);
render_scripts($content, ['products' => []]);

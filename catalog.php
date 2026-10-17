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

$catSlug  = isset($_GET['cat']) ? (string) $_GET['cat'] : '';
$prodSlug = isset($_GET['prod']) ? (string) $_GET['prod'] : '';

$activeCategory = $catSlug !== '' ? category_by_slug($content, $catSlug) : null;
$openProduct    = $prodSlug !== '' ? product_by_slug($content, $prodSlug) : null;

if ($openProduct && !$activeCategory) {
    foreach ($content['categories'] as $category) {
        if ((int) $category['id'] === (int) $openProduct['category_id']) {
            $activeCategory = $category;
            break;
        }
    }
}

$items = $activeCategory ? products_of($content, (int) $activeCategory['id']) : $content['products'];
$total = count($items);

$categoriesById = [];
foreach ($content['categories'] as $category) {
    $categoriesById[(int) $category['id']] = $category;
}

$title = $activeCategory
    ? $activeCategory['name'] . ' — каталог ' . $s['brandName']
    : 'Каталог оборудования — ' . $s['brandName'];
$description = $activeCategory && $activeCategory['description'] !== ''
    ? $activeCategory['description']
    : 'Каталог горного и строительного оборудования по категориям.';

// Данные позиций для карточки товара без перезагрузки страницы.
$productsData = [];
foreach ($content['products'] as $product) {
    $category = $categoriesById[(int) $product['category_id']] ?? null;
    $productsData[] = [
        'slug'     => $product['slug'],
        'code'     => $product['code'],
        'name'     => $product['name'],
        'specs'    => lines($product['specs']),
        'text'     => $product['description'],
        'images'   => $product['images'],
        'category' => $category['name'] ?? '',
    ];
}

render_head($content, $title, $description);
render_header($content, 'catalog');
?>
    <section id="sr-crumbs"><div class="wrap row">
      <a href="index.php">Главная</a><span class="sep">/</span>
      <?php if ($activeCategory): ?>
        <a href="catalog.php">Каталог</a><span class="sep">/</span><span><?= e($activeCategory['name']) ?></span>
      <?php else: ?>
        <span>Каталог</span>
      <?php endif; ?>
    </div></section>

    <section id="sr-catalog" style="padding-top:36px;"><div class="wrap">
      <div class="head">
        <p class="eyebrow"><?= e($s['catalogEyebrow']) ?></p>
        <h1 class="cat-h1"><?= e($activeCategory ? $activeCategory['name'] : $s['catalogTitle']) ?></h1>
      </div>
      <?php if ($s['catalogPageLead'] !== ''): ?><p class="lead"><?= e($s['catalogPageLead']) ?></p><?php endif; ?>

      <div class="cats">
        <a class="chip<?= $activeCategory ? '' : ' active' ?>" href="catalog.php">
          <?= e($s['catalogAllLabel']) ?><span class="cnt"><?= count($content['products']) ?></span>
        </a>
        <?php foreach ($content['categories'] as $category): ?>
          <a class="chip<?= $activeCategory && $activeCategory['id'] === $category['id'] ? ' active' : '' ?>"
             href="<?= e(catalog_href($category['slug'])) ?>">
            <?= e($category['name']) ?><span class="cnt"><?= count(products_of($content, (int) $category['id'])) ?></span>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="headrow">
        <p class="cat-desc"><?= e($activeCategory['description'] ?? '') ?></p>
        <span class="count"><?= $total ?> <?= plural($total, 'позиция', 'позиции', 'позиций') ?></span>
      </div>

      <div class="grid" id="sr-catalog-grid">
        <?php if (!$items): ?>
          <div class="empty">В этой категории пока нет позиций. Добавьте их в панели администратора.</div>
        <?php endif; ?>
        <?php foreach ($items as $i => $product): ?>
          <?php
            $category = $categoriesById[(int) $product['category_id']] ?? null;
            $specs = array_slice(lines($product['specs']), 0, 3);
            $images = $product['images'];
          ?>
          <article class="item">
            <div class="photo">
              <?php if ($images): ?>
                <img src="<?= e($images[0]) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                <?php if (count($images) > 1): ?><span class="ph-count"><?= count($images) ?> фото</span><?php endif; ?>
              <?php else: ?>
                <div class="ph-empty"><?= gauge_svg($i + 1) ?><span>Фото не добавлено</span></div>
              <?php endif; ?>
            </div>
            <div class="body">
              <div class="top">
                <?php if ($product['code'] !== ''): ?><span class="code"><?= e($product['code']) ?></span><?php endif; ?>
                <?php if ($category): ?><span class="cat-tag"><?= e($category['name']) ?></span><?php endif; ?>
              </div>
              <h3><?= e($product['name']) ?></h3>
              <ul><?php foreach ($specs as $spec): ?><li><?= e($spec) ?></li><?php endforeach; ?></ul>
              <a class="btn-ghost" href="<?= e(catalog_href($category['slug'] ?? null, $product['slug'])) ?>"
                 data-prod-slug="<?= e($product['slug']) ?>"><?= e($s['catalogMoreCta']) ?></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div></section>
<?php
render_contact($content);
render_footer($content, 'catalog');
render_overlays($content, $openProduct);
render_scripts($content, [
    'products'    => $productsData,
    'openProduct' => $openProduct['slug'] ?? '',
]);

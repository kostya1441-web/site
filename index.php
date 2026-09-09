<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Главная';
$categories = getActiveCategories();

$featured = db()->query(
    'SELECT p.*, c.slug AS category_slug FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.is_active = 1 AND p.is_featured = 1
     ORDER BY p.sort_order, p.id DESC LIMIT 8'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <h1><?= e(setting('site_name')) ?></h1>
        <p><?= e(setting('site_tagline')) ?></p>
        <a href="/catalog.php" class="btn btn-accent">Перейти в каталог</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Категории товаров</h2>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a class="category-card" href="/catalog.php?cat=<?= e($cat['slug']) ?>">
                    <div class="cat-icon">🌱</div>
                    <h3><?= e($cat['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($featured)): ?>
<section class="section" style="background:#fff;">
    <div class="container">
        <h2 class="section-title">Популярные товары</h2>
        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
                <?php include __DIR__ . '/includes/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container about-content" style="text-align:center;">
        <h2 class="section-title">Почему выбирают нас</h2>
        <p>Мы фермерское хозяйство рядом с Новокузнецком. Продаём мясо, ягоды и другие продукты собственного производства — без посредников, свежее и по честной цене. Доставка по городу, оплата картой онлайн через Сбербанк или наличными при получении.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

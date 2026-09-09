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
        <span class="hero-eyebrow"><span class="ico"><?= icon('leaf') ?></span> Фермерское хозяйство · Новокузнецк</span>
        <h1><?= e(setting('site_name')) ?></h1>
        <p><?= e(setting('site_tagline')) ?></p>
        <div class="hero-actions">
            <a href="/catalog.php" class="btn btn-accent"><span class="ico"><?= icon('arrow') ?></span> Перейти в каталог</a>
            <a href="/contacts.php" class="btn btn-outline">Как нас найти</a>
        </div>
    </div>
</section>

<section class="container">
    <div class="trust-strip">
        <div class="trust-item"><span class="ico"><?= icon('leaf') ?></span> Только натуральные продукты</div>
        <div class="trust-item"><span class="ico"><?= icon('truck') ?></span> Доставка по Новокузнецку</div>
        <div class="trust-item"><span class="ico"><?= icon('shield') ?></span> Безопасная оплата (Сбербанк)</div>
    </div>
</section>

<section class="section">
    <div class="container">
        <span class="section-eyebrow">Ассортимент</span>
        <h2 class="section-title">Категории товаров</h2>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a class="category-card" href="/catalog.php?cat=<?= e($cat['slug']) ?>">
                    <div class="cat-icon"><span class="ico"><?= icon('leaf') ?></span></div>
                    <h3><?= e($cat['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($featured)): ?>
<section class="section" style="background:var(--color-surface);">
    <div class="container">
        <span class="section-eyebrow">Выбор покупателей</span>
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

<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? getProductBySlug($slug) : null;

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Товар не найден';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state">Товар не найден или снят с продажи.</div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$related = db()->prepare(
    'SELECT p.*, c.slug AS category_slug FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.category_id = :cid AND p.id != :id AND p.is_active = 1
     ORDER BY p.sort_order LIMIT 4'
);
$related->execute(['cid' => $product['category_id'], 'id' => $product['id']]);
$relatedProducts = $related->fetchAll();

$pageTitle = $product['name'];
require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <div class="breadcrumbs">
            <a href="/index.php">Главная</a> /
            <a href="/catalog.php?cat=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> /
            <?= e($product['name']) ?>
        </div>

        <div class="product-detail">
            <div class="product-img-wrap">
                <img src="<?= e(imageUrl($product['image'])) ?>" alt="<?= e($product['name']) ?>">
            </div>
            <div class="product-detail-body">
                <h1><?= e($product['name']) ?></h1>
                <div class="product-price" style="font-size:26px;">
                    <?= formatPrice($product['price']) ?> <span class="product-unit">/ <?= e($product['unit']) ?></span>
                    <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                        <span class="old"><?= formatPrice($product['old_price']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($product['description']): ?>
                    <p><?= nl2br(e($product['description'])) ?></p>
                <?php endif; ?>

                <?php if ($product['in_stock']): ?>
                    <form class="js-add-to-cart" action="/cart_actions.php" method="post" style="display:flex; gap:10px; align-items:center; margin-top:16px;">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                        <label>Количество (<?= e($product['unit']) ?>):
                            <input type="number" class="qty-input" name="qty" value="1" min="0.1" step="0.1">
                        </label>
                        <button type="submit" class="btn btn-accent">Добавить в корзину</button>
                    </form>
                <?php else: ?>
                    <p class="out-of-stock-badge">Товар временно отсутствует</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($relatedProducts)): ?>
            <h2 class="section-title" style="margin-top:50px;">Похожие товары</h2>
            <div class="product-grid">
                <?php foreach ($relatedProducts as $p): ?>
                    <?php include __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

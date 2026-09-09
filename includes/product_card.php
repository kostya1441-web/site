<?php
/**
 * Partial карточки товара. Ожидает переменную $p (строка из products, с category_slug).
 */
?>
<div class="product-card">
    <a href="/product.php?slug=<?= e($p['slug']) ?>">
        <img class="product-img" src="<?= e(imageUrl($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
    </a>
    <div class="product-body">
        <h3><a href="/product.php?slug=<?= e($p['slug']) ?>"><?= e($p['name']) ?></a></h3>
        <div class="product-price">
            <?= formatPrice($p['price']) ?> <span class="product-unit">/ <?= e($p['unit']) ?></span>
            <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
                <span class="old"><?= formatPrice($p['old_price']) ?></span>
            <?php endif; ?>
        </div>
        <?php if (!$p['in_stock']): ?>
            <div class="out-of-stock-badge">Нет в наличии</div>
        <?php endif; ?>
        <div class="product-actions">
            <?php if ($p['in_stock']): ?>
            <form class="js-add-to-cart" action="/cart_actions.php" method="post" style="display:flex; gap:8px; align-items:center;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="number" class="qty-input" name="qty" value="1" min="0.1" step="0.1">
                <button type="submit" class="btn btn-sm">В корзину</button>
            </form>
            <?php else: ?>
                <button class="btn btn-sm" disabled>Нет в наличии</button>
            <?php endif; ?>
        </div>
    </div>
</div>

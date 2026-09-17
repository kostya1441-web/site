<?php
/** @var array $product */
$discount = (!empty($product['old_price']) && $product['old_price'] > $product['price'])
    ? (int) round(100 - ($product['price'] / $product['old_price'] * 100))
    : 0;
$inStock = (int) $product['stock'] > 0;
?>
<article class="card<?= $inStock ? '' : ' card--out' ?>">
    <a class="card__media" href="<?= u('/') ?>product/<?= e($product['slug']) ?>">
        <img src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="320" height="240">
        <?php if ($discount): ?><span class="badge badge--sale">−<?= $discount ?>%</span><?php endif; ?>
        <?php if (!empty($product['is_featured'])): ?><span class="badge badge--hit">Хит</span><?php endif; ?>
        <?php if (!$inStock): ?><span class="badge badge--out">Нет в наличии</span><?php endif; ?>
    </a>

    <div class="card__body">
        <h3 class="card__title"><a href="<?= u('/') ?>product/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a></h3>
        <p class="card__desc"><?= e(excerpt($product['short_description'] ?: $product['description'], 78)) ?></p>

        <div class="card__footer">
            <div class="card__price">
                <strong><?= price($product['price']) ?></strong>
                <span class="card__unit">/ <?= e($product['unit']) ?></span>
                <?php if ($discount): ?><s><?= price($product['old_price']) ?></s><?php endif; ?>
            </div>

            <form class="card__form" method="post" action="<?= u('/cart/add') ?>" data-cart-form>
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button class="btn btn--primary btn--sm" type="submit" <?= $inStock ? '' : 'disabled' ?>>
                    <?= $inStock ? 'В корзину' : 'Нет в наличии' ?>
                </button>
            </form>
        </div>
    </div>
</article>

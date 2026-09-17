<?php
/** @var array $product */
/** @var array $related */
$discount = (!empty($product['old_price']) && $product['old_price'] > $product['price'])
    ? (int) round(100 - ($product['price'] / $product['old_price'] * 100))
    : 0;
$inStock = (int) $product['stock'] > 0;
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="/">Главная</a><span>/</span>
        <a href="/catalog">Каталог</a><span>/</span>
        <?php if (!empty($product['category_slug'])): ?>
            <a href="/catalog/<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a><span>/</span>
        <?php endif; ?>
        <span><?= e($product['name']) ?></span>
    </nav>

    <div class="product">
        <div class="product__media">
            <img src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>" width="640" height="480">
            <?php if ($discount): ?><span class="badge badge--sale">−<?= $discount ?>%</span><?php endif; ?>
        </div>

        <div class="product__info">
            <h1 class="product__title"><?= e($product['name']) ?></h1>

            <div class="product__meta">
                <?php if (!empty($product['sku'])): ?><span>Артикул: <?= e($product['sku']) ?></span><?php endif; ?>
                <span class="product__stock<?= $inStock ? '' : ' is-out' ?>">
                    <?= $inStock ? 'В наличии' : 'Под заказ' ?>
                </span>
            </div>

            <?php if (!empty($product['short_description'])): ?>
                <p class="product__lead"><?= e($product['short_description']) ?></p>
            <?php endif; ?>

            <div class="product__price">
                <strong><?= price($product['price']) ?></strong>
                <span>за <?= e($product['unit']) ?></span>
                <?php if ($discount): ?><s><?= price($product['old_price']) ?></s><?php endif; ?>
            </div>

            <form class="product__buy" method="post" action="/cart/add" data-cart-form>
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <div class="qty" data-qty>
                    <button type="button" data-qty-minus aria-label="Уменьшить">−</button>
                    <input type="number" name="quantity" value="1" min="1" max="99" aria-label="Количество">
                    <button type="button" data-qty-plus aria-label="Увеличить">+</button>
                </div>
                <button class="btn btn--primary btn--lg" type="submit" <?= $inStock ? '' : 'disabled' ?>>
                    <?= $inStock ? 'Добавить в корзину' : 'Нет в наличии' ?>
                </button>
            </form>

            <ul class="product__benefits">
                <li>🚚 Доставка по Новокузнецку от <?= price($shopConfig['delivery_price'] ?? 300) ?>, бесплатно от <?= price($shopConfig['free_delivery_from'] ?? 3000) ?></li>
                <li>💳 Оплата картой онлайн через Сбербанк или при получении</li>
                <li>🧾 Ветеринарные документы на каждую партию</li>
            </ul>
        </div>
    </div>

    <?php if (!empty($product['description'])): ?>
        <section class="product__description">
            <h2>Описание</h2>
            <?php foreach (preg_split('/\R{2,}/', (string) $product['description']) as $paragraph): ?>
                <?php if (trim($paragraph) !== ''): ?>
                    <p><?= nl2br(e(trim($paragraph))) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (!empty($product['weight'])): ?>
                <p class="product__weight">Средний вес упаковки: <?= e(rtrim(rtrim(number_format((float) $product['weight'], 3, ',', ' '), '0'), ',')) ?> кг</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($related): ?>
        <section class="section">
            <div class="section__head"><h2 class="section__title">С этим часто берут</h2></div>
            <div class="grid grid--products">
                <?php foreach ($related as $item): ?>
                    <?php \App\Core\View::partial('partials/product-card', ['product' => $item]); ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php
/** @var array $categories */
/** @var array $featured */
/** @var array $latest */
$settings = $settings ?? [];
?>
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <span class="hero__tag">Фермерское хозяйство • Кузбасс</span>
            <h1 class="hero__title">Мясо, птица и ягода <span>прямо с фермы</span> в Новокузнецке</h1>
            <p class="hero__text">
                Забиваем и собираем под заказ, привозим в день оформления. Без заморозки, антибиотиков
                и «магазинного» хранения по три недели.
            </p>
            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="/catalog">Смотреть каталог</a>
                <a class="btn btn--ghost btn--lg" href="<?= e(phone_link($settings['phone'] ?? '')) ?>">Заказать по телефону</a>
            </div>
            <ul class="hero__facts">
                <li><strong>24 часа</strong><span>от фермы до стола</span></li>
                <li><strong>Бесплатно</strong><span>доставка от <?= price($shopConfig['free_delivery_from'] ?? 3000) ?></span></li>
                <li><strong>11 лет</strong><span>своё хозяйство</span></li>
            </ul>
        </div>
        <div class="hero__media" aria-hidden="true">
            <div class="hero__card hero__card--1">🥩<span>Говядина травяного откорма</span></div>
            <div class="hero__card hero__card--2">🫐<span>Таёжная ягода Горной Шории</span></div>
            <div class="hero__card hero__card--3">🍯<span>Домашние заготовки</span></div>
        </div>
    </div>
</section>

<section class="benefits">
    <div class="container benefits__grid">
        <div class="benefit"><span aria-hidden="true">🧾</span><h3>Ветеринарные документы</h3><p>На каждую партию мяса — свидетельство и клеймо.</p></div>
        <div class="benefit"><span aria-hidden="true">❄️</span><h3>Охлаждение, не заморозка</h3><p>Везём в рефрижераторе при +2…+4 °C.</p></div>
        <div class="benefit"><span aria-hidden="true">💳</span><h3>Оплата картой онлайн</h3><p>Безопасный платёж через эквайринг Сбербанка.</p></div>
        <div class="benefit"><span aria-hidden="true">↩️</span><h3>Вернём деньги</h3><p>Если продукт не понравился — заберём и вернём оплату.</p></div>
    </div>
</section>

<?php if ($categories): ?>
<section class="section">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title">Категории</h2>
            <a class="section__link" href="/catalog">Весь каталог →</a>
        </div>
        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <a class="category" href="/catalog/<?= e($category['slug']) ?>">
                    <?php if (!empty($category['image'])): ?>
                        <img src="<?= e(product_image($category['image'], 'category.svg')) ?>" alt="" loading="lazy" width="200" height="140">
                    <?php else: ?>
                        <span class="category__icon" aria-hidden="true"><?= e($category['icon'] ?: '🧺') ?></span>
                    <?php endif; ?>
                    <span class="category__name"><?= e($category['name']) ?></span>
                    <span class="category__count"><?= (int) $category['products_count'] ?> <?= plural((int) $category['products_count'], 'товар', 'товара', 'товаров') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($featured): ?>
<section class="section section--muted">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title">Хиты продаж</h2>
            <a class="section__link" href="/catalog?sort=popular">Все хиты →</a>
        </div>
        <div class="grid grid--products">
            <?php foreach ($featured as $product): ?>
                <?php \App\Core\View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container steps">
        <h2 class="section__title">Как мы работаем</h2>
        <ol class="steps__list">
            <li><span>1</span><h3>Вы оформляете заказ</h3><p>На сайте или по телефону — до 14:00 для доставки в этот же день.</p></li>
            <li><span>2</span><h3>Мы собираем корзину</h3><p>Режем мясо под ваш запрос, взвешиваем и упаковываем в вакуум.</p></li>
            <li><span>3</span><h3>Оплата онлайн или курьеру</h3><p>Картой через Сбербанк на сайте либо при получении.</p></li>
            <li><span>4</span><h3>Привозим за 2–4 часа</h3><p>По Новокузнецку — свой курьер, в пригород — по договорённости.</p></li>
        </ol>
    </div>
</section>

<?php if ($latest): ?>
<section class="section section--muted">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title">Новинки фермы</h2>
            <a class="section__link" href="/catalog?sort=new">Смотреть все →</a>
        </div>
        <div class="grid grid--products">
            <?php foreach ($latest as $product): ?>
                <?php \App\Core\View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container cta">
        <div class="cta__text">
            <h2>Сомневаетесь, что выбрать?</h2>
            <p>Позвоните — подскажем, какой отруб лучше для стейка, а какой для наваристого борща, и соберём заказ под ваш бюджет.</p>
        </div>
        <div class="cta__actions">
            <a class="btn btn--primary btn--lg" href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
            <a class="btn btn--ghost btn--lg" href="/contacts">Написать нам</a>
        </div>
    </div>
</section>

<?php /** @var array $cart */ ?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?= u('/') ?>">Главная</a><span>/</span><span>Корзина</span>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title">Корзина</h1>
    </header>

    <?php if (!$cart['lines']): ?>
        <div class="empty">
            <span aria-hidden="true">🛒</span>
            <h2>В корзине пока пусто</h2>
            <p>Загляните в каталог — свежая партия приезжает с фермы каждое утро.</p>
            <a class="btn btn--primary btn--lg" href="<?= u('/catalog') ?>">Перейти в каталог</a>
        </div>
    <?php else: ?>
        <div class="cart">
            <div class="cart__list">
                <?php foreach ($cart['lines'] as $line): ?>
                    <div class="cart-line" data-line="<?= (int) $line['product_id'] ?>">
                        <a class="cart-line__media" href="<?= u('/') ?>product/<?= e($line['slug']) ?>">
                            <img src="<?= e(product_image($line['image'])) ?>" alt="<?= e($line['name']) ?>" loading="lazy" width="96" height="96">
                        </a>
                        <div class="cart-line__info">
                            <a class="cart-line__name" href="<?= u('/') ?>product/<?= e($line['slug']) ?>"><?= e($line['name']) ?></a>
                            <span class="cart-line__price"><?= price($line['price']) ?> / <?= e($line['unit']) ?></span>
                            <?php if ($line['stock'] <= 0): ?>
                                <span class="cart-line__warn">Под заказ — уточним срок по телефону</span>
                            <?php endif; ?>
                        </div>

                        <form class="cart-line__qty" method="post" action="<?= u('/cart/update') ?>" data-cart-update>
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $line['product_id'] ?>">
                            <div class="qty" data-qty>
                                <button type="button" data-qty-minus aria-label="Уменьшить">−</button>
                                <input type="number" name="quantity" value="<?= (int) $line['quantity'] ?>" min="0" max="99" aria-label="Количество">
                                <button type="button" data-qty-plus aria-label="Увеличить">+</button>
                            </div>
                        </form>

                        <div class="cart-line__sum"><?= price($line['sum']) ?></div>

                        <form method="post" action="<?= u('/cart/remove') ?>" class="cart-line__remove" data-cart-remove>
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $line['product_id'] ?>">
                            <button type="submit" aria-label="Удалить товар">✕</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <form method="post" action="<?= u('/cart/clear') ?>" class="cart__clear">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-button">Очистить корзину</button>
                </form>
            </div>

            <aside class="cart__summary">
                <h2>Итого</h2>
                <dl class="summary">
                    <div><dt>Товары (<?= (int) $cart['count'] ?>)</dt><dd data-summary-subtotal><?= price($cart['subtotal']) ?></dd></div>
                    <div><dt>Доставка</dt><dd data-summary-delivery><?= $cart['delivery'] > 0 ? price($cart['delivery']) : 'бесплатно' ?></dd></div>
                    <div class="summary__total"><dt>К оплате</dt><dd data-summary-total><?= price($cart['total']) ?></dd></div>
                </dl>

                <?php if ($cart['left_to_free'] > 0): ?>
                    <p class="cart__hint">Добавьте товаров ещё на <strong><?= price($cart['left_to_free']) ?></strong> — и доставка бесплатная.</p>
                <?php endif; ?>

                <?php if ($cart['below_min_order']): ?>
                    <p class="cart__hint cart__hint--warn">Минимальная сумма заказа — <?= price($cart['min_order']) ?>.</p>
                <?php endif; ?>

                <a class="btn btn--primary btn--lg btn--block <?= $cart['below_min_order'] ? 'is-disabled' : '' ?>" href="<?= u('/checkout') ?>">
                    Оформить заказ
                </a>
                <a class="btn btn--ghost btn--block" href="<?= u('/catalog') ?>">Продолжить покупки</a>
            </aside>
        </div>
    <?php endif; ?>
</div>

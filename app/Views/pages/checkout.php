<?php
/** @var array $cart */
/** @var bool $sberReady */
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="/">Главная</a><span>/</span><a href="/cart">Корзина</a><span>/</span><span>Оформление</span>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title">Оформление заказа</h1>
        <p class="page-head__text">Заполните контакты — менеджер подтвердит заказ звонком в течение 15 минут.</p>
    </header>

    <form class="checkout" method="post" action="/checkout" id="checkout-form">
        <?= csrf_field() ?>

        <div class="checkout__main">
            <section class="panel">
                <h2 class="panel__title">1. Контакты</h2>
                <div class="form-grid">
                    <label class="field">
                        <span>Имя и фамилия *</span>
                        <input type="text" name="customer_name" required maxlength="120" value="<?= old('customer_name') ?>" placeholder="Иван Петров">
                    </label>
                    <label class="field">
                        <span>Телефон *</span>
                        <input type="tel" name="phone" required value="<?= old('phone') ?>" placeholder="+7 (923) 000-00-00" data-phone>
                    </label>
                    <label class="field field--full">
                        <span>E-mail (для чека и статуса заказа)</span>
                        <input type="email" name="email" value="<?= old('email') ?>" placeholder="mail@example.ru">
                    </label>
                </div>
            </section>

            <section class="panel">
                <h2 class="panel__title">2. Получение</h2>
                <div class="options">
                    <label class="option">
                        <input type="radio" name="delivery_type" value="delivery" checked data-delivery-type>
                        <span class="option__body">
                            <strong>Доставка по Новокузнецку</strong>
                            <small><?= price($shopConfig['delivery_price'] ?? 300) ?>, бесплатно от <?= price($shopConfig['free_delivery_from'] ?? 3000) ?></small>
                        </span>
                    </label>
                    <label class="option">
                        <input type="radio" name="delivery_type" value="pickup" data-delivery-type>
                        <span class="option__body">
                            <strong>Самовывоз</strong>
                            <small><?= e($settings['address'] ?? '') ?></small>
                        </span>
                    </label>
                </div>

                <label class="field field--full" data-address-field>
                    <span>Адрес доставки *</span>
                    <input type="text" name="address" value="<?= old('address') ?>" placeholder="ул. Кирова, 25, кв. 14, подъезд 2">
                </label>

                <label class="field field--full">
                    <span>Комментарий к заказу</span>
                    <textarea name="comment" rows="3" maxlength="1000" placeholder="Например: нарезать стейками по 300 г, позвонить за час"><?= old('comment') ?></textarea>
                </label>
            </section>

            <section class="panel">
                <h2 class="panel__title">3. Оплата</h2>
                <div class="options">
                    <label class="option">
                        <input type="radio" name="payment_method" value="sber" <?= $sberReady ? 'checked' : 'disabled' ?>>
                        <span class="option__body">
                            <strong>Картой онлайн — Сбербанк</strong>
                            <small><?= $sberReady
                                ? 'Безопасная оплата Mir, Visa, Mastercard на странице банка'
                                : 'Временно недоступно — настройте эквайринг в админке' ?></small>
                        </span>
                    </label>
                    <label class="option">
                        <input type="radio" name="payment_method" value="cash" <?= $sberReady ? '' : 'checked' ?>>
                        <span class="option__body">
                            <strong>При получении</strong>
                            <small>Наличными или картой курьеру</small>
                        </span>
                    </label>
                </div>

                <label class="checkbox checkbox--agree">
                    <input type="checkbox" name="agree" value="1" required>
                    <span>Согласен на обработку персональных данных и принимаю условия <a href="/delivery">оферты</a></span>
                </label>
            </section>
        </div>

        <aside class="checkout__summary">
            <div class="panel panel--sticky">
                <h2 class="panel__title">Ваш заказ</h2>
                <ul class="checkout__items">
                    <?php foreach ($cart['lines'] as $line): ?>
                        <li>
                            <span class="checkout__item-name"><?= e($line['name']) ?> <em>× <?= (int) $line['quantity'] ?></em></span>
                            <span><?= price($line['sum']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <dl class="summary"
                    data-summary
                    data-subtotal="<?= (float) $cart['subtotal'] ?>"
                    data-delivery-price="<?= (float) ($shopConfig['delivery_price'] ?? 300) ?>"
                    data-free-from="<?= (float) $cart['free_from'] ?>">
                    <div><dt>Товары</dt><dd><?= price($cart['subtotal']) ?></dd></div>
                    <div><dt>Доставка</dt><dd data-delivery-sum><?= $cart['delivery'] > 0 ? price($cart['delivery']) : 'бесплатно' ?></dd></div>
                    <div class="summary__total"><dt>К оплате</dt><dd data-total-sum><?= price($cart['total']) ?></dd></div>
                </dl>

                <button class="btn btn--primary btn--lg btn--block" type="submit">Подтвердить заказ</button>
                <p class="checkout__note">Нажимая кнопку, вы перейдёте на защищённую страницу Сбербанка (при оплате онлайн).</p>
            </div>
        </aside>
    </form>
</div>

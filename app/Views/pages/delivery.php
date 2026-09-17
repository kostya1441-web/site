<?php $settings = $settings ?? []; ?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="/">Главная</a><span>/</span><span>Доставка и оплата</span>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title">Доставка и оплата</h1>
        <p class="page-head__text"><?= e($settings['delivery_text'] ?? '') ?></p>
    </header>

    <div class="cards-2">
        <section class="panel">
            <h2 class="panel__title">🚚 Доставка по Новокузнецку</h2>
            <ul class="list-check">
                <li>Стоимость — <strong><?= price($shopConfig['delivery_price'] ?? 300) ?></strong>, бесплатно при заказе от <strong><?= price($shopConfig['free_delivery_from'] ?? 3000) ?></strong>.</li>
                <li>Заказы до 14:00 привозим в этот же день, позже — на следующий.</li>
                <li>Интервал доставки согласовываем по телефону, курьер звонит за 30–60 минут.</li>
                <li>Возим в рефрижераторе при +2…+4 °C — продукт приезжает охлаждённым, а не замороженным.</li>
                <li>В пригород (Кузедеево, Осинники, Калтан) — по договорённости.</li>
            </ul>
        </section>

        <section class="panel">
            <h2 class="panel__title">🏬 Самовывоз</h2>
            <ul class="list-check">
                <li>Адрес: <strong><?= e($settings['address'] ?? '') ?></strong>.</li>
                <li>Режим работы: <?= e($settings['work_hours'] ?? '') ?>.</li>
                <li>Заказ храним охлаждённым до конца рабочего дня.</li>
                <li>Оплатить можно на месте картой или наличными.</li>
            </ul>
        </section>

        <section class="panel">
            <h2 class="panel__title">💳 Оплата картой онлайн</h2>
            <p class="panel__text">
                Оплата проходит через интернет-эквайринг ПАО Сбербанк. После подтверждения заказа
                вы попадаете на защищённую страницу банка, где вводите данные карты.
            </p>
            <ul class="list-check">
                <li>Принимаем Мир, Visa, Mastercard, а также SberPay.</li>
                <li>Соединение защищено протоколом TLS, данные карты не попадают в магазин.</li>
                <li>Платёж подтверждается по 3-D Secure — кодом из СМС банка.</li>
                <li>Электронный чек приходит на указанную почту.</li>
            </ul>
        </section>

        <section class="panel">
            <h2 class="panel__title">↩️ Возврат и гарантии</h2>
            <ul class="list-check">
                <li>Проверяйте заказ при курьере: если продукт не соответствует описанию — не принимайте.</li>
                <li>Возврат средств по онлайн-оплате делаем на ту же карту в течение 1–5 рабочих дней.</li>
                <li>Отменить заказ можно до момента сборки — позвоните <a href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>.</li>
                <li>Продовольственные товары надлежащего качества обмену не подлежат (ПП РФ № 2463).</li>
            </ul>
        </section>
    </div>

    <section class="section">
        <div class="cta">
            <div class="cta__text">
                <h2>Остались вопросы по доставке?</h2>
                <p>Напишите или позвоните — подскажем, успеваем ли привезти сегодня и как лучше упаковать заказ.</p>
            </div>
            <div class="cta__actions">
                <a class="btn btn--primary btn--lg" href="/contacts#feedback">Задать вопрос</a>
                <a class="btn btn--ghost btn--lg" href="/catalog">В каталог</a>
            </div>
        </div>
    </section>
</div>

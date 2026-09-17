<?php $settings = $settings ?? []; ?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="/">Главная</a><span>/</span><span>О нас</span>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title">О хозяйстве «Ваш фермер»</h1>
        <p class="page-head__text"><?= e($settings['about_lead'] ?? '') ?></p>
    </header>

    <section class="about">
        <div class="about__text">
            <?php foreach (preg_split('/\R{2,}/', (string) ($settings['about_text'] ?? '')) as $paragraph): ?>
                <?php if (trim($paragraph) !== ''): ?>
                    <p><?= nl2br(e(trim($paragraph))) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <aside class="about__figures">
            <div><strong>2014</strong><span>год основания хозяйства</span></div>
            <div><strong>60 км</strong><span>от Новокузнецка до фермы</span></div>
            <div><strong>4 200+</strong><span>заказов доставлено</span></div>
            <div><strong>24 часа</strong><span>от забоя до доставки</span></div>
        </aside>
    </section>

    <section class="section">
        <div class="section__head"><h2 class="section__title">Почему нам доверяют</h2></div>
        <div class="benefits__grid">
            <div class="benefit"><span aria-hidden="true">🐄</span><h3>Свой скот</h3><p>Абердин-ангус на травяном откорме, свободный выгул, без стимуляторов роста.</p></div>
            <div class="benefit"><span aria-hidden="true">🧪</span><h3>Контроль качества</h3><p>Ветконтроль на каждую партию, ягода и грибы — с радиологическим заключением.</p></div>
            <div class="benefit"><span aria-hidden="true">🚛</span><h3>Своя логистика</h3><p>Рефрижератор и курьеры в штате: продукт не «гуляет» по складам посредников.</p></div>
            <div class="benefit"><span aria-hidden="true">👨‍🌾</span><h3>Семейное дело</h3><p>Хозяйством занимается одна семья — мы отвечаем за каждый килограмм своим именем.</p></div>
        </div>
    </section>

    <section class="section">
        <div class="cta">
            <div class="cta__text">
                <h2>Приезжайте в гости</h2>
                <p>Мы открыты для покупателей: можно приехать на ферму, посмотреть хозяйство и выбрать продукты на месте. Запишитесь по телефону.</p>
            </div>
            <div class="cta__actions">
                <a class="btn btn--primary btn--lg" href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
                <a class="btn btn--ghost btn--lg" href="/contacts">Контакты и карта</a>
            </div>
        </div>
    </section>
</div>

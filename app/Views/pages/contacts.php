<?php
$settings = $settings ?? [];
$lat  = $settings['map_lat'] ?? '53.757547';
$lng  = $settings['map_lng'] ?? '87.136044';
$zoom = $settings['map_zoom'] ?? '16';
$yandexWidget = sprintf(
    'https://yandex.ru/map-widget/v1/?ll=%s%%2C%s&z=%s&pt=%s%%2C%s%%2Cpm2rdm',
    rawurlencode($lng), rawurlencode($lat), rawurlencode($zoom), rawurlencode($lng), rawurlencode($lat)
);
$yandexLink = sprintf('https://yandex.ru/maps/?ll=%s%%2C%s&z=%s&pt=%s%%2C%s', rawurlencode($lng), rawurlencode($lat), rawurlencode($zoom), rawurlencode($lng), rawurlencode($lat));
$gisLink    = 'https://2gis.ru/novokuznetsk/search/' . rawurlencode($settings['address'] ?? 'Новокузнецк');
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?= u('/') ?>">Главная</a><span>/</span><span>Контакты</span>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title">Контакты</h1>
        <p class="page-head__text">Магазин и пункт самовывоза в Новокузнецке. Звоните — поможем выбрать и соберём заказ.</p>
    </header>

    <div class="contacts">
        <div class="contacts__cards">
            <div class="contact-card">
                <span aria-hidden="true">📞</span>
                <h2>Телефоны</h2>
                <a href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
                <?php if (!empty($settings['phone_extra'])): ?>
                    <a href="<?= e(phone_link($settings['phone_extra'])) ?>"><?= e($settings['phone_extra']) ?></a>
                <?php endif; ?>
            </div>
            <div class="contact-card">
                <span aria-hidden="true">📍</span>
                <h2>Адрес</h2>
                <p><?= e($settings['address'] ?? '') ?></p>
                <p class="contact-card__muted"><?= e($settings['work_hours'] ?? '') ?></p>
            </div>
            <div class="contact-card">
                <span aria-hidden="true">✉️</span>
                <h2>Почта и мессенджеры</h2>
                <a href="mailto:<?= e($settings['email'] ?? '') ?>"><?= e($settings['email'] ?? '') ?></a>
                <div class="contact-card__links">
                    <?php if (!empty($settings['telegram'])): ?><a href="<?= e($settings['telegram']) ?>" target="_blank" rel="noopener nofollow">Telegram</a><?php endif; ?>
                    <?php if (!empty($settings['whatsapp'])): ?><a href="<?= e($settings['whatsapp']) ?>" target="_blank" rel="noopener nofollow">WhatsApp</a><?php endif; ?>
                    <?php if (!empty($settings['vk'])): ?><a href="<?= e($settings['vk']) ?>" target="_blank" rel="noopener nofollow">ВКонтакте</a><?php endif; ?>
                </div>
            </div>
            <div class="contact-card">
                <span aria-hidden="true">🏢</span>
                <h2>Реквизиты</h2>
                <p><?= e($settings['legal_name'] ?? '') ?></p>
                <?php if (!empty($settings['inn'])): ?><p class="contact-card__muted">ИНН <?= e($settings['inn']) ?></p><?php endif; ?>
            </div>
        </div>

        <div class="contacts__map">
            <div class="map">
                <iframe src="<?= e($yandexWidget) ?>"
                        title="Карта: <?= e($settings['address'] ?? 'Новокузнецк') ?>"
                        width="100%" height="460" frameborder="0" allowfullscreen loading="lazy"></iframe>
            </div>
            <div class="map__links">
                <a class="btn btn--ghost btn--sm" href="<?= e($yandexLink) ?>" target="_blank" rel="noopener">Открыть в Яндекс.Картах</a>
                <a class="btn btn--ghost btn--sm" href="<?= e($gisLink) ?>" target="_blank" rel="noopener">Найти в 2ГИС</a>
            </div>
        </div>
    </div>

    <section class="section" id="feedback">
        <div class="panel panel--wide">
            <h2 class="panel__title">Задать вопрос</h2>
            <p class="panel__text">Оставьте телефон — перезвоним в рабочее время и ответим на любой вопрос о продуктах и доставке.</p>
            <form class="form-grid" method="post" action="<?= u('/feedback') ?>">
                <?= csrf_field() ?>
                <label class="field">
                    <span>Как к вам обращаться *</span>
                    <input type="text" name="name" required maxlength="120" value="<?= old('name') ?>">
                </label>
                <label class="field">
                    <span>Телефон *</span>
                    <input type="tel" name="phone" required value="<?= old('phone') ?>" data-phone placeholder="+7 (923) 000-00-00">
                </label>
                <label class="field field--full">
                    <span>E-mail (если удобнее ответить письмом)</span>
                    <input type="email" name="email" value="<?= old('email') ?>" placeholder="mail@example.ru">
                </label>
                <label class="field field--full">
                    <span>Вопрос *</span>
                    <textarea name="message" rows="4" required maxlength="2000"><?= old('message') ?></textarea>
                </label>
                <div class="field field--full">
                    <button class="btn btn--primary btn--lg" type="submit">Отправить</button>
                </div>
            </form>
        </div>
    </section>
</div>

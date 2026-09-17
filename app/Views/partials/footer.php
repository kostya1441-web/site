<?php $settings = $settings ?? []; ?>
<footer class="footer">
    <div class="container footer__grid">
        <div class="footer__col">
            <a class="logo logo--footer" href="<?= u('/') ?>">
                <span class="logo__mark" aria-hidden="true">🌾</span>
                <span class="logo__text"><strong>Ваш фермер</strong><small>Новокузнецк</small></span>
            </a>
            <p class="footer__about">Фермерское мясо, птица, таёжная ягода и домашние заготовки с доставкой по Новокузнецку.</p>
            <div class="footer__social">
                <?php if (!empty($settings['telegram'])): ?><a href="<?= e($settings['telegram']) ?>" rel="nofollow noopener" target="_blank">Telegram</a><?php endif; ?>
                <?php if (!empty($settings['whatsapp'])): ?><a href="<?= e($settings['whatsapp']) ?>" rel="nofollow noopener" target="_blank">WhatsApp</a><?php endif; ?>
                <?php if (!empty($settings['vk'])): ?><a href="<?= e($settings['vk']) ?>" rel="nofollow noopener" target="_blank">ВКонтакте</a><?php endif; ?>
            </div>
        </div>

        <div class="footer__col">
            <h3>Каталог</h3>
            <ul>
                <?php foreach (\App\Models\Category::active() as $category): ?>
                    <li><a href="<?= u('/') ?>catalog/<?= e($category['slug']) ?>"><?= e($category['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer__col">
            <h3>Покупателям</h3>
            <ul>
                <li><a href="<?= u('/delivery') ?>">Доставка и оплата</a></li>
                <li><a href="<?= u('/about') ?>">О хозяйстве</a></li>
                <li><a href="<?= u('/contacts') ?>">Контакты</a></li>
                <li><a href="<?= u('/cart') ?>">Корзина</a></li>
            </ul>
        </div>

        <div class="footer__col">
            <h3>Контакты</h3>
            <ul class="footer__contacts">
                <li><a href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a></li>
                <?php if (!empty($settings['phone_extra'])): ?>
                    <li><a href="<?= e(phone_link($settings['phone_extra'])) ?>"><?= e($settings['phone_extra']) ?></a></li>
                <?php endif; ?>
                <li><a href="mailto:<?= e($settings['email'] ?? '') ?>"><?= e($settings['email'] ?? '') ?></a></li>
                <li><?= e($settings['address'] ?? '') ?></li>
                <li><?= e($settings['work_hours'] ?? '') ?></li>
            </ul>
        </div>
    </div>

    <div class="container footer__bottom">
        <span>© <?= date('Y') ?> «Ваш фермер». <?= e($settings['legal_name'] ?? '') ?><?= !empty($settings['inn']) ? ', ИНН ' . e($settings['inn']) : '' ?></span>
        <span class="footer__pay">
            Оплата картой онлайн через Сбербанк
            <span class="paycards" aria-hidden="true"><i>MIR</i><i>VISA</i><i>MC</i></span>
        </span>
    </div>
</footer>

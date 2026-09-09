<?php
require_once __DIR__ . '/functions.php';
$siteName = setting('site_name', 'Ваш фермер');
?>
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-col footer-brand">
            <div class="logo footer-logo"><span class="logo-badge"><?= icon('leaf') ?></span> <?= e($siteName) ?></div>
            <p><?= e(setting('site_tagline')) ?></p>
            <div class="footer-badges">
                <span class="footer-badge"><span class="ico"><?= icon('truck') ?></span> Доставка по городу</span>
                <span class="footer-badge"><span class="ico"><?= icon('shield') ?></span> Оплата через Сбербанк</span>
            </div>
        </div>
        <div class="footer-col">
            <h4>Контакты</h4>
            <p><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('phone'))) ?>"><span class="ico"><?= icon('phone') ?></span><?= e(setting('phone')) ?></a></p>
            <p><a href="mailto:<?= e(setting('email')) ?>"><span class="ico"><?= icon('mail') ?></span><?= e(setting('email')) ?></a></p>
            <p><span class="ico"><?= icon('pin') ?></span><?= e(setting('address')) ?></p>
            <p><span class="ico"><?= icon('clock') ?></span><?= e(setting('work_hours')) ?></p>
        </div>
        <div class="footer-col">
            <h4>Информация</h4>
            <p><a href="/about.php">О нас</a></p>
            <p><a href="/contacts.php">Контакты</a></p>
            <p><a href="/catalog.php">Каталог товаров</a></p>
            <p><a href="/cart.php">Корзина</a></p>
        </div>
        <?php
        $socials = array_filter([
            'ВК' => setting('vk_link'),
            'IG' => setting('instagram_link'),
            'WA' => setting('whatsapp_link'),
        ]);
        ?>
        <div class="footer-col">
            <h4>Мы в соцсетях</h4>
            <?php if (!empty($socials)): ?>
                <div class="footer-social">
                    <?php foreach ($socials as $label => $link): ?>
                        <a href="<?= e($link) ?>" target="_blank" rel="noopener" class="footer-social-badge"><?= e($label) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Свяжитесь с нами по телефону или email.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            &copy; <?= date('Y') ?> <?= e($siteName) ?>. Все права защищены.
        </div>
    </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>

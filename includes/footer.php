<?php
require_once __DIR__ . '/functions.php';
$siteName = setting('site_name', 'Ваш фермер');
?>
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-col">
            <div class="logo footer-logo"><span class="logo-icon">🌾</span> <?= e($siteName) ?></div>
            <p><?= e(setting('site_tagline')) ?></p>
        </div>
        <div class="footer-col">
            <h4>Контакты</h4>
            <p>Телефон: <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
            <p>Email: <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
            <p>Адрес: <?= e(setting('address')) ?></p>
            <p><?= e(setting('work_hours')) ?></p>
        </div>
        <div class="footer-col">
            <h4>Информация</h4>
            <p><a href="/about.php">О нас</a></p>
            <p><a href="/contacts.php">Контакты</a></p>
            <p><a href="/catalog.php">Каталог товаров</a></p>
        </div>
        <?php
        $socials = array_filter([
            'ВКонтакте' => setting('vk_link'),
            'Instagram' => setting('instagram_link'),
            'WhatsApp'  => setting('whatsapp_link'),
        ]);
        if (!empty($socials)):
        ?>
        <div class="footer-col">
            <h4>Мы в соцсетях</h4>
            <?php foreach ($socials as $label => $link): ?>
                <p><a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($label) ?></a></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="footer-bottom">
        <div class="container">
            &copy; <?= date('Y') ?> <?= e($siteName) ?>. Все права защищены. Оплата картой через Сбербанк.
        </div>
    </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>

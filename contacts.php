<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Контакты';

$coords = setting('map_coords', '53.786589,87.155207');
$mapSrc = 'https://yandex.ru/map-widget/v1/?ll=' . rawurlencode($coords)
    . '&z=15&pt=' . rawurlencode($coords) . ',pm2rdm';

require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Контакты</h1>
        <div class="contacts-grid">
            <div class="contacts-info">
                <h3>Как с нами связаться</h3>
                <p><strong>Телефон:</strong> <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
                <p><strong>Email:</strong> <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
                <p><strong>Адрес:</strong> <?= e(setting('address')) ?></p>
                <p><strong>Режим работы:</strong> <?= e(setting('work_hours')) ?></p>

                <?php
                $socials = array_filter([
                    'ВКонтакте' => setting('vk_link'),
                    'Instagram' => setting('instagram_link'),
                    'WhatsApp'  => setting('whatsapp_link'),
                ]);
                ?>
                <?php if (!empty($socials)): ?>
                    <h3>Мы в соцсетях</h3>
                    <?php foreach ($socials as $label => $link): ?>
                        <p><a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($label) ?></a></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div>
                <iframe class="map-frame" src="<?= e($mapSrc) ?>" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

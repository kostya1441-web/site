<?php
/** @var array $settings */
/** @var bool $sberReady */
/** @var bool $sberTest */
$s = static fn (string $key) => e((string) ($settings[$key] ?? ''));
?>
<div class="admin-head">
    <div>
        <h1>Настройки сайта</h1>
        <p>Контакты, тексты и карта — меняются без правки кода</p>
    </div>
</div>

<div class="panel <?= $sberReady ? 'panel--ok' : 'panel--warn' ?>">
    <h2 class="panel__title">💳 Эквайринг Сбербанка</h2>
    <?php if ($sberReady): ?>
        <p>Онлайн-оплата подключена. Режим: <strong><?= $sberTest ? 'тестовый контур (3dsec.sberbank.ru)' : 'боевой (securepayments.sberbank.ru)' ?></strong>.</p>
    <?php else: ?>
        <p>Онлайн-оплата не настроена — покупателям доступна только оплата при получении.</p>
    <?php endif; ?>
    <p class="muted small">
        Доступы к API задаются в файле <code>config/config.local.php</code> (или через переменные окружения
        <code>SBER_USER</code>, <code>SBER_PASS</code>, <code>SBER_CALLBACK_TOKEN</code>, <code>SBER_TEST</code>) —
        так реквизиты не попадают в базу и в репозиторий.
        Адрес для callback-уведомлений в личном кабинете банка: <code><?= e(rtrim((string) \App\Core\Config::get('app.url'), '/')) ?>/payment/callback</code>
    </p>
</div>

<form method="post" action="/admin/settings" class="admin-cols admin-cols--2-1">
    <?= csrf_field() ?>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Контакты</h2>
            <div class="form-grid">
                <label class="field"><span>Телефон основной</span><input type="text" name="phone" value="<?= $s('phone') ?>"></label>
                <label class="field"><span>Телефон дополнительный</span><input type="text" name="phone_extra" value="<?= $s('phone_extra') ?>"></label>
                <label class="field"><span>E-mail для покупателей</span><input type="email" name="email" value="<?= $s('email') ?>"></label>
                <label class="field"><span>E-mail для уведомлений о заказах</span><input type="email" name="notify_email" value="<?= $s('notify_email') ?>"></label>
                <label class="field field--full"><span>Адрес магазина</span><input type="text" name="address" value="<?= $s('address') ?>"></label>
                <label class="field field--full"><span>Режим работы</span><input type="text" name="work_hours" value="<?= $s('work_hours') ?>"></label>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel__title">Карта</h2>
            <p class="muted small">Координаты точки на Яндекс.Картах. Подсмотреть можно в адресной строке карт: сначала широта, затем долгота.</p>
            <div class="form-grid form-grid--3">
                <label class="field"><span>Широта</span><input type="text" name="map_lat" value="<?= $s('map_lat') ?>"></label>
                <label class="field"><span>Долгота</span><input type="text" name="map_lng" value="<?= $s('map_lng') ?>"></label>
                <label class="field"><span>Масштаб (10–18)</span><input type="number" name="map_zoom" min="5" max="19" value="<?= $s('map_zoom') ?>"></label>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel__title">Тексты</h2>
            <label class="field"><span>Заголовок сайта (title главной)</span><input type="text" name="site_title" value="<?= $s('site_title') ?>"></label>
            <label class="field"><span>Описание сайта (meta description)</span><textarea name="site_description" rows="2"><?= $s('site_description') ?></textarea></label>
            <label class="field"><span>Условия доставки (короткой строкой)</span><textarea name="delivery_text" rows="2"><?= $s('delivery_text') ?></textarea></label>
            <label class="field"><span>О нас — вводный абзац</span><textarea name="about_lead" rows="3"><?= $s('about_lead') ?></textarea></label>
            <label class="field"><span>О нас — основной текст</span><textarea name="about_text" rows="8"><?= $s('about_text') ?></textarea></label>
        </section>
    </div>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Сохранение</h2>
            <button class="btn btn--primary btn--block btn--lg" type="submit">Сохранить настройки</button>
        </section>

        <section class="panel">
            <h2 class="panel__title">Соцсети</h2>
            <label class="field"><span>Telegram</span><input type="url" name="telegram" value="<?= $s('telegram') ?>"></label>
            <label class="field"><span>WhatsApp</span><input type="url" name="whatsapp" value="<?= $s('whatsapp') ?>"></label>
            <label class="field"><span>ВКонтакте</span><input type="url" name="vk" value="<?= $s('vk') ?>"></label>
        </section>

        <section class="panel">
            <h2 class="panel__title">Реквизиты</h2>
            <label class="field"><span>Юридическое лицо</span><input type="text" name="legal_name" value="<?= $s('legal_name') ?>"></label>
            <label class="field"><span>ИНН</span><input type="text" name="inn" value="<?= $s('inn') ?>"></label>
        </section>
    </div>
</form>

<section class="panel panel--wide">
    <h2 class="panel__title">Смена пароля администратора</h2>
    <form method="post" action="/admin/settings/password" class="form-grid form-grid--3">
        <?= csrf_field() ?>
        <label class="field"><span>Текущий пароль</span><input type="password" name="current_password" required autocomplete="current-password"></label>
        <label class="field"><span>Новый пароль</span><input type="password" name="new_password" required minlength="8" autocomplete="new-password"></label>
        <label class="field"><span>Повторите новый пароль</span><input type="password" name="repeat_password" required minlength="8" autocomplete="new-password"></label>
        <div class="field field--full"><button class="btn btn--ghost" type="submit">Обновить пароль</button></div>
    </form>
</section>

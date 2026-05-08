<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle  = 'Правила сервера — ' . SITE_NAME;
$activePage = 'rules';

require_once __DIR__ . '/includes/db.php';
$db = getDB();
$custom_rules = null;
if ($db) {
    $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key`='rules_content'");
    $stmt->execute();
    $val = $stmt->fetchColumn();
    if ($val) $custom_rules = $val;
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1 class="page-title">Правила сервера</h1>
        <p class="page-sub">Соблюдение правил обязательно для всех игроков. Незнание правил не освобождает от ответственности.</p>
    </div>
</section>

<section class="section">
    <div class="container rules-container">

        <div class="rules-nav">
            <a href="#general" class="rules-nav-link">Общие правила</a>
            <a href="#chat" class="rules-nav-link">Чат и общение</a>
            <a href="#gameplay" class="rules-nav-link">Геймплей</a>
            <a href="#cheats" class="rules-nav-link">Читерство</a>
            <a href="#admin" class="rules-nav-link">Администрация</a>
            <a href="#punishments" class="rules-nav-link">Наказания</a>
        </div>

        <?php if ($custom_rules): ?>
        <div class="rules-content">
            <div class="rules-section">
                <?= $custom_rules /* Admin-managed HTML content */ ?>
            </div>
        </div>
        <?php else: ?>
        <div class="rules-content">

            <div class="rules-section" id="general">
                <h2>1. Общие правила</h2>
                <ol class="rules-list">
                    <li>Уважайте других игроков и администрацию.</li>
                    <li>Запрещены оскорбления по национальному, расовому или религиозному признаку.</li>
                    <li>Запрещена реклама других серверов и ресурсов.</li>
                    <li>Никнейм не должен содержать нецензурной лексики или оскорбительных слов.</li>
                    <li>Запрещено использование ников, вводящих в заблуждение (имитация администратора).</li>
                </ol>
            </div>

            <div class="rules-section" id="chat">
                <h2>2. Правила чата и голосового общения</h2>
                <ol class="rules-list">
                    <li>Запрещён спам и флуд в чате (повторение одного сообщения).</li>
                    <li>Запрещена нецензурная лексика в голосовом чате и тексте.</li>
                    <li>Запрещено включать музыку или посторонние звуки через микрофон.</li>
                    <li>Запрещена провокация и разжигание конфликтов.</li>
                    <li>Запрещены ссылки на сторонние ресурсы без разрешения администрации.</li>
                </ol>
            </div>

            <div class="rules-section" id="gameplay">
                <h2>3. Правила геймплея</h2>
                <ol class="rules-list">
                    <li>Запрещён гриф (намеренный урон союзникам, блокировка, мешание командным действиям).</li>
                    <li>Запрещён AFK более 3 раундов подряд без уважительной причины.</li>
                    <li>Запрещено намеренное поддавание (слив раундов, передача оружия врагу).</li>
                    <li>Запрещено использование багов карты для получения преимущества.</li>
                    <li>Запрещена стрельба по союзникам умышленно.</li>
                </ol>
            </div>

            <div class="rules-section rules-section--danger" id="cheats">
                <h2>4. Читерство и нечестная игра</h2>
                <ol class="rules-list">
                    <li><strong>Любые читы — перманентный бан без обжалования.</strong></li>
                    <li>Запрещены инжекторы, скрипты кликеров, bhop-скрипты.</li>
                    <li>Запрещено использование стороннего ПО для получения преимущества (aimbot, wallhack, triggerbot и т.д.).</li>
                    <li>Запрещена передача аккаунта с читами другому лицу для обхода бана.</li>
                    <li>Игра с заведомо забаненного IP без разрешения администрации запрещена.</li>
                </ol>
            </div>

            <div class="rules-section" id="admin">
                <h2>5. Взаимодействие с администрацией</h2>
                <ol class="rules-list">
                    <li>Решения администратора на сервере обязательны к исполнению.</li>
                    <li>Обжалование решений происходит только через сайт или Discord, не в игре.</li>
                    <li>Запрещено провоцировать администраторов или пытаться подкупить их.</li>
                    <li>Жалобы принимаются с доказательствами (демо, скриншоты).</li>
                </ol>
            </div>

            <div class="rules-section" id="punishments">
                <h2>6. Система наказаний</h2>
                <div class="punishments-table-wrap">
                    <table class="punishments-table">
                        <thead>
                            <tr><th>Нарушение</th><th>1-е</th><th>2-е</th><th>3-е и более</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Спам / флуд</td><td>Предупреждение</td><td>Мут 30 мин</td><td>Мут 24 ч</td></tr>
                            <tr><td>Оскорбления</td><td>Мут 1 ч</td><td>Мут 24 ч</td><td>Бан 7 дней</td></tr>
                            <tr><td>Гриф</td><td>Предупреждение</td><td>Бан 1 день</td><td>Бан 7 дней</td></tr>
                            <tr><td>AFK</td><td>Кик</td><td>Бан 1 ч</td><td>Бан 24 ч</td></tr>
                            <tr><td>Реклама</td><td>Бан 7 дней</td><td>Перм-бан</td><td>—</td></tr>
                            <tr class="row-danger"><td>Читы</td><td colspan="3">Перманентный бан</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="rules-note">Администрация оставляет за собой право изменять меру наказания в зависимости от тяжести нарушения.</p>
            </div>

        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

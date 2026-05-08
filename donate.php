<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = 'Магазин привилегий — ' . SITE_NAME;
$activePage = 'donate';

$success = isset($_GET['success']);
$error   = isset($_GET['error']);

// Обработка формы покупки
$order_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package'])) {
    $pkg_id = $_POST['package'] ?? '';
    $steam  = trim($_POST['steam'] ?? '');
    $pkg    = null;
    foreach (DONATE_PACKAGES as $p) {
        if ($p['id'] === $pkg_id) { $pkg = $p; break; }
    }
    if (!$pkg) {
        $order_error = 'Неверный пакет.';
    } elseif (!preg_match('/^STEAM_\d+:\d+:\d+$/', $steam) && !preg_match('/^7656\d{13}$/', $steam)) {
        $order_error = 'Введите корректный SteamID (STEAM_0:X:XXXXX) или Steam64 ID.';
    } else {
        // Здесь будет создание платежа YooKassa / Lava
        // Пример с YooKassa:
        /*
        require_once __DIR__ . '/vendor/autoload.php';
        $client = new \YooKassa\Client();
        $client->setAuth(PAYMENT_SHOP_ID, PAYMENT_SECRET);
        $payment = $client->createPayment([
            'amount'      => ['value' => $pkg['price'], 'currency' => 'RUB'],
            'description' => SITE_NAME . ' — ' . $pkg['name'] . ' для ' . $steam,
            'confirmation'=> ['type' => 'redirect', 'return_url' => PAYMENT_RETURN_URL],
            'metadata'    => ['steam' => $steam, 'package' => $pkg_id],
        ], uniqid('', true));
        header('Location: ' . $payment->getConfirmation()->getConfirmationUrl());
        exit;
        */
        // Пока редиректим на страницу-заглушку
        header('Location: /donate.php?pending=1&pkg=' . urlencode($pkg['name']) . '&steam=' . urlencode($steam));
        exit;
    }
}

$pending = isset($_GET['pending']);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1 class="page-title">Магазин привилегий</h1>
        <p class="page-sub">Поддержи сервер и получи уникальные возможности. Активация в течение 5 минут после оплаты.</p>
    </div>
</section>

<?php if ($success): ?>
<div class="container"><div class="alert alert-success">✅ Оплата прошла успешно! Привилегия будет активирована в течение нескольких минут.</div></div>
<?php elseif ($pending): ?>
<div class="container"><div class="alert alert-info">⏳ Заказ оформлен: <strong><?= h($_GET['pkg'] ?? '') ?></strong> для <strong><?= h($_GET['steam'] ?? '') ?></strong>. Ожидайте подтверждение платежа.</div></div>
<?php elseif ($error): ?>
<div class="container"><div class="alert alert-error">❌ Ошибка при оплате. Попробуйте ещё раз или обратитесь в поддержку.</div></div>
<?php endif; ?>

<?php if ($order_error): ?>
<div class="container"><div class="alert alert-error">❌ <?= h($order_error) ?></div></div>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="donate-grid">
            <?php foreach (DONATE_PACKAGES as $pkg): ?>
            <div class="donate-card <?= $pkg['id'] === 'premium' ? 'donate-card--popular' : '' ?>" style="--pkg-color: <?= $pkg['color'] ?>">
                <?php if ($pkg['id'] === 'premium'): ?>
                <div class="popular-badge">Популярное</div>
                <?php endif; ?>

                <div class="donate-card-header">
                    <h3 class="donate-pkg-name" style="color: <?= $pkg['color'] ?>"><?= h($pkg['name']) ?></h3>
                    <div class="donate-pkg-price">
                        <span class="price-amount"><?= $pkg['price'] ?> ₽</span>
                        <span class="price-period">/ <?= h($pkg['duration']) ?></span>
                    </div>
                </div>

                <ul class="donate-perks">
                    <?php foreach ($pkg['perks'] as $perk): ?>
                    <li><span class="perk-check">✓</span> <?= h($perk) ?></li>
                    <?php endforeach; ?>
                </ul>

                <form method="post" action="/donate.php" class="donate-form">
                    <input type="hidden" name="package" value="<?= h($pkg['id']) ?>">
                    <input type="text" name="steam"
                           placeholder="Ваш SteamID или Steam64"
                           class="steam-input"
                           required
                           pattern="(STEAM_\d+:\d+:\d+|7656\d{13})">
                    <small class="input-hint">Пример: STEAM_0:1:12345678 или 76561198XXXXXXXXX</small>
                    <button type="submit" class="btn btn-primary btn-block" style="background: <?= $pkg['color'] ?>; border-color: <?= $pkg['color'] ?>">
                        Купить <?= h($pkg['name']) ?>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Платёжные системы -->
        <div class="payment-methods">
            <p>Способы оплаты:</p>
            <div class="payment-icons">
                <span class="payment-icon">💳 Карты РФ</span>
                <span class="payment-icon">📱 СБП</span>
                <span class="payment-icon">💼 ЮMoney</span>
                <span class="payment-icon">🏦 Сбербанк</span>
            </div>
        </div>

        <!-- FAQ -->
        <div class="donate-faq">
            <h2 class="section-title">Часто задаваемые вопросы</h2>
            <div class="faq-list">
                <details class="faq-item">
                    <summary>Как быстро активируется привилегия?</summary>
                    <p>После подтверждения платежа — в течение 1-5 минут. Если прошло более 15 минут, напишите в поддержку.</p>
                </details>
                <details class="faq-item">
                    <summary>Где найти свой SteamID?</summary>
                    <p>Откройте Steam → Аккаунт → Данные аккаунта. Или введите свой никнейм на <strong>steamid.io</strong>.</p>
                </details>
                <details class="faq-item">
                    <summary>Можно ли вернуть деньги?</summary>
                    <p>Возврат осуществляется в течение 24 часов если привилегия не была активирована. Напишите в поддержку.</p>
                </details>
                <details class="faq-item">
                    <summary>Можно ли продлить привилегию?</summary>
                    <p>Да, просто купите снова — срок добавится к текущему. Дни суммируются.</p>
                </details>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

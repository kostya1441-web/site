<?php /** @var array $order */ ?>
<div class="container">
    <div class="result result--fail">
        <span class="result__icon" aria-hidden="true">⚠️</span>
        <h1 class="result__title">Оплата не прошла</h1>
        <p class="result__lead">
            Заказ <strong><?= e($order['number']) ?></strong> сохранён, деньги не списаны.
            Попробуйте оплатить ещё раз или выберите оплату при получении — позвоните нам, и мы переоформим.
        </p>
        <div class="result__actions">
            <a class="btn btn--primary btn--lg" href="/checkout/retry?order=<?= urlencode($order['number']) ?>">Повторить оплату</a>
            <a class="btn btn--ghost btn--lg" href="<?= e(phone_link($settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
        </div>
    </div>
</div>

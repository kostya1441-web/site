<?php /** @var int $code */ ?>
<div class="container">
    <div class="result result--fail">
        <span class="result__icon" aria-hidden="true">🐄</span>
        <h1 class="result__title"><?= (int) $code ?> — <?= e($heading) ?></h1>
        <p class="result__lead"><?= e($message) ?></p>
        <div class="result__actions">
            <a class="btn btn--primary btn--lg" href="<?= u('/') ?>">На главную</a>
            <a class="btn btn--ghost btn--lg" href="<?= u('/catalog') ?>">В каталог</a>
        </div>
    </div>
</div>

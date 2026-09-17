<?php $flashes = $flashes ?? []; ?>
<?php if ($flashes): ?>
    <div class="container">
        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert--<?= e($flash['type']) ?>" role="status">
                <?= e($flash['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

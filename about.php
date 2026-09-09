<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'О нас';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">О компании «<?= e(setting('site_name')) ?>»</h1>
        <div class="about-content">
            <?= setting('about_text') /* доверенный контент, редактируется только в админке */ ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

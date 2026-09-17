<?php
/** @var array $pagination */
$page  = (int) $pagination['page'];
$pages = (int) $pagination['pages'];
if ($pages < 2) {
    return;
}
$query = $_GET;
$link  = static function (int $target) use ($query): string {
    $query['page'] = $target;
    return '?' . http_build_query($query);
};
$from = max(1, $page - 2);
$to   = min($pages, $page + 2);
?>
<nav class="pagination" aria-label="Постраничная навигация">
    <?php if ($page > 1): ?>
        <a class="pagination__item" href="<?= e($link($page - 1)) ?>" rel="prev">←</a>
    <?php endif; ?>

    <?php if ($from > 1): ?>
        <a class="pagination__item" href="<?= e($link(1)) ?>">1</a>
        <?php if ($from > 2): ?><span class="pagination__dots">…</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $from; $i <= $to; $i++): ?>
        <a class="pagination__item<?= $i === $page ? ' is-active' : '' ?>" href="<?= e($link($i)) ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($to < $pages): ?>
        <?php if ($to < $pages - 1): ?><span class="pagination__dots">…</span><?php endif; ?>
        <a class="pagination__item" href="<?= e($link($pages)) ?>"><?= $pages ?></a>
    <?php endif; ?>

    <?php if ($page < $pages): ?>
        <a class="pagination__item" href="<?= e($link($page + 1)) ?>" rel="next">→</a>
    <?php endif; ?>
</nav>

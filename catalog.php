<?php
require_once __DIR__ . '/includes/functions.php';

$catSlug = trim($_GET['cat'] ?? '');
$search = trim($_GET['q'] ?? '');

$currentCategory = $catSlug !== '' ? getCategoryBySlug($catSlug) : null;
if ($catSlug !== '' && !$currentCategory) {
    http_response_code(404);
}

$sql = 'SELECT p.*, c.slug AS category_slug FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1';
$params = [];

if ($currentCategory) {
    $sql .= ' AND p.category_id = :cid';
    $params['cid'] = $currentCategory['id'];
}
if ($search !== '') {
    $sql .= ' AND p.name LIKE :q';
    $params['q'] = '%' . $search . '%';
}
$sql .= ' ORDER BY p.sort_order, p.name';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = $currentCategory ? $currentCategory['name'] : 'Каталог товаров';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <div class="breadcrumbs">
            <a href="/index.php">Главная</a> /
            <?php if ($currentCategory): ?>
                <a href="/catalog.php">Каталог</a> / <?= e($currentCategory['name']) ?>
            <?php else: ?>
                Каталог
            <?php endif; ?>
        </div>

        <h1 class="section-title"><?= e($pageTitle) ?></h1>

        <form method="get" action="/catalog.php" style="max-width:420px;margin:0 auto 30px;display:flex;gap:8px;">
            <?php if ($currentCategory): ?><input type="hidden" name="cat" value="<?= e($currentCategory['slug']) ?>"><?php endif; ?>
            <input type="text" name="q" class="form-control" placeholder="Поиск товара..." value="<?= e($search) ?>">
            <button type="submit" class="btn">Найти</button>
        </form>

        <?php if (empty($products)): ?>
            <div class="empty-state">По вашему запросу товары не найдены.</div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <?php include __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$catFilter = (int)($_GET['cat'] ?? 0);
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE 1=1';
$params = [];
if ($catFilter > 0) {
    $sql .= ' AND p.category_id = :cat';
    $params['cat'] = $catFilter;
}
if ($search !== '') {
    $sql .= ' AND p.name LIKE :q';
    $params['q'] = '%' . $search . '%';
}
$sql .= ' ORDER BY c.sort_order, p.sort_order, p.name';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = db()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();

$pageTitle = 'Товары';
$activeNav = 'products';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <form method="get" action="/admin/products.php" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="q" class="form-control" placeholder="Поиск товара" value="<?= e($search) ?>" style="min-width:200px;">
            <select name="cat" class="form-control" style="min-width:180px;">
                <option value="0">Все категории</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= $catFilter === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline">Найти</button>
        </form>
        <a href="/admin/product_form.php" class="btn">+ Добавить товар</a>
    </div>
</div>

<div class="admin-card">
    <div class="table-scroll">
    <table class="admin-table">
        <thead>
        <tr><th>Фото</th><th>Название</th><th>Категория</th><th>Цена</th><th>В наличии</th><th>Активен</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img class="thumb" src="<?= e(imageUrl($p['image'])) ?>" alt=""></td>
                <td><?= e($p['name']) ?></td>
                <td><?= e($p['category_name']) ?></td>
                <td><?= formatPrice($p['price']) ?> / <?= e($p['unit']) ?></td>
                <td><?= $p['in_stock'] ? 'Да' : 'Нет' ?></td>
                <td><?= $p['is_active'] ? 'Да' : 'Нет' ?></td>
                <td>
                    <a href="/admin/product_form.php?id=<?= (int)$p['id'] ?>">Изменить</a> ·
                    <a href="/admin/product_delete.php?id=<?= (int)$p['id'] ?>&csrf_token=<?= e(csrfToken()) ?>"
                       onclick="return confirm('Удалить товар «<?= e($p['name']) ?>»?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
            <tr><td colspan="7">Товары не найдены.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

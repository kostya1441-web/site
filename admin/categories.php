<?php
require_once __DIR__ . '/includes/admin_auth.php';
requireAdminLogin();

$categories = db()->query(
    'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS products_count
     FROM categories c ORDER BY c.sort_order, c.name'
)->fetchAll();

$pageTitle = 'Категории';
$activeNav = 'categories';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-card">
    <a href="/admin/category_form.php" class="btn">+ Добавить категорию</a>
</div>

<div class="admin-card">
    <div class="table-scroll">
    <table class="admin-table">
        <thead><tr><th>Фото</th><th>Название</th><th>Товаров</th><th>Активна</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><img class="thumb" src="<?= e(imageUrl($c['image'], 'category')) ?>" alt=""></td>
                <td><?= e($c['name']) ?></td>
                <td><?= (int)$c['products_count'] ?></td>
                <td><?= $c['is_active'] ? 'Да' : 'Нет' ?></td>
                <td>
                    <a href="/admin/category_form.php?id=<?= (int)$c['id'] ?>">Изменить</a> ·
                    <a href="/admin/category_delete.php?id=<?= (int)$c['id'] ?>&csrf_token=<?= e(csrfToken()) ?>"
                       onclick="return confirm('Удалить категорию «<?= e($c['name']) ?>»? Все товары в ней также будут удалены!')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($categories)): ?>
            <tr><td colspan="5">Категории не найдены.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

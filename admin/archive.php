<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$search = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM orders WHERE archived_at IS NOT NULL';
$params = [];
if ($search !== '') {
    $sql .= ' AND (order_number LIKE :q OR customer_name LIKE :q OR phone LIKE :q)';
    $params['q'] = '%' . $search . '%';
}
$sql .= ' ORDER BY archived_at DESC LIMIT 200';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = 'Архив заказов';
$activeNav = 'archive';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-card">
    <form method="get" action="/admin/archive.php" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
        <div class="form-group" style="margin:0;min-width:200px;">
            <label>Поиск</label>
            <input type="text" name="q" class="form-control" placeholder="№ заказа, имя, телефон" value="<?= e($search) ?>">
        </div>
        <button type="submit" class="btn">Показать</button>
        <a href="/admin/archive.php" class="btn btn-outline">Сбросить</a>
        <a href="/admin/orders.php" class="btn btn-outline" style="margin-left:auto;">← К активным заказам</a>
    </form>
</div>

<div class="admin-card">
    <p style="color:var(--a-muted); margin-top:0;">Заказы со статусом «Выполнен» автоматически попадают сюда. Их можно вернуть в активный список на странице заказа.</p>
    <div class="table-scroll">
    <table class="admin-table">
        <thead>
        <tr><th>№ заказа</th><th>Клиент</th><th>Сумма</th><th>Оплата</th><th>Статус</th><th>В архиве с</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= e($o['order_number']) ?></td>
                <td><?= e($o['customer_name']) ?><br><small><?= e($o['phone']) ?></small></td>
                <td><?= formatPrice($o['total_amount']) ?></td>
                <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span></td>
                <td><span class="badge badge-<?= e($o['status']) ?>"><?= e(orderStatusLabel($o['status'])) ?></span></td>
                <td><?= e(date('d.m.Y H:i', strtotime($o['archived_at']))) ?></td>
                <td><a href="/admin/order_view.php?id=<?= (int)$o['id'] ?>">Открыть</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
            <tr><td colspan="7">В архиве пока нет заказов.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

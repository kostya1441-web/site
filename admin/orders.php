<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$statusFilter = trim($_GET['status'] ?? '');
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM orders WHERE 1=1';
$params = [];
if ($statusFilter !== '') {
    $sql .= ' AND status = :status';
    $params['status'] = $statusFilter;
}
if ($search !== '') {
    $sql .= ' AND (order_number LIKE :q OR customer_name LIKE :q OR phone LIKE :q)';
    $params['q'] = '%' . $search . '%';
}
$sql .= ' ORDER BY created_at DESC LIMIT 200';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['new' => 'Новый', 'processing' => 'В обработке', 'ready' => 'Готов к выдаче', 'shipped' => 'Передан в доставку', 'completed' => 'Выполнен', 'cancelled' => 'Отменён'];

$pageTitle = 'Заказы';
$activeNav = 'orders';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-card">
    <form method="get" action="/admin/orders.php" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
        <div class="form-group" style="margin:0;min-width:200px;">
            <label>Поиск</label>
            <input type="text" name="q" class="form-control" placeholder="№ заказа, имя, телефон" value="<?= e($search) ?>">
        </div>
        <div class="form-group" style="margin:0;min-width:180px;">
            <label>Статус</label>
            <select name="status" class="form-control">
                <option value="">Все статусы</option>
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $statusFilter === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn">Показать</button>
        <a href="/admin/orders.php" class="btn btn-outline">Сбросить</a>
    </form>
</div>

<div class="admin-card">
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead>
        <tr><th>№ заказа</th><th>Клиент</th><th>Сумма</th><th>Оплата</th><th>Статус</th><th>Дата</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= e($o['order_number']) ?></td>
                <td><?= e($o['customer_name']) ?><br><small><?= e($o['phone']) ?></small></td>
                <td><?= formatPrice($o['total_amount']) ?></td>
                <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span></td>
                <td><span class="badge badge-<?= e($o['status']) ?>"><?= e(orderStatusLabel($o['status'])) ?></span></td>
                <td><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></td>
                <td><a href="/admin/order_view.php?id=<?= (int)$o['id'] ?>">Открыть</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
            <tr><td colspan="7">Заказы не найдены.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

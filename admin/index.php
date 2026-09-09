<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$pdo = db();
$stats = [
    'new_orders'   => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'new' AND archived_at IS NULL")->fetchColumn(),
    'active_orders'=> (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE archived_at IS NULL")->fetchColumn(),
    'archived'     => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE archived_at IS NOT NULL")->fetchColumn(),
    'paid_today'   => (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid' AND DATE(created_at) = CURDATE()")->fetchColumn(),
    'products'     => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
];

$recentOrders = $pdo->query('SELECT * FROM orders WHERE archived_at IS NULL ORDER BY created_at DESC LIMIT 8')->fetchAll();

$pageTitle = 'Дашборд';
$activeNav = 'dashboard';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-stats">
    <div class="stat-box"><div class="stat-value"><?= $stats['new_orders'] ?></div><div class="stat-label">Новых заказов</div></div>
    <div class="stat-box"><div class="stat-value"><?= $stats['active_orders'] ?></div><div class="stat-label">Активных заказов</div></div>
    <div class="stat-box"><div class="stat-value"><?= formatPrice($stats['paid_today']) ?></div><div class="stat-label">Оплачено сегодня</div></div>
    <div class="stat-box"><div class="stat-value"><?= $stats['products'] ?></div><div class="stat-label">Товаров в каталоге</div></div>
    <div class="stat-box"><div class="stat-value"><?= $stats['archived'] ?></div><div class="stat-label">В архиве</div></div>
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Последние заказы</h3>
    <div class="table-scroll">
    <table class="admin-table">
        <thead>
        <tr><th>№</th><th>Клиент</th><th>Сумма</th><th>Оплата</th><th>Статус</th><th>Дата</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($recentOrders as $o): ?>
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
        <?php if (empty($recentOrders)): ?>
            <tr><td colspan="7">Заказов пока нет.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

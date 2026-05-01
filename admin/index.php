<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/../includes/functions.php';
admin_require();

// Stats
$totalOrders   = DB::fetch('SELECT COUNT(*) as c FROM orders')['c'];
$todayOrders   = DB::fetch('SELECT COUNT(*) as c FROM orders WHERE DATE(created_at)=CURDATE()')['c'];
$newOrders     = DB::fetch('SELECT COUNT(*) as c FROM orders WHERE status="new"')['c'];
$totalRevenue  = DB::fetch('SELECT COALESCE(SUM(total),0) as s FROM orders WHERE payment_status="paid"')['s'];
$totalBookings = DB::fetch('SELECT COUNT(*) as c FROM bookings')['c'];
$todayBookings = DB::fetch('SELECT COUNT(*) as c FROM bookings WHERE DATE(created_at)=CURDATE()')['c'];
$lastOrderId   = (int)(DB::fetch('SELECT MAX(id) as m FROM orders')['m'] ?? 0);

// Recent orders
$recentOrders = DB::fetchAll(
    'SELECT * FROM orders ORDER BY id DESC LIMIT 8'
);

// Upcoming events
$upcomingEvents = DB::fetchAll(
    'SELECT * FROM events WHERE active=1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 4'
);

$statusLabels = [
    'new' => 'Новый', 'preparing' => 'Готовится', 'ready' => 'Готов',
    'on_way' => 'В пути', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'
];

admin_head('Дашборд');
admin_topbar('Дашборд', 'Обзор системы');
?>

<span data-last-order-id="<?= $lastOrderId ?>"></span>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="icon">📦</div>
    <div class="label">Заказов сегодня</div>
    <div class="value"><?= $todayOrders ?></div>
    <div class="change">Всего: <?= $totalOrders ?></div>
  </div>
  <div class="stat-card">
    <div class="icon">🔴</div>
    <div class="label">Новых заказов</div>
    <div class="value"><?= $newOrders ?></div>
    <div class="change">Требуют обработки</div>
  </div>
  <div class="stat-card">
    <div class="icon">💰</div>
    <div class="label">Выручка (оплачено)</div>
    <div class="value"><?= number_format($totalRevenue, 0, '.', ' ') ?> ₽</div>
    <div class="change">Онлайн-платежи</div>
  </div>
  <div class="stat-card">
    <div class="icon">📅</div>
    <div class="label">Броней сегодня</div>
    <div class="value"><?= $todayBookings ?></div>
    <div class="change">Всего: <?= $totalBookings ?></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px">
  <!-- Recent orders -->
  <div class="table-card">
    <div class="table-card-header">
      <h3>Последние заказы</h3>
      <a href="/admin/orders.php" class="btn btn-outline btn-sm">Все заказы</a>
    </div>
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Клиент</th>
          <th>Тип</th>
          <th>Сумма</th>
          <th>Статус</th>
          <th>Время</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentOrders)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">Заказов пока нет</td></tr>
        <?php else: ?>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td style="color:var(--gold);font-weight:600">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($o['name']) ?></div>
            <div style="font-size:0.78rem;color:var(--text-muted)"><?= htmlspecialchars($o['phone']) ?></div>
          </td>
          <td><span class="badge badge-<?= $o['type'] ?>"><?= $o['type'] === 'delivery' ? '🚴 Доставка' : '🏠 Самовывоз' ?></span></td>
          <td style="color:var(--gold);font-weight:600"><?= number_format($o['total'], 0, '.', ' ') ?> ₽</td>
          <td><span class="badge badge-<?= $o['status'] ?> status-badge" data-id="<?= $o['id'] ?>"><?= $statusLabels[$o['status']] ?? $o['status'] ?></span></td>
          <td style="font-size:0.8rem;color:var(--text-muted)"><?= date('d.m H:i', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Upcoming events -->
  <div class="table-card">
    <div class="table-card-header">
      <h3>Ближайшие события</h3>
      <a href="/admin/events.php" class="btn btn-outline btn-sm">Все</a>
    </div>
    <?php if (empty($upcomingEvents)): ?>
      <div style="padding:32px;text-align:center;color:var(--text-muted)">Нет событий</div>
    <?php else: ?>
    <?php foreach ($upcomingEvents as $ev): ?>
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:flex-start">
      <div style="min-width:44px;text-align:center;background:rgba(200,145,90,0.1);border-radius:6px;padding:6px 4px;line-height:1.1">
        <div style="font-size:1.1rem;font-weight:700;color:var(--gold)"><?= date('d', strtotime($ev['event_date'])) ?></div>
        <div style="font-size:0.6rem;color:var(--text-muted);text-transform:uppercase"><?= date('M', strtotime($ev['event_date'])) ?></div>
      </div>
      <div>
        <div style="font-size:0.9rem;font-weight:500"><?= htmlspecialchars($ev['title']) ?></div>
        <div style="font-size:0.78rem;color:var(--text-muted)"><?= htmlspecialchars($ev['artist'] ?? '') ?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php admin_foot(); ?>

<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/../includes/functions.php';
admin_require();

$view        = $_GET['view'] ?? 'active'; // active | archive
$statusFilter = $_GET['status'] ?? '';
$typeFilter   = $_GET['type'] ?? '';

$activeStatuses  = ['new', 'preparing', 'ready', 'on_way'];
$archiveStatuses = ['delivered', 'cancelled'];

$sql = 'SELECT * FROM orders WHERE 1=1';
$params = [];

if ($view === 'archive') {
    if ($statusFilter && in_array($statusFilter, $archiveStatuses)) {
        $sql .= ' AND status=?'; $params[] = $statusFilter;
    } else {
        $placeholders = implode(',', array_fill(0, count($archiveStatuses), '?'));
        $sql .= " AND status IN ($placeholders)";
        $params = array_merge($params, $archiveStatuses);
    }
} else {
    if ($statusFilter && in_array($statusFilter, $activeStatuses)) {
        $sql .= ' AND status=?'; $params[] = $statusFilter;
    } else {
        $placeholders = implode(',', array_fill(0, count($activeStatuses), '?'));
        $sql .= " AND status IN ($placeholders)";
        $params = array_merge($params, $activeStatuses);
    }
}

if ($typeFilter) { $sql .= ' AND type=?'; $params[] = $typeFilter; }
$sql .= ' ORDER BY id DESC';

$orders = DB::fetchAll($sql, $params);

$statusLabels = [
    'new' => 'Новый', 'preparing' => 'Готовится', 'ready' => 'Готов',
    'on_way' => 'В пути', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'
];
$pickupStatuses   = ['new','preparing','ready','cancelled'];
$deliveryStatuses = ['new','preparing','on_way','delivered','cancelled'];

$lastOrderId = (int)(DB::fetch('SELECT MAX(id) as m FROM orders WHERE status IN ("new","preparing","ready","on_way")')['m'] ?? 0);

admin_head('Заказы');
admin_topbar('Заказы', $view === 'archive' ? 'Архив заказов' : 'Активные заказы');
?>

<span data-last-order-id="<?= $lastOrderId ?>"></span>

<!-- View tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;border-bottom:1px solid var(--border);padding-bottom:16px">
  <a href="/admin/orders.php?view=active"
     class="btn btn-sm <?= $view === 'active' ? 'btn-primary' : 'btn-outline' ?>">
    🔴 Активные
  </a>
  <a href="/admin/orders.php?view=archive"
     class="btn btn-sm <?= $view === 'archive' ? 'btn-primary' : 'btn-outline' ?>">
    📦 Архив
  </a>
</div>

<!-- Filters -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:center">
  <?php if ($view === 'active'): ?>
    <a href="/admin/orders.php?view=active" class="btn btn-sm <?= !$statusFilter && !$typeFilter ? 'btn-primary' : 'btn-outline' ?>">Все активные</a>
    <a href="/admin/orders.php?view=active&status=new"       class="btn btn-sm <?= $statusFilter==='new'       ? 'btn-primary' : 'btn-outline' ?>">Новые</a>
    <a href="/admin/orders.php?view=active&status=preparing" class="btn btn-sm <?= $statusFilter==='preparing' ? 'btn-primary' : 'btn-outline' ?>">Готовятся</a>
    <a href="/admin/orders.php?view=active&status=ready"     class="btn btn-sm <?= $statusFilter==='ready'     ? 'btn-primary' : 'btn-outline' ?>">Готовы</a>
    <a href="/admin/orders.php?view=active&status=on_way"    class="btn btn-sm <?= $statusFilter==='on_way'    ? 'btn-primary' : 'btn-outline' ?>">В пути</a>
  <?php else: ?>
    <a href="/admin/orders.php?view=archive" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline' ?>">Все архивные</a>
    <a href="/admin/orders.php?view=archive&status=delivered" class="btn btn-sm <?= $statusFilter==='delivered' ? 'btn-primary' : 'btn-outline' ?>">Доставлены</a>
    <a href="/admin/orders.php?view=archive&status=cancelled" class="btn btn-sm <?= $statusFilter==='cancelled' ? 'btn-primary' : 'btn-outline' ?>">Отменены</a>
  <?php endif; ?>
  <span style="color:var(--border)">|</span>
  <a href="/admin/orders.php?view=<?= $view ?>&type=pickup"   class="btn btn-sm <?= $typeFilter==='pickup'   ? 'btn-primary' : 'btn-outline' ?>">🏠 Самовывоз</a>
  <a href="/admin/orders.php?view=<?= $view ?>&type=delivery" class="btn btn-sm <?= $typeFilter==='delivery' ? 'btn-primary' : 'btn-outline' ?>">🚴 Доставка</a>
</div>

<div class="table-card">
  <div class="table-card-header">
    <h3>
      <?= $view === 'archive' ? 'Архив заказов' : 'Активные заказы' ?>
      <span style="color:var(--text-muted);font-size:0.85rem">(<?= count($orders) ?>)</span>
    </h3>
    <div class="table-search">
      <input type="text" placeholder="Поиск..." data-search-table="orders-table">
    </div>
  </div>
  <table class="admin-table" id="orders-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Клиент</th>
        <th>Тип</th>
        <th>Состав</th>
        <th>Сумма</th>
        <th>Оплата</th>
        <th>Статус</th>
        <th>Дата</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($orders)): ?>
      <tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:40px">Заказов нет</td></tr>
      <?php else: ?>
      <?php foreach ($orders as $o):
        $items = DB::fetchAll('SELECT * FROM order_items WHERE order_id=?', [$o['id']]);
        $statuses = $o['type'] === 'delivery' ? $deliveryStatuses : $pickupStatuses;
      ?>
      <tr>
        <td style="color:var(--gold);font-weight:700">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
        <td>
          <div style="font-weight:500"><?= htmlspecialchars($o['name']) ?></div>
          <a href="tel:<?= htmlspecialchars($o['phone']) ?>" style="font-size:0.8rem;color:var(--text-muted)"><?= htmlspecialchars($o['phone']) ?></a>
          <?php if ($o['address']): ?>
          <div style="font-size:0.78rem;color:var(--text-muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($o['address']) ?>"><?= htmlspecialchars($o['address']) ?></div>
          <?php endif; ?>
        </td>
        <td><span class="badge badge-<?= $o['type'] ?>"><?= $o['type'] === 'delivery' ? '🚴 Доставка' : '🏠 Самовывоз' ?></span></td>
        <td style="font-size:0.82rem">
          <?php foreach (array_slice($items, 0, 3) as $item): ?>
            <div><?= htmlspecialchars($item['name']) ?> × <?= $item['qty'] ?></div>
          <?php endforeach; ?>
          <?php if (count($items) > 3): ?>
            <div style="color:var(--text-muted)">+ещё <?= count($items)-3 ?></div>
          <?php endif; ?>
        </td>
        <td style="color:var(--gold);font-weight:700"><?= number_format($o['total'], 0, '.', ' ') ?> ₽</td>
        <td><span class="badge badge-<?= $o['payment_status'] ?>"><?= ['pending'=>'Ожидает','paid'=>'Оплачен','failed'=>'Ошибка','refunded'=>'Возврат'][$o['payment_status']] ?? $o['payment_status'] ?></span></td>
        <td>
          <span class="badge badge-<?= $o['status'] ?> status-badge" data-id="<?= $o['id'] ?>"><?= $statusLabels[$o['status']] ?></span>
        </td>
        <td style="font-size:0.8rem;color:var(--text-muted);white-space:nowrap"><?= date('d.m.y H:i', strtotime($o['created_at'])) ?></td>
        <td>
          <button class="btn btn-outline btn-sm" data-open-modal="order-modal-<?= $o['id'] ?>">Детали</button>
        </td>
      </tr>

      <!-- Order modal -->
      <div class="modal-overlay" id="order-modal-<?= $o['id'] ?>" style="display:none">
        <div class="modal">
          <div class="modal-header">
            <h3>Заказ #<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></h3>
            <button class="modal-close" data-close-modal="order-modal-<?= $o['id'] ?>">✕</button>
          </div>
          <div class="modal-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;font-size:0.88rem">
              <div><span style="color:var(--text-muted)">Клиент:</span> <strong><?= htmlspecialchars($o['name']) ?></strong></div>
              <div><span style="color:var(--text-muted)">Телефон:</span> <?= htmlspecialchars($o['phone']) ?></div>
              <div><span style="color:var(--text-muted)">Тип:</span> <?= $o['type'] === 'delivery' ? '🚴 Доставка' : '🏠 Самовывоз' ?></div>
              <div><span style="color:var(--text-muted)">Оплата:</span> <?= $o['payment_status'] ?></div>
              <?php if ($o['address']): ?>
              <div style="grid-column:span 2"><span style="color:var(--text-muted)">Адрес:</span> <?= htmlspecialchars($o['address']) ?></div>
              <?php endif; ?>
              <?php if ($o['comment']): ?>
              <div style="grid-column:span 2"><span style="color:var(--text-muted)">Комментарий:</span> <?= htmlspecialchars($o['comment']) ?></div>
              <?php endif; ?>
            </div>
            <hr style="border-color:var(--border);margin-bottom:16px">
            <ul class="order-items-list">
              <?php foreach ($items as $item): ?>
              <li>
                <span><?= htmlspecialchars($item['name']) ?> × <?= $item['qty'] ?></span>
                <span><?= number_format($item['price'] * $item['qty'], 0, '.', ' ') ?> ₽</span>
              </li>
              <?php endforeach; ?>
            </ul>
            <div class="order-total">
              <span>Итого</span>
              <span><?= number_format($o['total'], 0, '.', ' ') ?> ₽</span>
            </div>
            <hr style="border-color:var(--border);margin:16px 0">
            <div>
              <label style="margin-bottom:8px;display:block">Изменить статус:</label>
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <?php foreach ($statuses as $st): ?>
                <button class="btn btn-sm <?= $o['status'] === $st ? 'btn-primary' : 'btn-outline' ?>"
                  onclick="updateStatus('order', <?= $o['id'] ?>, '<?= $st ?>', this); closeModal('order-modal-<?= $o['id'] ?>')">
                  <?= $statusLabels[$st] ?>
                </button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php admin_foot(); ?>

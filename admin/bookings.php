<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/../includes/functions.php';
admin_require();

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT b.*, e.title as event_title FROM bookings b LEFT JOIN events e ON b.event_id=e.id WHERE 1=1';
$params = [];
if ($statusFilter) { $sql .= ' AND b.status=?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY b.id DESC';
$bookings = DB::fetchAll($sql, $params);

$statusLabels = ['new' => 'Новая', 'confirmed' => 'Подтверждена', 'cancelled' => 'Отменена'];

admin_head('Брони');
admin_topbar('Бронирования', 'Управление столами');
?>

<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
  <a href="/admin/bookings.php" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline' ?>">Все</a>
  <a href="/admin/bookings.php?status=new"       class="btn btn-sm <?= $statusFilter==='new' ? 'btn-primary' : 'btn-outline' ?>">Новые</a>
  <a href="/admin/bookings.php?status=confirmed" class="btn btn-sm <?= $statusFilter==='confirmed' ? 'btn-primary' : 'btn-outline' ?>">Подтверждённые</a>
  <a href="/admin/bookings.php?status=cancelled" class="btn btn-sm <?= $statusFilter==='cancelled' ? 'btn-primary' : 'btn-outline' ?>">Отменённые</a>
</div>

<div class="table-card">
  <div class="table-card-header">
    <h3>Брони <span style="color:var(--text-muted);font-size:0.85rem">(<?= count($bookings) ?>)</span></h3>
    <div class="table-search">
      <input type="text" placeholder="Поиск..." data-search-table="bookings-table">
    </div>
  </div>
  <table class="admin-table" id="bookings-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Клиент</th>
        <th>Дата и время</th>
        <th>Гостей</th>
        <th>Мероприятие</th>
        <th>Статус</th>
        <th>Заявка</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($bookings)): ?>
      <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px">Бронирований нет</td></tr>
      <?php else: ?>
      <?php foreach ($bookings as $b): ?>
      <tr>
        <td style="color:var(--gold);font-weight:600">#<?= $b['id'] ?></td>
        <td>
          <div style="font-weight:500"><?= htmlspecialchars($b['name']) ?></div>
          <a href="tel:<?= htmlspecialchars($b['phone']) ?>" style="font-size:0.8rem;color:var(--text-muted)"><?= htmlspecialchars($b['phone']) ?></a>
        </td>
        <td>
          <div style="font-weight:500"><?= date('d.m.Y', strtotime($b['booking_date'])) ?></div>
          <div style="font-size:0.8rem;color:var(--text-muted)"><?= substr($b['booking_time'], 0, 5) ?></div>
        </td>
        <td style="text-align:center;font-weight:600"><?= $b['guests'] ?></td>
        <td style="font-size:0.85rem"><?= $b['event_title'] ? htmlspecialchars($b['event_title']) : '<span style="color:var(--text-muted)">—</span>' ?></td>
        <td>
          <span class="badge badge-<?= $b['status'] ?> status-badge" data-id="<?= $b['id'] ?>">
            <?= $statusLabels[$b['status']] ?? $b['status'] ?>
          </span>
        </td>
        <td style="font-size:0.8rem;color:var(--text-muted)"><?= date('d.m H:i', strtotime($b['created_at'])) ?></td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn btn-green btn-sm"
              onclick="updateStatus('booking', <?= $b['id'] ?>, 'confirmed', this)"
              <?= $b['status'] === 'confirmed' ? 'disabled' : '' ?>>✓</button>
            <button class="btn btn-danger btn-sm"
              onclick="updateStatus('booking', <?= $b['id'] ?>, 'cancelled', this)"
              <?= $b['status'] === 'cancelled' ? 'disabled' : '' ?>>✕</button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php admin_foot(); ?>

<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/../includes/functions.php';
admin_require();

$alert = '';
$alertType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $artist      = trim($_POST['artist'] ?? '');
        $event_date  = trim($_POST['event_date'] ?? '');
        $event_time  = trim($_POST['event_time'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $seats_total = (int)($_POST['seats_total'] ?? 0);
        $active      = isset($_POST['active']) ? 1 : 0;

        if (!$title || !$event_date) {
            $alert = 'Заполните обязательные поля'; $alertType = 'error';
        } else {
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = upload_image($_FILES['image'], 'event');
            }
            $data = compact('title','description','artist','event_date','event_time','price','seats_total','active');
            if ($event_time) $data['event_time'] = $event_time . ':00';
            if ($image) $data['image'] = $image;

            if ($id) {
                DB::update('events', $data, 'id=?', [$id]);
                $alert = 'Мероприятие обновлено';
            } else {
                DB::insert('events', $data);
                $alert = 'Мероприятие добавлено';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        DB::query('DELETE FROM events WHERE id=?', [$id]);
        $alert = 'Мероприятие удалено';
    }
}

$events = DB::fetchAll('SELECT * FROM events ORDER BY event_date DESC');
$editEv = null;
if (isset($_GET['edit'])) {
    $editEv = DB::fetch('SELECT * FROM events WHERE id=?', [(int)$_GET['edit']]);
}

admin_head('Мероприятия');
admin_topbar('Мероприятия', 'Концерты и события');
?>

<?php if ($alert): ?>
<div class="alert alert-<?= $alertType ?>"><?= htmlspecialchars($alert) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">
  <!-- Events list -->
  <div class="table-card">
    <div class="table-card-header">
      <h3>Мероприятия <span style="color:var(--text-muted);font-size:0.85rem">(<?= count($events) ?>)</span></h3>
    </div>
    <table class="admin-table">
      <thead>
        <tr>
          <th></th>
          <th>Название</th>
          <th>Дата</th>
          <th>Исполнитель</th>
          <th>Цена</th>
          <th>Места</th>
          <th>Активно</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($events)): ?>
        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Мероприятий нет</td></tr>
        <?php else: ?>
        <?php foreach ($events as $ev): ?>
        <tr>
          <td>
            <?php if ($ev['image']): ?>
              <img src="<?= htmlspecialchars($ev['image']) ?>" class="img-thumb" alt="">
            <?php else: ?>
              <div class="item-img-placeholder">🎸</div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:500;max-width:200px"><?= htmlspecialchars($ev['title']) ?></div>
          </td>
          <td style="white-space:nowrap">
            <div style="font-weight:500"><?= date('d.m.Y', strtotime($ev['event_date'])) ?></div>
            <?php if ($ev['event_time']): ?>
              <div style="font-size:0.8rem;color:var(--text-muted)"><?= substr($ev['event_time'], 0, 5) ?></div>
            <?php endif; ?>
          </td>
          <td style="font-size:0.85rem"><?= htmlspecialchars($ev['artist'] ?? '—') ?></td>
          <td style="color:var(--gold)"><?= $ev['price'] > 0 ? number_format($ev['price'], 0, '.', ' ') . ' ₽' : 'Бесплатно' ?></td>
          <td style="font-size:0.85rem">
            <?= $ev['seats_booked'] ?>/<?= $ev['seats_total'] ?: '∞' ?>
          </td>
          <td>
            <label class="toggle">
              <input type="checkbox" <?= $ev['active'] ? 'checked' : '' ?>>
              <span class="toggle-slider"></span>
            </label>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="/admin/events.php?edit=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">✏</a>
              <form method="POST" style="display:inline" onsubmit="return confirm('Удалить мероприятие?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">🗑</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Add/edit form -->
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:sticky;top:24px">
    <h4 style="margin-bottom:20px"><?= $editEv ? 'Редактировать событие' : 'Добавить событие' ?></h4>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save">
      <?php if ($editEv): ?>
        <input type="hidden" name="id" value="<?= $editEv['id'] ?>">
      <?php endif; ?>

      <div class="form-group">
        <label>Название *</label>
        <input type="text" name="title" value="<?= htmlspecialchars($editEv['title'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Исполнитель / группа</label>
        <input type="text" name="artist" value="<?= htmlspecialchars($editEv['artist'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Описание</label>
        <textarea name="description"><?= htmlspecialchars($editEv['description'] ?? '') ?></textarea>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label>Дата *</label>
          <input type="date" name="event_date" value="<?= htmlspecialchars($editEv['event_date'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Время</label>
          <input type="time" name="event_time" value="<?= $editEv ? substr($editEv['event_time'] ?? '', 0, 5) : '' ?>">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label>Цена (₽, 0 = бесплатно)</label>
          <input type="number" name="price" step="0.01" min="0" value="<?= $editEv['price'] ?? 0 ?>">
        </div>
        <div class="form-group">
          <label>Мест (0 = без лимита)</label>
          <input type="number" name="seats_total" min="0" value="<?= $editEv['seats_total'] ?? 0 ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Фото</label>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <div class="img-preview-box" id="ev-img-preview" onclick="document.getElementById('ev-img-input').click()">
            <?php if (!empty($editEv['image'])): ?>
              <img src="<?= htmlspecialchars($editEv['image']) ?>" alt="">
            <?php else: ?>
              <span class="placeholder">📷</span>
            <?php endif; ?>
          </div>
          <input type="file" id="ev-img-input" name="image" accept="image/*" style="display:none"
            data-img-preview="ev-img-preview">
          <div style="font-size:0.78rem;color:var(--text-muted);line-height:1.5">JPG, PNG до 5 МБ</div>
        </div>
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:10px">
        <label class="toggle" style="margin-bottom:0">
          <input type="checkbox" name="active" <?= ($editEv['active'] ?? 1) ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
        <span style="font-size:0.9rem">Активно</span>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editEv ? 'Сохранить' : 'Добавить' ?></button>
        <?php if ($editEv): ?>
          <a href="/admin/events.php" class="btn btn-outline">Отмена</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<?php admin_foot(); ?>

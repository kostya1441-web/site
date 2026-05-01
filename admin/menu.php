<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/../includes/functions.php';
admin_require();

$alert = '';
$alertType = 'success';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $cat_id      = (int)($_POST['category_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $active      = isset($_POST['active']) ? 1 : 0;

        if (!$name || !$cat_id || $price <= 0) {
            $alert = 'Заполните обязательные поля'; $alertType = 'error';
        } else {
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = upload_image($_FILES['image'], 'menu');
            }

            $data = compact('cat_id', 'name', 'description', 'price', 'active');
            $data['category_id'] = $cat_id; unset($data['cat_id']);
            if ($image) $data['image'] = $image;

            if ($id) {
                DB::update('menu_items', $data, 'id=?', [$id]);
                $alert = 'Блюдо обновлено';
            } else {
                DB::insert('menu_items', $data);
                $alert = 'Блюдо добавлено';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        DB::query('DELETE FROM menu_items WHERE id=?', [$id]);
        $alert = 'Блюдо удалено';
    }
}

$categories = DB::fetchAll('SELECT * FROM menu_categories WHERE active=1 ORDER BY sort_order');
$items = DB::fetchAll('
    SELECT mi.*, mc.name as cat_name, mc.type
    FROM menu_items mi
    JOIN menu_categories mc ON mi.category_id=mc.id
    ORDER BY mc.sort_order, mi.sort_order, mi.id
');
$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = DB::fetch('SELECT * FROM menu_items WHERE id=?', [(int)$_GET['edit']]);
}

admin_head('Меню');
admin_topbar('Управление меню', 'Блюда и напитки');
?>

<?php if ($alert): ?>
<div class="alert alert-<?= $alertType ?>"><?= htmlspecialchars($alert) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">
  <!-- Items list -->
  <div class="table-card">
    <div class="table-card-header">
      <h3>Позиции меню <span style="color:var(--text-muted);font-size:0.85rem">(<?= count($items) ?>)</span></h3>
      <div class="table-search">
        <input type="text" placeholder="Поиск..." data-search-table="menu-table">
      </div>
    </div>
    <table class="admin-table" id="menu-table">
      <thead>
        <tr>
          <th></th>
          <th>Название</th>
          <th>Категория</th>
          <th>Цена</th>
          <th>Активно</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Позиций нет</td></tr>
        <?php else: ?>
        <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <?php if ($item['image']): ?>
              <img src="<?= htmlspecialchars($item['image']) ?>" class="img-thumb" alt="">
            <?php else: ?>
              <div class="item-img-placeholder">🍽</div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($item['name']) ?></div>
            <?php if ($item['description']): ?>
            <div style="font-size:0.78rem;color:var(--text-muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($item['description']) ?></div>
            <?php endif; ?>
          </td>
          <td style="font-size:0.85rem"><?= htmlspecialchars($item['cat_name']) ?></td>
          <td style="color:var(--gold);font-weight:600"><?= number_format($item['price'], 0, '.', ' ') ?> ₽</td>
          <td>
            <label class="toggle">
              <input type="checkbox" <?= $item['active'] ? 'checked' : '' ?>
                data-toggle-url="/api/admin/menu.php?action=toggle&id=<?= $item['id'] ?>">
              <span class="toggle-slider"></span>
            </label>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="/admin/menu.php?edit=<?= $item['id'] ?>" class="btn btn-outline btn-sm">✏</a>
              <form method="POST" style="display:inline" onsubmit="return confirm('Удалить «<?= htmlspecialchars(addslashes($item['name'])) ?>»?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
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
  <div class="form-section" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:sticky;top:24px">
    <h4 style="margin-bottom:20px"><?= $editItem ? 'Редактировать блюдо' : 'Добавить блюдо' ?></h4>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save">
      <?php if ($editItem): ?>
        <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
      <?php endif; ?>

      <div class="form-group">
        <label>Название *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Категория *</label>
        <select name="category_id" required>
          <option value="">Выберите...</option>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($editItem['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Описание</label>
        <textarea name="description"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Цена (₽) *</label>
        <input type="number" name="price" step="0.01" min="0.01"
          value="<?= $editItem['price'] ?? '' ?>" required>
      </div>

      <div class="form-group">
        <label>Фото</label>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <div class="img-preview-box" id="menu-img-preview" onclick="document.getElementById('img-input').click()">
            <?php if (!empty($editItem['image'])): ?>
              <img src="<?= htmlspecialchars($editItem['image']) ?>" alt="">
            <?php else: ?>
              <span class="placeholder">📷</span>
            <?php endif; ?>
          </div>
          <input type="file" id="img-input" name="image" accept="image/*" style="display:none"
            data-img-preview="menu-img-preview">
          <div style="font-size:0.78rem;color:var(--text-muted);line-height:1.5">
            Нажмите на область для выбора.<br>JPG, PNG, WEBP до 5 МБ
          </div>
        </div>
      </div>

      <div class="form-group" style="display:flex;align-items:center;gap:10px">
        <label class="toggle" style="margin-bottom:0">
          <input type="checkbox" name="active" <?= ($editItem['active'] ?? 1) ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
        <span style="font-size:0.9rem">Активно (показывать на сайте)</span>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editItem ? 'Сохранить' : 'Добавить' ?></button>
        <?php if ($editItem): ?>
          <a href="/admin/menu.php" class="btn btn-outline">Отмена</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<?php admin_foot(); ?>

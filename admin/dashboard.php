<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

$ordersFile = __DIR__ . '/../storage_orders.json';
$orders = file_exists($ordersFile) ? json_decode((string)file_get_contents($ordersFile), true) : [];
include __DIR__ . '/../includes/header.php';
?>
<section class="container section">
  <h1>Админ-панель</h1>
  <p><a class="btn btn-ghost" href="/admin/logout.php">Выйти</a></p>
  <h2>Управление меню</h2>
  <p>Добавление/редактирование/удаление блюд и загрузка фото реализуются через таблицы MySQL (см. schema.sql).</p>

  <h2>Заказы (демо)</h2>
  <?php if (!$orders): ?>
    <p>Пока заказов нет.</p>
  <?php else: ?>
    <div class="cards">
      <?php foreach (array_reverse($orders) as $order): ?>
        <article class="card">
          <p><strong>Клиент:</strong> <?= htmlspecialchars($order['name']) ?></p>
          <p><strong>Тип:</strong> <?= $order['delivery_type'] === 'pickup' ? 'Самовывоз' : 'Доставка' ?></p>
          <p><strong>Сумма:</strong> <?= (int)$order['total'] ?> ₽</p>
          <p><strong>Статус:</strong> <?= htmlspecialchars($order['status']) ?></p>
          <form action="/api/update_order_status.php" method="post" class="inline-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($order['id']) ?>">
            <select name="status">
              <option>Готовится</option>
              <option>Готов</option>
              <option>В пути</option>
              <option>Доставлен</option>
            </select>
            <button class="btn" type="submit">Обновить</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>

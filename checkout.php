<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';
start_session_if_needed();
$allItems = all_menu_items($menu);
$cart = $_SESSION['cart'] ?? [];
$total = cart_total($cart, $allItems);
$success = isset($_GET['ok']);
include __DIR__ . '/includes/header.php';
?>
<section class="container section reveal">
  <h1>Оформление заказа</h1>
  <?php if ($success): ?>
    <p class="notice">Заказ оформлен! Статус можно уточнить по телефону +7 (3843) 77-21-21.</p>
  <?php elseif (!$cart): ?>
    <p>Сначала добавьте блюда в корзину.</p>
  <?php else: ?>
    <p class="price">Сумма к оплате: <?= $total ?> ₽</p>
    <form class="form-card" method="post" action="/api/place_order.php">
      <label>Способ получения
        <select name="delivery_type" id="delivery_type">
          <option value="pickup">Самовывоз</option>
          <option value="delivery">Доставка</option>
        </select>
      </label>
      <label>Имя <input required type="text" name="name"></label>
      <label>Телефон <input required type="tel" name="phone"></label>
      <label id="address_wrap">Адрес (если доставка) <input type="text" name="address"></label>
      <label>Онлайн-оплата (ЮKassa)
        <input type="text" value="Демо: оплата через API ЮKassa подключается на сервере" readonly>
      </label>
      <button class="btn" type="submit">Подтвердить заказ</button>
    </form>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

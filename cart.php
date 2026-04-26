<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';
start_session_if_needed();
$allItems = all_menu_items($menu);
$cart = $_SESSION['cart'] ?? [];
$total = cart_total($cart, $allItems);
include __DIR__ . '/includes/header.php';
?>
<section class="container section reveal">
  <h1>Корзина</h1>
  <?php if (!$cart): ?>
    <p>Пока пусто. Добавьте что-нибудь из <a href="/menu.php">меню</a>.</p>
  <?php else: ?>
    <div class="cart-list">
      <?php foreach ($cart as $itemId => $qty): if (!isset($allItems[$itemId])) continue; $item = $allItems[$itemId]; ?>
        <article class="card cart-item">
          <div>
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><?= $item['price'] ?> ₽ × <?= $qty ?></p>
          </div>
          <form class="inline-form" action="/api/update_cart.php" method="post">
            <input type="hidden" name="item_id" value="<?= $itemId ?>">
            <input type="number" min="0" name="qty" value="<?= $qty ?>">
            <button class="btn" type="submit">Обновить</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="price">Итого: <?= $total ?> ₽</p>
    <a class="btn" href="/checkout.php">Оформить заказ</a>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

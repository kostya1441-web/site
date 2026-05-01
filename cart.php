<?php
$page_title = 'Корзина — Квартирник';
require_once 'includes/header.php';
?>

<section class="cart-page">
  <div class="container">
    <h1 style="margin-bottom:32px">🛒 Корзина</h1>

    <div class="cart-layout">
      <div>
        <div id="cart-items-container">
          <!-- Rendered by JS -->
          <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <span class="spinner"></span>
          </div>
        </div>
      </div>

      <div id="cart-summary" class="cart-summary" style="display:none">
        <h3>Ваш заказ</h3>
        <div class="summary-row">
          <span>Товары</span>
          <span id="cart-subtotal">0 ₽</span>
        </div>
        <div class="summary-row">
          <span>Доставка</span>
          <span style="color:var(--text-muted)">при оформлении</span>
        </div>
        <div class="summary-row total">
          <span>Итого</span>
          <span class="price" id="cart-total">0 ₽</span>
        </div>
        <a href="/checkout.php" class="btn btn-primary btn-lg btn-block" style="margin-top:20px">
          Оформить заказ →
        </a>
        <a href="/menu.php" class="btn btn-outline btn-block" style="margin-top:10px">
          Продолжить покупки
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

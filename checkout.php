<?php
$page_title = 'Оформление заказа — Квартирник';
require_once 'includes/header.php';
?>

<section class="checkout-page">
  <div class="container">
    <h1 style="margin-bottom:32px">Оформление заказа</h1>

    <div class="checkout-layout">
      <!-- Form -->
      <form id="checkout-form" class="checkout-form-section" novalidate>

        <div class="form-card">
          <h3>Способ получения</h3>
          <div class="radio-group">
            <div class="radio-option">
              <input type="radio" name="type" id="type-pickup" value="pickup" checked>
              <label class="radio-label" for="type-pickup">
                <span class="icon">🏠</span>
                <span>Самовывоз</span>
                <small style="color:var(--text-muted);font-size:0.75rem">Бесплатно</small>
              </label>
            </div>
            <div class="radio-option">
              <input type="radio" name="type" id="type-delivery" value="delivery">
              <label class="radio-label" for="type-delivery">
                <span class="icon">🚴</span>
                <span>Доставка</span>
                <small style="color:var(--text-muted);font-size:0.75rem">По городу</small>
              </label>
            </div>
          </div>
        </div>

        <div class="form-card">
          <h3>Контактные данные</h3>
          <div class="form-row">
            <div class="form-group">
              <label for="name">Имя *</label>
              <input type="text" id="name" name="name" placeholder="Ваше имя" required>
            </div>
            <div class="form-group">
              <label for="phone">Телефон *</label>
              <input type="tel" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required>
            </div>
          </div>

          <div class="form-group" id="address-group" style="display:none">
            <label for="address">Адрес доставки *</label>
            <input type="text" id="address" name="address" placeholder="Улица, дом, квартира">
          </div>

          <div class="form-group">
            <label for="comment">Комментарий к заказу</label>
            <textarea id="comment" name="comment" placeholder="Аллергии, пожелания, время..."></textarea>
          </div>
        </div>

        <div class="form-card">
          <h3>Оплата</h3>
          <div style="display:flex;align-items:center;gap:12px;padding:16px;background:rgba(200,145,90,0.05);border:1px solid var(--border);border-radius:var(--radius-sm)">
            <span style="font-size:1.5rem">💳</span>
            <div>
              <div style="font-size:0.9rem;color:var(--text-primary);margin-bottom:2px">Онлайн-оплата через ЮKassa</div>
              <div style="font-size:0.8rem;color:var(--text-muted)">Карты Visa, МИР, Mastercard · Безопасная оплата</div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">
          💳 Перейти к оплате
        </button>

      </form>

      <!-- Summary -->
      <div class="cart-summary" style="top:calc(var(--header-h) + 24px)">
        <h3>Состав заказа</h3>
        <ul id="checkout-items" style="list-style:none;margin-bottom:12px"></ul>
        <div class="summary-row total">
          <span>К оплате</span>
          <span class="price" id="checkout-total">0 ₽</span>
        </div>
        <div style="margin-top:16px;padding:12px;background:rgba(58,107,74,0.1);border:1px solid var(--green);border-radius:var(--radius-sm);font-size:0.82rem;color:#7ecf98">
          🔒 Защищённая оплата. Данные карты не хранятся на нашем сервере.
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

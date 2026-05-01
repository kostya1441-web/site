<?php
$page_title = 'Заказ оформлен — Квартирник';
require_once 'includes/header.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $order_id ? DB::fetch('SELECT * FROM orders WHERE id=?', [$order_id]) : null;
?>

<section class="confirm-page">
  <div class="confirm-card">
    <?php if ($order): ?>
    <div class="confirm-icon">🎉</div>
    <h2>Заказ оформлен!</h2>
    <p>Спасибо, <?= h($order['name']) ?>! Ваш заказ принят в работу.</p>
    <div class="confirm-order-num">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></div>

    <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin:20px 0;text-align:left">
      <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:0.9rem">
        <span style="color:var(--text-muted)">Способ получения:</span>
        <span><?= $order['type'] === 'delivery' ? '🚴 Доставка' : '🏠 Самовывоз' ?></span>
      </div>
      <?php if ($order['type'] === 'delivery' && $order['address']): ?>
      <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:0.9rem">
        <span style="color:var(--text-muted)">Адрес:</span>
        <span><?= h($order['address']) ?></span>
      </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;font-size:0.9rem">
        <span style="color:var(--text-muted)">Сумма:</span>
        <span style="color:var(--gold);font-weight:700"><?= price($order['total']) ?></span>
      </div>
    </div>

    <?php if ($order['payment_status'] === 'paid'): ?>
      <div style="background:rgba(58,107,74,0.15);border:1px solid var(--green);border-radius:var(--radius-sm);padding:12px;color:#7ecf98;font-size:0.9rem;margin-bottom:20px">
        ✓ Оплата прошла успешно
      </div>
    <?php else: ?>
      <div style="background:rgba(200,145,90,0.08);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;color:var(--text-secondary);font-size:0.9rem;margin-bottom:20px">
        📞 Мы свяжемся с вами по номеру <?= h($order['phone']) ?> для уточнения деталей
      </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="confirm-icon">✓</div>
    <h2>Спасибо!</h2>
    <p>Ваш заказ принят. Мы свяжемся с вами в ближайшее время.</p>
    <?php endif; ?>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a href="/" class="btn btn-outline">На главную</a>
      <a href="/menu.php" class="btn btn-primary">Продолжить</a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

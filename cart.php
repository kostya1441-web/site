<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart.php';

$pageTitle = 'Корзина';
$items = cartItems();
$total = cartTotal();

require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Корзина</h1>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                Ваша корзина пуста.<br><br>
                <a class="btn" href="/catalog.php">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px;">
                            <img src="<?= e(imageUrl($item['image'])) ?>" alt="">
                            <a href="/product.php?slug=<?= e($item['slug']) ?>"><?= e($item['name']) ?></a>
                        </td>
                        <td><?= formatPrice($item['price']) ?> / <?= e($item['unit']) ?></td>
                        <td>
                            <input type="number" class="qty-input js-cart-update" min="0" step="0.1"
                                   value="<?= e((string)$item['qty']) ?>" data-product-id="<?= (int)$item['product_id'] ?>">
                        </td>
                        <td><?= formatPrice($item['subtotal']) ?></td>
                        <td><a href="#" class="js-cart-remove" data-product-id="<?= (int)$item['product_id'] ?>" title="Удалить">✕</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-summary-row cart-summary-total">
                    <span>Итого:</span>
                    <span><?= formatPrice($total) ?></span>
                </div>
                <a href="/checkout.php" class="btn btn-accent" style="width:100%; text-align:center; margin-top:14px;">Оформить заказ</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

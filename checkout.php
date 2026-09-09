<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart.php';

$items = cartItems();
$itemsTotal = cartTotal();
$minOrder = (float)setting('min_order_amount', '0');
$deliveryCostSetting = (float)setting('delivery_cost', '0');
$freeDeliveryFrom = (float)setting('free_delivery_from', '0');

$errors = [];

if (empty($items)) {
    $pageTitle = 'Оформление заказа';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state">Ваша корзина пуста. <a href="/catalog.php">Перейти в каталог</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Истёк срок действия формы, попробуйте отправить ещё раз.';
    }

    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $deliveryType = ($_POST['delivery_type'] ?? 'delivery') === 'pickup' ? 'pickup' : 'delivery';
    $address = trim($_POST['address'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $paymentMethod = ($_POST['payment_method'] ?? 'sberbank') === 'cash' ? 'cash' : 'sberbank';

    if ($name === '') $errors[] = 'Укажите ваше имя.';
    if (!preg_match('/^[0-9+()\-\s]{7,20}$/', $phone)) $errors[] = 'Укажите корректный номер телефона.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email.';
    if ($deliveryType === 'delivery' && $address === '') $errors[] = 'Укажите адрес доставки.';
    if ($itemsTotal < $minOrder) $errors[] = 'Минимальная сумма заказа: ' . formatPrice($minOrder);

    if (empty($errors)) {
        $deliveryCost = 0.0;
        if ($deliveryType === 'delivery') {
            $deliveryCost = ($freeDeliveryFrom > 0 && $itemsTotal >= $freeDeliveryFrom) ? 0.0 : $deliveryCostSetting;
        }
        $totalAmount = round($itemsTotal + $deliveryCost, 2);
        $orderNumber = generateOrderNumber();

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO orders (order_number, customer_name, phone, email, delivery_type, address, comment,
                    delivery_cost, items_amount, total_amount, payment_method, payment_status, status)
                 VALUES (:order_number, :customer_name, :phone, :email, :delivery_type, :address, :comment,
                    :delivery_cost, :items_amount, :total_amount, :payment_method, "pending", "new")'
            );
            $stmt->execute([
                'order_number'   => $orderNumber,
                'customer_name'  => $name,
                'phone'          => $phone,
                'email'          => $email !== '' ? $email : null,
                'delivery_type'  => $deliveryType,
                'address'        => $deliveryType === 'delivery' ? $address : null,
                'comment'        => $comment !== '' ? $comment : null,
                'delivery_cost'  => $deliveryCost,
                'items_amount'   => $itemsTotal,
                'total_amount'   => $totalAmount,
                'payment_method' => $paymentMethod,
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, price, unit, quantity, subtotal)
                 VALUES (:order_id, :product_id, :product_name, :price, :unit, :quantity, :subtotal)'
            );
            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id'     => $orderId,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'unit'         => $item['unit'],
                    'quantity'     => $item['qty'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Не удалось создать заказ. Попробуйте ещё раз.';
        }

        if (empty($errors)) {
            if ($paymentMethod === 'cash') {
                cartClear();
                redirect('/order_success.php?order=' . $orderNumber);
            }

            // Оплата через Сбербанк — регистрируем заказ в платёжном шлюзе
            require_once __DIR__ . '/payment/SberbankAcquiring.php';
            $sber = new SberbankAcquiring();
            $result = $sber->register(
                $orderNumber,
                $totalAmount,
                'Заказ №' . $orderNumber . ' в интернет-магазине ' . setting('site_name'),
                SBER_RETURN_URL . '?order=' . urlencode($orderNumber),
                SBER_FAIL_URL . '?order=' . urlencode($orderNumber)
            );

            if ($result['success']) {
                $upd = $pdo->prepare('UPDATE orders SET sber_order_id = :sber_id WHERE id = :id');
                $upd->execute(['sber_id' => $result['orderId'], 'id' => $orderId]);
                cartClear();
                redirect($result['formUrl']);
            } else {
                $errors[] = 'Не удалось перейти к оплате: ' . $result['message'] . '. Заказ №' . $orderNumber . ' сохранён, с вами свяжется наш менеджер, либо позвоните нам по телефону ' . setting('phone') . '.';
            }
        }
    }
}

$pageTitle = 'Оформление заказа';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Оформление заказа</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <div class="contacts-grid">
            <form method="post" action="/checkout.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                <div class="form-group">
                    <label>Ваше имя *</label>
                    <input type="text" name="customer_name" class="form-control" required value="<?= e($_POST['customer_name'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="+7 900 000-00-00" value="<?= e($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group radio-group">
                    <label>Способ получения</label>
                    <?php $dt = $_POST['delivery_type'] ?? 'delivery'; ?>
                    <label><input type="radio" name="delivery_type" value="delivery" onclick="document.getElementById('addressGroup').style.display='block'" <?= $dt === 'delivery' ? 'checked' : '' ?>> Доставка по Новокузнецку (<?= formatPrice($deliveryCostSetting) ?>, бесплатно от <?= formatPrice($freeDeliveryFrom) ?>)</label>
                    <label><input type="radio" name="delivery_type" value="pickup" onclick="document.getElementById('addressGroup').style.display='none'" <?= $dt === 'pickup' ? 'checked' : '' ?>> Самовывоз (<?= e(setting('address')) ?>)</label>
                </div>

                <div class="form-group" id="addressGroup" style="<?= $dt === 'pickup' ? 'display:none;' : '' ?>">
                    <label>Адрес доставки *</label>
                    <input type="text" name="address" class="form-control" value="<?= e($_POST['address'] ?? '') ?>" placeholder="Улица, дом, квартира">
                </div>

                <div class="form-group">
                    <label>Комментарий к заказу</label>
                    <textarea name="comment" class="form-control"><?= e($_POST['comment'] ?? '') ?></textarea>
                </div>

                <div class="form-group radio-group">
                    <label>Способ оплаты</label>
                    <?php $pm = $_POST['payment_method'] ?? 'sberbank'; ?>
                    <label><input type="radio" name="payment_method" value="sberbank" <?= $pm === 'sberbank' ? 'checked' : '' ?>> Оплата картой онлайн (Сбербанк)</label>
                    <label><input type="radio" name="payment_method" value="cash" <?= $pm === 'cash' ? 'checked' : '' ?>> Наличными/картой при получении</label>
                </div>

                <button type="submit" class="btn btn-accent" style="width:100%;">Оформить заказ</button>
            </form>

            <div class="cart-summary" style="margin:0;">
                <h3>Ваш заказ</h3>
                <?php foreach ($items as $item): ?>
                    <div class="cart-summary-row">
                        <span><?= e($item['name']) ?> × <?= e((string)$item['qty']) ?> <?= e($item['unit']) ?></span>
                        <span><?= formatPrice($item['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="cart-summary-row">
                    <span>Товары:</span><span><?= formatPrice($itemsTotal) ?></span>
                </div>
                <div class="cart-summary-row cart-summary-total">
                    <span>Итого к оплате:</span><span><?= formatPrice($itemsTotal + ($dt === 'delivery' && $itemsTotal < $freeDeliveryFrom ? $deliveryCostSetting : 0)) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

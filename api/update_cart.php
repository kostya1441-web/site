<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
$itemId = (int)($_POST['item_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 0);
if ($itemId > 0) {
    if ($qty <= 0) {
        unset($_SESSION['cart'][$itemId]);
    } else {
        $_SESSION['cart'][$itemId] = $qty;
    }
}
header('Location: /cart.php');
exit;

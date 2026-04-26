<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
$itemId = (int)($_POST['item_id'] ?? 0);
if ($itemId > 0) {
    $_SESSION['cart'][$itemId] = ($_SESSION['cart'][$itemId] ?? 0) + 1;
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/menu.php'));
exit;

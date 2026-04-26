<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

$file = __DIR__ . '/../storage_orders.json';
$orders = file_exists($file) ? json_decode((string)file_get_contents($file), true) : [];
$id = $_POST['id'] ?? '';
$status = trim($_POST['status'] ?? '');

foreach ($orders as &$order) {
    if (($order['id'] ?? '') === $id) {
        $order['status'] = $status;
        break;
    }
}
unset($order);
file_put_contents($file, json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
header('Location: /admin/dashboard.php');
exit;

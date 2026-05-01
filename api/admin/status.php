<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

admin_require();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$type   = $body['type'] ?? '';
$id     = (int)($body['id'] ?? 0);
$status = $body['status'] ?? '';

$orderStatuses = ['new','preparing','ready','on_way','delivered','cancelled'];
$bookingStatuses = ['new','confirmed','cancelled'];

$labels = [
    'new'       => 'Новый',
    'preparing' => 'Готовится',
    'ready'     => 'Готов',
    'on_way'    => 'В пути',
    'delivered' => 'Доставлен',
    'cancelled' => 'Отменён',
    'confirmed' => 'Подтверждён',
];

if ($type === 'order' && in_array($status, $orderStatuses)) {
    DB::update('orders', ['status' => $status], 'id = ?', [$id]);
    json_response(['success' => true, 'label' => $labels[$status] ?? $status]);
}

if ($type === 'booking' && in_array($status, $bookingStatuses)) {
    DB::update('bookings', ['status' => $status], 'id = ?', [$id]);
    json_response(['success' => true, 'label' => $labels[$status] ?? $status]);
}

json_response(['success' => false, 'message' => 'Invalid parameters'], 400);

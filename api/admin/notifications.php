<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

admin_require();

$lastId = (int)($_GET['last'] ?? 0);

$newOrders = DB::fetchAll(
    'SELECT id, name, phone, type, total, created_at FROM orders WHERE id > ? AND status IN ("new","preparing","ready","on_way") ORDER BY id ASC LIMIT 10',
    [$lastId]
);

json_response(['new_orders' => $newOrders]);

<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

admin_require();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Delete
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    DB::query('DELETE FROM menu_items WHERE id = ?', [$id]);
    json_response(['success' => true]);
}

// Toggle active
if ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = (int)($_GET['id'] ?? 0);
    $active = (int)($body['active'] ?? 0);
    DB::update('menu_items', ['active' => $active], 'id = ?', [$id]);
    json_response(['success' => true]);
}

json_response(['success' => false], 400);

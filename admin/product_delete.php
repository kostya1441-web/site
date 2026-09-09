<?php
require_once __DIR__ . '/includes/admin_auth.php';
requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0 && csrfCheck($_GET['csrf_token'] ?? null)) {
    $stmt = db()->prepare('SELECT image FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $image = $stmt->fetchColumn();

    db()->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
    if ($image) {
        deleteImageFile($image, 'products');
    }
}

redirect('/admin/products.php');

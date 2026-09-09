<?php
require_once __DIR__ . '/includes/admin_auth.php';
requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0 && csrfCheck($_GET['csrf_token'] ?? null)) {
    $stmt = db()->prepare('SELECT image FROM categories WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $image = $stmt->fetchColumn();

    $productImages = db()->prepare('SELECT image FROM products WHERE category_id = :id AND image IS NOT NULL');
    $productImages->execute(['id' => $id]);
    $imagesToRemove = $productImages->fetchAll(PDO::FETCH_COLUMN);

    db()->prepare('DELETE FROM categories WHERE id = :id')->execute(['id' => $id]);

    if ($image) {
        deleteImageFile($image, 'categories');
    }
    foreach ($imagesToRemove as $img) {
        deleteImageFile($img, 'products');
    }
}

redirect('/admin/categories.php');

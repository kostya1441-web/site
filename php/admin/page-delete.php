<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT slug FROM pages WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['slug'] !== '') {
        db()->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
    }
}

redirect('/admin/pages.php');

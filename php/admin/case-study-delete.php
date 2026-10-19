<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM case_studies WHERE id = ?')->execute([$id]);
}

redirect('/admin/case-studies.php');

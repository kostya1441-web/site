<?php
require_once __DIR__ . '/../includes/admin_views.php';

start_session();
if (current_admin()) {
    redirect('/admin/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    if (attempt_login($username, $password)) {
        redirect('/admin/');
    }
    http_response_code(401);
    echo admin_login_page('Неверный логин или пароль.');
    exit;
}

echo admin_login_page();

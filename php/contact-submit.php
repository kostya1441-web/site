<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/contact/');
}

$stmt = db()->prepare(
    'INSERT INTO leads (name, email, phone, company, message) VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([
    trim($_POST['name'] ?? ''),
    trim($_POST['email'] ?? ''),
    trim($_POST['phone'] ?? ''),
    trim($_POST['company'] ?? ''),
    trim($_POST['message'] ?? ''),
]);

redirect('/contact/?sent=1');

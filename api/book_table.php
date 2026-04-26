<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /booking.php');
    exit;
}

$file = __DIR__ . '/../storage_bookings.json';
$bookings = file_exists($file) ? json_decode((string)file_get_contents($file), true) : [];
$bookings[] = [
    'id' => uniqid('booking_', true),
    'name' => trim($_POST['name'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'date' => $_POST['date'] ?? '',
    'time' => $_POST['time'] ?? '',
    'guests' => (int)($_POST['guests'] ?? 1),
    'created_at' => date('c'),
];
file_put_contents($file, json_encode($bookings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
header('Location: /booking.php?ok=1');
exit;

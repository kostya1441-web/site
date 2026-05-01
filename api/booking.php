<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$name     = trim($_POST['name'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$date     = trim($_POST['date'] ?? '');
$time     = trim($_POST['time'] ?? '');
$guests   = (int)($_POST['guests'] ?? 2);
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
$comment  = trim($_POST['comment'] ?? '');

if (!$name || mb_strlen($name) < 2) {
    json_response(['success' => false, 'message' => 'Введите имя']);
}
if (!preg_match('/^\+?[0-9\s\(\)\-]{7,20}$/', $phone)) {
    json_response(['success' => false, 'message' => 'Введите корректный телефон']);
}
if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || strtotime($date) < strtotime('today')) {
    json_response(['success' => false, 'message' => 'Выберите дату (не ранее сегодня)']);
}
if (!$time || !preg_match('/^\d{2}:\d{2}$/', $time)) {
    json_response(['success' => false, 'message' => 'Выберите время']);
}
if ($guests < 1 || $guests > 20) {
    json_response(['success' => false, 'message' => 'Некорректное количество гостей']);
}

// Validate event if provided
if ($event_id) {
    $event = DB::fetch('SELECT * FROM events WHERE id=? AND active=1', [$event_id]);
    if (!$event) { json_response(['success' => false, 'message' => 'Мероприятие не найдено']); }
}

$id = DB::insert('bookings', [
    'name'         => $name,
    'phone'        => $phone,
    'booking_date' => $date,
    'booking_time' => $time . ':00',
    'guests'       => $guests,
    'event_id'     => $event_id,
    'comment'      => $comment,
]);

// Update seats if event
if ($event_id) {
    DB::query('UPDATE events SET seats_booked = seats_booked + ? WHERE id = ?', [$guests, $event_id]);
}

$months = ['', 'января','февраля','марта','апреля','мая','июня',
           'июля','августа','сентября','октября','ноября','декабря'];
$ts = strtotime($date);
$date_ru = (int)date('d', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);

json_response([
    'success'    => true,
    'booking_id' => $id,
    'date'       => $date_ru,
    'time'       => substr($time, 0, 5),
]);

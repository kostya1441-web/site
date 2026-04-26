<?php
require_once __DIR__ . '/includes/functions.php';
start_session_if_needed();
$success = isset($_GET['ok']);
include __DIR__ . '/includes/header.php';
?>
<section class="container section reveal">
  <h1>Бронирование столиков</h1>
  <?php if ($success): ?>
    <p class="notice">Спасибо! Бронирование принято, мы свяжемся с вами для подтверждения.</p>
  <?php endif; ?>
  <form class="form-card" action="/api/book_table.php" method="post">
    <label>Имя <input required type="text" name="name"></label>
    <label>Телефон <input required type="tel" name="phone"></label>
    <label>Дата <input required type="date" name="date"></label>
    <label>Время <input required type="time" name="time"></label>
    <label>Количество гостей <input required min="1" max="20" type="number" name="guests"></label>
    <button class="btn" type="submit">Забронировать</button>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

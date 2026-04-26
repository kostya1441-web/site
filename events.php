<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';
$selectedDate = $_GET['date'] ?? '';
$filtered = array_filter($events, static fn($event) => $selectedDate === '' || $event['date'] >= $selectedDate);
include __DIR__ . '/includes/header.php';
?>
<section class="container section reveal">
  <h1>Мероприятия</h1>
  <form class="inline-form" method="get">
    <label for="date">Фильтр по дате:</label>
    <input id="date" type="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>">
    <button class="btn" type="submit">Применить</button>
  </form>

  <div class="cards">
    <?php foreach ($filtered as $event): ?>
      <article class="card">
        <p class="muted"><?= russian_date($event['date']) ?></p>
        <h3><?= htmlspecialchars($event['title']) ?></h3>
        <p><strong>Исполнитель:</strong> <?= htmlspecialchars($event['artist']) ?></p>
        <p><?= htmlspecialchars($event['description']) ?></p>
        <a class="btn" href="/booking.php?event_id=<?= $event['id'] ?>">Бронь столика</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

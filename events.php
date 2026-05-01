<?php
$page_title = 'Мероприятия — Квартирник';
require_once 'includes/header.php';

$from = $_GET['from'] ?? null;
$to   = $_GET['to']   ?? null;
if ($from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = null;
if ($to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = null;

$events = get_events_filtered($from, $to);
?>

<section class="page-hero">
  <div class="container">
    <span style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:10px">Афиша</span>
    <h1>Мероприятия</h1>
    <p>Живая музыка, квартирники и особые вечера — следите за нашим расписанием</p>
  </div>
</section>

<section class="events-page">
  <div class="container">

    <!-- Filter -->
    <form id="events-filter-form" class="events-filter">
      <div class="form-group">
        <label>От даты</label>
        <input type="date" name="from" value="<?= h($from ?? '') ?>">
      </div>
      <div class="form-group">
        <label>До даты</label>
        <input type="date" name="to" value="<?= h($to ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-primary">Найти</button>
      <?php if ($from || $to): ?>
        <button type="button" class="btn btn-outline btn-reset">Сбросить</button>
      <?php endif; ?>
    </form>

    <?php if (empty($events)): ?>
      <div style="text-align:center;padding:80px 24px;color:var(--text-muted)">
        <div style="font-size:4rem;margin-bottom:16px;opacity:0.4">🎸</div>
        <h3 style="color:var(--text-secondary);margin-bottom:8px">Мероприятий не найдено</h3>
        <p>Попробуйте изменить фильтр или <a href="/events.php">посмотрите все события</a></p>
      </div>
    <?php else: ?>
      <div class="cards-grid">
        <?php foreach ($events as $ev): ?>
        <div class="event-card fade-up">
          <div class="event-card-img">
            <?php if ($ev['image']): ?>
              <img src="<?= h($ev['image']) ?>" alt="<?= h($ev['title']) ?>">
            <?php else: ?>
              <div class="no-img">🎸</div>
            <?php endif; ?>
            <div class="event-date-badge">
              <div class="day"><?= date('d', strtotime($ev['event_date'])) ?></div>
              <div class="month"><?= mb_strtoupper(date('M', strtotime($ev['event_date']))) ?></div>
            </div>
            <?php if ($ev['price'] > 0): ?>
              <div class="event-price-badge"><?= price($ev['price']) ?></div>
            <?php else: ?>
              <div class="event-price-badge" style="background:var(--green)">Вход свободный</div>
            <?php endif; ?>
          </div>
          <div class="event-card-body">
            <h3><?= h($ev['title']) ?></h3>
            <?php if ($ev['artist']): ?>
              <div class="event-artist">♪ <?= h($ev['artist']) ?></div>
            <?php endif; ?>
            <p><?= h($ev['description']) ?></p>
            <div class="event-meta">
              <span>📅 <?= format_date_ru($ev['event_date']) ?></span>
              <?php if ($ev['event_time']): ?>
                <span>🕐 <?= substr($ev['event_time'], 0, 5) ?></span>
              <?php endif; ?>
              <?php if ($ev['seats_total'] > 0): ?>
                <span>💺 <?= $ev['seats_total'] - $ev['seats_booked'] ?> мест</span>
              <?php endif; ?>
            </div>
            <a href="/booking.php?event_id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm btn-block">
              Забронировать стол
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

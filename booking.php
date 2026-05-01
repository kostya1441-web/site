<?php
$page_title = 'Бронирование стола — Квартирник';
require_once 'includes/header.php';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
$event = $event_id ? DB::fetch('SELECT * FROM events WHERE id=? AND active=1', [$event_id]) : null;
?>

<section class="page-hero">
  <div class="container">
    <span style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:10px">Резервация</span>
    <h1>Забронировать стол</h1>
    <p>Зарезервируйте место заранее — мы обязательно вас ждём</p>
  </div>
</section>

<section class="booking-page">
  <div class="container">
    <div class="booking-layout">

      <!-- Info -->
      <div class="booking-info fade-up">
        <?php if ($event): ?>
        <div style="background:rgba(200,145,90,0.08);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:32px">
          <div style="font-size:0.8rem;color:var(--gold);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.1em">Бронирование на мероприятие</div>
          <h3 style="margin-bottom:4px"><?= h($event['title']) ?></h3>
          <div style="font-size:0.9rem;color:var(--text-secondary)"><?= format_date_ru($event['event_date']) ?> · <?= substr($event['event_time'], 0, 5) ?></div>
          <?php if ($event['artist']): ?>
          <div style="font-size:0.85rem;color:var(--gold);margin-top:6px">♪ <?= h($event['artist']) ?></div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <h2 style="margin-bottom:16px">Как это работает</h2>
        <p style="margin-bottom:24px">Оставьте заявку, и мы свяжемся с вами в течение часа для подтверждения брони.</p>

        <ul class="booking-info-list">
          <li>
            <div class="bi-icon">📋</div>
            <div class="bi-text">
              <strong>Заполните форму</strong>
              <span>Укажите дату, время и количество гостей</span>
            </div>
          </li>
          <li>
            <div class="bi-icon">📞</div>
            <div class="bi-text">
              <strong>Ждите звонка</strong>
              <span>Мы позвоним для подтверждения в течение 1 часа</span>
            </div>
          </li>
          <li>
            <div class="bi-icon">🎉</div>
            <div class="bi-text">
              <strong>Приходите!</strong>
              <span>Ваш столик будет готов к назначенному времени</span>
            </div>
          </li>
        </ul>

        <div style="margin-top:32px;padding:20px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius)">
          <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:8px">Также можно забронировать по телефону:</div>
          <a href="tel:<?= SITE_PHONE ?>" style="font-size:1.3rem;font-family:var(--font-serif);color:var(--gold)"><?= SITE_PHONE ?></a>
        </div>
      </div>

      <!-- Form -->
      <div class="fade-up">
        <div class="form-card">
          <h3>Данные для брони</h3>
          <form id="booking-form" novalidate>
            <?php if ($event_id): ?>
              <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <?php endif; ?>

            <div class="form-row">
              <div class="form-group">
                <label for="b-name">Ваше имя *</label>
                <input type="text" id="b-name" name="name" placeholder="Имя" required>
              </div>
              <div class="form-group">
                <label for="b-phone">Телефон *</label>
                <input type="tel" id="b-phone" name="phone" placeholder="+7 (___) ___-__-__" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="b-date">Дата *</label>
                <input type="date" id="b-date" name="date"
                  min="<?= date('Y-m-d') ?>"
                  value="<?= $event ? $event['event_date'] : '' ?>"
                  required>
              </div>
              <div class="form-group">
                <label for="b-time">Время *</label>
                <input type="time" id="b-time" name="time"
                  min="17:00" max="23:00"
                  value="<?= $event ? substr($event['event_time'], 0, 5) : '19:00' ?>"
                  required>
              </div>
            </div>

            <div class="form-group">
              <label for="b-guests">Количество гостей *</label>
              <select id="b-guests" name="guests" required>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="<?= $i ?>" <?= $i === 2 ? 'selected' : '' ?>>
                    <?= $i ?> <?= $i === 1 ? 'гость' : ($i < 5 ? 'гостя' : 'гостей') ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="b-comment">Пожелания (необязательно)</label>
              <textarea id="b-comment" name="comment" placeholder="Особые пожелания, повод, пожелания по столику..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
              🎉 Забронировать
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

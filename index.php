<?php
$page_title = 'Квартирник — Кафе-бар в Новокузнецке';
require_once 'includes/header.php';
$events = get_upcoming_events(4);
?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-bg"></div>

  <!-- Decorative SVG -->
  <svg class="hero-decor" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="200" cy="200" r="180" stroke="white" stroke-width="1"/>
    <circle cx="200" cy="200" r="140" stroke="white" stroke-width="0.5"/>
    <circle cx="200" cy="200" r="100" stroke="white" stroke-width="1"/>
    <line x1="200" y1="20" x2="200" y2="380" stroke="white" stroke-width="0.5"/>
    <line x1="20" y1="200" x2="380" y2="200" stroke="white" stroke-width="0.5"/>
    <path d="M200 60 L220 100 L260 110 L230 140 L240 180 L200 160 L160 180 L170 140 L140 110 L180 100 Z" stroke="white" stroke-width="0.5" fill="none"/>
  </svg>

  <div class="container">
    <div class="hero-content">
      <div class="hero-eyebrow">
        <span>♪</span> Живая музыка · Уютная атмосфера
      </div>
      <h1>Место, где<br><span>живёт музыка</span></h1>
      <p class="lead">
        «Квартирник» — камерный кафе-бар в Новокузнецке с атмосферой домашних вечеринок,
        живыми концертами и авторским меню. Здесь каждый вечер особенный.
      </p>
      <div class="hero-actions">
        <a href="/booking.php" class="btn btn-primary btn-lg">🎵 Забронировать стол</a>
        <a href="/menu.php" class="btn btn-outline btn-lg">Посмотреть меню</a>
      </div>
    </div>
  </div>
</section>

<!-- ── Features strip ────────────────────────────────────── -->
<section class="section-sm" style="background:var(--bg-dark);border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light)">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:32px;text-align:center">
      <?php foreach ([
        ['🎸', 'Живая музыка', 'Каждую пятницу и субботу'],
        ['🍷', 'Авторное меню', 'Блюда и коктейли от шефа'],
        ['🏡', 'Камерная атмосфера', 'Как дома, только лучше'],
        ['🎤', 'Квартирники', 'Открытые вечера и концерты'],
      ] as [$icon, $title, $sub]): ?>
      <div class="fade-up">
        <div style="font-size:2rem;margin-bottom:10px"><?= $icon ?></div>
        <h4 style="color:var(--text-primary);margin-bottom:4px"><?= $title ?></h4>
        <p style="font-size:0.85rem"><?= $sub ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── About snippet ─────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center">
      <div class="fade-up">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;height:420px">
          <div style="background:var(--bg-card2);border-radius:var(--radius);grid-row:span 2;display:flex;align-items:center;justify-content:center;font-size:5rem;opacity:0.4">🎻</div>
          <div style="background:var(--bg-card);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:0.4">🕯</div>
          <div style="background:var(--bg-card2);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:0.4">🍸</div>
        </div>
      </div>
      <div class="fade-up">
        <span class="eyebrow" style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:12px">О нас</span>
        <h2 style="margin-bottom:20px">Квартирник —<br>больше чем кафе</h2>
        <p style="margin-bottom:16px;line-height:1.8">Мы создали место, где можно прийти как к другу домой. Тёплый свет, живая музыка, вкусная еда и хорошие напитки — всё, что нужно для настоящего вечера.</p>
        <p style="margin-bottom:32px;line-height:1.8">С 2018 года «Квартирник» собирает людей, которые ценят живое общение, искусство и уют. Регулярные концерты, открытые микрофоны и тематические вечера — каждую неделю.</p>
        <a href="/about.php" class="btn btn-outline">Узнать больше о нас</a>
      </div>
    </div>
  </div>
</section>

<!-- ── Upcoming events ────────────────────────────────────── -->
<?php if ($events): ?>
<section class="section" style="background:var(--bg-dark)">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Афиша</span>
      <h2>Ближайшие мероприятия</h2>
      <p>Живая музыка, квартирники и особые вечера — следите за расписанием</p>
    </div>
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
          </div>
          <a href="/booking.php?event_id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm btn-block">Забронировать стол</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:40px">
      <a href="/events.php" class="btn btn-outline">Все мероприятия</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── CTA Section ────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:64px 48px;text-align:center;background-image:radial-gradient(ellipse at 50% 0%,rgba(200,145,90,0.08) 0%,transparent 60%)">
      <div class="fade-up">
        <div style="font-size:3rem;margin-bottom:16px">🎶</div>
        <h2 style="margin-bottom:16px">Готовы к особенному вечеру?</h2>
        <p style="max-width:480px;margin:0 auto 32px">Забронируйте стол прямо сейчас или закажите блюда из нашего авторского меню</p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
          <a href="/booking.php" class="btn btn-primary btn-lg">Забронировать стол</a>
          <a href="/menu.php" class="btn btn-outline btn-lg">Открыть меню</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

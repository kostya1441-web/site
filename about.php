<?php
$page_title = 'О нас — Квартирник Кафе-бар';
require_once 'includes/header.php';
?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="about-hero">
  <div class="container">
    <div class="about-grid">
      <div class="about-img-grid fade-up">
        <div class="img-block">
          <div class="about-img-placeholder">🎸</div>
        </div>
        <div class="img-block">
          <div class="about-img-placeholder">🕯</div>
        </div>
        <div class="img-block">
          <div class="about-img-placeholder">🍷</div>
        </div>
      </div>
      <div class="about-text fade-up">
        <span class="eyebrow">Наша история</span>
        <h2>Как появился<br>«Квартирник»</h2>
        <p>В 2018 году несколько друзей-музыкантов и ценителей хорошей еды решили создать место, которого им самим не хватало в Новокузнецке. Место, где можно расслабиться, послушать живую музыку и поесть действительно вкусно — как у друга дома.</p>
        <p>Название «Квартирник» — это не просто отсылка к домашним концертам. Это философия: тепло, уют, живое общение и настоящее искусство в камерном пространстве.</p>
        <p>За эти годы наш бар стал домом для десятков музыкантов и сотен постоянных гостей. Мы гордимся каждым вечером, который проходит на нашей сцене.</p>
        <a href="/booking.php" class="btn btn-primary" style="margin-top:16px">Прийти в гости</a>
      </div>
    </div>
  </div>
</section>

<!-- ── Features ─────────────────────────────────────────── -->
<section class="section" style="background:var(--bg-dark)">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Наша концепция</span>
      <h2>Что делает нас особенными</h2>
    </div>
    <div class="features-grid">
      <?php foreach ([
        ['🎸', 'Живая музыка', 'Каждую пятницу и субботу — живые концерты. Джаз, акустика, рок, электроника.'],
        ['🏡', 'Уют квартиры', 'Интерьер создан так, чтобы вы чувствовали себя как дома у хорошего друга.'],
        ['🍽', 'Авторское меню', 'Шеф-повар обновляет меню по сезонам. Только свежие продукты и авторские рецепты.'],
        ['🍸', 'Авторские коктейли', 'Бартендеры создают уникальные коктейли, вдохновлённые музыкой и сезоном.'],
        ['🎤', 'Открытый микрофон', 'Регулярные Open Mic вечера — сцена открыта для всех желающих.'],
        ['👥', 'Камерность', 'Небольшой зал до 60 человек — только тёплая атмосфера без шума.'],
      ] as [$icon, $title, $desc]): ?>
      <div class="feature-item fade-up">
        <div class="icon"><?= $icon ?></div>
        <h4><?= $title ?></h4>
        <p><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Team / Numbers ────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:24px;text-align:center" class="fade-up">
      <?php foreach ([
        ['2018', 'Год основания'],
        ['6+', 'Лет в Новокузнецке'],
        ['200+', 'Концертов проведено'],
        ['60', 'Мест в зале'],
        ['50+', 'Блюд в меню'],
        ['★ 4.9', 'Средний рейтинг'],
      ] as [$num, $label]): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:28px 16px">
        <div style="font-family:var(--font-serif);font-size:2.2rem;color:var(--gold);font-weight:700;margin-bottom:6px"><?= $num ?></div>
        <div style="font-size:0.85rem;color:var(--text-muted)"><?= $label ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Contacts ──────────────────────────────────────────── -->
<section class="contacts-section" style="background:var(--bg-dark)">
  <div class="container">
    <div class="contacts-grid">
      <div class="contact-info fade-up">
        <span class="eyebrow" style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:12px">Контакты</span>
        <h2>Как нас найти</h2>
        <ul class="contact-list" style="margin-top:24px">
          <li>
            <div class="ci-icon">📍</div>
            <div class="ci-body">
              <strong>Адрес</strong>
              <span><?= SITE_ADDRESS ?></span>
            </div>
          </li>
          <li>
            <div class="ci-icon">📞</div>
            <div class="ci-body">
              <strong>Телефон</strong>
              <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
            </div>
          </li>
          <li>
            <div class="ci-icon">✉</div>
            <div class="ci-body">
              <strong>Email</strong>
              <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
            </div>
          </li>
          <li>
            <div class="ci-icon">🕐</div>
            <div class="ci-body">
              <strong>Часы работы</strong>
              <span>Пн–Чт 17:00–00:00 · Пт–Сб 17:00–02:00 · Вс 16:00–23:00</span>
            </div>
          </li>
        </ul>
        <div class="socials">
          <a href="#" class="social-btn" title="ВКонтакте">🎵</a>
          <a href="#" class="social-btn" title="Telegram">✈</a>
          <a href="#" class="social-btn" title="Instagram">📸</a>
        </div>
      </div>
      <div class="fade-up">
        <div class="map-container">
          <!-- Яндекс.Карта -->
          <iframe
            src="https://yandex.ru/map-widget/v1/?text=Новокузнецк%2C+улица+Кирова+15&z=16&l=map"
            allowfullscreen
            loading="lazy"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

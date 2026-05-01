
</div><!-- /.page-content -->

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="/" class="logo">
          <span class="logo-name">Квартирник</span>
          <span class="logo-sub">Кафе-бар · Новокузнецк</span>
        </a>
        <p>Место, где живёт музыка.<br>Уютная атмосфера квартирных вечеров, живые концерты и авторское меню.</p>
        <div class="socials" style="margin-top:20px">
          <a href="#" class="social-btn" title="ВКонтакте">🎵</a>
          <a href="#" class="social-btn" title="Telegram">✈</a>
          <a href="#" class="social-btn" title="Instagram">📸</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Навигация</h4>
        <ul>
          <li><a href="/">Главная</a></li>
          <li><a href="/about.php">О нас</a></li>
          <li><a href="/events.php">Мероприятия</a></li>
          <li><a href="/menu.php">Меню</a></li>
          <li><a href="/booking.php">Бронирование</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Контакты</h4>
        <ul>
          <li><a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a></li>
          <li><a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></li>
        </ul>
        <p style="margin-top:12px;font-size:0.85rem;color:var(--text-muted)"><?= SITE_ADDRESS ?></p>
      </div>
      <div class="footer-col">
        <h4>Часы работы</h4>
        <ul>
          <li><a href="#">Пн–Чт: 17:00 – 00:00</a></li>
          <li><a href="#">Пт–Сб: 17:00 – 02:00</a></li>
          <li><a href="#">Вс: 16:00 – 23:00</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> Кафе-бар «Квартирник». Все права защищены.</span>
      <span>г. Новокузнецк</span>
    </div>
  </div>
</footer>

<div class="toast-container"></div>
<script src="/assets/js/main.js"></script>
</body>
</html>

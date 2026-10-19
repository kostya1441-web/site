<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$pageCount = (int)db()->query('SELECT COUNT(*) c FROM pages')->fetch()['c'];
$csCount = (int)db()->query('SELECT COUNT(*) c FROM case_studies')->fetch()['c'];
$artCount = (int)db()->query('SELECT COUNT(*) c FROM articles')->fetch()['c'];
$leadTotal = (int)db()->query('SELECT COUNT(*) c FROM leads')->fetch()['c'];
$leadUnread = (int)db()->query('SELECT COUNT(*) c FROM leads WHERE is_read = 0')->fetch()['c'];

$body = "
<div class=\"a-grid\">
  <div class=\"a-card\">
    <h2>Сайт</h2>
    <p>$pageCount страниц · $csCount примеров проектов · $artCount статей</p>
    <a class=\"a-btn accent\" href=\"/\" target=\"_blank\">Открыть сайт</a>
  </div>
  <div class=\"a-card\">
    <h2>Новые заявки</h2>
    <p>$leadUnread непрочитанных из $leadTotal всего</p>
    <a class=\"a-btn\" href=\"/admin/leads.php\">Посмотреть заявки</a>
  </div>
</div>
<div class=\"a-card\">
  <h2>Быстрые ссылки</h2>
  <div class=\"a-row\">
    <a class=\"a-btn ghost\" href=\"/admin/pages.php\">Редактировать страницы</a>
    <a class=\"a-btn ghost\" href=\"/admin/case-studies.php\">Примеры проектов</a>
    <a class=\"a-btn ghost\" href=\"/admin/articles.php\">Пресса и статьи</a>
    <a class=\"a-btn ghost\" href=\"/admin/settings.php\">Контакты и настройки</a>
  </div>
</div>";

echo admin_shell('Дашборд', '/admin/', $body);

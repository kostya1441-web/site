<?php
// Admin layout helpers
function admin_head(string $title = 'Панель управления'): void {
    $info = admin_info();
    echo '<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($title) . ' — Квартирник Админ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="name">Квартирник</div>
    <div class="sub">Администратор</div>
  </div>
  <nav class="sidebar-nav">
    <a href="/admin/" class="' . (basename($_SERVER['PHP_SELF']) === 'index.php' && dirname($_SERVER['PHP_SELF']) === '/admin' ? 'active' : '') . '">
      <span class="nav-icon">📊</span> Дашборд
    </a>
    <a href="/admin/orders.php" class="' . (basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '') . '">
      <span class="nav-icon">📦</span> Заказы
    </a>
    <a href="/admin/bookings.php" class="' . (basename($_SERVER['PHP_SELF']) === 'bookings.php' ? 'active' : '') . '">
      <span class="nav-icon">📅</span> Брони
    </a>
    <a href="/admin/menu.php" class="' . (basename($_SERVER['PHP_SELF']) === 'menu.php' ? 'active' : '') . '">
      <span class="nav-icon">🍽</span> Меню
    </a>
    <a href="/admin/events.php" class="' . (basename($_SERVER['PHP_SELF']) === 'events.php' ? 'active' : '') . '">
      <span class="nav-icon">🎸</span> Мероприятия
    </a>
    <a href="/" target="_blank">
      <span class="nav-icon">🌐</span> Сайт
    </a>
    <a href="/admin/logout.php" class="logout">
      <span class="nav-icon">🚪</span> Выйти
    </a>
  </nav>
  <div class="sidebar-bottom">
    <div class="sidebar-user">
      <div class="avatar">' . mb_strtoupper(mb_substr($info['name'] ?? 'A', 0, 1)) . '</div>
      <div>
        <div class="name">' . htmlspecialchars($info['name'] ?? '') . '</div>
        <div class="role">Администратор</div>
      </div>
    </div>
  </div>
</aside>
<div class="admin-content">';
}

function admin_topbar(string $title, string $breadcrumb = ''): void {
    echo '<div class="admin-topbar">
  <div>
    <div class="page-title">' . htmlspecialchars($title) . '</div>
    ' . ($breadcrumb ? '<div class="breadcrumb">' . htmlspecialchars($breadcrumb) . '</div>' : '') . '
  </div>
  <div style="display:flex;gap:12px;align-items:center">
    <button class="notif-btn" id="notif-btn" title="Уведомления">
      🔔 <span class="notif-dot hidden"></span>
    </button>
    <button class="btn btn-outline btn-sm" id="sidebar-toggle" style="display:none">☰</button>
  </div>
</div>
<main class="admin-main">';
}

function admin_foot(): void {
    echo '</main></div></div>
<script src="/assets/js/admin.js"></script>
</body></html>';
}

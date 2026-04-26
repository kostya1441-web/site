<?php
require_once __DIR__ . '/functions.php';
start_session_if_needed();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Кафе-бар «Квартирник» в Новокузнецке: живая музыка, уют и камерные мероприятия.">
  <title>Квартирник — кафе-бар Новокузнецк</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Comfortaa:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo" href="/index.php">Квартирник</a>
    <nav>
      <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="/index.php">Главная</a>
      <a class="<?= $currentPage === 'about.php' ? 'active' : '' ?>" href="/about.php">О нас</a>
      <a class="<?= $currentPage === 'events.php' ? 'active' : '' ?>" href="/events.php">Мероприятия</a>
      <a class="<?= $currentPage === 'booking.php' ? 'active' : '' ?>" href="/booking.php">Бронирование</a>
      <a class="<?= $currentPage === 'menu.php' ? 'active' : '' ?>" href="/menu.php">Меню</a>
      <a class="cart-link <?= $currentPage === 'cart.php' ? 'active' : '' ?>" href="/cart.php">Корзина (<?= $cartCount ?>)</a>
    </nav>
  </div>
</header>
<main>

<?php
require_once __DIR__ . '/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$page_title = $page_title ?? SITE_NAME;
$page_desc  = $page_desc  ?? 'Кафе-бар «Квартирник» — живая музыка, уютная атмосфера, авторское меню. Новокузнецк.';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title) ?></title>
<meta name="description" content="<?= h($page_desc) ?>">
<meta name="theme-color" content="#151210">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

<header class="header">
  <div class="container header-inner">
    <a href="/" class="logo">
      <span class="logo-name">Квартирник</span>
      <span class="logo-sub">Кафе-бар · Новокузнецк</span>
    </a>

    <nav class="nav">
      <a href="/"          class="<?= active_nav('index') ?>">Главная</a>
      <a href="/about.php" class="<?= active_nav('about') ?>">О нас</a>
      <a href="/events.php" class="<?= active_nav('events') ?>">Мероприятия</a>
      <a href="/menu.php"  class="<?= active_nav('menu') ?>">Меню</a>
      <a href="/booking.php" class="<?= active_nav('booking') ?>">Бронирование</a>
    </nav>

    <div class="header-actions">
      <a href="/cart.php" class="cart-btn">
        🛒 Корзина
        <span class="cart-count" style="display:none">0</span>
      </a>
      <button class="burger" aria-label="Меню">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="page-content">

<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';
?>
<section class="hero reveal">
  <div class="overlay"></div>
  <div class="container hero-content">
    <h1>Квартирник — тёплые вечера, живая музыка и вкусная кухня</h1>
    <p><?= htmlspecialchars($conceptText) ?></p>
    <div class="actions">
      <a class="btn" href="/booking.php">Забронировать стол</a>
      <a class="btn btn-ghost" href="/menu.php">Посмотреть меню</a>
    </div>
  </div>
</section>

<section class="container section reveal">
  <h2>Ближайшие мероприятия</h2>
  <div class="cards">
    <?php foreach ($events as $event): ?>
      <article class="card">
        <p class="muted"><?= russian_date($event['date']) ?></p>
        <h3><?= htmlspecialchars($event['title']) ?></h3>
        <p><?= htmlspecialchars($event['description']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="container section reveal">
  <h2>Популярные блюда и напитки</h2>
  <div class="cards menu-cards">
    <?php foreach (array_slice(all_menu_items($menu), 0, 4) as $item): ?>
      <article class="card menu-item">
        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p><?= htmlspecialchars($item['description']) ?></p>
        <p class="price"><?= $item['price'] ?> ₽</p>
        <form action="/api/add_to_cart.php" method="post">
          <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
          <button class="btn" type="submit">В корзину</button>
        </form>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';
?>
<section class="container section reveal">
  <h1>Меню</h1>
  <?php foreach ($menu as $category => $items): ?>
    <h2><?= htmlspecialchars($category) ?></h2>
    <div class="cards menu-cards">
      <?php foreach ($items as $item): ?>
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
  <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>

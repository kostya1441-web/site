<?php
$page_title = 'Меню — Квартирник';
require_once 'includes/header.php';
$menu = get_menu_all();

$type_icons = ['food' => '🍽', 'drinks' => '☕', 'bar' => '🍸'];
?>

<section class="page-hero">
  <div class="container">
    <span style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:10px">Кухня и бар</span>
    <h1>Наше меню</h1>
    <p>Авторская кухня и коктейли — всё для идеального вечера</p>
  </div>
</section>

<section class="menu-page">
  <div class="container">

    <?php if (empty($menu)): ?>
      <div style="text-align:center;padding:80px 0;color:var(--text-muted)">Меню скоро появится</div>
    <?php else: ?>

    <!-- Category tabs: Всё + each category -->
    <div class="menu-tabs" id="cat-tabs">
      <button class="menu-tab active" data-cat="all">🍽 Всё меню</button>
      <?php foreach ($menu as $cat): ?>
      <button class="menu-tab" data-cat="<?= $cat['id'] ?>">
        <?= h($cat['name']) ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- All items grouped by category, all visible by default -->
    <?php foreach ($menu as $cat): ?>
    <div class="menu-category visible" data-cat="<?= $cat['id'] ?>">
      <h2 class="menu-category-title"><?= h($cat['name']) ?></h2>
      <?php if (empty($cat['items'])): ?>
        <p style="color:var(--text-muted);padding:24px 0">Позиции появятся скоро</p>
      <?php else: ?>
      <div class="menu-grid">
        <?php foreach ($cat['items'] as $item): ?>
        <div class="menu-card fade-up">
          <div class="menu-card-img">
            <?php if ($item['image']): ?>
              <img src="<?= h($item['image']) ?>" alt="<?= h($item['name']) ?>" loading="lazy">
            <?php else: ?>
              <div class="no-img"><?= $type_icons[$cat['type']] ?? '🍽' ?></div>
            <?php endif; ?>
          </div>
          <div class="menu-card-body">
            <h4><?= h($item['name']) ?></h4>
            <?php if ($item['description']): ?>
              <p><?= h($item['description']) ?></p>
            <?php endif; ?>
            <div class="menu-card-footer">
              <span class="menu-price"><?= price($item['price']) ?></span>
              <button class="add-to-cart-btn"
                data-id="<?= $item['id'] ?>"
                data-name="<?= h($item['name']) ?>"
                data-price="<?= $item['price'] ?>"
                data-image="<?= h($item['image'] ?? '') ?>">
                + В корзину
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('#cat-tabs .menu-tab');
  const sections = document.querySelectorAll('.menu-category[data-cat]');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const cat = tab.dataset.cat;
      sections.forEach(s => {
        s.classList.toggle('visible', cat === 'all' || s.dataset.cat === cat);
      });
    });
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>

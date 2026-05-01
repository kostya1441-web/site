<?php
$page_title = 'Меню — Квартирник';
require_once 'includes/header.php';
$menu = get_menu_all();

$type_labels = ['food' => '🍽 Еда', 'drinks' => '☕ Напитки', 'bar' => '🍸 Бар'];
$type_icons  = ['food' => '🍽', 'drinks' => '☕', 'bar' => '🍸'];
$item_icons  = ['food' => '🍽', 'drinks' => '☕', 'bar' => '🍸'];
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

    <!-- Type tabs (food / drinks / bar) -->
    <div class="menu-tabs" id="type-tabs">
      <?php
      $types = [];
      foreach ($menu as $cat) {
          $types[$cat['type']] = $cat['type'];
      }
      $first = true;
      foreach ($types as $type):
      ?>
      <button class="menu-tab <?= $first ? 'active' : '' ?>" data-type="<?= $type ?>">
        <?= $type_labels[$type] ?? $type ?>
      </button>
      <?php $first = false; endforeach; ?>
    </div>

    <!-- Categories per type -->
    <?php
    $first_type = true;
    foreach ($types as $type):
    ?>
    <div class="menu-type-section <?= $first_type ? 'visible' : '' ?>" data-type="<?= $type ?>">
      <!-- Sub-tabs for categories within this type -->
      <?php
      $type_cats = array_filter($menu, fn($c) => $c['type'] === $type);
      if (count($type_cats) > 1):
      ?>
      <div class="menu-tabs" style="margin-bottom:32px">
        <?php $fc = true; foreach ($type_cats as $cat): ?>
        <button class="menu-tab <?= $fc ? 'active' : '' ?>" data-slug="<?= h($cat['slug']) ?>">
          <?= h($cat['name']) ?>
        </button>
        <?php $fc = false; endforeach; ?>
      </div>
      <?php endif; ?>

      <?php $fc = true; foreach ($type_cats as $cat): ?>
      <div class="menu-category <?= $fc ? 'visible' : '' ?>" data-slug="<?= h($cat['slug']) ?>">
        <?php if (count($type_cats) === 1): ?>
          <h2 class="menu-category-title"><?= h($cat['name']) ?></h2>
        <?php endif; ?>
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
                <div class="no-img"><?= $type_icons[$type] ?? '🍽' ?></div>
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
      <?php $fc = false; endforeach; ?>
    </div>
    <?php $first_type = false; endforeach; ?>

    <?php endif; ?>
  </div>
</section>

<script>
// Enhanced tab logic for type + category tabs
document.addEventListener('DOMContentLoaded', () => {
  // Type tabs
  const typeTabs = document.querySelectorAll('#type-tabs .menu-tab');
  const typeSections = document.querySelectorAll('.menu-type-section');
  typeTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      typeTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const type = tab.dataset.type;
      typeSections.forEach(s => {
        s.classList.toggle('visible', s.dataset.type === type);
      });
    });
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>

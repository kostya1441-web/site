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

    <!-- Type tabs (all / food / drinks / bar) -->
    <div class="menu-tabs" id="type-tabs">
      <button class="menu-tab active" data-type="all">🍽 Всё меню</button>
      <?php
      $types = [];
      foreach ($menu as $cat) {
          $types[$cat['type']] = $cat['type'];
      }
      foreach ($types as $type): ?>
      <button class="menu-tab" data-type="<?= $type ?>">
        <?= $type_labels[$type] ?? $type ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Categories per type -->
    <?php foreach ($types as $type): ?>
    <div class="menu-type-section visible" data-type="<?= $type ?>">
      <!-- Sub-tabs for categories within this type -->
      <?php
      $type_cats = array_filter($menu, fn($c) => $c['type'] === $type);
      if (count($type_cats) > 1):
      ?>
      <div class="menu-tabs menu-cat-tabs" style="margin-bottom:32px" data-type="<?= $type ?>">
        <button class="menu-tab active" data-slug="all-<?= $type ?>">Все</button>
        <?php foreach ($type_cats as $cat): ?>
        <button class="menu-tab" data-slug="<?= h($cat['slug']) ?>">
          <?= h($cat['name']) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php foreach ($type_cats as $cat): ?>
      <div class="menu-category visible" data-slug="<?= h($cat['slug']) ?>" data-type-slug="<?= $type ?>">
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
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const typeTabs = document.querySelectorAll('#type-tabs .menu-tab');
  const typeSections = document.querySelectorAll('.menu-type-section');

  typeTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      typeTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const type = tab.dataset.type;
      if (type === 'all') {
        typeSections.forEach(s => s.classList.add('visible'));
        // Show all categories in each section
        document.querySelectorAll('.menu-category').forEach(c => c.classList.add('visible'));
        document.querySelectorAll('.menu-cat-tabs .menu-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.menu-cat-tabs .menu-tab[data-slug^="all-"]').forEach(t => t.classList.add('active'));
      } else {
        typeSections.forEach(s => s.classList.toggle('visible', s.dataset.type === type));
      }
    });
  });

  // Category sub-tabs within each type
  document.querySelectorAll('.menu-cat-tabs').forEach(tabRow => {
    const sectionType = tabRow.dataset.type;
    tabRow.querySelectorAll('.menu-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        tabRow.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const slug = tab.dataset.slug;
        document.querySelectorAll(`.menu-category[data-type-slug="${sectionType}"]`).forEach(cat => {
          cat.classList.toggle('visible', slug.startsWith('all-') || cat.dataset.slug === slug);
        });
      });
    });
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>

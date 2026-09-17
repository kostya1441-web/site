<?php
/** @var array $products */
/** @var array $categories */
/** @var array $filters */
/** @var array $pagination */
/** @var array|null $category */
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="/">Главная</a>
        <span>/</span>
        <?php if ($category): ?>
            <a href="/catalog">Каталог</a><span>/</span><span><?= e($category['name']) ?></span>
        <?php else: ?>
            <span>Каталог</span>
        <?php endif; ?>
    </nav>

    <header class="page-head">
        <h1 class="page-head__title"><?= e($heading) ?></h1>
        <?php if (!empty($description)): ?>
            <p class="page-head__text"><?= e($description) ?></p>
        <?php endif; ?>
    </header>

    <div class="catalog">
        <aside class="catalog__aside">
            <button class="filters-toggle btn btn--ghost" type="button" data-filters-toggle>Фильтры и категории</button>

            <div class="catalog__panel" data-filters>
                <div class="filter">
                    <h2 class="filter__title">Категории</h2>
                    <ul class="filter__list">
                        <li><a class="<?= $category ? '' : 'is-active' ?>" href="/catalog">Все товары</a></li>
                        <?php foreach ($categories as $item): ?>
                            <li>
                                <a class="<?= ($category && $category['id'] === $item['id']) ? 'is-active' : '' ?>"
                                   href="/catalog/<?= e($item['slug']) ?>">
                                    <?= e($item['icon'] ?: '•') ?> <?= e($item['name']) ?>
                                    <em><?= (int) $item['products_count'] ?></em>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <form class="filter filter--form" method="get" action="<?= $category ? '/catalog/' . e($category['slug']) : '/catalog' ?>">
                    <?php if ($filters['search'] !== ''): ?>
                        <input type="hidden" name="q" value="<?= e($filters['search']) ?>">
                    <?php endif; ?>
                    <h2 class="filter__title">Цена, ₽</h2>
                    <div class="filter__range">
                        <input type="number" name="min" min="0" step="10" placeholder="от" value="<?= $filters['min'] ? e((string) (int) $filters['min']) : '' ?>" aria-label="Цена от">
                        <span>—</span>
                        <input type="number" name="max" min="0" step="10" placeholder="до" value="<?= $filters['max'] ? e((string) (int) $filters['max']) : '' ?>" aria-label="Цена до">
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" name="stock" value="1" <?= $filters['only_stock'] ? 'checked' : '' ?>>
                        <span>Только в наличии</span>
                    </label>
                    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>">
                    <button class="btn btn--primary btn--block" type="submit">Применить</button>
                    <a class="filter__reset" href="<?= $category ? '/catalog/' . e($category['slug']) : '/catalog' ?>">Сбросить фильтры</a>
                </form>
            </div>
        </aside>

        <div class="catalog__content">
            <div class="catalog__toolbar">
                <span class="catalog__total">
                    Найдено <?= (int) $pagination['total'] ?> <?= plural((int) $pagination['total'], 'товар', 'товара', 'товаров') ?>
                </span>
                <form class="catalog__sort" method="get" data-autosubmit>
                    <?php foreach (['q', 'min', 'max', 'stock'] as $keep): ?>
                        <?php if (!empty($_GET[$keep])): ?>
                            <input type="hidden" name="<?= e($keep) ?>" value="<?= e((string) $_GET[$keep]) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <label for="sort">Сортировка</label>
                    <select name="sort" id="sort">
                        <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Сначала популярные</option>
                        <option value="new" <?= $filters['sort'] === 'new' ? 'selected' : '' ?>>Сначала новые</option>
                        <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Дешевле</option>
                        <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Дороже</option>
                        <option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>По названию</option>
                    </select>
                </form>
            </div>

            <?php if ($products): ?>
                <div class="grid grid--products">
                    <?php foreach ($products as $product): ?>
                        <?php \App\Core\View::partial('partials/product-card', ['product' => $product]); ?>
                    <?php endforeach; ?>
                </div>
                <?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
            <?php else: ?>
                <div class="empty">
                    <span aria-hidden="true">🧺</span>
                    <h2>Ничего не нашлось</h2>
                    <p>Попробуйте изменить фильтры или посмотрите весь каталог — мы обновляем ассортимент каждую неделю.</p>
                    <a class="btn btn--primary" href="/catalog">Весь каталог</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

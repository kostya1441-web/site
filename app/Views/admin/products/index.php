<?php /** @var array $products */ ?>
<div class="admin-head">
    <div>
        <h1>Товары</h1>
        <p>Всего <?= (int) $pagination['total'] ?> <?= plural((int) $pagination['total'], 'позиция', 'позиции', 'позиций') ?> в каталоге</p>
    </div>
    <a class="btn btn--primary" href="/admin/products/create">+ Добавить товар</a>
</div>

<form class="filters" method="get" action="/admin/products">
    <input type="search" name="q" value="<?= e($filters['search']) ?>" placeholder="Название или артикул">
    <select name="category">
        <option value="">Все категории</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= (int) $filters['category'] === (int) $category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?> (<?= (int) $category['products_count'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn--primary" type="submit">Найти</button>
    <a class="btn btn--ghost" href="/admin/products">Сброс</a>
</form>

<?php if ($products): ?>
    <div class="table-wrap panel">
        <table class="table table--products">
            <thead>
            <tr>
                <th></th>
                <th>Товар</th>
                <th>Категория</th>
                <th>Цена</th>
                <th>Остаток</th>
                <th>Показывать</th>
                <th>Хит</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td class="cell-thumb">
                        <img src="<?= e(product_image($product['image'])) ?>" alt="" width="52" height="52" loading="lazy">
                    </td>
                    <td>
                        <a class="strong" href="/admin/products/<?= (int) $product['id'] ?>/edit"><?= e($product['name']) ?></a>
                        <small class="muted"><?= e($product['sku'] ?: 'без артикула') ?> · /<?= e($product['slug']) ?></small>
                    </td>
                    <td><?= e($product['category_name'] ?? '— без категории —') ?></td>
                    <td>
                        <strong><?= price($product['price']) ?></strong>
                        <small class="muted">за <?= e($product['unit']) ?></small>
                    </td>
                    <td>
                        <span class="stock <?= (int) $product['stock'] <= 0 ? 'stock--out' : ((int) $product['stock'] <= 5 ? 'stock--low' : '') ?>">
                            <?= (int) $product['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="post" action="/admin/products/<?= (int) $product['id'] ?>/toggle/is_active" data-toggle-form>
                            <?= csrf_field() ?>
                            <button type="submit" class="switch <?= $product['is_active'] ? 'is-on' : '' ?>" aria-label="Переключить видимость"><span></span></button>
                        </form>
                    </td>
                    <td>
                        <form method="post" action="/admin/products/<?= (int) $product['id'] ?>/toggle/is_featured" data-toggle-form>
                            <?= csrf_field() ?>
                            <button type="submit" class="star <?= $product['is_featured'] ? 'is-on' : '' ?>" aria-label="Отметить как хит">★</button>
                        </form>
                    </td>
                    <td class="cell-actions">
                        <a class="btn btn--ghost btn--sm" href="/admin/products/<?= (int) $product['id'] ?>/edit">Изменить</a>
                        <form method="post" action="/admin/products/<?= (int) $product['id'] ?>/delete" onsubmit="return confirm('Удалить товар «<?= e($product['name']) ?>»?')">
                            <?= csrf_field() ?>
                            <button class="btn btn--danger btn--sm" type="submit">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
<?php else: ?>
    <div class="panel empty-box">
        <p>Товаров не найдено.</p>
        <a class="btn btn--primary" href="/admin/products/create">Добавить первый товар</a>
    </div>
<?php endif; ?>

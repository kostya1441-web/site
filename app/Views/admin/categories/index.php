<?php /** @var array $categories */ ?>
<div class="admin-head">
    <div>
        <h1>Категории</h1>
        <p>Разделы каталога, которые видят покупатели</p>
    </div>
    <a class="btn btn--primary" href="/admin/categories/create">+ Добавить категорию</a>
</div>

<?php if ($categories): ?>
    <div class="table-wrap panel">
        <table class="table">
            <thead>
            <tr><th></th><th>Название</th><th>Ссылка</th><th>Товаров</th><th>Порядок</th><th>Статус</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td class="cell-thumb">
                        <?php if ($category['image']): ?>
                            <img src="<?= e(product_image($category['image'], 'category.svg')) ?>" alt="" width="52" height="52" loading="lazy">
                        <?php else: ?>
                            <span class="thumb-icon"><?= e($category['icon'] ?: '🧺') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="strong" href="/admin/categories/<?= (int) $category['id'] ?>/edit"><?= e($category['name']) ?></a>
                        <?php if ($category['description']): ?><small class="muted"><?= e(excerpt($category['description'], 60)) ?></small><?php endif; ?>
                    </td>
                    <td class="muted">/catalog/<?= e($category['slug']) ?></td>
                    <td><?= (int) $category['products_count'] ?></td>
                    <td><?= (int) $category['sort_order'] ?></td>
                    <td>
                        <span class="chip <?= $category['is_active'] ? 'chip--completed' : 'chip--canceled' ?>">
                            <?= $category['is_active'] ? 'Активна' : 'Скрыта' ?>
                        </span>
                    </td>
                    <td class="cell-actions">
                        <a class="btn btn--ghost btn--sm" href="/admin/categories/<?= (int) $category['id'] ?>/edit">Изменить</a>
                        <form method="post" action="/admin/categories/<?= (int) $category['id'] ?>/delete"
                              onsubmit="return confirm('Удалить категорию «<?= e($category['name']) ?>»? Товары останутся без категории.')">
                            <?= csrf_field() ?>
                            <button class="btn btn--danger btn--sm" type="submit">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="panel empty-box">
        <p>Категорий пока нет.</p>
        <a class="btn btn--primary" href="/admin/categories/create">Создать категорию</a>
    </div>
<?php endif; ?>

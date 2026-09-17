<?php
use App\Models\Product;
/** @var array|null $product */
/** @var array $categories */
$isEdit = $product !== null;
$action = u($isEdit ? '/admin/products/' . (int) $product['id'] : '/admin/products');
$value  = static fn (string $key, string $default = '') => $isEdit ? e((string) ($product[$key] ?? '')) : old($key, $default);
?>
<div class="admin-head">
    <div>
        <a class="back-link" href="<?= u('/admin/products') ?>">← К списку товаров</a>
        <h1><?= $isEdit ? 'Редактирование товара' : 'Новый товар' ?></h1>
        <?php if ($isEdit): ?>
            <p><a href="<?= u('/') ?>product/<?= e($product['slug']) ?>" target="_blank" rel="noopener">Открыть на сайте ↗</a></p>
        <?php endif; ?>
    </div>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="admin-cols admin-cols--2-1">
    <?= csrf_field() ?>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Основное</h2>
            <label class="field">
                <span>Название *</span>
                <input type="text" name="name" required maxlength="190" value="<?= $value('name') ?>" placeholder="Говядина, вырезка">
            </label>
            <label class="field">
                <span>Ссылка (slug)</span>
                <input type="text" name="slug" maxlength="190" value="<?= $value('slug') ?>" placeholder="оставьте пустым — сгенерируем из названия">
            </label>
            <div class="form-grid">
                <label class="field">
                    <span>Категория</span>
                    <select name="category_id">
                        <option value="">— без категории —</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= $isEdit && (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Артикул</span>
                    <input type="text" name="sku" maxlength="64" value="<?= $value('sku') ?>">
                </label>
            </div>
            <label class="field">
                <span>Краткое описание (для карточки в каталоге)</span>
                <textarea name="short_description" rows="2" maxlength="500"><?= $value('short_description') ?></textarea>
            </label>
            <label class="field">
                <span>Полное описание</span>
                <textarea name="description" rows="8"><?= $value('description') ?></textarea>
            </label>
        </section>

        <section class="panel">
            <h2 class="panel__title">Цена и наличие</h2>
            <div class="form-grid form-grid--3">
                <label class="field">
                    <span>Цена, ₽ *</span>
                    <input type="number" name="price" step="0.01" min="0" required value="<?= $isEdit ? e(rtrim(rtrim(number_format((float) $product['price'], 2, '.', ''), '0'), '.')) : old('price') ?>">
                </label>
                <label class="field">
                    <span>Старая цена, ₽</span>
                    <input type="number" name="old_price" step="0.01" min="0" value="<?= $isEdit && $product['old_price'] ? e(rtrim(rtrim(number_format((float) $product['old_price'], 2, '.', ''), '0'), '.')) : old('old_price') ?>">
                </label>
                <label class="field">
                    <span>Единица</span>
                    <select name="unit">
                        <?php foreach (Product::UNITS as $unit): ?>
                            <option value="<?= e($unit) ?>" <?= $isEdit && $product['unit'] === $unit ? 'selected' : '' ?>><?= e($unit) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Остаток</span>
                    <input type="number" name="stock" min="0" value="<?= $isEdit ? (int) $product['stock'] : old('stock', '0') ?>">
                </label>
                <label class="field">
                    <span>Средний вес, кг</span>
                    <input type="number" name="weight" step="0.001" min="0" value="<?= $isEdit && $product['weight'] ? e((string) (float) $product['weight']) : old('weight') ?>">
                </label>
                <label class="field">
                    <span>Порядок сортировки</span>
                    <input type="number" name="sort_order" value="<?= $isEdit ? (int) $product['sort_order'] : old('sort_order', '0') ?>">
                </label>
            </div>
        </section>
    </div>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Публикация</h2>
            <label class="checkbox">
                <input type="checkbox" name="is_active" value="1" <?= !$isEdit || $product['is_active'] ? 'checked' : '' ?>>
                <span>Показывать на сайте</span>
            </label>
            <label class="checkbox">
                <input type="checkbox" name="is_featured" value="1" <?= $isEdit && $product['is_featured'] ? 'checked' : '' ?>>
                <span>Хит продаж (на главной)</span>
            </label>
            <button class="btn btn--primary btn--block btn--lg" type="submit"><?= $isEdit ? 'Сохранить изменения' : 'Создать товар' ?></button>
        </section>

        <section class="panel">
            <h2 class="panel__title">Фотография</h2>
            <?php if ($isEdit && $product['image']): ?>
                <img class="preview" src="<?= e(product_image($product['image'])) ?>" alt="" width="240" height="180">
                <label class="checkbox">
                    <input type="checkbox" name="remove_image" value="1">
                    <span>Удалить текущее фото</span>
                </label>
            <?php endif; ?>
            <label class="field">
                <span>Загрузить новое фото</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            </label>
            <p class="muted small">JPEG, PNG или WebP до 6 МБ. Большие изображения автоматически уменьшаются до 1200 px.</p>
        </section>

        <?php if ($isEdit): ?>
            <section class="panel panel--danger">
                <h2 class="panel__title">Удаление</h2>
                <p class="muted small">Товар исчезнет из каталога. В уже оформленных заказах позиция сохранится.</p>
            </section>
        <?php endif; ?>
    </div>
</form>

<?php if ($isEdit): ?>
    <form method="post" action="<?= u('/') ?>admin/products/<?= (int) $product['id'] ?>/delete" class="delete-form"
          onsubmit="return confirm('Удалить товар «<?= e($product['name']) ?>»?')">
        <?= csrf_field() ?>
        <button class="btn btn--danger" type="submit">Удалить товар</button>
    </form>
<?php endif; ?>

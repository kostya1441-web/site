<?php
/** @var array|null $category */
$isEdit = $category !== null;
$action = u($isEdit ? '/admin/categories/' . (int) $category['id'] : '/admin/categories');
$value  = static fn (string $key, string $default = '') => $isEdit ? e((string) ($category[$key] ?? '')) : old($key, $default);
?>
<div class="admin-head">
    <div>
        <a class="back-link" href="<?= u('/admin/categories') ?>">← К списку категорий</a>
        <h1><?= $isEdit ? 'Редактирование категории' : 'Новая категория' ?></h1>
    </div>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="admin-cols admin-cols--2-1">
    <?= csrf_field() ?>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Данные категории</h2>
            <label class="field">
                <span>Название *</span>
                <input type="text" name="name" required maxlength="190" value="<?= $value('name') ?>" placeholder="Мясо и птица">
            </label>
            <label class="field">
                <span>Ссылка (slug)</span>
                <input type="text" name="slug" maxlength="190" value="<?= $value('slug') ?>" placeholder="myaso-i-ptitsa">
            </label>
            <div class="form-grid">
                <label class="field">
                    <span>Эмодзи-иконка</span>
                    <input type="text" name="icon" maxlength="8" value="<?= $value('icon') ?>" placeholder="🥩">
                </label>
                <label class="field">
                    <span>Порядок сортировки</span>
                    <input type="number" name="sort_order" value="<?= $isEdit ? (int) $category['sort_order'] : old('sort_order', '0') ?>">
                </label>
            </div>
            <label class="field">
                <span>Описание (текст под заголовком в каталоге)</span>
                <textarea name="description" rows="4" maxlength="1000"><?= $value('description') ?></textarea>
            </label>
        </section>
    </div>

    <div class="admin-col">
        <section class="panel">
            <h2 class="panel__title">Публикация</h2>
            <label class="checkbox">
                <input type="checkbox" name="is_active" value="1" <?= !$isEdit || $category['is_active'] ? 'checked' : '' ?>>
                <span>Показывать в каталоге</span>
            </label>
            <button class="btn btn--primary btn--block btn--lg" type="submit"><?= $isEdit ? 'Сохранить' : 'Создать категорию' ?></button>
        </section>

        <section class="panel">
            <h2 class="panel__title">Изображение</h2>
            <?php if ($isEdit && $category['image']): ?>
                <img class="preview" src="<?= e(product_image($category['image'], 'category.svg')) ?>" alt="" width="240" height="160">
                <label class="checkbox">
                    <input type="checkbox" name="remove_image" value="1">
                    <span>Удалить текущее изображение</span>
                </label>
            <?php endif; ?>
            <label class="field">
                <span>Загрузить изображение</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            </label>
            <p class="muted small">Если изображения нет, на сайте показывается эмодзи-иконка.</p>
        </section>
    </div>
</form>

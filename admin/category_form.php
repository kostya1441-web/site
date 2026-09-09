<?php
require_once __DIR__ . '/includes/admin_auth.php';
requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
$category = null;
if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $category = $stmt->fetch();
    if (!$category) {
        redirect('/admin/categories.php');
    }
}

$errors = [];
$form = $category ?: ['name' => '', 'description' => '', 'image' => null, 'sort_order' => 0, 'is_active' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Истёк срок действия формы, попробуйте ещё раз.';
    }

    $form['name'] = trim($_POST['name'] ?? '');
    $form['description'] = trim($_POST['description'] ?? '');
    $form['sort_order'] = (int)($_POST['sort_order'] ?? 0);
    $form['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    if ($form['name'] === '') $errors[] = 'Укажите название категории.';

    $newImage = null;
    if (empty($errors)) {
        try {
            $newImage = handleUploadedImage($_FILES['image'] ?? [], 'categories');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        $slugBase = slugify($form['name']);
        $slug = uniqueSlug('categories', $slugBase, $id ?: null);

        if ($category) {
            if ($newImage) {
                deleteImageFile($category['image'], 'categories');
                $form['image'] = $newImage;
            }
            $stmt = db()->prepare(
                'UPDATE categories SET name=:name, slug=:slug, description=:description, image=:image,
                    sort_order=:sort_order, is_active=:is_active WHERE id=:id'
            );
            $stmt->execute([
                'name' => $form['name'], 'slug' => $slug, 'description' => $form['description'],
                'image' => $form['image'], 'sort_order' => $form['sort_order'],
                'is_active' => $form['is_active'], 'id' => $id,
            ]);
        } else {
            $stmt = db()->prepare(
                'INSERT INTO categories (name, slug, description, image, sort_order, is_active)
                 VALUES (:name, :slug, :description, :image, :sort_order, :is_active)'
            );
            $stmt->execute([
                'name' => $form['name'], 'slug' => $slug, 'description' => $form['description'],
                'image' => $newImage, 'sort_order' => $form['sort_order'], 'is_active' => $form['is_active'],
            ]);
        }

        redirect('/admin/categories.php');
    }
}

$pageTitle = $category ? 'Редактирование категории' : 'Новая категория';
$activeNav = 'categories';
require __DIR__ . '/includes/admin_header.php';
?>
<?php foreach ($errors as $error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endforeach; ?>

<div class="admin-card">
    <form method="post" action="/admin/category_form.php<?= $id ? '?id=' . $id : '' ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label>Название *</label>
            <input type="text" name="name" class="form-control" required value="<?= e($form['name']) ?>">
        </div>
        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" class="form-control"><?= e($form['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Изображение</label>
            <?php if ($category && $category['image']): ?>
                <img class="thumb" style="margin-bottom:8px;" src="<?= e(imageUrl($category['image'], 'category')) ?>" alt="">
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Порядок сортировки</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)$form['sort_order'] ?>">
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" <?= $form['is_active'] ? 'checked' : '' ?>> Активна (видна на сайте)</label>
            </div>
        </div>

        <button type="submit" class="btn">Сохранить</button>
        <a href="/admin/categories.php" class="btn btn-outline">Отмена</a>
    </form>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

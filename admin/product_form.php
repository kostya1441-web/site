<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
$product = null;
if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    if (!$product) {
        redirect('/admin/products.php');
    }
}

$categories = db()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
$errors = [];
$form = $product ?: [
    'category_id' => $categories[0]['id'] ?? 0,
    'name' => '', 'description' => '', 'price' => '', 'old_price' => '',
    'unit' => 'кг', 'image' => null, 'in_stock' => 1, 'is_featured' => 0, 'is_active' => 1, 'sort_order' => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Истёк срок действия формы, попробуйте ещё раз.';
    }

    $form['category_id'] = (int)($_POST['category_id'] ?? 0);
    $form['name'] = trim($_POST['name'] ?? '');
    $form['description'] = trim($_POST['description'] ?? '');
    $form['price'] = (float)($_POST['price'] ?? 0);
    $form['old_price'] = trim($_POST['old_price'] ?? '') !== '' ? (float)$_POST['old_price'] : null;
    $form['unit'] = trim($_POST['unit'] ?? 'кг');
    $form['in_stock'] = isset($_POST['in_stock']) ? 1 : 0;
    $form['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $form['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    $form['sort_order'] = (int)($_POST['sort_order'] ?? 0);

    if ($form['name'] === '') $errors[] = 'Укажите название товара.';
    if ($form['category_id'] <= 0) $errors[] = 'Выберите категорию.';
    if ($form['price'] < 0) $errors[] = 'Цена не может быть отрицательной.';

    $newImage = null;
    if (empty($errors)) {
        try {
            $newImage = handleUploadedImage($_FILES['image'] ?? [], 'products');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        $slugBase = slugify($form['name']);
        $slug = uniqueSlug('products', $slugBase, $id ?: null);

        if ($product) {
            if ($newImage) {
                deleteImageFile($product['image'], 'products');
                $form['image'] = $newImage;
            }
            $stmt = db()->prepare(
                'UPDATE products SET category_id=:category_id, name=:name, slug=:slug, description=:description,
                    price=:price, old_price=:old_price, unit=:unit, image=:image, in_stock=:in_stock,
                    is_featured=:is_featured, is_active=:is_active, sort_order=:sort_order WHERE id=:id'
            );
            $stmt->execute([
                'category_id' => $form['category_id'], 'name' => $form['name'], 'slug' => $slug,
                'description' => $form['description'], 'price' => $form['price'], 'old_price' => $form['old_price'],
                'unit' => $form['unit'], 'image' => $form['image'], 'in_stock' => $form['in_stock'],
                'is_featured' => $form['is_featured'], 'is_active' => $form['is_active'],
                'sort_order' => $form['sort_order'], 'id' => $id,
            ]);
        } else {
            $stmt = db()->prepare(
                'INSERT INTO products (category_id, name, slug, description, price, old_price, unit, image,
                    in_stock, is_featured, is_active, sort_order)
                 VALUES (:category_id, :name, :slug, :description, :price, :old_price, :unit, :image,
                    :in_stock, :is_featured, :is_active, :sort_order)'
            );
            $stmt->execute([
                'category_id' => $form['category_id'], 'name' => $form['name'], 'slug' => $slug,
                'description' => $form['description'], 'price' => $form['price'], 'old_price' => $form['old_price'],
                'unit' => $form['unit'], 'image' => $newImage, 'in_stock' => $form['in_stock'],
                'is_featured' => $form['is_featured'], 'is_active' => $form['is_active'],
                'sort_order' => $form['sort_order'],
            ]);
            $id = (int)db()->lastInsertId();
        }

        redirect('/admin/products.php');
    }
}

$pageTitle = $product ? 'Редактирование товара' : 'Новый товар';
$activeNav = 'products';
require __DIR__ . '/includes/admin_header.php';
?>
<?php foreach ($errors as $error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endforeach; ?>

<div class="admin-card">
    <form method="post" action="/admin/product_form.php<?= $id ? '?id=' . $id : '' ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Название *</label>
                <input type="text" name="name" class="form-control" required value="<?= e($form['name']) ?>">
            </div>
            <div class="form-group">
                <label>Категория *</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)$form['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" class="form-control"><?= e($form['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Цена *</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?= e((string)$form['price']) ?>">
            </div>
            <div class="form-group">
                <label>Старая цена (для скидки)</label>
                <input type="number" step="0.01" min="0" name="old_price" class="form-control" value="<?= e((string)($form['old_price'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label>Единица измерения</label>
                <input type="text" name="unit" class="form-control" value="<?= e($form['unit']) ?>" placeholder="кг, шт, л, уп">
            </div>
        </div>

        <div class="form-group">
            <label>Изображение <?= $product && $product['image'] ? '(текущее заменится при выборе нового)' : '' ?></label>
            <?php if ($product && $product['image']): ?>
                <img class="thumb" style="margin-bottom:8px;" src="<?= e(imageUrl($product['image'])) ?>" alt="">
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="in_stock" <?= $form['in_stock'] ? 'checked' : '' ?>> В наличии</label>
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_featured" <?= $form['is_featured'] ? 'checked' : '' ?>> Показывать на главной</label>
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" <?= $form['is_active'] ? 'checked' : '' ?>> Активен (виден на сайте)</label>
            </div>
            <div class="form-group">
                <label>Порядок сортировки</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)$form['sort_order'] ?>">
            </div>
        </div>

        <button type="submit" class="btn">Сохранить</button>
        <a href="/admin/products.php" class="btn btn-outline">Отмена</a>
    </form>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

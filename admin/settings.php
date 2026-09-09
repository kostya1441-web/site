<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin = requireAdminLogin();

$fields = [
    'site_name' => 'Название сайта',
    'site_tagline' => 'Слоган / описание',
    'phone' => 'Телефон',
    'email' => 'Email',
    'address' => 'Адрес (самовывоз)',
    'work_hours' => 'Режим работы',
    'map_coords' => 'Координаты карты (широта,долгота)',
    'vk_link' => 'Ссылка ВКонтакте',
    'instagram_link' => 'Ссылка Instagram',
    'whatsapp_link' => 'Ссылка WhatsApp',
    'delivery_cost' => 'Стоимость доставки, ₽',
    'free_delivery_from' => 'Бесплатная доставка от суммы, ₽',
    'min_order_amount' => 'Минимальная сумма заказа, ₽',
];

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Истёк срок действия формы, попробуйте ещё раз.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = :v');

        foreach (array_keys($fields) as $key) {
            $stmt->execute(['k' => $key, 'v' => trim($_POST[$key] ?? '')]);
        }
        // about_text содержит доверенный HTML, редактируемый администратором
        $stmt->execute(['k' => 'about_text', 'v' => $_POST['about_text'] ?? '']);

        // Смена пароля администратора (опционально)
        $newPassword = trim($_POST['new_password'] ?? '');
        if ($newPassword !== '') {
            if (mb_strlen($newPassword) < 6) {
                $errors[] = 'Новый пароль должен быть не короче 6 символов.';
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE admin_users SET password_hash = :h WHERE id = :id')
                    ->execute(['h' => $hash, 'id' => $admin['id']]);
            }
        }

        if (empty($errors)) {
            $message = 'Настройки сохранены.';
        }
    }
}

// Перечитываем настройки заново (кэш в setting() статический, поэтому читаем напрямую)
$rows = db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
$values = [];
foreach ($rows as $row) {
    $values[$row['key']] = $row['value'];
}

$pageTitle = 'Настройки сайта';
$activeNav = 'settings';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endforeach; ?>

<div class="admin-card">
    <form method="post" action="/admin/settings.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <h3 style="margin-top:0;">Контактная информация</h3>
        <div class="form-row">
            <?php foreach ($fields as $key => $label): ?>
                <div class="form-group">
                    <label><?= e($label) ?></label>
                    <input type="text" name="<?= e($key) ?>" class="form-control" value="<?= e($values[$key] ?? '') ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <h3>О компании (текст страницы «О нас», поддерживается HTML)</h3>
        <div class="form-group">
            <textarea name="about_text" class="form-control" style="min-height:200px;"><?= e($values['about_text'] ?? '') ?></textarea>
        </div>

        <h3>Смена пароля администратора</h3>
        <div class="form-group">
            <label>Новый пароль (оставьте пустым, если не меняете)</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn">Сохранить настройки</button>
    </form>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>

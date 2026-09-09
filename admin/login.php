<?php
require_once __DIR__ . '/includes/admin_auth.php';

if (currentAdmin()) {
    redirect('/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $error = 'Истёк срок действия формы, попробуйте ещё раз.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = :u AND is_active = 1 LIMIT 1');
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['id'];
            redirect('/admin/index.php');
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Вход в админку — <?= e(setting('site_name')) ?></title>
<link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="login-wrap">
    <div class="login-box">
        <h1>🌾 Вход в админку</h1>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="/admin/login.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn" style="width:100%;">Войти</button>
        </form>
    </div>
</div>
</body>
</html>

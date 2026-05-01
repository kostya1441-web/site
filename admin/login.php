<?php
require_once __DIR__ . '/../includes/auth.php';

admin_start_session();
if (admin_check()) {
    header('Location: /admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (admin_login($user, $pass)) {
        header('Location: /admin/');
        exit;
    }
    $error = 'Неверный логин или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Вход — Квартирник Админ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="logo-name">Квартирник</div>
    <div class="logo-sub">Панель управления</div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label for="username">Логин</label>
        <input type="text" id="username" name="username" placeholder="admin" autocomplete="username" autofocus required>
      </div>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">
        Войти →
      </button>
    </form>
    <div style="margin-top:20px;text-align:center;font-size:0.8rem;color:var(--text-muted)">
      <a href="/" style="color:var(--text-muted)">← На сайт</a>
    </div>
  </div>
</div>
</body>
</html>

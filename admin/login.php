<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($login === 'admin' && $password === 'kvartirnik2026') {
        $_SESSION['admin'] = true;
        header('Location: /admin/dashboard.php');
        exit;
    }

    $error = 'Неверный логин или пароль';
}
include __DIR__ . '/../includes/header.php';
?>
<section class="container section">
  <h1>Админ-панель: вход</h1>
  <?php if ($error): ?><p class="notice"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form class="form-card" method="post">
    <label>Логин <input type="text" name="login" required></label>
    <label>Пароль <input type="password" name="password" required></label>
    <button class="btn" type="submit">Войти</button>
  </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>

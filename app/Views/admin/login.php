<div class="auth-card">
    <div class="auth-card__head">
        <span aria-hidden="true">🌾</span>
        <h1>Ваш фермер</h1>
        <p>Панель управления магазином</p>
    </div>
    <form method="post" action="/admin/login" class="auth-form">
        <?= csrf_field() ?>
        <label class="field">
            <span>Логин</span>
            <input type="text" name="login" required autofocus autocomplete="username" value="<?= old('login') ?>">
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="btn btn--primary btn--block btn--lg" type="submit">Войти</button>
    </form>
    <a class="auth-card__back" href="/">← Вернуться на сайт</a>
</div>

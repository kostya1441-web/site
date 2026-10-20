<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['__form']) && $_POST['__form'] === 'account') {
        $username = trim($_POST['username'] ?? '');
        $newPassword = trim($_POST['newPassword'] ?? '');
        if ($newPassword !== '' && strlen($newPassword) < 8) {
            $flash = ['error' => true, 'text' => 'Пароль должен быть не короче 8 символов. Логин/пароль не изменены.'];
        } else {
            update_credentials($username, $newPassword !== '' ? $newPassword : null);
            $flash = ['text' => 'Логин и пароль обновлены.'];
        }
    } else {
        $logo = handle_image_upload('logo', trim($_POST['logo_current'] ?? ''));
        $stmt = db()->prepare('UPDATE settings SET
            logo = ?, company_name = ?, phone = ?, email = ?,
            address_en = ?, address_ru = ?, footer_tag_en = ?, footer_tag_ru = ?, year = ?,
            cta_title_en = ?, cta_title_ru = ?, cta_body_en = ?, cta_body_ru = ?, meta_description = ?,
            theme_navy = ?, theme_amber = ?, theme_paper = ?
            WHERE id = 1');
        $stmt->execute([
            $logo,
            trim($_POST['companyName'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['email'] ?? ''),
            trim($_POST['address_en'] ?? ''), trim($_POST['address_ru'] ?? ''),
            trim($_POST['footerTag_en'] ?? ''), trim($_POST['footerTag_ru'] ?? ''), trim($_POST['year'] ?? ''),
            trim($_POST['ctaTitle_en'] ?? ''), trim($_POST['ctaTitle_ru'] ?? ''),
            trim($_POST['ctaBody_en'] ?? ''), trim($_POST['ctaBody_ru'] ?? ''),
            trim($_POST['metaDescription'] ?? ''),
            trim($_POST['themeNavy'] ?? '#122a3a'), trim($_POST['themeAmber'] ?? '#d97a28'), trim($_POST['themePaper'] ?? '#f5f3ee'),
        ]);
        $flash = ['text' => 'Настройки сохранены.'];
    }
}

$settings = get_settings();
$admin = current_admin();

$body = '<form method="post" enctype="multipart/form-data">
  <div class="a-card">
    <h2>Логотип и название</h2>
    ' . f_image('logo', 'Логотип (SVG/PNG, используется в шапке, футере и вкладке браузера)', $settings['logo']) . '
    ' . f_text('companyName', 'Название компании', $settings['company_name']) . '
    ' . f_text('phone', 'Телефон', $settings['phone']) . '
    ' . f_text('email', 'Email', $settings['email']) . '
    ' . f_bi('address', 'Адрес', $settings['address_en'], $settings['address_ru']) . '
  </div>
  <div class="a-card">
    <h2>Футер</h2>
    ' . f_bi('footerTag', 'Текст под логотипом в футере', $settings['footer_tag_en'], $settings['footer_tag_ru']) . '
    ' . f_text('year', 'Год копирайта', $settings['year']) . '
  </div>
  <div class="a-card">
    <h2>Блок «Поможем подобрать решение» (в конце каждой страницы)</h2>
    ' . f_bi('ctaTitle', 'Заголовок', $settings['cta_title_en'], $settings['cta_title_ru']) . '
    ' . f_bi('ctaBody', 'Текст', $settings['cta_body_en'], $settings['cta_body_ru'], true) . '
  </div>
  <div class="a-card">
    <h2>SEO</h2>
    ' . f_textarea('metaDescription', 'Meta description', $settings['meta_description']) . '
  </div>
  <div class="a-card">
    <h2>Внешний вид</h2>
    ' . f_text('themeNavy', 'Основной цвет (тёмный)', $settings['theme_navy'], ['type' => 'color']) . '
    ' . f_text('themeAmber', 'Акцентный цвет', $settings['theme_amber'], ['type' => 'color']) . '
    ' . f_text('themePaper', 'Цвет фона (светлый)', $settings['theme_paper'], ['type' => 'color']) . '
  </div>
  <button class="a-btn accent" type="submit">Сохранить</button>
</form>
<div class="a-card">
  <h2>Логин и пароль администратора</h2>
  <form method="post">
    <input type="hidden" name="__form" value="account">
    ' . f_text('username', 'Логин', $admin['username']) . '
    ' . f_text('newPassword', 'Новый пароль (оставьте пустым, чтобы не менять)', '', ['type' => 'password']) . '
    <button class="a-btn" type="submit">Обновить доступ</button>
  </form>
</div>';

echo admin_shell('Настройки и контакты', '/admin/settings.php', $body, $flash);

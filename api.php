<?php
declare(strict_types=1);

// Предупреждения PHP не должны попадать в ответ и ломать JSON: пишем их в лог.
@ini_set('display_errors', '0');
@ini_set('log_errors', '1');
ob_start();

// Перехват до подключения файлов — чтобы поймать и сбой внутри config.php или includes.
register_shutdown_function(static function () {
    $error = error_get_last();
    $fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if ($error && in_array($error['type'], $fatal, true)) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'ok'    => false,
            'error' => 'Ошибка PHP: ' . $error['message'] . ' (' . basename((string) $error['file']) . ':' . $error['line'] . ')',
        ], JSON_UNESCAPED_UNICODE);
    }
});

require_once __DIR__ . '/includes/content.php';

header('X-Content-Type-Options: nosniff');

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

try {
    switch ($action) {
        case 'login':   action_login();   break;
        case 'logout':  action_logout();  break;
        case 'state':   action_state();   break;
        case 'save':    action_save();    break;
        case 'upload':  action_upload();  break;
        case 'ping':    action_ping();    break;
        default:
            json_out(['ok' => false, 'error' => 'Неизвестное действие'], 400);
    }
} catch (PDOException $e) {
    error_log('[adamko] db: ' . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Ошибка базы данных. Проверьте настройки в config.php'], 500);
} catch (Throwable $e) {
    error_log('[adamko] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    json_out([
        'ok'    => false,
        'error' => 'Внутренняя ошибка сервера. Откройте api.php?action=ping — там написана причина',
    ], 500);
}

/** Самодиагностика: открывается в браузере как api.php?action=ping */
function action_ping(): void
{
    $result = [
        'ok'        => true,
        'php'       => PHP_VERSION,
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'gd'        => extension_loaded('gd'),
        'session'   => function_exists('session_start'),
        'uploads'   => 'не проверялась',
        'config'    => 'ok',
        'db'        => 'не проверялась',
        'tables'    => 'не проверялись',
    ];

    // Настройки читаем отдельно: опечатка в config.php — самая частая причина сбоя.
    try {
        $uploads = (string) config('uploads_dir');
        $result['uploads'] = is_dir($uploads) && is_writable($uploads)
            ? 'ok'
            : 'папка uploads недоступна для записи';
    } catch (Throwable $e) {
        $result['ok'] = false;
        $result['config'] = 'ошибка в config.php: ' . short_error($e);
        json_out($result);
    }

    try {
        $pdo = db();
        $result['db'] = 'ok';
    } catch (PDOException $e) {
        $result['ok'] = false;
        $result['db'] = 'нет подключения (код ' . $e->getCode() . '): ' . db_hint((string) $e->getCode());
        json_out($result);
    } catch (Throwable $e) {
        $result['ok'] = false;
        $result['db'] = 'не удалось подключиться: ' . short_error($e);
        json_out($result);
    }

    try {
        $pdo->query('SELECT 1 FROM settings LIMIT 1');
        $result['tables'] = 'ok';
    } catch (PDOException $e) {
        $result['ok'] = false;
        $result['tables'] = 'таблиц нет — импортируйте sql/schema.sql в phpMyAdmin';
    }

    if ($result['uploads'] !== 'ok') {
        $result['ok'] = false;
    }
    $result['admin'] = is_admin();

    json_out($result);
}

/** Сообщение об ошибке без полных путей на сервере. */
function short_error(Throwable $e): string
{
    return $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
}

/** Короткая подсказка по коду ошибки подключения. */
function db_hint(string $code): string
{
    $hints = [
        '1045' => 'неверный пользователь или пароль в config.php',
        '1049' => 'база с таким именем не найдена — проверьте name в config.php',
        '2002' => 'сервер базы недоступен — проверьте host в config.php',
        '2005' => 'неизвестный хост базы — проверьте host в config.php',
    ];
    return $hints[$code] ?? 'проверьте раздел db в config.php';
}

function body_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_admin(): void
{
    if (!is_admin()) {
        json_out(['ok' => false, 'error' => 'Нужен вход в панель администратора'], 401);
    }
}

function require_csrf(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? null);
    if (!csrf_check(is_string($token) ? $token : null)) {
        json_out(['ok' => false, 'error' => 'Сессия устарела, обновите страницу'], 403);
    }
}

function action_login(): void
{
    sr_session_start();
    $now = time();
    $fails = (int) ($_SESSION['sr_login_fails'] ?? 0);
    $until = (int) ($_SESSION['sr_login_block'] ?? 0);
    if ($fails >= 10 && $now < $until) {
        json_out(['ok' => false, 'error' => 'Слишком много попыток. Попробуйте через несколько минут'], 429);
    }

    $password = (string) (body_json()['password'] ?? '');
    if ($password !== '' && password_verify($password, (string) config('admin_password_hash'))) {
        session_regenerate_id(true);
        $_SESSION['sr_admin'] = true;
        $_SESSION['sr_login_fails'] = 0;
        json_out(['ok' => true, 'csrf' => csrf_token()]);
    }

    usleep(400000);
    $_SESSION['sr_login_fails'] = $fails + 1;
    $_SESSION['sr_login_block'] = $now + 300;
    json_out(['ok' => false, 'error' => 'Неверный пароль'], 401);
}

function action_logout(): void
{
    sr_session_start();
    $_SESSION = [];
    session_destroy();
    json_out(['ok' => true]);
}

function action_state(): void
{
    require_admin();
    json_out(['ok' => true, 'csrf' => csrf_token(), 'content' => content_load()]);
}

function action_save(): void
{
    require_admin();
    require_csrf();

    $data = body_json();
    if (!$data) {
        json_out(['ok' => false, 'error' => 'Пустой запрос'], 400);
    }
    content_save($data);
    json_out(['ok' => true, 'content' => content_load()]);
}

function action_upload(): void
{
    require_admin();
    require_csrf();

    if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'] ?? '')) {
        json_out(['ok' => false, 'error' => 'Файл не получен'], 400);
    }

    $file = $_FILES['file'];
    $maxBytes = (int) config('max_upload_mb') * 1024 * 1024;
    if (($file['size'] ?? 0) > $maxBytes) {
        json_out(['ok' => false, 'error' => 'Файл больше ' . config('max_upload_mb') . ' МБ'], 400);
    }

    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!$info || !isset($types[$info[2]])) {
        json_out(['ok' => false, 'error' => 'Поддерживаются только JPG, PNG, WEBP и GIF'], 400);
    }

    $dir = (string) config('uploads_dir');
    if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
        json_out(['ok' => false, 'error' => 'Папка uploads недоступна для записи'], 500);
    }
    if (!is_writable($dir)) {
        json_out(['ok' => false, 'error' => 'Папке uploads нужны права на запись (chmod 755 или 775)'], 500);
    }

    $ext  = $types[$info[2]];
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    $path = $dir . '/' . $name;

    if (!save_resized_image($file['tmp_name'], $path, $info[2])) {
        json_out(['ok' => false, 'error' => 'Не удалось обработать изображение'], 500);
    }

    json_out(['ok' => true, 'url' => rtrim((string) config('uploads_url'), '/') . '/' . $name]);
}

/** Уменьшает картинку до разумного размера и сохраняет в папку uploads. */
function save_resized_image(string $source, string $target, int $type): bool
{
    $maxSide = (int) config('image_max_side');
    $quality = (int) config('image_quality');

    if (!function_exists('imagecreatefromstring')) {
        return (bool) @move_uploaded_file($source, $target);
    }

    $data = @file_get_contents($source);
    if ($data === false) {
        return false;
    }
    $image = @imagecreatefromstring($data);
    if (!$image) {
        return false;
    }

    $w = imagesx($image);
    $h = imagesy($image);
    $scale = min(1, $maxSide / max($w, $h));
    if ($scale < 1) {
        $resized = imagescale($image, (int) round($w * $scale), (int) round($h * $scale));
        if ($resized) {
            imagedestroy($image);
            $image = $resized;
        }
    }

    switch ($type) {
        case IMAGETYPE_PNG:
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $ok = imagepng($image, $target, 6);
            break;
        case IMAGETYPE_WEBP:
            $ok = imagewebp($image, $target, $quality);
            break;
        case IMAGETYPE_GIF:
            $ok = imagegif($image, $target);
            break;
        default:
            $ok = imagejpeg($image, $target, $quality);
    }
    imagedestroy($image);

    if ($ok) {
        @chmod($target, 0644);
    }
    return (bool) $ok;
}

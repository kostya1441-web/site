<?php
/**
 * Общие вспомогательные функции сайта.
 */
require_once __DIR__ . '/../config/db.php';

/** Безопасный вывод в HTML */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Получить значение настройки сайта из таблицы settings */
function setting(string $key, string $default = ''): string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        $stmt = db()->query('SELECT `key`, `value` FROM settings');
        foreach ($stmt->fetchAll() as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }

    return $cache[$key] ?? $default;
}

/** Форматирование цены */
function formatPrice($price): string
{
    return number_format((float)$price, 0, ',', ' ') . ' ₽';
}

/** Транслитерация + слаг для URL */
function slugify(string $text): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i',
        'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
        'у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'',
        'э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    $text = mb_strtolower($text, 'UTF-8');
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item';
}

/** Уникальный слаг для таблицы/колонки, игнорируя указанный id */
function uniqueSlug(string $table, string $baseSlug, ?int $ignoreId = null): string
{
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $sql = "SELECT COUNT(*) FROM `$table` WHERE slug = :slug";
        $params = ['slug' => $slug];
        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ((int)$stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

/** Генерация CSRF-токена */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Проверка CSRF-токена */
function csrfCheck(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Редирект с завершением скрипта */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Получить список активных категорий */
function getActiveCategories(): array
{
    return db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();
}

/** Найти категорию по слагу */
function getCategoryBySlug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM categories WHERE slug = :slug AND is_active = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Найти товар по слагу (только активный) */
function getProductBySlug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p JOIN categories c ON c.id = p.category_id
        WHERE p.slug = :slug AND p.is_active = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Получить товар по id (для корзины), включая неактивные категории — на случай пометки товара неактивным */
function getProductById(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Путь к изображению товара/категории с заглушкой */
function imageUrl(?string $file, string $type = 'product'): string
{
    if ($file) {
        return '/uploads/' . ($type === 'category' ? 'categories/' : 'products/') . $file;
    }
    return '/assets/img/no-image.svg';
}

/** Генерация уникального номера заказа */
function generateOrderNumber(): string
{
    return date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

/** Человекочитаемые статусы заказа */
function orderStatusLabel(string $status): string
{
    $labels = [
        'new'        => 'Новый',
        'processing' => 'В обработке',
        'ready'      => 'Готов к выдаче',
        'shipped'    => 'Передан в доставку',
        'completed'  => 'Выполнен',
        'cancelled'  => 'Отменён',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Обработка загруженного изображения (для товаров/категорий).
 * Возвращает имя сохранённого файла или null, если файл не загружен.
 * Бросает RuntimeException при ошибке валидации.
 */
function handleUploadedImage(array $file, string $subdir): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Ошибка загрузки файла.');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Файл слишком большой (максимум 5 МБ).');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Разрешены только изображения JPG, PNG или WEBP.');
    }

    $targetDir = __DIR__ . '/../uploads/' . $subdir . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        throw new RuntimeException('Не удалось сохранить файл.');
    }

    return $filename;
}

/** Удалить файл изображения товара/категории, если он существует */
function deleteImageFile(?string $filename, string $subdir): void
{
    if (!$filename) {
        return;
    }
    $path = __DIR__ . '/../uploads/' . $subdir . '/' . $filename;
    if (is_file($path)) {
        unlink($path);
    }
}

function paymentStatusLabel(string $status): string
{
    $labels = [
        'pending'  => 'Ожидает оплаты',
        'paid'     => 'Оплачен',
        'failed'   => 'Не оплачен',
        'refunded' => 'Возврат',
    ];
    return $labels[$status] ?? $status;
}

<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function price(float $p): string {
    return number_format($p, 0, '.', ' ') . ' ₽';
}

function json_response(array $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function upload_image(array $file, string $prefix = 'img'): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $name = $prefix . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) return null;
    return '/assets/images/uploads/' . $name;
}

function get_menu_all(): array {
    $categories = DB::fetchAll('SELECT * FROM menu_categories WHERE active=1 ORDER BY sort_order');
    $items = DB::fetchAll('SELECT * FROM menu_items WHERE active=1 ORDER BY sort_order');
    foreach ($categories as &$cat) {
        $cat['items'] = array_values(array_filter($items, fn($i) => $i['category_id'] == $cat['id']));
    }
    return $categories;
}

function get_upcoming_events(int $limit = 4): array {
    return DB::fetchAll(
        'SELECT * FROM events WHERE active=1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT ?',
        [$limit]
    );
}

function get_events_filtered(?string $from = null, ?string $to = null): array {
    $sql = 'SELECT * FROM events WHERE active=1';
    $params = [];
    if ($from) { $sql .= ' AND event_date >= ?'; $params[] = $from; }
    if ($to)   { $sql .= ' AND event_date <= ?'; $params[] = $to; }
    $sql .= ' ORDER BY event_date ASC';
    return DB::fetchAll($sql, $params);
}

function format_date_ru(string $date): string {
    $months = ['', 'января','февраля','марта','апреля','мая','июня',
               'июля','августа','сентября','октября','ноября','декабря'];
    $days = ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'];
    $ts = strtotime($date);
    return $days[date('w', $ts)] . ', ' . (int)date('d', $ts) . ' ' . $months[(int)date('n', $ts)];
}

function active_nav(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

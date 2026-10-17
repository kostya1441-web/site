<?php
declare(strict_types=1);

const SR_ROOT = __DIR__ . '/..';

function config(?string $key = null)
{
    static $config = null;
    if ($config === null) {
        $config = require SR_ROOT . '/config.php';
    }
    if ($key === null) {
        return $config;
    }
    return $config[$key] ?? null;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $c = config('db');
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['name'], $c['charset'] ?? 'utf8mb4');
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    // За прокси хостинга признак приходит заголовком.
    return isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
}

/**
 * Открывает сессию. После вывода страницы это уже невозможно,
 * поэтому молча отказываемся вместо предупреждения PHP.
 */
function sr_session_start(): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }
    if (headers_sent()) {
        return false;
    }
    session_set_cookie_params([
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => is_https(),
    ]);
    return session_start();
}

/**
 * Вызывается в самом начале страницы, до вывода: продолжает уже открытую сессию.
 * Посетителям без сессии cookie не ставится.
 */
function sr_session_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (isset($_COOKIE[session_name()])) {
        sr_session_start();
    }
}

function is_admin(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // Сессии нет: либо посетитель не входил, либо страница уже выведена.
        if (!isset($_COOKIE[session_name()]) || !sr_session_start()) {
            return false;
        }
    }
    return !empty($_SESSION['sr_admin']);
}

function csrf_token(): string
{
    sr_session_start();
    if (empty($_SESSION['sr_csrf'])) {
        $_SESSION['sr_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['sr_csrf'];
}

function csrf_check(?string $token): bool
{
    sr_session_start();
    return !empty($_SESSION['sr_csrf']) && is_string($token) && hash_equals($_SESSION['sr_csrf'], $token);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Разбивает многострочное поле на непустые строки. */
function lines(?string $value): array
{
    $out = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $value) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $out[] = $line;
        }
    }
    return $out;
}

/** Транслитерация названия в слаг для адресной строки. */
function slugify(string $text, string $fallback = 'item'): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i',
        'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
        'у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'',
        'э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
    $text = trim($text, '-');
    if ($text === '') {
        $text = $fallback;
    }
    return mb_substr($text, 0, 80, 'UTF-8');
}

function json_out($data, int $status = 200): void
{
    // Чужой вывод (предупреждения, пробелы) выкидываем, иначе ответ перестаёт быть JSON.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Телефон в виде, пригодном для ссылки tel: */
function tel_href(string $phone): string
{
    return preg_replace('/[^+\d]/', '', $phone) ?? '';
}

/** Склонение: 1 позиция, 2 позиции, 5 позиций. */
function plural(int $n, string $one, string $few, string $many): string
{
    $a = abs($n) % 100;
    $b = $a % 10;
    if ($a > 10 && $a < 20) return $many;
    if ($b > 1 && $b < 5)   return $few;
    if ($b === 1)           return $one;
    return $many;
}

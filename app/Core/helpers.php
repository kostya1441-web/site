<?php

use App\Core\Config;
use App\Core\Csrf;

/** Экранирование для вывода в HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_field(): string
{
    return Csrf::field();
}

function url(string $path = '/'): string
{
    return rtrim(Config::get('app.url', ''), '/') . '/' . ltrim($path, '/');
}

/** Ссылка на статику с версией файла — чтобы браузер не держал старый кэш. */
function asset(string $path): string
{
    $path = '/assets/' . ltrim($path, '/');
    $file = dirname(__DIR__, 2) . '/public' . $path;
    $version = is_file($file) ? substr((string) filemtime($file), -6) : '1';
    return $path . '?v=' . $version;
}

/** 1290 -> «1 290 ₽» */
function price(float|int|string $value, bool $withCurrency = true): string
{
    $formatted = number_format((float) $value, ((float) $value == (int) $value) ? 0 : 2, ',', ' ');
    return $withCurrency ? $formatted . ' ₽' : $formatted;
}

/** Склонение: 1 товар / 2 товара / 5 товаров */
function plural(int $number, string $one, string $few, string $many): string
{
    $n  = abs($number) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
        return $many;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $few;
    }
    return $n1 === 1 ? $one : $many;
}

function product_image(?string $image, string $fallback = 'placeholder.svg'): string
{
    if ($image && is_file(dirname(__DIR__, 2) . '/public/uploads/' . $image)) {
        return '/uploads/' . $image;
    }
    return '/assets/img/' . $fallback;
}

/** Транслитерация в slug: «Колбаса домашняя» -> «kolbasa-domashnyaya» */
function slugify(string $text): string
{
    $map = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '-', '_' => '-',
    ];
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\-]+/u', '-', $text) ?? '';
    $text = preg_replace('/-+/', '-', $text) ?? '';
    return trim($text, '-');
}

function format_phone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($digits) === 11) {
        return sprintf('+7 (%s) %s-%s-%s', substr($digits, 1, 3), substr($digits, 4, 3), substr($digits, 7, 2), substr($digits, 9, 2));
    }
    return $phone;
}

function phone_link(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($digits) === 11 && $digits[0] === '8') {
        $digits = '7' . substr($digits, 1);
    }
    return 'tel:+' . $digits;
}

function date_ru(?string $datetime, bool $withTime = true): string
{
    if (!$datetime) {
        return '—';
    }
    $ts     = strtotime($datetime);
    $months = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
    $out    = date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    return $withTime ? $out . ', ' . date('H:i', $ts) : $out;
}

function is_active(string $path): bool
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $current = '/' . trim($current, '/');
    $path    = '/' . trim($path, '/');
    if ($path === '/') {
        return $current === '/';
    }
    return str_starts_with($current, $path);
}

function old(string $key, string $default = ''): string
{
    $input = $GLOBALS['_old_input'] ?? [];
    return e((string) ($input[$key] ?? $default));
}

function excerpt(?string $text, int $length = 120): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '…';
}

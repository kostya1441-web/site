<?php

namespace App\Core;

class Response
{
    public static function status(int $code): void
    {
        http_response_code($code);
    }

    public static function json(array $data, int $status = 200): void
    {
        self::status($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function redirect(string $url, int $status = 302): void
    {
        self::status($status);
        // Внутренние адреса дополняем папкой сайта, внешние (платёжный шлюз) — нет
        header('Location: ' . (str_starts_with($url, '/') ? u($url) : $url));
    }

    public static function back(string $fallback = '/'): void
    {
        self::redirect($_SERVER['HTTP_REFERER'] ?? $fallback);
    }
}

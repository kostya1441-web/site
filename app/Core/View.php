<?php

namespace App\Core;

/**
 * Простые PHP-шаблоны: render() отдаёт готовую страницу с layout,
 * partial() подключает кусок разметки.
 */
class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], string $layout = 'main'): void
    {
        echo self::capture($template, $data, $layout);
    }

    public static function capture(string $template, array $data = [], ?string $layout = 'main'): string
    {
        $content = self::partialToString($template, $data);

        if ($layout === null) {
            return $content;
        }

        return self::partialToString('layouts/' . $layout, array_merge($data, ['content' => $content]));
    }

    public static function partial(string $template, array $data = []): void
    {
        echo self::partialToString($template, $data);
    }

    private static function partialToString(string $template, array $data): string
    {
        $file = dirname(__DIR__) . '/Views/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Шаблон не найден: ' . $template);
        }

        extract(array_merge(self::$shared, $data), EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}

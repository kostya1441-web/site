<?php

namespace App\Services;

use App\Core\Config;

/**
 * Загрузка картинок товаров: проверка MIME, ограничение размера,
 * ресайз до 1200px по длинной стороне и конвертация в JPEG/PNG.
 */
class ImageUploader
{
    public static function store(array $file, string $prefix = 'p'): array
    {
        $maxSize = (int) Config::get('upload.max_size', 6 * 1024 * 1024);
        $allowed = (array) Config::get('upload.mime', ['image/jpeg', 'image/png', 'image/webp']);
        $dir     = (string) Config::get('upload.dir');

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Файл не загрузился (код ' . $file['error'] . ')'];
        }
        if ($file['size'] > $maxSize) {
            return ['ok' => false, 'error' => 'Файл больше ' . round($maxSize / 1024 / 1024) . ' МБ'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            return ['ok' => false, 'error' => 'Допустимы только JPEG, PNG и WebP'];
        }

        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return ['ok' => false, 'error' => 'Не удалось создать папку для загрузок'];
        }

        $extension = match ($mime) {
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $name = $prefix . '-' . date('Ymd') . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
        $path = $dir . '/' . $name;

        if (!self::resizeAndSave($file['tmp_name'], $path, $mime)) {
            // если GD не справился — сохраняем оригинал
            if (!move_uploaded_file($file['tmp_name'], $path) && !rename($file['tmp_name'], $path)) {
                return ['ok' => false, 'error' => 'Не удалось сохранить файл'];
            }
        }

        @chmod($path, 0644);
        return ['ok' => true, 'name' => $name];
    }

    private static function resizeAndSave(string $source, string $target, string $mime): bool
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return false;
        }

        $image = match ($mime) {
            'image/png'  => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
            default      => @imagecreatefromjpeg($source),
        };
        if (!$image) {
            return false;
        }

        $width  = imagesx($image);
        $height = imagesy($image);
        $max    = 1200;

        if ($width > $max || $height > $max) {
            $ratio     = min($max / $width, $max / $height);
            $newWidth  = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);
            $resized   = imagecreatetruecolor($newWidth, $newHeight);

            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $saved = match ($mime) {
            'image/png'  => imagepng($image, $target, 8),
            'image/webp' => imagewebp($image, $target, 85),
            default      => imagejpeg($image, $target, 86),
        };
        imagedestroy($image);

        return (bool) $saved;
    }

    public static function delete(?string $name): void
    {
        if (!$name) {
            return;
        }
        $path = Config::get('upload.dir') . '/' . basename($name);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

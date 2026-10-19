<?php
/**
 * Dev-only router for PHP's built-in server, which (unlike Apache) does
 * not read .htaccess. Run locally with:
 *   php -S localhost:8000 router.php
 * Production (Apache/shared hosting) uses .htaccess instead — this file
 * is never invoked there.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // let the built-in server serve the real file as-is
}
require __DIR__ . '/index.php';

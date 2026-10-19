<?php
require_once __DIR__ . '/../config.php';

/**
 * Resolves one image field given its logical (bracketed) path, e.g.
 * "blocks[0][items][2][image]", and the current value already read from
 * the parsed $_POST structure at that same path's "..._current" sibling.
 * The matching <input type="file"> is looked up by its flat, derived key
 * (see file_key_for()) rather than the bracketed name, since a bracketed
 * file input name would scatter across PHP's hoisted $_FILES structure.
 * Returns the new uploaded file's public URL, or $current unchanged if
 * no file was sent (so re-saving a form without picking a new file keeps
 * the existing image).
 */
function handle_image_upload(string $logicalPath, string $current = ''): string {
    $fieldName = file_key_for($logicalPath);
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $current;
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $current;
    }
    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!isset($allowed[$mime])) {
        return $current;
    }
    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0775, true);
    }
    $base = preg_replace('/[^a-z0-9]+/', '-', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
    $name = time() . '-' . bin2hex(random_bytes(3)) . '-' . substr($base, 0, 40) . '.' . $allowed[$mime];
    move_uploaded_file($file['tmp_name'], UPLOADS_DIR . '/' . $name);
    return UPLOADS_URL . '/' . $name;
}

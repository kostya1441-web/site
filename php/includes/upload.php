<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/helpers.php';

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
        upload_error_add(upload_error_message($file['error']));
        return $current;
    }
    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!isset($allowed[$mime])) {
        upload_error_add('неподдерживаемый формат файла «' . esc($file['name']) . '» (' . esc($mime) . '). Разрешены: PNG, JPEG, GIF, WEBP, SVG.');
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

function upload_error_message(int $code): string {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'файл слишком большой (максимум ' . ini_get('upload_max_filesize') . ')';
        case UPLOAD_ERR_PARTIAL:
            return 'файл загрузился не полностью, попробуйте ещё раз';
        default:
            return 'не удалось загрузить файл (код ошибки ' . $code . ')';
    }
}

function upload_error_add(string $message): void {
    global $__upload_errors;
    $__upload_errors[] = $message;
}

// Returns every upload error recorded since the last call, then clears them.
function upload_errors(): array {
    global $__upload_errors;
    $errors = $__upload_errors ?? [];
    $__upload_errors = [];
    return $errors;
}

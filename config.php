<?php
/**
 * Настройки сайта. Поменяйте данные доступа к базе на те, что выдал хостинг.
 * Пароль администратора хранится хешем — как его сменить, написано в README.md.
 */
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'adamko_sibir',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // Пароль по умолчанию: adamko2026
    'admin_password_hash' => '$2y$12$vHsT.tJXny2YwkM7rmMyNeqNIgwqy6XR5mWLLqF6qai4JkTCx5CDa',

    'uploads_dir'    => __DIR__ . '/uploads',
    'uploads_url'    => 'uploads',
    'max_upload_mb'  => 15,
    'image_max_side' => 1600,
    'image_quality'  => 82,
];

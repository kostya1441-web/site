<?php
/**
 * Database and app configuration.
 *
 * On shared hosting, just fill in the four DB_* constants below with the
 * values your host (or phpMyAdmin) gave you when you created the database.
 * Environment variables override these if set, which is convenient for
 * Docker/CI but entirely optional — most shared hosts never set them.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'hazleton_pumps');
define('DB_USER', getenv('DB_USER') ?: 'hazleton');
define('DB_PASS', getenv('DB_PASS') ?: 'hazleton_dev_pw');
define('DB_CHARSET', 'utf8mb4');

define('SITE_ROOT', __DIR__);
define('UPLOADS_DIR', __DIR__ . '/uploads');
define('UPLOADS_URL', '/uploads');

error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_DEBUG') ? '1' : '0');
ini_set('log_errors', '1');

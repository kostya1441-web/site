-- CS2 Server Website Schema
-- Run this after setting up your database in config.php

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Users (Steam login)
CREATE TABLE IF NOT EXISTS `users` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `steamid64`   VARCHAR(20)  NOT NULL UNIQUE,
  `name`        VARCHAR(100) NOT NULL DEFAULT '',
  `avatar`      VARCHAR(512) NOT NULL DEFAULT '',
  `role`        ENUM('user','vip','admin','superadmin') NOT NULL DEFAULT 'user',
  `last_login`  DATETIME     NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bans
CREATE TABLE IF NOT EXISTS `bans` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `steamid`       VARCHAR(30)  NOT NULL,
  `steamid64`     VARCHAR(20)  NOT NULL DEFAULT '',
  `name`          VARCHAR(100) NOT NULL DEFAULT '',
  `admin_steamid` VARCHAR(30)  NOT NULL DEFAULT 'CONSOLE',
  `admin_name`    VARCHAR(100) NOT NULL DEFAULT 'Console',
  `reason`        VARCHAR(500) NOT NULL DEFAULT '',
  `duration`      INT          NOT NULL DEFAULT 0 COMMENT '0 = permanent, minutes otherwise',
  `expires_at`    DATETIME     NULL,
  `active`        TINYINT(1)   NOT NULL DEFAULT 1,
  `server_id`     TINYINT      NOT NULL DEFAULT 0,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_steamid` (`steamid`),
  INDEX `idx_active`  (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mutes
CREATE TABLE IF NOT EXISTS `mutes` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `steamid`       VARCHAR(30)  NOT NULL,
  `steamid64`     VARCHAR(20)  NOT NULL DEFAULT '',
  `name`          VARCHAR(100) NOT NULL DEFAULT '',
  `admin_steamid` VARCHAR(30)  NOT NULL DEFAULT 'CONSOLE',
  `admin_name`    VARCHAR(100) NOT NULL DEFAULT 'Console',
  `reason`        VARCHAR(500) NOT NULL DEFAULT '',
  `duration`      INT          NOT NULL DEFAULT 0 COMMENT '0 = permanent, minutes otherwise',
  `expires_at`    DATETIME     NULL,
  `active`        TINYINT(1)   NOT NULL DEFAULT 1,
  `type`          ENUM('mute','gag','silence') NOT NULL DEFAULT 'mute',
  `server_id`     TINYINT      NOT NULL DEFAULT 0,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_steamid` (`steamid`),
  INDEX `idx_active`  (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- News articles
CREATE TABLE IF NOT EXISTS `news` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(300)  NOT NULL,
  `content`    TEXT          NOT NULL,
  `tag`        VARCHAR(50)   NOT NULL DEFAULT 'Новость',
  `tag_color`  VARCHAR(20)   NOT NULL DEFAULT '#3b82f6',
  `author`     VARCHAR(100)  NOT NULL DEFAULT 'Admin',
  `published`  TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_published` (`published`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site settings (key-value)
CREATE TABLE IF NOT EXISTS `settings` (
  `key`   VARCHAR(100) NOT NULL PRIMARY KEY,
  `value` TEXT         NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default settings
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('vk_url',       ''),
  ('telegram_url', ''),
  ('discord_url',  ''),
  ('rules_content', '');

-- Sample news
INSERT IGNORE INTO `news` (`id`, `title`, `content`, `tag`, `tag_color`, `author`) VALUES
(1, 'Открытие сервера!', 'Рады сообщить об открытии нашего CS2 сервера. Присоединяйтесь и получите удовольствие от игры!', 'Событие', '#22c55e', 'Admin'),
(2, 'Обновление плагинов', 'Обновлены плагины LvlRanks и Donate. Добавлены новые привилегии для VIP-игроков.', 'Обновление', '#3b82f6', 'Admin'),
(3, 'Набор в администраторы', 'Открыт набор в команду администраторов. Требования: возраст 16+, активность на сервере от 2 недель.', 'Набор', '#f59e0b', 'Admin');

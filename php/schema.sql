-- Hazleton Pumps — database schema.
-- Import this (and then seed.sql) via phpMyAdmin or `mysql < schema.sql`.
-- Safe to re-run: every table is created only if missing.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  logo VARCHAR(500) NOT NULL DEFAULT '/assets/img/logo.svg',
  company_name VARCHAR(255) NOT NULL DEFAULT '',
  phone VARCHAR(100) NOT NULL DEFAULT '',
  email VARCHAR(255) NOT NULL DEFAULT '',
  address_en TEXT,
  address_ru TEXT,
  footer_tag_en TEXT,
  footer_tag_ru TEXT,
  year VARCHAR(10) NOT NULL DEFAULT '',
  cta_title_en VARCHAR(255),
  cta_title_ru VARCHAR(255),
  cta_body_en TEXT,
  cta_body_ru TEXT,
  meta_description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nav_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  label_en VARCHAR(255) NOT NULL DEFAULT '',
  label_ru VARCHAR(255) NOT NULL DEFAULT '',
  href VARCHAR(500) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (parent_id) REFERENCES nav_items(id) ON DELETE CASCADE,
  INDEX (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) NOT NULL,
  title_en VARCHAR(255) NOT NULL DEFAULT '',
  title_ru VARCHAR(255) NOT NULL DEFAULT '',
  eyebrow_en VARCHAR(255) NOT NULL DEFAULT '',
  eyebrow_ru VARCHAR(255) NOT NULL DEFAULT '',
  show_head TINYINT(1) NOT NULL DEFAULT 1,
  show_cta TINYINT(1) NOT NULL DEFAULT 1,
  seo_title_en VARCHAR(255) NOT NULL DEFAULT '',
  crumbs_json JSON NULL,
  UNIQUE KEY uq_pages_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blocks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_id INT UNSIGNED NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  type VARCHAR(50) NOT NULL,
  data JSON NOT NULL,
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
  INDEX (page_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_studies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) NOT NULL,
  title_en VARCHAR(255) NOT NULL DEFAULT '',
  title_ru VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(500) NOT NULL DEFAULT '',
  challenge_en TEXT,
  challenge_ru TEXT,
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_case_studies_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_study_solutions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_study_id INT UNSIGNED NOT NULL,
  text_en TEXT,
  text_ru TEXT,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE,
  INDEX (case_study_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) NOT NULL,
  title_en VARCHAR(255) NOT NULL DEFAULT '',
  title_ru VARCHAR(255) NOT NULL DEFAULT '',
  date_en VARCHAR(100) NOT NULL DEFAULT '',
  date_ru VARCHAR(100) NOT NULL DEFAULT '',
  image VARCHAR(500) NOT NULL DEFAULT '',
  teaser_en TEXT,
  teaser_ru TEXT,
  body_en LONGTEXT,
  body_ru LONGTEXT,
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_articles_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL DEFAULT '',
  email VARCHAR(255) NOT NULL DEFAULT '',
  phone VARCHAR(100) NOT NULL DEFAULT '',
  company VARCHAR(255) NOT NULL DEFAULT '',
  message TEXT,
  received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (id, logo, company_name, phone, email, year) VALUES (1, '/assets/img/logo.svg', 'Hazleton Pumps', '', '', '2026');

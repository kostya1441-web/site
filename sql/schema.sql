-- Схема базы данных интернет-магазина "Ваш фермер" (Новокузнецк)
-- Импортируйте этот файл в вашу MySQL базу данных перед запуском сайта.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------
-- Категории товаров
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Товары
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `old_price` DECIMAL(10,2) NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'кг',
  `image` VARCHAR(255) NULL,
  `in_stock` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `fk_products_category` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Заказы
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(32) NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(32) NOT NULL,
  `email` VARCHAR(150) NULL,
  `delivery_type` ENUM('pickup','delivery') NOT NULL DEFAULT 'delivery',
  `address` VARCHAR(500) NULL,
  `comment` TEXT NULL,
  `delivery_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `items_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(20) NOT NULL DEFAULT 'sberbank',
  `payment_status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `sber_order_id` VARCHAR(64) NULL,
  `status` ENUM('new','processing','ready','shipped','completed','cancelled') NOT NULL DEFAULT 'new',
  `archived_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_number` (`order_number`),
  KEY `idx_orders_sber` (`sber_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Позиции заказа
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'кг',
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Администраторы
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `role` VARCHAR(30) NOT NULL DEFAULT 'admin',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Пароль по умолчанию: admin / admin123
-- ОБЯЗАТЕЛЬНО смените пароль после первого входа в админку!
INSERT INTO `admin_users` (`username`, `password_hash`, `full_name`, `role`)
VALUES ('admin', '$2y$12$rJ4kWhU5llDEE5Q6H//Dn.10gU3GCFvs7sle7jyiaxm8XRSURWCOa', 'Администратор', 'admin');

-- ---------------------------------------------------------------
-- Настройки сайта (ключ-значение)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'Ваш фермер'),
('site_tagline', 'Фермерские продукты с доставкой по Новокузнецку'),
('phone', '+7 (3843) 00-00-00'),
('email', 'info@vashfermer-nvkz.ru'),
('address', 'г. Новокузнецк, ул. Кузнецкая, д. 1'),
('work_hours', 'Ежедневно с 9:00 до 20:00'),
('map_coords', '53.786589,87.155207'),
('vk_link', ''),
('instagram_link', ''),
('whatsapp_link', ''),
('about_text', '<p>«Ваш фермер» — семейное фермерское хозяйство в окрестностях Новокузнецка. Мы выращиваем и продаём натуральное мясо, свежие ягоды и другие фермерские продукты без химии и лишних добавок.</p><p>Работаем напрямую с покупателями: от нашего хозяйства — к вашему столу, с доставкой по городу и области.</p>'),
('delivery_cost', '300'),
('free_delivery_from', '3000'),
('min_order_amount', '500');

-- ---------------------------------------------------------------
-- Категории (пример)
-- ---------------------------------------------------------------
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Мясо', 'myaso', 'Свежее фермерское мясо: говядина, свинина, баранина', 1),
('Птица', 'ptitsa', 'Курица, утка, индейка домашнего выращивания', 2),
('Ягоды', 'yagody', 'Сезонные и мороженые ягоды с наших полей', 3),
('Молочные продукты', 'moloko', 'Молоко, сметана, творог, масло', 4),
('Мёд и варенье', 'med', 'Натуральный мёд и домашние заготовки', 5),
('Овощи и фрукты', 'ovoshi', 'Фермерские овощи и фрукты по сезону', 6);

-- ---------------------------------------------------------------
-- Товары (пример)
-- ---------------------------------------------------------------
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `price`, `unit`, `in_stock`, `is_featured`) VALUES
(1, 'Говядина (вырезка)', 'govyadina-vyrezka', 'Отборная говяжья вырезка собственного откорма.', 950.00, 'кг', 1, 1),
(1, 'Свинина (окорок)', 'svinina-okorok', 'Свежая свинина, окорок без кости.', 480.00, 'кг', 1, 0),
(1, 'Баранина (лопатка)', 'baranina-lopatka', 'Молодая баранина, лопаточная часть.', 750.00, 'кг', 1, 0),
(1, 'Фарш домашний', 'farsh-domashniy', 'Фарш из говядины и свинины, приготовлен на месте.', 520.00, 'кг', 1, 1),
(2, 'Курица (тушка)', 'kuritsa-tushka', 'Домашняя курица, выращена без гормонов роста.', 380.00, 'кг', 1, 1),
(2, 'Индейка (филе)', 'indeyka-file', 'Филе грудки индейки.', 690.00, 'кг', 1, 0),
(2, 'Утка (тушка)', 'utka-tushka', 'Домашняя утка.', 550.00, 'кг', 1, 0),
(3, 'Клубника', 'klubnika', 'Свежая клубника с фермерских полей (в сезон).', 450.00, 'кг', 1, 1),
(3, 'Малина', 'malina', 'Свежая малина (в сезон) / мороженая вне сезона.', 600.00, 'кг', 1, 0),
(3, 'Черника', 'chernika', 'Лесная черника.', 900.00, 'кг', 1, 0),
(3, 'Смородина чёрная', 'smorodina-chernaya', 'Чёрная смородина, мороженая.', 350.00, 'кг', 1, 0),
(4, 'Молоко фермерское', 'moloko-fermerskoe', 'Свежее коровье молоко, 3.2-4.5% жирности.', 120.00, 'л', 1, 1),
(4, 'Сметана 25%', 'smetana-25', 'Домашняя сметана.', 280.00, 'кг', 1, 0),
(4, 'Творог домашний', 'tvorog-domashniy', 'Творог из натурального молока.', 350.00, 'кг', 1, 0),
(5, 'Мёд цветочный', 'med-tsvetochnoy', 'Натуральный цветочный мёд.', 700.00, 'кг', 1, 1),
(5, 'Варенье из малины', 'varenye-malina', 'Домашнее варенье, банка 0.5 л.', 250.00, 'шт', 1, 0),
(6, 'Картофель', 'kartofel', 'Молодой картофель с фермерского поля.', 45.00, 'кг', 1, 0),
(6, 'Морковь', 'morkov', 'Свежая морковь.', 40.00, 'кг', 1, 0),
(6, 'Яблоки', 'yabloki', 'Сезонные яблоки.', 90.00, 'кг', 1, 0);

SET FOREIGN_KEY_CHECKS = 1;

-- Quartirnik Cafe-Bar Database Schema
SET NAMES utf8mb4;
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS quartirnik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quartirnik;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Menu categories
CREATE TABLE IF NOT EXISTS menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    type ENUM('food','drinks','bar') NOT NULL DEFAULT 'food',
    sort_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Menu items
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Events
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    description TEXT,
    artist VARCHAR(300),
    event_date DATE NOT NULL,
    event_time TIME,
    price DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(500),
    seats_total INT DEFAULT 0,
    seats_booked INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table bookings
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    guests INT NOT NULL DEFAULT 2,
    event_id INT DEFAULT NULL,
    comment TEXT,
    status ENUM('new','confirmed','cancelled') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    type ENUM('pickup','delivery') NOT NULL DEFAULT 'pickup',
    address TEXT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('new','preparing','ready','on_way','delivered','cancelled') DEFAULT 'new',
    payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    payment_id VARCHAR(200),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Demo admin (password: admin123)
INSERT IGNORE INTO admins (username, password, name) VALUES
('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор');

-- Demo categories
INSERT IGNORE INTO menu_categories (id, name, slug, type, sort_order) VALUES
(1, 'Закуски', 'snacks', 'food', 1),
(2, 'Горячие блюда', 'hot', 'food', 2),
(3, 'Десерты', 'desserts', 'food', 3),
(4, 'Безалкогольные напитки', 'soft', 'drinks', 4),
(5, 'Авторские коктейли', 'cocktails', 'bar', 5),
(6, 'Пиво и вино', 'beer-wine', 'bar', 6);

-- Demo menu items
INSERT IGNORE INTO menu_items (id, category_id, name, description, price, image) VALUES
(1, 1, 'Брускетта с томатами', 'Поджаренный хлеб с томатами, базиликом и оливковым маслом', 290.00, NULL),
(2, 1, 'Сырная тарелка', 'Ассорти сыров с мёдом, виноградом и орехами', 650.00, NULL),
(3, 1, 'Хумус с питой', 'Домашний хумус с тёплой питой и свежими овощами', 350.00, NULL),
(4, 2, 'Паста карбонара', 'Классическая итальянская паста с беконом и сыром пармезан', 480.00, NULL),
(5, 2, 'Стейк из говядины', 'Говяжий стейк medium rare с картофельным пюре и соусом', 890.00, NULL),
(6, 2, 'Риссото с грибами', 'Кремовое ризотто с лесными грибами и пармезаном', 520.00, NULL),
(7, 3, 'Тирамису', 'Классический итальянский тирамису', 320.00, NULL),
(8, 3, 'Чизкейк Нью-Йорк', 'Нежный чизкейк с ягодным соусом', 340.00, NULL),
(9, 4, 'Авторский лимонад', 'Свежий лимонад с мятой и имбирём', 220.00, NULL),
(10, 4, 'Кофе латте', 'Двойной эспрессо с молочной пенкой', 200.00, NULL),
(11, 4, 'Чай Masala', 'Индийский чай со специями и молоком', 180.00, NULL),
(12, 5, 'Квартирник Смэш', 'Виски, лимонный сок, мёд, тимьян', 490.00, NULL),
(13, 5, 'Вечерний Нуар', 'Ром, кофейный ликёр, сливки, ваниль', 520.00, NULL),
(14, 5, 'Апероль Шпритц', 'Апероль, просекко, содовая, апельсин', 450.00, NULL),
(15, 6, 'Крафтовое пиво', 'Местное крафтовое пиво (0.5л)', 280.00, NULL),
(16, 6, 'Вино красное (бокал)', 'Каберне Совиньон, Чили', 350.00, NULL),
(17, 6, 'Вино белое (бокал)', 'Пино Гриджо, Италия', 350.00, NULL);

-- Demo events
INSERT IGNORE INTO events (id, title, description, artist, event_date, event_time, price, seats_total) VALUES
(1, 'Джазовый квартирник', 'Тёплый вечер живого джаза в уютной атмосфере нашего бара. Приходите отдохнуть от городской суеты.', 'Трио «Ночной экспресс»', '2026-05-10', '20:00:00', 500.00, 40),
(2, 'Акустика и вино', 'Авторская музыка под гитару и бокал хорошего вина — идеальное сочетание для пятничного вечера.', 'Алексей Ромов', '2026-05-17', '19:30:00', 300.00, 35),
(3, 'Ретро-вечеринка 80-х', 'Танцуем под любимые хиты! Живая музыка, культовые треки эпохи, тематические коктейли.', 'Группа «Эпоха»', '2026-05-24', '21:00:00', 600.00, 50),
(4, 'Open Mic Night', 'Вечер открытого микрофона — любой желающий может выступить. Поддержим начинающих музыкантов!', 'Открытая сцена', '2026-06-01', '19:00:00', 0.00, 60);

SET foreign_key_checks = 1;

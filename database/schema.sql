-- Схема «Ваш фермер» для MySQL 5.7+ / MariaDB

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    login         VARCHAR(64) NOT NULL UNIQUE,
    name          VARCHAR(128) NOT NULL DEFAULT '',
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(32) NOT NULL DEFAULT 'admin',
    last_login_at DATETIME NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(191) NOT NULL,
    slug        VARCHAR(191) NOT NULL UNIQUE,
    description TEXT,
    image       VARCHAR(255) NULL,
    icon        VARCHAR(32) NOT NULL DEFAULT '',
    sort_order  INT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    category_id       INT NULL,
    name              VARCHAR(191) NOT NULL,
    slug              VARCHAR(191) NOT NULL UNIQUE,
    sku               VARCHAR(64) NOT NULL DEFAULT '',
    short_description VARCHAR(500) NOT NULL DEFAULT '',
    description       TEXT,
    price             DECIMAL(10,2) NOT NULL DEFAULT 0,
    old_price         DECIMAL(10,2) NULL,
    unit              VARCHAR(16) NOT NULL DEFAULT 'кг',
    weight            DECIMAL(10,3) NULL,
    stock             INT NOT NULL DEFAULT 0,
    image             VARCHAR(255) NULL,
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    is_featured       TINYINT(1) NOT NULL DEFAULT 0,
    sort_order        INT NOT NULL DEFAULT 0,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_products_category (category_id),
    INDEX idx_products_active (is_active),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    number         VARCHAR(32) NOT NULL UNIQUE,
    status         VARCHAR(32) NOT NULL DEFAULT 'new',
    payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(32) NOT NULL DEFAULT 'sber',
    sber_order_id  VARCHAR(64) NULL,
    sber_form_url  VARCHAR(500) NULL,
    customer_name  VARCHAR(191) NOT NULL,
    phone          VARCHAR(32) NOT NULL,
    email          VARCHAR(191) NOT NULL DEFAULT '',
    delivery_type  VARCHAR(16) NOT NULL DEFAULT 'delivery',
    address        VARCHAR(500) NOT NULL DEFAULT '',
    comment        TEXT,
    subtotal       DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total          DECIMAL(10,2) NOT NULL DEFAULT 0,
    admin_note     TEXT,
    ip             VARCHAR(64) NOT NULL DEFAULT '',
    paid_at        DATETIME NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_orders_status (status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT NULL,
    name       VARCHAR(191) NOT NULL,
    unit       VARCHAR(16) NOT NULL DEFAULT 'кг',
    price      DECIMAL(10,2) NOT NULL DEFAULT 0,
    quantity   INT NOT NULL DEFAULT 1,
    sum        DECIMAL(10,2) NOT NULL DEFAULT 0,
    INDEX idx_items_order (order_id),
    CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_history (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    status     VARCHAR(32) NOT NULL,
    comment    VARCHAR(500) NOT NULL DEFAULT '',
    author     VARCHAR(128) NOT NULL DEFAULT 'система',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_history_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    `key`   VARCHAR(64) PRIMARY KEY,
    `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payment_log (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NULL,
    event      VARCHAR(64) NOT NULL,
    payload    TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(191) NOT NULL,
    phone      VARCHAR(32) NOT NULL,
    email      VARCHAR(191) NOT NULL DEFAULT '',
    message    TEXT,
    status     VARCHAR(16) NOT NULL DEFAULT 'new',
    admin_note TEXT,
    ip         VARCHAR(64) NOT NULL DEFAULT '',
    mail_sent  TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_status (status),
    INDEX idx_messages_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

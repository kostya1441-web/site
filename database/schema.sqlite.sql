-- Схема «Ваш фермер» для SQLite

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    login         TEXT NOT NULL UNIQUE,
    name          TEXT NOT NULL DEFAULT '',
    password_hash TEXT NOT NULL,
    role          TEXT NOT NULL DEFAULT 'admin',
    last_login_at TEXT,
    created_at    TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS categories (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT NOT NULL,
    slug        TEXT NOT NULL UNIQUE,
    description TEXT NOT NULL DEFAULT '',
    image       TEXT,
    icon        TEXT NOT NULL DEFAULT '',
    sort_order  INTEGER NOT NULL DEFAULT 0,
    is_active   INTEGER NOT NULL DEFAULT 1,
    created_at  TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS products (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id       INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    name              TEXT NOT NULL,
    slug              TEXT NOT NULL UNIQUE,
    sku               TEXT NOT NULL DEFAULT '',
    short_description TEXT NOT NULL DEFAULT '',
    description       TEXT NOT NULL DEFAULT '',
    price             REAL NOT NULL DEFAULT 0,
    old_price         REAL,
    unit              TEXT NOT NULL DEFAULT 'кг',
    weight            REAL,
    stock             INTEGER NOT NULL DEFAULT 0,
    image             TEXT,
    is_active         INTEGER NOT NULL DEFAULT 1,
    is_featured       INTEGER NOT NULL DEFAULT 0,
    sort_order        INTEGER NOT NULL DEFAULT 0,
    created_at        TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at        TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_active ON products(is_active);

CREATE TABLE IF NOT EXISTS orders (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    number         TEXT NOT NULL UNIQUE,
    status         TEXT NOT NULL DEFAULT 'new',
    payment_status TEXT NOT NULL DEFAULT 'pending',
    payment_method TEXT NOT NULL DEFAULT 'sber',
    sber_order_id  TEXT,
    sber_form_url  TEXT,
    customer_name  TEXT NOT NULL,
    phone          TEXT NOT NULL,
    email          TEXT NOT NULL DEFAULT '',
    delivery_type  TEXT NOT NULL DEFAULT 'delivery',
    address        TEXT NOT NULL DEFAULT '',
    comment        TEXT NOT NULL DEFAULT '',
    subtotal       REAL NOT NULL DEFAULT 0,
    delivery_price REAL NOT NULL DEFAULT 0,
    total          REAL NOT NULL DEFAULT 0,
    admin_note     TEXT NOT NULL DEFAULT '',
    ip             TEXT NOT NULL DEFAULT '',
    paid_at        TEXT,
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_orders_created ON orders(created_at);

CREATE TABLE IF NOT EXISTS order_items (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER,
    name       TEXT NOT NULL,
    unit       TEXT NOT NULL DEFAULT 'кг',
    price      REAL NOT NULL DEFAULT 0,
    quantity   INTEGER NOT NULL DEFAULT 1,
    sum        REAL NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_items_order ON order_items(order_id);

CREATE TABLE IF NOT EXISTS order_history (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    status     TEXT NOT NULL,
    comment    TEXT NOT NULL DEFAULT '',
    author     TEXT NOT NULL DEFAULT 'система',
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS settings (
    key   TEXT PRIMARY KEY,
    value TEXT NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS payment_log (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER,
    event      TEXT NOT NULL,
    payload    TEXT NOT NULL DEFAULT '',
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

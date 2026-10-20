# Hazleton Pumps — PHP + MySQL version (phpMyAdmin-friendly)

The Hazleton Pumps site, built for classic shared PHP/MySQL hosting —
the kind that gives you phpMyAdmin and an Apache document root. Bilingual
(EN/RU), with a full admin panel, backed by a real MySQL database.

**Deploy only this folder's contents**, i.e. upload what's *inside*
`php/` so that `index.php` ends up directly in your hosting's document
root (`public_html/index.php`, not `public_html/php/index.php`) — see
`УСТАНОВКА.md` for the full walkthrough.

## What's in the database

Everything editable lives in MySQL, browsable in phpMyAdmin:

- `settings` — one row: company name, logo, phone, email, address, footer
  text, the repeating "let us help you configure a solution" block, SEO.
- `nav_items` — the header menu (self-referencing `parent_id` for dropdown
  children).
- `pages` + `blocks` — every page is a row in `pages`; its content is an
  ordered list of rows in `blocks`, each with a `type` (hero, text,
  text+image, list, cards, …) and a `data` JSON column holding that
  block's fields. This is what the admin panel's "add/remove block" and
  per-field editing works against.
- `case_studies` + `case_study_solutions` — each case study is a row,
  automatically published at `/case-studies/<slug>/`; its bullet-point
  solution list is a child table.
- `articles` — press releases/articles, each published at
  `/press-releases/<slug>/`.
- `leads` — every "Request a quote" form submission from `/contact/`.
- `admin_users` — admin login (bcrypt-style hash via PHP's
  `password_hash`, never a plain password).

## Setting it up on your hosting

1. **Create a MySQL database** from your host's control panel (cPanel,
   ISPmanager, etc.) or directly in phpMyAdmin. Note the database name,
   username and password it gives you.
2. **Import the schema and content.** In phpMyAdmin: open the database →
   *Import* tab → choose `schema.sql` → Go. Repeat for `seed.sql` (import
   it *after* `schema.sql` — it fills the tables `schema.sql` creates).
   From a terminal instead: `mysql -u USER -p DBNAME < schema.sql` then
   `mysql -u USER -p DBNAME < seed.sql`.
3. **Create your admin login.** phpMyAdmin has no built-in way to hash a
   password, so run this once from a terminal with PHP available (your
   host's SSH, or your own machine — it only needs to reach the
   database):
   ```bash
   php -r "
   \$pdo = new PDO('mysql:host=HOST;dbname=DBNAME;charset=utf8mb4', 'USER', 'PASSWORD');
   \$hash = password_hash('choose-a-real-password', PASSWORD_DEFAULT);
   \$pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)')
       ->execute(['admin', \$hash]);
   echo \"done\n\";
   "
   ```
   (Replace `HOST`/`DBNAME`/`USER`/`PASSWORD` and the password string.)
4. **Fill in `config.php`** with that same host/database/username/password.
5. **Upload this whole `php/` folder's contents** to your hosting account
   so that `index.php` and `.htaccess` sit at your domain's document root
   (e.g. `public_html/`). Make sure the `uploads/` folder is writable by
   PHP (most hosts default new folders to writable; if not, `chmod 775
   uploads`).
6. Visit your domain — the site should load — then `/admin/login.php` to
   sign in.

Steps 1–4 are the ones phpMyAdmin is for: steps 5–6 are plain file upload,
no database tool involved.

### Requirements

Any shared host with PHP 8.1+ and MySQL 5.7+/MariaDB 10.3+ (for JSON
column support) and `mod_rewrite` enabled works — this covers the
overwhelming majority of standard hosting plans. No Composer packages or
PHP extensions beyond the ones PHP ships with by default (`pdo_mysql`,
`gd`/`fileinfo` for image validation) are required.

## Running it locally to test changes

```bash
php -S localhost:8000 router.php
```

`router.php` exists only for this — PHP's built-in server doesn't read
`.htaccess`, so it does the same "serve real files as-is, everything else
through `index.php`" job by hand. Production hosting (Apache) uses
`.htaccess` instead and never touches `router.php`.

You'll need a local MySQL/MariaDB server with `schema.sql` + `seed.sql`
imported and `config.php` pointed at it, same as production.

## Admin panel

`/admin/login.php`:

- **Страницы** — edit every page's blocks (EN/RU side by side), add or
  remove blocks, create brand-new pages at any URL, delete pages.
- **Меню навигации** — edit the header menu and its dropdown items.
- **Примеры проектов** / **Пресса и статьи** — full create/edit/delete;
  each becomes its own page automatically.
- **Настройки и контакты** — company info, logo upload, the CTA block
  text, the site's three theme colors ("Внешний вид" section — picking a
  new navy/accent/background color updates the whole site immediately,
  no code involved), and the admin login itself (change username/password
  here).
- **Заявки** — contact-form submissions. There's no outgoing email
  configured (that needs SMTP credentials this project doesn't have) —
  submissions only appear here, they aren't emailed anywhere automatically.

Images anywhere in the admin have a file picker; uploads land in
`uploads/` and are served at `/uploads/<filename>`.

Every image on the public site can be clicked to open it full-size in a
lightbox overlay (click the image again, the × button, or press Escape to
close) — this needs no admin configuration, it just works.

## Regenerating `seed.sql` from scratch

`seed.sql` was generated once from the original translated EN/RU content
via a one-off migration script (not part of this folder). Re-importing
`schema.sql` + `seed.sql` at any time discards all admin edits and
restores that original translated content — use this only if you want to
start over.

## Upgrading an existing install

`schema.sql` only creates tables that don't exist yet, so if you already
imported it once (e.g. before the "Внешний вид" / appearance colors were
added), re-importing it won't add the new columns. Run this once instead:

```sql
ALTER TABLE settings
  ADD COLUMN theme_navy VARCHAR(20) NOT NULL DEFAULT '#122a3a',
  ADD COLUMN theme_amber VARCHAR(20) NOT NULL DEFAULT '#d97a28',
  ADD COLUMN theme_paper VARCHAR(20) NOT NULL DEFAULT '#f5f3ee';
```

## Security notes

- Change the default database password before using this anywhere but
  your own machine — `config.php`'s defaults are placeholders.
- Always put the live site behind HTTPS; admin login should never run
  over plain HTTP.
- The admin password you set in step 3 above is the only credential
  gating `/admin/` — pick a real one, not the example string.

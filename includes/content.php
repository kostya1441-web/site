<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

/** Значения настроек по умолчанию: подставляются, если в базе ключа ещё нет. */
function settings_defaults(): array
{
    return [
        'brandName'       => 'АДАМКО Сибирь',
        'tagline'         => 'новый вектор в горнодобывающем оборудовании',
        'logo'            => '',
        'logoShowText'    => '1',
        'logoHeight'      => '44',
        'logoPad'         => '0',
        'phone'           => '+7 (000) 000-00-00',
        'email'           => 'mail@adamko-sib.ru',
        'city'            => 'г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209',
        'badge'           => 'Официальный партнёр JA Engineering Works (ЮАР)',
        'h1a'             => 'Горное и строительное',
        'h1b'             => 'оборудование',
        'heroText'        => 'ООО «Адамко Сибирь» — действующая компания в Новокузнецке.',
        'heroCta'         => 'Связаться с нами',
        'yearsNum'        => 'С 2020 ГОДА',
        'yearsText'       => 'Компания зарегистрирована в Новокузнецке в 2020 году.',
        'yearsCta'        => 'О компании',
        'catalogEyebrow'  => 'Каталог оборудования',
        'catalogTitle'    => 'Горное и строительное оборудование',
        'catalogAllLabel' => 'Все категории',
        'catalogAllCta'   => 'Открыть весь каталог',
        'catalogPageLead' => '',
        'megaTitle'       => 'Каталог',
        'catalogCta'      => 'Уточнить наличие',
        'catalogMoreCta'  => 'Подробнее',
        'aboutEyebrow'    => 'О компании',
        'aboutTitle'      => 'ООО «Адамко Сибирь»',
        'aboutText'       => '',
        'mapAddress'      => 'г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209',
        'requisites'      => 'ИНН 4217199759 · ОГРН 1204200012389',
        'copyright'       => '© 2026 ООО «Адамко Сибирь». Все права защищены.',
    ];
}

/** Читает весь контент сайта из базы. */
function content_load(): array
{
    $pdo = db();

    $settings = settings_defaults();
    foreach ($pdo->query('SELECT skey, svalue FROM settings') as $row) {
        $settings[$row['skey']] = (string) $row['svalue'];
    }

    $categories = $pdo->query(
        'SELECT id, slug, code, name, description FROM categories ORDER BY sort_order, id'
    )->fetchAll();

    $products = $pdo->query(
        'SELECT id, category_id, slug, code, name, specs, description
           FROM products ORDER BY sort_order, id'
    )->fetchAll();

    $images = [];
    foreach ($pdo->query('SELECT product_id, path FROM product_images ORDER BY sort_order, id') as $row) {
        $images[(int) $row['product_id']][] = (string) $row['path'];
    }

    foreach ($products as &$product) {
        $product['id']          = (int) $product['id'];
        $product['category_id'] = (int) $product['category_id'];
        $product['images']      = $images[$product['id']] ?? [];
    }
    unset($product);

    foreach ($categories as &$category) {
        $category['id'] = (int) $category['id'];
    }
    unset($category);

    return [
        'settings'   => $settings,
        'categories' => $categories,
        'products'   => $products,
        'features'   => $pdo->query('SELECT id, title, text FROM features ORDER BY sort_order, id')->fetchAll(),
        'stats'      => $pdo->query('SELECT id, value, label FROM stats ORDER BY sort_order, id')->fetchAll(),
    ];
}

/** Позиции указанной категории. */
function products_of(array $content, int $categoryId): array
{
    return array_values(array_filter(
        $content['products'],
        static fn(array $p): bool => $p['category_id'] === $categoryId
    ));
}

function category_by_slug(array $content, string $slug): ?array
{
    foreach ($content['categories'] as $category) {
        if ($category['slug'] === $slug) {
            return $category;
        }
    }
    return null;
}

function product_by_slug(array $content, string $slug): ?array
{
    foreach ($content['products'] as $product) {
        if ($product['slug'] === $slug) {
            return $product;
        }
    }
    return null;
}

/** Обложка категории — первое фото из её позиций. */
function category_cover(array $content, int $categoryId): string
{
    foreach (products_of($content, $categoryId) as $product) {
        if (!empty($product['images'][0])) {
            return (string) $product['images'][0];
        }
    }
    return '';
}

/**
 * Сохраняет контент, пришедший из панели администратора.
 * Всё пишется одной транзакцией: либо применяются все правки, либо ни одной.
 */
function content_save(array $data): void
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        save_settings($pdo, $data['settings'] ?? []);
        $categoryIds = save_categories($pdo, $data['categories'] ?? []);
        save_products($pdo, $data['products'] ?? [], $categoryIds);
        save_simple_list($pdo, 'features', ['title', 'text'], $data['features'] ?? []);
        save_simple_list($pdo, 'stats', ['value', 'label'], $data['stats'] ?? []);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    cleanup_uploads();
}

function save_settings(PDO $pdo, array $settings): void
{
    $allowed = array_keys(settings_defaults());
    $stmt = $pdo->prepare(
        'INSERT INTO settings (skey, svalue) VALUES (:k, :v)
         ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)'
    );
    foreach ($settings as $key => $value) {
        if (!in_array($key, $allowed, true)) {
            continue;
        }
        $stmt->execute([':k' => $key, ':v' => is_scalar($value) ? (string) $value : '']);
    }
}

/** @return array<int|string,int> карта «идентификатор из панели» → «идентификатор в базе» */
function save_categories(PDO $pdo, array $categories): array
{
    $existing = $pdo->query('SELECT id FROM categories')->fetchAll(PDO::FETCH_COLUMN);
    $existing = array_map('intval', $existing);

    $update = $pdo->prepare(
        'UPDATE categories SET slug = :slug, code = :code, name = :name,
                description = :description, sort_order = :sort WHERE id = :id'
    );
    $insert = $pdo->prepare(
        'INSERT INTO categories (slug, code, name, description, sort_order)
         VALUES (:slug, :code, :name, :description, :sort)'
    );

    $map = [];
    $kept = [];
    $slugs = [];
    foreach (array_values($categories) as $index => $category) {
        $name = trim((string) ($category['name'] ?? ''));
        if ($name === '') {
            $name = 'Категория ' . ($index + 1);
        }
        $slug = unique_slug((string) ($category['slug'] ?? ''), $name, 'category', $slugs);
        $params = [
            ':slug'        => $slug,
            ':code'        => trim((string) ($category['code'] ?? '')),
            ':name'        => $name,
            ':description' => (string) ($category['description'] ?? ''),
            ':sort'        => $index + 1,
        ];
        $id = (int) ($category['id'] ?? 0);
        if ($id > 0 && in_array($id, $existing, true)) {
            $update->execute($params + [':id' => $id]);
        } else {
            $insert->execute($params);
            $id = (int) $pdo->lastInsertId();
        }
        $kept[] = $id;
        // Позиции новых категорий приходят с ключом вида new0, new1 — по порядку в панели.
        $map['new' . $index] = $id;
        if (!empty($category['id'])) {
            $map[(string) (int) $category['id']] = $id;
        }
    }

    delete_missing($pdo, 'categories', $existing, $kept);
    return $map;
}

function save_products(PDO $pdo, array $products, array $categoryIds): void
{
    $existing = array_map('intval', $pdo->query('SELECT id FROM products')->fetchAll(PDO::FETCH_COLUMN));
    $validCategories = array_map('intval', $pdo->query('SELECT id FROM categories')->fetchAll(PDO::FETCH_COLUMN));

    $update = $pdo->prepare(
        'UPDATE products SET category_id = :category_id, slug = :slug, code = :code, name = :name,
                specs = :specs, description = :description, sort_order = :sort WHERE id = :id'
    );
    $insert = $pdo->prepare(
        'INSERT INTO products (category_id, slug, code, name, specs, description, sort_order)
         VALUES (:category_id, :slug, :code, :name, :specs, :description, :sort)'
    );
    $deleteImages = $pdo->prepare('DELETE FROM product_images WHERE product_id = :id');
    $insertImage  = $pdo->prepare(
        'INSERT INTO product_images (product_id, path, sort_order) VALUES (:product_id, :path, :sort)'
    );

    $kept = [];
    $slugs = [];
    foreach (array_values($products) as $index => $product) {
        $categoryId = (int) ($product['category_id'] ?? 0);
        $key = (string) ($product['category_key'] ?? '');
        if ($key !== '' && isset($categoryIds[$key])) {
            $categoryId = (int) $categoryIds[$key];
        }
        if (!in_array($categoryId, $validCategories, true)) {
            $categoryId = (int) ($validCategories[0] ?? 0);
        }
        if ($categoryId === 0) {
            continue;
        }

        $name = trim((string) ($product['name'] ?? ''));
        if ($name === '') {
            $name = 'Позиция ' . ($index + 1);
        }
        $slug = unique_slug((string) ($product['slug'] ?? ''), $name, 'product', $slugs);
        $params = [
            ':category_id' => $categoryId,
            ':slug'        => $slug,
            ':code'        => trim((string) ($product['code'] ?? '')),
            ':name'        => $name,
            ':specs'       => (string) ($product['specs'] ?? ''),
            ':description' => (string) ($product['description'] ?? ''),
            ':sort'        => $index + 1,
        ];

        $id = (int) ($product['id'] ?? 0);
        if ($id > 0 && in_array($id, $existing, true)) {
            $update->execute($params + [':id' => $id]);
        } else {
            $insert->execute($params);
            $id = (int) $pdo->lastInsertId();
        }
        $kept[] = $id;

        $deleteImages->execute([':id' => $id]);
        $sort = 0;
        foreach ((array) ($product['images'] ?? []) as $path) {
            $path = trim((string) $path);
            if ($path === '' || mb_strlen($path) > 500) {
                continue;
            }
            $insertImage->execute([':product_id' => $id, ':path' => $path, ':sort' => ++$sort]);
        }
    }

    delete_missing($pdo, 'products', $existing, $kept);
}

function save_simple_list(PDO $pdo, string $table, array $columns, array $rows): void
{
    $existing = array_map('intval', $pdo->query("SELECT id FROM {$table}")->fetchAll(PDO::FETCH_COLUMN));

    $set = implode(', ', array_map(static fn(string $c): string => "{$c} = :{$c}", $columns));
    $update = $pdo->prepare("UPDATE {$table} SET {$set}, sort_order = :sort WHERE id = :id");
    $insert = $pdo->prepare(sprintf(
        'INSERT INTO %s (%s, sort_order) VALUES (%s, :sort)',
        $table,
        implode(', ', $columns),
        implode(', ', array_map(static fn(string $c): string => ":{$c}", $columns))
    ));

    $kept = [];
    foreach (array_values($rows) as $index => $row) {
        $params = [':sort' => $index + 1];
        foreach ($columns as $column) {
            $params[':' . $column] = (string) ($row[$column] ?? '');
        }
        $id = (int) ($row['id'] ?? 0);
        if ($id > 0 && in_array($id, $existing, true)) {
            $update->execute($params + [':id' => $id]);
        } else {
            $insert->execute($params);
            $id = (int) $pdo->lastInsertId();
        }
        $kept[] = $id;
    }

    delete_missing($pdo, $table, $existing, $kept);
}

function delete_missing(PDO $pdo, string $table, array $existing, array $kept): void
{
    $remove = array_diff($existing, $kept);
    if (!$remove) {
        return;
    }
    $in = implode(',', array_fill(0, count($remove), '?'));
    $pdo->prepare("DELETE FROM {$table} WHERE id IN ({$in})")->execute(array_values($remove));
}

function unique_slug(string $slug, string $name, string $fallback, array &$taken): string
{
    $slug = $slug !== '' ? slugify($slug, $fallback) : slugify($name, $fallback);
    $base = $slug;
    $n = 2;
    while (isset($taken[$slug])) {
        $slug = $base . '-' . $n++;
    }
    $taken[$slug] = true;
    return $slug;
}

/** Удаляет из папки uploads файлы, на которые больше никто не ссылается. */
function cleanup_uploads(): void
{
    $dir = config('uploads_dir');
    if (!is_dir($dir)) {
        return;
    }
    $pdo = db();
    $used = [];
    foreach ($pdo->query('SELECT path FROM product_images') as $row) {
        $used[basename((string) $row['path'])] = true;
    }
    $logo = $pdo->query("SELECT svalue FROM settings WHERE skey = 'logo'")->fetchColumn();
    if ($logo) {
        $used[basename((string) $logo)] = true;
    }

    foreach (glob($dir . '/*') ?: [] as $file) {
        $name = basename($file);
        if ($name === '.htaccess' || $name === 'index.html' || is_dir($file)) {
            continue;
        }
        if (!isset($used[$name]) && filemtime($file) < time() - 3600) {
            @unlink($file);
        }
    }
}

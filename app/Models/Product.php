<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    public const UNITS = ['кг', 'г', 'шт', 'уп', 'банка', 'л'];

    /**
     * Витрина каталога с фильтрами.
     * @param array{category?:int|null,search?:string,sort?:string,min?:float,max?:float,only_stock?:bool} $filters
     */
    public static function paginate(array $filters, int $page, int $perPage): array
    {
        [$where, $params] = self::buildWhere($filters, true);

        $total = (int) Database::instance()->value(
            'SELECT COUNT(*) FROM products p WHERE ' . $where,
            $params
        );

        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = Database::instance()->all(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE ' . $where . '
             ORDER BY ' . self::orderBy($filters['sort'] ?? 'popular') . '
             LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    /** Список для админки — без ограничения по is_active. */
    public static function adminList(array $filters, int $page, int $perPage): array
    {
        [$where, $params] = self::buildWhere($filters, false);

        $total  = (int) Database::instance()->value('SELECT COUNT(*) FROM products p WHERE ' . $where, $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = Database::instance()->all(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE ' . $where . '
             ORDER BY p.id DESC
             LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    private static function buildWhere(array $filters, bool $onlyActive): array
    {
        $where  = $onlyActive ? ['p.is_active = 1'] : ['1 = 1'];
        $params = [];

        if (!empty($filters['category'])) {
            $where[]  = 'p.category_id = ?';
            $params[] = (int) $filters['category'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(LOWER(p.name) LIKE ? OR LOWER(p.short_description) LIKE ? OR LOWER(p.sku) LIKE ?)';
            $needle   = '%' . mb_strtolower(trim($filters['search'])) . '%';
            $params[] = $needle;
            $params[] = $needle;
            $params[] = $needle;
        }
        if (!empty($filters['min'])) {
            $where[]  = 'p.price >= ?';
            $params[] = (float) $filters['min'];
        }
        if (!empty($filters['max'])) {
            $where[]  = 'p.price <= ?';
            $params[] = (float) $filters['max'];
        }
        if (!empty($filters['only_stock'])) {
            $where[] = 'p.stock > 0';
        }

        return [implode(' AND ', $where), $params];
    }

    private static function orderBy(string $sort): string
    {
        return match ($sort) {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name'       => 'p.name ASC',
            'new'        => 'p.id DESC',
            default      => 'p.is_featured DESC, p.sort_order ASC, p.id DESC',
        };
    }

    public static function find(int $id): ?array
    {
        return Database::instance()->first(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ?',
            [$id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::instance()->first(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.is_active = 1',
            [$slug]
        );
    }

    /** Товары для корзины: берём актуальные цены из БД, а не из сессии. */
    public static function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }
        $in   = implode(',', array_fill(0, count($ids), '?'));
        $rows = Database::instance()->all(
            "SELECT * FROM products WHERE id IN ($in) AND is_active = 1",
            $ids
        );
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['id']] = $row;
        }
        return $byId;
    }

    public static function featured(int $limit = 8): array
    {
        return Database::instance()->all(
            'SELECT p.*, c.slug AS category_slug FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.is_featured = 1
             ORDER BY p.sort_order, p.id DESC LIMIT ' . (int) $limit
        );
    }

    public static function latest(int $limit = 8): array
    {
        return Database::instance()->all(
            'SELECT p.*, c.slug AS category_slug FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 ORDER BY p.id DESC LIMIT ' . (int) $limit
        );
    }

    public static function related(array $product, int $limit = 4): array
    {
        return Database::instance()->all(
            'SELECT p.*, c.slug AS category_slug FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.id <> ? AND (p.category_id = ? OR ? IS NULL)
             ORDER BY p.is_featured DESC, p.id DESC LIMIT ' . (int) $limit,
            [(int) $product['id'], $product['category_id'], $product['category_id']]
        );
    }

    public static function create(array $data): int
    {
        $data['slug']       = self::uniqueSlug($data['slug'] ?: $data['name']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::instance()->insert('products', $data);
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['slug'])) {
            $data['slug'] = self::uniqueSlug($data['slug'] ?: ($data['name'] ?? 'tovar'), $id);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::instance()->update('products', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::instance()->delete('products', 'id = :id', ['id' => $id]);
    }

    public static function toggle(int $id, string $field): int
    {
        if (!in_array($field, ['is_active', 'is_featured'], true)) {
            throw new \InvalidArgumentException('Недопустимое поле');
        }
        $db      = Database::instance();
        $current = (int) $db->value("SELECT {$field} FROM products WHERE id = ?", [$id]);
        $new     = $current ? 0 : 1;
        $db->run("UPDATE products SET {$field} = ? WHERE id = ?", [$new, $id]);
        return $new;
    }

    /** Списание остатков после оплаты заказа. */
    public static function decreaseStock(int $id, int $quantity): void
    {
        Database::instance()->run(
            'UPDATE products SET stock = CASE WHEN stock >= ? THEN stock - ? ELSE 0 END WHERE id = ?',
            [$quantity, $quantity, $id]
        );
    }

    public static function uniqueSlug(string $source, ?int $exceptId = null): string
    {
        $base = slugify($source) ?: 'tovar';
        $slug = $base;
        $i    = 2;
        while (self::slugTaken($slug, $exceptId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private static function slugTaken(string $slug, ?int $exceptId): bool
    {
        $sql    = 'SELECT COUNT(*) FROM products WHERE slug = ?';
        $params = [$slug];
        if ($exceptId) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        return (int) Database::instance()->value($sql, $params) > 0;
    }

    public static function lowStock(int $threshold = 5, int $limit = 10): array
    {
        return Database::instance()->all(
            'SELECT * FROM products WHERE is_active = 1 AND stock <= ? ORDER BY stock ASC LIMIT ' . (int) $limit,
            [$threshold]
        );
    }

    public static function countAll(): int
    {
        return (int) Database::instance()->value('SELECT COUNT(*) FROM products');
    }
}

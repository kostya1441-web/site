<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    /** Активные категории с количеством товаров — для меню и главной. */
    public static function active(): array
    {
        return Database::instance()->all(
            'SELECT c.*, (
                 SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1
             ) AS products_count
             FROM categories c
             WHERE c.is_active = 1
             ORDER BY c.sort_order, c.name'
        );
    }

    public static function all(): array
    {
        return Database::instance()->all(
            'SELECT c.*, (
                 SELECT COUNT(*) FROM products p WHERE p.category_id = c.id
             ) AS products_count
             FROM categories c ORDER BY c.sort_order, c.name'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::instance()->first('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::instance()->first('SELECT * FROM categories WHERE slug = ? AND is_active = 1', [$slug]);
    }

    public static function create(array $data): int
    {
        $data['slug'] = self::uniqueSlug($data['slug'] ?: $data['name']);
        return Database::instance()->insert('categories', $data);
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['slug'])) {
            $data['slug'] = self::uniqueSlug($data['slug'] ?: $data['name'], $id);
        }
        Database::instance()->update('categories', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        $db = Database::instance();
        $db->run('UPDATE products SET category_id = NULL WHERE category_id = ?', [$id]);
        $db->delete('categories', 'id = :id', ['id' => $id]);
    }

    public static function uniqueSlug(string $source, ?int $exceptId = null): string
    {
        $base = slugify($source) ?: 'category';
        $slug = $base;
        $i    = 2;
        while (self::slugTaken($slug, $exceptId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private static function slugTaken(string $slug, ?int $exceptId): bool
    {
        $sql    = 'SELECT COUNT(*) FROM categories WHERE slug = ?';
        $params = [$slug];
        if ($exceptId) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        return (int) Database::instance()->value($sql, $params) > 0;
    }
}

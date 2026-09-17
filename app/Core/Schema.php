<?php

namespace App\Core;

/**
 * Сверяет структуру базы с database/schema.sql.
 * Нужно, чтобы уже установленный магазин мог доехать до новых таблиц
 * без консоли: админ видит предупреждение и жмёт «Обновить базу».
 */
class Schema
{
    /** Имена таблиц, описанных в schema.sql. */
    public static function declaredTables(): array
    {
        $sql = (string) @file_get_contents(APP_ROOT . '/database/schema.sql');
        preg_match_all('/CREATE TABLE IF NOT EXISTS\s+`?(\w+)`?/i', $sql, $matches);
        return $matches[1] ?? [];
    }

    /** Каких таблиц не хватает в базе. */
    public static function missingTables(): array
    {
        try {
            $db = Database::instance();
        } catch (\Throwable $e) {
            return [];
        }

        $missing = [];
        foreach (self::declaredTables() as $table) {
            if (!$db->tableExists($table)) {
                $missing[] = $table;
            }
        }
        return $missing;
    }

    /** Создаёт недостающие таблицы. Существующие не трогает — в схеме IF NOT EXISTS. */
    public static function apply(): array
    {
        $db      = Database::instance();
        $before  = self::missingTables();
        $sql     = (string) file_get_contents(APP_ROOT . '/database/schema.sql');

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if (str_starts_with($statement, '--') && !str_contains($statement, 'CREATE')) {
                continue;
            }
            $db->run($statement);
        }

        return $before;
    }
}

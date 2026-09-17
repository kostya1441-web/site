<?php

namespace App\Models;

use App\Core\Database;

/** Обращения с формы «Задать вопрос» на странице контактов. */
class Message
{
    public const STATUSES = [
        'new'       => 'Новое',
        'processed' => 'Обработано',
    ];

    public static function create(array $data): int
    {
        return Database::instance()->insert('messages', [
            'name'      => $data['name'],
            'phone'     => $data['phone'],
            'email'     => $data['email'] ?? '',
            'message'   => $data['message'],
            'status'    => 'new',
            'ip'        => $data['ip'] ?? '',
            'mail_sent' => !empty($data['mail_sent']) ? 1 : 0,
        ]);
    }

    public static function find(int $id): ?array
    {
        return Database::instance()->first('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function paginate(array $filters, int $page, int $perPage = 20): array
    {
        $where  = ['1 = 1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $needle   = '%' . mb_strtolower(trim($filters['search'])) . '%';
            $where[]  = '(LOWER(name) LIKE ? OR phone LIKE ? OR LOWER(message) LIKE ?)';
            $params[] = $needle;
            $params[] = $needle;
            $params[] = $needle;
        }

        $whereSql = implode(' AND ', $where);
        $total    = (int) Database::instance()->value('SELECT COUNT(*) FROM messages WHERE ' . $whereSql, $params);
        $pages    = max(1, (int) ceil($total / $perPage));
        $page     = min(max(1, $page), $pages);
        $offset   = ($page - 1) * $perPage;

        $items = Database::instance()->all(
            'SELECT * FROM messages WHERE ' . $whereSql . ' ORDER BY id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public static function setStatus(int $id, string $status): void
    {
        if (!isset(self::STATUSES[$status])) {
            throw new \InvalidArgumentException('Неизвестный статус обращения');
        }
        Database::instance()->update('messages', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    public static function saveNote(int $id, string $note): void
    {
        Database::instance()->update('messages', ['admin_note' => $note], 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::instance()->delete('messages', 'id = :id', ['id' => $id]);
    }

    public static function countNew(): int
    {
        try {
            return (int) Database::instance()->value("SELECT COUNT(*) FROM messages WHERE status = 'new'");
        } catch (\Throwable $e) {
            return 0; // таблицы ещё нет — магазин обновляется
        }
    }

    public static function recent(int $limit = 5): array
    {
        return Database::instance()->all('SELECT * FROM messages ORDER BY id DESC LIMIT ' . (int) $limit);
    }
}

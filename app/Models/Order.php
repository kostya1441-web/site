<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    public const STATUSES = [
        'new'        => 'Новый',
        'confirmed'  => 'Подтверждён',
        'assembling' => 'Собирается',
        'delivering' => 'В доставке',
        'completed'  => 'Выполнен',
        'canceled'   => 'Отменён',
    ];

    public const PAYMENT_STATUSES = [
        'pending'  => 'Ожидает оплаты',
        'paid'     => 'Оплачен',
        'failed'   => 'Ошибка оплаты',
        'refunded' => 'Возврат',
    ];

    public const PAYMENT_METHODS = [
        'sber' => 'Онлайн-оплата (Сбербанк)',
        'cash' => 'Наличными или картой при получении',
    ];

    /** Создаёт заказ вместе с позициями в одной транзакции. */
    public static function create(array $order, array $items): array
    {
        return Database::instance()->transaction(static function (Database $db) use ($order, $items) {
            $order['number']     = 'temp-' . bin2hex(random_bytes(6));
            $order['created_at'] = date('Y-m-d H:i:s');
            $order['updated_at'] = date('Y-m-d H:i:s');

            $id     = $db->insert('orders', $order);
            $number = sprintf('VF-%s-%04d', date('ymd'), $id);
            $db->update('orders', ['number' => $number], 'id = :id', ['id' => $id]);

            foreach ($items as $item) {
                $db->insert('order_items', [
                    'order_id'   => $id,
                    'product_id' => $item['product_id'],
                    'name'       => $item['name'],
                    'unit'       => $item['unit'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'sum'        => $item['sum'],
                ]);
            }

            $db->insert('order_history', [
                'order_id' => $id,
                'status'   => $order['status'] ?? 'new',
                'comment'  => 'Заказ оформлен на сайте',
                'author'   => 'клиент',
            ]);

            return ['id' => $id, 'number' => $number];
        });
    }

    public static function find(int $id): ?array
    {
        return Database::instance()->first('SELECT * FROM orders WHERE id = ?', [$id]);
    }

    public static function findByNumber(string $number): ?array
    {
        return Database::instance()->first('SELECT * FROM orders WHERE number = ?', [$number]);
    }

    public static function items(int $orderId): array
    {
        return Database::instance()->all('SELECT * FROM order_items WHERE order_id = ? ORDER BY id', [$orderId]);
    }

    public static function history(int $orderId): array
    {
        return Database::instance()->all('SELECT * FROM order_history WHERE order_id = ? ORDER BY id DESC', [$orderId]);
    }

    public static function paginate(array $filters, int $page, int $perPage = 20): array
    {
        $where  = ['1 = 1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['payment_status'])) {
            $where[]  = 'payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        if (!empty($filters['search'])) {
            $needle   = '%' . mb_strtolower(trim($filters['search'])) . '%';
            $where[]  = '(LOWER(number) LIKE ? OR LOWER(customer_name) LIKE ? OR phone LIKE ?)';
            $params[] = $needle;
            $params[] = $needle;
            $params[] = $needle;
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $whereSql = implode(' AND ', $where);
        $total    = (int) Database::instance()->value('SELECT COUNT(*) FROM orders WHERE ' . $whereSql, $params);
        $pages    = max(1, (int) ceil($total / $perPage));
        $page     = min(max(1, $page), $pages);
        $offset   = ($page - 1) * $perPage;

        $items = Database::instance()->all(
            'SELECT o.*, (SELECT COUNT(*) FROM order_items i WHERE i.order_id = o.id) AS items_count
             FROM orders o WHERE ' . $whereSql . '
             ORDER BY o.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public static function setStatus(int $id, string $status, string $comment = '', string $author = 'администратор'): void
    {
        if (!isset(self::STATUSES[$status])) {
            throw new \InvalidArgumentException('Неизвестный статус заказа');
        }
        $db = Database::instance();
        $db->update('orders', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
        $db->insert('order_history', [
            'order_id' => $id,
            'status'   => $status,
            'comment'  => $comment,
            'author'   => $author,
        ]);
    }

    public static function setPayment(int $id, string $paymentStatus, array $extra = []): void
    {
        $data = array_merge([
            'payment_status' => $paymentStatus,
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $extra);

        if ($paymentStatus === 'paid' && !isset($data['paid_at'])) {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }

        Database::instance()->update('orders', $data, 'id = :id', ['id' => $id]);
    }

    public static function saveNote(int $id, string $note): void
    {
        Database::instance()->update('orders', ['admin_note' => $note], 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        $db = Database::instance();
        $db->delete('order_items', 'order_id = :id', ['id' => $id]);
        $db->delete('order_history', 'order_id = :id', ['id' => $id]);
        $db->delete('orders', 'id = :id', ['id' => $id]);
    }

    /** Показатели для дашборда админки. */
    public static function stats(): array
    {
        $db    = Database::instance();
        $today = date('Y-m-d');
        $month = date('Y-m-01');

        return [
            'total'         => (int) $db->value('SELECT COUNT(*) FROM orders'),
            'new'           => (int) $db->value("SELECT COUNT(*) FROM orders WHERE status = 'new'"),
            'today'         => (int) $db->value('SELECT COUNT(*) FROM orders WHERE created_at >= ?', [$today . ' 00:00:00']),
            'today_revenue' => (float) $db->value(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid' AND created_at >= ?",
                [$today . ' 00:00:00']
            ),
            'month_revenue' => (float) $db->value(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid' AND created_at >= ?",
                [$month . ' 00:00:00']
            ),
            'unpaid'        => (int) $db->value("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending' AND status <> 'canceled'"),
            'avg_check'     => (float) $db->value("SELECT COALESCE(AVG(total), 0) FROM orders WHERE payment_status = 'paid'"),
        ];
    }

    /** Выручка по дням за N дней — для графика на дашборде. */
    public static function revenueByDay(int $days = 14): array
    {
        $rows = Database::instance()->all(
            "SELECT substr(created_at, 1, 10) AS day, COUNT(*) AS orders_count, COALESCE(SUM(total), 0) AS revenue
             FROM orders
             WHERE created_at >= ? AND status <> 'canceled'
             GROUP BY substr(created_at, 1, 10)",
            [date('Y-m-d', strtotime("-{$days} days")) . ' 00:00:00']
        );

        $byDay = [];
        foreach ($rows as $row) {
            $byDay[$row['day']] = $row;
        }

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day      = date('Y-m-d', strtotime("-{$i} days"));
            $result[] = [
                'day'     => $day,
                'label'   => date('d.m', strtotime($day)),
                'orders'  => (int) ($byDay[$day]['orders_count'] ?? 0),
                'revenue' => (float) ($byDay[$day]['revenue'] ?? 0),
            ];
        }
        return $result;
    }

    public static function recent(int $limit = 8): array
    {
        return Database::instance()->all('SELECT * FROM orders ORDER BY id DESC LIMIT ' . (int) $limit);
    }

    public static function topProducts(int $limit = 5): array
    {
        return Database::instance()->all(
            "SELECT i.name, SUM(i.quantity) AS qty, SUM(i.sum) AS revenue
             FROM order_items i
             JOIN orders o ON o.id = i.order_id
             WHERE o.status <> 'canceled'
             GROUP BY i.name
             ORDER BY qty DESC
             LIMIT " . (int) $limit
        );
    }
}

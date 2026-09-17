<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Order;
use App\Services\SberPayment;

class OrderController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status'         => $this->request->string('status'),
            'payment_status' => $this->request->string('payment_status'),
            'search'         => $this->request->string('q'),
            'date_from'      => $this->request->string('date_from'),
            'date_to'        => $this->request->string('date_to'),
        ];

        $result = Order::paginate($filters, max(1, $this->request->int('page', 1)), 20);

        $this->view('admin/orders/index', [
            'title'      => 'Заказы',
            'orders'     => $result['items'],
            'pagination' => $result,
            'filters'    => $filters,
            'stats'      => Order::stats(),
        ], 'admin');
    }

    public function show(string $id): void
    {
        $order = Order::find((int) $id);
        if (!$order) {
            $this->abort(404, 'Заказ не найден');
            return;
        }

        $this->view('admin/orders/show', [
            'title'   => 'Заказ ' . $order['number'],
            'order'   => $order,
            'items'   => Order::items((int) $order['id']),
            'history' => Order::history((int) $order['id']),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $order = Order::find((int) $id);
        if (!$order) {
            $this->abort(404, 'Заказ не найден');
            return;
        }

        $status = $this->request->string('status');
        if (!isset(Order::STATUSES[$status])) {
            Session::flash('error', 'Неизвестный статус');
            $this->redirect('/admin/orders/' . $id);
            return;
        }

        $author = Auth::user()['name'] ?? 'администратор';
        Order::setStatus((int) $id, $status, $this->request->string('comment'), $author);
        Session::flash('success', 'Статус заказа обновлён: ' . Order::STATUSES[$status]);
        $this->redirect('/admin/orders/' . $id);
    }

    public function updatePayment(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $status = $this->request->string('payment_status');
        if (!isset(Order::PAYMENT_STATUSES[$status])) {
            Session::flash('error', 'Неизвестный статус оплаты');
            $this->redirect('/admin/orders/' . $id);
            return;
        }

        Order::setPayment((int) $id, $status);
        Session::flash('success', 'Статус оплаты обновлён');
        $this->redirect('/admin/orders/' . $id);
    }

    public function saveNote(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Order::saveNote((int) $id, $this->request->string('admin_note'));
        Session::flash('success', 'Заметка сохранена');
        $this->redirect('/admin/orders/' . $id);
    }

    /** Ручная сверка статуса платежа со шлюзом. */
    public function checkPayment(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $order = Order::find((int) $id);
        if (!$order) {
            $this->abort(404, 'Заказ не найден');
            return;
        }

        $status = SberPayment::status($order);
        if (!$status['ok']) {
            Session::flash('error', $status['error'] ?? 'Не удалось получить статус');
            $this->redirect('/admin/orders/' . $id);
            return;
        }

        if ($status['paid'] && $order['payment_status'] !== 'paid') {
            Order::setPayment((int) $id, 'paid');
        } elseif ($status['refunded']) {
            Order::setPayment((int) $id, 'refunded');
        } elseif ($status['declined']) {
            Order::setPayment((int) $id, 'failed');
        }

        Session::flash('success', 'Сбербанк: ' . $status['message']);
        $this->redirect('/admin/orders/' . $id);
    }

    public function refund(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $order = Order::find((int) $id);
        if (!$order) {
            $this->abort(404, 'Заказ не найден');
            return;
        }

        $result = SberPayment::refund($order);
        if (!$result['ok']) {
            Session::flash('error', 'Возврат не выполнен: ' . $result['error']);
        } else {
            Order::setPayment((int) $id, 'refunded');
            Order::setStatus((int) $id, 'canceled', 'Возврат средств', Auth::user()['name'] ?? 'администратор');
            Session::flash('success', 'Возврат оформлен');
        }
        $this->redirect('/admin/orders/' . $id);
    }

    public function destroy(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Order::delete((int) $id);
        Session::flash('success', 'Заказ удалён');
        $this->redirect('/admin/orders');
    }

    /** Выгрузка заказов в CSV для бухгалтерии. */
    public function export(): void
    {
        $filters = [
            'status'         => $this->request->string('status'),
            'payment_status' => $this->request->string('payment_status'),
            'search'         => $this->request->string('q'),
            'date_from'      => $this->request->string('date_from'),
            'date_to'        => $this->request->string('date_to'),
        ];
        $result = Order::paginate($filters, 1, 5000);

        header('Content-Type: text/csv; charset=windows-1251');
        header('Content-Disposition: attachment; filename="orders-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'wb');
        $row = static function (array $cells) use ($out): void {
            $converted = array_map(
                static fn ($cell) => mb_convert_encoding((string) $cell, 'Windows-1251', 'UTF-8'),
                $cells
            );
            fputcsv($out, $converted, ';');
        };

        $row(['Номер', 'Дата', 'Клиент', 'Телефон', 'Email', 'Получение', 'Адрес', 'Статус', 'Оплата', 'Товары', 'Доставка', 'Итого']);

        foreach ($result['items'] as $order) {
            $row([
                $order['number'],
                $order['created_at'],
                $order['customer_name'],
                $order['phone'],
                $order['email'],
                $order['delivery_type'] === 'pickup' ? 'самовывоз' : 'доставка',
                $order['address'],
                Order::STATUSES[$order['status']] ?? $order['status'],
                Order::PAYMENT_STATUSES[$order['payment_status']] ?? $order['payment_status'],
                number_format((float) $order['subtotal'], 2, ',', ''),
                number_format((float) $order['delivery_price'], 2, ',', ''),
                number_format((float) $order['total'], 2, ',', ''),
            ]);
        }
        fclose($out);
    }
}

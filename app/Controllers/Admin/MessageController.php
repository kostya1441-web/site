<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Message;

class MessageController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => $this->request->string('status'),
            'search' => $this->request->string('q'),
        ];
        $result = Message::paginate($filters, max(1, $this->request->int('page', 1)), 20);

        $this->view('admin/messages/index', [
            'title'      => 'Обращения',
            'messages'   => $result['items'],
            'pagination' => $result,
            'filters'    => $filters,
            'newCount'   => Message::countNew(),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        $status = $this->request->string('status');
        if (!isset(Message::STATUSES[$status])) {
            Session::flash('error', 'Неизвестный статус');
            $this->redirect('/admin/messages');
            return;
        }

        Message::setStatus((int) $id, $status);
        Session::flash('success', 'Обращение отмечено: ' . Message::STATUSES[$status]);
        $this->back('/admin/messages');
    }

    public function saveNote(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Message::saveNote((int) $id, $this->request->string('admin_note'));
        Session::flash('success', 'Заметка сохранена');
        $this->back('/admin/messages');
    }

    public function destroy(string $id): void
    {
        if (!$this->requireCsrf()) {
            return;
        }
        Message::delete((int) $id);
        Session::flash('success', 'Обращение удалено');
        $this->redirect('/admin/messages');
    }
}

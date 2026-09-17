<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Session;

class MaintenanceController extends Controller
{
    /** Создаёт таблицы, появившиеся в новых версиях магазина. */
    public function updateDatabase(): void
    {
        if (!$this->requireCsrf()) {
            return;
        }

        try {
            $created = Schema::apply();
        } catch (\Throwable $e) {
            Session::flash('error', 'Не удалось обновить базу: ' . $e->getMessage());
            $this->back('/admin');
            return;
        }

        Session::flash(
            'success',
            $created === []
                ? 'База уже в актуальном состоянии'
                : 'База обновлена, добавлены таблицы: ' . implode(', ', $created)
        );
        $this->back('/admin');
    }
}

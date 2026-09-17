<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function find(int $id): ?array
    {
        return Database::instance()->first('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByLogin(string $login): ?array
    {
        return Database::instance()->first('SELECT * FROM users WHERE login = ?', [$login]);
    }

    public static function all(): array
    {
        return Database::instance()->all('SELECT * FROM users ORDER BY id');
    }

    public static function create(string $login, string $password, string $name = '', string $role = 'admin'): int
    {
        return Database::instance()->insert('users', [
            'login'         => $login,
            'name'          => $name !== '' ? $name : $login,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
        ]);
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::instance()->update('users', [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ], 'id = :id', ['id' => $id]);
    }

    public static function touchLogin(int $id): void
    {
        Database::instance()->update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);
    }
}

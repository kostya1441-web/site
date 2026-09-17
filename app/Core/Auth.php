<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private const KEY = '_admin_id';

    public static function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            User::updatePassword((int) $user['id'], $password);
        }

        Session::regenerate();
        Session::set(self::KEY, (int) $user['id']);
        User::touchLogin((int) $user['id']);
        return true;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        $id = Session::get(self::KEY);
        if (!$id) {
            return null;
        }
        static $cached = null;
        if ($cached === null || (int) $cached['id'] !== (int) $id) {
            $cached = User::find((int) $id);
        }
        return $cached;
    }

    public static function logout(): void
    {
        Session::forget(self::KEY);
        Session::regenerate();
    }

    /** Ограничение попыток входа: 5 неудач — пауза 10 минут. */
    public static function throttled(): bool
    {
        $attempts = Session::get('_login_attempts', 0);
        $until    = Session::get('_login_locked_until', 0);
        return $attempts >= 5 && $until > time();
    }

    public static function registerFailure(): void
    {
        $attempts = (int) Session::get('_login_attempts', 0) + 1;
        Session::set('_login_attempts', $attempts);
        if ($attempts >= 5) {
            Session::set('_login_locked_until', time() + 600);
        }
    }

    public static function clearFailures(): void
    {
        Session::forget('_login_attempts');
        Session::forget('_login_locked_until');
    }
}

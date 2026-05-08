<?php
function isLoggedIn(): bool {
    return !empty($_SESSION['user']);
}

function getCurrentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool {
    $u = getCurrentUser();
    return $u && in_array($u['role'], ['admin', 'superadmin']);
}

function isSuperAdmin(): bool {
    $u = getCurrentUser();
    return $u && $u['role'] === 'superadmin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /auth/steam.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die('Access denied.');
    }
}

function syncUser(string $steamid64, string $name, string $avatar): array {
    $db = getDB();
    if (!$db) return ['steamid64' => $steamid64, 'name' => $name, 'avatar' => $avatar, 'role' => 'user'];

    $stmt = $db->prepare('SELECT * FROM users WHERE steamid64 = ?');
    $stmt->execute([$steamid64]);
    $user = $stmt->fetch();

    $isSuperadminSteam = defined('SUPERADMIN_STEAMID64') && SUPERADMIN_STEAMID64 && $steamid64 === SUPERADMIN_STEAMID64;

    if ($user) {
        $role = $isSuperadminSteam ? 'superadmin' : $user['role'];
        $db->prepare('UPDATE users SET name=?, avatar=?, last_login=NOW(), role=? WHERE steamid64=?')
           ->execute([$name, $avatar, $role, $steamid64]);
        $user['name']   = $name;
        $user['avatar'] = $avatar;
        $user['role']   = $role;
    } else {
        $role = $isSuperadminSteam ? 'superadmin' : 'user';
        $db->prepare('INSERT INTO users (steamid64, name, avatar, role, last_login) VALUES (?,?,?,?,NOW())')
           ->execute([$steamid64, $name, $avatar, $role]);
        $user = ['steamid64' => $steamid64, 'name' => $name, 'avatar' => $avatar, 'role' => $role];
    }
    return $user;
}

function getUserBanStatus(string $steamid): ?array {
    $db = getDB();
    if (!$db) return null;
    $stmt = $db->prepare('SELECT * FROM bans WHERE steamid=? AND active=1 AND (duration=0 OR expires_at > NOW()) ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([$steamid]);
    return $stmt->fetch() ?: null;
}

function getUserMuteStatus(string $steamid): ?array {
    $db = getDB();
    if (!$db) return null;
    $stmt = $db->prepare('SELECT * FROM mutes WHERE steamid=? AND active=1 AND (duration=0 OR expires_at > NOW()) ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([$steamid]);
    return $stmt->fetch() ?: null;
}

function formatDuration(int $minutes): string {
    if ($minutes === 0) return 'Навсегда';
    if ($minutes < 60)  return "{$minutes} мин.";
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    if ($h < 24) return $m ? "{$h}ч {$m}м" : "{$h}ч";
    $d = floor($h / 24);
    $hr = $h % 24;
    return $hr ? "{$d}д {$hr}ч" : "{$d}д";
}

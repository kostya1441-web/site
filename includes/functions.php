<?php
function steamid64_to_steamid(string $id64): string {
    $id64 = (int)$id64 - 76561197960265728;
    $auth = $id64 % 2;
    $server = ($id64 - $auth) / 2;
    return "STEAM_0:{$auth}:{$server}";
}

function steamid_to_steamid64(string $steamid): string {
    if (preg_match('/^STEAM_\d+:(\d+):(\d+)$/', $steamid, $m)) {
        return (string)(76561197960265728 + (int)$m[2] * 2 + (int)$m[1]);
    }
    return '';
}

function get_steam_avatar(string $steamid64): string {
    if (!STEAM_API_KEY || !$steamid64) return '/assets/img/default_avatar.png';
    $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=" . STEAM_API_KEY . "&steamids=" . $steamid64;
    $cache_file = sys_get_temp_dir() . '/avatar_' . $steamid64 . '.json';
    if (file_exists($cache_file) && filemtime($cache_file) > time() - 3600) {
        $data = json_decode(file_get_contents($cache_file), true);
    } else {
        $data = @json_decode(@file_get_contents($url), true);
        if ($data) file_put_contents($cache_file, json_encode($data));
    }
    return $data['response']['players'][0]['avatarmedium'] ?? '/assets/img/default_avatar.png';
}

function query_server(string $ip, int $port, int $timeout = 2): ?array {
    $socket = @fsockopen('udp://' . $ip, $port, $errno, $errstr, $timeout);
    if (!$socket) return null;
    stream_set_timeout($socket, $timeout);
    // A2S_INFO запрос
    $request = "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00";
    fwrite($socket, $request);
    $response = fread($socket, 1400);
    fclose($socket);
    if (!$response || strlen($response) < 6) return null;
    $pos = 4; // skip header
    $type = ord($response[$pos++]);
    if ($type !== 0x49) return null; // не I-тип
    $protocol = ord($response[$pos++]);
    // Name (null-terminated string)
    $name = '';
    while ($pos < strlen($response) && $response[$pos] !== "\x00") $name .= $response[$pos++];
    $pos++;
    // Map
    $map = '';
    while ($pos < strlen($response) && $response[$pos] !== "\x00") $map .= $response[$pos++];
    $pos++;
    // Folder
    while ($pos < strlen($response) && $response[$pos] !== "\x00") $pos++;
    $pos++;
    // Game
    while ($pos < strlen($response) && $response[$pos] !== "\x00") $pos++;
    $pos++;
    $pos += 2; // AppID
    $players    = ord($response[$pos++]);
    $max_players = ord($response[$pos++]);
    return ['name' => $name, 'map' => $map, 'players' => $players, 'max' => $max_players, 'online' => true];
}

function rank_label(int $rank): string {
    $ranks = [
        1 => 'Silver I', 2 => 'Silver II', 3 => 'Silver III', 4 => 'Silver IV',
        5 => 'Silver Elite', 6 => 'Silver Elite Master',
        7 => 'Gold Nova I', 8 => 'Gold Nova II', 9 => 'Gold Nova III', 10 => 'Gold Nova Master',
        11 => 'MG I', 12 => 'MG II', 13 => 'MGE',
        14 => 'DMG', 15 => 'LE', 16 => 'LEM',
        17 => 'Supreme', 18 => 'Global Elite',
    ];
    return $ranks[$rank] ?? 'Без ранга';
}

function rank_color(int $rank): string {
    if ($rank <= 6)  return '#9e9e9e';
    if ($rank <= 10) return '#f59e0b';
    if ($rank <= 13) return '#3b82f6';
    if ($rank <= 16) return '#ef4444';
    return '#8b5cf6';
}

function kd_ratio(int $kills, int $deaths): string {
    if ($deaths === 0) return number_format($kills, 2);
    return number_format($kills / $deaths, 2);
}

function time_played(int $seconds): string {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return "{$h}ч {$m}м";
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

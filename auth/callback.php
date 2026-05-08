<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
session_start();

// Validate OpenID response
if (empty($_GET['openid_claimed_id'])) {
    header('Location: /?auth_error=1');
    exit;
}

$claimedId = $_GET['openid_claimed_id'];
if (!preg_match('#^https?://steamcommunity\.com/openid/id/(\d{17})$#', $claimedId, $m)) {
    header('Location: /?auth_error=2');
    exit;
}
$steamid64 = $m[1];

// Verify with Steam
$params = $_GET;
$params['openid.mode'] = 'check_authentication';
$query = http_build_query($params);

$ctx = stream_context_create(['http' => [
    'method'  => 'POST',
    'header'  => 'Content-Type: application/x-www-form-urlencoded',
    'content' => $query,
    'timeout' => 8,
]]);
$result = @file_get_contents('https://steamcommunity.com/openid/login', false, $ctx);

if (!$result || strpos($result, 'is_valid:true') === false) {
    header('Location: /?auth_error=3');
    exit;
}

// Fetch Steam profile
$name   = 'Player';
$avatar = '/assets/img/default_avatar.png';

if (STEAM_API_KEY) {
    $url  = 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=' . STEAM_API_KEY . '&steamids=' . $steamid64;
    $data = @json_decode(@file_get_contents($url), true);
    if (!empty($data['response']['players'][0])) {
        $p      = $data['response']['players'][0];
        $name   = $p['personaname'] ?? $name;
        $avatar = $p['avatarmedium'] ?? $avatar;
    }
}

$user = syncUser($steamid64, $name, $avatar);
$_SESSION['user'] = $user;

$redirect = $_SESSION['login_redirect'] ?? '/';
unset($_SESSION['login_redirect']);

// Basic path validation
if (!preg_match('#^/#', $redirect)) $redirect = '/';

header('Location: ' . $redirect);
exit;

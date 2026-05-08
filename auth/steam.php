<?php
require_once __DIR__ . '/../config.php';
session_start();

if (isset($_GET['redirect'])) {
    $_SESSION['login_redirect'] = $_GET['redirect'];
}

$realm    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$callback = $realm . '/auth/callback.php';

$params = [
    'openid.ns'         => 'http://specs.openid.net/auth/2.0',
    'openid.mode'       => 'checkid_setup',
    'openid.return_to'  => $callback,
    'openid.realm'      => $realm,
    'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
    'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
];

header('Location: https://steamcommunity.com/openid/login?' . http_build_query($params));
exit;

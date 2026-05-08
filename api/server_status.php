<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$results = [];
foreach (SERVERS as $srv) {
    $info = query_server($srv['ip'], $srv['port']);
    $results[] = [
        'name'    => $srv['name'],
        'ip'      => $srv['ip'],
        'port'    => $srv['port'],
        'online'  => (bool)$info,
        'players' => $info['players'] ?? 0,
        'max'     => $info['max'] ?? 0,
        'map'     => $info['map'] ?? '—',
    ];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);

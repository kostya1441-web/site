<?php
// ЮKassa webhook handler
require_once __DIR__ . '/../includes/functions.php';

$body = file_get_contents('php://input');
$event = json_decode($body, true);

if (!$event || !isset($event['event'])) {
    http_response_code(400);
    exit('Bad request');
}

// Verify IP (ЮKassa sends from specific IPs)
$allowedIPs = ['185.71.76.0/27','185.71.77.0/27','77.75.153.0/25','77.75.156.11','77.75.156.35'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
// In production — uncomment IP validation:
// if (!ipInRanges($clientIP, $allowedIPs)) { http_response_code(403); exit; }

if ($event['event'] === 'payment.succeeded') {
    $payment = $event['object'];
    $orderId = (int)($payment['metadata']['order_id'] ?? 0);
    $paymentId = $payment['id'] ?? '';

    if ($orderId) {
        DB::update('orders',
            ['payment_status' => 'paid', 'payment_id' => $paymentId],
            'id = ? AND payment_status = ?',
            [$orderId, 'pending']
        );
    }
}

if ($event['event'] === 'payment.canceled') {
    $payment = $event['object'];
    $orderId = (int)($payment['metadata']['order_id'] ?? 0);
    if ($orderId) {
        DB::update('orders',
            ['payment_status' => 'failed'],
            'id = ? AND payment_status = ?',
            [$orderId, 'pending']
        );
    }
}

http_response_code(200);
echo 'OK';

function ipInRanges(string $ip, array $cidrs): bool {
    $ipLong = ip2long($ip);
    foreach ($cidrs as $cidr) {
        if (strpos($cidr, '/') === false) {
            if ($ip === $cidr) return true;
            continue;
        }
        [$subnet, $mask] = explode('/', $cidr);
        $subnetLong = ip2long($subnet);
        $maskLong   = ~((1 << (32 - (int)$mask)) - 1);
        if (($ipLong & $maskLong) === ($subnetLong & $maskLong)) return true;
    }
    return false;
}

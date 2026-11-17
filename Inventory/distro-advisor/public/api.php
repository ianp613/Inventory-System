<?php
declare(strict_types=1);

require __DIR__ . '/../src/Planner.php';

use DistroAdvisor\Planner;

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function respond(int $code, array $body): never
{
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'errors' => ['Use POST.']]);
}

$raw = file_get_contents('php://input', false, null, 0, 20000);
$in  = json_decode($raw === false ? '' : $raw, true);
if (!is_array($in)) {
    respond(400, ['ok' => false, 'errors' => ['Request body must be JSON.']]);
}

try {
    $planner = new Planner(__DIR__ . '/../data');
    $result  = $planner->plan($in);
    respond($result['ok'] ? 200 : 422, $result);
} catch (Throwable $e) {
    error_log('distro-advisor: ' . $e->getMessage());
    respond(500, ['ok' => false, 'errors' => ['Server error. Please try again.']]);
}

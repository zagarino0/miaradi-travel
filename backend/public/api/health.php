<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    database_connection()->query('SELECT 1');
    http_response_code(200);
    echo json_encode([
        'status' => 'ok',
        'service' => 'miaradi-travel-api',
        'database' => 'connected',
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    // Do not expose database details or credentials in public responses.
    error_log('[miaradi-api] Health check failed: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'service' => 'miaradi-travel-api',
        'database' => 'unavailable',
    ], JSON_UNESCAPED_SLASHES);
}

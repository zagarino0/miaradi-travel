<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['error' => ['code' => 'method_not_allowed', 'message' => 'Only GET requests are supported.']]);
    exit;
}
try {
    $rows = database_connection()->query(
        'SELECT option_type, option_value, option_label
         FROM miaradi_filter_options WHERE is_active = 1
         ORDER BY option_type ASC, sort_order ASC, option_label ASC'
    )->fetchAll();
    $options = ['region' => [], 'theme' => [], 'duration' => []];
    foreach ($rows as $row) {
        $type = (string) $row['option_type'];
        if (isset($options[$type])) $options[$type][] = ['value' => $row['option_value'], 'label' => $row['option_label']];
    }
    echo json_encode(['data' => $options], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Public filter options failed: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode(['error' => ['code' => 'filter_options_unavailable', 'message' => 'Filter options unavailable.']]);
}

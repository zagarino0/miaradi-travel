<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();

if ($method === 'GET') {
    try {
        $stmt = database_connection()->query(
            'SELECT q.id, q.full_name, q.email, q.desired_departure_date, q.traveler_count,
                    q.circuit_id, c.title_fr AS circuit_title_fr, c.title_en AS circuit_title_en,
                    q.message, q.preferred_language, q.status, q.privacy_consent_at, q.created_at
             FROM miaradi_quote_requests q
             LEFT JOIN miaradi_circuits c ON c.id = q.circuit_id
             ORDER BY q.created_at DESC, q.id DESC LIMIT 200'
        );
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['traveler_count'] = $row['traveler_count'] === null ? null : (int) $row['traveler_count'];
            $row['circuit_id'] = $row['circuit_id'] === null ? null : (int) $row['circuit_id'];
        }
        unset($row);
        api_json(['data' => $rows, 'meta' => ['limit' => 200]]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Admin quotes read failed: ' . $exception->getMessage());
        api_json(['error' => ['code' => 'quotes_unavailable', 'message' => 'Quote requests could not be loaded.']], 503);
    }
}

if ($method !== 'PATCH') {
    header('Allow: GET, PATCH');
    api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed.']], 405);
}
api_require_csrf();
$input = api_read_json();
$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$status = $input['status'] ?? null;
$allowed = ['new', 'in_progress', 'replied', 'closed', 'spam'];
if (!$id || !is_string($status) || !in_array($status, $allowed, true)) {
    api_json(['error' => ['code' => 'validation_error', 'message' => 'Quote ID or status is invalid.']], 422);
}

try {
    $stmt = database_connection()->prepare('UPDATE miaradi_quote_requests SET status=:status WHERE id=:id');
    $stmt->execute(['status' => $status, 'id' => $id]);
    $exists = database_connection()->prepare('SELECT id FROM miaradi_quote_requests WHERE id=:id');
    $exists->execute(['id' => $id]);
    if (!$exists->fetch()) api_json(['error' => ['code' => 'not_found', 'message' => 'Quote request not found.']], 404);
    api_json(['data' => ['id' => (int) $id, 'status' => $status]]);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Admin quote update failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'quote_update_failed', 'message' => 'Quote status could not be updated.']], 503);
}

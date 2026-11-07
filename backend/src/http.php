<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function api_json(array $payload, int $status = 200): never
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function api_read_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        api_json(['error' => ['code' => 'invalid_json', 'message' => 'A JSON request body is required.']], 400);
    }
    try {
        $data = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        api_json(['error' => ['code' => 'invalid_json', 'message' => 'The request body must be valid JSON.']], 400);
    }
    if (!is_array($data)) {
        api_json(['error' => ['code' => 'invalid_json', 'message' => 'The request body must be a JSON object.']], 400);
    }
    return $data;
}

function api_method(string ...$allowed): void
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if (!in_array($method, $allowed, true)) {
        header('Allow: ' . implode(', ', $allowed));
        api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed.']], 405);
    }
}

function api_require_admin(): array
{
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
        api_json(['error' => ['code' => 'unauthorized', 'message' => 'Authentication is required.']], 401);
    }
    return ['id' => (int) $_SESSION['admin_id'], 'email' => (string) $_SESSION['admin_email']];
}

function api_require_csrf(): void
{
    $sent = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $known = (string) ($_SESSION['csrf_token'] ?? '');
    if ($sent === '' || $known === '' || !hash_equals($known, $sent)) {
        api_json(['error' => ['code' => 'csrf_failed', 'message' => 'The security token is invalid or expired.']], 403);
    }
}

function api_clean_text(mixed $value, int $maxLength, bool $required = false): ?string
{
    if (!is_string($value)) {
        if ($value === null && !$required) return null;
        api_json(['error' => ['code' => 'validation_error', 'message' => 'A text field has an invalid value.']], 422);
    }
    $value = trim($value);
    if ($required && $value === '') {
        api_json(['error' => ['code' => 'validation_error', 'message' => 'A required field is missing.']], 422);
    }
    if (mb_strlen($value) > $maxLength) {
        api_json(['error' => ['code' => 'validation_error', 'message' => 'A text field exceeds the allowed length.']], 422);
    }
    return $value === '' ? null : $value;
}

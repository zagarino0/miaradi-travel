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

/**
 * Enforce a database-backed rate limit across app instances.
 * Identifiers are never stored in clear text; only scoped SHA-256 hashes are persisted.
 */
function api_enforce_rate_limit(
    PDO $pdo,
    string $scope,
    array $identifiers,
    int $limit,
    int $windowSeconds,
    int $blockSeconds
): void {
    $buckets = [];
    foreach ($identifiers as $identifier) {
        $identifier = trim((string) $identifier);
        if ($identifier !== '') {
            $buckets[] = hash('sha256', $scope . "\\0" . $identifier);
        }
    }
    $buckets = array_values(array_unique($buckets));
    sort($buckets, SORT_STRING);
    if ($buckets === []) {
        $buckets[] = hash('sha256', $scope . "\\0unknown");
    }

    $windowSeconds = max(1, min(86400, $windowSeconds));
    $blockSeconds = max(1, min(86400, $blockSeconds));
    $pdo->beginTransaction();
    try {
        foreach ($buckets as $bucket) {
            $insert = $pdo->prepare(
                'INSERT IGNORE INTO miaradi_api_rate_limits (bucket_key, scope, hits, window_started_at)
                 VALUES (:bucket_key, :scope, 0, UTC_TIMESTAMP())'
            );
            $insert->execute(['bucket_key' => $bucket, 'scope' => $scope]);
        }

        $blocked = false;
        foreach ($buckets as $bucket) {
            $select = $pdo->prepare(
                'SELECT hits,
                        (blocked_until IS NOT NULL AND blocked_until > UTC_TIMESTAMP()) AS is_blocked,
                        (window_started_at <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL ' . $windowSeconds . ' SECOND)) AS is_expired
                 FROM miaradi_api_rate_limits
                 WHERE bucket_key = :bucket_key
                 FOR UPDATE'
            );
            $select->execute(['bucket_key' => $bucket]);
            $row = $select->fetch();
            if (!$row) {
                throw new RuntimeException('Rate-limit bucket could not be locked.');
            }

            if ((int) $row['is_expired'] === 1) {
                $reset = $pdo->prepare(
                    'UPDATE miaradi_api_rate_limits
                     SET hits = 0, window_started_at = UTC_TIMESTAMP(), blocked_until = NULL
                     WHERE bucket_key = :bucket_key'
                );
                $reset->execute(['bucket_key' => $bucket]);
                $row['hits'] = 0;
                $row['is_blocked'] = 0;
            }

            if ((int) $row['is_blocked'] === 1 || (int) $row['hits'] >= $limit) {
                $block = $pdo->prepare(
                    'UPDATE miaradi_api_rate_limits
                     SET blocked_until = DATE_ADD(UTC_TIMESTAMP(), INTERVAL ' . $blockSeconds . ' SECOND)
                     WHERE bucket_key = :bucket_key'
                );
                $block->execute(['bucket_key' => $bucket]);
                $blocked = true;
            }
        }

        if (!$blocked) {
            foreach ($buckets as $bucket) {
                $increment = $pdo->prepare(
                    'UPDATE miaradi_api_rate_limits SET hits = hits + 1 WHERE bucket_key = :bucket_key'
                );
                $increment->execute(['bucket_key' => $bucket]);
            }
        }
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }

    if ($blocked) {
        header('Retry-After: ' . $blockSeconds);
        api_json([
            'error' => [
                'code' => 'rate_limited',
                'message' => 'Too many requests. Please try again later.',
            ],
        ], 429);
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

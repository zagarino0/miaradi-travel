<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
        api_json(['data' => ['authenticated' => false]]);
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    api_json(['data' => [
        'authenticated' => true,
        'admin' => ['id' => (int) $_SESSION['admin_id'], 'email' => (string) $_SESSION['admin_email']],
        'csrfToken' => $_SESSION['csrf_token'],
    ]]);
}

if ($method === 'POST') {
    $input = api_read_json();
    $action = (string) ($input['action'] ?? 'login');

    if ($action === 'logout') {
        api_require_admin();
        api_require_csrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax',
            ]);
        }
        session_destroy();
        api_json(['data' => ['authenticated' => false]]);
    }

    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $password = $input['password'] ?? null;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_string($password) || strlen($password) < 1 || strlen($password) > 1024) {
        api_json(['error' => ['code' => 'invalid_credentials', 'message' => 'Email or password is incorrect.']], 401);
    }

    try {
        $statement = database_connection()->prepare(
            'SELECT id, email, password_hash, is_active FROM miaradi_admin_users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $admin = $statement->fetch();

        if (!$admin || (int) $admin['is_active'] !== 1 || !password_verify($password, (string) $admin['password_hash'])) {
            // Keep the response identical for unknown users, inactive accounts and wrong passwords.
            usleep(250000);
            api_json(['error' => ['code' => 'invalid_credentials', 'message' => 'Email or password is incorrect.']], 401);
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_email'] = (string) $admin['email'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $update = database_connection()->prepare('UPDATE miaradi_admin_users SET last_login_at = UTC_TIMESTAMP() WHERE id = :id');
        $update->execute(['id' => (int) $admin['id']]);

        api_json(['data' => [
            'authenticated' => true,
            'admin' => ['id' => (int) $admin['id'], 'email' => (string) $admin['email']],
            'csrfToken' => $_SESSION['csrf_token'],
        ]]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Admin login failed: ' . $exception->getMessage());
        api_json(['error' => ['code' => 'authentication_unavailable', 'message' => 'Authentication is temporarily unavailable.']], 503);
    }
}

header('Allow: GET, POST');
api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed.']], 405);

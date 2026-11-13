<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Cache-Control: no-store');
api_method('GET', 'POST');
$pdo = database_connection();

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'GET') {
    try {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM miaradi_admin_users')->fetchColumn();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        api_json(['data' => [
            'available' => $count === 0,
            'csrfToken' => $_SESSION['csrf_token'],
        ]]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Admin setup status failed: ' . $exception->getMessage());
        api_json(['error' => ['code' => 'setup_unavailable', 'message' => 'La configuration de la base est incomplète. Vérifiez les migrations avant de continuer.']], 503);
    }
}

api_require_csrf();
$input = api_read_json();
$name = trim((string) ($input['displayName'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = $input['password'] ?? null;

if (($input['authorized'] ?? false) !== true) {
    api_json(['error' => ['code' => 'authorization_required', 'message' => 'Confirmez votre autorisation pour initialiser le compte.']], 422);
}
if ($name === '' || mb_strlen($name) > 180) {
    api_json(['error' => ['code' => 'invalid_name', 'message' => 'Saisissez un nom complet valide (180 caractères maximum).']], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    api_json(['error' => ['code' => 'invalid_email', 'message' => 'Saisissez une adresse e-mail valide.']], 422);
}
if (!is_string($password) || strlen($password) < 12 || strlen($password) > 72) {
    api_json(['error' => ['code' => 'invalid_password', 'message' => 'Le mot de passe doit contenir entre 12 et 72 octets.']], 422);
}

$lockAcquired = false;
try {
    $lockStatement = $pdo->query("SELECT GET_LOCK('miaradi_admin_initial_setup', 5)");
    $lockAcquired = (int) $lockStatement->fetchColumn() === 1;
    if (!$lockAcquired) {
        api_json(['error' => ['code' => 'setup_busy', 'message' => 'Une initialisation est déjà en cours. Réessayez dans un instant.']], 409);
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM miaradi_admin_users')->fetchColumn();
    if ($count > 0) {
        api_json(['error' => ['code' => 'setup_complete', 'message' => 'Un compte administrateur existe déjà. La création publique est désactivée.']], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if (!is_string($hash)) {
        throw new RuntimeException('Password hashing failed.');
    }
    $statement = $pdo->prepare(
        'INSERT INTO miaradi_admin_users (display_name, email, password_hash, is_active, auth_version)
         VALUES (:display_name, :email, :password_hash, 1, 1)'
    );
    $statement->execute(['display_name' => $name, 'email' => $email, 'password_hash' => $hash]);
    $adminId = (int) $pdo->lastInsertId();

    session_regenerate_id(true);
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_auth_version'] = 1;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    api_json(['data' => ['created' => true]]);
} catch (PDOException $exception) {
    error_log('[miaradi-api] Admin setup failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'setup_unavailable', 'message' => 'Impossible de créer le compte. Vérifiez que la migration 014 est appliquée.']], 503);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Admin setup failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'setup_unavailable', 'message' => 'Le service d’initialisation est temporairement indisponible.']], 503);
} finally {
    if ($lockAcquired) {
        try { $pdo->query("SELECT RELEASE_LOCK('miaradi_admin_initial_setup')"); } catch (Throwable $ignored) {}
    }
}

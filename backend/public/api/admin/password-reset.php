<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Cache-Control: no-store');
api_method('POST');

$input = api_read_json();
$action = (string) ($input['action'] ?? '');
$pdo = null;

try {
    $pdo = database_connection();

    if ($action === 'request') {
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            api_json(['error' => ['code' => 'validation_error', 'message' => 'Saisissez une adresse e-mail valide.']], 422);
        }

        $emailHash = hash('sha256', $email);
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $ipHash = hash('sha256', $ip);
        $rate = $pdo->prepare(
            'SELECT COUNT(*) FROM miaradi_admin_password_resets
             WHERE created_at >= (UTC_TIMESTAMP() - INTERVAL 1 HOUR)
               AND (email_hash = :email_hash OR request_ip_hash = :ip_hash)'
        );
        $rate->execute(['email_hash' => $emailHash, 'ip_hash' => $ipHash]);
        if ((int) $rate->fetchColumn() >= 5) {
            // Same public response: do not reveal account existence or rate-limit details.
            api_json(['data' => ['accepted' => true, 'message' => 'Si cette adresse correspond à un compte administrateur actif, un lien de réinitialisation sera envoyé.']]);
        }

        $find = $pdo->prepare('SELECT id, email FROM miaradi_admin_users WHERE email = :email AND is_active = 1 LIMIT 1');
        $find->execute(['email' => $email]);
        $admin = $find->fetch();

        if ($admin) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $insert = $pdo->prepare(
                'INSERT INTO miaradi_admin_password_resets
                    (admin_id, email_hash, token_hash, request_ip_hash, expires_at)
                 VALUES (:admin_id, :email_hash, :token_hash, :ip_hash, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 30 MINUTE))'
            );
            $insert->execute([
                'admin_id' => (int) $admin['id'],
                'email_hash' => $emailHash,
                'token_hash' => $tokenHash,
                'ip_hash' => $ipHash,
            ]);

            $appUrl = rtrim((string) app_env('APP_URL', ''), '/');
            $mailFrom = trim((string) app_env('MAIL_FROM', ''));
            if ($appUrl !== '' && filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
                $resetUrl = $appUrl . '/backend/public/admin/reset-password.php?token=' . rawurlencode($token);
                $subject = '=?UTF-8?B?' . base64_encode('Réinitialisation du mot de passe MIARADI') . '?=';
                $message = "Bonjour,\n\nUne demande de réinitialisation du mot de passe de votre compte administrateur MIARADI-TRAVEL a été effectuée.\n\nOuvrez ce lien dans les 30 minutes pour choisir un nouveau mot de passe :\n" . $resetUrl . "\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail. Votre mot de passe ne changera pas.\n\nL'équipe MIARADI-TRAVEL";
                $headers = "From: MIARADI-TRAVEL <{$mailFrom}>\r\nContent-Type: text/plain; charset=UTF-8";
                if (!mail((string) $admin['email'], $subject, $message, $headers)) {
                    error_log('[miaradi-auth] Password reset email could not be sent. Check PHP mail/SMTP configuration.');
                }
            } else {
                error_log('[miaradi-auth] Password reset email is not configured: APP_URL or MAIL_FROM is missing/invalid.');
            }
        }

        api_json(['data' => [
            'accepted' => true,
            'message' => 'Si cette adresse correspond à un compte administrateur actif, un lien de réinitialisation sera envoyé.'
        ]]);
    }

    if ($action === 'reset') {
        $token = (string) ($input['token'] ?? '');
        $password = $input['password'] ?? null;
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            api_json(['error' => ['code' => 'invalid_token', 'message' => 'Ce lien est invalide ou a expiré. Demandez un nouveau lien.']], 422);
        }
        if (!is_string($password) || strlen($password) < 12 || strlen($password) > 1024) {
            api_json(['error' => ['code' => 'weak_password', 'message' => 'Le mot de passe doit contenir au moins 12 caractères.']], 422);
        }

        $pdo->beginTransaction();
        $lookup = $pdo->prepare(
            'SELECT r.id, r.admin_id
             FROM miaradi_admin_password_resets r
             INNER JOIN miaradi_admin_users u ON u.id = r.admin_id
             WHERE r.token_hash = :token_hash
               AND r.used_at IS NULL
               AND r.expires_at > UTC_TIMESTAMP()
               AND u.is_active = 1
             LIMIT 1
             FOR UPDATE'
        );
        $lookup->execute(['token_hash' => hash('sha256', $token)]);
        $reset = $lookup->fetch();
        if (!$reset) {
            $pdo->rollBack();
            api_json(['error' => ['code' => 'invalid_token', 'message' => 'Ce lien est invalide ou a expiré. Demandez un nouveau lien.']], 422);
        }

        $update = $pdo->prepare('UPDATE miaradi_admin_users SET password_hash = :password_hash WHERE id = :id');
        $update->execute([
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'id' => (int) $reset['admin_id'],
        ]);
        $consume = $pdo->prepare('UPDATE miaradi_admin_password_resets SET used_at = UTC_TIMESTAMP() WHERE admin_id = :admin_id AND used_at IS NULL');
        $consume->execute(['admin_id' => (int) $reset['admin_id']]);
        $pdo->commit();

        api_json(['data' => ['reset' => true, 'message' => 'Votre mot de passe a été modifié. Vous pouvez maintenant vous connecter.']]);
    }

    api_json(['error' => ['code' => 'invalid_action', 'message' => 'Action non reconnue.']], 400);
} catch (Throwable $exception) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[miaradi-auth] Password reset failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'password_reset_unavailable', 'message' => 'Le service est temporairement indisponible. Réessayez plus tard.']], 503);
}

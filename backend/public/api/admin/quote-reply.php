<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

api_method('POST');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$admin = api_require_admin();
api_require_csrf();

$input = api_read_json();
$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$subject = api_clean_text($input['subject'] ?? null, 200, true);
$message = api_clean_text($input['message'] ?? null, 10000, true);

if (!$id) {
    api_json(['error' => ['code' => 'validation_error', 'message' => 'Identifiant de demande invalide.']], 422);
}

$mailFrom = app_env('MAIL_FROM');
if (!$mailFrom || !filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
    api_json(['error' => ['code' => 'mail_not_configured', 'message' => 'L’envoi d’e-mails n’est pas configuré. Vérifiez MAIL_FROM et la configuration de messagerie de l’hébergement.']], 503);
}

try {
    $pdo = database_connection();
    $stmt = $pdo->prepare(
        'SELECT id, full_name, email, preferred_language
         FROM miaradi_quote_requests WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $quote = $stmt->fetch();
    if (!$quote) {
        api_json(['error' => ['code' => 'not_found', 'message' => 'Demande de devis introuvable.']], 404);
    }

    $recipientName = trim((string) $quote['full_name']);
    $language = $quote['preferred_language'] === 'en' ? 'en' : 'fr';
    $greeting = $language === 'en' ? 'Hello ' . $recipientName . ',' : 'Bonjour ' . $recipientName . ',';
    $signature = $language === 'en'
        ? "Kind regards,\nMIARADI-TRAVEL Customer Service"
        : "Bien cordialement,\nLe service clientèle\nMIARADI-TRAVEL";
    $body = $greeting . "\n\n" . $message . "\n\n" . $signature;

    $headers = [
        'From' => 'MIARADI-TRAVEL <' . $mailFrom . '>',
        'Reply-To' => (string) $admin['email'],
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'X-Mailer' => 'MIARADI-TRAVEL',
    ];
    $headerLines = [];
    foreach ($headers as $key => $value) {
        $headerLines[] = $key . ': ' . str_replace(["\r", "\n"], '', $value);
    }
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $accepted = mail(
        (string) $quote['email'],
        $encodedSubject,
        $body,
        implode("\r\n", $headerLines)
    );

    if (!$accepted) {
        error_log('[miaradi-api] Customer reply mail() failed for quote #' . $id);
        api_json(['error' => ['code' => 'mail_send_failed', 'message' => 'Le serveur de messagerie a refusé l’envoi. La date et le statut n’ont pas été modifiés.']], 502);
    }

    // mail() returning true means the local mail transport accepted the message,
    // not that the recipient's mailbox has confirmed delivery.
    $update = $pdo->prepare(
        "UPDATE miaradi_quote_requests
         SET reply_subject = :subject,
             reply_message = :message,
             replied_at = UTC_TIMESTAMP(),
             status = 'replied'
         WHERE id = :id"
    );
    $update->execute(['subject' => $subject, 'message' => $message, 'id' => $id]);

    api_json([
        'data' => [
            'id' => (int) $id,
            'status' => 'replied',
            'replied_at' => gmdate('Y-m-d H:i:s'),
            'mail_accepted' => true,
        ],
        'message' => 'Le serveur de messagerie a accepté le message. La demande a été marquée comme « Client contacté ». La livraison finale dépend du fournisseur de messagerie.',
    ]);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Customer reply failed for quote #' . $id . ': ' . $exception->getMessage());
    api_json(['error' => ['code' => 'reply_failed', 'message' => 'La réponse n’a pas pu être enregistrée. Vérifiez les journaux du serveur avant de réessayer.']], 503);
}

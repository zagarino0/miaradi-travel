<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/http.php';

api_method('POST');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$input = api_read_json();
$name = api_clean_text($input['fullName'] ?? null, 180, true);
$email = api_clean_text($input['email'] ?? null, 254, true);
$message = api_clean_text($input['message'] ?? null, 5000, true);
$language = strtolower((string) ($input['language'] ?? 'fr'));
$date = $input['departureDate'] ?? null;
$count = $input['travelerCount'] ?? null;
$circuitId = $input['circuitId'] ?? null;
$consent = $input['privacyConsent'] ?? false;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_json(['error' => ['code' => 'invalid_email', 'message' => 'Please provide a valid email address.']], 422);
}
if (!in_array($language, ['fr', 'en'], true)) {
    api_json(['error' => ['code' => 'invalid_language', 'message' => 'Language must be fr or en.']], 422);
}
if ($consent !== true) {
    api_json(['error' => ['code' => 'consent_required', 'message' => 'Privacy consent is required to send this request.']], 422);
}
if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !checkdate((int) substr($date, 5, 2), (int) substr($date, 8, 2), (int) substr($date, 0, 4)))) {
    api_json(['error' => ['code' => 'invalid_departure_date', 'message' => 'Departure date must be a valid YYYY-MM-DD date.']], 422);
}
if ($count !== null && (!is_int($count) || $count < 1 || $count > 100)) {
    api_json(['error' => ['code' => 'invalid_traveler_count', 'message' => 'Traveler count must be between 1 and 100.']], 422);
}
if ($circuitId !== null && (!is_int($circuitId) || $circuitId < 1)) {
    api_json(['error' => ['code' => 'invalid_circuit', 'message' => 'Circuit ID must be a positive integer.']], 422);
}

try {
    $pdo = database_connection();
    if ($circuitId !== null) {
        $check = $pdo->prepare('SELECT id FROM miaradi_circuits WHERE id = :id AND is_published = 1 LIMIT 1');
        $check->execute(['id' => $circuitId]);
        if (!$check->fetch()) {
            api_json(['error' => ['code' => 'circuit_not_found', 'message' => 'The selected circuit is not available.']], 422);
        }
    }

    $statement = $pdo->prepare(
        'INSERT INTO miaradi_quote_requests
            (full_name, email, desired_departure_date, traveler_count, circuit_id, message, preferred_language, status, privacy_consent_at)
         VALUES
            (:full_name, :email, :departure_date, :traveler_count, :circuit_id, :message, :language, :status, UTC_TIMESTAMP())'
    );
    $statement->execute([
        'full_name' => $name,
        'email' => $email,
        'departure_date' => $date,
        'traveler_count' => $count,
        'circuit_id' => $circuitId,
        'message' => $message,
        'language' => $language,
        'status' => 'new',
    ]);

    $quoteId = (int) $pdo->lastInsertId();
    $mailTo = app_env('MAIL_TO');
    $mailFrom = app_env('MAIL_FROM');
    if ($mailTo && filter_var($mailTo, FILTER_VALIDATE_EMAIL) && $mailFrom && filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
        $subject = ($language === 'en' ? 'New MIARADI travel quote request #' : 'Nouvelle demande de devis MIARADI n°') . $quoteId;
        $body = "Request #{$quoteId}\nName: {$name}\nEmail: {$email}\nDeparture: " . ($date ?? 'not specified') .
            "\nTravellers: " . ($count === null ? 'not specified' : (string) $count) . "\nCircuit ID: " .
            ($circuitId === null ? 'tailor-made' : (string) $circuitId) . "\n\nMessage:\n{$message}";
        $headers = "From: {$mailFrom}\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8";
        if (!mail($mailTo, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers)) {
            error_log('[miaradi-api] Quote notification email failed for request #' . $quoteId);
        }
    } else {
        error_log('[miaradi-api] Quote request #' . $quoteId . ' stored; email settings are not configured.');
    }

    api_json(['data' => ['id' => $quoteId, 'status' => 'received'], 'message' => 'Your request has been received.'], 201);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Quote creation failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'quote_unavailable', 'message' => 'The request could not be saved. Please try again later.']], 503);
}

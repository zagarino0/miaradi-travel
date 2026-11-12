<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function privacy_policy_json(array $payload, int $status = 200): never
{
    api_json($payload, $status);
}

function privacy_policy_defaults(): array
{
    return [
        'id' => 1,
        'title_fr' => 'Politique de confidentialité',
        'title_en' => 'Privacy policy',
        'content_fr' => '',
        'content_en' => '',
        'is_published' => false,
        'updated_at' => null,
    ];
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    try {
        $stmt = database_connection()->query(
            'SELECT id, title_fr, title_en, content_fr, content_en, is_published, updated_at
             FROM miaradi_privacy_policy WHERE id = 1 LIMIT 1'
        );
        $policy = $stmt->fetch();
        if (!$policy) {
            $policy = privacy_policy_defaults();
        } else {
            $policy['id'] = (int) $policy['id'];
            $policy['is_published'] = (bool) $policy['is_published'];
        }

        $isAdmin = !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_email']);
        if (!$isAdmin && !$policy['is_published']) {
            privacy_policy_json(['error' => ['code' => 'policy_unavailable', 'message' => 'La politique de confidentialité n’est pas encore publiée.']], 404);
        }

        privacy_policy_json(['data' => $policy]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Privacy policy read failed: ' . $exception->getMessage());
        privacy_policy_json(['error' => ['code' => 'privacy_policy_unavailable', 'message' => 'Impossible de charger la politique de confidentialité.']], 503);
    }
}

if ($method !== 'POST') {
    header('Allow: GET, POST');
    privacy_policy_json(['error' => ['code' => 'method_not_allowed', 'message' => 'Méthode non autorisée.']], 405);
}

api_require_admin();
api_require_csrf();
$input = api_read_json();

$titleFr = api_clean_text($input['title_fr'] ?? null, 220, true);
$titleEn = api_clean_text($input['title_en'] ?? null, 220, true);
$contentFr = api_clean_text($input['content_fr'] ?? null, 30000, true);
$contentEn = api_clean_text($input['content_en'] ?? null, 30000, true);
$isPublished = $input['is_published'] ?? false;

if (!is_bool($isPublished)) {
    privacy_policy_json(['error' => ['code' => 'invalid_publication_status', 'message' => 'Le statut de publication est invalide.']], 422);
}

try {
    $pdo = database_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO miaradi_privacy_policy (id, title_fr, title_en, content_fr, content_en, is_published)
         VALUES (1, :title_fr, :title_en, :content_fr, :content_en, :is_published)
         ON DUPLICATE KEY UPDATE title_fr = VALUES(title_fr), title_en = VALUES(title_en),
             content_fr = VALUES(content_fr), content_en = VALUES(content_en),
             is_published = VALUES(is_published)'
    );
    $stmt->execute([
        'title_fr' => $titleFr,
        'title_en' => $titleEn,
        'content_fr' => $contentFr,
        'content_en' => $contentEn,
        'is_published' => $isPublished ? 1 : 0,
    ]);
    $stmt = $pdo->query('SELECT id, title_fr, title_en, content_fr, content_en, is_published, updated_at FROM miaradi_privacy_policy WHERE id = 1');
    $policy = $stmt->fetch();
    $policy['id'] = (int) $policy['id'];
    $policy['is_published'] = (bool) $policy['is_published'];
    privacy_policy_json(['data' => $policy, 'message' => 'Politique de confidentialité enregistrée.']);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Privacy policy save failed: ' . $exception->getMessage());
    privacy_policy_json(['error' => ['code' => 'privacy_policy_save_failed', 'message' => 'Impossible d’enregistrer la politique. Vérifiez que la migration 011_privacy_policy.sql est appliquée.']], 503);
}

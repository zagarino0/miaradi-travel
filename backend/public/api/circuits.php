<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');

function circuits_json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode(
        $payload,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    circuits_json_response([
        'error' => [
            'code' => 'method_not_allowed',
            'message' => 'Only GET requests are supported.',
        ],
    ], 405);
}

$language = strtolower(trim((string) ($_GET['lang'] ?? 'fr')));
if (!in_array($language, ['fr', 'en'], true)) {
    circuits_json_response([
        'error' => [
            'code' => 'invalid_language',
            'message' => 'The lang parameter must be fr or en.',
        ],
    ], 400);
}

try {
    $statement = database_connection()->prepare(
        "SELECT
            c.id,
            c.slug,
            c.title_fr,
            c.title_en,
            c.summary_fr,
            c.summary_en,
            c.region,
            c.theme,
            c.duration_min,
            c.duration_max,
            COALESCE(NULLIF(cp.hero_image, ''), c.image_path) AS image_path,
            c.updated_at
         FROM miaradi_circuits AS c
         LEFT JOIN miaradi_circuit_pages AS cp
           ON cp.circuit_id = c.id AND cp.is_published = 1
         WHERE c.is_published = 1
         ORDER BY c.sort_order ASC, c.id ASC"
    );
    $statement->execute();

    $circuits = array_map(
        static function (array $row) use ($language): array {
            $title = $language === 'en' && !empty($row['title_en'])
                ? $row['title_en']
                : $row['title_fr'];
            $summary = $language === 'en' && !empty($row['summary_en'])
                ? $row['summary_en']
                : $row['summary_fr'];

            return [
                'id' => (int) $row['id'],
                'slug' => $row['slug'],
                'title' => $title,
                'summary' => $summary,
                'region' => $row['region'],
                'theme' => $row['theme'],
                'duration' => [
                    'min' => $row['duration_min'] === null ? null : (int) $row['duration_min'],
                    'max' => $row['duration_max'] === null ? null : (int) $row['duration_max'],
                ],
                'image' => $row['image_path'],
                'updatedAt' => $row['updated_at'],
            ];
        },
        $statement->fetchAll()
    );

    circuits_json_response([
        'data' => $circuits,
        'meta' => [
            'count' => count($circuits),
            'language' => $language,
        ],
    ]);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Public circuits endpoint failed: ' . $exception->getMessage());
    circuits_json_response([
        'error' => [
            'code' => 'circuits_unavailable',
            'message' => 'The circuit catalogue is temporarily unavailable.',
        ],
    ], 503);
}

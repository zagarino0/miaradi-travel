<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['error' => ['code' => 'method_not_allowed', 'message' => 'Only GET requests are supported.']]);
    exit;
}

try {
    $sql = 'SELECT c.slug, c.title_fr, c.title_en, c.summary_fr, c.summary_en, c.region,
                   c.duration_min, c.duration_max, p.hero_title_fr, p.hero_title_en,
                   p.intro_fr, p.intro_en, p.hero_image, p.updated_at
            FROM miaradi_circuit_pages p
            INNER JOIN miaradi_circuits c ON c.id = p.circuit_id
            WHERE c.is_published = 1 AND p.is_published = 1
            ORDER BY c.sort_order ASC, c.id ASC';
    $rows = database_connection()->query($sql)->fetchAll();
    $pages = array_map(static function (array $row): array {
        $titleFr = trim((string)($row['hero_title_fr'] ?? ''));
        $titleEn = trim((string)($row['hero_title_en'] ?? ''));
        $introFr = trim((string)($row['intro_fr'] ?? ''));
        $introEn = trim((string)($row['intro_en'] ?? ''));
        return [
            'slug' => (string)$row['slug'],
            'title_fr' => $titleFr !== '' ? $titleFr : (string)$row['title_fr'],
            'title_en' => $titleEn !== '' ? $titleEn : ((string)($row['title_en'] ?? '') ?: (string)$row['title_fr']),
            'summary_fr' => $introFr !== '' ? $introFr : (string)($row['summary_fr'] ?? ''),
            'summary_en' => $introEn !== '' ? $introEn : ((string)($row['summary_en'] ?? '') ?: (string)($row['summary_fr'] ?? '')),
            'region' => (string)($row['region'] ?? ''),
            'duration_min' => isset($row['duration_min']) ? (int)$row['duration_min'] : null,
            'duration_max' => isset($row['duration_max']) ? (int)$row['duration_max'] : null,
            'hero_image' => (string)($row['hero_image'] ?? ''),
            'updated_at' => (string)($row['updated_at'] ?? ''),
        ];
    }, $rows);
    echo json_encode(['data' => $pages], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('[miaradi-api] Public circuit pages unavailable: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error' => ['code' => 'circuit_pages_unavailable', 'message' => 'Les pages de circuits sont temporairement indisponibles.']], JSON_UNESCAPED_UNICODE);
}

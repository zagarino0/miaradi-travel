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
    $sql = 'SELECT c.id, c.slug, c.title_fr, c.title_en, c.summary_fr, c.summary_en, c.region,
                   c.duration_min, c.duration_max, c.duration_nights, p.hero_title_fr, p.hero_title_en,
                   p.intro_fr, p.intro_en, p.hero_image, p.itinerary_json, p.updated_at
            FROM miaradi_circuit_pages p
            INNER JOIN miaradi_circuits c ON c.id = p.circuit_id
            WHERE c.is_published = 1 AND p.is_published = 1
            ORDER BY c.sort_order ASC, c.id ASC';
    $rows = database_connection()->query($sql)->fetchAll();
    $publicImageUrl = static function (mixed $value): string {
        $path = trim((string)$value);
        if ($path === '' || preg_match('~^(https?:)?//|^/~i', $path)) return $path;
        if (str_starts_with($path, 'assets/')) return $path;
        if (str_starts_with($path, 'backend/public/')) return $path;
        if (str_starts_with($path, 'uploads/')) return 'backend/public/' . $path;
        return $path;
    };
    $pages = array_map(static function (array $row) use ($publicImageUrl): array {
        $titleFr = trim((string)($row['hero_title_fr'] ?? ''));
        $titleEn = trim((string)($row['hero_title_en'] ?? ''));
        $introFr = trim((string)($row['intro_fr'] ?? ''));
        $introEn = trim((string)($row['intro_en'] ?? ''));
        $heroImage = $publicImageUrl($row['hero_image'] ?? '');
        $days = json_decode((string)($row['itinerary_json'] ?? ''), true);
        $gallery = [];
        $seen = [];
        $addImage = static function (mixed $value, mixed $alt) use (&$gallery, &$seen, $publicImageUrl): void {
            if (is_array($value)) {
                $alt = $value['alt'] ?? $value['title'] ?? $alt;
                $value = $value['url'] ?? $value['src'] ?? $value['path'] ?? '';
            }
            $url = $publicImageUrl($value);
            if ($url === '' || isset($seen[$url])) return;
            $seen[$url] = true;
            $gallery[] = ['url' => $url, 'alt' => trim((string)$alt)];
        };
        $addImage($heroImage, $titleFr !== '' ? $titleFr : (string)$row['title_fr']);
        foreach (is_array($days) ? $days : [] as $day) {
            if (!is_array($day)) continue;
            $dayTitle = trim((string)($day['title'] ?? $day['title_fr'] ?? $day['name'] ?? 'Photo du circuit'));
            foreach (is_array($day['images'] ?? null) ? $day['images'] : [] as $image) {
                $addImage($image, $dayTitle);
            }
        }
        return [
            'id' => (int)$row['id'],
            'slug' => (string)$row['slug'],
            'title_fr' => $titleFr !== '' ? $titleFr : (string)$row['title_fr'],
            'title_en' => $titleEn !== '' ? $titleEn : ((string)($row['title_en'] ?? '') ?: (string)$row['title_fr']),
            'summary_fr' => $introFr !== '' ? $introFr : (string)($row['summary_fr'] ?? ''),
            'summary_en' => $introEn !== '' ? $introEn : ((string)($row['summary_en'] ?? '') ?: (string)($row['summary_fr'] ?? '')),
            'region' => (string)($row['region'] ?? ''),
            'duration_min' => isset($row['duration_min']) ? (int)$row['duration_min'] : null,
            'duration_max' => isset($row['duration_max']) ? (int)$row['duration_max'] : null,
            'duration_nights' => isset($row['duration_nights']) ? (int)$row['duration_nights'] : null,
            'hero_image' => $heroImage,
            'gallery' => $gallery,
            'updated_at' => (string)($row['updated_at'] ?? ''),
        ];
    }, $rows);
    echo json_encode(['data' => $pages], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('[miaradi-api] Public circuit pages unavailable: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error' => ['code' => 'circuit_pages_unavailable', 'message' => 'Les pages de circuits sont temporairement indisponibles.']], JSON_UNESCAPED_UNICODE);
}

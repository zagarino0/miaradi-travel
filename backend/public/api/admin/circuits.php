<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();

if ($method === 'GET') {
    try {
        $rows = database_connection()->query(
            'SELECT id, slug, title_fr, title_en, summary_fr, summary_en, region, theme,
                    duration_min, duration_max, image_path, is_published, sort_order, created_at, updated_at
             FROM miaradi_circuits ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['duration_min'] = $row['duration_min'] === null ? null : (int) $row['duration_min'];
            $row['duration_max'] = $row['duration_max'] === null ? null : (int) $row['duration_max'];
            $row['is_published'] = (bool) $row['is_published'];
            $row['sort_order'] = (int) $row['sort_order'];
        }
        unset($row);
        api_json(['data' => $rows]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Admin circuits read failed: ' . $exception->getMessage());
        api_json(['error' => ['code' => 'circuits_unavailable', 'message' => 'Circuits could not be loaded.']], 503);
    }
}

if (!in_array($method, ['POST', 'PATCH', 'DELETE'], true)) {
    header('Allow: GET, POST, PATCH, DELETE');
    api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed.']], 405);
}
api_require_csrf();

try {
    $pdo = database_connection();
    if ($method === 'DELETE') {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) api_json(['error' => ['code' => 'invalid_id', 'message' => 'A valid circuit ID is required.']], 422);
        $stmt = $pdo->prepare('DELETE FROM miaradi_circuits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) api_json(['error' => ['code' => 'not_found', 'message' => 'Circuit not found.']], 404);
        api_json(['data' => ['deleted' => true]]);
    }

    $input = api_read_json();
    $id = null;
    if ($method === 'PATCH') {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) api_json(['error' => ['code' => 'invalid_id', 'message' => 'A valid circuit ID is required.']], 422);
    }

    $slug = api_clean_text($input['slug'] ?? null, 180, true);
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $slug)) {
        api_json(['error' => ['code' => 'invalid_slug', 'message' => 'Slug must contain lowercase letters, numbers and hyphens only.']], 422);
    }
    $titleFr = api_clean_text($input['title_fr'] ?? null, 200, true);
    $titleEn = api_clean_text($input['title_en'] ?? null, 200);
    $summaryFr = api_clean_text($input['summary_fr'] ?? null, 10000, true);
    $summaryEn = api_clean_text($input['summary_en'] ?? null, 10000);
    $region = api_clean_text($input['region'] ?? null, 80);
    $theme = api_clean_text($input['theme'] ?? null, 80);
    $image = api_clean_text($input['image_path'] ?? null, 500);
    $durationMin = $input['duration_min'] ?? null;
    $durationMax = $input['duration_max'] ?? null;
    $published = $input['is_published'] ?? false;
    $sortOrder = $input['sort_order'] ?? 0;

    foreach (['duration_min' => $durationMin, 'duration_max' => $durationMax] as $field => $value) {
        if ($value !== null && (!is_int($value) || $value < 1 || $value > 365)) {
            api_json(['error' => ['code' => 'validation_error', 'message' => $field . ' must be between 1 and 365.']], 422);
        }
    }
    if ($durationMin !== null && $durationMax !== null && $durationMin > $durationMax) {
        api_json(['error' => ['code' => 'validation_error', 'message' => 'Minimum duration cannot exceed maximum duration.']], 422);
    }
    if (!is_bool($published) || !is_int($sortOrder) || $sortOrder < -100000 || $sortOrder > 100000) {
        api_json(['error' => ['code' => 'validation_error', 'message' => 'Publication or display order is invalid.']], 422);
    }

    $values = [
        'slug' => $slug, 'title_fr' => $titleFr, 'title_en' => $titleEn,
        'summary_fr' => $summaryFr, 'summary_en' => $summaryEn, 'region' => $region,
        'theme' => $theme, 'duration_min' => $durationMin, 'duration_max' => $durationMax,
        'image_path' => $image, 'is_published' => $published ? 1 : 0, 'sort_order' => $sortOrder,
    ];

    if ($method === 'POST') {
        $stmt = $pdo->prepare(
            'INSERT INTO miaradi_circuits
             (slug, title_fr, title_en, summary_fr, summary_en, region, theme, duration_min, duration_max, image_path, is_published, sort_order)
             VALUES (:slug, :title_fr, :title_en, :summary_fr, :summary_en, :region, :theme, :duration_min, :duration_max, :image_path, :is_published, :sort_order)'
        );
        $stmt->execute($values);
        api_json(['data' => ['id' => (int) $pdo->lastInsertId()]], 201);
    }

    $values['id'] = $id;
    $stmt = $pdo->prepare(
        'UPDATE miaradi_circuits SET slug=:slug, title_fr=:title_fr, title_en=:title_en,
         summary_fr=:summary_fr, summary_en=:summary_en, region=:region, theme=:theme,
         duration_min=:duration_min, duration_max=:duration_max, image_path=:image_path,
         is_published=:is_published, sort_order=:sort_order WHERE id=:id'
    );
    $stmt->execute($values);
    if ($stmt->rowCount() === 0) {
        $exists = $pdo->prepare('SELECT id FROM miaradi_circuits WHERE id=:id');
        $exists->execute(['id' => $id]);
        if (!$exists->fetch()) api_json(['error' => ['code' => 'not_found', 'message' => 'Circuit not found.']], 404);
    }
    api_json(['data' => ['id' => (int) $id, 'updated' => true]]);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        api_json(['error' => ['code' => 'duplicate_slug', 'message' => 'This slug is already used by another circuit.']], 409);
    }
    error_log('[miaradi-api] Admin circuit mutation failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'circuit_save_failed', 'message' => 'Circuit could not be saved.']], 503);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Admin circuit mutation failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'circuit_save_failed', 'message' => 'Circuit could not be saved.']], 503);
}

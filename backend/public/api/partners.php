<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
$lang = strtolower((string)($_GET['lang'] ?? 'fr'));
if (!in_array($lang, ['fr','en'], true)) $lang = 'fr';
$scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
$publicBase = preg_replace('~/api(?:/admin)?/[^/]+$~', '', $scriptName) ?: '';
try {
    $rows = database_connection()->query('SELECT slot_key,title_fr,description_fr,alt_text,image_path,sort_order,updated_at FROM miaradi_partner_media ORDER BY sort_order ASC')->fetchAll();
    $data = [];
    foreach ($rows as $row) {
        $path = $row['image_path'] ? rtrim($publicBase, '/') . '/' . ltrim((string)$row['image_path'], '/') : null;
        $data[] = [
            'slot' => (string)$row['slot_key'],
            'title' => (string)$row['title_fr'],
            'description' => (string)($row['description_fr'] ?? ''),
            'alt' => (string)$row['alt_text'],
            'image' => $path,
            'updatedAt' => (string)$row['updated_at'],
        ];
    }
    echo json_encode(['data'=>$data], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('[miaradi-api] Public partner media read failed: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error'=>['code'=>'partner_media_unavailable','message'=>'Les images du réseau local ne sont pas configurées.']], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}

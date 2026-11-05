<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['error'=>['code'=>'method_not_allowed','message'=>'Only GET requests are supported.']]);
    exit;
}
$lang=strtolower(trim((string)($_GET['lang'] ?? 'fr')));
if (!in_array($lang,['fr','en'],true)) {
    http_response_code(400);
    echo json_encode(['error'=>['code'=>'invalid_language','message'=>'The lang parameter must be fr or en.']]);
    exit;
}
try {
    $stmt=database_connection()->query("SELECT id,media_type,source_type,title_fr,title_en,description_fr,description_en,alt_text,file_path,external_url,thumbnail_path,sort_order,updated_at FROM miaradi_gallery_items WHERE is_published=1 ORDER BY sort_order ASC,id DESC");
    $items=array_map(static function(array $r) use ($lang): array {
        return [
            'id'=>(int)$r['id'],
            'type'=>$r['media_type'],
            'source'=>$r['source_type'],
            'title'=>($lang==='en' && !empty($r['title_en']))?$r['title_en']:$r['title_fr'],
            'description'=>($lang==='en' && !empty($r['description_en']))?$r['description_en']:$r['description_fr'],
            'alt'=>$r['alt_text'],
            'url'=>$r['file_path'] ? '../'.$r['file_path'] : $r['external_url'],
            'thumbnail'=>$r['thumbnail_path'] ?: ($r['file_path'] ? '../'.$r['file_path'] : null),
            'updatedAt'=>$r['updated_at']
        ];
    },$stmt->fetchAll());
    echo json_encode(['data'=>$items,'meta'=>['count'=>count($items),'language'=>$lang]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('[miaradi-api] Public gallery read failed: '.$e->getMessage());
    http_response_code(503);
    echo json_encode(['error'=>['code'=>'gallery_unavailable','message'=>'The gallery is temporarily unavailable.']]);
}

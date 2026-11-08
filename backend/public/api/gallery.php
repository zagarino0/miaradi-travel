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
    $sql="SELECT g.id,g.circuit_id,c.slug AS circuit_slug,c.title_fr AS circuit_title_fr,c.title_en AS circuit_title_en,g.media_type,g.source_type,g.title_fr,g.title_en,g.description_fr,g.description_en,g.alt_text,g.file_path,g.external_url,g.thumbnail_path,g.sort_order,g.updated_at FROM miaradi_gallery_items g LEFT JOIN miaradi_circuits c ON c.id=g.circuit_id WHERE g.is_published=1";
    $params=[];
    $circuitIdRaw=$_GET['circuit_id'] ?? null;
    if ($circuitIdRaw !== null && $circuitIdRaw !== '') {
        $circuitId=filter_var($circuitIdRaw,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$circuitId) { http_response_code(400); echo json_encode(['error'=>['code'=>'invalid_circuit_id','message'=>'circuit_id must be a positive integer.']]); exit; }
        $sql.=' AND g.circuit_id=:circuit_id'; $params['circuit_id']=$circuitId;
    }
    $sql.=' ORDER BY g.sort_order ASC,g.id DESC';
    $stmt=database_connection()->prepare($sql);
    $stmt->execute($params);
    $items=array_map(static function(array $r) use ($lang): array {
        return [
            'id'=>(int)$r['id'],
            'circuit'=> $r['circuit_id'] === null ? null : ['id'=>(int)$r['circuit_id'],'slug'=>$r['circuit_slug'],'title'=>($lang==='en' && !empty($r['circuit_title_en']))?$r['circuit_title_en']:$r['circuit_title_fr']],
            'type'=>$r['media_type'],
            'source'=>$r['source_type'],
            'title'=>($lang==='en' && !empty($r['title_en']))?$r['title_en']:$r['title_fr'],
            'description'=>($lang==='en' && !empty($r['description_en']))?$r['description_en']:$r['description_fr'],
            'alt'=>$r['alt_text'],
            'url'=>$r['file_path'] ? rtrim(dirname(dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/api/gallery.php'))), '/').'/'.$r['file_path'] : $r['external_url'],
            'thumbnail'=>$r['thumbnail_path'] ?: ($r['file_path'] ? rtrim(dirname(dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/api/gallery.php'))), '/').'/'.$r['file_path'] : null),
            'updatedAt'=>$r['updated_at']
        ];
    },$stmt->fetchAll());
    echo json_encode(['data'=>$items,'meta'=>['count'=>count($items),'language'=>$lang]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('[miaradi-api] Public gallery read failed: '.$e->getMessage());
    http_response_code(503);
    echo json_encode(['error'=>['code'=>'gallery_unavailable','message'=>'The gallery is temporarily unavailable.']]);
}

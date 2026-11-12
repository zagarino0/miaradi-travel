<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
$scriptName=(string)($_SERVER['SCRIPT_NAME']??'');
$publicBase=preg_replace('~/api/[^/]+$~','',$scriptName)?:'';
try {
    $stmt=database_connection()->query('SELECT id,name,website_url,logo_path,alt_text,sort_order FROM miaradi_footer_partners WHERE is_published=1 ORDER BY sort_order ASC,id ASC');
    $data=[];
    foreach($stmt->fetchAll() as $row) {
        $data[]=[
            'id'=>(int)$row['id'],
            'name'=>(string)$row['name'],
            'url'=>$row['website_url'] ? (string)$row['website_url'] : null,
            'logo'=>rtrim($publicBase,'/').'/'.ltrim((string)$row['logo_path'],'/'),
            'alt'=>(string)($row['alt_text'] ?: $row['name'])
        ];
    }
    echo json_encode(['data'=>$data],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
} catch(Throwable $e) {
    error_log('[miaradi-api] Public footer partners read failed: '.$e->getMessage());
    http_response_code(503);
    echo json_encode(['error'=>['code'=>'footer_partners_unavailable','message'=>'Les partenaires du pied de page ne sont pas configurés.']],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}

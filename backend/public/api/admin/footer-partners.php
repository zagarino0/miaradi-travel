<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/http.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();

$pdo = database_connection();
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$uploadDir = dirname(__DIR__, 2) . '/uploads/footer-partners';
$scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
$publicBase = preg_replace('~/api(?:/admin)?/[^/]+$~', '', $scriptName) ?: '';
function footer_partner_payload(array $row, string $base): array {
    $row['id'] = (int)$row['id'];
    $row['sort_order'] = (int)$row['sort_order'];
    $row['is_published'] = (bool)$row['is_published'];
    $row['logo_url'] = rtrim($base, '/') . '/' . ltrim((string)$row['logo_path'], '/');
    return $row;
}
if ($method === 'GET') {
    try {
        $rows = $pdo->query('SELECT id,name,website_url,logo_path,alt_text,sort_order,is_published,created_at,updated_at FROM miaradi_footer_partners ORDER BY sort_order ASC,id ASC')->fetchAll();
        api_json(['data'=>array_map(fn($row)=>footer_partner_payload($row,$publicBase),$rows)]);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Footer partners read failed: '.$e->getMessage());
        api_json(['error'=>['code'=>'footer_partners_unavailable','message'=>'La table des partenaires du pied de page est indisponible. Appliquez la migration 009.']],503);
    }
}
if ($method !== 'POST') {
    header('Allow: GET, POST');
    api_json(['error'=>['code'=>'method_not_allowed','message'=>'Méthode HTTP non autorisée.']],405);
}
api_require_csrf();
try {
    $action = (string)($_POST['action'] ?? 'save');
    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) api_json(['error'=>['code'=>'invalid_id','message'=>'Partenaire invalide.']],422);
        $stmt=$pdo->prepare('SELECT logo_path FROM miaradi_footer_partners WHERE id=?');
        $stmt->execute([$id]); $row=$stmt->fetch();
        if (!$row) api_json(['error'=>['code'=>'not_found','message'=>'Partenaire introuvable.']],404);
        $pdo->prepare('DELETE FROM miaradi_footer_partners WHERE id=?')->execute([$id]);
        $path=dirname(__DIR__,2).'/'.ltrim((string)$row['logo_path'],'/');
        if (is_file($path)) @unlink($path);
        api_json(['success'=>true]);
    }
    $id = (int)($_POST['id'] ?? 0);
    $name = api_clean_text($_POST['name'] ?? null,120,true);
    $url = api_clean_text($_POST['website_url'] ?? null,500);
    $alt = api_clean_text($_POST['alt_text'] ?? null,180);
    $order = filter_var($_POST['sort_order'] ?? 0,FILTER_VALIDATE_INT);
    if ($order === false || $order < 0 || $order > 10000) api_json(['error'=>['code'=>'invalid_order','message'=>'L’ordre doit être compris entre 0 et 10000.']],422);
    $published = isset($_POST['is_published']) && (string)$_POST['is_published'] === '1' ? 1 : 0;
    if ($url !== null) {
        $parts=parse_url($url);
        if (!$parts || empty($parts['scheme']) || !in_array(strtolower((string)$parts['scheme']),['https','http'],true) || empty($parts['host'])) api_json(['error'=>['code'=>'invalid_url','message'=>'Le lien doit être une URL HTTP ou HTTPS valide.']],422);
    }
    $existing=null;
    if ($id>0) { $st=$pdo->prepare('SELECT logo_path FROM miaradi_footer_partners WHERE id=?');$st->execute([$id]);$existing=$st->fetch();if(!$existing) api_json(['error'=>['code'=>'not_found','message'=>'Partenaire introuvable.']],404); }
    $logoPath=$existing['logo_path'] ?? null;
    if (isset($_FILES['logo']) && (int)$_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file=$_FILES['logo'];
        if ((int)$file['error']!==UPLOAD_ERR_OK) api_json(['error'=>['code'=>'upload_failed','message'=>'Le téléversement du logo a échoué.']],422);
        if ((int)$file['size']<1 || (int)$file['size']>5*1024*1024) api_json(['error'=>['code'=>'invalid_size','message'=>'Le logo doit peser 5 Mo maximum.']],422);
        $tmp=(string)$file['tmp_name'];$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($tmp);
        $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!is_string($mime)||!isset($types[$mime])) api_json(['error'=>['code'=>'invalid_logo','message'=>'Formats acceptés : PNG, JPG et WebP.']],422);
        if ($mime!=='image/svg+xml' && @getimagesize($tmp)===false) api_json(['error'=>['code'=>'invalid_logo','message'=>'Le fichier image est invalide.']],422);
        if (!is_dir($uploadDir)&&!mkdir($uploadDir,0755,true)&&!is_dir($uploadDir)) throw new RuntimeException('Cannot create footer partner upload directory');
        $filename='partner-'.bin2hex(random_bytes(12)).'.'.$types[$mime];
        if (!move_uploaded_file($tmp,$uploadDir.'/'.$filename)) throw new RuntimeException('Cannot store footer partner logo');
        $logoPath='uploads/footer-partners/'.$filename;
    }
    if (!$logoPath) api_json(['error'=>['code'=>'logo_required','message'=>'Ajoutez un logo pour ce partenaire.']],422);
    if ($alt===null) $alt=$name;
    if ($id>0) {
        $stmt=$pdo->prepare('UPDATE miaradi_footer_partners SET name=?,website_url=?,logo_path=?,alt_text=?,sort_order=?,is_published=? WHERE id=?');
        $stmt->execute([$name,$url,$logoPath,$alt,$order,$published,$id]);
    } else {
        $stmt=$pdo->prepare('INSERT INTO miaradi_footer_partners(name,website_url,logo_path,alt_text,sort_order,is_published) VALUES(?,?,?,?,?,?)');
        $stmt->execute([$name,$url,$logoPath,$alt,$order,$published]);$id=(int)$pdo->lastInsertId();
    }
    $stmt=$pdo->prepare('SELECT id,name,website_url,logo_path,alt_text,sort_order,is_published,created_at,updated_at FROM miaradi_footer_partners WHERE id=?');
    $stmt->execute([$id]);api_json(['data'=>footer_partner_payload($stmt->fetch(),$publicBase)]);
} catch (Throwable $e) {
    error_log('[miaradi-api] Footer partner save failed: '.$e->getMessage());
    api_json(['error'=>['code'=>'footer_partner_save_failed','message'=>'Impossible d’enregistrer ce partenaire. Vérifiez la configuration du serveur.']],500);
}

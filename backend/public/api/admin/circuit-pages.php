<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_role('super_admin', 'manager', 'editor');

function circuit_pages_json(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function circuit_pages_text(mixed $value, int $max, bool $required = false): ?string {
    if ($value === null) return $required ? '' : null;
    if (!is_string($value)) circuit_pages_json(['error'=>['code'=>'invalid_field','message'=>'Un champ texte contient une valeur invalide.']],422);
    $value = trim($value);
    if ($required && $value === '') circuit_pages_json(['error'=>['code'=>'required_field','message'=>'Le titre et les textes obligatoires doivent être renseignés.']],422);
    if (mb_strlen($value) > $max) circuit_pages_json(['error'=>['code'=>'field_too_long','message'=>'Un texte dépasse la longueur autorisée.']],422);
    return $value === '' ? null : $value;
}
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method === 'GET') {
    try {
        $sql = 'SELECT p.id, p.circuit_id, c.slug, c.title_fr, c.title_en, p.hero_title_fr, p.hero_title_en,
                       p.intro_fr, p.intro_en, p.hero_image, p.itinerary_json, p.is_published, p.updated_at
                FROM miaradi_circuit_pages p
                INNER JOIN miaradi_circuits c ON c.id=p.circuit_id
                ORDER BY p.updated_at DESC';
        $rows = database_connection()->query($sql)->fetchAll();
        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id']; $row['circuit_id'] = (int)$row['circuit_id'];
            $row['is_published'] = (bool)$row['is_published'];
            $decoded = json_decode((string)$row['itinerary_json'], true);
            $row['days'] = is_array($decoded) ? $decoded : [];
            unset($row['itinerary_json']);
        }
        unset($row);
        $circuits = database_connection()->query('SELECT c.id,c.slug,c.title_fr,c.title_en FROM miaradi_circuits c ORDER BY c.sort_order,c.id')->fetchAll();
        foreach ($circuits as &$c) $c['id'] = (int)$c['id'];
        unset($c);
        circuit_pages_json(['data'=>$rows,'circuits'=>$circuits]);
    } catch (PDOException $e) {
        error_log('[miaradi-api] Circuit pages list failed: '.$e->getMessage());
        $driverCode = (string)($e->errorInfo[1] ?? '');
        if ($driverCode === '1146' || str_contains(strtolower($e->getMessage()), 'miaradi_circuit_pages')) {
            circuit_pages_json(['error'=>['code'=>'circuit_pages_migration_missing','message'=>'La table des pages détaillées est absente de cette base. Importez backend/migrations/010_circuit_pages.sql dans phpMyAdmin, sur la même base que le site, puis rechargez cette page.']],503);
        }
        circuit_pages_json(['error'=>['code'=>'circuit_pages_database_error','message'=>'La base répond, mais la lecture des pages détaillées a échoué. Vérifiez les tables miaradi_circuits et miaradi_circuit_pages ainsi que les logs PHP.']],503);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Circuit pages list failed: '.$e->getMessage());
        circuit_pages_json(['error'=>['code'=>'circuit_pages_unavailable','message'=>'Impossible de se connecter à la base ou de charger les pages détaillées. Vérifiez backend/miaradi-local-config.php (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD), puis les logs PHP.']],503);
    }
}
if (!in_array($method,['POST','PATCH','DELETE'],true)) {
    header('Allow: GET, POST, PATCH, DELETE');
    circuit_pages_json(['error'=>['code'=>'method_not_allowed','message'=>'Méthode HTTP non autorisée.']],405);
}
api_require_csrf();
try {
    $pdo=database_connection();
    if ($method==='DELETE') {
        $id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) circuit_pages_json(['error'=>['code'=>'invalid_id','message'=>'Identifiant de page invalide.']],422);
        $st=$pdo->prepare('DELETE FROM miaradi_circuit_pages WHERE id=?'); $st->execute([$id]);
        if (!$st->rowCount()) circuit_pages_json(['error'=>['code'=>'not_found','message'=>'Page introuvable.']],404);
        circuit_pages_json(['data'=>['deleted'=>true]]);
    }
    $input=api_read_json();
    $id=null;
    if ($method==='PATCH') {
        $id=filter_var($input['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) circuit_pages_json(['error'=>['code'=>'invalid_id','message'=>'Identifiant de page invalide.']],422);
    }
    $circuitId=filter_var($input['circuit_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if (!$circuitId) circuit_pages_json(['error'=>['code'=>'invalid_circuit','message'=>'Sélectionnez un circuit du catalogue.']],422);
    $check=$pdo->prepare('SELECT id FROM miaradi_circuits WHERE id=?'); $check->execute([$circuitId]);
    if (!$check->fetchColumn()) circuit_pages_json(['error'=>['code'=>'invalid_circuit','message'=>'Le circuit sélectionné n’existe plus.']],422);
    $heroFr=circuit_pages_text($input['hero_title_fr']??null,220);
    $heroEn=circuit_pages_text($input['hero_title_en']??null,220);
    $introFr=circuit_pages_text($input['intro_fr']??null,10000);
    $introEn=circuit_pages_text($input['intro_en']??null,10000);
    $heroImage=circuit_pages_text($input['hero_image']??null,500);
    $published=$input['is_published']??false;
    $days=$input['days']??[];
    if (!is_bool($published) || !is_array($days) || count($days)>60) circuit_pages_json(['error'=>['code'=>'invalid_content','message'=>'Le statut ou le nombre de journées est invalide (60 maximum).']],422);
    $cleanDays=[];
    foreach ($days as $i=>$day) {
        if (!is_array($day)) circuit_pages_json(['error'=>['code'=>'invalid_day','message'=>'Une journée est mal renseignée.']],422);
        $title=circuit_pages_text($day['title']??null,220,true);
        $subtitle=circuit_pages_text($day['subtitle']??null,220);
        $description=circuit_pages_text($day['description']??null,5000);
        $overnight=circuit_pages_text($day['overnight']??null,500);
        $images=$day['images']??[];
        if (!is_array($images) || count($images)>10) circuit_pages_json(['error'=>['code'=>'invalid_images','message'=>'Chaque journée accepte au maximum 10 images.']],422);
        $cleanImages=[];
        foreach ($images as $image) {
            if (!is_array($image)) continue;
            $url=circuit_pages_text($image['url']??null,1000);
            $alt=circuit_pages_text($image['alt']??null,250);
            if ($url) {
                if (!preg_match('~^(https?://|/|assets/|uploads/)~i',$url)) circuit_pages_json(['error'=>['code'=>'invalid_image_url','message'=>'Une image doit utiliser une URL HTTP(S) ou un chemin local autorisé.']],422);
                $cleanImages[]=['url'=>$url,'alt'=>$alt??$title];
            }
        }
        $cleanDays[]=['title'=>$title,'subtitle'=>$subtitle,'description'=>$description??'','overnight'=>$overnight??'','images'=>$cleanImages];
    }
    if ($method==='POST') {
        $st=$pdo->prepare('INSERT INTO miaradi_circuit_pages (circuit_id,hero_title_fr,hero_title_en,intro_fr,intro_en,hero_image,itinerary_json,is_published) VALUES (?,?,?,?,?,?,?,?)');
        $st->execute([$circuitId,$heroFr,$heroEn,$introFr,$introEn,$heroImage,json_encode($cleanDays,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),$published?1:0]);
        circuit_pages_json(['data'=>['id'=>(int)$pdo->lastInsertId(),'saved'=>true]],201);
    }
    $exists=$pdo->prepare('SELECT id FROM miaradi_circuit_pages WHERE id=?');$exists->execute([$id]);
    if (!$exists->fetchColumn()) circuit_pages_json(['error'=>['code'=>'not_found','message'=>'Page introuvable.']],404);
    $st=$pdo->prepare('UPDATE miaradi_circuit_pages SET circuit_id=?,hero_title_fr=?,hero_title_en=?,intro_fr=?,intro_en=?,hero_image=?,itinerary_json=?,is_published=? WHERE id=?');
    $st->execute([$circuitId,$heroFr,$heroEn,$introFr,$introEn,$heroImage,json_encode($cleanDays,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),$published?1:0,$id]);
    circuit_pages_json(['data'=>['id'=>(int)$id,'saved'=>true]]);
} catch (PDOException $e) {
    if ($e->getCode()==='23000') circuit_pages_json(['error'=>['code'=>'page_exists','message'=>'Une page détaillée existe déjà pour ce circuit. Modifiez la page existante.']],409);
    error_log('[miaradi-api] Circuit page save failed: '.$e->getMessage());
    circuit_pages_json(['error'=>['code'=>'save_failed','message'=>'Impossible d’enregistrer cette page détaillée.']],503);
} catch (Throwable $e) {
    error_log('[miaradi-api] Circuit page save failed: '.$e->getMessage());
    circuit_pages_json(['error'=>['code'=>'save_failed','message'=>'Impossible d’enregistrer cette page détaillée.']],503);
}

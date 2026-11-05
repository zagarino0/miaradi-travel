<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$uploadDir = dirname(__DIR__, 2) . '/uploads/gallery';
$publicPrefix = '../uploads/gallery/';

function gallery_text(array $input, string $key, int $max, bool $required = false): ?string {
    return api_clean_text($input[$key] ?? null, $max, $required);
}
function gallery_external(string $url): array {
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || empty($parts['host'])) {
        api_json(['error'=>['code'=>'invalid_video_url','message'=>'Use a valid HTTPS YouTube or Vimeo URL.']], 422);
    }
    $host = strtolower($parts['host']);
    $path = trim((string)($parts['path'] ?? ''), '/');
    $id = null;
    if (in_array($host, ['youtube.com','www.youtube.com','m.youtube.com','youtu.be','www.youtu.be','youtube-nocookie.com','www.youtube-nocookie.com'], true)) {
        if ($host === 'youtu.be' || $host === 'www.youtu.be') $id = explode('/', $path)[0] ?? null;
        else { parse_str((string)($parts['query'] ?? ''), $query); $id = $query['v'] ?? null; }
        if (!$id && preg_match('~^(?:embed|shorts|live)/([A-Za-z0-9_-]{6,20})$~', $path, $m)) $id = $m[1];
        if (!is_string($id) || !preg_match('/^[A-Za-z0-9_-]{6,20}$/', $id)) {
            api_json(['error'=>['code'=>'invalid_video_url','message'=>'The YouTube URL does not contain a valid video ID.']], 422);
        }
        return ['source_type'=>'youtube','external_url'=>'https://www.youtube-nocookie.com/embed/'.$id,'thumbnail_path'=>'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg'];
    }
    if (in_array($host, ['vimeo.com','www.vimeo.com','player.vimeo.com'], true)) {
        if (preg_match('/(?:video\/)?([0-9]{5,15})$/', $path, $m)) $id = $m[1];
        if (!$id) api_json(['error'=>['code'=>'invalid_video_url','message'=>'The Vimeo URL does not contain a valid video ID.']], 422);
        return ['source_type'=>'vimeo','external_url'=>'https://player.vimeo.com/video/'.$id,'thumbnail_path'=>null];
    }
    api_json(['error'=>['code'=>'unsupported_video_host','message'=>'Only YouTube and Vimeo links are supported.']], 422);
}

if ($method === 'GET') {
    try {
        $rows = database_connection()->query('SELECT * FROM miaradi_gallery_items ORDER BY sort_order ASC, id DESC')->fetchAll();
        foreach ($rows as &$row) { $row['id']=(int)$row['id']; $row['is_published']=(bool)$row['is_published']; $row['sort_order']=(int)$row['sort_order']; }
        unset($row);
        api_json(['data'=>$rows]);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Admin gallery read failed: '.$e->getMessage());
        api_json(['error'=>['code'=>'gallery_unavailable','message'=>'Gallery items could not be loaded.']],503);
    }
}
if (!in_array($method, ['POST','PATCH','DELETE'], true)) {
    header('Allow: GET, POST, PATCH, DELETE');
    api_json(['error'=>['code'=>'method_not_allowed','message'=>'HTTP method not allowed.']],405);
}
api_require_csrf();

try {
    $pdo = database_connection();
    if ($method === 'DELETE') {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if (!$id) api_json(['error'=>['code'=>'invalid_id','message'=>'A valid media ID is required.']],422);
        $stmt=$pdo->prepare('SELECT file_path FROM miaradi_gallery_items WHERE id=:id');
        $stmt->execute(['id'=>$id]); $row=$stmt->fetch();
        if (!$row) api_json(['error'=>['code'=>'not_found','message'=>'Media item not found.']],404);
        $pdo->prepare('DELETE FROM miaradi_gallery_items WHERE id=:id')->execute(['id'=>$id]);
        if (!empty($row['file_path'])) {
            $base=realpath($uploadDir); $file=realpath(dirname(__DIR__,2).'/'.ltrim($row['file_path'],'/'));
            if ($base && $file && str_starts_with($file, $base.DIRECTORY_SEPARATOR) && is_file($file)) @unlink($file);
        }
        api_json(['data'=>['deleted'=>true]]);
    }

    $isUpload = $method === 'POST' && isset($_FILES['media']);
    $input = $isUpload ? $_POST : api_read_json();
    $id = null;
    if ($method === 'PATCH') {
        $id=filter_var($input['id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) api_json(['error'=>['code'=>'invalid_id','message'=>'A valid media ID is required.']],422);
    }
    $type = (string)($input['media_type'] ?? 'image');
    if (!in_array($type,['image','video'],true)) api_json(['error'=>['code'=>'validation_error','message'=>'Media type is invalid.']],422);
    $titleFr=gallery_text($input,'title_fr',200,true);
    $titleEn=gallery_text($input,'title_en',200);
    $descriptionFr=gallery_text($input,'description_fr',10000);
    $descriptionEn=gallery_text($input,'description_en',10000);
    $altText=gallery_text($input,'alt_text',250);
    $published = filter_var($input['is_published'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $sortOrder = filter_var($input['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    if ($published === null || $sortOrder === false || $sortOrder < -100000 || $sortOrder > 100000) api_json(['error'=>['code'=>'validation_error','message'=>'Publication or display order is invalid.']],422);

    $source='upload'; $filePath=null; $externalUrl=null; $thumbnail=null;
    if ($isUpload) {
        if ($_FILES['media']['error'] !== UPLOAD_ERR_OK) api_json(['error'=>['code'=>'upload_failed','message'=>'The file could not be uploaded. Check PHP upload limits.']],422);
        $tmp=$_FILES['media']['tmp_name']; $size=(int)$_FILES['media']['size'];
        if ($size < 1 || $size > ($type === 'image' ? 10*1024*1024 : 100*1024*1024)) api_json(['error'=>['code'=>'file_size_invalid','message'=>$type==='image'?'Images must be 10 MB or smaller.':'Videos must be 100 MB or smaller.']],422);
        $finfo=new finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($tmp);
        $allowedImages=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $allowedVideos=['video/mp4'=>'mp4','video/webm'=>'webm'];
        $allowed=$type==='image'?$allowedImages:$allowedVideos;
        if (!is_string($mime) || !isset($allowed[$mime])) api_json(['error'=>['code'=>'invalid_file_type','message'=>$type==='image'?'Use a JPG, PNG or WebP image.':'Use an MP4 or WebM video.']],422);
        if ($type==='image' && @getimagesize($tmp) === false) api_json(['error'=>['code'=>'invalid_image','message'=>'The uploaded image is invalid.']],422);
        if (!is_dir($uploadDir) && !mkdir($uploadDir,0755,true) && !is_dir($uploadDir)) throw new RuntimeException('Unable to create gallery upload directory.');
        $name=bin2hex(random_bytes(18)).'.'.$allowed[$mime];
        if (!move_uploaded_file($tmp,$uploadDir.'/'.$name)) throw new RuntimeException('Unable to store uploaded media.');
        $filePath='uploads/gallery/'.$name;
    } elseif ($type==='video') {
        $url=gallery_text($input,'external_url',1000,true);
        $external=gallery_external((string)$url); $source=$external['source_type']; $externalUrl=$external['external_url']; $thumbnail=$external['thumbnail_path'];
    } elseif ($method === 'PATCH') {
        $existing=$pdo->prepare('SELECT file_path, source_type, external_url, thumbnail_path FROM miaradi_gallery_items WHERE id=:id');
        $existing->execute(['id'=>$id]); $old=$existing->fetch();
        if (!$old) api_json(['error'=>['code'=>'not_found','message'=>'Media item not found.']],404);
        $source=$old['source_type']; $filePath=$old['file_path']; $externalUrl=$old['external_url']; $thumbnail=$old['thumbnail_path'];
    } else {
        api_json(['error'=>['code'=>'media_required','message'=>'Choose an image file or provide a video URL.']],422);
    }

    if ($method==='POST') {
        $stmt=$pdo->prepare('INSERT INTO miaradi_gallery_items (media_type,source_type,title_fr,title_en,description_fr,description_en,alt_text,file_path,external_url,thumbnail_path,is_published,sort_order) VALUES (:media_type,:source_type,:title_fr,:title_en,:description_fr,:description_en,:alt_text,:file_path,:external_url,:thumbnail_path,:published,:sort_order)');
    } else {
        $stmt=$pdo->prepare('UPDATE miaradi_gallery_items SET media_type=:media_type,source_type=:source_type,title_fr=:title_fr,title_en=:title_en,description_fr=:description_fr,description_en=:description_en,alt_text=:alt_text,file_path=COALESCE(:file_path,file_path),external_url=:external_url,thumbnail_path=:thumbnail_path,is_published=:published,sort_order=:sort_order WHERE id=:id');
    }
    $values=['media_type'=>$type,'source_type'=>$source,'title_fr'=>$titleFr,'title_en'=>$titleEn,'description_fr'=>$descriptionFr,'description_en'=>$descriptionEn,'alt_text'=>$altText,'file_path'=>$filePath,'external_url'=>$externalUrl,'thumbnail_path'=>$thumbnail,'published'=>$published?1:0,'sort_order'=>$sortOrder];
    if ($method==='PATCH') $values['id']=$id;
    $stmt->execute($values);
    $savedId=$method==='POST'?(int)$pdo->lastInsertId():(int)$id;
    api_json(['data'=>['id'=>$savedId,'media_type'=>$type,'source_type'=>$source,'is_published'=>$published]]);
} catch (Throwable $e) {
    error_log('[miaradi-api] Gallery mutation failed: '.$e->getMessage());
    api_json(['error'=>['code'=>'gallery_save_failed','message'=>'The media item could not be saved. Check the server upload configuration.']],503);
}

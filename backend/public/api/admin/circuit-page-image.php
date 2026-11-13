<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_method('POST');
api_require_role('super_admin', 'manager', 'editor');
api_require_csrf();

function circuit_image_json(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
    circuit_image_json(['error'=>['code'=>'image_required','message'=>'Choisissez une image à importer.']],422);
}
$file = $_FILES['image'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    circuit_image_json(['error'=>['code'=>'upload_failed','message'=>'L’import a échoué. Vérifiez les limites de téléversement PHP.']],422);
}
$tmp = (string)($file['tmp_name'] ?? '');
$size = (int)($file['size'] ?? 0);
if ($size < 1 || $size > 8 * 1024 * 1024) {
    circuit_image_json(['error'=>['code'=>'file_size_invalid','message'=>'La taille maximale autorisée est de 8 Mo.']],422);
}
if (!is_uploaded_file($tmp)) {
    circuit_image_json(['error'=>['code'=>'invalid_upload','message'=>'Le fichier envoyé n’est pas valide.']],422);
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp);
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if (!is_string($mime) || !isset($allowed[$mime]) || @getimagesize($tmp) === false) {
    circuit_image_json(['error'=>['code'=>'invalid_image','message'=>'Formats acceptés : JPG, PNG ou WebP.']],422);
}
$uploadDir = dirname(__DIR__, 2) . '/uploads/circuit-pages';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
    error_log('[miaradi-api] Unable to create circuit page upload directory.');
    circuit_image_json(['error'=>['code'=>'upload_storage_unavailable','message'=>'Le dossier de stockage des images ne peut pas être créé.']],503);
}
$name = bin2hex(random_bytes(18)) . '.' . $allowed[$mime];
if (!move_uploaded_file($tmp, $uploadDir . '/' . $name)) {
    error_log('[miaradi-api] Unable to store circuit page image.');
    circuit_image_json(['error'=>['code'=>'upload_storage_unavailable','message'=>'Impossible d’enregistrer l’image sur le serveur.']],503);
}
circuit_image_json(['data'=>['path'=>'uploads/circuit-pages/'.$name,'url'=>'../uploads/circuit-pages/'.$name]]);

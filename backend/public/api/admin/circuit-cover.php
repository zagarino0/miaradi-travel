<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_role('super_admin', 'manager', 'editor');
api_method('POST');
api_require_csrf();

if (!isset($_FILES['cover']) || !is_array($_FILES['cover'])) {
    api_json(['error' => ['code' => 'cover_required', 'message' => 'Sélectionnez une photo de couverture.']], 422);
}

$file = $_FILES['cover'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $message = ($file['error'] ?? null) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? null) === UPLOAD_ERR_FORM_SIZE
        ? 'La photo dépasse la taille autorisée par le serveur.'
        : 'Le téléversement de la photo a échoué.';
    api_json(['error' => ['code' => 'upload_failed', 'message' => $message]], 422);
}

$tmp = (string) ($file['tmp_name'] ?? '');
$size = (int) ($file['size'] ?? 0);
if ($size < 1 || $size > 10 * 1024 * 1024) {
    api_json(['error' => ['code' => 'file_size_invalid', 'message' => 'La photo doit peser 10 Mo maximum.']], 422);
}
if (!is_uploaded_file($tmp)) {
    api_json(['error' => ['code' => 'invalid_upload', 'message' => 'Le fichier reçu est invalide.']], 422);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp);
$extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!is_string($mime) || !isset($extensions[$mime]) || @getimagesize($tmp) === false) {
    api_json(['error' => ['code' => 'invalid_file_type', 'message' => 'Formats acceptés : JPG, PNG et WebP.']], 422);
}

$uploadDir = dirname(__DIR__, 2) . '/uploads/circuit-covers';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
    throw new RuntimeException('Impossible de créer le dossier des couvertures de circuits.');
}

$name = bin2hex(random_bytes(18)) . '.' . $extensions[$mime];
if (!move_uploaded_file($tmp, $uploadDir . '/' . $name)) {
    throw new RuntimeException('Impossible d’enregistrer la photo de couverture.');
}

api_json(['data' => [
    'image_path' => 'backend/public/uploads/circuit-covers/' . $name,
    'filename' => $name,
]], 201);

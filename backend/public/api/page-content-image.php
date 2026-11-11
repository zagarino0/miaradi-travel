<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/http.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();
api_method('POST');
api_require_csrf();
$slot = (string)($_POST['slot'] ?? '');
if (!in_array($slot, ['logo', 'hero1', 'hero2', 'hero3', 'hero4', 'partnerFeature', 'partnerGuide', 'partnerLodging', 'partnerCrafts', 'feature', 'guide', 'lodging', 'crafts'], true)) {
    api_json(['error' => ['code' => 'invalid_image_slot', 'message' => 'Emplacement d’image invalide.']], 422);
}
$file = $_FILES['image'] ?? null;
if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    api_json(['error' => ['code' => 'image_upload_failed', 'message' => 'Sélectionnez une image valide.']], 422);
}
if ((int)($file['size'] ?? 0) < 1 || (int)$file['size'] > 8 * 1024 * 1024) {
    api_json(['error' => ['code' => 'image_too_large', 'message' => 'L’image doit peser 8 Mo maximum.']], 422);
}
$tmp = (string)($file['tmp_name'] ?? '');
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
$extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!isset($extensions[$mime]) || !is_uploaded_file($tmp)) {
    api_json(['error' => ['code' => 'unsupported_image_type', 'message' => 'Formats acceptés : JPG, PNG et WebP.']], 422);
}
$directory = dirname(__DIR__) . '/uploads/homepage-media';
if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
    api_json(['error' => ['code' => 'upload_directory_unavailable', 'message' => 'Le dossier de téléversement est indisponible.']], 500);
}
$name = $slot . '-' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
if (!move_uploaded_file($tmp, $directory . '/' . $name)) {
    api_json(['error' => ['code' => 'image_save_failed', 'message' => 'Impossible d’enregistrer cette image.']], 500);
}
api_json(['data' => ['slot' => $slot, 'path' => 'backend/public/uploads/homepage-media/' . $name]]);

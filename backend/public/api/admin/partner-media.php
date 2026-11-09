<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/http.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_admin();

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$allowedSlots = ['feature','guide','lodging','crafts'];
$uploadDir = dirname(__DIR__, 2) . '/uploads/partners';
$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$publicBase = preg_replace('~/api(?:/admin)?/[^/]+$~', '', $scriptName) ?: '';
function partner_media_url(?string $path, string $base): ?string {
    if (!$path) return null;
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
if ($method === 'GET') {
    try {
        $rows = database_connection()->query('SELECT slot_key,title_fr,description_fr,alt_text,image_path,sort_order,updated_at FROM miaradi_partner_media ORDER BY sort_order ASC')->fetchAll();
        foreach ($rows as &$row) {
            $row['sort_order'] = (int) $row['sort_order'];
            $row['image_url'] = partner_media_url($row['image_path'], $publicBase);
        }
        unset($row);
        api_json(['data' => $rows]);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Partner media admin read failed: ' . $e->getMessage());
        api_json(['error' => ['code' => 'partner_media_unavailable', 'message' => 'Les images du réseau local sont indisponibles. Vérifiez que la migration 005 a été appliquée.']], 503);
    }
}
if ($method !== 'POST') {
    header('Allow: GET, POST');
    api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'Méthode HTTP non autorisée.']], 405);
}
api_require_csrf();
try {
    $slot = trim((string) ($_POST['slot_key'] ?? ''));
    if (!in_array($slot, $allowedSlots, true)) api_json(['error'=>['code'=>'invalid_slot','message'=>'Emplacement d’image invalide.']],422);
    $title = api_clean_text($_POST['title_fr'] ?? null, 120, true);
    $description = api_clean_text($_POST['description_fr'] ?? null, 500);
    $alt = api_clean_text($_POST['alt_text'] ?? null, 250, true);
    $pdo = database_connection();
    $existingStmt = $pdo->prepare('SELECT image_path FROM miaradi_partner_media WHERE slot_key=:slot');
    $existingStmt->execute(['slot'=>$slot]);
    $existing = $existingStmt->fetch();
    if (!$existing) api_json(['error'=>['code'=>'slot_not_found','message'=>'Emplacement introuvable. Appliquez la migration 005.']],404);
    $newPath = null;
    if (isset($_FILES['image']) && (int)$_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        if ((int)$file['error'] !== UPLOAD_ERR_OK) api_json(['error'=>['code'=>'upload_failed','message'=>'Le téléversement a échoué. Vérifiez les limites PHP upload_max_filesize et post_max_size.']],422);
        $size = (int)$file['size'];
        if ($size < 1 || $size > 8 * 1024 * 1024) api_json(['error'=>['code'=>'file_size_invalid','message'=>'L’image doit peser 8 Mo maximum.']],422);
        $tmp = (string)$file['tmp_name'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $types = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!is_string($mime) || !isset($types[$mime]) || @getimagesize($tmp) === false) api_json(['error'=>['code'=>'invalid_image','message'=>'Formats acceptés : JPG, PNG et WebP.']],422);
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) throw new RuntimeException('Unable to create partner image directory.');
        $filename = $slot . '-' . bin2hex(random_bytes(12)) . '.' . $types[$mime];
        if (!move_uploaded_file($tmp, $uploadDir . '/' . $filename)) throw new RuntimeException('Unable to store partner image.');
        $newPath = 'uploads/partners/' . $filename;
    }
    $imagePath = $newPath ?? ($existing['image_path'] ?? null);
    $stmt = $pdo->prepare('UPDATE miaradi_partner_media SET title_fr=:title,description_fr=:description,alt_text=:alt,image_path=:image_path WHERE slot_key=:slot');
    $stmt->execute(['title'=>$title,'description'=>$description,'alt'=>$alt,'image_path'=>$imagePath,'slot'=>$slot]);
    if ($newPath && !empty($existing['image_path'])) {
        $old = realpath(dirname(__DIR__, 2) . '/' . ltrim((string)$existing['image_path'], '/'));
        $base = realpath($uploadDir);
        if ($old && $base && str_starts_with($old, $base . DIRECTORY_SEPARATOR) && is_file($old)) @unlink($old);
    }
    api_json(['data'=>['slot_key'=>$slot,'title_fr'=>$title,'description_fr'=>$description,'alt_text'=>$alt,'image_path'=>$imagePath,'image_url'=>partner_media_url($imagePath,$publicBase)]]);
} catch (Throwable $e) {
    error_log('[miaradi-api] Partner media save failed: ' . $e->getMessage());
    api_json(['error'=>['code'=>'partner_media_save_failed','message'=>'Impossible d’enregistrer cette image. Vérifiez les permissions du dossier uploads/partners.']],503);
}

<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/http.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$defaults = [
  'eyebrow' => "UNE AVENTURE HUMAINE",
  'title' => "Derrière chaque voyage, des rencontres.",
  'paragraph1' => "Notre connaissance de Madagascar s’enrichit des liens tissés avec les acteurs locaux : guides, hébergeurs, artisans, transporteurs et initiatives de terrain.",
  'paragraph2' => "Ces collaborations contribuent à créer des expériences plus humaines, adaptées aux réalités locales et attentives aux personnes qui accueillent les voyageurs.",
  'linkText' => "Découvrir notre réseau local",
  'caption' => "Découvrir Madagascar avec celles et ceux qui le connaissent.",
  'guideTitle' => "Guides locaux",
  'guideText' => "Partager les richesses du territoire.",
  'lodgingTitle' => "Hébergements",
  'lodgingText' => "Des étapes choisies avec soin.",
  'craftsTitle' => "Artisans & savoir-faire",
  'craftsText' => "Découvrir les traditions vivantes."
];
if ($method === 'GET') {
    try {
        $stmt = database_connection()->prepare("SELECT content_json FROM miaradi_page_content WHERE content_key = 'homepage_local_partners' LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        $data = $row ? json_decode((string)$row['content_json'], true) : $defaults;
        if (!is_array($data)) $data = $defaults;
        api_json(['data' => array_merge($defaults, $data)]);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Page content read failed: '.$e->getMessage());
        api_json(['data' => $defaults, 'warning' => 'migration_required']);
    }
}
api_require_admin();
api_method('PUT');
api_require_csrf();
$input = api_read_json();
$limits = ['eyebrow'=>100,'title'=>240,'paragraph1'=>1200,'paragraph2'=>1200,'linkText'=>160,'caption'=>300,'guideTitle'=>120,'guideText'=>500,'lodgingTitle'=>120,'lodgingText'=>500,'craftsTitle'=>120,'craftsText'=>500];
$data = [];
foreach ($limits as $key => $max) {
    $value = api_clean_text($input[$key] ?? null, $max, true);
    $data[$key] = $value;
}
try {
    $stmt = database_connection()->prepare("INSERT INTO miaradi_page_content (content_key, content_json) VALUES ('homepage_local_partners', :content) ON DUPLICATE KEY UPDATE content_json = VALUES(content_json)");
    $stmt->execute(['content' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
    api_json(['data' => $data, 'message' => 'Contenu enregistré.']);
} catch (Throwable $e) {
    error_log('[miaradi-api] Page content save failed: '.$e->getMessage());
    api_json(['error'=>['code'=>'content_save_failed','message'=>'Impossible d’enregistrer le contenu. Vérifiez que la migration est appliquée.']], 503);
}

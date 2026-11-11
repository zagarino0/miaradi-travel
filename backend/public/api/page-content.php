<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/http.php';
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
  'craftsText' => "Découvrir les traditions vivantes.",
  'featureImage' => "https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=1400&q=85",
  'guideImage' => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=85",
  'lodgingImage' => "https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?auto=format&fit=crop&w=900&q=85",
  'craftsImage' => "https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85",
  'featureAlt' => "Paysage naturel de Madagascar",
  'guideAlt' => "Guide local à Madagascar",
  'lodgingAlt' => "Hébergement au cœur de la nature malgache",
  'craftsAlt' => "Artisanat malgache traditionnel"
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
$limits = ['eyebrow'=>100,'title'=>240,'paragraph1'=>1200,'paragraph2'=>1200,'linkText'=>160,'caption'=>300,'guideTitle'=>120,'guideText'=>500,'lodgingTitle'=>120,'lodgingText'=>500,'craftsTitle'=>120,'craftsText'=>500,'featureImage'=>1000,'guideImage'=>1000,'lodgingImage'=>1000,'craftsImage'=>1000,'featureAlt'=>250,'guideAlt'=>250,'lodgingAlt'=>250,'craftsAlt'=>250];
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

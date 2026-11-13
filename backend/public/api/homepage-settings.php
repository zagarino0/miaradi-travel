<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/http.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$defaults = json_decode('{"navCircuits":"Circuits","navAbout":"Esprit MIARADI","navGallery":"Galerie","navContact":"Contact","navQuote":"Demander un devis","heroLocation":"Voyage à Madagascar","heroTitle":"Imaginons votre voyage","heroTitleLine2":"à Madagascar","heroCopy":"Des circuits authentiques, des rencontres uniques et des paysages d’exception.","heroQuote":"Demander un devis","heroCircuits":"Découvrir nos circuits","finderRegion":"Région","finderRegionAll":"Toutes les régions","finderTheme":"Thématique","finderThemeAll":"Toutes les thématiques","finderDuration":"Durée","finderDurationAll":"Indifférente","finderSubmit":"Rechercher un circuit","durationCustom":"Sur mesure","duration7to12":"7–12 jours","duration5to8":"5–8 jours","duration3to6":"3–6 jours","duration3to5":"3–5 jours","duration2to4":"2–4 jours","finderThemeCulture":"Culture","finderThemeCoast":"Côte & îles","finderThemeNature":"Nature","finderRegionHighlands":"Hautes Terres","finderRegionSouth":"Sud","finderRegionEast":"Est","finderRegionNorth":"Nord & îles","finderRegionWest":"Ouest","trust1Title":"100% Madagascar","trust1Text":"Des expériences locales et authentiques","trust2Title":"Tourisme responsable","trust2Text":"En collaboration avec les communautés","trust3Title":"Accompagnement dédié","trust3Text":"De la préparation à votre retour","trust4Title":"Voyage sur mesure","trust4Text":"Des circuits adaptés à vos envies","circuitsEyebrow":"NOS CIRCUITS","circuitsTitle":"Nos circuits phares","circuitsAll":"Voir tous les circuits","filterThemeLabel":"THÉMATIQUE","filterAll":"Tout voir","filterNature":"Nature","filterCoast":"Côte & îles","filterCulture":"Culture","filterRegionLabel":"RÉGION","filterDurationLabel":"DURÉE","introEyebrow":"L’ESPRIT MIARADI","introTitle":"Voyager","introTitleAccent":"autrement.","introFeatureEyebrow":"NOUVEAU VOYAGE SIGNATURE","introFeatureTitle":"Nord – Nord-Ouest :","introFeatureAccent":"au fil du fleuve Sofia","introFeatureText":"Explorez les trésors du Boeny entre patrimoine maritime, villages côtiers et Tsingy de Namoroka. Une aventure de 9 jours entre mer, culture et nature préservée.","introFeatureCta":"Découvrir le circuit","introFeatureImage":"https://commons.wikimedia.org/wiki/Special:FilePath/Walking_the_Avenue_of_the_Baobabs.jpg?width=2200","introFeatureImageAlt":"Allée des baobabs de Madagascar — arrière-plan du voyage signature","partnersEyebrow":"UNE AVENTURE HUMAINE","partnersTitle":"Derrière chaque voyage,","partnersAccent":"des rencontres.","partnersParagraph1":"Notre connaissance de Madagascar s’enrichit des liens tissés avec les acteurs locaux : guides, hébergeurs, artisans, transporteurs et initiatives de terrain.","partnersParagraph2":"Ces collaborations contribuent à créer des expériences plus humaines, adaptées aux réalités locales et attentives aux personnes qui accueillent les voyageurs.","partnersLink":"Découvrir notre réseau local","partnersCaption":"Découvrir Madagascar avec celles et ceux qui le connaissent.","guideTitle":"Guides locaux","guideText":"Partager les richesses du territoire.","lodgingTitle":"Hébergements","lodgingText":"Des étapes choisies avec soin.","craftsTitle":"Artisans & savoir-faire","craftsText":"Découvrir les traditions vivantes.","galleryEyebrow":"MADAGASCAR EN IMAGES","galleryTitle":"Laissez-vous","galleryAccent":"inspirer.","galleryText":"Partez à la rencontre de Madagascar, de ses paysages grandioses et de ses trésors cachés. Explorez chaque région à travers une sélection de photographies qui racontent l’âme de la Grande Île.","contactEyebrow":"VOTRE PROJET, NOTRE EXPERTISE","contactTitle":"Le plus beau voyage est celui qui vous","contactAccent":"ressemble.","contactText":"Vous rêvez d’aventure, de nature, de découvertes culturelles ou de quelques jours au bord de l’eau ? Nous construisons avec vous un itinéraire adapté à vos envies, à votre budget et au temps dont vous disposez.","stepsTitle":"Trois étapes pour imaginer votre voyage","step1Title":"Vous nous racontez vos envies","step1Text":"Vos destinations rêvées, vos centres d’intérêt, vos dates et votre manière de voyager.","step2Title":"Nous imaginons votre itinéraire","step2Text":"Nous étudions les étapes, les activités, les déplacements et les hébergements adaptés à votre projet.","step3Title":"Nous affinons ensemble votre voyage","step3Text":"Nous échangeons sur la proposition pour construire un programme cohérent avec vos attentes.","quoteEyebrow":"IMAGINONS VOTRE VOYAGE","quoteTitle":"Quel voyage vous attire ?","quoteIntro":"Choisissez une première inspiration. Nous préciserons les détails ensemble.","interestNature":"Nature & randonnée","interestBeach":"Plages & îles","interestCulture":"Culture & rencontres","interestDiscovery":"Circuit découverte","interestNote":"Ces choix sont indicatifs : ils pourront être précisés dans votre demande.","quoteStepEyebrow":"LE PREMIER PAS","quoteStepTitle":"Parlons de votre voyage.","quoteStepText":"Racontez-nous votre projet pour recevoir un devis personnalisé.","fieldName":"Nom complet *","fieldEmail":"Adresse e-mail *","fieldDate":"Date de départ souhaitée","fieldPeople":"Nombre de voyageurs","fieldCircuit":"Circuit qui vous intéresse","fieldMessage":"Parlez-nous de votre projet *","fieldNamePlaceholder":"Votre nom","fieldEmailPlaceholder":"vous@exemple.com","fieldMessagePlaceholder":"Vos envies, la durée du séjour, les lieux rêvés…","privacyText":"J’accepte que MIARADI-TRAVEL utilise ces informations pour répondre à ma demande de devis.","quoteSubmit":"Préparer ma demande","privacyLinkLabel":"Politique de confidentialité","circuitCustomOption":"Je souhaite un voyage sur mesure","people10":"10 personnes","people9":"9 personnes","people8":"8 personnes","people7":"7 personnes","people6":"6 personnes","people5":"5 personnes","people4":"4 personnes","people3":"3 personnes","people2":"2 personnes","people1":"1 personne","footerBrandText":"Madagascar se découvre mieux quand le voyage a du sens.","footerExploreTitle":"Explorer","footerAbout":"Esprit MIARADI","footerCircuits":"Nos circuits","footerTravelBook":"Carnet de voyage","footerContact":"Contact","footerPrepareTitle":"Préparer son voyage","footerCustomTrip":"Voyage sur mesure","footerQuote":"Demande de devis","footerFaq":"Questions fréquentes","footerLanguage":"Français / English","footerLegal":"Mentions légales · Politique de confidentialité","footerBackTop":"Retour en haut ↑","metaDescription":"Découvrez Madagascar avec MIARADI-TRAVEL : voyages sur mesure, rencontres locales et circuits authentiques. Demandez votre devis personnalisé.","metaTitle":"MIARADI-TRAVEL — Madagascar, autrement","logo":"assets/miaradi-logo.png","logoAlt":"Logo MIARADI — Tour Operator Madagascar","hero1":"https://commons.wikimedia.org/wiki/Special:FilePath/Walking_the_Avenue_of_the_Baobabs.jpg?width=2200","hero1Alt":"Panorama de l’Allée des Baobabs à Madagascar","hero2":"https://commons.wikimedia.org/wiki/Special:FilePath/Tsingy_de_Bemaraha.jpg?width=2200","hero2Alt":"Formations rocheuses des Tsingy de Bemaraha","hero3":"https://commons.wikimedia.org/wiki/Special:FilePath/Andasibe_Mantadia_National_Park.jpg?width=2200","hero3Alt":"Forêt tropicale de Madagascar","hero4":"https://commons.wikimedia.org/wiki/Special:FilePath/Nosy_Be_Madagascar.jpg?width=2200","hero4Alt":"Lagon de Nosy Be","partnerFeature":"https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=1400&q=85","partnerFeatureAlt":"Paysage naturel de Madagascar","partnerGuide":"https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=85","partnerGuideAlt":"Guide local à Madagascar","partnerLodging":"https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?auto=format&fit=crop&w=900&q=85","partnerLodgingAlt":"Hébergement à Madagascar","partnerCrafts":"https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85","partnerCraftsAlt":"Artisanat malgache traditionnel"}', true, 512, JSON_THROW_ON_ERROR);
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method === 'GET') {
    try {
        $stmt = database_connection()->prepare("SELECT content_json FROM miaradi_page_content WHERE content_key = 'homepage_full' LIMIT 1");
        $stmt->execute(); $row = $stmt->fetch();
        $saved = $row ? json_decode((string)$row['content_json'], true) : [];
        if (!is_array($saved)) $saved = [];
        $data = array_merge($defaults, $saved);
        // Keep any content already managed by the previous homepage editor.
        if (!$row) {
            try {
                $legacyStmt = database_connection()->query("SELECT content_json FROM miaradi_page_content WHERE content_key = 'homepage_local_partners' LIMIT 1");
                $legacyRow = $legacyStmt->fetch();
                $legacy = $legacyRow ? json_decode((string)$legacyRow['content_json'], true) : [];
                if (is_array($legacy)) {
                    foreach (['eyebrow'=>'partnersEyebrow','paragraph1'=>'partnersParagraph1','paragraph2'=>'partnersParagraph2','linkText'=>'partnersLink','caption'=>'partnersCaption','guideTitle'=>'guideTitle','guideText'=>'guideText','lodgingTitle'=>'lodgingTitle','lodgingText'=>'lodgingText','craftsTitle'=>'craftsTitle','craftsText'=>'craftsText','featureAlt'=>'partnerFeatureAlt','guideAlt'=>'partnerGuideAlt','lodgingAlt'=>'partnerLodgingAlt','craftsAlt'=>'partnerCraftsAlt'] as $from=>$to) {
                        if (isset($legacy[$from]) && is_string($legacy[$from]) && $legacy[$from] !== '') $data[$to] = $legacy[$from];
                    }
                    if (!empty($legacy['title']) && is_string($legacy['title'])) {
                        $parts = preg_split('/,\\s*/u', $legacy['title'], 2);
                        $data['partnersTitle'] = trim((string)($parts[0] ?? $legacy['title']));
                        if (isset($parts[1])) $data['partnersAccent'] = trim($parts[1]);
                    }
                    foreach (['featureImage'=>'partnerFeature','guideImage'=>'partnerGuide','lodgingImage'=>'partnerLodging','craftsImage'=>'partnerCrafts'] as $from=>$to) {
                        if (!empty($legacy[$from]) && is_string($legacy[$from])) $data[$to] = $legacy[$from];
                    }
                }
            } catch (Throwable $legacyError) {
                error_log('[miaradi-api] Legacy homepage content migration skipped: '.$legacyError->getMessage());
            }
            // Preserve existing images and card copy from the legacy partner-media manager.
            try {
                $rows = database_connection()->query('SELECT slot_key,title_fr,description_fr,alt_text,image_path FROM miaradi_partner_media ORDER BY sort_order ASC')->fetchAll();
                foreach ($rows as $media) {
                    $slot = (string)$media['slot_key'];
                    $map = ['feature'=>['partnerFeature','partnersCaption','partnerFeatureAlt'],'guide'=>['partnerGuide','guideTitle','guideAlt'],'lodging'=>['partnerLodging','lodgingTitle','partnerLodgingAlt'],'crafts'=>['partnerCrafts','craftsTitle','partnerCraftsAlt']];
                    if (!isset($map[$slot])) continue;
                    [$imageKey,$titleKey,$altKey] = $map[$slot];
                    if (!empty($media['image_path'])) $data[$imageKey] = 'backend/public/'.ltrim((string)$media['image_path'], '/');
                    if (!empty($media['alt_text'])) $data[$altKey] = (string)$media['alt_text'];
                    if ($slot !== 'feature' && !empty($media['title_fr'])) $data[$titleKey] = (string)$media['title_fr'];
                    if ($slot === 'feature' && !empty($media['description_fr'])) $data['partnersCaption'] = (string)$media['description_fr'];
                    if ($slot !== 'feature') {
                        $textKey = $slot === 'guide' ? 'guideText' : ($slot === 'lodging' ? 'lodgingText' : 'craftsText');
                        if (isset($media['description_fr']) && $media['description_fr'] !== null) $data[$textKey] = (string)$media['description_fr'];
                    }
                }
            } catch (Throwable $mediaError) {
                error_log('[miaradi-api] Legacy partner media migration skipped: '.$mediaError->getMessage());
            }
        }
        api_json(['data' => $data]);
    } catch (Throwable $e) {
        error_log('[miaradi-api] Homepage settings read failed: '.$e->getMessage());
        api_json(['data' => $defaults, 'warning' => 'migration_required']);
    }
}
api_require_admin();
api_method('PUT');
api_require_csrf();
$input = api_read_json();
$imageKeys = ["logo","hero1","hero2","hero3","hero4","introFeatureImage","partnerFeature","partnerGuide","partnerLodging","partnerCrafts"];

try {
    // Fusionner les champs reçus avec les valeurs enregistrées : les autres champs restent inchangés.
    $stmt = database_connection()->prepare("SELECT content_json FROM miaradi_page_content WHERE content_key = 'homepage_full' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $saved = $row ? json_decode((string)$row['content_json'], true) : [];
    if (!is_array($saved)) {
        $saved = [];
    }
    $data = array_merge($defaults, $saved);

    foreach ($input as $key => $rawValue) {
        if (!array_key_exists($key, $defaults)) {
            continue;
        }
        $max = str_ends_with($key, 'Alt') ? 250 : (in_array($key, $imageKeys, true) ? 1000 : 2000);
        $value = is_string($rawValue) ? trim($rawValue) : (string)$defaults[$key];

        if (mb_strlen($value, 'UTF-8') > $max) {
            api_json(['error' => ['code' => 'field_too_long', 'message' => 'Un champ dépasse la longueur autorisée : '.$key]], 422);
        }
        if (in_array($key, $imageKeys, true) && $value !== '' && !preg_match('~^(https?://|assets/|backend/public/uploads/)~i', $value)) {
            api_json(['error' => ['code' => 'invalid_image_path', 'message' => 'Chemin d’image invalide : '.$key]], 422);
        }
        $data[$key] = $value;
    }

    if (!$input) {
        api_json(['error' => ['code' => 'empty_update', 'message' => 'Aucune modification à enregistrer.']], 422);
    }

    $stmt = database_connection()->prepare("INSERT INTO miaradi_page_content (content_key, content_json) VALUES ('homepage_full', :content) ON DUPLICATE KEY UPDATE content_json = VALUES(content_json)");
    $stmt->execute(['content' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
    api_json(['data' => $data, 'message' => 'Modifications enregistrées.']);
} catch (Throwable $e) {
    error_log('[miaradi-api] Homepage settings save failed: '.$e->getMessage());
    api_json(['error' => ['code' => 'homepage_save_failed', 'message' => 'Impossible d’enregistrer le contenu. Vérifiez la connexion à la base de données.']], 503);
}

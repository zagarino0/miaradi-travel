<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';
function page_escape(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function page_image_src(mixed $value): string { $path=trim((string)$value); if ($path==='' || preg_match('~^(https?://|//|/)~i',$path)) return $path; if (str_starts_with($path,'assets/')) return '../../'.$path; return $path; }
$slug = trim((string)($_GET['slug'] ?? ''));
if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) { http_response_code(404); $slug=''; }
$page=null;
if ($slug !== '') {
    try {
        $stmt=database_connection()->prepare('SELECT c.slug,c.title_fr,c.title_en,c.summary_fr,c.summary_en,c.region,c.duration_min,c.duration_max,p.hero_title_fr,p.hero_title_en,p.intro_fr,p.intro_en,p.hero_image,p.itinerary_json FROM miaradi_circuit_pages p INNER JOIN miaradi_circuits c ON c.id=p.circuit_id WHERE c.slug=:slug AND c.is_published=1 AND p.is_published=1 LIMIT 1');
        $stmt->execute(['slug'=>$slug]); $page=$stmt->fetch();
    } catch (Throwable $e) { error_log('[miaradi] Public circuit detail unavailable: '.$e->getMessage()); }
}
if (!$page) { http_response_code(404); }
$days = $page ? json_decode((string)$page['itinerary_json'], true) : [];
if (!is_array($days)) $days=[];

$title = $page ? ((string)($page['hero_title_fr'] ?: $page['title_fr'])) : 'Circuit introuvable';
$intro = $page ? ((string)($page['intro_fr'] ?: $page['summary_fr'])) : 'Cette page de circuit est introuvable ou n’est pas encore publiée.';
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="<?=page_escape($intro)?>"><title><?=page_escape($title)?> — MIARADI-TRAVEL</title>
<style>
:root{--red:#922D20;--paper:#FBF8F5;--ink:#24211f;--muted:#746d68;--line:#ded7d1;--peach:#eab5a3}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.7 "DM Sans",Inter,system-ui,sans-serif}.nav{height:82px;background:#fffaf8;border-bottom:1px solid #eee5e0;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(20px,5vw,82px);gap:20px}.nav-brand{display:inline-flex;align-items:center;gap:3px;flex-shrink:0}.logo{width:125px;max-height:55px;object-fit:contain}.nav-brand-travel{color:var(--red);font-size:14px;font-weight:800;letter-spacing:.035em;white-space:nowrap}.nav-links{display:flex;align-items:center;gap:clamp(15px,3vw,38px)}.nav a{color:var(--ink);text-decoration:none;font-size:14px;font-weight:650}.cta{background:var(--red)!important;color:#fff!important;padding:13px 21px;border-radius:30px}.hero{min-height:590px;display:flex;align-items:center;position:relative;background:#30251f;color:#fff;overflow:hidden}.hero-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.hero:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(20,16,15,.78),rgba(20,16,15,.2)),linear-gradient(0deg,rgba(20,16,15,.34),transparent 65%)}.hero-content{position:relative;z-index:1;max-width:1200px;width:100%;margin:auto;padding:80px 7vw}.eyebrow{font-size:12px;text-transform:uppercase;letter-spacing:.16em;color:#f1c6b8;font-weight:800}.hero h1{font-size:clamp(46px,7vw,94px);line-height:.99;letter-spacing:-.055em;max-width:1000px;margin:20px 0}.hero h1 em{font-family:Georgia,serif;font-weight:400;color:var(--peach)}.hero p{font-size:clamp(17px,1.7vw,22px);max-width:700px;color:#f4eeeb}.chips{display:flex;gap:9px;flex-wrap:wrap;margin:22px 0}.chip{border:1px solid #ffffff80;border-radius:30px;padding:7px 14px;font-size:13px}.intro{max-width:1200px;margin:auto;padding:65px 6vw 30px;font-size:18px;color:#655d58}.intro p{max-width:900px}
.map-section{max-width:1200px;margin:0 auto;padding:18px 6vw 42px}
.map-section-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:18px}
.map-section-head h2{font-size:clamp(27px,3.5vw,42px);line-height:1.1;letter-spacing:-.04em;margin:0;color:#18253a}
.map-section-head p{margin:8px 0 0;color:var(--muted);max-width:620px}
.circuit-map-card{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(220px,.7fr);background:#fff;border:1px solid #eadfd8;border-radius:16px;overflow:hidden;box-shadow:0 12px 34px #34251c0d}
.circuit-map{height:360px;min-width:0;background:#e7f0ef}
.circuit-map-info{padding:26px;display:flex;flex-direction:column;justify-content:center;gap:12px}
.circuit-map-kicker{font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--red);font-weight:800}
.circuit-map-info h3{margin:0;font-size:25px;line-height:1.2;color:#18253a}
.circuit-map-info p{margin:0;color:var(--muted);font-size:14px}
.map-legend{display:flex;align-items:center;gap:9px;font-size:13px;font-weight:700;color:#514640}
.map-legend-dot{width:12px;height:12px;border-radius:50%;background:var(--red);box-shadow:0 0 0 5px #922d2020;flex-shrink:0}
@media(max-width:700px){.map-section{padding:14px 20px 30px}.map-section-head{display:block}.circuit-map-card{grid-template-columns:1fr}.circuit-map{height:280px}.circuit-map-info{padding:20px}.circuit-map-info h3{font-size:22px}}
.itinerary{max-width:1400px;margin:0 auto;padding:30px 4vw 90px}.section-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:28px}.section-head h2{font-size:clamp(30px,4vw,54px);line-height:1.05;letter-spacing:-.045em;margin:0}.section-head p{color:var(--muted);max-width:390px;margin:0}.day{position:relative;border-top:1px solid var(--line);padding:22px 0 34px 78px}.day:last-child{border-bottom:1px solid var(--line)}.day::before{content:"";position:absolute;left:27px;top:0;bottom:0;width:2px;background:#dfc5bb;z-index:0}.day-heading{position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,1fr);gap:8px;padding:0 0 18px;min-height:76px;align-content:center}.day-num{position:absolute;left:-78px;top:0;display:grid;place-items:center;width:48px;height:48px;border:1px solid #d8b2a6;border-radius:50%;color:#fff;font-size:14px;font-weight:800;background:var(--red);z-index:2;box-shadow:0 0 0 5px var(--paper)}.day-title{font-size:clamp(20px,2vw,27px);font-weight:750;line-height:1.3;color:#18253a}.day-subtitle{font-size:15px;color:var(--muted)}.day-content{display:grid;grid-template-columns:minmax(0,1.12fr) minmax(0,.88fr);gap:clamp(28px,4vw,64px);align-items:center;padding:0 0 8px;animation:day-reveal .25s ease-out}.day .gallery{grid-column:1;grid-row:1}.day .day-copy{grid-column:2;grid-row:1}.day.image-right .gallery{grid-column:2}.day.image-right .day-copy{grid-column:1}.day-copy{color:#625b56;font-size:clamp(16px,1.3vw,19px);line-height:1.85}.day-copy p:first-child{margin-top:0}.day-copy p{max-width:58ch}.day-copy .overnight{padding:12px 15px;border-left:3px solid var(--red);background:#f4ece6;font-size:14px}.gallery{min-width:0;width:100%}@keyframes day-reveal{from{opacity:.45;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}.overnight{font-size:14px}.gallery{min-width:0}.gallery-main{position:relative;width:100%;aspect-ratio:1.42/1;min-height:320px;max-height:520px;overflow:hidden;background:#eee3dc;border-radius:5px}.gallery-main img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity .35s}.gallery-main img.active{opacity:1}.gallery-arrow{position:absolute;z-index:1;top:50%;transform:translateY(-50%);width:44px;height:44px;border:1px solid #ffffffa8;border-radius:50%;background:#201c1bd9;color:#fff;font-size:25px;cursor:pointer}.gallery-arrow.prev{left:14px}.gallery-arrow.next{right:14px}.gallery-arrow:disabled{opacity:.45;cursor:default}.gallery-count{position:absolute;right:14px;top:14px;border-radius:20px;background:#201c1bd9;color:#fff;padding:5px 11px;font-size:12px}.gallery-caption{background:#f3e7e1;padding:10px 13px;color:#786c67;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.no-image{display:grid;place-items:center;height:100%;color:#9b8e85}.bottom-cta{background:#eee4dd;padding:55px 6vw;text-align:center}.bottom-cta h2{font-size:clamp(30px,4vw,48px);margin:0 0 12px}.bottom-cta p{color:var(--muted)}.bottom-cta a{display:inline-block;margin-top:15px;background:var(--red);color:white;padding:13px 24px;border-radius:30px;text-decoration:none;font-weight:700}.footer{padding:28px 6vw;background:#292320;color:#f7eee9;display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap;font-size:12px}.not-found{min-height:70vh;display:grid;place-content:center;text-align:center;padding:30px}.not-found a{color:var(--red)}@media(max-width:760px){.nav{height:68px}.logo{width:100px}.nav-links a:not(.cta){display:none}.hero{min-height:560px}.hero-content{padding:65px 24px}.day::before{left:17px}.day{padding:20px 0 28px 50px}.day-heading{padding-bottom:16px}.day-num{left:-50px;width:36px;height:36px;font-size:12px}.day-title{font-size:19px}.day-subtitle{font-size:13px}.day-content{grid-template-columns:1fr;gap:18px}.day .gallery,.day.image-right .gallery{grid-column:1;grid-row:1}.day .day-copy,.day.image-right .day-copy{grid-column:1;grid-row:2}.gallery-main{aspect-ratio:1.3/1;min-height:240px;max-height:420px}.section-head{display:block}.section-head p{margin-top:12px}.intro{padding:42px 24px 20px}.itinerary{padding-left:22px;padding-right:22px}}@media(max-width:420px){.day::before{left:13px}.day{padding-left:38px}.day-num{left:-38px;width:28px;height:28px;font-size:10px}.day-title{font-size:17px}.gallery-main{min-height:210px}.hero h1{font-size:clamp(40px,12vw,58px)}.gallery-arrow{width:40px;height:40px}.itinerary{padding-left:17px;padding-right:17px}}@media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;animation:none!important;transition:none!important}}

/* Footer fluide sur ordinateur, tablette et mobile */
.footer{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px 24px;padding:24px clamp(18px,5vw,72px);background:#211e1b;color:#FBF8F5;font-size:13px;}
.footer span{min-width:0;overflow-wrap:anywhere}
@media(max-width:640px){.footer{flex-direction:column;align-items:flex-start;gap:8px;padding:22px 20px;font-size:12px}}

/* Identité commune du pied de page MIARADI-TRAVEL */
.site-footer{background:#172033;color:#f6e8df;padding:44px clamp(20px,5vw,80px) 22px;width:100%;box-sizing:border-box}.site-footer .container{width:100%;max-width:1600px;margin:0 auto}.site-footer .footer-editorial{display:grid;grid-template-columns:minmax(220px,1.4fr) minmax(140px,.8fr) minmax(180px,1fr);gap:clamp(24px,5vw,80px);padding-bottom:34px}.site-footer .footer-brand-block p{max-width:350px;line-height:1.6;color:#e8d7ce;font-size:14px}.site-footer .brand-logo-footer{width:135px;max-height:60px;object-fit:contain}.site-footer .footer-link-group{display:flex;flex-direction:column;align-items:flex-start;gap:10px}.site-footer .footer-link-group h2{font-size:15px;font-weight:700;margin:0 0 5px;color:#fff}.site-footer a{color:inherit;text-decoration:none}.site-footer a:hover{text-decoration:underline;text-underline-offset:4px}.site-footer .footer-bottom{border-top:1px solid #ffffff2b;padding-top:22px;display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;font-size:12px}.site-footer .footer-credit{display:flex;align-items:center;gap:18px}.site-footer .footer-developer img{width:130px;max-height:48px;object-fit:contain}.site-footer .footer-legal a{color:#e8d7ce}.site-footer .copyright-admin-access{display:inline-flex;vertical-align:middle;margin-left:8px;opacity:.6}.site-footer .copyright-admin-access svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.5}.site-footer .footer-year{display:inline}
@media(max-width:760px){.site-footer{padding:32px 20px 20px}.site-footer .footer-editorial{grid-template-columns:repeat(2,minmax(0,1fr));gap:26px 18px;padding-bottom:24px}.site-footer .footer-brand-block{grid-column:1/-1}.site-footer .footer-link-group{min-width:0}.site-footer .footer-link-group h2{font-size:13px}.site-footer .footer-link-group a{font-size:13px;overflow-wrap:anywhere}.site-footer .footer-bottom{align-items:flex-start;flex-direction:column;gap:16px}.site-footer .footer-credit{flex-wrap:wrap;gap:12px}.site-footer .footer-developer img{width:112px}}
</style></head><body>
<header class="nav"><a class="nav-brand" href="../../index.html" aria-label="Accueil MIARADI-TRAVEL"><img class="logo" src="../../assets/miaradi-logo.png" alt="MIARADI — Tour Operator Madagascar"><span class="nav-brand-travel">-TRAVEL</span></a><nav class="nav-links"><a href="../../index.html">Accueil</a><a href="../../a-propos.html">L’esprit MIARADI</a><a href="../../index.html#circuits">Circuits</a><a class="cta" href="../../index.html#contact">Demander un devis ↗</a></nav></header>
<?php if (!$page): ?><main class="not-found"><h1>Cette page de circuit n’est pas disponible.</h1><p>Elle n’existe pas ou n’a pas encore été publiée.</p><a href="../../index.html#circuits">Retour aux circuits</a></main>
<?php else: ?><main><section class="hero"><?php if(!empty($page['hero_image'])): ?><img class="hero-bg" src="<?=page_escape(page_image_src($page['hero_image']))?>" alt=""><?php endif; ?><div class="hero-content"><div class="eyebrow">Voyage signature · Madagascar</div><h1><?=page_escape($title)?></h1><p><?=nl2br(page_escape($intro))?></p><div class="chips"><?php if($page['duration_min']): ?><span class="chip"><?=page_escape($page['duration_min'])?><?=($page['duration_max'] && (int)$page['duration_max']!==(int)$page['duration_min'])?'–'.page_escape($page['duration_max']):''?> jours</span><?php endif; ?><span class="chip">Tarif sur devis</span><span class="chip"><?=count($days)?> étapes</span></div><a class="cta" href="../../index.html#contact" style="display:inline-block;text-decoration:none">Imaginons votre voyage ↗</a></div></section>
<section class="intro"><div class="eyebrow">L’itinéraire</div><p><?=nl2br(page_escape($intro))?></p></section>
<?php
$regionName = trim((string)($page['region'] ?? 'Madagascar'));
$regionKey = mb_strtolower($regionName, 'UTF-8');
$mapZones = [
  'haute' => ['label' => 'Hautes Terres', 'lat' => -19.0, 'lng' => 47.2, 'zoom' => 5.4, 'note' => 'Les Hautes Terres centrales, autour d’Antananarivo et des hauts plateaux.'],
  'antananarivo' => ['label' => 'Hautes Terres', 'lat' => -19.0, 'lng' => 47.2, 'zoom' => 5.4, 'note' => 'Les Hautes Terres centrales, autour d’Antananarivo et des hauts plateaux.'],
  'nord-ouest' => ['label' => 'Nord-Ouest', 'lat' => -15.7, 'lng' => 46.3, 'zoom' => 5.2, 'note' => 'Le Nord-Ouest de Madagascar, entre le littoral et les territoires de l’intérieur.'],
  'nord ouest' => ['label' => 'Nord-Ouest', 'lat' => -15.7, 'lng' => 46.3, 'zoom' => 5.2, 'note' => 'Le Nord-Ouest de Madagascar, entre le littoral et les territoires de l’intérieur.'],
  'boeny' => ['label' => 'Boeny', 'lat' => -15.7, 'lng' => 46.3, 'zoom' => 6, 'note' => 'La région Boeny, autour de Mahajanga et de la côte nord-ouest.'],
  'sud' => ['label' => 'Sud de Madagascar', 'lat' => -23.4, 'lng' => 44.4, 'zoom' => 5.3, 'note' => 'Le Sud de Madagascar, aux paysages ouverts et aux reliefs contrastés.'],
  'nord' => ['label' => 'Nord de Madagascar', 'lat' => -13.5, 'lng' => 48.0, 'zoom' => 5.2, 'note' => 'Le Nord de Madagascar et ses paysages tropicaux.'],
  'nosy be' => ['label' => 'Nosy Be et le Nord', 'lat' => -13.3, 'lng' => 48.3, 'zoom' => 6, 'note' => 'Nosy Be et les îles du nord-ouest de Madagascar.'],
  'est' => ['label' => 'Côte Est', 'lat' => -18.2, 'lng' => 49.4, 'zoom' => 5.3, 'note' => 'La côte Est de Madagascar, entre forêts tropicales et littoral.'],
  'ouest' => ['label' => 'Ouest de Madagascar', 'lat' => -19.0, 'lng' => 45.0, 'zoom' => 5.2, 'note' => 'Les paysages de l’Ouest de Madagascar.'],
];
$mapZone = ['label' => $regionName ?: 'Madagascar', 'lat' => -19.0, 'lng' => 46.7, 'zoom' => 5.1, 'note' => 'La zone indicative de ce circuit à Madagascar.'];
foreach ($mapZones as $needle => $zone) { if (str_contains($regionKey, $needle)) { $mapZone = $zone; break; } }
?>
<section class="map-section" aria-labelledby="circuit-map-title">
  <div class="map-section-head"><div><div class="eyebrow">Où se déroule le voyage ?</div><h2 id="circuit-map-title">Le circuit sur la carte de Madagascar</h2><p>La zone mise en évidence correspond à la région principale de ce circuit.</p></div></div>
  <div class="circuit-map-card">
    <div id="circuit-madagascar-map" class="circuit-map" role="img" aria-label="Carte de Madagascar indiquant la région <?=page_escape($mapZone['label'])?>"></div>
    <div class="circuit-map-info"><span class="circuit-map-kicker">Région du circuit</span><h3><?=page_escape($mapZone['label'])?></h3><p><?=page_escape($mapZone['note'])?></p><div class="map-legend"><span class="map-legend-dot" aria-hidden="true"></span>Zone principale du circuit</div></div>
  </div>
</section>
<section class="itinerary"><div class="section-head"><h2>Votre voyage,<br><em style="font-family:Georgia,serif;font-weight:400;color:var(--red)">jour après jour.</em></h2><p>Ouvrez chaque étape pour découvrir les grandes expériences prévues. Le programme détaillé reste adaptable selon les conditions et l’organisation confirmée.</p></div>
<?php foreach($days as $i=>$day): $images=is_array($day['images']??null)?$day['images']:[]; ?><article class="day <?=($i%2===0?'image-left':'image-right')?>"><div class="day-heading"><span class="day-num"><?=str_pad((string)($i+1),2,'0',STR_PAD_LEFT)?></span><span class="day-title"><?=page_escape($day['title']??'Étape du voyage')?></span><span class="day-subtitle"><?=page_escape($day['subtitle']??'')?></span></div><div class="day-content"><div class="day-copy"><p><?=nl2br(page_escape($day['description']??''))?></p><?php if(!empty($day['overnight'])): ?><p class="overnight"><strong>Nuitée :</strong> <?=page_escape($day['overnight'])?></p><?php endif; ?></div><div class="gallery"><div class="gallery-main" data-gallery><?php if($images): foreach($images as $j=>$img): ?><img class="<?= $j===0?'active':'' ?>" src="<?=page_escape(page_image_src($img['url']??''))?>" alt="<?=page_escape($img['alt']??$day['title']??'Photo du circuit')?>" loading="lazy" decoding="async"><?php endforeach; ?><button type="button" class="gallery-arrow prev" aria-label="Photo précédente">‹</button><button type="button" class="gallery-arrow next" aria-label="Photo suivante">›</button><span class="gallery-count">1 / <?=count($images)?></span><?php else: ?><div class="no-image">Ajoutez des photos depuis l’administration</div><?php endif; ?></div><div class="gallery-caption">Jour <?=str_pad((string)($i+1),2,'0',STR_PAD_LEFT)?> · <?=page_escape($day['title']??'Étape du voyage')?></div></div></div></article><?php endforeach; ?></section><section class="bottom-cta"><h2>Prêts à imaginer votre voyage ?</h2><p>Parlons de vos envies et construisons ensemble un itinéraire qui vous ressemble.</p><a href="../../index.html#contact">Demander un devis ↗</a></section></main><footer class="site-footer">
  <div class="container footer-editorial">
    <div class="footer-brand-block"><a class="brand brand-footer" href="../../index.html#top"><img class="brand-logo brand-logo-footer" src="../../assets/miaradi-logo.png" alt="MIARADI-TRAVEL — Tour Operator Madagascar"></a><p>Madagascar se découvre mieux quand le voyage a du sens.</p></div>
    <div class="footer-link-group"><h2>Explorer</h2><a href="../../a-propos.html">Esprit MIARADI</a><a href="../../index.html#circuits">Nos circuits</a><a href="../../galerie.html">Galerie de voyage</a><a href="../../index.html#contact">Contact</a></div>
    <div class="footer-link-group"><h2>Préparer son voyage</h2><a href="../../index.html#contact">Voyage sur mesure</a><a href="../../index.html#contact">Demande de devis</a><a href="../../a-propos.html#faq">Questions fréquentes</a><a href="../../a-propos.html">Français / English</a></div>
  </div>
  <div class="container footer-bottom"><span>© <span class="footer-year"><?=date('Y')?></span> MIARADI-TRAVEL <a class="copyright-admin-access" href="admin/login.php" aria-label="Accès administrateur" title="Accès administrateur"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg></a></span><span class="footer-credit"><span class="footer-developer-label">Développé par :</span><a class="footer-developer" href="https://zagarino0.github.io/site-digital-work/" target="_blank" rel="noopener noreferrer"><img src="../../assets/02-logo-horizontal-fond-noir-removebg-preview (1).png" alt="Digital Work"></a></span><span class="footer-legal"><a href="politique-confidentialite.php">Mentions légales · Politique de confidentialité</a></span><a href="../../index.html#top">Retour en haut ↑</a></div>
</footer>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(() => {
  const node = document.getElementById('circuit-madagascar-map');
  if (!node || !window.L) return;
  const zone = <?=json_encode(['label' => $mapZone['label'], 'lat' => $mapZone['lat'], 'lng' => $mapZone['lng'], 'zoom' => $mapZone['zoom']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
  const map = L.map(node, {scrollWheelZoom: false, zoomControl: true}).setView([-19.0, 46.7], 5);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>'
  }).addTo(map);
  const zonePoint = [zone.lat, zone.lng];
  L.circle(zonePoint, {radius: 65000, color: '#922D20', weight: 2, opacity: .95, fillColor: '#922D20', fillOpacity: .22}).addTo(map);
  L.circleMarker(zonePoint, {radius: 7, color: '#fff', weight: 2, fillColor: '#922D20', fillOpacity: 1}).addTo(map)
    .bindTooltip(zone.label, {permanent: true, direction: 'top', className: 'miaradi-map-label'});
  // Garder l’île entière visible et afficher la zone du circuit en surbrillance.
  map.setView([-19.0, 46.7], 5);
  window.addEventListener('resize', () => map.invalidateSize(), {passive: true});
})();
</script>
<script>
document.querySelectorAll('[data-gallery]').forEach((gallery, galleryIndex) => {
  const imgs = [...gallery.querySelectorAll('img')];
  if (!imgs.length) return;
  const prev = gallery.querySelector('.prev');
  const next = gallery.querySelector('.next');
  const count = gallery.querySelector('.gallery-count');
  let index = Math.max(0, imgs.findIndex(img => img.classList.contains('active')));
  gallery.setAttribute('role', 'group');
  gallery.setAttribute('aria-label', 'Galerie photo de l’étape ' + (galleryIndex + 1));
  if (count) count.setAttribute('aria-live', 'polite');
  if (imgs.length < 2) {
    if (prev) prev.hidden = true;
    if (next) next.hidden = true;
  }
  function show(n) {
    index = (n + imgs.length) % imgs.length;
    imgs.forEach((img, i) => {
      img.classList.toggle('active', i === index);
      img.setAttribute('aria-hidden', i === index ? 'false' : 'true');
    });
    if (count) count.textContent = (index + 1) + ' / ' + imgs.length;
  }
  prev?.addEventListener('click', () => show(index - 1));
  next?.addEventListener('click', () => show(index + 1));
  show(index);
});

</script><?php endif; ?></body></html>
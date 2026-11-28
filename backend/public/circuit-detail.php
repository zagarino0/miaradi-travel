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
.itinerary-top{display:grid;grid-template-columns:minmax(0,.9fr) minmax(420px,1.1fr);gap:clamp(28px,4vw,64px);align-items:center;margin-bottom:34px}
.itinerary-top .section-head{margin:0;align-self:center}
.circuit-map-card{display:block;min-width:0;overflow:visible;border:0;border-radius:0;background:transparent;box-shadow:none}
.circuit-map{height:540px;min-width:0;background:transparent;position:relative;overflow:visible}
.circuit-map .map-layout{display:grid;grid-template-columns:minmax(125px,.42fr) minmax(0,1.58fr);align-items:stretch;gap:18px;width:100%;height:100%}
.circuit-map .map-zoom-wrap{position:relative;min-width:0;min-height:0}
.circuit-map .map-zoom{width:100%;height:100%}
.map-zoom-controls{position:absolute;right:8px;top:8px;display:flex;flex-direction:column;gap:5px;z-index:2}
.map-zoom-controls button{width:34px;height:34px;border:1px solid #d8c8b7;border-radius:50%;background:#FBF8F5;color:#922D20;font-size:23px;line-height:1;cursor:pointer;box-shadow:0 2px 8px #5b392018}
.map-zoom-controls button:hover,.map-zoom-controls button:focus-visible{background:#922D20;color:#fff;outline:2px solid #922D20;outline-offset:2px}
.circuit-map svg{display:block;width:100%;height:100%;overflow:visible;font-family:Georgia,"Times New Roman",serif}
.circuit-map .map-overview,.circuit-map .map-zoom{min-width:0;min-height:0}
.circuit-map-info{display:none}
.map-region-label{font-family:Inter,system-ui,sans-serif;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;fill:#7f2b1e}
.map-city-label{font-size:12px;font-style:italic;fill:#653021;paint-order:stroke;stroke:#FBF8F5;stroke-width:3px;stroke-linejoin:round}
.map-city-label.major{font-size:15px;font-style:normal;font-weight:700;letter-spacing:.01em}
.map-route-line{fill:none;stroke:#e99a1d;stroke-width:3.5;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:7 7}
.map-route-point{fill:#8f2c1d;stroke:#fff3d4;stroke-width:2}
.map-route-point.start{fill:#922d20;stroke:#fff;stroke-width:3}
.map-route-halo{fill:#922d20;fill-opacity:.12;stroke:#922d20;stroke-opacity:.55;stroke-width:1.5}
.map-zoom-ring{fill:none;stroke:#a87930;stroke-width:1.2;opacity:.45}
.map-ocean{fill:#f3d8a6;fill-opacity:.12}
@media(max-width:1000px){.itinerary-top{grid-template-columns:minmax(0,1fr);gap:26px}.itinerary-top .section-head{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1fr);gap:28px;align-items:end}.circuit-map{height:480px}.circuit-map .map-layout{grid-template-columns:minmax(170px,.55fr) minmax(0,1.45fr)}}
@media(max-width:600px){.itinerary-top .section-head{display:block}.circuit-map{height:390px}.circuit-map .map-layout{grid-template-columns:minmax(90px,.42fr) minmax(0,1.58fr);gap:6px}.map-region-label{font-size:10px}.map-city-label{font-size:10px}.map-city-label.major{font-size:12px}}
@media(max-width:390px){.circuit-map{height:340px}.circuit-map .map-layout{grid-template-columns:minmax(74px,.38fr) minmax(0,1.62fr);gap:3px}.map-city-label{font-size:9px}.map-city-label.major{font-size:10px}}
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
<section class="itinerary"><div class="itinerary-top"><div class="section-head"><div><div class="eyebrow">Votre itinéraire</div><h2>Votre voyage,<br><em style="font-family:Georgia,serif;font-weight:400;color:var(--red)">jour après jour.</em></h2></div><p>Suivez les principales étapes du voyage sur la carte. Le programme détaillé reste adaptable selon les conditions et l’organisation confirmée.</p></div>
  <div class="circuit-map-card" aria-label="Carte de Madagascar et itinéraire du circuit">
    <div id="circuit-madagascar-map" class="circuit-map" role="img" aria-label="Carte de Madagascar indiquant la région <?=page_escape($mapZone['label'])?> et les étapes du circuit"></div>
    <div class="circuit-map-info"><span class="circuit-map-kicker">Région du circuit</span><h3><?=page_escape($mapZone['label'])?></h3><div class="map-legend"><span class="map-legend-dot" aria-hidden="true"></span>Itinéraire en pointillés</div><p><?=page_escape($mapZone['note'])?></p></div>
  </div>
</div>
<?php foreach($days as $i=>$day): $images=is_array($day['images']??null)?$day['images']:[]; ?><article class="day <?=($i%2===0?'image-left':'image-right')?>"><div class="day-heading"><span class="day-num"><?=str_pad((string)($i+1),2,'0',STR_PAD_LEFT)?></span><span class="day-title"><?=page_escape($day['title']??'Étape du voyage')?></span><span class="day-subtitle"><?=page_escape($day['subtitle']??'')?></span></div><div class="day-content"><div class="day-copy"><p><?=nl2br(page_escape($day['description']??''))?></p><?php if(!empty($day['overnight'])): ?><p class="overnight"><strong>Nuitée :</strong> <?=page_escape($day['overnight'])?></p><?php endif; ?></div><div class="gallery"><div class="gallery-main" data-gallery><?php if($images): foreach($images as $j=>$img): ?><img class="<?= $j===0?'active':'' ?>" src="<?=page_escape(page_image_src($img['url']??''))?>" alt="<?=page_escape($img['alt']??$day['title']??'Photo du circuit')?>" loading="lazy" decoding="async"><?php endforeach; ?><button type="button" class="gallery-arrow prev" aria-label="Photo précédente">‹</button><button type="button" class="gallery-arrow next" aria-label="Photo suivante">›</button><span class="gallery-count">1 / <?=count($images)?></span><?php else: ?><div class="no-image">Ajoutez des photos depuis l’administration</div><?php endif; ?></div><div class="gallery-caption">Jour <?=str_pad((string)($i+1),2,'0',STR_PAD_LEFT)?> · <?=page_escape($day['title']??'Étape du voyage')?></div></div></div></article><?php endforeach; ?></section><section class="bottom-cta"><h2>Prêts à imaginer votre voyage ?</h2><p>Parlons de vos envies et construisons ensemble un itinéraire qui vous ressemble.</p><a href="../../index.html#contact">Demander un devis ↗</a></section></main><footer class="site-footer">
  <div class="container footer-editorial">
    <div class="footer-brand-block"><a class="brand brand-footer" href="../../index.html#top"><img class="brand-logo brand-logo-footer" src="../../assets/miaradi-logo.png" alt="MIARADI-TRAVEL — Tour Operator Madagascar"></a><p>Madagascar se découvre mieux quand le voyage a du sens.</p></div>
    <div class="footer-link-group"><h2>Explorer</h2><a href="../../a-propos.html">Esprit MIARADI</a><a href="../../index.html#circuits">Nos circuits</a><a href="../../galerie.html">Galerie de voyage</a><a href="../../index.html#contact">Contact</a></div>
    <div class="footer-link-group"><h2>Préparer son voyage</h2><a href="../../index.html#contact">Voyage sur mesure</a><a href="../../index.html#contact">Demande de devis</a><a href="../../a-propos.html#faq">Questions fréquentes</a><a href="../../a-propos.html">Français / English</a></div>
  </div>
  <div class="container footer-bottom"><span>© <span class="footer-year"><?=date('Y')?></span> MIARADI-TRAVEL <a class="copyright-admin-access" href="admin/login.php" aria-label="Accès administrateur" title="Accès administrateur"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg></a></span><span class="footer-credit"><span class="footer-developer-label">Développé par :</span><a class="footer-developer" href="https://zagarino0.github.io/site-digital-work/" target="_blank" rel="noopener noreferrer"><img src="../../assets/02-logo-horizontal-fond-noir-removebg-preview (1).png" alt="Digital Work"></a></span></div></div></footer>
<script>
(() => {
  const node = document.getElementById('circuit-madagascar-map');
  if (!node) return;
  const zone = <?=json_encode(['label' => $mapZone['label'], 'lat' => $mapZone['lat'], 'lng' => $mapZone['lng'], 'zoom' => $mapZone['zoom']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
  const routeStops = <?=json_encode((function () use ($days, $mapZone) {
    $places = [
      ['name'=>'Antananarivo','lat'=>-18.8792,'lng'=>47.5079,'keys'=>['antananarivo','tana','ivato']],
      ['name'=>'Andasibe','lat'=>-18.9333,'lng'=>48.4167,'keys'=>['andasibe','périnet']],
      ['name'=>'Moramanga','lat'=>-18.95,'lng'=>48.2333,'keys'=>['moramanga']],
      ['name'=>'Ambalavao','lat'=>-21.8333,'lng'=>46.9333,'keys'=>['ambalavao']],
      ['name'=>'Ranomafana','lat'=>-21.25,'lng'=>47.4167,'keys'=>['ranomafana']],
      ['name'=>'Ambatolampy','lat'=>-19.3833,'lng'=>47.4167,'keys'=>['ambatolampy']],
      ['name'=>'Antsirabe','lat'=>-19.8659,'lng'=>47.0333,'keys'=>['antsirabe']],
      ['name'=>'Fianarantsoa','lat'=>-21.45,'lng'=>47.0833,'keys'=>['fianarantsoa']],
      ['name'=>'Maevatanana','lat'=>-16.95,'lng'=>46.8333,'keys'=>['maevatanana']],
      ['name'=>'Ambondromamy','lat'=>-16.4333,'lng'=>47.15,'keys'=>['ambondromamy']],
      ['name'=>'Ankarafantsika','lat'=>-16.3167,'lng'=>46.8167,'keys'=>['ankarafantsika','ankarokaroka']],
      ['name'=>'Mahajanga','lat'=>-15.7167,'lng'=>46.3167,'keys'=>['mahajanga','majunga']],
      ['name'=>'Antsanitia','lat'=>-15.65,'lng'=>46.4,'keys'=>['antsanitia']],
      ['name'=>'Katsepy','lat'=>-15.75,'lng'=>46.25,'keys'=>['katsepy']],
      ['name'=>'Namoroka','lat'=>-16.4667,'lng'=>45.3333,'keys'=>['namoroka']],
      ['name'=>'Anjajavy','lat'=>-14.9833,'lng'=>47.25,'keys'=>['anjajavy']],
      ['name'=>'Marovoay','lat'=>-16.1167,'lng'=>46.6333,'keys'=>['marovoay']],
      ['name'=>'Fleuve Sofia','lat'=>-15.1,'lng'=>48.0,'keys'=>['fleuve sofia','sofia']],
      ['name'=>'Antsiranana','lat'=>-12.2765,'lng'=>49.2917,'keys'=>['antsiranana','diégo','diego suarez']],
      ['name'=>'Nosy Be','lat'=>-13.4,'lng'=>48.2667,'keys'=>['nosy be','nosy-be','hell-ville']],
      ['name'=>'Toamasina','lat'=>-18.1492,'lng'=>49.4023,'keys'=>['toamasina','tamatave']],
      ['name'=>'Sainte-Marie','lat'=>-17.0,'lng'=>49.85,'keys'=>['sainte-marie','sainte marie','nosy boraha']],
      ['name'=>'Sambava','lat'=>-14.2667,'lng'=>50.1667,'keys'=>['sambava']],
      ['name'=>'Vohemar','lat'=>-13.3667,'lng'=>50.0,'keys'=>['vohemar','vohémar']],
      ['name'=>'Ifaty','lat'=>-23.15,'lng'=>43.62,'keys'=>['ifaty']],
      ['name'=>'Anakao','lat'=>-23.6667,'lng'=>43.65,'keys'=>['anakao']],
      ['name'=>'Morondava','lat'=>-20.2833,'lng'=>44.2833,'keys'=>['morondava','baobab']],
      ['name'=>'Bekopaka','lat'=>-18.9333,'lng'=>44.8333,'keys'=>['bekopaka','tsingy de bemaraha','bemaraha']],
      ['name'=>'Manja','lat'=>-21.4333,'lng'=>44.3333,'keys'=>['manja']],
      ['name'=>'Toliara','lat'=>-23.35,'lng'=>43.6667,'keys'=>['toliara','tuléar']],
      ['name'=>'Parc national de l’Isalo','lat'=>-22.55,'lng'=>45.4,'keys'=>['isalo','ranohira']],
      ['name'=>'Manakara','lat'=>-22.15,'lng'=>48.0167,'keys'=>['manakara']]
    ];
    $found = [];
    foreach ($days as $day) {
      $text = mb_strtolower(implode(' ', array_map(static fn($v) => is_scalar($v) ? (string)$v : '', [$day['title'] ?? '', $day['subtitle'] ?? '', $day['description'] ?? ''])), 'UTF-8');
      $matches = [];
      foreach ($places as $place) {
        $position = null;
        foreach ($place['keys'] as $key) {
          $at = mb_strpos($text, mb_strtolower($key, 'UTF-8'), 0, 'UTF-8');
          if ($at !== false && ($position === null || $at < $position)) $position = $at;
        }
        if ($position !== null) $matches[] = ['position'=>$position, 'place'=>$place];
      }
      usort($matches, static fn($a,$b) => $a['position'] <=> $b['position']);
      // Une étape géographique par jour évite les tracés illisibles causés par
      // chaque ville citée dans les descriptions.
      if ($matches) {
        $place = $matches[0]['place'];
        $alreadyAdded = false;
        foreach ($found as $existing) {
          if ($existing['name'] === $place['name']) { $alreadyAdded = true; break; }
        }
        if (!$alreadyAdded) $found[] = ['name'=>$place['name'],'lat'=>$place['lat'],'lng'=>$place['lng']];
      }
    }
    if (!$found) $found = [['name'=>$mapZone['label'],'lat'=>(float)$mapZone['lat'],'lng'=>(float)$mapZone['lng']]];
    return $found;
  })(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
  const NS = 'http://www.w3.org/2000/svg';
  const make = (tag, attrs = {}, value = '') => {
    const el = document.createElementNS(NS, tag);
    Object.entries(attrs).forEach(([key, val]) => el.setAttribute(key, String(val)));
    if (value !== '') el.textContent = value;
    return el;
  };
  const mapLayout = document.createElement('div');
  mapLayout.className = 'map-layout';
  const overview = make('svg',{viewBox:'0 0 190 390',role:'img','aria-label':'Carte complète de Madagascar situant le circuit',preserveAspectRatio:'xMidYMid meet'});
  overview.classList.add('map-overview');
  const zoomWrap = document.createElement('div');
  zoomWrap.className = 'map-zoom-wrap';
  const zoom = make('svg',{viewBox:'0 0 520 390',role:'img','aria-label':'Carte agrandie de la zone du circuit avec les villes et le tracé',preserveAspectRatio:'xMidYMid meet'});
  zoom.classList.add('map-zoom');
  const zoomContent = make('g');
  const zoomControls = document.createElement('div');
  zoomControls.className = 'map-zoom-controls';
  zoomControls.setAttribute('aria-label','Commandes de zoom de la carte');
  zoomControls.innerHTML = '<button type="button" data-map-zoom="in" aria-label="Agrandir la carte" title="Agrandir">+</button><button type="button" data-map-zoom="out" aria-label="Réduire la carte" title="Réduire">−</button><button type="button" data-map-zoom="reset" aria-label="Réinitialiser le zoom" title="Réinitialiser">↺</button>';
  const defs = make('defs');
  const shadow = make('filter',{id:'island-shadow',x:'-30%',y:'-20%',width:'160%',height:'150%'});
  shadow.append(make('feDropShadow',{dx:'2',dy:'3',stdDeviation:'3','flood-color':'#8b6a3d','flood-opacity':'.17'}));
  defs.append(shadow);
    zoom.append(defs,zoomContent);

  // Contour côtier géographique Natural Earth de Madagascar.
  const coast = [
    [49.543519,-12.469833],[49.808981,-12.895285],[50.056511,-13.555761],[50.217431,-14.758789],
    [50.476537,-15.226512],[50.377111,-15.706069],[50.200275,-16.000263],[49.860606,-15.414253],
    [49.672607,-15.710204],[49.863344,-16.451037],[49.774564,-16.875042],[49.498612,-17.106036],
    [49.435619,-17.953064],[49.041792,-19.118781],[48.548541,-20.496888],[47.930749,-22.391501],
    [47.547723,-23.781959],[47.095761,-24.94163],[46.282478,-25.178463],[45.409508,-25.601434],
    [44.833574,-25.346101],[44.03972,-24.988345],[43.763768,-24.460677],[43.697778,-23.574116],
    [43.345654,-22.776904],[43.254187,-22.057413],[43.433298,-21.336475],[43.893683,-21.163307],
    [43.89637,-20.830459],[44.374325,-20.072366],[44.464397,-19.435454],[44.232422,-18.961995],
    [44.042976,-18.331387],[43.963084,-17.409945],[44.312469,-16.850496],[44.446517,-16.216219],
    [44.944937,-16.179374],[45.502732,-15.974373],[45.872994,-15.793454],[46.312243,-15.780018],
    [46.882183,-15.210182],[47.70513,-14.594303],[48.005215,-14.091233],[47.869047,-13.663869],
    [48.293828,-13.784068],[48.84506,-13.089175],[48.863509,-12.487868],[49.194651,-12.040557],
    [49.543519,-12.469833]
  ];
  const geo = (lat,lng)=>({x:(Number(lng)-43.2)*43.5,y:35+((-12-Number(lat))*40.5)});
  const globalCoast = coast.map(([lng,lat])=>geo(lat,lng));
  const pathFrom = points=>points.map((p,i)=>(i?'L ':'M ')+p.x.toFixed(1)+' '+p.y.toFixed(1)).join(' ')+' Z';
  // Carte entière à gauche : l’île reste identifiable à l’échelle du pays.
  const minX=Math.min(...globalCoast.map(p=>p.x)),maxX=Math.max(...globalCoast.map(p=>p.x));
  const minY=Math.min(...globalCoast.map(p=>p.y)),maxY=Math.max(...globalCoast.map(p=>p.y));
  const overviewScale=Math.min(150/(maxX-minX),350/(maxY-minY));
  const ox=(190-(maxX-minX)*overviewScale)/2-minX*overviewScale;
  const oy=(390-(maxY-minY)*overviewScale)/2-minY*overviewScale;
  const overviewPts=globalCoast.map(p=>({x:p.x*overviewScale+ox,y:p.y*overviewScale+oy}));
  overview.append(make('path',{d:pathFrom(overviewPts),fill:'#fffdfb',stroke:'#e8d8c6','stroke-width':1.1,filter:'url(#island-shadow)'}));
  overview.append(make('text',{x:95,y:18,'text-anchor':'middle',class:'map-region-label'},'MADAGASCAR'));
  const overviewCities=[
    {name:'Antsiranana',lat:-12.2765,lng:49.2917},
    {name:'Mahajanga',lat:-15.7167,lng:46.3167},
    {name:'Antananarivo',lat:-18.8792,lng:47.5079},
    {name:'Toamasina',lat:-18.1492,lng:49.4023},
    {name:'Toliara',lat:-23.35,lng:43.6667}
  ];
  overviewCities.forEach(city=>{
    const p=geo(city.lat,city.lng),x=p.x*overviewScale+ox,y=p.y*overviewScale+oy;
    overview.append(make('circle',{cx:x,cy:y,r:2.4,fill:'#922d20'}));
  });

  const route = Array.isArray(routeStops) ? routeStops : [];
  // Repérer également la zone du circuit sur la carte générale de Madagascar.
  const overviewRoute = route.map(stop => {
    const p = geo(stop.lat, stop.lng);
    return {x:p.x*overviewScale+ox, y:p.y*overviewScale+oy};
  });
  if (overviewRoute.length) {
    const xs = overviewRoute.map(p=>p.x), ys = overviewRoute.map(p=>p.y);
    const cx = (Math.min(...xs)+Math.max(...xs))/2;
    const cy = (Math.min(...ys)+Math.max(...ys))/2;
    const rx = Math.max(10,(Math.max(...xs)-Math.min(...xs))/2+9);
    const ry = Math.max(13,(Math.max(...ys)-Math.min(...ys))/2+9);
    overview.append(make('ellipse',{cx,cy,rx,ry,class:'map-route-halo'}));
    if (overviewRoute.length > 1) {
      const overviewPath = overviewRoute.map((p,i)=>(i?'L ':'M ')+p.x.toFixed(1)+' '+p.y.toFixed(1)).join(' ');
      overview.append(make('path',{d:overviewPath,class:'map-route-line','stroke-width':2}));
    }
    overviewRoute.forEach((p,i)=>overview.append(make('circle',{cx:p.x,cy:p.y,r:i===0?3.6:2.6,class:'map-route-point'+(i===0?' start':'')})));
  }
  // Calculer un cadrage dédié à l’itinéraire pour agrandir la bonne partie de l’île.
  const routeGeo=(route.length?route:[{lat:zone.lat,lng:zone.lng}]).map(s=>geo(s.lat,s.lng));
  const bounds={
    minX:Math.min(...routeGeo.map(p=>p.x)),maxX:Math.max(...routeGeo.map(p=>p.x)),
    minY:Math.min(...routeGeo.map(p=>p.y)),maxY:Math.max(...routeGeo.map(p=>p.y))
  };
  const spanX=Math.max(45,bounds.maxX-bounds.minX),spanY=Math.max(55,bounds.maxY-bounds.minY);
  // Le cadrage est calculé sur les étapes réellement retenues, avec une marge
  // réduite pour que la région visitée soit plus grande et plus facile à lire.
  const zoomScale=Math.min(455/(spanX*1.24),350/(spanY*1.24));
  const tx=260-(bounds.minX+bounds.maxX)/2*zoomScale;
  const ty=195-(bounds.minY+bounds.maxY)/2*zoomScale;
  const zoomPoint=p=>({x:p.x*zoomScale+tx,y:p.y*zoomScale+ty});
  const zoomCoast=globalCoast.map(zoomPoint);
  
  const landGroup=make('g');
  landGroup.append(make('path',{d:pathFrom(zoomCoast),fill:'#fffdfb',stroke:'#e8d8c6','stroke-width':1.3,filter:'url(#island-shadow)'}));
  zoomContent.append(landGroup);
  const pts=routeGeo.map(zoomPoint);
  if(pts.length>1){
    const d=pts.map((p,i)=>(i?'L ':'M ')+p.x.toFixed(1)+' '+p.y.toFixed(1)).join(' ');
    zoom.append(make('path',{d,class:'map-route-line'}));
  }
  route.forEach((stop,index)=>{
    const p=pts[index];
    if(!p) return;
    zoomContent.append(make('circle',{cx:p.x,cy:p.y,r:index===0||index===route.length-1?5.5:4,class:'map-route-point'+(index===0?' start':'')}));
    const labelOnLeft=(index%2===0);
    const labelX=p.x+(labelOnLeft?-8:8);
    const anchor=labelOnLeft?'end':'start';
    const labelY=Math.max(20,Math.min(370,p.y+(index%3===0?-9:15)));
    const major=['Antananarivo','Mahajanga','Antsiranana','Fianarantsoa','Toliara','Toamasina'].includes(stop.name);
    zoomContent.append(make('text',{x:labelX,y:labelY,'text-anchor':anchor,class:major?'map-city-label major':'map-city-label'},stop.name));
  });
  zoomContent.append(make('text',{x:260,y:20,'text-anchor':'middle',class:'map-region-label'},zone.label+' · ITINÉRAIRE'));
  let mapZoom = 1;
  const renderZoom = () => {
    const offsetX = 260 * (1 - mapZoom);
    const offsetY = 195 * (1 - mapZoom);
    zoomContent.setAttribute('transform', 'translate('+offsetX+' '+offsetY+') scale('+mapZoom+')');
  };
  zoomControls.addEventListener('click', event => {
    const button = event.target.closest('button[data-map-zoom]');
    if (!button) return;
    if (button.dataset.mapZoom === 'in') mapZoom = Math.min(3.2, mapZoom + 0.25);
    else if (button.dataset.mapZoom === 'out') mapZoom = Math.max(1, mapZoom - 0.25);
    else mapZoom = 1;
    renderZoom();
  });
  zoom.addEventListener('wheel', event => {
    event.preventDefault();
    mapZoom = Math.max(1, Math.min(3.2, mapZoom + (event.deltaY < 0 ? 0.12 : -0.12)));
    renderZoom();
  }, {passive:false});
  zoomWrap.append(zoom,zoomControls);
  mapLayout.append(overview,zoomWrap);
  node.replaceChildren(mapLayout);
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
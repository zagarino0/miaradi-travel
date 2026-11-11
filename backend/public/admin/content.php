<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}
$csrf = (string)($_SESSION['csrf_token'] ?? '');
$fields = [
 'eyebrow'=>'Petit titre','title'=>'Titre principal','paragraph1'=>'Premier paragraphe','paragraph2'=>'Deuxième paragraphe',
 'linkText'=>'Texte du lien','caption'=>'Légende de l’image','guideTitle'=>'Carte 1 — titre','guideText'=>'Carte 1 — texte',
 'lodgingTitle'=>'Carte 2 — titre','lodgingText'=>'Carte 2 — texte','craftsTitle'=>'Carte 3 — titre','craftsText'=>'Carte 3 — texte'
];
?><!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Contenu de la page d’accueil — MIARADI</title>
<style>
:root{--red:#922D20;--paper:#FBF8F5;--ink:#172033;--muted:#64748b;--line:#e8e2dc}*{box-sizing:border-box}body{margin:0;background:#f6f4f1;color:var(--ink);font:15px/1.5 Inter,system-ui,sans-serif}.wrap{max-width:1050px;margin:0 auto;padding:30px 20px 60px}.top{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:25px}.top a{color:var(--red);text-decoration:none}.panel{background:white;border:1px solid var(--line);border-radius:16px;padding:25px;box-shadow:0 8px 30px #17203308}.eyebrow{color:var(--red);font-size:11px;letter-spacing:1.5px;font-weight:800;text-transform:uppercase}h1{font-size:clamp(25px,4vw,36px);margin:5px 0}p{color:var(--muted)}form{display:grid;grid-template-columns:1fr 1fr;gap:15px}.field{display:grid;gap:6px;font-size:13px;font-weight:700}.field input,.field textarea{width:100%;border:1px solid #d8d3cf;border-radius:8px;padding:11px;font:inherit;color:var(--ink);background:#fff}.field textarea{min-height:110px;resize:vertical}.wide{grid-column:1/-1}.image-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;grid-column:1/-1}.image-card{border:1px solid var(--line);border-radius:12px;overflow:hidden;background:#fff}.image-card img{display:block;width:100%;height:190px;object-fit:cover;background:#f3eee9}.image-card-body{padding:13px}.image-card h2{font-size:16px;margin:0 0 8px}.image-card .field{margin:9px 0}.image-card input[type=file]{font-size:12px}.section-heading{grid-column:1/-1;padding-top:18px;border-top:1px solid var(--line);margin-top:6px}.section-heading h2{margin:0;font-size:20px}.section-heading p{margin:5px 0 0}@media(max-width:650px){.image-grid{grid-template-columns:1fr}.image-card img{height:210px}}.actions{display:flex;align-items:center;gap:15px;grid-column:1/-1;margin-top:8px}.button{border:0;border-radius:9px;padding:12px 18px;background:var(--red);color:#fff;font-weight:750;cursor:pointer}.status{color:var(--red);font-size:13px}@media(max-width:650px){form{grid-template-columns:1fr}.wide,.actions{grid-column:auto}.top{align-items:flex-start;flex-direction:column}}
</style></head><body><main class="wrap"><div class="top"><div><div class="eyebrow">Administration MIARADI</div><h1>Contenu de la page d’accueil</h1><p>Modifiez les textes de la section « Une aventure humaine » sans toucher au design.</p></div><a href="index.php">← Retour au tableau de bord</a></div><section class="panel"><form id="content-form"><?php foreach ($fields as $key=>$label): $long=in_array($key,['paragraph1','paragraph2','caption','guideText','lodgingText','craftsText'],true); ?><label class="field <?=in_array($key,['paragraph1','paragraph2'],true)?'wide':''?>"><?=htmlspecialchars($label,ENT_QUOTES,'UTF-8')?><?php if($long):?><textarea name="<?=htmlspecialchars($key,ENT_QUOTES,'UTF-8')?>" required></textarea><?php else:?><input name="<?=htmlspecialchars($key,ENT_QUOTES,'UTF-8')?>" maxlength="<?=in_array($key,['title'],true)?240:160?>" required><?php endif;?></label><?php endforeach;?>
<div class="section-heading"><h2>Images de la section</h2><p>Remplacez les quatre photos ici, sans quitter l’éditeur de contenu.</p></div>
<div class="image-grid">
<?php foreach (['feature'=>'Image principale','guide'=>'Guides locaux','lodging'=>'Hébergements','crafts'=>'Artisans & savoir-faire'] as $slot=>$imageLabel): $imageKey=$slot==='feature'?'featureImage':$slot.'Image'; $altKey=$slot.'Alt'; ?>
<article class="image-card" data-image-card="<?=htmlspecialchars($slot,ENT_QUOTES,'UTF-8')?>">
<img data-preview="<?=htmlspecialchars($slot,ENT_QUOTES,'UTF-8')?>" src="" alt="Aperçu de l’image">
<div class="image-card-body"><h2><?=htmlspecialchars($imageLabel,ENT_QUOTES,'UTF-8')?></h2>
<input type="hidden" name="<?=htmlspecialchars($imageKey,ENT_QUOTES,'UTF-8')?>">
<label class="field">Remplacer l’image<input type="file" data-upload="<?=htmlspecialchars($slot,ENT_QUOTES,'UTF-8')?>" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG ou WebP — 8 Mo maximum.</small></label>
<label class="field">Texte alternatif<input name="<?=htmlspecialchars($altKey,ENT_QUOTES,'UTF-8')?>" maxlength="250" required></label>
<span class="status" data-image-status="<?=htmlspecialchars($slot,ENT_QUOTES,'UTF-8')?>" role="status"></span>
</div></article>
<?php endforeach;?>
<div class="actions"><button class="button" id="save" type="submit">Enregistrer les modifications</button><span class="status" id="status" role="status"></span></div></form></section></main>
<script>
const form=document.querySelector('#content-form'),status=document.querySelector('#status'),save=document.querySelector('#save');
const csrf=<?=json_encode($csrf,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const imageKeys={feature:'featureImage',guide:'guideImage',lodging:'lodgingImage',crafts:'craftsImage'};
function previewPath(path){return path||'';}
async function uploadImage(slot,file){const status=document.querySelector('[data-image-status="'+slot+'"]');status.textContent='Téléversement…';const body=new FormData();body.append('slot',slot);body.append('image',file);try{const response=await fetch('../api/page-content-image.php',{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-Token':csrf},body});const result=await response.json();if(!response.ok)throw new Error(result.error?.message||'Échec du téléversement.');form.elements[imageKeys[slot]].value=result.data.path;document.querySelector('[data-preview="'+slot+'"]').src=previewPath(result.data.path);status.textContent='Image chargée. Cliquez sur « Enregistrer les modifications » pour confirmer le contenu.';}catch(error){status.textContent=error.message;}}
document.querySelectorAll('[data-upload]').forEach(input=>input.addEventListener('change',()=>{const file=input.files?.[0];if(file)uploadImage(input.dataset.upload,file);}));
async function call(method='GET',data=null){const response=await fetch('../api/page-content.php',{method,credentials:'same-origin',headers:{Accept:'application/json',...(data?{'Content-Type':'application/json','X-CSRF-Token':csrf}:{})},body:data?JSON.stringify(data):undefined});const payload=await response.json();if(!response.ok)throw new Error(payload.error?.message||'Une erreur est survenue.');return payload;}
(async()=>{try{const result=await call();for(const [key,value] of Object.entries(result.data||{})){if(form.elements[key])form.elements[key].value=value;}
for(const slot of Object.keys(imageKeys)){const path=result.data?.[imageKeys[slot]]||'';const preview=document.querySelector('[data-preview="'+slot+'"]');if(preview)preview.src=previewPath(path);}}catch(e){status.textContent=e.message;}})();
form.addEventListener('submit',async event=>{event.preventDefault();save.disabled=true;status.textContent='Enregistrement…';const data=Object.fromEntries(new FormData(form).entries());try{await call('PUT',data);status.textContent='Modifications enregistrées.';}catch(e){status.textContent=e.message;}finally{save.disabled=false;}});
</script></body></html>
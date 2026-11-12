<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}
$csrf = (string) ($_SESSION['csrf_token'] ?? '');
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Politique de confidentialité — MIARADI-TRAVEL</title>
<style>
:root{--red:#922d20;--paper:#fbf8f5;--ink:#172238;--muted:#65738a;--line:#eadfdd;--white:#fff;--soft:#f4efff}
*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:15px/1.55 Inter,"Segoe UI",Arial,sans-serif}
header{background:#fff;border-bottom:1px solid var(--line);padding:16px clamp(18px,4vw,52px);display:flex;align-items:center;justify-content:space-between;gap:18px}
.brand{font-weight:850;letter-spacing:.05em;color:var(--red)}a{color:var(--red)}.back{font-size:13px;text-decoration:none;font-weight:700}
main{max-width:1180px;margin:auto;padding:34px clamp(16px,4vw,44px) 60px}.kicker{font-size:11px;letter-spacing:.17em;font-weight:800;color:var(--red)}
h1{font-size:clamp(27px,4vw,38px);line-height:1.15;letter-spacing:-.04em;margin:8px 0 10px}.intro{max-width:760px;color:var(--muted);margin:0 0 24px}
.card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:clamp(18px,3vw,30px);box-shadow:0 8px 24px #34221a08}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.field{display:flex;flex-direction:column;gap:7px;min-width:0}.field label{font-weight:750;font-size:13px}
input[type=text],textarea{width:100%;border:1px solid #e3dbe8;border-radius:10px;background:#fdfcff;padding:12px 14px;color:var(--ink);font:inherit;outline-color:#a995ff}
textarea{min-height:340px;resize:vertical;line-height:1.65}.hint{font-size:12px;color:var(--muted);margin:0}
.publish{display:flex;align-items:flex-start;gap:10px;padding:15px;background:#faf7f4;border:1px solid var(--line);border-radius:10px;margin:22px 0}
.publish input{margin-top:5px;accent-color:var(--red);width:17px;height:17px}.publish strong{display:block}.publish span{font-size:13px;color:var(--muted)}
.actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.button{appearance:none;border:0;border-radius:10px;background:var(--red);color:white;padding:12px 18px;font:inherit;font-weight:800;cursor:pointer}.button:disabled{opacity:.55;cursor:wait}
.status{font-size:13px;color:var(--muted)}.status.error{color:#a52a24}.status.success{color:#247447}.preview{margin-top:24px;border-top:1px solid var(--line);padding-top:20px}.preview h2{font:clamp(23px,3vw,32px)/1.2 Georgia,serif;margin:0 0 12px}.preview-copy{white-space:pre-wrap;color:#514c4a;max-width:850px}
@media(max-width:760px){.grid{grid-template-columns:1fr}textarea{min-height:250px}header{align-items:flex-start}}
</style>
</head>
<body>
<header><div class="brand">MIARADI <span style="font-size:11px;color:var(--muted);font-weight:650">· ESPACE ADMIN</span></div><a class="back" href="index.php">← Retour au tableau de bord</a></header>
<main>
<p class="kicker">PARAMÈTRES DU SITE</p>
<h1>Politique de confidentialité</h1>
<p class="intro">Rédigez et gérez le texte présenté aux visiteurs lorsqu’ils cliquent sur « Politique de confidentialité » dans le formulaire de demande de devis. Vous pouvez enregistrer un brouillon sans le rendre public.</p>
<form class="card" id="policy-form">
<div class="grid">
<div class="field"><label for="title_fr">Titre (FR)</label><input id="title_fr" name="title_fr" type="text" maxlength="220" required value="Politique de confidentialité"></div>
<div class="field"><label for="title_en">Title (EN)</label><input id="title_en" name="title_en" type="text" maxlength="220" required value="Privacy policy"></div>
<div class="field"><label for="content_fr">Contenu de la politique (FR)</label><textarea id="content_fr" name="content_fr" maxlength="30000" required placeholder="Décrivez les données collectées, leur usage, leur durée de conservation et les droits des personnes."></textarea><p class="hint">Texte brut : les paragraphes sont conservés à l’affichage.</p></div>
<div class="field"><label for="content_en">Policy content (EN)</label><textarea id="content_en" name="content_en" maxlength="30000" required placeholder="Explain the data collected, how it is used, retention and individual rights."></textarea><p class="hint">Plain text; paragraph breaks are preserved.</p></div>
</div>
<label class="publish"><input type="checkbox" id="is_published" name="is_published"><span><strong>Publier la politique sur le site</strong>Lorsqu’elle est publiée, la page devient accessible aux visiteurs. Vérifiez le contenu et les coordonnées avant publication.</span></label>
<div class="actions"><button class="button" id="save" type="submit">Enregistrer les modifications</button><span class="status" id="status" role="status" aria-live="polite">Chargement…</span></div>
<div class="preview"><p class="kicker">APERÇU FRANÇAIS</p><h2 id="preview-title">Politique de confidentialité</h2><div class="preview-copy" id="preview-copy"></div></div>
</form>
</main>
<script>
const csrf = <?=json_encode($csrf, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const form=document.getElementById('policy-form'), statusNode=document.getElementById('status'), saveButton=document.getElementById('save');
const fields=['title_fr','title_en','content_fr','content_en'];
async function api(method='GET',data=null){
 const response=await fetch('../api/privacy-policy.php',{method,credentials:'same-origin',headers:{Accept:'application/json',...(data?{'Content-Type':'application/json','X-CSRF-Token':csrf}:{})},body:data?JSON.stringify(data):undefined});
 const raw=await response.text();let payload;try{payload=JSON.parse(raw)}catch{throw new Error(raw.replace(/<[^>]*>/g,' ').replace(/\s+/g,' ').trim().slice(0,240)||'Réponse serveur invalide.')}
 if(!response.ok)throw new Error(payload.error?.message||'Une erreur est survenue.');return payload;
}
function preview(){document.getElementById('preview-title').textContent=form.elements.title_fr.value;document.getElementById('preview-copy').textContent=form.elements.content_fr.value}
fields.forEach(name=>form.elements[name].addEventListener('input',preview));
(async()=>{try{const p=(await api()).data;fields.forEach(name=>{form.elements[name].value=p[name]||''});form.elements.is_published.checked=Boolean(p.is_published);preview();statusNode.textContent=p.updated_at?'Dernière mise à jour : '+p.updated_at:'Nouveau paramètre — migration à vérifier';}catch(e){statusNode.textContent=e.message;statusNode.className='status error'}})();
form.addEventListener('submit',async event=>{event.preventDefault();saveButton.disabled=true;statusNode.className='status';statusNode.textContent='Enregistrement…';const data={};fields.forEach(name=>data[name]=form.elements[name].value);data.is_published=form.elements.is_published.checked;try{const p=(await api('POST',data)).data;statusNode.className='status success';statusNode.textContent='Politique enregistrée'+(p.is_published?' et publiée.': ' en brouillon.');}catch(e){statusNode.className='status error';statusNode.textContent=e.message}finally{saveButton.disabled=false}});
</script>
</body></html>

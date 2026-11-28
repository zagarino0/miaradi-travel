<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) { header('Location: login.php'); exit; }
$csrf = (string)($_SESSION['csrf_token'] ?? '');
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pages des engagements — Administration MIARADI</title>
<style>
:root{--red:#922D20;--paper:#f7f4f0;--ink:#24211f;--muted:#706761;--line:#e5ddd6}
*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:14px/1.6 Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px clamp(18px,4vw,48px);background:#fff;border-bottom:1px solid var(--line)}header strong{font-size:16px}header a{color:var(--red);text-decoration:none;font-weight:700}
main{max-width:1000px;margin:0 auto;padding:36px 18px 80px}h1{margin:0 0 8px;font-size:clamp(26px,4vw,38px);letter-spacing:-.04em}.intro{margin:0 0 28px;color:var(--muted)}
fieldset{border:1px solid var(--line);border-radius:14px;background:#fff;padding:22px;margin:0 0 20px;min-width:0}legend{padding:0 8px;font-weight:800;font-size:18px}.field{display:grid;gap:6px;margin:14px 0}label{font-weight:700;font-size:13px}input,textarea{width:100%;border:1px solid #dcd3cc;border-radius:8px;padding:11px 12px;background:#fff;color:var(--ink);font:inherit}textarea{min-height:100px;resize:vertical}input:focus,textarea:focus{outline:3px solid #c98c7e66;border-color:var(--red)}
.actions{position:sticky;bottom:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--line);border-radius:12px;padding:14px;box-shadow:0 8px 28px #211d1a18}.save{background:var(--red);color:#fff;border:0;border-radius:8px;padding:12px 20px;font-weight:800}.save:disabled{opacity:.5;cursor:not-allowed}#status{color:var(--muted);flex:1}
</style></head><body>
<header><strong>MIARADI-TRAVEL · Pages éditoriales</strong><a href="index.php">← Retour à l’administration</a></header>
<main><h1>Modifier les pages des engagements</h1><p class="intro">Modifiez les textes des trois pages accessibles depuis les cartes de la page d’accueil. Les changements sont enregistrés dans le contenu du site et affichés sur les pages publiques.</p>
<form id="engagement-form">
<fieldset><legend>Tourisme responsable</legend>
<div class="field"><label for="responsibleTitle">Titre de la page</label><input id="responsibleTitle" name="responsibleTitle" maxlength="200" required></div>
<div class="field"><label for="responsibleIntro">Texte d’introduction</label><textarea id="responsibleIntro" name="responsibleIntro" maxlength="2000" required></textarea></div>
<div class="field"><label for="responsibleText1">Texte — bloc 1</label><textarea id="responsibleText1" name="responsibleText1" maxlength="2000" required></textarea></div>
<div class="field"><label for="responsibleText2">Texte — bloc 2</label><textarea id="responsibleText2" name="responsibleText2" maxlength="2000" required></textarea></div>
</fieldset>
<fieldset><legend>Accompagnement dédié</legend>
<div class="field"><label for="supportTitle">Titre de la page</label><input id="supportTitle" name="supportTitle" maxlength="200" required></div>
<div class="field"><label for="supportIntro">Texte d’introduction</label><textarea id="supportIntro" name="supportIntro" maxlength="2000" required></textarea></div>
<div class="field"><label for="supportText1">Texte — bloc 1</label><textarea id="supportText1" name="supportText1" maxlength="2000" required></textarea></div>
<div class="field"><label for="supportText2">Texte — bloc 2</label><textarea id="supportText2" name="supportText2" maxlength="2000" required></textarea></div>
</fieldset>
<fieldset><legend>Voyage sur mesure</legend>
<div class="field"><label for="customTitle">Titre de la page</label><input id="customTitle" name="customTitle" maxlength="200" required></div>
<div class="field"><label for="customIntro">Texte d’introduction</label><textarea id="customIntro" name="customIntro" maxlength="2000" required></textarea></div>
<div class="field"><label for="customText1">Texte — bloc 1</label><textarea id="customText1" name="customText1" maxlength="2000" required></textarea></div>
<div class="field"><label for="customText2">Texte — bloc 2</label><textarea id="customText2" name="customText2" maxlength="2000" required></textarea></div>
</fieldset>
<div class="actions"><button class="save" id="save" type="submit" disabled>Enregistrer les textes</button><span id="status" role="status" aria-live="polite">Chargement des textes…</span></div>
</form></main>
<script>
const form=document.getElementById('engagement-form');
const saveButton=document.getElementById('save');
const statusNode=document.getElementById('status');
const csrf=<?=json_encode($csrf,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const keys=['responsibleTitle','responsibleIntro','responsibleText1','responsibleText2','supportTitle','supportIntro','supportText1','supportText2','customTitle','customIntro','customText1','customText2'];
const dirty=new Set();
async function api(method='GET',data=null){const response=await fetch('../api/homepage-settings.php',{method,credentials:'same-origin',headers:{Accept:'application/json',...(data?{'Content-Type':'application/json','X-CSRF-Token':csrf}:{})},body:data?JSON.stringify(data):undefined});const raw=await response.text();let payload;try{payload=JSON.parse(raw)}catch{throw new Error('Réponse invalide du serveur. Vérifiez les journaux PHP.')}if(!response.ok)throw new Error(payload.error?.message||'Impossible de traiter la demande.');return payload;}
(async()=>{try{const payload=await api();for(const key of keys){const field=form.elements[key];if(field)field.value=payload.data?.[key]||'';}statusNode.textContent='Textes chargés. Vous pouvez modifier les champs.';}catch(error){statusNode.textContent=error.message;}})();
for(const key of keys){const field=form.elements[key];field.addEventListener('input',()=>{dirty.add(key);saveButton.disabled=false;statusNode.textContent=dirty.size+' champ(s) modifié(s), non enregistré(s).';});}
form.addEventListener('submit',async event=>{event.preventDefault();if(!dirty.size)return;saveButton.disabled=true;statusNode.textContent='Enregistrement…';const data=Object.fromEntries([...dirty].map(key=>[key,form.elements[key].value]));try{await api('PUT',data);dirty.clear();statusNode.textContent='Modifications enregistrées. Les pages publiques afficheront les nouveaux textes.';}catch(error){statusNode.textContent=error.message;saveButton.disabled=false;}});
</script></body></html>
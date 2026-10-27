<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Administration — MIARADI-TRAVEL</title>
<style>
:root{--red:#922D20;--paper:#FBF8F5;--ink:#211d1a;--muted:#706761;--line:rgba(33,29,26,.16);--soft:#F0EDE7;--serif:Georgia,serif;--sans:Arial,sans-serif}
*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:15px/1.55 var(--sans)}button,input,select,textarea{font:inherit}button{cursor:pointer}.shell{max-width:1180px;margin:auto;padding:28px 20px 60px}.top{display:flex;justify-content:space-between;align-items:center;gap:16px;border-bottom:1px solid var(--line);padding-bottom:20px}.brand{color:var(--red);font-size:13px;letter-spacing:.16em;font-weight:700}.top h1{font:normal clamp(28px,5vw,48px)/1.1 var(--serif);margin:8px 0}.muted{color:var(--muted)}.button{border:0;padding:11px 15px;background:var(--red);color:white}.button.secondary{background:var(--soft);color:var(--ink);border:1px solid var(--line)}.panel{margin-top:24px;padding:clamp(18px,3vw,30px);background:white;border:1px solid var(--line)}.login{max-width:480px;margin:9vh auto}.field{display:grid;gap:6px;margin:12px 0}.field input,.field select,.field textarea{width:100%;padding:11px;border:1px solid var(--line);background:var(--paper);color:var(--ink)}.field textarea{min-height:90px}.row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.tabs{display:flex;gap:8px;margin:24px 0}.tabs button{padding:10px 15px;border:1px solid var(--line);background:transparent}.tabs button.active{background:var(--ink);color:white}.table-wrap{overflow:auto}table{border-collapse:collapse;width:100%;min-width:700px}th,td{text-align:left;padding:12px;border-bottom:1px solid var(--line);vertical-align:top}th{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}.status{display:inline-block;padding:3px 7px;background:var(--soft);font-size:12px}.notice{min-height:24px;color:var(--red)}.actions{display:flex;gap:8px;flex-wrap:wrap}.quote-message{max-width:380px;white-space:pre-wrap;overflow-wrap:anywhere}.hidden{display:none!important}.hint{font-size:12px;color:var(--muted)}@media(max-width:650px){.row{grid-template-columns:1fr}.top{align-items:flex-start}.tabs{flex-wrap:wrap}.shell{padding-inline:14px}}
</style>
</head>
<body>
<div class="shell">
  <header class="top"><div><div class="brand">MIARADI — ADMINISTRATION</div><h1>Gestion du site</h1><div class="muted">Circuits et demandes de devis</div></div><button class="button secondary hidden" id="logout">Déconnexion</button></header>
  <section class="panel login" id="login-panel"><h2>Connexion administrateur</h2><p class="muted">Utilisez le compte administrateur déjà créé dans la base MIARADI.</p><form id="login-form"><label class="field">Adresse e-mail<input type="email" name="email" autocomplete="username" required maxlength="254"></label><label class="field">Mot de passe<input type="password" name="password" autocomplete="current-password" required maxlength="1024"></label><button class="button" type="submit">Se connecter</button><p class="notice" id="login-status" role="status"></p></form></section>
  <main id="dashboard" class="hidden">
    <nav class="tabs"><button class="active" data-tab="circuits">Circuits</button><button data-tab="quotes">Demandes de devis</button></nav>
    <p class="notice" id="notice" role="status"></p>
    <section class="panel" id="circuits-panel"><div class="actions" style="justify-content:space-between;align-items:center"><h2>Catalogue des circuits</h2><button class="button" id="new-circuit">Ajouter un circuit</button></div><div class="table-wrap"><table><thead><tr><th>Circuit</th><th>Région / thème</th><th>Durée</th><th>État</th><th>Actions</th></tr></thead><tbody id="circuits-list"></tbody></table></div></section>
    <section class="panel hidden" id="quotes-panel"><h2>Demandes reçues</h2><div class="table-wrap"><table><thead><tr><th>Contact</th><th>Voyage</th><th>Message</th><th>Statut</th></tr></thead><tbody id="quotes-list"></tbody></table></div></section>
    <section class="panel hidden" id="editor-panel"><h2 id="editor-title">Modifier un circuit</h2><form id="circuit-form">
      <input type="hidden" name="id">
      <div class="row"><label class="field">Slug (URL)<input name="slug" required maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*"></label><label class="field">Ordre d'affichage<input name="sort_order" type="number" value="0" required></label></div>
      <div class="row"><label class="field">Titre français<input name="title_fr" required maxlength="200"></label><label class="field">Titre anglais<input name="title_en" maxlength="200"></label></div>
      <div class="row"><label class="field">Résumé français<textarea name="summary_fr" required maxlength="10000"></textarea></label><label class="field">Résumé anglais<textarea name="summary_en" maxlength="10000"></textarea></label></div>
      <div class="row"><label class="field">Région<input name="region" maxlength="80"></label><label class="field">Thème<input name="theme" maxlength="80"></label></div>
      <div class="row"><label class="field">Durée minimale (jours)<input name="duration_min" type="number" min="1" max="365"></label><label class="field">Durée maximale (jours)<input name="duration_max" type="number" min="1" max="365"></label></div>
      <label class="field">Chemin de l'image<input name="image_path" maxlength="500" placeholder="assets/circuits/nom.jpg"></label>
      <label class="field"><span><input name="is_published" type="checkbox"> Publier ce circuit</span></label>
      <div class="actions"><button class="button" type="submit">Enregistrer</button><button class="button secondary" type="button" id="cancel-editor">Annuler</button></div>
    </form></section>
  </main>
</div>
<script>
(() => {
  const $ = (s) => document.querySelector(s);
  let csrf = '';
  let circuits = [];
  const api = (path) => '../api/' + path;
  async function request(path, options = {}) {
    const headers = {'Accept':'application/json', ...(options.body ? {'Content-Type':'application/json'} : {}), ...(options.mutate ? {'X-CSRF-Token':csrf} : {})};
    const response = await fetch(api(path), {credentials:'same-origin', ...options, headers});
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(payload.error?.message || 'Une erreur est survenue.');
    return payload;
  }
  function notice(text) { $('#notice').textContent = text || ''; }
  function esc(value) { return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  function showDashboard(session) {
    csrf = session.csrfToken || '';
    $('#login-panel').classList.add('hidden'); $('#dashboard').classList.remove('hidden'); $('#logout').classList.remove('hidden');
    loadCircuits();
  }
  async function checkSession() {
    try { const result = await request('admin/session.php'); if (result.data.authenticated) showDashboard(result.data); }
    catch (_) { /* Show login form if the session endpoint is not reachable. */ }
  }
  $('#login-form').addEventListener('submit', async (event) => {
    event.preventDefault(); $('#login-status').textContent = 'Connexion…';
    const form = new FormData(event.currentTarget);
    try {
      const result = await request('admin/session.php', {method:'POST', body:JSON.stringify({email:form.get('email'), password:form.get('password')})});
      $('#login-status').textContent = ''; showDashboard(result.data);
    } catch (error) { $('#login-status').textContent = error.message; }
  });
  $('#logout').addEventListener('click', async () => {
    try { await request('admin/session.php', {method:'POST', mutate:true, body:JSON.stringify({action:'logout'})}); } catch (_) {}
    location.reload();
  });
  async function loadCircuits() {
    try {
      const result = await request('admin/circuits.php'); circuits = result.data;
      $('#circuits-list').innerHTML = circuits.map(c => '<tr><td><strong>'+esc(c.title_fr)+'</strong><br><span class="muted">'+esc(c.slug)+'</span></td><td>'+esc(c.region)+'<br>'+esc(c.theme)+'</td><td>'+esc(c.duration_min ?? '—')+' – '+esc(c.duration_max ?? '—')+' j</td><td><span class="status">'+(c.is_published?'Publié':'Brouillon')+'</span></td><td><div class="actions"><button class="button secondary" data-edit="'+c.id+'">Modifier</button><button class="button secondary" data-delete="'+c.id+'">Supprimer</button></div></td></tr>').join('') || '<tr><td colspan="5">Aucun circuit dans la base.</td></tr>';
      $('#circuits-list').querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => editCircuit(Number(b.dataset.edit))));
      $('#circuits-list').querySelectorAll('[data-delete]').forEach(b => b.addEventListener('click', () => deleteCircuit(Number(b.dataset.delete))));
    } catch (error) { notice(error.message); }
  }
  function editCircuit(id = null) {
    const form = $('#circuit-form'); form.reset();
    const c = circuits.find(item => item.id === id);
    $('#editor-title').textContent = c ? 'Modifier un circuit' : 'Ajouter un circuit';
    for (const key of ['id','slug','title_fr','title_en','summary_fr','summary_en','region','theme','duration_min','duration_max','image_path','sort_order']) form.elements[key].value = c?.[key] ?? (key === 'sort_order' ? 0 : '');
    form.elements.is_published.checked = Boolean(c?.is_published);
    $('#editor-panel').classList.remove('hidden'); $('#editor-panel').scrollIntoView({behavior:'smooth',block:'start'});
  }
  async function deleteCircuit(id) {
    if (!confirm('Supprimer ce circuit ? Les demandes associées resteront conservées.')) return;
    try { await request('admin/circuits.php?id='+id, {method:'DELETE', mutate:true}); notice('Circuit supprimé.'); await loadCircuits(); }
    catch (error) { notice(error.message); }
  }
  $('#new-circuit').addEventListener('click', () => editCircuit());
  $('#cancel-editor').addEventListener('click', () => $('#editor-panel').classList.add('hidden'));
  $('#circuit-form').addEventListener('submit', async (event) => {
    event.preventDefault(); const f = event.currentTarget;
    const value = (name) => f.elements[name].value.trim();
    const intOrNull = (name) => value(name) === '' ? null : Number(value(name));
    const id = value('id');
    const payload = {slug:value('slug'),title_fr:value('title_fr'),title_en:value('title_en')||null,summary_fr:value('summary_fr'),summary_en:value('summary_en')||null,region:value('region')||null,theme:value('theme')||null,duration_min:intOrNull('duration_min'),duration_max:intOrNull('duration_max'),image_path:value('image_path')||null,is_published:f.elements.is_published.checked,sort_order:Number(value('sort_order')||0)};
    if (id) payload.id = Number(id);
    try {
      await request('admin/circuits.php', {method:id?'PATCH':'POST', mutate:true, body:JSON.stringify(payload)});
      $('#editor-panel').classList.add('hidden'); notice('Circuit enregistré.'); await loadCircuits();
    } catch (error) { notice(error.message); }
  });
  async function loadQuotes() {
    try {
      const result = await request('admin/quotes.php');
      $('#quotes-list').innerHTML = result.data.map(q => '<tr><td><strong>'+esc(q.full_name)+'</strong><br><a href="mailto:'+esc(q.email)+'">'+esc(q.email)+'</a><br><span class="muted">'+esc(q.created_at)+'</span></td><td>'+esc(q.circuit_title_fr || 'Sur mesure')+'<br>'+esc(q.desired_departure_date || 'Date à définir')+' · '+esc(q.traveler_count ?? '—')+' voyageur(s)</td><td><div class="quote-message">'+esc(q.message)+'</div></td><td><select data-status="'+q.id+'">'+['new','in_progress','replied','closed','spam'].map(s=>'<option value="'+s+'" '+(q.status===s?'selected':'')+'>'+s+'</option>').join('')+'</select></td></tr>').join('') || '<tr><td colspan="4">Aucune demande reçue.</td></tr>';
      $('#quotes-list').querySelectorAll('[data-status]').forEach(select => select.addEventListener('change', async () => {
        try { await request('admin/quotes.php', {method:'PATCH', mutate:true, body:JSON.stringify({id:Number(select.dataset.status),status:select.value})}); notice('Statut de la demande mis à jour.'); }
        catch (error) { notice(error.message); await loadQuotes(); }
      }));
    } catch (error) { notice(error.message); }
  }
  document.querySelectorAll('[data-tab]').forEach(button => button.addEventListener('click', async () => {
    document.querySelectorAll('[data-tab]').forEach(b=>b.classList.toggle('active',b===button));
    const quotes = button.dataset.tab === 'quotes';
    $('#circuits-panel').classList.toggle('hidden',quotes); $('#quotes-panel').classList.toggle('hidden',!quotes); $('#editor-panel').classList.add('hidden');
    if (quotes) await loadQuotes(); else await loadCircuits();
  }));
  checkSession();
})();
</script>
</body>
</html>

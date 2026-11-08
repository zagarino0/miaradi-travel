<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Administration — MIARADI-TRAVEL</title>
<style>
:root{--red:#922D20;--red-dark:#762218;--paper:#FBF8F5;--ink:#211d1a;--muted:#756b65;--line:#e8dfd8;--soft:#f5efe9;--white:#fff;--green:#237653;--green-bg:#e9f5ee;--amber:#986117;--amber-bg:#fff4dc;--blue:#315b91;--blue-bg:#edf3fb;--serif:Georgia,serif;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:linear-gradient(180deg,#f8f3ee 0,#fbf8f5 260px);color:var(--ink);font:14px/1.55 var(--sans)}button,input,select,textarea{font:inherit}button{cursor:pointer;transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease}button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible,a:focus-visible{outline:3px solid #c98c7e;outline-offset:2px}a{color:var(--red);text-underline-offset:3px}a:hover{color:var(--red-dark)}.shell{max-width:1360px;margin:auto;padding:30px 28px 72px}.top{display:flex;justify-content:space-between;align-items:center;gap:24px;padding:10px 0 26px;border-bottom:1px solid var(--line)}.brand{display:inline-flex;align-items:center;gap:9px;color:var(--red);font-size:11px;letter-spacing:.19em;font-weight:800;text-transform:uppercase}.brand:before{content:"";width:26px;height:2px;background:var(--red)}.top h1{font:normal clamp(30px,4vw,46px)/1.08 var(--serif);letter-spacing:-.035em;margin:10px 0 7px}.muted{color:var(--muted)}.top p{margin:13px 0 0}.button{border:1px solid var(--red);border-radius:7px;padding:11px 16px;background:var(--red);color:#fff;font-weight:650;box-shadow:0 3px 8px #922d2018}.button:hover{background:var(--red-dark);border-color:var(--red-dark);transform:translateY(-1px);box-shadow:0 5px 14px #922d2025}.button:disabled{opacity:.55;cursor:wait;transform:none;box-shadow:none}.button.secondary{background:#fff;color:var(--ink);border:1px solid #dcd1c8;box-shadow:none}.button.secondary:hover{background:var(--soft);border-color:#cbb9ad;color:var(--red)}.panel{margin-top:22px;padding:clamp(20px,3vw,32px);background:var(--white);border:1px solid var(--line);border-radius:13px;box-shadow:0 8px 28px #39251908}.panel h2{font-size:22px;letter-spacing:-.025em;margin:0 0 10px}.panel h3{font-size:18px;margin:0 0 15px}.login{max-width:480px;margin:9vh auto}.field{display:grid;gap:7px;margin:13px 0;font-weight:600}.field input,.field select,.field textarea{width:100%;padding:12px 13px;border:1px solid #ded4cc;border-radius:6px;background:#fdfbf9;color:var(--ink);font-weight:400;transition:border-color .18s,box-shadow .18s}.field input:focus,.field select:focus,.field textarea:focus{outline:0;border-color:var(--red);box-shadow:0 0 0 3px #922d2012}.field textarea{min-height:90px;resize:vertical}.row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.tabs{display:flex;gap:8px;margin:24px 0 18px;padding:6px;background:#eee6df;border-radius:9px;width:max-content;max-width:100%}.tabs button{padding:10px 18px;border:1px solid transparent;border-radius:6px;background:transparent;color:#655951;font-weight:650}.tabs button:hover{background:#fff8}.tabs button.active{background:var(--ink);color:#fff;box-shadow:0 2px 7px #211d1a20}.table-wrap{overflow:auto;border:1px solid var(--line);border-radius:9px;margin-top:18px}table{border-collapse:separate;border-spacing:0;width:100%;min-width:760px;background:#fff}th,td{text-align:left;padding:15px 14px;border-bottom:1px solid #eee7e1;vertical-align:top}th{position:sticky;top:0;background:#faf6f2;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#786b63;font-weight:800;white-space:nowrap}tbody tr:last-child td{border-bottom:0}tbody tr:hover td{background:#fdfaf7}td{overflow-wrap:anywhere}td strong{font-size:14px;font-weight:750}.status{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:999px;background:var(--soft);font-size:11px;font-weight:750;white-space:nowrap}.notice{min-height:24px;margin:12px 0;color:var(--red);font-weight:600}.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.quote-message{max-width:300px;white-space:pre-wrap;overflow-wrap:anywhere;color:#554b45;line-height:1.55;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden}.quote-status{min-width:155px;max-width:100%;padding:9px 30px 9px 11px;border:1px solid #ded4cc;border-radius:6px;background:#fff;color:var(--ink);font-weight:600}.quote-status[value="new"]{border-color:#e7c37d;background:var(--amber-bg)}.quote-status[value="in_progress"]{border-color:#b8cbea;background:var(--blue-bg)}.quote-status[value="replied"]{border-color:#a9d5ba;background:var(--green-bg)}.detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.detail-item{padding:15px 16px;background:#fcf9f6;border:1px solid var(--line);border-radius:8px;min-width:0}.detail-item strong{display:block;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#82756c;margin-bottom:7px}.detail-item p{margin:0;white-space:pre-wrap;overflow-wrap:anywhere}.detail-wide{grid-column:1/-1}.hidden{display:none!important}.hint{font-size:12px;color:var(--muted);font-weight:400}.cover-tools{display:grid;grid-template-columns:minmax(0,1fr) 180px;gap:16px;align-items:start}.cover-preview{width:100%;aspect-ratio:4/3;object-fit:cover;background:var(--soft);border:1px solid var(--line);border-radius:7px;display:block}.cover-preview-wrap{display:grid;gap:6px}.upload-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 15px;border:1px solid var(--red);border-radius:6px;background:var(--red);color:#fff;cursor:pointer;width:max-content;max-width:100%;font-weight:650}.upload-button:hover{background:var(--red-dark)}.cover-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.upload-progress{font-size:12px;color:var(--muted)}#quote-detail-panel{scroll-margin-top:18px}#quote-reply-form{margin-top:24px!important;padding-top:24px!important}#quote-send-reply{min-width:175px}#quote-reply-status{margin-top:12px}.top #logout{flex-shrink:0}
@media(min-width:1000px){#quotes-panel .table-wrap{margin-top:22px}#quotes-panel th,#quotes-panel td{padding:16px 15px}#quotes-panel th:nth-child(1){width:24%}#quotes-panel th:nth-child(2){width:23%}#quotes-panel th:nth-child(3){width:25%}#quotes-panel th:nth-child(4){width:17%}#quotes-panel th:nth-child(5){width:11%}}
@media(max-width:760px){.shell{padding:18px 13px 44px}.top{align-items:flex-start}.top h1{font-size:32px}.panel{padding:18px 14px;border-radius:10px}.tabs{width:100%}.tabs button{flex:1;padding:10px 8px}.row,.detail-grid{grid-template-columns:1fr}.detail-wide{grid-column:auto}.cover-tools{grid-template-columns:1fr}.cover-preview-wrap{max-width:240px}.table-wrap{margin-inline:-2px}#quotes-panel .actions>.button{width:auto}.top #logout{padding:9px 10px}.quote-status{min-width:140px}}
@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*:before,*:after{transition:none!important}}
</style>
</head>
<body>
<div class="shell">
  <header class="top"><div><div class="brand">MIARADI — ADMINISTRATION</div><h1>Gestion du site</h1><div class="muted">Circuits, demandes de devis et galerie multimédia</div><p><a href="gallery.php" style="color:var(--red);font-weight:700">Ouvrir la galerie photos et vidéos →</a></p></div><button class="button secondary hidden" id="logout">Déconnexion</button></header>
  <section class="panel login" id="login-panel"><h2>Connexion administrateur</h2><p class="muted">Utilisez le compte administrateur déjà créé dans la base MIARADI.</p><form id="login-form"><label class="field">Adresse e-mail<input type="email" name="email" autocomplete="username" required maxlength="254"></label><label class="field">Mot de passe<input type="password" name="password" autocomplete="current-password" required maxlength="1024"></label><button class="button" type="submit">Se connecter</button><p class="notice" id="login-status" role="status"></p></form></section>
  <main id="dashboard" class="hidden">
    <nav class="tabs"><button class="active" data-tab="circuits">Circuits</button><button data-tab="quotes">Demandes de devis</button></nav>
    <p class="notice" id="notice" role="status"></p>
    <section class="panel" id="circuits-panel"><div class="actions" style="justify-content:space-between;align-items:center"><h2>Catalogue des circuits</h2><button class="button" id="new-circuit">Ajouter un circuit</button></div><div class="table-wrap"><table><thead><tr><th>Circuit</th><th>Région / thème</th><th>Durée</th><th>État</th><th>Actions</th></tr></thead><tbody id="circuits-list"></tbody></table></div></section>
    <section class="panel hidden" id="quotes-panel"><div class="actions" style="justify-content:space-between;align-items:center"><h2>Demandes reçues</h2><button class="button secondary" id="refresh-quotes" type="button">Actualiser</button></div><p class="muted">Ouvrez une demande pour consulter ses détails, répondre au client et suivre son statut.</p><div class="table-wrap"><table><thead><tr><th>Contact</th><th>Voyage</th><th>Message</th><th>Statut</th><th>Actions</th></tr></thead><tbody id="quotes-list"></tbody></table></div></section>
    <section class="panel hidden" id="quote-detail-panel" aria-labelledby="quote-detail-title"><div class="actions" style="justify-content:space-between;align-items:center"><h2 id="quote-detail-title">Détail de la demande</h2><button class="button secondary" id="close-quote-detail" type="button">Fermer</button></div><div id="quote-detail-content"></div><form id="quote-reply-form" style="margin-top:20px;border-top:1px solid var(--line);padding-top:18px"><h3>Répondre au client</h3><label class="field">Objet de l’e-mail<input id="quote-reply-subject" name="subject" required maxlength="200" value="Votre demande de voyage — MIARADI-TRAVEL"></label><label class="field">Message<textarea id="quote-reply-message" name="message" required maxlength="10000" rows="8" placeholder="Rédigez votre réponse au client…"></textarea></label><div class="actions"><button class="button" id="quote-send-reply" type="submit">Envoyer la réponse</button><span class="hint">Le statut et la date ne changent que si le serveur de messagerie accepte l’envoi.</span></div><p class="notice" id="quote-reply-status" role="status"></p></form><div class="actions" style="margin-top:18px"><button class="button secondary" id="quote-detail-close-status" type="button">Clôturer la demande</button></div></section>
    <section class="panel hidden" id="editor-panel"><h2 id="editor-title">Modifier un circuit</h2><form id="circuit-form">
      <input type="hidden" name="id">
      <div class="row"><label class="field">Slug (URL)<input name="slug" required maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*"></label><label class="field">Ordre d'affichage<input name="sort_order" type="number" value="0" required></label></div>
      <div class="row"><label class="field">Titre français<input name="title_fr" required maxlength="200"></label><label class="field">Titre anglais<input name="title_en" maxlength="200"></label></div>
      <div class="row"><label class="field">Résumé français<textarea name="summary_fr" required maxlength="10000"></textarea></label><label class="field">Résumé anglais<textarea name="summary_en" maxlength="10000"></textarea></label></div>
      <div class="row"><label class="field">Région<input name="region" maxlength="80"></label><label class="field">Thème<input name="theme" maxlength="80"></label></div>
      <div class="row"><label class="field">Durée minimale (jours)<input name="duration_min" type="number" min="1" max="365"></label><label class="field">Durée maximale (jours)<input name="duration_max" type="number" min="1" max="365"></label></div>
      <div class="field"><label for="cover-file">Photo de couverture du circuit</label><div class="cover-tools"><div><div class="cover-actions"><label class="upload-button" for="cover-file">＋ Ajouter une photo de couverture</label><input id="cover-file" type="file" accept="image/jpeg,image/png,image/webp" hidden><span class="hint">JPG, PNG ou WebP · 10 Mo maximum</span></div><p class="hint" id="cover-status" role="status">Sélectionnez une photo pour remplacer la couverture actuelle.</p><label class="field">Chemin de l'image (avancé)<input name="image_path" maxlength="500" placeholder="assets/images/circuits/nom.jpg"></label></div><div class="cover-preview-wrap"><img id="cover-preview" class="cover-preview" alt="Aperçu de la couverture du circuit" hidden><span id="cover-empty" class="hint">Aucun aperçu disponible</span></div></div></div>
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
  let selectedCover = null;
  let coverPreviewUrl = null;
  const coverFile = () => $('#cover-file');
  function siteRoot() { const marker = '/backend/public/admin/'; const path = location.pathname; const index = path.indexOf(marker); return index >= 0 ? path.slice(0, index) : ''; }
  function showCoverPreview(path) { const img = $('#cover-preview'); if (coverPreviewUrl) { URL.revokeObjectURL(coverPreviewUrl); coverPreviewUrl = null; } const src = path ? (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/') ? path : siteRoot() + '/' + path) : ''; if (!src) { img.hidden = true; img.removeAttribute('src'); $('#cover-empty').hidden = false; return; } img.src = src; img.hidden = false; $('#cover-empty').hidden = true; img.onerror = () => { img.hidden = true; $('#cover-empty').hidden = false; $('#cover-empty').textContent = 'Aperçu indisponible pour ce chemin.'; }; img.onload = () => { $('#cover-empty').hidden = true; }; }
  coverFile().addEventListener('change', () => { selectedCover = coverFile().files?.[0] || null; if (coverPreviewUrl) URL.revokeObjectURL(coverPreviewUrl); if (selectedCover) { if (selectedCover.size > 10 * 1024 * 1024) { selectedCover = null; coverFile().value = ''; $('#cover-status').textContent = 'Fichier trop volumineux : 10 Mo maximum.'; return; } coverPreviewUrl = URL.createObjectURL(selectedCover); $('#cover-preview').src = coverPreviewUrl; $('#cover-preview').hidden = false; $('#cover-empty').hidden = true; $('#cover-status').textContent = selectedCover.name + ' — prêt à être envoyé à l’enregistrement.'; } else { $('#cover-status').textContent = 'Aucune nouvelle photo sélectionnée.'; showCoverPreview($('#circuit-form').elements.image_path.value); } });
  const api = (path) => '../api/' + path;
  async function request(path, options = {}) {
    const headers = {'Accept':'application/json', ...(options.body && !(options.body instanceof FormData) ? {'Content-Type':'application/json'} : {}), ...(options.mutate ? {'X-CSRF-Token':csrf} : {})};
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
    const form = $('#circuit-form'); form.reset(); selectedCover = null; coverFile().value = ''; $('#cover-status').textContent = 'Sélectionnez une photo pour remplacer la couverture actuelle.';
    const c = circuits.find(item => item.id === id);
    $('#editor-title').textContent = c ? 'Modifier un circuit' : 'Ajouter un circuit';
    for (const key of ['id','slug','title_fr','title_en','summary_fr','summary_en','region','theme','duration_min','duration_max','image_path','sort_order']) form.elements[key].value = c?.[key] ?? (key === 'sort_order' ? 0 : '');
    form.elements.is_published.checked = Boolean(c?.is_published); showCoverPreview(c?.image_path || '');
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
      if (selectedCover) {
        $('#cover-status').textContent = 'Envoi de la photo…';
        const upload = new FormData(); upload.append('cover', selectedCover);
        const uploaded = await request('admin/circuit-cover.php', {method:'POST', mutate:true, body:upload});
        payload.image_path = uploaded.data.image_path;
        selectedCover = null; coverFile().value = ''; $('#cover-status').textContent = 'Photo envoyée. Enregistrement du circuit…';
      }
      await request('admin/circuits.php', {method:id?'PATCH':'POST', mutate:true, body:JSON.stringify(payload)});
      $('#editor-panel').classList.add('hidden'); notice('Circuit enregistré.'); await loadCircuits();
    } catch (error) { notice(error.message); }
  });
  const quoteStatusLabels = {new:'Nouvelle demande',in_progress:'En cours de traitement',replied:'Client contacté',closed:'Clôturée',spam:'Indésirable'};
  let quoteRequests = [];
  let selectedQuote = null;
  function quoteStatusOptions(selected) {
    return Object.entries(quoteStatusLabels).map(([value,label]) => '<option value="'+value+'" '+(selected===value?'selected':'')+'>'+label+'</option>').join('');
  }
  function formatQuoteDate(value) {
    if (!value) return 'Non renseignée';
    const parsed = new Date(String(value).replace(' ', 'T'));
    return Number.isNaN(parsed.getTime()) ? String(value) : parsed.toLocaleString('fr-FR');
  }
  async function saveQuoteStatus(id, status, reload) {
    await request('admin/quotes.php', {method:'PATCH', mutate:true, body:JSON.stringify({id:Number(id),status})});
    const quote = quoteRequests.find(item => Number(item.id) === Number(id));
    if (quote) quote.status = status;
    notice('Statut enregistré : ' + quoteStatusLabels[status] + '.');
    if (reload) await loadQuotes();
    if (selectedQuote && Number(selectedQuote.id) === Number(id)) renderQuoteDetail(selectedQuote);
  }
  function renderQuoteDetail(q) {
    selectedQuote = q;
    const fields = [
      ['Nom du client', q.full_name],
      ['Adresse e-mail', q.email],
      ['Circuit demandé', q.circuit_title_fr || q.circuit_title_en || 'Voyage sur mesure'],
      ['Date de départ souhaitée', q.desired_departure_date || 'À définir'],
      ['Nombre de voyageurs', q.traveler_count ?? 'Non précisé'],
      ['Langue préférée', q.preferred_language === 'en' ? 'Anglais' : 'Français'],
      ['Date de réception', formatQuoteDate(q.created_at)],
      ['Date de réponse envoyée', formatQuoteDate(q.replied_at)],
      ['Consentement de confidentialité', q.privacy_consent_at ? 'Consentement enregistré' : 'Non indiqué'],
      ['Message du client', q.message || 'Aucun message fourni', true],
      ...(q.reply_message ? [['Dernière réponse envoyée', q.reply_message, true]] : [])
    ];
    $('#quote-detail-content').innerHTML = '<div class="detail-grid">' + fields.map(([label,value,wide]) => '<div class="detail-item '+(wide?'detail-wide':'')+'"><strong>'+esc(label)+'</strong><p>'+esc(value)+'</p></div>').join('') +
      '<div class="detail-item"><strong>Statut actuel</strong><label class="field"><select id="quote-detail-status" class="quote-status" aria-label="Statut de la demande">'+quoteStatusOptions(q.status)+'</select></label></div></div>';
    const subject = q.reply_subject || 'Votre demande de voyage — MIARADI-TRAVEL';
    const firstName = String(q.full_name || '').trim().split(/\s+/)[0] || 'Bonjour';
    const defaultBody = q.preferred_language === 'en'
      ? 'Thank you for contacting MIARADI-TRAVEL. We are reviewing your travel project and will get back to you with the next steps.'
      : 'Nous vous remercions d’avoir contacté MIARADI-TRAVEL. Nous étudions votre projet de voyage à Madagascar et reviendrons vers vous avec les prochaines étapes.';
    $('#quote-reply-subject').value = subject;
    $('#quote-reply-message').value = q.reply_message || defaultBody;
    $('#quote-reply-status').textContent = '';
    $('#quote-detail-status').addEventListener('change', async event => {
      const select = event.currentTarget;
      select.disabled = true;
      try { await saveQuoteStatus(q.id, select.value, true); }
      catch (error) { notice(error.message); select.value = q.status; }
      finally { select.disabled = false; }
    });
    const closeButton = $('#quote-detail-close-status');
    closeButton.textContent = q.status === 'closed' ? 'Demande clôturée' : 'Clôturer la demande';
    closeButton.disabled = q.status === 'closed';
    closeButton.onclick = async () => {
      try { await saveQuoteStatus(q.id, 'closed', true); }
      catch (error) { notice(error.message); }
    };
    $('#quote-detail-panel').classList.remove('hidden');
    $('#quote-detail-panel').scrollIntoView({behavior:'smooth',block:'start'});
  }
  $('#quote-reply-form').addEventListener('submit', async event => {
    event.preventDefault();
    if (!selectedQuote) return;
    const button = $('#quote-send-reply');
    const status = $('#quote-reply-status');
    const subject = $('#quote-reply-subject').value.trim();
    const message = $('#quote-reply-message').value.trim();
    if (!subject || !message) { status.textContent = 'Renseignez l’objet et le message.'; return; }
    button.disabled = true;
    status.textContent = 'Envoi en cours…';
    try {
      const result = await request('admin/quote-reply.php', {
        method: 'POST',
        mutate: true,
        body: JSON.stringify({id: Number(selectedQuote.id), subject, message})
      });
      selectedQuote.reply_subject = subject;
      selectedQuote.reply_message = message;
      selectedQuote.replied_at = result.data.replied_at;
      selectedQuote.status = 'replied';
      status.textContent = result.message || 'Réponse acceptée par le serveur de messagerie.';
      notice('Réponse envoyée au transport de messagerie. Statut mis à jour : Client contacté.');
      await loadQuotes();
      const refreshed = quoteRequests.find(item => Number(item.id) === Number(selectedQuote.id)) || selectedQuote;
      renderQuoteDetail(refreshed);
    } catch (error) {
      status.textContent = error.message;
      notice(error.message);
    } finally {
      button.disabled = false;
    }
  });
  async function loadQuotes() {
    try {
      const result = await request('admin/quotes.php');
      quoteRequests = result.data || [];
      $('#quotes-list').innerHTML = quoteRequests.map(q => '<tr><td><strong>'+esc(q.full_name)+'</strong><br><a href="mailto:'+esc(q.email)+'">'+esc(q.email)+'</a><br><span class="muted">'+esc(formatQuoteDate(q.created_at))+'</span></td><td>'+esc(q.circuit_title_fr || q.circuit_title_en || 'Sur mesure')+'<br>'+esc(q.desired_departure_date || 'Date à définir')+' · '+esc(q.traveler_count ?? '—')+' voyageur(s)</td><td><div class="quote-message">'+esc(q.message)+'</div></td><td><select class="quote-status" data-status="'+q.id+'" aria-label="Statut de la demande">'+quoteStatusOptions(q.status)+'</select></td><td><div class="actions"><button class="button secondary" type="button" data-detail="'+q.id+'">Détails / répondre</button></div></td></tr>').join('') || '<tr><td colspan="5">Aucune demande reçue.</td></tr>';
      $('#quotes-list').querySelectorAll('[data-status]').forEach(select => select.addEventListener('change', async () => {
        const quote = quoteRequests.find(item => Number(item.id) === Number(select.dataset.status));
        const previous = quote ? quote.status : 'new';
        select.disabled = true;
        try { await saveQuoteStatus(select.dataset.status, select.value, false); }
        catch (error) { notice(error.message); select.value = previous; }
        finally { select.disabled = false; }
      }));
      $('#quotes-list').querySelectorAll('[data-detail]').forEach(button => button.addEventListener('click', async () => {
        const quote = quoteRequests.find(item => Number(item.id) === Number(button.dataset.detail));
        if (!quote) return;

        // Prendre automatiquement en charge une nouvelle demande dès son ouverture.
        if (quote.status === 'new') {
          button.disabled = true;
          try {
            await request('admin/quotes.php', {
              method: 'PATCH',
              mutate: true,
              body: JSON.stringify({id: Number(quote.id), status: 'in_progress'})
            });
            quote.status = 'in_progress';
            notice('Demande prise en charge automatiquement.');
            await loadQuotes();
            const refreshedQuote = quoteRequests.find(item => Number(item.id) === Number(quote.id)) || quote;
            renderQuoteDetail(refreshedQuote);
          } catch (error) {
            notice(error.message);
            renderQuoteDetail(quote);
          } finally {
            button.disabled = false;
          }
          return;
        }

        renderQuoteDetail(quote);
      }));
    } catch (error) { notice(error.message); }
  }
  $('#refresh-quotes').addEventListener('click', loadQuotes);
  $('#close-quote-detail').addEventListener('click', () => $('#quote-detail-panel').classList.add('hidden'));
  document.querySelectorAll('[data-tab]').forEach(button => button.addEventListener('click', async () => {
    document.querySelectorAll('[data-tab]').forEach(b=>b.classList.toggle('active',b===button));
    const quotes = button.dataset.tab === 'quotes';
    $('#circuits-panel').classList.toggle('hidden',quotes); $('#quotes-panel').classList.toggle('hidden',!quotes); $('#editor-panel').classList.add('hidden'); if (!quotes) $('#quote-detail-panel').classList.add('hidden');
    if (quotes) await loadQuotes(); else await loadCircuits();
  }));
  checkSession();
})();
</script>
</body>
</html>

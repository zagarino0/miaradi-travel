<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#922D20">
<title>Créer le premier compte — MIARADI-TRAVEL</title>
<style>
:root{color-scheme:light;--red:#922D20;--red-dark:#762218;--paper:#FBF8F5;--ink:#28201d;--muted:#756861;--line:#d9d0ca;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
*{box-sizing:border-box}html{min-height:100%;background:var(--paper)}body{margin:0;min-height:100vh;min-height:100svh;display:flex;align-items:center;justify-content:center;padding:28px 16px 70px;font:15px/1.5 var(--sans);color:var(--ink);position:relative;isolation:isolate}
body:before{content:"";position:fixed;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(251,248,245,.88),rgba(251,248,245,.68) 55%,rgba(251,248,245,.25)),url("../../../assets/images/circuits/majunga.jpg") center/cover no-repeat}
body:after{content:"";position:fixed;z-index:-1;bottom:0;left:0;width:100%;height:74px;background:var(--red);clip-path:polygon(0 25%,20% 60%,42% 80%,65% 44%,82% 57%,100% 20%,100% 100%,0 100%)}
.shell{width:min(100%,580px);display:flex;flex-direction:column;align-items:center;gap:22px;padding-bottom:18px}.brand img{display:block;width:120px;height:100px;object-fit:contain}.card{width:100%;padding:34px 38px 30px;border:1px solid rgba(255,255,255,.8);border-radius:20px;background:rgba(255,255,255,.93);box-shadow:0 18px 55px rgba(64,38,24,.17);backdrop-filter:blur(9px)}
h1{margin:0;text-align:center;font-size:clamp(25px,4vw,32px);line-height:1.2;letter-spacing:-.04em;color:#171412}.accent{width:62px;height:5px;border-radius:99px;background:var(--red);margin:16px auto}.intro{text-align:center;margin:0 0 26px;color:#51443d}
.field{display:block;margin:0 0 17px}.field-label{display:block;margin-bottom:7px;font-weight:650;font-size:13px}.input-wrap{position:relative;display:flex;align-items:center}input{width:100%;height:54px;border:1px solid #d5cec9;border-radius:8px;background:#fff;padding:0 48px 0 15px;color:var(--ink);font:inherit;outline:none;transition:border-color .18s,box-shadow .18s}input:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(146,45,32,.12)}input::placeholder{color:#8b817a}.toggle{position:absolute;right:6px;width:38px;height:38px;border:0;border-radius:6px;background:transparent;color:#51443d;cursor:pointer}.toggle:hover{background:#f3e9e3}
.consent{display:flex;gap:10px;align-items:flex-start;margin:21px 0 4px;font-size:13px;color:#51443d}.consent input{width:17px;height:17px;min-width:17px;margin:2px 0 0;accent-color:var(--red)}
.submit{display:flex;align-items:center;justify-content:center;width:100%;min-height:56px;margin-top:22px;border:1px solid var(--red);border-radius:8px;background:var(--red);color:#fff;font:700 16px var(--sans);cursor:pointer;transition:background .18s}.submit:hover{background:var(--red-dark)}.submit:disabled{opacity:.55;cursor:not-allowed}
.status{min-height:22px;margin:12px 0 0;text-align:center;color:var(--red);font-size:13px;font-weight:600}.status.success{color:#286344}.foot{text-align:center;margin:19px 0 0;font-size:13px;color:#66564d}.foot a{color:var(--red);text-underline-offset:3px}.security{margin:0;text-align:center;color:#66564d;font-size:11px}
button:focus-visible,a:focus-visible,input:focus-visible{outline:3px solid #c98c7e;outline-offset:3px}
@media(max-width:520px){.card{padding:27px 20px 24px;border-radius:16px}.brand img{width:100px;height:84px}.intro{font-size:14px}.submit{min-height:53px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important}}
</style>
</head>
<body>
<main class="shell">
  <a class="brand" href="../../../index.html" aria-label="MIARADI-TRAVEL — accueil"><img src="../../../assets/miaradi-logo.png" alt="Logo MIARADI-TRAVEL"></a>
  <section class="card" aria-labelledby="register-title">
    <h1 id="register-title">Créer le compte administrateur</h1>
    <div class="accent" aria-hidden="true"></div>
    <p class="intro" id="intro">Configurez le premier accès à votre espace de gestion MIARADI.</p>
    <form id="register-form" hidden>
      <label class="field"><span class="field-label">Nom complet</span><input name="displayName" autocomplete="name" placeholder="Votre nom complet" required maxlength="180"></label>
      <label class="field"><span class="field-label">Adresse e-mail professionnelle</span><input name="email" type="email" autocomplete="email" placeholder="nom@exemple.com" required maxlength="254"></label>
      <label class="field"><span class="field-label">Mot de passe (12 caractères minimum)</span><span class="input-wrap"><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" placeholder="Choisissez un mot de passe fort" required><button class="toggle" type="button" data-toggle="password" aria-label="Afficher le mot de passe">◉</button></span></label>
      <label class="field"><span class="field-label">Confirmer le mot de passe</span><span class="input-wrap"><input id="password-confirm" name="passwordConfirm" type="password" autocomplete="new-password" minlength="12" maxlength="72" placeholder="Saisissez à nouveau le mot de passe" required><button class="toggle" type="button" data-toggle="password-confirm" aria-label="Afficher la confirmation">◉</button></span></label>
      <label class="consent"><input type="checkbox" name="authorized" required><span>Je confirme être autorisé à initialiser l’espace d’administration de MIARADI-TRAVEL.</span></label>
      <button class="submit" id="submit-button" type="submit">Créer mon compte</button>
    </form>
    <p class="status" id="status" role="status" aria-live="polite">Vérification de la disponibilité…</p>
    <p class="foot"><a href="login.php">Retour à la connexion</a></p>
  </section>
  <p class="security">Initialisation réservée au premier administrateur · Mot de passe protégé</p>
</main>
<script>
(() => {
  const form = document.querySelector('#register-form');
  const status = document.querySelector('#status');
  const intro = document.querySelector('#intro');
  const submit = document.querySelector('#submit-button');
  let csrfToken = '';
  let setupAvailable = false;
  document.querySelectorAll('[data-toggle]').forEach(button => button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.toggle);
    input.type = input.type === 'password' ? 'text' : 'password';
    button.setAttribute('aria-label', input.type === 'password' ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
  }));
  async function checkSetup() {
    try {
      const response = await fetch('../api/admin/register.php', {credentials:'same-origin',headers:{'Accept':'application/json'}});
      const result = await response.json();
      if (!response.ok) throw new Error(result.error?.message || 'Vérification impossible.');
      setupAvailable = Boolean(result.data?.available);
      csrfToken = result.data?.csrfToken || '';
      if (setupAvailable) {
        form.hidden = false;
        status.textContent = '';
      } else {
        intro.textContent = 'Un compte administrateur existe déjà. La création publique de comptes est désactivée.';
        status.textContent = 'Initialisation déjà effectuée. Connectez-vous avec votre compte administrateur.';
      }
    } catch (error) {
      intro.textContent = 'La page ne peut pas vérifier l’état de l’initialisation.';
      status.textContent = error.message || 'Service temporairement indisponible.';
      submit.disabled = true;
    }
  }
  form.addEventListener('submit', async event => {
    event.preventDefault();
    const data = new FormData(form);
    const password = String(data.get('password') || '');
    const confirmation = String(data.get('passwordConfirm') || '');
    if (password !== confirmation) { status.textContent = 'Les deux mots de passe ne correspondent pas.'; return; }
    if (password.length < 12) { status.textContent = 'Le mot de passe doit contenir au moins 12 caractères.'; return; }
    if (!data.get('authorized')) { status.textContent = 'Confirmez votre autorisation pour continuer.'; return; }
    submit.disabled = true;
    status.classList.remove('success');
    status.textContent = 'Création du compte en cours…';
    try {
      const response = await fetch('../api/admin/register.php', {
        method:'POST',credentials:'same-origin',
        headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-Token':csrfToken},
        body:JSON.stringify({displayName:data.get('displayName'),email:data.get('email'),password,authorized:true})
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(result.error?.message || 'La création du compte a échoué.');
      status.classList.add('success');
      status.textContent = 'Compte créé. Redirection vers votre espace…';
      window.setTimeout(() => window.location.replace('index.php'), 700);
    } catch (error) {
      status.textContent = error.message || 'Service temporairement indisponible.';
      submit.disabled = false;
      await checkSetup();
    }
  });
  checkSetup();
})();
</script>
</body>
</html>

<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

// Ne rediriger vers le tableau de bord que si la session correspond encore
// à un compte actif et à sa version d'authentification en base. Une ancienne
// session partielle ne doit pas créer de boucle login.php <-> index.php.
if (
    !empty($_SESSION['admin_id'])
    && !empty($_SESSION['admin_email'])
    && isset($_SESSION['admin_auth_version'])
) {
    try {
        $statement = database_connection()->prepare(
            'SELECT email, auth_version, is_active FROM miaradi_admin_users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $_SESSION['admin_id']]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        if (
            $admin
            && (int) $admin['is_active'] === 1
            && (int) $admin['auth_version'] === (int) $_SESSION['admin_auth_version']
            && strtolower((string) $admin['email']) === strtolower((string) $_SESSION['admin_email'])
        ) {
            header('Location: index.php', true, 302);
            exit;
        }
    } catch (Throwable $exception) {
        error_log('[miaradi-admin] Login session validation failed: ' . $exception->getMessage());
    }
}

// Nettoyer les restes de session incomplets ou révoqués afin de permettre
// une nouvelle connexion propre, sans conserver de privilèges partiels.
unset(
    $_SESSION['admin_id'],
    $_SESSION['admin_email'],
    $_SESSION['admin_auth_version'],
    $_SESSION['csrf_token']
);
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#922D20">
<title>Connexion administrateur — MIARADI-TRAVEL</title>
<style>
:root{color-scheme:light;--red:#922D20;--red-dark:#762218;--paper:#FBF8F5;--ink:#28201d;--muted:#756861;--line:#d9d0ca;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
*{box-sizing:border-box}html{min-height:100%;background:var(--paper)}body{margin:0;min-height:100vh;min-height:100svh;display:flex;align-items:center;justify-content:center;padding:36px 20px 76px;font:15px/1.5 var(--sans);color:var(--ink);position:relative;isolation:isolate;overflow-x:hidden}
body:before{content:"";position:fixed;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(251,248,245,.82) 0%,rgba(251,248,245,.52) 42%,rgba(251,248,245,.05) 100%),url("../../../assets/images/circuits/majunga.jpg") center 56%/cover no-repeat;transform:scale(1.01)}
body:after{content:"";position:fixed;z-index:-1;bottom:-1px;left:0;width:100%;height:100px;background:var(--red);clip-path:polygon(0 24%,8% 46%,17% 62%,27% 78%,39% 91%,51% 73%,62% 55%,72% 62%,82% 38%,91% 48%,100% 16%,100% 100%,0 100%)}
.login-shell{width:min(100%,640px);display:flex;flex-direction:column;align-items:center;gap:27px;padding:0 0 18px}
.brand{display:flex;flex-direction:column;align-items:center;text-align:center;color:var(--red);text-decoration:none}
.brand img{display:block;width:132px;height:112px;object-fit:contain;margin-bottom:0}
.card{width:min(100%,640px);padding:35px 40px 32px;border:1px solid rgba(255,255,255,.78);border-radius:22px;background:rgba(255,255,255,.91);box-shadow:0 18px 55px rgba(64,38,24,.17);backdrop-filter:blur(9px)}
h1{margin:0;text-align:center;font-size:clamp(25px,4vw,34px);line-height:1.2;letter-spacing:-.04em;color:#171412}
.accent{width:66px;height:5px;border-radius:99px;background:var(--red);margin:17px auto 18px}
.intro{text-align:center;margin:0 0 30px;color:#51443d;font-size:17px}
.field{display:block;margin:0 0 18px}
.field-label{display:block;margin-bottom:7px;font-weight:650;font-size:13px}
.input-wrap{position:relative;display:flex;align-items:center}
.field-icon{position:absolute;left:17px;width:20px;height:20px;color:#55443b;pointer-events:none}
input{width:100%;height:58px;border:1px solid #d5cec9;border-radius:8px;background:rgba(255,255,255,.76);padding:0 48px 0 51px;color:var(--ink);font:inherit;outline:none;transition:border-color .18s,box-shadow .18s}
input::placeholder{color:#756d68}
input:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(146,45,32,.12)}
.toggle-password{position:absolute;right:9px;display:grid;place-items:center;width:38px;height:38px;border:0;border-radius:6px;background:transparent;color:#51443d}
.toggle-password:hover{background:#f3e9e3}
.submit{display:flex;align-items:center;justify-content:center;gap:11px;width:100%;min-height:59px;margin-top:25px;border:1px solid var(--red);border-radius:8px;background:var(--red);color:#fff;font:700 17px var(--sans);box-shadow:0 5px 12px rgba(146,45,32,.13);transition:background .18s,transform .18s}
.submit:hover{background:var(--red-dark);transform:translateY(-1px)}.submit:disabled{opacity:.65;cursor:wait;transform:none}
.status{min-height:22px;margin:12px 0 0;text-align:center;color:var(--red);font-size:13px;font-weight:600}
.help{display:flex;align-items:center;justify-content:center;gap:9px;margin:20px 0 0;color:#5d4940;font-size:14px;text-align:center}
.help a{color:inherit;text-underline-offset:3px}.help svg{color:var(--red)}\n.setup-link{display:block;margin:13px 0 0;text-align:center;font-size:13px;color:var(--red);text-underline-offset:3px}
.security{margin:0;text-align:center;color:#74665d;font-size:11px}
button:focus-visible,a:focus-visible,input:focus-visible{outline:3px solid #c98c7e;outline-offset:3px}
@media(max-width:520px){body{padding:24px 14px 75px}.login-shell{gap:20px}.brand img{width:108px;height:92px}.card{padding:27px 20px 24px;border-radius:17px}.intro{font-size:15px;margin-bottom:24px}input{height:54px}.submit{min-height:55px}.security{font-size:10px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important}}
</style>
</head>
<body>
<main class="login-shell">
  <a class="brand" href="../../../index.html" aria-label="MIARADI-TRAVEL — accueil">
    <img src="../../../assets/miaradi-logo.png" alt="Logo MIARADI-TRAVEL">
  </a>
  <section class="card" aria-labelledby="login-title">
    <h1 id="login-title">Connexion administrateur</h1>
    <div class="accent" aria-hidden="true"></div>
    <p class="intro">Accédez à votre espace de gestion MIARADI.</p>
    <form id="login-form">
      <label class="field">
        <span class="field-label">Adresse e-mail</span>
        <span class="input-wrap">
          <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
          <input type="email" name="email" placeholder="Adresse e-mail" autocomplete="username" required maxlength="254" autofocus>
        </span>
      </label>
      <label class="field">
        <span class="field-label">Mot de passe</span>
        <span class="input-wrap">
          <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v3"/></svg>
          <input id="password" type="password" name="password" placeholder="Mot de passe" autocomplete="current-password" required maxlength="1024">
          <button class="toggle-password" type="button" id="toggle-password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
            <svg id="eye-icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path d="m3 3 18 18"/></svg>
          </button>
        </span>
      </label>
      <button class="submit" id="submit-button" type="submit">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg>
        <span>Se connecter</span>
      </button>
      <p class="status" id="login-status" role="status" aria-live="polite"></p>
    </form>
    <p class="help"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="8" cy="16" r="3"/><path d="m10 14 8-8 3 3-8 8M17 7l2 2"/></svg><a href="forgot-password.php">Mot de passe oublié ?</a></p>
  </section>
  <p class="security">Espace réservé aux administrateurs autorisés · Connexion sécurisée</p>
</main>
<script>
(() => {
  const form = document.querySelector('#login-form');
  const status = document.querySelector('#login-status');
  const submit = document.querySelector('#submit-button');
  const password = document.querySelector('#password');
  const toggle = document.querySelector('#toggle-password');
  toggle.addEventListener('click', () => {
    const show = password.type === 'password';
    password.type = show ? 'text' : 'password';
    toggle.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    toggle.title = show ? 'Masquer le mot de passe' : 'Afficher le mot de passe';
    document.querySelector('#eye-icon').innerHTML = show
      ? '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>'
      : '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path d="m3 3 18 18"/>';
  });
  form.addEventListener('submit', async event => {
    event.preventDefault();
    status.textContent = 'Connexion en cours…';
    submit.disabled = true;
    const data = new FormData(form);
    try {
      const response = await fetch('../api/admin/session.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Accept':'application/json','Content-Type':'application/json'},
        body: JSON.stringify({email:data.get('email'),password:data.get('password')})
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok || !result.data?.authenticated) throw new Error(result.error?.message || 'Connexion impossible. Vérifiez vos identifiants.');
      window.location.replace('index.php');
    } catch (error) {
      status.textContent = error.message || 'Le service de connexion est temporairement indisponible.';
      submit.disabled = false;
    }
  });
  fetch('../api/admin/session.php', {credentials:'same-origin',headers:{'Accept':'application/json'}})
    .then(response => response.ok ? response.json() : null)
    .then(result => { if (result?.data?.authenticated) window.location.replace('index.php'); })
    .catch(() => {});
})();
</script>
</body>
</html>

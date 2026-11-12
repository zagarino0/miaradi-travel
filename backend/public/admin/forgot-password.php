<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (!empty($_SESSION['admin_id']) && !empty($_SESSION['admin_email'])) {
    header('Location: index.php', true, 302);
    exit;
}
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#922D20">
<title>Mot de passe oublié — MIARADI-TRAVEL</title>
<style>
:root{--red:#922D20;--dark:#762218;--paper:#FBF8F5;--ink:#28201d;--muted:#756861;--line:#d9d0ca;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
*{box-sizing:border-box}body{margin:0;min-height:100vh;min-height:100svh;display:flex;align-items:center;justify-content:center;padding:30px 16px 76px;font:15px/1.5 var(--sans);color:var(--ink);position:relative;isolation:isolate}
body:before{content:"";position:fixed;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(251,248,245,.82),rgba(251,248,245,.25)),url("../../../assets/images/circuits/majunga.jpg") center/cover no-repeat}
body:after{content:"";position:fixed;z-index:-1;bottom:-1px;left:0;width:100%;height:72px;background:var(--red);clip-path:polygon(0 22%,20% 48%,42% 90%,62% 58%,82% 35%,100% 15%,100% 100%,0 100%)}
.shell{width:min(100%,640px);display:flex;flex-direction:column;align-items:center;gap:22px}.brand img{display:block;width:124px;height:96px;object-fit:contain}
.card{width:100%;padding:clamp(25px,5vw,42px);border:1px solid #ffffffc9;border-radius:22px;background:#fffffff0;box-shadow:0 18px 55px #4026182b;backdrop-filter:blur(9px)}
h1{text-align:center;margin:0;font-size:clamp(27px,4vw,35px);line-height:1.2;letter-spacing:-.04em;color:#171412}.accent{width:66px;height:5px;border-radius:99px;background:var(--red);margin:17px auto}.intro{text-align:center;color:#65564e;margin:0 auto 28px;max-width:460px}
label{display:block;font-weight:700;font-size:13px;margin-bottom:8px}input{width:100%;height:57px;padding:0 15px;border:1px solid #d5cec9;border-radius:9px;background:#fff;color:var(--ink);font:inherit;outline:none}input:focus{border-color:var(--red);box-shadow:0 0 0 3px #922d201f}
button{width:100%;min-height:57px;margin-top:22px;border:1px solid var(--red);border-radius:9px;background:var(--red);color:white;font:750 16px var(--sans);cursor:pointer}button:hover{background:var(--dark)}button:disabled{opacity:.65;cursor:wait}
.status{min-height:22px;margin:14px 0 0;text-align:center;font-size:13px;color:#756861}.status.error{color:#a3261b}.status.success{color:#246c45}
.back{display:block;margin-top:23px;text-align:center;color:#5d4940;text-underline-offset:3px;font-size:14px}.security{text-align:center;margin:0;color:#74665d;font-size:11px}
:focus-visible{outline:3px solid #c98c7e;outline-offset:3px}
@media(max-width:500px){.card{border-radius:17px}.brand img{width:105px;height:82px}}
@media(prefers-reduced-motion:reduce){*{transition:none!important}}
</style>
</head>
<body><main class="shell">
<a class="brand" href="../../../index.html" aria-label="Accueil MIARADI"><img src="../../../assets/miaradi-logo.png" alt="Logo MIARADI-TRAVEL"></a>
<section class="card" aria-labelledby="title">
<h1 id="title">Mot de passe oublié ?</h1><div class="accent" aria-hidden="true"></div>
<p class="intro">Indiquez l’adresse e-mail associée à votre compte administrateur. Si elle correspond à un compte actif, vous recevrez un lien sécurisé valable 30 minutes.</p>
<form id="request-form">
<label for="email">Adresse e-mail</label>
<input id="email" name="email" type="email" autocomplete="email" maxlength="254" placeholder="admin@miaradi.mg" required autofocus>
<button id="submit" type="submit">Envoyer le lien de réinitialisation</button>
<p class="status" id="status" role="status" aria-live="polite"></p>
</form>
<a class="back" href="login.php">← Retour à la connexion</a>
</section><p class="security">Espace réservé aux administrateurs autorisés · Connexion sécurisée</p>
</main>
<script>
const form=document.getElementById('request-form'),statusNode=document.getElementById('status'),button=document.getElementById('submit');
form.addEventListener('submit',async event=>{event.preventDefault();button.disabled=true;statusNode.className='status';statusNode.textContent='Envoi en cours…';try{const response=await fetch('../api/admin/password-reset.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({action:'request',email:form.elements.email.value})});const payload=await response.json().catch(()=>({}));if(!response.ok)throw new Error(payload.error?.message||'Impossible de traiter la demande.');statusNode.className='status success';statusNode.textContent=payload.data?.message||'Si cette adresse correspond à un compte actif, un lien sera envoyé.';form.reset()}catch(error){statusNode.className='status error';statusNode.textContent=error.message||'Le service est temporairement indisponible.'}finally{button.disabled=false}});
</script>
</body></html>

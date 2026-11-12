<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';
$lang = strtolower((string) ($_GET['lang'] ?? 'fr'));
$lang = in_array($lang, ['fr', 'en'], true) ? $lang : 'fr';
$policy = null;
try {
    $stmt = database_connection()->query('SELECT title_fr, title_en, content_fr, content_en, is_published, updated_at FROM miaradi_privacy_policy WHERE id = 1 LIMIT 1');
    $policy = $stmt->fetch() ?: null;
} catch (Throwable $exception) {
    error_log('[miaradi] Privacy policy page unavailable: ' . $exception->getMessage());
}
function privacy_escape(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$isAdmin = !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_email']);
if (!$policy || (!$isAdmin && !(bool) $policy['is_published'])) { http_response_code(404); }
$title = $policy ? (string) ($lang === 'en' ? $policy['title_en'] : $policy['title_fr']) : 'Politique de confidentialité';
$content = $policy ? (string) ($lang === 'en' ? $policy['content_en'] : $policy['content_fr']) : 'Cette politique n’est pas encore publiée.';
?><!doctype html>
<html lang="<?=privacy_escape($lang)?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?=privacy_escape($title)?> — MIARADI-TRAVEL</title>
<style>:root{--red:#922d20;--paper:#fbf8f5;--ink:#172238;--muted:#65738a;--line:#eadfdd}*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.8 Inter,"Segoe UI",Arial,sans-serif}header{padding:20px clamp(18px,5vw,64px);background:#fff;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:16px}.brand{font-weight:850;color:var(--red);letter-spacing:.06em}a{color:var(--red)}main{max-width:900px;margin:auto;padding:clamp(36px,7vw,80px) 22px 90px}.eyebrow{color:var(--red);font-size:11px;font-weight:800;letter-spacing:.18em}h1{font:clamp(34px,6vw,56px)/1.12 Georgia,serif;letter-spacing:-.04em;margin:10px 0 30px}.content{white-space:pre-wrap;color:#514c4a}.notice{padding:14px;border:1px solid var(--line);border-radius:10px;background:#fff;color:var(--muted);margin-top:24px}</style></head>
<body><header><a class="brand" href="../../index.html" style="text-decoration:none">MIARADI</a><a href="../../index.html#contact"><?= $lang === 'en' ? 'Back to quote request' : 'Retour à la demande de devis' ?></a></header><main><p class="eyebrow"><?= $lang === 'en' ? 'YOUR PRIVACY' : 'VOS DONNÉES PERSONNELLES' ?></p><h1><?=privacy_escape($title)?></h1><div class="content"><?=privacy_escape($content)?></div><?php if (!$policy || (!$isAdmin && !(bool) $policy['is_published'])): ?><p class="notice"><?= $lang === 'en' ? 'This policy has not been published yet.' : 'Cette politique n’a pas encore été publiée.' ?> <a href="admin/privacy-policy.php"><?= $lang === 'en' ? 'Configure it in admin' : 'La configurer dans l’administration' ?></a></p><?php endif; ?></main></body></html>

<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Administration — MIARADI-TRAVEL</title>
<style>
:root{--red:#922D20;--red-dark:#762218;--paper:#f4f1ed;--ink:#24211f;--muted:#827a74;--line:#e2ddd8;--nav:#202733;--nav-muted:#aeb7c3;--white:#fff;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--paper);color:var(--ink);font:14px/1.55 var(--sans)}button,input,select,textarea{font:inherit}button{cursor:pointer;transition:background .18s,border-color .18s,transform .18s,box-shadow .18s}button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible,a:focus-visible{outline:3px solid #c98c7e;outline-offset:2px}a{color:var(--red);text-underline-offset:3px}.hidden{display:none!important}
.app-shell{min-height:100vh;display:grid;grid-template-columns:236px minmax(0,1fr)}.sidebar{background:var(--nav);color:#fff;padding:25px 14px;display:flex;flex-direction:column;gap:26px;min-height:100vh}.side-brand{font-size:18px;letter-spacing:.07em;font-weight:800;padding:0 11px 23px;border-bottom:1px solid #ffffff20}.side-brand span{font-weight:400;color:#c5cbd3}.side-label{padding:0 12px;color:#8591a0;font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;margin-bottom:8px}.side-nav{display:grid;gap:5px}.side-nav button,.side-nav a{width:100%;display:flex;align-items:center;gap:12px;text-align:left;border:0;border-radius:7px;padding:12px 13px;background:transparent;color:#d3d9e0;text-decoration:none;font-weight:550}.side-nav button:hover,.side-nav a:hover{background:#ffffff10;color:#fff}.side-nav button.active{background:#922d20;color:#fff;box-shadow:0 4px 12px #0002}.nav-icon{width:18px;text-align:center;font-size:16px;opacity:.9}.side-bottom{margin-top:auto;color:#96a1ae;font-size:11px;padding:12px}
.workspace{min-width:0}.topbar{height:64px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 30px;gap:18px;position:sticky;top:0;z-index:5}.topbar-left{display:flex;align-items:center;gap:15px}.crumb{color:#7d7771;font-size:12px}.topbar-actions{display:flex;align-items:center;gap:12px}.topbar-icon{width:35px;height:35px;display:grid;place-items:center;border:1px solid var(--line);border-radius:50%;color:#5d5a57;background:#fff}.avatar{width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:#f0ddd5;color:var(--red);font-weight:800}.content{max-width:1600px;margin:auto;padding:28px 30px 60px}.page-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:23px}.eyebrow{font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--red);font-weight:800}.page-heading h1{font-size:29px;line-height:1.2;letter-spacing:-.035em;margin:5px 0 7px}.page-heading p{margin:0;color:var(--muted)}.button{border:1px solid var(--red);border-radius:5px;padding:11px 16px;background:var(--red);color:#fff;font-weight:650;box-shadow:none}.button:hover{background:var(--red-dark);border-color:var(--red-dark);transform:translateY(-1px)}.button:disabled{opacity:.55;cursor:wait;transform:none}.button.secondary{background:#fff;color:var(--ink);border:1px solid #d8d2cc;box-shadow:none}.button.secondary:hover{background:#f7f2ed;border-color:#bba99e;color:var(--red)}
.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin:0 0 22px}.kpi{min-height:118px;border-radius:6px;color:#fff;padding:17px 18px;position:relative;overflow:hidden;box-shadow:0 4px 12px #27211b0d}.kpi:nth-child(1){background:#5d57d9}.kpi:nth-child(2){background:#3296ef}.kpi:nth-child(3){background:#e9a91b}.kpi:nth-child(4){background:#d95c61}.kpi-label{font-size:12px;opacity:.92}.kpi-value{font-size:29px;line-height:1.15;font-weight:800;letter-spacing:-.04em;margin:8px 0 3px}.kpi-note{font-size:11px;opacity:.88}.kpi-mark{position:absolute;right:16px;top:12px;font-size:19px;opacity:.65}
.panel{margin-top:20px;padding:25px;background:#fff;border:1px solid var(--line);border-radius:7px;box-shadow:0 3px 12px #2c201408}.panel h2{font-size:21px;letter-spacing:-.025em;margin:0 0 9px}.panel h3{font-size:18px;margin:0 0 15px}.login{max-width:480px;margin:9vh auto}.field{display:grid;gap:7px;margin:13px 0;font-weight:600}.field input,.field select,.field textarea{width:100%;padding:12px 13px;border:1px solid #d9d3cd;border-radius:3px;background:#fcfaf8;color:var(--ink);font-weight:400}.field input:focus,.field select:focus,.field textarea:focus{outline:0;border-color:var(--red);box-shadow:0 0 0 3px #922d2012}.field textarea{min-height:90px;resize:vertical}.row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.tabs{display:flex;gap:5px;margin:0 0 20px;padding:0;border-bottom:1px solid var(--line);width:100%;max-width:100%}.tabs button{padding:12px 17px;border:0;border-bottom:3px solid transparent;background:transparent;color:#716963;font-weight:700}.tabs button:hover{background:#eee8e2}.tabs button.active{border-bottom-color:var(--red);color:var(--red);background:transparent}.table-wrap{overflow:auto;border:1px solid var(--line);border-radius:5px;margin-top:18px}table{border-collapse:separate;border-spacing:0;width:100%;min-width:760px;background:#fff}th,td{text-align:left;padding:14px 13px;border-bottom:1px solid #eee9e4;vertical-align:top}th{position:sticky;top:64px;background:#f8f6f3;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#756b63;font-weight:800;white-space:nowrap}tbody tr:last-child td{border-bottom:0}tbody tr:hover td{background:#fcfaf8}td{overflow-wrap:anywhere}td strong{font-size:14px;font-weight:750}.status{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:3px;background:#f2eee9;font-size:11px;font-weight:750;white-space:nowrap}.status-new{background:#fff4dc;color:#8a5a00}.status-progress{background:#edf3fb;color:#245b9e}.status-replied{background:#e9f5ee;color:#237344}.status-closed{background:#eeeae6;color:#655b54}.status-spam{background:#fcebea;color:#a52a24}.notice{min-height:24px;margin:12px 0;color:var(--red);font-weight:600}.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.quote-message{max-width:300px;white-space:pre-wrap;overflow-wrap:anywhere;color:#554b45;line-height:1.55;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden}.quote-status{min-width:155px;max-width:100%;padding:9px 30px 9px 11px;border:1px solid #ded4cc;border-radius:3px;background:#fff;color:var(--ink);font-weight:600}.quote-status[value="new"]{border-color:#e7c37d;background:#fff4dc}.quote-status[value="in_progress"]{border-color:#b8cbea;background:#edf3fb}.quote-status[value="replied"]{border-color:#a9d5ba;background:#e9f5ee}.detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.detail-item{padding:15px 16px;background:#fcfaf8;border:1px solid var(--line);border-radius:5px;min-width:0}.detail-item strong{display:block;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#82756c;margin-bottom:7px}.detail-item p{margin:0;white-space:pre-wrap;overflow-wrap:anywhere}.detail-wide{grid-column:1/-1}.hint{font-size:12px;color:var(--muted);font-weight:400}.cover-tools{display:grid;grid-template-columns:minmax(0,1fr) 180px;gap:16px;align-items:start}.cover-preview{width:100%;aspect-ratio:4/3;object-fit:cover;background:#f5efe9;border:1px solid var(--line);border-radius:5px;display:block}.cover-preview-wrap{display:grid;gap:6px}.upload-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 15px;border:1px solid var(--red);border-radius:4px;background:var(--red);color:#fff;cursor:pointer;width:max-content;max-width:100%;font-weight:650}.upload-button:hover{background:var(--red-dark)}.cover-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.upload-progress{font-size:12px;color:var(--muted)}#quote-detail-panel{scroll-margin-top:80px}#quote-reply-form{margin-top:24px!important;padding-top:24px!important;border-top:1px solid var(--line)}#quote-send-reply{min-width:175px}#quote-reply-status{margin-top:12px}.top #logout{flex-shrink:0}
/* CRM inbox split view */
.crm-layout{display:grid;grid-template-columns:minmax(300px,.88fr) minmax(360px,1.12fr);gap:16px;align-items:start;margin-top:22px}
.crm-list-panel{min-width:0;background:#fff;border:1px solid var(--line);border-radius:6px;padding:15px}
.crm-list-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}
.crm-list-heading strong{font-size:14px}.crm-table{min-width:0;width:100%;table-layout:fixed}
.crm-table th{position:static;padding:10px 7px;font-size:9px}.crm-table td{padding:11px 7px;font-size:12px}
.crm-table th:nth-child(1){width:56%}.crm-table th:nth-child(2){width:27%}.crm-table th:nth-child(3){width:17%}
.crm-table .quote-message{font-size:11px;-webkit-line-clamp:2}
.crm-table .quote-status{min-width:0;width:100%;font-size:10px;padding:7px 3px}
.crm-table .button{padding:7px 8px;font-size:11px;white-space:normal}
.crm-table tbody tr:hover td{background:#fbf3ef}
#quote-detail-panel{margin-top:0;min-width:0;padding:19px;max-height:calc(100vh - 150px);overflow:auto}
#quote-detail-panel h2{font-size:19px}#quote-detail-panel .detail-grid{grid-template-columns:1fr}
#quote-detail-panel .detail-wide{grid-column:auto}#quote-detail-panel .detail-item{padding:12px}
#quote-detail-panel #quote-reply-form h3{font-size:16px}
.crm-table td:first-child strong{display:block;margin-bottom:3px}
@media(max-width:1000px){.crm-layout{grid-template-columns:1fr}.crm-table{table-layout:auto}.crm-table th:nth-child(1),.crm-table th:nth-child(2),.crm-table th:nth-child(3){width:auto}#quote-detail-panel{max-height:none}}
@media(max-width:1100px){.app-shell{grid-template-columns:190px minmax(0,1fr)}.content{padding:23px 20px}.topbar{padding:0 20px}.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.sidebar{padding-inline:10px}}
@media(max-width:760px){.app-shell{grid-template-columns:1fr}.sidebar{min-height:0;padding:12px;gap:12px}.side-brand{padding:5px 8px 12px}.side-label,.side-bottom{display:none}.side-nav{display:flex;overflow:auto}.side-nav button,.side-nav a{white-space:nowrap;width:auto;padding:10px}.workspace{min-width:0}.topbar{height:54px;padding:0 14px}.content{padding:18px 12px 40px}.page-heading{align-items:flex-start}.page-heading h1{font-size:25px}.page-heading .button{padding:9px 10px}.kpi-grid{gap:9px}.kpi{min-height:105px;padding:13px}.kpi-value{font-size:25px}.panel{padding:17px 13px;margin-top:14px}.row,.detail-grid{grid-template-columns:1fr}.detail-wide{grid-column:auto}.cover-tools{grid-template-columns:1fr}.cover-preview-wrap{max-width:240px}.tabs{overflow:auto}.tabs button{white-space:nowrap;padding:11px 12px}.table-wrap{margin-inline:-2px}#quotes-panel .actions>.button{width:auto}.quote-status{min-width:140px}.topbar-actions{gap:7px}}
@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*:before,*:after{transition:none!important}}

/* MIARADI admin refresh — soft lavender SaaS workspace */
:root{--red:#922D20;--red-dark:#762218;--paper:#f7f6fb;--ink:#252331;--muted:#858292;--line:#eceaf2;--nav:#fff;--nav-muted:#8d8a9c;--white:#fff;--lavender:#eeeaff;--lavender-strong:#7568d8;--sans:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
body{background:var(--paper);color:var(--ink);font-size:13px}
.app-shell{grid-template-columns:250px minmax(0,1fr);background:var(--paper)}
.sidebar{background:#fff;color:var(--ink);padding:24px 16px 18px;gap:30px;border-right:1px solid #eeedf3;box-shadow:5px 0 24px #30205a04}
.side-brand{display:flex;align-items:center;gap:11px;padding:0 9px 23px;border-bottom:1px solid #efedf4;letter-spacing:0;font-size:initial}
.side-logo{width:43px;height:43px;object-fit:contain;border-radius:12px;background:#fbf4f0;padding:4px}
.side-brand-copy{display:grid;gap:2px}
.side-brand-copy strong{font-size:16px;letter-spacing:.07em;font-weight:850;color:#2d2935}
.side-brand-copy small{font-size:9px;font-weight:800;letter-spacing:.17em;color:#9a948f}
.side-label{padding:0 12px;color:#a29eaf;font-size:9px;letter-spacing:.16em;margin-bottom:10px}
.side-nav{gap:7px}
.side-nav button,.side-nav a{gap:12px;padding:12px 13px;border-radius:11px;color:#777487;font-size:12px;font-weight:650;transition:background .18s,color .18s,transform .18s}
.side-nav button:hover,.side-nav a:hover{background:#f7f5fc;color:var(--red);transform:translateX(2px)}
.side-nav button.active{background:#f2edff;color:#6658c8;box-shadow:inset 3px 0 #7667d8}
.nav-icon{width:18px;display:inline-grid;place-items:center;font-size:17px}
.side-bottom{padding:13px 10px;color:#aaa5b4;font-size:10px;line-height:1.8}
.workspace{min-width:0}
.topbar{height:70px;padding:0 34px;background:rgba(255,255,255,.9);border-bottom:1px solid #eeedf3;backdrop-filter:blur(16px)}
.topbar-left{gap:16px}.topbar-menu{width:36px;height:36px;display:grid;place-items:center;border:1px solid #eeebf3;border-radius:11px;background:#fff;color:#777286}
.topbar-menu svg,.topbar-actions svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.topbar-menu:hover{background:#f6f2ff;color:#6e60d2}
.crumb{font-size:11px;color:#9a96a5}.crumb-divider{padding:0 7px;color:#d2cedb}.crumb strong{color:#4c4858;font-weight:750}
.topbar-actions{gap:12px}.topbar-status{display:flex;align-items:center;gap:7px;color:#898596;font-size:10px}.topbar-status span{width:7px;height:7px;border-radius:50%;background:#47b881;box-shadow:0 0 0 3px #47b88120}
.topbar-icon{width:37px;height:37px;border:1px solid #eeebf3;color:#8b8698;background:#fff}
.avatar{width:36px;height:36px;background:#f6e9e4;color:var(--red);font-size:12px}
#logout{display:flex;align-items:center;gap:7px;border-radius:10px;padding:9px 12px;font-size:11px}
.content{max-width:1700px;padding:34px 36px 64px}
.page-heading{align-items:center;margin-bottom:25px}
.eyebrow{font-size:9px;letter-spacing:.18em;color:#8d4c41}
.page-heading h1{font-size:30px;font-weight:800;letter-spacing:-.045em;margin:6px 0 8px;color:#282532}
.page-heading p{font-size:12px;color:#898596}
.button{border-radius:10px;padding:10px 15px;font-size:11px;font-weight:750;box-shadow:0 5px 12px #922d2012}
.button:hover{box-shadow:0 8px 18px #922d201c}
.button.secondary{background:#fff;color:#686476;border-color:#e9e6ef;box-shadow:none}
.button.secondary:hover{background:#f8f6fc;border-color:#d7d1e7;color:var(--red)}
.kpi-grid{gap:16px;margin-bottom:25px}
.kpi{min-height:132px;display:flex;flex-direction:column;justify-content:center;padding:20px 21px 19px 76px;position:relative;background:#fff!important;color:var(--ink)!important;border:1px solid #eeecf3;border-radius:16px;box-shadow:0 5px 22px #2c234009}
.kpi:before{content:"";position:absolute;left:20px;top:23px;width:42px;height:42px;border-radius:13px;background:#f1edff}
.kpi:nth-child(2):before{background:#e8f4ff}.kpi:nth-child(3):before{background:#fff3df}.kpi:nth-child(4):before{background:#e6f7ee}
.kpi-mark{left:31px;right:auto;top:34px;font-size:17px;color:#7567d6;opacity:1;z-index:1}
.kpi:nth-child(2) .kpi-mark{color:#4396d6}.kpi:nth-child(3) .kpi-mark{color:#d89b27}.kpi:nth-child(4) .kpi-mark{color:#35a873}
.kpi-label{font-size:11px;font-weight:650;color:#898596;opacity:1}
.kpi-value{font-size:29px;font-weight:850;letter-spacing:-.05em;color:#2c2938;margin:8px 0 4px}
.kpi-note{font-size:10px;color:#9a96a5;opacity:1}
.panel{margin-top:21px;padding:24px;background:#fff;border:1px solid #eeecf3;border-radius:16px;box-shadow:0 5px 22px #2c234008}
.panel h2{font-size:18px;font-weight:800;color:#302c3a;margin-bottom:8px}
.panel h3{font-size:15px;font-weight:800;color:#302c3a}
.field input,.field select,.field textarea{border-radius:10px;background:#fbfaff;border-color:#e6e2ee;padding:12px 13px}
.field input:focus,.field select:focus,.field textarea:focus{border-color:#a69be9;box-shadow:0 0 0 3px #7568d81a}
.tabs{gap:7px;margin-bottom:19px;border-bottom:1px solid #eeecf3}
.tabs button{border-radius:9px 9px 0 0;padding:12px 16px;font-size:11px}
.tabs button:hover{background:#f7f5fc}.tabs button.active{border-bottom-color:#7568d8;color:#6b5dcd;background:#f5f2ff}
.table-wrap{border:1px solid #eeecf3;border-radius:12px;margin-top:16px}
table{background:#fff;min-width:760px}
th{background:#faf9fd;color:#9691a2;font-size:9px;letter-spacing:.12em;border-bottom-color:#eeecf3}
th,td{padding:14px 13px;border-bottom-color:#f0eef5}
tbody tr:hover td{background:#fbfaff}
td strong{color:#34303e;font-size:12px}
.status{border-radius:7px;padding:5px 9px;font-size:10px}
.status-new{background:#fff4df;color:#a36a09}.status-progress{background:#eeeaff;color:#6858c9}.status-replied{background:#e7f7ee;color:#258458}.status-closed{background:#f0eef4;color:#736e7c}.status-spam{background:#fdeceb;color:#a52a24}
.notice{border-radius:9px}
.quote-status{border-radius:8px}
.detail-item{background:#fbfaff;border-radius:10px;border-color:#eeecf3}
.cover-preview{border-radius:12px;border-color:#eeecf3}
.upload-button{border-radius:9px}
.crm-layout{grid-template-columns:minmax(320px,.92fr) minmax(360px,1.08fr);gap:18px;margin-top:20px}
.crm-list-panel{padding:17px;border:1px solid #eeecf3;border-radius:14px;background:#fff}
.crm-list-heading{margin-bottom:13px}.crm-list-heading strong{font-size:12px;color:#34303e}
.crm-table{border:0;min-width:0}.crm-table th{padding:11px 9px}.crm-table td{padding:13px 9px;font-size:11px}
.crm-table td:first-child strong{font-size:12px}
.crm-table .quote-message{color:#898596;font-size:10px;line-height:1.55}
.crm-table .button{border-radius:8px}
#quote-detail-panel{border-radius:14px;padding:20px;box-shadow:none}
#quote-detail-panel .detail-item{border-radius:10px}
#quote-reply-form{border-color:#eeecf3!important}
#quote-send-reply{min-width:150px}
#notice:empty{display:none}
#circuits-panel>.actions h2{margin:0}
#new-circuit{white-space:nowrap}
@media(max-width:1100px){.app-shell{grid-template-columns:215px minmax(0,1fr)}.content{padding:25px 22px}.topbar{padding:0 22px}.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.kpi{min-height:118px}}
@media(max-width:760px){.app-shell{grid-template-columns:1fr}.sidebar{position:fixed;z-index:20;inset:0 auto 0 0;width:min(285px,85vw);min-height:100dvh;transform:translateX(-105%);transition:transform .22s ease;box-shadow:20px 0 60px #211b3820;padding:20px 15px}.sidebar.is-open{transform:translateX(0)}.topbar{height:60px;padding:0 13px}.topbar-status{display:none}.content{padding:22px 13px 40px}.page-heading h1{font-size:26px}.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.kpi{min-height:112px;padding:16px 12px 15px 58px;border-radius:13px}.kpi:before{left:12px;top:19px;width:34px;height:34px;border-radius:10px}.kpi-mark{left:22px;top:27px;font-size:14px}.kpi-value{font-size:24px}.kpi-label{font-size:10px}.kpi-note{font-size:9px}.panel{padding:18px 13px;margin-top:15px;border-radius:13px}.crm-layout{grid-template-columns:1fr}.crm-list-panel{padding:12px}#quote-detail-panel{max-height:none}.topbar-actions{gap:8px}.avatar{width:32px;height:32px}#logout{padding:8px}.side-nav{flex-direction:column;overflow:visible}.side-nav button,.side-nav a{width:100%}}
@media(max-width:420px){.kpi-grid{grid-template-columns:1fr}.kpi{min-height:100px}.page-heading{gap:10px}.topbar-actions{gap:6px}.crumb{font-size:10px}.button{padding:9px 11px}}
@media(prefers-reduced-motion:reduce){.sidebar{transition:none}}


/* Travel package catalogue inspired by the supplied dashboard reference */
.circuits-workspace{padding:25px;margin-top:0}
.catalogue-heading{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:22px}
.catalogue-heading h2{font-size:23px;margin:5px 0 7px;letter-spacing:-.035em}
.catalogue-heading p{margin:0;max-width:560px;color:var(--muted);font-size:11px;line-height:1.65}
.catalogue-heading .button{flex-shrink:0;padding:12px 16px}
.catalogue-toolbar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:21px}
.catalogue-search{display:flex;align-items:center;gap:10px;flex:1;min-width:220px;height:42px;padding:0 13px;border:1px solid #eceaf2;border-radius:10px;background:#fbfaff;color:#9994a6}
.catalogue-search svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round}
.catalogue-search input{width:100%;min-width:0;border:0;outline:0;background:transparent;color:var(--ink);font:inherit;font-size:11px}
.catalogue-search input:focus{box-shadow:none}
.catalogue-search:focus-within{border-color:#a69be9;box-shadow:0 0 0 3px #7568d81a}
.catalogue-filter{height:42px;display:flex;align-items:center;gap:9px;padding:0 11px;border:1px solid #eceaf2;border-radius:10px;background:#fff;color:#9994a6;font-size:10px}
.catalogue-filter select{max-width:140px;border:0;outline:0;background:transparent;color:#4c4858;font:inherit;font-weight:700;cursor:pointer}
.catalogue-count{font-size:10px;color:#9691a2;white-space:nowrap}
.circuit-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px}
.circuit-card{min-width:0;overflow:hidden;border:1px solid #eeecf3;border-radius:15px;background:#fff;box-shadow:0 4px 18px #2c234008;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
.circuit-card:hover{transform:translateY(-3px);box-shadow:0 12px 27px #2c234011;border-color:#e0dbea}
.circuit-cover{position:relative;aspect-ratio:1.55/1;overflow:hidden;background:linear-gradient(135deg,#eeeaff,#f8e7dc)}
.circuit-cover img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
.circuit-card:hover .circuit-cover img{transform:scale(1.035)}
.circuit-cover-placeholder{display:grid;place-items:center;width:100%;height:100%;color:#a49cb9;font-size:36px}
.circuit-cover-placeholder svg{width:42px;height:42px;fill:none;stroke:currentColor;stroke-width:1.2}
.circuit-status{position:absolute;top:12px;left:12px;display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border:1px solid #ffffffa6;border-radius:7px;background:#ffffffed;color:#26865c;font-size:9px;font-weight:800;box-shadow:0 3px 9px #24152a0a}
.circuit-status:before{content:"";width:6px;height:6px;border-radius:50%;background:#35b47a}
.circuit-status.draft{color:#a36a09}
.circuit-status.draft:before{background:#e5a52e}
.circuit-card-body{padding:15px 15px 14px}
.circuit-meta{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:9px}
.circuit-chip{display:inline-flex;align-items:center;padding:5px 8px;border-radius:6px;background:#f1edff;color:#6b5dcd;font-size:9px;font-weight:750}
.circuit-chip.region{background:#eef6fc;color:#4386b5}
.circuit-card h3{margin:0 0 7px;color:#302c3a;font-size:15px;line-height:1.35;font-weight:800;letter-spacing:-.02em}
.circuit-summary{display:-webkit-box;min-height:36px;margin:0;color:#898596;font-size:10px;line-height:1.65;overflow:hidden;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.circuit-card-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:15px;padding-top:12px;border-top:1px solid #f0eef5}
.circuit-duration{display:flex;align-items:center;gap:7px;color:#777286;font-size:10px;font-weight:700;white-space:nowrap}
.circuit-duration svg{width:15px;height:15px;fill:none;stroke:#9993a6;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.circuit-actions{display:flex;align-items:center;gap:6px}
.circuit-actions .button{padding:7px 9px;border-radius:7px;font-size:10px;box-shadow:none}
.circuit-actions .circuit-delete{color:#a34b45;background:#fff;border-color:#f0e5e4}
.circuit-actions .circuit-delete:hover{background:#fff3f2;border-color:#e8c9c6}
.circuit-empty{grid-column:1/-1;display:grid;justify-items:center;gap:9px;padding:48px 20px;border:1px dashed #ded9e9;border-radius:14px;text-align:center;color:#8f899f}
.circuit-empty strong{color:#454052;font-size:13px}
.circuit-empty span{font-size:11px}
@media(max-width:1250px){.circuit-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.circuits-workspace{padding:16px 12px}.catalogue-heading{align-items:flex-start;flex-direction:column}.catalogue-heading .button{width:100%;justify-content:center}.catalogue-heading h2{font-size:21px}.catalogue-toolbar{gap:9px}.catalogue-search{flex-basis:100%;min-width:0}.catalogue-filter{flex:1;justify-content:space-between}.catalogue-count{width:100%}.circuit-grid{grid-template-columns:1fr;gap:13px}.circuit-cover{aspect-ratio:1.7/1}.circuit-card-body{padding:14px}.circuit-card-footer{flex-wrap:wrap}}
@media(prefers-reduced-motion:reduce){.circuit-card,.circuit-cover img{transition:none}}

</style>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="admin-sidebar"><div class="side-brand"><img src="../../../assets/miaradi-logo.png" alt="" class="side-logo"><span class="side-brand-copy"><strong>MIARADI</strong><small>ESPACE ADMIN</small></span></div><div><div class="side-label">Espace de gestion</div><nav class="side-nav"><button class="active" data-tab="circuits"><span class="nav-icon">▦</span> Catalogue des circuits</button><button data-tab="quotes"><span class="nav-icon">✉</span> Demandes de devis</button><a href="gallery.php"><span class="nav-icon">▧</span> Galerie médias ↗</a></nav></div><div class="side-bottom">MIARADI-TRAVEL<br>Administration sécurisée</div></aside>
  <div class="workspace"><header class="topbar"><div class="topbar-left"><button class="topbar-menu" id="sidebar-toggle" type="button" aria-label="Afficher ou masquer le menu" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button><span class="crumb">Espace de travail <span class="crumb-divider">/</span> <strong id="breadcrumb-current">Tableau de bord</strong></span></div><div class="topbar-actions"><span class="topbar-status"><span></span> Administration sécurisée</span><span class="avatar" title="Administrateur">M</span><button class="button secondary hidden" id="logout"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg> Déconnexion</button></div></header><div class="content"><div class="page-heading"><div><div class="eyebrow">Vue d’ensemble</div><h1>Tableau de bord</h1><p class="muted">Suivez le catalogue et les demandes de voyage MIARADI.</p></div></div>
  <main id="dashboard" class="hidden">
    <section class="kpi-grid" aria-label="Indicateurs du site"><article class="kpi"><span class="kpi-mark">▦</span><div class="kpi-label">Circuits au catalogue</div><div class="kpi-value" id="kpi-circuits">—</div><div class="kpi-note" id="kpi-circuits-note">Chargement des données</div></article><article class="kpi"><span class="kpi-mark">✉</span><div class="kpi-label">Demandes de devis</div><div class="kpi-value" id="kpi-quotes">—</div><div class="kpi-note">Demandes enregistrées en base</div></article><article class="kpi"><span class="kpi-mark">◷</span><div class="kpi-label">À traiter</div><div class="kpi-value" id="kpi-pending">—</div><div class="kpi-note">Nouvelles demandes et en cours</div></article><article class="kpi"><span class="kpi-mark">✓</span><div class="kpi-label">Clients contactés</div><div class="kpi-value" id="kpi-replied">—</div><div class="kpi-note">Réponses enregistrées</div></article></section>
    <nav class="tabs"><button class="active" data-tab="circuits">Catalogue des circuits</button><button data-tab="quotes">Demandes de devis</button></nav>
    <p class="notice" id="notice" role="status"></p>
    <section class="panel circuits-workspace" id="circuits-panel">
      <div class="catalogue-heading">
        <div><div class="eyebrow">Vos destinations</div><h2>Catalogue des circuits</h2><p class="muted">Organisez vos voyages, leurs visuels et leur visibilité sur le site MIARADI.</p></div>
        <button class="button" id="new-circuit" type="button"><span aria-hidden="true">＋</span> Ajouter un circuit</button>
      </div>
      <div class="catalogue-toolbar">
        <label class="catalogue-search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg><input id="circuit-search" type="search" placeholder="Rechercher un circuit, une région…" aria-label="Rechercher un circuit"></label>
        <label class="catalogue-filter"><span>Visibilité</span><select id="circuit-filter"><option value="all">Tous les circuits</option><option value="published">Publiés</option><option value="draft">Brouillons</option></select></label>
        <span class="catalogue-count" id="catalogue-count">Chargement des circuits…</span>
      </div>
      <div class="circuit-grid" id="circuits-list" aria-live="polite"></div>
    </section>
        <section class="panel hidden" id="quotes-panel">
      <div class="actions" style="justify-content:space-between;align-items:center">
        <div><div class="eyebrow">Service clientèle</div><h2>Boîte de réception</h2><p class="muted">Sélectionnez une demande pour consulter le voyage, répondre au client et suivre son statut.</p></div>
        <button class="button secondary" id="refresh-quotes" type="button">↻ Actualiser</button>
      </div>
      <div class="crm-layout">
        <div class="crm-list-panel">
          <div class="crm-list-heading"><strong>Demandes reçues</strong><span id="crm-quote-count" class="hint">Chargement…</span></div>
          <div class="table-wrap"><table class="crm-table"><thead><tr><th>Client / demande</th><th>Statut</th><th>Ouvrir</th></tr></thead><tbody id="quotes-list"></tbody></table></div>
        </div>
        <section class="panel hidden" id="quote-detail-panel" aria-labelledby="quote-detail-title"><div class="actions" style="justify-content:space-between;align-items:center"><h2 id="quote-detail-title">Détail de la demande</h2><button class="button secondary" id="close-quote-detail" type="button">Fermer</button></div><div id="quote-detail-content"></div><form id="quote-reply-form" style="margin-top:20px;border-top:1px solid var(--line);padding-top:18px"><h3>Répondre au client</h3><label class="field">Objet de l’e-mail<input id="quote-reply-subject" name="subject" required maxlength="200" value="Votre demande de voyage — MIARADI-TRAVEL"></label><label class="field">Message<textarea id="quote-reply-message" name="message" required maxlength="10000" rows="8" placeholder="Rédigez votre réponse au client…"></textarea></label><div class="actions"><button class="button" id="quote-send-reply" type="submit">Envoyer la réponse</button><span class="hint">Le statut et la date ne changent que si le serveur de messagerie accepte l’envoi.</span></div><p class="notice" id="quote-reply-status" role="status"></p></form><div class="actions" style="margin-top:18px"><button class="button secondary" id="quote-detail-close-status" type="button">Clôturer la demande</button></div></section>
      </div>
    </section>
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
  </div></div>
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
    $('#dashboard').classList.remove('hidden'); $('#logout').classList.remove('hidden');
    loadCircuits(); loadQuotes();
  }
  async function checkSession() {
    try { const result = await request('admin/session.php'); if (result.data.authenticated) showDashboard(result.data); else location.replace('login.php'); }
    catch (_) { location.replace('login.php'); }
  }
  $('#logout').addEventListener('click', async () => {
    try { await request('admin/session.php', {method:'POST', mutate:true, body:JSON.stringify({action:'logout'})}); } catch (_) {}
    location.replace('login.php');
  });
  function circuitImagePath(path) {
    const value = String(path || '').trim();
    if (!value) return '';
    if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('//') || value.startsWith('data:') || value.startsWith('/')) return value;
    const relative = value.startsWith('./') ? value.slice(2) : value;
    return siteRoot() + '/' + relative.replace(/^\\/+/, '');
  }
  function renderCircuits() {
    const search = ($('#circuit-search')?.value || '').trim().toLocaleLowerCase('fr');
    const filter = $('#circuit-filter')?.value || 'all';
    const filtered = circuits.filter(c => {
      const searchable = [c.title_fr,c.title_en,c.region,c.theme,c.slug,c.summary_fr].filter(Boolean).join(' ').toLocaleLowerCase('fr');
      return (!search || searchable.includes(search)) && (filter === 'all' || (filter === 'published' ? Boolean(c.is_published) : !Boolean(c.is_published)));
    });
    $('#catalogue-count').textContent = filtered.length + ' circuit' + (filtered.length > 1 ? 's' : '') + (filtered.length !== circuits.length ? ' affiché(s) sur ' + circuits.length : ' au catalogue');
    $('#circuits-list').innerHTML = filtered.map(c => {
      const image = circuitImagePath(c.image_path);
      const duration = c.duration_min && c.duration_max ? c.duration_min + '–' + c.duration_max + ' jours' : (c.duration_min ? 'Dès ' + c.duration_min + ' jours' : (c.duration_max ? 'Jusqu’à ' + c.duration_max + ' jours' : 'Durée sur devis'));
      const region = c.region ? '<span class="circuit-chip region">' + esc(c.region) + '</span>' : '';
      const theme = c.theme ? '<span class="circuit-chip">' + esc(c.theme) + '</span>' : '';
      const imageMarkup = image ? '<img src="' + esc(image) + '" alt="Photo du circuit ' + esc(c.title_fr) + '" loading="lazy" onerror="this.style.display=\'none\';this.nextElementSibling.hidden=false"><div class="circuit-cover-placeholder" hidden aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m21 15-5-5L5 20"/></svg></div>' : '<div class="circuit-cover-placeholder" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m21 15-5-5L5 20"/></svg></div>';
      return '<article class="circuit-card"><div class="circuit-cover">' + imageMarkup + '<span class="circuit-status ' + (c.is_published ? '' : 'draft') + '">' + (c.is_published ? 'Publié' : 'Brouillon') + '</span></div><div class="circuit-card-body"><div class="circuit-meta">' + region + theme + '</div><h3>' + esc(c.title_fr || 'Circuit sans titre') + '</h3><p class="circuit-summary">' + esc(c.summary_fr || 'Ajoutez une description pour présenter ce circuit aux voyageurs.') + '</p><div class="circuit-card-footer"><span class="circuit-duration"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>' + esc(duration) + '</span><div class="circuit-actions"><button class="button secondary" type="button" data-edit="' + Number(c.id) + '">Modifier</button><button class="button secondary circuit-delete" type="button" data-delete="' + Number(c.id) + '">Supprimer</button></div></div></div></article>';
    }).join('') || '<div class="circuit-empty"><strong>' + (circuits.length ? 'Aucun circuit trouvé' : 'Votre catalogue est prêt à commencer') + '</strong><span>' + (circuits.length ? 'Essayez un autre mot-clé ou modifiez le filtre de visibilité.' : 'Ajoutez votre premier circuit pour le présenter dans cet espace.') + '</span></div>';
    $('#circuits-list').querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => editCircuit(Number(b.dataset.edit))));
    $('#circuits-list').querySelectorAll('[data-delete]').forEach(b => b.addEventListener('click', () => deleteCircuit(Number(b.dataset.delete))));
  }
  async function loadCircuits() {
    try {
      const result = await request('admin/circuits.php'); circuits = result.data;
      $('#kpi-circuits').textContent = circuits.length;
      $('#kpi-circuits-note').textContent = circuits.filter(c => Boolean(c.is_published)).length + ' publié(s) · ' + circuits.filter(c => !Boolean(c.is_published)).length + ' brouillon(s)';
      renderCircuits();
    } catch (error) { notice(error.message); }
  }
  $('#circuit-search').addEventListener('input', renderCircuits);
  $('#circuit-filter').addEventListener('change', renderCircuits);
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
  function quoteStatusBadge(status) {
    const label = quoteStatusLabels[status] || quoteStatusLabels.new;
    const tone = ({new:'status-new',in_progress:'status-progress',replied:'status-replied',closed:'status-closed',spam:'status-spam'})[status] || 'status-new';
    return '<span class="status '+tone+'">'+esc(label)+'</span>';
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
      '<div class="detail-item"><strong>Statut actuel</strong><p>'+quoteStatusBadge(q.status)+'</p><p class="hint">Le statut évolue automatiquement selon le traitement de la demande.</p></div></div>';
    const subject = q.reply_subject || 'Votre demande de voyage — MIARADI-TRAVEL';
    const firstName = String(q.full_name || '').trim().split(/\s+/)[0] || 'Bonjour';
    const defaultBody = q.preferred_language === 'en'
      ? 'Thank you for contacting MIARADI-TRAVEL. We are reviewing your travel project and will get back to you with the next steps.'
      : 'Nous vous remercions d’avoir contacté MIARADI-TRAVEL. Nous étudions votre projet de voyage à Madagascar et reviendrons vers vous avec les prochaines étapes.';
    $('#quote-reply-subject').value = subject;
    $('#quote-reply-message').value = q.reply_message || defaultBody;
    $('#quote-reply-status').textContent = '';
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
      $('#kpi-quotes').textContent = quoteRequests.length;
      $('#kpi-pending').textContent = quoteRequests.filter(q => ['new','in_progress'].includes(q.status)).length;
      $('#kpi-replied').textContent = quoteRequests.filter(q => q.status === 'replied').length;
      $('#crm-quote-count').textContent = quoteRequests.length + (quoteRequests.length === 1 ? ' demande' : ' demandes');
      $('#quotes-list').innerHTML = quoteRequests.map(q => '<tr><td><strong>'+esc(q.full_name)+'</strong><br><span class="muted">'+esc(q.email)+'</span><br><strong>'+esc(q.circuit_title_fr || q.circuit_title_en || 'Voyage sur mesure')+'</strong><br><span class="muted">'+esc(formatQuoteDate(q.created_at))+'</span><div class="quote-message">'+esc(q.message)+'</div></td><td>'+quoteStatusBadge(q.status)+'</td><td><button class="button secondary" type="button" data-detail="'+q.id+'">Ouvrir</button></td></tr>').join('') || '<tr><td colspan="3">Aucune demande reçue.</td></tr>';
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
    document.querySelectorAll('[data-tab]').forEach(b=>b.classList.toggle('active',b.dataset.tab===button.dataset.tab));
    $('#breadcrumb-current').textContent = button.dataset.tab === 'quotes' ? 'Demandes de devis' : 'Catalogue des circuits';
    document.querySelector('.page-heading h1').textContent = button.dataset.tab === 'quotes' ? 'Demandes de devis' : 'Catalogue des circuits';
    document.querySelector('.page-heading .eyebrow').textContent = button.dataset.tab === 'quotes' ? 'Service clientèle' : 'Vue d’ensemble';
    const quotes = button.dataset.tab === 'quotes';
    $('#circuits-panel').classList.toggle('hidden',quotes); $('#quotes-panel').classList.toggle('hidden',!quotes); $('#editor-panel').classList.add('hidden'); if (!quotes) $('#quote-detail-panel').classList.add('hidden');
    if (quotes) await loadQuotes(); else await loadCircuits();
  }));
  const sidebarToggle = $('#sidebar-toggle');
  sidebarToggle.addEventListener('click', () => {
    const opened = $('#admin-sidebar').classList.toggle('is-open');
    sidebarToggle.setAttribute('aria-expanded', String(opened));
  });
  document.querySelectorAll('.side-nav [data-tab]').forEach(button => button.addEventListener('click', () => {
    $('#admin-sidebar').classList.remove('is-open');
    sidebarToggle.setAttribute('aria-expanded', 'false');
  }));
  checkSession();
})();
</script>
</body>
</html>

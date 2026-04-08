<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="dummy-csrf-token">
<title>ระบบบริหารการจัดส่ง</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --ink:#080f1e;
  --navy:#111d35;
  --navy-2:#162240;
  --navy-3:#1c2d52;
  --gold:#c9963a;
  --gold-2:#e8b455;
  --gold-3:#f5d080;
  --crimson:#b82020;
  --crimson-2:#961818;
  --crimson-bg:rgba(20, 4, 241, 0.07);
  --crimson-bd:rgba(255, 12, 12, 0.24);
  --surface:#ffffff;
  --surface-2:#f8f9fc;
  --surface-3:#f0f2f8;
  --border:#dde1ef;
  --border-2:#c8cde2;
  --text:#0a1628;
  --text-2:#2e3d5c;
  --text-3:#6b7a99;
  --text-4:#9daabe;
  --success:#076044;
  --success-bg:#eaf7f2;
  --success-bd:#6dd4b3;
  --warn:#7a4000;
  --warn-bg:#fff8ed;
  --warn-bd:#f0c070;
  --danger:#8b1a1a;
  --danger-bg:#fdf0f0;
  --danger-bd:#f5a0a0;
  --blue:#1a4fa0;
  --blue-bg:#eaf0fc;
  --blue-bd:#a8c0f0;
  --sh-sm:0 1px 3px rgba(8,15,30,.06),0 1px 8px rgba(8,15,30,.04);
  --sh-md:0 4px 16px rgba(8,15,30,.10),0 1px 4px rgba(8,15,30,.06);
  --sh-lg:0 8px 32px rgba(8,15,30,.14),0 2px 8px rgba(8,15,30,.08);
  --r:8px;--rl:14px;--rxl:18px;
}
html,body{height:100%}
body{font-family:'Kanit',sans-serif;background:var(--surface-2);color:var(--text);font-size:14px;line-height:1.55;display:flex;flex-direction:column;min-height:100vh}

.topbar{background:var(--ink);height:62px;flex-shrink:0;display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:100;box-shadow:0 1px 0 rgba(255,255,255,.05),0 4px 24px rgba(0,0,0,.32)}
.brand{display:flex;align-items:center;gap:12px}
.brand-logo{width:36px;height:36px;background:linear-gradient(135deg,var(--gold) 0%,var(--gold-2) 100%);border-radius:9px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(201,150,58,.35);flex-shrink:0}
.brand-logo svg{color:var(--ink);width:17px;height:17px}
.bdiv{width:1px;height:28px;background:rgba(255,255,255,.08);margin:0 2px}
.brand-text .name{font-size:14px;font-weight:700;color:#fff;letter-spacing:.04em}
.brand-text .sub{font-size:9px;color:rgba(255,255,255,.3);letter-spacing:.15em;text-transform:uppercase;margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:8px}
.tsep{width:1px;height:22px;background:rgba(255,255,255,.1);margin:0 2px}
.nav-btn{height:31px;padding:0 12px;border-radius:7px;font-family:'Kanit',sans-serif;font-size:11.5px;font-weight:500;cursor:pointer;text-decoration:none;border:1px solid;display:flex;align-items:center;gap:5px;transition:all .18s;letter-spacing:.02em}
.nav-btn svg{width:11px;height:11px;flex-shrink:0}
.nb-oil{background:rgba(201,150,58,.12);color:var(--gold-2);border-color:rgba(201,150,58,.25)}
.nb-oil:hover{background:rgba(201,150,58,.22);border-color:rgba(201,150,58,.45)}
.nb-insp{background:rgba(100,210,160,.1);color:#72ddb0;border-color:rgba(100,210,160,.22)}
.nb-insp:hover{background:rgba(100,210,160,.2);border-color:rgba(100,210,160,.4)}
.hdr-date{font-family:'IBM Plex Mono',monospace;font-size:10px;color:rgba(255,255,255,.32);letter-spacing:.04em}
.hdr-user{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:4px 12px 4px 5px}
.hdr-av{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-2));display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--ink);box-shadow:0 1px 4px rgba(201,150,58,.4)}
.hdr-name{font-size:11.5px;color:rgb(38, 199, 240);font-weight:500}

main{flex:1;display:flex;flex-direction:column;padding:20px 28px;gap:14px}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;flex-shrink:0}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:14px 18px;box-shadow:var(--sh-sm);display:flex;align-items:center;gap:12px;position:relative;overflow:hidden;transition:transform .18s,box-shadow .18s;cursor:default}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--sh-md)}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;border-radius:0 0 var(--rl) var(--rl)}
.sc-blue::after{background:linear-gradient(90deg,#4f8ef7,#3b74e8)}
.sc-amber::after{background:linear-gradient(90deg,#f5a623,#e8920a)}
.sc-green::after{background:linear-gradient(90deg,#27c98a,#18b07a)}
.sc-red::after{background:linear-gradient(90deg,var(--crimson),#d44040)}
.stat-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sc-blue .stat-ico{background:rgba(79,142,247,.1)} .sc-amber .stat-ico{background:rgba(245,166,35,.1)}
.sc-green .stat-ico{background:rgba(39,201,138,.1)} .sc-red .stat-ico{background:rgba(184,32,32,.1)}
.stat-ico svg{width:17px;height:17px}
.sc-blue .stat-ico svg{color:#4f8ef7} .sc-amber .stat-ico svg{color:#f5a623}
.sc-green .stat-ico svg{color:#27c98a} .sc-red .stat-ico svg{color:var(--crimson)}
.stat-lbl{font-size:10px;font-weight:500;color:var(--text-3);letter-spacing:.07em;text-transform:uppercase;margin-bottom:3px}
.stat-val{font-size:26px;font-weight:700;color:var(--text);font-family:'IBM Plex Mono',monospace;line-height:1}

/* FILTER */
.filter-bar{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:12px 18px;box-shadow:var(--sh-sm);display:flex;align-items:center;gap:11px;flex-wrap:wrap;flex-shrink:0}
.filter-tag{font-size:10px;font-weight:700;color:var(--text-4);text-transform:uppercase;letter-spacing:.12em;white-space:nowrap;display:flex;align-items:center;gap:5px}
.filter-tag svg{width:10px;height:10px}
.fsep{width:1px;height:24px;background:var(--border)}
.fi{height:34px;border:1px solid var(--border-2);border-radius:var(--r);padding:0 12px;font-family:'Kanit',sans-serif;font-size:13px;color:var(--text);background:var(--surface-2);outline:none;transition:all .15s}
.fi:focus{border-color:var(--navy-3);box-shadow:0 0 0 3px rgba(28,45,82,.1);background:var(--surface)}
select.fi{appearance:none;cursor:pointer;padding-right:28px;min-width:155px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7a99'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 9px center}
.btn-go{height:34px;background:var(--navy-3);color:#fff;border:none;border-radius:var(--r);padding:0 16px;font-family:'Kanit',sans-serif;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;box-shadow:0 2px 8px rgba(28,45,82,.22);white-space:nowrap}
.btn-go:hover{background:var(--navy-2);box-shadow:0 3px 12px rgba(28,45,82,.32)}
.btn-go:active{transform:translateY(1px)}
.btn-go svg{width:11px;height:11px}
.btn-rst{height:34px;background:transparent;color:var(--text-3);border:1px solid var(--border);border-radius:var(--r);padding:0 13px;font-family:'Kanit',sans-serif;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .15s}
.btn-rst:hover{background:var(--surface-3);color:var(--text-2)}
.btn-rst svg{width:11px;height:11px}

/* TABLE CARD */
.tbl-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);box-shadow:var(--sh-sm);flex:1;display:flex;flex-direction:column;min-height:0;overflow:hidden}
.tbl-head{padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,#fafbff,#f5f7fd);flex-shrink:0;flex-wrap:wrap;gap:10px}
.tbl-head-l{display:flex;align-items:center;gap:9px}
.tbl-title{font-size:13px;font-weight:600;color:var(--text)}
.rec-badge{background:var(--navy-3);color:#fff;border-radius:20px;padding:2px 10px;font-size:10px;font-weight:600;font-family:'IBM Plex Mono',monospace;letter-spacing:.04em}
.srch-wrap{position:relative}
.srch-wrap svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-4);pointer-events:none;width:12px;height:12px}
.srch-inp{height:32px;border:1px solid var(--border-2);border-radius:var(--r);padding:0 12px 0 30px;font-family:'Kanit',sans-serif;font-size:12px;color:var(--text);background:var(--surface-2);outline:none;width:210px;transition:all .2s}
.srch-inp:focus{border-color:var(--navy-3);box-shadow:0 0 0 3px rgba(28,45,82,.1);width:248px;background:var(--surface)}

/* TABLE */
.tbl-scroll{flex:1;overflow:auto}
table{width:100%;border-collapse:collapse;table-layout:fixed}
col.c-n{width:46px} col.c-d{width:106px} col.c-nm{width:145px}
col.c-bill{width:152px} col.c-job{width:210px} col.c-note{width:192px} col.c-st{width:188px}
thead th{padding:9px 14px;font-size:10px;font-weight:700;color:var(--text-4);text-transform:uppercase;letter-spacing:.1em;background:linear-gradient(180deg,#f5f6fb,#f0f2f9);border-bottom:2px solid var(--border);white-space:nowrap;text-align:center;position:sticky;top:0;z-index:2}
thead th.th-hl{color:var(--navy-3);background:linear-gradient(180deg,#eef0fb,#e8eaf8)}
tbody tr{border-bottom:1px solid rgb(3, 3, 3);transition:background .1s}
tbody tr:last-child{border-bottom:none}
tbody tr:nth-child(even){background:rgba(248,249,252,.8)}
tbody tr:hover{background:linear-gradient(90deg,rgba(236,241,253,.55),rgba(240,244,255,.35)) !important}
tbody td{padding:10px 14px;font-size:13px;vertical-align:middle;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:center}
tbody td.td-job,tbody td.td-note{text-align:left}
.row-num{font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--text-4)}
.cell-date{font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--text-3);letter-spacing:.03em}
.cell-name{font-weight:600;color:var(--text);font-size:13px}

/* ── CRIMSON BILL ── */
.cell-bill{
  display:inline-flex;align-items:center;
  font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:600;
  color:var(--crimson);
  background:var(--crimson-bg);
  border:1px solid var(--crimson-bd);
  border-radius:5px;
  padding:3px 9px 3px 12px;
  letter-spacing:.04em;
  position:relative;overflow:hidden;
  transition:all .15s;
}
.cell-bill::before{
  content:'';position:absolute;left:0;top:0;bottom:0;
  width:3px;background:var(--crimson);border-radius:0;
}
.cell-bill:hover{background:rgba(184,32,32,.12);border-color:rgba(184,32,32,.38);box-shadow:0 1px 6px rgba(184,32,32,.14)}

.cell-job{font-size:13px;font-weight:500;color:var(--text);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cell-note{font-size:12px;color:var(--text-3);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cell-note.empty{color:var(--text-4);font-style:italic}

/* STATUS */
.st-cell{display:flex;flex-direction:column;align-items:center;gap:5px}
.st-sel{border:1.5px solid;border-radius:6px;padding:5px 27px 5px 10px;font-family:'Kanit',sans-serif;font-size:11.5px;font-weight:500;cursor:pointer;outline:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7a99'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;transition:all .15s;max-width:162px}
.st-sel:hover{filter:brightness(.96);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.st-empty{background:#f4f5fa;color:#4b5570;border-color:#cdd1e4}
.st-newbill{background:var(--blue-bg);color:var(--blue);border-color:var(--blue-bd)}
.st-return{background:var(--warn-bg);color:var(--warn);border-color:var(--warn-bd)}
.st-stock{background:var(--success-bg);color:var(--success);border-color:var(--success-bd)}
.st-wrong{background:var(--danger-bg);color:var(--danger);border-color:var(--danger-bd)}

/* NEW BILL */
.nb-wrap{display:none;align-items:center;gap:5px;width:100%;animation:fd .15s ease}
.nb-wrap.show{display:flex}
@keyframes fd{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}
.nb-inp{height:27px;border:1.5px solid var(--crimson-bd);border-radius:5px;padding:0 8px;font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:600;color:var(--crimson);background:#fff;outline:none;flex:1;min-width:0;transition:all .15s}
.nb-inp::placeholder{color:rgba(184,32,32,.3);font-weight:400;font-family:'Kanit',sans-serif;font-size:11px}
.nb-inp:focus{border-color:var(--crimson);box-shadow:0 0 0 3px rgba(184,32,32,.1)}
.nb-save{height:27px;background:var(--crimson);color:#fff;border:none;border-radius:5px;padding:0 10px;font-family:'Kanit',sans-serif;font-size:11px;font-weight:500;cursor:pointer;white-space:nowrap;transition:all .15s;flex-shrink:0}
.nb-save:hover{background:var(--crimson-2)} .nb-save:active{transform:translateY(1px)}

.tbl-foot{padding:9px 20px;border-top:1px solid var(--border);background:linear-gradient(180deg,#f5f7fd,#f0f2fa);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--text-4);font-family:'IBM Plex Mono',monospace;letter-spacing:.03em;flex-shrink:0}

.empty-cell{padding:72px 24px !important;text-align:center !important;white-space:normal !important}
.empty-ico{width:50px;height:50px;background:var(--surface-3);border:1px solid var(--border);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--text-4)}
.empty-t{font-size:13.5px;font-weight:600;color:var(--text-2);margin-bottom:5px} .empty-s{font-size:12px;color:var(--text-4)}

/* MODAL */
.m-ov{position:fixed;inset:0;background:rgba(4,10,22,.6);backdrop-filter:blur(4px);z-index:200;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;padding:16px}
.m-ov.show{opacity:1;pointer-events:all}
.modal{background:var(--surface);border-radius:var(--rxl);width:500px;max-width:calc(100vw - 32px);box-shadow:var(--sh-lg);border:1px solid var(--border);transform:translateY(14px) scale(.97);transition:transform .22s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
.m-ov.show .modal{transform:translateY(0) scale(1)}
.modal-head{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;background:linear-gradient(135deg,#f8f9fe,#f1f4fc)}
.m-ico{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid}
.m-ico svg{width:18px;height:18px}
.m-ico.oil{background:linear-gradient(135deg,#fff8e8,#fdeec0);color:var(--warn);border-color:var(--warn-bd)}
.m-ico.insp{background:linear-gradient(135deg,#e8f8f2,#c2f0dc);color:var(--success);border-color:var(--success-bd)}
.m-title{font-size:14.5px;font-weight:700;color:var(--text);letter-spacing:.02em} .m-sub{font-size:11.5px;color:var(--text-4);margin-top:2px}
.m-close{margin-left:auto;background:none;border:1px solid var(--border);border-radius:7px;cursor:pointer;color:var(--text-4);width:28px;height:28px;display:flex;align-items:center;justify-content:center;transition:all .15s}
.m-close:hover{background:var(--surface-3);color:var(--text);border-color:var(--border-2)}
.m-close svg{width:13px;height:13px}
.modal-body{padding:20px 22px;display:flex;flex-direction:column;gap:14px}
.m-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.m-field{display:flex;flex-direction:column;gap:5px}
.m-field label{font-size:10.5px;font-weight:600;color:var(--text-3);letter-spacing:.06em;text-transform:uppercase}
.m-field input,.m-field select,.m-field textarea{height:36px;border:1px solid var(--border-2);border-radius:var(--r);padding:0 12px;font-family:'Kanit',sans-serif;font-size:13px;color:var(--text);background:var(--surface-2);outline:none;width:100%;transition:all .15s}
.m-field textarea{height:62px;padding:8px 12px;resize:none}
.m-field select{appearance:none;cursor:pointer;padding-right:28px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7a99'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center}
.m-field input:focus,.m-field select:focus,.m-field textarea:focus{border-color:var(--navy-3);box-shadow:0 0 0 3px rgba(28,45,82,.1);background:var(--surface)}
.m-sect{font-size:10px;font-weight:700;color:var(--text-4);text-transform:uppercase;letter-spacing:.12em;display:flex;align-items:center;gap:6px;padding-bottom:7px;border-bottom:1px solid var(--border)}
.m-sect svg{width:11px;height:11px;color:var(--text-3)}
.modal-foot{padding:13px 22px;border-top:1px solid var(--border);background:linear-gradient(180deg,#f8f9fe,#f2f4fb);display:flex;justify-content:flex-end;gap:9px}
.btn-save{height:36px;background:var(--navy-3);color:#fff;border:none;border-radius:var(--r);padding:0 18px;font-family:'Kanit',sans-serif;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(28,45,82,.22);transition:all .15s}
.btn-save:hover{background:var(--navy-2);box-shadow:0 3px 12px rgba(28,45,82,.3)}
.btn-save:active{transform:translateY(1px)}
.btn-save svg{width:12px;height:12px}
.btn-cancel{height:36px;background:transparent;color:var(--text-3);border:1px solid var(--border-2);border-radius:var(--r);padding:0 15px;font-family:'Kanit',sans-serif;font-size:13px;cursor:pointer;transition:all .15s}
.btn-cancel:hover{background:var(--surface-3);color:var(--text-2)}

.toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(14px);background:var(--ink);color:#fff;padding:10px 20px;border-radius:10px;font-size:12px;font-weight:500;opacity:0;transition:all .24s ease;z-index:999;pointer-events:none;white-space:nowrap;box-shadow:0 6px 28px rgba(4,10,22,.35);border-left:3px solid var(--gold);letter-spacing:.02em}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}

@keyframes slideUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.stat-card{animation:slideUp .3s ease both}
.stat-card:nth-child(1){animation-delay:.04s} .stat-card:nth-child(2){animation-delay:.08s}
.stat-card:nth-child(3){animation-delay:.12s} .stat-card:nth-child(4){animation-delay:.16s}

@media(max-width:768px){
  .topbar,main{padding-left:16px;padding-right:16px}
  .stats-row{grid-template-columns:1fr 1fr}
  .filter-bar{flex-direction:column;align-items:stretch}
  .fi{width:100%} .fsep{display:none}
  .hdr-date{display:none} .nav-btn span{display:none} .nav-btn{padding:0 10px}
  .m-row{grid-template-columns:1fr}
}
</style>
</head>
<body>

<header class="topbar">
  <div class="brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg>
    </div>
    <div class="bdiv"></div>
    <div class="brand-text">
      <div class="name">ระบบบริหารการจัดส่ง</div>
      <div class="sub">Delivery Management System</div>
    </div>
  </div>
  <div class="topbar-right">
    <span class="hdr-date" id="hdr-date"></span>
    <div class="tsep"></div>
    <a href="oil" class="nav-btn nb-oil" style="text-decoration:none">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22V8l9-6 9 6v14"/><path d="M3 22h18"/><path d="M12 22V12"/><circle cx="12" cy="8" r="2"/></svg>
      <span>บันทึกน้ำมัน</span>
    </a>
    <a href="service" class="nav-btn nb-insp" style="text-decoration:none">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
      <span>ตรวจสภาพรถ</span>
    </a>
    <div class="tsep"></div>
    <div class="hdr-user"><div class="hdr-av">A</div><span class="hdr-name">Admin</span></div>
  </div>
</header>

<main>
  <div class="stats-row">
    <div class="stat-card sc-blue">
      <div class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="11" width="14" height="10" rx="2"/><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg></div>
      <div><div class="stat-lbl">รายการทั้งหมด</div><div class="stat-val" id="s-total">0</div></div>
    </div>
    <div class="stat-card sc-amber">
      <div class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <div><div class="stat-lbl">รอดำเนินการ</div><div class="stat-val" id="s-pending">0</div></div>
    </div>
    <div class="stat-card sc-green">
      <div class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg></div>
      <div><div class="stat-lbl">เสร็จสิ้น</div><div class="stat-val" id="s-done">0</div></div>
    </div>
    <div class="stat-card sc-red">
      <div class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
      <div><div class="stat-lbl">จัดส่งผิด</div><div class="stat-val" id="s-wrong">0</div></div>
    </div>
  </div>

  <div class="filter-bar">
    <div class="filter-tag">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      กรอง
    </div>
    <div class="fsep"></div>
    <input type="date" id="f-date" class="fi" style="width:150px">
    <select id="f-driver" class="fi">
      <option value="">— คนขับทั้งหมด —</option>
      <option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option>
      <option>หรั่ง</option><option>เก่ง</option><option>เอ</option>
      <option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option>
    </select>
    <button class="btn-go" onclick="load()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      ค้นหา
    </button>
    <button class="btn-rst" onclick="resetFilter()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
      รีเซ็ต
    </button>
  </div>

  <div class="tbl-card">
    <div class="tbl-head">
      <div class="tbl-head-l">
        <span class="tbl-title">รายการทั้งหมด</span>
        <span class="rec-badge" id="tbl-count">0</span>
      </div>
      <div class="srch-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="srch-inp" placeholder="ค้นหา..." oninput="doSearch(this.value)">
      </div>
    </div>
    <div class="tbl-scroll">
      <table>
        <colgroup><col class="c-n"><col class="c-d"><col class="c-nm"><col class="c-bill"><col class="c-job"><col class="c-note"><col class="c-st"></colgroup>
        <thead>
          <tr><th>#</th><th>วันที่</th><th>ชื่อ Sale</th><th>เลขที่บิล</th><th class="th-hl">งาน</th><th class="th-hl">หมายเหตุ</th><th>สถานะ</th></tr>
        </thead>
        <tbody id="tbody">
          <tr><td colspan="7" class="empty-cell">
            <div class="empty-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg></div>
            <div class="empty-t">ยังไม่มีข้อมูล</div>
            <div class="empty-s">กรุณาเลือกวันที่และกดค้นหาเพื่อแสดงรายการ</div>
          </td></tr>
        </tbody>
      </table>
    </div>
    <div class="tbl-foot"><span id="ft-info">—</span><span id="ft-time">—</span></div>
  </div>
</main>

<!-- MODAL น้ำมัน -->
<div class="m-ov" id="modal-oil">
  <div class="modal">
    <div class="modal-head">
      <div class="m-ico oil"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22V8l9-6 9 6v14"/><path d="M3 22h18"/><path d="M12 22V12"/><circle cx="12" cy="8" r="2"/></svg></div>
      <div><div class="m-title">บันทึกการเติมน้ำมัน</div><div class="m-sub">บันทึกข้อมูลการเติมน้ำมันประจำวัน</div></div>
      <button class="m-close" onclick="closeModal('oil')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="m-sect"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>ข้อมูลทั่วไป</div>
      <div class="m-row">
        <div class="m-field"><label>วันที่</label><input type="date" id="oil-date"></div>
        <div class="m-field"><label>คนขับ</label><select id="oil-driver"><option value="">— เลือก —</option><option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option><option>หรั่ง</option><option>เก่ง</option><option>เอ</option><option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option></select></div>
      </div>
      <div class="m-sect"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>รายละเอียดรถ</div>
      <div class="m-row">
        <div class="m-field"><label>ทะเบียนรถ</label><input type="text" id="oil-plate" placeholder="เช่น กข-1234"></div>
        <div class="m-field"><label>เลขไมล์ (กม.)</label><input type="number" id="oil-mileage" placeholder="000000"></div>
      </div>
      <div class="m-sect"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>ข้อมูลน้ำมัน</div>
      <div class="m-row">
        <div class="m-field"><label>ปริมาณ (ลิตร)</label><input type="number" id="oil-liters" placeholder="0.00"></div>
        <div class="m-field"><label>ยอดเงิน (บาท)</label><input type="number" id="oil-amount" placeholder="0.00"></div>
      </div>
      <div class="m-field"><label>หมายเหตุ</label><textarea id="oil-note" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea></div>
    </div>
    <div class="modal-foot">
      <button class="btn-cancel" onclick="closeModal('oil')">ยกเลิก</button>
      <button class="btn-save" onclick="saveOil()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>บันทึกข้อมูล</button>
    </div>
  </div>
</div>

<!-- MODAL ตรวจสภาพรถ -->
<div class="m-ov" id="modal-inspect">
  <div class="modal">
    <div class="modal-head">
      <div class="m-ico insp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg></div>
      <div><div class="m-title">ตรวจสภาพรถ</div><div class="m-sub">บันทึกผลการตรวจสภาพรถประจำวัน</div></div>
      <button class="m-close" onclick="closeModal('inspect')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="m-sect"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>ข้อมูลรถ</div>
      <div class="m-row">
        <div class="m-field"><label>วันที่ตรวจ</label><input type="date" id="insp-date"></div>
        <div class="m-field"><label>คนขับ</label><select id="insp-driver"><option value="">— เลือก —</option><option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option><option>หรั่ง</option><option>เก่ง</option><option>เอ</option><option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option></select></div>
      </div>
      <div class="m-row">
        <div class="m-field"><label>ทะเบียนรถ</label><input type="text" id="insp-plate" placeholder="เช่น กข-1234"></div>
        <div class="m-field"><label>เลขไมล์ (กม.)</label><input type="number" id="insp-mileage" placeholder="เช่น 123456"></div>
      </div>
      <div class="m-sect"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>ผลการตรวจ</div>
      <div class="m-row">
        <div class="m-field"><label>สภาพโดยรวม</label><select id="insp-condition"><option value="">— เลือก —</option><option value="good">✅ ปกติ / ดี</option><option value="warn">⚠️ มีปัญหาเล็กน้อย</option><option value="bad">❌ ต้องซ่อมแซม</option></select></div>
        <div class="m-field"><label>ยางรถ</label><select id="insp-tire"><option value="">— เลือก —</option><option value="good">✅ ปกติ</option><option value="warn">⚠️ ต้องตรวจ</option><option value="bad">❌ ต้องเปลี่ยน</option></select></div>
      </div>
      <div class="m-row">
        <div class="m-field"><label>ไฟรถ</label><select id="insp-lights"><option value="">— เลือก —</option><option value="good">✅ ปกติ</option><option value="bad">❌ มีปัญหา</option></select></div>
        <div class="m-field"><label>เบรก</label><select id="insp-brake"><option value="">— เลือก —</option><option value="good">✅ ปกติ</option><option value="warn">⚠️ ต้องตรวจ</option><option value="bad">❌ ต้องซ่อม</option></select></div>
      </div>
      <div class="m-field"><label>หมายเหตุ</label><input type="text" id="insp-note" placeholder="ระบุรายละเอียดปัญหาที่พบ (ถ้ามี)"></div>
    </div>
    <div class="modal-foot">
      <button class="btn-cancel" onclick="closeModal('inspect')">ยกเลิก</button>
      <button class="btn-save" onclick="saveInspect()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>บันทึก</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const STATUS=[
  {v:'',l:'— ยังไม่ระบุ',c:'st-empty'},
  {v:'newbill',l:'สร้างบิลใหม่',c:'st-newbill'},
  {v:'return',l:'ลูกค้าเก็บบิล / ของกลับมา',c:'st-return'},
  {v:'stock',l:'เข้าสต็อก',c:'st-stock'},
  {v:'wrong',l:'จัดส่งผิด',c:'st-wrong'},
];
const DUMMY=[
  {id:'RET-001',date:'30/03/2026',customer:'สมชาย มีสุข',po:'CS6903-00051',job:'บริษัท เอบีซี จำกัด',note:'ลูกค้าไม่อยู่บ้าน',delivery_status:'',new_bill:'',driver:'บังเดช'},
  {id:'RET-002',date:'31/03/2026',customer:'สุดา แก้วใส',po:'46904-00363',job:'ห้างหุ้นส่วน เดลต้า',note:'ปิดร้าน ไม่มีคนรับของ',delivery_status:'return',new_bill:'',driver:'แชม'},
  {id:'RET-003',date:'01/04/2026',customer:'วิชัย รักดี',po:'CP6903-00051',job:'บริษัท สยามเทรด จำกัด',note:'ติดต่อไม่ได้ โทรไม่รับ',delivery_status:'stock',new_bill:'',driver:'กอล์ฟ'},
  {id:'RET-004',date:'30/03/2026',customer:'มาลี สวยงาม',po:'CS6903-00052',job:'บริษัท ไทยซัพพลาย จำกัด',note:'ที่อยู่ไม่ชัดเจน หาไม่เจอ',delivery_status:'',new_bill:'',driver:'หรั่ง'},
  {id:'RET-005',date:'31/03/2026',customer:'ประสิทธิ์ ดีมาก',po:'CP6903-00053',job:'ร้าน โปรเกรส เทค',note:'ลูกค้าขอเลื่อนรับของ',delivery_status:'wrong',new_bill:'',driver:'เก่ง'},
  {id:'RET-006',date:'01/04/2026',customer:'อารีย์ ใจดี',po:'46904-00364',job:'บริษัท นิวเวฟ จำกัด',note:'สินค้าเสียหายระหว่างขนส่ง',delivery_status:'newbill',new_bill:'NB-2026-001',driver:'เอ'},
  {id:'RET-007',date:'30/03/2026',customer:'ธนา รุ่งเรือง',po:'CS6903-00054',job:'ร้าน เอสพี มาร์ท',note:'เก็บเงินปลายทาง ลูกค้าไม่พร้อมจ่าย',delivery_status:'',new_bill:'',driver:'ยุทร'},
  {id:'RET-008',date:'31/03/2026',customer:'จิรา ภักดี',po:'CP6903-00055',job:'บริษัท โกลบอลเทค',note:'เข้าพื้นที่ไม่ได้ ต้องใช้บัตรผ่าน',delivery_status:'stock',new_bill:'',driver:'แฟรงค์'},
  {id:'RET-009',date:'01/04/2026',customer:'เอกชัย มั่นคง',po:'46904-00365',job:'ร้าน วีไอพี เทรดดิ้ง',note:'ฝนตกหนัก ไม่สามารถจัดส่งได้',delivery_status:'return',new_bill:'',driver:'เอ้'},
  {id:'RET-010',date:'30/03/2026',customer:'สุพัตรา งามดี',po:'CS6903-00056',job:'บริษัท ซันไรส์ จำกัด',note:'เวลาส่งไม่ตรงกับลูกค้านัด',delivery_status:'',new_bill:'',driver:'บังเดช'},
];

let rows=[],searchQ='';

(function init(){
  const now=new Date();
  document.getElementById('hdr-date').textContent=now.toLocaleDateString('th-TH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
  const td=now.toISOString().slice(0,10);
  document.getElementById('f-date').value=td;
  document.getElementById('oil-date').value=td;
  document.getElementById('insp-date').value=td;
  rows=DUMMY.map(r=>({...r}));
  render(rows);updateStats(rows);
})();

function load(){
  const di=document.getElementById('f-date').value,dr=document.getElementById('f-driver').value;
  let fd='';if(di){const[y,m,d]=di.split('-');fd=`${d}/${m}/${y}`;}
  rows=DUMMY.filter(r=>(!fd||r.date===fd)&&(!dr||r.driver===dr)).map(r=>({...r}));
  searchQ='';render(rows);updateStats(rows);
}
function resetFilter(){
  document.getElementById('f-date').value=new Date().toISOString().slice(0,10);
  document.getElementById('f-driver').value='';
  rows=DUMMY.map(r=>({...r}));searchQ='';render(rows);updateStats(rows);
}
function updateStats(data){
  document.getElementById('s-total').textContent=data.length;
  document.getElementById('s-pending').textContent=data.filter(r=>!r.delivery_status).length;
  document.getElementById('s-done').textContent=data.filter(r=>r.delivery_status==='stock').length;
  document.getElementById('s-wrong').textContent=data.filter(r=>r.delivery_status==='wrong').length;
}
function render(data){
  document.getElementById('tbl-count').textContent=data.length+' รายการ';
  const t=new Date();
  document.getElementById('ft-info').textContent=`แสดง ${data.length} รายการ`;
  document.getElementById('ft-time').textContent=`อัพเดต ${t.toLocaleTimeString('th-TH')}`;
  if(!data.length){
    document.getElementById('tbody').innerHTML=rows.length===0
      ?`<tr><td colspan="7" class="empty-cell"><div class="empty-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg></div><div class="empty-t">ยังไม่มีข้อมูล</div><div class="empty-s">กรุณาเลือกวันที่และกดค้นหา</div></td></tr>`
      :`<tr><td colspan="7" class="empty-cell"><div class="empty-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div><div class="empty-t">ไม่พบรายการ</div><div class="empty-s">ไม่มีข้อมูลที่ตรงกับเงื่อนไข</div></td></tr>`;
    return;
  }
  document.getElementById('tbody').innerHTML=data.map((r,i)=>{
    const sv=r.delivery_status||'';
    const sc=STATUS.find(s=>s.v===sv)?.c||'st-empty';
    const opts=STATUS.map(s=>`<option value="${s.v}"${sv===s.v?' selected':''}>${s.l}</option>`).join('');
    const isNew=sv==='newbill';
    return `<tr>
      <td><span class="row-num">${i+1}</span></td>
      <td><span class="cell-date">${r.date||'—'}</span></td>
      <td><span class="cell-name">${r.customer||'—'}</span></td>
      <td><span class="cell-bill">${r.po||r.id||'—'}</span></td>
      <td class="td-job"><span class="cell-job">${r.job||'—'}</span></td>
      <td class="td-note" title="${r.note||''}">${r.note?`<span class="cell-note">${r.note}</span>`:`<span class="cell-note empty">—</span>`}</td>
      <td>
        <div class="st-cell">
          <select class="${sc} st-sel" data-id="${r.id}" onchange="changeStatus(this)">${opts}</select>
          <div class="nb-wrap${isNew?' show':''}" id="nb-${r.id}">
            <input class="nb-inp" id="nb-input-${r.id}" type="text" placeholder="เลขบิลใหม่..." value="${r.new_bill||''}" onkeydown="if(event.key==='Enter')saveNewBill('${r.id}')">
            <button class="nb-save" onclick="saveNewBill('${r.id}')">บันทึก</button>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('');
}
function changeStatus(sel){
  STATUS.forEach(s=>sel.classList.remove(s.c));
  sel.classList.add(STATUS.find(s=>s.v===sel.value)?.c||'st-empty');
  const id=sel.dataset.id,isNew=sel.value==='newbill';
  const nb=document.getElementById('nb-'+id);
  if(isNew){nb.classList.add('show');setTimeout(()=>document.getElementById('nb-input-'+id)?.focus(),50);}
  else{nb.classList.remove('show');saveStatus(id,sel.value,null);}
  const r=rows.find(r=>r.id===id);if(r)r.delivery_status=sel.value;
  updateStats(rows);
}
async function saveNewBill(id){
  const inp=document.getElementById('nb-input-'+id);
  const v=inp?inp.value.trim():'';
  if(!v){showToast('⚠ กรุณาระบุเลขบิลใหม่');inp?.focus();return;}
  const r=rows.find(r=>r.id===id);if(r)r.new_bill=v;
  await saveStatus(id,'newbill',v);
}
async function saveStatus(id,status,newBill){
  try{
    const body={status:status||'processing',updated_by:'delivery'};
    if(newBill)body.new_bill=newBill;
    const res=await fetch('/return/'+encodeURIComponent(id)+'/status',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.head.querySelector('meta[name="csrf-token"]').getAttribute('content'),'Accept':'application/json'},body:JSON.stringify(body)});
    if(!res.ok)throw new Error('HTTP '+res.status);
  }catch(e){}
  showToast(newBill?`✓ บันทึกบิลใหม่ ${newBill} เรียบร้อย`:'✓ บันทึกสถานะเรียบร้อยแล้ว');
}
function doSearch(q){
  searchQ=q.toLowerCase().trim();
  render(searchQ?rows.filter(r=>(r.po||r.id||'').toLowerCase().includes(searchQ)||(r.customer||'').toLowerCase().includes(searchQ)||(r.job||'').toLowerCase().includes(searchQ)||(r.note||'').toLowerCase().includes(searchQ)):rows);
}
function openModal(t){document.getElementById('modal-'+(t==='oil'?'oil':'inspect')).classList.add('show')}
function closeModal(t){document.getElementById('modal-'+(t==='oil'?'oil':'inspect')).classList.remove('show')}
document.querySelectorAll('.m-ov').forEach(o=>{o.addEventListener('click',function(e){if(e.target===this)this.classList.remove('show')})});
function saveOil(){
  const d=document.getElementById('oil-driver').value,p=document.getElementById('oil-plate').value.trim(),l=document.getElementById('oil-liters').value,a=document.getElementById('oil-amount').value;
  if(!d){showToast('⚠ กรุณาเลือกคนขับ');return;}if(!p){showToast('⚠ กรุณากรอกทะเบียนรถ');return;}
  if(!l){showToast('⚠ กรุณากรอกปริมาณน้ำมัน');return;}if(!a){showToast('⚠ กรุณากรอกยอดเงิน');return;}
  closeModal('oil');showToast(`✓ บันทึก ${l} ลิตร ฿${Number(a).toLocaleString()} เรียบร้อย`);
  ['oil-driver','oil-plate','oil-mileage','oil-liters','oil-amount','oil-note'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
}
function saveInspect(){
  const d=document.getElementById('insp-driver').value,p=document.getElementById('insp-plate').value.trim(),c=document.getElementById('insp-condition').value;
  if(!d){showToast('⚠ กรุณาเลือกคนขับ');return;}if(!p){showToast('⚠ กรุณากรอกทะเบียนรถ');return;}if(!c){showToast('⚠ กรุณาเลือกสภาพโดยรวม');return;}
  closeModal('inspect');showToast(`✓ บันทึกตรวจสภาพรถ ${p} เรียบร้อย`);
  ['insp-driver','insp-plate','insp-mileage','insp-condition','insp-tire','insp-lights','insp-brake','insp-note'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
}
function showToast(msg){
  const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2800);
}
</script>
</body>
</html>
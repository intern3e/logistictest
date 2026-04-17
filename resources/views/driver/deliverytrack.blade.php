<!DOCTYPE html>
<html lang="th">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="dummy-csrf-token">
<title>ระบบบริหารการจัดส่ง</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --blue-950:#041830;
  --blue-900:#073260;
  --blue-800:#0c4a8c;
  --blue-700:#1560b8;
  --blue-600:#1a75d8;
  --blue-500:#2e8bef;
  --blue-400:#60aaff;
  --blue-300:#93c5fd;
  --blue-200:#bfdbfe;
  --blue-100:#dbeafe;
  --blue-50:#eff6ff;

  --gray-900:#111827;
  --gray-800:#1f2937;
  --gray-700:#374151;
  --gray-600:#4b5563;
  --gray-500:#6b7280;
  --gray-400:#9ca3af;
  --gray-300:#d1d5db;
  --gray-200:#e5e7eb;
  --gray-100:#f3f4f6;
  --gray-50:#f9fafb;

  --white:#ffffff;
  --surface:#f5f8ff;

  --green-700:#15803d;
  --green-600:#16a34a;
  --green-100:#dcfce7;
  --green-50:#f0fdf4;

  --amber-700:#b45309;
  --amber-100:#fef3c7;
  --amber-50:#fffbeb;

  --red-700:#b91c1c;
  --red-600:#dc2626;
  --red-100:#fee2e2;
  --red-50:#fef2f2;

  --r:6px;
  --rl:10px;
}

html,body{height:100%}
body{font-family:'IBM Plex Sans Thai',sans-serif;background:var(--surface);color:var(--gray-900);font-size:14px;line-height:1.6;display:flex;flex-direction:column;min-height:100vh }

/* TOP BAR */
.topbar{
  background:var(--blue-900);
  height:56px;flex-shrink:0;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 24px;
  position:sticky;top:0;z-index:100;
  border-bottom:1px solid var(--blue-800);
}
.brand{display:flex;align-items:center;gap:10px}
.brand-mark{
  width:32px;height:32px;
  background:var(--blue-600);
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  border:1px solid var(--blue-500);
}
.brand-mark svg{width:15px;height:15px;color:#fff}
.brand-text .name{font-size:13.5px;font-weight:600;color:#fff;letter-spacing:0.01em}
.brand-text .sub{font-size:10px;color:var(--blue-300);letter-spacing:0.08em;margin-top:1px;font-weight:400}
.topbar-right{display:flex;align-items:center;gap:6px}
.tsep{width:1px;height:20px;background:var(--blue-800);margin:0 4px}
.hdr-date{font-family:'IBM Plex Mono',monospace;font-size:10.5px;color:var(--blue-300);letter-spacing:0.03em}
.nav-btn{
  height:30px;padding:0 12px;
  border-radius:6px;
  font-family:'IBM Plex Sans Thai',sans-serif;
  font-size:12px;font-weight:500;
  cursor:pointer;text-decoration:none;
  border:1px solid;
  display:flex;align-items:center;gap:5px;
  transition:background .15s,border-color .15s;
  letter-spacing:0.01em;
}
.nav-btn svg{width:11px;height:11px;flex-shrink:0}
.nb-oil{color:#fcd34d;background:rgba(252,211,77,.08);border-color:rgba(252,211,77,.25)}
.nb-oil:hover{background:rgba(252,211,77,.16);border-color:rgba(252,211,77,.4)}
.nb-insp{color:#86efac;background:rgba(134,239,172,.08);border-color:rgba(134,239,172,.25)}
.nb-insp:hover{background:rgba(134,239,172,.16);border-color:rgba(134,239,172,.4)}

main{flex:1;display:flex;flex-direction:column;padding:20px 24px;gap:12px}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;flex-shrink:0}
.stat-card{
  background:var(--white);
  border:1px solid var(--gray-200);
  border-radius:var(--rl);
  padding:16px 18px;
  display:flex;align-items:center;gap:14px;
  transition:border-color .15s;
}
.stat-card:hover{border-color:var(--blue-300)}
.stat-ico{
  width:36px;height:36px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.sc-blue .stat-ico{background:var(--blue-100)}
.sc-amber .stat-ico{background:var(--amber-100)}
.sc-green .stat-ico{background:var(--green-100)}
.sc-red .stat-ico{background:var(--red-100)}
.stat-ico svg{width:16px;height:16px}
.sc-blue .stat-ico svg{color:var(--blue-600)}
.sc-amber .stat-ico svg{color:var(--amber-700)}
.sc-green .stat-ico svg{color:var(--green-600)}
.sc-red .stat-ico svg{color:var(--red-600)}
.stat-lbl{font-size:10.5px;font-weight:500;color:var(--gray-500);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:3px}
.stat-val{font-size:28px;font-weight:600;color:var(--gray-900);font-family:'IBM Plex Mono',monospace;line-height:1}
.stat-accent{height:3px;border-radius:0 0 var(--rl) var(--rl);margin:-1px -1px -1px;display:none}

/* FILTER */
.filter-bar{
  background:var(--white);
  border:1px solid var(--gray-200);
  border-radius:var(--rl);
  padding:11px 16px;
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.filter-label{font-size:11px;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.1em;white-space:nowrap;display:flex;align-items:center;gap:5px}
.filter-label svg{width:10px;height:10px}
.fsep{width:1px;height:22px;background:var(--gray-200)}
.fi{
  height:32px;border:1px solid var(--gray-300);border-radius:var(--r);
  padding:0 10px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;
  color:var(--gray-900);background:var(--gray-50);outline:none;
  transition:border-color .15s,box-shadow .15s;
}
.fi:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(26,117,216,.12);background:var(--white)}
select.fi{
  appearance:none;cursor:pointer;padding-right:26px;min-width:150px;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7280'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 8px center;
}
.btn-go{
  height:32px;background:var(--blue-700);color:#fff;
  border:none;border-radius:var(--r);padding:0 14px;
  font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;font-weight:500;
  cursor:pointer;display:flex;align-items:center;gap:6px;
  transition:background .15s;white-space:nowrap;
}
.btn-go:hover{background:var(--blue-800)}
.btn-go svg{width:11px;height:11px}
.btn-rst{
  height:32px;background:transparent;color:var(--gray-600);
  border:1px solid var(--gray-300);border-radius:var(--r);padding:0 12px;
  font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;cursor:pointer;
  display:flex;align-items:center;gap:5px;transition:all .15s;
}
.btn-rst:hover{background:var(--gray-100);border-color:var(--gray-400)}
.btn-rst svg{width:11px;height:11px}

/* TABLE CARD */
.tbl-card{
  background:var(--white);
  border:1px solid var(--gray-200);
  border-radius:var(--rl);
  flex:1;display:flex;flex-direction:column;min-height:0;overflow:hidden;
}
.tbl-head{
  padding:11px 16px;
  border-bottom:1px solid var(--gray-200);
  display:flex;align-items:center;justify-content:space-between;
  flex-shrink:0;flex-wrap:wrap;gap:8px;
  background:var(--white);
}
.tbl-head-l{display:flex;align-items:center;gap:8px}
.tbl-title{font-size:13px;font-weight:600;color:var(--gray-800)}
.rec-badge{
  background:var(--blue-100);color:var(--blue-800);
  border-radius:20px;padding:2px 10px;
  font-size:11px;font-weight:600;
  font-family:'IBM Plex Mono',monospace;
  letter-spacing:0.02em;
}
.srch-wrap{position:relative}
.srch-wrap svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--gray-400);pointer-events:none;width:12px;height:12px}
.srch-inp{
  height:30px;border:1px solid var(--gray-300);border-radius:var(--r);
  padding:0 10px 0 28px;font-family:'IBM Plex Sans Thai',sans-serif;
  font-size:12.5px;color:var(--gray-900);background:var(--gray-50);
  outline:none;width:200px;transition:all .15s;
}
.srch-inp:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(26,117,216,.1);width:230px;background:var(--white)}

/* TABLE */
.tbl-scroll{flex:1;overflow:auto}
table{width:100%;border-collapse:collapse;table-layout:fixed}
col.c-n{width:44px} col.c-d{width:100px} col.c-nm{width:140px}
col.c-bill{width:148px} col.c-job{width:200px} col.c-note{width:185px} col.c-st{width:192px}
thead th {
  padding: 10px 14px;
  font-size: 10.5px;
  font-weight: 600;
  /* เปลี่ยนสีข้อความเป็นขาวหรือฟ้าอ่อนเพื่อให้ตัดกับพื้นหลังเข้ม */
  color: #ffffff; 
  text-transform: uppercase;
  letter-spacing: 0.1em;
  /* พื้นหลังสีน้ำเงินเข้ม */
  background: #1e3a8a; /* Deep Blue (เทียบเท่า blue-900) */
  /* เส้นขอบล่างให้มีความสว่างขึ้นเล็กน้อยเพื่อให้เห็นขอบเขต */
  border-bottom: 2px solid #020202; 
  white-space: nowrap;
  text-align: center;
  position: sticky;
  top: 0;
  z-index: 2;
}

/* ส่วนที่ Highlight ให้สว่างขึ้นกว่าปกติ หรือเปลี่ยนเป็นสีน้ำเงินสด */
thead th.th-hl {
  background: #1e3a8a; /* Slightly lighter deep blue */
  color: #eff6ff;
}
tbody tr{border-bottom:1px solid var(--gray-100);transition:background .08s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--blue-50)}
tbody td{padding:12px 14px;font-size:13px;vertical-align:middle;color:var(--gray-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:center}
tbody td.td-job,tbody td.td-note{text-align:left}

.row-num{font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--gray-400);font-weight:500}
.cell-date{font-family:'IBM Plex Mono',monospace;font-size:11.5px;color:var(--gray-600);letter-spacing:0.02em}
.cell-name{font-weight:600;color:var(--blue-800);font-size:13px}

.cell-bill{
  display:inline-flex;align-items:center;
  font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:600;
  color:var(--red-700);
  background:var(--red-50);
  border:1px solid var(--red-200);
  border-radius:5px;padding:3px 9px;
  letter-spacing:0.04em;
}
.cell-bill:hover{background:var(--red-100);border-color:var(--red-300)}

.cell-job{font-size:13px;color:var(--gray-800);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cell-note{font-size:12px;color:var(--gray-500);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cell-note.empty{color:var(--gray-300)}

/* STATUS */
.st-cell{display:flex;flex-direction:column;align-items:stretch;gap:5px;width:100%}
.st-sel{
  border:1px solid;border-radius:6px;padding:0 26px 0 10px;
  font-family:'IBM Plex Sans Thai',sans-serif;font-size:12px;font-weight:500;
  cursor:pointer;outline:none;appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='currentColor' opacity='.5'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 7px center;
  transition:all .12s;width:100%;height:30px;
}
.st-sel option{color:#000;background:#fff;padding:5px 8px}
.st-empty{background:var(--gray-50);color:var(--gray-500);border-color:var(--gray-300)}
.st-newbill{background:var(--blue-50);color:var(--blue-700);border-color:var(--blue-300)}
.st-return{background:var(--amber-50);color:var(--amber-700);border-color:#fcd34d}
.st-stock{background:var(--green-50);color:var(--green-700);border-color:#86efac}
.st-wrong{background:var(--red-50);color:var(--red-700);border-color:#fca5a5}

/* NEW BILL */
.nb-wrap{display:none;align-items:stretch;gap:5px;width:100%;animation:fd .12s ease}
.nb-wrap.show{display:flex}
@keyframes fd{from{opacity:0;transform:translateY(-3px)}to{opacity:1;transform:translateY(0)}}
.nb-inp{
  height:30px;border:1px solid var(--blue-300);border-radius:5px;
  padding:0 9px;font-family:'IBM Plex Mono',monospace;font-size:11.5px;
  font-weight:500;color:var(--blue-800);background:var(--white);
  outline:none;flex:1;min-width:0;transition:border-color .12s;
}
.nb-inp::placeholder{color:var(--blue-300);font-family:'IBM Plex Sans Thai',sans-serif;font-size:11px}
.nb-inp:focus{border-color:var(--blue-500);box-shadow:0 0 0 2px rgba(26,117,216,.12)}
.nb-save{
  height:30px;background:var(--blue-700);color:#fff;
  border:none;border-radius:5px;padding:0 12px;
  font-family:'IBM Plex Sans Thai',sans-serif;font-size:12px;font-weight:500;
  cursor:pointer;white-space:nowrap;transition:background .12s;flex-shrink:0;
}
.nb-save:hover{background:var(--blue-800)}

.tbl-foot{
  padding:9px 16px;border-top:1px solid var(--gray-200);
  background:var(--gray-50);
  display:flex;justify-content:space-between;align-items:center;
  font-size:11px;color:var(--gray-500);
  font-family:'IBM Plex Mono',monospace;letter-spacing:0.02em;
  flex-shrink:0;
}

.empty-cell{padding:64px 24px !important;text-align:center !important;white-space:normal !important}
.empty-ico{width:44px;height:44px;background:var(--gray-100);border:1px solid var(--gray-200);border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--gray-400)}
.empty-t{font-size:13px;font-weight:600;color:var(--gray-700);margin-bottom:4px}
.empty-s{font-size:12px;color:var(--gray-400)}

/* MODAL */
.m-ov{position:fixed;inset:0;background:rgba(3,16,40,.5);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .18s;padding:16px}
.m-ov.show{opacity:1;pointer-events:all}
.modal{background:var(--white);border-radius:12px;width:480px;max-width:calc(100vw - 32px);border:1px solid var(--gray-200);transform:translateY(10px);transition:transform .2s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
.m-ov.show .modal{transform:translateY(0)}
.modal-head{padding:16px 20px 14px;border-bottom:1px solid var(--gray-200);display:flex;align-items:center;gap:11px;background:var(--gray-50)}
.m-ico{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.m-ico svg{width:16px;height:16px}
.m-ico.oil{background:var(--amber-100);color:var(--amber-700)}
.m-ico.insp{background:var(--green-100);color:var(--green-700)}
.m-title{font-size:14px;font-weight:600;color:var(--gray-900)}
.m-sub{font-size:11px;color:var(--gray-500);margin-top:2px}
.m-close{margin-left:auto;background:none;border:1px solid var(--gray-300);border-radius:6px;cursor:pointer;color:var(--gray-500);width:26px;height:26px;display:flex;align-items:center;justify-content:center;transition:all .12s}
.m-close:hover{background:var(--gray-100);color:var(--gray-800)}
.m-close svg{width:12px;height:12px}
.modal-body{padding:18px 20px;display:flex;flex-direction:column;gap:14px}
.m-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.m-field{display:flex;flex-direction:column;gap:5px}
.m-field label{font-size:10.5px;font-weight:600;color:var(--gray-600);letter-spacing:0.07em;text-transform:uppercase}
.m-field input,.m-field select,.m-field textarea{
  height:34px;border:1px solid var(--gray-300);border-radius:var(--r);
  padding:0 10px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;
  color:var(--gray-900);background:var(--gray-50);outline:none;width:100%;transition:all .12s;
}
.m-field textarea{height:58px;padding:8px 10px;resize:none}
.m-field select{appearance:none;cursor:pointer;padding-right:26px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7280'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center}
.m-field input:focus,.m-field select:focus,.m-field textarea:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(26,117,216,.1);background:var(--white)}
.m-sect{font-size:10px;font-weight:600;color:var(--blue-700);text-transform:uppercase;letter-spacing:0.12em;display:flex;align-items:center;gap:6px;padding-bottom:7px;border-bottom:1px solid var(--blue-100)}
.m-sect svg{width:11px;height:11px}
.modal-foot{padding:12px 20px;border-top:1px solid var(--gray-200);background:var(--gray-50);display:flex;justify-content:flex-end;gap:8px}
.btn-save{height:34px;background:var(--blue-700);color:#fff;border:none;border-radius:var(--r);padding:0 16px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .12s}
.btn-save:hover{background:var(--blue-800)}
.btn-save svg{width:11px;height:11px}
.btn-cancel{height:34px;background:transparent;color:var(--gray-600);border:1px solid var(--gray-300);border-radius:var(--r);padding:0 14px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;cursor:pointer;transition:all .12s}
.btn-cancel:hover{background:var(--gray-100);border-color:var(--gray-400)}

.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(10px);
  background:var(--gray-900);color:#fff;
  padding:9px 18px;border-radius:8px;
  font-size:12px;font-weight:500;
  opacity:0;transition:all .2s ease;z-index:999;pointer-events:none;
  white-space:nowrap;border-left:3px solid var(--blue-400);
  letter-spacing:0.01em;
}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}

@media(max-width:768px){
  .topbar,main{padding-left:14px;padding-right:14px}
  .stats-row{grid-template-columns:1fr 1fr}
  .filter-bar{flex-direction:column;align-items:stretch}
  .fi{width:100%} .fsep{display:none}
  .hdr-date{display:none} .nav-btn span{display:none}
  .m-row{grid-template-columns:1fr}
}
</style>
</head>
<body>

<header class="topbar">
  <div class="brand">
    <div class="brand-mark">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg>
    </div>
    <div class="brand-text">
      <div class="name">ระบบบริหารการจัดส่ง</div>
      <div class="sub">Delivery Management System</div>
    </div>
  </div>
  <div class="topbar-right">
    <span class="hdr-date" id="hdr-date"></span>
    <div class="tsep"></div>
    
  </div>
</header>

<main>
  <!-- <div class="stats-row">
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
  </div> -->

  <div class="filter-bar">
    <div class="filter-label">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      กรอง
    </div>
    <div class="fsep"></div>
    <input type="date" id="f-date" class="fi" style="width:148px">
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
            <div class="empty-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg></div>
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
      ?`<tr><td colspan="7" class="empty-cell"><div class="empty-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg></div><div class="empty-t">ยังไม่มีข้อมูล</div><div class="empty-s">กรุณาเลือกวันที่และกดค้นหา</div></td></tr>`
      :`<tr><td colspan="7" class="empty-cell"><div class="empty-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div><div class="empty-t">ไม่พบรายการ</div><div class="empty-s">ไม่มีข้อมูลที่ตรงกับเงื่อนไข</div></td></tr>`;
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
      <td>
  <span class="cell-bill">${r.po || r.id || '—'}</span>
</td>
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
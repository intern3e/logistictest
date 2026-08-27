<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ค้นหาสินค้า - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}
    
    /* Overlay & Block Loading */
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(4px)}
    .ov.on{opacity:1;visibility:visible}
    .progress-container{width: 320px; text-align: center;}
    .ov-text{color:#fff;font-size:18px;font-weight:600;margin-bottom:20px;letter-spacing: 0.5px;}
    .progress-track{
      width: 100%; 
      height: 32px; 
      background: rgba(255,255,255,0.1); 
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 4px; 
      overflow: hidden; 
      margin-bottom: 12px;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    .progress-fill{
      width: 0%; 
      height: 100%; 
      background-color: #5B65F3;
      background-image: repeating-linear-gradient(
        90deg,
        #5B65F3 0px,
        #5B65F3 18px,
        rgba(255,255,255,0.15) 18px,
        rgba(255,255,255,0.15) 20px
      );
      background-size: 20px 100%;
      transition: width 0.15s ease-out;
      box-shadow: 0 0 15px rgba(91,101,243,0.6);
    }
    .ov-percent{color:#fff;font-size:16px;font-weight:700;font-variant-numeric: tabular-nums; text-shadow: 0 2px 4px rgba(0,0,0,0.3);}

    .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 24px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:36px;border-radius:6px}
    .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
    .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
    .hamburger{background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;display:flex;flex-direction:column;gap:5px;flex-shrink:0;transition:background .2s}
    .hamburger span{display:block;width:20px;height:2px;background:#374151;transition:background .2s}
    .hamburger:hover{background:#f3f4f6}
    .hamburger:hover span{background:#5B65F3}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-280px;width:260px;height:100vh;z-index:1600;transition:left .3s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:4px 0 24px rgba(0,0,0,.08)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #e5e7eb;min-height:64px}
    .sb-head img{height:32px;border-radius:6px}
    .sb-head span{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .sb-close{background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;font-weight:bold;padding:4px 8px;border-radius:6px;transition:all .2s}
    .sb-close:hover{background:#f3f4f6;color:#111827}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 0}
    .sb-sec{padding:12px 20px 6px;font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.05em;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#374151;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none;transition:all .2s;border-radius:0 8px 8px 0;margin-right:8px}
    .sb-item:hover{background:#f9fafb;border-left-color:#5B65F3;color:#111827}
    .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#5B65F3;font-weight:600}
    #content{padding:88px 16px 24px;width:100%}
    .card{margin-bottom:20px;padding:20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .card h2{font-size:20px;font-weight:700;color:#111827;margin-bottom:16px;letter-spacing:-0.025em}
    .abar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
    .abar input,.abar select{padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:'Sarabun',sans-serif;background:#fff;transition:all .2s;color:#111827;flex:1;min-width:120px}
    .abar input:focus,.abar select:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .btn{padding:10px 20px;font-size:14px;font-weight:600;font-family:'Sarabun',sans-serif;cursor:pointer;white-space:nowrap;border:none;border-radius:8px;transition:all .2s}
    .btn-add{background:#10b981;color:#fff}
    .btn-add:hover{background:#059669}
    .btn-clr{background:#fff;color:#374151;border:1px solid #d1d5db}
    .btn-clr:hover{background:#f9fafb;border-color:#9ca3af}
    .btn-edit{background:#5B65F3;color:#fff}
    .btn-edit:hover{background:#4F46E5}
    .btn-save{background:#10b981;color:#fff}
    .btn-save:hover{background:#059669}
    .btn-del{background:#ef4444;color:#fff}
    .btn-del:hover{background:#dc2626}
    .btn-can{background:#e5e7eb;color:#374151}
    .btn-can:hover{background:#d1d5db}
    .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:#f9fafb}
    th{color:#374151;padding:14px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#f9fafb;z-index:5;border:1px solid #e5e7eb;border-top:none}
    th:first-child{border-left:none}
    th:last-child{border-right:none}
    td{padding:12px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:middle}
    td:first-child{border-left:none}
    td:last-child{border-right:none}
    tbody tr:hover{background:#f9fafb}
    tr.sub-row{background:#f9fafb}
    tr.sub-row td{padding:10px 16px;font-size:13px;border:1px solid #e5e7eb;border-top:none}
    tr.sub-row:hover{background:#f3f4f6}
    tr.sub-row.hide{display:none}
    tr.sub-form-row td{background:#EEF2FF;border:1px solid #e5e7eb;border-top:none;padding:16px;overflow:visible;white-space:normal}
    tr.sub-form-row.hide{display:none}
    .name-link{color:#5B65F3;cursor:pointer;text-decoration:none;font-weight:500;transition:color .2s}
    .name-link:hover{color:#4F46E5;text-decoration:underline}
    .expand-btn{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#e5e7eb;color:#374151;border:none;cursor:pointer;font-size:12px;font-weight:700;margin-right:6px;vertical-align:middle;border-radius:4px;transition:all .2s}
    .expand-btn:hover{background:#5B65F3;color:#fff}
    .expand-btn.open{background:#5B65F3;color:#fff;transform:rotate(90deg)}
    .add-sub-btn{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#10b981;color:#fff;border:none;cursor:pointer;font-size:16px;font-weight:700;vertical-align:middle;border-radius:4px;transition:all .2s}
    .add-sub-btn:hover{background:#059669}
    .id-cell{display:flex;align-items:center;gap:4px}
    .act-btns{display:flex;gap:6px;flex-wrap:wrap}
    .act-btns button{padding:6px 12px;font-size:13px;border-radius:6px}
    .badge{display:inline-block;padding:4px 10px;font-size:12px;font-weight:600;white-space:nowrap;border-radius:6px}
    .b-klang{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-asset{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3e{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
    .b-3in{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-3em{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3el{background:#fef9c3;color:#713f12;border:1px solid #fde047}
    .b-hd{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .b-ep{background:#fce7f3;color:#9d174d;border:1px solid #f9a8d4}
    .b-3p{background:#f0fdf4;color:#14532d;border:1px solid #86efac}
    .b-all{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
    .badge-wrap{display:flex;flex-direction:column;gap:4px}
    #paging{display:flex;justify-content:center;align-items:center;gap:12px;padding:16px;margin-top:8px}
    .pg-btn{padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Sarabun',sans-serif;background:#fff;border:1px solid #d1d5db;border-radius:8px;color:#374151;transition:all .2s}
    .pg-btn:hover{background:#f9fafb;border-color:#9ca3af;color:#111827}
    .pg-info{font-weight:600;font-size:14px;color:#374151;padding:10px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px}
    ul.ac{list-style:none;margin:4px 0 0;padding:0;background:#fff;border:1px solid #e5e7eb;max-height:180px;overflow-y:auto;position:fixed;z-index:3000;box-shadow:0 10px 25px rgba(0,0,0,.1);min-width:180px;border-radius:8px}
    ul.ac li{padding:10px 14px;cursor:pointer;font-size:13px;color:#374151;transition:background .2s}
    ul.ac li:hover{background:#EEF2FF;color:#5B65F3}
    .tx-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:5000;backdrop-filter:blur(4px);padding:12px}
    .tx-ov.on{display:flex}
    .tx-modal{background:#fff;border:none;width:95vw;max-width:1400px;height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.15);border-radius:12px;overflow:hidden}
    .tx-head{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#111827}
    .tx-head h3{font-size:16px;font-weight:700;margin:0}
    .tx-badge{background:#EEF2FF;color:#5B65F3;padding:4px 12px;font-size:13px;font-weight:700;border-radius:6px;border:1px solid #C7D2FE}
    .tx-xbtn{background:#e5e7eb;color:#374151;border:none;width:32px;height:32px;cursor:pointer;font-size:16px;font-weight:700;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:all .2s}
    .tx-xbtn:hover{background:#ef4444;color:#fff}
    .tx-body{flex:1;overflow:auto;background:#fff}
    .tx-foot{padding:12px 24px;border-top:1px solid #e5e7eb;text-align:right;color:#6b7280;font-size:13px;background:#f9fafb}
    .tx-spin{display:flex;justify-content:center;align-items:center;padding:50px}
    .tx-spin-inner{width:36px;height:36px;border:4px solid rgba(91,101,243,.1);border-top:4px solid #5B65F3;border-radius:50%;animation:sp .8s linear infinite}
    @keyframes sp{to{transform:rotate(360deg)}}
    .tx-tbl{width:100%;border-collapse:collapse;font-size:13px}
    .tx-tbl thead{background:#f9fafb;position:sticky;top:0;z-index:5}
    .tx-tbl th{color:#374151;padding:12px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;border:1px solid #e5e7eb;border-top:none}
    .tx-tbl th:first-child{border-left:none}
    .tx-tbl th:last-child{border-right:none}
    .tx-tbl td{padding:10px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:13px;vertical-align:middle}
    .tx-tbl td:first-child{border-left:none}
    .tx-tbl td:last-child{border-right:none}
    .tx-tbl tbody tr:hover{background:#f9fafb}
    .tx-type{display:block;width:100%;height:100%;text-align:center;padding:12px 8px;font-weight:600;color:#fff;font-size:12px}
    .t-in{background:#10b981}.t-ret{background:#06b6d4}.t-sell{background:#ef4444}.t-bor{background:#f59e0b}.t-wit{background:#f97316}
    .tx-empty{text-align:center;padding:50px;color:#9ca3af;font-size:16px}
    .finput{padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;background:#fff;font-family:'Sarabun',sans-serif;width:100%;transition:all .2s}
    .finput:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .toast{position:fixed;bottom:24px;right:24px;padding:12px 24px;font-size:14px;font-weight:600;z-index:9999;color:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);opacity:0;transition:opacity .3s}
    @media(max-width:768px){.abar{flex-direction:column} .abar input,.abar select{min-width:100%!important;max-width:100%!important} #content{padding:80px 8px 16px}}
    .btn-home{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 20px;
      font-family:'Sarabun',sans-serif;
      font-size:14px;
      font-weight:600;
      color:#fff;
      text-decoration:none;
      cursor:pointer;
      white-space:nowrap;
      background:#5B65F3;
      border-radius:8px;
      box-shadow:0 1px 3px rgba(91,101,243,.3);
      transition:all .2s;
    }
    .btn-home:hover{
      background:#4F46E5;
      box-shadow:0 4px 6px rgba(91,101,243,.2);
      transform:translateY(-1px);
    }
    .btn-home:active{
      transform:translateY(0);
    }
    /* สถานะ auto-refresh มุมขวาบน */
    .live-badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#059669;background:#d1fae5;border:1px solid #6ee7b7;padding:4px 10px;border-radius:999px}
    .live-dot{width:8px;height:8px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
  </style>
</head>
<body>

<!-- Overlay with Block Loading -->
<div class="ov" id="ov">
  <div class="progress-container">
    <p class="ov-text" id="ovText">กำลังโหลดข้อมูล...</p>
    <div class="progress-track">
      <div class="progress-fill" id="progressBar"></div>
    </div>
    <p class="ov-percent" id="ovPercent">0%</p>
  </div>
</div>

<div class="toast" id="toast"></div>

<div class="tx-ov" id="txOv">
  <div class="tx-modal">
    <div class="tx-head"><div style="display:flex;align-items:center;gap:12px"><h3>ประวัติ Transaction</h3><span class="tx-badge" id="txId">-</span><span id="txName" style="font-size:14px;color:#6b7280"></span></div><button class="tx-xbtn" onclick="closeTx()">&#10005;</button></div>
    <div class="tx-body" id="txBody"><div class="tx-spin"><div class="tx-spin-inner"></div></div></div>
    <div class="tx-foot" id="txFoot">กำลังโหลด...</div>
  </div>
</div>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.item') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.vehicles') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รถบริษัท</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name"> ผู้ใช้: {{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
    <a href="http://server_update:8000/solist" class="btn-home">หน้าหลัก</a>
  </div>
</div>

<div id="content">
  <div class="card">
    <h2>ค้นหาสินค้า</h2>
    <div class="abar">
      @if(in_array($authRole, ['admin','user']))
        <button class="btn btn-add" onclick="addRow()">+ เพิ่มสินค้าใหม่</button>
      @endif
      <input type="text" id="sName" placeholder="ชื่อสินค้า..." style="flex:2;min-width:200px" oninput="debounceFilter()">
      <input type="text" id="sBrand" placeholder="ยี่ห้อ..." oninput="debounceFilter()">
      <input type="text" id="sLoc" placeholder="สถานที่เก็บ" oninput="debounceFilter()">
      <select id="sPriv" onchange="applyFilter()"><option value="">ทุกบริษัท</option><option value="3E">3E</option><option value="3IN">3IN</option><option value="3EM">3EM</option><option value="3EL">3EL</option><option value="HD">HD</option><option value="EP">EP</option><option value="3P">3P</option><option value="AE&T">AE&T</option></select>
      <select id="sType" onchange="applyFilter()"><option value="">ทุกประเภท</option><option value="คลัง">คลัง</option><option value="ทรัพย์สินบริษัท">ทรัพย์สินบริษัท</option></select>
      <button class="btn btn-clr" onclick="clearFilter()">ล้างตัวกรอง</button>
    </div>
  </div>
  <div class="tbl-wrap">
    <table id="dt">
      <colgroup><col style="width:170px"><col style="width:340px"><col style="width:90px"><col style="width:120px"><col style="width:150px"><col style="width:140px">@if($authRole!=='viewer')<col style="width:160px">@endif</colgroup>
      <thead><tr><th>ID Item</th><th>ชื่อสินค้า</th><th>จำนวน</th><th>ยี่ห้อ</th><th>สถานที่เก็บ</th><th>ประเภท / บริษัท</th>@if($authRole!=='viewer')<th>จัดการ</th>@endif</tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
const CAN_ADD=(ROLE==='admin'||ROLE==='user'),CAN_EDIT=(ROLE==='admin');
const COLS=ROLE==='viewer'?6:7;
const COMPANIES=[{code:'3E',label:'Triple E Trading'},{code:'3IN',label:'Triple E Innovation'},{code:'3EM',label:'Triple E Empire Group'},{code:'3EL',label:'Triple E Lighting'},{code:'HD',label:'Hikari Denki'},{code:'EP',label:'Eita & Paul'},{code:'3P',label:'Triple P Factory & Eng'},{code:'AE&T',label:'AE&T International'}];
const PM={'3E':'b-3e','3IN':'b-3in','3EM':'b-3em','3EL':'b-3el','HD':'b-hd','EP':'b-ep','3P':'b-3p'};
const TM={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};
const API={
  async get(u){
    // เติม timestamp กัน browser/proxy cache ผลลัพธ์เดิม + no-store บังคับไม่เก็บ cache
    const sep = u.includes('?') ? '&' : '?';
    const noCacheUrl = u + sep + '_t=' + Date.now();
    return (await fetch(noCacheUrl, {
      cache: 'no-store',
      headers: {'Cache-Control':'no-cache, no-store, must-revalidate','Pragma':'no-cache'}
    })).json();
  },
  async post(u,d){
    return (await fetch(u,{
      method:'POST',
      cache:'no-store',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'},
      body:JSON.stringify(d)
    })).json();
  },
  async put(u,d){
    return (await fetch(u,{
      method:'PUT',
      cache:'no-store',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'},
      body:JSON.stringify(d)
    })).json();
  },
  async del(u){
    return (await fetch(u,{
      method:'DELETE',
      cache:'no-store',
      headers:{'X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'}
    })).json();
  }
};

let uBrands=[],uLocs=[],products=[],subs={},filtered=[],pg=1,totalItems=0;
const PG=50;
let exMap={},openSubKey=null;
let filterTimeout=null;
let currentFilters={name:'',brand:'',location:'',priv:'',type:''};
let progressInterval = null;
let isEditingRow = false; // true ขณะกำลังเพิ่ม/แก้ไขแถว เพื่อกัน auto-refresh ทับข้อมูลที่กำลังพิมพ์

function showOv(t){
  document.getElementById('ovText').textContent = t || 'กำลังโหลดข้อมูล...';
  document.getElementById('ov').classList.add('on');
  startProgressSimulation();
}

function hideOv(){
  clearInterval(progressInterval);
  document.getElementById('progressBar').style.width = '100%';
  document.getElementById('ovPercent').textContent = '100%';
  
  setTimeout(() => {
    document.getElementById('ov').classList.remove('on');
    setTimeout(() => {
      document.getElementById('progressBar').style.width = '0%';
      document.getElementById('ovPercent').textContent = '0%';
    }, 300);
  }, 200);
}

function startProgressSimulation(){
  let progress = 0;
  const bar = document.getElementById('progressBar');
  const text = document.getElementById('ovPercent');
  
  clearInterval(progressInterval);
  
  progressInterval = setInterval(() => {
    if (progress < 30) {
      progress += Math.random() * 15;
    } else if (progress < 70) {
      progress += Math.random() * 8;
    } else if (progress < 90) {
      progress += Math.random() * 3;
    } else {
      progress = 90; 
    }
    
    if (progress > 90) progress = 90;
    
    bar.style.width = Math.floor(progress) + '%';
    text.textContent = Math.floor(progress) + '%';
  }, 150);
}

function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){let t=document.getElementById('toast');t.textContent=m;t.style.background=e?'#ef4444':'#10b981';t.style.opacity='1';clearTimeout(t._t);t._t=setTimeout(()=>t.style.opacity='0',2500)}
function ej(s){return(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;').replace(/\n/g,'\\n')}
function eh(s){return(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function ci(s){return(s||'').replace(/[^a-zA-Z0-9]/g,'_')}
function tBadge(t){if(!t)return'<span class="badge b-klang">-</span>';return t==='คลัง'?'<span class="badge b-klang">คลัง</span>':t==='ทรัพย์สินบริษัท'?'<span class="badge b-asset">ทรัพย์สิน</span>':`<span class="badge b-klang">${t}</span>`}
function pBadge(p){if(!p)return'<span class="badge b-all">-</span>';const c=PM[p.trim()]||'b-all';const f=COMPANIES.find(x=>x.code===p.trim());return`<span class="badge ${c}">${f?f.code+' · '+f.label:p}</span>`}
function bldType(s,id){return`<select id="${id}" class="finput"><option value="คลัง" ${s==='คลัง'?'selected':''}>คลัง</option><option value="ทรัพย์สินบริษัท" ${s==='ทรัพย์สินบริษัท'?'selected':''}>ทรัพย์สินบริษัท</option></select>`}
function bldPriv(s,id){return`<select id="${id}" class="finput"><option value="" ${!s?'selected':''} disabled>-- บริษัท --</option>${COMPANIES.map(c=>`<option value="${c.code}" ${s===c.code?'selected':''}>${c.code} · ${c.label}</option>`).join('')}</select>`}

function debounceFilter(){
  clearTimeout(filterTimeout);
  filterTimeout=setTimeout(()=>{
    applyFilter();
  },400);
}

async function loadPage(page=1,showLoader=true){
  if(showLoader) showOv();
  try{
    const params=new URLSearchParams({
      page:page,
      limit:PG,
      name:currentFilters.name||'',
      brand:currentFilters.brand||'',
      location:currentFilters.location||'',
      priv:currentFilters.priv||'',
      type:currentFilters.type||''
    });
    
    const res=await API.get('/api/items/paged?'+params.toString());
    
    products=res.data||[];
    subs=res.subs||{};
    totalItems=res.total||0;
    pg=res.page||1;
    
    render();
  }catch(e){
    console.error('Error loading data:',e);
    toast('โหลดข้อมูลล้มเหลว: '+e.message,true);
  }
  if(showLoader) hideOv();
}

function applyFilter(){
  currentFilters={
    name:(document.getElementById('sName').value||'').trim(),
    brand:(document.getElementById('sBrand').value||'').trim(),
    location:(document.getElementById('sLoc').value||'').trim(),
    priv:document.getElementById('sPriv').value,
    type:document.getElementById('sType').value
  };
  pg=1;
  loadPage(1, false);
}

function clearFilter(){
  ['sName','sBrand','sLoc','sPriv','sType'].forEach(id=>document.getElementById(id).value='');
  currentFilters={name:'',brand:'',location:'',priv:'',type:''};
  pg=1;
  loadPage(1,true);
}

function render(){
  const tb=document.getElementById('tb');
  tb.innerHTML='';
  
  if(!products.length){
    tb.innerHTML=`<tr><td colspan="${COLS}" style="text-align:center;padding:40px;color:#9ca3af">ไม่พบข้อมูลสินค้า</td></tr>`;
    renderPg();
    return;
  }
  
  products.forEach((item,i)=>{
    const key=item._pid||item.iditem;
    const hasSub=!!(subs[key]?.length);
    const isExp=!!exMap[key];
    const isOpen=openSubKey===key;
    
    const tr=document.createElement('tr');
    tr.dataset.pid=key;
    
    const expBtn=hasSub?`<button class="expand-btn${isExp?' open':''}" onclick="toggleExp('${ej(key)}',this)">&#9658;</button>`:`<span style="display:inline-block;width:24px;margin-right:6px"></span>`;
    const addSub=CAN_ADD?`<button class="add-sub-btn" onclick="toggleSubForm('${ej(key)}')" title="เพิ่มรายการย่อย">+</button>`:'';
    
    let h=`<td><div class="id-cell">${expBtn}${addSub}<strong>${item.iditem}</strong></div></td><td><span class="name-link" onclick="openTx('${ej(item.iditem)}','${ej(item.name)}')">${item.name}</span></td><td><strong>${parseInt(item.quantity)||0}</strong></td><td>${item.brand||'-'}</td><td>${item.location||'-'}</td><td><div class="badge-wrap">${tBadge(item.typeitem)}${pBadge(item.privilege)}</div></td>`;
    
    if(ROLE!=='viewer'){
      let btns='';
      if(CAN_EDIT) btns=`<button class="btn btn-edit" onclick="editRow(${i})">แก้ไข</button><button class="btn btn-del" onclick="delRow(${i})">ลบ</button>`;
      h+=`<td><div class="act-btns">${btns}</div></td>`;
    }
    
    tr.innerHTML=h;
    tb.appendChild(tr);
    
    if(CAN_ADD){
      const ftr=document.createElement('tr');
      ftr.className='sub-form-row'+(isOpen?'':' hide');
      ftr.dataset.sf=key;
      ftr.innerHTML=`<td colspan="${COLS}"><div style="display:flex;gap:12px;align-items:center;padding:4px 0;flex-wrap:wrap"><span style="font-size:12px;color:#6b7280;white-space:nowrap">ID: <strong>${key}.<span style="color:#5B65F3">auto</span></strong></span><input type="text" placeholder="ชื่อรุ่นย่อย" id="sn_${ci(key)}" class="finput" style="max-width:260px;flex:1" onkeydown="if(event.key==='Enter')saveSub('${ej(key)}')"><input type="text" id="sb_${ci(key)}" value="${eh(item.brand||'')}" placeholder="ยี่ห้อ" class="finput" style="max-width:150px;flex:1" oninput="showAc(this,'brand')"><input type="text" id="sl_${ci(key)}" value="${eh(item.location||'')}" placeholder="สถานที่เก็บ" class="finput" style="max-width:240px;flex:1" oninput="showAc(this,'location')"><button class="btn btn-save" style="padding:8px 16px" onclick="saveSub('${ej(key)}')">บันทึก</button><button class="btn btn-can" style="padding:8px 16px" onclick="cancelSub('${ej(key)}')">ยกเลิก</button></div></td>`;
      tb.appendChild(ftr);
    }
    
    if(hasSub){
      (subs[key]||[]).forEach((sub,si)=>{
        const str=document.createElement('tr');
        str.className='sub-row'+(isExp?'':' hide');
        str.dataset.so=key;
        
        let sh=`<td style="padding-left:48px;color:#6b7280;font-size:13px">${sub.iditem}</td><td><span class="name-link" onclick="openTx('${ej(sub.iditem)}','${ej(sub.name)}')">${sub.name}</span></td><td><strong>${parseInt(sub.quantity)||0}</strong></td><td>${sub.brand||'-'}</td><td>${sub.location||'-'}</td><td><div class="badge-wrap">${tBadge(sub.typeitem||item.typeitem)}${pBadge(sub.privilege||item.privilege)}</div></td>`;
        
        if(ROLE!=='viewer'){
          let sbtns='';
          if(CAN_EDIT) sbtns=`<button class="btn btn-edit" onclick="editSub('${ej(key)}',${si})">แก้ไข</button><button class="btn btn-del" onclick="delSub('${ej(key)}',${si})">ลบ</button>`;
          sh+=`<td><div class="act-btns">${sbtns}</div></td>`;
        }
        
        str.innerHTML=sh;
        tb.appendChild(str);
      });
    }
  });
  
  renderPg();
}

function renderPg(){
  const tot=Math.max(1,Math.ceil(totalItems/PG));
  const el=document.getElementById('paging');
  el.innerHTML='';
  
  if(tot<=1) return;
  
  if(pg>1){
    const b=document.createElement('button');
    b.className='pg-btn';
    b.textContent='← ก่อนหน้า';
    b.onclick=()=>{loadPage(pg-1,true);scrollTo(0,0)};
    el.appendChild(b);
  }
  
  const s=document.createElement('span');
  s.className='pg-info';
  s.textContent=`หน้า ${pg} / ${tot} (${totalItems} รายการ)`;
  el.appendChild(s);
  
  if(pg<tot){
    const b=document.createElement('button');
    b.className='pg-btn';
    b.textContent='ถัดไป →';
    b.onclick=()=>{loadPage(pg+1,true);scrollTo(0,0)};
    el.appendChild(b);
  }
}

function toggleExp(pid,btn){
  exMap[pid]=!exMap[pid];
  btn.classList.toggle('open',exMap[pid]);
  document.querySelectorAll(`tr[data-so="${pid}"]`).forEach(r=>r.classList.toggle('hide',!exMap[pid]));
}

function toggleSubForm(pid){
  if(!CAN_ADD) return;
  if(openSubKey===pid){cancelSub(pid);return;}
  if(openSubKey) cancelSub(openSubKey);
  openSubKey=pid;
  isEditingRow=true;
  document.querySelector(`tr[data-sf="${pid}"]`)?.classList.remove('hide');
}


function showAc(inp,type){
  document.querySelectorAll('ul.ac').forEach(u=>u.remove());
  const list=type==='brand'?uBrands:uLocs,val=inp.value.toLowerCase();
  if(!val) return;
  const fil=list.filter(l=>l.toLowerCase().includes(val)).slice(0,10);
  if(!fil.length) return;
  const ul=document.createElement('ul');
  ul.className='ac';
  const rc=inp.getBoundingClientRect();
  ul.style.top=(rc.bottom+scrollY)+'px';
  ul.style.left=(rc.left+scrollX)+'px';
  ul.style.width=rc.width+'px';
  document.body.appendChild(ul);
  fil.forEach(v=>{
    const li=document.createElement('li');
    li.textContent=v;
    li.onclick=()=>{inp.value=v;ul.remove()};
    ul.appendChild(li);
  });
}

document.addEventListener('click',e=>{
  if(!e.target.closest('ul.ac')&&!e.target.matches('input'))
    document.querySelectorAll('ul.ac').forEach(u=>u.remove());
});

async function openTx(id,name){
  document.getElementById('txId').textContent=id;
  document.getElementById('txName').textContent=name;
  document.getElementById('txBody').innerHTML='<div class="tx-spin"><div class="tx-spin-inner"></div></div>';
  document.getElementById('txFoot').textContent='กำลังโหลด...';
  document.getElementById('txOv').classList.add('on');
  try{
    const rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));
    rows.sort((a,b)=>{
      const p=ts=>{if(!ts) return 0;const m=ts.match(/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})/);if(m) return new Date(m[3],m[2]-1,m[1],m[4],m[5],m[6]).getTime();return new Date(ts).getTime()||0};
      return p(b.Timestamp)-p(a.Timestamp);
    });
    renderTxModal(rows);
  }catch(e){
    document.getElementById('txBody').innerHTML=`<div class="tx-empty">โหลดล้มเหลว</div>`;
  }
}
function renderTxModal(data){
  const body=document.getElementById('txBody'),foot=document.getElementById('txFoot');
  if(!data.length){
    body.innerHTML='<div class="tx-empty">ไม่พบ Transaction</div>';
    foot.textContent='ไม่มีข้อมูล';
    return;
  }
  foot.textContent=`พบ ${data.length} รายการ`;
  let h='<table class="tx-tbl"><thead><tr><th>วันที่</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ชั้นวาง</th><th>หมายเหตุ</th><th>รูป</th></tr></thead><tbody>';
  data.forEach(r=>{
    const tc=TM[r['ประเภทข้อมูล']||'']||'';
    const pic=r['รูปประกอบ']?`<a href="${r['รูปประกอบ']}" target="_blank" style="color:#5B65F3;font-size:12px;font-weight:600">ดูรูป</a>`:'-';
    h+=`<tr><td style="white-space:nowrap">${r.Timestamp||'-'}</td><td>${r['ชื่อผู้ดำเนินงาน']||'-'}</td><td style="padding:0"><span class="tx-type ${tc}">${r['ประเภทข้อมูล']||'-'}</span></td><td>${r['หมายเลขเอกสาร']||'-'}</td><td>${r['รายการ']||'-'}</td><td>${r['จำนวน']!==''?r['จำนวน']:'-'}</td><td>${ROLE==='viewer'?'-':(r['ราคาต่อหน่วย']!==''?r['ราคาต่อหน่วย']:'-')}</td><td>${r['ชั้นวาง']||'-'}</td><td>${r['หมายเหตุ']||'-'}</td><td>${pic}</td></tr>`;
  });
  h+='</tbody></table>';
  body.innerHTML=h;
}

loadPage(1,true);

let itemsRefreshPending = false;
let itemsRefreshDebounce = null;

function isBusyEditingItems(){
  const txOpen = document.getElementById('txOv').classList.contains('on');
  return txOpen || isEditingRow || openSubKey !== null;
}

function requestItemsSilentRefresh(){
  clearTimeout(itemsRefreshDebounce);
  itemsRefreshDebounce = setTimeout(() => {
    if (isBusyEditingItems()) { itemsRefreshPending = true; return; }
    loadPage(pg, false);
  }, 300);
}

function flushItemsPendingRefresh(){
  if (!itemsRefreshPending || isBusyEditingItems()) return;
  itemsRefreshPending = false;
  loadPage(pg, false);
}

function connectItemsSSE(){
  const url = `${NEST_URL}/items/events?key=${encodeURIComponent(NEST_KEY)}`;
  const es = new EventSource(url);
  es.onmessage = (e) => {
    if (e.data === 'heartbeat') return;
    if (e.data === 'items') requestItemsSilentRefresh();
  };
  es.onerror = () => console.warn('[SSE items] connection issue, browser will auto-retry');
}
connectItemsSSE();
function cancelSub(pid){
  document.querySelector(`tr[data-sf="${pid}"]`)?.classList.add('hide');
  if(openSubKey===pid) openSubKey=null;
  isEditingRow=false;
  flushItemsPendingRefresh();
}

function addRow(){
  if(!CAN_ADD) return;
  if(document.getElementById('nName')){
    alert('มีแถวเพิ่มสินค้าอยู่แล้ว');
    scrollTo(0,0);
    return;
  }
  isEditingRow=true;
  const tb=document.getElementById('tb'),tr=document.createElement('tr');
  tr.style.background='#EEF2FF';
  tr.innerHTML=`<td><em style="color:#6b7280;font-size:12px">auto</em></td><td><input type="text" id="nName" placeholder="ชื่อสินค้า" class="finput"></td><td><input type="number" id="nQty" value="0" readonly class="finput"></td><td><input type="text" id="nBrand" placeholder="ยี่ห้อ" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="nLoc" placeholder="สถานที่เก็บ" class="finput" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:8px">${bldType('คลัง','nType')}${bldPriv('','nPriv')}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveNew()">บันทึก</button><button class="btn btn-can" onclick="isEditingRow=false;loadPage(pg,false);flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
  tb.prepend(tr);
  scrollTo(0,0);
}

async function saveNew(){
  const nm=document.getElementById('nName').value.trim(),pr=document.getElementById('nPriv')?.value||'';
  if(!pr){alert('กรุณาเลือกบริษัท');return;}
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.post('/api/items',{name:nm,typeitem:document.getElementById('nType').value,location:document.getElementById('nLoc').value.trim(),brand:document.getElementById('nBrand').value.trim(),quantity:'0',privilege:pr});
    toast('เพิ่มสินค้าเรียบร้อย');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function saveSub(pid){
  if(!CAN_ADD) return;
  const cid=ci(pid),nm=(document.getElementById('sn_'+cid)?.value||'').trim();
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  const par=products.find(p=>(p._pid||p.iditem)===pid);
  if(!par) return;
  try{
    await API.post('/api/items/sub',{parentId:pid,name:nm,brand:(document.getElementById('sb_'+cid)?.value||'').trim()||par.brand||'',location:(document.getElementById('sl_'+cid)?.value||'').trim()||par.location||'',typeitem:par.typeitem||'คลัง',quantity:'0',privilege:par.privilege||''});
    toast('เพิ่มรายการย่อยเรียบร้อย');
    openSubKey=null;
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function editRow(i){
  if(!CAN_EDIT) return;
  isEditingRow=true;
  const item=products[i],key=item._pid||item.iditem,row=document.querySelector(`tr[data-pid="${key}"]`);
  if(!row) return;
  const uid=ci(item.iditem);
  row.style.background='#EEF2FF';
  row.innerHTML=`<td><strong>${item.iditem}</strong></td><td><input type="text" id="eN_${uid}" value="${eh(item.name)}" class="finput"></td><td><input type="number" id="eQ_${uid}" value="${item.quantity}" class="finput"></td><td><input type="text" id="eB_${uid}" value="${eh(item.brand||'')}" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="eL_${uid}" value="${eh(item.location||'')}" class="finput" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:8px">${bldType(item.typeitem,'eT_'+uid)}${bldPriv(item.privilege||'','eP_'+uid)}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveEdit(${i})">บันทึก</button><button class="btn btn-can" onclick="isEditingRow=false;render();flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
}

async function saveEdit(i){
  const item=products[i],uid=ci(item.iditem),nm=document.getElementById('eN_'+uid).value.trim(),pr=document.getElementById('eP_'+uid)?.value||'';
  if(!pr){alert('กรุณาเลือกบริษัท');return;}
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.put('/api/items/'+encodeURIComponent(item.iditem),{name:nm,quantity:document.getElementById('eQ_'+uid).value,typeitem:document.getElementById('eT_'+uid).value,location:document.getElementById('eL_'+uid).value.trim(),brand:document.getElementById('eB_'+uid).value.trim(),privilege:pr});
    toast('บันทึกสำเร็จ');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delRow(i){
  if(!CAN_EDIT) return;
  const item=products[i];
  let cnt=0;
  try{cnt=(await API.get('/api/items/'+encodeURIComponent(item.iditem)+'/tx-count')).count||0}catch(e){}
  if(!confirm(cnt>0?`⚠️ ${item.iditem}\nมี Transaction ${cnt} รายการ\nดำเนินการต่อ?`:`ต้องการลบ ${item.iditem}?`)) return;
  try{
    await API.del('/api/items/'+encodeURIComponent(item.iditem));
    toast('ลบเรียบร้อย');
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function editSub(pid,si){
  if(!CAN_EDIT) return;
  isEditingRow=true;
  const sub=(subs[pid]||[])[si];
  if(!sub) return;
  const subTr=[...document.querySelectorAll(`tr[data-so="${pid}"]`)].find(r=>r.querySelector('td')?.textContent.trim()===sub.iditem);
  if(!subTr) return;
  const uid=ci(sub.iditem);
  subTr.style.background='#EEF2FF';
  subTr.innerHTML=`<td style="padding-left:48px"><strong>${sub.iditem}</strong></td><td><input type="text" id="seN_${uid}" value="${eh(sub.name)}" class="finput"></td><td><input type="number" id="seQ_${uid}" value="${sub.quantity}" class="finput"></td><td><input type="text" id="seB_${uid}" value="${eh(sub.brand||'')}" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="seL_${uid}" value="${eh(sub.location||'')}" class="finput" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:8px">${bldType(sub.typeitem,'seT_'+uid)}${bldPriv(sub.privilege||'','seP_'+uid)}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveSubEdit('${ej(pid)}',${si})">บันทึก</button><button class="btn btn-can" onclick="isEditingRow=false;render();flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
}

async function saveSubEdit(pid,si){
  const sub=(subs[pid]||[])[si];
  if(!sub) return;
  const uid=ci(sub.iditem),nm=document.getElementById('seN_'+uid).value.trim();
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.put('/api/items/'+encodeURIComponent(sub.iditem),{name:nm,quantity:document.getElementById('seQ_'+uid).value,brand:document.getElementById('seB_'+uid).value.trim(),location:document.getElementById('seL_'+uid).value.trim(),typeitem:document.getElementById('seT_'+uid).value,privilege:document.getElementById('seP_'+uid)?.value||''});
    toast('บันทึกสำเร็จ');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delSub(pid,si){
  if(!CAN_EDIT) return;
  const sub=(subs[pid]||[])[si];
  if(!sub||!confirm(`ลบ ${sub.iditem}?`)) return;
  try{
    await API.del('/api/items/'+encodeURIComponent(sub.iditem));
    toast('ลบเรียบร้อย');
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function closeTx(){
  document.getElementById('txOv').classList.remove('on');
  flushItemsPendingRefresh();
}
</script>
</body>
</html>
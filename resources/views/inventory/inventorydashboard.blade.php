<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ค้นหาสินค้า - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#ece9d8}
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.8);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s}
    .ov.on{opacity:1;visibility:visible}
    .sp{width:48px;height:48px;border:5px solid rgba(255,255,255,.3);border-top:5px solid #FCA50D;border-radius:50%;animation:sp 1s linear infinite;margin:0 auto 12px}
    @keyframes sp{to{transform:rotate(360deg)}}
    .ov p{color:#fff;font-size:16px}
    .topbar{height:52px;background:#444;display:flex;align-items:center;gap:10px;padding:0 12px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:2px solid #1a3f7a;box-shadow:0 2px 8px rgba(0,0,0,.5)}
    .topbar-logo{height:34px;border:1px solid rgba(255,255,255,.3)}
    .topbar-title{font-size:17px;font-weight:700;color:#fff;text-shadow:1px 1px 2px rgba(0,0,0,.5);flex:1}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .topbar-name{font-size:13px;color:rgba(255,255,255,.8)}
    .topbar-badge{font-size:11px;padding:2px 8px;font-weight:700;color:#000;background:#FCA50D}
    .hamburger{background:none;border:none;cursor:pointer;padding:5px;display:flex;flex-direction:column;gap:4px;flex-shrink:0}
    .hamburger span{display:block;width:20px;height:2px;background:#fff}
    .hamburger:hover span{background:#ffe8a0}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-250px;width:230px;height:100vh;z-index:1600;transition:left .25s ease;display:flex;flex-direction:column;background:linear-gradient(180deg,#dce9fa,#c5d9f5);border-right:2px solid #7a9fc8;box-shadow:3px 0 12px rgba(0,0,0,.3)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:8px;padding:10px 12px;background:linear-gradient(180deg,#4a7db5,#2358a4);border-bottom:2px solid #1a3f7a;min-height:52px}
    .sb-head img{height:30px}
    .sb-head span{font-size:16px;font-weight:700;color:#fff;flex:1}
    .sb-close{background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;font-size:18px;font-weight:bold;padding:0 4px}
    .sb-nav{flex:1;overflow-y:auto;padding:6px 0}
    .sb-sec{padding:8px 12px 3px;font-size:11px;font-weight:700;color:#1e4d96;letter-spacing:.8px;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:10px;padding:9px 14px;color:#1a1a6e;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none}
    .sb-item:hover{background:rgba(42,95,168,.15);border-left-color:#2a5fa8}
    .sb-item.cur{background:rgba(42,95,168,.2);border-left-color:#FCA50D;font-weight:700}
    #content{padding:62px 16px 16px}
    .card{margin-bottom:12px;padding:12px 0}
    .card h2{font-size:20px;font-weight:700;color:#333;margin-bottom:10px}
    .abar{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
    .abar input,.abar select{padding:5px 8px;border:2px inset #aaa;font-size:14px;font-family:'Sarabun',sans-serif;background:#fff;min-width:100px;flex:1}
    .abar input:focus,.abar select:focus{outline:none;border-color:#2358a4}
    .btn{padding:6px 14px;font-size:14px;font-weight:600;font-family:'Sarabun',sans-serif;cursor:pointer;white-space:nowrap;border:none}
    .btn-add{background:linear-gradient(180deg,#00ee66,#00cc55);color:#000;border:1px solid #009933!important}
    .btn-add:hover{background:linear-gradient(180deg,#00ff77,#00dd66)}
    .btn-clr{background:linear-gradient(180deg,#f0f0f0,#d0d0d0);color:#555;border:1px solid #999!important}
    .btn-edit{background:linear-gradient(180deg,#6090f0,#2749F5);color:#fff;border:1px solid #1a35c7!important}
    .btn-save{background:linear-gradient(180deg,#44dd77,#00D162);color:#fff;border:1px solid #009940!important}
    .btn-del{background:linear-gradient(180deg,#ff4444,#FF1A1A);color:#fff;border:1px solid #cc0000!important}
    .btn-can{background:linear-gradient(180deg,#f0f0f0,#d8d8d8);color:#555;border:1px solid #999!important}
    .tbl-wrap{background:#fff;overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:13px;table-layout:fixed}
    thead{background:linear-gradient(180deg,#555,#333)}
    th{color:#fff;padding:10px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#444;z-index:5;border-right:1px solid #555}
    th:last-child{border-right:none}
    td{padding:8px 10px;border-bottom:1px solid #eee;color:#333;font-size:13px;vertical-align:middle;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    td.wrap{white-space:normal;overflow:visible}
    tbody tr:hover{background:#f0f5ff}
    tr.sub-row{background:#f8f9ff}
    tr.sub-row td{padding:7px 10px;font-size:12px;border-bottom:1px solid #eaeeff}
    tr.sub-row:hover{background:#eef0ff}
    tr.sub-row.hide{display:none}
    tr.sub-form-row td{background:#fffde7;border-bottom:1px solid #ffe082;overflow:visible;white-space:nowrap}
    tr.sub-form-row.hide{display:none}
    .name-link{color:#1d4ed8;cursor:pointer;text-decoration:underline dotted;font-weight:500}
    .name-link:hover{color:#FCA50D}
    .expand-btn{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;background:#555;color:#fff;border:none;cursor:pointer;font-size:12px;font-weight:700;margin-right:4px;vertical-align:middle}
    .expand-btn:hover{background:#FCA50D;color:#000}
    .expand-btn.open{background:#FCA50D;color:#000;transform:rotate(90deg)}
    .add-sub-btn{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;background:#22c55e;color:#fff;border:none;cursor:pointer;font-size:14px;font-weight:700;vertical-align:middle}
    .add-sub-btn:hover{background:#16a34a}
    .id-cell{display:flex;align-items:center;gap:3px}
    .act-btns{display:flex;gap:4px;flex-wrap:wrap}
    .act-btns button{padding:5px 10px;font-size:12px}
    .badge{display:inline-block;padding:2px 7px;font-size:11px;font-weight:600;white-space:nowrap}
    .b-klang{background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc}
    .b-asset{background:#fdf4ff;color:#7e22ce;border:1px solid #d8b4fe}
    .b-3e{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
    .b-3in{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-3em{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3el{background:#fef9c3;color:#713f12;border:1px solid #fde047}
    .b-hd{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .b-ep{background:#fce7f3;color:#9d174d;border:1px solid #f9a8d4}
    .b-3p{background:#f0fdf4;color:#14532d;border:1px solid #86efac}
    .b-all{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
    .badge-wrap{display:flex;flex-direction:column;gap:2px}
    #paging{display:flex;justify-content:center;align-items:center;gap:12px;padding:14px;margin-top:6px}
    .pg-btn{padding:6px 20px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Sarabun',sans-serif;background:linear-gradient(180deg,#f0f0f0,#d0d0d0);border:1px solid #999}
    .pg-btn:hover{background:linear-gradient(180deg,#fff,#e0e0e0)}
    .pg-info{font-weight:600;font-size:14px;color:#333;padding:6px 16px;background:#f5f5f5;border:1px solid #ccc}
    ul.ac{list-style:none;margin:4px 0 0;padding:0;background:#fff;border:2px solid #FCA50D;max-height:180px;overflow-y:auto;position:fixed;z-index:3000;box-shadow:0 4px 12px rgba(0,0,0,.2);min-width:180px}
    ul.ac li{padding:8px 12px;cursor:pointer;font-size:13px}
    ul.ac li:hover{background:#FCA50D;color:#000}
    .tx-ov{position:fixed;inset:0;background:rgba(0,0,0,.75);display:none;justify-content:center;align-items:center;z-index:5000;backdrop-filter:blur(3px);padding:12px}
    .tx-ov.on{display:flex}
    .tx-modal{background:#fff;border:2px solid #555;width:99vw;max-width:1800px;height:95vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.5)}
    .tx-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(180deg,#555,#333);border-bottom:2px solid #222;color:#fff}
    .tx-head h3{font-size:16px;font-weight:700}
    .tx-badge{background:#FCA50D;color:#000;padding:3px 12px;font-size:13px;font-weight:700}
    .tx-xbtn{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);width:32px;height:32px;cursor:pointer;font-size:16px;font-weight:700}
    .tx-xbtn:hover{background:#ef4444}
    .tx-body{flex:1;overflow:auto}
    .tx-foot{padding:12px 20px;border-top:1px solid #eee;text-align:right;color:#888;font-size:13px}
    .tx-spin{display:flex;justify-content:center;align-items:center;padding:50px}
    .tx-spin-inner{width:36px;height:36px;border:4px solid rgba(0,0,0,.1);border-top:4px solid #FCA50D;border-radius:50%;animation:sp .8s linear infinite}
    .tx-tbl{width:100%;border-collapse:collapse;font-size:12px}
    .tx-tbl thead{background:#444;position:sticky;top:0;z-index:5}
    .tx-tbl th{color:#fff;padding:10px 8px;text-align:left;font-weight:600;font-size:12px;white-space:nowrap}
    .tx-tbl td{padding:8px;border-bottom:1px solid #eee;color:#333;font-size:12px;vertical-align:middle;white-space:normal;word-break:break-word}
    .tx-tbl tbody tr:hover{background:#f9f9f9}
    .tx-type{font-weight:600;color:#fff;text-align:center;padding:3px 7px;font-size:11px;white-space:nowrap}
    .t-in{background:#00D162}.t-ret{background:#19CBFC;color:#222}.t-sell{background:#FF1A1A}.t-bor{background:#F8FF2E;color:#222}.t-wit{background:#FF8538}
    .tx-empty{text-align:center;padding:50px;color:#aaa;font-size:16px}
    input,select{padding:7px 10px;border:2px inset #aaa;font-family:'Sarabun',sans-serif;font-size:13px;width:100%}
    input:focus,select:focus{outline:none;border-color:#2358a4}
    .toast{position:fixed;bottom:20px;right:20px;padding:10px 18px;font-size:14px;font-weight:600;z-index:9999;color:#fff;opacity:0;transition:opacity .3s}
    @media(max-width:768px){.abar{flex-direction:column} .abar input,.abar select{min-width:100%!important;max-width:100%!important}}
  </style>
</head>
<body>
<div class="ov" id="ov"><div><div class="sp"></div><p>กำลังโหลด...</p></div></div>
<div class="toast" id="toast"></div>

<div class="tx-ov" id="txOv">
  <div class="tx-modal">
    <div class="tx-head"><div style="display:flex;align-items:center;gap:10px"><h3>ประวัติ Transaction</h3><span class="tx-badge" id="txId">-</span><span id="txName" style="font-size:14px;opacity:.85"></span></div><button class="tx-xbtn" onclick="closeTx()">&#10005;</button></div>
    <div class="tx-body" id="txBody"><div class="tx-spin"><div class="tx-spin-inner"></div></div></div>
    <div class="tx-foot" id="txFoot">กำลังโหลด...</div>
  </div>
</div>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
<div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" href="{{ route('inventory.transaction', ['create_by' => $authUser['username'] ?? '']) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item cur" href="{{ route('inventory.item', ['create_by' => $authUser['username'] ?? '']) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
</div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
</div>

<div id="content">
  <div class="card">
    <h2>ค้นหาสินค้า</h2>
    <div class="abar">
      @if(in_array($authRole, ['admin','user']))
        <button class="btn btn-add" onclick="addRow()">+ เพิ่มสินค้าใหม่</button>
      @endif
      <input type="text" id="sName" placeholder="ชื่อสินค้า..." style="min-width:500px;max-width:600px" oninput="doFilter()">
      <input type="text" id="sBrand" placeholder="ยี่ห้อ..." style="min-width:100px;max-width:150px" oninput="doFilter()">
      <input type="text" id="sLoc" placeholder="สถานที่เก็บ" style="min-width:100px;max-width:150px" oninput="doFilter()">
      <select id="sPriv" onchange="doFilter()" style="min-width:130px;max-width:170px"><option value="">ทุกบริษัท</option><option value="3E">3E</option><option value="3IN">3IN</option><option value="3EM">3EM</option><option value="3EL">3EL</option><option value="HD">HD</option><option value="EP">EP</option><option value="3P">3P</option><option value="AE&T">AE&T</option></select>
      <select id="sType" onchange="doFilter()" style="min-width:120px;max-width:160px"><option value="">ทุกประเภท</option><option value="คลัง">คลัง</option><option value="ทรัพย์สินบริษัท">ทรัพย์สินบริษัท</option></select>
      <button class="btn btn-clr" onclick="clearFilter()">ล้างตัวกรอง</button>
    </div>
  </div>
  <div class="tbl-wrap">
    <table id="dt">
      <colgroup><col style="width:170px"><col style="width:340px"><col style="width:90px"><col style="width:120px"><col style="width:150px"><col style="width:80px">@if($authRole!=='viewer')<col style="width:60px">@endif</colgroup>
      <thead><tr><th>ID Item</th><th>ชื่อสินค้า</th><th>จำนวน</th><th>ยี่ห้อ</th><th>สถานที่เก็บ</th><th>ประเภท / บริษัท</th>@if($authRole!=='viewer')<th>จัดการ</th>@endif</tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const CAN_ADD=(ROLE==='admin'||ROLE==='user'),CAN_EDIT=(ROLE==='admin');
const COLS=ROLE==='viewer'?6:7;
const COMPANIES=[{code:'3E',label:'Triple E Trading'},{code:'3IN',label:'Triple E Innovation'},{code:'3EM',label:'Triple E Empire Group'},{code:'3EL',label:'Triple E Lighting'},{code:'HD',label:'Hikari Denki'},{code:'EP',label:'Eita & Paul'},{code:'3P',label:'Triple P Factory & Eng'},{code:'AE&T',label:'AE&T International'}];
const PM={'3E':'b-3e','3IN':'b-3in','3EM':'b-3em','3EL':'b-3el','HD':'b-hd','EP':'b-ep','3P':'b-3p'};
const TM={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};
const API={async get(u){return(await fetch(u)).json()},async post(u,d){return(await fetch(u,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(d)})).json()},async put(u,d){return(await fetch(u,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(d)})).json()},async del(u){return(await fetch(u,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}})).json()}};

let uBrands=[],uLocs=[],products=[],subs={},filtered=[],pg=1;const PG=100;let exMap={},openSubKey=null;
function showOv(){document.getElementById('ov').classList.add('on')}function hideOv(){document.getElementById('ov').classList.remove('on')}
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){let t=document.getElementById('toast');t.textContent=m;t.style.background=e?'#cc0000':'#007722';t.style.opacity='1';clearTimeout(t._t);t._t=setTimeout(()=>t.style.opacity='0',2500)}
function ej(s){return(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;').replace(/\n/g,'\\n')}
function eh(s){return(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function ci(s){return(s||'').replace(/[^a-zA-Z0-9]/g,'_')}
function tBadge(t){if(!t)return'<span class="badge b-klang">-</span>';return t==='คลัง'?'<span class="badge b-klang">คลัง</span>':t==='ทรัพย์สินบริษัท'?'<span class="badge b-asset">ทรัพย์สิน</span>':`<span class="badge b-klang">${t}</span>`}
function pBadge(p){if(!p)return'<span class="badge b-all">-</span>';const c=PM[p.trim()]||'b-all';const f=COMPANIES.find(x=>x.code===p.trim());return`<span class="badge ${c}">${f?f.code+' · '+f.label:p}</span>`}
function bldType(s,id){return`<select id="${id}" style="min-width:120px"><option value="คลัง" ${s==='คลัง'?'selected':''}>คลัง</option><option value="ทรัพย์สินบริษัท" ${s==='ทรัพย์สินบริษัท'?'selected':''}>ทรัพย์สินบริษัท</option></select>`}
function bldPriv(s,id){return`<select id="${id}" style="min-width:150px;margin-top:3px"><option value="" ${!s?'selected':''} disabled>-- บริษัท --</option>${COMPANIES.map(c=>`<option value="${c.code}" ${s===c.code?'selected':''}>${c.code} · ${c.label}</option>`).join('')}</select>`}

async function loadAll(){showOv();try{const r=await API.get('/api/items/pagedata');uBrands=r.brands||[];uLocs=r.locations||[];products=[];subs={};
(r.items||[]).forEach(row=>{const id=row.iditem||'',dot=id.lastIndexOf('.');if(dot>-1){const pid=id.substring(0,dot);if(!subs[pid])subs[pid]=[];subs[pid].push(row)}else products.push(row)});
Object.keys(subs).forEach(pid=>{subs[pid].sort((a,b)=>(parseInt((a.iditem||'').split('.').pop())||0)-(parseInt((b.iditem||'').split('.').pop())||0));if(!products.some(p=>p.iditem===pid)){products.push({...subs[pid][0],_virt:true,_pid:pid});subs[pid]=subs[pid].slice(1)}});
products.sort((a,b)=>{const ai=a._pid||a.iditem,bi=b._pid||b.iditem;if(ai.toUpperCase().startsWith('SKU-')&&!bi.toUpperCase().startsWith('SKU-'))return-1;if(!ai.toUpperCase().startsWith('SKU-')&&bi.toUpperCase().startsWith('SKU-'))return 1;const ap=ai.split('-')[0].toUpperCase(),bp=bi.split('-')[0].toUpperCase();if(ap!==bp)return ap<bp?-1:1;return(parseInt(ai.split('-').pop())||0)-(parseInt(bi.split('-').pop())||0)});
filtered=[...products];pg=1;render()}catch(e){alert('โหลดล้มเหลว: '+e.message)}hideOv()}

function doFilter(){const nv=(document.getElementById('sName').value||'').trim().toLowerCase(),bv=(document.getElementById('sBrand').value||'').trim().toLowerCase(),lv=(document.getElementById('sLoc').value||'').trim().toLowerCase(),pv=document.getElementById('sPriv').value,tv=document.getElementById('sType').value;if(!nv&&!bv&&!lv&&!pv&&!tv){filtered=[...products];pg=1;render();return}
filtered=products.filter(i=>{const k=i._pid||i.iditem,sa=subs[k]||[];return(!nv||(i.name||'').toLowerCase().includes(nv)||sa.some(s=>(s.name||'').toLowerCase().includes(nv)))&&(!bv||(i.brand||'').toLowerCase().includes(bv))&&(!lv||(i.location||'').toLowerCase().includes(lv))&&(!pv||(i.privilege||'')===pv)&&(!tv||(i.typeitem||'')===tv)});pg=1;render()}
function clearFilter(){['sName','sBrand','sLoc','sPriv','sType'].forEach(id=>document.getElementById(id).value='');filtered=[...products];pg=1;render()}

function render(){const tb=document.getElementById('tb');tb.innerHTML='';if(!filtered.length){tb.innerHTML=`<tr><td colspan="${COLS}" style="text-align:center;padding:36px;color:#999">ไม่พบข้อมูลสินค้า</td></tr>`;renderPg();return}
const start=(pg-1)*PG;filtered.slice(start,start+PG).forEach((item,i)=>{const ri=products.indexOf(item),key=item._pid||item.iditem,hasSub=!!(subs[key]?.length),isExp=!!exMap[key],isOpen=openSubKey===key;
const tr=document.createElement('tr');tr.dataset.pid=key;
const expBtn=hasSub?`<button class="expand-btn${isExp?' open':''}" onclick="toggleExp('${ej(key)}',this)">&#9658;</button>`:`<span style="display:inline-block;width:20px;margin-right:4px"></span>`;
const addSub=CAN_ADD?`<button class="add-sub-btn" onclick="toggleSubForm('${ej(key)}')" title="เพิ่มรายการย่อย">+</button>`:'';
let h=`<td><div class="id-cell">${expBtn}${addSub}<strong>${item.iditem}</strong></div></td><td class="wrap"><span class="name-link" onclick="openTx('${ej(item.iditem)}','${ej(item.name)}')">${item.name}</span></td><td><strong>${parseInt(item.quantity)||0}</strong></td><td>${item.brand||'-'}</td><td>${item.location||'-'}</td><td class="wrap"><div class="badge-wrap">${tBadge(item.typeitem)}${pBadge(item.privilege)}</div></td>`;
if(ROLE!=='viewer'){let btns='';if(CAN_EDIT)btns=`<button class="btn btn-edit" onclick="editRow(${ri})">แก้ไข</button><button class="btn btn-del" onclick="delRow(${ri})">ลบ</button>`;h+=`<td><div class="act-btns">${btns}</div></td>`}
tr.innerHTML=h;tb.appendChild(tr);
if(CAN_ADD){const ftr=document.createElement('tr');ftr.className='sub-form-row'+(isOpen?'':' hide');ftr.dataset.sf=key;ftr.innerHTML=`<td colspan="${COLS}"><div style="display:flex;gap:8px;align-items:center;padding:4px 0;flex-wrap:wrap"><span style="font-size:12px;color:#777">ID: <strong>${key}.<span style="color:#FCA50D">auto</span></strong></span><input type="text" placeholder="ชื่อรุ่นย่อย" id="sn_${ci(key)}" style="max-width:260px;flex:1" onkeydown="if(event.key==='Enter')saveSub('${ej(key)}')"><span style="font-size:12px;color:#777">ยี่ห้อ</span><input type="text" id="sb_${ci(key)}" value="${eh(item.brand||'')}" style="max-width:150px;flex:1" oninput="showAc(this,'brand')"><span style="font-size:12px;color:#777">สถานที่เก็บ</span><input type="text" id="sl_${ci(key)}" value="${eh(item.location||'')}" style="max-width:240px;flex:1" oninput="showAc(this,'location')"><button class="btn btn-save" style="padding:5px 12px" onclick="saveSub('${ej(key)}')">บันทึก</button><button class="btn btn-can" style="padding:5px 12px" onclick="cancelSub('${ej(key)}')">ยกเลิก</button></div></td>`;tb.appendChild(ftr)}
if(hasSub)(subs[key]||[]).forEach((sub,si)=>{const str=document.createElement('tr');str.className='sub-row'+(isExp?'':' hide');str.dataset.so=key;
let sh=`<td style="padding-left:42px;color:#555;font-size:12px">${sub.iditem}</td><td class="wrap"><span class="name-link" onclick="openTx('${ej(sub.iditem)}','${ej(sub.name)}')">${sub.name}</span></td><td><strong>${parseInt(sub.quantity)||0}</strong></td><td>${sub.brand||'-'}</td><td>${sub.location||'-'}</td><td class="wrap"><div class="badge-wrap">${tBadge(sub.typeitem||item.typeitem)}${pBadge(sub.privilege||item.privilege)}</div></td>`;
if(ROLE!=='viewer'){let sbtns='';if(CAN_EDIT)sbtns=`<button class="btn btn-edit" onclick="editSub('${ej(key)}',${si})">แก้ไข</button><button class="btn btn-del" onclick="delSub('${ej(key)}',${si})">ลบ</button>`;sh+=`<td><div class="act-btns">${sbtns}</div></td>`}
str.innerHTML=sh;tb.appendChild(str)})});renderPg()}

function renderPg(){const tot=Math.max(1,Math.ceil(filtered.length/PG)),el=document.getElementById('paging');el.innerHTML='';if(tot<=1)return;if(pg>1){const b=document.createElement('button');b.className='pg-btn';b.textContent='← ก่อนหน้า';b.onclick=()=>{pg--;render();scrollTo(0,0)};el.appendChild(b)}const s=document.createElement('span');s.className='pg-info';s.textContent=`หน้า ${pg} / ${tot}`;el.appendChild(s);if(pg<tot){const b=document.createElement('button');b.className='pg-btn';b.textContent='ถัดไป →';b.onclick=()=>{pg++;render();scrollTo(0,0)};el.appendChild(b)}}
function toggleExp(pid,btn){exMap[pid]=!exMap[pid];btn.classList.toggle('open',exMap[pid]);document.querySelectorAll(`tr[data-so="${pid}"]`).forEach(r=>r.classList.toggle('hide',!exMap[pid]))}
function toggleSubForm(pid){if(!CAN_ADD)return;if(openSubKey===pid){cancelSub(pid);return}if(openSubKey)cancelSub(openSubKey);openSubKey=pid;document.querySelector(`tr[data-sf="${pid}"]`)?.classList.remove('hide')}
function cancelSub(pid){document.querySelector(`tr[data-sf="${pid}"]`)?.classList.add('hide');if(openSubKey===pid)openSubKey=null}

function addRow(){if(!CAN_ADD)return;if(document.getElementById('nName')){alert('มีแถวเพิ่มสินค้าอยู่แล้ว');scrollTo(0,0);return}const tb=document.getElementById('tb'),tr=document.createElement('tr');tr.style.background='#fffacd';tr.innerHTML=`<td><em style="color:#888;font-size:12px">auto</em></td><td><input type="text" id="nName" placeholder="ชื่อสินค้า"></td><td><input type="number" id="nQty" value="0" readonly></td><td><input type="text" id="nBrand" placeholder="ยี่ห้อ" oninput="showAc(this,'brand')"></td><td><input type="text" id="nLoc" placeholder="สถานที่เก็บ" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:4px">${bldType('คลัง','nType')}${bldPriv('','nPriv')}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveNew()">บันทึก</button><button class="btn btn-can" onclick="loadAll()">ยกเลิก</button></div></td>`;tb.prepend(tr);scrollTo(0,0)}
async function saveNew(){const nm=document.getElementById('nName').value.trim(),pr=document.getElementById('nPriv')?.value||'';if(!pr){alert('กรุณาเลือกบริษัท');return}if(!nm){alert('กรุณากรอกชื่อ');return}showOv();try{await API.post('/api/items',{name:nm,typeitem:document.getElementById('nType').value,location:document.getElementById('nLoc').value.trim(),brand:document.getElementById('nBrand').value.trim(),quantity:'0',privilege:pr});toast('เพิ่มสินค้าเรียบร้อย');await loadAll()}catch(e){toast(e.message,true);hideOv()}}
async function saveSub(pid){if(!CAN_ADD)return;const cid=ci(pid),nm=(document.getElementById('sn_'+cid)?.value||'').trim();if(!nm){alert('กรุณากรอกชื่อ');return}const par=products.find(p=>(p._pid||p.iditem)===pid);if(!par)return;showOv();try{await API.post('/api/items/sub',{parentId:pid,name:nm,brand:(document.getElementById('sb_'+cid)?.value||'').trim()||par.brand||'',location:(document.getElementById('sl_'+cid)?.value||'').trim()||par.location||'',typeitem:par.typeitem||'คลัง',quantity:'0',privilege:par.privilege||''});toast('เพิ่มรายการย่อยเรียบร้อย');openSubKey=null;await loadAll()}catch(e){toast(e.message,true);hideOv()}}

function editRow(i){if(!CAN_EDIT)return;const item=products[i],key=item._pid||item.iditem,row=document.querySelector(`tr[data-pid="${key}"]`);if(!row)return;const uid=ci(item.iditem);row.style.background='#e0f2fe';row.innerHTML=`<td><strong>${item.iditem}</strong></td><td><input type="text" id="eN_${uid}" value="${eh(item.name)}"></td><td><input type="number" id="eQ_${uid}" value="${item.quantity}"></td><td><input type="text" id="eB_${uid}" value="${eh(item.brand||'')}" oninput="showAc(this,'brand')"></td><td><input type="text" id="eL_${uid}" value="${eh(item.location||'')}" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:4px">${bldType(item.typeitem,'eT_'+uid)}${bldPriv(item.privilege||'','eP_'+uid)}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveEdit(${i})">บันทึก</button><button class="btn btn-can" onclick="render()">ยกเลิก</button></div></td>`}
async function saveEdit(i){const item=products[i],uid=ci(item.iditem),nm=document.getElementById('eN_'+uid).value.trim(),pr=document.getElementById('eP_'+uid)?.value||'';if(!pr){alert('กรุณาเลือกบริษัท');return}if(!nm){alert('กรุณากรอกชื่อ');return}showOv();try{await API.put('/api/items/'+encodeURIComponent(item.iditem),{name:nm,quantity:document.getElementById('eQ_'+uid).value,typeitem:document.getElementById('eT_'+uid).value,location:document.getElementById('eL_'+uid).value.trim(),brand:document.getElementById('eB_'+uid).value.trim(),privilege:pr});toast('บันทึกสำเร็จ');await loadAll()}catch(e){toast(e.message,true);hideOv()}}
async function delRow(i){if(!CAN_EDIT)return;const item=products[i];let cnt=0;try{cnt=(await API.get('/api/items/'+encodeURIComponent(item.iditem)+'/tx-count')).count||0}catch(e){}if(!confirm(cnt>0?`⚠️ ${item.iditem}\nมี Transaction ${cnt} รายการ\nดำเนินการต่อ?`:`ต้องการลบ ${item.iditem}?`))return;showOv();try{await API.del('/api/items/'+encodeURIComponent(item.iditem));toast('ลบเรียบร้อย');await loadAll()}catch(e){toast(e.message,true);hideOv()}}
function editSub(pid,si){if(!CAN_EDIT)return;const sub=(subs[pid]||[])[si];if(!sub)return;const subTr=[...document.querySelectorAll(`tr[data-so="${pid}"]`)].find(r=>r.querySelector('td')?.textContent.trim()===sub.iditem);if(!subTr)return;const uid=ci(sub.iditem);subTr.style.background='#e0f2fe';subTr.innerHTML=`<td style="padding-left:42px"><strong>${sub.iditem}</strong></td><td><input type="text" id="seN_${uid}" value="${eh(sub.name)}"></td><td><input type="number" id="seQ_${uid}" value="${sub.quantity}"></td><td><input type="text" id="seB_${uid}" value="${eh(sub.brand||'')}" oninput="showAc(this,'brand')"></td><td><input type="text" id="seL_${uid}" value="${eh(sub.location||'')}" oninput="showAc(this,'location')"></td><td style="overflow:visible;white-space:normal"><div style="display:flex;flex-direction:column;gap:4px">${bldType(sub.typeitem,'seT_'+uid)}${bldPriv(sub.privilege||'','seP_'+uid)}</div></td><td style="overflow:visible"><div class="act-btns"><button class="btn btn-save" onclick="saveSubEdit('${ej(pid)}',${si})">บันทึก</button><button class="btn btn-can" onclick="render()">ยกเลิก</button></div></td>`}
async function saveSubEdit(pid,si){const sub=(subs[pid]||[])[si];if(!sub)return;const uid=ci(sub.iditem),nm=document.getElementById('seN_'+uid).value.trim();if(!nm){alert('กรุณากรอกชื่อ');return}showOv();try{await API.put('/api/items/'+encodeURIComponent(sub.iditem),{name:nm,quantity:document.getElementById('seQ_'+uid).value,brand:document.getElementById('seB_'+uid).value.trim(),location:document.getElementById('seL_'+uid).value.trim(),typeitem:document.getElementById('seT_'+uid).value,privilege:document.getElementById('seP_'+uid)?.value||''});toast('บันทึกสำเร็จ');await loadAll()}catch(e){toast(e.message,true);hideOv()}}
async function delSub(pid,si){if(!CAN_EDIT)return;const sub=(subs[pid]||[])[si];if(!sub||!confirm(`ลบ ${sub.iditem}?`))return;showOv();try{await API.del('/api/items/'+encodeURIComponent(sub.iditem));toast('ลบเรียบร้อย');await loadAll()}catch(e){toast(e.message,true);hideOv()}}

function showAc(inp,type){document.querySelectorAll('ul.ac').forEach(u=>u.remove());const list=type==='brand'?uBrands:uLocs,val=inp.value.toLowerCase();if(!val)return;const fil=list.filter(l=>l.toLowerCase().includes(val)).slice(0,10);if(!fil.length)return;const ul=document.createElement('ul');ul.className='ac';const rc=inp.getBoundingClientRect();ul.style.top=(rc.bottom+scrollY)+'px';ul.style.left=(rc.left+scrollX)+'px';ul.style.width=rc.width+'px';document.body.appendChild(ul);fil.forEach(v=>{const li=document.createElement('li');li.textContent=v;li.onclick=()=>{inp.value=v;ul.remove()};ul.appendChild(li)})}
document.addEventListener('click',e=>{if(!e.target.closest('ul.ac')&&!e.target.matches('input'))document.querySelectorAll('ul.ac').forEach(u=>u.remove())});

async function openTx(id,name){document.getElementById('txId').textContent=id;document.getElementById('txName').textContent=name;document.getElementById('txBody').innerHTML='<div class="tx-spin"><div class="tx-spin-inner"></div></div>';document.getElementById('txFoot').textContent='กำลังโหลด...';document.getElementById('txOv').classList.add('on');try{const rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));rows.sort((a,b)=>{const p=ts=>{if(!ts)return 0;const m=ts.match(/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})/);if(m)return new Date(m[3],m[2]-1,m[1],m[4],m[5],m[6]).getTime();return new Date(ts).getTime()||0};return p(b.Timestamp)-p(a.Timestamp)});renderTxModal(rows)}catch(e){document.getElementById('txBody').innerHTML=`<div class="tx-empty">โหลดล้มเหลว</div>`}}
function closeTx(){document.getElementById('txOv').classList.remove('on')}
function renderTxModal(data){const body=document.getElementById('txBody'),foot=document.getElementById('txFoot');if(!data.length){body.innerHTML='<div class="tx-empty">ไม่พบ Transaction</div>';foot.textContent='ไม่มีข้อมูล';return}foot.textContent=`พบ ${data.length} รายการ`;let h='<table class="tx-tbl"><thead><tr><th>วันที่</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ชั้นวาง</th><th>หมายเหตุ</th><th>รูป</th></tr></thead><tbody>';data.forEach(r=>{const tc=TM[r['ประเภทข้อมูล']||'']||'';const pic=r['รูปประกอบ']?`<a href="${r['รูปประกอบ']}" target="_blank" style="color:#2749F5;font-size:12px">ดูรูป</a>`:'-';h+=`<tr><td style="white-space:nowrap">${r.Timestamp||'-'}</td><td>${r['ชื่อผู้ดำเนินงาน']||'-'}</td><td><span class="tx-type ${tc}">${r['ประเภทข้อมูล']||'-'}</span></td><td>${r['หมายเลขเอกสาร']||'-'}</td><td>${r['รายการ']||'-'}</td><td>${r['จำนวน']!==''?r['จำนวน']:'-'}</td><td>${r['ราคาต่อหน่วย']!==''?r['ราคาต่อหน่วย']:'-'}</td><td>${r['ชั้นวาง']||'-'}</td><td>${r['หมายเหตุ']||'-'}</td><td>${pic}</td></tr>`});h+='</tbody></table>';body.innerHTML=h}
loadAll();
</script>
</body>
</html>
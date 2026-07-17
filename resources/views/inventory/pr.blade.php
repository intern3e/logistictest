<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ใบขอซื้อ - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#ece9d8;min-height:100vh;padding-bottom:40px}
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
    #content{max-width:860px;margin:0 auto;padding:68px 16px 40px}
    .page-title{font-size:20px;font-weight:700;color:#1a1a6e;margin-bottom:16px;padding-bottom:6px;border-bottom:2px solid #a8c3e0}
    .form-group{margin-bottom:14px}
    .form-label{display:block;font-size:15px;font-weight:600;color:#333;margin-bottom:3px}
    .required-mark{color:#dc3545;margin-left:3px}
    .form-input,.form-select,.form-textarea{width:100%;font-size:15px;padding:6px 8px;border:2px inset #aaa;background:#fff;color:#000;font-family:'Sarabun',Arial,sans-serif;outline:none}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:#2358a4}
    .form-input[readonly]{background:#e9e9e9;color:#555;cursor:not-allowed}
    .form-textarea{resize:vertical;min-height:70px}
    .items-section{background:#fff;border:2px inset #aaa;margin-bottom:14px}
    .items-header{background:#444;color:#fff;padding:7px 10px;font-size:15px;font-weight:700;display:flex;justify-content:space-between;align-items:center}
    .btn-add-row{background:linear-gradient(180deg,#FCA50D,#e69500);border:1px solid #c07800;color:#000;font-size:13px;font-weight:700;padding:3px 12px;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    table{width:100%;border-collapse:collapse;table-layout:fixed}
    th{background:#666;color:#fff;padding:5px 6px;text-align:left;font-size:12px;border-right:1px solid #555;overflow:hidden;white-space:nowrap}
    th:last-child{border-right:none}
    td{padding:3px 4px;border-bottom:1px solid #e0e0e0;vertical-align:middle}
    td input,td select{width:100%;border:1px solid #ccc;padding:3px 4px;font-size:13px;font-family:'Sarabun',Arial,sans-serif;background:#fff;box-sizing:border-box}
    td input:focus,td select:focus{outline:none;border-color:#2358a4}
    td input[readonly]{background:#e9e9e9;color:#555;cursor:not-allowed}
    .btn-del-row{background:#dc3545;border:none;color:#fff;font-size:12px;font-weight:700;padding:2px 7px;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    tr.row-new{background:#FFFDE7}
    tr.row-new td{border-bottom:1px solid #FFE082}
    .badge-new{display:inline-block;background:#F59E0B;color:#000;font-size:10px;font-weight:700;padding:1px 5px;margin-left:4px;vertical-align:middle}
    #acDropdown{display:none;position:fixed;background:#fff;border:2px solid #2358a4;max-height:220px;overflow-y:auto;z-index:5000;box-shadow:0 6px 20px rgba(0,0,0,.3);min-width:300px}
    .ac-item{padding:7px 10px;font-size:13px;cursor:pointer;border-bottom:1px solid #eee;color:#222;line-height:1.4}
    .ac-item:last-child{border-bottom:none}
    .ac-item:hover,.ac-item.ac-active{background:#2358a4;color:#fff}
    .ac-item .ac-name{font-weight:700;display:block}
    .ac-item .ac-meta{font-size:11px;color:#888;display:block}
    .ac-item:hover .ac-meta,.ac-item.ac-active .ac-meta{color:#cde}
    .row-img-thumb{position:relative;display:inline-block}
    .row-img-thumb img{width:44px;height:44px;object-fit:cover;border:1px solid #ccc;display:block}
    .row-img-del{position:absolute;top:0;right:0;background:#dc3545;color:#fff;border:none;font-size:10px;font-weight:700;width:15px;height:15px;cursor:pointer;line-height:15px;text-align:center;padding:0}
    .btn-row-img{background:linear-gradient(180deg,#f5f5f5,#e0e0e0);border:1px dashed #999;color:#555;font-size:11px;padding:3px 6px;cursor:pointer;font-family:'Sarabun',Arial,sans-serif;position:relative;overflow:hidden;white-space:nowrap}
    .btn-row-img input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;font-size:0}
    .doc-imgs-wrap{display:flex;flex-wrap:wrap;gap:3px;align-items:flex-start}
    .doc-img-thumb{position:relative;display:inline-block}
    .doc-img-thumb img{width:40px;height:40px;object-fit:cover;border:1px solid #bbb;display:block}
    .doc-img-del{position:absolute;top:0;right:0;background:#dc3545;color:#fff;border:none;font-size:9px;font-weight:700;width:14px;height:14px;cursor:pointer;line-height:14px;text-align:center;padding:0}
    .btn-doc-img{background:linear-gradient(180deg,#e8f0fe,#d0deff);border:1px dashed #7a9fc8;color:#1a3f7a;font-size:11px;padding:3px 5px;cursor:pointer;font-family:'Sarabun',Arial,sans-serif;position:relative;overflow:hidden;white-space:nowrap}
    .btn-doc-img input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;font-size:0}
    .btn-row{display:flex;gap:10px;margin-top:18px}
    .btn-submit{flex:1;font-size:16px;padding:9px;background:#388E3C;border:1px solid #2e7d32;color:#fff;font-weight:700;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .btn-submit:hover:not(:disabled){background:#2e7d32}
    .btn-submit:disabled{opacity:.5;cursor:not-allowed}
    .btn-clear{font-size:16px;padding:9px 20px;background:linear-gradient(180deg,#f0f0f0,#d0d0d0);border:1px solid #999;color:#555;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .btn-clear:hover{background:linear-gradient(180deg,#fff,#e8e8e8)}
    @media print{.topbar,.sidebar,.sb-ov,.ov,.btn-row,.btn-add-row,.btn-del-row{display:none!important}body{background:#fff}#content{padding:0;max-width:100%}}
    .btn-home{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:7px 18px;
  font-family:'Sarabun',sans-serif;
  font-size:14px;
  font-weight:600;
  color:#fff;
  text-decoration:none;
  cursor:pointer;
  white-space:nowrap;
  border:1px solid #8b0000;
  background:linear-gradient(180deg,#e03030 0%,#b00000 50%,#8b0000 100%);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.3),1px 1px 3px rgba(0,0,0,.3);
}
.btn-home:hover{
  background:linear-gradient(180deg,#ff4444 0%,#cc0000 50%,#a00000 100%);
}
.btn-home:active{
  box-shadow:inset 1px 1px 3px rgba(0,0,0,.4);
  transform:translateY(1px);
}
  </style>
</head>
<body>
<div class="ov" id="ov"><div><div class="sp"></div><p id="ovText">กำลังโหลด...</p></div></div>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <div style="height:1px;background:#a8c3e0;margin:5px 12px"></div>
    <div class="sb-sec">ใบขอซื้อ</div>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.pr', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>สร้างใบขอซื้อ</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.pr.dashboard', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>รายการขอซื้อ</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
  <a href="http://server_update:8000/solist" button  type="submit" class="btn-home">🚪 หน้าหลัก</a>
</div>

<div id="acDropdown"></div>
<div id="content">
  <div class="page-title">ใบขอซื้อ</div>
  <div class="form-group">
    <label class="form-label">ชื่อผู้ขอซื้อ:<span class="required-mark">*</span></label>
    <input type="text" id="requester" class="form-input" value="{{ $authUser['name'] ?? '' }}" readonly>
  </div>
  <div class="form-group">
    <label class="form-label">ชื่อทีมช่าง:<span class="required-mark">*</span></label>
    <input type="text" id="buyerName" class="form-input" placeholder="กรอกชื่อทีมช่าง">
  </div>
  <div class="form-group">
    <label class="form-label">เบอร์โทรศัพท์:<span class="required-mark">*</span></label>
    <input type="tel" id="phoneNumber" class="form-input" placeholder="กรอกเบอร์โทรศัพท์">
  </div>
  <div class="form-group">
    <label class="form-label">PO:<span class="required-mark">*</span></label>
    <input type="text" id="poNumber" class="form-input" placeholder="กรอกเลข PO">
  </div>
  <div class="form-group">
    <label class="form-label">วันที่:</label>
    <input type="text" id="reqDate" class="form-input" readonly>
  </div>

  <div class="items-section">
    <div class="items-header">
      <span>รายการสินค้าที่ขอซื้อ</span>
      <button class="btn-add-row" onclick="addRow()">+ เพิ่มรายการ</button>
    </div>
    <table id="itemsTable">
      <colgroup>
        <col style="width:22px"><col style="width:80px"><col><col style="width:100px">
        <col style="width:46px"><col style="width:66px"><col style="width:68px">
        <col style="width:66px"><col style="width:52px"><col style="width:68px"><col style="width:22px">
      </colgroup>
      <thead>
        <tr>
          <th>#</th><th>รหัสสินค้า</th><th>ชื่อสินค้า</th><th>บริษัท</th>
          <th>จำนวน</th><th>ราคา/หน่วย</th><th>สกุลเงิน</th>
          <th>รวม (฿)</th><th>รูปสินค้า</th><th>รูปเอกสาร</th><th></th>
        </tr>
      </thead>
      <tbody id="itemsBody"></tbody>
    </table>
  </div>

  <div class="form-group">
    <label class="form-label">เหตุผลที่ขอซื้อ:<span class="required-mark">*</span></label>
    <select id="reason" class="form-select" onchange="onReasonChange()">
      <option value="">-- เลือกเหตุผล --</option>
      <option value="ของหมด / ไม่พอ / สำรอง">ของหมด / ไม่พอ / สำรอง</option>
      <option value="อุปกรณ์เสีย / สูญหาย">อุปกรณ์เสีย / สูญหาย</option>
      <option value="ไม่มีเครื่องมือ / ต้องใช้เฉพาะทาง">ไม่มีเครื่องมือ / ต้องใช้เฉพาะทาง</option>
    </select>
    <textarea id="reasonOther" class="form-textarea" placeholder="ระบุเหตุผลเพิ่มเติม..." rows="2" style="display:none;margin-top:6px"></textarea>
  </div>
  <div class="form-group">
    <label class="form-label">หมายเหตุเพิ่มเติม:</label>
    <textarea id="note" class="form-textarea" placeholder="(ถ้ามี)" rows="2"></textarea>
  </div>
  <div class="btn-row">
    <button class="btn-submit" onclick="submitForm()" id="submitBtn">ส่งข้อมูล</button>
    <button class="btn-clear" onclick="clearForm()">ล้างข้อมูล</button>
  </div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const COMPANY_LIST=[{code:"3E",label:"Triple E Trading"},{code:"3IN",label:"Triple E Innovation"},{code:"3EM",label:"Triple E Empire Group"},{code:"3EL",label:"Triple E Lighting"},{code:"HD",label:"Hikari Denki"},{code:"EP",label:"Eita & Paul"},{code:"3P",label:"Triple P Factory & Eng"},{code:"AE&T",label:"AE&T International"}];
const CURRENCY_LIST=[{value:"บาท",label:"บาท (THB)"},{value:"เยน",label:"เยน (JPY)"},{value:"หยวน",label:"หยวน (CNY)"},{value:"ดอลล่า",label:"ดอลล่า (USD)"}];
const CO_OPTS=COMPANY_LIST.map(c=>`<option value="${c.code}">${c.code} – ${c.label}</option>`).join('');
const CUR_OPTS=CURRENCY_LIST.map(c=>`<option value="${c.value}">${c.label}</option>`).join('');

let rowCount=0,allProducts=[],exRates={บาท:1};
let activeAcRowId=null,activeAcItems=[],acHighlight=-1;
const rowImages={},docImages={};
const MAX_DOC=1;

const $=id=>document.getElementById(id);
const showLoading=txt=>{$('ovText').textContent=txt||'กำลังโหลด...';$('ov').classList.add('on')};
const hideLoading=()=>$('ov').classList.remove('on');
function openSB(){$('sidebar').classList.add('open');$('sbOv').classList.add('open')}
function closeSB(){$('sidebar').classList.remove('open');$('sbOv').classList.remove('open')}
const formatDate=d=>`${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;

function toTHB(amount,currency){return amount*(exRates[currency]??1)}
function calcRow(tr){
  const qty=parseFloat(tr.querySelector('.input-qty')?.value)||0;
  const price=parseFloat(tr.querySelector('.input-price')?.value)||0;
  const cur=tr.querySelector('.input-currency')?.value||'บาท';
  const cell=tr.querySelector('.row-total');
  if(cell)cell.textContent=toTHB(qty*price,cur).toLocaleString('th-TH',{maximumFractionDigits:2});
}
function onReasonChange(){const v=$('reason').value,o=$('reasonOther');o.style.display=v==='อื่นๆ'?'block':'none';if(v!=='อื่นๆ')o.value=''}

function lev(a,b){const d=Array.from({length:a.length+1},(_,i)=>Array(b.length+1).fill(0));for(let i=0;i<=a.length;i++)d[i][0]=i;for(let j=0;j<=b.length;j++)d[0][j]=j;for(let i=1;i<=a.length;i++)for(let j=1;j<=b.length;j++){const c=a[i-1]===b[j-1]?0:1;d[i][j]=Math.min(d[i-1][j]+1,d[i][j-1]+1,d[i-1][j-1]+c)}return d[a.length][b.length]}
function sim(a,b){if(!a||!b)return 0;return 1-lev(a.toLowerCase(),b.toLowerCase())/Math.max(a.length,b.length)}
function posAc(el){const dd=$('acDropdown'),r=el.getBoundingClientRect();dd.style.left=r.left+'px';dd.style.top=(r.bottom+2)+'px';dd.style.width=Math.max(300,r.width)+'px'}
function closeAc(){$('acDropdown').style.display='none';activeAcRowId=null;activeAcItems=[];acHighlight=-1}
function hlAc(i){const ds=$('acDropdown').querySelectorAll('.ac-item');ds.forEach((d,j)=>d.classList.toggle('ac-active',j===i));if(ds[i])ds[i].scrollIntoView({block:'nearest'});acHighlight=i}
function renderAc(items,rid){
  const dd=$('acDropdown');activeAcItems=items;acHighlight=-1;dd.innerHTML='';
  if(!items.length){dd.innerHTML=`<div class="ac-item" style="color:#999;cursor:default">ไม่พบสินค้าในระบบ</div>`}
  else{items.forEach(it=>{const d=document.createElement('div');d.className='ac-item';d.innerHTML=`<span class="ac-name">${it.name}</span><span class="ac-meta">${it.iditem} | คงเหลือ: ${it.quantity} | ${it.location||'-'}</span>`;d.addEventListener('mousedown',e=>{e.preventDefault();selectAc(it,rid)});dd.appendChild(d)})}
  dd.style.display='block';
}
function selectAc(it,rid){const tr=$('row_'+rid);if(!tr)return;tr.querySelector('.input-name').value=it.name;tr.querySelector('.input-iditem').value=it.iditem;tr.classList.remove('row-new');const badge=tr.querySelector('.badge-new');if(badge)badge.remove();if(it.privilege){const sel=tr.querySelector('.input-company');if(sel&&[...sel.options].some(o=>o.value===it.privilege))sel.value=it.privilege}closeAc()}
function onNameInput(el,rid){const q=el.value.trim(),tr=el.closest('tr');tr.querySelector('.input-iditem').value='';tr.classList.remove('row-new');const b=tr.querySelector('.badge-new');if(b)b.remove();if(!q){closeAc();return}const lq=q.toLowerCase();const res=allProducts.map(it=>({...it,score:sim(lq,(it.name||'').toLowerCase())})).filter(it=>{const n=(it.name||'').toLowerCase();return it.score>=0.45||n.includes(lq)||n.startsWith(lq)}).sort((a,b)=>b.score-a.score).slice(0,12);activeAcRowId=rid;posAc(el);renderAc(res,rid)}
function onNameBlur(el,rid){setTimeout(()=>{const tr=$('row_'+rid);if(!tr)return;const name=tr.querySelector('.input-name').value.trim();const id=tr.querySelector('.input-iditem').value.trim();if(name&&!id){tr.classList.add('row-new');if(!tr.querySelector('.badge-new')){const b=document.createElement('span');b.className='badge-new';b.textContent='ใหม่';tr.querySelector('.input-name').insertAdjacentElement('afterend',b)}}},200)}
function onNameKeydown(e,rid){if($('acDropdown').style.display==='none')return;if(e.key==='ArrowDown'){e.preventDefault();hlAc(Math.min(acHighlight+1,activeAcItems.length-1))}else if(e.key==='ArrowUp'){e.preventDefault();hlAc(Math.max(acHighlight-1,0))}else if(e.key==='Enter'){e.preventDefault();if(acHighlight>=0&&activeAcItems[acHighlight])selectAc(activeAcItems[acHighlight],rid)}else if(e.key==='Escape')closeAc()}
document.addEventListener('click',e=>{if($('acDropdown').style.display!=='none'&&!$('acDropdown').contains(e.target)&&!e.target.classList.contains('input-name'))closeAc()});
window.addEventListener('scroll',()=>{if(activeAcRowId){const tr=$('row_'+activeAcRowId);if(tr)posAc(tr.querySelector('.input-name'))}},true);
window.addEventListener('resize',()=>closeAc());

function addRow(){
  rowCount++;const rid=rowCount;docImages[rid]=[];
  const tr=document.createElement('tr');tr.id='row_'+rid;
  tr.innerHTML=`
    <td style="text-align:center;font-weight:600;color:#555;font-size:12px;padding-top:5px">${rid}</td>
    <td><input type="text" class="input-iditem" readonly placeholder="-"></td>
    <td><input type="text" class="input-name" placeholder="พิมพ์ชื่อสินค้า..." oninput="onNameInput(this,${rid})" onkeydown="onNameKeydown(event,${rid})" onfocus="if(this.value.trim())onNameInput(this,${rid})" onblur="onNameBlur(this,${rid})" autocomplete="off"></td>
    <td><select class="input-company"><option value="">-- บริษัท --</option>${CO_OPTS}</select></td>
    <td><input type="number" class="input-qty" placeholder="0" min="0" step="1" style="text-align:right" oninput="calcRow(this.closest('tr'))"></td>
    <td><input type="number" class="input-price" placeholder="0.00" min="0" step="0.01" style="text-align:right" oninput="calcRow(this.closest('tr'))"></td>
    <td><select class="input-currency" onchange="calcRow(this.closest('tr'))">${CUR_OPTS}</select></td>
    <td style="text-align:right;font-weight:600;color:#1a1a6e;font-size:12px;padding-top:5px" class="row-total">0</td>
    <td style="padding:3px 4px" id="imgcell_${rid}"><button class="btn-row-img"><input type="file" accept="image/*" onchange="rowImgUpload(event,${rid})">📷</button></td>
    <td style="padding:3px 4px" id="doccell_${rid}"><div class="doc-imgs-wrap" id="docwrap_${rid}"><button class="btn-doc-img"><input type="file" accept="image/*" onchange="docImgUpload(event,${rid})">📎+</button></div></td>
    <td style="text-align:center;padding-top:4px"><button class="btn-del-row" onclick="delRow('row_${rid}')">✕</button></td>
  `;
  $('itemsBody').appendChild(tr);
}
function delRow(id){const el=$(id);if(!el)return;const rid=id.replace('row_','');delete rowImages[rid];delete docImages[rid];el.remove();$('itemsBody').querySelectorAll('tr').forEach((tr,i)=>{tr.cells[0].textContent=i+1})}

function compressImage(dataUrl,callback){const img=new Image();img.onload=()=>{const MAX=800;let w=img.width,h=img.height;if(w>MAX||h>MAX){if(w>h){h=Math.round(h*MAX/w);w=MAX}else{w=Math.round(w*MAX/h);h=MAX}}const c=document.createElement('canvas');c.width=w;c.height=h;c.getContext('2d').drawImage(img,0,0,w,h);callback(c.toDataURL('image/jpeg',0.7))};img.src=dataUrl}

function rowImgUpload(e,rid){const file=e.target.files[0];if(!file)return;if(!file.type.startsWith('image/')){alert('รองรับเฉพาะไฟล์รูปภาพ');return}if(file.size>10*1024*1024){alert('ไฟล์ใหญ่เกิน 10MB');return}const reader=new FileReader();reader.onload=ev=>compressImage(ev.target.result,compressed=>{rowImages[rid]={name:file.name,dataUrl:compressed};renderRowImg(rid)});reader.readAsDataURL(file);e.target.value=''}
function renderRowImg(rid){const cell=$('imgcell_'+rid);if(!cell)return;const img=rowImages[rid];if(!img){cell.innerHTML=`<button class="btn-row-img"><input type="file" accept="image/*" onchange="rowImgUpload(event,${rid})">📷</button>`}else{cell.innerHTML=`<div class="row-img-thumb" title="${img.name}"><img src="${img.dataUrl}"><button class="row-img-del" onclick="delRowImg(${rid})">✕</button></div>`}}
function delRowImg(rid){rowImages[rid]=null;renderRowImg(rid)}

function docImgUpload(e,rid){const file=e.target.files[0];if(!file)return;if(!docImages[rid])docImages[rid]=[];if(docImages[rid].length>=MAX_DOC){alert('แนบรูปเอกสารได้สูงสุด '+MAX_DOC+' รูป');return}if(!file.type.startsWith('image/')){alert('รองรับเฉพาะไฟล์รูปภาพ');return}if(file.size>10*1024*1024){alert('ไฟล์ใหญ่เกิน 10MB');return}const reader=new FileReader();reader.onload=ev=>compressImage(ev.target.result,compressed=>{if(docImages[rid].length>=MAX_DOC)return;docImages[rid].push({name:file.name,dataUrl:compressed});renderDocImgs(rid)});reader.readAsDataURL(file);e.target.value=''}
function renderDocImgs(rid){const wrap=$('docwrap_'+rid);if(!wrap)return;const imgs=docImages[rid]||[];wrap.innerHTML='';imgs.forEach((img,i)=>{const div=document.createElement('div');div.className='doc-img-thumb';div.title=img.name;div.innerHTML=`<img src="${img.dataUrl}"><button class="doc-img-del" onclick="delDocImg(${rid},${i})">✕</button>`;wrap.appendChild(div)});if(imgs.length<MAX_DOC){const btn=document.createElement('button');btn.className='btn-doc-img';btn.innerHTML=`<input type="file" accept="image/*" onchange="docImgUpload(event,${rid})">📎+`;wrap.appendChild(btn)}}
function delDocImg(rid,idx){if(!docImages[rid])return;docImages[rid].splice(idx,1);renderDocImgs(rid)}

function submitForm(){
  const buyerName=$('buyerName').value.trim();
  const phone=$('phoneNumber').value.trim();
  const poNumber=$('poNumber').value.trim();
  const reasonSel=$('reason').value;
  const reasonOth=$('reasonOther').value.trim();
  const reason=reasonSel==='อื่นๆ'?reasonOth:reasonSel;

  if(!buyerName){alert('กรุณากรอกชื่อทีมช่าง');return}
  if(!phone){alert('กรุณากรอกเบอร์โทรศัพท์');return}
  if(!poNumber){alert('กรุณากรอกเลข PO');return}
  if(!reason){alert(reasonSel==='อื่นๆ'?'กรุณาระบุเหตุผลเพิ่มเติม':'กรุณาเลือกเหตุผลที่ขอซื้อ');return}

  const rows=$('itemsBody').querySelectorAll('tr');
  if(!rows.length){alert('กรุณากรอกรายการสินค้าอย่างน้อย 1 รายการ');return}

  const items=[];
  for(let i=0;i<rows.length;i++){
    const tr=rows[i],rowNo=i+1;
    const name=tr.querySelector('.input-name')?.value.trim();
    const iditem=tr.querySelector('.input-iditem')?.value.trim();
    const company=tr.querySelector('.input-company')?.value;
    const qtyRaw=tr.querySelector('.input-qty')?.value.trim();
    const priceRaw=tr.querySelector('.input-price')?.value.trim();
    const currency=tr.querySelector('.input-currency')?.value||'บาท';
    const rid=tr.id.replace('row_','');
    const hasImg=!!(rowImages[rid]?.dataUrl);
    const docArr=docImages[rid]||[];
    if(!name){alert(`รายการที่ ${rowNo}: กรุณากรอกชื่อสินค้า`);tr.querySelector('.input-name')?.focus();return}
    if(!company){alert(`รายการที่ ${rowNo}: กรุณาเลือกบริษัท`);tr.querySelector('.input-company')?.focus();return}
    const qty=parseFloat(qtyRaw);
    if(!qtyRaw||isNaN(qty)||qty<=0){alert(`รายการที่ ${rowNo}: กรุณากรอกจำนวน (ต้องมากกว่า 0)`);tr.querySelector('.input-qty')?.focus();return}
    const price=parseFloat(priceRaw);
    if(priceRaw===''||priceRaw===null||isNaN(price)||price<0){alert(`รายการที่ ${rowNo}: กรุณากรอกราคา/หน่วย`);tr.querySelector('.input-price')?.focus();return}
    if(!hasImg){alert(`รายการที่ ${rowNo}: กรุณาแนบรูปสินค้า`);return}
    if(docArr.length===0){alert(`รายการที่ ${rowNo}: กรุณาแนบรูปเอกสารอย่างน้อย 1 รูป`);return}
    items.push({iditem,name,company,qty,price,currency,thbPrice:toTHB(price,currency),_rid:rid,_imgData:rowImages[rid].dataUrl,_docData:docArr.map(d=>d.dataUrl)});
  }

  const newCount=items.filter(i=>!i.iditem).length;
  if(newCount>0&&!confirm(`มีสินค้าใหม่ ${newCount} รายการที่ยังไม่มีในระบบ\nระบบจะสร้างสินค้าใหม่ให้อัตโนมัติ\n\nส่งข้อมูลต่อไปหรือไม่?`))return;

  const btn=$('submitBtn');
  btn.disabled=true;showLoading('กำลังเตรียมข้อมูล...');

  // ตั้งชื่อไฟล์: ddmmyy_HHmmss_rN(_dN)
  const ts=new Date();
  const pad=n=>String(n).padStart(2,'0');
  const fileStamp=pad(ts.getDate())+pad(ts.getMonth()+1)+String(ts.getFullYear()).slice(-2)+'_'+pad(ts.getHours())+pad(ts.getMinutes())+pad(ts.getSeconds());

  const jobs=[];
  items.forEach((item,i)=>{
    const rowNo=i+1;
    jobs.push({itemIdx:i,type:'img',docIdx:-1,dataUrl:item._imgData,fileName:fileStamp+'_r'+rowNo});
    item._docData.forEach((d,di)=>{
      jobs.push({itemIdx:i,type:'doc',docIdx:di,dataUrl:d,fileName:fileStamp+'_r'+rowNo+'_d'+(di+1)});
    });
  });

  (async()=>{
    const imageUrls=new Array(items.length).fill('');
    const docUrls=items.map(it=>it._docData.map(()=>''));

    // ── อัปโหลดทีละรูป → Laravel → GAS → Google Drive ──
    for(let j=0;j<jobs.length;j++){
      const job=jobs[j];
      showLoading('กำลังอัปโหลดรูป '+(j+1)+'/'+jobs.length+'...');
      try{
        const r=await(await fetch('/api/pr/upload',{
          method:'POST',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
          body:JSON.stringify({image:job.dataUrl,fileName:job.fileName})
        })).json();
        const url=r.url||'';
        if(job.type==='img')imageUrls[job.itemIdx]=url;else docUrls[job.itemIdx][job.docIdx]=url;
      }catch(e){console.warn('upload fail:',e)}
    }

    // ── บันทึกใบขอซื้อ ──
    showLoading('กำลังบันทึก...');
    const itemsToSend=items.map((it,i)=>({
      iditem:it.iditem||'',name:it.name,company:it.company,
      qty:it.qty,price:it.price,currency:it.currency,
      thb_price:it.thbPrice,
      image_url:imageUrls[i]||'',
      doc_images:docUrls[i]||[]
    }));
    try{
      const res=await(await fetch('/api/pr',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body:JSON.stringify({
          requester:$('requester').value,
          buyerName,phone,po_number:poNumber,
          date:$('reqDate').value,reason,
          note:$('note').value.trim(),
          items:itemsToSend
        })
      })).json();
      hideLoading();btn.disabled=false;
      if(res.success){
        alert('ส่งข้อมูลเรียบร้อย!\nPR No.: '+res.pr_id+'\n\nรอ Admin อนุมัติ');
        resetForm();
      }else alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));
    }catch(err){hideLoading();btn.disabled=false;alert('Error: '+err.message)}
  })();
}

function resetForm(){
  $('itemsBody').innerHTML='';
  $('buyerName').value='';$('phoneNumber').value='';$('poNumber').value='';
  $('reason').value='';$('reasonOther').value='';$('reasonOther').style.display='none';
  $('note').value='';rowCount=0;
  Object.keys(rowImages).forEach(k=>delete rowImages[k]);
  Object.keys(docImages).forEach(k=>delete docImages[k]);
  addRow();
}
function clearForm(){if(!confirm('ล้างข้อมูลทั้งหมด?'))return;resetForm()}

// ══ INIT ══
$('reqDate').value=formatDate(new Date());
addRow();
(async()=>{try{const r=await(await fetch('/api/items/pagedata')).json();allProducts=r.items||[]}catch(e){}})();
(async()=>{try{const r=await(await fetch('/api/exchange-rates')).json();if(r)exRates={บาท:1,...r}}catch(e){}})();
</script>
</body>
</html>
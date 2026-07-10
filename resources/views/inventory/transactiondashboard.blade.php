<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>รายการสินค้า เข้า-ออก - 3E TRADING</title>
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
    #content{padding:62px 16px 16px}
    .hdr{padding:12px 0 10px}
    .hdr h2{color:#333;font-size:20px;font-weight:700;margin-bottom:12px}
    .fbar{display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end}
    .fbox{display:flex;flex-direction:column;gap:4px}
    .fbox label{font-weight:600;color:#333;font-size:13px;display:flex;align-items:center;gap:5px}
    .fbox label::before{content:'';width:3px;height:12px;background:#F5AD27}
    .finput{padding:6px 8px;border:2px inset #aaa;font-size:14px;background:#fff;font-family:'Sarabun',sans-serif;width:100%}
    .finput:focus{outline:none;border-color:#2358a4}
    .btn-clr{padding:7px 16px;border:1px solid #999;cursor:pointer;font-weight:600;font-size:14px;font-family:'Sarabun',sans-serif;background:linear-gradient(180deg,#f0f0f0,#d0d0d0);color:#555}
    .btn-clr:hover{background:linear-gradient(180deg,#fff,#e0e0e0)}
    .tbl-wrap{background:#fff;overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:13px}
    thead{background:linear-gradient(180deg,#555,#333)}
    th{color:#fff;padding:9px 8px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#444;z-index:5;border-right:1px solid #555}
    th:last-child{border-right:none}
    td{padding:7px 8px;border-bottom:1px solid #f0f0f0;color:#333;font-size:13px;vertical-align:middle}
    tbody tr:hover{background:#f0f5ff}
    .edit-input{width:100%;padding:4px 6px;font-size:13px;border:2px inset #aaa;font-family:'Sarabun',sans-serif}
    .edit-input:focus{outline:none;border-color:#2358a4}
    td.tc{font-weight:600;color:#fff;text-align:center;padding:4px 8px;font-size:12px;white-space:nowrap}
    td.t-in{background:#00D162}td.t-ret{background:#19CBFC;color:#222}td.t-sell{background:#FF1A1A}td.t-bor{background:#F8FF2E;color:#000}td.t-wit{background:#FF8538}
    td a{color:#2749F5;text-decoration:none;font-weight:600;padding:3px 8px;background:rgba(39,73,245,.1);font-size:13px;display:inline-block}
    td a:hover{background:#F5AD27;color:#fff}
    .acts{display:flex;gap:5px;flex-wrap:wrap}
    .acts button{padding:5px 11px;cursor:pointer;font-weight:600;font-size:13px;font-family:'Sarabun',sans-serif;white-space:nowrap;border:none;color:#fff}
    .a-edit{background:linear-gradient(180deg,#6090f0,#2749F5);border:1px solid #1a35c7!important}
    .a-save{background:linear-gradient(180deg,#44dd77,#00D162);border:1px solid #009940!important}
    .a-del{background:linear-gradient(180deg,#ff4444,#FF1A1A);border:1px solid #cc0000!important}
    .a-can{background:linear-gradient(180deg,#f0f0f0,#d8d8d8);color:#555!important;border:1px solid #999!important}
    #paging{margin-top:6px;text-align:center;display:flex;justify-content:center;align-items:center;gap:12px;padding:14px}
    .pg-btn{background:linear-gradient(180deg,#f0f0f0,#d0d0d0);color:#333;border:1px solid #999;padding:7px 20px;cursor:pointer;font-weight:600;font-size:14px}
    .pg-btn:hover{background:linear-gradient(180deg,#fff,#e8e8e8)}
    .pg-info{font-weight:600;font-size:14px;color:#333;padding:7px 14px;background:#f5f5f5;border:1px solid #ccc}
    .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#333;color:#fff;padding:10px 24px;font-size:14px;font-weight:600;z-index:9999;display:none}
    .toast.on{display:block}
    @media(max-width:768px){.fbar{flex-direction:column} .fbox{width:100%!important}}
  </style>
</head>
<body>
<div class="ov" id="ov"><div><div class="sp"></div><p>กำลังโหลดข้อมูล...</p></div></div>
<div class="toast" id="toast"></div>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item cur" href="{{ route('inventory.transaction') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" href="{{ route('inventory.item') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name">{{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
  </div>
</div>

<div id="content">
  <div class="hdr">
    <h2>รายการสินค้า เข้า-ออก</h2>
    <div class="fbar">
      <div class="fbox" style="width:150px"><label>เลือกวันที่</label><input type="date" id="fDate" class="finput" onchange="applyFilter()"></div>
      <div class="fbox" style="width:180px"><label>ชื่อผู้ดำเนินงาน</label><input type="text" id="fOp" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="width:250px"><label>หมายเลขเอกสาร</label><input type="text" id="fBill" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="width:400px"><label>รายการสินค้า</label><input type="text" id="fItem" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="width:170px"><label>ประเภทข้อมูล</label>
        <select id="fType" class="finput" onchange="applyFilter()">
          <option value="">ทั้งหมด</option><option value="รับเข้าสต็อก">รับเข้าสต็อก</option><option value="คืนเข้าสต็อก">คืนเข้าสต็อก</option>
          <option value="ขายสินค้าออก">ขายสินค้าออก</option><option value="ยืมสินค้า">ยืมสินค้า</option><option value="เบิกของ">เบิกของ</option>
        </select>
      </div>
      <div class="fbox" style="width:130px"><label>ชั้นวาง</label><input type="text" id="fShelf" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <button class="btn-clr" onclick="clearFilter()">ล้างตัวกรอง</button>
    </div>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th>Timestamp</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ชั้นวาง</th><th>JOB Detail</th><th>รูป</th>
        @if($authRole==='admin')<th>จัดการ</th>@endif
      </tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const COLS=ROLE==='admin'?11:10;
const PG=100;
const typeMap={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};

const API={
  async get(u){return(await fetch(u)).json()},
  async put(u,d){return(await fetch(u,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(d)})).json()},
  async del(u){return(await fetch(u,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}})).json()},
};

let pageData=[], pg=1, lastPage=1, total=0;
let _debounce=null;

function showOv(){document.getElementById('ov').classList.add('on')}
function hideOv(){document.getElementById('ov').classList.remove('on')}
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){const t=document.getElementById('toast');t.textContent=m;t.style.background=e?'#cc0000':'#007722';t.classList.add('on');setTimeout(()=>t.classList.remove('on'),3000)}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}

/** สร้าง query string จาก filter + page */
function buildQuery(page){
  const fD=document.getElementById('fDate').value;
  const p=new URLSearchParams();
  p.set('page',page); p.set('limit',PG);
  if(fD){const parts=fD.split('-');p.set('fDate',parts[2]+'/'+parts[1]+'/'+parts[0])}
  const fOp=document.getElementById('fOp').value.trim();    if(fOp) p.set('fOp',fOp);
  const fBill=document.getElementById('fBill').value.trim();if(fBill) p.set('fBill',fBill);
  const fItem=document.getElementById('fItem').value.trim();if(fItem) p.set('fItem',fItem);
  const fType=document.getElementById('fType').value;        if(fType) p.set('fType',fType);
  const fShelf=document.getElementById('fShelf').value.trim();if(fShelf) p.set('fShelf',fShelf);
  return '/api/transaction?'+p.toString();
}

async function loadPage(page, showLoader){
  if(showLoader) showOv();
  try{
    const res=await API.get(buildQuery(page));
    pageData=res.data||[];
    pg=res.page||1;
    lastPage=res.lastPage||1;
    total=res.total||0;
    render();
  }catch(e){alert('โหลดล้มเหลว: '+e.message)}
  if(showLoader) hideOv();
}

function render(){
  const tb=document.getElementById('tb');tb.innerHTML='';
  if(!pageData.length){tb.innerHTML=`<tr><td colspan="${COLS}" style="text-align:center;padding:40px;color:#999">ไม่มีรายการ (${total} ทั้งหมด)</td></tr>`;renderPg();return}
  pageData.forEach((row,i)=>{
    const type=row['ประเภทข้อมูล']||'',tc=typeMap[type]||'';
    const pic=row['รูปประกอบ']?`<a href="${row['รูปประกอบ']}" target="_blank">ดูรูป</a>`:'-';
    const tr=document.createElement('tr');
    let h=`<td style="white-space:nowrap">${row['Timestamp']||'-'}</td><td>${row['ชื่อผู้ดำเนินงาน']||'-'}</td><td class="tc ${tc}">${type||'-'}</td><td>${row['หมายเลขเอกสาร']||'-'}</td><td>${row['รายการ']||'-'}</td><td>${row['จำนวน']!==''?row['จำนวน']:'-'}</td><td>${row['ราคาต่อหน่วย']!==''?row['ราคาต่อหน่วย']:'-'}</td><td>${row['ชั้นวาง']||'-'}</td><td>${row['หมายเหตุ']||'-'}</td><td>${pic}</td>`;
    if(ROLE==='admin')h+=`<td class="acts"><button class="a-edit" onclick="editRow(${i})">แก้ไข</button></td>`;
    tr.innerHTML=h;tb.appendChild(tr);
  });renderPg();
}

function renderPg(){
  const el=document.getElementById('paging');el.innerHTML='';
  if(pg>1){const b=document.createElement('button');b.className='pg-btn';b.textContent='← ก่อนหน้า';b.onclick=()=>{loadPage(pg-1,true);scrollTo(0,0)};el.appendChild(b)}
  const s=document.createElement('span');s.className='pg-info';s.textContent=`หน้า ${pg} / ${lastPage} (${total.toLocaleString()} รายการ)`;el.appendChild(s);
  if(pg<lastPage){const b=document.createElement('button');b.className='pg-btn';b.textContent='ถัดไป →';b.onclick=()=>{loadPage(pg+1,true);scrollTo(0,0)};el.appendChild(b)}
}

/** debounce 400ms สำหรับพิมพ์ filter */
function applyFilter(){
  clearTimeout(_debounce);
  _debounce=setTimeout(()=>loadPage(1,false),400);
}
function clearFilter(){
  ['fBill','fItem','fDate','fType','fShelf','fOp'].forEach(id=>document.getElementById(id).value='');
  loadPage(1,true);
}

// ── Edit (admin only) ──
function editRow(i){
  if(ROLE!=='admin')return;
  const row=pageData[i],tr=document.getElementById('tb').children[i];if(!tr)return;tr.style.background='#e0f2fe';
  const types=['รับเข้าสต็อก','คืนเข้าสต็อก','ขายสินค้าออก','ยืมสินค้า','เบิกของ'];
  tr.innerHTML=`<td style="white-space:nowrap;font-size:12px">${row['Timestamp']||'-'}</td><td><input class="edit-input" id="eOp" value="${esc(row['ชื่อผู้ดำเนินงาน']||'')}"></td><td><select id="eType" class="edit-input">${types.map(t=>`<option ${row['ประเภทข้อมูล']===t?'selected':''}>${t}</option>`).join('')}</select></td><td><input class="edit-input" id="eBill" value="${esc(row['หมายเลขเอกสาร']||'')}"></td><td style="color:#555;font-size:12px">${esc(row['รายการ']||'-')}</td><td><input class="edit-input" id="eQty" value="${row['จำนวน']||''}" type="number"></td><td><input class="edit-input" id="ePrice" value="${row['ราคาต่อหน่วย']!==''&&row['ราคาต่อหน่วย']!=null?row['ราคาต่อหน่วย']:''}" type="number" step="0.001"></td><td><input class="edit-input" id="eShelf" value="${esc(row['ชั้นวาง']||'')}"></td><td><input class="edit-input" id="eNote" value="${esc(row['หมายเหตุ']||'')}"></td><td><input type="hidden" id="eImg" value="${esc(row['รูปประกอบ']||'')}">-</td><td class="acts"><button class="a-save" onclick="saveRow(${i})">บันทึก</button><button class="a-del" onclick="delRow(${i})">ลบ</button><button class="a-can" onclick="loadPage(pg,false)">ยกเลิก</button></td>`;
}
async function saveRow(i){const row=pageData[i];const op=document.getElementById('eOp').value.trim(),bill=document.getElementById('eBill').value.trim();if(!op||!bill){alert('กรุณากรอกข้อมูลให้ครบ');return}if(!confirm('ต้องการบันทึก?'))return;showOv();try{await API.put('/api/transaction/'+encodeURIComponent(row.transaction_id),{operator:op,type:document.getElementById('eType').value,bill,quantity:document.getElementById('eQty').value,price:document.getElementById('ePrice').value||'',shelf:document.getElementById('eShelf').value.trim(),note:document.getElementById('eNote').value.trim(),image:document.getElementById('eImg').value.trim(),oldQuantity:row['จำนวน'],oldType:row['ประเภทข้อมูล'],oldItemId:row['item_id']});toast('บันทึกสำเร็จ');await loadPage(pg,true)}catch(e){toast(e.message,true);hideOv()}}
async function delRow(i){const row=pageData[i];if(!confirm(`ลบรายการ?\nเอกสาร: ${row['หมายเลขเอกสาร']}\nรายการ: ${row['รายการ']}`))return;showOv();try{await API.del('/api/transaction/'+encodeURIComponent(row.transaction_id));toast('ลบเรียบร้อย');await loadPage(pg,true)}catch(e){toast(e.message,true);hideOv()}}

// ── Init ──
loadPage(1,true);
</script>
</body>
</html>
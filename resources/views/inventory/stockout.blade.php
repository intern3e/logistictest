<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ขายสินค้าออก - 3E TRADING</title>
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
    #content{max-width:680px;margin:0 auto;padding:68px 16px 40px}
    .page-title{font-size:20px;font-weight:700;color:#1a1a6e;margin-bottom:16px;padding-bottom:6px;border-bottom:2px solid #a8c3e0}
    .form-group{margin-bottom:14px}
    .form-label{display:block;font-size:14px;font-weight:600;color:#333;margin-bottom:3px}
    .form-sublabel{display:block;font-size:12px;color:#666;margin-bottom:3px;line-height:1.5}
    .required-mark{color:#dc3545;margin-left:3px}
    .form-input,.form-textarea{width:100%;font-size:14px;padding:6px 8px;border:2px inset #aaa;background:#fff;color:#000;font-family:'Sarabun',Arial,sans-serif;outline:none}
    .form-input:focus,.form-textarea:focus{border-color:#2358a4}
    .form-input:read-only{background:#e9e9e9;color:#555;cursor:not-allowed}
    .form-textarea{resize:vertical;min-height:80px}
    .suggestions{position:absolute;background:#fff;border:2px solid #2358a4;max-height:240px;overflow-y:auto;width:100%;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.25);margin-top:1px;display:none}
    .suggestions div{padding:8px 10px;font-size:13px;cursor:pointer;border-bottom:1px solid #e0e0e0;color:#222}
    .suggestions div:last-child{border-bottom:none}
    .suggestions div:hover{background:#2358a4;color:#fff}
    .btn-submit{width:100%;font-size:16px;padding:9px;margin-top:18px;background:#388E3C;border:1px solid #2e7d32;color:#fff;font-weight:700;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .btn-submit:hover:not(:disabled){background:#2e7d32}
    .btn-submit:disabled{opacity:.5;cursor:not-allowed}
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
<div class="ov" id="ov"><div><div class="sp"></div><p>กำลังโหลด...</p></div></div>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    @if(str_contains($authUser['page'] ?? '', 'pr'))
      <a class="sb-item" target="_blank" href="{{ route('inventory.pr', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>สร้างใบขอซื้อ</a>
    @endif
    <div style="height:1px;background:#a8c3e0;margin:5px 12px"></div>
    <div class="sb-sec">ดำเนินการ</div>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.stockout', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>ขายสินค้าออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.withdraw', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>เบิกของ</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
  <a href="http://server_update:8000/solist" button  type="submit" class="btn-home">🚪 หน้าหลัก</a>
</div>

<div id="content">
  <div class="page-title">ขายสินค้าออก</div>
  <div class="form-group">
    <label class="form-label">ชื่อผู้ทำรายการ:<span class="required-mark">*</span></label>
    <input type="text" id="addedBy" class="form-input" value="{{ $authUser['name'] ?? '' }}" readonly>
  </div>
  <div class="form-group">
    <label class="form-label">เลขใบสั่งขายสินค้า (SO):<span class="required-mark">*</span></label>
    <input type="text" id="soNumber" class="form-input" placeholder="กรอกเลข SO">
  </div>
  <div class="form-group">
    <label class="form-label">รหัสสินค้า:<span class="required-mark">*</span></label>
    <input type="text" id="iditem" class="form-input" readonly>
  </div>
  <div class="form-group" style="position:relative">
    <label class="form-label">ชื่อสินค้า:<span class="required-mark">*</span></label>
    <input type="text" id="productName" class="form-input" placeholder="พิมพ์ค้นหาชื่อสินค้า..." autocomplete="off">
    <div id="sug" class="suggestions"></div>
  </div>
  <div class="form-group">
    <label class="form-label">ยี่ห้อ:<span class="required-mark">*</span></label>
    <input type="text" id="brand" class="form-input" placeholder="กรอกยี่ห้อ">
  </div>
  <div class="form-group">
    <label class="form-label">จำนวน:<span class="required-mark">*</span></label>
    <span class="form-sublabel">* กรอกเป็นตัวเลขเท่านั้น (ห้ามใส่หน่วย)</span>
    <span class="form-sublabel">* กรณีของเป็นลังหรือกล่อง ให้ใส่เป็นจำนวน เช่น 1 ลัง มี 12 ชิ้น ให้ใส่ 12</span>
    <input type="text" id="quantity" class="form-input" placeholder="กรอกจำนวน">
  </div>
  <input type="hidden" id="typeitem">
  <div class="form-group">
    <label class="form-label">ชั้นวาง:<span class="required-mark">*</span></label>
    <input type="text" id="location" class="form-input" readonly>
  </div>
  <div class="form-group">
    <label class="form-label">JOB Detail:</label>
    <textarea id="note" class="form-textarea" placeholder="กรอกหมายเหตุ (ถ้ามี)" rows="3"></textarea>
  </div>
  <button type="button" class="btn-submit" id="submitBtn" onclick="submitForm()">บันทึกข้อมูล</button>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
function showOv(){document.getElementById('ov').classList.add('on')}
function hideOv(){document.getElementById('ov').classList.remove('on')}
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}

// ── โหลดรายการสินค้าสำหรับ autocomplete ──
let allProducts=[];
(async()=>{try{const r=await(await fetch('/api/items/pagedata')).json();allProducts=r.items||[]}catch(e){}})();

// ── fuzzy match (port จาก GAS) ──
function lev(a,b){const dp=Array.from({length:a.length+1},(_,i)=>Array(b.length+1).fill(0));for(let i=0;i<=a.length;i++)dp[i][0]=i;for(let j=0;j<=b.length;j++)dp[0][j]=j;for(let i=1;i<=a.length;i++)for(let j=1;j<=b.length;j++){const c=a[i-1]===b[j-1]?0:1;dp[i][j]=Math.min(dp[i-1][j]+1,dp[i][j-1]+1,dp[i-1][j-1]+c)}return dp[a.length][b.length]}
function sim(a,b){if(!a||!b)return 0;return 1-lev(a.toLowerCase(),b.toLowerCase())/Math.max(a.length,b.length)}

const inp=document.getElementById('productName'),sug=document.getElementById('sug');
inp.addEventListener('input',function(){
  const q=this.value.trim();if(!q){sug.style.display='none';return}
  const lq=q.toLowerCase();
  const results=allProducts
    .filter(it=>{const qty=parseFloat(it.quantity);return!isNaN(qty)&&qty>0})
    .map(it=>({...it,score:sim(lq,(it.name||'').trim().toLowerCase())}))
    .filter(it=>{const n=(it.name||'').toLowerCase();return it.score>=0.6||n.includes(lq)||n.startsWith(lq)})
    .sort((a,b)=>b.score-a.score).slice(0,10);
  renderSug(results);
  ['iditem','brand','location','typeitem'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('brand').readOnly=false;
});
function renderSug(results){
  sug.innerHTML='';
  if(!results.length){const d=document.createElement('div');d.textContent='ไม่พบชื่อสินค้า';sug.appendChild(d);sug.style.display='block';return}
  results.forEach(it=>{
    const d=document.createElement('div');
    d.textContent=`${it.iditem} | ${it.name} | ยี่ห้อ: ${it.brand||'-'} | คงเหลือ: ${it.quantity} | ${it.location||'-'}`;
    d.onclick=()=>{
      inp.value=it.name;
      document.getElementById('iditem').value=it.iditem;
      document.getElementById('brand').value=it.brand||'';
      document.getElementById('brand').readOnly=!!(it.brand&&it.brand.trim());
      document.getElementById('location').value=it.location||'';
      document.getElementById('typeitem').value=it.typeitem||'';
      sug.style.display='none';
    };
    sug.appendChild(d);
  });
  sug.style.display='block';
}
document.addEventListener('click',e=>{if(!sug.contains(e.target)&&e.target!==inp)sug.style.display='none'});

async function submitForm(){
  const addedBy=document.getElementById('addedBy').value.trim();
  const soNumber=document.getElementById('soNumber').value.trim();
  const iditem=document.getElementById('iditem').value.trim();
  const productName=document.getElementById('productName').value.trim();
  const brand=document.getElementById('brand').value.trim();
  const quantity=document.getElementById('quantity').value.trim();
  const location=document.getElementById('location').value.trim();
  const note=document.getElementById('note').value.trim();
  if(!soNumber||!iditem||!productName||!brand||!quantity||!location){alert('กรุณากรอกข้อมูลให้ครบถ้วน (ต้องเลือกสินค้าจากรายการค้นหา)');return}
  if(isNaN(parseFloat(quantity))||parseFloat(quantity)<=0){alert('จำนวนต้องเป็นตัวเลขมากกว่า 0');return}
  const btn=document.getElementById('submitBtn');
  btn.disabled=true;btn.textContent='กำลังบันทึก...';showOv();
  try{
    const res=await(await fetch('/api/stockout',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify({addedBy,soNumber,iditem,quantity,note})
    })).json();
    if(res.success){
      alert('บันทึกเรียบร้อย!');
      ['soNumber','iditem','productName','brand','quantity','location','typeitem','note'].forEach(id=>document.getElementById(id).value='');
      document.getElementById('brand').readOnly=false;
    }else alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));
  }catch(err){alert('เกิดข้อผิดพลาด: '+err.message)}
  btn.disabled=false;btn.textContent='บันทึกข้อมูล';hideOv();
}
</script>
</body>
</html>
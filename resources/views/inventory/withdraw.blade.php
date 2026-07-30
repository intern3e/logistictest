<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>เบิกของ - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f1f5f9;min-height:100vh;padding-bottom:40px}
    
    /* Overlay */
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.6);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(2px)}
    .ov.on{opacity:1;visibility:visible}
    .sp{width:40px;height:40px;border:4px solid rgba(255,255,255,.3);border-top:4px solid #3b82f6;border-radius:50%;animation:sp 1s linear infinite;margin:0 auto 12px}
    @keyframes sp{to{transform:rotate(360deg)}}
    .ov p{color:#fff;font-size:15px;font-weight:500}

    /* Topbar */
    .topbar{height:56px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 16px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:32px}
    .topbar-title{font-size:16px;font-weight:700;color:#1e293b;flex:1}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .topbar-name{font-size:13px;color:#64748b;font-weight:500}
    .topbar-badge{font-size:11px;padding:3px 10px;font-weight:700;color:#1e40af;background:#dbeafe;border-radius:99px}
    .hamburger{background:none;border:none;cursor:pointer;padding:6px;display:flex;flex-direction:column;gap:4px;flex-shrink:0;border-radius:4px}
    .hamburger:hover{background:#f1f5f9}
    .hamburger span{display:block;width:20px;height:2px;background:#475569;border-radius:1px}
    
    /* Sidebar */
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-260px;width:240px;height:100vh;z-index:1600;transition:left .25s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e2e8f0;box-shadow:4px 0 12px rgba(0,0,0,.05)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;min-height:56px}
    .sb-head img{height:28px}
    .sb-head span{font-size:15px;font-weight:700;color:#1e293b;flex:1}
    .sb-close{background:none;border:none;color:#64748b;cursor:pointer;font-size:18px;font-weight:bold;padding:4px;border-radius:4px}
    .sb-close:hover{background:#e2e8f0;color:#1e293b}
    .sb-nav{flex:1;overflow-y:auto;padding:8px 0}
    .sb-sec{padding:8px 16px 4px;font-size:11px;font-weight:700;color:#64748b;letter-spacing:.5px;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:10px;padding:9px 16px;color:#334155;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none;transition:all .15s}
    .sb-item:hover{background:#f1f5f9;color:#1e293b}
    .sb-item.cur{background:#eff6ff;border-left-color:#3b82f6;color:#2563eb;font-weight:600}
    .sb-item svg{width:16px;height:16px;flex-shrink:0}

    /* Content */
    #content{max-width:600px;margin:0 auto;padding:72px 20px 40px}
    .page-title{font-size:18px;font-weight:700;color:#1e293b;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid #e2e8f0}
    
    /* Form Container - White Background Box */
    .form-container{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .form-group{margin-bottom:16px}
    .form-label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
    .form-sublabel{display:block;font-size:11px;color:#64748b;margin-bottom:4px;line-height:1.4}
    .required-mark{color:#ef4444;margin-left:2px}
    .form-input,.form-textarea{width:100%;font-size:14px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;color:#0f172a;font-family:'Sarabun',Arial,sans-serif;outline:none;transition:border-color .15s,box-shadow .15s}
    .form-input:focus,.form-textarea:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.1);background:#fff}
    .form-input:read-only{background:#f1f5f9;color:#64748b;cursor:not-allowed;border-color:#e2e8f0}
    .form-textarea{resize:vertical;min-height:80px}
    
    /* Suggestions */
    .suggestions{position:absolute;background:#fff;border:1px solid #e2e8f0;border-radius:6px;max-height:200px;overflow-y:auto;width:100%;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.1);margin-top:4px;display:none}
    .suggestions div{padding:8px 10px;font-size:13px;cursor:pointer;border-bottom:1px solid #f1f5f9;color:#334155}
    .suggestions div:last-child{border-bottom:none}
    .suggestions div:hover{background:#eff6ff;color:#2563eb}
    
    /* Buttons */
    .btn-submit{width:100%;font-size:15px;padding:12px;margin-top:20px;background:#3E6AE1;border:none;border-radius:6px;color:#fff;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif;transition:background .15s}
    .btn-submit:hover:not(:disabled){background:rgb(19, 130, 240)}
    .btn-submit:disabled{opacity:.6;cursor:not-allowed}
    
    .btn-home{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:6px 14px;
      font-family:'Sarabun',sans-serif;
      font-size:13px;
      font-weight:600;
      color:#fff;
      text-decoration:none;
      cursor:pointer;
      white-space:nowrap;
      border:1px solid #991b1b;
      background:linear-gradient(180deg,#ef4444 0%,#dc2626 50%,#b91c1c 100%);
      border-radius:6px;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.2),0 1px 2px rgba(0,0,0,.1);
      transition:all .15s;
    }
    .btn-home:hover{
      background:linear-gradient(180deg,#f87171 0%,#ef4444 50%,#dc2626 100%);
    }
    .btn-home:active{
      box-shadow:inset 0 2px 4px rgba(0,0,0,.2);
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
    <div style="height:1px;background:#e2e8f0;margin:5px 16px"></div>
    <div class="sb-sec">ดำเนินการ</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.stockout', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>ขายสินค้าออก</a>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.withdraw', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>เบิกของ</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
  <a href="http://server_update:8000/solist" button type="submit" class="btn-home">🚪 หน้าหลัก</a>
</div>

<div id="content">
  <div class="page-title">เบิกของ</div>
  
  <div class="form-container">
    <div class="form-group">
      <label class="form-label">ชื่อผู้ทำรายการ:<span class="required-mark">*</span></label>
      <input type="text" id="addedBy" class="form-input" value="{{ $authUser['name'] ?? '' }}" readonly>
    </div>
    <div class="form-group">
      <label class="form-label">ชื่อผู้เบิก:<span class="required-mark">*</span></label>
      <input type="text" id="namewith" class="form-input" placeholder="กรอกชื่อผู้เบิก">
    </div>
    <div class="form-group">
      <label class="form-label">เบอร์ติดต่อ:<span class="required-mark">*</span></label>
      <input type="text" id="telwith" class="form-input" placeholder="กรอกเบอร์ติดต่อ">
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
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
function showOv(){document.getElementById('ov').classList.add('on')}
function hideOv(){document.getElementById('ov').classList.remove('on')}
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}

let allProducts=[];
(async()=>{try{const r=await(await fetch('/api/items/pagedata')).json();allProducts=r.items||[]}catch(e){}})();

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
  const namewith=document.getElementById('namewith').value.trim();
  const telwith=document.getElementById('telwith').value.trim();
  const iditem=document.getElementById('iditem').value.trim();
  const productName=document.getElementById('productName').value.trim();
  const brand=document.getElementById('brand').value.trim();
  const quantity=document.getElementById('quantity').value.trim();
  const location=document.getElementById('location').value.trim();
  const note=document.getElementById('note').value.trim();
  if(!namewith||!telwith||!iditem||!productName||!brand||!quantity||!location){alert('กรุณากรอกข้อมูลให้ครบถ้วน (ต้องเลือกสินค้าจากรายการค้นหา)');return}
  if(isNaN(parseFloat(quantity))||parseFloat(quantity)<=0){alert('จำนวนต้องเป็นตัวเลขมากกว่า 0');return}
  const btn=document.getElementById('submitBtn');
  btn.disabled=true;btn.textContent='กำลังบันทึก...';showOv();
  try{
    const res=await(await fetch('/api/withdraw',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify({addedBy,namewith,telwith,iditem,quantity,note})
    })).json();
    if(res.success){
      alert('บันทึกเรียบร้อย!');
      ['namewith','telwith','iditem','productName','brand','quantity','location','typeitem','note'].forEach(id=>document.getElementById(id).value='');
      document.getElementById('brand').readOnly=false;
    }else alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));
  }catch(err){alert('เกิดข้อผิดพลาด: '+err.message)}
  btn.disabled=false;btn.textContent='บันทึกข้อมูล';hideOv();
}
</script>
</body>
</html>
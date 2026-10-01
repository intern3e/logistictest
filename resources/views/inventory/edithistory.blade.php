<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ประวัติการแก้ไข - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}
    .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 24px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:36px;border-radius:6px}
    .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
    .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
    .btn-home{font-size:13px;font-weight:600;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;text-decoration:none;transition:all .2s}
    .btn-home:hover{background:#f9fafb;border-color:#9ca3af}
    .hamburger{background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;display:flex;flex-direction:column;gap:5px;flex-shrink:0}
    .hamburger span{display:block;width:20px;height:2px;background:#374151}
    .hamburger:hover{background:#f3f4f6}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-280px;width:260px;height:100vh;z-index:1600;transition:left .3s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:4px 0 24px rgba(0,0,0,.08)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #e5e7eb;min-height:64px}
    .sb-head img{height:32px;border-radius:6px}
    .sb-head span{font-size:18px;font-weight:700;color:#111827;flex:1}
    .sb-close{background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;font-weight:bold;padding:4px 8px;border-radius:6px}
    .sb-close:hover{background:#f3f4f6;color:#111827}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 0}
    .sb-sec{padding:12px 20px 6px;font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.05em;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#374151;font-size:14px;font-weight:500;border-left:3px solid transparent;text-decoration:none;transition:all .2s;border-radius:0 8px 8px 0;margin-right:8px}
    .sb-item:hover{background:#f9fafb;border-left-color:#5B65F3;color:#111827}
    .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#5B65F3;font-weight:600}

    #content{padding:88px 16px 24px;width:100%;max-width:1500px;margin:0 auto}
    .card{margin-bottom:20px;padding:20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .card h2{font-size:20px;font-weight:700;color:#111827;margin-bottom:16px}
    .abar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
    .abar input,.abar select{padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;color:#111827}
    .abar input:focus,.abar select:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .btn{padding:10px 18px;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;border:none;border-radius:8px;transition:all .2s}
    .btn-primary{background:#5B65F3;color:#fff}.btn-primary:hover{background:#4F46E5}
    .btn-clr{background:#fff;color:#374151;border:1px solid #d1d5db}.btn-clr:hover{background:#f9fafb}
    .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:#f9fafb}
    th{color:#374151;padding:12px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;border:1px solid #e5e7eb;border-top:none}
    th:first-child{border-left:none} th:last-child{border-right:none}
    td{padding:10px 12px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:top}
    td:first-child{border-left:none} td:last-child{border-right:none}
    tbody tr:hover{background:#f9fafb}
    .badge{display:inline-block;padding:3px 9px;font-size:12px;font-weight:600;border-radius:6px;white-space:nowrap}
    .b-update{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
    .b-delete{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .b-item{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-tx{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .chg{font-size:13px;line-height:1.7}
    .chg .fl{color:#6b7280;font-weight:600}
    .chg .fr{color:#991b1b;text-decoration:line-through;margin:0 2px}
    .chg .to{color:#065f46;font-weight:700;margin-left:2px}
    .reason{color:#b45309;font-weight:600}
    .muted{color:#9ca3af}
    .mono{font-family:ui-monospace,monospace;font-size:13px}
    .count-label{font-size:14px;color:#6b7280;font-weight:500;margin-bottom:10px;display:block}
    @media(max-width:768px){.abar input,.abar select{flex:1 1 100%}}
  </style>
</head>
<body>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <div class="sb-sec">รายงาน</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.analyze') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>วิเคราะห์สินค้า</a>
    <a class="sb-item cur"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>ประวัติการแก้ไข</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name"> ผู้ใช้: {{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
    <a href="{{ route('inventory.item') }}" class="btn-home">กลับคลัง</a>
  </div>
</div>

<div id="content">
  <div class="card">
    <h2>ประวัติการแก้ไข / ลบ</h2>
    <div class="abar">
      <select id="fAction"><option value="">ทุกการกระทำ</option><option value="update">แก้ไข</option><option value="delete">ลบ</option></select>
      <select id="fTarget"><option value="">ทุกประเภท</option><option value="item">สินค้า</option><option value="transaction">รายการเข้า-ออก</option></select>
      <input type="date" id="fDate">
      <input type="text" id="fQ" placeholder="ค้นหา รหัส/ชื่อ/ผู้แก้/เหตุผล..." style="flex:1;min-width:200px">
      <button class="btn btn-primary" onclick="search()">ค้นหา</button>
      <button class="btn btn-clr" onclick="clearF()">ล้าง</button>
    </div>
  </div>

  <span class="count-label" id="countLabel"></span>
  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th style="width:150px;">เวลา</th>
        <th style="width:80px;">การกระทำ</th>
        <th style="width:90px;">ประเภท</th>
        <th style="width:150px;">รหัส</th>
        <th>ชื่อ / รายการ</th>
        <th>รายละเอียดการแก้ไข</th>
        <th>เหตุผล (ลบ)</th>
        <th style="width:140px;">ผู้แก้ไข</th>
      </tr></thead>
      <tbody id="tb"><tr><td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">กำลังโหลด...</td></tr></tbody>
    </table>
  </div>
</div>

<script>
const DATA_URL = "{{ url('/api/inventory/edit-history') }}";
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open');}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open');}
function esc(s){return String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}

async function search(){
  const p=new URLSearchParams();
  const a=document.getElementById('fAction').value; if(a)p.set('action',a);
  const t=document.getElementById('fTarget').value; if(t)p.set('target_type',t);
  const d=document.getElementById('fDate').value; if(d)p.set('date',d);
  const q=document.getElementById('fQ').value.trim(); if(q)p.set('q',q);
  const tb=document.getElementById('tb');
  tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">กำลังโหลด...</td></tr>';
  try{
    const res=await fetch(DATA_URL+'?'+p.toString(),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    const data=await res.json();
    if(!res.ok||!data.ok){ tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:#ef4444;">โหลดไม่สำเร็จ</td></tr>'; return; }
    render(data.rows||[]);
  }catch(e){ tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:#ef4444;">เกิดข้อผิดพลาด</td></tr>'; }
}
function clearF(){document.getElementById('fAction').value='';document.getElementById('fTarget').value='';document.getElementById('fDate').value='';document.getElementById('fQ').value='';search();}

function render(rows){
  document.getElementById('countLabel').textContent=rows.length+' รายการ';
  const tb=document.getElementById('tb');
  if(!rows.length){tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">ไม่พบประวัติ</td></tr>';return;}
  tb.innerHTML=rows.map(r=>{
    const act=r.action==='delete'?'<span class="badge b-delete">ลบ</span>':'<span class="badge b-update">แก้ไข</span>';
    const typ=r.target_type==='transaction'?'<span class="badge b-tx">เข้า-ออก</span>':'<span class="badge b-item">สินค้า</span>';
    let detail='';
    if(r.action==='delete'){
      detail='<span class="muted">— ลบรายการ —</span>'+(r.qty_from!=null?` <span class="muted">(จำนวนเดิม ${esc(r.qty_from)})</span>`:'');
    } else if(Array.isArray(r.changes)&&r.changes.length){
      detail='<div class="chg">'+r.changes.map(c=>`<div><span class="fl">${esc(c.label||c.field)}:</span> <span class="fr">${esc(c.from||'(ว่าง)')}</span> &rarr; <span class="to">${esc(c.to||'(ว่าง)')}</span></div>`).join('')+'</div>';
    } else {
      detail='<span class="muted">—</span>';
    }
    const reason=r.reason?`<span class="reason">${esc(r.reason)}</span>`:'<span class="muted">—</span>';
    const by=esc(r.edited_by||'-')+(r.role?` <span class="muted">(${esc(r.role)})</span>`:'');
    return `<tr>
      <td class="mono" style="white-space:nowrap;">${esc(r.created_at)}</td>
      <td>${act}</td>
      <td>${typ}</td>
      <td class="mono">${esc(r.target_id||'-')}</td>
      <td>${esc(r.target_name||'-')}</td>
      <td>${detail}</td>
      <td>${reason}</td>
      <td>${by}</td>
    </tr>`;
  }).join('');
}

document.getElementById('fQ').addEventListener('keydown',e=>{if(e.key==='Enter')search();});
search();
</script>
</body>
</html>

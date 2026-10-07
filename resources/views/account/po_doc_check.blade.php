{{-- resources/views/account/po_doc_check.blade.php — สรุปเอกสาร PO (มี/ไม่มีเอกสาร) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>สรุปเอกสาร PO — บัญชี</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box}
:root{--ink:#1f2937;--ink2:#64748b;--line:#e5e7eb;--bg:#f1f5f9;--card:#fff;--primary:#3E6AE1;--green:#16a34a;--green-soft:#dcfce7;--red:#dc2626;--red-soft:#fee2e2;--amber:#d97706}
body{margin:0;font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--ink);font-size:14px}
.wrap{max-width:1180px;margin:0 auto;padding:18px 16px 60px}
h1{font-size:20px;margin:0 0 2px}
.sub{color:var(--ink2);font-size:12.5px;margin-bottom:16px}
.panel{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
.row{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.fld{display:flex;flex-direction:column;gap:4px}
.fld label{font-size:12px;font-weight:600;color:var(--ink2)}
input[type=text]{padding:9px 11px;border:1px solid #dee2e6;border-radius:9px;font-family:inherit;font-size:14px}
.btn{padding:9px 16px;border:1px solid var(--line);background:#fff;border-radius:9px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:16px}
.card{background:var(--card);border:1px solid var(--line);border-radius:12px;padding:12px 14px}
.card .k{font-size:12px;color:var(--ink2);font-weight:600}
.card .v{font-size:24px;font-weight:700;margin-top:2px}
.card.good .v{color:var(--green)}.card.bad .v{color:var(--red)}
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.tab{padding:7px 14px;border:1px solid var(--line);background:#fff;border-radius:999px;cursor:pointer;font-size:13px;font-weight:600;color:var(--ink2)}
.tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.table-wrap{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 14px;text-align:left;border-bottom:1px solid var(--line);font-size:14px;vertical-align:top}
th{background:#f8fafc;font-weight:700;color:var(--ink2)}
.mono{font-family:'JetBrains Mono',monospace}
.empty{padding:40px;text-align:center;color:var(--ink2)}
.badge{display:inline-block;padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:700;margin:1px}
.b-has{background:var(--green-soft);color:var(--green)}
.b-none{background:var(--red-soft);color:var(--red)}
.b-doc{background:#eef2ff;color:#4338ca}
.src{font-size:12px;color:var(--ink2)}
.toast-wrap{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:9999}
.toast{background:#111827;color:#fff;padding:10px 16px;border-radius:10px;font-size:13px}
.toast.err{background:var(--red)}
.spin{display:inline-block;width:14px;height:14px;border:2px solid var(--primary);border-top-color:transparent;border-radius:50%;animation:sp .7s linear infinite}
@keyframes sp{to{transform:rotate(360deg)}}
</style>
</head>
<body>
<div class="wrap">
  <h1>สรุปเอกสาร PO</h1>
  <div class="sub">เช็กว่า PO ไหนมีเอกสาร / ไม่มีเอกสาร และได้เอกสารชนิดใดบ้าง (ใบกำกับภาษี / ใบเสร็จ / ใบส่งของ) · ติ๊กจากหน้า mobile_app · ผู้ใช้: {{ $loginName }}</div>

  <div class="panel">
    <div class="row">
      <div class="fld" style="flex:1;min-width:220px">
        <label>ค้นหา (PO / SO / ผู้ขาย)</label>
        <input type="text" id="fQ" placeholder="พิมพ์เพื่อค้นหา...">
      </div>
      <button class="btn btn-primary" id="btnReload">โหลดใหม่</button>
    </div>
  </div>

  <div class="cards" id="cards"></div>

  <div class="tabs" id="statusTabs">
    <div class="tab active" data-status="">ทั้งหมด</div>
    <div class="tab" data-status="has">มีเอกสาร</div>
    <div class="tab" data-status="none">ไม่มีเอกสาร</div>
  </div>
  <div class="tabs" id="docTabs">
    <div class="tab active" data-doc="">ทุกชนิด</div>
    <div class="tab" data-doc="tax">ใบกำกับภาษี</div>
    <div class="tab" data-doc="receipt">ใบเสร็จ</div>
    <div class="tab" data-doc="delivery">ใบส่งของ</div>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr>
        <th style="width:150px">PO / SO</th>
        <th>ผู้ขาย / ลูกค้า</th>
        <th style="width:120px">มีเอกสาร</th>
        <th>ชนิดเอกสาร</th>
        <th style="width:160px">ตรวจโดย</th>
      </tr></thead>
      <tbody id="tbody"><tr><td colspan="5" class="empty">กำลังโหลด...</td></tr></tbody>
    </table>
  </div>
</div>
<div class="toast-wrap" id="toastWrap"></div>

<script>
const URL_DATA = "{{ route('podoccheck.data') }}";
const $ = id => document.getElementById(id);
let curStatus='', curDoc='';
function esc(s){return (s==null?'':String(s)).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function toast(m,t){const w=$('toastWrap');const d=document.createElement('div');d.className='toast '+(t||'');d.textContent=m;w.appendChild(d);setTimeout(()=>d.remove(),2600);}

function renderCards(s){
  if(!s){$('cards').innerHTML='';return;}
  $('cards').innerHTML =
    `<div class="card"><div class="k">PO ทั้งหมด</div><div class="v">${s.total}</div></div>`+
    `<div class="card good"><div class="k">มีเอกสาร</div><div class="v">${s.has}</div></div>`+
    `<div class="card bad"><div class="k">ไม่มีเอกสาร</div><div class="v">${s.none}</div></div>`+
    `<div class="card"><div class="k">ใบกำกับภาษี</div><div class="v" style="font-size:20px">${s.tax_invoice}</div></div>`+
    `<div class="card"><div class="k">ใบเสร็จ</div><div class="v" style="font-size:20px">${s.receipt}</div></div>`+
    `<div class="card"><div class="k">ใบส่งของ</div><div class="v" style="font-size:20px">${s.delivery}</div></div>`;
}
const DOC_LABEL = { tax:'ใบกำกับภาษี', receipt:'ใบเสร็จ', delivery:'ใบส่งของ' };
function docBadges(r){
  const types = r.doc_types || [];
  const h = types.map(t => '<span class="badge b-doc">'+esc(DOC_LABEL[t]||t)+'</span>').join('');
  return h || '<span class="src">-</span>';
}
function render(rows){
  const tb=$('tbody');
  if(!rows||!rows.length){tb.innerHTML='<tr><td colspan="5" class="empty">ไม่มีรายการ</td></tr>';return;}
  tb.innerHTML = rows.map(r=>`<tr>
    <td class="mono"><b>${esc(r.po_id)}</b>${r.so_id?`<div class="src">SO ${esc(r.so_id)}</div>`:''}</td>
    <td>${esc(r.vendor_name||'-')}${r.customer_name?`<div class="src">${esc(r.customer_name)}</div>`:''}</td>
    <td>${r.has_document?'<span class="badge b-has">มีเอกสาร</span>':'<span class="badge b-none">ไม่มีเอกสาร</span>'}</td>
    <td>${docBadges(r)}</td>
    <td>${r.checked_by?esc(r.checked_by):'-'}${r.checked_at?`<div class="src">${esc(r.checked_at)}</div>`:''}</td>
  </tr>`).join('');
}
async function load(){
  const tb=$('tbody'); tb.innerHTML='<tr><td colspan="5" class="empty"><span class="spin"></span> กำลังโหลด...</td></tr>';
  try{
    const u=new URL(URL_DATA, location.origin);
    u.searchParams.set('status',curStatus); u.searchParams.set('doc',curDoc); u.searchParams.set('q',$('fQ').value.trim());
    const r=await fetch(u,{headers:{'Accept':'application/json'}});
    const d=await r.json();
    if(!r.ok||!d.ok) throw new Error(d.message||('HTTP '+r.status));
    renderCards(d.summary); render(d.rows);
  }catch(e){ tb.innerHTML=`<tr><td colspan="5" class="empty" style="color:#dc2626">❌ ${esc(e.message)}</td></tr>`; }
}
$('btnReload').addEventListener('click', load);
let qT=null; $('fQ').addEventListener('input',()=>{clearTimeout(qT);qT=setTimeout(load,350);});
document.querySelectorAll('#statusTabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('#statusTabs .tab').forEach(x=>x.classList.remove('active'));
  t.classList.add('active'); curStatus=t.dataset.status; load();
}));
document.querySelectorAll('#docTabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('#docTabs .tab').forEach(x=>x.classList.remove('active'));
  t.classList.add('active'); curDoc=t.dataset.doc; load();
}));
document.addEventListener('DOMContentLoaded', load);
</script>
</body>
</html>

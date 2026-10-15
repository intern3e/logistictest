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
/* ── ส่วนอัปโหลด+OCR ── */
.btn-sm{padding:5px 10px;font-size:12.5px;border-radius:7px}
.btn-green{background:var(--green);color:#fff;border-color:var(--green)}
.btn-red{background:#fff;color:var(--red);border-color:#f3c0c0}
.btn-primary:disabled{opacity:.5;cursor:not-allowed}
.drop{border:2px dashed #cbd5e1;border-radius:12px;padding:20px;text-align:center;color:var(--ink2)}
.drop.hl{border-color:var(--primary);background:#f5f8ff}
.progress{height:10px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-top:10px;display:none}
.progress .bar{height:100%;width:0;background:var(--primary);transition:width .2s}
.pill{display:inline-block;padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:700}
.pill.ok{background:var(--green-soft);color:var(--green)}
.pill.warn{background:#fef3c7;color:var(--amber)}
.pill.err{background:var(--red-soft);color:var(--red)}
.pill.none{background:#eef2f7;color:var(--ink2)}
.num{font-variant-numeric:tabular-nums;text-align:right}
.assign-row{display:flex;gap:6px;align-items:center;margin-top:4px}
.assign-row input{width:120px;padding:5px 8px;border:1px solid #dee2e6;border-radius:7px;font-family:inherit;font-size:12.5px}
.overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);display:none;align-items:center;justify-content:center;z-index:50;padding:16px}
.modal{background:#fff;border-radius:14px;max-width:760px;width:100%;max-height:88vh;overflow:auto;padding:18px}
.modal h3{margin:0 0 10px}
.close{float:right;cursor:pointer;font-size:20px;color:var(--ink2);border:none;background:none}
pre{background:#0f172a;color:#e2e8f0;padding:14px;border-radius:10px;white-space:pre-wrap;word-break:break-word;font-family:'JetBrains Mono',monospace;font-size:12.5px;line-height:1.6}
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

  {{-- ── อัปโหลดเอกสารสแกน (OCR) ── --}}
  <div class="panel">
    <div style="font-weight:700;margin-bottom:8px">อัปโหลดเอกสาร PO จากซัพ (สแกน PDF) — OCR &amp; จับคู่ PO อัตโนมัติ</div>
    <div class="drop" id="drop">
      <div style="font-size:14px;margin-bottom:8px">เลือกโฟลเดอร์ที่มี PDF สแกน หรือ ลากไฟล์มาวาง</div>
      <div class="row" style="justify-content:center">
        <input type="file" id="folderInput" webkitdirectory directory multiple accept="application/pdf" style="display:none">
        <input type="file" id="fileInput" multiple accept="application/pdf" style="display:none">
        <button class="btn" type="button" onclick="document.getElementById('folderInput').click()">📁 เลือกโฟลเดอร์</button>
        <button class="btn" type="button" onclick="document.getElementById('fileInput').click()">📄 เลือกไฟล์</button>
        <span id="pickInfo" class="src"></span>
        <button id="btnUpload" class="btn btn-primary" type="button" disabled onclick="startUpload()">อัปโหลด &amp; OCR</button>
      </div>
      <div class="progress" id="progress"><div class="bar" id="bar"></div></div>
      <div id="progText" class="src" style="margin-top:6px"></div>
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
    <div class="tab" data-doc="tax">ใบกำกับภาษี/ใบเสร็จ</div>
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

  {{-- ── ตารางเอกสารสแกนที่ OCR แล้ว ── --}}
  <h1 style="font-size:17px;margin:26px 0 2px">เอกสารสแกนที่ OCR แล้ว</h1>
  <div class="sub">ไฟล์ที่อัปโหลด + ข้อมูลที่ OCR อ่านได้ + การจับคู่ PO (แก้/ยืนยันเองได้)</div>
  <div class="tabs" id="scanTabs">
    <div class="tab active" data-m="">ทั้งหมด</div>
    <div class="tab" data-m="unmatched">ยังไม่จับคู่</div>
    <div class="tab" data-m="auto">จับคู่อัตโนมัติ</div>
    <div class="tab" data-m="manual">จับคู่เอง</div>
  </div>
  <div class="table-wrap" style="overflow:auto">
    <table>
      <thead><tr>
        <th>ไฟล์</th>
        <th>ซัพ (OCR)</th>
        <th style="width:220px">เลข PO / invoice (ซัพ)</th>
        <th style="width:120px" class="num">ยอดรวม VAT</th>
        <th style="width:90px">OCR</th>
        <th style="width:220px">จับคู่ PO</th>
        <th style="width:180px">จัดการ</th>
      </tr></thead>
      <tbody id="scanBody"><tr><td colspan="7" class="empty">— ยังไม่มีไฟล์ —</td></tr></tbody>
    </table>
  </div>
</div>

{{-- modal ดู txt --}}
<div class="overlay" id="txtOverlay">
  <div class="modal">
    <button class="close" onclick="closeTxt()">&times;</button>
    <h3 id="txtTitle">ผล OCR</h3>
    <pre id="txtBody">...</pre>
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
    `<div class="card"><div class="k">ใบกำกับภาษี/ใบเสร็จ</div><div class="v" style="font-size:20px">${s.tax_invoice}</div></div>`+
    `<div class="card"><div class="k">ใบส่งของ</div><div class="v" style="font-size:20px">${s.delivery}</div></div>`;
}
const DOC_LABEL = { tax:'ใบกำกับภาษี', receipt:'ใบเสร็จ', delivery:'ใบส่งของ' };
function docBadges(r){
  let types = (r.doc_types || []).slice();
  // ใบกำกับภาษี + ใบเสร็จ รวมเป็นชนิดเดียว
  if(types.includes('tax') || types.includes('receipt')){
    types = types.filter(t=>t!=='tax' && t!=='receipt');
    types.unshift('taxreceipt');
  }
  const L = { taxreceipt:'ใบกำกับภาษี/ใบเสร็จ', delivery:'ใบส่งของ' };
  const h = types.map(t => '<span class="badge b-doc">'+esc(L[t]||DOC_LABEL[t]||t)+'</span>').join('');
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

/* ══════════ อัปโหลด + OCR เอกสารสแกน ══════════ */
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const SP = {
  data:    "{{ route('posupplierdocs.data') }}",
  upload:  "{{ route('posupplierdocs.upload') }}",
  process: (id,page) => "{{ url('posupplierdocs/process') }}/"+id+"/"+page,
  pdf:     id => "{{ url('posupplierdocs/pdf') }}/"+id,
  txt:     id => "{{ url('posupplierdocs/txt') }}/"+id,
  assign:  id => "{{ url('posupplierdocs/assign') }}/"+id,
  del:     id => "{{ url('posupplierdocs/delete') }}/"+id,
};
let pickedFiles=[], curScan='';
const OCR_CONCURRENCY = 1;   // typhoon+judge บน 10GB รับได้ทีละหน้า (กัน OOM)
// รันงานพร้อมกันไม่เกิน concurrency งาน
function runPool(items, worker, concurrency){
  return new Promise(resolve=>{
    let idx=0, active=0;
    (function next(){
      if(idx>=items.length && active===0){ resolve(); return; }
      while(active<concurrency && idx<items.length){
        const it=items[idx++]; active++;
        Promise.resolve(worker(it)).catch(()=>{}).finally(()=>{ active--; next(); });
      }
    })();
  });
}

$('folderInput').addEventListener('change', e=>setPicked([...e.target.files]));
$('fileInput').addEventListener('change',   e=>setPicked([...e.target.files]));
function setPicked(files){
  pickedFiles = files.filter(f=>/\.pdf$/i.test(f.name));
  $('pickInfo').textContent = pickedFiles.length ? ('เลือก '+pickedFiles.length+' ไฟล์ PDF') : 'ไม่พบไฟล์ PDF';
  $('btnUpload').disabled = pickedFiles.length===0;
}
const drop=$('drop');
['dragover','dragenter'].forEach(ev=>drop.addEventListener(ev,e=>{e.preventDefault();drop.classList.add('hl');}));
['dragleave','drop'].forEach(ev=>drop.addEventListener(ev,e=>{e.preventDefault();drop.classList.remove('hl');}));
drop.addEventListener('drop', e=>setPicked([...e.dataTransfer.files]));

async function startUpload(){
  if(!pickedFiles.length) return;
  $('btnUpload').disabled=true;
  const prog=$('progress'), bar=$('bar'), pt=$('progText');
  prog.style.display='block'; bar.style.width='0'; pt.textContent='กำลังอัปโหลด...';
  const CHUNK=20; let allRows=[], done=0;
  for(let i=0;i<pickedFiles.length;i+=CHUNK){
    const batch=pickedFiles.slice(i,i+CHUNK);
    const fd=new FormData();
    batch.forEach(f=>{fd.append('files[]',f);fd.append('paths[]',f.webkitRelativePath||f.name);});
    try{
      const r=await fetch(SP.upload,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF},body:fd});
      const j=await r.json(); if(j.ok) allRows=allRows.concat(j.created);
    }catch(e){}
    done+=batch.length; bar.style.width=Math.round(done/pickedFiles.length*40)+'%';
    pt.textContent='อัปโหลด '+done+'/'+pickedFiles.length;
  }
  toast('อัปโหลด '+allRows.length+' ไฟล์ · เริ่ม OCR'); loadScan();
  pt.textContent='กำลัง OCR...';
  // สร้างงานรายหน้าทั้งหมด แล้วรันพร้อมกันหลายงาน (เร่งความเร็ว)
  const tasks=[];
  allRows.forEach(r=>{ const pc=r.page_count||1; for(let pg=1;pg<=pc;pg++) tasks.push({id:r.id,page:pg}); });
  const totalPages = tasks.length; let donePages = 0;
  await runPool(tasks, async t=>{
    try{ await fetch(SP.process(t.id,t.page),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF}}); }catch(e){}
    donePages++;
    bar.style.width=(40+Math.round(donePages/totalPages*60))+'%';
    pt.textContent='OCR '+donePages+'/'+totalPages;
    if(donePages%OCR_CONCURRENCY===0 || donePages===totalPages) loadScan();
  }, OCR_CONCURRENCY);
  bar.style.width='100%'; pt.textContent='เสร็จสิ้น'; toast('OCR เสร็จทั้งหมด');
  pickedFiles=[]; $('pickInfo').textContent=''; load();
  setTimeout(()=>{prog.style.display='none';},1500); loadScan();
}

document.querySelectorAll('#scanTabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('#scanTabs .tab').forEach(x=>x.classList.remove('active'));
  t.classList.add('active'); curScan=t.dataset.m; loadScan();
}));

async function loadScan(){
  const tb=$('scanBody');
  try{
    const u=new URL(SP.data, location.origin);
    if(curScan) u.searchParams.set('match',curScan);
    const q=$('fQ').value.trim(); if(q) u.searchParams.set('q',q);
    const r=await fetch(u,{headers:{'Accept':'application/json'}}); const j=await r.json();
    if(!j.ok){tb.innerHTML='<tr><td colspan=7 class=empty>โหลดไม่สำเร็จ</td></tr>';return;}
    if(!j.rows.length){tb.innerHTML='<tr><td colspan=7 class=empty>— ไม่มีไฟล์ —</td></tr>';return;}
    tb.innerHTML=j.rows.map(scanRow).join('');
  }catch(e){ tb.innerHTML='<tr><td colspan=7 class=empty>โหลดไม่สำเร็จ</td></tr>'; }
}
function ocrPill(r){
  const prog = (r.page_count>1)?(' '+(r.pages_done||0)+'/'+r.page_count):'';
  if(r.ocr_status==='done')return '<span class="pill ok">สำเร็จ</span>';
  if(r.ocr_status==='processing')return '<span class="pill warn">กำลังอ่าน'+prog+'</span>';
  if(r.ocr_status==='error')return '<span class="pill err">ผิดพลาด</span>';
  return '<span class="pill none">รอ</span>';
}
function matchPill(r){
  if(r.match_status==='auto')return '<span class="pill ok">อัตโนมัติ'+(r.match_confidence==='low'?' (เดา)':'')+'</span>';
  if(r.match_status==='manual')return '<span class="pill ok">ยืนยันเอง</span>';
  return '<span class="pill none">ยังไม่จับคู่</span>';
}
function fmtNum(n){ return (n!=null&&n!=='')?Number(n).toLocaleString('en-US',{minimumFractionDigits:2}):'-'; }
function scanRow(r){
  const multi = r.page_count>1 && r.pages && r.pages.length;
  const po = (r.po_id?'<b>'+esc(r.po_id)+'</b> ':'')+matchPill(r);
  const assign='<span class="assign-row"><input type="text" id="po_'+r.id+'" value="'+esc(r.po_id||r.ocr_po_number||'')+'" placeholder="เลข PO"><button class="btn btn-sm btn-green" onclick="doAssign('+r.id+')">บันทึก</button></span>';
  const pageBadge = (r.page_count>1)?'<span class="pill none" style="margin-left:4px">'+r.page_count+' หน้า · พบ '+(r.po_count||0)+' PO</span>':'';
  const txtLabel = (r.page_count>1)?('txt ('+r.page_count+')'):'txt';

  let vendorCell, poCell, totalCell;
  if(multi){
    const poLines = r.pages.map(p=>{
      const snapped = (p.po_ocr && p.po_dist>0) ? ' <span class="src" title="OCR อ่านได้ '+esc(p.po_ocr)+' แก้เป็น PO จริง">(OCR:'+esc(p.po_ocr)+')</span>' : (p.po?' <span class="pill ok" style="padding:0 5px">✓</span>':'');
      return '<div style="padding:2px 0;border-bottom:1px solid #f1f5f9"><span class="src">น.'+p.page+'</span> PO <b class="mono">'+esc(p.po||'-')+'</b>'+snapped+'<br><span class="src">เอกสาร:</span> <b class="mono">'+esc(p.inv||'-')+'</b></div>';
    });
    poCell   = '<div style="max-height:200px;overflow:auto">'+poLines.join('')+'</div>';
    vendorCell = '<b>'+(r.po_count||0)+' PO / '+r.page_count+' หน้า</b><div class="src">กด txt ดูทุกใบ</div>';
    const sum = r.pages.reduce((s,p)=>s+(Number(p.total)||0),0);
    totalCell = '<b>'+fmtNum(sum)+'</b><div class="src">รวมทุกใบ</div>';
  } else {
    vendorCell = esc(r.vendor_name||'-');
    poCell = 'PO <b class="mono">'+esc(r.ocr_po_number||'-')+'</b><br><span class="src">เอกสาร:</span> <b class="mono">'+esc(r.invoice_no||'-')+'</b>';
    totalCell = fmtNum(r.total);
  }

  return '<tr>'
    +'<td>'+esc(r.original_name)+pageBadge+(r.rel_folder?'<div class="src">'+esc(r.rel_folder)+'</div>':'')+'</td>'
    +'<td>'+vendorCell+'</td>'
    +'<td>'+poCell+'</td>'
    +'<td class="num">'+totalCell+'</td>'
    +'<td>'+ocrPill(r)+'</td>'
    +'<td>'+po+assign+'</td>'
    +'<td>'
      +'<button class="btn btn-sm" onclick="window.open(\''+SP.pdf(r.id)+'\',\'_blank\')">ดูรูป</button> '
      +'<button class="btn btn-sm" onclick="viewTxt('+r.id+')">'+txtLabel+'</button> '
      +((r.ocr_status==='error'||r.ocr_status==='pending'||r.ocr_status==='processing')?'<button class="btn btn-sm" onclick="reOcr('+r.id+',this)">OCR ใหม่</button> ':'')
      +'<button class="btn btn-sm btn-red" onclick="doDelete('+r.id+')">ลบ</button>'
    +'</td></tr>';
}
async function viewTxt(id){
  try{
    const r=await fetch(SP.txt(id)); const j=await r.json();
    $('txtTitle').textContent='ผล OCR'+(j.po_id?' · PO '+j.po_id:'');
    $('txtBody').textContent=j.text||'(ยังไม่มีข้อความ)';
    $('txtOverlay').style.display='flex';
  }catch(e){ toast('เปิดไม่ได้','err'); }
}
function closeTxt(){ $('txtOverlay').style.display='none'; }
async function doAssign(id){
  const po=$('po_'+id).value.trim(); if(!po){toast('กรอกเลข PO ก่อน','err');return;}
  const fd=new FormData(); fd.append('po_id',po);
  const r=await fetch(SP.assign(id),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF},body:fd}); const j=await r.json();
  if(j.ok){toast('บันทึก PO แล้ว');loadScan();}else{toast(j.message||'ไม่สำเร็จ','err');}
}
async function reOcr(id,btn){
  if(btn){btn.disabled=true;btn.textContent='...';}
  // หา page_count ของแถวนี้
  let pc=1;
  try{ const rt=await fetch(SP.txt(id)); const rj=await rt.json(); pc=rj.page_count||1; }catch(e){}
  try{
    const tasks=[]; for(let pg=1;pg<=pc;pg++) tasks.push(pg);
    let d=0;
    await runPool(tasks, async pg=>{
      await fetch(SP.process(id,pg),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF}});
      d++; if(btn) btn.textContent='OCR '+d+'/'+pc;
    }, OCR_CONCURRENCY);
    toast('OCR เสร็จ');
  }catch(e){ toast('OCR ไม่สำเร็จ','err'); }
  loadScan();
}
async function doDelete(id){
  if(!confirm('ลบเอกสารนี้?'))return;
  const r=await fetch(SP.del(id),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF}}); const j=await r.json();
  if(j.ok){toast('ลบแล้ว');loadScan();}else{toast('ลบไม่สำเร็จ','err');}
}
document.addEventListener('DOMContentLoaded', loadScan);
</script>
</body>
</html>

{{-- resources/views/driver/billreceive.blade.php — ระบบรับเข้าบิล (ดึงจาก transaction_transport) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>รับเข้าบิล — ระบบจัดการขนส่ง</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
:root {
  /* โทนสีชุดเดียวกับหน้า Deliverytrack (จ่ายงานขนส่งสินค้า) */
  --bg: #f0f2f5;
  --card: #ffffff;
  --line: #e9ecef;
  --line-strong: #dee2e6;
  --line-light: #f8f9fa;
  --ink: #1a2634;
  --ink2: #5c6b7a;
  --ink3: #8592a0;
  --ink4: #aab4bd;

  --primary: #2853d5;
  --primary-rgb: 40,83,213;
  --primary-hover: #1f42ab;
  --primary-light: #eaf0fc;
  --primary-dark: #2853d5;
  --primary-dark-hover: #1f42ab;

  --green: #2e7d32; --green-d: #1b5e20; --green-l: #e8f5e9;
  --amber: #ed6c02; --amber-d: #b45309; --amber-l: #fff4e5;
  --red: #c62828; --red-rgb: 198,40,40; --red-d: #a91f1f; --red-l: #ffebee;
  --violet: #6d28d9; --violet-d: #5b21b6; --violet-l: #f3eefc;

  --radius: 12px;
  --font: 'Sarabun', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-mono: 'JetBrains Mono', 'Sarabun', monospace;
}

html, body { margin: 0; background: var(--bg); color: var(--ink); font-family: var(--font); -webkit-font-smoothing: antialiased; }
a { color: inherit; text-decoration: none; }

/* Topbar */
.topbar {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--line);
  position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px; height: 68px;
}
.topbar .brand { font-weight: 700; font-size: 17px; display: flex; align-items: center; gap: 12px; color: var(--ink); }
.topbar .brand .tag { background: var(--primary-light); color: var(--primary); font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; letter-spacing: 0.5px; }
.topbar .right { display: flex; align-items: center; gap: 14px; font-size: 13px; color: var(--ink2); }
.topbar .user { display: inline-flex; align-items: center; gap: 8px; background: #fff; padding: 5px 14px 5px 5px; border-radius: 30px; font-weight: 600; color: var(--ink); border: 1px solid var(--line); box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.user-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--primary-dark); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.topbar a.back { color: var(--ink3); font-size: 13px; border: 1px solid var(--line); padding: 7px 14px; border-radius: 10px; transition: all 0.2s; font-weight: 500; }
.topbar a.back:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

/* Main Container */
.wrap { width: 100%; max-width: 1800px; margin: 24px auto; padding: 0 20px; }

/* Filters Section */
.filters {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius);
  padding: 18px 20px;
  display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
  margin-bottom: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
}
.fg { display: flex; flex-direction: column; gap: 6px; }
.fg label { font-size: 12px; font-weight: 600; color: var(--ink3); }
.fg input {
  height: 42px; padding: 0 14px; border: 1px solid var(--line); border-radius: 10px;
  font-family: inherit; font-size: 14px; outline: none; min-width: 200px; background: #fff;
  transition: all 0.2s; color: var(--ink);
}
.fg input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(var(--primary-rgb),.12); }

.btn {
  height: 42px; padding: 0 18px; border-radius: 10px; border: 1px solid var(--line-strong);
  background: #fff; font-family: inherit; font-size: 13.5px; font-weight: 600; color: var(--ink2);
  cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
}
.btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.btn-primary { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; }
.btn-primary:hover { background: var(--primary-dark-hover); border-color: var(--primary-dark-hover); color: #fff; }
.hint { font-size: 12px; color: var(--ink4); margin-left: auto; align-self: center; font-weight: 500; }

.count-bar { font-size: 13.5px; color: var(--ink3); margin: 0 4px 14px; font-weight: 500; }
.count-bar b { color: var(--ink); font-weight: 700; }

/* Job Card */
.job {
  background: var(--card); border: 1px solid var(--line); border-radius: var(--radius);
  padding: 18px 20px; margin-bottom: 12px;
  display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;
  box-shadow: 0 2px 4px rgba(0,0,0,0.01);
  transition: all 0.2s ease;
}
.job:hover { border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); }
.job.done { opacity: 0.78; background: #fafafa; border-style: dashed; }
/* ทำสีเฉพาะป้ายประเภท (type label) ไม่ระบายทั้งกล่อง */
.job.type-company .job-type, .job.type-private .job-type { background: #d8e6ff; color: #1e40af; border-color: #9ec0ff; }
.job.type-doc .job-type { background: rgb(255, 247, 237); color: #b45309; border-color: #f3d3ac; }
.job-main { flex: 1 1 380px; min-width: 280px; }

.job-line1 { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }
.job-type { font-size: 12px; font-weight: 700; color: var(--ink3); padding: 3px 10px; border-radius: 6px; border: 1px solid var(--line); background: var(--line-light); }
.job-bill { font-weight: 700; font-size: 16px; color: var(--ink); }
.job-code { font-family: var(--font-mono); font-size: 12px; color: var(--primary); background: var(--primary-light); padding: 3px 9px; border-radius: 6px; font-weight: 600; }

.badge { font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 30px; letter-spacing: 0.3px; }
.badge.pending { background: var(--line-light); color: var(--ink3); border: 1px solid var(--line); }
.badge.ok { background: var(--green-l); color: var(--green-d); }
.badge.hold { background: var(--primary-light); color: var(--primary); }   /* ค้างบิล = สีฟ้า */
.badge.wrong { background: var(--red-l); color: var(--red-d); }

.job-cust { font-size: 14.5px; color: var(--ink2); font-weight: 600; margin-bottom: 10px; }
.job-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; font-size: 12.5px; color: var(--ink3); }
.job-meta .mi b { color: var(--ink2); font-weight: 600; }
.job-note { margin-top: 8px; font-size: 12.5px; color: var(--red-d); background: var(--red-l); padding: 8px 12px; border-radius: 8px; border-left: 3px solid var(--red); font-weight: 500; }
.job-linked { margin-top: 8px; font-size: 12.5px; color: #b45309; background: #fff7ed; padding: 8px 12px; border-radius: 8px; border-left: 3px solid #f0b374; font-weight: 600; }

/* Actions */
.job-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; align-self: stretch; padding-left: 20px; border-left: 1px solid var(--line); }
.act {
  height: 38px; padding: 0 16px; border-radius: 9px; border: 1px solid var(--line-strong);
  background: #fff; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--ink2);
  white-space: nowrap; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;
}
.act:hover:not(:disabled) { background: var(--line-light); border-color: var(--ink3); }
.act:disabled { opacity: .5; cursor: not-allowed; }

/* ปุ่มสถานะเป็นปุ่มสีทึบ อ่านง่ายสำหรับหน้างานในสำนักงาน */
.act.ok, .act.hold, .act.redo, .act.wrong { color: #fff; border-color: transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
.act.ok { background: var(--green); }
.act.ok:hover:not(:disabled) { background: var(--green-d); }
.act.hold { background: var(--amber); }
.act.hold:hover:not(:disabled) { background: var(--amber-d); }
.act.redo { background: var(--primary); }
.act.redo:hover:not(:disabled) { background: var(--primary-hover); }
.act.wrong { background: var(--red); }
.act.wrong:hover:not(:disabled) { background: var(--red-d); }
.job-confirmed { font-size: 12.5px; color: var(--green-d); font-weight: 600; align-self: center; background: var(--green-l); padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }
/* กล่องผลรับเข้า สีตามสถานะ */
.job-result { font-size: 12.5px; font-weight: 600; align-self: center; padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }
.job-result.ok { color: var(--green-d); background: var(--green-l); }
.job-result.hold { color: var(--primary); background: var(--primary-light); }      /* ค้างบิล = ฟ้า */
.job-result.wrong { color: var(--red-d); background: var(--red-l); }                 /* สินค้าผิด = แดง */
.job-redispatched { font-size: 12.5px; color: var(--amber-d); font-weight: 600; align-self: center; background: var(--amber-l); padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }

/* Wrong Box form toggle */
.wrong-box { flex: 1 1 100%; display: none; gap: 10px; margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--line); align-items: center; flex-wrap: wrap; }
.wrong-box.open { display: flex; animation: fadeIn 0.2s ease; }
.wrong-box input { flex: 1 1 240px; height: 38px; padding: 0 12px; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none; }
.wrong-box input:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(var(--red-rgb), 0.12); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

.state { text-align: center; padding: 60px 16px; color: var(--ink4); font-size: 14px; font-weight: 500; }
.spinner { width: 18px; height: 18px; border: 2.5px solid var(--line); border-top-color: var(--primary); border-radius: 50%; display: inline-block; animation: spin .6s linear infinite; vertical-align: -3px; margin-right: 8px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Toast Notifications */
.toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.toast {
  background: #fff; color: var(--ink); border: 1px solid var(--line); border-left: 4px solid var(--green);
  box-shadow: 0 8px 24px rgba(0,0,0,.15); padding: 14px 18px; border-radius: 12px;
  font-size: 13.5px; font-weight: 500; min-width: 260px; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  pointer-events: auto; transform: translateY(0); opacity: 1;
}
.toast.err { border-left-color: var(--red); }
.toast.hide { opacity: 0; transform: translateY(12px); }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9998; padding: 16px; }
.modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
.modal-box { background: #fff; border-radius: 16px; padding: 26px; width: 100%; max-width: 400px; box-shadow: 0 25px 50px -12px rgba(0,0,0,.25); border: 1px solid var(--line); }
.modal-title { font-weight: 700; font-size: 18px; color: var(--ink); }
.modal-sub { font-size: 13.5px; color: var(--ink3); margin: 6px 0 18px; line-height: 1.5; }
.modal-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink3); margin-bottom: 6px; }
.modal-date { width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--line); border-radius: 10px; font-family: inherit; font-size: 15px; color: var(--ink); }
.modal-date:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(var(--primary-rgb),.12); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
.modal-actions .btn { height: 42px; }

@media(max-width: 640px) { 
  .topbar { padding: 0 16px; }
  .filters { padding: 14px; }
  .job-actions { flex: 1 1 100%; justify-content: flex-start; margin-top: 8px; padding-left: 0; padding-top: 12px; border-left: none; border-top: 1px dashed var(--line); }
}
</style>
</head>
<body>

<div class="topbar">
  <div class="brand">รับเข้าบิล <span class="tag">BILL RECEIVE</span></div>
  <div class="right">
    <a class="back" href="{{ route('oil') }}">← กลับหน้าน้ำมัน</a>
    <span class="user"><span class="user-avatar">{{ mb_strtoupper(mb_substr($loggedInName, 0, 1)) }}</span>{{ $loggedInName }}</span>
  </div>
</div>

<div class="wrap">
  <div class="filters">
    <div class="fg">
      <label for="fDate">วันที่จ่ายงาน (time_pick)</label>
      <input type="date" id="fDate">
    </div>
    <div class="fg">
      <label for="fBill">ค้นหาเลขบิล (ไม่สนวันที่)</label>
      <input type="text" id="fBill" placeholder="เช่น 46909-02085" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fCust">รหัสลูกค้า</label>
      <input type="text" id="fCust" placeholder="เช่น CUS-16026" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fCustName">ชื่อลูกค้า</label>
      <input type="text" id="fCustName" placeholder="พิมพ์ชื่อลูกค้า" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fDriver">คนขับ</label>
      <input type="text" id="fDriver" placeholder="พิมพ์ชื่อคนขับ" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fStatus">สถานะบิล</label>
      <select id="fStatus" style="height:38px;padding:0 10px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:13px;">
        <option value="">ทั้งหมด</option>
        <option value="pending">รอส่ง / ค้าง</option>
        <option value="ok">สำเร็จ</option>
        <option value="hold">ค้างบิล</option>
        <option value="wrong">สินค้าผิด</option>
      </select>
    </div>
    <button type="button" class="btn btn-primary" id="btnSearch">ค้นหา</button>
    <button type="button" class="btn" id="btnClear">ล้าง</button>
    <span class="hint">ค้นเลขบิลจะไม่สนใจวันที่ · รหัส/ชื่อลูกค้า/สถานะ กรองในรายการที่โหลดมา</span>
  </div>

  <div class="count-bar" id="countBar"></div>
  <div id="list"></div>
</div>

<!-- แถบเลือกหลายรายการ (bulk) -->
<div id="bulkBar" style="display:none;position:fixed;left:50%;bottom:20px;transform:translateX(-50%);z-index:900;background:#0f172a;color:#fff;border-radius:30px;box-shadow:0 10px 25px -5px rgba(0,0,0,.3);padding:10px 18px;display:none;align-items:center;gap:12px;">
  <span>เลือก <b id="bulkCount">0</b> รายการ</span>
  <button type="button" class="act ok" onclick="bulkSetStatus('ok')">สำเร็จ</button>
  <button type="button" class="act redo" onclick="bulkRedo()">จัดส่งใหม่</button>
  <button type="button" class="act" style="background:rgba(255,255,255,.15);color:#fff;border:none;" onclick="clearBulk()">ล้างเลือก</button>
</div>


<div class="toast-wrap" id="toastWrap"></div>

<script>
const DATA_URL    = "{{ route('billreceive.data') }}";
const CONFIRM_URL = "{{ route('billreceive.confirm') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const fDate = document.getElementById('fDate');
const fBill = document.getElementById('fBill');
const listEl = document.getElementById('list');
const countBar = document.getElementById('countBar');

function esc(s){ return (s==null?'':String(s)).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

function toast(msg, err){
  const w = document.getElementById('toastWrap');
  const t = document.createElement('div');
  t.className = 'toast' + (err?' err':'');
  t.textContent = msg;
  w.appendChild(t);
  setTimeout(()=>{ t.classList.add('hide'); setTimeout(()=>t.remove(),300); }, 3200);
}

function statusInfo(st){
  const s = (st||'').trim();
  if(s === 'จัดส่งสำเร็จ') return {cls:'ok', txt:'สำเร็จ'};
  if(s === 'ค้างบิล')     return {cls:'hold', txt:'ค้างบิล'};
  if(s === 'สินค้าผิด')    return {cls:'wrong', txt:'สินค้าผิด'};
  if(s === 'ส่งใหม่วันพรุ่งนี้') return {cls:'hold', txt:'ส่งใหม่'};
  return {cls:'pending', txt:'รอส่ง'};
}

// "รับเข้าแล้ว" = สถานะเป็นผลจริง (สำเร็จ/ค้างบิล/สินค้าผิด) เท่านั้น — ไม่ดูแค่ check_time
function isReceived(r){ return ['จัดส่งสำเร็จ','ค้างบิล','สินค้าผิด'].includes(((r&&r.status)||'').trim()); }

let currentRows = [];
let selectedBulk = new Set();   // เก็บ index (ของ currentRows) ที่ติ๊กเลือกไว้

// map สถานะจริง -> key สำหรับ filter
function statusKey(r){
  const s = ((r&&r.status)||'').trim();
  if(s === 'จัดส่งสำเร็จ') return 'ok';
  if(s === 'ค้างบิล')     return 'hold';
  if(s === 'สินค้าผิด')    return 'wrong';
  return 'pending';   // รอส่ง / ส่งใหม่
}

// กรอง client-side: รหัสลูกค้า / ชื่อลูกค้า / สถานะ — คืน [{r, i}] (i = index จริงใน currentRows)
function getFilteredRows(){
  const cust  = (document.getElementById('fCust').value||'').trim().toLowerCase();
  const cname = (document.getElementById('fCustName').value||'').trim().toLowerCase();
  const drv   = (document.getElementById('fDriver').value||'').trim().toLowerCase();
  const st    = document.getElementById('fStatus').value;
  return currentRows.map((r,i)=>({r,i})).filter(({r})=>{
    if(cust  && !((r.customer_code||'').toLowerCase().includes(cust)))  return false;
    if(cname && !((r.customer_name||'').toLowerCase().includes(cname))) return false;
    if(drv   && !((r.driver_name||'').toLowerCase().includes(drv)))     return false;
    if(st    && statusKey(r) !== st) return false;
    return true;
  });
}

function toggleBulk(i, checked){ if(checked) selectedBulk.add(i); else selectedBulk.delete(i); updateBulkBar(); }
function clearBulk(){ selectedBulk.clear(); render(); }
function updateBulkBar(){
  const bar = document.getElementById('bulkBar');
  document.getElementById('bulkCount').textContent = selectedBulk.size;
  bar.style.display = selectedBulk.size ? 'flex' : 'none';
}
async function bulkSetStatus(action){
  const idxs = Array.from(selectedBulk);
  if(!idxs.length) return;
  const label = action==='ok' ? 'สำเร็จ' : 'ค้างบิล';
  let note = '';
  if(action==='hold'){
    note = (prompt(`หมายเหตุค้างบิล (ใช้กับ ${idxs.length} รายการที่เลือก):`, '') || '').trim();
    if(!note){ toast('กรุณากรอกหมายเหตุค้างบิล', true); return; }
  }
  if(!confirm(`ยืนยันตั้งสถานะ "${label}" ให้ ${idxs.length} รายการที่เลือก?`)) return;
  let okN=0, failN=0;
  for(const i of idxs){
    const r = currentRows[i];
    if(!r) continue;
    try{
      const data = await postConfirm({ job_key:r.job_key, action, note, tx_ids:r.tx_ids });
      r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time;
      if(note) r.note = note;
      okN++;
    }catch(e){ failN++; }
  }
  selectedBulk.clear();
  toast(`ตั้งสถานะสำเร็จ ${okN} รายการ${failN?` · ล้มเหลว ${failN}`:''}`, failN>0);
  render();
}

// bulk จัดส่งใหม่ = คืนงานที่เลือกกลับไปหน้าจ่ายงานขนส่ง
async function bulkRedo(){
  const idxs = Array.from(selectedBulk);
  if(!idxs.length) return;
  if(!confirm(`จัดส่งใหม่ ${idxs.length} รายการที่เลือก?\nงานจะถูกคืนกลับไปหน้าจ่ายงานขนส่ง เพื่อจ่ายให้คนขับใหม่`)) return;
  let okN=0, failN=0;
  for(const i of idxs){
    const r = currentRows[i];
    if(!r) continue;
    try{ await postConfirm({ job_key:r.job_key, action:'redo', tx_ids:r.tx_ids }); okN++; }
    catch(e){ failN++; }
  }
  selectedBulk.clear();
  toast(`คืนงานไปจ่ายใหม่ ${okN} รายการ${failN?` · ล้มเหลว ${failN}`:''}`, failN>0);
  loadData();
}

async function loadData(){
  const q      = fBill.value.trim();
  const cust   = document.getElementById('fCust').value.trim();
  const cname  = document.getElementById('fCustName').value.trim();
  const driver = document.getElementById('fDriver').value.trim();
  const status = document.getElementById('fStatus').value;
  const anyFilter = q || cust || cname || driver || status;
  const params = new URLSearchParams();
  if(q) params.set('q', q);
  if(cust) params.set('cust', cust);
  if(cname) params.set('cname', cname);
  if(driver) params.set('driver', driver);
  if(status) params.set('status', status);
  if(!anyFilter) params.set('date', fDate.value);   // ไม่มีตัวกรอง -> ตามวันที่
  listEl.innerHTML = '<div class="state"><span class="spinner"></span>กำลังโหลดข้อมูล...</div>';
  countBar.textContent = '';
  try{
    const res = await fetch(`${DATA_URL}?${params.toString()}`, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if(!res.ok || !data.ok){ throw new Error(data.message || 'โหลดข้อมูลไม่สำเร็จ'); }
    currentRows = data.rows || [];
    selectedBulk.clear();
    render();
  }catch(e){
    listEl.innerHTML = `<div class="state">เกิดข้อผิดพลาด: ${esc(e.message)}</div>`;
  }
}

function render(){
  const rows = getFilteredRows();
  if(!rows.length){
    listEl.innerHTML = '<div class="state">ไม่พบรายการตามเงื่อนไข</div>';
    countBar.textContent = '';
    updateBulkBar();
    return;
  }
  const doneN = rows.filter(({r})=>isReceived(r)).length;
  countBar.innerHTML = `แสดง <b>${rows.length}</b> บิล · รับเข้าแล้ว <b>${doneN}</b> · คงเหลือ <b>${rows.length-doneN}</b>`;

  listEl.innerHTML = rows.map(({r,i})=>{
    const si = statusInfo(r.status);
    const typeLabel = r.type==='doc' ? 'บิลชั่วคราว'
                    : (r.type==='private' ? 'บิล · ขนส่งเอกชน' : 'บิล · ส่งโดยบริษัท');
    const meta = [];
    if(r.driver_name)   meta.push(`<span class="mi"><b>คนขับ</b> ${esc(r.driver_name)}</span>`);
    if(r.transport_name)meta.push(`<span class="mi"><b>ขนส่ง</b> ${esc(r.transport_name)}</span>`);
    if(r.delivery_date) meta.push(`<span class="mi"><b>วันส่ง</b> ${esc(r.delivery_date)}</span>`);
    meta.push(`<span class="mi"><b>ผู้จ่ายงาน</b> ${esc(r.name_pick||'-')}</span>`);
    if(r.time_pick)     meta.push(`<span class="mi"><b>จ่ายเมื่อ</b> ${esc(r.time_pick)}</span>`);

    const received = isReceived(r);
    const redispatched = !received && !!r.redispatched_to;   // ถูกจ่ายใหม่ไปวันหลังแล้ว
    let actions;
    if(received){
      // รับเข้าแล้ว -> แสดงผลตามสถานะ + ให้กลับมากด "สำเร็จ" ได้ (เช่น ค้างบิล/สินค้าผิด -> เปลี่ยนเป็นสำเร็จภายหลัง)
      const noteLine = (r.note && (si.cls==='wrong' || si.cls==='hold')) ? `<br>หมายเหตุ: ${esc(r.note)}` : '';
      // "เปลี่ยนเป็นสำเร็จ" แสดงเฉพาะงานที่ค้างบิลเท่านั้น
      const canReSuccess = (((r.status||'').trim()) === 'ค้างบิล');
      actions = `<div class="job-result ${si.cls}">รับเข้าแล้ว: ${esc(si.txt)}<br>โดย ${esc(r.check_name||'-')}${r.check_time?' · เมื่อ '+esc(r.check_time):''}${noteLine}</div>`
        + (canReSuccess ? `<button type="button" class="act ok" style="margin-top:6px;" onclick="doAction(${i},'ok')">เปลี่ยนเป็นสำเร็จ</button>` : '');
    } else if(redispatched){
      // งานต้นทางที่ถูกจ่ายใหม่ไปวันอื่นแล้ว -> ไม่มีปุ่ม แสดงว่าย้ายไปวันไหน
      actions = `<div class="job-redispatched">↻ ถูกจ่ายใหม่ให้ไปวันที่ ${esc(r.redispatched_to)} แล้ว</div>`;
    } else {
      actions = `<button type="button" class="act ok"    onclick="doAction(${i},'ok')">สำเร็จ</button>
         <button type="button" class="act hold"  onclick="openNote(${i},'hold')">ค้างบิล</button>
         <button type="button" class="act redo"  onclick="doRedo(${i})">ส่งใหม่ (จ่ายงานใหม่)</button>
         <button type="button" class="act wrong" onclick="openNote(${i},'wrong')">สินค้าผิด</button>`;
    }

    const chk = !redispatched
      ? `<input type="checkbox" class="job-chk" ${selectedBulk.has(i)?'checked':''} onchange="toggleBulk(${i},this.checked)" title="เลือกเพื่อตั้งสถานะพร้อมกัน" style="width:20px;height:20px;align-self:center;margin-right:4px;cursor:pointer;flex-shrink:0;">`
      : '';
    return `<div class="job type-${r.type} ${received||redispatched?'done':''}" id="job-${i}">
      ${chk}
      <div class="job-main">
        <div class="job-line1">
          <span class="job-type">${esc(typeLabel)}</span>
          ${r.customer_code?`<span class="job-code">${esc(r.customer_code)}</span>`:''}
          <span class="job-bill">${esc(r.bill_no||'-')}</span>
        </div>
        <div class="job-cust">${r.so_id?`<b>SO ${esc(r.so_id)}</b> · `:''}${esc(r.customer_name||'-')}</div>
        <div class="job-meta">${meta.join('')}</div>
        ${(r.linked_bills && r.linked_bills.length)?`<div class="job-linked">เชื่อมกัน ${r.linked_bills.length} บิลค้าง: ${r.linked_bills.map(esc).join(', ')}</div>`:''}
        ${(!received && r.note)?`<div class="job-note">หมายเหตุ: ${esc(r.note)}</div>`:''}
        <div class="wrong-box" id="wrong-${i}" data-action="wrong">
          <input type="text" id="wrongnote-${i}" placeholder="ระบุหมายเหตุ...">
          <button type="button" class="act wrong" id="notesave-${i}" onclick="submitNote(${i})">บันทึก</button>
          <button type="button" class="act" onclick="closeNote(${i})">ยกเลิก</button>
        </div>
      </div>
      <div class="job-actions">${actions}</div>
    </div>`;
  }).join('');
}

// เปิดกล่องหมายเหตุ (ใช้ได้ทั้ง ค้างบิล และ สินค้าผิด) — ต้องกรอกหมายเหตุก่อนบันทึก
function openNote(i, action){
  const box  = document.getElementById('wrong-'+i);
  const inp  = document.getElementById('wrongnote-'+i);
  const save = document.getElementById('notesave-'+i);
  if(!box) return;
  box.dataset.action = action;
  const label = action==='hold' ? 'ค้างบิล' : 'สินค้าผิด';
  if(inp)  inp.placeholder = action==='hold' ? 'ระบุหมายเหตุค้างบิล...' : 'ระบุรายละเอียดสินค้าผิด...';
  if(save){ save.textContent = 'บันทึก'+label; save.className = 'act ' + (action==='hold' ? 'hold' : 'wrong'); }
  box.classList.add('open');
  inp?.focus();
}
function closeNote(i){ document.getElementById('wrong-'+i)?.classList.remove('open'); }

async function postConfirm(payload){
  const res = await fetch(CONFIRM_URL, {
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify(payload),
  });
  const data = await res.json().catch(()=>null);
  if(!res.ok || !data || !data.ok) throw new Error((data && data.message) || 'บันทึกไม่สำเร็จ');
  return data;
}

async function doAction(i, action){
  const r = currentRows[i];
  if(!r) return;
  const labels = {ok:'สำเร็จ', hold:'ค้างบิล'};
  if(!confirm(`ยืนยันบันทึกสถานะ "${labels[action]}" สำหรับบิล ${r.bill_no}?`)) return;
  try{
    const data = await postConfirm({ job_key:r.job_key, action, tx_ids:r.tx_ids });
    toast(data.message || 'บันทึกข้อมูลเรียบร้อย');
    r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
}

async function submitNote(i){
  const r = currentRows[i];
  if(!r) return;
  const box = document.getElementById('wrong-'+i);
  const action = (box && box.dataset.action) || 'wrong';
  const label = action==='hold' ? 'ค้างบิล' : 'สินค้าผิด';
  const note = (document.getElementById('wrongnote-'+i)?.value || '').trim();
  if(!note){ toast('กรุณากรอกหมายเหตุ'+label, true); return; }
  if(!confirm(`ยืนยันบันทึก "${label}" สำหรับบิล ${r.bill_no}?`)) return;
  try{
    const data = await postConfirm({ job_key:r.job_key, action, note, tx_ids:r.tx_ids });
    toast(data.message || 'บันทึกข้อมูลเรียบร้อย');
    r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time; r.note = note;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
}

// ส่งใหม่ = คืนงานกลับไปหน้าจ่ายงานขนส่ง (deliverytrack) เพื่อจ่ายให้คนขับใหม่ (ไม่ต้องเลือกวันที่แล้ว)
async function doRedo(i){
  const r = currentRows[i];
  if(!r) return;
  if(!confirm(`ส่งบิล ${r.bill_no} ใหม่?\nงานจะถูกคืนกลับไปหน้าจ่ายงานขนส่ง เพื่อจ่ายให้คนขับใหม่ (เลือกวัน/คนขับที่นั่น)`)) return;
  try{
    const data = await postConfirm({ job_key:r.job_key, action:'redo', tx_ids:r.tx_ids });
    toast(data.message || 'คืนงานไปจ่ายงานใหม่แล้ว');
    loadData();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
}

document.getElementById('btnSearch').addEventListener('click', loadData);
document.getElementById('btnClear').addEventListener('click', ()=>{
  fBill.value=''; fDate.value = new Date().toISOString().split('T')[0];
  document.getElementById('fCust').value=''; document.getElementById('fCustName').value='';
  document.getElementById('fDriver').value=''; document.getElementById('fStatus').value='';
  loadData();
});
fBill.addEventListener('keydown', e=>{ if(e.key==='Enter') loadData(); });
fDate.addEventListener('change', ()=>{ if(fBill.value.trim()==='') loadData(); });
// filter รหัส/ชื่อลูกค้า/สถานะ = ค้นข้ามวัน (โหลดใหม่จาก server) + render ทันทีระหว่างพิมพ์
let _filterTimer = null;
['fCust','fCustName','fDriver'].forEach(id => document.getElementById(id).addEventListener('input', ()=>{
  render();  // กรองชุดที่โหลดมาทันที
  clearTimeout(_filterTimer);
  _filterTimer = setTimeout(loadData, 400);  // แล้วค่อยโหลดข้ามวันจาก server
}));
document.getElementById('fStatus').addEventListener('change', loadData);

document.addEventListener('DOMContentLoaded', ()=>{
  fDate.value = new Date().toISOString().split('T')[0];
  loadData();
});
</script>
</body>
</html>
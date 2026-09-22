{{-- resources/views/driver/billreceive.blade.php — ระบบรับเข้าบิล (ดึงจาก transaction_transport) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>รับเข้าบิล</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;}
:root{
  --bg:#f4f5f7; --card:#fff; --line:#e5e7eb; --line2:#eaeaea;
  --ink:#18181b; --ink2:#3f3f46; --ink3:#6b7280; --ink4:#9ca3af;
  --blue:#3e6ae1; --blue2:#3457b2;
  --green:#10b981; --green-d:#059669; --green-l:#d1fae5;
  --amber:#f59e0b; --amber-l:#fef3c7; --amber-d:#b45309;
  --red:#ef4444; --red-l:#fee2e2; --red-d:#b91c1c;
  --violet:#8b5cf6; --violet-l:#ede9fe; --violet-d:#6d28d9;
  --radius:10px;
  --font:'IBM Plex Sans Thai',-apple-system,sans-serif;
}
html,body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font);}
a{color:inherit;}
.topbar{background:#fff;border-bottom:1px solid var(--line2);position:sticky;top:0;z-index:50;
  display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:60px;}
.topbar .brand{font-weight:700;font-size:16px;display:flex;align-items:center;gap:10px;}
.topbar .brand .tag{background:#eff6ff;color:var(--blue);font-size:11px;font-weight:600;padding:3px 8px;border-radius:6px;}
.topbar .right{display:flex;align-items:center;gap:12px;font-size:13px;color:var(--ink2);}
.topbar .user{display:inline-flex;align-items:center;gap:7px;background:#f3f4f6;padding:5px 12px;border-radius:20px;font-weight:600;}
.topbar a.back{color:var(--ink3);text-decoration:none;font-size:13px;border:1px solid var(--line);padding:6px 12px;border-radius:8px;}
.topbar a.back:hover{border-color:var(--blue);color:var(--blue);}

.wrap{max-width:1200px;margin:20px auto;padding:0 16px;}
.filters{background:#fff;border:1px solid var(--line2);border-radius:var(--radius);padding:14px 16px;
  display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap;margin-bottom:16px;}
.fg{display:flex;flex-direction:column;gap:5px;}
.fg label{font-size:12px;font-weight:600;color:var(--ink3);}
.fg input{height:38px;padding:0 12px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:14px;outline:none;min-width:180px;}
.fg input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(62,106,225,.12);}
.btn{height:38px;padding:0 16px;border-radius:8px;border:1px solid var(--line);background:#fff;font-family:inherit;
  font-size:13px;font-weight:600;color:var(--ink2);cursor:pointer;}
.btn:hover{border-color:var(--blue);color:var(--blue);}
.btn-primary{background:var(--blue);border-color:var(--blue);color:#fff;}
.btn-primary:hover{background:var(--blue2);color:#fff;}
.hint{font-size:12px;color:var(--ink4);margin-left:auto;align-self:center;}

.count-bar{font-size:13px;color:var(--ink3);margin:0 2px 12px;}
.count-bar b{color:var(--ink);}

.job{background:#fff;border:1px solid var(--line2);border-radius:var(--radius);padding:14px 16px;margin-bottom:10px;
  display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;}
.job.done{opacity:.72;background:#fafafa;}
.job-main{flex:1 1 340px;min-width:280px;}
.job-line1{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;}
.job-bill{font-weight:700;font-size:15px;}
.job-code{font-size:12px;color:var(--ink3);background:#f3f4f6;padding:2px 8px;border-radius:6px;}
.badge{font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;}
.badge.pending{background:#f3f4f6;color:var(--ink3);}
.badge.ok{background:var(--green-l);color:var(--green-d);}
.badge.hold{background:var(--amber-l);color:var(--amber-d);}
.badge.wrong{background:var(--red-l);color:var(--red-d);}
.job-cust{font-size:14px;color:var(--ink2);margin-bottom:8px;}
.job-meta{display:flex;flex-wrap:wrap;gap:6px 16px;font-size:12px;color:var(--ink3);}
.job-meta .mi b{color:var(--ink2);font-weight:600;}
.job-note{margin-top:6px;font-size:12px;color:var(--red-d);}

.job-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;align-self:center;}
.act{height:34px;padding:0 12px;border-radius:8px;border:1px solid var(--line);background:#fff;font-family:inherit;
  font-size:12.5px;font-weight:700;cursor:pointer;white-space:nowrap;}
.act:disabled{opacity:.5;cursor:not-allowed;}
.act.ok{border-color:var(--green);color:var(--green-d);}
.act.ok:hover:not(:disabled){background:var(--green-l);}
.act.hold{border-color:var(--amber);color:var(--amber-d);}
.act.hold:hover:not(:disabled){background:var(--amber-l);}
.act.redo{border-color:var(--violet);color:var(--violet-d);}
.act.redo:hover:not(:disabled){background:var(--violet-l);}
.act.wrong{border-color:var(--red);color:var(--red-d);}
.act.wrong:hover:not(:disabled){background:var(--red-l);}
.job-confirmed{font-size:12px;color:var(--green-d);font-weight:600;align-self:center;}

.wrong-box{flex:1 1 100%;display:none;gap:8px;margin-top:8px;padding-top:10px;border-top:1px dashed var(--line);
  align-items:center;flex-wrap:wrap;}
.wrong-box.open{display:flex;}
.wrong-box input{flex:1 1 240px;height:34px;padding:0 12px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:13px;}

.state{text-align:center;padding:50px 16px;color:var(--ink4);font-size:14px;}
.spinner{width:16px;height:16px;border:2px solid var(--line);border-top-color:var(--blue);border-radius:50%;
  display:inline-block;animation:spin .6s linear infinite;vertical-align:-3px;margin-right:8px;}
@keyframes spin{to{transform:rotate(360deg);}}

.toast-wrap{position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
.toast{background:#fff;border-left:4px solid var(--green);border:1px solid var(--line2);border-left-width:4px;
  box-shadow:0 10px 25px rgba(0,0,0,.12);padding:12px 16px;border-radius:8px;font-size:13px;min-width:240px;transition:.25s;}
.toast.err{border-left-color:var(--red);}
.toast.hide{opacity:0;transform:translateY(8px);}
@media(max-width:640px){ .job-actions{flex:1 1 100%;} }

.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;z-index:9998;padding:16px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:12px;padding:22px;width:100%;max-width:360px;box-shadow:0 20px 50px rgba(0,0,0,.22);}
.modal-title{font-weight:700;font-size:16px;color:var(--ink);}
.modal-sub{font-size:13px;color:var(--ink3);margin:6px 0 14px;}
.modal-label{display:block;font-size:12px;font-weight:600;color:var(--ink3);margin-bottom:6px;}
.modal-date{width:100%;height:42px;padding:0 12px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:15px;}
.modal-date:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(62,106,225,.12);}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:18px;}
.modal-actions .btn{height:40px;}
</style>
</head>
<body>

<div class="topbar">
  <div class="brand">รับเข้าบิล <span class="tag">BILL RECEIVE</span></div>
  <div class="right">
    <a class="back" href="{{ route('oil') }}">← กลับหน้าน้ำมัน</a>
    <span class="user">{{ $loggedInName }}</span>
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
    <button type="button" class="btn btn-primary" id="btnSearch">ค้นหา</button>
    <button type="button" class="btn" id="btnClear">ล้าง</button>
    <span class="hint">ค้นเลขบิลจะไม่สนใจวันที่ที่เลือก</span>
  </div>

  <div class="count-bar" id="countBar"></div>
  <div id="list"></div>
</div>

<!-- Modal ส่งวันใหม่ (ปฏิทิน) -->
<div class="modal-overlay" id="redoModal" onclick="if(event.target===this)closeRedo()">
  <div class="modal-box">
    <div class="modal-title" id="redoTitle">ส่งวันใหม่</div>
    <p class="modal-sub">เลือกวันที่จะส่งใหม่ — ผู้จ่ายงานจะเปลี่ยนเป็นชื่อคุณ</p>
    <label class="modal-label" for="redoDate">วันที่ส่งใหม่</label>
    <input type="date" id="redoDate" class="modal-date">
    <div class="modal-actions">
      <button type="button" class="btn" onclick="closeRedo()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" id="redoConfirmBtn" onclick="submitRedo()">ยืนยันเปลี่ยนวันที่</button>
    </div>
  </div>
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
function escJs(s){ return (s==null?'':String(s)).replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }

function toast(msg, err){
  const w = document.getElementById('toastWrap');
  const t = document.createElement('div');
  t.className = 'toast' + (err?' err':'');
  t.textContent = msg;
  w.appendChild(t);
  setTimeout(()=>{ t.classList.add('hide'); setTimeout(()=>t.remove(),260); }, 3200);
}

function statusInfo(st){
  const s = (st||'').trim();
  if(s === 'จัดส่งสำเร็จ') return {cls:'ok', txt:'สำเร็จ'};
  if(s === 'ค้างบิล')     return {cls:'hold', txt:'ค้างบิล'};
  if(s === 'สินค้าผิด')    return {cls:'wrong', txt:'สินค้าผิด'};
  if(s === 'ส่งใหม่วันพรุ่งนี้') return {cls:'hold', txt:'ส่งใหม่'};
  return {cls:'pending', txt:'รอส่ง'};
}

let currentRows = [];

async function loadData(){
  const q = fBill.value.trim();
  const date = fDate.value;
  listEl.innerHTML = '<div class="state"><span class="spinner"></span>กำลังโหลด...</div>';
  countBar.textContent = '';
  try{
    const url = q !== '' ? `${DATA_URL}?q=${encodeURIComponent(q)}`
                         : `${DATA_URL}?date=${encodeURIComponent(date)}`;
    const res = await fetch(url, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if(!res.ok || !data.ok){ throw new Error(data.message || 'โหลดข้อมูลไม่สำเร็จ'); }
    currentRows = data.rows || [];
    render();
  }catch(e){
    listEl.innerHTML = `<div class="state">เกิดข้อผิดพลาด: ${esc(e.message)}</div>`;
  }
}

function render(){
  if(!currentRows.length){
    listEl.innerHTML = '<div class="state">ไม่พบรายการงาน</div>';
    countBar.textContent = '';
    return;
  }
  const doneN = currentRows.filter(r=>r.confirmed).length;
  countBar.innerHTML = `พบ <b>${currentRows.length}</b> บิล · รับเข้าแล้ว <b>${doneN}</b> · คงเหลือ <b>${currentRows.length-doneN}</b>`;

  listEl.innerHTML = currentRows.map((r,i)=>{
    const si = statusInfo(r.status);
    const typeLabel = r.type==='doc' ? 'บิลชั่วคราว'
                    : (r.type==='private' ? 'บิล · ขนส่งเอกชน' : 'บิล · ส่งโดยบริษัท');
    const meta = [];
    if(r.so_id)         meta.push(`<span class="mi"><b>SO</b> ${esc(r.so_id)}</span>`);
    meta.push(`<span class="mi"><b>ผู้จ่ายงาน</b> ${esc(r.name_pick||'-')}</span>`);
    if(r.time_pick)     meta.push(`<span class="mi"><b>จ่ายเมื่อ</b> ${esc(r.time_pick)}</span>`);
    if(r.driver_name)   meta.push(`<span class="mi"><b>คนขับ</b> ${esc(r.driver_name)}</span>`);
    if(r.transport_name)meta.push(`<span class="mi"><b>ขนส่ง</b> ${esc(r.transport_name)}</span>`);
    if(r.delivery_date) meta.push(`<span class="mi"><b>วันส่ง</b> ${esc(r.delivery_date)}</span>`);

    const actions = r.confirmed
      ? `<div class="job-confirmed">✓ ${esc(si.txt)} · โดย ${esc(r.check_name||'-')}${r.check_time?' · '+esc(r.check_time):''}</div>
         <button type="button" class="act redo" onclick="doRedo(${i})">ส่งวันใหม่</button>`
      : `<button type="button" class="act ok"    onclick="doAction(${i},'ok')">สำเร็จ</button>
         <button type="button" class="act hold"  onclick="doAction(${i},'hold')">ค้างบิล</button>
         <button type="button" class="act redo"  onclick="doRedo(${i})">ส่งวันใหม่</button>
         <button type="button" class="act wrong" onclick="toggleWrong(${i})">สินค้าผิด</button>`;

    return `<div class="job ${r.confirmed?'done':''}" id="job-${i}">
      <div class="job-main">
        <div class="job-line1">
          <span class="job-bill">${esc(typeLabel)} ${esc(r.bill_no||'-')}</span>
          ${r.customer_code?`<span class="job-code">${esc(r.customer_code)}</span>`:''}
          <span class="badge ${si.cls}">${esc(si.txt)}</span>
        </div>
        <div class="job-cust">${esc(r.customer_name||'-')}</div>
        <div class="job-meta">${meta.join('')}</div>
        ${r.note?`<div class="job-note">หมายเหตุ: ${esc(r.note)}</div>`:''}
        <div class="wrong-box" id="wrong-${i}">
          <input type="text" id="wrongnote-${i}" placeholder="ระบุรายละเอียดสินค้าผิด...">
          <button type="button" class="act wrong" onclick="submitWrong(${i})">บันทึกสินค้าผิด</button>
          <button type="button" class="act" onclick="toggleWrong(${i})">ยกเลิก</button>
        </div>
      </div>
      <div class="job-actions">${actions}</div>
    </div>`;
  }).join('');
}

function toggleWrong(i){
  const box = document.getElementById('wrong-'+i);
  if(box){ box.classList.toggle('open'); if(box.classList.contains('open')) document.getElementById('wrongnote-'+i)?.focus(); }
}

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
    const data = await postConfirm({ job_key:r.job_key, action });
    toast(data.message || 'บันทึกแล้ว');
    r.confirmed = true; r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
}

async function submitWrong(i){
  const r = currentRows[i];
  if(!r) return;
  const note = (document.getElementById('wrongnote-'+i)?.value || '').trim();
  if(!note){ toast('กรุณากรอกหมายเหตุสินค้าผิด', true); return; }
  if(!confirm(`ยืนยันบันทึก "สินค้าผิด" สำหรับบิล ${r.bill_no}?`)) return;
  try{
    const data = await postConfirm({ job_key:r.job_key, action:'wrong', note });
    toast(data.message || 'บันทึกแล้ว');
    r.confirmed = true; r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time; r.note = note;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
}

let _redoIndex = null;
function doRedo(i){
  const r = currentRows[i];
  if(!r) return;
  _redoIndex = i;
  document.getElementById('redoTitle').textContent = `ส่งวันใหม่ — บิล ${r.bill_no}`;
  const dp = document.getElementById('redoDate');
  dp.value = new Date().toISOString().split('T')[0];
  document.getElementById('redoModal').classList.add('open');
  // เปิดปฏิทินให้เลย
  setTimeout(()=>{ try{ dp.focus(); dp.showPicker && dp.showPicker(); }catch(e){} }, 60);
}
function closeRedo(){
  document.getElementById('redoModal').classList.remove('open');
  _redoIndex = null;
}
async function submitRedo(){
  if(_redoIndex === null) return;
  const r = currentRows[_redoIndex];
  const d = (document.getElementById('redoDate').value || '').trim();
  if(!/^\d{4}-\d{2}-\d{2}$/.test(d)){ toast('กรุณาเลือกวันที่', true); return; }
  const [y,m,dd] = d.split('-');
  if(!confirm(`ยืนยันเปลี่ยนวันที่ส่งบิล ${r.bill_no} เป็น ${dd}/${m}/${y} ใช่หรือไม่?`)) return;
  const btn = document.getElementById('redoConfirmBtn');
  btn.disabled = true;
  try{
    const data = await postConfirm({ job_key:r.job_key, action:'redo', redo_date:d });
    toast(data.message || 'ส่งใหม่แล้ว');
    closeRedo();
    loadData();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
  finally{ btn.disabled = false; }
}

document.getElementById('btnSearch').addEventListener('click', loadData);
document.getElementById('btnClear').addEventListener('click', ()=>{ fBill.value=''; fDate.value = new Date().toISOString().split('T')[0]; loadData(); });
fBill.addEventListener('keydown', e=>{ if(e.key==='Enter') loadData(); });
fDate.addEventListener('change', ()=>{ if(fBill.value.trim()==='') loadData(); });

document.addEventListener('DOMContentLoaded', ()=>{
  fDate.value = new Date().toISOString().split('T')[0];
  loadData();
});
</script>
</body>
</html>

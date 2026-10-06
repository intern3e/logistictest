{{-- resources/views/account/bill_doc_check.blade.php — ตรวจเอกสารบิล vs ข้อมูลในระบบ (งานบัญชี) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ตรวจเอกสารบิล — บัญชี</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box}
:root{
  --ink:#1f2937; --ink2:#64748b; --line:#e5e7eb; --bg:#f1f5f9; --card:#fff;
  --primary:#3E6AE1; --primary-soft:#eaf0fe;
  --green:#16a34a; --green-soft:#dcfce7; --red:#dc2626; --red-soft:#fee2e2;
  --amber:#d97706; --amber-soft:#fef3c7; --violet:#7c3aed; --violet-soft:#ede9fe;
}
body{margin:0;font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--ink);font-size:14px}
.wrap{max-width:1280px;margin:0 auto;padding:18px 16px 60px}
.mono{font-family:'JetBrains Mono',monospace}
h1{font-size:20px;font-weight:700;margin:0 0 2px}
.sub{color:var(--ink2);font-size:12.5px;margin-bottom:16px}

.panel{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px;box-shadow:0 1px 2px rgba(0,0,0,.03)}
.row{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.fld{display:flex;flex-direction:column;gap:4px}
.fld label{font-size:12px;font-weight:600;color:var(--ink2)}
input[type=month],input[type=text],select{padding:9px 11px;border:1px solid #dee2e6;border-radius:9px;font-family:inherit;font-size:14px;background:#fff}
.btn{padding:9px 16px;border:1px solid var(--line);background:#fff;border-radius:9px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer}
.btn:hover{background:#f8fafc}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:#3358c4}
.btn:disabled{opacity:.6;cursor:not-allowed}

/* quick tick */
.quick{display:flex;gap:10px;align-items:center;flex-wrap:wrap;background:var(--primary-soft);border:1px dashed #b9cbf5;border-radius:12px;padding:12px 14px;margin-top:12px}
.quick input{flex:1;min-width:220px;font-size:16px;font-weight:600}
.quick .hint{font-size:12px;color:var(--ink2)}

/* cards */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:16px}
.card{background:var(--card);border:1px solid var(--line);border-radius:12px;padding:12px 14px}
.card .k{font-size:12px;color:var(--ink2);font-weight:600}
.card .v{font-size:24px;font-weight:700;margin-top:2px}
.card.good{border-color:#bbf7d0}.card.good .v{color:var(--green)}
.card.bad{border-color:#fecaca}.card.bad .v{color:var(--red)}
.card.warn{border-color:#fde68a}.card.warn .v{color:var(--amber)}
.card .mini{font-size:11.5px;color:var(--ink2);margin-top:4px}

/* tabs */
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.tab{padding:7px 14px;border:1px solid var(--line);background:#fff;border-radius:999px;cursor:pointer;font-size:13px;font-weight:600;color:var(--ink2)}
.tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}

/* table */
.table-wrap{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th,td{padding:16px 14px;text-align:left;border-bottom:1px solid var(--line);font-size:14px;vertical-align:top}
th{vertical-align:middle}
th{background:#f8fafc;font-weight:700;color:var(--ink2);position:sticky;top:0;z-index:1}
td.c,th.c{text-align:center}
td.r,th.r{text-align:right}
tr:hover td{background:#fafbff}
.empty{padding:40px;text-align:center;color:var(--ink2)}

.badge{display:inline-block;padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:700}
.b-matched{background:var(--green-soft);color:var(--green)}
.b-missing{background:var(--red-soft);color:var(--red)}
.b-mismatch{background:var(--amber-soft);color:var(--amber)}
.b-docnosys{background:var(--violet-soft);color:var(--violet)}
.b-pending{background:#f1f5f9;color:var(--ink2)}
.b-cancel{background:#111827;color:#fff}
.tag-type{font-size:11.5px;font-weight:700;padding:2px 8px;border-radius:6px}
.tag-good{background:#e0f2fe;color:#0369a1}
.tag-serv{background:#fce7f3;color:#be185d}
.tag-none{background:#f1f5f9;color:#94a3b8}
.chk{width:20px;height:20px;cursor:pointer}
.src{font-size:11px;color:var(--ink2)}
.mismatchline{font-size:11px;color:var(--amber);margin-top:2px}

/* toast */
.toast-wrap{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);display:flex;flex-direction:column;gap:8px;z-index:9999}
.toast{background:#111827;color:#fff;padding:10px 16px;border-radius:10px;font-size:13px;box-shadow:0 8px 24px rgba(0,0,0,.25);animation:pop .2s ease}
.toast.err{background:var(--red)}
.toast.ok{background:var(--green)}
@keyframes pop{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.spin{display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:sp .7s linear infinite;vertical-align:-2px}
@keyframes sp{to{transform:rotate(360deg)}}
/* รายการสินค้าแบบตาราง 4 คอลัมน์: รายการ | จำนวน | ราคา | ยอด (ตรงคอลัมน์กันทุกแถว) */
.items-grid{display:grid;grid-template-columns:1fr 60px 90px 100px;gap:6px 10px;align-items:start}
.ig-h{font-size:11px;color:var(--ink2);font-weight:700;text-align:right;border-bottom:1px solid var(--line);padding-bottom:3px}
.ig-h.ig-name{text-align:left;display:flex;align-items:center;gap:8px}
.ig-name{color:var(--ink);font-size:13px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}
.ig-num{white-space:nowrap;color:var(--ink2);font-family:'JetBrains Mono',monospace;font-size:12px;text-align:right}
.ig-amt{color:var(--ink);font-weight:600;font-size:12.5px}
.ck-row{display:flex;flex-direction:column;gap:5px;align-items:flex-start}
.ck-lbl{display:flex;align-items:center;gap:5px;font-size:12.5px;font-weight:600;cursor:pointer;white-space:nowrap}
.ck-found{color:var(--green)}
.ck-nf{color:var(--red)}
.ck-ns{color:var(--amber)}
.type-foot{margin-top:8px;font-size:12px;color:var(--ink2);display:flex;align-items:center;gap:6px}
.type-sel{padding:5px 8px;border:1px solid #dee2e6;border-radius:8px;font-family:inherit;font-size:12px}
.note-inp{display:block;width:100%;margin-top:8px;padding:7px 9px;border:1px solid #dee2e6;border-radius:8px;font-family:inherit;font-size:12.5px;line-height:1.4;resize:vertical;min-height:38px;white-space:pre-wrap;word-break:break-word;overflow-wrap:anywhere}
.note-inp:focus{outline:none;border-color:var(--primary);background:#f8faff}
.bill-cust{margin-top:8px;padding-top:6px;border-top:1px dotted var(--line)}
.cust-line{font-weight:600;color:var(--ink);white-space:normal;word-break:break-word;line-height:1.35}
.bill-detail{margin-top:6px;padding-top:6px;border-top:1px dotted var(--line);font-size:12px;color:var(--ink2)}
.dt-ln{line-height:1.5}
.dt-k{color:#94a3b8;font-weight:600}
tr.flash td{animation:flashbg 1.1s ease}
@keyframes flashbg{0%{background:#fde68a}60%{background:#fef3c7}100%{background:transparent}}
/* เลขบิลเขียว (เหมือนยอดรวมหลัง VAT) ; ยกเลิก = แดง (ทับ) */
.bill-no{color:var(--green);font-size:15px}
.bill-cancelled{color:var(--red)}
/* พื้นหลังแถวตามประเภท: สินค้า=ฟ้าอ่อน, บริการ=เหลืองอ่อน */
tr.row-goods td{background:#eff6ff}
tr.row-service td{background:#fffbeb}
/* ซ่อนบิลยกเลิก */
.chk-hide{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--ink2);cursor:pointer;align-self:flex-end;padding-bottom:9px}
table.hide-cancel tr.is-cancelled{display:none}
.cancel-tag{display:inline-block;margin-top:4px;background:#111827;color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:999px}
.cancel-reason{margin-top:3px;font-size:12px;color:var(--red);line-height:1.4;white-space:normal}
/* จำนวนเงิน = ชิดขวา ตรงคอลัมน์ยอด */
.amt-foot{margin-top:8px;padding-top:8px;border-top:1px solid var(--line);font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700}
.amt-ln{display:flex;justify-content:flex-end;align-items:baseline;gap:10px;line-height:1.7}
.amt-ln .amt-k{color:var(--ink2);font-family:'Sarabun',sans-serif;font-weight:600}
.amt-v{width:100px;text-align:right;white-space:nowrap}
.amt-before{color:var(--primary)}      /* ก่อน VAT = น้ำเงิน */
.amt-vat{color:var(--amber)}           /* VAT = ส้ม */
.amt-sum{color:var(--green);font-size:15px}  /* รวม = เขียว */
</style>
</head>
<body>
<div class="wrap">
  <h1>ตรวจเอกสารบิล ↔ ข้อมูลในระบบ</h1>
  <div class="sub">งานบัญชี — เช็กว่าเลขบิลในระบบมีเอกสารครบไหม แยกสินค้า/บริการ และหาเลขที่ยังไม่มีเอกสาร · ผู้ใช้: {{ $loginName }}</div>

  <div class="panel">
    <div class="row">
      <div class="fld">
        <label>เดือนที่ตรวจ (เลือกแล้วดึงข้อมูลให้อัตโนมัติ)</label>
        <input type="month" id="fPeriod" value="{{ $thisPeriod }}">
      </div>
      <div class="fld" style="flex:1;min-width:200px">
        <label>ค้นหา / กระโดดเลขรันนิ่ง (เช่น 1100 = แสดงตั้งแต่ 1100)</label>
        <input type="text" id="fQ" placeholder="พิมพ์เลขรันนิ่ง เช่น 1100 แล้วขึ้นตั้งแต่เลขนั้น" inputmode="numeric">
      </div>
      <button class="btn btn-primary" id="btnReload">โหลดใหม่</button>
      <button class="btn" id="btnPdf">สรุป PDF</button>
      <label class="chk-hide"><input type="checkbox" id="hideCancel"> ซ่อนบิลยกเลิก</label>
    </div>

  </div>

  <div class="cards" id="cards"></div>

  <div class="tabs" id="typeTabs">
    <div class="tab active" data-type="">ทั้งหมด</div>
    <div class="tab" data-type="สินค้า">สินค้า</div>
    <div class="tab" data-type="บริการ">บริการ</div>
    <div class="tab" data-type="none">ยังไม่ระบุ</div>
  </div>
  <div class="tabs" id="statusTabs">
    <div class="tab active" data-status="">ทุกสถานะ</div>
    <div class="tab" data-status="missing">ยังไม่ตรวจ</div>
    <div class="tab" data-status="has">มีเอกสาร</div>
    <div class="tab" data-status="notsigned">พบ·ไม่เซ็น</div>
    <div class="tab" data-status="notfound">ไม่พบบิล</div>
    <div class="tab" data-status="noted">มีหมายเหตุ</div>
    <div class="tab" data-status="cancelled">ยกเลิก</div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th class="c" style="width:150px">พบ / หมายเหตุ</th>
          <th style="width:230px">เลขบิล / ลูกค้า</th>
          <th>รายการสินค้า / จำนวนเงิน</th>
          <th style="width:140px">ตรวจโดย</th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr><td colspan="4" class="empty">เลือกเดือนเพื่อดึงข้อมูล</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const URL_DATA = "{{ route('billdoccheck.data') }}";
const URL_SYNC = "{{ route('billdoccheck.sync') }}";
const URL_TICK = "{{ route('billdoccheck.tick') }}";
const URL_TYPE = "{{ route('billdoccheck.type') }}";
const URL_NOTE = "{{ route('billdoccheck.note') }}";
const URL_NOTFOUND = "{{ route('billdoccheck.notfound') }}";
const URL_NOTSIGNED = "{{ route('billdoccheck.notsigned') }}";

const $ = id => document.getElementById(id);
let curType = '', curStatus = '', busy = false;

function esc(s){return (s==null?'':String(s)).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function fmt(n){return n==null?'-':Number(n).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2});}
function toast(msg,type){const w=$('toastWrap');const d=document.createElement('div');d.className='toast '+(type||'');d.textContent=msg;w.appendChild(d);setTimeout(()=>d.remove(),2600);}
function period(){return $('fPeriod').value || '{{ $thisPeriod }}';}

async function post(url,body){
  const r = await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify(body)});
  const d = await r.json().catch(()=>null);
  if(!r.ok || !d || d.ok===false) throw new Error((d&&d.message)||('HTTP '+r.status));
  return d;
}

const STATUS_META = {
  matched:{t:'ข้อมูลตรง',c:'b-matched'},
  missing_doc:{t:'ขาดเอกสาร',c:'b-missing'},
  mismatch:{t:'ข้อมูลไม่ตรง',c:'b-mismatch'},
  doc_no_system:{t:'มีเอกสาร·ไม่มีในระบบ',c:'b-docnosys'},
  pending:{t:'ยังไม่ตรวจ',c:'b-pending'},
};

function renderCards(s){
  if(!s){$('cards').innerHTML='';return;}
  const g=s.goods||{}, sv=s.service||{};
  $('cards').innerHTML = `
    <div class="card"><div class="k">บิลทั้งหมด</div><div class="v">${s.total}</div><div class="mini">ยกเลิก ${s.cancelled}</div></div>
    <div class="card good"><div class="k">มีเอกสาร</div><div class="v">${s.has_document}</div></div>
    <div class="card bad"><div class="k">ขาดเอกสาร</div><div class="v">${s.missing_doc}</div><div class="mini">สินค้า ${g.missing||0} · บริการ ${sv.missing||0}</div></div>
    <div class="card warn"><div class="k">ข้อมูลไม่ตรง</div><div class="v">${s.mismatch}</div></div>
    <div class="card"><div class="k">มีเอกสาร·ไม่มีในระบบ</div><div class="v">${s.doc_no_system}</div></div>
    <div class="card"><div class="k">สินค้า / บริการ / ยังไม่ระบุ</div><div class="v" style="font-size:18px">${g.total||0} / ${sv.total||0} / ${(s.untyped&&s.untyped.total)||0}</div></div>`;
}

function erpStatus(r){
  if(r.cancelled){
    const reason = r.cancel_reason ? `<div class="mismatchline" style="color:#dc2626">เหตุผล: ${esc(r.cancel_reason)}</div>` : '<div class="src" style="color:#dc2626">ไม่ระบุเหตุผล</div>';
    return `<span class="badge b-cancel">ยกเลิก</span>${reason}`;
  }
  return `<span class="src">${esc(r.sys_status_th||'-')}</span>`;
}
function typeTag(t){
  if(t==='สินค้า') return '<span class="tag-type tag-good">สินค้า</span>';
  if(t==='บริการ') return '<span class="tag-type tag-serv">บริการ</span>';
  return '<span class="tag-type tag-none">—</span>';
}

function itemsCell(r){
  const items = r.items||[];
  const nf = n => n!=null?Number(n).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}):'';
  const typeSel = `<select class="type-sel" onchange="onType('${esc(r.bill_no)}',this.value)">
      <option value="" ${!r.bill_type?'selected':''}>— ประเภท —</option>
      <option value="สินค้า" ${r.bill_type==='สินค้า'?'selected':''}>สินค้า</option>
      <option value="บริการ" ${r.bill_type==='บริการ'?'selected':''}>บริการ</option>
    </select>`;
  const list = items.length ? `<div class="items-grid">
    <div class="ig-h ig-name">รายการ ${typeSel}</div><div class="ig-h">จำนวน</div><div class="ig-h">ราคา</div><div class="ig-h">ยอด</div>`
    +items.map(it=>{
      const qty = it.qty!=null?Number(it.qty).toLocaleString('th-TH'):'';
      return `<div class="ig-name">${esc(it.name||'-')}</div><div class="ig-num">${qty}</div><div class="ig-num">${nf(it.price)}</div><div class="ig-num ig-amt">${fmt(it.amount)}</div>`;
    }).join('')+`</div>` : '<span class="src">- ไม่มีรายการ -</span>';
  // ยอดสรุป ชิดขวาตรงคอลัมน์ยอด
  const amt = `<div class="amt-foot">
    <div class="amt-ln"><span class="amt-k">ก่อน VAT</span><span class="amt-v amt-before">${fmt(r.amount_before_vat)}</span></div>
    <div class="amt-ln"><span class="amt-k">VAT</span><span class="amt-v amt-vat">${fmt(r.vat_amount)}</span></div>
    <div class="amt-ln"><span class="amt-k">รวม</span><span class="amt-v amt-sum">${fmt(r.amount)}</span></div>
  </div>`;
  return list + amt;
}

function render(rows){
  const tb=$('tbody');
  if(!rows||!rows.length){tb.innerHTML='<tr><td colspan="4" class="empty">ไม่มีรายการตามเงื่อนไข</td></tr>';return;}
  tb.innerHTML = rows.map(r=>{
    const chk = `<label class="ck-lbl ck-found"><input type="checkbox" class="chk chk-found" ${r.has_document?'checked':''} onchange="onTick('${esc(r.bill_no)}',this.checked)"> พบ</label>`;
    const nf  = `<label class="ck-lbl ck-nf"><input type="checkbox" class="chk chk-nf" ${r.not_found?'checked':''} onchange="onNotFound('${esc(r.bill_no)}',this.checked)"> ไม่พบ</label>`;
    const ns  = `<label class="ck-lbl ck-ns"><input type="checkbox" class="chk chk-ns" ${r.not_signed?'checked':''} onchange="onNotSigned('${esc(r.bill_no)}',this.checked)"> พบ·ไม่เซ็น</label>`;
    const by = byHtml(r);
    const done = (r.has_document||r.not_found) ? 1 : 0;
    // เลขบิล: ยกเลิก = แดง + ป้ายยกเลิก + เหตุผล อยู่ใต้เลขบิล
    const billCls = r.cancelled ? 'bill-cancelled' : '';
    let billSub = r.so_no ? `<div class="src">SO ${esc(r.so_no)}</div>` : '';
    if(r.cancelled){
      billSub += `<div class="cancel-tag">ยกเลิก</div>`;
      billSub += `<div class="cancel-reason">${r.cancel_reason?('เหตุผล: '+esc(r.cancel_reason)):'ไม่ระบุเหตุผล'}</div>`;
    }
    const custLine = `<div class="cust-line">${esc(r.customer_name||'-')}</div>${r.customer_id?`<div class="src">${esc(r.customer_id)}</div>`:''}`;
    const rowCls = (r.cancelled?'is-cancelled ':'') + (r.bill_type==='สินค้า' ? 'row-goods' : (r.bill_type==='บริการ' ? 'row-service' : ''));
    return `<tr data-bill="${esc(r.bill_no)}" data-done="${done}" class="${rowCls}">
      <td class="c">
        <div class="ck-row">${chk}${nf}${ns}</div>
        <textarea class="note-inp" rows="1" placeholder="หมายเหตุ... (ใส่ยาวได้)"
            oninput="autoGrow(this)"
            onchange="onNote('${esc(r.bill_no)}',this.value)">${esc(r.note||'')}</textarea>
      </td>
      <td><b class="mono bill-no ${billCls}">${esc(r.bill_no)}</b>${billSub}<div class="bill-cust">${custLine}</div>${detailHtml(r.detail)}</td>
      <td>${itemsCell(r)}</td>
      <td>${by}</td>
    </tr>`;
  }).join('');
  tb.querySelectorAll('textarea.note-inp').forEach(t=>{ if(t.value.trim()) autoGrow(t); });
  applyHideCancel();
}
function applyHideCancel(){
  const on = $('hideCancel').checked;
  document.querySelector('.table-wrap table').classList.toggle('hide-cancel', on);
}

async function load(){
  if(busy)return; busy=true;
  const tb=$('tbody'); tb.innerHTML='<tr><td colspan="4" class="empty"><span class="spin"></span> กำลังโหลด...</td></tr>';
  try{
    const u = new URL(URL_DATA, location.origin);
    u.searchParams.set('period',period());
    u.searchParams.set('type',curType);
    u.searchParams.set('status',curStatus);
    u.searchParams.set('q',$('fQ').value.trim());
    const r = await fetch(u,{headers:{'Accept':'application/json'}});
    const d = await r.json();
    if(!r.ok||!d.ok) throw new Error(d.message||('HTTP '+r.status));
    renderCards(d.summary); render(d.rows);
  }catch(e){ tb.innerHTML=`<tr><td colspan="4" class="empty" style="color:#dc2626">❌ ${esc(e.message)}</td></tr>`; }
  busy=false;
}

function autoGrow(el){ el.style.height='auto'; el.style.height=(el.scrollHeight+2)+'px'; }
function detailHtml(d){
  if(!d) return '';
  const row=(label,val)=> (val!=null&&String(val).trim()!=='') ? `<div class="dt-ln"><span class="dt-k">${label}</span> ${esc(val)}</div>` : '';
  let parts='';
  parts+=row('ผู้เปิด:', d.opener? (d.opener+(d.opened_at?(' · '+d.opened_at):'')) : null);
  parts+=row('ขนส่ง:', d.transport);
  parts+=row('คนขับ:', d.driver);
  if(d.received_by||d.received_at||d.receive_status){
    const r=[d.received_by, d.received_at, d.receive_status].filter(x=>x&&String(x).trim()!=='').join(' · ');
    parts+=row('รับเข้า:', r);
  }
  if(d.bill_received_by||d.bill_received_at||d.bill_received_st){
    const r=[d.bill_received_by, d.bill_received_at, d.bill_received_st].filter(x=>x&&String(x).trim()!=='').join(' · ');
    parts+=row('รับบิล:', r);
  }
  return parts ? `<div class="bill-detail">${parts}</div>` : '';
}
function byHtml(r){
  return r.checked_by ? `${esc(r.checked_by)}<div class="src">${esc(r.check_source||'')}${r.checked_at?' · '+esc(r.checked_at):''}${r.confidence!=null?' · '+r.confidence+'%':''}</div>` : '-';
}
// เรียงแถวใหม่ในตาราง (ติ๊กแล้วลงล่าง) โดยย้าย node ไม่สร้าง innerHTML ใหม่
function reorderRows(){
  const tb=$('tbody');
  const trs=[...tb.querySelectorAll('tr[data-bill]')];
  trs.sort((a,b)=>{
    const ha=a.dataset.done==='1'?1:0, hb=b.dataset.done==='1'?1:0;
    if(ha!==hb) return ha-hb;
    return a.dataset.bill.localeCompare(b.dataset.bill);
  });
  trs.forEach(tr=>tb.appendChild(tr));   // appendChild = ย้าย node เดิม ไม่รีโหลด
}
function flashRow(tr){ tr.classList.remove('flash'); void tr.offsetWidth; tr.classList.add('flash'); }
// อัปเดตแถวเดียวหลังติ๊ก/หมายเหตุ แบบไม่รีโหลดทั้งตาราง
function applyRowChange(billNo,r){
  const tb=$('tbody');
  const tr=tb.querySelector('tr[data-bill="'+String(billNo).replace(/"/g,'\\"')+'"]');
  if(!tr){ return; }
  const cbF=tr.querySelector('input.chk-found'); if(cbF) cbF.checked=!!r.has_document;
  const cbN=tr.querySelector('input.chk-nf');    if(cbN) cbN.checked=!!r.not_found;
  const cbS=tr.querySelector('input.chk-ns');    if(cbS) cbS.checked=!!r.not_signed;
  tr.dataset.done = (r.has_document||r.not_found)?'1':'0';
  const ni=tr.querySelector('.note-inp'); if(ni && document.activeElement!==ni){ ni.value=r.note||''; autoGrow(ni); }
  tr.cells[tr.cells.length-1].innerHTML = byHtml(r);
  // ถ้าแถวไม่เข้าเงื่อนไขแท็บปัจจุบันแล้ว -> ดีดออกจากตาราง
  let remove=false;
  if(curStatus==='missing' && (r.has_document||r.not_found)) remove=true;
  else if(curStatus==='has' && !r.has_document) remove=true;
  else if(curStatus==='notfound' && !r.not_found) remove=true;
  else if(curStatus==='notsigned' && !r.not_signed) remove=true;
  else if(curStatus==='noted' && !(r.note&&String(r.note).trim())) remove=true;
  if(remove){ tr.style.transition='opacity .25s,transform .25s'; tr.style.opacity='0'; tr.style.transform='translateX(20px)'; setTimeout(()=>tr.remove(),240); return; }
  reorderRows(); flashRow(tr);
}
async function onTick(billNo,has){
  try{ const d=await post(URL_TICK,{period:period(),bill_no:billNo,has:has}); renderCards(d.summary); applyRowChange(billNo,d.row); toast(has?'ติ๊กว่าพบเอกสารแล้ว':'ยกเลิกการติ๊ก','ok'); }
  catch(e){ toast(e.message,'err'); load(); }
}
async function onNotFound(billNo,val){
  try{ const d=await post(URL_NOTFOUND,{period:period(),bill_no:billNo,val:val}); renderCards(d.summary); applyRowChange(billNo,d.row); toast(val?'ทำเครื่องหมาย "ไม่พบบิล"':'ยกเลิก "ไม่พบบิล"','ok'); }
  catch(e){ toast(e.message,'err'); load(); }
}
async function onNotSigned(billNo,val){
  try{ const d=await post(URL_NOTSIGNED,{period:period(),bill_no:billNo,val:val}); renderCards(d.summary); applyRowChange(billNo,d.row); toast(val?'ทำเครื่องหมาย "พบ·ไม่เซ็น"':'ยกเลิก "พบ·ไม่เซ็น"','ok'); }
  catch(e){ toast(e.message,'err'); load(); }
}
async function onType(billNo,type){
  try{ await post(URL_TYPE,{period:period(),bill_no:billNo,type:type||null}); toast('บันทึกประเภทแล้ว','ok');
    const tr=$('tbody').querySelector('tr[data-bill="'+String(billNo).replace(/"/g,'\\"')+'"]');
    if(tr){ tr.classList.remove('row-goods','row-service'); if(type==='สินค้า')tr.classList.add('row-goods'); else if(type==='บริการ')tr.classList.add('row-service'); }
  }
  catch(e){ toast(e.message,'err'); }
}
async function onNote(billNo,note){
  try{ const d=await post(URL_NOTE,{period:period(),bill_no:billNo,note:note}); renderCards(d.summary); applyRowChange(billNo,d.row);
    toast(note.trim()?'บันทึกหมายเหตุ + ติ๊กอัตโนมัติ':'ลบหมายเหตุแล้ว','ok'); }
  catch(e){ toast(e.message,'err'); load(); }
}

// เลือกเดือน / โหลดใหม่ = ดึงบิลจากระบบ (sync) แล้วแสดงให้ครบอัตโนมัติ
async function syncAndLoad(){
  if(busy)return;
  const tb=$('tbody'); tb.innerHTML='<tr><td colspan="4" class="empty"><span class="spin"></span> กำลังดึงบิลเดือน '+esc(period())+' จากระบบ...</td></tr>';
  try{ const d=await post(URL_SYNC,{period:period()}); if(d.message) toast(d.message,'ok'); }
  catch(e){ toast('ดึงจากระบบไม่ได้: '+e.message,'err'); }
  await load();
}
$('btnReload').addEventListener('click', syncAndLoad);
$('fPeriod').addEventListener('change', syncAndLoad);
$('btnPdf').addEventListener('click', ()=>{
  window.open("{{ route('billdoccheck.reportpdf') }}?period="+encodeURIComponent(period()), '_blank');
});
// ซ่อน/แสดง บิลยกเลิก (ฝั่ง client) + จำค่าไว้
try{ $('hideCancel').checked = localStorage.getItem('bdc_hideCancel')==='1'; }catch(e){}
$('hideCancel').addEventListener('change', ()=>{
  try{ localStorage.setItem('bdc_hideCancel', $('hideCancel').checked?'1':'0'); }catch(e){}
  applyHideCancel();
});

let qTimer=null;
// จำค่าค้นหาไว้ (ไม่หายเวลากดปุ่ม/รีโหลดหน้า)
try{ const sv=localStorage.getItem('bdc_q'); if(sv) $('fQ').value=sv; }catch(e){}
$('fQ').addEventListener('input', ()=>{
  try{ localStorage.setItem('bdc_q', $('fQ').value); }catch(e){}
  clearTimeout(qTimer); qTimer=setTimeout(load,350);
});

document.querySelectorAll('#typeTabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('#typeTabs .tab').forEach(x=>x.classList.remove('active'));
  t.classList.add('active'); curType=t.dataset.type; load();
}));
document.querySelectorAll('#statusTabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('#statusTabs .tab').forEach(x=>x.classList.remove('active'));
  t.classList.add('active'); curStatus=t.dataset.status; load();
}));

document.addEventListener('DOMContentLoaded', syncAndLoad);
</script>
</body>
</html>

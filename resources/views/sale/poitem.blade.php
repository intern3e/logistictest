<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ค้นหาราคาต้นทุน</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.po-link{color:var(--blue);font-weight:700;text-decoration:none;cursor:pointer;}
.po-link:hover{text-decoration:underline;}
.modal-bg{display:none;position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.4);align-items:center;justify-content:center;}
.modal-bg.show{display:flex;}
.modal-box{background:#fff;border-radius:12px;width:min(95vw,1200px);max-height:85vh;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.25);animation:popIn .15s ease-out;}
@keyframes popIn{from{opacity:0;transform:scale(.96)}to{opacity:1;transform:none}}
.modal-head{display:flex;align-items:center;padding:12px 16px;background:#f8fafc;border-bottom:1.5px solid var(--line);}
.modal-head .mt{font-weight:700;font-size:14px;color:var(--dark);flex:1;}
.modal-head .mc{border:none;background:#eee;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:14px;color:#888;}
.modal-head .mc:hover{background:#ddd;color:#333;}
.modal-body{overflow:auto;max-height:calc(85vh - 50px);padding:0;}
.modal-body table.ht{min-width:1100px;}
.modal-loading{text-align:center;padding:40px;color:var(--muted);font-size:13px;}
  :root{--blue:#1e50c8;--bg:#f4f5f8;--line:#e2e5eb;--muted:#888;--dark:#222;}
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--dark);min-height:100vh;}
  .topbar{background:#2a2f3a;color:#fff;padding:11px 16px;font-weight:700;font-size:15px;position:sticky;top:0;z-index:100;}
  .wrap{max-width:1400px;margin:0 auto;padding:12px 16px;}

  .card{background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden;margin-bottom:12px;}
  .card-body{padding:14px;}

  textarea.ta{width:100%;padding:10px 12px;min-height:60px;border:1.5px solid var(--line);border-radius:8px;font-family:inherit;font-size:14px;line-height:1.6;resize:vertical;}
  textarea.ta:focus{border-color:var(--blue);outline:none;}
  textarea.ta::placeholder{color:#aaa;}

  .bar{display:flex;gap:8px;margin-top:8px;align-items:center;flex-wrap:wrap;}
  .btn{border:none;border-radius:7px;padding:8px 16px;font-family:inherit;font-weight:600;font-size:13px;cursor:pointer;}
  .btn-p{background:var(--blue);color:#fff;}.btn-p:disabled{background:#aaa;cursor:not-allowed;}
  .btn-s{background:#fff;border:1px solid var(--line);color:#555;padding:7px 12px;font-size:12px;}
  .hint{font-size:11px;color:var(--muted);margin-top:5px;}
  #st{font-size:12px;color:var(--muted);}

  .chips{display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;}
  .chip{padding:3px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#eef0f5;color:#555;}
  .chip.ok{background:#e8f5e9;color:#2e7d32;}.chip.no{background:#fce4ec;color:#c62828;}.chip.ld{background:#fff8e1;color:#f57f17;}

  .no-r{text-align:center;padding:28px;color:var(--muted);font-size:13px;}
  .fuzzy-tag{font-size:10px;padding:2px 8px;border-radius:4px;background:#fff3e0;color:#e65100;font-weight:600;margin-left:6px;}

  .ri{background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden;margin-bottom:10px;}
  .ri-head{display:flex;align-items:center;gap:8px;padding:9px 14px;border-bottom:1px solid var(--line);cursor:pointer;user-select:none;}
  .ri-head:hover{background:#fafbfc;}
  .ri-head .a{font-size:9px;color:#aaa;transition:.15s;}.ri-head.open .a{transform:rotate(90deg);}
  .ri-n{font-weight:700;font-size:13px;flex:1;}
  .ri-c{font-size:11px;color:var(--muted);}
  .ri-body{display:none;}.ri-body.open{display:block;}

  /* ===== section label ===== */
  .sec{padding:8px 14px;font-size:11px;font-weight:700;color:#555;background:#fafbfc;border-bottom:1px solid var(--line);}
  .sec:not(:first-child){border-top:1px solid var(--line);}

  /* ===== ราคาล่าสุด เน้น ===== */
  .sec-latest{
    padding:10px 14px;font-size:13px;font-weight:800;
    color:#1a5e2a;background:#edf7ef;
    border-bottom:2px solid #b6ddc0;
  }
  .sec-latest:not(:first-child){border-top:1px solid var(--line);}
  table.ht-latest td{background:#f6fbf7;}
  table.ht-latest tr:hover td{background:#eef8f0;}
  table.ht-latest .fw{font-weight:800;}

  /* ===== table ===== */
  table.ht{width:100%;border-collapse:collapse;font-size:12px;table-layout:auto;}
  table.ht th{
    padding:7px 10px;text-align:left;font-size:10px;font-weight:700;color:#666;
    text-transform:uppercase;letter-spacing:.03em;border-bottom:1.5px solid var(--line);
    background:#fafbfc;
  }
  table.ht th.r{text-align:right;}
  table.ht td{padding:7px 10px;border-bottom:1px solid #f2f3f6;vertical-align:top;line-height:1.4;word-break:break-word;}
  table.ht tr:last-child td{border-bottom:none;}
  table.ht tr:hover td{background:#f8f9fb;}
  table.ht .r{text-align:right;font-variant-numeric:tabular-nums;}
  table.ht .fw{font-weight:600;}
  table.ht .mu{color:var(--muted);font-size:11px;}
  .best{color:#2e7d32;font-weight:700;}
  .worst{color:#c62828;font-weight:700;}

  .sum{padding:7px 14px;font-size:11px;color:#555;background:#fafbfc;border-top:1px solid var(--line);display:flex;gap:14px;flex-wrap:wrap;}

  @media(max-width:600px){
    .wrap{padding:8px;}
    table.ht{font-size:11px;}
    table.ht th,table.ht td{padding:5px 6px;}
  }
</style>
</head>
<body>
<div class="topbar">ค้นหาราคาต้นทุน</div>
<div class="wrap">

  @if(session('success'))
    <div style="padding:7px 12px;border-radius:7px;font-size:12px;margin-bottom:8px;background:#e8f5e9;color:#2e7d32;">✅ {{ session('success') }}</div>
  @endif

  <div class="card"><div class="card-body">
    <textarea class="ta" id="inputArea" placeholder="พิมพ์ชื่อสินค้า 1 บรรทัด = 1 รายการ"></textarea>
    <div class="chips" id="chips"></div>
    <div class="bar">
      <button class="btn btn-p" id="searchBtn">🔍 ค้นหา</button>
      <button class="btn btn-s" id="clearBtn">ล้าง</button>
      <span id="st"></span>
    </div>
    <div class="hint">1 บรรทัด = 1 รายการ · คลิกข้างนอก หรือกดปุ่มค้นหา</div>
  </div></div>

  <div id="results"><div class="no-r">พิมพ์ชื่อสินค้าแล้วกดค้นหา</div></div>
  <div class="modal-bg" id="poModal" onclick="if(event.target===this)closePo()">
  <div class="modal-box">
    <div class="modal-head">
      <span class="mt" id="poModalTitle">รายละเอียด PO</span>
      <button class="mc" onclick="closePo()">✕</button>
    </div>
    <div class="modal-body" id="poModalBody">
      <div class="modal-loading">⏳ กำลังโหลด...</div>
    </div>
  </div>
</div>
</div>

<script>
const $=s=>document.querySelector(s);
const fmt=n=>(parseFloat(n)||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
const fmtQ=n=>{const v=parseFloat(n)||0;return v===Math.floor(v)?v.toLocaleString('en-US'):v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:4});};
const esc=s=>(s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const TM=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
function thD(ds){if(!ds)return'—';const d=new Date(ds);if(isNaN(d))return ds;return d.getDate()+' '+TM[d.getMonth()]+' '+(d.getFullYear()+543);}
function getItems(){return $('#inputArea').value.split('\n').map(l=>l.trim()).filter(l=>l.length>=1);}
function chips(items,states){$('#chips').innerHTML=items.map((t,i)=>{const s=(states&&states[i])||'';let c='chip';if(s==='ld')c+=' ld';if(s==='ok')c+=' ok';if(s==='no')c+=' no';return`<span class="${c}">${esc(t)}</span>`;}).join('');}

let busy=false,bt=null;
$('#searchBtn').onclick=()=>go();
$('#searchBtn').addEventListener('mousedown',()=>clearTimeout(bt));
$('#inputArea').addEventListener('blur',()=>{clearTimeout(bt);const it=getItems();if(it.length){chips(it);bt=setTimeout(()=>go(),200);}});
$('#clearBtn').onclick=()=>{$('#inputArea').value='';$('#chips').innerHTML='';$('#st').textContent='';$('#results').innerHTML='<div class="no-r">พิมพ์ชื่อสินค้าแล้วกดค้นหา</div>';};

async function go(){
  const items=getItems();if(!items.length||busy)return;
  busy=true;const btn=$('#searchBtn');btn.disabled=true;btn.textContent='⏳ กำลังค้น...';
  $('#st').textContent=`ค้นหา ${items.length} รายการ...`;chips(items,items.map(()=>'ld'));
  try{
    const res=await fetch('{{ route("po.search.api") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({items})});
    if(!res.ok)throw new Error('HTTP '+res.status);
    const data=await res.json();
    chips(items,data.map(d=>d.records&&d.records.length?'ok':'no'));
    const f=data.filter(d=>d.records&&d.records.length).length;
    $('#st').textContent=`พบ ${f}/${items.length}`;
    render(data);
  }catch(e){console.error(e);chips(items,items.map(()=>'no'));$('#st').textContent='❌ '+e.message;}
  btn.disabled=false;btn.textContent='🔍 ค้นหา';busy=false;
}

function tHead(){
  return `<thead><tr>
    <th>#</th>
    <th>วันที่</th>
    <th>เลขที่เอกสาร</th>
    <th>ผู้ขาย</th>
    <th>ชื่อสินค้า</th>
    <th class="r">จำนวน</th>
    <th>หน่วย</th>
    <th class="r">ราคา/หน่วย</th>
    <th class="r">ส่วนลด(%)</th>
    <th class="r">ส่วนลด(฿)</th>
    <th class="r">ส่วนลดบิล(%)</th>
    <th class="r">ส่วนลดบิล(฿)</th>
    <th class="r">ราคา(฿)</th>
    <th>สกุลเงิน</th>
  </tr></thead>`;
}
function tRow(r, pc, num){
  return `<tr>
    <td class="mu" style="text-align:center;white-space:nowrap">${num!=null?num:''}</td>
    <td style="white-space:nowrap">${thD(r.doc_date)}</td>
    <td style="white-space:nowrap"><a href="#" class="po-link" onclick="openPo('${esc(r.doc_no||'').replace(/'/g,"\\'")}','${esc(r.product_name||'').replace(/'/g,"\\'")}');return false;">${esc(r.doc_no||'-')}</a></td>
    <td>${esc(r.vendor_name||'-')}</td>
    <td>${esc(r.product_name||'-')}</td>
    <td class="r" style="white-space:nowrap">${fmtQ(r.qty)}</td>
    <td style="white-space:nowrap">${esc(r.unit||'-')}</td>
    <td class="r fw" style="white-space:nowrap">${fmt(r.unit_price)}</td>
    <td class="r" style="white-space:nowrap">${r.item_discount_pct!=null&&r.item_discount_pct!==''?esc(String(r.item_discount_pct)):'—'}</td>
    <td class="r" style="white-space:nowrap">${r.item_discount_amt!=null&&r.item_discount_amt!==''?esc(String(r.item_discount_amt)):'—'}</td>
    <td class="r" style="white-space:nowrap">${r.bill_discount_pct!=null&&r.bill_discount_pct!==''?esc(String(r.bill_discount_pct)):'—'}</td>
    <td class="r" style="white-space:nowrap">${r.bill_discount_amt!=null&&r.bill_discount_amt!==''?esc(String(r.bill_discount_amt)):'—'}</td>
    <td class="r fw ${pc}" style="white-space:nowrap">${fmt(r.unit_price_thb||r.unit_price)}</td>
    <td class="mu" style="white-space:nowrap">${esc(r.currency||'THB')}</td>
  </tr>`;
}
function render(data){
  const c=$('#results');if(!data.length){c.innerHTML='<div class="no-r">ไม่มีรายการ</div>';return;}
  let h='';
  data.forEach((item,idx)=>{
    const kw=item.keyword||'',recs=item.records||[],latest=item.latest;
    if(!recs.length){
      h+=`<div class="ri"><div class="ri-head"><span class="ri-n">${esc(kw)}</span><span class="ri-c" style="color:#c62828;">ไม่พบ</span></div></div>`;
      return;
    }

    const allP=recs.map(r=>parseFloat(r.unit_price_thb||r.unit_price)||0).filter(p=>p>0);
    const mn=allP.length?Math.min(...allP):0,mx=allP.length?Math.max(...allP):0;
    const isFuzzy = item.method === 'fuzzy';

    h+=`<div class="ri" id="ri-${idx}">
      <div class="ri-head open" onclick="tog(${idx})">
        <span class="a">▶</span>
        <span class="ri-n">${esc(kw)}${isFuzzy?'<span class="fuzzy-tag">ค้นแบบใกล้เคียง</span>':''}</span>
        <span class="ri-c">${recs.length} รายการ</span>
      </div>
      <div class="ri-body open" id="rb-${idx}">

        ${latest?`
        <div class="sec-latest">⬇ ราคาล่าสุดที่ซื้อ</div>
        <table class="ht ht-latest">
          ${tHead()}
          <tbody>${tRow(latest,'',1)}</tbody>
        </table>`:''}

        <div class="sec">ประวัติทั้งหมด (${recs.length})</div>
        ${sumRow(recs)}
        <div style="max-height:500px;overflow-y:auto;">
          <table class="ht">
            ${tHead()}
            <tbody>${recs.map((r,i)=>{
              const p=parseFloat(r.unit_price_thb||r.unit_price)||0;
              let pc='';if(mn>0&&mx>0&&mn!==mx){if(p<=mn)pc='best';else if(p>=mx)pc='worst';}
              return tRow(r,pc,i+1);
            }).join('')}</tbody>
          </table>
        </div>

      </div>
    </div>`;
  });
  c.innerHTML=h;
}

function sumRow(recs){
  const ps=recs.map(r=>parseFloat(r.unit_price_thb||r.unit_price)||0).filter(p=>p>0);
  if(!ps.length)return'';
  const mn=Math.min(...ps),mx=Math.max(...ps);
  const discCount=recs.filter(r=>(parseFloat(r.item_discount_pct)||0)>0||(parseFloat(r.item_discount_amt)||0)>0).length;
  return `<div class="sum">ต่ำสุด <b>${fmt(mn)}</b> · สูงสุด <b>${fmt(mx)}</b>${discCount?` · มีส่วนลด <b>${discCount}</b> รายการ`:''}</div>`;
}
function tog(i){const h=document.querySelector(`#ri-${i} .ri-head`),b=document.getElementById(`rb-${i}`);if(h)h.classList.toggle('open');if(b)b.classList.toggle('open');}
window.addEventListener('DOMContentLoaded',()=>$('#inputArea').focus());
const poCache={};
async function openPo(docNo, highlightName){
  if(!docNo||docNo==='-') return;
  const modal=$('#poModal');
  const title=$('#poModalTitle');
  const body=$('#poModalBody');

  title.textContent=`📋 ${docNo}`;
  body.innerHTML='<div class="modal-loading">⏳ กำลังโหลด...</div>';
  modal.classList.add('show');

  if(poCache[docNo]){
    renderPoDetail(poCache[docNo], docNo, highlightName);
    return;
  }

  try{
    const res=await fetch('{{ route("po.detail.api") }}',{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
      },
      body:JSON.stringify({doc_no:docNo})
    });
    if(!res.ok) throw new Error('HTTP '+res.status);
    const data=await res.json();
    poCache[docNo]=data;
    renderPoDetail(data, docNo, highlightName);
  }catch(e){
    body.innerHTML=`<div class="modal-loading" style="color:#c62828">❌ โหลดไม่สำเร็จ: ${esc(e.message)}</div>`;
  }
}

function renderPoDetail(records, docNo, highlightName){
  const body=$('#poModalBody');
  if(!records||!records.length){
    body.innerHTML='<div class="modal-loading">ไม่พบรายการใน PO นี้</div>';
    return;
  }

  const vendor=records[0].vendor_name||'-';
  const date=thD(records[0].doc_date);
  const poTotal=records[0].po_total;
  const hlLower=(highlightName||'').toLowerCase();

  let h=`<div style="padding:10px 16px;font-size:12px;color:#555;background:#fafbfc;border-bottom:1px solid var(--line);">
    📅 ${date} · 🏢 ${esc(vendor)} · ${records.length} รายการ${poTotal?' · รวม PO <b>'+raw(poTotal)+'</b> บาท':''}
  </div>`;

  h+='<div style="overflow-x:auto;"><table class="ht">';
  h+=`<thead><tr>
    <th>#</th>
    <th>รหัสสินค้า</th>
    <th>ชื่อสินค้า</th>
    <th class="r">จำนวน</th>
    <th>หน่วย</th>
    <th class="r">ราคา/หน่วย</th>
    <th class="r">ส่วนลด(%)</th>
    <th class="r">ส่วนลด(฿)</th>
    <th class="r">เงินสินค้า</th>
    <th class="r">รวม PO</th>
    <th class="r">ส่วนลดบิล(%)</th>
    <th class="r">ส่วนลดบิล(฿)</th>
    <th class="r">ก่อนภาษี</th>
    <th class="r">ภาษีซื้อ</th>
    <th class="r">รวมทั้งสิ้น</th>
    <th class="r">ราคา(฿)</th>
    <th>สกุลเงิน</th>
  </tr></thead><tbody>`;

  records.forEach((r,i)=>{
    const isHL = hlLower && (r.product_name||'').toLowerCase()===hlLower;
    const rowStyle = isHL ? 'background:#fff8e1;' : '';
    const nameStyle = isHL ? 'font-weight:800;color:#e65100;' : '';

    h+=`<tr style="${rowStyle}">
      <td class="mu" style="text-align:center">${i+1}</td>
      <td style="white-space:nowrap">${esc(r.product_code||'-')}</td>
      <td style="${nameStyle}">${esc(r.product_name||'-')}${isHL?' <span style="font-size:10px;background:#ffe082;color:#e65100;border-radius:4px;padding:1px 6px;font-weight:700;">◀ สินค้าที่ค้นหา</span>':''}</td>
      <td class="r" style="white-space:nowrap">${fmtQ(r.qty)}</td>
      <td style="white-space:nowrap">${esc(r.unit||'-')}</td>
      <td class="r fw" style="white-space:nowrap">${fmt(r.unit_price)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.item_discount_pct)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.item_discount_amt)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.item_amount)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.po_total)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.bill_discount_pct)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.bill_discount_amt)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.before_tax)}</td>
      <td class="r" style="white-space:nowrap">${raw(r.input_tax)}</td>
      <td class="r fw" style="white-space:nowrap">${raw(r.grand_total)}</td>
      <td class="r fw" style="white-space:nowrap">${fmt(r.unit_price_thb||r.unit_price)}</td>
      <td class="mu" style="white-space:nowrap">${esc(r.currency||'THB')}</td>
    </tr>`;
  });

  h+='</tbody></table></div>';
  body.innerHTML=h;
}
function raw(v){
  if(v==null||v==='') return '—';
  const s=String(v).trim();
  // ถ้าเป็นตัวเลข (อาจมี % ท้าย) → format ใส่ comma
  const numPart=s.replace(/%$/,'');
  const n=parseFloat(numPart);
  if(!isNaN(n) && numPart===numPart.trim()){
    const formatted=n.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    return s.endsWith('%') ? formatted+'%' : formatted;
  }
  return esc(s);
}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closePo();});
function closePo(){
  $('#poModal').classList.remove('show');
}
</script>
</body>
</html>
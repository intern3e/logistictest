<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบสร้างใบเสนอราคา (Quotation Generator)</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<style>
  :root{
    --navy:#1f3a93; --navy2:#16306f; --blue:#1e50c8; --bg:#eef1f6; --line:#dfe4ec;
    --green:#16a34a; --muted:#6b7280; --soft:#f7f9fc; --docline:#6c6cf0;
  }
  *{box-sizing:border-box;}
  body{margin:0;font-family:'Sarabun',sans-serif;background:var(--bg);color:#1f2937;}
  .topbar{background:linear-gradient(90deg,var(--navy2),var(--blue));color:#fff;padding:14px 22px;font-weight:600;font-size:18px;display:flex;align-items:center;gap:10px;}
  .topbar .dot{width:26px;height:26px;border-radius:6px;background:#fff3;display:flex;align-items:center;justify-content:center;}
  .wrap{display:grid;grid-template-columns:1fr;gap:18px;padding:18px;max-width:900px;margin:0 auto;}
  .card{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px #0000000a;}
  .tabs{display:flex;border-bottom:1px solid var(--line);}
  .tab{flex:1;padding:14px;text-align:center;cursor:pointer;font-weight:600;color:var(--muted);border-bottom:3px solid transparent;}
  .tab.active{color:var(--blue);border-bottom-color:var(--blue);}
  .pane{padding:18px;display:none;}
  .pane.active{display:block;}
  .drop{border:2px dashed #c7d0df;border-radius:12px;padding:26px;text-align:center;background:var(--soft);cursor:pointer;transition:.15s;}
  .drop:hover{border-color:var(--blue);background:#f0f5ff;}
  .drop img{max-width:100%;max-height:230px;border-radius:8px;margin-bottom:8px;}
  .muted{color:var(--muted);font-size:13px;}
  .sec-title{font-weight:700;color:var(--navy);margin:18px 0 6px;border-bottom:1px solid var(--line);padding-bottom:4px;}
  .search-wrap{position:relative;display:flex;gap:6px;}
  .search-wrap input{flex:1;}
  .ac-list{position:absolute;top:100%;left:0;right:0;z-index:50;background:#fff;border:1px solid var(--line);border-radius:8px;box-shadow:0 6px 20px #0002;max-height:220px;overflow:auto;margin-top:4px;display:none;}
  .ac-list .ac-item{padding:10px 12px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;line-height:1.4;}
  .ac-list .ac-item:last-child{border-bottom:none;}
  .ac-list .ac-item:hover{background:#f0f5ff;}
  .ac-list .ac-item .ac-name{font-weight:700;color:#111;}
  .ac-list .ac-item .ac-sub{color:var(--muted);font-size:12px;}
  .ac-list .ac-empty{padding:14px;text-align:center;color:var(--muted);font-size:13px;}

  label{display:block;font-size:13px;font-weight:600;margin:10px 0 4px;}
  input,textarea,select{width:100%;padding:9px 11px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:14px;background:#fff;}
  textarea{resize:vertical;}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:none;border-radius:8px;padding:11px 16px;font-family:inherit;font-weight:600;font-size:14px;cursor:pointer;}
  .btn-primary{background:var(--blue);color:#fff;width:100%;}
  .btn-primary:hover{background:var(--navy2);}
  .btn-green{background:var(--green);color:#fff;}
  .btn-ghost{background:#fff;border:1px solid var(--line);color:#374151;}
  .btn-row{display:flex;gap:10px;margin-top:14px;}
  .progress{height:8px;background:#e5e9f0;border-radius:6px;overflow:hidden;margin-top:10px;display:none;}
  .progress span{display:block;height:100%;width:0;background:var(--blue);transition:.2s;}
  .hint{background:#fff8e6;border:1px solid #f3e2b0;border-radius:10px;padding:12px 14px;font-size:13px;color:#7a5d00;margin-top:14px;}
  .ocr-stats{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-top:12px;font-size:13px;}
  .ocr-stats .os-head{font-weight:700;color:#15803d;margin-bottom:6px;}
  .ocr-stats .os-line{display:flex;align-items:center;gap:8px;margin:4px 0;font-size:12px;}
  .ocr-stats .os-bar{flex:0 0 80px;height:8px;background:#e5e9f0;border-radius:4px;overflow:hidden;}
  .ocr-stats .os-fill{height:100%;border-radius:4px;transition:.3s;}
  .ocr-stats .os-num{flex:0 0 36px;text-align:right;font-weight:600;}
  .ocr-stats .os-txt{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#555;}

  /* === Items Table === */
  .items-table-wrap{overflow-x:auto;margin:8px 0;border:1px solid var(--line);border-radius:10px;background:#fff;}
  .items-table{width:100%;border-collapse:collapse;font-size:13px;}
  .items-table thead th{
    background:linear-gradient(180deg,#f8fafc,#f1f4f9);
    padding:10px 8px;font-weight:700;font-size:12px;color:var(--navy);
    text-align:left;border-bottom:2px solid var(--line);white-space:nowrap;
    text-transform:uppercase;letter-spacing:.03em;
  }
  .items-table thead th.c{text-align:center;}
  .items-table thead th.r{text-align:right;}
  .items-table tbody td{padding:4px 4px;border-bottom:1px solid #f0f2f5;vertical-align:top;}
  .items-table tbody tr:last-child td{border-bottom:none;}
  .items-table tbody tr:hover td{background:#f8faff;}
  .items-table .td-num{width:38px;text-align:center;color:var(--blue);font-weight:700;font-size:12px;cursor:pointer;user-select:none;transition:.15s;vertical-align:middle;}
  .items-table .td-num:hover{background:#eef2ff;border-radius:4px;}
  .items-table .td-num.no-hist{color:#ccc;cursor:default;}
  .items-table .td-num.no-hist:hover{background:none;}
  .items-table .td-desc{min-width:180px;}

  /* new-tag */
  .new-tag{font-size:10px;color:#b45309;background:#fef3c7;border:1px solid #fde68a;border-radius:4px;padding:1px 5px;margin-top:2px;display:inline-block;line-height:1.4;}

  /* === hist popover === */
  .hist-popover{
    position:absolute;z-index:500;background:#fff;border:1px solid var(--line);border-radius:12px;
    box-shadow:0 12px 40px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04);
    width:min(680px,90vw);max-height:380px;overflow:hidden;display:none;
    animation:popIn .15s ease-out;
  }
  @keyframes popIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
  .hist-popover .hp-head{font-weight:700;font-size:12px;color:var(--navy);display:flex;gap:6px;flex-wrap:wrap;align-items:center;flex:1;}
  .hist-popover .hp-head .mm-tag{background:#dbeafe;color:var(--navy);padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;}
  .hist-popover .hp-close{border:none;background:#f3f4f6;border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:13px;color:#6b7280;flex-shrink:0;}
  .hist-popover .hp-close:hover{background:#e5e7eb;color:#111;}
  .hist-popover .hp-body{overflow:auto;max-height:310px;-webkit-overflow-scrolling:touch;}
  .hist-popover table{width:100%;border-collapse:collapse;font-size:11.5px;min-width:500px;}
  .hist-popover th{padding:7px 6px;font-weight:700;color:#374151;border-bottom:2px solid var(--line);text-align:left;white-space:nowrap;background:#f8fafc;position:sticky;top:0;z-index:1;}
  .hist-popover td{padding:6px 6px;border-bottom:1px solid #f0f2f5;}
  .hist-popover tr:hover td{background:#f8faff;}

  .items-table .td-qty{width:76px;}
  .items-table .td-unit{width:76px;}
  .items-table .td-price{width:108px;}
  .items-table .td-amt{width:108px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:var(--navy);white-space:nowrap;padding-right:10px;font-size:13px;vertical-align:middle;}
  .items-table .td-act{width:36px;text-align:center;vertical-align:middle;}

  .items-table input{
    width:100%;padding:7px 8px;border:1px solid transparent;border-radius:6px;
    font-size:13px;font-family:inherit;background:transparent;transition:border-color .15s,background .15s;
  }
  .items-table input:hover{border-color:var(--line);background:#fff;}
  .items-table input:focus{border-color:var(--blue);background:#fff;outline:none;box-shadow:0 0 0 2px rgba(30,80,200,.1);}
  .items-table input[type=number]{text-align:right;font-variant-numeric:tabular-nums;}
  .items-table input::placeholder{color:#bbb;font-weight:400;}
  .items-table .del{
    width:26px;height:26px;background:none;border:1px solid transparent;border-radius:6px;
    cursor:pointer;color:#ccc;font-size:13px;transition:.15s;display:inline-flex;align-items:center;justify-content:center;
  }
  .items-table .del:hover{background:#fef2f2;border-color:#fecaca;color:#b91c1c;}
  .items-table tfoot td{padding:10px 8px;font-weight:700;font-size:13px;border-top:2px solid var(--line);background:#f8fafc;}

  /* ===== batch-match status bar ===== */
  .match-bar{display:none;align-items:center;gap:8px;padding:8px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;margin-top:8px;font-size:12px;color:var(--navy);}
  .match-bar .spin{animation:spin .8s linear infinite;display:inline-block;}
  @keyframes spin{to{transform:rotate(360deg)}}

  /* === Document preview === */
  .doc-tools{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--line);}
  .doc-scroll{padding:18px;background:#f1f4f9;overflow:auto;}
  .doc{background:#fff;width:100%;max-width:760px;margin:0 auto;padding:44px 46px;box-shadow:0 2px 14px #0003;font-size:13px;color:#1a1a1a;}
  .doc.page{display:flex;flex-direction:column;min-height:1000px;}
  .doc.page + .doc.page{margin-top:22px;}
  .flexspace{flex:1 1 auto;min-height:24px;}

  .qhead{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;}
  .qhead .qbig{font-size:38px;font-weight:800;color:var(--navy);letter-spacing:-1px;line-height:1;text-align:right;}
  .qmeta{font-size:12px;color:#333;margin-top:8px;line-height:1.5;}
  .seller-top{line-height:1.45;}
  .seller-top .co{font-size:18px;font-weight:800;color:#111;}
  .seller-top .sln{font-size:11.5px;color:#333;}
  .bluebar{background:none;color:#1f3a93;font-weight:800;padding:6px 0;margin:14px 0 22px;font-size:26px;letter-spacing:1px;border-bottom:2px solid #1f3a93;text-align:center;}

  .cust-head{font-weight:700;font-size:14px;margin-bottom:8px;display:flex;align-items:center;gap:8px;}
  .cinfo-table{width:100%;border-collapse:collapse;font-size:12px;margin-bottom:20px;}
  .cinfo-table td{padding:3px 6px;vertical-align:top;}
  .cinfo-table .ck{color:#444;font-weight:500;white-space:nowrap;width:1%;padding-right:4px;}
  .cinfo-table .cv{font-weight:600;color:#111;}

  table.itbl{width:100%;border-collapse:collapse;font-size:13px;}
  .itbl th{border-bottom:2px solid #333;padding:8px 6px;font-weight:700;color:#222;}
  .itbl td{padding:7px 6px;}
  .itbl th.l,.itbl td.l{text-align:left;}
  .itbl th.c,.itbl td.c{text-align:center;}
  .itbl th.r,.itbl td.r{text-align:right;}
  .itbl tr.empty-row td{height:24px;}

  .ctot{margin-left:auto;width:300px;font-size:13px;}
  .ctot .tr{display:flex;justify-content:space-between;padding:3px 0;}
  .ctot .tr .lbl{color:#333;}
  .ctot .grand{border-top:2px solid var(--navy);margin-top:4px;padding-top:8px;font-size:17px;font-weight:800;color:var(--navy);}
  .ctot .baht{text-align:right;font-size:12px;color:#444;margin-top:2px;}

  .cfoot{display:flex;justify-content:space-between;gap:30px;align-items:flex-end;margin-top:50px;}
  .ssign{display:flex;gap:120px;}
  .sg{flex:1;width:230px;text-align:center;font-size:12px;position:relative;}
  .sg .sgline{border-top:1px solid #555;margin:40px 14px 4px;padding-top:4px;}
  .sg .sig-img{position:absolute;left:50%;bottom:28px;transform:translateX(-50%);height:70px;pointer-events:none;}

  .page-footer-keep{page-break-inside:avoid;break-inside:avoid;}

  @media print{
    @page{size:A4;margin:0;}
    .topbar,.doc-tools,.hist-popover{display:none!important;}
    .wrap{display:block!important;padding:0;margin:0;max-width:none;gap:0;}
    .wrap>.card:first-child{display:none!important;}
    .card{border:none!important;box-shadow:none!important;border-radius:0;}
    .doc-scroll{padding:0!important;background:#fff!important;max-height:none!important;overflow:visible!important;}
    .doc.page{box-shadow:none!important;max-width:none!important;width:100%!important;margin:0!important;padding:10mm 12mm!important;display:flex!important;flex-direction:column!important;min-height:0!important;height:287mm!important;page-break-after:always;}
    .doc.page:last-child{page-break-after:auto;}
    .flexspace{flex:1 1 auto!important;min-height:0!important;display:block!important;}
    .page-footer-keep{page-break-inside:avoid!important;break-inside:avoid!important;}
    .ssign{flex-direction:row!important;gap:120px!important;}
    .cfoot{flex-direction:row!important;}
  }

  @media(max-width:767px){
    .wrap{padding:10px;gap:12px;}
    .pane{padding:14px;}
    .doc-scroll{padding:10px;max-height:none;}
    .doc{padding:16px 14px;}
    .row{grid-template-columns:1fr;}
    .ssign{flex-direction:column;gap:30px;}
  }
</style>
</head>
<body>
<div class="topbar"><span class="dot">📄</span> ระบบสร้างใบเสนอราคา · Quotation Generator</div>

<div class="wrap">
  <div class="card">
    <div class="tabs">
      <div class="tab active" data-tab="upload">🖼️ อัพโหลดรูปภาพ (OCR)</div>
      <div class="tab" data-tab="manual">⌨️ พิมพ์ข้อมูล</div>
    </div>

    {{-- ========== TAB: OCR ========== --}}
    <div class="pane active" id="pane-upload">
      <div class="drop" id="drop">
        <div id="dropEmpty">
          <div style="font-size:34px">⬆️</div>
          <div style="font-weight:600;margin-top:6px">คลิกหรือลากรูปใบเสนอราคามาวาง</div>
          <div class="muted">รองรับ JPG, PNG · ระบบจะอ่านข้อความ (OCR) ภาษาไทย/อังกฤษ</div>
        </div>
        <img id="preview" style="display:none">
      </div>
      <input type="file" id="file" accept="image/*" hidden>
      <div class="progress" id="prog"><span></span></div>
      <div class="btn-row">
        <button class="btn btn-primary" id="ocrBtn">🔍 อ่านข้อความจากรูป (OCR)</button>
      </div>
      <div id="ocrStats" style="display:none"></div>
      <label>ข้อความที่อ่านได้ / วางข้อความเอง</label>
      <textarea id="ocrText" rows="2" placeholder="ผลลัพธ์ OCR จะแสดงที่นี่ — แก้ไขได้" style="min-height:60px;overflow:hidden;"></textarea>
      <div class="btn-row">
        <button class="btn btn-green" id="parseBtn" style="flex:1">✨ แยกรายการอัตโนมัติ → กรอกฟอร์ม</button>
      </div>
      <div class="hint">
        <b>เคล็ดลับ:</b> อ่าน OCR → แยกรายการสินค้า → <b>เลือกบริษัทลูกค้า</b> ระบบจะดึงราคาขายครั้งล่าสุดให้อัตโนมัติ
        · ถ้าสินค้าไม่เคยขายลูกค้ารายนั้นจะแสดง <span class="new-tag">🆕 สินค้าใหม่</span>
      </div>
    </div>

    {{-- ========== TAB: Manual ========== --}}
    <div class="pane" id="pane-manual">
      <div class="sec-title">ข้อมูลเอกสาร</div>
      <label>วันที่</label><input id="docDate" type="date">

      <div class="sec-title">ข้อมูลลูกค้า</div>
      <div class="row">
        <div><label>รหัสลูกค้า</label><input id="custCode" placeholder="เลือกจากชื่อบริษัท" readonly style="background:#f7f9fc;color:var(--navy);font-weight:600;"></div>
        <div><label>ชื่อผู้ติดต่อ</label><input id="contactName" placeholder="ชื่อผู้ติดต่อ"></div>
      </div>
      <label>ชื่อบริษัท</label>
      <div class="search-wrap">
        <input id="custCompany" placeholder="พิมพ์ชื่อบริษัท (อย่างน้อย 3 ตัวอักษร)">
        <div class="ac-list" id="acList"></div>
      </div>
      <label>ที่อยู่</label>
      <textarea id="custAddr" rows="2" placeholder="ที่อยู่ลูกค้า ..."></textarea>
      <div class="row">
        <div><label>โทร.</label><input id="custTel" placeholder="0xx-xxx-xxxx"></div>
        <div><label>เลขประจำตัวผู้เสียภาษี</label><input id="custTax" placeholder=""></div>
      </div>
      <div class="row">
        <div><label>สาขา</label><input id="custBranch" placeholder="สำนักงานใหญ่"></div>
        <div><label>ยืนราคาภายใน (วัน)</label><input id="validDays" placeholder="30"></div>
      </div>
      <div class="row">
        <div><label>Expire Date</label><input id="expireDate" type="date"></div>
        <div><label>จำนวนวันเครดิต</label><input id="creditDays" placeholder=""></div>
      </div>

      <div class="sec-title">รายการสินค้า / บริการ</div>

      {{-- batch-match status --}}
      <div class="match-bar" id="matchBar">
        <span class="spin">⏳</span>
        <span id="matchBarTxt">กำลังดึงราคาจากประวัติการขาย...</span>
      </div>

      <div class="items-table-wrap">
        <table class="items-table">
          <thead>
            <tr>
              <th class="c" style="width:38px">#</th>
              <th>รายการสินค้า</th>
              <th class="c" style="width:76px">จำนวน</th>
              <th class="c" style="width:76px">หน่วย</th>
              <th class="r" style="width:108px">ราคา/หน่วย</th>
              <th class="r" style="width:108px">จำนวนเงิน</th>
              <th class="c" style="width:36px"></th>
            </tr>
          </thead>
          <tbody id="itemRows"></tbody>
          <tfoot id="itemFoot" style="display:none">
            <tr>
              <td colspan="5" style="text-align:right;color:var(--navy);">รวมก่อน VAT</td>
              <td style="text-align:right;color:var(--navy);font-variant-numeric:tabular-nums;" id="footGross">0.00</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <button class="btn btn-ghost" id="addItem" style="margin-top:6px">➕ เพิ่มรายการ</button>

      <label style="margin-top:14px">หมายเหตุ</label>
      <textarea id="note" rows="2" placeholder="หมายเหตุ ..."></textarea>

      <div class="sec-title">ลายเซ็นผู้เสนอราคา</div>
      <canvas id="sigPad" width="380" height="120" style="border:1px solid var(--line);border-radius:8px;cursor:crosshair;background:#fff;display:block;width:100%;touch-action:none;"></canvas>
      <div class="btn-row">
        <button class="btn btn-ghost" id="sigClear" style="flex:1;font-size:12px">🗑️ ล้างลายเซ็น</button>
      </div>
    </div>
  </div>

  {{-- ========== Document Preview ========== --}}
  <div class="card">
    <div class="doc-tools">
      <b>📑 ตัวอย่างใบเสนอราคา</b>
      <div style="display:flex;gap:8px">
        <button class="btn btn-ghost" id="printBtn">🖨️ พิมพ์ / บันทึก PDF</button>
        <button class="btn btn-green" id="refreshBtn">↻ อัปเดต</button>
      </div>
    </div>
    <div class="doc-scroll">
      <div id="pages"></div>
    </div>
  </div>
</div>

{{-- ========== Sales History Popover ========== --}}
<div class="hist-popover" id="histPopover">
  <div style="display:flex;align-items:center;padding:10px 14px;background:linear-gradient(135deg,#eef2ff,#f8faff);border-bottom:1px solid var(--line);">
    <div class="hp-head" id="hpHead"></div>
    <button class="hp-close" onclick="document.getElementById('histPopover').style.display='none'">✕</button>
  </div>
  <div class="hp-body" id="hpBody"></div>
</div>

{{-- ================================================================
     JAVASCRIPT
     ================================================================ --}}
<script>
/* ---- shortcuts ---- */
const $  = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

/* ================================================================
   SELLER INFO
   ================================================================ */
const SELLER = {
  name:      'บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด',
  tel:       '02-4727341-48',
  addrShort: 'เลขที่ 39/7 ถนนวุฒากาส แขวงตลาดพลู เขตธนบุรี กรุงเทพฯ 10600',
  tax:       '0105547065721',
};

/* ================================================================
   PAGINATION CONFIG
   ================================================================ */
const FIRST_PAGE_MAX = 14;
const OTHER_PAGE_MAX = 24;

function paginate(total) {
  if (total <= FIRST_PAGE_MAX) return [total];
  const sizes = [FIRST_PAGE_MAX];
  let rem = total - FIRST_PAGE_MAX;
  while (rem > OTHER_PAGE_MAX) { sizes.push(OTHER_PAGE_MAX); rem -= OTHER_PAGE_MAX; }
  if (rem > 0) sizes.push(rem);
  return sizes;
}

/* ================================================================
   STATE
   ================================================================ */
let currentCustomerCode = '';   // set เมื่อเลือกบริษัท
let sellerSigData       = null;

/* ================================================================
   HELPERS
   ================================================================ */
function esc(s)  { return (s + '').replace(/"/g, '&quot;'); }
function esc2(s) { return (s + '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmt(n)  { return (Math.round(n * 100) / 100).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); }

function autoResize(el) { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; }

/* ================================================================
   TABS
   ================================================================ */
$$('.tab').forEach(t => t.onclick = () => {
  $$('.tab').forEach(x => x.classList.remove('active'));
  $$('.pane').forEach(x => x.classList.remove('active'));
  t.classList.add('active');
  $('#pane-' + t.dataset.tab).classList.add('active');
});

/* ================================================================
   IMAGE UPLOAD + OCR
   ================================================================ */
let imgData = null;
$('#drop').onclick    = () => $('#file').click();
$('#drop').ondragover = e => e.preventDefault();
$('#drop').ondrop     = e => { e.preventDefault(); handleFile(e.dataTransfer.files[0]); };
$('#file').onchange   = e => handleFile(e.target.files[0]);

function handleFile(f) {
  if (!f) return;
  const r = new FileReader();
  r.onload = () => {
    imgData = r.result;
    $('#preview').src = imgData;
    $('#preview').style.display = 'block';
    $('#dropEmpty').style.display = 'none';
  };
  r.readAsDataURL(f);
}

/* ---- image preprocessing ---- */
function preprocessImage(dataUrl) {
  return new Promise(resolve => {
    const img = new Image();
    img.onload = () => {
      let scale = 1;
      if (img.width < 1200) scale = Math.min(3, 1200 / img.width);
      const w = Math.round(img.width * scale);
      const h = Math.round(img.height * scale);
      const c = document.createElement('canvas');
      c.width = w; c.height = h;
      const ctx = c.getContext('2d');
      ctx.imageSmoothingEnabled = true;
      ctx.imageSmoothingQuality = 'high';
      ctx.drawImage(img, 0, 0, w, h);
      const id = ctx.getImageData(0, 0, w, h);
      const d  = id.data;
      // grayscale
      for (let i = 0; i < d.length; i += 4) {
        const g = d[i]*0.299 + d[i+1]*0.587 + d[i+2]*0.114;
        d[i] = d[i+1] = d[i+2] = g;
      }
      // Otsu threshold
      const hist = new Array(256).fill(0);
      for (let i = 0; i < d.length; i += 4) hist[d[i]]++;
      const total = w * h;
      let sum = 0; for (let i = 0; i < 256; i++) sum += i * hist[i];
      let sumB = 0, wB = 0, maxVar = 0, threshold = 128;
      for (let t = 0; t < 256; t++) {
        wB += hist[t]; if (!wB) continue;
        const wF = total - wB; if (!wF) break;
        sumB += t * hist[t];
        const mB = sumB / wB, mF = (sum - sumB) / wF;
        const v  = wB * wF * (mB - mF) * (mB - mF);
        if (v > maxVar) { maxVar = v; threshold = t; }
      }
      // binarize
      for (let i = 0; i < d.length; i += 4) {
        const v = d[i] > threshold ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
      }
      ctx.putImageData(id, 0, 0);
      resolve(c.toDataURL('image/png'));
    };
    img.src = dataUrl;
  });
}

/* ---- OCR post-process ---- */
function postProcessOCR(text) {
  let t = text;
  for (let i = 0; i < 10; i++) {
    const prev = t;
    t = t.replace(/([\u0E00-\u0E7F])\s+([\u0E00-\u0E7F])/g, '$1$2');
    if (t === prev) break;
  }
  t = t.replace(/^[.:;|,\s]+/gm, '');
  t = t.replace(/[.|,\s]+$/gm, '');
  t = t.replace(/%/g, 'x');
  t = t.replace(/\bO(\d)/g, '0$1');
  t = t.replace(/(\d)O/g, '$10');
  t = t.replace(/(\d)l(\d)/g, '$11$2');
  t = t.replace(/\bS(\d)/g, '5$1');
  t = t.split('\n').map(l => l.trim()).filter(l => l.length > 0).join('\n');
  return t;
}

$('#ocrBtn').onclick = async () => {
  if (!imgData) { alert('กรุณาเลือกรูปก่อน'); return; }
  const prog = $('#prog');
  prog.style.display = 'block';
  $('#ocrBtn').disabled = true;
  $('#ocrStats').style.display = 'none';
  try {
    $('#ocrBtn').textContent = '⏳ กำลังปรับภาพ...';
    const ocrInput = await preprocessImage(imgData);
    $('#ocrBtn').textContent = '⏳ กำลังอ่านข้อความ...';
    const { data } = await Tesseract.recognize(ocrInput, 'eng+tha', {
      logger: m => { if (m.status === 'recognizing text') prog.firstElementChild.style.width = (m.progress * 100) + '%'; }
    });
    const cleaned = postProcessOCR(data.text.trim());
    $('#ocrText').value = cleaned;
    autoResize($('#ocrText'));
    const lines = (data.lines || []).filter(l => l.text.trim());
    const avgConf = lines.length ? (lines.reduce((s, l) => s + l.confidence, 0) / lines.length) : 0;
    let html = `<div class="os-head">📊 อ่านได้ ${lines.length} บรรทัด · ความมั่นใจเฉลี่ย ${avgConf.toFixed(1)}%</div>`;
    lines.forEach((l, i) => {
      const c = l.confidence;
      const color = c >= 80 ? '#16a34a' : c >= 50 ? '#ca8a04' : '#dc2626';
      const txt   = postProcessOCR(l.text.trim()).substring(0, 60);
      html += `<div class="os-line">
        <span style="flex:0 0 22px;color:#888;">${i+1}</span>
        <div class="os-bar"><div class="os-fill" style="width:${c}%;background:${color}"></div></div>
        <span class="os-num" style="color:${color}">${c.toFixed(0)}%</span>
        <span class="os-txt">${esc2(txt)}</span>
      </div>`;
    });
    $('#ocrStats').innerHTML = `<div class="ocr-stats">${html}</div>`;
    $('#ocrStats').style.display = 'block';
  } catch (err) { alert('OCR ผิดพลาด: ' + err.message); }
  prog.style.display = 'none';
  prog.firstElementChild.style.width = '0';
  $('#ocrBtn').disabled = false;
  $('#ocrBtn').textContent = '🔍 อ่านข้อความจากรูป (OCR)';
};

$('#ocrText').addEventListener('input', function () { autoResize(this); });

/* ================================================================
   PARSE BUTTON — OCR → rows (ยังไม่ดึงราคา)
   ถ้ามี currentCustomerCode → batch match ทันที
   ================================================================ */
$('#parseBtn').onclick = async () => {
  const txt   = $('#ocrText').value;
  const lines = txt.split(/\n/).map(l => l.trim()).filter(Boolean);
  $('#itemRows').innerHTML = '';
  const rows = [];

  lines.forEach(l => {
    if (/[ก-๙A-Za-z]/.test(l) && !/รวม|total|vat|ภาษี|สุทธิ/i.test(l)) {
      const desc = l.trim();
      if (desc) { const tr = itemRow({ desc }); $('#itemRows').appendChild(tr); rows.push(tr); }
    }
  });

  if (!rows.length) { $('#itemRows').appendChild(itemRow()); }

  $$('.tab')[1].click();
  render();

  if (currentCustomerCode && rows.length) {
    await batchMatchItems(rows);
  }
};

/* ================================================================
   ITEM ROW
   ================================================================ */
function itemRow(d = {}) {
  const tr = document.createElement('tr');
  tr.className = 'item';

  // dataset
  if (d.itemNew)       tr.dataset.itemNew       = d.itemNew;
  if (d.productName)   tr.dataset.productName   = d.productName;
  tr.dataset.isNew     = d.isNew ? '1' : '0';

  const qty   = d.qty   ?? 1;
  const price = d.price ?? 0;
  const amt   = qty * price;

  tr.innerHTML = `
    <td class="td-num" title="ดูประวัติการขาย"></td>
    <td class="td-desc">
      <input class="desc" placeholder="ชื่อสินค้า" value="${esc(d.desc || '')}">
    </td>
    <td class="td-qty"><input class="qty" type="number" placeholder="จำนวน" value="${qty}" min="0"></td>
    <td class="td-unit"><input class="unit" placeholder="หน่วย" value="${esc(d.unit || '')}"></td>
    <td class="td-price"><input class="price" type="number" placeholder="ราคา/หน่วย" value="${price}" min="0"></td>
    <td class="td-amt">${fmt(amt)}</td>
    <td class="td-act"><button class="del" title="ลบ">✕</button></td>
  `;

  tr.querySelector('.del').onclick = () => { tr.remove(); render(); };
  tr.querySelectorAll('input').forEach(i => i.oninput = render);

  /* กดเลขลำดับ → popup ประวัติ — ส่ง keyword (ชื่อสินค้าที่พิมพ์) */
tr.querySelector('.td-num').onclick = () => {
    if (!currentCustomerCode) return;
    if (tr.dataset.isNew === '1') return;          // ★ สินค้าใหม่ ไม่เปิด popup
    const keyword = tr.dataset.keyword || tr.querySelector('.desc').value || '';
    if (!keyword) return;
    const pname   = tr.dataset.productName || keyword;
    const itemNew = tr.dataset.itemNew || '';
    showHistPopover(keyword, pname, currentCustomerCode, tr, itemNew);
};

  /* ถ้าส่ง isNew มาพร้อม → ติด tag เลย */
  if (d.isNew) setNewTag(tr, true);

  return tr;
}

$('#addItem').onclick = () => { $('#itemRows').appendChild(itemRow()); render(); };

/* ================================================================
   NEW-TAG helpers
   ================================================================ */
function setNewTag(tr, isNew) {
  const td  = tr.querySelector('.td-desc');
  let   tag = td.querySelector('.new-tag');
  if (isNew) {
    if (!tag) {
      tag = document.createElement('div');
      tag.className   = 'new-tag';
      tag.textContent = '🆕 สินค้าใหม่ — ยังไม่เคยขายลูกค้ารายนี้';
      td.appendChild(tag);
    }
    tr.dataset.isNew = '1';
    tr.querySelector('.td-num').classList.add('no-hist');
  } else {
    if (tag) tag.remove();
    tr.dataset.isNew = '0';
    tr.querySelector('.td-num').classList.remove('no-hist');
  }
}

/* ================================================================
   BATCH MATCH — ส่งทุก item ใน 1 request
   ================================================================ */
const BATCH_URL   = '/SoItem/batch-match';
const HISTORY_URL = '/SoItem/sales-history';   // /{customerCode}/{itemNew}

async function batchMatchItems(rows) {
  if (!currentCustomerCode || !rows.length) return;

  const names = rows.map(tr => (tr.querySelector('.desc')?.value || '').trim());
  if (names.every(n => n.length < 2)) return;

  /* แสดง status bar */
  const bar = $('#matchBar');
  bar.style.display = 'flex';
  $('#matchBarTxt').textContent = `กำลังดึงราคา ${names.length} รายการ จากประวัติลูกค้า...`;

  try {
    const res = await fetch(BATCH_URL, {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ customer_code: currentCustomerCode, items: names }),
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    const results = await res.json();

    let matched = 0;
    results.forEach((r, i) => {
      const tr = rows[i];
      if (!tr) return;

      tr.dataset.itemNew     = r.item_new     || '';
      tr.dataset.productName = r.product_name || '';
      tr.dataset.keyword     = r.match_keyword || names[i] || '';

      if (!r.is_new) {
        /* ใส่ราคา */
        const priceEl = tr.querySelector('.price');
        const unitEl  = tr.querySelector('.unit');
        if (r.unit_price != null && priceEl) priceEl.value = r.unit_price;
        if (r.unit && unitEl && !unitEl.value)  unitEl.value = r.unit;
        matched++;
      }

      setNewTag(tr, !!r.is_new);
    });

    $('#matchBarTxt').textContent = `จับคู่สำเร็จ ${matched} รายการ · สินค้าใหม่ ${names.length - matched} รายการ`;
    render();

  } catch (err) {
    console.warn('batchMatchItems error:', err);
    $('#matchBarTxt').textContent = '❌ ดึงราคาไม่สำเร็จ — ' + err.message;
  }

  /* ซ่อน bar หลัง 4 วิ */
  setTimeout(() => { bar.style.display = 'none'; }, 4000);
}

/* เรียกหลังเลือกบริษัท — match ทุก row ที่มีอยู่แล้ว */
async function onCustomerSelected() {
  const rows = [...$$('#itemRows .item')];
  if (rows.length) await batchMatchItems(rows);
}

/* ================================================================
   SEARCH COMPANY (API)
   ================================================================ */
const API_URL = 'http://server_update:8000/api/getCustAndVendor';
let searchTimer = null;

$('#custCompany').addEventListener('input', function () {
  const v = this.value.trim();
  clearTimeout(searchTimer);
  if (v.length >= 3) searchTimer = setTimeout(() => searchCompany(v), 400);
  else $('#acList').style.display = 'none';
});

$('#custCompany').addEventListener('keydown', function (e) {
  if (e.key === 'Enter') { e.preventDefault(); searchCompany(this.value.trim()); }
});

async function searchCompany(keyword) {
  if (!keyword) return;
  try {
    const res = await fetch(`${API_URL}?keySearch=${encodeURIComponent(keyword)}`);
    if (!res.ok) throw new Error('API error');
    const data     = await res.json();
    const companies = [...(data.Customer || []), ...(data.Supplier || [])];
    if (!companies.length) {
      $('#acList').innerHTML = '<div class="ac-empty">ไม่พบข้อมูลบริษัท</div>';
      $('#acList').style.display = 'block';
    } else {
      showAcResults(companies);
    }
  } catch (err) {
    console.error(err);
    $('#acList').innerHTML = '<div class="ac-empty">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
    $('#acList').style.display = 'block';
  }
}

function showAcResults(companies) {
  const list = $('#acList');
  list.innerHTML = '';
  list.style.display = 'block';

  companies.forEach(c => {
    const code    = (c.CustCode    || c.VendorCode   || '').trim();
    const title   = (c.CustTitle   || c.VendorTitle  || '').trim();
    const rawName = (c.CustName    || c.VendorName   || '').trim();
    const name    = (title && !rawName.startsWith(title)) ? title + ' ' + rawName : rawName;
    const addr    = [c.ContAddr1, c.ContAddr2, c.ContDistrict, c.ContAmphur, c.ContProvince, c.ContPostCode]
                    .filter(p => p && p.trim()).join(' ').trim() || (c.CustAddr1 || '').trim();
    const tel     = (c.ContTel  || c.Telephone || c.Tel   || c.Phone || '').trim();
    const tax     = (c.TaxId    || c.TaxNo     || c.TaxID || c.IDCardNo || '').trim();
    const branch  = (c.BrchID   || c.Branch    || c.BranchName || '').trim();

    const div = document.createElement('div');
    div.className = 'ac-item';
    div.innerHTML = `
      <div class="ac-name">
        ${code ? `<span style="color:var(--blue);margin-right:6px;">[${esc2(code)}]</span>` : ''}
        ${esc2(name)}
      </div>
      <div class="ac-sub">${esc2(addr || '—')}</div>
    `;

    div.onclick = () => {
      $('#custCode').value    = code;
      $('#custCompany').value = name;
      $('#custAddr').value    = addr;
      $('#custTel').value     = tel;
      $('#custTax').value     = tax;
      $('#custBranch').value  = branch;
      list.style.display = 'none';

      /* ★ set state + batch match ทันที */
      currentCustomerCode = code;
      render();
      onCustomerSelected();
    };

    list.appendChild(div);
  });
}

/* ปิด dropdown เมื่อคลิกที่อื่น */
document.addEventListener('click', e => {
  if (!$('.search-wrap')?.contains(e.target)) $('#acList').style.display = 'none';
  const pop = $('#histPopover');
  if (pop.style.display === 'block' && !pop.contains(e.target) && !e.target.closest('.td-num')) {
    pop.style.display = 'none';
  }
});

/* ================================================================
   SALES HISTORY POPOVER
   ================================================================ */
const histCache = {};

async function showHistPopover(keyword, productName, customerCode, rowEl, itemNewCode) {
  const pop  = $('#histPopover');
  const head = $('#hpHead');
  const body = $('#hpBody');
 
  positionPopover(pop, rowEl);
  pop.style.display = 'block';
 
  const cacheKey = `${customerCode}__${itemNewCode || keyword}`;
  if (histCache[cacheKey]) {
    renderHistContent(histCache[cacheKey], keyword, productName, head, body);
    return;
  }
 
  head.innerHTML = `⏳ กำลังโหลด...`;
  body.innerHTML = '<div style="padding:24px;text-align:center;color:var(--muted);font-size:12px;">⏳</div>';
 
  try {
    // ★ ส่ง item_new (exact) + keyword (fallback) ทั้งคู่
    const params = new URLSearchParams();
    if (itemNewCode) params.set('item_new', itemNewCode);
    params.set('keyword', keyword);
 
    const res = await fetch(
      `${HISTORY_URL}/${encodeURIComponent(customerCode)}?${params.toString()}`
    );
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const records = await res.json();
    histCache[cacheKey] = records;
    renderHistContent(records, keyword, productName, head, body);
  } catch (err) {
    body.innerHTML = '<div style="padding:24px;text-align:center;color:#b91c1c;font-size:12px;">❌ โหลดไม่สำเร็จ</div>';
  }
}
 
function positionPopover(pop, rowEl) {
  const rect = rowEl.getBoundingClientRect();
  const popW = Math.min(680, window.innerWidth * 0.9);
  let top    = rect.bottom + window.scrollY + 6;
  let left   = rect.left   + window.scrollX;
  if (left + popW > window.innerWidth) left = Math.max(8, window.innerWidth - popW - 8);
  pop.style.top  = top  + 'px';
  pop.style.left = left + 'px';
}

function renderHistContent(records, itemNew, productName, head, body) {
  const tag = txt => `<span class="mm-tag">${esc2(txt)}</span>`;

  if (!records || !records.length) {
    head.innerHTML = `📊 ${esc2(productName)} ${tag('ไม่พบประวัติ')}`;
    body.innerHTML = '<div style="padding:24px;text-align:center;color:var(--muted);font-size:12px;">ลูกค้ายังไม่เคยสั่งซื้อสินค้านี้</div>';
    return;
  }

  head.innerHTML = `📊 ${esc2(productName)} ${tag(records.length + ' รายการ')}`;

  let html = `
    <table>
      <thead><tr>
        <th>#</th>
        <th>SO No.</th>
        <th>วันที่</th>
        <th>ชื่อสินค้า</th>
        <th style="text-align:right">จำนวน</th>
        <th style="text-align:right">ราคา/หน่วย</th>
      </tr></thead>
      <tbody>
  `;

  records.forEach((r, i) => {
    html += `<tr>
      <td>${i + 1}</td>
      <td style="white-space:nowrap;">${esc2(r.so_no || '-')}</td>
      <td style="white-space:nowrap;font-variant-numeric:tabular-nums;">${esc2(r.doc_date_raw || '-')}</td>
      <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${esc2(r.product_name || '')}">${esc2(r.product_name || '-')}</td>
      <td style="text-align:right;font-variant-numeric:tabular-nums;">${fmt(r.qty || 0)} ${esc2(r.unit || '')}</td>
      <td style="text-align:right;font-variant-numeric:tabular-nums;color:#16a34a;font-weight:700;">${fmt(r.unit_price || 0)}</td>
    </tr>`;
  });

  html += '</tbody></table>';
  body.innerHTML = html;
}

/* ================================================================
   SIGNATURE PAD
   ================================================================ */
const sigCanvas = $('#sigPad');
const sigCtx    = sigCanvas.getContext('2d');
let sigDrawing  = false;

function sigPos(e) {
  const r = sigCanvas.getBoundingClientRect();
  const t = e.touches ? e.touches[0] : e;
  return { x: (t.clientX - r.left) * (sigCanvas.width / r.width), y: (t.clientY - r.top) * (sigCanvas.height / r.height) };
}
function sigStart(e) { e.preventDefault(); sigDrawing = true; const p = sigPos(e); sigCtx.beginPath(); sigCtx.moveTo(p.x, p.y); }
function sigMove(e)  {
  if (!sigDrawing) return; e.preventDefault();
  const p = sigPos(e);
  sigCtx.lineWidth = 2.2; sigCtx.lineCap = 'round'; sigCtx.lineJoin = 'round';
  sigCtx.strokeStyle = '#111'; sigCtx.lineTo(p.x, p.y); sigCtx.stroke();
}
function sigEnd() { sigDrawing = false; sellerSigData = sigCanvas.toDataURL('image/png'); render(); }

sigCanvas.addEventListener('mousedown',  sigStart);
sigCanvas.addEventListener('mousemove',  sigMove);
sigCanvas.addEventListener('mouseup',    sigEnd);
sigCanvas.addEventListener('mouseleave', sigEnd);
sigCanvas.addEventListener('touchstart', sigStart, { passive: false });
sigCanvas.addEventListener('touchmove',  sigMove,  { passive: false });
sigCanvas.addEventListener('touchend',   sigEnd);
$('#sigClear').onclick = () => { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); sellerSigData = null; render(); };

/* ================================================================
   DOCUMENT RENDER
   ================================================================ */
const THAI_MONTHS = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

function val(id)     { return esc2($('#' + id).value || ''); }
function rawVal(id)  { return ($('#' + id).value || '').trim(); }

function fmtThaiDate() {
  const v = rawVal('docDate');
  if (!v) return '—';
  const d = new Date(v);
  return d.getDate() + ' ' + THAI_MONTHS[d.getMonth()] + ' ' + (d.getFullYear() + 543);
}

function sellerSigHtml() {
  return sellerSigData ? `<img class="sig-img" src="${sellerSigData}">` : '';
}

function custInfoHtml() {
  const codeHtml = rawVal('custCode')
    ? `<span style="color:var(--navy);font-size:12px;">[${val('custCode')}]</span>`
    : '';
  return `
    <div class="cust-head">ข้อมูลลูกค้า ${codeHtml}</div>
    <table class="cinfo-table">
      <tr>
        <td class="ck">ชื่อลูกค้า</td>
        <td class="cv" colspan="2">${val('custCompany')}</td>
        <td class="ck">เลขประจำตัวผู้เสียภาษี</td>
        <td class="cv">${val('custTax')}</td>
        <td class="ck" style="width:1%">สาขา</td>
        <td class="cv" style="width:1%;white-space:nowrap">${val('custBranch')}</td>
      </tr>
      <tr>
        <td class="ck">ผู้ติดต่อ</td>
        <td class="cv" colspan="2">${val('contactName')}</td>
        <td class="ck">เบอร์โทรศัพท์</td>
        <td class="cv" colspan="3">${val('custTel')}</td>
      </tr>
      <tr>
        <td class="ck" rowspan="3" style="vertical-align:top">ที่อยู่</td>
        <td class="cv" colspan="2" rowspan="3" style="vertical-align:top">${val('custAddr')}</td>
        <td class="ck">ยืนราคาภายใน</td>
        <td class="cv" colspan="3">${val('validDays')}${rawVal('validDays') ? ' วัน' : ''}</td>
      </tr>
      <tr>
        <td class="ck">Expire Date</td>
        <td class="cv" colspan="3">${val('expireDate')}</td>
      </tr>
      <tr>
        <td class="ck">จำนวนวันเครดิต</td>
        <td class="cv" colspan="3">${val('creditDays')}${rawVal('creditDays') ? ' วัน' : ''}</td>
      </tr>
    </table>
  `;
}

function pageHtml(rows, pg, pageCount, isLast, isFirst, T) {
  const foot = isLast ? `
    <div class="page-footer-keep">
      <div class="ctot">
        <div class="tr"><span class="lbl">รวมเป็นเงิน</span><span>${fmt(T.gross)}</span></div>
        <div class="tr"><span class="lbl">ภาษีมูลค่าเพิ่ม (7%)</span><span>${fmt(T.vat)}</span></div>
        <div class="tr grand"><span>รวมเป็นเงินทั้งสิ้น</span><span>${fmt(T.grand)}</span></div>
        <div class="baht">(${bahtText(T.grand)})</div>
      </div>
      <div class="cfoot" style="justify-content:center">
        <div class="ssign">
          <div class="sg"><div class="sgline">${val('custCompany') || '&nbsp;'}<br>ผู้ซื้อ</div></div>
          <div class="sg">${sellerSigHtml()}<div class="sgline">${esc2(SELLER.name)}<br>ผู้เสนอราคา</div></div>
        </div>
      </div>
    </div>` : '';

  return `<div class="doc page">
    <div class="qhead">
      <div class="seller-top">
        <div class="co">${esc2(SELLER.name)}</div>
        <div class="sln">โทร. ${esc2(SELLER.tel)}</div>
        <div class="sln">${esc2(SELLER.addrShort)}</div>
        <div class="sln">เลขประจำตัวผู้เสียภาษี ${esc2(SELLER.tax)}</div>
      </div>
      <div style="text-align:right">
        <div class="qbig">Quotation</div>
        <div class="qmeta">วันที่ ${fmtThaiDate()}<br>หน้า ${pg} / ${pageCount}</div>
      </div>
    </div>
    <div class="bluebar">ใบเสนอราคา</div>
    ${isFirst ? custInfoHtml() : ''}
    <table class="itbl">
      <thead><tr>
        <th class="c" style="width:60px">ลำดับ</th>
        <th class="l">รายการสินค้า</th>
        <th class="c" style="width:80px">จำนวน</th>
        <th class="r" style="width:110px">ราคา/หน่วย</th>
        <th class="r" style="width:120px">ราคารวม</th>
      </tr></thead>
      <tbody>${rows}</tbody>
    </table>
    <div class="flexspace"></div>
    ${foot}
  </div>`;
}

function render() {
  const items = [];
  $$('#itemRows .item').forEach((tr, idx) => {
    const desc  = tr.querySelector('.desc').value;
    const qty   = parseFloat(tr.querySelector('.qty').value)   || 0;
    const unit  = tr.querySelector('.unit').value;
    const price = parseFloat(tr.querySelector('.price').value) || 0;

    /* อัปเดตเลขลำดับ */
    const numCell = tr.querySelector('.td-num');
    if (numCell) {
      numCell.textContent = idx + 1;
      /* ถ้าไม่มี itemNew → ใส่ class no-hist เพื่อบอกว่ากดไม่ได้ */
      const hasHist = tr.dataset.keyword || tr.dataset.itemNew;
      if (!hasHist) numCell.classList.add('no-hist');
      else numCell.classList.remove('no-hist');
    }

    /* อัปเดต amt cell */
    const amtCell = tr.querySelector('.td-amt');
    if (amtCell) amtCell.textContent = fmt(qty * price);

    if (!desc && !price) return;
    items.push({ desc, qty, unit, price });
  });

  const gross = items.reduce((s, it) => s + it.qty * it.price, 0);
  const vat   = gross * 0.07;
  const grand = gross + vat;
  const T     = { gross, vat, grand };

  /* footer ตาราง input */
  const foot = $('#itemFoot');
  if (items.length) { foot.style.display = ''; $('#footGross').textContent = fmt(gross); }
  else foot.style.display = 'none';

  /* สร้างหน้าเอกสาร */
  const sizes     = paginate(items.length);
  const pageCount = sizes.length;
  let out = '', offset = 0;

  for (let pg = 0; pg < pageCount; pg++) {
    const count  = sizes[pg];
    const slice  = items.slice(offset, offset + count);
    const isLast = pg === pageCount - 1;
    let rows = '';

    slice.forEach((it, idx) => {
      rows += `<tr>
        <td class="c">${offset + idx + 1}</td>
        <td class="l">${esc2(it.desc || '-')}</td>
        <td class="c">${it.qty || ''}${it.unit ? ' ' + esc2(it.unit) : ''}</td>
        <td class="r">${fmt(it.price || 0)}</td>
        <td class="r">${fmt(it.qty * it.price)}</td>
      </tr>`;
    });

    /* padding rows */
    const padTarget = pg === 0 ? FIRST_PAGE_MAX : OTHER_PAGE_MAX;
    if (isLast && count < padTarget) {
      for (let e = 0; e < padTarget - count; e++) {
        rows += `<tr class="empty-row"><td class="c"></td><td class="l"></td><td class="c"></td><td class="r"></td><td class="r"></td></tr>`;
      }
    }

    /* ไม่มี item เลย */
    if (!items.length) {
      rows = '';
      for (let e = 0; e < FIRST_PAGE_MAX; e++) {
        rows += `<tr class="empty-row">
          <td class="c">${e === 0 ? '1' : ''}</td>
          <td class="l">${e === 0 ? '—' : ''}</td>
          <td class="c"></td><td class="r"></td><td class="r"></td>
        </tr>`;
      }
    }

    out += pageHtml(rows, pg + 1, pageCount, isLast, pg === 0, T);
    offset += count;
  }

  $('#pages').innerHTML = out;
}

/* ================================================================
   THAI BAHT TEXT
   ================================================================ */
function bahtText(n) {
  n = Math.round(n * 100) / 100;
  const baht = Math.floor(n), satang = Math.round((n - baht) * 100);
  const txt  = ['','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
  function conv(num) {
    if (num === 0) return '';
    if (num > 999999) return conv(Math.floor(num / 1000000)) + 'ล้าน' + conv(num % 1000000);
    const str = num.toString(), len = str.length; let s = '';
    for (let i = 0; i < len; i++) {
      const d = +str[i], p = len - 1 - i; if (!d) continue;
      if (p === 0) s += (d === 1 && len > 1) ? 'เอ็ด' : txt[d];
      else if (p === 1) s += d === 1 ? 'สิบ' : d === 2 ? 'ยี่สิบ' : txt[d] + 'สิบ';
      else s += txt[d] + ['','สิบ','ร้อย','พัน','หมื่น','แสน'][p];
    }
    return s;
  }
  if (baht === 0 && satang === 0) return 'ศูนย์บาทถ้วน';
  let r = ''; if (baht > 0) r += conv(baht) + 'บาท';
  r += satang > 0 ? conv(satang) + 'สตางค์' : 'ถ้วน';
  return r;
}

/* ================================================================
   INIT
   ================================================================ */
['docDate','contactName','custCode','custCompany','custAddr','custTel',
 'custTax','custBranch','validDays','expireDate','creditDays','note']
  .forEach(id => { $('#' + id).oninput = $('#' + id).onchange = render; });

$('#refreshBtn').onclick = render;
$('#printBtn').onclick   = () => window.print();

$('#docDate').value = new Date().toISOString().slice(0, 10);
$('#itemRows').appendChild(itemRow({ desc: 'สินค้า/บริการ ตัวอย่าง', qty: 1, unit: 'ชิ้น', price: 1000 }));
render();
</script>

</body>
</html>
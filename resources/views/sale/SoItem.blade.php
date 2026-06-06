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
  .search-wrap .btn-search{flex:none;padding:9px 14px;background:var(--blue);color:#fff;border:none;border-radius:8px;font-family:inherit;font-weight:600;font-size:13px;cursor:pointer;}
  .search-wrap .btn-search:hover{background:var(--navy2);}
  .search-wrap .btn-search:disabled{opacity:.6;cursor:wait;}
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
  .items-table tbody td{padding:4px 4px;border-bottom:1px solid #f0f2f5;vertical-align:middle;}
  .items-table tbody tr:last-child td{border-bottom:none;}
  .items-table tbody tr:hover td{background:#f8faff;}
  .items-table .td-num{width:38px;text-align:center;color:var(--blue);font-weight:700;font-size:12px;cursor:pointer;user-select:none;transition:.15s;}
  .items-table .td-num:hover{background:#eef2ff;border-radius:4px;}
  .items-table .td-desc{position:relative;min-width:180px;}

  /* floating sales history popover */
  .hist-popover{
    position:fixed;z-index:500;background:#fff;border:1px solid var(--line);border-radius:12px;
    box-shadow:0 12px 40px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04);
    width:min(680px,90vw);max-height:380px;overflow:hidden;display:none;
    animation:popIn .15s ease-out;
  }
  @keyframes popIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
  .hist-popover .hp-head{font-weight:700;font-size:12px;color:var(--navy);}
  .hist-popover .hp-head .mm-tag{background:#dbeafe;color:var(--navy);padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;}
  .hist-popover .hp-close{border:none;background:#f3f4f6;border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:13px;color:#6b7280;flex-shrink:0;}
  .hist-popover .hp-close:hover{background:#e5e7eb;color:#111;}
  .hist-popover .hp-body{overflow:auto;max-height:310px;-webkit-overflow-scrolling:touch;}
  .hist-popover table{width:100%;border-collapse:collapse;font-size:11.5px;min-width:550px;}
  .hist-popover th{padding:7px 6px;font-weight:700;color:#374151;border-bottom:2px solid var(--line);text-align:left;white-space:nowrap;background:#f8fafc;position:sticky;top:0;z-index:1;}
  .hist-popover td{padding:6px 6px;border-bottom:1px solid #f0f2f5;}
  .hist-popover tr:hover td{background:#f8faff;}
  .items-table .td-qty{width:76px;}
  .items-table .td-unit{width:76px;}
  .items-table .td-price{width:108px;}
  .items-table .td-amt{width:108px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:var(--navy);white-space:nowrap;padding-right:10px;font-size:13px;}
  .items-table .td-act{width:36px;text-align:center;white-space:nowrap;}

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

  /* === fuzzy dropdown per item === */
  .fuzzy-wrap{position:relative;}
  .fuzzy-list{position:absolute;top:100%;left:0;right:0;z-index:60;background:#fff;border:1px solid var(--line);border-radius:8px;box-shadow:0 6px 20px #0002;max-height:200px;overflow:auto;margin-top:2px;display:none;}
  .fuzzy-list .fz-item{padding:8px 10px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:12px;line-height:1.35;}
  .fuzzy-list .fz-item:last-child{border-bottom:none;}
  .fuzzy-list .fz-item:hover{background:#f0f5ff;}
  .fuzzy-list .fz-item .fz-name{font-weight:700;color:#111;}
  .fuzzy-list .fz-item .fz-sub{color:var(--muted);font-size:11px;}
  .fuzzy-list .fz-empty{padding:10px;text-align:center;color:var(--muted);font-size:12px;}
  .fuzzy-list .fz-loading{padding:10px;text-align:center;color:var(--blue);font-size:12px;}

  /* === sales history modal === */
  .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:none;align-items:center;justify-content:center;padding:20px;}
  .modal-overlay.show{display:flex;}
  .modal{background:#fff;border-radius:14px;box-shadow:0 20px 60px #0004;width:100%;max-width:900px;max-height:85vh;display:flex;flex-direction:column;overflow:hidden;animation:modalIn .2s ease-out;}
  @keyframes modalIn{from{opacity:0;transform:translateY(16px) scale(.97)} to{opacity:1;transform:none}}
  .modal-head{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line);background:linear-gradient(135deg,#eef2ff,#f8faff);}
  .modal-head h3{margin:0;font-size:16px;color:var(--navy);}
  .modal-close{width:34px;height:34px;border:none;background:#f3f4f6;border-radius:8px;font-size:18px;cursor:pointer;color:#6b7280;display:flex;align-items:center;justify-content:center;}
  .modal-close:hover{background:#e5e7eb;color:#111;}
  .modal-body{overflow:auto;padding:0;flex:1;-webkit-overflow-scrolling:touch;}
  .modal-table-wrap{overflow-x:auto;min-width:0;}
  .modal-body table{width:100%;border-collapse:collapse;font-size:13px;min-width:700px;}
  .modal-body thead{position:sticky;top:0;background:#f8fafc;z-index:2;}
  .modal-body th{padding:10px 10px;font-weight:700;color:#374151;border-bottom:2px solid var(--line);text-align:left;white-space:nowrap;}
  .modal-body td{padding:9px 10px;border-bottom:1px solid #f0f2f5;color:#1f2937;}
  .modal-body tr:hover td{background:#f8faff;}
  .modal-body .empty-msg{padding:40px;text-align:center;color:var(--muted);font-size:14px;}
  .modal-meta{padding:12px 20px;border-bottom:1px solid var(--line);background:#fafbfc;display:flex;gap:10px;flex-wrap:wrap;font-size:13px;}
  .modal-meta .mm-tag{background:#eef2ff;color:var(--navy);padding:4px 10px;border-radius:6px;font-weight:600;font-size:12px;}
  .modal-body td.date-col{white-space:nowrap;font-variant-numeric:tabular-nums;}
  .modal-body td.num-col{text-align:right;font-variant-numeric:tabular-nums;}

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
    .topbar,.doc-tools,.modal-overlay{display:none!important;}
    .wrap{display:block!important;padding:0;margin:0;max-width:none;gap:0;}
    .wrap>.card:first-child{display:none!important;}
    .card{border:none!important;box-shadow:none!important;border-radius:0;}
    .doc-scroll{padding:0!important;background:#fff!important;max-height:none!important;overflow:visible!important;}
    /* @page margin:0 → ไม่มี browser header/footer → ได้เต็ม A4 = 297mm */
    .doc.page{
      box-shadow:none!important;
      max-width:none!important;
      width:100%!important;
      margin:0!important;
      padding:10mm 12mm!important;
      display:flex!important;
      flex-direction:column!important;
      min-height:0!important;
      height:287mm!important; /* A4 297mm − 10mm เผื่อขอบบน/ล่าง */
      page-break-after:always;
    }
    .doc.page:last-child{page-break-after:auto;}
    .flexspace{flex:1 1 auto!important;min-height:0!important;display:block!important;}
    .page-footer-keep{page-break-inside:avoid!important;break-inside:avoid!important;}
    /* บังคับ layout แนวนอน — ป้องกัน mobile CSS ซ้อนแนวตั้ง */
    .ssign{flex-direction:row!important;gap:120px!important;}
    .cfoot{flex-direction:row!important;}
  }

  @media(min-width:768px){
    .modal{max-width:900px;}
  }

  @media(max-width:767px){
    .wrap{padding:10px;gap:12px;}
    .pane{padding:14px;}
    .doc-scroll{padding:10px;max-height:none;}
    .doc{padding:16px 14px;}
    .row{grid-template-columns:1fr;}
    .ssign{flex-direction:column;gap:30px;}
    .modal-overlay{align-items:flex-end;padding:0;}
    .modal{max-width:100%;max-height:92vh;border-radius:14px 14px 0 0;animation:modalSlideUp .25s ease-out;}
    @keyframes modalSlideUp{from{transform:translateY(100%)} to{transform:none}}
    .modal-head{padding:12px 16px;}
    .modal-head h3{font-size:14px;}
    .modal-meta{gap:6px;padding:10px 14px;}
    .modal-meta .mm-tag{font-size:10px;padding:3px 7px;}
    .modal-body table{font-size:11.5px;min-width:580px;}
    .modal-body th{padding:8px 6px;font-size:11px;}
    .modal-body td{padding:7px 6px;font-size:11px;}
    .items-table{font-size:12px;}
    .items-table input{font-size:12px;padding:6px 6px;}
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
      <div class="hint"><b>เคล็ดลับ:</b> ระบบจะปรับภาพ + อ่าน OCR + รวมตัวอักษรไทย + ลบขยะ ให้อัตโนมัติทั้งหมด · กดปุ่มเดียวแล้วไปตรวจแก้ที่แท็บ "พิมพ์ข้อมูล"</div>
    </div>

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

{{-- ====== Sales History Modal ====== --}}

<script>
const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

const SELLER={
  name:'บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด',
  tel:'02-4727341-48',
  addrShort:'เลขที่ 39/7 ถนนวุฒากาส แขวงตลาดพลู เขตธนบุรี กรุงเทพฯ 10600',
  tax:'0105547065721'
};

// ⚙️ จำนวนรายการสูงสุดต่อหน้า — ปรับตัวเลขได้ตามต้องการ
// หน้าแรกมีข้อมูลลูกค้า จึงใส่ได้น้อยกว่าหน้าถัดไป
const FIRST_PAGE_MAX = 14;
const OTHER_PAGE_MAX = 24;

function paginate(total) {
  if (total <= FIRST_PAGE_MAX) return [total];
  const sizes = [FIRST_PAGE_MAX];
  let remaining = total - FIRST_PAGE_MAX;
  while (remaining > OTHER_PAGE_MAX) {
    sizes.push(OTHER_PAGE_MAX);
    remaining -= OTHER_PAGE_MAX;
  }
  if (remaining > 0) sizes.push(remaining);
  return sizes;
}

/* ---------- tabs ---------- */
$$('.tab').forEach(t=>t.onclick=()=>{
  $$('.tab').forEach(x=>x.classList.remove('active'));
  $$('.pane').forEach(x=>x.classList.remove('active'));
  t.classList.add('active');
  $('#pane-'+t.dataset.tab).classList.add('active');
});

/* ---------- image upload + OCR ---------- */
let imgData=null;
$('#drop').onclick=()=>$('#file').click();
$('#drop').ondragover=e=>e.preventDefault();
$('#drop').ondrop=e=>{e.preventDefault();handleFile(e.dataTransfer.files[0]);};
$('#file').onchange=e=>handleFile(e.target.files[0]);
function handleFile(f){
  if(!f) return;
  const r=new FileReader();
  r.onload=()=>{imgData=r.result;$('#preview').src=imgData;$('#preview').style.display='block';$('#dropEmpty').style.display='none';};
  r.readAsDataURL(f);
}
/* ---------- image preprocessing for better OCR ---------- */
function preprocessImage(dataUrl){
  return new Promise(resolve=>{
    const img = new Image();
    img.onload = ()=>{
      // ขยายรูปเล็กให้ใหญ่ขึ้น (ช่วย OCR อ่านง่ายขึ้น)
      let scale = 1;
      if(img.width < 1200) scale = Math.min(3, 1200 / img.width);
      const w = Math.round(img.width * scale);
      const h = Math.round(img.height * scale);

      const c = document.createElement('canvas');
      c.width = w; c.height = h;
      const ctx = c.getContext('2d');

      // วาดรูปขยาย
      ctx.imageSmoothingEnabled = true;
      ctx.imageSmoothingQuality = 'high';
      ctx.drawImage(img, 0, 0, w, h);

      // แปลง grayscale + contrast + binarize
      const id = ctx.getImageData(0, 0, w, h);
      const d = id.data;

      // 1) Grayscale
      for(let i=0; i<d.length; i+=4){
        const gray = d[i]*0.299 + d[i+1]*0.587 + d[i+2]*0.114;
        d[i] = d[i+1] = d[i+2] = gray;
      }

      // 2) คำนวณ threshold แบบ Otsu
      const hist = new Array(256).fill(0);
      for(let i=0; i<d.length; i+=4) hist[d[i]]++;
      const total = w * h;
      let sum = 0; for(let i=0;i<256;i++) sum += i*hist[i];
      let sumB=0, wB=0, wF=0, maxVar=0, threshold=128;
      for(let t=0;t<256;t++){
        wB += hist[t]; if(wB===0) continue;
        wF = total - wB; if(wF===0) break;
        sumB += t * hist[t];
        const mB = sumB/wB, mF = (sum-sumB)/wF;
        const v = wB * wF * (mB-mF) * (mB-mF);
        if(v > maxVar){ maxVar = v; threshold = t; }
      }

      // 3) Binarize ด้วย threshold + เพิ่มความคม
      for(let i=0; i<d.length; i+=4){
        const v = d[i] > threshold ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
      }

      ctx.putImageData(id, 0, 0);
      resolve(c.toDataURL('image/png'));
    };
    img.src = dataUrl;
  });
}

/* ---------- OCR post-processing ---------- */
function postProcessOCR(text){
  let t = text;

  // === 1) ลบช่องว่างระหว่างตัวอักษรไทย (ปัญหาหลักของ Tesseract) ===
  // "ท ่ อ ท อ ง แด ง" → "ท่อทองแดง"
  // ต้องวนหลายรอบเพราะ regex จับทีละคู่
  for(let i=0; i<10; i++){
    const prev = t;
    t = t.replace(/([\u0E00-\u0E7F])\s+([\u0E00-\u0E7F])/g, '$1$2');
    if(t === prev) break; // ไม่มีอะไรเปลี่ยนแล้ว
  }

  // === 2) ลบขยะต้นบรรทัด/ท้ายบรรทัด ===
  t = t.replace(/^[.:;|,\s]+/gm, '');   // ลบ . : ; | , ต้นบรรทัด
  t = t.replace(/[.|,\s]+$/gm, '');     // ลบ . | , ท้ายบรรทัด

  // === 3) แก้ตัวอักษรที่ OCR มักอ่านผิด ===
  t = t.replace(/%/g, 'x');             // % → x (เช่น 1" x 1/4)
  t = t.replace(/\bO(\d)/g, '0$1');     // O ตามด้วยตัวเลข → 0
  t = t.replace(/(\d)O/g, '$10');       // ตัวเลขตามด้วย O → 0
  t = t.replace(/(\d)l(\d)/g, '$11$2'); // l ระหว่างตัวเลข → 1
  t = t.replace(/\bS(\d)/g, '5$1');     // S นำหน้าตัวเลข → 5

  // === 4) ลบบรรทัดว่าง + trim ===
  t = t.split('\n').map(l => l.trim()).filter(l => l.length > 0).join('\n');

  return t;
}

$('#ocrBtn').onclick=async()=>{
  if(!imgData){alert('กรุณาเลือกรูปก่อน');return;}
  const prog=$('#prog'); prog.style.display='block';
  $('#ocrBtn').disabled=true;
  $('#ocrStats').style.display='none';

  try{
    // ปรับภาพอัตโนมัติ (ขยาย + ขาวดำ + Otsu threshold)
    $('#ocrBtn').textContent='⏳ กำลังปรับภาพ...';
    const ocrInput = await preprocessImage(imgData);

    // อ่าน OCR ด้วย eng+tha (เหมาะกับรหัสสินค้า/Part No. + ข้อความไทย)
    $('#ocrBtn').textContent='⏳ กำลังอ่านข้อความ...';
    const {data}=await Tesseract.recognize(ocrInput, 'eng+tha', {
      logger:m=>{ if(m.status==='recognizing text') prog.firstElementChild.style.width=(m.progress*100)+'%'; }
    });

    // Post-process: รวมตัวอักษรไทย + ลบขยะ + แก้ตัวสับสน
    const cleaned = postProcessOCR(data.text.trim());
    $('#ocrText').value = cleaned;
    autoResize($('#ocrText'));

    const lines=(data.lines||[]).filter(l=>l.text.trim());
    const avgConf=lines.length ? (lines.reduce((s,l)=>s+l.confidence,0)/lines.length) : 0;
    let html=`<div class="os-head">📊 อ่านได้ ${lines.length} บรรทัด · ความมั่นใจเฉลี่ย ${avgConf.toFixed(1)}%</div>`;
    lines.forEach((l,i)=>{
      const c=l.confidence;
      const color=c>=80?'#16a34a':c>=50?'#ca8a04':'#dc2626';
      const txt=postProcessOCR(l.text.trim()).substring(0,60);
      html+=`<div class="os-line">
        <span style="flex:0 0 22px;color:#888;">${i+1}</span>
        <div class="os-bar"><div class="os-fill" style="width:${c}%;background:${color}"></div></div>
        <span class="os-num" style="color:${color}">${c.toFixed(0)}%</span>
        <span class="os-txt">${esc2(txt)}</span>
      </div>`;
    });
    $('#ocrStats').innerHTML=`<div class="ocr-stats">${html}</div>`;
    $('#ocrStats').style.display='block';
  }catch(err){ alert('OCR ผิดพลาด: '+err.message); }
  prog.style.display='none'; prog.firstElementChild.style.width='0';
  $('#ocrBtn').disabled=false; $('#ocrBtn').textContent='🔍 อ่านข้อความจากรูป (OCR)';
};

/* ---------- helpers ---------- */
function esc(s){return (s+'').replace(/"/g,'&quot;');}
function esc2(s){return (s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function fmt(n){return (Math.round(n*100)/100).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}

/* ---------- auto-resize textarea ---------- */
function autoResize(el){
  el.style.height = 'auto';
  el.style.height = el.scrollHeight + 'px';
}
$('#ocrText').addEventListener('input', function(){ autoResize(this); });
/* ================================================================
   FUZZY SEARCH สินค้า — โหลดข้อมูลทั้งหมดครั้งเดียว แล้วค้นหาในเครื่อง
   ================================================================ */
const FUZZY_URL = '/SoItem/fuzzy-search';
const HISTORY_URL = '/SoItem/sales-history';

let allProducts = null;
let productsLoading = false;

async function preloadProducts(){
  productsLoading = true;
  try {
    const res = await fetch(`${FUZZY_URL}?q=`);
    if(res.ok){
      const data = await res.json();
      if(Array.isArray(data) && data.length){
        allProducts = data;
        console.log(`✅ โหลดสินค้าสำเร็จ ${data.length} รายการ`);
      }
    }
  } catch(e){ console.warn('โหลดสินค้าไม่สำเร็จ จะค้นหาจาก server แทน:', e); }
  productsLoading = false;
}
preloadProducts();

let fuzzyTimers = new WeakMap();

function attachFuzzy(descInput, itemEl) {
  const wrap = document.createElement('div');
  wrap.className = 'fuzzy-wrap';
  descInput.parentNode.insertBefore(wrap, descInput);
  wrap.appendChild(descInput);

  const list = document.createElement('div');
  list.className = 'fuzzy-list';
  wrap.appendChild(list);

  descInput.addEventListener('input', function(){
    const v = this.value.trim();
    clearTimeout(fuzzyTimers.get(descInput));
    if(v.length < 2){ list.style.display='none'; return; }
    fuzzyTimers.set(descInput, setTimeout(()=>{
      if(allProducts){
        doFuzzyLocal(v, list, descInput, itemEl);
      } else {
        doFuzzyAPI(v, list, descInput, itemEl);
      }
    }, 100)); // เร็วขึ้นเพราะค้นในเครื่อง
  });

  descInput.addEventListener('keydown', function(e){
    if(e.key === 'Escape') list.style.display='none';
  });

  document.addEventListener('click', function(e){
    if(!wrap.contains(e.target)) list.style.display='none';
  });
}

/* ค้นหาจากข้อมูลที่โหลดไว้แล้ว (ทันที ไม่ต้องรอ API) */
function doFuzzyLocal(keyword, listEl, descInput, itemEl){
  const kw = keyword.toLowerCase();
  const matches = allProducts.filter(item => {
    const name = (item.product_name || item.item_name || '').toLowerCase();
    const kwd = (item.keyword || '').toLowerCase();
    const sku = String(item.group_id || '').toLowerCase();
    return name.includes(kw) || kwd.includes(kw) || sku.includes(kw);
  }).slice(0, 15);

  if(!matches.length){
    listEl.innerHTML = '<div class="fz-empty">ไม่พบสินค้าที่ตรงกัน</div>';
    listEl.style.display = 'block';
    return;
  }

  listEl.innerHTML = '';
  listEl.style.display = 'block';
  matches.forEach(item => {
    const div = document.createElement('div');
    div.className = 'fz-item';
    div.innerHTML = `
      <div class="fz-name">${esc2(item.product_name||item.item_name||'')}</div>
      <div class="fz-sub">SKU: SKU-${item.group_id} · keyword: ${esc2(item.keyword||'-')}</div>
    `;
    div.onclick = () => {
      descInput.value = item.product_name || item.item_name || '';
      itemEl.dataset.groupId = item.group_id;
      itemEl.dataset.sku = 'SKU-' + item.group_id;
      itemEl.dataset.productName = item.product_name || item.item_name || '';
      listEl.style.display = 'none';
      fillLatestPrice(item.group_id, itemEl);
      render();
    };
    listEl.appendChild(div);
  });
}

/* fallback: ค้นหาจาก API (ถ้าโหลดข้อมูลไม่สำเร็จ) */
async function doFuzzyAPI(keyword, listEl, descInput, itemEl){
  listEl.innerHTML = '<div class="fz-loading">🔍 กำลังค้นหา...</div>';
  listEl.style.display = 'block';
  try {
    const res = await fetch(`${FUZZY_URL}?q=${encodeURIComponent(keyword)}`);
    if(!res.ok){
      let errMsg = `HTTP ${res.status}`;
      try { const j = await res.json(); errMsg = j.message || j.error || errMsg; } catch(_){}
      listEl.innerHTML = `<div class="fz-empty">❌ ${esc2(errMsg)}</div>`;
      return;
    }
    const data = await res.json();
    if(data.error){
      listEl.innerHTML = `<div class="fz-empty">❌ ${esc2(data.message||data.error)}</div>`;
      return;
    }
    if(!Array.isArray(data) || !data.length){
      listEl.innerHTML = '<div class="fz-empty">ไม่พบสินค้าที่ตรงกัน</div>';
      return;
    }
    listEl.innerHTML = '';
    data.forEach(item => {
      const div = document.createElement('div');
      div.className = 'fz-item';
      div.innerHTML = `
        <div class="fz-name">${esc2(item.product_name||item.item_name||'')}</div>
        <div class="fz-sub">SKU: SKU-${item.group_id} · keyword: ${esc2(item.keyword||'-')}</div>
      `;
      div.onclick = () => {
        descInput.value = item.product_name || item.item_name || '';
        itemEl.dataset.groupId = item.group_id;
        itemEl.dataset.sku = 'SKU-' + item.group_id;
        itemEl.dataset.productName = item.product_name || item.item_name || '';
        listEl.style.display = 'none';
        fillLatestPrice(item.group_id, itemEl);
        render();
      };
      listEl.appendChild(div);
    });
  } catch(err) {
    listEl.innerHTML = '<div class="fz-empty">❌ เชื่อมต่อ server ไม่ได้</div>';
  }
}

async function fillLatestPrice(groupId, itemEl){
  try {
    const res = await fetch(`${HISTORY_URL}/${encodeURIComponent(groupId)}`);
    if(!res.ok) return;
    const records = await res.json();
    if(!records || !records.length) return;

    function pd(s){
      if(!s) return 0; s=s.trim();
      if(/^\d{4}[-\/]\d{2}[-\/]\d{2}/.test(s)) return new Date(s.replace(/\//g,'-')).getTime()||0;
      const m=s.match(/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/);
      if(m){ let y=+m[3]; if(y>2400)y-=543; return new Date(`${y}-${m[2].padStart(2,'0')}-${m[1].padStart(2,'0')}`).getTime()||0; }
      return new Date(s).getTime()||0;
    }
    records.sort((a,b) => pd(b.doc_date_raw) - pd(a.doc_date_raw));

    const latest = records[0];
    const priceInput = itemEl.querySelector('.price');
    const unitInput  = itemEl.querySelector('.unit');

    if(latest.unit_price && priceInput){
      priceInput.value = parseFloat(latest.unit_price)||0;
    }
    if(latest.unit && unitInput && !unitInput.value){
      unitInput.value = latest.unit;
    }
    render();
  } catch(e){
    console.warn('fillLatestPrice error:', e);
  }
}

/* ================================================================
   SALES HISTORY — หน้าต่างลอยเมื่อเมาส์ชี้รายการสินค้า
   ================================================================ */
const histCache = {};

// กดที่อื่น → ปิด popover
document.addEventListener('click', function(e){
  const pop = $('#histPopover');
  if(pop && pop.style.display==='block' && !pop.contains(e.target) && !e.target.closest('.td-num')){
    pop.style.display='none';
  }
});

async function showHistPopover(groupId, productName, rowEl){
  const pop = $('#histPopover');
  const head = $('#hpHead');
  const body = $('#hpBody');

  // จัดตำแหน่ง popover ใต้บรรทัดที่กด
  const rect = rowEl.getBoundingClientRect();
  const popW = Math.min(680, window.innerWidth * 0.9);
  let top = rect.bottom + 4;
  let left = rect.left;
  // ถ้าล้นขวา → ขยับซ้าย
  if(left + popW > window.innerWidth) left = Math.max(8, window.innerWidth - popW - 8);
  // ถ้าล้นล่าง → แสดงด้านบนแทน
  if(top + 380 > window.innerHeight) top = Math.max(8, rect.top - 384);
  pop.style.top = top + 'px';
  pop.style.left = left + 'px';
  pop.style.display = 'block';

  // ใช้ cache ถ้ามี
  if(histCache[groupId]){
    renderHistContent(histCache[groupId], groupId, productName, head, body);
    return;
  }

  head.innerHTML = `📊 ${esc2(productName)} <span class="mm-tag">SKU-${groupId}</span>`;
  body.innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted);font-size:12px;">⏳ กำลังโหลด...</div>';

  try {
    const res = await fetch(`${HISTORY_URL}/${encodeURIComponent(groupId)}`);
    if(!res.ok) throw new Error('API error');
    const records = await res.json();
    histCache[groupId] = records;
    renderHistContent(records, groupId, productName, head, body);
  } catch(err) {
    body.innerHTML = '<div style="padding:20px;text-align:center;color:#b91c1c;font-size:12px;">❌ โหลดไม่สำเร็จ</div>';
  }
}

function renderHistContent(records, groupId, productName, head, body){
  if(!records.length){
    head.innerHTML = `📊 ${esc2(productName)} <span class="mm-tag">ไม่พบประวัติ</span>`;
    body.innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted);font-size:12px;">ไม่พบประวัติการขาย</div>';
    return;
  }

  function parseDate(s){
    if(!s) return 0; s=s.trim();
    if(/^\d{4}[-\/]\d{2}[-\/]\d{2}/.test(s)) return new Date(s.replace(/\//g,'-')).getTime()||0;
    const m=s.match(/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/);
    if(m){let y=+m[3];if(y>2400)y-=543;return new Date(`${y}-${m[2].padStart(2,'0')}-${m[1].padStart(2,'0')}`).getTime()||0;}
    return new Date(s).getTime()||0;
  }
  records.sort((a,b) => parseDate(b.doc_date_raw) - parseDate(a.doc_date_raw));

  const twelveMonthsAgo = Date.now() - (365*24*60*60*1000);
  const filtered = records.filter(r => parseDate(r.doc_date_raw) >= twelveMonthsAgo);
  const shown = filtered.length > 0 ? filtered : records.slice(0, 20);
  const customers = [...new Set(shown.map(r=>r.customer_name).filter(Boolean))];

  head.innerHTML = `📊 ${esc2(productName)}
    <span class="mm-tag">SKU-${groupId}</span>
    <span class="mm-tag">${shown.length} รายการ</span>
    <span class="mm-tag">${customers.length} ลูกค้า</span>`;

  let html = `<div style="overflow-x:auto;"><table>
    <thead><tr>
      <th>#</th><th>วันที่</th><th>ลูกค้า</th>
      <th style="text-align:right">จำนวน</th>
      <th style="text-align:right">ราคา/หน่วย</th>
      <th style="text-align:right">ยอดรวม</th>
    </tr></thead><tbody>`;

  shown.forEach((r, i) => {
    html += `<tr>
      <td>${i+1}</td>
      <td style="white-space:nowrap;font-variant-numeric:tabular-nums;">${esc2(r.doc_date_raw||'-')}</td>
      <td>${esc2(r.customer_name||r.customer_code||'-')}</td>
      <td style="text-align:right;font-variant-numeric:tabular-nums;">${fmt(r.qty||0)} ${esc2(r.unit||'')}</td>
      <td style="text-align:right;font-variant-numeric:tabular-nums;color:#16a34a;font-weight:600;">${fmt(r.unit_price||0)}</td>
      <td style="text-align:right;font-weight:600;font-variant-numeric:tabular-nums;">${fmt(r.line_amount||0)}</td>
    </tr>`;
  });
  html += '</tbody></table></div>';
  body.innerHTML = html;
}

/* ================================================================
   ITEM ROW — ตาราง (tr)
   ================================================================ */
function itemRow(d={}){
  const tr = document.createElement('tr');
  tr.className = 'item';
  if(d.groupId) tr.dataset.groupId = d.groupId;
  if(d.sku) tr.dataset.sku = d.sku;
  if(d.productName) tr.dataset.productName = d.productName;

  const qty = d.qty ?? 1;
  const price = d.price ?? 0;
  const amt = qty * price;

  tr.innerHTML = `
    <td class="td-num" title="กดเพื่อดูประวัติการขาย"></td>
    <td class="td-desc">
      <input class="desc" placeholder="พิมพ์ชื่อสินค้า → ค้นหาอัตโนมัติ" value="${esc(d.desc||'')}">
    </td>
    <td class="td-qty"><input class="qty" type="number" placeholder="จำนวน" value="${qty}"></td>
    <td class="td-unit"><input class="unit" placeholder="หน่วย" value="${esc(d.unit||'')}"></td>
    <td class="td-price"><input class="price" type="number" placeholder="ราคา/หน่วย" value="${price}"></td>
    <td class="td-amt">${fmt(amt)}</td>
    <td class="td-act">
      <button class="del" title="ลบ">✕</button>
    </td>
  `;

  tr.querySelector('.del').onclick = () => { tr.remove(); render(); };
  tr.querySelectorAll('input').forEach(i => i.oninput = render);

  // Attach fuzzy search to desc input
  const descInput = tr.querySelector('.desc');
  setTimeout(() => attachFuzzy(descInput, tr), 0);

  // กดเลขลำดับ → แสดงประวัติการขายลอย
  tr.querySelector('.td-num').onclick = () => {
    const gid = tr.dataset.groupId;
    if(!gid) return;
    const pname = tr.dataset.productName || descInput.value || '-';
    showHistPopover(gid, pname, tr);
  };

  return tr;
}
$('#addItem').onclick=()=>{$('#itemRows').appendChild(itemRow());render();};

/* ---------- auto-parse OCR ---------- */
async function autoMatchItem(itemEl){
  const descInput = itemEl.querySelector('.desc');
  const keyword = (descInput.value||'').trim();
  if(keyword.length < 2) return;

  let best = null;

  // ใช้ข้อมูลที่โหลดไว้แล้ว (เร็วกว่า)
  if(allProducts){
    const kw = keyword.toLowerCase();
    const match = allProducts.find(item => {
      const name = (item.product_name || item.item_name || '').toLowerCase();
      const kwd = (item.keyword || '').toLowerCase();
      return name.includes(kw) || kwd.includes(kw);
    });
    if(match) best = match;
  }

  // fallback: ค้นจาก API
  if(!best){
    try {
      const res = await fetch(`${FUZZY_URL}?q=${encodeURIComponent(keyword)}`);
      if(!res.ok) return;
      const data = await res.json();
      if(Array.isArray(data) && data.length) best = data[0];
    } catch(e){ return; }
  }

  if(!best) return;
  descInput.value = best.product_name || best.item_name || descInput.value;
  itemEl.dataset.groupId = best.group_id;
  itemEl.dataset.sku = 'SKU-' + best.group_id;
  itemEl.dataset.productName = best.product_name || best.item_name || '';
  await fillLatestPrice(best.group_id, itemEl);
}

$('#parseBtn').onclick=async()=>{
  const txt=$('#ocrText').value;
  const lines=txt.split(/\n/).map(l=>l.trim()).filter(Boolean);
  $('#itemRows').innerHTML=''; let added=0;
  const rows=[];
  lines.forEach(l=>{
    if(/[ก-๙A-Za-z]/.test(l) && !/รวม|total|vat|ภาษี|สุทธิ/i.test(l)){
      const desc=l.trim();
      if(desc){
        const row=itemRow({desc});
        $('#itemRows').appendChild(row);
        rows.push(row);
        added++;
      }
    }
  });
  if(!added){
    $('#itemRows').appendChild(itemRow());
    $$('.tab')[1].click(); render();
    return;
  }
  $$('.tab')[1].click(); render();

  const parseBtn=$('#parseBtn');
  parseBtn.disabled=true; parseBtn.textContent='⏳ กำลังจับคู่สินค้า...';
  await Promise.all(rows.map(r => autoMatchItem(r)));
  render();
  parseBtn.disabled=false; parseBtn.textContent='✨ แยกรายการอัตโนมัติ → กรอกฟอร์ม';
};

/* ---------- Thai baht text ---------- */
function bahtText(n){
  n=Math.round(n*100)/100;
  const baht=Math.floor(n), satang=Math.round((n-baht)*100);
  const txt=['','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
  const unit=['','สิบ','ร้อย','พัน','หมื่น','แสน'];
  function conv(num){
    if(num===0) return '';
    if(num>999999) return conv(Math.floor(num/1000000))+'ล้าน'+conv(num%1000000);
    const str=num.toString(), len=str.length; let s='';
    for(let i=0;i<len;i++){
      const d=+str[i], p=len-1-i; if(d===0)continue;
      if(p===0){ s+=(d===1&&len>1)?'เอ็ด':txt[d]; }
      else if(p===1){ s+=(d===1?'สิบ':d===2?'ยี่สิบ':txt[d]+'สิบ'); }
      else s+=txt[d]+unit[p];
    }
    return s;
  }
  if(baht===0&&satang===0) return 'ศูนย์บาทถ้วน';
  let r=''; if(baht>0)r+=conv(baht)+'บาท';
  r += satang>0 ? conv(satang)+'สตางค์' : 'ถ้วน';
  return r;
}

/* ---------- build document ---------- */
function val(id){return esc2($('#'+id).value||'');}

let sellerSigData = null;
function sellerSigHtml(){
  if(!sellerSigData) return '';
  return `<img class="sig-img" src="${sellerSigData}">`;
}

const THAI_MONTHS=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
function fmtThaiDate(){
  const v=$('#docDate').value;
  if(!v) return '—';
  const d=new Date(v);
  return d.getDate()+' '+THAI_MONTHS[d.getMonth()]+' '+(d.getFullYear()+543);
}

function custInfoHtml(){
  const codeHtml = val('custCode') ? `<span style="color:var(--navy);font-size:12px;">[${val('custCode')}]</span>` : '';
  return `<div class="cust-head">ข้อมูลลูกค้า ${codeHtml}</div>
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
        <td class="cv" colspan="3">${val('validDays')}${val('validDays')?' วัน':''}</td>
      </tr>
      <tr>
        <td class="ck">Expire Date</td>
        <td class="cv" colspan="3">${val('expireDate')}</td>
      </tr>
      <tr>
        <td class="ck">จำนวนวันเครดิต</td>
        <td class="cv" colspan="3">${val('creditDays')}${val('creditDays')?' วัน':''}</td>
      </tr>
    </table>`;
}

function pageHtml(rows, pg, pageCount, isLast, isFirst, T){
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
        <div class="sg"><div class="sgline">${val('custCompany')||'&nbsp;'}<br>ผู้ซื้อ</div></div>
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

function render(){
  const items=[];
  $$('#itemRows .item').forEach((tr, idx)=>{
    const desc=tr.querySelector('.desc').value;
    const qty=parseFloat(tr.querySelector('.qty').value)||0;
    const unit=tr.querySelector('.unit').value;
    const price=parseFloat(tr.querySelector('.price').value)||0;

    // อัปเดตเลขลำดับ
    const numCell = tr.querySelector('.td-num');
    if(numCell) numCell.textContent = idx + 1;

    // อัปเดตจำนวนเงินในตาราง
    const amt = qty * price;
    const amtCell = tr.querySelector('.td-amt');
    if(amtCell) amtCell.textContent = fmt(amt);

    if(!desc&&!price)return;
    items.push({desc,qty,unit,price});
  });

  const gross=items.reduce((s,it)=>s+it.qty*it.price,0);
  const vat=gross*0.07;
  const grand=gross+vat;
  const T={gross,vat,grand};

  // อัปเดต footer ตาราง
  const foot = $('#itemFoot');
  if(items.length > 0){
    foot.style.display = '';
    $('#footGross').textContent = fmt(gross);
  } else {
    foot.style.display = 'none';
  }

  const sizes = paginate(items.length);
  const pageCount = sizes.length;

  let out='';
  let offset = 0;

  for(let pg=0; pg<pageCount; pg++){
    const count = sizes[pg];
    const slice = items.slice(offset, offset + count);
    const isLast = (pg === pageCount - 1);
    let rows='';

    slice.forEach((it,idx)=>{
      const amt=it.qty*it.price;
      const name=esc2(it.desc||'-');
      rows+=`<tr>
        <td class="c">${offset+idx+1}</td>
        <td class="l">${name}</td>
        <td class="c">${it.qty||''}${it.unit?' '+esc2(it.unit):''}</td>
        <td class="r">${fmt(it.price||0)}</td>
        <td class="r">${fmt(amt)}</td>
      </tr>`;
    });

    const padTarget = (pg === 0) ? FIRST_PAGE_MAX : OTHER_PAGE_MAX;
    if(isLast && count < padTarget){
      const pad = padTarget - count;
      for(let e=0; e<pad; e++){
        rows+=`<tr class="empty-row">
          <td class="c"></td><td class="l"></td><td class="c"></td><td class="r"></td><td class="r"></td>
        </tr>`;
      }
    }

    if(!items.length){
      rows='';
      for(let e=0; e<FIRST_PAGE_MAX; e++){
        rows+=`<tr class="empty-row">
          <td class="c">${e===0?'1':''}</td><td class="l">${e===0?'—':''}</td><td class="c"></td><td class="r"></td><td class="r"></td>
        </tr>`;
      }
    }

    out+=pageHtml(rows, pg+1, pageCount, isLast, pg===0, T);
    offset += count;
  }
  $('#pages').innerHTML=out;
}

['docDate','contactName','custCode','custCompany','custAddr','custTel','custTax','custBranch',
 'validDays','expireDate','creditDays','note']
  .forEach(id=>$('#'+id).oninput=$('#'+id).onchange=render);
$('#refreshBtn').onclick=render;
$('#printBtn').onclick=()=>window.print();

$('#docDate').value=new Date().toISOString().slice(0,10);
$('#itemRows').appendChild(itemRow({desc:'สินค้า/บริการ ตัวอย่าง',qty:1,unit:'ชิ้น',price:1000}));
render();

/* ========== ค้นหาบริษัทจาก API (พิมพ์รหัสหรือชื่อก็ค้นได้) ========== */
const API_URL = 'http://server_update:8000/api/getCustAndVendor';
let allCompanies = [];
let searchTimer = null;

// ค้นหาจากช่องชื่อบริษัท
$('#custCompany').addEventListener('input', function(){
  const v = this.value.trim();
  clearTimeout(searchTimer);
  if(v.length >= 3){
    searchTimer = setTimeout(()=> searchCompany(v), 400);
  } else {
    $('#acList').style.display='none';
  }
});

$('#custCompany').addEventListener('keydown', function(e){
  if(e.key==='Enter'){ e.preventDefault(); searchCompany(this.value.trim()); }
});

async function searchCompany(keyword){
  if(!keyword) return;
  try{
    const res = await fetch(`${API_URL}?keySearch=${encodeURIComponent(keyword)}`);
    if(!res.ok) throw new Error('API error');
    const data = await res.json();
    allCompanies = [...(data.Customer||[]), ...(data.Supplier||[])];
    if(allCompanies.length===0){
      $('#acList').innerHTML='<div class="ac-empty">ไม่พบข้อมูลบริษัท</div>';
      $('#acList').style.display='block';
    } else {
      showAcResults(allCompanies);
    }
  } catch(err){
    console.error(err);
    $('#acList').innerHTML='<div class="ac-empty">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
    $('#acList').style.display='block';
  }
}

function showAcResults(companies){
  const list=$('#acList');
  list.innerHTML='';
  list.style.display='block';
  companies.forEach(c=>{
    const code  = (c.CustCode || c.VendorCode || '').trim();
    const title = (c.CustTitle || c.VendorTitle || '').trim();
    const rawName = (c.CustName || c.VendorName || '').trim();
    const name  = (title && !rawName.startsWith(title)) ? title+' '+rawName : rawName;
    const addr  = [c.ContAddr1, c.ContAddr2, c.ContDistrict, c.ContAmphur, c.ContProvince, c.ContPostCode]
                  .filter(p=> p && p.trim()).join(' ').trim() || (c.CustAddr1||'').trim();
    const tel   = (c.ContTel || c.Telephone || c.Tel || c.Phone || '').trim();
    const tax   = (c.TaxId || c.TaxNo || c.TaxID || c.IDCardNo || '').trim();
    const branch= (c.BrchID || c.Branch || c.BranchName || '').trim();

    const div = document.createElement('div');
    div.className='ac-item';
    div.innerHTML=`<div class="ac-name">${code ? '<span style="color:var(--blue);margin-right:6px;">['+esc2(code)+']</span>' : ''}${esc2(name)}</div><div class="ac-sub">${esc2(addr||'—')}</div>`;
    div.onclick=()=>{
      $('#custCode').value    = code;
      $('#custCompany').value = name;
      $('#custAddr').value    = addr;
      $('#custTel').value     = tel;
      $('#custTax').value     = tax;
      $('#custBranch').value  = branch;
      list.style.display='none';
      render();
    };
    list.appendChild(div);
  });
}

document.addEventListener('click', function(e){
  const wrap=$('.search-wrap');
  if(wrap && !wrap.contains(e.target)) $('#acList').style.display='none';
});

/* ========== ลายเซ็นด้วยเมาส์/นิ้ว ========== */
const sigCanvas=$('#sigPad'), sigCtx=sigCanvas.getContext('2d');
let sigDrawing=false;

function sigPos(e){
  const r=sigCanvas.getBoundingClientRect();
  const t=e.touches?e.touches[0]:e;
  return { x:(t.clientX-r.left)*(sigCanvas.width/r.width), y:(t.clientY-r.top)*(sigCanvas.height/r.height) };
}
function sigStart(e){ e.preventDefault(); sigDrawing=true; const p=sigPos(e); sigCtx.beginPath(); sigCtx.moveTo(p.x,p.y); }
function sigMove(e){ if(!sigDrawing)return; e.preventDefault(); const p=sigPos(e); sigCtx.lineWidth=2.2; sigCtx.lineCap='round'; sigCtx.lineJoin='round'; sigCtx.strokeStyle='#111'; sigCtx.lineTo(p.x,p.y); sigCtx.stroke(); }
function sigEnd(){ sigDrawing=false; sellerSigData=sigCanvas.toDataURL('image/png'); render(); }

sigCanvas.addEventListener('mousedown',sigStart);
sigCanvas.addEventListener('mousemove',sigMove);
sigCanvas.addEventListener('mouseup',sigEnd);
sigCanvas.addEventListener('mouseleave',sigEnd);
sigCanvas.addEventListener('touchstart',sigStart,{passive:false});
sigCanvas.addEventListener('touchmove',sigMove,{passive:false});
sigCanvas.addEventListener('touchend',sigEnd);

$('#sigClear').onclick=()=>{ sigCtx.clearRect(0,0,sigCanvas.width,sigCanvas.height); sellerSigData=null; render(); };
</script>

<div class="hist-popover" id="histPopover">
  <div style="display:flex;align-items:center;padding:10px 14px;background:linear-gradient(135deg,#eef2ff,#f8faff);border-bottom:1px solid var(--line);">
    <div class="hp-head" id="hpHead" style="flex:1;display:flex;gap:6px;flex-wrap:wrap;align-items:center;"></div>
    <button class="hp-close" onclick="$('#histPopover').style.display='none'">✕</button>
  </div>
  <div class="hp-body" id="hpBody"></div>
</div>

</body>
</html>
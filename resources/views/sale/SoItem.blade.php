<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบสร้างใบเสนอราคา (Quotation Generator)</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<style>
.desc-icon:disabled{opacity:.4;cursor:not-allowed;pointer-events:none;}
:root{--navy:#1f3a93;--navy2:#16306f;--blue:#1e50c8;--bg:#eef1f6;--line:#dfe4ec;--green:#16a34a;--muted:#6b7280;--soft:#f7f9fc;}
*{box-sizing:border-box;}
body{margin:0;font-family:'Sarabun',sans-serif;background:var(--bg);color:#1f2937;min-width:0;}
.topbar{background:linear-gradient(90deg,var(--navy2),var(--blue));color:#fff;padding:14px 22px;font-weight:600;font-size:18px;display:flex;align-items:center;gap:10px;}
.topbar .dot{width:26px;height:26px;border-radius:6px;background:#fff3;display:flex;align-items:center;justify-content:center;}
.wrap{display:grid;grid-template-columns:1fr;gap:18px;padding:18px;margin:0 auto;}
@media(min-width:1920px){.wrap{grid-template-columns:minmax(600px,850px) 1fr;max-width:1800px;align-items:start;}.wrap>.card-preview{position:sticky;top:18px;max-height:calc(100vh - 36px);overflow-y:auto;scrollbar-width:thin;}.wrap>.card-preview::-webkit-scrollbar{width:6px;}.wrap>.card-preview::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px;}}
@media(min-width:2400px){.wrap{grid-template-columns:800px 1fr;}}
@media(max-width:1919px){.wrap{max-width:900px;}}
.wrap.ocr-mode{max-width:1000px;margin:0 auto;justify-content:center;}
.wrap.ocr-mode>.card:first-child{width:100%;}
@media(min-width:1920px){.wrap.ocr-mode{grid-template-columns:1fr;max-width:1000px;}}
.card{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:visible;box-shadow:0 1px 3px #0000000a;}
.tabs{display:flex;border-bottom:1px solid var(--line);}
.tab{flex:1;padding:14px;text-align:center;cursor:pointer;font-weight:600;color:var(--muted);border-bottom:3px solid transparent;font-size:14px;}
.tab.active{color:var(--blue);border-bottom-color:var(--blue);}
.pane{padding:18px;display:none;}
.pane.active{display:block;}
.muted{color:var(--muted);font-size:13px;}
.sec-title{font-weight:700;color:var(--navy);margin:18px 0 6px;border-bottom:1px solid var(--line);padding-bottom:4px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;}
.search-wrap{position:relative;display:flex;gap:6px;}
.search-wrap input{flex:1;}
.ac-list{position:absolute;top:100%;left:0;right:0;z-index:50;background:#fff;border:1px solid var(--line);border-radius:8px;box-shadow:0 6px 20px #0002;margin-top:4px;display:none;max-height:300px;overflow-y:auto;}
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
@media(max-width:500px){.row{grid-template-columns:1fr;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:none;border-radius:8px;padding:11px 16px;font-family:inherit;font-weight:600;font-size:14px;cursor:pointer;}
.btn-primary{background:var(--blue);color:#fff;width:100%;}
.btn-primary:hover:not(:disabled){background:var(--navy2);}
.btn-primary:disabled{opacity:.6;cursor:not-allowed;}
.btn-green{background:var(--green);color:#fff;}
.btn-green:disabled{background:#9ca3af;cursor:not-allowed;opacity:.7;}
.btn-ghost{background:#fff;border:1px solid var(--line);color:#374151;}
.btn-ghost:hover:not(:disabled){background:#f3f4f6;}
.btn-ghost:disabled{opacity:.5;cursor:not-allowed;}
.btn-row{display:flex;gap:10px;margin-top:14px;}
.progress{height:8px;background:#e5e9f0;border-radius:6px;overflow:hidden;margin-top:10px;display:none;}
.progress span{display:block;height:100%;width:0;background:var(--blue);transition:.2s;}
.hint{background:#fff8e6;border:1px solid #f3e2b0;border-radius:10px;padding:12px 14px;font-size:13px;color:#7a5d00;margin-top:14px;}
.items-table-wrap{margin:8px 0;border:1px solid var(--line);border-radius:10px;background:#fff;overflow-x:auto;}
.items-table{width:100%;border-collapse:collapse;font-size:13px;min-width:580px;}
.items-table thead th{background:linear-gradient(180deg,#f8fafc,#f1f4f9);padding:10px 8px;font-weight:700;font-size:12px;color:var(--navy);text-align:left;border-bottom:2px solid var(--line);white-space:nowrap;text-transform:uppercase;letter-spacing:.03em;}
.items-table thead th.c{text-align:center;}
.items-table thead th.r{text-align:right;}
.items-table tbody td{padding:4px 4px;border-bottom:1px solid #f0f2f5;vertical-align:top;}
.items-table tbody tr:last-child td{border-bottom:none;}
.items-table tbody tr:hover td{background:#f8faff;}
.items-table .td-num{width:38px;text-align:center;color:var(--blue);font-weight:700;font-size:12px;vertical-align:middle;}
.items-table .td-desc{min-width:360px;}
.desc-icons{display:flex;gap:3px;margin-top:3px;}
.desc-icon{border:none;background:none;border-radius:5px;padding:2px 7px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px;color:#888;transition:.15s;line-height:1.4;font-family:inherit;white-space:nowrap;}
.desc-icon:hover{background:#f0f5ff;color:var(--blue);}
.desc-icon.ic-price{color:#0e7490;}
.desc-icon.ic-price:hover{background:#ecfeff;color:#0c5f73;}
.desc-icon.ic-ai{color:#7c3aed;}
.desc-icon.ic-ai:hover{background:#f5f3ff;color:#6d28d9;}
.desc-icon.ic-doc{color:#b45309;}
.desc-icon.ic-doc:hover{background:#fffbeb;color:#92400e;}
.desc-icon .badge{background:#0e7490;color:#fff;border-radius:8px;padding:0 5px;font-size:9px;font-weight:700;min-width:14px;text-align:center;line-height:1.5;}
.price-pop{position:fixed;z-index:600;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,.18);width:min(780px,94vw);display:none;font-size:12px;font-family:inherit;animation:popIn .15s ease-out;}
@keyframes popIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.price-pop .pp-head{display:flex;align-items:center;padding:10px 14px;background:linear-gradient(135deg,#ecfeff,#f0fdfe);border-bottom:1px solid var(--line);border-radius:11px 11px 0 0;}
.price-pop .pp-title{flex:1;font-weight:700;font-size:12px;color:#0e7490;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.price-pop .pp-close{border:none;background:#eee;border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:13px;color:#888;flex-shrink:0;}
.price-pop .pp-close:hover{background:#ddd;color:#333;}
.price-pop .pp-tokens{padding:5px 14px;background:#f0fdfe;border-bottom:1px solid var(--line);font-size:11px;color:#0e7490;line-height:1.8;}
.price-pop .pp-body{max-height:360px;overflow-y:auto;}
.price-pop .pp-body::-webkit-scrollbar{width:5px;}
.price-pop .pp-body::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px;}
.price-pop table{width:100%;border-collapse:collapse;}
.price-pop th{padding:6px 10px;font-weight:700;color:#374151;border-bottom:1.5px solid var(--line);background:#f8fafc;text-align:left;white-space:nowrap;position:sticky;top:0;z-index:1;font-size:11px;}
.price-pop td{padding:6px 10px;border-bottom:1px solid #f0f2f5;font-size:12px;}
.price-pop tr:hover td{background:#f0fdfe;}
.price-pop .pp-sim{font-weight:700;white-space:nowrap;}
.price-pop .pp-use{font-size:11px;color:#fff;background:#0e7490;border:none;border-radius:5px;padding:3px 10px;cursor:pointer;white-space:nowrap;font-weight:600;}
.price-pop .pp-use:hover{background:#0c5f73;}
.price-pop .pp-empty{padding:30px;text-align:center;color:var(--muted);}
.price-pop tr.pp-active td{background:#ecfeff !important;}
.price-pop tr.pp-active{border-left:3px solid #0e7490;}
.price-src{font-size:10px;color:#0e7490;margin-top:1px;line-height:1.3;}
.doc-pop{position:fixed;z-index:600;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,.18);width:min(720px,94vw);display:none;font-size:12px;font-family:inherit;animation:popIn .15s ease-out;}
.doc-pop .dp-head{display:flex;align-items:center;padding:10px 14px;background:linear-gradient(135deg,#fffbeb,#fefce8);border-bottom:1px solid var(--line);border-radius:11px 11px 0 0;}
.doc-pop .dp-title{flex:1;font-weight:700;font-size:12px;color:#92400e;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.doc-pop .dp-close{border:none;background:#eee;border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:13px;color:#888;flex-shrink:0;}
.doc-pop .dp-close:hover{background:#ddd;color:#333;}
.doc-pop .dp-body{max-height:360px;overflow-y:auto;}
.doc-pop .dp-body::-webkit-scrollbar{width:5px;}
.doc-pop .dp-body::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px;}
.doc-pop table{width:100%;border-collapse:collapse;}
.doc-pop th{padding:6px 10px;font-weight:700;color:#374151;border-bottom:1.5px solid var(--line);background:#fefce8;text-align:left;white-space:nowrap;position:sticky;top:0;z-index:1;font-size:11px;}
.doc-pop td{padding:6px 10px;border-bottom:1px solid #f0f2f5;font-size:12px;}
.doc-pop tr:hover td{background:#fffbeb;}
.doc-pop .dp-empty{padding:30px;text-align:center;color:var(--muted);}
.doc-pop .dp-src{font-size:9px;border-radius:4px;padding:1px 6px;font-weight:600;}
.doc-pop .dp-src.s-hist{background:#dbeafe;color:#1e40af;}
.doc-pop .dp-src.s-qt{background:#fef3c7;color:#92400e;}
.match-bar{display:none;align-items:center;gap:8px;padding:8px 12px;background:#ecfeff;border:1px solid #a5f3fc;border-radius:8px;margin-bottom:8px;font-size:12px;color:#0e7490;}
.match-bar .spin{animation:ai-spin .8s linear infinite;display:inline-block;}
.items-table .td-qty{width:76px;}
.items-table .td-unit{width:76px;}
.items-table .td-price{width:108px;}
.items-table .td-amt{width:108px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:var(--navy);white-space:nowrap;padding-right:10px;font-size:13px;vertical-align:middle;}
.items-table .td-act{width:80px;text-align:center;vertical-align:middle;white-space:nowrap;}
.items-table .td-act .act-wrap{display:inline-flex;gap:2px;align-items:center;}
.items-table input{width:100%;padding:7px 8px;border:1px solid transparent;border-radius:6px;font-size:13px;font-family:inherit;background:transparent;transition:border-color .15s,background .15s;}
.items-table input:hover{border-color:var(--line);background:#fff;}
.items-table input:focus{border-color:var(--blue);background:#fff;outline:none;box-shadow:0 0 0 2px rgba(30,80,200,.1);}
.items-table input[type=text]{text-align:right;font-variant-numeric:tabular-nums;}
.items-table input::placeholder{color:#bbb;font-weight:400;}
.items-table .del{width:26px;height:26px;background:none;border:1px solid transparent;border-radius:6px;cursor:pointer;color:#ccc;font-size:13px;transition:.15s;display:inline-flex;align-items:center;justify-content:center;}
.items-table .del:hover{background:#fef2f2;border-color:#fecaca;color:#b91c1c;}
.items-table tfoot td{padding:10px 8px;font-weight:700;font-size:13px;border-top:2px solid var(--line);background:#f8fafc;}
.cart-btn{width:26px;height:26px;background:none;border:1px solid transparent;border-radius:6px;cursor:pointer;color:#aaa;font-size:14px;transition:.15s;display:inline-flex;align-items:center;justify-content:center;}
.cart-btn:hover{background:#eff6ff;border-color:#bfdbfe;color:var(--blue);}
.doc-tools{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--line);}
.doc-scroll{padding:18px;background:#f1f4f9;}
.doc{background:#fff;width:100%;max-width:760px;margin:0 auto;padding:22px 30px;box-shadow:0 2px 14px #0003;font-size:13px;color:#1a1a1a;}
.doc.page{display:flex;flex-direction:column;min-height:1080px;}
.doc.page+.doc.page{margin-top:22px;}
.flexspace{flex:1 1 auto;min-height:8px;}
.qhead{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;}
.qhead .qbig{font-size:38px;font-weight:800;color:var(--navy);letter-spacing:-1px;line-height:1;text-align:right;}
.qmeta{font-size:12px;color:#333;margin-top:4px;line-height:1.3;}
.seller-top{line-height:1.3;}
.seller-top .co{font-size:18px;font-weight:800;color:#111;}
.seller-top .sln{font-size:11.5px;color:#333;}
.bluebar{background:none;color:#1f3a93;font-weight:800;padding:3px 0;margin:4px 0 8px;font-size:26px;letter-spacing:1px;border-bottom:2px solid #1f3a93;text-align:center;}
.cust-head{font-weight:700;font-size:14px;margin-bottom:3px;display:flex;align-items:center;gap:8px;}
.cinfo-table{width:100%;border-collapse:collapse;font-size:12px;margin-bottom:6px;}
.cinfo-table td{padding:1.5px 4px;vertical-align:top;}
.cinfo-table .ck{color:#444;font-weight:500;white-space:nowrap;width:1%;padding-right:4px;}
.cinfo-table .cv{font-weight:600;color:#111;}
table.itbl{width:100%;border-collapse:collapse;font-size:13px;}
.itbl th{border-bottom:2px solid #333;padding:8px 5px;font-weight:700;color:#222;}
.itbl td{padding:8px 5px;}
.itbl th.l,.itbl td.l{text-align:left;word-break:break-word;white-space:normal;}
.itbl th.c,.itbl td.c{text-align:center;}
.itbl th.r,.itbl td.r{text-align:right;}
.itbl tr.empty-row td{height:auto;padding:8px 5px;}
.itbl tr.sub-row td{font-size:11px;color:#6b7280;padding:4px 5px 4px 20px;background:#f9fafb;}
.ctot{margin-left:auto;width:300px;font-size:13px;}
.ctot .tr{display:flex;justify-content:space-between;padding:2px 0;}
.ctot .tr .lbl{color:#333;}
.ctot .grand{border-top:2px solid var(--navy);margin-top:3px;padding-top:6px;font-size:17px;font-weight:800;color:var(--navy);}
.ctot .baht{text-align:right;font-size:12px;color:#444;margin-top:1px;}
.cfoot{display:flex;justify-content:space-between;gap:30px;align-items:flex-end;margin-top:14px;}
.ssign{display:flex;gap:20px;justify-content:space-between;width:100%;}
.sg{flex:1;text-align:center;font-size:12px;position:relative;white-space:nowrap;min-height:85px;}
.sg .sgline{border-top:1px solid #555;margin:65px 0 3px;padding-top:6px;}
.sg .sig-img{position:absolute;left:50%;bottom:28px;transform:translateX(-50%);max-height:55px;max-width:80%;height:auto;pointer-events:none;}
.page-footer-keep{page-break-inside:avoid;break-inside:avoid;margin-top:auto;}
@media(max-width:700px){.doc{padding:14px 16px;}.qhead .qbig{font-size:24px;}.seller-top .co{font-size:15px;}.bluebar{font-size:18px;}.cinfo-table{font-size:11px;}.ssign{gap:40px;}.sg{width:auto;}.ctot{width:100%;}.tab{padding:10px 8px;font-size:13px;}}
.preview-header{display:none;padding:14px 18px;font-weight:700;font-size:14px;color:var(--navy);border-bottom:1px solid var(--line);background:linear-gradient(180deg,#f8fafc,#fff);gap:8px;align-items:center;}
@media(min-width:1920px){.preview-header{display:flex;}}
.page-number{text-align:right;font-size:11px;color:#888;padding-top:4px;margin-top:2px;}
@media print{@page{size:A4;margin:0;}.topbar,.doc-tools,.preview-header{display:none!important;}.wrap{display:block!important;padding:0;margin:0;max-width:none;gap:0;}.wrap>.card:first-child{display:none!important;}.card{border:none!important;box-shadow:none!important;border-radius:0;}.doc-scroll{padding:0!important;background:#fff!important;}.doc.page{box-shadow:none!important;max-width:none!important;width:100%!important;margin:0!important;padding:6mm 10mm!important;display:flex!important;flex-direction:column!important;min-height:0!important;height:287mm!important;page-break-after:always;}.doc.page:last-child{page-break-after:auto;}.flexspace{flex:1 1 auto!important;min-height:0!important;display:block!important;}.page-footer-keep{page-break-inside:avoid!important;break-inside:avoid!important;}.ssign{flex-direction:row!important;gap:20px!important;justify-content:space-between!important;width:100%!important;}.cfoot{flex-direction:row!important;}}
.ai-drop{border:2px dashed #c7d0df;border-radius:12px;padding:28px 20px;text-align:center;background:var(--soft);cursor:pointer;transition:.15s;position:relative;min-height:110px;}
.ai-drop:hover,.ai-drop.drag-over{border-color:var(--blue);background:#f0f5ff;}
.ai-chip{display:inline-flex;align-items:center;gap:5px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:16px;padding:3px 10px;font-size:12px;color:var(--blue);font-weight:600;}
.ai-chip-del{background:none;border:none;color:#93c5fd;cursor:pointer;font-size:13px;line-height:1;padding:0;}
.ai-chip-del:hover{color:var(--blue);}
.ai-status{display:none;padding:10px 14px;border-radius:8px;font-size:13px;margin-top:10px;}
.ai-status.info{background:#eff6ff;border:1px solid #bfdbfe;color:var(--blue);}
.ai-status.success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;}
.ai-status.error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;}
.ai-result-table td{padding:7px 10px;border-bottom:1px solid #f0f2f5;}
.ai-result-table tr:last-child td{border-bottom:none;}
.ai-result-table tr:hover td{background:#f8faff;}
@keyframes ai-spin{to{transform:rotate(360deg)}}
.ai-spin{display:inline-block;animation:ai-spin .8s linear infinite;}
.sig-mode-wrap label{border:1.5px solid #1f3a93;background:#fff;border-radius:6px;padding:5px 12px;font-size:12px;font-weight:600;color:#1f3a93;cursor:pointer;transition:.15s;white-space:nowrap;}
</style>
</head>
<body>
<div class="topbar"><span class="dot">📄</span> ระบบสร้างใบเสนอราคา · Quotation Generator</div>

<div class="wrap ocr-mode">
  <div class="card">
    <div class="tabs">
      <div class="tab active" data-tab="ai-ocr">AI สกัดรายการ</div>
      <div class="tab" data-tab="manual">⌨️ พิมพ์ข้อมูล</div>
    </div>

  {{-- ========== TAB: AI OCR ========== --}}
  <div class="pane active" id="pane-ai-ocr">
    <div class="ai-drop" id="aiDrop">
      <div id="aiDropEmpty">
        <div style="font-weight:600;margin-top:6px;">คลิก ลากไฟล์มาวาง หรือ Ctrl+V วางรูป</div>
        <div class="muted">รองรับ JPG PNG PDF Excel CSV· เลือกหลายไฟล์พร้อมกันได้</div>
      </div>
      <input type="file" id="aiFileInput" multiple
        accept="image/*,.pdf,.xlsx,.xls,.csv,.pptx,.ppt,.docx,.doc"
        style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
    </div>

    <div id="aiFileChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;"></div>

    <label style="margin-top:12px;font-size:12px;color:var(--muted);">หรือวางข้อความโดยตรง (Ctrl+A จากเอกสาร)</label>
    <textarea id="aiPasteText" rows="10"
      placeholder="วางข้อความที่คัดลอกมาจาก PDF หน้าเว็บ หรือเอกสารใดก็ได้..."
      style="min-height:200px;max-height:200px;overflow-y:auto;resize:none;"></textarea>

    <div class="btn-row" style="margin-top:12px;">
      <button class="btn btn-primary" id="aiProcessBtn" style="flex:1">ให้ AI อ่านและสกัดรายการสินค้า</button>
      <button class="btn btn-ghost" id="aiClearBtn" style="padding:11px 18px;">🗑️ ล้าง</button>
    </div>

    <div class="progress" id="aiProg"><span id="aiProgBar"></span></div>
    <div class="ai-status" id="aiStatus"></div>

    <div id="aiOcrTextWrap" style="display:none;margin-top:14px;">
      <label style="font-size:12px;color:var(--muted);margin-bottom:4px;">📄 ข้อความที่ AI อ่านได้ (อ่านอย่างเดียว)</label>
      <textarea id="aiOcrText" readonly rows="10"
        style="min-height:200px;max-height:200px;overflow-y:auto;resize:none;background:#f8fafc;color:#555;font-size:12px;cursor:default;user-select:text;"></textarea>
    </div>

    <div id="aiResultWrap" style="display:none;margin-top:14px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <div style="font-weight:700;font-size:13px;color:var(--navy);" id="aiResultTitle">📋 รายการที่พบ</div>
        <button class="btn btn-ghost" id="aiAddRowBtn" style="padding:5px 12px;font-size:12px;">➕ เพิ่มแถว</button>
      </div>
      <div style="border:1px solid var(--line);border-radius:10px;overflow:hidden;">
        <table class="ai-result-table" style="width:100%;border-collapse:collapse;font-size:13px;">
          <thead>
            <tr style="background:#f8fafc;">
              <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--navy);border-bottom:2px solid var(--line);">ชื่อสินค้า</th>
              <th style="padding:8px 6px;text-align:center;width:90px;font-size:11px;color:var(--navy);border-bottom:2px solid var(--line);">จำนวน</th>
              <th style="padding:8px 6px;text-align:center;width:80px;font-size:11px;color:var(--navy);border-bottom:2px solid var(--line);">หน่วย</th>
              <th style="padding:8px 6px;text-align:center;width:36px;border-bottom:2px solid var(--line);"></th>
            </tr>
          </thead>
          <tbody id="aiResultBody"></tbody>
        </table>
      </div>
      <button class="btn btn-primary" id="aiUseBtn" style="margin-top:10px;">✅ เพิ่มรายการที่เลือกไปใบเสนอราคา</button>
    </div>
    <div class="hint" style="margin-top:14px;"></div>
  </div>

    {{-- ========== TAB: Manual ========== --}}
    <div class="pane" id="pane-manual">
      <div style="background:#f8fafc;border:1px solid var(--line);border-radius:6px;padding:12px 14px;margin-bottom:14px;">
        <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;flex-wrap:wrap;">
          <span style="font-size:12px;color:var(--navy);font-weight:700;white-space:nowrap;">วันที่</span>
          <input id="docDate" type="date" readonly style="background:#fff;color:var(--navy);font-weight:600;padding:5px 8px;font-size:13px;border-radius:6px;width:150px;">
          <span style="font-size:12px;color:var(--navy);font-weight:700;white-space:nowrap;margin-left:auto;">ลูกค้า</span>
          <input id="custCode" placeholder="รหัส" readonly style="background:#fff;color:var(--navy);font-weight:600;padding:5px 8px;font-size:12px;border-radius:6px;width:90px;">
        </div>
        <div class="search-wrap" style="margin-bottom:8px;">
          <input id="custCompany" placeholder="พิมพ์ชื่อบริษัท..." style="padding:8px 10px;font-size:13px;">
          <div class="ac-list" id="acList"></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;">
          <input id="contactName" placeholder="ผู้ติดต่อ" style="padding:8px 10px;font-size:13px;">
          <input id="custTel" placeholder="โทร." style="padding:8px 10px;font-size:13px;">
          <input id="custBranch" placeholder="สาขา" style="padding:8px 10px;font-size:13px;">
        </div>
        <textarea id="custAddr" rows="1" placeholder="ที่อยู่ลูกค้า ..." style="padding:8px 10px;font-size:13px;margin-top:6px;min-height:30px;overflow:hidden;"></textarea>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:6px;margin-top:6px;">
          <input id="custTax" placeholder="เลขผู้เสียภาษี" style="padding:8px 10px;font-size:13px;">
          <input id="validDays" type="number" placeholder="ยืนราคา (วัน)" min="0" style="padding:8px 10px;font-size:13px;">
          <input id="expireDate" type="text" readonly placeholder="Expire" style="background:#f7f9fc;color:var(--navy);font-weight:600;padding:8px 10px;font-size:13px;">
          <input id="creditDays" placeholder="เครดิต (วัน)" style="padding:8px 10px;font-size:13px;">
        </div>
      </div>

      {{-- ── ลูกค้าในกลุ่ม ── --}}
      <div id="refCustWrap" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;margin-bottom:10px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
          <span style="font-size:12px;font-weight:700;color:#92400e;">👥 ลูกค้าในกลุ่มเดียวกัน</span>
          <span style="font-size:11px;color:#a16207;" id="refCodesDisplay"></span>
        </div>
        <div id="refChips" style="display:grid;grid-template-columns:1fr 1fr;gap:4px;"></div>
      </div>

      {{-- ── ปุ่มเริ่มจับคู่ราคา ── --}}
      <div id="matchPriceWrap" style="display:none;margin-bottom:10px;">
        <button class="btn btn-primary" id="matchPriceBtn" onclick="startBatchMatch()"
          style="background:#0e7490;font-size:14px;width:100%;padding:13px 16px;">
          🔍 เริ่มจับคู่ราคาทั้งหมดจากประวัติการขาย
        </button>
        <div id="matchPriceHint" style="text-align:center;font-size:11px;color:#6b7280;margin-top:4px;"></div>
      </div>

      <div class="sec-title">รายการสินค้า / บริการ</div>
      <div class="match-bar" id="matchBar"><span class="spin">⏳</span><span id="matchBarTxt">กำลังจับคู่ราคา...</span></div>

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
              <th class="c" style="width:80px"></th>
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

      {{-- ── ลายเซ็น ── --}}
      <div class="sec-title">
        <span>ลายเซ็นพนักงานขาย</span>
        <div class="sig-mode-wrap" style="display:flex;gap:6px;flex-wrap:wrap;">
          <label style="display:inline-flex;align-items:center;gap:3px;cursor:pointer;margin:0;font-size:12px;font-weight:500;color:#555;">
            <input type="radio" name="sigMode" value="draw" checked onchange="switchSigMode('draw')"> ✏️ วาดลายเซ็น
          </label>
          <label style="display:inline-flex;align-items:center;gap:3px;cursor:pointer;margin:0;font-size:12px;font-weight:500;color:#555;">
            <input type="radio" name="sigMode" value="upload" onchange="switchSigMode('upload')"> 📎 e-Sign
          </label>
          <label style="display:inline-flex;align-items:center;gap:3px;cursor:pointer;margin:0;font-size:12px;font-weight:500;color:#555;">
            <input type="radio" name="sigMode" value="type" onchange="switchSigMode('type')"> ⌨️ พิมพ์ชื่อ
          </label>
        </div>
      </div>

      <div id="sigDrawWrap">
        <canvas id="sigPad" width="760" height="140" style="border:1px solid var(--line);border-radius:8px;cursor:crosshair;background:#fff;display:block;width:100%;max-width:100%;box-sizing:border-box;touch-action:none;"></canvas>
        <div class="btn-row">
          <button class="btn btn-ghost" id="sigClear" style="flex:1;font-size:12px">🗑️ ล้างลายเซ็น</button>
        </div>
      </div>

      <div id="sigUploadWrap" style="display:none;">
        <div id="sigDropZone" style="border:2px dashed #c7d0df;border-radius:10px;padding:20px;text-align:center;background:var(--soft);cursor:pointer;transition:.15s;" onclick="document.getElementById('sigFileInput').click()">
          <div id="sigUploadEmpty"><div style="font-size:28px">📎</div><div style="font-weight:600;font-size:13px;margin-top:4px;">คลิกเลือก หรือลากไฟล์ลายเซ็นมาวาง</div><div class="muted">รองรับ JPG, PNG</div></div>
          <img id="sigUploadPreview" style="display:none;max-height:100px;max-width:100%;border-radius:6px;">
        </div>
        <input type="file" id="sigFileInput" accept="image/*" hidden>
        <div class="btn-row"><button class="btn btn-ghost" style="flex:1;font-size:12px" onclick="clearSigUpload()">🗑️ ล้างรูปลายเซ็น</button></div>
      </div>

      <div id="sigTypeWrap" style="display:none;">
        <div style="border:1px solid var(--line);border-radius:8px;padding:16px;background:#fff;text-align:center;">
          <input id="sigTypeName" placeholder="พิมพ์ชื่อ-นามสกุล ..." style="text-align:center;font-size:20px;font-family:'Sarabun',cursive;border:none;border-bottom:1px solid var(--line);border-radius:0;padding:8px 4px;width:80%;" oninput="render();">
        </div>
      </div>

      <div style="margin-top:12px;background:#f8fafc;border:1px solid var(--line);border-radius:8px;padding:12px 14px;">
        <div style="font-size:12px;font-weight:700;color:var(--navy);margin-bottom:8px;">📋 ข้อมูลพนักงานขาย</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
          <input id="sellerTel" placeholder="เบอร์โทรศัพท์" style="padding:8px 10px;font-size:13px;" oninput="render()">
          <input id="sellerEmail" placeholder="อีเมล" type="email" style="padding:8px 10px;font-size:13px;" oninput="render()">
        </div>
      </div>
    </div>
  </div>

  {{-- Document Preview --}}
  <div class="card card-preview" style="display:none">
    <div class="preview-header">📄 ตัวอย่างใบเสนอราคา</div>
    <div class="doc-scroll"><div id="pages"></div></div>
    <div class="doc-tools" style="border-top:1px solid var(--line);border-bottom:none;justify-content:center;padding:14px 18px;flex-direction:column;align-items:center;gap:6px;">
      <button class="btn btn-green" id="printBtn" style="min-width:280px;max-width:400px;width:100%;padding:13px 40px;font-size:16px;">บันทึกใบเสนอราคา</button>
      <div id="validateMsg" class="muted" style="font-size:12px;color:#dc2626;text-align:center;"></div>
    </div>
  </div>
</div>

<script>
const $  = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

const SELLER = {
  name:      'บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด',
  tel:       '02-4727341-48',
  addrShort: 'เลขที่ 39/7 ถนนวุฒากาส แขวงตลาดพลู เขตธนบุรี กรุงเทพฯ 10600',
  tax:       '0105547065721',
};

const FIRST_PAGE_MAX=20,FIRST_LAST_PAGE_MAX=15,OTHER_PAGE_MAX=23,OTHER_LAST_PAGE_MAX=18;
let currentCustomerCode='',sellerSigData=null,sigUploadData=null,currentQuotationNo='';

function esc(s){return(s+'').replace(/"/g,'&quot;');}
function esc2(s){return(s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function fmt(n){return(Math.round(n*100)/100).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
function fmtComma(n){
  if(n===''||n===null||n===undefined)return'';
  const num=parseFloat(String(n).replace(/,/g,''));
  if(isNaN(num))return'';
  if(num===Math.floor(num))return num.toLocaleString('en-US');
  return num.toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:2});
}
function parseNum(s){
  if(s===''||s===null||s===undefined)return 0;
  return parseFloat(String(s).replace(/,/g,''))||0;
}
function onNumInput(el){
  const pos=el.selectionStart,oldLen=el.value.length,raw=el.value.replace(/,/g,'');
  if(raw===''||raw==='-'){el.value=raw;return;}
  if(raw.endsWith('.'))return;
  const num=parseFloat(raw);if(isNaN(num))return;
  el.value=fmtComma(num);
  const diff=el.value.length-oldLen;el.setSelectionRange(pos+diff,pos+diff);
}

function setAllBtnsLoading(loading){
  ['addItem','aiProcessBtn','aiClearBtn','aiUseBtn','aiAddRowBtn','printBtn','sigClear']
    .forEach(id=>{const el=document.getElementById(id);if(el)el.disabled=loading;});
  document.querySelectorAll('#itemRows .del, #itemRows .cart-btn').forEach(b=>b.disabled=loading);
  // ★ เพิ่ม: disable input ทุกช่องในตารางรายการสินค้า
  document.querySelectorAll('#itemRows input, #itemRows textarea, #itemRows .desc-icon')
    .forEach(el=>{el.disabled=loading;});
  // ★ ใส่ visual ให้รู้ว่ากำลังล็อก
  const wrap=document.querySelector('.items-table-wrap');
  if(wrap){wrap.style.opacity=loading?'0.6':'';wrap.style.pointerEvents=loading?'none':'';}
}
function setBtnLoading(btnEl,loading,loadingText){
  if(!btnEl)return;
  if(loading){
    if(!btnEl._origHTML)btnEl._origHTML=btnEl.innerHTML;
    btnEl.innerHTML=`<span class="ai-spin">⏳</span> ${loadingText||'กำลังทำงาน...'}`;
    btnEl.disabled=true;
  } else {
    if(btnEl._origHTML){btnEl.innerHTML=btnEl._origHTML;btnEl._origHTML=null;}
    btnEl.disabled=false;
  }
}
// ── Tab switching ──
$$('.tab').forEach(t=>t.onclick=()=>{
  $$('.tab').forEach(x=>x.classList.remove('active'));
  $$('.pane').forEach(x=>x.classList.remove('active'));
  t.classList.add('active');
  $('#pane-'+t.dataset.tab).classList.add('active');
  const isOcr=t.dataset.tab==='ai-ocr';
  const preview=$('.card-preview');if(preview)preview.style.display=isOcr?'none':'';
  $('.wrap').classList.toggle('ocr-mode',isOcr);
});

// ══════════════════════════════════════════
// AI OCR
// ══════════════════════════════════════════
let aiFiles=[];
const aiDrop=$('#aiDrop'),aiFileInput=$('#aiFileInput');
aiDrop.addEventListener('dragover',e=>{e.preventDefault();aiDrop.classList.add('drag-over');});
aiDrop.addEventListener('dragleave',()=>aiDrop.classList.remove('drag-over'));
aiDrop.addEventListener('drop',e=>{e.preventDefault();aiDrop.classList.remove('drag-over');addAiFiles([...e.dataTransfer.files]);});
aiFileInput.addEventListener('change',()=>{addAiFiles([...aiFileInput.files]);aiFileInput.value='';});
document.addEventListener('paste',e=>{
  if(!$('#pane-ai-ocr').classList.contains('active'))return;
  const imgs=[...(e.clipboardData?.items||[])].filter(i=>i.type.startsWith('image/'));
  if(!imgs.length)return;e.preventDefault();
  addAiFiles(imgs.map(i=>i.getAsFile()).filter(Boolean));
});
function addAiFiles(newFiles){newFiles.forEach(f=>{if(!aiFiles.some(s=>s.name===f.name&&s.size===f.size))aiFiles.push(f);});renderAiChips();}
function renderAiChips(){
  const wrap=$('#aiFileChips');wrap.innerHTML='';
  aiFiles.forEach((f,i)=>{
    const chip=document.createElement('span');chip.className='ai-chip';
    chip.innerHTML=`${f.name.split('.').pop().toUpperCase()} · ${f.name.length>22?f.name.slice(0,19)+'...':f.name} <span style="color:var(--muted);font-weight:400;">(${(f.size/1024).toFixed(0)}KB)</span><button class="ai-chip-del" onclick="aiRemoveFile(${i})">✕</button>`;
    wrap.appendChild(chip);
  });
}
window.aiRemoveFile=(i)=>{aiFiles.splice(i,1);renderAiChips();};

$('#aiClearBtn').addEventListener('click',()=>{
  aiFiles=[];renderAiChips();$('#aiPasteText').value='';
  $('#aiStatus').style.display='none';$('#aiResultWrap').style.display='none';
  $('#aiOcrTextWrap').style.display='none';$('#aiOcrText').value='';
});

$('#aiAddRowBtn').addEventListener('click',function(){
  const body=$('#aiResultBody'),tr=document.createElement('tr');
  tr.innerHTML=`<td style="padding:4px 6px;"><input type="text" class="ai-row-name" value="" placeholder="พิมพ์ชื่อสินค้า..." style="width:100%;padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:13px;font-family:inherit;"></td>
    <td style="padding:4px 6px;"><input type="number" class="ai-row-qty" value="1" min="0" style="width:80px;padding:4px 6px;border:1px solid var(--line);border-radius:6px;font-size:13px;text-align:right;font-family:inherit;"></td>
    <td style="padding:4px 6px;"><input type="text" class="ai-row-unit" value="" placeholder="หน่วย" style="width:72px;padding:4px 6px;border:1px solid var(--line);border-radius:6px;font-size:13px;font-family:inherit;"></td>
    <td style="padding:4px 6px;text-align:center;"><button onclick="this.closest('tr').remove();" style="background:none;border:1px solid transparent;border-radius:5px;color:#ccc;cursor:pointer;font-size:13px;width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;" onmouseover="this.style.background='#fef2f2';this.style.borderColor='#fecaca';this.style.color='#b91c1c';" onmouseout="this.style.background='none';this.style.borderColor='transparent';this.style.color='#ccc';">✕</button></td>`;
    body.insertBefore(tr, body.firstChild);
  tr.querySelector('.ai-row-name').focus();
});

$('#aiPasteText').addEventListener('paste',function(e){
  const html=e.clipboardData?.getData('text/html')||'',plain=e.clipboardData?.getData('text/plain')||'';
  const hasTab=plain.includes('\t'),hasTable=html.toLowerCase().includes('<table');
  if(!hasTab&&!hasTable)return;
  e.preventDefault();let lines=[];
  if(hasTable){
    const tmp=document.createElement('div');tmp.innerHTML=html;
    tmp.querySelectorAll('tr').forEach(tr=>{
      const cells=[...tr.querySelectorAll('th,td')].map(td=>td.innerText.trim().replace(/\n+/g,' '));
      if(cells.some(c=>c))lines.push(cells.join('\n'));
    });
  } else {
    plain.split('\n').forEach(row=>{
      const cells=row.split('\t').map(c=>c.trim());
      if(cells.some(c=>c))lines.push(cells.join('\n'));
    });
  }
  this.value=lines.join('\n');
});

$('#aiProcessBtn').addEventListener('click',async()=>{
  const text=$('#aiPasteText').value.trim();
  if(!aiFiles.length&&!text){showAiStatus('กรุณาอัพโหลดไฟล์หรือวางข้อความก่อน','error');return;}
  const btn=$('#aiProcessBtn');
  setBtnLoading(btn,true,'กำลังส่งไฟล์...');
  ['aiClearBtn','aiAddRowBtn','aiUseBtn'].forEach(id=>{const el=document.getElementById(id);if(el)el.disabled=true;});
  $('#aiProg').style.display='block';$('#aiResultWrap').style.display='none';$('#aiProgBar').style.width='10%';
  showAiStatus('<span class="ai-spin">⏳</span> กำลังส่งไฟล์ไปประมวลผล...','info');
  try{
    const fd=new FormData();
    aiFiles.forEach(f=>fd.append('files[]',f));
    fd.append('text',text);
    fd.append('_token',document.querySelector('meta[name="csrf-token"]').content);
    const res=await fetch('/soitem/ocr/process',{method:'POST',body:fd});
    const data=await res.json();
    if(!res.ok||data.status==='error')throw new Error(data.message||'เกิดข้อผิดพลาด');
    const jobName=data.job_name;
    btn.innerHTML='<span class="ai-spin"></span> AI กำลังอ่านเอกสาร...';
    showAiStatus('<span class="ai-spin">⏳</span> AI กำลังอ่านและสกัดรายการ...','info');
    let fakeP=15;
    const progTimer=setInterval(()=>{fakeP=Math.min(fakeP+1.5,90);$('#aiProgBar').style.width=fakeP+'%';},1000);
    const result=await new Promise((resolve,reject)=>{
      const pollTimer=setInterval(async()=>{
        try{
          const sr=await fetch(`/soitem/ocr/status/${jobName}`);const sd=await sr.json();
          if(sd.status==='done'){clearInterval(pollTimer);resolve(sd);}
          else if(sd.status==='error'){clearInterval(pollTimer);reject(new Error(sd.message||'Python error'));}
        }catch(e){clearInterval(pollTimer);reject(e);}
      },3000);
    });
    clearInterval(progTimer);$('#aiProgBar').style.width='100%';
    showAiStatus('✅ '+result.message,'success');renderAiResult(result);
  }catch(err){
    showAiStatus('❌ '+err.message,'error');
  }finally{
    setTimeout(()=>{$('#aiProg').style.display='none';$('#aiProgBar').style.width='0';},700);
    setBtnLoading(btn,false);
    ['aiClearBtn','aiAddRowBtn'].forEach(id=>{const el=document.getElementById(id);if(el)el.disabled=false;});
    const useBtn=document.getElementById('aiUseBtn');
    if(useBtn&&document.querySelectorAll('#aiResultBody tr').length>0)useBtn.disabled=false;
  }
});

function showAiStatus(msg,type){const s=$('#aiStatus');s.className='ai-status '+type;s.innerHTML=msg;s.style.display='block';}
async function runAiFallback(payload, headers){
  const r = await fetch('/soitem/ai-fallback-match', {method:'POST', headers, body:JSON.stringify(payload)});
  if(!r.ok) return [];
  const data = await r.json();
  if(data.status === 'done') return data.results || [];
  if(data.status === 'processing' && data.job_id){
    const start = Date.now();
    while(Date.now() - start < 180000){          // กันค้างเกิน 3 นาที
      await new Promise(res => setTimeout(res, 2500));
      const sr = await fetch('/soitem/ai-fallback-status/' + encodeURIComponent(data.job_id));
      if(!sr.ok) continue;
      const sd = await sr.json();
      if(sd.status === 'done') return sd.results || [];
    }
  }
  return [];
}
function renderAiResult(data){
  $('#aiResultTitle').textContent=`📋 รายการที่พบ (${data.table.length} รายการ)`;
  const body=$('#aiResultBody');body.innerHTML='';
  if(data.ocr_text){$('#aiOcrText').value=data.ocr_text;$('#aiOcrTextWrap').style.display='block';}
  if(!data.table.length){
    body.innerHTML='<tr><td colspan="4" style="padding:20px;text-align:center;color:var(--muted);">ไม่พบรายการสินค้า</td></tr>';
  } else {
    data.table.forEach(row=>{
      const qtyVal=(row.qty!==null&&row.qty!==undefined&&row.qty!=='')?row.qty:'',unit=row.unit||'';
      const tr=document.createElement('tr');
      tr.innerHTML=`<td style="padding:4px 6px;"><input type="text" class="ai-row-name" value="${esc(row.name)}" style="width:100%;padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:13px;font-family:inherit;"></td>
        <td style="padding:4px 6px;"><input type="number" class="ai-row-qty" value="${qtyVal}" min="0" style="width:80px;padding:4px 6px;border:1px solid var(--line);border-radius:6px;font-size:13px;text-align:right;font-family:inherit;"></td>
        <td style="padding:4px 6px;"><input type="text" class="ai-row-unit" value="${esc(unit)}" placeholder="หน่วย" style="width:72px;padding:4px 6px;border:1px solid var(--line);border-radius:6px;font-size:13px;font-family:inherit;"></td>
        <td style="padding:4px 6px;text-align:center;"><button onclick="this.closest('tr').remove();" style="background:none;border:1px solid transparent;border-radius:5px;color:#ccc;cursor:pointer;font-size:13px;width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;" onmouseover="this.style.background='#fef2f2';this.style.borderColor='#fecaca';this.style.color='#b91c1c';" onmouseout="this.style.background='none';this.style.borderColor='transparent';this.style.color='#ccc';">✕</button></td>`;
      body.appendChild(tr);
    });
  }
  $('#aiResultWrap').style.display='block';
}

$('#aiUseBtn').addEventListener('click',async()=>{
  const rows=[...document.querySelectorAll('#aiResultBody tr')];
  if(!rows.length){showAiStatus('ไม่มีรายการ','error');return;}
  const btn=$('#aiUseBtn');setBtnLoading(btn,true,'กำลังเพิ่มรายการ...');
  try{
    $('#itemRows').innerHTML='';
    rows.forEach(tr=>{
      const name=(tr.querySelector('.ai-row-name')?.value||'').trim();
      const qtyInput=tr.querySelector('.ai-row-qty')?.value;
      const qty=qtyInput!==''&&qtyInput!=null?parseFloat(qtyInput)||null:null;
      const unit=(tr.querySelector('.ai-row-unit')?.value||'').trim();
      $('#itemRows').appendChild(itemRow({desc:name,qty:qty??'',unit}));
    });
    $$('.tab').forEach(t=>t.classList.remove('active'));
    $$('.pane').forEach(p=>p.classList.remove('active'));
    document.querySelector('.tab[data-tab="manual"]').classList.add('active');
    $('#pane-manual').classList.add('active');
    const preview=$('.card-preview');if(preview)preview.style.display='';
    $('.wrap').classList.remove('ocr-mode');
    render();
    updateMatchPriceBtn();
  }finally{setBtnLoading(btn,false);}
});

// ══════════════════════════════════════════
// ITEMS TABLE
// ══════════════════════════════════════════
function itemRow(d={}){
  const tr=document.createElement('tr');tr.className='item';
  const qty=d.qty??1,price=d.price??0;
  tr.innerHTML=`<td class="td-num"></td>
    <td class="td-desc">
      <input class="desc" placeholder="ชื่อสินค้า" value="${esc(d.desc||'')}">
      <div class="desc-icons">
        <button class="desc-icon ic-price" title="ค้นราคาจากประวัติขาย" onclick="onIconPrice(this)">💰 ราคาขาย</button>
        <button class="desc-icon ic-ai" title="AI ค้นสินค้าใกล้เคียง" onclick="onIconAi(this)">🤖 AI</button>
        <button class="desc-icon ic-doc" title="ประวัติใบเสนอราคา" onclick="onIconDoc(this)">📁 เคยเสนอ</button>
      </div>
    </td>
    <td class="td-qty"><input class="qty" type="text" inputmode="decimal" placeholder="จำนวน" value="${fmtComma(qty)}" style="text-align:right;font-variant-numeric:tabular-nums;"></td>
    <td class="td-unit"><input class="unit" placeholder="หน่วย" value="${esc(d.unit||'')}"></td>
    <td class="td-price"><input class="price" type="text" inputmode="decimal" placeholder="ราคา/หน่วย" value="${fmtComma(price)}" style="text-align:right;font-variant-numeric:tabular-nums;"></td>
    <td class="td-amt">${fmt(parseNum(qty)*parseNum(price))}</td>
    <td class="td-act"><div class="act-wrap"><button class="cart-btn" title="เพิ่มรายละเอียดย่อย" onclick="addSubItem(this)">➕</button><button class="del" title="ลบ">✕</button></div></td>`;
  tr.querySelector('.del').onclick=()=>{tr.remove();render();updateMatchPriceBtn();};
  tr.querySelectorAll('input').forEach(i=>i.oninput=render);
  tr.querySelector('.qty').addEventListener('input',function(){onNumInput(this);});
  tr.querySelector('.price').addEventListener('input',function(){onNumInput(this);});
  tr.dataset.itemId='item_'+Date.now()+'_'+Math.random().toString(36).substr(2,6);
  updateIconButtonsState(tr); 
  return tr;
}

function addSubItem(btn){
  const parentRow=btn.closest('tr.item'),tdDesc=parentRow.querySelector('.td-desc');
  let wrap=tdDesc.querySelector('.sub-wrap');
  if(wrap){wrap.querySelector('.sub-detail').focus();return;}
  wrap=document.createElement('div');wrap.className='sub-wrap';wrap.style.cssText='position:relative;margin-top:2px;';
  const ta=document.createElement('textarea');ta.className='sub-detail';ta.placeholder='พิมพ์รายละเอียดย่อย...';
  ta.style.cssText='width:100%;border:1px solid var(--line);border-radius:6px;background:#fafbfc;padding:4px 28px 4px 8px;font-size:11.5px;color:#555;font-family:inherit;resize:none;min-height:28px;overflow:hidden;';
  ta.rows=1;ta.oninput=function(){this.style.height='auto';this.style.height=this.scrollHeight+'px';render();};
  const delBtn=document.createElement('button');delBtn.innerHTML='✕';
  delBtn.style.cssText='position:absolute;top:3px;right:3px;width:20px;height:20px;border:none;background:none;color:#ccc;font-size:11px;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;';
  delBtn.onclick=function(e){e.stopPropagation();wrap.remove();render();};
  wrap.appendChild(ta);wrap.appendChild(delBtn);tdDesc.appendChild(wrap);ta.focus();render();
}

// ══════════════════════════════════════════
// PRICE MATCH STORAGE + POPUP
// ══════════════════════════════════════════
const priceMatchData=new Map();
const docMatchData=new Map();   
const aiMatchData=new Map(); 

function onIconPrice(btn){
  const tr=btn.closest('tr.item');
  const desc=(tr.querySelector('.desc')?.value||'').trim();
  if(!desc)return;
  const itemId=tr.dataset.itemId||'';
  const matches=priceMatchData.get(itemId)||[];
  showPricePopup(btn,desc,matches,tr);
}

function onIconAi(btn){
  const tr=btn.closest('tr.item');
  const desc=(tr.querySelector('.desc')?.value||'').trim();
  if(!desc)return;
  const itemId=tr.dataset.itemId||'';
  const stored=aiMatchData.get(itemId)||{};
  const matches=stored.matches||stored||[];  // backward compat
  const meta={
    search_tokens:stored.search_tokens||[],
    candidates:stored.candidates||[],
    llm_picked:stored.llm_picked,
    llm_matched:stored.llm_matched||'',
  };
  showAiPopup(btn,desc,Array.isArray(matches)?matches:stored.matches||[],tr,meta);
}

function showAiPopup(anchorEl,desc,matches,tr,meta){
  const pop=$('#pricePop');
  const rect=anchorEl.getBoundingClientRect();
  const pw=Math.min(780,window.innerWidth*.94);
  let left=Math.max(8,Math.min(rect.left,window.innerWidth-pw-8));
  let top=rect.bottom+6;
  if(top+400>window.innerHeight)top=Math.max(8,rect.top-406);
  pop.style.left=left+'px';pop.style.top=top+'px';pop.style.display='block';

  $('#ppTitle').textContent='AI ค้นสินค้าใกล้เคียง: '+(desc.length>45?desc.slice(0,45)+'…':desc);

  // ── แสดง search tokens + candidates + LLM picked ──
  const tokEl=$('#ppTokens');
  meta=meta||{};
  const stoks=meta.search_tokens||[];
  const cands=meta.candidates||[];
  const picked=meta.llm_matched||'';

  if(stoks.length||cands.length){
    let infoHtml='';
    if(stoks.length){
      const chips=stoks.map(t=>'<span style="background:#ede9fe;color:#7c3aed;border-radius:4px;padding:1px 8px;margin:0 2px;font-weight:700;">'+esc2(t)+'</span>').join(' ');
      infoHtml+='🔍 token: '+chips+'<br>';
    }
    if(cands.length){
      infoHtml+='📋 candidates: '+cands.map((c,i)=>{
        const isSel=i===meta.llm_picked;
        return isSel
          ?'<b style="color:#7c3aed;">['+(i+1)+'] '+esc2(c.substring(0,35))+'</b>'
          :'<span style="color:#888;">['+(i+1)+'] '+esc2(c.substring(0,35))+'</span>';
      }).join(' · ');
    }
    if(picked) infoHtml+='<br>✅ LLM เลือก: <b style="color:#7c3aed;">'+esc2(picked.substring(0,50))+'</b>';
    tokEl.innerHTML=infoHtml;
    tokEl.style.display='';
    tokEl.style.background='#f5f3ff';
    tokEl.style.color='#7c3aed';
  } else {
    tokEl.style.display='none';
  }

  const body=$('#ppBody');
  if(!matches.length){
    body.innerHTML='<div class="pp-empty">ยังไม่มีข้อมูล — กด "เริ่มจับคู่ราคา" แล้ว AI จะค้นให้อัตโนมัติ</div>';
    return;
  }

  const top3=matches.slice(0,3);
  const currentPrice=parseNum(tr.querySelector('.price')?.value);
  let activeIdx=-1;
  if(currentPrice>0){
    const found=top3.findIndex(m=>Math.abs((m.unit_price||0)-currentPrice)<0.01);
    if(found>=0)activeIdx=found;
  }

  let html=`<table><thead><tr>
    <th>วันที่</th><th>ลูกค้า</th><th>SO No.</th>
    <th>ชื่อสินค้าในระบบ</th>
    <th style="text-align:right;">ราคา/หน่วย</th><th>หน่วย</th><th style="width:60px"></th>
  </tr></thead><tbody>`;

  top3.forEach((m,i)=>{
    const isActive=i===activeIdx;
    const rowBg=isActive?'background:#f5f3ff;border-left:3px solid #7c3aed;':'';
    html+=`<tr style="${rowBg}">
      <td style="white-space:nowrap;">${esc2(m.doc_date||'-')}</td>
      <td style="font-size:11px;max-width:140px;white-space:normal;word-break:break-word;line-height:1.3;">
        <div style="color:#7c3aed;font-weight:700;">[${esc2(m.customer_code||'')}]</div>
        <div style="color:#888;">${esc2(m.customer_name||'')}</div>
      </td>
      <td style="color:#7c3aed;font-size:11px;white-space:nowrap;">${esc2(m.so_no||'-')}</td>
      <td style="max-width:220px;word-break:break-word;white-space:normal;line-height:1.4;">${esc2(m.product_name||'')}</td>
      <td style="text-align:right;font-weight:700;color:var(--navy);white-space:nowrap;font-size:14px;">${fmt(m.unit_price||0)}${isActive?' <span style="font-size:10px;color:#7c3aed;">✔ ใช้อยู่</span>':''}</td>
      <td>${esc2(m.unit||'')}</td>
      <td>${isActive
        ?'<span style="font-size:11px;color:#7c3aed;font-weight:700;">✔</span>'
        :`<button class="pp-use" data-i="${i}" style="background:#7c3aed;">ใช้</button>`}</td>
    </tr>`;
  });

  html+='</tbody></table>';
  body.innerHTML=html;

  body.querySelectorAll('.pp-use').forEach(btn=>{
    btn.onclick=e=>{
      e.stopPropagation();
      const m=top3[parseInt(btn.dataset.i)];
      const pe=tr.querySelector('.price'),ue=tr.querySelector('.unit');
      if(pe&&m.unit_price!=null)pe.value=fmtComma(m.unit_price);
      if(ue&&m.unit)ue.value=m.unit;
      render();
      showAiPopup(anchorEl,desc,matches,tr);
    };
  });
}

function updateAiBadge(tr){
  const itemId=tr.dataset.itemId||'';
  const stored=aiMatchData.get(itemId)||{};
  const matches=stored.matches||[];
  const count=Math.min(matches.length,3);
  const btn=tr.querySelector('.ic-ai');
  if(!btn)return;
  let badge=btn.querySelector('.badge');
  if(count>0){
    if(!badge){badge=document.createElement('span');badge.className='badge';badge.style.background='#7c3aed';btn.appendChild(badge);}
    badge.textContent=count;
  } else {
    if(badge)badge.remove();
  }
  updateIconButtonsState(tr);
}

function onIconDoc(btn){
  const tr=btn.closest('tr.item');
  const desc=(tr.querySelector('.desc')?.value||'').trim();
  if(!desc)return;
  const itemId=tr.dataset.itemId||'';
  const matches=docMatchData.get(itemId)||[];
  showDocPopup(btn,desc,matches);
}

function showDocPopup(anchorEl,desc,data){
  const pop=$('#docPop');
  const rect=anchorEl.getBoundingClientRect();
  const pw=Math.min(720,window.innerWidth*.94);
  let left=Math.max(8,Math.min(rect.left,window.innerWidth-pw-8));
  let top=rect.bottom+6;
  if(top+400>window.innerHeight)top=Math.max(8,rect.top-406);
  pop.style.left=left+'px';pop.style.top=top+'px';pop.style.display='block';

  $('#dpTitle').textContent='📁 ประวัติใบเสนอราคา: '+(desc.length>45?desc.slice(0,45)+'…':desc);

  if(!data.length){
    $('#dpBody').innerHTML='<div class="dp-empty">ยังไม่มีข้อมูล — กด "เริ่มจับคู่ราคา" ก่อน</div>';
    return;
  }

  let html='<table><thead><tr>'+
    '<th>เลขที่</th>'+
    '<th>วันที่</th>'+
    '<th>บริษัท</th>'+
    '<th>ชื่อสินค้า</th>'+
    '<th style="text-align:right;white-space:nowrap;">ราคา/หน่วย</th>'+
    '<th>หน่วย</th>'+
    '</tr></thead><tbody>';

  data.forEach(r=>{
    const nameHtml=highlightTokens(r.product||'',r.matched_tokens||[]);
    html+='<tr>'+
      '<td style="color:#b45309;font-size:11px;max-width:120px;word-break:break-word;white-space:normal;line-height:1.3;font-weight:600;">'+esc2(r.quotation_no||'-')+'</td>'+
      '<td style="white-space:nowrap;">'+esc2(r.quotation_date||'-')+'</td>'+
      '<td style="font-size:11px;max-width:140px;white-space:normal;word-break:break-word;line-height:1.3;color:#555;">'+esc2(r.customer_company||'-')+'</td>'+
      '<td style="max-width:200px;word-break:break-word;white-space:normal;line-height:1.4;">'+nameHtml+'</td>'+
      '<td style="text-align:right;font-weight:700;color:var(--navy);white-space:nowrap;">'+fmt(r.price_per_unit||0)+'</td>'+
      '<td>'+esc2(r.unit||'')+'</td>'+
      '</tr>';
  });

  html+='</tbody></table>';
  $('#dpBody').innerHTML=html;
}

function updateDocBadge(tr){
  const itemId=tr.dataset.itemId||'';
  const matches=docMatchData.get(itemId)||[];
  const count=Math.min(matches.length,5);
  const btn=tr.querySelector('.ic-doc');
  if(!btn)return;
  let badge=btn.querySelector('.badge');
  if(count>0){
    if(!badge){badge=document.createElement('span');badge.className='badge';badge.style.background='#b45309';btn.appendChild(badge);}
    badge.textContent=count;
  } else {
    if(badge)badge.remove();
  }
  updateIconButtonsState(tr);
}

function showPricePopup(anchorEl,desc,matches,tr){
  const pop=$('#pricePop');
  const rect=anchorEl.getBoundingClientRect();
  const pw=Math.min(780,window.innerWidth*.94);
  let left=Math.max(8,Math.min(rect.left,window.innerWidth-pw-8));
  let top=rect.bottom+6;
  if(top+400>window.innerHeight)top=Math.max(8,rect.top-406);
  pop.style.left=left+'px';pop.style.top=top+'px';pop.style.display='block';

  $('#ppTitle').textContent='💰 ราคาจากประวัติขาย: '+(desc.length>50?desc.slice(0,50)+'…':desc);

  const tokEl=$('#ppTokens');
  if(matches.length&&matches[0].matched_tokens?.length){
    const chips=matches[0].matched_tokens.map(t=>
      `<span style="background:#cffafe;color:#0e7490;border-radius:4px;padding:1px 8px;margin:0 2px;font-weight:700;">${esc2(t)}</span>`
    ).join('<span style="color:#94a3b8;margin:0 2px;">+</span>');
    tokEl.innerHTML='🔍 จับด้วย token: '+chips;
    tokEl.style.display='';
  } else {
    tokEl.style.display='none';
  }

  const body=$('#ppBody');
  if(!matches.length){
    body.innerHTML='<div class="pp-empty">ยังไม่มีข้อมูล — กด "เริ่มจับคู่ราคา" ก่อน</div>';
    return;
  }

  const top3=matches.slice(0,3);

  // หา active index จากราคาปัจจุบันในช่อง
  const currentPrice=parseNum(tr.querySelector('.price')?.value);
  let activeIdx=0; // default อันดับ 1
  if(currentPrice>0){
    const found=top3.findIndex(m=>Math.abs((m.unit_price||0)-currentPrice)<0.01);
    if(found>=0)activeIdx=found;
  }

  let html=`<table><thead><tr>
    <th>วันที่</th>
    <th>ลูกค้า</th>
    <th>SO No.</th>
    <th>ชื่อสินค้าในระบบ</th>
    <th style="text-align:right;white-space:nowrap;">ราคา/หน่วย</th>
    <th>หน่วย</th>
    <th style="width:60px"></th>
  </tr></thead><tbody>`;

  top3.forEach((m,i)=>{
    const nameHtml=highlightTokens(m.product_name||'',m.matched_tokens||[]);
    const isActive=i===activeIdx;
    const rowBg=isActive?'background:#ecfeff;border-left:3px solid #0e7490;':'';
    const rowClass=isActive?'pp-active':'';
    html+=`<tr class="${rowClass}" style="${rowBg}">
      <td style="white-space:nowrap;">${esc2(m.doc_date||'-')}</td>
      <td style="font-size:11px;max-width:140px;white-space:normal;word-break:break-word;line-height:1.3;">
        <div style="color:#0e7490;font-weight:700;">[${esc2(m.customer_code||'')}]</div>
        <div style="color:#888;">${esc2(m.customer_name||'')}</div>
      </td>
      <td style="color:#0e7490;font-size:11px;white-space:nowrap;">${esc2(m.so_no||'-')}</td>
      <td style="max-width:220px;word-break:break-word;white-space:normal;line-height:1.4;">${nameHtml}</td>
      <td style="text-align:right;font-weight:700;color:var(--navy);white-space:nowrap;font-size:14px;">${fmt(m.unit_price||0)}${isActive?' <span style="font-size:10px;color:#0e7490;">✔ ใช้อยู่</span>':''}</td>
      <td>${esc2(m.unit||'')}</td>
      <td>${isActive
        ?'<span style="font-size:11px;color:#0e7490;font-weight:700;">✔</span>'
        :`<button class="pp-use" data-i="${i}">ใช้</button>`}</td>
    </tr>`;
  });

  html+='</tbody></table>';
  body.innerHTML=html;

  body.querySelectorAll('.pp-use').forEach(btn=>{
    btn.onclick=e=>{
      e.stopPropagation();
      const idx=parseInt(btn.dataset.i);
      const m=top3[idx];
      const pe=tr.querySelector('.price'),ue=tr.querySelector('.unit');
      if(pe&&m.unit_price!=null)pe.value=fmtComma(m.unit_price);
      if(ue&&m.unit)ue.value=m.unit;
      render();
      // refresh popup เพื่ออัพเดท highlight
      showPricePopup(anchorEl,desc,matches,tr);
    };
  });
}

// ── highlight tokens อย่างปลอดภัย (escape ก่อน แล้วค่อย wrap) ──
function highlightTokens(text,tokens){
  if(!tokens||!tokens.length)return esc2(text);
  // เรียง token ยาวสุดก่อน เพื่อไม่ให้ token สั้นตัด token ยาว
  const sorted=[...tokens].sort((a,b)=>b.length-a.length);
  const escaped=sorted.map(t=>t.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'));
  const re=new RegExp('('+escaped.join('|')+')','gi');
  const parts=text.split(re);
  return parts.map(part=>{
    const isMatch=sorted.some(t=>part.toLowerCase()===t.toLowerCase());
    return isMatch
      ?'<b style="background:#fef9c3;color:#713f12;border-radius:2px;padding:0 2px;">'+esc2(part)+'</b>'
      :esc2(part);
  }).join('');
}

function updatePriceBadge(tr){
  const itemId=tr.dataset.itemId||'';
  const matches=priceMatchData.get(itemId)||[];
  const count=Math.min(matches.length,3);
  const btn=tr.querySelector('.ic-price');
  if(!btn)return;
  let badge=btn.querySelector('.badge');
  if(count>0){
    if(!badge){badge=document.createElement('span');badge.className='badge';btn.appendChild(badge);}
    badge.textContent=count;
  } else {
    if(badge)badge.remove();
  }
   updateIconButtonsState(tr); 
}
function updateIconButtonsState(tr){
  const itemId=tr.dataset.itemId||'';

  const priceCount=(priceMatchData.get(itemId)||[]).length;
  const aiStored=aiMatchData.get(itemId)||{};
  const aiCount=(aiStored.matches||[]).length;
  const docCount=(docMatchData.get(itemId)||[]).length;

  const setState=(sel,hasData)=>{
    const btn=tr.querySelector(sel);
    if(!btn)return;
    btn.disabled=!hasData;
    btn.style.opacity=hasData?'':'0.4';
    btn.style.cursor=hasData?'pointer':'not-allowed';
  };

  setState('.ic-price',priceCount>0);
  setState('.ic-ai',  aiCount>0);
  setState('.ic-doc', docCount>0);
}

// ══════════════════════════════════════════
// BATCH MATCH PRICE
// ══════════════════════════════════════════
function getAllSearchCodes(){
  const codes=[currentCustomerCode];
  refCustomers.forEach(c=>{if(c.code&&!codes.includes(c.code))codes.push(c.code);});
  return codes;
}

function updateMatchPriceBtn(){
  const wrap=document.getElementById('matchPriceWrap');
  if(!wrap)return;
  const hasItems=document.querySelectorAll('#itemRows .item').length>0;
  const hasCust=!!currentCustomerCode;
  const hint=document.getElementById('matchPriceHint');
  const btn=document.getElementById('matchPriceBtn');

  if(!hasItems){
    wrap.style.display='none';
    return;
  }

  wrap.style.display='';

  if(!hasCust){
    btn.disabled=true;
    btn.style.opacity='.5';
    if(hint) hint.textContent=' กรุณาเลือกลูกค้าก่อนจึงจะจับคู่ราคาได้';
  } else {
    btn.disabled=false;
    btn.style.opacity='1';
    const count=document.querySelectorAll('#itemRows .item').length;
    const allCodes=getAllSearchCodes();
    if(hint) hint.textContent=`พร้อมจับคู่ ${count} รายการ · ค้นจาก ${allCodes.length} รหัสลูกค้า: ${allCodes.join(', ')}`;
  }
}

async function startBatchMatch(){
  const rows=[...$$('#itemRows .item')];
  if(!rows.length||!currentCustomerCode)return;
  const allCodes=getAllSearchCodes();
  const allNames=rows.map(tr=>(tr.querySelector('.desc')?.value||'').trim());
  if(allNames.every(n=>n.length<2))return;

  const btn=document.getElementById('matchPriceBtn');
  const bar=$('#matchBar');
  const csrf=document.querySelector('meta[name="csrf-token"]').content;
  const headers={'Content-Type':'application/json','X-CSRF-TOKEN':csrf};

  setBtnLoading(btn,true,'กำลังจับคู่...');
  setAllBtnsLoading(true);
  bar.style.display='flex';

  // ★ แบ่ง chunk ละ 10
  const CHUNK=10;
  const allPriceData=new Array(allNames.length).fill(null).map(()=>({matches:[]}));
  const allDocData  =new Array(allNames.length).fill(null).map(()=>({matches:[]}));

  try{
    // ═══ ขั้น 1: Token match — chunk by chunk ═══
    const totalChunks=Math.ceil(allNames.length/CHUNK);
    for(let c=0;c<totalChunks;c++){
      const start=c*CHUNK, end=Math.min(start+CHUNK,allNames.length);
      const chunkNames=allNames.slice(start,end);
      $('#matchBarTxt').textContent=`⏳ ขั้น 1/2: ค้นราคา รอบที่${c+1}/${totalChunks} (${start+1}-${end})`;

      // 💰 ราคา
      try{
        const r=await fetch('/soitem/batch-match',{method:'POST',headers,
          body:JSON.stringify({customer_codes:allCodes,items:chunkNames})});
        if(r.ok){const d=await r.json();d.forEach((v,i)=>{if(v)allPriceData[start+i]=v;});}
      }catch(e){console.warn('price chunk',c,e);}

      // 📁 เอกสาร
      try{
        const r=await fetch('/soitem/batch-quotation-history',{method:'POST',headers,
          body:JSON.stringify({items:chunkNames})});
        if(r.ok){const d=await r.json();d.forEach((v,i)=>{if(v)allDocData[start+i]=v;});}
      }catch(e){console.warn('doc chunk',c,e);}
    }

    let totalPrice=0,totalDoc=0;
    const needAiPrice=[],needAiDoc=[];

    rows.forEach((tr,i)=>{
      const itemId=tr.dataset.itemId||'';
      const pm=(allPriceData[i]?.matches)||[];
      priceMatchData.set(itemId,pm);updatePriceBadge(tr);
      if(pm.length){totalPrice++;applyPriceToRow(tr,pm[0]);}
      else needAiPrice.push({name:allNames[i],idx:i});

      const dm=(allDocData[i]?.matches)||[];
      docMatchData.set(itemId,dm);updateDocBadge(tr);
      if(dm.length)totalDoc++;
      else needAiDoc.push({name:allNames[i],idx:i});
    });
    render();
    $('#matchBarTxt').textContent='✅ ขั้น 1: ราคา '+totalPrice+'/'+rows.length+' · เสนอราคา '+totalDoc+'/'+rows.length;

    // ═══ ขั้น 2: AI fallback — chunk by chunk ═══
    if(needAiPrice.length||needAiDoc.length){
      const aiChunks=Math.ceil(Math.max(needAiPrice.length,needAiDoc.length)/CHUNK);

      for(let c=0;c<Math.ceil(needAiPrice.length/CHUNK);c++){
        const slice=needAiPrice.slice(c*CHUNK,(c+1)*CHUNK);
        if(!slice.length)break;
        $('#matchBarTxt').textContent=`⏳ ขั้น 2 AI ราคา รอบ ${c+1}: ${slice.length} รายการ...`;
        try{
          const data = await runAiFallback({items:slice, customer_codes:allCodes, type:'price'}, headers);
          data.forEach((result,j)=>{
            const origIdx=slice[j]?.idx; if(origIdx==null)return;
            const tr=rows[origIdx]; if(!tr)return;
            const itemId=tr.dataset.itemId||'';
            const matches=result.matches||[];
            if(matches.length){
              aiMatchData.set(itemId,{matches,search_tokens:result.search_tokens||[],candidates:result.candidates||[],llm_picked:result.llm_picked,llm_matched:result.llm_matched||''});
              updateAiBadge(tr);applyPriceToRow(tr,matches[0]);totalPrice++;
            }
          });
        }catch(e){console.warn('ai price chunk',c,e);}
      }

      for(let c=0;c<Math.ceil(needAiDoc.length/CHUNK);c++){
        const slice=needAiDoc.slice(c*CHUNK,(c+1)*CHUNK);
        if(!slice.length)break;
        $('#matchBarTxt').textContent=`⏳ ขั้น 2 AI เสนอราคา รอบ ${c+1}: ${slice.length} รายการ...`;
        try{
            const data = await runAiFallback({items:slice, type:'doc'}, headers);
            data.forEach((result,j)=>{
              const origIdx=slice[j]?.idx; if(origIdx==null)return;
              const tr=rows[origIdx]; if(!tr)return;
              const itemId=tr.dataset.itemId||'';
              const matches=result.matches||[];
              if(matches.length){docMatchData.set(itemId,matches);updateDocBadge(tr);totalDoc++;}
            });
        }catch(e){console.warn('ai doc chunk',c,e);}
      }
      render();
    }

    const aiCount=[...aiMatchData.values()].filter(v=>v?.matches?.length).length;
    $('#matchBarTxt').textContent='✅ ราคา '+totalPrice+'/'+rows.length+(aiCount?' (🤖 AI '+aiCount+')':'')+' · เสนอราคา '+totalDoc+'/'+rows.length;
    setTimeout(()=>{bar.style.display='none';},4000);

  }catch(err){
    $('#matchBarTxt').textContent='❌ '+err.message;
    setTimeout(()=>{bar.style.display='none';},5000);
  }finally{
    setAllBtnsLoading(false);setBtnLoading(btn,false);
  }
}
// ── helper: ใส่ราคา+source ใน row ──
function applyPriceToRow(tr,m){
  const pe=tr.querySelector('.price'),ue=tr.querySelector('.unit');
  if(pe&&m.unit_price>0)pe.value=fmtComma(m.unit_price);
  if(ue&&m.unit)ue.value=m.unit;
  tr.querySelector('.price-src')?.remove();
  if(!m.unit_price||m.unit_price<=0)return;
  const src=document.createElement('div');
  src.className='price-src';
  src.style.cssText='font-size:10px;color:#0e7490;margin-top:1px;line-height:1.3;';
  const srcDate=esc2(m.doc_date||'');
  const srcCust=esc2((m.customer_code||'')+(m.customer_name?' '+m.customer_name:''));
  const srcSo=esc2(m.so_no||'');
  const srcName=esc2((m.product_name||'').substring(0,35));
  const srcPrice=fmt(m.unit_price);
  const srcUnit=esc2(m.unit||'');
  const isAi=m.matched_tokens&&m.matched_tokens[0]==='🤖 AI';
  src.innerHTML=(isAi?'🤖 ':'✔ ')+srcDate+' · '+srcCust+' · '+srcSo+' · '+srcName+' · <b>'+srcPrice+'</b> '+srcUnit;
  if(isAi)src.style.color='#7c3aed';
  tr.querySelector('.td-desc').appendChild(src);
}

$('#addItem').onclick=()=>{
  const tbody = $('#itemRows');
  const newRow = itemRow();
  tbody.insertBefore(newRow, tbody.firstChild);
  render();
  updateMatchPriceBtn();
};

// ══════════════════════════════════════════
// CUSTOMER SEARCH
// ══════════════════════════════════════════
async function searchCompany(keyword){
  if(!keyword||keyword.length<2)return;
  try{
    const res=await fetch('/soitem/customers/search?q='+encodeURIComponent(keyword),{headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}});
    if(!res.ok)throw new Error('HTTP '+res.status);
    const data=await res.json();
    if(!data.length){$('#acList').innerHTML='<div class="ac-empty">ไม่พบข้อมูลลูกค้า</div>';$('#acList').style.display='block';}
    else showAcResults(data);
  }catch(err){$('#acList').innerHTML='<div class="ac-empty">เกิดข้อผิดพลาด: '+esc2(err.message)+'</div>';$('#acList').style.display='block';}
}
let searchTimer=null;
function triggerCompanySearch(el){clearTimeout(searchTimer);const raw=el.value.trim();if(raw.length<2){$('#acList').style.display='none';return;}searchTimer=setTimeout(()=>searchCompany(raw),300);}
$('#custCompany').addEventListener('input',function(){triggerCompanySearch(this);});
$('#custCompany').addEventListener('paste',function(){const el=this;setTimeout(()=>triggerCompanySearch(el),50);});
$('#custCompany').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();clearTimeout(searchTimer);searchCompany(this.value.trim());}});

function showAcResults(companies){
  const list=$('#acList');list.innerHTML='';list.style.display='block';
  companies.forEach(c=>{
    const code=(c.customer_code||'').trim(),name=(c.customer_name||'').trim();
    const div=document.createElement('div');div.className='ac-item';
    div.innerHTML=`<div class="ac-name">${code?`<span style="color:var(--blue);margin-right:6px;">[${esc2(code)}]</span>`:''} ${esc2(name)}</div><div class="ac-sub">${esc2(c.address||'—')}</div>`;
    div.onclick=()=>{
      $('#custCode').value=code;$('#custCompany').value=name;$('#custAddr').value=c.address||'';
      $('#custTel').value=c.phone||'';$('#custTax').value=c.tax_id||'';$('#custBranch').value=c.branch||'';
      if(c.contact_name)$('#contactName').value=c.contact_name;
      list.style.display='none';currentCustomerCode=code;render();onCustomerSelected();
    };
    list.appendChild(div);
  });
}

document.addEventListener('click',e=>{if(!$('.search-wrap')?.contains(e.target))$('#acList').style.display='none';});

// ══════════════════════════════════════════
// RELATED CUSTOMERS (group)
// ══════════════════════════════════════════
let refCustomers=[];

async function onCustomerSelected(){
  refCustomers=[];
  renderRefChips();
  if(!currentCustomerCode)return;
  try{
    const res=await fetch('/soitem/customers/related/'+encodeURIComponent(currentCustomerCode));
    if(res.ok){
      const related=await res.json();
      refCustomers=related.filter(c=>c.customer_code&&c.customer_code!==currentCustomerCode)
        .map(c=>({code:c.customer_code,name:c.customer_name||''}));
      renderRefChips();
    }
  }catch(e){console.warn('fetch related error',e);}
  updateMatchPriceBtn();
}

function renderRefChips(){
  const wrap=$('#refCustWrap'),chips=$('#refChips');
  if(!refCustomers.length){wrap.style.display='none';return;}
  wrap.style.display='';
  $('#refCodesDisplay').textContent=refCustomers.length+' ราย';
  chips.innerHTML='';
  refCustomers.sort((a,b)=>a.code.localeCompare(b.code));
  refCustomers.forEach(c=>{
    const chip=document.createElement('div');
    chip.style.cssText='display:flex;align-items:center;gap:4px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:4px 8px;font-size:11px;color:#92400e;min-width:0;';
    chip.innerHTML=`<span style="font-weight:700;white-space:nowrap;flex-shrink:0;color:#b45309;">${esc2(c.code)}</span><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0;">${esc2(c.name)}</span>`;
    chips.appendChild(chip);
  });
}

// ══════════════════════════════════════════
// SIGNATURE
// ══════════════════════════════════════════
function switchSigMode(mode){
  $('#sigDrawWrap').style.display=mode==='draw'?'':'none';
  $('#sigUploadWrap').style.display=mode==='upload'?'':'none';
  $('#sigTypeWrap').style.display=mode==='type'?'':'none';
  render();
}
const sigCanvas=$('#sigPad'),sigCtx=sigCanvas.getContext('2d');
let sigDrawing=false;
function sigPos(e){const r=sigCanvas.getBoundingClientRect(),t=e.touches?e.touches[0]:e;return{x:(t.clientX-r.left)*(sigCanvas.width/r.width),y:(t.clientY-r.top)*(sigCanvas.height/r.height)};}
function sigStart(e){e.preventDefault();sigDrawing=true;const p=sigPos(e);sigCtx.beginPath();sigCtx.moveTo(p.x,p.y);}
function sigMove(e){if(!sigDrawing)return;e.preventDefault();const p=sigPos(e);sigCtx.lineWidth=2.2;sigCtx.lineCap='round';sigCtx.lineJoin='round';sigCtx.strokeStyle='#111';sigCtx.lineTo(p.x,p.y);sigCtx.stroke();}
function sigEnd(){sigDrawing=false;sellerSigData=trimSignature(sigCanvas);render();}
function trimSignature(canvas){const ctx=canvas.getContext('2d'),w=canvas.width,h=canvas.height,img=ctx.getImageData(0,0,w,h),d=img.data;let top=h,left=w,bottom=0,right=0;for(let y=0;y<h;y++)for(let x=0;x<w;x++){const a=d[(y*w+x)*4+3];if(a>10){if(y<top)top=y;if(y>bottom)bottom=y;if(x<left)left=x;if(x>right)right=x;}}if(top>bottom)return null;const pad=10;top=Math.max(0,top-pad);left=Math.max(0,left-pad);bottom=Math.min(h-1,bottom+pad);right=Math.min(w-1,right+pad);const tw=right-left+1,th=bottom-top+1,tc=document.createElement('canvas');tc.width=tw;tc.height=th;tc.getContext('2d').drawImage(canvas,left,top,tw,th,0,0,tw,th);return tc.toDataURL('image/png');}
sigCanvas.addEventListener('mousedown',sigStart);sigCanvas.addEventListener('mousemove',sigMove);sigCanvas.addEventListener('mouseup',sigEnd);sigCanvas.addEventListener('mouseleave',sigEnd);
sigCanvas.addEventListener('touchstart',sigStart,{passive:false});sigCanvas.addEventListener('touchmove',sigMove,{passive:false});sigCanvas.addEventListener('touchend',sigEnd);
$('#sigClear').onclick=()=>{sigCtx.clearRect(0,0,sigCanvas.width,sigCanvas.height);sellerSigData=null;render();};
$('#sigFileInput').onchange=e=>{const f=e.target.files[0];if(!f||!f.type.startsWith('image/'))return;const r=new FileReader();r.onload=()=>{sigUploadData=r.result;$('#sigUploadPreview').src=sigUploadData;$('#sigUploadPreview').style.display='block';$('#sigUploadEmpty').style.display='none';render();};r.readAsDataURL(f);};
const sigDZ=$('#sigDropZone');
sigDZ.ondragover=e=>{e.preventDefault();sigDZ.style.borderColor='var(--blue)';};
sigDZ.ondragleave=()=>{sigDZ.style.borderColor='#c7d0df';};
sigDZ.ondrop=e=>{e.preventDefault();sigDZ.style.borderColor='#c7d0df';const f=e.dataTransfer.files[0];if(f&&f.type.startsWith('image/')){const r=new FileReader();r.onload=()=>{sigUploadData=r.result;$('#sigUploadPreview').src=sigUploadData;$('#sigUploadPreview').style.display='block';$('#sigUploadEmpty').style.display='none';render();};r.readAsDataURL(f);}};
function clearSigUpload(){sigUploadData=null;$('#sigUploadPreview').style.display='none';$('#sigUploadEmpty').style.display='';$('#sigFileInput').value='';render();}
function getActiveSigMode(){return document.querySelector('input[name="sigMode"]:checked')?.value||'draw';}
function sellerSigHtml(){
  const mode=getActiveSigMode();
  if(mode==='draw'&&sellerSigData)return`<img class="sig-img" src="${sellerSigData}">`;
  if(mode==='upload'&&sigUploadData)return`<img class="sig-img" src="${sigUploadData}">`;
  if(mode==='type'){const typed=($('#sigTypeName')?.value||'').trim();if(typed)return`<div style="position:absolute;left:50%;bottom:30px;transform:translateX(-50%);font-size:16px;font-family:'Sarabun',cursive;color:#333;white-space:nowrap;">${esc2(typed)}</div>`;}
  return'';
}

// ══════════════════════════════════════════
// DOCUMENT RENDER
// ══════════════════════════════════════════
function val(id){return esc2($('#'+id).value||'');}
function rawVal(id){return($('#'+id).value||'').trim();}
function fmtThaiDateStr(v){if(!v)return'—';const d=new Date(v);if(isNaN(d))return v;return String(d.getDate()).padStart(2,'0')+'/'+String(d.getMonth()+1).padStart(2,'0')+'/'+(d.getFullYear()+543);}
function fmtThaiDate(){return fmtThaiDateStr(rawVal('docDate'));}
function custInfoHtml(){
  const codeHtml=rawVal('custCode')?`<span style="color:var(--navy);font-size:11px;">[${val('custCode')}]</span>`:'';
  return`<div class="cust-head">ข้อมูลลูกค้า ${codeHtml}</div>
    <table class="cinfo-table">
      <tr><td class="ck">ชื่อลูกค้า</td><td class="cv" colspan="2">${val('custCompany')}</td><td class="ck">ยืนราคาภายใน</td><td class="cv" colspan="3">${val('validDays')}${rawVal('validDays')?' วัน':''}</td></tr>
      <tr><td class="ck">ผู้ติดต่อ</td><td class="cv" colspan="2">${val('contactName')}</td><td class="ck">Expire Date</td><td class="cv" colspan="3">${fmtThaiDateStr($('#expireDate').dataset.raw||'')}</td></tr>
      <tr><td class="ck" rowspan="2" style="vertical-align:top">ที่อยู่</td><td class="cv" colspan="2" rowspan="2" style="vertical-align:top">${val('custAddr')}</td><td class="ck">จำนวนวันเครดิต</td><td class="cv" colspan="3">${val('creditDays')}${rawVal('creditDays')?' วัน':''}</td></tr>
      <tr><td class="ck" colspan="4"></td></tr>
      <tr><td colspan="7" style="padding:1.5px 4px;"><span class="ck" style="display:inline;">เลขผู้เสียภาษี</span> <span class="cv" style="display:inline;margin-right:8px;">${val('custTax')}</span><span class="ck" style="display:inline;">สาขา</span> <span class="cv" style="display:inline;">${val('custBranch')}</span></td></tr>
      <tr><td class="ck">โทรศัพท์</td><td class="cv" colspan="6">${val('custTel')}</td></tr>
    </table>`;
}
function pageHtml(rows,pg,pageCount,isLast,isFirst,T){
  const sellerContact=(rawVal('sellerTel')||rawVal('sellerEmail'))?`<div style="position:absolute;bottom:-18px;left:0;width:100%;text-align:center;font-size:10px;color:#555;">${rawVal('sellerTel')?esc2(rawVal('sellerTel')):''}${rawVal('sellerTel')&&rawVal('sellerEmail')?' &nbsp; ':''}${rawVal('sellerEmail')?esc2(rawVal('sellerEmail')):''}</div>`:'';
  const noteText=rawVal('note');
  const noteHtml=noteText?`<div style="margin-top:8px;margin-bottom:4px;max-height:120px;overflow:hidden;"><div style="font-weight:700;font-size:12px;color:#333;margin-bottom:2px;">หมายเหตุ:</div><div style="font-size:12px;color:#555;line-height:1.6;white-space:pre-wrap;word-break:break-word;">${esc2(noteText)}</div></div>`:'';
  const foot=isLast?`<div class="page-footer-keep">
    <div class="ctot"><div class="tr"><span class="lbl">รวมเป็นเงิน</span><span>${fmt(T.gross)}</span></div><div class="tr"><span class="lbl">ภาษีมูลค่าเพิ่ม (7%)</span><span>${fmt(T.vat)}</span></div><div class="tr grand"><span>รวมเป็นเงินทั้งสิ้น</span><span>${fmt(T.grand)}</span></div><div class="baht">(${bahtText(T.grand)})</div></div>
    ${noteHtml}
    <div class="cfoot" style="margin-top:auto;padding-top:${noteText?'10':'20'}px;"><div class="ssign">
      <div class="sg"><div class="sgline">ผู้อนุมัติซื้อ</div></div>
      <div class="sg" style="position:relative;">${sellerSigHtml()}<div class="sgline">พนักงานขาย</div>${sellerContact}</div>
      <div class="sg"><div class="sgline">ผู้จัดการฝ่ายขาย</div></div>
    </div></div></div>`:'';
  return`<div class="doc page">
    <div class="qhead">
      <div class="seller-top"><div class="co">${esc2(SELLER.name)}</div><div class="sln">โทร. ${esc2(SELLER.tel)}</div><div class="sln">${esc2(SELLER.addrShort)}</div><div class="sln">เลขประจำตัวผู้เสียภาษี ${esc2(SELLER.tax)}</div></div>
      <div style="text-align:right"><div class="qbig">Quotation</div><div class="qmeta">${currentQuotationNo?'<div style="font-size:14px;color:#333;font-weight:700;margin-bottom:4px;">เลขที่ '+esc2(currentQuotationNo)+'</div>':''}วันที่ ${fmtThaiDate()}</div></div>
    </div>
    <div class="bluebar">ใบเสนอราคา</div>
    ${isFirst?custInfoHtml():''}
    <table class="itbl"><thead><tr><th class="c" style="width:8%">ลำดับ</th><th class="l">รายการสินค้า</th><th class="c" style="width:10%">จำนวน</th><th class="c" style="width:8%">หน่วย</th><th class="r" style="width:15%">ราคา/หน่วย</th><th class="r" style="width:16%">ราคารวม</th></tr></thead><tbody>${rows}</tbody></table>
    <div class="flexspace"></div>
    ${foot}
    <div class="page-number">หน้า ${pg} / ${pageCount}</div>
  </div>`;
}
// ══════════════════════════════════════════
// VALIDATE — ก่อนบันทึก
// ══════════════════════════════════════════
function validateBeforeSave(){
  const errors=[];
  const checks=[
    {id:'custCompany', label:'ชื่อบริษัท'},
    {id:'contactName', label:'ผู้ติดต่อ'},
    {id:'custTel',     label:'โทรศัพท์'},
    {id:'custAddr',    label:'ที่อยู่'},
    {id:'custTax',     label:'เลขผู้เสียภาษี'},
    {id:'custBranch',  label:'สาขา'},
    {id:'validDays',   label:'ยืนราคา (วัน)'},
    {id:'creditDays',  label:'เครดิต (วัน)'},
    // ★ เพิ่ม
    {id:'sellerTel',   label:'เบอร์โทรพนักงานขาย'},
    {id:'sellerEmail', label:'อีเมลพนักงานขาย'},
  ];
  checks.forEach(c=>{
    const el=document.getElementById(c.id);
    if(!el)return;
    const v=(el.value||'').trim();
    if(!v){
      errors.push(c.label);
      el.style.borderColor='#f87171';el.style.background='#fef2f2';
    } else {
      el.style.borderColor='';el.style.background='';
    }
  });

  // ★ เช็คลายเซ็น
  const mode=getActiveSigMode();
  let hasSig=false;
  if(mode==='draw')   hasSig=!!sellerSigData;
  if(mode==='upload') hasSig=!!sigUploadData;
  if(mode==='type')   hasSig=!!($('#sigTypeName')?.value||'').trim();
  if(!hasSig){
    errors.push('ลายเซ็นพนักงานขาย');
    const sigWrap=document.getElementById(
      mode==='draw'?'sigDrawWrap':mode==='upload'?'sigUploadWrap':'sigTypeWrap'
    );
    if(sigWrap){sigWrap.style.outline='2px solid #f87171';sigWrap.style.borderRadius='8px';}
  } else {
    ['sigDrawWrap','sigUploadWrap','sigTypeWrap'].forEach(id=>{
      const el=document.getElementById(id);if(el){el.style.outline='';el.style.borderRadius='';}
    });
  }

  const hasItems=[...document.querySelectorAll('#itemRows .item')].some(tr=>
    (tr.querySelector('.desc')?.value||'').trim().length>0
  );
  if(!hasItems) errors.push('รายการสินค้า (อย่างน้อย 1 รายการ)');
  if(errors.length){
    const msg=$('#validateMsg');
    msg.style.color='#dc2626';
    msg.textContent='⚠️ กรุณากรอก: '+errors.join(', ');
    const first=checks.find(c=>!(document.getElementById(c.id)?.value||'').trim());
    if(first) document.getElementById(first.id)?.focus();
    return false;
  }
  $('#validateMsg').textContent='';
  return true;
}
function render(){
  const items=[];let idx=0;
  $$('#itemRows .item').forEach(tr=>{
    idx++;
    const desc=tr.querySelector('.desc').value,qty=parseNum(tr.querySelector('.qty').value),unit=tr.querySelector('.unit').value,price=parseNum(tr.querySelector('.price').value);
    const subTa=tr.querySelector('.sub-detail'),subDetail=subTa?subTa.value.trim():'';
    const numCell=tr.querySelector('.td-num');
    if(numCell)numCell.textContent=idx;
    const amtCell=tr.querySelector('.td-amt');if(amtCell)amtCell.textContent=fmt(qty*price);
    if(!desc&&!price)return;
    items.push({desc,qty,unit,price,index:idx,subDetail});
  });
  const gross=items.reduce((s,it)=>s+it.qty*it.price,0),vat=gross*.07,grand=gross+vat,T={gross,vat,grand};
  const foot=$('#itemFoot');
  if(items.length){foot.style.display='';$('#footGross').textContent=fmt(gross);}else foot.style.display='none';
  const itemWeights=items.map(it=>{const sub=it.subDetail?it.subDetail.split('\n').filter(l=>l.trim()).length:0;return 1+sub;});
  function buildPages(){const totalW=itemWeights.reduce((s,w)=>s+w,0);if(totalW<=FIRST_LAST_PAGE_MAX)return[items.length];const pages=[];let i=0;while(i<items.length){const isFirst=pages.length===0,maxFull=isFirst?FIRST_PAGE_MAX:OTHER_PAGE_MAX,maxLast=isFirst?FIRST_LAST_PAGE_MAX:OTHER_LAST_PAGE_MAX;let w=0,cnt=0;while(i+cnt<items.length&&w+itemWeights[i+cnt]<=maxFull){w+=itemWeights[i+cnt];cnt++;}if(i+cnt>=items.length){w=0;cnt=0;while(i+cnt<items.length&&w+itemWeights[i+cnt]<=maxLast){w+=itemWeights[i+cnt];cnt++;}}if(cnt===0)cnt=1;pages.push(cnt);i+=cnt;}return pages;}
  const sizes=buildPages(),pageCount=sizes.length;
  let out='',offset=0;
  for(let pg=0;pg<pageCount;pg++){
    const count=sizes[pg],slice=items.slice(offset,offset+count),isLast=pg===pageCount-1;
    let rows='';
    slice.forEach(it=>{const subLines=it.subDetail?it.subDetail.split('\n').filter(l=>l.trim()).map(l=>`<div style="font-size:11px;color:#555;padding-left:12px;line-height:1.4;word-break:break-word;">${esc2(l)}</div>`).join(''):'';rows+=`<tr><td class="c">${it.index}</td><td class="l" style="word-break:break-word;white-space:normal;">${esc2(it.desc||'-')}${subLines}</td><td class="c">${it.qty||''}</td><td class="c">${it.unit?esc2(it.unit):''}</td><td class="r">${fmt(it.price||0)}</td><td class="r">${fmt(it.qty*it.price)}</td></tr>`;});
    const sliceW=slice.reduce((s,it,i)=>s+itemWeights[offset+i],0);
    const padTarget=pg===0?(isLast?FIRST_LAST_PAGE_MAX:FIRST_PAGE_MAX):(isLast?OTHER_LAST_PAGE_MAX:OTHER_PAGE_MAX);
    const pad=Math.max(0,padTarget-sliceW);
    if(isLast&&pad>0)for(let e=0;e<pad;e++)rows+=`<tr class="empty-row"><td class="c"></td><td class="l"></td><td class="c"></td><td class="c"></td><td class="r"></td><td class="r"></td></tr>`;
    if(!items.length){rows='';for(let e=0;e<FIRST_LAST_PAGE_MAX;e++)rows+=`<tr class="empty-row"><td class="c">${e===0?'1':''}</td><td class="l">${e===0?'—':''}</td><td class="c"></td><td class="c"></td><td class="r"></td><td class="r"></td></tr>`;}
    out+=pageHtml(rows,pg+1,pageCount,isLast,pg===0,T);offset+=count;
  }
  $('#pages').innerHTML=out;
  $('#printBtn').disabled=false;$('#validateMsg').textContent='';
  updateMatchPriceBtn();
}
function bahtText(n){n=Math.round(n*100)/100;const baht=Math.floor(n),satang=Math.round((n-baht)*100);const txt=['','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];function conv(num){if(num===0)return'';if(num>999999)return conv(Math.floor(num/1000000))+'ล้าน'+conv(num%1000000);const str=num.toString(),len=str.length;let s='';for(let i=0;i<len;i++){const d=+str[i],p=len-1-i;if(!d)continue;if(p===0)s+=(d===1&&len>1)?'เอ็ด':txt[d];else if(p===1)s+=d===1?'สิบ':d===2?'ยี่สิบ':txt[d]+'สิบ';else s+=txt[d]+['','สิบ','ร้อย','พัน','หมื่น','แสน'][p];}return s;}if(baht===0&&satang===0)return'ศูนย์บาทถ้วน';let r='';if(baht>0)r+=conv(baht)+'บาท';r+=satang>0?conv(satang)+'สตางค์':'ถ้วน';return r;}

['docDate','contactName','custCode','custCompany','custAddr','custTel','custTax','custBranch','validDays','expireDate','creditDays','note']
  .forEach(id=>{$('#'+id).oninput=$('#'+id).onchange=render;});
function calcExpireDate(){const days=parseInt($('#validDays').value)||0,baseDate=$('#docDate').value;if(days>0&&baseDate){const d=new Date(baseDate);d.setDate(d.getDate()+days);const raw=d.toISOString().slice(0,10);$('#expireDate').dataset.raw=raw;$('#expireDate').value=fmtThaiDateStr(raw);render();}}
$('#validDays').addEventListener('input',calcExpireDate);$('#validDays').addEventListener('change',calcExpireDate);$('#docDate').addEventListener('change',calcExpireDate);

// ══════════════════════════════════════════
// SAVE / PRINT
// ══════════════════════════════════════════
$('#printBtn').onclick=async()=>{
  const btn=$('#printBtn'),origHTML=btn.innerHTML;
  if(!validateBeforeSave())return;

  // ★ เช็ค jsPDF ก่อนเริ่ม
  const { jsPDF }=window.jspdf||{};
  if(!jsPDF){alert('jsPDF โหลดไม่สำเร็จ — ตรวจสอบว่าใส่ <script> jspdf + html2canvas แล้ว');return;}

  setAllBtnsLoading(true);
  setBtnLoading(btn,true,'กำลังบันทึกลงระบบ...');

  try{
    // ── 1) บันทึก DB ──
    const items=[];
    $$('#itemRows .item').forEach(tr=>{
      const desc=(tr.querySelector('.desc')?.value||'').trim(),price=parseNum(tr.querySelector('.price')?.value);
      if(!desc&&price<=0)return;
      const subTa=tr.querySelector('.sub-detail');
      items.push({desc,price:parseNum(tr.querySelector('.price')?.value),qty:parseNum(tr.querySelector('.qty')?.value),unit:(tr.querySelector('.unit')?.value||'').trim(),sub_detail:subTa?subTa.value.trim():null});
    });

    const res=await fetch('/soitem',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify({doc_date:rawVal('docDate')||new Date().toISOString().slice(0,10),customer_code:rawVal('custCode')||'-',customer_company:rawVal('custCompany')||'-',customer_address:rawVal('custAddr')||'-',customer_tel:rawVal('custTel')||'-',customer_tax:rawVal('custTax')||'',customer_branch:rawVal('custBranch')||'',contact_name:rawVal('contactName')||'-',valid_days:parseInt($('#validDays').value)||0,expire_date:$('#expireDate').dataset.raw||null,credit_days:parseInt($('#creditDays').value)||null,note:rawVal('note')||null,items:items.length?items:[{desc:'-',qty:0,unit:'',price:0}],pdf_base64:null})
    });
    const result=await res.json();
    if(!res.ok||result.status==='error')throw new Error(result.message||'HTTP '+res.status);

    // ── 2) render พร้อมเลขที่ใบเสนอราคา ──
    currentQuotationNo=result.quotation_no;
    render();
    await new Promise(r=>setTimeout(r,300));

    // ── 3) สร้าง PDF จากหน้าตัวอย่าง ──
    setBtnLoading(btn,true,'กำลังสร้าง PDF...');
    const pages=document.querySelectorAll('#pages .doc.page');
    if(!pages.length)throw new Error('ไม่มีข้อมูลใบเสนอราคา');
    const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
    const PDF_WIDTH='760px',PDF_MIN_H='1080px';
    for(let i=0;i<pages.length;i++){
      if(i>0)pdf.addPage();
      const pg=pages[i],_w=pg.style.maxWidth,_h=pg.style.minHeight;
      pg.style.maxWidth=PDF_WIDTH;pg.style.minHeight=PDF_MIN_H;pg.offsetHeight;
      const canvas=await html2canvas(pg,{scale:3,useCORS:true,backgroundColor:'#ffffff',logging:false});
      pg.style.maxWidth=_w;pg.style.minHeight=_h;
      const imgData=canvas.toDataURL('image/jpeg',0.95);
      const ratio=Math.min(210/canvas.width,297/canvas.height);
      pdf.addImage(imgData,'JPEG',(210-canvas.width*ratio)/2,0,canvas.width*ratio,canvas.height*ratio);
    }

    // ── 4) ส่ง PDF กลับ server (ถ้ามี route /soitem/{no}/pdf) ──
    const pdfBase64=await blobToBase64(pdf.output('blob'));
    await fetch('/soitem/'+encodeURIComponent(result.quotation_no)+'/pdf',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify({pdf_base64:pdfBase64}),
    }).catch(()=>{});

    // ── 5) ดาวน์โหลด PDF ──
    const custName=(rawVal('custCompany')||'QT').replace(/[^ก-๙A-Za-z0-9]/g,'_').substring(0,30);
    pdf.save(`${result.quotation_no}.pdf`);

    // ── 6) แสดงผลสำเร็จ ──
    btn.innerHTML=`✅ บันทึกแล้ว (${result.quotation_no})`;
    const msg=$('#validateMsg');
    msg.style.color='#16a34a';
    msg.textContent=`✅ ${result.message}`;
    setAllBtnsLoading(false);

    // ── 7) reset ฟอร์ม ──
    setTimeout(()=>{
      ['custCode','custCompany','custAddr','custTel','custTax','custBranch',
       'contactName','validDays','expireDate','creditDays','note','sellerTel','sellerEmail']
        .forEach(id=>{const el=$('#'+id);if(el)el.value='';});
      $('#expireDate').dataset.raw='';
      $('#itemRows').innerHTML='';
      $('#itemRows').appendChild(itemRow({desc:'สินค้า/บริการ ตัวอย่าง',qty:1,unit:'ชิ้น',price:1000}));
      sigCtx.clearRect(0,0,sigCanvas.width,sigCanvas.height);
      sellerSigData=null;sigUploadData=null;
      currentCustomerCode='';currentQuotationNo='';
      refCustomers=[];renderRefChips();
      aiFiles=[];renderAiChips();
      $('#aiPasteText').value='';
      $('#aiStatus').style.display='none';
      $('#aiResultWrap').style.display='none';
      $('#aiOcrTextWrap').style.display='none';
      $('#aiOcrText').value='';
      render();
      $$('.tab').forEach(t=>t.classList.remove('active'));
      $$('.pane').forEach(p=>p.classList.remove('active'));
      document.querySelector('.tab[data-tab="ai-ocr"]').classList.add('active');
      $('#pane-ai-ocr').classList.add('active');
      const preview=$('.card-preview');if(preview)preview.style.display='none';
      $('.wrap').classList.add('ocr-mode');
      btn.innerHTML='บันทึกใบเสนอราคา';
      btn.disabled=false;
      msg.textContent='';
    },1500);

  }catch(err){
    alert('บันทึกไม่สำเร็จ: '+err.message);
    setAllBtnsLoading(false);
    btn.innerHTML=origHTML;btn.disabled=false;
  }
};
function blobToBase64(blob){return new Promise((resolve,reject)=>{const r=new FileReader();r.onload=()=>resolve(r.result.split(',')[1]);r.onerror=reject;r.readAsDataURL(blob);});}
// ── Init ──
$('#docDate').value=new Date().toISOString().slice(0,10);
$('#itemRows').appendChild(itemRow({desc:'สินค้า/บริการ ตัวอย่าง',qty:1,unit:'ชิ้น',price:1000}));
render();
updateMatchPriceBtn();

// ── close popup on outside click ──
document.addEventListener('click',e=>{
  const pop=document.getElementById('pricePop');
  if(pop&&pop.style.display==='block'&&!pop.contains(e.target)
    &&!e.target.closest('.ic-price')&&!e.target.closest('.ic-ai'))
    pop.style.display='none';
  const dpop=document.getElementById('docPop');
  if(dpop&&dpop.style.display==='block'&&!dpop.contains(e.target)&&!e.target.closest('.ic-doc'))
    dpop.style.display='none';
});
</script>

<div class="price-pop" id="pricePop">
  <div class="pp-head">
    <span class="pp-title" id="ppTitle"></span>
    <button class="pp-close" onclick="document.getElementById('pricePop').style.display='none'">✕</button>
  </div>
  <div class="pp-tokens" id="ppTokens" style="display:none"></div>
  <div class="pp-body" id="ppBody"></div>
</div>

<div class="doc-pop" id="docPop">
  <div class="dp-head">
    <span class="dp-title" id="dpTitle"></span>
    <button class="dp-close" onclick="document.getElementById('docPop').style.display='none'">✕</button>
  </div>
  <div class="dp-body" id="dpBody"></div>
</div>

</body>
</html>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบสร้างใบเสนอราคา (Quotation Generator)</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<style>
 /* ===== PO Detail Modal (nested) ===== */
.po-detail-bg{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.5);justify-content:center;padding:20px 0;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none;}
.po-detail-bg::-webkit-scrollbar{display:none;}
.po-detail-bg.show{display:flex;align-items:flex-start;}
.po-detail-box{background:#fff;border-radius:12px;width:min(95vw,1300px);overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.3);animation:popIn .15s ease-out;margin:20px auto;flex-shrink:0;}
.po-detail-head{display:flex;align-items:center;padding:12px 16px;background:#f8fafc;border-bottom:1.5px solid var(--line);}
.po-detail-head .pdt{font-weight:700;font-size:14px;color:var(--navy);flex:1;}
.po-detail-head .pdc{border:none;background:#eee;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:14px;color:#888;}
.po-detail-head .pdc:hover{background:#ddd;color:#333;}
.po-detail-body{padding:0;overflow-x:auto;}

/* ===== PO Cost Modal ===== */
.po-modal-bg{display:none;position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.4);justify-content:center;padding:20px 0;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none;}
.po-modal-bg::-webkit-scrollbar{display:none;}
.po-modal-bg.show{display:flex;align-items:flex-start;}
.po-modal-box{background:#fff;border-radius:12px;width:min(95vw,1200px);overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.25);animation:popIn .15s ease-out;margin:20px auto;flex-shrink:0;}
.po-modal-head{display:flex;align-items:center;padding:12px 16px;background:linear-gradient(135deg,#f0f4ff,#f8faff);border-bottom:1.5px solid var(--line);}
.po-modal-head .pmt{font-weight:700;font-size:14px;color:var(--navy);flex:1;}
.po-modal-head .pmc{border:none;background:#eee;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:14px;color:#888;}
.po-modal-head .pmc:hover{background:#ddd;color:#333;}
.po-modal-body{padding:0;overflow-x:auto;}
.po-modal-body table{width:max-content;min-width:100%;border-collapse:collapse;font-size:12px;}
.po-modal-body th{padding:7px 10px;text-align:left;font-size:10px;font-weight:700;color:#666;text-transform:uppercase;border-bottom:1.5px solid var(--line);background:#fafbfc;white-space:nowrap;position:sticky;top:0;}
.po-modal-body th.r{text-align:right;}
.po-modal-body td{padding:7px 10px;border-bottom:1px solid #f2f3f6;vertical-align:top;white-space:nowrap;}
.po-modal-body tr:hover td{background:#f8f9fb;}
.po-modal-body .best{color:#2e7d32;font-weight:700;}
.po-modal-body .worst{color:#c62828;font-weight:700;}
.po-modal-loading{text-align:center;padding:40px;color:var(--muted);font-size:13px;}
.po-modal-sum{padding:8px 14px;font-size:12px;color:#555;background:#f0fdf4;border-bottom:1px solid var(--line);font-weight:600;}
.po-modal-body, .po-detail-body{
    padding:0;
    overflow:auto;
    max-height:70vh;
    cursor:grab;
}
.po-modal-body:active, .po-detail-body:active{
    cursor:grabbing;
}
.po-modal-body::-webkit-scrollbar,
.po-detail-body::-webkit-scrollbar{
    width:6px;
    height:6px;
}
.po-modal-body::-webkit-scrollbar-thumb,
.po-detail-body::-webkit-scrollbar-thumb{
    background:#ccc;
    border-radius:3px;
}
.po-modal-body::-webkit-scrollbar-thumb:hover,
.po-detail-body::-webkit-scrollbar-thumb:hover{
    background:#aaa;
}
.cart-btn{
  width:26px;height:26px;background:none;border:1px solid transparent;border-radius:6px;
  cursor:pointer;color:#aaa;font-size:14px;transition:.15s;display:inline-flex;align-items:center;justify-content:center;
}
.cart-btn:hover{background:#eff6ff;border-color:#bfdbfe;color:var(--blue);}
.ref-tag{
  font-size:11px;color:#92400e;background:#fffbeb;
  border:1px solid #fcd34d;border-radius:6px;
  padding:3px 8px;margin-top:3px;display:inline-block;
  line-height:1.6;cursor:pointer;position:relative;
}
.ref-tag:hover{background:#fef3c7;}
.ref-panel{
  display:none;position:fixed;z-index:300;
  background:#fff;border:1px solid var(--line);border-radius:12px;
  box-shadow:0 12px 40px rgba(0,0,0,.2),0 0 0 1px rgba(0,0,0,.04);
  width:min(820px,94vw);
  animation:popIn .15s ease-out;
}
.ref-panel-backdrop{
  display:none;position:fixed;inset:0;z-index:299;background:transparent;
}
.ref-panel .rp-head{
  padding:10px 14px;font-size:12px;font-weight:700;color:var(--navy);
  border-bottom:1px solid var(--line);background:#fffbeb;
  border-radius:10px 10px 0 0;
}
.ref-panel .rp-scroll{overflow-x:auto;max-height:50vh;overflow-y:auto;}
.ref-panel .rp-scroll::-webkit-scrollbar{width:5px;height:5px;}
.ref-panel .rp-scroll::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px;}
.ref-panel table{width:max-content;min-width:100%;border-collapse:collapse;font-size:12px;}
.ref-panel th{
  padding:8px 10px;font-weight:700;color:#374151;
  border-bottom:1.5px solid var(--line);background:#f8fafc;text-align:left;
  white-space:nowrap;position:sticky;top:0;z-index:1;
}
.ref-panel td{padding:8px 10px;border-bottom:1px solid #f0f2f5;vertical-align:top;}
.ref-panel tr:last-child td{border-bottom:none;}
.ref-panel tr.rp-row:hover td{background:#fffbeb;cursor:pointer;}
.ref-panel .rp-cust{max-width:160px;white-space:normal;word-break:break-word;line-height:1.4;font-weight:500;}
.ref-panel .rp-price{text-align:right;font-weight:700;color:var(--navy);font-variant-numeric:tabular-nums;white-space:nowrap;font-size:13px;}
.ref-panel .rp-pname{color:#555;font-size:11px;white-space:normal;word-break:break-word;max-width:180px;line-height:1.4;}
.ref-panel .rp-kw{font-size:8px;color:var(--blue);font-weight:600;white-space:normal;line-height:1.5;}
.ref-panel .rp-use{
  font-size:11px;color:#fff;background:var(--blue);
  border:none;border-radius:5px;padding:4px 12px;
  cursor:pointer;white-space:nowrap;font-weight:600;
}
.ref-panel .rp-use:hover{background:var(--navy2);}
  :root{
    --navy:#1f3a93; --navy2:#16306f; --blue:#1e50c8; --bg:#eef1f6; --line:#dfe4ec;
    --green:#16a34a; --muted:#6b7280; --soft:#f7f9fc; --docline:#6c6cf0;
  }
  *{box-sizing:border-box;}
  body{margin:0;font-family:'Sarabun',sans-serif;background:var(--bg);color:#1f2937;min-width:0;}
  .topbar{background:linear-gradient(90deg,var(--navy2),var(--blue));color:#fff;padding:14px 22px;font-weight:600;font-size:18px;display:flex;align-items:center;gap:10px;}
  .topbar .dot{width:26px;height:26px;border-radius:6px;background:#fff3;display:flex;align-items:center;justify-content:center;}

  /* ===== RESPONSIVE WRAP LAYOUT ===== */
  .wrap{
    display:grid;
    grid-template-columns:1fr;
    gap:18px;
    padding:18px;
    margin:0 auto;
  }
  /* Wide screen: side-by-side (≥24" monitor / 1920px) */
  @media (min-width:1920px) {
    .wrap{
      grid-template-columns:minmax(600px,850px) 1fr;
      max-width:1800px;
      align-items:start;
    }
    .wrap > .card-preview{
      position:sticky;
      top:18px;
      max-height:calc(100vh - 36px);
      overflow-y:auto;
      scrollbar-width:thin;
    }
    .wrap > .card-preview::-webkit-scrollbar{width:6px;}
    .wrap > .card-preview::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px;}
    .wrap > .card-preview::-webkit-scrollbar-thumb:hover{background:#aaa;}
  }
  /* Ultra wide */
  @media (min-width:2400px) {
    .wrap{
      grid-template-columns:800px 1fr;
    }
  }
  /* Medium: single column, capped width */
  @media (max-width:1919px) {
    .wrap{
      max-width:900px;
    }
  }
  /* ===== OCR MODE: center & enlarge when preview hidden ===== */
  .wrap.ocr-mode{
    max-width:1000px;
    margin:0 auto;
    justify-content:center;
  }
  .wrap.ocr-mode > .card:first-child{
    width:100%;
  }
  @media (min-width:1920px){
    .wrap.ocr-mode{
      grid-template-columns:1fr;
      max-width:1000px;
    }
  }

  .card{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:visible;box-shadow:0 1px 3px #0000000a;}
  .tabs{display:flex;border-bottom:1px solid var(--line);}
  .tab{flex:1;padding:14px;text-align:center;cursor:pointer;font-weight:600;color:var(--muted);border-bottom:3px solid transparent;font-size:14px;}
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
  @media (max-width:500px){
    .row{grid-template-columns:1fr;}
  }
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:none;border-radius:8px;padding:11px 16px;font-family:inherit;font-weight:600;font-size:14px;cursor:pointer;}
  .btn-primary{background:var(--blue);color:#fff;width:100%;}
  .btn-primary:hover{background:var(--navy2);}
  .btn-green{background:var(--green);color:#fff;}
  .btn-green:disabled{background:#9ca3af;cursor:not-allowed;opacity:0.7;}
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
  .items-table-wrap{margin:8px 0;border:1px solid var(--line);border-radius:10px;background:#fff;overflow-x:auto;}
  .items-table{width:100%;border-collapse:collapse;font-size:13px;min-width:580px;}
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

  .new-tag{font-size:10px;color:#b45309;background:#fef3c7;border:1px solid #fde68a;border-radius:4px;padding:1px 5px;margin-top:2px;display:inline-block;line-height:1.4;}

  /* === hist popover === */
  .hist-popover{
    position:absolute;z-index:500;background:#fff;border:1px solid var(--line);border-radius:12px;
    box-shadow:0 12px 40px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04);
    width:min(680px,90vw);overflow:visible;display:none;
    animation:popIn .15s ease-out;
  }
  @keyframes popIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
  .hist-popover .hp-head{font-weight:700;font-size:12px;color:var(--navy);display:flex;gap:6px;flex-wrap:wrap;align-items:center;flex:1;}
  .hist-popover .hp-head .mm-tag{background:#dbeafe;color:var(--navy);padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;}
  .hist-popover .hp-close{border:none;background:#f3f4f6;border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:13px;color:#6b7280;flex-shrink:0;}
  .hist-popover .hp-close:hover{background:#e5e7eb;color:#111;}
  .hist-popover .hp-body{}
  .hist-popover table{width:100%;border-collapse:collapse;font-size:11.5px;}
  .hist-popover th{padding:7px 6px;font-weight:700;color:#374151;border-bottom:2px solid var(--line);text-align:left;white-space:nowrap;background:#f8fafc;position:sticky;top:0;z-index:1;}
  .hist-popover td{padding:6px 6px;border-bottom:1px solid #f0f2f5;}
  .hist-popover tr:hover td{background:#f8faff;}
  .hist-popover tr.hist-row:hover td{background:#eff6ff;}

  .items-table .td-qty{width:76px;}
  .items-table .td-unit{width:76px;}
  .items-table .td-price{width:108px;}
  .items-table .td-amt{width:108px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:var(--navy);white-space:nowrap;padding-right:10px;font-size:13px;vertical-align:middle;}
.items-table .td-act{width:80px;text-align:center;vertical-align:middle;white-space:nowrap;}
.items-table .td-act .act-wrap{display:inline-flex;gap:2px;align-items:center;}

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

/* Sub-item styles */
.items-table .sub-item{background:#ffffff;}
.items-table .sub-item td{border-bottom:1px solid #e5e7eb;}
.items-table .sub-item .td-num{color:#9ca3af;font-size:11px;}
.items-table .sub-item .td-desc{padding-left:24px;}
.items-table .sub-item input{font-size:12px;color:#374151;}
.items-table .sub-item .td-amt{color:#9ca3af;font-size:12px;}

/* ในหน้าพิมพ์/PDF */
.itbl tr.sub-row td{font-size:11px;color:#555555;padding:6px 5px 6px 24px;background:#ffffff !important;border:none !important;}

  /* ===== batch-match status bar ===== */
  .match-bar{display:none;align-items:center;gap:8px;padding:8px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;margin-top:8px;font-size:12px;color:var(--navy);}
  .match-bar .spin{animation:spin .8s linear infinite;display:inline-block;}
  @keyframes spin{to{transform:rotate(360deg)}}

  /* === Document preview === */
  .doc-tools{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--line);}
  .doc-scroll{padding:18px;background:#f1f4f9;}
  .doc{background:#fff;width:100%;max-width:760px;margin:0 auto;padding:22px 30px;box-shadow:0 2px 14px #0003;font-size:13px;color:#1a1a1a;}
  .doc.page{display:flex;flex-direction:column;min-height:1080px;}
  .doc.page + .doc.page{margin-top:22px;}
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
  .sg{flex:1;text-align:center;font-size:12px;position:relative;white-space:nowrap;}
  .sg .sgline{border-top:1px solid #555;margin:40px 0 3px;padding-top:16px;}
  .sg .sig-img{position:absolute;left:50%;bottom:28px;transform:translateX(-50%);max-height:55px;max-width:80%;height:auto;pointer-events:none;}

  .page-footer-keep{page-break-inside:avoid;break-inside:avoid;}

  /* ===== Mobile adjustments for document preview ===== */
  @media (max-width:700px){
    .doc{padding:14px 16px;}
    .qhead .qbig{font-size:24px;}
    .seller-top .co{font-size:15px;}
    .bluebar{font-size:18px;}
    .cinfo-table{font-size:11px;}
    .ssign{gap:40px;}
    .sg{width:auto;}
    .ctot{width:100%;}
    .tab{padding:10px 8px;font-size:13px;}
  }

  /* ===== Preview panel header for wide layout ===== */
  .preview-header{
    display:none;
    padding:14px 18px;
    font-weight:700;
    font-size:14px;
    color:var(--navy);
    border-bottom:1px solid var(--line);
    background:linear-gradient(180deg,#f8fafc,#fff);
    gap:8px;
    align-items:center;
  }
  @media (min-width:1920px){
    .preview-header{display:flex;}
  }

  /* ===== Page number bottom-right ===== */
  .page-number{
    text-align:right;
    font-size:11px;
    color:#888;
    padding-top:4px;
    margin-top:2px;
  }

  @media print{
    @page{size:A4;margin:0;}
    .topbar,.doc-tools,.hist-popover,.preview-header{display:none!important;}
    .wrap{display:block!important;padding:0;margin:0;max-width:none;gap:0;}
    .wrap>.card:first-child{display:none!important;}
    .card{border:none!important;box-shadow:none!important;border-radius:0;}
    .doc-scroll{padding:0!important;background:#fff!important;}
    .doc.page{box-shadow:none!important;max-width:none!important;width:100%!important;margin:0!important;padding:6mm 10mm!important;display:flex!important;flex-direction:column!important;min-height:0!important;height:287mm!important;page-break-after:always;}
    .doc.page:last-child{page-break-after:auto;}
    .flexspace{flex:1 1 auto!important;min-height:0!important;display:block!important;}
    .page-footer-keep{page-break-inside:avoid!important;break-inside:avoid!important;}
    .ssign{flex-direction:row!important;gap:20px!important;justify-content:space-between!important;width:100%!important;}
    .cfoot{flex-direction:row!important;}
  }
</style>
</head>
<body>
<div class="topbar"><span class="dot">📄</span> ระบบสร้างใบเสนอราคา · Quotation Generator</div>

<div class="wrap ocr-mode">
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
          <div style="font-weight:600;margin-top:6px">คลิก ลากไฟล์มาวาง หรือ Ctrl+V วางรูป</div>
          <div class="muted">รองรับ JPG, PNG, PDF, Excel (.xlsx, .xls, .csv)</div>
        </div>
        <img id="preview" style="display:none">
        <div id="fileInfo" style="display:none;font-size:13px;color:var(--navy);font-weight:600;"></div>
      </div>
      <input type="file" id="file" accept="image/*,.pdf,.xlsx,.xls,.csv" hidden>
      <div class="progress" id="prog"><span></span></div>
      <div class="btn-row">
        <button class="btn btn-primary" id="ocrBtn" style="flex:1">🔍 อ่านข้อความ</button>
        <button class="btn btn-ghost" id="clearBtn" style="flex:0 0 auto;padding:11px 20px;">🗑️ ล้างค่า</button>
      </div>
      <div id="ocrStats" style="display:none"></div>
      <label id="ocrTextLabel">ข้อความที่อ่านได้ / วางข้อความเอง</label>
      <textarea id="ocrText" rows="2" placeholder="ผลลัพธ์จะแสดงที่นี่ — แก้ไขได้" style="min-height:60px;overflow:hidden;"></textarea>
      <div id="excelTableWrap" style="display:none;"></div>
      <div class="btn-row">
        <button class="btn btn-green" id="parseBtn" style="flex:1">✨ แยกรายการอัตโนมัติ → กรอกฟอร์ม</button>
      </div>
      <div class="hint">
        <b>เคล็ดลับ:</b> อ่านไฟล์ → แยกรายการสินค้า → <b>เลือกบริษัทลูกค้า</b> ระบบจะดึงราคาขายครั้งล่าสุดให้อัตโนมัติ
        · ถ้าสินค้าไม่เคยขายลูกค้ารายนั้นจะแสดง <span class="new-tag">🆕 สินค้าใหม่</span>
      </div>
    </div>

    {{-- ========== TAB: Manual ========== --}}
    <div class="pane" id="pane-manual">

      {{-- ===== Compact Header ===== --}}
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

      <div class="sec-title">รายการสินค้า / บริการ</div>

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

      <div class="sec-title">ลายเซ็นพนักงานขาย</div>
      <canvas id="sigPad" width="760" height="140" style="border:1px solid var(--line);border-radius:8px;cursor:crosshair;background:#fff;display:block;width:100%;max-width:100%;box-sizing:border-box;touch-action:none;"></canvas>
      <div class="btn-row">
        <button class="btn btn-ghost" id="sigClear" style="flex:1;font-size:12px">🗑️ ล้างลายเซ็น</button>
      </div>
    </div>
  </div>

  {{-- ========== Document Preview ========== --}}
  <!-- ★ CHANGED: ซ่อน preview ตั้งแต่โหลดหน้า (แท็บ OCR เป็น default) -->
  <div class="card card-preview" style="display:none">
    <div class="preview-header">📄 ตัวอย่างใบเสนอราคา</div>
    <div class="doc-scroll">
      <div id="pages"></div>
    </div>
    <div class="doc-tools" style="border-top:1px solid var(--line);border-bottom:none;justify-content:center;padding:14px 18px;flex-direction:column;align-items:center;gap:6px;">
      <button class="btn btn-green" id="printBtn" style="min-width:280px;max-width:400px;width:100%;padding:13px 40px;font-size:16px;">บันทึกใบเสนอราคา</button>
      <div id="validateMsg" class="muted" style="font-size:12px;color:#dc2626;text-align:center;"></div>
    </div>
  </div>

  <div class="po-modal-bg" id="poModal" onclick="if(event.target===this)closePoModal()">
    <div class="po-detail-bg" id="poDetailModal" onclick="if(event.target===this)closePoDetail()">
  <div class="po-detail-box">
    <div class="po-detail-head">
      <span class="pdt" id="poDetailTitle">📋 รายละเอียด PO</span>
      <button class="pdc" onclick="closePoDetail()">✕</button>
    </div>
    <div class="po-detail-body" id="poDetailBody">
      <div class="po-modal-loading">⏳</div>
    </div>
  </div>
</div>
  <div class="po-modal-box">
    <div class="po-modal-head">
      <button class="pmc" onclick="closePoModal()">✕</button>
    </div>
    <div class="po-modal-body" id="poModalBody">
      <div class="po-modal-loading">กำลังโหลด...</div>
    </div>
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

<script>
const $  = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

const SELLER = {
  name:      'บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด',
  tel:       '02-4727341-48',
  addrShort: 'เลขที่ 39/7 ถนนวุฒากาส แขวงตลาดพลู เขตธนบุรี กรุงเทพฯ 10600',
  tax:       '0105547065721',
};

/* ===== Pagination: items per page =====
   หน้าแรกมี header + ข้อมูลลูกค้า → จุได้น้อยกว่า
   หน้าสุดท้ายมี footer (ยอดรวม + ลายเซ็น) → ต้องเผื่อที่
   ค่าต่าง ๆ คำนวณจาก min-height 1080px ของ .doc.page */
const FIRST_PAGE_MAX      = 20;  // หน้าแรก (ไม่ใช่หน้าสุดท้าย)
const FIRST_LAST_PAGE_MAX = 15;  // หน้าแรก + หน้าสุดท้าย (มี footer)
const OTHER_PAGE_MAX      = 23;  // หน้ากลาง
const OTHER_LAST_PAGE_MAX = 18;  // หน้าสุดท้าย (ไม่ใช่หน้าแรก, มี footer)

function paginate(total) {
  // กรณีหน้าเดียว — ต้องพอใส่ทั้ง items + footer
  if (total <= FIRST_LAST_PAGE_MAX) return [total];

  // หลายหน้า — เผื่อที่ให้หน้าสุดท้ายเสมอ (min 1 item)
  const first = Math.min(total - 1, FIRST_PAGE_MAX);
  const sizes = [first];
  let rem = total - first;

  // หน้ากลาง — ใส่ได้เต็ม แต่ต้องเหลือให้หน้าสุดท้าย
  while (rem > OTHER_LAST_PAGE_MAX) {
    const chunk = Math.min(rem - 1, OTHER_PAGE_MAX);
    sizes.push(chunk);
    rem -= chunk;
  }
  // หน้าสุดท้าย — items ≤ OTHER_LAST_PAGE_MAX เพื่อเผื่อ footer
  if (rem > 0) sizes.push(rem);

  return sizes;
}

let currentCustomerCode = '';
let sellerSigData       = null;
let currentQuotationNo  = '';
let lastSeparators = [];
let lastOcrLines   = [];

function esc(s)  { return (s + '').replace(/"/g, '&quot;'); }
function esc2(s) { return (s + '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmt(n)  { return (Math.round(n * 100) / 100).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); }

function autoResize(el) { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; }

/* ★ CHANGED: สลับแสดง/ซ่อน preview ตาม tab + จัดกลางตอน OCR */
$$('.tab').forEach(t => t.onclick = () => {
  $$('.tab').forEach(x => x.classList.remove('active'));
  $$('.pane').forEach(x => x.classList.remove('active'));
  t.classList.add('active');
  $('#pane-' + t.dataset.tab).classList.add('active');
  const isOcr = t.dataset.tab === 'upload';
  // ซ่อน preview ตอนอยู่แท็บ OCR, แสดงตอนอยู่แท็บ manual
  const preview = $('.card-preview');
  if (preview) {
    preview.style.display = isOcr ? 'none' : '';
  }
  // จัดกลาง + ขยายตอนอยู่แท็บ OCR
  const wrap = $('.wrap');
  if (wrap) {
    wrap.classList.toggle('ocr-mode', isOcr);
  }
});

let imgData = null;
let uploadedFile = null;
let fileType = null; // 'image' | 'pdf' | 'excel'

$('#drop').onclick    = () => $('#file').click();
$('#drop').ondragover = e => e.preventDefault();
$('#drop').ondrop     = e => { e.preventDefault(); handleFile(e.dataTransfer.files[0]); };
$('#file').onchange   = e => handleFile(e.target.files[0]);

// วางรูปจาก clipboard (Ctrl+V / คลิกขวา→วาง)
document.addEventListener('paste', e => {
  const items = e.clipboardData?.items;
  if (!items) return;
  for (const item of items) {
    if (item.type.startsWith('image/')) {
      e.preventDefault();
      const blob = item.getAsFile();
      if (blob) handleFile(blob);
      return;
    }
  }
});

function handleFile(f) {
  if (!f) return;
  uploadedFile = f;
  const ext = f.name.split('.').pop().toLowerCase();
  const isImage = f.type.startsWith('image/');
  const isPdf = ext === 'pdf' || f.type === 'application/pdf';
  const isExcel = ['xlsx','xls','csv'].includes(ext);

  $('#dropEmpty').style.display = 'none';
  $('#preview').style.display = 'none';
  $('#fileInfo').style.display = 'none';

  if (isImage) {
    fileType = 'image';
    const r = new FileReader();
    r.onload = () => { imgData = r.result; $('#preview').src = imgData; $('#preview').style.display = 'block'; };
    r.readAsDataURL(f);
  } else {
    fileType = isPdf ? 'pdf' : 'excel';
    imgData = null;
    const icon = isPdf ? '📄' : '📊';
    $('#fileInfo').innerHTML = `<div style="font-size:28px;margin-bottom:6px;">${icon}</div>${esc2(f.name)}<br><span class="muted">${(f.size/1024).toFixed(0)} KB</span>`;
    $('#fileInfo').style.display = 'block';
  }
}

// ปุ่มล้างค่า
$('#clearBtn').onclick = () => {
  imgData = null; uploadedFile = null; fileType = null;
  window._excelRows = null; window._excelColMap = {};
  $('#preview').style.display = 'none';
  $('#fileInfo').style.display = 'none';
  $('#dropEmpty').style.display = '';
  $('#ocrText').value = '';
  $('#ocrText').style.display = '';
  $('#ocrText').style.minHeight = '60px';
  $('#ocrTextLabel').style.display = '';
  $('#excelTableWrap').style.display = 'none';
  $('#excelTableWrap').innerHTML = '';
  $('#ocrStats').style.display = 'none';
  $('#file').value = '';
  autoResize($('#ocrText'));
};

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

      for (let i = 0; i < d.length; i += 4) {
        const g = d[i] * 0.299 + d[i+1] * 0.587 + d[i+2] * 0.114;
        d[i] = d[i+1] = d[i+2] = g;
      }

      const hist = new Array(256).fill(0);
      for (let i = 0; i < d.length; i += 4) hist[d[i]]++;
      const total = w * h;
      let sum = 0;
      for (let i = 0; i < 256; i++) sum += i * hist[i];
      let sumB = 0, wB = 0, maxVar = 0, threshold = 128;
      for (let t = 0; t < 256; t++) {
        wB += hist[t]; if (!wB) continue;
        const wF = total - wB; if (!wF) break;
        sumB += t * hist[t];
        const mB = sumB / wB, mF = (sum - sumB) / wF;
        const v  = wB * wF * (mB - mF) * (mB - mF);
        if (v > maxVar) { maxVar = v; threshold = t; }
      }

      for (let i = 0; i < d.length; i += 4) {
        const v = d[i] > threshold ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
      }

      const minLineW = Math.round(w * 0.25);
      const lineThick = Math.max(3, Math.round(h * 0.008));

      for (let y = 0; y < h; y++) {
        let runStart = -1;
        for (let x = 0; x <= w; x++) {
          const idx = (y * w + x) * 4;
          const isBlack = (x < w) && (d[idx] === 0);
          if (isBlack && runStart < 0) { runStart = x; }
          if (!isBlack && runStart >= 0) {
            if ((x - runStart) >= minLineW) {
              for (let dy = -lineThick; dy <= lineThick; dy++) {
                const yy = y + dy;
                if (yy < 0 || yy >= h) continue;
                for (let xx = runStart; xx < x; xx++) {
                  const ii = (yy * w + xx) * 4;
                  d[ii] = d[ii+1] = d[ii+2] = 255;
                }
              }
            }
            runStart = -1;
          }
        }
      }

      const minLineH = Math.round(h * 0.25);
      for (let x = 0; x < w; x++) {
        let runStart = -1;
        for (let y = 0; y <= h; y++) {
          const idx = (y * w + x) * 4;
          const isBlack = (y < h) && (d[idx] === 0);
          if (isBlack && runStart < 0) { runStart = y; }
          if (!isBlack && runStart >= 0) {
            if ((y - runStart) >= minLineH) {
              for (let dx = -lineThick; dx <= lineThick; dx++) {
                const xx = x + dx;
                if (xx < 0 || xx >= w) continue;
                for (let yy = runStart; yy < y; yy++) {
                  const ii = (yy * w + xx) * 4;
                  d[ii] = d[ii+1] = d[ii+2] = 255;
                }
              }
            }
            runStart = -1;
          }
        }
      }

      const copy = new Uint8ClampedArray(d);
      for (let y = 1; y < h - 1; y++) {
        for (let x = 1; x < w - 1; x++) {
          const idx = (y * w + x) * 4;
          if (copy[idx] === 0) continue;
          const up    = copy[((y-1) * w + x) * 4];
          const down  = copy[((y+1) * w + x) * 4];
          const left  = copy[(y * w + (x-1)) * 4];
          const right = copy[(y * w + (x+1)) * 4];
          if (up === 0 || down === 0 || left === 0 || right === 0) {
            d[idx] = d[idx+1] = d[idx+2] = 0;
          }
        }
      }

      ctx.putImageData(id, 0, 0);
      resolve(c.toDataURL('image/png'));
    };
    img.src = dataUrl;
  });
}

function postProcessOCR(text) {
  let t = text;
  for (let i = 0; i < 10; i++) {
    const prev = t;
    t = t.replace(/([\u0E00-\u0E7F])[^\S\n]+([\u0E00-\u0E7F])/g, '$1$2');
    if (t === prev) break;
  }
  t = t.replace(/^[.:;|,\s]+/gm, '');
  t = t.replace(/[.|,\s]+$/gm, '');
  t = t.replace(/(\d)%(\d)/g, '$1x$2');
  t = t.replace(/\bO(\d)/g, '0$1');
  t = t.replace(/(\d)O/g, '$10');
  t = t.replace(/(\d)l(\d)/g, '$11$2');
  t = t.replace(/\bS(\d)/g, '5$1');
  t = t.split('\n').map(line => fixMixedTokens(line)).join('\n');
  const knownCodeFixes = [
    [/(?<=^|[\s])โทพ(?=$|[\s])/gm, 'THW'],
    [/(?<=^|[\s])โท(?=[A-Z0-9])/gm, 'TH'],
    [/(?<=[A-Z0-9])พ(?=$|[\s])/gm,  'W'],
    [/(?<=^|[\s])ทว(?=$|[\s])/gm,   'HW'],
  ];
  knownCodeFixes.forEach(([rx, rep]) => { t = t.replace(rx, rep); });
  t = t.split('\n').map(l => l.trim()).filter(l => l.length > 0).join('\n');
  return t;
}

const THAI_TO_LATIN_MAP = {
  'ท': 'H','ห': 'H','ฟ': 'F','โ': 'T','ต': 'T',
  'พ': 'W','ว': 'W','ช': 'U','ย': 'Y','ซ': 'Z','ค': 'K','ก': 'K',
  'ล': 'L','ม': 'M','น': 'N','ข': 'X','อ': 'O','ป': 'P','ร': 'R','ส': 'S',
  'บ': 'B','ด': 'D','ฉ': 'C','จ': 'C','ฝ': 'F',
  'ิ': '','ั': '','่': '','้': '','๊': '','็': '',
};

function fixMixedTokens(line) {
  return line.split(/(\s+)/).map(token => {
    const hasAsciiLetter = /[A-Za-z0-9]/.test(token);
    const hasThai        = /[\u0E00-\u0E7F]/.test(token);
    if (!hasThai) return token;
    if (hasAsciiLetter && token.length <= 12) return convertThaiToLatin(token);
    if (!hasAsciiLetter && token.length <= 6) {
      const consonantsOnly = token.replace(/[\u0E30-\u0E4E]/g, '');
      if (/^[\u0E01-\u0E2E]{2,5}$/.test(consonantsOnly) && consonantsOnly.length >= 2) {
        const hasRealVowelCombo = /[\u0E30\u0E32\u0E33\u0E34\u0E35\u0E36\u0E37\u0E38\u0E39\u0E40\u0E41\u0E43\u0E44]/.test(token);
        if (!hasRealVowelCombo) return convertThaiToLatin(token);
      }
    }
    return token;
  }).join('');
}

function convertThaiToLatin(token) {
  let result = '';
  for (const ch of token) { result += THAI_TO_LATIN_MAP[ch] ?? ch; }
  return result;
}

$('#ocrBtn').onclick = async () => {
  if (!uploadedFile && !imgData) { alert('กรุณาเลือกไฟล์ก่อน'); return; }
  const prog = $('#prog');
  prog.style.display = 'block';
  $('#ocrBtn').disabled = true;
  $('#ocrStats').style.display = 'none';
  try {
    if (fileType === 'excel') {
      // === Excel: อ่านด้วย SheetJS → เลือกชีท + เลือกคอลัมน์ ===
      $('#ocrBtn').textContent = '⏳ กำลังอ่าน Excel...';
      prog.firstElementChild.style.width = '50%';
      const data = await uploadedFile.arrayBuffer();
      const wb = XLSX.read(data, { type: 'array' });

      window._excelWb = wb;
      window._excelColMap = {};
      window._activeRole = null;
      window._activeSheet = 0;

      const roles = [
        { key:'desc',  label:'รายการสินค้า', color:'#dbeafe', border:'#3b82f6', icon:'📦' },
        { key:'qty',   label:'จำนวน',       color:'#dcfce7', border:'#22c55e', icon:'🔢' },
        { key:'unit',  label:'หน่วย',        color:'#fef3c7', border:'#f59e0b', icon:'📏' },
      ];

      prog.firstElementChild.style.width = '100%';
      $('#ocrTextLabel').style.display = 'none';
      $('#ocrText').style.display = 'none';
      $('#excelTableWrap').style.display = 'block';

      function loadSheet(idx) {
        const ws = wb.Sheets[wb.SheetNames[idx]];
        const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
        const maxCols = rows.length ? Math.max(...rows.map(r => r.length)) : 0;
        rows.forEach(r => { while (r.length < maxCols) r.push(''); });
        window._excelRows = rows;
        window._activeSheet = idx;
        window._excelColMap = {};
        window._activeRole = null;
        return { rows, maxCols };
      }

      let { rows, maxCols } = loadSheet(0);

      function renderExcelTable() {
        const sheetData = { rows: window._excelRows, maxCols: window._excelRows[0]?.length || 0 };
        const map = window._excelColMap;
        const active = window._activeRole;
        const reverseMap = {};
        Object.entries(map).forEach(([k,v]) => { reverseMap[v] = roles.find(r => r.key === k); });

        let h = '';

        // Sheet tabs (แสดงเฉพาะถ้ามี 2+ ชีท)
        if (wb.SheetNames.length > 1) {
          h += `<div style="display:flex;border-bottom:2px solid var(--line);background:#f0f2f5;">`;
          wb.SheetNames.forEach((name, si) => {
            const isActive = si === window._activeSheet;
            h += `<button class="sheet-tab" data-si="${si}" style="
              padding:8px 16px;border:none;background:${isActive ? '#fff' : 'transparent'};
              font-size:13px;font-weight:${isActive ? '700' : '400'};color:${isActive ? 'var(--navy)' : '#888'};
              cursor:pointer;font-family:inherit;border-bottom:${isActive ? '2px solid var(--blue)' : '2px solid transparent'};
              margin-bottom:-2px;transition:.15s;">
              ${esc2(name)}
            </button>`;
          });
          h += `</div>`;
        }

        // Role buttons
        h += `<div style="padding:10px 12px;background:#f8fafc;border-bottom:1px solid var(--line);display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
          <span style="font-size:11px;color:#888;margin-right:4px;">เลือกฟิลด์:</span>`;
        roles.forEach(r => {
          const assigned = map[r.key] != null;
          const isActive = active === r.key;
          const bg = isActive ? r.border : (assigned ? r.color : '#fff');
          const textColor = isActive ? '#fff' : (assigned ? r.border : '#666');
          const bdr = isActive ? r.border : (assigned ? r.border : '#ddd');
          h += `<button class="role-btn" data-role="${r.key}" style="
            padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;
            border:2px solid ${bdr};background:${bg};color:${textColor};
            transition:.15s;font-family:inherit;display:inline-flex;align-items:center;gap:4px;
            ${isActive ? 'box-shadow:0 2px 8px '+r.border+'44;transform:scale(1.05);' : ''}">
            ${r.icon} ${r.label}${assigned ? ' ✓' : ''}
          </button>`;
        });
        h += `</div>`;

        // Hint
        if (active) {
          const ar = roles.find(r => r.key === active);
          h += `<div style="padding:6px 12px;background:${ar.color};border-bottom:1px solid ${ar.border}44;font-size:12px;color:${ar.border};font-weight:600;">
            👆 คลิกคอลัมน์ที่ต้องการกำหนดเป็น "${ar.label}"
          </div>`;
        }

        // Table
        const theRows = sheetData.rows;
        const mc = sheetData.maxCols;
        if (!theRows.length) {
          h += `<div style="padding:20px;text-align:center;color:#999;">ชีทนี้ไม่มีข้อมูล</div>`;
        } else {
          h += `<div style="overflow:auto;max-height:50vh;">
            <table style="width:max-content;min-width:100%;border-collapse:collapse;font-size:12px;">`;
          theRows.forEach((row, ri) => {
            const isHead = ri === 0;
            const tag = isHead ? 'th' : 'td';
            h += '<tr>';
            for (let ci = 0; ci < mc; ci++) {
              const role = reverseMap[ci];
              const bg = isHead ? (role ? role.color : '#f0f4ff') : (role ? role.color + '88' : (ri % 2 === 0 ? '#fff' : '#f8f9fb'));
              const bc = isHead && role ? role.border : '#bbb';
              const cursor = isHead && active ? 'cursor:pointer;' : '';
              const label = isHead && role ? `<div style="font-size:9px;color:${role.border};margin-top:2px;font-weight:700;">${role.icon} ${role.label}</div>` : '';
              const val = row[ci] != null ? String(row[ci]) : '';
              const align = !isNaN(val.replace(/,/g,'')) && val !== '' ? 'text-align:right;' : '';
              const weight = isHead ? 'font-weight:700;color:var(--navy);' : '';
              h += `<${tag} ${isHead ? 'data-col="'+ci+'" class="excel-th"' : ''} style="padding:${isHead?'6':'4'}px 8px;border:${isHead?'1.5':'1'}px solid ${isHead?bc:'#ccc'};white-space:nowrap;background:${bg};${align}${weight}${cursor}">${esc2(val)}${label}</${tag}>`;
            }
            h += '</tr>';
          });
          h += '</table></div>';
        }

        $('#excelTableWrap').innerHTML = `<div style="border:2px solid #999;border-radius:6px;overflow:hidden;margin-top:8px;">${h}</div>`;

        // Bind sheet tabs
        $('#excelTableWrap').querySelectorAll('.sheet-tab').forEach(tab => {
          tab.onclick = () => {
            loadSheet(parseInt(tab.dataset.si));
            renderExcelTable();
            const r = window._excelRows;
            $('#ocrStats').querySelector('.os-head').innerHTML = `📊 ${esc2(wb.SheetNames[window._activeSheet])} · ${Math.max(0, r.length - 1)} รายการ`;
          };
        });

        // Bind role buttons
        $('#excelTableWrap').querySelectorAll('.role-btn').forEach(btn => {
          btn.onclick = () => {
            window._activeRole = (window._activeRole === btn.dataset.role) ? null : btn.dataset.role;
            renderExcelTable();
          };
        });

        // Bind column headers
        $('#excelTableWrap').querySelectorAll('.excel-th').forEach(th => {
          th.onclick = () => {
            if (!window._activeRole) return;
            const ci = parseInt(th.dataset.col);
            const map = window._excelColMap;
            const roleKey = window._activeRole;
            if (map[roleKey] != null) delete map[roleKey];
            Object.keys(map).forEach(k => { if (map[k] === ci) delete map[k]; });
            map[roleKey] = ci;
            window._activeRole = null;
            renderExcelTable();
          };
          th.onmouseenter = () => { if (window._activeRole) th.style.opacity = '0.7'; };
          th.onmouseleave = () => { th.style.opacity = '1'; };
        });
      }

      renderExcelTable();
      $('#ocrStats').innerHTML = `<div class="ocr-stats"><div class="os-head">📊 ${esc2(wb.SheetNames[0])} · ${rows.length - 1} รายการ</div></div>`;
      $('#ocrStats').style.display = 'block';

    } else if (fileType === 'pdf') {
      // === PDF: อ่านด้วย pdf.js → แยกเป็นตาราง ===
      $('#ocrTextLabel').style.display = 'none';
      $('#ocrText').style.display = 'none';
      $('#excelTableWrap').style.display = 'block';
      $('#ocrBtn').textContent = '⏳ กำลังอ่าน PDF...';
      const data = await uploadedFile.arrayBuffer();
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
      const pdf = await pdfjsLib.getDocument({ data }).promise;

      // ดึง text items พร้อมตำแหน่ง จากทุกหน้า
      const allItems = [];
      for (let i = 1; i <= pdf.numPages; i++) {
        prog.firstElementChild.style.width = (i / pdf.numPages * 100) + '%';
        const page = await pdf.getPage(i);
        const content = await page.getTextContent();
        content.items.forEach(item => {
          if (!item.str.trim()) return;
          const y = Math.round(item.transform[5]);
          const x = Math.round(item.transform[4]);
          allItems.push({ text: item.str.trim(), x, y });
        });
      }

      // จัดกลุ่มเป็นแถวตาม y (tolerance ±3px)
      allItems.sort((a, b) => b.y - a.y || a.x - b.x);
      const lineGroups = [];
      let currentY = null;
      allItems.forEach(item => {
        if (currentY === null || Math.abs(item.y - currentY) > 3) {
          lineGroups.push([]);
          currentY = item.y;
        }
        lineGroups[lineGroups.length - 1].push(item);
      });

      // แต่ละแถว sort ตาม x แล้วแยกเป็นคอลัมน์ (ช่องว่าง > 15px = คอลัมน์ใหม่)
      const tableRows = [];
      lineGroups.forEach(group => {
        group.sort((a, b) => a.x - b.x);
        const cols = [];
        let lastX = -999;
        group.forEach(item => {
          if (item.x - lastX > 15) {
            cols.push(item.text);
          } else {
            cols[cols.length - 1] += ' ' + item.text;
          }
          lastX = item.x + (item.text.length * 5);
        });
        if (cols.some(c => c.trim())) tableRows.push(cols);
      });

      if (!tableRows.length) throw new Error('ไม่พบข้อมูลใน PDF');

      // Pad ให้ทุกแถวมีจำนวนคอลัมน์เท่ากัน
      const maxCols = Math.max(...tableRows.map(r => r.length));
      tableRows.forEach(r => { while (r.length < maxCols) r.push(''); });

      // ใช้ระบบเดียวกับ Excel
      window._excelRows = tableRows;
      window._excelColMap = {};
      window._activeRole = null;

      const roles = [
        { key:'desc',  label:'รายการสินค้า', color:'#dbeafe', border:'#3b82f6', icon:'📦' },
        { key:'qty',   label:'จำนวน',       color:'#dcfce7', border:'#22c55e', icon:'🔢' },
        { key:'unit',  label:'หน่วย',        color:'#fef3c7', border:'#f59e0b', icon:'📏' },
      ];

      function renderPdfTable() {
        const map = window._excelColMap;
        const active = window._activeRole;
        const reverseMap = {};
        Object.entries(map).forEach(([k,v]) => { reverseMap[v] = roles.find(r => r.key === k); });

        let h = `<div style="padding:10px 12px;background:#f8fafc;border-bottom:1px solid var(--line);display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
          <span style="font-size:11px;color:#888;margin-right:4px;">เลือกฟิลด์:</span>`;
        roles.forEach(r => {
          const assigned = map[r.key] != null;
          const isActive = active === r.key;
          const bg = isActive ? r.border : (assigned ? r.color : '#fff');
          const textColor = isActive ? '#fff' : (assigned ? r.border : '#666');
          const bdr = isActive ? r.border : (assigned ? r.border : '#ddd');
          h += `<button class="role-btn" data-role="${r.key}" style="
            padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;
            border:2px solid ${bdr};background:${bg};color:${textColor};
            transition:.15s;font-family:inherit;display:inline-flex;align-items:center;gap:4px;
            ${isActive ? 'box-shadow:0 2px 8px '+r.border+'44;transform:scale(1.05);' : ''}">
            ${r.icon} ${r.label}${assigned ? ' ✓' : ''}
          </button>`;
        });
        h += `</div>`;

        if (active) {
          const ar = roles.find(r => r.key === active);
          h += `<div style="padding:6px 12px;background:${ar.color};border-bottom:1px solid ${ar.border}44;font-size:12px;color:${ar.border};font-weight:600;">
            👆 คลิกคอลัมน์ที่ต้องการกำหนดเป็น "${ar.label}"
          </div>`;
        }

        h += `<div style="overflow:auto;max-height:50vh;">
          <table style="width:max-content;min-width:100%;border-collapse:collapse;font-size:12px;">`;

        tableRows.forEach((row, ri) => {
          const isHead = ri === 0;
          const tag = isHead ? 'th' : 'td';
          h += '<tr>';
          for (let ci = 0; ci < maxCols; ci++) {
            const role = reverseMap[ci];
            const bg = isHead ? (role ? role.color : '#f0f4ff') : (role ? role.color + '88' : (ri % 2 === 0 ? '#fff' : '#f8f9fb'));
            const bc = isHead && role ? role.border : '#bbb';
            const cursor = isHead && active ? 'cursor:pointer;' : '';
            const label = isHead && role ? `<div style="font-size:9px;color:${role.border};margin-top:2px;font-weight:700;">${role.icon} ${role.label}</div>` : '';
            const val = row[ci] != null ? String(row[ci]) : '';
            const align = !isNaN(val.replace(/,/g,'')) && val !== '' ? 'text-align:right;' : '';
            const weight = isHead ? 'font-weight:700;color:var(--navy);' : '';
            h += `<${tag} ${isHead ? 'data-col="'+ci+'" class="excel-th"' : ''} style="padding:${isHead?'6':'4'}px 8px;border:${isHead?'1.5':'1'}px solid ${isHead?bc:'#ccc'};white-space:nowrap;background:${bg};${align}${weight}${cursor}">${esc2(val)}${label}</${tag}>`;
          }
          h += '</tr>';
        });
        h += '</table></div>';

        $('#excelTableWrap').innerHTML = `<div style="border:2px solid #999;border-radius:6px;overflow:hidden;margin-top:8px;">${h}</div>`;

        $('#excelTableWrap').querySelectorAll('.role-btn').forEach(btn => {
          btn.onclick = () => {
            window._activeRole = (window._activeRole === btn.dataset.role) ? null : btn.dataset.role;
            renderPdfTable();
          };
        });

        $('#excelTableWrap').querySelectorAll('.excel-th').forEach(th => {
          th.onclick = () => {
            if (!window._activeRole) return;
            const ci = parseInt(th.dataset.col);
            const map = window._excelColMap;
            const roleKey = window._activeRole;
            if (map[roleKey] != null) delete map[roleKey];
            Object.keys(map).forEach(k => { if (map[k] === ci) delete map[k]; });
            map[roleKey] = ci;
            window._activeRole = null;
            renderPdfTable();
          };
          th.onmouseenter = () => { if (window._activeRole) th.style.opacity = '0.7'; };
          th.onmouseleave = () => { th.style.opacity = '1'; };
        });
      }

      renderPdfTable();
      $('#ocrStats').innerHTML = `<div class="ocr-stats"><div class="os-head">📄 PDF · ${pdf.numPages} หน้า · ${tableRows.length - 1} รายการ</div></div>`;
      $('#ocrStats').style.display = 'block';

    } else {
      // === Image: OCR ด้วย Tesseract ===
      $('#ocrTextLabel').style.display = '';
      $('#ocrText').style.display = '';
      $('#excelTableWrap').style.display = 'none';
      $('#ocrBtn').textContent = '⏳ กำลังปรับภาพ + ลบเส้นตาราง...';
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
    }
  } catch (err) { alert('อ่านไฟล์ผิดพลาด: ' + err.message); }
  prog.style.display = 'none';
  prog.firstElementChild.style.width = '0';
  $('#ocrBtn').disabled = false;
  $('#ocrBtn').textContent = '🔍 อ่านข้อความ';
};

$('#ocrText').addEventListener('input', function () { autoResize(this); });

$('#parseBtn').onclick = async () => {
  $('#itemRows').innerHTML = '';
  const rows = [];

  if ((fileType === 'excel' || fileType === 'pdf') && window._excelRows && window._excelColMap) {
    // === Excel/PDF: ใช้คอลัมน์ที่เลือก ===
    const map = window._excelColMap;
    const dataRows = window._excelRows.slice(1); // ข้ามหัวตาราง
    if (map.desc == null) { alert('กรุณาคลิกเลือกคอลัมน์ "รายการสินค้า" ก่อน'); return; }
    dataRows.forEach(row => {
      const desc  = String(row[map.desc] ?? '').trim();
      if (!desc) return;
      const qty   = map.qty != null ? (parseFloat(row[map.qty]) || 1) : 1;
      const unit  = map.unit != null ? String(row[map.unit] ?? '').trim() : '';
      const tr = itemRow({ desc, qty, unit });
      $('#itemRows').appendChild(tr);
      rows.push(tr);
    });
  } else {
    // === Text: parse จาก textarea ===
    const txt   = $('#ocrText').value;
    const lines = txt.split(/\n/).map(l => l.trim()).filter(Boolean);
    lines.forEach(l => {
      if (/[ก-๙A-Za-z]/.test(l) && !/รวม|total|vat|ภาษี|สุทธิ/i.test(l)) {
        const desc = l.trim();
        if (desc) { const tr = itemRow({ desc }); $('#itemRows').appendChild(tr); rows.push(tr); }
      }
    });
  }

  if (!rows.length) { $('#itemRows').appendChild(itemRow()); }
  $$('.tab')[1].click();
  render();
  if (currentCustomerCode && rows.length) { await batchMatchItems(rows); }
};

function itemRow(d = {}) {
  const tr = document.createElement('tr');
  tr.className = 'item';
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
    <td class="td-act">
      <div class="act-wrap">
        <button class="cart-btn" title="เพิ่มรายละเอียดย่อย" onclick="addSubItem(this)">➕</button>
        <button class="del" title="ลบ">✕</button>
      </div>
    </td>
  `;
  tr.querySelector('.del').onclick = () => { 
    tr.remove(); 
    render(); 
  };
  tr.querySelectorAll('input').forEach(i => i.oninput = render);
  const descInput = tr.querySelector('.desc');
  let lastDesc = (d.desc || '').trim();
  descInput.addEventListener('focus', function () { lastDesc = this.value.trim(); });
  descInput.addEventListener('blur', async function () {
    const newDesc = this.value.trim();
    if (!newDesc || newDesc === lastDesc || !currentCustomerCode) return;
    if (newDesc.length < 2) return;
    lastDesc = newDesc;
    await batchMatchItems([tr]);
  });
  tr.querySelector('.td-num').onclick = () => {
    if (!currentCustomerCode) return;
    if (tr.dataset.isNew === '1') return;
    const keyword = tr.dataset.keyword || tr.querySelector('.desc').value || '';
    if (!keyword) return;
    const pname   = tr.dataset.productName || keyword;
    const itemNew = tr.dataset.itemNew || '';
    const currentPrice = parseFloat(tr.querySelector('.price')?.value) || 0;
    showHistPopover(keyword, pname, currentCustomerCode, tr, itemNew, currentPrice);
  };
  if (d.isNew) setNewTag(tr, true);
  
  // สร้าง unique ID สำหรับ item นี้
  tr.dataset.itemId = 'item_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  
  return tr;
}

// ฟังก์ชันเพิ่ม/โฟกัส รายละเอียดย่อย (textarea ตัวเดียวใน td-desc)
function addSubItem(btn) {
  const parentRow = btn.closest('tr.item');
  const tdDesc = parentRow.querySelector('.td-desc');
  let wrap = tdDesc.querySelector('.sub-wrap');
  if (wrap) { wrap.querySelector('.sub-detail').focus(); return; }
  wrap = document.createElement('div');
  wrap.className = 'sub-wrap';
  wrap.style.cssText = 'position:relative;margin-top:2px;';
  const ta = document.createElement('textarea');
  ta.className = 'sub-detail';
  ta.placeholder = 'พิมพ์รายละเอียดย่อย... (กด Enter ขึ้นบรรทัดใหม่)';
  ta.style.cssText = 'width:100%;border:1px solid var(--line);border-radius:6px;background:#fafbfc;padding:4px 28px 4px 8px;font-size:11.5px;color:#555;font-family:inherit;resize:none;min-height:28px;overflow:hidden;transition:border-color .15s,background .15s;';
  ta.rows = 1;
  ta.oninput = function() { this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'; render(); };
  ta.addEventListener('focus', function() { this.style.borderColor = 'var(--blue)'; this.style.background = '#fff'; });
  ta.addEventListener('blur', function() { this.style.borderColor = 'var(--line)'; this.style.background = '#fafbfc'; });
  const delBtn = document.createElement('button');
  delBtn.innerHTML = '✕';
  delBtn.title = 'ลบรายละเอียดย่อย';
  delBtn.style.cssText = 'position:absolute;top:3px;right:3px;width:20px;height:20px;border:none;background:none;color:#ccc;font-size:11px;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;transition:.15s;';
  delBtn.onmouseenter = function() { this.style.background = '#fef2f2'; this.style.color = '#b91c1c'; };
  delBtn.onmouseleave = function() { this.style.background = 'none'; this.style.color = '#ccc'; };
  delBtn.onclick = function(e) { e.stopPropagation(); wrap.remove(); render(); };
  wrap.appendChild(ta);
  wrap.appendChild(delBtn);
  tdDesc.appendChild(wrap);
  ta.focus();
  render();
}

$('#addItem').onclick = () => { $('#itemRows').appendChild(itemRow()); render(); };

const poSearchCache = {};

async function openPoModal(keyword) {
  const modal = $('#poModal');
  const title = $('#poModalTitle');
  const body  = $('#poModalBody');
  title.textContent = `🛒 ราคาต้นทุน: ${keyword}`;
  body.innerHTML = '<div class="po-modal-loading">⏳ กำลังค้นหา...</div>';
  modal.classList.add('show');
  if (poSearchCache[keyword]) { renderPoResults(poSearchCache[keyword], keyword); return; }
  try {
    const res = await fetch('/po-search/search', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ items: [keyword] }),
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    poSearchCache[keyword] = data;
    renderPoResults(data, keyword);
  } catch (e) {
    body.innerHTML = `<div class="po-modal-loading" style="color:#b91c1c">❌ ค้นไม่สำเร็จ: ${esc2(e.message)}</div>`;
  }
}

function closePoModal() { $('#poModal').classList.remove('show'); }

function renderPoResults(data, keyword) {
  const body = $('#poModalBody');
  const item = data && data[0];
  const recs = (item?.records || []).slice().sort((a, b) => {
    const da = new Date(a.doc_date || 0), db = new Date(b.doc_date || 0);
    return db - da;
  });
  if (!recs.length) {
    const method = item?.method === 'fuzzy' ? ' (ค้นแบบใกล้เคียง)' : '';
    body.innerHTML = `<div class="po-modal-loading">ไม่พบราคาต้นทุนสำหรับ "${esc2(keyword)}"${method}</div>`;
    return;
  }
  const isFuzzy = item.method === 'fuzzy';
  const prices = recs.map(r => parseFloat(r.unit_price_thb || r.unit_price) || 0).filter(p => p > 0);
  const mn = prices.length ? Math.min(...prices) : 0;
  const mx = prices.length ? Math.max(...prices) : 0;
  const latest = item.latest;
  let h = '';
  if (prices.length) {
    h += `<div class="po-modal-sum">
      📊 ${recs.length} รายการ${isFuzzy ? ' <span style="color:#e65100;font-size:10px;background:#fff3e0;padding:2px 6px;border-radius:4px;">ค้นแบบใกล้เคียง</span>' : ''}
      · ต่ำสุด <b style="color:#2e7d32">${poFmt(mn)}</b>
      · สูงสุด <b style="color:#c62828">${poFmt(mx)}</b>
      ${latest ? ' · ล่าสุด <b>' + poFmt(parseFloat(latest.unit_price_thb || latest.unit_price) || 0) + '</b> (' + poThD(latest.doc_date) + ')' : ''}
    </div>`;
  }
  h += '<div><table>';
  h += `<thead><tr>
    <th>#</th><th>วันที่</th><th>เลขที่ PO</th><th>ผู้ขาย</th>
    <th>ชื่อสินค้า</th><th class="r">จำนวน</th><th>หน่วย</th>
    <th class="r">ราคา/หน่วย</th>
    <th class="r">ส่วนลด(%)</th><th class="r">ส่วนลด(฿)</th>
    <th class="r">ส่วนลดบิล(%)</th><th class="r">ส่วนลดบิล(฿)</th>
    <th class="r">ราคา(฿)</th><th>สกุลเงิน</th>
  </tr></thead><tbody>`;
  recs.forEach((r, i) => {
    const p = parseFloat(r.unit_price_thb || r.unit_price) || 0;
    let pc = '';
    if (mn > 0 && mx > 0 && mn !== mx) {
      if (p <= mn) pc = 'best'; else if (p >= mx) pc = 'worst';
    }
    const isLatest = latest && r.doc_no === latest.doc_no && r.product_name === latest.product_name;
    const docNoSafe = (r.doc_no||'').replace(/'/g,"\\'");
    const pnameSafe = (r.product_name||'').replace(/'/g,"\\'");
    h += `<tr style="${isLatest ? 'background:#f0fdf4;' : ''}">
      <td style="text-align:center;color:#aaa">${i + 1}</td>
      <td>${poThD(r.doc_date)}</td>
      <td><a href="#" style="color:var(--blue);font-weight:700;text-decoration:none;" onclick="openPoDetail('${docNoSafe}','${pnameSafe}');return false;">${esc2(r.doc_no || '-')}</a></td>
      <td>${esc2(r.vendor_name || '-')}</td>
      <td>${esc2(r.product_name || '-')}${isLatest ? ' <span style="font-size:9px;color:#16a34a;background:#dcfce7;border-radius:3px;padding:1px 5px;">ล่าสุด</span>' : ''}</td>
      <td class="r">${poFmtQ(r.qty)}</td>
      <td>${esc2(r.unit || '-')}</td>
      <td class="r" style="font-weight:600">${poFmt(r.unit_price)}</td>
      <td class="r">${poRaw(r.item_discount_pct)}</td>
      <td class="r">${poRaw(r.item_discount_amt)}</td>
      <td class="r">${poRaw(r.bill_discount_pct)}</td>
      <td class="r">${poRaw(r.bill_discount_amt)}</td>
      <td class="r ${pc}" style="font-weight:700">${poFmt(r.unit_price_thb || r.unit_price)}</td>
      <td style="color:#888">${esc2(r.currency || 'THB')}</td>
    </tr>`;
  });
  h += '</tbody></table></div>';
  body.innerHTML = h;
}

const THAI_M2 = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
function poThD(ds) { if (!ds) return '—'; const d = new Date(ds); if (isNaN(d)) return ds; return d.getDate() + ' ' + THAI_M2[d.getMonth()] + ' ' + (d.getFullYear() + 543); }
function poFmt(n) { return (parseFloat(n) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
function poFmtQ(n) { const v = parseFloat(n) || 0; return v === Math.floor(v) ? v.toLocaleString('en-US') : v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }); }
function poRaw(v) { if (v == null || v === '') return '—'; return esc2(String(v)); }

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if ($('#poDetailModal').classList.contains('show')) closePoDetail();
    else if ($('#poModal').classList.contains('show')) closePoModal();
  }
});

function clearRefTag(tr) { tr.querySelector('.ref-tag')?.remove(); }

const poDetailCache = {};

async function openPoDetail(docNo, highlightName) {
  if (!docNo || docNo === '-') return;
  const modal = $('#poDetailModal');
  const title = $('#poDetailTitle');
  const body  = $('#poDetailBody');
  title.textContent = `📋 ${docNo}`;
  body.innerHTML = '<div class="po-modal-loading">⏳ กำลังโหลด...</div>';
  modal.classList.add('show');
  if (poDetailCache[docNo]) { renderPoDetailContent(poDetailCache[docNo], docNo, highlightName); return; }
  try {
    const res = await fetch('/po-search/detail', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ doc_no: docNo }),
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    poDetailCache[docNo] = data;
    renderPoDetailContent(data, docNo, highlightName);
  } catch (e) {
    body.innerHTML = `<div class="po-modal-loading" style="color:#c62828">❌ โหลดไม่สำเร็จ: ${esc2(e.message)}</div>`;
  }
}

function closePoDetail() { $('#poDetailModal').classList.remove('show'); }

function renderPoDetailContent(records, docNo, highlightName) {
  const body = $('#poDetailBody');
  if (!records || !records.length) {
    body.innerHTML = '<div class="po-modal-loading">ไม่พบรายการใน PO นี้</div>';
    return;
  }
  const vendor = records[0].vendor_name || '-';
  const date = poThD(records[0].doc_date);
  const poTotal = records[0].po_total;
  const hlLower = (highlightName || '').toLowerCase();
  let h = `<div style="padding:10px 16px;font-size:12px;color:#555;background:#fafbfc;border-bottom:1px solid var(--line);">
    📅 ${date} · 🏢 ${esc2(vendor)} · ${records.length} รายการ${poTotal ? ' · รวม PO <b>' + poRaw(poTotal) + '</b> บาท' : ''}
  </div>`;
  h += '<div><table class="po-modal-body">';
  h += `<thead><tr>
    <th>#</th><th>รหัสสินค้า</th><th>ชื่อสินค้า</th>
    <th class="r">จำนวน</th><th>หน่วย</th><th class="r">ราคา/หน่วย</th>
    <th class="r">ส่วนลด(%)</th><th class="r">ส่วนลด(฿)</th>
    <th class="r">เงินสินค้า</th><th class="r">รวม PO</th>
    <th class="r">ส่วนลดบิล(%)</th><th class="r">ส่วนลดบิล(฿)</th>
    <th class="r">ก่อนภาษี</th><th class="r">ภาษีซื้อ</th>
    <th class="r">รวมทั้งสิ้น</th><th class="r">ราคา(฿)</th><th>สกุลเงิน</th>
  </tr></thead><tbody>`;
  records.forEach((r, i) => {
    const isHL = hlLower && (r.product_name || '').toLowerCase() === hlLower;
    const rowStyle = isHL ? 'background:#fff8e1;' : '';
    const nameStyle = isHL ? 'font-weight:800;color:#e65100;' : '';
    h += `<tr style="${rowStyle}">
      <td style="text-align:center;color:#aaa">${i + 1}</td>
      <td>${esc2(r.product_code || '-')}</td>
      <td style="${nameStyle}">${esc2(r.product_name || '-')}${isHL ? ' <span style="font-size:9px;background:#ffe082;color:#e65100;border-radius:3px;padding:1px 5px;font-weight:700;">◀ สินค้าที่ค้นหา</span>' : ''}</td>
      <td class="r">${poFmtQ(r.qty)}</td>
      <td>${esc2(r.unit || '-')}</td>
      <td class="r" style="font-weight:600">${poFmt(r.unit_price)}</td>
      <td class="r">${poRaw(r.item_discount_pct)}</td>
      <td class="r">${poRaw(r.item_discount_amt)}</td>
      <td class="r">${poRaw(r.item_amount)}</td>
      <td class="r">${poRaw(r.po_total)}</td>
      <td class="r">${poRaw(r.bill_discount_pct)}</td>
      <td class="r">${poRaw(r.bill_discount_amt)}</td>
      <td class="r">${poRaw(r.before_tax)}</td>
      <td class="r">${poRaw(r.input_tax)}</td>
      <td class="r" style="font-weight:700">${poRaw(r.grand_total)}</td>
      <td class="r" style="font-weight:700">${poFmt(r.unit_price_thb || r.unit_price)}</td>
      <td style="color:#888">${esc2(r.currency || 'THB')}</td>
    </tr>`;
  });
  h += '</tbody></table></div>';
  body.innerHTML = h;
}

function setRefTag(tr, suggestions) {
  const td = tr.querySelector('.td-desc');
  clearRefTag(tr);
  if (!suggestions?.length) return;
  const best = suggestions[0];
  const wrap = document.createElement('div');
  wrap.style.position = 'relative';
  const tag = document.createElement('div');
  tag.className = 'ref-tag';
  tag.innerHTML =
    `💡 ราคาอ้างอิง: <b>${fmt(best.unit_price)}</b> บาท` +
    ` · ${esc2(best.so_no || '-')}` +
    ` · ${esc2(best.customer_name || '-')}` +
    (best.match_keyword ? `<br>🔍 จับคู่: <b>${best.match_keyword.split(/\s*\+\s*/).reduce((acc, kw, i) => {
      if (i === 0) return esc2(kw);
      return acc + (i % 2 === 0 ? '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+ ' : ' + ') + esc2(kw);
    }, '')}</b>` : '') +
    (suggestions.length > 1 ? ` <span style="color:var(--blue)">▾ ${suggestions.length} บริษัท</span>` : '');

  /* สร้าง panel + backdrop (append เข้า body ตอนเปิด) */
  const panel = document.createElement('div');
  panel.className = 'ref-panel';
  const backdrop = document.createElement('div');
  backdrop.className = 'ref-panel-backdrop';
  let rows = '';
  suggestions.forEach((s, i) => {
    rows += `<tr class="rp-row">
      <td style="text-align:center;color:#aaa">${i + 1}</td>
      <td class="rp-cust">${esc2(s.customer_name || '-')}</td>
      <td style="white-space:nowrap">${esc2(s.so_no || '-')}</td>
      <td style="white-space:nowrap">${esc2(s.doc_date || '-')}</td>
      <td class="rp-pname">${esc2(s.product_name || '-')}</td>
      <td class="rp-kw">${((s.match_keyword || '-').split(/\s*\+\s*/).reduce((acc, kw, i) => {
        if (i === 0) return esc2(kw);
        return acc + (i % 2 === 0 ? '<br>+ ' : ' + ') + esc2(kw);
      }, ''))}</td>
      <td class="rp-price">${fmt(s.unit_price)}</td>
      <td><button class="rp-use" data-i="${i}">ใช้ราคานี้</button></td>
    </tr>`;
  });
  panel.innerHTML = `<div class="rp-head">📋 ราคาอ้างอิงจากลูกค้าอื่น (${suggestions.length} บริษัทล่าสุด)</div>
    <div class="rp-scroll"><table><thead><tr><th style="width:30px">#</th><th>บริษัท</th><th>SO No.</th><th>วันที่</th><th>ชื่อสินค้า</th><th>keyword</th><th style="text-align:right">ราคา/หน่วย</th><th></th></tr></thead>
    <tbody>${rows}</tbody></table></div>`;

  function closePanel() {
    panel.style.display = 'none';
    backdrop.style.display = 'none';
    if (panel.parentNode) panel.remove();
    if (backdrop.parentNode) backdrop.remove();
  }

  tag.onclick = e => {
    e.stopPropagation();
    const wasOpen = panel.style.display === 'block';
    closeAllRefPanels();
    if (wasOpen) return;

    document.body.appendChild(backdrop);
    document.body.appendChild(panel);
    backdrop.style.display = 'block';
    panel.style.display = 'block';

    /* คำนวณตำแหน่ง: อยู่ใต้ tag ถ้าพอ / ขึ้นบนถ้าล้น */
    requestAnimationFrame(() => {
      const tagRect = tag.getBoundingClientRect();
      const panelH  = panel.offsetHeight;
      const panelW  = panel.offsetWidth;
      const vw = window.innerWidth, vh = window.innerHeight;

      let top, left;
      /* แนวตั้ง */
      if (tagRect.bottom + panelH + 6 <= vh) {
        top = tagRect.bottom + 4;                       /* เปิดลงล่าง */
      } else if (tagRect.top - panelH - 6 >= 0) {
        top = tagRect.top - panelH - 4;                 /* เปิดขึ้นบน */
      } else {
        top = Math.max(8, vh - panelH - 8);             /* ชิดล่างสุด */
      }
      /* แนวนอน */
      left = tagRect.left;
      if (left + panelW > vw - 8) left = Math.max(8, vw - panelW - 8);

      panel.style.top  = top  + 'px';
      panel.style.left = left + 'px';
    });
  };

  backdrop.onclick = closePanel;

  panel.querySelectorAll('.rp-use').forEach(btn => {
    btn.onclick = e => {
      e.stopPropagation();
      const s = suggestions[parseInt(btn.dataset.i)];
      const priceEl = tr.querySelector('.price');
      if (priceEl) { priceEl.value = s.unit_price; render(); }
      closePanel();
    };
  });
  panel.addEventListener('click', e => e.stopPropagation());
  wrap.appendChild(tag);
  td.appendChild(wrap);
}

function closeAllRefPanels() {
  document.querySelectorAll('.ref-panel').forEach(p => { p.style.display = 'none'; p.remove(); });
  document.querySelectorAll('.ref-panel-backdrop').forEach(b => { b.style.display = 'none'; b.remove(); });
}

document.addEventListener('click', () => {
  closeAllRefPanels();
});

function setNewTag(tr, isNew) {
  const td  = tr.querySelector('.td-desc');
  let   tag = td.querySelector('.new-tag');
  if (isNew) {
    if (!tag) { tag = document.createElement('div'); tag.className = 'new-tag'; td.appendChild(tag); }
    tr.dataset.isNew = '1';
    tr.querySelector('.td-num').classList.add('no-hist');
  } else {
    if (tag) tag.remove();
    tr.dataset.isNew = '0';
    tr.querySelector('.td-num').classList.remove('no-hist');
  }
}

const BATCH_URL   = '/soitem/batch-match';
const HISTORY_URL = '/soitem/sales-history';

async function batchMatchItems(rows) {
  if (!currentCustomerCode || !rows.length) return;
  const names = rows.map(tr => (tr.querySelector('.desc')?.value || '').trim());
  if (names.every(n => n.length < 2)) return;
  const bar = $('#matchBar');
  bar.style.display = 'flex';
  $('#matchBarTxt').textContent = `กำลังดึงราคา ${names.length} รายการ จากประวัติลูกค้า...`;
  try {
    const res = await fetch(BATCH_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
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
      clearRefTag(tr);
      setNewTag(tr, false);
      if (!r.is_new) {
        const priceEl = tr.querySelector('.price');
        const unitEl  = tr.querySelector('.unit');
        if (r.has_price && priceEl) { priceEl.value = r.unit_price; }
        else if (r.ref_suggestions?.length && priceEl) { priceEl.value = r.ref_suggestions[0].unit_price; setRefTag(tr, r.ref_suggestions); }
        if (r.unit && unitEl) unitEl.value = r.unit;
        matched++;
      } else {
        const priceEl = tr.querySelector('.price');
        const unitEl  = tr.querySelector('.unit');
        if (r.ref_suggestions?.length) {
          if (priceEl) priceEl.value = r.ref_suggestions[0].unit_price;
          setRefTag(tr, r.ref_suggestions);
          if (unitEl && r.ref_suggestions[0].unit) unitEl.value = r.ref_suggestions[0].unit;
        }
        setNewTag(tr, true);
      }
    });
    $('#matchBarTxt').textContent = `จับคู่สำเร็จ ${matched} รายการ · สินค้าใหม่ ${names.length - matched} รายการ`;
    render();
  } catch (err) {
    console.warn('batchMatchItems error:', err);
    $('#matchBarTxt').textContent = '❌ ดึงราคาไม่สำเร็จ — ' + err.message;
  }
  setTimeout(() => { bar.style.display = 'none'; }, 4000);
}

async function onCustomerSelected() {
  const rows = [...$$('#itemRows .item')];
  rows.forEach(tr => {
    const priceEl = tr.querySelector('.price');
    if (priceEl) priceEl.value = 0;
    // ไม่ล้าง unit — เก็บค่าเดิมไว้
    tr.dataset.itemNew = ''; tr.dataset.productName = ''; tr.dataset.keyword = ''; tr.dataset.isNew = '0';
    setNewTag(tr, false); clearRefTag(tr);
  });
  render();
  if (rows.length) await batchMatchItems(rows);
}

const API_URL = 'http://server_update:8000/api/getCustAndVendor';
let searchTimer = null;

function triggerCompanySearch(el) {
  clearTimeout(searchTimer);
  const raw = el.value.trim();
  if (raw.length < 2) { $('#acList').style.display = 'none'; return; }
  const cleaned = cleanThaiCompanyName(raw);
  if (cleaned !== raw) el.value = cleaned;
  searchCompany(cleaned);
}

$('#custCompany').addEventListener('input', function () {
  clearTimeout(searchTimer);
  const v = this.value.trim();
  if (v.length < 2) { $('#acList').style.display = 'none'; return; }
  const el = this;
  searchTimer = setTimeout(() => triggerCompanySearch(el), 300);
});

$('#custCompany').addEventListener('paste', function () {
  clearTimeout(searchTimer);
  const el = this;
  searchTimer = setTimeout(() => triggerCompanySearch(el), 50);
});

$('#custCompany').addEventListener('keydown', function (e) {
  if (e.key === 'Enter') { e.preventDefault(); triggerCompanySearch(this); }
});

async function searchCompany(keyword) {
  if (!keyword) return;
  let cleaned = cleanThaiCompanyName(keyword);
  let coreName = extractCoreName(cleaned);
  const words = coreName.split(/\s+/).filter(w => w.length >= 2);
  if (!words.length) {
    $('#acList').innerHTML = '<div class="ac-empty">ไม่พบข้อมูลบริษัท</div>';
    $('#acList').style.display = 'block';
    return;
  }
  try {
    const allResults = await Promise.all(
      words.map(async (word) => {
        try {
          const res = await fetch(`${API_URL}?keySearch=${encodeURIComponent(word)}`);
          if (!res.ok) return [];
          const data = await res.json();
          return [...(data.Customer || []), ...(data.Supplier || [])];
        } catch { return []; }
      })
    );
    if (words.length === 1) {
      const companies = allResults[0] || [];
      if (!companies.length) { $('#acList').innerHTML = '<div class="ac-empty">ไม่พบข้อมูลบริษัท</div>'; $('#acList').style.display = 'block'; }
      else { showAcResults(companies); }
      return;
    }
    const getCode = c => (c.CustCode || c.VendorCode || '').trim();
    let matchedCodes = new Set(allResults[0].map(getCode));
    for (let i = 1; i < allResults.length; i++) {
      const codes = new Set(allResults[i].map(getCode));
      matchedCodes = new Set([...matchedCodes].filter(c => codes.has(c)));
    }
    let companies = allResults[0].filter(c => matchedCodes.has(getCode(c)));
    if (!companies.length) {
      const longestIdx = words.reduce((best, w, i) => w.length > words[best].length ? i : best, 0);
      companies = allResults[longestIdx] || [];
    }
    if (!companies.length) { $('#acList').innerHTML = '<div class="ac-empty">ไม่พบข้อมูลบริษัท</div>'; $('#acList').style.display = 'block'; }
    else { showAcResults(companies); }
  } catch (err) {
    console.error(err);
    $('#acList').innerHTML = '<div class="ac-empty">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
    $('#acList').style.display = 'block';
  }
}

function extractCoreName(name) {
  let t = name;
  t = t.replace(/^(บริษัท|บจก\.|บจก|บมจ\.|บมจ|หจก\.|หจก|ห้างหุ้นส่วนจำกัด|ห้างหุ้นส่วนสามัญ|ห้าง)\s*/i, '');
  t = t.replace(/\s*(จำกัด|\(มหาชน\)|มหาชน|จก\.|จก)\s*/g, '');
  t = t.replace(/\(\s*\)/g, '').replace(/\s{2,}/g, ' ').trim();
  return t.length >= 2 ? t : name;
}

function cleanThaiCompanyName(text) {
  let t = text;
  t = t.replace(/[\u200B\u200C\u200D\uFEFF\u00A0]/g, '');
  const pdfFixes = [
    [/จ\s*ำ\s*กั\s*ด/g,'จำกัด'],[/ม\s*ห\s*า\s*ช\s*น/g,'มหาชน'],
    [/บ\s*ริ\s*ษั\s*ท/g,'บริษัท'],[/ห้\s*า\s*ง/g,'ห้าง'],[/หุ้\s*น\s*ส่\s*วน/g,'หุ้นส่วน'],
  ];
  pdfFixes.forEach(([rx, rep]) => { t = t.replace(rx, rep); });
  for (let i = 0; i < 5; i++) {
    const prev = t;
    t = t.replace(/([\u0E31\u0E34-\u0E3A\u0E47-\u0E4E])\s+([\u0E01-\u0E2E])/g, '$1$2');
    t = t.replace(/([\u0E01-\u0E2E])\s+([\u0E31\u0E34-\u0E3A\u0E47-\u0E4E])/g, '$1$2');
    if (t === prev) break;
  }
  t = t.replace(/\s{2,}/g, ' ').trim();
  return t;
}

function showAcResults(companies) {
  const list = $('#acList');
  list.innerHTML = '';
  list.style.display = 'block';
  companies.forEach(c => {
    const code    = (c.CustCode||c.VendorCode||'').trim();
    const title   = (c.CustTitle||c.VendorTitle||'').trim();
    const rawName = (c.CustName||c.VendorName||'').trim();
    const name    = (title && !rawName.startsWith(title)) ? title+' '+rawName : rawName;
    const addr    = [c.ContAddr1,c.ContAddr2,c.ContDistrict,c.ContAmphur,c.ContProvince,c.ContPostCode].filter(p=>p&&p.trim()).join(' ').trim()||(c.CustAddr1||'').trim();
    const tel     = (c.ContTel||c.Telephone||c.Tel||c.Phone||'').trim();
    const tax     = (c.TaxId||c.TaxNo||c.TaxID||c.IDCardNo||'').trim();
    const branch  = (c.BrchID||c.Branch||c.BranchName||'').trim();
    const div = document.createElement('div');
    div.className = 'ac-item';
    div.innerHTML = `<div class="ac-name">${code?`<span style="color:var(--blue);margin-right:6px;">[${esc2(code)}]</span>`:''} ${esc2(name)}</div><div class="ac-sub">${esc2(addr||'—')}</div>`;
    div.onclick = () => {
      $('#custCode').value=code;$('#custCompany').value=name;$('#custAddr').value=addr;
      $('#custTel').value=tel;$('#custTax').value=tax;$('#custBranch').value=branch;
      list.style.display='none';currentCustomerCode=code;render();onCustomerSelected();
    };
    list.appendChild(div);
  });
}

document.addEventListener('click', e => {
  if (!$('.search-wrap')?.contains(e.target)) $('#acList').style.display = 'none';
  const pop = $('#histPopover');
  if (pop.style.display === 'block' && !pop.contains(e.target) && !e.target.closest('.td-num')) pop.style.display = 'none';
});

const histCache = {};

async function showHistPopover(keyword, productName, customerCode, rowEl, itemNewCode, currentPrice) {
  const pop  = $('#histPopover');
  const head = $('#hpHead');
  const body = $('#hpBody');
  positionPopover(pop, rowEl);
  pop.style.display = 'block';
  const cacheKey = `${customerCode}__${itemNewCode || keyword}`;
  if (histCache[cacheKey]) { renderHistContent(histCache[cacheKey], keyword, productName, head, body, currentPrice, rowEl); return; }
  head.innerHTML = `⏳ กำลังโหลด...`;
  body.innerHTML = '<div style="padding:24px;text-align:center;color:var(--muted);font-size:12px;">⏳</div>';
  try {
    const params = new URLSearchParams();
    if (itemNewCode) params.set('item_new', itemNewCode);
    params.set('keyword', keyword);
    const res = await fetch(`${HISTORY_URL}/${encodeURIComponent(customerCode)}?${params.toString()}`);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const records = await res.json();
    histCache[cacheKey] = records;
    renderHistContent(records, keyword, productName, head, body, currentPrice, rowEl);
  } catch (err) {
    body.innerHTML = '<div style="padding:24px;text-align:center;color:#b91c1c;font-size:12px;">❌ โหลดไม่สำเร็จ</div>';
  }
}

function positionPopover(pop, rowEl) {
  const rect = rowEl.getBoundingClientRect();
  const popW = Math.min(680, window.innerWidth * 0.9);
  let top  = rect.bottom + window.scrollY + 6;
  let left = rect.left + window.scrollX;
  if (left + popW > window.innerWidth) left = Math.max(8, window.innerWidth - popW - 8);
  pop.style.top  = top  + 'px';
  pop.style.left = left + 'px';
}

function renderHistContent(records, itemNew, productName, head, body, currentPrice, itemRowEl) {
  const tag = txt => `<span class="mm-tag">${esc2(txt)}</span>`;
  if (!records || !records.length) {
    head.innerHTML = `📊 ${esc2(productName)} ${tag('ไม่พบประวัติ')}`;
    body.innerHTML = '<div style="padding:24px;text-align:center;color:var(--muted);font-size:12px;">ลูกค้ายังไม่เคยสั่งซื้อสินค้านี้</div>';
    return;
  }
  records = records.slice().sort((a, b) => {
    const da = new Date(a.doc_date || a.doc_date_raw || 0);
    const db = new Date(b.doc_date || b.doc_date_raw || 0);
    return db - da;
  });
  head.innerHTML = `📊 ${esc2(productName)} ${tag(records.length + ' รายการ')}`;
  let html = `<div style="padding:4px 14px 0;font-size:11px;color:var(--muted);">💡 ดับเบิ้ลคลิกที่รายการเพื่อใช้ราคานั้น</div>
    <table><thead><tr><th>#</th><th>SO No.</th><th>วันที่</th><th>ชื่อสินค้า</th><th style="text-align:right">จำนวน</th><th style="text-align:right">ราคา/หน่วย</th></tr></thead><tbody>`;
  let matchedIdx = -1;
  if (currentPrice > 0) matchedIdx = records.findIndex(r => Math.abs((r.unit_price || 0) - currentPrice) < 0.01);
  records.forEach((r, i) => {
    const isUsed = (i === matchedIdx);
    const priceColor = isUsed ? 'color:#16a34a;font-weight:700;' : 'color:#374151;';
    const rowBg      = isUsed ? 'background:#f0fdf4;' : '';
    const usedBadge  = isUsed ? ' <span style="font-size:10px;color:#16a34a;background:#dcfce7;border:1px solid #bbf7d0;border-radius:4px;padding:1px 5px;">✓ ราคาที่ใช้</span>' : '';
    const priceDisplay = (r.unit_price != null && r.unit_price > 0) ? fmt(r.unit_price) : '<span style="color:#9ca3af;font-size:11px;">ไม่ระบุ</span>';
    html += `<tr class="hist-row" data-idx="${i}" style="${rowBg}cursor:pointer;" title="ดับเบิ้ลคลิกเพื่อใช้ราคา ${fmt(r.unit_price || 0)}">
      <td>${i + 1}</td>
      <td style="white-space:nowrap;">${esc2(r.so_no || '-')}</td>
      <td style="white-space:nowrap;font-variant-numeric:tabular-nums;">${esc2(r.doc_date_raw || '-')}</td>
      <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${esc2(r.product_name || '')}">${esc2(r.product_name || '-')}${usedBadge}</td>
      <td style="text-align:right;font-variant-numeric:tabular-nums;">${fmt(r.qty || 0)} ${esc2(r.unit || '')}</td>
      <td style="text-align:right;${priceColor}">${priceDisplay}</td>
    </tr>`;
  });
  html += '</tbody></table>';
  body.innerHTML = html;
  body.querySelectorAll('.hist-row').forEach(tr => {
    tr.addEventListener('dblclick', () => {
      const idx = parseInt(tr.dataset.idx);
      const rec = records[idx];
      if (!rec || !itemRowEl) return;
      const priceEl = itemRowEl.querySelector('.price');
      if (priceEl) priceEl.value = rec.unit_price || 0;
      const unitEl = itemRowEl.querySelector('.unit');
      if (unitEl && !unitEl.value && rec.unit) unitEl.value = rec.unit;
      $('#histPopover').style.display = 'none';
      render();
    });
  });
}

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
function sigEnd() {
  sigDrawing = false;
  sellerSigData = trimSignature(sigCanvas);
  render();
}

function trimSignature(canvas) {
  const ctx = canvas.getContext('2d');
  const w = canvas.width, h = canvas.height;
  const img = ctx.getImageData(0, 0, w, h);
  const d = img.data;
  let top = h, left = w, bottom = 0, right = 0;
  for (let y = 0; y < h; y++) {
    for (let x = 0; x < w; x++) {
      const a = d[(y * w + x) * 4 + 3];
      if (a > 10) {
        if (y < top) top = y;
        if (y > bottom) bottom = y;
        if (x < left) left = x;
        if (x > right) right = x;
      }
    }
  }
  if (top > bottom) return null; // ว่างเปล่า
  const pad = 10;
  top = Math.max(0, top - pad);
  left = Math.max(0, left - pad);
  bottom = Math.min(h - 1, bottom + pad);
  right = Math.min(w - 1, right + pad);
  const tw = right - left + 1, th = bottom - top + 1;
  const tc = document.createElement('canvas');
  tc.width = tw; tc.height = th;
  tc.getContext('2d').drawImage(canvas, left, top, tw, th, 0, 0, tw, th);
  return tc.toDataURL('image/png');
}

sigCanvas.addEventListener('mousedown', sigStart);
sigCanvas.addEventListener('mousemove', sigMove);
sigCanvas.addEventListener('mouseup', sigEnd);
sigCanvas.addEventListener('mouseleave', sigEnd);
sigCanvas.addEventListener('touchstart', sigStart, { passive: false });
sigCanvas.addEventListener('touchmove', sigMove, { passive: false });
sigCanvas.addEventListener('touchend', sigEnd);
$('#sigClear').onclick = () => { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); sellerSigData = null; render(); };

const THAI_MONTHS = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

function val(id)     { return esc2($('#' + id).value || ''); }
function rawVal(id)  { return ($('#' + id).value || '').trim(); }

function fmtThaiDate() {
  return fmtThaiDateStr(rawVal('docDate'));
}

function fmtThaiDateStr(v) {
  if (!v) return '—';
  const d = new Date(v);
  if (isNaN(d)) return v;
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const yy = d.getFullYear() + 543;
  return dd + '/' + mm + '/' + yy;
}

function sellerSigHtml() {
  return sellerSigData ? `<img class="sig-img" src="${sellerSigData}">` : '';
}

function custInfoHtml() {
  const codeHtml = rawVal('custCode') ? `<span style="color:var(--navy);font-size:11px;">[${val('custCode')}]</span>` : '';
  return `
    <div class="cust-head">ข้อมูลลูกค้า ${codeHtml}</div>
    <table class="cinfo-table">
      <tr><td class="ck">ชื่อลูกค้า</td><td class="cv" colspan="2">${val('custCompany')}</td><td class="ck">ยืนราคาภายใน</td><td class="cv" colspan="3">${val('validDays')}${rawVal('validDays') ? ' วัน' : ''}</td></tr>
      <tr><td class="ck">ผู้ติดต่อ</td><td class="cv" colspan="2">${val('contactName')}</td><td class="ck">Expire Date</td><td class="cv" colspan="3">${fmtThaiDateStr($('#expireDate').dataset.raw || '')}</td></tr>
      <tr><td class="ck" rowspan="2" style="vertical-align:top">ที่อยู่</td><td class="cv" colspan="2" rowspan="2" style="vertical-align:top">${val('custAddr')}</td><td class="ck">จำนวนวันเครดิต</td><td class="cv" colspan="3">${val('creditDays')}${rawVal('creditDays') ? ' วัน' : ''}</td></tr>
      <tr><td class="ck" colspan="4"></td></tr>
      <tr><td colspan="7" style="padding:1.5px 4px;"><span class="ck" style="display:inline;">เลขประจำตัวผู้เสียภาษี</span> <span class="cv" style="display:inline;margin-right:8px;">${val('custTax')}</span><span class="ck" style="display:inline;">สาขา</span> <span class="cv" style="display:inline;">${val('custBranch')}</span></td></tr>
      <tr><td class="ck">เบอร์โทรศัพท์</td><td class="cv" colspan="6">${val('custTel')}</td></tr>
    </table>`;
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
      <div class="cfoot" style="margin-top:60px;">
        <div class="ssign">
          <div class="sg"><div class="sgline">ผู้อนุมัติซื้อ</div></div>
          <div class="sg">${sellerSigHtml()}<div class="sgline">พนักงานขาย</div></div>
          <div class="sg"><div class="sgline">ผู้จัดการฝ่ายขาย</div></div>
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
        <div class="qmeta">วันที่ ${fmtThaiDate()}${currentQuotationNo ? '<br><span style="font-size:10px;color:#666;">เลขที่ ' + esc2(currentQuotationNo) + '</span>' : ''}</div>
      </div>
    </div>
    <div class="bluebar">ใบเสนอราคา</div>
    ${isFirst ? custInfoHtml() : ''}
    <table class="itbl">
      <thead><tr>
        <th class="c" style="width:8%">ลำดับ</th>
        <th class="l">รายการสินค้า</th>
        <th class="c" style="width:10%">จำนวน</th>
        <th class="c" style="width:8%">หน่วย</th>
        <th class="r" style="width:15%">ราคา/หน่วย</th>
        <th class="r" style="width:16%">ราคารวม</th>
      </tr></thead>
      <tbody>${rows}</tbody>
    </table>
    <div class="flexspace"></div>
    ${foot}
    <div class="page-number">หน้า ${pg} / ${pageCount}</div>
  </div>`;
}

function render() {
  const items = [];
  let mainItemIndex = 0;
  
  $$('#itemRows .item').forEach((tr) => {
    if (tr.classList.contains('sub-item')) return; // skip old sub-item rows (ไม่มีแล้ว)
    mainItemIndex++;
    const desc  = tr.querySelector('.desc').value;
    const qty   = parseFloat(tr.querySelector('.qty').value) || 0;
    const unit  = tr.querySelector('.unit').value;
    const price = parseFloat(tr.querySelector('.price').value) || 0;
    const subTa = tr.querySelector('.sub-detail');
    const subDetail = subTa ? subTa.value.trim() : '';
    const numCell = tr.querySelector('.td-num');
    if (numCell) {
      numCell.textContent = mainItemIndex;
      const hasHist = tr.dataset.keyword || tr.dataset.itemNew;
      if (!hasHist) numCell.classList.add('no-hist'); else numCell.classList.remove('no-hist');
    }
    const amtCell = tr.querySelector('.td-amt');
    if (amtCell) amtCell.textContent = fmt(qty * price);
    if (!desc && !price) return;
    items.push({ desc, qty, unit, price, isSub: false, index: mainItemIndex, subDetail });
  });
  
  // คำนวณยอดรวม
  const gross = items.reduce((s, it) => s + it.qty * it.price, 0);
  const vat   = gross * 0.07;
  const grand = gross + vat;
  const T     = { gross, vat, grand };
  const foot = $('#itemFoot');
  if (items.length) { foot.style.display = ''; $('#footGross').textContent = fmt(gross); }
  else foot.style.display = 'none';
  
  // คำนวณ weight: item = 1 แถว, sub-detail แต่ละบรรทัด = 1 แถว
  const itemWeights = items.map(it => {
    const sub = it.subDetail ? it.subDetail.split('\n').filter(l => l.trim()).length : 0;
    return 1 + sub;
  });

  // แบ่งหน้าแบบ Word: ค่าคงที่เดิม แต่นับ sub-detail เป็นแถวเพิ่ม
  function buildPages() {
    const totalW = itemWeights.reduce((s, w) => s + w, 0);
    if (totalW <= FIRST_LAST_PAGE_MAX) return [items.length];
    const pages = [];
    let idx = 0;
    while (idx < items.length) {
      const isFirst = pages.length === 0;
      const remW = itemWeights.slice(idx).reduce((s, w) => s + w, 0);
      // ดูว่าหน้านี้เป็นหน้าสุดท้ายไหม
      const maxFull = isFirst ? FIRST_PAGE_MAX : OTHER_PAGE_MAX;
      const maxLast = isFirst ? FIRST_LAST_PAGE_MAX : OTHER_LAST_PAGE_MAX;
      // ลองใส่ให้เต็ม
      let w = 0, cnt = 0;
      while (idx + cnt < items.length && w + itemWeights[idx + cnt] <= maxFull) { w += itemWeights[idx + cnt]; cnt++; }
      // ถ้าใส่ทั้งหมดที่เหลือพอดี ใช้ maxLast แทน
      if (idx + cnt >= items.length) {
        w = 0; cnt = 0;
        while (idx + cnt < items.length && w + itemWeights[idx + cnt] <= maxLast) { w += itemWeights[idx + cnt]; cnt++; }
      }
      if (cnt === 0) cnt = 1; // อย่างน้อย 1 item
      pages.push(cnt);
      idx += cnt;
    }
    return pages;
  }

  const sizes     = buildPages();
  const pageCount = sizes.length;
  let out = '', offset = 0;
  for (let pg = 0; pg < pageCount; pg++) {
    const count  = sizes[pg];
    const slice  = items.slice(offset, offset + count);
    const isLast = pg === pageCount - 1;
    let rows = '';
slice.forEach((it, idx) => {
  const subLines = it.subDetail ? it.subDetail.split('\n').filter(l => l.trim()).map(l => `<div style="font-size:11px;color:#555;padding-left:12px;line-height:1.4;word-break:break-word;white-space:normal;">${esc2(l)}</div>`).join('') : '';
  rows += `<tr>
    <td class="c">${it.index}</td>
    <td class="l" style="word-break:break-word;white-space:normal;">${esc2(it.desc || '-')}${subLines}</td>
    <td class="c">${it.qty || ''}</td>
    <td class="c">${it.unit ? esc2(it.unit) : ''}</td>
    <td class="r">${fmt(it.price || 0)}</td>
    <td class="r">${fmt(it.qty * it.price)}</td>
  </tr>`;
});
    /* pad empty rows: นับ weight ของ slice */
    const sliceW = slice.reduce((s, it, i) => s + itemWeights[offset + i], 0);
    let padTarget;
    if (pg === 0) padTarget = isLast ? FIRST_LAST_PAGE_MAX : FIRST_PAGE_MAX;
    else padTarget = isLast ? OTHER_LAST_PAGE_MAX : OTHER_PAGE_MAX;
    const pad = Math.max(0, padTarget - sliceW);
    if (isLast && pad > 0) {
      for (let e = 0; e < pad; e++) rows += `<tr class="empty-row"><td class="c"></td><td class="l"></td><td class="c"></td><td class="c"></td><td class="r"></td><td class="r"></td></tr>`;
    }
    if (!items.length) {
      rows = '';
      for (let e = 0; e < FIRST_LAST_PAGE_MAX; e++) rows += `<tr class="empty-row"><td class="c">${e===0?'1':''}</td><td class="l">${e===0?'—':''}</td><td class="c"></td><td class="c"></td><td class="r"></td><td class="r"></td></tr>`;
    }
    out += pageHtml(rows, pg + 1, pageCount, isLast, pg === 0, T);
    offset += count;
  }
  $('#pages').innerHTML = out;
  validateForm();
}

function validateForm() {
  $('#printBtn').disabled = false;
  $('#validateMsg').textContent = '';
}

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

['docDate','contactName','custCode','custCompany','custAddr','custTel',
 'custTax','custBranch','validDays','expireDate','creditDays','note']
  .forEach(id => { $('#' + id).oninput = $('#' + id).onchange = render; });

function calcExpireDate() {
  const days = parseInt($('#validDays').value) || 0;
  const baseDate = $('#docDate').value;
  if (days > 0 && baseDate) {
    const d = new Date(baseDate);
    d.setDate(d.getDate() + days);
    const raw = d.toISOString().slice(0, 10);
    $('#expireDate').dataset.raw = raw;
    $('#expireDate').value = fmtThaiDateStr(raw);
    render();
  }
}
$('#validDays').addEventListener('input', calcExpireDate);
$('#validDays').addEventListener('change', calcExpireDate);
$('#docDate').addEventListener('change', calcExpireDate);

$('#printBtn').onclick = async () => {
  const btn = $('#printBtn');
  const origText = btn.textContent;
  btn.disabled = true;
  btn.textContent = '⏳ กำลังบันทึกลงระบบ...';
  try {
    const { jsPDF } = window.jspdf || {};
    if (!jsPDF) { alert('jsPDF โหลดไม่สำเร็จ'); return; }

    // 1) รวบรวม items
    const items = [];
    $$('#itemRows .item').forEach((tr, idx) => {
      const desc  = (tr.querySelector('.desc')?.value || '').trim();
      const price = parseFloat(tr.querySelector('.price')?.value) || 0;
      if (!desc && price <= 0) return;
      const subTa = tr.querySelector('.sub-detail');
      const subDetail = subTa ? subTa.value.trim() : null;
      items.push({
        desc, price,
        qty: parseFloat(tr.querySelector('.qty')?.value) || 0,
        unit: (tr.querySelector('.unit')?.value || '').trim(),
        item_new: tr.dataset.itemNew || null,
        product_name: tr.dataset.productName || null,
        is_new: tr.dataset.isNew === '1',
        sub_detail: subDetail || null,
      });
    });

    // 2) ส่งข้อมูลไป server ก่อน (ยังไม่มี PDF) → ได้เลขใบสั่งขาย
    const res = await fetch('/soitem', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({
        doc_date: rawVal('docDate') || new Date().toISOString().slice(0,10),
        customer_code: rawVal('custCode') || '-',
        customer_company: rawVal('custCompany') || '-',
        customer_address: rawVal('custAddr') || '-',
        customer_tel: rawVal('custTel') || '-',
        customer_tax: rawVal('custTax') || '',
        customer_branch: rawVal('custBranch') || '',
        contact_name: rawVal('contactName') || '-',
        valid_days: parseInt($('#validDays').value) || 0,
        expire_date: $('#expireDate').dataset.raw || null,
        credit_days: parseInt($('#creditDays').value) || null,
        note: rawVal('note') || null,
        items: items.length ? items : [{ desc: '-', qty: 0, unit: '', price: 0 }],
        pdf_base64: null,
      }),
    });
    const result = await res.json();
    if (!res.ok || result.status === 'error') throw new Error(result.message || 'HTTP ' + res.status);

    // 3) ได้เลขแล้ว → render ใหม่ให้มีเลขในเอกสาร
    currentQuotationNo = result.quotation_no;
    render();
    await new Promise(r => setTimeout(r, 100)); // รอ DOM update

    // 4) สร้าง PDF จากเอกสารที่มีเลขแล้ว
    btn.textContent = '⏳ กำลังสร้าง PDF...';
    const pages = document.querySelectorAll('#pages .doc.page');
    if (!pages.length) { alert('ไม่มีข้อมูลใบเสนอราคา'); return; }
    const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
    const PDF_WIDTH = '640px';
    const PDF_MIN_H = '905px';
    for (let i = 0; i < pages.length; i++) {
      if (i > 0) pdf.addPage();
      const pg = pages[i];
      const _w = pg.style.maxWidth, _h = pg.style.minHeight;
      pg.style.maxWidth  = PDF_WIDTH;
      pg.style.minHeight = PDF_MIN_H;
      pg.offsetHeight;
      const canvas = await html2canvas(pg, { scale:3, useCORS:true, backgroundColor:'#ffffff', logging:false });
      pg.style.maxWidth = _w; pg.style.minHeight = _h;
      const imgData = canvas.toDataURL('image/jpeg', 0.95);
      const ratio = Math.min(210/canvas.width, 297/canvas.height);
      pdf.addImage(imgData,'JPEG',(210-canvas.width*ratio)/2, 0, canvas.width*ratio, canvas.height*ratio);
    }

    // 5) อัพเดท PDF กลับไป server
    const pdfBase64 = await blobToBase64(pdf.output('blob'));
    await fetch('/soitem/' + encodeURIComponent(result.quotation_no) + '/pdf', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ pdf_base64: pdfBase64 }),
    }).catch(() => {});

    // 6) Save PDF ลงเครื่อง
    const custName = (rawVal('custCompany') || 'QT').replace(/[^ก-๙A-Za-z0-9]/g,'_').substring(0,30);
    pdf.save(`${result.quotation_no}_${custName}.pdf`);
    btn.textContent = `✅ บันทึกแล้ว (${result.quotation_no})`;
    btn.disabled = true;
    const msg = $('#validateMsg');
    msg.style.color = '#16a34a';
    msg.textContent = `✅ ${result.message}`;
  } catch (err) {
    console.error('Save error:', err);
    alert('บันทึกไม่สำเร็จ: ' + err.message);
    btn.disabled = false;
    btn.textContent = origText;
  }
};

function blobToBase64(blob) {
  return new Promise((resolve, reject) => {
    const r = new FileReader();
    r.onload = () => resolve(r.result.split(',')[1]);
    r.onerror = reject;
    r.readAsDataURL(blob);
  });
}

$('#docDate').value = new Date().toISOString().slice(0, 10);
$('#itemRows').appendChild(itemRow({ desc: 'สินค้า/บริการ ตัวอย่าง', qty: 1, unit: 'ชิ้น', price: 1000 }));
render();

document.querySelectorAll('.po-modal-body, .po-detail-body').forEach(el => {
    let isDown = false, startX, startY, scrollLeft, scrollTop;
    el.style.cursor = 'grab';
    el.addEventListener('mousedown', e => {
        if (e.target.closest('a, button, input, select')) return;
        isDown = true;
        el.style.cursor = 'grabbing';
        el.style.userSelect = 'none';
        startX = e.pageX - el.offsetLeft;
        startY = e.pageY - el.offsetTop;
        scrollLeft = el.scrollLeft;
        scrollTop = el.scrollTop;
    });
    el.addEventListener('mouseleave', () => { isDown = false; el.style.cursor = 'grab'; el.style.userSelect = ''; });
    el.addEventListener('mouseup', () => { isDown = false; el.style.cursor = 'grab'; el.style.userSelect = ''; });
    el.addEventListener('mousemove', e => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - el.offsetLeft;
        const y = e.pageY - el.offsetTop;
        el.scrollLeft = scrollLeft - (x - startX);
        el.scrollTop = scrollTop - (y - startY);
    });
});
</script>
</body>
</html>
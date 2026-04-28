<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SolarSystem</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:100%;overflow-x:hidden}
body{font-family:'Sarabun','Noto Sans Thai',sans-serif;background:#f0f2f7;color:#1a1a1a;font-size:16px;min-height:100vh}

.topbar{background:#102273;display:flex;align-items:center;height:60px;padding:0;gap:0;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(9,9,181,.25)}
.topbar-brand{display:flex;align-items:center;gap:9px;padding:0 22px;border-right:1px solid rgba(255,255,255,.15);height:100%;flex-shrink:0}
.topbar-brand h1{color:#fff;font-size:20px;font-weight:700;letter-spacing:.4px;white-space:nowrap}
nav{display:flex;align-items:center;flex:1;height:100%;padding:0 6px;overflow-x:auto;scrollbar-width:none}
nav::-webkit-scrollbar{display:none}
.nb{display:flex;align-items:center;gap:8px;padding:0 20px;height:100%;font-size:15px;border:none;background:none;cursor:pointer;color:rgba(255,255,255,.65);white-space:nowrap;transition:all .18s;font-family:inherit;position:relative;border-bottom:3px solid transparent;font-weight:600}
.nb svg{width:18px;height:18px;opacity:.7;flex-shrink:0}
.nb:hover{color:#fff;background:rgba(255,255,255,.1)}
.nb.on{color:#fff;border-bottom-color:#7dd3fc;background:rgba(255,255,255,.12)}
.nb-link{color:#fff;font-weight:700;background:rgba(255,255,255,.12);border-bottom:none;text-decoration:none}
.nb-link:hover{background:rgba(255,255,255,.22);color:#fff}
.nb-link-tech{background:rgba(80,200,46,.25);color:#b9ffb0}
.nb-link-tech:hover{background:rgba(80,200,46,.38)}
.nb-link-home{background:rgba(125,211,252,.18);color:#bae6fd}
.nb-link-home:hover{background:rgba(125,211,252,.3)}
.back-group{display:flex;align-items:center;gap:0;border-left:1px solid rgba(255,255,255,.15);height:100%;flex-shrink:0}
.topbar-date{padding:0 18px;font-size:13px;color:rgba(255,255,255,.6);white-space:nowrap;flex-shrink:0;border-left:1px solid rgba(255,255,255,.15);height:100%;display:flex;align-items:center}

.main{padding:20px;width:100%;max-width:100%}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:20px}
.mc{background:#fff;border-radius:10px;padding:16px 18px;border:1px solid #e8e8e5;min-width:0}
.ml{font-size:13px;color:#888;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mv{font-size:30px;font-weight:700;line-height:1.1}
.ms{font-size:13px;color:#aaa;margin-top:4px}

.card{background:#fff;border-radius:10px;border:1px solid #e5e5e5;overflow:hidden;margin-bottom:18px}
.ct{padding:14px 18px;font-size:15px;font-weight:700;color:#333;border-bottom:1px solid #e0e0dd;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.ct-title{flex:1;min-width:200px}
.ct-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

.ov{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:15px;table-layout:auto}
th{padding:11px 13px;text-align:left;background:#0f2fbd;color:#fff;font-weight:700;border-bottom:2px solid #0a1f8a;white-space:nowrap;font-size:14px;vertical-align:middle;letter-spacing:.2px}
td{padding:10px 13px;border-bottom:1px solid #ebebea;color:#1a1a1a;vertical-align:middle;font-size:15px;word-break:break-word;line-height:1.45}
tbody tr:nth-child(even) td{background:#f8f9fc}
tbody tr:nth-child(odd) td{background:#fff}
tbody tr:hover td{background:#eef2ff !important}
tr:last-child td{border-bottom:none}
th.th-wash-next{background:#D97706 !important;border-bottom-color:#B45309 !important}

.cell-center{text-align:center}.cell-right{text-align:right}.cell-nowrap{white-space:nowrap}
.cell-id{color:#bbb;font-size:13px;text-align:center;width:44px;font-weight:600}
.cell-compact{font-size:14px;color:#444;line-height:1.5}
.cell-muted{font-size:13px;color:#aaa;margin-top:2px}
.cell-name-main{font-size:15px;font-weight:700;color:#0909b5;cursor:pointer;background:none;border:none;padding:0;text-align:left;font-family:inherit;line-height:1.4;text-decoration:none}
.cell-name-main:hover{text-decoration:underline;color:#0416ba}

.col-id{width:44px}.col-size{width:80px}.col-loc{width:100px}.col-status{width:140px}
.col-super{width:80px}.col-contact{width:160px}.col-action{width:130px}.col-wash-date{width:115px}

.wash-next-cell{background:#fef3c7 !important;color:#92400e;font-weight:700;text-align:center;font-size:14px}
.wash-next-cell.empty{color:#d1d5db;font-weight:400;background:#f9fafb !important}
tbody tr:hover .wash-next-cell{background:#fde68a !important}

.badge{display:inline-block;padding:3px 10px;border-radius:5px;font-size:13px;font-weight:700;letter-spacing:.2px;white-space:nowrap}
.b-warn{background:#fdcb27;color:#7a4a00}.b-due{background:#f52222;color:#fff}
.b-info{background:#1D4ED8;color:#fff}.b-purple{background:#6c10ff;color:#fff}
.b-navy{background:#0f2d6e;color:#e8f0ff}.b-wash-ok{background:#00c944;color:#fff}
.b-quote{background:#8B5CF6;color:#fff}.b-installing{background:#0891B2;color:#fff}
.b-closed{background:#059669;color:#fff}.b-success{background:#047857;color:#fff}
.b-none{background:#6B7280;color:#fff}.b-other{background:#64748B;color:#fff}
.b-wash-next{background:#F59E0B;color:#fff}

.btn-sm{padding:4px 10px;font-size:13px;border:1px solid #e0e0dd;border-radius:5px;cursor:pointer;background:#fff;color:#555;transition:all .15s;font-family:inherit}
.btn-sm:hover{background:#f0f0ee}
.btn-add{padding:8px 16px;font-size:14px;border:none;border-radius:7px;cursor:pointer;background:#0909b5;color:#fff;font-weight:600;font-family:inherit;white-space:nowrap}
.btn-add:hover{background:#0416ba}
.btn-edit{padding:5px 12px;font-size:13px;border:1px solid #0909b5;border-radius:5px;cursor:pointer;background:#fff;color:#0909b5;font-family:inherit}
.btn-edit:hover{background:#eef}
.btn-del{padding:5px 12px;font-size:13px;border:1px solid #f52222;border-radius:5px;cursor:pointer;background:#fff;color:#f52222;font-family:inherit}
.btn-del:hover{background:#fff0f0}
.btn-save{padding:10px 22px;font-size:15px;border:none;border-radius:7px;cursor:pointer;background:#0909b5;color:#fff;font-weight:600;font-family:inherit}
.btn-cancel{padding:10px 20px;font-size:15px;border:1px solid #ccc;border-radius:7px;cursor:pointer;background:#fff;color:#555;font-family:inherit}
.btn-map{padding:5px 12px;font-size:13px;border:1px solid #059669;border-radius:5px;cursor:pointer;background:#fff;color:#059669;white-space:nowrap;font-family:inherit}
.btn-map:hover{background:#f0fff8}

input[type=text],input[type=date],input[type=number],select,textarea{padding:9px 13px;border:1px solid #d0d0cc;border-radius:7px;font-size:15px;outline:none;font-family:inherit;transition:border .2s;width:100%;background:#fff}
input[type=text]:focus,input[type=date]:focus,input[type=number]:focus,select:focus,textarea:focus{border-color:#0909b5}
textarea{resize:vertical;min-height:60px}
.search-inp{padding:7px 12px;border:1px solid #e0e0dd;border-radius:8px;font-size:14px;outline:none;width:240px;max-width:100%;font-family:inherit}
.search-inp:focus{border-color:#0909b5}

.two-col{display:grid;grid-template-columns:1fr 1fr;gap:18px}

.gr{display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:14px}
.gl{width:280px;flex-shrink:0;color:#555;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:14px}
.gt{flex:1;height:20px;background:#f0f0ee;border-radius:4px;position:relative;min-width:80px}
.gb{position:absolute;height:100%;border-radius:4px;display:flex;align-items:center;padding-left:5px}
.gd{font-size:12px;color:rgba(255,255,255,.9);white-space:nowrap}
.gn{width:34px;font-size:13px;color:#888;text-align:right;flex-shrink:0}
.legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:12px}
.le{font-size:14px;color:#666;display:flex;align-items:center;gap:5px}
.lc{width:13px;height:13px;border-radius:2px;display:inline-block}

.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;padding:12px}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:12px;padding:26px;width:640px;max-width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.modal-lg{width:900px}
.modal-title{font-size:19px;font-weight:800;margin-bottom:20px;color:#1a1a1a;border-bottom:2px solid #0909b5;padding-bottom:10px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid.one{grid-template-columns:1fr}
.fg{display:flex;flex-direction:column;gap:5px}
.fg label{font-size:14px;color:#555;font-weight:700}
.modal-actions{display:flex;gap:9px;justify-content:flex-end;margin-top:22px;padding-top:18px;border-top:1px solid #f0f0ee;align-items:center;flex-wrap:wrap}

.detail-section{margin-bottom:18px}
.detail-section h4{font-size:15px;font-weight:800;color:#0909b5;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px}
.detail-row{display:flex;gap:12px;font-size:15px;margin-bottom:8px;flex-wrap:wrap}
.detail-label{color:#888;min-width:130px;flex-shrink:0;font-size:14px}
.detail-value{color:#1a1a1a;font-weight:600;flex:1;min-width:160px;word-break:break-word}

.wash-history-table{width:100%;border-collapse:collapse;font-size:14px;margin-top:8px}
.wash-history-table th{background:#f0f2f7;color:#333;padding:10px 13px;text-align:left;font-size:13px;border-bottom:2px solid #e0e0dd}
.wash-history-table td{padding:10px 13px;border-bottom:1px solid #f0f0ee;font-size:14px}
.wash-history-table tr:last-child td{border-bottom:none}
.wash-num-circle{width:28px;height:28px;background:#0909b5;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800}
.wash-add-btn{display:flex;align-items:center;gap:6px;padding:7px 16px;background:#f0f7ff;border:1px dashed #0909b5;border-radius:7px;color:#0909b5;cursor:pointer;font-size:14px;font-family:inherit;font-weight:600;margin-top:10px}
.wash-add-btn:hover{background:#e0efff}

.status-sel{font-size:14px;padding:5px 10px;border-radius:6px;border:1px solid #ddd;cursor:pointer;font-family:inherit;background:#fff;width:100%;max-width:160px}

.confirm-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;align-items:center;justify-content:center;padding:12px}
.confirm-overlay.open{display:flex}
.confirm-box{background:#fff;border-radius:10px;padding:26px;width:380px;max-width:100%;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,.2)}
.confirm-box h3{font-size:18px;margin-bottom:10px;font-weight:800}
.confirm-box p{font-size:15px;color:#666;margin-bottom:20px}
.confirm-actions{display:flex;gap:9px;justify-content:center}
.btn-danger{padding:10px 24px;border:none;border-radius:7px;background:#f52222;color:#fff;cursor:pointer;font-weight:700;font-size:15px;font-family:inherit}

.map-modal{background:#fff;border-radius:12px;padding:0;width:700px;max-width:100%;max-height:90vh;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.map-header{padding:14px 18px;background:#0f2fbd;color:#fff;font-size:16px;font-weight:800;display:flex;align-items:center;justify-content:space-between;gap:10px}
.map-close{background:none;border:none;color:#fff;cursor:pointer;font-size:22px;line-height:1}
iframe#map-frame{width:100%;height:420px;border:none;display:block}

.toast{position:fixed;top:80px;right:20px;background:#047857;color:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.18);font-weight:600;z-index:5000;display:none;font-size:14px}
.toast.error{background:#dc2626}
.toast.show{display:block;animation:slideIn .3s ease}
@keyframes slideIn{from{transform:translateX(40px);opacity:0}to{transform:translateX(0);opacity:1}}

@media (max-width:1100px){.two-col{grid-template-columns:1fr}.gl{width:220px}}
@media (max-width:768px){
  body{font-size:15px}.main{padding:14px}.topbar-brand{padding:0 14px}.topbar-brand h1{font-size:17px}
  .topbar-date{display:none}.nb{padding:0 14px;font-size:14px}.nb span{display:none}
  .mv{font-size:24px}.ml{font-size:12px}.ct{padding:12px 14px;font-size:14px}
  .ct-actions{width:100%;justify-content:space-between}.search-inp{flex:1;width:auto;min-width:140px}
  th,td{padding:10px 11px;font-size:14px}th{font-size:13px}.modal{padding:20px}
  .form-grid{grid-template-columns:1fr}.gl{width:160px;font-size:13px}.detail-label{min-width:100px}
  .metrics{grid-template-columns:repeat(2,1fr);gap:10px}.mc{padding:12px 14px}
}
@media (max-width:480px){
  .metrics{grid-template-columns:1fr 1fr}.mv{font-size:22px}.topbar-brand h1{font-size:15px}
  .nb{padding:0 10px}.btn-add{padding:7px 12px;font-size:13px}
  .ct-actions{flex-direction:column;align-items:stretch}.search-inp{width:100%}
}
</style>
</head>
<body>
<div class="topbar">
  <div class="topbar-brand">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
    <h1>SolarSystem</h1>
  </div>
  <nav id="nav"></nav>
  <div class="back-group" id="back-group"></div>
  <span class="topbar-date" id="today-label"></span>
</div>
<div class="main" id="main"></div>

<div id="toast" class="toast"></div>

<!-- Modal: Customer Detail -->
<div class="modal-overlay" id="modal-detail">
  <div class="modal modal-lg">
    <div class="modal-title" id="detail-title">รายละเอียดลูกค้า</div>
    <div id="detail-body"></div>
    <div class="modal-actions">
      <button class="btn-del" onclick="askDeleteFromDetail()">🗑 ลบ</button>
      <button class="btn-edit" onclick="editFromDetail()">✏️ แก้ไข</button>
      <div style="flex:1"></div>
      <button class="btn-cancel" onclick="closeModal('modal-detail')">ปิด</button>
      <button class="btn-save" onclick="saveDetail()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Customer CRUD -->
<div class="modal-overlay" id="modal-wash">
  <div class="modal modal-lg">
    <div class="modal-title" id="wash-modal-title">เพิ่มข้อมูลลูกค้า</div>
    <div class="form-grid">
      <div class="fg"><label>ชื่อสถานที่ *</label><input type="text" id="w-name" placeholder="ชื่อสถานที่"></div>
      <div class="fg"><label>รายละเอียด/ลักษณะพื้นที่</label><input type="text" id="w-desc" placeholder="เช่น โรงงาน, หมู่บ้าน"></div>
      <div class="fg"><label>ชื่อผู้ติดต่อ</label><input type="text" id="w-contact-name" placeholder="ชื่อผู้ติดต่อ"></div>
      <div class="fg"><label>Line/โทร</label><input type="text" id="w-phone" placeholder="เบอร์โทร"></div>
      <div class="fg"><label>ขนาดติดตั้ง</label><input type="text" id="w-size" placeholder="เช่น 10kW"></div>
      <div class="fg"><label>ราคาที่เสนอ (รวม VAT)</label><input type="text" id="w-price" placeholder="เช่น 500000"></div>
      <div class="fg"><label>Latitude</label><input type="text" id="w-lat" placeholder="13.7563"></div>
      <div class="fg"><label>Longitude</label><input type="text" id="w-lng" placeholder="100.5018"></div>
      <div class="fg" style="min-width:130px;gap:5px">
        <label>🧹 รอบล้างแผง</label>
        <select id="w-cycle">
          <option value="6">6 เดือน</option>
          <option value="12">12 เดือน (1 ปี)</option>
        </select>
      </div>
      <div class="fg" style="grid-column:1/-1"><label>หมายเหตุ</label><textarea id="w-notes" placeholder="Notes..."></textarea></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-wash')">ยกเลิก</button>
      <button class="btn-save" onclick="saveWash()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Account -->
<div class="modal-overlay" id="modal-acc">
  <div class="modal modal-lg">
    <div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้</div>
    <div class="form-grid">
      <div class="fg"><label>No.</label><input type="text" id="a-no" placeholder="ลำดับ"></div>
      <div class="fg"><label>Plane</label><input type="text" id="a-plane" placeholder="Plane name"></div>
      <div class="fg"><label>Username</label><input type="text" id="a-username" placeholder="username"></div>
      <div class="fg"><label>Password</label><input type="text" id="a-password" placeholder="password"></div>
      <div class="fg" style="grid-column:1/-1"><label>E-mail</label><input type="text" id="a-email" placeholder="email"></div>
      <div class="fg" style="grid-column:1/-1"><label>App Password</label><input type="text" id="a-apppassword" placeholder="App password"></div>
      <div class="fg"><label>Customer</label><input type="text" id="a-customer" placeholder="ชื่อลูกค้า"></div>
      <div class="fg"><label>Inverter</label><input type="text" id="a-inverter" placeholder="Inverter type"></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-acc')">ยกเลิก</button>
      <button class="btn-save" onclick="saveAcc()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Add Wash Log -->
<div class="modal-overlay" id="modal-addwash">
  <div class="modal">
    <div class="modal-title" id="addwash-title">เพิ่มการล้างแผง</div>
    <div class="form-grid one">
      <div class="fg"><label>วันที่ล้าง *</label><input type="date" id="aw-date"></div>
      <div class="fg"><label>ช่างที่ล้าง *</label>
        <select id="aw-tech">
          <option value="">-- เลือกช่าง --</option>
          <option value="ทีมมิน">ทีมมิน</option>
          <option value="ทีมเมฆ">ทีมเมฆ</option>
          <option value="ทีมดา">ทีมดา</option>
          <option value="ช่างเล๊าะ">ช่างเล๊าะ</option>
          <option value="ช่างภายนอก">ช่างภายนอก</option>
          <option value="อื่นๆ">อื่นๆ</option>
        </select>
      </div>
      <div class="fg"><label>หมายเหตุ</label><input type="text" id="aw-note" placeholder="หมายเหตุ (ถ้ามี)"></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-addwash')">ยกเลิก</button>
      <button class="btn-save" onclick="saveAddWash()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Map -->
<div class="modal-overlay" id="modal-map">
  <div class="map-modal">
    <div class="map-header">
      <span id="map-title">แผนที่</span>
      <button class="map-close" onclick="closeModal('modal-map')">✕</button>
    </div>
    <iframe id="map-frame" src=""></iframe>
  </div>
</div>

<!-- Confirm Delete -->
<div class="confirm-overlay" id="confirm-del">
  <div class="confirm-box">
    <h3>ยืนยันการลบ</h3>
    <p>คุณต้องการลบรายการนี้ใช่หรือไม่?<br>การกระทำนี้ไม่สามารถยกเลิกได้</p>
    <div class="confirm-actions">
      <button class="btn-cancel" onclick="closeConfirm()">ยกเลิก</button>
      <button class="btn-danger" onclick="confirmDelete()">ลบ</button>
    </div>
  </div>
</div>

<script>
// ===========================================================
// Bootstrap data จาก Controller
// ===========================================================
const TODAY = new Date();
const CSRF  = document.querySelector('meta[name=csrf-token]').content;

const API = {
    customers     : "{{ url('solar/customers') }}",
    accounts      : "{{ url('solar/accounts') }}",
    customerStatus: (id)      => "{{ url('solar/customers') }}/" + id + "/status",
    customerNotes : (id)      => "{{ url('solar/customers') }}/" + id + "/notes",
    customerById  : (id)      => "{{ url('solar/customers') }}/" + id,
    accountById   : (id)      => "{{ url('solar/accounts') }}/" + id,
    washStore     : (id)      => "{{ url('solar/customers') }}/" + id + "/wash-logs",
    washDestroy   : (id, num) => "{{ url('solar/customers') }}/" + id + "/wash-logs/" + num,
};

let CUSTOMERS = @json($customers);
let ACCOUNTS  = @json($accounts);
const PERMIT_DOCS = @json($permitDocs);
const NOTIFY_DOCS = @json($notifyDocs);
const WORK_STEPS  = @json($workSteps);

const CAT_COLORS = {design:'#0d86ff',permit:'#05ce20',procure:'#f89306',install:'#01daa3',wire:'#3401ff',power:'#d41e06',verify:'#74d300'};
const CAT_LABELS = {design:'ออกแบบ/เอกสาร',permit:'ยื่นเอกสาร',procure:'จัดซื้อ',install:'ติดตั้ง',wire:'Wiring',power:'ขนานไฟ',verify:'ตรวจสอบ/ยื่น'};

let tab = 'dash';
let searchQMap = {dash:'', acc:''};
let editingId = null, editingType = null;
let deletingId = null, deletingType = null;
let detailId = null;
let addWashId = null;

// ===========================================================
// API helper
// ===========================================================
async function apiCall(url, opts = {}) {
    opts.headers = {
        'Content-Type'    : 'application/json',
        'Accept'          : 'application/json',
        'X-CSRF-TOKEN'    : CSRF,
        'X-Requested-With': 'XMLHttpRequest',
        ...(opts.headers || {}),
    };
    if (opts.body && typeof opts.body !== 'string') opts.body = JSON.stringify(opts.body);
    const r  = await fetch(url, opts);
    let data = null;
    try { data = await r.json(); } catch (e) {}
    if (!r.ok) {
        const msg = (data && (data.message || data.error)) || ('HTTP ' + r.status);
        throw new Error(msg);
    }
    return data;
}

function toast(msg, isError = false) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.className = 'toast' + (isError ? ' error' : '') + ' show';
    setTimeout(() => el.classList.remove('show'), 2200);
}

// ===========================================================
// UTILS
// ===========================================================
function parseD(s) { if (!s) return null; const d = new Date(s); return isNaN(d) ? null : d; }
function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
function statusBadge(s) {
    if (!s) return '<span class="badge b-none">-</span>';
    if (s === 'เสนอ') return '<span class="badge b-quote">เสนอ</span>';
    if (s === 'ปิดการขาย') return '<span class="badge b-closed">ปิดการขาย</span>';
    if (s === 'กำลังติดตั้ง') return '<span class="badge b-installing">กำลังติดตั้ง</span>';
    if (s === 'ติดตั้งสำเร็จ') return '<span class="badge b-success">ติดตั้งสำเร็จ</span>';
    return `<span class="badge b-other">${esc(s)}</span>`;
}
function fmtD(s) { const d = parseD(s); if (!d) return '-'; return d.toLocaleDateString('th-TH', {day:'numeric',month:'short',year:'2-digit'}); }
function fmtPrice(p) { if (!p) return '-'; return Number(p).toLocaleString('th-TH', {minimumFractionDigits:2,maximumFractionDigits:2}) + ' ฿'; }
function docBadge(s) {
    if (s === 'มี') return '<span class="badge b-closed">มี</span>';
    if (s === 'ยังไม่ครบ') return '<span class="badge b-warn">ยังไม่ครบ</span>';
    if (s === 'ไม่ใช้') return '<span class="badge b-none">ไม่ใช้</span>';
    return `<span class="badge b-info">${esc(s)}</span>`;
}
function kwNum(s) { const m = (s || '').match(/(\d+\.?\d*)/); return m ? parseFloat(m[1]) : 0; }
function setTab(id) { tab = id; render(); }
function locBtn(r) {
    const loc = r.loc || '';
    if (!loc || loc.startsWith('http')) return '<span style="color:#ccc">-</span>';
    const m = loc.match(/(-?\d+\.?\d*),\s*(-?\d+\.?\d*)/);
    if (!m) return '<span style="color:#ccc">-</span>';
    return `<button class="btn-map" onclick="openMap('${esc(r.name || '').replace(/'/g, "\\'")}','${m[1]}','${m[2]}')">📍 แผนที่</button>`;
}
function openMap(name, lat, lng) {
    document.getElementById('map-title').textContent = name;
    document.getElementById('map-frame').src = `https://maps.google.com/maps?q=${lat},${lng}&z=16&output=embed`;
    openModal('modal-map');
}

function replaceCust(updated) {
    const idx = CUSTOMERS.findIndex(x => x.id === updated.id);
    if (idx >= 0) CUSTOMERS[idx] = updated; else CUSTOMERS.push(updated);
}

// ===========================================================
// MODALS
// ===========================================================
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    if (id === 'modal-map') document.getElementById('map-frame').src = '';
    if (id === 'modal-wash' || id === 'modal-acc') { editingId = null; editingType = null; }
}
function closeConfirm() { document.getElementById('confirm-del').classList.remove('open'); deletingId = null; deletingType = null; }
function askDelete(type, id) { deletingType = type; deletingId = id; document.getElementById('confirm-del').classList.add('open'); }

async function confirmDelete() {
    try {
        if (deletingType === 'cust') {
            await apiCall(API.customerById(deletingId), {method:'DELETE'});
            CUSTOMERS = CUSTOMERS.filter(x => x.id !== deletingId);
            toast('ลบลูกค้าเรียบร้อย');
        } else if (deletingType === 'acc') {
            await apiCall(API.accountById(deletingId), {method:'DELETE'});
            ACCOUNTS = ACCOUNTS.filter(x => x.id !== deletingId);
            toast('ลบบัญชีเรียบร้อย');
        } else if (deletingType === 'washlog') {
            const data = await apiCall(API.washDestroy(detailId, deletingId), {method:'DELETE'});
            replaceCust(data.customer);
            closeConfirm();
            openDetail(detailId);
            toast('ลบประวัติการล้างเรียบร้อย');
            return;
        }
        closeConfirm();
        renderMain();
    } catch (e) {
        toast(e.message, true);
        closeConfirm();
    }
}

// ===========================================================
// DETAIL POPUP
// ===========================================================
function openDetail(id) {
    const r = CUSTOMERS.find(x => x.id === id);
    if (!r) return;
    detailId = id;
    const wl = Array.isArray(r.wash_logs) ? r.wash_logs : [];
    document.getElementById('detail-title').textContent = r.name;

    const isInstalled = r.status === 'ติดตั้งสำเร็จ';
    const washLog = [...wl].sort((a,b) => a.num - b.num);

    let washSection = '';
    if (isInstalled) {
        const washRows = washLog.length === 0
            ? '<tr><td colspan="5" style="text-align:center;color:#aaa;padding:18px">ยังไม่มีประวัติการล้างแผง</td></tr>'
            : washLog.map(w => `<tr>
                <td style="text-align:center;width:50px"><span class="wash-num-circle">${w.num}</span></td>
                <td style="width:130px">${fmtD(w.date)}</td>
                <td style="font-weight:600">${esc(w.tech || '-')}</td>
                <td style="color:#555">${esc(w.note || '-')}</td>
                <td style="width:70px;text-align:right"><button class="btn-del" onclick="askDelete('washlog',${w.num})">ลบ</button></td>
            </tr>`).join('');
        washSection = `
            <div class="detail-section">
                <h4>ประวัติการล้างแผง (รวม ${washLog.length} ครั้ง) — รอบ ${r.wash_cycle || 6} เดือน</h4>
                <div style="overflow-x:auto">
                    <table class="wash-history-table">
                        <thead><tr>
                            <th style="text-align:center;width:50px">#</th>
                            <th style="width:130px">วันที่ล้าง</th>
                            <th>ช่างที่ล้าง</th>
                            <th>หมายเหตุ</th>
                            <th style="width:70px"></th>
                        </tr></thead>
                        <tbody>${washRows}</tbody>
                    </table>
                </div>
                <button class="wash-add-btn" onclick="openAddWashLog(${r.id})">+ เพิ่มการล้างแผง</button>
            </div>`;
    }

    document.getElementById('detail-body').innerHTML = `
        <div class="detail-section">
            <h4>ข้อมูลทั่วไป</h4>
            <div class="detail-row"><span class="detail-label">ชื่อสถานที่</span><span class="detail-value">${esc(r.name)}</span></div>
            <div class="detail-row"><span class="detail-label">รายละเอียด</span><span class="detail-value">${esc(r.desc || '-')}</span></div>
            <div class="detail-row"><span class="detail-label">ขนาดติดตั้ง</span><span class="detail-value">${esc(r.size || '-')}</span></div>
            <div class="detail-row"><span class="detail-label">ราคาที่เสนอ</span><span class="detail-value">${fmtPrice(r.price)}</span></div>
            <div class="detail-row"><span class="detail-label">สถานะ</span><span class="detail-value">${statusBadge(r.status)}</span></div>
            <div class="detail-row"><span class="detail-label">Location</span><span class="detail-value">${esc(r.loc || '-')}</span></div>
            ${isInstalled ? `<div class="detail-row"><span class="detail-label">🟠 ล้างครั้งหน้า</span><span class="detail-value">${r.wash_next ? `<span class="badge b-wash-next">${fmtD(r.wash_next)}</span>` : '<span style="color:#ccc">-</span>'}</span></div>` : ''}
        </div>
        <div class="detail-section">
            <h4>ข้อมูลติดต่อ</h4>
            <div class="detail-row"><span class="detail-label">ชื่อผู้ติดต่อ</span><span class="detail-value">${esc(r.contact_name || '-')}</span></div>
            <div class="detail-row"><span class="detail-label">Line/โทร</span><span class="detail-value">${esc(r.phone || '-')}</span></div>
            <div class="detail-row"><span class="detail-label">ผู้ดูแล</span><span class="detail-value">${esc(r.supervisor || '-')}</span></div>
        </div>
        ${washSection}
        <div class="detail-section">
            <h4>หมายเหตุ / Notes</h4>
            <textarea id="detail-notes" style="width:100%;min-height:80px;font-size:15px">${esc(r.notes || '')}</textarea>
        </div>`;
    openModal('modal-detail');
}

async function saveDetail() {
    const r = CUSTOMERS.find(x => x.id === detailId);
    if (!r) return;
    if (r.is_extra) { toast('ไม่สามารถแก้ไขระบบเก่า', true); closeModal('modal-detail'); return; }
    const notes = document.getElementById('detail-notes').value;
    try {
        const data = await apiCall(API.customerNotes(detailId), {method:'PATCH', body:{notes}});
        replaceCust(data.customer);
        closeModal('modal-detail'); renderMain();
        toast('บันทึกหมายเหตุแล้ว');
    } catch (e) { toast(e.message, true); }
}

function editFromDetail() {
    if (detailId == null) return;
    const c = CUSTOMERS.find(x => x.id === detailId);
    if (!c || c.is_extra) { alert('ระบบเก่าแก้ไขไม่ได้'); return; }
    closeModal('modal-detail'); openEditCust(detailId);
}
function askDeleteFromDetail() {
    if (detailId == null) return;
    const c = CUSTOMERS.find(x => x.id === detailId);
    if (!c || c.is_extra) { alert('ไม่สามารถลบระบบเก่าได้'); return; }
    const delId = detailId; closeModal('modal-detail'); askDelete('cust', delId);
}

// ===========================================================
// WASH LOG
// ===========================================================
function openAddWashLog(custId) {
    addWashId = custId;
    document.getElementById('addwash-title').textContent = 'เพิ่มการล้างแผง';
    document.getElementById('aw-date').value = '';
    document.getElementById('aw-tech').value = '';
    document.getElementById('aw-note').value = '';
    openModal('modal-addwash');
}

async function saveAddWash() {
    const dt   = document.getElementById('aw-date').value;
    const tech = document.getElementById('aw-tech').value;
    const note = document.getElementById('aw-note').value.trim();
    if (!dt)   { alert('กรุณาเลือกวันที่ล้าง'); return; }
    if (!tech) { alert('กรุณาเลือกช่างที่ล้าง'); return; }
    try {
        const data = await apiCall(API.washStore(addWashId), {
            method:'POST',
            body:{wash_date: dt, tech, note},
        });
        replaceCust(data.customer);
        closeModal('modal-addwash');
        openDetail(addWashId);
        toast('บันทึกการล้างเรียบร้อย');
    } catch (e) { toast(e.message, true); }
}

// ===========================================================
// CUSTOMER CRUD
// ===========================================================
function openAddCust() {
    editingId = null; editingType = 'cust';
    document.getElementById('wash-modal-title').textContent = 'เพิ่มข้อมูลลูกค้า';
    ['w-name','w-desc','w-contact-name','w-phone','w-size','w-price','w-notes','w-lat','w-lng'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    document.getElementById('w-cycle').value = '6';
    openModal('modal-wash');
}

function openEditCust(id) {
    const r = CUSTOMERS.find(x => x.id === id); if (!r) return;
    editingId = id; editingType = 'cust';
    document.getElementById('wash-modal-title').textContent = 'แก้ไขข้อมูลลูกค้า';
    document.getElementById('w-name').value         = r.name || '';
    document.getElementById('w-desc').value         = r.desc || '';
    document.getElementById('w-contact-name').value = r.contact_name || '';
    document.getElementById('w-phone').value        = r.phone || '';
    document.getElementById('w-size').value         = r.size || '';
    document.getElementById('w-price').value        = r.price || '';
    document.getElementById('w-notes').value        = r.notes || '';
    document.getElementById('w-cycle').value        = String(r.wash_cycle || 6);
    const m = (r.loc || '').match(/(-?\d+\.?\d*),\s*(-?\d+\.?\d*)/);
    document.getElementById('w-lat').value = m ? m[1] : '';
    document.getElementById('w-lng').value = m ? m[2] : '';
    openModal('modal-wash');
}

async function saveWash() {
    const name = document.getElementById('w-name').value.trim();
    if (!name) { alert('กรุณากรอกชื่อสถานที่'); return; }
    const lat = document.getElementById('w-lat').value.trim();
    const lng = document.getElementById('w-lng').value.trim();
    const loc = (lat && lng) ? lat + ', ' + lng : '';
    const cycle = parseInt(document.getElementById('w-cycle').value) || 6;
    const obj = {
        name,
        desc        : document.getElementById('w-desc').value.trim(),
        contact_name: document.getElementById('w-contact-name').value.trim(),
        phone       : document.getElementById('w-phone').value.trim(),
        size        : document.getElementById('w-size').value.trim(),
        price       : parseFloat(document.getElementById('w-price').value) || null,
        notes       : document.getElementById('w-notes').value.trim(),
        loc,
        wash_cycle  : cycle,
    };
    try {
        let data;
        if (editingId) {
            data = await apiCall(API.customerById(editingId), {method:'PUT', body:obj});
        } else {
            data = await apiCall(API.customers, {method:'POST', body:obj});
        }
        const fresh = await apiCall(API.customerById(data.customer.id));
        replaceCust(fresh);
        closeModal('modal-wash'); renderMain();
        toast(editingId ? 'บันทึกการแก้ไขเรียบร้อย' : 'เพิ่มลูกค้าเรียบร้อย');
    } catch (e) { toast(e.message, true); }
}

// ===========================================================
// ACCOUNT CRUD
// ===========================================================
function openAddAcc() {
    editingId = null; editingType = 'acc';
    document.getElementById('acc-modal-title').textContent = 'เพิ่มบัญชีผู้ใช้';
    ['a-no','a-plane','a-username','a-password','a-email','a-apppassword','a-customer','a-inverter']
        .forEach(id => document.getElementById(id).value = '');
    openModal('modal-acc');
}

function openEditAcc(id) {
    const r = ACCOUNTS.find(x => x.id === id); if (!r) return;
    editingId = id; editingType = 'acc';
    document.getElementById('acc-modal-title').textContent = 'แก้ไขบัญชีผู้ใช้';
    document.getElementById('a-no').value          = r.no || '';
    document.getElementById('a-customer').value    = r.customer || '';
    document.getElementById('a-plane').value       = r.plane || '';
    document.getElementById('a-username').value    = r.username || '';
    document.getElementById('a-password').value    = r.password || '';
    document.getElementById('a-email').value       = r.email || '';
    document.getElementById('a-apppassword').value = r.app_password || '';
    document.getElementById('a-inverter').value    = r.inverter || '';
    openModal('modal-acc');
}

async function saveAcc() {
    const obj = {
        no          : document.getElementById('a-no').value.trim(),
        customer    : document.getElementById('a-customer').value.trim(),
        plane       : document.getElementById('a-plane').value.trim(),
        username    : document.getElementById('a-username').value.trim(),
        password    : document.getElementById('a-password').value.trim(),
        email       : document.getElementById('a-email').value.trim(),
        app_password: document.getElementById('a-apppassword').value.trim(),
        inverter    : document.getElementById('a-inverter').value.trim(),
    };
    try {
        let data;
        if (editingId) {
            data = await apiCall(API.accountById(editingId), {method:'PUT', body:obj});
            const idx = ACCOUNTS.findIndex(x => x.id === editingId);
            if (idx > -1) ACCOUNTS[idx] = data.account;
        } else {
            data = await apiCall(API.accounts, {method:'POST', body:obj});
            ACCOUNTS.push(data.account);
        }
        closeModal('modal-acc'); renderMain();
        toast(editingId ? 'บันทึกการแก้ไขเรียบร้อย' : 'เพิ่มบัญชีเรียบร้อย');
    } catch (e) { toast(e.message, true); }
}

// ===========================================================
// PASSWORD TOGGLE
// ===========================================================
function togglePwd(i) {
    const el = document.getElementById('pw' + i), bt = document.getElementById('pb' + i);
    if (el.dataset.v === '1') { el.textContent = '••••••••'; el.dataset.v = '0'; bt.textContent = 'แสดง'; }
    else { el.textContent = el.dataset.p; el.dataset.v = '1'; bt.textContent = 'ซ่อน'; }
}

// ===========================================================
// STATUS INLINE
// ===========================================================
async function updateCustStatus(id, val) {
    const r = CUSTOMERS.find(x => x.id === id); if (!r) return;
    if (r.status === 'ติดตั้งสำเร็จ') return;
    try {
        const data = await apiCall(API.customerStatus(id), {method:'PATCH', body:{status: val}});
        replaceCust(data.customer);
        renderMain();
        toast('อัปเดตสถานะแล้ว');
    } catch (e) { toast(e.message, true); }
}

// ===========================================================
// SEARCH
// ===========================================================
function handleSearch(e) {
    searchQMap[tab] = e.target.value;
    renderBodyOnly();
    requestAnimationFrame(() => {
        const s = document.getElementById('search-inp-' + tab);
        if (s) { s.focus(); const l = s.value.length; try { s.setSelectionRange(l, l); } catch (_) {} }
    });
}

function renderStatusCell(i) {
    if (i.status === 'ติดตั้งสำเร็จ')
        return '<span class="badge b-success" style="font-size:12px">&#x2705; ติดตั้งสำเร็จ</span>';
    return '<select class="status-sel" onchange="updateCustStatus(' + i.id + ',this.value)">'
        + '<option value="เสนอ"' + (i.status === 'เสนอ' ? ' selected' : '') + '>เสนอ</option>'
        + '<option value="ปิดการขาย"' + (i.status === 'ปิดการขาย' ? ' selected' : '') + '>ปิดการขาย</option>'
        + '<option value="กำลังติดตั้ง"' + (i.status === 'กำลังติดตั้ง' ? ' selected' : '') + '>กำลังติดตั้ง</option>'
        + '<option value="ติดตั้งสำเร็จ">ติดตั้งสำเร็จ</option></select>';
}

// ===========================================================
// RENDER DASHBOARD
// ===========================================================
function renderDash() {
    const q = (searchQMap.dash || '').toLowerCase();
    const visible = CUSTOMERS.filter(c => c.status !== 'ติดตั้งสำเร็จ');
    const total   = visible.length;
    const kwSum   = visible.reduce((a, b) => a + kwNum(b.size), 0);
    const cntQuote      = visible.filter(i => i.status === 'เสนอ').length;
    const cntClosed     = visible.filter(i => i.status === 'ปิดการขาย').length;
    const cntInstalling = visible.filter(i => i.status === 'กำลังติดตั้ง').length;
    const installedTotal = CUSTOMERS.filter(c => c.status === 'ติดตั้งสำเร็จ').length;

    const allList = CUSTOMERS.filter(c => !q
        || (c.name || '').toLowerCase().includes(q)
        || (c.desc || '').toLowerCase().includes(q)
        || (c.contact_name || '').toLowerCase().includes(q)
        || (c.supervisor || '').toLowerCase().includes(q));

    let num = 0;
    const rows = allList.length === 0
        ? '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:32px;font-size:15px">ไม่พบข้อมูล</td></tr>'
        : allList.map(i => {
            num++;
            return '<tr>'
                + '<td class="cell-id" style="font-size:13px;color:#999">' + num + '</td>'
                + '<td style="min-width:140px;max-width:200px">'
                + '<button class="cell-name-main" onclick="openDetail(' + i.id + ')">' + esc(i.name) + '</button>'
                + (i.desc ? '<div class="cell-muted" style="margin-top:2px;font-size:12px;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical">' + esc(i.desc) + '</div>' : '')
                + '</td>'
                + '<td class="col-contact" style="min-width:140px">'
                + '<div style="font-size:14px;color:#222;font-weight:600">' + esc(i.contact_name || '-') + '</div>'
                + (i.phone ? '<div class="cell-muted" style="font-size:12px">' + esc(i.phone) + '</div>' : '')
                + '</td>'
                + '<td class="col-size cell-center"><span class="badge b-info" style="font-size:12px">' + esc(i.size || '-') + '</span></td>'
                + '<td class="wash-next-cell col-wash-date' + (i.wash_next ? '' : ' empty') + '">' + (i.wash_next ? fmtD(i.wash_next) : '-') + '</td>'
                + '<td class="col-loc cell-center">' + locBtn(i) + '</td>'
                + '<td class="col-super cell-center" style="font-size:14px;font-weight:600;color:#374151">' + esc(i.supervisor || '-') + '</td>'
                + '<td class="col-status">' + renderStatusCell(i) + '</td>'
                + '</tr>';
        }).join('');

    return '<div class="metrics">'
        + '<div class="mc"><div class="ml">ลูกค้าที่ยังดำเนินการ</div><div class="mv">' + total + '</div><div class="ms">ราย</div></div>'
        + '<div class="mc"><div class="ml">กำลังไฟฟ้ารวม</div><div class="mv">' + kwSum.toLocaleString() + '</div><div class="ms">kW</div></div>'
        + '<div class="mc"><div class="ml">เสนอราคา</div><div class="mv" style="color:#8B5CF6">' + cntQuote + '</div><div class="ms">ราย</div></div>'
        + '<div class="mc"><div class="ml">ปิดการขาย</div><div class="mv" style="color:#059669">' + cntClosed + '</div><div class="ms">ราย</div></div>'
        + '<div class="mc"><div class="ml">กำลังติดตั้ง</div><div class="mv" style="color:#0891B2">' + cntInstalling + '</div><div class="ms">ราย</div></div>'
        + '<div class="mc"><div class="ml">ติดตั้งสำเร็จ</div><div class="mv" style="color:#047857">' + installedTotal + '</div><div class="ms">ราย</div></div>'
        + '</div>'
        + '<div class="card">'
        + '<div class="ct">'
        + '<span class="ct-title">รายชื่อลูกค้าทั้งหมด (' + allList.length + ' ราย)</span>'
        + '<div class="ct-actions">'
        + '<button class="btn-add" onclick="openAddCust()">+ เพิ่มลูกค้า</button>'
        + '<input class="search-inp" id="search-inp-dash" type="text" placeholder="&#x1F50D; ค้นหาชื่อ, ผู้ดูแล..." value="' + esc(q) + '" oninput="handleSearch(event)">'
        + '</div></div>'
        + '<div class="ov"><table>'
        + '<thead><tr>'
        + '<th class="col-id cell-center" style="width:44px">#</th>'
        + '<th style="min-width:140px">ชื่อบริษัท / สถานที่</th>'
        + '<th class="col-contact">ผู้ติดต่อ</th>'
        + '<th class="col-size cell-center">ขนาด</th>'
        + '<th class="th-wash-next col-wash-date cell-center">🟠 ล้างครั้งหน้า</th>'
        + '<th class="col-loc cell-center">Location</th>'
        + '<th class="col-super cell-center">ผู้ดูแล</th>'
        + '<th class="col-status">สถานะ</th>'
        + '</tr></thead>'
        + '<tbody>' + rows + '</tbody>'
        + '</table></div>'
        + '</div>';
}

// ===========================================================
// RENDER ACCOUNTS
// ===========================================================
function renderAcc() {
    const q = (searchQMap.acc || '').toLowerCase();
    const list = ACCOUNTS.filter(a => !q
        || (a.customer || '').toLowerCase().includes(q)
        || (a.username || '').toLowerCase().includes(q)
        || (a.plane || '').toLowerCase().includes(q)
        || (a.email || '').toLowerCase().includes(q));
    const rows = list.length === 0
        ? `<tr><td colspan="8" style="text-align:center;color:#aaa;padding:30px">ไม่พบข้อมูล</td></tr>`
        : list.map((a, i) => `<tr>
            <td class="cell-id">${esc(a.no || a.id)}</td>
            <td class="cell-compact">${esc(a.plane || '-')}</td>
            <td style="font-family:monospace;font-size:14px;word-break:break-all">${esc(a.username || '-')}</td>
            <td>${a.password && a.password !== '-' ? `<span id="pw${i}" style="font-family:monospace;color:#888;font-size:14px" data-p="${esc(a.password)}" data-v="0">••••••••</span><button class="btn-sm" id="pb${i}" onclick="togglePwd(${i})" style="margin-left:6px">แสดง</button>` : '<span style="color:#ccc">-</span>'}</td>
            <td class="cell-compact" style="word-break:break-all">${esc(a.email || '-')}</td>
            <td style="font-weight:700">${esc(a.customer || '-')}</td>
            <td class="cell-center"><span class="badge b-purple">${esc(a.inverter || '-')}</span></td>
            <td class="cell-nowrap cell-center">
                <button class="btn-edit" onclick="openEditAcc(${a.id})">แก้ไข</button>
                <button class="btn-del" onclick="askDelete('acc',${a.id})" style="margin-left:4px">ลบ</button>
            </td>
        </tr>`).join('');

    const TOTAL = 60;
    const ganttRows = WORK_STEPS.map(s => {
        const color = CAT_COLORS[s.category] || '#888';
        const lPct  = ((s.start_day - 1) / TOTAL * 100).toFixed(1);
        const wPct  = (s.duration_days / TOTAL * 100).toFixed(1);
        return `<div class="gr"><div class="gl" title="${esc(s.name)}">${s.step_no}. ${esc(s.name)}</div><div class="gt"><div class="gb" style="left:${lPct}%;width:${wPct}%;background:${color}"><span class="gd">${s.start_day}-${s.start_day + s.duration_days - 1}</span></div></div><div class="gn">${s.duration_days}d</div></div>`;
    }).join('');
    const legends = Object.entries(CAT_LABELS).map(([k, v]) =>
        `<div class="le"><span class="lc" style="background:${CAT_COLORS[k]}"></span>${v}</div>`).join('');
    const permitRows = PERMIT_DOCS.map((d, i) =>
        `<tr><td class="cell-id">${i + 1}</td><td style="line-height:1.5">${esc(d.name)}</td><td class="cell-center">${docBadge(d.status)}</td></tr>`).join('');
    const notifyRows = NOTIFY_DOCS.map((d, i) =>
        `<tr><td class="cell-id">${i + 1}</td><td style="line-height:1.5">${esc(d.name)}</td><td class="cell-center"><span class="badge b-navy">${esc(d.qty)}</span></td></tr>`).join('');

    return `
    <div class="card" style="overflow:visible">
        <div class="ct"><span class="ct-title">แผนการดำเนินการติดตั้งโซล่าเซลล์ — 60 วัน</span></div>
        <div style="padding:18px;overflow-x:auto"><div style="min-width:600px">${ganttRows}
            <div class="legend" style="margin-top:16px;padding-top:14px;border-top:1px solid #f0f0ee">${legends}</div>
        </div></div>
    </div>
    <div class="card">
        <div class="ct">
            <span class="ct-title">บัญชีผู้ใช้งานระบบ Monitoring (${list.length}/${ACCOUNTS.length})</span>
            <div class="ct-actions">
                <button class="btn-add" onclick="openAddAcc()">+ เพิ่มบัญชี</button>
                <input class="search-inp" id="search-inp-acc" type="text" placeholder="🔍 ค้นหา username, ลูกค้า, email..." value="${esc(q)}" oninput="handleSearch(event)">
            </div>
        </div>
        <div class="ov"><table>
            <thead><tr>
                <th class="col-id cell-center">No.</th><th style="min-width:120px">Plane</th>
                <th style="min-width:140px">Username</th><th style="min-width:140px">Password</th>
                <th style="min-width:180px">E-mail</th><th style="min-width:130px">Customer</th>
                <th class="cell-center" style="min-width:110px">Inverter</th><th class="col-action cell-center">จัดการ</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table></div>
    </div>
    <div class="two-col" style="align-items:start">
        <div class="card">
            <div class="ct"><span class="ct-title">เอกสารขอใบอนุญาต อ.1 (ติดต่อเขต)</span></div>
            <div class="ov"><table><thead><tr><th class="col-id cell-center">#</th><th>รายละเอียด</th><th class="cell-center" style="width:100px">สถานะ</th></tr></thead><tbody>${permitRows}</tbody></table></div>
        </div>
        <div class="card">
            <div class="ct"><span class="ct-title">เอกสารแจ้งการติดตั้งโซล่าเซลล์กับเขต</span></div>
            <div class="ov"><table><thead><tr><th class="col-id cell-center">#</th><th>รายละเอียด</th><th class="cell-center" style="width:80px">จำนวน</th></tr></thead><tbody>${notifyRows}</tbody></table></div>
        </div>
    </div>`;
}

// ===========================================================
// MAIN
// ===========================================================
const ICONS = {
    dash: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
    acc:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    tech: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    home: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
};
const TABS = [{id:'dash', label:'รายชื่อลูกค้า'}, {id:'acc', label:'บัญชีผู้ใช้งาน'}];
const RENDERS = {dash: renderDash, acc: renderAcc};

function renderNav() {
    document.getElementById('back-group').innerHTML =
        `<a href="dashboardtechnician" class="nb nb-link nb-link-tech">${ICONS.tech}<span>งานช่าง</span></a>`
        + `<a href="SOlist" class="nb nb-link nb-link-home">${ICONS.home}<span>หน้าหลัก</span></a>`;
    document.getElementById('nav').innerHTML =
        TABS.map(t => `<button class="nb${t.id === tab ? ' on' : ''}" onclick="setTab('${t.id}')">${ICONS[t.id] || ''}<span>${t.label}</span></button>`).join('');
}
function renderBodyOnly() { document.getElementById('main').innerHTML = RENDERS[tab](); }
function renderMain()     { renderBodyOnly(); }
function render()         { renderNav(); renderMain(); }

document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
});
document.getElementById('confirm-del').addEventListener('click', e => { if (e.target === e.currentTarget) closeConfirm(); });
document.getElementById('today-label').textContent = TODAY.toLocaleDateString('th-TH', {day:'numeric', month:'long', year:'numeric'});
render();
</script>
</body>
</html>
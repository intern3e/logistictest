{{-- resources/views/driver/service.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบจัดการเซอร์วิสรถ</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;--accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;--bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;--border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;--shadow:0 2px 12px rgba(26,39,68,.08);--radius:12px;--radius-sm:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.navbar{background:var(--navy);padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.nb-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:15px;text-decoration:none}
.nb-icon{width:34px;height:34px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px}
.nb-sub{font-size:10px;font-weight:300;opacity:.6;letter-spacing:1px}
.nb-menu{display:flex;align-items:center;gap:2px;flex:1;margin:0 16px}
.nb-btn{display:flex;align-items:center;gap:6px;padding:7px 13px;color:rgba(255,255,255,.7);text-decoration:none;font-family:'Sarabun',sans-serif;font-size:13px;font-weight:500;border:none;background:transparent;cursor:pointer;border-radius:7px;transition:all .2s}
.nb-btn:hover{color:#fff;background:rgba(255,255,255,.1)}
.nb-btn.active{color:#fff;background:rgba(79,142,247,.3)}
.nb-right{display:flex;align-items:center;gap:12px}
.nav-user{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);border-radius:20px;padding:4px 12px 4px 5px}
.nav-avatar{width:26px;height:26px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:11px;color:#fff}
.nav-user span{color:#fff;font-size:12px;font-weight:500}
.main{padding:24px;max-width:1400px;margin:0 auto}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.page-title{font-size:20px;font-weight:600;color:var(--navy)}
.page-subtitle{font-size:13px;color:var(--text2);margin-top:2px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:#3a7ce0}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}.btn-outline:hover{background:var(--surface2)}
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:12px 16px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:18px;box-shadow:var(--shadow)}
.filter-bar select,.filter-bar input{font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent)}
.srch-wrap{position:relative;display:flex;align-items:center}
.srch-wrap .si{position:absolute;left:9px;font-size:13px;color:var(--text3);pointer-events:none}
.srch-wrap input{padding-left:30px!important}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px}
.metric-card{background:var(--surface);border-radius:var(--radius);padding:16px 18px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.blue::before{background:var(--accent)}.metric-card.green::before{background:var(--accent2)}.metric-card.amber::before{background:var(--accent3)}.metric-card.navy::before{background:var(--navy)}
.metric-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:10px}
.metric-icon.blue{background:rgba(79,142,247,.1)}.metric-icon.green{background:rgba(56,201,138,.1)}.metric-icon.amber{background:rgba(245,166,35,.12)}.metric-icon.navy{background:rgba(26,39,68,.08)}
.metric-label{font-size:12px;color:var(--text2);font-weight:500;margin-bottom:3px}
.metric-value{font-size:24px;font-weight:700;color:var(--text);line-height:1}
.metric-sub{font-size:11px;color:var(--text3);margin-top:3px}
/* TABLE */
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:18px}
.table-header-navy{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);display:flex;align-items:center;justify-content:space-between;padding:14px 18px;gap:12px;flex-wrap:wrap;border-radius:var(--radius) var(--radius) 0 0}
.table-header-navy .table-title{color:#fff;font-size:15px;font-weight:600}
.badge-count{background:rgba(255,255,255,.2);color:#fff;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:8px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:rgba(26,58,107,.06);padding:10px 14px;text-align:left;font-weight:600;font-size:12px;color:var(--navy);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:10px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.plate-tag{background:var(--surface2);border:1px solid var(--border);border-radius:4px;font-size:11px;color:var(--text2);padding:1px 6px;font-family:monospace;font-weight:600}
/* TYPE BADGES */
.svc-badge{display:inline-flex;align-items:center;gap:3px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.svc-oil    {background:rgba(79,142,247,.12);color:#1a5fd1}
.svc-tire   {background:rgba(245,166,35,.12);color:#b45309}
.svc-brake  {background:rgba(232,93,93,.12);color:#c0392b}
.svc-engine {background:rgba(168,85,247,.12);color:#7c3aed}
.svc-ac     {background:rgba(6,182,212,.12);color:#0e7490}
.svc-battery{background:rgba(16,185,129,.12);color:#065f46}
.svc-wash   {background:rgba(56,201,138,.12);color:#1a7a4d}
.svc-glass  {background:rgba(148,163,184,.12);color:#475569}
.svc-light  {background:rgba(251,191,36,.12);color:#92400e}
.svc-other  {background:rgba(107,114,153,.12);color:#374151}
/* STATUS */
.status-done{font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:rgba(56,201,138,.12);color:#1a7a4d;white-space:nowrap}
.status-wait{font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:rgba(245,166,35,.12);color:#b45309;white-space:nowrap}
.status-prog{font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:rgba(79,142,247,.12);color:#1a5fd1;white-space:nowrap}
/* IMAGE THUMBS */
.img-thumbs{display:flex;gap:4px;align-items:center;flex-wrap:wrap}
.img-thumb{width:38px;height:38px;border-radius:6px;object-fit:cover;border:1px solid var(--border);cursor:pointer;transition:transform .15s}
.img-thumb:hover{transform:scale(1.12);z-index:2;box-shadow:0 2px 8px rgba(0,0,0,.15)}
.no-img{width:38px;height:38px;border-radius:6px;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--text3)}
.more-badge{font-size:10px;font-weight:600;color:var(--text2);background:var(--surface2);border:1px solid var(--border);border-radius:4px;padding:2px 5px}
/* ACTION BTNS */
.action-btns{display:flex;gap:6px}
.action-btn{width:28px;height:28px;border-radius:6px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .15s}
.action-btn.edit{background:rgba(79,142,247,.1);color:var(--accent)}.action-btn.edit:hover{background:var(--accent);color:#fff}
.action-btn.del{background:rgba(232,93,93,.1);color:var(--accent4)}.action-btn.del:hover{background:var(--accent4);color:#fff}
/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:600px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.modal-title{font-size:16px;font-weight:600;color:var(--text)}
.modal-close{width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:18px 22px;overflow-y:auto;flex:1}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
textarea.form-control{resize:vertical;min-height:60px}
/* IMAGE UPLOAD */
.img-upload-wrap{border:2px dashed var(--border);border-radius:var(--radius-sm);padding:20px;text-align:center;cursor:pointer;transition:all .2s;background:var(--surface2);position:relative}
.img-upload-wrap:hover{border-color:var(--accent);background:rgba(79,142,247,.04)}
.img-upload-wrap input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-top:10px}
.preview-item{position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;border:2px solid var(--border);transition:border-color .15s}
.preview-item:hover{border-color:var(--accent)}
.preview-item img{width:100%;height:100%;object-fit:cover;cursor:pointer}
.preview-item .rm{position:absolute;top:3px;right:3px;width:18px;height:18px;background:rgba(232,93,93,.9);border:none;border-radius:50%;color:#fff;font-size:9px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1}
/* LIGHTBOX */
.lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:900;align-items:center;justify-content:center;flex-direction:column}
.lightbox.open{display:flex}
.lightbox img{max-width:90vw;max-height:85vh;border-radius:8px;object-fit:contain;box-shadow:0 8px 40px rgba(0,0,0,.4)}
.lb-close{position:absolute;top:16px;right:20px;color:#fff;font-size:22px;cursor:pointer;background:rgba(255,255,255,.12);border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center}
.lb-caption{color:rgba(255,255,255,.7);font-size:12px;margin-top:10px}
.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.empty-state .icon{font-size:44px;margin-bottom:10px;opacity:.4}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}.main{padding:14px}.nb-menu{display:none}}
</style>
</head>
<body>

<nav class="navbar">
  <a class="nb-brand" href="{{ route('oil') }}">
    <div class="nb-icon">🛠️</div>
    <div><div>ระบบจัดการเซอร์วิส</div><div class="nb-sub">SERVICE MANAGEMENT</div></div>
  </a>
  <div class="nb-menu">
    <a class="nb-btn" href="{{ route('oil') }}"><span>⛽</span>ติดตามน้ำมัน</a>
    <a class="nb-btn" href="{{ route('oil') }}" onclick="event.preventDefault();history.back()"><span>📊</span>สรุปรายงาน</a>
    <a class="nb-btn active" href="{{ url('/service') }}"><span>🛠️</span>Service</a>
  </div>
</nav>

<div class="main">
  <div class="page-header">
    <div>
      <div class="page-title">ระบบจัดการเซอร์วิสรถ</div>
      <div class="page-subtitle">บันทึกและติดตามประวัติการซ่อมบำรุง</div>
    </div>
    <button class="btn btn-primary" onclick="openSvcModal()">+ เพิ่มข้อมูลเซอร์วิส</button>
  </div>

  <!-- Metrics -->
  <div class="metrics" id="metricsRow">
    <div class="metric-card blue"><div class="metric-icon blue">📋</div><div class="metric-label">รายการทั้งหมด</div><div class="metric-value" id="mTotal">0</div><div class="metric-sub">รายการ</div></div>
    <div class="metric-card green"><div class="metric-icon green">💰</div><div class="metric-label">ค่าใช้จ่ายรวม</div><div class="metric-value" id="mCost">฿0</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">📈</div><div class="metric-label">เฉลี่ย/ครั้ง</div><div class="metric-value" id="mAvg">฿0</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card navy"><div class="metric-icon navy">🚗</div><div class="metric-label">รถที่ซ่อม</div><div class="metric-value" id="mCars">0</div><div class="metric-sub">คัน</div></div>
  </div>

  <!-- Filter -->
  <div class="filter-bar">
    <div class="srch-wrap">
      <span class="si">🔍</span>
      <input type="text" id="svcSearch" placeholder="ค้นหาทะเบียน / คนขับ..." oninput="filterAndRender()" style="min-width:200px">
    </div>
    <select id="typeFilter" onchange="filterAndRender()">
      <option value="">ประเภทงานทั้งหมด</option>
      <option value="เปลี่ยนถ่ายน้ำมันเครื่อง"> เปลี่ยนถ่ายน้ำมันเครื่อง</option>
      <option value="เปลี่ยนยาง"> เปลี่ยนยาง</option>
      <option value="ตรวจ/เปลี่ยนเบรก"> ตรวจ/เปลี่ยนเบรก</option>
      <option value="ซ่อมเครื่องยนต์"> ซ่อมเครื่องยนต์</option>
      <option value="ล้าง/ซ่อมแอร์"> ล้าง/ซ่อมแอร์</option>
      <option value="เปลี่ยนแบตเตอรี่"> เปลี่ยนแบตเตอรี่</option>
      <option value="ล้างรถ">ล้างรถ</option>
      <option value="ซ่อม/เปลี่ยนกระจก"> ซ่อม/เปลี่ยนกระจก</option>
      <option value="ซ่อม/เปลี่ยนไฟ">ซ่อม/เปลี่ยนไฟ</option>
      <option value="อื่นๆ">อื่นๆ</option>
    </select>
    <select id="statusFilter" onchange="filterAndRender()">
      <option value="">สถานะทั้งหมด</option>
      <option value="เสร็จแล้ว">✅ เสร็จแล้ว</option>
      <option value="รอดำเนินการ">⏳ รอดำเนินการ</option>
      <option value="อยู่ระหว่างซ่อม">🔧 อยู่ระหว่างซ่อม</option>
    </select>
  </div>

  <!-- Table -->
  <div class="table-card">
    <div class="table-header-navy">
      <div>
        <span class="table-title">ประวัติการเซอร์วิส</span>
        <span class="badge-count" id="tblCount">0</span>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:36px">#</th>
            <th>วันที่</th>
            <th>คนขับ / ทะเบียน</th>
            <th>ประเภทงาน</th>
            <th>รายละเอียด</th>
            <th style="text-align:right">ค่าใช้จ่าย (฿)</th>
            <th>รูปภาพ</th>
            <th>สถานะ</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="svcTbody">
          <tr><td colspan="9"><div class="empty-state"><div class="icon">🛠️</div><p>ยังไม่มีรายการ กดปุ่ม "+ เพิ่มข้อมูลเซอร์วิส" เพื่อเริ่มต้น</p></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLb()">
  <button class="lb-close" onclick="closeLb()">✕</button>
  <img id="lbImg" src="" alt="" onclick="event.stopPropagation()">
  <div class="lb-caption" id="lbCaption"></div>
</div>

<!-- SERVICE MODAL -->
<div class="modal-overlay" id="svcModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="svcModalTitle">🛠️ เพิ่มข้อมูลเซอร์วิส</div>
      <button type="button" class="modal-close" onclick="closeSvcModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div>
          <label class="form-label">วันที่ *</label>
          <input type="date" id="sv-date" class="form-control">
        </div>
        <div>
          <label class="form-label">คนขับ *</label>
          <select id="sv-driver" class="form-control" onchange="toggleOther('sv-driver','sv-driver-other')">
            <option value="">— เลือกคนขับ —</option>
            <option value="บังเดช">บังเดช</option>
            <option value="แชม">แชม</option>
            <option value="กอล์ฟ">กอล์ฟ</option>
            <option value="หรั่ง">หรั่ง</option>
            <option value="เก่ง">เก่ง</option>
            <option value="เอ">เอ</option>
            <option value="ยุทร">ยุทร</option>
            <option value="แฟรงค์">แฟรงค์</option>
            <option value="เอ้">เอ้</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-driver-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุชื่อคนขับ">
        </div>
        <div>
          <label class="form-label">ทะเบียนรถ *</label>
          <select id="sv-plate" class="form-control" onchange="toggleOther('sv-plate','sv-plate-other')">
            <option value="">— เลือกทะเบียน —</option>
            <option value="1 ฉผ 1276">1 ฉผ 1276</option>
            <option value="1 ฉผ 3181">1 ฉผ 3181</option>
            <option value="1ฉผ213">1ฉผ213</option>
            <option value="2 ฉธ 1620">2 ฉธ 1620</option>
            <option value="2ฉธ1619">2ฉธ1619</option>
            <option value="3ฉมก6071">3ฉมก6071</option>
            <option value="3ฉมง3059">3ฉมง3059</option>
            <option value="City 8กค6309">City 8กค6309</option>
            <option value="City 9 กค4815">City 9 กค4815</option>
            <option value="แจ๊ส 9กธ4830">แจ๊ส 9กธ4830</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-plate-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุทะเบียน">
        </div>
        <div>
          <label class="form-label">ประเภทงาน *</label>
          <select id="sv-type" class="form-control">
            <option value="">— เลือกประเภท —</option>
            <option value="เปลี่ยนถ่ายน้ำมันเครื่อง"> เปลี่ยนถ่ายน้ำมันเครื่อง</option>
            <option value="เปลี่ยนยาง"> เปลี่ยนยาง</option>
            <option value="ตรวจ/เปลี่ยนเบรก"> ตรวจ/เปลี่ยนเบรก</option>
            <option value="ซ่อมเครื่องยนต์"> ซ่อมเครื่องยนต์</option>
            <option value="ล้าง/ซ่อมแอร์"> ล้าง/ซ่อมแอร์</option>
            <option value="เปลี่ยนแบตเตอรี่"> เปลี่ยนแบตเตอรี่</option>
            <option value="ล้างรถ"> ล้างรถ</option>
            <option value="ซ่อม/เปลี่ยนกระจก"> ซ่อม/เปลี่ยนกระจก</option>
            <option value="ซ่อม/เปลี่ยนไฟ"> ซ่อม/เปลี่ยนไฟ</option>
            <option value="อื่นๆ"> อื่นๆ</option>
          </select>
        </div>
        <div>
          <label class="form-label">ค่าใช้จ่าย (฿)</label>
          <input type="number" id="sv-cost" class="form-control" step="0.01" min="0" placeholder="0.00">
        </div>
        <div>
          <label class="form-label">สถานะ</label>
          <select id="sv-status" class="form-control">
            <option value="เสร็จแล้ว">✅ เสร็จแล้ว</option>
            <option value="รอดำเนินการ">⏳ รอดำเนินการ</option>
            <option value="อยู่ระหว่างซ่อม">🔧 อยู่ระหว่างซ่อม</option>
          </select>
        </div>
        <div class="full">
          <label class="form-label">รายละเอียด</label>
          <textarea id="sv-detail" class="form-control" rows="2" placeholder="รายละเอียดงานซ่อม..."></textarea>
        </div>
        <div class="full">
          <label class="form-label">รูปภาพ (เลือกได้หลายรูป)</label>
          <div class="img-upload-wrap" id="uploadWrap">
            <input type="file" accept="image/*" multiple onchange="addImages(this)" id="imgInput">
            <div style="font-size:28px;margin-bottom:6px">📷</div>
            <div style="font-size:13px;color:var(--text2);font-weight:500">คลิกหรือลากรูปมาวางที่นี่</div>
            <div style="font-size:11px;color:var(--text3);margin-top:3px">JPG, PNG, WEBP (ขนาดไม่เกิน 5MB ต่อรูป)</div>
          </div>
          <div class="preview-grid" id="previewGrid"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeSvcModal()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" onclick="saveSvc()">💾 บันทึก</button>
    </div>
  </div>
</div>

<script>
let records = [];
let editIdx = null;
let pendingImgs = []; // {src: base64, name: string}

const TYPE_CSS = {
  'เปลี่ยนถ่ายน้ำมันเครื่อง':'svc-oil',
  'เปลี่ยนยาง':'svc-tire',
  'ตรวจ/เปลี่ยนเบรก':'svc-brake',
  'ซ่อมเครื่องยนต์':'svc-engine',
  'ล้าง/ซ่อมแอร์':'svc-ac',
  'เปลี่ยนแบตเตอรี่':'svc-battery',
  'ล้างรถ':'svc-wash',
  'ซ่อม/เปลี่ยนกระจก':'svc-glass',
  'ซ่อม/เปลี่ยนไฟ':'svc-light',
  'อื่นๆ':'svc-other'
};

function toggleOther(selId, otherId){
  const sel = document.getElementById(selId);
  const oth = document.getElementById(otherId);
  oth.style.display = sel.value === '__other__' ? 'block' : 'none';
  if(sel.value === '__other__') oth.focus();
}

function getVal(selId, otherId){
  const sel = document.getElementById(selId);
  if(sel.value === '__other__') return document.getElementById(otherId).value.trim();
  return sel.value;
}

function todayStr(){
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

/* ---- IMAGE UPLOAD ---- */
function addImages(input){
  const files = Array.from(input.files);
  files.forEach(file => {
    if(file.size > 5*1024*1024){ alert(`${file.name} ใหญ่เกิน 5MB`); return; }
    const reader = new FileReader();
    reader.onload = e => {
      pendingImgs.push({ src: e.target.result, name: file.name });
      renderPreviewGrid();
    };
    reader.readAsDataURL(file);
  });
  input.value = '';
}

function renderPreviewGrid(){
  const grid = document.getElementById('previewGrid');
  grid.innerHTML = pendingImgs.map((img, i) => `
    <div class="preview-item">
      <img src="${img.src}" alt="${img.name}" onclick="openLb('${img.src}','${img.name}')">
      <button class="rm" onclick="removeImg(${i})" title="ลบรูป">✕</button>
    </div>`).join('');
}

function removeImg(i){
  pendingImgs.splice(i, 1);
  renderPreviewGrid();
}

/* ---- MODAL ---- */
function openSvcModal(idx = null){
  editIdx = idx;
  pendingImgs = [];
  document.getElementById('previewGrid').innerHTML = '';
  document.getElementById('svcModalTitle').textContent = idx !== null ? '🛠️ แก้ไขข้อมูลเซอร์วิส' : '🛠️ เพิ่มข้อมูลเซอร์วิส';

  if(idx !== null){
    const r = records[idx];
    document.getElementById('sv-date').value   = r.date;
    document.getElementById('sv-driver').value = r.driver;
    document.getElementById('sv-plate').value  = r.plate;
    document.getElementById('sv-type').value   = r.type;
    document.getElementById('sv-cost').value   = r.cost;
    document.getElementById('sv-status').value = r.status;
    document.getElementById('sv-detail').value = r.detail;
    pendingImgs = (r.images||[]).map(src => ({ src, name: '' }));
    renderPreviewGrid();
  } else {
    document.getElementById('sv-date').value   = todayStr();
    document.getElementById('sv-driver').value = '';
    document.getElementById('sv-plate').value  = '';
    document.getElementById('sv-type').value   = '';
    document.getElementById('sv-cost').value   = '';
    document.getElementById('sv-status').value = 'เสร็จแล้ว';
    document.getElementById('sv-detail').value = '';
    ['sv-driver-other','sv-plate-other'].forEach(id => {
      const el = document.getElementById(id);
      el.style.display = 'none';
      el.value = '';
    });
  }
  document.getElementById('svcModal').classList.add('open');
}

function closeSvcModal(){ document.getElementById('svcModal').classList.remove('open'); }

function saveSvc(){
  const date   = document.getElementById('sv-date').value;
  const driver = getVal('sv-driver','sv-driver-other');
  const plate  = getVal('sv-plate','sv-plate-other');
  const type   = document.getElementById('sv-type').value;
  const cost   = document.getElementById('sv-cost').value;
  const status = document.getElementById('sv-status').value;
  const detail = document.getElementById('sv-detail').value;

  if(!date)  { alert('กรุณาเลือกวันที่'); return; }
  if(!driver){ alert('กรุณาเลือกหรือระบุชื่อคนขับ'); return; }
  if(!plate) { alert('กรุณาเลือกหรือระบุทะเบียนรถ'); return; }
  if(!type)  { alert('กรุณาเลือกประเภทงาน'); return; }

  const record = {
    date, driver, plate, type,
    cost: cost ? parseFloat(cost) : 0,
    status, detail,
    images: pendingImgs.map(img => img.src),
    createdAt: new Date().toLocaleString('th-TH', {timeZone:'Asia/Bangkok'})
  };

  if(editIdx !== null) records[editIdx] = record;
  else records.unshift(record);

  closeSvcModal();
  filterAndRender();
}

function deleteSvc(idx){
  if(!confirm('ยืนยันการลบรายการนี้?')) return;
  records.splice(idx, 1);
  filterAndRender();
}

/* ---- FILTER & RENDER ---- */
function filterAndRender(){
  const q    = document.getElementById('svcSearch').value.toLowerCase();
  const type = document.getElementById('typeFilter').value;
  const stat = document.getElementById('statusFilter').value;

  const filtered = records.filter((r, i) => {
    const matchQ = !q || r.driver.toLowerCase().includes(q) || r.plate.toLowerCase().includes(q) || (r.detail||'').toLowerCase().includes(q);
    const matchT = !type || r.type === type;
    const matchS = !stat || r.status === stat;
    return matchQ && matchT && matchS;
  });

  // Metrics
  const total = records.length;
  const totalCost = records.reduce((s,r) => s + (r.cost||0), 0);
  const cars = new Set(records.map(r => r.plate)).size;
  document.getElementById('mTotal').textContent = total;
  document.getElementById('mCost').textContent  = '฿' + totalCost.toLocaleString();
  document.getElementById('mAvg').textContent   = total ? '฿' + Math.round(totalCost/total).toLocaleString() : '฿0';
  document.getElementById('mCars').textContent  = cars;
  document.getElementById('tblCount').textContent = filtered.length;

  const tbody = document.getElementById('svcTbody');
  if(!filtered.length){
    tbody.innerHTML = `<tr><td colspan="9"><div class="empty-state"><div class="icon">🛠️</div><p>${total ? 'ไม่พบรายการที่ตรงกับการค้นหา' : 'ยังไม่มีรายการ กดปุ่ม "+ เพิ่มข้อมูลเซอร์วิส" เพื่อเริ่มต้น'}</p></div></td></tr>`;
    return;
  }

  tbody.innerHTML = filtered.map((r, fi) => {
    const origIdx = records.indexOf(r);
    const typeCss = TYPE_CSS[r.type] || 'svc-other';
    const statusCss = r.status === 'เสร็จแล้ว' ? 'status-done' : r.status === 'รอดำเนินการ' ? 'status-wait' : 'status-prog';

    let imgHtml = '';
    if(r.images && r.images.length){
      const show = r.images.slice(0, 3);
      imgHtml = `<div class="img-thumbs">` +
        show.map((src,si) => `<img class="img-thumb" src="${src}" onclick="openLb('${src}','รูปที่ ${si+1}')" title="คลิกเพื่อดูรูปขนาดใหญ่">`).join('') +
        (r.images.length > 3 ? `<span class="more-badge">+${r.images.length - 3}</span>` : '') +
        `</div>`;
    } else {
      imgHtml = `<div class="no-img">📷</div>`;
    }

    return `<tr>
      <td style="color:var(--text3)">${fi+1}</td>
      <td style="font-size:12px;white-space:nowrap">${r.date}</td>
      <td>
        <strong style="color:var(--navy)">${r.driver}</strong>
        <div><span class="plate-tag">${r.plate}</span></div>
      </td>
      <td><span class="svc-badge ${typeCss}">${r.type}</span></td>
      <td style="font-size:12px;color:var(--text2);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${r.detail||''}">${r.detail||'—'}</td>
      <td style="text-align:right;font-weight:600;color:var(--navy)">${r.cost ? '฿'+r.cost.toLocaleString() : '—'}</td>
      <td>${imgHtml}</td>
      <td><span class="${statusCss}">${r.status}</span></td>
      <td><div class="action-btns">
        
      </div></td>
    </tr>`;
  }).join('');
}

/* ---- LIGHTBOX ---- */
function openLb(src, caption){
  document.getElementById('lbImg').src = src;
  document.getElementById('lbCaption').textContent = caption || '';
  document.getElementById('lightbox').classList.add('open');
}
function closeLb(){ document.getElementById('lightbox').classList.remove('open'); }
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLb(); });

/* ---- INIT ---- */
document.addEventListener('DOMContentLoaded', () => {
  filterAndRender();
});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบติดตามน้ำมันรถ</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
:root{--navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;--accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;--bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;--border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;--shadow:0 2px 12px rgba(26,39,68,.08);--shadow-md:0 4px 24px rgba(26,39,68,.12);--radius:12px;--radius-sm:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.navbar{background:var(--navy);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.navbar-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:16px}
.navbar-brand .icon{width:36px;height:36px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px}
.navbar-brand .sub{font-size:11px;font-weight:300;opacity:.65;letter-spacing:1px}
.nav-date{color:rgba(255,255,255,.75);font-size:13px}
.nav-user{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border-radius:20px;padding:5px 14px 5px 6px;cursor:pointer}
.nav-avatar{width:28px;height:28px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#fff}
.nav-user span{color:#fff;font-size:13px;font-weight:500}
.layout{display:flex;min-height:calc(100vh - 60px)}
.sidebar{width:220px;background:var(--navy-dark);flex-shrink:0;padding:20px 0}
.sidebar-menu{list-style:none}
.sidebar-menu a{display:flex;align-items:center;gap:10px;padding:11px 22px;color:rgba(255,255,255,.6);text-decoration:none;font-size:14px;transition:all .2s;border-left:3px solid transparent;cursor:pointer}
.sidebar-menu a:hover{color:rgba(255,255,255,.9);background:rgba(255,255,255,.05)}
.sidebar-menu a.active{color:#fff;background:rgba(79,142,247,.15);border-left-color:var(--accent);font-weight:500}
.sidebar-menu .icon{font-size:16px;width:20px;text-align:center}
.sidebar-section{font-size:10px;font-weight:600;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:20px 22px 6px;text-transform:uppercase}
.main{flex:1;overflow-x:hidden}
.page{display:none;padding:28px}
.page.active{display:block}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-title{font-size:22px;font-weight:600;color:var(--navy)}
.page-subtitle{font-size:13px;color:var(--text2);margin-top:2px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{background:#3a7ce0;transform:translateY(-1px)}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}
.btn-outline:hover{background:var(--surface2)}
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:14px 18px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:20px;box-shadow:var(--shadow)}
.filter-bar select,.filter-bar input[type=month],.filter-bar input[type=date]{font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);cursor:pointer;outline:none;transition:border-color .2s;height:36px}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent)}
.view-tabs{display:flex;background:var(--surface2);border-radius:var(--radius-sm);padding:3px;gap:2px;border:1px solid var(--border)}
.view-tab{padding:6px 14px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;color:var(--text2);border:none;background:transparent;font-family:'Sarabun',sans-serif;transition:all .15s;white-space:nowrap}
.view-tab.active{background:var(--surface);color:var(--accent);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:22px}
.metric-card{background:var(--surface);border-radius:var(--radius);padding:18px 20px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.metric-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.blue::before{background:var(--accent)}.metric-card.green::before{background:var(--accent2)}.metric-card.amber::before{background:var(--accent3)}.metric-card.red::before{background:var(--accent4)}.metric-card.navy::before{background:var(--navy)}
.metric-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px}
.metric-icon.blue{background:rgba(79,142,247,.1)}.metric-icon.green{background:rgba(56,201,138,.1)}.metric-icon.amber{background:rgba(245,166,35,.12)}.metric-icon.red{background:rgba(232,93,93,.1)}.metric-icon.navy{background:rgba(26,39,68,.08)}
.metric-label{font-size:12px;color:var(--text2);font-weight:500;margin-bottom:4px}
.metric-value{font-size:26px;font-weight:700;color:var(--text);line-height:1}
.metric-sub{font-size:11px;color:var(--text3);margin-top:4px}
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
@media(max-width:768px){.charts-grid{grid-template-columns:1fr}}
.chart-card{background:var(--surface);border-radius:var(--radius);padding:20px;border:1px solid var(--border);box-shadow:var(--shadow)}
.chart-card.full{grid-column:1/-1}
.chart-card-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px}
.chart-card-sub{font-size:12px;color:var(--text2);margin-bottom:16px}
.legend{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px}
.legend-item{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text2)}
.legend-dot{width:10px;height:10px;border-radius:3px}
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden}
.table-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.table-title{font-size:15px;font-weight:600;color:var(--text)}
.badge-count{background:var(--accent);color:#fff;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:8px}
.search-box{display:flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:7px 12px;background:var(--surface2)}
.search-box input{border:none;background:transparent;font-family:'Sarabun',sans-serif;font-size:13px;color:var(--text);outline:none;width:180px}
.search-box input::placeholder{color:var(--text3)}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:var(--surface2);padding:10px 14px;text-align:left;font-weight:600;font-size:12px;color:var(--text2);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.table-footer{padding:11px 20px;border-top:1px solid var(--border);font-size:12px;color:var(--text3);display:flex;justify-content:space-between;align-items:center}
.badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px}
.badge-green{background:rgba(56,201,138,.12);color:#1a7a4d}.badge-amber{background:rgba(245,166,35,.12);color:#a06a00}.badge-blue{background:rgba(79,142,247,.12);color:#1a52b0}
.km-val{font-weight:700;color:var(--accent2)}
.action-btns{display:flex;gap:6px}
.action-btn{width:28px;height:28px;border-radius:6px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .15s}
.action-btn.edit{background:rgba(79,142,247,.1);color:var(--accent)}.action-btn.edit:hover{background:var(--accent);color:#fff}
.action-btn.del{background:rgba(232,93,93,.1);color:var(--accent4)}.action-btn.del:hover{background:var(--accent4);color:#fff}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:17px;font-weight:600;color:var(--text)}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.modal-close:hover{background:var(--border);color:var(--text)}
.modal-body{padding:20px 24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
textarea.form-control{resize:vertical;min-height:70px}
.form-hint{font-size:11px;color:var(--text3);margin-top:4px}
.oil-price-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;color:#fff}
.oil-price-banner .lbl{font-size:12px;opacity:.7}
.oil-price-banner .price{font-size:22px;font-weight:700}
.oil-price-banner .unit{font-size:12px;opacity:.7;margin-left:4px}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--accent2);animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.85)}}
.trips-box{background:var(--surface2);border-radius:var(--radius-sm);border:1px solid var(--border);margin:12px 0;overflow:hidden}
.trips-box-header{background:var(--navy);color:rgba(255,255,255,.85);font-size:12px;font-weight:600;padding:8px 13px;letter-spacing:.5px;text-transform:uppercase}
.trips-box-item{display:flex;justify-content:space-between;align-items:center;padding:9px 13px;font-size:13px;border-bottom:1px solid var(--border)}
.trips-box-item:last-child{border-bottom:none}
.trips-box-item .dest{color:var(--text)}
.trips-box-item .km{font-weight:600;color:var(--accent);font-size:12px}
.trips-total{background:rgba(56,201,138,.06);border-top:1px solid var(--border);padding:8px 13px;display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:var(--text)}
.calc-box{background:rgba(56,201,138,.06);border:1px solid rgba(56,201,138,.25);border-radius:var(--radius-sm);padding:14px 16px;display:none;margin-top:12px}
.calc-box .title{font-size:12px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.calc-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.calc-item .lbl{font-size:11px;color:var(--text3)}
.calc-item .val{font-size:20px;font-weight:700;color:var(--accent2)}
.calc-item .val.amber{color:var(--accent3)}
.calc-item .unit{font-size:11px;color:var(--text3)}
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .icon{font-size:48px;margin-bottom:12px;opacity:.4}
.empty-state p{font-size:14px}
.toast{position:fixed;bottom:28px;right:28px;background:var(--navy);color:#fff;padding:12px 20px;border-radius:var(--radius-sm);font-size:14px;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:999;display:none;align-items:center;gap:10px}
.toast.show{display:flex}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:1}.sidebar{display:none}.page{padding:16px}.metrics{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-brand">
    <div class="icon">⛽</div>
    <div><div>ระบบจัดการเซอร์วิส</div><div class="sub">SERVICE MANAGEMENT SYSTEM</div></div>
  </div>
  <div style="display:flex;align-items:center;gap:16px">
    <div class="nav-date" id="navDate"></div>
    <div class="nav-user"><div class="nav-avatar">A</div><span>Admin</span></div>
  </div>
</nav>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-section">หลัก</div>
    <ul class="sidebar-menu">
      <li><a onclick="showPage('fuel')" class="active" id="menu-fuel"><span class="icon">⛽</span>ติดตามน้ำมัน</a></li>
    </ul>
    <div class="sidebar-section">รายงาน</div>
    <ul class="sidebar-menu">
      <li><a onclick="showPage('report')" id="menu-report"><span class="icon">📊</span>สรุปรายงาน</a></li>
    </ul>
  </aside>
  <main class="main">
    <div class="page active" id="page-fuel">
      <div class="page-header">
        <div><div class="page-title">ระบบติดตามน้ำมันรถ</div><div class="page-subtitle">บันทึกและวิเคราะห์การใช้น้ำมันแต่ละคนขับ</div></div>
        <button class="btn btn-primary" onclick="openFuelModal()">+ เพิ่มข้อมูลน้ำมัน</button>
      </div>
      <div class="filter-bar">
        <div class="view-tabs">
          <button class="view-tab" id="tab-day" onclick="setView('day')">รายวัน</button>
          <button class="view-tab active" id="tab-month" onclick="setView('month')">รายเดือน</button>
          <button class="view-tab" id="tab-year" onclick="setView('year')">รายปี</button>
          <button class="view-tab" id="tab-all" onclick="setView('all')">ทั้งหมด</button>
        </div>
        <div id="fltDayWrap" style="display:none"><input type="date" id="fltDay" onchange="renderFuel()"></div>
        <div id="fltMonthWrap"><input type="month" id="fltMonth" value="2025-12" onchange="renderFuel()"></div>
        <select id="fltDriver" onchange="renderFuel()" style="font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px">
          <option value="all">คนขับทั้งหมด</option>
        </select>
      </div>
       <div class="table-card">
        <div class="table-header">
          <div><span class="table-title">รายการเติมน้ำมัน</span><span class="badge-count" id="fuelCount">0</span></div>
          <div class="search-box"><span style="color:var(--text3)">🔍</span><input type="text" placeholder="ค้นหา..." id="fuelSearch" oninput="renderFuelTable()"></div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>วันที่</th><th>คนขับ</th><th>ยอดเงิน (฿)</th><th>ราคา/ลิตร</th><th>จำนวนลิตร</th><th>ระยะทาง (กม.)</th><th>km/L</th><th>฿/กม.</th><th>งานที่วิ่ง</th><th>จัดการ</th></tr></thead>
            <tbody id="fuelTableBody"></tbody>
          </table>
        </div>
        <div class="table-footer"><span id="fuelTableInfo">แสดง 0 จาก 0 รายการ</span><span id="fuelUpdateTime">—</span></div>
      </div>
      <div class="metrics" id="fuelMetrics"></div>
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-card-title">ปริมาณน้ำมัน (ลิตร) แต่ละคนขับ</div>
          <div class="chart-card-sub">เปรียบเทียบการเติมน้ำมันตามช่วงเวลา</div>
          <div class="legend" id="legend1"></div>
          <div style="position:relative;width:100%;height:230px"><canvas id="chartDriver"></canvas></div>
        </div>
        <div class="chart-card">
          <div class="chart-card-title">อัตราสิ้นเปลือง km/L แต่ละคนขับ</div>
          <div class="chart-card-sub">ยิ่งสูงยิ่งประหยัดน้ำมัน</div>
          <div class="legend" id="legend2"></div>
          <div style="position:relative;width:100%;height:230px"><canvas id="chartKm"></canvas></div>
        </div>
        <div class="chart-card full">
          <div class="chart-card-title">แนวโน้มค่าใช้จ่ายน้ำมัน (บาท) รายเดือน</div>
          <div class="chart-card-sub">ภาพรวมค่าใช้จ่ายน้ำมันของทั้งทีม</div>
          <div style="position:relative;width:100%;height:180px"><canvas id="chartTrend"></canvas></div>
        </div>
      </div>
    </div>

    <div class="page" id="page-report">
      <div class="page-header"><div><div class="page-title">สรุปรายงาน</div><div class="page-subtitle">ภาพรวมค่าใช้จ่ายน้ำมันและการเซอร์วิส</div></div></div>
      <div class="metrics" id="reportMetrics"></div>
      <div class="chart-card" style="margin-bottom:16px">
        <div class="chart-card-title">ค่าใช้จ่ายน้ำมันรายคนขับ (สะสม)</div>
        <div style="position:relative;width:100%;height:260px"><canvas id="chartReport"></canvas></div>
      </div>
    </div>
  </main>
</div>

<div class="modal-overlay" id="fuelModal" onclick="if(event.target===this)closeFuelModal()">
  <div class="modal">
    <div class="modal-header"><div class="modal-title" id="fuelModalTitle">เพิ่มข้อมูลเติมน้ำมัน</div><button class="modal-close" onclick="closeFuelModal()">✕</button></div>
    <div class="modal-body">
      <div class="oil-price-banner">
        <div><div class="lbl">ราคาน้ำมันดีเซล B7 (ปัจจุบัน)</div>
          <div style="display:flex;align-items:baseline;gap:4px"><span class="price" id="oilPriceShow">40.74</span><span class="unit">บาท/ลิตร</span></div></div>
        <div style="display:flex;align-items:center;gap:6px"><div class="live-dot"></div><span style="font-size:12px;opacity:.7">Live</span></div>
      </div>
      <div class="form-grid">
        <div><label class="form-label">วันที่เติม *</label><input type="date" class="form-control" id="f-date" onchange="onDateDriverChange()"></div>
        <div><label class="form-label">คนขับ *</label>
          <select class="form-control" id="f-driver" onchange="onDateDriverChange()">
            <option value="">— เลือกคนขับ —</option>
            <option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option>
            <option>หรั่ง</option><option>เก่ง</option><option>เอ</option>
            <option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option>
          </select></div>
        <div><label class="form-label">ราคาน้ำมัน (฿/ลิตร) *</label><input type="number" class="form-control" id="f-price" step="0.01" placeholder="40.74" oninput="calcEffi()"></div>
        <div><label class="form-label">ยอดเงินที่เติม (฿) *</label><input type="number" class="form-control" id="f-amount" step="1" placeholder="เช่น 1000" oninput="calcEffi()"><div class="form-hint">ระบบจะคำนวณลิตรให้อัตโนมัติ</div></div>
        <div class="full"><label class="form-label">ระยะทางที่วิ่ง (กม.) — ดึงอัตโนมัติจากงานวันนั้น</label><input type="number" class="form-control" id="f-km" placeholder="กม." oninput="calcEffi()"></div>
      </div>
      <div id="tripsSection" style="display:none">
        <div class="trips-box">
          <div class="trips-box-header">🗺 งานที่วิ่งวันนั้น (ดึงอัตโนมัติ)</div>
          <div id="tripsList"></div>
          <div class="trips-total" id="tripsTotal"></div>
        </div>
      </div>
      <div class="calc-box" id="calcBox">
        <div class="title">📊 ผลการคำนวณอัตโนมัติ</div>
        <div class="calc-grid">
          <div class="calc-item"><div class="lbl">จำนวนลิตรที่ได้</div><div class="val amber" id="calcLiters">—</div><div class="unit">ลิตร</div></div>
          <div class="calc-item"><div class="lbl">อัตราสิ้นเปลือง</div><div class="val" id="effiKm">—</div><div class="unit">km/L</div></div>
          <div class="calc-item"><div class="lbl">ราคาต่อ กม.</div><div class="val" id="effiPKm">—</div><div class="unit">฿/กม.</div></div>
        </div>
      </div>
      <div style="margin-top:14px"><label class="form-label">หมายเหตุ</label><textarea class="form-control" id="f-note" placeholder="เช่น เติมที่ปั๊ม PTT สาขาลาดกระบัง"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeFuelModal()">ยกเลิก</button><button class="btn btn-primary" onclick="saveFuel()">💾 บันทึกข้อมูล</button></div>
  </div>
</div>

<div class="modal-overlay" id="serviceModal" onclick="if(event.target===this)closeServiceModal()">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">เพิ่มข้อมูลเซอร์วิส</div><button class="modal-close" onclick="closeServiceModal()">✕</button></div>
    <div class="modal-body">
      <div class="form-grid">
        <div><label class="form-label">วันที่ *</label><input type="date" class="form-control" id="sv-date"></div>
        <div><label class="form-label">รหัสรถ *</label><input type="text" class="form-control" id="sv-code" placeholder="เช่น รถ-001"></div>
        <div class="full"><label class="form-label">ชื่อรายการ *</label><input type="text" class="form-control" id="sv-name" placeholder="เช่น เปลี่ยนน้ำมันเครื่อง"></div>
        <div><label class="form-label">ประเภท</label><select class="form-control" id="sv-type"><option>เปลี่ยนน้ำมันเครื่อง</option><option>เปลี่ยนยาง</option><option>เปลี่ยนเบรก</option><option>ตรวจเช็คระยะ</option><option>ซ่อมเครื่องยนต์</option><option>อื่นๆ</option></select></div>
        <div><label class="form-label">ค่าใช้จ่าย (฿)</label><input type="number" class="form-control" id="sv-cost" placeholder="0"></div>
        <div><label class="form-label">สถานะ</label><select class="form-control" id="sv-status"><option value="done">เสร็จแล้ว</option><option value="pending">รอดำเนินการ</option><option value="open">กำลังดำเนินการ</option></select></div>
        <div class="full"><label class="form-label">หมายเหตุ</label><textarea class="form-control" id="sv-note" placeholder="รายละเอียดเพิ่มเติม..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeServiceModal()">ยกเลิก</button><button class="btn btn-primary" onclick="saveService()">💾 บันทึก</button></div>
  </div>
</div>
<div class="toast" id="toast"></div>
<script>
const DRIVERS=['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
const COLORS=['#4f8ef7','#38c98a','#f5a623','#e85d5d','#a855f7','#06b6d4','#f59e0b','#10b981','#ef4444'];
let currentView='month',currentOilPrice=40.74;
const MOCK_TRIPS={
  'บังเดช':{'2025-12-10':[{dest:'ลาดกระบัง → บางนา',km:45},{dest:'บางนา → สีลม',km:22}],'2025-12-15':[{dest:'ดอนเมือง → อ่อนนุช',km:38},{dest:'อ่อนนุช → ลาดกระบัง',km:33}]},
  'แชม':{'2025-12-11':[{dest:'พระราม 3 → บางขุนเทียน',km:30}],'2025-12-20':[{dest:'มีนบุรี → ลาดพร้าว',km:25},{dest:'ลาดพร้าว → อนุสาวรีย์',km:12}]},
  'กอล์ฟ':{'2025-12-08':[{dest:'สุขุมวิท → พระโขนง',km:18},{dest:'พระโขนง → บางนา',km:12}],'2025-12-18':[{dest:'รังสิต → ดอนเมือง',km:20}]},
  'หรั่ง':{'2025-12-05':[{dest:'บางนา → สมุทรปราการ',km:35},{dest:'สมุทรปราการ → บางนา',km:35}]},
  'เก่ง':{'2025-12-12':[{dest:'ลาดกระบัง → บางปะกอก',km:28}]},
};
let fuelData=[
  {id:1,date:'2025-12-10',driver:'บังเดช',amount:1340,price:33.5,km:600,trips:'ลาดกระบัง→บางนา, บางนา→สีลม',note:'ปั๊ม PTT ลาดกระบัง'},
  {id:2,date:'2025-12-11',driver:'แชม',amount:1172.5,price:33.5,km:525,trips:'พระราม3→บางขุนเทียน',note:''},
  {id:3,date:'2025-12-08',driver:'กอล์ฟ',amount:944.72,price:33.74,km:420,trips:'สุขุมวิท→พระโขนง',note:''},
  {id:4,date:'2025-12-05',driver:'หรั่ง',amount:1675,price:33.5,km:750,trips:'บางนา→สมุทรปราการ',note:'เต็มถัง'},
  {id:5,date:'2025-12-12',driver:'เก่ง',amount:1079.68,price:33.74,km:480,trips:'ลาดกระบัง→บางปะกอก',note:''},
  {id:6,date:'2025-12-15',driver:'บังเดช',amount:1518.3,price:33.74,km:675,trips:'ดอนเมือง→อ่อนนุช',note:''},
  {id:7,date:'2025-12-18',driver:'กอล์ฟ',amount:1012.2,price:33.74,km:450,trips:'รังสิต→ดอนเมือง',note:''},
  {id:8,date:'2025-12-20',driver:'แชม',amount:1293.52,price:34.04,km:570,trips:'มีนบุรี→ลาดพร้าว',note:''},
  {id:9,date:'2025-11-15',driver:'เอ',amount:1385.58,price:32.99,km:630,trips:'ท่าเรือ→บางปู',note:''},
  {id:10,date:'2025-11-22',driver:'ยุทร',amount:1187.64,price:32.99,km:540,trips:'นวนคร→ดอนเมือง',note:''},
  {id:11,date:'2025-10-10',driver:'แฟรงค์',amount:1263.12,price:33.24,km:570,trips:'สีลม→บางรัก',note:''},
  {id:12,date:'2025-10-20',driver:'เอ้',amount:1462.56,price:33.24,km:660,trips:'ดอนเมือง→สุวรรณภูมิ',note:''},
];
let serviceData=[],nextFuelId=13,nextSvId=1,editFuelId=null;
let chartD,chartK,chartT,chartR;
function getLiters(r){return r.price>0?r.amount/r.price:0;}
function init(){
  updateNavDate();populateDriverFilter();setView('month');renderServiceTable();renderReportPage();
}
function updateNavDate(){
  const now=new Date();
  const days=['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัส','ศุกร์','เสาร์'];
  const months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  document.getElementById('navDate').textContent=`วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()+543}`;
}
function populateDriverFilter(){
  const sel=document.getElementById('fltDriver');
  DRIVERS.forEach(d=>{const o=document.createElement('option');o.value=d;o.textContent=d;sel.appendChild(o);});
}
function showPage(name){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.sidebar-menu a').forEach(a=>a.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  document.getElementById('menu-'+name).classList.add('active');
  if(name==='report')renderReportPage();
}
function setView(v){
  currentView=v;
  ['day','month','year','all'].forEach(t=>{document.getElementById('tab-'+t).classList.toggle('active',t===v);});
  document.getElementById('fltDayWrap').style.display=(v==='day')?'block':'none';
  document.getElementById('fltMonthWrap').style.display=(v==='month'||v==='year')?'flex':'none';
  if(v==='day'&&!document.getElementById('fltDay').value)document.getElementById('fltDay').value=new Date().toISOString().slice(0,10);
  renderFuel();
}
function getFiltered(){
  const drv=document.getElementById('fltDriver').value;
  return fuelData.filter(r=>{
    if(drv!=='all'&&r.driver!==drv)return false;
    if(currentView==='day'){const d=document.getElementById('fltDay').value;return !d||r.date===d;}
    if(currentView==='month'){const m=document.getElementById('fltMonth').value;return !m||r.date.startsWith(m);}
    if(currentView==='year'){const m=document.getElementById('fltMonth').value;return !m||r.date.startsWith(m.slice(0,4));}
    return true;
  });
}
function renderFuel(){const data=getFiltered();renderMetrics(data);renderCharts(data);renderFuelTable();}
function renderMetrics(data){
  const tL=data.reduce((a,r)=>a+getLiters(r),0);
  const tC=data.reduce((a,r)=>a+r.amount,0);
  const tK=data.reduce((a,r)=>a+r.km,0);
  const avg=tL>0?tK/tL:0;
  document.getElementById('fuelMetrics').innerHTML=`
    <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวม</div><div class="metric-value">${tL.toFixed(1)}</div><div class="metric-sub">ลิตร</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿${Math.round(tC).toLocaleString()}</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card green"><div class="metric-icon green">🛣</div><div class="metric-label">ระยะทางรวม</div><div class="metric-value">${tK.toLocaleString()}</div><div class="metric-sub">กม.</div></div>
    <div class="metric-card green"><div class="metric-icon green">📈</div><div class="metric-label">เฉลี่ย km/L</div><div class="metric-value">${avg.toFixed(1)}</div><div class="metric-sub">ทุกคนขับ</div></div>
    <div class="metric-card navy"><div class="metric-icon navy">📋</div><div class="metric-label">จำนวนครั้ง</div><div class="metric-value">${data.length}</div><div class="metric-sub">รายการ</div></div>
  `;
}
function renderCharts(data){
  const totals={};DRIVERS.forEach(d=>{totals[d]={liters:0,km:0,cost:0};});
  data.forEach(r=>{if(totals[r.driver]){const l=getLiters(r);totals[r.driver].liters+=l;totals[r.driver].km+=r.km;totals[r.driver].cost+=r.amount;}});
  const active=DRIVERS.filter(d=>totals[d].liters>0);
  const colors=active.map(d=>COLORS[DRIVERS.indexOf(d)]);
  const liters=active.map(d=>parseFloat(totals[d].liters.toFixed(1)));
  const kmL=active.map(d=>totals[d].liters>0?parseFloat((totals[d].km/totals[d].liters).toFixed(1)):0);
  document.getElementById('legend1').innerHTML=active.map((d,i)=>`<div class="legend-item"><div class="legend-dot" style="background:${colors[i]}"></div>${d}</div>`).join('');
  document.getElementById('legend2').innerHTML=active.map((d,i)=>`<div class="legend-item"><div class="legend-dot" style="background:${colors[i]}"></div>${d}</div>`).join('');
  if(chartD)chartD.destroy();
  chartD=new Chart(document.getElementById('chartDriver'),{type:'bar',data:{labels:active,datasets:[{data:liters,backgroundColor:colors,borderRadius:5}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{font:{size:11}},grid:{color:'rgba(0,0,0,.04)'}},x:{ticks:{font:{size:11}}}}}});
  if(chartK)chartK.destroy();
  chartK=new Chart(document.getElementById('chartKm'),{type:'bar',data:{labels:active,datasets:[{data:kmL,backgroundColor:colors,borderRadius:5}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>`${v.raw} km/L`}}},scales:{y:{beginAtZero:true,ticks:{font:{size:11},callback:v=>v+' km/L'},grid:{color:'rgba(0,0,0,.04)'}},x:{ticks:{font:{size:11}}}}}});
  const mMap={};fuelData.forEach(r=>{const m=r.date.slice(0,7);if(!mMap[m])mMap[m]=0;mMap[m]+=r.amount;});
  const mons=Object.keys(mMap).sort();
  const mLabels=mons.map(m=>{const[y,mo]=m.split('-');return `${mo}/${y.slice(2)}`;});
  if(chartT)chartT.destroy();
  chartT=new Chart(document.getElementById('chartTrend'),{type:'line',data:{labels:mLabels,datasets:[{data:mons.map(m=>Math.round(mMap[m])),borderColor:'#4f8ef7',backgroundColor:'rgba(79,142,247,.07)',fill:true,tension:.4,pointRadius:5,pointBackgroundColor:'#4f8ef7',pointBorderColor:'#fff',pointBorderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>`฿${v.raw.toLocaleString()}`}}},scales:{y:{beginAtZero:true,ticks:{font:{size:11},callback:v=>'฿'+v.toLocaleString()},grid:{color:'rgba(0,0,0,.04)'}},x:{ticks:{font:{size:11}}}}}});
}
function renderFuelTable(){
  const q=(document.getElementById('fuelSearch').value||'').toLowerCase();
  const filtered=getFiltered().filter(r=>!q||r.driver.includes(q)||r.date.includes(q)||(r.trips||'').includes(q)||(r.note||'').includes(q));
  const sorted=[...filtered].sort((a,b)=>b.date.localeCompare(a.date));
  document.getElementById('fuelCount').textContent=sorted.length;
  document.getElementById('fuelTableInfo').textContent=`แสดง ${sorted.length} จาก ${fuelData.length} รายการ`;
  document.getElementById('fuelUpdateTime').textContent=`อัพเดต ${new Date().toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'})}`;
  document.getElementById('fuelTableBody').innerHTML=sorted.length===0
    ?`<tr><td colspan="11"><div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการ</p></div></td></tr>`
    :sorted.map((r,i)=>{
      const liters=getLiters(r);
      const kml=liters>0?(r.km/liters).toFixed(1):'—';
      const pkm=r.km>0?(r.amount/r.km).toFixed(2):'—';
      const kmlNum=parseFloat(kml);
      const bc=kmlNum>=16?'badge-green':kmlNum>=13?'badge-blue':'badge-amber';
      const bt=kmlNum>=16?'🟢 ประหยัด':kmlNum>=13?'🔵 ปกติ':'🟡 สิ้นเปลือง';
      return `<tr>
        <td style="color:var(--text3)">${i+1}</td><td>${r.date}</td>
        <td><strong style="color:var(--navy)">${r.driver}</strong></td>
        <td style="font-weight:600">฿${r.amount.toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:2})}</td>
        <td>฿${r.price}</td><td>${liters.toFixed(2)} ล.</td>
        <td>${r.km.toLocaleString()}</td>
        <td><span class="km-val">${kml}</span> <span class="badge ${bc}" style="font-size:10px">${bt}</span></td>
        <td style="color:var(--text2)">${pkm}</td>
        <td style="font-size:11px;color:var(--text2);max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.trips||'—'}</td>
        <td><div class="action-btns"><button class="action-btn edit" onclick="editFuel(${r.id})">✏</button><button class="action-btn del" onclick="deleteFuel(${r.id})">🗑</button></div></td>
      </tr>`;
    }).join('');
}
function openFuelModal(id){
  editFuelId=id||null;
  document.getElementById('fuelModalTitle').textContent=id?'แก้ไขข้อมูลเติมน้ำมัน':'เพิ่มข้อมูลเติมน้ำมัน';
  if(id){
    const r=fuelData.find(x=>x.id===id);
    document.getElementById('f-date').value=r.date;document.getElementById('f-driver').value=r.driver;
    document.getElementById('f-price').value=r.price;document.getElementById('f-amount').value=r.amount;
    document.getElementById('f-km').value=r.km;document.getElementById('f-note').value=r.note||'';
    onDateDriverChange();
  }else{
    document.getElementById('f-date').value=new Date().toISOString().slice(0,10);
    document.getElementById('f-driver').value='';document.getElementById('f-price').value=currentOilPrice;
    document.getElementById('f-amount').value='';document.getElementById('f-km').value='';document.getElementById('f-note').value='';
    document.getElementById('calcBox').style.display='none';document.getElementById('tripsSection').style.display='none';
  }
  calcEffi();document.getElementById('fuelModal').classList.add('open');
}
function closeFuelModal(){document.getElementById('fuelModal').classList.remove('open');}
function editFuel(id){openFuelModal(id);}
function deleteFuel(id){
  if(!confirm('ยืนยันการลบรายการนี้?'))return;
  fuelData=fuelData.filter(r=>r.id!==id);renderFuel();showToast('✅ ลบรายการแล้ว');
}
function onDateDriverChange(){
  const date=document.getElementById('f-date').value;const driver=document.getElementById('f-driver').value;
  if(!date||!driver){document.getElementById('tripsSection').style.display='none';return;}
  const trips=(MOCK_TRIPS[driver]||{})[date];
  if(trips&&trips.length){
    const totalKm=trips.reduce((a,t)=>a+t.km,0);
    document.getElementById('f-km').value=totalKm;
    document.getElementById('tripsList').innerHTML=trips.map(t=>`<div class="trips-box-item"><span class="dest">📍 ${t.dest}</span><span class="km">${t.km} กม.</span></div>`).join('');
    document.getElementById('tripsTotal').innerHTML=`<span>รวมระยะทางทั้งหมด</span><span style="color:var(--accent2)">🛣 ${totalKm} กม.</span>`;
    document.getElementById('tripsSection').style.display='block';
  }else{document.getElementById('tripsSection').style.display='none';}
  calcEffi();
}
function calcEffi(){
  const price=parseFloat(document.getElementById('f-price').value)||0;
  const amount=parseFloat(document.getElementById('f-amount').value)||0;
  const km=parseFloat(document.getElementById('f-km').value)||0;
  const box=document.getElementById('calcBox');
  if(price>0&&amount>0){
    const liters=amount/price;
    const kml=km>0?km/liters:0;
    const pkm=km>0?amount/km:0;
    document.getElementById('calcLiters').textContent=liters.toFixed(2);
    document.getElementById('effiKm').textContent=km>0?kml.toFixed(2):'—';
    document.getElementById('effiPKm').textContent=km>0?pkm.toFixed(2):'—';
    box.style.display='block';
  }else{
    document.getElementById('calcLiters').textContent='—';document.getElementById('effiKm').textContent='—';document.getElementById('effiPKm').textContent='—';
    box.style.display='none';
  }
}
function saveFuel(){
  const date=document.getElementById('f-date').value;const driver=document.getElementById('f-driver').value;
  const price=parseFloat(document.getElementById('f-price').value);const amount=parseFloat(document.getElementById('f-amount').value);
  const km=parseFloat(document.getElementById('f-km').value);const note=document.getElementById('f-note').value;
  if(!date||!driver||!price||!amount||!km){alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบ');return;}
  const tripsVisible=document.getElementById('tripsSection').style.display!=='none';
  const trips=tripsVisible?Array.from(document.querySelectorAll('.trips-box-item .dest')).map(e=>e.textContent.replace('📍 ','')).join(', '):'';
  if(editFuelId){const r=fuelData.find(x=>x.id===editFuelId);Object.assign(r,{date,driver,price,amount,km,note,trips});showToast('✅ อัพเดตข้อมูลแล้ว');}
  else{fuelData.push({id:nextFuelId++,date,driver,price,amount,km,note,trips});showToast('✅ บันทึกข้อมูลแล้ว');}
  closeFuelModal();renderFuel();
}
function openServiceModal(){document.getElementById('serviceModal').classList.add('open');document.getElementById('sv-date').value=new Date().toISOString().slice(0,10);}
function closeServiceModal(){document.getElementById('serviceModal').classList.remove('open');}
function saveService(){
  const date=document.getElementById('sv-date').value;const code=document.getElementById('sv-code').value;const name=document.getElementById('sv-name').value;
  const type=document.getElementById('sv-type').value;const cost=parseFloat(document.getElementById('sv-cost').value)||0;
  const status=document.getElementById('sv-status').value;const note=document.getElementById('sv-note').value;
  if(!date||!code||!name){alert('กรุณากรอกข้อมูลให้ครบ');return;}
  serviceData.push({id:nextSvId++,date,code,name,type,cost,status,note});closeServiceModal();renderServiceTable();showToast('✅ บันทึกข้อมูลเซอร์วิสแล้ว');
}
function renderServiceTable(){
  const q=(document.getElementById('serviceSearch').value||'').toLowerCase();
  const filtered=serviceData.filter(r=>!q||(r.name+r.code+r.type).toLowerCase().includes(q));
  document.getElementById('serviceCount').textContent=filtered.length;
  document.getElementById('serviceTableInfo').textContent=`แสดง ${filtered.length} จาก ${serviceData.length} รายการ`;
  document.getElementById('serviceUpdateTime').textContent=serviceData.length?`อัพเดต ${new Date().toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'})}`:'';
  document.getElementById('serviceTableBody').innerHTML=filtered.length===0
    ?`<tr><td colspan="9"><div class="empty-state"><div class="icon">🔧</div><p>ยังไม่มีข้อมูล<br>กดปุ่ม "เพิ่มข้อมูลเซอร์วิส" เพื่อเริ่มต้น</p></div></td></tr>`
    :filtered.sort((a,b)=>b.date.localeCompare(a.date)).map((r,i)=>{
      const sc=r.status==='done'?'badge-green':r.status==='pending'?'badge-amber':'badge-blue';
      const st=r.status==='done'?'✅ เสร็จแล้ว':r.status==='pending'?'⏳ รอดำเนินการ':'🔄 กำลังดำเนินการ';
      return `<tr><td style="color:var(--text3)">${i+1}</td><td>${r.date}</td><td><code style="background:var(--surface2);padding:2px 7px;border-radius:4px;font-size:12px">${r.code}</code></td><td><strong>${r.name}</strong></td><td>${r.type}</td><td style="font-weight:600">฿${r.cost.toLocaleString()}</td><td style="font-size:12px;color:var(--text2)">${r.note||'—'}</td><td><span class="badge ${sc}">${st}</span></td><td><div class="action-btns"><button class="action-btn del" onclick="deleteService(${r.id})">🗑</button></div></td></tr>`;
    }).join('');
}
function deleteService(id){if(!confirm('ยืนยันการลบ?'))return;serviceData=serviceData.filter(r=>r.id!==id);renderServiceTable();}
function renderReportPage(){
  const tL=fuelData.reduce((a,r)=>a+getLiters(r),0);const tC=fuelData.reduce((a,r)=>a+r.amount,0);const tK=fuelData.reduce((a,r)=>a+r.km,0);const svC=serviceData.reduce((a,r)=>a+r.cost,0);
  document.getElementById('reportMetrics').innerHTML=`
    <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวมทั้งปี</div><div class="metric-value">${tL.toFixed(1)}</div><div class="metric-sub">ลิตร</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿${Math.round(tC).toLocaleString()}</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card green"><div class="metric-icon green">🛣</div><div class="metric-label">ระยะทางรวม</div><div class="metric-value">${tK.toLocaleString()}</div><div class="metric-sub">กม.</div></div>
    <div class="metric-card red"><div class="metric-icon red">🔧</div><div class="metric-label">ค่าเซอร์วิสรวม</div><div class="metric-value">฿${svC.toLocaleString()}</div><div class="metric-sub">บาท</div></div>
  `;
  const totals={};DRIVERS.forEach(d=>{totals[d]=0;});fuelData.forEach(r=>{if(totals[r.driver]!==undefined)totals[r.driver]+=r.amount;});
  const active=DRIVERS.filter(d=>totals[d]>0);
  if(chartR)chartR.destroy();
  chartR=new Chart(document.getElementById('chartReport'),{type:'doughnut',data:{labels:active,datasets:[{data:active.map(d=>Math.round(totals[d])),backgroundColor:active.map(d=>COLORS[DRIVERS.indexOf(d)]),borderWidth:2,borderColor:'#fff'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{font:{family:'Sarabun',size:12},padding:12}},tooltip:{callbacks:{label:v=>`${v.label}: ฿${v.raw.toLocaleString()}`}}}}});
}
function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2800);}
init();
</script>
</body>
</html>
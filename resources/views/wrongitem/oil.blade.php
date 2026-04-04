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
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px}
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
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
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
.form-control:disabled{background:var(--surface2);color:var(--text3);cursor:not-allowed}
.oil-price-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;color:#fff}
.oil-price-banner .lbl{font-size:12px;opacity:.7}
.oil-price-banner .price{font-size:22px;font-weight:700}
.oil-price-banner .unit{font-size:12px;opacity:.7;margin-left:4px}
.oil-price-loading{font-size:12px;opacity:.6;font-style:italic}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--accent2);animation:pulse 1.5s infinite}
.live-dot.loading{background:var(--accent3)}
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
.plate-tag{background:var(--surface2);border:1px solid var(--border);border-radius:4px;font-size:11px;color:var(--text2);padding:1px 6px;font-family:monospace;font-weight:600}
.job-legend{display:flex;gap:14px;margin-bottom:10px;flex-wrap:wrap}
.job-legend-item{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)}
.job-legend-dot{width:12px;height:12px;border-radius:3px}
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
      <li>
        <a href="/service" class="active" id="menu-fuel">
          <span class="icon">🛠️</span>Service
        </a>
      </li>
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
            <thead><tr><th>#</th><th>วันที่</th><th>คนขับ</th><th>ทะเบียน</th><th>ยอดเงิน (฿)</th><th>ราคา/ลิตร</th><th>จำนวนลิตร</th><th>ระยะทาง (กม.)</th><th>km/L</th><th>฿/กม.</th><th>จัดการ</th></tr></thead>
            <tbody id="fuelTableBody"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="fuelTableInfo">แสดง 0 จาก 0 รายการ</span>
          <div style="display:flex;align-items:center;gap:12px">
            <button id="btnShowAll" onclick="toggleShowAll()" style="display:none;font-family:'Sarabun',sans-serif;font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;border:1px solid var(--accent);color:var(--accent);background:transparent;cursor:pointer;transition:all .15s" onmouseover="this.style.background='var(--accent)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--accent)'">ดูทั้งหมด</button>
            <span id="fuelUpdateTime">—</span>
          </div>
        </div>
      </div>
      <div class="metrics" id="fuelMetrics"></div>
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-card-title">ปริมาณน้ำมัน (ลิตร) ต่อคนขับ</div>
          <div class="chart-card-sub">รวมน้ำมันที่เติมแต่ละคน</div>
          <div class="legend" id="legend1"></div>
          <div style="position:relative;width:100%;height:230px"><canvas id="chartDriver"></canvas></div>
        </div>
        <div class="chart-card">
          <div class="chart-card-title">ปริมาณงานต่อคนขับ</div>
          <div class="chart-card-sub">สำเร็จ vs ไม่สำเร็จ แยกตามคนขับ</div>
          <div class="job-legend">
            <div class="job-legend-item"><div class="job-legend-dot" style="background:#38c98a"></div>สำเร็จ</div>
            <div class="job-legend-item"><div class="job-legend-dot" style="background:#e85d5d"></div>ไม่สำเร็จ</div>
          </div>
          <div style="position:relative;width:100%;height:230px"><canvas id="chartJobs"></canvas></div>
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
    <div class="modal-header">
      <div class="modal-title" id="fuelModalTitle">เพิ่มข้อมูลเติมน้ำมัน</div>
      <button class="modal-close" onclick="closeFuelModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="oil-price-banner">
        <div>
          <div class="lbl" id="oilPriceLabel">ราคาน้ำมันดีเซล (ปัจจุบัน)</div>
          <div style="display:flex;align-items:baseline;gap:4px">
            <span class="price" id="oilPriceShow">—</span>
            <span class="unit">บาท/ลิตร</span>
          </div>
          <div class="oil-price-loading" id="oilPriceStatus">กำลังโหลดราคาน้ำมัน...</div>
          <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
            <button onclick="switchOilType('diesel')" id="btnOilDiesel" style="font-family:'Sarabun',sans-serif;font-size:12px;font-weight:600;padding:6px 12px;border-radius:16px;border:2px solid #fff;background:rgba(255,255,255,.2);color:#fff;cursor:pointer;transition:all .2s">⛽ ดีเซล</button>
            <button onclick="switchOilType('95')" id="btnOil95" style="font-family:'Sarabun',sans-serif;font-size:12px;font-weight:600;padding:6px 12px;border-radius:16px;border:2px solid transparent;background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);cursor:pointer;transition:all .2s">⛽ 95</button>
            <button onclick="refreshOilPrice()" id="btnRefreshOil" style="font-family:'Sarabun',sans-serif;font-size:12px;font-weight:600;padding:6px 12px;border-radius:16px;border:2px solid rgba(255,255,255,.3);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);cursor:pointer;transition:all .2s" title="ดึงราคาจาก API">🔄 รีเฟรช</button>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px">
          <div class="live-dot loading" id="liveDot"></div>
          <span style="font-size:12px;opacity:.7" id="liveLabel">กำลังดึงข้อมูล</span>
        </div>
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">วันที่เติม *</label>
          <input type="date" class="form-control" id="f-date" onchange="onDateChange()">
        </div>
        <div>
          <label class="form-label">คนขับ *</label>
          <select class="form-control" id="f-driver" onchange="onDriverChange()">
            <option value="">— เลือกคนขับ —</option>
          </select>
        </div>
        <div>
          <label class="form-label">เลขทะเบียนรถ *</label>
          <select class="form-control" id="f-plate" onchange="calcEffi()">
            <option value="">— เลือกทะเบียน —</option>
          </select>
        </div>
        <div>
          <label class="form-label">ราคาน้ำมัน (฿/ลิตร) *</label>
          <input type="number" class="form-control" id="f-price" step="0.01" placeholder="—" oninput="calcEffi()">
        </div>

        <div>
          <label class="form-label">เวลาเริ่มต้น *</label>
          <input type="time" class="form-control" id="f-start-time">
        </div>
        <div>
          <label class="form-label">เวลาสิ้นสุด *</label>
          <input type="time" class="form-control" id="f-end-time">
        </div>

        <div class="full">
          <label class="form-label">ยอดเงินที่เติม (฿) *</label>
          <input type="number" class="form-control" id="f-amount" step="1" placeholder="เช่น 1000" oninput="calcEffi()">
          <div class="form-hint">ระบบจะคำนวณลิตรให้อัตโนมัติ</div>
        </div>
        <div class="full">
          <label class="form-label">ระยะทางที่วิ่ง (กม.) — ดึงอัตโนมัติจากงานวันนั้น</label>
          <input type="number" class="form-control" id="f-km" placeholder="กม." oninput="calcEffi()">
        </div>
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
        <div class="calc-grid" style="grid-template-columns:1fr 1fr">
          <div class="calc-item"><div class="lbl">จำนวนลิตรที่ได้</div><div class="val amber" id="calcLiters">—</div><div class="unit">ลิตร</div></div>
          <div class="calc-item"><div class="lbl">อัตราสิ้นเปลือง</div><div class="val" id="effiKm">—</div><div class="unit">km/L</div></div>
        </div>
      </div>
      <div style="margin-top:14px">
        <label class="form-label">หมายเหตุ</label>
        <textarea class="form-control" id="f-note" placeholder="เช่น เติมที่ปั๊ม PTT สาขาลาดกระบัง"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeFuelModal()">ยกเลิก</button>
      <button class="btn btn-primary" onclick="saveFuel()">💾 บันทึกข้อมูล</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ===================== DATA =====================
const ALL_PLATES = [
  '1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158',
  '2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621',
  '3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478',
  '3ฉมห200','4กย1540','6762',
  'City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830',
];
const DRIVER_DATA = [
  {name:'บังเดช', plates:['3ฉมห200','6762']},
  {name:'แชม',   plates:['3ฉมง3059']},
  {name:'กอล์ฟ', plates:['1 ฉผ 3181']},
  {name:'หรั่ง', plates:['3ฉมย478']},
  {name:'เก่ง',  plates:['2 ฉธ 1620']},
  {name:'เอ',    plates:['2ฉมฎ3017']},
  {name:'ยุทร',  plates:['3ฉมก6071']},
  {name:'แฟรงค์',plates:['2ฉธ1621']},
  {name:'เอ้',   plates:['2ฉธ1619']},
];
const DRIVERS = DRIVER_DATA.map(d=>d.name);
const COLORS=['#4f8ef7','#38c98a','#f5a623','#e85d5d','#a855f7','#06b6d4','#f59e0b','#10b981','#ef4444'];
let currentOilType = 'diesel';
let oilPrices = {diesel: 47.74, '95': 44.45};

const MOCK_TRIPS = {
  'บังเดช':{
    '2025-12-10':[{dest:'ลาดกระบัง → บางนา',km:45,done:true},{dest:'บางนา → สีลม',km:22,done:true}],
    '2025-12-15':[{dest:'ดอนเมือง → อ่อนนุช',km:38,done:true},{dest:'อ่อนนุช → ลาดกระบัง',km:33,done:false}]
  },
  'แชม':{
    '2025-12-11':[{dest:'พระราม 3 → บางขุนเทียน',km:30,done:true}],
    '2025-12-20':[{dest:'มีนบุรี → ลาดพร้าว',km:25,done:true},{dest:'ลาดพร้าว → อนุสาวรีย์',km:12,done:false}]
  },
  'กอล์ฟ':{
    '2025-12-08':[{dest:'สุขุมวิท → พระโขนง',km:18,done:true},{dest:'พระโขนง → บางนา',km:12,done:true}],
    '2025-12-18':[{dest:'รังสิต → ดอนเมือง',km:20,done:false}]
  },
  'หรั่ง':{'2025-12-05':[{dest:'บางนา → สมุทรปราการ',km:35,done:true},{dest:'สมุทรปราการ → บางนา',km:35,done:true}]},
  'เก่ง':{'2025-12-12':[{dest:'ลาดกระบัง → บางปะกอก',km:28,done:true}]},
  'เอ':{'2025-11-15':[{dest:'ท่าเรือ → บางปู',km:40,done:true},{dest:'บางปู → ท่าเรือ',km:40,done:false}]},
  'ยุทร':{'2025-11-22':[{dest:'นวนคร → ดอนเมือง',km:45,done:true}]},
  'แฟรงค์':{'2025-10-10':[{dest:'สีลม → บางรัก',km:15,done:true},{dest:'บางรัก → สาทร',km:8,done:false}]},
  'เอ้':{'2025-10-20':[{dest:'ดอนเมือง → สุวรรณภูมิ',km:55,done:true}]},
};

let fuelData=[
  {id:1,date:'2025-12-10',driver:'บังเดช',plate:'3ฉมห200',amount:1340,price:33.5,km:600,trips:'ลาดกระบัง→บางนา, บางนา→สีลม',note:'ปั๊ม PTT ลาดกระบัง'},
  {id:2,date:'2025-12-11',driver:'แชม',plate:'3ฉมง3059',amount:1172.5,price:33.5,km:525,trips:'พระราม3→บางขุนเทียน',note:''},
  {id:3,date:'2025-12-08',driver:'กอล์ฟ',plate:'1 ฉผ 3181',amount:944.72,price:33.74,km:420,trips:'สุขุมวิท→พระโขนง',note:''},
  {id:4,date:'2025-12-05',driver:'หรั่ง',plate:'3ฉมย478',amount:1675,price:33.5,km:750,trips:'บางนา→สมุทรปราการ',note:'เต็มถัง'},
  {id:5,date:'2025-12-12',driver:'เก่ง',plate:'2 ฉธ 1620',amount:1079.68,price:33.74,km:480,trips:'ลาดกระบัง→บางปะกอก',note:''},
  {id:6,date:'2025-12-15',driver:'บังเดช',plate:'6762',amount:1518.3,price:33.74,km:675,trips:'ดอนเมือง→อ่อนนุช',note:''},
  {id:7,date:'2025-12-18',driver:'กอล์ฟ',plate:'1 ฉผ 3181',amount:1012.2,price:33.74,km:450,trips:'รังสิต→ดอนเมือง',note:''},
  {id:8,date:'2025-12-20',driver:'แชม',plate:'3ฉมง3059',amount:1293.52,price:34.04,km:570,trips:'มีนบุรี→ลาดพร้าว',note:''},
  {id:9,date:'2025-11-15',driver:'เอ',plate:'2ฉมฎ3017',amount:1385.58,price:32.99,km:630,trips:'ท่าเรือ→บางปู',note:''},
  {id:10,date:'2025-11-22',driver:'ยุทร',plate:'3ฉมก6071',amount:1187.64,price:32.99,km:540,trips:'นวนคร→ดอนเมือง',note:''},
  {id:11,date:'2025-10-10',driver:'แฟรงค์',plate:'2ฉธ1621',amount:1263.12,price:33.24,km:570,trips:'สีลม→บางรัก',note:''},
  {id:12,date:'2025-10-20',driver:'เอ้',plate:'2ฉธ1619',amount:1462.56,price:33.24,km:660,trips:'ดอนเมือง→สุวรรณภูมิ',note:''},
];

let nextFuelId=13,editFuelId=null,showAllRows=false;
let currentView='month';
let currentOilPrice=null;
let chartD,chartJobs,chartT,chartR;

// ===================== OIL PRICE =====================

// ===================== OIL PRICE =====================

async function refreshOilPrice() {
  const btn = document.getElementById('btnRefreshOil');
  btn.disabled = true;
  btn.style.opacity = '0.5';
  
  document.getElementById('oilPriceStatus').textContent = '⏳ กำลังดึง...';
  document.getElementById('liveDot').className = 'live-dot loading';
  
  await loadOilPrice(currentOilType);
  
  btn.disabled = false;
  btn.style.opacity = '1';
}

// Load prices from localStorage on init
function loadCachedPrices() {
  try {
    const cached = localStorage.getItem('oilPrices');
    if(cached) {
      const prices = JSON.parse(cached);
      oilPrices = {...oilPrices, ...prices};
      console.log('✅ Loaded cached prices:', oilPrices);
    }
  } catch(e) {
    console.log('Could not load cache:', e.message);
  }
}

// Save prices to localStorage
function cachePrices() {
  try {
    localStorage.setItem('oilPrices', JSON.stringify(oilPrices));
    console.log('✅ Prices cached');
  } catch(e) {
    console.log('Could not save cache:', e.message);
  }
}

function switchOilType(type) {
  currentOilType = type;
  // Update button styles
  document.getElementById('btnOilDiesel').style.background = type === 'diesel' ? 'rgba(255,255,255,.3)' : 'rgba(255,255,255,.1)';
  document.getElementById('btnOilDiesel').style.borderColor = type === 'diesel' ? '#fff' : 'transparent';
  document.getElementById('btnOil95').style.background = type === '95' ? 'rgba(255,255,255,.3)' : 'rgba(255,255,255,.1)';
  document.getElementById('btnOil95').style.borderColor = type === '95' ? '#fff' : 'transparent';
  // Load price for selected type
  loadOilPrice(type);
}

function applyOilPrice(price, type) {
  oilPrices[type] = price;
  cachePrices();
  const typeNames = {'diesel': 'ดีเซล', '95': 'แก๊สโซฮอล์ 95'};
  document.getElementById('oilPriceLabel').textContent = `ราคาน้ำมัน${typeNames[type] || 'ดีเซล'} (ปัจจุบัน)`;
  document.getElementById('oilPriceShow').textContent = price.toFixed(2);
  document.getElementById('oilPriceStatus').textContent = `อัพเดต ${new Date().toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'})}`;
  document.getElementById('liveDot').className = 'live-dot';
  document.getElementById('liveDot').style.background = '';
  document.getElementById('liveLabel').textContent = 'Live';
  document.getElementById('f-price').value = price.toFixed(2);
  calcEffi();
}

async function fetchOilPrice(type='diesel') {
  const config = {
    'diesel': {name: 'ดีเซล', fallback: 47.74},
    '95': {name: 'แก๊สโซฮอล์ 95', fallback: 44.45}
  };
  
  const typeConfig = config[type] || config['diesel'];
  
  try {
    console.log(`🔄 Fetching ${type} price...`);
    
    // Method 1: Try direct CORS request
    try {
      const resp = await Promise.race([
        fetch('https://api.chnwt.dev/thai-oil-api/latest'),
        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 8000))
      ]);
      
      if(resp.ok) {
        const data = await resp.json();
        console.log('✅ Got data from API:', typeof data);
        
        let price = null;
        
        // Parse based on type
        if(type === 'diesel') {
          price = data.fuelsafe_diesel?.price || data.diesel?.price;
        } else if(type === '95') {
          price = data.gasohol_95?.price;
        }
        
        if(price) {
          const numPrice = parseFloat(price);
          if(!isNaN(numPrice) && numPrice > 30 && numPrice < 60) {
            console.log(`✅ Got valid ${type} price: ${numPrice}`);
            return numPrice;
          }
        }
      }
    } catch(e1) {
      console.log('❌ Method 1 (Direct): ' + e1.message);
    }
    
    // Method 2: Use cors-anywhere proxy
    try {
      const proxyUrl = 'https://cors-anywhere.herokuapp.com/https://api.chnwt.dev/thai-oil-api/latest';
      const resp = await Promise.race([
        fetch(proxyUrl),
        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 8000))
      ]);
      
      if(resp.ok) {
        const data = await resp.json();
        console.log('✅ Got data from CORS proxy');
        
        let price = null;
        if(type === 'diesel') {
          price = data.fuelsafe_diesel?.price || data.diesel?.price;
        } else if(type === '95') {
          price = data.gasohol_95?.price;
        }
        
        if(price) {
          const numPrice = parseFloat(price);
          if(!isNaN(numPrice) && numPrice > 30 && numPrice < 60) {
            console.log(`✅ Got ${type} price from proxy: ${numPrice}`);
            return numPrice;
          }
        }
      }
    } catch(e2) {
      console.log('❌ Method 2 (CORS proxy): ' + e2.message);
    }
    
    // Method 3: Use alternative Thailand oil API (ราคาน้ำมัน.com)
    try {
      const resp = await Promise.race([
        fetch('https://www.thaioe.com/api/fuel-prices'),
        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 8000))
      ]);
      
      if(resp.ok) {
        const data = await resp.json();
        console.log('✅ Got data from alternative API');
        
        let price = null;
        if(type === 'diesel') {
          price = data.diesel || data['fuel_price'] || data['B7'];
        } else if(type === '95') {
          price = data.gasohol95 || data['gasohol_95'] || data['E95'];
        }
        
        if(price) {
          const numPrice = parseFloat(price);
          if(!isNaN(numPrice) && numPrice > 30 && numPrice < 60) {
            console.log(`✅ Got ${type} price from alternative API: ${numPrice}`);
            return numPrice;
          }
        }
      }
    } catch(e3) {
      console.log('❌ Method 3 (Alternative API): ' + e3.message);
    }
    
    // Method 4: Manual input - show prompt
    console.log('⚠️ All API methods failed, asking user to input price');
    return null;
    
  } catch(e) {
    console.error('💥 Unexpected error:', e.message);
    return null;
  }
}

async function loadOilPrice(type) {
  type = type || currentOilType;
  
  const typeNames = {
    'diesel': 'ดีเซล',
    '95': 'แก๊สโซฮอล์ 95'
  };
  
  const displayName = typeNames[type] || 'ดีเซล';
  document.getElementById('oilPriceLabel').textContent = `ราคาน้ำมัน${displayName} (ปัจจุบัน)`;
  document.getElementById('oilPriceShow').textContent = '...';
  document.getElementById('oilPriceStatus').textContent = `⏳ กำลังดึงราคาน้ำมัน...`;
  document.getElementById('liveDot').className = 'live-dot loading';
  document.getElementById('liveLabel').textContent = 'กำลังดึง';

  const price = await fetchOilPrice(type);
  const now = new Date().toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'});

  if(price) {
    oilPrices[type] = price;
    cachePrices();
    document.getElementById('oilPriceShow').textContent = price.toFixed(2);
    document.getElementById('oilPriceStatus').textContent = `✅ ดึงสำเร็จ • ${now}`;
    document.getElementById('liveDot').className = 'live-dot';
    document.getElementById('liveDot').style.background = '';
    document.getElementById('liveLabel').textContent = 'Live';
    document.getElementById('f-price').value = price.toFixed(2);
    calcEffi();
  } else {
    // Use fallback from oilPrices
    const fallback = oilPrices[type] || (type === '95' ? 44.45 : 47.74);
    oilPrices[type] = fallback;
    document.getElementById('oilPriceShow').textContent = fallback.toFixed(2);
    document.getElementById('oilPriceStatus').textContent = `⚠️ ใช้ราคาที่บันทึก • ${now}`;
    document.getElementById('liveDot').className = 'live-dot';
    document.getElementById('liveDot').style.background = 'var(--accent3)';
    document.getElementById('liveLabel').textContent = 'Cached';
    document.getElementById('f-price').value = fallback.toFixed(2);
    calcEffi();
  }
}

// ===================== INIT =====================
function getLiters(r){return r.price>0?r.amount/r.price:0;}

function updateNavDate(){
  const now=new Date();
  const days=['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัส','ศุกร์','เสาร์'];
  const months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  document.getElementById('navDate').textContent=`วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()+543}`;
}

function populateDriverFilter(){
  const sel=document.getElementById('fltDriver');
  DRIVER_DATA.forEach(d=>{const o=document.createElement('option');o.value=d.name;o.textContent=d.name;sel.appendChild(o);});
}

function populateModalDrivers(){
  const sel=document.getElementById('f-driver');
  sel.innerHTML='<option value="">— เลือกคนขับ —</option>';
  DRIVER_DATA.forEach(d=>{
    const o=document.createElement('option');
    o.value=d.name;o.textContent=d.name;sel.appendChild(o);
  });
  const plateSel=document.getElementById('f-plate');
  plateSel.innerHTML='<option value="">— เลือกทะเบียน —</option>';
  ALL_PLATES.forEach(p=>{
    const o=document.createElement('option');o.value=p;o.textContent=p;plateSel.appendChild(o);
  });
}

function showPage(name){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.sidebar-menu a').forEach(a=>a.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  document.getElementById('menu-'+name).classList.add('active');
  if(name==='report') renderReportPage();
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

function renderFuel(){const data=getFiltered();showAllRows=false;renderMetrics(data);renderCharts(data);renderFuelTable();}

function renderMetrics(data){
  const drv=document.getElementById('fltDriver').value;
  const metricsEl=document.getElementById('fuelMetrics');
  if(drv==='all'){metricsEl.innerHTML='';return;}
  const tL=data.reduce((a,r)=>a+getLiters(r),0);
  const tC=data.reduce((a,r)=>a+r.amount,0);
  const tK=data.reduce((a,r)=>a+r.km,0);
  metricsEl.innerHTML=`
    <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวม</div><div class="metric-value">${tL.toFixed(1)}</div><div class="metric-sub">ลิตร</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿${Math.round(tC).toLocaleString()}</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card green"><div class="metric-icon green">🛣</div><div class="metric-label">ระยะทางรวม</div><div class="metric-value">${tK.toLocaleString()}</div><div class="metric-sub">กม.</div></div>
  `;
}

function getJobStats(filteredDrivers){
  const stats = {};
  DRIVERS.forEach(d=>{stats[d]={done:0,fail:0};});

  const monthFilter = currentView==='month'?document.getElementById('fltMonth').value:'';
  const yearFilter = currentView==='year'?(document.getElementById('fltMonth').value||'').slice(0,4):'';
  const dayFilter = currentView==='day'?document.getElementById('fltDay').value:'';

  DRIVERS.forEach(driver=>{
    const driverTrips = MOCK_TRIPS[driver]||{};
    Object.entries(driverTrips).forEach(([date,trips])=>{
      let include=true;
      if(currentView==='day'&&dayFilter&&date!==dayFilter)include=false;
      if(currentView==='month'&&monthFilter&&!date.startsWith(monthFilter))include=false;
      if(currentView==='year'&&yearFilter&&!date.startsWith(yearFilter))include=false;
      if(include){
        trips.forEach(t=>{
          if(t.done)stats[driver].done++;
          else stats[driver].fail++;
        });
      }
    });
  });
  return stats;
}

function renderCharts(data){
  const totals={};
  DRIVERS.forEach(d=>{totals[d]={liters:0,km:0,cost:0};});
  data.forEach(r=>{
    if(totals[r.driver]){
      const l=getLiters(r);
      totals[r.driver].liters+=l;
      totals[r.driver].km+=r.km;
      totals[r.driver].cost+=r.amount;
    }
  });

  const active=DRIVERS.filter(d=>totals[d].liters>0);
  const colors=active.map(d=>COLORS[DRIVERS.indexOf(d)]);
  const liters=active.map(d=>parseFloat(totals[d].liters.toFixed(1)));

  document.getElementById('legend1').innerHTML=active.map((d,i)=>`<div class="legend-item"><div class="legend-dot" style="background:${colors[i]}"></div>${d}</div>`).join('');
  if(chartD)chartD.destroy();
  chartD=new Chart(document.getElementById('chartDriver'),{
    type:'bar',
    data:{
      labels:active,
      datasets:[{
        label:'ลิตร',
        data:liters,
        backgroundColor:colors,
        borderRadius:5
      }]
    },
    options:{
      responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>`${v.raw} ลิตร`}}},
      scales:{
        y:{beginAtZero:true,ticks:{font:{size:11},callback:v=>v+' L'},grid:{color:'rgba(0,0,0,.04)'}},
        x:{ticks:{font:{size:11}}}
      }
    }
  });

  const drvFilter = document.getElementById('fltDriver').value;
  const jobStats = getJobStats();
  const allJobDrivers = (drvFilter!=='all' ? [drvFilter] : DRIVERS).filter(d=>(jobStats[d]?.done>0||jobStats[d]?.fail>0));
  const jobDone = allJobDrivers.map(d=>jobStats[d].done);
  const jobFail = allJobDrivers.map(d=>jobStats[d].fail);

  if(chartJobs)chartJobs.destroy();
  if(allJobDrivers.length===0){
    const ctx=document.getElementById('chartJobs').getContext('2d');
    ctx.clearRect(0,0,400,230);
    ctx.font='14px Sarabun';
    ctx.fillStyle='#9aa3bc';
    ctx.textAlign='center';
    ctx.fillText('ไม่มีข้อมูลงานในช่วงนี้',200,115);
  } else {
    chartJobs=new Chart(document.getElementById('chartJobs'),{
      type:'bar',
      data:{
        labels:allJobDrivers,
        datasets:[
          {label:'สำเร็จ',data:jobDone,backgroundColor:'#38c98a',borderRadius:4},
          {label:'ไม่สำเร็จ',data:jobFail,backgroundColor:'#e85d5d',borderRadius:4}
        ]
      },
      options:{
        responsive:true,maintainAspectRatio:false,
        plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>`${v.dataset.label}: ${v.raw} งาน`}}},
        scales:{
          y:{beginAtZero:true,ticks:{font:{size:11},stepSize:1},grid:{color:'rgba(0,0,0,.04)'}},
          x:{ticks:{font:{size:11}}}
        }
      }
    });
  }

  const trendSource = drvFilter==='all' ? fuelData : fuelData.filter(r=>r.driver===drvFilter);
  const mMap={};
  trendSource.forEach(r=>{const m=r.date.slice(0,7);if(!mMap[m])mMap[m]=0;mMap[m]+=r.amount;});
  const mons=Object.keys(mMap).sort();
  const mLabels=mons.map(m=>{const[y,mo]=m.split('-');return `${mo}/${y.slice(2)}`;});
  if(chartT)chartT.destroy();
  chartT=new Chart(document.getElementById('chartTrend'),{
    type:'line',
    data:{
      labels:mLabels,
      datasets:[{
        data:mons.map(m=>Math.round(mMap[m])),
        borderColor:'#4f8ef7',backgroundColor:'rgba(79,142,247,.07)',
        fill:true,tension:.4,pointRadius:5,pointBackgroundColor:'#4f8ef7',pointBorderColor:'#fff',pointBorderWidth:2
      }]
    },
    options:{
      responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>`฿${v.raw.toLocaleString()}`}}},
      scales:{
        y:{beginAtZero:true,ticks:{font:{size:11},callback:v=>'฿'+v.toLocaleString()},grid:{color:'rgba(0,0,0,.04)'}},
        x:{ticks:{font:{size:11}}}
      }
    }
  });
}

function toggleShowAll(){
  showAllRows=!showAllRows;
  renderFuelTable();
}

function renderFuelTable(){
  const q=(document.getElementById('fuelSearch').value||'').toLowerCase();
  const base = showAllRows
    ? [...fuelData]
    : getFiltered();
  const filtered=base.filter(r=>!q||r.driver.includes(q)||r.date.includes(q)||(r.note||'').includes(q)||(r.plate||'').includes(q));
  const sorted=[...filtered].sort((a,b)=>b.date.localeCompare(a.date));

  document.getElementById('fuelCount').textContent=sorted.length;
  document.getElementById('fuelUpdateTime').textContent=`อัพเดต ${new Date().toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'})}`;

  const btnShowAll = document.getElementById('btnShowAll');
  const filteredTotal = getFiltered().length;
  const hasMore = !showAllRows && fuelData.length > filteredTotal;
  btnShowAll.style.display = (hasMore || showAllRows) ? 'inline-block' : 'none';
  btnShowAll.textContent = showAllRows ? `◀ กลับมุมมองเดิม` : `ดูทั้งหมด ${fuelData.length} รายการ`;
  btnShowAll.style.borderColor = showAllRows ? 'var(--text3)' : 'var(--accent)';
  btnShowAll.style.color = showAllRows ? 'var(--text3)' : 'var(--accent)';

  document.getElementById('fuelTableInfo').textContent = showAllRows
    ? `แสดงทั้งหมด ${sorted.length} รายการ (เรียงล่าสุดก่อน)`
    : `แสดง ${sorted.length} จาก ${fuelData.length} รายการ`;
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
        <td style="color:var(--text3)">${i+1}</td>
        <td>${r.date}</td>
        <td><strong style="color:var(--navy)">${r.driver}</strong></td>
        <td><span class="plate-tag">${r.plate||'—'}</span></td>
        <td style="font-weight:600">฿${r.amount.toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:2})}</td>
        <td>฿${r.price}</td>
        <td>${liters.toFixed(2)} ล.</td>
        <td>${r.km.toLocaleString()}</td>
        <td><span class="km-val">${kml}</span> <span class="badge ${bc}" style="font-size:10px">${bt}</span></td>
        <td style="color:var(--text2)">${pkm}</td>
        <td><div class="action-btns"><button class="action-btn edit" onclick="editFuel(${r.id})">✏</button><button class="action-btn del" onclick="deleteFuel(${r.id})">🗑</button></div></td>
      </tr>`;
    }).join('');
}

// ===================== MODAL =====================
function openFuelModal(id){
  editFuelId=id||null;
  document.getElementById('fuelModalTitle').textContent=id?'แก้ไขข้อมูลเติมน้ำมัน':'เพิ่มข้อมูลเติมน้ำมัน';
  if(id){
    const r=fuelData.find(x=>x.id===id);
    document.getElementById('f-date').value=r.date;
    document.getElementById('f-driver').value=r.driver;
    document.getElementById('f-plate').value=r.plate||'';
    document.getElementById('f-price').value=r.price;
    document.getElementById('f-amount').value=r.amount;
    document.getElementById('f-km').value=r.km;
    document.getElementById('f-note').value=r.note||'';
    onDateDriverChange();
  } else {
    document.getElementById('f-date').value=new Date().toISOString().slice(0,10);
    document.getElementById('f-driver').value='';
    document.getElementById('f-plate').value='';
    document.getElementById('f-price').value='';
    document.getElementById('f-amount').value='';
    document.getElementById('f-km').value='';
    document.getElementById('f-note').value='';
    document.getElementById('calcBox').style.display='none';
    document.getElementById('tripsSection').style.display='none';
  }
  currentOilType = 'diesel';
  oilPrices = {diesel: 47.74, '95': 44.45};
  document.getElementById('fuelModal').classList.add('open');
  // Update button states
  document.getElementById('btnOilDiesel').style.background = 'rgba(255,255,255,.3)';
  document.getElementById('btnOilDiesel').style.borderColor = '#fff';
  document.getElementById('btnOil95').style.background = 'rgba(255,255,255,.1)';
  document.getElementById('btnOil95').style.borderColor = 'transparent';
  loadOilPrice('diesel');
}

function closeFuelModal(){document.getElementById('fuelModal').classList.remove('open');}
function editFuel(id){openFuelModal(id);}
function deleteFuel(id){
  if(!confirm('ยืนยันการลบรายการนี้?'))return;
  fuelData=fuelData.filter(r=>r.id!==id);
  renderFuel();
  showToast('✅ ลบรายการแล้ว');
}

function onDriverChange(){
  const driverName=document.getElementById('f-driver').value;
  const driverObj=DRIVER_DATA.find(d=>d.name===driverName);
  if(driverObj && driverObj.plates.length===1){
    document.getElementById('f-plate').value=driverObj.plates[0];
  }
  onDateDriverChange();
}

function onDateChange(){
  onDateDriverChange();
}

function onDateDriverChange(){
  const date=document.getElementById('f-date').value;
  const driver=document.getElementById('f-driver').value;
  if(!date||!driver){document.getElementById('tripsSection').style.display='none';return;}
  const trips=(MOCK_TRIPS[driver]||{})[date];
  if(trips&&trips.length){
    const totalKm=trips.reduce((a,t)=>a+t.km,0);
    document.getElementById('f-km').value=totalKm;
    document.getElementById('tripsList').innerHTML=trips.map(t=>`
      <div class="trips-box-item">
        <span class="dest">📍 ${t.dest}</span>
        <span style="display:flex;align-items:center;gap:6px">
          <span class="km">${t.km} กม.</span>
          <span class="badge ${t.done?'badge-green':'badge-amber'}">${t.done?'✅ สำเร็จ':'⚠️ ไม่สำเร็จ'}</span>
        </span>
      </div>`).join('');
    document.getElementById('tripsTotal').innerHTML=`<span>รวมระยะทางทั้งหมด</span><span style="color:var(--accent2)">🛣 ${totalKm} กม.</span>`;
    document.getElementById('tripsSection').style.display='block';
  }else{
    document.getElementById('tripsSection').style.display='none';
  }
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
    document.getElementById('calcLiters').textContent=liters.toFixed(2);
    document.getElementById('effiKm').textContent=km>0?kml.toFixed(2):'—';
    box.style.display='block';
  }else{
    document.getElementById('calcLiters').textContent='—';
    document.getElementById('effiKm').textContent='—';
    box.style.display='none';
  }
}

function saveFuel(){
  const date=document.getElementById('f-date').value;
  const driver=document.getElementById('f-driver').value;
  const plate=document.getElementById('f-plate').value;
  const price=parseFloat(document.getElementById('f-price').value);
  const amount=parseFloat(document.getElementById('f-amount').value);
  const km=parseFloat(document.getElementById('f-km').value);
  const note=document.getElementById('f-note').value;
  if(!date||!driver||!price||!amount||!km){alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบ');return;}
  const tripsVisible=document.getElementById('tripsSection').style.display!=='none';
  const trips=tripsVisible?Array.from(document.querySelectorAll('.trips-box-item .dest')).map(e=>e.textContent.replace('📍 ','')).join(', '):'';
  if(editFuelId){
    const r=fuelData.find(x=>x.id===editFuelId);
    Object.assign(r,{date,driver,plate,price,amount,km,note,trips});
    showToast('✅ อัพเดตข้อมูลแล้ว');
  } else {
    fuelData.push({id:nextFuelId++,date,driver,plate,price,amount,km,note,trips});
    showToast('✅ บันทึกข้อมูลแล้ว');
  }
  closeFuelModal();
  renderFuel();
}

// ===================== REPORT =====================
function renderReportPage(){
  const tL=fuelData.reduce((a,r)=>a+getLiters(r),0);
  const tC=fuelData.reduce((a,r)=>a+r.amount,0);
  const tK=fuelData.reduce((a,r)=>a+r.km,0);
  document.getElementById('reportMetrics').innerHTML=`
    <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวมทั้งปี</div><div class="metric-value">${tL.toFixed(1)}</div><div class="metric-sub">ลิตร</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿${Math.round(tC).toLocaleString()}</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card green"><div class="metric-icon green">🛣</div><div class="metric-label">ระยะทางรวม</div><div class="metric-value">${tK.toLocaleString()}</div><div class="metric-sub">กม.</div></div>
  `;
  const totals={};DRIVERS.forEach(d=>{totals[d]=0;});
  fuelData.forEach(r=>{if(totals[r.driver]!==undefined)totals[r.driver]+=r.amount;});
  const active=DRIVERS.filter(d=>totals[d]>0);
  if(chartR)chartR.destroy();
  chartR=new Chart(document.getElementById('chartReport'),{
    type:'doughnut',
    data:{
      labels:active,
      datasets:[{
        data:active.map(d=>Math.round(totals[d])),
        backgroundColor:active.map(d=>COLORS[DRIVERS.indexOf(d)]),
        borderWidth:2,borderColor:'#fff'
      }]
    },
    options:{
      responsive:true,maintainAspectRatio:false,
      plugins:{
        legend:{position:'right',labels:{font:{family:'Sarabun',size:12},padding:12}},
        tooltip:{callbacks:{label:v=>`${v.label}: ฿${v.raw.toLocaleString()}`}}
      }
    }
  });
}

function showToast(msg){
  const t=document.getElementById('toast');
  t.textContent=msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2800);
}

function init(){
  updateNavDate();
  loadCachedPrices();
  populateDriverFilter();
  populateModalDrivers();
  setView('month');
  renderReportPage();
}

init();
function saveFuel() {
    const startTime = document.getElementById('f-start-time').value;
    const endTime = document.getElementById('f-end-time').value;
    
    // ตรวจสอบความถูกต้อง (Validation)
    if(!startTime || !endTime) {
        alert("กรุณาระบุเวลาให้ครบถ้วน");
        return;
    }
    
    // โค้ดบันทึกข้อมูลเดิมของคุณ...
}
</script>
</body>
</html>
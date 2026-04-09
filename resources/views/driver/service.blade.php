<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบจัดการเซอร์วิส</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
  --navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;
  --accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;
  --bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;
  --border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;
  --shadow:0 2px 12px rgba(26,39,68,.08);--radius:12px;--radius-sm:8px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}

/* NAVBAR */
.navbar{background:var(--navy);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.navbar-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:16px}
.navbar-brand .logo{width:36px;height:36px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px}
.navbar-brand .sub{font-size:11px;font-weight:300;opacity:.65;letter-spacing:1px}
.nav-date{color:rgba(255,255,255,.75);font-size:13px}
.nav-user{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border-radius:20px;padding:5px 14px 5px 6px}
.nav-avatar{width:28px;height:28px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#fff}
.nav-user span{color:#fff;font-size:13px;font-weight:500}

/* LAYOUT */
.layout{display:flex;min-height:calc(100vh - 60px)}

/* SIDEBAR */
.sidebar{width:220px;background:var(--navy-dark);flex-shrink:0;padding:20px 0}
.sidebar-back{padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:8px}
.sidebar-back a{display:flex;align-items:center;gap:10px;padding:10px 22px;color:rgba(255,255,255,.75);text-decoration:none;font-size:14px;transition:all .2s}
.sidebar-back a:hover{color:#fff;background:rgba(255,255,255,.06)}
.sidebar-section{font-size:10px;font-weight:600;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:16px 22px 6px;text-transform:uppercase}
.sidebar-menu{list-style:none}
.sidebar-menu a{display:flex;align-items:center;gap:10px;padding:11px 22px;color:rgba(255,255,255,.6);text-decoration:none;font-size:14px;transition:all .2s;border-left:3px solid transparent;cursor:pointer}
.sidebar-menu a:hover{color:rgba(255,255,255,.9);background:rgba(255,255,255,.05)}
.sidebar-menu a.active{color:#fff;background:rgba(79,142,247,.15);border-left-color:var(--accent);font-weight:500}
.si{font-size:15px;width:20px;text-align:center}

/* MAIN */
.main{flex:1;overflow-x:hidden;padding:28px}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.page-title{font-size:22px;font-weight:600;color:var(--navy)}
.page-subtitle{font-size:13px;color:var(--text2);margin-top:3px}

/* TAB SWITCHER */
.tab-bar{display:flex;background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:4px;gap:4px;box-shadow:var(--shadow);margin-bottom:22px;width:fit-content}
.tab-btn{padding:8px 22px;border-radius:var(--radius-sm);border:none;font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;background:transparent;color:var(--text2);transition:all .2s}
.tab-btn.active{background:var(--accent);color:#fff;box-shadow:0 2px 8px rgba(79,142,247,.3)}
.tab-panel{display:none}
.tab-panel.active{display:block}

/* METRICS */
.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px}
.metrics-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.metric-card{background:var(--surface);border-radius:var(--radius);padding:18px 20px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.blue::before{background:var(--accent)}
.metric-card.green::before{background:var(--accent2)}
.metric-card.amber::before{background:var(--accent3)}
.metric-card.red::before{background:var(--accent4)}
.metric-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:10px}
.metric-icon.blue{background:rgba(79,142,247,.1)}
.metric-icon.green{background:rgba(56,201,138,.1)}
.metric-icon.amber{background:rgba(245,166,35,.12)}
.metric-icon.red{background:rgba(232,93,93,.1)}
.metric-label{font-size:12px;color:var(--text2);font-weight:500;margin-bottom:4px}
.metric-value{font-size:26px;font-weight:700;color:var(--text);line-height:1;font-family:'IBM Plex Mono',monospace}
.metric-sub{font-size:11px;color:var(--text3);margin-top:4px}

/* CHARTS */
.charts-grid{display:grid;grid-template-columns:1fr 2fr;gap:16px;margin-bottom:20px}
.chart-card{background:var(--surface);border-radius:var(--radius);padding:20px;border:1px solid var(--border);box-shadow:var(--shadow)}
.chart-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px}
.chart-sub{font-size:12px;color:var(--text2);margin-bottom:14px}

/* LEGEND */
.legend{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:12px}
.legend-item{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)}
.legend-dot{width:10px;height:10px;border-radius:2px;flex-shrink:0}

/* BOTTOM ROW */
.bottom-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}

/* RANK LIST */
.rank-item{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)}
.rank-item:last-child{border-bottom:none}
.rank-num{width:24px;height:24px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--text2);flex-shrink:0;border:1px solid var(--border)}
.rank-num.top{background:rgba(79,142,247,.1);color:var(--accent);border-color:rgba(79,142,247,.2)}
.rank-info{flex:1;min-width:0}
.rank-name{font-size:13px;font-weight:600}
.rank-detail{font-size:11px;color:var(--text3);margin-top:1px}
.rank-bar-wrap{width:90px;flex-shrink:0}
.rank-bar-bg{background:var(--surface2);border-radius:4px;height:6px;overflow:hidden;border:1px solid var(--border)}
.rank-bar-fill{height:6px;border-radius:4px;background:var(--accent)}
.rank-val{font-size:11px;font-weight:600;text-align:right;margin-top:3px;color:var(--text2)}

/* ACTIVITY */
.act-item{display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)}
.act-item:last-child{border-bottom:none}
.act-dot{width:8px;height:8px;border-radius:50%;margin-top:4px;flex-shrink:0}
.act-dot.check{background:var(--accent2)}.act-dot.fix{background:var(--accent3)}.act-dot.urgent{background:var(--accent4)}
.act-info{flex:1}
.act-name{font-size:13px;font-weight:600}
.act-meta{font-size:11px;color:var(--text3);margin-top:1px}
.act-cost{font-size:13px;font-weight:600;color:var(--text);flex-shrink:0;margin-top:1px}

/* TABLE */
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px}
.table-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.table-title{font-size:15px;font-weight:600;color:var(--text)}
.table-sub{font-size:12px;color:var(--text2);margin-top:2px}
.badge-count{background:var(--accent);color:#fff;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:8px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:var(--surface2);padding:10px 16px;text-align:left;font-weight:600;font-size:12px;color:var(--text2);border-bottom:1px solid var(--border);white-space:nowrap}
thead th.center{text-align:center}
tbody td{padding:12px 16px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody td.center{text-align:center}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}

/* BADGES */
.type-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.type-check{background:rgba(56,201,138,.12);color:#1a7a4d}
.type-fix{background:rgba(245,166,35,.12);color:#a06a00}
.type-urgent{background:rgba(232,93,93,.12);color:#c0392b}
.cnt-badge{display:inline-flex;align-items:center;justify-content:center;min-width:22px;padding:1px 7px;border-radius:10px;font-size:11px;font-weight:600}
.cnt-check{background:rgba(56,201,138,.12);color:#1a7a4d}
.cnt-fix{background:rgba(245,166,35,.12);color:#a06a00}
.cnt-urgent{background:rgba(232,93,93,.12);color:#c0392b}
.badge{display:inline-flex;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin-left:5px}
.badge-check{background:rgba(56,201,138,.12);color:#1a7a4d}
.badge-fix{background:rgba(245,166,35,.12);color:#a06a00}
.badge-urgent{background:rgba(232,93,93,.12);color:#c0392b}

/* BUTTONS & INPUT */
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:#3a7ce0}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}.btn-outline:hover{background:var(--surface2)}
.btn-danger-sm{background:rgba(232,93,93,.1);color:var(--accent4);border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:13px;transition:all .15s}
.btn-danger-sm:hover{background:var(--accent4);color:#fff}
.search-box{padding:8px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:13px;color:var(--text);background:var(--surface2);outline:none;width:220px}
.search-box:focus{border-color:var(--accent)}

/* FILTER BAR */
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:12px 18px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:20px;box-shadow:var(--shadow)}
.filter-bar select{font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px}
.filter-label{font-size:13px;font-weight:500;color:var(--text2)}

/* PLATE TAG */
.plate-tag{background:var(--surface2);border:1px solid var(--border);border-radius:4px;font-size:11px;color:var(--text2);padding:2px 7px;font-family:monospace;font-weight:600}

/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.show{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:16px;font-weight:600;color:var(--text)}
.modal-close{width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:20px 24px;display:flex;flex-direction:column;gap:14px}
.modal-footer{padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}

.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.mono{font-family:'IBM Plex Mono',monospace}

@media(max-width:900px){.charts-grid{grid-template-columns:1fr}.bottom-row{grid-template-columns:1fr}.metrics-4{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.sidebar{display:none}.main{padding:16px}.metrics{grid-template-columns:1fr 1fr}.metrics-4{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-brand">
    <div class="logo">🛠️</div>
    <div>
      <div>ระบบจัดการเซอร์วิส</div>
      <div class="sub">SERVICE MANAGEMENT SYSTEM</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:16px">
    <div class="nav-date" id="navDate"></div>
    <div class="nav-user"><div class="nav-avatar">A</div><span>Admin</span></div>
  </div>
</nav>

<div class="layout">

  <aside class="sidebar">
    <div class="sidebar-back">
      <a href="javascript:history.back()"><span class="si">←</span> ย้อนกลับ</a>
    </div>
    <div class="sidebar-section">หลัก</div>
    <ul class="sidebar-menu">
      <li><a href="{{ route('oil') }}"><span class="si">⛽</span>ติดตามน้ำมัน</a></li>
      <li><a onclick="switchTab('service')" id="sb-service" class="active"><span class="si">🛠️</span>Service</a></li>
    </ul>
    <div class="sidebar-section">รายงาน</div>
    <ul class="sidebar-menu">
      <li><a onclick="switchTab('report')" id="sb-report"><span class="si">📊</span>สรุปรายงาน</a></li>
    </ul>
  </aside>

  <div class="main">

    {{-- =================== TAB: SERVICE =================== --}}
    <div class="tab-panel active" id="panel-service">

      <div class="page-header">
        <div>
          <div class="page-title">ระบบจัดการเซอร์วิสรถ</div>
          <div class="page-subtitle">บันทึกและติดตามประวัติการซ่อมบำรุง</div>
        </div>
        <div class="tab-bar">
          <button class="tab-btn active" onclick="switchTab('service')">🛠️ บันทึกงาน</button>
          <button class="tab-btn" onclick="switchTab('report')">📊 สรุปรายงาน</button>
        </div>
      </div>

      <div class="metrics">
        <div class="metric-card blue">
          <div class="metric-icon blue">📋</div>
          <div class="metric-label">จำนวนงานทั้งหมด</div>
          <div class="metric-value" id="stat-total">0</div>
          <div class="metric-sub">รายการ</div>
        </div>
        <div class="metric-card green">
          <div class="metric-icon green">💰</div>
          <div class="metric-label">ค่าใช้จ่ายรวม</div>
          <div class="metric-value" id="stat-cost">0</div>
          <div class="metric-sub">บาท</div>
        </div>
        <div class="metric-card red">
          <div class="metric-icon red">📈</div>
          <div class="metric-label">เฉลี่ยต่อรายการ</div>
          <div class="metric-value" id="stat-avg">0</div>
          <div class="metric-sub">บาท</div>
        </div>
      </div>

      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-title">สัดส่วนประเภทงาน</div>
          <div class="chart-sub">แบ่งตามประเภทการซ่อม</div>
          <div style="position:relative;height:220px">
            <canvas id="typeChart" role="img" aria-label="donut chart ประเภทงาน">donut chart</canvas>
          </div>
        </div>
        <div class="chart-card">
          <div class="chart-title">แนวโน้มค่าใช้จ่าย</div>
          <div class="chart-sub">ค่าใช้จ่ายตามลำดับเวลา</div>
          <div style="position:relative;height:220px">
            <canvas id="costChart" role="img" aria-label="line chart ค่าใช้จ่าย">line chart</canvas>
          </div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-header">
          <div>
            <span class="table-title">ประวัติการรับบริการ</span>
            <span class="badge-count" id="badge-count">0</span>
          </div>
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" class="search-box" placeholder="ค้นหาทะเบียน..." oninput="doSearch(this.value)">
            <button class="btn btn-primary" onclick="openModal()">+ เพิ่มข้อมูล</button>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th><th>วันที่</th><th>ทะเบียน</th>
                <th>ประเภท</th><th>ค่าใช้จ่าย (฿)</th><th>จัดการ</th>
              </tr>
            </thead>
            <tbody id="tbody"></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =================== TAB: REPORT =================== --}}
    <div class="tab-panel" id="panel-report">

      <div class="page-header">
        <div>
          <div class="page-title">สรุปรายงานเซอร์วิสรถ</div>
          <div class="page-subtitle">ภาพรวมค่าใช้จ่ายและสถิติการซ่อมบำรุง</div>
        </div>
        <div class="tab-bar">
          <button class="tab-btn" onclick="switchTab('service')">🛠️ บันทึกงาน</button>
          <button class="tab-btn active" onclick="switchTab('report')">📊 สรุปรายงาน</button>
        </div>
      </div>

      <div class="filter-bar">
        <span class="filter-label">กรอง:</span>
        <select id="rSelYear" onchange="renderReport()">
          <option value="all">ทุกปี</option>
        </select>
        <select id="rSelMonth" onchange="renderReport()">
          <option value="all">ทุกเดือน</option>
          <option value="01">ม.ค.</option><option value="02">ก.พ.</option>
          <option value="03">มี.ค.</option><option value="04">เม.ย.</option>
          <option value="05">พ.ค.</option><option value="06">มิ.ย.</option>
          <option value="07">ก.ค.</option><option value="08">ส.ค.</option>
          <option value="09">ก.ย.</option><option value="10">ต.ค.</option>
          <option value="11">พ.ย.</option><option value="12">ธ.ค.</option>
        </select>
        <select id="rSelPlate" onchange="renderReport()">
          <option value="all">ทะเบียนทั้งหมด</option>
        </select>
        <select id="rSelType" onchange="renderReport()">
          <option value="all">ทุกประเภท</option>
          <option value="check">เช็คระยะ</option>
          <option value="fix">ซ่อมบำรุง</option>
          <option value="urgent">ฉุกเฉิน</option>
        </select>
      </div>

      <div class="metrics-4">
        <div class="metric-card blue">
          <div class="metric-icon blue">📋</div>
          <div class="metric-label">รายการทั้งหมด</div>
          <div class="metric-value" id="r-total">0</div>
          <div class="metric-sub">รายการ</div>
        </div>
        <div class="metric-card green">
          <div class="metric-icon green">💰</div>
          <div class="metric-label">ค่าใช้จ่ายรวม</div>
          <div class="metric-value" id="r-cost">฿0</div>
          <div class="metric-sub">บาท</div>
        </div>
        <div class="metric-card amber">
          <div class="metric-icon amber">📈</div>
          <div class="metric-label">เฉลี่ยต่อครั้ง</div>
          <div class="metric-value" id="r-avg">฿0</div>
          <div class="metric-sub">บาท/ครั้ง</div>
        </div>
        <div class="metric-card red">
          <div class="metric-icon red">🚨</div>
          <div class="metric-label">งานฉุกเฉิน</div>
          <div class="metric-value" id="r-urgent">0</div>
          <div class="metric-sub">ครั้ง</div>
        </div>
      </div>

      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-title">สัดส่วนประเภทงาน</div>
          <div class="chart-sub">แบ่งตามประเภทการซ่อม</div>
          <div class="legend" id="rDonutLegend"></div>
          <div style="position:relative;height:200px">
            <canvas id="rDonutChart" role="img" aria-label="donut chart ประเภทงาน report">donut</canvas>
          </div>
        </div>
        <div class="chart-card">
          <div class="chart-title">ค่าใช้จ่ายรายเดือน</div>
          <div class="chart-sub">แนวโน้มค่าซ่อมบำรุง</div>
          <div class="legend">
            <span class="legend-item"><span class="legend-dot" style="background:#85B7EB"></span>ค่าใช้จ่าย (฿)</span>
          </div>
          <div style="position:relative;height:200px">
            <canvas id="rBarChart" role="img" aria-label="bar chart รายเดือน">bar</canvas>
          </div>
        </div>
      </div>

      <div class="bottom-row">
        <div class="chart-card">
          <div class="chart-title">รถค่าใช้จ่ายสูงสุด</div>
          <div class="chart-sub">เรียงตามค่าซ่อมรวม</div>
          <div id="rRankList" style="margin-top:8px"></div>
        </div>
        <div class="chart-card">
          <div class="chart-title">รายการล่าสุด</div>
          <div class="chart-sub">5 รายการล่าสุด</div>
          <div id="rActivityList" style="margin-top:8px"></div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-header">
          <div>
            <div class="table-title">สรุปรายคัน</div>
            <div class="table-sub">ค่าใช้จ่ายและจำนวนครั้งแยกตามทะเบียน</div>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th><th>ทะเบียน</th>
                <th class="center">ครั้ง</th>
                <th class="center">เช็คระยะ</th>
                <th class="center">ซ่อมบำรุง</th>
                <th class="center">ฉุกเฉิน</th>
                <th>รวม (฿)</th>
                <th>เฉลี่ย (฿)</th>
              </tr>
            </thead>
            <tbody id="rSumTable"></tbody>
          </table>
        </div>
      </div>

    </div>{{-- /panel-report --}}

  </div>
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="serviceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">เพิ่มรายการซ่อมบำรุง</div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div>
        <label class="form-label">วันที่</label>
        <input type="date" id="inputDate" class="form-control">
      </div>
      <div>
        <label class="form-label">ทะเบียนรถ</label>
        <select id="inputName" class="form-control">
          <option value="">— เลือกทะเบียนรถ —</option>
          <option value="1 ฉผ 1276">1 ฉผ 1276</option>
          <option value="1 ฉผ 3181">1 ฉผ 3181</option>
          <option value="2 ฉธ 1620">2 ฉธ 1620</option>
          <option value="2ฉธ1619">2ฉธ1619</option>
          <option value="3ฉมก6071">3ฉมก6071</option>
          <option value="City 8กค6309">City 8กค6309</option>
          <option value="City 9 กค4815">City 9 กค4815</option>
          <option value="แจ๊ส 9กธ4830">แจ๊ส 9กธ4830</option>
        </select>
      </div>
      <div>
        <label class="form-label">ประเภทงาน</label>
        <select id="inputType" class="form-control">
          <option value="check">เช็คระยะ</option>
          <option value="fix">ซ่อมบำรุง</option>
          <option value="urgent">ฉุกเฉิน</option>
        </select>
      </div>
      <div>
        <label class="form-label">ค่าใช้จ่าย (บาท)</label>
        <input type="number" id="inputAmount" class="form-control" placeholder="0.00" step="0.01">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal()">ยกเลิก</button>
      <button class="btn btn-primary" onclick="submitData()">💾 บันทึก</button>
    </div>
  </div>
</div>

<script>
let records = [];
let query   = '';
let typeChart, costChart, rDonutChart, rBarChart;
const MONTHS_TH = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const TYPE_LABEL = {check:'เช็คระยะ', fix:'ซ่อมบำรุง', urgent:'ฉุกเฉิน'};

// ===== NAV DATE =====
function updateNavDate() {
  const now = new Date();
  const days = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัส','ศุกร์','เสาร์'];
  const months = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const el = document.getElementById('navDate');
  if (el) el.textContent = `วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()+543}`;
}

// ===== TAB SWITCH =====
function switchTab(tab) {
  document.getElementById('panel-service').classList.toggle('active', tab === 'service');
  document.getElementById('panel-report').classList.toggle('active', tab === 'report');
  document.getElementById('sb-service').classList.toggle('active', tab === 'service');
  document.getElementById('sb-report').classList.toggle('active', tab === 'report');
  if (tab === 'report') {
    syncReportFilters();
    renderReport();
  }
}

// ===== INIT CHARTS (Service Tab) =====
function initCharts() {
  typeChart = new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
      labels: ['เช็คระยะ','ซ่อมบำรุง','ฉุกเฉิน'],
      datasets: [{ data:[0,0,0], backgroundColor:['#4f8ef7','#f5a623','#e85d5d'], borderWidth:0 }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{ position:'bottom', labels:{ font:{family:'Sarabun',size:12}, padding:14 } } },
      cutout:'68%'
    }
  });

  costChart = new Chart(document.getElementById('costChart'), {
    type: 'line',
    data: { labels:[], datasets:[{ label:'ค่าใช้จ่าย', data:[], borderColor:'#4f8ef7', backgroundColor:'rgba(79,142,247,.1)', fill:true, tension:0.4, pointRadius:5, pointBackgroundColor:'#4f8ef7' }] },
    options: {
      responsive:true, maintainAspectRatio:false,
      scales: {
        y:{ beginAtZero:true, ticks:{ font:{size:11}, callback:v=>'฿'+v.toLocaleString() }, grid:{color:'rgba(0,0,0,.04)'} },
        x:{ ticks:{font:{size:11}}, grid:{display:false} }
      },
      plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:v=>'฿'+Number(v.raw).toLocaleString() } } }
    }
  });
}

// ===== RENDER SERVICE TAB =====
function render() {
  const filtered = query ? records.filter(r => r.name.includes(query)) : records;
  const typeLabel = { check:'เช็คระยะ', fix:'ซ่อมบำรุง', urgent:'ฉุกเฉิน' };

  document.getElementById('tbody').innerHTML = filtered.length
    ? filtered.map((r,i) => `
        <tr>
          <td style="color:var(--text3)">${i+1}</td>
          <td class="mono" style="font-size:12px">${r.date}</td>
          <td style="font-weight:600;color:var(--navy)">${r.name}</td>
          <td><span class="type-badge type-${r.type}">${typeLabel[r.type]}</span></td>
          <td class="mono" style="font-weight:600;color:var(--accent2)">฿${Number(r.amount).toLocaleString()}</td>
          <td><button class="btn-danger-sm" onclick="del(${i})" title="ลบ">🗑</button></td>
        </tr>`).join('')
    : `<tr><td colspan="6"><div class="empty-state"><div style="font-size:40px;margin-bottom:10px;opacity:.3">🔧</div><div>ยังไม่มีรายการ</div></div></td></tr>`;

  const total     = records.length;
  const totalCost = records.reduce((s,r) => s + Number(r.amount), 0);
  document.getElementById('stat-total').textContent  = total;
  document.getElementById('stat-cost').textContent   = totalCost.toLocaleString();
  document.getElementById('stat-avg').textContent    = total ? Math.round(totalCost/total).toLocaleString() : '0';
  document.getElementById('badge-count').textContent = total;

  const counts = {check:0, fix:0, urgent:0};
  records.forEach(r => counts[r.type]++);
  typeChart.data.datasets[0].data = [counts.check, counts.fix, counts.urgent];
  typeChart.update();

  const sorted = [...records].sort((a,b) => new Date(a.date) - new Date(b.date));
  costChart.data.labels = sorted.map(r => r.date);
  costChart.data.datasets[0].data = sorted.map(r => Number(r.amount));
  costChart.update();
}

// ===== MODAL =====
function openModal() {
  document.getElementById('inputDate').value = new Date().toISOString().slice(0,10);
  document.getElementById('serviceModal').classList.add('show');
}
function closeModal() { document.getElementById('serviceModal').classList.remove('show'); }

function submitData() {
  const data = {
    date:   document.getElementById('inputDate').value,
    name:   document.getElementById('inputName').value,
    type:   document.getElementById('inputType').value,
    amount: document.getElementById('inputAmount').value,
  };
  if (!data.date || !data.name || !data.amount) { alert('กรุณากรอกข้อมูลให้ครบ'); return; }
  records.push(data);
  render();
  closeModal();
  document.getElementById('inputName').value   = '';
  document.getElementById('inputAmount').value = '';
  syncReportFilters();
}

function doSearch(val) { query = val; render(); }
function del(i) { if (confirm('ยืนยันการลบ?')) { records.splice(i,1); render(); } }

// ===== SYNC REPORT FILTERS =====
function syncReportFilters() {
  const years  = [...new Set(records.map(r => r.date.slice(0,4)))].sort().reverse();
  const plates = [...new Set(records.map(r => r.name))].sort();

  const selYear  = document.getElementById('rSelYear');
  const selPlate = document.getElementById('rSelPlate');
  const prevYear  = selYear.value;
  const prevPlate = selPlate.value;

  selYear.innerHTML  = '<option value="all">ทุกปี</option>';
  selPlate.innerHTML = '<option value="all">ทะเบียนทั้งหมด</option>';

  years.forEach(y  => { const o=document.createElement('option'); o.value=y; o.textContent='ปี '+(parseInt(y)+543); selYear.appendChild(o); });
  plates.forEach(p => { const o=document.createElement('option'); o.value=p; o.textContent=p; selPlate.appendChild(o); });

  if (years.includes(prevYear))   selYear.value  = prevYear;
  if (plates.includes(prevPlate)) selPlate.value = prevPlate;
}

// ===== RENDER REPORT =====
function renderReport() {
  const year  = document.getElementById('rSelYear').value;
  const month = document.getElementById('rSelMonth').value;
  const plate = document.getElementById('rSelPlate').value;
  const type  = document.getElementById('rSelType').value;

  const f = records.filter(r => {
    const yr = r.date.slice(0,4);
    const mo = r.date.slice(5,7);
    return (year==='all'||yr===year) && (month==='all'||mo===month)
        && (plate==='all'||r.name===plate) && (type==='all'||r.type===type);
  });

  // Metrics
  const total  = f.length;
  const cost   = f.reduce((s,r)=>s+Number(r.amount),0);
  const avg    = total ? Math.round(cost/total) : 0;
  const urgent = f.filter(r=>r.type==='urgent').length;
  document.getElementById('r-total').textContent  = total.toLocaleString();
  document.getElementById('r-cost').textContent   = '฿'+cost.toLocaleString();
  document.getElementById('r-avg').textContent    = '฿'+avg.toLocaleString();
  document.getElementById('r-urgent').textContent = urgent.toLocaleString();

  // Donut
  const counts = {check:0, fix:0, urgent:0};
  f.forEach(r => counts[r.type]++);
  const dData   = [counts.check, counts.fix, counts.urgent];
  const dColors = ['#38c98a','#f5a623','#e85d5d'];
  const dLabels = ['เช็คระยะ','ซ่อมบำรุง','ฉุกเฉิน'];
  const tot1 = f.length || 1;
  document.getElementById('rDonutLegend').innerHTML = dLabels.map((l,i)=>
    `<span class="legend-item"><span class="legend-dot" style="background:${dColors[i]}"></span>${l} ${Math.round(dData[i]/tot1*100)}%</span>`
  ).join('');

  if (!rDonutChart) {
    rDonutChart = new Chart(document.getElementById('rDonutChart'), {
      type:'doughnut',
      data:{ labels:dLabels, datasets:[{ data:dData, backgroundColor:dColors, borderWidth:0 }] },
      options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, cutout:'68%' }
    });
  } else { rDonutChart.data.datasets[0].data=dData; rDonutChart.update(); }

  // Bar
  const monthly = Array.from({length:12},(_,i)=>({m:i,cost:0}));
  f.forEach(r=>{ const m=parseInt(r.date.slice(5,7))-1; monthly[m].cost+=Number(r.amount); });
  const active = monthly.filter(m=>m.cost>0);
  const bLabels = active.map(m=>MONTHS_TH[m.m]);
  const bData   = active.map(m=>m.cost);

  if (!rBarChart) {
    rBarChart = new Chart(document.getElementById('rBarChart'), {
      type:'bar',
      data:{ labels:bLabels, datasets:[{ label:'ค่าใช้จ่าย', data:bData, backgroundColor:'#85B7EB', borderRadius:5, borderSkipped:false }] },
      options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:false}, tooltip:{callbacks:{label:v=>'฿'+v.raw.toLocaleString()}} },
        scales:{
          y:{ beginAtZero:true, ticks:{font:{size:11},callback:v=>v>=1000?'฿'+(v/1000)+'k':'฿'+v}, grid:{color:'rgba(0,0,0,.04)'} },
          x:{ ticks:{font:{size:11}}, grid:{display:false} }
        }
      }
    });
  } else { rBarChart.data.labels=bLabels; rBarChart.data.datasets[0].data=bData; rBarChart.update(); }

  // Rank
  const byPlate = {};
  f.forEach(r=>{ if(!byPlate[r.name])byPlate[r.name]={cost:0,n:0}; byPlate[r.name].cost+=Number(r.amount); byPlate[r.name].n++; });
  const sorted = Object.entries(byPlate).sort((a,b)=>b[1].cost-a[1].cost).slice(0,6);
  const maxCost = sorted[0]?.[1].cost||1;
  document.getElementById('rRankList').innerHTML = sorted.length
    ? sorted.map(([p,d],i)=>`
        <div class="rank-item">
          <div class="rank-num ${i<3?'top':''}">${i+1}</div>
          <div class="rank-info">
            <div class="rank-name"><span class="plate-tag">${p}</span></div>
            <div class="rank-detail">${d.n} ครั้ง</div>
          </div>
          <div class="rank-bar-wrap">
            <div class="rank-bar-bg"><div class="rank-bar-fill" style="width:${Math.round(d.cost/maxCost*100)}%"></div></div>
            <div class="rank-val">฿${d.cost.toLocaleString()}</div>
          </div>
        </div>`).join('')
    : '<div class="empty-state" style="padding:16px">ไม่มีข้อมูล</div>';

  // Activity
  const last5 = [...f].sort((a,b)=>b.date.localeCompare(a.date)).slice(0,5);
  document.getElementById('rActivityList').innerHTML = last5.length
    ? last5.map(r=>`
        <div class="act-item">
          <div class="act-dot ${r.type}"></div>
          <div class="act-info">
            <div class="act-name"><span class="plate-tag">${r.name}</span><span class="badge badge-${r.type}">${TYPE_LABEL[r.type]}</span></div>
            <div class="act-meta">${r.date}</div>
          </div>
          <div class="act-cost">฿${Number(r.amount).toLocaleString()}</div>
        </div>`).join('')
    : '<div class="empty-state" style="padding:16px">ไม่มีข้อมูล</div>';

  // Sum Table
  const byP = {};
  f.forEach(r=>{ if(!byP[r.name])byP[r.name]={check:0,fix:0,urgent:0,cost:0,n:0}; byP[r.name][r.type]++; byP[r.name].cost+=Number(r.amount); byP[r.name].n++; });
  const rows = Object.entries(byP).sort((a,b)=>b[1].cost-a[1].cost);
  document.getElementById('rSumTable').innerHTML = rows.length
    ? rows.map(([p,d],i)=>`
        <tr>
          <td style="color:var(--text3)">${i+1}</td>
          <td><span class="plate-tag">${p}</span></td>
          <td class="center">${d.n}</td>
          <td class="center"><span class="cnt-badge cnt-check">${d.check}</span></td>
          <td class="center"><span class="cnt-badge cnt-fix">${d.fix}</span></td>
          <td class="center"><span class="cnt-badge cnt-urgent">${d.urgent}</span></td>
          <td style="font-weight:600;color:var(--accent2)">฿${d.cost.toLocaleString()}</td>
          <td style="color:var(--text2)">฿${Math.round(d.cost/d.n).toLocaleString()}</td>
        </tr>`).join('')
    : '<tr><td colspan="8"><div class="empty-state">ยังไม่มีข้อมูล กรุณาบันทึกงานก่อน</div></td></tr>';
}

// ===== BOOT =====
document.addEventListener('DOMContentLoaded', () => {
  updateNavDate();
  initCharts();
  render();
});
</script>
</body>
</html>
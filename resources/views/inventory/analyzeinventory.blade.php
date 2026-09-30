<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>วิเคราะห์สินค้า - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}

    /* Loader */
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(4px)}
    .ov.on{opacity:1;visibility:visible}
    .sp{width:48px;height:48px;border:5px solid rgba(255,255,255,.25);border-top:5px solid #5B65F3;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 14px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .ov p{color:#fff;font-size:16px;font-weight:600}

    /* Topbar / Sidebar (theme = inventorydashboard) */
    .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 24px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:36px;border-radius:6px}
    .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
    .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
    .btn-home{font-size:13px;font-weight:600;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;text-decoration:none;transition:all .2s}
    .btn-home:hover{background:#f9fafb;border-color:#9ca3af}
    .hamburger{background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;display:flex;flex-direction:column;gap:5px;flex-shrink:0;transition:background .2s}
    .hamburger span{display:block;width:20px;height:2px;background:#374151;transition:background .2s}
    .hamburger:hover{background:#f3f4f6}
    .hamburger:hover span{background:#5B65F3}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-280px;width:260px;height:100vh;z-index:1600;transition:left .3s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:4px 0 24px rgba(0,0,0,.08)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #e5e7eb;min-height:64px}
    .sb-head img{height:32px;border-radius:6px}
    .sb-head span{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .sb-close{background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;font-weight:bold;padding:4px 8px;border-radius:6px;transition:all .2s}
    .sb-close:hover{background:#f3f4f6;color:#111827}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 0}
    .sb-sec{padding:12px 20px 6px;font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.05em;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#374151;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none;transition:all .2s;border-radius:0 8px 8px 0;margin-right:8px}
    .sb-item:hover{background:#f9fafb;border-left-color:#5B65F3;color:#111827}
    .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#5B65F3;font-weight:600}

    #content{padding:88px 16px 24px;width:100%;max-width:1500px;margin:0 auto}
    .card{margin-bottom:20px;padding:20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .card h2{font-size:20px;font-weight:700;color:#111827;margin-bottom:16px;letter-spacing:-0.025em}

    /* Filter row + multi dropdown */
    .filter-row{display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:10px}
    .filter-row > label{padding-top:10px;font-size:14px;font-weight:600;color:#374151;white-space:nowrap}
    .bd-wrap{position:relative;flex:1;min-width:200px;max-width:560px}
    .bd-btn{display:flex;align-items:center;justify-content:space-between;width:100%;min-height:40px;padding:5px 12px;background:#fff;border:1px solid #d1d5db;border-radius:8px;cursor:pointer;font-family:inherit;gap:8px;text-align:left;transition:all .2s}
    .bd-btn:hover{border-color:#9ca3af}
    .bd-chips{display:flex;flex-wrap:wrap;gap:5px;flex:1;align-items:center;min-height:26px}
    .bd-chip{display:inline-flex;align-items:center;gap:4px;background:#EEF2FF;color:#4F46E5;border:1px solid #C7D2FE;padding:2px 9px;font-size:13px;font-weight:600;border-radius:12px}
    .bd-chip button{background:none;border:none;cursor:pointer;color:#4F46E5;font-size:13px;line-height:1;padding:0 0 0 2px;opacity:.7}
    .bd-chip button:hover{opacity:1}
    .bd-caret{flex-shrink:0;font-size:11px;color:#9ca3af}
    .bd-placeholder{color:#9ca3af;font-size:14px}
    .bd-menu{position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #d1d5db;border-radius:8px;z-index:500;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:280px;overflow:hidden;display:flex;flex-direction:column}
    .bd-menu.hidden{display:none!important}
    .bd-search-box{padding:8px;border-bottom:1px solid #f0f0f0;flex-shrink:0}
    .bd-search-box input{width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:14px;font-family:inherit;background:#f9fafb}
    .bd-search-box input:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .bd-list{overflow-y:auto;flex:1}
    .bd-item2{display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;font-size:14px;font-family:inherit}
    .bd-item2:hover{background:#f5f7ff}
    .bd-item2.checked{color:#4F46E5}
    .bd-cb{width:16px;height:16px;border:2px solid #cbd5e1;border-radius:4px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:#fff;font-size:11px}
    .bd-cb.on{background:#5B65F3;border-color:#5B65F3;color:#fff}
    .bd-footer-bar{display:flex;justify-content:space-between;padding:7px 10px;border-top:1px solid #f0f0f0;flex-shrink:0;background:#f9fafb}
    .bd-footer-bar button{font-size:13px;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:6px;padding:5px 12px;cursor:pointer;font-family:inherit}
    .bd-footer-bar button:hover{background:#f3f4f6}
    .name-search{padding:9px 12px;font-size:14px;font-family:inherit;border:1px solid #d1d5db;border-radius:8px;background:#fff;flex:1;min-width:180px;max-width:320px}
    .name-search:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}

    /* Summary */
    .summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px}
    .summary-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .summary-card .s-label{font-size:13px;color:#6b7280;font-weight:600;margin-bottom:6px}
    .summary-card .s-value{font-size:22px;font-weight:700;color:#5B65F3;font-variant-numeric:tabular-nums}

    /* Toolbar + table */
    .toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:0;flex-wrap:wrap;gap:8px;padding:14px 16px;border-bottom:1px solid #e5e7eb}
    .count-label{font-size:14px;color:#6b7280;font-weight:500}
    .btn{padding:8px 16px;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;border:none;border-radius:8px;transition:all .2s}
    .btn-csv{background:#10b981;color:#fff}
    .btn-csv:hover{background:#059669}
    .btn-csv:disabled{opacity:.5;cursor:not-allowed}
    .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:#f9fafb}
    th{color:#374151;padding:12px 12px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#f9fafb;z-index:5;border:1px solid #e5e7eb;border-top:none}
    th:first-child{border-left:none} th:last-child{border-right:none}
    td{padding:10px 12px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:middle;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    td:first-child{border-left:none} td:last-child{border-right:none}
    td.wrap{white-space:normal;overflow:visible}
    tbody tr:hover{background:#f9fafb}
    .name-link{color:#5B65F3;cursor:pointer;text-decoration:none;font-weight:500}
    .name-link:hover{color:#4F46E5;text-decoration:underline}
    .badge{display:inline-block;padding:3px 9px;font-size:12px;font-weight:600;white-space:nowrap;border-radius:6px}
    .b-klang{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-asset{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3e{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
    .b-3in{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-3em{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3el{background:#fef9c3;color:#713f12;border:1px solid #fde047}
    .b-hd{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .b-ep{background:#fce7f3;color:#9d174d;border:1px solid #f9a8d4}
    .b-3p{background:#f0fdf4;color:#14532d;border:1px solid #86efac}
    .b-all{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}

    .pagi{display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:center;padding:16px}
    .pagi button{padding:7px 13px;font-size:14px;font-family:inherit;border:1px solid #d1d5db;background:#fff;border-radius:8px;cursor:pointer;color:#374151;transition:all .2s}
    .pagi button:hover:not(:disabled):not(.active){background:#f9fafb;border-color:#9ca3af}
    .pagi button.active{background:#5B65F3;border-color:#5B65F3;color:#fff;font-weight:700}
    .pagi button:disabled{opacity:.45;cursor:not-allowed}
    .pagi-info{font-size:14px;color:#6b7280;margin:0 6px}

    /* Modals */
    .tx-ov,.az-ov{position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;justify-content:center;align-items:center;z-index:5000;backdrop-filter:blur(3px);padding:12px}
    .tx-ov.on,.az-ov.on{display:flex}
    .tx-modal{background:#fff;border-radius:14px;width:99vw;max-width:1300px;height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,.35);overflow:hidden}
    .az-modal{background:#fff;border-radius:14px;width:99vw;max-width:1100px;height:92vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,.35);overflow:hidden}
    .tx-head,.az-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#5B65F3;color:#fff;flex-wrap:wrap;gap:8px;flex-shrink:0}
    .tx-head h3,.az-head h3{font-size:18px;font-weight:700}
    .tx-badge,.az-badge{background:#fff;color:#4F46E5;padding:3px 12px;font-size:15px;font-weight:700;border-radius:6px}
    .tx-xbtn,.az-xbtn{background:rgba(255,255,255,.2);color:#fff;border:none;width:32px;height:32px;cursor:pointer;font-size:15px;font-weight:700;flex-shrink:0;border-radius:8px}
    .tx-xbtn:hover,.az-xbtn:hover{background:rgba(255,255,255,.35)}
    .tx-tabs{display:flex;border-bottom:1px solid #e5e7eb;background:#f9fafb}
    .tx-tab{padding:12px 22px;font-size:15px;font-weight:600;cursor:pointer;border:none;border-bottom:3px solid transparent;font-family:inherit;background:none;color:#6b7280}
    .tx-tab.active{border-bottom-color:#5B65F3;color:#5B65F3}
    .tx-tab:hover:not(.active){background:#f0f0f0}
    .tx-body,.az-body{flex:1;overflow:auto}
    .az-body{padding:18px}
    .tx-foot{padding:12px 20px;border-top:1px solid #e5e7eb;font-size:14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;color:#6b7280}
    .btn-analyze{padding:7px 14px;font-size:14px;font-weight:600;background:#fff;color:#4F46E5;border:none;border-radius:8px;cursor:pointer;font-family:inherit}

    .tx-tbl{width:100%;border-collapse:collapse;font-size:14px}
    .tx-tbl thead{background:#f9fafb;position:sticky;top:0;z-index:5}
    .tx-tbl th{color:#374151;padding:11px 10px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;border-bottom:1px solid #e5e7eb}
    .tx-tbl td{padding:9px 10px;border-bottom:1px solid #f0f0f0;color:#1f2937;font-size:14px;vertical-align:middle;word-break:break-word}
    .tx-tbl tbody tr:hover{background:#f9fafb}
    .tx-type{font-weight:700;color:#fff;padding:3px 8px;font-size:12px;white-space:nowrap;border-radius:6px}
    .t-in{background:#10b981}.t-ret{background:#0ea5e9}.t-sell{background:#ef4444}.t-bor{background:#f59e0b}.t-wit{background:#f97316}

    .cost-box{padding:16px 18px;background:#f9fafb;border-bottom:1px solid #e5e7eb}
    .cost-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px}
    .cost-item{background:#fff;padding:12px 14px;border:1px solid #e5e7eb;border-radius:10px}
    .cost-item .cl{font-size:13px;color:#6b7280;font-weight:600;margin-bottom:4px}
    .cost-item .cv{font-size:18px;font-weight:700;color:#5B65F3}

    .az-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:22px}
    .az-stat{background:#f5f7ff;border-left:4px solid #5B65F3;padding:13px 15px;border-radius:10px}
    .az-stat .asl{font-size:13px;color:#6b7280;font-weight:600;margin-bottom:4px}
    .az-stat .asv{font-size:20px;font-weight:700;color:#111827}
    .az-stat .asu{font-size:12px;color:#9ca3af;margin-left:3px}
    .az-yr-tbl{width:100%;border-collapse:collapse;font-size:14px;margin-bottom:24px}
    .az-yr-tbl th{background:#f9fafb;color:#374151;padding:9px 10px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb}
    .az-yr-tbl td{padding:9px 10px;border-bottom:1px solid #f0f0f0}
    .az-yr-tbl td.num{text-align:right}
    .az-chart-wrap{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:20px}
    .az-chart-title{font-size:15px;font-weight:600;color:#111827;margin-bottom:10px}
    .az-chart-canvas{position:relative;height:260px}
    .freq-badge{display:inline-block;padding:2px 9px;font-size:13px;font-weight:700;border-radius:6px}
    .freq-high{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .freq-mid{background:#fef9c3;color:#713f12;border:1px solid #fde047}
    .freq-low{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .muted-note{color:#6b7280}
  </style>
</head>
<body>

<div class="ov" id="ov"><div><div class="sp"></div><p>กำลังโหลด...</p></div></div>

<!-- TX MODAL -->
<div class="tx-ov" id="txOv">
  <div class="tx-modal">
    <div class="tx-head">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <h3>รายละเอียดสินค้า</h3>
        <span class="tx-badge" id="txId">-</span>
        <span id="txName" style="font-size:14px;opacity:.9;"></span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <button class="btn-analyze" onclick="openAnalyzeFromTx()">วิเคราะห์สินค้านี้</button>
        <button class="tx-xbtn" onclick="closeTx()">&#10005;</button>
      </div>
    </div>
    <div class="tx-tabs">
      <button class="tx-tab active" id="tabTx" onclick="switchTab('tx')">ประวัติ Transaction</button>
      <button class="tx-tab" id="tabCost" onclick="switchTab('cost')">ต้นทุน / มูลค่า</button>
    </div>
    <div class="tx-body" id="txBody"></div>
    <div class="tx-foot">
      <span id="txFoot">-</span>
      <span id="txCostFoot" style="font-weight:700;color:#111827;"></span>
    </div>
  </div>
</div>

<!-- ANALYZE POPUP -->
<div class="az-ov" id="azOv">
  <div class="az-modal">
    <div class="az-head">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <h3>วิเคราะห์สินค้า</h3>
        <span class="az-badge" id="azId">-</span>
        <span id="azName" style="font-size:14px;opacity:.9;"></span>
      </div>
      <button class="az-xbtn" onclick="closeAnalyze()">&#10005;</button>
    </div>
    <div class="az-body" id="azBody"></div>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.vehicles') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รถบริษัท</a>
    <div class="sb-sec">รายงาน</div>
    <a class="sb-item cur"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>วิเคราะห์สินค้า</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name"> ผู้ใช้: {{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
    <a href="http://server_update:8000/solist" class="btn-home">หน้าหลัก</a>
  </div>
</div>

<div id="content">
  <div class="card">
    <h2>วิเคราะห์สินค้าตาม Brand</h2>
    <div class="filter-row">
      <label>Brand:</label>
      <div class="bd-wrap" id="bdw">
        <button class="bd-btn" onclick="toggleBrandMenu()">
          <div class="bd-chips" id="bdChips"><span class="bd-placeholder">ทุก Brand</span></div>
          <span class="bd-caret">&#9660;</span>
        </button>
        <div class="bd-menu hidden" id="bdMenu">
          <div class="bd-search-box"><input type="text" id="bdSearch" placeholder="ค้นหา brand..." oninput="filterBrandMenu(this.value)"></div>
          <div class="bd-list" id="bdItems"></div>
          <div class="bd-footer-bar"><button onclick="selectAllBrands()">เลือกทั้งหมด</button><button onclick="clearBrands()">ล้าง</button></div>
        </div>
      </div>
    </div>
    <div class="filter-row">
      <label>ปี:</label>
      <div class="bd-wrap" id="yrw" style="max-width:320px;">
        <button class="bd-btn" onclick="toggleYearMenu()">
          <div class="bd-chips" id="yrChips"><span class="bd-placeholder">ทุกปี</span></div>
          <span class="bd-caret">&#9660;</span>
        </button>
        <div class="bd-menu hidden" id="yrMenu">
          <div class="bd-list" id="yrItems"></div>
          <div class="bd-footer-bar"><button onclick="selectAllYears()">เลือกทั้งหมด</button><button onclick="clearYears()">ล้าง</button></div>
        </div>
      </div>
      <label>ประเภท:</label>
      <div class="bd-wrap" id="typew" style="max-width:260px;">
        <button class="bd-btn" onclick="toggleTypeMenu()">
          <div class="bd-chips" id="typeChips"><span class="bd-placeholder">ทุกประเภท</span></div>
          <span class="bd-caret">&#9660;</span>
        </button>
        <div class="bd-menu hidden" id="typeMenu">
          <div class="bd-list" id="typeItems"></div>
          <div class="bd-footer-bar"><button onclick="selectAllTypes()">เลือกทั้งหมด</button><button onclick="clearTypes()">ล้าง</button></div>
        </div>
      </div>
      <label>บริษัท:</label>
      <div class="bd-wrap" id="compw" style="max-width:320px;">
        <button class="bd-btn" onclick="toggleCompMenu()">
          <div class="bd-chips" id="compChips"><span class="bd-placeholder">ทุกบริษัท</span></div>
          <span class="bd-caret">&#9660;</span>
        </button>
        <div class="bd-menu hidden" id="compMenu">
          <div class="bd-list" id="compItems"></div>
          <div class="bd-footer-bar"><button onclick="selectAllComps()">เลือกทั้งหมด</button><button onclick="clearComps()">ล้าง</button></div>
        </div>
      </div>
      <label>ค้นหาชื่อ:</label>
      <input type="text" id="nameSearch" class="name-search" placeholder="พิมพ์ชื่อสินค้า..." oninput="onFilterChange()">
    </div>
  </div>

  <div class="summary-grid" id="summaryGrid" style="display:none;">
    <div class="summary-card"><div class="s-label">ประเภทสินค้า</div><div class="s-value" id="sProducts">0</div></div>
    <div class="summary-card"><div class="s-label">รับเข้ารวม (ชิ้น)</div><div class="s-value" id="sReceived">0</div></div>
    <div class="summary-card"><div class="s-label">คงเหลือรวม (ชิ้น)</div><div class="s-value" id="sStock">0</div></div>
    <div class="summary-card"><div class="s-label">มูลค่าคงเหลือ (บาท)</div><div class="s-value" id="sValue">0</div></div>
  </div>

  <div class="tbl-wrap">
    <div class="toolbar">
      <span class="count-label" id="countLabel">กำลังโหลด...</span>
      <button class="btn btn-csv" id="csvBtn" onclick="downloadCSV()" disabled>&#8681; ดาวน์โหลด CSV</button>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <colgroup>
          <col style="width:160px;"><col style="min-width:220px;width:auto;">
          <col style="width:90px;"><col style="width:100px;"><col style="width:90px;">
          <col style="width:140px;"><col style="width:150px;">
          <col style="width:100px;"><col style="width:100px;">
        </colgroup>
        <thead><tr>
          <th>ID Item</th><th>ชื่อสินค้า</th>
          <th style="text-align:center;">ครั้งที่รับ</th>
          <th style="text-align:center;">รับเข้า/ชิ้น</th>
          <th style="text-align:center;">คงเหลือ</th>
          <th style="text-align:right;">ราคาเฉลี่ย (&#3647;)</th>
          <th style="text-align:right;">มูลค่าคงเหลือ</th>
          <th>ประเภท</th><th>บริษัท</th>
        </tr></thead>
        <tbody id="tb"><tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">กำลังโหลด...</td></tr></tbody>
      </table>
    </div>
    <div class="pagi" id="pagi"></div>
  </div>
</div>

<script>
const DATA_URL = "{{ url('/api/inventory/brand-analysis') }}";

// ==================== GLOBALS ====================
let allItems=[],allTx=[],selectedBrands=new Set(),itemMap={},txCache={};
let curTxItem=null,curTab="tx",_rawBrands=[],bdMenuOpen=false;
const PAGE_SIZE=100;
let curPage=1,filteredRows=[];
let azPriceChart=null,azQtyChart=null;

function showOv(){document.getElementById("ov").classList.add("on");}
function hideOv(){document.getElementById("ov").classList.remove("on");}
function openSB(){document.getElementById("sidebar").classList.add("open");document.getElementById("sbOv").classList.add("open");}
function closeSB(){document.getElementById("sidebar").classList.remove("open");document.getElementById("sbOv").classList.remove("open");}

// ==================== LOAD DATA (Laravel) ====================
async function loadData(){
  showOv();
  try{
    const res=await fetch(DATA_URL,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    if(!res.ok) throw new Error('HTTP '+res.status);
    const d=await res.json();
    allItems=d.items||[]; _rawBrands=d.brands||[]; allTx=d.tx||[];
    itemMap={}; allItems.forEach(i=>{itemMap[i.iditem]=i;});
    buildTxCache();
    renderBrandMenuItems(""); onFilterChange();
  }catch(e){ alert("โหลดข้อมูลล้มเหลว: "+(e.message||e)); }
  finally{ hideOv(); }
}
function buildTxCache(){txCache={};allTx.forEach(function(t){if(!txCache[t.iditem])txCache[t.iditem]=[];txCache[t.iditem].push(t);});}

// ==================== BRAND DROPDOWN ====================
function toggleBrandMenu(){
  bdMenuOpen=!bdMenuOpen;
  document.getElementById("bdMenu").classList.toggle("hidden",!bdMenuOpen);
  if(bdMenuOpen){document.getElementById("bdSearch").value="";renderBrandMenuItems("");setTimeout(()=>document.getElementById("bdSearch").focus(),50);}
}
function closeBrandMenu(){bdMenuOpen=false;document.getElementById("bdMenu").classList.add("hidden");}
function renderBrandMenuItems(filter){
  const f=(filter||"").toLowerCase(),list=document.getElementById("bdItems");list.innerHTML="";
  (_rawBrands||[]).filter(b=>b&&(!f||b.toLowerCase().includes(f))).forEach(function(b){
    const on=selectedBrands.has(b),d=document.createElement("div");
    d.className="bd-item2"+(on?" checked":"");
    d.innerHTML=`<div class="bd-cb ${on?"on":""}">${on?"&#10003;":""}</div><span>${esc(b)}</span>`;
    d.onclick=function(){toggleBrand(b);};list.appendChild(d);
  });
}
function filterBrandMenu(v){renderBrandMenuItems(v);}
function toggleBrand(b){if(selectedBrands.has(b))selectedBrands.delete(b);else selectedBrands.add(b);renderBrandChips();renderBrandMenuItems(document.getElementById("bdSearch").value||"");onFilterChange();}
function removeBrandChip(e,b){e.stopPropagation();selectedBrands.delete(b);renderBrandChips();renderBrandMenuItems("");onFilterChange();}
function selectAllBrands(){(_rawBrands||[]).forEach(b=>{if(b)selectedBrands.add(b);});renderBrandChips();renderBrandMenuItems("");onFilterChange();}
function clearBrands(){selectedBrands.clear();renderBrandChips();renderBrandMenuItems("");onFilterChange();}
function renderBrandChips(){
  const ch=document.getElementById("bdChips");
  if(selectedBrands.size===0){ch.innerHTML=`<span class="bd-placeholder">ทุก Brand</span>`;return;}
  ch.innerHTML=[...selectedBrands].map(b=>`<span class="bd-chip">${esc(b)}<button onclick="removeBrandChip(event,'${escJs(b)}')">&#10005;</button></span>`).join("");
}

// ==================== COMPUTE COST ====================
function computeCost(iditem, years){
  const rows=(txCache[iditem]||[]).filter(function(t){
    const isIn=t.type==="รับเข้าสต็อก"||t.type==="คืนเข้าสต็อก";
    const inYear=years.length===0||years.includes((t.timestamp||"").substring(6,10));
    return isIn&&inYear;
  });
  let totalQty=0,totalCost=0,countUsed=0,hasNoCurrPrice=false;
  rows.forEach(function(t){
    const qty=parseFloat(t.quantity)||0;const cp=t.currency_price;
    if(cp===null||cp===undefined){hasNoCurrPrice=true;return;}
    if(cp<=0)return;
    totalQty+=qty;totalCost+=qty*cp;countUsed++;
  });
  const received=rows.reduce((s,t)=>s+(parseFloat(t.quantity)||0),0);
  return{count:countUsed,received,avg:totalQty>0?totalCost/totalQty:0,hasNoCurrPrice,rows};
}
function hasTxInYear(iditem, years){
  if(years.length===0)return true;
  return(txCache[iditem]||[]).some(t=>(t.type==="รับเข้าสต็อก"||t.type==="คืนเข้าสต็อก")&&years.includes((t.timestamp||"").substring(6,10)));
}

// ==================== FILTER + RENDER ====================
function onFilterChange(){curPage=1;applyFiltersAndRender();}
function applyFiltersAndRender(){
  const years=getSelectedYears();
  const nameQ=(document.getElementById("nameSearch").value||"").trim().toLowerCase();
  let items=selectedBrands.size===0?allItems:allItems.filter(i=>selectedBrands.has((i.brand||"").trim()));
  if(years.length>0)items=items.filter(i=>hasTxInYear(i.iditem,years));
  if(selectedTypes.size>0)items=items.filter(i=>selectedTypes.has((i.typeitem||"").trim()));
  if(selectedComps.size>0)items=items.filter(i=>selectedComps.has((i.privilege||"").trim()));
  if(nameQ)items=items.filter(i=>(i.name||"").toLowerCase().includes(nameQ));
  filteredRows=items.map(function(item){
    const cost=computeCost(item.iditem,years);
    const stock=parseFloat(item.quantity)||0;
    return{item,cost,stock,value:cost.avg*stock};
  }).sort((a,b)=>b.value-a.value);

  const sg=document.getElementById("summaryGrid");
  if(!filteredRows.length){
    document.getElementById("tb").innerHTML=`<tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">ไม่พบสินค้า</td></tr>`;
    document.getElementById("countLabel").textContent="ไม่พบรายการ";
    document.getElementById("csvBtn").disabled=true;
    document.getElementById("pagi").innerHTML="";sg.style.display="none";return;
  }
  let totR=0,totS=0,totV=0;
  filteredRows.forEach(r=>{totR+=r.cost.received;totS+=r.stock;totV+=r.value;});
  document.getElementById("sProducts").textContent=filteredRows.length.toLocaleString("th-TH");
  document.getElementById("sReceived").textContent=totR.toLocaleString("th-TH");
  document.getElementById("sStock").textContent=totS.toLocaleString("th-TH");
  document.getElementById("sValue").textContent=totV.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2});
  sg.style.display="grid";
  const brandLabel=selectedBrands.size===0?"ทั้งหมด":[...selectedBrands].join(", ");
  const yearLabel=years.length===0?"ทุกปี":[...years].sort((a,b)=>b-a).join(", ");
  document.getElementById("countLabel").textContent=brandLabel+" · ปี "+yearLabel+(nameQ?" · \""+nameQ+"\"":" ")+" · "+filteredRows.length+" รายการ";
  document.getElementById("csvBtn").disabled=false;
  renderPage();
}
function renderPage(){
  const total=filteredRows.length,totalPages=Math.ceil(total/PAGE_SIZE);
  if(curPage>totalPages)curPage=1;
  const start=(curPage-1)*PAGE_SIZE,pageRows=filteredRows.slice(start,start+PAGE_SIZE);
  const tb=document.getElementById("tb");tb.innerHTML="";
  pageRows.forEach(function(r){
    const tr=document.createElement("tr");
    const eid=escJs(r.item.iditem),ename=escJs(r.item.name);
    tr.innerHTML=
      `<td style="font-size:13px;color:#6b7280;">${esc(r.item.iditem)}</td>`+
      `<td class="wrap"><span class="name-link" onclick="openTx('${eid}','${ename}')">${esc(r.item.name)}</span></td>`+
      `<td style="text-align:center;">${r.cost.count}</td>`+
      `<td style="text-align:center;"><strong>${r.cost.received.toLocaleString("th-TH")}</strong></td>`+
      `<td style="text-align:center;"><strong>${r.stock.toLocaleString("th-TH")}</strong></td>`+
      `<td style="text-align:right;">${r.cost.avg>0?r.cost.avg.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td>`+
      `<td style="text-align:right;font-weight:700;color:${r.value>0?"#059669":"#374151"};">${r.value>0?r.value.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td>`+
      `<td>${typeBadge(r.item.typeitem)}</td>`+
      `<td>${privBadge(r.item.privilege)}</td>`;
    tb.appendChild(tr);
  });
  renderPagi(totalPages);
}
function renderPagi(totalPages){
  const pagi=document.getElementById("pagi");
  if(totalPages<=1){pagi.innerHTML="";return;}
  let html=`<button ${curPage===1?"disabled":""} onclick="goPage(${curPage-1})">&#9664;</button>`;
  const win=5;let lo=Math.max(1,curPage-Math.floor(win/2)),hi=Math.min(totalPages,lo+win-1);
  if(hi-lo+1<win)lo=Math.max(1,hi-win+1);
  if(lo>1)html+=`<button onclick="goPage(1)">1</button>${lo>2?'<span class="pagi-info">…</span>':""}`;
  for(let p=lo;p<=hi;p++)html+=`<button class="${p===curPage?"active":""}" onclick="goPage(${p})">${p}</button>`;
  if(hi<totalPages)html+=`${hi<totalPages-1?'<span class="pagi-info">…</span>':""}<button onclick="goPage(${totalPages})">${totalPages}</button>`;
  html+=`<button ${curPage===totalPages?"disabled":""} onclick="goPage(${curPage+1})">&#9654;</button>`;
  html+=`<span class="pagi-info">หน้า ${curPage}/${totalPages} (${filteredRows.length} รายการ)</span>`;
  pagi.innerHTML=html;
}
function goPage(p){curPage=p;renderPage();window.scrollTo({top:0,behavior:'smooth'});}

// ==================== TX MODAL ====================
function openTx(id,name){
  curTxItem={id,name};
  document.getElementById("txId").textContent=id;
  document.getElementById("txName").textContent=decodeURIComponent((name||"").replace(/\\'/g,"'"));
  curTab="tx";
  document.getElementById("tabTx").classList.add("active");
  document.getElementById("tabCost").classList.remove("active");
  renderTxTab();
  document.getElementById("txOv").classList.add("on");
}
function closeTx(){document.getElementById("txOv").classList.remove("on");curTxItem=null;}
function switchTab(t){
  curTab=t;
  document.getElementById("tabTx").classList.toggle("active",t==="tx");
  document.getElementById("tabCost").classList.toggle("active",t==="cost");
  if(t==="tx")renderTxTab();else renderCostTab();
}
function renderTxTab(){
  if(!curTxItem)return;
  const rows=(txCache[curTxItem.id]||[]).slice().sort((a,b)=>_tsNum(b.timestamp)-_tsNum(a.timestamp));
  const body=document.getElementById("txBody");
  document.getElementById("txCostFoot").textContent="";
  if(!rows.length){body.innerHTML=`<div style="text-align:center;padding:50px;color:#9ca3af;">ไม่พบ Transaction</div>`;document.getElementById("txFoot").textContent="ไม่มีข้อมูล";return;}
  document.getElementById("txFoot").textContent="พบ "+rows.length+" รายการ";
  let html=`<table class="tx-tbl"><thead><tr>
    <th>วันที่</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ราคา/หน่วย (฿)</th>
  </tr></thead><tbody>`;
  rows.forEach(t=>{
    const up=parseFloat(t.unit_price)||0;const cp=t.currency_price;const cpVal=(cp!==null&&cp!==undefined)?parseFloat(cp):null;
    const upDisp=up>0?up.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-";
    let cpDisp=(cpVal===null||cpVal===undefined)?`<span class="muted-note">-</span>`:`<strong style="color:#059669;">${cpVal.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2})}</strong>`;
    html+=`<tr>
      <td>${esc(t.timestamp||"-")}</td><td>${esc(t.operator||"-")}</td>
      <td><span class="tx-type ${txTypeClass(t.type)}">${esc(t.type||"-")}</span></td>
      <td>${esc(t.bill||"-")}</td><td>${esc(t.product||"-")}</td>
      <td style="text-align:center;">${(parseFloat(t.quantity)||0).toLocaleString("th-TH")}</td>
      <td style="text-align:right;">${upDisp}</td><td style="text-align:right;">${cpDisp}</td>
    </tr>`;
  });
  html+=`</tbody></table>`;body.innerHTML=html;
}
function renderCostTab(){
  if(!curTxItem)return;
  const years=getSelectedYears();
  const cost=computeCost(curTxItem.id,years);
  const item=itemMap[curTxItem.id];
  const stock=item?parseFloat(item.quantity)||0:0;
  const value=cost.avg*stock;
  const body=document.getElementById("txBody");
  const yearLabel=years.length===0?"ทั้งหมด":[...years].sort((a,b)=>b-a).join(", ");
  document.getElementById("txFoot").textContent="ต้นทุน · ปี "+yearLabel;
  document.getElementById("txCostFoot").textContent="มูลค่าคงเหลือ: "+value.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2})+" บาท";
  let html=`<div class="cost-box"><div class="cost-grid">
    <div class="cost-item"><div class="cl">จำนวนครั้งที่มีราคา</div><div class="cv">${cost.count}</div></div>
    <div class="cost-item"><div class="cl">จำนวนรับเข้ารวม</div><div class="cv">${cost.received.toLocaleString("th-TH")} ชิ้น</div></div>
    <div class="cost-item"><div class="cl">คงเหลือปัจจุบัน</div><div class="cv">${stock.toLocaleString("th-TH")} ชิ้น</div></div>
    <div class="cost-item"><div class="cl">ราคาเฉลี่ย/หน่วย</div><div class="cv">${cost.avg>0?cost.avg.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"} ฿</div></div>
    <div class="cost-item"><div class="cl">มูลค่าคงเหลือ</div><div class="cv">${value>0?value.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"} ฿</div></div>
    ${cost.hasNoCurrPrice?'<div class="cost-item" style="border-left:3px solid #f59e0b;"><div class="cl" style="color:#b45309;">หมายเหตุ</div><div style="font-size:14px;color:#b45309;">มีรายการที่ไม่มีราคาต่อหน่วย</div></div>':''}
  </div></div>`;
  if(cost.rows.length){
    html+=`<table class="tx-tbl"><thead><tr><th>วันที่</th><th>ประเภท</th><th>เอกสาร</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ราคา/หน่วย (฿)</th></tr></thead><tbody>`;
    cost.rows.forEach(t=>{
      const up=parseFloat(t.unit_price)||0;const cp=t.currency_price;const cpVal=(cp!==null&&cp!==undefined)?parseFloat(cp):null;const noCP=cpVal===null||cpVal===undefined;
      html+=`<tr><td>${esc(t.timestamp||"-")}</td><td><span class="tx-type ${txTypeClass(t.type)}">${esc(t.type)}</span></td><td>${esc(t.bill||"-")}</td>
        <td style="text-align:center;">${(parseFloat(t.quantity)||0).toLocaleString("th-TH")}</td>
        <td style="text-align:right;">${up>0?up.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td>
        <td style="text-align:right;">${noCP?'<span class="muted-note">ไม่มีข้อมูล</span>':`<strong style="color:#059669;">${cpVal.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2})}</strong>`}</td></tr>`;
    });
    html+=`</tbody></table>`;
  }
  body.innerHTML=html;
}

// ==================== ANALYZE POPUP ====================
function openAnalyzeFromTx(){if(!curTxItem)return;openAnalyze(curTxItem.id,decodeURIComponent((curTxItem.name||"").replace(/\\'/g,"'")));}
function openAnalyze(iditem,name){
  document.getElementById("azId").textContent=iditem;
  document.getElementById("azName").textContent=name;
  if(azPriceChart){azPriceChart.destroy();azPriceChart=null;}
  if(azQtyChart){azQtyChart.destroy();azQtyChart=null;}
  buildAnalyzeBody(iditem);
  document.getElementById("azOv").classList.add("on");
}
function closeAnalyze(){document.getElementById("azOv").classList.remove("on");if(azPriceChart){azPriceChart.destroy();azPriceChart=null;}if(azQtyChart){azQtyChart.destroy();azQtyChart=null;}}
function buildAnalyzeBody(iditem){
  const body=document.getElementById("azBody");
  const allIn=(txCache[iditem]||[]).filter(t=>t.type==="รับเข้าสต็อก"||t.type==="คืนเข้าสต็อก");
  if(!allIn.length){body.innerHTML=`<div style="text-align:center;padding:60px;color:#9ca3af;">ไม่พบข้อมูลการรับเข้า</div>`;return;}
  const yearly={};
  allIn.forEach(t=>{
    const y=_getYear(t.timestamp);if(!y)return;
    if(!yearly[y])yearly[y]={qty:0,cost:0,countTx:0,countWithPrice:0};
    const qty=parseFloat(t.quantity)||0;const cp=t.currency_price;const cpVal=(cp!==null&&cp!==undefined)?parseFloat(cp):null;
    yearly[y].qty+=qty;yearly[y].countTx++;
    if(cpVal!==null&&cpVal>0){yearly[y].cost+=qty*cpVal;yearly[y].countWithPrice++;}
  });
  const years=Object.keys(yearly).sort((a,b)=>a-b);
  let totQty=0,totCost=0,totTx=0,allPrices=[];
  allIn.forEach(t=>{const qty=parseFloat(t.quantity)||0;totQty+=qty;totTx++;const cp=t.currency_price;const cpVal=(cp!==null&&cp!==undefined)?parseFloat(cp):null;if(cpVal!==null&&cpVal>0){totCost+=cpVal*qty;allPrices.push(cpVal);}});
  const avgAll=totQty>0?totCost/totQty:0;const minP=allPrices.length>0?Math.min(...allPrices):0;const maxP=allPrices.length>0?Math.max(...allPrices):0;
  const sortedTx=allIn.slice().sort((a,b)=>_tsNum(a.timestamp)-_tsNum(b.timestamp));
  let freqLabel="",freqClass="freq-low",freqNote="";
  if(sortedTx.length>=2){
    const gaps=[];for(let i=1;i<sortedTx.length;i++){const diff=(_tsNum(sortedTx[i].timestamp)-_tsNum(sortedTx[i-1].timestamp))/86400000;if(diff>0)gaps.push(diff);}
    if(gaps.length){const avgGap=gaps.reduce((a,b)=>a+b,0)/gaps.length;
      if(avgGap<30){freqLabel="บ่อยมาก";freqClass="freq-high";freqNote="เฉลี่ยทุก "+Math.round(avgGap)+" วัน";}
      else if(avgGap<90){freqLabel="ปานกลาง";freqClass="freq-mid";freqNote="เฉลี่ยทุก "+Math.round(avgGap)+" วัน";}
      else{freqLabel="นานๆ ครั้ง";freqClass="freq-low";freqNote="เฉลี่ยทุก "+Math.round(avgGap)+" วัน";}}
  }else{freqLabel="ข้อมูลน้อย";freqClass="freq-low";freqNote="มีเพียง "+sortedTx.length+" ครั้ง";}
  const item=itemMap[iditem];
  let html=`<div class="az-stats">
    <div class="az-stat"><div class="asl">รับเข้าทั้งหมด</div><div class="asv">${totTx} <span class="asu">ครั้ง</span></div></div>
    <div class="az-stat"><div class="asl">จำนวนรวม</div><div class="asv">${totQty.toLocaleString("th-TH")} <span class="asu">ชิ้น</span></div></div>
    <div class="az-stat"><div class="asl">ราคาเฉลี่ย/หน่วย</div><div class="asv">${avgAll>0?avgAll.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"} <span class="asu">฿</span></div></div>
    <div class="az-stat"><div class="asl">ราคาต่ำสุด</div><div class="asv">${minP>0?minP.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"} <span class="asu">฿</span></div></div>
    <div class="az-stat"><div class="asl">ราคาสูงสุด</div><div class="asv">${maxP>0?maxP.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"} <span class="asu">฿</span></div></div>
    <div class="az-stat"><div class="asl">ความถี่การซื้อ</div><div class="asv"><span class="freq-badge ${freqClass}">${freqLabel}</span><div style="font-size:13px;color:#9ca3af;margin-top:3px;">${freqNote}</div></div></div>
    ${item?`<div class="az-stat"><div class="asl">คงเหลือปัจจุบัน</div><div class="asv">${(parseFloat(item.quantity)||0).toLocaleString("th-TH")} <span class="asu">ชิ้น</span></div></div>`:""}
  </div>
  <div class="az-chart-wrap"><div class="az-chart-title">ราคาเฉลี่ย/หน่วย รายปี (฿)</div><div class="az-chart-canvas"><canvas id="azPriceCanvas"></canvas></div></div>
  <div class="az-chart-wrap"><div class="az-chart-title">จำนวนรับเข้า รายปี (ชิ้น)</div><div class="az-chart-canvas"><canvas id="azQtyCanvas"></canvas></div></div>
  <h3 style="font-size:16px;font-weight:700;color:#111827;margin:16px 0 10px;border-bottom:2px solid #e5e7eb;padding-bottom:6px;">สรุปรายปี</h3>
  <table class="az-yr-tbl"><thead><tr><th>ปี</th><th>ครั้งที่รับเข้า</th><th>จำนวน (ชิ้น)</th><th>ราคาเฉลี่ย (฿)</th><th>ต้นทุนรวม (฿)</th></tr></thead><tbody>`;
  years.forEach(y=>{const d=yearly[y],avg=d.qty>0?d.cost/d.qty:0;
    html+=`<tr><td><strong>${y}</strong></td><td class="num">${d.countTx}</td><td class="num">${d.qty.toLocaleString("th-TH")}</td>
      <td class="num">${avg>0?avg.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td>
      <td class="num">${d.cost>0?d.cost.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td></tr>`;});
  html+=`</tbody></table>
  <h3 style="font-size:16px;font-weight:700;color:#111827;margin:20px 0 10px;border-bottom:2px solid #e5e7eb;padding-bottom:6px;">รายการรับเข้าทั้งหมด</h3>
  <table class="tx-tbl"><thead><tr><th>วันที่</th><th>ประเภท</th><th>เอกสาร</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ราคา/หน่วย (฿)</th></tr></thead><tbody>`;
  sortedTx.slice().reverse().forEach(t=>{
    const up=parseFloat(t.unit_price)||0;const cp=t.currency_price;const cpVal=(cp!==null&&cp!==undefined)?parseFloat(cp):null;const noCP=cpVal===null||cpVal===undefined;
    html+=`<tr><td>${esc(t.timestamp||"-")}</td><td><span class="tx-type ${txTypeClass(t.type)}">${esc(t.type)}</span></td><td>${esc(t.bill||"-")}</td>
      <td style="text-align:center;">${(parseFloat(t.quantity)||0).toLocaleString("th-TH")}</td>
      <td style="text-align:right;">${up>0?up.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2}):"-"}</td>
      <td style="text-align:right;">${noCP?'<span class="muted-note">-</span>':cpVal<=0?'<span class="muted-note">0.00</span>':`<strong style="color:#059669;">${cpVal.toLocaleString("th-TH",{minimumFractionDigits:2,maximumFractionDigits:2})}</strong>`}</td></tr>`;
  });
  html+=`</tbody></table>`;body.innerHTML=html;
  const avgsByYear=years.map(y=>yearly[y].qty>0?yearly[y].cost/yearly[y].qty:0);
  const qtyByYear=years.map(y=>yearly[y].qty);
  const pc=document.getElementById("azPriceCanvas");
  if(pc){azPriceChart=new Chart(pc.getContext("2d"),{type:"line",data:{labels:years,datasets:[{label:"ราคาเฉลี่ย (฿)",data:avgsByYear,borderColor:"#5B65F3",backgroundColor:"rgba(91,101,243,.1)",borderWidth:2.5,fill:true,tension:0,pointRadius:5,pointBackgroundColor:"#5B65F3",pointBorderColor:"#fff",pointBorderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:false,ticks:{font:{family:"'Sarabun',Arial,sans-serif"},color:"#9ca3af"}},x:{grid:{display:false},ticks:{font:{family:"'Sarabun',Arial,sans-serif"},color:"#9ca3af"}}}}});}
  const qc=document.getElementById("azQtyCanvas");
  if(qc){azQtyChart=new Chart(qc.getContext("2d"),{type:"bar",data:{labels:years,datasets:[{label:"จำนวน (ชิ้น)",data:qtyByYear,backgroundColor:"rgba(139,92,246,.7)",borderColor:"#8b5cf6",borderWidth:1}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{font:{family:"'Sarabun',Arial,sans-serif"},color:"#9ca3af"}},x:{grid:{display:false},ticks:{font:{family:"'Sarabun',Arial,sans-serif"},color:"#9ca3af"}}}}});}
}

// ==================== CSV ====================
function downloadCSV(){
  if(!filteredRows.length){alert("ไม่มีข้อมูล");return;}
  const years=getSelectedYears();const bom="﻿";
  const header=["#","ID Item","ชื่อสินค้า","ครั้งที่รับ(มีราคา)","รับเข้า","คงเหลือ","ราคาเฉลี่ย/หน่วย(฿)","มูลค่าคงเหลือ(฿)","ประเภท","บริษัท","ยี่ห้อ","สถานที่","หมายเหตุ"];
  const lines=[header.join(",")];let totR=0,totS=0,totV=0;
  filteredRows.forEach((r,i)=>{
    lines.push([i+1,r.item.iditem,csvEsc(r.item.name),r.cost.count,r.cost.received,r.stock,
      r.cost.avg>0?r.cost.avg.toFixed(2):"",r.value>0?r.value.toFixed(2):"",
      csvEsc(r.item.typeitem),csvEsc(r.item.privilege),csvEsc(r.item.brand),csvEsc(r.item.location),
      r.cost.hasNoCurrPrice?"มีรายการไม่มี currency_price":""].join(","));
    totR+=r.cost.received;totS+=r.stock;totV+=r.value;
  });
  lines.push("");
  lines.push(["สรุป","","","","รับเข้ารวม: "+totR,"คงเหลือรวม: "+totS,"","มูลค่ารวม: "+totV.toFixed(2)].join(","));
  const blob=new Blob([bom+lines.join("\r\n")],{type:"text/csv;charset=utf-8;"});
  const a=document.createElement("a");a.href=URL.createObjectURL(blob);
  const bn=selectedBrands.size===0?"all":[...selectedBrands].join("-");
  const yn=years.length===0?"all":years.sort((a,b)=>b-a).join("-");
  a.download=bn+"-"+yn+"-brand.csv";
  document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(a.href);
}

// ==================== HELPERS ====================
function esc(s){return String(s??"").replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}
function escJs(s){return String(s??"").replace(/\\/g,"\\\\").replace(/'/g,"\\'");}
function csvEsc(v){const s=String(v||"");return(s.includes(",")||s.includes('"')||s.includes("\n"))?'"'+s.replace(/"/g,'""')+'"':s;}
function _tsNum(ts){if(!ts)return 0;const m=ts.match(/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})/);return m?new Date(m[3],m[2]-1,m[1],m[4],m[5],m[6]).getTime():0;}
function _getYear(ts){try{return ts.split(" ")[0].split("/")[2]||"";}catch(e){return "";}}
function txTypeClass(t){return{"รับเข้าสต็อก":"t-in","คืนเข้าสต็อก":"t-ret","ขายสินค้าออก":"t-sell","ยืมสินค้า":"t-bor","เบิกของ":"t-wit"}[t]||"";}
function typeBadge(t){if(!t)return`<span class="badge b-klang">-</span>`;if(t==="คลัง")return`<span class="badge b-klang">คลัง</span>`;if(t==="ทรัพย์สินบริษัท")return`<span class="badge b-asset">ทรัพย์สิน</span>`;return`<span class="badge b-klang">${esc(t)}</span>`;}
function privBadge(p){if(!p)return`<span class="badge b-all">-</span>`;const m={"3E":"b-3e","3IN":"b-3in","3EM":"b-3em","3EL":"b-3el","HD":"b-hd","EP":"b-ep","3P":"b-3p"};return`<span class="badge ${m[(p||"").trim()]||"b-all"}">${esc(p)}</span>`;}

// ==================== YEAR / TYPE / COMPANY DROPDOWNS ====================
const YEARS=["2026","2025","2024","2023","2022"];
let selectedYears=new Set(),yrMenuOpen=false;
function toggleYearMenu(){yrMenuOpen=!yrMenuOpen;document.getElementById("yrMenu").classList.toggle("hidden",!yrMenuOpen);if(yrMenuOpen)renderYearMenuItems();}
function closeYearMenu(){yrMenuOpen=false;document.getElementById("yrMenu").classList.add("hidden");}
function renderYearMenuItems(){const list=document.getElementById("yrItems");list.innerHTML="";YEARS.forEach(function(y){const on=selectedYears.has(y),d=document.createElement("div");d.className="bd-item2"+(on?" checked":"");d.innerHTML=`<div class="bd-cb ${on?"on":""}">${on?"&#10003;":""}</div><span>${y}</span>`;d.onclick=function(){toggleYear(y);};list.appendChild(d);});}
function toggleYear(y){if(selectedYears.has(y))selectedYears.delete(y);else selectedYears.add(y);renderYearChips();renderYearMenuItems();onFilterChange();}
function removeYearChip(e,y){e.stopPropagation();selectedYears.delete(y);renderYearChips();renderYearMenuItems();onFilterChange();}
function selectAllYears(){YEARS.forEach(y=>selectedYears.add(y));renderYearChips();renderYearMenuItems();onFilterChange();}
function clearYears(){selectedYears.clear();renderYearChips();renderYearMenuItems();onFilterChange();}
function renderYearChips(){const ch=document.getElementById("yrChips");if(selectedYears.size===0){ch.innerHTML=`<span class="bd-placeholder">ทุกปี</span>`;return;}const sorted=[...selectedYears].sort((a,b)=>b-a);ch.innerHTML=sorted.map(y=>`<span class="bd-chip">${y}<button onclick="removeYearChip(event,'${y}')">&#10005;</button></span>`).join("");}
function getSelectedYears(){return selectedYears.size===0?[]:([...selectedYears]);}

const TYPE_LIST=["คลัง","ทรัพย์สินบริษัท"];
let selectedTypes=new Set(),typeMenuOpen=false;
function toggleTypeMenu(){typeMenuOpen=!typeMenuOpen;document.getElementById("typeMenu").classList.toggle("hidden",!typeMenuOpen);if(typeMenuOpen)renderTypeMenuItems();}
function closeTypeMenu(){typeMenuOpen=false;document.getElementById("typeMenu").classList.add("hidden");}
function renderTypeMenuItems(){const list=document.getElementById("typeItems");list.innerHTML="";TYPE_LIST.forEach(function(t){const on=selectedTypes.has(t),d=document.createElement("div");d.className="bd-item2"+(on?" checked":"");d.innerHTML=`<div class="bd-cb ${on?"on":""}">${on?"&#10003;":""}</div><span>${esc(t)}</span>`;d.onclick=function(){toggleType(t);};list.appendChild(d);});}
function toggleType(t){if(selectedTypes.has(t))selectedTypes.delete(t);else selectedTypes.add(t);renderTypeChips();renderTypeMenuItems();onFilterChange();}
function removeTypeChip(e,t){e.stopPropagation();selectedTypes.delete(t);renderTypeChips();renderTypeMenuItems();onFilterChange();}
function selectAllTypes(){TYPE_LIST.forEach(t=>selectedTypes.add(t));renderTypeChips();renderTypeMenuItems();onFilterChange();}
function clearTypes(){selectedTypes.clear();renderTypeChips();renderTypeMenuItems();onFilterChange();}
function renderTypeChips(){const ch=document.getElementById("typeChips");if(selectedTypes.size===0){ch.innerHTML=`<span class="bd-placeholder">ทุกประเภท</span>`;return;}ch.innerHTML=[...selectedTypes].map(t=>`<span class="bd-chip">${esc(t)}<button onclick="removeTypeChip(event,'${escJs(t)}')">&#10005;</button></span>`).join("");}

const COMPANY_LIST=[{code:"3E",label:"Triple E Trading"},{code:"3IN",label:"Triple E Innovation"},{code:"3EM",label:"Triple E Empire Group"},{code:"3EL",label:"Triple E Lighting"},{code:"HD",label:"Hikari Denki"},{code:"EP",label:"Eita & Paul"},{code:"3P",label:"Triple P Factory & Eng"},{code:"AE&T",label:"AE&T International"}];
let selectedComps=new Set(),compMenuOpen=false;
function toggleCompMenu(){compMenuOpen=!compMenuOpen;document.getElementById("compMenu").classList.toggle("hidden",!compMenuOpen);if(compMenuOpen)renderCompMenuItems();}
function closeCompMenu(){compMenuOpen=false;document.getElementById("compMenu").classList.add("hidden");}
function renderCompMenuItems(){const list=document.getElementById("compItems");list.innerHTML="";COMPANY_LIST.forEach(function(c){const on=selectedComps.has(c.code),d=document.createElement("div");d.className="bd-item2"+(on?" checked":"");d.innerHTML=`<div class="bd-cb ${on?"on":""}">${on?"&#10003;":""}</div><span>${esc(c.code)} — ${esc(c.label)}</span>`;d.onclick=function(){toggleComp(c.code);};list.appendChild(d);});}
function toggleComp(code){if(selectedComps.has(code))selectedComps.delete(code);else selectedComps.add(code);renderCompChips();renderCompMenuItems();onFilterChange();}
function removeCompChip(e,code){e.stopPropagation();selectedComps.delete(code);renderCompChips();renderCompMenuItems();onFilterChange();}
function selectAllComps(){COMPANY_LIST.forEach(c=>selectedComps.add(c.code));renderCompChips();renderCompMenuItems();onFilterChange();}
function clearComps(){selectedComps.clear();renderCompChips();renderCompMenuItems();onFilterChange();}
function renderCompChips(){const ch=document.getElementById("compChips");if(selectedComps.size===0){ch.innerHTML=`<span class="bd-placeholder">ทุกบริษัท</span>`;return;}ch.innerHTML=[...selectedComps].map(code=>`<span class="bd-chip">${esc(code)}<button onclick="removeCompChip(event,'${escJs(code)}')">&#10005;</button></span>`).join("");}

// close dropdowns on outside click
document.addEventListener("click",function(e){
  if(!document.getElementById("bdw").contains(e.target)&&bdMenuOpen)closeBrandMenu();
  if(!document.getElementById("yrw").contains(e.target)&&yrMenuOpen)closeYearMenu();
  if(!document.getElementById("typew").contains(e.target)&&typeMenuOpen)closeTypeMenu();
  if(!document.getElementById("compw").contains(e.target)&&compMenuOpen)closeCompMenu();
});

// close modals on backdrop click
document.getElementById("txOv").addEventListener("click",e=>{if(e.target.id==="txOv")closeTx();});
document.getElementById("azOv").addEventListener("click",e=>{if(e.target.id==="azOv")closeAnalyze();});

if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",loadData);else loadData();
</script>
</body>
</html>

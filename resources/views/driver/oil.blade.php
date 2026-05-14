{{-- resources/views/driver/oil.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบติดตามน้ำมันรถ</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<style>
:root{
  --navy:#0f172a;
  --navy-light:#1e293b;
  --accent:#2563eb;
  --accent-hover:#1d4ed8;
  --green:#16a34a;
  --green-light:#dcfce7;
  --amber:#d97706;
  --amber-light:#fef3c7;
  --red:#dc2626;
  --red-light:#fee2e2;
  --purple:#7c3aed;
  --bg:#f8fafc;
  --surface:#fff;
  --surface2:#f1f5f9;
  --border:#e2e8f0;
  --border-strong:#cbd5e1;
  --text:#0f172a;
  --text2:#475569;
  --text3:#94a3b8;
  --shadow:0 1px 3px rgba(15,23,42,.04), 0 1px 2px rgba(15,23,42,.06);
  --shadow-md:0 4px 12px rgba(15,23,42,.06);
  --radius:14px;
  --radius-sm:10px;
  --radius-xs:8px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'IBM Plex Sans Thai','Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;-webkit-font-smoothing:antialiased}

/* ═══════ APP LAYOUT — Sidebar (ซ้าย) + Content (ขวา) ═══════ */
.app{display:flex;min-height:100vh}

/* ═══════ SIDEBAR ซ้าย ═══════ */
.sidebar{
  width:260px;
  flex-shrink:0;
  background:var(--surface);
  border-right:1px solid var(--border);
  display:flex;
  flex-direction:column;
  position:sticky;
  top:0;
  height:100vh;
  overflow-y:auto;
  z-index:200;
}
.sidebar-brand{
  padding:20px 22px 18px;
  border-bottom:1px solid var(--border);
}
.sidebar-brand .title{
  font-size:15px;
  font-weight:700;
  color:var(--text);
  letter-spacing:-.01em;
  display:flex;
  align-items:center;
  gap:8px;
}
.sidebar-brand .title .logo{
  width:30px;
  height:30px;
  border-radius:8px;
  background:linear-gradient(135deg,var(--accent) 0%,#3b82f6 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-size:15px;
  flex-shrink:0;
}
.sidebar-brand .sub{
  font-size:11px;
  color:var(--text3);
  margin-top:6px;
  margin-left:38px;
}

.sidebar-section{
  padding:14px 16px 8px;
}
.sidebar-section .label{
  font-size:10px;
  font-weight:700;
  color:var(--text3);
  text-transform:uppercase;
  letter-spacing:.08em;
  padding:0 8px 8px;
}

/* Nav items */
.nav-item{
  display:flex;
  align-items:center;
  gap:10px;
  padding:9px 12px;
  border-radius:var(--radius-xs);
  color:var(--text2);
  font-size:13.5px;
  font-weight:500;
  cursor:pointer;
  text-decoration:none;
  border:none;
  background:transparent;
  font-family:inherit;
  width:100%;
  text-align:left;
  transition:all .15s;
  margin-bottom:2px;
}
.nav-item:hover{background:var(--surface2);color:var(--text)}
.nav-item.active{background:rgba(37,99,235,.08);color:var(--accent);font-weight:600}
.nav-item .ic{font-size:15px;width:18px;text-align:center;flex-shrink:0}
.nav-item .badge-dot{
  margin-left:auto;
  width:6px;height:6px;border-radius:50%;
  background:var(--accent);
}

/* View tabs (สำหรับใน sidebar) — แนวตั้ง */
.sidebar-view-tabs{
  display:flex;
  flex-direction:column;
  background:var(--surface2);
  border-radius:var(--radius-xs);
  padding:3px;
  gap:2px;
  margin:0 8px;
}
.sidebar-view-tabs .view-tab{
  padding:7px 12px;
  border-radius:6px;
  font-size:13px;
  font-weight:500;
  cursor:pointer;
  color:var(--text2);
  border:none;
  background:transparent;
  font-family:inherit;
  transition:all .15s;
  text-align:left;
}
.sidebar-view-tabs .view-tab:hover{color:var(--text)}
.sidebar-view-tabs .view-tab.active{background:var(--surface);color:var(--text);box-shadow:0 1px 3px rgba(15,23,42,.08);font-weight:600}

/* Sidebar form controls */
.sidebar-control{
  margin:0 8px 8px;
}
.sidebar-control .lbl{
  font-size:10px;
  font-weight:700;
  color:var(--text3);
  text-transform:uppercase;
  letter-spacing:.06em;
  padding:0 4px 5px;
}
.sidebar .driver-select,
.sidebar .date-trigger{
  width:100%;
  height:36px;
  font-size:13px;
}

/* Add button */
.sidebar-add-btn{
  margin:8px 16px 0;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:7px;
  background:var(--accent);
  color:#fff;
  border:none;
  padding:10px 14px;
  border-radius:var(--radius-xs);
  font-size:13.5px;
  font-weight:600;
  cursor:pointer;
  font-family:inherit;
  transition:background .15s;
  width:calc(100% - 32px);
}
.sidebar-add-btn:hover{background:var(--accent-hover)}
.sidebar-add-btn-top{
  margin:14px 16px 6px;
  padding:11px 14px;
  box-shadow:0 2px 6px rgba(37,99,235,.2);
}

.sidebar-footer{
  margin-top:auto;
  padding:12px 22px 18px;
  border-top:1px solid var(--border);
  font-size:11px;
  color:var(--text3);
}
.sidebar-footer .live-time{
  display:flex;
  align-items:center;
  gap:6px;
  font-weight:500;
  color:var(--text2);
}
.sidebar-footer .live-time::before{
  content:'';
  width:6px;height:6px;border-radius:50%;
  background:var(--green);
  animation:pulse 1.5s infinite;
}

/* Content wrapper */
.content-wrap{flex:1;min-width:0}

/* Mobile sidebar toggle */
.sidebar-toggle{
  display:none;
  position:fixed;
  top:14px;left:14px;
  z-index:250;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius-xs);
  width:40px;height:40px;
  align-items:center;
  justify-content:center;
  font-size:18px;
  cursor:pointer;
  box-shadow:var(--shadow);
}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.4);z-index:190}
@media(max-width:900px){
  .sidebar{position:fixed;left:-280px;top:0;height:100vh;transition:left .25s ease}
  .sidebar.open{left:0;box-shadow:0 4px 20px rgba(0,0,0,.18)}
  .sidebar-toggle{display:flex}
  .sidebar-overlay.show{display:block}
  .content-wrap{padding-top:60px}
}

/* ═══════ TOP BAR — รวมทุกอย่างไว้ในแถบเดียวตามรูป ═══════ */
.topbar{
  background:var(--surface);
  border-bottom:1px solid var(--border);
  padding:14px 28px;
  display:flex;
  align-items:center;
  gap:18px;
  flex-wrap:wrap;
  position:sticky;
  top:0;
  z-index:200;
}
.topbar-brand{display:flex;align-items:center;gap:14px;flex-shrink:0}
.topbar-title{font-size:17px;font-weight:700;color:var(--text);letter-spacing:-.01em}
.topbar-sub{font-size:12px;color:var(--text3);font-weight:400;border-left:1px solid var(--border);padding-left:14px;margin-left:2px}
.topbar-actions{display:flex;align-items:center;gap:10px;margin-left:auto;flex-wrap:wrap}

/* View tabs (รายวัน / รายเดือน / รายปี) */
.view-tabs{
  display:flex;
  background:var(--surface2);
  border-radius:var(--radius-xs);
  padding:3px;
  gap:2px;
}
.view-tab{
  padding:6px 16px;
  border-radius:6px;
  font-size:13px;
  font-weight:500;
  cursor:pointer;
  color:var(--text2);
  border:none;
  background:transparent;
  font-family:inherit;
  transition:all .15s;
  white-space:nowrap;
}
.view-tab:hover{color:var(--text)}
.view-tab.active{background:var(--surface);color:var(--text);box-shadow:0 1px 3px rgba(15,23,42,.08);font-weight:600}

/* Date picker trigger */
.date-trigger{
  display:flex;
  align-items:center;
  gap:8px;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius-xs);
  padding:7px 14px;
  font-size:13px;
  font-weight:500;
  color:var(--text);
  cursor:pointer;
  transition:all .15s;
  font-family:inherit;
}
.date-trigger:hover{border-color:var(--border-strong)}
.date-trigger .arrow{font-size:9px;color:var(--text3)}

/* Primary button */
.btn-primary{
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:var(--accent);
  color:#fff;
  border:none;
  padding:8px 16px;
  border-radius:var(--radius-xs);
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  font-family:inherit;
  transition:background .15s;
}
.btn-primary:hover{background:var(--accent-hover)}
.btn-primary .plus{font-size:14px;line-height:1}

/* Outline button */
.btn-outline{
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:var(--surface);
  color:var(--text2);
  border:1px solid var(--border);
  padding:7px 14px;
  border-radius:var(--radius-xs);
  font-size:13px;
  font-weight:500;
  cursor:pointer;
  font-family:inherit;
  transition:all .15s;
  text-decoration:none;
}
.btn-outline:hover{background:var(--surface2);color:var(--text)}

/* ═══════ MAIN CONTENT ═══════ */
.main{padding:24px 28px;max-width:1600px;margin:0 auto}

/* ═══════ METRIC CARDS (4 cards บนสุด) ═══════ */
.metrics{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:16px;
  margin-bottom:24px;
}
@media(max-width:1100px){.metrics{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.metrics{grid-template-columns:1fr}}

.metric-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:18px 20px;
  position:relative;
  overflow:hidden;
  box-shadow:var(--shadow);
}
.metric-card::before{
  content:'';
  position:absolute;
  top:0;left:0;right:0;
  height:3px;
}
.metric-card.green::before{background:var(--green)}
.metric-card.blue::before{background:var(--accent)}
.metric-card.amber::before{background:#eab308}
.metric-card.red::before{background:var(--red)}

.metric-label{
  font-size:13px;
  color:var(--text2);
  font-weight:500;
  margin-bottom:8px;
}
.metric-row{display:flex;align-items:baseline;gap:6px;margin-bottom:0}
.metric-value{
  font-size:30px;
  font-weight:700;
  color:var(--text);
  letter-spacing:-.02em;
  line-height:1;
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
}
.metric-unit{font-size:13px;color:var(--text2);font-weight:500}
.metric-trend{
  font-size:12px;
  font-weight:500;
  display:flex;
  align-items:center;
  gap:4px;
  margin-top:6px;
}
.metric-trend.up{color:var(--green)}
.metric-trend.down{color:var(--red)}
.metric-trend.warn{color:var(--red)}
.metric-trend .ic{font-size:10px}

/* ═══════ TWO-COLUMN LAYOUT ═══════ */
.dual-grid{
  display:grid;
  grid-template-columns:1.55fr 1fr;
  gap:18px;
  margin-bottom:18px;
}
@media(max-width:1100px){.dual-grid{grid-template-columns:1fr}}
.dual-grid.single-col{grid-template-columns:1fr}

.panel{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  overflow:hidden;
  box-shadow:var(--shadow);
}
.panel-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:16px 20px;
  border-bottom:1px solid var(--border);
  gap:12px;
  flex-wrap:wrap;
}
.panel-title{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:15px;
  font-weight:600;
  color:var(--text);
}
.count-badge{
  background:var(--surface2);
  color:var(--text);
  font-size:12px;
  font-weight:600;
  padding:2px 9px;
  border-radius:10px;
  font-family:'Inter',sans-serif;
}
.panel-meta{
  font-size:12px;
  color:var(--text3);
  font-weight:500;
}
.panel-meta .arrow{margin-left:4px}

/* Search input ในแถบ panel */
.search-wrap{position:relative;display:flex;align-items:center}
.search-wrap input{
  font-family:inherit;
  font-size:13px;
  padding:6px 10px 6px 28px;
  border:1px solid var(--border);
  border-radius:var(--radius-xs);
  background:var(--surface2);
  color:var(--text);
  outline:none;
  width:170px;
  transition:all .15s;
}
.search-wrap input:focus{border-color:var(--accent);background:var(--surface)}
.search-wrap .si{
  position:absolute;
  left:9px;
  font-size:12px;
  color:var(--text3);
  pointer-events:none;
}

/* ═══════ FUEL TABLE ═══════ */
/* Fuel table scroll wrapper — max ~10 rows then internal scroll */
.fuel-table-scroll{
  max-height:560px;       /* ~10 rows × 56px */
  overflow-y:auto;
  overflow-x:auto;
}
.fuel-table-scroll thead th{
  position:sticky;
  top:0;
  background:var(--surface);
  z-index:2;
}
.fuel-table-scroll::-webkit-scrollbar{width:8px;height:8px}
.fuel-table-scroll::-webkit-scrollbar-track{background:transparent}
.fuel-table-scroll::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:4px}
.fuel-table-scroll::-webkit-scrollbar-thumb:hover{background:var(--text3)}

.fuel-table{width:100%;border-collapse:collapse;font-size:13px}
.fuel-table thead th{
  background:transparent;
  padding:10px 16px;
  text-align:left;
  font-weight:500;
  font-size:11px;
  color:var(--text3);
  border-bottom:1px solid var(--border);
  text-transform:uppercase;
  letter-spacing:.05em;
}
.fuel-table thead th.num{text-align:right}
.fuel-table tbody td{
  padding:14px 16px;
  border-bottom:1px solid var(--border);
  color:var(--text);
  vertical-align:middle;
}
.fuel-table tbody tr:last-child td{border-bottom:none}
.fuel-table tbody tr:hover{background:var(--surface2)}
.fuel-table tbody td.num{text-align:right;font-family:'Inter','IBM Plex Sans Thai',sans-serif;font-variant-numeric:tabular-nums}

.row-idx{
  color:var(--text3);
  font-size:12px;
  font-weight:500;
  font-family:'Inter',sans-serif;
}

/* Avatar (วงกลมตัวอักษร) */
.driver-cell{display:flex;align-items:center;gap:10px}
.avatar{
  width:32px;
  height:32px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:12px;
  font-weight:700;
  color:#fff;
  flex-shrink:0;
  letter-spacing:-.01em;
}
.driver-info{display:flex;flex-direction:column;line-height:1.3}
.driver-name{font-weight:600;font-size:13.5px;color:var(--text)}
.driver-plate{font-size:11.5px;color:var(--text3);font-family:'Inter',monospace}

/* Time pill */
.time-pill{
  display:inline-flex;
  align-items:center;
  border:1px solid var(--border);
  background:var(--surface);
  border-radius:20px;
  padding:4px 12px;
  font-size:12px;
  font-weight:500;
  color:var(--text2);
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
  font-variant-numeric:tabular-nums;
  white-space:nowrap;
}

/* km value highlight */
.km-good{color:var(--green);font-weight:700}
.km-bad{color:var(--red);font-weight:700}
.km-mid{color:var(--text);font-weight:600}

/* ═══════ DRIVER LIST PANEL (ขวา) ═══════ */
.driver-list{
  padding:8px 0;
  max-height:560px;
  overflow-y:auto;
}
.driver-list::-webkit-scrollbar{width:8px}
.driver-list::-webkit-scrollbar-track{background:transparent}
.driver-list::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:4px}
.driver-list::-webkit-scrollbar-thumb:hover{background:var(--text3)}
.driver-row{
  display:flex;
  align-items:center;
  gap:12px;
  padding:11px 20px;
  border-bottom:1px solid var(--border);
  transition:background .15s;
}
.driver-row:last-child{border-bottom:none}
.driver-row:hover{background:var(--surface2)}
.driver-rank{
  display:flex;
  align-items:center;
  justify-content:center;
  width:28px;
  flex-shrink:0;
}
.rank-num{
  font-size:14px;
  font-weight:700;
  color:var(--text3);
  font-family:'Inter',sans-serif;
}
.rank-circle{
  width:22px;
  height:22px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  font-weight:700;
  color:#fff;
  font-family:'Inter',sans-serif;
}
.driver-row .body{flex:1;min-width:0}
.driver-row .body .name-row{
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom:3px;
}
.driver-row .body .name{font-weight:600;font-size:13.5px;color:var(--text)}
.driver-row .body .stats{
  font-size:11.5px;
  color:var(--text3);
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
  font-variant-numeric:tabular-nums;
}
.driver-row .body .stats span{margin-right:6px}
.driver-row .body .stats span+span{padding-left:6px;border-left:1px solid var(--border)}
.driver-row .right{text-align:right;flex-shrink:0}
.driver-row .right .price{
  font-size:14px;
  font-weight:700;
  color:var(--text);
  font-family:'Inter',sans-serif;
  font-variant-numeric:tabular-nums;
}
.driver-row .right .kml{
  font-size:11.5px;
  color:var(--text3);
  margin-top:2px;
  font-family:'Inter',sans-serif;
}
.driver-row .right .kml.warn{color:var(--red);font-weight:600}

/* ═══════ CHARTS GRID ═══════ */
.charts-grid{
  display:grid;
  grid-template-columns:minmax(0, 1fr) minmax(0, 1.3fr);
  gap:18px;
  margin-bottom:24px;
}
@media(max-width:1100px){.charts-grid{grid-template-columns:minmax(0,1fr)}}

.chart-panel{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:18px 20px;
  box-shadow:var(--shadow);
  min-width:0;
  overflow:hidden;
}
.chart-head{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:14px;
  gap:10px;
  flex-wrap:wrap;
}
.chart-title{
  font-size:14.5px;
  font-weight:600;
  color:var(--text);
}
.chart-sub{
  font-size:12px;
  color:var(--text3);
  font-weight:400;
  border-left:1px solid var(--border);
  padding-left:12px;
  margin-left:12px;
}
.chart-canvas{position:relative;width:100%;max-width:100%;height:230px}
/* Horizontal scroll container — limits visible bars to ~4, scrolls if more */
.chart-scroll{
  overflow-x:auto;
  overflow-y:hidden;
  width:100%;
  max-width:100%;
  display:block;
}
.chart-scroll .chart-inner{
  position:relative;
  height:100%;
  display:block;
}
.chart-scroll canvas{display:block}
.chart-scroll::-webkit-scrollbar{height:8px}
.chart-scroll::-webkit-scrollbar-track{background:transparent}
.chart-scroll::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:4px}
.chart-scroll::-webkit-scrollbar-thumb:hover{background:var(--text3)}
.chart-legend{
  display:flex;
  flex-wrap:wrap;
  gap:14px;
  margin-top:12px;
  padding-top:12px;
  border-top:1px solid var(--border);
  font-size:12px;
  color:var(--text2);
}
.chart-legend-item{display:flex;align-items:center;gap:6px;font-weight:500}
.chart-legend-dot{width:10px;height:10px;border-radius:2px;flex-shrink:0}

/* ═══════ PAGINATION ═══════ */
.pagination{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:12px 20px;
  border-top:1px solid var(--border);
  background:var(--surface);
  gap:12px;
  flex-wrap:wrap;
}
.pagination-info{font-size:12px;color:var(--text2)}
.pagination-info strong{color:var(--text);font-weight:600;font-family:'Inter',sans-serif}
.pagination-controls{display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.page-btn{
  min-width:30px;
  height:30px;
  padding:0 10px;
  border:1px solid var(--border);
  background:var(--surface);
  color:var(--text2);
  border-radius:6px;
  font-family:inherit;
  font-size:12.5px;
  font-weight:500;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition:all .15s;
}
.page-btn:hover:not(:disabled){background:var(--surface2);border-color:var(--border-strong);color:var(--text)}
.page-btn.active{background:var(--accent);border-color:var(--accent);color:#fff}
.page-btn:disabled{opacity:.4;cursor:not-allowed}
.page-ellipsis{color:var(--text3);font-size:12px;padding:0 4px;user-select:none}

/* ═══════ DATE RANGE PICKER POPUP ═══════ */
.drp-wrap{position:relative}
.drp-popup{
  position:fixed;
  background:#fff;
  border:1px solid var(--border);
  border-radius:12px;
  box-shadow:0 8px 28px rgba(15,23,42,.12);
  padding:16px;
  z-index:600;
  display:none;
  min-width:320px;
}
.drp-popup.open{display:block;animation:drpIn .15s ease}
@keyframes drpIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.drp-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:0 4px}
.drp-nav-btn{width:28px;height:28px;border-radius:50%;border:none;background:transparent;color:var(--text2);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center}
.drp-nav-btn:hover{background:var(--surface2);color:var(--accent)}
.drp-title{font-size:14px;font-weight:600;color:var(--text)}
.drp-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px}
.drp-weekday{text-align:center;font-size:11px;color:var(--text3);padding:6px 0;font-weight:500}
.drp-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;user-select:none;-webkit-user-select:none;touch-action:none}
.drp-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text);cursor:pointer;border:none;background:transparent;font-family:inherit;font-weight:500;border-radius:6px;transition:all .12s}
.drp-day:hover:not(.disabled):not(.in-range):not(.selected){background:var(--surface2)}
.drp-day.muted{color:var(--text3);opacity:.4}
.drp-day.today{color:var(--accent);font-weight:700}
.drp-day.selected{background:var(--accent);color:#fff;font-weight:700;border-radius:8px;z-index:2}
.drp-day.in-range{background:rgba(37,99,235,.12);border-radius:0;color:var(--accent)}
.drp-day.range-start{background:var(--accent);color:#fff;border-radius:8px 0 0 8px}
.drp-day.range-end{background:var(--accent);color:#fff;border-radius:0 8px 8px 0}
.drp-day.range-start.range-end{border-radius:8px}
.drp-footer{display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:1px solid var(--border);gap:8px}
.drp-presets{display:flex;gap:4px;flex-wrap:wrap}
.drp-preset-btn{padding:4px 10px;font-size:11px;border:1px solid var(--border);background:var(--surface2);color:var(--text2);border-radius:14px;cursor:pointer;font-family:inherit;font-weight:500}
.drp-preset-btn:hover{border-color:var(--accent);color:var(--accent);background:#fff}
.drp-apply-btn{padding:6px 16px;background:var(--accent);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.drp-apply-btn:hover{background:var(--accent-hover)}
.drp-apply-btn:disabled{opacity:.5;cursor:not-allowed}
.drp-hint{font-size:11px;color:var(--text3);margin-top:8px;text-align:center}

/* Driver picker dropdown ในแถบ topbar */
.driver-select{
  font-family:inherit;
  font-size:13px;
  padding:7px 12px;
  border:1px solid var(--border);
  border-radius:var(--radius-xs);
  background:var(--surface);
  color:var(--text);
  outline:none;
  height:34px;
  cursor:pointer;
  transition:border-color .15s;
}
.driver-select:hover{border-color:var(--border-strong)}
.driver-select:focus{border-color:var(--accent)}

/* ═══════ MODAL (เก็บ logic เดิม แต่ refresh สี) ═══════ */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(4px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.modal-title{font-size:16px;font-weight:600;color:var(--text)}
.modal-close{width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:18px 22px;overflow-y:auto;flex:1}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}
.modal.modal-fullscreen{max-width:1200px!important;width:96vw!important;max-height:96vh!important;height:96vh}
.modal.modal-fullscreen .modal-body{padding:24px 32px}

.step-indicator{display:flex;align-items:center;justify-content:center;margin-bottom:20px;width:100%}
.step-item{display:flex;align-items:center;gap:8px;flex-shrink:0}
.step-circle{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;transition:all .3s}
.step-circle.active{background:var(--accent);color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.18)}
.step-circle.done{background:var(--green);color:#fff}
.step-circle.inactive{background:var(--border);color:var(--text3)}
.step-label{font-size:12px;font-weight:600}
.step-label.active{color:var(--accent)}
.step-label.done{color:var(--green)}
.step-label.inactive{color:var(--text3)}
.step-line{flex:0 0 120px;height:2px;margin:0 10px;border-radius:2px}
.step-line.done{background:var(--green)}
.step-line.inactive{background:var(--border)}

.driver-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px;color:#fff}
.driver-avatar-big{width:44px;height:44px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.driver-banner-name{font-size:17px;font-weight:700;line-height:1}
.driver-banner-plate{font-size:12px;opacity:.7;margin-top:4px;font-family:monospace}

.driver-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.driver-card-grid .full{grid-column:1/-1}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-xs);font-family:inherit;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.form-control.is-invalid{border-color:var(--red)}
.form-control.auto-calc{background:var(--surface2);color:var(--text2);cursor:default}
textarea.form-control{resize:vertical;min-height:65px}
.invalid-feedback{font-size:11px;color:var(--red);margin-top:4px}
.auto-hint{font-size:11px;color:var(--accent);margin-top:4px;font-weight:500}
.section-divider{font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:4px 0 8px;border-bottom:1px solid var(--border);margin:14px 0 10px;grid-column:1/-1}

.oil-price-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:12px;color:#fff}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 1.5s infinite}
.live-dot.loading{background:var(--amber)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.calc-box{background:rgba(22,163,74,.06);border:1px solid rgba(22,163,74,.25);border-radius:var(--radius-xs);padding:12px 14px;display:none;margin-top:10px}
.calc-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}
.calc-item .lbl{font-size:11px;color:var(--text3)}
.calc-item .val{font-size:19px;font-weight:700;color:var(--green)}
.calc-item .val.amber{color:var(--amber)}
.calc-item .unit{font-size:11px;color:var(--text3)}

.alert{padding:11px 14px;border-radius:var(--radius-xs);margin-bottom:14px;font-size:14px}
.alert-success{background:var(--green-light);border:1px solid var(--green);color:#14532d}
.alert-error{background:var(--red-light);border:1px solid var(--red);color:#7f1d1d}

.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.empty-state .icon{font-size:44px;margin-bottom:10px;opacity:.4}

.summary-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}
.summary-chip{background:var(--surface2);border:1px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;color:var(--text2);display:flex;align-items:center;gap:5px;font-weight:500}
.summary-chip strong{color:var(--text)}

/* Time clock picker */
.clock-picker-row{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap}
.clock-block{flex:1;min-width:120px}
.clock-label{font-size:11px;color:var(--text2);margin-bottom:5px;font-weight:600}
.clock-display{padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-xs);background:var(--surface);font-family:'Inter',monospace;font-size:15px;font-weight:600;color:var(--text);cursor:pointer;text-align:center;transition:all .15s}
.clock-display:hover{border-color:var(--accent);box-shadow:0 0 0 2px rgba(37,99,235,.1)}
.clock-picker-row.is-invalid .clock-display{border-color:var(--red);background:rgba(220,38,38,.04)}
.time-arrow{font-size:18px;color:var(--text3);padding-bottom:10px}

.clock-modal{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:10000;display:flex;align-items:center;justify-content:center}
.clock-box{background:#fff;border-radius:16px;padding:20px;width:300px;box-shadow:0 20px 50px rgba(0,0,0,.3)}
.clock-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.clock-header span{font-weight:700;font-size:15px}
.clock-close{background:none;border:none;font-size:24px;cursor:pointer;color:#666;line-height:1}
.clock-tabs{display:flex;gap:6px;margin-bottom:10px}
.clock-tab{flex:1;padding:6px;border:1px solid #ddd;background:#f5f5f5;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600}
.clock-tab.active{background:var(--accent);color:#fff;border-color:transparent}
.clock-current{text-align:center;font-size:28px;font-weight:700;margin:8px 0;color:var(--accent);font-family:'Inter',monospace}
#clock-face{display:block;margin:0 auto;cursor:pointer}
.clock-ok{width:100%;margin-top:12px;padding:10px;border:none;background:var(--accent);color:#fff;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px}
.clock-ok:hover{background:var(--accent-hover)}

/* Job table inside modal */
.job-table-wrap{border:1px solid var(--border);border-radius:var(--radius-xs);overflow:hidden}
.job-table-wrap table{width:100%;border-collapse:collapse;font-size:12px}
.job-table-wrap thead th{background:var(--surface2);padding:8px 12px;text-align:left;font-weight:600;font-size:11px;color:var(--text2);border-bottom:1px solid var(--border)}
.job-table-wrap tbody td{padding:9px 12px;border-bottom:1px solid var(--border)}
.job-loading{text-align:center;padding:18px;color:var(--text3);font-size:13px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-xs)}
.job-bill{font-family:monospace;font-size:11px;background:var(--surface2);border:1px solid var(--border);border-radius:3px;padding:1px 5px;color:var(--text2)}
.job-summary-bar{display:flex;gap:8px;padding:10px 14px;background:var(--surface2);border-top:1px solid var(--border);flex-wrap:wrap}
.job-chip{font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;border:1px solid var(--border);color:var(--text2);background:var(--surface)}
.job-chip.ok{background:var(--green-light);color:#14532d;border-color:var(--green)}
.job-chip.fail{background:var(--red-light);color:#7f1d1d;border-color:var(--red)}
.job-date-chip{font-size:11px;color:var(--text3);background:var(--surface2);border:1px solid var(--border);border-radius:20px;padding:2px 9px}

/* Toast */
.toast{position:fixed;top:76px;right:20px;z-index:9999;min-width:240px;max-width:340px;background:var(--green);color:#fff;padding:14px 18px 18px 16px;border-radius:12px;box-shadow:0 6px 28px rgba(0,0,0,.22);display:flex;align-items:flex-start;gap:10px;font-family:inherit;font-size:14px;font-weight:500;animation:toastIn .35s cubic-bezier(.34,1.56,.64,1) both;overflow:hidden}
.toast.hiding{animation:toastOut .35s ease forwards}
.toast-icon{font-size:20px;flex-shrink:0;margin-top:1px}
.toast-body{flex:1}
.toast-title{font-weight:700;font-size:14px;margin-bottom:2px}
.toast-msg{font-size:13px;opacity:.88}
.toast-progress{position:absolute;bottom:0;left:0;height:4px;background:rgba(255,255,255,.45);width:100%}
@keyframes toastIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}
@keyframes toastOut{from{transform:translateX(0);opacity:1}to{transform:translateX(110%);opacity:0}}

/* Date input lock */
.date-input-locked{background:var(--surface2)!important;cursor:not-allowed!important;opacity:.6;pointer-events:none}
.date-loading-hint{display:none;margin-top:6px;font-size:12px;color:var(--amber);font-weight:600}
.date-loading-hint.show{display:flex;align-items:center;gap:6px}
.date-loading-hint .spinner{display:inline-block;width:12px;height:12px;border:2px solid var(--amber);border-top-color:transparent;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

@media(max-width:760px){
  .topbar{padding:12px 16px;gap:10px}
  .topbar-sub{display:none}
  .main{padding:18px 16px}
  .form-grid,.driver-card-grid{grid-template-columns:1fr}
}

/* Hide report page elements ที่ไม่ได้ใช้ในหน้านี้ — เก็บไว้เผื่อมี route */
/* #pageReport visibility controlled via JS — start hidden via inline style */

/* Keep all the report page styles for compatibility */
.report-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:16px 20px;background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius);color:#fff;flex-wrap:wrap;gap:10px;box-shadow:0 4px 16px rgba(15,23,42,.18)}
.report-section-title{font-size:17px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px}
.report-section-sub{font-size:12px;color:rgba(255,255,255,.65);margin-top:2px}
.report-pie-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px}
@media(max-width:900px){.report-pie-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.report-pie-grid{grid-template-columns:1fr}}
.pie-card{background:var(--surface);border-radius:var(--radius);padding:18px;border:1px solid var(--border);box-shadow:var(--shadow)}
.pie-card-title{font-size:16px;font-weight:700;color:var(--text);margin-bottom:3px}
.pie-card-sub{font-size:13px;color:var(--text2);margin-bottom:12px}
.pie-canvas-wrap{position:relative;width:100%;height:200px}
.pie-legend{margin-top:10px;display:flex;flex-direction:column;gap:5px}
.pie-legend-item{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text2)}
.pie-legend-dot{width:11px;height:11px;border-radius:50%;flex-shrink:0}
.pie-legend-label{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pie-legend-val{font-weight:600;color:var(--text);white-space:nowrap;font-size:13px}
.report-driver-table{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:18px}
.report-stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:18px}
.report-stat-card{background:var(--surface);border-radius:var(--radius);padding:14px 16px;border:1px solid var(--border);box-shadow:var(--shadow);text-align:center}
.report-stat-label{font-size:12px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.report-stat-value{font-size:22px;font-weight:700;color:var(--navy)}
.report-stat-sub{font-size:12px;color:var(--text3);margin-top:2px}
.kml-bar-wrap{display:flex;align-items:center;gap:8px}
.kml-bar-bg{flex:1;height:6px;background:var(--surface2);border-radius:3px;overflow:hidden}
.kml-bar-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--green),#10b981)}
.rank-badge{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:700;background:var(--surface2);color:var(--text2)}
.rank-badge.gold{background:#fef3c7;color:#b45309}
.rank-badge.silver{background:#f1f5f9;color:#475569}
.rank-badge.bronze{background:#fef0e7;color:#9a3412}
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:12px 16px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:18px}
.dlv-filter-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-xs);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s;text-decoration:none}
</style>
</head>
<body>

{{-- Mobile sidebar toggle --}}
<button type="button" class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
<div class="sidebar-overlay" id="sbOverlay" onclick="toggleSidebar()"></div>

{{-- ═══════════════════════════════════════════════════════════════
     APP LAYOUT: Sidebar (ซ้าย) + Content (ขวา)
═══════════════════════════════════════════════════════════════ --}}
<div class="app">

<aside class="sidebar" id="appSidebar">
  {{-- Brand --}}
  <div class="sidebar-brand">
    <div class="title">
      <div class="logo">⛽</div>
      <div>ระบบติดตามน้ำมันรถ</div>
    </div>
    <div class="sub">บันทึกและวิเคราะห์การใช้น้ำมัน</div>
  </div>

  {{-- Add button (top of sidebar) --}}
  <button class="sidebar-add-btn sidebar-add-btn-top" onclick="openModal()">
    <span style="font-size:15px">＋</span> เพิ่มข้อมูล
  </button>

  {{-- Main menu --}}
  <div class="sidebar-section">
    <div class="label">เมนูหลัก</div>
    <button type="button" class="nav-item active" id="navHome" onclick="showTrackingPage();closeMobileSidebar();">
      <span class="ic">📈</span>
      <span>หน้าหลัก / ติดตาม</span>
      <span class="badge-dot"></span>
    </button>
    <a class="nav-item" id="navReport" href="#" onclick="event.preventDefault();showReportPage();closeMobileSidebar();">
      <span class="ic">📊</span>
      <span>สรุปรายงาน</span>
    </a>
    <a class="nav-item" href="{{ url('/service') }}">
      <span class="ic">🚚</span>
      <span>service</span>
    </a>
        <a class="nav-item" href="{{ url('/SOlist') }}" style="display: flex; align-items: center; padding: 10px 15px; text-decoration: none; color: inherit;">
      <span class="ic" style="margin-right: 12px; display: flex; align-items: center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
          <polyline points="10 17 15 12 10 7"></polyline>
          <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>
      </span>
      <span style="font-weight: 500;">(SO List)</span>
    </a>
  </div>

  {{-- View filter --}}
  <div class="sidebar-section">
    <div class="label">มุมมอง</div>
    <div class="sidebar-view-tabs">
      @foreach(['day'=>'📅 รายวัน','month'=>'🗓 รายเดือน','year'=>'📆 รายปี','all'=>'∞ ทั้งหมด'] as $v=>$label)
      <button type="button" class="view-tab {{ $view===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
      @endforeach
    </div>
  </div>

  {{-- Date filter --}}
  @if($view==='day')
  @php
    $dateFrom = request('date_from', request('date', $filterDay));
    $dateTo   = request('date_to',   request('date', $filterDay));
  @endphp
  <div class="sidebar-section" style="padding-top:6px">
    <div class="label">ช่วงวันที่</div>
    <div class="sidebar-control">
      <div class="drp-wrap" data-from="{{ $dateFrom }}" data-to="{{ $dateTo }}">
        <button type="button" class="date-trigger" id="drpTrigger" onclick="drpToggle(event)">
          <span class="ic">📅</span>
          <span id="drpLabel" style="flex:1">—</span>
          <span class="arrow">▼</span>
        </button>
        <div class="drp-popup" id="drpPopup">
          <div class="drp-header">
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(-1)">‹</button>
            <div class="drp-title" id="drpMonthTitle">—</div>
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(1)">›</button>
          </div>
          <div class="drp-weekdays">
            <div class="drp-weekday">อา.</div><div class="drp-weekday">จ.</div><div class="drp-weekday">อ.</div>
            <div class="drp-weekday">พ.</div><div class="drp-weekday">พฤ.</div><div class="drp-weekday">ศ.</div>
            <div class="drp-weekday">ส.</div>
          </div>
          <div class="drp-days" id="drpDays"></div>
          <div class="drp-hint" id="drpHint">เลือกวันเริ่มต้น</div>
          <div class="drp-footer">
            <div class="drp-presets">
              <button type="button" class="drp-preset-btn" onclick="drpPreset('today')">วันนี้</button>
              <button type="button" class="drp-preset-btn" onclick="drpPreset('7days')">7 วัน</button>
              <button type="button" class="drp-preset-btn" onclick="drpPreset('thismonth')">เดือนนี้</button>
            </div>
            <button type="button" class="drp-apply-btn" id="drpApplyBtn" onclick="drpApply()">ตกลง</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  @elseif($view==='year')
  <div class="sidebar-section" style="padding-top:6px">
    <div class="label">ปี</div>
    <div class="sidebar-control">
      <select class="driver-select" id="yearPicker" onchange="onYearChange()">
        @php
          $savedYear = request('year', date('Y'));
          // Allow showing future years up to +2
          $yearMax = max((int)date('Y') + 2, (int)$savedYear);
        @endphp
        @for($y=$yearMax;$y>=2020;$y--)
        <option value="{{ $y }}" {{ $savedYear==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
  </div>
  @elseif($view!=='all')
  <div class="sidebar-section" style="padding-top:6px">
    <div class="label">เดือน</div>
    <div class="sidebar-control">
      <input type="month" class="driver-select" id="monthPicker" value="{{ $filterMonth }}" onchange="submitFilter()">
    </div>
  </div>
  @endif

  {{-- Driver filter --}}
  <div class="sidebar-section" style="padding-top:6px">
    <div class="label">คนขับ</div>
    <div class="sidebar-control">
      <select class="driver-select" id="driverPicker" onchange="submitFilter()">
        <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @foreach($drivers as $d)
        <option value="{{ $d }}" {{ $filterDriver===$d?'selected':'' }}>{{ $d }}</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- Footer --}}
  <div class="sidebar-footer">
    <div class="live-time">อัปเดต <span id="navDate">—</span> น.</div>
    <div style="margin-top:4px">© Oil Tracker</div>
  </div>
</aside>

<div class="content-wrap">

@if(session('success'))
<div class="toast" id="successToast">
  <div class="toast-icon">✅</div>
  <div class="toast-body">
    <div class="toast-title">บันทึกสำเร็จ</div>
    <div class="toast-msg">{{ session('success') }}</div>
  </div>
  <div class="toast-progress" id="toastProgress"></div>
</div>
<script>
(function(){
  var DURATION = 5000;
  var toast = document.getElementById('successToast');
  var bar   = document.getElementById('toastProgress');
  if (!toast) return;
  bar.style.transition = 'width ' + DURATION + 'ms linear';
  requestAnimationFrame(function(){requestAnimationFrame(function(){bar.style.width = '0%';});});
  setTimeout(function(){toast.classList.add('hiding');setTimeout(function(){ toast.remove(); }, 380);}, DURATION);
  toast.addEventListener('click', function(){toast.classList.add('hiding');setTimeout(function(){ toast.remove(); }, 380);});
})();
</script>
@endif

<main class="main">
<div id="pageTracking">

  {{-- ═══════ METRIC CARDS ═══════ --}}
  @php
    // คำนวณ trend สำหรับแต่ละการ์ด
    $totalLogs       = $logs->count();
    $totalLiters     = $metrics['total_liters']     ?? 0;
    $totalPrice      = $metrics['total_price']      ?? 0;
    $avgKml          = $metrics['avg_km_per_liter'] ?? 0;
    $totalWorkHours  = $metrics['total_work_hours'] ?? 0;

    // นับคนขับที่มี km/L < 9 (ผิดปกติ)
    $abnormalCount = 0;
    foreach($logs as $r){
      if(($r['km_per_liter'] ?? 0) > 0 && ($r['km_per_liter'] ?? 0) < 9){
        $abnormalCount++;
      }
    }
  @endphp

  <div class="metrics">
    {{-- น้ำมันรวม --}}
    <div class="metric-card green">
      <div class="metric-label">น้ำมันรวม</div>
      <div class="metric-row">
        <div class="metric-value">{{ number_format($totalLiters, 2) }}</div>
        <div class="metric-unit">ลิตร</div>
      </div>
    </div>

    {{-- ค่าน้ำมันรวม --}}
    <div class="metric-card blue">
      <div class="metric-label">ค่าน้ำมันรวม</div>
      <div class="metric-row">
        <div class="metric-value">฿{{ number_format($totalPrice) }}</div>
        <div class="metric-unit">บาท</div>
      </div>
    </div>

    {{-- เฉลี่ย km/L --}}
    <div class="metric-card amber">
      <div class="metric-label">เฉลี่ย km/L</div>
      <div class="metric-row">
        <div class="metric-value">{{ number_format($avgKml, 2) }}</div>
        <div class="metric-unit">กม./ลิตร</div>
      </div>
    </div>

    {{-- ชม.ทำงาน --}}
    <div class="metric-card red">
      <div class="metric-label">ชม.ทำงาน</div>
      <div class="metric-row">
        <div class="metric-value">{{ number_format($totalWorkHours, 0) }}</div>
        <div class="metric-unit">ชั่วโมง</div>
      </div>
    </div>
  </div>

  {{-- ═══════ DUAL GRID: ตารางเติมน้ำมัน (ซ้าย) + คนขับวันนี้ (ขวา) ═══════ --}}
  <div class="dual-grid {{ $view === 'day' ? 'single-col' : '' }}">

    {{-- ── ตารางเติมน้ำมัน ── --}}
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          รายการเติมน้ำมัน
          <span class="count-badge" id="oilCount">{{ $logs->count() }}</span>
          <span class="panel-meta">เรียง: เวลาทำงาน <span class="arrow">↓</span></span>
        </div>
        <div class="search-wrap">
          <span class="si">🔍</span>
          <input type="text" placeholder="เพื่อค้นหา..." oninput="filterOilTable(this.value)">
        </div>
      </div>

      <div class="fuel-table-scroll">
      <table class="fuel-table">
        <thead>
          <tr>
            <th style="width:42px">#</th>
            <th>คนขับ / ทะเบียน</th>
            <th style="width:140px;white-space:nowrap">เวลาทำงาน</th>
            <th class="num" style="width:80px">ระยะ</th>
            <th class="num" style="width:70px">ลิตร</th>
            <th class="num" style="width:80px">฿</th>
            <th class="num" style="width:70px">KM/L</th>
          </tr>
        </thead>
        <tbody id="oilTbody">
          @php $rowNo = 0; @endphp
          @forelse($logs as $i => $r)
          @php
            $rowNo++;
            $kml=$r['km_per_liter']??0;
            $dist=($r['total_distance']??0)>0?number_format($r['total_distance']).' km':'—';
            $wh=$r['work_hours']??0;
            $createdTH=$r['created_at']?\Carbon\Carbon::parse($r['created_at'])->timezone('Asia/Bangkok')->format('d/m/Y H:i'):'—';
            $name = $r['driver_name'] ?? '—';
            $plate = $r['vehicle_id'] ?? '—';
            // Avatar: ใช้ตัวอักษร 2 ตัวแรก
            $initials = mb_substr($name, 0, 2, 'UTF-8');
            // สีของ avatar — hash ตามชื่อ
            $palette = ['#16a34a','#2563eb','#eab308','#9333ea','#ea580c','#0891b2','#db2777','#dc2626'];
            $avColor = $palette[abs(crc32($name)) % count($palette)];
            // KML class
            $kmlClass = 'km-mid';
            if($kml >= 13) $kmlClass = 'km-good';
            elseif($kml > 0 && $kml < 9) $kmlClass = 'km-bad';
            // Time pill
            $tStart = $r['start_time'] ?? '';
            $tEnd   = $r['end_time']   ?? '';
            // ตัด ":XX" เหลือ HH:MM
            if(strlen($tStart) >= 5) $tStart = substr($tStart, 0, 5);
            if(strlen($tEnd)   >= 5) $tEnd   = substr($tEnd,   0, 5);
            $timeText = ($tStart && $tEnd) ? $tStart.'-'.$tEnd : '—';
          @endphp
          <tr data-driver="{{ strtolower($name) }}">
            <td class="row-idx">{{ str_pad((string)$rowNo, 2, '0', STR_PAD_LEFT) }}</td>
            <td>
              <div class="driver-cell">
                <div class="driver-info">
                  <div class="driver-name">{{ $name }}</div>
                  <div class="driver-plate">{{ $plate }}</div>
                </div>
              </div>
            </td>
            <td><span class="time-pill">{{ $timeText }}</span></td>
            <td class="num">{{ $dist }}</td>
            <td class="num">{{ $r['liters']?number_format($r['liters'],1):'—' }}</td>
            <td class="num">{{ $r['total_price']?'฿'.number_format($r['total_price']):'—' }}</td>
            <td class="num">
              @if($kml>0)<span class="{{ $kmlClass }}">{{ number_format($kml,2) }}</span>
              @else<span style="color:var(--text3)">—</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7"><div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการ</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
      </div>

      <div class="pagination" id="oilPagination" style="display:none">
        <div class="pagination-info">
          แสดง <strong id="pgFrom">0</strong>–<strong id="pgTo">0</strong> จาก <strong id="pgTotal">0</strong> รายการ
        </div>
        <div class="pagination-controls" id="pgControls"></div>
      </div>
    </div>

    {{-- ── คนขับวันนี้ (ซ่อนเมื่อเป็น view รายวัน เพราะข้อมูลรายวันอยู่ในตารางหลักแล้ว) ── --}}
    @if($view !== 'day')
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          คนขับวันนี้
          @php
            // นับคนขับ unique ใน logs ปัจจุบัน
            $uniqueDrivers = [];
            foreach($logs as $r){
              $n = $r['driver_name'] ?? '';
              if(!isset($uniqueDrivers[$n])){
                $uniqueDrivers[$n] = [
                  'name' => $n,
                  'rounds' => 0,
                  'distance' => 0,
                  'liters' => 0,
                  'price' => 0,
                  'kml_sum' => 0,
                  'kml_count' => 0,
                ];
              }
              $uniqueDrivers[$n]['rounds']++;
              $uniqueDrivers[$n]['distance'] += $r['total_distance'] ?? 0;
              $uniqueDrivers[$n]['liters']   += $r['liters'] ?? 0;
              $uniqueDrivers[$n]['price']    += $r['total_price'] ?? 0;
              if(($r['km_per_liter'] ?? 0) > 0){
                $uniqueDrivers[$n]['kml_sum'] += $r['km_per_liter'];
                $uniqueDrivers[$n]['kml_count']++;
              }
            }
            // sort by total price DESC
            uasort($uniqueDrivers, fn($a,$b)=> $b['price'] <=> $a['price']);
          @endphp
          <span class="count-badge">{{ count($uniqueDrivers) }}</span>
        </div>
        <span class="panel-meta">เรียง: ค่าน้ำมัน <span class="arrow">↓</span></span>
      </div>

      <div class="driver-list">
        @php $rankNo = 0; @endphp
        @forelse($uniqueDrivers as $idx => $d)
        @php
          $rankNo++;
          $rankPalette = ['#eab308','#16a34a','#eab308','#dc2626','#dc2626','#7c3aed','#0891b2'];
          $rankColor = $rankPalette[($rankNo - 1) % count($rankPalette)];
          $initials = mb_substr($d['name'], 0, 2, 'UTF-8');
          $avgKmlD = $d['kml_count'] > 0 ? $d['kml_sum'] / $d['kml_count'] : 0;
          $kmlBad = $avgKmlD > 0 && $avgKmlD < 9;
        @endphp
        <div class="driver-row">
          <div class="driver-rank">
            <div class="rank-num">{{ str_pad((string)$rankNo, 2, '0', STR_PAD_LEFT) }}</div>
          </div>
          <div class="body">
            <div class="name-row">
              <div class="name">{{ $d['name'] }}</div>
            </div>
            <div class="stats">
              <span>{{ $d['rounds'] }} รอบ</span>
              <span>{{ number_format($d['distance']) }} km</span>
              <span>{{ number_format($d['liters'],1) }} ลิตร</span>
            </div>
          </div>
          <div class="right">
            <div class="price">฿{{ number_format($d['price']) }}</div>
            @if($avgKmlD > 0)
            <div class="kml {{ $kmlBad ? 'warn' : '' }}">{{ number_format($avgKmlD,2) }} km/L</div>
            @endif
          </div>
        </div>
        @empty
        <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
        @endforelse
      </div>
    </div>
    @endif

  </div>

  {{-- ═══════ CHARTS GRID ═══════ --}}
  <div class="charts-grid">

    {{-- รายการสมบูรณ์ / รายการผิดพลาด (Stacked Bar) --}}
    <div class="chart-panel">
      <div class="chart-head">
        <div style="display:flex;align-items:center">
          <div class="chart-title">รายการสมบูรณ์ / รายการผิดพลาด</div>
          <div class="chart-sub">ประสิทธิภาพการส่งสินค้าแยกตามคนขับ</div>
        </div>
      </div>
      <div class="chart-canvas chart-scroll" style="height:280px"><div class="chart-inner" id="deliveryChartInner"><canvas id="deliveryChart"></canvas></div></div>
      <div class="chart-legend" id="dlvLegend"></div>
    </div>

    {{-- น้ำมันต่อกิโล (km/L) — Bar Chart per driver --}}
    <div class="chart-panel">
      <div class="chart-head">
        <div style="display:flex;align-items:center">
          <div class="chart-title">น้ำมันต่อกิโล (km/L)</div>
          <div class="chart-sub">เฉลี่ย km/L แต่ละคน · เกณฑ์ 9.0</div>
        </div>
      </div>
      <div class="chart-canvas chart-scroll" style="height:280px"><div class="chart-inner" id="chartKmlInner"><canvas id="chartKml"></canvas></div></div>
      <div class="chart-legend" id="kmlLegend"></div>
    </div>

  </div>

</div>

{{-- ═══════ HIDDEN: Report page (เก็บไว้เผื่อ route เดิม) ═══════ --}}
<div id="pageReport" style="display:none;padding:24px 28px;max-width:1600px;margin:0 auto">
  <div class="report-section-header">
    <div><div class="report-section-title">📊 สรุปรายงาน</div><div class="report-section-sub">วิเคราะห์การใช้น้ำมันแยกตามคนขับ</div></div>
    <button type="button" onclick="showTrackingPage()" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.25);padding:8px 14px;border-radius:8px;font-family:inherit;font-size:13px;font-weight:500;cursor:pointer">← กลับหน้าหลัก</button>
  </div>
  <div class="report-stat-row" id="repStatRow"></div>
  <div class="report-pie-grid">
    <div class="pie-card"><div class="pie-card-title">สัดส่วนค่าน้ำมัน (฿)</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieCost"></canvas></div><div class="pie-legend" id="pieCostLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนลิตรที่เติม</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieLiters"></canvas></div><div class="pie-legend" id="pieLitersLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนชั่วโมงทำงาน</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieHours"></canvas></div><div class="pie-legend" id="pieHoursLegend"></div></div>
  </div>
  <canvas id="chartDriver" style="display:none"></canvas>
</div>

</main>
</div>{{-- /.content-wrap --}}
</div>{{-- /.app --}}

{{-- ═══════════════════════════════════════════════════════════════
     MODAL — ข้อมูลคนขับ (เหมือนเดิม)
═══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="step1Modal">
  <div class="modal modal-fullscreen">
    <div class="modal-header"><div class="modal-title">🚗 ข้อมูลคนขับรถ</div><button type="button" class="modal-close" onclick="closeAllModals()">✕</button></div>
    <div class="modal-body">
      <div class="step-indicator">
        <div class="step-item"><div class="step-circle active">1</div><div class="step-label active">ข้อมูลคนขับ</div></div>
        <div class="step-line inactive"></div>
        <div class="step-item"><div class="step-circle inactive">2</div><div class="step-label inactive">ข้อมูลน้ำมัน</div></div>
      </div>
      <div class="driver-banner" id="driverBanner" style="display:none">
        <div class="driver-avatar-big">👤</div>
        <div><div class="driver-banner-name" id="bannerName">—</div><div class="driver-banner-plate" id="bannerPlate">ทะเบียน: —</div></div>
      </div>
      <div class="driver-card-grid">
        @php
          $driverList=['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
          foreach($drivers as $dbD){if(!in_array($dbD,$driverList))$driverList[]=$dbD;}
          $plateList=['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','805','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
          foreach($plates as $dbP){if(!in_array($dbP,$plateList))$plateList[]=$dbP;}
        @endphp
        <div class="full">
          <label class="form-label">วันที่ทำงาน *</label>
          <input type="date" id="s1-work-date" class="form-control" value="{{ date('Y-m-d') }}" onchange="onS1DateChange()">
          <div class="invalid-feedback" id="s1-err-date" style="display:none">กรุณาเลือกวันที่</div>
          <div class="date-loading-hint" id="s1-date-loading-hint">
            <span class="spinner"></span><span>กำลังโหลดข้อมูลคนขับ</span>
          </div>
        </div>
        <div>
          <label class="form-label">คนขับ *</label>
          <select id="s1-driver-select" class="form-control" onchange="onS1SelectOther(this,'s1-driver-name','s1-driver-other');updateDriverBanner();loadJobsForDriver()">
            <option value="">— เลือกคนขับ —</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="hidden" id="s1-driver-name" value="">
          <input type="text" id="s1-driver-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุชื่อคนขับ" oninput="document.getElementById('s1-driver-name').value=this.value;updateDriverBanner();loadJobsForDriver()">
          <div class="invalid-feedback" id="s1-err-driver" style="display:none">กรุณาเลือกคนขับ</div>
        </div>
        <div>
          <label class="form-label">ทะเบียนรถ *</label>
          <select id="s1-plate-select" class="form-control" onchange="onS1SelectOther(this,'s1-vehicle-id','s1-plate-other');updateDriverBanner()">
            <option value="">— เลือกทะเบียน —</option>
            @foreach($plateList as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
            <option value="__other__">อื่นๆ</option>
          </select>
          <input type="hidden" id="s1-vehicle-id" value="">
          <input type="text" id="s1-plate-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุทะเบียน" oninput="document.getElementById('s1-vehicle-id').value=this.value;updateDriverBanner()">
          <div class="invalid-feedback" id="s1-err-plate" style="display:none">กรุณาเลือกทะเบียนรถ</div>
        </div>

        <div class="full" style="margin-top:4px">
          <label class="form-label">เวลาทำงาน (เวลาไทย) *</label>
          <div class="clock-picker-row" id="s1-time-block">
            <div class="clock-block">
              <div class="clock-label">เวลาเริ่ม</div>
              <div class="clock-display" onclick="openClock('start')"><span id="s1-start-display">00:00</span> น.</div>
            </div>
            <div class="time-arrow">→</div>
            <div class="clock-block">
              <div class="clock-label">เวลาสิ้นสุด</div>
              <div class="clock-display" onclick="openClock('end')"><span id="s1-end-display">00:00</span> น.</div>
            </div>
          </div>
          <div class="invalid-feedback" id="s1-err-time" style="display:none">กรุณาเลือกเวลาเริ่มและเวลาสิ้นสุด</div>
          <div id="s1-wh-preview" style="margin-top:8px;font-size:12px;color:var(--amber);font-weight:600;display:none">
            ⏱ <span id="s1-wh-val">0</span>
          </div>
          <input type="hidden" id="s1-start-h" value="0">
          <input type="hidden" id="s1-start-m" value="0">
          <input type="hidden" id="s1-end-h" value="0">
          <input type="hidden" id="s1-end-m" value="0">
        </div>
      </div>

      <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <div style="font-size:13px;font-weight:600;color:var(--text2)">📋 รายการงานของคนขับ</div>
          <span id="jobDateChip" class="job-date-chip" style="display:none"></span>
        </div>
        <div id="jobTableWrap"><div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div></div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeAllModals()">ยกเลิก</button>
      <button type="button" class="btn-primary" onclick="goToStep2()">ถัดไป — เพิ่มข้อมูลน้ำมัน →</button>
    </div>
  </div>
</div>

{{-- Clock modal --}}
<div id="clock-modal" class="clock-modal" style="display:none" onclick="if(event.target===this)closeClock()">
  <div class="clock-box">
    <div class="clock-header">
      <span id="clock-title">เลือกเวลา</span>
      <button type="button" class="clock-close" onclick="closeClock()">×</button>
    </div>
    <div class="clock-tabs">
      <button type="button" id="tab-hour" class="clock-tab active" onclick="switchTab('hour')">ชั่วโมง</button>
      <button type="button" id="tab-min" class="clock-tab" onclick="switchTab('min')">นาที</button>
    </div>
    <div class="clock-current"><span id="clock-h-val">00</span>:<span id="clock-m-val">00</span></div>
    <svg id="clock-face" viewBox="0 0 240 240" width="240" height="240"></svg>
    <button type="button" class="clock-ok" onclick="confirmClock()">ตกลง</button>
  </div>
</div>

{{-- Fuel modal --}}
<div class="modal-overlay" id="fuelModal">
  <div class="modal">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <button type="button" id="backBtn" onclick="backToStep1()" style="width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center">←</button>
        <div class="modal-title" id="modalTitle">เพิ่มข้อมูลเติมน้ำมัน</div>
      </div>
      <button type="button" class="modal-close" onclick="closeAllModals()">✕</button>
    </div>
    <form id="fuelForm" method="POST" action="{{ route('oil.store') }}" style="display:contents">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="">
      <div class="modal-body">
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:12px"><ul style="list-style:none;padding:0;margin:0">@foreach($errors->all() as $err)<li>• {{ $err }}</li>@endforeach</ul></div>
        @endif
        <div class="step-indicator" style="margin-bottom:14px">
          <div class="step-item"><div class="step-circle done">✓</div><div class="step-label done">ข้อมูลคนขับ</div></div>
          <div class="step-line done"></div>
          <div class="step-item"><div class="step-circle active">2</div><div class="step-label active">ข้อมูลน้ำมัน</div></div>
        </div>
        <div class="summary-row" id="summaryRow" style="display:none">
          <div class="summary-chip">👤 <strong id="chipDriver">—</strong></div>
          <div class="summary-chip">🚗 <strong id="chipPlate">—</strong></div>
          <div class="summary-chip">📅 <strong id="chipDate">—</strong></div>
          <div class="summary-chip" id="chipTimeWrap" style="display:none">⏱ <strong id="chipTime">—</strong></div>
        </div>
        <input type="hidden" name="work_date" id="f-work-date">
        <input type="hidden" name="driver_name" id="f-driver-name">
        <input type="hidden" name="vehicle_id" id="f-vehicle-id">
        <input type="hidden" name="start_time" id="f-start-time">
        <input type="hidden" name="end_time" id="f-end-time">
        <input type="hidden" name="ok" id="f-ok" value="0">
        <input type="hidden" name="ng" id="f-ng" value="0">
        <div class="oil-price-banner">
          <div style="width:100%">
            <div style="font-size:12px;opacity:.7" id="oilPriceLabel">ราคาน้ำมันดีเซล</div>
            <div style="display:flex;align-items:baseline;gap:4px;margin:4px 0">
              <span style="font-size:24px;font-weight:700" id="oilPriceShow">—</span>
              <span style="font-size:12px;opacity:.7;margin-left:4px">บาท/ลิตร</span>
              <div style="margin-left:auto;display:flex;align-items:center;gap:6px">
                <div class="live-dot loading" id="liveDot"></div>
                <span style="font-size:12px;opacity:.7" id="liveLabel">กำลังดึง</span>
              </div>
            </div>
            <div style="font-size:12px;opacity:.6;font-style:italic;margin-bottom:8px" id="oilPriceStatus">กำลังโหลด...</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              @php $oilBtns=['diesel'=>'⛽ ดีเซล','95'=>'⛽ 95','benzin95'=>'⛽ เบนซิน 95','91'=>'⛽ 91','e20'=>'⛽ E20','e85'=>'⛽ E85']; @endphp
              @foreach($oilBtns as $oilKey=>$oilLabel)
              <button type="button" onclick="switchOilType('{{ $oilKey }}')" id="btnOil-{{ $oilKey }}" class="oil-btn" style="font-family:inherit;font-size:11px;font-weight:600;padding:5px 11px;border-radius:14px;cursor:pointer;border:2px solid {{ $oilKey==='diesel'?'#fff':'transparent' }};background:{{ $oilKey==='diesel'?'rgba(255,255,255,.3)':'rgba(255,255,255,.1)' }};color:{{ $oilKey==='diesel'?'#fff':'rgba(255,255,255,.7)' }}">{{ $oilLabel }}</button>
              @endforeach
              <button type="button" onclick="refreshOilPrice()" id="btnRefreshOil" style="font-family:inherit;font-size:11px;padding:5px 11px;border-radius:14px;border:2px solid rgba(255,255,255,.3);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);cursor:pointer">🔄</button>
            </div>
          </div>
        </div>
        <div class="form-grid">
          <div class="section-divider">⛽ ข้อมูลน้ำมัน</div>
          <div class="full"><label class="form-label">ค่าน้ำมัน (฿) *</label><input type="number" name="total_price" id="f-total-price" class="form-control {{ $errors->has('total_price')?'is-invalid':'' }}" step="0.01" value="{{ old('total_price',$editLog['total_price']??'') }}" placeholder="กรอกยอดเงิน เช่น 500" oninput="calcPreview()">@error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <div class="full"><label class="form-label">ระยะทางทั้งหมด (km)</label><input type="number" name="total_distance" id="f-total-distance" class="form-control" value="{{ old('total_distance',$editLog['total_distance']??'') }}" oninput="calcPreview()"></div>
        </div>
        <div><label class="form-label">จำนวนลิตร</label><input type="number" name="liters" id="f-liters" class="form-control auto-calc" step="0.01" value="{{ old('liters',$editLog['liters']??'') }}" readonly></div>
        <div><label class="form-label">ราคาต่อลิตร (฿)</label><input type="number" name="price_per_liter" id="f-price-per-liter" class="form-control auto-calc" step="0.01" value="{{ old('price_per_liter',$editLog['price_per_liter']??'') }}" readonly></div>
        <div class="calc-box" id="calcBox">
          <div style="font-size:11px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">📊 Preview</div>
          <div class="calc-grid">
            <div class="calc-item"><div class="lbl">ชั่วโมงทำงาน</div><div class="val amber" id="calcWorkHours">—</div><div class="unit">ชม.</div></div>
            <div class="calc-item"><div class="lbl">ลิตร / ยอดเงิน</div><div class="val" id="calcLitersPreview">—</div><div class="unit">ล. / ฿</div></div>
            <div class="calc-item"><div class="lbl">km/L</div><div class="val" id="calcKml">—</div><div class="unit">กม./ลิตร</div></div>
          </div>
        </div>
        <div class="full"><label class="form-label">หมายเหตุ</label><textarea name="note" id="f-note" class="form-control">{{ old('note',$editLog['note']??'') }}</textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="backToStep1()" id="backBtnFooter">← ย้อนกลับ</button>
        <button type="submit" class="btn-primary">💾 บันทึกข้อมูล</button>
      </div>
    </form>
  </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════
   CONSTANTS
═══════════════════════════════════════════════════════════════ */
const COLORS=['#16a34a','#2563eb','#eab308','#dc2626','#9333ea','#0891b2','#ea580c','#db2777','#0d9488','#7c3aed','#84cc16','#f59e0b'];
const ROUTE_STORE    = '{{ route("oil") }}';
const ROUTE_FILTER   = '{{ route("oil.filter") }}';
const ROUTE_UPDATE   = id => `{{ url("/oil/update") }}/${id}`;
const ROUTE_PREVMILE = '{{ route("oil.prevMileage") }}';
const ROUTE_SYNC_NG  = '{{ route("oil.syncNg") }}';
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const TZ = 'Asia/Bangkok';
let currentOilType = 'diesel', isEditMode = false, editId = null;

const MAIN_VIEW   = @json($view);
const MAIN_DRIVER = @json($filterDriver);
const MAIN_YEAR   = parseInt(@json(request('year', date('Y'))));

/* ═══════════════════════════════════════════════════════════════
   FILTER SUBMIT
═══════════════════════════════════════════════════════════════ */
function submitFilterForm(params){
  const form = document.createElement('form');
  form.method = 'POST'; form.action = ROUTE_FILTER; form.style.display = 'none';
  const add = (name, value) => { if (value === undefined || value === null || value === '') return; const i = document.createElement('input'); i.type = 'hidden'; i.name = name; i.value = value; form.appendChild(i); };
  add('_token', CSRF_TOKEN);
  Object.keys(params).forEach(k => add(k, params[k]));
  document.body.appendChild(form); form.submit();
}
function switchView(v){
  const params = { view: v };
  const driverSel = document.getElementById('driverPicker');
  if (driverSel && driverSel.value) params.driver_name = driverSel.value;
  if (v === 'month') { const el = document.getElementById('monthPicker'); if (el && el.value) params.month = el.value; }
  else if (v === 'year') { const el = document.getElementById('yearPicker'); if (el && el.value) params.year = el.value; }
  submitFilterForm(params);
}
function submitFilter(){
  const params = { view: '{{ $view }}' };
  const driverSel = document.getElementById('driverPicker');
  if (driverSel && driverSel.value) params.driver_name = driverSel.value;
  const monthEl = document.getElementById('monthPicker'); if (monthEl && monthEl.value) params.month = monthEl.value;
  const yearEl = document.getElementById('yearPicker');  if (yearEl && yearEl.value)  params.year  = yearEl.value;
  submitFilterForm(params);
}
/* Persist user-picked year so UI doesn't snap back if backend default overrides */
function onYearChange(){
  const yearEl = document.getElementById('yearPicker');
  if (yearEl && yearEl.value) {
    try { sessionStorage.setItem('oilPickedYear', yearEl.value); } catch(e){}
  }
  submitFilter();
}
/* On page load: if backend returned a year that doesn't match what user just picked,
   keep the user's choice visible in the dropdown */
(function restoreYear(){
  try {
    const saved = sessionStorage.getItem('oilPickedYear');
    if (!saved) return;
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('yearPicker');
      if (!el) return;
      // Only restore if it's a valid option AND backend default is showing instead
      const exists = Array.from(el.options).some(o => o.value === saved);
      if (exists && el.value !== saved) {
        el.value = saved;
      }
    });
  } catch(e){}
})();

/* ═══════════════════════════════════════════════════════════════
   NAV DATE / TABLE FILTER / SIDEBAR
═══════════════════════════════════════════════════════════════ */
function nowThai(){return new Date(new Date().toLocaleString('en-US',{timeZone:TZ}));}
function todayStr(){const d=nowThai();return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
function updateNavDate(){
  const now=new Date();
  const time = now.toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
  const el = document.getElementById('navDate'); if(el) el.textContent = time;
}
function filterOilTable(q){
  oilSearchQuery = q.toLowerCase();
  oilCurrentPage = 1;
  renderOilPage();
}

/* Mobile sidebar */
function toggleSidebar(){
  const sb = document.getElementById('appSidebar');
  const ov = document.getElementById('sbOverlay');
  if(!sb) return;
  const isOpen = sb.classList.toggle('open');
  ov.classList.toggle('show', isOpen);
}
function closeMobileSidebar(){
  if(window.innerWidth > 900) return;
  const sb = document.getElementById('appSidebar');
  const ov = document.getElementById('sbOverlay');
  sb?.classList.remove('open');
  ov?.classList.remove('show');
}

/* Show/hide report page (สรุปรายงาน) */
function showReportPage(){
  const tracking = document.getElementById('pageTracking');
  const report = document.getElementById('pageReport');
  if(!tracking || !report) return;
  tracking.style.display = 'none';
  report.style.display = '';
  document.querySelectorAll('.sidebar .nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('navReport')?.classList.add('active');
  if(typeof renderReportPage === 'function') renderReportPage();
  window.scrollTo({top:0, behavior:'smooth'});
}
function showTrackingPage(){
  const tracking = document.getElementById('pageTracking');
  const report = document.getElementById('pageReport');
  if(!tracking || !report) return;
  tracking.style.display = '';
  report.style.display = 'none';
  document.querySelectorAll('.sidebar .nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('navHome')?.classList.add('active');
  window.scrollTo({top:0, behavior:'smooth'});
}


/* ═══════════════════════════════════════════════════════════════
   DELIVERY CHART (Stacked Bar) — รวม success/fail ต่อคนขับ
═══════════════════════════════════════════════════════════════ */
let dlvChart = null;
@php
  $deliveryByDriver = [];
  foreach ($logs as $log) {
      $driver  = $log['driver_name'] ?? 'ไม่ระบุ';
      if (!isset($deliveryByDriver[$driver])) {
          $deliveryByDriver[$driver] = ['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];
      }
      $deliveryByDriver[$driver]['success'] += (int)($log['delivery_success'] ?? $log['success_count'] ?? $log['ok_count']  ?? 0);
      $deliveryByDriver[$driver]['fail']    += (int)($log['delivery_fail']    ?? $log['fail_count']    ?? $log['ng_count']   ?? 0);
  }
@endphp
const DLV_BY_DRIVER = @json($deliveryByDriver);

function renderDlv(){
  const drivers = Object.keys(DLV_BY_DRIVER);
  if(drivers.length === 0){
    if(dlvChart) dlvChart.destroy();
    document.getElementById('dlvLegend').innerHTML = '<span style="color:var(--text3)">ไม่มีข้อมูล</span>';
    return;
  }

  // Sort by total (success+fail) desc — show all (scroll horizontal if > 4)
  const sorted = drivers
    .map(d => ({name:d, plate:DLV_BY_DRIVER[d].plate, s:DLV_BY_DRIVER[d].success, f:DLV_BY_DRIVER[d].fail}))
    .sort((a,b) => (b.s+b.f) - (a.s+a.f));

  // Lock chart width to panel: ≤4 drivers fit panel exactly; >4 scroll
  const inner = document.getElementById('deliveryChartInner');
  const scrollWrap = inner ? inner.parentElement : null;
  if(inner && scrollWrap){
    const BAR_PX = 140;
    inner.style.width = '100%';
    const panel = scrollWrap.closest('.chart-panel');
    const panelW = panel ? (panel.clientWidth - 40) : scrollWrap.clientWidth;
    const containerW = Math.max(scrollWrap.clientWidth, panelW);
    const needed = sorted.length * BAR_PX;
    const w = (sorted.length > 4 && needed > containerW) ? needed : containerW;
    inner.style.width = w + 'px';
    inner.style.height = '100%';
  }

  const labels = sorted.map(d => [d.name, d.plate || '']);
  const success = sorted.map(d => d.s);
  const fail = sorted.map(d => d.f);

  if(dlvChart) dlvChart.destroy();
  dlvChart = new Chart(document.getElementById('deliveryChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label:'ส่งสำเร็จ',    data:success, backgroundColor:'#16a34a', borderRadius:{topLeft:0,topRight:0,bottomLeft:6,bottomRight:6}, borderSkipped:false, stack:'s', maxBarThickness:50 },
        { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:'#dc2626', borderRadius:{topLeft:6,topRight:6,bottomLeft:0,bottomRight:0}, borderSkipped:false, stack:'s', maxBarThickness:50 },
      ],
    },
    plugins: [ChartDataLabels],
    options: {
      responsive: true, maintainAspectRatio: false,
      layout: { padding: { top: 20, left: 10, right: 10 } },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: items => Array.isArray(items[0].label) ? items[0].label[0] : items[0].label,
            label: ctx => `${ctx.dataset.label}: ${ctx.raw} รายการ`,
            footer: items => 'รวม: ' + items.reduce((s, i) => s + i.raw, 0) + ' รายการ',
          }
        },
        datalabels: {
          color: '#fff',
          font: { weight: '700', size: 13, family: 'Inter' },
          formatter: v => v > 0 ? v : '',
          display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
          anchor: 'center', align: 'center',
        },
      },
      scales: {
        x: {
          stacked: true,
          ticks: {
            font: { size: 12, weight: '600', family: 'IBM Plex Sans Thai' },
            color: '#0f172a',
            autoSkip: false,
            callback: function(value, idx) {
              const l = this.getLabelForValue(value);
              return Array.isArray(l) ? l : [l];
            }
          },
          grid: { display:false }
        },
        y: {
          stacked: true, beginAtZero: true,
          ticks: { font: { size: 11, family: 'Inter' }, color: '#94a3b8', stepSize:2, callback: v => v + ' รายการ' },
          grid: { color: 'rgba(15,23,42,.05)' },
        },
      },
    },
  });

  buildDlvLegend();
}
function buildDlvLegend(){
  const wrap = document.getElementById('dlvLegend'); if(!wrap) return;
  wrap.innerHTML = `
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#16a34a"></span>ส่งสำเร็จ</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#dc2626"></span>ส่งไม่สำเร็จ</div>
  `;
}

/* ═══════════════════════════════════════════════════════════════
   KML BAR CHART — น้ำมันต่อกิโล (avg km/L แต่ละคนขับ + เกณฑ์ 9.0)
═══════════════════════════════════════════════════════════════ */
@php
  // คำนวณ avg km/L ต่อคนขับ
  $kmlByDriver = [];
  foreach ($logs as $log) {
      $driver = $log['driver_name'] ?? 'ไม่ระบุ';
      $kml    = (float)($log['km_per_liter'] ?? 0);
      if ($kml <= 0) continue;
      if (!isset($kmlByDriver[$driver])) $kmlByDriver[$driver] = ['sum'=>0,'count'=>0,'plate'=>$log['vehicle_id']??''];
      $kmlByDriver[$driver]['sum']   += $kml;
      $kmlByDriver[$driver]['count']++;
  }
@endphp
const KML_BY_DRIVER = @json($kmlByDriver);

let kmlChart = null;
function renderKmlChart(){
  // รวมข้อมูลและจัดเรียงจากมากไปน้อย
  const drivers = Object.keys(KML_BY_DRIVER).map(name => ({
    name,
    plate: KML_BY_DRIVER[name].plate || '',
    avg: KML_BY_DRIVER[name].count > 0 ? KML_BY_DRIVER[name].sum / KML_BY_DRIVER[name].count : 0,
  })).filter(d => d.avg > 0).sort((a,b) => b.avg - a.avg);

  if(drivers.length === 0){
    if(kmlChart) kmlChart.destroy();
    document.getElementById('kmlLegend').innerHTML = '<span style="color:var(--text3)">ไม่มีข้อมูล</span>';
    return;
  }

  // Lock chart width to panel: ≤4 drivers fit panel exactly; >4 scroll
  const innerKml = document.getElementById('chartKmlInner');
  const scrollWrapKml = innerKml ? innerKml.parentElement : null;
  if(innerKml && scrollWrapKml){
    const BAR_PX = 120;
    // Reset first so we can read true container width
    innerKml.style.width = '100%';
    const panel = scrollWrapKml.closest('.chart-panel');
    const panelW = panel ? (panel.clientWidth - 40) : scrollWrapKml.clientWidth; // 40 = padding
    const containerW = Math.max(scrollWrapKml.clientWidth, panelW);
    const needed = drivers.length * BAR_PX;
    const w = (drivers.length > 4 && needed > containerW) ? needed : containerW;
    innerKml.style.width = w + 'px';
    innerKml.style.height = '100%';
  }

  const labels = drivers.map(d => d.name);
  const data   = drivers.map(d => +d.avg.toFixed(2));

  // แต่ละแท่งสีตาม performance: < 9 แดง / 9-12 อำพัน / > 12 เขียว / default น้ำเงิน
  const barColors = data.map(v => v < 9 ? '#dc2626' : (v < 12 ? '#f59e0b' : '#16a34a'));

  // ค่าเฉลี่ยรวมทั้งหมด
  const overallAvg = data.reduce((a,b)=>a+b,0) / data.length;

  // หา max เพื่อกำหนด y-axis
  const maxVal = Math.max(...data, 9);
  const yMax   = Math.ceil((maxVal + 1) / 2) * 2;

  if(kmlChart) kmlChart.destroy();
  kmlChart = new Chart(document.getElementById('chartKml'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'เฉลี่ย km/L',
        data,
        backgroundColor: barColors,
        borderRadius: 6,
        borderSkipped: false,
        maxBarThickness: 50,
      }],
    },
    plugins: [
      ChartDataLabels,
      {
        // ลากเส้น threshold 9.0 บนกราฟ
        id: 'kmlThreshold',
        afterDatasetsDraw(chart){
          const {ctx, chartArea: {left, right}, scales: {y}} = chart;
          const yPos = y.getPixelForValue(9);
          ctx.save();
          ctx.strokeStyle = '#dc2626';
          ctx.setLineDash([6, 4]);
          ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.moveTo(left, yPos);
          ctx.lineTo(right, yPos);
          ctx.stroke();
          // Label
          ctx.setLineDash([]);
          ctx.fillStyle = '#dc2626';
          ctx.font = '600 11px Inter';
          ctx.textAlign = 'right';
          ctx.fillText('เกณฑ์ 9.0', right - 6, yPos - 6);
          ctx.restore();
        },
      },
    ],
    options: {
      responsive: true, maintainAspectRatio: false,
      layout: { padding: { top: 24, right: 16, left: 6, bottom: 6 } },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => `เฉลี่ย: ${ctx.raw.toFixed(2)} km/L`,
            afterLabel: ctx => {
              const d = drivers[ctx.dataIndex];
              return d.plate ? `ทะเบียน: ${d.plate}` : '';
            },
          },
        },
        datalabels: {
          color: '#0f172a',
          font: { weight: '700', size: 11, family: 'Inter' },
          anchor: 'end', align: 'top', offset: 2,
          formatter: v => v.toFixed(2),
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            font: { size: 11, family: 'IBM Plex Sans Thai' },
            color: '#475569',
            autoSkip: false,
            maxRotation: 0,
            callback: function(value){
              const lbl = this.getLabelForValue(value);
              // ตัดชื่อยาวให้สั้น
              return lbl.length > 8 ? lbl.substring(0,7) + '…' : lbl;
            },
          },
        },
        y: {
          beginAtZero: true,
          suggestedMax: yMax,
          ticks: {
            stepSize: 2,
            font: { size: 11, family: 'Inter' },
            color: '#94a3b8',
            callback: v => v + ' km/L',
          },
          grid: { color: 'rgba(15,23,42,.05)' },
        },
      },
    },
  });

  // Legend
  const wrap = document.getElementById('kmlLegend');
  if(wrap){
    wrap.innerHTML = `
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#16a34a"></span>ดี (≥ 12)</div>
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (9–12)</div>
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#dc2626"></span>ผิดปกติ (&lt; 9)</div>
      <div class="chart-legend-item" style="margin-left:auto;color:var(--text3)">เฉลี่ยรวม: <strong style="color:var(--text);margin-left:4px">${overallAvg.toFixed(2)} km/L</strong></div>
    `;
  }
}

/* ═══════════════════════════════════════════════════════════════
   DATE RANGE PICKER (เก็บ logic เดิม)
═══════════════════════════════════════════════════════════════ */
const TH_MONTHS_SHORT = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
let drpViewYear=null, drpViewMonth=null, drpFrom=null, drpTo=null;
function drpPad(n){return String(n).padStart(2,'0');}
function drpFmt(d){return `${d.getFullYear()}-${drpPad(d.getMonth()+1)}-${drpPad(d.getDate())}`;}
function drpParse(s){if(!s)return null;const p=s.split('-');return new Date(parseInt(p[0]),parseInt(p[1])-1,parseInt(p[2]));}
function drpFormatLabel(fromStr,toStr){
  if(!fromStr) return 'เลือกช่วงวันที่';
  const f=drpParse(fromStr);
  if(!toStr||fromStr===toStr) return `${f.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} ${f.getFullYear()+543}`;
  const t=drpParse(toStr);
  if(f.getFullYear()===t.getFullYear()&&f.getMonth()===t.getMonth()) return `${f.getDate()}–${t.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} ${f.getFullYear()+543}`;
  return `${f.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} – ${t.getDate()} ${TH_MONTHS_SHORT[t.getMonth()]} ${f.getFullYear()+543}`;
}
function drpUpdateLabel(){const lbl=document.getElementById('drpLabel');if(lbl)lbl.textContent=drpFormatLabel(drpFrom,drpTo);}
function drpPositionPopup(){
  const pop = document.getElementById('drpPopup');
  const trg = document.getElementById('drpTrigger');
  if(!pop || !trg) return;
  const r = trg.getBoundingClientRect();
  const popW = 340, popH = 420;
  const isMobile = window.innerWidth <= 900;
  if(isMobile){
    // Below the trigger
    pop.style.top  = (r.bottom + 6) + 'px';
    pop.style.left = Math.max(8, Math.min(r.left, window.innerWidth - popW - 8)) + 'px';
  }else{
    // To the right of the sidebar trigger; clamp inside viewport
    let top  = r.top;
    if(top + popH > window.innerHeight - 8) top = Math.max(8, window.innerHeight - popH - 8);
    let left = r.right + 8;
    if(left + popW > window.innerWidth - 8) left = Math.max(8, window.innerWidth - popW - 8);
    pop.style.top  = top  + 'px';
    pop.style.left = left + 'px';
  }
}
function drpToggle(e){
  if(e)e.stopPropagation();
  const pop=document.getElementById('drpPopup');const trg=document.getElementById('drpTrigger');
  if(pop.classList.contains('open')){
    pop.classList.remove('open');trg.classList.remove('active');
  }else{
    const a=drpFrom?drpParse(drpFrom):new Date();
    drpViewYear=a.getFullYear();drpViewMonth=a.getMonth();drpRender();
    drpPositionPopup();
    pop.classList.add('open');trg.classList.add('active');
  }
}
window.addEventListener('resize',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();});
window.addEventListener('scroll',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();},true);
function drpNavMonth(d){drpViewMonth+=d;if(drpViewMonth<0){drpViewMonth=11;drpViewYear--;}if(drpViewMonth>11){drpViewMonth=0;drpViewYear++;}drpRender();}
function drpRender(){
  document.getElementById('drpMonthTitle').textContent=`${TH_MONTHS_SHORT[drpViewMonth]} ${drpViewYear+543}`;
  const grid=document.getElementById('drpDays');
  const fd=new Date(drpViewYear,drpViewMonth,1);const fw=fd.getDay();
  const dim=new Date(drpViewYear,drpViewMonth+1,0).getDate();
  const pmd=new Date(drpViewYear,drpViewMonth,0).getDate();
  const ts=drpFmt(new Date());
  let h='';
  for(let i=fw-1;i>=0;i--){const d=pmd-i;const y=drpViewMonth===0?drpViewYear-1:drpViewYear;const m=drpViewMonth===0?11:drpViewMonth-1;const ds=`${y}-${drpPad(m+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,true,ts);}
  for(let d=1;d<=dim;d++){const ds=`${drpViewYear}-${drpPad(drpViewMonth+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,false,ts);}
  const total=fw+dim;const rem=(7-(total%7))%7;
  for(let d=1;d<=rem;d++){const y=drpViewMonth===11?drpViewYear+1:drpViewYear;const m=drpViewMonth===11?0:drpViewMonth+1;const ds=`${y}-${drpPad(m+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,true,ts);}
  grid.innerHTML=h;
  const hint=document.getElementById('drpHint');
  if(!drpFrom)hint.textContent='🖱️ กดค้างแล้วลากเพื่อเลือกช่วงวันที่';
  else if(drpFrom===drpTo)hint.textContent=`เลือก ${drpFormatLabel(drpFrom,drpTo)}`;
  else hint.textContent=`เลือกช่วง ${drpFormatLabel(drpFrom,drpTo)}`;
  document.getElementById('drpApplyBtn').disabled=!drpFrom;
}
function drpDayBtn(ds,d,muted,ts){
  const c=['drp-day'];if(muted)c.push('muted');if(ds===ts)c.push('today');
  if(drpFrom&&drpTo){
    if(ds===drpFrom&&ds===drpTo)c.push('selected');
    else if(ds===drpFrom)c.push('range-start');
    else if(ds===drpTo)c.push('range-end');
    else if(ds>drpFrom&&ds<drpTo)c.push('in-range');
  }else if(drpFrom&&ds===drpFrom)c.push('selected');
  return `<button type="button" class="${c.join(' ')}" data-date="${ds}">${d}</button>`;
}
let drpDragging=false,drpDragStart=null;
function drpGetDateFromEvent(e){const p=e.touches?e.touches[0]:e;if(!p)return null;const el=document.elementFromPoint(p.clientX,p.clientY);if(!el)return null;const b=el.closest('.drp-day');return b?b.dataset.date:null;}
function drpStartDrag(e){const ds=drpGetDateFromEvent(e);if(!ds)return;e.preventDefault();drpDragging=true;drpDragStart=ds;drpFrom=ds;drpTo=ds;drpRender();}
function drpMoveDrag(e){if(!drpDragging)return;const ds=drpGetDateFromEvent(e);if(!ds)return;e.preventDefault();if(ds<drpDragStart){drpFrom=ds;drpTo=drpDragStart;}else{drpFrom=drpDragStart;drpTo=ds;}drpRender();}
function drpEndDrag(){drpDragging=false;}
function drpPreset(p){const n=new Date();let f,t;if(p==='today'){f=t=drpFmt(n);}else if(p==='7days'){t=drpFmt(n);const d=new Date(n);d.setDate(d.getDate()-6);f=drpFmt(d);}else if(p==='thismonth'){f=drpFmt(new Date(n.getFullYear(),n.getMonth(),1));t=drpFmt(n);}drpFrom=f;drpTo=t;const fd=drpParse(f);drpViewYear=fd.getFullYear();drpViewMonth=fd.getMonth();drpRender();}
function drpApply(){if(!drpFrom)return;const to=drpTo||drpFrom;const params={view:'day',date_from:drpFrom,date_to:to};const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;submitFilterForm(params);}
document.addEventListener('click',(e)=>{const pop=document.getElementById('drpPopup');const trg=document.getElementById('drpTrigger');if(!pop||!pop.classList.contains('open'))return;if(pop.contains(e.target)||trg.contains(e.target))return;pop.classList.remove('open');trg.classList.remove('active');});
function drpInit(){
  const wrap=document.querySelector('.drp-wrap[data-from]');if(!wrap)return;
  const f=wrap.dataset.from;const t=wrap.dataset.to;
  if(f)drpFrom=f;if(t)drpTo=t;drpUpdateLabel();
  const grid=document.getElementById('drpDays');
  if(grid){grid.addEventListener('mousedown',drpStartDrag);document.addEventListener('mousemove',drpMoveDrag);document.addEventListener('mouseup',drpEndDrag);grid.addEventListener('touchstart',drpStartDrag,{passive:false});document.addEventListener('touchmove',drpMoveDrag,{passive:false});document.addEventListener('touchend',drpEndDrag);}
}

/* ═══════════════════════════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════════════════════════ */
const OIL_PAGE_SIZE = 10;
let oilCurrentPage = 1;
let oilSearchQuery = '';
function getVisibleRows(){const rows=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));if(!oilSearchQuery)return rows;return rows.filter(r=>r.dataset.driver.includes(oilSearchQuery));}
function renderOilPage(){
  // Show all rows that match the search filter; scroll handled by .fuel-table-scroll container
  const allRows=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));
  const visibleSet=new Set(getVisibleRows());
  allRows.forEach(r=>{ r.style.display = visibleSet.has(r) ? '' : 'none'; });
  const total=visibleSet.size;
  const c=document.getElementById('oilCount');if(c)c.textContent=total;
  // Hide pagination — using internal scroll instead
  const pag=document.getElementById('oilPagination');if(pag)pag.style.display='none';
}
function renderPageButtons(tp){/* pagination disabled */}
function gotoPage(n){/* pagination disabled — kept for backward compat */}

/* ═══════════════════════════════════════════════════════════════
   MODAL — Driver / Plate / Time / Jobs (เก็บ logic เดิม)
═══════════════════════════════════════════════════════════════ */
function buildTimeDropdowns(){}
function getTimeVal(hId,mId){const hEl=document.getElementById(hId),mEl=document.getElementById(mId);if(!hEl||!mEl)return '';const h=hEl.value,m=mEl.value;if(h===''||m==='')return '';return String(h).padStart(2,'0')+':'+String(m).padStart(2,'0');}
function setTimeDropdown(hId,mId,t){const hEl=document.getElementById(hId),mEl=document.getElementById(mId);if(!hEl||!mEl)return;if(!t){hEl.value='0';mEl.value='0';return;}const p=t.split(':');hEl.value=String(parseInt(p[0])||0);mEl.value=String(parseInt(p[1])||0);}
function onS1SelectOther(sel,hid,tid){const v=sel.value,t=document.getElementById(tid),h=document.getElementById(hid);if(v==='__other__'){t.style.display='block';t.focus();h.value=t.value;}else{t.style.display='none';t.value='';h.value=v;}}
function updateDriverBanner(){
  const name=document.getElementById('s1-driver-name').value||document.getElementById('s1-driver-select').value;
  const plate=document.getElementById('s1-vehicle-id').value||document.getElementById('s1-plate-select').value;
  const banner=document.getElementById('driverBanner');
  if(name&&name!=='__other__'&&plate&&plate!=='__other__'){
    document.getElementById('bannerName').textContent=name;
    document.getElementById('bannerPlate').textContent='ทะเบียน: '+plate;
    banner.style.display='flex';
  }else banner.style.display='none';
  if(typeof calcWorkHours==='function')calcWorkHours();
}

const JOB_API_BASE = 'http://server_update:8000/api/getDeliveryPersonByDate';
const jobApiCache = {};
async function fetchJobsByDate(dateStr){
  if(jobApiCache[dateStr]!==undefined)return;
  jobApiCache[dateStr]=null;
  try{
    const res=await fetch(`${JOB_API_BASE}?date=${dateStr}`);if(!res.ok)throw new Error('HTTP '+res.status);
    const json=await res.json();
    const drivers=(json.data||[]).map(block=>({
      driver_name:block.bill_out_by||'ไม่ระบุ',
      jobs:(block.jobs||[]).map(j=>({
        bill_no:j.bill_no||'',so_id:j.so_id||'',customer_name:j.customer_name||'',
        bill_in_by:j.bill_in_by||'',status:j.delivery_status||'',note:j.reason||'',
      })),
    }));
    jobApiCache[dateStr]=[{date:json.date||dateStr,drivers:drivers}];
  }catch(e){console.warn('fetchJobsByDate:',e);jobApiCache[dateStr]=[];}
}
const DB_DRIVERS = @json($drivers);
function populateDriverDropdown(dateStr){
  const sel=document.getElementById('s1-driver-select');const hidEl=document.getElementById('s1-driver-name');
  const dayBlocks=jobApiCache[dateStr]||[];const apiDrivers=[];
  dayBlocks.forEach(day=>{(day.drivers||[]).forEach(d=>{if(d.driver_name&&!apiDrivers.includes(d.driver_name))apiDrivers.push(d.driver_name);});});
  const prevVal=hidEl.value||sel.value;
  sel.innerHTML='<option value="">— เลือกคนขับ —</option>';
  if(apiDrivers.length>0){
    apiDrivers.forEach(d=>{const o=document.createElement('option');o.value=d;o.textContent=d;sel.appendChild(o);});
    const rest=DB_DRIVERS.filter(d=>!apiDrivers.includes(d));
    if(rest.length){const grp=document.createElement('optgroup');grp.label='📋 คนขับอื่นๆ';rest.forEach(d=>{const o=document.createElement('option');o.value=d;o.textContent=d;grp.appendChild(o);});sel.appendChild(grp);}
  }else{DB_DRIVERS.forEach(d=>{const o=document.createElement('option');o.value=d;o.textContent=d;sel.appendChild(o);});}
  const oo=document.createElement('option');oo.value='__other__';oo.textContent='อื่นๆ (พิมพ์เอง)';sel.appendChild(oo);
  if(prevVal&&prevVal!=='__other__'){const found=[...sel.options].find(o=>o.value===prevVal);if(found){sel.value=prevVal;hidEl.value=prevVal;}else{sel.value='';hidEl.value='';}}
}
let isLoadingDrivers=false,lastLoadedDate=null;
function setDateInputLocked(locked){
  const dateInput=document.getElementById('s1-work-date');const hint=document.getElementById('s1-date-loading-hint');
  if(!dateInput)return;
  if(locked){dateInput.classList.add('date-input-locked');dateInput.setAttribute('readonly','readonly');if(hint)hint.classList.add('show');isLoadingDrivers=true;}
  else{dateInput.classList.remove('date-input-locked');dateInput.removeAttribute('readonly');if(hint)hint.classList.remove('show');isLoadingDrivers=false;}
}
async function onS1DateChange(){
  if(isLoadingDrivers){if(lastLoadedDate)document.getElementById('s1-work-date').value=lastLoadedDate;return;}
  updateDriverBanner();
  const date=document.getElementById('s1-work-date').value;if(!date)return;
  setDateInputLocked(true);
  const sel=document.getElementById('s1-driver-select');
  sel.innerHTML='<option value="">⏳ กำลังโหลด...</option>';sel.disabled=true;
  document.getElementById('s1-driver-name').value='';
  document.getElementById('jobTableWrap').innerHTML='<div class="job-loading">กำลังโหลดข้อมูลคนขับ...</div>';
  document.getElementById('jobDateChip').style.display='none';
  try{
    await fetchJobsByDate(date);lastLoadedDate=date;sel.disabled=false;populateDriverDropdown(date);updateDriverBanner();
    document.getElementById('jobTableWrap').innerHTML='<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';
  }catch(e){console.warn(e);sel.disabled=false;sel.innerHTML='<option value="">— เกิดข้อผิดพลาด —</option>';}
  finally{setDateInputLocked(false);}
}
async function loadJobsForDriver(){
  const name=document.getElementById('s1-driver-name').value||document.getElementById('s1-driver-select').value;
  const date=document.getElementById('s1-work-date').value;
  const wrap=document.getElementById('jobTableWrap');const chip=document.getElementById('jobDateChip');
  if(!name||name==='__other__'||!name.trim()){wrap.innerHTML='<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';chip.style.display='none';return;}
  if(!date){wrap.innerHTML='<div class="job-loading">กรุณาเลือกวันที่ก่อน</div>';chip.style.display='none';return;}
  wrap.innerHTML='<div class="job-loading">⏳ กำลังโหลดรายการงาน...</div>';chip.style.display='none';
  await fetchJobsByDate(date);
  const jobs=_collectJobs(name,date);chip.textContent='วันที่ '+date;chip.style.display='';
  if(!jobs.length){wrap.innerHTML=`<div class="job-loading">ไม่พบรายการงานของ ${name} วันที่ ${date}</div>`;return;}
  renderJobTable(jobs);
}
function renderJobTable(jobs){
  const wrap=document.getElementById('jobTableWrap');
  const rows=jobs.map(j=>{
      const raw=(j.status||'').trim();
      const noteText = (j.note || '').trim();
      
      // ถ้า note = ส่งสำเร็จ ให้ถือว่า status = สำเร็จด้วย
      const effectiveStatus = (noteText === 'ส่งสำเร็จ' || noteText === 'สำเร็จ') ? 'ส่งสำเร็จ' : raw;
      
      const isOk = effectiveStatus.includes('สำเร็จ') && !effectiveStatus.includes('ไม่');
      const isNg = effectiveStatus.includes('ไม่สำเร็จ') || effectiveStatus.toLowerCase()==='ng' || effectiveStatus.toLowerCase()==='fail';
      const sb = isOk ? `<span class="job-chip ok">✅ สำเร็จ</span>` : isNg ? `<span class="job-chip fail">❌ ไม่สำเร็จ</span>` : `<span class="job-chip">— รอ</span>`;
      
      const isSuccessNote = noteText === 'ส่งสำเร็จ' || noteText === 'สำเร็จ';
      const nh = (noteText && noteText !== raw && !isSuccessNote)
        ? `<div style="font-size:11px;color:var(--text3);margin-top:3px">${noteText}</div>`
        : '';
    // ─────────────────
    
    const so=j.so_id?`<span class="job-bill" style="background:rgba(37,99,235,.08);color:var(--accent)">${j.so_id}</span>`:`<span style="color:var(--text3);font-size:11px">—</span>`;
    return `<tr><td><span class="job-bill">${j.bill_no}</span></td><td>${so}</td><td>${j.customer_name}</td><td style="color:var(--text2)">${j.bill_in_by}</td><td>${sb}${nh}</td></tr>`;
  }).join('');
  const t=jobs.length,ok=jobs.filter(j=>{const r=(j.status||'').trim();return r.includes('สำเร็จ')&&!r.includes('ไม่');}).length;
  const ng=jobs.filter(j=>{const r=(j.status||'').trim();return r.includes('ไม่สำเร็จ')||r.toLowerCase()==='ng';}).length;
  const p=t-ok-ng;
  wrap.innerHTML=`<div class="job-table-wrap"><table><thead><tr><th>เลขบิล</th><th>SO ID</th><th>ลูกค้า</th><th>ผู้นำบิลเข้า</th><th>สถานะ</th></tr></thead><tbody>${rows}</tbody></table><div class="job-summary-bar"><span class="job-chip">ทั้งหมด ${t}</span>${ok?`<span class="job-chip ok">✅ ${ok}</span>`:''}${ng?`<span class="job-chip fail">❌ ${ng}</span>`:''}${p?`<span class="job-chip">⏳ ${p}</span>`:''}</div></div>`;
}
function _collectJobs(driverName,date){const jobs=[];(jobApiCache[date]||[]).forEach(day=>{(day.drivers||[]).forEach(d=>{if(d.driver_name!==driverName)return;(d.jobs||[]).forEach((j,i)=>{jobs.push({...j,_key:`${driverName}_${day.date}_${i}`,_date:day.date});});});});return jobs;}

function goToStep2(){
  let ok=true;
  const date=document.getElementById('s1-work-date').value;
  const driver=document.getElementById('s1-driver-name').value;
  const plate=document.getElementById('s1-vehicle-id').value;
  ['s1-err-date','s1-err-driver','s1-err-plate','s1-err-time'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display='none';});
  ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id=>document.getElementById(id).classList.remove('is-invalid'));
  document.getElementById('s1-time-block')?.classList.remove('is-invalid');
  if(!date){document.getElementById('s1-err-date').style.display='block';document.getElementById('s1-work-date').classList.add('is-invalid');ok=false;}
  if(!driver||driver==='__other__'){document.getElementById('s1-err-driver').style.display='block';document.getElementById('s1-driver-select').classList.add('is-invalid');ok=false;}
  if(!plate||plate==='__other__'){document.getElementById('s1-err-plate').style.display='block';document.getElementById('s1-plate-select').classList.add('is-invalid');ok=false;}
  const sh=+document.getElementById('s1-start-h').value||0;const sm=+document.getElementById('s1-start-m').value||0;
  const eh=+document.getElementById('s1-end-h').value||0;const em=+document.getElementById('s1-end-m').value||0;
  const startMin=sh*60+sm;const endMin=eh*60+em;
  const errTime=document.getElementById('s1-err-time');
  if(startMin===0&&endMin===0){if(errTime){errTime.textContent='กรุณาเลือกเวลาเริ่มและเวลาสิ้นสุด';errTime.style.display='block';}document.getElementById('s1-time-block')?.classList.add('is-invalid');ok=false;}
  else if(startMin===endMin){if(errTime){errTime.textContent='เวลาเริ่มและเวลาสิ้นสุดต้องไม่เหมือนกัน';errTime.style.display='block';}document.getElementById('s1-time-block')?.classList.add('is-invalid');ok=false;}
  if(!ok)return;

  jobs.forEach(j=>{
      const raw=(j.status||'').trim();
      const noteText=(j.note||'').trim();
      // ถ้า note = ส่งสำเร็จ ให้นับเป็น ok
      const effectiveStatus = (noteText === 'ส่งสำเร็จ' || noteText === 'สำเร็จ') ? 'ส่งสำเร็จ' : raw;
      const isOk = effectiveStatus.includes('สำเร็จ') && !effectiveStatus.includes('ไม่');
      const isNg = effectiveStatus.includes('ไม่สำเร็จ') || effectiveStatus.toLowerCase()==='ng';
      if(isOk) okCount++;
      else if(isNg) ngCount++;
  });
  setF('f-ok',okCount);setF('f-ng',ngCount);
  if(jobs.length>0){
    fetch(ROUTE_SYNC_NG,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},body:JSON.stringify({date,jobs:jobs.map(j=>({bill_no:j.bill_no,so_id:j.so_id||'',driver_name:driver,bill_in_by:j.bill_in_by||'',customer_name:j.customer_name||'',status:(j.status||'').trim(),note:j.note||'',})),})}).catch(e=>console.warn(e));
  }
  const sT=getTimeVal('s1-start-h','s1-start-m'),eT=getTimeVal('s1-end-h','s1-end-m');
  setF('f-work-date',date);setF('f-driver-name',driver);setF('f-vehicle-id',plate);setF('f-start-time',sT);setF('f-end-time',eT);
  document.getElementById('chipDriver').textContent=driver;document.getElementById('chipPlate').textContent=plate;document.getElementById('chipDate').textContent=date;
  if(sT&&eT){document.getElementById('chipTime').textContent=sT+' – '+eT+' น.';document.getElementById('chipTimeWrap').style.display='flex';}
  else document.getElementById('chipTimeWrap').style.display='none';
  document.getElementById('summaryRow').style.display='flex';
  document.getElementById('step1Modal').classList.remove('open');document.getElementById('fuelModal').classList.add('open');
  loadOilPrice('diesel');calcPreview();fetchPrevMileage(plate,date);
}
function backToStep1(){document.getElementById('fuelModal').classList.remove('open');document.getElementById('step1Modal').classList.add('open');}
function openModal(id=null){
  isEditMode=!!id;editId=id;
  if(id){
    const allLogs=@json($logs);const r=allLogs.find(l=>l.id===id);if(!r)return;
    document.getElementById('modalTitle').textContent='แก้ไขข้อมูลเติมน้ำมัน';
    document.getElementById('fuelForm').action=ROUTE_UPDATE(id);
    document.getElementById('formMethod').value='PUT';
    setF('f-work-date',r.work_date);setF('f-driver-name',r.driver_name);setF('f-vehicle-id',r.vehicle_id);
    setF('f-start-time',r.start_time);setF('f-end-time',r.end_time);
    setF('f-liters',r.liters);setF('f-total-distance',r.total_distance);setF('f-total-price',r.total_price);setF('f-note',r.note);setF('f-price-per-liter',r.price_per_liter);
    document.getElementById('chipDriver').textContent=r.driver_name??'—';document.getElementById('chipPlate').textContent=r.vehicle_id??'—';document.getElementById('chipDate').textContent=r.work_date??'—';
    if(r.start_time&&r.end_time){document.getElementById('chipTime').textContent=r.start_time+' – '+r.end_time+' น.';document.getElementById('chipTimeWrap').style.display='flex';}
    else document.getElementById('chipTimeWrap').style.display='none';
    document.getElementById('summaryRow').style.display='flex';document.getElementById('backBtn').style.display='none';
    document.getElementById('backBtnFooter').style.display='none';document.getElementById('fuelModal').classList.add('open');
    loadOilPrice('diesel');fetchPrevMileage(r.vehicle_id,r.work_date,id);calcPreview();
  }else{
    document.getElementById('modalTitle').textContent='เพิ่มข้อมูลเติมน้ำมัน';
    document.getElementById('fuelForm').action=ROUTE_STORE;document.getElementById('formMethod').value='';
    document.getElementById('backBtn').style.display='';document.getElementById('backBtnFooter').style.display='';
    ['s1-driver-name','s1-vehicle-id'].forEach(i=>{const el=document.getElementById(i);if(el)el.value='';});
    ['s1-driver-other','s1-plate-other'].forEach(i=>{const el=document.getElementById(i);if(el){el.style.display='none';el.value='';}});
    document.getElementById('s1-plate-select').value='';
    setTimeDropdown('s1-start-h','s1-start-m','');setTimeDropdown('s1-end-h','s1-end-m','');
    document.getElementById('driverBanner').style.display='none';document.getElementById('s1-wh-preview').style.display='none';
    ['f-liters','f-total-price','f-total-distance','f-note','f-price-per-liter'].forEach(i=>setF(i,''));
    document.getElementById('calcBox').style.display='none';
    ['s1-err-date','s1-err-driver','s1-err-plate'].forEach(id=>document.getElementById(id).style.display='none');
    ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id=>document.getElementById(id).classList.remove('is-invalid'));
    document.getElementById('jobDateChip').style.display='none';
    const today=todayStr();document.getElementById('s1-work-date').value=today;
    (async()=>{
      setDateInputLocked(true);
      const sel=document.getElementById('s1-driver-select');sel.innerHTML='<option value="">⏳ กำลังโหลดรายชื่อ...</option>';sel.disabled=true;
      document.getElementById('jobTableWrap').innerHTML='<div class="job-loading">กำลังโหลดข้อมูลคนขับ...</div>';
      try{await fetchJobsByDate(today);lastLoadedDate=today;sel.disabled=false;populateDriverDropdown(today);document.getElementById('jobTableWrap').innerHTML='<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';}
      catch(e){console.warn(e);sel.disabled=false;}
      finally{setDateInputLocked(false);}
    })();
    document.getElementById('step1Modal').classList.add('open');
  }
}
function closeAllModals(){document.getElementById('step1Modal').classList.remove('open');document.getElementById('fuelModal').classList.remove('open');}
function setF(id,v){const el=document.getElementById(id);if(el)el.value=v??'';}

/* ═══════════════════════════════════════════════════════════════
   PREV MILEAGE / CALC PREVIEW / OIL PRICE
═══════════════════════════════════════════════════════════════ */
async function fetchPrevMileage(vid,wd,xid=null){
  try{const p=new URLSearchParams({vehicle_id:vid,work_date:wd});if(xid)p.set('exclude_id',xid);
    await fetch(`${ROUTE_PREVMILE}?${p}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
  }catch(_){}calcPreview();
}
function calcPreview(){
  const sT=document.getElementById('f-start-time')?.value??'';const eT=document.getElementById('f-end-time')?.value??'';
  const ppl=parseFloat(document.getElementById('f-price-per-liter')?.value)||0;const tp=parseFloat(document.getElementById('f-total-price')?.value)||0;
  if(tp>0&&ppl>0)setF('f-liters',(tp/ppl).toFixed(2));
  const liters=parseFloat(document.getElementById('f-liters')?.value)||0;
  let wh=0;
  if(sT&&eT){const[sh,sm]=sT.split(':').map(Number);const[eh,em]=eT.split(':').map(Number);const sM=sh*60+sm;let eM=eh*60+em;if(eM<sM)eM+=24*60;const dM=eM-sM;if(dM>0)wh=dM/60;}
  const show=wh>0||liters>0||tp>0;const calcBox=document.getElementById('calcBox');if(calcBox)calcBox.style.display=show?'block':'none';
  if(show){
    const whEl=document.getElementById('calcWorkHours');
    if(whEl){if(wh>0){const h=Math.floor(wh);const m=Math.round((wh-h)*60);if(m===0)whEl.textContent=h+'';else if(h===0)whEl.textContent=m+' น.';else whEl.textContent=`${h}:${String(m).padStart(2,'0')}`;}else whEl.textContent='—';}
    const litEl=document.getElementById('calcLitersPreview');if(litEl)litEl.textContent=liters>0?`${liters} / ฿${tp.toFixed(0)}`:'—';
    const dist=parseFloat(document.getElementById('f-total-distance')?.value)||0;
    const kmlEl=document.getElementById('calcKml');if(kmlEl)kmlEl.textContent=(liters>0&&dist>0)?(dist/liters).toFixed(2):'—';
  }
}
function switchOilType(t){
  currentOilType=t;
  document.querySelectorAll('.oil-btn').forEach(b=>{b.style.background='rgba(255,255,255,.1)';b.style.borderColor='transparent';b.style.color='rgba(255,255,255,.7)';});
  const a=document.getElementById('btnOil-'+t);if(a){a.style.background='rgba(255,255,255,.3)';a.style.borderColor='#fff';a.style.color='#fff';}
  loadOilPrice(t);
}
async function refreshOilPrice(){const btn=document.getElementById('btnRefreshOil');btn.disabled=true;btn.style.opacity='.5';await loadOilPrice(currentOilType);btn.disabled=false;btn.style.opacity='1';}
async function loadOilPrice(type){
  const config={'diesel':{label:'ดีเซล',matchName:'ดีเซล',maxPrice:55},'95':{label:'แก๊สโซฮอล์ 95',matchName:'แก๊สโซฮอล์ 95',maxPrice:50},'benzin95':{label:'เบนซิน 95',matchName:'เบนซิน 95',maxPrice:60},'91':{label:'แก๊สโซฮอล์ 91',matchName:'แก๊สโซฮอล์ 91',maxPrice:50},'e20':{label:'แก๊สโซฮอล์ E20',matchName:'E20',maxPrice:45},'e85':{label:'แก๊สโซฮอล์ E85',matchName:'E85',maxPrice:40}};
  const cfg=config[type]??config['diesel'];
  document.getElementById('oilPriceLabel').textContent=`ราคาน้ำมัน${cfg.label}`;
  document.getElementById('oilPriceShow').textContent='...';document.getElementById('oilPriceStatus').textContent='⏳ กำลังดึง...';
  document.getElementById('liveDot').className='live-dot loading';document.getElementById('liveLabel').textContent='กำลังดึง';
  document.getElementById('f-price-per-liter').value='';
  let fetched=null;
  try{
    const r=await Promise.race([fetch('https://api.chnwt.dev/thai-oil-api/latest'),new Promise((_,rj)=>setTimeout(()=>rj(new Error('t')),8000))]);
    if(r.ok){const json=await r.json();const stations=json?.response?.stations;
      if(stations){const prices=[];for(const station of Object.values(stations))for(const fuel of Object.values(station))if(fuel?.name?.includes(cfg.matchName)&&fuel?.price){const n=parseFloat(fuel.price);if(!isNaN(n)&&n>0&&n<cfg.maxPrice)prices.push(n);}
        if(prices.length>0){const freq={};for(const p of prices){const k=p.toFixed(2);freq[k]=(freq[k]||0)+1;}fetched=parseFloat(Object.entries(freq).sort((a,b)=>b[1]-a[1])[0][0]);}
      }
    }
  }catch(_){}
  const now=new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
  if(fetched){document.getElementById('oilPriceShow').textContent=fetched.toFixed(2);document.getElementById('oilPriceStatus').textContent=`✅ ราคากลาง • ${now} น.`;document.getElementById('liveDot').className='live-dot';document.getElementById('liveLabel').textContent='Live';document.getElementById('f-price-per-liter').value=fetched.toFixed(2);}
  else{document.getElementById('oilPriceShow').textContent='—';document.getElementById('oilPriceStatus').textContent=`❌ ดึงไม่ได้ • ${now} น.`;document.getElementById('liveDot').className='live-dot';document.getElementById('liveDot').style.background='var(--red)';document.getElementById('liveLabel').textContent='ไม่มีข้อมูล';document.getElementById('f-price-per-liter').value='';}
  calcPreview();
}

/* ═══════════════════════════════════════════════════════════════
   CLOCK PICKER (เก็บ logic เดิม)
═══════════════════════════════════════════════════════════════ */
const CLOCK_COLOR='#2563eb';
let clockState={target:'start',mode:'hour',h:0,m:0};
function openClock(target){clockState.target=target;clockState.h=+document.getElementById(`s1-${target}-h`).value||0;clockState.m=+document.getElementById(`s1-${target}-m`).value||0;clockState.mode='hour';document.getElementById('clock-title').textContent=target==='start'?'เลือกเวลาเริ่ม':'เลือกเวลาสิ้นสุด';document.getElementById('clock-modal').style.display='flex';switchTab('hour');updateDigital();}
function closeClock(){document.getElementById('clock-modal').style.display='none';}
function switchTab(mode){clockState.mode=mode;document.getElementById('tab-hour').classList.toggle('active',mode==='hour');document.getElementById('tab-min').classList.toggle('active',mode==='min');drawClock();}
function drawClock(){
  const svg=document.getElementById('clock-face');const cx=120,cy=120;const isHour=clockState.mode==='hour';
  let html=`<circle cx="${cx}" cy="${cy}" r="115" fill="#eff6ff"/>`;
  if(isHour){
    const rO=100,rI=65;
    for(let i=1;i<=12;i++){
      const ang=(i*30-90)*Math.PI/180;const oV=(i+12)%24;
      const ox=cx+rO*Math.cos(ang),oy=cy+rO*Math.sin(ang);const oS=clockState.h===oV;
      html+=`<circle cx="${ox}" cy="${oy}" r="15" fill="${oS?CLOCK_COLOR:'transparent'}" data-val="${oV}" style="cursor:pointer"/>`;
      html+=`<text x="${ox}" y="${oy+4}" text-anchor="middle" font-size="12" font-weight="600" fill="${oS?'#fff':'#1e40af'}" style="pointer-events:none">${String(oV).padStart(2,'0')}</text>`;
      const ix=cx+rI*Math.cos(ang),iy=cy+rI*Math.sin(ang);const iS=clockState.h===i;
      html+=`<circle cx="${ix}" cy="${iy}" r="14" fill="${iS?CLOCK_COLOR:'transparent'}" data-val="${i}" style="cursor:pointer"/>`;
      html+=`<text x="${ix}" y="${iy+4}" text-anchor="middle" font-size="13" font-weight="600" fill="${iS?'#fff':'#1e3a8a'}" style="pointer-events:none">${i}</text>`;
    }
    const useO=clockState.h>=13||clockState.h===0;const handR=useO?rO-12:rI-10;const handH=clockState.h===0?12:(clockState.h>12?clockState.h-12:clockState.h);
    const hAng=(handH*30-90)*Math.PI/180;
    html+=`<line x1="${cx}" y1="${cy}" x2="${cx+handR*Math.cos(hAng)}" y2="${cy+handR*Math.sin(hAng)}" stroke="${CLOCK_COLOR}" stroke-width="2"/>`;
  }else{
    const rMa=100,rMi=82;
    for(let i=0;i<60;i++){if(i%5===0)continue;const ang=(i*6-90)*Math.PI/180;const x=cx+rMi*Math.cos(ang),y=cy+rMi*Math.sin(ang);const iS=clockState.m===i;if(iS){html+=`<circle cx="${x}" cy="${y}" r="11" fill="${CLOCK_COLOR}" data-val="${i}" style="cursor:pointer"/><text x="${x}" y="${y+3}" text-anchor="middle" font-size="10" font-weight="600" fill="#fff" style="pointer-events:none">${String(i).padStart(2,'0')}</text>`;}else{html+=`<circle cx="${x}" cy="${y}" r="9" fill="transparent" data-val="${i}" style="cursor:pointer"/><circle cx="${x}" cy="${y}" r="2.5" fill="#93c5fd" style="pointer-events:none"/>`;}}
    for(let i=0;i<60;i+=5){const ang=(i*6-90)*Math.PI/180;const x=cx+rMa*Math.cos(ang),y=cy+rMa*Math.sin(ang);const iS=clockState.m===i;html+=`<circle cx="${x}" cy="${y}" r="14" fill="${iS?CLOCK_COLOR:'transparent'}" data-val="${i}" style="cursor:pointer"/><text x="${x}" y="${y+4}" text-anchor="middle" font-size="13" font-weight="700" fill="${iS?'#fff':'#1e3a8a'}" style="pointer-events:none">${String(i).padStart(2,'0')}</text>`;}
    const iMS=clockState.m%5===0;const handR=(iMS?rMa:rMi)-14;const ang=(clockState.m*6-90)*Math.PI/180;
    html+=`<line x1="${cx}" y1="${cy}" x2="${cx+handR*Math.cos(ang)}" y2="${cy+handR*Math.sin(ang)}" stroke="${CLOCK_COLOR}" stroke-width="2"/>`;
  }
  html+=`<circle cx="${cx}" cy="${cy}" r="4" fill="${CLOCK_COLOR}"/>`;
  svg.innerHTML=html;
  svg.querySelectorAll('circle[data-val]').forEach(c=>{c.onclick=()=>{const v=+c.getAttribute('data-val');if(clockState.mode==='hour')clockState.h=v;else clockState.m=v;updateDigital();drawClock();if(clockState.mode==='hour')setTimeout(()=>switchTab('min'),200);};});
}
function updateDigital(){document.getElementById('clock-h-val').textContent=String(clockState.h).padStart(2,'0');document.getElementById('clock-m-val').textContent=String(clockState.m).padStart(2,'0');}
function confirmClock(){
  const t=clockState.target;
  document.getElementById(`s1-${t}-h`).value=clockState.h;document.getElementById(`s1-${t}-m`).value=clockState.m;
  document.getElementById(`s1-${t}-display`).textContent=`${String(clockState.h).padStart(2,'0')}:${String(clockState.m).padStart(2,'0')}`;
  closeClock();calcWorkHours();
  const errTimeEl=document.getElementById('s1-err-time');if(errTimeEl)errTimeEl.style.display='none';
  document.getElementById('s1-time-block')?.classList.remove('is-invalid');
}
function calcWorkHours(){
  const sh=+document.getElementById('s1-start-h').value||0;const sm=+document.getElementById('s1-start-m').value||0;
  const eh=+document.getElementById('s1-end-h').value||0;const em=+document.getElementById('s1-end-m').value||0;
  const sM=sh*60+sm;let eM=eh*60+em;if(eM<sM)eM+=24*60;const dM=eM-sM;
  const preview=document.getElementById('s1-wh-preview');const valEl=document.getElementById('s1-wh-val');
  if(dM<=0){preview.style.display='none';return;}
  const h=Math.floor(dM/60);const m=dM%60;let txt='';if(h>0)txt+=h+' ชั่วโมง';if(m>0)txt+=(h>0?' ':'')+m+' นาที';if(!txt)txt='0 นาที';
  valEl.textContent=txt;preview.style.display='block';
}

/* ═══════════════════════════════════════════════════════════════
   REPORT PAGE — สรุปรายงาน (pie charts + stat cards)
═══════════════════════════════════════════════════════════════ */
const REPORT_LOGS = @json($logs);
let reportRendered = false;
let _reportCharts = {};
function renderReportPage(){
  if(reportRendered) return;
  reportRendered = true;
  const logs = REPORT_LOGS || [];
  const byDriver = {};
  let totalPrice=0, totalLiters=0, totalHours=0, totalKm=0, kmlSum=0, kmlCount=0;
  logs.forEach(r=>{
    const n = r.driver_name || '—';
    if(!byDriver[n]) byDriver[n]={price:0,liters:0,hours:0,km:0,rounds:0,kmlSum:0,kmlCount:0};
    const p = +(r.total_price||0), L = +(r.liters||0), h = +(r.work_hours||0), km = +(r.total_distance||0), kml = +(r.km_per_liter||0);
    byDriver[n].price += p; byDriver[n].liters += L; byDriver[n].hours += h; byDriver[n].km += km; byDriver[n].rounds++;
    if(kml>0){byDriver[n].kmlSum+=kml; byDriver[n].kmlCount++;}
    totalPrice += p; totalLiters += L; totalHours += h; totalKm += km;
    if(kml>0){kmlSum+=kml; kmlCount++;}
  });
  const avgKml = kmlCount>0 ? (kmlSum/kmlCount) : 0;
  // Stat cards
  const statRow = document.getElementById('repStatRow');
  if(statRow){
    statRow.innerHTML = `
      <div class="report-stat-card"><div class="report-stat-label">รายการทั้งหมด</div><div class="report-stat-value">${logs.length}</div><div class="report-stat-sub">ครั้ง</div></div>
      <div class="report-stat-card"><div class="report-stat-label">ค่าน้ำมันรวม</div><div class="report-stat-value">฿${totalPrice.toLocaleString(undefined,{maximumFractionDigits:0})}</div><div class="report-stat-sub">บาท</div></div>
      <div class="report-stat-card"><div class="report-stat-label">ลิตรที่เติม</div><div class="report-stat-value">${totalLiters.toFixed(1)}</div><div class="report-stat-sub">ลิตร</div></div>
      <div class="report-stat-card"><div class="report-stat-label">ระยะทางรวม</div><div class="report-stat-value">${totalKm.toLocaleString(undefined,{maximumFractionDigits:0})}</div><div class="report-stat-sub">กม.</div></div>
      <div class="report-stat-card"><div class="report-stat-label">เฉลี่ย km/L</div><div class="report-stat-value">${avgKml.toFixed(2)}</div><div class="report-stat-sub">กม./ลิตร</div></div>
      <div class="report-stat-card"><div class="report-stat-label">ชั่วโมงรวม</div><div class="report-stat-value">${totalHours.toFixed(1)}</div><div class="report-stat-sub">ชม.</div></div>
    `;
  }
  // Pie charts
  const drivers = Object.keys(byDriver);
  if(drivers.length === 0) return;
  const sorted = drivers.map(n=>({name:n,...byDriver[n]})).sort((a,b)=>b.price-a.price);
  const pieColors = ['#16a34a','#2563eb','#eab308','#dc2626','#9333ea','#0891b2','#ea580c','#db2777','#0d9488','#7c3aed','#84cc16','#f59e0b'];
  function makePie(canvasId, legendId, dataKey, label, formatter){
    const canvas = document.getElementById(canvasId); if(!canvas) return;
    if(_reportCharts[canvasId]) _reportCharts[canvasId].destroy();
    const data = sorted.map(d => +d[dataKey].toFixed(2));
    const labels = sorted.map(d => d.name);
    _reportCharts[canvasId] = new Chart(canvas, {
      type:'doughnut',
      data:{labels, datasets:[{data, backgroundColor:labels.map((_,i)=>pieColors[i%pieColors.length]), borderWidth:2, borderColor:'#fff'}]},
      options:{responsive:true,maintainAspectRatio:false,cutout:'55%',plugins:{legend:{display:false},datalabels:{display:false},tooltip:{callbacks:{label:ctx=>`${ctx.label}: ${formatter(ctx.raw)}`}}}}
    });
    const lg = document.getElementById(legendId);
    if(lg){
      const total = data.reduce((s,v)=>s+v,0);
      lg.innerHTML = sorted.map((d,i)=>{
        const v=data[i]; const pct = total>0 ? (v/total*100).toFixed(1) : '0';
        return `<div class="pie-legend-item"><span class="pie-legend-dot" style="background:${pieColors[i%pieColors.length]}"></span><span class="pie-legend-label">${d.name}</span><span class="pie-legend-val">${formatter(v)} · ${pct}%</span></div>`;
      }).join('');
    }
  }
  makePie('pieCost', 'pieCostLegend', 'price', 'ค่าน้ำมัน', v=>'฿'+v.toLocaleString(undefined,{maximumFractionDigits:0}));
  makePie('pieLiters', 'pieLitersLegend', 'liters', 'ลิตร', v=>v.toFixed(1)+' L');
  makePie('pieHours', 'pieHoursLegend', 'hours', 'ชั่วโมง', v=>v.toFixed(1)+' ชม.');
}

/* ═══════════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',()=>{
  updateNavDate();setInterval(updateNavDate,60000);
  buildTimeDropdowns();
  renderDlv();
  renderKmlChart();
  renderOilPage();
  drpInit();
  // Re-render charts when window resizes so inner width matches container
  let _resizeTimer = null;
  window.addEventListener('resize', () => {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(() => {
      renderDlv();
      renderKmlChart();
    }, 200);
  });
  @if($errors->any())
  openModal({{ isset($editLog['id']) ? $editLog['id'] : 'null' }});
  @endif
});
</script>
</body>
</html>
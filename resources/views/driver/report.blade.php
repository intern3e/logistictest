{{-- resources/views/driver/oil-report.blade.php — หน้าสรุปรายงานแยก --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>สรุปรายงานน้ำมัน</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
/* ═══════════════════════════════════════════════════════════════
   DESIGN SYSTEM
═══════════════════════════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
html,body{margin:0;padding:0;overflow-x:hidden;max-width:100vw}

:root{
  --bg:#fafafa; --bg-card:#ffffff; --bg-subtle:#f4f4f5; --bg-subtle2:#fafafa;
  --separator:rgba(0,0,0,.06); --separator-strong:rgba(0,0,0,.10);
  --text:#18181b; --text2:#3f3f46; --text3:#71717a; --text4:#a1a1aa; --text5:#d4d4d8;
  --blue:#f59e0b; --blue-hover:#d97706; --blue-light:#fef3c7;
  --green:#3b82f6; --green-dark:#2563eb; --green-light:#dbeafe;
  --orange:#f59e0b; --red:#ef4444; --red-light:#fee2e2;
  --radius:12px; --radius-lg:18px; --radius-xl:22px;
  --shadow-xs:0 1px 2px rgba(0,0,0,.04);
  --shadow-sm:0 1px 3px rgba(0,0,0,.05),0 1px 2px rgba(0,0,0,.04);
  --shadow:0 2px 8px rgba(0,0,0,.04),0 1px 3px rgba(0,0,0,.03);
  --shadow-xl:0 20px 50px rgba(0,0,0,.10),0 4px 12px rgba(0,0,0,.05);
  --font-thai:'IBM Plex Sans Thai','Inter',-apple-system,sans-serif;
  --font-mono:ui-monospace,'SF Mono',Menlo,monospace;
  --ease:cubic-bezier(.4,0,.2,1); --ease-out:cubic-bezier(0,0,.2,1);
}

body{font-family:var(--font-thai);background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;line-height:1.5;letter-spacing:-0.01em;}

/* ── Top nav ── */
.topnav{position:sticky;top:0;z-index:50;background:rgba(245,245,247,.78);backdrop-filter:saturate(180%) blur(20px);-webkit-backdrop-filter:saturate(180%) blur(20px);border-bottom:0.5px solid var(--separator);}
.topnav-main{display:flex;align-items:center;gap:24px;padding:0 28px;height:52px;max-width:1800px;margin:0 auto;}
.topnav-brand{display:flex;align-items:center;gap:10px;flex-shrink:0}
.topnav-brand .logo{width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:16px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(59,130,246,.3);}
.topnav-brand .title-text{font-size:14px;font-weight:600;letter-spacing:-0.02em;color:var(--text);}
.topnav-menu{display:flex;align-items:center;gap:2px;flex:1}
.topnav-menu .nav-item{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:7px;background:transparent;border:none;color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;text-decoration:none;white-space:nowrap;transition:all .15s var(--ease);}
.topnav-menu .nav-item:hover{background:rgba(0,0,0,.04);color:var(--text)}
.topnav-menu .nav-item.active{background:rgba(0,0,0,.06);color:var(--text);font-weight:600;}
.topnav-menu .nav-item .ic{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;opacity:.85;flex-shrink:0;}
.topnav-menu .nav-item .ic svg{display:block}
.topnav-right{display:flex;align-items:center;gap:10px;flex-shrink:0}
.topnav-user{display:inline-flex;align-items:center;gap:7px;padding:4px 11px 4px 4px;background:rgba(0,0,0,.04);border:0.5px solid var(--separator);border-radius:100px;color:var(--text);font-size:14px;font-weight:600;letter-spacing:-0.01em;max-width:180px;}
.topnav-user-avatar{width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;text-transform:uppercase;}
.topnav-user-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;}
.topnav-time{font-size:14px;color:var(--text3);font-family:var(--font-mono);font-weight:500;display:flex;align-items:center;gap:5px;}
.topnav-time .pulse{width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 0 0 rgba(59,130,246,.4);animation:pulse 2s infinite;}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(59,130,246,.4)}70%{box-shadow:0 0 0 7px rgba(59,130,246,0)}100%{box-shadow:0 0 0 0 rgba(59,130,246,0)}}
.topnav-toggle{display:none;background:transparent;border:none;width:32px;height:32px;font-size:17px;cursor:pointer;color:var(--text);border-radius:7px;}
.topnav-toggle:hover{background:rgba(0,0,0,.04)}
.topnav-toggle svg{display:block}

/* ── Filters bar ── */
.topnav-filters{display:flex;align-items:center;gap:16px;padding:0 28px;height:44px;max-width:1800px;margin:0 auto;border-top:0.5px solid var(--separator);overflow-x:auto;scrollbar-width:none;}
.topnav-filters::-webkit-scrollbar{display:none}
.filter-group{display:flex;align-items:center;gap:8px;flex-shrink:0}
.filter-group-label{font-size:14px;font-weight:500;color:var(--text3);white-space:nowrap;}
.segmented{display:inline-flex;background:rgba(120,120,128,.12);border-radius:7px;padding:2px;}
.segmented .seg-btn{padding:4px 12px;border-radius:5px;background:transparent;border:none;color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;white-space:nowrap;transition:all .15s var(--ease);}
.segmented .seg-btn:hover{color:var(--text)}
.segmented .seg-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.1),0 1px 1px rgba(0,0,0,.05);}
.pill-select{padding:5px 12px;border:0.5px solid var(--separator-strong);border-radius:7px;font-family:inherit;font-size:14px;font-weight:500;background:#fff;color:var(--text);min-width:130px;cursor:pointer;transition:all .15s var(--ease);outline:none;}
.pill-select:hover{border-color:rgba(0,0,0,.2)}
.pill-select:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.2)}
.pill-date{padding:5px 12px;border:0.5px solid var(--separator-strong);border-radius:7px;font-family:inherit;font-size:14px;font-weight:500;background:#fff;color:var(--text);min-width:130px;cursor:pointer;transition:all .15s var(--ease);outline:none;}
.pill-date:hover{border-color:rgba(0,0,0,.2)}
.pill-date:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.2)}

/* ── Report page ── */
.report-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;margin-bottom:20px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:var(--radius-xl);box-shadow:var(--shadow);flex-wrap:wrap;gap:12px;}
.report-title{font-size:20px;font-weight:700;letter-spacing:-0.02em;}
.report-sub{font-size:14px;opacity:.8;margin-top:3px}
.report-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.report-back{background:rgba(255,255,255,.15);color:#fff;border:0.5px solid rgba(255,255,255,.2);padding:7px 14px;border-radius:100px;font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;transition:all .15s var(--ease);text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.report-back:hover{background:rgba(255,255,255,.25)}
.report-export-btn{background:rgba(255,255,255,.2);color:#fff;border:0.5px solid rgba(255,255,255,.25);padding:7px 14px;border-radius:100px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;transition:all .15s var(--ease);display:inline-flex;align-items:center;gap:6px;}
.report-export-btn:hover{background:rgba(255,255,255,.35);transform:translateY(-1px);}
.report-export-btn:active{transform:translateY(0)}
.report-export-btn:disabled{opacity:.5;cursor:wait;transform:none;}
.report-export-btn svg{flex-shrink:0}

/* ── Stat row ── */
.report-stat-row{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px;}
.report-stat-card{background:var(--bg-card);border-radius:var(--radius-lg);padding:16px 18px;box-shadow:var(--shadow-sm);border:1px solid rgba(0,0,0,.04);transition:transform .15s var(--ease),box-shadow .15s var(--ease);}
.report-stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow);}
.report-stat-label{font-size:14px;color:var(--text3);font-weight:500;margin-bottom:6px;}
.report-stat-value{font-size:22px;font-weight:700;color:var(--text);letter-spacing:-0.02em;font-family:var(--font-mono);}
.report-stat-sub{font-size:14px;color:var(--text4);margin-top:2px}

/* ── Pie charts grid ── */
.report-pie-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
.pie-card{background:var(--bg-card);border-radius:var(--radius-xl);padding:20px;box-shadow:var(--shadow-sm);border:1px solid rgba(0,0,0,.04);}
.pie-card-title{font-size:14.5px;font-weight:600;color:var(--text);letter-spacing:-0.01em}
.pie-card-sub{font-size:14px;color:var(--text3);margin:2px 0 14px}
.pie-canvas-wrap{height:220px;position:relative}
.pie-legend{margin-top:12px;display:flex;flex-direction:column;gap:6px}
.pie-legend-item{display:flex;align-items:center;gap:8px;font-size:14px}
.pie-legend-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0}
.pie-legend-label{flex:1;color:var(--text2);font-weight:500}
.pie-legend-val{color:var(--text3);font-size:14px;font-family:var(--font-mono)}

/* ── Bar charts ── */
.charts-grid{display:grid;grid-template-columns:1fr;gap:18px;margin-bottom:24px;}
.chart-card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);padding:clamp(12px,2vw,22px);min-width:0;overflow:hidden;}
.chart-head{display:flex;align-items:baseline;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
.chart-title{font-size:clamp(14px,1.5vw,16px);font-weight:600;color:var(--text);letter-spacing:-0.02em;}
.chart-sub{font-size:14px;color:var(--text3);font-weight:400;}
.chart-canvas{position:relative;width:100%;min-width:0;}
.chart-inner{height:100%;width:100%;min-width:0;}
.chart-legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:0.5px solid var(--separator);}
.chart-legend-item{display:inline-flex;align-items:center;gap:6px;font-size:14px;color:var(--text2);font-weight:500;}
.chart-legend-dot{width:9px;height:9px;border-radius:3px}
.vehicle-toggle{margin-left:auto;display:inline-flex;background:rgba(0,0,0,.05);border-radius:8px;padding:3px;flex-shrink:0;}
.vt-btn{padding:5px 12px;border:none;background:transparent;color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;border-radius:5px;white-space:nowrap;letter-spacing:-0.01em;transition:all .15s var(--ease);}
.vt-btn:hover{color:var(--text)}
.vt-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 1px rgba(0,0,0,.04);}

/* ── Driver ranking ── */
.rank-card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);overflow:hidden;margin-bottom:24px;}
.rank-card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:0.5px solid var(--separator);gap:12px;}
.rank-card-title{font-size:15px;font-weight:600;color:var(--text);letter-spacing:-0.02em;display:flex;align-items:center;gap:8px;}
.card-count{display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 7px;background:rgba(0,0,0,.06);color:var(--text2);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);}
.sort-toggle{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.sort-label{font-size:14px;color:var(--text3);font-weight:500;white-space:nowrap;}
.sort-segmented{display:inline-flex;background:rgba(0,0,0,.05);border-radius:7px;padding:2px;}
.sort-btn{padding:4px 10px;border:none;background:transparent;color:var(--text3);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;border-radius:5px;white-space:nowrap;letter-spacing:-0.01em;transition:all .15s var(--ease);}
.sort-btn:hover{color:var(--text)}
.sort-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 1px rgba(0,0,0,.04);}
.driver-list{padding:6px;max-height:600px;overflow-y:auto;-webkit-overflow-scrolling:touch;}
.driver-list::-webkit-scrollbar{width:8px}
.driver-list::-webkit-scrollbar-thumb{background:rgba(0,0,0,.12);border-radius:100px;border:2px solid #fff;background-clip:padding-box;}
.driver-row{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;padding:10px 14px;border-radius:12px;transition:background .12s;}
.driver-row:hover{background:var(--bg-subtle)}
.driver-rank{font-family:var(--font-mono);font-size:14px;font-weight:700;width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:var(--bg-subtle);color:var(--text3);border-radius:8px;}
.driver-row:nth-child(1) .driver-rank{background:linear-gradient(135deg,#fde047,#eab308);color:#713f12;box-shadow:0 2px 6px rgba(234,179,8,.3)}
.driver-row:nth-child(2) .driver-rank{background:linear-gradient(135deg,#e5e7eb,#9ca3af);color:#1f2937;box-shadow:0 2px 6px rgba(156,163,175,.3)}
.driver-row:nth-child(3) .driver-rank{background:linear-gradient(135deg,#fdba74,#c2410c);color:#fff;box-shadow:0 2px 6px rgba(194,65,12,.3)}
.driver-row .body{min-width:0}
.driver-row .name{font-weight:600;font-size:14px;color:var(--text);letter-spacing:-0.01em;margin-bottom:2px;}
.driver-row .stats{display:flex;gap:10px;flex-wrap:wrap;font-size:14px;color:var(--text3);}
.driver-row .right{text-align:right}
.driver-row .price{font-size:14.5px;font-weight:700;color:var(--text);font-family:var(--font-mono);letter-spacing:-0.02em;}
.driver-row .kml{font-size:14px;color:var(--green-dark);font-weight:600;margin-top:2px;font-family:var(--font-mono);}
.driver-row .kml.warn{color:var(--red)}
.driver-row .thb-km{font-size:14px;color:var(--text3);font-weight:500;margin-top:1px;font-family:var(--font-mono);}
.empty-state{text-align:center;padding:50px 20px;color:var(--text4);}
.empty-state .icon{font-size:30px;margin-bottom:8px;opacity:.4}
.empty-state p{margin:0;font-size:14px}

/* ── Full data table ── */
.full-table-card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);overflow:hidden;margin-bottom:24px;}
.full-table-head{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:0.5px solid var(--separator);gap:12px;}
.full-table-title{font-size:15px;font-weight:600;color:var(--text);letter-spacing:-0.02em;display:flex;align-items:center;gap:8px;}
.search-pill{position:relative;flex-shrink:0;}
.search-pill .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text4);pointer-events:none;display:inline-flex;align-items:center;width:13px;height:13px;}
.search-pill .si svg{display:block}
.search-pill input{padding:6px 11px 6px 28px;border:0.5px solid var(--separator-strong);border-radius:100px;font-family:inherit;font-size:14px;width:200px;background:var(--bg-subtle);color:var(--text);transition:all .15s var(--ease);}
.search-pill input:focus{outline:none;background:#fff;border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.search-pill input::placeholder{color:var(--text4)}
.fuel-table-scroll{overflow-x:hidden;overflow-y:auto;max-height:600px;-webkit-overflow-scrolling:touch;}
.fuel-table-scroll::-webkit-scrollbar{width:8px;height:8px}
.fuel-table-scroll::-webkit-scrollbar-thumb{background:rgba(0,0,0,.12);border-radius:100px;border:2px solid #fff;background-clip:padding-box;}
.fuel-table{width:100%;min-width:0;border-collapse:collapse;font-size:14px;table-layout:auto;}
.fuel-table thead{position:sticky;top:0;z-index:1;background:#fff;}
.fuel-table thead th{padding:11px 10px;background:var(--bg-subtle2);font-size:14px;color:var(--text3);font-weight:600;text-align:left;border-bottom:0.5px solid var(--separator);letter-spacing:-0.01em;white-space:nowrap;}
.fuel-table thead th:first-child{padding-left:16px}
.fuel-table thead th:last-child{padding-right:16px}
.fuel-table thead th.num{text-align:right}
.fuel-table tbody td{padding:11px 10px;border-bottom:0.5px solid var(--separator);vertical-align:middle;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.fuel-table tbody td:first-child{padding-left:16px}
.fuel-table tbody td:last-child{padding-right:16px}
.fuel-table tbody tr{transition:background .12s}
.fuel-table tbody tr:hover{background:var(--bg-subtle)}
.fuel-table tbody tr:last-child td{border-bottom:none}
.fuel-table .num{text-align:right;font-variant-numeric:tabular-nums;font-family:var(--font-mono);font-size:14px;}
.row-idx{color:var(--text4);font-weight:500;font-size:14px;font-family:var(--font-mono);}
.driver-cell{min-width:0;overflow:hidden;}
.driver-name{font-weight:600;font-size:14px;color:var(--text);letter-spacing:-0.01em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.driver-plate{font-size:14px;color:var(--text3);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.date-pill{display:inline-block;padding:2px 8px;background:rgba(59,130,246,.08);color:var(--green-dark);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;letter-spacing:-0.01em;}
.time-pill{display:inline-block;padding:2px 8px;background:var(--bg-subtle);color:var(--text2);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;}
.hour-pill{display:inline-block;padding:2px 9px;background:rgba(245,158,11,.12);color:#b45309;border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;letter-spacing:-0.01em;}
.carry-hint{font-size:14px;color:var(--orange);font-weight:500;margin-top:2px;white-space:nowrap;font-family:var(--font-mono);}
.km-good{color:var(--green-dark);font-weight:600}
.km-mid{color:var(--text);font-weight:500}
.km-bad{color:var(--red);font-weight:700}
.thb-km-val{color:var(--text2);font-weight:600;}

/* ── PDF modal ── */
.pdf-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(2px);}
.pdf-modal-overlay.open{display:flex}
.pdf-modal{background:#fff;border-radius:18px;width:min(420px,92vw);box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;animation:pdfPop .2s var(--ease);}
@keyframes pdfPop{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:none}}
.pdf-modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--separator);font-size:16px;font-weight:700;}
.pdf-modal-x{border:none;background:var(--bg-subtle);width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:14px;color:var(--text2);}
.pdf-modal-x:hover{background:var(--separator)}
.pdf-modal-body{padding:20px}
.pdf-mode-tabs{display:flex;gap:6px;background:var(--bg-subtle);padding:4px;border-radius:10px;margin-bottom:18px;}
.pdf-mode-btn{flex:1;padding:8px 4px;border:none;background:transparent;border-radius:7px;font-family:inherit;font-size:14px;font-weight:600;color:var(--text2);cursor:pointer;transition:all .15s var(--ease);}
.pdf-mode-btn.active{background:#fff;color:var(--text);box-shadow:0 1px 3px rgba(0,0,0,.1);}
.pdf-field label{display:block;font-size:14px;font-weight:600;color:var(--text2);margin-bottom:8px;}
.pdf-field input{width:100%;padding:11px 12px;border:1px solid var(--separator-strong);border-radius:10px;font-family:inherit;font-size:15px;color:var(--text);background:#fff;}
.pdf-field input:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.pdf-modal-foot{display:flex;gap:10px;padding:16px 20px;border-top:1px solid var(--separator);}
.pdf-btn-cancel{flex:1;padding:11px;border:1px solid var(--separator-strong);background:#fff;border-radius:10px;font-family:inherit;font-size:15px;font-weight:600;color:var(--text2);cursor:pointer;}
.pdf-btn-cancel:hover{background:var(--bg-subtle)}
.pdf-btn-go{flex:2;padding:11px;border:none;background:var(--blue);color:#fff;border-radius:10px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s var(--ease);}
.pdf-btn-go:hover{filter:brightness(1.05);transform:translateY(-1px);}

/* ═══ RESPONSIVE ═══ */
.main{padding:clamp(14px,2.5vw,28px);max-width:1800px;margin:0 auto;}
@media (max-width:1280px){.report-stat-row{grid-template-columns:repeat(3,1fr)}}
@media (max-width:1024px){.report-pie-grid{grid-template-columns:1fr 1fr}}
@media (max-width:900px){
  .topnav-toggle{display:inline-flex}.topnav-main{padding:0 16px;gap:12px}
  .topnav-menu{position:absolute;top:52px;left:0;right:0;background:rgba(255,255,255,.95);backdrop-filter:blur(20px);flex-direction:column;align-items:stretch;gap:0;padding:8px;border-bottom:0.5px solid var(--separator);box-shadow:0 8px 24px rgba(0,0,0,.08);display:none;}
  .topnav-menu.open{display:flex}.topnav-menu .nav-item{width:100%;justify-content:flex-start;padding:11px 14px;}
  .topnav-time{display:none}
  .topnav-user-name{display:none}.topnav-user{padding:4px;max-width:none}
  .main{padding:18px 16px}
  .report-stat-row{grid-template-columns:repeat(2,1fr)}
  .report-pie-grid{grid-template-columns:1fr}
  .search-pill input{width:140px}
}
@media (max-width:640px){
  .report-stat-row{grid-template-columns:repeat(2,1fr)}
  .topnav-brand .title-text{display:none}
  .chart-card{padding:14px}
  .topnav-filters{gap:12px;padding:8px 14px;height:auto;flex-wrap:nowrap}
  .pill-select,.pill-date{min-width:auto}

  .fuel-table colgroup{display:none}.fuel-table thead{display:none}
  .fuel-table,.fuel-table tbody,.fuel-table tr,.fuel-table td{display:block;width:100%}
  .fuel-table tbody td:nth-child(n){display:flex !important}
  .fuel-table tr{background:var(--bg-card);border:1px solid var(--separator);border-radius:14px;margin-bottom:10px;padding:12px 14px;box-shadow:var(--shadow-xs);}
  .fuel-table tr:hover{background:var(--bg-card)}
  .fuel-table td{display:flex !important;justify-content:space-between;align-items:center;padding:5px 0 !important;border:none !important;text-align:right;font-size:14px;}
  .fuel-table td::before{content:attr(data-label);font-weight:600;color:var(--text3);font-size:14px;margin-right:12px;text-align:left;font-family:var(--font-thai);}
  .fuel-table td.row-idx{display:none !important}
  .fuel-table td[data-label="คนขับ"]{border-bottom:1px solid var(--separator) !important;padding-bottom:10px !important;margin-bottom:4px;}
  .fuel-table td[data-label="คนขับ"]::before{display:none}
  .fuel-table td[data-label="คนขับ"] .driver-cell{text-align:left;width:100%}
  .fuel-table td[data-label="วันที่"]{justify-content:flex-start}
  .fuel-table .driver-name{font-size:15px}
}
</style>
</head>
<body>

@php
  $currentUser = request()->filled('create_by') ? request('create_by') : 'Guest';
  $userQuery = $currentUser !== 'Guest' ? '?create_by='.urlencode($currentUser) : '';
  $allowedDrivers = ['บังเดช','กอลฟ์','เก่ง','หรั่ง','เอ้','แซม','เอ','แฟงค์','yuth','แมน','กบ','joey'];

  // Carry-forward calculation
  $allArr = $allLogs->all();
  $byKey = [];
  foreach($allArr as $idx => $r){
    $k = $r['vehicle_id'] ?? '';
    if(!isset($byKey[$k])) $byKey[$k] = [];
    $byKey[$k][] = $idx;
  }
  $effDistance = []; $effKml = []; $isCarryRow = [];
  foreach($byKey as $k => $indices){
    usort($indices, function($a, $b) use ($allArr){
      $ra = $allArr[$a]; $rb = $allArr[$b];
      $da = $ra['work_date'] ?? ''; $db = $rb['work_date'] ?? '';
      if($da !== $db) return strcmp($da, $db);
      return ((int)($ra['id']??0)) <=> ((int)($rb['id']??0));
    });
    $pending = 0;
    foreach($indices as $idx){
      $r = $allArr[$idx];
      $rid = (int)($r['id'] ?? 0);
      if(!$rid) continue;
      $price = (float)($r['total_price'] ?? 0);
      $thisDist = (float)($r['total_distance'] ?? 0);
      if($price <= 0){
        $pending += $thisDist;
        $isCarryRow[$rid] = true; $effDistance[$rid] = 0; $effKml[$rid] = 0;
      } else {
        $eff = $thisDist + $pending;
        $effDistance[$rid] = $eff;
        $liters = (float)($r['liters'] ?? 0);
        $effKml[$rid] = ($liters > 0 && $eff > 0) ? round($eff / $liters, 2) : 0;
        $isCarryRow[$rid] = false; $pending = 0;
      }
    }
  }

  // Aggregate data
  $normDrv = function($s){
    $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
    return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
  };
  $normName = function($s){
    $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
    return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
  };

  $uniqueDrivers = [];
  foreach($logs as $r){
    $n = $r['driver_name'] ?? '';
    if(!isset($uniqueDrivers[$n])) $uniqueDrivers[$n] = ['name'=>$n,'rounds'=>0,'distance'=>0,'liters'=>0,'price'=>0,'kml_sum'=>0,'kml_count'=>0,'hours'=>0];
    $uniqueDrivers[$n]['rounds']++;
    $uniqueDrivers[$n]['distance'] += $r['total_distance'] ?? 0;
    $uniqueDrivers[$n]['liters']   += $r['liters'] ?? 0;
    $uniqueDrivers[$n]['price']    += $r['total_price'] ?? 0;
    $uniqueDrivers[$n]['hours']    += (float)($r['work_hours'] ?? 0);
    if(($r['km_per_liter'] ?? 0) > 0){
      $uniqueDrivers[$n]['kml_sum'] += $r['km_per_liter'];
      $uniqueDrivers[$n]['kml_count']++;
    }
  }
  $byPrice = $uniqueDrivers;
  uasort($byPrice, fn($a,$b)=> $b['price'] <=> $a['price']);
  $byDistance = $uniqueDrivers;
  uasort($byDistance, fn($a,$b)=> $b['distance'] <=> $a['distance']);

  // Filter options
  $drvHasData = [];
  foreach($allLogs as $r){
    $nm = trim((string)($r['driver_name'] ?? ''));
    if($nm === '') continue;
    $price = (float)($r['total_price'] ?? 0);
    $dist  = (float)($r['total_distance'] ?? 0);
    if($price > 0 || $dist > 0) $drvHasData[$normDrv($nm)] = true;
  }
  $drvSource = collect($allLogs)->pluck('driver_name')->map(fn($n)=>trim((string)$n))
    ->filter(fn($n)=> $n !== '' && isset($drvHasData[$normDrv($n)]))
    ->unique()->values()->all();

  // Delivery stats
  $deliveryByDriver = [];
  foreach($logs as $log){
    $driver = $log['driver_name'] ?? 'ไม่ระบุ';
    if(!isset($deliveryByDriver[$driver])) $deliveryByDriver[$driver] = ['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];
    $deliveryByDriver[$driver]['success'] += (int)($log['delivery_success']??$log['success_count']??$log['ok_count']??0);
    $deliveryByDriver[$driver]['fail'] += (int)($log['delivery_fail']??$log['fail_count']??$log['ng_count']??0);
  }

  // KML / Cost chart data
  $plateFilterActive = ($filterPlate ?? 'all') !== 'all';
  $kmlByDriver = [];
  foreach($logs as $log){
    $rid = $log['id'] ?? null;
    $plate = $log['vehicle_id'] ?? 'ไม่ระบุ';
    $driver = $log['driver_name'] ?? '';
    $key = $plateFilterActive ? $plate : ($plate.'|'.$driver);
    $kml = ($rid !== null && isset($effKml[$rid])) ? $effKml[$rid] : (float)($log['km_per_liter']??0);
    if($kml<=0) continue;
    if(!isset($kmlByDriver[$key])) $kmlByDriver[$key]=['sum'=>0,'count'=>0,'plate'=>$plate,'driver'=>($plateFilterActive ? '' : $driver)];
    $kmlByDriver[$key]['sum']+=$kml;
    $kmlByDriver[$key]['count']++;
  }
  $costByDriver2 = [];
  foreach($logs as $log){
    $plate = $log['vehicle_id']??'ไม่ระบุ';
    $driver = $log['driver_name']??'';
    $key = $plateFilterActive ? $plate : ($plate.'|'.$driver);
    if(!isset($costByDriver2[$key])) $costByDriver2[$key]=['price'=>0,'dist'=>0,'plate'=>$plate,'driver'=>($plateFilterActive ? '' : $driver)];
    $costByDriver2[$key]['price'] += (float)($log['total_price']??0);
    $costByDriver2[$key]['dist'] += (float)($log['total_distance']??0);
  }

  // PDF + report data
  $reportLogsArr = $logs->map(function($l){
    return ['driver'=>$l['driver_name']??'','price'=>(float)($l['total_price']??0),'liters'=>(float)($l['liters']??0),'hours'=>(float)($l['work_hours']??0),'distance'=>(float)($l['total_distance']??0)];
  })->values();
  $pdfLogsArr = $allLogs->map(function($l){
    return ['driver'=>$l['driver_name']??'','plate'=>$l['vehicle_id']??'','date'=>$l['work_date']??'','start'=>$l['start_time']??'','end'=>$l['end_time']??'','price'=>(float)($l['total_price']??0),'distance'=>(float)($l['total_distance']??0),'liters'=>(float)($l['liters']??0),'kml'=>(float)($l['km_per_liter']??0),'hours'=>(float)($l['work_hours']??0)];
  })->values();

  // Log table sorting
  $orderMap = [];
  foreach($allowedDrivers as $i => $nm){ $orderMap[$normName($nm)] = $i; }
  $logsArr2 = $logs->all();
  usort($logsArr2, function($a, $b) use ($orderMap, $normName){
    $ia = $orderMap[$normName($a['driver_name'] ?? '')] ?? 999;
    $ib = $orderMap[$normName($b['driver_name'] ?? '')] ?? 999;
    if($ia !== $ib) return $ia - $ib;
    return strcmp($b['work_date'] ?? '', $a['work_date'] ?? '');
  });
  $logsSorted = collect($logsArr2);
@endphp

{{-- ══════ NAV ══════ --}}
<nav class="topnav">
  <div class="topnav-main">
    <div class="topnav-brand">
      <div class="logo">⛽</div>
      <div class="title-text">สรุปรายงานน้ำมัน</div>
    </div>
    <button type="button" class="topnav-toggle" onclick="document.getElementById('topMenu')?.classList.toggle('open')" aria-label="menu">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="topnav-menu" id="topMenu">
      <a class="nav-item" href="{{ url('/oil').$userQuery }}">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 16 4-8 4 5 4-6"/></svg></span>
        <span>ติดตามน้ำมัน</span>
      </a>
      <a class="nav-item active" href="#">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="3" y1="20" x2="21" y2="20"/></svg></span>
        <span>รายงาน</span>
      </a>
      <a class="nav-item" href="{{ url('/service').$userQuery }}">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
        <span>Service</span>
      </a>
    </div>
    <div class="topnav-right">
      <span class="topnav-user" title="ผู้ใช้ปัจจุบัน">
        <span class="topnav-user-avatar">{{ mb_substr($currentUser, 0, 1) }}</span>
        <span class="topnav-user-name">{{ $currentUser }}</span>
      </span>
      <span class="topnav-time"><span class="pulse"></span><span id="navDate">—</span></span>
    </div>
  </div>

  {{-- ── Filter bar ── --}}
  <div class="topnav-filters">
    <div class="filter-group">
      <span class="filter-group-label">มุมมอง</span>
      <div class="segmented">
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="button" class="seg-btn {{ $view===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
        @endforeach
      </div>
    </div>

    @if($view==='year')
    <div class="filter-group">
      <span class="filter-group-label">ปี</span>
      <select class="pill-select" id="yearPicker" onchange="submitFilter()">
        @php $savedYear = request('year', date('Y')); $yearMax = max((int)date('Y') + 2, (int)$savedYear); @endphp
        @for($y=$yearMax;$y>=2020;$y--)
        <option value="{{ $y }}" {{ $savedYear==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    @elseif($view==='month')
    <div class="filter-group">
      <span class="filter-group-label">เดือน</span>
      <input type="month" class="pill-date" id="monthPicker" value="{{ $filterMonth }}" onchange="submitFilter()">
    </div>
    @elseif($view==='day')
    <div class="filter-group">
      <span class="filter-group-label">วันที่</span>
      <input type="date" class="pill-date" id="datePicker" value="{{ $filterDay ?? date('Y-m-d') }}" onchange="submitFilter()">
    </div>
    @endif

    <div class="filter-group">
      <span class="filter-group-label">คนขับ</span>
      <select class="pill-select" id="driverPicker" onchange="submitFilter()">
        <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @php $seenDrv = []; @endphp
        @foreach($drvSource as $d)
          @php $nd = $normDrv($d); @endphp
          @if(!in_array($nd, $seenDrv, true))
            @php $seenDrv[] = $nd; @endphp
            <option value="{{ trim($d) }}" {{ trim($filterDriver)===trim($d)?'selected':'' }}>{{ trim($d) }}</option>
          @endif
        @endforeach
      </select>
    </div>

    <div class="filter-group">
      <span class="filter-group-label">ทะเบียน</span>
      <select class="pill-select" id="platePicker" onchange="submitFilter()">
        <option value="all" {{ ($filterPlate ?? 'all')==='all'?'selected':'' }}>ทะเบียนทั้งหมด</option>
        @foreach($plates as $p)
        <option value="{{ $p }}" {{ ($filterPlate ?? 'all')===$p?'selected':'' }}>{{ $p }}</option>
        @endforeach
      </select>
    </div>
  </div>
</nav>

{{-- ══════ MAIN CONTENT ══════ --}}
<main class="main">

  {{-- ── Header ── --}}
  <div class="report-header">
    <div>
      <div class="report-title">สรุปรายงาน</div>
      <div class="report-sub">วิเคราะห์การใช้น้ำมันแยกตามคนขับ · {{ $currentUser }}</div>
    </div>
    <div class="report-actions">
      <button type="button" class="report-export-btn" id="pdfExportBtn" onclick="openPdfRangeModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export PDF
      </button>
      <a class="report-back" href="{{ url('/oil').$userQuery }}">← กลับหน้าหลัก</a>
    </div>
  </div>

  {{-- ── Stat cards ── --}}
  <div class="report-stat-row" id="repStatRow"></div>

  {{-- ── Pie charts ── --}}
  <div class="report-pie-grid">
    <div class="pie-card">
      <div class="pie-card-title">ค่าน้ำมัน</div><div class="pie-card-sub">แยกตามคนขับ</div>
      <div class="pie-canvas-wrap"><canvas id="pieCost"></canvas></div>
      <div class="pie-legend" id="pieCostLegend"></div>
    </div>
    <div class="pie-card">
      <div class="pie-card-title">ลิตรที่เติม</div><div class="pie-card-sub">แยกตามคนขับ</div>
      <div class="pie-canvas-wrap"><canvas id="pieLiters"></canvas></div>
      <div class="pie-legend" id="pieLitersLegend"></div>
    </div>
    <div class="pie-card">
      <div class="pie-card-title">ชั่วโมงทำงาน</div><div class="pie-card-sub">แยกตามคนขับ</div>
      <div class="pie-canvas-wrap"><canvas id="pieHours"></canvas></div>
      <div class="pie-legend" id="pieHoursLegend"></div>
    </div>
  </div>

  {{-- ── Charts: delivery + km/L + cost/km ── --}}
  <div class="charts-grid">
    <div class="chart-card">
      <div class="chart-head">
        <div class="chart-title">รายการสมบูรณ์ / ผิดพลาด</div>
        <div class="chart-sub">ประสิทธิภาพการส่งสินค้าแยกตามคนขับ</div>
      </div>
      <div class="chart-canvas" style="height:300px">
        <div class="chart-inner" id="deliveryChartInner"><canvas id="deliveryChart"></canvas></div>
      </div>
      <div class="chart-legend" id="dlvLegend"></div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <div class="chart-title">น้ำมันต่อกิโล</div>
        <div class="chart-sub">เฉลี่ย km/L แต่ละคน · เกณฑ์อัตโนมัติ (ค่าเฉลี่ยรวม)</div>
        <div class="vehicle-toggle" data-chart="kml">
          <button type="button" class="vt-btn active" data-type="car" onclick="switchVehicleType('kml','car')">🚗 รถยนต์</button>
          <button type="button" class="vt-btn" data-type="moto" onclick="switchVehicleType('kml','moto')">🏍 มอเตอร์ไซค์</button>
        </div>
      </div>
      <div class="chart-canvas">
        <div class="chart-inner" id="chartKmlInner"><canvas id="chartKml"></canvas></div>
      </div>
      <div class="chart-legend" id="kmlLegend"></div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <div class="chart-title">ต้นทุนต่อกิโล (฿/km)</div>
        <div class="chart-sub">ค่าน้ำมันเฉลี่ยต่อระยะทาง 1 กิโลเมตร · ยิ่งน้อยยิ่งดี</div>
        <div class="vehicle-toggle" data-chart="cost">
          <button type="button" class="vt-btn active" data-type="car" onclick="switchVehicleType('cost','car')">🚗 รถยนต์</button>
          <button type="button" class="vt-btn" data-type="moto" onclick="switchVehicleType('cost','moto')">🏍 มอเตอร์ไซค์</button>
        </div>
      </div>
      <div class="chart-canvas">
        <div class="chart-inner" id="chartCostInner"><canvas id="chartCost"></canvas></div>
      </div>
      <div class="chart-legend" id="costLegend"></div>
    </div>
  </div>

  {{-- ── Driver ranking ── --}}
  <div class="rank-card">
    <div class="rank-card-head">
      <div class="rank-card-title">
        อันดับคนขับ
        <span class="card-count">{{ count($uniqueDrivers) }}</span>
      </div>
      <div class="sort-toggle">
        <span class="sort-label">เรียงตาม</span>
        <div class="sort-segmented">
          <button type="button" class="sort-btn active" data-sort="price" onclick="switchRankSort('price')">฿ ค่าน้ำมัน</button>
          <button type="button" class="sort-btn" data-sort="distance" onclick="switchRankSort('distance')">km ระยะทาง</button>
        </div>
      </div>
    </div>

    <div class="driver-list" id="rankListPrice">
      @php $rankNo = 0; @endphp
      @forelse($byPrice as $d)
      @php
        $rankNo++;
        $avgKmlD = $d['kml_count'] > 0 ? $d['kml_sum'] / $d['kml_count'] : 0;
        $kmlBad = $avgKmlD > 0 && $avgKmlD < 9;
        $thbPerKmD = $d['distance'] > 0 ? $d['price'] / $d['distance'] : 0;
      @endphp
      <div class="driver-row">
        <div class="driver-rank">{{ str_pad((string)$rankNo, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="body">
          <div class="name">{{ $d['name'] }}</div>
          <div class="stats">
            <span>{{ $d['rounds'] }} รอบ</span><span>·</span>
            <span>{{ number_format($d['distance']) }} km</span><span>·</span>
            <span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span>
          </div>
        </div>
        <div class="right">
          <div class="price">฿{{ number_format($d['price']) }}</div>
          @if($avgKmlD > 0)<div class="kml {{ $kmlBad ? 'warn' : '' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>@endif
          @if($thbPerKmD > 0)<div class="thb-km">฿{{ number_format($thbPerKmD, 2) }}/km</div>@endif
        </div>
      </div>
      @empty
      <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
      @endforelse
    </div>

    <div class="driver-list" id="rankListDistance" style="display:none">
      @php $rankNo = 0; @endphp
      @forelse($byDistance as $d)
      @php
        $rankNo++;
        $avgKmlD = $d['kml_count'] > 0 ? $d['kml_sum'] / $d['kml_count'] : 0;
        $kmlBad = $avgKmlD > 0 && $avgKmlD < 9;
        $thbPerKmD = $d['distance'] > 0 ? $d['price'] / $d['distance'] : 0;
      @endphp
      <div class="driver-row">
        <div class="driver-rank">{{ str_pad((string)$rankNo, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="body">
          <div class="name">{{ $d['name'] }}</div>
          <div class="stats">
            <span>{{ $d['rounds'] }} รอบ</span><span>·</span>
            <span>฿{{ number_format($d['price']) }}</span><span>·</span>
            <span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span>
          </div>
        </div>
        <div class="right">
          <div class="price">{{ number_format($d['distance']) }} <span style="font-size:14px;color:var(--text3);font-weight:500">km</span></div>
          @if($avgKmlD > 0)<div class="kml {{ $kmlBad ? 'warn' : '' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>@endif
          @if($thbPerKmD > 0)<div class="thb-km">฿{{ number_format($thbPerKmD, 2) }}/km</div>@endif
        </div>
      </div>
      @empty
      <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
      @endforelse
    </div>
  </div>

  {{-- ── Full data table ── --}}
  <div class="full-table-card">
    <div class="full-table-head">
      <div class="full-table-title">
        รายการเติมน้ำมันทั้งหมด
        <span class="card-count" id="oilCount">{{ $logs->count() }}</span>
      </div>
      <div class="search-pill">
        <span class="si"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></span>
        <input type="text" placeholder="ค้นหา" oninput="filterOilTable(this.value)">
      </div>
    </div>

    <div class="fuel-table-scroll">
    <table class="fuel-table">
      <colgroup>
        <col style="width:44px"><col style="width:78px"><col style="width:auto;min-width:170px">
        <col style="width:96px"><col style="width:70px"><col style="width:80px">
        <col style="width:66px"><col style="width:84px"><col style="width:74px"><col style="width:78px">
      </colgroup>
      <thead>
        <tr>
          <th>#</th><th>วันที่</th><th>คนขับ / ทะเบียน</th><th>เวลา</th>
          <th class="num">ชม.</th><th class="num">ระยะ</th><th class="num">ลิตร</th>
          <th class="num">฿</th><th class="num">KM/L</th><th class="num">฿/km</th>
        </tr>
      </thead>
      <tbody id="oilTbody">
        @php $rowNo = 0; @endphp
        @forelse($logsSorted as $r)
        @php
          $rowNo++;
          $rid = (int)($r['id'] ?? 0);
          $effDist = $effDistance[$rid] ?? ((float)($r['total_distance']??0));
          $kml = $effKml[$rid] ?? ($r['km_per_liter']??0);
          $rawDist = (float)($r['total_distance']??0);
          $carryAmt = $effDist - $rawDist;
          if($rawDist > 0){
            $distHtml = number_format($rawDist).' km';
            if($carryAmt > 0) $distHtml .= '<div class="carry-hint" title="รวมระยะจากวันที่ไม่เติม">+'.number_format($carryAmt).' km สะสม</div>';
          } else { $distHtml = '—'; }
          $name = $r['driver_name'] ?? '—';
          $plate = $r['vehicle_id'] ?? '—';
          $kmlClass = 'km-mid';
          if($kml >= 13) $kmlClass = 'km-good';
          elseif($kml > 0 && $kml < 9) $kmlClass = 'km-bad';
          $tStart = $r['start_time'] ?? ''; $tEnd = $r['end_time'] ?? '';
          if(strlen($tStart) >= 5) $tStart = substr($tStart, 0, 5);
          if(strlen($tEnd) >= 5) $tEnd = substr($tEnd, 0, 5);
          $timeText = ($tStart && $tEnd) ? $tStart.'-'.$tEnd : '—';
          $wh = (float)($r['work_hours'] ?? 0);
          $durText = '';
          if($wh > 0){
            $totalMin = (int) round($wh * 60);
            $days = intdiv($totalMin, 1440);
            $hh = intdiv($totalMin % 1440, 60);
            $mm = $totalMin % 60;
            if($days > 0){
              $durText = $days.' วัน';
              if($hh > 0) $durText .= ' '.$hh.' ชม.';
              if($mm > 0) $durText .= ' '.$mm.' น.';
            } elseif($hh > 0 && $mm > 0) $durText = $hh.' ชม. '.$mm.' น.';
            elseif($hh > 0) $durText = $hh.' ชม.';
            else $durText = $mm.' น.';
          }
          $workDate = $r['work_date'] ?? '';
          $dateText = '—'; $dateFull = '';
          if($workDate){
            try {
              $dt = \Carbon\Carbon::parse($workDate);
              $dateText = $dt->format('d/m'); $dateFull = $dt->format('d/m/Y');
            } catch(\Exception $e){ $dateText = '—'; }
          }
          $thbPerKm = ($effDist > 0 && ($r['total_price']??0) > 0) ? ($r['total_price'] / $effDist) : 0;
        @endphp
        <tr data-driver="{{ strtolower($name) }}">
          <td class="row-idx" data-label="#">{{ str_pad((string)$rowNo, 2, '0', STR_PAD_LEFT) }}</td>
          <td data-label="วันที่"><span class="date-pill" title="{{ $dateFull }}">{{ $dateText }}</span></td>
          <td data-label="คนขับ">
            <div class="driver-cell">
              <div class="driver-name" title="{{ $name }}">{{ $name }}</div>
              <div class="driver-plate" title="{{ $plate }}">{{ $plate }}</div>
            </div>
          </td>
          <td data-label="เวลา"><span class="time-pill">{{ $timeText }}</span></td>
          <td class="num" data-label="ชม.">{!! $durText ? '<span class="hour-pill">'.$durText.'</span>' : '<span style="color:var(--text4)">—</span>' !!}</td>
          <td class="num" data-label="ระยะ">{!! $distHtml !!}</td>
          <td class="num" data-label="ลิตร">{{ $r['liters']?rtrim(rtrim(number_format($r['liters'],2,'.',''),'0'),'.'):'—' }}</td>
          <td class="num" data-label="ค่าน้ำมัน">{{ $r['total_price']?'฿'.number_format($r['total_price']):'—' }}</td>
          <td class="num" data-label="KM/L">
            @if($kml>0)<span class="{{ $kmlClass }}">{{ rtrim(rtrim(number_format($kml,2,'.',''),'0'),'.') }}</span>
            @else<span style="color:var(--text4)">—</span>@endif
          </td>
          <td class="num" data-label="฿/km">
            @if($thbPerKm > 0)<span class="thb-km-val">฿{{ number_format($thbPerKm, 2) }}</span>
            @else<span style="color:var(--text4)">—</span>@endif
          </td>
        </tr>
        @empty
        <tr><td colspan="10"><div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการ</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>

</main>

{{-- ══════ PDF MODAL ══════ --}}
<div class="pdf-modal-overlay" id="pdfModalOverlay" onclick="if(event.target===this)closePdfRangeModal()">
  <div class="pdf-modal">
    <div class="pdf-modal-head">
      <span>ดาวน์โหลดรายงาน PDF</span>
      <button type="button" class="pdf-modal-x" onclick="closePdfRangeModal()">✕</button>
    </div>
    <div class="pdf-modal-body">
      <div class="pdf-mode-tabs">
        <button type="button" class="pdf-mode-btn active" data-mode="range" onclick="setPdfMode('range')">ช่วงวันที่</button>
        <button type="button" class="pdf-mode-btn" data-mode="single" onclick="setPdfMode('single')">วันเดียว</button>
      </div>
      <div id="pdfRangeFields">
        <div class="pdf-field" style="margin-bottom:14px">
          <label>ตั้งแต่วันที่</label>
          <input type="date" id="pdfDateFrom">
        </div>
        <div class="pdf-field">
          <label>ถึงวันที่</label>
          <input type="date" id="pdfDateTo">
        </div>
      </div>
      <div id="pdfSingleFields" style="display:none">
        <div class="pdf-field">
          <label>เลือกวันที่</label>
          <input type="date" id="pdfSingleDate" value="{{ date('Y-m-d') }}">
        </div>
      </div>
    </div>
    <div class="pdf-modal-foot">
      <button type="button" class="pdf-btn-cancel" onclick="closePdfRangeModal()">ยกเลิก</button>
      <button type="button" class="pdf-btn-go" onclick="confirmPdfExport()">📄 สร้าง PDF</button>
    </div>
  </div>
</div>

{{-- ══════ JAVASCRIPT ══════ --}}
<script>
/* ═══════════════════════════════════════════════════════════════
   CONSTANTS
═══════════════════════════════════════════════════════════════ */
const ROUTE_FILTER  = '{{ route("oil.filter") }}';
const CURRENT_USER  = @json($currentUser);
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const TZ            = 'Asia/Bangkok';
const MAIN_VIEW     = @json($view);
const ALLOWED_DRIVERS = @json($allowedDrivers);

function fmtN(v, max=2){ return (+(+v).toFixed(max)).toString(); }

/* ═══════════════════════════════════════════════════════════════
   NAV TIME
═══════════════════════════════════════════════════════════════ */
function updateNavDate(){
  const el = document.getElementById('navDate');
  if(el) el.textContent = new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
}

/* ═══════════════════════════════════════════════════════════════
   FILTER
═══════════════════════════════════════════════════════════════ */
function submitFilterForm(params){
  const form = document.createElement('form');
  form.method='POST'; form.action=ROUTE_FILTER; form.style.display='none';
  const add=(n,v)=>{ if(v==null||v==='')return; const i=document.createElement('input'); i.type='hidden'; i.name=n; i.value=v; form.appendChild(i); };
  add('_token',CSRF_TOKEN);
  add('redirect_to','report');
  if(CURRENT_USER && CURRENT_USER !== 'Guest') add('create_by', CURRENT_USER);
  Object.keys(params).forEach(k=>add(k,params[k]));
  document.body.appendChild(form); form.submit();
}
function switchView(v){
  const params={view:v};
  const ds=document.getElementById('driverPicker'); if(ds&&ds.value)params.driver_name=ds.value;
  const ps=document.getElementById('platePicker'); if(ps&&ps.value)params.vehicle_id=ps.value;
  if(v==='month'){ const el=document.getElementById('monthPicker'); if(el&&el.value)params.month=el.value; }
  else if(v==='year'){ const el=document.getElementById('yearPicker'); if(el&&el.value)params.year=el.value; }
  else if(v==='day'){ const el=document.getElementById('datePicker'); if(el&&el.value){ params.date_from=el.value; params.date_to=el.value; } }
  submitFilterForm(params);
}
function submitFilter(){
  const params={view:MAIN_VIEW};
  const ds=document.getElementById('driverPicker'); if(ds&&ds.value)params.driver_name=ds.value;
  const ps=document.getElementById('platePicker'); if(ps&&ps.value)params.vehicle_id=ps.value;
  const me=document.getElementById('monthPicker'); if(me&&me.value)params.month=me.value;
  const ye=document.getElementById('yearPicker'); if(ye&&ye.value)params.year=ye.value;
  const de=document.getElementById('datePicker'); if(de&&de.value){ params.date_from=de.value; params.date_to=de.value; }
  submitFilterForm(params);
}

/* ═══════════════════════════════════════════════════════════════
   DRIVER NORMALIZE
═══════════════════════════════════════════════════════════════ */
function _normalizeName(s){
  if(!s) return '';
  return String(s).replace(/[\u200B-\u200D\uFEFF]/g,'').replace(/\s+/g,' ').trim().toLowerCase();
}
const DRIVER_ALIASES = {
  'กอลฟ':'กอลฟ','กอลฟ์':'กอลฟ','แฟงค':'แฟงค','แฟรงค':'แฟงค',
  'yuth':'yuth','ยุทร':'yuth','ยุท':'yuth','joey':'joey','โจอี':'joey',
  'แซม':'แซม','แชม':'แซม',
};
function _normalizeDriver(s){
  let n = _normalizeName(s).replace(/\u0E4C/g,'');
  return DRIVER_ALIASES[n] || n;
}
const _allowedSet = new Set(ALLOWED_DRIVERS.map(_normalizeDriver));
function isAllowedDriver(name){ return _allowedSet.has(_normalizeDriver(name)); }

/* ═══════════════════════════════════════════════════════════════
   TABLE SEARCH + RANK SORT
═══════════════════════════════════════════════════════════════ */
let oilSearchQuery='';
function filterOilTable(q){
  oilSearchQuery=q.toLowerCase();
  const rows=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));
  let visCount=0;
  rows.forEach(r=>{
    const show = !oilSearchQuery || r.dataset.driver.includes(oilSearchQuery);
    r.style.display = show ? '' : 'none';
    if(show) visCount++;
  });
  const c=document.getElementById('oilCount'); if(c)c.textContent=visCount;
}
function switchRankSort(mode){
  document.querySelectorAll('.sort-btn').forEach(b=>b.classList.toggle('active', b.dataset.sort===mode));
  document.getElementById('rankListPrice').style.display = (mode==='price') ? '' : 'none';
  document.getElementById('rankListDistance').style.display = (mode==='distance') ? '' : 'none';
}

/* ═══════════════════════════════════════════════════════════════
   CHART: delivery
═══════════════════════════════════════════════════════════════ */
let dlvChart=null;
const DLV_BY_DRIVER=@json($deliveryByDriver);
function renderDlv(){
  const drivers=Object.keys(DLV_BY_DRIVER).filter(d=>isAllowedDriver(d));
  if(drivers.length===0){ if(dlvChart)dlvChart.destroy(); document.getElementById('dlvLegend').innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>'; return; }
  const orderIdx=name=>{ const i=ALLOWED_DRIVERS.map(_normalizeDriver).indexOf(_normalizeDriver(name)); return i<0?999:i; };
  const sorted=drivers.map(d=>({name:d,s:DLV_BY_DRIVER[d].success,f:DLV_BY_DRIVER[d].fail})).sort((a,b)=>orderIdx(a.name)-orderIdx(b.name));
  const inner=document.getElementById('deliveryChartInner');
  if(inner){ inner.style.width='100%'; inner.style.height='100%'; }
  if(dlvChart)dlvChart.destroy();
  dlvChart=new Chart(document.getElementById('deliveryChart'),{
    type:'bar',
    data:{labels:sorted.map(d=>d.name),datasets:[
      {label:'ส่งสำเร็จ',data:sorted.map(d=>d.s),backgroundColor:'#10b981',borderRadius:{topLeft:0,topRight:0,bottomLeft:6,bottomRight:6},borderSkipped:false,stack:'s',maxBarThickness:50},
      {label:'ส่งไม่สำเร็จ',data:sorted.map(d=>d.f),backgroundColor:'#ef4444',borderRadius:{topLeft:6,topRight:6,bottomLeft:0,bottomRight:0},borderSkipped:false,stack:'s',maxBarThickness:50},
    ]},
    plugins:[ChartDataLabels],
    options:{
      responsive:true,maintainAspectRatio:false,
      layout:{padding:{top:20,left:10,right:10}},
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:ctx=>`${ctx.dataset.label}: ${ctx.raw} รายการ`}},
        datalabels:{color:'#fff',font:{weight:'700',size:13,family:'Inter'},formatter:v=>v>0?v:'',display:ctx=>ctx.dataset.data[ctx.dataIndex]>0,anchor:'center',align:'center'},
      },
      scales:{
        x:{stacked:true,ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#18181b',autoSkip:true,maxRotation:0},grid:{display:false}},
        y:{stacked:true,beginAtZero:true,ticks:{font:{size:14,family:'Inter'},color:'#71717a',stepSize:2},grid:{color:'rgba(0,0,0,.05)'}},
      },
    },
  });
  document.getElementById('dlvLegend').innerHTML=`
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ส่งสำเร็จ</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ส่งไม่สำเร็จ</div>`;
}

/* ═══════════════════════════════════════════════════════════════
   CHART: km/L + cost/km
═══════════════════════════════════════════════════════════════ */
const KML_BY_DRIVER=@json($kmlByDriver);
const COST_BY_DRIVER=@json($costByDriver2);
const VEHICLE_TYPE={kml:'car', cost:'car'};

function isMoto(plate){
  const p=(plate||'').trim();
  return p.startsWith('มอเตอร์ไซด์')||p.startsWith('มอเตอร์ไซค์')||p.startsWith('มอ.')||p.startsWith('มอ ');
}
function switchVehicleType(chart, type){
  VEHICLE_TYPE[chart]=type;
  document.querySelectorAll(`.vehicle-toggle[data-chart="${chart}"] .vt-btn`).forEach(b=>b.classList.toggle('active', b.dataset.type===type));
  if(chart==='kml') renderKmlChart(); else if(chart==='cost') renderCostChart();
}

let kmlChart=null;
function renderKmlChart(){
  const vType=VEHICLE_TYPE.kml||'car';
  const drivers=Object.keys(KML_BY_DRIVER).map(key=>({name:KML_BY_DRIVER[key].driver||'',plate:KML_BY_DRIVER[key].plate||key,avg:KML_BY_DRIVER[key].count>0?KML_BY_DRIVER[key].sum/KML_BY_DRIVER[key].count:0}))
    .filter(d=>d.avg>0).filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate)).sort((a,b)=>b.avg-a.avg);
  if(drivers.length===0){
    if(kmlChart)kmlChart.destroy();
    document.getElementById('kmlLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${vType==='moto'?'มอเตอร์ไซค์':'รถยนต์'}</span>`;
    return;
  }
  const inner=document.getElementById('chartKmlInner');
  if(inner){ inner.style.width='100%'; inner.style.height=Math.max(drivers.length*44+40,300)+'px'; }
  const labels=drivers.map(d=>[d.plate||d.name, d.name && d.plate ? d.name : '']);
  const data=drivers.map(d=>d.avg);
  const overallAvg=data.reduce((a,b)=>a+b,0)/data.length;
  const lowBand=overallAvg*0.9;
  const barColors=data.map(v=>v<lowBand?'#ef4444':(v<overallAvg?'#f59e0b':'#10b981'));
  const xMax=Math.ceil((Math.max(...data,overallAvg)+1)/2)*2;
  if(kmlChart)kmlChart.destroy();
  kmlChart=new Chart(document.getElementById('chartKml'),{
    type:'bar',
    data:{labels,datasets:[{label:'เฉลี่ย km/L',data,backgroundColor:barColors,borderRadius:6,borderSkipped:false,maxBarThickness:28}]},
    plugins:[ChartDataLabels,{id:'kmlThreshold',afterDatasetsDraw(chart){
      const {ctx,chartArea:{top,bottom},scales:{x}}=chart;
      const xPos=x.getPixelForValue(overallAvg);
      ctx.save();ctx.strokeStyle='#ef4444';ctx.setLineDash([6,4]);ctx.lineWidth=2;
      ctx.beginPath();ctx.moveTo(xPos,top);ctx.lineTo(xPos,bottom);ctx.stroke();
      ctx.setLineDash([]);ctx.fillStyle='#ef4444';ctx.font='600 11px Inter';ctx.textAlign='left';
      ctx.fillText('เกณฑ์เฉลี่ย '+fmtN(overallAvg),xPos+6,top+12);ctx.restore();
    }}],
    options:{
      indexAxis:'y',responsive:true,maintainAspectRatio:false,
      layout:{padding:{top:10,right:50,left:6,bottom:6}},
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:ctx=>`เฉลี่ย: ${fmtN(ctx.raw)} km/L`}},
        datalabels:{color:'#18181b',font:{weight:'700',size:11,family:'Inter'},anchor:'end',align:'right',offset:4,formatter:v=>fmtN(v)+' km/L'},
      },
      scales:{
        x:{beginAtZero:true,suggestedMax:xMax,ticks:{stepSize:2,font:{size:14,family:'Inter'},color:'#71717a'},grid:{color:'rgba(0,0,0,.05)'}},
        y:{grid:{display:false},ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#3f3f46',autoSkip:false,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}}},
      },
    },
  });
  document.getElementById('kmlLegend').innerHTML=`
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (≥ เฉลี่ย)</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (ใกล้เฉลี่ย)</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ผิดปกติ (ต่ำกว่าเฉลี่ย 10%)</div>
    <div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">${fmtN(overallAvg)} km/L</strong></div>`;
}

let costChart=null;
function renderCostChart(){
  const vType=VEHICLE_TYPE.cost||'car';
  const drivers=Object.keys(COST_BY_DRIVER).map(key=>({name:COST_BY_DRIVER[key].driver||'',plate:COST_BY_DRIVER[key].plate||key,cost:COST_BY_DRIVER[key].dist>0?COST_BY_DRIVER[key].price/COST_BY_DRIVER[key].dist:0,price:COST_BY_DRIVER[key].price,dist:COST_BY_DRIVER[key].dist}))
    .filter(d=>d.cost>0).filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate)).sort((a,b)=>a.cost-b.cost);
  if(drivers.length===0){
    if(costChart)costChart.destroy();
    document.getElementById('costLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${vType==='moto'?'มอเตอร์ไซค์':'รถยนต์'}</span>`;
    return;
  }
  const inner=document.getElementById('chartCostInner');
  if(inner){ inner.style.width='100%'; inner.style.height=Math.max(drivers.length*44+40,300)+'px'; }
  const labels=drivers.map(d=>[d.plate||d.name, d.name && d.plate ? d.name : '']);
  const data=drivers.map(d=>d.cost);
  const avg=data.reduce((a,b)=>a+b,0)/data.length;
  const barColors=data.map(v=>v<=avg*0.85?'#10b981':(v<=avg*1.05?'#f59e0b':'#ef4444'));
  const xMax=Math.ceil(Math.max(...data)*1.15);
  if(costChart)costChart.destroy();
  costChart=new Chart(document.getElementById('chartCost'),{
    type:'bar',
    data:{labels,datasets:[{label:'฿/km',data,backgroundColor:barColors,borderRadius:6,borderSkipped:false,maxBarThickness:28}]},
    plugins:[ChartDataLabels,{id:'costAvgLine',afterDatasetsDraw(chart){
      const {ctx,chartArea:{top,bottom},scales:{x}}=chart;
      const xPos=x.getPixelForValue(avg);
      ctx.save();ctx.strokeStyle='#3b82f6';ctx.setLineDash([6,4]);ctx.lineWidth=2;
      ctx.beginPath();ctx.moveTo(xPos,top);ctx.lineTo(xPos,bottom);ctx.stroke();
      ctx.setLineDash([]);ctx.fillStyle='#3b82f6';ctx.font='600 11px Inter';ctx.textAlign='left';
      ctx.fillText('เฉลี่ย ฿'+fmtN(avg),xPos+6,top+12);ctx.restore();
    }}],
    options:{
      indexAxis:'y',responsive:true,maintainAspectRatio:false,
      layout:{padding:{top:10,right:70,left:6,bottom:6}},
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:ctx=>`฿${fmtN(ctx.raw)} / km`}},
        datalabels:{color:'#18181b',font:{weight:'700',size:11,family:'Inter'},anchor:'end',align:'right',offset:4,formatter:v=>'฿'+fmtN(v)},
      },
      scales:{
        x:{beginAtZero:true,suggestedMax:xMax,ticks:{font:{size:14,family:'Inter'},color:'#71717a',callback:v=>'฿'+v},grid:{color:'rgba(0,0,0,.05)'}},
        y:{grid:{display:false},ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#3f3f46',autoSkip:false,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}}},
      },
    },
  });
  document.getElementById('costLegend').innerHTML=`
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (ต่ำกว่าเฉลี่ย ≥15%)</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (±5–15%)</div>
    <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>สูง (สูงกว่าเฉลี่ย >5%)</div>
    <div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">฿${fmtN(avg)}/km</strong></div>`;
}

/* ═══════════════════════════════════════════════════════════════
   REPORT PIE CHARTS
═══════════════════════════════════════════════════════════════ */
const REPORT_LOGS=@json($reportLogsArr);
const PIE_COLORS=['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16','#06b6d4','#a855f7'];
let _pieCharts={};

function renderReportPage(){
  const agg={};
  REPORT_LOGS.forEach(l=>{
    const n=l.driver||'ไม่ระบุ';
    if(!agg[n])agg[n]={price:0,liters:0,hours:0,distance:0,rounds:0};
    agg[n].price+=l.price;agg[n].liters+=l.liters;agg[n].hours+=l.hours;agg[n].distance+=l.distance;agg[n].rounds++;
  });
  const drivers=Object.keys(agg);
  const totPrice=drivers.reduce((s,d)=>s+agg[d].price,0);
  const totLiters=drivers.reduce((s,d)=>s+agg[d].liters,0);
  const totHours=drivers.reduce((s,d)=>s+agg[d].hours,0);
  const totDist=drivers.reduce((s,d)=>s+agg[d].distance,0);
  const totRounds=drivers.reduce((s,d)=>s+agg[d].rounds,0);
  const avgKml=totLiters>0?totDist/totLiters:0;

  document.getElementById('repStatRow').innerHTML=`
    ${repStat('คนขับ',drivers.length,'คน')}
    ${repStat('ค่าน้ำมันรวม','฿'+Math.round(totPrice).toLocaleString(),'บาท')}
    ${repStat('ลิตรรวม',fmtN(totLiters),'ลิตร')}
    ${repStat('ระยะรวม',Math.round(totDist).toLocaleString(),'km')}
    ${repStat('ชั่วโมงรวม',fmtN(totHours),'ชม.')}
    ${repStat('เฉลี่ย km/L',fmtN(avgKml),'km/L')}`;

  _renderPie('pieCost','pieCostLegend',drivers.map(d=>({label:d,value:agg[d].price})),v=>'฿'+Math.round(v).toLocaleString());
  _renderPie('pieLiters','pieLitersLegend',drivers.map(d=>({label:d,value:agg[d].liters})),v=>fmtN(v)+' L');
  _renderPie('pieHours','pieHoursLegend',(function(){const all=drivers.map(d=>({label:d,value:agg[d].hours})).filter(d=>d.value>0).sort((a,b)=>b.value-a.value);if(all.length<=9)return all;const top=all.slice(0,9);const rest=all.slice(8).reduce((s,d)=>s+d.value,0);if(rest>0)top.push({label:'อื่นๆ',value:rest});return top;})(),v=>fmtN(v)+' ชม.');
}
function repStat(label,value,sub){
  return `<div class="report-stat-card"><div class="report-stat-label">${label}</div><div class="report-stat-value">${value}</div><div class="report-stat-sub">${sub}</div></div>`;
}
function _renderPie(canvasId,legendId,data,fmt){
  data=data.filter(d=>d.value>0).sort((a,b)=>b.value-a.value);
  if(_pieCharts[canvasId]){_pieCharts[canvasId].destroy();}
  const ctx=document.getElementById(canvasId);if(!ctx)return;
  if(data.length===0){document.getElementById(legendId).innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>';return;}
  const total=data.reduce((s,d)=>s+d.value,0);
  _pieCharts[canvasId]=new Chart(ctx,{
    type:'doughnut',
    data:{labels:data.map(d=>d.label),datasets:[{data:data.map(d=>d.value),backgroundColor:data.map((_,i)=>PIE_COLORS[i%PIE_COLORS.length]),borderWidth:2,borderColor:'#fff'}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'62%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>{const v=ctx.raw;const pct=total>0?(v/total*100).toFixed(1):0;return `${ctx.label}: ${fmt(v)} (${pct}%)`;}}},datalabels:{display:false}}},
  });
  document.getElementById(legendId).innerHTML=data.map((d,i)=>{
    const pct=total>0?(d.value/total*100).toFixed(1):0;
    return `<div class="pie-legend-item"><span class="pie-legend-dot" style="background:${PIE_COLORS[i%PIE_COLORS.length]}"></span><span class="pie-legend-label">${d.label}</span><span class="pie-legend-val">${fmt(d.value)} · ${pct}%</span></div>`;
  }).join('');
}

/* ═══════════════════════════════════════════════════════════════
   PDF EXPORT
═══════════════════════════════════════════════════════════════ */
const PDF_LOGS=@json($pdfLogsArr);
let pdfMode='range';

function openPdfRangeModal(){
  document.getElementById('pdfModalOverlay')?.classList.add('open');
  const today=new Date().toISOString().slice(0,10);
  const f=document.getElementById('pdfDateFrom'),t=document.getElementById('pdfDateTo');
  if(f&&!f.value)f.value=today; if(t&&!t.value)t.value=today;
}
function closePdfRangeModal(){ document.getElementById('pdfModalOverlay')?.classList.remove('open'); }
function setPdfMode(mode){
  pdfMode=mode;
  document.querySelectorAll('.pdf-mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode));
  document.getElementById('pdfRangeFields').style.display=mode==='range'?'':'none';
  document.getElementById('pdfSingleFields').style.display=mode==='single'?'':'none';
}

async function confirmPdfExport(){
  let from,to,title;
  if(pdfMode==='single'){
    const d=document.getElementById('pdfSingleDate').value; if(!d){alert('เลือกวันที่');return;}
    from=to=d; title='รายงานประจำวันที่ '+_thDate(d);
  }else{
    from=document.getElementById('pdfDateFrom').value;
    to=document.getElementById('pdfDateTo').value;
    if(!from||!to){alert('เลือกช่วงวันที่');return;}
    if(from>to){const tmp=from;from=to;to=tmp;}
    title=(from===to)?('รายงานประจำวันที่ '+_thDate(from)):('รายงานช่วงวันที่ '+_thDate(from)+' – '+_thDate(to));
  }
  closePdfRangeModal();
  await exportPDF(from,to,title);
}
function _thDate(d){const p=(d||'').split('-');if(p.length!==3)return d;return `${p[2]}/${p[1]}/${parseInt(p[0])+543}`;}

async function exportPDF(fromDate,toDate,reportTitle){
  const btn=document.getElementById('pdfExportBtn');
  const orig=btn?btn.innerHTML:'';
  if(btn){btn.disabled=true;btn.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> กำลังสร้าง...';}
  try{
    const rows=PDF_LOGS.filter(l=>{const d=l.date||'';return d>=fromDate&&d<=toDate;});
    if(rows.length===0){alert('ไม่มีข้อมูลในช่วงวันที่นี้');if(btn){btn.disabled=false;btn.innerHTML=orig;}return;}
    const {jsPDF}=window.jspdf;
    const pdf=new jsPDF('l','mm','a4');
    const pageW=297,pageH=210,margin=10,usableW=pageW-margin*2,usableH=pageH-margin*2;

    const stage=document.createElement('div');
    stage.style.cssText='position:fixed;left:-99999px;top:0;background:#fff;font-family:"IBM Plex Sans Thai",sans-serif;padding:0;';
    document.body.appendChild(stage);

    const totPrice=rows.reduce((s,r)=>s+r.price,0);
    const totDist=rows.reduce((s,r)=>s+r.distance,0);
    const totLiters=rows.reduce((s,r)=>s+r.liters,0);
    const avgKml=totLiters>0?totDist/totLiters:0;

    const byDriver={};
    rows.forEach(r=>{
      const n=r.driver||'ไม่ระบุ';
      if(!byDriver[n])byDriver[n]={rows:[],price:0,dist:0,liters:0};
      byDriver[n].rows.push(r);
      byDriver[n].price+=r.price;byDriver[n].dist+=r.distance;byDriver[n].liters+=r.liters;
    });
    const driverNames=Object.keys(byDriver).filter(n=>byDriver[n].price>0||byDriver[n].dist>0).sort((a,b)=>{
      const thbKmA=byDriver[a].dist>0?byDriver[a].price/byDriver[a].dist:0;
      const thbKmB=byDriver[b].dist>0?byDriver[b].price/byDriver[b].dist:0;
      return thbKmB-thbKmA;
    });
    driverNames.forEach(n=>{byDriver[n].rows.sort((a,b)=>(a.date||'').localeCompare(b.date||''));});

    async function renderPage(el,isFirst){
      stage.appendChild(el);
      if(!isFirst)pdf.addPage();
      const canvas=await html2canvas(el,{scale:1.5,backgroundColor:'#fff',logging:false});
      const imgData=canvas.toDataURL('image/jpeg',0.80);
      const imgW=usableW,imgH=canvas.height*imgW/canvas.width;
      pdf.addImage(imgData,'JPEG',margin,margin,imgW,Math.min(imgH,usableH));
      stage.removeChild(el);
    }

    // PAGE 1: Header + stats + rankings
    const p1=document.createElement('div');
    p1.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    const totHours=rows.reduce((s,r)=>s+(r.hours||0),0);
    let h1=`<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #3b82f6;padding-bottom:10px;margin-bottom:14px;">
      <div><div style="font-size:20px;font-weight:700;color:#18181b;">${reportTitle}</div>
      <div style="font-size:11px;color:#71717a;margin-top:2px;">ระบบติดตามน้ำมันรถ · ${CURRENT_USER}</div></div>
      <div style="text-align:right;font-size:10px;color:#a1a1aa;">พิมพ์เมื่อ ${new Date().toLocaleString('th-TH',{timeZone:TZ})}</div>
    </div>`;
    h1+=`<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:7px;margin-bottom:14px;">
      ${_pdfStat('ค่าน้ำมันรวม','฿'+Math.round(totPrice).toLocaleString())}
      ${_pdfStat('ระยะทางรวม',Math.round(totDist).toLocaleString()+' km')}
      ${_pdfStat('น้ำมันรวม',fmtN(totLiters)+' L')}
      ${_pdfStat('เฉลี่ย km/L',fmtN(avgKml))}
      ${_pdfStat('ชั่วโมงรวม',fmtN(totHours)+' ชม.')}
      ${_pdfStat('จำนวน',rows.length+' รายการ · '+driverNames.length+' คน')}
    </div>`;

    const rankPrice=[...driverNames].sort((a,b)=>byDriver[b].price-byDriver[a].price);
    const rankDist=[...driverNames].sort((a,b)=>byDriver[b].dist-byDriver[a].dist);
    const rankHours=[...driverNames].sort((a,b)=>{
      const ha=byDriver[a].rows.reduce((s,r)=>s+(r.hours||0),0);
      const hb=byDriver[b].rows.reduce((s,r)=>s+(r.hours||0),0);
      return hb-ha;
    });
    const rankKml=[...driverNames].filter(n=>byDriver[n].liters>0).sort((a,b)=>(byDriver[b].dist/byDriver[b].liters)-(byDriver[a].dist/byDriver[a].liters));
    const medal=(i)=>i===0?'🥇':i===1?'🥈':i===2?'🥉':'';
    const _rankRow=(arr,valFn)=>arr.map((n,i)=>`<div style="display:flex;align-items:center;gap:6px;padding:4px 0;${i<arr.length-1?'border-bottom:1px solid #f4f4f5;':''}"><span style="font-size:13px;width:20px;text-align:center;">${medal(i)}</span><span style="font-size:11px;font-weight:600;color:#18181b;flex:1;">${n}</span><span style="font-size:11px;font-weight:700;color:#3f3f46;font-family:ui-monospace,monospace;">${valFn(n)}</span></div>`).join('');

    h1+=`<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-bottom:14px;">
      <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#92400e;margin-bottom:6px;">⛽ เติมน้ำมันมากสุด</div>${_rankRow(rankPrice,n=>'฿'+Math.round(byDriver[n].price).toLocaleString())}</div>
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#1e40af;margin-bottom:6px;">🛣️ ขับรถไกลสุด</div>${_rankRow(rankDist,n=>Math.round(byDriver[n].dist).toLocaleString()+' km')}</div>
      <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#166534;margin-bottom:6px;">⏱️ ใช้เวลาทำงานมากสุด</div>${_rankRow(rankHours,n=>{const h=byDriver[n].rows.reduce((s,r)=>s+(r.hours||0),0);return fmtN(h)+' ชม.';})}</div>
      <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#6b21a8;margin-bottom:6px;">📊 ประหยัดน้ำมันสุด (km/L)</div>${_rankRow(rankKml,n=>{const k=byDriver[n].dist/byDriver[n].liters;return fmtN(k)+' km/L';})}</div>
    </div>`;
    p1.innerHTML=h1;
    await renderPage(p1,true);

    // PAGE: summary table
    const pSum=document.createElement('div');
    pSum.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    let hSum=`<div style="font-size:16px;font-weight:700;color:#18181b;margin-bottom:14px;border-bottom:2px solid #3b82f6;padding-bottom:8px;">ค่าน้ำมันแยกตามคนขับ · ${reportTitle}</div>`;
    hSum+=`<table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr style="background:#f4f4f5;">
      <th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;">คนขับ</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ค่าน้ำมัน</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ระยะ km</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ลิตร</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">km/L</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">฿/km</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ชม.</th>
      <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">รายการ</th>
    </tr></thead><tbody>`;
    driverNames.forEach((n,i)=>{
      const d=byDriver[n];const kml=d.liters>0?d.dist/d.liters:0;const thbKm=d.dist>0?d.price/d.dist:0;
      const hrs=d.rows.reduce((s,r)=>s+(r.hours||0),0);
      hSum+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;font-weight:600;">${n}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">฿${Math.round(d.price).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${Math.round(d.dist).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(d.liters)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;color:${kml>=13?'#059669':(kml>0&&kml<9?'#ef4444':'#18181b')};font-weight:600;">${kml>0?fmtN(kml):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(hrs)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${d.rows.length}</td></tr>`;
    });
    hSum+=`</tbody></table>`;
    pSum.innerHTML=hSum;
    await renderPage(pSum,false);

    // Charts pages
    async function renderBigBarChart(titleHTML, subHTML, bars, avgVal, avgLabel, avgColor){
      const page=document.createElement('div');
      page.style.cssText='width:1200px;background:#fff;padding:34px 40px;box-sizing:border-box;';
      const SVG_W=1120,ROW_H=46,TOP=20,LABEL_W=200,BAR_X=LABEL_W+12,BAR_MAX_W=SVG_W-BAR_X-150;
      const maxVal=Math.max(...bars.map(b=>b.value),avgVal||0,1);
      const svgH=Math.max(bars.length*ROW_H+TOP+20,120);
      let svg=`<svg viewBox="0 0 ${SVG_W} ${svgH}" width="100%" xmlns="http://www.w3.org/2000/svg" style="display:block">`;
      if(avgVal>0){const ax=BAR_X+(avgVal/maxVal)*BAR_MAX_W;svg+=`<line x1="${ax}" y1="${TOP-6}" x2="${ax}" y2="${bars.length*ROW_H+TOP}" stroke="${avgColor||'#3b82f6'}" stroke-width="2" stroke-dasharray="7 5"/><text x="${ax+5}" y="${TOP+2}" style="font-size:14px;fill:${avgColor||'#3b82f6'};font-weight:700">${avgLabel||''}</text>`;}
      bars.forEach((b,i)=>{const y=i*ROW_H+TOP+ROW_H/2;const w=(b.value/maxVal)*BAR_MAX_W;svg+=`<text x="0" y="${y+6}" style="font-size:18px;fill:#18181b;font-weight:600;font-family:'IBM Plex Sans Thai',sans-serif">${b.name}</text><rect x="${BAR_X}" y="${y-15}" width="${Math.max(w,1)}" height="30" rx="5" fill="${b.color}"/><text x="${BAR_X+w+10}" y="${y+6}" style="font-size:16px;fill:#3f3f46;font-weight:700;font-family:ui-monospace,monospace">${b.label}</text>`;});
      svg+=`</svg>`;
      page.innerHTML=`<div style="font-size:22px;font-weight:700;color:#18181b;margin-bottom:4px;border-bottom:3px solid #3b82f6;padding-bottom:10px;">${titleHTML}</div><div style="font-size:14px;color:#71717a;margin:6px 0 22px;">${subHTML} · ${reportTitle}</div>${svg}`;
      await renderPage(page,false);
    }

    const costData=driverNames.map(n=>({name:n,cost:(byDriver[n].price>0&&byDriver[n].dist>0)?byDriver[n].price/byDriver[n].dist:0})).filter(d=>d.cost>0).sort((a,b)=>a.cost-b.cost);
    if(costData.length>0){const costAvg=costData.reduce((s,d)=>s+d.cost,0)/costData.length;await renderBigBarChart('ต้นทุนต่อกิโล (฿/km)','ยิ่งน้อยยิ่งดี',costData.map(d=>({name:d.name,value:d.cost,label:'฿'+fmtN(d.cost),color:d.cost<=costAvg*0.85?'#10b981':(d.cost<=costAvg*1.05?'#f59e0b':'#ef4444')})),costAvg,'เฉลี่ย ฿'+fmtN(costAvg),'#3b82f6');}

    const kmlData=driverNames.map(n=>({name:n,kml:(byDriver[n].price>0&&byDriver[n].liters>0)?byDriver[n].dist/byDriver[n].liters:0})).filter(d=>d.kml>0).sort((a,b)=>b.kml-a.kml);
    if(kmlData.length>0){const kmlAvg=kmlData.reduce((s,d)=>s+d.kml,0)/kmlData.length;await renderBigBarChart('น้ำมันต่อกิโล (km/L)','ยิ่งมากยิ่งดี',kmlData.map(d=>({name:d.name,value:d.kml,label:fmtN(d.kml)+' km/L',color:d.kml<kmlAvg*0.9?'#ef4444':(d.kml<kmlAvg?'#f59e0b':'#10b981')})),kmlAvg,'เฉลี่ย '+fmtN(kmlAvg),'#ef4444');}

    // Detail pages per driver
    const ROWS_PER_PAGE=24;
    for(const drvName of driverNames){
      const drvRows=byDriver[drvName].rows;
      const drvPrice=byDriver[drvName].price;
      const drvDist=byDriver[drvName].dist;
      const drvLiters=byDriver[drvName].liters;
      const drvKml=drvLiters>0?drvDist/drvLiters:0;
      const drvThbKm=drvDist>0?drvPrice/drvDist:0;
      const chunks=[];
      for(let i=0;i<drvRows.length;i+=ROWS_PER_PAGE)chunks.push(drvRows.slice(i,i+ROWS_PER_PAGE));

      for(let ci=0;ci<chunks.length;ci++){
        const chunk=chunks[ci];
        const page=document.createElement('div');
        page.style.cssText='width:1200px;background:#fff;padding:28px 36px;box-sizing:border-box;';
        let html=`<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid #3b82f6;padding-bottom:8px;margin-bottom:10px;"><div><div style="font-size:16px;font-weight:700;color:#18181b;">${drvName}</div><div style="font-size:10px;color:#71717a;">${reportTitle}${chunks.length>1?' · ('+(ci+1)+'/'+chunks.length+')':''}</div></div><div style="display:flex;gap:14px;font-size:10px;color:#3f3f46;"><span>ค่าน้ำมันรวม<b style="font-size:13px">฿${Math.round(drvPrice).toLocaleString()}</b></span><span>ระยะทาง<b style="font-size:13px">${Math.round(drvDist).toLocaleString()}</b> km</span><span>เติมน้ำมัน<b style="font-size:13px">${fmtN(drvLiters)}</b> L</span>${drvKml>0?`<span><b style="font-size:13px;color:${drvKml>=13?'#059669':(drvKml<9?'#ef4444':'#18181b')}">${fmtN(drvKml)}</b> km/L</span>`:''}</div></div>`;
        html+=`<table style="width:100%;border-collapse:collapse;font-size:11px;"><thead><tr style="background:#f4f4f5;"><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;">วันที่</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ทะเบียน</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;">เวลา</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ค่าน้ำมัน</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ระยะ</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ลิตร</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">km/L</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">฿/km</th></tr></thead><tbody>`;
        chunk.forEach((r,i)=>{
          const dp=(r.date||'').split('-');const dateText=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'—';
          const thbKm=(r.distance>0&&r.price>0)?(r.price/r.distance):0;
          const startT=(r.start||'').substring(0,5);const endT=(r.end||'').substring(0,5);
          const timeT=(startT&&endT)?startT+'-'+endT:'—';
          html+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${dateText}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${r.plate||'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;font-size:10px;color:#71717a;">${timeT}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.price>0?'฿'+Math.round(r.price).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.distance>0?Math.round(r.distance).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.liters>0?fmtN(r.liters):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;color:${r.kml>=13?'#059669':(r.kml>0&&r.kml<9?'#ef4444':'#18181b')};font-weight:${r.kml>0?'600':'400'}">${r.kml>0?fmtN(r.kml):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td></tr>`;
        });
        html+='</tbody>';
        if(ci===chunks.length-1){
          html+=`<tfoot><tr style="background:#f0f7ff;border-top:3px solid #3b82f6;"><td colspan="3" style="padding:10px 10px;font-weight:700;font-size:14px;color:#1e40af;">รวม ${drvName}</td><td style="padding:10px 10px;text-align:right;font-weight:700;font-size:14px;color:#18181b;">฿${Math.round(drvPrice).toLocaleString()}</td><td style="padding:10px 10px;text-align:right;font-weight:700;font-size:14px;color:#18181b;">${Math.round(drvDist).toLocaleString()} km</td><td style="padding:10px 10px;text-align:right;font-weight:700;font-size:14px;color:#18181b;">${fmtN(drvLiters)} L</td><td style="padding:10px 10px;text-align:right;font-weight:700;font-size:14px;color:${drvKml>=13?'#059669':(drvKml>0&&drvKml<9?'#ef4444':'#18181b')}">${drvKml>0?fmtN(drvKml)+' km/L':'—'}</td><td style="padding:10px 10px;text-align:right;font-weight:700;font-size:14px;color:#1e40af;">${drvThbKm>0?'฿'+fmtN(drvThbKm)+'/km':'—'}</td></tr></tfoot>`;
        }
        html+='</table>';
        page.innerHTML=html;
        await renderPage(page,false);
      }
    }

    document.body.removeChild(stage);
    const fn=(fromDate===toDate)?`รายงานน้ำมัน_${fromDate}.pdf`:`รายงานน้ำมัน_${fromDate}_ถึง_${toDate}.pdf`;
    pdf.save(fn);
  }catch(e){
    console.error('PDF error',e);
    alert('สร้าง PDF ไม่สำเร็จ: '+e.message);
  }finally{
    if(btn){btn.disabled=false;btn.innerHTML=orig;}
  }
}
function _pdfStat(label,value){
  return `<div style="background:#f9fafb;border:1px solid #f0f0f0;border-radius:12px;padding:14px 16px;"><div style="font-size:12px;color:#71717a;margin-bottom:4px;">${label}</div><div style="font-size:19px;font-weight:700;color:#18181b;">${value}</div></div>`;
}

/* ═══════════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',function(){
  updateNavDate();
  setInterval(updateNavDate,30000);

  // Render report data
  renderReportPage();

  // Render charts
  try{ renderDlv(); }catch(e){console.warn('dlv',e);}
  try{ renderKmlChart(); }catch(e){console.warn('kml',e);}
  try{ renderCostChart(); }catch(e){console.warn('cost',e);}

  // Resize handler
  let _rt=null;
  window.addEventListener('resize',()=>{
    clearTimeout(_rt);
    _rt=setTimeout(()=>{
      try{ if(kmlChart)renderKmlChart(); if(costChart)renderCostChart(); if(dlvChart)renderDlv(); }catch(e){}
    },250);
  });
});
</script>

</body>
</html>
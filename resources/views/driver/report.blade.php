{{-- resources/views/driver/oil-report.blade.php --}}
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
*,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
html{overflow-y:auto;}

:root{
  --bg:#fafafa; --bg-card:#ffffff; --bg-subtle:#f4f4f5; --bg-subtle2:#fafafa;
  --separator:rgba(0,0,0,.06); --separator-strong:rgba(0,0,0,.10);
  --text:#18181b; --text2:#3f3f46; --text3:#71717a; --text4:#a1a1aa; --text5:#d4d4d8;
  --blue:#3e6ae1; --blue-hover:#3457b2; --blue-light:#eff6ff;
  --green:#10b981; --green-dark:#059669; --green-light:#d1fae5;
  --orange:#f59e0b; --red:#ef4444; --red-light:#fee2e2;
  --radius:10px;
  --font-thai:'IBM Plex Sans Thai','Inter',-apple-system,sans-serif;
  --font-mono:ui-monospace,'SF Mono',Menlo,monospace;
}

.tesla-topnav { background-color: #ffffff; border-bottom: 1px solid #eaeaea; position: sticky; top: 0; z-index: 1000; width: 100%; }
.tesla-topnav-container { max-width: 100%; margin: 0 auto; padding: 0 20px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
.tesla-brand { display: flex; align-items: center; gap: 10px; }
.tesla-logo { font-size: 20px; }
.tesla-title { font-weight: 600; font-size: 16px; color: #171a20; }
.tesla-right { display: flex; align-items: center; gap: 16px; }
.tesla-user-badge { display: flex; align-items: center; font-size: 14px; color: #4b5563; font-weight: 400; white-space: nowrap; }
.tesla-actions { display: flex; align-items: center; gap: 8px; }
.tesla-btn { height: 36px; padding: 0 18px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-sizing: border-box; font-size: clamp(11px, 0.55vw + 5px, 14px) !important; border-radius: 6px !important; transition: all 0.2s ease; white-space: nowrap; line-height: 1; }
.tesla-btn-neutral { background-color: #3e6ae1; color: #fff; border: 1px solid #3e6ae1; }
.tesla-btn-neutral:hover { background-color: #3457b2; border-color: #3457b2; color: #fff; text-decoration: none; }
.tesla-btn svg { color: #ffffff; flex-shrink: 0; }

.topnav-filters { display: flex; align-items: center; gap: 16px; padding: 12px 20px; background-color: #ffffff; border-bottom: 1px solid #eaeaea; flex-wrap: wrap; width: 100%; box-sizing: border-box; }
.filter-group { display: flex; align-items: center; gap: 8px; position: relative; }
.filter-group-label { font-weight: 500; color: #4b5563; font-size: clamp(11px, 0.55vw + 5px, 14px); white-space: nowrap; }
.segmented { display: inline-flex; background: #f1f3f5; padding: 3px; border-radius: 8px; gap: 2px; border: 1px solid #eaeaea; }
.seg-btn { background: transparent; border: none; height: 30px; padding: 0 12px; font-size: clamp(11px, 0.55vw + 5px, 14px); font-weight: 500; color: #4b5563; border-radius: 6px !important; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; }
.seg-btn:hover { color: #111827; }
.seg-btn.active { background: #3e6ae1; color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.tesla-select, .pill-date { display: inline-flex; align-items: center; height: 36px; padding: 0 14px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 6px !important; font-size: clamp(11px, 0.55vw + 5px, 14px); font-weight: 500; color: #374151; cursor: pointer; outline: none; transition: all 0.2s ease; box-sizing: border-box; }
.tesla-select { min-width: 150px; }
.pill-date { min-width: 140px; }
.tesla-select:hover, .tesla-select:focus, .pill-date:hover, .pill-date:focus { border-color: #3e6ae1; }

.main { max-width: 1600px; margin: 24px auto; padding: 0 28px; box-sizing: border-box; }
.report-header { display: flex; align-items: center; justify-content: space-between; padding: 24px 28px; margin-bottom: 24px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; border-radius: var(--radius); box-shadow: 0 1px 3px rgba(0,0,0,0.1); flex-wrap: wrap; gap: 16px; }
.report-title { font-size: 22px; font-weight: 700; letter-spacing: -0.02em; }
.report-sub { font-size: 14px; opacity: .9; margin-top: 4px; }
.report-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.report-back { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.25); padding: 8px 16px; border-radius: 6px; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .15s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.report-back:hover { background: rgba(255,255,255,.25); }
.report-export-btn { background: rgba(255,255,255,.2); color: #fff; border: 1px solid rgba(255,255,255,.3); padding: 8px 16px; border-radius: 6px; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .15s ease; display: inline-flex; align-items: center; gap: 6px; }
.report-export-btn:hover { background: rgba(255,255,255,.3); transform: translateY(-1px); }
.report-export-btn:disabled { opacity: .5; cursor: wait; transform: none; }
.report-export-btn svg { flex-shrink: 0; }

.report-stat-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 24px; }
.report-stat-card { background: var(--bg-card); border-radius: var(--radius); padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--separator); transition: transform .15s ease, box-shadow .15s ease; }
.report-stat-card:hover { transform: translateY(-2px); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.report-stat-label { font-size: 13px; color: var(--text3); font-weight: 600; margin-bottom: 8px; }
.report-stat-value { font-size: 24px; font-weight: 700; color: var(--text); letter-spacing: -0.02em; font-family: var(--font-mono); }
.report-stat-sub { font-size: 13px; color: var(--text4); margin-top: 4px; }

.report-pie-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
.pie-card { background: var(--bg-card); border-radius: var(--radius); padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--separator); }
.pie-card-title { font-size: 15px; font-weight: 700; color: var(--text); letter-spacing: -0.01em; }
.pie-card-sub { font-size: 13px; color: var(--text3); margin: 4px 0 16px; }
.pie-canvas-wrap { height: 240px; position: relative; }
.pie-legend { margin-top: 16px; display: flex; flex-direction: column; gap: 8px; }
.pie-legend-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.pie-legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.pie-legend-label { flex: 1; color: var(--text2); font-weight: 500; }
.pie-legend-val { color: var(--text3); font-size: 13px; font-family: var(--font-mono); }

.charts-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 28px; }
.chart-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--separator); padding: clamp(16px, 2vw, 24px); min-width: 0; overflow: hidden; }
.chart-head { display: flex; align-items: baseline; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.chart-title { font-size: clamp(15px, 1.5vw, 17px); font-weight: 700; color: var(--text); letter-spacing: -0.02em; }
.chart-sub { font-size: 13px; color: var(--text3); font-weight: 400; }
.chart-canvas { position: relative; width: 100%; min-width: 0; }
.chart-inner { height: 100%; width: 100%; min-width: 0; }
.chart-legend { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--separator); }
.chart-legend-item { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text2); font-weight: 500; }
.chart-legend-dot { width: 10px; height: 10px; border-radius: 3px; }
.vehicle-toggle { margin-left: auto; display: inline-flex; background: var(--bg-subtle); border-radius: 6px; padding: 3px; border: 1px solid var(--separator); flex-shrink: 0; }
.vt-btn { padding: 6px 12px; border: none; background: transparent; color: var(--text2); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border-radius: 4px; white-space: nowrap; letter-spacing: -0.01em; transition: all .15s ease; }
.vt-btn:hover { color: var(--text); }
.vt-btn.active { background: #fff; color: var(--blue); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 1px rgba(0,0,0,0.04); border: 1px solid var(--separator); }

.rank-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--separator); overflow: hidden; margin-bottom: 28px; }
.rank-card-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--separator); gap: 16px; }
.rank-card-title { font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: -0.02em; display: flex; align-items: center; gap: 10px; }
.card-count { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 22px; padding: 0 8px; background: var(--blue-light); color: var(--blue); border-radius: 100px; font-size: 13px; font-weight: 700; font-family: var(--font-mono); }
.sort-toggle { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.sort-label { font-size: 13px; color: var(--text3); font-weight: 600; white-space: nowrap; }
.sort-segmented { display: inline-flex; background: var(--bg-subtle); border-radius: 8px; padding: 3px; border: 1px solid var(--separator); }
.sort-btn { padding: 5px 12px; border: none; background: transparent; color: var(--text3); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border-radius: 6px; white-space: nowrap; letter-spacing: -0.01em; transition: all .15s ease; }
.sort-btn:hover { color: var(--text); }
.sort-btn.active { background: #fff; color: var(--blue); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 1px rgba(0,0,0,0.04); border: 1px solid var(--separator); }
.driver-list { display: flex; flex-direction: column; max-height: 650px; overflow-y: auto; }
.driver-list::-webkit-scrollbar { width: 6px; }
.driver-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.driver-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.driver-list::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.driver-row { display: flex; align-items: center; padding: 18px 28px; border-bottom: 1px solid #f3f4f6; }
.driver-row:hover { background: #f9fafb; }
.driver-row:last-child { border-bottom: none; }
.driver-rank { width: 34px; font-weight: 700; color: #9ca3af; font-size: 14px; }
.driver-row .body { flex: 1; }
.driver-row .name { font-weight: 700; font-size: 14px; color: var(--text); }
.driver-row .stats { font-size: 12px; color: var(--text3); margin-top: 4px; display: flex; gap: 8px; }
.driver-row .right { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.driver-row .price { font-weight: 700; font-size: 16px; color: var(--text); }
.driver-row .kml { font-size: 12px; font-weight: 600; }
.driver-row .kml.warn { color: var(--red); }
.driver-row .thb-km { font-size: 11px; color: var(--text3); }
.empty-state { text-align: center; padding: 50px 20px; color: var(--text4); }
.empty-state .icon { font-size: 36px; margin-bottom: 10px; opacity: 0.5; }

.full-table-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--separator); overflow: hidden; margin-bottom: 28px; }
.full-table-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--separator); gap: 16px; }
.full-table-title { font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: -0.02em; display: flex; align-items: center; gap: 10px; }
.search-pill { position: relative; display: flex; align-items: center; }
.search-pill .si { position: absolute; left: 12px; color: var(--text4); pointer-events: none; }
.search-pill input { height: 38px; padding: 0 14px 0 36px; border: 1px solid var(--separator-strong); border-radius: 6px; font-size: 13px; outline: none; width: 220px; background: var(--bg-subtle); color: var(--text); transition: all .15s ease; }
.search-pill input:focus { outline: none; background: #fff; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.fuel-table-scroll { overflow-x: auto; overflow-y: auto; max-height: 650px; width: 100%; }
.fuel-table-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
.fuel-table-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.fuel-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.fuel-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.fuel-table { width: 100%; min-width: 0; border-collapse: collapse; font-size: 13px; table-layout: auto; }
.fuel-table thead { position: sticky; top: 0; z-index: 1; background: #fff; }
.fuel-table thead th { padding: 14px 16px; background: var(--bg-subtle2); font-size: 13px; color: var(--text3); font-weight: 600; text-align: left; border-bottom: 2px solid var(--separator); letter-spacing: -0.01em; white-space: nowrap; }
.fuel-table thead th:first-child { padding-left: 20px; }
.fuel-table thead th:last-child { padding-right: 20px; }
.fuel-table thead th.num { text-align: right; }
.fuel-table tbody td { padding: 14px 16px; border-bottom: 1px solid var(--separator); vertical-align: middle; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text2); }
.fuel-table tbody td:first-child { padding-left: 20px; }
.fuel-table tbody td:last-child { padding-right: 20px; }
.fuel-table tbody tr { transition: background .12s; }
.fuel-table tbody tr:hover { background: var(--bg-subtle); }
.fuel-table tbody tr:last-child td { border-bottom: none; }
.fuel-table .num { text-align: right; font-variant-numeric: tabular-nums; font-family: var(--font-mono); font-size: 13px; }
.row-idx { color: var(--text4); font-weight: 500; font-size: 13px; font-family: var(--font-mono); }
.driver-cell { min-width: 0; overflow: hidden; }
.driver-name { font-weight: 600; font-size: 14px; color: var(--text); letter-spacing: -0.01em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.driver-plate { font-size: 13px; color: var(--text3); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.date-pill { display: inline-block; padding: 3px 10px; background: var(--blue-light); color: var(--blue); border-radius: 100px; font-size: 13px; font-weight: 600; font-family: var(--font-mono); white-space: nowrap; letter-spacing: -0.01em; }
.time-pill { display: inline-block; padding: 3px 10px; background: var(--bg-subtle); color: var(--text2); border-radius: 100px; font-size: 13px; font-weight: 600; font-family: var(--font-mono); white-space: nowrap; }
.hour-pill { display: inline-block; padding: 3px 11px; background: rgba(245,158,11,.15); color: #b45309; border-radius: 100px; font-size: 13px; font-weight: 600; font-family: var(--font-mono); white-space: nowrap; letter-spacing: -0.01em; }
.carry-hint { font-size: 13px; color: var(--orange); font-weight: 500; margin-top: 3px; white-space: nowrap; font-family: var(--font-mono); }
.km-good { color: var(--green-dark); font-weight: 600; }
.km-mid { color: var(--text); font-weight: 500; }
.km-bad { color: var(--red); font-weight: 700; }
.thb-km-val { color: var(--text2); font-weight: 600; }

.pdf-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.4); backdrop-filter: blur(2px); z-index: 9999; display: none; align-items: center; justify-content: center; }
.pdf-modal-overlay.open { display: flex; }
.pdf-modal { background: #fff; width: 100%; max-width: 420px; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden; }
.pdf-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid var(--separator); font-weight: 700; font-size: 16px; }
.pdf-modal-x { background: none; border: none; font-size: 20px; color: var(--text3); cursor: pointer; padding: 4px; }
.pdf-modal-body { padding: 24px; }
.pdf-mode-tabs { display: flex; background: var(--bg-subtle); padding: 3px; border-radius: 8px; margin-bottom: 22px; }
.pdf-mode-btn { flex: 1; background: transparent; border: none; padding: 9px; font-size: 13px; font-weight: 500; color: var(--text2); border-radius: 6px; cursor: pointer; }
.pdf-mode-btn.active { background: #fff; color: var(--text); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.pdf-field { margin-bottom: 18px; }
.pdf-field label { display: block; font-size: 12px; font-weight: 600; color: var(--text2); margin-bottom: 8px; }
.pdf-field input[type="date"] { width: 100%; height: 40px; padding: 0 14px; border: 1px solid var(--separator-strong); border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; }
.pdf-field input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.pdf-modal-foot { display: flex; justify-content: flex-end; gap: 12px; padding: 18px 24px; background: var(--bg-subtle); border-top: 1px solid var(--separator); }
.pdf-btn-cancel { background: #fff; border: 1px solid var(--separator-strong); color: var(--text2); padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; }
.pdf-btn-cancel:hover { background: var(--bg-subtle); }
.pdf-btn-go { background: var(--blue); border: 1px solid var(--blue); color: #fff; padding: 9px 22px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.pdf-btn-go:hover { background: var(--blue-hover); }

@media (max-width: 1280px) { .report-stat-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 1024px) { .report-pie-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 900px) {
  .tesla-topnav-container { padding: 0 16px; gap: 12px; }
  .tesla-brand .tesla-title { display: none; }
  .main { padding: 20px 16px; }
  .report-stat-row { grid-template-columns: repeat(2, 1fr); }
  .report-pie-grid { grid-template-columns: 1fr; }
  .search-pill input { width: 160px; }
}
@media (max-width: 640px) {
  .report-stat-row { grid-template-columns: repeat(2, 1fr); }
  .chart-card { padding: 16px; }
  .topnav-filters { gap: 12px; padding: 12px 16px; height: auto; flex-wrap: nowrap; }
  .tesla-select, .pill-date { min-width: auto; }
  .fuel-table colgroup { display: none; }
  .fuel-table thead { display: none; }
  .fuel-table, .fuel-table tbody, .fuel-table tr, .fuel-table td { display: block; width: 100%; }
  .fuel-table tbody td:nth-child(n) { display: flex !important; }
  .fuel-table tr { background: var(--bg-card); border: 1px solid var(--separator); border-radius: 14px; margin-bottom: 12px; padding: 14px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
  .fuel-table tr:hover { background: var(--bg-card); }
  .fuel-table td { display: flex !important; justify-content: space-between; align-items: center; padding: 6px 0 !important; border: none !important; text-align: right; font-size: 13px; }
  .fuel-table td::before { content: attr(data-label); font-weight: 600; color: var(--text3); font-size: 13px; margin-right: 14px; text-align: left; font-family: var(--font-thai); }
  .fuel-table td.row-idx { display: none !important; }
  .fuel-table td[data-label="คนขับ"] { border-bottom: 1px solid var(--separator) !important; padding-bottom: 12px !important; margin-bottom: 6px; }
  .fuel-table td[data-label="คนขับ"]::before { display: none; }
  .fuel-table td[data-label="คนขับ"] .driver-cell { text-align: left; width: 100%; }
  .fuel-table td[data-label="วันที่"] { justify-content: flex-start; }
  .fuel-table .driver-name { font-size: 15px; }
}
</style>
</head>
<body>

@php
  $currentUser = $creator ?? 'Guest';
  $userQuery = '';
  $allowedDrivers = ['บังเดช','กอลฟ์','เก่ง','หรั่ง','เอ้','แซม','เอ','แฟงค์','yuth','แมน','กบ','joey'];

  $logsCollection = collect($logs ?? []);
  $allLogsCollection = collect($allLogs ?? []);
  $platesArray = is_array($plates ?? null) ? $plates : [];
  $deliveryByDriverData = $deliveryByDriver ?? [];
  $kmlByDriverData = $kmlByDriver ?? [];
  $costByDriver2Data = $costByDriver2 ?? [];
  $reportLogsArrData = $reportLogsArr ?? [];
  $pdfLogsArrData = $pdfLogsArr ?? [];

  if($logsCollection->isEmpty()){
    $mockLogs = [
      ['id'=>1,'driver_name'=>'บังเดช','vehicle_id'=>'1 ฉผ 1276','work_date'=>'2026-07-25','start_time'=>'08:00:00','end_time'=>'18:00:00','total_price'=>2500,'total_distance'=>250,'liters'=>25,'km_per_liter'=>10.0,'work_hours'=>10,'delivery_success'=>5,'delivery_fail'=>1],
      ['id'=>2,'driver_name'=>'กอลฟ์','vehicle_id'=>'1 ฉผ 3181','work_date'=>'2026-07-25','start_time'=>'09:00:00','end_time'=>'19:00:00','total_price'=>1800,'total_distance'=>180,'liters'=>15,'km_per_liter'=>12.0,'work_hours'=>10,'delivery_success'=>4,'delivery_fail'=>0],
      ['id'=>3,'driver_name'=>'เก่ง','vehicle_id'=>'2 ฉธ 1620','work_date'=>'2026-07-26','start_time'=>'07:30:00','end_time'=>'17:30:00','total_price'=>3200,'total_distance'=>320,'liters'=>30,'km_per_liter'=>10.67,'work_hours'=>10,'delivery_success'=>6,'delivery_fail'=>2],
      ['id'=>4,'driver_name'=>'หรั่ง','vehicle_id'=>'มอ.1234','work_date'=>'2026-07-26','start_time'=>'08:30:00','end_time'=>'18:30:00','total_price'=>800,'total_distance'=>120,'liters'=>4,'km_per_liter'=>30.0,'work_hours'=>10,'delivery_success'=>3,'delivery_fail'=>1],
      ['id'=>5,'driver_name'=>'เอ้','vehicle_id'=>'3ฉมก6071','work_date'=>'2026-07-27','start_time'=>'08:00:00','end_time'=>'18:00:00','total_price'=>2800,'total_distance'=>280,'liters'=>28,'km_per_liter'=>10.0,'work_hours'=>10,'delivery_success'=>5,'delivery_fail'=>0],
      ['id'=>6,'driver_name'=>'แซม','vehicle_id'=>'มอ.5678','work_date'=>'2026-07-27','start_time'=>'09:00:00','end_time'=>'19:00:00','total_price'=>600,'total_distance'=>90,'liters'=>3,'km_per_liter'=>30.0,'work_hours'=>10,'delivery_success'=>2,'delivery_fail'=>1],
    ];
    $logsCollection = collect($mockLogs);
    $allLogsCollection = collect($mockLogs);
  }

  if(empty($platesArray)){
    $platesArray = $allLogsCollection->pluck('vehicle_id')->filter()->unique()->values()->toArray();
  }

  $uniqueDrivers = [];
  foreach($logsCollection as $r){
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

  if(empty($deliveryByDriverData)){
    foreach($logsCollection as $log){
      $driver = $log['driver_name'] ?? 'ไม่ระบุ';
      if(!isset($deliveryByDriverData[$driver])) $deliveryByDriverData[$driver] = ['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];
      $deliveryByDriverData[$driver]['success'] += (int)($log['delivery_success'] ?? $log['success_count'] ?? $log['ok_count'] ?? 0);
      $deliveryByDriverData[$driver]['fail'] += (int)($log['delivery_fail'] ?? $log['fail_count'] ?? $log['ng_count'] ?? 0);
    }
  }

  if(empty($kmlByDriverData)){
    foreach($logsCollection as $log){
      $plate = $log['vehicle_id'] ?? 'ไม่ระบุ';
      $driver = $log['driver_name'] ?? '';
      $key = $plate.'|'.$driver;
      $kml = (float)($log['km_per_liter'] ?? 0);
      if($kml <= 0) continue;
      if(!isset($kmlByDriverData[$key])) $kmlByDriverData[$key] = ['sum'=>0,'count'=>0,'plate'=>$plate,'driver'=>$driver];
      $kmlByDriverData[$key]['sum'] += $kml;
      $kmlByDriverData[$key]['count']++;
    }
  }

  if(empty($costByDriver2Data)){
    foreach($logsCollection as $log){
      $plate = $log['vehicle_id'] ?? 'ไม่ระบุ';
      $driver = $log['driver_name'] ?? '';
      $key = $plate.'|'.$driver;
      if(!isset($costByDriver2Data[$key])) $costByDriver2Data[$key] = ['price'=>0,'dist'=>0,'plate'=>$plate,'driver'=>$driver];
      $costByDriver2Data[$key]['price'] += (float)($log['total_price'] ?? 0);
      $costByDriver2Data[$key]['dist'] += (float)($log['total_distance'] ?? 0);
    }
  }

  if(empty($reportLogsArrData)){
    $reportLogsArrData = $logsCollection->map(function($l){
      return ['driver'=>$l['driver_name']??'','price'=>(float)($l['total_price']??0),'liters'=>(float)($l['liters']??0),'hours'=>(float)($l['work_hours']??0),'distance'=>(float)($l['total_distance']??0)];
    })->values()->toArray();
  }

  if(empty($pdfLogsArrData)){
    $pdfLogsArrData = $allLogsCollection->map(function($l){
      return ['driver'=>$l['driver_name']??'','plate'=>$l['vehicle_id']??'','date'=>$l['work_date']??'','start'=>$l['start_time']??'','end'=>$l['end_time']??'','price'=>(float)($l['total_price']??0),'distance'=>(float)($l['total_distance']??0),'liters'=>(float)($l['liters']??0),'kml'=>(float)($l['km_per_liter']??0),'hours'=>(float)($l['work_hours']??0)];
    })->values()->toArray();
  }
@endphp

<nav class="tesla-topnav">
  <div class="tesla-topnav-container">
    <div class="tesla-brand">
      <div class="tesla-logo">⛽</div>
      <span class="tesla-title">สรุปรายงานน้ำมัน</span>
    </div>
    <div class="tesla-right">
      <div class="tesla-user-badge" title="ผู้ใช้ปัจจุบัน">👤 ผู้ใช้: {{ $currentUser }}</div>
      <div class="tesla-actions">
        {{-- ✅ แก้แล้ว: ปุ่มนี้ลิงก์ไปหน้า oil.blade.php และเปลี่ยนชื่อเป็น "ติดตามน้ำมัน" --}}
        <a href="{{ url('/oil').$userQuery }}" class="tesla-btn tesla-btn-neutral">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          ติดตามน้ำมัน
        </a>
        <a href="{{ url('/service').$userQuery }}" class="tesla-btn tesla-btn-neutral">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          Service
        </a>
        <a href="http://server_update:8000/solist{{ $userQuery }}" class="tesla-btn tesla-btn-neutral">หน้าหลัก</a>
      </div>
    </div>
  </div>
</nav>

<div class="topnav-filters">
    <div class="filter-group">
      <span class="filter-group-label">มุมมอง</span>
      <div class="segmented">
        @php $currentView = $view ?? 'all'; @endphp
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="button" class="seg-btn {{ $currentView===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
        @endforeach
      </div>
    </div>

    @if($currentView==='year')
    <div class="filter-group">
      <span class="filter-group-label">ปี</span>
      <select class="tesla-select" id="yearPicker" onchange="submitFilter()">
        @php $savedYear = request('year', date('Y')); $yearMax = max((int)date('Y') + 2, (int)$savedYear); @endphp
        @for($y=$yearMax;$y>=2020;$y--)
        <option value="{{ $y }}" {{ $savedYear==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    @elseif($currentView==='month')
    <div class="filter-group">
      <span class="filter-group-label">เดือน</span>
      <input type="month" class="pill-date" id="monthPicker" value="{{ $filterMonth ?? date('Y-m') }}" onchange="submitFilter()">
    </div>
    @elseif($currentView==='day')
    <div class="filter-group">
      <span class="filter-group-label">วันที่</span>
      <input type="date" class="pill-date" id="datePicker" value="{{ $filterDay ?? date('Y-m-d') }}" onchange="submitFilter()">
    </div>
    @endif

    <div class="filter-group">
      <span class="filter-group-label">คนขับ</span>
      <select class="tesla-select" id="driverPicker" onchange="submitFilter()">
        <option value="all" {{ ($filterDriver ?? 'all')==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @php
          $normDrv = function($s){
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
          };
          $drvHasData = [];
          foreach($allLogsCollection as $r){
            $nm = trim((string)($r['driver_name'] ?? ''));
            if($nm === '') continue;
            $price = (float)($r['total_price'] ?? 0);
            $dist  = (float)($r['total_distance'] ?? 0);
            if($price > 0 || $dist > 0) $drvHasData[$normDrv($nm)] = true;
          }
          $drvSource = $allLogsCollection->pluck('driver_name')->map(fn($n)=>trim((string)$n))
            ->filter(fn($n)=> $n !== '' && isset($drvHasData[$normDrv($n)]))
            ->unique()->values()->all();
          $seenDrv = [];
        @endphp
        @foreach($drvSource as $d)
          @php $nd = $normDrv($d); @endphp
          @if(!in_array($nd, $seenDrv, true))
            @php $seenDrv[] = $nd; @endphp
            <option value="{{ trim($d) }}" {{ trim($filterDriver ?? '')===trim($d)?'selected':'' }}>{{ trim($d) }}</option>
          @endif
        @endforeach
      </select>
    </div>

    <div class="filter-group">
      <span class="filter-group-label">ทะเบียน</span>
      <select class="tesla-select" id="platePicker" onchange="submitFilter()">
        <option value="all" {{ ($filterPlate ?? 'all')==='all'?'selected':'' }}>ทะเบียนทั้งหมด</option>
        @foreach($platesArray as $p)
        <option value="{{ $p }}" {{ ($filterPlate ?? 'all')===$p?'selected':'' }}>{{ $p }}</option>
        @endforeach
      </select>
    </div>
</div>

<main class="main">
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

  <div class="report-stat-row" id="repStatRow"></div>

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

  <div class="rank-card">
    <div class="rank-card-head">
      <div class="rank-card-title">อันดับคนขับ <span class="card-count">{{ count($uniqueDrivers) }}</span></div>
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

  <div class="full-table-card">
    <div class="full-table-head">
      <div class="full-table-title">รายการเติมน้ำมันทั้งหมด <span class="card-count" id="oilCount">{{ $logsCollection->count() }}</span></div>
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
        @forelse($logsCollection as $r)
        @php
          $rowNo++;
          $rid = (int)($r['id'] ?? 0);
          $effDist = (float)($r['total_distance'] ?? 0);
          $kml = (float)($r['km_per_liter'] ?? 0);
          $rawDist = $effDist;
          $carryAmt = 0;
          
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
          <td class="num" data-label="ลิตร">{{ isset($r['liters']) && $r['liters'] !== '' ? rtrim(rtrim(number_format($r['liters'],2,'.',''),'0'),'.') : '—' }}</td>
          <td class="num" data-label="ค่าน้ำมัน">{{ isset($r['total_price']) && $r['total_price'] !== '' ? '฿'.number_format($r['total_price']) : '—' }}</td>
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
        <div class="pdf-field" style="margin-bottom:14px"><label>ตั้งแต่วันที่</label><input type="date" id="pdfDateFrom"></div>
        <div class="pdf-field"><label>ถึงวันที่</label><input type="date" id="pdfDateTo"></div>
      </div>
      <div id="pdfSingleFields" style="display:none">
        <div class="pdf-field"><label>เลือกวันที่</label><input type="date" id="pdfSingleDate" value="{{ date('Y-m-d') }}"></div>
      </div>
    </div>
    <div class="pdf-modal-foot">
      <button type="button" class="pdf-btn-cancel" onclick="closePdfRangeModal()">ยกเลิก</button>
      <button type="button" class="pdf-btn-go" onclick="confirmPdfExport()">📄 สร้าง PDF</button>
    </div>
  </div>
</div>

<script>
const ROUTE_FILTER  = '{{ route("oil.filter") ?? "#" }}';
const CURRENT_USER  = @json($currentUser);
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const TZ            = 'Asia/Bangkok';
const MAIN_VIEW     = @json($currentView ?? 'all');
const ALLOWED_DRIVERS = @json($allowedDrivers);

const DLV_BY_DRIVER = @json($deliveryByDriverData);
const KML_BY_DRIVER = @json($kmlByDriverData);
const COST_BY_DRIVER = @json($costByDriver2Data);
const REPORT_LOGS = @json($reportLogsArrData);
const PDF_LOGS = @json($pdfLogsArrData);

function fmtN(v, max=2){ return (+(+v).toFixed(max)).toString(); }

function submitFilterForm(params){
  const form = document.createElement('form');
  form.method='POST'; form.action=ROUTE_FILTER; form.style.display='none';
  const add=(n,v)=>{ if(v==null||v==='')return; const i=document.createElement('input'); i.type='hidden'; i.name=n; i.value=v; form.appendChild(i); };
  add('_token',CSRF_TOKEN);
  add('redirect_to','report');
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

function _normalizeName(s){ if(!s) return ''; return String(s).replace(/[\u200B-\u200D\uFEFF]/g,'').replace(/\s+/g,' ').trim().toLowerCase(); }
const DRIVER_ALIASES = {'กอลฟ':'กอลฟ','กอลฟ์':'กอลฟ','แฟงค':'แฟงค','แฟรงค':'แฟงค','yuth':'yuth','ยุทร':'yuth','ยุท':'yuth','joey':'joey','โจอี':'joey','แซม':'แซม','แชม':'แซม'};
function _normalizeDriver(s){ let n = _normalizeName(s).replace(/\u0E4C/g,''); return DRIVER_ALIASES[n] || n; }
const _allowedSet = new Set(ALLOWED_DRIVERS.map(_normalizeDriver));
function isAllowedDriver(name){ return _allowedSet.has(_normalizeDriver(name)); }

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

let dlvChart=null;
function renderDlv(){
  const drivers=Object.keys(DLV_BY_DRIVER).filter(d=>isAllowedDriver(d));
  if(drivers.length===0){ 
    if(dlvChart)dlvChart.destroy(); 
    document.getElementById('dlvLegend').innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>'; 
    return; 
  }
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
  document.getElementById('dlvLegend').innerHTML=`<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ส่งสำเร็จ</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ส่งไม่สำเร็จ</div>`;
}

const VEHICLE_TYPE={kml:'car', cost:'car'};
function isMoto(plate){ 
  const p=(plate||'').trim(); 
  return p.startsWith('มอ.')||p.startsWith('มอ ')||p.toLowerCase().startsWith('moto'); 
}
function switchVehicleType(chart, type){
  VEHICLE_TYPE[chart]=type;
  document.querySelectorAll(`.vehicle-toggle[data-chart="${chart}"] .vt-btn`).forEach(b=>b.classList.toggle('active', b.dataset.type===type));
  if(chart==='kml') renderKmlChart(); else if(chart==='cost') renderCostChart();
}

let kmlChart=null;
function renderKmlChart(){
  const vType=VEHICLE_TYPE.kml||'car';
  console.log('🔍 KML Chart - Vehicle Type:', vType);
  console.log(' KML_BY_DRIVER:', KML_BY_DRIVER);
  
  const drivers=Object.keys(KML_BY_DRIVER).map(key=>({
    name:KML_BY_DRIVER[key].driver||'',
    plate:KML_BY_DRIVER[key].plate||key,
    avg:KML_BY_DRIVER[key].count>0?KML_BY_DRIVER[key].sum/KML_BY_DRIVER[key].count:0
  })).filter(d=>d.avg>0);
  
  console.log('🔍 All drivers before filter:', drivers);
  
  const filtered = drivers.filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate));
  console.log('🔍 After vehicle filter:', filtered);
  
  if(filtered.length===0){ 
    if(kmlChart)kmlChart.destroy(); 
    document.getElementById('kmlLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${vType==='moto'?'มอเตอร์ไซค์':'รถยนต์'}</span>`;
    console.warn('⚠️ ไม่มีข้อมูลสำหรับ KML Chart');
    return; 
  }
  
  const sorted = filtered.sort((a,b)=>b.avg-a.avg);
  const inner=document.getElementById('chartKmlInner');
  if(inner){ inner.style.width='100%'; inner.style.height=Math.max(sorted.length*44+40,300)+'px'; }
  const labels=sorted.map(d=>[d.plate||d.name, d.name && d.plate ? d.name : '']);
  const data=sorted.map(d=>d.avg);
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
  document.getElementById('kmlLegend').innerHTML=`<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (≥ เฉลี่ย)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (ใกล้เฉลี่ย)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ผิดปกติ (ต่ำกว่าเฉลี่ย 10%)</div><div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">${fmtN(overallAvg)} km/L</strong></div>`;
}

let costChart=null;
function renderCostChart(){
  const vType=VEHICLE_TYPE.cost||'car';
  const drivers=Object.keys(COST_BY_DRIVER).map(key=>({name:COST_BY_DRIVER[key].driver||'',plate:COST_BY_DRIVER[key].plate||key,cost:COST_BY_DRIVER[key].dist>0?COST_BY_DRIVER[key].price/COST_BY_DRIVER[key].dist:0,price:COST_BY_DRIVER[key].price,dist:COST_BY_DRIVER[key].dist})).filter(d=>d.cost>0).filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate)).sort((a,b)=>a.cost-b.cost);
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
  document.getElementById('costLegend').innerHTML=`<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (ต่ำกว่าเฉลี่ย ≥15%)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (±5–15%)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>สูง (สูงกว่าเฉลี่ย >5%)</div><div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">฿${fmtN(avg)}/km</strong></div>`;
}

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
function repStat(label,value,sub){ return `<div class="report-stat-card"><div class="report-stat-label">${label}</div><div class="report-stat-value">${value}</div><div class="report-stat-sub">${sub}</div></div>`; }
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

let pdfMode='range';
function openPdfRangeModal(){ document.getElementById('pdfModalOverlay')?.classList.add('open'); const today=new Date().toISOString().slice(0,10); const f=document.getElementById('pdfDateFrom'),t=document.getElementById('pdfDateTo'); if(f&&!f.value)f.value=today; if(t&&!t.value)t.value=today; }
function closePdfRangeModal(){ document.getElementById('pdfModalOverlay')?.classList.remove('open'); }
function setPdfMode(mode){ pdfMode=mode; document.querySelectorAll('.pdf-mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode)); document.getElementById('pdfRangeFields').style.display=mode==='range'?'':'none'; document.getElementById('pdfSingleFields').style.display=mode==='single'?'':'none'; }
async function confirmPdfExport(){
  let from,to,title;
  if(pdfMode==='single'){ const d=document.getElementById('pdfSingleDate').value; if(!d){alert('เลือกวันที่');return;} from=to=d; title='รายงานประจำวันที่ '+_thDate(d); }
  else{ from=document.getElementById('pdfDateFrom').value; to=document.getElementById('pdfDateTo').value; if(!from||!to){alert('เลือกช่วงวันที่');return;} if(from>to){const tmp=from;from=to;to=tmp;} title=(from===to)?('รายงานประจำวันที่ '+_thDate(from)):('รายงานช่วงวันที่ '+_thDate(from)+' – '+_thDate(to)); }
  closePdfRangeModal(); await exportPDF(from,to,title);
}
function _thDate(d){const p=(d||'').split('-');if(p.length!==3)return d;return `${p[2]}/${p[1]}/${parseInt(p[0])+543}`;}
async function exportPDF(fromDate,toDate,reportTitle){
  const btn=document.getElementById('pdfExportBtn'); const orig=btn?btn.innerHTML:'';
  if(btn){btn.disabled=true;btn.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> กำลังสร้าง...';}
  try{
    const rows=PDF_LOGS.filter(l=>{const d=l.date||'';return d>=fromDate&&d<=toDate;});
    if(rows.length===0){alert('ไม่มีข้อมูลในช่วงวันที่นี้');if(btn){btn.disabled=false;btn.innerHTML=orig;}return;}
    const {jsPDF}=window.jspdf; const pdf=new jsPDF('l','mm','a4'); const pageW=297,pageH=210,margin=10,usableW=pageW-margin*2,usableH=pageH-margin*2;
    const stage=document.createElement('div'); stage.style.cssText='position:fixed;left:-99999px;top:0;background:#fff;font-family:"IBM Plex Sans Thai",sans-serif;padding:0;'; document.body.appendChild(stage);
    const totPrice=rows.reduce((s,r)=>s+r.price,0),totDist=rows.reduce((s,r)=>s+r.distance,0),totLiters=rows.reduce((s,r)=>s+r.liters,0);
    const avgKml=totLiters>0?totDist/totLiters:0;
    const byDriver={}; rows.forEach(r=>{const n=r.driver||'ไม่ระบุ';if(!byDriver[n])byDriver[n]={rows:[],price:0,dist:0,liters:0};byDriver[n].rows.push(r);byDriver[n].price+=r.price;byDriver[n].dist+=r.distance;byDriver[n].liters+=r.liters;});
    const driverNames=Object.keys(byDriver).filter(n=>byDriver[n].price>0||byDriver[n].dist>0).sort((a,b)=>{const ta=byDriver[a].dist>0?byDriver[a].price/byDriver[a].dist:0;const tb=byDriver[b].dist>0?byDriver[b].price/byDriver[b].dist:0;return tb-ta;});
    driverNames.forEach(n=>{byDriver[n].rows.sort((a,b)=>(a.date||'').localeCompare(b.date||''));});
    async function renderPage(el,isFirst){ stage.appendChild(el); if(!isFirst)pdf.addPage(); const canvas=await html2canvas(el,{scale:1.5,backgroundColor:'#fff',logging:false}); const imgData=canvas.toDataURL('image/jpeg',0.80); const imgW=usableW,imgH=canvas.height*imgW/canvas.width; pdf.addImage(imgData,'JPEG',margin,margin,imgW,Math.min(imgH,usableH)); stage.removeChild(el); }
    const p1=document.createElement('div'); p1.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    const totHours=rows.reduce((s,r)=>s+(r.hours||0),0);
    let h1=`<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #3b82f6;padding-bottom:10px;margin-bottom:14px;"><div><div style="font-size:20px;font-weight:700;color:#18181b;">${reportTitle}</div><div style="font-size:11px;color:#71717a;margin-top:2px;">ระบบติดตามน้ำมันรถ · ${CURRENT_USER}</div></div><div style="text-align:right;font-size:10px;color:#a1a1aa;">พิมพ์เมื่อ ${new Date().toLocaleString('th-TH',{timeZone:TZ})}</div></div>`;
    h1+=`<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:7px;margin-bottom:14px;">${_pdfStat('ค่าน้ำมันรวม','฿'+Math.round(totPrice).toLocaleString())}${_pdfStat('ระยะทางรวม',Math.round(totDist).toLocaleString()+' km')}${_pdfStat('น้ำมันรวม',fmtN(totLiters)+' L')}${_pdfStat('เฉลี่ย km/L',fmtN(avgKml))}${_pdfStat('ชั่วโมงรวม',fmtN(totHours)+' ชม.')}${_pdfStat('จำนวน',rows.length+' รายการ · '+driverNames.length+' คน')}</div>`;
    const rankPrice=[...driverNames].sort((a,b)=>byDriver[b].price-byDriver[a].price);
    const rankDist=[...driverNames].sort((a,b)=>byDriver[b].dist-byDriver[a].dist);
    const rankHours=[...driverNames].sort((a,b)=>{const ha=byDriver[a].rows.reduce((s,r)=>s+(r.hours||0),0);const hb=byDriver[b].rows.reduce((s,r)=>s+(r.hours||0),0);return hb-ha;});
    const rankKml=[...driverNames].filter(n=>byDriver[n].liters>0).sort((a,b)=>(byDriver[b].dist/byDriver[b].liters)-(byDriver[a].dist/byDriver[a].liters));
    const medal=i=>i===0?'🥇':i===1?'':i===2?'🥉':'';
    const _rankRow=(arr,valFn)=>arr.map((n,i)=>`<div style="display:flex;align-items:center;gap:6px;padding:4px 0;${i<arr.length-1?'border-bottom:1px solid #f4f4f5;':''}"><span style="font-size:13px;width:20px;text-align:center;">${medal(i)}</span><span style="font-size:11px;font-weight:600;color:#18181b;flex:1;">${n}</span><span style="font-size:11px;font-weight:700;color:#3f3f46;font-family:ui-monospace,monospace;">${valFn(n)}</span></div>`).join('');
    h1+=`<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-bottom:14px;"><div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#92400e;margin-bottom:6px;">⛽ เติมน้ำมันมากสุด</div>${_rankRow(rankPrice,n=>'฿'+Math.round(byDriver[n].price).toLocaleString())}</div><div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#1e40af;margin-bottom:6px;">️ ขับรถไกลสุด</div>${_rankRow(rankDist,n=>Math.round(byDriver[n].dist).toLocaleString()+' km')}</div><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#166534;margin-bottom:6px;">⏱️ ใช้เวลาทำงานมากสุด</div>${_rankRow(rankHours,n=>{const h=byDriver[n].rows.reduce((s,r)=>s+(r.hours||0),0);return fmtN(h)+' ชม.';})}</div><div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#6b21a8;margin-bottom:6px;"> ประหยัดน้ำมันสุด (km/L)</div>${_rankRow(rankKml,n=>{const k=byDriver[n].dist/byDriver[n].liters;return fmtN(k)+' km/L';})}</div></div>`;
    p1.innerHTML=h1; await renderPage(p1,true);
    const pSum=document.createElement('div'); pSum.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    let hSum=`<div style="font-size:16px;font-weight:700;color:#18181b;margin-bottom:14px;border-bottom:2px solid #3b82f6;padding-bottom:8px;">ค่าน้ำมันแยกตามคนขับ · ${reportTitle}</div><table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr style="background:#f4f4f5;"><th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e4e4e7;">คนขับ</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ค่าน้ำมัน</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ระยะ</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ลิตร</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">km/L</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">฿/km</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ชม.</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">รายการ</th></tr></thead><tbody>`;
    driverNames.forEach((n,i)=>{const d=byDriver[n];const kml=d.liters>0?d.dist/d.liters:0;const thbKm=d.dist>0?d.price/d.dist:0;const hrs=d.rows.reduce((s,r)=>s+(r.hours||0),0);hSum+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;font-weight:600;">${n}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">฿${Math.round(d.price).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${Math.round(d.dist).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(d.liters)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${kml>0?fmtN(kml):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(hrs)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${d.rows.length}</td></tr>`;});
    hSum+=`</tbody></table>`; pSum.innerHTML=hSum; await renderPage(pSum,false);
    const ROWS_PER_PAGE=24;
    for(const drvName of driverNames){
      const drvRows=byDriver[drvName].rows; const drvPrice=byDriver[drvName].price; const drvDist=byDriver[drvName].dist; const drvLiters=byDriver[drvName].liters;
      const drvKml=drvLiters>0?drvDist/drvLiters:0; const drvThbKm=drvDist>0?drvPrice/drvDist:0;
      const chunks=[]; for(let i=0;i<drvRows.length;i+=ROWS_PER_PAGE)chunks.push(drvRows.slice(i,i+ROWS_PER_PAGE));
      for(let ci=0;ci<chunks.length;ci++){
        const chunk=chunks[ci]; const page=document.createElement('div'); page.style.cssText='width:1200px;background:#fff;padding:28px 36px;box-sizing:border-box;';
        let html=`<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid #3b82f6;padding-bottom:8px;margin-bottom:10px;"><div><div style="font-size:16px;font-weight:700;">${drvName}</div><div style="font-size:10px;color:#71717a;">${reportTitle}${chunks.length>1?' · ('+(ci+1)+'/'+chunks.length+')':''}</div></div><div style="display:flex;gap:14px;font-size:10px;"><span>฿<b style="font-size:13px">${Math.round(drvPrice).toLocaleString()}</b></span><span><b style="font-size:13px">${Math.round(drvDist).toLocaleString()}</b> km</span><span><b style="font-size:13px">${fmtN(drvLiters)}</b> L</span>${drvKml>0?`<span><b style="font-size:13px">${fmtN(drvKml)}</b> km/L</span>`:''}</div></div><table style="width:100%;border-collapse:collapse;font-size:11px;"><thead><tr style="background:#f4f4f5;"><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">วันที่</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">ทะเบียน</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">เวลา</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">฿</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">km</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">L</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">km/L</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">฿/km</th></tr></thead><tbody>`;
        chunk.forEach((r,i)=>{const dp=(r.date||'').split('-');const dateText=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'—';const thbKm=(r.distance>0&&r.price>0)?(r.price/r.distance):0;const startT=(r.start||'').substring(0,5);const endT=(r.end||'').substring(0,5);const timeT=(startT&&endT)?startT+'-'+endT:'—';html+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${dateText}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${r.plate||'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;font-size:10px;color:#71717a;">${timeT}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.price>0?'฿'+Math.round(r.price).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.distance>0?Math.round(r.distance).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.liters>0?fmtN(r.liters):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.kml>0?fmtN(r.kml):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td></tr>`;});
        html+='</tbody>';
        if(ci===chunks.length-1){html+=`<tfoot><tr style="background:#f0f7ff;border-top:3px solid #3b82f6;"><td colspan="3" style="padding:10px;font-weight:700;font-size:14px;color:#1e40af;">รวม ${drvName}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">฿${Math.round(drvPrice).toLocaleString()}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${Math.round(drvDist).toLocaleString()} km</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${fmtN(drvLiters)} L</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${drvKml>0?fmtN(drvKml)+' km/L':'—'}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;color:#1e40af;">${drvThbKm>0?'฿'+fmtN(drvThbKm)+'/km':'—'}</td></tr></tfoot>`;}
        html+='</table>'; page.innerHTML=html; await renderPage(page,false);
      }
    }
    document.body.removeChild(stage);
    const fn=(fromDate===toDate)?`รายงานน้ำมัน_${fromDate}.pdf`:`รายงานน้ำมัน_${fromDate}_ถึง_${toDate}.pdf`;
    pdf.save(fn);
  }catch(e){ console.error('PDF error',e); alert('สร้าง PDF ไม่สำเร็จ: '+e.message); }
  finally{ if(btn){btn.disabled=false;btn.innerHTML=orig;} }
}
function _pdfStat(label,value){ return `<div style="background:#f9fafb;border:1px solid #f0f0f0;border-radius:12px;padding:14px 16px;"><div style="font-size:12px;color:#71717a;margin-bottom:4px;">${label}</div><div style="font-size:19px;font-weight:700;color:#18181b;">${value}</div></div>`; }

document.addEventListener('DOMContentLoaded',function(){
  console.log('📊 เริ่มต้นโหลดกราฟ...');
  console.log('DLV_BY_DRIVER:', DLV_BY_DRIVER);
  console.log('KML_BY_DRIVER:', KML_BY_DRIVER);
  console.log('COST_BY_DRIVER:', COST_BY_DRIVER);
  console.log('REPORT_LOGS:', REPORT_LOGS);
  
  renderReportPage();
  
  try{ 
    if(document.getElementById('deliveryChart')){
      console.log('🎨 Render Delivery Chart...');
      renderDlv(); 
    }
  }catch(e){console.error('dlv error:',e);}
  
  try{ 
    if(document.getElementById('chartKml')){
      console.log('🎨 Render KML Chart...');
      renderKmlChart(); 
    }
  }catch(e){console.error('kml error:',e);}
  
  try{ 
    if(document.getElementById('chartCost')){
      console.log('🎨 Render Cost Chart...');
      renderCostChart(); 
    }
  }catch(e){console.error('cost error:',e);}
  
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
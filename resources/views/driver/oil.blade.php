{{-- resources/views/driver/oil.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบติดตามน้ำมันรถ</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
:root{--navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;--accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;--bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;--border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;--shadow:0 2px 12px rgba(26,39,68,.08);--radius:12px;--radius-sm:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.navbar{background:var(--navy);padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.nb-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:15px;flex-shrink:0;text-decoration:none}
.nb-icon{width:34px;height:34px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px}
.nb-sub{font-size:10px;font-weight:300;opacity:.6;letter-spacing:1px}
.nb-menu{display:flex;align-items:center;gap:2px;flex:1;margin:0 16px}
.nb-btn{display:flex;align-items:center;gap:6px;padding:7px 13px;color:rgba(255,255,255,.7);text-decoration:none;font-family:'Sarabun',sans-serif;font-size:13px;font-weight:500;border:none;background:transparent;cursor:pointer;border-radius:7px;transition:all .2s;white-space:nowrap}
.nb-btn:hover{color:#fff;background:rgba(255,255,255,.1)}
.nb-btn.active{color:#fff;background:rgba(79,142,247,.3)}
.nb-right{display:flex;align-items:center;gap:12px;flex-shrink:0}
.nav-date{color:rgba(255,255,255,.65);font-size:12px}
.nav-user{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);border-radius:20px;padding:4px 12px 4px 5px}
.nav-avatar{width:26px;height:26px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:11px;color:#fff}
.nav-user span{color:#fff;font-size:12px;font-weight:500}
.layout{display:flex;min-height:calc(100vh - 60px)}
.main{flex:1;overflow-x:hidden;padding:24px}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.page-title{font-size:20px;font-weight:600;color:var(--navy)}
.page-subtitle{font-size:13px;color:var(--text2);margin-top:2px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s;text-decoration:none}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:#3a7ce0}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}.btn-outline:hover{background:var(--surface2)}
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:12px 16px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:18px;box-shadow:var(--shadow)}
.filter-bar select,.filter-bar input{font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px}
.view-tabs{display:flex;background:var(--surface2);border-radius:var(--radius-sm);padding:3px;gap:2px;border:1px solid var(--border)}
.view-tab{padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;color:var(--text2);border:none;background:transparent;font-family:'Sarabun',sans-serif;transition:all .15s;white-space:nowrap}
.view-tab.active{background:var(--surface);color:var(--accent);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.srch-wrap{position:relative;display:flex;align-items:center}
.srch-wrap .si{position:absolute;left:9px;font-size:13px;color:var(--text3);pointer-events:none}
.srch-wrap input{padding-left:30px!important;height:36px;font-family:'Sarabun',sans-serif;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none}
.srch-wrap input:focus{border-color:var(--accent)}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px}
.metric-card{background:var(--surface);border-radius:var(--radius);padding:16px 18px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.blue::before{background:var(--accent)}.metric-card.green::before{background:var(--accent2)}.metric-card.amber::before{background:var(--accent3)}.metric-card.navy::before{background:var(--navy)}
.metric-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:10px}
.metric-icon.blue{background:rgba(79,142,247,.1)}.metric-icon.green{background:rgba(56,201,138,.1)}.metric-icon.amber{background:rgba(245,166,35,.12)}.metric-icon.navy{background:rgba(26,39,68,.08)}
.metric-label{font-size:12px;color:var(--text2);font-weight:500;margin-bottom:3px}
.metric-value{font-size:24px;font-weight:700;color:var(--text);line-height:1}
.metric-sub{font-size:11px;color:var(--text3);margin-top:3px}
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px}
@media(max-width:768px){.charts-grid{grid-template-columns:1fr}}
.chart-card{background:var(--surface);border-radius:var(--radius);padding:18px;border:1px solid var(--border);box-shadow:var(--shadow)}
.chart-card-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:3px}
.chart-card-sub{font-size:12px;color:var(--text2);margin-bottom:14px}
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:18px}
.table-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.table-title{font-size:15px;font-weight:600;color:var(--text)}
.badge-count{background:var(--accent);color:#fff;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:8px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:var(--surface2);padding:10px 14px;text-align:left;font-weight:600;font-size:12px;color:var(--text2);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:10px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.km-val{font-weight:700;color:var(--accent2)}
.action-btns{display:flex;gap:6px}
.action-btn{width:28px;height:28px;border-radius:6px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .15s}
.action-btn.edit{background:rgba(79,142,247,.1);color:var(--accent)}.action-btn.edit:hover{background:var(--accent);color:#fff}
.action-btn.del{background:rgba(232,93,93,.1);color:var(--accent4)}.action-btn.del:hover{background:var(--accent4);color:#fff}
.plate-tag{background:var(--surface2);border:1px solid var(--border);border-radius:4px;font-size:11px;color:var(--text2);padding:1px 6px;font-family:monospace;font-weight:600}
.job-table-wrap{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.job-table-wrap table{font-size:12px}.job-table-wrap thead th{font-size:11px;padding:8px 12px}.job-table-wrap tbody td{padding:9px 12px}
.job-status-btns{display:flex;gap:5px;flex-wrap:wrap}
.job-btn{padding:4px 10px;border-radius:20px;border:1px solid var(--border);font-size:11px;font-weight:600;cursor:pointer;font-family:'Sarabun',sans-serif;background:var(--surface2);color:var(--text2);transition:all .15s}
.job-btn:hover{background:var(--border)}
.job-btn.ok{background:rgba(56,201,138,.15);color:#1a7a4d;border-color:rgba(56,201,138,.4)}
.job-btn.fail{background:rgba(232,93,93,.15);color:#c0392b;border-color:rgba(232,93,93,.4)}
.job-note-input{width:100%;margin-top:5px;padding:5px 9px;border:1px solid rgba(232,93,93,.3);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:12px;color:var(--text);background:var(--surface);outline:none;resize:none}
.job-loading{text-align:center;padding:18px;color:var(--text3);font-size:13px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius)}
.job-bill{font-family:monospace;font-size:11px;background:var(--surface2);border:1px solid var(--border);border-radius:3px;padding:1px 5px;color:var(--text2)}
.job-summary-bar{display:flex;gap:8px;padding:10px 14px;background:var(--surface2);border-top:1px solid var(--border);flex-wrap:wrap}
.job-chip{font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;border:1px solid var(--border);color:var(--text2);background:var(--surface)}
.job-chip.ok{background:rgba(56,201,138,.1);color:#1a7a4d;border-color:rgba(56,201,138,.25)}
.job-chip.fail{background:rgba(232,93,93,.1);color:#c0392b;border-color:rgba(232,93,93,.25)}
.job-date-chip{font-size:11px;color:var(--text3);background:var(--surface2);border:1px solid var(--border);border-radius:20px;padding:2px 9px}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.modal-title{font-size:16px;font-weight:600;color:var(--text)}
.modal-close{width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:18px 22px;overflow-y:auto;flex:1}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}
.step-indicator{display:flex;align-items:center;justify-content:center;margin-bottom:20px;width:100%}
.step-item{display:flex;align-items:center;gap:8px;white-space:nowrap;flex-shrink:0}
.step-circle{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;transition:all .3s}
.step-circle.active{background:var(--accent);color:#fff;box-shadow:0 0 0 4px rgba(79,142,247,.2)}
.step-circle.done{background:var(--accent2);color:#fff}
.step-circle.inactive{background:var(--border);color:var(--text3)}
.step-label{font-size:12px;font-weight:600;transition:color .3s}
.step-label.active{color:var(--accent)}.step-label.done{color:var(--accent2)}.step-label.inactive{color:var(--text3)}
.step-line{flex:0 0 120px;height:2px;margin:0 10px;border-radius:2px;transition:background .3s}
.step-line.done{background:var(--accent2)}.step-line.inactive{background:var(--border)}
.driver-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.driver-card-grid .full{grid-column:1/-1}
.driver-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px;color:#fff}
.driver-avatar{width:44px;height:44px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.driver-banner-name{font-size:17px;font-weight:700;line-height:1}
.driver-banner-plate{font-size:12px;opacity:.7;margin-top:4px;font-family:monospace}
.time-picker-row{display:grid;grid-template-columns:1fr auto 1fr;align-items:end;gap:8px}
.time-arrow{font-size:18px;color:var(--text3);text-align:center;padding-bottom:10px}
.time-select-wrap{display:flex;gap:4px;align-items:center}
.time-select{font-family:'Sarabun',sans-serif;font-size:14px;padding:9px 8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface);color:var(--text);outline:none;flex:1;text-align:center}
.time-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
.time-colon{font-size:18px;font-weight:700;color:var(--text2);padding-bottom:2px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
.form-control.is-invalid{border-color:var(--accent4)}
.form-control.auto-calc{background:var(--surface2);color:var(--text2);cursor:default}
textarea.form-control{resize:vertical;min-height:65px}
.invalid-feedback{font-size:11px;color:var(--accent4);margin-top:4px}
.auto-hint{font-size:11px;color:var(--accent);margin-top:4px;font-weight:500}
.section-divider{font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:4px 0 8px;border-bottom:1px solid var(--border);margin:14px 0 10px;grid-column:1/-1}
.oil-price-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:12px;color:#fff}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--accent2);animation:pulse 1.5s infinite}
.live-dot.loading{background:var(--accent3)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.calc-box{background:rgba(56,201,138,.06);border:1px solid rgba(56,201,138,.25);border-radius:var(--radius-sm);padding:12px 14px;display:none;margin-top:10px}
.calc-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}
.calc-item .lbl{font-size:11px;color:var(--text3)}
.calc-item .val{font-size:19px;font-weight:700;color:var(--accent2)}
.calc-item .val.amber{color:var(--accent3)}
.calc-item .unit{font-size:11px;color:var(--text3)}
.alert{padding:11px 14px;border-radius:var(--radius-sm);margin-bottom:14px;font-size:14px}
.alert-success{background:rgba(56,201,138,.1);border:1px solid rgba(56,201,138,.3);color:#1a7a4d}
.alert-error{background:rgba(232,93,93,.1);border:1px solid rgba(232,93,93,.3);color:#c0392b}
.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.empty-state .icon{font-size:44px;margin-bottom:10px;opacity:.4}
.summary-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}
.summary-chip{background:var(--surface2);border:1px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;color:var(--text2);display:flex;align-items:center;gap:5px;font-weight:500}
.summary-chip strong{color:var(--navy)}
.report-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:16px 20px;background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius);color:#fff;flex-wrap:wrap;gap:10px;box-shadow:0 4px 16px rgba(26,58,107,.18)}
.report-section-title{font-size:17px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px}
.report-section-sub{font-size:12px;color:rgba(255,255,255,.65);margin-top:2px}
.report-pie-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px}
@media(max-width:900px){.report-pie-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.report-pie-grid{grid-template-columns:1fr}}
.pie-card{background:var(--surface);border-radius:var(--radius);padding:18px;border:1px solid var(--border);box-shadow:var(--shadow)}
.pie-card-title{font-size:16px;font-weight:700;color:var(--text);margin-bottom:3px}
.pie-card-sub{font-size:13px;color:var(--text2);margin-bottom:12px}
.pie-canvas-wrap{position:relative;width:100%;height:200px}
.pie-legend{margin-top:10px;display:flex;flex-direction:column;gap:5px;max-height:140px;overflow-y:auto}
.pie-legend-item{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text2)}
.pie-legend-dot{width:11px;height:11px;border-radius:50%;flex-shrink:0}
.pie-legend-label{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pie-legend-val{font-weight:600;color:var(--text);white-space:nowrap;font-size:13px}
.report-driver-table{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:18px}
.report-driver-table table thead th{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);color:rgba(255,255,255,.85);font-size:12px;padding:11px 14px;text-align:left;border-bottom:none;white-space:nowrap}
.report-driver-table table tbody td{padding:11px 14px;font-size:13px;border-bottom:1px solid var(--border)}
.report-driver-table table tbody tr:last-child td{border-bottom:none;background:var(--surface2);font-weight:700}
.rank-badge{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:700;background:var(--surface2);color:var(--text2)}
.rank-badge.gold{background:#fef3c7;color:#b45309}.rank-badge.silver{background:#f1f5f9;color:#475569}.rank-badge.bronze{background:#fef0e7;color:#9a3412}
.kml-bar-wrap{display:flex;align-items:center;gap:8px}
.kml-bar-bg{flex:1;height:6px;background:var(--surface2);border-radius:3px;overflow:hidden}
.kml-bar-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--accent2),#10b981);transition:width .5s ease}
.report-stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:18px}
.report-stat-card{background:var(--surface);border-radius:var(--radius);padding:14px 16px;border:1px solid var(--border);box-shadow:var(--shadow);text-align:center}
.report-stat-label{font-size:12px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.report-stat-value{font-size:22px;font-weight:700;color:var(--navy)}
.report-stat-sub{font-size:12px;color:var(--text3);margin-top:2px}
.dlv-filter-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px}
.dlv-controls{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.dlv-sel{height:36px;font-family:'Sarabun',sans-serif;font-size:13px;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none}
.dlv-sel:focus{border-color:var(--accent)}
.print-header{display:none;padding:0 0 16px;margin-bottom:16px;border-bottom:2px solid var(--navy)}
.print-header-title{font-size:18px;font-weight:700;color:var(--navy)}

/* ── TOAST ── */
.toast{position:fixed;top:76px;right:20px;z-index:9999;
  min-width:240px;max-width:340px;
  background:#1a7a4d;color:#fff;
  padding:14px 18px 18px 16px;
  border-radius:12px;
  box-shadow:0 6px 28px rgba(0,0,0,.22);
  display:flex;align-items:flex-start;gap:10px;
  font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;
  animation:toastIn .35s cubic-bezier(.34,1.56,.64,1) both;
  overflow:hidden;
}
.toast.hiding{animation:toastOut .35s ease forwards}
.toast-icon{font-size:20px;line-height:1;flex-shrink:0;margin-top:1px}
.toast-body{flex:1}
.toast-title{font-weight:700;font-size:14px;margin-bottom:2px}
.toast-msg{font-size:13px;opacity:.88}
.toast-progress{position:absolute;bottom:0;left:0;height:4px;background:rgba(255,255,255,.45);border-radius:0 0 12px 12px;width:100%}
@keyframes toastIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}
@keyframes toastOut{from{transform:translateX(0);opacity:1}to{transform:translateX(110%);opacity:0}}
@keyframes toastShrink{from{width:100%}to{width:0%}}

@media print{body{background:#fff!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}.navbar,.filter-bar,.action-btns,.modal-overlay,.no-print{display:none!important}.main{padding:0!important}#pageTracking{display:none!important}#pageReport{display:block!important}.print-header{display:block!important}canvas{max-width:100%!important}}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}.driver-card-grid{grid-template-columns:1fr}.main{padding:14px}.metrics{grid-template-columns:1fr 1fr}.time-picker-row{grid-template-columns:1fr auto 1fr}.nb-menu{display:none}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a class="nb-brand" href="{{ route('oil') }}">
    <div class="nb-icon">⛽</div>
    <div><div>ระบบจัดการเซอร์วิส</div><div class="nb-sub">SERVICE MANAGEMENT</div></div>
  </a>
  <div class="nb-menu">
    <button class="nb-btn active" id="navOil" onclick="switchPage('tracking')"><span>⛽</span>ติดตามน้ำมัน</button>
    <button class="nb-btn" id="navReport" onclick="switchPage('report')"><span>📊</span>สรุปรายงาน</button>
    <a class="nb-btn" href="{{ url('/service') }}"><span>🛠️</span>Service</a>
  </div>
</nav>

<div class="layout">
<main class="main">

{{-- ── TOAST NOTIFICATION ── --}}
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
  // shrink progress bar
  bar.style.transition = 'width ' + DURATION + 'ms linear';
  requestAnimationFrame(function(){
    requestAnimationFrame(function(){
      bar.style.width = '0%';
    });
  });
  // hide after duration
  setTimeout(function(){
    toast.classList.add('hiding');
    setTimeout(function(){ toast.remove(); }, 380);
  }, DURATION);
  // click to dismiss
  toast.addEventListener('click', function(){
    toast.classList.add('hiding');
    setTimeout(function(){ toast.remove(); }, 380);
  });
})();
</script>
@endif

{{-- PAGE: TRACKING --}}
<div id="pageTracking">
  <div class="page-header">
    <div><div class="page-title">ระบบติดตามน้ำมันรถ</div><div class="page-subtitle">บันทึกและวิเคราะห์การใช้น้ำมันแต่ละคนขับ</div></div>
    <div style="display:flex;gap:10px;align-items:center">
      <div class="srch-wrap"><span class="si">🔍</span><input type="text" placeholder="ค้นหาชื่อคนขับ..." oninput="filterOilTable(this.value)" style="min-width:170px"></div>
      <button class="btn btn-primary" onclick="openModal()">+ เพิ่มข้อมูลน้ำมัน</button>
    </div>
  </div>

  <form method="GET" action="{{ route('oil') }}" id="filterForm">
    <div class="filter-bar">
      <div class="view-tabs">
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="submit" name="view" value="{{ $v }}" class="view-tab {{ $view===$v?'active':''}}">{{ $label }}</button>
        @endforeach
      </div>
      @if($view==='day')
      <input type="date" name="date" value="{{ $filterDay }}" onchange="this.form.submit()">
      @elseif($view==='year')
      <select name="year" onchange="this.form.submit()">
        @for($y=date('Y');$y>=2020;$y--)
        <option value="{{ $y }}" {{ request('year',date('Y'))==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
      @elseif($view!=='all')
      <input type="month" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()">
      @endif
      <select name="driver_name" onchange="this.form.submit()">
        <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @foreach($drivers as $d)
        <option value="{{ $d }}" {{ $filterDriver===$d?'selected':'' }}>{{ $d }}</option>
        @endforeach
      </select>
    </div>
  </form>

  @if($metrics)
  <div class="metrics">
    <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวม</div><div class="metric-value">{{ $metrics['total_liters'] }}</div><div class="metric-sub">ลิตร</div></div>
    <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿{{ number_format($metrics['total_price']) }}</div><div class="metric-sub">บาท</div></div>
    <div class="metric-card green"><div class="metric-icon green">📈</div><div class="metric-label">เฉลี่ย km/L</div><div class="metric-value">{{ $metrics['avg_km_per_liter'] }}</div><div class="metric-sub">กม./ลิตร</div></div>
    <div class="metric-card navy"><div class="metric-icon navy">⏱</div><div class="metric-label">ชม.ทำงาน</div><div class="metric-value">{{ $metrics['total_work_hours'] }}</div><div class="metric-sub">ชั่วโมง</div></div>
  </div>
  @endif

  <div class="table-card">
    <div class="table-header">
      <div><span class="table-title">รายการเติมน้ำมัน</span><span class="badge-count" id="oilCount">{{ $logs->count() }}</span></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>วันที่ทำงาน</th><th>คนขับ / ทะเบียน</th><th>เวลาทำงาน</th><th>ระยะทาง</th><th>ลิตร</th><th>ค่าน้ำมัน (฿)</th><th>km/L</th><th>บันทึกเมื่อ (เวลาไทย)</th><th></th></tr></thead>
        <tbody id="oilTbody">
          @forelse($logs as $i => $r)
          @php
            $kml=$r['km_per_liter']??0;
            $dist=($r['total_distance']??0)>0?number_format($r['total_distance'],2):'—';
            $wh=$r['work_hours']??0;
            $createdTH=$r['created_at']?\Carbon\Carbon::parse($r['created_at'])->timezone('Asia/Bangkok')->format('d/m/Y H:i'):'—';
          @endphp
          <tr data-driver="{{ strtolower($r['driver_name']) }}">
            <td style="color:var(--text3)">{{ $i+1 }}</td>
            <td>{{ $r['work_date'] }}</td>
            <td><strong style="color:var(--navy)">{{ $r['driver_name'] }}</strong><div><span class="plate-tag">{{ $r['vehicle_id']??'—' }}</span></div></td>
            <td style="font-size:12px;color:var(--text2)">{{ $r['start_time']??'—' }} – {{ $r['end_time']??'—' }}@if($wh>0)<div style="font-size:11px;color:var(--accent3);font-weight:600">{{ $wh }} ชม.</div>@endif</td>
            <td>{{ $dist }}</td>
            <td>{{ $r['liters']?number_format($r['liters'],2).' ล.':'—' }}</td>
            <td style="font-weight:600">{{ $r['total_price']?'฿'.number_format($r['total_price'],2):'—' }}</td>
            <td>@if($kml>0)<span class="km-val">{{ number_format($kml,1) }}</span>@else<span style="color:var(--text3);font-size:11px">—</span>@endif</td>
            <td style="font-size:11px;color:var(--text3)">{{ $createdTH }}</td>
            <td><div class="action-btns"></div></td>
          </tr>
          @empty
          <tr><td colspan="10"><div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการ</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="charts-grid">
    <div class="chart-card"><div class="chart-card-title">ค่าน้ำมัน (฿) ต่อคนขับ</div><div class="chart-card-sub">รวม total_price แต่ละคน</div><div style="position:relative;width:100%;height:220px"><canvas id="chartDriver"></canvas></div></div>
    <div class="chart-card"><div class="chart-card-title">น้ำมันต่อกิโล (km/L)</div><div class="chart-card-sub">เฉลี่ย km/L แต่ละคน</div><div style="position:relative;width:100%;height:220px"><canvas id="chartKml"></canvas></div></div>
  </div>

  {{-- Delivery chart --}}
  <div class="chart-card" style="margin-bottom:18px">
    <div class="dlv-filter-row">
      <div>
        <div class="chart-card-title"> รายการสมบูรณ์ / รายการผิดพลาด</div>
        <div class="chart-card-sub" style="margin-bottom:0">ประสิทธิภาพการส่งสินค้าแยกตามคนขับ</div>
      </div>
      <div class="dlv-controls">
        <div class="view-tabs" id="dlvTabs">
          <button class="view-tab active" onclick="setDlv('day',this)">รายวัน</button>
          <button class="view-tab" onclick="setDlv('month',this)">รายเดือน</button>
          <button class="view-tab" onclick="setDlv('year',this)">รายปี</button>
          <button class="view-tab" onclick="setDlv('all',this)">ทั้งหมด</button>
        </div>
        {{-- year selector --}}
        <select id="dlvYearSel" onchange="renderDlv()" class="dlv-sel" style="display:none">
          @for($y=date('Y');$y>=2020;$y--)
          <option value="{{ $y }}" {{ $y==date('Y')?'selected':'' }}>{{ $y }}</option>
          @endfor
        </select>
        {{-- driver selector (injected by JS, placeholder kept here) --}}
        <select id="dlvDriverSel" onchange="dlvDriver=this.value;renderDlv()" class="dlv-sel">
          <option value="all">ทุกคน (รวม)</option>
        </select>
      </div>
    </div>
    <div style="height:300px;position:relative"><canvas id="deliveryChart"></canvas></div>
    {{-- legend --}}
    <div id="dlvLegend" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)"></div>
  </div>
</div>{{-- end pageTracking --}}

{{-- PAGE: REPORT --}}
<div id="pageReport" style="display:none">
  <div class="print-header"><div class="print-header-title">📊 สรุปรายงานการใช้น้ำมันรถ</div></div>
  <div class="report-section-header">
    <div><div class="report-section-title">📊 สรุปรายงาน</div><div class="report-section-sub">วิเคราะห์การใช้น้ำมันแยกตามคนขับ</div></div>
    <div class="no-print"><button class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'" onclick="printReport()">🖨️ พิมพ์ / PDF</button></div>
  </div>
  @php
    $totalLogs=$logs->count();$avgPrice=$totalLogs>0?$logs->avg('total_price'):0;
    $maxDriver=$costByDriver&&count($costByDriver)?collect($costByDriver)->sortByDesc('total_price')->first():null;
    $bestKml=$kmlByDriver&&count($kmlByDriver)?collect($kmlByDriver)->sortByDesc('km_per_liter')->first():null;
    $totalDist=$logs->sum('total_distance');$totalLiters=$logs->sum('liters');
  @endphp
  <div class="report-stat-row">
    <div class="report-stat-card"><div class="report-stat-label">รายการทั้งหมด</div><div class="report-stat-value">{{ $totalLogs }}</div><div class="report-stat-sub">รายการ</div></div>
    <div class="report-stat-card"><div class="report-stat-label">ระยะทางรวม</div><div class="report-stat-value">{{ $totalDist>0?number_format($totalDist,0):'—' }}</div><div class="report-stat-sub">กิโลเมตร</div></div>
    <div class="report-stat-card"><div class="report-stat-label">เฉลี่ย ฿/ครั้ง</div><div class="report-stat-value">{{ $avgPrice>0?'฿'.number_format($avgPrice,0):'—' }}</div><div class="report-stat-sub">บาท/ครั้ง</div></div>
    @if($maxDriver)<div class="report-stat-card"><div class="report-stat-label">ใช้น้ำมันสูงสุด</div><div class="report-stat-value" style="font-size:15px">{{ $maxDriver['driver'] }}</div><div class="report-stat-sub">฿{{ number_format($maxDriver['total_price'],0) }}</div></div>@endif
    @if($bestKml)<div class="report-stat-card"><div class="report-stat-label">ประหยัดที่สุด</div><div class="report-stat-value" style="font-size:15px">{{ $bestKml['driver'] }}</div><div class="report-stat-sub">{{ $bestKml['km_per_liter'] }} km/L</div></div>@endif
  </div>
  <div class="report-pie-grid">
    <div class="pie-card"><div class="pie-card-title">สัดส่วนค่าน้ำมัน (฿)</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieCost"></canvas></div><div class="pie-legend" id="pieCostLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนลิตรที่เติม (ล.)</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieLiters"></canvas></div><div class="pie-legend" id="pieLitersLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนชั่วโมงทำงาน</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieHours"></canvas></div><div class="pie-legend" id="pieHoursLegend"></div></div>
  </div>
  @php
    $driverSummary=[];
    foreach($logs as $log){
      $name=$log['driver_name'];
      if(!isset($driverSummary[$name]))$driverSummary[$name]=['driver'=>$name,'records'=>0,'total_price'=>0,'total_liters'=>0,'total_dist'=>0,'work_hours'=>0,'kml_values'=>[]];
      $driverSummary[$name]['records']++;
      $driverSummary[$name]['total_price']+=$log['total_price']??0;
      $driverSummary[$name]['total_liters']+=$log['liters']??0;
      $driverSummary[$name]['total_dist']+=$log['total_distance']??0;
      $driverSummary[$name]['work_hours']+=$log['work_hours']??0;
      if(($log['km_per_liter']??0)>0)$driverSummary[$name]['kml_values'][]=$log['km_per_liter'];
    }
    usort($driverSummary,fn($a,$b)=>$b['total_price']<=>$a['total_price']);
    $grandTotal=['price'=>array_sum(array_column($driverSummary,'total_price')),'liters'=>array_sum(array_column($driverSummary,'total_liters')),'dist'=>array_sum(array_column($driverSummary,'total_dist')),'hours'=>array_sum(array_column($driverSummary,'work_hours')),'records'=>array_sum(array_column($driverSummary,'records'))];
    $maxKmlForBar=count($driverSummary)?max(array_map(fn($d)=>count($d['kml_values'])>0?array_sum($d['kml_values'])/count($d['kml_values']):0,$driverSummary)):1;
  @endphp
  <div class="report-driver-table">
    <div class="table-header"><div class="table-title">📋 ตารางสรุปรายคนขับ</div><span style="font-size:12px;color:var(--text2)">เรียงตามค่าน้ำมันสูงสุด</span></div>
    <div class="table-wrap"><table>
      <thead><tr><th style="width:36px">#</th><th>คนขับ</th><th style="text-align:right">ครั้ง</th><th style="text-align:right">ค่าน้ำมัน (฿)</th><th style="text-align:right">ลิตรรวม</th><th style="text-align:right">ระยะทาง</th><th style="text-align:right">ชม.</th><th style="min-width:150px">เฉลี่ย km/L</th></tr></thead>
      <tbody>
        @foreach($driverSummary as $i=>$ds)
        @php $avgKml=count($ds['kml_values'])>0?array_sum($ds['kml_values'])/count($ds['kml_values']):0;$kmlPct=$maxKmlForBar>0?min(100,($avgKml/$maxKmlForBar)*100):0;$rankClass=$i===0?'gold':($i===1?'silver':($i===2?'bronze':'')); @endphp
        <tr>
          <td><span class="rank-badge {{ $rankClass }}">{{ $i+1 }}</span></td>
          <td><strong>{{ $ds['driver'] }}</strong></td>
          <td style="text-align:right;color:var(--text2)">{{ $ds['records'] }}</td>
          <td style="text-align:right;font-weight:700;color:var(--navy)">฿{{ number_format($ds['total_price'],0) }}</td>
          <td style="text-align:right;color:var(--text2)">{{ $ds['total_liters']>0?number_format($ds['total_liters'],1).' ล.':'—' }}</td>
          <td style="text-align:right;color:var(--text2)">{{ $ds['total_dist']>0?number_format($ds['total_dist'],0):'—' }}</td>
          <td style="text-align:right;color:var(--accent3);font-weight:600">{{ $ds['work_hours']>0?number_format($ds['work_hours'],1):'—' }}</td>
          <td>@if($avgKml>0)<div class="kml-bar-wrap"><div class="kml-bar-bg"><div class="kml-bar-fill" style="width:{{ $kmlPct }}%"></div></div><span class="km-val" style="font-size:12px">{{ number_format($avgKml,1) }}</span></div>@else<span style="color:var(--text3);font-size:12px">—</span>@endif</td>
        </tr>
        @endforeach
        <tr><td></td><td><strong>รวมทั้งหมด</strong></td><td style="text-align:right">{{ $grandTotal['records'] }}</td><td style="text-align:right;color:var(--navy)">฿{{ number_format($grandTotal['price'],0) }}</td><td style="text-align:right">{{ $grandTotal['liters']>0?number_format($grandTotal['liters'],1).' ล.':'—' }}</td><td style="text-align:right">{{ $grandTotal['dist']>0?number_format($grandTotal['dist'],0):'—' }}</td><td style="text-align:right">{{ $grandTotal['hours']>0?number_format($grandTotal['hours'],1):'—' }}</td><td></td></tr>
      </tbody>
    </table></div>
  </div>
</div>{{-- end pageReport --}}
</main>
</div>

{{-- STEP 1 MODAL --}}
<div class="modal-overlay" id="step1Modal">
  <div class="modal" style="max-width:540px">
    <div class="modal-header"><div class="modal-title">🚗 ข้อมูลคนขับรถ</div><button type="button" class="modal-close" onclick="closeAllModals()">✕</button></div>
    <div class="modal-body">
      <div class="step-indicator">
        <div class="step-item"><div class="step-circle active">1</div><div class="step-label active">ข้อมูลคนขับ</div></div>
        <div class="step-line inactive"></div>
        <div class="step-item"><div class="step-circle inactive">2</div><div class="step-label inactive">ข้อมูลน้ำมัน</div></div>
      </div>
      <div class="driver-banner" id="driverBanner" style="display:none">
        <div class="driver-avatar">👤</div>
        <div><div class="driver-banner-name" id="bannerName">—</div><div class="driver-banner-plate" id="bannerPlate">ทะเบียน: —</div></div>
      </div>
      <div class="driver-card-grid">
        @php
          $driverList=['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
          foreach($drivers as $dbD){if(!in_array($dbD,$driverList))$driverList[]=$dbD;}
          $plateList=['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
          foreach($plates as $dbP){if(!in_array($dbP,$plateList))$plateList[]=$dbP;}
        @endphp
        <div class="full"><label class="form-label">วันที่ทำงาน *</label><input type="date" id="s1-work-date" class="form-control" value="{{ date('Y-m-d') }}" onchange="onS1DateChange()"><div class="invalid-feedback" id="s1-err-date" style="display:none">กรุณาเลือกวันที่</div></div>
        <div>
          <label class="form-label">คนขับ *</label>
        <select id="s1-driver-select" class="form-control"
          onchange="onS1SelectOther(this,'s1-driver-name','s1-driver-other');updateDriverBanner();loadJobsForDriver()">
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
          <label class="form-label">เวลาทำงาน (เวลาไทย)</label>
          <div class="time-picker-row">
            <div><div style="font-size:11px;color:var(--text2);margin-bottom:5px;font-weight:600">เวลาเริ่ม</div><div class="time-select-wrap"><select id="s1-start-h" class="time-select" onchange="onTimeChange()"></select><span class="time-colon">:</span><select id="s1-start-m" class="time-select" onchange="onTimeChange()"></select><span style="font-size:11px;color:var(--text2);margin-left:4px">น.</span></div></div>
            <div class="time-arrow">→</div>
            <div><div style="font-size:11px;color:var(--text2);margin-bottom:5px;font-weight:600">เวลาสิ้นสุด</div><div class="time-select-wrap"><select id="s1-end-h" class="time-select" onchange="onTimeChange()"></select><span class="time-colon">:</span><select id="s1-end-m" class="time-select" onchange="onTimeChange()"></select><span style="font-size:11px;color:var(--text2);margin-left:4px">น.</span></div></div>
          </div>
          <div id="s1-wh-preview" style="margin-top:8px;font-size:12px;color:var(--accent3);font-weight:600;display:none">⏱ <span id="s1-wh-val">0</span> ชั่วโมง</div>
        </div>
      </div>
      <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px"><div style="font-size:13px;font-weight:600;color:var(--text2)">📋 รายการงานของคนขับ</div><span id="jobDateChip" class="job-date-chip" style="display:none"></span></div>
        <div id="jobTableWrap"><div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div></div>
      </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeAllModals()">ยกเลิก</button><button type="button" class="btn btn-primary" onclick="goToStep2()">ถัดไป — เพิ่มข้อมูลน้ำมัน →</button></div>
  </div>
</div>

{{-- STEP 2 MODAL --}}
<div class="modal-overlay" id="fuelModal">
  <div class="modal">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px"><button type="button" id="backBtn" onclick="backToStep1()" style="width:30px;height:30px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center">←</button><div class="modal-title" id="modalTitle">เพิ่มข้อมูลเติมน้ำมัน</div></div>
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
            <div style="font-size:12px;opacity:.7" id="oilPriceLabel">ราคาน้ำมันดีเซล (PTT)</div>
            <div style="display:flex;align-items:baseline;gap:4px;margin:4px 0"><span style="font-size:24px;font-weight:700" id="oilPriceShow">—</span><span style="font-size:12px;opacity:.7;margin-left:4px">บาท/ลิตร</span><div style="margin-left:auto;display:flex;align-items:center;gap:6px"><div class="live-dot loading" id="liveDot"></div><span style="font-size:12px;opacity:.7" id="liveLabel">กำลังดึง</span></div></div>
            <div style="font-size:12px;opacity:.6;font-style:italic;margin-bottom:8px" id="oilPriceStatus">กำลังโหลด...</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
            @php $oilBtns=['diesel'=>'⛽ ดีเซล','95'=>'⛽ 95','benzin95'=>'⛽ เบนซิน 95','91'=>'⛽ 91','e20'=>'⛽ E20','e85'=>'⛽ E85']; @endphp
            @foreach($oilBtns as $oilKey=>$oilLabel)
            <button type="button" onclick="switchOilType('{{ $oilKey }}')" id="btnOil-{{ $oilKey }}" class="oil-btn" style="font-family:'Sarabun',sans-serif;font-size:11px;font-weight:600;padding:5px 11px;border-radius:14px;cursor:pointer;border:2px solid {{ $oilKey==='diesel'?'#fff':'transparent' }};background:{{ $oilKey==='diesel'?'rgba(255,255,255,.3)':'rgba(255,255,255,.1)' }};color:{{ $oilKey==='diesel'?'#fff':'rgba(255,255,255,.7)' }}">{{ $oilLabel }}</button>
            @endforeach
            <button type="button" onclick="refreshOilPrice()" id="btnRefreshOil" style="font-family:'Sarabun',sans-serif;font-size:11px;padding:5px 11px;border-radius:14px;border:2px solid rgba(255,255,255,.3);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);cursor:pointer">🔄</button>
            </div>
          </div>
        </div>
        <div class="form-grid">
          <div class="section-divider">⛽ ข้อมูลน้ำมัน</div>
          <div class="full"><label class="form-label">ค่าน้ำมัน (฿) *</label><input type="number" name="total_price" id="f-total-price" class="form-control {{ $errors->has('total_price')?'is-invalid':'' }}" step="0.01" value="{{ old('total_price',$editLog['total_price']??'') }}" placeholder="กรอกยอดเงิน เช่น 500" oninput="calcPreview()">@error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
           <div class="full"><label class="form-label">ระยะทางทั้งหมด (km)</label><input type="number" name="total_distance" id="f-total-distance" class="form-control" value="{{ old('total_distance',$editLog['total_distance']??'') }}" oninput="calcPreview()"></div>
        </div>
          <div><label class="form-label">จำนวนลิตร</label><input type="number" name="liters" id="f-liters" class="form-control auto-calc" step="0.01" value="{{ old('liters',$editLog['liters']??'') }}" readonly><div class="auto-hint"></div></div>
          <div><label class="form-label">ราคาต่อลิตร (฿)</label><input type="number" name="price_per_liter" id="f-price-per-liter" class="form-control auto-calc" step="0.01" value="{{ old('price_per_liter',$editLog['price_per_liter']??'') }}" readonly><div class="auto-hint"></div></div>
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
      <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="backToStep1()" id="backBtnFooter">← ย้อนกลับ</button><button type="submit" class="btn btn-primary">💾 บันทึกข้อมูล</button></div>
    </form>
  </div>
</div>
<script>
const COLORS=['#4f8ef7','#38c98a','#f5a623','#e85d5d','#a855f7','#06b6d4','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
const COLORS_FADE=COLORS.map(c=>c+'55');
const ROUTE_STORE    = '{{ route("oil") }}';
const ROUTE_UPDATE   = id => `{{ url("/oil/update") }}/${id}`;
const ROUTE_PREVMILE = '{{ route("oil.prevMileage") }}';
const ROUTE_SYNC_NG  = '{{ route("oil.syncNg") }}';
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const TZ = 'Asia/Bangkok';
let currentOilType = 'diesel', isEditMode = false, editId = null, reportChartsInited = false;

/* ══════════════════════════════════════════════════════════════════
   DELIVERY CHART
══════════════════════════════════════════════════════════════════ */
const MONTH_LABELS = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
let dlvView = 'day', dlvChart = null, dlvDriver = 'all';

@php
  $deliveryByDriver = [];
  foreach ($logs as $log) {
      $driver  = $log['driver_name'] ?? 'ไม่ระบุ';
      $date    = $log['work_date']   ?? '';
      $monIdx  = $date ? ((int) substr($date, 5, 2)) - 1 : -1;
      $quarter = $monIdx >= 0 ? (int) floor($monIdx / 3) : -1;
      if (!isset($deliveryByDriver[$driver])) {
          $deliveryByDriver[$driver] = [
              'days'     => [],
              'months'   => array_fill(0, 12, ['s' => 0, 'f' => 0]),
              'quarters' => array_fill(0, 4,  ['s' => 0, 'f' => 0]),
              'all'      => ['s' => 0, 'f' => 0],
          ];
      }
      $success = (int) ($log['delivery_success'] ?? $log['success_count'] ?? $log['ok_count']  ?? 0);
      $fail    = (int) ($log['delivery_fail']    ?? $log['fail_count']    ?? $log['ng_count']   ?? 0);
      if ($date) {
          if (!isset($deliveryByDriver[$driver]['days'][$date]))
              $deliveryByDriver[$driver]['days'][$date] = ['s' => 0, 'f' => 0];
          $deliveryByDriver[$driver]['days'][$date]['s'] += $success;
          $deliveryByDriver[$driver]['days'][$date]['f'] += $fail;
      }
      if ($monIdx >= 0 && $monIdx < 12) {
          $deliveryByDriver[$driver]['months'][$monIdx]['s'] += $success;
          $deliveryByDriver[$driver]['months'][$monIdx]['f'] += $fail;
      }
      if ($quarter >= 0 && $quarter < 4) {
          $deliveryByDriver[$driver]['quarters'][$quarter]['s'] += $success;
          $deliveryByDriver[$driver]['quarters'][$quarter]['f'] += $fail;
      }
      $deliveryByDriver[$driver]['all']['s'] += $success;
      $deliveryByDriver[$driver]['all']['f'] += $fail;
  }
@endphp
const DLV_BY_DRIVER = @json($deliveryByDriver);

function initDlvDriverSel() {
  const sel = document.getElementById('dlvDriverSel');
  while (sel.options.length > 1) sel.remove(1);
  Object.keys(DLV_BY_DRIVER).forEach(d => {
    const o = document.createElement('option'); o.value = d; o.textContent = d; sel.appendChild(o);
  });
}
function setDlv(v, btn) {
  dlvView = v;
  document.querySelectorAll('#dlvTabs .view-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('dlvYearSel').style.display = v === 'year' ? 'block' : 'none';
  renderDlv();
}
function renderDlv() {
  const drivers = Object.keys(DLV_BY_DRIVER);
  const yr = parseInt(document.getElementById('dlvYearSel')?.value || new Date().getFullYear());
  let labels = [], datasets = [];
  const tf = { size: 11 };
  if (dlvDriver !== 'all') {
    const data = DLV_BY_DRIVER[dlvDriver];
    if (!data) { if (dlvChart) dlvChart.destroy(); return; }
    let success = [], fail = [];
    if (dlvView === 'day') {
      const sd = Object.keys(data.days || {}).sort();
      labels = sd.map(d => d.slice(5)); success = sd.map(d => data.days[d].s); fail = sd.map(d => data.days[d].f);
    } else if (dlvView === 'month') {
      labels = MONTH_LABELS; success = (data.months||[]).map(m=>m.s); fail = (data.months||[]).map(m=>m.f);
    } else if (dlvView === 'year') {
      labels = ['Q1','Q2','Q3','Q4'].map(q=>`${q} ${yr}`); success = (data.quarters||[]).map(q=>q.s); fail = (data.quarters||[]).map(q=>q.f);
    } else { labels = ['ทั้งหมด']; success = [data.all?.s||0]; fail = [data.all?.f||0]; }
    datasets = [
      { label:'ส่งสำเร็จ',    data:success, backgroundColor:'#38c98a', borderRadius:5, borderSkipped:false, stack:'s' },
      { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:'#e85d5d', borderRadius:5, borderSkipped:false, stack:'s' },
    ];
  } else {
    if (dlvView === 'day') {
      const allDates = [...new Set(drivers.flatMap(d=>Object.keys(DLV_BY_DRIVER[d].days||{})))].sort();
      labels = allDates.map(d=>d.slice(5));
      datasets = drivers.flatMap((d,i)=>{ const col=COLORS[i%COLORS.length]; return [
        { label:`${d} ✓`, data:allDates.map(dt=>DLV_BY_DRIVER[d].days?.[dt]?.s||0), backgroundColor:col,      borderRadius:4, borderSkipped:false, stack:d },
        { label:`${d} ✗`, data:allDates.map(dt=>DLV_BY_DRIVER[d].days?.[dt]?.f||0), backgroundColor:col+'77', borderRadius:4, borderSkipped:false, stack:d },
      ]; });
    } else if (dlvView === 'month') {
      labels = MONTH_LABELS;
      datasets = drivers.flatMap((d,i)=>{ const col=COLORS[i%COLORS.length]; const months=DLV_BY_DRIVER[d].months||[]; return [
        { label:`${d} ✓`, data:months.map(m=>m.s), backgroundColor:col,      borderRadius:4, borderSkipped:false, stack:d },
        { label:`${d} ✗`, data:months.map(m=>m.f), backgroundColor:col+'77', borderRadius:4, borderSkipped:false, stack:d },
      ]; });
    } else if (dlvView === 'year') {
      labels = ['Q1','Q2','Q3','Q4'].map(q=>`${q} ${yr}`);
      datasets = drivers.flatMap((d,i)=>{ const col=COLORS[i%COLORS.length]; const qs=DLV_BY_DRIVER[d].quarters||[]; return [
        { label:`${d} ✓`, data:qs.map(q=>q.s), backgroundColor:col,      borderRadius:4, borderSkipped:false, stack:d },
        { label:`${d} ✗`, data:qs.map(q=>q.f), backgroundColor:col+'77', borderRadius:4, borderSkipped:false, stack:d },
      ]; });
    } else {
      labels = drivers;
      datasets = [
        { label:'ส่งสำเร็จ',    data:drivers.map(d=>DLV_BY_DRIVER[d].all?.s||0), backgroundColor:drivers.map((_,i)=>COLORS[i%COLORS.length]),      borderRadius:5, borderSkipped:false, stack:'ok' },
        { label:'ส่งไม่สำเร็จ', data:drivers.map(d=>DLV_BY_DRIVER[d].all?.f||0), backgroundColor:drivers.map((_,i)=>COLORS[i%COLORS.length]+'77'), borderRadius:5, borderSkipped:false, stack:'ok' },
      ];
    }
  }
  if (dlvChart) dlvChart.destroy();
  dlvChart = new Chart(document.getElementById('deliveryChart'), {
    type:'bar', data:{labels,datasets},
    options:{
      responsive:true, maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{
        label:ctx=>`${ctx.dataset.label}: ${ctx.raw} รายการ`,
        footer:items=>'รวม: '+items.reduce((s,i)=>s+i.raw,0)+' รายการ',
      }}},
      scales:{
        x:{stacked:true,ticks:{font:tf},grid:{color:'rgba(0,0,0,.04)'}},
        y:{stacked:true,beginAtZero:true,ticks:{font:tf,callback:v=>v+' รายการ'},grid:{color:'rgba(0,0,0,.04)'}},
      },
    },
  });
  buildDlvLegend(datasets);
}
function buildDlvLegend(datasets) {
  const wrap = document.getElementById('dlvLegend'); if (!wrap) return;
  const seen = new Set();
  const items = datasets.filter(ds=>{ if(seen.has(ds.label))return false; seen.add(ds.label); return true; });
  wrap.innerHTML = items.map(ds=>{
    const color = Array.isArray(ds.backgroundColor)?ds.backgroundColor[0]:ds.backgroundColor;
    return `<span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)">
      <span style="width:10px;height:10px;border-radius:2px;background:${color};display:inline-block;flex-shrink:0"></span>${ds.label}</span>`;
  }).join('');
}

/* ══════════════════════════════════════════════════════════════════
   PAGE SWITCH / NAV DATE / TABLE FILTER
══════════════════════════════════════════════════════════════════ */
function switchPage(p){
  ['pageTracking','pageReport'].forEach(id=>document.getElementById(id).style.display='none');
  ['navOil','navReport'].forEach(id=>document.getElementById(id).classList.remove('active'));
  if(p==='tracking'){document.getElementById('pageTracking').style.display='block';document.getElementById('navOil').classList.add('active');}
  else if(p==='report'){document.getElementById('pageReport').style.display='block';document.getElementById('navReport').classList.add('active');if(!reportChartsInited){initReportCharts();reportChartsInited=true;}}
}
function nowThai(){return new Date(new Date().toLocaleString('en-US',{timeZone:TZ}));}
function todayStr(){const d=nowThai();return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
function updateNavDate(){
  const now=new Date();const opts={timeZone:TZ,weekday:'long',year:'numeric',month:'long',day:'numeric'};
  const parts=new Intl.DateTimeFormat('th-TH-u-ca-buddhist',opts).formatToParts(now);
  const map={};parts.forEach(p=>map[p.type]=p.value);
  const el=document.getElementById('navDate');if(el)el.textContent=`${map.day} ${map.month} ${map.year}`;
}
function filterOilTable(q){
  const rows=document.querySelectorAll('#oilTbody tr[data-driver]');let v=0;
  rows.forEach(r=>{const m=r.dataset.driver.includes(q.toLowerCase());r.style.display=m?'':'none';if(m)v++;});
  const c=document.getElementById('oilCount');if(c)c.textContent=v;
}

/* ══════════════════════════════════════════════════════════════════
   TIME DROPDOWNS
══════════════════════════════════════════════════════════════════ */
function buildTimeDropdowns(){
  const hours=['',...Array.from({length:24},(_,i)=>String(i).padStart(2,'0'))];
  const mins =['',...Array.from({length:60},(_,i)=>String(i).padStart(2,'0'))];
  ['s1-start-h','s1-end-h'].forEach(id=>{document.getElementById(id).innerHTML=hours.map(h=>`<option value="${h}">${h}</option>`).join('');});
  ['s1-start-m','s1-end-m'].forEach(id=>{document.getElementById(id).innerHTML=mins.map(m=>`<option value="${m}">${m}</option>`).join('');});
}
function getTimeVal(hId,mId){const h=document.getElementById(hId).value,m=document.getElementById(mId).value;return(h===''||m==='')?'':h+':'+m;}
function setTimeDropdown(hId,mId,t){if(!t){document.getElementById(hId).value='00';document.getElementById(mId).value='00';return;}const p=t.split(':');document.getElementById(hId).value=p[0]||'--';document.getElementById(mId).value=p[1]||'--';}
function onTimeChange(){updateDriverBanner();}
function onS1SelectOther(sel,hid,tid){
  const v=sel.value,t=document.getElementById(tid),h=document.getElementById(hid);
  if(v==='__other__'){t.style.display='block';t.focus();h.value=t.value;}
  else{t.style.display='none';t.value='';h.value=v;}
}
function updateDriverBanner(){
  const name =document.getElementById('s1-driver-name').value||document.getElementById('s1-driver-select').value;
  const plate=document.getElementById('s1-vehicle-id').value ||document.getElementById('s1-plate-select').value;
  const sT=getTimeVal('s1-start-h','s1-start-m'),eT=getTimeVal('s1-end-h','s1-end-m');
  const banner=document.getElementById('driverBanner');
  if(name&&name!=='__other__'&&plate&&plate!=='__other__'){
    document.getElementById('bannerName').textContent=name;
    document.getElementById('bannerPlate').textContent='ทะเบียน: '+plate;
    banner.style.display='flex';
  } else banner.style.display='none';
  const wp=document.getElementById('s1-wh-preview');
  if(sT&&eT){const[sh,sm]=sT.split(':').map(Number),[eh,em]=eT.split(':').map(Number),d=(eh*60+em)-(sh*60+sm);if(d>0){document.getElementById('s1-wh-val').textContent=(d/60).toFixed(2);wp.style.display='block';return;}}
  wp.style.display='none';
}

/* ══════════════════════════════════════════════════════════════════
   JOB API — cache + driver dropdown + แสดง status จาก API
══════════════════════════════════════════════════════════════════ */
const JOB_API_BASE = 'https://script.google.com/macros/s/AKfycbyL82yRPXR1eiHOnaJqZ5Q0y1VnOZGAPXW2jyEB3NUEWWfJBBhMKosWYxf_363jnmAcHw/exec';
const jobApiCache = {};   // { "YYYY-MM-DD": Array | null }

/* ── fetch + cache ─────────────────────────────────────────────── */
async function fetchJobsByDate(dateStr) {
  if (jobApiCache[dateStr] !== undefined) return;
  jobApiCache[dateStr] = null;
  try {
    const res  = await fetch(`${JOB_API_BASE}?date=${dateStr}`);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();
    jobApiCache[dateStr] = Array.isArray(json) ? json
      : (json.drivers !== undefined ? [json] : []);
  } catch(e) {
    console.warn('fetchJobsByDate:', e);
    jobApiCache[dateStr] = [];
  }
}

/* ── populate driver dropdown ──────────────────────────────────── */
// DB drivers จาก Blade (fallback)
const DB_DRIVERS = @json($drivers);

function populateDriverDropdown(dateStr) {
  const sel   = document.getElementById('s1-driver-select');
  const hidEl = document.getElementById('s1-driver-name');
  const dayBlocks = jobApiCache[dateStr] || [];

  // คนขับที่วิ่งวันนั้นจาก API
  const apiDrivers = [];
  dayBlocks.forEach(day => {
    (day.drivers || []).forEach(d => {
      if (d.driver_name && !apiDrivers.includes(d.driver_name))
        apiDrivers.push(d.driver_name);
    });
  });

  const prevVal = hidEl.value || sel.value;
  sel.innerHTML = '<option value="">— เลือกคนขับ —</option>';

  if (apiDrivers.length > 0) {
    // คนขับจาก API (flat — ไม่มีหัวกลุ่ม)
    apiDrivers.forEach(d => {
      const o = document.createElement('option'); o.value = d; o.textContent = d;
      sel.appendChild(o);
    });

    // คนขับ DB ที่ไม่อยู่ใน API
    const rest = DB_DRIVERS.filter(d => !apiDrivers.includes(d));
    if (rest.length) {
      const grpDb = document.createElement('optgroup');
      grpDb.label = '📋 คนขับอื่นๆ';
      rest.forEach(d => {
        const o = document.createElement('option'); o.value = d; o.textContent = d;
        grpDb.appendChild(o);
      });
      sel.appendChild(grpDb);
    }
  } else {
    // ไม่มีข้อมูล API — ใช้ DB ทั้งหมด
    DB_DRIVERS.forEach(d => {
      const o = document.createElement('option'); o.value = d; o.textContent = d;
      sel.appendChild(o);
    });
  }

  // option อื่นๆ
  const oo = document.createElement('option'); oo.value = '__other__'; oo.textContent = 'อื่นๆ (พิมพ์เอง)';
  sel.appendChild(oo);

  // คืนค่าเดิม
  if (prevVal && prevVal !== '__other__') {
    const found = [...sel.options].find(o => o.value === prevVal);
    if (found) { sel.value = prevVal; hidEl.value = prevVal; }
    else        { sel.value = ''; hidEl.value = ''; }
  }
}

/* ── onDateChange: fetch + repopulate driver + clear jobs ──────── */
async function onS1DateChange() {
  updateDriverBanner();
  const date = document.getElementById('s1-work-date').value;
  if (!date) return;

  // loading state
  const sel = document.getElementById('s1-driver-select');
  sel.innerHTML = '<option value="">⏳ กำลังโหลด...</option>';
  sel.disabled  = true;
  document.getElementById('s1-driver-name').value = '';
  document.getElementById('jobTableWrap').innerHTML = '<div class="job-loading">⏳ กำลังโหลดข้อมูลคนขับ...</div>';
  document.getElementById('jobDateChip').style.display = 'none';

  await fetchJobsByDate(date);

  sel.disabled = false;
  populateDriverDropdown(date);
  updateDriverBanner();
  document.getElementById('jobTableWrap').innerHTML = '<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';
}

/* ── loadJobsForDriver: trigger เมื่อเลือก driver ─────────────── */
async function loadJobsForDriver() {
  const name = document.getElementById('s1-driver-name').value
            || document.getElementById('s1-driver-select').value;
  const date = document.getElementById('s1-work-date').value;
  const wrap = document.getElementById('jobTableWrap');
  const chip = document.getElementById('jobDateChip');

  if (!name || name === '__other__' || !name.trim()) {
    wrap.innerHTML = '<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';
    chip.style.display = 'none'; return;
  }
  if (!date) {
    wrap.innerHTML = '<div class="job-loading">กรุณาเลือกวันที่ก่อน</div>';
    chip.style.display = 'none'; return;
  }

  wrap.innerHTML = '<div class="job-loading">⏳ กำลังโหลดรายการงาน...</div>';
  chip.style.display = 'none';

  await fetchJobsByDate(date);

  const jobs = _collectJobs(name, date);

  chip.textContent = 'วันที่ ' + date; chip.style.display = '';

  if (!jobs.length) {
    wrap.innerHTML = `<div class="job-loading">ไม่พบรายการงานของ ${name} วันที่ ${date}</div>`;
    return;
  }
  renderJobTable(jobs);
}

/* ── render job table — display only (status จาก API) ─────────── */
function renderJobTable(jobs) {
  const wrap = document.getElementById('jobTableWrap');

  const rows = jobs.map(j => {
    const raw  = (j.status || '').trim();
    const isOk = raw.includes('สำเร็จ') && !raw.includes('ไม่');
    const isNg = raw.includes('ไม่สำเร็จ') || raw.toLowerCase() === 'ng' || raw.toLowerCase() === 'fail';

    const statusBadge = isOk
      ? `<span class="job-chip ok" style="display:inline-flex">✅ สำเร็จ</span>`
      : isNg
        ? `<span class="job-chip fail" style="display:inline-flex">❌ ไม่สำเร็จ</span>`
        : `<span class="job-chip" style="display:inline-flex;color:var(--text3)">— รอ</span>`;

    const noteHtml = (j.note && j.note.trim())
      ? `<div style="font-size:11px;color:var(--text3);margin-top:3px">${j.note}</div>` : '';

    return `<tr>
      <td><span class="job-bill">${j.bill_no}</span></td>
      <td>${j.customer_name}</td>
      <td style="color:var(--text2)">${j.seller_name}</td>
      <td>${statusBadge}${noteHtml}</td>
    </tr>`;
  }).join('');

  const total   = jobs.length;
  const okCount = jobs.filter(j => { const r=(j.status||'').trim(); return r.includes('สำเร็จ')&&!r.includes('ไม่'); }).length;
  const ngCount = jobs.filter(j => { const r=(j.status||'').trim(); return r.includes('ไม่สำเร็จ')||r.toLowerCase()==='ng'; }).length;
  const pending = total - okCount - ngCount;

  wrap.innerHTML = `
    <div class="job-table-wrap">
      <table>
        <thead><tr>
          <th style="width:130px">เลขบิล</th><th>ลูกค้า</th>
          <th style="width:85px">เซลล์</th><th style="width:120px">สถานะ</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>
      <div class="job-summary-bar">
        <span class="job-chip">ทั้งหมด ${total}</span>
        ${okCount ? `<span class="job-chip ok">✅ สำเร็จ ${okCount}</span>` : ''}
        ${ngCount ? `<span class="job-chip fail">❌ ไม่สำเร็จ ${ngCount}</span>` : ''}
        ${pending ? `<span class="job-chip">⏳ รอ ${pending}</span>` : ''}
      </div>
    </div>`;
}

function _collectJobs(driverName, date) {
  const jobs = [];
  (jobApiCache[date] || []).forEach(day => {
    (day.drivers || []).forEach(d => {
      if (d.driver_name !== driverName) return;
      (d.jobs || []).forEach((j, i) => {
        jobs.push({ ...j, _key: `${driverName}_${day.date}_${i}`, _date: day.date });
      });
    });
  });
  return jobs;
}

/* ── sync NG ───────────────────────────────────────────────────── */
/* ══════════════════════════════════════════════════════════════════
   MODAL FLOW
══════════════════════════════════════════════════════════════════ */
function goToStep2(){
  let ok=true;
  const date  =document.getElementById('s1-work-date').value;
  const driver=document.getElementById('s1-driver-name').value;
  const plate =document.getElementById('s1-vehicle-id').value;
  ['s1-err-date','s1-err-driver','s1-err-plate'].forEach(id=>document.getElementById(id).style.display='none');
  ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id=>document.getElementById(id).classList.remove('is-invalid'));
  if(!date)                       {document.getElementById('s1-err-date').style.display='block';  document.getElementById('s1-work-date').classList.add('is-invalid');   ok=false;}
  if(!driver||driver==='__other__'){document.getElementById('s1-err-driver').style.display='block'; document.getElementById('s1-driver-select').classList.add('is-invalid'); ok=false;}
  if(!plate ||plate ==='__other__'){document.getElementById('s1-err-plate').style.display='block';  document.getElementById('s1-plate-select').classList.add('is-invalid');  ok=false;}
  if(!ok)return;

  // ── นับ ok/ng จาก job list + auto sync NG → DB ──────────────────────────
  const jobs = _collectJobs(driver, date);
  let okCount = 0, ngCount = 0;
  const ngJobs = [], okBillNos = [];

  jobs.forEach(j => {
    const raw  = (j.status || '').trim();
    const isOk = raw.includes('สำเร็จ') && !raw.includes('ไม่');
    const isNg = raw.includes('ไม่สำเร็จ') || raw.toLowerCase() === 'ng';
    if (isOk) { okCount++; okBillNos.push(j.bill_no); }
    else if (isNg) { ngCount++; ngJobs.push(j); }
  });

  setF('f-ok', okCount);
  setF('f-ng', ngCount);

  // บันทึก NG อัตโนมัติ (fire-and-forget ไม่ block UI)
  if (jobs.length > 0) {
    fetch(ROUTE_SYNC_NG, {
      method  : 'POST',
      headers : { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
      body    : JSON.stringify({
        date,
        jobs: jobs.map(j => ({
          bill_no      : j.bill_no,
          driver_name  : driver,
          seller_name  : j.seller_name   || '',
          customer_name: j.customer_name || '',
          status       : (j.status || '').trim(),
          note         : j.note || '',
        })),
      }),
    }).catch(e => console.warn('syncNg error:', e));
  }

  const sT=getTimeVal('s1-start-h','s1-start-m'),eT=getTimeVal('s1-end-h','s1-end-m');
  setF('f-work-date',date);setF('f-driver-name',driver);setF('f-vehicle-id',plate);
  setF('f-start-time',sT);setF('f-end-time',eT);
  document.getElementById('chipDriver').textContent=driver;
  document.getElementById('chipPlate').textContent=plate;
  document.getElementById('chipDate').textContent=date;
  if(sT&&eT){document.getElementById('chipTime').textContent=sT+' – '+eT+' น.';document.getElementById('chipTimeWrap').style.display='flex';}
  else document.getElementById('chipTimeWrap').style.display='none';
  document.getElementById('summaryRow').style.display='flex';
  document.getElementById('step1Modal').classList.remove('open');
  document.getElementById('fuelModal').classList.add('open');
  loadOilPrice('diesel');calcPreview();fetchPrevMileage(plate,date);
}
function backToStep1(){document.getElementById('fuelModal').classList.remove('open');document.getElementById('step1Modal').classList.add('open');}

function openModal(id=null){
  isEditMode=!!id; editId=id;
  if(id){
    const allLogs=@json($logs);const r=allLogs.find(l=>l.id===id);if(!r)return;
    document.getElementById('modalTitle').textContent='แก้ไขข้อมูลเติมน้ำมัน';
    document.getElementById('fuelForm').action=ROUTE_UPDATE(id);
    document.getElementById('formMethod').value='PUT';
    setF('f-work-date',r.work_date);setF('f-driver-name',r.driver_name);setF('f-vehicle-id',r.vehicle_id);
    setF('f-start-time',r.start_time);setF('f-end-time',r.end_time);
    setF('f-liters',r.liters);setF('f-total-distance',r.total_distance);
    setF('f-total-price',r.total_price);setF('f-note',r.note);setF('f-price-per-liter',r.price_per_liter);
    document.getElementById('chipDriver').textContent=r.driver_name??'—';
    document.getElementById('chipPlate').textContent=r.vehicle_id??'—';
    document.getElementById('chipDate').textContent=r.work_date??'—';
    if(r.start_time&&r.end_time){document.getElementById('chipTime').textContent=r.start_time+' – '+r.end_time+' น.';document.getElementById('chipTimeWrap').style.display='flex';}
    else document.getElementById('chipTimeWrap').style.display='none';
    document.getElementById('summaryRow').style.display='flex';
    document.getElementById('backBtn').style.display='none';
    document.getElementById('backBtnFooter').style.display='none';
    document.getElementById('fuelModal').classList.add('open');
    loadOilPrice('diesel');fetchPrevMileage(r.vehicle_id,r.work_date,id);calcPreview();
  } else {
    // ── new record ──
    document.getElementById('modalTitle').textContent='เพิ่มข้อมูลเติมน้ำมัน';
    document.getElementById('fuelForm').action=ROUTE_STORE;
    document.getElementById('formMethod').value='';
    document.getElementById('backBtn').style.display='';
    document.getElementById('backBtnFooter').style.display='';
    // reset
    ['s1-driver-name','s1-vehicle-id'].forEach(i=>{const el=document.getElementById(i);if(el)el.value='';});
    ['s1-driver-other','s1-plate-other'].forEach(i=>{const el=document.getElementById(i);if(el){el.style.display='none';el.value='';}});
    document.getElementById('s1-plate-select').value='';
    setTimeDropdown('s1-start-h','s1-start-m','');setTimeDropdown('s1-end-h','s1-end-m','');
    document.getElementById('driverBanner').style.display='none';
    document.getElementById('s1-wh-preview').style.display='none';
    ['f-liters','f-total-price','f-total-distance','f-note','f-price-per-liter'].forEach(i=>setF(i,''));
    document.getElementById('calcBox').style.display='none';
    ['s1-err-date','s1-err-driver','s1-err-plate'].forEach(id=>document.getElementById(id).style.display='none');
    ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id=>document.getElementById(id).classList.remove('is-invalid'));
    document.getElementById('jobDateChip').style.display='none';

    const today = todayStr();
    document.getElementById('s1-work-date').value = today;

    // ── fetch API ทันที แล้ว populate driver dropdown ──
    (async () => {
      const sel = document.getElementById('s1-driver-select');
      sel.innerHTML = '<option value="">⏳ กำลังโหลดรายชื่อ...</option>';
      sel.disabled  = true;
      document.getElementById('jobTableWrap').innerHTML = '<div class="job-loading">⏳ กำลังโหลดข้อมูลคนขับ...</div>';

      await fetchJobsByDate(today);

      sel.disabled = false;
      populateDriverDropdown(today);
      document.getElementById('jobTableWrap').innerHTML = '<div class="job-loading">เลือกคนขับเพื่อดูรายการงาน</div>';
    })();

    document.getElementById('step1Modal').classList.add('open');
  }
}

function closeAllModals(){document.getElementById('step1Modal').classList.remove('open');document.getElementById('fuelModal').classList.remove('open');}
function setF(id,v){const el=document.getElementById(id);if(el)el.value=v??'';}

/* ══════════════════════════════════════════════════════════════════
   PREV MILEAGE + CALC PREVIEW
══════════════════════════════════════════════════════════════════ */
async function fetchPrevMileage(vid,wd,xid=null){
  try{
    const p=new URLSearchParams({vehicle_id:vid,work_date:wd});if(xid)p.set('exclude_id',xid);
    const r=await fetch(`${ROUTE_PREVMILE}?${p}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    const d=await r.json();const el=document.getElementById('prevMileageInfo');
    if(el&&d.data){document.getElementById('prevMileageText').innerHTML=`🔖 Log ก่อนหน้า: <strong>${d.data.work_date}</strong>`;el.style.display='block';}
    else if(el)el.style.display='none';
  }catch(_){}
  calcPreview();
}
function calcPreview(){
  const sT=document.getElementById('f-start-time')?.value??'';const eT=document.getElementById('f-end-time')?.value??'';
  const ppl=parseFloat(document.getElementById('f-price-per-liter')?.value)||0;const tp=parseFloat(document.getElementById('f-total-price')?.value)||0;
  if(tp>0&&ppl>0)setF('f-liters',(tp/ppl).toFixed(2));
  const liters=parseFloat(document.getElementById('f-liters')?.value)||0;let wh=0;
  if(sT&&eT){const[sh,sm]=sT.split(':').map(Number),[eh,em]=eT.split(':').map(Number),d=(eh*60+em)-(sh*60+sm);if(d>0)wh=d/60;}
  const show=wh>0||liters>0||tp>0;document.getElementById('calcBox').style.display=show?'block':'none';
  if(show){
    document.getElementById('calcWorkHours').textContent=wh>0?wh.toFixed(2):'—';
    document.getElementById('calcLitersPreview').textContent=liters>0?`${liters} / ฿${tp.toFixed(0)}`:'—';
    const dist=parseFloat(document.getElementById('f-total-distance')?.value)||0;
    document.getElementById('calcKml').textContent=(liters>0&&dist>0)?(dist/liters).toFixed(2):'—';
  }
}

/* ══════════════════════════════════════════════════════════════════
   OIL PRICE
══════════════════════════════════════════════════════════════════ */
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
  document.getElementById('oilPriceLabel').textContent=`ราคาน้ำมัน${cfg.label}`;document.getElementById('oilPriceShow').textContent='...';document.getElementById('oilPriceStatus').textContent='⏳ กำลังดึง...';document.getElementById('liveDot').className='live-dot loading';document.getElementById('liveLabel').textContent='กำลังดึง';document.getElementById('f-price-per-liter').value='';
  let fetched=null;
  try{
    const r=await Promise.race([fetch('https://api.chnwt.dev/thai-oil-api/latest'),new Promise((_,rj)=>setTimeout(()=>rj(new Error('t')),8000))]);
    if(r.ok){const json=await r.json();const stations=json?.response?.stations;if(stations){const prices=[];for(const station of Object.values(stations))for(const fuel of Object.values(station))if(fuel?.name?.includes(cfg.matchName)&&fuel?.price){const n=parseFloat(fuel.price);if(!isNaN(n)&&n>0&&n<cfg.maxPrice)prices.push(n);}if(prices.length>0){const freq={};for(const p of prices){const k=p.toFixed(2);freq[k]=(freq[k]||0)+1;}fetched=parseFloat(Object.entries(freq).sort((a,b)=>b[1]-a[1])[0][0]);}}}
  }catch(_){}
  const now=new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
  if(fetched){document.getElementById('oilPriceShow').textContent=fetched.toFixed(2);document.getElementById('oilPriceStatus').textContent=`✅ ราคากลาง • ${now} น.`;document.getElementById('liveDot').className='live-dot';document.getElementById('liveDot').style.background='';document.getElementById('liveLabel').textContent='Live';document.getElementById('f-price-per-liter').value=fetched.toFixed(2);}
  else{document.getElementById('oilPriceShow').textContent='—';document.getElementById('oilPriceStatus').textContent=`❌ ดึงไม่ได้ • ${now} น.`;document.getElementById('liveDot').className='live-dot';document.getElementById('liveDot').style.background='var(--accent4)';document.getElementById('liveLabel').textContent='ไม่มีข้อมูล';document.getElementById('f-price-per-liter').value='';}
  calcPreview();
}

/* ══════════════════════════════════════════════════════════════════
   STATIC CHARTS
══════════════════════════════════════════════════════════════════ */
function initCharts(){
  const cbd=@json($costByDriver);const kbd=@json($kmlByDriver);
  const col=arr=>arr.map((_,i)=>COLORS[i%COLORS.length]);const g='rgba(0,0,0,.04)';const tf={size:11};
  if(cbd.length)new Chart(document.getElementById('chartDriver'),{type:'bar',data:{labels:cbd.map(d=>d.driver),datasets:[{data:cbd.map(d=>d.total_price),backgroundColor:col(cbd),borderRadius:5,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>'฿'+ctx.raw.toLocaleString()}}},scales:{y:{beginAtZero:true,ticks:{font:tf,callback:v=>'฿'+v.toLocaleString()},grid:{color:g}},x:{ticks:{font:tf}}}}});
  if(kbd.length)new Chart(document.getElementById('chartKml'),{type:'bar',data:{labels:kbd.map(d=>d.driver),datasets:[{data:kbd.map(d=>d.km_per_liter),backgroundColor:col(kbd),borderRadius:5,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>ctx.raw.toFixed(1)+' km/L'}}},scales:{y:{beginAtZero:true,ticks:{font:tf,callback:v=>v+' km/L'},grid:{color:g}},x:{ticks:{font:tf}}}}});
  initDlvDriverSel();renderDlv();
}

/* ══════════════════════════════════════════════════════════════════
   PIE CHARTS
══════════════════════════════════════════════════════════════════ */
function buildPieLegend(cid,labels,values,colors,unit=''){
  const el=document.getElementById(cid);if(!el)return;const total=values.reduce((s,v)=>s+v,0);
  el.innerHTML=labels.map((lbl,i)=>{const pct=total>0?(values[i]/total*100).toFixed(1):0;return `<div class="pie-legend-item"><div class="pie-legend-dot" style="background:${colors[i%colors.length]}"></div><div class="pie-legend-label" title="${lbl}">${lbl}</div><div class="pie-legend-val">${unit}${Number(values[i]).toLocaleString()} <span style="color:var(--text3);font-weight:400">(${pct}%)</span></div></div>`;}).join('');
}
function initReportCharts(){
  const logs=@json($logs);if(!logs.length)return;const byDriver={};
  logs.forEach(r=>{const n=r.driver_name||'ไม่ระบุ';if(!byDriver[n])byDriver[n]={price:0,liters:0,hours:0};byDriver[n].price+=parseFloat(r.total_price)||0;byDriver[n].liters+=parseFloat(r.liters)||0;byDriver[n].hours+=parseFloat(r.work_hours)||0;});
  const labels=Object.keys(byDriver);const prices=labels.map(k=>byDriver[k].price);const liters=labels.map(k=>byDriver[k].liters);const hours=labels.map(k=>byDriver[k].hours);const bgColors=labels.map((_,i)=>COLORS[i%COLORS.length]);
  const pieOpts=unit=>({responsive:true,maintainAspectRatio:false,cutout:'55%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>`${ctx.label}: ${unit}${Number(ctx.raw).toLocaleString()} (${(ctx.parsed/ctx.dataset.data.reduce((a,b)=>a+b,0)*100).toFixed(1)}%)`}}}});
  if(prices.some(v=>v>0)){new Chart(document.getElementById('pieCost'),{type:'doughnut',data:{labels,datasets:[{data:prices,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},options:pieOpts('฿')});buildPieLegend('pieCostLegend',labels,prices,bgColors,'฿');}
  if(liters.some(v=>v>0)){new Chart(document.getElementById('pieLiters'),{type:'doughnut',data:{labels,datasets:[{data:liters,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},options:pieOpts('')});buildPieLegend('pieLitersLegend',labels,liters,bgColors,'');}
  if(hours.some(v=>v>0)){new Chart(document.getElementById('pieHours'),{type:'doughnut',data:{labels,datasets:[{data:hours,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},options:pieOpts('')});buildPieLegend('pieHoursLegend',labels,hours,bgColors,'');}
}
function printReport(){
  const el=document.getElementById('printDateTime');
  if(el){const now=new Date().toLocaleString('th-TH',{timeZone:TZ,day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit',hour12:false});el.textContent=now+' น.';}
  setTimeout(()=>window.print(),150);
}

/* ══════════════════════════════════════════════════════════════════
   TOAST
══════════════════════════════════════════════════════════════════ */
function showToast(title,msg,type='ok'){
  const old=document.getElementById('_jsToast');if(old)old.remove();
  const t=document.createElement('div');t.id='_jsToast';t.className='toast';
  t.style.background=type==='ok'?'#1a7a4d':'#c0392b';
  t.innerHTML=`<div class="toast-icon">${type==='ok'?'✅':'❌'}</div>
    <div class="toast-body"><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div>
    <div class="toast-progress" id="_jsToastBar"></div>`;
  document.body.appendChild(t);
  const bar=document.getElementById('_jsToastBar');const DUR=4500;
  bar.style.transition=`width ${DUR}ms linear`;
  requestAnimationFrame(()=>requestAnimationFrame(()=>{bar.style.width='0%';}));
  const hide=()=>{t.classList.add('hiding');setTimeout(()=>t.remove(),380);};
  setTimeout(hide,DUR);t.addEventListener('click',hide);
}

/* ══════════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',()=>{
  updateNavDate();
  buildTimeDropdowns();
  initCharts();

  @if($errors->any())
  openModal({{ isset($editLog['id']) ? $editLog['id'] : 'null' }});
  @endif
});
</script>
</body>
</html>
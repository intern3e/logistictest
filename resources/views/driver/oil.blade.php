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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
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
.print-header{display:none;padding:0 0 16px;margin-bottom:16px;border-bottom:2px solid var(--navy)}
.print-header-title{font-size:18px;font-weight:700;color:var(--navy)}

/* ── PAGINATION ── */
.pagination{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border);background:var(--surface2);gap:12px;flex-wrap:wrap}
.pagination-info{font-size:12px;color:var(--text2);font-weight:500}
.pagination-info strong{color:var(--navy);font-weight:700}
.pagination-controls{display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.page-btn{min-width:32px;height:32px;padding:0 10px;border:1px solid var(--border);background:var(--surface);color:var(--text2);border-radius:6px;font-family:'Sarabun',sans-serif;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .15s}
.page-btn:hover:not(:disabled){background:var(--surface2);border-color:var(--accent);color:var(--accent)}
.page-btn.active{background:var(--accent);border-color:var(--accent);color:#fff;font-weight:700}
.page-btn:disabled{opacity:.4;cursor:not-allowed}
.page-ellipsis{color:var(--text3);font-size:13px;padding:0 4px;user-select:none}

/* ── DATE RANGE PICKER (Calendar Popup) ── */
.drp-wrap{position:relative}
.drp-trigger{display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;height:36px;cursor:pointer;font-family:'Sarabun',sans-serif;font-size:13px;color:var(--text);transition:all .15s;min-width:220px}
.drp-trigger:hover{border-color:var(--accent);background:var(--surface)}
.drp-trigger.active{border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px rgba(79,142,247,.12)}
.drp-trigger .ic{font-size:14px;color:var(--text2)}
.drp-trigger .txt{flex:1;font-weight:500;color:var(--text)}
.drp-trigger .arrow{font-size:10px;color:var(--text3);transition:transform .2s}
.drp-trigger.active .arrow{transform:rotate(180deg)}

.drp-popup{position:absolute;top:calc(100% + 6px);left:0;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 28px rgba(0,0,0,.12);padding:16px;z-index:100;display:none;min-width:320px;animation:drpIn .18s ease}
.drp-popup.open{display:block}
@keyframes drpIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

.drp-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding:0 4px}
.drp-nav-btn{width:28px;height:28px;border-radius:50%;border:none;background:transparent;color:var(--text2);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:background .15s}
.drp-nav-btn:hover{background:var(--surface2);color:var(--accent)}
.drp-title{font-size:14px;font-weight:600;color:var(--text)}

.drp-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px}
.drp-weekday{text-align:center;font-size:11px;color:var(--text3);padding:6px 0;font-weight:500}

.drp-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;user-select:none;-webkit-user-select:none;touch-action:none}
.drp-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text);cursor:pointer;border:none;background:transparent;font-family:'Sarabun',sans-serif;font-weight:500;border-radius:6px;transition:all .12s;position:relative;user-select:none;-webkit-user-select:none;-webkit-tap-highlight-color:transparent}
.drp-day:hover:not(.disabled):not(.in-range):not(.selected){background:var(--surface2)}
.drp-day.muted{color:var(--text3);opacity:.4}
.drp-day.today{color:var(--accent);font-weight:700}
.drp-day.selected{background:var(--accent);color:#fff;font-weight:700;border-radius:8px;z-index:2}
.drp-day.in-range{background:rgba(79,142,247,.12);border-radius:0;color:var(--accent)}
.drp-day.range-start{background:var(--accent);color:#fff;border-radius:8px 0 0 8px}
.drp-day.range-end{background:var(--accent);color:#fff;border-radius:0 8px 8px 0}
.drp-day.range-start.range-end{border-radius:8px}

.drp-footer{display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:12px;border-top:1px solid var(--border);gap:8px}
.drp-presets{display:flex;gap:4px;flex-wrap:wrap}
.drp-preset-btn{padding:4px 10px;font-size:11px;border:1px solid var(--border);background:var(--surface2);color:var(--text2);border-radius:14px;cursor:pointer;font-family:'Sarabun',sans-serif;font-weight:500;transition:all .15s}
.drp-preset-btn:hover{border-color:var(--accent);color:var(--accent);background:#fff}
.drp-apply-btn{padding:6px 16px;background:var(--accent);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Sarabun',sans-serif}
.drp-apply-btn:hover{background:#3a7ce0}
.drp-apply-btn:disabled{opacity:.5;cursor:not-allowed}

.drp-hint{font-size:11px;color:var(--text3);margin-top:8px;text-align:center;padding:0 4px}

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

/* ซ่อน element เอกสารเก่าๆ ที่ไม่ใช้แล้ว */
.doc-header, .doc-info, .doc-section-title, .doc-footer, .doc-signature{display:none}

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
    {{-- hidden input เก็บ view ปัจจุบัน — ให้ส่งไปพร้อม form ทุกครั้ง --}}
    <input type="hidden" name="view" id="currentView" value="{{ $view }}">
    <div class="filter-bar">
      <div class="view-tabs">
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="button" class="view-tab {{ $view===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
        @endforeach
      </div>
      @if($view==='day')
      {{-- ── เลือกช่วงวันที่แบบ calendar popup ── --}}
      @php
        $dateFrom = request('date_from', request('date', $filterDay));
        $dateTo   = request('date_to',   request('date', $filterDay));
      @endphp
      <input type="hidden" name="date_from" id="drpInputFrom" value="{{ $dateFrom }}">
      <input type="hidden" name="date_to"   id="drpInputTo"   value="{{ $dateTo }}">
      <div class="drp-wrap">
        <button type="button" class="drp-trigger" id="drpTrigger" onclick="drpToggle(event)">
          <span class="ic">📅</span>
          <span class="txt" id="drpLabel">—</span>
          <span class="arrow">▼</span>
        </button>
        <div class="drp-popup" id="drpPopup">
          <div class="drp-header">
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(-1)">‹</button>
            <div class="drp-title" id="drpMonthTitle">—</div>
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(1)">›</button>
          </div>
          <div class="drp-weekdays">
            <div class="drp-weekday">อา.</div>
            <div class="drp-weekday">จ.</div>
            <div class="drp-weekday">อ.</div>
            <div class="drp-weekday">พ.</div>
            <div class="drp-weekday">พฤ.</div>
            <div class="drp-weekday">ศ.</div>
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
    {{-- ── Pagination ── --}}
    <div class="pagination" id="oilPagination" style="display:none">
      <div class="pagination-info">
        แสดง <strong id="pgFrom">0</strong>–<strong id="pgTo">0</strong> จาก <strong id="pgTotal">0</strong> รายการ
      </div>
      <div class="pagination-controls" id="pgControls"></div>
    </div>
  </div>

  <div class="charts-grid">
    <div class="chart-card"><div class="chart-card-title">ค่าน้ำมัน (฿) ต่อคนขับ</div><div class="chart-card-sub">รวม total_price แต่ละคน</div><div style="position:relative;width:100%;height:220px"><canvas id="chartDriver"></canvas></div></div>
    <div class="chart-card"><div class="chart-card-title">น้ำมันต่อกิโล (km/L)</div><div class="chart-card-sub">เฉลี่ย km/L แต่ละคน</div><div style="position:relative;width:100%;height:220px"><canvas id="chartKml"></canvas></div></div>
  </div>

  {{-- Delivery chart (ใช้ฟิลเตอร์หลักด้านบน) --}}
  <div class="chart-card" style="margin-bottom:18px">
    <div class="dlv-filter-row">
      <div>
        <div class="chart-card-title">รายการสมบูรณ์ / รายการผิดพลาด</div>
        <div class="chart-card-sub" style="margin-bottom:0">ประสิทธิภาพการส่งสินค้าแยกตามคนขับ (ตามฟิลเตอร์ด้านบน)</div>
      </div>
    </div>
    <div style="height:300px;position:relative"><canvas id="deliveryChart"></canvas></div>
    {{-- legend --}}
    <div id="dlvLegend" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)"></div>
  </div>
</div>{{-- end pageTracking --}}

{{-- PAGE: REPORT --}}
<div id="pageReport" style="display:none">
  <div class="report-section-header">
    <div><div class="report-section-title">📊 สรุปรายงาน</div><div class="report-section-sub">วิเคราะห์การใช้น้ำมันแยกตามคนขับ</div></div>
    <div class="no-print"><button type="button" class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'" onclick="printReport();return false;">🖨️ พิมพ์ / PDF</button></div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       ฟิลเตอร์เฉพาะหน้า Report (แยกจากหน้าติดตามน้ำมัน)
  ══════════════════════════════════════════════════════════ --}}
  <div class="filter-bar no-print" style="margin-bottom:18px">
    <div class="view-tabs">
      <button type="button" class="view-tab active" onclick="setReportView('day',this)">รายวัน</button>
      <button type="button" class="view-tab" onclick="setReportView('month',this)">รายเดือน</button>
      <button type="button" class="view-tab" onclick="setReportView('year',this)">รายปี</button>
      <button type="button" class="view-tab" onclick="setReportView('all',this)">ทั้งหมด</button>
    </div>

    {{-- Date range picker (รายวัน) --}}
    <div class="drp-wrap" id="repDrpWrap">
      <button type="button" class="drp-trigger" id="repDrpTrigger" onclick="repDrpToggle(event)">
        <span class="ic">📅</span>
        <span class="txt" id="repDrpLabel">—</span>
        <span class="arrow">▼</span>
      </button>
      <div class="drp-popup" id="repDrpPopup">
        <div class="drp-header">
          <button type="button" class="drp-nav-btn" onclick="repDrpNavMonth(-1)">‹</button>
          <div class="drp-title" id="repDrpMonthTitle">—</div>
          <button type="button" class="drp-nav-btn" onclick="repDrpNavMonth(1)">›</button>
        </div>
        <div class="drp-weekdays">
          <div class="drp-weekday">อา.</div><div class="drp-weekday">จ.</div><div class="drp-weekday">อ.</div>
          <div class="drp-weekday">พ.</div><div class="drp-weekday">พฤ.</div><div class="drp-weekday">ศ.</div>
          <div class="drp-weekday">ส.</div>
        </div>
        <div class="drp-days" id="repDrpDays"></div>
        <div class="drp-hint" id="repDrpHint">🖱️ กดค้างแล้วลากเพื่อเลือกช่วงวันที่</div>
        <div class="drp-footer">
          <div class="drp-presets">
            <button type="button" class="drp-preset-btn" onclick="repDrpPreset('today')">วันนี้</button>
            <button type="button" class="drp-preset-btn" onclick="repDrpPreset('7days')">7 วัน</button>
            <button type="button" class="drp-preset-btn" onclick="repDrpPreset('thismonth')">เดือนนี้</button>
          </div>
          <button type="button" class="drp-apply-btn" onclick="repDrpApply()">ตกลง</button>
        </div>
      </div>
    </div>

    {{-- Month picker (รายเดือน) --}}
    <input type="month" id="repMonthPicker" value="{{ date('Y-m') }}" onchange="renderReport()" style="display:none">

    {{-- Year picker (รายปี) --}}
    <select id="repYearPicker" onchange="renderReport()" style="display:none">
      @for($y=date('Y');$y>=2020;$y--)
        <option value="{{ $y }}">{{ $y }}</option>
      @endfor
    </select>

    {{-- Driver filter --}}
    <select id="repDriverPicker" onchange="renderReport()">
      <option value="all">คนขับทั้งหมด</option>
      @foreach($drivers as $d)
        <option value="{{ $d }}">{{ $d }}</option>
      @endforeach
    </select>
  </div>

  {{-- ── Report content: render by JS ── --}}
  <div class="report-stat-row" id="repStatRow"></div>

  <div class="report-pie-grid">
    <div class="pie-card"><div class="pie-card-title">สัดส่วนค่าน้ำมัน (฿)</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieCost"></canvas></div><div class="pie-legend" id="pieCostLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนลิตรที่เติม (ล.)</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieLiters"></canvas></div><div class="pie-legend" id="pieLitersLegend"></div></div>
    <div class="pie-card"><div class="pie-card-title">สัดส่วนชั่วโมงทำงาน</div><div class="pie-card-sub">แยกตามคนขับ</div><div class="pie-canvas-wrap"><canvas id="pieHours"></canvas></div><div class="pie-legend" id="pieHoursLegend"></div></div>
  </div>

  <div class="report-driver-table">
    <div class="table-header"><div class="table-title">📋 ตารางสรุปรายคนขับ</div><span style="font-size:12px;color:var(--text2)" id="repTableHint">เรียงตามค่าน้ำมันสูงสุด</span></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:36px">#</th><th>คนขับ</th>
            <th style="text-align:right">ครั้ง</th>
            <th style="text-align:right">ค่าน้ำมัน (฿)</th>
            <th style="text-align:right">ลิตรรวม</th>
            <th style="text-align:right">ระยะทาง</th>
            <th style="text-align:right">ชม.</th>
            <th style="min-width:150px">เฉลี่ย km/L</th>
          </tr>
        </thead>
        <tbody id="repTableBody"></tbody>
      </table>
    </div>
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
   DELIVERY CHART (ใช้ฟิลเตอร์หลักด้านบน)
══════════════════════════════════════════════════════════════════ */
const MONTH_LABELS = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
let dlvChart = null;

// ── ค่าจากฟิลเตอร์หลัก (Blade → JS) ──
const MAIN_VIEW   = @json($view);                                // 'day' | 'month' | 'year' | 'all'
const MAIN_DRIVER = @json($filterDriver);                        // ชื่อคนขับ หรือ 'all'
const MAIN_YEAR   = parseInt(@json(request('year', date('Y')))); // ปีที่เลือก (สำหรับ view=year)

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

function renderDlv() {
  const drivers = MAIN_DRIVER === 'all'
    ? Object.keys(DLV_BY_DRIVER)
    : (DLV_BY_DRIVER[MAIN_DRIVER] ? [MAIN_DRIVER] : []);

  const OK_COLOR = '#38c98a';  // เขียว = ส่งสำเร็จ
  const NG_COLOR = '#e85d5d';  // แดง  = ส่งไม่สำเร็จ
  let labels = [], datasets = [];
  const tf = { size: 11 };

  // ── โหมด: เลือกคนขับคนเดียว ──
  if (MAIN_DRIVER !== 'all') {
    const data = DLV_BY_DRIVER[MAIN_DRIVER];
    if (!data) {
      if (dlvChart) dlvChart.destroy();
      buildDlvLegend([]);
      return;
    }

    let success = [], fail = [];
    if (MAIN_VIEW === 'day') {
      const sd = Object.keys(data.days || {}).sort();
      labels  = sd.map(d => d.slice(5));
      success = sd.map(d => data.days[d].s);
      fail    = sd.map(d => data.days[d].f);
    } else if (MAIN_VIEW === 'month') {
      labels  = MONTH_LABELS;
      success = (data.months || []).map(m => m.s);
      fail    = (data.months || []).map(m => m.f);
    } else if (MAIN_VIEW === 'year') {
      labels  = ['Q1','Q2','Q3','Q4'].map(q => `${q} ${MAIN_YEAR}`);
      success = (data.quarters || []).map(q => q.s);
      fail    = (data.quarters || []).map(q => q.f);
    } else { // all
      labels  = [MAIN_DRIVER];
      success = [data.all?.s || 0];
      fail    = [data.all?.f || 0];
    }

    datasets = [
      { label:'ส่งสำเร็จ',    data:success, backgroundColor:OK_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
      { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:NG_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
    ];
  }
  // ── โหมด: ทุกคน ──
  else {
    if (MAIN_VIEW === 'day') {
      // แยกเป็นของแต่ละคนขับ — แกน X = ชื่อคนขับ
      // (ใช้ข้อมูลรวมทั้งหมดในช่วงที่ฟิลเตอร์ ของแต่ละคน)
      labels = drivers;
      const success = drivers.map(d => {
        const days = DLV_BY_DRIVER[d].days || {};
        return Object.values(days).reduce((s, v) => s + (v.s || 0), 0);
      });
      const fail = drivers.map(d => {
        const days = DLV_BY_DRIVER[d].days || {};
        return Object.values(days).reduce((s, v) => s + (v.f || 0), 0);
      });
      datasets = [
        { label:'ส่งสำเร็จ',    data:success, backgroundColor:OK_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
        { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:NG_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
      ];
    } else if (MAIN_VIEW === 'month') {
      // รวมทุกคนขับในแต่ละเดือน
      labels = MONTH_LABELS;
      const success = MONTH_LABELS.map((_, i) => drivers.reduce((s, d) => s + (DLV_BY_DRIVER[d].months?.[i]?.s || 0), 0));
      const fail    = MONTH_LABELS.map((_, i) => drivers.reduce((s, d) => s + (DLV_BY_DRIVER[d].months?.[i]?.f || 0), 0));
      datasets = [
        { label:'ส่งสำเร็จ',    data:success, backgroundColor:OK_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
        { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:NG_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
      ];
    } else if (MAIN_VIEW === 'year') {
      // รวมทุกคนขับในแต่ละ Quarter
      labels = ['Q1','Q2','Q3','Q4'].map(q => `${q} ${MAIN_YEAR}`);
      const success = [0,1,2,3].map(i => drivers.reduce((s, d) => s + (DLV_BY_DRIVER[d].quarters?.[i]?.s || 0), 0));
      const fail    = [0,1,2,3].map(i => drivers.reduce((s, d) => s + (DLV_BY_DRIVER[d].quarters?.[i]?.f || 0), 0));
      datasets = [
        { label:'ส่งสำเร็จ',    data:success, backgroundColor:OK_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
        { label:'ส่งไม่สำเร็จ', data:fail,    backgroundColor:NG_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
      ];
    } else { // all — ชื่อคนขับเป็นแกน X
      labels = drivers;
      datasets = [
        { label:'ส่งสำเร็จ',    data:drivers.map(d => DLV_BY_DRIVER[d].all?.s || 0), backgroundColor:OK_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
        { label:'ส่งไม่สำเร็จ', data:drivers.map(d => DLV_BY_DRIVER[d].all?.f || 0), backgroundColor:NG_COLOR, borderRadius:5, borderSkipped:false, stack:'s' },
      ];
    }
  }

  if (dlvChart) dlvChart.destroy();
  dlvChart = new Chart(document.getElementById('deliveryChart'), {
    type: 'bar',
    data: { labels, datasets },
    plugins: [ChartDataLabels],
    options: {
      responsive: true, maintainAspectRatio: false,
      layout: { padding: { top: 20 } },
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: {
          label : ctx   => `${ctx.dataset.label}: ${ctx.raw} รายการ`,
          footer: items => 'รวม: ' + items.reduce((s, i) => s + i.raw, 0) + ' รายการ',
        }},
        // ── แสดงตัวเลขบนแท่งกราฟ ──
        datalabels: {
          color: '#fff',
          font: { weight: '700', size: 11, family: 'Sarabun' },
          formatter: (v) => v > 0 ? v : '',
          display: (ctx) => ctx.dataset.data[ctx.dataIndex] > 0,
          anchor: 'center',
          align: 'center',
        },
      },
      scales: {
        x: {
          stacked: true,
          ticks: {
            font: { size: 12, weight: '600', family: 'Sarabun' },
            color: '#1a2744',
            autoSkip: false,
            maxRotation: 30,
            minRotation: 0,
          },
          grid: { color:'rgba(0,0,0,.04)' },
        },
        y: {
          stacked: true, beginAtZero: true,
          ticks: { font: tf, callback: v => v + ' รายการ' },
          grid: { color: 'rgba(0,0,0,.04)' },
        },
      },
    },
  });
  buildDlvLegend(datasets);
}

function buildDlvLegend(datasets) {
  const wrap = document.getElementById('dlvLegend'); if (!wrap) return;
  // แสดงแค่ 2 รายการหลัก: สำเร็จ/ไม่สำเร็จ (ไม่ต้องซ้ำตามคนขับ)
  const items = [
    { label: 'ส่งสำเร็จ',    color: '#38c98a' },
    { label: 'ส่งไม่สำเร็จ', color: '#e85d5d' },
  ];
  wrap.innerHTML = items.map(it =>
    `<span style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text2);font-weight:500">
      <span style="width:14px;height:14px;border-radius:3px;background:${it.color};display:inline-block;flex-shrink:0"></span>${it.label}
    </span>`
  ).join('');
}

/* ══════════════════════════════════════════════════════════════════
   PAGE SWITCH / NAV DATE / TABLE FILTER
══════════════════════════════════════════════════════════════════ */
function switchPage(p){
  ['pageTracking','pageReport'].forEach(id=>document.getElementById(id).style.display='none');
  ['navOil','navReport'].forEach(id=>document.getElementById(id).classList.remove('active'));
  if(p==='tracking'){document.getElementById('pageTracking').style.display='block';document.getElementById('navOil').classList.add('active');}
  else if(p==='report'){document.getElementById('pageReport').style.display='block';document.getElementById('navReport').classList.add('active');if(!reportChartsInited){renderReport();reportChartsInited=true;}}
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
  oilSearchQuery = q.toLowerCase();
  oilCurrentPage = 1;
  renderOilPage();
}

/* ══════════════════════════════════════════════════════════════════
   VIEW SWITCHER — เปลี่ยน view (day/month/year/all)
══════════════════════════════════════════════════════════════════ */
function switchView(v){
  // ตั้งค่า hidden input แล้ว submit
  document.getElementById('currentView').value = v;
  // ล้างค่า date_from/date_to ถ้าเปลี่ยนจาก day ไป view อื่น
  // (ปล่อยให้ controller ใช้ filter ของ view ใหม่แทน)
  document.getElementById('filterForm').submit();
}

/* ══════════════════════════════════════════════════════════════════
   DATE RANGE PICKER (Calendar Popup)
══════════════════════════════════════════════════════════════════ */
const TH_MONTHS = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
const TH_MONTHS_SHORT = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

let drpViewYear  = null;
let drpViewMonth = null;
let drpFrom      = null;  // 'YYYY-MM-DD'
let drpTo        = null;  // 'YYYY-MM-DD'

function drpPad(n){ return String(n).padStart(2,'0'); }
function drpFmt(d){ return `${d.getFullYear()}-${drpPad(d.getMonth()+1)}-${drpPad(d.getDate())}`; }
function drpParse(s){ if(!s) return null; const p=s.split('-'); return new Date(parseInt(p[0]),parseInt(p[1])-1,parseInt(p[2])); }

function drpFormatLabel(fromStr, toStr){
  if(!fromStr) return 'เลือกช่วงวันที่';
  const f = drpParse(fromStr);
  if(!toStr || fromStr === toStr){
    return `${f.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} ${f.getFullYear()+543}`;
  }
  const t = drpParse(toStr);
  // เดือนเดียวกัน
  if(f.getFullYear()===t.getFullYear() && f.getMonth()===t.getMonth()){
    return `${f.getDate()} – ${t.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} ${f.getFullYear()+543}`;
  }
  // ปีเดียวกัน
  if(f.getFullYear()===t.getFullYear()){
    return `${f.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} – ${t.getDate()} ${TH_MONTHS_SHORT[t.getMonth()]} ${f.getFullYear()+543}`;
  }
  return `${f.getDate()} ${TH_MONTHS_SHORT[f.getMonth()]} ${f.getFullYear()+543} – ${t.getDate()} ${TH_MONTHS_SHORT[t.getMonth()]} ${t.getFullYear()+543}`;
}

function drpUpdateLabel(){
  const lbl = document.getElementById('drpLabel'); if(!lbl) return;
  lbl.textContent = drpFormatLabel(drpFrom, drpTo);
}

function drpToggle(e){
  if(e) e.stopPropagation();
  const pop = document.getElementById('drpPopup');
  const trg = document.getElementById('drpTrigger');
  const isOpen = pop.classList.contains('open');
  if(isOpen){
    pop.classList.remove('open');
    trg.classList.remove('active');
  } else {
    // เปิดมา -> focus ที่เดือนของ from (หรือเดือนปัจจุบัน)
    const anchor = drpFrom ? drpParse(drpFrom) : new Date();
    drpViewYear  = anchor.getFullYear();
    drpViewMonth = anchor.getMonth();
    drpRender();
    pop.classList.add('open');
    trg.classList.add('active');
  }
}

function drpNavMonth(delta){
  drpViewMonth += delta;
  if(drpViewMonth < 0){ drpViewMonth = 11; drpViewYear--; }
  if(drpViewMonth > 11){ drpViewMonth = 0; drpViewYear++; }
  drpRender();
}

function drpRender(){
  // หัวเรื่องเดือน/ปี (พ.ศ.)
  document.getElementById('drpMonthTitle').textContent =
    `${TH_MONTHS_SHORT[drpViewMonth]} ${drpViewYear+543}`;

  const grid = document.getElementById('drpDays');
  const firstDay = new Date(drpViewYear, drpViewMonth, 1);
  const firstWeekday = firstDay.getDay(); // 0=อาทิตย์
  const daysInMonth = new Date(drpViewYear, drpViewMonth+1, 0).getDate();

  const prevMonthDays = new Date(drpViewYear, drpViewMonth, 0).getDate();

  const todayStr = drpFmt(new Date());
  let html = '';

  // วันของเดือนก่อนหน้า (muted)
  for(let i = firstWeekday-1; i >= 0; i--){
    const d = prevMonthDays - i;
    const y = drpViewMonth === 0 ? drpViewYear - 1 : drpViewYear;
    const m = drpViewMonth === 0 ? 11 : drpViewMonth - 1;
    const ds = `${y}-${drpPad(m+1)}-${drpPad(d)}`;
    html += drpDayBtn(ds, d, true, todayStr);
  }

  // วันของเดือนปัจจุบัน
  for(let d = 1; d <= daysInMonth; d++){
    const ds = `${drpViewYear}-${drpPad(drpViewMonth+1)}-${drpPad(d)}`;
    html += drpDayBtn(ds, d, false, todayStr);
  }

  // วันของเดือนถัดไป เพื่อเติมให้ครบ 6 สัปดาห์ (42 ช่อง)
  const totalShown = firstWeekday + daysInMonth;
  const remain = (7 - (totalShown % 7)) % 7;
  for(let d = 1; d <= remain; d++){
    const y = drpViewMonth === 11 ? drpViewYear + 1 : drpViewYear;
    const m = drpViewMonth === 11 ? 0 : drpViewMonth + 1;
    const ds = `${y}-${drpPad(m+1)}-${drpPad(d)}`;
    html += drpDayBtn(ds, d, true, todayStr);
  }

  grid.innerHTML = html;

  // hint
  const hint = document.getElementById('drpHint');
  if(!drpFrom)          hint.textContent = '🖱️ กดค้างแล้วลากเพื่อเลือกช่วงวันที่';
  else if(drpFrom === drpTo) hint.textContent = `เลือก ${drpFormatLabel(drpFrom,drpTo)} (ลากเพื่อเลือกช่วง)`;
  else                  hint.textContent = `เลือกช่วง ${drpFormatLabel(drpFrom,drpTo)}`;

  document.getElementById('drpApplyBtn').disabled = !drpFrom;
}

function drpDayBtn(ds, dayNum, muted, todayStr){
  const classes = ['drp-day'];
  if(muted) classes.push('muted');
  if(ds === todayStr) classes.push('today');

  // highlight range
  if(drpFrom && drpTo){
    if(ds === drpFrom && ds === drpTo){ classes.push('selected'); }
    else if(ds === drpFrom){ classes.push('range-start'); }
    else if(ds === drpTo){   classes.push('range-end'); }
    else if(ds > drpFrom && ds < drpTo){ classes.push('in-range'); }
  } else if(drpFrom && ds === drpFrom){
    classes.push('selected');
  }

  return `<button type="button" class="${classes.join(' ')}" data-date="${ds}">${dayNum}</button>`;
}

/* ── Drag selection ── */
let drpDragging = false;
let drpDragStart = null;

function drpGetDateFromEvent(e){
  // รองรับทั้ง mouse และ touch
  const point = e.touches ? e.touches[0] : e;
  if(!point) return null;
  const el = document.elementFromPoint(point.clientX, point.clientY);
  if(!el) return null;
  const btn = el.closest('.drp-day');
  return btn ? btn.dataset.date : null;
}

function drpStartDrag(e){
  const ds = drpGetDateFromEvent(e);
  if(!ds) return;
  e.preventDefault();
  drpDragging  = true;
  drpDragStart = ds;
  drpFrom = ds;
  drpTo   = ds;
  drpRender();
}

function drpMoveDrag(e){
  if(!drpDragging) return;
  const ds = drpGetDateFromEvent(e);
  if(!ds) return;
  e.preventDefault();
  // ถ้าลากย้อนขึ้นไป -> ให้ start/end swap
  if(ds < drpDragStart){
    drpFrom = ds;
    drpTo   = drpDragStart;
  } else {
    drpFrom = drpDragStart;
    drpTo   = ds;
  }
  drpRender();
}

function drpEndDrag(){
  drpDragging = false;
}

function drpPreset(preset){
  const now = new Date();
  let f, t;
  if(preset === 'today'){
    f = t = drpFmt(now);
  } else if(preset === '7days'){
    t = drpFmt(now);
    const d = new Date(now); d.setDate(d.getDate()-6);
    f = drpFmt(d);
  } else if(preset === 'thismonth'){
    f = drpFmt(new Date(now.getFullYear(), now.getMonth(), 1));
    t = drpFmt(now);
  }
  drpFrom = f; drpTo = t;
  // ย้าย view ไปที่เดือนของ from
  const fd = drpParse(f);
  drpViewYear = fd.getFullYear(); drpViewMonth = fd.getMonth();
  drpRender();
}

function drpApply(){
  if(!drpFrom) return;
  // ถ้าไม่มี to -> เลือกวันเดียว
  const to = drpTo || drpFrom;
  document.getElementById('drpInputFrom').value = drpFrom;
  document.getElementById('drpInputTo').value   = to;
  // ── ล็อก view เป็น 'day' เสมอ (เพราะ picker นี้ใช้ได้เฉพาะ view=day) ──
  document.getElementById('currentView').value = 'day';
  document.getElementById('filterForm').submit();
}

// ปิด popup เมื่อคลิกนอก
document.addEventListener('click', (e) => {
  const pop = document.getElementById('drpPopup');
  const trg = document.getElementById('drpTrigger');
  if(!pop || !pop.classList.contains('open')) return;
  if(pop.contains(e.target) || trg.contains(e.target)) return;
  pop.classList.remove('open');
  trg.classList.remove('active');
});

// init จาก hidden input
function drpInit(){
  const f = document.getElementById('drpInputFrom')?.value;
  const t = document.getElementById('drpInputTo')?.value;
  if(f) drpFrom = f;
  if(t) drpTo   = t;
  drpUpdateLabel();

  // ── Drag & drop event listeners ──
  const grid = document.getElementById('drpDays');
  if(grid){
    // mouse
    grid.addEventListener('mousedown',  drpStartDrag);
    document.addEventListener('mousemove', drpMoveDrag);
    document.addEventListener('mouseup',   drpEndDrag);
    // touch
    grid.addEventListener('touchstart', drpStartDrag, {passive:false});
    document.addEventListener('touchmove', drpMoveDrag, {passive:false});
    document.addEventListener('touchend',  drpEndDrag);
    // ป้องกัน context menu ขณะลาก
    grid.addEventListener('contextmenu', e => { if(drpDragging) e.preventDefault(); });
  }
}

/* ══════════════════════════════════════════════════════════════════
   PAGINATION (รายการเติมน้ำมัน — หน้าละ 20)
══════════════════════════════════════════════════════════════════ */
const OIL_PAGE_SIZE = 20;
let oilCurrentPage = 1;
let oilSearchQuery = '';

function getVisibleRows() {
  const rows = Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));
  if (!oilSearchQuery) return rows;
  return rows.filter(r => r.dataset.driver.includes(oilSearchQuery));
}

function renderOilPage() {
  const allRows    = Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));
  const visible    = getVisibleRows();
  const total      = visible.length;
  const totalPages = Math.max(1, Math.ceil(total / OIL_PAGE_SIZE));
  if (oilCurrentPage > totalPages) oilCurrentPage = totalPages;

  const start = (oilCurrentPage - 1) * OIL_PAGE_SIZE;
  const end   = start + OIL_PAGE_SIZE;

  // ซ่อนทั้งหมดก่อน แล้วค่อยโชว์เฉพาะหน้านี้
  allRows.forEach(r => r.style.display = 'none');
  visible.slice(start, end).forEach(r => r.style.display = '');

  // อัปเดต badge จำนวน
  const c = document.getElementById('oilCount');
  if (c) c.textContent = total;

  // ── ซ่อน pagination ถ้ามีน้อยกว่าหรือเท่ากับ 1 หน้า ──
  const pag = document.getElementById('oilPagination');
  if (total <= OIL_PAGE_SIZE) {
    pag.style.display = 'none';
    return;
  }
  pag.style.display = 'flex';

  // ข้อความสรุป
  document.getElementById('pgFrom').textContent  = total === 0 ? 0 : (start + 1);
  document.getElementById('pgTo').textContent    = Math.min(end, total);
  document.getElementById('pgTotal').textContent = total;

  // ปุ่มเลขหน้า
  renderPageButtons(totalPages);
}

function renderPageButtons(totalPages) {
  const wrap = document.getElementById('pgControls');
  const cur  = oilCurrentPage;
  let html = '';

  // ปุ่มย้อนกลับ
  html += `<button class="page-btn" ${cur===1?'disabled':''} onclick="gotoPage(${cur-1})">← ก่อนหน้า</button>`;

  // แสดงเลขหน้าแบบย่อ ถ้ามีมาก
  const pages = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) pages.push(i);
  } else {
    pages.push(1);
    if (cur > 3) pages.push('...');
    const s = Math.max(2, cur - 1), e = Math.min(totalPages - 1, cur + 1);
    for (let i = s; i <= e; i++) pages.push(i);
    if (cur < totalPages - 2) pages.push('...');
    pages.push(totalPages);
  }

  pages.forEach(p => {
    if (p === '...') html += `<span class="page-ellipsis">…</span>`;
    else html += `<button class="page-btn ${p===cur?'active':''}" onclick="gotoPage(${p})">${p}</button>`;
  });

  // ปุ่มถัดไป
  html += `<button class="page-btn" ${cur===totalPages?'disabled':''} onclick="gotoPage(${cur+1})">ถัดไป →</button>`;

  wrap.innerHTML = html;
}

function gotoPage(n) {
  oilCurrentPage = n;
  renderOilPage();
  // scroll ไปบนตารางเล็กน้อย
  document.querySelector('.table-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
  renderDlv();
}

/* ══════════════════════════════════════════════════════════════════
   PIE CHARTS
══════════════════════════════════════════════════════════════════ */
function buildPieLegend(cid,labels,values,colors,unit=''){
  const el=document.getElementById(cid);if(!el)return;const total=values.reduce((s,v)=>s+v,0);
  el.innerHTML=labels.map((lbl,i)=>{const pct=total>0?(values[i]/total*100).toFixed(1):0;return `<div class="pie-legend-item"><div class="pie-legend-dot" style="background:${colors[i%colors.length]}"></div><div class="pie-legend-label" title="${lbl}">${lbl}</div><div class="pie-legend-val">${unit}${Number(values[i]).toLocaleString()} <span style="color:var(--text3);font-weight:400">(${pct}%)</span></div></div>`;}).join('');
}
/* ══════════════════════════════════════════════════════════════════
   REPORT PAGE — มีฟิลเตอร์ของตัวเอง แยกจากหน้าติดตามน้ำมัน
══════════════════════════════════════════════════════════════════ */
const ALL_LOGS = @json($allLogs ?? []);  // ข้อมูลทั้งหมด (ไม่ filter)

let repView       = 'day';
let repDateFrom   = null;     // 'YYYY-MM-DD'
let repDateTo     = null;
let repChartCost  = null;
let repChartLit   = null;
let repChartHour  = null;

function setReportView(v, btn){
  repView = v;
  document.querySelectorAll('#pageReport .view-tabs .view-tab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  // toggle filter ย่อย
  document.getElementById('repDrpWrap').style.display     = (v === 'day')   ? '' : 'none';
  document.getElementById('repMonthPicker').style.display = (v === 'month') ? '' : 'none';
  document.getElementById('repYearPicker').style.display  = (v === 'year')  ? '' : 'none';
  renderReport();
}

/* ── filter logs ตามเงื่อนไขหน้า report ── */
function getReportLogs(){
  const driver = document.getElementById('repDriverPicker').value;
  const month  = document.getElementById('repMonthPicker').value;   // 'YYYY-MM'
  const year   = document.getElementById('repYearPicker').value;    // 'YYYY'

  return ALL_LOGS.filter(log => {
    // filter driver
    if (driver !== 'all' && log.driver_name !== driver) return false;

    const wd = (log.work_date || '').slice(0, 10);
    if (!wd) return repView === 'all';

    if (repView === 'day') {
      if (!repDateFrom || !repDateTo) return false;
      return wd >= repDateFrom && wd <= repDateTo;
    } else if (repView === 'month') {
      return wd.startsWith(month);
    } else if (repView === 'year') {
      return wd.startsWith(year);
    }
    return true; // 'all'
  });
}

/* ── render report ทั้งหน้า ── */
function renderReport(){
  const logs = getReportLogs();
  renderRepStats(logs);
  renderRepPies(logs);
  renderRepTable(logs);
}

/* ── Stats row ── */
function renderRepStats(logs){
  const totalLogs   = logs.length;
  const totalDist   = logs.reduce((s,r)=>s+(parseFloat(r.total_distance)||0),0);
  const totalPrice  = logs.reduce((s,r)=>s+(parseFloat(r.total_price)||0),0);
  const avgPrice    = totalLogs>0 ? totalPrice/totalLogs : 0;

  // หา driver ที่ใช้น้ำมันสูงสุด
  const byDriver = {};
  logs.forEach(r=>{
    const n = r.driver_name || 'ไม่ระบุ';
    if(!byDriver[n]) byDriver[n] = {price:0, kml:[]};
    byDriver[n].price += parseFloat(r.total_price)||0;
    if((parseFloat(r.km_per_liter)||0) > 0) byDriver[n].kml.push(parseFloat(r.km_per_liter));
  });
  const drivers = Object.entries(byDriver);
  const maxDriver = drivers.length ? drivers.sort((a,b)=>b[1].price-a[1].price)[0] : null;
  const bestKml = drivers
    .map(([n,d])=>[n, d.kml.length ? d.kml.reduce((a,b)=>a+b,0)/d.kml.length : 0])
    .filter(([_,k])=>k>0)
    .sort((a,b)=>b[1]-a[1])[0];

  const fmt = n => n > 0 ? Number(n).toLocaleString('en-US',{maximumFractionDigits:0}) : '—';
  let html = `
    <div class="report-stat-card"><div class="report-stat-label">รายการทั้งหมด</div><div class="report-stat-value">${totalLogs}</div><div class="report-stat-sub">รายการ</div></div>
    <div class="report-stat-card"><div class="report-stat-label">ระยะทางรวม</div><div class="report-stat-value">${fmt(totalDist)}</div><div class="report-stat-sub">กิโลเมตร</div></div>
    <div class="report-stat-card"><div class="report-stat-label">เฉลี่ย ฿/ครั้ง</div><div class="report-stat-value">${avgPrice>0?'฿'+fmt(avgPrice):'—'}</div><div class="report-stat-sub">บาท/ครั้ง</div></div>
  `;
  if(maxDriver) html += `<div class="report-stat-card"><div class="report-stat-label">ใช้น้ำมันสูงสุด</div><div class="report-stat-value" style="font-size:15px">${maxDriver[0]}</div><div class="report-stat-sub">฿${fmt(maxDriver[1].price)}</div></div>`;
  if(bestKml)   html += `<div class="report-stat-card"><div class="report-stat-label">ประหยัดที่สุด</div><div class="report-stat-value" style="font-size:15px">${bestKml[0]}</div><div class="report-stat-sub">${bestKml[1].toFixed(1)} km/L</div></div>`;

  document.getElementById('repStatRow').innerHTML = html;
}

/* ── Pie charts ── */
function renderRepPies(logs){
  const byDriver = {};
  logs.forEach(r=>{
    const n = r.driver_name || 'ไม่ระบุ';
    if(!byDriver[n]) byDriver[n] = {price:0, liters:0, hours:0};
    byDriver[n].price  += parseFloat(r.total_price) || 0;
    byDriver[n].liters += parseFloat(r.liters) || 0;
    byDriver[n].hours  += parseFloat(r.work_hours) || 0;
  });
  const labels   = Object.keys(byDriver);
  const prices   = labels.map(k => byDriver[k].price);
  const liters   = labels.map(k => byDriver[k].liters);
  const hours    = labels.map(k => byDriver[k].hours);
  const bgColors = labels.map((_,i)=>COLORS[i%COLORS.length]);

  const pieOpts = unit => ({
    responsive:true, maintainAspectRatio:false, cutout:'55%',
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{
      label: ctx => {
        const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
        const pct = total>0 ? (ctx.parsed/total*100).toFixed(1) : 0;
        return `${ctx.label}: ${unit}${Number(ctx.raw).toLocaleString()} (${pct}%)`;
      }
    }}, datalabels: { display: false } },
  });

  // destroy เก่า
  if(repChartCost) repChartCost.destroy();
  if(repChartLit)  repChartLit.destroy();
  if(repChartHour) repChartHour.destroy();

  if(prices.some(v=>v>0)){
    repChartCost = new Chart(document.getElementById('pieCost'),{
      type:'doughnut',
      data:{labels,datasets:[{data:prices,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},
      options:pieOpts('฿')
    });
    buildPieLegend('pieCostLegend',labels,prices,bgColors,'฿');
  } else {
    document.getElementById('pieCostLegend').innerHTML = '<div style="color:var(--text3);text-align:center;padding:20px 0">ไม่มีข้อมูล</div>';
  }

  if(liters.some(v=>v>0)){
    repChartLit = new Chart(document.getElementById('pieLiters'),{
      type:'doughnut',
      data:{labels,datasets:[{data:liters,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},
      options:pieOpts('')
    });
    buildPieLegend('pieLitersLegend',labels,liters,bgColors,'');
  } else {
    document.getElementById('pieLitersLegend').innerHTML = '<div style="color:var(--text3);text-align:center;padding:20px 0">ไม่มีข้อมูล</div>';
  }

  if(hours.some(v=>v>0)){
    repChartHour = new Chart(document.getElementById('pieHours'),{
      type:'doughnut',
      data:{labels,datasets:[{data:hours,backgroundColor:bgColors,borderWidth:2,borderColor:'#fff',hoverOffset:8}]},
      options:pieOpts('')
    });
    buildPieLegend('pieHoursLegend',labels,hours,bgColors,'');
  } else {
    document.getElementById('pieHoursLegend').innerHTML = '<div style="color:var(--text3);text-align:center;padding:20px 0">ไม่มีข้อมูล</div>';
  }
}

/* ── Driver table ── */
function renderRepTable(logs){
  const sum = {};
  logs.forEach(log=>{
    const n = log.driver_name || 'ไม่ระบุ';
    if(!sum[n]) sum[n] = {driver:n, records:0, total_price:0, total_liters:0, total_dist:0, work_hours:0, kml_values:[]};
    sum[n].records++;
    sum[n].total_price  += parseFloat(log.total_price)||0;
    sum[n].total_liters += parseFloat(log.liters)||0;
    sum[n].total_dist   += parseFloat(log.total_distance)||0;
    sum[n].work_hours   += parseFloat(log.work_hours)||0;
    const kml = parseFloat(log.km_per_liter)||0;
    if(kml > 0) sum[n].kml_values.push(kml);
  });
  const list = Object.values(sum).sort((a,b)=>b.total_price-a.total_price);
  const maxKml = list.length ? Math.max(...list.map(d=>d.kml_values.length>0?d.kml_values.reduce((a,b)=>a+b,0)/d.kml_values.length:0),1) : 1;

  const fmt = n => n > 0 ? Number(n).toLocaleString('en-US',{maximumFractionDigits:1}) : '—';
  const fmt0 = n => n > 0 ? Number(n).toLocaleString('en-US',{maximumFractionDigits:0}) : '—';

  let rows = list.map((ds,i)=>{
    const avgKml = ds.kml_values.length ? ds.kml_values.reduce((a,b)=>a+b,0)/ds.kml_values.length : 0;
    const pct = maxKml>0 ? Math.min(100, (avgKml/maxKml)*100) : 0;
    const rankClass = i===0?'gold':(i===1?'silver':(i===2?'bronze':''));
    const kmlCell = avgKml>0
      ? `<div class="kml-bar-wrap"><div class="kml-bar-bg"><div class="kml-bar-fill" style="width:${pct}%"></div></div><span class="km-val" style="font-size:12px">${avgKml.toFixed(1)}</span></div>`
      : `<span style="color:var(--text3);font-size:12px">—</span>`;

    return `<tr>
      <td><span class="rank-badge ${rankClass}">${i+1}</span></td>
      <td><strong>${ds.driver}</strong></td>
      <td style="text-align:right;color:var(--text2)">${ds.records}</td>
      <td style="text-align:right;font-weight:700;color:var(--navy)">฿${fmt0(ds.total_price)}</td>
      <td style="text-align:right;color:var(--text2)">${ds.total_liters>0?fmt(ds.total_liters)+' ล.':'—'}</td>
      <td style="text-align:right;color:var(--text2)">${fmt0(ds.total_dist)}</td>
      <td style="text-align:right;color:var(--accent3);font-weight:600">${fmt(ds.work_hours)}</td>
      <td>${kmlCell}</td>
    </tr>`;
  }).join('');

  // row รวม
  const gt = {
    price: list.reduce((s,d)=>s+d.total_price,0),
    liters: list.reduce((s,d)=>s+d.total_liters,0),
    dist:  list.reduce((s,d)=>s+d.total_dist,0),
    hours: list.reduce((s,d)=>s+d.work_hours,0),
    records: list.reduce((s,d)=>s+d.records,0),
  };

  if(list.length > 0){
    rows += `<tr>
      <td></td><td><strong>รวมทั้งหมด</strong></td>
      <td style="text-align:right">${gt.records}</td>
      <td style="text-align:right;color:var(--navy)">฿${fmt0(gt.price)}</td>
      <td style="text-align:right">${gt.liters>0?fmt(gt.liters)+' ล.':'—'}</td>
      <td style="text-align:right">${fmt0(gt.dist)}</td>
      <td style="text-align:right">${fmt(gt.hours)}</td>
      <td></td>
    </tr>`;
  } else {
    rows = `<tr><td colspan="8"><div class="empty-state"><div class="icon">📊</div><p>ไม่พบข้อมูลในช่วงที่เลือก</p></div></td></tr>`;
  }

  document.getElementById('repTableBody').innerHTML = rows;
}

/* ══════════════════════════════════════════════════════════════════
   REPORT — Date Range Picker (แยกจากหน้า Tracking)
══════════════════════════════════════════════════════════════════ */
let repDrpViewYear  = null;
let repDrpViewMonth = null;
let repDrpFrom = null;
let repDrpTo   = null;
let repDrpDragging  = false;
let repDrpDragStart = null;

function repDrpUpdateLabel(){
  const lbl = document.getElementById('repDrpLabel'); if(!lbl) return;
  lbl.textContent = drpFormatLabel(repDrpFrom, repDrpTo);
}

function repDrpToggle(e){
  if(e) e.stopPropagation();
  const pop = document.getElementById('repDrpPopup');
  const trg = document.getElementById('repDrpTrigger');
  const isOpen = pop.classList.contains('open');
  if(isOpen){
    pop.classList.remove('open'); trg.classList.remove('active');
  } else {
    const anchor = repDrpFrom ? drpParse(repDrpFrom) : new Date();
    repDrpViewYear = anchor.getFullYear();
    repDrpViewMonth = anchor.getMonth();
    repDrpRender();
    pop.classList.add('open'); trg.classList.add('active');
  }
}

function repDrpNavMonth(delta){
  repDrpViewMonth += delta;
  if(repDrpViewMonth < 0){ repDrpViewMonth = 11; repDrpViewYear--; }
  if(repDrpViewMonth > 11){ repDrpViewMonth = 0; repDrpViewYear++; }
  repDrpRender();
}

function repDrpRender(){
  document.getElementById('repDrpMonthTitle').textContent =
    `${TH_MONTHS_SHORT[repDrpViewMonth]} ${repDrpViewYear+543}`;
  const grid = document.getElementById('repDrpDays');
  const firstDay = new Date(repDrpViewYear, repDrpViewMonth, 1);
  const firstWeekday = firstDay.getDay();
  const daysInMonth = new Date(repDrpViewYear, repDrpViewMonth+1, 0).getDate();
  const prevMonthDays = new Date(repDrpViewYear, repDrpViewMonth, 0).getDate();
  const todayStr = drpFmt(new Date());
  let html = '';

  for(let i = firstWeekday-1; i >= 0; i--){
    const d = prevMonthDays - i;
    const y = repDrpViewMonth === 0 ? repDrpViewYear-1 : repDrpViewYear;
    const m = repDrpViewMonth === 0 ? 11 : repDrpViewMonth-1;
    const ds = `${y}-${drpPad(m+1)}-${drpPad(d)}`;
    html += repDrpDayBtn(ds, d, true, todayStr);
  }
  for(let d = 1; d <= daysInMonth; d++){
    const ds = `${repDrpViewYear}-${drpPad(repDrpViewMonth+1)}-${drpPad(d)}`;
    html += repDrpDayBtn(ds, d, false, todayStr);
  }
  const totalShown = firstWeekday + daysInMonth;
  const remain = (7 - (totalShown % 7)) % 7;
  for(let d = 1; d <= remain; d++){
    const y = repDrpViewMonth === 11 ? repDrpViewYear+1 : repDrpViewYear;
    const m = repDrpViewMonth === 11 ? 0 : repDrpViewMonth+1;
    const ds = `${y}-${drpPad(m+1)}-${drpPad(d)}`;
    html += repDrpDayBtn(ds, d, true, todayStr);
  }
  grid.innerHTML = html;

  const hint = document.getElementById('repDrpHint');
  if(!repDrpFrom)                     hint.textContent = '🖱️ กดค้างแล้วลากเพื่อเลือกช่วงวันที่';
  else if(repDrpFrom === repDrpTo)    hint.textContent = `เลือก ${drpFormatLabel(repDrpFrom,repDrpTo)}`;
  else                                hint.textContent = `เลือกช่วง ${drpFormatLabel(repDrpFrom,repDrpTo)}`;
}

function repDrpDayBtn(ds, dayNum, muted, todayStr){
  const classes = ['drp-day'];
  if(muted) classes.push('muted');
  if(ds === todayStr) classes.push('today');
  if(repDrpFrom && repDrpTo){
    if(ds === repDrpFrom && ds === repDrpTo) classes.push('selected');
    else if(ds === repDrpFrom) classes.push('range-start');
    else if(ds === repDrpTo)   classes.push('range-end');
    else if(ds > repDrpFrom && ds < repDrpTo) classes.push('in-range');
  } else if(repDrpFrom && ds === repDrpFrom){
    classes.push('selected');
  }
  return `<button type="button" class="${classes.join(' ')}" data-date="${ds}">${dayNum}</button>`;
}

function repDrpGetDateFromEvent(e){
  const point = e.touches ? e.touches[0] : e;
  if(!point) return null;
  const el = document.elementFromPoint(point.clientX, point.clientY);
  if(!el) return null;
  const btn = el.closest('.drp-day');
  return btn ? btn.dataset.date : null;
}

function repDrpStartDrag(e){
  const ds = repDrpGetDateFromEvent(e); if(!ds) return;
  e.preventDefault();
  repDrpDragging  = true;
  repDrpDragStart = ds;
  repDrpFrom = ds; repDrpTo = ds;
  repDrpRender();
}

function repDrpMoveDrag(e){
  if(!repDrpDragging) return;
  const ds = repDrpGetDateFromEvent(e); if(!ds) return;
  e.preventDefault();
  if(ds < repDrpDragStart){ repDrpFrom = ds; repDrpTo = repDrpDragStart; }
  else                    { repDrpFrom = repDrpDragStart; repDrpTo = ds; }
  repDrpRender();
}

function repDrpEndDrag(){ repDrpDragging = false; }

function repDrpPreset(preset){
  const now = new Date();
  let f, t;
  if(preset === 'today'){ f = t = drpFmt(now); }
  else if(preset === '7days'){ t = drpFmt(now); const d = new Date(now); d.setDate(d.getDate()-6); f = drpFmt(d); }
  else if(preset === 'thismonth'){ f = drpFmt(new Date(now.getFullYear(), now.getMonth(), 1)); t = drpFmt(now); }
  repDrpFrom = f; repDrpTo = t;
  const fd = drpParse(f);
  repDrpViewYear = fd.getFullYear(); repDrpViewMonth = fd.getMonth();
  repDrpRender();
}

function repDrpApply(){
  if(!repDrpFrom) return;
  repDrpTo = repDrpTo || repDrpFrom;
  repDateFrom = repDrpFrom;
  repDateTo   = repDrpTo;
  repDrpUpdateLabel();
  document.getElementById('repDrpPopup').classList.remove('open');
  document.getElementById('repDrpTrigger').classList.remove('active');
  renderReport();
}

function repDrpInit(){
  // default = เดือนนี้
  const now = new Date();
  repDrpFrom  = drpFmt(new Date(now.getFullYear(), now.getMonth(), 1));
  repDrpTo    = drpFmt(now);
  repDateFrom = repDrpFrom;
  repDateTo   = repDrpTo;
  repDrpUpdateLabel();

  const grid = document.getElementById('repDrpDays');
  if(grid){
    grid.addEventListener('mousedown',  repDrpStartDrag);
    document.addEventListener('mousemove', repDrpMoveDrag);
    document.addEventListener('mouseup',   repDrpEndDrag);
    grid.addEventListener('touchstart', repDrpStartDrag, {passive:false});
    document.addEventListener('touchmove', repDrpMoveDrag, {passive:false});
    document.addEventListener('touchend',  repDrpEndDrag);
  }

  // ปิด popup เมื่อคลิกนอก
  document.addEventListener('click', (e) => {
    const pop = document.getElementById('repDrpPopup');
    const trg = document.getElementById('repDrpTrigger');
    if(!pop || !pop.classList.contains('open')) return;
    if(pop.contains(e.target) || trg.contains(e.target)) return;
    pop.classList.remove('open');
    trg.classList.remove('active');
  });
}
function printReport(){
  console.log('[printReport] เริ่มทำงาน');
  try {
    // ── เตรียมข้อมูล ──
    const logs = getReportLogs();
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    const nowStr = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()+543} ${pad(now.getHours())}:${pad(now.getMinutes())}`;
    const docNo = `FR-${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}`;

    let period = '—';
    if(repView === 'day'){ period = drpFormatLabel(repDateFrom, repDateTo); }
    else if(repView === 'month'){
      const m = document.getElementById('repMonthPicker').value;
      if(m){ const [y,mo] = m.split('-'); period = `เดือน${TH_MONTHS[parseInt(mo)-1]} ${parseInt(y)+543}`; }
    } else if(repView === 'year'){
      const y = document.getElementById('repYearPicker').value;
      period = `ปี พ.ศ. ${parseInt(y)+543}`;
    } else {
      period = 'ทั้งหมด (ทุกช่วงเวลา)';
    }
    const driverFilter = document.getElementById('repDriverPicker').value;
    const driverLabel = driverFilter === 'all' ? 'ทั้งหมด' : driverFilter;

    // ── สรุป ──
    const totalLogs = logs.length;
    const totalDist = logs.reduce((s,r)=>s+(parseFloat(r.total_distance)||0),0);
    const totalPrice = logs.reduce((s,r)=>s+(parseFloat(r.total_price)||0),0);
    const totalLiters = logs.reduce((s,r)=>s+(parseFloat(r.liters)||0),0);
    const avgPrice = totalLogs>0 ? totalPrice/totalLogs : 0;
    const kmlVals = logs.filter(r=>parseFloat(r.km_per_liter)>0).map(r=>parseFloat(r.km_per_liter));
    const avgKml = kmlVals.length ? kmlVals.reduce((a,b)=>a+b,0)/kmlVals.length : 0;

    const sum = {};
    logs.forEach(log=>{
      const n = log.driver_name || 'ไม่ระบุ';
      if(!sum[n]) sum[n] = {driver:n, records:0, total_price:0, total_liters:0, total_dist:0, work_hours:0, kml_values:[]};
      sum[n].records++;
      sum[n].total_price  += parseFloat(log.total_price)||0;
      sum[n].total_liters += parseFloat(log.liters)||0;
      sum[n].total_dist   += parseFloat(log.total_distance)||0;
      sum[n].work_hours   += parseFloat(log.work_hours)||0;
      const kml = parseFloat(log.km_per_liter)||0;
      if(kml > 0) sum[n].kml_values.push(kml);
    });
    const driverList = Object.values(sum).sort((a,b)=>b.total_price-a.total_price);
    const maxDriver = driverList[0];
    const bestKml = driverList
      .map(d=>({n:d.driver, k: d.kml_values.length ? d.kml_values.reduce((a,b)=>a+b,0)/d.kml_values.length : 0}))
      .filter(x=>x.k>0).sort((a,b)=>b.k-a.k)[0];

    const fmt  = n => n>0 ? Number(n).toLocaleString('en-US',{maximumFractionDigits:1}) : '—';
    const fmt0 = n => n>0 ? Number(n).toLocaleString('en-US',{maximumFractionDigits:0}) : '—';
    const thDate = s => { if(!s) return '—'; const [y,m,d]=s.split('-'); return `${parseInt(d)}/${parseInt(m)}/${parseInt(y)+543}`; };

    const tableRows = driverList.map((d,i)=>{
      const avgK = d.kml_values.length ? d.kml_values.reduce((a,b)=>a+b,0)/d.kml_values.length : 0;
      return `<tr>
        <td class="c">${i+1}</td>
        <td>${d.driver}</td>
        <td class="r">${d.records}</td>
        <td class="r b">฿${fmt0(d.total_price)}</td>
        <td class="r">${fmt(d.total_liters)}</td>
        <td class="r">${fmt0(d.total_dist)}</td>
        <td class="r">${fmt(d.work_hours)}</td>
        <td class="r">${avgK>0?avgK.toFixed(1):'—'}</td>
      </tr>`;
    }).join('');
    const grandTotal = {
      records: driverList.reduce((s,d)=>s+d.records,0),
      price:   driverList.reduce((s,d)=>s+d.total_price,0),
      liters:  driverList.reduce((s,d)=>s+d.total_liters,0),
      dist:    driverList.reduce((s,d)=>s+d.total_dist,0),
      hours:   driverList.reduce((s,d)=>s+d.work_hours,0),
    };

    const detailRows = logs.slice().sort((a,b)=>{
      if((a.work_date||'') === (b.work_date||'')) return (a.driver_name||'').localeCompare(b.driver_name||'');
      return (b.work_date||'').localeCompare(a.work_date||'');
    }).map((r,i)=>`<tr>
      <td class="c">${i+1}</td>
      <td class="c">${thDate(r.work_date)}</td>
      <td>${r.driver_name||'—'}</td>
      <td class="r">${fmt(r.total_distance)}</td>
      <td class="r">${fmt(r.liters)}</td>
      <td class="r b">฿${fmt0(r.total_price)}</td>
      <td class="r">${parseFloat(r.km_per_liter)>0 ? parseFloat(r.km_per_liter).toFixed(1) : '—'}</td>
      <td class="r">${fmt(r.work_hours)}</td>
    </tr>`).join('');

    // ══════════════════════════════════════════════════════════════
    //  สร้าง HTML เอกสาร
    // ══════════════════════════════════════════════════════════════
    const docHTML = `
<div id="_printDoc" style="font-family:'Sarabun',sans-serif;color:#000;background:#fff;padding:20px;max-width:210mm;margin:0 auto">
  <div style="text-align:center;border-bottom:3px double #1a3a6b;padding-bottom:14px;margin-bottom:18px">
    <div style="font-size:9pt;color:#888;margin-top:4px">รายงานติดตามการใช้น้ำมันยานพาหนะ</div>
    <div style="display:inline-block;font-size:16pt;font-weight:700;color:#1a3a6b;margin-top:14px;padding:6px 24px;border:2px solid #1a3a6b;border-radius:4px">รายงานสรุปการใช้น้ำมันรถ</div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 18px;margin-bottom:18px;padding:10px 14px;border:1px solid #c8d1e0;background:#f8f9fc;border-radius:4px;font-size:10pt;-webkit-print-color-adjust:exact;print-color-adjust:exact">
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">📄 เลขที่เอกสาร:</span><span style="flex:1;font-weight:500">${docNo}</span></div>
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">🕐 วันที่พิมพ์:</span><span style="flex:1;font-weight:500">${nowStr} น.</span></div>
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">📅 ช่วงเวลา:</span><span style="flex:1;font-weight:500">${period}</span></div>
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">👤 คนขับ:</span><span style="flex:1;font-weight:500">${driverLabel}</span></div>
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">📊 จำนวนรายการ:</span><span style="flex:1;font-weight:500">${totalLogs} รายการ</span></div>
    <div style="display:flex;align-items:baseline"><span style="font-weight:700;color:#1a3a6b;min-width:110px;font-size:9.5pt">💰 ยอดรวม:</span><span style="flex:1;font-weight:500">฿${fmt0(totalPrice)}</span></div>
  </div>

  <div style="margin-bottom:16px">
    <div style="font-size:12pt;font-weight:700;color:#1a3a6b;margin-bottom:8px;padding:6px 10px;background:#e8eef8;border-left:4px solid #1a3a6b;-webkit-print-color-adjust:exact;print-color-adjust:exact">1. สรุปภาพรวม</div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:8px">
      ${['รายการทั้งหมด:'+totalLogs+':รายการ','ระยะทางรวม:'+fmt0(totalDist)+':กิโลเมตร','น้ำมันรวม:'+fmt(totalLiters)+':ลิตร','ค่าน้ำมันรวม:฿'+fmt0(totalPrice)+':บาท'].map(s=>{
        const [l,v,u]=s.split(':');
        return `<div style="padding:10px 8px;border:1px solid #c8d1e0;border-radius:3px;text-align:center"><div style="font-size:8.5pt;color:#666;margin-bottom:3px;font-weight:500">${l}</div><div style="font-size:14pt;font-weight:700;color:#1a3a6b;line-height:1.1">${v}</div><div style="font-size:8pt;color:#888;margin-top:2px">${u}</div></div>`;
      }).join('')}
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
      <div style="padding:10px 8px;border:1px solid #c8d1e0;border-radius:3px;text-align:center"><div style="font-size:8.5pt;color:#666;margin-bottom:3px;font-weight:500">เฉลี่ย ฿/ครั้ง</div><div style="font-size:14pt;font-weight:700;color:#1a3a6b">${avgPrice>0?'฿'+fmt0(avgPrice):'—'}</div><div style="font-size:8pt;color:#888;margin-top:2px">บาท/ครั้ง</div></div>
      <div style="padding:10px 8px;border:1px solid #c8d1e0;border-radius:3px;text-align:center"><div style="font-size:8.5pt;color:#666;margin-bottom:3px;font-weight:500">เฉลี่ย km/L</div><div style="font-size:14pt;font-weight:700;color:#1a3a6b">${avgKml>0?avgKml.toFixed(1):'—'}</div><div style="font-size:8pt;color:#888;margin-top:2px">กม./ลิตร</div></div>
      ${maxDriver?`<div style="padding:10px 8px;border:1px solid #f5a623;border-radius:3px;text-align:center;background:#fffbf0;-webkit-print-color-adjust:exact;print-color-adjust:exact"><div style="font-size:8.5pt;color:#666;margin-bottom:3px;font-weight:500">ใช้น้ำมันสูงสุด</div><div style="font-size:11pt;font-weight:700;color:#b45309">${maxDriver.driver}</div><div style="font-size:8pt;color:#888;margin-top:2px">฿${fmt0(maxDriver.total_price)}</div></div>`:'<div></div>'}
      ${bestKml?`<div style="padding:10px 8px;border:1px solid #f5a623;border-radius:3px;text-align:center;background:#fffbf0;-webkit-print-color-adjust:exact;print-color-adjust:exact"><div style="font-size:8.5pt;color:#666;margin-bottom:3px;font-weight:500">ประหยัดน้ำมันที่สุด</div><div style="font-size:11pt;font-weight:700;color:#b45309">${bestKml.n}</div><div style="font-size:8pt;color:#888;margin-top:2px">${bestKml.k.toFixed(1)} km/L</div></div>`:'<div></div>'}
    </div>
  </div>

  <div style="margin-bottom:16px">
    <div style="font-size:12pt;font-weight:700;color:#1a3a6b;margin-bottom:8px;padding:6px 10px;background:#e8eef8;border-left:4px solid #1a3a6b;-webkit-print-color-adjust:exact;print-color-adjust:exact">2. สรุปแยกตามคนขับ (เรียงตามค่าน้ำมันสูงสุด)</div>
    <table class="printdoc-table" style="width:100%;border-collapse:collapse;font-size:9.5pt">
      <thead>
        <tr>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:center;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact;width:32px">ลำดับ</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:left;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ชื่อคนขับ</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ครั้ง</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ค่าน้ำมัน</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ลิตร</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ระยะทาง</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ชม.</th>
          <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">km/L</th>
        </tr>
      </thead>
      <tbody>
        ${tableRows||'<tr><td colspan="8" style="text-align:center;padding:20px;color:#888">ไม่พบข้อมูล</td></tr>'}
        ${driverList.length?`<tr style="background:#e8eef8;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact">
          <td colspan="2" style="padding:6px 8px;border:1px solid #d7dde8;text-align:center;color:#1a3a6b">รวมทั้งหมด</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">${grandTotal.records}</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">฿${fmt0(grandTotal.price)}</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">${fmt(grandTotal.liters)}</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">${fmt0(grandTotal.dist)}</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">${fmt(grandTotal.hours)}</td>
          <td style="padding:6px 8px;border:1px solid #d7dde8;text-align:right;color:#1a3a6b">—</td>
        </tr>`:''}
      </tbody>
    </table>
    <style>.printdoc-table tbody td{padding:5px 8px;border:1px solid #d7dde8;font-size:9.5pt} .printdoc-table tbody td.c{text-align:center} .printdoc-table tbody td.r{text-align:right} .printdoc-table tbody td.b{font-weight:700;color:#1a3a6b}</style>
  </div>

  ${logs.length>0&&logs.length<=100?`<div style="margin-bottom:16px;page-break-before:always">
    <div style="font-size:12pt;font-weight:700;color:#1a3a6b;margin-bottom:8px;padding:6px 10px;background:#e8eef8;border-left:4px solid #1a3a6b;-webkit-print-color-adjust:exact;print-color-adjust:exact">3. รายการเติมน้ำมันโดยละเอียด</div>
    <table class="printdoc-table" style="width:100%;border-collapse:collapse;font-size:9.5pt">
      <thead><tr>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:center;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact;width:32px">#</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:center;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact;width:85px">วันที่</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:left;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">คนขับ</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ระยะทาง</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ลิตร</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ค่าน้ำมัน</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">km/L</th>
        <th style="background:#1a3a6b;color:#fff;padding:7px 8px;text-align:right;border:1px solid #122956;-webkit-print-color-adjust:exact;print-color-adjust:exact">ชม.</th>
      </tr></thead>
      <tbody>${detailRows}</tbody>
    </table>
  </div>`:''}

  <div style="margin-top:18px;padding:8px 12px;border-left:3px solid #4f8ef7;background:#f0f5ff;font-size:9pt;color:#555;-webkit-print-color-adjust:exact;print-color-adjust:exact">
    <strong>หมายเหตุ:</strong> รายงานฉบับนี้สร้างขึ้นโดยอัตโนมัติจากระบบ ข้อมูลอ้างอิงจากการบันทึกการเติมน้ำมันในฐานข้อมูล ณ วันที่พิมพ์
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:40px;page-break-inside:avoid">
    <div style="text-align:center">
      <div style="height:48px"></div>
      <div style="border-bottom:1px dotted #000;margin:0 15px 6px"></div>
      <div style="font-size:10pt;color:#333">(............................................)</div>
      <div style="font-size:9.5pt;color:#1a3a6b;font-weight:700;margin-top:3px">ผู้จัดทำรายงาน</div>
      <div style="font-size:9pt;color:#666;margin-top:8px">วันที่ ............/............/............</div>
    </div>
    <div style="text-align:center">
      <div style="height:48px"></div>
      <div style="border-bottom:1px dotted #000;margin:0 15px 6px"></div>
      <div style="font-size:10pt;color:#333">(............................................)</div>
      <div style="font-size:9.5pt;color:#1a3a6b;font-weight:700;margin-top:3px">ผู้ตรวจสอบ / อนุมัติ</div>
      <div style="font-size:9pt;color:#666;margin-top:8px">วันที่ ............/............/............</div>
    </div>
  </div>
</div>

<style id="_printDocStyle">
  @media print{
    @page { size: A4 portrait; margin: 12mm; }
    body > *:not(#_printDoc){display:none!important}
    #_printDoc{padding:0!important;margin:0!important;max-width:none!important}
    .no-print{display:none!important}
  }
  @media screen{
    body > *:not(#_printDoc):not(#_printBar){display:none!important}
    body{background:#eef0f5!important;padding:20px}
    #_printDoc{background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.15);border-radius:4px;margin:60px auto 20px}
  }
  #_printBar{position:fixed;top:15px;right:15px;background:#1a3a6b;padding:10px 14px;border-radius:8px;display:flex;gap:8px;box-shadow:0 4px 16px rgba(0,0,0,.3);z-index:99999;font-family:'Sarabun',sans-serif}
  #_printBar button{padding:8px 16px;border:none;border-radius:5px;font-family:'Sarabun',sans-serif;font-size:11pt;font-weight:600;cursor:pointer}
  #_printBar .pb-p{background:#4f8ef7;color:#fff}
  #_printBar .pb-c{background:#fff;color:#1a3a6b}
  @media print{#_printBar{display:none!important}}
</style>
<div id="_printBar">
  <button class="pb-p" onclick="window.print()">🖨️ พิมพ์ / บันทึก PDF</button>
  <button class="pb-c" onclick="closePrintDoc()">✕ ปิด</button>
</div>`;

    // ══════════════════════════════════════════════════════════════
    //  แทรก HTML ลง body (ไม่ลบของเดิม) + ซ่อนของเดิมด้วย CSS
    // ══════════════════════════════════════════════════════════════
    // เก็บ scroll position
    window._printScrollY = window.scrollY;

    // Container ชั่วคราว
    const container = document.createElement('div');
    container.id = '_printContainer';
    container.innerHTML = docHTML;
    document.body.appendChild(container);

    // ย้าย doc + bar + style ออกจาก container มาไว้ที่ body โดยตรง
    // (เพราะ CSS selector body > * ต้องชี้ตรง ๆ)
    const doc = container.querySelector('#_printDoc');
    const bar = container.querySelector('#_printBar');
    const sty = container.querySelector('#_printDocStyle');
    if(doc) document.body.appendChild(doc);
    if(bar) document.body.appendChild(bar);
    if(sty) document.head.appendChild(sty);
    container.remove();

    // scroll top
    window.scrollTo(0, 0);

    // สร้าง close function
    window.closePrintDoc = function(){
      document.getElementById('_printDoc')?.remove();
      document.getElementById('_printBar')?.remove();
      document.getElementById('_printDocStyle')?.remove();
      window.scrollTo(0, window._printScrollY || 0);
    };

    console.log('[printReport] สำเร็จ — แสดงเอกสารแล้ว');
  } catch(err) {
    console.error('[printReport] ERROR:', err);
    alert('เกิดข้อผิดพลาด: ' + err.message + '\n\nดูรายละเอียดใน Console (F12)');
  }
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
  renderOilPage();
  drpInit();
  repDrpInit();

  @if($errors->any())
  openModal({{ isset($editLog['id']) ? $editLog['id'] : 'null' }});
  @endif
});
</script>
</body>
</html>
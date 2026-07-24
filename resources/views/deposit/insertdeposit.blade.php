<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>สร้างใบมัดจำ — ระบบจัดการเอกสาร</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#3E6AE1; --primary-hover:#2f56c4; --primary-light:#eef2fd; --primary-mid:#5a85e8; --primary-border:#c7d5f5;
  --blue:#3E6AE1; --blue-hover:#2f56c4; --blue-light:#eef2fd; --blue-border:#c7d5f5;
  --amber:#f59e0b; --amber-hover:#d97706; --amber-light:#fef3c7; --amber-border:#fde68a;
  --green:#16a34a; --green-light:#dcfce7; --green-dark:#15803d;
  --red:#dc2626; --red-light:#fee2e2; --red-dark:#b91c1c;
  --surface:#ffffff; --bg:#f5f7fa; --bg-alt:#eef1f5;
  --border:#e5e7eb; --border-light:#f0f2f5; --border-strong:#d1d5db;
  --text:#1b2d4f; --text-secondary:#374151; --text-muted:#6b7280; --text-hint:#9ca3af;
  --shadow-xs:0 1px 2px rgba(62,106,225,.06); --shadow-sm:0 2px 8px rgba(62,106,225,.08);
  --shadow-md:0 4px 16px rgba(62,106,225,.10); --shadow-lg:0 8px 32px rgba(62,106,225,.14);
  --r-sm:4px; --r-md:6px; --r-lg:12px; --r-xl:16px;
  --font-thai:'Sarabun',system-ui,sans-serif; --font-mono:'JetBrains Mono',ui-monospace,monospace;
  --t-fast:.12s ease; --t-base:.2s ease;
}
body{font-family:var(--font-thai);font-size:15px;background:var(--bg);color:var(--text);line-height:1.6;min-height:100vh;-webkit-font-smoothing:antialiased}
#loadingOverlay{display:none;position:fixed;inset:0;background:rgba(255,255,255,.92);backdrop-filter:blur(4px);z-index:9999;justify-content:center;align-items:center;flex-direction:column;gap:16px}
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:44px;height:44px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite}
#loadingMsg{font-size:14px;color:var(--text-secondary);font-weight:500}
#toast-container{position:fixed;top:24px;right:24px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow-lg);min-width:280px;max-width:380px;pointer-events:all;animation:toastIn .25s ease forwards;border-left:4px solid var(--primary)}
.toast.toast-error{border-left-color:var(--red)}
.toast.toast-warning{border-left-color:var(--amber)}
.toast-icon{flex-shrink:0;margin-top:1px}
.toast-body{flex:1;min-width:0}
.toast-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px}
.toast-msg{font-size:13px;color:var(--text-secondary);line-height:1.5}
.toast-close{flex-shrink:0;background:none;border:none;cursor:pointer;color:var(--text-muted);padding:2px;border-radius:var(--r-sm);line-height:1;transition:color var(--t-fast)}
.toast-close:hover{color:var(--text)}
@keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
@keyframes toastOut{to{opacity:0;transform:translateX(20px)}}
.navbar{background:var(--surface);border-bottom:1px solid var(--border);box-shadow:0 2px 6px rgba(0,0,0,.04);position:sticky;top:0;z-index:100}
.navbar-inner{max-width:1200px;margin:0 auto;padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.navbar-brand{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:var(--primary);text-decoration:none;flex-shrink:0}
.navbar-brand-icon{width:34px;height:34px;background:var(--primary);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center}
.navbar-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-muted)}
.navbar-breadcrumb a{color:var(--text-muted);text-decoration:none;transition:color var(--t-fast)}
.navbar-breadcrumb a:hover{color:var(--primary)}
.navbar-breadcrumb .sep{color:var(--text-hint)}
.navbar-breadcrumb .current{color:var(--text);font-weight:600}
.navbar-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.btn-nav{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:var(--r-md);border:1px solid var(--border);background:var(--surface);color:var(--text-secondary);font-size:13px;font-weight:500;cursor:pointer;transition:all var(--t-fast);font-family:inherit;white-space:nowrap}
.btn-nav:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-light)}
.page{max-width:1200px;margin:0 auto;padding:28px 28px 60px}
.page-header{margin-bottom:28px;padding:24px 28px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}
.page-header-left{display:flex;align-items:center;gap:16px}
.page-header-icon{width:52px;height:52px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);border-radius:var(--r-lg);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(62,106,225,.30);flex-shrink:0}
.page-header-text h1{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px}
.page-header-text p{font-size:13px;color:var(--text-secondary);margin-top:3px}
.grid-layout{display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start}
.col-main{display:flex;flex-direction:column;gap:18px}
.col-side{display:flex;flex-direction:column;gap:16px;position:sticky;top:80px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow var(--t-base)}
.card:hover{box-shadow:var(--shadow-md)}
.card-head{padding:18px 24px 15px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;gap:12px;background:var(--surface)}
.card-icon{width:38px;height:38px;border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ci-green{background:var(--primary-light);border:1px solid var(--primary-border)}
.ci-blue{background:#dbeafe;border:1px solid #bfdbfe}
.ci-amber{background:var(--amber-light);border:1px solid var(--amber-border)}
.card-head-text h3{font-size:15px;font-weight:700;color:var(--text)}
.card-head-text p{font-size:12px;color:var(--text-muted);margin-top:2px}
.card-badge{margin-left:auto;padding:3px 10px;border-radius:var(--r-sm);font-size:11px;font-weight:600}
.badge-required{background:var(--red-light);color:var(--red-dark)}
.badge-optional{background:var(--bg-alt);color:var(--text-muted);border:1px solid var(--border)}
.sale-chip{margin-left:auto;display:inline-flex;align-items:center;gap:8px;padding:6px 12px;background:var(--primary-light);border:1px solid var(--primary-border);border-radius:var(--r-md);font-size:12px;color:var(--text-secondary);max-width:280px;min-width:0}
.sale-chip-label{font-weight:700;color:var(--primary);white-space:nowrap;font-size:11px;letter-spacing:.04em;text-transform:uppercase}
.sale-chip-value{font-weight:700;color:var(--text);font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0}
.sale-chip-icon{flex-shrink:0;display:flex;align-items:center;justify-content:center;width:22px;height:22px;background:var(--surface);border:1px solid var(--primary-border);border-radius:var(--r-sm)}
.card-body{padding:22px 24px}
.section-divider{display:flex;align-items:center;gap:10px;padding:0 24px;margin-bottom:16px}
.section-divider-line{flex:1;height:1px;background:var(--border-light)}
.section-divider-label{font-size:11px;font-weight:700;color:var(--text-muted);letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}
.form-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.form-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.span-2{grid-column:span 2}
.span-full{grid-column:1/-1}
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-size:12px;font-weight:700;color:var(--text-secondary);letter-spacing:.04em;display:flex;align-items:center;gap:5px}
.field label .req{color:var(--red);font-size:14px;line-height:1}
.field label .tip{display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;background:var(--bg-alt);border:1px solid var(--border);border-radius:3px;font-size:10px;color:var(--text-muted);cursor:help;font-style:normal;font-weight:700;position:relative}
.field label .tip:hover::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--text);color:#fff;font-size:11px;font-weight:400;padding:5px 9px;border-radius:var(--r-sm);white-space:nowrap;pointer-events:none;z-index:50;letter-spacing:0}
.field-wrap{position:relative}
.field input[type="text"],.field input[type="number"],.field textarea{padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--r-md);background:var(--surface);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast);width:100%}
.field input[type="text"]:focus,.field input[type="number"]:focus,.field textarea:focus{border-color:var(--primary);background:var(--surface);box-shadow:0 0 0 3px rgba(62,106,225,.12)}
.field textarea{resize:vertical;min-height:70px;line-height:1.5;font-family:inherit}
.field textarea::placeholder{color:var(--text-hint)}
.field input[readonly]{background:var(--bg);color:var(--text-secondary);cursor:default;border-style:dashed;border-color:var(--border)}
.field input[readonly]:focus{border-color:var(--border);box-shadow:none}
.field input::placeholder{color:var(--text-hint)}
.readonly-badge{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:10px;font-weight:600;color:var(--text-hint);background:var(--bg-alt);padding:2px 6px;border-radius:var(--r-sm);pointer-events:none}
.dep-section{padding:0 24px 22px}
.dep-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.dep-card{border:2px solid var(--border);border-radius:var(--r-lg);padding:16px;transition:all var(--t-base);background:var(--surface);cursor:pointer}
.dep-card:hover{border-color:var(--primary-mid);box-shadow:var(--shadow-sm);transform:translateY(-1px)}
.dep-card.active-g{border-color:var(--primary);background:linear-gradient(135deg,var(--primary-light),#f8faff);box-shadow:0 4px 16px rgba(62,106,225,.14)}
.dep-card.active-b{border-color:var(--blue);background:linear-gradient(135deg,var(--blue-light),#f8faff);box-shadow:0 4px 16px rgba(62,106,225,.14)}
.dep-card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;user-select:none}
.dep-card-info{display:flex;align-items:center;gap:10px}
.dep-ico{width:34px;height:34px;border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.di-g{background:var(--primary-light);border:1px solid var(--primary-border)}
.di-b{background:var(--blue-light);border:1px solid var(--blue-border)}
.dep-card-name{font-size:14px;font-weight:700;color:var(--text)}
.dep-card-sub{font-size:11px;color:var(--text-muted);margin-top:2px}
.dep-toggle{width:42px;height:24px;border-radius:12px;background:#d1d5db;position:relative;transition:background var(--t-base);flex-shrink:0;cursor:pointer;border:none;outline:none;padding:0}
.dep-toggle.on-g{background:var(--primary)}
.dep-toggle.on-b{background:var(--blue)}
.dep-toggle-knob{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:left var(--t-base);box-shadow:0 1px 4px rgba(0,0,0,.18)}
.dep-toggle.on-g .dep-toggle-knob,.dep-toggle.on-b .dep-toggle-knob{left:21px}
.mode-tabs{display:flex;border:1.5px solid var(--border);border-radius:var(--r-md);overflow:hidden;background:var(--bg);margin-bottom:8px;flex-shrink:0}
.mode-tab{padding:4px 14px;border:none;background:transparent;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;color:var(--text-muted);transition:all var(--t-fast);letter-spacing:.03em}
.mode-tab:hover{color:var(--text)}
.mode-tab.on-g{background:var(--primary);color:#fff}
.mode-tab.on-b{background:var(--blue);color:#fff}
.pct-wrap{display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--r-md);background:var(--surface);overflow:hidden;transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast)}
.pct-wrap:focus-within{border-color:var(--primary);box-shadow:0 0 0 3px rgba(62,106,225,.12)}
.pct-wrap.bfocus:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px rgba(62,106,225,.12)}
.pct-wrap input{border:none;background:transparent;padding:9px 10px;font-size:14px;font-family:var(--font-mono);color:var(--text);outline:none;width:100%;text-align:right}
.pct-wrap input:disabled{color:var(--text-hint);cursor:not-allowed}
.pct-unit{padding:0 11px 0 4px;font-size:13px;color:var(--text-secondary);font-weight:700;white-space:nowrap;min-width:28px;font-family:var(--font-mono)}
.dep-result{font-size:12px;font-weight:600;margin-top:9px;min-height:22px;padding:4px 10px;border-radius:var(--r-sm);display:inline-block;transition:all var(--t-fast);font-family:var(--font-mono)}
.dep-result.g{background:var(--primary-light);color:var(--primary-hover)}
.dep-result.b{background:var(--blue-light);color:var(--blue-hover)}
.dep-result.empty{background:transparent;color:var(--text-hint);padding-left:0;font-weight:400;font-size:11px;font-family:var(--font-thai)}
.selection-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;margin-bottom:12px;background:var(--primary-light);border:1px solid var(--primary-border);border-radius:var(--r-md);flex-wrap:wrap}
.selection-info{display:flex;align-items:center;gap:10px;font-size:12px;color:var(--text-secondary);flex-wrap:wrap}
.selection-badge{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;background:var(--surface);border:1px solid var(--primary-border);border-radius:var(--r-sm);font-weight:600;color:var(--primary);font-size:12px}
.selection-badge strong{font-weight:800;font-size:13px;font-family:var(--font-mono)}
.selection-amount{font-weight:700;color:var(--text);font-variant-numeric:tabular-nums;font-family:var(--font-mono)}
.selection-actions{display:flex;align-items:center;gap:6px}
.btn-link{background:none;border:none;color:var(--primary);font-size:12px;font-weight:600;cursor:pointer;padding:4px 8px;border-radius:var(--r-sm);font-family:inherit;transition:all var(--t-fast)}
.btn-link:hover{background:var(--surface);color:var(--primary-hover)}
.btn-link.warn{color:var(--red)}
.btn-link.warn:hover{background:var(--red-light)}
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:var(--r-md);border:1px solid var(--border)}
table{width:100%;border-collapse:collapse;font-size:14px;table-layout:fixed}
thead{background:var(--primary)}
th{padding:11px 16px;font-size:11px;font-weight:700;color:#fff;letter-spacing:.07em;text-transform:uppercase;border-bottom:none;border-right:1px solid rgba(255,255,255,.15);text-align:left;white-space:nowrap}
th:last-child{border-right:none}
td{padding:12px 16px;border-bottom:1px solid var(--border-light);border-right:1px solid var(--border-light);vertical-align:middle}
td:last-child{border-right:none}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:var(--primary-light)}
tbody tr.row-unchecked td{background:#fafafa;opacity:.55}
tbody tr.row-unchecked:hover td{opacity:.75;background:var(--primary-light)}
tbody tr.row-checked td{background:#fbfdff}
.td-num{text-align:right;font-variant-numeric:tabular-nums;color:var(--text);font-family:var(--font-mono);font-size:13px}
.td-dep{text-align:right;font-variant-numeric:tabular-nums;font-weight:600;color:var(--red);font-family:var(--font-mono);font-size:13px}
.td-name{color:var(--text)}
.td-check{text-align:center;padding:12px 8px}
.tbl-empty{padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px}
.tbl-empty-icon{margin-bottom:10px;opacity:.4}
.chk{position:relative;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;width:20px;height:20px;flex-shrink:0}
.chk input{position:absolute;opacity:0;width:0;height:0;pointer-events:none}
.chk-box{width:18px;height:18px;border:1.8px solid var(--text-hint);background:var(--surface);transition:all var(--t-fast);display:flex;align-items:center;justify-content:center;border-radius:var(--r-sm)}
.chk:hover .chk-box{border-color:var(--primary)}
.chk input:checked + .chk-box{background:var(--primary);border-color:var(--primary)}
.chk input:checked + .chk-box::after{content:'';width:5px;height:9px;border:solid #fff;border-width:0 2px 2px 0;transform:rotate(45deg);margin-top:-2px}
.chk input:indeterminate + .chk-box{background:var(--primary);border-color:var(--primary)}
.chk input:indeterminate + .chk-box::after{content:'';width:9px;height:2px;background:#fff}
.chk input:focus-visible + .chk-box{box-shadow:0 0 0 3px rgba(62,106,225,.20)}
.summary-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow-sm)}
.summary-head{padding:16px 20px;border-bottom:1px solid var(--border-light);font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;background:var(--surface)}
.summary-rows{padding:16px 20px;display:flex;flex-direction:column;gap:10px}
.s-row{display:flex;justify-content:space-between;align-items:center;font-size:13px}
.s-row .slbl{color:var(--text-secondary)}
.s-row .sval{font-weight:600;color:var(--text);font-variant-numeric:tabular-nums;font-family:var(--font-mono)}
.s-row.neg .sval{color:var(--red)}
.s-row.divider-row{border-top:1px dashed var(--border);padding-top:10px;margin-top:2px}
.s-row.total{margin-top:6px;padding:14px 0 0;border-top:2px solid var(--border-light)}
.s-row.total .slbl{font-weight:700;color:var(--text);font-size:14px}
.s-row.total .sval{font-size:24px;font-weight:800;color:var(--primary);letter-spacing:-.5px}
.pdf-preview-section{border-top:1px solid var(--border-light);padding:14px 20px 16px;background:var(--bg)}
.pdf-preview-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px}
.pdf-preview-title{display:flex;align-items:center;gap:7px;font-size:11px;font-weight:700;color:var(--text-secondary);letter-spacing:.06em;text-transform:uppercase}
.pdf-preview-actions{display:flex;align-items:center;gap:6px}
.pdf-action-btn{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid var(--border);background:var(--surface);color:var(--text-secondary);cursor:pointer;transition:all var(--t-fast);text-decoration:none;border-radius:var(--r-sm)}
.pdf-action-btn:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-light)}
.pdf-frame-wrap{position:relative;border:1px solid var(--border);background:var(--bg);height:240px;overflow:hidden;border-radius:var(--r-md)}
.pdf-frame-wrap iframe{width:100%;height:100%;border:none;display:block;background:#fff}
.pdf-frame-fallback{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:16px;text-align:center;background:var(--bg)}
.pdf-frame-fallback-icon{width:42px;height:42px;background:var(--primary-light);border:1px solid var(--primary-border);display:flex;align-items:center;justify-content:center;border-radius:var(--r-md)}
.pdf-frame-fallback-text{font-size:12px;color:var(--text-secondary);font-weight:500}
.pdf-meta{display:flex;align-items:center;justify-content:space-between;margin-top:8px;font-size:11px;color:var(--text-muted)}
.pdf-meta-name{font-weight:600;color:var(--text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;font-family:var(--font-mono)}
.pdf-modal{display:none;position:fixed;inset:0;background:rgba(27,45,79,.75);backdrop-filter:blur(4px);z-index:9998;align-items:center;justify-content:center;padding:20px}
.pdf-modal.show{display:flex}
.pdf-modal-content{background:var(--surface);width:100%;max-width:1000px;height:90vh;display:flex;flex-direction:column;box-shadow:var(--shadow-lg);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden}
.pdf-modal-head{padding:14px 20px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--surface)}
.pdf-modal-title{display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:var(--text)}
.pdf-modal-actions{display:flex;align-items:center;gap:8px}
.pdf-modal-body{flex:1;background:#525659;overflow:hidden}
.pdf-modal-body iframe{width:100%;height:100%;border:none;display:block}
.summary-footer{padding:14px 20px 20px;border-top:1px solid var(--border-light)}
.btn-save{width:100%;padding:14px;border-radius:var(--r-md);border:none;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);color:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:all var(--t-base);font-family:inherit;letter-spacing:.01em;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 12px rgba(62,106,225,.30)}
.btn-save:hover{background:linear-gradient(135deg,var(--primary-hover) 0%,var(--primary) 100%);transform:translateY(-1px);box-shadow:0 6px 18px rgba(62,106,225,.36)}
.btn-save:active{transform:scale(.98);box-shadow:var(--shadow-sm)}
.btn-save:disabled{background:linear-gradient(135deg,#94a3b8,#cbd5e1);cursor:not-allowed;transform:none;box-shadow:none}
.btn-save-hint{text-align:center;font-size:11px;color:var(--text-muted);margin-top:8px}
.info-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow-xs)}
.info-card-head{padding:13px 16px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--text-secondary);letter-spacing:.05em;text-transform:uppercase;background:var(--surface)}
.info-items{padding:4px 0}
.info-item{display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:10px 16px;border-bottom:1px solid var(--border-light);gap:12px}
.info-item:last-child{border-bottom:none}
.info-item .ik{color:var(--text-muted);flex-shrink:0}
.info-item .iv{font-weight:600;color:var(--text);text-align:right;min-width:0;word-break:break-all;font-family:var(--font-mono);font-size:12px}
.info-item .iv.status-active{display:inline-flex;align-items:center;gap:5px;color:var(--primary);background:var(--primary-light);padding:3px 9px;border-radius:var(--r-sm);font-size:12px;font-family:var(--font-thai)}
.help-card{background:var(--primary-light);border:1px solid var(--primary-border);border-radius:var(--r-lg);padding:16px}
.help-card-head{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--primary);letter-spacing:.05em;text-transform:uppercase;margin-bottom:12px}
.help-item{display:flex;align-items:flex-start;gap:9px;font-size:12px;color:var(--text-secondary);padding:6px 0;border-bottom:1px solid rgba(199,213,245,.6)}
.help-item:last-child{border-bottom:none;padding-bottom:0}
.help-num{width:18px;height:18px;background:var(--surface);border:1px solid var(--primary-border);border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--primary);flex-shrink:0;margin-top:1px;font-family:var(--font-mono)}
hr.divider{border:none;border-top:1px solid var(--border-light);margin:0}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
@media(max-width:1100px){
  .page{padding:22px 20px 50px}
  .grid-layout{grid-template-columns:1fr}
  .col-side{position:static;display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .form-grid-3{grid-template-columns:repeat(3,1fr)}
  .form-grid-4{grid-template-columns:repeat(2,1fr)}
  .dep-cards{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:768px){
  .page{padding:16px 16px 50px}
  .navbar-inner{padding:0 16px}
  .navbar-breadcrumb{display:none}
  .page-header{padding:18px 20px}
  .page-header-text h1{font-size:18px}
  .col-side{grid-template-columns:1fr}
  .dep-cards{grid-template-columns:1fr}
  .card-body{padding:16px 18px}
  .card-head{padding:14px 18px 12px;flex-wrap:wrap}
  .dep-section{padding:0 18px 18px}
  table{font-size:13px}
  th,td{padding:10px 12px}
  .s-row.total .sval{font-size:20px}
  .form-grid-3{grid-template-columns:1fr 1fr}
  .form-grid-4{grid-template-columns:1fr}
  .span-2{grid-column:1}
  .page-header-icon{width:42px;height:42px}
  .sale-chip{margin-left:0;width:100%;max-width:none;margin-top:4px}
  .pdf-frame-wrap{height:200px}
  .pdf-modal-content{height:95vh}
}
@media(max-width:480px){
  .page{padding:12px 12px 50px}
  .page-header{padding:14px 16px}
  .card-body,.card-head{padding:14px}
  .dep-section{padding:0 14px 14px}
  table{min-width:560px}
  .btn-save{padding:13px}
  .summary-rows,.summary-footer{padding:14px 16px}
  .pdf-preview-section{padding:12px 16px 14px}
  .form-grid-3{grid-template-columns:1fr}
}
</style>
</head>
<body>

<div id="loadingOverlay"><div class="spinner"></div><span id="loadingMsg">กำลังโหลดข้อมูล...</span></div>
<div id="toast-container"></div>

<nav class="navbar">
  <div class="navbar-inner">
    <a href="/dashboard" class="navbar-brand">
      <div class="navbar-brand-icon">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <rect x="2" y="2" width="6" height="6" rx="1.5" fill="white" opacity=".9"/>
          <rect x="10" y="2" width="6" height="6" rx="1.5" fill="white" opacity=".6"/>
          <rect x="2" y="10" width="6" height="6" rx="1.5" fill="white" opacity=".6"/>
          <rect x="10" y="10" width="6" height="6" rx="1.5" fill="white" opacity=".9"/>
        </svg>
      </div>
      ระบบจัดการเอกสาร
    </a>
    <div class="navbar-breadcrumb"></div>
    <div class="navbar-actions">
      <button class="btn-nav" onclick="window.location.href='http://server_update:8000/solist'">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <path d="M9 11L5 7L9 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        ย้อนกลับ
      </button>
    </div>
  </div>
</nav>

<div class="page">
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
          <rect x="4" y="3" width="16" height="20" rx="3" stroke="white" stroke-width="1.8"/>
          <path d="M8 9h10M8 13h10M8 17h6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="20" cy="20" r="5" fill="white" opacity=".15"/>
          <path d="M18 20l1.5 1.5L22 18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="page-header-text">
        <h1>สร้างใบมัดจำ</h1>
        <p>กรอกข้อมูลและกำหนดอัตราการมัดจำก่อนบันทึกเอกสาร</p>
      </div>
    </div>
  </div>

  <form id="billForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="so_id"        id="so_id">
    <input type="hidden" name="subtotal"      id="hidden-subtotal"   value="0">
    <input type="hidden" name="discount"      id="hidden-discount"   value="0">
    <input type="hidden" name="grand_total"   id="hidden-grandtotal" value="0">
    <input type="hidden" name="sale_name"     id="hidden-sale"       value="">
    <input type="hidden" name="po_document"   id="hidden-po"         value="">
    <input type="hidden" name="emp_name"      id="hidden-emp" value="{{ request()->filled('create_by') ? request('create_by') : 'Guest' }}">

    <div class="grid-layout">
      <div class="col-main">
        <!-- Card 1: ข้อมูลเอกสาร -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-green">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <rect x="3" y="2" width="12" height="14" rx="2.5" stroke="#3E6AE1" stroke-width="1.5"/>
                <path d="M6 6h6M6 9h6M6 12h4" stroke="#3E6AE1" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>ข้อมูลเอกสาร</h3>
              <p>Sales Order และวันที่ออกเอกสาร</p>
            </div>
            <div class="sale-chip" id="sale-chip" title="ชื่อ Sale ผู้รับผิดชอบเอกสาร">
              <div class="sale-chip-icon">
                <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                  <circle cx="7" cy="5" r="2.6" stroke="#3E6AE1" stroke-width="1.4"/>
                  <path d="M2 12.5c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="#3E6AE1" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
              </div>
              <span class="sale-chip-label">ชื่อ Sale:</span>
              <span class="sale-chip-value" id="sale-chip-value">—</span>
            </div>
          </div>
          <div class="card-body">
            <div class="form-grid-4">
              <div class="field">
                <label>เลขที่ SO <i class="tip" data-tip="รหัส Sales Order จากระบบ">?</i></label>
                <div class="field-wrap">
                  <input type="text" id="so_number" name="so_number" readonly placeholder="SO-XXXX">
                </div>
              </div>
              <div class="field">
                <label>เลขที่ PO ลูกค้า <i class="tip" data-tip="เลขที่ใบสั่งซื้อจากลูกค้า">?</i></label>
                <div class="field-wrap">
                  <input type="text" id="billid" name="billid" readonly placeholder="—">
                </div>
              </div>
              <div class="field">
                <label>วันที่ออกเอกสาร</label>
                <div class="field-wrap">
                  <input type="text" id="sell_date" name="sell_date" readonly placeholder="DD-MM-YYYY">
                </div>
              </div>
              <input type="hidden" id="tax_id" name="tax_id">
              <div class="field">
                <label>รหัสลูกค้า</label>
                <div class="field-wrap">
                  <input type="text" id="customer_id" name="customer_id" readonly placeholder="—">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: ข้อมูลลูกค้า -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-blue">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="6" r="3.2" stroke="#3E6AE1" stroke-width="1.5"/>
                <path d="M2.5 15.5c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="#3E6AE1" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>ข้อมูลลูกค้า</h3>
              <p>ชื่อบริษัท ผู้ติดต่อ เบอร์โทร และที่อยู่จัดส่ง</p>
            </div>
            <span class="card-badge badge-optional">แก้ไขได้</span>
          </div>
          <div class="card-body">
            <div class="form-grid-4" style="row-gap:16px">
              <div class="field span-full">
                <label>ชื่อบริษัท / ลูกค้า</label>
                <div class="field-wrap">
                  <input type="text" id="customer_name" name="customer_name" readonly placeholder="ชื่อบริษัทหรือลูกค้า">
                </div>
              </div>
              <div class="field span-2">
                <label>ชื่อผู้ติดต่อ <span class="req">*</span></label>
                <input type="text" id="contactso" name="contactso" placeholder="กรอกชื่อผู้ติดต่อ">
              </div>
              <div class="field span-2">
                <label>เบอร์ติดต่อ</label>
                <input type="text" id="customer_tel" name="customer_tel" placeholder="0XX-XXX-XXXX">
              </div>
              <div class="field span-full">
                <label>ที่อยู่จัดส่ง <i class="tip" data-tip="ที่อยู่สำหรับจัดส่งสินค้า">?</i></label>
                <div class="field-wrap">
                  <input type="text" id="customer_address" name="customer_address" readonly placeholder="ที่อยู่จัดส่ง">
                </div>
              </div>
              <div class="field span-full">
                <label>หมายเหตุ <i class="tip" data-tip="ข้อความเพิ่มเติมที่ต้องการระบุในใบมัดจำ">?</i></label>
                <div class="field-wrap">
                  <textarea id="note" name="note" rows="3" placeholder="กรอกหมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: รายการสินค้าและมัดจำ -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-amber">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <rect x="2" y="5" width="10" height="9" rx="1.5" stroke="#b45309" stroke-width="1.5"/>
                <path d="M12 7.5l4 2.5v4h-4" stroke="#b45309" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="4.5" cy="15" r="1.3" stroke="#b45309" stroke-width="1.3"/>
                <circle cx="13.5" cy="15" r="1.3" stroke="#b45309" stroke-width="1.3"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>รายการสินค้าและมัดจำ</h3>
              <p>เลือกรายการที่ต้องการคิดมัดจำ และกำหนดเปอร์เซ็นต์หรือจำนวนเงิน</p>
            </div>
          </div>

          <div class="dep-section" style="padding-top:20px">
            <div class="section-divider" style="padding:0;margin-bottom:14px">
              <div class="section-divider-line"></div>
              <span class="section-divider-label">กำหนดอัตรามัดจำ</span>
              <div class="section-divider-line"></div>
            </div>
            <div class="dep-cards">
              <div class="dep-card" id="dcard-g" onclick="toggleCard('g')">
                <div class="dep-card-top">
                  <div class="dep-card-info">
                    <div class="dep-ico di-g">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="10" rx="1.5" stroke="#3E6AE1" stroke-width="1.4"/><path d="M5 3v3M11 3v3M2 8h12" stroke="#3E6AE1" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                      <div class="dep-card-name">สินค้า</div>
                      <div class="dep-card-sub">มัดจำสินค้า</div>
                    </div>
                  </div>
                  <button type="button" class="dep-toggle" id="dtog-g" onclick="event.stopPropagation();toggleCard('g')">
                    <div class="dep-toggle-knob"></div>
                  </button>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                  <span style="font-size:11px;color:var(--text-muted);font-weight:600">โหมดกรอก</span>
                  <div class="mode-tabs" onclick="event.stopPropagation()">
                    <button type="button" class="mode-tab on-g" id="mtab-g-pct" onclick="event.stopPropagation();setMode('g','pct')">% เปอร์เซ็นต์</button>
                    <button type="button" class="mode-tab" id="mtab-g-amt" onclick="event.stopPropagation();setMode('g','amt')">฿ จำนวนเงิน</button>
                  </div>
                </div>
                <div class="pct-wrap" id="pwrap-g">
                  <input type="number" id="dep-g" name="dep_product" value="" min="0" max="100" step="1" disabled placeholder="0" oninput="onDepInput('g')" onclick="event.stopPropagation()">
                  <span class="pct-unit" id="unit-g">%</span>
                </div>
                <div class="dep-result empty" id="dres-g">ยังไม่เปิดใช้งาน</div>
              </div>

              <div class="dep-card" id="dcard-b" onclick="toggleCard('b')">
                <div class="dep-card-top">
                  <div class="dep-card-info">
                    <div class="dep-ico di-b">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5.5" r="3" stroke="#3E6AE1" stroke-width="1.4"/><path d="M2 14c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="#3E6AE1" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                      <div class="dep-card-name">บริการ</div>
                      <div class="dep-card-sub">มัดจำบริการ</div>
                    </div>
                  </div>
                  <button type="button" class="dep-toggle" id="dtog-b" onclick="event.stopPropagation();toggleCard('b')">
                    <div class="dep-toggle-knob"></div>
                  </button>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                  <span style="font-size:11px;color:var(--text-muted);font-weight:600">โหมดกรอก</span>
                  <div class="mode-tabs" onclick="event.stopPropagation()">
                    <button type="button" class="mode-tab on-b" id="mtab-b-pct" onclick="event.stopPropagation();setMode('b','pct')">% เปอร์เซ็นต์</button>
                    <button type="button" class="mode-tab" id="mtab-b-amt" onclick="event.stopPropagation();setMode('b','amt')">฿ จำนวนเงิน</button>
                  </div>
                </div>
                <div class="pct-wrap bfocus" id="pwrap-b">
                  <input type="number" id="dep-b" name="dep_service" value="" min="0" max="100" step="1" disabled placeholder="0" oninput="onDepInput('b')" onclick="event.stopPropagation()">
                  <span class="pct-unit" id="unit-b">%</span>
                </div>
                <div class="dep-result empty" id="dres-b">ยังไม่เปิดใช้งาน</div>
              </div>
            </div>
          </div>

          <hr class="divider">

          <div style="padding:20px 24px 24px">
            <div class="section-divider" style="padding:0;margin-bottom:14px">
              <div class="section-divider-line"></div>
              <span class="section-divider-label">รายการสินค้า</span>
              <div class="section-divider-line"></div>
            </div>

            <div class="selection-bar" id="selection-bar" style="display:none">
              <div class="selection-info">
                <span class="selection-badge">
                  <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                    <path d="M3 7l3 3 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  เลือก <strong id="sel-count">0</strong>/<span id="sel-total">0</span> รายการ
                </span>
                <span>ยอดที่เลือก: <span class="selection-amount" id="sel-amount">฿0.00</span></span>
              </div>
              <div class="selection-actions">
                <button type="button" class="btn-link" onclick="selectAllItems(true)">เลือกทั้งหมด</button>
                <button type="button" class="btn-link warn" onclick="selectAllItems(false)">ยกเลิกทั้งหมด</button>
              </div>
            </div>

            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:44px;text-align:center;padding:11px 8px">
                      <label class="chk" title="เลือก/ยกเลิกทั้งหมด">
                        <input type="checkbox" id="chk-all" onchange="onCheckAllChange(this)">
                        <span class="chk-box"></span>
                      </label>
                    </th>
                    <th style="width:42%">รายการสินค้า / บริการ</th>
                    <th style="width:13%;text-align:center">จำนวน</th>
                    <th style="width:17%;text-align:right">ราคา/หน่วย</th>
                    <th style="width:17%;text-align:right">ยอดรวม</th>
                    <th style="width:15%;text-align:right;display:none" id="dep-th">มัดจำ</th>
                  </tr>
                </thead>
                <tbody id="tbody">
                  <tr>
                    <td colspan="5">
                      <div class="tbl-empty">
                        <div class="tbl-empty-icon">
                          <svg width="40" height="40" viewBox="0 0 40 40" fill="none"><rect x="8" y="6" width="24" height="28" rx="4" stroke="#c7d5f5" stroke-width="2"/><path d="M14 14h12M14 20h12M14 26h8" stroke="#c7d5f5" stroke-width="2" stroke-linecap="round"/></svg>
                        </div>
                        กำลังโหลดรายการสินค้า...
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== Sidebar ===== -->
      <div class="col-side">
        <div class="summary-card">
          <div class="summary-head">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="3" stroke="#1b2d4f" stroke-width="1.4"/><path d="M5 8h6M5 10.5h4" stroke="#1b2d4f" stroke-width="1.4" stroke-linecap="round"/><path d="M5 5.5h2" stroke="#3E6AE1" stroke-width="1.6" stroke-linecap="round"/></svg>
            สรุปยอดมัดจำ
          </div>
          <div class="summary-rows">
            <div class="s-row">
              <span class="slbl">ยอดรวมทั้งหมด</span>
              <span class="sval" id="sv-fulltotal">฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-disc" style="display:none">
              <span class="slbl">ส่วนลด</span>
              <span class="sval" id="sval-disc">-฿0.00</span>
            </div>
            <div class="s-row" id="srow-selected" style="display:none">
              <span class="slbl" id="sv-sub-label">ยอดที่เลือกคิดมัดจำ</span>
              <span class="sval" id="sv-sub" style="color:var(--primary)">฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-g" style="display:none">
              <span class="slbl" id="slbl-g">หักมัดจำสินค้า (0%)</span>
              <span class="sval" id="sval-g">-฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-b" style="display:none">
              <span class="slbl" id="slbl-b">หักมัดจำบริการ (0%)</span>
              <span class="sval" id="sval-b">-฿0.00</span>
            </div>
            <div class="s-row neg divider-row" id="srow-tot" style="display:none">
              <span class="slbl" style="font-weight:600;color:#374151">รวมมัดจำทั้งหมด</span>
              <span class="sval" id="sval-tot">-฿0.00</span>
            </div>
            <div class="s-row total">
              <span class="slbl">ยอดคงเหลือที่ต้องชำระ</span>
              <span class="sval" id="sv-grand">฿0.00</span>
            </div>
            <div class="s-row" id="srow-grand-vat" style="margin-top:4px;padding-top:8px;border-top:1px dashed var(--border)">
              <span class="slbl" style="font-size:12px;color:var(--text-muted)">ยอดคงเหลือ (รวม VAT 7%)</span>
              <span class="sval" id="sv-grand-vat" style="color:var(--primary);font-weight:700;font-size:15px">฿0.00</span>
            </div>
          </div>

          <div class="pdf-preview-section">
            <div class="pdf-preview-head">
              <div class="pdf-preview-title">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                  <path d="M3 1.5h5l3 3v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-10a1 1 0 0 1 1-1z" stroke="#374151" stroke-width="1.3" stroke-linejoin="round"/>
                  <path d="M8 1.5v3h3" stroke="#374151" stroke-width="1.3" stroke-linejoin="round"/>
                  <path d="M5 8h4M5 10h3" stroke="#dc2626" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                ตัวอย่างเอกสารใบมัดจำ
              </div>
              <div class="pdf-preview-actions">
                <button type="button" class="pdf-action-btn" onclick="openPdfModal()" title="ดูเต็มจอ">
                  <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M2 5V2h3M12 5V2H9M2 9v3h3M12 9v3H9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <a class="pdf-action-btn" href="{{ asset('storage/deposit_templates/templates.pdf') }}" target="_blank" rel="noopener" title="เปิดในแท็บใหม่">
                  <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M5.5 2.5h-3v9h9v-3M9 2.5h3M12 2.5l-5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
                <a class="pdf-action-btn" href="{{ asset('storage/deposit_templates/templates.pdf') }}" download title="ดาวน์โหลด">
                  <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M7 2v8M3.5 7L7 10.5 10.5 7M2.5 12.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>
            </div>
            <div class="pdf-frame-wrap">
              <iframe id="pdfPreviewFrame" src="{{ asset('storage/deposit_templates/templates.pdf') }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" title="ตัวอย่างเอกสารใบมัดจำ" onload="this.previousElementSibling && (this.previousElementSibling.style.display='none')" onerror="showPdfFallback()"></iframe>
              <div class="pdf-frame-fallback" id="pdfFallback" style="display:none">
                <div class="pdf-frame-fallback-icon">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5 2h7l4 4v11a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" stroke="#3E6AE1" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M12 2v4h4" stroke="#3E6AE1" stroke-width="1.5" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="pdf-frame-fallback-text">ไม่สามารถแสดงตัวอย่างได้<br>กดปุ่มด้านบนเพื่อเปิดในแท็บใหม่</div>
              </div>
            </div>
            <div class="pdf-meta">
              <span class="pdf-meta-name">templates.pdf</span>
              <span>เทมเพลตใบมัดจำ</span>
            </div>
          </div>

          <div class="summary-footer">
            <button type="button" class="btn-save" id="submitBill">
              <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              บันทึกใบมัดจำ
            </button>
            <p class="btn-save-hint">ข้อมูลจะถูกบันทึกและส่งไปยังระบบ</p>
          </div>
        </div>

        <div class="info-card">
          <div class="info-card-head">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="#374151" stroke-width="1.3"/><path d="M7 5v3l2 1.5" stroke="#374151" stroke-width="1.3" stroke-linecap="round"/></svg>
            รายละเอียดเอกสาร
          </div>
          <div class="info-items">
            <div class="info-item"><span class="ik">SO Number</span><span class="iv" id="info-so">—</span></div>
            <div class="info-item"><span class="ik">เลขที่ PO ลูกค้า</span><span class="iv" id="info-po">—</span></div>
            <div class="info-item"><span class="ik">วันที่สร้าง</span><span class="iv" id="info-date">—</span></div>
            <div class="info-item"><span class="ik">ผู้สร้างเอกสาร</span><span class="iv" id="info-emp">—</span></div>
            <div class="info-item">
              <span class="ik">สถานะ</span>
              <span class="iv">
                <span class="status-active">
                  <span style="width:6px;height:6px;background:var(--primary);animation:pulse 2s infinite;border-radius:50%;display:inline-block"></span>
                  รอมัดจำ
                </span>
              </span>
            </div>
          </div>
        </div>

        <div class="help-card">
          <div class="help-card-head">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="#3E6AE1" stroke-width="1.3"/><path d="M5.5 5.5C5.5 4.67 6.17 4 7 4s1.5.67 1.5 1.5c0 1-1.5 1.5-1.5 2.5" stroke="#3E6AE1" stroke-width="1.3" stroke-linecap="round"/><circle cx="7" cy="10.5" r=".6" fill="#3E6AE1"/></svg>
            วิธีใช้งาน
          </div>
          <div class="help-item"><div class="help-num">1</div><span>ตรวจสอบข้อมูลเอกสารและลูกค้าให้ถูกต้อง</span></div>
          <div class="help-item"><div class="help-num">2</div><span>ติ๊กเลือกรายการที่ต้องการคิดมัดจำ (เลือกเฉพาะบางรายการได้)</span></div>
          <div class="help-item"><div class="help-num">3</div><span>โหมด % ใส่จำนวนเต็ม (1–100) / โหมด ฿ ใส่ทศนิยมได้</span></div>
          <div class="help-item"><div class="help-num">4</div><span>ยอดคงเหลือ = (ยอดรวมทั้งหมด − ส่วนลด) − มัดจำ</span></div>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- PDF Modal -->
<div class="pdf-modal" id="pdfModal" onclick="closePdfModalOnBackdrop(event)">
  <div class="pdf-modal-content" onclick="event.stopPropagation()">
    <div class="pdf-modal-head">
      <div class="pdf-modal-title">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M3.5 1.5h6l3.5 3.5v9a1 1 0 0 1-1 1H3.5a1 1 0 0 1-1-1v-11a1 1 0 0 1 1-1z" stroke="#3E6AE1" stroke-width="1.5" stroke-linejoin="round"/>
          <path d="M9.5 1.5v3.5h3.5" stroke="#3E6AE1" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        ตัวอย่างเอกสารใบมัดจำ
      </div>
      <div class="pdf-modal-actions">
        <a class="pdf-action-btn" href="{{ asset('storage/deposit_templates/templates.pdf') }}" download title="ดาวน์โหลด">
          <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
            <path d="M7 2v8M3.5 7L7 10.5 10.5 7M2.5 12.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        <button type="button" class="pdf-action-btn" onclick="closePdfModal()" title="ปิด">
          <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
            <path d="M3 3l8 8M11 3l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="pdf-modal-body">
      <iframe id="pdfModalFrame" title="ตัวอย่างเอกสารใบมัดจำ (เต็มจอ)"></iframe>
    </div>
  </div>
</div>

<script>
let BASE = 0;
let FULL_TOTAL = 0;
let DISCOUNT = 0;
let ALL_ITEMS = [];

const active = { g: false, b: false };
const mode   = { g: 'pct', b: 'pct' };
const names  = { g: 'สินค้า', b: 'บริการ' };
const KEYS   = ['g', 'b'];

const PDF_TEMPLATE_URL = "{{ asset('storage/deposit_templates/templates.pdf') }}";
const PDF_PREVIEW_BASE = "{{ url('/deposit/preview') }}";
let PDF_URL = PDF_TEMPLATE_URL;

function fmt(n){ return '฿'+n.toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}) }
function fmtPlain(n){ return n.toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}) }
function fmtPct(n){ return n.toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:4}) }

function showLoading(msg){ document.getElementById('loadingOverlay').style.display='flex'; document.getElementById('loadingMsg').textContent=msg||'กำลังโหลดข้อมูล...'; }
function hideLoading(){ document.getElementById('loadingOverlay').style.display='none'; }

function showToast(title,msg,type='success'){
  const container=document.getElementById('toast-container');
  const id='toast-'+Date.now();
  const icons={
    success:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#eef2fd"/><path d="M5.5 9l3 3 4-5" stroke="#3E6AE1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    error:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#fee2e2"/><path d="M6 6l6 6M12 6l-6 6" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round"/></svg>`,
    warning:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#fef3c7"/><path d="M9 6v4M9 12.5v.5" stroke="#d97706" stroke-width="1.8" stroke-linecap="round"/></svg>`
  };
  const toast=document.createElement('div');
  toast.id=id;
  toast.className=`toast${type==='error'?' toast-error':type==='warning'?' toast-warning':''}`;
  toast.innerHTML=`<div class="toast-icon">${icons[type]||icons.success}</div><div class="toast-body"><div class="toast-title">${title}</div>${msg?`<div class="toast-msg">${msg}</div>`:''}</div><button class="toast-close" onclick="removeToast('${id}')"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 3l8 8M11 3l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>`;
  container.appendChild(toast);
  setTimeout(()=>removeToast(id),5000);
}
function removeToast(id){
  const el=document.getElementById(id);if(!el)return;
  el.style.animation='toastOut .25s ease forwards';
  setTimeout(()=>el.remove(),250);
}

function getSelectedItems(){ return ALL_ITEMS.filter(it => it.selected); }

function buildPreviewUrl(){
  const params = new URLSearchParams();
  params.set('so_id',            document.getElementById('so_id').value || '');
  params.set('sell_date',        document.getElementById('sell_date').value || '');
  params.set('customer_id',      document.getElementById('customer_id').value || '');
  params.set('tax_id',           document.getElementById('tax_id').value || '');
  params.set('customer_name',    document.getElementById('customer_name').value || '');
  params.set('contactso',        document.getElementById('contactso').value || '');
  params.set('customer_tel',     document.getElementById('customer_tel').value || '');
  params.set('customer_address', document.getElementById('customer_address').value || '');
  params.set('note',             document.getElementById('note').value || '');
  params.set('emp_name',         document.getElementById('hidden-emp').value || '');
  params.set('sale_name',        document.getElementById('hidden-sale').value || '');
  params.set('billid',           document.getElementById('billid').value || '');
  params.set('grand_total',      document.getElementById('hidden-grandtotal').value || '0');
  params.set('po_document',      document.getElementById('hidden-po').value || '');
  params.set('discount',         document.getElementById('hidden-discount').value || '0');
  const selectedIdx = getSelectedItems().map(it => it.idx);
  params.set('selected_items', JSON.stringify(selectedIdx));
  const deposits = [];
  const typeMap = { g:'product', b:'service' };
  KEYS.forEach(k => {
    if(active[k]){
      const raw = parseFloat(document.getElementById('dep-'+k).value) || 0;
      let pct, amt;
      if(mode[k]==='pct'){ pct = Math.min(100, Math.max(0, raw)); amt = BASE * pct / 100; }
      else { amt = Math.max(0, raw); pct = BASE > 0 ? amt / BASE * 100 : 0; }
      if(amt > 0) deposits.push({ type:typeMap[k], percent:+pct.toFixed(4), amount:+amt.toFixed(2) });
    }
  });
  params.set('deposits', JSON.stringify(deposits));
  params.set('_t', Date.now());
  return PDF_PREVIEW_BASE + '?' + params.toString();
}

let _pdfPreviewDebounce = null;
function updatePdfPreview(){
  clearTimeout(_pdfPreviewDebounce);
  _pdfPreviewDebounce = setTimeout(() => {
    PDF_URL = buildPreviewUrl();
    const inlineFrame = document.getElementById('pdfPreviewFrame');
    if(inlineFrame) inlineFrame.setAttribute('src', PDF_URL + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH');
    const modal = document.getElementById('pdfModal');
    if(modal && modal.classList.contains('show')){
      const modalFrame = document.getElementById('pdfModalFrame');
      if(modalFrame) modalFrame.setAttribute('src', PDF_URL + '#view=FitH');
    }
  }, 600);
}

function openPdfModal(){
  const modal = document.getElementById('pdfModal');
  const frame = document.getElementById('pdfModalFrame');
  frame.setAttribute('src', (PDF_URL || PDF_TEMPLATE_URL) + '#view=FitH');
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closePdfModal(){
  document.getElementById('pdfModal').classList.remove('show');
  document.body.style.overflow = '';
  const frame = document.getElementById('pdfModalFrame');
  if(frame) frame.setAttribute('src', '');
}
function closePdfModalOnBackdrop(e){ if(e.target.id === 'pdfModal') closePdfModal(); }
function showPdfFallback(){ const fb = document.getElementById('pdfFallback'); if(fb) fb.style.display = 'flex'; }
document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closePdfModal(); });

window.addEventListener('DOMContentLoaded',function(){
  const params=new URLSearchParams(window.location.search);
  const soNum   =params.get('so_num')   ||params.get('SONum')   ||'';
  const billId  =params.get('billid')   ||params.get('bill_id') ||'';
  const custId  =params.get('cust_id')  ||params.get('CustID')  ||'';
  const custName=params.get('cust_name')||'';
  const date    =params.get('date')     ||params.get('sell_date')||'';
  const emp     =params.get('create_by')||params.get('emp')     ||'';
  const empEl=document.getElementById('hidden-emp');
  if(empEl){ if(emp)empEl.value=emp; document.getElementById('info-emp').textContent=empEl.value||'—'; }
  if(soNum){ document.getElementById('so_number').value=soNum; document.getElementById('so_id').value=soNum; document.getElementById('info-so').textContent=soNum; }
  if(custId)   document.getElementById('customer_id').value=custId;
  if(custName) document.getElementById('customer_name').value=custName;
  if(date){ document.getElementById('sell_date').value=date; document.getElementById('info-date').textContent=date; }
  if(soNum)fetchSODetails(soNum);
  ['contactso','customer_tel','note'].forEach(id=>{
    const el = document.getElementById(id);
    if(el) el.addEventListener('input', updatePdfPreview);
  });
});

async function fetchSODetails(soNum){
  showLoading('กำลังโหลดข้อมูล SO...');
  try{
    const res=await fetch(`http://server_update:8000/api/getSODetail?SONum=${encodeURIComponent(soNum)}`);
    if(!res.ok)throw new Error(`HTTP ${res.status}`);
    const data=await res.json();
    if(!data.SoDetail){hideLoading();showToast('ไม่พบข้อมูล SO','ไม่พบข้อมูล SO: '+soNum,'error');return;}
    const d=data.SoDetail, s=data.SoStatus;
    
    // ✅ ดึงค่าส่วนลดจาก API (ตรวจสอบทั้ง SoDetail และ SoStatus)
    const disc = parseFloat(d.BaseDiscAmnt || s.BaseDiscAmnt || 0);
    DISCOUNT = isNaN(disc) ? 0 : disc;
    document.getElementById('hidden-discount').value = DISCOUNT.toFixed(2);

    document.getElementById('so_id').value=s.SONum||'';
    document.getElementById('customer_id').value=s.CustID||'';
    document.getElementById('customer_name').value=d.CustName||'';
    document.getElementById('tax_id').value=d.TaxId||'';
    document.getElementById('customer_tel').value=d.ContTel||'';
    document.getElementById('customer_address').value=[d.CustAddr1,d.ContDistrict,d.ContAmphur,d.ContProvince,d.ContPostCode].filter(Boolean).join(', ');
    const custPONo = d.CustPONo || s.CustPONo || '';
    const bi = document.getElementById('billid');
    if(bi) bi.value = custPONo;
    const hiddenPo = document.getElementById('hidden-po');
    if(hiddenPo) hiddenPo.value = custPONo;
    const infoPo = document.getElementById('info-po');
    if(infoPo) infoPo.textContent = custPONo || '—';
    const createdBy = d.createdBy || s.createdBy || d.CreatedBy || s.CreatedBy || '';
    const saleEl = document.getElementById('hidden-sale');
    if(saleEl) saleEl.value = createdBy;
    const saleChipVal = document.getElementById('sale-chip-value');
    if(saleChipVal) saleChipVal.textContent = createdBy || '—';
    if(d.DocuDate){
      const[dp]=d.DocuDate.split(' ');
      const[y,m,dd]=dp.split('-');
      const f=`${dd}-${m}-${y}`;
      document.getElementById('sell_date').value=f;
      document.getElementById('info-date').textContent=f;
    }
    const items=data.SOLists?.[0]?.ms_sodt||[];
    renderItems(items);
    fetchContactSo(s.CustID);
    showToast('โหลดข้อมูลสำเร็จ','ข้อมูล SO '+soNum+' พร้อมใช้งาน','success');
  }catch(err){
    console.error(err);
    showToast('เกิดข้อผิดพลาด','ไม่สามารถโหลดข้อมูล SO ได้: '+err.message,'error');
  }finally{hideLoading();}
}

function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function renderItems(items){
  const tbody=document.getElementById('tbody');
  tbody.innerHTML='';
  ALL_ITEMS = [];
  if(!items||!items.length){
    tbody.innerHTML=`<tr><td colspan="5"><div class="tbl-empty"><div class="tbl-empty-icon"><svg width="40" height="40" viewBox="0 0 40 40" fill="none"><rect x="8" y="6" width="24" height="28" rx="4" stroke="#c7d5f5" stroke-width="2"/><path d="M14 14h12M14 20h12M14 26h8" stroke="#c7d5f5" stroke-width="2" stroke-linecap="round"/></svg></div>ไม่พบรายการสินค้า</div></td></tr>`;
    document.getElementById('selection-bar').style.display = 'none';
    return;
  }
  items.forEach((item, idx)=>{
    const qty  =parseFloat(item.GoodQty2)  ||0;
    const price=parseFloat(item.GoodPrice2)||0;
    const sub  =qty*price;
    const name = (item.GoodName||'').trim();
    ALL_ITEMS.push({ idx, name, qty, price, sub, selected: true });
    tbody.insertAdjacentHTML('beforeend',`
      <tr class="row-checked" data-idx="${idx}" data-sub="${sub}">
        <td class="td-check"><label class="chk"><input type="checkbox" class="chk-item" data-idx="${idx}" checked onchange="onItemCheckChange(${idx}, this.checked)"><span class="chk-box"></span></label></td>
        <td class="td-name">${escapeHtml(name)}</td>
        <td class="td-num" style="text-align:center">${qty.toFixed(2)}</td>
        <td class="td-num">${fmtPlain(price)}</td>
        <td class="td-num">${fmtPlain(sub)}</td>
        <td class="td-dep dep-cell" style="display:none">—</td>
      </tr>`);
  });
  document.getElementById('selection-bar').style.display = 'flex';
  recalcBase();
}

function onItemCheckChange(idx, checked){
  const item = ALL_ITEMS.find(it => it.idx === idx);
  if(item) item.selected = checked;
  const row = document.querySelector(`tr[data-idx="${idx}"]`);
  if(row){ row.classList.toggle('row-checked', checked); row.classList.toggle('row-unchecked', !checked); }
  recalcBase();
}
function selectAllItems(checked){
  ALL_ITEMS.forEach(it => it.selected = checked);
  document.querySelectorAll('.chk-item').forEach(cb => { cb.checked = checked; });
  document.querySelectorAll('#tbody tr[data-idx]').forEach(row => { row.classList.toggle('row-checked', checked); row.classList.toggle('row-unchecked', !checked); });
  recalcBase();
}
function onCheckAllChange(el){ selectAllItems(el.checked); }
function updateCheckAllState(){
  const total = ALL_ITEMS.length;
  const sel   = ALL_ITEMS.filter(it => it.selected).length;
  const all = document.getElementById('chk-all');
  if(!all) return;
  if(sel === 0){ all.checked = false; all.indeterminate = false; }
  else if(sel === total){ all.checked = true; all.indeterminate = false; }
  else { all.checked = false; all.indeterminate = true; }
}

function recalcBase(){
  FULL_TOTAL = ALL_ITEMS.reduce((s, it) => s + it.sub, 0);
  BASE       = ALL_ITEMS.filter(it => it.selected).reduce((s, it) => s + it.sub, 0);
  
  // ✅ อัปเดตการแสดงส่วนลดใน UI
  const srowDisc = document.getElementById('srow-disc');
  const svalDisc = document.getElementById('sval-disc');
  if (DISCOUNT > 0) {
    srowDisc.style.display = 'flex';
    svalDisc.textContent = '-' + fmt(DISCOUNT);
  } else {
    srowDisc.style.display = 'none';
  }

  const sel = ALL_ITEMS.filter(it => it.selected).length;
  document.getElementById('sel-count').textContent  = sel;
  document.getElementById('sel-total').textContent  = ALL_ITEMS.length;
  document.getElementById('sel-amount').textContent = fmt(BASE);
  document.getElementById('sv-fulltotal').textContent = fmt(FULL_TOTAL);
  const selRow = document.getElementById('srow-selected');
  if(sel < ALL_ITEMS.length && ALL_ITEMS.length > 0){
    selRow.style.display = 'flex';
    document.getElementById('sv-sub-label').textContent = `ยอดที่เลือกคิดมัดจำ (${sel}/${ALL_ITEMS.length} รายการ)`;
    document.getElementById('sv-sub').textContent = fmt(BASE);
  } else { selRow.style.display = 'none'; }
  document.getElementById('hidden-subtotal').value   = FULL_TOTAL.toFixed(2);
  document.getElementById('hidden-grandtotal').value = Math.max(0, FULL_TOTAL - DISCOUNT).toFixed(2); // ✅ ปรับยอดรวมเบื้องต้น
  updateCheckAllState();
  KEYS.forEach(k => {
    if(active[k] && mode[k] === 'amt'){
      const inp = document.getElementById('dep-'+k);
      const cur = parseFloat(inp.value) || 0;
      const otherAmt = getOtherAmt(k);
      const maxAmt = Math.max(0, +(BASE - otherAmt).toFixed(2)); // ✅ ใช้ BASE แทน FULL_TOTAL
      if(cur > maxAmt) inp.value = maxAmt;
    }
  });
  calc();
}

function fetchContactSo(cid){
  if(!cid)return;
  fetch('/fetch-contactso',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    body:JSON.stringify({customer_id:cid})
  }).then(r=>r.json()).then(d=>{
    document.getElementById('contactso').value=d.contactso||'';
  }).catch(()=>{});
}

function setMode(k, m){
  if(!active[k]) return;
  mode[k] = m;
  const inp    = document.getElementById('dep-'+k);
  const unit   = document.getElementById('unit-'+k);
  const tabPct = document.getElementById('mtab-'+k+'-pct');
  const tabAmt = document.getElementById('mtab-'+k+'-amt');
  tabPct.className = 'mode-tab' + (m==='pct' ? ' on-'+k : '');
  tabAmt.className = 'mode-tab' + (m==='amt' ? ' on-'+k : '');
  if(m === 'pct'){ unit.textContent = '%'; inp.max = '100'; inp.step = '1'; inp.placeholder = '0'; }
  else { unit.textContent = '฿'; inp.removeAttribute('max'); inp.step = '0.01'; inp.placeholder = '0.00'; }
  inp.value = '';
  const res = document.getElementById('dres-'+k);
  res.className = 'dep-result '+k;
  res.textContent = m==='pct' ? 'กรอกเปอร์เซ็นต์มัดจำ' : 'กรอกจำนวนเงินมัดจำ';
  calc();
}

function toggleCard(k){
  const wasActive = active[k];
  KEYS.forEach(id=>{
    if(active[id]){
      active[id] = false; mode[id] = 'pct';
      document.getElementById('dcard-'+id).className = 'dep-card';
      document.getElementById('dtog-'+id).className  = 'dep-toggle';
      const inp = document.getElementById('dep-'+id);
      inp.disabled = true; inp.value = ''; inp.max = '100'; inp.step = '1'; inp.placeholder = '0';
      document.getElementById('mtab-'+id+'-pct').className = 'mode-tab on-'+id;
      document.getElementById('mtab-'+id+'-amt').className = 'mode-tab';
      document.getElementById('unit-'+id).textContent = '%';
      const res = document.getElementById('dres-'+id);
      res.className = 'dep-result empty'; res.textContent = 'ยังไม่เปิดใช้งาน';
    }
  });
  if(!wasActive){
    if(BASE <= 0){ showToast('ไม่มีรายการที่เลือก','กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการก่อน','warning'); return; }
    active[k] = true;
    document.getElementById('dcard-'+k).className = 'dep-card active-'+k;
    document.getElementById('dtog-'+k).className  = 'dep-toggle on-'+k;
    const inp = document.getElementById('dep-'+k);
    inp.disabled = false; inp.focus();
    const res = document.getElementById('dres-'+k);
    res.className = 'dep-result '+k;
    res.textContent = 'กรอกเปอร์เซ็นต์มัดจำ';
  }
  calc();
}

function getOtherAmt(k){
  const other = k === 'g' ? 'b' : 'g';
  if(!active[other]) return 0;
  const raw = parseFloat(document.getElementById('dep-'+other).value) || 0;
  if(mode[other] === 'pct'){ const pct = Math.min(100, Math.max(0, Math.floor(raw))); return BASE * pct / 100; }
  return Math.max(0, Math.round(raw * 100) / 100);
}

function onDepInput(k){
  const inp = document.getElementById('dep-'+k);
  let raw = parseFloat(inp.value);
  if(isNaN(raw) || raw < 0){ inp.value = ''; calc(); return; }
  if(mode[k] === 'pct'){
    if(!Number.isInteger(raw)){ raw = Math.floor(raw); inp.value = raw; }
    if(raw > 100){ inp.value = 100; showToastOnce('เกินขีดจำกัด','เปอร์เซ็นต์มัดจำต้องไม่เกิน 100%','warning'); }
  } else {
    const rounded = Math.round(raw * 100) / 100;
    if(rounded !== raw){ raw = rounded; inp.value = raw; }
    const otherAmt = getOtherAmt(k);
    const maxAmt = Math.max(0, +(BASE - otherAmt).toFixed(2)); // ✅ ใช้ BASE แทน FULL_TOTAL
    if(raw > maxAmt){ inp.value = maxAmt; showToastOnce('เกินยอดรวม',`มัดจำ${names[k]}ต้องไม่เกิน ${fmt(maxAmt)}`,'warning'); }
  }
  calc();
}

let _toastDebounce = null;
function showToastOnce(title, msg, type){ clearTimeout(_toastDebounce); _toastDebounce = setTimeout(() => showToast(title, msg, type), 600); }

function calc(){
  let totDepAmt = 0;
  KEYS.forEach(k=>{
    const srow = document.getElementById('srow-'+k);
    const slbl = document.getElementById('slbl-'+k);
    const sval = document.getElementById('sval-'+k);
    const res  = document.getElementById('dres-'+k);
    if(active[k]){
      const raw = parseFloat(document.getElementById('dep-'+k).value) || 0;
      let pct, amt;
      if(mode[k]==='pct'){
        pct = Math.min(100, Math.max(0, Math.floor(raw)));
        amt = BASE * pct / 100;
        res.className = 'dep-result '+k;
        res.textContent = amt > 0 ? fmtPlain(amt)+' บาท' : 'กรอกเปอร์เซ็นต์มัดจำ';
      } else {
        amt = Math.max(0, Math.round(raw * 100) / 100);
        pct = BASE > 0 ? (amt / BASE * 100) : 0;
        res.className = 'dep-result '+k;
        res.textContent = amt > 0 ? 'คิดเป็น '+fmtPct(pct)+'% ของยอดที่เลือก' : 'กรอกจำนวนเงินมัดจำ';
      }
      totDepAmt += amt;
      srow.style.display = 'flex';
      slbl.textContent = 'หักมัดจำ'+names[k]+' ('+fmtPct(pct)+'%)';
      sval.textContent = '-'+fmt(amt);
    } else { srow.style.display = 'none'; }
  });
  
  // ✅ คำนวณยอดสุทธิต่อการชำระ (ยอดรวม - ส่วนลด)
  const netBeforeDeposit = Math.max(0, FULL_TOTAL - DISCOUNT);
  // ป้องกันยอดมัดจำรวมเกินยอดสุทธิที่ลูกค้าต้องจ่ายจริง
  totDepAmt = Math.min(totDepAmt, netBeforeDeposit);
  
  const any = Object.values(active).some(v=>v);
  const net = Math.max(0, netBeforeDeposit - totDepAmt);
  
  document.getElementById('srow-tot').style.display = any ? 'flex' : 'none';
  document.getElementById('sval-tot').textContent = '-'+fmt(totDepAmt);
  document.getElementById('sv-grand').textContent = fmt(net);
  document.getElementById('sv-grand-vat').textContent = fmt(net * 1.07);
  document.getElementById('hidden-grandtotal').value = net.toFixed(2);
  
  const dth = document.getElementById('dep-th');
  if(any && totDepAmt > 0){
    dth.style.display = '';
    document.querySelectorAll('#tbody tr[data-idx]').forEach(row=>{
      const idx = parseInt(row.getAttribute('data-idx'));
      const item = ALL_ITEMS.find(it => it.idx === idx);
      const cell = row.querySelector('.dep-cell');
      if(!cell || !item) return;
      cell.style.display = '';
      if(item.selected && BASE > 0){
        const itemDep = (item.sub / BASE) * totDepAmt;
        cell.textContent = '-฿'+fmtPlain(itemDep);
        cell.style.color = '';
      } else { cell.textContent = '—'; cell.style.color = 'var(--text-hint)'; }
    });
  } else {
    dth.style.display = 'none';
    document.querySelectorAll('.dep-cell').forEach(c=>c.style.display='none');
  }
  updatePdfPreview();
}

document.getElementById('submitBill').addEventListener('click', async function(){
  const btn = this;
  const soId = document.getElementById('so_id').value.trim();
  const contactso = document.getElementById('contactso').value.trim();
  const customerTel = document.getElementById('customer_tel').value.trim();
  if(!soId){ showToast('ข้อมูลไม่ครบ','ไม่พบเลขที่ SO','error'); return; }
  if(!contactso){ showToast('ข้อมูลไม่ครบ','กรุณากรอกชื่อผู้ติดต่อ','error'); document.getElementById('contactso').focus(); return; }
  const selectedItems = getSelectedItems();
  if(selectedItems.length === 0){ showToast('ไม่มีรายการที่เลือก','กรุณาเลือกรายการสินค้าที่ต้องการคิดมัดจำอย่างน้อย 1 รายการ','warning'); return; }
  const anyActive = Object.values(active).some(v => v);
  if(!anyActive){ showToast('ยังไม่ได้เลือกประเภทมัดจำ','กรุณาเปิดสวิตช์และกรอกค่ามัดจำอย่างน้อย 1 ประเภท','warning'); return; }
  const typeMap = { g:'product', b:'service' };
  const deposits = [];
  KEYS.forEach(k => {
    if(active[k]){
      const raw = parseFloat(document.getElementById('dep-'+k).value) || 0;
      let pct, amt;
      if(mode[k]==='pct'){ pct = Math.min(100, Math.max(0, raw)); amt = BASE * pct / 100; }
      else { amt = Math.max(0, raw); pct = BASE > 0 ? amt / BASE * 100 : 0; }
      if(amt > 0) deposits.push({ type:typeMap[k], percent:+pct.toFixed(4), amount:+amt.toFixed(2) });
    }
  });
  if(deposits.length === 0){ showToast('ข้อมูลมัดจำไม่ถูกต้อง','กรุณากรอกค่ามัดจำให้มากกว่า 0','warning'); return; }
  const payload = {
    so_id: soId,
    sell_date: document.getElementById('sell_date').value,
    customer_id: document.getElementById('customer_id').value,
    customer_name: document.getElementById('customer_name').value,
    contactso: contactso,
    customer_tel: customerTel,
    customer_address: document.getElementById('customer_address').value,
    note: document.getElementById('note').value,
    emp_name: document.getElementById('hidden-emp').value,
    sale_name: (document.getElementById('hidden-sale').value || '').trim(),
    po_document: (document.getElementById('hidden-po').value || '').trim(),
    billid: document.getElementById('billid').value,
    subtotal: parseFloat(document.getElementById('hidden-subtotal').value) || 0,
    discount: DISCOUNT, // ✅ เพิ่มค่าส่วนลดเข้าไปใน Payload
    grand_total: parseFloat(document.getElementById('hidden-grandtotal').value) || 0,
    deposits: deposits,
    tax_id: document.getElementById('tax_id').value,
    selected_items: selectedItems.map(it => ({ idx:it.idx, name:it.name, qty:it.qty, price:it.price, sub:+it.sub.toFixed(2) })),
    selected_count: selectedItems.length,
    total_count: ALL_ITEMS.length,
  };
  btn.disabled = true;
  const originalHTML = btn.innerHTML;
  btn.innerHTML = `<svg width="17" height="17" viewBox="0 0 17 17" fill="none" style="animation:spin .7s linear infinite"><path d="M8.5 2a6.5 6.5 0 0 1 6.5 6.5" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>กำลังบันทึก...`;
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch('/deposit/store', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if(!res.ok || !data.success) throw new Error(data.message || `HTTP ${res.status}`);
    showToast('บันทึกสำเร็จ', data.message || 'บันทึกใบมัดจำเรียบร้อยแล้ว', 'success');
    if(data.pdf_url) await downloadSavedPdf(data.pdf_url, data.deposit_bill_id || soId);
    setTimeout(() => { window.location.href = 'http://server_update:8000/solist'; }, 2000);
  }catch(err){
    console.error(err);
    showToast('บันทึกไม่สำเร็จ', err.message || 'เกิดข้อผิดพลาด', 'error');
    btn.disabled = false;
    btn.innerHTML = originalHTML;
  }
});

async function downloadSavedPdf(pdfUrl, billId){
  try{
    const res = await fetch(pdfUrl);
    if(!res.ok) throw new Error(`HTTP ${res.status}`);
    const blob = await res.blob();
    const safeName = (billId || 'deposit').replace(/[^a-zA-Z0-9_-]/g, '_');
    const filename = `${safeName}.pdf`;
    const blobUrl = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = blobUrl; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
    showToast('ดาวน์โหลด PDF', `บันทึกไฟล์ ${filename} เรียบร้อย`, 'success');
  }catch(err){
    console.error('Download saved PDF error:', err);
    showToast('ดาวน์โหลด PDF ไม่สำเร็จ', err.message || 'ไม่สามารถดาวน์โหลด PDF ได้', 'warning');
  }
}
</script>
</body>
</html>
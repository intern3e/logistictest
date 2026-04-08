<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>สร้างใบมัดจำ — ระบบจัดการเอกสาร</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0F6E56;
  --primary-hover:#085041;
  --primary-light:#E1F5EE;
  --primary-mid:#1D9E75;
  --primary-border:#9FE1CB;
  --blue:#185FA5;
  --blue-hover:#0C447C;
  --blue-light:#E6F1FB;
  --blue-border:#B5D4F4;
  --amber:#854F0B;
  --amber-hover:#633806;
  --amber-light:#FAEEDA;
  --amber-border:#FAC775;
  --red:#C0392B;
  --red-light:#FDEDEC;
  --surface:#FFFFFF;
  --bg:#F0F4F8;
  --bg-alt:#E8EDF3;
  --border:#DDE3EC;
  --border-light:#EEF1F7;
  --text:#0F172A;
  --text-secondary:#475569;
  --text-muted:#94A3B8;
  --text-hint:#CBD5E1;
  --shadow-xs:0 1px 2px rgba(15,23,42,.06);
  --shadow-sm:0 2px 8px rgba(15,23,42,.08);
  --shadow-md:0 4px 16px rgba(15,23,42,.10);
  --shadow-lg:0 8px 32px rgba(15,23,42,.12);
  --r-sm:8px;--r-md:12px;--r-lg:16px;--r-xl:20px;
  --t-fast:.12s ease;--t-base:.2s ease;
}
body{font-family:'Sarabun',system-ui,sans-serif;font-size:15px;background:var(--bg);color:var(--text);line-height:1.6;min-height:100vh}

/* LOADING */
#loadingOverlay{display:none;position:fixed;inset:0;background:rgba(255,255,255,.92);backdrop-filter:blur(4px);z-index:9999;justify-content:center;align-items:center;flex-direction:column;gap:16px}
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:44px;height:44px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite}
#loadingMsg{font-size:14px;color:var(--text-secondary);font-weight:500}

/* TOAST */
#toast-container{position:fixed;top:24px;right:24px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);box-shadow:var(--shadow-lg);min-width:280px;max-width:380px;pointer-events:all;animation:toastIn .25s ease forwards;border-left:4px solid var(--primary)}
.toast.toast-error{border-left-color:var(--red)}
.toast.toast-warning{border-left-color:#D97706}
.toast-icon{flex-shrink:0;margin-top:1px}
.toast-body{flex:1;min-width:0}
.toast-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px}
.toast-msg{font-size:13px;color:var(--text-secondary);line-height:1.5}
.toast-close{flex-shrink:0;background:none;border:none;cursor:pointer;color:var(--text-muted);padding:2px;border-radius:4px;line-height:1;transition:color var(--t-fast)}
.toast-close:hover{color:var(--text)}
@keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
@keyframes toastOut{to{opacity:0;transform:translateX(20px)}}

/* NAVBAR */
.navbar{background:var(--surface);border-bottom:1px solid var(--border);box-shadow:var(--shadow-xs);position:sticky;top:0;z-index:100}
.navbar-inner{max-width:1200px;margin:0 auto;padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.navbar-brand{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:var(--primary);text-decoration:none;flex-shrink:0}
.navbar-brand-icon{width:34px;height:34px;background:var(--primary);border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center}
.navbar-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-muted)}
.navbar-breadcrumb a{color:var(--text-muted);text-decoration:none;transition:color var(--t-fast)}
.navbar-breadcrumb a:hover{color:var(--primary)}
.navbar-breadcrumb .sep{color:var(--text-hint)}
.navbar-breadcrumb .current{color:var(--text);font-weight:600}
.navbar-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.btn-nav{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:var(--r-sm);border:1px solid var(--border);background:var(--surface);color:var(--text-secondary);font-size:13px;font-weight:500;cursor:pointer;transition:all var(--t-fast);font-family:inherit;white-space:nowrap}
.btn-nav:hover{border-color:#CBD5E1;color:var(--text);background:#F8FAFC;box-shadow:var(--shadow-xs)}

/* PAGE */
.page{max-width:1200px;margin:0 auto;padding:28px 28px 60px}
.page-header{margin-bottom:28px;padding:24px 28px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}
.page-header-left{display:flex;align-items:center;gap:16px}
.page-header-icon{width:52px;height:52px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(15,110,86,.25);flex-shrink:0}
.page-header-text h1{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px}
.page-header-text p{font-size:13px;color:var(--text-secondary);margin-top:3px}

/* GRID */
.grid-layout{display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start}
.col-main{display:flex;flex-direction:column;gap:18px}
.col-side{display:flex;flex-direction:column;gap:16px;position:sticky;top:80px}

/* CARD */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow var(--t-base)}
.card:hover{box-shadow:var(--shadow-md)}
.card-head{padding:18px 24px 15px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;gap:12px;background:linear-gradient(to bottom,#FAFBFD,var(--surface))}
.card-icon{width:38px;height:38px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ci-green{background:var(--primary-light);border:1px solid var(--primary-border)}
.ci-blue{background:var(--blue-light);border:1px solid var(--blue-border)}
.ci-amber{background:var(--amber-light);border:1px solid var(--amber-border)}
.card-head-text h3{font-size:15px;font-weight:700;color:var(--text)}
.card-head-text p{font-size:12px;color:var(--text-muted);margin-top:2px}
.card-badge{margin-left:auto;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-required{background:var(--red-light);color:var(--red)}
.badge-optional{background:var(--bg-alt);color:var(--text-muted)}
.card-body{padding:22px 24px}

/* SECTION DIVIDER */
.section-divider{display:flex;align-items:center;gap:10px;padding:0 24px;margin-bottom:16px}
.section-divider-line{flex:1;height:1px;background:var(--border-light)}
.section-divider-label{font-size:11px;font-weight:700;color:var(--text-muted);letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}

/* FORM */
.form-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.span-2{grid-column:span 2}
.span-full{grid-column:1/-1}
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-size:12px;font-weight:700;color:var(--text-secondary);letter-spacing:.04em;display:flex;align-items:center;gap:5px}
.field label .req{color:var(--red);font-size:14px;line-height:1}
.field label .tip{display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;background:var(--bg-alt);border-radius:50%;font-size:10px;color:var(--text-muted);cursor:help;font-style:normal;font-weight:700;position:relative}
.field label .tip:hover::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--text);color:#fff;font-size:11px;font-weight:400;padding:5px 9px;border-radius:6px;white-space:nowrap;pointer-events:none;z-index:50;letter-spacing:0}
.field-wrap{position:relative}
.field input[type="text"],
.field input[type="number"],
.field input[type="email"]{
  padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--r-sm);background:#FAFBFC;color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast);width:100%
}
.field input[type="text"]:focus,
.field input[type="number"]:focus,
.field input[type="email"]:focus{border-color:var(--primary);background:var(--surface);box-shadow:0 0 0 3px rgba(15,110,86,.12)}
.field input[readonly]{background:#F5F7FA;color:var(--text-secondary);cursor:default;border-style:dashed}
.field input[readonly]:focus{border-color:var(--border);box-shadow:none}
.field input::placeholder{color:var(--text-hint)}
.readonly-badge{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:10px;font-weight:600;color:var(--text-hint);background:var(--bg-alt);padding:2px 6px;border-radius:4px;pointer-events:none}

/* EMAIL FIELD SPECIAL */
.field-email-wrap{position:relative}
.field-email-wrap .email-prefix{
  position:absolute;left:11px;top:50%;transform:translateY(-50%);
  font-size:13px;color:var(--text-muted);pointer-events:none;
  display:flex;align-items:center;gap:4px;
}
.field-email-wrap input[type="email"]{padding-left:36px}
.email-valid{border-color:var(--primary)!important;background:var(--primary-light)!important}
.email-invalid{border-color:var(--red)!important;background:var(--red-light)!important}
.email-hint{font-size:11px;margin-top:3px;min-height:16px}
.email-hint.ok{color:var(--primary)}
.email-hint.err{color:var(--red)}

/* DEPOSIT CARDS */
.dep-section{padding:0 24px 22px}
.dep-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.dep-card{border:2px solid var(--border);border-radius:var(--r-md);padding:16px;transition:all var(--t-base);background:var(--surface);cursor:pointer}
.dep-card:hover{border-color:#CBD5E1;box-shadow:var(--shadow-sm);transform:translateY(-1px)}
.dep-card.active-g{border-color:var(--primary);background:linear-gradient(135deg,#F0FBF7,#FAFFFD);box-shadow:0 4px 16px rgba(15,110,86,.12)}
.dep-card.active-b{border-color:var(--blue);background:linear-gradient(135deg,#EEF6FE,#FAFCFF);box-shadow:0 4px 16px rgba(24,95,165,.12)}
.dep-card.active-a{border-color:var(--amber);background:linear-gradient(135deg,#FEF6EC,#FFFDF8);box-shadow:0 4px 16px rgba(133,79,11,.12)}
.dep-card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;user-select:none}
.dep-card-info{display:flex;align-items:center;gap:10px}
.dep-ico{width:34px;height:34px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.di-g{background:var(--primary-light);border:1px solid var(--primary-border)}
.di-b{background:var(--blue-light);border:1px solid var(--blue-border)}
.di-a{background:var(--amber-light);border:1px solid var(--amber-border)}
.dep-card-name{font-size:14px;font-weight:700;color:var(--text)}
.dep-card-sub{font-size:11px;color:var(--text-muted);margin-top:2px}
.dep-toggle{width:42px;height:24px;border-radius:12px;background:#E2E8F0;position:relative;transition:background var(--t-base);flex-shrink:0;cursor:pointer;border:none;outline:none;padding:0}
.dep-toggle.on-g{background:var(--primary)}
.dep-toggle.on-b{background:var(--blue)}
.dep-toggle.on-a{background:var(--amber)}
.dep-toggle-knob{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:left var(--t-base);box-shadow:0 1px 4px rgba(0,0,0,.18)}
.dep-toggle.on-g .dep-toggle-knob,.dep-toggle.on-b .dep-toggle-knob,.dep-toggle.on-a .dep-toggle-knob{left:21px}
.pct-wrap{display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--r-sm);background:#F7F8FA;overflow:hidden;transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast)}
.pct-wrap:focus-within{border-color:var(--primary);background:var(--surface);box-shadow:0 0 0 3px rgba(15,110,86,.12)}
.pct-wrap input{border:none;background:transparent;padding:9px 10px;font-size:14px;font-family:inherit;color:var(--text);outline:none;width:100%;text-align:right}
.pct-wrap input:disabled{color:var(--text-hint);cursor:not-allowed}
.pct-unit{padding:0 11px 0 4px;font-size:13px;color:var(--text-secondary);font-weight:600}
.dep-result{font-size:12px;font-weight:600;margin-top:9px;min-height:22px;padding:4px 10px;border-radius:6px;display:inline-block;transition:all var(--t-fast)}
.dep-result.g{background:var(--primary-light);color:var(--primary-hover)}
.dep-result.b{background:var(--blue-light);color:var(--blue-hover)}
.dep-result.a{background:var(--amber-light);color:var(--amber-hover)}
.dep-result.empty{background:transparent;color:var(--text-hint);padding-left:0;font-weight:400;font-size:11px}

/* TABLE */
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:var(--r-md);border:1px solid var(--border)}
table{width:100%;border-collapse:collapse;font-size:14px;table-layout:fixed}
thead{background:linear-gradient(to bottom,#F8FAFC,#F1F5F9)}
th{padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);letter-spacing:.07em;text-transform:uppercase;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
td{padding:12px 16px;border-bottom:1px solid var(--border-light);vertical-align:middle}
td input{border:none;background:transparent;font-size:14px;font-family:inherit;color:var(--text-muted);outline:none;width:100%}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:#F8FAFC}
.td-code{font-family:'SFMono-Regular',Consolas,monospace;font-size:12px;color:var(--text-muted)}
.td-num{text-align:right;font-variant-numeric:tabular-nums;color:var(--text)}
.td-dep{text-align:right;font-variant-numeric:tabular-nums;font-weight:600;color:var(--red)}
.td-name{color:var(--text)}
.tbl-empty{padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px}
.tbl-empty-icon{margin-bottom:10px;opacity:.4}

/* SUMMARY CARD */
.summary-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow-sm)}
.summary-head{padding:16px 20px;border-bottom:1px solid var(--border-light);font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;background:linear-gradient(to bottom,#FAFBFD,var(--surface))}
.summary-rows{padding:16px 20px;display:flex;flex-direction:column;gap:10px}
.s-row{display:flex;justify-content:space-between;align-items:center;font-size:13px}
.s-row .slbl{color:var(--text-secondary)}
.s-row .sval{font-weight:600;color:var(--text);font-variant-numeric:tabular-nums}
.s-row.neg .sval{color:var(--red)}
.s-row.divider-row{border-top:1px dashed var(--border);padding-top:10px;margin-top:2px}
.s-row.total{margin-top:6px;padding:14px 0 0;border-top:2px solid var(--border-light)}
.s-row.total .slbl{font-weight:700;color:var(--text);font-size:14px}
.s-row.total .sval{font-size:24px;font-weight:800;color:var(--primary);letter-spacing:-.5px}
.summary-footer{padding:14px 20px 20px;border-top:1px solid var(--border-light)}
.btn-save{width:100%;padding:14px;border-radius:var(--r-md);border:none;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);color:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:all var(--t-base);font-family:inherit;letter-spacing:.01em;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 12px rgba(15,110,86,.25)}
.btn-save:hover{background:linear-gradient(135deg,var(--primary-hover) 0%,var(--primary) 100%);transform:translateY(-1px);box-shadow:0 6px 18px rgba(15,110,86,.32)}
.btn-save:active{transform:scale(.98);box-shadow:var(--shadow-sm)}
.btn-save:disabled{background:linear-gradient(135deg,#94A3B8,#CBD5E1);cursor:not-allowed;transform:none;box-shadow:none}
.btn-save-hint{text-align:center;font-size:11px;color:var(--text-muted);margin-top:8px}

/* INFO CARD */
.info-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow-xs)}
.info-card-head{padding:13px 16px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--text-secondary);letter-spacing:.05em;text-transform:uppercase;background:linear-gradient(to bottom,#FAFBFD,var(--surface))}
.info-items{padding:4px 0}
.info-item{display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:10px 16px;border-bottom:1px solid var(--border-light);gap:12px}
.info-item:last-child{border-bottom:none}
.info-item .ik{color:var(--text-muted);flex-shrink:0}
.info-item .iv{font-weight:600;color:var(--text);text-align:right;min-width:0;word-break:break-all}
.info-item .iv.status-active{display:inline-flex;align-items:center;gap:5px;color:var(--primary);background:var(--primary-light);padding:3px 9px;border-radius:20px;font-size:12px}

/* HELP CARD */
.help-card{background:linear-gradient(135deg,#EEF6FE,#F0F8FF);border:1px solid var(--blue-border);border-radius:var(--r-lg);padding:16px}
.help-card-head{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--blue);letter-spacing:.05em;text-transform:uppercase;margin-bottom:12px}
.help-item{display:flex;align-items:flex-start;gap:9px;font-size:12px;color:var(--text-secondary);padding:6px 0;border-bottom:1px solid rgba(181,212,244,.5)}
.help-item:last-child{border-bottom:none;padding-bottom:0}
.help-num{width:18px;height:18px;background:var(--blue-light);border:1px solid var(--blue-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--blue);flex-shrink:0;margin-top:1px}

hr.divider{border:none;border-top:1px solid var(--border-light);margin:0}

@media(max-width:1100px){
  .page{padding:22px 20px 50px}
  .grid-layout{grid-template-columns:1fr}
  .col-side{position:static;display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .form-grid-4{grid-template-columns:repeat(2,1fr)}
  .dep-cards{grid-template-columns:repeat(3,1fr)}
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
  .card-head{padding:14px 18px 12px}
  .dep-section{padding:0 18px 18px}
  table{font-size:13px}
  th,td{padding:10px 12px}
  .s-row.total .sval{font-size:20px}
  .form-grid-4{grid-template-columns:1fr}
  .span-2{grid-column:1}
  .page-header-icon{width:42px;height:42px}
}
@media(max-width:480px){
  .page{padding:12px 12px 50px}
  .page-header{padding:14px 16px}
  .card-body,.card-head{padding:14px}
  .dep-section{padding:0 14px 14px}
  table{min-width:480px}
  .btn-save{padding:13px}
  .summary-rows,.summary-footer{padding:14px 16px}
}
</style>
</head>
<body>

<div id="loadingOverlay">
  <div class="spinner"></div>
  <span id="loadingMsg">กำลังโหลดข้อมูล...</span>
</div>
<div id="toast-container"></div>

<!-- NAVBAR -->
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
    <div class="navbar-breadcrumb">
      <a href="/dashboard">หน้าหลัก</a>
      <span class="sep">›</span>
      <a href="/SOlist">รายการ SO</a>
      <span class="sep">›</span>
      <span class="current">สร้างใบมัดจำ</span>
    </div>
    <div class="navbar-actions">
      <button class="btn-nav" onclick="history.back()">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 11L5 7L9 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        ย้อนกลับ
      </button>
    </div>
  </div>
</nav>

<div class="page">

  <!-- PAGE HEADER -->
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
    <input type="hidden" name="so_id"       id="so_id">
    <input type="hidden" name="subtotal"     id="hidden-subtotal"   value="0">
    <input type="hidden" name="grand_total"  id="hidden-grandtotal" value="0">
    <input type="hidden" name="emp_name"     id="hidden-emp"
      value="{{ request()->filled('create_by') ? request('create_by') : 'Guest' }}">

    <div class="grid-layout">
      <div class="col-main">

        <!-- CARD: ข้อมูลเอกสาร -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-green">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <rect x="3" y="2" width="12" height="14" rx="2.5" stroke="#0F6E56" stroke-width="1.5"/>
                <path d="M6 6h6M6 9h6M6 12h4" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>ข้อมูลเอกสาร</h3>
              <p>Sales Order และวันที่ออกเอกสาร</p>
            </div>
          </div>
          <div class="card-body">
            <div class="form-grid-4">
              <div class="field">
                <label>เลขที่ SO <i class="tip" data-tip="รหัส Sales Order จากระบบ">?</i></label>
                <div class="field-wrap">
                  <input type="text" id="so_number" name="so_number" readonly placeholder="SO-XXXX">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>
              <div class="field">
                <label>เลขที่บิล</label>
                <div class="field-wrap">
                  <input type="text" id="billid" name="billid" readonly placeholder="—">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>
              <div class="field">
                <label>วันที่ออกเอกสาร</label>
                <div class="field-wrap">
                  <input type="text" id="sell_date" name="sell_date" readonly placeholder="DD-MM-YYYY">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>
              <div class="field">
                <label>รหัสลูกค้า</label>
                <div class="field-wrap">
                  <input type="text" id="customer_id" name="customer_id" readonly placeholder="—">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD: ข้อมูลลูกค้า -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-blue">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="6" r="3.2" stroke="#185FA5" stroke-width="1.5"/>
                <path d="M2.5 15.5c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>ข้อมูลลูกค้า</h3>
              <p>ชื่อบริษัท ผู้ติดต่อ เบอร์โทร อีเมล และที่อยู่จัดส่ง</p>
            </div>
            <span class="card-badge badge-optional">แก้ไขได้</span>
          </div>
          <div class="card-body">
            <div class="form-grid-4" style="row-gap:16px">

              <!-- ชื่อบริษัท -->
              <div class="field span-full">
                <label>ชื่อบริษัท / ลูกค้า</label>
                <div class="field-wrap">
                  <input type="text" id="customer_name" name="customer_name" readonly placeholder="ชื่อบริษัทหรือลูกค้า">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>

              <!-- ชื่อผู้ติดต่อ -->
              <div class="field span-2">
                <label>ชื่อผู้ติดต่อ <span class="req">*</span></label>
                <input type="text" id="contactso" name="contactso" placeholder="กรอกชื่อผู้ติดต่อ">
              </div>

              <!-- เบอร์ติดต่อ -->
              <div class="field span-2">
                <label>เบอร์ติดต่อ</label>
                <input type="text" id="customer_tel" name="customer_tel" placeholder="0XX-XXX-XXXX">
              </div>

              <!-- Gmail ลูกค้า — NEW FIELD -->
              <div class="field span-2">
                <label>
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="flex-shrink:0">
                    <rect x="1.5" y="3" width="11" height="8" rx="1.5" stroke="#185FA5" stroke-width="1.2"/>
                    <path d="M1.5 4.5L7 8l5.5-3.5" stroke="#185FA5" stroke-width="1.2" stroke-linecap="round"/>
                  </svg>
                  Gmail ลูกค้า
                  <i class="tip" data-tip="อีเมลสำหรับส่งเอกสารให้ลูกค้า ต้องเป็น @gmail.com">?</i>
                </label>
                <div class="field-wrap field-email-wrap">
                  <span class="email-prefix">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                      <rect x="1.5" y="3" width="11" height="8" rx="1.5" stroke="#CBD5E1" stroke-width="1.2"/>
                      <path d="M1.5 4.5L7 8l5.5-3.5" stroke="#CBD5E1" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                  </span>
                  <input type="email" id="customer_email" name="customer_email"
                    placeholder="example@gmail.com"
                    oninput="validateEmail(this)"
                    autocomplete="email">
                </div>
                <span class="email-hint" id="email-hint"></span>
              </div>

              <!-- ที่อยู่จัดส่ง -->
              <div class="field span-full">
                <label>
                  ที่อยู่จัดส่ง
                  <i class="tip" data-tip="ที่อยู่สำหรับจัดส่งสินค้า">?</i>
                </label>
                <div class="field-wrap">
                  <input type="text" id="customer_address" name="customer_address" readonly placeholder="ที่อยู่จัดส่ง">
                  <span class="readonly-badge">อัตโนมัติ</span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- CARD: รายการสินค้าและมัดจำ -->
        <div class="card">
          <div class="card-head">
            <div class="card-icon ci-amber">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <rect x="2" y="5" width="10" height="9" rx="1.5" stroke="#854F0B" stroke-width="1.5"/>
                <path d="M12 7.5l4 2.5v4h-4" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="4.5" cy="15" r="1.3" stroke="#854F0B" stroke-width="1.3"/>
                <circle cx="13.5" cy="15" r="1.3" stroke="#854F0B" stroke-width="1.3"/>
              </svg>
            </div>
            <div class="card-head-text">
              <h3>รายการสินค้าและมัดจำ</h3>
              <p>กำหนดเปอร์เซ็นต์มัดจำแต่ละประเภทสินค้า</p>
            </div>
          </div>

          <!-- Deposit Section -->
          <div class="dep-section" style="padding-top:20px">
            <div class="section-divider" style="padding:0;margin-bottom:14px">
              <div class="section-divider-line"></div>
              <span class="section-divider-label">กำหนดอัตรามัดจำ</span>
              <div class="section-divider-line"></div>
            </div>
            <div class="dep-cards">
              <!-- สินค้า -->
              <div class="dep-card" id="dcard-g" onclick="toggleCard('g')">
                <div class="dep-card-top">
                  <div class="dep-card-info">
                    <div class="dep-ico di-g">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="10" rx="1.5" stroke="#0F6E56" stroke-width="1.4"/><path d="M5 3v3M11 3v3M2 8h12" stroke="#0F6E56" stroke-width="1.4" stroke-linecap="round"/></svg>
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
                <div class="pct-wrap">
                  <input type="number" id="dep-g" name="dep_product" value="0" min="0" max="100" step="1" disabled oninput="calc()" onclick="event.stopPropagation()">
                  <span class="pct-unit">%</span>
                </div>
                <div class="dep-result empty" id="dres-g">ยังไม่เปิดใช้งาน</div>
              </div>
              <!-- บริการ -->
              <div class="dep-card" id="dcard-b" onclick="toggleCard('b')">
                <div class="dep-card-top">
                  <div class="dep-card-info">
                    <div class="dep-ico di-b">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5.5" r="3" stroke="#185FA5" stroke-width="1.4"/><path d="M2 14c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="#185FA5" stroke-width="1.4" stroke-linecap="round"/></svg>
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
                <div class="pct-wrap">
                  <input type="number" id="dep-b" name="dep_service" value="0" min="0" max="100" step="1" disabled oninput="calc()" onclick="event.stopPropagation()">
                  <span class="pct-unit">%</span>
                </div>
                <div class="dep-result empty" id="dres-b">ยังไม่เปิดใช้งาน</div>
              </div>
              <!-- ขนส่ง -->
              <div class="dep-card" id="dcard-a" onclick="toggleCard('a')">
                <div class="dep-card-top">
                  <div class="dep-card-info">
                    <div class="dep-ico di-a">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="5" width="9" height="7" rx="1" stroke="#854F0B" stroke-width="1.4"/><path d="M10 7l4 2.5V13h-4" stroke="#854F0B" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="3.5" cy="13" r="1.2" stroke="#854F0B" stroke-width="1.2"/><circle cx="12.5" cy="13" r="1.2" stroke="#854F0B" stroke-width="1.2"/></svg>
                    </div>
                    <div>
                      <div class="dep-card-name">ขนส่ง</div>
                      <div class="dep-card-sub">มัดจำขนส่ง</div>
                    </div>
                  </div>
                  <button type="button" class="dep-toggle" id="dtog-a" onclick="event.stopPropagation();toggleCard('a')">
                    <div class="dep-toggle-knob"></div>
                  </button>
                </div>
                <div class="pct-wrap">
                  <input type="number" id="dep-a" name="dep_shipping" value="0" min="0" max="100" step="1" disabled oninput="calc()" onclick="event.stopPropagation()">
                  <span class="pct-unit">%</span>
                </div>
                <div class="dep-result empty" id="dres-a">ยังไม่เปิดใช้งาน</div>
              </div>
            </div>
          </div>

          <hr class="divider">

          <!-- Table -->
          <div style="padding:20px 24px 24px">
            <div class="section-divider" style="padding:0;margin-bottom:14px">
              <div class="section-divider-line"></div>
              <span class="section-divider-label">รายการสินค้า</span>
              <div class="section-divider-line"></div>
            </div>
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:12%">รหัสสินค้า</th>
                    <th style="width:36%">รายการสินค้า / บริการ</th>
                    <th style="width:10%;text-align:center">จำนวน</th>
                    <th style="width:14%;text-align:right">ราคา/หน่วย</th>
                    <th style="width:14%;text-align:right">ยอดรวม</th>
                    <th style="width:14%;text-align:right;display:none" id="dep-th">มัดจำ</th>
                  </tr>
                </thead>
                <tbody id="tbody">
                  <tr id="empty-row">
                    <td colspan="6">
                      <div class="tbl-empty">
                        <div class="tbl-empty-icon">
                          <svg width="40" height="40" viewBox="0 0 40 40" fill="none"><rect x="8" y="6" width="24" height="28" rx="4" stroke="#CBD5E1" stroke-width="2"/><path d="M14 14h12M14 20h12M14 26h8" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round"/></svg>
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

      </div><!-- /col-main -->

      <!-- SIDEBAR -->
      <div class="col-side">

        <!-- Summary -->
        <div class="summary-card">
          <div class="summary-head">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="3" stroke="#0F172A" stroke-width="1.4"/><path d="M5 8h6M5 10.5h4" stroke="#0F172A" stroke-width="1.4" stroke-linecap="round"/><path d="M5 5.5h2" stroke="#0F6E56" stroke-width="1.6" stroke-linecap="round"/></svg>
            สรุปยอดมัดจำ
          </div>
          <div class="summary-rows">
            <div class="s-row">
              <span class="slbl">ยอดรวมสินค้าทั้งหมด</span>
              <span class="sval" id="sv-sub">฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-g" style="display:none">
              <span class="slbl" id="slbl-g">หักมัดจำสินค้า (0%)</span>
              <span class="sval" id="sval-g">-฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-b" style="display:none">
              <span class="slbl" id="slbl-b">หักมัดจำบริการ (0%)</span>
              <span class="sval" id="sval-b">-฿0.00</span>
            </div>
            <div class="s-row neg" id="srow-a" style="display:none">
              <span class="slbl" id="slbl-a">หักมัดจำขนส่ง (0%)</span>
              <span class="sval" id="sval-a">-฿0.00</span>
            </div>
            <div class="s-row neg divider-row" id="srow-tot" style="display:none">
              <span class="slbl" style="font-weight:600;color:#475569">รวมมัดจำทั้งหมด</span>
              <span class="sval" id="sval-tot">-฿0.00</span>
            </div>
            <div class="s-row total">
              <span class="slbl">ยอดคงเหลือที่ต้องชำระ</span>
              <span class="sval" id="sv-grand">฿0.00</span>
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

        <!-- Doc Info -->
        <div class="info-card">
          <div class="info-card-head">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="#475569" stroke-width="1.3"/><path d="M7 5v3l2 1.5" stroke="#475569" stroke-width="1.3" stroke-linecap="round"/></svg>
            รายละเอียดเอกสาร
          </div>
          <div class="info-items">
            <div class="info-item"><span class="ik">SO Number</span><span class="iv" id="info-so">—</span></div>
            <div class="info-item"><span class="ik">วันที่สร้าง</span><span class="iv" id="info-date">—</span></div>
            <div class="info-item"><span class="ik">ผู้สร้างเอกสาร</span><span class="iv" id="info-emp">—</span></div>
            <div class="info-item">
              <span class="ik">Gmail ลูกค้า</span>
              <span class="iv" id="info-email" style="color:var(--text-muted);font-weight:400">—</span>
            </div>
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

        <!-- Help -->
        <div class="help-card">
          <div class="help-card-head">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="#185FA5" stroke-width="1.3"/><path d="M5.5 5.5C5.5 4.67 6.17 4 7 4s1.5.67 1.5 1.5c0 1-1.5 1.5-1.5 2.5" stroke="#185FA5" stroke-width="1.3" stroke-linecap="round"/><circle cx="7" cy="10.5" r=".6" fill="#185FA5"/></svg>
            วิธีใช้งาน
          </div>
          <div class="help-item"><div class="help-num">1</div><span>ตรวจสอบข้อมูลเอกสารและลูกค้าให้ถูกต้อง</span></div>
          <div class="help-item"><div class="help-num">2</div><span>กรอก Gmail ลูกค้า (ถ้ามี) เพื่อส่งเอกสาร</span></div>
          <div class="help-item"><div class="help-num">3</div><span>เปิดใช้งานประเภทมัดจำที่ต้องการโดยคลิกที่การ์ด</span></div>
          <div class="help-item"><div class="help-num">4</div><span>กรอกเปอร์เซ็นต์มัดจำ (0–100%) สำหรับแต่ละประเภท</span></div>
          <div class="help-item"><div class="help-num">5</div><span>ตรวจสอบยอดสรุปด้านขวา แล้วกด "บันทึกใบมัดจำ"</span></div>
        </div>

      </div><!-- /col-side -->
    </div><!-- /grid-layout -->
  </form>
</div><!-- /page -->

<script>
/* ===== STATE ===== */
let BASE = 0;
const active = { g:false, b:false, a:false };
const names  = { g:'สินค้า', b:'บริการ', a:'ขนส่ง' };

/* ===== FORMATTERS ===== */
function fmt(n){ return '฿'+n.toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}) }
function fmtPlain(n){ return n.toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}) }

/* ===== LOADING ===== */
function showLoading(msg){ document.getElementById('loadingOverlay').style.display='flex'; document.getElementById('loadingMsg').textContent=msg||'กำลังโหลดข้อมูล...'; }
function hideLoading(){ document.getElementById('loadingOverlay').style.display='none' }

/* ===== TOAST ===== */
function showToast(title,msg,type='success'){
  const container=document.getElementById('toast-container');
  const id='toast-'+Date.now();
  const icons={
    success:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#E1F5EE"/><path d="M5.5 9l3 3 4-5" stroke="#0F6E56" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    error:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#FDEDEC"/><path d="M6 6l6 6M12 6l-6 6" stroke="#C0392B" stroke-width="1.8" stroke-linecap="round"/></svg>`,
    warning:`<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" fill="#FEF6EC"/><path d="M9 6v4M9 12.5v.5" stroke="#D97706" stroke-width="1.8" stroke-linecap="round"/></svg>`
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

/* ===== EMAIL VALIDATION ===== */
function validateEmail(input){
  const val=input.value.trim();
  const hint=document.getElementById('email-hint');
  const infoEmail=document.getElementById('info-email');
  if(!val){
    input.classList.remove('email-valid','email-invalid');
    hint.textContent='';hint.className='email-hint';
    infoEmail.textContent='—';infoEmail.style.color='var(--text-muted)';infoEmail.style.fontWeight='400';
    return;
  }
  const isGmail=/^[^\s@]+@gmail\.com$/i.test(val);
  if(isGmail){
    input.classList.add('email-valid');input.classList.remove('email-invalid');
    hint.textContent='✓ อีเมล Gmail ถูกต้อง';hint.className='email-hint ok';
    infoEmail.textContent=val;infoEmail.style.color='var(--primary)';infoEmail.style.fontWeight='600';
  } else {
    input.classList.add('email-invalid');input.classList.remove('email-valid');
    const hasAt=val.includes('@');
    if(hasAt && !val.endsWith('@gmail.com')){
      hint.textContent='⚠ ต้องเป็น @gmail.com เท่านั้น';
    } else {
      hint.textContent='⚠ รูปแบบอีเมลไม่ถูกต้อง';
    }
    hint.className='email-hint err';
    infoEmail.textContent=val;infoEmail.style.color='var(--red)';infoEmail.style.fontWeight='500';
  }
}

/* ===== INIT ===== */
window.addEventListener('DOMContentLoaded',function(){
  const params=new URLSearchParams(window.location.search);
  const soNum   =params.get('so_num')   ||params.get('SONum')   ||'';
  const billId  =params.get('billid')   ||params.get('bill_id') ||'';
  const custId  =params.get('cust_id')  ||params.get('CustID')  ||'';
  const custName=params.get('cust_name')||'';
  const date    =params.get('date')     ||params.get('sell_date')||'';
  const emp     =params.get('create_by')||params.get('emp')     ||'';
  const email   =params.get('email')    ||params.get('customer_email')||'';

  const empEl=document.getElementById('hidden-emp');
  if(empEl){
    if(emp)empEl.value=emp;
    document.getElementById('info-emp').textContent=empEl.value||'—';
  }
  if(soNum){
    document.getElementById('so_number').value=soNum;
    document.getElementById('so_id').value=soNum;
    document.getElementById('info-so').textContent=soNum;
  }
  const billEl=document.getElementById('billid');
  if(billId&&billEl)billEl.value=billId;
  if(custId)   document.getElementById('customer_id').value=custId;
  if(custName) document.getElementById('customer_name').value=custName;
  if(date){
    document.getElementById('sell_date').value=date;
    document.getElementById('info-date').textContent=date;
  }
  /* prefill email if passed via URL */
  if(email){
    const emailEl=document.getElementById('customer_email');
    emailEl.value=email;
    validateEmail(emailEl);
  }
  if(soNum)fetchSODetails(soNum,billId);
});

/* ===== FETCH SO DETAILS ===== */
async function fetchSODetails(soNum,billId){
  showLoading('กำลังโหลดข้อมูล SO...');
  try{
    const res=await fetch(`http://server_update:8000/api/getSODetail?SONum=${encodeURIComponent(soNum)}`);
    if(!res.ok)throw new Error(`HTTP ${res.status}`);
    const data=await res.json();
    if(!data.SoDetail){hideLoading();showToast('ไม่พบข้อมูล SO','ไม่พบข้อมูล SO: '+soNum,'error');return;}
    const d=data.SoDetail,s=data.SoStatus;
    document.getElementById('so_id').value=s.SONum||'';
    document.getElementById('customer_id').value=s.CustID||'';
    document.getElementById('customer_name').value=d.CustName||'';
    document.getElementById('customer_tel').value=d.ContTel||'';
    const ship=[d.ShipToAddr1,d.ShipToAddr2].filter(Boolean).join(', ');
    document.getElementById('customer_address').value=[d.CustAddr1,d.ContDistrict,d.ContAmphur,d.ContProvince,d.ContPostCode,ship?'สถานที่ส่ง: '+ship:null].filter(Boolean).join(', ');
    /* ดึง email จาก API ถ้ามี */
    if(d.CustEmail){
      const emailEl=document.getElementById('customer_email');
      if(!emailEl.value){
        emailEl.value=d.CustEmail;
        validateEmail(emailEl);
      }
    }
    let bill=null,key=billId;
    if(billId&&data.Bills?.[0]?.[billId]){bill=data.Bills[0][billId];}
    else if(data.Bills?.[0]){
      key=Object.keys(data.Bills[0])[0];
      bill=data.Bills[0][key];
      const bi=document.getElementById('billid');
      if(bi&&key)bi.value=key;
    }
    if(bill){
      if(bill.DocuDate){
        const[dp]=bill.DocuDate.split(' ');
        const[y,m,dd]=dp.split('-');
        const f=`${dd}-${m}-${y}`;
        document.getElementById('sell_date').value=f;
        document.getElementById('info-date').textContent=f;
      }
      renderItems(bill.items||[]);
    }
    fetchContactSo(s.CustID);
    showToast('โหลดข้อมูลสำเร็จ','ข้อมูล SO '+soNum+' พร้อมใช้งาน','success');
  }catch(err){
    console.error(err);
    showToast('เกิดข้อผิดพลาด','ไม่สามารถโหลดข้อมูล SO ได้: '+err.message,'error');
  }finally{hideLoading();}
}

/* ===== RENDER ITEMS ===== */
function renderItems(items){
  const tbody=document.getElementById('tbody');
  tbody.innerHTML='';
  let total=0;
  if(!items.length){
    tbody.innerHTML=`<tr><td colspan="6"><div class="tbl-empty"><div class="tbl-empty-icon"><svg width="40" height="40" viewBox="0 0 40 40" fill="none"><rect x="8" y="6" width="24" height="28" rx="4" stroke="#CBD5E1" stroke-width="2"/><path d="M14 14h12M14 20h12M14 26h8" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round"/></svg></div>ไม่พบรายการสินค้า</div></td></tr>`;
    return;
  }
  items.forEach((item,i)=>{
    const qty=parseFloat(item.GoodQty2)||0;
    const price=parseFloat(item.GoodPrice2)||0;
    const sub=qty*price;
    total+=sub;
    tbody.insertAdjacentHTML('beforeend',`
      <tr data-sub="${sub}">
        <td class="td-code">53-${String(i+1).padStart(4,'0')}</td>
        <td class="td-name">${(item.GoodName||'').replace(/"/g,'&quot;')}</td>
        <td class="td-num" style="text-align:center">${qty.toFixed(2)}</td>
        <td class="td-num">${fmtPlain(price)}</td>
        <td class="td-num">${fmtPlain(sub)}</td>
        <td class="td-dep dep-cell" style="display:none">—</td>
      </tr>`);
  });
  BASE=total;
  document.getElementById('sv-sub').textContent=fmt(BASE);
  document.getElementById('sv-grand').textContent=fmt(BASE);
  document.getElementById('hidden-subtotal').value=BASE.toFixed(2);
  document.getElementById('hidden-grandtotal').value=BASE.toFixed(2);
  calc();
}

/* ===== FETCH CONTACT ===== */
function fetchContactSo(cid){
  if(!cid)return;
  fetch('/fetch-contactso',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    body:JSON.stringify({customer_id:cid})
  }).then(r=>r.json()).then(d=>{
    document.getElementById('contactso').value=d.contactso||'';
    /* ถ้า API ส่ง email มาด้วย */
    if(d.customer_email){
      const emailEl=document.getElementById('customer_email');
      if(!emailEl.value){emailEl.value=d.customer_email;validateEmail(emailEl);}
    }
  }).catch(()=>{});
}

/* ===== TOGGLE DEPOSIT CARD ===== */
function toggleCard(k){
  active[k]=!active[k];
  const card=document.getElementById('dcard-'+k);
  const tog=document.getElementById('dtog-'+k);
  const inp=document.getElementById('dep-'+k);
  const res=document.getElementById('dres-'+k);
  if(active[k]){
    card.className='dep-card active-'+k;
    tog.className='dep-toggle on-'+k;
    inp.disabled=false;inp.focus();
    res.className='dep-result '+k;res.textContent='0.00 บาท';
  }else{
    card.className='dep-card';tog.className='dep-toggle';
    inp.disabled=true;inp.value='0';
    res.className='dep-result empty';res.textContent='ยังไม่เปิดใช้งาน';
  }
  calc();
}

/* ===== CALCULATE ===== */
function calc(){
  let totDep=0,totPct=0;
  ['g','b','a'].forEach(k=>{
    const srow=document.getElementById('srow-'+k);
    const slbl=document.getElementById('slbl-'+k);
    const sval=document.getElementById('sval-'+k);
    const res=document.getElementById('dres-'+k);
    if(active[k]){
      const pct=Math.min(100,Math.max(0,parseFloat(document.getElementById('dep-'+k).value)||0));
      const amt=BASE*pct/100;
      totDep+=amt;totPct+=pct;
      srow.style.display='flex';
      slbl.textContent='หักมัดจำ'+names[k]+' ('+pct+'%)';
      sval.textContent='-'+fmt(amt);
      res.textContent=fmtPlain(amt)+' บาท';
    }else{srow.style.display='none';}
  });
  const any=Object.values(active).some(v=>v);
  const net=BASE-totDep;
  document.getElementById('srow-tot').style.display=any?'flex':'none';
  document.getElementById('sval-tot').textContent='-'+fmt(totDep);
  document.getElementById('sv-grand').textContent=fmt(net);
  document.getElementById('hidden-grandtotal').value=net.toFixed(2);
  const dth=document.getElementById('dep-th');
  if(any&&totPct>0){
    dth.style.display='';
    document.querySelectorAll('#tbody tr').forEach(row=>{
      const sub=parseFloat(row.getAttribute('data-sub'))||0;
      const cell=row.querySelector('.dep-cell');
      if(cell){cell.style.display='';cell.textContent='-฿'+fmtPlain(sub*totPct/100);}
    });
  }else{
    dth.style.display='none';
    document.querySelectorAll('.dep-cell').forEach(c=>c.style.display='none');
  }
}

/* ===== SUBMIT ===== */
document.getElementById('submitBill').addEventListener('click',async function(){
  const btn=this;

  /* validate email ถ้ากรอกไว้ */
  const emailEl=document.getElementById('customer_email');
  const emailVal=emailEl.value.trim();
  if(emailVal && !/^[^\s@]+@gmail\.com$/i.test(emailVal)){
    showToast('อีเมลไม่ถูกต้อง','กรุณากรอก Gmail ให้ถูกต้อง หรือเว้นว่างไว้','warning');
    emailEl.focus();
    return;
  }

  const soId=document.getElementById('so_id').value.trim();
  const soNum=document.getElementById('so_number').value.trim();
  if(!soId&&!soNum){showToast('ข้อมูลไม่ครบ','ไม่พบข้อมูล SO กรุณาโหลดหน้าใหม่','error');return;}
  if(!soId&&soNum)document.getElementById('so_id').value=soNum;

  btn.disabled=true;
  btn.innerHTML=`<svg width="17" height="17" viewBox="0 0 17 17" fill="none" style="animation:spin .7s linear infinite"><path d="M8.5 2a6.5 6.5 0 0 1 6.5 6.5" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>กำลังบันทึก...`;
  try{
    const res=await fetch('{{ route("insert.post") }}',{
      method:'POST',
      headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body:new FormData(document.getElementById('billForm'))
    });
    const data=await res.json();
    if(data.success){
      showToast('บันทึกสำเร็จ',data.success,'success');
      setTimeout(()=>{window.location.href='/SOlist';},1500);
    }else{
      showToast('เกิดข้อผิดพลาด',data.error||'ไม่สามารถบันทึกข้อมูลได้','error');
    }
  }catch(err){
    console.error(err);
    showToast('เกิดข้อผิดพลาด','ไม่สามารถส่งข้อมูลได้ กรุณาลองใหม่อีกครั้ง','error');
  }finally{
    btn.disabled=false;
    btn.innerHTML=`<svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>บันทึกใบมัดจำ`;
  }
});
</script>
</body>
</html>
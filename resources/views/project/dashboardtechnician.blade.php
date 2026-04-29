<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Triple 3E Group — ระบบจัดการ</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --or:#f04709;--or2:#ffffff;--or3:#f78833;--or4:#ff9100;--or5:#fa8c0e;--or6:#f68812;
      --dk:#1a1512;--dk2:#2d2521;--md:#6b6560;--lt:#f5f3f0;--wh:#ffffff;
      --bg:#f4f1ec;--bd:#e3ddd6;--bd2:#d4cdc4;
      --gr:#06fa60;--bl:#2563eb;--rd:#dc2626;
      --solar:#102273;--solar2:#1d35a0;--solar3:#7dd3fc;
      --shadow-sm:0 1px 3px rgba(26,21,18,.08),0 1px 2px rgba(26,21,18,.04);
      --shadow-md:0 4px 12px rgba(26,21,18,.10),0 2px 4px rgba(26,21,18,.06);
      --shadow-lg:0 12px 32px rgba(26,21,18,.12),0 4px 10px rgba(26,21,18,.07);
      --shadow-or:0 4px 16px rgba(249,115,22,.22);
      --radius-sm:8px;--radius-md:14px;--radius-lg:18px;--radius-xl:24px;
    }
    html{font-size:16px}
    body{font-family:'IBM Plex Sans Thai','Sarabun',sans-serif;background:var(--bg);color:var(--dk);min-height:100vh;line-height:1.6;-webkit-font-smoothing:antialiased}
    nav{background:var(--wh);border-bottom:3px solid var(--or);position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(249,115,22,.10)}
    .nav-inner{display:flex;align-items:center;height:70px;padding:0 32px;gap:12px}
    .nav-logo{display:flex;align-items:center;gap:14px;margin-right:6px;flex-shrink:0}
    .nav-mark{width:44px;height:44px;background:linear-gradient(135deg,var(--or),#ea580c);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:900;color:#fff;box-shadow:var(--shadow-or);letter-spacing:-.5px}
    .nav-title{font-size:17px;font-weight:800;color:var(--dk);letter-spacing:-.2px;white-space:nowrap}
    .nav-sub{font-size:12px;color:var(--md);font-weight:500;margin-top:1px}
    .nav-tabs{display:flex;align-items:stretch;flex:1;height:100%;overflow-x:auto;scrollbar-width:none}
    .nav-tabs::-webkit-scrollbar{display:none}
    .nav-tab{display:flex;align-items:center;gap:7px;padding:0 20px;font-size:14px;font-weight:700;color:var(--md);border:none;background:none;cursor:pointer;border-bottom:3px solid transparent;font-family:inherit;white-space:nowrap;transition:all .18s;height:100%}
    .nav-tab:hover{color:var(--or);background:rgba(240,71,9,.04)}
    .nav-tab.active{color:var(--or);border-bottom-color:var(--or);background:rgba(240,71,9,.06)}
    .nav-tab svg{width:16px;height:16px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2}
    .nav-badge-count{background:rgba(240,71,9,.12);color:var(--or);font-size:10px;font-weight:800;padding:1px 6px;border-radius:10px}
    .nav-tab.active .nav-badge-count{background:var(--or);color:#fff}
    .nav-end{display:flex;align-items:center;gap:8px;flex-shrink:0;padding-left:12px;border-left:1px solid var(--bd)}
    .main{padding:24px 36px}
    .flash{padding:13px 18px;border-radius:var(--radius-md);font-size:14px;font-weight:600;margin-bottom:18px;display:flex;align-items:center;gap:10px;position:relative;overflow:hidden;animation:flashIn .3s ease,flashOut .5s ease 4.5s forwards}
    .flash::after{content:'';position:absolute;bottom:0;left:0;height:3px;background:currentColor;opacity:.4;animation:flashProg 5s linear forwards}
    .flash-success{background:#dcfce7;color:#14532d;border:1.5px solid #86efac}
    .flash-error{background:#fee2e2;color:#7f1d1d;border:1.5px solid #fca5a5}
    @keyframes flashIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}
    @keyframes flashOut{from{opacity:1;max-height:100px;margin-bottom:18px;padding:13px 18px}to{opacity:0;max-height:0;margin-bottom:0;padding:0;border-width:0}}
    @keyframes flashProg{from{width:100%}to{width:0}}
    .panel{display:none}
    .panel.active{display:block;animation:fi .22s ease}
    @keyframes fi{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
    .panel-header{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
    .panel-title{font-size:20px;font-weight:800;color:var(--dk);letter-spacing:-.3px;flex:1}
    .panel-actions{display:flex;gap:10px;align-items:center}
    .btn{padding:9px 20px;border-radius:var(--radius-sm);border:none;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .18s;display:inline-flex;align-items:center;gap:6px}
    .btn-primary{background:linear-gradient(135deg,var(--or),#ea580c);color:#fff;box-shadow:0 2px 8px rgba(249,115,22,.30)}
    .btn-primary:hover{transform:translateY(-1px);box-shadow:var(--shadow-or)}
    .btn-solar{background:linear-gradient(135deg,var(--solar),var(--solar2));color:#fff}
    .btn-solar:hover{opacity:.9;transform:translateY(-1px)}
    .btn-ghost{background:var(--lt);color:var(--md);border:1.5px solid var(--bd)}
    .btn-ghost:hover{background:var(--bd);color:var(--dk)}
    .btn-sm{padding:5px 12px;font-size:12px;border-radius:7px}
    .btn-danger{background:#fee2e2;color:#991b1b;border:1.5px solid #fca5a5}
    .btn-danger:hover{background:#fca5a5}
    .search-inp{padding:9px 14px;border:1.5px solid var(--bd);border-radius:var(--radius-sm);font-size:14px;font-family:inherit;outline:none;background:var(--wh);transition:border .18s;min-width:240px}
    .search-inp:focus{border-color:var(--or)}
    .team-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:20px;margin-bottom:24px}
    .team-card{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-lg);overflow:hidden;transition:all .22s;box-shadow:var(--shadow-sm);display:flex;flex-direction:column}
    .team-card:hover{box-shadow:var(--shadow-lg);border-color:var(--or5);transform:translateY(-3px)}
    .team-head-bar{padding:16px 18px;background:linear-gradient(135deg,#2d2521,#1a1512);border-bottom:3px solid var(--or);display:flex;align-items:center;gap:12px}
    .team-title{font-size:16px;font-weight:800;color:#fff}
    .team-meta{font-size:12px;color:var(--or3);margin-top:3px;font-weight:600}
    .tl-cell.tl-busy{cursor:not-allowed;pointer-events:none;}
    .team-cal-btn{background:linear-gradient(135deg,var(--or),#ea580c);color:#fff;border:none;padding:7px 13px;border-radius:9px;font-size:12px;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all .18s;box-shadow:0 2px 8px rgba(249,115,22,.35);font-family:inherit;flex-shrink:0}
    .team-cal-btn:hover{transform:translateY(-1px)}
    .team-cal-btn svg{width:13px;height:13px;stroke:#fff;fill:none;stroke-width:2}
    .badge-count{background:rgba(255,255,255,.25);color:#fff;font-size:10px;font-weight:800;padding:1px 6px;border-radius:10px}
    .team-body{padding:4px 0;flex:1}
    .member{display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid #f5f2ee;cursor:pointer;transition:background .15s}
    .member:last-child{border-bottom:none}
    .member:hover{background:#fff7ed}
    .m-av{width:38px;height:38px;border-radius:50%;border:2px solid var(--or5);overflow:hidden;background:var(--or4);flex-shrink:0}
    .m-av img{width:100%;height:100%;object-fit:cover}
    .m-name{font-size:14px;font-weight:700;display:flex;align-items:center;gap:7px;color:var(--dk)}
    .m-role{font-size:12px;color:var(--md);margin-top:2px;font-weight:500}
    .head-tag{background:linear-gradient(135deg,var(--or),#f90b0b);color:#f8f8f8;font-size:10px;font-weight:800;padding:2px 8px;border-radius:5px}
    .status-dot{width:9px;height:9px;border-radius:50%;margin-left:auto;flex-shrink:0;box-shadow:0 0 0 2px #fdfdfd}
    .st-active{background:var(--gr)}.st-leave{background:var(--rd)}
    .member-hidden{display:none!important}
    .view-all-btn{width:100%;padding:13px 16px;background:linear-gradient(135deg,#fff7ee,#ffeedc);border:none;border-top:1.5px dashed var(--or5);color:var(--or);font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .18s}
    .view-all-btn:hover{background:linear-gradient(135deg,var(--or3),#ffe1c2);color:#c2410c}
    .view-all-badge{background:var(--or);color:#fff;font-size:11px;font-weight:800;padding:2px 9px;border-radius:12px}
    .table-wrap{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-lg);overflow-x:auto;box-shadow:var(--shadow-sm)}
    table{width:100%;border-collapse:collapse;min-width:900px}
    th,td{padding:13px 16px;text-align:left;font-size:14px;border-bottom:1px solid var(--bd)}
    th{background:linear-gradient(to bottom,#f9f7f4,var(--lt));font-weight:800;font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--md);border-bottom:2px solid var(--bd2)}
    tbody tr:hover td{background:#fff8f4}
    tbody tr:last-child td{border-bottom:none}
    .so-code{font-family:'Courier New',monospace;font-weight:800;color:var(--or2);font-size:13px;background:var(--or3);padding:3px 9px;border-radius:6px;border:1px solid var(--or5)}
    .empty-state{text-align:center;padding:60px 20px;color:var(--md);font-size:15px;font-weight:500}
    .badge{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;display:inline-block}
    .b-progress{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}
    .job-type-tag{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:16px;font-size:12px;font-weight:700}
    .jt-solar_install{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}
    .jt-solar_wash{background:#cffafe;color:#155e75;border:1px solid #67e8f9}
    .jt-solar_maintenance{background:#fce7f3;color:#9d174d;border:1px solid #fbcfe8}
    .jt-electrical{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .jt-civil{background:#fef3c7;color:#78350f;border:1px solid #fcd34d}
    .jt-general{background:var(--lt);color:var(--md);border:1px solid var(--bd)}
    .cust-st{display:inline-block;padding:4px 11px;border-radius:14px;font-size:12px;font-weight:700}
    .cst-quote{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .cst-closed{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .cst-installing{background:#cffafe;color:#155e75;border:1px solid #67e8f9}
    .cst-success{background:#dcfce7;color:#14532d;border:1px solid #86efac}
    .cst-active{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}
    .cst-review{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .cst-done{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .cst-cancel{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .cst-other{background:var(--lt);color:var(--md);border:1px solid var(--bd)}
    .cust-name-btn{background:none;border:none;padding:0;font-family:inherit;font-size:14px;font-weight:700;color:var(--bl);cursor:pointer;text-align:left}
    .cust-name-btn:hover{text-decoration:underline}
    .wash-status-tag{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:18px;font-size:11px;font-weight:700;white-space:nowrap}
    .wst-ok{background:#dcfce7;color:#14532d;border:1px solid #86efac}
    .wst-soon{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
    .wst-overdue{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .wst-pending{background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe}
    .wst-scheduled{background:#dbeafe;color:#1e40af;border:1px solid #93c5fd}
    .cust-filter-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center}
    .cust-filter-btn{padding:7px 16px;border-radius:20px;border:1.5px solid var(--bd);background:var(--wh);font-family:inherit;font-size:13px;font-weight:700;color:var(--md);cursor:pointer;transition:all .18s;display:inline-flex;align-items:center;gap:6px}
    .cust-filter-btn:hover{border-color:var(--or5);background:var(--or3);color:var(--or2)}
    .cust-filter-btn.active{border-color:var(--or);background:var(--or);color:#fff}
    .cust-filter-btn .fbc{background:rgba(255,255,255,.25);font-size:10px;padding:1px 6px;border-radius:10px;font-weight:800}
    .cust-filter-btn:not(.active) .fbc{background:var(--lt);color:var(--md)}
    .wash-alert-bar{background:linear-gradient(135deg,#fff7ed,#fff3e0);border:1.5px solid #fdba74;border-radius:var(--radius-md);padding:12px 16px;margin-bottom:16px}
    .wash-alert-title{font-size:13px;font-weight:800;color:#c2410c;display:flex;align-items:center;gap:7px;margin-bottom:8px}
    .wash-alert-scroll{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;scrollbar-width:thin}
    .wash-alert-chip{flex-shrink:0;background:var(--wh);border:1.5px solid #fdba74;border-radius:10px;padding:8px 13px;display:flex;align-items:center;gap:9px;cursor:pointer;transition:all .15s;min-width:190px}
    .wash-alert-chip:hover{border-color:var(--or);box-shadow:var(--shadow-md);transform:translateY(-1px)}
    .wash-alert-chip.overdue{border-color:#fca5a5;background:#fff5f5}
    .wac-name{font-size:13px;font-weight:700;color:var(--dk);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px}
    .wac-date{font-size:11px;font-weight:600}
    .wac-date.overdue{color:#dc2626}
    .wac-date.soon{color:#d97706}
    .overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(26,21,18,.65);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:16px}
    .overlay.open{display:flex;animation:fadeIn .2s ease}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    .pmodal{background:var(--wh);border-radius:var(--radius-xl);width:760px;max-width:100%;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,.22);max-height:92vh;overflow-y:auto;animation:slideUp .22s ease}
    .pmodal-wide{width:940px}
    .pmodal-sm{width:500px}
    @keyframes slideUp{from{opacity:0;transform:translateY(18px) scale(.98)}to{opacity:1;transform:none}}
    .pmodal::-webkit-scrollbar{width:5px}
    .pmodal::-webkit-scrollbar-thumb{background:var(--or5);border-radius:3px}
    .pmodal-strip{height:5px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24,var(--or6));flex-shrink:0}
    .modal-header{padding:18px 24px 14px;display:flex;align-items:flex-start;justify-content:space-between;background:linear-gradient(to bottom,#fffbf7,var(--wh));border-bottom:1.5px solid var(--bd)}
    .modal-title{font-size:18px;font-weight:800;color:var(--dk)}
    .modal-subtitle{font-size:12px;color:var(--md);margin-top:3px}
    .modal-close{width:32px;height:32px;border-radius:50%;background:var(--lt);border:1.5px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--md);transition:all .18s;font-weight:700;flex-shrink:0;margin-top:2px}
    .modal-close:hover{background:var(--or);color:#fff;border-color:var(--or)}
    .modal-body{padding:20px 24px}
    .finput{width:100%;padding:10px 13px;border-radius:var(--radius-sm);border:1.5px solid var(--bd);font-family:inherit;font-size:14px;color:var(--dk);outline:none;transition:all .18s;background:var(--wh);font-weight:500}
    .finput:focus{border-color:var(--or);box-shadow:0 0 0 3px rgba(249,115,22,.12)}
    .finput::placeholder{color:#c4bdb6}
    .frow{margin-bottom:14px}
    .flabel{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--or3);margin-bottom:5px;display:block}
    .fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .fcol-full{grid-column:1/-1}
    .ferr{background:#fee2e2;color:#b91c1c;padding:11px 14px;border-radius:var(--radius-sm);font-size:13px;margin-bottom:14px;border:1.5px solid #fca5a5;font-weight:600}
    .factions{display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1.5px solid var(--bd)}
    .finfo-box{padding:10px 13px;background:#fff7ed;border:1.5px solid #fdba74;border-radius:var(--radius-sm);font-size:13px;color:#c2410c;font-weight:600;margin-bottom:14px}
    .dtab-bar{display:flex;gap:0;border-bottom:2px solid var(--bd);margin-bottom:16px}
    .dtab{padding:9px 18px;font-size:13px;font-weight:700;color:var(--md);border:none;background:none;cursor:pointer;font-family:inherit;border-bottom:2.5px solid transparent;margin-bottom:-2px;transition:all .15s}
    .dtab:hover{color:var(--dk)}
    .dtab.active{color:var(--or);border-bottom-color:var(--or)}
    .dtab-panel{display:none}
    .dtab-panel.active{display:block;animation:fi .18s ease}
    .pinfo-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
    .pinfo-card{background:var(--lt);border:1.5px solid var(--bd);border-radius:var(--radius-sm);padding:10px 13px}
    .pinfo-label{font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--or3);margin-bottom:4px}
    .pinfo-val{font-size:14px;font-weight:700;color:var(--dk)}
    .wash-countdown{padding:14px 16px;border-radius:var(--radius-md);margin-bottom:14px;display:flex;align-items:center;gap:14px}
    .wash-countdown.ok{background:#f0fdf4;border:1.5px solid #86efac}
    .wash-countdown.soon{background:#fffbeb;border:1.5px solid #fde68a}
    .wash-countdown.overdue{background:#fff5f5;border:1.5px solid #fca5a5}
    .wash-countdown.scheduled{background:#eff6ff;border:1.5px solid #93c5fd}
    .wash-countdown.pending{background:var(--lt);border:1.5px solid var(--bd)}
    .wcd-num{font-size:32px;font-weight:900;line-height:1;min-width:55px;text-align:center}
    .wcd-num.ok{color:#16a34a}.wcd-num.soon{color:#d97706}.wcd-num.overdue{color:#dc2626}.wcd-num.scheduled{color:#2563eb}.wcd-num.pending{color:var(--md)}
    .wcd-label{font-size:13px;font-weight:700}
    .wcd-date{font-size:12px;color:var(--md);margin-top:2px}
    .milestone-item{position:relative;margin-bottom:12px;padding:10px 12px;background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-sm)}
    .milestone-item::before{content:'';position:absolute;left:-20px;top:14px;width:10px;height:10px;border-radius:50%;background:var(--or);border:2px solid #fff;box-shadow:0 0 0 2px var(--or5)}
    .milestone-date{font-size:11px;color:var(--md);font-weight:600;margin-bottom:3px}
    .milestone-note{font-size:13px;font-weight:700;color:var(--dk)}
    .milestone-by{font-size:11px;color:var(--md);margin-top:2px}
    .wash-log-tbl{width:100%;border-collapse:collapse;font-size:13px;margin-top:8px}
    .wash-log-tbl th{background:var(--lt);color:var(--md);padding:8px 10px;text-align:left;font-size:12px;border-bottom:2px solid var(--bd);font-weight:700}
    .wash-log-tbl td{padding:8px 10px;border-bottom:1px solid var(--bd)}
    .wash-num-circle{width:24px;height:24px;background:var(--or);color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800}
    .section-h{font-size:13px;font-weight:800;color:var(--or2);margin:18px 0 10px;padding-bottom:6px;border-bottom:2px solid var(--or5);display:flex;align-items:center;gap:6px}
    .section-h::before{content:'';display:inline-block;width:4px;height:14px;background:var(--or);border-radius:2px}
    .skill-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:7px}
    .skill-check{display:flex;align-items:center;gap:7px;padding:7px 11px;border-radius:var(--radius-sm);border:1.5px solid var(--bd);cursor:pointer;font-size:13px;font-weight:600;transition:all .15s;user-select:none;color:var(--dk)}
    .skill-check:hover,.skill-check.checked{border-color:var(--or);background:var(--or3);color:var(--or2)}
    .skill-check input[type=checkbox]{accent-color:var(--or);width:14px;height:14px}
    .comp-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:9px}
    .comp-card{border:1.5px solid var(--bd);border-radius:var(--radius-md);padding:9px 11px;background:var(--wh)}
    .comp-head{display:flex;align-items:center;gap:7px;margin-bottom:7px}
    .comp-label{font-size:13px;font-weight:700;color:var(--dk)}
    .comp-code{font-size:10px;color:var(--md);background:var(--lt);padding:2px 6px;border-radius:4px;margin-left:auto}
    .comp-select{width:100%;padding:6px 9px;border-radius:var(--radius-sm);border:1.5px solid var(--bd);font-family:inherit;font-size:13px;font-weight:600;background:var(--wh);cursor:pointer}
    .comp-select.lv-basic{background:#fef3c7;border-color:#fde68a;color:#92400e}
    .comp-select.lv-skill{background:#dbeafe;border-color:#bfdbfe;color:#1e40af}
    .comp-select.lv-expert{background:#dcfce7;border-color:#86efac;color:#14532d;font-weight:800}
    .sw-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:7px}
    .sw-custom-row{display:flex;gap:8px;margin-top:9px}
    .sw-custom-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:9px}
    .sw-tag{background:var(--bl);color:#fff;font-size:12px;font-weight:600;padding:4px 12px;border-radius:14px;display:inline-flex;align-items:center;gap:6px}
    .sw-tag .x{cursor:pointer;font-weight:800;opacity:.8;font-size:11px}
    .btn-other{padding:9px 14px;border-radius:var(--radius-sm);border:1.5px solid var(--or5);background:var(--or3);color:var(--or2);font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s}
    .btn-other:hover{background:var(--or);color:#fff}
    .resume-top{display:flex;gap:22px;padding:18px 22px;background:linear-gradient(135deg,#fffbf7,#fff8f0);border-bottom:1.5px solid var(--bd)}
    .photo-col{flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:7px}
    .photo-box{width:108px;height:135px;border:2.5px dashed var(--or5);border-radius:12px;overflow:hidden;cursor:pointer;background:#fff8f0;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;transition:all .2s}
    .photo-box:hover{border-color:var(--or);transform:translateY(-2px)}
    .photo-box img.resume-img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;display:none}
    .photo-overlay{position:absolute;inset:0;background:rgba(240,71,9,.72);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .18s}
    .photo-box:hover .photo-overlay{opacity:1}
    .photo-overlay span{color:#fff;font-size:11px;font-weight:800;text-align:center}
    .photo-placeholder{display:flex;flex-direction:column;align-items:center;gap:5px;color:var(--md)}
    .photo-placeholder svg{width:28px;height:28px;opacity:.5}
    .photo-placeholder span{font-size:10px;font-weight:700;text-align:center;color:var(--md)}
    .photo-label{font-size:10px;font-weight:700;color:var(--or3);letter-spacing:.08em;text-transform:uppercase}
    .resume-badge-abs{position:absolute;top:-6px;right:-6px;background:var(--or);color:#fff;font-size:9px;font-weight:800;padding:2px 6px;border-radius:6px;z-index:3}
    .resume-fields{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .resume-fields .frow{margin-bottom:0}
    input[type=file].hidden-file{display:none}
    .lic-list{display:flex;flex-direction:column;gap:11px}
    .lic-item{border:1.5px solid var(--bd);border-radius:var(--radius-md);padding:12px 14px;background:var(--lt)}
    .lic-item-head{display:flex;align-items:center;gap:8px;margin-bottom:10px}
    .lic-num{font-size:12px;font-weight:800;color:var(--or2);background:var(--or3);padding:2px 9px;border-radius:12px;border:1px solid var(--or5)}
    .lic-del{margin-left:auto;padding:3px 11px;background:#ec0101;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit}
    .lic-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}
    .lic-file-row{display:flex;gap:7px;align-items:center;margin-top:7px;flex-wrap:wrap}
    .lic-file-link{font-size:12px;color:#06fa60;font-weight:700;padding:3px 9px;background:#dcfce7;border-radius:5px;border:1px solid #13cc57;text-decoration:none}
    .btn-add-lic{width:100%;padding:11px;border:2px dashed var(--or5);background:var(--or3);color:var(--or2);border-radius:var(--radius-md);cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:all .18s;margin-top:10px}
    .btn-add-lic:hover{border-color:var(--or);background:var(--or4)}
    .dob-row{display:flex;gap:8px;align-items:center}
    .dob-row input[type=date]{flex:1}
    .dob-be{font-size:12px;color:var(--or2);font-weight:700;background:var(--or3);padding:6px 11px;border-radius:var(--radius-sm);border:1.5px solid var(--or5);white-space:nowrap;min-width:130px;text-align:center}
    .emp-id-note{font-size:12px;color:var(--md);margin-top:4px}
    .head-info-box{padding:11px 14px;background:var(--or3);border:1.5px solid var(--or5);border-radius:var(--radius-sm);font-size:13px;color:var(--or2);font-weight:600}
    .p-top{padding:18px 22px;display:flex;gap:16px;align-items:flex-start;border-bottom:1.5px solid var(--bd);background:linear-gradient(to bottom,#fffbf7,var(--wh));position:relative}
    .p-photo{width:84px;height:84px;border-radius:14px;overflow:hidden;border:2.5px solid var(--or5);background:var(--or4);flex-shrink:0}
    .p-photo img{width:100%;height:100%;object-fit:cover}
    .p-fullname{font-size:19px;font-weight:800;line-height:1.25;color:var(--dk)}
    .p-engname{font-size:13px;color:var(--md);font-weight:500;margin-top:2px}
    .p-role-tag{margin-top:7px;display:inline-block;background:var(--or3);border:1.5px solid var(--or5);color:var(--or2);font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px}
    .p-close{position:absolute;top:14px;right:14px;width:30px;height:30px;border-radius:50%;background:var(--lt);border:1.5px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--md);transition:all .18s;font-weight:700}
    .p-close:hover{background:var(--or);color:#fff;border-color:var(--or)}
    .igrid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
    .ilabel{font-size:11px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;color:var(--or3);margin-bottom:3px}
    .ival{font-size:14px;font-weight:700;color:var(--dk)}
    .sk-wrap{display:flex;flex-wrap:wrap;gap:7px;margin-top:7px}
    .sk{font-size:12px;font-weight:600;padding:4px 11px;border-radius:18px;background:var(--or3);color:var(--or2);border:1.5px solid var(--or5)}
    .profile-section{margin-top:14px}
    .profile-comp-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:7px}
    .pc-card{display:flex;align-items:center;gap:7px;padding:7px 11px;border-radius:var(--radius-sm);background:var(--lt);border:1.5px solid var(--bd);font-size:13px}
    .pc-card.lv-basic{background:#fef3c7;border-color:#fde68a}
    .pc-card.lv-skill{background:#dbeafe;border-color:#bfdbfe}
    .pc-card.lv-expert{background:#dcfce7;border-color:#86efac;font-weight:700}
    .pc-card.lv-none{opacity:.45}
    .pc-lbl{flex:1;color:var(--dk)}.pc-val{font-weight:800;font-size:11px;color:var(--dk)}
    .profile-lic-item{padding:9px 12px;border:1.5px solid var(--bd);border-radius:var(--radius-sm);margin-bottom:7px;background:var(--lt)}
    .profile-lic-title{font-weight:800;font-size:14px;color:var(--or2)}
    .profile-lic-meta{font-size:12px;color:var(--md);margin-top:2px}
    .autocomp{position:relative}
    .autocomp-list{position:absolute;top:100%;left:0;right:0;background:var(--wh);border:1.5px solid var(--or5);border-top:none;border-radius:0 0 var(--radius-sm) var(--radius-sm);max-height:220px;overflow-y:auto;z-index:20;box-shadow:var(--shadow-md);display:none}
    .autocomp-list.open{display:block}
    .ac-item{padding:10px 13px;cursor:pointer;border-bottom:1px solid var(--bd);font-size:14px;transition:background .12s}
    .ac-item:last-child{border-bottom:none}
    .ac-item:hover,.ac-item.ac-active{background:var(--or3);color:var(--or2)}
    .ac-item-name{font-weight:700;color:var(--dk)}
    .ac-item-meta{font-size:12px;color:var(--md);margin-top:2px}
    .ac-item:hover .ac-item-name,.ac-item:hover .ac-item-meta,.ac-item.ac-active .ac-item-name,.ac-item.ac-active .ac-item-meta{color:var(--or2)}
    .ac-empty{padding:11px 13px;font-size:13px;color:var(--md);text-align:center}
    .cust-banner{padding:10px 13px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;margin-top:7px;display:none;align-items:center;gap:8px}
    .cust-banner-old{background:#fff7ee;border:1.5px solid var(--or5);color:#c84b07}
    .cust-banner-new{background:#dcfce7;border:1.5px solid #86efac;color:#14532d}
    .sched-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .sched-grid .frow{margin-bottom:0}
    .sched-full{grid-column:1/-1}
    .tl-wrap{border:1.5px solid var(--bd);border-radius:var(--radius-lg);background:var(--wh);overflow:hidden;box-shadow:var(--shadow-sm)}
    .tl-header{display:flex;align-items:center;gap:9px;padding:11px 15px;background:linear-gradient(to bottom,#fffbf7,var(--wh));border-bottom:1.5px solid var(--bd)}
    .tl-mnav-btn{width:30px;height:30px;border-radius:50%;border:1.5px solid var(--bd);background:var(--wh);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:17px;color:var(--md);transition:all .15s;font-family:inherit;font-weight:700;flex-shrink:0}
    .tl-mnav-btn:hover{background:var(--or);color:#fff;border-color:var(--or)}
    .tl-mname{flex:1;text-align:center;font-size:14px;font-weight:800;color:var(--dk)}
    .tl-today-btn{padding:5px 13px;background:var(--lt);border:1.5px solid var(--bd);border-radius:7px;font-size:12px;font-weight:700;color:var(--md);cursor:pointer;font-family:inherit;transition:all .15s}
    .tl-today-btn:hover{background:var(--or3);color:var(--or2);border-color:var(--or5)}
    .tl-team-info{padding:7px 15px;background:#fff7ee;border-bottom:1px solid #ffe1cc;font-size:12px;color:#c84b07;font-weight:600;display:flex;align-items:center;gap:7px}
    .tl-team-info.no-team{background:#fef3c7;color:#92400e;border-color:#fde68a}
    .tl-months{display:grid;grid-template-columns:1fr 1fr;gap:0}
    .tl-month-block{padding:13px 15px 8px}
    .tl-month-block:first-child{border-right:1.5px solid var(--bd)}
    .tl-month-title{text-align:center;font-size:13px;font-weight:800;color:var(--dk);margin-bottom:9px}
    .tl-dhdrs{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-bottom:5px}
    .tl-dhdr{font-size:11px;font-weight:700;color:var(--md);text-align:center;padding:3px 0}
    .tl-dhdr.weekend{color:var(--rd)}
    .tl-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;user-select:none}
    .tl-cell{position:relative;aspect-ratio:1;min-height:40px;border:1.5px solid var(--bd);border-radius:7px;background:var(--wh);cursor:pointer;transition:all .12s;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden}
    .tl-cell:hover{border-color:var(--or5);transform:translateY(-1px);box-shadow:0 2px 6px rgba(249,115,22,.15)}
    .tl-cell.tl-other{opacity:.35;cursor:default;pointer-events:none}
    .tl-cell.tl-today{border-color:var(--or);border-width:2px}
    .tl-cell.tl-busy{background:repeating-linear-gradient(45deg,#fee2e2,#fee2e2 4px,#fecaca 4px,#fecaca 8px);border-color:#fca5a5}
    .tl-cell.tl-sel-s,.tl-cell.tl-sel-e{background:var(--or)!important;border-color:var(--or)!important;color:#fff;box-shadow:0 4px 12px rgba(249,115,22,.35);z-index:2}
    .tl-cell.tl-in-range{background:#ffe1cc!important;border-color:var(--or5)!important}
    .tl-d{font-size:13px;font-weight:700;color:var(--dk);line-height:1;margin-bottom:1px}
    .tl-cell.tl-today .tl-d{color:var(--or)}
    .tl-cell.tl-sel-s .tl-d,.tl-cell.tl-sel-e .tl-d{color:#fff}
    .tl-cell.tl-in-range .tl-d{color:#c84b07}
    .tl-busy-bar{position:absolute;bottom:3px;left:3px;right:3px;height:4px;border-radius:2px;background:#ef4444;pointer-events:none}
    .tl-cell.tl-sel-s .tl-busy-bar,.tl-cell.tl-sel-e .tl-busy-bar,.tl-cell.tl-in-range .tl-busy-bar{display:none}
    .tl-jobs-count{position:absolute;top:2px;right:2px;font-size:9px;font-weight:800;color:#ef4444;background:#fff;border-radius:7px;padding:0 3px;line-height:1.4;pointer-events:none}
    .tl-cell.tl-sel-s .tl-jobs-count,.tl-cell.tl-sel-e .tl-jobs-count,.tl-cell.tl-in-range .tl-jobs-count{display:none}
    .tl-summary{padding:10px 15px;background:linear-gradient(to bottom,#fffbf7,var(--wh));border-top:1.5px solid var(--bd);display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .tl-summary-info{flex:1;font-size:13px;color:var(--md);font-weight:600;min-width:180px}
    .tl-summary-info strong{color:var(--or);font-weight:800}
    .tl-summary-warn{color:var(--rd)!important;font-weight:700}
    .tl-clear-btn{padding:5px 11px;background:var(--lt);border:1.5px solid var(--bd);border-radius:6px;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;color:var(--md);transition:all .15s}
    .tl-clear-btn:hover{background:#fee2e2;color:#991b1b;border-color:#fca5a5}
    .tl-legend{padding:7px 15px 11px;display:flex;gap:13px;flex-wrap:wrap;font-size:11px;color:var(--md);border-top:1px solid var(--bd);background:#fafaf9}
    .tl-leg{display:flex;align-items:center;gap:5px}
    .tl-leg-box{width:13px;height:13px;border-radius:4px;border:1.5px solid var(--bd);flex-shrink:0}
    .tl-leg-box.busy{background:repeating-linear-gradient(45deg,#fee2e2,#fee2e2 3px,#fecaca 3px,#fecaca 6px);border-color:#fca5a5}
    .tl-leg-box.sel{background:var(--or);border-color:var(--or)}
    .tl-leg-box.range{background:#ffe1cc;border-color:var(--or5)}
    .tl-leg-box.today{border-color:var(--or);border-width:2px;background:var(--wh)}
    .tcal-overlay{display:none;position:fixed;inset:0;z-index:700;background:rgba(26,21,18,.78);backdrop-filter:blur(10px)}
    .tcal-overlay.open{display:flex;flex-direction:column;animation:fadeIn .22s ease}
    .tcal-fs{background:var(--bg);width:100%;height:100%;display:flex;flex-direction:column;overflow:hidden}
    .tcal-header{background:linear-gradient(135deg,#2d2521,#1a1512);border-bottom:4px solid var(--or);padding:18px 30px;display:flex;align-items:center;gap:16px;flex-shrink:0}
    .tcal-icon{width:46px;height:46px;background:linear-gradient(135deg,var(--or),#ea580c);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .tcal-icon svg{width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2}
    .tcal-title-block{flex:1;min-width:0}
    .tcal-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--or3);margin-bottom:2px}
    .tcal-title{font-size:21px;font-weight:800;color:#fff}
    .tcal-stat{display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.08);padding:3px 10px;border-radius:13px;font-size:12px;font-weight:600;color:#fff;margin-top:4px}
    .tcal-stat.has-jobs{background:rgba(240,71,9,.25);border:1px solid rgba(240,71,9,.45)}
    .tcal-close{width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;transition:all .18s;font-weight:700}
    .tcal-close:hover{background:var(--or);transform:rotate(90deg)}
    .tcal-body{flex:1;overflow-y:auto;padding:22px 30px}
    .tcal-content{max-width:1100px;margin:0 auto}
    .cal-sel-bar{background:var(--wh);border-radius:var(--radius-md);padding:5px;display:flex;gap:5px;margin-bottom:13px;box-shadow:var(--shadow-sm);border:1.5px solid var(--bd)}
    .cal-sel-btn{flex:1;padding:9px 13px;border-radius:9px;border:none;background:none;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:9px;transition:background .15s;text-align:left}
    .cal-sel-btn.active{background:#fff0e6}
    .cal-sel-btn:hover:not(.active){background:var(--lt)}
    .cal-sel-icon{width:30px;height:30px;border-radius:7px;background:var(--or);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .cal-sel-icon svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:1.8}
    .cal-sel-icon.gray{background:#e2e2e2}
    .cal-sel-icon.gray svg{stroke:#888}
    .cal-sel-lbl{font-size:13px;font-weight:700;color:var(--dk)}
    .cal-sel-sub{font-size:11px;color:var(--md);margin-top:1px}
    .cal-wrap{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-lg);overflow:hidden}
    .cal-months{display:grid;grid-template-columns:1fr 1fr}
    .cal-block{padding:17px 17px 13px}
    .cal-block:first-child{border-right:1px solid var(--bd)}
    .cal-mnav{display:flex;align-items:center;margin-bottom:12px}
    .cal-mnav-btn{width:27px;height:27px;border-radius:50%;border:none;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--md);transition:background .15s}
    .cal-mnav-btn:hover{background:var(--lt)}
    .cal-mnav-btn.invisible{visibility:hidden}
    .cal-mname{flex:1;text-align:center;font-size:14px;font-weight:700;color:var(--dk)}
    .cal-dhdrs{display:grid;grid-template-columns:repeat(7,1fr);margin-bottom:5px}
    .cal-dhdr{text-align:center;font-size:11px;font-weight:600;color:var(--md);padding:3px 0}
    .cal-dgrid{display:grid;grid-template-columns:repeat(7,1fr);row-gap:3px}
    .cal-day{position:relative;text-align:center;cursor:pointer;user-select:none;padding:3px 0 13px}
    .cal-di{width:32px;height:32px;margin:0 auto;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:500;color:var(--dk);transition:background .12s;position:relative;z-index:2}
    .cal-day:hover .cal-di{background:var(--lt)}
    .cal-day.cal-other .cal-di{color:#ccc}
    .cal-day.cal-other{pointer-events:none}
    .cal-day.cal-today .cal-di{border:2px solid var(--or);color:var(--or);font-weight:700}
    .cal-day.cal-sel-s .cal-di,.cal-day.cal-sel-e .cal-di{background:var(--or);color:#fff;font-weight:700}
    .cal-day.cal-in-range::before,.cal-day.cal-sel-s::before,.cal-day.cal-sel-e::before{content:'';position:absolute;top:7px;height:32px;background:#ffe1cc;z-index:1}
    .cal-day.cal-in-range::before{left:0;right:0}
    .cal-day.cal-sel-s::before{left:50%;right:0}
    .cal-day.cal-sel-e::before{left:0;right:50%}
    .cal-day.cal-sel-s.cal-sel-e::before{display:none}
    .cal-evdots{position:absolute;bottom:2px;left:50%;transform:translateX(-50%);display:flex;gap:2px;z-index:3;align-items:center;height:7px}
    .cal-evdot{width:5px;height:5px;border-radius:50%;background:var(--or);flex-shrink:0}
    .cal-evcount{font-size:8px;font-weight:800;color:var(--md);margin-left:1px}
    .cal-legend{display:flex;gap:13px;flex-wrap:wrap;padding:9px 17px;border-top:1px solid var(--bd);background:#fafaf9;font-size:11px;color:var(--md)}
    .cal-leg{display:flex;align-items:center;gap:5px}
    .cal-leg-dot{width:8px;height:8px;border-radius:50%}
    .cal-footer{padding:11px 17px;border-top:1px solid var(--bd);background:#fafaf9;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .cal-footer-note{font-size:12px;color:var(--md);flex:1}
    .cal-popup-bg{display:none;position:fixed;inset:0;background:rgba(26,21,18,.62);backdrop-filter:blur(7px);align-items:center;justify-content:center;padding:16px;z-index:800}
    .cal-popup-bg.open{display:flex;animation:fadeIn .18s ease}
    .cal-popup{background:var(--wh);border-radius:var(--radius-xl);width:500px;max-width:100%;box-shadow:0 24px 60px rgba(0,0,0,.22);overflow:hidden;max-height:86vh;display:flex;flex-direction:column}
    .cal-popup-body{overflow-y:auto;flex:1}
    .cal-popup-strip{height:4px;background:linear-gradient(90deg,var(--or),#f78833,var(--or6));flex-shrink:0}
    .cal-popup-head{padding:13px 17px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:9px;flex-shrink:0}
    .cal-popup-date{background:#fff0e6;color:#c84b07;font-size:13px;font-weight:700;padding:5px 12px;border-radius:7px;border:1px solid #fdc59f;flex:1;text-align:center}
    .cal-popup-count{background:var(--or3);color:var(--or2);font-size:12px;font-weight:800;padding:3px 11px;border-radius:18px;border:1px solid var(--or5);white-space:nowrap}
    .cal-popup-close{width:26px;height:26px;border-radius:50%;border:1px solid var(--bd);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--md);transition:all .15s}
    .cal-popup-close:hover{background:var(--or);color:#fff;border-color:var(--or)}
    .cal-popup-inner{padding:13px 17px}
    .cal-ev-card{border:1.5px solid var(--bd);border-radius:var(--radius-md);padding:13px 13px 13px 18px;margin-bottom:9px;position:relative;overflow:hidden;background:var(--wh)}
    .cal-ev-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:5px;background:var(--or)}
    .cal-ev-card:last-child{margin-bottom:0}
    .cal-ev-top{display:flex;align-items:flex-start;justify-content:space-between;gap:7px;margin-bottom:7px}
    .cal-so{background:#fff0e6;color:#c84b07;border:1px solid #fdc59f;font-size:10px;font-weight:800;padding:2px 7px;border-radius:5px;font-family:'Courier New',monospace}
    .cal-ev-cust{font-size:15px;font-weight:800;color:var(--dk);margin-bottom:3px;display:flex;align-items:center;gap:7px}
    .cal-ev-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;background:var(--or)}
    .cal-ev-job{font-size:12px;color:var(--md);margin-bottom:9px;padding-left:16px}
    .cal-ev-meta{display:grid;grid-template-columns:auto 1fr;gap:4px 10px;font-size:12px}
    .cal-ev-ml{color:var(--md);font-weight:600;white-space:nowrap}
    .cal-ev-mv{color:var(--dk);font-weight:600}
    .cal-empty{text-align:center;padding:36px 20px;color:var(--md);font-size:14px}
    .members-modal{background:var(--wh);border-radius:var(--radius-xl);width:760px;max-width:100%;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 28px 80px rgba(0,0,0,.22);animation:slideUp .22s ease}
    .mm-strip{height:5px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24,var(--or6));flex-shrink:0}
    .mm-head{padding:18px 22px;background:linear-gradient(135deg,#2d2521,#1a1512);display:flex;align-items:center;gap:13px;flex-shrink:0;border-bottom:3px solid var(--or)}
    .mm-icon{width:42px;height:42px;background:linear-gradient(135deg,var(--or),#ea580c);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .mm-icon svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2}
    .mm-title-block{flex:1;min-width:0}
    .mm-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--or3);margin-bottom:2px}
    .mm-name{font-size:18px;font-weight:800;color:#fff}
    .mm-sub{font-size:12px;color:#d4cdc4;margin-top:2px}
    .mm-close{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.22);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff;transition:all .18s;font-weight:700}
    .mm-close:hover{background:var(--or);border-color:var(--or);transform:rotate(90deg)}
    .mm-body{flex:1;overflow-y:auto;padding:16px 20px;background:#fafaf8}
    .mm-search-wrap{margin-bottom:12px;position:relative}
    .mm-search-wrap input{width:100%;padding:10px 14px 10px 38px;border:1.5px solid var(--bd);border-radius:var(--radius-sm);font-family:inherit;font-size:14px;background:var(--wh);outline:none;transition:border .18s}
    .mm-search-wrap input:focus{border-color:var(--or)}
    .mm-search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--md);pointer-events:none}
    .mm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:11px}
    .mm-card{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-md);padding:13px;display:flex;align-items:center;gap:11px;cursor:pointer;transition:all .18s;position:relative;overflow:hidden}
    .mm-card:hover{border-color:var(--or5);box-shadow:var(--shadow-md);transform:translateY(-2px)}
    .mm-card.is-head{background:linear-gradient(135deg,#fff7ee,#fff);border-color:var(--or5)}
    .mm-card.is-head::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(to bottom,var(--or),#ea580c)}
    .mm-card-info{flex:1;min-width:0}
    .mm-card-name{font-size:14px;font-weight:800;color:var(--dk);display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .mm-card-role{font-size:12px;color:var(--md);margin-top:2px}
    .mm-empty{text-align:center;padding:36px 20px;color:var(--md);font-size:14px;grid-column:1/-1}
    .acc-pw-wrap{display:flex;align-items:center;gap:6px}
    .lock-banner{padding:10px 13px;background:#eff6ff;border:1.5px solid #93c5fd;border-radius:var(--radius-sm);font-size:12px;color:#1e40af;font-weight:600;margin-top:6px;display:flex;align-items:center;gap:6px}
    @media(max-width:768px){
      .main{padding:14px 16px}
      .nav-inner{padding:0 16px;height:62px}
      .nav-tab span{display:none}
      .team-grid{grid-template-columns:1fr}
      .fgrid,.sched-grid,.resume-fields,.igrid,.profile-comp-grid,.comp-grid,.skill-grid,.sw-grid{grid-template-columns:1fr}
      .cal-months,.tl-months{grid-template-columns:1fr}
      .cal-block:first-child,.tl-month-block:first-child{border-right:none;border-bottom:1px solid var(--bd)}
      .tcal-header{padding:13px 16px}
      .tl-cell{min-height:35px}
      .pinfo-grid{grid-template-columns:1fr}
    }
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <div class="nav-logo">
      <div class="nav-mark">3E</div>
      <div>
        <div class="nav-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
        <div class="nav-sub">ระบบจัดการทีมช่าง</div>
      </div>
    </div>
    <div class="nav-tabs">
      <button class="nav-tab active" onclick="switchTab('teams',this)">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>ทีมช่าง</span>
        <span class="nav-badge-count">{{ $teams->count() }}</span>
      </button>
      <button class="nav-tab" onclick="switchTab('schedules',this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>ตารางงาน</span>
        <span class="nav-badge-count">{{ $schedules->count() }}</span>
      </button>
      <button class="nav-tab" onclick="switchTab('customers',this)">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>ลูกค้า PROJECT</span>
        <span class="nav-badge-count">{{ $customers->count() }}</span>
      </button>
      <button class="nav-tab" onclick="switchTab('accounts',this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span>บัญชี Solar</span>
        <span class="nav-badge-count">{{ $accounts->count() }}</span>
      </button>
    </div>
    <div class="nav-end">
      <button class="btn btn-primary btn-sm" onclick="openModal('modal-tech')">+ ช่าง</button>
      <button class="btn btn-solar btn-sm" onclick="openAddSchedModal()">+ งาน</button>
    </div>
  </div>
</nav>

<div class="main">

  @if(session('success'))
    <div class="flash flash-success" id="flash-msg">{{ session('success') }}</div>
  @endif
  @if($errors->has('delete'))
    <div class="flash flash-error">{{ $errors->first('delete') }}</div>
  @endif

  {{-- ════════════ PANEL: ทีมช่าง ════════════ --}}
  <div class="panel active" id="panel-teams">
    <div class="panel-header">
      <div class="panel-title">ทีมช่าง ({{ $teams->count() }} ทีม · {{ $stats['total_tech'] }} คน)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" placeholder=" ค้นหาช่าง..." oninput="filterTeams(this.value)">
      </div>
    </div>
    @if($teams->count() === 0)
      <div class="empty-state">ยังไม่มีทีมช่างในระบบ</div>
    @else
      <div class="team-grid" id="team-grid-wrap">
        @foreach($teams as $team)
          @php
            $members    = $technicians->where('emp_team', $team['team_name']);
            $head       = $members->firstWhere('emp_position','หัวหน้าทีม');
            $others     = $members->where('emp_position','!=','หัวหน้าทีม');
            $allMbr     = collect(); if($head) $allMbr->push($head); foreach($others as $o) $allMbr->push($o);
            $total      = $allMbr->count();
            $teamScheds = $schedules->where('team_name',$team['team_name'])->values();
          @endphp
          <div class="team-card">
            <div class="team-head-bar">
              <div style="flex:1;min-width:0">
                <div class="team-title">{{ $team['team_name'] }}</div>
                <div class="team-meta">สมาชิก {{ $members->count() }} คน</div>
              </div>
              <button type="button" class="team-cal-btn" onclick="openTeamCalendar('{{ addslashes($team['team_name']) }}')">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                ปฏิทิน <span class="badge-count team-job-count" data-team="{{ $team['team_name'] }}">{{ $teamScheds->count() }}</span>
              </button>
            </div>
            <div class="team-body">
              @foreach($allMbr as $idx => $m)
                @php $isHead = ($m->emp_position === 'หัวหน้าทีม'); @endphp
                <div class="member {{ $idx >= 5 ? 'member-hidden' : '' }}"
                     data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                     onclick="openProfileFromEl(this)">
                  <div class="m-av">
                    <img src="{{ $m->img ? asset('storage/'.$m->img) : '' }}"
                         onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect width=\'64\' height=\'64\' fill=\'%23fed7aa\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-size=\'16\' fill=\'%23f97316\' font-weight=\'bold\'%3E3E%3C/text%3E%3C/svg%3E'">
                  </div>
                  <div style="flex:1;min-width:0">
                    <div class="m-name">{{ $m->emp_name ?: $m->emp_id }} @if($isHead)<span class="head-tag">หัวหน้า</span>@endif</div>
                    <div class="m-role">{{ $m->emp_id }}@if($m->emp_nickname) · {{ $m->emp_nickname }}@endif</div>
                  </div>
                  <div class="status-dot st-{{ $m->status }}"></div>
                  <div style="display:flex;gap:5px;margin-left:9px" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                    <form method="POST" action="{{ route('tech.delete', $m->emp_id) }}" onsubmit="return confirm('ลบ {{ $m->emp_name ?: $m->emp_id }} ?')">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </div>
              @endforeach
              @if($total > 5)
                <button type="button" class="view-all-btn" onclick='openTeamMembers(@json($team['team_name']))'>
                  ดูสมาชิกทั้งหมด <span class="view-all-badge">{{ $total }}</span>
                </button>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ════════════ PANEL: ตารางงาน ════════════ --}}
  <div class="panel" id="panel-schedules">
    <div class="panel-header">
      <div class="panel-title">ตารางงานทั้งหมด ({{ $schedules->count() }} งาน)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" placeholder=" ค้นหางาน..." oninput="filterTable('sched-tbody',this.value)">
      </div>
    </div>
    @if($schedules->count() === 0)
      <div class="empty-state">ยังไม่มีงานในระบบ</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>SO</th><th>ลูกค้า / งาน</th><th>ประเภท</th>
              <th>สถานที่</th><th>ทีม</th>
              <th>วันเริ่ม</th><th>วันสิ้นสุด</th><th>หมายเหตุ</th><th>จัดการ</th>
            </tr>
          </thead>
          <tbody id="sched-tbody">
            @foreach($schedules as $s)
              @php
                // ดึง job_type จาก note prefix [xxx] note...
                $jobType = 'general';
                $noteClean = $s->note ?? '';
                if (preg_match('/^\[([a-z_]+)\]\s*(.*)$/s', $noteClean, $m)) {
                  $jobType   = $m[1];
                  $noteClean = $m[2];
                }
              @endphp
              <tr data-search="{{ strtolower($s->so_number.' '.$s->customer_name.' '.$s->job_title.' '.$s->team_name) }}">
                <td><span class="so-code">{{ $s->so_number }}</span></td>
                <td>
                  <strong>{{ $s->customer_name }}</strong><br>
                  <small style="color:var(--md);font-size:13px">{{ $s->job_title }}</small>
                </td>
                <td><span class="job-type-tag jt-{{ $jobType }}">{{ $jobTypes[$jobType] ?? $jobType }}</span></td>
                <td style="font-size:13px">{{ $s->job_location }}</td>
                <td><strong>{{ $s->team_name }}</strong></td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->start_date)->format('d/m/Y') }}</td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->end_date)->format('d/m/Y') }}</td>
                <td><small style="color:var(--md);font-size:13px">{{ $noteClean }}</small></td>
                <td>
                  <div style="display:flex;gap:5px">
                    <button type="button" class="btn btn-sm btn-ghost"
                            data-sched="{{ json_encode(array_merge($s->toArray(), ['job_type' => $jobType, 'note_clean' => $noteClean]), JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                            onclick="openEditSchedFromEl(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('sched.delete', $s->id) }}" onsubmit="return confirm('ลบงาน {{ $s->so_number }} ?')">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ════════════ PANEL: ลูกค้า PROJECT ════════════ --}}
  <div class="panel" id="panel-customers">
    <div class="panel-header">
      <div class="panel-title">ลูกค้า PROJECT ({{ $customers->count() }} ราย)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" id="cust-search" placeholder=" ค้นหาลูกค้า..." oninput="filterCustTable(this.value)">
      </div>
    </div>

    @if($washAlerts->count() > 0)
    <div class="wash-alert-bar">
      <div class="wash-alert-title">⚠ แจ้งเตือนล้างแผง Solar ({{ $washAlerts->count() }} ราย)</div>
      <div class="wash-alert-scroll">
        @foreach($washAlerts as $wa)
          @php
            $daysLeft  = $wa->daysUntilWash();
            $isOver    = $wa->isWashOverdue();
            $chipCls   = $isOver ? 'wash-alert-chip overdue' : 'wash-alert-chip';
            $dateCls   = $isOver ? 'wac-date overdue' : 'wac-date soon';
            $dateText  = $isOver
              ? 'เลยกำหนด '.abs($daysLeft).' วัน'
              : ($daysLeft === 0 ? 'ถึงกำหนดวันนี้!' : 'อีก '.$daysLeft.' วัน · '.date('d/m/Y', strtotime($wa->wash_next)));
          @endphp
          <div class="{{ $chipCls }}"
               data-cust="{{ json_encode($wa, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
               onclick="openCustDetail(this)">
            <div>
              <div class="wac-name" title="{{ $wa->name }}">{{ $wa->name }}</div>
              <div class="{{ $dateCls }}">{{ $dateText }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    <div class="cust-filter-bar">
      <button class="cust-filter-btn active" data-cat="all" onclick="filterCustCat('all',this)">
        ทั้งหมด <span class="fbc">{{ $customers->count() }}</span>
      </button>
      <button class="cust-filter-btn" data-cat="solar" onclick="filterCustCat('solar',this)">
         Solar <span class="fbc">{{ $custSummary['solar']->count() }}</span>
      </button>
      <button class="cust-filter-btn" data-cat="electrical" onclick="filterCustCat('electrical',this)">
         ไฟฟ้า <span class="fbc">{{ $custSummary['electrical']->count() }}</span>
      </button>
      <button class="cust-filter-btn" data-cat="civil" onclick="filterCustCat('civil',this)">
         โยธา <span class="fbc">{{ $custSummary['civil']->count() }}</span>
      </button>
      <button class="cust-filter-btn" data-cat="general" onclick="filterCustCat('general',this)">
         ทั่วไป <span class="fbc">{{ $custSummary['general']->count() }}</span>
      </button>
    </div>

    @if($customers->count() === 0)
      <div class="empty-state">ยังไม่มีลูกค้าในระบบ</div>
    @else
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>ชื่อลูกค้า</th>
            <th style="width:140px">ประเภท</th>
            <th style="width:160px">สถานะโครงการ</th>
            <th style="width:130px">จัดการ</th>
          </tr>
        </thead>
        <tbody id="cust-tbody">
          @foreach($customers as $idx => $c)
            @php
              $cat = $c->getCategory();

              $stClass = match(true) {
                $c->status === 'เสนอ' => 'cst-quote',
                $c->status === 'ปิดการขาย' => 'cst-closed',
                $c->status === 'กำลังติดตั้ง' => 'cst-installing',
                $c->status === 'ติดตั้งสำเร็จ' => 'cst-success',
                $c->status === 'ดำเนินการ' => 'cst-active',
                in_array($c->status, ['ทดสอบ/ตรวจรับ','ตรวจรับงาน']) => 'cst-review',
                $c->status === 'เสร็จสิ้น' => 'cst-done',
                $c->status === 'ยกเลิก' => 'cst-cancel',
                default => 'cst-other',
              };

              // นัดล้างแผง chip — ใช้ upcoming_wash_date ก่อน ถ้ามี
              $washHtml = '';
              if ($cat === 'solar' && $c->status === 'ติดตั้งสำเร็จ') {
                if (!empty($c->upcoming_wash_date)) {
                  // มี schedule นัดล้าง — แสดงตามนั้น (ทับ wash_next)
                  $upDate = \Carbon\Carbon::parse($c->upcoming_wash_date);
                  $upDays = (int) now()->startOfDay()->diffInDays($upDate->startOfDay(), false);
                  if ($upDays === 0) {
                    $washHtml = '<span class="wash-status-tag wst-overdue" style="margin-left:8px">นัดล้าง วันนี้ ('.$upDate->format('d/m/y').')</span>';
                  } elseif ($upDays > 0) {
                    $washHtml = '<span class="wash-status-tag wst-scheduled" style="margin-left:8px">นัดล้าง '.$upDate->format('d/m/y').' (อีก '.$upDays.' วัน)</span>';
                  }
                } else {
                  $daysLeft = $c->daysUntilWash();
                  if ($c->isWashOverdue()) {
                    $washHtml = '<span class="wash-status-tag wst-overdue" style="margin-left:8px">เลยกำหนด '.abs($daysLeft).' วัน</span>';
                  } elseif ($daysLeft === 0) {
                    $washHtml = '<span class="wash-status-tag wst-overdue" style="margin-left:8px">ถึงกำหนดวันนี้!</span>';
                  } elseif ($c->isWashDueSoon()) {
                    $washHtml = '<span class="wash-status-tag wst-soon" style="margin-left:8px">นัดล้าง อีก '.$daysLeft.' วัน</span>';
                  } elseif ($daysLeft !== null) {
                    $washHtml = '<span class="wash-status-tag wst-ok" style="margin-left:8px">นัดล้าง อีก '.$daysLeft.' วัน</span>';
                  } else {
                    $washHtml = '<span class="wash-status-tag wst-pending" style="margin-left:8px">ยังไม่ตั้งกำหนด</span>';
                  }
                }
              }
            @endphp
            <tr data-cat="{{ $cat }}"
                data-search="{{ strtolower($c->name.' '.($c->desc ?? '').' '.($c->contact_name ?? '')) }}">
              <td style="color:#999;font-weight:600;font-size:13px">{{ $idx+1 }}</td>
              <td>
                <div style="display:flex;align-items:center;flex-wrap:wrap">
                  <button class="cust-name-btn"
                          data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                          onclick="openCustDetail(this)">{{ $c->name }}</button>
                  {!! $washHtml !!}
                </div>
                @if($c->desc)<div style="color:var(--md);font-size:12px;margin-top:2px">{{ $c->desc }}</div>@endif
                @if($c->contact_name)<div style="color:var(--md);font-size:11px;margin-top:1px">{{ $c->contact_name }}@if($c->phone) · {{ $c->phone }}@endif</div>@endif
              </td>
              <td>
                @php $jt = $c->type_project ?? ''; @endphp
                <span class="job-type-tag jt-{{ $jt ?: 'general' }}">{{ $jobTypes[$jt] ?? ($jt ?: 'ทั่วไป') }}</span>
              </td>
              <td>
                <form method="POST" action="{{ route('cust.status', $c->id) }}">
                  @csrf
                  <select name="status"
                          onchange="this.form.submit()"
                          class="cust-st {{ $stClass }}"
                          style="border:none;outline:none;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;border-radius:14px;padding:4px 10px;appearance:none;-webkit-appearance:none">
                    @foreach(['เสนอราคา','กำลังติดตั้ง','ติดตั้งสำเร็จ','ยกเลิก'] as $opt)
                      <option value="{{ $opt }}" {{ ($c->status ?? 'เสนอราคา') === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td>
                <div style="display:flex;gap:5px;flex-wrap:wrap">
                  <button type="button" class="btn btn-sm btn-ghost"
                          data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                          onclick="openCustEdit(this)">แก้ไข</button>
                  <form method="POST" action="{{ route('cust.delete', $c->id) }}" onsubmit="return confirm('ลบลูกค้า {{ addslashes($c->name) }} ?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- ════════════ PANEL: บัญชี Solar ════════════ --}}
  <div class="panel" id="panel-accounts">
    <div class="panel-header">
      <div class="panel-title">บัญชีผู้ใช้ Solar / Monitoring ({{ $accounts->count() }} บัญชี)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" placeholder=" ค้นหาบัญชี..." oninput="filterTable('acc-tbody',this.value)">
        <button class="btn btn-solar" onclick="openAccAdd()">+ เพิ่มบัญชี</button>
      </div>
    </div>
    @if($accounts->count() === 0)
      <div class="empty-state">ยังไม่มีบัญชีในระบบ</div>
    @else
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:50px">#</th>
            <th>ชื่อระบบ / Platform</th>
            <th>ลูกค้า / Inverter</th>
            <th>Username / Email</th>
            <th>Password</th>
            <th style="width:130px">จัดการ</th>
          </tr>
        </thead>
        <tbody id="acc-tbody">
          @foreach($accounts as $idx => $a)
            <tr data-search="{{ strtolower(($a->plane ?? '').' '.($a->customer ?? '').' '.($a->inverter ?? '').' '.($a->username ?? '')) }}">
              <td style="color:#999;font-weight:600;font-size:13px">{{ $idx+1 }}</td>
              <td>
                <div style="font-weight:700;font-size:14px">{{ $a->plane ?: '—' }}</div>
                @if($a->inverter)<div style="font-size:12px;color:var(--md);margin-top:2px">{{ $a->inverter }}</div>@endif
              </td>
              <td><div style="font-weight:600">{{ $a->customer ?: '—' }}</div></td>
              <td>
                @if($a->username)<div style="font-family:'Courier New',monospace;font-size:13px;font-weight:700;color:var(--bl)">{{ $a->username }}</div>@endif
                @if($a->email)<div style="font-size:12px;color:var(--md);margin-top:2px">{{ $a->email }}</div>@endif
                @if(!$a->username && !$a->email)<span style="color:#ccc">—</span>@endif
              </td>
              <td>
                @if($a->password)
                  <div class="acc-pw-wrap">
                    <span class="acc-pw-text" data-pw="{{ $a->password }}" style="font-family:'Courier New',monospace;font-size:13px;font-weight:700">••••••••</span>
                    <button type="button" class="btn btn-sm btn-ghost" style="padding:2px 8px;font-size:11px" onclick="togglePw(this)">แสดง</button>
                    <button type="button" class="btn btn-sm btn-ghost" style="padding:2px 8px;font-size:11px" onclick="copyPw('{{ addslashes($a->password) }}',this)">คัดลอก</button>
                  </div>
                @endif
                @if($a->app_password)
                  <div style="font-size:11px;color:var(--md);margin-top:3px">App PW:
                    <span class="acc-pw-text" data-pw="{{ $a->app_password }}" style="font-family:'Courier New',monospace;font-weight:700">••••••••</span>
                    <button type="button" class="btn btn-sm btn-ghost" style="padding:1px 6px;font-size:10px;margin-left:2px" onclick="togglePw(this)">แสดง</button>
                  </div>
                @endif
                @if(!$a->password && !$a->app_password)<span style="color:#ccc">—</span>@endif
              </td>
              <td>
                <div style="display:flex;gap:5px">
                  <button type="button" class="btn btn-sm btn-ghost"
                          data-acc="{{ json_encode($a, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                          onclick="openAccEdit(this)">แก้ไข</button>
                  <form method="POST" action="{{ route('account.delete', $a->id) }}" onsubmit="return confirm('ลบบัญชีนี้?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

</div>
<div class="overlay" id="members-overlay" onclick="if(event.target===this)closeMembersModal()">
  <div class="members-modal" onclick="event.stopPropagation()">
    <div class="mm-strip"></div>
    <div class="mm-head">
      <div class="mm-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <div class="mm-title-block">
        <div class="mm-eyebrow">สมาชิกทั้งหมด</div>
        <div class="mm-name" id="mm-team-name">—</div>
        <div class="mm-sub" id="mm-count">0 คน</div>
      </div>
      <button class="mm-close" onclick="closeMembersModal()">✕</button>
    </div>
    <div class="mm-body">
      <div class="mm-search-wrap">
        <svg class="mm-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="ค้นหา..." oninput="filterMembers(this.value)">
      </div>
      <div class="mm-grid" id="mm-grid"></div>
    </div>
  </div>
</div>

{{--  Team Calendar fullscreen  --}}
<div class="tcal-overlay" id="tcal-overlay">
  <div class="tcal-fs">
    <div class="tcal-header">
      <div class="tcal-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div class="tcal-title-block">
        <div class="tcal-eyebrow">ตารางเวลางาน</div>
        <div class="tcal-title" id="tcal-team-name">—</div>
        <div><span class="tcal-stat" id="tcal-job-count">0 งาน</span></div>
      </div>
      <button class="tcal-close" onclick="closeTeamCalendar()">✕</button>
    </div>
    <div class="tcal-body">
      <div class="tcal-content">
        <div class="cal-sel-bar">
          <button class="cal-sel-btn active" id="cal-start-btn">
            <div class="cal-sel-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><div class="cal-sel-lbl" id="cal-start-lbl">เลือกวันเริ่มต้น</div><div class="cal-sel-sub" id="cal-start-sub">วันที่เริ่มงาน</div></div>
          </button>
          <button class="cal-sel-btn" id="cal-end-btn">
            <div class="cal-sel-icon gray" id="cal-end-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><div class="cal-sel-lbl" id="cal-end-lbl">เลือกวันสิ้นสุด</div><div class="cal-sel-sub" id="cal-end-sub">วันที่สิ้นสุดงาน</div></div>
          </button>
        </div>
        <div class="cal-wrap">
          <div class="cal-months">
            <div class="cal-block">
              <div class="cal-mnav"><button class="cal-mnav-btn" id="cal-prev">&#8249;</button><div class="cal-mname" id="cal-left-name"></div><button class="cal-mnav-btn invisible">&#8250;</button></div>
              <div class="cal-dhdrs" id="cal-left-hdrs"></div>
              <div class="cal-dgrid" id="cal-left-grid"></div>
            </div>
            <div class="cal-block">
              <div class="cal-mnav"><button class="cal-mnav-btn invisible">&#8249;</button><div class="cal-mname" id="cal-right-name"></div><button class="cal-mnav-btn" id="cal-next">&#8250;</button></div>
              <div class="cal-dhdrs" id="cal-right-hdrs"></div>
              <div class="cal-dgrid" id="cal-right-grid"></div>
            </div>
          </div>
          <div class="cal-legend">
            <div class="cal-leg"><div class="cal-leg-dot" style="background:var(--or)"></div>มีงาน</div>
            <div class="cal-leg"><div class="cal-leg-dot" style="background:#ffe1cc"></div>ช่วงเลือก</div>
            <div class="cal-leg"><div class="cal-leg-dot" style="background:#e2e2e2;border:1.5px solid var(--or)"></div>วันนี้</div>
          </div>
          <div class="cal-footer">
            <div class="cal-footer-note" id="cal-footer-note">เลือกช่วงวันที่ต้องการดูงาน</div>
            <button class="btn btn-ghost btn-sm" id="cal-reset-btn" onclick="calReset()" style="display:none">ล้าง</button>
            <button class="btn btn-primary btn-sm" onclick="closeTeamCalendar();openAddSchedModal()">+ เพิ่มงาน</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="cal-popup-bg" id="cal-popup-bg" >
  <div class="cal-popup" onclick="event.stopPropagation()">
    <div class="cal-popup-strip"></div>
    <div class="cal-popup-head">
      <div class="cal-popup-date" id="cal-popup-date"></div>
      <div class="cal-popup-count" id="cal-popup-count" style="display:none"></div>
      <button class="cal-popup-close" onclick="document.getElementById('cal-popup-bg').classList.remove('open')">✕</button>
    </div>
    <div class="cal-popup-body"><div class="cal-popup-inner" id="cal-popup-inner"></div></div>
  </div>
</div>

{{--  Profile View  --}}
<div class="overlay" id="overlay" >
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div class="p-photo"><img id="m-img" src="" alt=""></div>
      <div>
        <div class="p-fullname" id="m-name"></div>
        <div class="p-engname" id="m-name-eng"></div>
        <div class="p-role-tag" id="m-position"></div>
      </div>
      <div class="p-close" onclick="this.closest('.overlay').classList.remove('open')">✕</div>
    </div>
    <div class="modal-body">
      <div class="igrid">
        <div><div class="ilabel">รหัสพนักงาน</div><div class="ival" id="m-empid"></div></div>
        <div><div class="ilabel">ชื่อเล่น</div><div class="ival" id="m-nickname"></div></div>
        <div><div class="ilabel">ทีม</div><div class="ival" id="m-team"></div></div>
        <div><div class="ilabel">เบอร์โทร</div><div class="ival" id="m-phone"></div></div>
        <div><div class="ilabel">วันเกิด</div><div class="ival" id="m-dob"></div></div>
        <div><div class="ilabel">สถานะ</div><div class="ival" id="m-status"></div></div>
      </div>
      <div class="profile-section"><div class="ilabel">ทักษะ</div><div class="sk-wrap" id="m-skills"></div></div>
      <div class="profile-section"><div class="ilabel">Core Competencies</div><div class="profile-comp-grid" id="m-competencies"></div></div>
      <div class="profile-section"><div class="ilabel">Licenses</div><div id="m-licenses"></div></div>
      <div class="profile-section"><div class="ilabel">Software &amp; Tools</div><div class="sk-wrap" id="m-software"></div></div>
    </div>
  </div>
</div>

{{--  เพิ่มช่าง  --}}
<div class="overlay" id="modal-tech" >
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header">
      <div class="modal-title">เพิ่มช่างใหม่</div>
      <div class="modal-close" onclick="closeModalById('modal-tech')">✕</div>
    </div>
    <div class="modal-body" style="padding:0">
      @if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && !old('so_number'))
        <div class="ferr" style="margin:16px 22px 0">{{ $errors->first() }}</div>
      @endif

      @php
        $oldSkills = old('emp_skill', []);
        if (is_string($oldSkills)) $oldSkills = array_filter(array_map('trim', explode(',', $oldSkills)));
        if (!is_array($oldSkills)) $oldSkills = [];
        $oldComp = old('core_competencies', []); if (!is_array($oldComp)) $oldComp = [];
        $oldSw = old('software_tools', []); if (!is_array($oldSw)) $oldSw = [];
      @endphp

      <form method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data" id="form-add-tech">
        @csrf
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative">
              <span class="resume-badge-abs">PHOTO</span>
              <div class="photo-box" onclick="document.getElementById('add-img-input').click()">
                <img id="add-img-preview" class="resume-img" src="" alt="">
                <div class="photo-overlay"><span>เปลี่ยนรูป</span></div>
                <div class="photo-placeholder" id="add-img-ph">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <span>อัปโหลดรูป</span>
                </div>
              </div>
            </div>
            <div class="photo-label">รูปประจำตัว</div>
            <input type="file" id="add-img-input" name="img" class="hidden-file" accept="image/*" onchange="resumePreview(this,'add')">
          </div>
          <div class="resume-fields">
            <div class="frow"><label class="flabel">รหัสพนักงาน *</label><input class="finput" type="text" name="emp_id" value="{{ old('emp_id') }}" required placeholder="3E-001"><div class="emp-id-note">ตัวอักษร, ตัวเลข, -, _</div></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (ไทย)</label><input class="finput" type="text" name="emp_name" value="{{ old('emp_name') }}"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (Eng)</label><input class="finput" type="text" name="emp_name_eng" value="{{ old('emp_name_eng') }}"></div>
            <div class="frow"><label class="flabel">ชื่อเล่น</label><input class="finput" type="text" name="emp_nickname" value="{{ old('emp_nickname') }}"></div>
            <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="emp_phone" value="{{ old('emp_phone') }}"></div>
            <div class="frow"><label class="flabel">วันเกิด</label><div class="dob-row"><input class="finput" type="date" name="date_of_birth" id="add-dob" value="{{ old('date_of_birth') }}" onchange="updateBE('add')"><span class="dob-be" id="add-dob-be">พ.ศ. -</span></div></div>
            <div class="frow"><label class="flabel">ตำแหน่ง</label>
              <select class="finput" name="emp_position" id="add-emp_position" onchange="handlePositionChange('add')">
                <option value="">-- เลือก --</option>
                <option value="ลูกทีม" {{ old('emp_position')==='ลูกทีม'?'selected':'' }}>ลูกทีม</option>
                <option value="หัวหน้าทีม" {{ old('emp_position')==='หัวหน้าทีม'?'selected':'' }}>หัวหน้าทีม</option>
              </select>
            </div>
            <div class="frow" id="add-team-wrap" style="{{ old('emp_position')==='หัวหน้าทีม'?'display:none':'' }}">
              <label class="flabel">ทีม</label>
              <select class="finput" name="emp_team" id="add-team-select">
                <option value="">-- เลือกทีม --</option>
                @foreach($availableTeams as $tn)
                  <option value="{{ $tn }}" {{ old('emp_team')===$tn?'selected':'' }}>{{ $tn }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div style="padding:12px 22px 0">
          <div id="add-head-info" style="{{ old('emp_position')==='หัวหน้าทีม'?'':'display:none' }}">
            <div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> อัตโนมัติ</div>
          </div>
        </div>

        <div style="padding:0 22px 20px">
          <div class="section-h">ทักษะ</div>
          <div class="skill-grid">
            @foreach($skillOptions as $sk)
              <label class="skill-check {{ in_array($sk,$oldSkills)?'checked':'' }}">
                <input type="checkbox" name="emp_skill[]" value="{{ $sk }}" {{ in_array($sk,$oldSkills)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">
                {{ $sk }}
              </label>
            @endforeach
          </div>

          <div class="section-h">Core Competencies</div>
          <div class="comp-grid">
            @foreach($competencyList as $c)
              @php $compKey = $c['key']; $compVal = $oldComp[$compKey] ?? 'none'; @endphp
              <div class="comp-card">
                <div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $compKey }}</span></div>
                <select class="comp-select lv-{{ $compVal }}" name="core_competencies[{{ $compKey }}]" onchange="updateCompClass(this)">
                  @foreach($competencyLevels as $lv => $lvL)
                    <option value="{{ $lv }}" {{ $compVal===$lv?'selected':'' }}>{{ $lvL }}</option>
                  @endforeach
                </select>
              </div>
            @endforeach
          </div>

          <div class="section-h">Licenses &amp; Experience</div>
          <div class="lic-list" id="add-lic-list"></div>
          <button type="button" class="btn-add-lic" onclick="addLicense('add')">+ เพิ่มใบรับรอง</button>

          <div class="section-h">Software &amp; Tools</div>
          <div class="sw-grid">
            @foreach($softwareOptions as $sw)
              <label class="skill-check {{ in_array($sw,$oldSw)?'checked':'' }}">
                <input type="checkbox" name="software_tools[]" value="{{ $sw }}" {{ in_array($sw,$oldSw)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">
                {{ $sw }}
              </label>
            @endforeach
          </div>
          <div class="sw-custom-row">
            <input type="text" class="finput" id="add-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('add')}">
            <button type="button" class="btn-other" onclick="addCustomSw('add')">+ เพิ่ม</button>
          </div>
          <div class="sw-custom-tags" id="add-sw-custom-tags">
            @foreach($oldSw as $sw)
              @if(!in_array($sw,$softwareOptions,true))
                <span class="sw-tag"><input type="hidden" name="software_tools[]" value="{{ $sw }}">{{ $sw }}<span class="x" onclick="this.parentElement.remove()">✕</span></span>
              @endif
            @endforeach
          </div>

          <div class="factions">
            <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-tech')">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{--  แก้ไขช่าง  --}}
<div class="overlay" id="modal-edit-tech" >
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">แก้ไขข้อมูลช่าง</div><div class="modal-close" onclick="closeModalById('modal-edit-tech')">✕</div></div>
    <div class="modal-body" style="padding:0">
      @if($errors->any() && old('_edit_tech'))<div class="ferr" style="margin:16px 22px 0">{{ $errors->first() }}</div>@endif
      <form method="POST" id="form-edit-tech" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_edit_tech" value="1">
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative"><span class="resume-badge-abs">PHOTO</span>
              <div class="photo-box" onclick="document.getElementById('et-img-input').click()">
                <img id="et-img-preview" class="resume-img" src="" alt=""><div class="photo-overlay"><span>เปลี่ยนรูป</span></div>
                <div class="photo-placeholder" id="et-img-ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span>คลิกเปลี่ยนรูป</span></div>
              </div>
            </div>
            <div class="photo-label">รูปประจำตัว</div>
            <input type="file" id="et-img-input" name="img" class="hidden-file" accept="image/*" onchange="resumePreview(this,'et')">
          </div>
          <div class="resume-fields">
            <div class="frow"><label class="flabel">รหัสพนักงาน</label><input class="finput" type="text" id="et-emp_id" readonly style="background:var(--lt);color:var(--md);cursor:not-allowed"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (ไทย)</label><input class="finput" type="text" name="emp_name" id="et-emp_name"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (Eng)</label><input class="finput" type="text" name="emp_name_eng" id="et-emp_name_eng"></div>
            <div class="frow"><label class="flabel">ชื่อเล่น</label><input class="finput" type="text" name="emp_nickname" id="et-emp_nickname"></div>
            <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="emp_phone" id="et-emp_phone"></div>
            <div class="frow"><label class="flabel">วันเกิด</label><div class="dob-row"><input class="finput" type="date" name="date_of_birth" id="et-dob" onchange="updateBE('et')"><span class="dob-be" id="et-dob-be">พ.ศ. -</span></div></div>
            <div class="frow"><label class="flabel">ตำแหน่ง</label><select class="finput" name="emp_position" id="et-emp_position" onchange="handlePositionChange('et')"><option value="">-- เลือก --</option><option value="ลูกทีม">ลูกทีม</option><option value="หัวหน้าทีม">หัวหน้าทีม</option></select></div>
            <div class="frow" id="et-team-wrap"><label class="flabel">ทีม</label><select class="finput" name="emp_team" id="et-team-select"><option value="">-- เลือกทีม --</option>@foreach($availableTeams as $tn)<option value="{{ $tn }}">{{ $tn }}</option>@endforeach</select></div>
            <div class="frow" style="grid-column:1/-1"><label class="flabel">สถานะ</label><select class="finput" name="status" id="et-status"><option value="active">พร้อมทำงาน</option><option value="leave">ลาออก</option></select></div>
          </div>
        </div>
        <div style="padding:12px 22px 0"><div id="et-head-info" style="display:none"><div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> อัตโนมัติ</div></div></div>
        <div style="padding:0 22px 20px">
          <div class="section-h">ทักษะ</div>
          <div class="skill-grid" id="et-skill-grid">@foreach($skillOptions as $sk)<label class="skill-check" data-skill="{{ $sk }}"><input type="checkbox" name="emp_skill[]" value="{{ $sk }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sk }}</label>@endforeach</div>
          <div class="section-h">Core Competencies</div>
          <div class="comp-grid" id="et-comp-grid">@foreach($competencyList as $c)<div class="comp-card"><div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div><select class="comp-select lv-none" data-comp="{{ $c['key'] }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">@foreach($competencyLevels as $lv=>$lvL)<option value="{{ $lv }}">{{ $lvL }}</option>@endforeach</select></div>@endforeach</div>
          <div class="section-h">Licenses &amp; Experience</div>
          <div class="lic-list" id="et-lic-list"></div>
          <button type="button" class="btn-add-lic" onclick="addLicense('et')">+ เพิ่มใบรับรอง</button>
          <div class="section-h">Software &amp; Tools</div>
          <div class="sw-grid" id="et-sw-grid">@foreach($softwareOptions as $sw)<label class="skill-check" data-sw="{{ $sw }}"><input type="checkbox" name="software_tools[]" value="{{ $sw }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sw }}</label>@endforeach</div>
          <div class="sw-custom-row"><input type="text" class="finput" id="et-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('et')}"><button type="button" class="btn-other" onclick="addCustomSw('et')">+ เพิ่ม</button></div>
          <div class="sw-custom-tags" id="et-sw-custom-tags"></div>
          <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-tech')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

{{--  เพิ่มงาน  --}}
<div class="overlay" id="modal-sched" >
  <div class="pmodal pmodal-wide"  onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">เพิ่มงานใหม่</div><div class="modal-close" onclick="closeModalById('modal-sched')">✕</div></div>
    <div class="modal-body">
      @if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))<div class="ferr">{{ $errors->first() }}</div>@endif
      <form method="POST" action="{{ route('sched.store') }}" id="form-add-sched">
        @csrf
        <input type="hidden" name="customer_id" id="add-customer_id" value="">
        <div class="sched-grid">
          <div class="frow"><label class="flabel">ประเภทงาน *</label><select class="finput" name="job_type" id="add-job_type" required><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}" {{ old('job_type')===$key?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-2025-001"></div>
          <div class="frow sched-full autocomp" style="position:relative">
            <label class="flabel">ชื่อลูกค้า * — พิมพ์เพื่อค้น หรือกรอกใหม่ = ลูกค้าใหม่</label>
            <input class="finput" type="text" name="customer_name" id="add-customer_name" value="{{ old('customer_name') }}" required autocomplete="off" placeholder="พิมพ์ชื่อลูกค้า..." oninput="custAutocomp(this.value,'add')" onkeydown="custAutocompKey(event,'add')">
            <div class="autocomp-list" id="add-ac-list"></div>
            <div class="cust-banner cust-banner-old" id="add-cust-banner"></div>
          </div>
          <div class="frow" id="add-ncf-1" style="display:none"><label class="flabel">รายละเอียดโครงการ</label><input class="finput" type="text" name="cust_desc" ></div>
          <div class="frow" id="add-ncf-2" style="display:none"><label class="flabel">ชื่อผู้ติดต่อ</label><input class="finput" type="text" name="cust_contact_name"></div>
          <div class="frow" id="add-ncf-3" style="display:none"><label class="flabel">เบอร์โทรลูกค้า</label><input class="finput" type="text" name="cust_phone"></div>
          <div class="frow" id="add-ncf-4" style="display:none"><label class="flabel">ขนาดติดตั้ง</label><input class="finput" type="text" name="cust_size" ></div>
          <div class="frow"><label class="flabel">ทีมที่รับผิดชอบ *</label><select class="finput" name="team_name" id="add-team_name" required onchange="TL.onTeamChange('add')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)<option value="{{ $t['team_name'] }}" {{ old('team_name')===$t['team_name']?'selected':'' }}>{{ $t['team_name'] }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required></div>
          <div class="frow"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="add-job_location" value="{{ old('job_location') }}"></div>
          <div class="frow"><label class="flabel">ละติจูด,ลองจิจูด</label><input class="finput" type="text" name="job_la_long" id="add-job_la_long" value="{{ old('job_la_long') }}"></div>
          <div class="frow sched-full">
            <label class="flabel">ช่วงวันที่ทำงาน * (กดและลากเพื่อเลือก)</label>
            <div class="tl-wrap" id="add-tl-wrap">
              <div class="tl-header"><button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="add">&#8249;</button><div class="tl-mname" id="add-tl-mname"></div><button type="button" class="tl-today-btn" onclick="TL.gotoToday('add')">วันนี้</button><button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="add">&#8250;</button></div>
              <div class="tl-team-info no-team" id="add-tl-team-info"><span> เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</span></div>
              <div class="tl-months">
                <div class="tl-month-block"><div class="tl-month-title" id="add-tl-mname-left"></div><div class="tl-grid-wrap"><div class="tl-dhdrs" id="add-tl-dhdrs-left"></div><div class="tl-grid" id="add-tl-grid-left"></div></div></div>
                <div class="tl-month-block"><div class="tl-month-title" id="add-tl-mname-right"></div><div class="tl-grid-wrap"><div class="tl-dhdrs" id="add-tl-dhdrs-right"></div><div class="tl-grid" id="add-tl-grid-right"></div></div></div>
              </div>
              <div class="tl-summary"><div class="tl-summary-info" id="add-tl-summary">กรุณาเลือกช่วงวันที่</div><button type="button" class="tl-clear-btn" onclick="TL.clear('add')">ล้าง</button></div>
              <div class="tl-legend"><div class="tl-leg"><div class="tl-leg-box today"></div>วันนี้</div><div class="tl-leg"><div class="tl-leg-box busy"></div>ทีมมีงาน</div><div class="tl-leg"><div class="tl-leg-box sel"></div>เลือก</div><div class="tl-leg"><div class="tl-leg-box range"></div>ช่วงเลือก</div></div>
            </div>
          </div>
          <div class="frow sched-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" rows="3" style="resize:vertical">{{ old('note') }}</textarea></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-sched')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกงาน</button></div>
      </form>
    </div>
  </div>
</div>

{{--  แก้ไขงาน  --}}
<div class="overlay" id="modal-edit-sched" onclick="if(event.target===this)closeModalById('modal-edit-sched')">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">แก้ไขงาน</div><div class="modal-close" onclick="closeModalById('modal-edit-sched')"></div></div>
    <div class="modal-body">
      @if($errors->any() && old('_edit_sched'))<div class="ferr">{{ $errors->first() }}</div>@endif
      <form method="POST" id="form-edit-sched" action="">
        @csrf
        <input type="hidden" name="_edit_sched" value="1">
        <div class="sched-grid">
          <div class="frow"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" id="es-so_number" required></div>
          <div class="frow"><label class="flabel">ชื่อลูกค้า *</label><input class="finput" type="text" name="customer_name" id="es-customer_name" required></div>
          <div class="frow"><label class="flabel">ประเภทงาน</label><select class="finput" name="job_type" id="es-job_type"><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" id="es-job_title" required></div>
          <div class="frow"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="es-job_location"></div>
          <div class="frow"><label class="flabel">ละติจูด,ลองจิจูด</label><input class="finput" type="text" name="job_la_long" id="es-job_la_long"></div>
          <div class="frow"><label class="flabel">ทีม *</label><select class="finput" name="team_name" id="es-team_name" required onchange="TL.onTeamChange('es')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)<option value="{{ $t['team_name'] }}">{{ $t['team_name'] }}</option>@endforeach</select></div>
          <div class="frow"></div>
          <div class="frow sched-full">
            <label class="flabel">ช่วงวันที่ทำงาน *</label>
            <div class="tl-wrap" id="es-tl-wrap">
              <div class="tl-header"><button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="es">&#8249;</button><div class="tl-mname" id="es-tl-mname"></div><button type="button" class="tl-today-btn" onclick="TL.gotoToday('es')">วันนี้</button><button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="es">&#8250;</button></div>
              <div class="tl-team-info no-team" id="es-tl-team-info"><span> เลือกทีมก่อน</span></div>
              <div class="tl-months">
                <div class="tl-month-block"><div class="tl-month-title" id="es-tl-mname-left"></div><div class="tl-grid-wrap"><div class="tl-dhdrs" id="es-tl-dhdrs-left"></div><div class="tl-grid" id="es-tl-grid-left"></div></div></div>
                <div class="tl-month-block"><div class="tl-month-title" id="es-tl-mname-right"></div><div class="tl-grid-wrap"><div class="tl-dhdrs" id="es-tl-dhdrs-right"></div><div class="tl-grid" id="es-tl-grid-right"></div></div></div>
              </div>
              <div class="tl-summary"><div class="tl-summary-info" id="es-tl-summary">กรุณาเลือกช่วงวันที่</div><button type="button" class="tl-clear-btn" onclick="TL.clear('es')">ล้าง</button></div>
              <div class="tl-legend"><div class="tl-leg"><div class="tl-leg-box today"></div>วันนี้</div><div class="tl-leg"><div class="tl-leg-box busy"></div>ทีมมีงาน</div><div class="tl-leg"><div class="tl-leg-box sel"></div>เลือก</div><div class="tl-leg"><div class="tl-leg-box range"></div>ช่วงเลือก</div></div>
            </div>
          </div>
          <div class="frow sched-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="es-note" rows="3" style="resize:vertical"></textarea></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button></div>
      </form>
    </div>
  </div>
</div>

{{--  เพิ่ม/แก้ไขลูกค้า  --}}
<div class="overlay" id="modal-cust" onclick="if(event.target===this)closeModalById('modal-cust')">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip" id="cust-modal-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header">
      <div>
        <div class="modal-title" id="cust-modal-title">เพิ่มลูกค้าใหม่</div>
        <div class="modal-subtitle" id="cust-modal-sub"></div>
      </div>
      <div class="modal-close" onclick="closeModalById('modal-cust')"></div>
    </div>
    <div class="modal-body">
      <form method="POST" id="form-cust" action="{{ route('cust.store') }}">
        @csrf
        <div class="fgrid">
          <div class="frow">
            <label class="flabel">ประเภทงาน *</label>
            <select class="finput" name="type_project" id="cf-type_project" onchange="onCustTypeChange(this.value)">
              <option value="">-- เลือกประเภทงาน --</option>
              @foreach($jobTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select>
          </div>
          <div class="frow">
            <label class="flabel">สถานะ</label>
            <select class="finput" name="status" id="cf-status"><option value="เสนอ">เสนอ</option></select>
          </div>
          <div class="frow fcol-full"><label class="flabel">ชื่อลูกค้า / สถานที่ *</label><input class="finput" type="text" name="name" id="cf-name" required></div>
          <div class="frow"><label class="flabel">รายละเอียด</label><input class="finput" type="text" name="desc" id="cf-desc" placeholder="โรงงาน / หมู่บ้าน"></div>
          <div class="frow"><label class="flabel">ผู้ติดต่อ</label><input class="finput" type="text" name="contact_name" id="cf-contact_name"></div>
          <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="phone" id="cf-phone"></div>
          <div class="frow"><label class="flabel" id="cf-size-lbl">ขนาดติดตั้ง</label><input class="finput" type="text" name="size" id="cf-size" placeholder="เช่น 30kW"></div>
          <div class="frow"><label class="flabel">ราคา (บาท รวม VAT)</label><input class="finput" type="number" step="0.01" name="price" id="cf-price"></div>
          <div class="frow"><label class="flabel">พิกัด (Lat, Long)</label><input class="finput" type="text" name="loc" id="cf-loc" placeholder="13.7563, 100.5018"></div>

          {{-- supervisor = finish_date (reuse column — no migration) --}}
          <div class="frow" id="cf-finish-wrap">
            <label class="flabel" id="cf-finish-lbl">วันสิ้นสุด / ติดตั้งเสร็จ</label>
            <input class="finput" type="date" name="supervisor" id="cf-finish_date">
          </div>

          <div class="frow" id="cf-wash-wrap" style="display:none">
            <label class="flabel">รอบล้างแผง</label>
            <select class="finput" name="wash_cycle" id="cf-wash_cycle"><option value="6">6 เดือน</option><option value="12">12 เดือน</option></select>
          </div>

          <div class="frow fcol-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="notes" id="cf-notes" rows="3" style="resize:vertical"></textarea></div>
        </div>

        <div class="finfo-box" id="cf-solar-info" style="display:none">
           เมื่อสถานะ <strong>"ติดตั้งสำเร็จ"</strong> ระบบจะคำนวณวันล้างแผงครั้งแรก = วันติดตั้งเสร็จ + รอบล้างแผง อัตโนมัติ
        </div>

        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust')">ยกเลิก</button>
          <button type="submit" class="btn btn-solar">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{--  รายละเอียดลูกค้า  --}}
<div class="overlay" id="modal-cust-detail" data-no-backdrop-close>
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip" id="cd-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header">
      <div>
        <div class="modal-title" id="cd-name">รายละเอียดลูกค้า</div>
        <div style="margin-top:4px" id="cd-type-tag"></div>
      </div>
      <div class="modal-close" onclick="closeModalById('modal-cust-detail')"></div>
    </div>
    <div class="modal-body">
      {{-- Tabs --}}
      <div class="dtab-bar">
        <button class="dtab active" id="dtab-btn-info" onclick="switchDTab('info',this)"> ข้อมูล</button>
        <button class="dtab" id="dtab-btn-wash" onclick="switchDTab('wash',this)" style="display:none"> ล้างแผง</button>
        <button class="dtab" id="dtab-btn-milestone" onclick="switchDTab('milestone',this)" style="display:none"> Timeline</button>
        <button class="dtab" id="dtab-btn-sched" onclick="switchDTab('sched',this)"> งานที่เกี่ยวข้อง</button>
      </div>

      {{-- Tab: Info --}}
      <div class="dtab-panel active" id="dtab-info">
        <div id="cd-wash-countdown" style="display:none"></div>
        <div class="pinfo-grid">
          <div class="pinfo-card"><div class="pinfo-label">รายละเอียด</div><div class="pinfo-val" id="cd-desc">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label">สถานะ</div><div class="pinfo-val" id="cd-status">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label">ผู้ติดต่อ</div><div class="pinfo-val" id="cd-contact">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label">เบอร์โทร</div><div class="pinfo-val" id="cd-phone">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label" id="cd-size-lbl">ขนาด</div><div class="pinfo-val" id="cd-size">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label">ราคา</div><div class="pinfo-val" id="cd-price">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label">พิกัด</div><div class="pinfo-val" id="cd-loc">-</div></div>
          <div class="pinfo-card"><div class="pinfo-label" id="cd-finish-lbl">วันสิ้นสุด</div><div class="pinfo-val" id="cd-finish_date">-</div></div>
        </div>
        <div class="pinfo-card" style="margin-bottom:10px"><div class="pinfo-label">หมายเหตุ</div><div class="pinfo-val" id="cd-notes" style="font-weight:500;white-space:pre-wrap;line-height:1.6">-</div></div>
      </div>

      {{-- Tab: Wash --}}
      <div class="dtab-panel" id="dtab-wash">
        <div style="margin-bottom:10px;font-size:13px;color:var(--md)">ประวัติการล้างแผง <span id="cd-wash-count" style="font-weight:700;color:var(--dk)"></span></div>
        <div id="cd-wash-body"></div>
        <button type="button" class="btn-add-lic" onclick="openAddWashModal()" style="background:#e0e7ff;border-color:#a5b4fc;color:#3730a3;margin-top:12px">+ เพิ่มประวัติการล้างแผง</button>
      </div>

      {{-- Tab: Milestone --}}
      <div class="dtab-panel" id="dtab-milestone">
        <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
          <span style="font-size:13px;font-weight:600;color:var(--md)">Timeline ความคืบหน้าโครงการ</span>
          <button type="button" class="btn btn-primary btn-sm" onclick="openAddMilestoneModal()">+ เพิ่ม milestone</button>
        </div>
        <div style="position:relative;padding-left:24px" id="cd-milestone-body">
          <div style="text-align:center;color:var(--md);padding:24px;font-size:13px">ยังไม่มี milestone</div>
        </div>
      </div>

      {{-- Tab: Schedules --}}
      <div class="dtab-panel" id="dtab-sched">
        <div id="cd-schedules" style="font-size:13px;color:var(--md)">ยังไม่มีงานที่ผูกกับลูกค้านี้</div>
      </div>

      <div class="factions">
        <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust-detail')">ปิด</button>
        <button type="button" class="btn btn-solar" onclick="editFromDetail()"> แก้ไข</button>
      </div>
    </div>
  </div>
</div>

{{--  เพิ่มการล้างแผง  --}}
<div class="overlay" id="modal-add-wash" onclick="if(event.target===this)closeModalById('modal-add-wash')">
  <div class="pmodal pmodal-sm" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),#0891b2,#06b6d4)"></div>
    <div class="modal-header"><div class="modal-title">เพิ่มประวัติการล้างแผง</div><div class="modal-close" onclick="closeModalById('modal-add-wash')"></div></div>
    <div class="modal-body">
      <form method="POST" id="form-add-wash" action="">
        @csrf
        <div class="frow"><label class="flabel">วันที่ล้าง *</label><input class="finput" type="date" name="wash_date" id="aw-date" required></div>
        <div class="frow"><label class="flabel">ทีม / ช่างที่ล้าง *</label>
          <select class="finput" name="tech" id="aw-tech" required>
            <option value="">-- เลือก --</option>
            @foreach($teams as $t)<option value="{{ $t['team_name'] }}">{{ $t['team_name'] }}</option>@endforeach
            <option value="ช่างภายนอก">ช่างภายนอก</option>
            <option value="อื่นๆ">อื่นๆ</option>
          </select>
        </div>
        <div class="frow"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="aw-note" rows="2"></textarea></div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-wash')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>

{{--  เพิ่ม Milestone  --}}
<div class="overlay" id="modal-add-milestone" onclick="if(event.target===this)closeModalById('modal-add-milestone')">
  <div class="pmodal pmodal-sm" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,#7c3aed,#a855f7,#c084fc)"></div>
    <div class="modal-header"><div class="modal-title">เพิ่ม Milestone</div><div class="modal-close" onclick="closeModalById('modal-add-milestone')"></div></div>
    <div class="modal-body">
      <form method="POST" id="form-add-milestone" action="">
        @csrf
        <div class="frow"><label class="flabel">วันที่ *</label><input class="finput" type="date" name="milestone_date" id="am-date" required></div>
        <div class="frow"><label class="flabel">รายละเอียด *</label><textarea class="finput" name="milestone_note" id="am-note" rows="3" required placeholder="เช่น เทรากฐาน, ทดสอบระบบ, ส่งมอบงาน..."></textarea></div>
        <div class="frow"><label class="flabel">บันทึกโดย</label><input class="finput" type="text" name="milestone_by" id="am-by" placeholder="ชื่อผู้บันทึก"></div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-milestone')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>

{{--  เพิ่ม/แก้ไขบัญชี Solar  --}}
<div class="overlay" id="modal-account" onclick="if(event.target===this)closeModalById('modal-account')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header"><div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้ Solar</div><div class="modal-close" onclick="closeModalById('modal-account')"></div></div>
    <div class="modal-body">
      <form method="POST" id="form-account" action="{{ route('account.store') }}">
        @csrf
        <div class="fgrid">
          <div class="frow"><label class="flabel">เลขที่ / รหัส</label><input class="finput" type="text" name="no" id="af-no" readonly></div>
          <div class="frow"><label class="flabel">Inverter / ยี่ห้อ</label><input class="finput" type="text" name="inverter" id="af-inverter" placeholder="Huawei, Solis, Growatt..."></div>
          <div class="frow fcol-full"><label class="flabel">ชื่อระบบ / Platform *</label><input class="finput" type="text" name="plane" id="af-plane" placeholder="FusionSolar, SolarmanPV..." required></div>
          <div class="frow fcol-full">
            <label class="flabel">ลูกค้า / สถานที่ติดตั้ง</label>
            <div class="autocomp" style="position:relative">
              <input class="finput" type="text" name="customer" id="af-customer" autocomplete="off" oninput="accCustAutocomp(this.value)">
              <div class="autocomp-list" id="af-cust-list"></div>
            </div>
          </div>
          <div class="frow"><label class="flabel">Username</label><input class="finput" type="text" name="username" id="af-username" autocomplete="off"></div>
          <div class="frow"><label class="flabel">Password</label><div style="position:relative"><input class="finput" type="password" name="password" id="af-password" autocomplete="new-password" style="padding-right:44px"><button type="button" onclick="toggleInputPw('af-password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--md)"></button></div></div>
          <div class="frow"><label class="flabel">Email</label><input class="finput" type="text" name="email" id="af-email"></div>
          <div class="frow"><label class="flabel">App Password</label><div style="position:relative"><input class="finput" type="password" name="app_password" id="af-app_password" autocomplete="new-password" style="padding-right:44px" placeholder="สำหรับ 2FA"><button type="button" onclick="toggleInputPw('af-app_password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--md)"></button></div></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-account')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>
<script>
    const URL_TECH_STORE      = "{{ route('tech.store') }}";
    const URL_TECH_UPDATE     = (id) => `/technicians/${id}/update`;
    const URL_SCHED_STORE     = "{{ route('sched.store') }}";
    const URL_SCHED_UPDATE    = (id) => `/schedules/${id}/update`;
    const URL_CUST_STORE      = "{{ route('cust.store') }}";
    const URL_CUST_UPDATE     = (id) => `/customers/${id}/update`;
    const URL_WASH_STORE      = (id) => `/customers/${id}/wash/store`;
    const URL_WASH_DEL        = (id, num) => `/customers/${id}/wash/${num}/delete`;
    const URL_MILESTONE_STORE = (id) => `/customers/${id}/milestone/store`;
    const URL_MILESTONE_DEL   = (id, idx) => `/customers/${id}/milestone/${idx}/delete`;
    const URL_ACC_STORE       = "{{ route('account.store') }}";
    const URL_ACC_UPDATE      = (id) => `/solar-accounts/${id}/update`;
    const CSRF                = "{{ csrf_token() }}";
    
    // ─── Server Data ──────────────────────────────────────────────────
    const TECH_DATA  = @json($technicians, JSON_UNESCAPED_UNICODE);
    const SCHED_DATA = @json($schedules,   JSON_UNESCAPED_UNICODE);
    const CUST_DATA  = @json($customers,   JSON_UNESCAPED_UNICODE);
    const JOB_TYPES  = @json($jobTypes,    JSON_UNESCAPED_UNICODE);
    
    // ─── Utilities ────────────────────────────────────────────────────
    function escHtml(s) {
      return String(s ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function fmtDate(d) {
      if (!d) return '-';
      const dt = new Date(d);
      if (isNaN(dt)) return d;
      return `${dt.getDate()}/${dt.getMonth()+1}/${dt.getFullYear()+543}`;
    }
    function ymd(d) {
      const dt = (d instanceof Date) ? d : new Date(d);
      return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
    }
    function daysBetween(a, b) {
      return Math.round((new Date(b) - new Date(a)) / 86400000);
    }
    
    // ─── Tab Navigation ───────────────────────────────────────────────
    function switchTab(tab, el) {
      document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
      const panel = document.getElementById('panel-' + tab);
      if (panel) panel.classList.add('active');
      if (el) el.classList.add('active');
    }
    
    // ─── Modal Helpers ────────────────────────────────────────────────
    function openModal(id) {
      const m = document.getElementById(id);
      if (!m) return;
      m.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function closeModalById(id) {
      const m = document.getElementById(id);
      if (!m) return;
      m.classList.remove('open');
      document.body.style.overflow = '';
    }
    // ─── Generic Search ───────────────────────────────────────────────
    function filterTable(tbodyId, q) {
      const rows = document.querySelectorAll('#' + tbodyId + ' tr[data-search]');
      const kw = (q || '').toLowerCase().trim();
      rows.forEach(r => {
        r.style.display = (!kw || r.dataset.search.includes(kw)) ? '' : 'none';
      });
    }
    function filterTeams(q) {
      const kw = (q || '').toLowerCase().trim();
      document.querySelectorAll('#team-grid-wrap .team-card').forEach(card => {
        const members = card.querySelectorAll('.member');
        let teamMatch = false;
        members.forEach(m => {
          const txt = (m.textContent || '').toLowerCase();
          const ok = !kw || txt.includes(kw);
          if (ok) teamMatch = true;
        });
        card.style.display = teamMatch ? '' : 'none';
      });
    }
    
    // ═══════════════════════════════════════════════════════════════
    //  TIMELINE DATE-RANGE PICKER (TL)
    // ═══════════════════════════════════════════════════════════════
    const TL = (function () {
      const state = {}; // { add: {...}, es: {...} }
    
      function init(prefix) {
        if (state[prefix]) return state[prefix];
        const today = new Date();
        state[prefix] = {
          year:  today.getFullYear(),
          month: today.getMonth(),
          start: null,
          end:   null,
          team:  '',
          busyDays: {},      
          isDragging: false,
        };
        return state[prefix];
      }
    
      function getTeamSchedules(teamName) {
        if (!teamName) return [];
        return SCHED_DATA.filter(s => s.team_name === teamName);
      }
    
      function buildBusyDays(prefix, excludeId) {
        const st = state[prefix];
        st.busyDays = {};
        const scheds = getTeamSchedules(st.team);
        scheds.forEach(s => {
          if (excludeId && String(s.id) === String(excludeId)) return;
          let d = new Date(s.start_date);
          const end = new Date(s.end_date);
          while (d <= end) {
            const k = ymd(d);
            st.busyDays[k] = (st.busyDays[k] || 0) + 1;
            d.setDate(d.getDate() + 1);
          }
        });
      }
    
      function onTeamChange(prefix) {
        const st = init(prefix);
        const sel = document.getElementById(prefix === 'add' ? 'add-team_name' : 'es-team_name');
        st.team = sel ? sel.value : '';
    
        const info = document.getElementById(prefix + '-tl-team-info');
        if (info) {
          if (st.team) {
            info.classList.remove('no-team');
            const count = getTeamSchedules(st.team).length;
            info.innerHTML = `<span>ทีม <strong>${escHtml(st.team)}</strong> มีงาน ${count} งาน</span>`;
          } else {
            info.classList.add('no-team');
            info.innerHTML = '<span>เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</span>';
          }
        }
    
        buildBusyDays(prefix, prefix === 'es' ? state.es?.editingId : null);
        render(prefix);
      }
    
      function gotoToday(prefix) {
        const st = init(prefix);
        const t = new Date();
        st.year = t.getFullYear();
        st.month = t.getMonth();
        render(prefix);
      }
    
      function clear(prefix) {
        const st = init(prefix);
        st.start = null;
        st.end = null;
        syncHidden(prefix);
        render(prefix);
      }
    
      function nav(prefix, dir) {
        const st = init(prefix);
        st.month += dir;
        if (st.month < 0)  { st.month = 11; st.year--; }
        if (st.month > 11) { st.month = 0;  st.year++; }
        render(prefix);
      }
    
      function selectDate(prefix, dateStr) {
        const st = init(prefix);
        if (!st.start || (st.start && st.end)) {
          st.start = dateStr;
          st.end = null;
        } else {
          if (dateStr < st.start) {
            st.end = st.start;
            st.start = dateStr;
          } else {
            st.end = dateStr;
          }
        }
        syncHidden(prefix);
        render(prefix);
      }
    
      function startDrag(prefix, dateStr) {
        const st = init(prefix);
        st.isDragging = true;
        st.start = dateStr;
        st.end = dateStr;
        syncHidden(prefix);
        render(prefix);
      }
    
      function dragOver(prefix, dateStr) {
        const st = init(prefix);
        if (!st.isDragging) return;
        if (dateStr < st.start) {
          st.end = st.start;
          st.start = dateStr;
        } else {
          st.end = dateStr;
        }
        render(prefix);
      }
    
      function endDrag(prefix) {
        const st = init(prefix);
        st.isDragging = false;
        syncHidden(prefix);
      }
    
      function syncHidden(prefix) {
        const st = state[prefix];
        if (!st) return;
        const formId = prefix === 'add' ? 'form-add-sched' : 'form-edit-sched';
        const form = document.getElementById(formId);
        if (!form) return;
    
        let sInp = form.querySelector('input[name="start_date"]');
        let eInp = form.querySelector('input[name="end_date"]');
        if (!sInp) {
          sInp = document.createElement('input');
          sInp.type = 'hidden'; sInp.name = 'start_date';
          form.appendChild(sInp);
        }
        if (!eInp) {
          eInp = document.createElement('input');
          eInp.type = 'hidden'; eInp.name = 'end_date';
          form.appendChild(eInp);
        }
        sInp.value = st.start || '';
        eInp.value = st.end || st.start || '';
      }
    
      function render(prefix) {
        const st = init(prefix);
        const monthsTH = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                          'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
        const dayHdrs = ['อา','จ','อ','พ','พฤ','ศ','ส'];
    
        // header แสดงเดือน
        const mname = document.getElementById(prefix + '-tl-mname');
        if (mname) mname.textContent = `${monthsTH[st.month]} ${st.year + 543}`;
    
        // 2 เดือน left/right
        const renderMonth = (yr, mo, sideId) => {
          const titleEl = document.getElementById(`${prefix}-tl-mname-${sideId}`);
          if (titleEl) titleEl.textContent = `${monthsTH[mo]} ${yr + 543}`;
          const hdrsEl = document.getElementById(`${prefix}-tl-dhdrs-${sideId}`);
          if (hdrsEl) {
            hdrsEl.innerHTML = dayHdrs.map((d, i) =>
              `<div class="tl-dhdr ${i===0||i===6 ? 'weekend' : ''}">${d}</div>`
            ).join('');
          }
          const gridEl = document.getElementById(`${prefix}-tl-grid-${sideId}`);
          if (!gridEl) return;
    
          const firstDay = new Date(yr, mo, 1).getDay();
          const daysInMo = new Date(yr, mo+1, 0).getDate();
          const todayStr = ymd(new Date());
    
          let html = '';
          // padding ก่อนวันที่ 1
          for (let i = 0; i < firstDay; i++) {
            html += '<div class="tl-cell tl-other"></div>';
          }
          for (let d = 1; d <= daysInMo; d++) {
            const dStr = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const cls = ['tl-cell'];
            if (dStr === todayStr) cls.push('tl-today');
            const busyCount = st.busyDays[dStr] || 0;
            if (busyCount > 0) cls.push('tl-busy');
            if (st.start && dStr === st.start) cls.push('tl-sel-s');
            if (st.end && dStr === st.end) cls.push('tl-sel-e');
            if (st.start && st.end && dStr > st.start && dStr < st.end) cls.push('tl-in-range');
    
            html += `<div class="${cls.join(' ')}" data-date="${dStr}"
            ${busyCount > 0
              ? '' // ← busy: ไม่ใส่ event ใดๆ
              : `onmousedown="TL.startDrag('${prefix}','${dStr}');event.preventDefault()"
                onmouseenter="TL.dragOver('${prefix}','${dStr}')"
                onclick="TL.selectDate('${prefix}','${dStr}')"`
            }>
            <div class="tl-d">${d}</div>
            ${busyCount > 0 ? `<div class="tl-busy-bar"></div><div class="tl-jobs-count">${busyCount}</div>` : ''}
          </div>`;
          }
          gridEl.innerHTML = html;
        };
    
        // left = current month, right = next month
        const nextMo = st.month === 11 ? 0 : st.month + 1;
        const nextYr = st.month === 11 ? st.year + 1 : st.year;
        renderMonth(st.year, st.month, 'left');
        renderMonth(nextYr, nextMo, 'right');
    
        // summary
        const summary = document.getElementById(prefix + '-tl-summary');
        if (summary) {
          if (st.start && st.end) {
            const d = daysBetween(st.start, st.end) + 1;
            // ตรวจสอบทับซ้อนกับงานเก่า
            let conflict = 0;
            let cur = new Date(st.start);
            const endD = new Date(st.end);
            while (cur <= endD) {
              if (st.busyDays[ymd(cur)]) conflict++;
              cur.setDate(cur.getDate() + 1);
            }
            let html = `เลือก: <strong>${fmtDate(st.start)}</strong> ถึง <strong>${fmtDate(st.end)}</strong> (${d} วัน)`;
            if (conflict > 0) {
              html += ` <span class="tl-summary-warn">ทับซ้อน ${conflict} วัน</span>`;
            }
            summary.innerHTML = html;
          } else if (st.start) {
            summary.innerHTML = `เริ่ม: <strong>${fmtDate(st.start)}</strong> — เลือกวันสิ้นสุด`;
          } else {
            summary.textContent = 'กรุณาเลือกช่วงวันที่ (คลิกหรือลาก)';
          }
        }
      }
    
      // กลับสำหรับ edit-mode: ตั้งค่าเริ่มต้นจาก schedule เดิม
      function setRange(prefix, startDate, endDate, teamName, editingId) {
        const st = init(prefix);
        st.team = teamName || '';
        st.start = startDate || null;
        st.end = endDate || null;
        st.editingId = editingId;
        if (startDate) {
          const d = new Date(startDate);
          st.year = d.getFullYear();
          st.month = d.getMonth();
        }
        buildBusyDays(prefix, editingId);
        syncHidden(prefix);
        render(prefix);
    
        // อัปเดต team-info banner
        const info = document.getElementById(prefix + '-tl-team-info');
        if (info && teamName) {
          info.classList.remove('no-team');
          const count = getTeamSchedules(teamName).length;
          info.innerHTML = `<span>ทีม <strong>${escHtml(teamName)}</strong> มีงาน ${count} งาน</span>`;
        }
      }
    
      return {
        init, onTeamChange, gotoToday, clear, nav,
        selectDate, startDrag, dragOver, endDrag,
        setRange, render, _state: state,
      };
    })();
    
    // global mouseup สำหรับ drag
    document.addEventListener('mouseup', () => {
      TL.endDrag('add');
      TL.endDrag('es');
    });
    
    // ═══════════════════════════════════════════════════════════════
    //  TECHNICIAN: PROFILE / ADD / EDIT
    // ═══════════════════════════════════════════════════════════════
    function openProfileFromEl(el) {
      if (!el || !el.dataset.tech) return;
      let t;
      try { t = JSON.parse(el.dataset.tech); } catch (e) { return; }
      openProfileModal(t);
    }
    
    function openProfileModal(t) {
      const overlay = document.getElementById('overlay');
      if (!overlay || !t) return;
    
      const sv = (id, v) => {
        const el = document.getElementById(id);
        if (el) el.textContent = (v == null || v === '') ? '-' : v;
      };
    
      // photo
      const imgEl = document.getElementById('m-img');
      if (imgEl) {
        if (t.img) { imgEl.src = `/storage/${t.img}`; imgEl.style.display = ''; }
        else       { imgEl.src = ''; imgEl.style.display = 'none'; }
      }
    
      sv('m-name', t.emp_name || t.emp_id);
      sv('m-name-eng', t.emp_name_eng);
      const posEl = document.getElementById('m-position');
      if (posEl) posEl.textContent = `${t.emp_position || 'ลูกทีม'}${t.emp_team ? ' · ' + t.emp_team : ''}`;
    
      sv('m-empid', t.emp_id);
      sv('m-nickname', t.emp_nickname);
      sv('m-team', t.emp_team);
      sv('m-phone', t.emp_phone);
      sv('m-dob', t.date_of_birth ? fmtDate(t.date_of_birth) : '-');
    
      const statusMap = { active: 'active', leave: 'ลาออก' };
      sv('m-status', statusMap[t.status] || t.status || '-');
    
      // skills
      const skillsEl = document.getElementById('m-skills');
      if (skillsEl) {
        const skills = (t.emp_skill || '').split(',').map(s => s.trim()).filter(Boolean);
        skillsEl.innerHTML = skills.length
          ? skills.map(s => `<span class="sk">${escHtml(s)}</span>`).join('')
          : '<span style="color:var(--md);font-size:13px">-</span>';
      }
    
      // competencies
      const compEl = document.getElementById('m-competencies');
      if (compEl) {
        const comps = t.core_competencies || {};
        const lvLbl = { none:'ไม่มี', basic:'พื้นฐาน', skill:'ชำนาญ', expert:'เชี่ยวชาญ' };
        const entries = Object.entries(comps).filter(([_,v]) => v && v !== 'none');
        compEl.innerHTML = entries.length
          ? entries.map(([k,v]) => `
              <div class="pc-card lv-${v}">
                <span class="pc-lbl">${escHtml(k)}</span>
                <span class="pc-val">${lvLbl[v] || v}</span>
              </div>`).join('')
          : '<span style="color:var(--md);font-size:13px">-</span>';
      }
    
      // licenses
      const licEl = document.getElementById('m-licenses');
      if (licEl) {
        const lics = t.licenses || [];
        licEl.innerHTML = lics.length
          ? lics.map(l => `
              <div class="profile-lic-item">
                <div class="profile-lic-title">${escHtml(l.title || '-')}</div>
                <div class="profile-lic-meta">
                  ${l.doc_no ? 'เลขที่: ' + escHtml(l.doc_no) : ''}
                  ${l.date_issued ? ' · ออก: ' + escHtml(l.date_issued) : ''}
                  ${l.file ? ` · <a href="/storage/${escHtml(l.file)}" target="_blank" style="color:var(--bl);font-weight:700">📎 เปิดไฟล์</a>` : ''}
                </div>
              </div>`).join('')
          : '<span style="color:var(--md);font-size:13px">-</span>';
      }
    
      // software
      const swEl = document.getElementById('m-software');
      if (swEl) {
        const sw = t.software_tools || [];
        swEl.innerHTML = sw.length
          ? sw.map(s => `<span class="sk">${escHtml(s)}</span>`).join('')
          : '<span style="color:var(--md);font-size:13px">-</span>';
      }
    
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
      document.getElementById('add-customer_name')?.addEventListener('blur', () => {
      setTimeout(() => {
          document.getElementById('add-ac-list')?.classList.remove('open');
      }, 200); // delay เล็กน้อยให้ click ac-item ทำงานก่อน
  });
    // ─── Add/Edit Tech Helpers ────────────────────────────────────────
    function updateBE(prefix) {
      const inp = document.getElementById(prefix + '-dob');
      const lbl = document.getElementById(prefix + '-dob-be');
      if (!inp || !lbl) return;
      if (inp.value) {
        const d = new Date(inp.value);
        if (!isNaN(d)) {
          lbl.textContent = `พ.ศ. ${d.getFullYear() + 543}`;
          return;
        }
      }
      lbl.textContent = 'พ.ศ. -';
    }
    
    function handlePositionChange(prefix) {
      const sel = document.getElementById(prefix + '-emp_position');
      const teamWrap = document.getElementById(prefix + '-team-wrap');
      const headInfo = document.getElementById(prefix + '-head-info');
      if (!sel) return;
      const isHead = sel.value === 'หัวหน้าทีม';
      if (teamWrap) teamWrap.style.display = isHead ? 'none' : '';
      if (headInfo) headInfo.style.display = isHead ? '' : 'none';
    }
    
    function updateCompClass(sel) {
      sel.className = 'comp-select lv-' + sel.value;
    }
    
    function resumePreview(input, prefix) {
      const file = input.files && input.files[0];
      const img = document.getElementById(prefix + '-img-preview');
      const ph  = document.getElementById(prefix + '-img-ph');
      if (!file || !img) return;
      const reader = new FileReader();
      reader.onload = e => {
        img.src = e.target.result;
        img.style.display = 'block';
        if (ph) ph.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
    
    // License rows
    let _licIdx = { add: 0, et: 0 };
    
    function addLicense(prefix, lic = null) {
      const i = _licIdx[prefix]++;
      const list = document.getElementById(prefix + '-lic-list');
      if (!list) return;
      const row = document.createElement('div');
      row.className = 'lic-item';
      row.innerHTML = `
        <div class="lic-item-head">
          <span class="lic-num">#${i + 1}</span>
          <button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button>
        </div>
        <div class="lic-grid">
          <input class="finput" name="licenses[${i}][title]" placeholder="ชื่อใบรับรอง" value="${escHtml(lic?.title || '')}">
          <input class="finput" name="licenses[${i}][doc_no]" placeholder="เลขที่" value="${escHtml(lic?.doc_no || '')}">
          <input class="finput" name="licenses[${i}][date_issued]" placeholder="วันที่ออก (YYYY-MM-DD)" value="${escHtml(lic?.date_issued || '')}">
          <input type="file" name="licenses[${i}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf" style="font-size:12px;padding:6px">
        </div>
        ${lic?.file ? `
          <div class="lic-file-row">
            <input type="hidden" name="licenses[${i}][existing_file]" value="${escHtml(lic.file)}">
            <a href="/storage/${escHtml(lic.file)}" target="_blank" class="lic-file-link">📎 ไฟล์เดิม</a>
          </div>` : ''}
      `;
      list.appendChild(row);
    }
    
    function addCustomSw(prefix) {
      const inp = document.getElementById(prefix + '-sw-custom');
      const tags = document.getElementById(prefix + '-sw-custom-tags');
      if (!inp || !tags) return;
      const val = inp.value.trim();
      if (!val) return;
      const tag = document.createElement('span');
      tag.className = 'sw-tag';
      tag.innerHTML = `
        <input type="hidden" name="software_tools[]" value="${escHtml(val)}">
        ${escHtml(val)}
        <span class="x" onclick="this.parentElement.remove()">✕</span>
      `;
      tags.appendChild(tag);
      inp.value = '';
    }
    
    // Edit Tech (เปิดจาก row .member)
    function openEditTechFromEl(memberEl) {
      if (!memberEl || !memberEl.dataset.tech) return;
      let t;
      try { t = JSON.parse(memberEl.dataset.tech); } catch (e) { return; }
    
      const m = document.getElementById('modal-edit-tech');
      if (!m) return;
    
      const form = document.getElementById('form-edit-tech');
      if (form) form.action = URL_TECH_UPDATE(t.emp_id);
    
      const v = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
      };
    
      v('et-emp_id', t.emp_id);
      v('et-emp_name', t.emp_name);
      v('et-emp_name_eng', t.emp_name_eng);
      v('et-emp_nickname', t.emp_nickname);
      v('et-emp_phone', t.emp_phone);
      v('et-dob', t.date_of_birth);
      v('et-emp_position', t.emp_position || 'ลูกทีม');
      v('et-team-select', t.emp_team);
      v('et-status', t.status || 'active');
    
      updateBE('et');
      handlePositionChange('et');
    
      // photo
      const imgEl = document.getElementById('et-img-preview');
      const ph    = document.getElementById('et-img-ph');
      if (imgEl) {
        if (t.img) {
          imgEl.src = `/storage/${t.img}`;
          imgEl.style.display = 'block';
          if (ph) ph.style.display = 'none';
        } else {
          imgEl.src = '';
          imgEl.style.display = 'none';
          if (ph) ph.style.display = 'flex';
        }
      }
    
      // skills
      const skills = (t.emp_skill || '').split(',').map(s => s.trim()).filter(Boolean);
      m.querySelectorAll('#et-skill-grid label').forEach(lab => {
        const cb = lab.querySelector('input');
        if (!cb) return;
        cb.checked = skills.includes(cb.value);
        lab.classList.toggle('checked', cb.checked);
      });
    
      // competencies
      const comps = t.core_competencies || {};
      m.querySelectorAll('#et-comp-grid select[data-comp]').forEach(s => {
        s.value = comps[s.dataset.comp] || 'none';
        updateCompClass(s);
      });
    
      // software
      const sw = t.software_tools || [];
      m.querySelectorAll('#et-sw-grid label').forEach(lab => {
        const cb = lab.querySelector('input');
        if (!cb) return;
        cb.checked = sw.includes(cb.value);
        lab.classList.toggle('checked', cb.checked);
      });
    
      // custom software (ที่ไม่อยู่ใน predefined list)
      const tags = document.getElementById('et-sw-custom-tags');
      if (tags) {
        tags.innerHTML = '';
        const predefined = Array.from(m.querySelectorAll('#et-sw-grid label input')).map(i => i.value);
        sw.forEach(s => {
          if (!predefined.includes(s)) {
            const tag = document.createElement('span');
            tag.className = 'sw-tag';
            tag.innerHTML = `<input type="hidden" name="software_tools[]" value="${escHtml(s)}">${escHtml(s)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;
            tags.appendChild(tag);
          }
        });
      }
    
      // licenses
      const list = document.getElementById('et-lic-list');
      if (list) {
        list.innerHTML = '';
        _licIdx.et = 0;
        (t.licenses || []).forEach(l => addLicense('et', l));
      }
    
      openModal('modal-edit-tech');
    }
    
    function openAddSchedModal() {
      const m = document.getElementById('modal-sched');
      if (!m) return;
    
      const form = document.getElementById('form-add-sched');
      if (form) form.reset();
      const cidEl = document.getElementById('add-customer_id');
      if (cidEl) cidEl.value = '';
    
      // hide new-customer fields
      ['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
      });
      const banner = document.getElementById('add-cust-banner');
      if (banner) banner.style.display = 'none';
      TL.init('add');
      TL._state.add.start = null;
      TL._state.add.end = null;
      TL._state.add.team = '';
      TL.gotoToday('add');
      TL.onTeamChange('add');
    
      openModal('modal-sched');
    }
    
    function openEditSchedFromEl(btn) {
      if (!btn || !btn.dataset.sched) return;
      let s;
      try { s = JSON.parse(btn.dataset.sched); } catch (e) { return; }
    
      const m = document.getElementById('modal-edit-sched');
      if (!m) return;
    
      const form = document.getElementById('form-edit-sched');
      if (form) form.action = URL_SCHED_UPDATE(s.id);
    
      const v = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
      };
    
      v('es-so_number', s.so_number);
      v('es-customer_name', s.customer_name);
      v('es-job_type', s.job_type || 'general');
      v('es-job_title', s.job_title);
      v('es-job_location', s.job_location);
      v('es-job_la_long', s.job_la_long);
      v('es-team_name', s.team_name);
      v('es-note', s.note);
      TL.setRange('es', s.start_date, s.end_date, s.team_name, s.id);
    
      openModal('modal-edit-sched');
    }
    let _acIdx = -1;
    function custAutocomp(q, prefix) {
      const list = document.getElementById(prefix + '-ac-list');
      const cidEl = document.getElementById(prefix + '-customer_id');
      const banner = document.getElementById(prefix + '-cust-banner');
      if (!list) return;
    
      const kw = (q || '').toLowerCase().trim();
      if (!kw) {
        list.classList.remove('open');
        if (cidEl) cidEl.value = '';
        if (banner) banner.style.display = 'none';
        showNewCustFields(prefix, false);
        return;
      }
    
      const matches = CUST_DATA.filter(c =>
        (c.name || '').toLowerCase().includes(kw) ||
        (c.desc || '').toLowerCase().includes(kw)
      ).slice(0, 6);
    
      if (!matches.length) {
          list.classList.remove('open');  // ← ปิด dropdown
          list.innerHTML = '';
          if (cidEl) cidEl.value = '';
          if (banner) {
              banner.className = 'cust-banner cust-banner-new';
              banner.style.display = 'flex';
              banner.innerHTML = '✦ ลูกค้าใหม่ — กรุณากรอกรายละเอียดเพิ่มเติมด้านล่าง';
          }
          showNewCustFields(prefix, true);
          return;
      }
    
      list.innerHTML = matches.map((c, i) => `
        <div class="ac-item" data-idx="${i}" onclick="pickCust('${prefix}', ${c.id})">
          <div class="ac-item-name">${escHtml(c.name)}</div>
          ${c.desc ? `<div class="ac-item-meta">${escHtml(c.desc)}</div>` : ''}
        </div>
      `).join('');
      list.classList.add('open');
      _acIdx = -1;
    }
    
    function custAutocompKey(e, prefix) {
      const list = document.getElementById(prefix + '-ac-list');
      if (!list || !list.classList.contains('open')) return;
      const items = list.querySelectorAll('.ac-item');
      if (!items.length) return;
    
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        _acIdx = Math.min(_acIdx + 1, items.length - 1);
        items.forEach(i => i.classList.remove('ac-active'));
        items[_acIdx].classList.add('ac-active');
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        _acIdx = Math.max(_acIdx - 1, 0);
        items.forEach(i => i.classList.remove('ac-active'));
        items[_acIdx].classList.add('ac-active');
      } else if (e.key === 'Enter' && _acIdx >= 0) {
        e.preventDefault();
        items[_acIdx].click();
      } else if (e.key === 'Escape') {
        list.classList.remove('open');
      }
    }
    
    function pickCust(prefix, id) {
      const c = CUST_DATA.find(x => x.id === id);
      if (!c) return;
      const nameInp = document.getElementById(prefix + '-customer_name');
      const cidEl = document.getElementById(prefix + '-customer_id');
      const list = document.getElementById(prefix + '-ac-list');
      const banner = document.getElementById(prefix + '-cust-banner');

      if (nameInp) nameInp.value = c.name;
      if (cidEl) cidEl.value = c.id;
      if (list) list.classList.remove('open');
      if (banner) {
        banner.className = 'cust-banner cust-banner-old';
        banner.style.display = 'flex';
        banner.innerHTML = `✓ ลูกค้าเดิม: <strong>${escHtml(c.name)}</strong>${c.desc ? ' · ' + escHtml(c.desc) : ''}`;
      }
      showNewCustFields(prefix, false);
 
      if (c.loc) {
        const llEl = document.getElementById(prefix + '-job_la_long');
        if (llEl && !llEl.value) llEl.value = c.loc;
      }
      if (c.desc || c.name) {
        const locEl = document.getElementById(prefix + '-job_location');
        if (locEl && !locEl.value) locEl.value = c.desc ? c.name + ' · ' + c.desc + c.job_location : c.name;
      }
    }
    
    function showNewCustFields(prefix, show) {
      ['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = show ? '' : 'none';
      });
    }
    const STATUS_OPTS = {
      solar:      ['เสนอ','ปิดการขาย','กำลังติดตั้ง','ติดตั้งสำเร็จ'],
      electrical: ['เสนอ','ปิดการขาย','ดำเนินการ','ทดสอบ/ตรวจรับ','เสร็จสิ้น','ยกเลิก'],
      civil:      ['เสนอ','ปิดการขาย','ดำเนินการ','ตรวจรับงาน','เสร็จสิ้น','ยกเลิก'],
      general:    ['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],
    };
    function categoryOf(type) {
      if (!type) return 'general';
      if (String(type).startsWith('solar')) return 'solar';
      if (type === 'electrical') return 'electrical';
      if (type === 'civil') return 'civil';
      return 'general';
    }
    
    function onCustTypeChange(typeVal) {
      const cat = categoryOf(typeVal);
      const statusSel = document.getElementById('cf-status');
      if (statusSel) {
        const cur = statusSel.value;
        statusSel.innerHTML = (STATUS_OPTS[cat] || STATUS_OPTS.general)
          .map(s => `<option value="${s}"${s===cur?' selected':''}>${s}</option>`).join('');
      }
      const washWrap = document.getElementById('cf-wash-wrap');
      if (washWrap) washWrap.style.display = cat === 'solar' ? '' : 'none';
      const solarInfo = document.getElementById('cf-solar-info');
      if (solarInfo) solarInfo.style.display = cat === 'solar' ? '' : 'none';
      const finLbl = document.getElementById('cf-finish-lbl');
      if (finLbl) finLbl.textContent = cat === 'solar' ? 'วันติดตั้งสำเร็จ' : 'วันสิ้นสุด';
      const sizeLbl = document.getElementById('cf-size-lbl');
      if (sizeLbl) {
        sizeLbl.textContent = cat === 'solar' ? 'ขนาดติดตั้ง (kW)'
          : cat === 'electrical' ? 'ขนาดงาน'
          : cat === 'civil' ? 'พื้นที่/ขอบเขต'
          : 'ขนาด/ปริมาณ';
      }
    }
    
    function openCustAdd() {
      const m = document.getElementById('modal-cust');
      if (!m) return;
      const t = document.getElementById('cust-modal-title');
      if (t) t.textContent = 'เพิ่มลูกค้าใหม่';
      const form = document.getElementById('form-cust');
      if (form) {
        form.action = URL_CUST_STORE;
        form.reset();
      }
      const typeEl = document.getElementById('cf-type_project');
      if (typeEl) {
        typeEl.value = 'solar_install';
        onCustTypeChange('solar_install');
      }
      openModal('modal-cust');
    }
    
    function openCustEdit(btn) {
      if (!btn || !btn.dataset.cust) return;
      let c;
      try { c = JSON.parse(btn.dataset.cust); } catch (e) { return; }
    
      const m = document.getElementById('modal-cust');
      if (!m) return;
    
      const t = document.getElementById('cust-modal-title');
      if (t) t.textContent = 'แก้ไขลูกค้า: ' + (c.name || '');
    
      const form = document.getElementById('form-cust');
      if (form) form.action = URL_CUST_UPDATE(c.id);
    
      const v = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
      };
    
      v('cf-type_project', c.type_project || 'solar_install');
      onCustTypeChange(c.type_project || 'solar_install');
    
      v('cf-name', c.name);
      v('cf-desc', c.desc);
      v('cf-contact_name', c.contact_name);
      v('cf-phone', c.phone);
      v('cf-size', c.size);
      v('cf-price', c.price);
      v('cf-loc', c.loc);
      v('cf-finish_date', c.supervisor); 
      v('cf-wash_cycle', c.wash_cycle || 6);
      v('cf-notes', c.notes);
    
      // status (ต้องตั้งหลัง onCustTypeChange เพราะ options เพิ่งรีเฟรช)
      setTimeout(() => v('cf-status', c.status || 'เสนอ'), 50);
    
      openModal('modal-cust');
    }
    
    // ─── Customer Filter ──────────────────────────────────────────────
    let _custCat = 'all';
    let _custKw = '';
    
    function filterCustCat(cat, btn) {
      _custCat = cat;
      document.querySelectorAll('.cust-filter-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      applyCustFilter();
    }
    
    function filterCustTable(q) {
      _custKw = (q || '').toLowerCase().trim();
      applyCustFilter();
    }
    
    function applyCustFilter() {
      document.querySelectorAll('#cust-tbody tr[data-cat]').forEach(r => {
        const matchCat = _custCat === 'all' || r.dataset.cat === _custCat;
        const matchKw = !_custKw || (r.dataset.search || '').includes(_custKw);
        r.style.display = (matchCat && matchKw) ? '' : 'none';
      });
    }
    
    // ─── Customer Detail Modal ────────────────────────────────────────
    let _detailCust = null;
    
    function openCustDetail(btn) {
      if (!btn || !btn.dataset.cust) return;
      let c;
      try { c = JSON.parse(btn.dataset.cust); } catch (e) { return; }
      _detailCust = c;
    
      const m = document.getElementById('modal-cust-detail');
      if (!m) return;
    
      const cat = categoryOf(c.type_project);
      const catLabel = { solar:'🔆 Solar', electrical:'ไฟฟ้า', civil:'โยธา', general:'ทั่วไป' };
      const nameEl = document.getElementById('cd-name');
      if (nameEl) nameEl.textContent = c.name || '-';
      const tag = document.getElementById('cd-type-tag');
      if (tag) tag.innerHTML = `<span class="job-type-tag jt-${c.type_project || 'general'}">${catLabel[cat]}</span> <span class="cust-st cst-other" style="margin-left:6px">${escHtml(c.status || 'เสนอ')}</span>`;
    
      const sv = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = (val == null || val === '') ? '-' : val;
      };
    
      sv('cd-desc', c.desc);
      sv('cd-status', c.status);
      sv('cd-contact', c.contact_name);
      sv('cd-phone', c.phone);
      sv('cd-size', c.size);
      sv('cd-price', c.price ? Number(c.price).toLocaleString() + ' ฿' : '-');
      sv('cd-loc', c.loc);
      sv('cd-finish_date', c.supervisor ? fmtDate(c.supervisor) : '-');
      sv('cd-notes', c.notes);
    
      const sizeLbl = document.getElementById('cd-size-lbl');
      if (sizeLbl) sizeLbl.textContent = cat === 'solar' ? 'ขนาดติดตั้ง' : 'ขนาด';
      const finLbl = document.getElementById('cd-finish-lbl');
      if (finLbl) finLbl.textContent = cat === 'solar' ? 'วันติดตั้งเสร็จ' : 'วันสิ้นสุด';
    
      // ─── Wash tab (Solar only) ─────────────────────────
      const washBtn = document.getElementById('dtab-btn-wash');
      if (washBtn) washBtn.style.display = cat === 'solar' ? '' : 'none';
    
      const washCountdown = document.getElementById('cd-wash-countdown');
      if (washCountdown && cat === 'solar') {
        if (c.wash_next) {
          const days = daysBetween(new Date(), c.wash_next);
          let cls, label;
          if (days < 0) { cls = 'overdue'; label = `เลยกำหนด ${Math.abs(days)} วัน`; }
          else if (days === 0) { cls = 'overdue'; label = 'ถึงกำหนดวันนี้!'; }
          else if (days <= 30) { cls = 'soon'; label = `อีก ${days} วัน`; }
          else { cls = 'ok'; label = `อีก ${days} วัน`; }
          washCountdown.style.display = '';
          washCountdown.className = 'wash-countdown ' + cls;
          washCountdown.innerHTML = `
            <div class="wcd-num ${cls}">${Math.abs(days)}</div>
            <div>
              <div class="wcd-label">${label}</div>
              <div class="wcd-date">กำหนดล้างถัดไป: ${fmtDate(c.wash_next)}</div>
            </div>`;
        } else {
          washCountdown.style.display = '';
          washCountdown.className = 'wash-countdown pending';
          washCountdown.innerHTML = '<div class="wcd-num pending">⏳</div><div><div class="wcd-label">ยังไม่ตั้งกำหนด</div><div class="wcd-date">รอเปลี่ยนสถานะเป็น "ติดตั้งสำเร็จ"</div></div>';
        }
      } else if (washCountdown) {
        washCountdown.style.display = 'none';
      }
    
      // wash logs
      const wlogs = (c.wash_logs || []).filter(w => !w.type || w.type === 'wash');
      const washCnt = document.getElementById('cd-wash-count');
      if (washCnt) washCnt.textContent = `(${wlogs.length} ครั้ง)`;
      const washBody = document.getElementById('cd-wash-body');
      if (washBody) {
        washBody.innerHTML = wlogs.length ? `
          <table class="wash-log-tbl">
            <thead><tr><th>#</th><th>วันที่</th><th>ทีม/ช่าง</th><th>หมายเหตุ</th><th></th></tr></thead>
            <tbody>${wlogs.map(w => `
              <tr>
                <td><span class="wash-num-circle">${w.num}</span></td>
                <td>${fmtDate(w.date)}</td>
                <td>${escHtml(w.tech || '-')}</td>
                <td>${escHtml(w.note || '-')}</td>
                <td><form method="POST" action="${URL_WASH_DEL(c.id, w.num)}" onsubmit="return confirm('ลบประวัติ #${w.num}?')" style="display:inline">
                  <input type="hidden" name="_token" value="${CSRF}">
                  <button class="btn btn-sm btn-danger" type="submit">ลบ</button>
                </form></td>
              </tr>`).join('')}
            </tbody>
          </table>
        ` : '<div style="text-align:center;color:var(--md);padding:24px;font-size:13px">ยังไม่มีประวัติการล้าง</div>';
      }
      const washForm = document.getElementById('form-add-wash');
      if (washForm) washForm.action = URL_WASH_STORE(c.id);
    
      // ─── Milestone tab (non-solar) ─────────────────────
      const msBtn = document.getElementById('dtab-btn-milestone');
      if (msBtn) msBtn.style.display = cat !== 'solar' ? '' : 'none';
    
      const mlogs = (c.wash_logs || []).filter(w => w.type === 'milestone');
      const msBody = document.getElementById('cd-milestone-body');
      if (msBody) {
        msBody.innerHTML = mlogs.length
          ? mlogs.map((ms, i) => `
              <div class="milestone-item">
                <div class="milestone-date">${fmtDate(ms.date)}</div>
                <div class="milestone-note">${escHtml(ms.note || '-')}</div>
                ${ms.by ? `<div class="milestone-by">โดย: ${escHtml(ms.by)}</div>` : ''}
                <form method="POST" action="${URL_MILESTONE_DEL(c.id, i)}" onsubmit="return confirm('ลบ?')" style="display:inline-block;margin-top:6px">
                  <input type="hidden" name="_token" value="${CSRF}">
                  <button class="btn btn-sm btn-danger" type="submit">ลบ</button>
                </form>
              </div>`).join('')
          : '<div style="text-align:center;color:var(--md);padding:24px;font-size:13px">ยังไม่มี milestone</div>';
      }
      const msForm = document.getElementById('form-add-milestone');
      if (msForm) msForm.action = URL_MILESTONE_STORE(c.id);
    
      // ─── Schedules tab ─────────────────────────────────
      const linked = SCHED_DATA.filter(s => s.customer_name === c.name);
      const schedDiv = document.getElementById('cd-schedules');
      if (schedDiv) {
        schedDiv.innerHTML = linked.length ? `
          <div class="table-wrap"><table>
            <thead><tr><th>SO</th><th>งาน</th><th>ประเภท</th><th>วันเริ่ม</th><th>วันสิ้นสุด</th><th>ทีม</th></tr></thead>
            <tbody>${linked.map(s => `
              <tr>
                <td><span class="so-code">${escHtml(s.so_number)}</span></td>
                <td>${escHtml(s.job_title)}</td>
                <td><span class="job-type-tag jt-${s.job_type || 'general'}">${escHtml(JOB_TYPES[s.job_type || 'general'] || s.job_type)}</span></td>
                <td>${fmtDate(s.start_date)}</td>
                <td>${fmtDate(s.end_date)}</td>
                <td>${escHtml(s.team_name)}</td>
              </tr>`).join('')}
            </tbody>
          </table></div>
        ` : '<div style="text-align:center;color:var(--md);padding:24px;font-size:13px">ยังไม่มีงานที่ผูกกับลูกค้านี้</div>';
      }
    
      // default tab
      switchDTab('info', document.getElementById('dtab-btn-info'));
      openModal('modal-cust-detail');
    }
    
    function switchDTab(name, btn) {
      document.querySelectorAll('.dtab-panel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.dtab').forEach(b => b.classList.remove('active'));
      const panel = document.getElementById('dtab-' + name);
      if (panel) panel.classList.add('active');
      if (btn) btn.classList.add('active');
    }
    
    function editFromDetail() {
      if (!_detailCust) return;
      closeModalById('modal-cust-detail');
      const fakeBtn = { dataset: { cust: JSON.stringify(_detailCust) } };
      openCustEdit(fakeBtn);
    }
    
    function openAddWashModal() {
      if (!_detailCust) return;
      const f = document.getElementById('form-add-wash');
      if (f) { f.action = URL_WASH_STORE(_detailCust.id); f.reset(); }
      openModal('modal-add-wash');
    }
    function openAddMilestoneModal() {
      if (!_detailCust) return;
      const f = document.getElementById('form-add-milestone');
      if (f) { f.action = URL_MILESTONE_STORE(_detailCust.id); f.reset(); }
      openModal('modal-add-milestone');
    }
    
    // ═══════════════════════════════════════════════════════════════
    //  SOLAR ACCOUNTS
    // ═══════════════════════════════════════════════════════════════
    function openAccAdd() {
      const m = document.getElementById('modal-account');
      if (!m) return;
      const t = document.getElementById('acc-modal-title');
      if (t) t.textContent = 'เพิ่มบัญชีผู้ใช้ Solar';
      const form = document.getElementById('form-account');
      if (form) { form.action = URL_ACC_STORE; form.reset(); }
      openModal('modal-account');
    }
    
    function openAccEdit(btn) {
      if (!btn || !btn.dataset.acc) return;
      let a;
      try { a = JSON.parse(btn.dataset.acc); } catch (e) { return; }
    
      const m = document.getElementById('modal-account');
      if (!m) return;
      const t = document.getElementById('acc-modal-title');
      if (t) t.textContent = 'แก้ไขบัญชี';
      const form = document.getElementById('form-account');
      if (form) form.action = URL_ACC_UPDATE(a.id);
    
      const v = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
      };
      v('af-no', a.no);
      v('af-plane', a.plane);
      v('af-username', a.username);
      v('af-password', a.password);
      v('af-email', a.email);
      v('af-app_password', a.app_password);
      v('af-customer', a.customer);
      v('af-inverter', a.inverter);
    
      openModal('modal-account');
    }
    
    function togglePw(btn) {
      const span = btn.previousElementSibling;
      if (!span || !span.classList.contains('acc-pw-text')) return;
      if (span.textContent === '••••••••') {
        span.textContent = span.dataset.pw;
        btn.textContent = '🔒';
      } else {
        span.textContent = '••••••••';
        btn.textContent = '👁';
      }
    }
    
    function copyPw(pw, btn) {
      if (!pw) return;
      navigator.clipboard.writeText(pw).then(() => {
        const old = btn.textContent;
        btn.textContent = '✓';
        setTimeout(() => { btn.textContent = old; }, 1200);
      }).catch(() => alert('คัดลอกไม่สำเร็จ'));
    }
    
    // account customer autocomplete
    function accCustAutocomp(q) {
      const list = document.getElementById('af-cust-list');
      if (!list) return;
      const kw = (q || '').toLowerCase().trim();
      if (!kw) { list.classList.remove('open'); return; }
      const matches = CUST_DATA
        .filter(c => (c.name || '').toLowerCase().includes(kw))
        .slice(0, 6);
      if (!matches.length) { list.classList.remove('open'); return; }
      list.innerHTML = matches.map(c => `
        <div class="ac-item" onclick="document.getElementById('af-customer').value='${escHtml(c.name).replace(/'/g,'\\\'')}';this.parentElement.classList.remove('open')">
          <div class="ac-item-name">${escHtml(c.name)}</div>
          ${c.desc ? `<div class="ac-item-meta">${escHtml(c.desc)}</div>` : ''}
        </div>`).join('');
      list.classList.add('open');
    }
    
    // ═══════════════════════════════════════════════════════════════
    //  TEAM MEMBERS MODAL
    // ═══════════════════════════════════════════════════════════════
    let _currentMembers = [];
    
    function openTeamMembers(teamName) {
      const overlay = document.getElementById('members-overlay');
      if (!overlay) return;
      const nameEl = document.getElementById('mm-team-name');
      if (nameEl) nameEl.textContent = teamName;
    
      const members = TECH_DATA.filter(t => t.emp_team === teamName);
      members.sort((a, b) => {
        if (a.emp_position === 'หัวหน้าทีม' && b.emp_position !== 'หัวหน้าทีม') return -1;
        if (b.emp_position === 'หัวหน้าทีม' && a.emp_position !== 'หัวหน้าทีม') return 1;
        return (a.emp_name || '').localeCompare(b.emp_name || '', 'th');
      });
      _currentMembers = members;
    
      const cnt = document.getElementById('mm-count');
      if (cnt) cnt.textContent = members.length + ' คน';
    
      const searchInput = overlay.querySelector('.mm-search-wrap input');
      if (searchInput) searchInput.value = '';
    
      renderMembers(members);
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    
    function renderMembers(members) {
      const grid = document.getElementById('mm-grid');
      if (!grid) return;
      if (!members.length) {
        grid.innerHTML = '<div class="mm-empty">ไม่พบสมาชิก</div>';
        return;
      }
      grid.innerHTML = members.map(t => {
        const isHead = t.emp_position === 'หัวหน้าทีม';
        const headTag = isHead ? '<span class="head-tag">หัวหน้า</span>' : '';
        const photo = t.img
          ? `<img src="/storage/${escHtml(t.img)}" style="width:100%;height:100%;object-fit:cover">`
          : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--or4);color:#fff;font-weight:800">3E</div>';
        return `
          <div class="mm-card ${isHead ? 'is-head' : ''}" data-tech='${JSON.stringify(t).replace(/'/g, "&#39;")}'
              onclick="closeMembersModal();openProfileFromEl(this)">
            <div style="width:42px;height:42px;border-radius:50%;border:2px solid var(--or5);overflow:hidden;flex-shrink:0">${photo}</div>
            <div class="mm-card-info">
              <div class="mm-card-name">${escHtml(t.emp_name || t.emp_id)} ${headTag}</div>
              <div class="mm-card-role">${escHtml(t.emp_id)}${t.emp_nickname ? ' · ' + escHtml(t.emp_nickname) : ''}</div>
              ${t.emp_phone ? `<div class="mm-card-role">${escHtml(t.emp_phone)}</div>` : ''}
            </div>
          </div>`;
      }).join('');
    }
    
    function filterMembers(q) {
      const kw = (q || '').toLowerCase().trim();
      if (!kw) { renderMembers(_currentMembers); return; }
      const filtered = _currentMembers.filter(t => {
        const blob = `${t.emp_name || ''} ${t.emp_name_eng || ''} ${t.emp_nickname || ''} ${t.emp_id || ''} ${t.emp_phone || ''} ${t.emp_skill || ''}`.toLowerCase();
        return blob.includes(kw);
      });
      renderMembers(filtered);
    }
    
    function closeMembersModal() {
      const o = document.getElementById('members-overlay');
      if (o) o.classList.remove('open');
      document.body.style.overflow = '';
    }
    
    // ═══════════════════════════════════════════════════════════════
    //  TEAM CALENDAR (FULLSCREEN)
    // ═══════════════════════════════════════════════════════════════
    const TCAL = {
      team: '',
      year: 0,
      month: 0,
      selStart: null,
      selEnd: null,
      selecting: 'start',
      jobs: [],
    };
    
    function openTeamCalendar(teamName) {
      TCAL.team = teamName;
      const today = new Date();
      TCAL.year = today.getFullYear();
      TCAL.month = today.getMonth();
      TCAL.selStart = null;
      TCAL.selEnd = null;
      TCAL.selecting = 'start';
      TCAL.jobs = SCHED_DATA.filter(s => s.team_name === teamName);
    
      const nameEl = document.getElementById('tcal-team-name');
      if (nameEl) nameEl.textContent = teamName;
      const cntEl = document.getElementById('tcal-job-count');
      if (cntEl) {
        cntEl.textContent = `${TCAL.jobs.length} งาน`;
        cntEl.classList.toggle('has-jobs', TCAL.jobs.length > 0);
      }
    
      renderTCal();
    
      const overlay = document.getElementById('tcal-overlay');
      if (overlay) overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    
    function closeTeamCalendar() {
      const overlay = document.getElementById('tcal-overlay');
      if (overlay) overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
    
    function renderTCal() {
      const monthsTH = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                        'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
      const dayHdrs = ['อา','จ','อ','พ','พฤ','ศ','ส'];
    
      const renderMonth = (yr, mo, sideId) => {
        const nameEl = document.getElementById(`cal-${sideId}-name`);
        if (nameEl) nameEl.textContent = `${monthsTH[mo]} ${yr + 543}`;
        const hdrsEl = document.getElementById(`cal-${sideId}-hdrs`);
        if (hdrsEl) hdrsEl.innerHTML = dayHdrs.map(d => `<div class="cal-dhdr">${d}</div>`).join('');
        const gridEl = document.getElementById(`cal-${sideId}-grid`);
        if (!gridEl) return;
    
        const firstDay = new Date(yr, mo, 1).getDay();
        const daysInMo = new Date(yr, mo + 1, 0).getDate();
        const todayStr = ymd(new Date());
    
        let html = '';
        for (let i = 0; i < firstDay; i++) html += '<div class="cal-day cal-other"><div class="cal-di"></div></div>';
        for (let d = 1; d <= daysInMo; d++) {
          const dStr = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
          const cls = ['cal-day'];
          if (dStr === todayStr) cls.push('cal-today');
          if (TCAL.selStart && dStr === TCAL.selStart) cls.push('cal-sel-s');
          if (TCAL.selEnd && dStr === TCAL.selEnd) cls.push('cal-sel-e');
          if (TCAL.selStart && TCAL.selEnd && dStr > TCAL.selStart && dStr < TCAL.selEnd) cls.push('cal-in-range');
    
          const evs = TCAL.jobs.filter(s => s.start_date <= dStr && s.end_date >= dStr);
          let evDots = '';
          if (evs.length > 0) {
            const dots = Math.min(evs.length, 3);
            let d2 = '';
            for (let i = 0; i < dots; i++) d2 += '<div class="cal-evdot"></div>';
            if (evs.length > 3) d2 += `<span class="cal-evcount">+${evs.length - 3}</span>`;
            evDots = `<div class="cal-evdots">${d2}</div>`;
          }
          html += `<div class="${cls.join(' ')}" onclick="tcalPickDate('${dStr}')">
            <div class="cal-di">${d}</div>${evDots}
          </div>`;
        }
        gridEl.innerHTML = html;
      };
    
      const nextMo = TCAL.month === 11 ? 0 : TCAL.month + 1;
      const nextYr = TCAL.month === 11 ? TCAL.year + 1 : TCAL.year;
      renderMonth(TCAL.year, TCAL.month, 'left');
      renderMonth(nextYr, nextMo, 'right');
    
      // selector buttons
      const startLbl = document.getElementById('cal-start-lbl');
      const startSub = document.getElementById('cal-start-sub');
      const endLbl = document.getElementById('cal-end-lbl');
      const endSub = document.getElementById('cal-end-sub');
      if (startLbl) startLbl.textContent = TCAL.selStart ? fmtDate(TCAL.selStart) : 'เลือกวันเริ่มต้น';
      if (startSub) startSub.textContent = TCAL.selStart ? 'วันที่เริ่มงาน' : 'แตะเพื่อเลือก';
      if (endLbl) endLbl.textContent = TCAL.selEnd ? fmtDate(TCAL.selEnd) : 'เลือกวันสิ้นสุด';
      if (endSub) endSub.textContent = TCAL.selEnd ? 'วันที่สิ้นสุดงาน' : (TCAL.selStart ? 'แตะเพื่อเลือก' : 'รอเลือกวันเริ่ม');
    
      const note = document.getElementById('cal-footer-note');
      const reset = document.getElementById('cal-reset-btn');
      if (TCAL.selStart && TCAL.selEnd) {
        if (note) {
          const inRange = TCAL.jobs.filter(s =>
            !(s.end_date < TCAL.selStart || s.start_date > TCAL.selEnd)
          );
          note.innerHTML = `ช่วงที่เลือก: <strong>${fmtDate(TCAL.selStart)}</strong> ถึง <strong>${fmtDate(TCAL.selEnd)}</strong> — พบ <strong>${inRange.length}</strong> งาน`;
        }
        if (reset) reset.style.display = '';
      } else if (TCAL.selStart) {
        if (note) note.innerHTML = `เริ่ม: <strong>${fmtDate(TCAL.selStart)}</strong> — เลือกวันสิ้นสุด`;
        if (reset) reset.style.display = '';
      } else {
        if (note) note.textContent = 'เลือกช่วงวันที่ต้องการดูงาน';
        if (reset) reset.style.display = 'none';
      }
    }
    
    function tcalPickDate(dStr) {
      const evs = TCAL.jobs.filter(s => s.start_date <= dStr && s.end_date >= dStr);
      if (evs.length > 0) {
        showCalPopup(dStr, evs);
        return;
      }
      // ถ้าไม่มีงาน ให้ใช้เป็นการเลือก range
      if (TCAL.selecting === 'start' || (TCAL.selStart && TCAL.selEnd)) {
        TCAL.selStart = dStr;
        TCAL.selEnd = null;
        TCAL.selecting = 'end';
      } else {
        if (dStr < TCAL.selStart) {
          TCAL.selEnd = TCAL.selStart;
          TCAL.selStart = dStr;
        } else {
          TCAL.selEnd = dStr;
        }
        TCAL.selecting = 'start';
      }
      renderTCal();
    }
    
    function showCalPopup(dStr, evs) {
      const bg = document.getElementById('cal-popup-bg');
      if (!bg) return;
      const dateEl = document.getElementById('cal-popup-date');
      if (dateEl) dateEl.textContent = fmtDate(dStr);
      const cntEl = document.getElementById('cal-popup-count');
      if (cntEl) {
        if (evs.length > 0) {
          cntEl.style.display = '';
          cntEl.textContent = `${evs.length} งาน`;
        } else {
          cntEl.style.display = 'none';
        }
      }
      const inner = document.getElementById('cal-popup-inner');
      if (inner) {
        inner.innerHTML = evs.length ? evs.map(s => `
          <div class="cal-ev-card">
            <div class="cal-ev-top">
              <div>
                <div class="cal-ev-cust"><div class="cal-ev-dot"></div>${escHtml(s.customer_name)}</div>
                <div class="cal-ev-job">${escHtml(s.job_title)}</div>
              </div>
              <span class="cal-so">${escHtml(s.so_number)}</span>
            </div>
            <div class="cal-ev-meta">
              <span class="cal-ev-ml">ช่วง:</span><span class="cal-ev-mv">${fmtDate(s.start_date)} → ${fmtDate(s.end_date)}</span>
              ${s.job_location ? `<span class="cal-ev-ml">สถานที่:</span><span class="cal-ev-mv">${escHtml(s.job_location)}</span>` : ''}
              ${s.note ? `<span class="cal-ev-ml">หมายเหตุ:</span><span class="cal-ev-mv">${escHtml(s.note)}</span>` : ''}
            </div>
          </div>`).join('') : '<div class="cal-empty">ไม่มีงานในวันนี้</div>';
      }
      bg.classList.add('open');
    }
    
    function calReset() {
      TCAL.selStart = null;
      TCAL.selEnd = null;
      TCAL.selecting = 'start';
      renderTCal();
    }
    
    document.addEventListener('click', e => {
      // tl nav (เลื่อนเดือนในฟอร์มเพิ่ม/แก้ไขงาน)
      const tlBtn = e.target.closest('[data-tl-nav]');
      if (tlBtn) {
        e.stopPropagation();
        const prefix = tlBtn.dataset.tlPrefix;
        const dir = tlBtn.dataset.tlNav === 'prev' ? -1 : 1;
        TL.nav(prefix, dir);
        return;
      }
      if (e.target.id === 'cal-prev') {
        TCAL.month--;
        if (TCAL.month < 0) { TCAL.month = 11; TCAL.year--; }
        renderTCal();
      } else if (e.target.id === 'cal-next') {
        TCAL.month++;
        if (TCAL.month > 11) { TCAL.month = 0; TCAL.year++; }
        renderTCal();
      }
    });
    
    document.getElementById('cal-start-btn')?.addEventListener('click', () => {
      TCAL.selecting = 'start';
      document.getElementById('cal-start-btn').classList.add('active');
      document.getElementById('cal-end-btn').classList.remove('active');
    });
    document.getElementById('cal-end-btn')?.addEventListener('click', () => {
      TCAL.selecting = 'end';
      document.getElementById('cal-end-btn').classList.add('active');
      document.getElementById('cal-start-btn').classList.remove('active');
    });
    
    // ═══════════════════════════════════════════════════════════════
    //  INIT
    // ═══════════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
      // close autocomp lists on outside click
      document.addEventListener('click', e => {
        document.querySelectorAll('.autocomp-list.open').forEach(list => {
          if (!list.parentElement.contains(e.target)) {
            list.classList.remove('open');
          }
        });
      });
    
      // initial competency colors
      document.querySelectorAll('.comp-select').forEach(s => updateCompClass(s));
    
      // initial DOB BE labels
      ['add', 'et'].forEach(p => updateBE(p));
    });
</script>
</body>
</html>
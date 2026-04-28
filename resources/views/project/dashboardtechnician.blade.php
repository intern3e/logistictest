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

/* ── NAV ── */
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
.nav-solar-link{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:linear-gradient(135deg,var(--solar),var(--solar2));color:#fff;font-size:13px;font-weight:700;border-radius:20px;text-decoration:none;transition:all .18s;white-space:nowrap}
.nav-solar-link:hover{opacity:.85;transform:translateY(-1px)}

/* ── FLASH ── */
.main{padding:24px 36px}
.flash{padding:13px 18px;border-radius:var(--radius-md);font-size:14px;font-weight:600;margin-bottom:18px;display:flex;align-items:center;gap:10px;position:relative;overflow:hidden;animation:flashIn .3s ease,flashOut .5s ease 4.5s forwards}
.flash::after{content:'';position:absolute;bottom:0;left:0;height:3px;background:currentColor;opacity:.4;animation:flashProg 5s linear forwards}
.flash-success{background:#dcfce7;color:#14532d;border:1.5px solid #86efac}
.flash-success::before{content:'✓';font-size:16px}
.flash-error{background:#fee2e2;color:#7f1d1d;border:1.5px solid #fca5a5}
.flash-error::before{content:'✕';font-size:16px}
@keyframes flashIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}
@keyframes flashOut{from{opacity:1;max-height:100px;margin-bottom:18px;padding:13px 18px}to{opacity:0;max-height:0;margin-bottom:0;padding:0;border-width:0}}
@keyframes flashProg{from{width:100%}to{width:0}}

/* ── PANELS ── */
.panel{display:none}
.panel.active{display:block;animation:fi .22s ease}
@keyframes fi{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
.panel-header{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.panel-title{font-size:20px;font-weight:800;color:var(--dk);letter-spacing:-.3px;flex:1}
.panel-actions{display:flex;gap:10px;align-items:center}

/* ── BTNS ── */
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

/* ── TEAM CARDS ── */
.team-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:20px;margin-bottom:24px}
.team-card{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-lg);overflow:hidden;transition:all .22s;box-shadow:var(--shadow-sm);display:flex;flex-direction:column}
.team-card:hover{box-shadow:var(--shadow-lg);border-color:var(--or5);transform:translateY(-3px)}
.team-head-bar{padding:16px 18px;background:linear-gradient(135deg,#2d2521,#1a1512);border-bottom:3px solid var(--or);display:flex;align-items:center;gap:12px}
.team-title{font-size:16px;font-weight:800;color:#fff}
.team-meta{font-size:12px;color:var(--or3);margin-top:3px;font-weight:600}
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

/* ── TABLE ── */
.table-wrap{background:var(--wh);border:1.5px solid var(--bd);border-radius:var(--radius-lg);overflow-x:auto;box-shadow:var(--shadow-sm)}
table{width:100%;border-collapse:collapse;min-width:1000px}
th,td{padding:13px 16px;text-align:left;font-size:14px;border-bottom:1px solid var(--bd)}
th{background:linear-gradient(to bottom,#f9f7f4,var(--lt));font-weight:800;font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--md);border-bottom:2px solid var(--bd2)}
tbody tr:hover td{background:#fff8f4}
tbody tr:last-child td{border-bottom:none}
.so-code{font-family:'Courier New',monospace;font-weight:800;color:var(--or2);font-size:13px;background:var(--or3);padding:3px 9px;border-radius:6px;border:1px solid var(--or5)}
.empty-state{text-align:center;padding:60px 20px;color:var(--md);font-size:15px;font-weight:500}

/* ── BADGES ── */
.badge{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;display:inline-block}
.b-pending{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.b-progress{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}
.b-done{background:#dcfce7;color:#14532d;border:1px solid #86efac}
.b-cancel{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
.b-solar{background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe}
.b-wash{background:#cffafe;color:#155e75;border:1px solid #67e8f9}
.b-maint{background:#fce7f3;color:#9d174d;border:1px solid #fbcfe8}

/* ── JOB TYPE ICON ── */
.job-type-tag{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:16px;font-size:12px;font-weight:700}
.jt-solar_install{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}
.jt-solar_wash{background:#cffafe;color:#155e75;border:1px solid #67e8f9}
.jt-solar_maintenance{background:#fce7f3;color:#9d174d;border:1px solid #fbcfe8}
.jt-electrical{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
.jt-civil{background:#fef3c7;color:#78350f;border:1px solid #fcd34d}
.jt-general{background:var(--lt);color:var(--md);border:1px solid var(--bd)}

/* ── CUSTOMER TABLE ── */
.cust-st{display:inline-block;padding:4px 11px;border-radius:14px;font-size:12px;font-weight:700}
.cst-quote{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
.cst-closed{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
.cst-installing{background:#cffafe;color:#155e75;border:1px solid #67e8f9}
.cst-success{background:#dcfce7;color:#14532d;border:1px solid #86efac}
.cst-other{background:var(--lt);color:var(--md);border:1px solid var(--bd)}
.wash-next-tag{display:inline-block;padding:4px 10px;border-radius:7px;font-size:13px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.wash-next-tag.empty{background:var(--lt);color:#c4bdb6;font-weight:500;border-color:var(--bd)}
.cust-name-btn{background:none;border:none;padding:0;font-family:inherit;font-size:14px;font-weight:700;color:var(--bl);cursor:pointer;text-align:left}
.cust-name-btn:hover{text-decoration:underline}

/* ── MODAL ── */
.overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(26,21,18,.65);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:16px}
.overlay.open{display:flex;animation:fadeIn .2s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.pmodal{background:var(--wh);border-radius:var(--radius-xl);width:760px;max-width:100%;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,.22);max-height:92vh;overflow-y:auto;animation:slideUp .22s ease}
.pmodal-wide{width:920px}
@keyframes slideUp{from{opacity:0;transform:translateY(18px) scale(.98)}to{opacity:1;transform:none}}
.pmodal::-webkit-scrollbar{width:5px}
.pmodal::-webkit-scrollbar-thumb{background:var(--or5);border-radius:3px}
.pmodal-strip{height:5px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24,var(--or6));flex-shrink:0}
.modal-header{padding:18px 24px 14px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(to bottom,#fffbf7,var(--wh));border-bottom:1.5px solid var(--bd)}
.modal-title{font-size:18px;font-weight:800;color:var(--dk)}
.modal-close{width:32px;height:32px;border-radius:50%;background:var(--lt);border:1.5px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--md);transition:all .18s;font-weight:700}
.modal-close:hover{background:var(--or);color:#fff;border-color:var(--or)}
.modal-body{padding:20px 24px}

/* ── FORM ── */
.finput{width:100%;padding:10px 13px;border-radius:var(--radius-sm);border:1.5px solid var(--bd);font-family:inherit;font-size:14px;color:var(--dk);outline:none;transition:all .18s;background:var(--wh);font-weight:500}
.finput:focus{border-color:var(--or);box-shadow:0 0 0 3px rgba(249,115,22,.12)}
.finput::placeholder{color:#c4bdb6}
.frow{margin-bottom:14px}
.flabel{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--or3);margin-bottom:5px;display:block}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.fgrid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
.fcol-full{grid-column:1/-1}
.ferr{background:#fee2e2;color:#b91c1c;padding:11px 14px;border-radius:var(--radius-sm);font-size:13px;margin-bottom:14px;border:1.5px solid #fca5a5;font-weight:600}
.factions{display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1.5px solid var(--bd)}

/* ── SKILL / COMP ── */
.section-h{font-size:13px;font-weight:800;color:var(--or2);margin:18px 0 10px;padding-bottom:6px;border-bottom:2px solid var(--or5);letter-spacing:.02em;display:flex;align-items:center;gap:6px}
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
.comp-select{width:100%;padding:6px 9px;border-radius:var(--radius-sm);border:1.5px solid var(--bd);font-family:inherit;font-size:13px;font-weight:600;background:var(--wh);cursor:pointer;transition:all .15s}
.comp-select.lv-basic{background:#fef3c7;border-color:#fde68a;color:#92400e}
.comp-select.lv-skill{background:#dbeafe;border-color:#bfdbfe;color:#1e40af}
.comp-select.lv-expert{background:#dcfce7;border-color:#86efac;color:#14532d;font-weight:800}
.sw-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:7px}
.sw-custom-row{display:flex;gap:8px;margin-top:9px}
.sw-custom-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:9px}
.sw-tag{background:var(--bl);color:#fff;font-size:12px;font-weight:600;padding:4px 12px;border-radius:14px;display:inline-flex;align-items:center;gap:6px}
.sw-tag .x{cursor:pointer;font-weight:800;opacity:.8;font-size:11px}
.btn-other{padding:9px 14px;border-radius:var(--radius-sm);border:1.5px solid var(--or5);background:var(--or3);color:var(--or2);font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap;transition:all .15s}
.btn-other:hover{background:var(--or);color:#fff}

/* ── RESUME / PHOTO ── */
.resume-top{display:flex;gap:22px;padding:18px 22px;background:linear-gradient(135deg,#fffbf7,#fff8f0);border-bottom:1.5px solid var(--bd)}
.photo-col{flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:7px}
.photo-box{width:108px;height:135px;border:2.5px dashed var(--or5);border-radius:12px;overflow:hidden;cursor:pointer;background:#fff8f0;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;transition:all .2s}
.photo-box:hover{border-color:var(--or);background:var(--or3);transform:translateY(-2px)}
.photo-box:hover .photo-overlay{opacity:1}
.photo-box img.resume-img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;display:none}
.photo-overlay{position:absolute;inset:0;background:rgba(240,71,9,.72);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .18s}
.photo-overlay span{color:#fff;font-size:11px;font-weight:800;text-align:center}
.photo-placeholder{display:flex;flex-direction:column;align-items:center;gap:5px;color:var(--md)}
.photo-placeholder svg{width:28px;height:28px;opacity:.5}
.photo-placeholder span{font-size:10px;font-weight:700;text-align:center;color:var(--md)}
.photo-label{font-size:10px;font-weight:700;color:var(--or3);letter-spacing:.08em;text-transform:uppercase}
.resume-badge-abs{position:absolute;top:-6px;right:-6px;background:var(--or);color:#fff;font-size:9px;font-weight:800;padding:2px 6px;border-radius:6px;z-index:3}
.resume-fields{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:12px}
.resume-fields .frow{margin-bottom:0}
input[type=file].hidden-file{display:none}

/* ── LICENSE ── */
.lic-list{display:flex;flex-direction:column;gap:11px}
.lic-item{border:1.5px solid var(--bd);border-radius:var(--radius-md);padding:12px 14px;background:var(--lt)}
.lic-item:hover{border-color:var(--or5)}
.lic-item-head{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.lic-num{font-size:12px;font-weight:800;color:var(--or2);background:var(--or3);padding:2px 9px;border-radius:12px;border:1px solid var(--or5)}
.lic-del{margin-left:auto;padding:3px 11px;background:#ec0101;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit;transition:all .15s}
.lic-del:hover{background:#fca5a5;color:#7f1d1d}
.lic-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}
.lic-file-row{display:flex;gap:7px;align-items:center;margin-top:7px;flex-wrap:wrap}
.lic-file-link{font-size:12px;color:#06fa60;font-weight:700;padding:3px 9px;background:#dcfce7;border-radius:5px;border:1px solid #13cc57;text-decoration:none}
.btn-add-lic{width:100%;padding:11px;border:2px dashed var(--or5);background:var(--or3);color:var(--or2);border-radius:var(--radius-md);cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:all .18s;margin-top:10px}
.btn-add-lic:hover{border-color:var(--or);background:var(--or4)}

/* ── DOB ── */
.dob-row{display:flex;gap:8px;align-items:center}
.dob-row input[type=date]{flex:1}
.dob-be{font-size:12px;color:var(--or2);font-weight:700;background:var(--or3);padding:6px 11px;border-radius:var(--radius-sm);border:1.5px solid var(--or5);white-space:nowrap;min-width:130px;text-align:center}
.emp-id-note{font-size:12px;color:var(--md);margin-top:4px}
.head-info-box{padding:11px 14px;background:var(--or3);border:1.5px solid var(--or5);border-radius:var(--radius-sm);font-size:13px;color:var(--or2);font-weight:600}

/* ── PROFILE VIEW ── */
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
.profile-lic-meta a{color:var(--bl);text-decoration:underline}

/* ── SCHED FORM ── */
.sched-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.sched-grid .frow{margin-bottom:0}
.sched-full{grid-column:1/-1}

/* ── AUTOCOMPLETE ── */
.autocomp{position:relative}
.autocomp-list{position:absolute;top:100%;left:0;right:0;background:var(--wh);border:1.5px solid var(--or5);border-top:none;border-radius:0 0 var(--radius-sm) var(--radius-sm);max-height:220px;overflow-y:auto;z-index:20;box-shadow:var(--shadow-md);display:none}
.autocomp-list.open{display:block}
.ac-item{padding:10px 13px;cursor:pointer;border-bottom:1px solid var(--bd);font-size:14px;transition:background .12s}
.ac-item:last-child{border-bottom:none}
.ac-item:hover,.ac-item.ac-active{background:var(--or3);color:var(--or2)}
.ac-item-name{font-weight:700;color:var(--dk)}
.ac-item-meta{font-size:12px;color:var(--md);margin-top:2px}
.ac-item:hover .ac-item-name,.ac-item:hover .ac-item-meta,
.ac-item.ac-active .ac-item-name,.ac-item.ac-active .ac-item-meta{color:var(--or2)}
.ac-empty{padding:11px 13px;font-size:13px;color:var(--md);text-align:center}
.cust-banner{padding:10px 13px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;margin-top:7px;display:none;align-items:center;gap:8px}
.cust-banner-old{background:#fff7ee;border:1.5px solid var(--or5);color:#c84b07}
.cust-banner-new{background:#dcfce7;border:1.5px solid #86efac;color:#14532d}
.new-cust-fields{display:none}
.new-cust-fields.show{display:contents}

/* ── CUSTOMER DETAIL MODAL ── */
.cd-row{display:flex;gap:10px;font-size:14px;margin-bottom:8px;flex-wrap:wrap}
.cd-label{color:var(--md);min-width:110px;flex-shrink:0;font-size:13px;font-weight:600}
.cd-value{color:var(--dk);font-weight:700;flex:1;min-width:140px;word-break:break-word}
.wash-log-tbl{width:100%;border-collapse:collapse;font-size:13px;margin-top:8px}
.wash-log-tbl th{background:var(--lt);color:var(--md);padding:8px 10px;text-align:left;font-size:12px;border-bottom:2px solid var(--bd);font-weight:700}
.wash-log-tbl td{padding:8px 10px;border-bottom:1px solid var(--bd)}
.wash-num-circle{width:24px;height:24px;background:var(--or);color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800}

/* ── ALL MEMBERS MODAL ── */
.members-modal{background:var(--wh);border-radius:var(--radius-xl);width:760px;max-width:100%;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 28px 80px rgba(0,0,0,.22);animation:slideUp .22s ease}
.mm-strip{height:5px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24,var(--or6));flex-shrink:0}
.mm-head{padding:18px 22px;background:linear-gradient(135deg,#2d2521,#1a1512);display:flex;align-items:center;gap:13px;flex-shrink:0;border-bottom:3px solid var(--or)}
.mm-icon{width:42px;height:42px;background:linear-gradient(135deg,var(--or),#ea580c);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mm-icon svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2}
.mm-title-block{flex:1;min-width:0}
.mm-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--or3);margin-bottom:2px}
.mm-name{font-size:18px;font-weight:800;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mm-sub{font-size:12px;color:#d4cdc4;margin-top:2px}
.mm-close{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.22);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff;transition:all .18s;font-weight:700}
.mm-close:hover{background:var(--or);border-color:var(--or);transform:rotate(90deg)}
.mm-body{flex:1;overflow-y:auto;padding:16px 20px;background:#fafaf8}
.mm-body::-webkit-scrollbar{width:6px}
.mm-body::-webkit-scrollbar-thumb{background:var(--or5);border-radius:3px}
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
.mm-card-role{font-size:12px;color:var(--md);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mm-empty{text-align:center;padding:36px 20px;color:var(--md);font-size:14px;grid-column:1/-1}
.mm-empty-icon{font-size:32px;margin-bottom:7px;opacity:.5}

/* ── TEAM CALENDAR ── */
.tcal-overlay{display:none;position:fixed;inset:0;z-index:700;background:rgba(26,21,18,.78);backdrop-filter:blur(10px)}
.tcal-overlay.open{display:flex;flex-direction:column;animation:fadeIn .22s ease}
.tcal-fs{background:var(--bg);width:100%;height:100%;display:flex;flex-direction:column;overflow:hidden}
.tcal-header{background:linear-gradient(135deg,#2d2521,#1a1512);border-bottom:4px solid var(--or);padding:18px 30px;display:flex;align-items:center;gap:16px;flex-shrink:0}
.tcal-icon{width:46px;height:46px;background:linear-gradient(135deg,var(--or),#ea580c);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tcal-icon svg{width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2}
.tcal-title-block{flex:1;min-width:0}
.tcal-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--or3);margin-bottom:2px}
.tcal-title{font-size:21px;font-weight:800;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
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

/* ── CAL POPUP ── */
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
.cal-ev-card:hover{border-color:var(--or5)}
.cal-ev-top{display:flex;align-items:flex-start;justify-content:space-between;gap:7px;margin-bottom:7px}
.cal-so{background:#fff0e6;color:#c84b07;border:1px solid #fdc59f;font-size:10px;font-weight:800;padding:2px 7px;border-radius:5px;font-family:'Courier New',monospace}
.cal-ev-cust{font-size:15px;font-weight:800;color:var(--dk);margin-bottom:3px;display:flex;align-items:center;gap:7px}
.cal-ev-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;background:var(--or)}
.cal-ev-job{font-size:12px;color:var(--md);margin-bottom:9px;padding-left:16px}
.cal-ev-meta{display:grid;grid-template-columns:auto 1fr;gap:4px 10px;font-size:12px}
.cal-ev-ml{color:var(--md);font-weight:600;white-space:nowrap}
.cal-ev-mv{color:var(--dk);font-weight:600}
.cal-empty{text-align:center;padding:36px 20px;color:var(--md);font-size:14px}
.cal-empty-icon{font-size:32px;margin-bottom:7px;opacity:.5}
.cal-popup-range-bar{padding:7px 17px;background:#fff7ee;border-bottom:1px solid #ffe1cc;font-size:11px;color:#c84b07;font-weight:600;flex-shrink:0;display:flex;align-items:center;gap:5px}

/* ── TIMELINE PICKER ── */
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
.tl-month-title{text-align:center;font-size:13px;font-weight:800;color:var(--dk);margin-bottom:9px;padding:3px 0}
.tl-grid-wrap{padding:0 0 6px;overflow-x:auto}
.tl-dhdrs{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-bottom:5px}
.tl-dhdr{font-size:11px;font-weight:700;color:var(--md);text-align:center;padding:3px 0}
.tl-dhdr.weekend{color:var(--rd)}
.tl-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;user-select:none}
.tl-cell{position:relative;aspect-ratio:1;min-height:40px;border:1.5px solid var(--bd);border-radius:7px;background:var(--wh);cursor:pointer;transition:all .12s;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden}
.tl-cell:hover{border-color:var(--or5);transform:translateY(-1px);box-shadow:0 2px 6px rgba(249,115,22,.15)}
.tl-cell.tl-other{opacity:.35;cursor:default;pointer-events:none}
.tl-cell.tl-today{border-color:var(--or);border-width:2px}
.tl-cell.tl-weekend{background:#fafaf9}
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

/* ── RESPONSIVE ── */
@media(max-width:768px){
  .main{padding:14px 16px}
  .nav-inner{padding:0 16px;height:62px}
  .nav-title{font-size:15px}
  .nav-tab span{display:none}
  .team-grid{grid-template-columns:1fr}
  .fgrid,.sched-grid,.resume-fields,.igrid,.profile-comp-grid,.comp-grid,.skill-grid,.sw-grid{grid-template-columns:1fr}
  .cal-months,.tl-months{grid-template-columns:1fr}
  .cal-block:first-child,.tl-month-block:first-child{border-right:none;border-bottom:1px solid var(--bd)}
  .tcal-header{padding:13px 16px}
  .tcal-title{font-size:17px}
  .tl-cell{min-height:35px}
}
@media(max-width:480px){
  .skill-grid,.sw-grid{grid-template-columns:1fr 1fr}
  .comp-grid{grid-template-columns:1fr}
  .pmodal{max-height:96vh}
}
</style>
</head>
<body>

{{-- ══════════════════════════════════
     NAV
══════════════════════════════════ --}}
<nav>
  <div class="nav-inner">
    <div class="nav-logo">
      <div class="nav-mark">3E</div>
      <div>
        <div class="nav-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
        <div class="nav-sub" style="display:none" id="nav-sub-text">ระบบจัดการทีมช่าง</div>
      </div>
    </div>
    <div class="nav-tabs">
      <button class="nav-tab active" id="tab-btn-teams" onclick="switchTab('teams',this)">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>ทีมช่าง</span>
        <span class="nav-badge-count">{{ $teams->count() }}</span>
      </button>
      <button class="nav-tab" id="tab-btn-schedules" onclick="switchTab('schedules',this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>ตารางงาน</span>
        <span class="nav-badge-count">{{ $schedules->count() }}</span>
      </button>
      <button class="nav-tab" id="tab-btn-customers" onclick="switchTab('customers',this)">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>ลูกค้า PROJECT</span>
        <span class="nav-badge-count">{{ $customers->count() }}</span>
      </button>
      <button class="nav-tab" id="tab-btn-accounts" onclick="switchTab('accounts',this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span>บัญชีผู้ใช้ Solar</span>
        <span class="nav-badge-count">{{ $accounts->count() }}</span>
      </button>
    </div>
    <div class="nav-end">
      <button class="btn btn-primary btn-sm" onclick="openModal('modal-tech')">+ ช่าง</button>
      <button class="btn btn-solar btn-sm" onclick="openAddSchedModal()">+ งาน</button>
      <button class="btn btn-ghost btn-sm" onclick="openCustAdd()">+ ลูกค้า</button>
    </div>
  </div>
</nav>

{{-- ══════════════════════════════════
     MAIN
══════════════════════════════════ --}}
<div class="main">

  @if(session('success'))
    <div class="flash flash-success" id="flash-msg">{{ session('success') }}</div>
  @endif
  @if($errors->has('delete'))
    <div class="flash flash-error">{{ $errors->first('delete') }}</div>
  @endif

  {{-- ── PANEL: ทีมช่าง ── --}}
  <div class="panel active" id="panel-teams">
    <div class="panel-header">
      <div class="panel-title">ทีมช่าง ({{ $teams->count() }} ทีม · {{ $stats['total_tech'] }} คน)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" id="tech-search" placeholder="🔍 ค้นหาช่าง..." oninput="filterTeams(this.value)">
      </div>
    </div>

    @if($teams->count() === 0)
      <div class="empty-state">ยังไม่มีทีมช่างในระบบ</div>
    @else
      <div class="team-grid" id="team-grid-wrap">
        @foreach($teams as $team)
          @php
            $members = $technicians->where('emp_team', $team['team_name']);
            $head    = $members->firstWhere('emp_position','หัวหน้าทีม');
            $others  = $members->where('emp_position','!=','หัวหน้าทีม');
            $allMbr  = collect(); if($head) $allMbr->push($head); foreach($others as $o) $allMbr->push($o);
            $total   = $allMbr->count();
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
                         onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect width=\'64\' height=\'64\' fill=\'%23fed7aa\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-weight=\'bold\' font-size=\'16\' fill=\'%23f97316\'%3E3E%3C/text%3E%3C/svg%3E'">
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
                <button type="button" class="view-all-btn" onclick='openTeamMembers(@json($team["team_name"]))'>
                  ดูสมาชิกทั้งหมด <span class="view-all-badge">{{ $total }}</span>
                </button>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ── PANEL: ตารางงาน ── --}}
  <div class="panel" id="panel-schedules">
    <div class="panel-header">
      <div class="panel-title">ตารางงานทั้งหมด ({{ $schedules->count() }} งาน)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" id="sched-search" placeholder="🔍 ค้นหางาน..." oninput="filterTable('sched-tbody', this.value, [0,1,2,3])">
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
              <tr data-search="{{ strtolower($s->so_number.' '.$s->customer_name.' '.$s->job_title.' '.$s->team_name) }}">
                <td><span class="so-code">{{ $s->so_number }}</span></td>
                <td>
                  <strong style="font-size:14px;color:var(--dk)">{{ $s->customer_name }}</strong><br>
                  <small style="color:var(--md);font-size:13px">{{ $s->job_title }}</small>
                </td>
                <td>
                  @php $jt = $s->job_type ?? 'general'; @endphp
                  <span class="job-type-tag jt-{{ $jt }}">{{ $jobTypes[$jt] ?? $jt }}</span>
                </td>
                <td style="font-size:13px">{{ $s->job_location }}</td>
                <td><strong style="font-size:14px">{{ $s->team_name }}</strong></td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->start_date)->format('d/m/Y') }}</td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->end_date)->format('d/m/Y') }}</td>
                <td><small style="color:var(--md);font-size:13px">{{ $s->note }}</small></td>
                <td>
                  <div style="display:flex;gap:5px">
                    <button type="button" class="btn btn-sm btn-ghost"
                            data-sched="{{ json_encode($s, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
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

  {{-- ── PANEL: ลูกค้า Solar ── --}}
  <div class="panel" id="panel-customers">
    <div class="panel-header">
      <div class="panel-title">ลูกค้า PROJECT ({{ $customers->count() }} ราย)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" id="cust-search" placeholder="🔍 ค้นหาลูกค้า..." oninput="filterTable('cust-tbody', this.value, [0,1,2,3])">
        <button class="btn btn-solar" onclick="openCustAdd()">+ เพิ่มลูกค้า</button>
      </div>
    </div>
    @if($customers->count() === 0)
      <div class="empty-state">ยังไม่มีลูกค้าในระบบ</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th style="width:44px">#</th>
              <th>ชื่อลูกค้า / สถานที่</th>
              <th>ผู้ติดต่อ</th>
              <th style="width:90px">ขนาด</th>
              <th style="width:130px">ประเภทงาน</th>
              <th style="width:130px">🟠 ล้างครั้งหน้า</th>
              <th style="width:120px">สถานะ</th>
              <th style="width:140px">จัดการ</th>
            </tr>
          </thead>
          <tbody id="cust-tbody">
            @foreach($customers as $idx => $c)
              @php
                $stClass = match($c->status) {
                  'เสนอ'         => 'cst-quote',
                  'ปิดการขาย'    => 'cst-closed',
                  'กำลังติดตั้ง' => 'cst-installing',
                  'ติดตั้งสำเร็จ'=> 'cst-success',
                  default        => 'cst-other',
                };
              @endphp
              <tr data-search="{{ strtolower($c->name.' '.($c->desc ?? '').' '.($c->contact_name ?? '')) }}">
                <td style="color:#999;font-weight:600;font-size:13px">{{ $idx+1 }}</td>
                <td>
                  <button class="cust-name-btn"
                          data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                          onclick="openCustDetail(this)">{{ $c->name }}</button>
                  @if($c->desc)<div style="color:var(--md);font-size:12px;margin-top:1px">{{ $c->desc }}</div>@endif
                </td>
                <td>
                  <div style="font-weight:600;font-size:14px">{{ $c->contact_name ?: '-' }}</div>
                  @if($c->phone)<div style="color:var(--md);font-size:12px">{{ $c->phone }}</div>@endif
                </td>
                <td><span class="badge b-progress">{{ $c->size ?: '-' }}</span></td>
                <td><span style="font-size:13px">{{ $jobTypes[$c->type_project ?? ''] ?? ($c->type_project ?: '-') }}</span></td>
                <td>
                  @if($c->wash_next)
                    <span class="wash-next-tag">{{ \Carbon\Carbon::parse($c->wash_next)->format('d/m/Y') }}</span>
                  @else
                    <span class="wash-next-tag empty">-</span>
                  @endif
                </td>
                <td><span class="cust-st {{ $stClass }}">{{ $c->status ?: 'เสนอ' }}</span></td>
                <td>
                  <div style="display:flex;gap:5px">
                    <button type="button" class="btn btn-sm btn-ghost"
                            data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                            onclick="openCustEdit(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('cust.delete', $c->id) }}" onsubmit="return confirm('ลบลูกค้า {{ $c->name }} ?')">
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

  {{-- ── PANEL: บัญชีผู้ใช้ Solar ── --}}
  <div class="panel" id="panel-accounts">
    <div class="panel-header">
      <div class="panel-title">บัญชีผู้ใช้ Solar / Monitoring ({{ $accounts->count() }} บัญชี)</div>
      <div class="panel-actions">
        <input type="text" class="search-inp" id="acc-search" placeholder="🔍 ค้นหาบัญชี..." oninput="filterTable('acc-tbody', this.value, [])">
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
              <th style="width:80px">เลขที่</th>
              <th>ชื่อระบบ / แพลตฟอร์ม</th>
              <th>ลูกค้า / Inverter</th>
              <th>Username / Email</th>
              <th>Password / App PW</th>
              <th style="width:120px">จัดการ</th>
            </tr>
          </thead>
          <tbody id="acc-tbody">
            @foreach($accounts as $idx => $a)
              <tr data-search="{{ strtolower(($a->no ?? '').' '.($a->plane ?? '').' '.($a->customer ?? '').' '.($a->inverter ?? '').' '.($a->username ?? '').' '.($a->email ?? '')) }}">
                <td style="color:#999;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                <td>
                  @if($a->no)
                    <span class="so-code" style="font-size:12px">{{ $a->no }}</span>
                  @else
                    <span style="color:#ccc">—</span>
                  @endif
                </td>
                <td>
                  <div style="font-weight:700;font-size:14px;color:var(--dk)">{{ $a->plane ?: '—' }}</div>
                  @if($a->inverter)
                    <div style="font-size:12px;color:var(--md);margin-top:2px">🔌 {{ $a->inverter }}</div>
                  @endif
                </td>
                <td>
                  <div style="font-weight:600;font-size:14px">{{ $a->customer ?: '—' }}</div>
                </td>
                <td>
                  @if($a->username)
                    <div style="font-family:'Courier New',monospace;font-size:13px;font-weight:700;color:var(--bl)">{{ $a->username }}</div>
                  @endif
                  @if($a->email)
                    <div style="font-size:12px;color:var(--md);margin-top:2px">✉️ {{ $a->email }}</div>
                  @endif
                  @if(!$a->username && !$a->email)
                    <span style="color:#ccc">—</span>
                  @endif
                </td>
                <td>
                  @if($a->password)
                    <div class="acc-pw-wrap" style="display:flex;align-items:center;gap:6px">
                      <span class="acc-pw-text" data-pw="{{ $a->password }}" style="font-family:'Courier New',monospace;font-size:13px;font-weight:700;letter-spacing:.08em">••••••••</span>
                      <button type="button" class="btn btn-sm btn-ghost" style="padding:2px 8px;font-size:11px" onclick="togglePw(this)">👁</button>
                      <button type="button" class="btn btn-sm btn-ghost" style="padding:2px 8px;font-size:11px" onclick="copyPw(this,'{{ addslashes($a->password) }}')" title="คัดลอก">📋</button>
                    </div>
                  @endif
                  @if($a->app_password)
                    <div style="font-size:12px;color:var(--md);margin-top:3px">App PW: <span class="acc-pw-text" data-pw="{{ $a->app_password }}" style="font-family:'Courier New',monospace;font-weight:700">••••••••</span>
                      <button type="button" class="btn btn-sm btn-ghost" style="padding:1px 6px;font-size:10px;margin-left:3px" onclick="togglePw(this)">👁</button>
                    </div>
                  @endif
                  @if(!$a->password && !$a->app_password)
                    <span style="color:#ccc">—</span>
                  @endif
                </td>
                <td>
                  <div style="display:flex;gap:5px">
                    <button type="button" class="btn btn-sm btn-ghost"
                            data-acc="{{ json_encode($a, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                            onclick="openAccEdit(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('account.delete', $a->id) }}" onsubmit="return confirm('ลบบัญชี {{ $a->plane ?: $a->no }} ?')">
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

</div>{{-- /.main --}}

{{-- ══════════════════════════════════
     ALL MEMBERS MODAL
══════════════════════════════════ --}}
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
        <input type="text" placeholder="ค้นหาชื่อ / รหัส / ชื่อเล่น..." oninput="filterMembers(this.value)">
      </div>
      <div class="mm-grid" id="mm-grid"></div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     TEAM CALENDAR
══════════════════════════════════ --}}
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
              <div class="cal-mnav">
                <button class="cal-mnav-btn" id="cal-prev">&#8249;</button>
                <div class="cal-mname" id="cal-left-name"></div>
                <button class="cal-mnav-btn invisible">&#8250;</button>
              </div>
              <div class="cal-dhdrs" id="cal-left-hdrs"></div>
              <div class="cal-dgrid" id="cal-left-grid"></div>
            </div>
            <div class="cal-block">
              <div class="cal-mnav">
                <button class="cal-mnav-btn invisible">&#8249;</button>
                <div class="cal-mname" id="cal-right-name"></div>
                <button class="cal-mnav-btn" id="cal-next">&#8250;</button>
              </div>
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

{{-- cal day popup --}}
<div class="cal-popup-bg" id="cal-popup-bg" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="cal-popup">
    <div class="cal-popup-strip"></div>
    <div class="cal-popup-head">
      <div class="cal-popup-date" id="cal-popup-date"></div>
      <div class="cal-popup-count" id="cal-popup-count" style="display:none"></div>
      <button class="cal-popup-close" onclick="document.getElementById('cal-popup-bg').classList.remove('open')">✕</button>
    </div>
    <div class="cal-popup-range-bar" id="cal-popup-range-bar" style="display:none">
      <span id="cal-popup-range-text"></span>
    </div>
    <div class="cal-popup-body"><div class="cal-popup-inner" id="cal-popup-inner"></div></div>
  </div>
</div>

{{-- ══════════════════════════════════
     PROFILE MODAL
══════════════════════════════════ --}}
<div class="overlay" id="overlay" onclick="if(event.target===this)this.classList.remove('open')">
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

{{-- ══════════════════════════════════
     เพิ่มช่าง MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-tech" onclick="if(event.target===this)this.classList.remove('open')">
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
      <form method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data" id="form-add-tech">
        @csrf
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative">
              <span class="resume-badge-abs">PHOTO</span>
              <div class="photo-box" onclick="document.getElementById('add-img-input').click()">
                <img id="add-img-preview" class="resume-img" src="" alt="">
                <div class="photo-overlay"><span>เปลี่ยน<br>รูป</span></div>
                <div class="photo-placeholder" id="add-img-ph">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <span>อัปโหลด<br>รูปภาพ</span>
                </div>
              </div>
            </div>
            <div class="photo-label">รูปประจำตัว</div>
            <div style="font-size:10px;color:var(--md);text-align:center">JPG/PNG 3:4</div>
            <input type="file" id="add-img-input" name="img" class="hidden-file" accept="image/*" onchange="resumePreview(this,'add')">
          </div>
          <div class="resume-fields">
            <div class="frow">
              <label class="flabel">รหัสพนักงาน *</label>
              <input class="finput" type="text" name="emp_id" value="{{ old('emp_id') }}" required placeholder="3E-001">
              <div class="emp-id-note">ตัวอักษร, ตัวเลข, -, _</div>
            </div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (ไทย)</label><input class="finput" type="text" name="emp_name" id="add-emp_name" value="{{ old('emp_name') }}" placeholder="ชื่อ นามสกุล"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (Eng)</label><input class="finput" type="text" name="emp_name_eng" value="{{ old('emp_name_eng') }}" placeholder="First Last"></div>
            <div class="frow"><label class="flabel">ชื่อเล่น</label><input class="finput" type="text" name="emp_nickname" value="{{ old('emp_nickname') }}" placeholder="ชื่อเล่น"></div>
            <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="emp_phone" value="{{ old('emp_phone') }}" placeholder="0xx-xxx-xxxx"></div>
            <div class="frow">
              <label class="flabel">วันเกิด</label>
              <div class="dob-row">
                <input class="finput" type="date" name="date_of_birth" id="add-dob" value="{{ old('date_of_birth') }}" onchange="updateBE('add')">
                <span class="dob-be" id="add-dob-be">พ.ศ. -</span>
              </div>
            </div>
            <div class="frow">
              <label class="flabel">ตำแหน่ง</label>
              <select class="finput" name="emp_position" id="add-emp_position" onchange="handlePositionChange('add')">
                <option value="">-- เลือกตำแหน่ง --</option>
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
          @php $oldSkills = old('emp_skill', []); if(is_string($oldSkills)) $oldSkills=array_filter(array_map('trim',explode(',',$oldSkills))); $oldSkills=is_array($oldSkills)?$oldSkills:[]; @endphp
          <div class="skill-grid">
            @foreach($skillOptions as $sk)
              <label class="skill-check {{ in_array($sk,$oldSkills)?'checked':'' }}">
                <input type="checkbox" name="emp_skill[]" value="{{ $sk }}" {{ in_array($sk,$oldSkills)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sk }}
              </label>
            @endforeach
          </div>
          <div class="section-h">Core Competencies</div>
          @php $oldComp=old('core_competencies',[]); @endphp
          <div class="comp-grid">
            @foreach($competencyList as $c)
              @php $val=$oldComp[$c['key']]??'none'; @endphp
              <div class="comp-card">
                <div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div>
                <select class="comp-select lv-{{ $val }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">
                  @foreach($competencyLevels as $lv=>$lvL)<option value="{{ $lv }}" {{ $val===$lv?'selected':'' }}>{{ $lvL }}</option>@endforeach
                </select>
              </div>
            @endforeach
          </div>
          <div class="section-h">Licenses &amp; Experience</div>
          <div class="lic-list" id="add-lic-list"></div>
          <button type="button" class="btn-add-lic" onclick="addLicense('add')">+ เพิ่มใบรับรอง</button>
          <div class="section-h">Software &amp; Tools</div>
          @php $oldSw=old('software_tools',[]); if(!is_array($oldSw)) $oldSw=[]; @endphp
          <div class="sw-grid">
            @foreach($softwareOptions as $sw)
              <label class="skill-check {{ in_array($sw,$oldSw)?'checked':'' }}">
                <input type="checkbox" name="software_tools[]" value="{{ $sw }}" {{ in_array($sw,$oldSw)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sw }}
              </label>
            @endforeach
          </div>
          <div class="sw-custom-row">
            <input type="text" class="finput" id="add-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('add')}">
            <button type="button" class="btn-other" onclick="addCustomSw('add')">+ เพิ่ม</button>
          </div>
          <div class="sw-custom-tags" id="add-sw-custom-tags">
            @foreach($oldSw as $sw)@if(!in_array($sw,$softwareOptions,true))<span class="sw-tag"><input type="hidden" name="software_tools[]" value="{{ $sw }}">{{ $sw }}<span class="x" onclick="this.parentElement.remove()">✕</span></span>@endif
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

{{-- ══════════════════════════════════
     แก้ไขช่าง MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-edit-tech" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header">
      <div class="modal-title">แก้ไขข้อมูลช่าง</div>
      <div class="modal-close" onclick="closeModalById('modal-edit-tech')">✕</div>
    </div>
    <div class="modal-body" style="padding:0">
      @if($errors->any() && old('_edit_tech'))
        <div class="ferr" style="margin:16px 22px 0">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-tech" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_edit_tech" value="1">
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative">
              <span class="resume-badge-abs">PHOTO</span>
              <div class="photo-box" onclick="document.getElementById('et-img-input').click()">
                <img id="et-img-preview" class="resume-img" src="" alt="">
                <div class="photo-overlay"><span>เปลี่ยน<br>รูป</span></div>
                <div class="photo-placeholder" id="et-img-ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span>คลิกเปลี่ยน<br>รูป</span></div>
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
            <div class="frow">
              <label class="flabel">วันเกิด</label>
              <div class="dob-row">
                <input class="finput" type="date" name="date_of_birth" id="et-dob" onchange="updateBE('et')">
                <span class="dob-be" id="et-dob-be">พ.ศ. -</span>
              </div>
            </div>
            <div class="frow">
              <label class="flabel">ตำแหน่ง</label>
              <select class="finput" name="emp_position" id="et-emp_position" onchange="handlePositionChange('et')">
                <option value="">-- เลือกตำแหน่ง --</option>
                <option value="ลูกทีม">ลูกทีม</option>
                <option value="หัวหน้าทีม">หัวหน้าทีม</option>
              </select>
            </div>
            <div class="frow" id="et-team-wrap">
              <label class="flabel">ทีม</label>
              <select class="finput" name="emp_team" id="et-team-select">
                <option value="">-- เลือกทีม --</option>
                @foreach($availableTeams as $tn)<option value="{{ $tn }}">{{ $tn }}</option>@endforeach
              </select>
            </div>
            <div class="frow" style="grid-column:1/-1">
              <label class="flabel">สถานะ</label>
              <select class="finput" name="status" id="et-status">
                <option value="active">พร้อมทำงาน</option>
                <option value="leave">ลาออก</option>
              </select>
            </div>
          </div>
        </div>
        <div style="padding:12px 22px 0">
          <div id="et-head-info" style="display:none"><div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> อัตโนมัติ</div></div>
        </div>
        <div style="padding:0 22px 20px">
          <div class="section-h">ทักษะ</div>
          <div class="skill-grid" id="et-skill-grid">
            @foreach($skillOptions as $sk)
              <label class="skill-check" data-skill="{{ $sk }}">
                <input type="checkbox" name="emp_skill[]" value="{{ $sk }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sk }}
              </label>
            @endforeach
          </div>
          <div class="section-h">Core Competencies</div>
          <div class="comp-grid" id="et-comp-grid">
            @foreach($competencyList as $c)
              <div class="comp-card">
                <div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div>
                <select class="comp-select lv-none" data-comp="{{ $c['key'] }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">
                  @foreach($competencyLevels as $lv=>$lvL)<option value="{{ $lv }}">{{ $lvL }}</option>@endforeach
                </select>
              </div>
            @endforeach
          </div>
          <div class="section-h">Licenses &amp; Experience</div>
          <div class="lic-list" id="et-lic-list"></div>
          <button type="button" class="btn-add-lic" onclick="addLicense('et')">+ เพิ่มใบรับรอง</button>
          <div class="section-h">Software &amp; Tools</div>
          <div class="sw-grid" id="et-sw-grid">
            @foreach($softwareOptions as $sw)
              <label class="skill-check" data-sw="{{ $sw }}">
                <input type="checkbox" name="software_tools[]" value="{{ $sw }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">{{ $sw }}
              </label>
            @endforeach
          </div>
          <div class="sw-custom-row">
            <input type="text" class="finput" id="et-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('et')}">
            <button type="button" class="btn-other" onclick="addCustomSw('et')">+ เพิ่ม</button>
          </div>
          <div class="sw-custom-tags" id="et-sw-custom-tags"></div>
          <div class="factions">
            <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-tech')">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     เพิ่มงาน MODAL (รวม autocomplete + ประเภทงาน)
══════════════════════════════════ --}}
<div class="overlay" id="modal-sched" onclick="if(event.target===this)closeModalById('modal-sched')">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header">
      <div class="modal-title">เพิ่มงานใหม่</div>
      <div class="modal-close" onclick="closeModalById('modal-sched')">✕</div>
    </div>
    <div class="modal-body">
      @if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('sched.store') }}" id="form-add-sched">
        @csrf
        <input type="hidden" name="customer_id" id="add-customer_id" value="">
        <div class="sched-grid">
          <div class="frow">
            <label class="flabel">ประเภทงาน *</label>
            <select class="finput" name="job_type" id="add-job_type" required>
              <option value="">-- เลือกประเภท --</option>
              @foreach($jobTypes as $key => $label)
                <option value="{{ $key }}" {{ old('job_type')===$key?'selected':'' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="frow">
            <label class="flabel">เลข SO *</label>
            <input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-2025-001">
          </div>

          <div class="frow sched-full autocomp" style="position:relative">
            <label class="flabel">ชื่อลูกค้า * — พิมพ์เพื่อค้น หรือกรอกใหม่ = ลูกค้าใหม่</label>
            <input class="finput" type="text" name="customer_name" id="add-customer_name"
                   value="{{ old('customer_name') }}" required autocomplete="off"
                   placeholder="พิมพ์ชื่อลูกค้า..."
                   oninput="custAutocomp(this.value,'add')"
                   onkeydown="custAutocompKey(event,'add')">
            <div class="autocomp-list" id="add-ac-list"></div>
            <div class="cust-banner cust-banner-old" id="add-cust-banner"></div>
          </div>

          {{-- ฟิลด์ลูกค้าใหม่ (โผล่เมื่อไม่เจอในระบบ) --}}
          <div class="frow" id="add-ncf-1" style="display:none"><label class="flabel">รายละเอียดลูกค้า</label><input class="finput" type="text" name="cust_desc" placeholder="โรงงาน / หมู่บ้าน"></div>
          <div class="frow" id="add-ncf-2" style="display:none"><label class="flabel">ชื่อผู้ติดต่อ</label><input class="finput" type="text" name="cust_contact_name"></div>
          <div class="frow" id="add-ncf-3" style="display:none"><label class="flabel">เบอร์โทรลูกค้า</label><input class="finput" type="text" name="cust_phone"></div>
          <div class="frow" id="add-ncf-4" style="display:none"><label class="flabel">ขนาดติดตั้ง</label><input class="finput" type="text" name="cust_size" placeholder="เช่น 10kW"></div>

          <div class="frow">
            <label class="flabel">ทีมที่รับผิดชอบ *</label>
            <select class="finput" name="team_name" id="add-team_name" required onchange="TL.onTeamChange('add')">
              <option value="">-- เลือกทีม --</option>
              @foreach($teams as $t)
                <option value="{{ $t['team_name'] }}" {{ old('team_name')===$t['team_name']?'selected':'' }}>{{ $t['team_name'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="frow">
            <label class="flabel">ชื่องาน *</label>
            <input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required>
          </div>
          <div class="frow">
            <label class="flabel">สถานที่</label>
            <input class="finput" type="text" name="job_location" id="add-job_location" value="{{ old('job_location') }}">
          </div>
          <div class="frow">
            <label class="flabel">ละติจูด,ลองจิจูด</label>
            <input class="finput" type="text" name="job_la_long" id="add-job_la_long" value="{{ old('job_la_long') }}" placeholder="13.7563, 100.5018">
          </div>

          <div class="frow sched-full">
            <label class="flabel">ช่วงวันที่ทำงาน * (กดและลากเพื่อเลือก)</label>
            <div class="tl-wrap" id="add-tl-wrap">
              <div class="tl-header">
                <button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="add">&#8249;</button>
                <div class="tl-mname" id="add-tl-mname"></div>
                <button type="button" class="tl-today-btn" onclick="TL.gotoToday('add')">วันนี้</button>
                <button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="add">&#8250;</button>
              </div>
              <div class="tl-team-info no-team" id="add-tl-team-info">
                <span>⚠️ เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</span>
              </div>
              <div class="tl-months">
                <div class="tl-month-block">
                  <div class="tl-month-title" id="add-tl-mname-left"></div>
                  <div class="tl-grid-wrap"><div class="tl-dhdrs" id="add-tl-dhdrs-left"></div><div class="tl-grid" id="add-tl-grid-left"></div></div>
                </div>
                <div class="tl-month-block">
                  <div class="tl-month-title" id="add-tl-mname-right"></div>
                  <div class="tl-grid-wrap"><div class="tl-dhdrs" id="add-tl-dhdrs-right"></div><div class="tl-grid" id="add-tl-grid-right"></div></div>
                </div>
              </div>
              <div class="tl-summary">
                <div class="tl-summary-info" id="add-tl-summary">กรุณาเลือกช่วงวันที่</div>
                <button type="button" class="tl-clear-btn" onclick="TL.clear('add')">ล้าง</button>
              </div>
              <div class="tl-legend">
                <div class="tl-leg"><div class="tl-leg-box today"></div>วันนี้</div>
                <div class="tl-leg"><div class="tl-leg-box busy"></div>ทีมมีงาน</div>
                <div class="tl-leg"><div class="tl-leg-box sel"></div>วันเริ่ม/สิ้นสุด</div>
                <div class="tl-leg"><div class="tl-leg-box range"></div>ช่วงเลือก</div>
              </div>
            </div>
          </div>

          <div class="frow sched-full">
            <label class="flabel">หมายเหตุ</label>
            <textarea class="finput" name="note" rows="3" style="resize:vertical">{{ old('note') }}</textarea>
          </div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกงาน</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     แก้ไขงาน MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-edit-sched" onclick="if(event.target===this)closeModalById('modal-edit-sched')">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header">
      <div class="modal-title">แก้ไขงาน</div>
      <div class="modal-close" onclick="closeModalById('modal-edit-sched')">✕</div>
    </div>
    <div class="modal-body">
      @if($errors->any() && old('_edit_sched'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-sched" action="">
        @csrf
        <input type="hidden" name="_edit_sched" value="1">
        <div class="sched-grid">
          <div class="frow">
            <label class="flabel">เลข SO *</label>
            <input class="finput" type="text" name="so_number" id="es-so_number" required>
          </div>
          <div class="frow">
            <label class="flabel">ชื่อลูกค้า *</label>
            <input class="finput" type="text" name="customer_name" id="es-customer_name" required>
          </div>
          <div class="frow">
            <label class="flabel">ประเภทงาน</label>
            <select class="finput" name="job_type" id="es-job_type">
              <option value="">-- เลือกประเภท --</option>
              @foreach($jobTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="frow">
            <label class="flabel">ชื่องาน *</label>
            <input class="finput" type="text" name="job_title" id="es-job_title" required>
          </div>
          <div class="frow">
            <label class="flabel">สถานที่</label>
            <input class="finput" type="text" name="job_location" id="es-job_location">
          </div>
          <div class="frow">
            <label class="flabel">ละติจูด,ลองจิจูด</label>
            <input class="finput" type="text" name="job_la_long" id="es-job_la_long">
          </div>
          <div class="frow">
            <label class="flabel">ทีมที่รับผิดชอบ *</label>
            <select class="finput" name="team_name" id="es-team_name" required onchange="TL.onTeamChange('es')">
              <option value="">-- เลือกทีม --</option>
              @foreach($teams as $t)
                <option value="{{ $t['team_name'] }}">{{ $t['team_name'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="frow"></div>

          <div class="frow sched-full">
            <label class="flabel">ช่วงวันที่ทำงาน *</label>
            <div class="tl-wrap" id="es-tl-wrap">
              <div class="tl-header">
                <button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="es">&#8249;</button>
                <div class="tl-mname" id="es-tl-mname"></div>
                <button type="button" class="tl-today-btn" onclick="TL.gotoToday('es')">วันนี้</button>
                <button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="es">&#8250;</button>
              </div>
              <div class="tl-team-info no-team" id="es-tl-team-info">
                <span>⚠️ เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</span>
              </div>
              <div class="tl-months">
                <div class="tl-month-block">
                  <div class="tl-month-title" id="es-tl-mname-left"></div>
                  <div class="tl-grid-wrap"><div class="tl-dhdrs" id="es-tl-dhdrs-left"></div><div class="tl-grid" id="es-tl-grid-left"></div></div>
                </div>
                <div class="tl-month-block">
                  <div class="tl-month-title" id="es-tl-mname-right"></div>
                  <div class="tl-grid-wrap"><div class="tl-dhdrs" id="es-tl-dhdrs-right"></div><div class="tl-grid" id="es-tl-grid-right"></div></div>
                </div>
              </div>
              <div class="tl-summary">
                <div class="tl-summary-info" id="es-tl-summary">กรุณาเลือกช่วงวันที่</div>
                <button type="button" class="tl-clear-btn" onclick="TL.clear('es')">ล้าง</button>
              </div>
              <div class="tl-legend">
                <div class="tl-leg"><div class="tl-leg-box today"></div>วันนี้</div>
                <div class="tl-leg"><div class="tl-leg-box busy"></div>ทีมมีงาน</div>
                <div class="tl-leg"><div class="tl-leg-box sel"></div>วันเริ่ม/สิ้นสุด</div>
                <div class="tl-leg"><div class="tl-leg-box range"></div>ช่วงเลือก</div>
              </div>
            </div>
          </div>

          <div class="frow sched-full">
            <label class="flabel">หมายเหตุ</label>
            <textarea class="finput" name="note" id="es-note" rows="3" style="resize:vertical"></textarea>
          </div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     เพิ่ม/แก้ไขลูกค้า MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-cust" onclick="if(event.target===this)closeModalById('modal-cust')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header">
      <div class="modal-title" id="cust-modal-title">เพิ่มลูกค้าใหม่</div>
      <div class="modal-close" onclick="closeModalById('modal-cust')">✕</div>
    </div>
    <div class="modal-body">
      <form method="POST" id="form-cust" action="{{ route('cust.store') }}">
        @csrf
        <div class="fgrid">
          <div class="frow fcol-full"><label class="flabel">ชื่อลูกค้า / สถานที่ *</label><input class="finput" type="text" name="name" id="cf-name" required></div>
          <div class="frow"><label class="flabel">รายละเอียด</label><input class="finput" type="text" name="desc" id="cf-desc"></div>
          <div class="frow">
            <label class="flabel">ประเภทงาน</label>
            <select class="finput" name="type_project" id="cf-type_project">
              <option value="">-- ไม่ระบุ --</option>
              @foreach($jobTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select>
          </div>
          <div class="frow"><label class="flabel">ผู้ติดต่อ</label><input class="finput" type="text" name="contact_name" id="cf-contact_name"></div>
          <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="phone" id="cf-phone"></div>
          <div class="frow"><label class="flabel">ขนาดติดตั้ง</label><input class="finput" type="text" name="size" id="cf-size" placeholder="10kW"></div>
          <div class="frow"><label class="flabel">ราคา (บาท รวม VAT)</label><input class="finput" type="number" step="0.01" name="price" id="cf-price"></div>
          <div class="frow"><label class="flabel">Lat, Long</label><input class="finput" type="text" name="loc" id="cf-loc" placeholder="13.7563, 100.5018"></div>
          <div class="frow">
            <label class="flabel">รอบล้างแผง</label>
            <select class="finput" name="wash_cycle" id="cf-wash_cycle">
              <option value="6">6 เดือน</option>
              <option value="12">12 เดือน</option>
            </select>
          </div>
          <div class="frow">
            <label class="flabel">สถานะ</label>
            <select class="finput" name="status" id="cf-status">
              <option value="เสนอ">เสนอ</option>
              <option value="ปิดการขาย">ปิดการขาย</option>
              <option value="กำลังติดตั้ง">กำลังติดตั้ง</option>
              <option value="ติดตั้งสำเร็จ">ติดตั้งสำเร็จ</option>
            </select>
          </div>
          <div class="frow fcol-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="notes" id="cf-notes" rows="3" style="resize:vertical"></textarea></div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust')">ยกเลิก</button>
          <button type="submit" class="btn btn-solar">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     ดูรายละเอียดลูกค้า MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-cust-detail" onclick="if(event.target===this)closeModalById('modal-cust-detail')">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header">
      <div class="modal-title" id="cd-name">รายละเอียดลูกค้า</div>
      <div class="modal-close" onclick="closeModalById('modal-cust-detail')">✕</div>
    </div>
    <div class="modal-body">
      <div class="fgrid">
        <div>
          <div class="ilabel" style="margin-bottom:8px">ข้อมูลทั่วไป</div>
          <div class="cd-row"><span class="cd-label">รายละเอียด</span><span class="cd-value" id="cd-desc">-</span></div>
          <div class="cd-row"><span class="cd-label">ประเภทงาน</span><span class="cd-value" id="cd-type">-</span></div>
          <div class="cd-row"><span class="cd-label">ขนาด</span><span class="cd-value" id="cd-size">-</span></div>
          <div class="cd-row"><span class="cd-label">ราคา</span><span class="cd-value" id="cd-price">-</span></div>
          <div class="cd-row"><span class="cd-label">สถานะ</span><span class="cd-value" id="cd-status">-</span></div>
        </div>
        <div>
          <div class="ilabel" style="margin-bottom:8px">ข้อมูลติดต่อ</div>
          <div class="cd-row"><span class="cd-label">ผู้ติดต่อ</span><span class="cd-value" id="cd-contact">-</span></div>
          <div class="cd-row"><span class="cd-label">เบอร์โทร</span><span class="cd-value" id="cd-phone">-</span></div>
          <div class="cd-row"><span class="cd-label">พิกัด</span><span class="cd-value" id="cd-loc">-</span></div>
          <div class="cd-row"><span class="cd-label">🟠 ล้างครั้งหน้า</span><span class="cd-value" id="cd-wash_next">-</span></div>
          <div class="cd-row"><span class="cd-label">รอบล้าง</span><span class="cd-value" id="cd-wash_cycle">-</span></div>
        </div>
      </div>

      <div style="margin-top:18px">
        <div class="ilabel" style="margin-bottom:8px">ประวัติการล้างแผง <span id="cd-wash-count" style="font-weight:500;color:var(--md)"></span></div>
        <div id="cd-wash-body"></div>
        <button type="button" class="btn-add-lic" onclick="openAddWashModal()" style="background:#e0e7ff;border-color:#a5b4fc;color:#3730a3">+ เพิ่มประวัติการล้างแผง</button>
      </div>

      <div style="margin-top:16px">
        <div class="ilabel" style="margin-bottom:6px">หมายเหตุ</div>
        <div id="cd-notes" style="font-size:14px;color:var(--dk);padding:10px 13px;background:var(--lt);border-radius:var(--radius-sm);min-height:38px">-</div>
      </div>

      <div style="margin-top:16px">
        <div class="ilabel" style="margin-bottom:8px">งานที่เกี่ยวข้อง</div>
        <div id="cd-schedules" style="font-size:13px;color:var(--md)">ยังไม่มีงานที่ผูกกับลูกค้านี้</div>
      </div>

      <div class="factions">
        <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust-detail')">ปิด</button>
        <button type="button" class="btn btn-solar" id="cd-edit-btn" onclick="editFromDetail()">✏️ แก้ไข</button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     เพิ่มการล้างแผง MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-add-wash" onclick="if(event.target===this)closeModalById('modal-add-wash')">
  <div class="pmodal" style="width:480px;max-width:100%" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),#0891b2,#06b6d4)"></div>
    <div class="modal-header">
      <div class="modal-title">เพิ่มประวัติการล้างแผง</div>
      <div class="modal-close" onclick="closeModalById('modal-add-wash')">✕</div>
    </div>
    <div class="modal-body">
      <form method="POST" id="form-add-wash" action="">
        @csrf
        <div class="frow"><label class="flabel">วันที่ล้าง *</label><input class="finput" type="date" name="wash_date" id="aw-date" required></div>
        <div class="frow">
          <label class="flabel">ทีม / ช่างที่ล้าง *</label>
          <select class="finput" name="tech" id="aw-tech" required>
            <option value="">-- เลือก --</option>
            @foreach($teams as $t)<option value="{{ $t['team_name'] }}">{{ $t['team_name'] }}</option>@endforeach
            <option value="ช่างภายนอก">ช่างภายนอก</option>
            <option value="อื่นๆ">อื่นๆ</option>
          </select>
        </div>
        <div class="frow"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="aw-note" rows="2"></textarea></div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-wash')">ยกเลิก</button>
          <button type="submit" class="btn btn-solar">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     เพิ่ม/แก้ไขบัญชี Solar MODAL
══════════════════════════════════ --}}
<div class="overlay" id="modal-account" onclick="if(event.target===this)closeModalById('modal-account')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip" style="background:linear-gradient(90deg,var(--solar),var(--solar2),var(--solar3))"></div>
    <div class="modal-header">
      <div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้ Solar</div>
      <div class="modal-close" onclick="closeModalById('modal-account')">✕</div>
    </div>
    <div class="modal-body">
      <form method="POST" id="form-account" action="{{ route('account.store') }}">
        @csrf
        <div class="fgrid">
          <div class="frow">
            <label class="flabel">เลขที่ / รหัส</label>
            <input class="finput" type="text" name="no" id="af-no" placeholder="เช่น 001">
          </div>
          <div class="frow">
            <label class="flabel">Inverter / ยี่ห้อ</label>
            <input class="finput" type="text" name="inverter" id="af-inverter" placeholder="เช่น Huawei, Solis, Growatt">
          </div>
          <div class="frow fcol-full">
            <label class="flabel">ชื่อระบบ / แพลตฟอร์ม *</label>
            <input class="finput" type="text" name="plane" id="af-plane" placeholder="เช่น FusionSolar, SolarmanPV, iSolarCloud" required>
          </div>
          <div class="frow fcol-full">
            <label class="flabel">ลูกค้า / สถานที่ติดตั้ง</label>
            <div class="autocomp" style="position:relative">
              <input class="finput" type="text" name="customer" id="af-customer"
                     placeholder="ชื่อลูกค้าหรือสถานที่..." autocomplete="off"
                     oninput="accCustAutocomp(this.value)">
              <div class="autocomp-list" id="af-cust-list"></div>
            </div>
          </div>
          <div class="frow">
            <label class="flabel">Username</label>
            <input class="finput" type="text" name="username" id="af-username" autocomplete="off">
          </div>
          <div class="frow">
            <label class="flabel">Password</label>
            <div style="position:relative">
              <input class="finput" type="password" name="password" id="af-password" autocomplete="new-password" style="padding-right:44px">
              <button type="button" onclick="toggleInputPw('af-password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--md)">👁</button>
            </div>
          </div>
          <div class="frow">
            <label class="flabel">Email</label>
            <input class="finput" type="text" name="email" id="af-email" placeholder="email@example.com">
          </div>
          <div class="frow">
            <label class="flabel">App Password</label>
            <div style="position:relative">
              <input class="finput" type="password" name="app_password" id="af-app_password" autocomplete="new-password" style="padding-right:44px" placeholder="สำหรับ 2FA / App-specific">
              <button type="button" onclick="toggleInputPw('af-app_password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--md)">👁</button>
            </div>
          </div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-account')">ยกเลิก</button>
          <button type="submit" class="btn btn-solar">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════
     JAVASCRIPT
══════════════════════════════════ --}}
<script>
// ── Bootstrap data ──────────────────────────────────────────────────
const KNOWN_SW       = @json(collect($softwareOptions)->values());
const COMP_LIST      = @json($competencyList);
const COMP_LEVELS    = @json($competencyLevels);
const STORAGE_URL    = '{{ asset("storage") }}/';
const TECHS_DATA     = @json($technicians);
const JOB_TYPES_MAP  = @json($jobTypes);
let ALL_SCHEDULES    = @json($schedules);
const ALL_CUSTOMERS  = @json($customers);
const CSRF           = '{{ csrf_token() }}';

const URL_CUST_STORE  = "{{ url('/customers') }}";
const URL_CUST_UPDATE = (id) => `{{ url('/customers') }}/${id}/update`;
const URL_WASH_STORE  = (id) => `{{ url('/customers') }}/${id}/wash`;
const URL_WASH_DEL    = (id, num) => `{{ url('/customers') }}/${id}/wash/${num}/delete`;

// ── Flash auto-hide ──────────────────────────────────────────────────
(function(){
  const el = document.getElementById('flash-msg');
  if(el) setTimeout(()=>{if(el.parentNode)el.parentNode.removeChild(el)},5000);
})();

// ── Tab switching ────────────────────────────────────────────────────
function switchTab(name, btn){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById('panel-'+name).classList.add('active');
  document.querySelectorAll('.nav-tab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
}

// ── Modal helpers ────────────────────────────────────────────────────
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModalById(id){ document.getElementById(id).classList.remove('open'); }

// ── Generic table filter ─────────────────────────────────────────────
function filterTable(tbodyId, q, cols){
  const tbody = document.getElementById(tbodyId); if(!tbody) return;
  const lq = q.toLowerCase().trim();
  tbody.querySelectorAll('tr').forEach(tr=>{
    const s = tr.dataset.search || '';
    tr.style.display = (!lq || s.includes(lq)) ? '' : 'none';
  });
}

// ── Filter team grid ─────────────────────────────────────────────────
function filterTeams(q){
  const lq = q.toLowerCase().trim();
  document.querySelectorAll('.team-card').forEach(card=>{
    if(!lq){ card.style.display=''; return; }
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(lq) ? '' : 'none';
  });
}

// ── Open add sched modal ─────────────────────────────────────────────
function openAddSchedModal(){
  TL.reset('add');
  TL.setValues('add','{{ old("start_date") }}','{{ old("end_date") }}');
  TL.onTeamChange('add',true);
  // reset autocomplete
  document.getElementById('add-customer_id').value='';
  const banner=document.getElementById('add-cust-banner');
  banner.style.display='none';
  for(let i=1;i<=4;i++){const el=document.getElementById('add-ncf-'+i);if(el)el.style.display='none';}
  openModal('modal-sched');
}

// ── Profile from element ──────────────────────────────────────────────
function openProfileFromEl(el){ try{openProfile(JSON.parse(el.dataset.tech));}catch(e){alert('เกิดข้อผิดพลาด');} }
function openEditTechFromEl(el){ try{openEditTech(JSON.parse(el.dataset.tech));}catch(e){alert('เกิดข้อผิดพลาด');} }
function openEditSchedFromEl(el){ try{openEditSched(JSON.parse(el.dataset.sched));}catch(e){alert('เกิดข้อผิดพลาด: '+e.message);} }

// ── All members modal ────────────────────────────────────────────────
let _curTeamMembers=[];
function openTeamMembers(teamName){
  const members=(TECHS_DATA||[]).filter(t=>t.emp_team===teamName);
  members.sort((a,b)=>{
    if(a.emp_position==='หัวหน้าทีม'&&b.emp_position!=='หัวหน้าทีม') return -1;
    if(b.emp_position==='หัวหน้าทีม'&&a.emp_position!=='หัวหน้าทีม') return 1;
    return (a.emp_name||a.emp_id||'').localeCompare(b.emp_name||b.emp_id||'','th');
  });
  _curTeamMembers=members;
  document.getElementById('mm-team-name').textContent=teamName;
  document.getElementById('mm-count').textContent=`สมาชิก ${members.length} คน`;
  document.querySelector('#members-overlay input').value='';
  renderMembersGrid(members);
  openModal('members-overlay');
}
function closeMembersModal(){ closeModalById('members-overlay'); }
function filterMembers(q){
  const lq=(q||'').toLowerCase().trim();
  if(!lq){renderMembersGrid(_curTeamMembers);return;}
  renderMembersGrid(_curTeamMembers.filter(m=>(m.emp_name||'').toLowerCase().includes(lq)||(m.emp_id||'').toLowerCase().includes(lq)||(m.emp_nickname||'').toLowerCase().includes(lq)));
}
function renderMembersGrid(members){
  const grid=document.getElementById('mm-grid');
  const FB="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Crect width='64' height='64' fill='%23fed7aa'/%3E%3Ctext x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-size='16' fill='%23f97316' font-weight='bold'%3E3E%3C/text%3E%3C/svg%3E";
  if(!members||!members.length){
    grid.innerHTML='<div class="mm-empty"><div class="mm-empty-icon">🔍</div>ไม่พบสมาชิก</div>'; return;
  }
  grid.innerHTML=members.map(m=>{
    const isHead=m.emp_position==='หัวหน้าทีม';
    const img=m.img?STORAGE_URL+m.img:FB;
    const tj=JSON.stringify(m).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    return `<div class="mm-card ${isHead?'is-head':''}" data-tech="${tj}" onclick="closeMembersModal();openProfileFromEl(this)">
      <div class="m-av"><img src="${img}" onerror="this.onerror=null;this.src='${FB}'"></div>
      <div class="mm-card-info">
        <div class="mm-card-name">${esc(m.emp_name||m.emp_id||'-')}${isHead?'<span class="head-tag" style="margin-left:5px">หัวหน้า</span>':''}</div>
        <div class="mm-card-role">${esc(m.emp_id||'')}${m.emp_nickname?' · '+esc(m.emp_nickname):''}</div>
      </div>
      <div class="status-dot st-${m.status}" style="flex-shrink:0"></div>
    </div>`;
  }).join('');
}

// ── Escape helpers ───────────────────────────────────────────────────
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;')}
function parseJSON(v){if(!v)return null;if(typeof v==='object')return v;try{return JSON.parse(v);}catch(e){return null;}}

// ── Dates ─────────────────────────────────────────────────────────────
function ceToBeStr(ds){
  if(!ds)return'-';const d=String(ds).substring(0,10);const p=d.split('-');if(p.length!==3)return'-';
  return `${p[2]}/${p[1]}/${parseInt(p[0],10)+543}`;
}
function fmtDTH(ds){
  if(!ds)return'-';const d=String(ds).substring(0,10).split('-');if(d.length!==3)return'-';
  const mn=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  return `${parseInt(d[2])} ${mn[parseInt(d[1])-1]} ${parseInt(d[0])+543}`;
}

// ── PHOTO upload preview ─────────────────────────────────────────────
function resumePreview(input,prefix){
  const file=input.files[0]; if(!file)return;
  const reader=new FileReader();
  reader.onload=e=>{
    const img=document.getElementById(prefix+'-img-preview');
    const ph=document.getElementById(prefix+'-img-ph');
    img.src=e.target.result;img.style.display='block';
    if(ph)ph.style.display='none';
  };reader.readAsDataURL(file);
}

// ── Position / team toggle ─────────────────────────────────────────
function handlePositionChange(p){
  const posEl=document.getElementById(p+'-emp_position');if(!posEl)return;
  const isHead=posEl.value==='หัวหน้าทีม';
  const tw=document.getElementById(p+'-team-wrap'),hi=document.getElementById(p+'-head-info'),ts=document.getElementById(p+'-team-select');
  if(isHead){if(tw)tw.style.display='none';if(hi)hi.style.display='';if(ts)ts.disabled=true;}
  else{if(tw)tw.style.display='';if(hi)hi.style.display='none';if(ts)ts.disabled=false;}
}
function attachHeadAutoFill(formId){
  const form=document.getElementById(formId);if(!form)return;
  form.addEventListener('submit',function(e){
    const posEl=form.querySelector('select[name="emp_position"]');
    if(!posEl||posEl.value!=='หัวหน้าทีม')return;
    const nameEl=form.querySelector('input[name="emp_name"]');
    const teamName=(nameEl?.value||'').trim();
    if(!teamName){e.preventDefault();alert('กรุณากรอกชื่อ-นามสกุลก่อน');nameEl?.focus();return;}
    form.querySelectorAll('input[data-auto-head]').forEach(el=>el.remove());
    const h=document.createElement('input');h.type='hidden';h.name='emp_team';h.value=teamName;h.setAttribute('data-auto-head','1');
    form.appendChild(h);
  });
}
attachHeadAutoFill('form-add-tech');
attachHeadAutoFill('form-edit-tech');

// ── Competency class ─────────────────────────────────────────────────
function updateCompClass(sel){sel.classList.remove('lv-none','lv-basic','lv-skill','lv-expert');sel.classList.add('lv-'+sel.value);}

// ── DOB BE display ──────────────────────────────────────────────────
function updateBE(p){
  const input=document.getElementById(p+'-dob'),label=document.getElementById(p+'-dob-be');if(!input||!label)return;
  if(input.value){const pts=input.value.split('-');if(pts.length===3){label.textContent=`พ.ศ. ${pts[2]}/${pts[1]}/${parseInt(pts[0],10)+543}`;return;}}
  label.textContent='พ.ศ. -';
}

// ── License items ───────────────────────────────────────────────────
let licCounter={add:0,et:0};
function addLicense(prefix,data){
  data=data||{};const idx=licCounter[prefix]++;
  const list=document.getElementById(prefix+'-lic-list');
  const item=document.createElement('div');item.className='lic-item';item.dataset.idx=idx;
  const fileBlock=data.file
    ?`<div class="lic-file-row"><a href="${STORAGE_URL}${data.file}" target="_blank" class="lic-file-link">ดูไฟล์เดิม</a><input type="hidden" name="licenses[${idx}][existing_file]" value="${esc(data.file)}"><input type="file" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>`
    :`<div class="lic-file-row"><input type="file" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>`;
  item.innerHTML=`<div class="lic-item-head"><span class="lic-num">#${idx+1}</span><button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button></div>
    <div class="lic-grid">
      <div><label class="flabel">ชื่อใบรับรอง</label><input class="finput" type="text" name="licenses[${idx}][title]" value="${esc(data.title||'')}" placeholder="ใบรับรองช่างไฟฟ้า"></div>
      <div><label class="flabel">เลขเอกสาร</label><input class="finput" type="text" name="licenses[${idx}][doc_no]" value="${esc(data.doc_no||'')}"></div>
      <div><label class="flabel">วันที่ได้รับ</label><input class="finput" type="text" name="licenses[${idx}][date_issued]" value="${esc(data.date_issued||'')}" placeholder="DD/MM/YYYY (พ.ศ.)"></div>
      <div><label class="flabel">ไฟล์แนบ</label>${fileBlock}</div>
    </div>`;
  list.appendChild(item);
}

// ── Custom software ─────────────────────────────────────────────────
function addCustomSw(p){
  const input=document.getElementById(p+'-sw-custom'),val=input.value.trim();if(!val)return;
  const modalId=p==='add'?'modal-tech':'modal-edit-tech';
  for(const lb of document.querySelectorAll(`#${modalId} .sw-grid .skill-check`)){
    const cb=lb.querySelector('input[type=checkbox]');
    if(cb&&cb.value===val){if(!cb.checked){cb.checked=true;lb.classList.add('checked');}input.value='';return;}
  }
  const tags=document.getElementById(p+'-sw-custom-tags');
  for(const t of tags.querySelectorAll('input[type=hidden]')){if(t.value===val){input.value='';return;}}
  const tag=document.createElement('span');tag.className='sw-tag';
  tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${esc(val)}">${esc(val)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;
  tags.appendChild(tag);input.value='';
}

// ── Open Profile ─────────────────────────────────────────────────────
function openProfile(m){
  const FB="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Crect width='160' height='160' fill='%23ffedd5'/%3E%3Ctext x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-size='40' fill='%23f97316' font-weight='bold'%3E3E%3C/text%3E%3C/svg%3E";
  const imgEl=document.getElementById('m-img');
  imgEl.src=m.img?STORAGE_URL+m.img:FB;imgEl.onerror=function(){this.onerror=null;this.src=FB;};
  document.getElementById('m-name').textContent=m.emp_name||m.emp_id||'-';
  document.getElementById('m-name-eng').textContent=m.emp_name_eng||'';
  document.getElementById('m-position').textContent=m.emp_position||'-';
  document.getElementById('m-empid').textContent=m.emp_id||'-';
  document.getElementById('m-nickname').textContent=m.emp_nickname||'-';
  document.getElementById('m-team').textContent=m.emp_team||'-';
  document.getElementById('m-phone').textContent=m.emp_phone||'-';
  const statusEl=document.getElementById('m-status');
  statusEl.textContent={active:'พร้อมทำงาน',leave:'ลาออก'}[m.status]||m.status||'-';
  statusEl.style.color=m.status==='active'?'#16a34a':'#dc2626';statusEl.style.fontWeight='700';
  document.getElementById('m-dob').textContent=ceToBeStr(m.date_of_birth);
  const skills=(m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.getElementById('m-skills').innerHTML=skills.length?skills.map(s=>`<span class="sk">${esc(s)}</span>`).join(''):'<span style="color:var(--md);font-size:13px">-</span>';
  const comps=parseJSON(m.core_competencies)||{};
  document.getElementById('m-competencies').innerHTML=COMP_LIST.map(c=>{
    const lv=comps[c.key]||'none';const lvLabel=COMP_LEVELS[lv]||'ไม่มี';
    return `<div class="pc-card lv-${lv}"><span class="pc-lbl">${c.label}</span><span class="pc-val">${lvLabel}</span></div>`;
  }).join('');
  const licEl=document.getElementById('m-licenses');
  const licenses=parseJSON(m.licenses)||[];
  licEl.innerHTML=licenses.length?licenses.map(l=>{
    const fileLink=l.file?`<a href="${STORAGE_URL}${l.file}" target="_blank">ดูไฟล์</a>`:'';
    const parts=[];if(l.doc_no)parts.push(`เลขที่: ${esc(l.doc_no)}`);if(l.date_issued)parts.push(`วันที่: ${esc(l.date_issued)}`);if(fileLink)parts.push(fileLink);
    return `<div class="profile-lic-item"><div class="profile-lic-title">${esc(l.title||'(ไม่มีชื่อ)')}</div><div class="profile-lic-meta">${parts.join(' · ')||'-'}</div></div>`;
  }).join(''):'<span style="color:var(--md);font-size:13px">ไม่มีข้อมูล</span>';
  const sw=parseJSON(m.software_tools)||[];
  document.getElementById('m-software').innerHTML=sw.length?sw.map(s=>`<span class="sk">${esc(s)}</span>`).join(''):'<span style="color:var(--md);font-size:13px">-</span>';
  openModal('overlay');
}

// ── Edit Technician ──────────────────────────────────────────────────
function openEditTech(m){
  document.getElementById('form-edit-tech').action=`/technicians/${m.emp_id}/update`;
  document.getElementById('et-emp_id').value=m.emp_id||'';
  document.getElementById('et-emp_name').value=m.emp_name||'';
  document.getElementById('et-emp_name_eng').value=m.emp_name_eng||'';
  document.getElementById('et-emp_nickname').value=m.emp_nickname||'';
  document.getElementById('et-emp_phone').value=m.emp_phone||'';
  const dob=m.date_of_birth?String(m.date_of_birth).substring(0,10):'';
  document.getElementById('et-dob').value=dob;updateBE('et');
  document.getElementById('et-emp_position').value=m.emp_position||'';
  document.getElementById('et-team-select').value=m.emp_team||'';
  document.getElementById('et-status').value=m.status||'active';
  const preview=document.getElementById('et-img-preview'),ph=document.getElementById('et-img-ph');
  if(m.img){preview.src=STORAGE_URL+m.img;preview.style.display='block';if(ph)ph.style.display='none';}
  else{preview.src='';preview.style.display='none';if(ph)ph.style.display='flex';}
  document.getElementById('et-img-input').value='';
  const skills=(m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.querySelectorAll('#et-skill-grid .skill-check').forEach(label=>{const cb=label.querySelector('input[type=checkbox]');const checked=skills.includes(label.dataset.skill);cb.checked=checked;label.classList.toggle('checked',checked);});
  const comps=parseJSON(m.core_competencies)||{};
  document.querySelectorAll('#et-comp-grid .comp-select').forEach(sel=>{const key=sel.dataset.comp;sel.value=comps[key]||'none';updateCompClass(sel);});
  licCounter.et=0;document.getElementById('et-lic-list').innerHTML='';
  (parseJSON(m.licenses)||[]).forEach(l=>addLicense('et',l));
  const sw=parseJSON(m.software_tools)||[];
  document.querySelectorAll('#et-sw-grid .skill-check').forEach(label=>{const cb=label.querySelector('input[type=checkbox]');const checked=sw.includes(label.dataset.sw);cb.checked=checked;label.classList.toggle('checked',checked);});
  const etTags=document.getElementById('et-sw-custom-tags');etTags.innerHTML='';
  sw.forEach(s=>{if(!KNOWN_SW.includes(s)){const tag=document.createElement('span');tag.className='sw-tag';tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${esc(s)}">${esc(s)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;etTags.appendChild(tag);}});
  handlePositionChange('et');openModal('modal-edit-tech');
}

// ── Edit Schedule ────────────────────────────────────────────────────
function openEditSched(s){
  window._curEditSched=s;
  window._curEditSchedId=s.id;
  document.getElementById('form-edit-sched').action=`/schedules/${s.id}/update`;
  document.getElementById('es-so_number').value=s.so_number||'';
  document.getElementById('es-customer_name').value=s.customer_name||'';
  const jtEl=document.getElementById('es-job_type');if(jtEl)jtEl.value=s.job_type||'';
  document.getElementById('es-job_title').value=s.job_title||'';
  document.getElementById('es-job_location').value=s.job_location||'';
  document.getElementById('es-job_la_long').value=s.job_la_long||'';
  document.getElementById('es-team_name').value=s.team_name||'';
  const noteEl=document.getElementById('es-note');if(noteEl)noteEl.value=s.note||'';
  const sd=s.start_date?String(s.start_date).substring(0,10):'';
  const ed=s.end_date?String(s.end_date).substring(0,10):'';
  TL.reset('es');TL.setValues('es',sd,ed);TL.onTeamChange('es',true);
  openModal('modal-edit-sched');
}
// update schedule in memory after form submit
(function(){
  const form=document.getElementById('form-edit-sched');if(!form)return;
  form.addEventListener('submit',function(){
    const cur=window._curEditSched;if(!cur)return;
    const idx=ALL_SCHEDULES.findIndex(s=>s.id==cur.id);
    if(idx>=0)ALL_SCHEDULES[idx]={...ALL_SCHEDULES[idx],
      so_number:document.getElementById('es-so_number').value,
      customer_name:document.getElementById('es-customer_name').value,
      job_title:document.getElementById('es-job_title').value,
      job_location:document.getElementById('es-job_location').value,
      job_la_long:document.getElementById('es-job_la_long').value,
      team_name:document.getElementById('es-team_name').value,
      note:document.getElementById('es-note')?.value||'',
    };
    document.querySelectorAll('.team-job-count').forEach(b=>{
      b.textContent=ALL_SCHEDULES.filter(s=>s.team_name===b.dataset.team).length;
    });
  });
})();

// ── AUTOCOMPLETE ─────────────────────────────────────────────────────
let _acIdx=-1;
function custAutocomp(query,prefix){
  const list=document.getElementById(prefix+'-ac-list');
  const banner=document.getElementById(prefix+'-cust-banner');
  const hidId=document.getElementById(prefix+'-customer_id');
  const q=(query||'').trim().toLowerCase();
  if(hidId)hidId.value='';
  banner.style.display='none';
  for(let i=1;i<=4;i++){const el=document.getElementById(prefix+'-ncf-'+i);if(el)el.style.display='none';}
  _acIdx=-1;
  if(!q){list.classList.remove('open');list.innerHTML='';return;}
  const matches=ALL_CUSTOMERS.filter(c=>(c.name||'').toLowerCase().includes(q)||(c.desc||'').toLowerCase().includes(q)||(c.contact_name||'').toLowerCase().includes(q)).slice(0,8);
  if(!matches.length){
    list.innerHTML=`<div class="ac-empty">ไม่พบลูกค้าในระบบ — จะสร้างเป็น <strong>ลูกค้าใหม่</strong></div>`;
    list.classList.add('open');
    // show new cust fields & banner
    banner.className='cust-banner cust-banner-new';
    banner.style.display='flex';
    banner.innerHTML='🆕 ลูกค้าใหม่ — ระบบจะบันทึกเข้าฐานข้อมูลอัตโนมัติ';
    for(let i=1;i<=4;i++){const el=document.getElementById(prefix+'-ncf-'+i);if(el)el.style.display='';}
    return;
  }
  list.innerHTML=matches.map((c,i)=>{
    const meta=[c.desc,c.contact_name,c.phone].filter(Boolean).join(' · ');
    return `<div class="ac-item" data-idx="${i}" onmousedown="selectCust(${JSON.stringify(c).replace(/'/g,"&#39;")})">
      <div class="ac-item-name">${esc(c.name)}</div>
      ${meta?`<div class="ac-item-meta">${esc(meta)}</div>`:''}
    </div>`;
  }).join('');
  list.classList.add('open');
}
function custAutocompKey(e,prefix){
  const list=document.getElementById(prefix+'-ac-list');
  const items=list.querySelectorAll('.ac-item');
  if(e.key==='ArrowDown'){_acIdx=Math.min(_acIdx+1,items.length-1);}
  else if(e.key==='ArrowUp'){_acIdx=Math.max(_acIdx-1,0);}
  else if(e.key==='Enter'&&_acIdx>=0){e.preventDefault();items[_acIdx]?.dispatchEvent(new MouseEvent('mousedown'));return;}
  else{return;}
  items.forEach((item,i)=>item.classList.toggle('ac-active',i===_acIdx));
  e.preventDefault();
}
function selectCust(c){
  const prefix='add';
  document.getElementById(prefix+'-customer_id').value=c.id;
  document.getElementById(prefix+'-customer_name').value=c.name;
  if(c.loc){document.getElementById(prefix+'-job_location').value=c.loc;document.getElementById(prefix+'-job_la_long').value=c.loc;}
  document.getElementById(prefix+'-ac-list').classList.remove('open');
  const banner=document.getElementById(prefix+'-cust-banner');
  banner.className='cust-banner cust-banner-old';
  banner.style.display='flex';
  const meta=[c.size?'ขนาด '+c.size:null,c.status?'สถานะ '+c.status:null].filter(Boolean).join(' · ');
  banner.innerHTML=`✓ ลูกค้าเดิม: <strong>${esc(c.name)}</strong>${meta?' · '+esc(meta):''}`;
  for(let i=1;i<=4;i++){const el=document.getElementById(prefix+'-ncf-'+i);if(el)el.style.display='none';}
}
document.addEventListener('click',function(e){
  if(!e.target.closest('.autocomp')){document.querySelectorAll('.autocomp-list').forEach(l=>l.classList.remove('open'));}
});

// ── Customer Add/Edit/Detail ─────────────────────────────────────────
let _curCustDetail=null;
function openCustAdd(){
  document.getElementById('cust-modal-title').textContent='เพิ่มลูกค้าใหม่';
  document.getElementById('form-cust').action=URL_CUST_STORE;
  ['cf-name','cf-desc','cf-contact_name','cf-phone','cf-size','cf-price','cf-loc','cf-notes'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('cf-type_project').value='';
  document.getElementById('cf-status').value='เสนอ';
  document.getElementById('cf-wash_cycle').value='6';
  openModal('modal-cust');
}
function openCustEdit(btn){
  try{
    const c=JSON.parse(btn.dataset.cust);
    document.getElementById('cust-modal-title').textContent='แก้ไขลูกค้า: '+c.name;
    document.getElementById('form-cust').action=URL_CUST_UPDATE(c.id);
    document.getElementById('cf-name').value=c.name||'';
    document.getElementById('cf-desc').value=c.desc||'';
    document.getElementById('cf-type_project').value=c.type_project||'';
    document.getElementById('cf-contact_name').value=c.contact_name||'';
    document.getElementById('cf-phone').value=c.phone||'';
    document.getElementById('cf-size').value=c.size||'';
    document.getElementById('cf-price').value=c.price||'';
    document.getElementById('cf-loc').value=c.loc||'';
    document.getElementById('cf-notes').value=c.notes||'';
    document.getElementById('cf-status').value=c.status||'เสนอ';
    document.getElementById('cf-wash_cycle').value=String(c.wash_cycle||6);
    openModal('modal-cust');
  }catch(e){alert('เกิดข้อผิดพลาด: '+e.message);}
}
function openCustDetail(btn){
  try{
    const c=JSON.parse(btn.dataset.cust);
    _curCustDetail=c;
    document.getElementById('cd-name').textContent=c.name||'-';
    document.getElementById('cd-desc').textContent=c.desc||'-';
    document.getElementById('cd-type').textContent=JOB_TYPES_MAP[c.type_project]||c.type_project||'-';
    document.getElementById('cd-size').textContent=c.size||'-';
    document.getElementById('cd-price').textContent=c.price?Number(c.price).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2})+' ฿':'-';
    document.getElementById('cd-status').textContent=c.status||'-';
    document.getElementById('cd-contact').textContent=c.contact_name||'-';
    document.getElementById('cd-phone').textContent=c.phone||'-';
    document.getElementById('cd-loc').textContent=c.loc||'-';
    document.getElementById('cd-wash_next').textContent=c.wash_next?fmtDTH(c.wash_next):'-';
    document.getElementById('cd-wash_cycle').textContent=(c.wash_cycle||6)+' เดือน';
    document.getElementById('cd-notes').textContent=c.notes||'-';
    // wash logs
    const logs=parseJSON(c.wash_logs)||[];
    document.getElementById('cd-wash-count').textContent=logs.length?`(${logs.length} ครั้ง)`:'(ยังไม่มี)';
    const washBody=document.getElementById('cd-wash-body');
    if(!logs.length){
      washBody.innerHTML='<div style="text-align:center;color:var(--md);padding:16px;font-size:13px;background:var(--lt);border-radius:var(--radius-sm)">ยังไม่มีประวัติการล้าง</div>';
    } else {
      const sorted=[...logs].sort((a,b)=>(a.num||0)-(b.num||0));
      washBody.innerHTML=`<table class="wash-log-tbl">
        <thead><tr><th style="width:40px">#</th><th style="width:110px">วันที่</th><th>ทีม/ช่าง</th><th>หมายเหตุ</th><th style="width:50px"></th></tr></thead>
        <tbody>${sorted.map(w=>`<tr>
          <td><span class="wash-num-circle">${w.num}</span></td>
          <td>${fmtDTH(w.date)}</td>
          <td style="font-weight:600">${esc(w.tech||'-')}</td>
          <td style="color:var(--md);font-size:12px">${esc(w.note||'-')}</td>
          <td><form method="POST" action="${URL_WASH_DEL(c.id,w.num)}" onsubmit="return confirm('ลบประวัติล้างครั้งที่ ${w.num} ?')" style="display:inline">
            <input type="hidden" name="_token" value="${CSRF}">
            <button type="submit" class="btn btn-sm btn-danger" style="padding:2px 7px;font-size:11px">ลบ</button>
          </form></td>
        </tr>`).join('')}</tbody></table>`;
    }
    // schedules linked
    const relScheds=ALL_SCHEDULES.filter(s=>(s.customer_name||'').toLowerCase()===(c.name||'').toLowerCase());
    const schedEl=document.getElementById('cd-schedules');
    if(!relScheds.length){
      schedEl.innerHTML='<span style="color:var(--md)">ยังไม่มีงานที่ผูกกับลูกค้านี้</span>';
    } else {
      schedEl.innerHTML=relScheds.map(s=>`<div style="display:flex;align-items:center;gap:8px;padding:7px 10px;background:var(--lt);border-radius:6px;margin-bottom:5px;font-size:13px">
        <span class="so-code" style="font-size:11px">${esc(s.so_number)}</span>
        <span style="font-weight:600">${esc(s.job_title)}</span>
        <span class="job-type-tag jt-${s.job_type||'general'}" style="font-size:11px">${JOB_TYPES_MAP[s.job_type||'general']||s.job_type||'-'}</span>
        <span style="color:var(--md);margin-left:auto">${esc(s.team_name)}</span>
      </div>`).join('');
    }
    openModal('modal-cust-detail');
  }catch(e){alert('เกิดข้อผิดพลาด: '+e.message);}
}
function editFromDetail(){
  if(!_curCustDetail)return;
  closeModalById('modal-cust-detail');
  // สร้าง fake button
  const btn=document.createElement('button');
  btn.dataset.cust=JSON.stringify(_curCustDetail);
  openCustEdit(btn);
}
function openAddWashModal(){
  if(!_curCustDetail)return;
  document.getElementById('form-add-wash').action=URL_WASH_STORE(_curCustDetail.id);
  document.getElementById('aw-date').value=new Date().toISOString().slice(0,10);
  document.getElementById('aw-tech').value='';
  document.getElementById('aw-note').value='';
  openModal('modal-add-wash');
}

// ── INIT modals from validation errors ──────────────────────────────
updateBE('add');
@if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && old('emp_id'))
  openModal('modal-tech'); handlePositionChange('add');
@elseif($errors->any() && old('_edit_tech'))
  openModal('modal-edit-tech'); handlePositionChange('et');
@endif
@if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))
  openAddSchedModal();
@elseif($errors->any() && old('_edit_sched'))
  openModal('modal-edit-sched');
@endif

/* ═══════════════════════════════════════════════════════════════
   TIMELINE PICKER
═══════════════════════════════════════════════════════════════ */
const TL=(function(){
  const MONTHS=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const DAYS=['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];
  const MSHORT=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  const state={};let navHoldTimer=null,navHoldDir=null,navHoldPrefix=null;
  function pad(n){return String(n).padStart(2,'0')}
  function iso(y,m,d){return `${y}-${pad(m+1)}-${pad(d)}`}
  function todayIso(){const t=new Date();return iso(t.getFullYear(),t.getMonth(),t.getDate())}
  function parseIso(s){const p=String(s).substring(0,10).split('-');return new Date(+p[0],+p[1]-1,+p[2])}
  function fmtBE(ds){if(!ds)return'';const p=ds.split('-');return`${parseInt(p[2])} ${MSHORT[parseInt(p[1])-1]} ${parseInt(p[0])+543}`}
  function ensure(prefix){
    if(state[prefix])return state[prefix];
    const t=new Date();
    state[prefix]={year:t.getFullYear(),month:t.getMonth(),start:null,end:null,teamName:'',isDragging:false,dragStart:null,dragEnd:null};
    return state[prefix];
  }
  function getSchedsForTeam(prefix){
    const st=ensure(prefix);if(!st.teamName)return[];
    let list=ALL_SCHEDULES.filter(s=>s.team_name===st.teamName);
    if(prefix==='es'&&window._curEditSchedId)list=list.filter(s=>s.id!=window._curEditSchedId);
    return list;
  }
  function jobsOnDate(prefix,ds){
    const cd=parseIso(ds);
    return getSchedsForTeam(prefix).filter(s=>{const sd=parseIso(s.start_date),ed=parseIso(s.end_date);return cd>=sd&&cd<=ed;});
  }
  function countInRange(prefix,lo,hi){
    if(!lo||!hi)return 0;
    const ld=parseIso(lo),hd=parseIso(hi);
    return getSchedsForTeam(prefix).filter(s=>parseIso(s.start_date)<=hd&&parseIso(s.end_date)>=ld).length;
  }
  function renderHdrs(prefix,side){
    const el=document.getElementById(prefix+'-tl-dhdrs-'+side);if(!el)return;
    el.innerHTML='';DAYS.forEach((d,i)=>{const e=document.createElement('div');e.className='tl-dhdr'+(i===0||i===6?' weekend':'');e.textContent=d;el.appendChild(e);});
  }
  function render(prefix){
    const st=ensure(prefix);
    const mnameEl=document.getElementById(prefix+'-tl-mname');
    const tiEl=document.getElementById(prefix+'-tl-team-info');
    if(!mnameEl)return;
    const lY=st.year,lM=st.month,rY=lM===11?lY+1:lY,rM=lM===11?0:lM+1;
    mnameEl.textContent=`${MONTHS[lM]} ${lY+543} — ${MONTHS[rM]} ${rY+543}`;
    if(!st.teamName){tiEl.classList.add('no-team');tiEl.innerHTML='<span>⚠️ เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</span>';}
    else{tiEl.classList.remove('no-team');tiEl.innerHTML=`<span>📋 ทีม <strong>${esc(st.teamName)}</strong> มี <strong>${getSchedsForTeam(prefix).length}</strong> งาน</span>`;}
    renderMonth(prefix,'left',lY,lM);renderMonth(prefix,'right',rY,rM);updateSummary(prefix);
  }
  function renderMonth(prefix,side,year,month){
    const st=ensure(prefix);
    const titleEl=document.getElementById(prefix+'-tl-mname-'+side);
    const gridEl=document.getElementById(prefix+'-tl-grid-'+side);
    if(!gridEl)return;
    titleEl.textContent=`${MONTHS[month]} ${year+543}`;
    renderHdrs(prefix,side);gridEl.innerHTML='';
    const tod=todayIso(),firstDow=new Date(year,month,1).getDay(),dim=new Date(year,month+1,0).getDate(),prevDim=new Date(year,month,0).getDate();
    const effS=st.isDragging?st.dragStart:st.start,effE=st.isDragging?st.dragEnd:st.end;
    for(let i=0;i<firstDow;i++){const e=document.createElement('div');e.className='tl-cell tl-other';e.innerHTML=`<div class="tl-d">${prevDim-firstDow+1+i}</div>`;gridEl.appendChild(e);}
    for(let d=1;d<=dim;d++){
      const ds=iso(year,month,d),dow=new Date(year,month,d).getDay(),jobs=jobsOnDate(prefix,ds),cnt=jobs.length;
      let cls='tl-cell';
      if(dow===0||dow===6)cls+=' tl-weekend';
      if(ds===tod)cls+=' tl-today';
      if(cnt>0)cls+=' tl-busy';
      if(ds===effS)cls+=' tl-sel-s';
      if(ds===effE)cls+=' tl-sel-e';
      if(effS&&effE&&effS!==effE){const lo=effS<effE?effS:effE,hi=effS<effE?effE:effS;if(ds>lo&&ds<hi)cls+=' tl-in-range';}
      const el=document.createElement('div');el.className=cls;el.dataset.ds=ds;
      const bar=cnt>0?'<div class="tl-busy-bar"></div>':'';
      const cnt2=cnt>1?`<div class="tl-jobs-count">${cnt}</div>`:'';
      el.innerHTML=`<div class="tl-d">${d}</div>${bar}${cnt2}`;
      el.addEventListener('mousedown',ev=>{ev.preventDefault();onDown(prefix,ds);});
      el.addEventListener('mouseenter',()=>onEnter(prefix,ds));
      el.addEventListener('touchstart',ev=>{ev.preventDefault();onDown(prefix,ds);},{passive:false});
      gridEl.appendChild(el);
    }
    const total=firstDow+dim,trail=(7-total%7)%7;
    for(let i=1;i<=trail;i++){const e=document.createElement('div');e.className='tl-cell tl-other';e.innerHTML=`<div class="tl-d">${i}</div>`;gridEl.appendChild(e);}
  }
  function updateSummary(prefix){
    const st=ensure(prefix);const sumEl=document.getElementById(prefix+'-tl-summary');if(!sumEl)return;
    if(st.start&&st.end){
      const lo=st.start<st.end?st.start:st.end,hi=st.start<st.end?st.end:st.start;
      const days=Math.round((parseIso(hi)-parseIso(lo))/864e5)+1;
      const cnt=st.teamName?countInRange(prefix,lo,hi):0;
      const warn=cnt>0?` · <span class="tl-summary-warn">⚠️ ทีมมีงาน ${cnt} งานในช่วงนี้</span>`:'';
      sumEl.innerHTML=`${fmtBE(lo)} → ${fmtBE(hi)} · <strong>${days}</strong> วัน${warn}`;
    } else if(st.start){
      sumEl.innerHTML=`เริ่ม: <strong>${fmtBE(st.start)}</strong> · เลือกวันสิ้นสุด`;
    } else {
      sumEl.textContent='กรุณาเลือกช่วงวันที่ (กดและลาก)';
    }
  }
  function onDown(prefix,ds){
    const st=ensure(prefix);st.isDragging=true;st.dragStart=ds;st.dragEnd=ds;st.start=null;st.end=null;
    window._activeDragPrefix=prefix;render(prefix);
  }
  function onEnter(prefix,ds){const st=ensure(prefix);if(!st.isDragging)return;st.dragEnd=ds;render(prefix);}
  function onUp(prefix){
    const st=ensure(prefix);if(!st.isDragging)return;st.isDragging=false;
    if(st.dragStart&&st.dragEnd){const lo=st.dragStart<st.dragEnd?st.dragStart:st.dragEnd,hi=st.dragStart<st.dragEnd?st.dragEnd:st.dragStart;st.start=lo;st.end=hi;}
    st.dragStart=null;st.dragEnd=null;syncInputs(prefix);render(prefix);
  }
  function syncInputs(prefix){
    const st=ensure(prefix);
    let si=document.getElementById(prefix+'-start_date'),ei=document.getElementById(prefix+'-end_date');
    const wrap=document.getElementById(prefix+'-tl-wrap');if(!wrap)return;
    if(!si){si=document.createElement('input');si.type='hidden';si.name='start_date';si.id=prefix+'-start_date';wrap.appendChild(si);}
    if(!ei){ei=document.createElement('input');ei.type='hidden';ei.name='end_date';ei.id=prefix+'-end_date';wrap.appendChild(ei);}
    si.value=st.start||'';ei.value=st.end||'';
  }
  function startHold(prefix,dir){if(navHoldTimer)return;navHoldDir=dir;navHoldPrefix=prefix;doNav();navHoldTimer=setInterval(doNav,600);}
  function stopHold(){if(navHoldTimer){clearInterval(navHoldTimer);navHoldTimer=null;}navHoldDir=null;navHoldPrefix=null;}
  function doNav(){
    if(!navHoldPrefix||!state[navHoldPrefix])return;const st=state[navHoldPrefix];
    if(navHoldDir==='prev'){st.month--;if(st.month<0){st.month=11;st.year--;}}
    else{st.month++;if(st.month>11){st.month=0;st.year++;}}
    render(navHoldPrefix);
  }
  document.addEventListener('click',function(ev){
    const btn=ev.target.closest('.tl-mnav-btn[data-tl-nav]');if(!btn)return;
    const prefix=btn.dataset.tlPrefix,dir=btn.dataset.tlNav;
    if(!prefix||!dir)return;if(state[prefix]&&state[prefix].isDragging)return;
    const st=ensure(prefix);
    if(dir==='prev'){st.month--;if(st.month<0){st.month=11;st.year--;}}
    else{st.month++;if(st.month>11){st.month=0;st.year++;}}
    render(prefix);
  });
  document.addEventListener('mouseup',function(){stopHold();Object.keys(state).forEach(p=>{if(state[p].isDragging)onUp(p);});window._activeDragPrefix=null;});
  document.addEventListener('touchend',function(){stopHold();Object.keys(state).forEach(p=>{if(state[p].isDragging)onUp(p);});window._activeDragPrefix=null;});
  document.addEventListener('mousemove',function(ev){
    const prefix=window._activeDragPrefix;if(!prefix||!state[prefix]||!state[prefix].isDragging)return;
    const target=document.elementFromPoint(ev.clientX,ev.clientY);if(!target||!target.closest)return;
    const navBtn=target.closest('.tl-mnav-btn[data-tl-nav]');
    if(navBtn&&navBtn.dataset.tlPrefix===prefix){
      const dir=navBtn.dataset.tlNav;if(navHoldDir!==dir||navHoldPrefix!==prefix){stopHold();startHold(prefix,dir);}return;
    }
    if(navHoldTimer)stopHold();
    const cell=target.closest('.tl-cell');if(!cell||!cell.dataset.ds)return;
    const wrapEl=cell.closest('.tl-wrap');if(!wrapEl||wrapEl.id!==prefix+'-tl-wrap')return;
    if(state[prefix].dragEnd!==cell.dataset.ds){state[prefix].dragEnd=cell.dataset.ds;render(prefix);}
  });
  document.addEventListener('touchmove',function(ev){
    const prefix=window._activeDragPrefix;if(!prefix||!state[prefix]||!state[prefix].isDragging)return;
    const t=ev.touches[0];if(!t)return;
    const target=document.elementFromPoint(t.clientX,t.clientY);if(!target||!target.closest)return;
    const navBtn=target.closest('.tl-mnav-btn[data-tl-nav]');
    if(navBtn&&navBtn.dataset.tlPrefix===prefix){
      const dir=navBtn.dataset.tlNav;if(navHoldDir!==dir||navHoldPrefix!==prefix){stopHold();startHold(prefix,dir);}return;
    }
    if(navHoldTimer)stopHold();
    const cell=target.closest('.tl-cell');if(!cell||!cell.dataset.ds)return;
    const wrapEl=cell.closest('.tl-wrap');if(!wrapEl||wrapEl.id!==prefix+'-tl-wrap')return;
    if(state[prefix].dragEnd!==cell.dataset.ds){state[prefix].dragEnd=cell.dataset.ds;render(prefix);}
  },{passive:true});
  return{
    onTeamChange(prefix,skip){const st=ensure(prefix);const sel=document.getElementById(prefix+'-team_name');if(sel)st.teamName=sel.value||'';render(prefix);},
    gotoToday(prefix){const st=ensure(prefix);const t=new Date();st.year=t.getFullYear();st.month=t.getMonth();render(prefix);},
    clear(prefix){const st=ensure(prefix);st.start=null;st.end=null;st.dragStart=null;st.dragEnd=null;st.isDragging=false;syncInputs(prefix);render(prefix);},
    reset(prefix){const t=new Date();state[prefix]={year:t.getFullYear(),month:t.getMonth(),start:null,end:null,teamName:'',isDragging:false,dragStart:null,dragEnd:null};},
    setValues(prefix,sd,ed){
      const st=ensure(prefix);st.start=sd||null;st.end=ed||null;
      if(st.start){const d=parseIso(st.start);st.year=d.getFullYear();st.month=d.getMonth();}
      else{const t=new Date();st.year=t.getFullYear();st.month=t.getMonth();}
      const sel=document.getElementById(prefix+'-team_name');if(sel)st.teamName=sel.value||'';
      syncInputs(prefix);render(prefix);
    }
  };
})();
TL.setValues('add','{{ old("start_date") }}','{{ old("end_date") }}');

/* ═══════════════════════════════════════════════════════════════
   TEAM CALENDAR
═══════════════════════════════════════════════════════════════ */
const TeamCal=(function(){
  const MONTHS=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const DAYS=['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];
  let teamScheds=[],now=new Date(),calYear=now.getFullYear(),calMonth=now.getMonth();
  let calStart=null,calEnd=null,calHover=null,attached=false;
  function pad(n){return String(n).padStart(2,'0')}
  function iso(y,m,d){return `${y}-${pad(m+1)}-${pad(d)}`}
  function todayIso(){return iso(now.getFullYear(),now.getMonth(),now.getDate())}
  function parseIso(s){const p=String(s).substring(0,10).split('-');return new Date(+p[0],+p[1]-1,+p[2])}
  function fmtTH(ds){const p=ds.split('-');const d=parseInt(p[2]),m=parseInt(p[1])-1,y=parseInt(p[0])+543;return`${DAYS[parseIso(ds).getDay()]} ${d} ${MONTHS[m]} ${y}`}
  function evOn(ds){const cd=parseIso(ds);return teamScheds.filter(s=>{const sd=parseIso(s.start_date),ed=parseIso(s.end_date);return cd>=sd&&cd<=ed;});}
  function evInRange(lo,hi){const ld=parseIso(lo),hd=parseIso(hi);return teamScheds.filter(s=>parseIso(s.start_date)<=hd&&parseIso(s.end_date)>=ld);}
  function buildMonth(year,month,gridId,hdrId,nameId){
    document.getElementById(nameId).textContent=`${MONTHS[month]} ${year+543}`;
    const hdrEl=document.getElementById(hdrId);hdrEl.innerHTML='';
    DAYS.forEach(d=>{const e=document.createElement('div');e.className='cal-dhdr';e.textContent=d;hdrEl.appendChild(e);});
    const grid=document.getElementById(gridId);grid.innerHTML='';
    const tod=todayIso(),firstDow=new Date(year,month,1).getDay(),dim=new Date(year,month+1,0).getDate(),prevDim=new Date(year,month,0).getDate();
    const effE=calEnd||calHover;
    for(let i=0;i<firstDow;i++){const e=document.createElement('div');e.className='cal-day cal-other';e.innerHTML=`<div class="cal-di">${prevDim-firstDow+1+i}</div>`;grid.appendChild(e);}
    for(let d=1;d<=dim;d++){
      const ds=iso(year,month,d),evs=evOn(ds),cnt=evs.length;
      let cls='cal-day';
      if(ds===tod)cls+=' cal-today';
      if(ds===calStart)cls+=' cal-sel-s';
      if(ds===calEnd)cls+=' cal-sel-e';
      if(cnt>0)cls+=' has-event';
      if(calStart&&effE){const lo=calStart<effE?calStart:effE,hi=calStart<effE?effE:calStart;if(ds>lo&&ds<hi)cls+=' cal-in-range';}
      const el=document.createElement('div');el.className=cls;
      let dots='';
      if(cnt>0){const show=Math.min(cnt,4);for(let i=0;i<show;i++)dots+=`<div class="cal-evdot"></div>`;if(cnt>4)dots+=`<span class="cal-evcount">+${cnt-4}</span>`;dots=`<div class="cal-evdots">${dots}</div>`;}
      el.innerHTML=`<div class="cal-di">${d}</div>${dots}`;
      el.addEventListener('click',()=>calClick(ds,evs));
      el.addEventListener('mouseenter',()=>{calHover=ds;renderAll();});
      el.addEventListener('mouseleave',()=>{calHover=null;renderAll();});
      grid.appendChild(el);
    }
    const total=firstDow+dim,trail=(7-total%7)%7;
    for(let i=1;i<=trail;i++){const e=document.createElement('div');e.className='cal-day cal-other';e.innerHTML=`<div class="cal-di">${i}</div>`;grid.appendChild(e);}
  }
  function renderAll(){
    const rY=calMonth===11?calYear+1:calYear,rM=calMonth===11?0:calMonth+1;
    buildMonth(calYear,calMonth,'cal-left-grid','cal-left-hdrs','cal-left-name');
    buildMonth(rY,rM,'cal-right-grid','cal-right-hdrs','cal-right-name');
    updateBar();updateFooter();
  }
  function calClick(ds,evs){
    if(evs&&evs.length>0){
      if(!calStart||(calStart&&calEnd)){calStart=ds;calEnd=null;}
      else if(ds===calStart){calStart=null;calEnd=null;}
      else if(ds<calStart){calEnd=calStart;calStart=ds;}
      else{calEnd=ds;}
      renderAll();showDayPopup(ds,evs);return;
    }
    if(!calStart||(calStart&&calEnd)){calStart=ds;calEnd=null;}
    else if(ds===calStart){calStart=null;calEnd=null;}
    else if(ds<calStart){calEnd=calStart;calStart=ds;}
    else{calEnd=ds;}
    renderAll();
  }
  function showDayPopup(ds,evs){
    document.getElementById('cal-popup-date').textContent=fmtTH(ds);
    const cnt=document.getElementById('cal-popup-count');cnt.textContent=`${evs.length} งาน`;cnt.style.display='';
    document.getElementById('cal-popup-range-bar').style.display='none';
    document.getElementById('cal-popup-inner').innerHTML=buildEvCards(evs);
    document.getElementById('cal-popup-bg').classList.add('open');
  }
  function buildEvCards(evs){
    if(!evs||!evs.length)return`<div class="cal-empty"><div class="cal-empty-icon">📅</div>ไม่มีงานในวันนี้</div>`;
    return evs.map(ev=>{
      const sf=String(ev.start_date).substring(0,10).split('-').reverse().join('/');
      const ef=String(ev.end_date).substring(0,10).split('-').reverse().join('/');
      const note=ev.note?`<span class="cal-ev-ml">📝 หมายเหตุ</span><span class="cal-ev-mv">${esc(ev.note)}</span>`:'';
      const jt=ev.job_type||'general';
      return `<div class="cal-ev-card">
        <div class="cal-ev-top">
          <span class="cal-so">SO: ${esc(ev.so_number||'-')}</span>
          <span class="job-type-tag jt-${jt}" style="font-size:10px">${JOB_TYPES_MAP[jt]||jt}</span>
        </div>
        <div class="cal-ev-cust"><span class="cal-ev-dot"></span>${esc(ev.customer_name||'(ไม่ระบุ)')}</div>
        <div class="cal-ev-job">${esc(ev.job_title||'-')}</div>
        <div class="cal-ev-meta">
          <span class="cal-ev-ml">📍 สถานที่</span><span class="cal-ev-mv">${esc(ev.job_location||'-')}</span>
          <span class="cal-ev-ml">👥 ทีม</span><span class="cal-ev-mv">${esc(ev.team_name||'-')}</span>
          <span class="cal-ev-ml">📅 ช่วงงาน</span><span class="cal-ev-mv">${sf} → ${ef}</span>
          ${note}
        </div>
      </div>`;
    }).join('');
  }
  function updateBar(){
    const sl=document.getElementById('cal-start-lbl'),ss=document.getElementById('cal-start-sub');
    const el=document.getElementById('cal-end-lbl'),es=document.getElementById('cal-end-sub');
    const ei=document.getElementById('cal-end-icon');
    if(calStart){sl.textContent=fmtTH(calStart).replace(/^[^ ]+ /,'');ss.textContent=calStart.split('-').reverse().join('/');}
    else{sl.textContent='เลือกวันเริ่มต้น';ss.textContent='วันที่เริ่มงาน';}
    if(calEnd){el.textContent=fmtTH(calEnd).replace(/^[^ ]+ /,'');es.textContent=calEnd.split('-').reverse().join('/');ei.classList.remove('gray');document.getElementById('cal-end-btn').classList.add('active');}
    else{el.textContent='เลือกวันสิ้นสุด';es.textContent='วันที่สิ้นสุดงาน';ei.classList.add('gray');document.getElementById('cal-end-btn').classList.remove('active');}
  }
  function updateFooter(){
    const note=document.getElementById('cal-footer-note'),resetBtn=document.getElementById('cal-reset-btn');
    if(calStart&&calEnd){
      const lo=calStart<calEnd?calStart:calEnd,hi=calStart<calEnd?calEnd:calStart;
      const days=Math.round((parseIso(hi)-parseIso(lo))/864e5)+1;
      const cnt=evInRange(lo,hi).length;
      note.textContent=`${days} วัน · พบ ${cnt} งาน`;if(resetBtn)resetBtn.style.display='';
    } else if(calStart){
      const dEvs=evOn(calStart);
      note.textContent=dEvs.length?`${fmtTH(calStart).replace(/^[^ ]+ /,'')} · ${dEvs.length} งาน`:'เลือกวันสิ้นสุดเพื่อดูงาน';
      if(resetBtn)resetBtn.style.display='';
    } else {
      note.textContent='เลือกช่วงวันที่ต้องการดูงาน';if(resetBtn)resetBtn.style.display='none';
    }
  }
  function attachListeners(){
    if(attached)return;
    document.getElementById('cal-prev').addEventListener('click',()=>{calMonth--;if(calMonth<0){calMonth=11;calYear--;}renderAll();});
    document.getElementById('cal-next').addEventListener('click',()=>{calMonth++;if(calMonth>11){calMonth=0;calYear++;}renderAll();});
    attached=true;
  }
  return{
    open(teamName){
      teamScheds=ALL_SCHEDULES.filter(s=>s.team_name===teamName);
      calStart=null;calEnd=null;calHover=null;
      const today=new Date();calYear=today.getFullYear();calMonth=today.getMonth();
      document.getElementById('tcal-team-name').textContent=teamName;
      const cnt=teamScheds.length;const cntEl=document.getElementById('tcal-job-count');
      cntEl.textContent=`${cnt} งาน`;cntEl.classList.toggle('has-jobs',cnt>0);
      attachListeners();renderAll();
      document.getElementById('tcal-overlay').classList.add('open');
      document.body.style.overflow='hidden';
    },
    close(){
      document.getElementById('tcal-overlay').classList.remove('open');
      document.getElementById('cal-popup-bg').classList.remove('open');
      document.body.style.overflow='';
    },
    reset(){calStart=null;calEnd=null;renderAll();}
  };
})();
function openTeamCalendar(teamName){TeamCal.open(teamName);}
function closeTeamCalendar(){TeamCal.close();}
function calReset(){TeamCal.reset();}

// ── Keyboard ESC ─────────────────────────────────────────────────────
document.addEventListener('keydown',function(e){
  if(e.key!=='Escape')return;
  const tc=document.getElementById('tcal-overlay');
  const pop=document.getElementById('cal-popup-bg');
  const mm=document.getElementById('members-overlay');
  if(pop.classList.contains('open'))pop.classList.remove('open');
  else if(mm.classList.contains('open'))closeMembersModal();
  else if(tc.classList.contains('open'))closeTeamCalendar();
});

// ── cd-row style ─────────────────────────────────────────────────────
const cdStyle=document.createElement('style');
cdStyle.textContent=`.cd-row{display:flex;gap:10px;font-size:14px;margin-bottom:8px;flex-wrap:wrap}.cd-label{color:var(--md);min-width:110px;flex-shrink:0;font-size:13px;font-weight:600}.cd-value{color:var(--dk);font-weight:700;flex:1;min-width:140px;word-break:break-word}`;
document.head.appendChild(cdStyle);

// ════════════════════════════════════════════════════════════════════════
//  ACCOUNTS
// ════════════════════════════════════════════════════════════════════════
const ALL_ACCOUNTS_DATA = @json($accounts);
const URL_ACC_STORE  = "{{ url('/solar-accounts/store') }}";
const URL_ACC_UPDATE = (id) => `{{ url('/solar-accounts') }}/${id}/update`;

// ── toggle show/hide password ในตาราง ─────────────────────────────
function togglePw(btn){
  const row  = btn.closest('.acc-pw-wrap') || btn.closest('div');
  const span = row.querySelector('.acc-pw-text');
  if(!span) return;
  const isHidden = span.textContent.includes('•');
  span.textContent = isHidden ? span.dataset.pw : '••••••••';
  btn.textContent  = isHidden ? '🙈' : '👁';
}

function copyPw(btn, pw){
  navigator.clipboard.writeText(pw).then(()=>{
    btn.textContent = '✓';
    setTimeout(()=>{ btn.textContent = '📋'; }, 1500);
  }).catch(()=>{
    // fallback
    const ta = document.createElement('textarea');
    ta.value = pw; document.body.appendChild(ta); ta.select();
    document.execCommand('copy'); document.body.removeChild(ta);
    btn.textContent = '✓';
    setTimeout(()=>{ btn.textContent = '📋'; }, 1500);
  });
}

// ── toggle show/hide password ใน input ────────────────────────────
function toggleInputPw(inputId, btn){
  const inp = document.getElementById(inputId);
  if(!inp) return;
  const isHidden = inp.type === 'password';
  inp.type = isHidden ? 'text' : 'password';
  btn.textContent = isHidden ? '🙈' : '👁';
}

// ── Autocomplete ลูกค้าใน modal account ──────────────────────────
function accCustAutocomp(query){
  const list = document.getElementById('af-cust-list');
  const q = (query||'').trim().toLowerCase();
  if(!q){ list.classList.remove('open'); list.innerHTML=''; return; }

  const matches = ALL_CUSTOMERS.filter(c =>
    (c.name||'').toLowerCase().includes(q)||
    (c.desc||'').toLowerCase().includes(q)
  ).slice(0, 6);

  if(!matches.length){ list.classList.remove('open'); return; }

  list.innerHTML = matches.map(c =>
    `<div class="ac-item" onmousedown="document.getElementById('af-customer').value='${esc(c.name)}';document.getElementById('af-cust-list').classList.remove('open')">
      <div class="ac-item-name">${esc(c.name)}</div>
      ${c.desc ? `<div class="ac-item-meta">${esc(c.desc)}</div>` : ''}
    </div>`
  ).join('');
  list.classList.add('open');
}

// ── Open Add Account ──────────────────────────────────────────────
function openAccAdd(){
  document.getElementById('acc-modal-title').textContent = 'เพิ่มบัญชีผู้ใช้ Solar';
  document.getElementById('form-account').action = URL_ACC_STORE;
  ['af-no','af-inverter','af-plane','af-customer','af-username','af-password','af-email','af-app_password']
    .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
  // reset password visibility
  ['af-password','af-app_password'].forEach(id => {
    const el = document.getElementById(id); if(el) el.type = 'password';
  });
  openModal('modal-account');
}

// ── Open Edit Account ─────────────────────────────────────────────
function openAccEdit(btn){
  try {
    const a = JSON.parse(btn.dataset.acc);
    document.getElementById('acc-modal-title').textContent = 'แก้ไขบัญชี: ' + (a.plane || a.no || '');
    document.getElementById('form-account').action = URL_ACC_UPDATE(a.id);

    const fields = {
      'af-no': a.no, 'af-inverter': a.inverter, 'af-plane': a.plane,
      'af-customer': a.customer, 'af-username': a.username,
      'af-password': a.password, 'af-email': a.email,
      'af-app_password': a.app_password,
    };
    Object.entries(fields).forEach(([id, val]) => {
      const el = document.getElementById(id);
      if(el) el.value = val || '';
    });

    // reset password visibility
    ['af-password','af-app_password'].forEach(id => {
      const el = document.getElementById(id); if(el) el.type = 'password';
    });

    openModal('modal-account');
  } catch(e){ alert('เกิดข้อผิดพลาด: ' + e.message); }
}
</script>
</body>
</html>
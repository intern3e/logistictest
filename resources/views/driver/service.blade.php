{{-- resources/views/driver/service.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบจัดการเซอร์วิสรถ</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════════════════
   RESET + DESIGN TOKENS — เดียวกับหน้า oil ทั้งหมด
═══════════════════════════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box}
html,body{margin:0;padding:0}
body{
  font-family:'IBM Plex Sans Thai','Inter',system-ui,-apple-system,sans-serif;
  background:#f4f6fb;
  color:#0f172a;
  -webkit-font-smoothing:antialiased;
  min-height:100vh;
}

:root{
  --bg:#f4f6fb;
  --surface:#ffffff;
  --surface2:#f1f5f9;
  --surface3:#f8fafc;
  --border:#e5e7eb;
  --border2:#e2e8f0;
  --text:#0f172a;
  --text2:#475569;
  --text3:#94a3b8;
  --text4:#cbd5e1;
  --primary:#16a34a;
  --primary-dark:#15803d;
  --primary-light:rgba(22,163,74,.1);
  --accent:#2563eb;
  --amber:#f59e0b;
  --red:#dc2626;
  --green:#16a34a;
  --purple:#9333ea;
  --shadow-sm:0 1px 2px rgba(15,23,42,.04);
  --shadow:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
  --shadow-md:0 4px 12px rgba(15,23,42,.08);
  --shadow-lg:0 10px 25px rgba(15,23,42,.1);
  --radius:10px;
  --radius-xs:8px;
  --radius-lg:14px;
  --nav-h:60px;
}

/* ═══════════════════════════════════════════════════════════════
   TOP NAVBAR — เหมือนหน้า oil ทุกอย่าง
═══════════════════════════════════════════════════════════════ */
.topnav{
  position:sticky;top:0;z-index:50;
  background:#fff;
  border-bottom:1px solid var(--border);
  box-shadow:var(--shadow-sm);
}
.topnav-main{
  display:flex;align-items:center;gap:18px;
  padding:0 24px;height:var(--nav-h);
  max-width:1600px;margin:0 auto;
}
.topnav-brand{display:flex;align-items:center;gap:10px;flex-shrink:0}
.topnav-brand .logo{
  font-size:18px;width:36px;height:36px;
  display:flex;align-items:center;justify-content:center;
  background:#0f172a;
  color:#fff;border-radius:9px;
}
.topnav-brand .title-text{
  font-size:15.5px;font-weight:700;color:var(--text);letter-spacing:-.01em;
}

.topnav-toggle{
  display:none;width:38px;height:38px;
  background:var(--surface2);border:1px solid var(--border);border-radius:9px;
  cursor:pointer;color:var(--text2);font-size:16px;
  align-items:center;justify-content:center;
}

.topnav-menu{display:flex;align-items:center;gap:2px}
.nav-item{
  display:inline-flex;align-items:center;gap:8px;
  padding:8px 13px;border-radius:8px;
  font-size:13.5px;font-weight:500;color:var(--text2);
  text-decoration:none;background:transparent;border:none;cursor:pointer;font-family:inherit;
  transition:all .15s;
}
.nav-item:hover{background:var(--surface2);color:var(--text)}
.nav-item.active{background:#f1f5f9;color:#0f172a;font-weight:600}
.nav-item .ic{
  display:inline-flex;align-items:center;justify-content:center;
  width:18px;height:18px;opacity:.85;flex-shrink:0;
}
.nav-item .ic svg{display:block}
.nav-item.active .ic{opacity:1}

.topnav-spacer{flex:1}

.topnav-right{display:flex;align-items:center;gap:10px}
.topnav-time{
  display:inline-flex;align-items:center;gap:7px;
  padding:7px 12px;border-radius:8px;
  background:var(--surface2);
  font-size:13px;font-weight:500;color:var(--text2);
  font-family:'Inter',sans-serif;letter-spacing:.01em;
}
.topnav-time .pulse{
  width:7px;height:7px;border-radius:50%;background:#64748b;
  box-shadow:0 0 0 3px rgba(100,116,139,.15);animation:pulse 1.5s infinite;
}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

/* ═══════════════════════════════════════════════════════════════
   FILTER STRIP — แถวฟิลเตอร์ใต้ navbar (เหมือน oil)
═══════════════════════════════════════════════════════════════ */
.topnav-filters{
  background:#fff;
  border-bottom:1px solid var(--border);
  padding:10px 24px;
  display:flex;align-items:center;gap:14px;flex-wrap:wrap;
  max-width:1600px;margin:0 auto;
}
.filter-group{display:flex;align-items:center;gap:8px}
.filter-group-label{
  font-size:12.5px;font-weight:500;color:var(--text3);
  white-space:nowrap;
}
.segmented{
  display:inline-flex;align-items:center;gap:2px;
  background:var(--surface2);border-radius:9px;padding:3px;
}
.seg-btn{
  padding:6px 14px;border-radius:7px;
  font-size:13px;font-weight:500;color:var(--text2);
  background:transparent;border:none;cursor:pointer;font-family:inherit;
  transition:all .15s;
}
.seg-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 2px rgba(15,23,42,.08)}
.seg-btn:hover:not(.active){color:var(--text)}

.fs-divider{width:1px;height:22px;background:var(--border);margin:0 2px}

.flt-input{
  padding:7px 12px;border:1px solid var(--border);border-radius:8px;
  background:#fff;font-family:inherit;font-size:13px;color:var(--text);
  outline:none;height:36px;
  transition:border-color .15s, box-shadow .15s;
}
.flt-input:focus{border-color:#0f172a;box-shadow:0 0 0 3px rgba(15,23,42,.08)}
.flt-input:hover:not(:focus){border-color:var(--text4)}

select.flt-input{
  cursor:pointer;min-width:140px;padding-right:30px;
  appearance:none;-webkit-appearance:none;
  background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
  background-repeat:no-repeat;background-position:right 10px center;background-size:12px;
}

.flt-spacer{flex:1}
.flt-info{
  font-size:12.5px;color:var(--text3);font-weight:500;
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
}
.flt-info strong{color:var(--text);font-weight:700}

/* Mobile */
@media(max-width:900px){
  .topnav-main{padding:0 14px;gap:10px}
  .topnav-toggle{display:inline-flex}
  .topnav-menu{
    position:absolute;top:var(--nav-h);left:0;right:0;
    background:#fff;border-bottom:1px solid var(--border);
    flex-direction:column;align-items:stretch;gap:0;padding:8px;
    display:none;box-shadow:var(--shadow-md);
  }
  .topnav-menu.open{display:flex}
  .nav-item{justify-content:flex-start;padding:11px 14px}
  .topnav-filters{padding:10px 14px;overflow-x:auto;flex-wrap:nowrap}
  .topnav-filters::-webkit-scrollbar{height:4px}
  .filter-group{flex-shrink:0}
}

/* ═══════════════════════════════════════════════════════════════
   MAIN
═══════════════════════════════════════════════════════════════ */
.main{padding:22px 28px 32px;max-width:1600px;margin:0 auto}

/* ═══════ HERO ═══════ */
.hero{margin-bottom:20px;display:flex;align-items:flex-end;justify-content:space-between;gap:14px;flex-wrap:wrap}
.hero-left{flex:1;min-width:0}
.hero-title{font-size:24px;font-weight:700;letter-spacing:-.025em;color:var(--text);margin:0 0 4px;line-height:1.2}
.hero-sub{font-size:13.5px;font-weight:400;color:var(--text3);margin:0}
.hero-actions{display:flex;gap:8px;flex-shrink:0}

/* ═══════ BUTTONS ═══════ */
.btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:9px 15px;border-radius:9px;
  font-family:inherit;font-size:13.5px;font-weight:600;cursor:pointer;
  border:1px solid transparent;transition:all .15s;white-space:nowrap;
}
.btn-primary{
  background:#0f172a;
  color:#fff;
}
.btn-primary:hover{background:#1e293b;transform:translateY(-1px);box-shadow:0 4px 12px rgba(15,23,42,.18)}
.btn-primary:active{transform:translateY(0)}
.btn-outline{background:#fff;color:var(--text2);border-color:var(--border)}
.btn-outline:hover{background:var(--surface2);color:var(--text);border-color:var(--text4)}
.btn:disabled{opacity:.55;cursor:not-allowed;transform:none!important}
.btn svg{width:14px;height:14px}

/* ═══════ METRICS — เหมือนหน้า oil 100% ═══════ */
.metrics{
  display:grid;grid-template-columns:repeat(5,1fr);gap:14px;
  margin-bottom:18px;
}
@media(max-width:1300px){.metrics{grid-template-columns:repeat(3,1fr)}}
@media(max-width:900px){.metrics{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.metrics{grid-template-columns:1fr}}

.metric-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:16px 18px;
  position:relative;overflow:hidden;
  box-shadow:var(--shadow);
  transition:transform .2s, box-shadow .2s;
}
.metric-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
/* removed colored border-top — keep cards clean */
.metric-label{font-size:12.5px;color:var(--text2);font-weight:500;margin-bottom:8px}
.metric-row{display:flex;align-items:baseline;gap:6px}
.metric-value{
  font-size:28px;font-weight:700;color:var(--text);letter-spacing:-.02em;line-height:1;
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
}
.metric-unit{font-size:12.5px;color:var(--text2);font-weight:500}

/* ═══════ PANEL — เหมือนหน้า oil ═══════ */
.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);
  margin-bottom:18px;
}
.panel-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 20px;border-bottom:1px solid var(--border);
  gap:12px;flex-wrap:wrap;
}
.panel-title{
  display:flex;align-items:center;gap:10px;
  font-size:15px;font-weight:600;color:var(--text);
}
.count-badge{
  background:var(--surface2);color:var(--text);
  font-size:12px;font-weight:600;
  padding:2px 9px;border-radius:10px;font-family:'Inter',sans-serif;
}
.panel-meta{font-size:12px;color:var(--text3);font-weight:500}

.search-wrap{position:relative;display:flex;align-items:center}
.search-wrap input{
  font-family:inherit;font-size:13px;
  padding:7px 12px 7px 32px;border:1px solid var(--border);
  border-radius:8px;background:var(--surface2);color:var(--text);outline:none;
  width:200px;transition:all .15s;height:36px;
}
.search-wrap input:focus{border-color:#0f172a;background:#fff;box-shadow:0 0 0 3px rgba(15,23,42,.08)}
.search-wrap .si{
  position:absolute;left:11px;font-size:12px;color:var(--text3);pointer-events:none;
  display:flex;align-items:center;
}
.search-wrap .si svg{width:14px;height:14px}

/* ═══════ TABLE ═══════ */
.table-wrap{overflow-x:auto;max-height:680px;overflow-y:auto}
.table-wrap::-webkit-scrollbar{width:8px;height:8px}
.table-wrap::-webkit-scrollbar-track{background:transparent}
.table-wrap::-webkit-scrollbar-thumb{background:var(--text4);border-radius:4px}
.table-wrap::-webkit-scrollbar-thumb:hover{background:var(--text3)}

table{width:100%;border-collapse:collapse;font-size:13px}
thead th{
  text-align:left;
  padding:11px 14px;
  font-size:11.5px;font-weight:600;color:var(--text3);
  background:var(--surface3);
  border-bottom:1px solid var(--border);
  white-space:nowrap;text-transform:uppercase;letter-spacing:.04em;
  position:sticky;top:0;z-index:2;
}
tbody td{
  padding:11px 14px;border-bottom:1px solid var(--border);
  color:var(--text);vertical-align:middle;font-size:13px;
}
tbody tr:last-child td{border-bottom:none}
tbody tr{transition:background .1s}
tbody tr:hover{background:var(--surface3)}

/* cells */
.cell-idx{
  font-family:'Inter',sans-serif;font-weight:600;font-size:12px;color:var(--text3);
}
.cell-date{
  font-family:'Inter',monospace;font-size:12.5px;color:var(--text);
  white-space:nowrap;font-weight:500;
}
.driver-cell{display:flex;align-items:center;gap:10px}
.driver-avatar{
  width:32px;height:32px;border-radius:8px;
  background:#f1f5f9;
  display:flex;align-items:center;justify-content:center;
  font-weight:700;color:#475569;font-size:12.5px;flex-shrink:0;
  font-family:'Inter',sans-serif;
}
.driver-info{display:flex;flex-direction:column;min-width:0;line-height:1.3}
.driver-name{font-weight:600;color:var(--text);font-size:13px}
.driver-plate{font-size:11px;color:var(--text3);font-family:'Inter',monospace;letter-spacing:.02em;margin-top:1px}

/* Service type — text only, no bg color */
.svc-tag{
  display:inline-flex;align-items:center;
  font-size:13px;font-weight:500;color:var(--text);
  white-space:nowrap;line-height:1.5;
}
.svc-oil, .svc-tire, .svc-brake, .svc-engine, .svc-ac,
.svc-battery, .svc-wash, .svc-glass, .svc-light, .svc-other{
  background:transparent;color:var(--text);border:none;padding:0;
}

.cell-detail{
  font-size:12.5px;color:var(--text2);
  max-width:230px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.cell-detail.empty{color:var(--text4);font-style:italic}

.cell-cost{
  font-weight:700;color:var(--text);font-size:13px;
  font-family:'Inter','IBM Plex Sans Thai',sans-serif;
  text-align:right;white-space:nowrap;
}
.cell-cost.zero{color:var(--text4);font-weight:400}
.cell-em{color:var(--text4);font-size:13px}

/* thumb stack */
.thumb-stack{display:inline-flex;align-items:center;gap:0}
.thumb-stack .t-img{
  width:32px;height:32px;border-radius:6px;object-fit:cover;cursor:pointer;
  border:1.5px solid #fff;box-shadow:0 0 0 1px var(--border);
  background:var(--surface2);position:relative;margin-left:-6px;
  transition:transform .15s, box-shadow .15s, z-index 0s .15s;
}
.thumb-stack .t-img:first-child{margin-left:0}
.thumb-stack .t-img:hover{
  transform:translateY(-2px) scale(1.08);
  box-shadow:0 0 0 1.5px #0f172a, 0 4px 10px rgba(0,0,0,.12);
  z-index:5;transition-delay:0s;
}
.thumb-stack .t-more{
  width:32px;height:32px;border-radius:6px;
  background:#fff;border:1px solid var(--border);
  display:inline-flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:700;color:var(--text2);
  font-family:'Inter',sans-serif;cursor:pointer;
  margin-left:-6px;position:relative;z-index:1;
  transition:all .12s;
}
.thumb-stack .t-more:hover{background:var(--surface2);color:var(--text);border-color:var(--text3)}
.thumb-none{
  width:32px;height:32px;border-radius:6px;
  background:var(--surface2);border:1px dashed var(--text4);
  display:inline-flex;align-items:center;justify-content:center;color:var(--text4);
}
.thumb-none svg{width:14px;height:14px}

/* action buttons */
.actions{display:flex;gap:4px;justify-content:flex-end;align-items:center}
.act-btn{
  width:30px;height:30px;border-radius:7px;
  border:1px solid var(--border);background:#fff;
  cursor:pointer;display:inline-flex;align-items:center;justify-content:center;
  color:var(--text2);transition:all .12s;
}
.act-btn:hover{background:var(--surface2);color:var(--text);border-color:var(--text4)}
.act-btn.edit:hover{color:#0f172a;border-color:#0f172a;background:#f1f5f9}
.act-btn.del:hover{color:var(--red);border-color:#fecaca;background:#fef2f2}
.act-btn svg{width:13px;height:13px}

/* ═══════ EMPTY ═══════ */
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .icon{
  width:52px;height:52px;border-radius:12px;background:var(--surface2);
  margin:0 auto 12px;display:flex;align-items:center;justify-content:center;
  font-size:22px;color:var(--text3);
}
.empty-state .title{font-size:14px;color:var(--text2);font-weight:600;margin-bottom:3px}
.empty-state p{font-size:12.5px;color:var(--text3)}

/* ═══════ MODAL ═══════ */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(3px)}
.modal-overlay.open{display:flex}
.modal{
  background:var(--surface);border-radius:var(--radius-lg);
  width:100%;max-width:640px;max-height:92vh;
  display:flex;flex-direction:column;
  box-shadow:0 24px 64px rgba(15,23,42,.2);
  animation:mIn .18s ease;overflow:hidden;
}
@keyframes mIn{from{transform:translateY(16px);opacity:0}to{transform:translateY(0);opacity:1}}

.modal-header{
  padding:18px 22px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;flex-shrink:0;
  background:#fff;
}
.modal-title{display:flex;align-items:center;gap:10px;font-size:15.5px;font-weight:700;color:var(--text)}
.modal-title .mt-icon{
  width:32px;height:32px;border-radius:9px;
  background:#0f172a;
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;
}
.modal-close{
  width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:#fff;color:var(--text2);
  cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;
  transition:all .12s;
}
.modal-close:hover{background:#f1f5f9;color:var(--text);border-color:var(--text4)}

.modal-body{padding:20px 22px;overflow-y:auto;flex:1}
.modal-footer{
  padding:14px 22px;border-top:1px solid var(--border);
  display:flex;justify-content:flex-end;gap:8px;flex-shrink:0;background:var(--surface3);
}

/* ═══════ FORM ═══════ */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid .full{grid-column:1/-1}
.form-label{
  display:block;font-size:11.5px;font-weight:600;color:var(--text2);
  margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;
}
.form-label .req{color:var(--red);margin-left:2px}
.form-control{
  width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;
  font-family:inherit;font-size:13.5px;color:var(--text);background:#fff;outline:none;
  transition:border-color .12s, box-shadow .12s;
}
.form-control:focus{border-color:#0f172a;box-shadow:0 0 0 3px rgba(15,23,42,.08)}
.form-control:hover:not(:focus){border-color:var(--text4)}
textarea.form-control{resize:vertical;min-height:72px;line-height:1.55}
select.form-control{
  cursor:pointer;appearance:none;-webkit-appearance:none;padding-right:32px;
  background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23737373' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
  background-repeat:no-repeat;background-position:right 11px center;background-size:12px;
}

/* ═══════ IMAGE UPLOAD ═══════ */
.img-upload{
  border:2px dashed var(--border);border-radius:10px;
  padding:22px;text-align:center;cursor:pointer;
  transition:all .15s;background:var(--surface3);position:relative;
}
.img-upload:hover{border-color:var(--text3);background:#f1f5f9}
.img-upload input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.img-upload .ic-up{
  width:46px;height:46px;border-radius:11px;background:#fff;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:20px;margin-bottom:6px;box-shadow:var(--shadow);
}
.img-upload .t{font-size:13px;color:var(--text2);font-weight:600}
.img-upload .s{font-size:11.5px;color:var(--text3);margin-top:3px}

.preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(78px,1fr));gap:8px;margin-top:10px}
.preview-item{position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;border:2px solid var(--border);transition:all .12s}
.preview-item:hover{border-color:var(--text3);transform:scale(1.03)}
.preview-item img{width:100%;height:100%;object-fit:cover;cursor:pointer;display:block}
.preview-item .rm{
  position:absolute;top:4px;right:4px;width:22px;height:22px;
  background:rgba(220,38,38,.95);border:none;border-radius:50%;color:#fff;font-size:10px;
  cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;
  box-shadow:0 2px 6px rgba(0,0,0,.25);
}
.preview-item .rm:hover{background:var(--red);transform:scale(1.1)}
.preview-item.existing{border-color:var(--text2)}
.preview-item.existing::after{
  content:'เดิม';position:absolute;bottom:4px;left:4px;
  background:var(--text);color:#fff;font-size:9px;font-weight:700;
  padding:2px 5px;border-radius:4px;font-family:'Inter',sans-serif;letter-spacing:.04em;
}

/* ═══════ LIGHTBOX ═══════ */
.lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.93);z-index:900;align-items:center;justify-content:center;flex-direction:column;backdrop-filter:blur(4px)}
.lightbox.open{display:flex}
.lightbox img{max-width:90vw;max-height:85vh;border-radius:8px;object-fit:contain;box-shadow:0 8px 40px rgba(0,0,0,.5)}
.lb-close{position:absolute;top:16px;right:20px;color:#fff;font-size:16px;cursor:pointer;background:rgba(255,255,255,.12);border:none;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;transition:background .12s}
.lb-close:hover{background:rgba(255,255,255,.22)}
.lb-caption{color:rgba(255,255,255,.7);font-size:12px;margin-top:12px}

/* ═══════ TOAST ═══════ */
.toast{
  position:fixed;bottom:24px;right:24px;background:var(--text);color:#fff;
  padding:12px 18px;border-radius:10px;font-size:13.5px;font-weight:500;z-index:999;
  opacity:0;transform:translateY(10px);transition:all .22s;pointer-events:none;
  box-shadow:0 8px 24px rgba(0,0,0,.25);
}
.toast.show{opacity:1;transform:translateY(0)}

@media(max-width:640px){
  .form-grid{grid-template-columns:1fr}
  .main{padding:16px 14px 24px}
  .hero-title{font-size:20px}
  .metric-value{font-size:24px}
  .search-wrap input{width:140px}
}
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════════════
     TOP NAVBAR — เหมือนหน้า oil ทุกอย่าง
═══════════════════════════════════════════════════════════════ --}}
<nav class="topnav">
  <div class="topnav-main">
    <div class="topnav-brand">
      <div class="logo">🛠️</div>
      <div class="title-text">ระบบจัดการเซอร์วิสรถ</div>
    </div>

    <button type="button" class="topnav-toggle" onclick="toggleTopMenu()">☰</button>

    <div class="topnav-menu" id="topMenu">
      <a class="nav-item active" href="#">
        <span class="ic">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
        </span>
        <span>Service</span>
      </a>
      <a class="nav-item" href="http://server_update:8000/solist{{ $userQuery ?? '' }}">
        <span class="ic">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>
          </svg>
        </span>
        <span>SO List</span>
      </a>
    </div>

    <div class="topnav-spacer"></div>

    <div class="topnav-right">
      <span class="topnav-time">
        <span class="pulse"></span>
        <span id="navTime">—</span>
      </span>
    </div>
  </div>

  {{-- Filter strip --}}
  <div class="topnav-filters">
    <div class="filter-group">
      <span class="filter-group-label">มุมมอง</span>
      <div class="segmented">
        <button type="button" class="seg-btn active" data-view="day"   onclick="setView('day')">รายวัน</button>
        <button type="button" class="seg-btn"        data-view="month" onclick="setView('month')">รายเดือน</button>
        <button type="button" class="seg-btn"        data-view="year"  onclick="setView('year')">รายปี</button>
        <button type="button" class="seg-btn"        data-view="all"   onclick="setView('all')">ทั้งหมด</button>
      </div>
    </div>

    <div class="fs-divider"></div>

    <div class="filter-group">
      <span class="filter-group-label" id="fsDateLabel">ช่วงวันที่</span>
      <input type="date" id="fsDate" class="flt-input" onchange="loadRecords()">
    </div>

    <div class="fs-divider"></div>

    <div class="filter-group">
      <span class="filter-group-label">ประเภทงาน</span>
      <select id="typeFilter" class="flt-input" onchange="loadRecords()">
        <option value="">ทั้งหมด</option>
        <option value="เปลี่ยนถ่ายน้ำมันเครื่อง">เปลี่ยนถ่ายน้ำมันเครื่อง</option>
        <option value="เปลี่ยนยาง">เปลี่ยนยาง</option>
        <option value="ตรวจ/เปลี่ยนเบรก">ตรวจ/เปลี่ยนเบรก</option>
        <option value="ซ่อมเครื่องยนต์">ซ่อมเครื่องยนต์</option>
        <option value="ล้าง/ซ่อมแอร์">ล้าง/ซ่อมแอร์</option>
        <option value="เปลี่ยนแบตเตอรี่">เปลี่ยนแบตเตอรี่</option>
        <option value="ล้างรถ">ล้างรถ</option>
        <option value="ซ่อม/เปลี่ยนกระจก">ซ่อม/เปลี่ยนกระจก</option>
        <option value="ซ่อม/เปลี่ยนไฟ">ซ่อม/เปลี่ยนไฟ</option>
        <option value="อื่นๆ">อื่นๆ</option>
      </select>
    </div>

    <div class="fs-divider"></div>

    <div class="filter-group">
      <span class="filter-group-label">ทะเบียน</span>
      <select id="plateFilter" class="flt-input" onchange="loadRecords()">
        <option value="">ทั้งหมด</option>
        <option value="1 ฉผ 1276">1 ฉผ 1276</option>
        <option value="1 ฉผ 3181">1 ฉผ 3181</option>
        <option value="1ฉผ213">1ฉผ213</option>
        <option value="2 ฉธ 1620">2 ฉธ 1620</option>
        <option value="2ฉธ1619">2ฉธ1619</option>
        <option value="3ฉมก6071">3ฉมก6071</option>
        <option value="3ฉมง3059">3ฉมง3059</option>
        <option value="805">805</option>
        <option value="City 8กค6309">City 8กค6309</option>
        <option value="City 9 กค4815">City 9 กค4815</option>
        <option value="แจ๊ส 9กธ4830">แจ๊ส 9กธ4830</option>
      </select>
    </div>

    <div class="flt-spacer"></div>

    <div class="flt-info">พบ <strong id="fltCount">0</strong> รายการ</div>
  </div>
</nav>

{{-- ═══════════════════════════════════════════════════════════════
     MAIN
═══════════════════════════════════════════════════════════════ --}}
<div class="main">

  {{-- Action bar (ปุ่ม) --}}
  <div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:16px;flex-wrap:wrap">
    <button class="btn btn-outline" onclick="loadRecords()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
        <path d="M21 22v-6h-6"/><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
        <path d="M3 2v6h6"/>
      </svg>
      รีเฟรช
    </button>
    <button class="btn btn-primary" onclick="openSvcModal()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      เพิ่มข้อมูลเซอร์วิส
    </button>
  </div>

  {{-- Metrics — 5 ใบเหมือนหน้า oil --}}
  <div class="metrics">
    <div class="metric-card blue">
      <div class="metric-label">รายการทั้งหมด</div>
      <div class="metric-row"><div class="metric-value" id="mTotal">—</div><div class="metric-unit">รายการ</div></div>
    </div>
    <div class="metric-card green">
      <div class="metric-label">ค่าใช้จ่ายรวม</div>
      <div class="metric-row"><div class="metric-value" id="mCost">—</div><div class="metric-unit">บาท</div></div>
    </div>
    <div class="metric-card amber">
      <div class="metric-label">เฉลี่ย/ครั้ง</div>
      <div class="metric-row"><div class="metric-value" id="mAvg">—</div><div class="metric-unit">บาท</div></div>
    </div>
    <div class="metric-card red">
      <div class="metric-label">รถที่ซ่อม</div>
      <div class="metric-row"><div class="metric-value" id="mCars">—</div><div class="metric-unit">คัน</div></div>
    </div>
    <div class="metric-card purple">
      <div class="metric-label">ประเภทงาน</div>
      <div class="metric-row"><div class="metric-value" id="mTypes">—</div><div class="metric-unit">ประเภท</div></div>
    </div>
  </div>

  {{-- Panel: รายการเซอร์วิส --}}
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        รายการเซอร์วิส
        <span class="count-badge" id="tblCount">0</span>
        <span class="panel-meta">เรียงตามวันที่ล่าสุด</span>
      </div>
      <div class="search-wrap">
        <span class="si">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </span>
        <input type="text" id="svcSearch" placeholder="ค้นหา..." oninput="debounceLoad()">
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:42px">#</th>
            <th style="width:88px">วันที่</th>
            <th style="width:200px">คนขับ / ทะเบียน</th>
            <th style="width:180px">ประเภทงาน</th>
            <th>รายละเอียด</th>
            <th style="text-align:right;width:110px">ค่าใช้จ่าย</th>
            <th style="width:120px">หลักฐาน</th>
            <th style="width:80px;text-align:right">จัดการ</th>
          </tr>
        </thead>
        <tbody id="svcTbody">
          <tr><td colspan="8"><div class="empty-state"><div class="icon">⏳</div><div class="title">กำลังโหลด</div><p>โปรดรอสักครู่</p></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div>{{-- /.main --}}

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLb()">
  <button class="lb-close" onclick="closeLb()">✕</button>
  <img id="lbImg" src="" alt="" onclick="event.stopPropagation()">
  <div class="lb-caption" id="lbCaption"></div>
</div>

<!-- SERVICE MODAL -->
<div class="modal-overlay" id="svcModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">
        <div class="mt-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
        </div>
        <span id="svcModalTitle">เพิ่มข้อมูลเซอร์วิส</span>
      </div>
      <button type="button" class="modal-close" onclick="closeSvcModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div>
          <label class="form-label">วันที่ <span class="req">*</span></label>
          <input type="date" id="sv-date" class="form-control">
        </div>
        <div>
          <label class="form-label">คนขับ <span class="req">*</span></label>
          <select id="sv-driver" class="form-control" onchange="toggleOther('sv-driver','sv-driver-other')">
            <option value="">— เลือกคนขับ —</option>
            <option value="บังเดช">บังเดช</option>
            <option value="แชม">แชม</option>
            <option value="กอล์ฟ">กอล์ฟ</option>
            <option value="หรั่ง">หรั่ง</option>
            <option value="เก่ง">เก่ง</option>
            <option value="เอ">เอ</option>
            <option value="ยุทร">ยุทร</option>
            <option value="แฟรงค์">แฟรงค์</option>
            <option value="เอ้">เอ้</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-driver-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุชื่อคนขับ">
        </div>
        <div>
          <label class="form-label">ทะเบียนรถ <span class="req">*</span></label>
          <select id="sv-plate" class="form-control" onchange="toggleOther('sv-plate','sv-plate-other')">
            <option value="">— เลือกทะเบียน —</option>
            <option value="1 ฉผ 1276">1 ฉผ 1276</option>
            <option value="1 ฉผ 3181">1 ฉผ 3181</option>
            <option value="1ฉผ213">1ฉผ213</option>
            <option value="2 ฉธ 1620">2 ฉธ 1620</option>
            <option value="2ฉธ1619">2ฉธ1619</option>
            <option value="3ฉมก6071">3ฉมก6071</option>
            <option value="3ฉมง3059">3ฉมง3059</option>
            <option value="805">805</option>
            <option value="City 8กค6309">City 8กค6309</option>
            <option value="City 9 กค4815">City 9 กค4815</option>
            <option value="แจ๊ส 9กธ4830">แจ๊ส 9กธ4830</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-plate-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุทะเบียน">
        </div>
        <div>
          <label class="form-label">ประเภทงาน <span class="req">*</span></label>
          <select id="sv-type" class="form-control">
            <option value="">— เลือกประเภท —</option>
            <option value="เปลี่ยนถ่ายน้ำมันเครื่อง">เปลี่ยนถ่ายน้ำมันเครื่อง</option>
            <option value="เปลี่ยนยาง">เปลี่ยนยาง</option>
            <option value="ตรวจ/เปลี่ยนเบรก">ตรวจ/เปลี่ยนเบรก</option>
            <option value="ซ่อมเครื่องยนต์">ซ่อมเครื่องยนต์</option>
            <option value="ล้าง/ซ่อมแอร์">ล้าง/ซ่อมแอร์</option>
            <option value="เปลี่ยนแบตเตอรี่">เปลี่ยนแบตเตอรี่</option>
            <option value="ล้างรถ">ล้างรถ</option>
            <option value="ซ่อม/เปลี่ยนกระจก">ซ่อม/เปลี่ยนกระจก</option>
            <option value="ซ่อม/เปลี่ยนไฟ">ซ่อม/เปลี่ยนไฟ</option>
            <option value="อื่นๆ">อื่นๆ</option>
          </select>
        </div>
        <div>
          <label class="form-label">ค่าใช้จ่าย (฿)</label>
          <input type="number" id="sv-cost" class="form-control" step="0.01" min="0" placeholder="0.00">
        </div>
        <div class="full">
          <label class="form-label">รายละเอียด</label>
          <textarea id="sv-detail" class="form-control" rows="2" placeholder="รายละเอียดงานซ่อม..."></textarea>
        </div>
        <div class="full">
          <label class="form-label">รูปภาพหลักฐาน (เลือกได้หลายรูป)</label>
          <div class="img-upload">
            <input type="file" accept="image/*" multiple onchange="addNewImages(this)" id="imgInput">
            <div class="ic-up">📷</div>
            <div class="t">คลิกหรือลากรูปมาวางที่นี่</div>
            <div class="s">JPG, PNG, WEBP — ไม่เกิน 5MB ต่อรูป</div>
          </div>
          <div class="preview-grid" id="previewGrid"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeSvcModal()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveSvc()">บันทึก</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const LIST_URL    = "{{ url('/service/list') }}";
const STORE_URL   = "{{ url('/service') }}";
const UPDATE_BASE = "{{ url('/service') }}";

const TYPE_CSS = {
  'เปลี่ยนถ่ายน้ำมันเครื่อง':'svc-oil','เปลี่ยนยาง':'svc-tire',
  'ตรวจ/เปลี่ยนเบรก':'svc-brake','ซ่อมเครื่องยนต์':'svc-engine',
  'ล้าง/ซ่อมแอร์':'svc-ac','เปลี่ยนแบตเตอรี่':'svc-battery',
  'ล้างรถ':'svc-wash','ซ่อม/เปลี่ยนกระจก':'svc-glass',
  'ซ่อม/เปลี่ยนไฟ':'svc-light','อื่นๆ':'svc-other'
};

let editId = null;
let existingImgs = [];
let newFiles = [];
let currentView = 'day';

/* ── MOBILE MENU ── */
function toggleTopMenu(){
  document.getElementById('topMenu').classList.toggle('open');
}

/* ── TIME ── */
function updateNavTime(){
  const now = new Date();
  const time = now.toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit',hour12:false});
  const el = document.getElementById('navTime'); if(el) el.textContent = time;
}

/* ── TOAST ── */
function showToast(msg, dur=2400){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), dur);
}

/* ── VIEW TABS ── */
function setView(v){
  currentView = v;
  document.querySelectorAll('.seg-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.view === v);
  });

  const dateInput = document.getElementById('fsDate');
  const dateLabel = document.getElementById('fsDateLabel');
  const dateGroup = dateInput.parentElement;

  // clear old value when switching mode (เพื่อไม่ให้ format ชนกัน)
  dateInput.value = '';
  dateInput.removeAttribute('min');
  dateInput.removeAttribute('max');
  dateInput.removeAttribute('placeholder');

  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth()+1).padStart(2,'0');
  const dd = String(now.getDate()).padStart(2,'0');

  if(v === 'day'){
    dateInput.type = 'date';
    dateInput.value = `${yyyy}-${mm}-${dd}`;
    dateLabel.textContent = 'ช่วงวันที่';
    dateGroup.style.display = '';
  } else if(v === 'month'){
    dateInput.type = 'month';
    dateInput.value = `${yyyy}-${mm}`;
    dateLabel.textContent = 'เดือน';
    dateGroup.style.display = '';
  } else if(v === 'year'){
    dateInput.type = 'number';
    dateInput.placeholder = 'พ.ศ.';
    dateInput.min = '2560';
    dateInput.max = '2580';
    dateInput.value = String(yyyy + 543);
    dateLabel.textContent = 'ปี (พ.ศ.)';
    dateGroup.style.display = '';
  } else {
    dateGroup.style.display = 'none';
  }
  loadRecords();
}

/* ── HELPERS ── */
function toggleOther(selId, otherId){
  const sel = document.getElementById(selId);
  const oth = document.getElementById(otherId);
  oth.style.display = sel.value === '__other__' ? 'block' : 'none';
  if(sel.value === '__other__') oth.focus();
}

function getVal(selId, otherId){
  const sel = document.getElementById(selId);
  if(sel.value === '__other__') return document.getElementById(otherId).value.trim();
  return sel.value;
}

function todayStr(){
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function setSelectOrOther(selId, otherId, val){
  const sel = document.getElementById(selId);
  const oth = document.getElementById(otherId);
  const opt = Array.from(sel.options).find(o => o.value === val);
  if(opt){ sel.value = val; oth.style.display = 'none'; }
  else   { sel.value = '__other__'; oth.style.display = 'block'; oth.value = val; }
}

function initials(name){ return name ? name.trim().charAt(0).toUpperCase() : '?'; }

function formatDate(dateStr){
  if(!dateStr) return '—';
  const d = new Date(dateStr);
  if(isNaN(d)) return dateStr;
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const yy = (d.getFullYear()+543).toString().slice(-2);
  return `${dd}/${mm}/${yy}`;
}

/* ── LOAD ── */
let debTimer = null;
function debounceLoad(){ clearTimeout(debTimer); debTimer = setTimeout(() => loadRecords(), 350); }

async function loadRecords(){
  const q      = document.getElementById('svcSearch').value;
  const type   = document.getElementById('typeFilter').value;
  const plate  = document.getElementById('plateFilter').value;
  const fsDate = document.getElementById('fsDate').value;

  const params = new URLSearchParams({q, type, plate});
  params.append('view', currentView);
  if(fsDate && currentView !== 'all') params.append('date', fsDate);

  try {
    const res  = await fetch(`${LIST_URL}?${params}`);
    const data = await res.json();

    // ── CLIENT-SIDE date filter (เผื่อ backend ยังไม่กรอง) ──
    let filtered = data.records || [];
    if(currentView !== 'all' && fsDate){
      filtered = filtered.filter(r => {
        if(!r.date) return false;
        const d = new Date(r.date);
        if(isNaN(d)) return false;

        if(currentView === 'day'){
          // fsDate: YYYY-MM-DD
          const rDate = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
          return rDate === fsDate;
        }
        if(currentView === 'month'){
          // fsDate: YYYY-MM
          const rMonth = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;
          return rMonth === fsDate;
        }
        if(currentView === 'year'){
          // fsDate: พ.ศ. (number) — แปลงเป็น ค.ศ.
          const buddhistYear = parseInt(fsDate);
          if(isNaN(buddhistYear)) return true;
          const gregorianYear = buddhistYear - 543;
          return d.getFullYear() === gregorianYear;
        }
        return true;
      });
    }

    // ── คำนวณ metrics ใหม่จากผลที่กรองแล้ว ──
    const recalcMetrics = computeMetrics(filtered);
    renderMetrics(recalcMetrics, filtered);
    renderTable(filtered);
  } catch(e){
    document.getElementById('svcTbody').innerHTML =
      `<tr><td colspan="8"><div class="empty-state"><div class="icon">⚠</div><div class="title">โหลดข้อมูลไม่สำเร็จ</div><p>กรุณาลองรีเฟรชอีกครั้ง</p></div></td></tr>`;
  }
}

function computeMetrics(records){
  const total = records.length;
  const totalCost = records.reduce((s, r) => s + (Number(r.cost) || 0), 0);
  const avg = total ? Math.round(totalCost / total) : 0;
  const cars = new Set(records.map(r => r.plate).filter(Boolean)).size;
  return { total, totalCost, avg, cars };
}

function renderMetrics(m, records){
  document.getElementById('mTotal').textContent = m.total.toLocaleString();
  document.getElementById('mCost').textContent  = Number(m.totalCost).toLocaleString();
  document.getElementById('mAvg').textContent   = Number(m.avg).toLocaleString();
  document.getElementById('mCars').textContent  = m.cars.toLocaleString();
  const types = new Set((records||[]).map(r => r.type).filter(Boolean));
  document.getElementById('mTypes').textContent = types.size;
}

function renderTable(records){
  document.getElementById('tblCount').textContent = records.length;
  document.getElementById('fltCount').textContent = records.length;
  const tbody = document.getElementById('svcTbody');
  if(!records.length){
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="icon">🛠️</div><div class="title">ไม่พบรายการ</div><p>ลองปรับตัวกรองหรือเพิ่มรายการใหม่</p></div></td></tr>`;
    return;
  }
  tbody.innerHTML = records.map((r, fi) => {
    const typeCss   = TYPE_CSS[r.type] || 'svc-other';
    const urls      = r.image_urls || [];
    const hasImg    = urls.length > 0;

    let thumbHtml = '';
    if(hasImg){
      const allUrlsJson = JSON.stringify(urls).replace(/"/g, '&quot;');
      const show = urls.slice(0, 3);
      const remaining = urls.length - show.length;
      thumbHtml = `<div class="thumb-stack">` +
        show.map((u, si) =>
          `<img class="t-img" src="${u}" alt="" onclick="openLbList(${allUrlsJson}, ${si})">`
        ).join('') +
        (remaining > 0
          ? `<button class="t-more" onclick="openLbList(${allUrlsJson}, ${show.length})">+${remaining}</button>`
          : '') +
        `</div>`;
    } else {
      thumbHtml = `<span class="thumb-none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
          <circle cx="8.5" cy="8.5" r="1.5"/>
          <polyline points="21 15 16 10 5 21"/>
        </svg>
      </span>`;
    }

    const costDisplay = r.cost && Number(r.cost) > 0
      ? `฿${Number(r.cost).toLocaleString()}`
      : '<span class="cell-em">—</span>';

    const detailHtml = r.detail
      ? `<div class="cell-detail" title="${r.detail}">${r.detail}</div>`
      : `<div class="cell-detail empty">—</div>`;

    return `<tr>
      <td><span class="cell-idx">${String(fi+1).padStart(2,'0')}</span></td>
      <td><div class="cell-date">${formatDate(r.date)}</div></td>
      <td>
        <div class="driver-cell">
          <div class="driver-avatar">${initials(r.driver)}</div>
          <div class="driver-info">
            <span class="driver-name">${r.driver}</span>
            <span class="driver-plate">${r.plate}</span>
          </div>
        </div>
      </td>
      <td><span class="svc-tag ${typeCss}">${r.type}</span></td>
      <td>${detailHtml}</td>
      <td class="cell-cost">${costDisplay}</td>
      <td>${thumbHtml}</td>
      <td><div class="actions">
        <button class="act-btn edit" onclick="openSvcModal(${JSON.stringify(r).split('"').join('&quot;')})" title="แก้ไข">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
        </button>
        <button class="act-btn del" onclick="deleteSvc(${r.id})" title="ลบ">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"/>
            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            <path d="M10 11v6"/><path d="M14 11v6"/>
          </svg>
        </button>
      </div></td>
    </tr>`;
  }).join('');
}

/* ── IMAGE HANDLING ── */
function addNewImages(input){
  Array.from(input.files).forEach(file => {
    if(file.size > 5*1024*1024){ alert(`${file.name} ใหญ่เกิน 5MB`); return; }
    newFiles.push(file);
  });
  input.value = '';
  renderPreviewGrid();
}

function removeExisting(idx){ existingImgs.splice(idx, 1); renderPreviewGrid(); }
function removeNew(idx){ newFiles.splice(idx, 1); renderPreviewGrid(); }

function renderPreviewGrid(){
  const grid = document.getElementById('previewGrid');
  const existHtml = existingImgs.map((img, i) => `
    <div class="preview-item existing" title="รูปเดิม">
      <img src="${img.url}" onclick="openLb('${img.url}','รูปเดิม')">
      <button class="rm" onclick="removeExisting(${i})">✕</button>
    </div>`).join('');

  const newHtml = newFiles.map((f, i) => {
    const url = URL.createObjectURL(f);
    return `<div class="preview-item">
      <img src="${url}" onclick="openLb('${url}','${f.name}')">
      <button class="rm" onclick="removeNew(${i})">✕</button>
    </div>`;
  }).join('');

  grid.innerHTML = existHtml + newHtml;
}

/* ── MODAL ── */
function openSvcModal(record = null){
  editId = null;
  existingImgs = [];
  newFiles = [];
  renderPreviewGrid();

  if(record){
    editId = record.id;
    document.getElementById('svcModalTitle').textContent = 'แก้ไขข้อมูลเซอร์วิส';
    document.getElementById('sv-date').value   = record.date;
    document.getElementById('sv-type').value   = record.type;
    document.getElementById('sv-cost').value   = record.cost;
    document.getElementById('sv-detail').value = record.detail || '';
    setSelectOrOther('sv-driver','sv-driver-other', record.driver);
    setSelectOrOther('sv-plate', 'sv-plate-other',  record.plate);

    const paths = record.images || [];
    const urls  = record.image_urls || [];
    existingImgs = paths.map((p, i) => ({ path: p, url: urls[i] || '' }));
    renderPreviewGrid();
  } else {
    document.getElementById('svcModalTitle').textContent = 'เพิ่มข้อมูลเซอร์วิส';
    document.getElementById('sv-date').value   = todayStr();
    document.getElementById('sv-driver').value = '';
    document.getElementById('sv-plate').value  = '';
    document.getElementById('sv-type').value   = '';
    document.getElementById('sv-cost').value   = '';
    document.getElementById('sv-detail').value = '';
    ['sv-driver-other','sv-plate-other'].forEach(id => {
      const el = document.getElementById(id); el.style.display = 'none'; el.value = '';
    });
  }
  document.getElementById('svcModal').classList.add('open');
}

function closeSvcModal(){ document.getElementById('svcModal').classList.remove('open'); }

async function saveSvc(){
  const date   = document.getElementById('sv-date').value;
  const driver = getVal('sv-driver','sv-driver-other');
  const plate  = getVal('sv-plate','sv-plate-other');
  const type   = document.getElementById('sv-type').value;
  const cost   = document.getElementById('sv-cost').value;
  const detail = document.getElementById('sv-detail').value;

  if(!date)  { alert('กรุณาเลือกวันที่'); return; }
  if(!driver){ alert('กรุณาเลือกหรือระบุชื่อคนขับ'); return; }
  if(!plate) { alert('กรุณาเลือกหรือระบุทะเบียนรถ'); return; }
  if(!type)  { alert('กรุณาเลือกประเภทงาน'); return; }

  const btn = document.getElementById('saveBtn');
  btn.disabled = true; btn.textContent = 'กำลังบันทึก...';

  const fd = new FormData();
  fd.append('date',   date);
  fd.append('driver', driver);
  fd.append('plate',  plate);
  fd.append('type',   type);
  fd.append('cost',   cost || 0);
  fd.append('status', 'เสร็จแล้ว');
  fd.append('detail', detail);
  newFiles.forEach(f => fd.append('images[]', f));

  let url    = STORE_URL;
  let method = 'POST';

  if(editId){
    url    = `${UPDATE_BASE}/${editId}`;
    method = 'POST';
    fd.append('_method', 'POST');
    fd.append('keep_images', JSON.stringify(existingImgs.map(i => i.path)));
  }

  try {
    const res = await fetch(url, { method, headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}, body: fd });
    const responseText = await res.text();
    let data;
    try { data = JSON.parse(responseText); }
    catch(parseErr){
      console.error('Server response (not JSON):', responseText.substring(0,500));
      alert(`Server ตอบกลับไม่ใช่ JSON\n\nURL: ${url}\nStatus: ${res.status}\n\nคำตอบ: ${responseText.substring(0,200)}`);
      return;
    }
    if(!res.ok){
      alert(`HTTP ${res.status}: ${data.message || JSON.stringify(data.errors || data)}`);
      return;
    }
    if(data.success){
      closeSvcModal();
      showToast(editId ? 'แก้ไขสำเร็จ' : 'บันทึกสำเร็จ');
      loadRecords();
    } else {
      alert('เกิดข้อผิดพลาด: ' + JSON.stringify(data.errors || data));
    }
  } catch(e){
    console.error('Network error:', e);
    alert(`ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้\n\nURL: ${url}\nError: ${e.message}`);
  } finally {
    btn.disabled = false; btn.textContent = 'บันทึก';
  }
}

/* ── DELETE ── */
async function deleteSvc(id){
  if(!confirm('ยืนยันการลบรายการนี้?')) return;
  try {
    const res = await fetch(`${UPDATE_BASE}/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json' }
    });
    const data = await res.json();
    if(data.success){ showToast('ลบสำเร็จ'); loadRecords(); }
  } catch(e){ alert('ลบไม่สำเร็จ'); }
}

/* ── LIGHTBOX ── */
let lbList = [], lbIdx = 0;
function openLb(src, cap){
  lbList = [src]; lbIdx = 0;
  document.getElementById('lbImg').src = src;
  document.getElementById('lbCaption').textContent = cap || '';
  document.getElementById('lightbox').classList.add('open');
}
function openLbList(urls, startIdx){
  lbList = urls; lbIdx = startIdx || 0;
  document.getElementById('lbImg').src = urls[lbIdx];
  document.getElementById('lbCaption').textContent = `รูปที่ ${lbIdx+1} / ${urls.length}`;
  document.getElementById('lightbox').classList.add('open');
}
function closeLb(){ document.getElementById('lightbox').classList.remove('open'); }
function lbNext(d){
  if(!lbList.length) return;
  lbIdx = (lbIdx + d + lbList.length) % lbList.length;
  document.getElementById('lbImg').src = lbList[lbIdx];
  document.getElementById('lbCaption').textContent = `รูปที่ ${lbIdx+1} / ${lbList.length}`;
}
document.addEventListener('keydown', e => {
  if(e.key === 'Escape'){ closeLb(); closeSvcModal(); }
  if(document.getElementById('lightbox').classList.contains('open')){
    if(e.key === 'ArrowRight') lbNext(1);
    if(e.key === 'ArrowLeft')  lbNext(-1);
  }
});

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
  updateNavTime();
  setInterval(updateNavTime, 60000);
  document.getElementById('fsDate').value = todayStr();
  loadRecords();
});
</script>
</body>
</html>
{{-- resources/views/driver/oil.blade.php — Apple-style (cleaned) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบติดตามน้ำมันรถ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
/* ═══════════════════════════════════════════════════════════════
  DESIGN SYSTEM — ฟ้า (หลัก) + ส้ม (รอง)
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
.topnav-filters{display:flex;align-items:center;gap:16px;padding:0 28px;height:44px;max-width:1800px;margin:0 auto;border-top:0.5px solid var(--separator);overflow-x:auto;scrollbar-width:none;}
.topnav-filters::-webkit-scrollbar{display:none}
.filter-group{display:flex;align-items:center;gap:8px;flex-shrink:0}
.filter-group-label{font-size:14px;font-weight:500;color:var(--text3);white-space:nowrap;}
.segmented{display:inline-flex;background:rgba(120,120,128,.12);border-radius:7px;padding:2px;}
.segmented .seg-btn{padding:4px 12px;border-radius:5px;background:transparent;border:none;color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;white-space:nowrap;transition:all .15s var(--ease);}
.segmented .seg-btn:hover{color:var(--text)}
.segmented .seg-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.1),0 1px 1px rgba(0,0,0,.05);}
.pill-select,.pill-date{padding:5px 12px;border:0.5px solid var(--separator-strong);border-radius:7px;font-family:inherit;font-size:14px;font-weight:500;background:#fff;color:var(--text);min-width:130px;cursor:pointer;transition:all .15s var(--ease);outline:none;}
.pill-select:hover,.pill-date:hover{border-color:rgba(0,0,0,.2)}
.pill-select:focus,.pill-date:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.2)}
.date-trigger-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border:0.5px solid var(--separator-strong);border-radius:7px;background:#fff;color:var(--text);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;min-width:200px;transition:all .15s var(--ease);}
.date-trigger-pill:hover{border-color:rgba(0,0,0,.2)}
.date-trigger-pill.active{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.2)}
.date-trigger-pill .arrow{margin-left:auto;font-size:14px;color:var(--text4)}

/* ── Entry card ── */
.entry-layout{display:grid;grid-template-columns:minmax(0,70fr) minmax(0,30fr);gap:18px;margin-bottom:24px;align-items:start;}
.entry-card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);overflow:hidden;min-width:0;}
.entry-card-head{padding:14px 20px;border-bottom:1px solid var(--separator);background:linear-gradient(180deg,#fafbfc,#fff);}
.entry-card-head-left{display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.entry-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:10px;font-size:19px;box-shadow:0 2px 8px rgba(59,130,246,.35),inset 0 1px 0 rgba(255,255,255,.2);flex-shrink:0;}
.entry-titlewrap{display:flex;flex-direction:column;line-height:1.25;flex-shrink:0}
.entry-title{font-size:16px;font-weight:600;color:var(--text);letter-spacing:-0.02em;}
.entry-sub{font-size:14px;font-weight:400;color:var(--text3);margin-top:2px;}
.entry-oil-mini{display:inline-flex;align-items:center;gap:8px;padding:7px 12px;background:var(--green-light);border:1px solid #dbeafe;border-radius:100px;color:var(--green-dark);}
.entry-oil-label{font-size:14px;font-weight:600}
.entry-oil-num{font-family:var(--font-mono);font-size:15px;font-weight:700;color:var(--text);}
.entry-oil-refresh{width:22px;height:22px;border-radius:50%;background:rgba(255,255,255,.7);border:none;cursor:pointer;font-size:14px;color:var(--green-dark);display:inline-flex;align-items:center;justify-content:center;transition:transform .3s var(--ease);}
.entry-oil-refresh:hover{transform:rotate(180deg)}
.entry-export-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;background:var(--blue);color:#fff;border:none;border-radius:9px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;letter-spacing:-0.01em;transition:all .15s var(--ease);white-space:nowrap;box-shadow:0 1px 3px rgba(245,158,11,.3);}
.entry-export-btn:hover{background:var(--blue-hover);transform:translateY(-1px);box-shadow:0 4px 10px rgba(245,158,11,.35);}
.entry-export-btn:active{transform:translateY(0)}
.entry-export-btn:disabled{background:var(--text4);cursor:wait;transform:none;box-shadow:none;}
.entry-export-btn svg{flex-shrink:0}
.entry-oil-tabs{display:flex;align-items:center;gap:4px;flex-wrap:wrap;padding:10px 22px;background:var(--bg-subtle2);border-bottom:1px solid var(--separator);}
.entry-oil-tab{padding:4px 11px;border-radius:100px;background:#fff;border:1px solid var(--separator-strong);color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;transition:all .15s var(--ease);}
.entry-oil-tab:hover{background:#fafafa;border-color:rgba(0,0,0,.2)}
.entry-oil-tab.active{background:var(--green);color:#fff;border-color:var(--green);font-weight:600}
.entry-oil-status{font-size:14px;color:var(--text3);font-weight:500;margin-left:6px;}
.entry-oil-live{margin-left:auto;display:inline-flex;align-items:center;gap:5px;padding:3px 9px;background:rgba(59,130,246,.12);color:var(--green-dark);border-radius:100px;font-size:14px;font-weight:600;}
.entry-oil-live .dot{width:5px;height:5px;border-radius:50%;background:var(--green)}
.entry-oil-live.loading{background:rgba(245,158,11,.12);color:var(--orange)}
.entry-oil-live.loading .dot{background:var(--orange);animation:pulse 1s infinite}
.entry-loading-row{display:flex;align-items:center;justify-content:center;gap:8px;padding:30px;color:var(--orange);font-size:14px;font-weight:500;}
.entry-loading-row .spinner{width:14px;height:14px;border:2px solid var(--orange);border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}
.entry-rows-wrap{overflow-x:hidden;}
.entry-rows-header,.entry-row{display:grid;grid-template-columns:minmax(100px,0.8fr) minmax(95px,0.9fr) minmax(115px,1fr) minmax(80px,0.85fr) minmax(72px,0.7fr) minmax(78px,0.75fr) minmax(72px,auto);gap:8px;align-items:center;padding:10px 14px;min-width:0;}
.entry-rows-header{background:var(--bg-subtle2);border-bottom:1px solid var(--separator);font-size:14px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.4px;}
.entry-rows-header > div:last-child{text-align:center;min-width:80px}
.entry-row{border-bottom:1px solid var(--separator);transition:background .12s;position:relative;cursor:pointer;}
.entry-row:hover{background:var(--bg-subtle2)}
.entry-row.focused{background:linear-gradient(90deg,rgba(59,130,246,.18),rgba(59,130,246,.06) 60%);box-shadow:inset 0 0 0 1px rgba(59,130,246,.25);}
.entry-row.focused::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--green);}
.entry-row.saving{opacity:.6;pointer-events:none}
.er-driver{display:flex;align-items:center;gap:10px;min-width:0;}
.er-driver-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--blue-light),var(--green-light));color:var(--green-dark);display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;}
.er-driver-info{min-width:0;flex:1}
.er-driver-name{font-size:14px;font-weight:600;color:var(--text);letter-spacing:-0.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.er-driver-jobs{font-size:14px;color:var(--text3);margin-top:1px;}
.er-driver-jobs .er-ok{color:#059669;font-weight:600}
.er-driver-jobs .er-fail{color:var(--red);font-weight:600}
.er-plate-select{width:100%;padding:7px 11px;border:1px solid var(--separator-strong);border-radius:8px;font-family:inherit;font-size:14px;font-weight:500;background:#fff;color:var(--text);outline:none;appearance:none;background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23a1a1aa' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 9px center;padding-right:26px;transition:all .15s var(--ease);}
.er-plate-select:hover{border-color:rgba(0,0,0,.2)}
.er-plate-select:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.er-time-pair{display:grid;grid-template-columns:1fr;gap:4px;align-items:center;}
.er-time-arrow{display:none;}
.er-dt-input{padding:7px 8px;border:1px solid var(--separator-strong);border-radius:8px;font-family:var(--font-mono);font-size:13px;font-weight:500;background:#fff;color:var(--text);cursor:pointer;width:100%;min-width:0;transition:all .15s var(--ease);}
.er-dt-input:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.er-num-input{width:100%;padding:7px 10px;border:1px solid var(--separator-strong);border-radius:8px;font-family:var(--font-mono);font-size:14px;font-weight:600;background:#fff;color:var(--text);outline:none;transition:all .15s var(--ease);text-align:right;}
.er-num-input::placeholder{color:var(--text4);font-weight:400}
.er-num-input:hover{border-color:rgba(0,0,0,.2)}
.er-num-input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.er-num-input:disabled{background:var(--bg-subtle);color:var(--text4);cursor:not-allowed;}
.er-nofuel-check{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text3);margin-bottom:5px;cursor:pointer;user-select:none;}
.er-nofuel-check input{cursor:pointer;accent-color:var(--green);}
.er-summary{display:flex;flex-direction:column;gap:3px;font-size:14px;}
.er-summary-row{display:flex;gap:6px;align-items:baseline;}
.er-summary-label{color:var(--text4);font-weight:500;min-width:32px}
.er-summary-val{font-family:var(--font-mono);font-weight:600;color:var(--text2)}
.er-summary-val.green{color:var(--green-dark)}
.er-summary-val.red{color:var(--red)}
.er-summary-val.empty{color:var(--text5);font-weight:400}
.er-save-btn{padding:8px 16px;background:var(--green);color:#fff;border:none;border-radius:100px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;letter-spacing:-0.01em;white-space:nowrap;transition:all .15s var(--ease);display:inline-flex;align-items:center;gap:5px;}
.er-save-btn:hover{background:var(--green-dark);transform:scale(1.04)}
.er-save-btn:active{transform:scale(.96)}
.er-save-btn:disabled{background:var(--text5);cursor:not-allowed;transform:none;}
.er-save-btn .ic{font-size:14px}
.entry-empty{text-align:center;padding:40px 20px;color:var(--text4);font-size:14px;}

/* ── Save toast ── */
.save-toast{position:fixed;top:78px;right:28px;z-index:200;background:#fff;border-radius:14px;box-shadow:0 12px 32px rgba(0,0,0,.12),0 4px 12px rgba(0,0,0,.06);border:1px solid var(--separator);padding:12px 16px;display:flex;align-items:center;gap:12px;min-width:240px;border-left:3px solid var(--green);animation:toastSlideIn .25s var(--ease) forwards;}
.save-toast.hiding{animation:toastSlideOut .25s var(--ease) forwards}
@keyframes toastSlideIn{from{opacity:0;transform:translateY(-10px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes toastSlideOut{to{opacity:0;transform:translateY(-10px) scale(.95)}}
.save-toast-icon{width:28px;height:28px;display:flex;align-items:center;justify-content:center;background:var(--green);color:#fff;border-radius:50%;font-size:14px;font-weight:700;flex-shrink:0;}
.save-toast-body{flex:1;min-width:0}
.save-toast-title{font-size:14px;font-weight:600;color:var(--text);letter-spacing:-0.01em;}
.save-toast-msg{font-size:14px;color:var(--text3);margin-top:1px;}

/* ── Jobs panel ── */
.jobs-panel{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);overflow:hidden;display:flex;flex-direction:column;position:sticky;top:110px;max-height:calc(100vh - 130px);}
.jobs-panel-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 16px;border-bottom:1px solid var(--separator);background:linear-gradient(180deg,#fafbfc,#fff);flex-shrink:0;}
.jobs-panel-title{font-size:14px;font-weight:600;color:var(--text);display:flex;align-items:center;gap:8px;letter-spacing:-0.01em;}
.jobs-panel-title .ico{font-size:15px}
.jobs-panel-body{flex:1;min-height:0;overflow-y:auto;-webkit-overflow-scrolling:touch;}
.jobs-panel-body::-webkit-scrollbar{width:8px}
.jobs-panel-body::-webkit-scrollbar-thumb{background:rgba(0,0,0,.12);border-radius:100px;border:2px solid #fff;background-clip:padding-box;}
.dgj-row{padding:8px 12px;border-top:1px solid var(--separator);font-size:14px;}
.dgj-row:first-child{border-top:none}
.dgj-main{display:flex;flex-direction:column;gap:4px;}
.dgj-top{display:grid;grid-template-columns:auto 1fr auto;gap:8px;align-items:center;}
.dgj-meta{display:flex;flex-wrap:wrap;gap:4px 6px;font-size:14px;color:var(--text3);line-height:1.4;}
.dgj-meta-item{display:inline-flex;align-items:baseline;gap:3px;}
.dgj-meta-label{color:var(--text4);font-size:14px;}
.dgj-meta-sep{color:var(--text5)}
.dgj-meta-item.dgj-note{color:var(--red)}
.dgj-meta-item.dgj-note .dgj-meta-label{color:var(--red)}
.dgj-bill{font-family:var(--font-mono);font-size:14px;font-weight:600;color:var(--text2);padding:1px 6px;background:var(--bg-subtle);border-radius:5px;white-space:nowrap;user-select:text;}
.dgj-customer{color:var(--text2);font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;}
.dgj-status{display:inline-flex;align-items:center;padding:1px 8px;border-radius:100px;font-size:14px;font-weight:600;white-space:nowrap;}
.dgj-status.ok{background:#d1fae5;color:#059669}
.dgj-status.fail{background:var(--red-light);color:var(--red)}
.dgj-status.pending{background:var(--bg-subtle);color:var(--text3)}
.jobs-summary-bar{display:flex;gap:6px;flex-wrap:wrap;padding:10px 12px;background:linear-gradient(180deg,#fafbfc,#fff);border-bottom:1px solid var(--separator);position:sticky;top:0;z-index:1;}
.jsb-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:100px;font-size:14px;font-weight:600;background:#fff;border:1px solid var(--separator);color:var(--text2);}
.jsb-chip strong{font-family:var(--font-mono);color:var(--text)}
.jsb-chip.ok{background:#d1fae5;color:#059669;border-color:transparent}
.jsb-chip.fail{background:var(--red-light);color:var(--red);border-color:transparent}
.job-loading{text-align:center;padding:22px;color:var(--text3);font-size:14px;}
.job-date-chip{display:inline-block;padding:1px 8px;background:var(--blue);color:#fff;border-radius:100px;font-size:14px;font-weight:600;}

/* ── Dual grid / cards ── */
.dual-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:18px;margin-bottom:18px;align-items:stretch;}
.dual-grid.single-col{grid-template-columns:1fr}
.card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);overflow:hidden;display:flex;flex-direction:column;min-height:540px;max-height:640px;}
.card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:0.5px solid var(--separator);gap:12px;}
.card-title{font-size:15px;font-weight:600;color:var(--text);letter-spacing:-0.02em;display:flex;align-items:center;gap:8px;}
.card-count{display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 7px;background:rgba(0,0,0,.06);color:var(--text2);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);}
.card-meta{font-size:14px;color:var(--text3);font-weight:400;margin-left:4px;}
.sort-toggle{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.sort-label{font-size:14px;color:var(--text3);font-weight:500;white-space:nowrap;}
.sort-segmented{display:inline-flex;background:rgba(0,0,0,.05);border-radius:7px;padding:2px;}
.sort-btn{padding:4px 10px;border:none;background:transparent;color:var(--text3);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;border-radius:5px;white-space:nowrap;letter-spacing:-0.01em;transition:all .15s var(--ease);}
.sort-btn:hover{color:var(--text)}
.sort-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 1px rgba(0,0,0,.04);}
.search-pill{position:relative;flex-shrink:0;}
.search-pill .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text4);pointer-events:none;display:inline-flex;align-items:center;width:13px;height:13px;}
.search-pill .si svg{display:block}
.search-pill input{padding:6px 11px 6px 28px;border:0.5px solid var(--separator-strong);border-radius:100px;font-family:inherit;font-size:14px;width:200px;background:var(--bg-subtle);color:var(--text);transition:all .15s var(--ease);}
.search-pill input:focus{outline:none;background:#fff;border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.search-pill input::placeholder{color:var(--text4)}
.fuel-table-scroll{overflow-x:hidden;overflow-y:auto;flex:1;min-height:0;-webkit-overflow-scrolling:touch;}
.fuel-table-scroll::-webkit-scrollbar{width:8px;height:8px}
.fuel-table-scroll::-webkit-scrollbar-thumb{background:rgba(0,0,0,.12);border-radius:100px;border:2px solid #fff;background-clip:padding-box;}
.fuel-table{width:100%;min-width:0;border-collapse:collapse;font-size:14px;table-layout:auto;}
.fuel-table thead{position:sticky;top:0;z-index:1;background:#fff;}
.fuel-table thead th{padding:11px 10px;background:var(--bg-subtle2);font-size:14px;color:var(--text3);font-weight:600;text-align:left;border-bottom:0.5px solid var(--separator);letter-spacing:-0.01em;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;}
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
.time-pill{display:inline-block;padding:2px 8px;background:var(--bg-subtle);color:var(--text2);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;}
.hour-pill{display:inline-block;padding:2px 9px;background:rgba(245,158,11,.12);color:#b45309;border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;letter-spacing:-0.01em;}
.carry-hint{font-size:14px;color:var(--orange);font-weight:500;margin-top:2px;white-space:nowrap;font-family:var(--font-mono);}
.date-pill{display:inline-block;padding:2px 8px;background:rgba(59,130,246,.08);color:var(--green-dark);border-radius:100px;font-size:14px;font-weight:600;font-family:var(--font-mono);white-space:nowrap;letter-spacing:-0.01em;}
.km-good{color:var(--green-dark);font-weight:600}
.km-mid{color:var(--text);font-weight:500}
.km-bad{color:var(--red);font-weight:700}
.thb-km-val{color:var(--text2);font-weight:600;}
.driver-list{padding:6px;overflow-y:auto;flex:1;min-height:0;-webkit-overflow-scrolling:touch;}
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

/* ── Charts ── */
.charts-grid{display:grid;grid-template-columns:1fr;gap:18px;}
.chart-card{background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow);border:1px solid rgba(0,0,0,.04);padding:clamp(12px,2vw,22px);min-width:0;overflow:hidden;}
.chart-head{display:flex;align-items:baseline;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
.vehicle-toggle{margin-left:auto;display:inline-flex;background:rgba(0,0,0,.05);border-radius:8px;padding:3px;flex-shrink:0;}
.vt-btn{padding:5px 12px;border:none;background:transparent;color:var(--text2);font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;border-radius:5px;white-space:nowrap;letter-spacing:-0.01em;transition:all .15s var(--ease);}
.vt-btn:hover{color:var(--text)}
.vt-btn.active{background:#fff;color:var(--text);font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 1px rgba(0,0,0,.04);}
.chart-title{font-size:clamp(14px,1.5vw,16px);font-weight:600;color:var(--text);letter-spacing:-0.02em;}
.chart-sub{font-size:14px;color:var(--text3);font-weight:400;}
.chart-canvas{position:relative;width:100%;min-width:0;}
.chart-inner{height:100%;width:100%;min-width:0;}
.chart-legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:0.5px solid var(--separator);}
.chart-legend-item{display:inline-flex;align-items:center;gap:6px;font-size:14px;color:var(--text2);font-weight:500;}
.chart-legend-dot{width:9px;height:9px;border-radius:3px}

/* ── Report page ── */
.report-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;margin-bottom:20px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:var(--radius-xl);box-shadow:var(--shadow);}
.report-title{font-size:20px;font-weight:700;letter-spacing:-0.02em;}
.report-sub{font-size:14px;opacity:.8;margin-top:3px}
.report-back{background:rgba(255,255,255,.15);color:#fff;border:0.5px solid rgba(255,255,255,.2);padding:7px 14px;border-radius:100px;font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;transition:all .15s var(--ease);}
.report-back:hover{background:rgba(255,255,255,.25)}
.report-stat-row{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px;}
.report-stat-card{background:var(--bg-card);border-radius:var(--radius-lg);padding:16px 18px;box-shadow:var(--shadow-sm);}
.report-stat-label{font-size:14px;color:var(--text3);font-weight:500;margin-bottom:6px;}
.report-stat-value{font-size:22px;font-weight:700;color:var(--text);letter-spacing:-0.02em;font-family:var(--font-mono);}
.report-stat-sub{font-size:14px;color:var(--text4);margin-top:2px}
.report-pie-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
.pie-card{background:var(--bg-card);border-radius:var(--radius-xl);padding:20px;box-shadow:var(--shadow-sm);}
.pie-card-title{font-size:14.5px;font-weight:600;color:var(--text);letter-spacing:-0.01em}
.pie-card-sub{font-size:14px;color:var(--text3);margin:2px 0 14px}
.pie-canvas-wrap{height:220px;position:relative}
.pie-legend{margin-top:12px;display:flex;flex-direction:column;gap:6px}
.pie-legend-item{display:flex;align-items:center;gap:8px;font-size:14px}
.pie-legend-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0}
.pie-legend-label{flex:1;color:var(--text2);font-weight:500}
.pie-legend-val{color:var(--text3);font-size:14px;font-family:var(--font-mono)}

/* ── Success toast ── */
.toast{position:fixed;top:72px;right:28px;z-index:200;background:rgba(255,255,255,.95);backdrop-filter:blur(20px);border-radius:14px;box-shadow:var(--shadow-xl);padding:12px 16px;display:flex;align-items:center;gap:12px;min-width:280px;max-width:380px;border-left:3px solid var(--green);overflow:hidden;animation:toastSlideIn .35s var(--ease-out);cursor:pointer;}
.toast.hiding{animation:toastSlideOut .25s var(--ease) forwards}
.toast-icon{font-size:22px}
.toast-body{flex:1;min-width:0}
.toast-title{font-size:14px;font-weight:600;color:var(--text);letter-spacing:-0.01em}
.toast-msg{font-size:14px;color:var(--text3);margin-top:1px}
.toast-progress{position:absolute;left:0;bottom:0;height:2px;background:var(--green);width:100%}

/* ── Date range picker ── */
.drp-popup{position:fixed;z-index:100;width:340px;background:rgba(255,255,255,.98);backdrop-filter:blur(20px);border-radius:14px;border:0.5px solid var(--separator);box-shadow:var(--shadow-xl);padding:14px;display:none;}
.drp-popup.open{display:block}
.drp-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.drp-nav-btn{width:28px;height:28px;border-radius:100px;border:none;background:rgba(0,0,0,.05);color:var(--text2);font-size:14px;cursor:pointer;transition:background .12s;}
.drp-nav-btn:hover{background:rgba(0,0,0,.08);color:var(--blue)}
.drp-title{font-size:14px;font-weight:600;color:var(--text);letter-spacing:-0.01em}
.drp-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px}
.drp-weekday{text-align:center;padding:6px 0;font-size:14px;color:var(--text4);font-weight:600}
.drp-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px}
.drp-day{aspect-ratio:1;border:none;border-radius:100px;font-family:var(--font-mono);font-size:14px;background:transparent;color:var(--text);cursor:pointer;transition:background .12s;}
.drp-day:hover{background:rgba(0,0,0,.05)}
.drp-day.muted{color:var(--text5)}
.drp-day.today{font-weight:700;color:var(--blue)}
.drp-day.selected,.drp-day.range-start,.drp-day.range-end{background:var(--blue);color:#fff;font-weight:600;}
.drp-day.in-range{background:rgba(245,158,11,.15);color:var(--blue-hover);border-radius:0}
.drp-day.range-start{border-top-right-radius:0;border-bottom-right-radius:0}
.drp-day.range-end{border-top-left-radius:0;border-bottom-left-radius:0}
.drp-hint{margin-top:10px;padding:7px;text-align:center;font-size:14px;color:var(--text3);background:var(--bg-subtle);border-radius:8px;}
.drp-footer{display:flex;justify-content:space-between;align-items:center;margin-top:10px;gap:8px}
.drp-presets{display:flex;gap:4px}
.drp-preset-btn{padding:5px 10px;border-radius:100px;border:0.5px solid var(--separator-strong);background:#fff;color:var(--text2);font-family:inherit;font-size:14px;cursor:pointer;transition:all .12s;}
.drp-preset-btn:hover{background:var(--bg-subtle);border-color:var(--blue);color:var(--blue)}
.drp-apply-btn{padding:6px 14px;border-radius:100px;border:none;background:var(--blue);color:#fff;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;}
.drp-apply-btn:disabled{opacity:.4;cursor:not-allowed}

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
.pdf-field input,.pdf-field select{width:100%;padding:11px 12px;border:1px solid var(--separator-strong);border-radius:10px;font-family:inherit;font-size:15px;color:var(--text);background:#fff;}
.pdf-field input:focus,.pdf-field select:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.pdf-modal-foot{display:flex;gap:10px;padding:16px 20px;border-top:1px solid var(--separator);}
.pdf-btn-cancel{flex:1;padding:11px;border:1px solid var(--separator-strong);background:#fff;border-radius:10px;font-family:inherit;font-size:15px;font-weight:600;color:var(--text2);cursor:pointer;}
.pdf-btn-cancel:hover{background:var(--bg-subtle)}
.pdf-btn-go{flex:2;padding:11px;border:none;background:var(--blue);color:#fff;border-radius:10px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s var(--ease);}
.pdf-btn-go:hover{filter:brightness(1.05);transform:translateY(-1px);}

/* ════ RESPONSIVE ════ */
.main{padding:clamp(14px,2.5vw,28px);max-width:1800px;margin:0 auto;}
@media (max-width:1500px){
  .entry-layout{grid-template-columns:62fr 38fr}
  .entry-rows-header{display:none}
  .entry-row{grid-template-columns:1fr 1fr;grid-template-areas:"driver driver" "plate time" "price distance" "summary action";gap:12px 14px;padding:16px 18px;}
  .entry-row > .er-driver{grid-area:driver}
  .entry-row > div:nth-child(2){grid-area:plate}
  .entry-row > .er-time-pair{grid-area:time}
  .entry-row > div:nth-child(4){grid-area:price}
  .entry-row > div:nth-child(5){grid-area:distance}
  .entry-row > .er-summary{grid-area:summary;flex-direction:row;gap:14px}
  .entry-row > div:last-child{grid-area:action;text-align:right !important}
  .entry-row > div:nth-child(2)::before{content:"ทะเบียน";display:block;font-size:14px;color:var(--text4);font-weight:600;margin-bottom:4px;}
  .entry-row > .er-time-pair::before{content:"เวลา";display:block;font-size:14px;color:var(--text4);font-weight:600;margin-bottom:4px;grid-column:1/-1;}
  .entry-row > div:nth-child(5)::before{content:"ระยะ (km)";display:block;font-size:14px;color:var(--text4);font-weight:600;margin-bottom:4px;}
}
@media (max-width:1200px){.entry-layout{grid-template-columns:1fr;gap:14px}.jobs-panel{position:static;max-height:500px}}
@media (max-width:1024px){
  .topnav-user{max-width:130px}.entry-card-head-left{gap:10px}.entry-export-btn{padding:7px 11px}
  .dual-grid{grid-template-columns:1fr}.dual-grid .card{min-height:400px;max-height:520px}
  .report-pie-grid{grid-template-columns:1fr 1fr}
  .fuel-table thead th:nth-child(5),.fuel-table tbody td:nth-child(5),
  .fuel-table thead th:nth-child(6),.fuel-table tbody td:nth-child(6){display:none}
}
@media (max-width:900px){
  .topnav-toggle{display:inline-flex}.topnav-main{padding:0 16px;gap:12px}
  .topnav-menu{position:absolute;top:52px;left:0;right:0;background:rgba(255,255,255,.95);backdrop-filter:blur(20px);flex-direction:column;align-items:stretch;gap:0;padding:8px;border-bottom:0.5px solid var(--separator);box-shadow:0 8px 24px rgba(0,0,0,.08);display:none;}
  .topnav-menu.open{display:flex}.topnav-menu .nav-item{width:100%;justify-content:flex-start;padding:11px 14px;}
  .topnav-time{display:none}.topnav-filters{padding:0 16px;gap:10px}.filter-group-label{display:none}
  .main{padding:18px 16px}.report-stat-row{grid-template-columns:repeat(2,1fr)}.report-pie-grid{grid-template-columns:1fr}
  .search-pill input{width:140px}
  .topnav-user-name{display:none}.topnav-user{padding:4px;max-width:none}
  .entry-card-head{padding:14px 16px}.entry-oil-tabs{padding:10px 16px}
  .entry-oil-mini{flex-shrink:0;padding:5px 10px}.entry-oil-mini .entry-oil-label{display:none}
  .fuel-table thead th:nth-child(1),.fuel-table tbody td:nth-child(1),
  .fuel-table thead th:nth-child(4),.fuel-table tbody td:nth-child(4),
  .fuel-table thead th:nth-child(7),.fuel-table tbody td:nth-child(7),
  .fuel-table thead th:nth-child(9),.fuel-table tbody td:nth-child(9),
  .fuel-table thead th:nth-child(10),.fuel-table tbody td:nth-child(10){display:none}
}
@media (max-width:600px){
  .entry-row{grid-template-columns:1fr;grid-template-areas:"driver" "plate" "time" "price" "distance" "summary" "action";gap:8px;}
  .entry-card-head-left{flex-wrap:wrap}
  .topnav-brand .title-text{display:none}
  .report-stat-row{grid-template-columns:repeat(2,1fr)}
}
@media (max-width:1280px){.report-stat-row{grid-template-columns:repeat(3,1fr)}}
@media (max-width:640px){
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
  .chart-card{padding:14px}
  .topnav-filters{gap:12px;padding:8px 14px;height:auto;flex-wrap:nowrap}
  .pill-select,.pill-date,.date-trigger-pill{min-width:auto}
}
</style>
</head>
<body>

@php
  $currentUser = request()->filled('create_by') ? request('create_by') : 'Guest';
  $userQuery = $currentUser !== 'Guest' ? '?create_by='.urlencode($currentUser) : '';
  $privilegedUsers = ['จัน','kanitin2','test101'];
  $isPrivileged = in_array(trim($currentUser), $privilegedUsers, true);
  // Whitelist คนขับหลัก (ต้องตรงกับ JS ALLOWED_DRIVERS)
  $allowedDrivers = ['บังเดช','กอลฟ์','เก่ง','หรั่ง','เอ้','แซม','เอ','แฟงค์','yuth','แมน','กบ','joey'];
@endphp

<nav class="topnav">
  <div class="topnav-main">
    <div class="topnav-brand">
      <div class="logo">⛽</div>
      <div class="title-text">ติดตามน้ำมัน</div>
    </div>
    <button type="button" class="topnav-toggle" onclick="toggleTopMenu()" aria-label="menu">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="topnav-menu" id="topMenu">
      <a class="nav-item" id="navReport" href="#" onclick="event.preventDefault();showReportPage();closeMobileMenu();">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="3" y1="20" x2="21" y2="20"/></svg></span>
        <span>รายงาน</span>
      </a>
      <a class="nav-item" href="{{ url('/service').$userQuery }}">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
        <span>Service</span>
      </a>
      <a class="nav-item" href="http://server_update:8000/solist{{ $userQuery }}">
        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg></span>
        <span>SO List</span>
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

  <div class="topnav-filters">
    <div class="filter-group">
      <span class="filter-group-label">มุมมอง</span>
      <div class="segmented">
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="button" class="seg-btn {{ $view===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
        @endforeach
      </div>
    </div>

    @if($view==='day')
    @php
      $dateFrom = request('date_from', request('date', $filterDay));
      $dateTo   = request('date_to',   request('date', $filterDay));
    @endphp
    <div class="filter-group">
      <span class="filter-group-label">ช่วงวันที่</span>
      <div class="drp-wrap" data-from="{{ $dateFrom }}" data-to="{{ $dateTo }}" style="position:relative">
        <button type="button" class="date-trigger-pill" id="drpTrigger" onclick="drpToggle(event)">
          <span id="drpLabel" style="flex:1">—</span>
          <span class="arrow">⌄</span>
        </button>
        <div class="drp-popup" id="drpPopup">
          <div class="drp-header">
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(-1)">‹</button>
            <div class="drp-title" id="drpMonthTitle">—</div>
            <button type="button" class="drp-nav-btn" onclick="drpNavMonth(1)">›</button>
          </div>
          <div class="drp-weekdays">
            <div class="drp-weekday">อา</div><div class="drp-weekday">จ</div><div class="drp-weekday">อ</div>
            <div class="drp-weekday">พ</div><div class="drp-weekday">พฤ</div><div class="drp-weekday">ศ</div>
            <div class="drp-weekday">ส</div>
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
    @elseif($view==='year')
    <div class="filter-group">
      <span class="filter-group-label">ปี</span>
      <select class="pill-select" id="yearPicker" onchange="onYearChange()">
        @php
          $savedYear = request('year', date('Y'));
          $yearMax = max((int)date('Y') + 2, (int)$savedYear);
        @endphp
        @for($y=$yearMax;$y>=2020;$y--)
        <option value="{{ $y }}" {{ $savedYear==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    @elseif($view!=='all')
    <div class="filter-group">
      <span class="filter-group-label">เดือน</span>
      <input type="month" class="pill-date" id="monthPicker" value="{{ $filterMonth }}" onchange="submitFilter()">
    </div>
    @endif

    <div class="filter-group">
      <span class="filter-group-label">คนขับ</span>
      <select class="pill-select" id="driverPicker" onchange="submitFilter()">
        <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @php
          $normDrv = function($s){
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
          };
          if($view === 'day'){
            $drvSource = collect($logs)->pluck('driver_name')->map(fn($n)=>trim((string)$n))->filter()->unique()->values()->all();
            $useWhitelist = false;
          } else {
            $drvSource = $drivers;
            $useWhitelist = true;
          }
          $allowedNorm = array_map($normDrv, $allowedDrivers);
          $seenDrv = [];
        @endphp
        @foreach($drvSource as $d)
          @php $nd = $normDrv($d); @endphp
          @if(!in_array($nd, $seenDrv, true) && (!$useWhitelist || in_array($nd, $allowedNorm, true)))
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

@if(session('success'))
<div class="toast" id="successToast">
  <div class="toast-icon">✓</div>
  <div class="toast-body">
    <div class="toast-title">บันทึกสำเร็จ</div>
    <div class="toast-msg">{{ session('success') }}</div>
  </div>
  <div class="toast-progress" id="toastProgress"></div>
</div>
<script>
(function(){
  var D=5000,t=document.getElementById('successToast'),b=document.getElementById('toastProgress');
  if(!t)return;
  b.style.transition='width '+D+'ms linear';
  requestAnimationFrame(function(){requestAnimationFrame(function(){b.style.width='0%';});});
  setTimeout(function(){t.classList.add('hiding');setTimeout(function(){t.remove();},280);},D);
  t.addEventListener('click',function(){t.classList.add('hiding');setTimeout(function(){t.remove();},280);});
})();
</script>
@endif

<main class="main">
<div id="pageTracking">

  @php
    $plateList=['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','805','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
    foreach($plates as $dbP){if(!in_array($dbP,$plateList))$plateList[]=$dbP;}
  @endphp

  @if($view === 'day' && $isPrivileged)
  <div class="entry-layout">
    <div class="entry-card">
      <div class="entry-card-head">
        <div class="entry-card-head-left">
          <div class="entry-icon">⛽</div>
          <div class="entry-titlewrap">
            <span class="entry-title">บันทึกการเติมน้ำมัน</span>
            <span class="entry-sub" id="entrySub">เลือกวันที่เพื่อโหลดคนขับของวันนั้น</span>
          </div>
          <input type="hidden" id="il-work-date" value="{{ request('date_from', request('date', $filterDay ?? date('Y-m-d'))) }}">
          <div class="entry-oil-mini">
            <span class="entry-oil-label" id="ilOilPriceLabel">ราคาดีเซล</span>
            <span class="entry-oil-num">฿<span id="ilOilPriceShow">—</span></span>
            <button type="button" id="ilBtnRefresh" class="entry-oil-refresh" onclick="ilRefreshOilPrice()" title="รีเฟรช">↻</button>
          </div>
          <button type="button" class="entry-export-btn" onclick="openPdfRangeModal()" title="ดาวน์โหลดรายงาน PDF">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export PDF</span>
          </button>
        </div>
      </div>

      <div class="entry-oil-tabs">
        @php $oilBtns=['diesel'=>'ดีเซล','95'=>'95','benzin95'=>'เบนซิน 95','91'=>'91','e20'=>'E20','e85'=>'E85']; @endphp
        @foreach($oilBtns as $oilKey=>$oilLabel)
        <button type="button" onclick="ilSwitchOilType('{{ $oilKey }}')" id="ilBtnOil-{{ $oilKey }}" class="entry-oil-tab {{ $oilKey==='diesel'?'active':'' }}">{{ $oilLabel }}</button>
        @endforeach
        <span class="entry-oil-status" id="ilOilPriceStatus">กำลังโหลด</span>
        <span class="entry-oil-live loading" id="ilLiveWrap">
          <span class="dot" id="ilLiveDot"></span>
          <span id="ilLiveLabel">กำลังดึง</span>
        </span>
      </div>

      <input type="hidden" id="il-price-per-liter" value="">

      <div class="entry-loading-row" id="entryLoadingHint" style="display:none">
        <span class="spinner"></span> กำลังโหลดข้อมูลคนขับ...
      </div>

      <div class="entry-rows-wrap">
        <div class="entry-rows-header">
          <div>คนขับ</div>
          <div>ทะเบียนรถ</div>
          <div>เวลา</div>
          <div>ค่าน้ำมัน (฿)</div>
          <div>ระยะ (km)</div>
          <div>สรุป</div>
          <div>บันทึก</div>
        </div>
        <div id="entryRowsBody">
          <div class="entry-empty">เลือกวันที่เพื่อแสดงรายชื่อคนขับ</div>
        </div>
      </div>
    </div>

    <aside class="jobs-panel" id="inlineJobsWrap">
      <div class="jobs-panel-head">
        <div class="jobs-panel-title">
          <span class="ico">📋</span>
          <span id="jobsPanelTitleText">รายการงาน</span>
        </div>
        <span id="ilJobDateChip" class="job-date-chip" style="display:none">วันนี้</span>
      </div>
      <div class="jobs-panel-body" id="inlineJobTableWrap">
        <div class="job-loading">คลิกที่แถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>
      </div>
    </aside>
  </div>
  @endif

  <div class="dual-grid {{ $view === 'day' ? 'single-col' : '' }}">
    <div class="card">
      <div class="card-head">
        <div class="card-title">
          รายการเติมน้ำมัน
          <span class="card-count" id="oilCount">{{ $logs->count() }}</span>
          <span class="card-meta">เรียงตามเวลาทำงาน</span>
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
          @php
            $rowNo = 0;
            // ── Carry-forward: ใช้ $allLogs (ทุก row) group ตามทะเบียน ──
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
            // เรียงคนใน whitelist ขึ้นก่อน
            $normName = function($s){
              $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
              return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
            };
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

    @if($view !== 'day')
    <div class="card">
      <div class="card-head">
        <div class="card-title">
          อันดับคนขับ
          @php
            $uniqueDrivers = [];
            foreach($logs as $r){
              $n = $r['driver_name'] ?? '';
              if(!isset($uniqueDrivers[$n])) $uniqueDrivers[$n] = ['name'=>$n,'rounds'=>0,'distance'=>0,'liters'=>0,'price'=>0,'kml_sum'=>0,'kml_count'=>0];
              $uniqueDrivers[$n]['rounds']++;
              $uniqueDrivers[$n]['distance'] += $r['total_distance'] ?? 0;
              $uniqueDrivers[$n]['liters']   += $r['liters'] ?? 0;
              $uniqueDrivers[$n]['price']    += $r['total_price'] ?? 0;
              if(($r['km_per_liter'] ?? 0) > 0){
                $uniqueDrivers[$n]['kml_sum'] += $r['km_per_liter'];
                $uniqueDrivers[$n]['kml_count']++;
              }
            }
            $byPrice = $uniqueDrivers;
            uasort($byPrice, fn($a,$b)=> $b['price'] <=> $a['price']);
            $byDistance = $uniqueDrivers;
            uasort($byDistance, fn($a,$b)=> $b['distance'] <=> $a['distance']);
          @endphp
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
    @endif
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
</div>

<div id="pageReport" style="display:none">
  <div class="report-header">
    <div>
      <div class="report-title">สรุปรายงาน</div>
      <div class="report-sub">วิเคราะห์การใช้น้ำมันแยกตามคนขับ</div>
    </div>
    <button type="button" class="report-back" onclick="showTrackingPage()">← กลับ</button>
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
</div>

</main>

<script>
/* ═══════════════════════════════════════════════════════════════
  CONSTANTS
═══════════════════════════════════════════════════════════════ */
const ROUTE_STORE         = '{{ route("oil") }}';
const ROUTE_FILTER        = '{{ route("oil.filter") }}';
const ROUTE_SYNC_NG       = '{{ route("oil.syncNg") }}';
const ROUTE_SAVED_DRIVERS = '{{ url("/oil/saved-drivers") }}';
const CURRENT_USER        = @json($currentUser);
const IS_PRIVILEGED       = @json($isPrivileged);
const CSRF_TOKEN          = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const TZ                  = 'Asia/Bangkok';
const MAIN_VIEW           = @json($view);
const ALLOWED_DRIVERS     = @json($allowedDrivers);
window.PLATE_LIST         = @json($plateList);

// แสดงตัวเลขแบบไม่ปัด ตัด 0 ท้าย: 11.90→11.9, 12.00→12
function fmtN(v, max=2){ return (+(+v).toFixed(max)).toString(); }

/* ═══════════════════════════════════════════════════════════════
  NAV / TIME
═══════════════════════════════════════════════════════════════ */
function toggleTopMenu(){ document.getElementById('topMenu')?.classList.toggle('open'); }
function closeMobileMenu(){ if(window.innerWidth>900) return; document.getElementById('topMenu')?.classList.remove('open'); }
function nowThai(){ return new Date(new Date().toLocaleString('en-US',{timeZone:TZ})); }
function todayStr(){ const d=nowThai(); return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
function updateNavDate(){
  const el = document.getElementById('navDate');
  if(el) el.textContent = new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
}

/* ═══════════════════════════════════════════════════════════════
  FILTER FORM
═══════════════════════════════════════════════════════════════ */
function submitFilterForm(params){
  const form = document.createElement('form');
  form.method='POST'; form.action=ROUTE_FILTER; form.style.display='none';
  const add=(n,v)=>{ if(v==null||v==='')return; const i=document.createElement('input'); i.type='hidden'; i.name=n; i.value=v; form.appendChild(i); };
  add('_token',CSRF_TOKEN);
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
  submitFilterForm(params);
}
function submitFilter(){
  const params={view:MAIN_VIEW};
  const ds=document.getElementById('driverPicker'); if(ds&&ds.value)params.driver_name=ds.value;
  const ps=document.getElementById('platePicker'); if(ps&&ps.value)params.vehicle_id=ps.value;
  const me=document.getElementById('monthPicker'); if(me&&me.value)params.month=me.value;
  const ye=document.getElementById('yearPicker'); if(ye&&ye.value)params.year=ye.value;
  if(MAIN_VIEW==='day'){
    const wrap=document.querySelector('.drp-wrap');
    const from = drpFrom || wrap?.dataset.from;
    const to   = drpTo   || wrap?.dataset.to;
    if(from) params.date_from = from;
    if(to)   params.date_to   = to;
  }
  submitFilterForm(params);
}
function onYearChange(){
  const ye=document.getElementById('yearPicker');
  if(ye&&ye.value){ try{ sessionStorage.setItem('oilPickedYear',ye.value); }catch(e){} }
  submitFilter();
}
(function restoreYear(){
  try{
    const s=sessionStorage.getItem('oilPickedYear'); if(!s)return;
    document.addEventListener('DOMContentLoaded',()=>{
      const el=document.getElementById('yearPicker'); if(!el)return;
      if(Array.from(el.options).some(o=>o.value===s) && el.value!==s) el.value=s;
    });
  }catch(e){}
})();

/* ═══════════════════════════════════════════════════════════════
  OIL TABLE SEARCH + RANK SORT
═══════════════════════════════════════════════════════════════ */
let oilSearchQuery='';
function filterOilTable(q){ oilSearchQuery=q.toLowerCase(); renderOilPage(); }
function renderOilPage(){
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
  const listPrice=document.getElementById('rankListPrice');
  const listDist=document.getElementById('rankListDistance');
  if(listPrice && listDist){
    listPrice.style.display = (mode==='price') ? '' : 'none';
    listDist.style.display  = (mode==='distance') ? '' : 'none';
  }
  try{ sessionStorage.setItem('oilRankSort', mode); }catch(e){}
}
(function restoreRankSort(){
  try{
    const saved=sessionStorage.getItem('oilRankSort'); if(!saved)return;
    document.addEventListener('DOMContentLoaded',()=>{
      if(document.getElementById('rankListPrice') && document.getElementById('rankListDistance')) switchRankSort(saved);
    });
  }catch(e){}
})();

/* ═══════════════════════════════════════════════════════════════
  PAGE SWITCH (tracking / report)
═══════════════════════════════════════════════════════════════ */
function showReportPage(){
  document.getElementById('pageTracking').style.display='none';
  document.getElementById('pageReport').style.display='';
  document.querySelectorAll('.topnav-menu .nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('navReport')?.classList.add('active');
  if(typeof renderReportPage==='function') renderReportPage();
  window.scrollTo({top:0,behavior:'smooth'});
}
function showTrackingPage(){
  document.getElementById('pageTracking').style.display='';
  document.getElementById('pageReport').style.display='none';
  document.querySelectorAll('.topnav-menu .nav-item').forEach(n=>n.classList.remove('active'));
  window.scrollTo({top:0,behavior:'smooth'});
}

/* ═══════════════════════════════════════════════════════════════
  DRIVER NORMALIZE / WHITELIST
═══════════════════════════════════════════════════════════════ */
function _normalizeName(s){
  if(!s) return '';
  return String(s).replace(/[\u200B-\u200D\uFEFF]/g,'').replace(/\s+/g,' ').trim().toLowerCase();
}
const DRIVER_ALIASES = {
  'กอลฟ':'กอลฟ','กอลฟ์':'กอลฟ',
  'แฟงค':'แฟงค','แฟรงค':'แฟงค',
  'yuth':'yuth','ยุทร':'yuth','ยุท':'yuth',
  'joey':'joey','โจอี':'joey',
  'แซม':'แซม','แชม':'แซม',
};
function _normalizeDriver(s){
  let n = _normalizeName(s).replace(/\u0E4C/g,'');
  return DRIVER_ALIASES[n] || n;
}
const _allowedSet = new Set(ALLOWED_DRIVERS.map(_normalizeDriver));
function isAllowedDriver(name){ return _allowedSet.has(_normalizeDriver(name)); }

/* ═══════════════════════════════════════════════════════════════
  CHART: delivery (ส่งสำเร็จ/ไม่สำเร็จ)
═══════════════════════════════════════════════════════════════ */
let dlvChart=null;
@php
  $deliveryByDriver=[];
  foreach($logs as $log){
    $driver=$log['driver_name']??'ไม่ระบุ';
    if(!isset($deliveryByDriver[$driver])) $deliveryByDriver[$driver]=['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];
    $deliveryByDriver[$driver]['success']+=(int)($log['delivery_success']??$log['success_count']??$log['ok_count']??0);
    $deliveryByDriver[$driver]['fail']+=(int)($log['delivery_fail']??$log['fail_count']??$log['ng_count']??0);
  }
@endphp
const DLV_BY_DRIVER=@json($deliveryByDriver);
function renderDlv(){
  const drivers=Object.keys(DLV_BY_DRIVER).filter(d=>isAllowedDriver(d));
  if(drivers.length===0){ if(dlvChart)dlvChart.destroy(); document.getElementById('dlvLegend').innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>'; return; }
  const orderIdx=name=>{ const i=ALLOWED_DRIVERS.map(_normalizeDriver).indexOf(_normalizeDriver(name)); return i<0?999:i; };
  const sorted=drivers.map(d=>({name:d,s:DLV_BY_DRIVER[d].success,f:DLV_BY_DRIVER[d].fail})).sort((a,b)=>orderIdx(a.name)-orderIdx(b.name));
  const inner=document.getElementById('deliveryChartInner');
  if(inner){ inner.style.width='100%'; inner.style.height='100%'; }
  const labels=sorted.map(d=>d.name);
  if(dlvChart)dlvChart.destroy();
  dlvChart=new Chart(document.getElementById('deliveryChart'),{
    type:'bar',
    data:{labels,datasets:[
      {label:'ส่งสำเร็จ',data:sorted.map(d=>d.s),backgroundColor:'#10b981',borderRadius:{topLeft:0,topRight:0,bottomLeft:6,bottomRight:6},borderSkipped:false,stack:'s',maxBarThickness:50},
      {label:'ส่งไม่สำเร็จ',data:sorted.map(d=>d.f),backgroundColor:'#ef4444',borderRadius:{topLeft:6,topRight:6,bottomLeft:0,bottomRight:0},borderSkipped:false,stack:'s',maxBarThickness:50},
    ]},
    plugins:[ChartDataLabels],
    options:{
      responsive:true,maintainAspectRatio:false,
      layout:{padding:{top:20,left:10,right:10}},
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:ctx=>`${ctx.dataset.label}: ${ctx.raw} รายการ`,footer:items=>'รวม: '+items.reduce((s,i)=>s+i.raw,0)+' รายการ'}},
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
@php
  $plateFilterActive = ($filterPlate ?? 'all') !== 'all';
  $kmlByDriver=[];
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
  $costByDriver2=[];
  foreach($logs as $log){
    $plate=$log['vehicle_id']??'ไม่ระบุ';
    $driver=$log['driver_name']??'';
    $key = $plateFilterActive ? $plate : ($plate.'|'.$driver);
    if(!isset($costByDriver2[$key])) $costByDriver2[$key]=['price'=>0,'dist'=>0,'plate'=>$plate,'driver'=>($plateFilterActive ? '' : $driver)];
    $costByDriver2[$key]['price']+=(float)($log['total_price']??0);
    $costByDriver2[$key]['dist']+=(float)($log['total_distance']??0);
  }
@endphp
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
        tooltip:{callbacks:{label:ctx=>`เฉลี่ย: ${fmtN(ctx.raw)} km/L`,afterLabel:ctx=>{const d=drivers[ctx.dataIndex];return d.name?`คนขับ: ${d.name}`:'';}}},
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
        tooltip:{callbacks:{label:ctx=>`฿${fmtN(ctx.raw)} / km`,afterLabel:ctx=>{const d=drivers[ctx.dataIndex];const lines=[];if(d.name)lines.push(`คนขับ: ${d.name}`);lines.push(`รวม ฿${d.price.toLocaleString(undefined,{maximumFractionDigits:0})} / ${d.dist.toLocaleString()} km`);return lines;}}},
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
  DATE RANGE PICKER
═══════════════════════════════════════════════════════════════ */
const TH_MONTHS_SHORT=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
let drpViewYear=null,drpViewMonth=null,drpFrom=null,drpTo=null;
function drpPad(n){return String(n).padStart(2,'0');}
function drpFmt(d){return `${d.getFullYear()}-${drpPad(d.getMonth()+1)}-${drpPad(d.getDate())}`;}
function drpParse(s){if(!s)return null;const p=s.split('-');return new Date(parseInt(p[0]),parseInt(p[1])-1,parseInt(p[2]));}
function drpFormatLabel(f,t){
  if(!f)return 'เลือกช่วง';
  const fd=drpParse(f);
  if(!t||f===t)return `${fd.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} ${fd.getFullYear()+543}`;
  const td=drpParse(t);
  if(fd.getFullYear()===td.getFullYear()&&fd.getMonth()===td.getMonth())return `${fd.getDate()}–${td.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} ${fd.getFullYear()+543}`;
  return `${fd.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} – ${td.getDate()} ${TH_MONTHS_SHORT[td.getMonth()]} ${fd.getFullYear()+543}`;
}
function drpUpdateLabel(){const lbl=document.getElementById('drpLabel');if(lbl)lbl.textContent=drpFormatLabel(drpFrom,drpTo);}
function drpPositionPopup(){
  const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');
  if(!pop||!trg)return;
  const r=trg.getBoundingClientRect(),popW=340,popH=420;
  let top=r.bottom+6; if(top+popH>window.innerHeight-8)top=Math.max(8,r.top-popH-6);
  let left=r.left; if(left+popW>window.innerWidth-8)left=Math.max(8,window.innerWidth-popW-8);
  pop.style.top=top+'px'; pop.style.left=left+'px';
}
function drpToggle(e){
  if(e)e.stopPropagation();
  const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');
  if(pop.classList.contains('open')){pop.classList.remove('open');trg.classList.remove('active');}
  else{const a=drpFrom?drpParse(drpFrom):new Date();drpViewYear=a.getFullYear();drpViewMonth=a.getMonth();drpRender();drpPositionPopup();pop.classList.add('open');trg.classList.add('active');}
}
window.addEventListener('resize',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();});
window.addEventListener('scroll',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();},true);
function drpNavMonth(d){drpViewMonth+=d;if(drpViewMonth<0){drpViewMonth=11;drpViewYear--;}if(drpViewMonth>11){drpViewMonth=0;drpViewYear++;}drpRender();}
function drpRender(){
  document.getElementById('drpMonthTitle').textContent=`${TH_MONTHS_SHORT[drpViewMonth]} ${drpViewYear+543}`;
  const grid=document.getElementById('drpDays');
  const fw=new Date(drpViewYear,drpViewMonth,1).getDay();
  const dim=new Date(drpViewYear,drpViewMonth+1,0).getDate();
  const pmd=new Date(drpViewYear,drpViewMonth,0).getDate();
  const ts=drpFmt(new Date());
  let h='';
  for(let i=fw-1;i>=0;i--){const d=pmd-i;const y=drpViewMonth===0?drpViewYear-1:drpViewYear;const m=drpViewMonth===0?11:drpViewMonth-1;h+=drpDayBtn(`${y}-${drpPad(m+1)}-${drpPad(d)}`,d,true,ts);}
  for(let d=1;d<=dim;d++){h+=drpDayBtn(`${drpViewYear}-${drpPad(drpViewMonth+1)}-${drpPad(d)}`,d,false,ts);}
  const rem=(7-((fw+dim)%7))%7;
  for(let d=1;d<=rem;d++){const y=drpViewMonth===11?drpViewYear+1:drpViewYear;const m=drpViewMonth===11?0:drpViewMonth+1;h+=drpDayBtn(`${y}-${drpPad(m+1)}-${drpPad(d)}`,d,true,ts);}
  grid.innerHTML=h;
  const hint=document.getElementById('drpHint');
  hint.textContent = !drpFrom ? 'เลือกวันเริ่มต้น' : drpFormatLabel(drpFrom,drpTo);
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
async function drpApply(){
  if(!drpFrom)return;
  const to=drpTo||drpFrom;
  const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');
  if(pop)pop.classList.remove('open');
  if(trg)trg.classList.remove('active');

  const wrap=document.querySelector('.drp-wrap[data-from]');
  const prevFrom=wrap?.dataset.from||'';
  const prevTo=wrap?.dataset.to||'';

  // ★ เฉพาะ privileged + day view: โหลดคนขับก่อน แล้วค่อยตัดสินใจ
  if(IS_PRIVILEGED && MAIN_VIEW==='day' && document.getElementById('entryRowsBody')){
    const workDateInput=document.getElementById('il-work-date');
    if(workDateInput) workDateInput.value=drpFrom;
    drpUpdateLabel();

    const pending=await ilOnDateChange();

    if(pending>0){
      // มีคนขับที่ยังไม่กรอก → อยู่หน้าเดิม (entry card แสดงแล้ว)
      // อัปเดต wrap data เพื่อไม่ reload ซ้ำถ้ากด ตกลง อีกรอบ
      if(wrap){wrap.dataset.from=drpFrom;wrap.dataset.to=to;}
      return;
    }
    // pending===0 (กรอกครบ) หรือ pending===-1 (ไม่มีข้อมูล/error) → ย้อนกลับวันที่เดิม
    if(workDateInput) workDateInput.value=prevFrom;
    drpFrom=prevFrom; drpTo=prevTo;
    drpUpdateLabel();
    // โหลดคนขับวันเดิมกลับมา
    if(prevFrom){
      workDateInput.value=prevFrom;
      await ilOnDateChange();
    }
    return;
  }

  // non-privileged หรือ non-day view → reload ปกติ
  drpUpdateLabel();
  if(drpFrom!==prevFrom || to!==prevTo){
    const params={view:'day',date_from:drpFrom,date_to:to};
    const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;
    const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;
    submitFilterForm(params);
  }
}
document.addEventListener('click',(e)=>{const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');if(!pop||!pop.classList.contains('open'))return;if(pop.contains(e.target)||trg.contains(e.target))return;pop.classList.remove('open');trg.classList.remove('active');});
function drpInit(){
  const wrap=document.querySelector('.drp-wrap[data-from]');if(!wrap)return;
  if(wrap.dataset.from)drpFrom=wrap.dataset.from;
  if(wrap.dataset.to)drpTo=wrap.dataset.to;
  drpUpdateLabel();
  const grid=document.getElementById('drpDays');
  if(grid){
    grid.addEventListener('mousedown',drpStartDrag);document.addEventListener('mousemove',drpMoveDrag);document.addEventListener('mouseup',drpEndDrag);
    grid.addEventListener('touchstart',drpStartDrag,{passive:false});document.addEventListener('touchmove',drpMoveDrag,{passive:false});document.addEventListener('touchend',drpEndDrag);
  }
}

/* ═══════════════════════════════════════════════════════════════
  OIL PRICE — ดึง API ครั้งเดียว cache ทุก tab
═══════════════════════════════════════════════════════════════ */
let OIL_PRICE_CACHE=null;     // station ดิบจาก API
let _oilFetchPromise=null;
async function _fetchOilOnce(){
  if(OIL_PRICE_CACHE) return OIL_PRICE_CACHE;
  if(_oilFetchPromise) return _oilFetchPromise;
  _oilFetchPromise=(async()=>{
    try{
      const r=await Promise.race([fetch('https://api.chnwt.dev/thai-oil-api/latest'),new Promise((_,rj)=>setTimeout(()=>rj(new Error('t')),8000))]);
      if(r.ok){ const json=await r.json(); OIL_PRICE_CACHE=json?.response?.stations||null; }
    }catch(_){ OIL_PRICE_CACHE=null; }
    return OIL_PRICE_CACHE;
  })();
  return _oilFetchPromise;
}
function _extractPrice(stations, cfg){
  if(!stations) return null;
  const prices=[];
  for(const station of Object.values(stations))
    for(const fuel of Object.values(station))
      if(fuel?.name?.includes(cfg.matchName) && fuel?.price){
        const n=parseFloat(fuel.price);
        if(!isNaN(n) && n>0 && n<cfg.maxPrice) prices.push(n);
      }
  if(prices.length===0) return null;
  const freq={};
  for(const p of prices){ const k=p.toFixed(2); freq[k]=(freq[k]||0)+1; }
  return parseFloat(Object.entries(freq).sort((a,b)=>b[1]-a[1])[0][0]);
}
const OIL_CONFIG={
  'diesel':{label:'ดีเซล',matchName:'ดีเซล',maxPrice:55},
  '95':{label:'แก๊สโซฮอล์ 95',matchName:'แก๊สโซฮอล์ 95',maxPrice:50},
  'benzin95':{label:'เบนซิน 95',matchName:'เบนซิน 95',maxPrice:60},
  '91':{label:'แก๊สโซฮอล์ 91',matchName:'แก๊สโซฮอล์ 91',maxPrice:50},
  'e20':{label:'แก๊สโซฮอล์ E20',matchName:'E20',maxPrice:45},
  'e85':{label:'แก๊สโซฮอล์ E85',matchName:'E85',maxPrice:40},
};
let ilCurrentOilType='diesel';
function ilSwitchOilType(t){
  ilCurrentOilType=t;
  document.querySelectorAll('.entry-oil-tab').forEach(b=>b.classList.remove('active'));
  document.getElementById('ilBtnOil-'+t)?.classList.add('active');
  ilLoadOilPrice(t);
}
async function ilRefreshOilPrice(){
  OIL_PRICE_CACHE=null; _oilFetchPromise=null;   // refresh = ล้าง cache
  const btn=document.getElementById('ilBtnRefresh');
  if(btn){btn.disabled=true;btn.style.opacity='.5';}
  await ilLoadOilPrice(ilCurrentOilType);
  if(btn){btn.disabled=false;btn.style.opacity='1';}
}
async function ilLoadOilPrice(type){
  const cfg=OIL_CONFIG[type]??OIL_CONFIG['diesel'];
  const labelEl=document.getElementById('ilOilPriceLabel');if(labelEl)labelEl.textContent=`ราคา${cfg.label}`;
  const showEl=document.getElementById('ilOilPriceShow');if(showEl)showEl.textContent='...';
  const statusEl=document.getElementById('ilOilPriceStatus');if(statusEl)statusEl.textContent='กำลังดึง';
  const wrapEl=document.getElementById('ilLiveWrap');if(wrapEl)wrapEl.classList.add('loading');
  const liveLabel=document.getElementById('ilLiveLabel');if(liveLabel)liveLabel.textContent='กำลังดึง';
  const pplEl=document.getElementById('il-price-per-liter');if(pplEl)pplEl.value='';

  const stations=await _fetchOilOnce();   // ดึงครั้งเดียว
  const fetched=_extractPrice(stations, cfg);

  const now=new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
  if(fetched){
    if(showEl)showEl.textContent=fetched.toFixed(2);
    if(statusEl)statusEl.textContent=`อัปเดต ${now}`;
    if(wrapEl)wrapEl.classList.remove('loading');
    if(liveLabel)liveLabel.textContent='Live';
    if(pplEl)pplEl.value=fetched.toFixed(2);
  }else{
    if(showEl)showEl.textContent='—';
    if(statusEl)statusEl.textContent=`ดึงไม่ได้ ${now}`;
    if(wrapEl)wrapEl.classList.remove('loading');
    if(liveLabel)liveLabel.textContent='ออฟไลน์';
    if(pplEl)pplEl.value='';
  }
  if(typeof erUpdateAllRows==='function') erUpdateAllRows();
}

/* ═══════════════════════════════════════════════════════════════
  JOBS — ดึง API ครั้งเดียวต่อวันที่ แล้วจัดข้อมูลทีเดียว
═══════════════════════════════════════════════════════════════ */
const JOB_API_BASE='http://server_update:8000/api/getDeliveryPersonByDate';
const jobFetched={};        // [date] => true (ดึง+จัดแล้ว)
const JOBS_PROCESSED={};    // [date] => {whitelist:{name:{name,jobs}}, auto:{...}}

async function fetchJobsByDate(dateStr){
  if(jobFetched[dateStr]) return;   // ดึง+จัดไปแล้ว ไม่ทำซ้ำ
  jobFetched[dateStr]=true;
  let drivers=[];
  try{
    const res=await fetch(`${JOB_API_BASE}?date=${dateStr}`);
    if(!res.ok) throw new Error('HTTP '+res.status);
    const json=await res.json();
    drivers=(json.data||[]).map(b=>({
      driver_name:b.bill_out_by||'ไม่ระบุ',
      jobs:(b.jobs||[]).map(j=>({bill_no:j.bill_no||'',so_id:j.so_id||'',customer_name:j.customer_name||'',bill_in_by:j.bill_in_by||'',status:j.delivery_status||'',note:j.reason||''})),
    }));
  }catch(e){
    console.warn('fetchJobsByDate:',e);
    drivers=[];
  }
  // ── จัดข้อมูลทีเดียว: แยก whitelist / auto ──
  const whitelist={}, auto={};
  drivers.forEach(d=>{
    const n=d.driver_name||''; if(!n) return;
    const bucket = isAllowedDriver(n) ? whitelist : auto;
    if(!bucket[n]) bucket[n]={name:n, jobs:[]};
    (d.jobs||[]).forEach(j=>bucket[n].jobs.push(j));
  });
  JOBS_PROCESSED[dateStr]={whitelist, auto};
}

/* ═══════════════════════════════════════════════════════════════
  SAVED DRIVERS — อ่านจาก DOM + API (เก็บไว้ตามที่ขอ)
═══════════════════════════════════════════════════════════════ */
const SAVED_DRIVERS_CACHE={};
const SESSION_SAVED={};

function _readSavedDriversFromDOM(date){
  const set=new Set();
  if(!date) return set;
  const parts=date.split('-'); if(parts.length!==3) return set;
  const target=`${parts[2]}/${parts[1]}/${parts[0]}`;  // DD/MM/YYYY
  document.querySelectorAll('#oilTbody tr[data-driver]').forEach(tr=>{
    const dateEl=tr.querySelector('.date-pill'), nameEl=tr.querySelector('.driver-name');
    if(!dateEl||!nameEl) return;
    if((dateEl.getAttribute('title')||'').trim()===target){
      const name=(nameEl.textContent||'').trim();
      if(name && name!=='—') set.add(name);
    }
  });
  return set;
}
async function fetchSavedDrivers(date){
  if(!date) return new Set();
  if(SAVED_DRIVERS_CACHE[date]) return SAVED_DRIVERS_CACHE[date];
  const fromDOM=_readSavedDriversFromDOM(date);
  try{
    const res=await fetch(`${ROUTE_SAVED_DRIVERS}?date=${encodeURIComponent(date)}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}).catch(()=>null);
    if(res && res.ok){
      const data=await res.json();
      let raw=[];
      if(Array.isArray(data)) raw=data;
      else if(Array.isArray(data.drivers)) raw=data.drivers;
      else if(Array.isArray(data.data)) raw=data.data;
      else if(Array.isArray(data.saved)) raw=data.saved;
      else if(Array.isArray(data.result)) raw=data.result;
      raw.forEach(item=>{
        let n='';
        if(typeof item==='string') n=item.trim();
        else if(item && typeof item==='object') n=(item.driver_name||item.name||item.driver||'').toString().trim();
        if(n) fromDOM.add(n);
      });
    }
  }catch(e){ /* API ไม่พร้อม — ใช้ DOM อย่างเดียว */ }
  SAVED_DRIVERS_CACHE[date]=fromDOM;
  return fromDOM;
}
function isDriverSaved(date, driverName){
  const n=_normalizeName(driverName);
  if(!n||!date) return false;
  for(const set of [SAVED_DRIVERS_CACHE[date], SESSION_SAVED[date]]){
    if(set){ for(const saved of set){ if(_normalizeName(saved)===n) return true; } }
  }
  return false;
}
function markDriverSaved(date, driverName){
  const n=(driverName||'').trim();
  if(!n||!date) return;
  if(!SESSION_SAVED[date]) SESSION_SAVED[date]=new Set();
  SESSION_SAVED[date].add(n);
  if(!SAVED_DRIVERS_CACHE[date]) SAVED_DRIVERS_CACHE[date]=new Set();
  SAVED_DRIVERS_CACHE[date].add(n);
}

function _jobStatusKind(j){
  const raw=(j.status||'').trim();
  const noteText=(j.note||'').trim();
  const eff=(noteText==='ส่งสำเร็จ'||noteText==='สำเร็จ')?'ส่งสำเร็จ':raw;
  if(eff.includes('สำเร็จ')&&!eff.includes('ไม่'))return 'ok';
  if(eff.includes('ไม่สำเร็จ')||eff.toLowerCase()==='ng'||eff.toLowerCase()==='fail')return 'fail';
  return 'pending';
}

/* ═══════════════════════════════════════════════════════════════
  ENTRY ROWS STATE + RENDER
═══════════════════════════════════════════════════════════════ */
const driverRowState={};
let ilIsLoadingDrivers=false, ilLastLoadedDate=null;

async function ilOnDateChange(){
  if(ilIsLoadingDrivers)return -1;
  const date=document.getElementById('il-work-date').value;if(!date)return -1;
  ilLastLoadedDate=date;
  ilIsLoadingDrivers=true;
  const hint=document.getElementById('entryLoadingHint');if(hint)hint.style.display='flex';
  document.getElementById('entryRowsBody').innerHTML='';
  document.getElementById('inlineJobTableWrap').innerHTML='<div class="job-loading">กำลังโหลด...</div>';
  let pendingCount=-1;
  try{
    delete SAVED_DRIVERS_CACHE[date];
    delete jobFetched[date];
    delete JOBS_PROCESSED[date];
    await Promise.all([fetchJobsByDate(date), fetchSavedDrivers(date)]);
    // นับคนขับที่ยังไม่กรอก
    const proc=JOBS_PROCESSED[date]||{whitelist:{},auto:{}};
    const unsaved=Object.values(proc.whitelist).filter(d=>!isDriverSaved(date,d.name));
    pendingCount=unsaved.length;
    ilRenderDriverRows(date);
    ilResetJobsPanel();
  }catch(e){
    console.warn(e);
    document.getElementById('entryRowsBody').innerHTML='<div class="entry-empty" style="color:var(--red)">โหลดข้อมูลไม่สำเร็จ</div>';
  }finally{
    ilIsLoadingDrivers=false;
    if(hint)hint.style.display='none';
  }
  return pendingCount;
}

// กันชื่อขยะจาก API (ไม่ใช่ชื่อคนขับ)
function isLikelyDriverName(name){
  const n=(name||'').trim();
  if(!n||n.length>20) return false;
  const banned=['ลูกค้า','เซ็นบิล','เซ็น','บิล','สาขา','จำกัด','บริษัท','หจก','ร้าน','คุณ','ไป','ที่','กับ'];
  for(const w of banned){ if(n.includes(w)) return false; }
  if((n.match(/\d/g)||[]).length>=4) return false;
  return true;
}

// ลงข้อมูลคนนอก whitelist อัตโนมัติ (09:00-18:00, ไม่เติมน้ำมัน)
const _autoStoreInFlight=new Set();
async function ilAutoStoreNonWhitelist(date, driverList){
  if(!driverList||driverList.length===0||!IS_PRIVILEGED) return;
  for(const d of driverList){
    const name=d.name;
    if(!isLikelyDriverName(name)) continue;
    if(!d.jobs||d.jobs.length===0) continue;
    if(isDriverSaved(date,name)) continue;
    const fireKey=date+'|'+_normalizeName(name);
    if(_autoStoreInFlight.has(fireKey)) continue;
    _autoStoreInFlight.add(fireKey);

    let okC=0,failC=0;
    d.jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});

    const fd=new FormData();
    fd.append('_token',CSRF_TOKEN);
    fd.append('work_date',date);
    fd.append('driver_name',name);
    fd.append('vehicle_id','-');
    fd.append('start_time',date+' 09:00:00');
    fd.append('end_time',date+' 18:00:00');
    fd.append('total_price',0);
    fd.append('ok',okC);
    fd.append('ng',failC);
    if(CURRENT_USER && CURRENT_USER!=='Guest') fd.append('create_by',CURRENT_USER);

    try{
      const res=await fetch(ROUTE_STORE,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:fd});
      if(res.ok||res.status===302){
        markDriverSaved(date,name);
        ilAppendLogRow({date,driver_name:name,vehicle_id:'-',start_h:9,start_m:0,end_h:18,end_m:0,total_price:0,total_distance:0,liters:0,km_per_liter:0,ok_count:okC,fail_count:failC});
        if(d.jobs.length>0) _syncNgJobs(date, name, d.jobs);
      }
    }catch(e){
      console.warn('auto-store error',name,e);
      _autoStoreInFlight.delete(fireKey);
    }
  }
}

function _syncNgJobs(date, driverName, jobs){
  fetch(ROUTE_SYNC_NG,{
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
    body:JSON.stringify({
      date,
      create_by:(CURRENT_USER && CURRENT_USER!=='Guest')?CURRENT_USER:null,
      jobs:jobs.map(j=>({bill_no:j.bill_no,so_id:j.so_id||'',driver_name:driverName,bill_in_by:j.bill_in_by||'',customer_name:j.customer_name||'',status:(j.status||'').trim(),note:j.note||''}))
    })
  }).catch(()=>{});
}

function ilRenderDriverRows(date){
  const tbody=document.getElementById('entryRowsBody');if(!tbody)return;
  const proc=JOBS_PROCESSED[date]||{whitelist:{},auto:{}};
  const driversMap=proc.whitelist;       // อ่านจากที่จัดไว้แล้ว ไม่จัดซ้ำ
  const autoDriversMap=proc.auto;

  ilAutoStoreNonWhitelist(date, Object.values(autoDriversMap));

  let driverList=Object.values(driversMap).filter(d=>!isDriverSaved(date,d.name));
  const totalCount=Object.keys(driversMap).length;
  const savedCount=totalCount-driverList.length;
  driverList.sort((a,b)=>b.jobs.length-a.jobs.length);

  if(driverList.length===0){
    if(totalCount===0){
      tbody.innerHTML='<div class="entry-empty">ไม่พบคนขับสำหรับวันที่นี้</div>';
      document.getElementById('entrySub').textContent='ไม่พบคนขับของวันนี้';
    }else{
      tbody.innerHTML=`<div class="entry-empty" style="color:var(--green-dark)">✓ บันทึกครบทุกคนแล้ว (${savedCount} คน)</div>`;
      document.getElementById('entrySub').textContent=`บันทึกครบ ${savedCount} คน`;
    }
    return;
  }

  let subText=`${driverList.length} คนขับ · กรอกข้อมูลแล้วกดบันทึก`;
  if(savedCount>0) subText+=` · บันทึกแล้ว ${savedCount} คน`;
  document.getElementById('entrySub').textContent=subText;

  const plateOpts=(window.PLATE_LIST||[]).map(p=>`<option value="${p}">${p}</option>`).join('');
  const _wd=document.getElementById('il-work-date')?.value || todayStr();

  tbody.innerHTML=driverList.map((d,idx)=>{
    const key=`row_${idx}_${d.name.replace(/[^a-zA-Z0-9ก-๙]/g,'_')}`;
    let okC=0,failC=0;
    d.jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});
    driverRowState[key]={driverName:d.name,jobs:d.jobs,okCount:okC,failCount:failC,sh:0,sm:0,eh:0,em:0,startDT:'',endDT:'',noFuel:false};
    const ini=(d.name||'?').trim().charAt(0).toUpperCase();
    return `<div class="entry-row" data-key="${key}" onclick="erFocusRow('${key}')">
      <div class="er-driver">
        <span class="er-driver-avatar">${ini}</span>
        <div class="er-driver-info">
          <div class="er-driver-name" title="${d.name}">${d.name}</div>
          <div class="er-driver-jobs">${d.jobs.length} งาน · <span class="er-ok">${okC} ✓</span>${failC>0?` · <span class="er-fail">${failC} ✕</span>`:''}</div>
        </div>
      </div>
      <div>
        <select class="er-plate-select" data-key="${key}" onchange="erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
          <option value="">— เลือกทะเบียน —</option>${plateOpts}
        </select>
      </div>
      <div class="er-time-pair">
        <input type="datetime-local" class="er-dt-input" id="${key}-start-dt" value="${_wd}T09:00" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
        <span class="er-time-arrow">→</span>
        <input type="datetime-local" class="er-dt-input" id="${key}-end-dt" value="${_wd}T18:00" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
      </div>
      <div>
        <label class="er-nofuel-check">
          <input type="checkbox" onchange="erToggleNoFuel('${key}', this.checked)" onfocus="erFocusRow('${key}')"> ไม่เติมน้ำมัน
        </label>
        <input type="text" inputmode="decimal" class="er-num-input" id="${key}-price" placeholder="ค่าน้ำมัน" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
      </div>
      <div>
        <input type="text" inputmode="decimal" class="er-num-input" id="${key}-dist" placeholder="250" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
      </div>
      <div class="er-summary" id="${key}-summary">
        <div class="er-summary-row"><span class="er-summary-label">L:</span><span class="er-summary-val empty">—</span></div>
        <div class="er-summary-row"><span class="er-summary-label">km/L:</span><span class="er-summary-val empty">—</span></div>
        <div class="er-summary-row"><span class="er-summary-label">฿/km:</span><span class="er-summary-val empty">—</span></div>
      </div>
      <div style="text-align:center">
        <button type="button" class="er-save-btn" id="${key}-save" onclick="event.stopPropagation();erSaveRow('${key}')">บันทึก</button>
      </div>
    </div>`;
  }).join('');

  // sync ค่าเริ่มต้นเข้า state
  Object.keys(driverRowState).forEach(k=>{
    if(document.getElementById(`${k}-start-dt`)) erUpdateDateTime(k);
    if(document.getElementById(`${k}-price`)) erUpdateRow(k);
  });
}

/* ═══════════════════════════════════════════════════════════════
  ENTRY ROW — INPUT HANDLERS
═══════════════════════════════════════════════════════════════ */
// กันพิมพ์ตัวอักษร — รับเฉพาะตัวเลข + จุดทศนิยม
function erSanitizeNum(el){
  let v=el.value.replace(/[^0-9.]/g,'');
  const parts=v.split('.');
  if(parts.length>2) v=parts[0]+'.'+parts.slice(1).join('');
  el.value=v;
}
// ติ๊ก "ไม่เติมน้ำมัน" → ซ่อน/disable ช่องราคา (บันทึก price=0)
function erToggleNoFuel(key, checked){
  const s=driverRowState[key];if(!s)return;
  s.noFuel=checked;
  const priceEl=document.getElementById(`${key}-price`);
  if(priceEl){
    if(checked){ priceEl.value=''; priceEl.disabled=true; priceEl.placeholder='ไม่เติม'; }
    else{ priceEl.disabled=false; priceEl.placeholder='ค่าน้ำมัน'; }
  }
  erUpdateRow(key);
}
function erUpdateRow(key){
  const s=driverRowState[key];if(!s)return;
  const priceEl=document.getElementById(`${key}-price`);
  const distEl=document.getElementById(`${key}-dist`);
  const price=s.noFuel?0:(parseFloat(priceEl?.value)||0);
  const dist=parseFloat(distEl?.value)||0;
  const ppl=parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
  const liters=(price>0 && ppl>0)?(price/ppl):0;
  const kml=(dist>0 && liters>0)?(dist/liters):0;
  const thbKm=(price>0 && dist>0)?(price/dist):0;
  s.price=price;s.distance=dist;s.liters=liters;s.kml=kml;s.thbKm=thbKm;
  const sum=document.getElementById(`${key}-summary`);
  if(sum){
    const litersTxt=liters>0?fmtN(liters)+' L':'<span class="empty">—</span>';
    let kmlCls='empty',kmlTxt='—';
    if(kml>0){ kmlTxt=fmtN(kml)+' km/L'; kmlCls=kml>=12?'green':(kml<9?'red':''); }
    const thbKmTxt=thbKm>0?'฿'+fmtN(thbKm):'<span class="empty">—</span>';
    sum.innerHTML=`
      <div class="er-summary-row"><span class="er-summary-label">L:</span><span class="er-summary-val ${liters>0?'':'empty'}">${litersTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">km/L:</span><span class="er-summary-val ${kmlCls}">${kmlTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">฿/km:</span><span class="er-summary-val ${thbKm>0?'':'empty'}">${thbKmTxt}</span></div>`;
  }
}
function erUpdateAllRows(){ Object.keys(driverRowState).forEach(k=>erUpdateRow(k)); }
function erUpdateDateTime(key){
  const s=driverRowState[key];if(!s)return;
  s.startDT=document.getElementById(`${key}-start-dt`)?.value||'';
  s.endDT=document.getElementById(`${key}-end-dt`)?.value||'';
  if(s.startDT){ const[h,m]=(s.startDT.split('T')[1]||'00:00').split(':').map(Number); s.sh=h||0; s.sm=m||0; }
  if(s.endDT){ const[h,m]=(s.endDT.split('T')[1]||'00:00').split(':').map(Number); s.eh=h||0; s.em=m||0; }
}

let _focusedRowKey=null;
function erFocusRow(key){
  if(_focusedRowKey===key)return;
  _focusedRowKey=key;
  document.querySelectorAll('.entry-row').forEach(r=>r.classList.toggle('focused', r.dataset.key===key));
  const s=driverRowState[key];if(!s)return;
  ilRenderJobsForDriver(s.driverName, s.jobs);
}

/* ═══════════════════════════════════════════════════════════════
  SAVE ROW
═══════════════════════════════════════════════════════════════ */
async function erSaveRow(key){
  const s=driverRowState[key];if(!s)return;
  const plate=document.querySelector(`.er-plate-select[data-key="${key}"]`)?.value||'';
  const btn=document.getElementById(`${key}-save`);
  const row=document.querySelector(`.entry-row[data-key="${key}"]`);

  const errors=[];
  if(!plate) errors.push('เลือกทะเบียนรถ');
  if(!s.noFuel){
    const priceRaw=document.getElementById(`${key}-price`)?.value??'';
    if(priceRaw===''||isNaN(parseFloat(priceRaw))) errors.push('ใส่ค่าน้ำมัน หรือติ๊ก "ไม่เติมน้ำมัน"');
    else if(parseFloat(priceRaw)<0) errors.push('ค่าน้ำมันติดลบไม่ได้');
  }
  if(!s.startDT||!s.endDT) errors.push('เลือกวันเวลาเริ่ม-สิ้นสุด');
  else if(new Date(s.endDT)<=new Date(s.startDT)) errors.push('เวลาสิ้นสุดต้องหลังเวลาเริ่ม');
  if(errors.length){
    btn.textContent='⚠ '+errors[0];
    setTimeout(()=>{btn.innerHTML='บันทึก';},2200);
    return;
  }

  const date=s.startDT.split('T')[0];
  row?.classList.add('saving');
  btn.disabled=true;
  btn.innerHTML='<span class="ic">⏳</span> กำลังบันทึก...';

  const toBackendDT=v=>v?v.replace('T',' ')+':00':'';
  const fd=new FormData();
  fd.append('_token',CSRF_TOKEN);
  fd.append('work_date',date);
  fd.append('driver_name',s.driverName);
  fd.append('vehicle_id',plate);
  fd.append('start_time',toBackendDT(s.startDT));
  fd.append('end_time',toBackendDT(s.endDT));
  fd.append('total_price',s.price);
  if(s.distance>0) fd.append('total_distance',s.distance);
  const ppl=parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
  if(ppl>0) fd.append('price_per_liter',ppl);
  if(s.liters>0) fd.append('liters',s.liters.toFixed(2));
  fd.append('ok',s.okCount);
  fd.append('ng',s.failCount);
  if(CURRENT_USER && CURRENT_USER!=='Guest') fd.append('create_by',CURRENT_USER);

  try{
    const res=await fetch(ROUTE_STORE,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:fd});
    if(!res.ok && res.status!==302) throw new Error('HTTP '+res.status);
    markDriverSaved(date,s.driverName);
    ilAppendLogRow({date,driver_name:s.driverName,vehicle_id:plate,start_h:s.sh,start_m:s.sm,end_h:s.eh,end_m:s.em,start_dt:s.startDT,end_dt:s.endDT,total_price:s.price,total_distance:s.distance,liters:s.liters,km_per_liter:s.kml,ok_count:s.okCount,fail_count:s.failCount});
    if(s.jobs.length>0) _syncNgJobs(date, s.driverName, s.jobs);
    if(_focusedRowKey===key){ _focusedRowKey=null; ilResetJobsPanel(); }
    delete driverRowState[key];
    showSaveToast(s.driverName);
    ilRenderDriverRows(date);
  }catch(e){
    console.warn('save error',e);
    row?.classList.remove('saving');
    btn.disabled=false;
    btn.innerHTML='⚠ ลองอีกครั้ง';
    setTimeout(()=>{btn.innerHTML='บันทึก';},2200);
  }
}

function showSaveToast(driverName){
  document.getElementById('saveToast')?.remove();
  const toast=document.createElement('div');
  toast.id='saveToast';
  toast.className='save-toast';
  toast.innerHTML=`<span class="save-toast-icon">✓</span><div class="save-toast-body"><div class="save-toast-title">บันทึกสำเร็จ</div><div class="save-toast-msg">${driverName}</div></div>`;
  document.body.appendChild(toast);
  setTimeout(()=>{toast.classList.add('hiding');setTimeout(()=>toast.remove(),250);},2500);
}

/* ═══════════════════════════════════════════════════════════════
  APPEND SAVED ROW เข้าตารางล่าง (ไม่ reload)
═══════════════════════════════════════════════════════════════ */
function ilAppendLogRow(r){
  const tbody=document.getElementById('oilTbody');if(!tbody)return;
  const emptyRow=tbody.querySelector('tr:not([data-driver])');if(emptyRow)emptyRow.remove();

  // duration
  let durText='',totalMin=0;
  if(r.start_dt && r.end_dt){
    totalMin=Math.round((new Date(r.end_dt)-new Date(r.start_dt))/60000);
  }else{
    let sm=(r.start_h||0)*60+(r.start_m||0), em=(r.end_h||0)*60+(r.end_m||0);
    if(em<sm) em+=1440;
    totalMin=em-sm;
  }
  if(totalMin>0){
    const days=Math.floor(totalMin/1440), hh=Math.floor((totalMin%1440)/60), mm=totalMin%60;
    if(days>0){ durText=days+' วัน'; if(hh>0)durText+=' '+hh+' ชม.'; if(mm>0)durText+=' '+mm+' น.'; }
    else if(hh>0&&mm>0)durText=hh+' ชม. '+mm+' น.';
    else if(hh>0)durText=hh+' ชม.';
    else durText=mm+' น.';
  }
  const pad=n=>String(n).padStart(2,'0');
  const timeText=`${pad(r.start_h||0)}:${pad(r.start_m||0)}-${pad(r.end_h||0)}:${pad(r.end_m||0)}`;
  const kml=r.km_per_liter||0;
  let kmlCls='km-mid'; if(kml>=13)kmlCls='km-good'; else if(kml>0&&kml<9)kmlCls='km-bad';
  const thbPerKm=(r.total_distance>0 && r.total_price>0)?(r.total_price/r.total_distance):0;
  const dp=(r.date||'').split('-');
  const dateText=dp.length===3?`${dp[2]}/${dp[1]}`:'—';
  const dateFull=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'';

  const tr=document.createElement('tr');
  tr.setAttribute('data-driver',(r.driver_name||'').toLowerCase());
  tr.style.background='rgba(59,130,246,.08)';
  tr.innerHTML=`
    <td class="row-idx" data-label="#">•</td>
    <td data-label="วันที่"><span class="date-pill" title="${dateFull}">${dateText}</span></td>
    <td data-label="คนขับ"><div class="driver-cell"><div class="driver-name" title="${r.driver_name}">${r.driver_name}</div><div class="driver-plate">${r.vehicle_id||'—'}</div></div></td>
    <td data-label="เวลา"><span class="time-pill">${timeText}</span></td>
    <td class="num" data-label="ชม.">${durText?'<span class="hour-pill">'+durText+'</span>':'<span style="color:var(--text4)">—</span>'}</td>
    <td class="num" data-label="ระยะ">${r.total_distance>0?Math.round(r.total_distance).toLocaleString()+' km':'—'}</td>
    <td class="num" data-label="ลิตร">${r.liters>0?fmtN(r.liters):'—'}</td>
    <td class="num" data-label="ค่าน้ำมัน">${r.total_price>0?'฿'+Math.round(r.total_price).toLocaleString():'—'}</td>
    <td class="num" data-label="KM/L">${kml>0?'<span class="'+kmlCls+'">'+fmtN(kml)+'</span>':'<span style="color:var(--text4)">—</span>'}</td>
    <td class="num" data-label="฿/km">${thbPerKm>0?'<span class="thb-km-val">฿'+thbPerKm.toFixed(2)+'</span>':'<span style="color:var(--text4)">—</span>'}</td>`;
  tbody.insertBefore(tr,tbody.firstChild);
  setTimeout(()=>{tr.style.transition='background 1s';tr.style.background='';},100);

  const c=document.getElementById('oilCount');
  if(c)c.textContent=document.querySelectorAll('#oilTbody tr[data-driver]').length;

  ilUpdateChartsAfterSave(r);
}

function ilUpdateChartsAfterSave(r){
  const name=r.driver_name;if(!name)return;
  if(isAllowedDriver(name)){
    if(!DLV_BY_DRIVER[name]) DLV_BY_DRIVER[name]={success:0,fail:0,plate:r.vehicle_id||''};
    DLV_BY_DRIVER[name].success+=(r.ok_count||0);
    DLV_BY_DRIVER[name].fail+=(r.fail_count||0);
  }
  const plate=r.vehicle_id||'ไม่ระบุ';
  const key=plate+'|'+name;
  if(r.km_per_liter>0){
    if(!KML_BY_DRIVER[key]) KML_BY_DRIVER[key]={sum:0,count:0,plate,driver:name};
    KML_BY_DRIVER[key].sum+=r.km_per_liter; KML_BY_DRIVER[key].count++;
  }
  if(!COST_BY_DRIVER[key]) COST_BY_DRIVER[key]={price:0,dist:0,plate,driver:name};
  COST_BY_DRIVER[key].price+=(r.total_price||0);
  COST_BY_DRIVER[key].dist+=(r.total_distance||0);
  try{ renderDlv(); renderKmlChart(); renderCostChart(); }catch(e){}
}

/* ═══════════════════════════════════════════════════════════════
  JOBS PANEL (ขวา)
═══════════════════════════════════════════════════════════════ */
function ilResetJobsPanel(){
  const wrap=document.getElementById('inlineJobTableWrap');
  if(wrap)wrap.innerHTML='<div class="job-loading">คลิกที่แถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>';
  const title=document.getElementById('jobsPanelTitleText');if(title)title.textContent='รายการงาน';
  const chip=document.getElementById('ilJobDateChip');if(chip)chip.style.display='none';
}
function ilRenderJobsForDriver(driverName, jobs){
  const wrap=document.getElementById('inlineJobTableWrap');if(!wrap)return;
  const title=document.getElementById('jobsPanelTitleText');if(title)title.textContent=driverName;
  const date=document.getElementById('il-work-date')?.value||'';
  const chip=document.getElementById('ilJobDateChip');
  if(chip&&date){const dp=date.split('-');chip.textContent=dp.length===3?`${dp[2]}/${dp[1]}`:date;chip.style.display='';}
  if(!jobs||jobs.length===0){ wrap.innerHTML='<div class="job-loading">ไม่มีรายการงาน</div>'; return; }

  let okC=0,failC=0;
  jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});

  let html=`<div class="jobs-summary-bar">
    <span class="jsb-chip"><strong>${jobs.length}</strong> งาน</span>
    <span class="jsb-chip ok"><strong>${okC}</strong> สำเร็จ</span>
    ${failC>0?`<span class="jsb-chip fail"><strong>${failC}</strong> ไม่สำเร็จ</span>`:''}
  </div>`;

  jobs.forEach(j=>{
    const kind=_jobStatusKind(j);
    const stTxt=kind==='ok'?'สำเร็จ':(kind==='fail'?'ไม่สำเร็จ':'รอ');
    const meta=[];
    if(j.so_id)meta.push(`<span class="dgj-meta-item"><span class="dgj-meta-label">SO</span> ${j.so_id}</span>`);
    if(j.bill_in_by)meta.push(`<span class="dgj-meta-item"><span class="dgj-meta-label">รับ</span> ${j.bill_in_by}</span>`);
    if(j.note)meta.push(`<span class="dgj-meta-item dgj-note"><span class="dgj-meta-label">หมายเหตุ</span> ${j.note}</span>`);
    html+=`<div class="dgj-row"><div class="dgj-main">
      <div class="dgj-top">
        <span class="dgj-bill">${j.bill_no||'—'}</span>
        <span class="dgj-customer" title="${j.customer_name||''}">${j.customer_name||'—'}</span>
        <span class="dgj-status ${kind}">${stTxt}</span>
      </div>
      ${meta.length?`<div class="dgj-meta">${meta.join('<span class="dgj-meta-sep">·</span>')}</div>`:''}
    </div></div>`;
  });
  wrap.innerHTML=html;
}

/* ═══════════════════════════════════════════════════════════════
  REPORT PAGE
═══════════════════════════════════════════════════════════════ */
@php
  $reportLogsArr = $logs->map(function($l){
    return ['driver'=>$l['driver_name']??'','price'=>(float)($l['total_price']??0),'liters'=>(float)($l['liters']??0),'hours'=>(float)($l['work_hours']??0),'distance'=>(float)($l['total_distance']??0)];
  })->values();
@endphp
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
  _renderPie('pieHours','pieHoursLegend',drivers.map(d=>({label:d,value:agg[d].hours})),v=>fmtN(v)+' ชม.');
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
@php
  $pdfLogsArr = $allLogs->map(function($l){
    return ['driver'=>$l['driver_name']??'','plate'=>$l['vehicle_id']??'','date'=>$l['work_date']??'','start'=>$l['start_time']??'','end'=>$l['end_time']??'','price'=>(float)($l['total_price']??0),'distance'=>(float)($l['total_distance']??0),'liters'=>(float)($l['liters']??0),'kml'=>(float)($l['km_per_liter']??0),'hours'=>(float)($l['work_hours']??0)];
  })->values();
@endphp
const PDF_LOGS=@json($pdfLogsArr);
const PDF_VIEW=@json($view);
let pdfMode='range';

function openPdfRangeModal(){
  document.getElementById('pdfModalOverlay')?.classList.add('open');
  const wd=document.getElementById('il-work-date')?.value||todayStr();
  const f=document.getElementById('pdfDateFrom'),t=document.getElementById('pdfDateTo');
  if(f&&!f.value)f.value=wd; if(t&&!t.value)t.value=wd;
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
  const btn=document.querySelector('.entry-export-btn');
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
    const COLORS=['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16','#06b6d4','#a855f7'];

    // ── จัดกลุ่มตามคนขับ ──
    const byDriver={};
    rows.forEach(r=>{
      const n=r.driver||'ไม่ระบุ';
      if(!byDriver[n])byDriver[n]={rows:[],price:0,dist:0,liters:0,ok:0,fail:0};
      byDriver[n].rows.push(r);
      byDriver[n].price+=r.price;byDriver[n].dist+=r.distance;byDriver[n].liters+=r.liters;
    });
    // เรียงคนขับตาม whitelist order แล้วตาม price
    const orderIdx=name=>{const i=ALLOWED_DRIVERS.map(_normalizeDriver).indexOf(_normalizeDriver(name));return i<0?999:i;};
    const driverNames=Object.keys(byDriver).sort((a,b)=>{const oa=orderIdx(a),ob=orderIdx(b);if(oa!==ob)return oa-ob;return byDriver[b].price-byDriver[a].price;});
    // เรียงแต่ละคนตามวันที่
    driverNames.forEach(n=>{byDriver[n].rows.sort((a,b)=>(a.date||'').localeCompare(b.date||''));});

    // ═══ helper: render 1 page → addImage ═══
    async function renderPage(el,isFirst){
      stage.appendChild(el);
      if(!isFirst)pdf.addPage();
      const canvas=await html2canvas(el,{scale:1.5,backgroundColor:'#fff',logging:false});
      const imgData=canvas.toDataURL('image/jpeg',0.80);
      const imgW=usableW,imgH=canvas.height*imgW/canvas.width;
      pdf.addImage(imgData,'JPEG',margin,margin,imgW,Math.min(imgH,usableH));
      stage.removeChild(el);
    }

    // ════════════════════════════════════════════════════════════
    //  PAGE 1: HEADER + STATS + SUMMARY + 3 CHARTS
    // ════════════════════════════════════════════════════════════
    const p1=document.createElement('div');
    p1.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    let h1=`<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #3b82f6;padding-bottom:10px;margin-bottom:14px;">
      <div><div style="font-size:20px;font-weight:700;color:#18181b;">${reportTitle}</div>
      <div style="font-size:11px;color:#71717a;margin-top:2px;">ระบบติดตามน้ำมันรถ · ${CURRENT_USER}</div></div>
      <div style="text-align:right;font-size:10px;color:#a1a1aa;">พิมพ์เมื่อ ${new Date().toLocaleString('th-TH',{timeZone:TZ})}</div>
    </div>`;

    // ── Stats cards ──
    const totHours=rows.reduce((s,r)=>s+(r.hours||0),0);
    h1+=`<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:7px;margin-bottom:14px;">
      ${_pdfStat('ค่าน้ำมันรวม','฿'+Math.round(totPrice).toLocaleString())}
      ${_pdfStat('ระยะทางรวม',Math.round(totDist).toLocaleString()+' km')}
      ${_pdfStat('น้ำมันรวม',fmtN(totLiters)+' L')}
      ${_pdfStat('เฉลี่ย km/L',fmtN(avgKml))}
      ${_pdfStat('ชั่วโมงรวม',fmtN(totHours)+' ชม.')}
      ${_pdfStat('จำนวน',rows.length+' รายการ · '+driverNames.length+' คน')}
    </div>`;

    // ── สรุปผล: ใครเติมมากสุด / ใช้เวลามากสุด / ขับไกลสุด ──
    const rankPrice=[...driverNames].sort((a,b)=>byDriver[b].price-byDriver[a].price);
    const rankDist=[...driverNames].sort((a,b)=>byDriver[b].dist-byDriver[a].dist);
    const rankHours=[...driverNames].sort((a,b)=>{
      const ha=byDriver[a].rows.reduce((s,r)=>s+(r.hours||0),0);
      const hb=byDriver[b].rows.reduce((s,r)=>s+(r.hours||0),0);
      return hb-ha;
    });
    const rankKml=[...driverNames].filter(n=>byDriver[n].liters>0).sort((a,b)=>{
      const ka=byDriver[a].dist/byDriver[a].liters;
      const kb=byDriver[b].dist/byDriver[b].liters;
      return kb-ka;
    });

    const medal=(i)=>i===0?'🥇':i===1?'🥈':i===2?'🥉':'';
    const _rankRow=(arr,valFn,max)=>{
      return arr.slice(0,max||3).map((n,i)=>{
        return `<div style="display:flex;align-items:center;gap:6px;padding:4px 0;${i<arr.length-1?'border-bottom:1px solid #f4f4f5;':''}">
          <span style="font-size:13px;width:20px;text-align:center;">${medal(i)}</span>
          <span style="font-size:11px;font-weight:600;color:#18181b;flex:1;">${n}</span>
          <span style="font-size:11px;font-weight:700;color:#3f3f46;font-family:ui-monospace,monospace;">${valFn(n)}</span>
        </div>`;
      }).join('');
    };

    h1+=`<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-bottom:14px;">
      <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;">
        <div style="font-size:10px;font-weight:700;color:#92400e;margin-bottom:6px;">⛽ เติมน้ำมันมากสุด</div>
        ${_rankRow(rankPrice,n=>'฿'+Math.round(byDriver[n].price).toLocaleString())}
      </div>
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 12px;">
        <div style="font-size:10px;font-weight:700;color:#1e40af;margin-bottom:6px;">🛣️ ขับรถไกลสุด</div>
        ${_rankRow(rankDist,n=>Math.round(byDriver[n].dist).toLocaleString()+' km')}
      </div>
      <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;">
        <div style="font-size:10px;font-weight:700;color:#166534;margin-bottom:6px;">⏱️ ใช้เวลาทำงานมากสุด</div>
        ${_rankRow(rankHours,n=>{const h=byDriver[n].rows.reduce((s,r)=>s+(r.hours||0),0);return fmtN(h)+' ชม.';})}
      </div>
      <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;">
        <div style="font-size:10px;font-weight:700;color:#6b21a8;margin-bottom:6px;">📊 ประหยัดน้ำมันสุด (km/L)</div>
        ${_rankRow(rankKml,n=>{const k=byDriver[n].dist/byDriver[n].liters;return fmtN(k)+' km/L';})}
      </div>
    </div>`;

    // ── ตารางสรุปแยกคนขับ (mini table) ──
    h1+=`<div style="margin-bottom:14px;">
      <div style="font-size:11px;font-weight:700;color:#18181b;margin-bottom:6px;">ค่าน้ำมันแยกตามคนขับ</div>
      <table style="width:100%;border-collapse:collapse;font-size:10px;">
        <thead><tr style="background:#f4f4f5;">
          <th style="padding:5px 6px;text-align:left;border-bottom:1px solid #e4e4e7;color:#3f3f46;">คนขับ</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">ค่าน้ำมัน</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">ระยะ km</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">ลิตร</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">km/L</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">฿/km</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">ชม.</th>
          <th style="padding:5px 6px;text-align:right;border-bottom:1px solid #e4e4e7;color:#3f3f46;">รายการ</th>
        </tr></thead><tbody>`;
    driverNames.forEach((n,i)=>{
      const d=byDriver[n];const kml=d.liters>0?d.dist/d.liters:0;const thbKm=d.dist>0?d.price/d.dist:0;
      const hrs=d.rows.reduce((s,r)=>s+(r.hours||0),0);
      h1+=`<tr style="background:${i%2?'#fafafa':'#fff'};">
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;font-weight:600;">${n}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">฿${Math.round(d.price).toLocaleString()}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${Math.round(d.dist).toLocaleString()}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(d.liters)}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;color:${kml>=13?'#059669':(kml>0&&kml<9?'#ef4444':'#18181b')};font-weight:600;">${kml>0?fmtN(kml):'—'}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(hrs)}</td>
        <td style="padding:4px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${d.rows.length}</td>
      </tr>`;
    });
    h1+=`</tbody></table></div>`;

    p1.innerHTML=h1;
    await renderPage(p1,true);

    // ════════════════════════════════════════════════════════════
    //  PAGE 2: CHARTS (3 กราฟ)
    // ════════════════════════════════════════════════════════════
    const p2=document.createElement('div');
    p2.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';

    let h2=`<div style="font-size:14px;font-weight:700;color:#18181b;margin-bottom:14px;border-bottom:2px solid #3b82f6;padding-bottom:6px;">กราฟวิเคราะห์ · ${reportTitle}</div>`;

    // ── Chart 1: ต้นทุนต่อกิโล (฿/km) — horizontal bar ──
    const costData=driverNames.map(n=>({name:n,cost:byDriver[n].dist>0?byDriver[n].price/byDriver[n].dist:0})).filter(d=>d.cost>0).sort((a,b)=>a.cost-b.cost);
    const costAvg=costData.length>0?costData.reduce((s,d)=>s+d.cost,0)/costData.length:0;
    const costMax=Math.max(...costData.map(d=>d.cost),costAvg,1);
    let costBars='';
    costData.forEach((d,i)=>{
      const y=i*22+18;const pct=d.cost/costMax*100;
      const col=d.cost<=costAvg*0.85?'#10b981':(d.cost<=costAvg*1.05?'#f59e0b':'#ef4444');
      costBars+=`<text x="0" y="${y+4}" style="font-size:10px;fill:#3f3f46;font-weight:600">${d.name.length>7?d.name.slice(0,7)+'…':d.name}</text>`;
      costBars+=`<rect x="80" y="${y-7}" width="${pct*3.8}" height="15" rx="3" fill="${col}"/>`;
      costBars+=`<text x="${84+pct*3.8}" y="${y+4}" style="font-size:9px;fill:#71717a">฿${fmtN(d.cost)}</text>`;
    });
    const costH=Math.max(costData.length*22+24,60);

    // ── Chart 2: น้ำมันต่อกิโล (km/L) — horizontal bar ──
    const kmlData=driverNames.map(n=>({name:n,kml:byDriver[n].liters>0?byDriver[n].dist/byDriver[n].liters:0})).filter(d=>d.kml>0).sort((a,b)=>b.kml-a.kml);
    const kmlAvg=kmlData.length>0?kmlData.reduce((s,d)=>s+d.kml,0)/kmlData.length:0;
    const kmlMax=Math.max(...kmlData.map(d=>d.kml),kmlAvg,1);
    let kmlBars='';
    kmlData.forEach((d,i)=>{
      const y=i*22+18;const pct=d.kml/kmlMax*100;
      const col=d.kml<kmlAvg*0.9?'#ef4444':(d.kml<kmlAvg?'#f59e0b':'#10b981');
      kmlBars+=`<text x="0" y="${y+4}" style="font-size:10px;fill:#3f3f46;font-weight:600">${d.name.length>7?d.name.slice(0,7)+'…':d.name}</text>`;
      kmlBars+=`<rect x="80" y="${y-7}" width="${pct*3.8}" height="15" rx="3" fill="${col}"/>`;
      kmlBars+=`<text x="${84+pct*3.8}" y="${y+4}" style="font-size:9px;fill:#71717a">${fmtN(d.kml)} km/L</text>`;
    });
    const kmlH=Math.max(kmlData.length*22+24,60);

    // ── Chart 3: ส่งสำเร็จ/ผิดพลาด — stacked bar (ใช้ DLV_BY_DRIVER จากหน้าเว็บ) ──
    const dlvData=driverNames.map(n=>{const d=DLV_BY_DRIVER[n];if(!d)return null;return{name:n,ok:d.success||0,ng:d.fail||0,total:(d.success||0)+(d.fail||0)};}).filter(d=>d&&d.total>0);
    const dlvMax=Math.max(...dlvData.map(d=>d.total),1);
    let dlvBars='';
    dlvData.forEach((d,i)=>{
      const y=i*22+18;const pctOk=d.ok/dlvMax*100;const pctNg=d.ng/dlvMax*100;
      dlvBars+=`<text x="0" y="${y+4}" style="font-size:10px;fill:#3f3f46;font-weight:600">${d.name.length>7?d.name.slice(0,7)+'…':d.name}</text>`;
      dlvBars+=`<rect x="80" y="${y-7}" width="${pctOk*3.8}" height="15" rx="0" fill="#10b981"/>`;
      if(d.ng>0)dlvBars+=`<rect x="${80+pctOk*3.8}" y="${y-7}" width="${pctNg*3.8}" height="15" rx="0" fill="#ef4444"/>`;
      dlvBars+=`<text x="${84+(pctOk+pctNg)*3.8}" y="${y+4}" style="font-size:9px;fill:#71717a">${d.ok}✓${d.ng>0?' '+d.ng+'✕':''}</text>`;
    });
    const dlvH=Math.max(dlvData.length*22+24,60);

    h2+=`<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
      <div><div style="font-size:11px;font-weight:700;color:#18181b;margin-bottom:6px;">ต้นทุนต่อกิโล (฿/km) <span style="color:#71717a;font-weight:400">ยิ่งน้อยยิ่งดี</span></div>
        <svg viewBox="0 0 520 ${costH}" width="100%" xmlns="http://www.w3.org/2000/svg" style="display:block">${costBars}</svg></div>
      <div><div style="font-size:11px;font-weight:700;color:#18181b;margin-bottom:6px;">น้ำมันต่อกิโล (km/L) <span style="color:#71717a;font-weight:400">ยิ่งมากยิ่งดี</span></div>
        <svg viewBox="0 0 520 ${kmlH}" width="100%" xmlns="http://www.w3.org/2000/svg" style="display:block">${kmlBars}</svg></div>
      <div><div style="font-size:11px;font-weight:700;color:#18181b;margin-bottom:6px;">รายการส่ง <span style="color:#10b981">สำเร็จ</span> / <span style="color:#ef4444">ผิดพลาด</span></div>
        <svg viewBox="0 0 520 ${dlvH}" width="100%" xmlns="http://www.w3.org/2000/svg" style="display:block">${dlvBars}</svg></div>
    </div>`;

    p2.innerHTML=h2;
    await renderPage(p2,false);

    // ════════════════════════════════════════════════════════════
    //  PAGE 2+: ตารางแยกตามคนขับ (เรียงวันภายในแต่ละคน)
    // ════════════════════════════════════════════════════════════
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

        // หัวตาราง: ชื่อคนขับ + สรุปย่อ
        let html=`<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid #3b82f6;padding-bottom:8px;margin-bottom:10px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#dbeafe,#fef3c7);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#2563eb;">${(drvName||'?')[0].toUpperCase()}</div>
            <div>
              <div style="font-size:16px;font-weight:700;color:#18181b;">${drvName}</div>
              <div style="font-size:10px;color:#71717a;">${reportTitle}${chunks.length>1?' · ('+(ci+1)+'/'+chunks.length+')':''}</div>
            </div>
          </div>
          <div style="display:flex;gap:14px;font-size:10px;color:#3f3f46;">
            <span><b style="font-size:13px">฿${Math.round(drvPrice).toLocaleString()}</b> รวม</span>
            <span><b style="font-size:13px">${Math.round(drvDist).toLocaleString()}</b> km</span>
            <span><b style="font-size:13px">${fmtN(drvLiters)}</b> L</span>
            ${drvKml>0?`<span><b style="font-size:13px;color:${drvKml>=13?'#059669':(drvKml<9?'#ef4444':'#18181b')}">${fmtN(drvKml)}</b> km/L</span>`:''}
            ${drvThbKm>0?`<span><b style="font-size:13px">฿${fmtN(drvThbKm)}</b>/km</span>`:''}
            <span><b style="font-size:13px">${drvRows.length}</b> รายการ</span>
          </div>
        </div>`;

        html+=`<table style="width:100%;border-collapse:collapse;font-size:11px;">
        <thead><tr style="background:#f4f4f5;">
          <th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;width:80px">วันที่</th>
          <th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ทะเบียน</th>
          <th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;color:#3f3f46;width:70px">เวลา</th>
          <th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ค่าน้ำมัน</th>
          <th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ระยะ</th>
          <th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">ลิตร</th>
          <th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">km/L</th>
          <th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;color:#3f3f46;">฿/km</th>
        </tr></thead><tbody>`;
        chunk.forEach((r,i)=>{
          const dp=(r.date||'').split('-');
          const dateText=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'—';
          const bg=i%2?'#fafafa':'#fff';
          const thbKm=(r.distance>0&&r.price>0)?(r.price/r.distance):0;
          const startT=(r.start||'').substring(0,5);const endT=(r.end||'').substring(0,5);
          const timeT=(startT&&endT)?startT+'-'+endT:'—';
          html+=`<tr style="background:${bg};">
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${dateText}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${r.plate||'—'}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;font-size:10px;color:#71717a;">${timeT}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.price>0?'฿'+Math.round(r.price).toLocaleString():'—'}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.distance>0?Math.round(r.distance).toLocaleString():'—'}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.liters>0?fmtN(r.liters):'—'}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;color:${r.kml>=13?'#059669':(r.kml>0&&r.kml<9?'#ef4444':'#18181b')};font-weight:${r.kml>0?'600':'400'}">${r.kml>0?fmtN(r.kml):'—'}</td>
            <td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td>
          </tr>`;
        });
        html+='</tbody></table>';
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
  return `<div style="background:#f9fafb;border:1px solid #f0f0f0;border-radius:12px;padding:14px 16px;">
    <div style="font-size:12px;color:#71717a;margin-bottom:4px;">${label}</div>
    <div style="font-size:19px;font-weight:700;color:#18181b;">${value}</div></div>`;
}

/* ═══════════════════════════════════════════════════════════════
  INIT
═══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',function(){
  updateNavDate();
  setInterval(updateNavDate,30000);

  try{ if(document.getElementById('deliveryChart')) renderDlv(); }catch(e){console.warn('dlv',e);}
  try{ if(document.getElementById('chartKml')) renderKmlChart(); }catch(e){console.warn('kml',e);}
  try{ if(document.getElementById('chartCost')) renderCostChart(); }catch(e){console.warn('cost',e);}
  renderOilPage();
  drpInit();

  let _rt=null;
  window.addEventListener('resize',()=>{
    clearTimeout(_rt);
    _rt=setTimeout(()=>{
      try{ if(kmlChart) renderKmlChart(); if(costChart) renderCostChart(); if(dlvChart) renderDlv(); }catch(e){}
    },250);
  });

  // ── Entry card init (เฉพาะ privileged + day view) ──
  if(IS_PRIVILEGED && MAIN_VIEW==='day' && document.getElementById('entryRowsBody')){
    ilLoadOilPrice('diesel');   // ดึงราคาครั้งเดียว
    const date=document.getElementById('il-work-date')?.value||todayStr();
    (async()=>{
      const hint=document.getElementById('entryLoadingHint');if(hint)hint.style.display='flex';
      try{
        await Promise.all([fetchJobsByDate(date), fetchSavedDrivers(date)]);
        ilRenderDriverRows(date);
      }catch(e){ console.warn('init entry',e); }
      finally{ if(hint)hint.style.display='none'; }
    })();
  }
});
</script>

<!-- PDF Export Modal -->
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

</body>
</html>
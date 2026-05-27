  {{-- resources/views/driver/oil.blade.php — Apple-style redesign --}}
  <!DOCTYPE html>
  <html lang="th">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ระบบติดตามน้ำมันรถ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&family=SF+Pro+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <style>
  /* ═══════════════════════════════════════════════════════════════
    APPLE-STYLE DESIGN SYSTEM — ธีมฟ้า (หลัก) + ส้ม (รอง)
  ═══════════════════════════════════════════════════════════════ */
  *,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
  html,body{margin:0;padding:0;overflow-x:hidden;max-width:100vw}

  :root{
    --bg: #fafafa;
    --bg-elevated: #ffffff;
    --bg-card: #ffffff;
    --bg-card-hover: #f9fafb;
    --bg-subtle: #f4f4f5;
    --bg-subtle2: #fafafa;
    --separator: rgba(0,0,0,.06);
    --separator-strong: rgba(0,0,0,.10);
    --text: #18181b;
    --text2: #3f3f46;
    --text3: #71717a;
    --text4: #a1a1aa;
    --text5: #d4d4d8;
    /* ── สีรอง = ส้ม (ตัวแปรชื่อ blue) ── */
    --blue: #f59e0b;
    --blue-hover: #d97706;
    --blue-light: #fef3c7;
    /* ── สีหลัก = ฟ้า (ตัวแปรชื่อ green เพื่อ remap ทั้งหน้า) ── */
    --green: #3b82f6;
    --green-dark: #2563eb;
    --green-light: #dbeafe;
    --orange: #f59e0b;
    --orange-light: #fef3c7;
    --red: #ef4444;
    --red-light: #fee2e2;
    --yellow: #eab308;
    --purple: #8b5cf6;
    --pink: #ec4899;
    --indigo: #6366f1;
    --teal: #14b8a6;
    --cyan: #06b6d4;
    --radius-sm: 8px;
    --radius: 12px;
    --radius-lg: 18px;
    --radius-xl: 22px;
    --shadow-xs: 0 1px 2px rgba(0,0,0,.04);
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.04);
    --shadow: 0 2px 8px rgba(0,0,0,.04), 0 1px 3px rgba(0,0,0,.03);
    --shadow-lg: 0 10px 30px rgba(0,0,0,.07), 0 2px 6px rgba(0,0,0,.04);
    --shadow-xl: 0 20px 50px rgba(0,0,0,.10), 0 4px 12px rgba(0,0,0,.05);
    --font: 'SF Pro Display', 'Inter', 'IBM Plex Sans Thai', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
    --font-thai: 'IBM Plex Sans Thai', 'SF Pro Display', 'Inter', -apple-system, sans-serif;
    --font-mono: ui-monospace, 'SF Mono', Menlo, monospace;
    --ease: cubic-bezier(.4,0,.2,1);
    --ease-out: cubic-bezier(0,0,.2,1);
  }

  body{font-family: var(--font-thai);background: var(--bg);color: var(--text);min-height: 100vh;font-size: 14px;line-height: 1.5;letter-spacing: -0.01em;}

  .topnav{position: sticky; top: 0; z-index: 50;background: rgba(245,245,247,.78);backdrop-filter: saturate(180%) blur(20px);-webkit-backdrop-filter: saturate(180%) blur(20px);border-bottom: 0.5px solid var(--separator);}
  .topnav-main{display: flex; align-items: center; gap: 24px;padding: 0 28px; height: 52px;max-width: 1800px; margin: 0 auto;}
  .topnav-brand{display: flex; align-items: center; gap: 10px; flex-shrink: 0}
  .topnav-brand .logo{width: 28px; height: 28px;display: flex; align-items: center; justify-content: center;font-size: 16px;background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color: #fff;border-radius: 8px;box-shadow: 0 1px 3px rgba(59,130,246,.3);}
  .topnav-brand .title-text{font-size: 14px; font-weight: 600;letter-spacing: -0.02em;color: var(--text);}
  .topnav-menu{display: flex; align-items: center; gap: 2px; flex: 1}
  .topnav-menu .nav-item{display: inline-flex; align-items: center; gap: 6px;padding: 6px 12px; border-radius: 7px;background: transparent; border: none;color: var(--text2);font-family: inherit; font-size: 14px; font-weight: 500;cursor: pointer; text-decoration: none;white-space: nowrap;transition: all .15s var(--ease);}
  .topnav-menu .nav-item:hover{background: rgba(0,0,0,.04); color: var(--text)}
  .topnav-menu .nav-item.active{background: rgba(0,0,0,.06);color: var(--text); font-weight: 600;}
  .topnav-menu .nav-item .ic{display: inline-flex; align-items: center; justify-content: center;width: 18px; height: 18px;opacity: .85;flex-shrink: 0;}
  .topnav-menu .nav-item .ic svg{display: block}
  .topnav-menu .nav-item.active .ic{opacity: 1}
  .topnav-toggle svg{display: block}
  .topnav-right{display: flex; align-items: center; gap: 10px; flex-shrink: 0}
  .topnav-user{display: inline-flex; align-items: center; gap: 7px;padding: 4px 11px 4px 4px;background: rgba(0,0,0,.04);border: 0.5px solid var(--separator);border-radius: 100px;color: var(--text);font-size: 14px; font-weight: 600;letter-spacing: -0.01em;max-width: 180px;}
  .topnav-user-avatar{width: 22px; height: 22px;border-radius: 50%;background: linear-gradient(135deg, #3b82f6, #2563eb);color: #fff;display: inline-flex; align-items: center; justify-content: center;font-size: 14px; font-weight: 700;flex-shrink: 0;text-transform: uppercase;}
  .topnav-user-name{white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0;}
  .topnav-time{font-size: 14px; color: var(--text3);font-family: var(--font-mono); font-weight: 500;display: flex; align-items: center; gap: 5px;}
  .topnav-time .pulse{width: 6px; height: 6px; border-radius: 50%;background: var(--green);box-shadow: 0 0 0 0 rgba(59,130,246,.4);animation: pulse 2s infinite;}
  @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(59,130,246,.4)}70%{box-shadow:0 0 0 7px rgba(59,130,246,0)}100%{box-shadow:0 0 0 0 rgba(59,130,246,0)}}
  .topnav-toggle{display: none; background: transparent;border: none; width: 32px; height: 32px;font-size: 17px; cursor: pointer; color: var(--text);border-radius: 7px;}
  .topnav-toggle:hover{background: rgba(0,0,0,.04)}
  .topnav-filters{display: flex; align-items: center; gap: 16px;padding: 0 28px; height: 44px;max-width: 1800px; margin: 0 auto;border-top: 0.5px solid var(--separator);overflow-x: auto;}
  .topnav-filters::-webkit-scrollbar{display: none}
  .topnav-filters{scrollbar-width: none}
  .filter-group{display: flex; align-items: center; gap: 8px; flex-shrink: 0}
  .filter-group-label{font-size: 14px; font-weight: 500;color: var(--text3);white-space: nowrap;}
  .segmented{display: inline-flex;background: rgba(120,120,128,.12);border-radius: 7px;padding: 2px;gap: 0;}
  .segmented .seg-btn{padding: 4px 12px; border-radius: 5px;background: transparent; border: none;color: var(--text2);font-family: inherit; font-size: 14px; font-weight: 500;cursor: pointer; white-space: nowrap;transition: all .15s var(--ease);position: relative;}
  .segmented .seg-btn:hover{color: var(--text)}
  .segmented .seg-btn.active{background: #fff;color: var(--text); font-weight: 600;box-shadow: 0 1px 3px rgba(0,0,0,.1), 0 1px 1px rgba(0,0,0,.05);}
  .pill-select,.pill-date{padding: 5px 12px;border: 0.5px solid var(--separator-strong);border-radius: 7px;font-family: inherit; font-size: 14px; font-weight: 500;background: #fff;color: var(--text);min-width: 130px;cursor: pointer;transition: all .15s var(--ease);outline: none;}
  .pill-select:hover,.pill-date:hover{border-color: rgba(0,0,0,.2)}
  .pill-select:focus,.pill-date:focus{border-color: var(--green); box-shadow: 0 0 0 3px rgba(59,130,246,.2)}
  .date-trigger-pill{display: inline-flex; align-items: center; gap: 6px;padding: 5px 10px;border: 0.5px solid var(--separator-strong);border-radius: 7px;background: #fff; color: var(--text);font-family: inherit; font-size: 14px; font-weight: 500;cursor: pointer;min-width: 200px;transition: all .15s var(--ease);}
  .date-trigger-pill:hover{border-color: rgba(0,0,0,.2)}
  .date-trigger-pill.active{border-color: var(--green); box-shadow: 0 0 0 3px rgba(59,130,246,.2)}
  .date-trigger-pill .arrow{margin-left: auto; font-size: 14px; color: var(--text4)}

  .hero{margin-bottom: 24px;}
  .hero-title{font-size: 28px; font-weight: 700;letter-spacing: -0.03em;color: var(--text);margin: 0 0 6px;}
  .hero-sub{font-size: 14px; font-weight: 400;color: var(--text3);margin: 0;}

  .entry-layout{display: grid;grid-template-columns: minmax(0, 70fr) minmax(0, 30fr);gap: 18px;margin-bottom: 24px;align-items: start;}
  .entry-card{background: var(--bg-card);border-radius: var(--radius-xl);box-shadow: var(--shadow);border: 1px solid rgba(0,0,0,.04);overflow: hidden;min-width: 0;}
  .entry-card-head{padding: 14px 20px;border-bottom: 1px solid var(--separator);background: linear-gradient(180deg, #fafbfc 0%, #ffffff 100%);}
  .entry-card-head-left{display: flex; align-items: center; gap: 14px;flex-wrap: wrap;}
  .entry-icon{width: 38px; height: 38px;display: flex; align-items: center; justify-content: center;background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color: #fff;border-radius: 10px;font-size: 19px;box-shadow: 0 2px 8px rgba(59,130,246,.35), inset 0 1px 0 rgba(255,255,255,.2);flex-shrink: 0;}
  .entry-titlewrap{display: flex; flex-direction: column; line-height: 1.25; flex-shrink: 0}
  .entry-title{font-size: 16px; font-weight: 600;color: var(--text); letter-spacing: -0.02em;}
  .entry-sub{font-size: 14px; font-weight: 400;color: var(--text3); margin-top: 2px;}
  .entry-head-right{display: flex; align-items: center; gap: 12px}
  .entry-date-field{display: flex; flex-direction: column; gap: 3px;}
  .entry-date-label{font-size: 14px; font-weight: 600;color: var(--text3);letter-spacing: -0.01em;text-transform: none;}
  .entry-date-input{padding: 7px 11px;border: 1px solid var(--separator-strong);border-radius: 9px;font-family: inherit;font-size: 14px; font-weight: 600;color: var(--text);background: #fff;transition: all .15s var(--ease);outline: none;}
  .entry-date-input:hover{border-color: rgba(0,0,0,.2)}
  .entry-date-input:focus{border-color: var(--green); box-shadow: 0 0 0 3px rgba(59,130,246,.15)}
  .entry-oil-mini{display: inline-flex; align-items: center; gap: 8px;padding: 7px 12px;background: var(--green-light);border: 1px solid #dbeafe;border-radius: 100px;color: var(--green-dark);}
  .entry-oil-label{font-size: 14px; font-weight: 600}
  .entry-oil-num{font-family: var(--font-mono);font-size: 15px; font-weight: 700;color: var(--text);}
  .entry-oil-refresh{width: 22px; height: 22px;border-radius: 50%;background: rgba(255,255,255,.7);border: none;cursor: pointer;font-size: 14px;color: var(--green-dark);display: inline-flex; align-items: center; justify-content: center;transition: transform .3s var(--ease);}
  .entry-oil-refresh:hover{transform: rotate(180deg)}
  .entry-export-btn{display: inline-flex; align-items: center; gap: 6px;padding: 7px 13px;background: var(--blue);color: #fff;border: none;border-radius: 9px;font-family: inherit;font-size: 14px; font-weight: 600;cursor: pointer;letter-spacing: -0.01em;transition: all .15s var(--ease);white-space: nowrap;box-shadow: 0 1px 3px rgba(245,158,11,.3);}
  .entry-export-btn:hover{background: var(--blue-hover);transform: translateY(-1px);box-shadow: 0 4px 10px rgba(245,158,11,.35);}
  .entry-export-btn:active{transform: translateY(0)}
  .entry-export-btn:disabled{background: var(--text4); cursor: wait; transform: none;box-shadow: none;}
  .entry-export-btn svg{flex-shrink: 0}
  .entry-oil-tabs{display: flex; align-items: center; gap: 4px; flex-wrap: wrap;padding: 10px 22px;background: var(--bg-subtle2);border-bottom: 1px solid var(--separator);}
  .entry-oil-tab{padding: 4px 11px; border-radius: 100px;background: #fff; border: 1px solid var(--separator-strong);color: var(--text2); font-family: inherit;font-size: 14px; font-weight: 500;cursor: pointer;transition: all .15s var(--ease);}
  .entry-oil-tab:hover{background: #fafafa; border-color: rgba(0,0,0,.2)}
  .entry-oil-tab.active{background: var(--green); color: #fff; border-color: var(--green); font-weight: 600}
  .entry-oil-status{font-size: 14px; color: var(--text3); font-weight: 500;margin-left: 6px;}
  .entry-oil-live{margin-left: auto;display: inline-flex; align-items: center; gap: 5px;padding: 3px 9px;background: rgba(59,130,246,.12);color: var(--green-dark);border-radius: 100px;font-size: 14px; font-weight: 600;}
  .entry-oil-live .dot{width: 5px; height: 5px; border-radius: 50%; background: var(--green)}
  .entry-oil-live.loading{background: rgba(245,158,11,.12); color: var(--orange)}
  .entry-oil-live.loading .dot{background: var(--orange); animation: pulse-dot 1s infinite}
  .entry-loading-row{display: flex; align-items: center; justify-content: center; gap: 8px;padding: 30px;color: var(--orange); font-size: 14px; font-weight: 500;}
  .entry-loading-row .spinner{width: 14px; height: 14px;border: 2px solid var(--orange);border-top-color: transparent;border-radius: 50%;animation: spin 1s linear infinite;}
  .entry-rows-wrap{overflow-x: hidden;}
  .entry-rows-wrap::-webkit-scrollbar{height: 8px}
  .entry-rows-wrap::-webkit-scrollbar-thumb{background: rgba(0,0,0,.12);border-radius: 100px;border: 2px solid #fff;background-clip: padding-box;}
.entry-rows-header,.entry-row{display: grid;grid-template-columns:minmax(100px, 0.8fr) minmax(95px, 0.9fr) minmax(115px, 1fr) minmax(80px, 0.75fr) minmax(72px, 0.7fr) minmax(78px, 0.75fr) minmax(72px, auto);gap: 8px;align-items: center;padding: 10px 14px;min-width: 0;}
  .entry-rows-header{background: var(--bg-subtle2);border-bottom: 1px solid var(--separator);font-size: 14px; font-weight: 700;color: var(--text3);text-transform: uppercase; letter-spacing: 0.4px;position: sticky; top: 0; z-index: 1;}
  .entry-rows-header > div:last-child{text-align: center; min-width: 80px}
  .entry-row{border-bottom: 1px solid var(--separator);transition: background .12s;position: relative;cursor: pointer;}
  .entry-row:hover{background: var(--bg-subtle2)}
  .entry-row.focused{background: linear-gradient(90deg, rgba(59,130,246,.18), rgba(59,130,246,.06) 60%);box-shadow: inset 0 0 0 1px rgba(59,130,246,.25);}
  .entry-row.focused::before{content: '';position: absolute; left: 0; top: 0; bottom: 0;width: 4px; background: var(--green);}
  .entry-row.saved{background: linear-gradient(90deg, rgba(59,130,246,.06), transparent 50%)}
  .entry-row.saved::before{content: '';position: absolute; left: 0; top: 0; bottom: 0;width: 3px; background: var(--green);}
  .entry-row.saving{opacity: .6; pointer-events: none}
  .save-toast{position: fixed;top: 78px; right: 28px;z-index: 200;background: #fff;border-radius: 14px;box-shadow: 0 12px 32px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.06);border: 1px solid var(--separator);padding: 12px 16px;display: flex; align-items: center; gap: 12px;min-width: 240px;border-left: 3px solid var(--green);animation: toastSlideIn .25s var(--ease) forwards;}
  .save-toast.hiding{animation: toastSlideOut .25s var(--ease) forwards}
  @keyframes toastSlideIn{from{opacity: 0; transform: translateY(-10px) scale(.95)}to{opacity: 1; transform: translateY(0) scale(1)}}
  @keyframes toastSlideOut{to{opacity: 0; transform: translateY(-10px) scale(.95)}}
  .save-toast-icon{width: 28px; height: 28px;display: flex; align-items: center; justify-content: center;background: var(--green);color: #fff;border-radius: 50%;font-size: 14px; font-weight: 700;flex-shrink: 0;}
  .save-toast-body{flex: 1; min-width: 0}
  .save-toast-title{font-size: 14px; font-weight: 600;color: var(--text);letter-spacing: -0.01em;}
  .save-toast-msg{font-size: 14px; color: var(--text3);margin-top: 1px;}
  .er-driver{display: flex; align-items: center; gap: 10px;min-width: 0;}
  .er-driver-avatar{width: 32px; height: 32px;border-radius: 50%;background: linear-gradient(135deg, var(--blue-light), var(--green-light));color: var(--green-dark);display: inline-flex; align-items: center; justify-content: center;font-size: 14px; font-weight: 700;flex-shrink: 0;}
  .er-driver-info{min-width: 0; flex: 1}
  .er-driver-name{font-size: 14px; font-weight: 600;color: var(--text);letter-spacing: -0.01em;white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  .er-driver-jobs{font-size: 14px; color: var(--text3);margin-top: 1px;}
    .er-driver-jobs .er-ok{color: #059669; font-weight: 600}
  .er-driver-jobs .er-fail{color: var(--red); font-weight: 600}
  .er-plate-select{width: 100%;padding: 7px 11px;border: 1px solid var(--separator-strong);border-radius: 8px;font-family: inherit;font-size: 14px; font-weight: 500;background: #fff; color: var(--text);outline: none;appearance: none;background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23a1a1aa' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat: no-repeat;background-position: right 9px center;padding-right: 26px;transition: all .15s var(--ease);}
  .er-plate-select:hover{border-color: rgba(0,0,0,.2)}
  .er-plate-select:focus{border-color: var(--green); box-shadow: 0 0 0 3px rgba(59,130,246,.15)}
  .er-time-pair{display: grid;grid-template-columns: 1fr;gap: 4px;align-items: center;}
  .er-time-pair .er-time-arrow{display: none;}
  .er-time-btn{padding: 7px 4px;border: 1px solid var(--separator-strong);border-radius: 8px;font-family: var(--font-mono);font-size: 14px; font-weight: 600;background: #fff; color: var(--text);cursor: pointer;text-align: center;letter-spacing: 0.5px;transition: all .15s var(--ease);}
  .er-dt-input{padding: 7px 8px;border: 1px solid var(--separator-strong);border-radius: 8px;font-family: var(--font-mono);font-size: 13px; font-weight: 500;background: #fff; color: var(--text);cursor: pointer;width: 100%;min-width: 0;transition: all .15s var(--ease);}
  .er-dt-input:focus{outline: none; border-color: var(--green);box-shadow: 0 0 0 3px rgba(59,130,246,.15);}
  .er-time-btn:hover{border-color: var(--blue); background: rgba(59,130,246,.04)}
  .er-time-arrow{color: var(--text4); font-size: 14px; padding: 0 2px}
  .er-num-input{width: 100%;padding: 7px 10px;border: 1px solid var(--separator-strong);border-radius: 8px;font-family: var(--font-mono);font-size: 14px; font-weight: 600;background: #fff; color: var(--text);outline: none;transition: all .15s var(--ease);text-align: right;}
  .er-num-input::placeholder{color: var(--text4); font-weight: 400}
  .er-num-input:hover{border-color: rgba(0,0,0,.2)}
  .er-num-input:focus{border-color: var(--green); box-shadow: 0 0 0 3px rgba(59,130,246,.15)}
  .er-summary{display: flex; flex-direction: column; gap: 3px;font-size: 14px;}
  .er-summary-row{display: flex; gap: 6px; align-items: baseline;}
  .er-summary-label{color: var(--text4); font-weight: 500; min-width: 32px}
  .er-summary-val{font-family: var(--font-mono); font-weight: 600; color: var(--text2)}
  .er-summary-val.green{color: var(--green-dark)}
  .er-summary-val.red{color: var(--red)}
  .er-summary-val.empty{color: var(--text5); font-weight: 400}
  .er-save-btn{padding: 8px 16px;background: var(--green);color: #fff;border: none;border-radius: 100px;font-family: inherit;font-size: 14px; font-weight: 600;cursor: pointer;letter-spacing: -0.01em;white-space: nowrap;transition: all .15s var(--ease);display: inline-flex; align-items: center; gap: 5px;}
  .er-save-btn:hover{background: var(--green-dark); transform: scale(1.04)}
  .er-save-btn:active{transform: scale(.96)}
  .er-save-btn:disabled{background: var(--text5); cursor: not-allowed; transform: none;}
  .er-save-btn.saved{background: var(--bg-subtle); color: var(--green-dark);border: 1px solid var(--green-light);}
  .er-save-btn .ic{font-size: 14px}
  .entry-empty{text-align: center; padding: 40px 20px;color: var(--text4); font-size: 14px;}

  /* ════ RESPONSIVE — improved fluid scaling ════ */
  .main{padding: clamp(14px, 2.5vw, 28px);max-width: 1800px;margin: 0 auto;}

  @media (max-width: 1500px){
    .entry-layout{grid-template-columns: 62fr 38fr}
    .entry-rows-header{display: none}
    .entry-row{
      grid-template-columns: 1fr 1fr;
      grid-template-areas:
        "driver driver"
        "plate time"
        "price distance"
        "summary action";
      gap: 12px 14px;
      padding: 16px 18px;
      min-width: 0;
    }
    .entry-row > .er-driver{grid-area: driver}
    .entry-row > div:nth-child(2){grid-area: plate}
    .entry-row > .er-time-pair{grid-area: time}
    .entry-row > div:nth-child(4){grid-area: price}
    .entry-row > div:nth-child(5){grid-area: distance}
    .entry-row > .er-summary{grid-area: summary; flex-direction: row; gap: 14px}
    .entry-row > div:last-child{grid-area: action; text-align: right !important}
    .entry-row > div:nth-child(2)::before{content:"ทะเบียน"; display:block; font-size:14px; color:var(--text4); font-weight:600; margin-bottom:4px;}
    .entry-row > .er-time-pair::before{content:"เวลา"; display:block; font-size:14px; color:var(--text4); font-weight:600; margin-bottom:4px; grid-column:1/-1;}
    .entry-row > div:nth-child(4)::before{content:"ค่าน้ำมัน (฿)"; display:block; font-size:14px; color:var(--text4); font-weight:600; margin-bottom:4px;}
    .entry-row > div:nth-child(5)::before{content:"ระยะ (km)"; display:block; font-size:14px; color:var(--text4); font-weight:600; margin-bottom:4px;}
  }
  @media (max-width: 1200px){
    .entry-layout{grid-template-columns: 1fr; gap: 14px}
    .jobs-panel{position: static; max-height: 500px}
  }
  @media (max-width: 1024px){
    .topnav-user{max-width: 130px}
    .entry-card-head-left{gap: 10px}
    .entry-export-btn{padding: 7px 11px}
    .fuel-table thead th:nth-child(5),
    .fuel-table tbody td:nth-child(5),
    .fuel-table thead th:nth-child(6),
    .fuel-table tbody td:nth-child(6){display: none}
  }
  @media (max-width: 900px){
    .entry-card-head{padding: 14px 16px}
    .entry-oil-tabs{padding: 10px 16px}
    .entry-card-head-left{gap: 10px}
    .entry-oil-mini{flex-shrink: 0; padding: 5px 10px}
    .entry-oil-mini .entry-oil-label{display: none}
    .topnav-user-name{display: none}
    .topnav-user{padding: 4px; max-width: none}
    .fuel-table thead th:nth-child(1),
    .fuel-table tbody td:nth-child(1),
    .fuel-table thead th:nth-child(4),
    .fuel-table tbody td:nth-child(4),
    .fuel-table thead th:nth-child(7),
    .fuel-table tbody td:nth-child(7),
    .fuel-table thead th:nth-child(9),
    .fuel-table tbody td:nth-child(9),
    .fuel-table thead th:nth-child(10),
    .fuel-table tbody td:nth-child(10){display: none}
  }
  @media (max-width: 600px){
    .entry-row{
      grid-template-columns: 1fr;
      grid-template-areas:
        "driver"
        "plate"
        "time"
        "price"
        "distance"
        "summary"
        "action";
      gap: 8px;
    }
    .entry-card-head-left{flex-wrap: wrap}
  }


  .jobs-panel{background: var(--bg-card);border-radius: var(--radius-xl);box-shadow: var(--shadow);border: 1px solid rgba(0,0,0,.04);overflow: hidden;display: flex; flex-direction: column;position: sticky;top: 110px;max-height: calc(100vh - 130px);}
  .jobs-panel-head{display: flex; align-items: center; justify-content: space-between;gap: 10px;padding: 12px 16px;border-bottom: 1px solid var(--separator);background: linear-gradient(180deg, #fafbfc 0%, #ffffff 100%);flex-shrink: 0;}
  .jobs-panel-title{font-size: 14px; font-weight: 600; color: var(--text);display: flex; align-items: center; gap: 8px;letter-spacing: -0.01em;}
  .jobs-panel-title .ico{font-size: 15px}
  .jobs-panel-body{flex: 1; min-height: 0;overflow-y: auto;padding: 0;-webkit-overflow-scrolling: touch;}
  .jobs-panel-body::-webkit-scrollbar{width: 8px}
  .jobs-panel-body::-webkit-scrollbar-track{background: transparent}
  .jobs-panel-body::-webkit-scrollbar-thumb{background: rgba(0,0,0,.12);border-radius: 100px;border: 2px solid #fff;background-clip: padding-box;}
  .jobs-panel-body::-webkit-scrollbar-thumb:hover{background: rgba(0,0,0,.22)}
  .dgj-row{padding: 8px 12px;border-top: 1px solid var(--separator);font-size: 14px;}
  .dgj-row:first-child{border-top: none}
  .dgj-main{display: flex; flex-direction: column; gap: 4px;}
  .dgj-top{display: grid; grid-template-columns: auto 1fr auto; gap: 8px; align-items: center;}
  .dgj-meta{display: flex; flex-wrap: wrap; gap: 4px 6px; padding-left: 0; font-size: 14px; color: var(--text3); line-height: 1.4;}
  .dgj-meta-item{display: inline-flex; align-items: baseline; gap: 3px;}
  .dgj-meta-label{color: var(--text4); font-size: 14px;}
  .dgj-meta-sep{color: var(--text5)}
  .dgj-meta-item.dgj-note{color: var(--red)}
  .dgj-meta-item.dgj-note .dgj-meta-label{color: var(--red)}
  .dgj-bill{font-family: var(--font-mono);font-size: 14px; font-weight: 600;color: var(--text2);padding: 1px 6px;background: var(--bg-subtle);border-radius: 5px;white-space: nowrap;user-select: text;}
  .dgj-customer{color: var(--text2);font-size: 14px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis;min-width: 0;}
  .dgj-status{display: inline-flex; align-items: center;padding: 1px 8px; border-radius: 100px;font-size: 14px; font-weight: 600;white-space: nowrap;}
  .dgj-status.ok{background: #d1fae5; color: #059669}
  .dgj-status.fail{background: var(--red-light); color: var(--red)}
  .dgj-status.pending{background: var(--bg-subtle); color: var(--text3)}
  .jobs-summary-bar{display: flex; gap: 6px; flex-wrap: wrap;padding: 10px 12px;background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);border-bottom: 1px solid var(--separator);position: sticky; top: 0;z-index: 1;}
  .jsb-chip{display: inline-flex; align-items: center; gap: 4px;padding: 3px 9px; border-radius: 100px;font-size: 14px; font-weight: 600;background: #fff;border: 1px solid var(--separator);color: var(--text2);}
  .jsb-chip strong{font-family: var(--font-mono); color: var(--text)}
  .jsb-chip.ok{background: #d1fae5; color: #059669; border-color: transparent}
  .jsb-chip.ok strong{color: #059669}
  .jsb-chip.fail{background: var(--red-light); color: var(--red); border-color: transparent}
  .jsb-chip.fail strong{color: var(--red)}
  .job-loading{text-align: center; padding: 22px;color: var(--text3); font-size: 14px;}
  .job-date-chip{display: inline-block; padding: 1px 8px;background: var(--blue); color: #fff;border-radius: 100px; font-size: 14px; font-weight: 600;}

  .dual-grid{display: grid;grid-template-columns: 1.6fr 1fr;gap: 18px;margin-bottom: 18px;align-items: stretch;}
  .dual-grid.single-col{grid-template-columns: 1fr}
  .card{background: var(--bg-card);border-radius: var(--radius-xl);box-shadow: var(--shadow);border: 1px solid rgba(0,0,0,.04);overflow: hidden;display: flex; flex-direction: column;min-height: 540px;max-height: 640px;}
  .card-head{display: flex; align-items: center; justify-content: space-between;padding: 16px 22px;border-bottom: 0.5px solid var(--separator);gap: 12px;}
  .card-title{font-size: 15px; font-weight: 600;color: var(--text); letter-spacing: -0.02em;display: flex; align-items: center; gap: 8px;}
  .card-count{display: inline-flex; align-items: center; justify-content: center;min-width: 22px; height: 20px; padding: 0 7px;background: rgba(0,0,0,.06); color: var(--text2);border-radius: 100px;font-size: 14px; font-weight: 600;font-family: var(--font-mono);}
  .card-meta{font-size: 14px; color: var(--text3); font-weight: 400;margin-left: 4px;}
  .sort-toggle{display: flex; align-items: center; gap: 8px;flex-shrink: 0;}
  .sort-label{font-size: 14px; color: var(--text3); font-weight: 500;white-space: nowrap;}
  .sort-segmented{display: inline-flex;background: rgba(0,0,0,.05);border-radius: 7px;padding: 2px;gap: 0;}
  .sort-btn{padding: 4px 10px;border: none;background: transparent;color: var(--text3);font-family: inherit;font-size: 14px; font-weight: 500;cursor: pointer;border-radius: 5px;white-space: nowrap;letter-spacing: -0.01em;transition: all .15s var(--ease);}
  .sort-btn:hover{color: var(--text)}
  .sort-btn.active{background: #fff;color: var(--text);font-weight: 600;box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 1px rgba(0,0,0,.04);}
  .search-pill{position: relative; flex-shrink: 0;}
  .search-pill .si{position: absolute; left: 10px; top: 50%;transform: translateY(-50%);color: var(--text4);pointer-events: none;display: inline-flex; align-items: center;width: 13px; height: 13px;}
  .search-pill .si svg{display: block}
  .search-pill input{padding: 6px 11px 6px 28px;border: 0.5px solid var(--separator-strong);border-radius: 100px;font-family: inherit; font-size: 14px;width: 200px; background: var(--bg-subtle);color: var(--text);transition: all .15s var(--ease);}
  .search-pill input:focus{outline: none; background: #fff;border-color: var(--green);box-shadow: 0 0 0 3px rgba(59,130,246,.15);}
  .search-pill input::placeholder{color: var(--text4)}
  .fuel-table-scroll{overflow-x: hidden;overflow-y: auto;flex: 1;min-height: 0;-webkit-overflow-scrolling: touch;}
  .fuel-table-scroll::-webkit-scrollbar{width: 8px; height: 8px}
  .fuel-table-scroll::-webkit-scrollbar-track{background: transparent}
  .fuel-table-scroll::-webkit-scrollbar-thumb{background: rgba(0,0,0,.12);border-radius: 100px;border: 2px solid #fff;background-clip: padding-box;}
  .fuel-table-scroll::-webkit-scrollbar-thumb:hover{background: rgba(0,0,0,.22); border: 2px solid #fff; background-clip: padding-box}
  .fuel-table{width: 100%;min-width: 0;border-collapse: collapse;font-size: 14px;table-layout: auto;}
  .fuel-table thead{position: sticky; top: 0; z-index: 1;background: #fff;}
  .fuel-table thead th{padding: 11px 10px;background: var(--bg-subtle2);font-size: 14px; color: var(--text3);font-weight: 600; text-align: left;border-bottom: 0.5px solid var(--separator);letter-spacing: -0.01em;white-space: nowrap;text-overflow: ellipsis;overflow: hidden;}
  .fuel-table thead th:first-child{padding-left: 16px}
  .fuel-table thead th:last-child{padding-right: 16px}
  .fuel-table thead th.num{text-align: right}
  .fuel-table tbody td{padding: 11px 10px;border-bottom: 0.5px solid var(--separator);vertical-align: middle;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
  .fuel-table tbody td:first-child{padding-left: 16px}
  .fuel-table tbody td:last-child{padding-right: 16px}
  .fuel-table tbody tr{transition: background .12s}
  .fuel-table tbody tr:hover{background: var(--bg-subtle)}
  .fuel-table tbody tr:last-child td{border-bottom: none}
  .fuel-table .num{text-align: right;font-variant-numeric: tabular-nums;font-family: var(--font-mono);font-size: 14px;}
  .row-idx{color: var(--text4); font-weight: 500; font-size: 14px;font-family: var(--font-mono);}
  .driver-cell{min-width: 0;overflow: hidden;}
  .driver-name{font-weight: 600; font-size: 14px;color: var(--text); letter-spacing: -0.01em;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
  .driver-plate{font-size: 14px; color: var(--text3); margin-top: 1px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
  .time-pill{display: inline-block; padding: 2px 8px;background: var(--bg-subtle); color: var(--text2);border-radius: 100px; font-size: 14px; font-weight: 600;font-family: var(--font-mono);white-space: nowrap;}
  .hour-pill{display: inline-block; padding: 2px 9px;background: rgba(245,158,11,.12);color: #b45309;border-radius: 100px;font-size: 14px; font-weight: 600;font-family: var(--font-mono);white-space: nowrap;letter-spacing: -0.01em;}
  .carry-hint{font-size: 14px; color: var(--orange); font-weight: 500; margin-top: 2px;white-space: nowrap;font-family: var(--font-mono);}
  .date-pill{display: inline-block; padding: 2px 8px;background: rgba(59,130,246,.08);color: var(--green-dark);border-radius: 100px;font-size: 14px; font-weight: 600;font-family: var(--font-mono);white-space: nowrap;letter-spacing: -0.01em;}
  .km-good{color: var(--green-dark); font-weight: 600}
  .km-mid{color: var(--text); font-weight: 500}
  .km-bad{color: var(--red); font-weight: 700}
  .driver-list{padding: 6px;overflow-y: auto;flex: 1;min-height: 0;-webkit-overflow-scrolling: touch;}
  .driver-list::-webkit-scrollbar{width: 8px}
  .driver-list::-webkit-scrollbar-track{background: transparent}
  .driver-list::-webkit-scrollbar-thumb{background: rgba(0,0,0,.12);border-radius: 100px;border: 2px solid #fff;background-clip: padding-box;}
  .driver-list::-webkit-scrollbar-thumb:hover{background: rgba(0,0,0,.22); border: 2px solid #fff; background-clip: padding-box}
  .driver-row{display: grid;grid-template-columns: auto 1fr auto;gap: 12px; align-items: center;padding: 10px 14px;border-radius: 12px;transition: background .12s;}
  .driver-row:hover{background: var(--bg-subtle)}
  .driver-rank{font-family: var(--font-mono);font-size: 14px; font-weight: 700;width: 26px; height: 26px;display: flex; align-items: center; justify-content: center;background: var(--bg-subtle); color: var(--text3);border-radius: 8px;}
  .driver-row:nth-child(1) .driver-rank{background: linear-gradient(135deg, #fde047, #eab308); color: #713f12; box-shadow: 0 2px 6px rgba(234,179,8,.3)}
  .driver-row:nth-child(2) .driver-rank{background: linear-gradient(135deg, #e5e7eb, #9ca3af); color: #1f2937; box-shadow: 0 2px 6px rgba(156,163,175,.3)}
  .driver-row:nth-child(3) .driver-rank{background: linear-gradient(135deg, #fdba74, #c2410c); color: #fff; box-shadow: 0 2px 6px rgba(194,65,12,.3)}
  .driver-row .body{min-width: 0}
  .driver-row .name{font-weight: 600; font-size: 14px; color: var(--text);letter-spacing: -0.01em; margin-bottom: 2px;}
  .driver-row .stats{display: flex; gap: 10px; flex-wrap: wrap;font-size: 14px; color: var(--text3);}
  .driver-row .right{text-align: right}
  .driver-row .price{font-size: 14.5px; font-weight: 700; color: var(--text);font-family: var(--font-mono);letter-spacing: -0.02em;}
  .driver-row .kml{font-size: 14px; color: var(--green-dark); font-weight: 600;margin-top: 2px; font-family: var(--font-mono);}
  .driver-row .kml.warn{color: var(--red)}
  .driver-row .thb-km{font-size: 14px; color: var(--text3); font-weight: 500; margin-top: 1px; font-family: var(--font-mono);}
  .thb-km-val{color: var(--text2); font-weight: 600;}
  .empty-state{text-align: center; padding: 50px 20px;color: var(--text4);}
  .empty-state .icon{font-size: 30px; margin-bottom: 8px; opacity: .4}
  .empty-state p{margin: 0; font-size: 14px}
  .charts-grid{display: grid;grid-template-columns: 1fr;gap: 18px;}
  .chart-card{background: var(--bg-card);border-radius: var(--radius-xl);box-shadow: var(--shadow);border: 1px solid rgba(0,0,0,.04);padding: clamp(12px, 2vw, 22px);min-width: 0;overflow: hidden;}
  .chart-head{display: flex; align-items: baseline; gap: 10px;margin-bottom: 14px;flex-wrap: wrap;}
  .vehicle-toggle{margin-left: auto;display: inline-flex;background: rgba(0,0,0,.05);border-radius: 8px;padding: 3px;gap: 0;flex-shrink: 0;}
  .vt-btn{padding: 5px 12px;border: none;background: transparent;color: var(--text2);font-family: inherit;font-size: 14px; font-weight: 500;cursor: pointer;border-radius: 5px;white-space: nowrap;letter-spacing: -0.01em;transition: all .15s var(--ease);}
  .vt-btn:hover{color: var(--text)}
  .vt-btn.active{background: #fff;color: var(--text);font-weight: 600;box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 1px rgba(0,0,0,.04);}
  .chart-title{font-size: clamp(14px, 1.5vw, 16px); font-weight: 600;color: var(--text); letter-spacing: -0.02em;}
  .chart-sub{font-size: 14px; color: var(--text3); font-weight: 400;}
  .chart-canvas{position: relative; width: 100%; min-width: 0;}
  .chart-scroll{overflow-x: hidden; overflow-y: hidden}
  .chart-scroll::-webkit-scrollbar{height: 5px}
  .chart-scroll::-webkit-scrollbar-thumb{background: rgba(0,0,0,.15); border-radius: 3px}
  .chart-inner{height: 100%; width: 100%; min-width: 0;}
  .chart-legend{display: flex; gap: 14px; flex-wrap: wrap;margin-top: 12px; padding-top: 12px;border-top: 0.5px solid var(--separator);}
  .chart-legend-item{display: inline-flex; align-items: center; gap: 6px;font-size: 14px; color: var(--text2); font-weight: 500;}
  .chart-legend-dot{width: 9px; height: 9px; border-radius: 3px}

  .report-header{display: flex; align-items: center; justify-content: space-between;padding: 20px 24px; margin-bottom: 20px;background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color: #fff;border-radius: var(--radius-xl);box-shadow: var(--shadow);}
  .report-title{font-size: 20px; font-weight: 700;letter-spacing: -0.02em;}
  .report-sub{font-size: 14px; opacity: .8; margin-top: 3px}
  .report-back{background: rgba(255,255,255,.15); color: #fff;border: 0.5px solid rgba(255,255,255,.2);padding: 7px 14px; border-radius: 100px;font-family: inherit; font-size: 14px; font-weight: 500;cursor: pointer;transition: all .15s var(--ease);}
  .report-back:hover{background: rgba(255,255,255,.25)}
  .report-stat-row{display: grid;grid-template-columns: repeat(6, 1fr);gap: 12px; margin-bottom: 20px;}
  .report-stat-card{background: var(--bg-card);border-radius: var(--radius-lg);padding: 16px 18px;box-shadow: var(--shadow-sm);}
  .report-stat-label{font-size: 14px; color: var(--text3); font-weight: 500;margin-bottom: 6px;}
  .report-stat-value{font-size: 22px; font-weight: 700;color: var(--text); letter-spacing: -0.02em;font-family: var(--font-mono);}
  .report-stat-sub{font-size: 14px; color: var(--text4); margin-top: 2px}
  .report-pie-grid{display: grid;grid-template-columns: repeat(3, 1fr);gap: 16px;}
  .pie-card{background: var(--bg-card);border-radius: var(--radius-xl);padding: 20px;box-shadow: var(--shadow-sm);}
  .pie-card-title{font-size: 14.5px; font-weight: 600; color: var(--text); letter-spacing: -0.01em}
  .pie-card-sub{font-size: 14px; color: var(--text3); margin: 2px 0 14px}
  .pie-canvas-wrap{height: 220px; position: relative}
  .pie-legend{margin-top: 12px; display: flex; flex-direction: column; gap: 6px}
  .pie-legend-item{display: flex; align-items: center; gap: 8px; font-size: 14px}
  .pie-legend-dot{width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0}
  .pie-legend-label{flex: 1; color: var(--text2); font-weight: 500}
  .pie-legend-val{color: var(--text3); font-size: 14px; font-family: var(--font-mono)}

  .toast{position: fixed; top: 72px; right: 28px; z-index: 200;background: rgba(255,255,255,.95);backdrop-filter: blur(20px);border-radius: 14px;box-shadow: var(--shadow-xl);padding: 12px 16px;display: flex; align-items: center; gap: 12px;min-width: 280px; max-width: 380px;border-left: 3px solid var(--green);overflow: hidden;animation: toast-in .35s var(--ease-out);cursor: pointer;}
  .toast.hiding{animation: toast-out .25s var(--ease) forwards}
  @keyframes toast-in{from{opacity: 0; transform: translateY(-10px) scale(.96)}to{opacity: 1; transform: translateY(0) scale(1)}}
  @keyframes toast-out{to{opacity: 0; transform: translateY(-10px)}}
  .toast-icon{font-size: 22px}
  .toast-body{flex: 1; min-width: 0}
  .toast-title{font-size: 14px; font-weight: 600; color: var(--text); letter-spacing: -0.01em}
  .toast-msg{font-size: 14px; color: var(--text3); margin-top: 1px}
  .toast-progress{position: absolute; left: 0; bottom: 0; height: 2px; background: var(--green); width: 100%}

  .clock-modal{position: fixed; inset: 0; z-index: 150;background: rgba(0,0,0,.4);backdrop-filter: blur(8px);align-items: center; justify-content: center;padding: 20px;}
  .clock-box{background: rgba(255,255,255,.98);border-radius: 18px; padding: 22px;max-width: 340px; width: 100%;box-shadow: var(--shadow-xl);}
  .clock-header{display: flex; justify-content: space-between; align-items: center;margin-bottom: 14px; font-size: 15px; font-weight: 600;letter-spacing: -0.01em;}
  .clock-close{background: transparent; border: none;font-size: 22px; cursor: pointer; color: var(--text3);width: 28px; height: 28px; border-radius: 100px;display: flex; align-items: center; justify-content: center;}
  .clock-close:hover{background: rgba(0,0,0,.06)}
  .clock-tabs{display: flex; gap: 4px; margin-bottom: 14px;background: rgba(0,0,0,.05); padding: 3px; border-radius: 8px;}
  .clock-tab{flex: 1; padding: 6px 10px; border-radius: 6px; border: none;background: transparent; color: var(--text2);font-family: inherit; font-size: 14px; font-weight: 500;cursor: pointer;transition: all .15s var(--ease);}
  .clock-tab.active{background: #fff; color: var(--text); font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,.08)}
  .clock-current{text-align: center; font-size: 30px; font-weight: 700;margin-bottom: 14px; color: var(--text);font-family: var(--font-mono); letter-spacing: -0.02em;}
  #clock-face{display: block; margin: 0 auto}
  .clock-ok{width: 100%; margin-top: 16px; padding: 11px;background: var(--blue); color: #fff;border: none; border-radius: 100px;font-family: inherit; font-size: 14px; font-weight: 600;cursor: pointer;transition: all .15s var(--ease);}
  .clock-ok:hover{background: var(--blue-hover)}

  .drp-popup{position: fixed; z-index: 100; width: 340px;background: rgba(255,255,255,.98);backdrop-filter: blur(20px);border-radius: 14px;border: 0.5px solid var(--separator);box-shadow: var(--shadow-xl);padding: 14px;display: none;}
  .drp-popup.open{display: block}
  .drp-header{display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px}
  .drp-nav-btn{width: 28px; height: 28px; border-radius: 100px;border: none; background: rgba(0,0,0,.05); color: var(--text2);font-size: 14px; cursor: pointer;transition: background .12s;}
  .drp-nav-btn:hover{background: rgba(0,0,0,.08); color: var(--blue)}
  .drp-title{font-size: 14px; font-weight: 600; color: var(--text); letter-spacing: -0.01em}
  .drp-weekdays{display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; margin-bottom: 4px}
  .drp-weekday{text-align: center; padding: 6px 0; font-size: 14px; color: var(--text4); font-weight: 600}
  .drp-days{display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px}
  .drp-day{aspect-ratio: 1; border: none; border-radius: 100px;font-family: var(--font-mono); font-size: 14px;background: transparent; color: var(--text); cursor: pointer;transition: background .12s;}
  .drp-day:hover{background: rgba(0,0,0,.05)}
  .drp-day.muted{color: var(--text5)}
  .drp-day.today{font-weight: 700; color: var(--blue)}
  .drp-day.selected, .drp-day.range-start, .drp-day.range-end{background: var(--blue); color: #fff; font-weight: 600;}
  .drp-day.in-range{background: rgba(245,158,11,.15); color: var(--blue-hover); border-radius: 0}
  .drp-day.range-start{border-top-right-radius: 0; border-bottom-right-radius: 0}
  .drp-day.range-end{border-top-left-radius: 0; border-bottom-left-radius: 0}
  .drp-hint{margin-top: 10px; padding: 7px; text-align: center;font-size: 14px; color: var(--text3);background: var(--bg-subtle); border-radius: 8px;}
  .drp-footer{display: flex; justify-content: space-between; align-items: center; margin-top: 10px; gap: 8px}
  .drp-presets{display: flex; gap: 4px}
  .drp-preset-btn{padding: 5px 10px; border-radius: 100px;border: 0.5px solid var(--separator-strong);background: #fff; color: var(--text2);font-family: inherit; font-size: 14px; cursor: pointer;transition: all .12s;}
  .drp-preset-btn:hover{background: var(--bg-subtle); border-color: var(--blue); color: var(--blue)}
  .drp-apply-btn{padding: 6px 14px; border-radius: 100px; border: none;background: var(--blue); color: #fff;font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer;}
  .drp-apply-btn:disabled{opacity: .4; cursor: not-allowed}

  .alert{padding: 11px 14px; border-radius: 10px;font-size: 14px;margin-bottom: 14px;}
  .alert-error{background: var(--red-light); color: var(--red);border: 0.5px solid rgba(239,68,68,.2);}

  @media (max-width: 1280px){.report-stat-row{grid-template-columns: repeat(3, 1fr)}}
  @media (max-width: 1024px){.dual-grid{grid-template-columns: 1fr}.dual-grid .card{min-height: 400px; max-height: 520px}.charts-grid{grid-template-columns: 1fr}.report-pie-grid{grid-template-columns: 1fr 1fr}}
  @media (max-width: 900px){.topnav-toggle{display: inline-flex}.topnav-main{padding: 0 16px; gap: 12px}.topnav-menu{position: absolute; top: 52px; left: 0; right: 0;background: rgba(255,255,255,.95);backdrop-filter: blur(20px);flex-direction: column; align-items: stretch;gap: 0; padding: 8px;border-bottom: 0.5px solid var(--separator);box-shadow: 0 8px 24px rgba(0,0,0,.08);display: none;}.topnav-menu.open{display: flex}.topnav-menu .nav-item{width: 100%; justify-content: flex-start;padding: 11px 14px; font-size: 14px;}.topnav-time{display: none}.topnav-filters{padding: 0 16px; gap: 10px}.filter-group-label{display: none}.main{padding: 18px 16px}.hero-title{font-size: 24px}.report-stat-row{grid-template-columns: repeat(2, 1fr)}.report-pie-grid{grid-template-columns: 1fr}.search-pill input{width: 140px}}
  @media (max-width: 600px){.topnav-brand .title-text{display: none}.hero{margin-bottom: 16px}.hero-title{font-size: 22px}.hero-sub{font-size: 14px}.card-head{padding: 14px 16px}.search-pill input{width: 120px}}

  /* ════ MOBILE: fuel-table → card layout (≤640px) ════ */
  @media (max-width: 640px){
    .fuel-table colgroup{display: none}
    .fuel-table thead{display: none}
    .fuel-table, .fuel-table tbody, .fuel-table tr, .fuel-table td{display: block; width: 100%}
    .fuel-table tbody td:nth-child(n){display: flex !important}
    .fuel-table tr{
      background: var(--bg-card);
      border: 1px solid var(--separator);
      border-radius: 14px;
      margin-bottom: 10px;
      padding: 12px 14px;
      box-shadow: var(--shadow-xs);
    }
    .fuel-table tr:hover{background: var(--bg-card)}
    .fuel-table td{
      display: flex !important;
      justify-content: space-between;
      align-items: center;
      padding: 5px 0 !important;
      border: none !important;
      text-align: right;
      font-size: 14px;
    }
    .fuel-table td::before{
      content: attr(data-label);
      font-weight: 600;
      color: var(--text3);
      font-size: 14px;
      margin-right: 12px;
      text-align: left;
      font-family: var(--font-thai);
    }
    .fuel-table td.row-idx{display: none !important}
    .fuel-table td[data-label="คนขับ"]{
      border-bottom: 1px solid var(--separator) !important;
      padding-bottom: 10px !important;
      margin-bottom: 4px;
    }
    .fuel-table td[data-label="คนขับ"]::before{display: none}
    .fuel-table td[data-label="คนขับ"] .driver-cell{text-align: left; width: 100%}
    .fuel-table td[data-label="วันที่"]{justify-content: flex-start}
    .fuel-table .driver-name{font-size: 15px}
    .dual-grid{grid-template-columns: 1fr}
    .charts-grid{gap: 12px}
    .chart-card{padding: 14px}
    .topnav-filters{gap: 12px; padding: 8px 14px; height: auto; flex-wrap: nowrap}
    .filter-group{flex-shrink: 0}
    .pill-select, .pill-date, .date-trigger-pill{min-width: auto}
  }
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
  </style>
  </head>
  <body>

  @php
    $currentUser = request()->filled('create_by') ? request('create_by') : 'Guest';
    $userQuery = $currentUser !== 'Guest' ? '?create_by='.urlencode($currentUser) : '';
    $privilegedUsers = ['จัน','kanitin2','test101'];
    $isPrivileged = in_array(trim($currentUser), $privilegedUsers, true);
    // ════ Whitelist คนขับหลัก (ต้องตรงกับ JS ALLOWED_DRIVERS) ════
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
          <span class="topnav-user-avatar">{{ mb_substr(request()->filled('create_by') ? request('create_by') : 'Guest', 0, 1) }}</span>
          <span class="topnav-user-name">{{ request()->filled('create_by') ? request('create_by') : 'Guest' }}</span>
        </span>
        <span class="topnav-time">
          <span class="pulse"></span>
          <span id="navDate">—</span>
        </span>
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
          // normalize: ตัด zero-width + ์ + กลั่น space + lowercase
          $normDrv = function($s){
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
          };

          if($view === 'day'){
            // ── รายวัน: เอาทุกคนที่มีในตาราง logs ของวันนั้น (ไม่กรอง whitelist) ──
            $drvSource = collect($logs)->pluck('driver_name')
              ->map(fn($n)=>trim((string)$n))
              ->filter()->unique()->values()->all();
            $useWhitelist = false;
          } else {
            // ── เดือน/ปี/ทั้งหมด: กรองด้วย whitelist ตามเดิม ──
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
      $driverList=['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
      foreach($drivers as $dbD){if(!in_array($dbD,$driverList))$driverList[]=$dbD;}
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
            <div class="erh-driver">คนขับ</div>
            <div class="erh-plate">ทะเบียนรถ</div>
            <div class="erh-time">เวลา</div>
            <div class="erh-price" title="ใส่ 0 ได้ถ้าไม่ได้เติมน้ำมัน">ค่าน้ำมัน (฿)</div>
            <div class="erh-distance">ระยะ (km)</div>
            <div class="erh-summary">สรุป</div>
            <div class="erh-action">บันทึก</div>
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
          <div class="job-loading">คลิกที่ช่องในแถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>
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
            <span class="si">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </span>
            <input type="text" placeholder="ค้นหา" oninput="filterOilTable(this.value)">
          </div>
        </div>

        <div class="fuel-table-scroll">
        <table class="fuel-table">
          <colgroup>
            <col style="width:44px">
            <col style="width:78px">
            <col style="width:auto;min-width:170px">
            <col style="width:96px">
            <col style="width:70px">
            <col style="width:80px">
            <col style="width:66px">
            <col style="width:84px">
            <col style="width:74px">
            <col style="width:78px">
          </colgroup>
          <thead>
            <tr>
              <th>#</th>
              <th>วันที่</th>
              <th>คนขับ / ทะเบียน</th>
              <th>เวลา</th>
              <th class="num">ชม.</th>
              <th class="num">ระยะ</th>
              <th class="num">ลิตร</th>
              <th class="num">฿</th>
              <th class="num">KM/L</th>
              <th class="num">฿/km</th>
            </tr>
          </thead>
          <tbody id="oilTbody">
            @php
              $rowNo = 0;
              // ════════════════════════════════════════════════════════════
              // Carry-forward logic — ใช้ $allLogs (ทุก row ใน DB)
              // เพื่อให้ carry ข้ามเดือน/ปี ทำงานถูกต้อง
              // จากนั้นค่อย apply กับ row ที่อยู่ใน $logs (filtered view)
              // ════════════════════════════════════════════════════════════
              $allArr = $allLogs->all();
              // Group by "ทะเบียน" อย่างเดียว — น้ำมันอยู่ในถังของรถ
              // รถคันเดียวกัน ใครขับก็ carry ระยะรวมกัน
              $byKey = [];
              foreach($allArr as $idx => $r){
                $k = $r['vehicle_id'] ?? '';
                if(!isset($byKey[$k])) $byKey[$k] = [];
                $byKey[$k][] = $idx;
              }
              $effDistance = [];   // [id => effective km used]
              $effKml      = [];   // [id => recalculated km/L]
              $isCarryRow  = [];   // [id => true if price=0]
              foreach($byKey as $k => $indices){
                // เรียง old→new ตาม work_date แล้วตาม id (กันกรณีหลายคนขับวันเดียวกัน)
                usort($indices, function($a, $b) use ($allArr){
                  $ra = $allArr[$a]; $rb = $allArr[$b];
                  $da = $ra['work_date'] ?? ''; $db = $rb['work_date'] ?? '';
                  if($da !== $db) return strcmp($da, $db);
                  return ((int)($ra['id']??0)) <=> ((int)($rb['id']??0));
                });
                $sorted = $indices;
                $pending = 0;
                foreach($sorted as $idx){
                  $r = $allArr[$idx];
                  $rid = (int)($r['id'] ?? 0);
                  if(!$rid) continue;
                  $price = (float)($r['total_price'] ?? 0);
                  $thisDist = (float)($r['total_distance'] ?? 0);
                  if($price <= 0){
                    $pending += $thisDist;
                    $isCarryRow[$rid] = true;
                    $effDistance[$rid] = 0;
                    $effKml[$rid] = 0;
                  } else {
                    $eff = $thisDist + $pending;
                    $effDistance[$rid] = $eff;
                    $liters = (float)($r['liters'] ?? 0);
                    $effKml[$rid] = ($liters > 0 && $eff > 0) ? round($eff / $liters, 2) : 0;
                    $isCarryRow[$rid] = false;
                    $pending = 0;
                  }
                }
              }
            @endphp
            @php
              // เรียงคนใน whitelist ขึ้นก่อน ตามลำดับใน $allowedDrivers (global)
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
                // ลำดับเดียวกัน → เรียงตามวันที่ใหม่→เก่า
                return strcmp($b['work_date'] ?? '', $a['work_date'] ?? '');
              });
              $logsSorted = collect($logsArr2);
            @endphp
            @forelse($logsSorted as $i => $r)
            @php
              $rowNo++;
              $rid = (int)($r['id'] ?? 0);
              $isCarry = $isCarryRow[$rid] ?? false;
              $effDist = $effDistance[$rid] ?? ((float)($r['total_distance']??0));
              $kml = $effKml[$rid] ?? ($r['km_per_liter']??0);
              $rawDist = (float)($r['total_distance']??0);
              $carryAmt = $effDist - $rawDist;
              if($rawDist > 0){
                $distHtml = number_format($rawDist).' km';
                if($carryAmt > 0){
                  $distHtml .= '<div class="carry-hint" title="รวมระยะจากวันที่ไม่เติม">+'.number_format($carryAmt).' km สะสม</div>';
                }
              } else {
                $distHtml = '—';
              }
              $name = $r['driver_name'] ?? '—';
              $plate = $r['vehicle_id'] ?? '—';
              $kmlClass = 'km-mid';
              if($kml >= 13) $kmlClass = 'km-good';
              elseif($kml > 0 && $kml < 9) $kmlClass = 'km-bad';
              $tStart = $r['start_time'] ?? '';
              $tEnd = $r['end_time'] ?? '';
              if(strlen($tStart) >= 5) $tStart = substr($tStart, 0, 5);
              if(strlen($tEnd) >= 5) $tEnd = substr($tEnd, 0, 5);
              $timeText = ($tStart && $tEnd) ? $tStart.'-'.$tEnd : '—';
              // ระยะเวลาทำงาน (จาก work_hours ที่ controller คำนวณไว้แล้ว)
              $wh = (float)($r['work_hours'] ?? 0);
              $durText = '';
              if($wh > 0){
                $totalMin = (int) round($wh * 60);
                $days = intdiv($totalMin, 1440);          // 1440 = 24*60
                $hh   = intdiv($totalMin % 1440, 60);
                $mm   = $totalMin % 60;
                if($days > 0){
                  // เกิน 24 ชม. → แสดงเป็น "X วัน Y ชม."
                  $durText = $days.' วัน';
                  if($hh > 0) $durText .= ' '.$hh.' ชม.';
                  if($mm > 0) $durText .= ' '.$mm.' น.';
                } elseif($hh > 0 && $mm > 0) {
                  $durText = $hh.' ชม. '.$mm.' น.';
                } elseif($hh > 0) {
                  $durText = $hh.' ชม.';
                } else {
                  $durText = $mm.' น.';
                }
              }
              $workDate = $r['work_date'] ?? '';
              $dateText = '—';
              $dateFull = '';
              if($workDate){
                try {
                  $dt = \Carbon\Carbon::parse($workDate);
                  $dateText = $dt->format('d/m');
                  $dateFull = $dt->format('d/m/Y');
                } catch(\Exception $e){ $dateText = '—'; }
              }
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
              @php
                // ฿/km = ราคา ÷ ระยะ effective (มี carry-forward แล้ว)
                $thbPerKm = ($effDist > 0 && ($r['total_price']??0) > 0) ? ($r['total_price'] / $effDist) : 0;
              @endphp
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
                if(!isset($uniqueDrivers[$n])){
                  $uniqueDrivers[$n] = ['name'=>$n,'rounds'=>0,'distance'=>0,'liters'=>0,'price'=>0,'kml_sum'=>0,'kml_count'=>0];
                }
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
          @forelse($byPrice as $idx => $d)
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
                <span>{{ $d['rounds'] }} รอบ</span>
                <span>·</span>
                <span>{{ number_format($d['distance']) }} km</span>
                <span>·</span>
                <span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span>
              </div>
            </div>
            <div class="right">
              <div class="price">฿{{ number_format($d['price']) }}</div>
              @if($avgKmlD > 0)
              <div class="kml {{ $kmlBad ? 'warn' : '' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>
              @endif
              @if($thbPerKmD > 0)
              <div class="thb-km">฿{{ number_format($thbPerKmD, 2) }}/km</div>
              @endif
            </div>
          </div>
          @empty
          <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
          @endforelse
        </div>

        <div class="driver-list" id="rankListDistance" style="display:none">
          @php $rankNo = 0; @endphp
          @forelse($byDistance as $idx => $d)
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
                <span>{{ $d['rounds'] }} รอบ</span>
                <span>·</span>
                <span>฿{{ number_format($d['price']) }}</span>
                <span>·</span>
                <span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span>
              </div>
            </div>
            <div class="right">
              <div class="price">{{ number_format($d['distance']) }} <span style="font-size: 14px;color:var(--text3);font-weight:500">km</span></div>
              @if($avgKmlD > 0)
              <div class="kml {{ $kmlBad ? 'warn' : '' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>
              @endif
              @if($thbPerKmD > 0)
              <div class="thb-km">฿{{ number_format($thbPerKmD, 2) }}/km</div>
              @endif
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
        <div class="chart-canvas chart-scroll" style="height:300px">
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
        <div class="pie-card-title">ค่าน้ำมัน</div>
        <div class="pie-card-sub">แยกตามคนขับ</div>
        <div class="pie-canvas-wrap"><canvas id="pieCost"></canvas></div>
        <div class="pie-legend" id="pieCostLegend"></div>
      </div>
      <div class="pie-card">
        <div class="pie-card-title">ลิตรที่เติม</div>
        <div class="pie-card-sub">แยกตามคนขับ</div>
        <div class="pie-canvas-wrap"><canvas id="pieLiters"></canvas></div>
        <div class="pie-legend" id="pieLitersLegend"></div>
      </div>
      <div class="pie-card">
        <div class="pie-card-title">ชั่วโมงทำงาน</div>
        <div class="pie-card-sub">แยกตามคนขับ</div>
        <div class="pie-canvas-wrap"><canvas id="pieHours"></canvas></div>
        <div class="pie-legend" id="pieHoursLegend"></div>
      </div>
    </div>
    <canvas id="chartDriver" style="display:none"></canvas>
  </div>

  </main>

  <div id="clock-modal" class="clock-modal" style="display:none" onclick="if(event.target===this)closeClock()">
    <div class="clock-box">
      <div class="clock-header">
        <span id="clock-title">เลือกเวลา</span>
        <button type="button" class="clock-close" onclick="closeClock()">×</button>
      </div>
      <div class="clock-tabs">
        <button type="button" id="tab-hour" class="clock-tab active" onclick="switchTab('hour')">ชั่วโมง</button>
        <button type="button" id="tab-min" class="clock-tab" onclick="switchTab('min')">นาที</button>
      </div>
      <div class="clock-current"><span id="clock-h-val">00</span>:<span id="clock-m-val">00</span></div>
      <svg id="clock-face" viewBox="0 0 240 240" width="240" height="240"></svg>
      <button type="button" class="clock-ok" onclick="confirmClock()">ตกลง</button>
    </div>
  </div>

  <script>
  const ROUTE_STORE    = '{{ route("oil") }}';
  const ROUTE_FILTER   = '{{ route("oil.filter") }}';
  const ROUTE_PREVMILE = '{{ route("oil.prevMileage") }}';
  const ROUTE_SYNC_NG  = '{{ route("oil.syncNg") }}';
  const ROUTE_SAVED_DRIVERS = '{{ url("/oil/saved-drivers") }}';
  const CURRENT_USER   = @json(request()->filled('create_by') ? request('create_by') : 'Guest');
  // รายชื่อ user ที่เห็นรายละเอียดเพิ่มเติม (ผู้รับเข้า + ลิงก์ไป sodetail)
  // คำนวณจาก PHP ฝั่ง server เพื่อกันการ tamper ที่ frontend
  const IS_PRIVILEGED = @json($isPrivileged);
  const URL_SODETAIL = 'http://server_update:8000/sodetail';
  console.log('[privilege]', {
    CURRENT_USER,
    IS_PRIVILEGED,
    isPrivilegedType: typeof IS_PRIVILEGED,
  });
  const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const TZ = 'Asia/Bangkok';
  // แสดงตัวเลขแบบไม่ปัด ตัด 0 ท้ายออก: 11.90→11.9, 12.00→12, 422.43→422.43
  function fmtN(v, max=2){ return (+(+v).toFixed(max)).toString(); }
  const DB_DRIVERS = @json($drivers);
  window.PLATE_LIST = @json($plateList);
  const MAIN_VIEW = @json($view);

  function toggleTopMenu(){document.getElementById('topMenu')?.classList.toggle('open');}
  function closeMobileMenu(){if(window.innerWidth>900)return;document.getElementById('topMenu')?.classList.remove('open');}

  function nowThai(){return new Date(new Date().toLocaleString('en-US',{timeZone:TZ}));}
  function todayStr(){const d=nowThai();return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function updateNavDate(){
    const time = new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});
    const el = document.getElementById('navDate'); if(el) el.textContent = time;
  }

  function submitFilterForm(params){
    const form = document.createElement('form');
    form.method='POST';form.action=ROUTE_FILTER;form.style.display='none';
    const add=(n,v)=>{if(v==null||v==='')return;const i=document.createElement('input');i.type='hidden';i.name=n;i.value=v;form.appendChild(i);};
    add('_token',CSRF_TOKEN);
    // แปะ create_by ลง form ทุกครั้งเพื่อให้ controller ส่งกลับมา + ใส่ใน URL หลัง redirect
    if(CURRENT_USER && CURRENT_USER !== 'Guest') add('create_by', CURRENT_USER);
    Object.keys(params).forEach(k=>add(k,params[k]));
    document.body.appendChild(form);form.submit();
  }
  function switchView(v){
    const params={view:v};
    const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;
    const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;
    if(v==='month'){const el=document.getElementById('monthPicker');if(el&&el.value)params.month=el.value;}
    else if(v==='year'){const el=document.getElementById('yearPicker');if(el&&el.value)params.year=el.value;}
    submitFilterForm(params);
  }
  function submitFilter(){
    const params={view:MAIN_VIEW};
    const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;
    const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;
    const me=document.getElementById('monthPicker');if(me&&me.value)params.month=me.value;
    const ye=document.getElementById('yearPicker');if(ye&&ye.value)params.year=ye.value;
    // day view: ส่งช่วงวันที่ที่เลือกอยู่ด้วย (ไม่งั้น controller reset เป็นวันนี้)
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
    if(ye&&ye.value){try{sessionStorage.setItem('oilPickedYear',ye.value);}catch(e){}}
    submitFilter();
  }
  (function restoreYear(){try{const s=sessionStorage.getItem('oilPickedYear');if(!s)return;document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('yearPicker');if(!el)return;const ok=Array.from(el.options).some(o=>o.value===s);if(ok&&el.value!==s)el.value=s;});}catch(e){}})();

  function filterOilTable(q){oilSearchQuery=q.toLowerCase();renderOilPage();}

  function switchRankSort(mode){
    document.querySelectorAll('.sort-btn').forEach(b=>{
      b.classList.toggle('active', b.dataset.sort===mode);
    });
    const listPrice = document.getElementById('rankListPrice');
    const listDist  = document.getElementById('rankListDistance');
    if(listPrice && listDist){
      listPrice.style.display = (mode==='price') ? '' : 'none';
      listDist.style.display  = (mode==='distance') ? '' : 'none';
    }
    try { sessionStorage.setItem('oilRankSort', mode); } catch(e){}
  }
  (function restoreRankSort(){
    try {
      const saved = sessionStorage.getItem('oilRankSort');
      if(!saved) return;
      document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('rankListPrice') && document.getElementById('rankListDistance')){
          switchRankSort(saved);
        }
      });
    } catch(e){}
  })();

  function showReportPage(){
    document.getElementById('pageTracking').style.display='none';
    document.getElementById('pageReport').style.display='';
    document.querySelectorAll('.topnav-menu .nav-item').forEach(n=>n.classList.remove('active'));
    document.getElementById('navReport')?.classList.add('active');
    if(typeof renderReportPage==='function')renderReportPage();
    window.scrollTo({top:0,behavior:'smooth'});
  }
  function showTrackingPage(){
    document.getElementById('pageTracking').style.display='';
    document.getElementById('pageReport').style.display='none';
    document.querySelectorAll('.topnav-menu .nav-item').forEach(n=>n.classList.remove('active'));
    document.getElementById('navHome')?.classList.add('active');
    window.scrollTo({top:0,behavior:'smooth'});
  }

  let dlvChart=null;
  @php
    $deliveryByDriver=[];
    foreach($logs as $log){
      $driver=$log['driver_name']??'ไม่ระบุ';
      if(!isset($deliveryByDriver[$driver])){$deliveryByDriver[$driver]=['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];}
      $deliveryByDriver[$driver]['success']+=(int)($log['delivery_success']??$log['success_count']??$log['ok_count']??0);
      $deliveryByDriver[$driver]['fail']+=(int)($log['delivery_fail']??$log['fail_count']??$log['ng_count']??0);
    }
  @endphp
  const DLV_BY_DRIVER=@json($deliveryByDriver);
  function renderDlv(){
    // แสดงเฉพาะคนใน whitelist
    const drivers=Object.keys(DLV_BY_DRIVER).filter(d=>isAllowedDriver(d));
    if(drivers.length===0){if(dlvChart)dlvChart.destroy();document.getElementById('dlvLegend').innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>';return;}
    // เรียงตามลำดับใน ALLOWED_DRIVERS (คนหลักขึ้นก่อน)
    const orderIdx=name=>{const i=ALLOWED_DRIVERS.map(_normalizeDriver).indexOf(_normalizeDriver(name));return i<0?999:i;};
    const sorted=drivers.map(d=>({name:d,plate:DLV_BY_DRIVER[d].plate,s:DLV_BY_DRIVER[d].success,f:DLV_BY_DRIVER[d].fail})).sort((a,b)=>orderIdx(a.name)-orderIdx(b.name));
    const inner=document.getElementById('deliveryChartInner');
    const sw=inner?inner.parentElement:null;
    if(inner&&sw){
      // Fit container — บีบ bar เข้าให้พอดี ไม่ scroll
      inner.style.width='100%';
      inner.style.height='100%';
    }
    const labels=sorted.map(d=>d.name);
    const success=sorted.map(d=>d.s);
    const fail=sorted.map(d=>d.f);
    if(dlvChart)dlvChart.destroy();
    dlvChart=new Chart(document.getElementById('deliveryChart'),{
      type:'bar',
      data:{labels,datasets:[
        {label:'ส่งสำเร็จ',data:success,backgroundColor:'#10b981',borderRadius:{topLeft:0,topRight:0,bottomLeft:6,bottomRight:6},borderSkipped:false,stack:'s',maxBarThickness:50},
        {label:'ส่งไม่สำเร็จ',data:fail,backgroundColor:'#ef4444',borderRadius:{topLeft:6,topRight:6,bottomLeft:0,bottomRight:0},borderSkipped:false,stack:'s',maxBarThickness:50},
      ]},
      plugins:[ChartDataLabels],
      options:{
        responsive:true,maintainAspectRatio:false,
        layout:{padding:{top:20,left:10,right:10}},
        plugins:{
          legend:{display:false},
          tooltip:{callbacks:{title:items=>Array.isArray(items[0].label)?items[0].label[0]:items[0].label,label:ctx=>`${ctx.dataset.label}: ${ctx.raw} รายการ`,footer:items=>'รวม: '+items.reduce((s,i)=>s+i.raw,0)+' รายการ',}},
          datalabels:{color:'#fff',font:{weight:'700',size:13,family:'Inter'},formatter:v=>v>0?v:'',display:ctx=>ctx.dataset.data[ctx.dataIndex]>0,anchor:'center',align:'center'},
        },
        scales:{
          x:{stacked:true,ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#18181b',autoSkip:true,maxRotation:0,minRotation:0,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}},grid:{display:false}},
          y:{stacked:true,beginAtZero:true,ticks:{font:{size:14,family:'Inter'},color:'#71717a',stepSize:2,callback:v=>v+' '},grid:{color:'rgba(0,0,0,.05)'}},
        },
      },
    });
    document.getElementById('dlvLegend').innerHTML=`
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ส่งสำเร็จ</div>
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ส่งไม่สำเร็จ</div>
    `;
  }

  @php
    // group: ถ้าเลือก filter ทะเบียนแล้ว → group แค่ทะเบียน (ไม่แยกคน)
    //        ถ้าไม่เลือก → group ตาม ทะเบียน+คนขับ (แยกหมด)
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
  @endphp
  const KML_BY_DRIVER=@json($kmlByDriver);
  // ตัวจำแนกรถยนต์/มอเตอร์ไซค์ จากชื่อทะเบียน
  function isMoto(plate){
    const p = (plate||'').trim();
    return p.startsWith('มอเตอร์ไซด์') || p.startsWith('มอเตอร์ไซค์') || p.startsWith('มอ.') || p.startsWith('มอ ');
  }
  // State ของ toggle (default: car)
  const VEHICLE_TYPE = {kml:'car', cost:'car'};
  function switchVehicleType(chart, type){
    VEHICLE_TYPE[chart] = type;
    // Update button active state
    document.querySelectorAll(`.vehicle-toggle[data-chart="${chart}"] .vt-btn`).forEach(b=>{
      b.classList.toggle('active', b.dataset.type === type);
    });
    // Re-render the chart
    if(chart === 'kml') renderKmlChart();
    else if(chart === 'cost') renderCostChart();
  }
  let kmlChart=null;
  function renderKmlChart(){
    const vType = VEHICLE_TYPE.kml || 'car';
    const drivers=Object.keys(KML_BY_DRIVER).map(key=>({name:KML_BY_DRIVER[key].driver||'',plate:KML_BY_DRIVER[key].plate||key,avg:KML_BY_DRIVER[key].count>0?KML_BY_DRIVER[key].sum/KML_BY_DRIVER[key].count:0}))
      .filter(d=>d.avg>0)
      .filter(d => vType==='moto' ? isMoto(d.plate) : !isMoto(d.plate))
      .sort((a,b)=>b.avg-a.avg);
    if(drivers.length===0){
      if(kmlChart)kmlChart.destroy();
      const typeLabel = vType==='moto' ? 'มอเตอร์ไซค์' : 'รถยนต์';
      document.getElementById('kmlLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${typeLabel}</span>`;
      return;
    }
    const inner=document.getElementById('chartKmlInner');
    const sw=inner?inner.parentElement:null;
    if(inner&&sw){
      const ROW=44;
      const minH=Math.max(drivers.length*ROW+40, 300);
      inner.style.width='100%';
      inner.style.height=minH+'px';
      sw.style.height='auto';
      sw.style.maxHeight='none';
    }
    const labels=drivers.map(d=>[d.plate||d.name, d.name && d.plate ? d.name : '']);
    const data=drivers.map(d=>d.avg);
    const overallAvg=data.reduce((a,b)=>a+b,0)/data.length;
    // เกณฑ์ auto = ค่าเฉลี่ยรวม — ต่ำกว่าเฉลี่ยมาก=แดง, ใกล้เฉลี่ย=ส้ม, สูงกว่า=เขียว
    const lowBand=overallAvg*0.9;   // ต่ำกว่าเฉลี่ย 10% = ผิดปกติ
    const barColors=data.map(v=>v<lowBand?'#ef4444':(v<overallAvg?'#f59e0b':'#10b981'));
    const maxVal=Math.max(...data,overallAvg);
    const xMax=Math.ceil((maxVal+1)/2)*2;
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
        indexAxis:'y',
        responsive:true,maintainAspectRatio:false,
        layout:{padding:{top:10,right:50,left:6,bottom:6}},
        plugins:{
          legend:{display:false},
          tooltip:{callbacks:{label:ctx=>`เฉลี่ย: ${fmtN(ctx.raw)} km/L`,afterLabel:ctx=>{const d=drivers[ctx.dataIndex];return d.name?`คนขับ: ${d.name}`:'';}}},
          datalabels:{color:'#18181b',font:{weight:'700',size:11,family:'Inter'},anchor:'end',align:'right',offset:4,formatter:v=>fmtN(v)+' km/L'},
        },
        scales:{
          x:{beginAtZero:true,suggestedMax:xMax,ticks:{stepSize:2,font:{size:14,family:'Inter'},color:'#71717a',callback:v=>v+''},grid:{color:'rgba(0,0,0,.05)'}},
          y:{grid:{display:false},ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#3f3f46',autoSkip:false,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}}},
        },
      },
    });
    document.getElementById('kmlLegend').innerHTML=`
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (≥ เฉลี่ย)</div>
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (ใกล้เฉลี่ย)</div>
      <div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ผิดปกติ (ต่ำกว่าเฉลี่ย 10%)</div>
      <div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">${fmtN(overallAvg)} km/L</strong></div>
    `;
  }

  @php
    // group: filter ทะเบียน active → แค่ทะเบียน, ไม่ active → ทะเบียน+คนขับ
    $costByDriver=[];
    foreach($logs as $log){
      $plate=$log['vehicle_id']??'ไม่ระบุ';
      $driver=$log['driver_name']??'';
      $key = $plateFilterActive ? $plate : ($plate.'|'.$driver);
      $price=(float)($log['total_price']??0);
      $dist=(float)($log['total_distance']??0);
      if(!isset($costByDriver[$key]))$costByDriver[$key]=['price'=>0,'dist'=>0,'plate'=>$plate,'driver'=>($plateFilterActive ? '' : $driver)];
      $costByDriver[$key]['price']+=$price;
      $costByDriver[$key]['dist']+=$dist;
    }
  @endphp
  const COST_BY_DRIVER=@json($costByDriver);
  let costChart=null;
  function renderCostChart(){
    const vType = VEHICLE_TYPE.cost || 'car';
    const drivers=Object.keys(COST_BY_DRIVER).map(key=>({
      name:COST_BY_DRIVER[key].driver||'',plate:COST_BY_DRIVER[key].plate||key,
      cost:COST_BY_DRIVER[key].dist>0?COST_BY_DRIVER[key].price/COST_BY_DRIVER[key].dist:0,
      price:COST_BY_DRIVER[key].price,dist:COST_BY_DRIVER[key].dist,
    }))
      .filter(d=>d.cost>0)
      .filter(d => vType==='moto' ? isMoto(d.plate) : !isMoto(d.plate))
      .sort((a,b)=>a.cost-b.cost);
    if(drivers.length===0){
      if(costChart)costChart.destroy();
      const typeLabel = vType==='moto' ? 'มอเตอร์ไซค์' : 'รถยนต์';
      document.getElementById('costLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${typeLabel}</span>`;
      return;
    }
    const inner=document.getElementById('chartCostInner');
    const sw=inner?inner.parentElement:null;
    if(inner&&sw){
      const ROW=44;
      const minH=Math.max(drivers.length*ROW+40, 300);
      inner.style.width='100%';inner.style.height=minH+'px';
      sw.style.height='auto';sw.style.maxHeight='none';
    }
    const labels=drivers.map(d=>[d.plate||d.name, d.name && d.plate ? d.name : '']);
    const data=drivers.map(d=>d.cost);
    const avg=data.reduce((a,b)=>a+b,0)/data.length;
    const barColors=data.map(v=>{
      if(v<=avg*0.85)return '#10b981';
      if(v<=avg*1.05)return '#f59e0b';
      return '#ef4444';
    });
    const maxVal=Math.max(...data);
    const xMax=Math.ceil(maxVal*1.15);
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
        indexAxis:'y',
        responsive:true,maintainAspectRatio:false,
        layout:{padding:{top:10,right:70,left:6,bottom:6}},
        plugins:{
          legend:{display:false},
          tooltip:{callbacks:{
            label:ctx=>`฿${fmtN(ctx.raw)} / km`,
            afterLabel:ctx=>{
              const d=drivers[ctx.dataIndex];
              const lines=[];
              if(d.name)lines.push(`คนขับ: ${d.name}`);
              lines.push(`รวม ฿${d.price.toLocaleString(undefined,{maximumFractionDigits:0})} / ${d.dist.toLocaleString()} km`);
              return lines;
            }
          }},
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
      <div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">฿${fmtN(avg)}/km</strong></div>
    `;
  }

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
    const pop=document.getElementById('drpPopup');const trg=document.getElementById('drpTrigger');
    if(!pop||!trg)return;
    const r=trg.getBoundingClientRect();
    const popW=340,popH=420;
    let top=r.bottom+6;
    if(top+popH>window.innerHeight-8)top=Math.max(8,r.top-popH-6);
    let left=r.left;
    if(left+popW>window.innerWidth-8)left=Math.max(8,window.innerWidth-popW-8);
    pop.style.top=top+'px';pop.style.left=left+'px';
  }
  function drpToggle(e){
    if(e)e.stopPropagation();
    const pop=document.getElementById('drpPopup');const trg=document.getElementById('drpTrigger');
    if(pop.classList.contains('open')){pop.classList.remove('open');trg.classList.remove('active');}
    else{const a=drpFrom?drpParse(drpFrom):new Date();drpViewYear=a.getFullYear();drpViewMonth=a.getMonth();drpRender();drpPositionPopup();pop.classList.add('open');trg.classList.add('active');}
  }
  window.addEventListener('resize',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();});
  window.addEventListener('scroll',()=>{const p=document.getElementById('drpPopup');if(p&&p.classList.contains('open'))drpPositionPopup();},true);
  function drpNavMonth(d){drpViewMonth+=d;if(drpViewMonth<0){drpViewMonth=11;drpViewYear--;}if(drpViewMonth>11){drpViewMonth=0;drpViewYear++;}drpRender();}
  function drpRender(){
    document.getElementById('drpMonthTitle').textContent=`${TH_MONTHS_SHORT[drpViewMonth]} ${drpViewYear+543}`;
    const grid=document.getElementById('drpDays');
    const fd=new Date(drpViewYear,drpViewMonth,1);const fw=fd.getDay();
    const dim=new Date(drpViewYear,drpViewMonth+1,0).getDate();
    const pmd=new Date(drpViewYear,drpViewMonth,0).getDate();
    const ts=drpFmt(new Date());
    let h='';
    for(let i=fw-1;i>=0;i--){const d=pmd-i;const y=drpViewMonth===0?drpViewYear-1:drpViewYear;const m=drpViewMonth===0?11:drpViewMonth-1;const ds=`${y}-${drpPad(m+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,true,ts);}
    for(let d=1;d<=dim;d++){const ds=`${drpViewYear}-${drpPad(drpViewMonth+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,false,ts);}
    const total=fw+dim;const rem=(7-(total%7))%7;
    for(let d=1;d<=rem;d++){const y=drpViewMonth===11?drpViewYear+1:drpViewYear;const m=drpViewMonth===11?0:drpViewMonth+1;const ds=`${y}-${drpPad(m+1)}-${drpPad(d)}`;h+=drpDayBtn(ds,d,true,ts);}
    grid.innerHTML=h;
    const hint=document.getElementById('drpHint');
    if(!drpFrom)hint.textContent='เลือกวันเริ่มต้น';
    else if(drpFrom===drpTo)hint.textContent=drpFormatLabel(drpFrom,drpTo);
    else hint.textContent=drpFormatLabel(drpFrom,drpTo);
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
  function drpApply(){
    if(!drpFrom)return;
    const to=drpTo||drpFrom;
    // ใช้ date_from เป็น "วันที่ทำงาน" ของ entry-card
    const workDateInput = document.getElementById('il-work-date');
    if(workDateInput){
      workDateInput.value = drpFrom;
      // Reload entry-card data ทันทีโดยไม่ต้อง submit form
      if(typeof ilOnDateChange === 'function'){
        ilOnDateChange();
      }
    }
    // ปิด popup
    const pop=document.getElementById('drpPopup');
    const trg=document.getElementById('drpTrigger');
    if(pop) pop.classList.remove('open');
    if(trg) trg.classList.remove('active');
    // อัปเดต label
    drpUpdateLabel();
    // ถ้าช่วงวันที่ต่างจาก URL ปัจจุบัน → submit form เพื่อ reload table ด้านล่าง
    const wrap=document.querySelector('.drp-wrap[data-from]');
    const curFrom = wrap?.dataset.from;
    const curTo   = wrap?.dataset.to;
    if(drpFrom !== curFrom || to !== curTo){
      const params={view:'day',date_from:drpFrom,date_to:to};
      const ds=document.getElementById('driverPicker');
      if(ds&&ds.value)params.driver_name=ds.value;
      const ps=document.getElementById('platePicker');
      if(ps&&ps.value)params.vehicle_id=ps.value;
      submitFilterForm(params);
    }
  }
  document.addEventListener('click',(e)=>{const pop=document.getElementById('drpPopup');const trg=document.getElementById('drpTrigger');if(!pop||!pop.classList.contains('open'))return;if(pop.contains(e.target)||trg.contains(e.target))return;pop.classList.remove('open');trg.classList.remove('active');});
  function drpInit(){
    const wrap=document.querySelector('.drp-wrap[data-from]');if(!wrap)return;
    const f=wrap.dataset.from;const t=wrap.dataset.to;
    if(f)drpFrom=f;if(t)drpTo=t;drpUpdateLabel();
    const grid=document.getElementById('drpDays');
    if(grid){grid.addEventListener('mousedown',drpStartDrag);document.addEventListener('mousemove',drpMoveDrag);document.addEventListener('mouseup',drpEndDrag);grid.addEventListener('touchstart',drpStartDrag,{passive:false});document.addEventListener('touchmove',drpMoveDrag,{passive:false});document.addEventListener('touchend',drpEndDrag);}
  }

  let oilSearchQuery='';
  function getVisibleRows(){const rows=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));if(!oilSearchQuery)return rows;return rows.filter(r=>r.dataset.driver.includes(oilSearchQuery));}
  function renderOilPage(){
    const all=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));
    const vis=new Set(getVisibleRows());
    all.forEach(r=>{r.style.display=vis.has(r)?'':'none';});
    const c=document.getElementById('oilCount');if(c)c.textContent=vis.size;
  }

  const JOB_API_BASE='http://server_update:8000/api/getDeliveryPersonByDate';
  const jobApiCache={};
  async function fetchJobsByDate(dateStr){
    if(jobApiCache[dateStr]!==undefined)return;
    jobApiCache[dateStr]=null;
    try{
      const res=await fetch(`${JOB_API_BASE}?date=${dateStr}`);if(!res.ok)throw new Error('HTTP '+res.status);
      const json=await res.json();
      const drivers=(json.data||[]).map(b=>({
        driver_name:b.bill_out_by||'ไม่ระบุ',
        jobs:(b.jobs||[]).map(j=>({bill_no:j.bill_no||'',so_id:j.so_id||'',customer_name:j.customer_name||'',bill_in_by:j.bill_in_by||'',status:j.delivery_status||'',note:j.reason||''})),
      }));
      jobApiCache[dateStr]=[{date:json.date||dateStr,drivers}];
    }catch(e){console.warn('fetchJobsByDate:',e);jobApiCache[dateStr]=[];}
  }
  function _collectJobs(driverName,date){
    const jobs=[];
    (jobApiCache[date]||[]).forEach(day=>{
      (day.drivers||[]).forEach(d=>{
        if(d.driver_name!==driverName)return;
        (d.jobs||[]).forEach((j,i)=>{jobs.push({...j,_key:`${driverName}_${day.date}_${i}`,_date:day.date});});
      });
    });
    return jobs;
  }

  let ilIsLoadingDrivers=false,ilLastLoadedDate=null;
  async function ilOnDateChange(){
    if(ilIsLoadingDrivers)return;
    const date=document.getElementById('il-work-date').value;if(!date)return;
    ilLastLoadedDate=date;
    ilIsLoadingDrivers=true;
    const hint=document.getElementById('entryLoadingHint');
    if(hint)hint.style.display='flex';
    document.getElementById('entryRowsBody').innerHTML='';
    document.getElementById('inlineJobTableWrap').innerHTML='<div class="job-loading">กำลังโหลด...</div>';
    try{
      delete SAVED_DRIVERS_CACHE[date];
      delete jobApiCache[date];
      await Promise.all([
        fetchJobsByDate(date),
        fetchSavedDrivers(date),
      ]);
      ilRenderDriverRows(date);
      ilRenderAllJobs(date);
    }catch(e){
      console.warn(e);
      document.getElementById('entryRowsBody').innerHTML='<div class="entry-empty" style="color:var(--red)">โหลดข้อมูลไม่สำเร็จ</div>';
    }finally{
      ilIsLoadingDrivers=false;
      if(hint)hint.style.display='none';
    }
  }

  function _jobStatusKind(j){
    const raw=(j.status||'').trim();
    const noteText=(j.note||'').trim();
    const eff=(noteText==='ส่งสำเร็จ'||noteText==='สำเร็จ')?'ส่งสำเร็จ':raw;
    if(eff.includes('สำเร็จ')&&!eff.includes('ไม่'))return 'ok';
    if(eff.includes('ไม่สำเร็จ')||eff.toLowerCase()==='ng'||eff.toLowerCase()==='fail')return 'fail';
    return 'pending';
  }

  const driverRowState = {};
  const SAVED_DRIVERS_CACHE = {};
  const SESSION_SAVED = {};

  /* ═══════════════════════════════════════════════════════════════
    FIX #1: fetchSavedDrivers — อ่านจากตาราง logs ใน DOM ก่อน (เสถียร 100%)
    แล้วค่อยลอง API เป็น bonus
  ═══════════════════════════════════════════════════════════════ */
  function _readSavedDriversFromDOM(date){
    // อ่านจาก #oilTbody — แถวที่ render โดย Blade ($logs)
    // โครงสร้าง row: <td class="date-pill" title="DD/MM/YYYY">...
    //                <td>...<div class="driver-name">ชื่อ</div>...
    const set = new Set();
    if(!date) return set;
    // แปลง date ค่า YYYY-MM-DD → DD/MM/YYYY (เทียบกับ title ของ date-pill)
    const parts = date.split('-');
    if(parts.length !== 3) return set;
    const target = `${parts[2]}/${parts[1]}/${parts[0]}`; // DD/MM/YYYY
    const rows = document.querySelectorAll('#oilTbody tr[data-driver]');
    rows.forEach(tr => {
      const dateEl = tr.querySelector('.date-pill');
      const nameEl = tr.querySelector('.driver-name');
      if(!dateEl || !nameEl) return;
      const rowDate = (dateEl.getAttribute('title') || '').trim();
      if(rowDate === target){
        const name = (nameEl.textContent || '').trim();
        if(name && name !== '—'){
          set.add(name);
          console.log('[DOM-read]', JSON.stringify(name),
            '| chars:', [...name].map(c=>c.charCodeAt(0)).join(','));
        }
      }
    });
    return set;
  }

  async function fetchSavedDrivers(date){
    if(!date) return new Set();
    if(SAVED_DRIVERS_CACHE[date]) return SAVED_DRIVERS_CACHE[date];

    // 1. อ่านจาก DOM ก่อน (เสถียร — ใช้ข้อมูลที่ Blade render มาแล้ว)
    const fromDOM = _readSavedDriversFromDOM(date);
    console.log('[savedDrivers/DOM]', date, '→', Array.from(fromDOM));

    // 2. ลอง API ถ้ามี — merge เข้ากับ DOM
    try {
      const res = await fetch(`${ROUTE_SAVED_DRIVERS}?date=${encodeURIComponent(date)}`, {
        headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
      });
      if(res.ok){
        const data = await res.json();
        let raw = [];
        if(Array.isArray(data)) raw = data;
        else if(Array.isArray(data.drivers)) raw = data.drivers;
        else if(Array.isArray(data.data)) raw = data.data;
        else if(Array.isArray(data.saved)) raw = data.saved;
        else if(Array.isArray(data.result)) raw = data.result;
        raw.forEach(item => {
          let n = '';
          if(typeof item === 'string') n = item.trim();
          else if(item && typeof item === 'object'){
            n = (item.driver_name || item.name || item.driver || '').toString().trim();
          }
          if(n) fromDOM.add(n);
        });
        console.log('[savedDrivers/merged]', date, '→', Array.from(fromDOM));
      }
    } catch(e){
      console.warn('[savedDrivers] API error, using DOM only', e);
    }

    SAVED_DRIVERS_CACHE[date] = fromDOM;
    return fromDOM;
  }

  /* ═══════════════════════════════════════════════════════════════
    FIX #2: isDriverSaved — เทียบแบบ normalize + ละเว้น zero-width chars
  ═══════════════════════════════════════════════════════════════ */
  function _normalizeName(s){
    if(!s) return '';
    return String(s)
      .replace(/[\u200B-\u200D\uFEFF]/g, '')  // ลบ zero-width chars
      .replace(/\s+/g, ' ')                    // กลั่น whitespace
      .trim()
      .toLowerCase();
  }

  // ════════════════════════════════════════════════════════════
  // Whitelist คนขับ — แสดงเฉพาะชื่อในลิสต์นี้ (ดึงจาก API มาแล้วกรอง)
  // แก้รายชื่อได้ที่นี่
  // ════════════════════════════════════════════════════════════
  const ALLOWED_DRIVERS = @json($allowedDrivers);
  // alias — ชื่อที่ API อาจสะกดต่าง → map ไปชื่อหลัก
  const DRIVER_ALIASES = {
    'กอลฟ':'กอลฟ', 'กอลฟ์':'กอลฟ',           // กอล์ฟ / กอลฟ์
    'แฟงค':'แฟงค', 'แฟรงค':'แฟงค',           // แฟงค์ / แฟรงค์
    'yuth':'yuth', 'ยุทร':'yuth', 'ยุท':'yuth', // yuth / ยุทร
    'joey':'joey', 'โจอี':'joey',
    'แซม':'แซม', 'แชม':'แซม',                  // แซม / แชม
  };
  // normalize เพิ่ม: ตัด ์ (thanthakhat) + ์ ออกเพื่อ match สะกดต่าง
  function _normalizeDriver(s){
    let n = _normalizeName(s).replace(/\u0E4C/g, ''); // ลบ ์
    return DRIVER_ALIASES[n] || n;
  }
  const _allowedSet = new Set(ALLOWED_DRIVERS.map(_normalizeDriver));
  function isAllowedDriver(name){
    return _allowedSet.has(_normalizeDriver(name));
  }

  function isDriverSaved(date, driverName){
    const n = _normalizeName(driverName);
    if(!n || !date) return false;
    const dbSet = SAVED_DRIVERS_CACHE[date];
    if(dbSet){
      for(const saved of dbSet){
        if(_normalizeName(saved) === n) return true;
      }
    }
    const sessSet = SESSION_SAVED[date];
    if(sessSet){
      for(const saved of sessSet){
        if(_normalizeName(saved) === n) return true;
      }
    }
    return false;
  }

  /* ═══════════════════════════════════════════════════════════════
    FIX #3: markDriverSaved — เพิ่ม log + trim
  ═══════════════════════════════════════════════════════════════ */
  function markDriverSaved(date, driverName){
    const n = (driverName||'').trim();
    if(!n || !date) return;
    if(!SESSION_SAVED[date]) SESSION_SAVED[date] = new Set();
    SESSION_SAVED[date].add(n);
    if(!SAVED_DRIVERS_CACHE[date]) SAVED_DRIVERS_CACHE[date] = new Set();
    SAVED_DRIVERS_CACHE[date].add(n);
    console.log('[markSaved]', date, '→', n);
  }

  // ════════════════════════════════════════════════════════════
  // ลงข้อมูลคนขับนอก whitelist อัตโนมัติ
  // เวลา 09:00–18:00 (9 ชม.), ไม่มีค่าน้ำมัน/ระยะ
  // เช็คซ้ำก่อนลง (กันลงซ้ำเมื่อรีเฟรช)
  // ════════════════════════════════════════════════════════════
  // เช็คว่า "ดูเหมือนชื่อคนขับ" หรือไม่ (กรองขยะจาก API)
  function isLikelyDriverName(name){
    const n = (name||'').trim();
    if(!n) return false;
    // ยาวเกินไป = ไม่ใช่ชื่อคน (น่าจะเป็นหมายเหตุ/ชื่อลูกค้า)
    if(n.length > 20) return false;
    // มีคำที่บ่งบอกว่าไม่ใช่คนขับ
    const banned = ['ลูกค้า','เซ็นบิล','เซ็น','บิล','สาขา','จำกัด','บริษัท','หจก','ร้าน','คุณ','ไป','ที่','กับ'];
    for(const w of banned){ if(n.includes(w)) return false; }
    // มีตัวเลขเยอะ (น่าจะเป็นรหัส/เลขบิล)
    const digits = (n.match(/\d/g)||[]).length;
    if(digits >= 4) return false;
    return true;
  }

  const _autoStoreInFlight = new Set();   // กัน double-fire ระหว่างรอ network
  async function ilAutoStoreNonWhitelist(date, driverList){
    if(!driverList || driverList.length===0) return;
    if(!IS_PRIVILEGED) return;   // เฉพาะ user ที่มีสิทธิ์บันทึก
    for(const d of driverList){
      const name = d.name;
      // ⛔ กรองชื่อที่ดูไม่ใช่คนขับ (ขยะจาก API)
      if(!isLikelyDriverName(name)) continue;
      // ⛔ ต้องมี jobs จริง (มีงานส่ง) ถึงจะลง
      if(!d.jobs || d.jobs.length===0) continue;
      // ข้ามถ้าบันทึกแล้ว (มีในตารางหรือ marked)
      if(isDriverSaved(date, name)) continue;
      const fireKey = date + '|' + _normalizeName(name);
      if(_autoStoreInFlight.has(fireKey)) continue;
      _autoStoreInFlight.add(fireKey);

      // นับ ok/fail จาก jobs
      let okC=0, failC=0;
      (d.jobs||[]).forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});

      const fd = new FormData();
      fd.append('_token', CSRF_TOKEN);
      fd.append('work_date', date);
      fd.append('driver_name', name);
      fd.append('vehicle_id', '-');                 // ไม่มีทะเบียน
      fd.append('start_time', date+' 09:00:00');    // เริ่ม 9 โมง
      fd.append('end_time',   date+' 18:00:00');    // กลับ 6 โมง
      fd.append('total_price', 0);                  // ไม่เติมน้ำมัน
      fd.append('ok', okC);
      fd.append('ng', failC);
      if(CURRENT_USER && CURRENT_USER !== 'Guest') fd.append('create_by', CURRENT_USER);

      try{
        const res = await fetch(ROUTE_STORE, {
          method:'POST',
          headers:{'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json'},
          body: fd,
        });
        if(res.ok || res.status===302){
          markDriverSaved(date, name);
          // เพิ่มแถวลงตารางทันที
          ilAppendLogRow({
            date: date,
            driver_name: name,
            vehicle_id: '-',
            start_h: 9, start_m: 0,
            end_h: 18, end_m: 0,
            total_price: 0,
            total_distance: 0,
            liters: 0,
            km_per_liter: 0,
            ok_count: okC,
            fail_count: failC,
          });
          // sync งาน NG ถ้ามี
          if((d.jobs||[]).length>0){
            fetch(ROUTE_SYNC_NG, {
              method:'POST',
              headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
              body: JSON.stringify({
                date,
                create_by: (CURRENT_USER && CURRENT_USER !== 'Guest') ? CURRENT_USER : null,
                jobs: d.jobs.map(j=>({bill_no:j.bill_no, so_id:j.so_id||'', driver_name:name, bill_in_by:j.bill_in_by||'', customer_name:j.customer_name||'', status:(j.status||'').trim(), note:j.note||''}))
              })
            }).catch(()=>{});
          }
        }
      }catch(e){
        console.warn('auto-store error', name, e);
        _autoStoreInFlight.delete(fireKey);   // ให้ลองใหม่ได้
      }
    }
  }

  function ilRenderDriverRows(date){
    const tbody = document.getElementById('entryRowsBody');
    if(!tbody) return;
    const dayBlocks = jobApiCache[date] || [];
    const driversMap = {};
    const autoDriversMap = {};   // คนนอก whitelist → ลงอัตโนมัติ
    dayBlocks.forEach(day=>{
      (day.drivers||[]).forEach(d=>{
        const n = d.driver_name || '';
        if(!n) return;
        if(isAllowedDriver(n)){
          // ✅ คนใน whitelist → ให้กรอกเอง
          if(!driversMap[n]) driversMap[n] = {name:n, jobs:[]};
          (d.jobs||[]).forEach(j=>driversMap[n].jobs.push(j));
        } else {
          // ⏩ คนนอก whitelist → เก็บไว้ลงอัตโนมัติ (09:00-18:00)
          if(!autoDriversMap[n]) autoDriversMap[n] = {name:n, jobs:[]};
          (d.jobs||[]).forEach(j=>autoDriversMap[n].jobs.push(j));
        }
      });
    });
    // ลงข้อมูลคนนอก whitelist อัตโนมัติ (เช็คซ้ำก่อน)
    ilAutoStoreNonWhitelist(date, Object.values(autoDriversMap));
    let driverList = Object.values(driversMap).filter(d => {
      const saved = isDriverSaved(date, d.name);
      console.log('[filter]', JSON.stringify(d.name), '→ saved?', saved,
        '| chars:', [...d.name].map(c=>c.charCodeAt(0)).join(','));
      return !saved;
    });
    const totalCount = Object.keys(driversMap).length;
    const savedCount = totalCount - driverList.length;
    driverList.sort((a,b)=>b.jobs.length - a.jobs.length);

    if(driverList.length === 0){
      if(totalCount === 0){
        tbody.innerHTML = '<div class="entry-empty">ไม่พบคนขับสำหรับวันที่นี้</div>';
        document.getElementById('entrySub').textContent = 'ไม่พบคนขับของวันนี้';
      } else {
        tbody.innerHTML = `<div class="entry-empty" style="color:var(--green-dark)">✓ บันทึกครบทุกคนแล้ว (${savedCount} คน)</div>`;
        document.getElementById('entrySub').textContent = `บันทึกครบ ${savedCount} คน`;
      }
      return;
    }

    let subText = `${driverList.length} คนขับ · กรอกข้อมูลแล้วกดบันทึก`;
    if(savedCount > 0) subText += ` · บันทึกแล้ว ${savedCount} คน`;
    document.getElementById('entrySub').textContent = subText;

    const plateOpts = (window.PLATE_LIST||[]).map(p=>`<option value="${p}">${p}</option>`).join('');

    // วันที่ default จาก il-work-date (สำหรับ prefill datetime input)
    const _wd = document.getElementById('il-work-date')?.value || new Date().toISOString().split('T')[0];
    tbody.innerHTML = driverList.map((d,idx)=>{
      const key = `row_${idx}_${d.name.replace(/[^a-zA-Z0-9ก-๙]/g,'_')}`;
      let okC=0, failC=0;
      d.jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});
      driverRowState[key] = {driverName:d.name, jobs:d.jobs, okCount:okC, failCount:failC, sh:0, sm:0, eh:0, em:0, startDT:'', endDT:''};
      const ini = (d.name||'?').trim().charAt(0).toUpperCase();
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
            <option value="">— เลือกทะเบียน —</option>
            ${plateOpts}
          </select>
        </div>
        <div class="er-time-pair">
          <input type="datetime-local" class="er-dt-input" id="${key}-start-dt" value="${_wd}T09:00" data-key="${key}" data-field="start_dt" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
          <span class="er-time-arrow">→</span>
          <input type="datetime-local" class="er-dt-input" id="${key}-end-dt" value="${_wd}T18:00" data-key="${key}" data-field="end_dt" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
        </div>
        <div>
          <input type="number" class="er-num-input" value="0" placeholder="0 = ไม่เติม" title="ใส่ 0 ถ้าไม่ได้เติมน้ำมัน" data-key="${key}" data-field="price" oninput="erUpdateRow('${key}')" onfocus="erPriceFocus(this,'${key}')" onblur="erPriceBlur(this,'${key}')" step="0.01" min="0">
        </div>
        <div>
          <input type="number" class="er-num-input" placeholder="250" data-key="${key}" data-field="distance" oninput="erUpdateRow('${key}')" onfocus="erFocusRow('${key}')" step="0.01">
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
    // sync ค่า datetime + price เริ่มต้นเข้า state ทันที (เผื่อ user กดบันทึกโดยไม่แตะ)
    Object.keys(driverRowState).forEach(k=>{
      if(document.getElementById(`${k}-start-dt`)) erUpdateDateTime(k);
      if(document.querySelector(`.er-num-input[data-key="${k}"][data-field="price"]`)) erUpdateRow(k);
    });
  }

  function showSaveToast(driverName){
    const existing = document.getElementById('saveToast');
    if(existing) existing.remove();
    const toast = document.createElement('div');
    toast.id = 'saveToast';
    toast.className = 'save-toast';
    toast.innerHTML = `
      <span class="save-toast-icon">✓</span>
      <div class="save-toast-body">
        <div class="save-toast-title">บันทึกสำเร็จ</div>
        <div class="save-toast-msg">${driverName}</div>
      </div>
    `;
    document.body.appendChild(toast);
    setTimeout(()=>{toast.classList.add('hiding');setTimeout(()=>toast.remove(),250);}, 2500);
  }

  let _focusedRowKey = null;
  function erFocusRow(key){
    if(_focusedRowKey === key) return;
    _focusedRowKey = key;
    document.querySelectorAll('.entry-row').forEach(r=>{
      r.classList.toggle('focused', r.dataset.key === key);
    });
    const s = driverRowState[key];
    if(!s) return;
    ilRenderJobsForDriver(s.driverName, s.jobs);
  }

function erUpdateRow(key){
  const s = driverRowState[key]; if(!s) return;
  const priceEl = document.querySelector(`.er-num-input[data-key="${key}"][data-field="price"]`);
  const distEl  = document.querySelector(`.er-num-input[data-key="${key}"][data-field="distance"]`);
  const price = parseFloat(priceEl?.value)||0;
  const dist  = parseFloat(distEl?.value)||0;
  const ppl   = parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
  const liters = (price>0 && ppl>0) ? (price/ppl) : 0;
  const kml    = (dist>0 && liters>0) ? (dist/liters) : 0;
  const thbKm  = (price>0 && dist>0) ? (price/dist) : 0;   // ← เพิ่ม
  s.price = price; s.distance = dist; s.liters = liters; s.kml = kml; s.thbKm = thbKm;
  const sum = document.getElementById(`${key}-summary`);
  if(sum){
    const litersTxt = liters>0 ? fmtN(liters)+' L' : '<span class="empty">—</span>';
    let kmlCls='empty', kmlTxt='—';
    if(kml>0){
      kmlTxt = fmtN(kml)+' km/L';
      kmlCls = kml>=12 ? 'green' : (kml<9 ? 'red' : '');
    }
    const thbKmTxt = thbKm>0 ? '฿'+fmtN(thbKm) : '<span class="empty">—</span>';   // ← เพิ่ม
    sum.innerHTML = `
      <div class="er-summary-row"><span class="er-summary-label">L:</span><span class="er-summary-val ${liters>0?'':'empty'}">${litersTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">km/L:</span><span class="er-summary-val ${kmlCls}">${kmlTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">฿/km:</span><span class="er-summary-val ${thbKm>0?'':'empty'}">${thbKmTxt}</span></div>
    `;
  }
}

  function erUpdateAllRows(){
    Object.keys(driverRowState).forEach(k=>erUpdateRow(k));
  }
  // คลิกเข้าช่องค่าน้ำมัน: ถ้าเป็น 0 → เคลียร์เป็นว่าง พร้อมพิมพ์ใหม่
  function erPriceFocus(el, key){
    erFocusRow(key);
    if(parseFloat(el.value) === 0) el.value = '';
  }
  // ออกจากช่อง: ถ้าปล่อยว่าง → กลับเป็น 0
  function erPriceBlur(el, key){
    if(el.value.trim() === ''){
      el.value = '0';
      erUpdateRow(key);
    }
  }
  // อัปเดตเวลาเริ่ม/สิ้นสุดจาก datetime-local input
  function erUpdateDateTime(key){
    const s = driverRowState[key]; if(!s) return;
    const startEl = document.getElementById(`${key}-start-dt`);
    const endEl   = document.getElementById(`${key}-end-dt`);
    s.startDT = startEl?.value || '';   // "2026-05-22T09:00"
    s.endDT   = endEl?.value   || '';
    // แตกชั่วโมง/นาที ไว้เพื่อคำนวณ ชม. (เผื่อ logic เดิมยังใช้ sh/sm/eh/em)
    if(s.startDT){
      const t = s.startDT.split('T')[1] || '00:00';
      const [h,m] = t.split(':').map(Number);
      s.sh = h||0; s.sm = m||0;
    }
    if(s.endDT){
      const t = s.endDT.split('T')[1] || '00:00';
      const [h,m] = t.split(':').map(Number);
      s.eh = h||0; s.em = m||0;
    }
  }

  let _erClockTargetKey = null;
  let _erClockTargetWhich = null;
  function erOpenClock(key, which){
    _erClockTargetKey = key; _erClockTargetWhich = which;
    const s = driverRowState[key]; if(!s) return;
    clockState.target = '_er';
    clockState.h = which==='start' ? s.sh : s.eh;
    clockState.m = which==='start' ? s.sm : s.em;
    clockState.mode = 'hour';
    document.getElementById('clock-title').textContent = (which==='start'?'เวลาเริ่ม':'เวลาสิ้นสุด') + ' · ' + s.driverName;
    document.getElementById('clock-modal').style.display = 'flex';
    switchTab('hour'); updateDigital();
  }

  // แทรก row ใหม่ลงในตาราง "รายการเติมน้ำมัน" ด้านล่างทันที (ไม่ต้องรีหน้าเว็บ)
  function ilAppendLogRow(r){
    const tbody = document.getElementById('oilTbody');
    if(!tbody) return;

    // ถ้ามี empty-state row อยู่ → ลบทิ้ง
    const emptyTr = tbody.querySelector('tr td .empty-state');
    if(emptyTr) emptyTr.closest('tr').remove();

    const pad = n=>String(n).padStart(2,'0');
    const dateParts = (r.date||'').split('-');
    let dateText = '—', dateFull = '';
    if(dateParts.length === 3){
      dateText = `${dateParts[2]}/${dateParts[1]}`;
      dateFull = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
    }
    const tStart = `${pad(r.start_h)}:${pad(r.start_m)}`;
    const tEnd   = `${pad(r.end_h)}:${pad(r.end_m)}`;
    const timeText = `${tStart}-${tEnd}`;
    // คำนวณระยะเวลา (นาที)
    let durText = '';
    let totalMin = 0;
    if(r.start_dt && r.end_dt){
      // ใช้ datetime เต็ม → รองรับงานข้ามหลายวัน
      totalMin = Math.round((new Date(r.end_dt) - new Date(r.start_dt)) / 60000);
    } else {
      const startMin = r.start_h*60 + r.start_m;
      let endMin   = r.end_h*60   + r.end_m;
      if(endMin < startMin) endMin += 24*60; // ข้ามคืน
      totalMin = endMin - startMin;
    }
    if(totalMin > 0){
      const days = Math.floor(totalMin/1440);   // 1440 = 24*60
      const hh   = Math.floor((totalMin%1440)/60);
      const mm   = totalMin % 60;
      if(days > 0){
        // เกิน 24 ชม. → "X วัน Y ชม."
        durText = `${days} วัน`;
        if(hh > 0) durText += ` ${hh} ชม.`;
        if(mm > 0) durText += ` ${mm} น.`;
      } else if(hh > 0 && mm > 0){
        durText = `${hh} ชม. ${mm} น.`;
      } else if(hh > 0){
        durText = `${hh} ชม.`;
      } else {
        durText = `${mm} น.`;
      }
    }

    const distText = (r.total_distance>0) ? `${Math.round(r.total_distance).toLocaleString()} km` : '—';
    const litersText = (r.liters>0) ? fmtN(r.liters) : '—';
    // price = 0 = ไม่เติมน้ำมัน (วิ่งต่อ) → แสดง "฿0" ไม่ใช่ "—"
    const priceText = (typeof r.total_price === 'number' && r.total_price >= 0)
      ? `฿${Math.round(r.total_price).toLocaleString()}`
      : '—';

    let kmlHtml = '<span style="color:var(--text4)">—</span>';
    if(r.km_per_liter > 0){
      let cls = 'km-mid';
      if(r.km_per_liter >= 13) cls = 'km-good';
      else if(r.km_per_liter < 9) cls = 'km-bad';
      kmlHtml = `<span class="${cls}">${fmtN(r.km_per_liter)}</span>`;
    }

    // ฿/km = ราคา ÷ ระยะ
    let thbKmHtml = '<span style="color:var(--text4)">—</span>';
    if(r.total_price > 0 && r.total_distance > 0){
      const tpk = r.total_price / r.total_distance;
      thbKmHtml = `<span class="thb-km-val">฿${fmtN(tpk)}</span>`;
    }

    // นับ row ปัจจุบัน + 1
    const existingRows = tbody.querySelectorAll('tr[data-driver]').length;
    const rowNo = String(existingRows + 1).padStart(2, '0');

    const tr = document.createElement('tr');
    tr.setAttribute('data-driver', (r.driver_name||'').toLowerCase());
    tr.innerHTML = `
      <td class="row-idx" data-label="#">${rowNo}</td>
      <td data-label="วันที่"><span class="date-pill" title="${dateFull}">${dateText}</span></td>
      <td data-label="คนขับ">
        <div class="driver-cell">
          <div class="driver-name" title="${r.driver_name||''}">${r.driver_name||'—'}</div>
          <div class="driver-plate" title="${r.vehicle_id||''}">${r.vehicle_id||'—'}</div>
        </div>
      </td>
      <td data-label="เวลา"><span class="time-pill">${timeText}</span></td>
      <td class="num" data-label="ชม.">${durText ? `<span class="hour-pill">${durText}</span>` : '<span style="color:var(--text4)">—</span>'}</td>
      <td class="num" data-label="ระยะ">${distText}</td>
      <td class="num" data-label="ลิตร">${litersText}</td>
      <td class="num" data-label="ค่าน้ำมัน">${priceText}</td>
      <td class="num" data-label="KM/L">${kmlHtml}</td>
      <td class="num" data-label="฿/km">${thbKmHtml}</td>
    `;
    // แทรกท้ายตาราง
    tbody.appendChild(tr);

    // อัปเดต count
    const countEl = document.getElementById('oilCount');
    if(countEl) countEl.textContent = tbody.querySelectorAll('tr[data-driver]').length;

    // Highlight ชั่วครู่
    tr.style.transition = 'background .4s';
    tr.style.background = 'rgba(16,185,129,.12)';
    setTimeout(()=>{ tr.style.background = ''; }, 1500);

    // Scroll ให้เห็น row ใหม่
    tr.scrollIntoView({behavior:'smooth', block:'nearest'});

    // ════ Update chart data + re-render ════
    ilUpdateChartsAfterSave(r);
  }

  // Update 3 charts (delivery/KML/cost) after a new row is saved
  function ilUpdateChartsAfterSave(r){
    const name = r.driver_name || 'ไม่ระบุ';
    const plate = r.vehicle_id || 'ไม่ระบุ';

    // 1. Delivery chart — group ตาม "คนขับ"
    if(typeof DLV_BY_DRIVER !== 'undefined'){
      if(!DLV_BY_DRIVER[name]) DLV_BY_DRIVER[name] = {success:0, fail:0, plate};
      if(typeof r.ok_count === 'number')   DLV_BY_DRIVER[name].success += r.ok_count;
      if(typeof r.fail_count === 'number') DLV_BY_DRIVER[name].fail    += r.fail_count;
      if(plate) DLV_BY_DRIVER[name].plate = plate;
      if(typeof renderDlv === 'function') renderDlv();
    }

    // 2. KML chart — group ตาม "ทะเบียน + คนขับ"
    if(typeof KML_BY_DRIVER !== 'undefined' && r.km_per_liter > 0){
      const key = plate + '|' + name;
      if(!KML_BY_DRIVER[key]) KML_BY_DRIVER[key] = {sum:0, count:0, plate, driver:name};
      KML_BY_DRIVER[key].sum   += r.km_per_liter;
      KML_BY_DRIVER[key].count += 1;
      if(typeof renderKmlChart === 'function') renderKmlChart();
    }

    // 3. Cost/km chart — group ตาม "ทะเบียน + คนขับ"
    if(typeof COST_BY_DRIVER !== 'undefined' && r.total_price > 0 && r.total_distance > 0){
      const key = plate + '|' + name;
      if(!COST_BY_DRIVER[key]) COST_BY_DRIVER[key] = {price:0, dist:0, plate, driver:name};
      COST_BY_DRIVER[key].price += r.total_price;
      COST_BY_DRIVER[key].dist  += r.total_distance;
      if(typeof renderCostChart === 'function') renderCostChart();
    }
  }

  async function erSaveRow(key){
    const s = driverRowState[key]; if(!s) return;
    const plateEl = document.querySelector(`.er-plate-select[data-key="${key}"]`);
    const plate = plateEl?.value || '';
    const btn = document.getElementById(`${key}-save`);
    const row = document.querySelector(`.entry-row[data-key="${key}"]`);

    const errors = [];
    if(!plate) errors.push('เลือกทะเบียนรถ');
    // ค่าน้ำมัน: ยอมรับ 0 ได้
    const priceEl = document.querySelector(`.er-num-input[data-key="${key}"][data-field="price"]`);
    const priceRaw = priceEl?.value ?? '';
    if(priceRaw === '' || isNaN(parseFloat(priceRaw))) errors.push('ใส่ค่าน้ำมัน (ใส่ 0 ได้)');
    else if(s.price < 0) errors.push('ค่าน้ำมันติดลบไม่ได้');
    // เวลา: ต้องเลือกวันเวลาเริ่ม + สิ้นสุด
    if(!s.startDT || !s.endDT) errors.push('เลือกวันเวลาเริ่ม-สิ้นสุด');
    else if(new Date(s.endDT) <= new Date(s.startDT)) errors.push('เวลาสิ้นสุดต้องหลังเวลาเริ่ม');
    if(errors.length){
      btn.textContent = '⚠ ' + errors[0];
      setTimeout(()=>{btn.innerHTML='บันทึก';}, 2200);
      return;
    }
    // วันที่ทำงาน = วันที่ของ "เวลาเริ่ม"
    const date = s.startDT.split('T')[0];

    row?.classList.add('saving');
    btn.disabled = true;
    btn.innerHTML = '<span class="ic">⏳</span> กำลังบันทึก...';

    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    fd.append('work_date', date);
    fd.append('driver_name', s.driverName);
    fd.append('vehicle_id', plate);
    // ส่งเวลาเต็มรูปแบบ datetime (รองรับงานข้ามวัน)
    const toBackendDT = v => v ? v.replace('T', ' ') + ':00' : '';  // "2026-05-22T09:00" → "2026-05-22 09:00:00"
    fd.append('start_time', toBackendDT(s.startDT));
    fd.append('end_time',   toBackendDT(s.endDT));
    fd.append('total_price', s.price);
    if(s.distance>0) fd.append('total_distance', s.distance);
    const ppl = parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
    if(ppl>0) fd.append('price_per_liter', ppl);
    if(s.liters>0) fd.append('liters', s.liters.toFixed(2));
    fd.append('ok', s.okCount);
    fd.append('ng', s.failCount);
    if(CURRENT_USER && CURRENT_USER !== 'Guest') fd.append('create_by', CURRENT_USER);

    try{
      const res = await fetch(ROUTE_STORE, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json'},
        body: fd,
      });
      if(!res.ok && res.status !== 302) throw new Error('HTTP '+res.status);
      markDriverSaved(date, s.driverName);
      // ← เพิ่ม row ลงตาราง "รายการเติมน้ำมัน" ด้านล่างทันที (ไม่ต้องรีหน้า)
      ilAppendLogRow({
        date: date,
        driver_name: s.driverName,
        vehicle_id: plate,
        start_h: s.sh, start_m: s.sm,
        end_h: s.eh, end_m: s.em,
        start_dt: s.startDT, end_dt: s.endDT,
        total_price: s.price,
        total_distance: s.distance,
        liters: s.liters,
        km_per_liter: s.kml,
        ok_count: s.okCount,
        fail_count: s.failCount,
      });
      if(s.jobs.length>0){
        fetch(ROUTE_SYNC_NG, {
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
          body: JSON.stringify({
            date,
            create_by: (CURRENT_USER && CURRENT_USER !== 'Guest') ? CURRENT_USER : null,
            jobs: s.jobs.map(j=>({bill_no:j.bill_no, so_id:j.so_id||'', driver_name:s.driverName, bill_in_by:j.bill_in_by||'', customer_name:j.customer_name||'', status:(j.status||'').trim(), note:j.note||''}))
          })
        }).catch(()=>{});
      }
      if(_focusedRowKey === key){
        _focusedRowKey = null;
        ilResetJobsPanel();
      }
      delete driverRowState[key];
      showSaveToast(s.driverName);
      ilRenderDriverRows(date);
      return;
    }catch(e){
      console.warn('save error', e);
      row?.classList.remove('saving');
      btn.disabled = false;
      btn.innerHTML = '⚠ ลองอีกครั้ง';
      setTimeout(()=>{btn.innerHTML='บันทึก';}, 2200);
    }
  }

  function ilRenderJobsForDriver(driverName, jobs){
    const wrap=document.getElementById('inlineJobTableWrap');
    const chipEl=document.getElementById('ilJobDateChip');
    const titleEl=document.getElementById('jobsPanelTitleText');
    if(titleEl) titleEl.textContent = driverName || 'รายการงาน';
    const date = document.getElementById('il-work-date')?.value || '';
    if(chipEl){
      if(date){const p=date.split('-');chipEl.textContent=(p.length===3)?`${p[2]}/${p[1]}/${p[0]}`:date;chipEl.style.display='';}
      else chipEl.style.display='none';
    }
    if(!jobs || jobs.length===0){
      wrap.innerHTML = `<div class="job-loading">ไม่มีงานของ <strong>${driverName}</strong> วันนี้</div>`;
      return;
    }
    let okC=0,failC=0,pendC=0;
    jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;else pendC++;});
    const rowsHtml = jobs.map(j=>{
      const k=_jobStatusKind(j);
      const statusLabel = k==='ok'?'สำเร็จ':k==='fail'?'ไม่สำเร็จ':'รอ';
      const customer = j.customer_name || '—';
      const billIn   = (j.bill_in_by||'').trim();
      const noteText = (j.note||'').trim();
      const billNo   = j.bill_no || '';
      const escAttr = s => String(s).replace(/"/g,'&quot;');
      // Bill no — แสดงเป็น chip ปกติ (ไม่ใช่ลิงก์ ห้ามคลิก)
      const billHtml = `<span class="dgj-bill">${billNo||'—'}</span>`;
      // Sub-line — bill_in_by เห็นเฉพาะ privileged
      const lineParts = [];
      if(IS_PRIVILEGED && billIn) lineParts.push(`<span class="dgj-meta-item"><span class="dgj-meta-label">ผู้รับ:</span> ${billIn}</span>`);
      const showNote = noteText && noteText !== 'ส่งสำเร็จ' && noteText !== 'สำเร็จ';
      if(showNote) lineParts.push(`<span class="dgj-meta-item dgj-note"><span class="dgj-meta-label">หมายเหตุ:</span> ${noteText}</span>`);
      const subLine = lineParts.length ? `<div class="dgj-meta">${lineParts.join('<span class="dgj-meta-sep">·</span>')}</div>` : '';
      return `<div class="dgj-row">
        <div class="dgj-main">
          <div class="dgj-top">
            ${billHtml}
            <span class="dgj-customer" title="${escAttr(customer)}">${customer}</span>
            <span class="dgj-status ${k}">${statusLabel}</span>
          </div>
          ${subLine}
        </div>
      </div>`;
    }).join('');
    const summary = `<div class="jobs-summary-bar">
      <span class="jsb-chip">รวม <strong>${jobs.length}</strong></span>
      ${okC>0?`<span class="jsb-chip ok">สำเร็จ <strong>${okC}</strong></span>`:''}
      ${failC>0?`<span class="jsb-chip fail">ไม่สำเร็จ <strong>${failC}</strong></span>`:''}
      ${pendC>0?`<span class="jsb-chip">รอ <strong>${pendC}</strong></span>`:''}
    </div>`;
    wrap.innerHTML = summary + `<div style="padding:10px">${rowsHtml}</div>`;
  }

  function ilResetJobsPanel(){
    const wrap=document.getElementById('inlineJobTableWrap');
    const chipEl=document.getElementById('ilJobDateChip');
    const titleEl=document.getElementById('jobsPanelTitleText');
    if(titleEl) titleEl.textContent = 'รายการงาน';
    if(chipEl) chipEl.style.display='none';
    if(wrap) wrap.innerHTML = '<div class="job-loading">คลิกที่แถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>';
  }

  function ilRenderAllJobs(date){
    ilResetJobsPanel();
  }

  function ilCalcPreview(){
    if(typeof erUpdateAllRows==='function') erUpdateAllRows();
  }

  let ilCurrentOilType='diesel';
  function ilSwitchOilType(t){
    ilCurrentOilType=t;
    document.querySelectorAll('.aoil-type-btn, .entry-oil-tab').forEach(b=>b.classList.remove('active'));
    document.getElementById('ilBtnOil-'+t)?.classList.add('active');
    ilLoadOilPrice(t);
  }
  async function ilRefreshOilPrice(){
    const btn=document.getElementById('ilBtnRefresh');
    if(btn){btn.disabled=true;btn.style.opacity='.5';}
    await ilLoadOilPrice(ilCurrentOilType);
    if(btn){btn.disabled=false;btn.style.opacity='1';}
  }
  async function ilLoadOilPrice(type){
    const config={
      'diesel':{label:'ดีเซล',matchName:'ดีเซล',maxPrice:55},
      '95':{label:'แก๊สโซฮอล์ 95',matchName:'แก๊สโซฮอล์ 95',maxPrice:50},
      'benzin95':{label:'เบนซิน 95',matchName:'เบนซิน 95',maxPrice:60},
      '91':{label:'แก๊สโซฮอล์ 91',matchName:'แก๊สโซฮอล์ 91',maxPrice:50},
      'e20':{label:'แก๊สโซฮอล์ E20',matchName:'E20',maxPrice:45},
      'e85':{label:'แก๊สโซฮอล์ E85',matchName:'E85',maxPrice:40}
    };
    const cfg=config[type]??config['diesel'];
    const labelEl=document.getElementById('ilOilPriceLabel');
    if(labelEl)labelEl.textContent=`ราคา${cfg.label}`;
    const showEl=document.getElementById('ilOilPriceShow');
    if(showEl)showEl.textContent='...';
    const statusEl=document.getElementById('ilOilPriceStatus');
    if(statusEl)statusEl.textContent='กำลังดึง';
    const wrapEl=document.getElementById('ilLiveWrap');
    if(wrapEl)wrapEl.classList.add('loading');
    const liveLabel=document.getElementById('ilLiveLabel');
    if(liveLabel)liveLabel.textContent='กำลังดึง';
    const pplEl=document.getElementById('il-price-per-liter');
    if(pplEl)pplEl.value='';
    let fetched=null;
    try{
      const r=await Promise.race([fetch('https://api.chnwt.dev/thai-oil-api/latest'),new Promise((_,rj)=>setTimeout(()=>rj(new Error('t')),8000))]);
      if(r.ok){const json=await r.json();const stations=json?.response?.stations;
        if(stations){const prices=[];for(const station of Object.values(stations))for(const fuel of Object.values(station))if(fuel?.name?.includes(cfg.matchName)&&fuel?.price){const n=parseFloat(fuel.price);if(!isNaN(n)&&n>0&&n<cfg.maxPrice)prices.push(n);}
          if(prices.length>0){const freq={};for(const p of prices){const k=p.toFixed(2);freq[k]=(freq[k]||0)+1;}fetched=parseFloat(Object.entries(freq).sort((a,b)=>b[1]-a[1])[0][0]);}
        }
      }
    }catch(_){}
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
    ilCalcPreview();
    if(typeof erUpdateAllRows==='function') erUpdateAllRows();
  }

  const CLOCK_COLOR='#3b82f6';
  let clockState={target:'start',mode:'hour',h:0,m:0};
  function closeClock(){document.getElementById('clock-modal').style.display='none';}
  function switchTab(mode){clockState.mode=mode;document.getElementById('tab-hour').classList.toggle('active',mode==='hour');document.getElementById('tab-min').classList.toggle('active',mode==='min');drawClock();}
  function drawClock(){
    const svg=document.getElementById('clock-face');const cx=120,cy=120;const isHour=clockState.mode==='hour';
    let html=`<circle cx="${cx}" cy="${cy}" r="115" fill="#f5f5f7"/>`;
    if(isHour){
      const rO=100,rI=65;
      for(let i=1;i<=12;i++){
        const ang=(i*30-90)*Math.PI/180;const oV=(i+12)%24;
        const ox=cx+rO*Math.cos(ang),oy=cy+rO*Math.sin(ang);const oS=clockState.h===oV;
        html+=`<circle cx="${ox}" cy="${oy}" r="15" fill="${oS?CLOCK_COLOR:'transparent'}" data-val="${oV}" style="cursor:pointer"/>`;
        html+=`<text x="${ox}" y="${oy+4}" text-anchor="middle" font-size="12" font-weight="600" fill="${oS?'#fff':'#424245'}" style="pointer-events:none">${String(oV).padStart(2,'0')}</text>`;
        const ix=cx+rI*Math.cos(ang),iy=cy+rI*Math.sin(ang);const iS=clockState.h===i;
        html+=`<circle cx="${ix}" cy="${iy}" r="14" fill="${iS?CLOCK_COLOR:'transparent'}" data-val="${i}" style="cursor:pointer"/>`;
        html+=`<text x="${ix}" y="${iy+4}" text-anchor="middle" font-size="13" font-weight="600" fill="${iS?'#fff':'#1d1d1f'}" style="pointer-events:none">${i}</text>`;
      }
      const useO=clockState.h>=13||clockState.h===0;const handR=useO?rO-12:rI-10;const handH=clockState.h===0?12:(clockState.h>12?clockState.h-12:clockState.h);
      const hAng=(handH*30-90)*Math.PI/180;
      html+=`<line x1="${cx}" y1="${cy}" x2="${cx+handR*Math.cos(hAng)}" y2="${cy+handR*Math.sin(hAng)}" stroke="${CLOCK_COLOR}" stroke-width="2"/>`;
    }else{
      const rMa=100,rMi=82;
      for(let i=0;i<60;i++){if(i%5===0)continue;const ang=(i*6-90)*Math.PI/180;const x=cx+rMi*Math.cos(ang),y=cy+rMi*Math.sin(ang);const iS=clockState.m===i;if(iS){html+=`<circle cx="${x}" cy="${y}" r="11" fill="${CLOCK_COLOR}" data-val="${i}" style="cursor:pointer"/><text x="${x}" y="${y+3}" text-anchor="middle" font-size="10" font-weight="600" fill="#fff" style="pointer-events:none">${String(i).padStart(2,'0')}</text>`;}else{html+=`<circle cx="${x}" cy="${y}" r="9" fill="transparent" data-val="${i}" style="cursor:pointer"/><circle cx="${x}" cy="${y}" r="2.5" fill="#a1a1aa" style="pointer-events:none"/>`;}}
      for(let i=0;i<60;i+=5){const ang=(i*6-90)*Math.PI/180;const x=cx+rMa*Math.cos(ang),y=cy+rMa*Math.sin(ang);const iS=clockState.m===i;html+=`<circle cx="${x}" cy="${y}" r="14" fill="${iS?CLOCK_COLOR:'transparent'}" data-val="${i}" style="cursor:pointer"/><text x="${x}" y="${y+4}" text-anchor="middle" font-size="13" font-weight="700" fill="${iS?'#fff':'#1d1d1f'}" style="pointer-events:none">${String(i).padStart(2,'0')}</text>`;}
      const iMS=clockState.m%5===0;const handR=(iMS?rMa:rMi)-14;const ang=(clockState.m*6-90)*Math.PI/180;
      html+=`<line x1="${cx}" y1="${cy}" x2="${cx+handR*Math.cos(ang)}" y2="${cy+handR*Math.sin(ang)}" stroke="${CLOCK_COLOR}" stroke-width="2"/>`;
    }
    html+=`<circle cx="${cx}" cy="${cy}" r="4" fill="${CLOCK_COLOR}"/>`;
    svg.innerHTML=html;
    svg.querySelectorAll('circle[data-val]').forEach(c=>{c.onclick=()=>{const v=+c.getAttribute('data-val');if(clockState.mode==='hour')clockState.h=v;else clockState.m=v;updateDigital();drawClock();if(clockState.mode==='hour')setTimeout(()=>switchTab('min'),200);};});
  }
  function updateDigital(){document.getElementById('clock-h-val').textContent=String(clockState.h).padStart(2,'0');document.getElementById('clock-m-val').textContent=String(clockState.m).padStart(2,'0');}
  function confirmClock(){
    const t=clockState.target;
    const h=clockState.h,m=clockState.m;
    if(t==='_er' && _erClockTargetKey){
      const s = driverRowState[_erClockTargetKey];
      if(s){
        if(_erClockTargetWhich==='start'){ s.sh=h; s.sm=m; }
        else { s.eh=h; s.em=m; }
        const el = document.getElementById(`${_erClockTargetKey}-${_erClockTargetWhich}-display`);
        if(el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
      }
      closeClock();
      _erClockTargetKey = null; _erClockTargetWhich = null;
      return;
    }
    closeClock();
  }

  const PDF_LOGS = @json($logs);
  const PDF_ALL_LOGS = @json($allLogs);  // ทุก record — สำหรับเลือกช่วงเอง
  const PDF_VIEW = @json($view);
  const PDF_FILTER_DAY   = @json($filterDay ?? '');
  const PDF_FILTER_MONTH = @json($filterMonth ?? '');

  // ════════════════════════════════════════════════════════════
  // Modal เลือกช่วงเวลาก่อน Export PDF
  // ════════════════════════════════════════════════════════════
  function openPdfRangeModal(){
    let modal = document.getElementById('pdfRangeModal');
    if(modal){ modal.classList.add('open'); return; }
    modal = document.createElement('div');
    modal.id = 'pdfRangeModal';
    modal.className = 'pdf-modal-overlay open';
    const thisYear = new Date().getFullYear();
    const thisMonth = String(new Date().getMonth()+1).padStart(2,'0');
    const today = new Date().toISOString().split('T')[0];
    let yearOpts = '';
    for(let y=thisYear; y>=thisYear-4; y--) yearOpts += `<option value="${y}">${y+543}</option>`;
    modal.innerHTML = `
      <div class="pdf-modal">
        <div class="pdf-modal-head">
          <span>เลือกช่วงเวลา Export PDF</span>
          <button type="button" class="pdf-modal-x" onclick="closePdfRangeModal()">✕</button>
        </div>
        <div class="pdf-modal-body">
          <div class="pdf-mode-tabs">
            <button type="button" class="pdf-mode-btn active" data-mode="day"   onclick="setPdfMode('day')">รายวัน</button>
            <button type="button" class="pdf-mode-btn" data-mode="month" onclick="setPdfMode('month')">รายเดือน</button>
            <button type="button" class="pdf-mode-btn" data-mode="year"  onclick="setPdfMode('year')">รายปี</button>
            <button type="button" class="pdf-mode-btn" data-mode="all"   onclick="setPdfMode('all')">ทั้งหมด</button>
          </div>
          <div class="pdf-field" id="pdfFieldDay">
            <label>เลือกวันที่</label>
            <input type="date" id="pdfDay" value="${today}">
          </div>
          <div class="pdf-field" id="pdfFieldMonth" style="display:none">
            <label>เลือกเดือน</label>
            <input type="month" id="pdfMonth" value="${thisYear}-${thisMonth}">
          </div>
          <div class="pdf-field" id="pdfFieldYear" style="display:none">
            <label>เลือกปี</label>
            <select id="pdfYear">${yearOpts}</select>
          </div>
          <div class="pdf-field" id="pdfFieldAll" style="display:none">
            <p style="color:var(--text3);font-size:14px;margin:0">Export ข้อมูลทั้งหมดที่มี</p>
          </div>
        </div>
        <div class="pdf-modal-foot">
          <button type="button" class="pdf-btn-cancel" onclick="closePdfRangeModal()">ยกเลิก</button>
          <button type="button" class="pdf-btn-go" onclick="confirmPdfExport()">📄 สร้าง PDF</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', e=>{ if(e.target===modal) closePdfRangeModal(); });
  }
  function closePdfRangeModal(){ document.getElementById('pdfRangeModal')?.classList.remove('open'); }
  let PDF_MODE = 'day';
  function setPdfMode(m){
    PDF_MODE = m;
    document.querySelectorAll('.pdf-mode-btn').forEach(b=>b.classList.toggle('active', b.dataset.mode===m));
    document.getElementById('pdfFieldDay').style.display   = m==='day'   ? '' : 'none';
    document.getElementById('pdfFieldMonth').style.display = m==='month' ? '' : 'none';
    document.getElementById('pdfFieldYear').style.display  = m==='year'  ? '' : 'none';
    document.getElementById('pdfFieldAll').style.display   = m==='all'   ? '' : 'none';
  }
  function confirmPdfExport(){
    // กรอง PDF_ALL_LOGS ตามช่วงที่เลือก
    let logs = PDF_ALL_LOGS || [];
    let rangeLabel = '';
    if(PDF_MODE==='day'){
      const d = document.getElementById('pdfDay').value;
      logs = logs.filter(r => (r.work_date||'').slice(0,10) === d);
      rangeLabel = 'วันที่ ' + new Date(d).toLocaleDateString('th-TH',{year:'numeric',month:'long',day:'numeric'});
    } else if(PDF_MODE==='month'){
      const m = document.getElementById('pdfMonth').value;  // YYYY-MM
      logs = logs.filter(r => (r.work_date||'').slice(0,7) === m);
      const [y,mo] = m.split('-');
      rangeLabel = 'เดือน ' + new Date(y,mo-1).toLocaleDateString('th-TH',{year:'numeric',month:'long'});
    } else if(PDF_MODE==='year'){
      const y = document.getElementById('pdfYear').value;
      logs = logs.filter(r => (r.work_date||'').slice(0,4) === String(y));
      rangeLabel = 'ปี ' + (parseInt(y)+543);
    } else {
      rangeLabel = 'ทั้งหมด';
    }
    closePdfRangeModal();
    if(logs.length===0){
      alert('ไม่มีข้อมูลในช่วงที่เลือก');
      return;
    }
    exportPDF(logs, rangeLabel);
  }

  // สร้างกราฟสำหรับ PDF จาก logs ของช่วงที่เลือก (offscreen canvas)
  async function buildPdfCharts(logs){
    const results = [];
    // เตรียมข้อมูล 3 กราฟ
    // 1. km/L by plate|driver
    const kmlMap = {};
    logs.forEach(r=>{
      const kml = +(r.km_per_liter||0);
      if(kml<=0) return;
      const key = (r.vehicle_id||'')+'|'+(r.driver_name||'');
      if(!kmlMap[key]) kmlMap[key]={sum:0,n:0,label:(r.vehicle_id||'')+' '+(r.driver_name||'')};
      kmlMap[key].sum+=kml; kmlMap[key].n++;
    });
    // 2. cost ฿/km
    const costMap = {};
    logs.forEach(r=>{
      const price=+(r.total_price||0), dist=+(r.total_distance||0);
      const key=(r.vehicle_id||'')+'|'+(r.driver_name||'');
      if(!costMap[key]) costMap[key]={price:0,dist:0,label:(r.vehicle_id||'')+' '+(r.driver_name||'')};
      costMap[key].price+=price; costMap[key].dist+=dist;
    });

    const mkChart = async (title, labels, data, color, fmt)=>{
      if(labels.length===0) return;
      const cv = document.createElement('canvas');
      cv.width = 900; cv.height = Math.max(200, labels.length*40+80);
      cv.style.cssText='position:fixed;left:-99999px;top:0';
      document.body.appendChild(cv);
      const chart = new Chart(cv, {
        type:'bar',
        data:{labels, datasets:[{data, backgroundColor:color, borderRadius:5}]},
        options:{
          indexAxis:'y', responsive:false, animation:false,
          layout:{padding:{right:70}},   // เผื่อที่ให้ค่าที่ปลายแท่ง
          plugins:{legend:{display:false}, datalabels:{anchor:'end',align:'end',clamp:true,color:'#111',font:{size:13,weight:'600',family:'IBM Plex Sans Thai'},formatter:fmt}},
          scales:{
            x:{ticks:{font:{size:12,family:'IBM Plex Sans Thai'}}, grace:'10%'},
            y:{ticks:{font:{size:13,weight:'600',family:'IBM Plex Sans Thai'}}}
          }
        },
        plugins:[ChartDataLabels]
      });
      await new Promise(r=>setTimeout(r,300));  // รอ render
      let dataUrl='';
      try{ dataUrl = cv.toDataURL('image/png',1.0); }catch(e){}
      chart.destroy(); cv.remove();
      if(dataUrl && dataUrl.length>2000) results.push({title, dataUrl});
    };

    // km/L
    const kmlArr=Object.values(kmlMap).map(v=>({label:v.label,val:v.n>0?v.sum/v.n:0})).filter(v=>v.val>0).sort((a,b)=>b.val-a.val);
    await mkChart('น้ำมันต่อกิโล (km/L)', kmlArr.map(v=>v.label), kmlArr.map(v=>+v.val.toFixed(2)), '#10b981', v=>fmtN(v)+' km/L');
    // cost
    const costArr=Object.values(costMap).map(v=>({label:v.label,val:v.dist>0?v.price/v.dist:0})).filter(v=>v.val>0).sort((a,b)=>a.val-b.val);
    await mkChart('ต้นทุนต่อกิโล (฿/km)', costArr.map(v=>v.label), costArr.map(v=>+v.val.toFixed(2)), '#f59e0b', v=>'฿'+fmtN(v));

    return results;
  }

  async function exportPDF(customLogs, rangeLabel){
    const btn = document.querySelector('.entry-export-btn');
    const origHTML = btn?.innerHTML;
    if(btn){btn.disabled = true; btn.innerHTML = '<span>กำลังสร้าง PDF...</span>';}

    // Build a hidden offscreen report DOM in Thai using the page's own fonts,
    // snapshot it with html2canvas, then put each page-sized slice into jsPDF.
    // This avoids jsPDF's lack of built-in Thai font support.
    const reportEl = document.createElement('div');
    reportEl.style.cssText = `
      position:fixed; left:-99999px; top:0;
      width:794px; padding:32px;
      background:#fff; color:#18181b;
      font-family:'IBM Plex Sans Thai','Inter',sans-serif;
      font-size: 14px; line-height:1.5;
      box-sizing:border-box;
    `;

    try{
      const { jsPDF } = window.jspdf;
      const logs = customLogs || PDF_LOGS || [];
      const today = new Date();
      const dateNow = today.toLocaleDateString('th-TH',{year:'numeric',month:'2-digit',day:'2-digit'});
      const timeNow = today.toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'});

      // Summary stats
      let totalPrice=0, totalLiters=0, totalKm=0, kmlSum=0, kmlCount=0;
      const drivers = new Set();
      logs.forEach(r=>{
        totalPrice += +(r.total_price||0);
        totalLiters += +(r.liters||0);
        totalKm += +(r.total_distance||0);
        if((r.km_per_liter||0)>0){kmlSum+=+r.km_per_liter;kmlCount++;}
        if(r.driver_name) drivers.add(r.driver_name);
      });
      const avgKml = kmlCount>0 ? kmlSum/kmlCount : 0;
      const avgCostPerKm = totalKm>0 ? totalPrice/totalKm : 0;

      let periodTxt = '';
      if(rangeLabel){
        periodTxt = rangeLabel;
      } else if(PDF_VIEW === 'day') periodTxt = `วันที่: ${PDF_FILTER_DAY||dateNow}`;
      else if(PDF_VIEW === 'month') periodTxt = `เดือน: ${PDF_FILTER_MONTH}`;
      else if(PDF_VIEW === 'year') periodTxt = 'รายปี';
      else periodTxt = 'ทั้งหมด';

      // Per-driver summary
      const byDriver = {};
      logs.forEach(r=>{
        const n = r.driver_name || '—';
        if(!byDriver[n]) byDriver[n] = {name:n, plate:r.vehicle_id||'', rounds:0, distance:0, liters:0, price:0, kmlSum:0, kmlCount:0};
        byDriver[n].rounds++;
        byDriver[n].distance += +(r.total_distance||0);
        byDriver[n].liters += +(r.liters||0);
        byDriver[n].price += +(r.total_price||0);
        if(!byDriver[n].plate && r.vehicle_id) byDriver[n].plate = r.vehicle_id;
        if((r.km_per_liter||0)>0){byDriver[n].kmlSum += +r.km_per_liter;byDriver[n].kmlCount++;}
      });
      const sortedDrivers = Object.values(byDriver).sort((a,b)=>b.price-a.price);

      const fmtNum = (n, d=0)=> Number(n||0).toLocaleString('en-US',{minimumFractionDigits:d, maximumFractionDigits:d});
      const fmtMoney = n => '฿'+fmtNum(n,0);

      const thS = 'padding:8px 6px;background:#10b981;color:#fff;font-weight:700;font-size: 14px;text-align:left;border:1px solid #059669';
      const thSB = 'padding:8px 6px;background:#3b82f6;color:#fff;font-weight:700;font-size: 14px;text-align:left;border:1px solid #2563eb';
      const tdS = 'padding:6px;border:1px solid #e5e7eb;font-size: 14px;background:#fff';

      // แบ่ง logs เป็น chunk ละ 20 แถว — แต่ละ chunk มีหัวตาราง + ขึ้นหน้าใหม่
      const ROWS_PER_PAGE = 20;
      const logTableHead = `
        <thead><tr>
          <th style="${thS};width:30px;text-align:center">#</th>
          <th style="${thS};width:50px;text-align:center">วันที่</th>
          <th style="${thS}">คนขับ</th>
          <th style="${thS}">ทะเบียน</th>
          <th style="${thS};text-align:center">เวลา</th>
          <th style="${thS};text-align:right">ระยะ (km)</th>
          <th style="${thS};text-align:right">ลิตร</th>
          <th style="${thS};text-align:right">บาท</th>
          <th style="${thS};text-align:right">km/L</th>
        </tr></thead>`;

      const buildLogRow = (r, i) => {
        const date = r.work_date || '';
        const dateOut = date ? date.split('-').reverse().slice(0,2).join('/') : '-';
        const t1 = (r.start_time||'').slice(0,5);
        const t2 = (r.end_time||'').slice(0,5);
        const timeOut = (t1 && t2) ? `${t1}-${t2}` : '-';
        return `
          <tr>
            <td style="${tdS};text-align:center;color:#71717a">${i+1}</td>
            <td style="${tdS};text-align:center">${dateOut}</td>
            <td style="${tdS}"><strong>${r.driver_name||'-'}</strong></td>
            <td style="${tdS}">${r.vehicle_id||'-'}</td>
            <td style="${tdS};text-align:center;font-family:ui-monospace,monospace;font-size:14px">${timeOut}</td>
            <td style="${tdS};text-align:right;font-family:ui-monospace,monospace">${fmtNum(r.total_distance,0)}</td>
            <td style="${tdS};text-align:right;font-family:ui-monospace,monospace">${fmtNum(r.liters,1)}</td>
            <td style="${tdS};text-align:right;font-family:ui-monospace,monospace">${fmtNum(r.total_price,0)}</td>
            <td style="${tdS};text-align:right;font-family:ui-monospace,monospace">${(r.km_per_liter>0)?fmtNum(r.km_per_liter,2):'-'}</td>
          </tr>`;
      };

      let logTablesHtml = '';
      if(logs.length === 0){
        logTablesHtml = `<table style="width:100%;border-collapse:collapse;margin-bottom:18px">${logTableHead}<tbody><tr><td colspan="9" style="${tdS};text-align:center;padding:20px;color:#a1a1aa">ไม่มีข้อมูล</td></tr></tbody></table>`;
      } else {
        for(let start=0; start<logs.length; start+=ROWS_PER_PAGE){
          const chunk = logs.slice(start, start+ROWS_PER_PAGE);
          const rowsHtml = chunk.map((r,idx)=>buildLogRow(r, start+idx)).join('');
          logTablesHtml += `
            <div class="pdf-log-chunk">
              <table style="width:100%;border-collapse:collapse;margin-bottom:18px">
                ${logTableHead}
                <tbody>${rowsHtml}</tbody>
              </table>
            </div>`;
        }
      }

      let driverRowsHtml = '';
      sortedDrivers.forEach((d,i)=>{
        const kml = d.kmlCount>0 ? d.kmlSum/d.kmlCount : 0;
        const costKm = d.distance>0 ? d.price/d.distance : 0;
        driverRowsHtml += `
          <tr>
            <td style="text-align:center;font-weight:600">${i+1}</td>
            <td><strong>${d.name}</strong></td>
            <td style="color:#71717a">${d.plate||'-'}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${d.rounds}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${fmtNum(d.distance,0)}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${fmtNum(d.liters,1)}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${fmtNum(d.price,0)}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${kml>0?fmtNum(kml,2):'-'}</td>
            <td style="text-align:right;font-family:ui-monospace,monospace">${costKm>0?fmtNum(costKm,2):'-'}</td>
          </tr>`;
      });

      reportEl.innerHTML = `
        <div class="pdf-head-section">
          <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;padding:20px 24px;border-radius:14px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:22px;font-weight:700;letter-spacing:-0.02em">รายงานการเติมน้ำมัน</div>
              <div style="font-size: 14px;opacity:.85;margin-top:4px">สร้างเมื่อ: ${dateNow} ${timeNow}</div>
            </div>
            <div style="font-size: 14px;opacity:.9;text-align:right">${periodTxt}</div>
          </div>

          <div style="font-size:15px;font-weight:700;margin-bottom:10px;color:#18181b">สรุปภาพรวม</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:20px">
            ${[
              ['รายการทั้งหมด', logs.length+' ครั้ง'],
              ['คนขับ', drivers.size+' คน'],
              ['ค่าน้ำมันรวม', fmtMoney(totalPrice)],
              ['ลิตรรวม', fmtNum(totalLiters,1)+' L'],
              ['ระยะทางรวม', fmtNum(totalKm,0)+' km'],
              ['เฉลี่ย km/L', fmtNum(avgKml,2)],
              ['ต้นทุนเฉลี่ย', fmtMoney(avgCostPerKm)+'/km'],
              ['ช่วงข้อมูล', periodTxt.replace(/^[^:]+:\s*/,'')||'-'],
            ].map(([l,v])=>`
              <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px">
                <div style="font-size: 14px;color:#71717a;margin-bottom:3px">${l}</div>
                <div style="font-size:14px;font-weight:700;color:#18181b">${v}</div>
              </div>`).join('')}
          </div>
          <div style="font-size:15px;font-weight:700;margin:14px 0 8px;color:#18181b">รายการเติมน้ำมัน</div>
        </div>

        ${logTablesHtml}

        <div class="pdf-summary-section">
          <div style="font-size:15px;font-weight:700;margin:14px 0 8px;color:#18181b">สรุปแยกตามคนขับ</div>
          <table style="width:100%;border-collapse:collapse;margin-bottom:18px">
            <thead><tr>
              <th style="${thSB};width:40px;text-align:center">อันดับ</th>
              <th style="${thSB}">คนขับ</th>
              <th style="${thSB}">ทะเบียน</th>
              <th style="${thSB};text-align:right">รอบ</th>
              <th style="${thSB};text-align:right">ระยะ (km)</th>
              <th style="${thSB};text-align:right">ลิตร</th>
              <th style="${thSB};text-align:right">บาท</th>
              <th style="${thSB};text-align:right">เฉลี่ย km/L</th>
              <th style="${thSB};text-align:right">฿/km</th>
            </tr></thead>
            <tbody>${driverRowsHtml.replace(/<td style="/g,'<td style="'+tdS+';')}</tbody>
          </table>
        </div>
      `;

      // Inject td styles into log rows (they were created without inline padding/borders for brevity)
      reportEl.querySelectorAll('tbody td').forEach(td=>{
        const cur = td.getAttribute('style') || '';
        if(!cur.includes('border:1px solid')){
          td.setAttribute('style', tdS + ';' + cur);
        }
      });

      document.body.appendChild(reportEl);
      await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));

      // Build PDF
      const doc = new jsPDF({orientation:'portrait', unit:'mm', format:'a4'});
      const pageW = doc.internal.pageSize.getWidth();
      const pageH = doc.internal.pageSize.getHeight();
      const margin = 8;
      const imgW = pageW - margin*2;
      let pageAdded = false;

      // helper: snapshot 1 element → ใส่ลง PDF (slice ถ้าสูงเกิน 1 หน้า)
      const addElementToPdf = async (el)=>{
        const cv = await html2canvas(el, {scale:2, backgroundColor:'#ffffff', logging:false, useCORS:true});
        const fullH = (cv.height * imgW) / cv.width;
        const contentH = pageH - margin*2;
        if(fullH <= contentH){
          if(pageAdded) doc.addPage();
          doc.addImage(cv.toDataURL('image/png'), 'PNG', margin, margin, imgW, fullH);
          pageAdded = true;
        } else {
          // สูงเกิน → slice
          const sliceCanvasH = (contentH * cv.width) / imgW;
          let yOff = 0;
          while(yOff < cv.height){
            const sliceH = Math.min(sliceCanvasH, cv.height - yOff);
            const sc = document.createElement('canvas');
            sc.width = cv.width; sc.height = sliceH;
            const cx = sc.getContext('2d');
            cx.fillStyle='#ffffff'; cx.fillRect(0,0,sc.width,sc.height);
            cx.drawImage(cv, 0, yOff, cv.width, sliceH, 0, 0, cv.width, sliceH);
            if(pageAdded) doc.addPage();
            doc.addImage(sc.toDataURL('image/png'), 'PNG', margin, margin, imgW, (sliceH*imgW)/cv.width);
            pageAdded = true;
            yOff += sliceH;
          }
        }
      };

      // 1) หน้าแรก: header + สรุปภาพรวม
      const headEl = reportEl.querySelector('.pdf-head-section');
      if(headEl) await addElementToPdf(headEl);

      // 2) ตาราง log — แต่ละ chunk (20 แถว) = 1 หน้า มีหัวตารางทุกหน้า
      const chunks = reportEl.querySelectorAll('.pdf-log-chunk');
      for(const chunkEl of chunks){
        await addElementToPdf(chunkEl);
      }

      // 3) สรุปแยกตามคนขับ
      const summaryEl = reportEl.querySelector('.pdf-summary-section');
      if(summaryEl) await addElementToPdf(summaryEl);

      const chartReportEl = document.createElement('div');
      chartReportEl.style.cssText = reportEl.style.cssText;
      let chartHtml = `
        <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;padding:20px 24px;border-radius:14px;margin-bottom:20px">
          <div style="font-size:22px;font-weight:700">กราฟวิเคราะห์</div>
          <div style="font-size: 14px;opacity:.85;margin-top:4px">${dateNow} ${timeNow}</div>
        </div>`;

      // สร้างกราฟใหม่จาก logs ของช่วงที่เลือก (ไม่ใช้ canvas บนหน้าจอ
      // เพราะอาจซ่อน/ว่าง/เป็นข้อมูลคนละช่วง)
      const pdfCharts = await buildPdfCharts(logs);

      let hasChart = false;
      for(const ch of pdfCharts){
        if(!ch.dataUrl || ch.dataUrl.length < 2000) continue;
        chartHtml += `
          <div style="margin-bottom:18px;page-break-inside:avoid">
            <div style="font-size:14px;font-weight:700;margin-bottom:8px;color:#18181b">${ch.title}</div>
            <img src="${ch.dataUrl}" style="width:100%;border:1px solid #e5e7eb;border-radius:8px"/>
          </div>`;
        hasChart = true;
      }

      if(hasChart){
        chartReportEl.innerHTML = chartHtml;
        document.body.appendChild(chartReportEl);
        await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));
        await addElementToPdf(chartReportEl);
        document.body.removeChild(chartReportEl);
      }

      document.body.removeChild(reportEl);

      const filename = `fuel-report-${today.toISOString().slice(0,10)}.pdf`;
      doc.save(filename);

    } catch(err){
      console.error('PDF error:', err);
      alert('เกิดข้อผิดพลาดในการสร้าง PDF: ' + err.message);
      if(reportEl.parentNode) reportEl.parentNode.removeChild(reportEl);
    } finally {
      if(btn){btn.disabled = false; btn.innerHTML = origHTML;}
    }
  }


  const REPORT_LOGS=@json($logs);
  let reportRendered=false,_reportCharts={};
  function renderReportPage(){
    if(reportRendered)return;reportRendered=true;
    const logs=REPORT_LOGS||[];
    const byDriver={};
    let totalPrice=0,totalLiters=0,totalHours=0,totalKm=0,kmlSum=0,kmlCount=0;
    logs.forEach(r=>{
      const n=r.driver_name||'—';
      if(!byDriver[n])byDriver[n]={price:0,liters:0,hours:0,km:0,rounds:0,kmlSum:0,kmlCount:0};
      const p=+(r.total_price||0),L=+(r.liters||0),h=+(r.work_hours||0),km=+(r.total_distance||0),kml=+(r.km_per_liter||0);
      byDriver[n].price+=p;byDriver[n].liters+=L;byDriver[n].hours+=h;byDriver[n].km+=km;byDriver[n].rounds++;
      if(kml>0){byDriver[n].kmlSum+=kml;byDriver[n].kmlCount++;}
      totalPrice+=p;totalLiters+=L;totalHours+=h;totalKm+=km;
      if(kml>0){kmlSum+=kml;kmlCount++;}
    });
    const avgKml=kmlCount>0?(kmlSum/kmlCount):0;
    const statRow=document.getElementById('repStatRow');
    if(statRow){
      statRow.innerHTML=`
        <div class="report-stat-card"><div class="report-stat-label">รายการทั้งหมด</div><div class="report-stat-value">${logs.length}</div><div class="report-stat-sub">ครั้ง</div></div>
        <div class="report-stat-card"><div class="report-stat-label">ค่าน้ำมันรวม</div><div class="report-stat-value">฿${totalPrice.toLocaleString(undefined,{maximumFractionDigits:0})}</div><div class="report-stat-sub">บาท</div></div>
        <div class="report-stat-card"><div class="report-stat-label">ลิตรที่เติม</div><div class="report-stat-value">${fmtN(totalLiters)}</div><div class="report-stat-sub">ลิตร</div></div>
        <div class="report-stat-card"><div class="report-stat-label">ระยะทางรวม</div><div class="report-stat-value">${totalKm.toLocaleString(undefined,{maximumFractionDigits:0})}</div><div class="report-stat-sub">กม.</div></div>
        <div class="report-stat-card"><div class="report-stat-label">เฉลี่ย km/L</div><div class="report-stat-value">${fmtN(avgKml)}</div><div class="report-stat-sub">กม./ลิตร</div></div>
        <div class="report-stat-card"><div class="report-stat-label">ชั่วโมงรวม</div><div class="report-stat-value">${fmtN(totalHours)}</div><div class="report-stat-sub">ชม.</div></div>
      `;
    }
    const drivers=Object.keys(byDriver);
    if(drivers.length===0)return;
    const sorted=drivers.map(n=>({name:n,...byDriver[n]})).sort((a,b)=>b.price-a.price);
    const pieColors=['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#ef4444','#6366f1','#06b6d4','#eab308','#f97316','#84cc16'];
    function makePie(canvasId,legendId,dataKey,fmt){
      const canvas=document.getElementById(canvasId);if(!canvas)return;
      if(_reportCharts[canvasId])_reportCharts[canvasId].destroy();
      const data=sorted.map(d=>d[dataKey]);
      const labels=sorted.map(d=>d.name);
      _reportCharts[canvasId]=new Chart(canvas,{
        type:'doughnut',
        data:{labels,datasets:[{data,backgroundColor:labels.map((_,i)=>pieColors[i%pieColors.length]),borderWidth:2,borderColor:'#fff'}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'58%',plugins:{legend:{display:false},datalabels:{display:false},tooltip:{callbacks:{label:ctx=>`${ctx.label}: ${fmt(ctx.raw)}`}}}}
      });
      const lg=document.getElementById(legendId);
      if(lg){
        const total=data.reduce((s,v)=>s+v,0);
        lg.innerHTML=sorted.map((d,i)=>{const v=data[i];const pct=total>0?fmtN(v/total*100,1):'0';return `<div class="pie-legend-item"><span class="pie-legend-dot" style="background:${pieColors[i%pieColors.length]}"></span><span class="pie-legend-label">${d.name}</span><span class="pie-legend-val">${fmt(v)} · ${pct}%</span></div>`;}).join('');
      }
    }
    makePie('pieCost','pieCostLegend','price',v=>'฿'+v.toLocaleString(undefined,{maximumFractionDigits:0}));
    makePie('pieLiters','pieLitersLegend','liters',v=>fmtN(v)+' L');
    makePie('pieHours','pieHoursLegend','hours',v=>fmtN(v)+' ชม.');
  }

  document.addEventListener('DOMContentLoaded',()=>{
    updateNavDate();setInterval(updateNavDate,60000);
    renderDlv();renderKmlChart();renderCostChart();renderOilPage();drpInit();
    let _t=null;
    window.addEventListener('resize',()=>{clearTimeout(_t);_t=setTimeout(()=>{renderDlv();renderKmlChart();renderCostChart();},200);});

    // Entry-card init (เฉพาะ view=day — element อาจไม่มีใน view อื่น)
    if(document.getElementById('il-work-date')){
      ilLoadOilPrice('diesel');
      (async()=>{
        const dateInput = document.getElementById('il-work-date');
        if(!dateInput) return;
        const today = dateInput.value || todayStr();
        const hint = document.getElementById('entryLoadingHint');
        if(hint) hint.style.display = 'flex';
        try{
          await Promise.all([
            fetchJobsByDate(today),
            fetchSavedDrivers(today),
          ]);
          ilLastLoadedDate = today;
          ilRenderDriverRows(today);
          ilRenderAllJobs(today);
        }catch(e){
          console.warn(e);
          const tbody = document.getElementById('entryRowsBody');
          if(tbody) tbody.innerHTML = '<div class="entry-empty" style="color:var(--red)">โหลดข้อมูลไม่สำเร็จ</div>';
        }finally{
          if(hint) hint.style.display = 'none';
        }
      })();
    }
  });
  </script>
  </body>
  </html>
<!DOCTYPE html>
{{-- resources/views/sale/dashboardshelf.blade.php  (ชั้น SALE — รอเช็คเอาท์) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ชั้น SALE</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#0f172a; --ink-700:#334155; --muted:#64748b; --faint:#94a3b8;
            --canvas:#fff; --page-bg:#f8fafc; --soft:#f1f5f9; --border:#e2e8f0; --line:#e8edf3;
            --primary:#1a4fd6; --primary-dark:#123bb0; --primary-light:#eaf0fd; --on-primary:#fff;
            --success:#10b981; --success-dark:#047857; --success-light:#d1fae5;
            --danger:#ef4444; --danger-dark:#b91c1c; --danger-light:#fee2e2;
            --warning:#f59e0b; --warning-dark:#b45309; --warning-light:#fef3c7;
            --shadow:0 1px 3px rgba(15,23,42,.04), 0 4px 12px rgba(15,23,42,.05);
            --shadow-hover:0 4px 6px -1px rgba(15,23,42,.05), 0 10px 15px -3px rgba(15,23,42,.08);
            --r-card:12px; --r-field:8px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{font-size:clamp(14.5px,0.3vw + 10.5px,17px)}   /* ตัวอักษรทั้งหน้า (ขยายใหญ่ขึ้น ~10%) */
        html,body{background:var(--page-bg);font-family:'Sarabun','Segoe UI',Tahoma,sans-serif;color:var(--ink);line-height:1.5;min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
        svg.i{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}
        .page-frame{width:100%;min-height:100vh;display:flex;flex-direction:column}
        html, body { height: 100%; }
        main{padding:0 15px 24px;flex:1;width:100%;display:flex;flex-direction:column;min-height:0}

        /* ===== แถบบน ===== */
        .top-banner{display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--line);border-radius:var(--r-card);
                    margin:15px 15px 12px;padding:12px 16px 12px 18px;box-shadow:var(--shadow);position:sticky;top:10px;z-index:100}
        .top-banner .h1{flex:none;color:var(--ink);text-decoration:none;cursor:pointer;font-weight:800;font-size:clamp(19px,.8vw + 10px,23px);letter-spacing:-.3px;display:flex;align-items:center;gap:10px;white-space:nowrap}
        .top-banner .logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(26,79,214,.25)}
        .top-banner .logo svg{width:18px;height:18px}
        .vsep{width:1px;height:28px;background:var(--line);flex:none}
        .tools{flex:1 1 760px;display:flex;align-items:center;gap:8px;min-width:0}
        .tools .f-sopo{flex:1.6 1 240px}
        .q-hint{flex:none;font-size:.72rem;font-weight:700;color:var(--primary-dark);background:var(--primary-light);border-radius:999px;padding:2px 9px;white-space:nowrap}
        .q-hint:empty{display:none}
        .tools .f-shelf,.tools .f-sale{flex:1 1 170px}
        .banner-right{display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:auto;flex-wrap:wrap}
        .icon-btn{width:40px;height:40px;flex:none;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:var(--r-field);background:#fff;color:var(--muted);cursor:pointer;transition:.15s;padding:0;text-decoration:none}
        .icon-btn svg{width:17px;height:17px}
        .icon-btn:hover{background:var(--page-bg);color:var(--ink);border-color:var(--border)}
        .print-btn{height:40px;flex:none;display:inline-flex;align-items:center;gap:6px;padding:0 14px;border-radius:var(--r-field);border:1px solid var(--border);background:#fff;color:var(--ink-700);font:inherit;font-size:.88rem;font-weight:600;cursor:pointer;transition:.15s;white-space:nowrap}
        .print-btn svg{width:17px;height:17px}
        .print-btn:hover:not(:disabled){background:var(--primary);border-color:var(--primary);color:#fff}
        .print-btn:disabled{opacity:.6;cursor:progress}
        
        @keyframes alertPulse{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.4)}50%{box-shadow:0 0 0 6px rgba(239,68,68,0)}}
        .overdue-alert{height:40px;display:inline-flex;align-items:center;gap:8px;padding:0 14px;border:1px solid #fecaca;background:#fef2f2;color:var(--danger-dark);
                       border-radius:var(--r-field);font-size:.88rem;font-weight:600;white-space:nowrap;animation:alertPulse 2s ease-in-out infinite}
        .overdue-alert b{font-size:1.1rem;font-weight:800}
        .overdue-alert .oa-icon{width:20px;height:20px;border-radius:50%;background:var(--danger);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:12px}
        
        .value-block{height:40px;display:inline-flex;align-items:center;gap:10px;padding:0 14px;border:1px solid var(--line);background:var(--page-bg);border-radius:var(--r-field);white-space:nowrap}
        .value-block .vb-label{font-size:.8rem;color:var(--muted)}
        .value-block .vb-amount{font-size:1.1rem;font-weight:800;color:var(--ink);font-variant-numeric:tabular-nums}
        .value-block .vb-unit{font-size:.78rem;font-weight:500;color:var(--muted);margin-left:4px}
        
        .user-badge{height:40px;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 5px;border-radius:999px;background:var(--page-bg);border:1px solid var(--line);font-size:.86rem;color:var(--ink-700);white-space:nowrap}
        .user-badge .av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--soft);color:var(--ink)}
        .user-badge .av svg{width:15px;height:15px}

        /* ===== ตัวกรอง ===== */
        .fld{height:40px;display:flex;align-items:center;gap:8px;padding:0 12px;background:var(--page-bg);border:1px solid var(--line);border-radius:var(--r-field);transition:.15s;min-width:0;flex:1 1 150px;position:relative}
        .fld:hover{border-color:var(--border);background:#fff}
        .fld:focus-within{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .fld .lbl{font-size:.82rem;font-weight:600;color:var(--faint);white-space:nowrap}
        .fld > svg{color:var(--faint)}
        .fld:has(.lbl){align-items:baseline}
        .fld:has(.lbl) > svg,.fld:has(.lbl) .q-hint{align-self:center}
        .fld .autocomplete-wrap{align-items:baseline}
        .fld input{border:0;outline:0;background:transparent;font:inherit;font-size:.92rem;color:var(--ink);height:100%;min-width:0;padding:0;flex:1;width:100%}
        .fld input::placeholder{color:var(--faint)}
        .fld input[readonly]{color:var(--muted);cursor:not-allowed}
        .fld.locked{background:var(--soft)}
        .fld .autocomplete-wrap{flex:1;min-width:0;height:100%;display:flex;position:static}
        button{font:inherit;cursor:pointer}
        
        .autocomplete-wrap{position:relative}
        .suggest-panel{display:none;position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid var(--border);border-radius:var(--r-field);box-shadow:var(--shadow-hover);max-height:min(320px,45vh);overflow-y:auto;z-index:9999;padding:4px}
        .suggest-panel.open{display:block}
        .suggest-item{padding:8px 12px;font-size:.88rem;cursor:pointer;border-radius:6px;color:var(--ink);transition:.1s}
        .suggest-item:hover,.suggest-item.hl{background:var(--primary-light);color:var(--primary-dark)}
        .suggest-empty{padding:12px 14px;font-size:.88rem;color:var(--muted);text-align:center}

        .fld-caret{flex:none;align-self:center;width:26px;height:26px;margin-right:-6px;border:0;background:transparent;border-radius:6px;color:var(--muted);
                   display:inline-flex;align-items:center;justify-content:center;cursor:pointer;padding:0;transition:.15s}
        .fld-caret svg{width:17px;height:17px;transition:transform .15s}
        .fld-caret:hover{background:var(--soft);color:var(--ink)}
        .fld:has(.suggest-panel.open) .fld-caret svg{transform:rotate(180deg)}
        
        .f-status{flex:0 1 230px;cursor:pointer;user-select:none}
        .f-status .st-btn{flex:1;min-width:0;height:100%;border:0;background:transparent;padding:0;text-align:left;font:inherit;font-size:.92rem;color:var(--ink);cursor:pointer;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;outline:0}
        .f-status .st-dot{align-self:center}
        .st-item{display:flex;align-items:center;gap:10px}
        .st-item .d{width:9px;height:9px;border-radius:50%;flex:none;background:var(--faint)}
        .st-item .n{margin-left:auto;font-weight:700;font-variant-numeric:tabular-nums;color:var(--muted);font-size:.84rem}
        .st-item.sel{font-weight:600}
        .st-item.sel .n{color:var(--ink)}
        .st-item .ck{width:15px;height:15px;color:var(--ink);visibility:hidden}
        .st-item.sel .ck{visibility:visible}
        .f-status .st-dot{width:10px;height:10px;border-radius:50%;flex:none;background:var(--faint);box-shadow:0 0 0 3px var(--soft)}
        .f-status[data-f="over"] .st-dot{background:#ef4444;box-shadow:0 0 0 3px var(--danger-light)}
        .f-status[data-f="today"] .st-dot{background:#f59e0b;box-shadow:0 0 0 3px var(--warning-light)}
        .f-status[data-f="up"] .st-dot{background:#10b981;box-shadow:0 0 0 3px var(--success-light)}
        .f-status[data-f="done"] .st-dot{background:#94a3b8}
        .f-status[data-f]:not([data-f="all"]){background:#fff;border-color:var(--primary)}

        /* ===== ตารางแบบยาว (Flat Table) ===== */
        .flat-table-wrapper {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--r-card);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
.flat-table-header {
    display: grid;
    grid-template-columns: var(--w1,120px) var(--w2,120px) var(--w3,140px) var(--w4,110px) var(--w5,110px) minmax(0,1fr);
    gap: 6px 40px;
    padding: 14px 20px;
    background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
    border-bottom: 2px solid var(--border);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    align-items: center;
}
        .flat-table-body {
            display: flex;
            flex-direction: column;
        }
        
        .it {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--line);
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
            background: #fff;
        }
        .it:last-child { border-bottom: 0; }
        .it:hover { 
            background: #fafbfc; 
            transform: translateX(2px);
            box-shadow: inset 4px 0 0 var(--rc, var(--primary));
        }
        
        .it.st-over{--rc:#ef4444} 
        .it.st-today{--rc:#f59e0b} 
        .it.st-up{--rc:#10b981} 
        .it.st-done{--rc:#cbd5e1; background:#fcfcfd;}
        .it.st-done .it-main, .it.st-done .it-side { opacity: 0.65; }
        .it.hidden-row { display: none; }

        .it-main {
            flex: 1;
            min-width: 0;
            display: grid;
            grid-template-columns: var(--w1,120px) var(--w2,120px) var(--w3,140px) var(--w4,110px) var(--w5,110px) minmax(0,1fr);
            justify-content: start;
            align-items: center;
            gap: 6px 40px;
            font-size: 0.92rem;
        }
        .it-main > span { min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .it-main .k { font-size: 0.72rem; color: var(--faint); margin-right: 5px; font-weight: 500; display: block; margin-bottom: 2px; }
        .it-main .c-po { color: var(--muted); font-family: 'Courier New', monospace; font-weight: 600; background: var(--soft); padding: 4px 8px; border-radius: 6px; display: inline-block; }
        .it-main .c-so { font-family: 'Courier New', monospace; font-weight: 700; color: var(--primary-dark); }
        .it-main .c-who { color: var(--ink-700); font-weight: 600; }
        .it-main .c-ship { color: var(--ink-700); font-weight: 500; }
        .it-main .c-co { font-size: 0.82rem; color: var(--muted); grid-column: 1 / -1; margin-top: 4px; padding-top: 8px; border-top: 1px dashed var(--line); }
        .it-main .c-co b { color: var(--ink-700); }
        .it-main .c-st { overflow: visible; display: flex; justify-content: flex-start; }
        
        .it-side { display: flex; align-items: center; gap: 12px; flex: none; }
        .it-price { min-width: 120px; text-align: right; font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; font-size: 1.05rem; color: var(--success-dark); }
        .it-price .u { font-weight: 600; color: var(--muted); font-size: 0.8rem; margin-left: 3px; }
        .num { font-variant-numeric: tabular-nums; }
        .ref-link { font-weight: 700; color: var(--primary-dark); text-decoration: none; border-bottom: 1px dashed var(--primary); }
        a.ref-link:hover { text-decoration: none; border-bottom-style: solid; }
        
        .pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .due-overdue { background: var(--danger-light); color: var(--danger-dark); }
        .due-today { background: var(--warning-light); color: var(--warning-dark); }
        .due-upcoming { background: var(--success-light); color: var(--success-dark); }
        .due-none, .due-done { background: var(--soft); color: var(--muted); }
        
        @keyframes pillPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 50% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); } }
        .it .due-overdue { animation: pillPulse 2s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { .due-overdue, .overdue-alert { animation: none; } }
        
        .manage { display: flex; gap: 8px; justify-content: flex-end; }
        .it-side .manage, .it-side .done-note { min-width: 184px; justify-content: flex-end; }
        .done-note { display: inline-flex; align-items: center; gap: 6px; font-size: 0.84rem; color: var(--muted); white-space: nowrap; font-weight: 600; }
        
        /* ปุ่มในตาราง */
        .btn-view {
            height: 36px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 14px;
            border: 1px solid var(--line); border-radius: var(--r-field); background: #fff; color: var(--ink-700);
            font-size: 0.84rem; font-weight: 600; white-space: nowrap; transition: all 0.15s ease;
        }
        .btn-view svg { width: 15px; height: 15px; }
        .btn-view:hover { background: var(--soft); border-color: var(--border); color: var(--ink); transform: translateY(-1px); }
        .btn-view .cnt { background: var(--soft); color: var(--muted); border-radius: 999px; padding: 0 7px; font-size: 0.74rem; line-height: 20px; font-weight: 700; }
        
        .btn-move { background: var(--page-bg); border-color: var(--border); }
        .btn-move:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary-dark); }
        
        .btn-checkout { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 2px 4px rgba(26, 79, 214, 0.3); }
        .btn-checkout:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 8px rgba(26, 79, 214, 0.4); }
        .btn-checkout:disabled { opacity: 0.6; cursor: progress; transform: none; }

        .empty-wrapper{display:flex;align-items:center;justify-content:center;flex:1;min-height:calc(100vh - 160px);width:100%;background:#fff;border:1px solid var(--line);border-radius:var(--r-card);box-shadow:var(--shadow);}
        .empty-state { text-align: center; color: var(--muted); font-size: 1rem; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .empty-state svg { width: 48px; height: 48px; color: var(--border); stroke-width: 1.5; }
        
        .loading-state { display: flex; flex-direction: column; align-items: center; gap: 14px; width: min(300px, 80%); padding: 40px 0; }
        .shelf-loader { position: relative; width: 110px; height: 84px; }
        .shelf-loader .bar { position: absolute; left: 0; right: 0; height: 5px; border-radius: 3px; background: var(--primary); }
        .shelf-loader .b1 { top: 36px; } .shelf-loader .b2 { bottom: 0; }
        .shelf-loader i { position: absolute; left: calc(8px + var(--x) * 34px); width: 26px; height: 22px; border-radius: 5px; background: var(--primary); opacity: 0; animation: boxDrop 1.8s ease-in-out infinite; animation-delay: calc(var(--k) * .2s); }
        .shelf-loader i.up { top: 12px; } .shelf-loader i.dn { bottom: 7px; }
        @keyframes boxDrop { 0% { opacity: 0; transform: translateY(-14px); } 15%, 70% { opacity: 1; transform: none; } 85%, 100% { opacity: 0; transform: none; } }
        .loading-label { color: var(--muted); font-size: 1rem; font-weight: 600; margin-top: 6px; animation: lblFade 1.6s ease-in-out infinite; }
        @keyframes lblFade { 0%, 100% { opacity: 1; } 50% { opacity: 0.45; } }

        /* ===== ป๊อปอัพ ===== */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 200; align-items: center; justify-content: center; padding: 20px; }
        .modal-box { background: #fff; border-radius: var(--r-card); width: min(92vw, 600px); box-shadow: 0 24px 60px rgba(0,0,0,.25); display: flex; flex-direction: column; max-height: 86vh; overflow: hidden; animation: modalPop .2s ease-out; }
        .modal-box-lg { width: min(94vw, 980px); }
        @keyframes modalPop { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
        .modal-title { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); font-weight: 700; font-size: 1.1rem; background: var(--page-bg); }
        .modal-body { overflow-y: auto; flex: 1; padding: 0; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; padding: 16px 22px; border-top: 1px solid var(--line); background: #fff; }
        .modal-x { width: 36px; height: 36px; border: 0; background: none; border-radius: 8px; color: var(--muted); font-size: 24px; line-height: 1; display: flex; align-items: center; justify-content: center; transition: .15s; }
        .modal-x:hover { background: var(--danger-light); color: var(--danger); }
        
        .sub-table { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: auto; }
        #productModal .modal-body { overflow-x: hidden; }
        .sub-table .col-fit { width: 1%; white-space: nowrap; }   /* คอลัมน์แคบพอดีเนื้อหา -> ชื่อสินค้าได้ที่เหลือทั้งหมด */
        .sub-table td:nth-child(2) { text-align: left; }
        @media (max-width:560px){
            .sub-table th, .sub-table td { padding: 10px 8px; }
            #productModal .btn-move { font-size: 0; gap: 0; padding: 0 10px; }   /* จอเล็ก: ปุ่มย้ายเหลือแต่ไอคอน */
            #productModal .btn-move svg { width: 17px; height: 17px; }
        }
        .sub-table th, .sub-table td { border: 0; border-bottom: 1px solid var(--line); padding: 12px 16px; font-size: 0.92rem; text-align: center; color: var(--ink); word-break: break-word; }
        .sub-table th { background: var(--page-bg); color: var(--ink-700); font-weight: 700; font-size: 0.84rem; position: sticky; top: 0; z-index: 10; }
        .sub-table tbody tr:last-child td { border-bottom: 0; }
        .sub-table tbody tr:hover td { background: var(--primary-light); }

        .move-modal-box { width: min(92vw, 440px); overflow: visible; max-height: none; }
        .move-modal-header { display: flex; align-items: center; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); background: var(--page-bg); }
        .move-modal-icon { width: 40px; height: 40px; border-radius: var(--r-field); flex-shrink: 0; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; }
        .move-modal-heading { flex: 1; min-width: 0; }
        .move-modal-title { font-weight: 700; font-size: 1.1rem; color: var(--ink); }
        .move-modal-sub { margin-top: 2px; font-size: 0.86rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .modal-close-btn { width: 36px; height: 36px; border: 0; background: none; border-radius: 8px; color: var(--muted); font-size: 24px; line-height: 1; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: .15s; }
        .modal-close-btn::before { content: "\00d7"; }
        .modal-close-btn:hover { background: var(--danger-light); color: var(--danger); }
        .move-modal-label { display: block; font-size: 0.86rem; font-weight: 600; color: var(--ink-700); margin: 18px 22px 6px; }
        .move-modal-input-wrap { position: relative; margin: 0 22px 20px; }
        .move-modal-input { width: 100%; height: 42px; padding: 0 40px 0 12px; border: 1px solid var(--line); border-radius: var(--r-field); background: var(--page-bg); font: inherit; font-size: 0.95rem; outline: none; transition: .15s; }
        .move-modal-input:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .move-modal-input-toggle { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 30px; height: 30px; border: 0; background: transparent; color: var(--muted); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .move-modal-input-toggle:hover { background: var(--soft); color: var(--ink); }

        /* ===== กล่องยืนยัน / แจ้งเตือน ===== */
        .ui-dlg-overlay { position: fixed; inset: 0; z-index: 400; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); display: flex; align-items: center; justify-content: center; padding: 20px; opacity: 0; visibility: hidden; transition: opacity .15s, visibility .15s; }
        .ui-dlg-overlay.open { opacity: 1; visibility: visible; }
        .ui-dlg { width: min(92vw, 420px); background: #fff; border-radius: 16px; box-shadow: 0 24px 60px rgba(0,0,0,.28); padding: 26px 24px 20px; text-align: center; transform: translateY(8px) scale(.97); transition: transform .15s; }
        .ui-dlg-overlay.open .ui-dlg { transform: none; }
        .ui-dlg-icon { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; background: var(--soft); color: var(--ink); }
        .ui-dlg-icon svg { width: 26px; height: 26px; }
        .ui-dlg.t-ok .ui-dlg-icon { background: var(--primary); color: #fff; box-shadow: 0 0 0 6px var(--primary-light); }
        .ui-dlg.t-error .ui-dlg-icon { background: var(--danger-light); color: var(--danger); box-shadow: 0 0 0 6px #fef2f2; }
        .ui-dlg-title { font-size: 1.2rem; font-weight: 700; color: var(--ink); }
        .ui-dlg-msg { margin-top: 6px; color: var(--muted); font-size: 0.95rem; }
        .ui-dlg-msg:empty, .ui-dlg-detail:empty { display: none; }
        .ui-dlg-detail { margin-top: 16px; background: var(--page-bg); border: 1px solid var(--line); border-radius: 12px; padding: 12px 14px; text-align: left; display: grid; grid-template-columns: auto 1fr; gap: 6px 16px; font-size: 0.92rem; }
        .ui-dlg-detail dt { color: var(--faint); font-size: 0.82rem; align-self: center; font-weight: 600; }
        .ui-dlg-detail dd { margin: 0; font-weight: 700; color: var(--ink); overflow-wrap: anywhere; }
        .ui-dlg-actions { display: flex; gap: 10px; margin-top: 22px; }
        .ui-dlg-actions button { flex: 1; justify-content: center; height: 44px; font-size: 0.95rem; }
        .ui-dlg-actions .btn-primary:disabled { opacity: 0.7; }
        .ui-dlg.single #uiDlgCancel { display: none; }
        
        .ui-toast { position: fixed; left: 50%; bottom: 24px; z-index: 450; transform: translate(-50%, 20px); opacity: 0; pointer-events: none; transition: .2s; display: flex; align-items: center; gap: 10px; background: var(--ink); color: #fff; padding: 12px 18px; border-radius: 12px; font-size: 0.92rem; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,.25); width: max-content; max-width: 92vw; }
        .ui-toast.show { opacity: 1; transform: translate(-50%, 0); }
        .ui-toast svg { width: 18px; height: 18px; flex: none; }
        .ui-toast.t-error { background: var(--danger); }

        /* ===== มาสคอต ===== */
        .mascot { position: fixed; left: 0; bottom: 4px; z-index: 150; width: 88px; cursor: pointer; user-select: none; will-change: transform; }
        .mascot.hidden { display: none; }
        body:has(.mascot:not(.hidden)) main { padding-bottom: 110px; }
        .mascot-pick { position: fixed; right: 16px; bottom: 16px; z-index: 160; }
        .mp-btn { width: 46px; height: 46px; border-radius: 50%; border: 1px solid var(--line); background: #fff; box-shadow: 0 4px 14px rgba(15,23,42,.12); padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; overflow: hidden; transition: .15s; }
        .mp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,23,42,.16); }
        .mp-btn svg { width: 40px; height: 43px; margin-top: 4px; }
        .mascot-pick.off .mp-btn { opacity: .55; filter: grayscale(1); }
        .mp-menu { display: none; position: absolute; right: 0; bottom: calc(100% + 10px); width: 300px; background: #fff; border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 16px 40px rgba(15,23,42,.18); padding: 12px; }
        .mascot-pick.open .mp-menu { display: block; animation: modalPop .15s ease-out; }
        .mp-head { font-weight: 700; font-size: .95rem; margin: 2px 4px 10px; color: var(--ink); }
        .mp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .mp-item { border: 1.5px solid var(--line); border-radius: 12px; background: #fff; padding: 6px 4px 6px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 2px; font: inherit; font-size: .78rem; color: var(--ink-700); transition: .15s; }
        .mp-item svg { width: 62px; height: 67px; }
        .mp-item:hover { border-color: var(--border); background: var(--page-bg); transform: translateY(-2px); }
        .mp-item.sel { border-color: var(--primary); background: var(--primary-light); font-weight: 700; color: var(--primary-dark); }
        .mp-toggle { width: 100%; margin-top: 10px; height: 38px; border-radius: 10px; border: 1px solid var(--line); background: var(--page-bg); font: inherit; font-size: .86rem; font-weight: 600; color: var(--ink-700); cursor: pointer; transition: .15s; }
        .mp-toggle:hover { background: var(--soft); color: var(--ink); }
        
        .mascot-pick svg .m-mouth-sad, .mascot-pick svg .m-mouth-o, .mascot .m-mouth-sad, .mascot .m-mouth-o { display: none; }
        .mascot.sad .m-mouth, .mascot.held .m-mouth, .mascot.sad .m-tongue, .mascot.held .m-tongue { display: none; }
        .mascot.sad:not(.held) .m-mouth-sad { display: inline; }
        .mascot.held .m-mouth-o { display: inline; }
        .mascot .m-tail { animation: mTail .6s ease-in-out infinite alternate; transform-box: fill-box; transform-origin: 0% 70%; }
        @keyframes mTail { from { transform: rotate(-8deg); } to { transform: rotate(10deg); } }
        .mascot .m-body { transform-origin: 50% 100%; }
        .mascot.left .m-body svg { transform: scaleX(-1); }
        .mascot .m-shadow { fill: rgba(15,23,42,.12); }
        /* สำคัญ: ให้แขน/ขา/ตัว หมุนรอบจุดของชิ้นนั้นเอง (ไม่งั้นจะหมุนรอบมุมภาพ แขนขาเหวี่ยงหลุด) */
        .mascot svg g[class^="m-"] { transform-box: fill-box; }
        .mascot .m-torso { transform-origin: 50% 100%; }
        .mascot.walk .m-torso { animation: mBob .5s ease-in-out infinite; }
        .mascot.walk .m-leg-l { animation: mStep .5s ease-in-out infinite; transform-origin: 50% 0; }
        .mascot.walk .m-leg-r { animation: mStep .5s ease-in-out infinite reverse; transform-origin: 50% 0; }
        .mascot.walk .m-arm-l { animation: mSwing .5s ease-in-out infinite; transform-origin: 50% 10%; }
        .mascot.walk .m-arm-r { animation: mSwing .5s ease-in-out infinite reverse; transform-origin: 50% 10%; }
        .mascot.wave .m-arm-r { animation: mWave .35s ease-in-out infinite alternate; transform-origin: 50% 10%; }
        .mascot.jump .m-body { animation: mJump .55s cubic-bezier(.3,1.6,.5,1) 2; }
        .mascot .m-eyes { animation: mBlink 4s infinite; transform-origin: 50% 50%; }
        @keyframes mBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
        /* ===== ท่าเดินเฉพาะตัว: เพนกวิน / ลูกเจี๊ยบ = เดินเตาะแตะโยกตัว กระพือปีกเบา ๆ ===== */
        .mascot[data-char="penguin"].walk .m-torso, .mascot[data-char="chick"].walk .m-torso { animation: mWaddle .6s ease-in-out infinite; transform-box: fill-box; transform-origin: 50% 100%; }
        .mascot[data-char="penguin"].walk .m-leg-l, .mascot[data-char="chick"].walk .m-leg-l { animation: mShuffle .6s ease-in-out infinite; transform-origin: 50% 50%; }
        .mascot[data-char="penguin"].walk .m-leg-r, .mascot[data-char="chick"].walk .m-leg-r { animation: mShuffle .6s ease-in-out infinite; animation-delay: -.3s; transform-origin: 50% 50%; }
        .mascot[data-char="penguin"] .m-arm-l, .mascot[data-char="chick"] .m-arm-l { transform-origin: 85% 8% !important; }
        .mascot[data-char="penguin"] .m-arm-r, .mascot[data-char="chick"] .m-arm-r { transform-origin: 15% 8% !important; }
        .mascot[data-char="penguin"].walk .m-arm-l, .mascot[data-char="chick"].walk .m-arm-l { animation: mFlipL .3s ease-in-out infinite alternate; }
        .mascot[data-char="penguin"].walk .m-arm-r, .mascot[data-char="chick"].walk .m-arm-r { animation: mFlipR .3s ease-in-out infinite alternate; }
        .mascot[data-char="penguin"].wave .m-arm-r, .mascot[data-char="chick"].wave .m-arm-r { animation: mFlipWave .3s ease-in-out infinite alternate; }
        .mascot[data-char="penguin"].held .m-arm-l, .mascot[data-char="chick"].held .m-arm-l { animation: mFlipL .15s ease-in-out infinite alternate; }
        .mascot[data-char="penguin"].held .m-arm-r, .mascot[data-char="chick"].held .m-arm-r { animation: mFlipR .15s ease-in-out infinite alternate; }
        @keyframes mWaddle { 0%, 100% { transform: rotate(-6deg) translateY(0); } 25% { transform: rotate(0deg) translateY(-2px); } 50% { transform: rotate(6deg) translateY(0); } 75% { transform: rotate(0deg) translateY(-2px); } }
        @keyframes mShuffle { 0%, 100% { transform: translate(0, 0); } 25% { transform: translate(0, -4px); } 50% { transform: translate(0, 0); } }
        @keyframes mFlipL { from { transform: rotate(0deg); } to { transform: rotate(18deg); } }
        @keyframes mFlipR { from { transform: rotate(0deg); } to { transform: rotate(-18deg); } }
        @keyframes mFlipWave { from { transform: rotate(-35deg); } to { transform: rotate(-70deg); } }
        @keyframes mStep { 0%, 100% { transform: translateY(0) rotate(14deg); } 50% { transform: translateY(-3px) rotate(-14deg); } }
        @keyframes mSwing { 0%, 100% { transform: rotate(-20deg); } 50% { transform: rotate(20deg); } }
        @keyframes mWave { from { transform: rotate(-155deg); } to { transform: rotate(-115deg); } }
        @keyframes mJump { 0%, 100% { transform: translateY(0) scale(1); } 15% { transform: translateY(0) scale(1.08,.9); } 50% { transform: translateY(-28px) scale(.96,1.05); } }
        @keyframes mBlink { 0%, 93%, 100% { transform: scaleY(1); } 95% { transform: scaleY(.1); } }
        .m-bubble { position: absolute; bottom: calc(100% + 4px); left: 50%; transform: translate(-50%, 6px); background: #fff; color: var(--ink); border: 1px solid var(--line); border-radius: 12px; padding: 8px 12px; font-size: .86rem; font-weight: 600; width: max-content; max-width: min(300px, 86vw); box-shadow: 0 8px 24px rgba(15,23,42,.14); margin-left: var(--bs, 0px); opacity: 0; pointer-events: none; transition: .2s; text-align: center; line-height: 1.4; }
        .m-bubble::after { content: ""; position: absolute; top: 100%; left: calc(50% - var(--bs, 0px)); margin-left: -7px; border: 7px solid transparent; border-top-color: #fff; }
        .m-bubble.show { opacity: 1; transform: translate(-50%, 0); }
        .mascot { touch-action: none; }
        .mascot.dragging { cursor: grabbing; }
        .mascot.held .m-body { animation: mDangle .9s ease-in-out infinite; transform-origin: 50% 0; }
        .mascot.held .m-leg-l { animation: mKick .3s ease-in-out infinite; transform-origin: 50% 0; }
        .mascot.held .m-leg-r { animation: mKick .3s ease-in-out infinite reverse; transform-origin: 50% 0; }
        .mascot.held .m-arm-l, .mascot.held .m-arm-r { animation: mFlap .25s ease-in-out infinite alternate; transform-origin: 50% 10%; }
        .mascot.held .m-shadow, .mascot.falling .m-shadow { opacity: 0; }
        .mascot.land .m-body { animation: mLand .45s cubic-bezier(.3,1.6,.5,1); }
        @keyframes mDangle { 0%, 100% { transform: rotate(-7deg); } 50% { transform: rotate(7deg); } }
        @keyframes mKick { 0%, 100% { transform: rotate(22deg); } 50% { transform: rotate(-22deg); } }
        @keyframes mFlap { from { transform: rotate(-40deg); } to { transform: rotate(-80deg); } }
        @keyframes mLand { 0% { transform: scale(1.18,.78); } 60% { transform: scale(.95,1.06); } 100% { transform: none; } }
        
        @media print { .mascot, .mascot-show, .flat-table-header { display: none !important; } .it { break-inside: avoid; page-break-inside: avoid; } }
        
        @media (max-width: 1500px) { 
            .tools { order: 3; flex-basis: 100%; } 
            .vsep { display: none; } 
            .it-main { column-gap: 24px; } 
            .it-main .c-co { grid-column: 1 / -1; } 
        }
        @media (max-width: 1200px) and (min-width: 761px) {
            .flat-table-header, .it-main { grid-template-columns: 135px 125px minmax(80px, 1fr) 112px; }
            .flat-table-header > :nth-child(5), .it-main .c-st { grid-column: 4; grid-row: 1; }
            .flat-table-header > :nth-child(4), .it-main .c-ship { grid-column: 4; grid-row: 2; }
            .flat-table-header > :nth-child(3), .it-main .c-who { grid-column: 1 / 4; grid-row: 2; }
        }
        @media (max-width: 1000px) {
            .top-banner { position: static; }
            .tools { flex-wrap: wrap; }
            .tools .fld { flex: 1 1 calc(50% - 4px); }
            .tools .f-sopo { flex-basis: 100%; }
            .tools .print-btn { flex: 1 1 100%; justify-content: center; }
        }
        @media (max-width: 760px) {
            .flat-table-header { display: none; }
            .it { flex-direction: column; align-items: stretch; gap: 10px; padding: 16px; }
            .it-main { grid-template-columns: auto auto 1fr; gap: 4px 12px; padding: 0; }
            .it-main .c-st { grid-column: 3; grid-row: 1; text-align: right; justify-content: flex-end; }
            .it-main .c-who { grid-column: 1 / 3; grid-row: 2; }
            .it-main .c-ship { grid-column: 3; grid-row: 2; text-align: right; justify-content: flex-end; }
            .it-main .c-co { grid-column: 1 / -1; margin-top: 8px; }
            .it-side { flex-wrap: wrap; justify-content: space-between; margin-top: 8px; padding-top: 12px; border-top: 1px dashed var(--line); }
            .it-price { min-width: 0; order: -1; text-align: left; }
            .manage { flex: 1 1 100%; margin-top: 8px; }
            .manage .btn-view { flex: 1; }
        }
        @media (max-width: 560px) {
            .user-badge { display: none; }
            .banner-right { width: 100%; flex-wrap: nowrap; }
            .value-block, .overdue-alert { flex: 1; justify-content: center; min-width: 0; padding: 0 10px; }
            .value-block .vb-label { display: none; }
            .empty-wrapper { min-height: 300px; }
        }
        /* ===== ปุ่มค้นหา ===== */
        .search-btn{position:relative;height:40px;flex:none;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 16px;border:0;border-radius:var(--r-field);
                    background:var(--primary);color:#fff;font:inherit;font-size:1.02rem;font-weight:700;cursor:pointer;white-space:nowrap;
                    box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 3px 10px rgba(26,79,214,.20);transition:transform .12s,box-shadow .15s,background .15s}
        .search-btn:hover{background:var(--primary-dark);box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 6px 16px rgba(26,79,214,.28);transform:translateY(-1px)}
        .search-btn:active{transform:translateY(0) scale(.97)}
        .search-btn:focus-visible{outline:3px solid var(--primary-light);outline-offset:2px}
        .search-btn .sb-ic{display:inline-flex}
        .search-btn .sb-ic svg{width:19px;height:19px}
        .search-btn .sb-kbd{font:inherit;font-size:.76rem;font-weight:600;color:rgba(255,255,255,.9);background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);
                            border-radius:6px;padding:1px 6px;line-height:18px}
        .search-btn .sb-spin{display:none;width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;animation:sbSpin .7s linear infinite}
        .search-btn.loading{cursor:progress}
        .search-btn.loading .sb-ic{display:none}
        .search-btn.loading .sb-spin{display:inline-block}
        /* มีตัวกรองเปลี่ยนแต่ยังไม่ได้ค้น -> จุดแจ้งเตือน + เด้งเบา ๆ */
        .search-btn.dirty::after{content:"";position:absolute;top:-4px;right:-4px;width:11px;height:11px;border-radius:50%;background:#ef4444;border:2px solid #fff}
        .search-btn.dirty{animation:sbNudge 1.8s ease-in-out infinite}
        @keyframes sbSpin{to{transform:rotate(360deg)}}
        @keyframes sbNudge{0%,100%{box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 3px 10px rgba(26,79,214,.20)}50%{box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 0 0 5px rgba(26,79,214,.15),0 3px 10px rgba(26,79,214,.20)}}
        @media (max-width:1000px){ .tools .search-btn{flex:1 1 100%;justify-content:center;height:44px} }
        @media (hover:none),(max-width:760px){ .search-btn .sb-kbd{display:none} }   /* มือถือไม่มีปุ่ม Enter */

        /* ===== ตารางรายการ: หัวตารางสีฟ้า + เส้นตาราง ===== */
        .ftbl-scroll{overflow-x:auto}
        .ftbl{width:100%;border-collapse:collapse;background:#fff}
        .ftbl th{position:sticky;top:0;z-index:2;background:var(--primary);color:#fff;font-size:.84rem;font-weight:700;text-align:center;padding:12px 14px;
                 border-right:1px solid rgba(255,255,255,.18);white-space:nowrap}
        .ftbl th:last-child{border-right:0}
        .ftbl td{padding:12px 14px;border-bottom:1px solid var(--border);border-right:1px solid var(--line);text-align:center;vertical-align:middle;font-size:.92rem}
        .ftbl td:last-child{border-right:0}
        .ftbl tbody tr:last-child td{border-bottom:0}
        .ftbl .it{display:table-row;padding:0;border:0;gap:0;transform:none !important;box-shadow:none !important}
        .ftbl .it.hidden-row{display:none}
        .ftbl tbody tr:nth-child(even){background:#fafbfd}
        .ftbl tbody tr:hover{background:#eef3fe}
        .ftbl tbody tr.it > td:first-child{box-shadow:inset 4px 0 0 var(--rc,transparent)}
        .ftbl .it.st-done td{opacity:.7}
        .ftbl .k{display:none}
        .ftbl .c-so{font-family:inherit;font-weight:700;color:var(--primary);white-space:nowrap}
        .ftbl .c-so .ref-link{font-family:inherit;font-weight:700;color:var(--primary);border-bottom:0;text-decoration:none}
        .ftbl .c-so a.ref-link:hover{text-decoration:underline}
        .ftbl .c-po{color:var(--muted);font-family:'Courier New',monospace;font-weight:600;background:var(--soft);padding:4px 8px;border-radius:6px;display:inline-block;white-space:nowrap}
        .ftbl .c-who{color:var(--ink-700);font-weight:600;display:inline-block;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle}
        .ftbl .c-ship{color:var(--ink-700);font-weight:500;white-space:nowrap}
        .ftbl .t-co{margin-top:6px;font-size:.78rem;color:var(--muted)}
        .ftbl .t-co .c-co{border:0;padding:0;margin:0}
        .ftbl .it-price{min-width:0;text-align:right;display:block}
        .ftbl .manage{justify-content:center;min-width:0}
        .ftbl .done-note{justify-content:center;min-width:0}
        @media (max-width:760px){
            .ftbl thead{display:none}
            .ftbl,.ftbl tbody{display:block}
            .ftbl .it{display:grid;grid-template-columns:1fr 1fr;gap:8px 14px;padding:14px 16px;border-bottom:1px solid var(--line);box-shadow:inset 4px 0 0 var(--rc,transparent) !important}
            .ftbl .it.hidden-row{display:none}
            .ftbl td{display:block;border:0;padding:0;text-align:left}
            .ftbl tbody tr.it > td:first-child{box-shadow:none}
            .ftbl td[data-label]::before{content:attr(data-label);display:block;font-size:.72rem;font-weight:600;color:var(--faint);margin-bottom:2px}
            .ftbl .t-who,.ftbl .t-manage{grid-column:1/-1}
            .ftbl .c-who{max-width:none;white-space:normal}
            .ftbl .it-price{text-align:left}
            .ftbl .manage{width:100%}
            .ftbl .manage .btn-view{flex:1}
        }
    </style>
</head>
<body>
<div class="page-frame">
    <div class="top-banner">
        <a class="h1" href="" title="รีเฟรชหน้า"><span class="logo"><svg class="i" viewBox="0 0 24 24"><path d="M4 4h16v6H4zM4 14h16v6H4z"/><path d="M8 7h.01M8 17h.01"/></svg></span>ชั้น SALE</a>
        <span class="vsep"></span>

        <div class="tools">
            <label class="fld f-sopo" for="fSoPo" title="พิมพ์เลข SO (เช่น 69/016727) หรือเลข PO">
                <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <input type="search" id="fSoPo" autocomplete="off" placeholder="ค้นหา เลข SO / PO">
                <span class="q-hint" id="soPoHint"></span>
            </label>
            <input type="hidden" id="fSo"><input type="hidden" id="fPo">
            <label class="fld f-shelf" for="fShelf"><span class="lbl">ชั้นวาง</span>
                <div class="autocomplete-wrap">
                    <input type="search" id="fShelf" autocomplete="off" placeholder="เลือกหรือพิมพ์ชั้น">
                    <div id="shelfSuggest" class="suggest-panel"></div>
                </div>
                <button type="button" class="fld-caret" id="shelfToggle" tabindex="-1" aria-label="แสดงรายการชั้น"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button></label>
            @if(($lockSale ?? false))
                <label class="fld f-sale locked" for="fSale" title="เห็นเฉพาะงานของคุณ"><span class="lbl">SALE</span>
                    <input type="search" id="fSale" value="{{ $loginName ?? '' }}" readonly></label>
            @else
                <label class="fld f-sale" for="fSale"><span class="lbl">SALE</span>
                    <div class="autocomplete-wrap">
                        <input type="search" id="fSale" autocomplete="off" placeholder="ชื่อ Sale">
                        <div id="saleSuggest" class="suggest-panel"></div>
                    </div>
                    <button type="button" class="fld-caret" id="saleToggle" tabindex="-1" aria-label="แสดงรายชื่อ Sale"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button></label>
            @endif
            <button type="button" class="search-btn" id="btnSearch" title="ค้นหา (หรือกด Enter ในช่องไหนก็ได้)">
                <span class="sb-ic"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
                <span class="sb-spin" aria-hidden="true"></span>
                <span class="sb-txt">ค้นหา</span>
                <kbd class="sb-kbd">Enter</kbd>
            </button>
            <div class="fld f-status" id="stats" title="กรองตามสถานะกำหนดส่ง" data-f="all">
                <span class="st-dot"></span>
                <button type="button" class="st-btn" id="fStatusBtn" aria-haspopup="listbox"><span id="fStatusText">ทุกสถานะ</span></button>
                <span class="fld-caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                <select id="fStatus" hidden>
                    <option value="all">ทุกสถานะ</option>
                    <option value="over">เลยกำหนด</option>
                    <option value="today">ครบวันนี้</option>
                    <option value="up">ยังไม่ถึงกำหนด</option>
                    <option value="done">เช็คเอาท์แล้ว</option>
                </select>
                <div id="statusPanel" class="suggest-panel" role="listbox"></div>
            </div>
            <button type="button" class="print-btn" id="btnPrint" title="พิมพ์รายการที่ยังไม่ได้เช็คเอาท์"><svg class="i" viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg><span>พิมพ์</span></button>
        </div>

        <div class="banner-right">
            <div class="overdue-alert" id="overdueAlert" style="display:none;" title="จำนวนรายการที่เลยกำหนดส่ง">
                <span class="oa-icon">!</span>เลยกำหนด <b id="overdueCount">0</b>
            </div>
            @if(($canSeePrice ?? false))
                <div class="value-block" title="มูลค่ารวมของรายการที่ค้นหา">
                    <span class="vb-label">มูลค่า</span>
                    <span class="vb-amount"><span id="totalValue">0.00</span><span class="vb-unit">฿</span></span>
                </div>
            @endif
            <span class="user-badge" title="ผู้ใช้งาน"><span class="av"><svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="name">{{ $creator }}</span></span>
            <a href="http://server-3e/3e/" class="icon-btn home" title="หน้าหลัก"><svg class="i" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></a>
        </div>
    </div>

    <main>
        <div id="shelfList" class="shelf-list">
            <div class="empty-wrapper"><div class="empty-state"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>พิมพ์หรือเลือกตัวกรอง (ชั้น / Sale / SO / PO) เพื่อค้นหา</div></div>
        </div>
    </main>
</div>

@if(($canManage ?? false))
<div id="moveModal" class="modal-overlay">
    <div class="modal-box move-modal-box">
        <div class="move-modal-header">
            <div class="move-modal-icon"><svg class="i" viewBox="0 0 24 24"><path d="M4 4h16v6H4zM4 14h16v6H4z"/><path d="M8 7h.01M8 17h.01"/></svg></div>
            <div class="move-modal-heading">
                <div class="move-modal-title">ย้ายชั้นวาง</div>
                <div class="move-modal-sub" id="movePoLabel"></div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeMove()" aria-label="ปิด"></button>
        </div>
        <label class="move-modal-label" for="moveShelfInput">ย้ายไปยังชั้น</label>
        <div class="move-modal-input-wrap">
            <input type="search" id="moveShelfInput" placeholder="เลือกหรือพิมพ์ชื่อชั้น..." autocomplete="off" class="move-modal-input">
            <button type="button" class="move-modal-input-toggle" id="moveShelfToggle" aria-label="แสดงรายการชั้น"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button>
            <div id="moveShelfSuggest" class="suggest-panel"></div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-view" onclick="closeMove()">ยกเลิก</button>
            <button type="button" class="btn-checkout" id="moveConfirmBtn" onclick="confirmMove()" style="width:auto; padding: 0 24px;">ยืนยันย้ายชั้น</button>
        </div>
    </div>
</div>
@endif

<div id="productModal" class="modal-overlay">
    <div class="modal-box modal-box-lg">
        <div class="modal-title">
            <span>รายการสินค้า — <span id="productPoLabel" style="color:var(--primary);"></span></span>
            <button type="button" class="modal-x" onclick="closeProductModal()" aria-label="ปิด">&times;</button>
        </div>
        <div class="modal-body">
            <table class="sub-table">
                <thead>
                    <tr>
                        <th class="col-fit">ชั้นวาง</th>
                        <th style="text-align: left;">ชื่อสินค้า</th>
                        <th class="col-fit">จำนวน</th>
                        @if(($canManage ?? false))
                            <th class="col-fit">จัดการ</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="productModalBody"></tbody>
            </table>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-view" onclick="closeProductModal()">ปิด</button>
        </div>
    </div>
</div>

<div id="uiDialog" class="ui-dlg-overlay" aria-hidden="true">
    <div class="ui-dlg" role="alertdialog" aria-modal="true" aria-labelledby="uiDlgTitle">
        <div class="ui-dlg-icon" id="uiDlgIcon"></div>
        <div class="ui-dlg-title" id="uiDlgTitle"></div>
        <div class="ui-dlg-msg" id="uiDlgMsg"></div>
        <div class="ui-dlg-detail" id="uiDlgDetail"></div>
        <div class="ui-dlg-actions">
            <button type="button" class="btn-view" id="uiDlgCancel">ยกเลิก</button>
            <button type="button" class="btn-checkout" id="uiDlgOk" style="width:auto; padding: 0 24px;">ตกลง</button>
        </div>
    </div>
</div>

<div id="mascot" class="mascot" aria-hidden="true" title="กดเพื่อคุย / กดค้างแล้วลากได้ / ดับเบิลคลิกเพื่อซ่อน">
    <div class="m-bubble" id="mascotBubble"></div>
    <div class="m-body" id="mascotBody"></div>
</div>
<div class="mascot-pick" id="mascotPick">
    <button type="button" class="mp-btn" id="mascotShow" title="เลือกตัวการ์ตูน" aria-label="เลือกตัวการ์ตูน" aria-haspopup="true"></button>
    <div class="mp-menu" id="mascotMenu" role="menu">
        <div class="mp-head">เลือกเพื่อนร่วมงาน</div>
        <div class="mp-grid" id="mascotGrid"></div>
        <button type="button" class="mp-toggle" id="mascotToggle"></button>
    </div>
</div>
<div id="uiToast" class="ui-toast" role="status" aria-live="polite"></div>

<script>
    const DATA_URL   = "{{ route('shelfsale.data') }}";
    const listEl     = document.getElementById('shelfList');
    const fShelf     = document.getElementById('fShelf');
    const fSale      = document.getElementById('fSale');
    const fSo        = document.getElementById('fSo');
    const fPo        = document.getElementById('fPo');
    const fSoPo      = document.getElementById('fSoPo');
    const soPoHint   = document.getElementById('soPoHint');

    let soPoMode = '';
    function syncSoPo(){
        const v = fSoPo.value.trim();
        if (!v) soPoMode = '';
        else if (/\//.test(v) || /^so/i.test(v)) soPoMode = 'so';
        else if (/^po/i.test(v) || /[a-z\-]/i.test(v)) soPoMode = 'po';
        else soPoMode = 'both';
        fSo.value = (soPoMode === 'so' || soPoMode === 'both') ? v : '';
        fPo.value = (soPoMode === 'po' || soPoMode === 'both') ? v : '';
        soPoHint.textContent = soPoMode === 'so' ? 'SO' : soPoMode === 'po' ? 'PO' : soPoMode === 'both' ? 'SO / PO' : '';
    }

    const AUTO_LOAD     = {{ ($autoLoad ?? false) ? 'true' : 'false' }};
    const LOGIN_NAME    = @json(($loginName ?? '') ?: ($creator ?? ''));
    const IS_SALE       = {{ ($isSaleView ?? false) ? 'true' : 'false' }};
    const CAN_SEE_PRICE = {{ ($canSeePrice ?? false) ? 'true' : 'false' }};
    const CAN_MANAGE    = {{ ($canManage ?? false) ? 'true' : 'false' }};
    const SHOW_CHECKOUT = {{ ($showCheckout ?? false) ? 'true' : 'false' }};
    const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
    const MOVE_URL      = "{{ route('shelfsale.move') }}";
    const CHECKOUT_URL  = "{{ route('shelfsale.checkout') }}";

    let currentRows = [];

    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
    function escJs(s){ return String(s ?? '').replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
    function fmtBaht(n){ return Number(n||0).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function soLink(so){
        if (!so || so === '-') return '<span class="ref-link">-</span>';
        return '<a class="ref-link" href="http://server_update:8000/sodetail?SONum=' + encodeURIComponent(so) + '" target="_blank" rel="noopener">' + esc(so) + '</a>';
    }
    function srcBadge(r){
        if (!r.is_legacy) return '';
        return ' <span style="display:inline-block;margin-left:6px;padding:1px 7px;border-radius:999px;font-size:.72rem;font-weight:700;background:#fef3c7;color:#b45309">ระบบเก่า</span>';
    }

    const SHELF_OPTIONS = @json($shelfOptions ?? []);
    const SALE_OPTIONS  = @json($saleOptions ?? []);
    function attachSuggest(input, panel, options, onPick, toggleBtn){
        if (!input || !panel) return;
        let hl = -1;
        function render(){
            const q = (input.value || '').trim().toLowerCase();
            const matches = (q ? options.filter(s => String(s).toLowerCase().includes(q)) : options).slice(0, 1000);
            hl = -1;
            if (!matches.length){ panel.innerHTML = '<div class="suggest-empty">ไม่พบตัวเลือก</div>'; panel.classList.add('open'); return; }
            panel.innerHTML = matches.map(s => '<div class="suggest-item" data-val="' + String(s).replace(/"/g,'&quot;') + '">' + esc(s) + '</div>').join('');
            panel.classList.add('open');
        }
        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        panel.addEventListener('mousedown', e => {
            const it = e.target.closest('.suggest-item'); if (!it) return;
            e.preventDefault(); input.value = it.dataset.val; panel.classList.remove('open'); input.focus();
            if (onPick) onPick();
        });
        input.addEventListener('keydown', e => {
            const items = Array.from(panel.querySelectorAll('.suggest-item'));
            if (e.key === 'ArrowDown' && items.length){ e.preventDefault(); hl = Math.min(hl+1, items.length-1); items.forEach((it,i)=>it.classList.toggle('hl', i===hl)); items[hl].scrollIntoView({block:'nearest'}); }
            else if (e.key === 'ArrowUp' && items.length){ e.preventDefault(); hl = Math.max(hl-1, 0); items.forEach((it,i)=>it.classList.toggle('hl', i===hl)); items[hl].scrollIntoView({block:'nearest'}); }
            else if (e.key === 'Enter' && hl >= 0 && items[hl]){ e.preventDefault(); input.value = items[hl].dataset.val; panel.classList.remove('open'); if (onPick) onPick(); }
            else if (e.key === 'Escape'){ panel.classList.remove('open'); }
        });
        input.addEventListener('blur', () => setTimeout(() => panel.classList.remove('open'), 120));
        document.addEventListener('mousedown', e => {
            if (!panel.classList.contains('open')) return;
            if (input.contains(e.target) || panel.contains(e.target)) return;
            if (toggleBtn && toggleBtn.contains(e.target)) return;
            panel.classList.remove('open');
        });
        if (toggleBtn) {
            toggleBtn.addEventListener('mousedown', e => {
                e.preventDefault();
                if (panel.classList.contains('open')) { panel.classList.remove('open'); }
                else { input.focus(); render(); }
            });
        }
    }
    attachSuggest(fShelf, document.getElementById('shelfSuggest'), SHELF_OPTIONS, () => searchNow(), document.getElementById('shelfToggle'));
    if (fSale && !fSale.readOnly) attachSuggest(fSale, document.getElementById('saleSuggest'), SALE_OPTIONS, () => searchNow(), document.getElementById('saleToggle'));
    attachSuggest(document.getElementById('moveShelfInput'), document.getElementById('moveShelfSuggest'), SHELF_OPTIONS, null, document.getElementById('moveShelfToggle'));

    function setMsg(text){
        listEl.innerHTML = '<div class="empty-wrapper"><div class="empty-state">' + ICON_EMPTY + esc(text) + '</div></div>';
        updateStats([]);
    }

    function setLoading(text){
        let boxes = '';
        for (let k = 0; k < 6; k++) boxes += '<i class="' + (k < 3 ? 'up' : 'dn') + '" style="--x:' + (k % 3) + ';--k:' + k + '"></i>';
        listEl.innerHTML = '<div class="empty-wrapper"><div class="loading-state">'
            + '<div class="shelf-loader" role="status" aria-label="' + esc(text) + '"><span class="bar b1"></span><span class="bar b2"></span>' + boxes + '</div>'
            + '<div class="loading-label">' + esc(text) + '</div></div></div>';
    }

    function updateOverdueAlert(n){
        const el = document.getElementById('overdueAlert');
        const c  = document.getElementById('overdueCount');
        if (c) c.textContent = n;
        if (el) el.style.display = n > 0 ? 'inline-flex' : 'none';
    }

    const ICON_EMPTY = '<svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>';
    const IC_BOX     = '<svg class="i" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>';
    const IC_MOVE    = '<svg class="i" viewBox="0 0 24 24"><path d="M5 9l-3 3 3 3M9 5l3-3 3 3M15 19l-3 3-3-3M19 9l3 3-3 3M2 12h20M12 2v20"/></svg>';
    const IC_OUT     = '<svg class="i" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>';
    const IC_CHECK   = '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';

    const DLG_ICONS = {
        ok:    '<svg class="i" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>',
        error: '<svg class="i" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>',
        info:  '<svg class="i" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>'
    };
    function uiDialog(o){
        return new Promise(resolve => {
            const ov = document.getElementById('uiDialog'), box = ov.querySelector('.ui-dlg');
            const ok = document.getElementById('uiDlgOk'), cancel = document.getElementById('uiDlgCancel');
            const tone = o.tone || 'info';
            box.className = 'ui-dlg t-' + tone + (o.cancelText === false ? ' single' : '');
            document.getElementById('uiDlgIcon').innerHTML = o.icon || DLG_ICONS[tone] || DLG_ICONS.info;
            document.getElementById('uiDlgTitle').textContent = o.title || '';
            document.getElementById('uiDlgMsg').textContent = o.message || '';
            document.getElementById('uiDlgDetail').innerHTML = (o.detail || [])
                .filter(d => d[1] !== undefined && d[1] !== null && d[1] !== '')
                .map(d => '<dt>' + esc(d[0]) + '</dt><dd>' + esc(d[1]) + '</dd>').join('');
            ok.textContent = o.okText || 'ตกลง';
            cancel.textContent = o.cancelText || 'ยกเลิก';
            const done = v => {
                ov.classList.remove('open'); ov.setAttribute('aria-hidden', 'true');
                ok.onclick = cancel.onclick = ov.onclick = null; document.removeEventListener('keydown', onKey);
                resolve(v);
            };
            const onKey = e => { if (e.key === 'Escape') done(false); else if (e.key === 'Enter') { e.preventDefault(); done(true); } };
            ok.onclick = () => done(true);
            cancel.onclick = () => done(false);
            ov.onclick = e => { if (e.target === ov) done(false); };
            document.addEventListener('keydown', onKey);
            ov.classList.add('open'); ov.setAttribute('aria-hidden', 'false');
            setTimeout(() => ok.focus(), 30);
        });
    }
    function uiAlert(title, message, tone){ return uiDialog({ title: title, message: message, tone: tone || 'error', okText: 'ตกลง', cancelText: false }); }
    let toastTimer = null;
    function toast(text, tone){
        const t = document.getElementById('uiToast');
        t.className = 'ui-toast' + (tone === 'error' ? ' t-error' : '');
        t.innerHTML = (tone === 'error' ? DLG_ICONS.error : '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>') + '<span>' + esc(text) + '</span>';
        requestAnimationFrame(() => t.classList.add('show'));
        clearTimeout(toastTimer); toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }

    function rowState(r){
        if (r.is_checkedout) return 'done';
        if (r.due_days === null || r.due_days === undefined) return 'up';
        if (r.due_days < 0) return 'over';
        if (r.due_days === 0) return 'today';
        return 'up';
    }
    let quickFilter = 'all';
    function applyQuickFilter(){
        listEl.querySelectorAll('.it[data-st]').forEach(it => {
            it.classList.toggle('hidden-row', quickFilter !== 'all' && it.dataset.st !== quickFilter);
        });
        fitColumns();
        const fs = document.getElementById('fStatus'); if (fs) fs.value = quickFilter;
        if (window.syncStatusText) window.syncStatusText();
        const st = document.getElementById('stats'); if (st) st.dataset.f = quickFilter;
    }
    function updateStats(rows){
        const c = { over:0, today:0, up:0, done:0 };
        rows.forEach(r => c[rowState(r)]++);
        const fs = document.getElementById('fStatus'); if (!fs) return;
        const n = { all: rows.length, over: c.over, today: c.today, up: c.up, done: c.done };
        const name = { all:'ทุกสถานะ', over:'เลยกำหนด', today:'ครบวันนี้', up:'ยังไม่ถึงกำหนด', done:'เช็คเอาท์แล้ว' };
        Array.from(fs.options).forEach(o => {
            o.textContent = name[o.value] + (rows.length ? ' (' + n[o.value].toLocaleString('th-TH') + ')' : '');
            o.hidden = (o.value === 'done' && !c.done);
        });
        if (quickFilter === 'done' && !c.done) { quickFilter = 'all'; applyQuickFilter(); }
    }
    document.getElementById('fStatus').addEventListener('change', e => {
        quickFilter = e.target.value || 'all';
        applyQuickFilter();
    });

    (function(){
        const box = document.getElementById('stats'), sel = document.getElementById('fStatus'),
              panel = document.getElementById('statusPanel'), txt = document.getElementById('fStatusText'),
              btn = document.getElementById('fStatusBtn');
        const DOT = { all:'var(--faint)', over:'#ef4444', today:'#f59e0b', up:'#10b981', done:'#94a3b8' };
        const ICK = '<svg class="i ck" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
        let hl = -1;
        function render(){
            const opts = Array.from(sel.options).filter(o => !o.hidden);
            panel.innerHTML = opts.map(o => {
                const m = o.textContent.match(/^(.*?)(?:\s*\(([\d,]+)\))?$/);
                return '<div class="suggest-item st-item' + (o.value === sel.value ? ' sel' : '') + '" data-val="' + o.value + '" role="option">'
                    + ICK + '<span class="d" style="background:' + DOT[o.value] + '"></span>' + esc(m[1])
                    + (m[2] ? '<span class="n">' + m[2] + '</span>' : '') + '</div>';
            }).join('');
            hl = -1;
        }
        function open(){ render(); panel.classList.add('open'); }
        function close(){ panel.classList.remove('open'); }
        function pick(v){ sel.value = v; sel.dispatchEvent(new Event('change')); close(); }
        box.addEventListener('mousedown', e => {
            const it = e.target.closest('.st-item');
            e.preventDefault();
            if (it) { pick(it.dataset.val); return; }
            btn.focus();
            panel.classList.contains('open') ? close() : open();
        });
        btn.addEventListener('keydown', e => {
            const items = Array.from(panel.querySelectorAll('.st-item'));
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!panel.classList.contains('open')) { open(); return; }
                hl = e.key === 'ArrowDown' ? Math.min(hl + 1, items.length - 1) : Math.max(hl - 1, 0);
                items.forEach((it, i) => it.classList.toggle('hl', i === hl));
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (panel.classList.contains('open') && items[hl]) pick(items[hl].dataset.val); else open();
            } else if (e.key === 'Escape') close();
        });
        btn.addEventListener('blur', () => setTimeout(close, 120));
        const sync = () => { const o = sel.options[sel.selectedIndex]; txt.textContent = o ? o.textContent : 'ทุกสถานะ'; if (panel.classList.contains('open')) render(); };
        sel.addEventListener('change', sync);
        new MutationObserver(sync).observe(sel, { subtree: true, childList: true, characterData: true });
        window.syncStatusText = sync;
    })();

    function dueCell(r){
        if (r.due_days === null || r.due_days === undefined) return { cls:'due-none', txt:'-' };
        if (r.due_days > 0)  return { cls:'due-upcoming', txt:'อีก ' + r.due_days + ' วัน' };
        if (r.due_days === 0) return { cls:'due-today', txt:'ครบวันนี้' };
        return { cls:'due-overdue', txt:'เลยกำหนด ' + Math.abs(r.due_days) + ' วัน' };
    }

    function itemHtml(r, i){
        const d  = dueCell(r);
        const st = rowState(r);
        const pill = r.is_checkedout
            ? '<span class="pill due-done">เช็คเอาท์แล้ว</span>'
            : (d.txt !== '-' ? '<span class="pill ' + d.cls + '">' + esc(d.txt) + '</span>' : '');

        const who = CAN_MANAGE
            ? '<span class="c-who" title="Sale ' + esc(r.sale || '-') + '"><span class="k">Sale</span>' + esc(r.sale || '-') + '</span>'
            : '<span class="c-who" title="' + esc(r.cust_name || '-') + '"><span class="k">ลูกค้า</span>' + esc(r.cust_name || '-') + '</span>';
        const ship = '<span class="c-ship"><span class="k">ส่ง</span>' + (r.ship_date ? esc(r.ship_date) : '-') + '</span>';
        const co = (SHOW_CHECKOUT && r.is_checkedout)
            ? '<span class="c-co">เช็คเอาท์โดย ' + (r.checkout_by ? '<b>' + esc(r.checkout_by) + '</b>' : '— (ระบบเก่า)')
              + (r.checkout_at ? ' ' + esc(r.checkout_at) : '') + '</span>'
            : '';

        const productBtn = '<button type="button" class="btn-view" onclick="openProductModal(' + i + ', \'' + escJs(r.po) + '\', \'' + escJs(r.so) + '\')">'
            + IC_BOX + 'สินค้า <span class="cnt">' + (r.item_count || 0) + '</span></button>';
        const price = CAN_SEE_PRICE ? '<span class="it-price">' + fmtBaht(r.price) + '<span class="u">฿</span></span>' : '';

        const manage = CAN_MANAGE
            ? (r.is_checkedout
                ? '<span class="done-note">' + IC_CHECK + 'เช็คเอาท์แล้ว</span>'
                : '<div class="manage"><button type="button" class="btn-view btn-move" onclick="openMove(\'' + escJs(r.po) + '\',\'' + escJs(r.so) + '\')">' + IC_MOVE + 'ย้ายชั้น</button>'
                  + '<button type="button" class="btn-view btn-checkout" onclick="doCheckout(this,\'' + escJs(r.po) + '\',\'' + escJs(r.so) + '\',' + (r.po_receive_id || 'null') + ')">' + IC_OUT + 'เช็คเอาท์</button></div>')
            : '';

        return '<tr class="it st-' + st + (quickFilter !== 'all' && quickFilter !== st ? ' hidden-row' : '') + '" data-st="' + st + '">'
            + '<td class="t-so" data-label="SO"><span class="c-so">' + soLink(r.so) + '</span></td>'
            + '<td class="t-po" data-label="PO"><span class="c-po" title="' + esc(r.po || '-') + '">' + esc(r.po || '-') + srcBadge(r) + '</span></td>'
            + '<td class="t-who" data-label="' + (CAN_MANAGE ? 'Sale' : 'ลูกค้า') + '">' + who + '</td>'
            + '<td class="t-ship" data-label="วันส่ง">' + ship + '</td>'
            + '<td class="t-st" data-label="สถานะ">' + pill + (co ? '<div class="t-co">' + co + '</div>' : '') + '</td>'
            + '<td class="t-prod" data-label="สินค้า">' + productBtn + '</td>'
            + (CAN_SEE_PRICE ? '<td class="t-price" data-label="มูลค่า">' + price + '</td>' : '')
            + (CAN_MANAGE ? '<td class="t-manage">' + manage + '</td>' : '')
            + '</tr>';
    }

    const COL_CLASSES = ['c-so', 'c-po', 'c-who', 'c-ship', 'c-st'];
    const COL_MAX     = [999, 999, 260, 999, 999];
    function fitColumns(){
        COL_CLASSES.forEach((c, k) => listEl.style.setProperty('--w' + (k + 1), 'max-content'));
        COL_CLASSES.forEach((c, k) => {
            let w = 0;
            // วัดความกว้างจากทั้งส่วนหัวตารางและข้อมูลในแถว เพื่อให้ตรงกันพอดี
            listEl.querySelectorAll('.it-main > .' + c + ', .flat-table-header > .' + c).forEach(el => { 
                w = Math.max(w, el.scrollWidth); 
            });
            listEl.style.setProperty('--w' + (k + 1), Math.min(Math.ceil(w) + 1, COL_MAX[k]) + 'px');
        });
    }

    // ฟังก์ชันใหม่: แสดงผลแบบตารางยาวเรียงตามสถานะ (เลยกำหนด -> วันนี้ -> ยังไม่ถึง -> เช็คเอาท์)
    function renderFlatTable(rows){
        if (rows.length === 0) return '';
        // ตารางจริง: หัวตารางสีฟ้า + เส้นตาราง (คอลัมน์ตรงกันอัตโนมัติ)
        let html = '<div class="flat-table-wrapper"><div class="ftbl-scroll"><table class="ftbl"><thead><tr>'
            + '<th>SO</th><th>PO</th><th>' + (CAN_MANAGE ? 'Sale' : 'ลูกค้า') + '</th><th>วันส่ง</th><th>สถานะ</th><th>สินค้า</th>'
            + (CAN_SEE_PRICE ? '<th>มูลค่า</th>' : '')
            + (CAN_MANAGE ? '<th>จัดการ</th>' : '')
            + '</tr></thead><tbody>';
        rows.forEach((r, i) => { html += itemHtml(r, i); });
        html += '</tbody></table></div></div>';
        return html;
    }

    function openProductModal(index, po, so) {
        const row = currentRows[index];
        if (!row) return;
        document.getElementById('productPoLabel').textContent = 'PO ' + po + (so ? ' / SO ' + so : '');
        const tbodyModal = document.getElementById('productModalBody');
        const products = row.products || [];
        const prodCols = CAN_MANAGE ? 4 : 3;
        
        if (products.length === 0) {
            tbodyModal.innerHTML = '<tr><td colspan="' + prodCols + '" style="text-align:center; padding: 24px; color: var(--muted);">ไม่มีรายการสินค้า</td></tr>';
        } else {
            tbodyModal.innerHTML = products.map(p => {
                const moveBtn = (CAN_MANAGE && !row.is_checkedout)
                    ? '<td style="text-align:center;"><button type="button" class="btn-view btn-move" onclick="closeProductModal(); openMove(\'' + escJs(po) + '\', \'' + escJs(so) + '\', ' + (p.line_id ? p.line_id : 'null') + ')">' + IC_MOVE + 'ย้ายชั้น</button></td>'
                    : (CAN_MANAGE ? '<td style="text-align:center;"><span class="done-note">' + IC_CHECK + 'เช็คเอาท์แล้ว</span></td>' : '');
                const qtyTxt = (p.qty !== null && p.qty !== undefined && p.qty !== '') ? (parseFloat(p.qty) + '') : '-';
                return '<tr>' +
                    '<td>' + (p.shelf ? '<span class="pill" style="background:var(--soft);color:var(--ink-700);font-weight:600;">' + esc(p.shelf) + '</span>' : '<span style="color:var(--faint)">-</span>') + '</td>' +
                    '<td style="text-align:left; font-weight:500;">' + esc(p.name || '-') + '</td>' +
                    '<td style="text-align:center; font-weight:700; color:var(--ink);">' + esc(qtyTxt) + '</td>' +
                    moveBtn +
                    '</tr>';
            }).join('');
        }
        document.getElementById('productModal').style.display = 'flex';
    }

    function closeProductModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    function printDoc() {
        const rows = (currentRows || []).filter(r => !r.is_checkedout);
        if (!rows.length) {
            uiAlert('ไม่มีรายการให้พิมพ์', 'ไม่มีรายการที่ยังไม่ได้เช็คเอาท์', 'info');
            return;
        }
        const nz = v => (v !== null && v !== undefined && v !== '' && !isNaN(parseFloat(v))) ? (parseFloat(v) + '') : '-';
        const custLabel = CAN_MANAGE ? 'Sale' : 'ลูกค้า';

        let body = '';
        rows.forEach((r, idx) => {
            const products = r.products || [];
            let prod = '';
            if (!products.length) {
                prod = '<tr><td colspan="3" class="pc">ไม่มีรายการสินค้า</td></tr>';
            } else {
                prod = products.map(p => {
                    const recv = nz(p.qty);
                    const ord  = nz(p.ordered);
                    return '<tr>'
                        + '<td class="pl">' + esc(p.name || '-') + '</td>'
                        + '<td class="pc">' + esc(p.shelf || '-') + '</td>'
                        + '<td class="pc"><b>' + esc(recv) + '</b> / ' + esc(ord) + '</td>'
                        + '</tr>';
                }).join('');
            }
            body += '<div class="blk">'
                + '<div class="blk-h">'
                +   '<span class="bh-no">' + (idx + 1) + '.</span>'
                +   '<span class="bh-i"><span class="bh-l">SO</span> ' + esc(r.so || '-') + '</span>'
                +   '<span class="bh-i"><span class="bh-l">PO</span> ' + esc(r.po || '-') + '</span>'
                +   '<span class="bh-i"><span class="bh-l">ชั้นวาง</span> ' + esc(r.shelf || '-') + '</span>'
                +   '<span class="bh-i"><span class="bh-l">' + custLabel + '</span> ' + esc(CAN_MANAGE ? (r.sale || '-') : (r.cust_name || '-')) + '</span>'
                +   '<span class="bh-i"><span class="bh-l">กำหนดส่ง</span> ' + esc(r.ship_date || '-') + '</span>'
                + '</div>'
                + '<table class="ptbl">'
                + '<colgroup><col class="c-name"><col class="c-shelf"><col class="c-qty"></colgroup>'
                + '<thead><tr>'
                +   '<th class="pl">สินค้า</th><th class="pc">ชั้นวาง</th><th class="pc">รับเข้าจริง / สั่ง</th>'
                + '</tr></thead><tbody>' + prod + '</tbody></table>'
                + '</div>';
        });

        const now = new Date();
        const stamp = now.toLocaleString('th-TH');
        const summary = 'ทั้งหมด ' + rows.length + ' รายการ';

        const html = '<!DOCTYPE html><html lang="th"><head><meta charset="utf-8">'
            + '<title>เอกสารรายการสินค้า</title><style>'
            + '*{box-sizing:border-box;} body{font-family:"Sarabun","TH Sarabun New",Tahoma,sans-serif;color:#111;margin:16px auto;padding:0 24px;max-width:900px;font-size:13px;}'
            + 'h1{font-size:17px;margin:0 0 2px;} .meta{font-size:12px;color:#555;margin-bottom:10px;}'
            + '.blk{padding:2px 0 8px;margin-bottom:8px;page-break-inside:avoid;break-inside:avoid;}'
            + '.blk-h{display:flex;flex-wrap:wrap;gap:6px 16px;align-items:baseline;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid #999;}'
            + '.bh-no{font-weight:800;font-size:14px;} .bh-i{font-size:13px;} .bh-l{color:#777;font-size:11px;}'
            + '.ptbl{width:100%;border-collapse:collapse;table-layout:fixed;} .ptbl th,.ptbl td{border:1px solid #bbb;padding:4px 6px;font-size:12.5px;overflow-wrap:anywhere;}'
            + '.ptbl col.c-name{width:auto;} .ptbl col.c-shelf{width:26%;} .ptbl col.c-qty{width:22%;}'
            + '.ptbl th{background:#eee;} .pl{text-align:left;} .pc{text-align:center;}'
            + 'thead{display:table-header-group;}'
            + '@page{margin:12mm 10mm;}'
            + '@media print{body{margin:0;padding:0 14mm;max-width:none;} .blk{page-break-inside:avoid;break-inside:avoid;padding-top:6px;}}'
            + '</style></head><body>'
            + '<h1>รายการสินค้า (ยังไม่ได้เช็คเอาท์)</h1>'
            + '<div class="meta">พิมพ์เมื่อ ' + esc(stamp) + ' · ' + esc(summary) + '</div>'
            + body
            + '<scr' + 'ipt>window.onload=function(){setTimeout(function(){window.print();},250);};</scr' + 'ipt>'
            + '</body></html>';

        const old = document.getElementById('printFrame'); if (old) old.remove();
        const fr = document.createElement('iframe');
        fr.id = 'printFrame';
        fr.setAttribute('aria-hidden', 'true');
        fr.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
        document.body.appendChild(fr);
        const btn = document.getElementById('btnPrint');
        if (btn) { btn.disabled = true; setTimeout(() => { btn.disabled = false; }, 1500); }
        const d = fr.contentWindow.document;
        d.open(); d.write(html); d.close();
    }

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'productModal') closeProductModal();
                if (this.id === 'moveModal') closeMove();
            }
        });
    });

    let moveTarget = null;
    function openMove(po, so, lineId){
        moveTarget = { po: po, so: so, lineId: (lineId || null) };
        document.getElementById('movePoLabel').textContent = 'PO ' + po + (so ? ' / SO ' + so : '')
            + (moveTarget.lineId ? ' (เฉพาะรายการนี้)' : '');
        const inp = document.getElementById('moveShelfInput'); inp.value = '';
        document.getElementById('moveModal').style.display = 'flex';
    }
    function closeMove(){ 
        const m = document.getElementById('moveModal'); 
        if (m) m.style.display = 'none'; 
        moveTarget = null; 
    }
    async function confirmMove(){
        if (!moveTarget) return;
        const shelf = document.getElementById('moveShelfInput').value.trim();
        if (!shelf) { toast('กรุณาเลือกชั้นที่จะย้ายไป', 'error'); document.getElementById('moveShelfInput').focus(); return; }
        const btn = document.getElementById('moveConfirmBtn'); btn.disabled = true; btn.textContent = 'กำลังบันทึก...';
        try {
            const res = await fetch(MOVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ po: moveTarget.po, so: moveTarget.so, shelf: shelf, line_id: moveTarget.lineId })
            });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || !data.ok) { uiAlert('ย้ายชั้นไม่สำเร็จ', (data && data.message) || 'กรุณาลองใหม่อีกครั้ง'); return; }
            closeMove(); search(); toast('ย้ายไปชั้น ' + shelf + ' แล้ว'); window.mascot && window.mascot.cheer('ย้ายไปชั้น ' + shelf + ' แล้วนะ 📦');
        } catch (e) { console.error(e); uiAlert('เชื่อมต่อไม่สำเร็จ', 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่'); }
        finally { btn.disabled = false; btn.textContent = 'ยืนยันย้ายชั้น'; }
    }
    async function doCheckout(btn, po, so, poReceiveId){
        const r = (currentRows || []).find(x => x.po === po && x.so === so && (x.po_receive_id || null) === (poReceiveId || null)) || {};
        const ok = await uiDialog({
            tone: 'ok',
            title: 'ยืนยันเช็คเอาท์',
            message: 'เช็คเอาท์รายการนี้ออกจากชั้น (รอบนี้)',
            detail: [['SO', so || '-'], ['PO', po || '-'], ['ชั้นวาง', r.shelf], [CAN_MANAGE ? 'Sale' : 'ลูกค้า', CAN_MANAGE ? r.sale : r.cust_name]],
            okText: 'เช็คเอาท์', cancelText: 'ยกเลิก'
        });
        if (!ok) return;
        btn.disabled = true;
        try {
            const res = await fetch(CHECKOUT_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ po: po, so: so, po_receive_id: poReceiveId || null })
            });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || !data.ok) { uiAlert('เช็คเอาท์ไม่สำเร็จ', (data && data.message) || 'กรุณาลองใหม่อีกครั้ง'); btn.disabled = false; return; }
            search(); toast('เช็คเอาท์ PO ' + po + ' แล้ว'); window.mascot && window.mascot.cheer('เช็คเอาท์เรียบร้อย 🎉');
        } catch (e) { console.error(e); uiAlert('เชื่อมต่อไม่สำเร็จ', 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่'); btn.disabled = false; }
    }

    function hasFilter(){
        return !!(fShelf.value.trim() || fSale.value.trim() || fSo.value.trim() || fPo.value.trim());
    }

    let searchSeq = 0;
    async function search(){
        const seq = ++searchSeq;
        if (!AUTO_LOAD && !hasFilter()) {
            const tv0 = document.getElementById('totalValue'); if (tv0) tv0.textContent = '0.00';
            updateOverdueAlert(0);
            setMsg('พิมพ์หรือเลือกตัวกรอง (ชั้น / Sale / SO / PO) เพื่อค้นหา');
            return;
        }

        const mk = (so, po) => {
            const params = new URLSearchParams();
            params.set('shelf', fShelf.value.trim());
            params.set('sale',  fSale.value.trim());
            params.set('so',    so);
            params.set('po',    po);
            return fetch(DATA_URL + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(async res => ({ res, data: await res.json() }));
        };

        setLoading('กำลังโหลดข้อมูลชั้นวาง...');
        if (btnSearch) btnSearch.classList.add('loading');
        try {
            let res, data;
            if (soPoMode === 'both') {
                const v = fSoPo.value.trim();
                const [a, b] = await Promise.all([mk(v, ''), mk('', v)]);
                res = (a.res.ok && a.data && a.data.ok) ? a.res : b.res;
                const okA = a.res.ok && a.data && a.data.ok, okB = b.res.ok && b.data && b.data.ok;
                if (!okA && !okB) { data = a.data || b.data; }
                else {
                    const seen = new Set(), rows = [];
                    [].concat(okA ? (a.data.rows || []) : [], okB ? (b.data.rows || []) : []).forEach(r => {
                        const k = [r.so, r.po, r.po_receive_id].join('|');
                        if (!seen.has(k)) { seen.add(k); rows.push(r); }
                    });
                    const tv = rows.reduce((s, r) => s + (Number(r.price) || 0), 0);
                    data = { ok: true, rows: rows, total_value: tv, message: (okA ? a.data.message : b.data.message) };
                }
            } else {
                ({ res, data } = await mk(fSo.value.trim(), fPo.value.trim()));
            }
            if (seq !== searchSeq) return;
            if (!res.ok || !data.ok) { setMsg((data && data.message) || 'ค้นหาไม่สำเร็จ'); return; }

            const rows = data.rows || [];
            currentRows = rows;

            // ====== SORTING LOGIC: เลยกำหนด (มากไปน้อย) -> วันนี้ -> ยังไม่ถึง -> เช็คเอาท์ ======
            rows.sort((a, b) => {
                // 1. เช็คเอาท์แล้ว ไปอยู่ล่างสุดเสมอ
                if (a.is_checkedout !== b.is_checkedout) {
                    return a.is_checkedout ? 1 : -1;
                }
                // 2. เรียงตาม due_days (ติดลบมาก = เลยกำหนดนาน = อยู่บนสุด)
                // ถ้าไม่มีวัน (null) ให้ถือว่ามีค่ามากที่สุด (อยู่ล่างสุดของกลุ่มที่ยังไม่เช็คเอาท์)
                const da = (a.due_days === null || a.due_days === undefined) ? 99999 : a.due_days;
                const db = (b.due_days === null || b.due_days === undefined) ? 99999 : b.due_days;
                return da - db;
            });

            const tv = document.getElementById('totalValue');
            if (tv) tv.textContent = fmtBaht(data.total_value || 0);

            const overdue = rows.filter(r => !r.is_checkedout && r.due_days !== null && r.due_days !== undefined && r.due_days < 0).length;
            updateOverdueAlert(overdue);
            
            if (window.mascot) {
                if (!window.mascot.reported) setTimeout(() => window.mascot.report(rows), 700);
                else if (overdue > 0) setTimeout(() => window.mascot.worryOnce(overdue), 600);
            }

            if (rows.length === 0) { setMsg(data.message || 'ไม่พบรายการตามตัวกรอง'); return; }

            // เปลี่ยนจาก renderGroups เป็น renderFlatTable
            listEl.innerHTML = renderFlatTable(rows);
            fitColumns();
            if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitColumns);
            updateStats(rows);
            applyQuickFilter();
        } catch (e) {
            if (seq !== searchSeq) return;
            console.error(e);
            setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            updateOverdueAlert(0);
        } finally {
            if (seq === searchSeq && btnSearch) btnSearch.classList.remove('loading');
        }
    }

    let searchDebounce = null;
    function searchNow(){ runSearch(); }   // เลือกจากรายการชั้น / Sale = ค้นทันที
    function scheduleSearch(delay = 450){
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => { search(); }, delay);
    }

    // ===== เพิ่ม Event Listener สำหรับปุ่มค้นหา =====
    const btnSearch = document.getElementById('btnSearch');
    function setDirty(v){ if (btnSearch) btnSearch.classList.toggle('dirty', !!v); }
    function runSearch(){
        syncSoPo();                                   // อ่านค่าช่อง SO / PO ล่าสุดก่อนค้น (เดิมลืมเรียก -> ค้นไม่เจอ)
        if (searchDebounce) clearTimeout(searchDebounce);
        setDirty(false);
        search();
    }
    if (btnSearch) btnSearch.addEventListener('click', runSearch);

    // ===== ลบ auto-search ออกจาก input event =====
    [fShelf, fSale, fSoPo].forEach(el => {
        if (!el || el.readOnly) return;
        el.addEventListener('input', () => {
            if (el === fSoPo) syncSoPo();             // อัปเดตป้าย SO / PO ระหว่างพิมพ์
            setDirty(true);                           // ไม่ค้นอัตโนมัติ - รอกดปุ่มหรือ Enter
        });
    });

    // ===== ค้นหาเมื่อกด Enter =====
    [fShelf, fSale, fSoPo].forEach(el => {
        if (!el) return;
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); runSearch(); }
        });
    });

    const btnPrint = document.getElementById('btnPrint');
    if (btnPrint) btnPrint.addEventListener('click', printDoc);

    if (fSale && fSale.readOnly && fSale.value.trim()) { search(); }

    @if(($autoLoad ?? false))
    search();
    @endif

    // ===== มาสคอต "น้องกล่อง" =====
    window.mascot = (function(){
        const el = document.getElementById('mascot'), bubble = document.getElementById('mascotBubble'), showBtn = document.getElementById('mascotShow');
        const body = document.getElementById('mascotBody'), pick = document.getElementById('mascotPick'),
              grid = document.getElementById('mascotGrid'), toggleBtn = document.getElementById('mascotToggle');
        const MASCOTS = [{"id": "box", "name": "น้องกล่อง", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><linearGradient id=\"bxA\" x1=\"0\" y1=\"0\" x2=\"0\" y2=\"1\"><stop offset=\"0\" stop-color=\"#f2c48d\"/><stop offset=\"1\" stop-color=\"#dfa264\"/></linearGradient><linearGradient id=\"bxB\" x1=\"0\" y1=\"0\" x2=\"1\" y2=\"0\"><stop offset=\"0\" stop-color=\"#e9b77c\"/><stop offset=\"1\" stop-color=\"#f7d3a3\"/></linearGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<g class=\"m-leg m-leg-l\"><ellipse cx=\"45\" cy=\"117\" rx=\"10\" ry=\"7\" fill=\"#8a5a33\"/><ellipse cx=\"43\" cy=\"115\" rx=\"4\" ry=\"2\" fill=\"#a8703f\"/></g>\n<g class=\"m-leg m-leg-r\"><ellipse cx=\"75\" cy=\"117\" rx=\"10\" ry=\"7\" fill=\"#8a5a33\"/><ellipse cx=\"73\" cy=\"115\" rx=\"4\" ry=\"2\" fill=\"#a8703f\"/></g>\n<g class=\"m-torso\">\n <rect x=\"18\" y=\"38\" width=\"84\" height=\"74\" rx=\"20\" fill=\"url(#bxA)\"/>\n <rect x=\"18\" y=\"38\" width=\"84\" height=\"16\" rx=\"8\" fill=\"#c98a4f\" opacity=\".35\"/>\n <path d=\"M16 42 Q20 30 34 28 L60 26 L60 40 L20 46 Z\" fill=\"url(#bxB)\"/><path d=\"M104 42 Q100 30 86 28 L60 26 L60 40 L100 46 Z\" fill=\"#f7d3a3\"/>\n <rect x=\"54\" y=\"25\" width=\"12\" height=\"17\" rx=\"3\" fill=\"#fff4e0\" opacity=\".85\"/>\n <path d=\"M52 16 q8 -10 16 0 q-8 -3 -16 0z\" fill=\"#7cc576\"/><path d=\"M60 26 L60 14\" stroke=\"#5aa654\" stroke-width=\"2.4\" stroke-linecap=\"round\"/>\n <g class=\"m-eyes\"><ellipse cx=\"45\" cy=\"74\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><ellipse cx=\"75\" cy=\"74\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><circle cx=\"47.2\" cy=\"70.8\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"77.2\" cy=\"70.8\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"42.6\" cy=\"77.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"72.6\" cy=\"77.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/></g><ellipse cx=\"34\" cy=\"88\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"86\" cy=\"88\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><path class=\"m-mouth\" d=\"M54 86 Q57.0 90 60 86 Q63.0 90 66 86\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M54 90 Q60 84 66 90\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"88\" rx=\"3.6\" ry=\"4.4\" fill=\"#1f1a17\"/>\n <g class=\"m-arm m-arm-l\"><ellipse cx=\"17\" cy=\"78\" rx=\"7\" ry=\"10\" fill=\"#e3a86b\" stroke=\"#c98a4f\" stroke-width=\"1.2\"/></g>\n <g class=\"m-arm m-arm-r\"><ellipse cx=\"103\" cy=\"78\" rx=\"7\" ry=\"10\" fill=\"#e3a86b\" stroke=\"#c98a4f\" stroke-width=\"1.2\"/></g>\n</g></svg>"}, {"id": "cat", "name": "น้องเหมียว", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><radialGradient id=\"ctA\" cx=\".4\" cy=\".35\" r=\".8\"><stop offset=\"0\" stop-color=\"#ffc27a\"/><stop offset=\"1\" stop-color=\"#f39a3d\"/></radialGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<path class=\"m-tail\" d=\"M92 104 Q116 100 110 78 Q108 70 102 74\" fill=\"none\" stroke=\"#f39a3d\" stroke-width=\"8\" stroke-linecap=\"round\"/>\n<g class=\"m-leg m-leg-l\"><ellipse cx=\"46\" cy=\"117\" rx=\"9\" ry=\"7\" fill=\"#fff3e6\" stroke=\"#f0c9a0\"/></g>\n<g class=\"m-leg m-leg-r\"><ellipse cx=\"74\" cy=\"117\" rx=\"9\" ry=\"7\" fill=\"#fff3e6\" stroke=\"#f0c9a0\"/></g>\n<g class=\"m-torso\">\n <ellipse cx=\"60\" cy=\"98\" rx=\"30\" ry=\"20\" fill=\"url(#ctA)\"/><ellipse cx=\"60\" cy=\"102\" rx=\"17\" ry=\"13\" fill=\"#fff3e6\"/>\n <path d=\"M24 46 L22 18 L44 32 Z\" fill=\"#f39a3d\"/><path d=\"M28 40 L27 25 L38 33 Z\" fill=\"#ffb3b3\"/>\n <path d=\"M96 46 L98 18 L76 32 Z\" fill=\"#f39a3d\"/><path d=\"M92 40 L93 25 L82 33 Z\" fill=\"#ffb3b3\"/>\n <ellipse cx=\"60\" cy=\"58\" rx=\"40\" ry=\"32\" fill=\"url(#ctA)\"/>\n <path d=\"M24 30 Q30 18 60 17 Q90 18 96 30 L98 36 Q60 24 22 36 Z\" fill=\"#facc15\"/><rect x=\"18\" y=\"33\" width=\"84\" height=\"6\" rx=\"3\" fill=\"#eab308\"/>\n <ellipse cx=\"60\" cy=\"72\" rx=\"16\" ry=\"11\" fill=\"#fff3e6\"/>\n <g class=\"m-eyes\"><ellipse cx=\"44\" cy=\"60\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><ellipse cx=\"76\" cy=\"60\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><circle cx=\"46.2\" cy=\"56.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"78.2\" cy=\"56.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"41.6\" cy=\"63.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"73.6\" cy=\"63.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/></g><path d=\"M57 66 L63 66 L60 69.5 Z\" fill=\"#ff7a8a\"/><path class=\"m-mouth\" d=\"M53 72 Q56.5 76 60 72 Q63.5 76 67 72\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M53 76 Q60 70 67 76\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"74\" rx=\"3.6\" ry=\"4.4\" fill=\"#1f1a17\"/><ellipse cx=\"30\" cy=\"70\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"90\" cy=\"70\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/>\n <path d=\"M20 64 L8 62 M20 69 L8 71 M100 64 L112 62 M100 69 L112 71\" stroke=\"#c46a1c\" stroke-width=\"1.6\" stroke-linecap=\"round\" opacity=\".6\"/>\n <rect x=\"44\" y=\"86\" width=\"32\" height=\"24\" rx=\"5\" fill=\"#dfa264\"/><rect x=\"57\" y=\"86\" width=\"6\" height=\"24\" fill=\"#f7d3a3\"/>\n <g class=\"m-arm m-arm-l\"><ellipse cx=\"40\" cy=\"96\" rx=\"7\" ry=\"8\" fill=\"#f39a3d\"/></g>\n <g class=\"m-arm m-arm-r\"><ellipse cx=\"80\" cy=\"96\" rx=\"7\" ry=\"8\" fill=\"#f39a3d\"/></g>\n</g></svg>"}, {"id": "chick", "name": "ลูกเจี๊ยบ", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><radialGradient id=\"ckA\" cx=\".38\" cy=\".3\" r=\".85\"><stop offset=\"0\" stop-color=\"#fff3a6\"/><stop offset=\"1\" stop-color=\"#ffd23f\"/></radialGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<g class=\"m-leg m-leg-l\"><path d=\"M44 108 L44 118 M38 120 L44 117 L50 120\" stroke=\"#ff9b3d\" stroke-width=\"4\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></g>\n<g class=\"m-leg m-leg-r\"><path d=\"M76 108 L76 118 M70 120 L76 117 L82 120\" stroke=\"#ff9b3d\" stroke-width=\"4\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></g>\n<g class=\"m-torso\">\n <ellipse cx=\"60\" cy=\"72\" rx=\"40\" ry=\"40\" fill=\"url(#ckA)\"/><ellipse cx=\"60\" cy=\"92\" rx=\"24\" ry=\"16\" fill=\"#fff8cc\" opacity=\".8\"/>\n <rect x=\"38\" y=\"10\" width=\"44\" height=\"26\" rx=\"6\" fill=\"#e2a868\"/><rect x=\"38\" y=\"10\" width=\"44\" height=\"8\" rx=\"4\" fill=\"#cf9150\"/><rect x=\"56\" y=\"10\" width=\"8\" height=\"26\" fill=\"#f7d3a3\"/>\n <g class=\"m-eyes\"><ellipse cx=\"45\" cy=\"66\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><ellipse cx=\"75\" cy=\"66\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><circle cx=\"47.2\" cy=\"62.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"77.2\" cy=\"62.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"42.6\" cy=\"69.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"72.6\" cy=\"69.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/></g><path d=\"M53 75 Q60 70 67 75 Q60 82 53 75 Z\" fill=\"#ff9b3d\"/><ellipse cx=\"32\" cy=\"80\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"88\" cy=\"80\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/>\n <g class=\"mouth-wrap\" transform=\"translate(0,4)\"><path class=\"m-mouth\" d=\"M56 80 Q58.0 84 60 80 Q62.0 84 64 80\" fill=\"none\" stroke=\"#e07a1c\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M56 84 Q60 78 64 84\" fill=\"none\" stroke=\"#e07a1c\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"82\" rx=\"3.6\" ry=\"4.4\" fill=\"#e07a1c\"/></g>\n <g class=\"m-arm m-arm-l\"><path d=\"M22 70 Q8 78 16 92 Q24 88 28 78 Z\" fill=\"#ffc21a\"/></g>\n <g class=\"m-arm m-arm-r\"><path d=\"M98 70 Q112 78 104 92 Q96 88 92 78 Z\" fill=\"#ffc21a\"/></g>\n</g></svg>"}, {"id": "lizard", "name": "น้องเงินทอง", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><radialGradient id=\"lzA\" cx=\".4\" cy=\".3\" r=\".85\"><stop offset=\"0\" stop-color=\"#a7c06a\"/><stop offset=\"1\" stop-color=\"#6f8a3c\"/></radialGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<path class=\"m-tail\" d=\"M72 104 Q96 116 112 102 Q120 94 114 88 Q110 86 108 92 Q102 102 80 92 Z\" fill=\"#6f8a3c\"/>\n<g class=\"m-leg m-leg-l\"><ellipse cx=\"44\" cy=\"116\" rx=\"10\" ry=\"7\" fill=\"#5d7531\"/><path d=\"M36 120 l-3 3 M41 122 l-1 3 M47 122 l1 3\" stroke=\"#3f5420\" stroke-width=\"2\" stroke-linecap=\"round\"/></g>\n<g class=\"m-leg m-leg-r\"><ellipse cx=\"76\" cy=\"116\" rx=\"10\" ry=\"7\" fill=\"#5d7531\"/><path d=\"M70 122 l-1 3 M76 122 l1 3 M82 120 l3 3\" stroke=\"#3f5420\" stroke-width=\"2\" stroke-linecap=\"round\"/></g>\n<g class=\"m-torso\">\n <ellipse cx=\"60\" cy=\"96\" rx=\"30\" ry=\"20\" fill=\"url(#lzA)\"/><ellipse cx=\"60\" cy=\"100\" rx=\"17\" ry=\"12\" fill=\"#e9e3a8\"/>\n <ellipse cx=\"60\" cy=\"56\" rx=\"42\" ry=\"30\" fill=\"url(#lzA)\"/>\n <ellipse cx=\"60\" cy=\"68\" rx=\"30\" ry=\"14\" fill=\"#93aa58\"/>\n <circle cx=\"34\" cy=\"40\" r=\"4\" fill=\"#e8d36a\" opacity=\".85\"/><circle cx=\"48\" cy=\"33\" r=\"3.2\" fill=\"#e8d36a\" opacity=\".85\"/><circle cx=\"66\" cy=\"31\" r=\"3.6\" fill=\"#e8d36a\" opacity=\".85\"/><circle cx=\"82\" cy=\"36\" r=\"3\" fill=\"#e8d36a\" opacity=\".85\"/><circle cx=\"90\" cy=\"46\" r=\"2.6\" fill=\"#e8d36a\" opacity=\".85\"/>\n <circle cx=\"44\" cy=\"92\" r=\"2.6\" fill=\"#e8d36a\" opacity=\".8\"/><circle cx=\"78\" cy=\"90\" r=\"2.6\" fill=\"#e8d36a\" opacity=\".8\"/>\n <g class=\"m-eyes\"><ellipse cx=\"42\" cy=\"52\" rx=\"7\" ry=\"8.4\" fill=\"#1f1a17\"/><ellipse cx=\"78\" cy=\"52\" rx=\"7\" ry=\"8.4\" fill=\"#1f1a17\"/><circle cx=\"44.1\" cy=\"49.1\" r=\"3.0\" fill=\"#fff\"/><circle cx=\"80.1\" cy=\"49.1\" r=\"3.0\" fill=\"#fff\"/><circle cx=\"39.8\" cy=\"55.1\" r=\"1.4\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"75.8\" cy=\"55.1\" r=\"1.4\" fill=\"#fff\" opacity=\".9\"/></g><circle cx=\"54\" cy=\"66\" r=\"1.6\" fill=\"#3f5420\"/><circle cx=\"66\" cy=\"66\" r=\"1.6\" fill=\"#3f5420\"/>\n <path class=\"m-mouth\" d=\"M51 73 Q55.5 77 60 73 Q64.5 77 69 73\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M51 77 Q60 71 69 77\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"75\" rx=\"3.6\" ry=\"4.4\" fill=\"#1f1a17\"/><ellipse cx=\"30\" cy=\"64\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"90\" cy=\"64\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/>\n <path class=\"m-tongue\" d=\"M60 78 L60 86 M60 86 l-3 3 M60 86 l3 3\" stroke=\"#ff5f7e\" stroke-width=\"2.2\" stroke-linecap=\"round\" fill=\"none\"/>\n <g class=\"m-arm m-arm-l\"><ellipse cx=\"32\" cy=\"94\" rx=\"7\" ry=\"9\" fill=\"#6f8a3c\"/></g>\n <g class=\"m-arm m-arm-r\"><ellipse cx=\"88\" cy=\"94\" rx=\"7\" ry=\"9\" fill=\"#6f8a3c\"/></g>\n</g></svg>"}, {"id": "dog", "name": "น้องหมา", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><radialGradient id=\"dgA\" cx=\".4\" cy=\".3\" r=\".85\"><stop offset=\"0\" stop-color=\"#e9b27a\"/><stop offset=\"1\" stop-color=\"#c8874a\"/></radialGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<path class=\"m-tail\" d=\"M88 100 Q108 96 106 80\" fill=\"none\" stroke=\"#c8874a\" stroke-width=\"8\" stroke-linecap=\"round\"/>\n<g class=\"m-leg m-leg-l\"><ellipse cx=\"46\" cy=\"117\" rx=\"9\" ry=\"7\" fill=\"#fff7ec\" stroke=\"#ead6bd\"/></g>\n<g class=\"m-leg m-leg-r\"><ellipse cx=\"74\" cy=\"117\" rx=\"9\" ry=\"7\" fill=\"#fff7ec\" stroke=\"#ead6bd\"/></g>\n<g class=\"m-torso\">\n <ellipse cx=\"60\" cy=\"98\" rx=\"29\" ry=\"19\" fill=\"url(#dgA)\"/><ellipse cx=\"60\" cy=\"102\" rx=\"16\" ry=\"12\" fill=\"#fff7ec\"/>\n <ellipse cx=\"60\" cy=\"56\" rx=\"38\" ry=\"33\" fill=\"url(#dgA)\"/>\n <path d=\"M30 32 Q14 34 14 58 Q16 70 26 66 Q30 50 34 40 Z\" fill=\"#8a5530\"/><path d=\"M90 32 Q106 34 106 58 Q104 70 94 66 Q90 50 86 40 Z\" fill=\"#8a5530\"/>\n <ellipse cx=\"60\" cy=\"70\" rx=\"17\" ry=\"13\" fill=\"#fff7ec\"/><ellipse cx=\"74\" cy=\"46\" rx=\"9\" ry=\"8\" fill=\"#fff7ec\" opacity=\".9\"/>\n <g class=\"m-eyes\"><ellipse cx=\"45\" cy=\"54\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><ellipse cx=\"75\" cy=\"54\" rx=\"7.5\" ry=\"9.0\" fill=\"#1f1a17\"/><circle cx=\"47.2\" cy=\"50.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"77.2\" cy=\"50.9\" r=\"3.2\" fill=\"#fff\"/><circle cx=\"42.6\" cy=\"57.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"72.6\" cy=\"57.4\" r=\"1.5\" fill=\"#fff\" opacity=\".9\"/></g><ellipse cx=\"60\" cy=\"64\" rx=\"5.5\" ry=\"4\" fill=\"#2b1d16\"/><ellipse cx=\"58.5\" cy=\"62.8\" rx=\"1.6\" ry=\"1\" fill=\"#fff\" opacity=\".7\"/>\n <path class=\"m-mouth\" d=\"M54 70 Q57.0 74 60 70 Q63.0 74 66 70\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M54 74 Q60 68 66 74\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"72\" rx=\"3.6\" ry=\"4.4\" fill=\"#1f1a17\"/><ellipse cx=\"31\" cy=\"68\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"89\" cy=\"68\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/>\n <path class=\"m-tongue\" d=\"M57 73 Q57 81 60 81 Q63 81 63 73 Z\" fill=\"#ff7d8f\"/>\n <path d=\"M38 86 Q60 94 82 86\" stroke=\"#e5484d\" stroke-width=\"5\" fill=\"none\" stroke-linecap=\"round\"/><circle cx=\"60\" cy=\"93\" r=\"4\" fill=\"#facc15\" stroke=\"#eab308\"/>\n <g class=\"m-arm m-arm-l\"><ellipse cx=\"38\" cy=\"100\" rx=\"7\" ry=\"8\" fill=\"#c8874a\"/></g>\n <g class=\"m-arm m-arm-r\"><ellipse cx=\"82\" cy=\"100\" rx=\"7\" ry=\"8\" fill=\"#c8874a\"/></g>\n</g></svg>"}, {"id": "penguin", "name": "น้องเพนกวิน", "svg": "<svg viewBox=\"0 0 120 130\" width=\"88\" height=\"95\"><defs><radialGradient id=\"pgA\" cx=\".4\" cy=\".3\" r=\".85\"><stop offset=\"0\" stop-color=\"#4a6284\"/><stop offset=\"1\" stop-color=\"#2c3e57\"/></radialGradient></defs><ellipse class=\"m-shadow\" cx=\"60\" cy=\"125\" rx=\"30\" ry=\"4.5\"/>\n<g class=\"m-leg m-leg-l\"><ellipse cx=\"46\" cy=\"118\" rx=\"10\" ry=\"6\" fill=\"#ff9b3d\"/></g>\n<g class=\"m-leg m-leg-r\"><ellipse cx=\"74\" cy=\"118\" rx=\"10\" ry=\"6\" fill=\"#ff9b3d\"/></g>\n<g class=\"m-torso\">\n <ellipse cx=\"60\" cy=\"70\" rx=\"40\" ry=\"44\" fill=\"url(#pgA)\"/>\n <path d=\"M60 36 Q30 38 32 72 Q34 108 60 110 Q86 108 88 72 Q90 38 60 36 Z\" fill=\"#fff\"/>\n <path d=\"M38 44 Q60 30 82 44 Q82 54 70 52 Q60 46 50 52 Q38 54 38 44Z\" fill=\"#2c3e57\"/>\n <g class=\"m-eyes\"><ellipse cx=\"47\" cy=\"58\" rx=\"6.5\" ry=\"7.8\" fill=\"#1f1a17\"/><ellipse cx=\"73\" cy=\"58\" rx=\"6.5\" ry=\"7.8\" fill=\"#1f1a17\"/><circle cx=\"49.0\" cy=\"55.3\" r=\"2.8\" fill=\"#fff\"/><circle cx=\"75.0\" cy=\"55.3\" r=\"2.8\" fill=\"#fff\"/><circle cx=\"44.9\" cy=\"60.9\" r=\"1.3\" fill=\"#fff\" opacity=\".9\"/><circle cx=\"70.9\" cy=\"60.9\" r=\"1.3\" fill=\"#fff\" opacity=\".9\"/></g><path d=\"M54 67 Q60 63 66 67 Q60 73 54 67 Z\" fill=\"#ff9b3d\"/>\n <g transform=\"translate(0,6)\"><path class=\"m-mouth\" d=\"M56 70 Q58.0 74 60 70 Q62.0 74 64 70\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path class=\"m-mouth-sad\" d=\"M56 74 Q60 68 64 74\" fill=\"none\" stroke=\"#1f1a17\" stroke-width=\"2.3\" stroke-linecap=\"round\"/><ellipse class=\"m-mouth-o\" cx=\"60\" cy=\"72\" rx=\"3.6\" ry=\"4.4\" fill=\"#1f1a17\"/></g><ellipse cx=\"36\" cy=\"72\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/><ellipse cx=\"84\" cy=\"72\" rx=\"7\" ry=\"4.5\" fill=\"#ff8f8f\" opacity=\".55\"/>\n <path d=\"M26 84 Q60 98 94 84 L94 92 Q60 106 26 92 Z\" fill=\"#e5484d\"/><path d=\"M80 92 L86 112 L76 110 L74 94 Z\" fill=\"#e5484d\"/>\n <path d=\"M30 88 L90 88\" stroke=\"#fff\" stroke-width=\"2\" stroke-dasharray=\"4 5\" opacity=\".7\"/>\n <g class=\"m-arm m-arm-l\"><path d=\"M22 62 Q8 80 16 98 Q26 92 28 74 Z\" fill=\"#2c3e57\"/></g>\n <g class=\"m-arm m-arm-r\"><path d=\"M98 62 Q112 80 104 98 Q94 92 92 74 Z\" fill=\"#2c3e57\"/></g>\n</g></svg>"}];
        const CHAR_KEY = 'shelfsale_mascot_char';
        let cur = MASCOTS[0];
        try { cur = MASCOTS.find(m => m.id === localStorage.getItem(CHAR_KEY)) || MASCOTS[0]; } catch (e) {}
        let svgSeq = 0;
        const uniq = svg => { const n = ++svgSeq; return svg.replace(/id="(\w+)"/g, 'id="$1_' + n + '"').replace(/url\(#(\w+)\)/g, 'url(#$1_' + n + ')'); };
        function applyChar(m, announce){
            cur = m; el.dataset.char = m.id; body.innerHTML = uniq(m.svg); showBtn.innerHTML = uniq(m.svg);
            el.title = m.name + ' · กดเพื่อคุย / กดค้างแล้วลากได้ / ดับเบิลคลิกเพื่อซ่อน';
            try { localStorage.setItem(CHAR_KEY, m.id); } catch (e) {}
            grid.querySelectorAll('.mp-item').forEach(b => b.classList.toggle('sel', b.dataset.id === m.id));
            if (announce) { hide(false); setMode('wave', 2200, true); say('สวัสดีครับ ผม' + m.name + ' 👋', 2600); }
        }
        grid.innerHTML = MASCOTS.map(m => '<button type="button" class="mp-item" data-id="' + m.id + '" role="menuitem">' + uniq(m.svg) + '<span>' + m.name + '</span></button>').join('');
        if (!el) return { say(){}, cheer(){}, worry(){} };
        const KEY = 'shelfsale_mascot_hidden';
        const W = 88, SPEED = 38;
        let x = 40, dir = 1, mode = 'walk', until = 0, last = performance.now(), bubbleTimer = null, raf = null;
        let y = 0, vy = 0, dragging = false, justDragged = false;
        const TIPS = ['เปลี่ยนตัวการ์ตูนได้ที่ปุ่มมุมขวาล่างนะ 🎭', 'พิมพ์เลข SO หรือ PO ได้ในช่องเดียวเลยนะ', 'ของเลยกำหนดจะอยู่บนสุดของตารางเสมอ', 'ดับเบิลคลิกผม ถ้าอยากให้ผมไปพัก 😴', 'กดค้างแล้วลากผมไปไหนก็ได้นะ 🎈', 'วันนี้ก็สู้ ๆ นะครับ '];
        function setMode(m, ms, force){
            if ((mode === 'held' || mode === 'fall') && m !== 'held' && m !== 'fall' && !force) return;
            mode = m; until = performance.now() + (ms || 0);
            el.classList.toggle('walk', m === 'walk'); el.classList.toggle('wave', m === 'wave'); }
        function say(text, ms){
            bubble.textContent = text; bubble.classList.add('show');
            clearTimeout(bubbleTimer); bubbleTimer = setTimeout(() => bubble.classList.remove('show'), ms || 3200);
        }
        function tick(now){
            const dt = Math.min(0.05, (now - last) / 1000); last = now;
            const maxX = Math.max(0, window.innerWidth - W - 8);
            if (mode === 'held') {
            } else if (mode === 'fall') {
                vy += 1800 * dt; y -= vy * dt;
                if (y <= 0) {
                    y = 0; vy = 0; el.classList.remove('falling');
                    el.classList.remove('land'); void el.offsetWidth; el.classList.add('land'); setTimeout(() => el.classList.remove('land'), 500);
                    setMode('idle', 1400, true);
                    say(['ตุ้บ! 😵', 'ว้าย~ เกือบไป 😆', 'ขอบคุณที่พามาเดินเล่นนะ 😄', 'ฮึบ! ลงพื้นสวย ๆ ✨'][Math.floor(Math.random() * 4)], 2200);
                }
            } else if (mode === 'walk') {
                x += dir * SPEED * dt;
                if (x <= 8) { x = 8; dir = 1; } else if (x >= maxX) { x = maxX; dir = -1; }
                if (Math.random() < dt * 0.08) setMode(Math.random() < .5 ? 'idle' : 'wave', 1800 + Math.random() * 2200);
            } else if (now > until) {
                if (Math.random() < .3) dir = -dir;
                setMode('walk');
            }
            if (mode !== 'held') x = Math.min(Math.max(x, 0), maxX);
            el.classList.toggle('left', dir < 0);
            el.style.transform = 'translate(' + x.toFixed(1) + 'px,' + (-y).toFixed(1) + 'px)';
            if (bubble.classList.contains('show')) {
                const half = bubble.offsetWidth / 2, c = x + W / 2;
                const cc = Math.min(Math.max(c, half + 8), window.innerWidth - half - 8);
                bubble.style.setProperty('--bs', (cc - c).toFixed(0) + 'px');
            }
            raf = requestAnimationFrame(tick);
        }
        function hide(v){
            el.classList.toggle('hidden', v); pick.classList.toggle('off', v);
            toggleBtn.textContent = v ? 'แสดงตัวการ์ตูน' : 'ซ่อนตัวการ์ตูน';
            try { v ? localStorage.setItem(KEY, '1') : localStorage.removeItem(KEY); } catch (e) {}
            if (v) { cancelAnimationFrame(raf); raf = null; } else if (!raf) { last = performance.now(); raf = requestAnimationFrame(tick); }
        }
        let sx = 0, sy = 0, gx = 0, gy = 0, pid = null;
        el.addEventListener('pointerdown', e => {
            if (e.button !== 0) return;
            const r = el.getBoundingClientRect();
            sx = e.clientX; sy = e.clientY; gx = e.clientX - r.left; gy = e.clientY - r.top; pid = e.pointerId;
            el.setPointerCapture(pid);
        });
        el.addEventListener('pointermove', e => {
            if (pid !== e.pointerId) return;
            if (!dragging && Math.hypot(e.clientX - sx, e.clientY - sy) < 6) return;
            if (!dragging) { dragging = true; setMode('held'); el.classList.add('held', 'dragging'); say(['ว้าย! จะพาไปไหนครับ 😳', 'ลอยได้ด้วย~ 🎈', 'อย่าทำผมหล่นนะ 🙏'][Math.floor(Math.random() * 3)], 1800); }
            const h = el.offsetHeight;
            x = Math.min(Math.max(e.clientX - gx, 0), window.innerWidth - W);
            y = Math.min(Math.max(window.innerHeight - (e.clientY - gy) - h - 4, 0), window.innerHeight - h - 4);
            const dx = e.movementX || 0; if (Math.abs(dx) > 1) dir = dx > 0 ? 1 : -1;
        });
        const endDrag = e => {
            if (pid !== e.pointerId) return;
            try { el.releasePointerCapture(pid); } catch (err) {}
            pid = null;
            if (!dragging) return;
            dragging = false; justDragged = true; setTimeout(() => { justDragged = false; }, 50);
            el.classList.remove('held', 'dragging');
            if (y > 2) { el.classList.add('falling'); vy = 0; setMode('fall'); } else setMode('idle', 900, true);
        };
        el.addEventListener('pointerup', endDrag);
        el.addEventListener('pointercancel', endDrag);

        let clickTimer = null;
        el.addEventListener('click', () => {
            if (justDragged || mode === 'held' || mode === 'fall') return;
            clearTimeout(clickTimer);
            clickTimer = setTimeout(() => {
                setMode('wave', 2500);
                if (api.lastReport && !api._tipNext) { say(api.lastReport, 5000); api._tipNext = true; }
                else { say(TIPS[Math.floor(Math.random() * TIPS.length)]); api._tipNext = false; }
            }, 220);
        });
        el.addEventListener('dblclick', () => { clearTimeout(clickTimer); hide(true); });
        showBtn.addEventListener('click', e => { e.stopPropagation(); pick.classList.toggle('open'); });
        grid.addEventListener('click', e => {
            const b = e.target.closest('.mp-item'); if (!b) return;
            const m = MASCOTS.find(x => x.id === b.dataset.id); if (m) applyChar(m, true);
            pick.classList.remove('open');
        });
        toggleBtn.addEventListener('click', () => {
            const willHide = !el.classList.contains('hidden');
            hide(willHide); pick.classList.remove('open');
            if (!willHide) { setMode('wave', 2000, true); say('กลับมาแล้วครับ 😄'); }
        });
        document.addEventListener('mousedown', e => { if (!pick.contains(e.target)) pick.classList.remove('open'); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') pick.classList.remove('open'); });
        let hidden = false; try { hidden = localStorage.getItem(KEY) === '1'; } catch (e) {}
        x = Math.max(8, window.innerWidth * 0.15);
        el.style.transform = 'translate(' + x + 'px,0px)';
        setMode('walk');
        applyChar(cur, false);
        hide(hidden);
        if (!hidden) setTimeout(() => { if (!bubble.classList.contains('show')) { setMode('wave', 2200); say('สวัสดีครับ' + (LOGIN_NAME ? ' คุณ' + LOGIN_NAME : '') + ' 👋'); } }, 300);
        function jump(){ el.classList.remove('jump'); void el.offsetWidth; el.classList.add('jump'); setTimeout(() => el.classList.remove('jump'), 1200); }
        const api = {
            say: say,
            reported: false, lastReport: '',
            report(rows){
                this.reported = true;
                const norm = v => String(v || '').trim().toLowerCase();
                const me = norm(LOGIN_NAME);
                const mine = (rows || []).filter(r => !r.is_checkedout && (IS_SALE || (me && norm(r.sale) === me)));
                const n = mine.length;
                const over = mine.filter(r => r.due_days !== null && r.due_days !== undefined && r.due_days < 0).length;
                const val = mine.reduce((s, r) => s + (Number(r.price) || 0), 0);
                const who = LOGIN_NAME ? 'คุณ' + LOGIN_NAME : 'คุณ';
                let msg;
                if (n > 0) {
                    msg = who + ' มีงานค้างบนชั้น ' + n.toLocaleString('th-TH') + ' รายการ'
                        + (CAN_SEE_PRICE && val ? ' มูลค่า ' + fmtBaht(val) + ' ฿' : '')
                        + (over ? ' · เลยกำหนด ' + over.toLocaleString('th-TH') + ' รายการ 😥' : ' ');
                } else {
                    msg = who + ' ไม่มีงานค้างบนชั้นครับ 🎉';
                }
                this.lastReport = msg; this._t = Date.now(); this._n = over;
                if (el.classList.contains('hidden')) return;
                if (n > 0 && over) { el.classList.add('sad'); setTimeout(() => el.classList.remove('sad'), 6000); setMode('idle', 4000); }
                else if (n > 0) { setMode('wave', 3000); }
                else { setMode('idle', 2000); jump(); }
                say(msg, 6500);
            },
            cheer(text){ if (el.classList.contains('hidden')) return; el.classList.remove('sad'); setMode('idle', 2000); jump(); say(text || 'เย้! เรียบร้อย 🎉'); },
            worryOnce(n){ const now = Date.now(); if (n === this._n && now - (this._t || 0) < 60000) return; if (now - (this._t || 0) < 60000) return;
                this._n = n; this._t = now; this.worry('มี ' + n.toLocaleString('th-TH') + ' รายการเลยกำหนดแล้วนะ 😥'); },
            worry(text){ if (el.classList.contains('hidden')) return; el.classList.add('sad'); setMode('idle', 3000); say(text, 4000); setTimeout(() => el.classList.remove('sad'), 4000); }
        };
        return api;
    })();
</script>
</body>
</html>
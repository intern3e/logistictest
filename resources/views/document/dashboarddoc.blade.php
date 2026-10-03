<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เอกสารชั่วคราว</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&family=Mali:wght@300;400;500;600;700&family=Chakra+Petch:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand:#111827; --brand-600:#111827; --brand-50:#f3f4f6; --brand-100:#e5e7eb;
            --mint:#f8fafc; --mint-ink:#475569; --mint-line:#e2e8f0;
            --pink:#fbd0f0; --pink-ink:#86198f;
            --ink-900:#0f172a; --ink-700:#334155; --ink-500:#64748b; --ink-300:#94a3b8;
            --ink-150:#e2e8f0; --ink-100:#f1f5f9; --ink-050:#f8fafc;
            --paper:#fff; --line:#e8edf3; --radius:8px;
            --shadow:0 1px 2px rgba(15,23,42,.04),0 4px 14px rgba(15,23,42,.05);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; font-size: clamp(12px, 0.3vw + 8px, 15px); }
        body {
            margin: 0; display: flex; flex-direction: column;
            font-family: 'Sarabun', 'Prompt', 'Mali', 'Chakra Petch', 'Segoe UI', sans-serif;
            background: var(--ink-050); color: var(--ink-900); line-height: 1.6;
        }
        a { color: var(--brand-600); text-decoration: none; font-weight: 500; }
        a:hover { text-decoration: underline; }
        svg.i { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex: none; }

        /* ===== TOP BAR (หัว + ตัวกรอง แถวเดียว) ===== */
        .topbar {
            display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
            background: var(--paper); border: 1px solid var(--line); border-radius: 18px;
            margin: 15px 15px 12px; padding: 12px 16px 12px 18px; box-shadow: var(--shadow);
        }
        .topbar h2 {
            margin: 0; flex: none; font-size: clamp(17px, .8vw + 8px, 21px); font-weight: 700; letter-spacing: -.3px;
            display: flex; align-items: center; gap: 10px; white-space: nowrap; cursor: pointer;
        }
        .topbar h2:hover { opacity: 0.8; }
        .topbar h2 .logo {
            width: 36px; height: 36px; border-radius: 11px; color: #fff; display: flex; align-items: center; justify-content: center;
            background: #111827; box-shadow: 0 4px 10px rgba(17,24,39,.25);
        }
        .topbar h2 .logo svg { width: 18px; height: 18px; }
        .vsep { width: 1px; height: 28px; background: var(--line); flex: none; }

        .tools { flex: 1 1 760px; display: flex; align-items: center; gap: 8px; min-width: 0; }
        .actions { flex: none; display: flex; align-items: center; gap: 8px; margin-left: auto; }

        /* ช่องกรอกทุกตัวสูงเท่ากัน */
        .fld {
            height: 40px; display: flex; align-items: center; gap: 8px; padding: 0 12px; margin: 0;
            background: var(--ink-050); border: 1px solid var(--line); border-radius: 12px; transition: .15s; min-width: 0;
        }
        .fld:hover { border-color: var(--ink-150); }
        .fld:focus-within { background: #fff; border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-50); }
        .fld > svg { color: var(--ink-300); }
        .fld input, .fld select {
            border: 0; outline: 0; background: transparent; font: inherit; font-size: .9rem; color: var(--ink-900);
            height: 100%; min-width: 0; padding: 0; font-family: 'Sarabun', 'Prompt', sans-serif;
        }
        .fld input::placeholder { color: var(--ink-300); }
        .fld input:disabled { color: var(--ink-300); }

        .q-box { flex: 1 1 280px; position: relative; }
        .q-box input { flex: 1; }
        .q-hint { flex: none; font-size: .72rem; font-weight: 600; color: var(--brand-600); background: var(--brand-50);
                  border-radius: 999px; padding: 2px 9px; white-space: nowrap; }
        .q-hint:empty { display: none; }
        .f-date { flex: none; }
        .f-date input { width: 128px; }
        .f-com { flex: 0 1 270px; }
        .f-com select { flex: 1; cursor: pointer; text-overflow: ellipsis; }
        /* ===== ดรอปดาวน์บริษัท (แบบสวย) ===== */
        .f-com { position: relative; cursor: pointer; user-select: none; }
        .f-com.cd-ready select { display: none; }
        .cd-btn { flex: 1; min-width: 0; height: 100%; border: 0; background: transparent; padding: 0; text-align: left; font: inherit; font-size: .9rem;
                  color: var(--ink-900); cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; outline: 0; }
        .f-com:not(.cd-ready) .cd-btn, .f-com:not(.cd-ready) .cd-caret { display: none; }
        .cd-caret { flex: none; display: inline-flex; color: var(--ink-500); }
        .cd-caret svg { width: 17px; height: 17px; transition: transform .15s; }
        .f-com.open .cd-caret svg { transform: rotate(180deg); }
        .f-com.open { background: #fff; border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-50); }
        .cd-panel { display: none; position: absolute; left: 0; top: calc(100% + 6px); min-width: 100%; width: max-content; max-width: min(440px, 92vw); z-index: 300;
                    background: #fff; border: 1px solid var(--ink-150); border-radius: 12px; box-shadow: 0 12px 32px rgba(15,23,42,.16); padding: 6px; cursor: default; }
        .f-com.open .cd-panel { display: block; }
        .cd-search { display: flex; align-items: center; gap: 8px; height: 38px; padding: 0 10px; margin-bottom: 4px; border: 1px solid var(--line); border-radius: 8px; background: var(--ink-050); color: var(--ink-300); }
        .cd-search:focus-within { background: #fff; border-color: var(--ink-300); }
        .cd-search input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; font: inherit; font-size: .88rem; color: var(--ink-900); }
        .cd-list { max-height: min(360px, 55vh); overflow-y: auto; }
        .cd-group { padding: 8px 10px 4px; font-size: .74rem; font-weight: 700; color: var(--ink-300); letter-spacing: .2px; display: flex; align-items: center; gap: 6px; }
        .cd-group svg { width: 13px; height: 13px; }
        .cd-item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 8px; font-size: .9rem; color: var(--ink-900); cursor: pointer; white-space: nowrap; }
        .cd-item:hover, .cd-item.hl { background: var(--ink-100); }
        .cd-item .ck { width: 15px; height: 15px; visibility: hidden; color: var(--ink-900); }
        .cd-item.sel { font-weight: 600; background: var(--brand-50); }
        .cd-item.sel .ck { visibility: visible; }
        .cd-item.all { color: var(--ink-700); }
        .cd-sep { height: 1px; background: var(--line); margin: 4px 6px; }
        .cd-empty { padding: 14px; text-align: center; color: var(--ink-500); font-size: .88rem; }

        .chip {
            height: 40px; flex: none; display: inline-flex; align-items: center; gap: 7px; padding: 0 12px;
            border: 1px solid var(--line); border-radius: 12px; background: var(--paper); color: var(--ink-700);
            font-size: .88rem; cursor: pointer; user-select: none; transition: .15s; white-space: nowrap;
            font-family: 'Sarabun', 'Prompt', sans-serif;
        }
        .chip input { width: 15px; height: 15px; margin: 0; accent-color: var(--brand); cursor: pointer; }
        .chip:hover { background: var(--ink-050); }
        .chip:has(input:checked) { background: var(--brand-50); border-color: var(--brand-100); color: var(--brand-600); font-weight: 500; }

        .icon-btn {
            width: 40px; height: 40px; flex: none; display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--line); border-radius: 12px; background: var(--paper); color: var(--ink-500);
            cursor: pointer; transition: .15s; padding: 0;
        }
        .icon-btn svg { width: 17px; height: 17px; }
        .icon-btn:hover { text-decoration: none; }
        .icon-btn.reset:hover   { color: #dc2626; border-color: #fecaca; background: #fef2f2; }

        .user {
            height: 40px; display: inline-flex; align-items: center; gap: 8px; padding: 0 12px 0 5px;
            border-radius: 999px; background: var(--ink-050); border: 1px solid var(--line); font-size: .86rem; color: var(--ink-700); white-space: nowrap;
            font-family: 'Sarabun', 'Prompt', sans-serif;
        }
        .user .av {
            width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: var(--brand-100); color: var(--brand-600);
        }
        .user .av svg { width: 15px; height: 15px; }
        .btn {
            height: 40px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px; border-radius: 12px;
            font-weight: 600; font-size: .9rem; border: 1px solid transparent; transition: .15s; white-space: nowrap;
            font-family: 'Sarabun', 'Prompt', sans-serif;
        }
        .btn:hover { text-decoration: none; }
        .btn-solid { background: var(--brand); color: #fff; border-color: var(--brand); box-shadow: 0 2px 8px rgba(17,24,39,.25); }
        .btn-solid:hover { background: var(--brand-600); }

        @media (max-width: 1500px) {
            .tools { order: 3; flex-basis: 100%; }
            .vsep { display: none; }
        }
        @media (max-width: 760px) {
            .tools { flex-wrap: wrap; }
            .q-box { flex-basis: 100%; }
            .f-com { flex: 1 1 200px; }
            .user span.name { display: none; }
        }

        /* ===== SPLIT VIEW ===== */
        .split { flex: 1; min-height: 0; display: grid; grid-template-columns: minmax(320px, 35fr) 65fr; gap: 12px; margin: 0 15px 15px; }

        .list { background: var(--paper); border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow); display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
        .list-head { padding: 12px 16px; border-bottom: 1px solid var(--line); }
        .list-head .count { font-size: .85rem; color: var(--ink-500); font-family: 'Sarabun', 'Prompt', sans-serif; }
        .list-head .count b { color: var(--ink-900); }
        .list-body { overflow-y: auto; flex: 1; padding: 6px 8px 8px; }

        .it { display: flex; gap: 12px; align-items: center; padding: 10px 12px; margin: 2px 0; border: 1px solid transparent; border-radius: 12px; cursor: pointer; transition: background .12s; outline: none; }
        .it:hover { background: var(--ink-050); }
        .it.on { background: var(--brand-50); border-color: var(--brand-100); box-shadow: inset 3px 0 0 var(--brand), 0 1px 3px rgba(17,24,39,.06); }
        .it.on .mid b { color: var(--brand-600); }
        .it:focus-visible { box-shadow: inset 0 0 0 2px var(--brand); }
        .it .no { width: 22px; font-size: .75rem; color: var(--ink-300); text-align: right; flex: none; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dot { width: 10px; height: 10px; border-radius: 50%; flex: none; background: var(--ink-300); }
        .it .mid { min-width: 0; flex: 1; }
        .it .mid b { display: flex; align-items: center; gap: 6px; font-weight: 600; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .it .mid small { display: block; color: var(--ink-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .it .right { text-align: right; font-size: .78rem; color: var(--ink-500); flex: none; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .it .right span { display: block; }
        .it .right .badge { display: inline-flex; margin-top: 4px; font-size: .74rem; padding: 2px 10px; animation: none; }
        .flag-tag { font-size: .68rem; font-weight: 600; background: #f5f3ff; color: #7c3aed; border-radius: 6px; padding: 0 6px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .list-empty { padding: 40px 16px; text-align: center; color: var(--ink-500); font-family: 'Sarabun', 'Prompt', sans-serif; }

        .det { background: var(--paper); border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow); padding: 26px 30px; overflow-y: auto; min-height: 0; }
        .det-empty { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--ink-300); gap: 10px; text-align: center; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .det-empty svg { width: 48px; height: 48px; stroke-width: 1.4; }
        .det-top { display: flex; align-items: center; gap: 12px 18px; flex-wrap: wrap; margin-bottom: 18px; }
        .det-titles { flex: none; }
        .det-top #det-dlv { flex: 1 1 380px; min-width: 0; margin: 0; }
        /* ===== แถวสถานะจ่ายงาน (หัวเอกสาร) ===== */
        .dlv-card { background: #fff !important; border: 1px solid var(--line) !important; border-left: 1px solid var(--line) !important; border-radius: 14px !important; padding: 8px 10px 8px 12px !important; }
        .dlv-card.empty { background: #fffbeb !important; border-color: #fde68a !important; }
        .dlv-card.err { background: #fef2f2 !important; border-color: #fecaca !important; }
        .dlv-card .row1 { flex-wrap: nowrap; gap: 12px; }
        .dlv-card .row1 .info1 { display: flex; align-items: center; gap: 14px; overflow: hidden; white-space: nowrap; }
        .dlv-meta { display: inline-flex; align-items: center; gap: 6px; color: var(--ink-500); font-size: .86rem; min-width: 0; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-meta svg { width: 14px; height: 14px; color: var(--ink-300); flex: none; }
        .dlv-meta b { color: var(--ink-900); font-weight: 600; overflow: hidden; text-overflow: ellipsis; }
        .dlv-meta .muted { color: var(--ink-500); font-weight: 400; overflow: hidden; text-overflow: ellipsis; }
        .dlv-card .hist-btn { flex: none; display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 12px; border: 1px solid var(--line); border-radius: 10px;
                              background: #fff; color: var(--ink-700); font-size: .84rem; font-weight: 500; text-decoration: none !important; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-card .hist-btn:hover { background: var(--brand-50); border-color: var(--brand-100); color: var(--brand-600); }
        .dlv-card .hist-btn .n { background: var(--ink-100); color: var(--ink-500); border-radius: 999px; padding: 0 7px; font-size: .74rem; line-height: 18px; }
        .badge.st { gap: 6px; padding: 4px 11px; font-size: .78rem; flex: none; animation: none; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .badge.st::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
        .dlv-gray { color: #475569; background: #f1f5f9; }

        /* ===== ไทม์ไลน์ในป๊อปอัพ ===== */
        .hs-top { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 12px 14px; background: var(--ink-050); border: 1px solid var(--line); border-radius: 12px; margin-bottom: 18px; font-size: .9rem; color: var(--ink-700); font-family: 'Sarabun', 'Prompt', sans-serif; }
        .hs-top .sp { margin-left: auto; color: var(--ink-500); font-size: .82rem; }
        .tl { position: relative; padding-left: 26px; }
        .tl::before { content: ""; position: absolute; left: 8px; top: 6px; bottom: 6px; width: 2px; background: var(--line); border-radius: 2px; }
        .tl-item { position: relative; margin-bottom: 14px; }
        .tl-item:last-child { margin-bottom: 0; }
        .tl-dot { position: absolute; left: -26px; top: 12px; width: 18px; height: 18px; border-radius: 50%; background: #fff; border: 4px solid var(--dot, #94a3b8); }
        .tl-card { border: 1px solid var(--line); border-radius: 12px; background: #fff; overflow: hidden; }
        .tl-card.past { background: #fcfcfd; }
        .tl-head { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-bottom: 1px solid var(--line); }
        .tl-head b { font-size: .92rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .tl-head .tag { font-size: .72rem; font-weight: 600; color: var(--brand-600); background: var(--brand-50); border-radius: 999px; padding: 1px 8px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .tl-head .tag.old { color: var(--ink-500); background: var(--ink-100); }
        .tl-head .badge { margin-left: auto; }
        .kv2 { display: grid; grid-template-columns: 104px 1fr; gap: 6px 12px; padding: 12px 14px; font-size: .88rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .kv2 dt { color: var(--ink-500); }
        .kv2 dd { margin: 0; color: var(--ink-900); overflow-wrap: anywhere; }
        .kv2 dd small { color: var(--ink-500); }
        .kv2 dd.warn { color: #b91c1c; }

        /* ป๊อปอัพ */
        .modal { position: fixed; inset: 0; z-index: 1000; background: rgba(15,23,42,.55); display: flex; align-items: center; justify-content: center; padding: 20px; }
        .modal[hidden] { display: none; }
        .modal-box { background: #fff; width: 100%; max-width: 860px; max-height: 86vh; border-radius: 14px; box-shadow: 0 24px 60px rgba(0,0,0,.25);
                     display: flex; flex-direction: column; overflow: hidden; animation: modal-in .15s ease-out; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 20px 28px; border-bottom: 1px solid var(--line); }
        .modal-head h3 { margin: 0; font-size: 1.35rem; font-weight: 600; color: var(--ink-900); font-family: 'Sarabun', 'Prompt', sans-serif; }
        .modal-x { border: 0; background: none; width: 40px; height: 40px; border-radius: 10px; font-size: 28px; line-height: 1; color: var(--ink-500); cursor: pointer; }
        .modal-x:hover { background: var(--ink-100); color: var(--ink-900); }
        .modal-body { padding: 22px 28px 28px; overflow-y: auto; }
        /* ขยายตัวอักษรในป๊อปอัพ */
        .modal-body .hs-top { font-size: 1.08rem; padding: 16px 18px; }
        .modal-body .hs-top .sp { font-size: .98rem; }
        .modal-body .tl-head { padding: 13px 18px; }
        .modal-body .tl-head b { font-size: 1.12rem; }
        .modal-body .tl-head .tag { font-size: .86rem; padding: 2px 10px; }
        .modal-body .kv2 { grid-template-columns: 130px 1fr; gap: 10px 16px; padding: 16px 18px; font-size: 1.06rem; }
        .modal-body .badge.st { font-size: .92rem; padding: 5px 13px; }
        .modal-body .tl { padding-left: 32px; }
        .modal-body .tl::before { left: 10px; }
        .modal-body .tl-dot { left: -32px; top: 15px; width: 22px; height: 22px; border-width: 5px; }
        .modal-body .tl-item { margin-bottom: 18px; }
        .modal-body .kv2 dd small, .modal-body .kv2 dd { line-height: 1.6; }
        .modal-body .kv2 dd small { font-size: .95rem; }
        @keyframes modal-in { from { opacity: 0; transform: translateY(8px) scale(.98); } to { opacity: 1; transform: none; } }
        .det-top h3 { margin: 0; font-size: 1.9rem; font-weight: 700; letter-spacing: -.6px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .det-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; flex: none; }
        .det-sub { display: flex; flex-wrap: wrap; gap: 6px 14px; align-items: center; color: var(--ink-500); margin: 4px 0 0; font-size: .92rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        /* ข้อมูลเอกสาร: รายการ หัวข้อ / ค่า แบบเรียบ */
        .kv { border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
        .kv-row { display: grid; grid-template-columns: 130px 1fr; gap: 12px; padding: 10px 16px; border-top: 1px solid var(--line); }
        .kv-row:first-child { border-top: 0; }
        .kv-row dt { color: var(--ink-500); font-size: .88rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .kv-row dd { margin: 0; font-weight: 500; overflow-wrap: anywhere; white-space: pre-wrap; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .kv-row dd small { color: var(--ink-500); font-weight: 400; }

        /* สรุปสถานะจ่ายงาน (ด้านบน) */
        .dlv-card { border: 1px solid var(--line); border-left: 4px solid var(--c, #94a3b8); background: var(--bgc, #f8fafc); padding: 10px 14px !important;
                    border-radius: 14px; padding: 14px 16px; margin-bottom: 16px; }
        .dlv-card .t { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: .95rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-card .t .lbl { font-weight: 700; }
        .dlv-card .t .rounds { margin-left: auto; color: var(--ink-500); font-size: .82rem; }
        .dlv-card .m { margin-top: 6px; color: var(--ink-700); font-size: .9rem; line-height: 1.8; }
        .dlv-card .m b { color: var(--ink-900); }
        .dlv-card .m .sep { color: var(--ink-300); margin: 0 6px; }
        /* แถวเดียว: ป้ายสถานะ · รายละเอียด ······ รอบ · ดูประวัติ */
        .dlv-card .row1 { display: flex; align-items: center; gap: 6px 12px; flex-wrap: wrap; font-size: .9rem; color: var(--ink-700); font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-card .row1 .info1 { flex: 1; min-width: 0; }
        .dlv-card .row1 .info1 b { color: var(--ink-900); }
        .dlv-card .row1 .sep { color: var(--ink-300); margin: 0 6px; }
        .dlv-card .row1 .rounds { color: var(--ink-500); font-size: .8rem; white-space: nowrap; }
        .dlv-card .hist-btn { border: 0; background: none; padding: 0; font: inherit; font-size: .84rem; font-weight: 500; color: var(--brand-600); cursor: pointer; white-space: nowrap; }
        .dlv-card .hist-btn:hover { text-decoration: underline; }
        .dlv-card .hist-body { margin-top: 12px; }
        .dlv-card details { margin-top: 8px; }
        .dlv-card summary { cursor: pointer; color: var(--brand-600); font-size: .86rem; font-weight: 500; list-style: none; display: inline-flex; align-items: center; gap: 4px; }
        .dlv-card summary::-webkit-details-marker { display: none; }
        .dlv-card summary::before { content: "▸"; transition: transform .15s; display: inline-block; }
        .dlv-card details[open] summary::before { transform: rotate(90deg); }
        .dlv-card details > div { margin-top: 10px; }
        .dlv-card.empty { --c: #f59e0b; --bgc: #fffbeb; color: #92400e; }
        .dlv-card.err { --c: #ef4444; --bgc: #fef2f2; color: #991b1b; }

        /* ===== แผ่นเอกสาร (รูปแบบเดียวกับไฟล์ PDF ใบส่งของชั่วคราว) ===== */
        .paper-wrap { background: var(--ink-100); border-radius: 14px; padding: 22px; }
        .paper {
            background: #fff; max-width: 900px; margin: 0 auto; padding: 34px 40px 26px;
            border: 1px solid #e2e8f0; border-radius: 4px;
            box-shadow: 0 1px 2px rgba(15,23,42,.06), 0 10px 30px rgba(15,23,42,.08);
            font-family: 'Sarabun', 'Prompt', 'Mali', sans-serif; color: #1e293b; font-size: 15px;
            display: flex; flex-direction: column; min-height: 1100px;
        }
        .pd-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 10px; padding-bottom: 6px; }
        .pd-head h1 { margin: 0; font-size: 26px; font-weight: 800; color: #1e293b; letter-spacing: .01em; line-height: 1.25; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-head p { margin: 4px 0 0; font-size: 17px; color: #64748b; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-head p b { font-weight: 600; color: #334155; }
        .pd-box { border: 1.5px solid #1e293b; border-radius: 6px; min-width: 190px; background: #fff; font-size: 14px; color: #1e293b; overflow: hidden; flex: none; }
        .pd-box div { padding: 6px 12px; display: flex; align-items: center; }
        .pd-box div + div { border-top: 1px solid #cbd5e1; }
        .pd-box .k { font-weight: 700; color: #64748b; width: 46px; }
        .pd-box .c { font-weight: 700; color: #64748b; padding-right: 6px; }
        .pd-box .v { font-weight: 800; }
        .pd-box div + div .v { font-weight: 600; }
        .pd-title { border: 1.5px solid #1e293b; border-radius: 6px; padding: 6px 14px; text-align: center; margin: 0 0 8px; }
        .pd-title h2 { margin: 0; font-size: 21px; font-weight: 700; color: #1e293b; letter-spacing: .01em; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-info { padding: 10px 0; font-size: 15px; line-height: 1.8; color: #1e293b; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-info > div { display: flex; align-items: flex-start; }
        .pd-info .l { font-weight: 700; color: #475569; width: 78px; flex-shrink: 0; }
        .pd-info .c { font-weight: 700; color: #475569; padding-right: 8px; }
        .pd-info .v { flex: 1; min-width: 0; overflow-wrap: anywhere; white-space: pre-wrap; }
        .pd-info .tel { display: inline-block; margin-left: 60px; white-space: nowrap; }
        .pd-info .tel b { font-weight: 700; color: #475569; }
        .pd-table { width: 100%; border-collapse: collapse; font-size: 13.5px; margin: 12px 0 20px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-table th { border: 1px solid #94a3b8; padding: 7px; background: #fff; color: #1e293b; font-weight: 700; font-size: 14.5px; text-align: center; }
        .pd-table td { border: 1px solid #94a3b8; padding: 7px 10px; color: #1e293b; }
        .pd-table .n, .pd-table .q { text-align: center; }
        .pd-table .n { width: 8%; } .pd-table .q { width: 18%; }
        .pd-foot { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; padding-top: 30px; }
        .pd-sign { text-align: center; width: 260px; }
        .pd-sign .line { border-bottom: 1px solid #1e293b; height: 40px; }
        .pd-sign p { margin: 8px 0 0; font-size: 12.5px; font-weight: 700; color: #334155; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-sign .dt { display: flex; align-items: baseline; justify-content: center; gap: 4px; margin-top: 6px; font-size: 11px; color: #475569; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .pd-sign .dt i { display: inline-block; border-bottom: 1px solid #94a3b8; width: 56px; height: 11px; }
        .pd-sign .dt s { text-decoration: none; color: #94a3b8; }
        .pd-page { font-size: 11px; color: #64748b; padding-bottom: 26px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .p-items, .p-note, .p-sign { display: none; }

        @media (max-width: 1100px) { .paper { padding: 24px 20px; min-height: 0; } .paper-wrap { padding: 10px; } .pd-info .tel { margin-left: 24px; } }


        .info { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 6px; }
        .info > div { background: var(--paper); border: 1px solid var(--line); border-radius: 14px; padding: 12px 14px; min-width: 0; overflow-wrap: anywhere; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 1px 2px rgba(15,23,42,.03); }
        .info .tile { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex: none; }
        .info .tile svg { width: 18px; height: 18px; }
        .t-blue{background:#eff6ff;color:#2563eb}.t-green{background:#ecfdf3;color:#16a34a}.t-purple{background:#f5f3ff;color:#7c3aed}.t-orange{background:#fff7ed;color:#ea580c}.t-slate{background:#f1f5f9;color:#475569}
        .info small { display: block; color: var(--ink-500); font-size: .78rem; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .info .v { font-weight: 500; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .info .wide { grid-column: span 2; }
        .det h4 { margin: 20px 0 10px; font-size: 1.05rem; font-weight: 700; display: flex; align-items: center; gap: 8px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .det h4 small { font-weight: 400; color: var(--ink-500); }
        .items { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .items th { background: var(--mint); color: var(--mint-ink); text-align: left; padding: 10px 14px; font-weight: 600; font-size: .85rem; border-bottom: 1px solid var(--mint-line); }
        .items td { padding: 10px 14px; border-top: 1px solid var(--line); }
        .items tr:hover td { background: var(--ink-050); }
        .items td:last-child { font-weight: 600; }
        .items th:last-child, .items td:last-child { text-align: right; width: 110px; }
        .notes { white-space: pre-wrap; background: var(--ink-050); border: 1px solid var(--line); border-radius: 14px; padding: 12px 14px; color: var(--ink-700); min-height: 44px; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .loading { color: var(--ink-300); padding: 10px 0; font-family: 'Sarabun', 'Prompt', sans-serif; }

        .pdf-btn, .pdf-btn-disabled { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 12px; }
        .print-btn { display: inline-flex; align-items: center; gap: 6px; height: 38px; padding: 0 14px; border-radius: 12px; cursor: pointer;
                     border: 1px solid var(--brand-100); background: var(--brand-50); color: var(--brand-600); font: inherit; font-size: .88rem; font-weight: 600; transition: .15s; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .print-btn:hover:not(:disabled) { background: var(--brand); border-color: var(--brand); color: #fff; }
        .print-btn:disabled { border: 1.5px dashed var(--ink-300); background: var(--ink-050); color: var(--ink-300); cursor: not-allowed; }
        .print-btn.loading { border-style: solid; border-color: var(--brand-100); background: var(--brand-50); color: var(--brand-600); cursor: progress; }
        .pdf-btn { border: 1px solid var(--brand-100); background: var(--brand-50); color: var(--brand-600); transition: .15s; }
        .pdf-btn:hover { background: var(--brand); color: #fff; text-decoration: none; }
        .pdf-btn-disabled { border: 1.5px dashed var(--ink-300); background: var(--ink-050); color: var(--ink-300); cursor: not-allowed; }
        .btn-edit { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 12px; height: 38px; background: var(--paper); color: var(--ink-900); border: 1px solid var(--ink-150); font-size: .88rem; transition: .15s; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .btn-edit:hover { background: var(--brand-50); color: var(--brand-600); border-color: var(--brand); text-decoration: none; }

        /* สถานะจ่ายงาน (สีตามผลจริง) */
        .badge { display: inline-flex; align-items: center; padding: 3px 11px; border-radius: 999px; font-weight: 600; font-size: .78rem; white-space: nowrap; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-none  { color:#dc2626; background:#fee2e2; }   .dot.dlv-none  { background:#ef4444; }
        .dlv-ok    { color:#166534; background:#d4f2e2; }   .dot.dlv-ok    { background:#22c55e; }
        .dlv-hold  { color:#a16207; background:#fef3c7; }   .dot.dlv-hold  { background:#f59e0b; }
        .dlv-wrong { color:#be185d; background:#fce7f3; }   .dot.dlv-wrong { background:#ec4899; }
        .dlv-redo  { color:#1d4ed8; background:#dbeafe; }   .dot.dlv-redo  { background:#3b82f6; }
        .dlv-wait  { color:#6d28d9; background:#ede9fe; }   .dot.dlv-wait  { background:#8b5cf6; }
        .badge.dlv-none { animation: pulse-glow 2s infinite; }
        .dlv-inline { margin-top: 8px; font-size: .85rem; padding: 6px 10px; border-radius: 8px; display: inline-block; font-family: 'Sarabun', 'Prompt', sans-serif; }
        .dlv-inline-note { color: #6b7280; }
        .no-delivery { text-align: center; padding: 26px 16px; background: #fff8e6; border: 2px dashed #fbbf24; border-radius: 12px; color: #92400e; font-weight: 500; font-family: 'Sarabun', 'Prompt', sans-serif; }
        @keyframes pulse-glow { 0%{box-shadow:0 0 0 0 rgba(220,38,38,.35)} 70%{box-shadow:0 0 0 6px rgba(220,38,38,0)} 100%{box-shadow:0 0 0 0 rgba(220,38,38,0)} }

        @media (max-width: 900px) {
            body { height: auto; }
            .split { grid-template-columns: 1fr; }
            .list-body { max-height: 45vh; }
            .info { grid-template-columns: 1fr 1fr; }
            .info .wide { grid-column: span 2; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h2 onclick="window.location.reload()" title="คลิกเพื่อรีเฟรชหน้าเว็บ"><span class="logo"><svg class="i" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/></svg></span>เอกสารชั่วคราว</h2>
        <span class="vsep"></span>

        <div class="tools">
            <form method="GET" action="{{ route('document.dashboarddoc') }}" class="fld f-date" id="autoSearchForm" title="วันที่">
                @php $allDates = request('date') === 'all'; @endphp
                <svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <input type="date" id="date" name="date"
                       value="{{ $allDates ? '' : request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                       {{ $allDates ? 'disabled' : '' }}>
                <button type="submit" style="display: none;">ค้นหา</button>
            </form>

            <label class="chip"><input type="checkbox" id="allDates" {{ $allDates ? 'checked' : '' }}>ทั้งหมด</label>

            <div class="fld f-com" id="comBox" title="บริษัท">
                <svg class="i" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/></svg>
                <select id="headcom" name="headcom" form="autoSearchForm" onchange="if(window.submitFilters){submitFilters()}else{document.getElementById('autoSearchForm').submit()}">
                    <option value="">ทุกบริษัท</option>
                    <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
                    <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
                    <option value="บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด">บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด</option>
                    <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
                    <option value="บริษัท ฮิคาริ เดงกิ จำกัด">บริษัท ฮิคาริ เดงกิ จำกัด</option>
                    <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
                    <option value="บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด">บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด</option>
                    <option value="บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด">บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด</option>
                    <option value="บริษัท ชาเวสต์ เรียลเอสเตท จำกัด">บริษัท ชาเวสต์ เรียลเอสเตท จำกัด</option>
                    <option value="บริษัท เทคเพียร์ เอ็นจิเนียริ่ง จำกัด">บริษัท เทคเพียร์ เอ็นจิเนียริ่ง จำกัด</option>
                    <option value="บริษัท เดชา อิเล็คทริค แอนด์ คอนสตรัคชั่น จำกัด">บริษัท เดชา อิเล็คทริค แอนด์ คอนสตรัคชั่น จำกัด</option>
                </select>
                {{-- ดรอปดาวน์แบบสวย: แสดงแทน select ด้านบน (select ยังเป็นตัวเก็บค่า/ส่งค่าเหมือนเดิม) --}}
                <button type="button" class="cd-btn" id="comBtn" aria-haspopup="listbox"><span id="comText">ทุกบริษัท</span></button>
                <span class="cd-caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                <div class="cd-panel" id="comPanel" role="listbox">
                    <div class="cd-search"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" id="comFilter" placeholder="พิมพ์ชื่อบริษัท..." autocomplete="off"></div>
                    <div class="cd-list" id="comList"></div>
                </div>
            </div>

            {{-- ช่องค้นหาเดียว: ระบบเดาให้ว่าเป็น เลขเอกสาร / SO / ชื่อลูกค้า แล้วส่งเป็น search / so / com ตามเดิม --}}
            <div class="fld q-box">
                <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <input type="text" id="q-input" value="{{ request('search') ?: (request('so') ?: request('com', '')) }}" autocomplete="off"
                       placeholder="ค้นหา เลขเอกสาร / SO / ชื่อลูกค้า" title="พิมพ์เลขเอกสาร (เช่น SP6909-0025), เลข SO (เช่น 69/016727) หรือชื่อลูกค้า แล้วกด Enter">
                <span class="q-hint" id="q-hint"></span>
            </div>

            <button type="button" id="clear-filters-btn" onclick="clearFilters()" class="icon-btn reset" title="ล้าง filter">
                <svg class="i" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
            </button>
        </div>

        <div class="actions">
            <span class="user" title="ผู้ใช้"><span class="av"><svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="name">{{ $creator }}</span></span>
            <a href="{{ route('document.insertdoc') }}" class="btn btn-solid"><svg class="i" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>สร้างเอกสาร</a>
            @csrf
        </div>
    </div>

    <!-- ===== รายการซ้าย + รายละเอียดขวา ===== -->
    <div class="split">
        <div class="list">
            <div class="list-head">
                <div class="count">ทั้งหมด <b>{{ count($docbill) }}</b> รายการ <span id="list-shown"></span></div>
            </div>
            <div class="list-body" id="table-body">
                @foreach($docbill as $item)
                @php
                    $pdfPath = "temporary_bill/{$item->doc_id}.pdf";
                    $hasPdf = \Storage::exists('public/' . $pdfPath) || $item->statuspdf == 1;
                    $st = $item->dlv_status ?? '';
                    if (!$item->has_delivery)              { $dlvCls = 'dlv-none';   $dlvTxt = 'ยังไม่จ่ายงาน'; }
                    elseif ($st === 'จัดส่งสำเร็จ')          { $dlvCls = 'dlv-ok';     $dlvTxt = 'จัดส่งสำเร็จ'; }
                    elseif ($st === 'ค้างบิล')              { $dlvCls = 'dlv-hold';   $dlvTxt = 'ค้างบิล'; }
                    elseif ($st === 'สินค้าผิด')            { $dlvCls = 'dlv-wrong';  $dlvTxt = 'สินค้าผิด'; }
                    elseif ($st === 'ส่งใหม่')              { $dlvCls = 'dlv-redo';   $dlvTxt = 'สั่งส่งใหม่'; }
                    else                                    { $dlvCls = 'dlv-wait';   $dlvTxt = 'จ่ายงานแล้ว · รอผล'; }
                    $soNo = $item->so_id ? preg_replace('/^SO\s*/i', '', trim($item->so_id)) : '-';
                @endphp
                <div class="it" tabindex="0"
                     data-doc="{{ $item->doc_id }}"
                     data-so="{{ $soNo }}"
                     data-headcom="{{ $item->headcom }}"
                     data-com="{{ $item->com_name }}"
                     data-address="{{ $item->com_address }}"
                     data-contact="{{ $item->contact_name }}"
                     data-tel="{{ $item->contact_tel }}"
                     data-doctype="{{ $item->doctype }}"
                     data-notes="{{ $item->notes }}"
                     data-emp="{{ $item->emp_name }}"
                     data-date="{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}"
                     data-pdf="{{ $hasPdf ? asset('storage/' . $pdfPath) : '' }}"
                     data-edit="{{ route('document.editdoc', $item->doc_id) }}"
                     data-drive="{{ $item->statusdeli == 1 ? 'https://drive.google.com/drive/u/0/search?q=' . $item->doc_id . '+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB' : '' }}"
                     data-dlv-cls="{{ $dlvCls }}"
                     data-dlv-txt="{{ $dlvTxt }}"
                     data-dlv-confirmed="{{ $item->dlv_confirmed ? 1 : 0 }}"
                     data-dlv-by="{{ $item->dlv_check_name ?: '-' }}"
                     data-dlv-time="{{ $item->dlv_check_time }}"
                     data-dlv-note="{{ $item->dlv_note }}">
                    <span class="no">{{ $loop->iteration }}</span>
                    <div class="mid">
                        <b>{{ $item->doc_id }} @if($item->statusdeli == 1)<span class="flag-tag">Drive</span>@endif</b>
                        <small>{{ $item->com_name }}</small>
                    </div>
                    <div class="right">
                        <span>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</span>
                        <span class="badge {{ $dlvCls }}">{{ $dlvTxt }}</span>
                    </div>
                </div>
                @endforeach
                <div class="list-empty" id="list-empty" style="{{ count($docbill) ? 'display:none' : '' }}">ไม่พบเอกสาร</div>
            </div>
        </div>

        <div class="det" id="detail">
            <div class="det-empty">
                <svg class="i" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/></svg>
                <div>เลือกเอกสารทางซ้ายเพื่อดูรายละเอียด</div>
                @if(isset($message))<div style="color:var(--ink-500)">{{ $message }}</div>@endif
            </div>
        </div>
    </div>

    <!-- ป๊อปอัพประวัติการจ่ายงาน -->
    <div class="modal" id="hist-modal" hidden onclick="if (event.target === this) closeDlvHistory()">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="hist-title">
            <div class="modal-head">
                <h3 id="hist-title">ประวัติการจ่ายงาน</h3>
                <button type="button" class="modal-x" id="hist-close" onclick="closeDlvHistory()" aria-label="ปิด">&times;</button>
            </div>
            <div class="modal-body" id="hist-body"></div>
        </div>
    </div>

    <script>
        const form = document.getElementById('autoSearchForm');
        const dateInput = document.getElementById('date');
        const headcomSel = document.getElementById('headcom');
        const qInputEl = document.getElementById('q-input');
        const qHintEl  = document.getElementById('q-hint');

        // เดาว่าคำค้นเป็นอะไร -> ส่งเป็นพารามิเตอร์เดิมของ Controller (search / so / com)
        function classifyQuery(v) {
            v = (v || '').trim();
            if (!v) return null;
            if (/^SO[\s:#-]*\d[\d\/\s]*$/i.test(v)) return { key: 'so', value: v.replace(/^SO[\s:#-]*/i, ''), label: 'เลข SO' };
            if (/^\d+\s*\/\s*\d+$/.test(v) || /^\d+$/.test(v)) return { key: 'so', value: v.replace(/\s+/g, ''), label: 'เลข SO' };
            if (/^[A-Za-z]{1,5}\s*-?\d[\d-]*$/.test(v)) return { key: 'search', value: v.replace(/\s+/g, '').toUpperCase(), label: 'เลขเอกสาร' };
            return { key: 'com', value: v, label: 'ชื่อลูกค้า' };
        }
        function updateHint() {
            const c = classifyQuery(qInputEl.value);
            qHintEl.textContent = c ? 'ค้นเป็น: ' + c.label : '';
        }
        if (qInputEl) { qInputEl.addEventListener('input', updateHint); updateHint(); }
        const allDatesChk = document.getElementById('allDates');

        // จำค่าบริษัทผู้ส่งที่เลือกไว้ (ค้นข้ามวัน)
        if (headcomSel) headcomSel.value = @json(request('headcom', ''));

        // บริษัทที่ใช้ล่าสุด (จำในเบราว์เซอร์นี้) ขึ้นไว้บนสุดของรายการ
        const RECENT_KEY = 'dashboarddoc_recent_headcom';
        function getRecentHeadcom() {
            try { return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch (e) { return []; }
        }
        function saveRecentHeadcom(v) {
            if (!v) return;
            const r = [v].concat(getRecentHeadcom().filter(x => x !== v)).slice(0, 3);
            try { localStorage.setItem(RECENT_KEY, JSON.stringify(r)); } catch (e) {}
        }
        function arrangeHeadcom() {
            if (!headcomSel) return;
            const cur  = headcomSel.value;
            const all  = headcomSel.querySelector('option[value=""]');
            const opts = Array.from(headcomSel.querySelectorAll('option')).filter(o => o.value);
            const rec  = getRecentHeadcom().filter(v => opts.some(o => o.value === v));
            headcomSel.innerHTML = '';
            if (all) headcomSel.appendChild(all);
            if (rec.length) {
                const g1 = document.createElement('optgroup'); g1.label = 'ใช้ล่าสุด';
                rec.forEach(v => g1.appendChild(opts.find(o => o.value === v)));
                const g2 = document.createElement('optgroup'); g2.label = 'บริษัททั้งหมด';
                opts.filter(o => rec.indexOf(o.value) === -1).forEach(o => g2.appendChild(o));
                headcomSel.appendChild(g1); headcomSel.appendChild(g2);
            } else {
                opts.forEach(o => headcomSel.appendChild(o));
            }
            headcomSel.value = cur;
        }
        if (headcomSel) { saveRecentHeadcom(headcomSel.value); arrangeHeadcom(); }

        // ===== ดรอปดาวน์บริษัทแบบสวย (อ่านตัวเลือก/กลุ่มจาก select เดิม แล้วเลือกค่าเข้า select) =====
        (function () {
            const box = document.getElementById('comBox'), btn = document.getElementById('comBtn'), txt = document.getElementById('comText');
            const list = document.getElementById('comList'), filter = document.getElementById('comFilter');
            if (!box || !headcomSel || !btn) return;
            box.classList.add('cd-ready');
            const ICK = '<svg class="i ck" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
            const IC_CLOCK = '<svg class="i" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
            const IC_BLD = '<svg class="i" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/></svg>';
            const esc = s => String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
            let hl = -1;
            function item(o, cls) {
                return '<div class="cd-item' + (cls || '') + (o.value === headcomSel.value ? ' sel' : '') + '" role="option" data-val="' + esc(o.value) + '">' + ICK + esc(o.textContent) + '</div>';
            }
            function render() {
                const q = filter.value.trim().toLowerCase();
                const match = o => !q || o.textContent.toLowerCase().indexOf(q) !== -1;
                let html = '';
                const all = headcomSel.querySelector('option[value=""]');
                if (all && !q) html += item(all, ' all') + '<div class="cd-sep"></div>';
                const groups = headcomSel.querySelectorAll('optgroup');
                if (groups.length) {
                    groups.forEach((g, gi) => {
                        const opts = Array.from(g.querySelectorAll('option')).filter(match);
                        if (!opts.length) return;
                        html += '<div class="cd-group">' + (gi === 0 ? IC_CLOCK : IC_BLD) + esc(g.label) + '</div>' + opts.map(o => item(o)).join('');
                    });
                } else {
                    html += Array.from(headcomSel.querySelectorAll('option')).filter(o => o.value && match(o)).map(o => item(o)).join('');
                }
                list.innerHTML = html || '<div class="cd-empty">ไม่พบบริษัท</div>';
                hl = -1;
            }
            function sync() { const o = headcomSel.options[headcomSel.selectedIndex]; txt.textContent = o ? o.textContent : 'ทุกบริษัท'; }
            function open() { filter.value = ''; render(); box.classList.add('open'); setTimeout(() => filter.focus(), 0);
                              const s = list.querySelector('.sel'); if (s) s.scrollIntoView({ block: 'nearest' }); }
            function close() { box.classList.remove('open'); }
            function pick(v) { close(); if (headcomSel.value === v) return; headcomSel.value = v; sync(); headcomSel.dispatchEvent(new Event('change')); }
            box.addEventListener('mousedown', e => {
                if (e.target.closest('.cd-search')) return;
                const it = e.target.closest('.cd-item');
                e.preventDefault();
                if (it) { pick(it.dataset.val); return; }
                box.classList.contains('open') ? close() : open();
            });
            filter.addEventListener('input', render);
            const keys = e => {
                const items = Array.from(list.querySelectorAll('.cd-item'));
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!box.classList.contains('open')) { open(); return; }
                    hl = e.key === 'ArrowDown' ? Math.min(hl + 1, items.length - 1) : Math.max(hl - 1, 0);
                    items.forEach((it, i) => it.classList.toggle('hl', i === hl));
                    if (items[hl]) items[hl].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!box.classList.contains('open')) { open(); return; }
                    const it = items[hl] || (filter.value.trim() && items.length === 1 ? items[0] : null);
                    if (it) pick(it.dataset.val);
                } else if (e.key === 'Escape') { close(); btn.focus(); }
            };
            btn.addEventListener('keydown', keys);
            filter.addEventListener('keydown', keys);
            document.addEventListener('mousedown', e => { if (!box.contains(e.target)) close(); });
            sync();
        })();

        window.clearFilters = function clearFilters() {
            window.location.href = @json(route('document.dashboarddoc'));
        };
        window.submitFilters = function submitFilters() {
            const base = @json(route('document.dashboarddoc'));
            const p = new URLSearchParams();
            const allDates = allDatesChk ? allDatesChk.checked : false;
            const date    = dateInput ? dateInput.value : '';
            const headcom = headcomSel ? headcomSel.value : '';
            const q       = qInputEl ? classifyQuery(qInputEl.value) : null;
            const search  = q && q.key === 'search' ? q.value : '';
            const so      = q && q.key === 'so'     ? q.value : '';
            const com     = q && q.key === 'com'    ? q.value : '';
            if (allDates)     p.set('date', 'all');
            else if (date)    p.set('date', date);
            if (headcom) { p.set('headcom', headcom); saveRecentHeadcom(headcom); }
            if (search)  p.set('search', search);
            if (so)      p.set('so', so);
            if (com)     p.set('com', com);
            window.location.href = base + '?' + p.toString() + location.hash;
        };

        if (dateInput)   dateInput.addEventListener('change', () => submitFilters());
        if (headcomSel)  headcomSel.addEventListener('change', () => submitFilters());
        if (allDatesChk) allDatesChk.addEventListener('change', () => {
            if (dateInput) dateInput.disabled = allDatesChk.checked;
            submitFilters();
        });
        [qInputEl].forEach(el => {
            if (!el) return;
            el.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); submitFilters(); }
            });
        });

        window.addEventListener('load', () => {
            if (!sessionStorage.getItem('hasAutoSubmitted')) {
                sessionStorage.setItem('hasAutoSubmitted', 'true');
                form.submit();
            }
        });

        const DELIVERY_STATUS_URL = "{{ route('document.deliveryStatus') }}";

        function escHtml(s){ return String(s == null ? '' : s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

        function fmtDT(s){
            if(!s) return '-';
            const d = new Date(String(s).replace(' ','T'));
            if(isNaN(d)) return s;
            const p = n => String(n).padStart(2,'0');
            return p(d.getDate())+'/'+p(d.getMonth()+1)+'/'+(d.getFullYear()+543)+' '+p(d.getHours())+':'+p(d.getMinutes());
        }

    /* ===== ไทม์ไลน์รายละเอียดการจัดส่ง (โค้ดชุดเดียวกันในหน้า sale/dashboard, document/dashboarddoc, so/show) ===== */
    function dlvEsc(x){ return (x==null?'':String(x)).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]);}); }
    function dlvStyle(st){
        st = (st||'').toString().trim();
        if(st.indexOf('สำเร็จ')!==-1 && st.indexOf('ไม่')===-1) return {bg:'#e8f5e9',fg:'#1b5e20',border:'#2e7d32',txt:'สำเร็จ'};
        if(st.indexOf('สินค้าผิด')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'สินค้าผิด'};
        if(st.indexOf('ไม่สำเร็จ')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'ไม่สำเร็จ'};
        if(st.indexOf('ส่งใหม่')!==-1) return {bg:'#eaf0fc',fg:'#2853d5',border:'#2853d5',txt:'ส่งใหม่'};
        if(st.indexOf('ค้างบิล')!==-1) return {bg:'#fff4e5',fg:'#b45309',border:'#ed6c02',txt:'ค้างบิล'};
        if(st===''||st==='0') return {bg:'#f3f4f6',fg:'#6b7280',border:'#d1d5db',txt:'กำลังไปส่ง'};
        return {bg:'#f3f4f6',fg:'#374151',border:'#d1d5db',txt:st};
    }
    function dlvBadge(txt, s){ return '<span style="background:'+s.bg+';color:'+s.fg+';font-size:12px;font-weight:700;padding:2px 10px;border-radius:10px;white-space:nowrap;">'+dlvEsc(txt)+'</span>'; }
    function dlvParseNote(note){
        note = (note||'').toString().trim();
        if(!note) return {text:''};
        if(note.indexOf('ส่งใหม่ (ไม่สำเร็จ)') === 0){
            var m1 = note.match(/เหตุผล:\s*([^·]+)/), m2 = note.match(/จ่ายใหม่ให้\s*([^·]+?)(?:\s*·|$)/);
            return {auto:true, reason: m1 ? m1[1].trim() : '', reassign: m2 ? m2[1].trim() : '', text:''};
        }
        return {text: note};
    }
    function dlvLine(label, val){
        return '<div style="display:flex;gap:8px;align-items:baseline;">'
             + '<span style="flex:0 0 110px;color:#6b7280;white-space:nowrap;">'+label+'</span>'
             + '<span style="flex:1;min-width:0;">'+val+'</span></div>';
    }
    function dlvTimeline(rows){
        rows = rows || [];
        var total = rows.length, redoN = 0, active = [];
        rows.forEach(function(r){
            if((r.status||'').indexOf('ส่งใหม่') !== -1) redoN++;
            if(!r.cancelled_at) active.push(r);
        });
        var latest = active.length ? active[active.length-1] : null;

        var h = '<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13.5px;color:#374151;line-height:1.8;">'
              + 'จ่ายงานไปส่งทั้งหมด <b>'+total+'</b> รอบ' + (redoN ? ' · สั่งส่งใหม่ <b>'+redoN+'</b> ครั้ง' : '') + '<br>'
              + 'สถานะตอนนี้: ';
        if(latest){
            var ls = dlvStyle(latest.status);
            h += dlvBadge(ls.txt, ls) + ' · คนขับ <b>'+dlvEsc(latest.driver_name||'-')+'</b>'
               + (latest.transport_name ? ' ('+dlvEsc(latest.transport_name)+')' : '')
               + (latest.delivery_date ? ' · ไปส่งวันที่ <b>'+dlvEsc(latest.delivery_date)+'</b>' : '');
        } else {
            h += '<b style="color:#9a3412;">รอจ่ายงานใหม่</b> (ทุกรอบถูกส่งใหม่/ยกเลิกแล้ว — รอจ่ายที่หน้าจ่ายงานขนส่ง)';
        }
        h += '</div>';

        rows.forEach(function(r, i){
            var isHist = !!r.cancelled_at;
            var isRedo = (r.status||'').indexOf('ส่งใหม่') !== -1;
            var next   = rows[i+1];
            var kind = !isHist ? '' : (isRedo ? 'redo' : ((next && /^เปลี่ยน/.test((next.note||'').trim())) ? 'change' : 'cancel'));
            var s    = dlvStyle(r.status);
            var pn   = dlvParseNote(r.note);
            var st   = (r.status||'').toString().trim();
            var hasResult = st !== '' && st !== '0' && !isRedo;

            var head, border;
            if(kind === 'redo')        { head = dlvBadge('ไม่สำเร็จ / ส่งใหม่', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'change') { head = dlvBadge('เปลี่ยนคนขับ/ขนส่ง', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'cancel') { head = dlvBadge('ยกเลิกการจ่ายงาน', {bg:'#f3f4f6',fg:'#6b7280'}); border = '#9ca3af'; }
            else                       { head = dlvBadge(s.txt, s); border = s.border; }

            h += '<div style="border:2px '+(isHist?'dashed':'solid')+' '+border+';border-radius:10px;padding:10px 14px;background:#fff;">'
               + '<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">'
               +   '<span style="font-weight:700;color:#111827;">รอบที่ '+(i+1)
               +   (isHist ? ' <span style="color:#9a3412;font-size:12px;font-weight:600;">(จบรอบแล้ว)</span>'
                           : (r === latest ? ' <span style="color:#1b5e20;font-size:12px;font-weight:600;">(รอบปัจจุบัน)</span>' : ''))
               +   '</span>' + head
               + '</div>'
               + '<div style="font-size:13.5px;color:#374151;line-height:1.9;">';

            h += dlvLine('จ่ายงานโดย', '<b>'+dlvEsc(r.name_pick||'-')+'</b>' + (r.time_pick ? ' · '+dlvEsc(r.time_pick) : ''));
            h += dlvLine('ผู้ไปส่ง', '<b>'+dlvEsc(r.driver_name||'-')+'</b>'
                 + (r.transport_name ? ' · '+dlvEsc(r.transport_name) : '')
                 + (r.delivery_date ? ' · ให้ไปส่งวันที่ <b>'+dlvEsc(r.delivery_date)+'</b>' : '')
                 + (r.id_transport ? ' · เลขขนส่ง <b>'+dlvEsc(r.id_transport)+'</b>' : ''));
            if(hasResult){
                h += dlvLine('ผลการส่ง', dlvBadge(s.txt, s)
                     + ' · ยืนยันโดย <b>'+dlvEsc(r.check_name||'-')+'</b>' + (r.check_time ? ' · '+dlvEsc(r.check_time) : ''));
            } else if(kind === 'redo'){
                h += dlvLine('ผลการส่ง', '<b style="color:#a91f1f;">ไม่สำเร็จ</b>');
            } else if(!isHist){
                h += dlvLine('ผลการส่ง', '<span style="color:#9ca3af;">ยังไม่ยืนยันผล (อยู่ระหว่างไปส่ง)</span>');
            }
            var by = '<b>'+dlvEsc(r.cancelled_by || r.check_name || '-')+'</b>' + (r.cancelled_at ? ' · '+dlvEsc(r.cancelled_at) : '');
            if(kind === 'redo'){
                h += dlvLine('สั่งส่งใหม่', by
                     + (pn.reason ? ' · เหตุผล <b style="color:#a91f1f;">'+dlvEsc(pn.reason)+'</b>' : '')
                     + (pn.reassign ? ' · จ่ายใหม่ให้ '+dlvEsc(pn.reassign) : ''));
            } else if(kind === 'change'){
                h += dlvLine('เปลี่ยนคนขับ', by + ' · ไปต่อที่รอบที่ '+(i+2));
            } else if(kind === 'cancel'){
                h += dlvLine('ยกเลิกการจ่าย', by + ' · คืนงานไปหน้าจ่ายงาน');
            }
            if(pn.text){ h += dlvLine('หมายเหตุ', '<span style="color:#6b7280;">'+dlvEsc(pn.text)+'</span>'); }

            h += '</div></div>';
            if(i < total-1){ h += '<div style="text-align:center;color:#c7ccd3;font-size:11px;line-height:1;padding:3px 0;">|</div>'; }
        });
        return h;
    }

        /* ===== รายการซ้าย / รายละเอียดขวา ===== */
        const listBody  = document.getElementById('table-body');
        const detailEl  = document.getElementById('detail');
        const items     = Array.from(listBody.querySelectorAll('.it'));
        const itemCache = {};   // doc_id -> รายการสินค้า
        const dlvCache  = {};   // doc_id -> แถวการจ่ายงาน
        let current = null;

        const ICON_PDF  = '<svg class="i" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15h6M9 11h6"/></svg>';
        const ICON_PRINT = '<svg class="i" viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>';

        // พิมพ์ไฟล์ PDF ทันที (โหลดใน iframe ที่ซ่อนไว้แล้วเปิดหน้าต่างพิมพ์ ไม่เปิดแท็บใหม่)
        window.printPdf = function () {
            if (!current || !current.dataset.pdf) return;
            const btn = document.getElementById('print-btn');
            let url = current.dataset.pdf;
            try { const u = new URL(url, location.href); url = u.pathname + u.search; } catch (e) {}   // ใช้ path เดียวกับหน้าเว็บ กันปัญหาข้ามโดเมน
            url += (url.indexOf('?') === -1 ? '?' : '&') + 't=' + Date.now();                          // กันได้ไฟล์เก่าจาก cache
            if (btn) { btn.disabled = true; btn.classList.add('loading'); btn.querySelector('span').textContent = 'กำลังเตรียม...'; }
            const done = () => { if (btn) { btn.disabled = false; btn.classList.remove('loading'); btn.querySelector('span').textContent = 'พิมพ์'; } };
            let fr = document.getElementById('print-frame');
            if (fr) fr.remove();
            fr = document.createElement('iframe');
            fr.id = 'print-frame';
            fr.style.cssText = 'position:fixed;right:0;bottom:0;width:1px;height:1px;border:0;opacity:0;pointer-events:none;';
            fr.onload = function () {
                setTimeout(function () {
                    try { fr.contentWindow.focus(); fr.contentWindow.print(); }
                    catch (e) { window.open(current.dataset.pdf, '_blank'); }   // เบราว์เซอร์ไม่ยอมให้สั่งพิมพ์ -> เปิดไฟล์ให้กดพิมพ์เอง
                    done();
                }, 400);
            };
            setTimeout(done, 15000);
            fr.src = url;
            document.body.appendChild(fr);
        };
        const ICON_EDIT = '<svg class="i" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>';
        const ICON_DRV  = '<svg class="i" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/></svg>';

        const IC = {
            user:'<svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
            tag:'<svg class="i" viewBox="0 0 24 24"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><path d="M7 7h.01"/></svg>',
            pin:'<svg class="i" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            phone:'<svg class="i" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
            building:'<svg class="i" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/></svg>'
        };
        function card(cls, tone, icon, label, val) {
            return '<div class="'+cls+'"><span class="tile '+tone+'">'+icon+'</span><div style="min-width:0"><small>'+label+'</small><div class="v">'+val+'</div></div></div>';
        }

        function selectItem(el, opts) {
            if (!el) return;
            opts = opts || {};
            items.forEach(x => x.classList.remove('on'));
            el.classList.add('on');
            if (opts.scroll !== false) el.scrollIntoView({ block: 'nearest' });
            current = el;
            const d = el.dataset;
            if (history.replaceState) history.replaceState(null, '', '#' + encodeURIComponent(d.doc));

            const pdfBtn = d.pdf
                ? '<button type="button" class="print-btn" id="print-btn" onclick="printPdf()" title="พิมพ์เอกสาร PDF">'+ICON_PRINT+'<span>พิมพ์</span></button>'
                : '<button type="button" class="print-btn" disabled title="ยังไม่มีไฟล์ PDF">'+ICON_PRINT+'<span>พิมพ์</span></button>';
            const driveBtn = d.drive
                ? '<a class="btn-edit" href="'+escHtml(d.drive)+'" target="_blank" title="เปิดไฟล์ใน Google Drive">'+ICON_DRV+'Drive</a>' : '';
            const kv = (label, val) => '<div class="kv-row"><dt>'+label+'</dt><dd>'+val+'</dd></div>';

            detailEl.innerHTML =
                '<div class="det-top">'
              +   '<div class="det-titles"><h3>'+escHtml(d.doc)+'</h3>'
              +     '<div class="det-sub"><span>SO '+escHtml(d.so)+'</span><span>เปิดโดย '+escHtml(d.emp || '-')+'</span><span>'+escHtml(d.date)+'</span></div></div>'
              // สถานะจ่ายงานให้คนขับ อยู่ในหัว ระหว่างชื่อเอกสารกับปุ่ม
              +   '<div id="det-dlv" class="dlv-card" style="--c:var(--ink-300)"><div class="row1"><span class="badge st '+escHtml(d.dlvCls)+'">'+escHtml(d.dlvTxt)+'</span>'
              +     '<span class="info1" style="color:var(--ink-300)">กำลังโหลดสถานะจ่ายงาน...</span></div></div>'
              +   '<div class="det-actions">'+driveBtn+pdfBtn
              +     '<a class="btn-edit" href="'+escHtml(d.edit)+'" title="แก้ไขเอกสาร">'+ICON_EDIT+'แก้ไข</a></div>'
              + '</div>'

              // 2) แผ่นเอกสาร
              + '<div class="paper-wrap"><div class="paper" id="det-paper">'
              +   '<div class="pd-head">'
              +     '<div><h1>'+escHtml(d.headcom || '-')+'</h1>'
              +       '<p>ประเภทบิล: <b>'+escHtml(d.doctype || '-')+'</b>'+(d.so && d.so !== '-' ? '&nbsp;&nbsp; เลข SO: <b>'+escHtml(d.so)+'</b>' : '')+'</p></div>'
              +     '<div class="pd-box"><div><span class="k">SP</span><span class="c">:</span><span class="v">'+escHtml(d.doc)+'</span></div>'
              +       '<div><span class="k">DATE</span><span class="c">:</span><span class="v">'+escHtml(d.date)+'</span></div></div>'
              +   '</div>'
              +   '<div class="pd-title"><h2>ใบส่งของชั่วคราว</h2></div>'
              +   '<div class="pd-info">'
              +     '<div><span class="l">บริษัท</span><span class="c">:</span><span class="v">'+escHtml(d.com || '-')+'</span></div>'
              +     '<div><span class="l">ที่อยู่</span><span class="c">:</span><span class="v">'+escHtml(d.address || '-')+'</span></div>'
              +     '<div><span class="l">ผู้ติดต่อ</span><span class="c">:</span><span class="v">'+escHtml(d.contact || '-')
              +       '<span class="tel"><b>โทร :</b> '+escHtml(d.tel || '-')+'</span></span></div>'
              +     '<div><span class="l">หมายเหตุ</span><span class="c">:</span><span class="v">'+escHtml(d.notes || '-')+'</span></div>'
              +   '</div>'
              +   '<div id="det-items" class="loading">กำลังโหลดรายการสินค้า...</div>'
              +   '<div class="pd-foot">'
              +     '<div class="pd-sign"><div class="line"></div><p>ผู้รับสินค้า</p><div class="dt">วันที่ <i></i><s>/</s><i></i><s>/</s><i></i></div></div>'
              +     '<div class="pd-page">แผ่นที่ 1/1</div>'
              +     '<div class="pd-sign"><div class="line"></div><p>ผู้ส่งสินค้า</p><div class="dt">วันที่ <i></i><s>/</s><i></i><s>/</s><i></i></div></div>'
              +   '</div>'
              + '</div></div>';
            detailEl.scrollTop = 0;
            dlvHistHtml = '';
            if (window.innerWidth <= 900 && opts.scroll !== false) detailEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

            loadItems(d.doc);
            loadDelivery(d.doc);
        }

        function renderItems(docId, data) {
            if (!current || current.dataset.doc !== docId) return;
            const box = document.getElementById('det-items');
            if (!box) return;
            if (!data) { box.className = 'loading'; box.innerHTML = '<span style="color:#991b1b">เกิดข้อผิดพลาดในการโหลดรายการสินค้า</span>'; return; }
            box.className = '';
            box.innerHTML = '<table class="pd-table"><thead><tr><th class="n">ลำดับ</th><th>รายการ</th><th class="q">จำนวน</th></tr></thead><tbody>'
                + (data.length
                    ? data.map((it, i) => '<tr><td class="n">'+(i + 1)+'</td><td>'+escHtml(it.item_name)+'</td><td class="q">'+escHtml(String(it.quantity).replace(/\.00$/, ''))+'</td></tr>').join('')
                    : '<tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:18px">ไม่มีรายการสินค้า</td></tr>')
                + '</tbody></table>';
        }
        function loadItems(docId) {
            if (itemCache[docId]) return renderItems(docId, itemCache[docId]);
            fetch(`/get-docbill-detail/${encodeURIComponent(docId)}`)
                .then(r => r.json())
                .then(data => { itemCache[docId] = data; renderItems(docId, data); })
                .catch(err => { console.error('Error fetching data:', err); renderItems(docId, null); });
        }

        // สรุปสั้น ๆ ของรอบล่าสุด + กดดูประวัติทุกรอบ (ใช้ dlvTimeline เดิม)
        // โทนสีสถานะ (ชุดเดียวกับป้ายในรายการซ้าย)
        function dlvTone(st) {
            st = (st || '').toString().trim();
            if (st === '' || st === '0')                                  return { cls: 'dlv-wait',  dot: '#8b5cf6', txt: 'กำลังไปส่ง' };
            if (st.indexOf('สำเร็จ') !== -1 && st.indexOf('ไม่') === -1)    return { cls: 'dlv-ok',    dot: '#22c55e', txt: 'สำเร็จ' };
            if (st.indexOf('สินค้าผิด') !== -1)                             return { cls: 'dlv-wrong', dot: '#ec4899', txt: 'สินค้าผิด' };
            if (st.indexOf('ไม่สำเร็จ') !== -1)                             return { cls: 'dlv-none',  dot: '#ef4444', txt: 'ไม่สำเร็จ' };
            if (st.indexOf('ส่งใหม่') !== -1)                               return { cls: 'dlv-redo',  dot: '#3b82f6', txt: 'ส่งใหม่' };
            if (st.indexOf('ค้างบิล') !== -1)                               return { cls: 'dlv-hold',  dot: '#f59e0b', txt: 'ค้างบิล' };
            return { cls: 'dlv-gray', dot: '#94a3b8', txt: st };
        }
        const stBadge = t => '<span class="badge st '+t.cls+'">'+dlvEsc(t.txt)+'</span>';
        const IC_TRUCK = '<svg class="i" viewBox="0 0 24 24"><path d="M1 4h14v12H1zM15 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg>';
        const IC_CAL   = '<svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
        const IC_CHECK = '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
        const IC_HIST  = '<svg class="i" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/></svg>';

        function dlvSummary(rows) {
            const active = rows.filter(r => !r.cancelled_at);
            const r = active.length ? active[active.length - 1] : null;
            const btn = '<button type="button" class="hist-btn" onclick="openDlvHistory()">'+IC_HIST+'ดูประวัติ<span class="n">'+rows.length+'</span></button>';
            const hist = dlvHistoryHtml(rows);
            if (!r) {
                return { html: '<div class="row1">'+stBadge({cls:'dlv-hold', txt:'รอจ่ายงานใหม่'})
                             + '<span class="info1"><span class="dlv-meta"><span class="muted">ทุกรอบถูกส่งใหม่/ยกเลิกแล้ว · รอจ่ายที่หน้าจ่ายงานขนส่ง</span></span></span>'+btn+'</div>', hist: hist };
            }
            const t  = dlvTone(r.status);
            const st = (r.status || '').toString().trim();
            const hasResult = st !== '' && st !== '0' && st.indexOf('ส่งใหม่') === -1;
            let m = '<span class="dlv-meta">'+IC_TRUCK+'<b>'+dlvEsc(r.driver_name || '-')+'</b>'
                  + (r.transport_name ? '<span class="muted">'+dlvEsc(r.transport_name)+'</span>' : '')+'</span>';
            if (r.delivery_date) m += '<span class="dlv-meta">'+IC_CAL+'ไปส่ง <b>'+dlvEsc(r.delivery_date)+'</b></span>';
            if (hasResult)       m += '<span class="dlv-meta">'+IC_CHECK+'ยืนยันโดย <b>'+dlvEsc(r.check_name || '-')+'</b></span>';
            return { html: '<div class="row1">'+stBadge(t)+'<span class="info1">'+m+'</span>'+btn+'</div>', hist: hist };
        }

        // ไทม์ไลน์สำหรับป๊อปอัพ
        function dlvHistoryHtml(rows) {
            const active = rows.filter(r => !r.cancelled_at);
            const latest = active.length ? active[active.length - 1] : null;
            const redoN  = rows.filter(r => (r.status || '').indexOf('ส่งใหม่') !== -1).length;
            let h = '<div class="hs-top">' + (latest ? stBadge(dlvTone(latest.status)) : stBadge({cls:'dlv-hold', txt:'รอจ่ายงานใหม่'}))
                  + (latest ? '<span>คนขับ <b>'+dlvEsc(latest.driver_name || '-')+'</b>'+(latest.delivery_date ? ' · ไปส่ง <b>'+dlvEsc(latest.delivery_date)+'</b>' : '')+'</span>' : '')
                  + '<span class="sp">ทั้งหมด '+rows.length+' รอบ'+(redoN ? ' · ส่งใหม่ '+redoN+' ครั้ง' : '')+'</span></div><div class="tl">';
            rows.forEach((r, i) => {
                const isHist = !!r.cancelled_at;
                const isRedo = (r.status || '').indexOf('ส่งใหม่') !== -1;
                const next   = rows[i + 1];
                const kind   = !isHist ? '' : (isRedo ? 'redo' : ((next && /^เปลี่ยน/.test((next.note || '').trim())) ? 'change' : 'cancel'));
                const st     = (r.status || '').toString().trim();
                const pn     = dlvParseNote(r.note);
                let t = dlvTone(r.status);
                if (kind === 'redo')   t = { cls: 'dlv-redo', dot: '#3b82f6', txt: 'ไม่สำเร็จ · ส่งใหม่' };
                if (kind === 'change') t = { cls: 'dlv-redo', dot: '#3b82f6', txt: 'เปลี่ยนคนขับ' };
                if (kind === 'cancel') t = { cls: 'dlv-gray', dot: '#94a3b8', txt: 'ยกเลิกการจ่ายงาน' };
                const row = (k, v, cls) => '<dt>'+k+'</dt><dd'+(cls ? ' class="'+cls+'"' : '')+'>'+v+'</dd>';
                let kv = row('จ่ายงานโดย', dlvEsc(r.name_pick || '-') + (r.time_pick ? ' <small>· '+dlvEsc(r.time_pick)+'</small>' : ''))
                       + row('คนขับ', '<b>'+dlvEsc(r.driver_name || '-')+'</b>' + (r.transport_name ? ' <small>· '+dlvEsc(r.transport_name)+'</small>' : ''));
                if (r.delivery_date) kv += row('วันที่ไปส่ง', dlvEsc(r.delivery_date));
                if (r.id_transport)  kv += row('เลขขนส่ง', dlvEsc(r.id_transport));
                if (st !== '' && st !== '0' && !isRedo) kv += row('ผลการส่ง', stBadge(dlvTone(r.status)) + ' <small>โดย '+dlvEsc(r.check_name || '-')+(r.check_time ? ' · '+dlvEsc(r.check_time) : '')+'</small>');
                else if (!isHist) kv += row('ผลการส่ง', '<small>ยังไม่ยืนยันผล (อยู่ระหว่างไปส่ง)</small>');
                const by = dlvEsc(r.cancelled_by || r.check_name || '-') + (r.cancelled_at ? ' <small>· '+dlvEsc(r.cancelled_at)+'</small>' : '');
                if (kind === 'redo') {
                    kv += row('สั่งส่งใหม่โดย', by);
                    if (pn.reason)   kv += row('เหตุผล', dlvEsc(pn.reason), 'warn');
                    if (pn.reassign) kv += row('จ่ายใหม่ให้', dlvEsc(pn.reassign));
                } else if (kind === 'change') kv += row('เปลี่ยนโดย', by + ' <small>· ไปต่อรอบที่ '+(i + 2)+'</small>');
                else if (kind === 'cancel')   kv += row('ยกเลิกโดย', by + ' <small>· คืนงานไปหน้าจ่ายงาน</small>');
                if (pn.text) kv += row('หมายเหตุ', dlvEsc(pn.text));
                h += '<div class="tl-item" style="--dot:'+t.dot+'"><span class="tl-dot"></span><div class="tl-card'+(isHist ? ' past' : '')+'">'
                   + '<div class="tl-head"><b>รอบที่ '+(i + 1)+'</b>'
                   + (r === latest ? '<span class="tag">ปัจจุบัน</span>' : (isHist ? '<span class="tag old">จบรอบแล้ว</span>' : ''))
                   + stBadge(t)+'</div><dl class="kv2" style="margin:0">'+kv+'</dl></div></div>';
            });
            return h + '</div>';
        }

        // ป๊อปอัพประวัติการจ่ายงาน
        let dlvHistHtml = '';
        window.openDlvHistory = function () {
            const m = document.getElementById('hist-modal');
            document.getElementById('hist-title').textContent = 'ประวัติการจ่ายงาน — ' + (current ? current.dataset.doc : '');
            document.getElementById('hist-body').innerHTML = dlvHistHtml || '<div class="loading">ไม่มีข้อมูล</div>';
            m.hidden = false;
            document.body.style.overflow = 'hidden';
            document.getElementById('hist-body').scrollTop = 0;
            document.getElementById('hist-close').focus();
        };
        window.closeDlvHistory = function () {
            document.getElementById('hist-modal').hidden = true;
            document.body.style.overflow = '';
        };
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !document.getElementById('hist-modal').hidden) closeDlvHistory();
        });

        function renderDelivery(docId, j) {
            if (!current || current.dataset.doc !== docId) return;
            const box = document.getElementById('det-dlv');
            if (j === null) {
                box.className = 'dlv-card err'; box.removeAttribute('style');
                box.innerHTML = '<div class="row1" style="color:inherit"><b>โหลดสถานะจ่ายงานไม่สำเร็จ</b><span class="info1">กรุณาลองใหม่อีกครั้ง</span></div>';
            } else if (!j.found || !j.rows.length) {
                box.className = 'dlv-card empty'; box.removeAttribute('style');
                box.innerHTML = '<div class="row1" style="color:inherit"><span class="badge st dlv-none">ยังไม่จ่ายงาน</span>'
                              + '<span class="info1"><span class="dlv-meta" style="color:#92400e">บิลนี้ยังไม่มีการจ่ายงานให้คนขับ · กรุณาติดต่อฝ่ายจ่ายงานเพื่อดำเนินการ</span></span></div>';
            } else {
                const sm = dlvSummary(j.rows);
                box.className = 'dlv-card'; box.removeAttribute('style');
                box.innerHTML = sm.html;
                dlvHistHtml = sm.hist;
            }
        }
        async function loadDelivery(docId) {
            if (dlvCache[docId]) return renderDelivery(docId, dlvCache[docId]);
            try {
                const res = await fetch(DELIVERY_STATUS_URL + '?bill_id=' + encodeURIComponent(docId), { headers: { 'Accept': 'application/json' } });
                const j = await res.json();
                dlvCache[docId] = j;
                renderDelivery(docId, j);
            } catch (e) {
                renderDelivery(docId, null);
            }
        }

        // คลิก / Enter เลือกรายการ ; ลูกศรขึ้นลงเลื่อนรายการ
        items.forEach(el => {
            el.addEventListener('click', () => selectItem(el));
            el.addEventListener('keydown', e => {
                if (e.key === 'Enter') selectItem(el);
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
            const tag = (document.activeElement && document.activeElement.tagName) || '';
            if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') return;
            const vis = items.filter(x => x.style.display !== 'none');
            if (!vis.length) return;
            e.preventDefault();
            let i = vis.indexOf(current);
            i = e.key === 'ArrowDown' ? Math.min(vis.length - 1, i + 1) : Math.max(0, i - 1);
            selectItem(vis[i]);
            vis[i].focus({ preventScroll: true });
        });

        // ช่องค้นหาด้านบน: พิมพ์ = กรองรายการที่โหลดมาแล้วทันที ; กด Enter = ค้นจาก server
        const listEmpty  = document.getElementById('list-empty');
        const listShown  = document.getElementById('list-shown');
        qInputEl.addEventListener('input', () => {
            const c = classifyQuery(qInputEl.value);
            const q = c ? c.value.toLowerCase() : '';
            let n = 0;
            items.forEach(el => {
                const d = el.dataset;
                const hay = [d.doc, d.so, d.com, d.emp, d.headcom, d.dlvTxt].join(' ').toLowerCase();
                const ok = !q || hay.indexOf(q) !== -1;
                el.style.display = ok ? '' : 'none';
                if (ok) n++;
            });
            listEmpty.style.display = n ? 'none' : '';
            listShown.textContent = q ? '· แสดง ' + n + ' (กด Enter เพื่อค้นทั้งหมด)' : '';
        });

        // เปิดรายการเดิมจาก #hash (หลังรีเฟรช/กรอง) ไม่งั้นเลือกรายการแรก
        (function initSelection(){
            const want = decodeURIComponent((location.hash || '').slice(1));
            const found = want && items.find(x => x.dataset.doc === want);
            if (found) selectItem(found);
            else if (items.length && window.innerWidth > 900) selectItem(items[0], { scroll: false });
        })();
    </script>
</body>
</html>
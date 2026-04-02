<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="dummy-csrf-token">
    <title>ระบบติดตามการจัดส่ง</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:    #1a3a6b;
            --primary-d:  #122a52;
            --primary-l:  #e8edf6;
            --accent:     #c8932a;
            --border:     #d0d5dd;
            --border-l:   #e4e7ec;
            --bg:         #f5f6f8;
            --white:      #ffffff;
            --text:       #101828;
            --text-2:     #344054;
            --muted:      #667085;
            --muted-l:    #98a2b3;
            --success:    #027a48;
            --success-bg: #ecfdf3;
            --success-bd: #abefc6;
            --warn:       #b54708;
            --warn-bg:    #fef6ee;
            --warn-bd:    #f9dbaf;
            --danger:     #b42318;
            --danger-bg:  #fef3f2;
            --danger-bd:  #fecdca;
            --info:       #000000;
            --info-bg:    #f97a7a;
            --info-bd:    #ffc4b2;
            --oil:        #92400e;
            --oil-bg:     #fffbeb;
            --oil-bd:     #fde68a;
            --insp:       #065f46;
            --insp-bg:    #ecfdf5;
            --insp-bd:    #6ee7b7;
            --mono:       'IBM Plex Mono', monospace;
            --font:       'Noto Sans Thai', sans-serif;
            --radius:     6px;
            --shadow-sm:  0 1px 3px rgba(16,24,40,.08), 0 1px 2px rgba(16,24,40,.06);
        }

        html, body { height: 100%; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ──────────────────────────────────── */
        .topbar {
            background: var(--primary);
            height: 60px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.18);
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-logo {
            width: 34px; height: 34px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-logo svg { color: #fff; }
        .brand-name { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
        .brand-sub  { font-size: 10px; color: rgba(255,255,255,.5); letter-spacing: .06em; text-transform: uppercase; font-weight: 500; }
        .topbar-divider { width: 1px; height: 28px; background: rgba(255,255,255,.15); }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .hdr-date { font-size: 12px; color: rgba(255,255,255,.65); font-family: var(--mono); }

        /* ── TOPBAR ACTION BUTTONS ───────────────────── */
        .btn-topbar {
            height: 34px;
            border-radius: var(--radius);
            padding: 0 13px;
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all .15s;
            border: 1px solid;
            white-space: nowrap;
        }
        .btn-oil {
            background: rgba(251,191,36,.15);
            color: #fbbf24;
            border-color: rgba(251,191,36,.3);
        }
        .btn-oil:hover {
            background: rgba(251,191,36,.25);
            border-color: rgba(251,191,36,.5);
        }
        .btn-inspect {
            background: rgba(52,211,153,.15);
            color: #34d399;
            border-color: rgba(52,211,153,.3);
        }
        .btn-inspect:hover {
            background: rgba(52,211,153,.25);
            border-color: rgba(52,211,153,.5);
        }

        .hdr-user {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px; padding: 4px 12px 4px 6px;
        }
        .hdr-avatar {
            width: 24px; height: 24px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #fff;
        }
        .hdr-username { font-size: 12px; color: rgba(255,255,255,.85); font-weight: 500; }

        /* ── MAIN ────────────────────────────────────── */
        main {
            flex: 1; display: flex; flex-direction: column;
            padding: 20px 28px; min-height: 0;
        }

        /* ── FILTER ──────────────────────────────────── */
        .filter-card {
            background: var(--white); border: 1px solid var(--border-l);
            border-radius: var(--radius); box-shadow: var(--shadow-sm);
            margin-bottom: 14px; flex-shrink: 0; overflow: hidden;
        }
        .filter-card-head {
            padding: 9px 18px; border-bottom: 1px solid var(--border-l);
            background: #fafafa; display: flex; align-items: center; gap: 8px;
        }
        .filter-card-head-title {
            font-size: 11px; font-weight: 700; color: var(--text-2);
            text-transform: uppercase; letter-spacing: .07em;
        }
        .filter-card-body {
            padding: 14px 18px;
            display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
        }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { font-size: 12px; font-weight: 600; color: var(--text-2); }
        .field input, .field select {
            height: 36px; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 12px; font-family: var(--font); font-size: 13px; color: var(--text);
            background: var(--white); outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-shadow: 0 1px 2px rgba(16,24,40,.05);
        }
        .field input { min-width: 160px; }
        .field select { min-width: 180px; cursor: pointer; }
        .field input:focus, .field select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,58,107,.1);
        }
        .field-divider { width: 1px; height: 36px; background: var(--border-l); align-self: flex-end; }
        .btn-primary {
            height: 36px; background: var(--primary); color: #fff;
            border: 1px solid var(--primary-d); border-radius: var(--radius);
            padding: 0 18px; font-family: var(--font); font-size: 13px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: all .15s; box-shadow: 0 1px 2px rgba(16,24,40,.1);
        }
        .btn-primary:hover { background: var(--primary-d); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-secondary {
            height: 36px; background: var(--white); color: var(--text-2);
            border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 14px; font-family: var(--font); font-size: 13px; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            transition: all .15s; box-shadow: 0 1px 2px rgba(16,24,40,.05);
        }
        .btn-secondary:hover { background: #f9fafb; border-color: #9ca3af; }

        /* ── TABLE CARD ──────────────────────────────── */
        .table-card {
            background: var(--white); border: 1px solid var(--border-l);
            border-radius: var(--radius); box-shadow: var(--shadow-sm);
            flex: 1; display: flex; flex-direction: column;
            min-height: 0; overflow: hidden;
        }
        .table-card-head {
            padding: 11px 18px; border-bottom: 1px solid var(--border-l);
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa; flex-shrink: 0;
        }
        .table-card-head-left { display: flex; align-items: center; gap: 10px; }
        .table-card-title { font-size: 13px; font-weight: 700; color: var(--text); }
        .record-badge {
            background: var(--primary-l); color: var(--primary);
            border: 1px solid #c5d3ec; border-radius: 20px; padding: 1px 9px;
            font-size: 11px; font-weight: 700; font-family: var(--mono);
        }
        .search-wrap { position: relative; }
        .search-wrap svg {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%); color: var(--muted-l); pointer-events: none;
        }
        .search-wrap input {
            height: 32px; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 12px 0 32px; font-family: var(--font); font-size: 12px;
            color: var(--text); background: var(--white); outline: none;
            width: 220px; transition: all .15s;
            box-shadow: 0 1px 2px rgba(16,24,40,.05);
        }
        .search-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,58,107,.08);
            width: 260px;
        }

        /* ── TABLE ───────────────────────────────────── */
        .table-scroll { flex: 1; overflow: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }

        col.c-num  { width: 50px; }
        col.c-date { width: 115px; }
        col.c-name { width: 148px; }
        col.c-bill { width: 145px; }
        col.c-job  { width: 230px; }
        col.c-note { width: 220px; }
        col.c-stat { width: 180px; }

        thead th {
            padding: 10px 14px;
            text-align: center;
            font-size: 11px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .07em;
            background: #f9fafb;
            border-bottom: 2px solid var(--border-l);
            white-space: nowrap;
            position: sticky; top: 0; z-index: 2;
        }
        thead th.th-job, thead th.th-note {
            color: var(--primary); background: #eef2f9;
        }

        tbody tr { border-bottom: 1px solid var(--border-l); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover { background: #edf2fb !important; }

        tbody td {
            padding: 10px 14px;
            font-size: 13px; vertical-align: middle;
            color: var(--text-2);
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            text-align: center;
        }
        tbody td.td-job, tbody td.td-note { text-align: left; }

        .row-num  { font-family: var(--mono); font-size: 11px; color: var(--muted-l); }
        .cell-date { font-family: var(--mono); font-size: 12px; color: var(--muted); }
        .cell-name { font-weight: 600; color: var(--text); }
        .cell-bill {
            font-family: var(--mono); font-size: 12px;
            color: var(--info); font-weight: 600;
            background: var(--info-bg); border: 1px solid var(--info-bd);
            border-radius: 4px; padding: 3px 10px; display: inline-block;
        }
        .cell-job {
            font-size: 13px; font-weight: 500; color: var(--text);
            display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .cell-note {
            font-size: 12px; color: var(--muted);
            display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .cell-note.empty { color: #c8d0dc; font-style: italic; }

        /* ── STATUS CELL ─────────────────────────────── */
        .status-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            justify-content: center;
        }
        .st-sel {
            border: 1px solid; border-radius: 4px;
            padding: 5px 24px 5px 10px;
            font-family: var(--font); font-size: 12px; font-weight: 600;
            cursor: pointer; outline: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23667085'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
            transition: all .15s;
            width: auto;
            max-width: 160px;
        }
        .st-sel:hover { filter: brightness(.97); }
        .st-empty   { background: #f9fafb;           color: #4b5563;        border-color: var(--border); }
        .st-newbill { background: var(--info-bg);    color: var(--info);    border-color: var(--info-bd); }
        .st-return  { background: var(--warn-bg);    color: var(--warn);    border-color: var(--warn-bd); }
        .st-stock   { background: var(--success-bg); color: var(--success); border-color: var(--success-bd); }
        .st-wrong   { background: var(--danger-bg);  color: var(--danger);  border-color: var(--danger-bd); }

        /* ── NEW BILL INLINE ─────────────────────────── */
        .newbill-wrap {
            display: none;
            align-items: center;
            gap: 6px;
            width: 100%;
            animation: fadeDown .15s ease;
        }
        .newbill-wrap.show { display: flex; }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-3px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .newbill-input {
            height: 28px;
            border: 1.5px solid var(--info-bd);
            border-radius: 4px;
            padding: 0 8px;
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            color: var(--info);
            background: #fff;
            outline: none;
            flex: 1;
            min-width: 0;
            transition: border-color .15s, box-shadow .15s;
        }
        .newbill-input::placeholder { color: #93c5fd; font-weight: 400; font-family: var(--font); }
        .newbill-input:focus {
            border-color: var(--info);
            box-shadow: 0 0 0 3px rgba(23,92,211,.1);
        }
        .newbill-save {
            height: 28px;
            background: var(--info);
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 0 10px;
            font-family: var(--font);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s;
            flex-shrink: 0;
        }
        .newbill-save:hover { background: #1248a8; }
        .newbill-save:active { transform: translateY(1px); }

        /* ── EMPTY / LOADING ─────────────────────────── */
        .empty-cell { padding: 64px 24px !important; text-align: center !important; white-space: normal !important; }
        .empty-icon {
            width: 50px; height: 50px; background: var(--bg);
            border: 1px solid var(--border-l); border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; color: var(--muted-l);
        }
        .empty-t { font-size: 13px; font-weight: 600; color: var(--text-2); margin-bottom: 4px; }
        .empty-s { font-size: 12px; color: var(--muted); }

        @keyframes spin { to { transform: rotate(360deg) } }
        .spin {
            display: inline-block; width: 16px; height: 16px;
            border: 2px solid var(--border-l); border-top-color: var(--primary);
            border-radius: 50%; animation: spin .7s linear infinite;
            vertical-align: middle; margin-right: 8px;
        }

        /* ── TABLE FOOTER ────────────────────────────── */
        .table-card-foot {
            padding: 9px 18px; border-top: 1px solid var(--border-l);
            background: #fafafa;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 11px; color: var(--muted); font-family: var(--mono);
            flex-shrink: 0;
        }
        .foot-left { display: flex; align-items: center; gap: 12px; }

        /* ── MODAL ───────────────────────────────────── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .2s;
        }
        .modal-overlay.show { opacity: 1; pointer-events: all; }
        .modal {
            background: var(--white);
            border-radius: 10px;
            width: 520px;
            max-width: calc(100vw - 32px);
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            transform: translateY(10px) scale(.98);
            transition: transform .2s;
            overflow: hidden;
        }
        .modal-overlay.show .modal { transform: translateY(0) scale(1); }
        .modal-head {
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--border-l);
            display: flex; align-items: center; gap: 12px;
        }
        .modal-icon {
            width: 40px; height: 40px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .modal-icon.oil   { background: var(--oil-bg); color: var(--oil); border: 1px solid var(--oil-bd); }
        .modal-icon.insp  { background: var(--insp-bg); color: var(--insp); border: 1px solid var(--insp-bd); }
        .modal-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .modal-sub   { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .modal-close {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: var(--muted-l); padding: 4px;
            border-radius: 4px; transition: all .15s;
        }
        .modal-close:hover { background: var(--bg); color: var(--text); }
        .modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 14px; }
        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-field { display: flex; flex-direction: column; gap: 5px; }
        .modal-field label { font-size: 12px; font-weight: 600; color: var(--text-2); }
        .modal-field input, .modal-field select {
            height: 36px; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 12px; font-family: var(--font); font-size: 13px; color: var(--text);
            background: var(--white); outline: none; width: 100%;
            transition: border-color .15s, box-shadow .15s;
        }
        .modal-field input:focus, .modal-field select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,58,107,.1);
        }
        .modal-foot {
            padding: 14px 22px;
            border-top: 1px solid var(--border-l);
            background: #fafafa;
            display: flex; justify-content: flex-end; gap: 10px;
        }

        /* ── TOAST ───────────────────────────────────── */
        .toast {
            position: fixed; bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(12px);
            background: var(--text); color: #fff;
            padding: 10px 20px; border-radius: var(--radius);
            font-size: 12px; font-weight: 600; opacity: 0;
            transition: all .22s ease; z-index: 999; pointer-events: none;
            white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,.18);
            border-left: 3px solid var(--accent);
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        @media (max-width: 768px) {
            .topbar, main { padding-left: 16px; padding-right: 16px; }
            .filter-card-body { flex-direction: column; align-items: stretch; }
            .field input, .field select { min-width: unset; width: 100%; }
            .field-divider { display: none; }
            .table-card-head { flex-direction: column; align-items: flex-start; gap: 10px; }
            .search-wrap, .search-wrap input { width: 100%; }
            .hdr-date { display: none; }
            .btn-topbar span { display: none; }
            .btn-topbar { padding: 0 10px; }
            .modal-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <div class="brand-logo">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/>
                <rect x="9" y="11" width="14" height="10" rx="2"/>
                <circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">ระบบบริหารการจัดส่ง</div>
            <div class="brand-sub">Delivery Management System</div>
        </div>
    </div>
    <div class="topbar-right">
        <span class="hdr-date" id="hdr-date"></span>
        <div class="topbar-divider"></div>
        <!-- ปุ่มน้ำมัน -->
        <a href="oil" class="btn-topbar btn-oil" style="text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 22V8l9-6 9 6v14"/><path d="M3 22h18"/><path d="M12 22V12"/><circle cx="12" cy="8" r="2"/>
            </svg>
            <span>บันทึกน้ำมัน</span>
        </a>
        <!-- ปุ่มตรวจสภาพรถ -->
        <a href="service" class="btn-topbar btn-inspect" style="text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
            </svg>
            <span>ตรวจสภาพรถ</span>
        </a>
        <div class="topbar-divider"></div>
        <div class="hdr-user">
            <div class="hdr-avatar">A</div>
            <span class="hdr-username">Admin</span>
        </div>
    </div>
</header>

<main>
    <div class="filter-card">
        <div class="filter-card-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#667085" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            <span class="filter-card-head-title">เงื่อนไขการค้นหา</span>
        </div>
        <div class="filter-card-body">
            <div class="field">
                <label>วันที่จัดส่ง</label>
                <input type="date" id="f-date">
            </div>
            <div class="field">
                <label>ชื่อ คนขับ</label>
                <select id="f-driver">
                    <option value="">— เลือก คนขับ —</option>
                    <option value="บังเดช">บังเดช</option>
                    <option value="แชม">แชม</option>
                    <option value="กอล์ฟ">กอล์ฟ</option>
                    <option value="หรั่ง">หรั่ง</option>
                    <option value="เก่ง">เก่ง</option>
                    <option value="เอ">เอ</option>
                    <option value="ยุทร">ยุทร</option>
                    <option value="แฟรงค์">แฟรงค์</option>
                    <option value="เอ้">เอ้</option>
                </select>
            </div>
            <div class="field-divider"></div>
            <button class="btn-primary" onclick="load()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                ค้นหา
            </button>
            <button class="btn-secondary" onclick="resetFilter()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                รีเซ็ต
            </button>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-head-left">
                <span class="table-card-title">รายการทั้งหมด</span>
                <span class="record-badge" id="tbl-count">0 รายการ</span>
            </div>
            <div class="search-wrap">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="ค้นหาในตาราง..." oninput="doSearch(this.value)">
            </div>
        </div>

        <div class="table-scroll">
            <table>
                <colgroup>
                    <col class="c-num">
                    <col class="c-date">
                    <col class="c-name">
                    <col class="c-bill">
                    <col class="c-job">
                    <col class="c-note">
                    <col class="c-stat">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>วันที่</th>
                        <th>ชื่อ Sale</th>
                        <th>เลขที่บิล</th>
                        <th class="th-job">งาน</th>
                        <th class="th-note">หมายเหตุ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr><td colspan="7" class="empty-cell">
                        <div class="empty-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg>
                        </div>
                        <div class="empty-t">ยังไม่มีข้อมูล</div>
                        <div class="empty-s">กรุณาเลือกวันที่และกดค้นหาเพื่อแสดงรายการ</div>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-card-foot">
            <div class="foot-left">
                <span id="ft-info">—</span>
            </div>
            <span id="ft-time">—</span>
        </div>
    </div>
</main>

<!-- ── MODAL น้ำมัน ─────────────────────────────── -->
<div class="modal-overlay" id="modal-oil">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-icon oil">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 22V8l9-6 9 6v14"/><path d="M3 22h18"/><path d="M12 22V12"/><circle cx="12" cy="8" r="2"/>
                </svg>
            </div>
            <div>
                <div class="modal-title">บันทึกการเติมน้ำมัน</div>
                <div class="modal-sub">บันทึกข้อมูลการเติมน้ำมันประจำวัน</div>
            </div>
            <button class="modal-close" onclick="closeModal('oil')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-row">
                <div class="modal-field">
                    <label>วันที่</label>
                    <input type="date" id="oil-date">
                </div>
                <div class="modal-field">
                    <label>คนขับ</label>
                    <select id="oil-driver">
                        <option value="">— เลือก —</option>
                        <option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option>
                        <option>หรั่ง</option><option>เก่ง</option><option>เอ</option>
                        <option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option>
                    </select>
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>ทะเบียนรถ</label>
                    <input type="text" id="oil-plate" placeholder="เช่น กข-1234">
                </div>
                <div class="modal-field">
                    <label>เลขไมล์ (กม.)</label>
                    <input type="number" id="oil-mileage" placeholder="เช่น 123456">
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>ปริมาณน้ำมัน (ลิตร)</label>
                    <input type="number" id="oil-liters" placeholder="เช่น 40">
                </div>
                <div class="modal-field">
                    <label>ยอดเงิน (บาท)</label>
                    <input type="number" id="oil-amount" placeholder="เช่น 1500">
                </div>
            </div>
            <div class="modal-field">
                <label>หมายเหตุ</label>
                <input type="text" id="oil-note" placeholder="บันทึกเพิ่มเติม (ถ้ามี)">
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-secondary" onclick="closeModal('oil')">ยกเลิก</button>
            <button class="btn-primary" onclick="saveOil()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                บันทึก
            </button>
        </div>
    </div>
</div>

<!-- ── MODAL ตรวจสภาพรถ ──────────────────────────── -->
<div class="modal-overlay" id="modal-inspect">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-icon insp">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4"/><path d="M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                </svg>
            </div>
            <div>
                <div class="modal-title">ตรวจสภาพรถ</div>
                <div class="modal-sub">บันทึกผลการตรวจสภาพรถประจำวัน</div>
            </div>
            <button class="modal-close" onclick="closeModal('inspect')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-row">
                <div class="modal-field">
                    <label>วันที่ตรวจ</label>
                    <input type="date" id="insp-date">
                </div>
                <div class="modal-field">
                    <label>คนขับ</label>
                    <select id="insp-driver">
                        <option value="">— เลือก —</option>
                        <option>บังเดช</option><option>แชม</option><option>กอล์ฟ</option>
                        <option>หรั่ง</option><option>เก่ง</option><option>เอ</option>
                        <option>ยุทร</option><option>แฟรงค์</option><option>เอ้</option>
                    </select>
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>ทะเบียนรถ</label>
                    <input type="text" id="insp-plate" placeholder="เช่น กข-1234">
                </div>
                <div class="modal-field">
                    <label>เลขไมล์ (กม.)</label>
                    <input type="number" id="insp-mileage" placeholder="เช่น 123456">
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>สภาพโดยรวม</label>
                    <select id="insp-condition">
                        <option value="">— เลือก —</option>
                        <option value="good">✅ ปกติ / ดี</option>
                        <option value="warn">⚠️ มีปัญหาเล็กน้อย</option>
                        <option value="bad">❌ ต้องซ่อมแซม</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>ยางรถ</label>
                    <select id="insp-tire">
                        <option value="">— เลือก —</option>
                        <option value="good">✅ ปกติ</option>
                        <option value="warn">⚠️ ต้องตรวจ</option>
                        <option value="bad">❌ ต้องเปลี่ยน</option>
                    </select>
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>ไฟรถ</label>
                    <select id="insp-lights">
                        <option value="">— เลือก —</option>
                        <option value="good">✅ ปกติ</option>
                        <option value="bad">❌ มีปัญหา</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>เบรก</label>
                    <select id="insp-brake">
                        <option value="">— เลือก —</option>
                        <option value="good">✅ ปกติ</option>
                        <option value="warn">⚠️ ต้องตรวจ</option>
                        <option value="bad">❌ ต้องซ่อม</option>
                    </select>
                </div>
            </div>
            <div class="modal-field">
                <label>หมายเหตุ / รายละเอียดเพิ่มเติม</label>
                <input type="text" id="insp-note" placeholder="ระบุรายละเอียดปัญหาที่พบ (ถ้ามี)">
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-secondary" onclick="closeModal('inspect')">ยกเลิก</button>
            <button class="btn-primary" onclick="saveInspect()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                บันทึก
            </button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
/* ── CONSTANTS ────────────────────────────────── */
const STATUS = [
    { v:'',        l:'— ยังไม่ระบุ',               c:'st-empty'   },
    { v:'newbill', l:'สร้างบิลใหม่',               c:'st-newbill' },
    { v:'return',  l:'ลูกค้าเก็บบิล / ของกลับมา', c:'st-return'  },
    { v:'stock',   l:'เข้าสต็อก',                  c:'st-stock'   },
    { v:'wrong',   l:'จัดส่งผิด',                  c:'st-wrong'   },
];

const DUMMY = [
    { id:'RET-001', date:'30/03/2026', customer:'สมชาย มีสุข',     po:'CS6903-00051', job:'บริษัท เอบีซี จำกัด',             note:'ลูกค้าไม่อยู่บ้าน',                    delivery_status:'', new_bill:'', driver:'บังเดช' },
    { id:'RET-002', date:'31/03/2026', customer:'สุดา แก้วใส',     po:'46904-00363',  job:'ห้างหุ้นส่วน เดลต้า',             note:'ปิดร้าน ไม่มีคนรับของ',               delivery_status:'', new_bill:'', driver:'แชม'   },
    { id:'RET-003', date:'01/04/2026', customer:'วิชัย รักดี',     po:'CP6903-00051', job:'บริษัท สยามเทรด จำกัด',           note:'ติดต่อไม่ได้ โทรไม่รับ',              delivery_status:'', new_bill:'', driver:'กอล์ฟ' },
    { id:'RET-004', date:'30/03/2026', customer:'มาลี สวยงาม',     po:'CS6903-00052', job:'บริษัท ไทยซัพพลาย จำกัด',         note:'ที่อยู่ไม่ชัดเจน หาไม่เจอ',          delivery_status:'', new_bill:'', driver:'หรั่ง' },
    { id:'RET-005', date:'31/03/2026', customer:'ประสิทธิ์ ดีมาก', po:'CP6903-00053', job:'ร้าน โปรเกรส เทค',                note:'ลูกค้าขอเลื่อนรับของ',                delivery_status:'', new_bill:'', driver:'เก่ง'  },
    { id:'RET-006', date:'01/04/2026', customer:'อารีย์ ใจดี',     po:'46904-00364',  job:'บริษัท นิวเวฟ จำกัด',              note:'สินค้าเสียหายระหว่างขนส่ง',          delivery_status:'', new_bill:'', driver:'เอ'    },
    { id:'RET-007', date:'30/03/2026', customer:'ธนา รุ่งเรือง',   po:'CS6903-00054', job:'ร้าน เอสพี มาร์ท',                note:'เก็บเงินปลายทาง ลูกค้าไม่พร้อมจ่าย', delivery_status:'', new_bill:'', driver:'ยุทร'  },
    { id:'RET-008', date:'31/03/2026', customer:'จิรา ภักดี',      po:'CP6903-00055', job:'บริษัท โกลบอลเทค',                note:'เข้าพื้นที่ไม่ได้ ต้องใช้บัตรผ่าน',  delivery_status:'', new_bill:'', driver:'แฟรงค์'},
    { id:'RET-009', date:'01/04/2026', customer:'เอกชัย มั่นคง',   po:'46904-00365',  job:'ร้าน วีไอพี เทรดดิ้ง',            note:'ฝนตกหนัก ไม่สามารถจัดส่งได้',       delivery_status:'', new_bill:'', driver:'เอ้'   },
    { id:'RET-010', date:'30/03/2026', customer:'สุพัตรา งามดี',   po:'CS6903-00056', job:'บริษัท ซันไรส์ จำกัด',            note:'เวลาส่งไม่ตรงกับลูกค้านัด',          delivery_status:'', new_bill:'', driver:'บังเดช'},
];

/* ── STATE ────────────────────────────────────── */
let rows = [];

/* ── INIT ─────────────────────────────────────── */
(function init() {
    const now = new Date();
    document.getElementById('hdr-date').textContent =
        now.toLocaleDateString('th-TH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

    // set today's date as default
    const todayStr = now.toISOString().slice(0, 10);
    document.getElementById('f-date').value   = todayStr;
    document.getElementById('oil-date').value  = todayStr;
    document.getElementById('insp-date').value = todayStr;

    // load all data on start
    rows = DUMMY.map(r => ({ ...r }));
    render(rows);

    // enter key on filter fields
    document.getElementById('f-date').addEventListener('keydown', e => { if (e.key === 'Enter') load(); });
    document.getElementById('f-driver').addEventListener('keydown', e => { if (e.key === 'Enter') load(); });
})();

/* ── LOAD / FILTER ────────────────────────────── */
function load() {
    const dateInput = document.getElementById('f-date').value;
    const driver    = document.getElementById('f-driver').value;

    let formattedDate = '';
    if (dateInput) {
        const [y, m, d] = dateInput.split('-');
        formattedDate = `${d}/${m}/${y}`;
    }

    const result = DUMMY.filter(item => {
        const matchDate   = !formattedDate || item.date === formattedDate;
        const matchDriver = !driver        || item.driver === driver;
        return matchDate && matchDriver;
    });

    rows = result.map(r => ({ ...r }));
    render(rows);
}

function resetFilter() {
    const todayStr = new Date().toISOString().slice(0, 10);
    document.getElementById('f-date').value   = todayStr;
    document.getElementById('f-driver').value = '';
    rows = DUMMY.map(r => ({ ...r }));
    render(rows);
}

/* ── RENDER ───────────────────────────────────── */
function render(data) {
    document.getElementById('tbl-count').textContent = data.length + ' รายการ';
    const t = new Date();
    document.getElementById('ft-info').textContent = `แสดง ${data.length} รายการ`;
    document.getElementById('ft-time').textContent = `อัพเดต ${t.toLocaleTimeString('th-TH')}`;

    if (!data.length) {
        document.getElementById('tbody').innerHTML =
            `<tr><td colspan="7" class="empty-cell">
                <div class="empty-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
                <div class="empty-t">ไม่พบรายการ</div>
                <div class="empty-s">ไม่มีข้อมูลที่ตรงกับเงื่อนไขที่ระบุ</div>
            </td></tr>`;
        return;
    }

    document.getElementById('tbody').innerHTML = data.map((r, i) => {
        const sv   = r.delivery_status || '';
        const sc   = STATUS.find(s => s.v === sv)?.c || 'st-empty';
        const opts = STATUS.map(s =>
            `<option value="${s.v}"${sv === s.v ? ' selected' : ''}>${s.l}</option>`
        ).join('');
        const note  = r.note || null;
        const isNew = sv === 'newbill';
        const nbVal = r.new_bill || '';

        return `<tr>
            <td><span class="row-num">${i + 1}</span></td>
            <td><span class="cell-date">${r.date || '—'}</span></td>
            <td><span class="cell-name">${r.customer || '—'}</span></td>
            <td><span class="cell-bill">${r.po || r.id || '—'}</span></td>
            <td class="td-job"><span class="cell-job">${r.job || '—'}</span></td>
            <td class="td-note" title="${note || ''}">${note
                ? `<span class="cell-note">${note}</span>`
                : `<span class="cell-note empty">—</span>`}</td>
            <td>
                <div class="status-cell">
                    <select class="${sc} st-sel" data-id="${r.id}" onchange="changeStatus(this)">
                        ${opts}
                    </select>
                    <div class="newbill-wrap${isNew ? ' show' : ''}" id="nb-${r.id}">
                        <input class="newbill-input"
                               id="nb-input-${r.id}"
                               type="text"
                               placeholder="กรอกเลขบิลใหม่..."
                               value="${nbVal}"
                               onkeydown="if(event.key==='Enter') saveNewBill('${r.id}')">
                        <button class="newbill-save" onclick="saveNewBill('${r.id}')">บันทึก</button>
                    </div>
                </div>
            </td>
        </tr>`;
    }).join('');
}

/* ── STATUS CHANGE ────────────────────────────── */
function changeStatus(sel) {
    STATUS.forEach(s => sel.classList.remove(s.c));
    sel.classList.add(STATUS.find(s => s.v === sel.value)?.c || 'st-empty');

    const id     = sel.dataset.id;
    const isNew  = sel.value === 'newbill';
    const nbWrap = document.getElementById('nb-' + id);

    if (isNew) {
        nbWrap.classList.add('show');
        setTimeout(() => document.getElementById('nb-input-' + id)?.focus(), 50);
    } else {
        nbWrap.classList.remove('show');
        saveStatus(id, sel.value, null);
    }

    const r = rows.find(r => r.id === id);
    if (r) r.delivery_status = sel.value;
}

async function saveNewBill(id) {
    const input   = document.getElementById('nb-input-' + id);
    const newBill = input ? input.value.trim() : '';
    if (!newBill) { showToast('⚠ กรุณาระบุเลขบิลใหม่'); input?.focus(); return; }

    const r = rows.find(r => r.id === id);
    if (r) r.new_bill = newBill;

    await saveStatus(id, 'newbill', newBill);
}

async function saveStatus(id, status, newBill) {
    try {
        const body = { status: status || 'processing', updated_by: 'delivery' };
        if (newBill) body.new_bill = newBill;

        const res = await fetch('/return/' + encodeURIComponent(id) + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        showToast(newBill ? `✓ บันทึกบิลใหม่ ${newBill} เรียบร้อย` : '✓ บันทึกข้อมูลเรียบร้อยแล้ว');
    } catch(e) {
        // In demo mode, still show success
        showToast(newBill ? `✓ บันทึกบิลใหม่ ${newBill} เรียบร้อย` : '✓ บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}

/* ── SEARCH ───────────────────────────────────── */
function doSearch(q) {
    q = q.toLowerCase().trim();
    render(q ? rows.filter(r =>
        (r.po || r.id || '').toString().toLowerCase().includes(q) ||
        (r.customer || '').toLowerCase().includes(q) ||
        (r.job || '').toLowerCase().includes(q) ||
        (r.note || '').toLowerCase().includes(q)
    ) : rows);
}

/* ── MODAL ────────────────────────────────────── */
function openModal(type) {
    document.getElementById('modal-' + (type === 'oil' ? 'oil' : 'inspect')).classList.add('show');
}
function closeModal(type) {
    document.getElementById('modal-' + (type === 'oil' ? 'oil' : 'inspect')).classList.remove('show');
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('show');
        }
    });
});

function saveOil() {
    const driver  = document.getElementById('oil-driver').value;
    const plate   = document.getElementById('oil-plate').value.trim();
    const liters  = document.getElementById('oil-liters').value;
    const amount  = document.getElementById('oil-amount').value;

    if (!driver)  { showToast('⚠ กรุณาเลือกคนขับ'); return; }
    if (!plate)   { showToast('⚠ กรุณากรอกทะเบียนรถ'); return; }
    if (!liters)  { showToast('⚠ กรุณากรอกปริมาณน้ำมัน'); return; }
    if (!amount)  { showToast('⚠ กรุณากรอกยอดเงิน'); return; }

    // In production: send to API
    closeModal('oil');
    showToast(`✓ บันทึกการเติมน้ำมัน ${liters} ลิตร ฿${Number(amount).toLocaleString()} เรียบร้อย`);

    // Reset form
    ['oil-driver','oil-plate','oil-mileage','oil-liters','oil-amount','oil-note'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = id === 'oil-date' ? el.value : '';
    });
}

function saveInspect() {
    const driver    = document.getElementById('insp-driver').value;
    const plate     = document.getElementById('insp-plate').value.trim();
    const condition = document.getElementById('insp-condition').value;

    if (!driver)    { showToast('⚠ กรุณาเลือกคนขับ'); return; }
    if (!plate)     { showToast('⚠ กรุณากรอกทะเบียนรถ'); return; }
    if (!condition) { showToast('⚠ กรุณาเลือกสภาพโดยรวม'); return; }

    // In production: send to API
    closeModal('inspect');
    const condLabel = condition === 'good' ? 'ปกติ / ดี' : condition === 'warn' ? 'มีปัญหาเล็กน้อย' : 'ต้องซ่อมแซม';
    showToast(`✓ บันทึกตรวจสภาพรถ ${plate} — ${condLabel}`);

    // Reset form
    ['insp-driver','insp-plate','insp-mileage','insp-condition','insp-tire','insp-lights','insp-brake','insp-note'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

/* ── TOAST ────────────────────────────────────── */
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
</body>
</html>